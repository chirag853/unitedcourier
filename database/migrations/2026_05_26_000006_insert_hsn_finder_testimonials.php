<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $testimonials = [
            [
                'page' => 'hsn-finder',
                'customer_name' => 'Ravi Kumar',
                'customer_image' => null,
                'content' => 'This HSN finder tool is a lifesaver! I used to spend hours classifying products for export. Now I get accurate codes in seconds.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'hsn-finder',
                'customer_name' => 'Priya Sharma',
                'customer_image' => null,
                'content' => 'The AI-powered analysis is incredibly accurate. It correctly identified the HSN codes for all my product samples. Highly recommended for importers.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'hsn-finder',
                'customer_name' => 'Amit Patel',
                'customer_image' => null,
                'content' => 'As a customs broker, this tool has transformed my workflow. The confidence scores and detailed classification insights give me peace of mind.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'hsn-finder',
                'customer_name' => 'Neha Gupta',
                'customer_image' => null,
                'content' => 'Love the simplicity! Just upload a photo and get instant results. The HSN and HTS code suggestions are spot on for my e-commerce business.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'hsn-finder',
                'customer_name' => 'Vikram Singh',
                'customer_image' => null,
                'content' => 'Finally a tool that understands international trade classifications. The US HTS codes are just as accurate as the Indian HSN codes. Brilliant!',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('testimonials')->insert($testimonials);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('testimonials')->where('page', 'hsn-finder')->delete();
    }
};