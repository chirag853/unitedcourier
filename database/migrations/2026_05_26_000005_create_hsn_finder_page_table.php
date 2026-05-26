<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hsn_finder_page', function (Blueprint $table) {
            $table->id();
            $table->string('section_type', 50);
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->text('icon_svg')->nullable();
            $table->string('link')->nullable();
            $table->integer('display_order')->default(0);
            $table->string('page_badge_text')->nullable();
            $table->string('page_button_text')->nullable();
            $table->string('page_tag')->nullable();
            $table->string('page_label')->nullable();
            $table->string('page_placeholder')->nullable();
            $table->string('page_icon_class')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Insert seed data
        $sections = [
            // Hero section
            [
                'section_type' => 'hero',
                'title' => 'HSN Classification <span class="moving-gradient-text"> Made Simple</span>',
                'description' => 'Want to know a product\'s Indian HSN or US HTS code in a snap? Just upload or take a photo.',
                'page_badge_text' => 'HSN Finder',
                'page_button_text' => 'Capture Again',
                'page_icon_class' => 'fa-solid fa-camera',
                'display_order' => 1,
                'status' => true,
            ],
            // Features heading
            [
                'section_type' => 'features_heading',
                'title' => 'How It Works?',
                'description' => 'Three simple steps to get accurate HSN and HTS codes for your products',
                'display_order' => 2,
                'status' => true,
            ],
            // Feature 1 - Upload Image
            [
                'section_type' => 'features',
                'title' => 'Upload Image',
                'description' => 'Snap a photo or upload an existing image in JPG, PNG, or WEBP format with a maximum file size of 15MB.',
                'page_icon_class' => 'fa-solid fa-image',
                'display_order' => 3,
                'status' => true,
            ],
            // Feature 2 - AI Analysis
            [
                'section_type' => 'features',
                'title' => 'AI Analysis',
                'description' => 'Our smart AI analyzes your product, checks materials, structure, features, and other important details for accurate classification.',
                'page_icon_class' => 'fa-solid fa-robot',
                'display_order' => 4,
                'status' => true,
            ],
            // Feature 3 - Get Results
            [
                'section_type' => 'features',
                'title' => 'Get Results',
                'description' => 'Instantly receive the HSN & HTS codes with confidence scores and additional insights for complex or sensitive products.',
                'page_icon_class' => 'fa-solid fa-file-circle-check',
                'display_order' => 5,
                'status' => true,
            ],
        ];

        $createdAt = now();
        foreach ($sections as $section) {
            $section['created_at'] = $createdAt;
            $section['updated_at'] = $createdAt;
            DB::table('hsn_finder_page')->insert($section);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hsn_finder_page');
    }
};