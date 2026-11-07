<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class GaliDisawarDemoSeeder extends Seeder
{
    public function run(): void
    {
        $siteKey = (string) config('gali.site_key', 'galidisawar');
        foreach (['games', 'results', 'home_pages', 'faqs', 'pages', 'settings'] as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'site_key')) {
                throw new RuntimeException("Shared table [{$table}] or its site_key column is missing. Apply the shared multi-site migrations first.");
            }
        }

        foreach (['image', 'image_alt', 'button_label', 'button_url', 'button_style', 'layout', 'display_order'] as $column) {
            if (!Schema::hasColumn('home_pages', $column)) {
                throw new RuntimeException("home_pages.{$column} is missing. Apply the Gali theme presentation migration before demo seeding.");
            }
        }

        DB::transaction(function () use ($siteKey): void {
            $this->seedSettings($siteKey);
            $games = $this->seedGames($siteKey);
            $this->seedResults($siteKey, $games);
            $this->seedSections($siteKey);
            $this->seedFaqs($siteKey);
            $this->seedPages($siteKey);
        });

        $this->command?->info("Demo content added for site_key={$siteKey}. Existing records and non-demo rows were not updated.");
    }

    private function seedSettings(string $siteKey): void
    {
        $settings = [
            'title' => 'Gali Disawar',
            'site_tagline' => 'LIVE RESULT RECORDS • MARKET DIRECTORY • MONTHLY CHARTS',
            'site_description' => 'Browse published Gali Disawar market records, daily result summaries and historical monthly charts.',
            'meta_title' => 'Gali Disawar | Live Result Records & Monthly Charts',
            'meta_description' => 'Browse Gali Disawar market result records, today and yesterday summaries, and monthly historical charts.',
            'meta_keywords' => 'Gali Disawar, Gali Result, Disawar Result, Monthly Chart, Historical Records',
            'canonical_url' => 'https://galidisawar.com',
            'site_logo' => 'gali-demo-logo.svg',
            'site_favicon' => 'gali-demo-favicon.svg',
            'homepage_heading_line1' => 'Every market.',
            'homepage_heading_line2' => 'Every record.',
            'homepage_heading_line3' => 'One clear board.',
            'homepage_intro' => 'Browse published market results, compare recent records, and open date-wise monthly charts.',
            'homepage_note_text' => 'DEMO PREVIEW — Sample records are shown for layout testing only.',
            'announcement_text' => 'Demo preview is active. Sample values are not live results.',
            'web_marquee' => 'Gali Disawar demo preview • Sample market records • Monthly charts',
            'header_cta_label' => 'Open Chart',
            'header_cta_url' => '/chart',
            'homepage_primary_cta_label' => 'Browse Market Charts',
            'homepage_primary_cta_url' => '/chart',
            'homepage_secondary_cta_label' => 'View Live Results',
            'homepage_secondary_cta_url' => '/#markets',
            'homepage_guide_heading' => 'How to Read the Market Result Chart',
            'homepage_guide_content' => '<p>Select a market, then choose an available year and month. A dash means that no published value is currently stored for that date. This preview uses sample data only.</p>',
            'homepage_seo_heading' => 'About Gali Disawar Result Records',
            'homepage_seo_content' => '<p>This demo homepage demonstrates the full theme layout, market directory, monthly result tables, historical archive, managed content sections and FAQ blocks. Replace sample content with verified site information before publishing.</p>',
            'footer_description' => 'Gali Disawar organizes published market records and monthly charts for informational reference.',
            'disclaimer_content' => 'Demo content is for layout testing only. Sample values are not live or verified results. Historical records are not predictions or guarantees. Follow applicable local laws.',
            'copyright_text' => '© '.date('Y').' Gali Disawar. Demo preview.',
            'sticky_social_enabled' => '0',
            'robots_default' => 'noindex,follow',
        ];

        foreach ($settings as $option => $value) {
            $existing = DB::table('settings')->where('site_key', $siteKey)->where('option', $option)->first();
            if (!$existing) {
                DB::table('settings')->insert([
                    'site_key' => $siteKey, 'option' => $option, 'value' => $value,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            } elseif (blank($existing->value) && in_array($option, ['site_logo', 'site_favicon', 'homepage_guide_content', 'homepage_seo_content', 'header_cta_label', 'header_cta_url', 'homepage_primary_cta_label', 'homepage_primary_cta_url', 'homepage_secondary_cta_label', 'homepage_secondary_cta_url'], true)) {
                DB::table('settings')->where('id', $existing->id)->update(['value' => $value, 'updated_at' => now()]);
            }
        }
    }

    private function seedGames(string $siteKey): array
    {
        $definitions = [
            ['name' => 'Gali Demo', 'slug' => 'demo-gali', 'result_time' => '23:00:00', 'play_start' => '10:00:00', 'play_end' => '22:55:00', 'serial' => 901],
            ['name' => 'Disawar Demo', 'slug' => 'demo-disawar', 'result_time' => '05:00:00', 'play_start' => '00:00:00', 'play_end' => '04:55:00', 'serial' => 902],
            ['name' => 'Faridabad Demo', 'slug' => 'demo-faridabad', 'result_time' => '18:00:00', 'play_start' => '10:00:00', 'play_end' => '17:55:00', 'serial' => 903],
            ['name' => 'Ghaziabad Demo', 'slug' => 'demo-ghaziabad', 'result_time' => '21:30:00', 'play_start' => '10:00:00', 'play_end' => '21:25:00', 'serial' => 904],
            ['name' => 'Delhi Bazar Demo', 'slug' => 'demo-delhi-bazar', 'result_time' => '15:00:00', 'play_start' => '10:00:00', 'play_end' => '14:55:00', 'serial' => 905],
            ['name' => 'Shri Ganesh Demo', 'slug' => 'demo-shri-ganesh', 'result_time' => '16:30:00', 'play_start' => '10:00:00', 'play_end' => '16:25:00', 'serial' => 906],
        ];

        $ids = [];
        foreach ($definitions as $game) {
            $existing = DB::table('games')->where('site_key', $siteKey)->where('slug', $game['slug'])->first();
            if ($existing) {
                $ids[] = ['id' => (int) $existing->id, 'serial' => $game['serial']];
                continue;
            }
            $id = DB::table('games')->insertGetId([
                'site_key' => $siteKey, 'name' => $game['name'], 'slug' => $game['slug'],
                'result_time' => $game['result_time'], 'play_start' => $game['play_start'],
                'play_end' => $game['play_end'], 'last_result' => null, 'status' => 'active',
                'serial' => $game['serial'], 'reward' => 1, 'created_at' => now(),
                'updated_at' => now(), 'deleted_at' => null,
            ]);
            $ids[] = ['id' => $id, 'serial' => $game['serial']];
        }
        return $ids;
    }

    private function seedResults(string $siteKey, array $games): void
    {
        $today = Carbon::now(config('gali.timezone'));
        $days = min(7, $today->day);
        foreach ($games as $index => $game) {
            for ($offset = 0; $offset < $days; $offset++) {
                $date = $today->copy()->subDays($offset)->toDateString();
                $number = str_pad((string) (($game['serial'] * 7 + $offset * 13) % 100), 2, '0', STR_PAD_LEFT);
                DB::table('results')->insertOrIgnore([
                    'site_key' => $siteKey, 'game_id' => $game['id'], 'game_date' => $date,
                    'type' => 'jodi', 'number' => $number, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedSections(string $siteKey): void
    {
        $sections = [
            ['title' => '[DEMO] Featured information banner', 'location' => 'first_place', 'layout' => 'banner', 'image' => 'https://placehold.co/1200x420/111827/ffffff?text=Gali+Disawar+Theme+Preview', 'button_label' => 'Browse Charts', 'button_url' => '/chart', 'content' => '<p>Sample banner section with a dynamic image and call-to-action button. Replace it from Admin → Home Page Sections.</p>'],
            ['title' => '[DEMO] Market timing guide', 'location' => 'second_place', 'layout' => 'image-left', 'image' => 'https://placehold.co/640x420/7c3aed/ffffff?text=Market+Guide', 'button_label' => 'See Markets', 'button_url' => '/#markets', 'content' => '<p>Use this section to explain market schedules and how the result archive is organized.</p>'],
            ['title' => '[DEMO] Yesterday and today records', 'location' => 'second_place_another', 'layout' => 'image-right', 'image' => 'https://placehold.co/640x420/0f766e/ffffff?text=Daily+Records', 'button_label' => 'Open Daily Board', 'button_url' => '/#markets-quick-record', 'content' => '<p>This content block demonstrates an image on the right with a separate button.</p>'],
            ['title' => '[DEMO] Historical result archive', 'location' => 'third_place', 'layout' => 'image-top', 'image' => 'https://placehold.co/1200x360/1e293b/ffffff?text=Historical+Archive', 'button_label' => 'View Old Records', 'button_url' => '/#records', 'content' => '<p>Promote the historical archive and explain how visitors can choose a year and month.</p>'],
            ['title' => '[DEMO] Full width announcement', 'location' => 'third_place_another', 'layout' => 'standard', 'image' => null, 'button_label' => 'Open Chart Directory', 'button_url' => '/chart', 'content' => '<p>A second announcement position with its own title, copy and CTA.</p>'],
            ['title' => '[DEMO] Before footer contact block', 'location' => 'before_footer', 'layout' => 'standard', 'image' => null, 'button_label' => 'Contact via Admin Settings', 'button_url' => '/#khaiwal', 'content' => '<p>Configure the real phone, WhatsApp and Telegram details in the site settings before publishing.</p>'],
            ['title' => '[DEMO] After footer information block', 'location' => 'after_footer', 'layout' => 'standard', 'image' => null, 'button_label' => 'Back to Top', 'button_url' => '/#freshness', 'content' => '<p>This final managed section verifies the last homepage section position.</p>'],
        ];

        foreach ($sections as $order => $section) {
            $exists = DB::table('home_pages')->where('site_key', $siteKey)->where('title', $section['title'])->exists();
            if ($exists) continue;
            DB::table('home_pages')->insert([
                'site_key' => $siteKey, 'title' => $section['title'], 'content' => $section['content'],
                'status' => 'active', 'location' => $section['location'], 'phone' => null,
                'whatsapp' => null, 'telegram' => null, 'background' => '#ffffff',
                'image' => $section['image'], 'image_alt' => $section['title'],
                'button_label' => $section['button_label'], 'button_url' => $section['button_url'],
                'button_style' => 'primary', 'layout' => $section['layout'], 'display_order' => $order + 1,
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
            ]);
        }
    }

    private function seedFaqs(string $siteKey): void
    {
        $faqs = [
            ['[DEMO] How do I view a monthly chart?', 'Choose a market, then select an available year and month to view date-wise saved records.'],
            ['[DEMO] What does a dash mean?', 'A dash means no published value is currently stored for that date.'],
            ['[DEMO] Are historical records predictions?', 'No. Historical records are for reference only and do not predict or guarantee future outcomes.'],
            ['[DEMO] Can I browse old market records?', 'Yes. Open the Chart Archive and select a market, year and month.'],
        ];
        foreach ($faqs as [$question, $answer]) {
            $exists = DB::table('faqs')->where('site_key', $siteKey)->where('question', $question)->exists();
            if (!$exists) {
                $row = ['site_key' => $siteKey, 'question' => $question, 'answer' => $answer, 'created_at' => now(), 'updated_at' => now()];
                if (Schema::hasColumn('faqs', 'platform')) $row['platform'] = 'web';
                DB::table('faqs')->insert($row);
            }
        }
    }

    private function seedPages(string $siteKey): void
    {
        $pages = [
            ['name' => 'Disclaimer', 'slug' => 'disclaimer', 'content' => '<h2>Disclaimer</h2><p>This website provides informational result records and historical charts only. Records are not predictions or guarantees. Please follow applicable local laws.</p>'],
            ['name' => 'Privacy Policy', 'slug' => 'privacy-policy', 'content' => '<h2>Privacy Policy</h2><p>Review the site privacy policy and contact the site administrator for privacy questions. Replace this demo copy with your approved policy before launch.</p>'],
            ['name' => 'Terms & Conditions', 'slug' => 'terms-and-conditions', 'content' => '<h2>Terms & Conditions</h2><p>Use the site responsibly and follow applicable laws. Historical records are informational and do not guarantee future outcomes.</p>'],
        ];
        foreach ($pages as $index => $page) {
            $exists = DB::table('pages')->where('site_key', $siteKey)->where('slug', $page['slug'])->exists();
            if ($exists) continue;
            $row = [
                'site_key' => $siteKey, 'name' => $page['name'], 'slug' => $page['slug'],
                'content' => $page['content'], 'status' => 'active', 'is_editable' => 'yes',
                'menu_visible' => true, 'menu_order' => 90 + $index,
                'meta_title' => $page['name'].' | Gali Disawar',
                'meta_description' => 'Demo '.$page['name'].' page for the Gali Disawar theme preview.',
                'meta_keywords' => 'Gali Disawar, '.$page['name'], 'noindex' => true,
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
            ];
            DB::table('pages')->insert($row);
        }
    }
}
