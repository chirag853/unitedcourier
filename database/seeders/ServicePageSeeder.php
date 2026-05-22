<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServicePage;

class ServicePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Services
        ServicePage::create([
            'section' => 'services',
            'item_key' => 'service_1',
            'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M17.8 19.2L16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"></path></svg>',
            'color_scheme' => 'sc-air',
            'button_url' => 'express-air-freight-solutions.php',
            'btn_text' => 'View Routes →',
            'extra_content' => json_encode([
                'title' => 'Express Air Freight Solutions',
                'description' => 'High-priority cargo handling with guaranteed flight space and rapid customs clearance globally.',
            ]),
            'sort_order' => 1,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'services',
            'item_key' => 'service_2',
            'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 1 2 2v11.61a2 2 0 0 1-2-2v-11.61a2 2 0 0 1-2-2H1z"></path></svg>',
            'color_scheme' => 'sc-ecom',
            'button_url' => '#',
            'extra_content' => json_encode([
                'title' => 'E-commerce Logistics Solutions',
                'description' => 'Fully integrated shipping for online stores. Real-time API connections and returns management.',
            ]),
            'sort_order' => 2,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'services',
            'item_key' => 'service_3',
            'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
            'color_scheme' => 'sc-ware',
            'button_url' => '#',
            'extra_content' => json_encode([
                'title' => 'Warehousing Solutions',
                'description' => 'State-of-the-art climate controlled storage with inventory management and 24/7 security.',
            ]),
            'sort_order' => 3,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'services',
            'item_key' => 'service_4',
            'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 22 9"></polyline></svg>',
            'color_scheme' => 'sc-drop',
            'button_url' => '#',
            'extra_content' => json_encode([
                'title' => 'B2B Shipping Made Simple',
                'description' => 'Scale your brand without holding inventory. We handle fulfillment directly to your end customers.',
            ]),
            'sort_order' => 4,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'services',
            'item_key' => 'service_5',
            'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><polyline points="22 12 18 12 2 12 2 12"></polyline></svg>',
            'color_scheme' => 'sc-tech',
            'button_url' => '#',
            'extra_content' => json_encode([
                'title' => 'Marketplace Shipping',
                'description' => 'Real-time data visualization and AI-driven insights to optimize your global cargo movements.',
            ]),
            'sort_order' => 5,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'services',
            'item_key' => 'service_6',
            'icon_svg' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>',
            'color_scheme' => 'sc-secure',
            'button_url' => '#',
            'extra_content' => json_encode([
                'title' => 'Dropshipping Solutions',
                'description' => 'Dedicated security protocols and full-coverage insurance for your most sensitive shipments.',
            ]),
            'sort_order' => 6,
            'is_active' => true
        ]);

        // Testimonials
        ServicePage::create([
            'section' => 'testimonials',
            'item_key' => 'review_1',
            'name' => 'Shelly Kapoor',
            'avatar_url' => 'public/website_images/review-1.png',
            'rating' => 5,
            'text_content' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
            'sort_order' => 1,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'testimonials',
            'item_key' => 'review_2',
            'name' => 'Vansh Agarwal',
            'avatar_url' => 'public/website_images/review-2.png',
            'rating' => 5,
            'text_content' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.',
            'sort_order' => 2,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'testimonials',
            'item_key' => 'review_3',
            'name' => 'Rahul Mehta',
            'avatar_url' => 'public/website_images/review-3.png',
            'rating' => 5,
            'text_content' => 'Best courier service for international deliveries, offering smooth documentation, fast processing, and quick dispatch with no delays or complications.',
            'sort_order' => 3,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'testimonials',
            'item_key' => 'review_4',
            'name' => 'Anjali Sharma',
            'avatar_url' => 'public/website_images/review-4.png',
            'rating' => 5,
            'text_content' => 'Affordable pricing combined with premium quality service makes United Worldwide Couriers a highly recommended choice for all shipping and logistics needs.',
            'sort_order' => 4,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'testimonials',
            'item_key' => 'review_5',
            'name' => 'Karan Singh',
            'avatar_url' => 'public/website_images/review-5.png',
            'rating' => 5,
            'text_content' => 'Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience',
            'sort_order' => 5,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'testimonials',
            'item_key' => 'review_6',
            'name' => 'Vinay Verma',
            'avatar_url' => 'public/website_images/review-5.png',
            'rating' => 5,
            'text_content' => 'United Worldwide Couriers delivered my international parcel quickly and safely, with smooth handling and excellent service throughout the entire process.',
            'sort_order' => 6,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'testimonials',
            'item_key' => 'review_7',
            'name' => 'Shelly Kapoor',
            'avatar_url' => 'public/website_images/review-6.png',
            'rating' => 5,
            'text_content' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
            'sort_order' => 7,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'testimonials',
            'item_key' => 'review_8',
            'name' => 'Vansh Agarwal',
            'avatar_url' => 'public/website_images/review-7.png',
            'rating' => 5,
            'text_content' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.',
            'sort_order' => 8,
            'is_active' => true
        ]);

        // FAQs
        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'how_to_start',
            'question' => 'How do I get started?',
            'answer' => 'To connect with our team, you have to register yourself, get a quote, and schedule your first pickup. Thereafter, the team will guide you through every step of the process.',
            'sort_order' => 1,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'shipping_needs',
            'question' => 'How does United Worldwide Couriers meet your shipping and logistics needs?',
            'answer' => 'To provide the best freight management solutions, we work with broad strategies, technologies, and services to simplify the planning, storage, and movement of goods.',
            'sort_order' => 2,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'packaging_standards',
            'question' => 'What packaging standards should we follow for shipping?',
            'answer' => 'We utilize study and secure packaging for small to large packages to protect your goods during transit. In case of fragile items, they will be cushioned enough and clearly labelled as "Fragile". In addition, we also provide a packaging and labelling guide for all new on boarders for no confusion and faster results.',
            'sort_order' => 3,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'cost_calculation',
            'question' => 'How do we calculate cost?',
            'answer' => 'The exact shipping cost will be calculated based on your goods weight, dimensions, destinations, where it is expected to be delivered, and the delivery speed. If you are interested in knowing the accurate pricing, you are welcome to connect with the team.',
            'sort_order' => 4,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'shipment_notifications',
            'question' => 'Will I be notified about my shipment status?',
            'answer' => 'Yes. To keep clients informed, our team provides regular updates via email or SMS throughout the shipping process.',
            'sort_order' => 5,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'bulk_shipments',
            'question' => 'Does United Worldwide Couriers handle bulk or commercial shipments?',
            'answer' => 'Yes, we specialize in managing bulk as well as commercial consignments with easy-flowing coordination, dedicated handling, secure transit, and timely delivery. This remains the same for both small and large volumes.',
            'sort_order' => 6,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'schedule_pickup',
            'question' => 'Can I schedule a pickup for my shipment?',
            'answer' => 'Yes, you can easily schedule a pick up at your preferred time either by reaching out to our team online (via our website) or by directly contacting our customer support.',
            'sort_order' => 7,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'track_shipment',
            'question' => 'How can I track my shipment?',
            'answer' => 'Through our online tracking system, you can track your shipment in real-time. For that, you just have to enter the tracking ID provided on our website dashboard to get live updates.',
            'sort_order' => 8,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'pickup_team',
            'question' => 'Will my package be picked up by the United Worldwide Couriers team only?',
            'answer' => 'It depends on the service and location. So, your shipment may be picked up either by our in-house delivery team or by third-party courier partners. On the contrary, we ensure strict quality control and safe handling for whatever the case may be.',
            'sort_order' => 9,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'customs_clearance',
            'question' => 'Do you provide customs clearance support?',
            'answer' => 'Yes, while others face limited support, we complete international customs documentation and clearance faster. This way, we support rapid shipping without delays.',
            'sort_order' => 10,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'faq',
            'item_key' => 'delayed_shipment',
            'question' => 'What happens if my shipment is delayed or stuck?',
            'answer' => 'First things first, the team proactively monitors the shipment to identify and resolve potential issues before they escalate. Still, if an issue occurs, the internal team coordinates with the partners to resolve the situation quickly.',
            'sort_order' => 11,
            'is_active' => true
        ]);

        // Stats
        ServicePage::create([
            'section' => 'stats',
            'item_key' => 'stat_1',
            'stat_value' => '30+',
            'stat_label' => 'Years of Excellence',
            'sort_order' => 1,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'stats',
            'item_key' => 'stat_2',
            'stat_value' => '500+',
            'stat_label' => 'Global Partners',
            'sort_order' => 2,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'stats',
            'item_key' => 'stat_3',
            'stat_value' => '1M+',
            'stat_label' => 'Deliveries',
            'sort_order' => 3,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'stats',
            'item_key' => 'stat_4',
            'stat_value' => '99.5%',
            'stat_label' => 'On-Time Rate',
            'sort_order' => 4,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'stats',
            'item_key' => 'stat_5',
            'stat_value' => '24/7',
            'stat_label' => 'Support',
            'sort_order' => 5,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'stats',
            'item_key' => 'stat_6',
            'stat_value' => '150+',
            'stat_label' => 'Countries',
            'sort_order' => 6,
            'is_active' => true
        ]);

        // Partner Logos
        ServicePage::create([
            'section' => 'partners',
            'item_key' => 'partner_fedex',
            'name' => 'FedEx',
            'logo_url' => 'https://1000logos.net/wp-content/uploads/2021/04/Fedex-logo.png',
            'alt_text' => 'FedEx',
            'sort_order' => 1,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'partners',
            'item_key' => 'partner_ups',
            'name' => 'UPS',
            'logo_url' => 'https://www.ups.com/webassets/icons/logo.svg',
            'alt_text' => 'UPS',
            'sort_order' => 2,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'partners',
            'item_key' => 'partner_usps',
            'name' => 'USPS',
            'logo_url' => 'https://www.usps.com/global-elements/header/public/website_images/utility-header/logo-sb.svg',
            'alt_text' => 'USPS',
            'sort_order' => 3,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'partners',
            'item_key' => 'partner_tnt',
            'name' => 'TNT',
            'logo_url' => 'https://www.tnt.com/dam/tnt_express_media/en_gb/public/website_images/ChoosingTNT/TNT-Logo-edt.png',
            'alt_text' => 'TNT',
            'sort_order' => 4,
            'is_active' => true
        ]);

        ServicePage::create([
            'section' => 'partners',
            'item_key' => 'partner_aramex',
            'name' => 'Aramex',
            'logo_url' => 'https://dotcomaramexprod.blob.core.windows.net/default/docs/default-source/logo/aramex-logo-english.webp',
            'alt_text' => 'Aramex',
            'sort_order' => 5,
            'is_active' => true
        ]);
    }
}
