@extends('frontend.layout')
@section('title', $siteSettings['meta_title'] ?? ($siteSettings['title'].' | Today Live Results & Record Charts'))
@section('description', $siteSettings['meta_description'] ?? $siteSettings['site_description'] ?? '')
@section('canonical', $canonical)
@section('content')
@if(filled($siteSettings['site_counter_text'] ?? null))<div class="site-counter"><span id="sconlineimg" title="{{ $siteSettings['site_counter_text'] }}">{{ $siteSettings['site_counter_text'] }}</span></div>@endif
<section id="freshness" class="freshness-wrap">
    <div class="freshness-headline">{{ $siteSettings['homepage_note_text'] ?? ($siteSettings['title'].' — '.now(config('gali.timezone'))->format('d F Y').' market result records and historical charts.') }}</div>
    <div class="freshness-disclaimer">{{ $siteSettings['disclaimer_content'] ?? 'This website is an independent informational record archive. Displayed records are not predictions or guarantees. Please follow applicable local laws.' }} <a class="freshness-more" href="{{ route('page', 'disclaimer') }}">Read more…</a></div>
    <div class="freshness-updated">Updated: {{ now(config('gali.timezone'))->format('d M Y, h:i:s A') }} IST</div>
</section>

<section class="boxc3 homepage-intro">
    <div class="result-heading">
        <span class="eyebrow">{{ $siteSettings['site_tagline'] ?? 'LIVE RESULTS • RECORD CHARTS • MARKET HISTORY' }}</span>
        <h1>{{ $siteSettings['homepage_heading_line1'] ?? 'Every market.' }} <span>{{ $siteSettings['homepage_heading_line2'] ?? 'Every record.' }}</span> {{ $siteSettings['homepage_heading_line3'] ?? 'One clear board.' }}</h1>
        <p>{{ $siteSettings['homepage_intro'] ?? 'Browse published market results, compare recent records, and open date-wise monthly charts.' }}</p>
    </div>
    <div class="homepage-main-actions">
        @php($primaryUrl = $siteSettings['homepage_primary_cta_url_safe'] ?? '/chart')
        @php($secondaryUrl = $siteSettings['homepage_secondary_cta_url_safe'] ?? '#markets')
        @if(filled($siteSettings['homepage_primary_cta_label'] ?? null) || filled($siteSettings['app_download_url_safe'] ?? null))
            <a class="buttonb bggreen" href="{{ filled($siteSettings['homepage_primary_cta_url'] ?? null) ? $primaryUrl : $siteSettings['app_download_url_safe'] }}" @if(str_starts_with($primaryUrl, 'http')) target="_blank" rel="noopener noreferrer" @endif>{{ $siteSettings['homepage_primary_cta_label'] ?? 'Download App' }} ↗</a>
        @endif
        <a class="buttonb bgblue" href="{{ $secondaryUrl }}">{{ $siteSettings['homepage_secondary_cta_label'] ?? 'Explore Result Charts' }} ↗</a>
        <a class="buttonb" href="{{ route('charts.index') }}">All Markets ↗</a>
    </div>
</section>

@foreach($sections->where('location', 'first_place') as $section) @include('frontend.partials.home-section', ['section' => $section]) @endforeach

@if($featuredGame)
<section class="resultmain" id="results">
    <div class="resultmaintime">LATEST PUBLISHED RESULT · {{ $featuredGame['latest_date'] }}</div>
    <div class="livegame">{{ $featuredGame['name'] }}</div>
    <div class="liveresult">{{ $featuredGame['latest_result'] }}</div>
    <div class="resultmaintoday">Market time: {{ $featuredGame['time'] }}</div>
    <a class="market-arrow" href="{{ route('market', $featuredGame['slug']) }}">View {{ $featuredGame['name'] }} record chart →</a>
</section>
<div class="result-bottom"><a title="Guessing Forum" href="{{ route('guessing') }}">COMMUNITY & GUESSING FORUM ↗</a></div>
@endif

<section class="boxc3" id="markets-quick-record">
    <div class="result-heading"><h2>Today Live Results</h2><p>Market time · Yesterday / Today · Full record chart</p></div>
    <div class="quick-record-box"><div class="table-scroll"><table class="quick-record-table">
        <thead><tr><th>Market</th><th>Time</th><th>Record Chart</th><th>Yesterday</th><th>Today</th></tr></thead>
        <tbody>
        @forelse($frontendGames as $game)
            <tr><td class="qr-market"><a href="{{ route('market', $game['slug']) }}">{{ $game['name'] }}</a></td><td class="qr-time">{{ $game['time'] }}</td><td class="qr-link"><a href="{{ route('market', $game['slug']) }}">Record Chart</a></td><td>{{ $game['yesterday'] }}</td><td class="qr-val">{{ $game['today'] }}</td></tr>
        @empty<tr><td colspan="5">No active markets are configured yet.</td></tr>@endforelse
        </tbody>
    </table></div></div>
