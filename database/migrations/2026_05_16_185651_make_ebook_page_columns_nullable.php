<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebook_page', function (Blueprint $table) {
            $table->string('title', 255)->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->string('image', 255)->nullable()->change();
            $table->string('link', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ebook_page', function (Blueprint $table) {
            $table->string('title', 255)->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->string('image', 255)->nullable(false)->change();
            $table->string('link', 500)->nullable(false)->change();
        });
    }
};
