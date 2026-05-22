<?php

namespace Database\Seeders;

use App\Models\PartnershipPage;
use Illuminate\Database\Seeder;

class PartnershipPageSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // 1. Hero Section
        // ──────────────────────────────────────────────
        PartnershipPage::create([
            'section'    => 'hero',
            'item_key'   => 'hero_content',
            'title'      => 'Be a Part of the Fastest <br class="d-none d-md-block"> <span class="moving-gradient-text">Growing Ecosystem</span>',
            'description' => 'Maximise your profits by accessing affordable international shipping, and a network that helps you reach customers in every major market.',
            'image'      => 'images/partnership.webp',
            'button_text' => 'Join Network',
            'button_url' => '#',
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 2. Logo Slider (5 logos)
        // ──────────────────────────────────────────────
        $logos = [
            ['title' => 'eBay',     'image' => 'images/ebay.webp'],
            ['title' => 'Etsy',     'image' => 'images/etsy.webp'],
            ['title' => 'Amazon',   'image' => 'images/amazon.webp'],
            ['title' => 'Shopify',  'image' => 'images/shopify.webp'],
            ['title' => 'Walmart',  'image' => 'images/walmart.webp'],
        ];

        foreach ($logos as $index => $logo) {
            PartnershipPage::create([
                'section'    => 'logos',
                'item_key'   => 'logo_' . ($index + 1),
                'title'      => $logo['title'],
                'image'      => $logo['image'],
                'sort_order' => $index + 1,
                'status'     => 'Active',
            ]);
        }

        // ──────────────────────────────────────────────
        // 3. Partner Form Section
        // ──────────────────────────────────────────────
        PartnershipPage::create([
            'section'    => 'partner_form',
            'item_key'   => 'form_content',
            'title'      => 'Partner with <span class="gradient-text">United Couriers</span>',
            'button_text' => 'Become a Partner',
            'extra_content' => json_encode([
                'first_name_placeholder' => 'First Name',
                'last_name_placeholder'  => 'Last Name',
                'email_placeholder'      => 'Email',
                'phone_placeholder'      => 'Phone',
                'company_placeholder'    => 'Company Name',
                'message_placeholder'   => 'Message',
            ]),
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 4. About Section (right column text)
        // ──────────────────────────────────────────────
        PartnershipPage::create([
            'section'    => 'about',
            'item_key'   => 'about_content',
            'title'      => 'Where trust and collaboration <br class="d-none d-md-block"> <span class="moving-gradient-text">create lasting partnerships</span>',
            'description' => 'With us, you&rsquo;re joining hands with a brand that values collaboration and long-term success. Together, let&rsquo;s create opportunities, expand reach, and build solutions to move businesses forward &ndash; across industries.',
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 5. Features (3 items)
        // ──────────────────────────────────────────────
        $features = [
            'Access over 190+ Countries',
            'Seamless API Integration',
            'Dedicated Partner Support',
        ];

        foreach ($features as $index => $feature) {
            PartnershipPage::create([
                'section'    => 'features',
                'item_key'   => 'feature_' . ($index + 1),
                'title'      => $feature,
                'sort_order' => $index + 1,
                'status'     => 'Active',
            ]);
        }

        // ──────────────────────────────────────────────
        // 6. Ecosystem Section (header before cards)
        // ──────────────────────────────────────────────
        PartnershipPage::create([
            'section'    => 'ecosystem',
            'item_key'   => 'ecosystem_content',
            'badge_text' => 'Our Export Ecosystem Partners',
            'title'      => 'Powering global commerce through strong, trusted partnerships',
            'description' => 'United Worldwide Couriers works with leading platforms, service providers, institutions, and logistics networks to simplify cross-border trade for Indian exporters and D2C brands. Together, we help businesses sell globally with confidence, speed, and scale.',
            'extra_content' => json_encode([
                'global_card_title' => 'Worldwide Marketplaces',
                'partner_card_title' => 'Our Partners',
            ]),
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 7. Ecosystem Global Cards (5 marketplace logos)
        // ──────────────────────────────────────────────
        $globalLogos = [
            'images/etsy.webp',
            'images/ebay.webp',
            'images/shopify.webp',
            'images/amazon.webp',
            'images/walmart.webp',
        ];

        foreach ($globalLogos as $index => $logo) {
            PartnershipPage::create([
                'section'    => 'ecosystem_global',
                'item_key'   => 'ecosystem_global_' . ($index + 1),
                'image'      => $logo,
                'sort_order' => $index + 1,
                'status'     => 'Active',
            ]);
        }

        // ──────────────────────────────────────────────
        // 8. Ecosystem Partner Cards (5 external partner URLs)
        // ──────────────────────────────────────────────
        $partnerLogos = [
            ['title' => 'TNT',    'image' => 'https://www.tnt.com/dam/tnt_express_media/en_gb/images/ChoosingTNT/TNT-Logo-edt.png'],
            ['title' => 'FedEx',  'image' => 'https://1000logos.net/wp-content/uploads/2021/04/Fedex-logo.png'],
            ['title' => 'UPS',    'image' => 'https://www.ups.com/webassets/icons/logo.svg'],
            ['title' => 'USPS',   'image' => 'https://www.usps.com/global-elements/header/images/utility-header/logo-sb.svg'],
            ['title' => 'Aramex', 'image' => 'https://dotcomaramexprod.blob.core.windows.net/default/docs/default-source/logo/aramex-logo-english.webp'],
        ];

        foreach ($partnerLogos as $index => $partner) {
            PartnershipPage::create([
                'section'    => 'ecosystem_partner',
                'item_key'   => 'ecosystem_partner_' . ($index + 1),
                'title'      => $partner['title'],
                'image'      => $partner['image'],
                'sort_order' => $index + 1,
                'status'     => 'Active',
            ]);
        }

        // ──────────────────────────────────────────────
        // 9. FAQ Section (header, sidebar, contact box)
        // ──────────────────────────────────────────────
        PartnershipPage::create([
            'section'    => 'faq',
            'item_key'   => 'faq_content',
            'badge_text' => 'Common Questions',
            'title'      => 'Why Partner with us?',
            'description' => 'Join India&rsquo;s leading logistics brand, where trust and shared success drive lasting partnerships as we empower Indian exporters to go global.',
            'extra_content' => json_encode([
                'sidebar_image'       => 'https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif',
                'sidebar_title'       => 'Need personalized help?',
                'sidebar_description' => 'Our logistics experts are available 24/7 to assist your requirements.',
                'contact_box_title'   => 'Contact Us',
                'contact_box_description' => 'For urgent inquiries regarding your current shipment status.',
                'contact_box_button'  => 'Message Support',
            ]),
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 10. FAQ Items (11 accordion questions)
        // ──────────────────────────────────────────────
        $faqItems = [
            [
                'question' => 'How can partnering with us help grow your brand?',
                'answer'   => 'Grow your brand and build customer trust through reliable logistics solutions, consistent delivery performance, and a strong global presence.',
            ],
            [
                'question' => 'What global reach do our partnership services offer?',
                'answer'   => 'Our platform connects you to 220+ countries, enabling seamless international shipping and expanding your business footprint worldwide.',
            ],
            [
                'question' => 'How strong is our partner network?',
                'answer'   => 'Join a trusted network of 25K+ exporters, built to support business growth, collaboration, and long-term success.',
            ],
            [
                'question' => 'What exclusive benefits do partners receive?',
                'answer'   => 'Partners get exclusive access to webinars, training sessions, and growth opportunities designed to enhance business capabilities and market reach.',
            ],
            [
                'question' => 'How do we support your business growth through logistics?',
                'answer'   => 'We simplify logistics operations while helping you build growth and trust for your business through efficient, reliable, and scalable solutions.',
            ],
            [
                'question' => 'How easy is it to start a partnership with us?',
                'answer'   => 'Getting started is simple&mdash;register with us, connect with our team, and begin accessing our logistics network and partner benefits quickly.',
            ],
            [
                'question' => 'Do partners receive dedicated support?',
                'answer'   => 'Yes, our partners receive dedicated support to ensure smooth operations, quick issue resolution, and consistent business growth.',
            ],
            [
                'question' => 'Can partnerships help expand into new markets?',
                'answer'   => 'Absolutely. Our global network and logistics expertise make it easier for partners to enter and succeed in new international markets.',
            ],
            [
                'question' => 'What makes our partnership model reliable?',
                'answer'   => 'Our combination of strong network, advanced systems, and strict quality control ensures reliable and consistent logistics services for all partners.',
            ],
            [
                'question' => 'Do partners get access to learning and development resources?',
                'answer'   => 'Yes, we provide ongoing learning opportunities including webinars, training modules, and expert sessions to help partners grow continuously.',
            ],
            [
                'question' => 'How does our platform build long-term trust for partners?',
                'answer'   => 'By ensuring transparent processes, reliable delivery, and consistent service quality, we help partners build long-term trust with their customers.',
            ],
        ];

        foreach ($faqItems as $index => $faq) {
            PartnershipPage::create([
                'section'    => 'faq_item',
                'item_key'   => 'faq_' . ($index + 1),
                'title'      => $faq['question'],
                'description' => $faq['answer'],
                'sort_order' => $index + 1,
                'status'     => 'Active',
            ]);
        }
    }
}