-- Gali Disawar theme preview data
-- Run only after the shared multi-site migrations and the homepage presentation migration.
-- This script inserts rows for site_key=galidisawar only and never overwrites existing records.
SET @site_key := 'galidisawar';

-- 1) Demo markets (unique demo-* slugs; no real market row is changed).
INSERT IGNORE INTO games
(site_key,name,slug,result_time,play_start,play_end,last_result,status,serial,reward,created_at,updated_at)
VALUES
(@site_key,'Gali Demo','demo-gali','23:00:00','10:00:00','22:55:00',NULL,'active',901,1,NOW(),NOW()),
(@site_key,'Disawar Demo','demo-disawar','05:00:00','00:00:00','04:55:00',NULL,'active',902,1,NOW(),NOW()),
(@site_key,'Faridabad Demo','demo-faridabad','18:00:00','10:00:00','17:55:00',NULL,'active',903,1,NOW(),NOW()),
(@site_key,'Ghaziabad Demo','demo-ghaziabad','21:30:00','10:00:00','21:25:00',NULL,'active',904,1,NOW(),NOW()),
(@site_key,'Delhi Bazar Demo','demo-delhi-bazar','15:00:00','10:00:00','14:55:00',NULL,'active',905,1,NOW(),NOW()),
(@site_key,'Shri Ganesh Demo','demo-shri-ganesh','16:30:00','10:00:00','16:25:00',NULL,'active',906,1,NOW(),NOW());

-- 2) Deterministic sample Jodi records for up to the last 7 calendar days of this month.
INSERT IGNORE INTO results
(site_key,game_id,game_date,type,number,created_at,updated_at)
SELECT @site_key,g.id,DATE_SUB(CURDATE(), INTERVAL d.day_offset DAY),'jodi',
       LPAD(MOD(g.serial * 7 + d.day_offset * 13,100),2,'0'),NOW(),NOW()
FROM games g
JOIN (
    SELECT 0 AS day_offset UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL
    SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
) d
WHERE g.site_key=@site_key
  AND g.slug IN ('demo-gali','demo-disawar','demo-faridabad','demo-ghaziabad','demo-delhi-bazar','demo-shri-ganesh')
  AND DATE_SUB(CURDATE(), INTERVAL d.day_offset DAY) >= DATE_FORMAT(CURDATE(),'%Y-%m-01');

-- 3) Fill only blank/missing site settings. Existing configured values are preserved.
INSERT INTO settings (site_key,option,value,created_at,updated_at) VALUES
(@site_key,'title','Gali Disawar',NOW(),NOW()),
(@site_key,'site_tagline','LIVE RESULT RECORDS • MARKET DIRECTORY • MONTHLY CHARTS',NOW(),NOW()),
(@site_key,'site_description','Browse published Gali Disawar market records, daily result summaries and historical monthly charts.',NOW(),NOW()),
(@site_key,'meta_title','Gali Disawar | Live Result Records & Monthly Charts',NOW(),NOW()),
(@site_key,'meta_description','Browse Gali Disawar market result records, today and yesterday summaries, and monthly historical charts.',NOW(),NOW()),
(@site_key,'meta_keywords','Gali Disawar, Gali Result, Disawar Result, Monthly Chart, Historical Records',NOW(),NOW()),
(@site_key,'canonical_url','https://galidisawar.com',NOW(),NOW()),
(@site_key,'site_logo','gali-demo-logo.svg',NOW(),NOW()),
(@site_key,'site_favicon','gali-demo-favicon.svg',NOW(),NOW()),
(@site_key,'homepage_heading_line1','Every market.',NOW(),NOW()),
(@site_key,'homepage_heading_line2','Every record.',NOW(),NOW()),
(@site_key,'homepage_heading_line3','One clear board.',NOW(),NOW()),
(@site_key,'homepage_intro','Browse published market results, compare recent records, and open date-wise monthly charts.',NOW(),NOW()),
(@site_key,'homepage_note_text','DEMO PREVIEW — Sample records are shown for layout testing only.',NOW(),NOW()),
(@site_key,'announcement_text','Demo preview is active. Sample values are not live results.',NOW(),NOW()),
(@site_key,'web_marquee','Gali Disawar demo preview • Sample market records • Monthly charts',NOW(),NOW()),
(@site_key,'header_cta_label','Open Chart',NOW(),NOW()),
(@site_key,'header_cta_url','/chart',NOW(),NOW()),
(@site_key,'homepage_primary_cta_label','Browse Market Charts',NOW(),NOW()),
(@site_key,'homepage_primary_cta_url','/chart',NOW(),NOW()),
(@site_key,'homepage_secondary_cta_label','View Live Results',NOW(),NOW()),
(@site_key,'homepage_secondary_cta_url','/#markets',NOW(),NOW()),
(@site_key,'app_section_heading','Stay Updated',NOW(),NOW()),
(@site_key,'app_section_description','Open the result charts or follow the official channels configured by the site administrator.',NOW(),NOW()),
(@site_key,'app_download_label','Download App Now',NOW(),NOW()),
(@site_key,'homepage_guide_heading','How to Read the Market Result Chart',NOW(),NOW()),
(@site_key,'homepage_guide_content','<p>Select a market, then choose an available year and month. A dash means that no published value is currently stored for that date. This preview uses sample data only.</p>',NOW(),NOW()),
(@site_key,'homepage_seo_heading','About Gali Disawar Result Records',NOW(),NOW()),
(@site_key,'homepage_seo_content','<p>This demo homepage demonstrates the full theme layout, market directory, monthly result tables, historical archive, managed content sections and FAQ blocks. Replace sample content with verified site information before publishing.</p>',NOW(),NOW()),
(@site_key,'footer_description','Gali Disawar organizes published market records and monthly charts for informational reference.',NOW(),NOW()),
(@site_key,'disclaimer_content','Demo content is for layout testing only. Sample values are not live or verified results. Historical records are not predictions or guarantees. Follow applicable local laws.',NOW(),NOW()),
(@site_key,'copyright_text',CONCAT('© ',YEAR(CURDATE()),' Gali Disawar. Demo preview.'),NOW(),NOW()),
(@site_key,'sticky_social_enabled','0',NOW(),NOW()),
(@site_key,'robots_default','noindex,follow',NOW(),NOW())
ON DUPLICATE KEY UPDATE
value=IF(value IS NULL OR value='',VALUES(value),value),updated_at=IF(value IS NULL OR value='',NOW(),updated_at);

