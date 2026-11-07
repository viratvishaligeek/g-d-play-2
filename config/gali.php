<?php

return [
    'site_key' => env('GALI_SITE_KEY', 'galidisawar'),
    'site_name' => env('GALI_SITE_NAME', 'Gali Disawar'),
    'canonical_url' => rtrim(env('GALI_CANONICAL_URL', env('APP_URL', 'https://galidisawar.com')), '/'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),
    'chart_chunk_size' => max(1, min(50, (int) env('GALI_CHART_CHUNK_SIZE', 10))),
];
