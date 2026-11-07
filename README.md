# Gali Disawar Laravel frontend

This application renders the Gali Disawar public website in Laravel Blade using the visual system exported in `theme/`. It reads the existing Play Online Khaiwal database directly; it does not create a second API backend or copy production records.

## Runtime requirement

The committed Composer lockfile currently includes Symfony 8.1 components that require PHP 8.4.1 or newer. Use PHP 8.4.1+ on the hosting account unless the lockfile is deliberately regenerated for an older supported runtime.

## Shared database setup

Configure the existing Play Online Khaiwal MySQL database credentials in the deployment environment's `.env` file. Do not commit real credentials.

```dotenv
APP_URL=https://galidisawar.com
APP_TIMEZONE=Asia/Kolkata
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_existing_database
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
GALI_SITE_KEY=galidisawar
GALI_SITE_NAME="Gali Disawar"
GALI_CANONICAL_URL=https://galidisawar.com
GALI_CHART_CHUNK_SIZE=10
```

The frontend applies `GALI_SITE_KEY` to every site-owned table that has a `site_key` column. It does not run migrations or write to the shared business tables. The Play Online Khaiwal site remains the default in its own application; do not change that application's default site key to deploy this frontend.

## Frontend routes

- `/` — dynamic homepage, managed homepage sections, current/previous result summary, FAQ and monthly chart.
- `/chart` — active market chart directory.
- `/result/{slug}` — active market monthly result chart.
- `/{slug}` — site-scoped, active CMS page.
- `POST /get_result` — JSON month chart data for the selected active market.

## Theme

The exported theme's Tailwind utility stylesheet, site theme CSS and animation stylesheet are served from `public/frontend/css`. The Blade layout reconstructs the page structure instead of serving Next.js server-rendered markup or its React runtime. Google Fonts are loaded for Outfit and Noto Sans Devanagari.

## Validation

Run `composer install`, configure the test database, then run `php artisan test`. Before deployment, ensure the shared database is migrated to the current `final-game-apk` main schema (including site ownership and page SEO/navigation columns). Verify that Gali Disawar games, results, homepage sections, pages, FAQs and settings are tagged with `site_key=galidisawar`. Do not run the Gali Disawar frontend's default migrations against the shared business database.
