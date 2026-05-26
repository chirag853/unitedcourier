<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('testimonials')->insert([
            [
                'page' => 'shipping-rate-calculator',
                'customer_name' => 'Shelly Kapoor',
                'customer_image' => 'website_images/review-1.png',
                'content' => '"Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues."',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'shipping-rate-calculator',
                'customer_name' => 'Vansh Agarwal',
                'customer_image' => 'website_images/review-2.png',
                'content' => '"Customer support team is highly responsive and helpful, assisting me in tracking my urgent shipment with accurate real-time updates and guidance."',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'shipping-rate-calculator',
                'customer_name' => 'Rahul Mehta',
                'customer_image' => 'website_images/review-3.png',
                'content' => '"Best courier service for international deliveries, offering smooth documentation, fast processing, and quick dispatch with no delays or complications."',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'shipping-rate-calculator',
                'customer_name' => 'Anjali Sharma',
                'customer_image' => 'website_images/review-4.png',
                'content' => '"Affordable pricing combined with premium quality service makes United Worldwide Couriers a highly recommended choice for all shipping and logistics needs."',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'shipping-rate-calculator',
                'customer_name' => 'Karan Singh',
                'customer_image' => 'website_images/review-5.png',
                'content' => '"Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience"',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('testimonials')->where('page', 'shipping-rate-calculator')->delete();
    }
};