@extends('frontend.layout')
@section('title', 'Market Record Charts | '.$siteName)
@section('canonical', config('gali.canonical_url').'/chart')
@section('content')
<section class="freshness-wrap"><div class="freshness-headline">Market Record Charts</div><div class="freshness-updated">Browse all active markets and every year/month with saved result records.</div></section>
<section class="seo-box"><div class="result-heading"><h1>Satta King Record Chart</h1><p>Select a market, then choose an available year and month. Only months with stored records are linked.</p></div></section>
<section class="market-list"><div class="market-list-grid">
@forelse($games as $game)
    <article class="market-card market-card--full">
        <a class="market-card-head" href="{{ route('market', $game->slug) }}"><span class="market-card-name">{{ $game->name }}</span><span class="market-card-time">{{ $game->result_time ? \Carbon\Carbon::parse($game->result_time)->format('h:i A') : 'Market time not set' }}</span></a>
        <div class="market-card-body">
            @php($years = $archiveByGame->get($game->id, collect()))
            @forelse($years as $year => $months)
                <div class="chart-year-group"><h3>{{ $year }}</h3><div class="homepage-archive-months">
                    @foreach($months as $month)<a href="{{ route('market', ['slug'=>$game->slug,'year'=>$year,'month'=>$month]) }}">{{ \Carbon\Carbon::create()->month($month)->format('F') }}</a>@endforeach
                </div></div>
            @empty<p>No historical records have been published for this market yet.</p>@endforelse
        </div>
    </article>
@empty<p>No active markets are available.</p>@endforelse
</div></section>
@endsection
