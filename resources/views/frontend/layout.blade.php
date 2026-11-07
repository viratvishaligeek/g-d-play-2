<!doctype html>
<html lang="hi" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#006ef5">
    <title>@yield('title', $siteSettings['meta_title'] ?? ($siteSettings['title'].' | Live Results & Record Charts'))</title>
    <meta name="description" content="@yield('description', $siteSettings['meta_description'] ?? $siteSettings['site_description'] ?? 'Latest market result records, market timings and historical charts.')">
    <meta name="keywords" content="{{ $siteSettings['meta_keywords'] ?? '' }}">
    <meta name="robots" content="{{ $siteSettings['robots_default'] ?? 'index,follow' }}">
    <link rel="canonical" href="@yield('canonical', !empty($siteSettings['canonical_url']) ? rtrim($siteSettings['canonical_url'], '/').'/' : config('gali.canonical_url').'/')">
    <link rel="icon" href="{{ $siteSettings['site_favicon_url'] ?? asset('favicon.svg') }}">
    <meta property="og:title" content="{{ $siteSettings['og_title'] ?? ($siteSettings['meta_title'] ?? $siteSettings['title']) }}">
    <meta property="og:description" content="{{ $siteSettings['og_description'] ?? ($siteSettings['meta_description'] ?? $siteSettings['site_description'] ?? '') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteSettings['title'] }}">
    @if(!empty($siteSettings['og_image_url']))<meta property="og:image" content="{{ $siteSettings['og_image_url'] }}">@endif
    <meta name="twitter:card" content="{{ $siteSettings['twitter_card'] ?? 'summary_large_image' }}">
    @if(!empty($siteSettings['og_image_url']))<meta name="twitter:image" content="{{ $siteSettings['og_image_url'] }}">@endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=Noto+Sans+Devanagari:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/css/theme-base.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/theme-fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/site-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/custom-animations.css') }}">
    @stack('head')
</head>
<body class="bg-white text-black font-sans overflow-x-hidden">
<header class="site-header" role="banner">
    <div class="site-header__inner">
        <a class="site-brand" title="{{ $siteSettings['title'] }} Home" href="{{ route('home') }}" aria-label="{{ $siteSettings['title'] }} home">
            @if(!empty($siteSettings['site_logo_url']))
                <img class="site-brand__logo" src="{{ $siteSettings['site_logo_url'] }}" alt="{{ $siteSettings['title'] }}" loading="eager">
            @else
                <span class="site-brand__mark">{{ $siteSettings['title'] }}</span><span class="site-brand__num">LIVE</span>
            @endif
        </a>
        <nav class="site-nav" id="primary-nav" aria-label="Primary navigation">
            <a class="site-nav__link {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="site-nav__link {{ request()->routeIs('guessing') ? 'is-active' : '' }}" href="{{ route('guessing') }}">Guessing</a>
            <a class="site-nav__link {{ request()->routeIs('charts.index','market') ? 'is-active' : '' }}" href="{{ route('charts.index') }}">Chart</a>
            <a class="site-nav__link" href="{{ route('home') }}#records">Records</a>
            @isset($navPages) @foreach($navPages as $navPage)
                <a class="site-nav__link {{ request()->routeIs('page') && request()->route('slug') === $navPage->slug ? 'is-active' : '' }}" href="{{ route('page', $navPage->slug) }}">{{ $navPage->name }}</a>
            @endforeach @endisset
            @if(!empty($siteSettings['header_cta_url_safe']) && !empty($siteSettings['header_cta_label']))
                <a class="site-nav__cta" href="{{ $siteSettings['header_cta_url'] }}" @if(str_starts_with($siteSettings['header_cta_url'], 'http')) target="_blank" rel="noopener noreferrer" @endif>{{ $siteSettings['header_cta_label'] }}</a>
            @elseif(!empty($siteSettings['social_whatsapp_safe']))
                <a class="site-nav__cta" href="{{ $siteSettings['social_whatsapp_safe'] }}" target="_blank" rel="noopener noreferrer">Contact Us</a>
            @endif
        </nav>
    </div>
    <div class="site-ticker" aria-label="{{ $siteSettings['title'] }} updates">
        <div class="site-ticker__mask"><div class="site-ticker__track">
            @php($tickerText = $siteSettings['web_marquee'] ?? $siteSettings['announcement_text'] ?? '')
            @if(filled($tickerText))
                @for($i=0;$i<3;$i++)<span class="site-ticker__item">{{ $tickerText }}</span><span class="site-ticker__sep">•</span>@endfor
            @else
                @foreach(['Today’s results','Previous results','Market timings','Historical charts','Year-wise records'] as $tickerItem)
                    <span class="site-ticker__item">{{ $tickerItem }}</span><span class="site-ticker__sep">•</span>
                @endforeach
                @foreach(['Today’s results','Previous results','Market timings','Historical charts','Year-wise records'] as $tickerItem)
                    <span class="site-ticker__item">{{ $tickerItem }}</span><span class="site-ticker__sep">•</span>
                @endforeach
            @endif
        </div></div>
    </div>
