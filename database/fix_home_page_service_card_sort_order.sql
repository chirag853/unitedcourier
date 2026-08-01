-- ============================================================================
-- FIX: home_page service_card + process sort_order normalization
-- ============================================================================
-- PROBLEM:
--   The "service_card" section rows have inconsistent sort_order values
--   (1, 2, 3 for images; 30-50 for label/title/desc/etc.). The controller
--   (WebsiteController@index) groups service_card rows by sort_order to build
--   3 cards ($serviceCard1/2/3). Because each field has a DIFFERENT sort_order,
--   groupBy('sort_order') produces ~20 single-field chunks instead of 3 cards,
--   so $serviceCard2/3 end up empty and the homepage shows fallback text
--   ("Built for Sellers. Designed for scale") instead of backend content.
--
-- FIX:
--   Normalize sort_order so all fields of ONE card share the same value:
--     Card 1 (Air Express)            -> sort_order = 1
--     Card 2 (E-commerce Logistics)   -> sort_order = 2
--     Card 3 (Warehousing)            -> sort_order = 3
--
-- SAFETY: Wrapped in a transaction. Review, then COMMIT.
-- ============================================================================

START TRANSACTION;

-- Card 1: Air Express Services (image id=293, content ids 332-338)
UPDATE `home_page` SET `sort_order` = 1
 WHERE `section` = 'service_card'
   AND `id` IN (293, 332, 333, 334, 335, 336, 337, 338);

-- Card 2: E-commerce Logistics Solutions (image id=294, content ids 339-345)
UPDATE `home_page` SET `sort_order` = 2
 WHERE `section` = 'service_card'
   AND `id` IN (294, 339, 340, 341, 342, 343, 344, 345);

-- Card 3: Warehousing Solutions (image id=295, content ids 346-352)
UPDATE `home_page` SET `sort_order` = 3
 WHERE `section` = 'service_card'
   AND `id` IN (295, 346, 347, 348, 349, 350, 351, 352);

-- ----------------------------------------------------------------------------
-- PROCESS SECTION (4 steps). Controller groups by sort_order; each step's
-- step_title + step_desc must share one sort_order. Header = 0, steps = 1..4.
-- ----------------------------------------------------------------------------
-- Header (section_tag + heading)
UPDATE `home_page` SET `sort_order` = 0
 WHERE `section` = 'process' AND `id` IN (322, 323);
-- Step 1
UPDATE `home_page` SET `sort_order` = 1
 WHERE `section` = 'process' AND `id` IN (324, 325);
-- Step 2
UPDATE `home_page` SET `sort_order` = 2
 WHERE `section` = 'process' AND `id` IN (326, 327);
-- Step 3
UPDATE `home_page` SET `sort_order` = 3
 WHERE `section` = 'process' AND `id` IN (328, 329);
-- Step 4
UPDATE `home_page` SET `sort_order` = 4
 WHERE `section` = 'process' AND `id` IN (330, 331);

-- Verification: service_card should show 3 sort groups (1,2,3), each 8 fields.
SELECT 'service_card' AS section, `sort_order`, COUNT(*) AS field_count,
       GROUP_CONCAT(`field_name` ORDER BY `field_name`) AS fields
  FROM `home_page` WHERE `section` = 'service_card'
 GROUP BY `sort_order` ORDER BY `sort_order`;

-- Verification: process should show 5 sort groups (0,1,2,3,4).
SELECT 'process' AS section, `sort_order`, COUNT(*) AS field_count,
       GROUP_CONCAT(`field_name` ORDER BY `field_name`) AS fields
  FROM `home_page` WHERE `section` = 'process'
 GROUP BY `sort_order` ORDER BY `sort_order`;

-- NOTE: This fix was already applied to the live database via Eloquent on
-- 2026-07-28. This script is kept for documentation/re-run safety. If you
-- execute it fresh, the UPDATEs are idempotent (setting sort_order to the
-- same value it already has), so COMMIT is safe.
COMMIT;
