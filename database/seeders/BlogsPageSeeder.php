<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create blog categories
        $categories = [
            ['name' => 'Air Freight', 'slug' => 'air'],
            ['name' => 'E-commerce Logistics', 'slug' => 'ecommerce'],
            ['name' => 'Warehousing', 'slug' => 'warehousing'],
            ['name' => 'Dropshipping', 'slug' => 'dropshipping'],
            ['name' => 'B2B Shipments', 'slug' => 'b2b-shipments'],
        ];

        foreach ($categories as $cat) {
            BlogCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Look up category IDs dynamically
        $catAir = BlogCategory::where('slug', 'air')->first();
        $catEcommerce = BlogCategory::where('slug', 'ecommerce')->first();
        $catWarehousing = BlogCategory::where('slug', 'warehousing')->first();
        $catDropshipping = BlogCategory::where('slug', 'dropshipping')->first();
        $catB2b = BlogCategory::where('slug', 'b2b-shipments')->first();

        // Create blog posts
        $blogs = [
            [
                'blog_title' => 'How to Sell on Blinkit in Delhi: Blinkit Seller Registration Guide',
                'url_title' => 'how-to-sell-on-blinkit-delhi-seller-registration-guide',
                'slug' => 'how-to-sell-on-blinkit-delhi-seller-registration-guide',
                'category_id' => $catAir?->id,
                'sub_heading' => 'Your Complete Guide to Becoming a Blinkit Seller in Delhi',
                'sub_content' => 'Blinkit has revolutionized quick commerce in India. This guide walks you through everything you need to know about registering as a seller on Blinkit in Delhi, from documentation to logistics setup.',
                'blog_description' => '<p>Complete guide on how to register as a seller on Blinkit in Delhi and start selling your products. Learn about the registration process, required documents, pricing strategies, and delivery logistics to succeed on India\'s fastest growing quick commerce platform.</p><p>Blinkit (formerly Grofers) has become one of India\'s leading quick commerce platforms, offering delivery in 10 minutes. For sellers in Delhi, this presents a massive opportunity to reach customers at unprecedented speed.</p>',
                'master_image' => 'https://images.unsplash.com/photo-1566576721346-d4a3b4eaeb55?q=80&w=765&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'master_image_alt_text' => 'Blinkit seller registration process in Delhi',
                'image_alt' => 'Blinkit Delivery Boy',
                'social_title' => 'How to Sell on Blinkit in Delhi | Seller Registration Guide',
                'seo_meta_title' => 'How to Sell on Blinkit in Delhi: Complete Seller Registration Guide 2026',
                'meta_keyword' => 'Blinkit seller registration, sell on Blinkit Delhi, Blinkit seller guide, quick commerce Delhi',
                'meta_description' => 'Complete guide on how to register as a seller on Blinkit in Delhi. Learn about documentation, pricing, and logistics to start selling on India\'s fastest quick commerce platform.',
                'og_title' => 'How to Sell on Blinkit in Delhi: Step-by-Step Seller Guide',
                'og_url' => 'https://unitedwayshipping.com/blog/how-to-sell-on-blinkit-delhi-seller-registration-guide',
                'og_description' => 'Your complete guide to becoming a Blinkit seller in Delhi. Everything from registration to delivery logistics covered.',
                'og_image_url' => 'https://images.unsplash.com/photo-1566576721346-d4a3b4eaeb55?q=80&w=765&auto=format&fit=crop',
                'twitter_card' => 'summary_large_image',
                'is_trending' => 'Yes',
                'status' => 'Active',
                'author_name' => 'Sanjay Negi',
                'author_description' => 'Assoc Dir @ United',
                'author_image' => 'https://ui-avatars.com/api/?name=Sanjay+Negi&background=random',
                'feed' => 'Blinkit seller registration guide for Delhi entrepreneurs. Complete step-by-step process from documentation to going live.',
                'country_name' => 'India',
                'state_name' => 'Delhi',
                'city_name' => 'New Delhi',
            ],
            [
                'blog_title' => '16 Best SEO Automation Software for Higher Rankings in 2026',
                'url_title' => 'best-seo-automation-software-2026',
                'slug' => 'best-seo-automation-software-2026',
                'category_id' => $catEcommerce?->id,
                'sub_heading' => 'Top SEO Tools to Automate Your Way to the Top of Search Results',
                'sub_content' => 'SEO automation is no longer optional for businesses that want to stay competitive. Here are the 16 best tools that can help you automate keyword research, content optimization, link building, and reporting.',
                'blog_description' => '<p>Discover the top 16 SEO automation tools that can help you achieve higher search engine rankings in 2026. From keyword research to content optimization, these tools will streamline your SEO workflow.</p><p>In the rapidly evolving world of digital marketing, automation has become a cornerstone of successful SEO strategies. This comprehensive guide explores the best automation software available in 2026.</p>',
                'master_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800',
                'master_image_alt_text' => 'SEO automation software dashboard showing analytics',
                'image_alt' => 'SEO Automation Tools',
                'social_title' => '16 Best SEO Automation Software for Higher Rankings in 2026',
                'seo_meta_title' => '16 Best SEO Automation Software for Higher Rankings in 2026',
                'meta_keyword' => 'SEO automation, SEO tools, search engine optimization, automation software, digital marketing',
                'meta_description' => 'Discover the top 16 SEO automation tools that can help you achieve higher search engine rankings in 2026. Comprehensive guide with features and pricing.',
                'og_title' => '16 Best SEO Automation Software for Higher Rankings in 2026',
                'og_url' => 'https://unitedwayshipping.com/blog/best-seo-automation-software-2026',
                'og_description' => 'Top 16 SEO automation tools to boost your search rankings in 2026. Features, pricing, and comparison included.',
                'og_image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800',
                'twitter_card' => 'summary_large_image',
                'is_trending' => 'Yes',
                'status' => 'Active',
                'author_name' => 'Sahil Bajaj',
                'author_description' => 'Sr Specialist @ United',
                'author_image' => 'https://ui-avatars.com/api/?name=Sahil+Bajaj&background=random',
                'feed' => 'Discover 16 best SEO automation tools for 2026. Automate keyword research, content optimization, and link building.',
                'country_name' => 'India',
                'state_name' => null,
                'city_name' => null,
            ],
            [
                'blog_title' => 'How to Export Moringa Powder from India: Complete Logistics Guide',
                'url_title' => 'export-moringa-powder-from-india-logistics-guide',
                'slug' => 'export-moringa-powder-from-india-logistics-guide',
                'category_id' => $catWarehousing?->id,
                'sub_heading' => 'Everything You Need to Know About Moringa Export from India',
                'sub_content' => 'India is one of the largest producers of Moringa. This guide covers the entire export process from documentation, quality standards, packaging requirements to shipping and logistics.',
                'blog_description' => '<p>A comprehensive logistics guide on how to export moringa powder from India to international markets. Learn about documentation, quality standards, and shipping processes.</p><p>Moringa oleifera, often called the miracle tree, has gained immense popularity in global markets for its nutritional benefits. India, being a major producer, offers tremendous export opportunities.</p>',
                'master_image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&q=80&w=800',
                'master_image_alt_text' => 'Moringa powder export from India logistics',
                'image_alt' => 'Moringa Powder Export',
                'social_title' => 'How to Export Moringa Powder from India | Logistics Guide',
                'seo_meta_title' => 'How to Export Moringa Powder from India: Complete Logistics Guide 2026',
                'meta_keyword' => 'Moringa export India, moringa powder export, export logistics, spice export India, agricultural export',
                'meta_description' => 'Complete logistics guide on how to export moringa powder from India. Learn about documentation, packaging, shipping, and international trade requirements.',
                'og_title' => 'How to Export Moringa Powder from India: Step-by-Step Logistics Guide',
                'og_url' => 'https://unitedwayshipping.com/blog/export-moringa-powder-from-india-logistics-guide',
                'og_description' => 'Everything you need to know about exporting moringa powder from India. Documentation, quality standards, and shipping guide.',
                'og_image_url' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&q=80&w=800',
                'twitter_card' => 'summary',
                'is_trending' => 'No',
                'status' => 'Active',
                'author_name' => 'Sanjay Negi',
                'author_description' => 'Assoc Dir @ United',
                'author_image' => 'https://ui-avatars.com/api/?name=Sanjay+Negi&background=random',
                'feed' => 'Complete guide on exporting moringa powder from India. Documentation, packaging, shipping, and quality standards explained.',
                'country_name' => 'India',
                'state_name' => null,
                'city_name' => null,
            ],
            [
                'blog_title' => 'The Future of AI in Global Supply Chain Management 2026',
                'url_title' => 'future-of-ai-global-supply-chain-management-2026',
                'slug' => 'future-of-ai-global-supply-chain-management-2026',
                'category_id' => $catDropshipping?->id,
                'sub_heading' => 'How Artificial Intelligence is Revolutionizing Supply Chains Worldwide',
                'sub_content' => 'From predictive analytics to autonomous vehicles, AI is transforming every aspect of supply chain management. Explore the key trends and technologies shaping the future of global logistics.',
                'blog_description' => '<p>Explore how artificial intelligence is transforming global supply chain management in 2026 and beyond. From predictive analytics to autonomous vehicles, discover the technologies reshaping logistics.</p><p>The integration of AI in supply chain management has moved from experimental to essential. Companies leveraging AI are seeing dramatic improvements in efficiency, cost reduction, and customer satisfaction.</p>',
                'master_image' => 'https://plus.unsplash.com/premium_photo-1664297616681-81ae24954249?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'master_image_alt_text' => 'AI in global supply chain management',
                'image_alt' => 'AI Supply Chain',
                'social_title' => 'The Future of AI in Global Supply Chain Management 2026',
                'seo_meta_title' => 'The Future of AI in Global Supply Chain Management 2026',
                'meta_keyword' => 'AI supply chain, artificial intelligence logistics, supply chain management, AI in logistics, future of supply chain',
                'meta_description' => 'Explore how artificial intelligence is transforming global supply chain management in 2026. Trends, technologies, and predictions for AI in logistics.',
                'og_title' => 'The Future of AI in Global Supply Chain Management 2026',
                'og_url' => 'https://unitedwayshipping.com/blog/future-of-ai-global-supply-chain-management-2026',
                'og_description' => 'How AI is transforming global supply chain management. Key trends and technologies shaping the future of logistics.',
                'og_image_url' => 'https://plus.unsplash.com/premium_photo-1664297616681-81ae24954249?q=80&w=1170&auto=format&fit=crop',
                'twitter_card' => 'summary_large_image',
                'is_trending' => 'Yes',
                'status' => 'Active',
                'author_name' => 'Aman Verma',
                'author_description' => 'Logistics Expert @ UWD',
                'author_image' => 'https://ui-avatars.com/api/?name=Aman+Verma&background=random',
                'feed' => 'Explore how AI is transforming global supply chain management in 2026. Predictive analytics, automation, and more.',
                'country_name' => 'India',
                'state_name' => null,
                'city_name' => null,
            ],
            [
                'blog_title' => 'Social Media Strategies for B2B Export Businesses',
                'url_title' => 'social-media-strategies-b2b-export-businesses',
                'slug' => 'social-media-strategies-b2b-export-businesses',
                'category_id' => $catB2b?->id,
                'sub_heading' => 'Leverage Social Media to Grow Your B2B Export Business',
                'sub_content' => 'Social media isn\'t just for B2C companies. Learn how B2B export businesses can use LinkedIn, Instagram, and other platforms to connect with international buyers and build brand authority.',
                'blog_description' => '<p>Learn effective social media strategies to grow your B2B export business and reach international buyers. From LinkedIn networking to content marketing, discover platforms and tactics that deliver results.</p><p>In the B2B export space, social media has emerged as a powerful tool for lead generation, brand building, and market research. This guide explores proven strategies tailored specifically for export businesses.</p>',
                'master_image' => 'https://images.unsplash.com/photo-1557838923-2985c318be48?auto=format&fit=crop&q=80&w=800',
                'master_image_alt_text' => 'Social media strategies for B2B export businesses',
                'image_alt' => 'B2B Social Media',
                'social_title' => 'Social Media Strategies for B2B Export Businesses',
                'seo_meta_title' => 'Social Media Strategies for B2B Export Businesses | Complete Guide 2026',
                'meta_keyword' => 'B2B social media, export business marketing, LinkedIn for export, B2B lead generation, social media strategy',
                'meta_description' => 'Learn effective social media strategies to grow your B2B export business. LinkedIn, Instagram, and content marketing tactics for international buyers.',
                'og_title' => 'Social Media Strategies for B2B Export Businesses',
                'og_url' => 'https://unitedwayshipping.com/blog/social-media-strategies-b2b-export-businesses',
                'og_description' => 'Proven social media strategies for B2B export businesses. Connect with international buyers and build brand authority online.',
                'og_image_url' => 'https://images.unsplash.com/photo-1557838923-2985c318be48?auto=format&fit=crop&q=80&w=800',
                'twitter_card' => 'summary',
                'is_trending' => 'No',
                'status' => 'Active',
                'author_name' => 'Priya Sharma',
                'author_description' => 'Marketing Head @ UWD',
                'author_image' => 'https://ui-avatars.com/api/?name=Priya+Sharma&background=random',
                'feed' => 'Social media strategies for B2B export businesses. LinkedIn, Instagram, and content marketing to reach international buyers.',
                'country_name' => 'India',
                'state_name' => null,
                'city_name' => null,
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::firstOrCreate(['slug' => $blog['slug']], $blog);
        }
    }
}