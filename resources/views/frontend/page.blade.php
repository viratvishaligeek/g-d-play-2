@extends('frontend.layout')
@section('title', $title.' | '.$siteSettings['title'] ?? config('gali.site_name'))
@section('description', $description)
@section('canonical', $canonical)
@section('content')
<section class="freshness-wrap"><div class="freshness-headline">{{ $page->name }}</div></section>
<article class="seo-box cms-page-content">{!! $page->content !!}</article>
@endsection
