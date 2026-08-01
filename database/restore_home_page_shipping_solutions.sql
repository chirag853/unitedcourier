-- ============================================================================
-- RESTORE: home_page shipping_solutions rows
-- ============================================================================
-- PURPOSE:
--   The earlier cleanup script (cleanup_website_pages_junk_data.sql) deleted
--   home_page rows with id BETWEEN 180 AND 244. That range included the
--   UNIQUE shipping_solutions card content (IDs 223-244) which is actively
--   rendered by the "Tailor-Made Shipping Option" cards on the homepage
--   (index.blade.php -> $shippingCards). Deleting them blanked the cards.
--
--   This script re-inserts ONLY the shipping_solutions rows (223-244) plus
--   the card_image rows (296-299) so the homepage cards render again.
--
--   NOTE: card_image rows (296-299) were NOT deleted by the old script
--   (they are > 244), but they are included here commented-out in case you
--   also lost them. Uncomment if needed.
--
-- SAFETY: Wrapped in a transaction. Review, then COMMIT.
-- ============================================================================

START TRANSACTION;

-- Only insert if these IDs are missing (avoids duplicate-key errors if
-- the rows still exist). We use INSERT IGNORE so re-running is safe.
INSERT IGNORE INTO `home_page` (`id`, `section`, `field_name`, `content`, `sort_order`, `updated_at`) VALUES
-- Section header (sort_order 0)
(223, 'shipping_solutions', 'heading', 'Shipping Solutions Designed Around You!', 0, '2026-05-20 09:43:00'),
(224, 'shipping_solutions', 'description', 'No two businesses ship the same way. That\'s why United Worldwide Couriers offers flexible logistics solutions built around your shipment type, delivery timeline, budget, and business goals. Whether you need B2B Export Support, Dropshipping Solutions, Marketplace shipping, or personal deliveries for friends and family, we help you choose the right service with clarity, reliability, and complete support.', 0, '2026-05-20 09:43:35'),

-- Card 1: B2B Shipping Made Simple (sort_order 1)
(225, 'shipping_solutions', 'card_title', 'B2B Shipping Made Simple', 1, '2026-05-26 07:42:49'),
(226, 'shipping_solutions', 'card_desc', 'Move commercial shipments with confidence. We support exporters, manufacturers, distributors, and growing businesses with secure handling, flexible delivery options, documentation support, and competitive pricing for bulk volumes.', 1, '2026-05-20 09:51:55'),
(227, 'shipping_solutions', 'card_point1', 'Fragile Goods: <span style=\"font-weight: 400;\">Secure handling for sensitive and high-volume shipments<span>', 1, '2026-05-26 11:45:11'),
(228, 'shipping_solutions', 'card_point2', 'Bulk Shipments: <span style=\"font-weight: 400;\">Cost-effective solutions and customs clearance support.<span>', 1, '2026-05-26 11:46:55'),
(229, 'shipping_solutions', 'card_cta', 'Start Shipping', 1, '2026-05-08 09:07:23'),

-- Card 2: Marketplace Shipping (sort_order 3 in DB, but grouped as 2nd card)
(230, 'shipping_solutions', 'card_title', 'Marketplace Shipping', 3, '2026-05-19 12:31:21'),
(231, 'shipping_solutions', 'card_desc', 'Connect your online store with our shipping platform and manage orders from leading marketplaces like Amazon, eBay, Etsy, Walmart, and more, with faster processing, real-time tracking, and reliable delivery support.', 3, '2026-05-26 23:47:03'),
(232, 'shipping_solutions', 'card_point1', 'End-to-End Support: <span style=\"font-weight:400\">Manage multiple tasks with one easy-to-use dashboard.</span>', 2, '2026-05-26 11:57:09'),
(233, 'shipping_solutions', 'card_point2', 'Automated Order flow: <span style=\"font-weight:400\">Manage. Process. Dispatch.</span>', 2, '2026-05-26 11:57:59'),
(234, 'shipping_solutions', 'card_cta', 'Start Shipping', 2, '2026-05-08 09:07:23'),

-- Card 3: Dropshipping Solutions (sort_order 2 in DB)
(235, 'shipping_solutions', 'card_title', 'Dropshipping Solutions', 2, '2026-05-19 12:31:42'),
(236, 'shipping_solutions', 'card_desc', 'Dropshipping Solutions Launch and Scale your Online business with reliable door-to-door Shipping. Whether you sell through Shopify, your own website, or a dropshiping store, we help you ship products directly to customers with speed, tracking and complete reliability.', 2, '2026-05-26 23:51:36'),
(237, 'shipping_solutions', 'card_point1', 'Real-Time Tracking: <span style=\"font-weight:400\">Complete visibility for every shipment.<span>', 3, '2026-05-26 11:56:08'),
(238, 'shipping_solutions', 'card_point2', 'Seller-Friendly Shipping:  <span style=\"font-weight:400\">Built for marketplace sellers handling small packages daily and high volume.</span>', 3, '2026-05-26 11:53:20'),
(239, 'shipping_solutions', 'card_cta', 'Start Shipping', 3, '2026-05-08 09:07:23'),

-- Card 4: Overseas Friends & Family (sort_order 4)
(240, 'shipping_solutions', 'card_title', 'Overseas Friends & Family', 4, '2026-05-08 09:07:23'),
(241, 'shipping_solutions', 'card_desc', 'Send personal packages to your loved ones worldwide with safe handling, timely delivery, and clear updates at every step.', 4, '2026-05-08 09:07:23'),
(242, 'shipping_solutions', 'card_point1', 'International Delivery: <span style=\"font-weight:400\">Reliable shipping for gifts, documents, clothes, and personal items.</span>', 4, '2026-05-26 11:50:13'),
(243, 'shipping_solutions', 'card_point2', 'Live Updates: <span style=\"font-weight:400\">Stay informed from pickup to delivery.</span>', 4, '2026-05-26 11:50:34'),
(244, 'shipping_solutions', 'card_cta', 'Start Shipping', 4, '2026-05-26 07:55:43');

-- Card images (IDs 296-299). These were NOT deleted by the old script
-- (id > 244), but uncomment below if you also need to restore them.
--
-- INSERT IGNORE INTO `home_page` (`id`, `section`, `field_name`, `content`, `sort_order`, `updated_at`) VALUES
-- (296, 'shipping_solutions', 'card_image', 'website_images/marketplace.webp', 1, '2026-05-19 10:51:09'),
-- (297, 'shipping_solutions', 'card_image', '/website_images/dropshipping.webp', 2, '2026-05-19 11:04:52'),
-- (298, 'shipping_solutions', 'card_image', '/website_images/b2b.webp', 3, '2026-05-19 11:04:52'),
-- (299, 'shipping_solutions', 'card_image', '/website_images/b2b.webp', 4, '2026-05-19 11:04:52');

-- ============================================================================
-- Verify before committing:
--   SELECT * FROM home_page WHERE section = 'shipping_solutions' ORDER BY sort_order, id;
--   Expect: 22 rows (2 header + 4 cards x 5 fields each)
-- ============================================================================

COMMIT;
-- ROLLBACK;  -- Uncomment (and comment COMMIT above) to undo.
