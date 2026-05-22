<?php

namespace Database\Seeders;

use App\Models\Ebook;
use Illuminate\Database\Seeder;

class EbookPageSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // A. Page Content (hero, section header, FAQ)
        // ──────────────────────────────────────────────

        // 1. Hero Section
        Ebook::create([
            'section'    => 'hero',
            'item_key'   => 'hero_content',
            'title'      => null,
            'description' => null,
            'image'      => null,
            'link'       => null,
            'content'    => [
                'badge_text' => 'Read our ebooks',
                'title'      => 'eBooks for  <span class="moving-gradient-text">Exporters</span>',
                'subtitle'   => 'Must-read guides, handpicked for their popularity among global exporters',
                'image'      => 'images/e-books.webp',
            ],
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // 2. Section Header (above e-book feature blocks)
        Ebook::create([
            'section'    => 'section_header',
            'item_key'   => 'section_header_content',
            'title'      => null,
            'description' => null,
            'image'      => null,
            'link'       => null,
            'content'    => [
                'title'       => 'Grow your knowledge to grow your business',
                'description' => 'EGet your hands on our eBooks and learn about everything required to grow your business. Be it marketing, sales, logistics, or social media. Access the A to Z guides for guaranteed business growth.. Join our growing network of satisfied clients who depend on us for easy, secure, fast, and efficient logistics solutions. More than three decades (30+ years) of our positive track record speaks for itself.',
            ],
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // 3. FAQ Header
        Ebook::create([
            'section'    => 'faq',
            'item_key'   => 'faq_header',
            'title'      => null,
            'description' => null,
            'image'      => null,
            'link'       => null,
            'content'    => [
                'badge'                  => 'Common Questions',
                'title'                  => 'Frequently Asked Questions',
                'sidebar_image'          => 'https://i.pinimg.com/originals/f6/5d/46/f65d4681649d85bc91c86872a1775919.gif',
                'sidebar_title'          => 'Need personalized help?',
                'sidebar_description'    => 'Our logistics experts are available 24/7 to assist your requirements.',
                'contact_box_title'      => 'Contact Us',
                'contact_box_description'=> 'For urgent inquiries regarding your current shipment status.',
                'contact_button_text'    => 'Message Support',
            ],
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // 4. FAQ Items (11 questions)
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
            Ebook::create([
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

        // ──────────────────────────────────────────────
        // B. E-book Items (the actual downloadable e-books)
        // ──────────────────────────────────────────────
        $ebooks = [
            [
                'section'     => null,
                'item_key'    => null,
                'title'       => 'Cross-Border trade & expansion opportunities',
                'description' => 'EThis eBook takes you on a tour of the current export landscape of India. Know about the global expansion opportunities for your eCommerce business.',
                'image'       => 'images/book-1.webp',
                'link'        => null,
                'content'     => null,
                'sort_order'  => 1,
                'status'      => 'Active',
            ],
            [
                'section'     => null,
                'item_key'    => null,
                'title'       => 'Get started with hyperlocal delivery',
                'description' => 'The pandemic brought about a drastic shift in the buying process. People now shop online even for basic requirements. Learn about hyperlocal deliveries and how you can start with them.',
                'image'       => 'images/book-3.webp',
                'link'        => null,
                'content'     => null,
                'sort_order'  => 2,
                'status'      => 'Active',
            ],
            [
                'section'     => null,
                'item_key'    => null,
                'title'       => 'Master strategies for eCommerce business management',
                'description' => 'Get your hands on exclusive techniques and expert ideas about how to manage your eCommerce business efficiently.',
                'image'       => 'images/book-2.webp',
                'link'        => null,
                'content'     => null,
                'sort_order'  => 3,
                'status'      => 'Active',
            ],
        ];

        foreach ($ebooks as $ebook) {
            Ebook::create($ebook);
        }
    }
}