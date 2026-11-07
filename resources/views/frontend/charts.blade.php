@extends('frontend.layout')
@section('title', 'Market Record Charts | '.$siteName)
@section('canonical', config('gali.canonical_url').'/chart')
@section('content')
<section class="freshness-wrap"><div class="freshness-headline">Market Record Charts</div><div class="freshness-updated">Select a market to open its monthly historical results.</div></section>
<section class="market-list"><div class="result-heading"><h1>All Market Charts</h1><p>Monthly result records for active markets.</p></div><div class="market-list-grid">
@forelse($games as $game)
<article class="market-card market-card--full"><a class="market-card-head" href="{{ route('market', $game->slug) }}"><span class="market-card-name">{{ $game->name }}</span><span class="market-card-time">{{ $game->result_time ? \Carbon\Carbon::parse($game->result_time)->format('h:i A') : '—' }}</span></a><div class="market-card-body"><a class="market-arrow" href="{{ route('market', $game->slug) }}">Open monthly chart →</a></div></article>
@empty<p>No active markets are available.</p>@endforelse
</div></section>
@endsection
