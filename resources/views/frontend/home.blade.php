@extends('frontend.layout')
@section('title', $siteSettings['meta_title'] ?? config('gali.site_name').' | Today Live Result, Market Time & Monthly Chart')
@section('canonical', $canonical)
@section('content')
<div class="site-ticker"><div class="site-ticker__mask"><div class="site-ticker__track">@foreach($frontendGames as $game)<span class="site-ticker__item">{{ strtoupper($game['name']) }} <b>{{ $game['today'] }}</b><i class="site-ticker__sep">◆</i></span>@endforeach @foreach($frontendGames as $game)<span class="site-ticker__item">{{ strtoupper($game['name']) }} <b>{{ $game['today'] }}</b><i class="site-ticker__sep">◆</i></span>@endforeach</div></div></div>
<section class="freshness-wrap"><div class="freshness-headline">{{ config('gali.site_name') }} — Daily result records for {{ now(config('gali.timezone'))->format('d F Y') }} with market timings and historical charts.</div><div class="freshness-disclaimer">DISCLAIMER: This website is an independent informational platform. We do not promote gambling. Users are responsible for following local laws. <a class="freshness-more" href="#disclaimer">Read more</a></div><div class="freshness-updated">Updated: {{ now(config('gali.timezone'))->format('d M Y, h:i:s A') }} IST</div></section>
@foreach($sections->where('location', 'first_place') as $section) @include('frontend.partials.home-section', ['section' => $section]) @endforeach
@if($featuredGame)
<section class="resultmain">
    <div class="livegame"><span>Latest Published Result</span><strong>{{ $featuredGame['name'] }}</strong></div>
    <div class="liveresult"><span>{{ $featuredGame['latest_date'] }}</span><strong>{{ $featuredGame['latest_result'] }}</strong></div>
    <a class="market-arrow" href="{{ route('market', $featuredGame['slug']) }}">View {{ $featuredGame['name'] }} record chart →</a>
</section>
@endif
<section class="boxc3" id="markets"><div class="result-heading"><h1>Today Live Results</h1><p>Market time · Record chart · Yesterday / Today</p></div><div class="quick-record-box"><table class="quick-record-table"><thead><tr><th>Market</th><th>Time</th><th>Yesterday</th><th>Today</th><th>Chart</th></tr></thead><tbody>
@forelse($frontendGames as $game)<tr><td class="qr-market"><a href="{{ route('market', $game['slug']) }}">{{ $game['name'] }}</a></td><td class="qr-time">{{ $game['time'] }}</td><td>{{ $game['yesterday'] }}</td><td>{{ $game['today'] }}</td><td class="qr-link"><a href="{{ route('market', $game['slug']) }}">Record Chart</a></td></tr>
@empty<tr><td colspan="5">No active markets are configured yet.</td></tr>@endforelse
</tbody></table></div></section>
@foreach(['second_place','second_place_another','third_place','third_place_another'] as $position) @foreach($sections->where('location', $position) as $section) @include('frontend.partials.home-section', ['section' => $section]) @endforeach @endforeach
<section class="market-list" id="records"><div class="result-heading"><h2>Market Record Charts</h2><p>Choose a market to view its monthly record.</p></div><div class="market-list-grid">
@foreach($frontendGames as $game)<article class="market-card"><a class="market-card-head" href="{{ route('market', $game['slug']) }}"><span class="market-card-name">{{ $game['name'] }}</span><span class="market-card-time">{{ $game['time'] }}</span></a><div class="market-card-body"><div class="market-card-res"><span>Yesterday <b>{{ $game['yesterday'] }}</b></span><span>Today <b>{{ $game['today'] }}</b></span></div><a class="market-arrow" href="{{ route('market', $game['slug']) }}">View monthly chart →</a></div></article>@endforeach
</div></section>
@foreach($chartTables as $tableIndex => $table)<section class="seo-box quick-record-box" id="{{ $tableIndex === 0 ? 'charts' : 'charts-'.$tableIndex }}"><div class="result-heading"><h2>{{ now(config('gali.timezone'))->format('F Y') }} Monthly Chart</h2></div><div class="table-scroll"><table class="khaiwal-table"><thead><tr><th>Date</th>@foreach($table['games'] as $game)<th>{{ $game->name }}</th>@endforeach</tr></thead><tbody>
@for($day=1;$day<=$daysInMonth;$day++)<tr><th>{{ sprintf('%02d',$day) }}</th>@foreach($table['games'] as $game)<td>{{ $table['results'][$game->id][$day] ?? ($day > now(config('gali.timezone'))->day ? '—' : '--') }}</td>@endforeach</tr>@endfor
</tbody></table></div></section>@endforeach
@foreach($sections->where('location', 'before_footer') as $section) @include('frontend.partials.home-section', ['section' => $section]) @endforeach
@if($faqs->isNotEmpty())<section class="seo-box" id="faq"><h2>Frequently Asked Questions</h2><div class="faq-accordion">@foreach($faqs as $faq)<details class="faq-item"><summary>{{ $faq->question }}</summary><div class="faq-answer">{!! nl2br(e(strip_tags($faq->answer))) !!}</div></details>@endforeach</div></section>@endif
<section class="seo-box" id="disclaimer"><h2>Disclaimer</h2><p>This platform publishes informational records only. It is not affiliated with betting operators and does not guarantee outcomes. Users must comply with applicable laws.</p></section>
@foreach($sections->where('location', 'after_footer') as $section) @include('frontend.partials.home-section', ['section' => $section]) @endforeach
@endsection
