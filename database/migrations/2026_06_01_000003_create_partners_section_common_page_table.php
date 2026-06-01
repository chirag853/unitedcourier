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
        Schema::create('partners_section_common_page', function (Blueprint $table) {
            $table->id();
            $table->string('logo_image')->comment('Path to the uploaded logo image');
            $table->string('alt_text')->nullable()->comment('Alt text for the logo image');
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
        Schema::dropIfExists('partners_section_common_page');
    }
};