</header>
<main>@yield('content')</main>
<footer class="px-6 py-12 bg-black text-white border-t border-black/10 relative z-10 mt-20 site-footer">
    <div class="max-w-7xl mx-auto text-center space-y-8">
        <div class="flex items-center justify-center gap-2 mb-4">
            <span class="w-12 h-[1px] bg-brand-400/30"></span>
            @if(!empty($siteSettings['site_logo_url']))<img src="{{ $siteSettings['site_logo_url'] }}" alt="{{ $siteSettings['title'] }}" class="site-footer__logo" loading="lazy">
            @else<span class="text-xl font-black text-white italic tracking-tighter uppercase">{{ $siteSettings['title'] }}</span>@endif
            <span class="w-12 h-[1px] bg-brand-400/30"></span>
        </div>
        @if(filled($siteSettings['site_tagline'] ?? null))<p class="site-footer__tagline">{{ $siteSettings['site_tagline'] }}</p>@endif
        @if(filled($siteSettings['footer_description'] ?? null))<p class="max-w-4xl mx-auto text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-loose">{{ $siteSettings['footer_description'] }}</p>@endif
        <div class="site-footer__contacts">
            @if(filled($siteSettings['email'] ?? null))<a href="mailto:{{ $siteSettings['email'] }}">{{ $siteSettings['email'] }}</a>@endif
            @if(filled($siteSettings['contact_phone'] ?? null))<a href="tel:{{ preg_replace('/[^0-9+]/','',$siteSettings['contact_phone']) }}">{{ $siteSettings['contact_phone'] }}</a>@endif
            @if(filled($siteSettings['contact_address'] ?? null))<span>{{ $siteSettings['contact_address'] }}</span>@endif
        </div>
        <div class="site-footer__social">
            @foreach(['facebook'=>'Facebook','instagram'=>'Instagram','youtube'=>'YouTube','telegram'=>'Telegram','whatsapp'=>'WhatsApp'] as $key=>$label)
                @if(filled($siteSettings['social_'.$key.'_safe'] ?? null) && filter_var($siteSettings['social_'.$key.'_safe'], FILTER_VALIDATE_URL))
                    <a href="{{ $siteSettings['social_'.$key.'_safe'] }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>
                @endif
            @endforeach
        </div>
        <nav class="flex flex-wrap items-center justify-center gap-x-8 gap-y-4 text-[9px] font-black uppercase tracking-[0.3em] text-brand-400/60" aria-label="Footer navigation">
            <a href="{{ route('home') }}">Home</a><a href="{{ route('charts.index') }}">Monthly Chart</a><a href="{{ route('guessing') }}">Guessing</a>
            @isset($navPages) @foreach($navPages as $navPage)<a href="{{ route('page', $navPage->slug) }}">{{ $navPage->name }}</a>@endforeach @endisset
        </nav>
        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-gray-500">{{ $siteSettings['copyright_text'] ?? ('© '.date('Y').' '.$siteSettings['title'].' All Rights Reserved') }}</p>
    </div>
</footer>
@if(($siteSettings['sticky_social_enabled'] ?? '1') !== '0')
    @if(filled($siteSettings['social_telegram_safe'] ?? null))<a class="sticky-social sticky-social--telegram" href="{{ $siteSettings['social_telegram'] }}" target="_blank" rel="noopener noreferrer" aria-label="Join Telegram"><span>✈</span></a>@endif
    @if(filled($siteSettings['social_whatsapp'] ?? null))<a class="sticky-social sticky-social--whatsapp" href="{{ $siteSettings['social_whatsapp'] }}" target="_blank" rel="noopener noreferrer" aria-label="Contact on WhatsApp"><span>◉</span></a>@endif
@endif
<script src="{{ asset('frontend/js/site-theme.js') }}" defer></script>
@stack('scripts')
</body>
</html>
