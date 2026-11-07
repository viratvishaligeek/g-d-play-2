<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('gali:demo-seed {--force : Allow adding demo rows when APP_ENV=production}', function () {
    if (app()->environment('production') && !$this->option('force')) {
        $this->error('Demo seeding is blocked in production by default. Use --force only on a disposable preview site.');
        return 1;
    }

    $exitCode = $this->call('db:seed', [
        '--class' => \Database\Seeders\GaliDisawarDemoSeeder::class,
        '--force' => (bool) $this->option('force'),
    ]);

    return $exitCode;
})->purpose('Add isolated Gali Disawar demo markets, records, pages, FAQs and homepage sections');
