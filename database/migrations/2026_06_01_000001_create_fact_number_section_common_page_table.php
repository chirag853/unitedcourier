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
        Schema::create('fact_number_section_common_page', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('target_number', 100)->nullable();
            $table->string('suffix', 50)->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_number_section_common_page');
    }
};