-- 4) Homepage sections: each configured position is populated with a safe sample card.
INSERT INTO home_pages
(site_key,title,content,status,location,whatsapp,phone,telegram,background,image,image_alt,button_label,button_url,button_style,layout,display_order,created_at,updated_at)
SELECT @site_key,'[DEMO] Featured information banner','<p>Sample banner section with a dynamic image and call-to-action button. Replace it from Admin → Home Page Sections.</p>','active','first_place',NULL,NULL,NULL,'#ffffff','https://placehold.co/1200x420/111827/ffffff?text=Gali+Disawar+Theme+Preview','Demo theme banner','Browse Charts','/chart','primary','banner',1,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM home_pages WHERE site_key=@site_key AND title='[DEMO] Featured information banner');
INSERT INTO home_pages
(site_key,title,content,status,location,background,image,image_alt,button_label,button_url,button_style,layout,display_order,created_at,updated_at)
SELECT @site_key,'[DEMO] Market timing guide','<p>Use this section to explain market schedules and how the result archive is organized.</p>','active','second_place','#ffffff','https://placehold.co/640x420/7c3aed/ffffff?text=Market+Guide','Market guide sample image','See Markets','/#markets','primary','image-left',2,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM home_pages WHERE site_key=@site_key AND title='[DEMO] Market timing guide');
INSERT INTO home_pages
(site_key,title,content,status,location,background,image,image_alt,button_label,button_url,button_style,layout,display_order,created_at,updated_at)
SELECT @site_key,'[DEMO] Yesterday and today records','<p>This content block demonstrates an image on the right with a separate button.</p>','active','second_place_another','#ffffff','https://placehold.co/640x420/0f766e/ffffff?text=Daily+Records','Daily records sample image','Open Daily Board','/#markets-quick-record','primary','image-right',3,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM home_pages WHERE site_key=@site_key AND title='[DEMO] Yesterday and today records');
INSERT INTO home_pages
(site_key,title,content,status,location,background,image,image_alt,button_label,button_url,button_style,layout,display_order,created_at,updated_at)
SELECT @site_key,'[DEMO] Historical result archive','<p>Promote the historical archive and explain how visitors can choose a year and month.</p>','active','third_place','#ffffff','https://placehold.co/1200x360/1e293b/ffffff?text=Historical+Archive','Historical archive sample image','View Old Records','/#records','primary','image-top',4,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM home_pages WHERE site_key=@site_key AND title='[DEMO] Historical result archive');
INSERT INTO home_pages
(site_key,title,content,status,location,background,button_label,button_url,button_style,layout,display_order,created_at,updated_at)
SELECT @site_key,'[DEMO] Full width announcement','<p>A second announcement position with its own title, copy and CTA.</p>','active','third_place_another','#ffffff','Open Chart Directory','/chart','primary','standard',5,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM home_pages WHERE site_key=@site_key AND title='[DEMO] Full width announcement');
INSERT INTO home_pages
(site_key,title,content,status,location,background,button_label,button_url,button_style,layout,display_order,created_at,updated_at)
SELECT @site_key,'[DEMO] Before footer contact block','<p>Configure the real phone, WhatsApp and Telegram details in site settings before publishing.</p>','active','before_footer','#ffffff','Contact via Admin Settings','/#khaiwal','primary','standard',6,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM home_pages WHERE site_key=@site_key AND title='[DEMO] Before footer contact block');
INSERT INTO home_pages
(site_key,title,content,status,location,background,button_label,button_url,button_style,layout,display_order,created_at,updated_at)
SELECT @site_key,'[DEMO] After footer information block','<p>This final managed section verifies the last homepage section position.</p>','active','after_footer','#ffffff','Back to Top','/#freshness','primary','standard',7,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM home_pages WHERE site_key=@site_key AND title='[DEMO] After footer information block');

