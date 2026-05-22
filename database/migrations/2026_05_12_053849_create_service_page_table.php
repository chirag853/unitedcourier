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
        Schema::create('service_page', function (Blueprint $table) {
            $table->id();
            $table->string('section', 50);           // e.g., 'services', 'testimonials', 'faq', 'stats', 'partners', 'contact', 'settings'
            $table->string('item_key', 100);        // unique identifier within section (e.g., 'service_1', 'faq_how_to_start')
            $table->json('content');                 // all fields stored as JSON (title, description, image, rating, etc.)
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['section', 'item_key']);
            $table->index(['section', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_page');
    }
};
