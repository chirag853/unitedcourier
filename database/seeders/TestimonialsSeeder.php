<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            // Home page testimonials
            ['customer_name' => 'Shelly Kapoor', 'content' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.', 'customer_designation' => null, 'customer_image' => 'public/website_images/review-1.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 1, 'page' => 'home'],
            ['customer_name' => 'Vansh Agarwal', 'content' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.', 'customer_designation' => null, 'customer_image' => 'public/website_images/review-2.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 2, 'page' => 'home'],
            ['customer_name' => 'Rahul Mehta', 'content' => 'Best courier service for international deliveries, offering smooth documentation, fast processing, and quick dispatch with no delays or complications.', 'customer_designation' => null, 'customer_image' => 'public/website_images/review-3.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 3, 'page' => 'home'],
            ['customer_name' => 'Anjali Sharma', 'content' => 'Affordable pricing combined with premium quality service makes United Worldwide Couriers a highly recommended choice for all shipping and logistics needs.', 'customer_designation' => null, 'customer_image' => 'public/website_images/review-4.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 4, 'page' => 'home'],
            ['customer_name' => 'Karan Singh', 'content' => 'Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience.', 'customer_designation' => null, 'customer_image' => 'public/website_images/review-5.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 5, 'page' => 'home'],
            ['customer_name' => 'Vinay Verma', 'content' => 'United Worldwide Couriers delivered my international parcel quickly and safely, with smooth handling and excellent service throughout the entire process.', 'customer_designation' => null, 'customer_image' => 'public/website_images/review-6.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 6, 'page' => 'home'],

            // About page testimonials
            ['customer_name' => 'Priya Patel', 'content' => 'United Worldwide Couriers has been our trusted logistics partner for years. Their professionalism and attention to detail are unmatched in the industry.', 'customer_designation' => 'CEO, ExportHub India', 'customer_image' => 'public/website_images/review-1.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 1, 'page' => 'about'],
            ['customer_name' => 'Arun Kumar', 'content' => 'The team at United Worldwide Couriers goes above and beyond to ensure customer satisfaction. Their global network is truly impressive.', 'customer_designation' => 'Operations Head, TradeLink Inc.', 'customer_image' => 'public/website_images/review-2.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 2, 'page' => 'about'],
            ['customer_name' => 'Meera Joshi', 'content' => 'I appreciate their transparent communication and reliable service. They make international shipping stress-free and efficient.', 'customer_designation' => 'Founder, ArtisanCrafts Co.', 'customer_image' => 'public/website_images/review-3.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 3, 'page' => 'about'],

            // Service page testimonials
            ['customer_name' => 'Rajesh Khanna', 'content' => 'Outstanding freight forwarding services! United Worldwide Couriers handled our bulk shipment with utmost care and delivered it ahead of schedule.', 'customer_designation' => 'Supply Chain Manager, MegaCorp Ltd.', 'customer_image' => 'public/website_images/review-4.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 1, 'page' => 'service'],
            ['customer_name' => 'Neha Gupta', 'content' => 'Their express delivery service is incredibly fast. I received my urgent documents within 24 hours across international borders. Highly recommended!', 'customer_designation' => 'Legal Advisor, GlobalLaw Partners', 'customer_image' => 'public/website_images/review-5.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 2, 'page' => 'service'],
            ['customer_name' => 'Vikram Singh', 'content' => 'The warehousing and distribution solutions provided by United Worldwide Couriers are top-notch. They manage our inventory with great precision.', 'customer_designation' => 'Warehouse Director, RetailMax Group', 'customer_image' => 'public/website_images/review-6.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 3, 'page' => 'service'],

            // Volumetric calculator page testimonials
            ['customer_name' => 'Sneha Reddy', 'content' => 'The volumetric calculator is a game-changer! It helped me estimate shipping costs instantly and plan my budget accordingly. Fantastic tool!', 'customer_designation' => 'E-commerce Entrepreneur', 'customer_image' => 'public/website_images/review-1.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 1, 'page' => 'volumetric-calculator'],
            ['customer_name' => 'Amit Desai', 'content' => 'Very user-friendly interface and accurate calculations. This tool saves me hours of manual work. United Worldwide Couriers truly understands customer needs.', 'customer_designation' => 'Freight Broker, ShipWise Inc.', 'customer_image' => 'public/website_images/review-2.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 2, 'page' => 'volumetric-calculator'],
            ['customer_name' => 'Deepa Nair', 'content' => 'I use the volumetric calculator regularly for my export business. It is reliable, fast, and gives me confidence in my shipping cost projections.', 'customer_designation' => 'Export Manager, SpiceTrade Ltd.', 'customer_image' => 'public/website_images/review-3.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 3, 'page' => 'volumetric-calculator'],

            // Network page testimonials
            ['customer_name' => 'Rohit Sharma', 'content' => 'The global network of United Worldwide Couriers is incredibly extensive. They have offices in every major city, making international logistics seamless.', 'customer_designation' => 'Director, GlobalTrade Solutions', 'customer_image' => 'public/website_images/review-4.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 1, 'page' => 'network'],
            ['customer_name' => 'Pooja Mehta', 'content' => 'Their India and overseas network coverage is outstanding. No matter where I need to ship, United Worldwide Couriers has a presence there.', 'customer_designation' => 'Logistics Coordinator, FreshFarms Export', 'customer_image' => 'public/website_images/review-5.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 2, 'page' => 'network'],

            // Warehousing page testimonials
            ['customer_name' => 'Ankit Verma', 'content' => 'United Worldwide Couriers transformed our warehousing operations with their state-of-the-art facilities and professional management team.', 'customer_designation' => 'Operations Director, MegaStorage Pvt. Ltd.', 'customer_image' => 'public/website_images/review-6.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 1, 'page' => 'warehousing'],
            ['customer_name' => 'Sonia Kapoor', 'content' => 'Their warehousing solutions are secure, organized, and cost-effective. We have been storing our inventory with them for over a year now.', 'customer_designation' => 'Inventory Manager, ShopEase India', 'customer_image' => 'public/website_images/review-1.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 2, 'page' => 'warehousing'],
            ['customer_name' => 'Vivek Saxena', 'content' => 'The team provides exceptional support for all warehousing needs. From storage to distribution, everything is handled professionally.', 'customer_designation' => 'CEO, QuickShip Logistics', 'customer_image' => 'public/website_images/review-2.png', 'rating' => 5, 'is_active' => true, 'sort_order' => 3, 'page' => 'warehousing'],
        ];

        DB::table('testimonials')->insert($testimonials);
    }
}