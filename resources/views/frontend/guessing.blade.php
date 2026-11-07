@extends('frontend.layout')
@section('title', 'Market Guide & Guessing Forum | '.$siteName)
@section('canonical', config('gali.canonical_url').'/guessing')
@section('content')
<section class="freshness-wrap"><div class="freshness-headline">Market Guide & Community Forum</div><div class="freshness-updated">Read the market guide and browse administrator-approved community posts.</div></section>
<section class="guessing-intro">
    <h1>{{ $siteSettings['homepage_guide_heading'] ?? 'Satta Result Guide & Market Records' }}</h1>
    @if(filled($siteSettings['homepage_guide_content'] ?? null))
        <div class="homepage-seo-content">{!! $siteSettings['homepage_guide_content'] !!}</div>
    @else
        <p>This page explains how to browse the market directory and read date-wise historical charts. Select a market below to open its saved monthly records.</p>
        <p>Published result values are displayed as stored. A dash means that a value is not currently available in the database. Historical data does not predict or guarantee future outcomes.</p>
    @endif
    <div class="homepage-main-actions">
        <a class="buttonb bgblue" href="{{ route('charts.index') }}">Open Record Chart ↗</a>
        <a class="buttonb bggreen" href="{{ route('home') }}#markets">Browse Markets ↗</a>
        @if(filled($siteSettings['guessing_login_url_safe'] ?? null))<a class="buttonb" href="{{ $siteSettings['guessing_login_url_safe'] }}" target="_blank" rel="noopener noreferrer">Login ↗</a>@endif
        @if(filled($siteSettings['guessing_register_url_safe'] ?? null))<a class="buttonb" href="{{ $siteSettings['guessing_register_url_safe'] }}" target="_blank" rel="noopener noreferrer">Register ↗</a>@endif
        @if(filled($siteSettings['guessing_submit_url_safe'] ?? null))<a class="buttonb bggreen" href="{{ $siteSettings['guessing_submit_url_safe'] }}" target="_blank" rel="noopener noreferrer">Submit a Post ↗</a>@endif
    </div>
</section>

<section class="market-list">
    <div class="result-heading"><h2>Approved Community Posts</h2><p>Only posts approved for this site are displayed here.</p></div>
    <div class="forum-post-list">
        @forelse($posts as $post)
            <article class="forum-post">
                <header class="forum-post-head"><div class="forum-avatar">{{ strtoupper(substr($post->author_name ?: 'M',0,1)) }}</div><div class="forum-user"><strong>{{ $post->author_name ?: 'Community Member' }}</strong><small>{{ $post->created_at ? \Carbon\Carbon::parse($post->created_at)->format('d M Y, h:i A') : '' }}</small></div><span class="forum-meta">{{ $post->game_name ?: 'Market discussion' }}</span></header>
                <div class="forum-post-body"><h3>{{ $post->title }}</h3><p>{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 260) }}</p></div>
                <footer class="forum-post-actions"><a class="market-arrow" href="{{ route('guessing.show', $post->id) }}">Read discussion →</a></footer>
            </article>
        @empty
            <div class="empty-state"><b>No approved community posts yet.</b><p>When the site administrator approves a post for Gali Disawar, it will appear here. No sample forum posts are fabricated.</p></div>
        @endforelse
    </div>
    @if(method_exists($posts, 'links'))<div class="forum-pagination">{{ $posts->links() }}</div>@endif
</section>

<section class="market-list"><div class="result-heading"><h2>Market Record Directory</h2></div><div class="market-list-grid">
    @forelse($games as $game)<article class="market-card"><a class="market-card-head" href="{{ route('market', $game->slug) }}"><span class="market-card-name">{{ $game->name }}</span></a><div class="market-card-body"><a class="market-arrow" href="{{ route('market', $game->slug) }}">View result history →</a></div></article>@empty<p>No active markets are available.</p>@endforelse
</div></section>

<section class="seo-box"><div class="result-heading"><h2>Helpful Links</h2></div><div class="seo-jump"><a href="{{ route('home') }}">Live Results</a><a href="{{ route('charts.index') }}">Monthly Charts</a><a href="{{ route('page','privacy-policy') }}">Privacy Policy</a><a href="{{ route('page','terms-and-conditions') }}">Terms & Conditions</a><a href="{{ route('page','disclaimer') }}">Disclaimer</a></div></section>
@endsection
