<?php

namespace App\Http\Controllers;

use App\Models\HomePageSection;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    private function scoped(string $table): Builder
    {
        abort_unless(Schema::hasTable($table), 503, "Required shared table [{$table}] is unavailable.");
        $query = DB::table($table);
        abort_unless(Schema::hasColumn($table, 'site_key'), 503, 'Shared database site isolation is not installed. Apply the merged multi-site migrations before enabling this frontend.');
        $query->where($table.'.site_key', config('gali.site_key'));
        if (Schema::hasColumn($table, 'deleted_at')) {
            $query->whereNull($table.'.deleted_at');
        }
        return $query;
    }

    private function shareNavigation(): array
    {
        $pages = Schema::hasTable('pages')
            ? $this->scoped('pages')->where('status', 'active')->where('menu_visible', true)
                ->orderBy('menu_order')->get(['name', 'slug'])
            : collect();
        $settings = Schema::hasTable('settings') ? $this->scoped('settings')->pluck('value', 'option')->all() : [];
        view()->share('navPages', $pages);
        $siteName = filled($settings['title'] ?? null) ? $settings['title'] : config('gali.site_name');
        $settings['title'] = $siteName;
        $settings['site_tagline'] = $settings['site_tagline'] ?? '';
        $settings['site_logo_url'] = $this->logoUrl($settings['site_logo'] ?? null);
        $settings['site_favicon_url'] = $this->logoUrl($settings['site_favicon'] ?? null);
        $settings['og_image_url'] = $this->logoUrl($settings['og_image'] ?? null);
        view()->share('siteSettings', $settings);
        return $settings;
    }

    private function logoUrl(?string $filename): ?string
    {
        if (!$filename) {
            return null;
        }

        $safeName = basename($filename);
        return $safeName !== '' && is_file(public_path('logos/'.$safeName))
            ? asset('logos/'.$safeName)
            : null;
    }

    private function safeFrontendUrl(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (str_starts_with($value, '/') || str_starts_with($value, '#')) {
            return $value;
        }
        return filter_var($value, FILTER_VALIDATE_URL)
            && in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true)
                ? $value
                : null;
    }

    private function gameBusinessDate(object $game, Carbon $now): string
    {
        $slug = Str::slug((string) ($game->slug ?? $game->name ?? ''));
        if (!in_array($slug, ['disawar', 'desawar'], true) || empty($game->result_time)) {
            return $now->toDateString();
        }
        try {
            $boundary = Carbon::parse($game->result_time, config('gali.timezone'));
            $todayBoundary = $now->copy()->setTime($boundary->hour, $boundary->minute, $boundary->second);
            return $now->lt($todayBoundary) ? $now->copy()->subDay()->toDateString() : $now->toDateString();
        } catch (\Throwable $exception) {
            return $now->toDateString();
        }
    }

    public function home()
    {
        $siteSettings = $this->shareNavigation();
        $now = now(config('gali.timezone'));
        $games = $this->scoped('games')->where('status', 'active')
            ->orderBy('serial')->orderBy('id')
            ->get(['id', 'name', 'slug', 'serial', 'result_time', 'last_result']);

        $businessDates = [];
        foreach ($games as $game) {
            $businessDates[$game->id] = $this->gameBusinessDate($game, $now);
        }
        $dateSet = collect($businessDates)->flatMap(fn ($date) => [$date, Carbon::parse($date, config('gali.timezone'))->subDay()->toDateString()])->unique()->values();
        $gameIds = $games->pluck('id')->all();
        $results = collect();
        $latestRows = collect();
        if ($gameIds) {
            $results = $this->scoped('results')->whereIn('game_id', $gameIds)->where('type', 'jodi')
                ->whereIn('game_date', $dateSet)->get(['game_id', 'game_date', 'number'])->groupBy('game_id');
            $latestRows = $this->scoped('results')->whereIn('game_id', $gameIds)->where('type', 'jodi')
                ->whereNotNull('number')->where('number', '<>', '')
                ->whereDate('game_date', '<=', $now->toDateString())
                ->orderByDesc('game_date')->orderByDesc('updated_at')->orderByDesc('created_at')
                ->get(['game_id', 'game_date', 'number', 'updated_at', 'created_at'])->groupBy('game_id');
        }

        $frontendGames = $games->map(function ($game) use ($results, $businessDates, $latestRows) {
            $rows = $results->get($game->id, collect());
            $todayDate = $businessDates[$game->id];
            $yesterdayDate = Carbon::parse($todayDate, config('gali.timezone'))->subDay()->toDateString();
            $todayResult = optional($rows->firstWhere('game_date', $todayDate))->number;
            $yesterdayResult = optional($rows->firstWhere('game_date', $yesterdayDate))->number;
            $latestRow = $latestRows->get($game->id, collect())->first();
            return [
                'id' => $game->id, 'name' => $game->name, 'slug' => $game->slug,
                'serial' => (int) $game->serial,
                'latest_at' => $latestRow ? (string) $latestRow->game_date : null,
                'latest_result' => $latestRow && filled($latestRow->number) ? (string) $latestRow->number : '--',
                'latest_date' => $latestRow ? Carbon::parse($latestRow->game_date)->format('d M Y') : 'No published result',
                'time' => $game->result_time ? Carbon::parse($game->result_time)->format('h:i A') : '—',
                'today' => filled($todayResult) ? (string) $todayResult : '--',
                'yesterday' => filled($yesterdayResult) ? (string) $yesterdayResult : '--',
                'last_result' => filled($game->last_result) ? (string) $game->last_result : '--',
            ];
        })->values();

        $featuredGame = $frontendGames->sort(function ($a, $b) {
            $latest = strcmp((string) ($b['latest_at'] ?? ''), (string) ($a['latest_at'] ?? ''));
            return $latest !== 0 ? $latest : ($a['serial'] <=> $b['serial']);
        })->first();
        $sections = collect();
        if (Schema::hasTable('home_pages')) {
            $this->scoped('home_pages'); // Require site ownership before using the Eloquent section model.
            $sectionQuery = HomePageSection::query()->where('status', 'active');
            if (Schema::hasColumn('home_pages', 'display_order')) {
                $sectionQuery->orderBy('display_order');
            }
            $sections = $sectionQuery->orderBy('id')->get();
            $sections->each(function ($section): void {
                $section->image_url = $section->image
                    ? Storage::disk('public')->url($section->image)
                    : null;
                $section->safe_button_url = $this->safeFrontendUrl($section->button_url);
            });
        }
        $faqs = Schema::hasTable('faqs') ? $this->scoped('faqs')->latest('id')->get() : collect();

        $year = (int) $now->format('Y');
        $month = (int) $now->format('n');
        $daysInMonth = $now->daysInMonth;
        $chartResults = collect();
        if ($gameIds) {
            $chartResults = $this->scoped('results')->whereIn('game_id', $gameIds)->where('type', 'jodi')
                ->whereBetween('game_date', [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()])
                ->whereNotNull('number')->where('number', '<>', '')
                ->get(['game_id', 'game_date', 'number']);
        }
        $resultMap = [];
        foreach ($chartResults as $result) {
            $resultMap[(int) $result->game_id][(int) Carbon::parse($result->game_date)->day] = (string) $result->number;
        }
        $archiveYears = collect();
        if ($gameIds) {
            $archiveYears = $this->scoped('results')->whereIn('game_id', $gameIds)->where('type', 'jodi')
                ->whereNotNull('number')->where('number', '<>', '')
                ->distinct()->orderBy('game_date')->pluck('game_date')
                ->map(fn ($date) => Carbon::parse($date)->year)->unique()->sortDesc()->values();
        }

        $chunkSize = config('gali.chart_chunk_size');
        $tableCount = $games->isEmpty() ? 0 : (int) ceil($games->count() / $chunkSize);
        $chartTables = [];
        if ($tableCount) {
            $base = intdiv($games->count(), $tableCount);
            $extra = $games->count() % $tableCount;
            $offset = 0;
            for ($i = 0; $i < $tableCount; $i++) {
                $size = $base + ($i < $extra ? 1 : 0);
                $chartTables[] = ['games' => $games->slice($offset, $size), 'results' => $resultMap];
                $offset += $size;
            }
        }

        $siteName = $siteSettings['title'] ?? config('gali.site_name');
        $canonicalBase = filled($siteSettings['canonical_url'] ?? null) ? $siteSettings['canonical_url'] : config('gali.canonical_url');
        $canonical = rtrim((string) $canonicalBase, '/') . '/';
        return view('frontend.home', compact('frontendGames', 'featuredGame', 'sections', 'faqs', 'chartTables', 'year', 'month', 'daysInMonth', 'archiveYears', 'siteName', 'canonical'));
    }

    public function market(Request $request, string $slug)
    {
        $siteSettings = $this->shareNavigation();
        $game = $this->scoped('games')->where('status', 'active')->where('slug', $slug)->firstOrFail();
        $now = now(config('gali.timezone'));
        $year = (int) $request->query('year', $now->year);
        $month = (int) $request->query('month', $now->month);
        abort_if($year < 2000 || $year > $now->year || $month < 1 || $month > 12 || ($year === $now->year && $month > $now->month), 404);
        $start = Carbon::create($year, $month, 1, 0, 0, 0, config('gali.timezone'))->startOfMonth();
        $days = $start->daysInMonth;
        $records = $this->scoped('results')->where('game_id', $game->id)->where('type', 'jodi')
            ->whereBetween('game_date', [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()])
            ->get(['game_date', 'number'])->keyBy(fn ($row) => Carbon::parse($row->game_date)->day);
        $rows = [];
        for ($day = 1; $day <= $days; $day++) {
            $date = $start->copy()->day($day);
            $record = $records->get($day);
            $rows[] = ['date' => $date, 'number' => $date->lte($now->copy()->startOfDay()) && $record && filled($record->number) ? (string) $record->number : '--'];
        }
        $siteName = $siteSettings['title'] ?? config('gali.site_name');
        $canonicalBase = filled($siteSettings['canonical_url'] ?? null) ? $siteSettings['canonical_url'] : config('gali.canonical_url');
        $canonical = rtrim((string) $canonicalBase, '/') . '/result/' . $game->slug;
        return view('frontend.market', compact('game', 'rows', 'year', 'month', 'siteName', 'canonical'));
    }

    public function chartsIndex()
    {
        $siteSettings = $this->shareNavigation();
        $games = $this->scoped('games')->where('status', 'active')->orderBy('serial')->orderBy('id')
            ->get(['id', 'name', 'slug', 'result_time']);
        $gameIds = $games->pluck('id')->all();
        $archiveByGame = collect();
        if ($gameIds) {
            $dates = $this->scoped('results')->whereIn('game_id', $gameIds)->where('type', 'jodi')
                ->whereNotNull('number')->where('number', '<>', '')
                ->orderBy('game_date')->get(['game_id', 'game_date']);
            $archiveByGame = $dates->groupBy('game_id')->map(function ($rows) {
                return $rows->map(fn ($row) => Carbon::parse($row->game_date))
                    ->groupBy(fn ($date) => $date->year)
                    ->map(fn ($yearRows) => $yearRows->map(fn ($date) => $date->month)->unique()->sort()->values());
            });
        }
        $siteName = $siteSettings['title'] ?? config('gali.site_name');
        return view('frontend.charts', compact('games', 'archiveByGame', 'siteName'));
    }

    public function page(string $slug)
    {
        $siteSettings = $this->shareNavigation();
        abort_unless(Schema::hasTable('pages'), 404);
        $page = $this->scoped('pages')->where('slug', $slug)->where('status', 'active')->firstOrFail();
        $title = $page->meta_title ?: $page->name;
        $description = $page->meta_description ?? '';
        $canonicalBase = filled($siteSettings['canonical_url'] ?? null) ? $siteSettings['canonical_url'] : config('gali.canonical_url');
        $canonical = rtrim((string) $canonicalBase, '/') . '/' . $page->slug;
        return response()
            ->view('frontend.page', compact('page', 'title', 'description', 'canonical'))
            ->header('X-Robots-Tag', !empty($page->noindex) ? 'noindex, follow' : 'index, follow');
    }

    public function guessingIndex()
    {
        $siteSettings = $this->shareNavigation();
        $games = $this->scoped('games')->where('status', 'active')->orderBy('serial')->get(['name', 'slug']);
        $siteName = $siteSettings['title'] ?? config('gali.site_name');
        return view('frontend.guessing', compact('games', 'siteName'));
    }

    public function chart(Request $request)
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'regex:/^[A-Za-z0-9-]+$/'],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
        ]);
        $game = $this->scoped('games')->where('status', 'active')->where('slug', $data['slug'])->firstOrFail();
        $start = Carbon::create((int) $data['year'], (int) $data['month'], 1, 0, 0, 0, config('gali.timezone'))->startOfMonth();
        abort_if($start->gt(now(config('gali.timezone'))->copy()->startOfMonth()), 404);
        $records = $this->scoped('results')->where('game_id', $game->id)->where('type', 'jodi')
            ->whereBetween('game_date', [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()])
            ->get(['game_date', 'number'])->keyBy(fn ($row) => Carbon::parse($row->game_date)->day);
        $days = [];
        for ($day = 1; $day <= $start->daysInMonth; $day++) {
            $record = $records->get($day);
            $days[] = ['day' => $day, 'result' => $record && filled($record->number) ? (string) $record->number : null];
        }
        return response()->json(['game' => ['name' => $game->name, 'slug' => $game->slug], 'year' => $start->year, 'month' => $start->month, 'days' => $days])->header('Cache-Control', 'no-store');
    }
}
