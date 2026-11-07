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

## Theme completeness and dynamic admin content

The frontend reads the existing site-scoped settings for title, tagline, logo, favicon, Open Graph image, announcement ticker, homepage headings/copy, header and homepage CTA buttons, app download, contact details, social links, SEO text, footer and disclaimer. The original header, ticker, result board, quick links, market cards, managed section positions, app/contact block, monthly charts, historical archive, guide, SEO copy, FAQ and legal footer are rendered as Blade components/views.

Homepage sections use the existing `home_pages` records. The companion Admin change in `final-game-apk` PR adds section image, alt text, CTA label/URL/style, layout and display-order controls. Apply that migration before using those new editor controls. Existing sections continue to render without those optional fields.

The original theme export has no inline homepage `<img>` elements; the theme's visual identity is primarily CSS. Dynamic logo/favicon/OG images come from the existing Settings upload fields, while section images come from Home Page Sections. The guessing page uses the existing theme typography and CSS and does not invent forum posts.

## Demo preview data

The Admin may be empty for the new site. To populate a disposable preview with clearly marked sample rows, first apply the shared `final-game-apk` multi-site migrations and the companion homepage presentation migration, then configure the Gali Disawar DB connection and run:

```bash
php artisan gali:demo-seed
```

The command inserts only rows scoped to `site_key=galidisawar`, uses `demo-*` market slugs and `[DEMO]` homepage/FAQ labels, never updates existing market/result rows, and refuses to run in `APP_ENV=production` unless explicitly forced. It adds sample records for the last few days of the current month so the homepage board and charts are populated.

If you prefer phpMyAdmin, import `database/demo/gali-disawar-demo.sql`. To remove only the demo markets, results, sections, FAQs and demo legal pages, use `database/demo/gali-disawar-demo-cleanup.sql`. The cleanup script deliberately preserves settings because some may have existed before the demo import; review and replace/clear demo branding and preview text in Admin Settings after cleanup. The demo logo and favicon are in `public/logos/`.

Do not use demo rows as live result data. Replace demo copy, links, sample sections and branding before launch.

## Theme

The exported theme's Tailwind utility stylesheet, site theme CSS and animation stylesheet are served from `public/frontend/css`. The Blade layout reconstructs the page structure instead of serving Next.js server-rendered markup or its React runtime. Google Fonts are loaded for Outfit and Noto Sans Devanagari.

## Validation

Run `composer install`, configure the test database, then run `php artisan test`. Before deployment, ensure the shared database is migrated to the current `final-game-apk` main schema (including site ownership and page SEO/navigation columns). Verify that Gali Disawar games, results, homepage sections, pages, FAQs and settings are tagged with `site_key=galidisawar`. Do not run the Gali Disawar frontend's default migrations against the shared business database.