</section>

<div class="seo-jump">
    <a href="#results">Latest Result</a><a href="#markets">Market Board</a><a href="#charts">Monthly Chart</a><a href="#records">Old Records</a><a href="#faq">FAQ</a>
</div>

@foreach(['second_place','second_place_another'] as $position) @foreach($sections->where('location', $position) as $section) @include('frontend.partials.home-section', ['section' => $section]) @endforeach @endforeach

<section class="market-list" id="markets">
    <div class="result-heading"><h2>Market Record Charts</h2><p>Search a market and open its live result board or monthly history.</p></div>
    <div class="market-tools"><label class="market-search"><span aria-hidden="true">⌕</span><input id="market-search" type="search" placeholder="Search market name…" aria-label="Search markets"><kbd>/</kbd></label><span class="market-count">{{ $frontendGames->count() }} active markets</span></div>
    <div class="market-list-grid" id="market-grid">
        @forelse($frontendGames as $game)
            <article class="market-card {{ $featuredGame && $featuredGame['id'] === $game['id'] ? 'market-card--full' : '' }}" data-market-name="{{ strtolower($game['name']) }}">
                <a class="market-card-head" href="{{ route('market', $game['slug']) }}"><span class="market-card-name">{{ $game['name'] }}</span><span class="market-card-time">{{ $game['time'] }}</span></a>
                <div class="market-card-body"><div class="market-card-res"><span>Yesterday <b>{{ $game['yesterday'] }}</b></span><span>Today <b>{{ $game['today'] }}</b></span></div><a class="market-arrow" href="{{ route('market', $game['slug']) }}">View monthly chart →</a></div>
            </article>
        @empty<p>No active markets are available. Add markets in the admin panel.</p>@endforelse
    </div>
    <p class="empty-state" id="no-markets" hidden>No market matches that search. Try another name.</p>
</section>

@foreach(['third_place','third_place_another'] as $position) @foreach($sections->where('location', $position) as $section) @include('frontend.partials.home-section', ['section' => $section]) @endforeach @endforeach

<section class="boxc3" id="khaiwal">
    <div class="result-heading"><h2>{{ $siteSettings['app_section_heading'] ?? 'Stay Updated' }}</h2><p>{{ $siteSettings['app_section_description'] ?? 'Open the result charts or follow the official channels configured by the site administrator.' }}</p></div>
    <div class="btn-wrap">
        @if(filled($siteSettings['app_download_url'] ?? null))<a class="btn whatsapp" href="{{ $siteSettings['app_download_url'] }}" target="_blank" rel="noopener noreferrer">{{ $siteSettings['app_download_label'] ?? 'Download App Now' }}</a>@endif
        @if(filled($siteSettings['social_telegram'] ?? null))<a class="btn whatsapp" href="{{ $siteSettings['social_telegram_safe'] }}" target="_blank" rel="noopener noreferrer">Join Telegram</a>@endif
        @if(filled($siteSettings['social_whatsapp'] ?? null))<a class="btn whatsapp" href="{{ $siteSettings['social_whatsapp_safe'] }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>@endif
        @if(filled($siteSettings['contact_phone'] ?? null))<a class="btn call" href="tel:{{ preg_replace('/[^0-9+]/','',$siteSettings['contact_phone']) }}">Call Now</a>@endif
    </div>
    <div class="table-scroll homepage-market-schedule"><table class="khaiwal-table"><thead><tr><th>Market</th><th>Published Result Time</th><th>Record Chart</th></tr></thead><tbody>
        @foreach($frontendGames as $game)<tr><td>{{ $game['name'] }}</td><td>{{ $game['time'] }}</td><td><a href="{{ route('market',$game['slug']) }}">View Chart</a></td></tr>@endforeach
    </tbody></table></div>
</section>

<section class="seo-box quick-record-box" id="charts">
    <div class="result-heading"><h2>{{ now(config('gali.timezone'))->format('F Y') }} Monthly Market Chart</h2><p>Daily published results for all active markets. Older months are available in the archive.</p></div>
    @forelse($chartTables as $tableIndex => $table)
        <div class="table-scroll"><table class="khaiwal-table"><thead><tr><th>Date</th>@foreach($table['games'] as $game)<th><a href="{{ route('market', $game->slug) }}">{{ $game->name }}</a></th>@endforeach</tr></thead><tbody>
        @for($day=1;$day<=$daysInMonth;$day++)
            <tr><th>{{ sprintf('%02d',$day) }}</th>@foreach($table['games'] as $game)<td>{{ $table['results'][$game->id][$day] ?? ($day > now(config('gali.timezone'))->day ? '—' : '--') }}</td>@endforeach</tr>
        @endfor
        </tbody></table></div>
    @empty<p>No published monthly records are available yet.</p>@endforelse
