<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_rates', function (Blueprint $table) {
            $table->decimal('fuel_charge', 10, 2)->nullable()->after('price');
            $table->decimal('fuel_percentage', 5, 2)->nullable()->after('fuel_charge');
            $table->decimal('gst_percentage', 5, 2)->nullable()->after('fuel_percentage');
            $table->decimal('gst_amount', 10, 2)->nullable()->after('gst_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('courier_rates', function (Blueprint $table) {
            $table->dropColumn(['fuel_charge', 'fuel_percentage', 'gst_percentage', 'gst_amount']);
        });
    }
};