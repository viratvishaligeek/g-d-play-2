<!doctype html>
<html lang="hi" class="scroll-smooth">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#006ef5"><link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', $siteSettings['meta_title'] ?? config('gali.site_name').' | Live Results & Record Charts')</title>
    <meta name="description" content="@yield('description', $siteSettings['meta_description'] ?? 'Latest Gali and Disawar result records, market timings and historical charts.')">
    <meta name="keywords" content="{{ $siteSettings['meta_keywords'] ?? 'Gali Disawar, Gali result, Disawar result, monthly chart, old records' }}">
    <meta property="og:title" content="@yield('title', $siteSettings['meta_title'] ?? config('gali.site_name'))">
    <meta property="og:description" content="@yield('description', $siteSettings['meta_description'] ?? 'Latest Gali and Disawar result records and charts.')">
    <meta property="og:type" content="website">
    <meta name="robots" content="index, follow"><link rel="canonical" href="@yield('canonical', !empty($siteSettings['canonical_url']) ? rtrim($siteSettings['canonical_url'], '/').'/' : config('gali.canonical_url').'/')">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=Noto+Sans+Devanagari:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/css/theme-base.css') }}"><link rel="stylesheet" href="{{ asset('frontend/css/theme-fonts.css') }}"><link rel="stylesheet" href="{{ asset('frontend/css/site-theme.css') }}"><link rel="stylesheet" href="{{ asset('frontend/css/custom-animations.css') }}">@stack('head')
</head>
<body class="bg-white text-black font-sans overflow-x-hidden">
<header class="site-header"><div class="site-header__inner">
    <a class="site-brand" href="{{ route('home') }}"><span class="site-brand__mark">GALI DISAWAR</span><span class="site-brand__num">LIVE</span></a>
    <nav class="site-nav" aria-label="Main navigation"><a class="site-nav__link is-active" href="{{ route('home') }}">Home</a><a class="site-nav__link" href="{{ route('home') }}#markets">Markets</a><a class="site-nav__link" href="{{ route('charts.index') }}">Chart</a><a class="site-nav__cta" href="{{ route('home') }}#records">Records</a>@isset($navPages) @foreach($navPages as $navPage)<a class="site-nav__link" href="{{ route('page', $navPage->slug) }}">{{ $navPage->name }}</a>@endforeach @endisset</nav>
</div></header>
<main>@yield('content')</main>
<footer class="px-6 py-12 bg-black text-white border-t border-black/10 relative z-10 mt-20"><div class="max-w-7xl mx-auto text-center space-y-8">
    <div class="text-xl font-black italic tracking-tighter uppercase">{{ config('gali.site_name') }}</div>
    <p class="max-w-4xl mx-auto text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-loose">This website is an informational record archive. It does not organize or promote gambling. Please follow applicable local laws.</p>
    <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-4 text-[9px] font-black uppercase tracking-[0.3em] text-gray-400"><a href="{{ route('home') }}">Home</a><a href="{{ route('home') }}#markets">Markets</a><a href="{{ route('home') }}#charts">Monthly Chart</a></div>
    <small class="text-gray-500">© {{ date('Y') }} {{ config('gali.site_name') }}. All rights reserved.</small>
</div></footer>
<script src="{{ asset('frontend/js/site-theme.js') }}" defer></script>@stack('scripts')
</body></html>
