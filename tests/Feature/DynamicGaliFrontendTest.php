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
            $table->time('result_time')->nullable(); $table->time('play_start')->nullable(); $table->time('play_end')->nullable(); $table->decimal('reward', 10, 2)->default(1); $table->string('last_result')->nullable();
            $table->timestamps(); $table->softDeletes();
        });
        Schema::create('results', function (Blueprint $table) {
            $table->id(); $table->string('site_key'); $table->unsignedBigInteger('game_id');
            $table->date('game_date'); $table->string('type'); $table->string('number')->nullable(); $table->timestamps(); $table->unique(['game_id', 'game_date', 'type']);
        });
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id(); $table->string('site_key'); $table->string('title'); $table->longText('content')->nullable();
            $table->string('status')->default('active'); $table->string('location')->nullable();
            $table->string('phone')->nullable(); $table->string('whatsapp')->nullable(); $table->string('telegram')->nullable();
            $table->string('background')->nullable(); $table->string('image')->nullable(); $table->string('image_alt')->nullable(); $table->string('button_label')->nullable(); $table->string('button_url')->nullable(); $table->string('button_style')->nullable(); $table->string('layout')->nullable(); $table->unsignedInteger('display_order')->default(0); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('faqs', function (Blueprint $table) {
            $table->id(); $table->string('site_key'); $table->string('question'); $table->text('answer'); $table->string('platform', 8)->default('both'); $table->timestamps();
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
            $table->id(); $table->string('site_key'); $table->string('option'); $table->longText('value')->nullable(); $table->timestamps(); $table->unique(['site_key', 'option']);
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
            'status' => 'active', 'location' => 'first_place', 'image' => 'https://placehold.co/640x360/111827/ffffff?text=Demo', 'image_alt' => 'Demo image', 'button_label' => 'Browse Demo Section', 'button_url' => '/chart', 'button_style' => 'primary', 'layout' => 'image-left', 'display_order' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('settings')->insert([
            ['site_key' => 'galidisawar', 'option' => 'title', 'value' => 'Gali Demo Brand', 'created_at' => now(), 'updated_at' => now()],
            ['site_key' => 'galidisawar', 'option' => 'site_logo', 'value' => 'gali-demo-logo.svg', 'created_at' => now(), 'updated_at' => now()],
            ['site_key' => 'galidisawar', 'option' => 'header_cta_label', 'value' => 'Open Chart', 'created_at' => now(), 'updated_at' => now()],
            ['site_key' => 'galidisawar', 'option' => 'header_cta_url', 'value' => '/chart', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->get('/');
        $response->assertOk()->assertSee('Gali Demo Brand')->assertSee('Managed notice content')
            ->assertSee('LATEST PUBLISHED RESULT')->assertSee('42')->assertSee('Browse Demo Section')
            ->assertSee('https://placehold.co/640x360/111827/ffffff?text=Demo')
            ->assertSee('/logos/gali-demo-logo.svg')->assertSee('Open Chart')
            ->assertDontSee('Private Play Online Market');
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

    public function test_demo_seeder_is_repeatable_and_keeps_sample_data_site_scoped(): void
    {
        DB::table('games')->insert([
            'site_key' => 'galidisawar', 'name' => 'Existing Live Market', 'slug' => 'existing-live-market',
            'status' => 'active', 'serial' => 1, 'result_time' => '20:00:00',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        app(\\Database\\Seeders\\GaliDisawarDemoSeeder::class)->run();
        $firstCounts = [
            'games' => DB::table('games')->where('site_key', 'galidisawar')->count(),
            'results' => DB::table('results')->where('site_key', 'galidisawar')->count(),
            'sections' => DB::table('home_pages')->where('site_key', 'galidisawar')->count(),
            'faqs' => DB::table('faqs')->where('site_key', 'galidisawar')->count(),
            'pages' => DB::table('pages')->where('site_key', 'galidisawar')->count(),
        ];

        app(\\Database\\Seeders\\GaliDisawarDemoSeeder::class)->run();
        $secondCounts = [
            'games' => DB::table('games')->where('site_key', 'galidisawar')->count(),
            'results' => DB::table('results')->where('site_key', 'galidisawar')->count(),
            'sections' => DB::table('home_pages')->where('site_key', 'galidisawar')->count(),
            'faqs' => DB::table('faqs')->where('site_key', 'galidisawar')->count(),
            'pages' => DB::table('pages')->where('site_key', 'galidisawar')->count(),
        ];

        $this->assertSame($firstCounts, $secondCounts);
        $this->assertSame(7, $firstCounts['games']);
        $this->assertSame(7, $firstCounts['sections']);
        $this->assertSame(4, $firstCounts['faqs']);
        $this->assertSame(3, $firstCounts['pages']);
        $this->assertSame(0, DB::table('games')->where('site_key', 'play-online-khaiwal')->count());
        $this->assertDatabaseHas('games', ['site_key' => 'galidisawar', 'slug' => 'existing-live-market']);
        $this->assertDatabaseHas('settings', ['site_key' => 'galidisawar', 'option' => 'site_logo', 'value' => 'gali-demo-logo.svg']);
    }

    public function test_guessing_forum_reads_only_approved_posts_and_replies_for_this_site(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('site_key')->default('galidisawar');
        });
        Schema::create('community_posts', function (Blueprint $table): void {
            $table->id(); $table->string('site_key'); $table->unsignedBigInteger('game_id');
            $table->unsignedBigInteger('user_id'); $table->string('title', 120); $table->text('body');
            $table->string('status', 16)->default('pending'); $table->timestamps();
        });
        Schema::create('community_replies', function (Blueprint $table): void {
            $table->id(); $table->string('site_key'); $table->unsignedBigInteger('community_post_id');
            $table->unsignedBigInteger('user_id'); $table->text('body'); $table->timestamps();
        });

        $gameId = DB::table('games')->insertGetId([
            'site_key' => 'galidisawar', 'name' => 'Forum Demo Market', 'slug' => 'forum-demo',
            'status' => 'active', 'serial' => 1, 'result_time' => '23:00:00',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $userId = DB::table('users')->insertGetId([
            'site_key' => 'galidisawar', 'name' => 'Demo Member',
            'email' => 'demo-member@example.test', 'password' => bcrypt('secret'),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $otherUserId = DB::table('users')->insertGetId([
            'site_key' => 'play-online-khaiwal', 'name' => 'Private Member',
            'email' => 'private-member@example.test', 'password' => bcrypt('secret'),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $postId = DB::table('community_posts')->insertGetId([
            'site_key' => 'galidisawar', 'game_id' => $gameId, 'user_id' => $userId,
            'title' => 'Approved community topic', 'body' => 'This is a site-scoped approved discussion.',
            'status' => 'approved', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('community_posts')->insert([
            'site_key' => 'play-online-khaiwal', 'game_id' => $gameId, 'user_id' => $otherUserId,
            'title' => 'Private site topic', 'body' => 'This must not be visible.',
            'status' => 'approved', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('community_replies')->insert([
            'site_key' => 'galidisawar', 'community_post_id' => $postId, 'user_id' => $userId,
            'body' => 'Visible demo reply.', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('community_replies')->insert([
            'site_key' => 'play-online-khaiwal', 'community_post_id' => $postId, 'user_id' => $otherUserId,
            'body' => 'Private reply must not leak.', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->get('/guessing')->assertOk()->assertSee('Approved community topic')->assertDontSee('Private site topic');
        $this->get('/guessing/'.$postId)->assertOk()->assertSee('This is a site-scoped approved discussion.')
            ->assertSee('Visible demo reply.')->assertDontSee('Private reply must not leak.');
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
        $this->get('/chart')->assertOk()->assertSee('Disawar')->assertSee(\Carbon\Carbon::create()->month($month)->format('F'));
        $this->get('/guessing')->assertOk()->assertSee('Market Guide');
        $this->get('/result/private-market')->assertNotFound();
    }
}
