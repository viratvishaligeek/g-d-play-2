<section class="market-card market-card--full homepage-managed-section homepage-managed-section--{{ $section->layout ?: 'standard' }}" id="homepage-section-{{ $section->id }}" style="background: {{ $section->background ?: '#fff' }}" data-layout="{{ $section->layout ?: 'standard' }}">
    @if(filled($section->title))<header class="market-card-head"><span class="market-card-name">{{ $section->title }}</span></header>@endif
    <div class="homepage-managed-inner">
        @if($section->image_url)
            <figure class="homepage-managed-media"><img src="{{ $section->image_url }}" alt="{{ $section->image_alt ?: $section->title }}" loading="lazy"></figure>
        @endif
        <div class="market-card-body homepage-managed-content">
            @if(filled($section->content)){!! $section->content !!}@endif
            @if(filled($section->button_label) && $section->safe_button_url)
                <p class="homepage-managed-cta"><a class="buttonb {{ match($section->button_style) { 'secondary' => 'bgblue', 'outline' => 'button-outline', 'whatsapp' => 'bggreen', 'telegram' => 'bgblue', default => 'bggreen' } }}" href="{{ $section->safe_button_url }}" @if(str_starts_with($section->safe_button_url, 'http')) target="_blank" rel="noopener noreferrer" @endif>{{ $section->button_label }} ↗</a></p>
            @endif
        </div>
    </div>
    @if($section->phone || $section->whatsapp || $section->telegram)
        <div class="btn-wrap">
            @if($section->phone)<a class="btn call" href="tel:{{ preg_replace('/[^0-9+]/', '', $section->phone) }}">Call</a>@endif
            @if($section->whatsapp)
                @php($whatsappDigits = preg_replace('/\D/', '', $section->whatsapp))
                @php($whatsappUrl = filter_var($section->whatsapp, FILTER_VALIDATE_URL) && in_array(parse_url($section->whatsapp, PHP_URL_SCHEME), ['http','https'], true) ? $section->whatsapp : ($whatsappDigits !== '' ? 'https://wa.me/'.$whatsappDigits : null))
                @if($whatsappUrl)<a class="btn whatsapp" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>@endif
            @endif
            @if($section->telegram)
                @php($telegramUrl = filter_var($section->telegram, FILTER_VALIDATE_URL) && in_array(parse_url($section->telegram, PHP_URL_SCHEME), ['http','https'], true) ? $section->telegram : (preg_match('/^@?[A-Za-z0-9_]{5,32}$/', trim($section->telegram)) ? 'https://t.me/'.ltrim(trim($section->telegram), '@') : null))
                @if($telegramUrl)<a class="btn whatsapp" href="{{ $telegramUrl }}" target="_blank" rel="noopener noreferrer">Telegram</a>@endif
            @endif
        </div>
    @endif
</section>
