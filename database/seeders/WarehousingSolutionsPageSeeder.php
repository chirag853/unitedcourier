<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WarehousingSolutionsPage;

class WarehousingSolutionsPageSeeder extends Seeder
{
    public function run()
    {
        // Hero Section
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'hero', 'item_key' => 'hero_main'],
            [
                'section' => 'hero',
                'item_key' => 'hero_main',
                'subtitle' => 'Secure Storage for Your Business Needs',
                'paragraphs' => 'Our state-of-the-art warehousing facilities provide secure, climate-controlled storage solutions tailored to your business requirements. With advanced inventory management systems and 24/7 monitoring, we ensure your goods are safe and accessible whenever you need them.',
                'badge_text' => 'Premium Storage',
                'button_text' => 'Get Started',
                'button_url' => '/contact-us',
                'list_items_text' => "24–72 Hr Delivery\nFully Insured\n220+ Countries",
                'extra_content' => json_encode([
                    'title' => 'Warehousing Solutions',
                    'image' => 'images/warehousing.webp',
                ]),
                'sort_order' => 1,
                'is_active' => true,
            ]
        );


        // Stats Section - 6 cards
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'stats', 'item_key' => 'stat_1'],
            [
                'section' => 'stats',
                'item_key' => 'stat_1',
                'stat_value' => '150',
                'stat_label' => 'Cities Covered',
                'stat_suffix' => '+',
                'sort_order' => 6,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'stats', 'item_key' => 'stat_2'],
            [
                'section' => 'stats',
                'item_key' => 'stat_2',
                'stat_value' => '100',
                'stat_label' => 'Daily Parcels',
                'stat_suffix' => 'K+',
                'sort_order' => 7,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'stats', 'item_key' => 'stat_3'],
            [
                'section' => 'stats',
                'item_key' => 'stat_3',
                'stat_value' => '5',
                'stat_label' => 'Delivery Riders',
                'stat_suffix' => 'K+',
                'sort_order' => 8,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'stats', 'item_key' => 'stat_4'],
            [
                'section' => 'stats',
                'item_key' => 'stat_4',
                'stat_value' => '99',
                'stat_label' => 'On-time Rate',
                'stat_suffix' => '.9%',
                'sort_order' => 9,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'stats', 'item_key' => 'stat_5'],
            [
                'section' => 'stats',
                'item_key' => 'stat_5',
                'stat_value' => '24',
                'stat_label' => 'Live Tracking',
                'stat_suffix' => '/7',
                'sort_order' => 10,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'stats', 'item_key' => 'stat_6'],
            [
                'section' => 'stats',
                'item_key' => 'stat_6',
                'stat_value' => '50',
                'stat_label' => 'Happy Clients',
                'stat_suffix' => 'K+',
                'sort_order' => 11,
                'is_active' => true,
            ]
        );

        // Overview Section
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'overview', 'item_key' => 'overview_main'],
            [
                'section' => 'overview',
                'item_key' => 'overview_main',
                'paragraphs' => 'United Worldwide Couriers offers an exceptional Express Air Freight service built for B2B enterprises, e-commerce businesses, and time-critical international shipments. We operate an end-to-end ecosystem from collection to customs clearance to last-mile delivery all under one roof. Our services span air freight to over 220 countries, including door-to-door personal import pickups and full freight-forwarding with seamless documentation. Each shipment is handled by a dedicated logistics specialist and backed by real-time tracking.',
                'list_items_text' => "Priority loading on partner airline networks across 6 continents\nFull customs brokerage with pre-clearance documentation support\nDedicated account manager and 24/7 live shipment support\nDoor-to-door delivery with real-time GPS tracking portal\nFulfilment services, returns management, and COD options",
                'button_text' => 'Book Shipments',
                'button_url' => '#',
                'extra_content' => json_encode([
                    'title' => 'Store. Manage. Fulfil—Smarter from The USA',
                    'image' => 'images/map-pattern.png',
                ]),
                'sort_order' => 12,
                'is_active' => true,
            ]
        );

        // Features Header
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'features', 'item_key' => 'features_header'],
            [
                'section' => 'features',
                'item_key' => 'features_header',
                'paragraphs' => 'Every feature is designed to give your business a competitive edge — speed, transparency, and reliability, every single shipment. Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself.',
                'extra_content' => json_encode([
                    'title' => 'What Makes Our E-commerce Logistics Stand Out',
                ]),
                'sort_order' => 13,
                'is_active' => true,
            ]
        );

        // Feature 1
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'features', 'item_key' => 'feature_tracking'],
            [
                'section' => 'features',
                'item_key' => 'feature_tracking',
                'subtitle' => 'Real-Time Tracking',
                'paragraphs' => 'Monitor your shipment at every checkpoint — from pickup to customs to final-mile — through our live dashboard and SMS alerts.',
                'icon_svg' => '<i class="fas fa-satellite"></i>',
                'icon_class' => 'fi-blue',
                'sort_order' => 14,
                'is_active' => true,
            ]
        );

        // Feature 2
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'features', 'item_key' => 'feature_temperature'],
            [
                'section' => 'features',
                'item_key' => 'feature_temperature',
                'subtitle' => 'Temperature Control',
                'paragraphs' => 'Specialized cold-chain and pharma-grade shipping options for sensitive or perishable cargo types.',
                'icon_svg' => '<i class="fas fa-thermometer-half"></i>',
                'icon_class' => 'fi-orange',
                'sort_order' => 15,
                'is_active' => true,
            ]
        );

        // Feature 3
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'features', 'item_key' => 'feature_support'],
            [
                'section' => 'features',
                'item_key' => 'feature_support',
                'subtitle' => '24/7 Support',
                'paragraphs' => 'Our logistics experts are available around the clock — via phone, chat, or email — to resolve any issue in real time.',
                'icon_svg' => '<i class="fas fa-headset"></i>',
                'icon_class' => 'fi-navy',
                'sort_order' => 16,
                'is_active' => true,
            ]
        );

        // Feature 4
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'features', 'item_key' => 'feature_eco'],
            [
                'section' => 'features',
                'item_key' => 'feature_eco',
                'subtitle' => 'Eco-Warehousing Options',
                'paragraphs' => 'Carbon-offset shipping options and sustainable packaging solutions for businesses committed to reducing their footprint.',
                'icon_svg' => '<i class="fas fa-leaf"></i>',
                'icon_class' => 'fi-green',
                'sort_order' => 17,
                'is_active' => true,
            ]
        );

        // FAQ Section - 11 items
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_1'],
            [
                'section' => 'faq',
                'item_key' => 'faq_1',
                'question' => 'How do I get started?',
                'answer' => 'To connect with our team, you have to register yourself, get a quote, and schedule your first pickup. Thereafter, the team will guide you through every step of the process.',
                'sort_order' => 27,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_2'],
            [
                'section' => 'faq',
                'item_key' => 'faq_2',
                'question' => 'How does United Worldwide Couriers meet your shipping and logistics needs?',
                'answer' => 'To provide the best freight management solutions, we work with broad strategies, technologies, and services to simplify the planning, storage, and movement of goods.',
                'sort_order' => 28,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_3'],
            [
                'section' => 'faq',
                'item_key' => 'faq_3',
                'question' => 'What packaging standards should we follow for shipping?',
                'answer' => 'We utilize study and secure packaging for small to large packages to protect your goods during transit. In case of fragile items, they will be cushioned enough and clearly labelled as "Fragile". In addition, we also provide a packaging and labelling guide for all new on boarders for no confusion and faster results.',
                'sort_order' => 29,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_4'],
            [
                'section' => 'faq',
                'item_key' => 'faq_4',
                'question' => 'How do we calculate cost?',
                'answer' => "The exact shipping cost will be calculated based on your goods' weight, dimensions, destinations, where it is expected to be delivered, and the delivery speed. If you are interested in knowing the accurate pricing, you are welcome to connect with the team.",
                'sort_order' => 30,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_5'],
            [
                'section' => 'faq',
                'item_key' => 'faq_5',
                'question' => 'Will I be notified about my shipment status?',
                'answer' => 'Yes. To keep the clients informed, our team provides regular updates via email or SMS throughout the shipping process.',
                'sort_order' => 31,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_6'],
            [
                'section' => 'faq',
                'item_key' => 'faq_6',
                'question' => 'Does United Worldwide Couriers handle bulk or commercial shipments?',
                'answer' => 'Yes, we specialize in managing bulk as well as commercial consignments with easy-flowing coordination, dedicated handling, secure transit, and timely delivery. This remains the same for both small and large volumes.',
                'sort_order' => 32,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_7'],
            [
                'section' => 'faq',
                'item_key' => 'faq_7',
                'question' => 'Can I schedule a pickup for my shipment?',
                'answer' => 'Yes, you can easily schedule a pick up at your preferred time either by reaching out to our team online (via our website) or by directly contacting our customer support.',
                'sort_order' => 33,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_8'],
            [
                'section' => 'faq',
                'item_key' => 'faq_8',
                'question' => 'How can I track my shipment?',
                'answer' => 'Through our online tracking system, you can track your shipment in real-time. For that, you just have to enter the tracking ID provided on our website dashboard to get live updates.',
                'sort_order' => 34,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_9'],
            [
                'section' => 'faq',
                'item_key' => 'faq_9',
                'question' => 'Will my package be picked up by the United Worldwide Couriers team only?',
                'answer' => 'It depends on the service and location. So, your shipment may be picked up either by our in-house delivery team or by third-party courier partners. On the contrary, we ensure strict quality control and safe handling for whatever the case may be.',
                'sort_order' => 35,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_10'],
            [
                'section' => 'faq',
                'item_key' => 'faq_10',
                'question' => 'Do you provide customs clearance support?',
                'answer' => 'Yes, while others face limited support, we complete international customs documentation and clearance faster. This way, we support rapid shipping without delays.',
                'sort_order' => 36,
                'is_active' => true,
            ]
        );

        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'faq', 'item_key' => 'faq_11'],
            [
                'section' => 'faq',
                'item_key' => 'faq_11',
                'question' => 'What happens if my shipment is delayed or stuck?',
                'answer' => 'First things first, the team proactively monitors the shipment to identify and resolve potential issues before they escalate. Still, if an issue occurs, the internal team coordinates with the partners to resolve the situation quickly.',
                'sort_order' => 37,
                'is_active' => true,
            ]
        );

        // CTA Section
        WarehousingSolutionsPage::updateOrCreate(
            ['section' => 'cta', 'item_key' => 'cta_main'],
            [
                'section' => 'cta',
                'item_key' => 'cta_main',
                'button_text' => 'Contact Us',
                'button_url' => '/contact-us',
                'extra_content' => json_encode([
                    'title' => 'Ready to Optimize Your Storage?',
                    'subtitle' => 'Get in touch with our team to discuss your warehousing needs',
                ]),
                'sort_order' => 38,
                'is_active' => true,
            ]
        );
    }
}
