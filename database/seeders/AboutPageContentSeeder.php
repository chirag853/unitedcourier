<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutPageContent;

class AboutPageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hero Section
        AboutPageContent::create([
            'section_type' => 'hero',
            'title' => 'Global Logistics Excellence with United Couriers',
            'description' => 'United Couriers is your trusted partner for comprehensive logistics solutions. We specialize in time-critical deliveries, freight forwarding, customs clearance, and supply chain management. With cutting-edge technology and a global network, we ensure your shipments reach their destination safely and on time, every time.',
            'image' => 'public/website_images/about-united.webp',
            'page_badge_text' => 'About Us',
            'display_order' => 1,
        ]);

        // Statistics / Facts
        $stats = [
            ['title' => 'Cities Covered', 'page_target_number' => 150, 'page_suffix' => '+', 'display_order' => 1],
            ['title' => 'Daily Parcels', 'page_target_number' => 100, 'page_suffix' => 'K+', 'display_order' => 2],
            ['title' => 'Delivery Riders', 'page_target_number' => 5, 'page_suffix' => 'K+', 'display_order' => 3],
            ['title' => 'On-time Rate', 'page_target_number' => 99, 'page_suffix' => '.9%', 'display_order' => 4],
            ['title' => 'Live Tracking', 'page_target_number' => 24, 'page_suffix' => '/7', 'display_order' => 5],
            ['title' => 'Happy Clients', 'page_target_number' => 50, 'page_suffix' => 'K+', 'display_order' => 6],
        ];

        foreach ($stats as $stat) {
            AboutPageContent::create([
                'section_type' => 'stat',
                'title' => $stat['title'],
                'page_target_number' => $stat['page_target_number'],
                'page_suffix' => $stat['page_suffix'],
                'display_order' => $stat['display_order'],
            ]);
        }

        // Overview Section
        AboutPageContent::create([
            'section_type' => 'overview',
            'title' => 'Overview for United Couriers',
            'description' => 'United Worldwide Couriers has built an exceptional logistics ecosystem for modern B2B enterprises and scaling e-commerce businesses. Our services span air freight, road transport (including pan-India pickups), customs brokerage (clearance with adequate documentation), and fulfilment services, all under one roof. Every shipment is supported by a team of professionals who foresee and resolve potential challenges prior to their occurrence. Each client is entertained by dedicated support and a special personal touch.' . "\n\n" . 'United Worldwide Couriers has built an exceptional logistics ecosystem for modern B2B enterprises and scaling e-commerce businesses. Our services span air freight, road transport (including pan-India pickups), customs brokerage (clearance with adequate documentation), and fulfilment services, all under one roof.',
            'image' => 'public/website_images/global-network.webp',
            'page_button_text' => 'Book Shipments',
            'display_order' => 1,
        ]);

        // Mission & Vision Intro
        AboutPageContent::create([
            'section_type' => 'mission_vision_intro',
            'title' => 'Our Mission and Vision',
            'description' => 'That\'s why United Worldwide Couriers offers flexible logistics solutions built around your shipment type, delivery timeline, budget, and business goals. Whether you need B2B Export Support, Dropshipping Solutions, Marketplace shipping, or personal deliveries for friends and family, we help you choose the right service with clarity, reliability, and complete support.',
            'display_order' => 1,
        ]);

        // Mission Card
        AboutPageContent::create([
            'section_type' => 'mission',
            'title' => 'Delivering with Care and Commitment',
            'description' => 'To deliver excellence by ensuring timely, secure, and cost-effective courier services across every destination we serve. We are committed to building long-term relationships through reliability, transparency, and customer-first service, powered by innovation and a passionate team.',
            'icon_svg' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
            'page_tag' => 'Our Mission',
            'page_color_scheme' => 'blue',
            'display_order' => 2,
        ]);

        // Vision Card
        AboutPageContent::create([
            'section_type' => 'vision',
            'title' => 'Connecting the World with Trust and Speed',
            'description' => 'To become a trusted leader in logistics, connecting people and businesses through fast, reliable, and seamless delivery solutions. We envision a future where every shipment is handled with precision, every destination is within reach, and every customer experiences unmatched trust and efficiency.',
            'icon_svg' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
            'page_tag' => 'Our Vision',
            'page_color_scheme' => 'purple',
            'display_order' => 3,
        ]);

        // Journey Timeline Intro
        AboutPageContent::create([
            'section_type' => 'journey_intro',
            'title' => 'One-stop solution to achieve your dream',
            'image' => 'public/website_images/logistic.webp',
            'page_countries' => '220+',
            'page_pin_codes' => '19000+',
            'display_order' => 1,
        ]);

        // Milestones
        $milestones = [
            [
                'title' => 'The Foundation (2017)',
                'description' => 'Starting with a vision to revolutionize last-mile delivery, we launched our first hub with just 10 dedicated riders.',
                'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>',
                'page_year' => 2017,
                'page_card_color_class' => 'card-purple',
                'display_order' => 1,
            ],
            [
                'title' => 'Hyper-Growth (2019)',
                'description' => 'Expanded to 50+ cities. Our partner network grew by 400%, becoming the preferred courier for top e-commerce players.',
                'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'page_year' => 2019,
                'page_card_color_class' => 'card-green',
                'display_order' => 2,
            ],
            [
                'title' => 'Tech First (2021)',
                'description' => 'Introduced real-time AI tracking and automated sorting, ensuring 99.9% accuracy across our entire logistics chain.',
                'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
                'page_year' => 2021,
                'page_card_color_class' => 'card-blue',
                'display_order' => 3,
            ],
            [
                'title' => 'Global Reach (2023)',
                'description' => 'Cross-border shipping launched, connecting local businesses to over 220 countries with effortless international logistics.',
                'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
                'page_year' => 2023,
                'page_card_color_class' => 'card-orange',
                'display_order' => 4,
            ],
            [
                'title' => 'Sustainable Future (2025)',
                'description' => 'Committing to 100% EV delivery for last-mile and pioneering zero-waste packaging for all corporate partners.',
                'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>',
                'page_year' => 2025,
                'page_card_color_class' => 'card-blue',
                'display_order' => 5,
            ],
        ];

        foreach ($milestones as $milestone) {
            AboutPageContent::create([
                'section_type' => 'milestone',
                'title' => $milestone['title'],
                'description' => $milestone['description'],
                'icon_svg' => $milestone['icon_svg'],
                'page_year' => $milestone['page_year'],
                'page_card_color_class' => $milestone['page_card_color_class'],
                'display_order' => $milestone['display_order'],
            ]);
        }

        // Testimonials
        $testimonials = [
            [
                'title' => 'Shelly Kapoor',
                'description' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
                'image' => 'public/website_images/review-1.png',
                'page_rating' => 5,
                'display_order' => 1,
            ],
            [
                'title' => 'Vansh Agarwal',
                'description' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.',
                'image' => 'public/website_images/review-2.png',
                'page_rating' => 5,
                'display_order' => 2,
            ],
            [
                'title' => 'Rahul Mehta',
                'description' => 'Best courier service for international deliveries, offering smooth documentation, fast processing, and quick dispatch with no delays or complications.',
                'image' => 'public/website_images/review-3.png',
                'page_rating' => 5,
                'display_order' => 3,
            ],
            [
                'title' => 'Anjali Sharma',
                'description' => 'Affordable pricing combined with premium quality service makes United Worldwide Couriers a highly recommended choice for all shipping and logistics needs.',
                'image' => 'public/website_images/review-4.png',
                'page_rating' => 5,
                'display_order' => 4,
            ],
            [
                'title' => 'Karan Singh',
                'description' => 'Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience.',
                'image' => 'public/website_images/review-5.png',
                'page_rating' => 5,
                'display_order' => 5,
            ],
            [
                'title' => 'Vinay Verma',
                'description' => 'United Worldwide Couriers delivered my international parcel quickly and safely, with smooth handling and excellent service throughout the entire process.',
                'image' => 'public/website_images/review-6.png',
                'page_rating' => 5,
                'display_order' => 6,
            ],
            [
                'title' => 'Shelly Kapoor (second review)',
                'description' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
                'image' => 'public/website_images/review-7.png',
                'page_rating' => 5,
                'display_order' => 7,
            ],
            [
                'title' => 'Vansh Agarwal (second review)',
                'description' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.',
                'image' => 'public/website_images/review-7.png',
                'page_rating' => 5,
                'display_order' => 8,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            AboutPageContent::create([
                'section_type' => 'testimonial',
                'title' => $testimonial['title'],
                'description' => $testimonial['description'],
                'image' => $testimonial['image'],
                'page_rating' => $testimonial['page_rating'],
                'display_order' => $testimonial['display_order'],
            ]);
        }

        // FAQ Header
        AboutPageContent::create([
            'section_type' => 'faq_header',
            'title' => 'Frequently Asked Questions',
            'subtitle' => 'Common Questions',
            'display_order' => 1,
        ]);

        // FAQ Items
        $faqs = [
            [
                'title' => 'How do I get started?',
                'description' => 'To connect with our team, you have to register yourself, get a quote, and schedule your first pickup. Thereafter, the team will guide you through every step of the process.',
                'display_order' => 1,
            ],
            [
                'title' => 'How does United Worldwide Couriers meet your shipping and logistics needs?',
                'description' => 'To provide the best freight management solutions, we work with broad strategies, technologies, and services to simplify the planning, storage, and movement of goods.',
                'display_order' => 2,
            ],
            [
                'title' => 'What packaging standards should we follow for shipping?',
                'description' => 'We utilize study and secure packaging for small to large packages to protect your goods during transit. In case of fragile items, they will be cushioned enough and clearly labelled as "Fragile". In addition, we also provide a packaging and labelling guide for all new on boarders for no confusion and faster results.',
                'display_order' => 3,
            ],
            [
                'title' => 'How do we calculate cost?',
                'description' => 'The exact shipping cost will be calculated based on your goods weight, dimensions, destinations, where it is expected to be delivered, and the delivery speed. If you are interested in knowing the accurate pricing, you are welcome to connect with the team.',
                'display_order' => 4,
            ],
            [
                'title' => 'Will I be notified about my shipment status?',
                'description' => 'Yes. To keep the clients informed, our team provides regular updates via email or SMS throughout the shipping process.',
                'display_order' => 5,
            ],
            [
                'title' => 'Does United Worldwide Couriers handle bulk or commercial shipments?',
                'description' => 'Yes, we specialize in managing bulk as well as commercial consignments with easy-flowing coordination, dedicated handling, secure transit, and timely delivery. This remains the same for both small and large volumes.',
                'display_order' => 6,
            ],
            [
                'title' => 'Can I schedule a pickup for my shipment?',
                'description' => 'Yes, you can easily schedule a pick up at your preferred time either by reaching out to our team online (via our website) or by directly contacting our customer support.',
                'display_order' => 7,
            ],
            [
                'title' => 'How can I track my shipment?',
                'description' => 'Through our online tracking system, you can track your shipment in real-time. For that, you just have to enter the tracking ID provided on our website dashboard to get live updates.',
                'display_order' => 8,
            ],
            [
                'title' => 'Will my package be picked up by the United Worldwide Couriers team only?',
                'description' => 'It depends on the service and location. So, your shipment may be picked up either by our in-house delivery team or by third-party courier partners. On the contrary, we ensure strict quality control and safe handling for whatever the case may be.',
                'display_order' => 9,
            ],
            [
                'title' => 'Do you provide customs clearance support?',
                'description' => 'Yes, while others face limited support, we complete international customs documentation and clearance faster. This way, we support rapid shipping without delays.',
                'display_order' => 10,
            ],
            [
                'title' => 'What happens if my shipment is delayed or stuck?',
                'description' => 'First things first, the team proactively monitors the shipment to identify and resolve potential issues before they escalate. Still, if an issue occurs, the internal team coordinates with the partners to resolve the situation quickly.',
                'display_order' => 11,
            ],
        ];

        foreach ($faqs as $faq) {
            AboutPageContent::create([
                'section_type' => 'faq',
                'title' => $faq['title'],
                'description' => $faq['description'],
                'display_order' => $faq['display_order'],
            ]);
        }

        // Partner Logos
        $partners = [
            ['title' => 'FedEx', 'image' => 'https://1000logos.net/wp-content/uploads/2021/04/Fedex-logo.png', 'display_order' => 1],
            ['title' => 'UPS', 'image' => 'https://www.ups.com/webassets/icons/logo.svg', 'display_order' => 2],
            ['title' => 'USPS', 'image' => 'https://www.usps.com/global-elements/header/images/utility-header/logo-sb.svg', 'display_order' => 3],
            ['title' => 'TNT', 'image' => 'https://www.tnt.com/dam/tnt_express_media/en_gb/images/ChoosingTNT/TNT-Logo-edt.png', 'display_order' => 4],
            ['title' => 'Aramex', 'image' => 'https://dotcomaramexprod.blob.core.windows.net/default/docs/default-source/logo/aramex-logo-english.webp', 'display_order' => 5],
        ];

        foreach ($partners as $partner) {
            AboutPageContent::create([
                'section_type' => 'partner',
                'title' => $partner['title'],
                'image' => $partner['image'],
                'display_order' => $partner['display_order'],
            ]);
        }

        // Newsletter CTA
        AboutPageContent::create([
            'section_type' => 'newsletter_cta',
            'title' => 'Never miss a shipment update!',
            'description' => 'Subscribe for smart logistics tips, real-time updates, and exclusive offers to simplify your shipping experience.',
            'display_order' => 1,
        ]);
    }
}
