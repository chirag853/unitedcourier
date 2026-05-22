<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar_page', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100)->nullable();
            $table->string('item_key', 100)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('link', 500)->nullable();
            $table->json('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status', 20)->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_page');
    }
};