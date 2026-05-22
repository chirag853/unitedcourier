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
        Schema::create('terms_and_condition_page', function (Blueprint $table) {
            $table->id();
            $table->string('section_key', 100)->unique();
            $table->string('title')->nullable();
            $table->text('paragraphs')->nullable();
            $table->json('list_items')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('effective_date', 50)->nullable();
            $table->string('footer_heading')->nullable();
            $table->string('footer_email')->nullable();
            $table->timestamps();
            
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_and_condition_page');
    }
};
