@extends('frontend.layout')
@section('title', 'Market Guide & Result Chart Help | '.$siteName)
@section('canonical', config('gali.canonical_url').'/guessing')
@section('content')
<section class="freshness-wrap"><div class="freshness-headline">Market Guide & Result Information</div><div class="freshness-updated">Understand the result board, market timing and historical chart navigation.</div></section>
<section class="guessing-intro">
    <h1>{{ $siteSettings['homepage_guide_heading'] ?? 'Satta Result Guide & Market Records' }}</h1>
    @if(filled($siteSettings['homepage_guide_content'] ?? null))
        <div class="homepage-seo-content">{!! $siteSettings['homepage_guide_content'] !!}</div>
    @else
        <p>This page explains how to browse the market directory and read date-wise historical charts. Select a market below to open its saved monthly records.</p>
        <p>Published result values are displayed as stored. A dash means that a value is not currently available in the database. Historical data does not predict or guarantee future outcomes.</p>
    @endif
    <div class="homepage-main-actions"><a class="buttonb bgblue" href="{{ route('charts.index') }}">Open Record Chart ↗</a><a class="buttonb bggreen" href="{{ route('home') }}#markets">Browse Markets ↗</a></div>
</section>
<section class="market-list"><div class="result-heading"><h2>Market Record Directory</h2></div><div class="market-list-grid">
    @forelse($games as $game)<article class="market-card"><a class="market-card-head" href="{{ route('market', $game->slug) }}"><span class="market-card-name">{{ $game->name }}</span></a><div class="market-card-body"><a class="market-arrow" href="{{ route('market', $game->slug) }}">View result history →</a></div></article>@empty<p>No active markets are available.</p>@endforelse
</div></section>
<section class="seo-box"><div class="result-heading"><h2>Helpful Links</h2></div><div class="seo-jump"><a href="{{ route('home') }}">Live Results</a><a href="{{ route('charts.index') }}">Monthly Charts</a><a href="{{ route('page','privacy-policy') }}">Privacy Policy</a><a href="{{ route('page','terms-conditions') }}">Terms & Conditions</a><a href="{{ route('page','disclaimer') }}">Disclaimer</a></div></section>
@endsection
