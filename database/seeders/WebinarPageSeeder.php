<?php

namespace Database\Seeders;

use App\Models\WebinarPage;
use Illuminate\Database\Seeder;

class WebinarPageSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // 1. Hero Section
        // ──────────────────────────────────────────────
        WebinarPage::create([
            'section'    => 'hero',
            'item_key'   => 'hero_content',
            'title'      => null,
            'description' => null,
            'image'      => null,
            'link'       => null,
            'content'    => [
                'badge'       => 'Webinars',
                'title'       => 'Join Our Webinars Made <span class="moving-gradient-text">Just for Exporters.</span>',
                'description' => 'Explore upcoming live sessions and watch past webinars on international shipping, ecommerce logistics, and exporter growth.',
            ],
            'sort_order' => 1,
            'status'     => 'Active',
        ]);

        // ──────────────────────────────────────────────
        // 2. Webinar Cards (5 items)
        // ──────────────────────────────────────────────
        $webinars = [
            [
                'image'          => 'https://images.unsplash.com/photo-1591115765373-5207764f72e7?auto=format&fit=crop&q=80&w=800',
                'category_tag'   => 'Webinar',
                'read_time'      => '45 min',
                'title'          => 'Breaking Down Export Customs & Duties for Beginners',
                'link'           => '#',
                'link_text'      => 'Register Now',
                'author_name'    => 'Sanjay Negi',
                'author_role'    => 'Assoc Dir',
                'author_company' => 'United',
                'publish_date'   => 'May 20, 2026',
            ],
            [
                'image'          => 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&q=80&w=800',
                'category_tag'   => 'Live Webinar',
                'read_time'      => '60 min',
                'title'          => 'Scaling Your eCommerce Brand with International Fulfillment',
                'link'           => '#',
                'link_text'      => 'Register Now',
                'author_name'    => 'Sahil Bajaj',
                'author_role'    => 'Export Specialist',
                'author_company' => 'United',
                'publish_date'   => 'May 25, 2026',
            ],
            [
                'image'          => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=800',
                'category_tag'   => 'Expert Session',
                'read_time'      => '40 min',
                'title'          => 'Navigating Cross-Border Shipping Regulations in 2026',
                'link'           => '#',
                'link_text'      => 'Register Now',
                'author_name'    => 'Aman Verma',
                'author_role'    => 'Logistics Head',
                'author_company' => 'United',
                'publish_date'   => 'May 28, 2026',
            ],
            [
                'image'          => 'https://images.unsplash.com/photo-1552581234-26160f608093?auto=format&fit=crop&q=80&w=800',
                'category_tag'   => 'eCommerce Webinar',
                'read_time'      => '50 min',
                'title'          => 'D2C Brand Growth: From Local to Global Markets',
                'link'           => '#',
                'link_text'      => 'Register Now',
                'author_name'    => 'Priya Sharma',
                'author_role'    => 'eCommerce Lead',
                'author_company' => 'United',
                'publish_date'   => 'June 1, 2026',
            ],
            [
                'image'          => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?auto=format&fit=crop&q=80&w=800',
                'category_tag'   => 'B2B Webinar',
                'read_time'      => '35 min',
                'title'          => 'Exploring New Horizons: B2B Export Opportunities in Southeast Asia',
                'link'           => '#',
                'link_text'      => 'Register Now',
                'author_name'    => 'Rahul Mehta',
                'author_role'    => 'Market Analyst',
                'author_company' => 'United',
                'publish_date'   => 'May 22, 2026',
            ],
        ];

        foreach ($webinars as $index => $webinar) {
            WebinarPage::create([
                'section'    => null,
                'item_key'   => 'webinar_' . ($index + 1),
                'title'      => $webinar['title'],
                'description' => null,
                'image'      => $webinar['image'],
                'link'       => $webinar['link'],
                'content'    => [
                    'category_tag'   => $webinar['category_tag'],
                    'read_time'      => $webinar['read_time'],
                    'link_text'      => $webinar['link_text'],
                    'author_name'    => $webinar['author_name'],
                    'author_role'    => $webinar['author_role'],
                    'author_company' => $webinar['author_company'],
                    'publish_date'   => $webinar['publish_date'],
                ],
                'sort_order' => $index + 1,
                'status'     => 'Active',
            ]);
        }
    }
}