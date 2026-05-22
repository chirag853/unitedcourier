<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_logistics_solutions_page', function (Blueprint $table) {
            $table->id();
            $table->string('section', 50);
            $table->string('item_key', 100)->nullable();
            $table->json('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['section', 'item_key'], 'ecom_logistics_section_item_key_unique');
            $table->index(['section', 'is_active', 'sort_order'], 'ecom_logistics_section_active_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_logistics_solutions_page');
    }
};