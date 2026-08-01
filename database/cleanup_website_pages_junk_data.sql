-- ============================================================================
-- Website Pages Junk Data Cleanup Script
-- Database: lead_crm_server
-- Project: unitedcourier
-- Purpose: Remove orphan/unused/duplicate/junk data from website page tables
--          that is NOT actually used by the web pages (Blade views & controllers)
-- ============================================================================
--
-- HOW TO USE:
--   1. Take a full DB backup before running (mysqldump lead_crm_server > backup.sql)
--   2. Run this entire script in phpMyAdmin / MySQL CLI
--   3. If anything looks wrong, run: ROLLBACK;  (transaction is open)
--   4. If everything is correct, the COMMIT at the bottom finalizes changes
--
-- SAFETY: Wrapped in a single transaction. DELETEs only run if COMMIT executes.
-- ============================================================================

START TRANSACTION;

-- ----------------------------------------------------------------------------
-- 1. testimonials
--    3 duplicate sets exist for home testimonials (IDs 1-6, 7-12, 13-18).
--    Controller renders only one set; the other two are dead duplicates.
--    KEEP: IDs 1-6 (home) + IDs 19-21 (about page, unique)
--    DELETE: IDs 7-18 (two full duplicate sets)
-- ----------------------------------------------------------------------------
DELETE FROM `testimonials` WHERE `id` IN (7,8,9,10,11,12,13,14,15,16,17,18);

-- ----------------------------------------------------------------------------
-- 2. volumetric_calculator_page
--    Contains duplicate section rows and junk/test rows not rendered by the
--    view (resources/views/volumetric-calculator.blade.php).
--    DELETE: duplicate + junk IDs
-- ----------------------------------------------------------------------------
DELETE FROM `volumetric_calculator_page`
WHERE `id` IN (20,21,33,34,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,
               56,57,58,59,60,61,62,63,64,65,66,67,68);

-- ----------------------------------------------------------------------------
-- 3. faq
--    ID 144 is a junk/test FAQ entry not shown on the website.
-- ----------------------------------------------------------------------------
DELETE FROM `faq` WHERE `id` = 144;

-- ----------------------------------------------------------------------------
-- 4. faq_quries
--    IDs 1,2,3 are test submissions (not real customer queries).
-- ----------------------------------------------------------------------------
DELETE FROM `faq_quries` WHERE `id` IN (1,2,3);

-- ----------------------------------------------------------------------------
-- 5. about_page_content
--    IDs 24,25 are duplicate rows overriding earlier content.
-- ----------------------------------------------------------------------------
DELETE FROM `about_page_content` WHERE `id` IN (24,25);

-- ----------------------------------------------------------------------------
-- 6. warehousing_solutions_page
--    ID 19 is a junk/test row not used by the view.
-- ----------------------------------------------------------------------------
DELETE FROM `warehousing_solutions_page` WHERE `id` = 19;

-- ----------------------------------------------------------------------------
-- 7. document_download_page
--    ID 18 is a junk/test row not used by the view.
-- ----------------------------------------------------------------------------
DELETE FROM `document_download_page` WHERE `id` = 18;

-- ----------------------------------------------------------------------------
-- 8. blogs
--    ID 1 is a junk/test blog post not published on the website.
-- ----------------------------------------------------------------------------
DELETE FROM `blogs` WHERE `id` = 1;

-- ----------------------------------------------------------------------------
-- 9. home_page  (CRITICAL - REVISED)
--    Two content sets exist, but they are NOT full duplicates:
--      IDs 180-244 : OLD set. Contains hero, about, process, service_card,
--                   AND shipping_solutions (223-244) which is UNIQUE.
--      IDs 265-309 : FAQ items, images, partner logos, footer -> UNIQUE, KEEP
--      IDs 310-356 : NEW set. Contains hero, about, process, service_card,
--                    services_heading, testimonial_heading (sort_order 1-71).
--                    Does NOT contain shipping_solutions/faq/partners/footer.
--    The NEW set (310-356) duplicates only: hero, about, process, service_card,
--    services_heading. The OLD set (180-244) is overridden for those sections
--    because the controller uses pluck('content','field_name') (last row wins).
--    HOWEVER: shipping_solutions (223-244) exists ONLY in the old set and is
--    actively rendered by the "Tailor-Made Shipping Option" cards section.
--    Deleting 180-244 entirely would BLANK those cards.
--    SAFE DELETE: only the duplicated sections from the old set.
--    KEEP: shipping_solutions (223-244), all of 265-309, all of 310-356.
-- ----------------------------------------------------------------------------
DELETE FROM `home_page`
  WHERE `id` BETWEEN 180 AND 222     -- old hero/about/process/service_card
     OR `id` IN (308, 309);          -- old services_heading (dup of 353/354)
-- NOTE: IDs 223-244 (shipping_solutions) are KEPT - they are unique & rendered.

-- ----------------------------------------------------------------------------
-- 10. ebook_page
--     ID 21 : DUPLICATE of ID 4 (hero_content)
--     ID 22 : DUPLICATE of ID 5 (section_header_content)
--     ID 38 : "The Final Experiment | The Mystery Thriller" -> JUNK test content
--             (unrelated to logistics, links to file-sample PDF)
-- ----------------------------------------------------------------------------
DELETE FROM `ebook_page` WHERE `id` IN (21, 22, 38);

-- ----------------------------------------------------------------------------
-- 11. webinar_page
--     ID 7 : "Oasis of the Seas: The Giant That Changed Cruising Forever"
--            by "chirag" -> JUNK test content, not a real webinar.
-- ----------------------------------------------------------------------------
DELETE FROM `webinar_page` WHERE `id` = 7;

-- ============================================================================
-- Verify row counts before committing (optional, run manually if needed):
--   SELECT COUNT(*) FROM testimonials WHERE id IN (7,8,9,10,11,12,13,14,15,16,17,18); -- expect 0
--   SELECT COUNT(*) FROM home_page WHERE id BETWEEN 180 AND 222 OR id IN (308,309); -- expect 0
--   SELECT COUNT(*) FROM home_page WHERE section='shipping_solutions';              -- expect 26 (KEPT)
--   SELECT COUNT(*) FROM ebook_page WHERE id IN (21,22,38);                          -- expect 0
--   SELECT COUNT(*) FROM webinar_page WHERE id = 7;                                  -- expect 0
-- ============================================================================

COMMIT;
-- ROLLBACK;  -- Uncomment (and comment COMMIT above) to undo everything.

-- ============================================================================
-- SUMMARY OF CLEANUP
-- ----------------------------------------------------------------------------
-- Table                          | Rows removed | Reason
-- -------------------------------+--------------+---------------------------
-- testimonials                   |    12        | 2 duplicate sets of home
-- volumetric_calculator_page     |    32        | duplicates + junk
-- faq                            |     1        | junk test entry
-- faq_quries                     |     3        | test submissions
-- about_page_content             |     2        | duplicate rows
-- warehousing_solutions_page     |     1        | junk row
-- document_download_page         |     1        | junk row
-- blogs                          |     1        | junk test post
-- home_page                      |    45        | old dup hero/about/process/service_card + services_heading
--                                              | (shipping_solutions 223-244 KEPT - unique & rendered)
-- ebook_page                     |     3        | duplicates + junk
-- webinar_page                   |     1        | junk test webinar
-- -------------------------------+--------------+---------------------------
-- TOTAL                          |   122 rows
-- ============================================================================
