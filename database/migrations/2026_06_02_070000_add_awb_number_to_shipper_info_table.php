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
        Schema::table('shipper_info', function (Blueprint $table) {
            $table->string('awb_number', 50)->nullable()->unique()->after('customer_id')->comment('Auto-generated AWB number: UWC+YYMMDD+00001');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipper_info', function (Blueprint $table) {
            $table->dropColumn('awb_number');
        });
    }
};