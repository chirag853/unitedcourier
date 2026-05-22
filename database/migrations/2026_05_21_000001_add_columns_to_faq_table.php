<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->string('question')->after('id');
            $table->text('answer')->after('question');
            $table->string('page')->default('network')->after('answer');
            $table->integer('sort_order')->default(0)->after('page');
            $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->dropColumn(['question', 'answer', 'page', 'sort_order', 'is_active']);
        });
    }
};