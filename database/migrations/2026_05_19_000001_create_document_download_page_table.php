<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_download_page', function (Blueprint $table) {
            $table->id();
            $table->string('file_type', 50)->nullable()->default('pdf')->comment('pdf, xls, zip');
            $table->string('title', 255)->nullable();
            $table->string('file_size', 50)->nullable();
            $table->string('file_url', 500)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('status_badge', 100)->nullable()->default('Verified');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status', 20)->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_download_page');
    }
};