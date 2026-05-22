<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop the blog_detail_page table after data has been migrated to blogs.detail_sections JSON column.
     */
    public function up(): void
    {
        Schema::dropIfExists('blog_detail_page');
    }

    /**
     * Reverse the migrations.
     * Recreate the blog_detail_page table in case of rollback.
     */
    public function down(): void
    {
        Schema::create('blog_detail_page', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('blog_id');
            $table->string('section_key');
            $table->string('section_title')->nullable();
            $table->text('section_content')->nullable();
            $table->string('section_type')->default('content_section');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
