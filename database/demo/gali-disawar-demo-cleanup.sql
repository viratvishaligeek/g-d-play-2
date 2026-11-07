-- Remove only demo rows created by the Gali Disawar preview script.
SET @site_key := 'galidisawar';
DELETE FROM results WHERE site_key=@site_key AND game_id IN (SELECT id FROM games WHERE site_key=@site_key AND slug IN ('demo-gali','demo-disawar','demo-faridabad','demo-ghaziabad','demo-delhi-bazar','demo-shri-ganesh'));
DELETE FROM games WHERE site_key=@site_key AND slug IN ('demo-gali','demo-disawar','demo-faridabad','demo-ghaziabad','demo-delhi-bazar','demo-shri-ganesh');
DELETE FROM home_pages WHERE site_key=@site_key AND title LIKE '[DEMO]%';
DELETE FROM faqs WHERE site_key=@site_key AND question LIKE '[DEMO]%';
DELETE FROM pages WHERE site_key=@site_key AND content LIKE '%GALI_DEMO_SEED%';
-- Settings are intentionally preserved: some may have existed before the demo import.
-- Review title/SEO/CTA/logo values in Admin Settings after preview and replace or clear them manually.
-- Remove public/logos/gali-demo-logo.svg and public/logos/gali-demo-favicon.svg manually if no longer needed.
