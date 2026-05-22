<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add page content columns to ebook_page table
        Schema::table('ebook_page', function (Blueprint $table) {
            $table->string('section', 50)->nullable()->after('id');
            $table->string('item_key', 100)->nullable()->after('section');
            $table->json('content')->nullable()->after('link');
        });

        // Drop the separate ebook_page_content table
        Schema::dropIfExists('ebook_page_content');
    }

    public function down(): void
    {
        // Recreate ebook_page_content table
        Schema::create('ebook_page_content', function (Blueprint $table) {
            $table->id();
            $table->string('section', 50);
            $table->string('item_key', 100)->nullable();
            $table->json('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['section', 'item_key'], 'ebook_page_content_section_item_key_unique');
            $table->index(['section', 'is_active', 'sort_order'], 'ebook_page_content_section_active_sort_idx');
        });

        // Remove page content columns from ebook_page
        Schema::table('ebook_page', function (Blueprint $table) {
            $table->dropColumn(['section', 'item_key', 'content']);
        });
    }
};