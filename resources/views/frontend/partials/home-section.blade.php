<section class="market-card market-card--full homepage-managed-section" id="homepage-section-{{ $section->id }}" style="background: {{ $section->background ?: '#fff' }}">
@if(filled($section->title))<header class="market-card-head"><span class="market-card-name">{{ $section->title }}</span></header>@endif
@if(filled($section->content))<div class="market-card-body homepage-managed-content">{!! $section->content !!}</div>@endif
@if($section->phone || $section->whatsapp || $section->telegram)<div class="btn-wrap">
@if($section->phone)<a class="btn call" href="tel:{{ preg_replace('/[^0-9+]/', '', $section->phone) }}">Call</a>@endif
@if($section->whatsapp)<a class="btn whatsapp" href="https://wa.me/{{ preg_replace('/\D/', '', $section->whatsapp) }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>@endif
@if($section->telegram)<a class="btn whatsapp" href="{{ str_starts_with($section->telegram, 'http') ? $section->telegram : 'https://t.me/'.ltrim($section->telegram, '@') }}" target="_blank" rel="noopener noreferrer">Telegram</a>@endif
</div>@endif</section>
