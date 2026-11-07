-- Remove only demo rows created by the Gali Disawar preview script.
SET @site_key := 'galidisawar';
DELETE FROM results WHERE site_key=@site_key AND game_id IN (SELECT id FROM games WHERE site_key=@site_key AND slug IN ('demo-gali','demo-disawar','demo-faridabad','demo-ghaziabad','demo-delhi-bazar','demo-shri-ganesh'));
DELETE FROM games WHERE site_key=@site_key AND slug IN ('demo-gali','demo-disawar','demo-faridabad','demo-ghaziabad','demo-delhi-bazar','demo-shri-ganesh');
DELETE FROM home_pages WHERE site_key=@site_key AND title LIKE '[DEMO]%';
DELETE FROM faqs WHERE site_key=@site_key AND question LIKE '[DEMO]%';
DELETE FROM pages WHERE site_key=@site_key AND content LIKE '%GALI_DEMO_SEED%';
DELETE FROM settings WHERE site_key=@site_key AND (
    (option='site_logo' AND value='gali-demo-logo.svg') OR
    (option='site_favicon' AND value='gali-demo-favicon.svg') OR
    (option='sticky_social_enabled' AND value='0') OR
    (option='robots_default' AND value='noindex,follow') OR
    (option='header_cta_label' AND value='Open Chart') OR
    (option='header_cta_url' AND value='/chart')
);
-- Remove public/logos/gali-demo-logo.svg and public/logos/gali-demo-favicon.svg manually if no longer needed.