-- 5) FAQs.
INSERT INTO faqs (site_key,platform,question,answer,created_at,updated_at)
SELECT @site_key,'web','[DEMO] How do I view a monthly chart?','Choose a market, then select an available year and month to view date-wise saved records.',NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM faqs WHERE site_key=@site_key AND question='[DEMO] How do I view a monthly chart?');
INSERT INTO faqs (site_key,platform,question,answer,created_at,updated_at)
SELECT @site_key,'web','[DEMO] What does a dash mean?','A dash means no published value is currently stored for that date.',NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM faqs WHERE site_key=@site_key AND question='[DEMO] What does a dash mean?');
INSERT INTO faqs (site_key,platform,question,answer,created_at,updated_at)
SELECT @site_key,'web','[DEMO] Are historical records predictions?','No. Historical records are for reference only and do not predict or guarantee future outcomes.',NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM faqs WHERE site_key=@site_key AND question='[DEMO] Are historical records predictions?');
INSERT INTO faqs (site_key,platform,question,answer,created_at,updated_at)
SELECT @site_key,'web','[DEMO] Can I browse old market records?','Yes. Open the Chart Archive and select a market, year and month.',NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM faqs WHERE site_key=@site_key AND question='[DEMO] Can I browse old market records?');

-- 6) CMS legal pages. The marker makes these exact demo rows safe to clean up.
INSERT INTO pages
(site_key,name,slug,content,status,is_editable,menu_visible,menu_order,meta_title,meta_description,meta_keywords,noindex,created_at,updated_at)
SELECT @site_key,'Disclaimer','disclaimer','<!-- GALI_DEMO_SEED --><h2>Disclaimer</h2><p>This website provides informational result records and historical charts only. Records are not predictions or guarantees. Please follow applicable local laws.</p>','active','yes',1,90,'Demo Disclaimer | Gali Disawar','Demo disclaimer page for theme preview.','Gali Disawar, Disclaimer',1,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM pages WHERE site_key=@site_key AND slug='disclaimer');
INSERT INTO pages
(site_key,name,slug,content,status,is_editable,menu_visible,menu_order,meta_title,meta_description,meta_keywords,noindex,created_at,updated_at)
SELECT @site_key,'Privacy Policy','privacy-policy','<!-- GALI_DEMO_SEED --><h2>Privacy Policy</h2><p>Replace this sample policy with your approved privacy policy before launch.</p>','active','yes',1,91,'Demo Privacy Policy | Gali Disawar','Demo privacy policy page for theme preview.','Gali Disawar, Privacy Policy',1,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM pages WHERE site_key=@site_key AND slug='privacy-policy');
INSERT INTO pages
(site_key,name,slug,content,status,is_editable,menu_visible,menu_order,meta_title,meta_description,meta_keywords,noindex,created_at,updated_at)
SELECT @site_key,'Terms & Conditions','terms-and-conditions','<!-- GALI_DEMO_SEED --><h2>Terms & Conditions</h2><p>Replace this sample page with approved terms before launch.</p>','active','yes',1,92,'Demo Terms & Conditions | Gali Disawar','Demo terms page for theme preview.','Gali Disawar, Terms',1,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM pages WHERE site_key=@site_key AND slug='terms-conditions');

-- Sample logo and favicon files are committed in public/logos in this repository.
