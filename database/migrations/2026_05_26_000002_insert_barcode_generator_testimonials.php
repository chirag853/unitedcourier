<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $testimonials = [
            [
                'customer_name' => 'Shelly Kapoor',
                'content' => 'Very reliable logistics partner with consistent performance, ensuring all my shipments arrive safely, securely, and always on time without any issues.',
                'customer_designation' => null,
                'customer_image' => 'website_images/review-1.png',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 1,
                'page' => 'barcode-generator',
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
                'page' => 'barcode-generator',
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
                'page' => 'barcode-generator',
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
                'page' => 'barcode-generator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Karan Singh',
                'content' => 'Professional and efficient team handled my bulk shipments perfectly, ensuring timely delivery, careful handling, and a completely hassle-free logistics experience',
                'customer_designation' => null,
                'customer_image' => 'website_images/review-5.png',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 5,
                'page' => 'barcode-generator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('testimonials')->insert($testimonials);
    }

    public function down(): void
    {
        DB::table('testimonials')->where('page', 'barcode-generator')->delete();
    }
};