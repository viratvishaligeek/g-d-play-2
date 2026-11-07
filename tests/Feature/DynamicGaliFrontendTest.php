<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DynamicGaliFrontendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['gali.site_key' => 'galidisawar', 'gali.site_name' => 'Gali Disawar', 'gali.canonical_url' => 'https://galidisawar.com']);

        Schema::create('games', function (Blueprint $table) {
            $table->id(); $table->string('site_key'); $table->string('name'); $table->string('slug');
            $table->string('status')->default('active'); $table->unsignedInteger('serial')->default(0);
            $table->time('result_time')->nullable(); $table->string('last_result')->nullable();
            $table->timestamps(); $table->softDeletes();
        });
        Schema::create('results', function (Blueprint $table) {
            $table->id(); $table->string('site_key'); $table->unsignedBigInteger('game_id');
            $table->date('game_date'); $table->string('type'); $table->string('number')->nullable(); $table->timestamps();
        });
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id(); $table->string('site_key'); $table->string('title'); $table->longText('content')->nullable();
            $table->string('status')->default('active'); $table->string('location')->nullable();
            $table->string('phone')->nullable(); $table->string('whatsapp')->nullable(); $table->string('telegram')->nullable();
            $table->string('background')->nullable(); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('faqs', function (Blueprint $table) {
            $table->id(); $table->string('site_key'); $table->string('question'); $table->text('answer'); $table->timestamps();
        });
        Schema::create('pages', function (Blueprint $table) {
            $table->id(); $table->string('site_key'); $table->string('name'); $table->string('slug');
            $table->text('content')->nullable(); $table->string('status')->default('active');
            $table->string('is_editable')->default('yes'); $table->boolean('menu_visible')->default(true);
            $table->unsignedInteger('menu_order')->default(0); $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable(); $table->text('meta_keywords')->nullable();
            $table->boolean('noindex')->default(false); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); $table->string('site_key'); $table->string('option'); $table->longText('value')->nullable(); $table->timestamps();
        });
    }

    public function test_homepage_only_shows_the_configured_site_and_managed_sections(): void
    {
        $resultDate = now(config('gali.timezone'))->subDays(3)->toDateString();
        $gameId = DB::table('games')->insertGetId([
            'site_key' => 'galidisawar', 'name' => 'Gali', 'slug' => 'gali', 'status' => 'active',
            'serial' => 1, 'result_time' => '23:00:00', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('games')->insert([
            'site_key' => 'play-online-khaiwal', 'name' => 'Private Play Online Market', 'slug' => 'private-market',
            'status' => 'active', 'serial' => 2, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('results')->insert([
            'site_key' => 'galidisawar', 'game_id' => $gameId, 'game_date' => $resultDate, 'type' => 'jodi',
            'number' => '42', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('home_pages')->insert([
            'site_key' => 'galidisawar', 'title' => 'Managed Gali notice', 'content' => '<p>Managed notice content</p>',
            'status' => 'active', 'location' => 'first_place', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $response = $this->get('/');
        $response->assertOk()->assertSee('Gali')->assertSee('Managed notice content')->assertSee('Latest Published Result')->assertSee('42')->assertDontSee('Private Play Online Market');
    }

    public function test_cms_pages_and_seo_settings_are_site_scoped(): void
    {
        DB::table('pages')->insert([
            'site_key' => 'galidisawar', 'name' => 'Privacy Policy', 'slug' => 'privacy-policy',
            'content' => '<p>Gali Disawar privacy information</p>', 'status' => 'active',
            'is_editable' => 'yes', 'menu_visible' => true, 'menu_order' => 1,
            'meta_title' => 'Gali Privacy', 'meta_description' => 'Privacy information for Gali Disawar',
            'meta_keywords' => 'Gali privacy', 'noindex' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('pages')->insert([
            'site_key' => 'play-online-khaiwal', 'name' => 'Private Page', 'slug' => 'private-page',
            'content' => '<p>Must not leak</p>', 'status' => 'active',
            'is_editable' => 'yes', 'menu_visible' => true, 'menu_order' => 1,
            'noindex' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('settings')->insert([
            ['site_key' => 'galidisawar', 'option' => 'meta_title', 'value' => 'Gali Disawar SEO Title', 'created_at' => now(), 'updated_at' => now()],
            ['site_key' => 'galidisawar', 'option' => 'canonical_url', 'value' => 'https://galidisawar.com', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->get('/privacy-policy')->assertOk()
            ->assertSee('Gali Disawar privacy information')
            ->assertSee('Gali Privacy')
            ->assertSee('https://galidisawar.com/privacy-policy')
            ->assertDontSee('Must not leak');
        $this->get('/')->assertOk()->assertSee('Gali Disawar SEO Title');
        $this->get('/private-page')->assertNotFound();
    }

    public function test_market_chart_and_json_chart_endpoint_use_site_scoped_game(): void
    {
        $gameId = DB::table('games')->insertGetId([
            'site_key' => 'galidisawar', 'name' => 'Disawar', 'slug' => 'disawar', 'status' => 'active',
            'serial' => 1, 'result_time' => '05:00:00', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $year = (int) now(config('gali.timezone'))->year;
        $month = (int) now(config('gali.timezone'))->month;
        $date = now(config('gali.timezone'))->startOfMonth()->toDateString();
        DB::table('results')->insert([
            'site_key' => 'galidisawar', 'game_id' => $gameId, 'game_date' => $date, 'type' => 'jodi',
            'number' => '87', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->get('/result/disawar')->assertOk()->assertSee('Disawar')->assertSee('87');
        $this->post('/get_result', ['slug' => 'disawar', 'year' => $year, 'month' => $month])
            ->assertOk()->assertJsonPath('game.slug', 'disawar')->assertJsonPath('days.0.result', '87');
        $this->get('/result/private-market')->assertNotFound();
    }
}
