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
DELETE FROM settings WHERE site_key=@site_key AND (
    (option='title' AND value='Gali Disawar') OR
    (option='site_tagline' AND value='LIVE RESULT RECORDS • MARKET DIRECTORY • MONTHLY CHARTS') OR
    (option='site_description' AND value='Browse published Gali Disawar market records, daily result summaries and historical monthly charts.') OR
    (option='meta_title' AND value='Gali Disawar | Live Result Records & Monthly Charts') OR
    (option='meta_description' AND value='Browse Gali Disawar market result records, today and yesterday summaries, and monthly historical charts.') OR
    (option='meta_keywords' AND value='Gali Disawar, Gali Result, Disawar Result, Monthly Chart, Historical Records') OR
    (option='canonical_url' AND value='https://galidisawar.com') OR
    (option='homepage_heading_line1' AND value='Every market.') OR
    (option='homepage_heading_line2' AND value='Every record.') OR
    (option='homepage_heading_line3' AND value='One clear board.') OR
    (option='homepage_intro' AND value='Browse published market results, compare recent records, and open date-wise monthly charts.') OR
    (option='homepage_note_text' AND value='DEMO PREVIEW — Sample records are shown for layout testing only.') OR
    (option='announcement_text' AND value='Demo preview is active. Sample values are not live results.') OR
    (option='web_marquee' AND value='Gali Disawar demo preview • Sample market records • Monthly charts') OR
    (option='homepage_primary_cta_label' AND value='Browse Market Charts') OR
    (option='homepage_primary_cta_url' AND value='/chart') OR
    (option='homepage_secondary_cta_label' AND value='View Live Results') OR
    (option='homepage_secondary_cta_url' AND value='/#markets') OR
    (option='homepage_guide_heading' AND value='How to Read the Market Result Chart') OR
    (option='homepage_guide_content' AND value LIKE '%This preview uses sample data only%') OR
    (option='homepage_seo_heading' AND value='About Gali Disawar Result Records') OR
    (option='homepage_seo_content' AND value LIKE '%demonstrates the full theme layout%') OR
    (option='app_section_heading' AND value='Stay Updated') OR
    (option='app_section_description' AND value='Open the result charts or follow the official channels configured by the site administrator.') OR
    (option='app_download_label' AND value='Download App Now') OR
    (option='footer_description' AND value='Gali Disawar organizes published market records and monthly charts for informational reference.') OR
    (option='copyright_text' AND value LIKE '© % Gali Disawar. Demo preview.') OR
    (option='disclaimer_content' AND value LIKE 'Demo content is for layout testing only%')
);

-- Remove public/logos/gali-demo-logo.svg and public/logos/gali-demo-favicon.svg manually if no longer needed.
