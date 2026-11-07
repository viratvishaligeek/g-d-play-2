@extends('frontend.layout')
@section('title', $post->title.' | '.$siteName)
@section('canonical', $canonical)
@section('content')
<section class="freshness-wrap"><div class="freshness-headline">Community Discussion</div><div class="freshness-updated"><a href="{{ route('guessing') }}">← Back to Guessing Forum</a></div></section>
<article class="forum-post forum-post-detail">
    <header class="forum-post-head"><div class="forum-avatar">{{ strtoupper(substr($post->author_name ?: 'M',0,1)) }}</div><div class="forum-user"><strong>{{ $post->author_name ?: 'Community Member' }}</strong><small>{{ $post->created_at ? \Carbon\Carbon::parse($post->created_at)->format('d M Y, h:i A') : '' }}</small></div><span class="forum-meta">{{ $post->game_name ?: 'Market discussion' }}</span></header>
    <div class="forum-post-body"><h1>{{ $post->title }}</h1><p>{{ $post->body }}</p></div>
</article>
<section class="seo-box"><div class="result-heading"><h2>Replies ({{ $replies->count() }})</h2></div>
    @forelse($replies as $reply)<article class="forum-reply"><strong>{{ $reply->author_name ?: 'Community Member' }}</strong><small>{{ \Carbon\Carbon::parse($reply->created_at)->format('d M Y, h:i A') }}</small><p>{{ $reply->body }}</p></article>
    @empty<p>No replies have been approved for this discussion.</p>@endforelse
</section>
@endsection