</section>

<section class="seo-box" id="records">
    <div class="result-heading"><h2>Historical Record Archive</h2><p>Select a year and market to browse available monthly records.</p></div>
    <div class="yearline-grid">
        @forelse($archiveYears as $archiveYear)
            <div class="yearline-row"><strong class="yearline-label">{{ $archiveYear }}</strong>@foreach($frontendGames as $game)<a href="{{ route('market', ['slug'=>$game['slug'],'year'=>$archiveYear,'month'=>($archiveYear === now(config('gali.timezone'))->year ? now(config('gali.timezone'))->month : 12)]) }}">{{ $game['name'] }}</a>@endforeach</div>
        @empty<p>Historical archive links will appear when dated results are added.</p>@endforelse
    </div>
    <p class="text-center"><a class="buttonb bgblue" href="{{ route('charts.index') }}">Open Complete Chart Archive ↗</a></p>
</section>

<section class="seo-box" id="guide">
    <div class="result-heading"><h2>{{ $siteSettings['homepage_guide_heading'] ?? 'How to Read the Result Chart' }}</h2></div>
    @if(filled($siteSettings['homepage_guide_content'] ?? null))<div class="homepage-seo-content">{!! $siteSettings['homepage_guide_content'] !!}</div>
    @else<p>Choose a market, then select a month and year to view the stored date-wise record. A dash means a result has not been published in the website database. Historical records are displayed as stored and are not predictions.</p>@endif
</section>

<section class="seo-box" id="seo">
    <div class="result-heading"><h2>{{ $siteSettings['homepage_seo_heading'] ?? 'About '.$siteSettings['title'].' Results & Records' }}</h2></div><div class="seo-jump" aria-label="Quick jumps"><a href="#markets-quick-record">Today</a><a href="#records">Old Record</a><a href="{{ route('charts.index') }}">Chart</a><a href="#faq">FAQ</a></div>
    @if(filled($siteSettings['homepage_seo_content'] ?? null))<div class="homepage-seo-content">{!! $siteSettings['homepage_seo_content'] !!}</div>
    @else<p>{{ $siteSettings['site_description'] ?? ($siteSettings['title'].' provides an organized view of published market results, market timings and monthly historical records.') }}</p>@endif
</section>

<section class="seo-box seo-box--terms" id="terms">
    <div class="result-heading"><h2>{{ $siteSettings['homepage_terms_heading'] ?? 'Important Result Chart Terms' }}</h2></div>
    @if(filled($siteSettings['homepage_terms_content'] ?? null))<div class="homepage-seo-content">{!! $siteSettings['homepage_terms_content'] !!}</div>
    @else
        <dl class="term-glossary"><div><dt>Jodi (जोड़ी)</dt><dd>A two-digit result value recorded from 00 to 99.</dd></div><div><dt>Market Result</dt><dd>A value saved and published for a market and date in the result archive.</dd></div><div><dt>Monthly Chart</dt><dd>A date-wise view of the stored records for one market and calendar month.</dd></div></dl>
    @endif
</section>

<section class="seo-box" id="faq">
    <div class="result-heading"><h2>Frequently Asked Questions (FAQ)</h2></div>
    <div class="faq-accordion">
        @forelse($faqs as $faq)<details class="faq-item"><summary>{{ $faq->question }}</summary><div class="faq-answer">{!! nl2br(e(strip_tags($faq->answer))) !!}</div></details>
        @empty
            @foreach([
                ['How do I view a monthly result chart?','Choose a market, then select the year and month you want to review.'],
                ['Where do the displayed numbers come from?','Numbers are read from published records saved in this website database. Missing records are not generated.'],
                ['What does a dash mean?','A dash means no result is currently available for that market and date.'],
                ['Do historical records predict future outcomes?','No. Historical records are for reference only and do not predict or guarantee future outcomes.']
            ] as $faq)<details class="faq-item"><summary>{{ $faq[0] }}</summary><div class="faq-answer">{{ $faq[1] }}</div></details>@endforeach
        @endforelse
    </div>
</section>

<section class="seo-box" id="disclaimer">
    <div class="result-heading"><h2>Disclaimer</h2></div>
    <p>{{ $siteSettings['disclaimer_content'] ?? 'This website provides informational records only. It does not verify every third-party source or guarantee outcomes. Users must comply with applicable local laws.' }}</p>
    <a class="market-arrow" href="{{ route('page', 'disclaimer') }}">Read full disclaimer →</a>
</section>

@foreach($sections->where('location', 'before_footer') as $section) @include('frontend.partials.home-section', ['section' => $section]) @endforeach
@foreach($sections->where('location', 'after_footer') as $section) @include('frontend.partials.home-section', ['section' => $section]) @endforeach
@endsection
