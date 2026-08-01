-- ============================================================================
-- Remove ALL FAQ Sections from Website Page Tables
-- Database: lead_crm_server
-- Project: unitedcourier
-- Purpose: Delete every FAQ-related row (faq, faq_header, faq_item, faq_sidebar,
--          faq_contact_sidebar) from all website page content tables.
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
-- 1. about_page_content
--    Column: section_type
--    FAQ rows: faq_header (ID 26) + faq items (IDs 27-37)
-- ----------------------------------------------------------------------------
DELETE FROM `about_page_content`
WHERE `section_type` IN ('faq', 'faq_header', 'faq_item', 'faq_sidebar', 'faq_contact_sidebar');

-- ----------------------------------------------------------------------------
-- 2. ebook_page
--    Column: section
--    FAQ rows: IDs 6-17 (faq_header + faq_item_1..11)
-- ----------------------------------------------------------------------------
DELETE FROM `ebook_page`
WHERE `section` IN ('faq', 'faq_header', 'faq_item', 'faq_sidebar', 'faq_contact_sidebar');

-- ----------------------------------------------------------------------------
-- 3. ecommerce_logistics_solutions_page
--    Column: section
--    FAQ rows: IDs 171-181 (faq_header + faq_1..11)
-- ----------------------------------------------------------------------------
DELETE FROM `ecommerce_logistics_solutions_page`
WHERE `section` IN ('faq', 'faq_header', 'faq_item', 'faq_sidebar', 'faq_contact_sidebar');

-- ----------------------------------------------------------------------------
-- 4. express_air_freight_solutions_page
--    Column: section
--    FAQ rows: IDs 18-29 (faq_header + faq_1..11)
-- ----------------------------------------------------------------------------
DELETE FROM `express_air_freight_solutions_page`
WHERE `section` IN ('faq', 'faq_header', 'faq_item', 'faq_sidebar', 'faq_contact_sidebar');

-- ----------------------------------------------------------------------------
-- 5. home_page
--    Column: section
--    FAQ rows: IDs 265-286 (question/answer pairs) + ID 301 (side_image)
-- ----------------------------------------------------------------------------
DELETE FROM `home_page`
WHERE `section` IN ('faq', 'faq_header', 'faq_item', 'faq_sidebar', 'faq_contact_sidebar');

-- ----------------------------------------------------------------------------
-- 6. partnership_page
--    Column: section
--    FAQ rows: ID 23 (faq/faq_content) + IDs 24-34 (faq_item/faq_1..11)
-- ----------------------------------------------------------------------------
DELETE FROM `partnership_page`
WHERE `section` IN ('faq', 'faq_header', 'faq_item', 'faq_sidebar', 'faq_contact_sidebar');

-- ----------------------------------------------------------------------------
-- 7. track_order_page
--    Column: section
--    FAQ rows: ID 10 (faq_header) + IDs 11-21 (faq_item_1..11)
-- ----------------------------------------------------------------------------
DELETE FROM `track_order_page`
WHERE `section` IN ('faq', 'faq_header', 'faq_item', 'faq_sidebar', 'faq_contact_sidebar');

-- ----------------------------------------------------------------------------
-- 8. volumetric_calculator_page
--    Column: section
--    FAQ rows: faq_sidebar (IDs 19-21, 45, 63) + faq (IDs 22-32, 46-48, 64-67)
-- ----------------------------------------------------------------------------
DELETE FROM `volumetric_calculator_page`
WHERE `section` IN ('faq', 'faq_header', 'faq_item', 'faq_sidebar', 'faq_contact_sidebar');

-- ----------------------------------------------------------------------------
-- 9. warehousing_solutions_page
--    Column: section
--    FAQ rows: IDs 23-33 (faq_1..11)
-- ----------------------------------------------------------------------------
DELETE FROM `warehousing_solutions_page`
WHERE `section` IN ('faq', 'faq_header', 'faq_item', 'faq_sidebar', 'faq_contact_sidebar');

-- ============================================================================
-- NOTE: The standalone `faq` table is NOT touched here. It is the unified FAQ
--       table used across pages (with a `page` column). If you also want to
--       empty/truncate that table, uncomment the line below:
-- TRUNCATE TABLE `faq`;
-- ============================================================================

-- ============================================================================
-- Verify FAQ rows are gone (optional, run manually if needed):
--   SELECT section_type, COUNT(*) FROM about_page_content
--     WHERE section_type LIKE 'faq%' GROUP BY section_type;            -- expect 0 rows
--   SELECT section, COUNT(*) FROM ebook_page
--     WHERE section LIKE 'faq%' GROUP BY section;                      -- expect 0 rows
--   SELECT section, COUNT(*) FROM home_page
--     WHERE section LIKE 'faq%' GROUP BY section;                      -- expect 0 rows
-- ============================================================================

COMMIT;
-- ROLLBACK;  -- Uncomment (and comment COMMIT above) to undo everything.

-- ============================================================================
-- SUMMARY
-- ----------------------------------------------------------------------------
-- Table                              | FAQ column   | Sections removed
-- -----------------------------------+--------------+--------------------------------
-- about_page_content                 | section_type | faq, faq_header
-- ebook_page                         | section      | faq
-- ecommerce_logistics_solutions_page | section      | faq
-- express_air_freight_solutions_page | section      | faq
-- home_page                          | section      | faq
-- partnership_page                   | section      | faq, faq_item
-- track_order_page                   | section      | faq
-- volumetric_calculator_page         | section      | faq, faq_sidebar
-- warehousing_solutions_page         | section      | faq
-- -----------------------------------+--------------+--------------------------------
-- Standalone `faq` table is NOT modified (see NOTE above to truncate it).
-- ============================================================================
