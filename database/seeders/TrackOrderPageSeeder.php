<?php

namespace Database\Seeders;

use App\Models\TrackOrderPage;
use Illuminate\Database\Seeder;

class TrackOrderPageSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // 1. Hero Section
        // ──────────────────────────────────────────────
        TrackOrderPage::create([
            'section'    => 'hero',
            'item_key'   => 'hero_content',
            'title'      => null,
            'description' => null,
            'image'      => 'images/tracking.webp',
            'link'       => null,
            'content'    => [
                'badge_text'  => 'Free Tool · Instant Results',
                'title'       => 'Track Your <span class="moving-gradient-text">Orders Easily</span>',
                'subtitle'    => 'Just enter your Mobile Number, AWB tracking number or Order ID & it\'s done.',
                'button_text' => 'Track Now',
            ],
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 2. Track Form Section
        // ──────────────────────────────────────────────
        TrackOrderPage::create([
            'section'    => 'track_form',
            'item_key'   => 'track_form_content',
            'title'      => null,
            'description' => null,
            'image'      => null,
            'link'       => null,
            'content'    => [
                'title'              => 'Track <span class="gradient-text">Now</span>',
                'button_text'        => 'Track Now',
                'field_1_label'      => 'AWB Number',
                'field_1_placeholder'=> 'Airway Bill Number',
                'field_2_label'      => 'Order Id',
                'field_2_placeholder'=> 'eg: 983434599',
                'field_3_label'      => 'Phone Number',
                'field_3_placeholder'=> '+91 9876543210',
            ],
            'sort_order' => 2,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 3. Features Header
        // ──────────────────────────────────────────────
        TrackOrderPage::create([
            'section'    => 'features',
            'item_key'   => 'features_header',
            'title'      => null,
            'description' => null,
            'image'      => null,
            'link'       => null,
            'content'    => [
                'title'       => 'What\'s your order status?',
                'description' => 'Carriers use dimensional weight to price large, light packages — here\'s what you need to know.',
            ],
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 4. Features Cards (4 items)
        // ──────────────────────────────────────────────
        $features = [
            [
                'icon_color'      => 'blue',
                'icon'            => 'fa-solid fa-check-to-slot',
                'icon_color_code' => '#2563eb',
                'title'           => 'Order Placed',
                'description'     => 'Your order has been successfully placed and is being processed by the seller.',
            ],
            [
                'icon_color'      => 'orange',
                'icon'            => 'fa-solid fa-box-open',
                'icon_color_code' => '#f59e0b',
                'title'           => 'Order Confirmed',
                'description'     => 'Seller has confirmed your order and it\'s being prepared for shipment.',
            ],
            [
                'icon_color'      => 'green',
                'icon'            => 'fa-solid fa-truck',
                'icon_color_code' => '#10b981',
                'title'           => 'In Transit',
                'description'     => 'Your package is on its way and moving through the delivery network.',
            ],
            [
                'icon_color'      => 'purple',
                'icon'            => 'fa-solid fa-circle-check',
                'icon_color_code' => '#8b5cf6',
                'title'           => 'Delivered',
                'description'     => 'Your package has been delivered successfully. Thank you for your patience!',
            ],
        ];

        foreach ($features as $index => $feature) {
            TrackOrderPage::create([
                'section'    => 'features',
                'item_key'   => 'feature_' . ($index + 1),
                'title'      => null,
                'description' => null,
                'image'      => null,
                'link'       => null,
                'content'    => $feature,
                'sort_order' => $index + 2,
                'status'     => 'Active',
            ]);
        }

        // ──────────────────────────────────────────────
        // 5. About / Optimize Section
        // ──────────────────────────────────────────────
        TrackOrderPage::create([
            'section'    => 'about',
            'item_key'   => 'about_content',
            'title'      => null,
            'description' => 'Always stay informed about your shipments, regardless of your courier partner',
            'image'      => null,
            'link'       => 'contact-us.php',
            'content'    => [
                'title'       => 'Optimize your order <span class="gradient-text">tracking experience</span>',
                'description' => 'Always stay informed about your shipments, regardless of your courier partner',
                'button_text' => 'Need help?',
                'button_link' => 'contact-us.php',
                'video_url'   => 'https://www.youtube.com/embed/tOvpjmnh5h4?si=-O5MSnO7OXm2Wspk',
                'checklist'   => [
                    'Real-time tracking updates across all major carriers',
                    'End-to-end visibility from pickup to delivery',
                ],
            ],
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 6. CTA Section
        // ──────────────────────────────────────────────
        TrackOrderPage::create([
            'section'    => 'cta',
            'item_key'   => 'cta_content',
            'title'      => null,
            'description' => 'Get live updates across all major carriers — end-to-end visibility from pickup to delivery for every order you export worldwide.',
            'image'      => null,
            'link'       => '#',
            'content'    => [
                'badge_text'  => 'Get in Touch',
                'title'       => 'Need any help related order??',
                'description' => 'Get live updates across all major carriers — end-to-end visibility from pickup to delivery for every order you export worldwide.',
                'button_text' => 'Contact Us Now →',
                'button_link' => '#',
            ],
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 7. FAQ Header
        // ──────────────────────────────────────────────
        TrackOrderPage::create([
            'section'    => 'faq',
            'item_key'   => 'faq_header',
            'title'      => null,
            'description' => null,
            'image'      => 'https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif',
            'link'       => null,
            'content'    => [
                'badge_text'           => 'Common Questions',
                'title'                => 'Frequently Asked Questions',
                'sidebar_title'        => 'Need personalized help?',
                'sidebar_description'  => 'Our logistics experts are available 24/7 to assist your requirements.',
                'contact_title'        => 'Contact Us',
                'contact_description'  => 'For urgent inquiries regarding your current shipment status.',
                'contact_button'       => 'Message Support',
            ],
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 8. FAQ Items (11 questions)
        // ──────────────────────────────────────────────
        $faqs = [
            [
                'question' => 'How do I get started?',
                'answer'   => 'To connect with our team, you have to register yourself, get a quote, and schedule your first pickup. Thereafter, the team will guide you through every step of the process.',
            ],
            [
                'question' => 'How does United Worldwide Couriers meet your shipping and logistics needs?',
                'answer'   => 'To provide the best freight management solutions, we work with broad strategies, technologies, and services to simplify the planning, storage, and movement of goods.',
            ],
            [
                'question' => 'What packaging standards should we follow for shipping?',
                'answer'   => 'We utilize study and secure packaging for small to large packages to protect your goods during transit. In case of fragile items, they will be cushioned enough and clearly labelled as "Fragile". In addition, we also provide a packaging and labelling guide for all new on boarders for no confusion and faster results.',
            ],
            [
                'question' => 'How do we calculate cost?',
                'answer'   => 'The exact shipping cost will be calculated based on your goods\' weight, dimensions, destinations, where it is expected to be delivered, and the delivery speed. If you are interested in knowing the accurate pricing, you are welcome to connect with the team.',
            ],
            [
                'question' => 'Will I be notified about my shipment status?',
                'answer'   => 'Yes. To keep the clients informed, our team provides regular updates via email or SMS throughout the shipping process.',
            ],
            [
                'question' => 'Does United Worldwide Couriers handle bulk or commercial shipments?',
                'answer'   => 'Yes, we specialize in managing bulk as well as commercial consignments with easy-flowing coordination, dedicated handling, secure transit, and timely delivery. This remains the same for both small and large volumes.',
            ],
            [
                'question' => 'Can I schedule a pickup for my shipment?',
                'answer'   => 'Yes, you can easily schedule a pick up at your preferred time either by reaching out to our team online (via our website) or by directly contacting our customer support.',
            ],
            [
                'question' => 'How can I track my shipment?',
                'answer'   => 'Through our online tracking system, you can track your shipment in real-time. For that, you just have to enter the tracking ID provided on our website dashboard to get live updates.',
            ],
            [
                'question' => 'Will my package be picked up by the United Worldwide Couriers team only?',
                'answer'   => 'It depends on the service and location. So, your shipment may be picked up either by our in-house delivery team or by third-party courier partners. On the contrary, we ensure strict quality control and safe handling for whatever the case may be.',
            ],
            [
                'question' => 'Do you provide customs clearance support?',
                'answer'   => 'Yes, while others face limited support, we complete international customs documentation and clearance faster. This way, we support rapid shipping without delays.',
            ],
            [
                'question' => 'What happens if my shipment is delayed or stuck?',
                'answer'   => 'First things first, the team proactively monitors the shipment to identify and resolve potential issues before they escalate. Still, if an issue occurs, the internal team coordinates with the partners to resolve the situation quickly.',
            ],
        ];

        foreach ($faqs as $index => $faq) {
            TrackOrderPage::create([
                'section'    => 'faq',
                'item_key'   => 'faq_item_' . ($index + 1),
                'title'      => null,
                'description' => null,
                'image'      => null,
                'link'       => null,
                'content'    => $faq,
                'sort_order' => $index + 2,
                'status'     => 'Active',
            ]);
        }
    }
}