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
        Schema::create('contact_us_page', function (Blueprint $table) {
            $table->id();
            $table->string('section_key', 100)->unique();
            $table->string('title')->nullable();
            $table->text('paragraphs')->nullable();
            $table->json('list_items')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('phone_numbers')->nullable();
            $table->json('email_addresses')->nullable();
            $table->text('address')->nullable();
            $table->json('social_links')->nullable();
            $table->text('map_embed_url')->nullable();
            $table->timestamps();
            
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_us_page');
    }
};
