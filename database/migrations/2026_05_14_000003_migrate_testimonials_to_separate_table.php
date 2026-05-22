<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, insert testimonials from home_page into the new testimonials table
        $testimonialsData = [
            [
                'customer_name' => 'Shelly Kapoor',
                'content' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
                'customer_designation' => null,
                'customer_image' => 'website_images/review-1.png',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 1,
                'page' => 'home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Vansh Agarwal',
                'content' => 'Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance.',
                'customer_designation' => null,
                'customer_image' => 'website_images/review-2.png',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 2,
                'page' => 'home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Rahul Mehta',
                'content' => 'Best courier service for international deliveries, offering smooth documentation, fast processing, and quick dispatch with no delays or complications.',
                'customer_designation' => null,
                'customer_image' => 'website_images/review-3.png',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 3,
                'page' => 'home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Anjali Sharma',
                'content' => 'Affordable pricing combined with premium quality service makes United Worldwide Couriers a highly recommended choice for all shipping and logistics needs.',
                'customer_designation' => null,
                'customer_image' => 'website_images/review-4.png',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 4,
                'page' => 'home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Karan Singh',
                'content' => 'Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience.',
                'customer_designation' => null,
                'customer_image' => 'website_images/review-5.png',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 5,
                'page' => 'home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Vinay Verma',
                'content' => 'United Worldwide Couriers delivered my international parcel quickly and safely, with smooth handling and excellent service throughout the entire process.',
                'customer_designation' => null,
                'customer_image' => 'website_images/review-6.png',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 6,
                'page' => 'home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('testimonials')->insert($testimonialsData);

        // Now remove testimonials from home_page table
        DB::table('home_page')
            ->where('section', 'testimonial')
            ->delete();
    }

    public function down()
    {
        // Rollback: remove from testimonials table
        DB::table('testimonials')->where('page', 'home')->delete();

        // Restore testimonials to home_page table (this would need the original data)
        // For simplicity, we're not restoring the exact original structure
        // as it was complex with multiple rows per testimonial
    }
};
