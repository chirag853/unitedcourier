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
        Schema::table('courier_services', function (Blueprint $table) {
            $table->string('description')->nullable()->after('tat');
            $table->string('weight')->nullable()->after('description');
            $table->string('real_name')->nullable()->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courier_services', function (Blueprint $table) {
            $table->dropColumn(['description', 'weight', 'real_name']);
        });
    }
};