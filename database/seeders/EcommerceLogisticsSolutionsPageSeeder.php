<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EcommerceLogisticsSolutionsPage;

class EcommerceLogisticsSolutionsPageSeeder extends Seeder
{
    public function run(): void
    {
        // ==================== HERO SECTION ====================
        EcommerceLogisticsSolutionsPage::create([
            'section' => 'hero',
            'item_key' => 'hero_main',
            'badge_text' => 'E-commerce Logistics Solutions',
            'extra_content' => json_encode([
                'title' => 'Built for Sellers. <span class="moving-gradient-text">Designed for scale</span>',
                'description' => 'Connect your marketplace account directly with our platform and ship orders with ease — starting from lightweight parcels of just 50g to high-volume e-commerce shipments.',
                'button_primary_text' => 'Book a Shipping',
                'button_primary_icon' => 'fa-paper-plane',
                'button_primary_url' => '#',
                'button_secondary_text' => 'Get a Quote',
                'button_secondary_icon' => 'fa-calculator',
                'button_secondary_url' => '#',
                'image' => 'images/ecomm-service.webp',
                'badges' => [
                    ['icon' => 'fa-clock', 'text' => '24–72 Hr Delivery'],
                    ['icon' => 'fa-shield-alt', 'text' => 'Fully Insured'],
                    ['icon' => 'fa-map-marker-alt', 'text' => '220+ Countries'],
                ],
                'stat_pills' => [
                    ['icon' => 'fa-box', 'value' => '50K+', 'label' => 'Shipments/Month', 'color' => 'rgba(26,115,232,.1)', 'text_color' => 'var(--primary)'],
                    ['icon' => 'fa-star', 'value' => '4.9★', 'label' => 'Avg Rating', 'color' => 'rgba(255,107,0,.1)', 'text_color' => 'var(--accent)'],
                    ['icon' => 'fa-check-circle', 'value' => '99.2%', 'label' => 'On-Time Rate', 'color' => 'rgba(40,167,69,.1)', 'text_color' => 'var(--success)'],
                ],
            ]),
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // ==================== STATS SECTION ====================
        EcommerceLogisticsSolutionsPage::create([
            'section' => 'stats',
            'item_key' => 'stats_header',
            'extra_content' => json_encode([
                'title' => 'Trusted by over <span class="gradient-text">50,000 Businesses</span> for daily logistics',
            ]),
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $stats = [
            ['value' => '150', 'suffix' => '+', 'label' => 'Cities Covered'],
            ['value' => '100', 'suffix' => 'K+', 'label' => 'Daily Parcels'],
            ['value' => '5', 'suffix' => 'K+', 'label' => 'Delivery Riders'],
            ['value' => '99', 'suffix' => '.9%', 'label' => 'On-time Rate'],
            ['value' => '24', 'suffix' => '/7', 'label' => 'Live Tracking'],
            ['value' => '50', 'suffix' => 'K+', 'label' => 'Happy Clients'],
        ];

        foreach ($stats as $i => $stat) {
            EcommerceLogisticsSolutionsPage::create([
                'section' => 'stats',
                'item_key' => 'stat_' . ($i + 1),
                'stat_value' => $stat['value'],
                'stat_suffix' => $stat['suffix'],
                'stat_label' => $stat['label'],
                'sort_order' => $i + 2,
                'is_active' => true,
            ]);
        }

        // ==================== OVERVIEW / ABOUT SECTION ====================
        EcommerceLogisticsSolutionsPage::create([
            'section' => 'overview',
            'item_key' => 'overview_main',
            'button_text' => 'Book Shipments',
            'button_url' => '#',
            'check_list_text' => "Priority loading on partner airline networks across 6 continents\nFull customs brokerage with pre-clearance documentation support\nDedicated account manager and 24/7 live shipment support\nDoor-to-door delivery with real-time GPS tracking portal\nFulfilment services, returns management, and COD options",
            'extra_content' => json_encode([
                'title' => 'Dedicated Supply Chain for <span class="moving-gradient-text">E-commerce</span>',
                'description' => 'United Worldwide Couriers offers an exceptional Express Air Freight service built for B2B enterprises, e-commerce businesses, and time-critical international shipments. We operate an end-to-end ecosystem from collection to customs clearance to last-mile delivery all under one roof. Our services span air freight to over 220 countries, including door-to-door personal import pickups and full freight-forwarding with seamless documentation. Each shipment is handled by a dedicated logistics specialist and backed by real-time tracking.',
                'image' => 'images/map-pattern.png',
            ]),
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // ==================== FEATURES SECTION ====================
        EcommerceLogisticsSolutionsPage::create([
            'section' => 'features',
            'item_key' => 'features_header',
            'extra_content' => json_encode([
                'title' => 'What Makes Our E-commerce Logistics Stand Out',
                'description' => 'Every feature is designed to give your business a competitive edge — speed, transparency, and reliability, every single shipment. Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself.',
            ]),
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $features = [
            [
                'icon' => 'fa-satellite',
                'color_class' => 'fi-blue',
                'title' => 'Real-Time Tracking',
                'description' => 'Monitor your shipment at every checkpoint — from pickup to customs to final-mile — through our live dashboard and SMS alerts.',
            ],
            [
                'icon' => 'fa-thermometer-half',
                'color_class' => 'fi-orange',
                'title' => 'Temperature Control',
                'description' => 'Specialized cold-chain and pharma-grade shipping options for sensitive or perishable cargo types.',
            ],
            [
                'icon' => 'fa-headset',
                'color_class' => 'fi-navy',
                'title' => '24/7 Support',
                'description' => 'Our logistics experts are available around the clock — via phone, chat, or email — to resolve any issue in real time.',
            ],
            [
                'icon' => 'fa-leaf',
                'color_class' => 'fi-green',
                'title' => 'Eco-Ecommerce Options',
                'description' => 'Carbon-offset shipping options and sustainable packaging solutions for businesses committed to reducing their footprint.',
            ],
        ];

        foreach ($features as $i => $feature) {
            EcommerceLogisticsSolutionsPage::create([
                'section' => 'features',
                'item_key' => 'feature_' . ($i + 1),
                'color_scheme' => $feature['color_class'],
                'extra_content' => json_encode([
                    'icon' => $feature['icon'],
                    'title' => $feature['title'],
                    'description' => $feature['description'],
                ]),
                'sort_order' => $i + 2,
                'is_active' => true,
            ]);
        }

        // ==================== TESTIMONIALS SECTION ====================
        EcommerceLogisticsSolutionsPage::create([
            'section' => 'testimonials',
            'item_key' => 'testimonials_header',
            'extra_content' => json_encode([
                'title' => 'Trusted by the Brands You Trust',
                'description' => 'Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself. From on-time delivery and zero-compromise on quality to customer satisfaction, United Worldwide Couriers is completely reliable and trustworthy.',
                'google_review_image' => 'images/google-review.png',
            ]),
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $testimonials = [
            [
                'name' => 'Shelly Kapoor',
                'avatar' => 'images/review-1.png',
                'rating' => 5,
                'text' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
            ],
            [
                'name' => 'Vansh Agarwal',
                'avatar' => 'images/review-2.png',
                'rating' => 5,
                'text' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.',
            ],
            [
                'name' => 'Rahul Mehta',
                'avatar' => 'images/review-3.png',
                'rating' => 5,
                'text' => 'Best courier service for international deliveries, offering smooth documentation, fast processing, and quick dispatch with no delays or complications.',
            ],
            [
                'name' => 'Anjali Sharma',
                'avatar' => 'images/review-4.png',
                'rating' => 5,
                'text' => 'Affordable pricing combined with premium quality service makes United Worldwide Couriers a highly recommended choice for all shipping and logistics needs.',
            ],
            [
                'name' => 'Karan Singh',
                'avatar' => 'images/review-5.png',
                'rating' => 5,
                'text' => 'Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience',
            ],
            [
                'name' => 'Vinay Verma',
                'avatar' => 'images/review-5.png',
                'rating' => 5,
                'text' => 'United Worldwide Couriers delivered my international parcel quickly and safely, with smooth handling and excellent service throughout the entire process.',
            ],
            [
                'name' => 'Shelly Kapoor',
                'avatar' => 'images/review-6.png',
                'rating' => 5,
                'text' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
            ],
            [
                'name' => 'Vansh Agarwal',
                'avatar' => 'images/review-7.png',
                'rating' => 5,
                'text' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.',
            ],
        ];

        foreach ($testimonials as $i => $testimonial) {
            EcommerceLogisticsSolutionsPage::create([
                'section' => 'testimonials',
                'item_key' => 'review_' . ($i + 1),
                'name' => $testimonial['name'],
                'avatar_url' => $testimonial['avatar'],
                'rating' => $testimonial['rating'],
                'text_content' => $testimonial['text'],
                'sort_order' => $i + 2,
                'is_active' => true,
            ]);
        }

        // ==================== FAQ SECTION ====================
        EcommerceLogisticsSolutionsPage::create([
            'section' => 'faq',
            'item_key' => 'faq_header',
            'extra_content' => json_encode([
                'badge' => 'Common Questions',
                'title' => 'Frequently Asked Questions',
                'sidebar_image' => 'https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif',
                'sidebar_title' => 'Need personalized help?',
                'sidebar_description' => 'Our logistics experts are available 24/7 to assist your requirements.',
                'contact_box_title' => 'Contact Us',
                'contact_box_description' => 'For urgent inquiries regarding your current shipment status.',
                'contact_button_text' => 'Message Support',
            ]),
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $faqs = [
            [
                'question' => 'How do I get started?',
                'answer' => 'To connect with our team, you have to register yourself, get a quote, and schedule your first pickup. Thereafter, the team will guide you through every step of the process.',
            ],
            [
                'question' => 'How does United Worldwide Couriers meet your shipping and logistics needs?',
                'answer' => 'To provide the best freight management solutions, we work with broad strategies, technologies, and services to simplify the planning, storage, and movement of goods.',
            ],
            [
                'question' => 'What packaging standards should we follow for shipping?',
                'answer' => 'We utilize study and secure packaging for small to large packages to protect your goods during transit. In case of fragile items, they will be cushioned enough and clearly labelled as "Fragile". In addition, we also provide a packaging and labelling guide for all new on boarders for no confusion and faster results.',
            ],
            [
                'question' => 'How do we calculate cost?',
                'answer' => 'The exact shipping cost will be calculated based on your goods\' weight, dimensions, destinations, where it is expected to be delivered, and the delivery speed. If you are interested in knowing the accurate pricing, you are welcome to connect with the team.',
            ],
            [
                'question' => 'Will I be notified about my shipment status?',
                'answer' => 'Yes. To keep the clients informed, our team provides regular updates via email or SMS throughout the shipping process.',
            ],
            [
                'question' => 'Does United Worldwide Couriers handle bulk or commercial shipments?',
                'answer' => 'Yes, we specialize in managing bulk as well as commercial consignments with easy-flowing coordination, dedicated handling, secure transit, and timely delivery. This remains the same for both small and large volumes.',
            ],
            [
                'question' => 'Can I schedule a pickup for my shipment?',
                'answer' => 'Yes, you can easily schedule a pick up at your preferred time either by reaching out to our team online (via our website) or by directly contacting our customer support.',
            ],
            [
                'question' => 'How can I track my shipment?',
                'answer' => 'Through our online tracking system, you can track your shipment in real-time. For that, you just have to enter the tracking ID provided on our website dashboard to get live updates.',
            ],
            [
                'question' => 'Will my package be picked up by the United Worldwide Couriers team only?',
                'answer' => 'It depends on the service and location. So, your shipment may be picked up either by our in-house delivery team or by third-party courier partners. On the contrary, we ensure strict quality control and safe handling for whatever the case may be.',
            ],
            [
                'question' => 'Do you provide customs clearance support?',
                'answer' => 'Yes, while others face limited support, we complete international customs documentation and clearance faster. This way, we support rapid shipping without delays.',
            ],
            [
                'question' => 'What happens if my shipment is delayed or stuck?',
                'answer' => 'First things first, the team proactively monitors the shipment to identify and resolve potential issues before they escalate. Still, if an issue occurs, the internal team coordinates with the partners to resolve the situation quickly.',
            ],
        ];

        foreach ($faqs as $i => $faq) {
            EcommerceLogisticsSolutionsPage::create([
                'section' => 'faq',
                'item_key' => 'faq_' . ($i + 1),
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'sort_order' => $i + 2,
                'is_active' => true,
            ]);
        }
    }
}