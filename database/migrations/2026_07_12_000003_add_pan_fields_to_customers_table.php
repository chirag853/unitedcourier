<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds PAN fields to the customers table so the PAN number and
     * verification status can be stored on the customer record (mirrors
     * the existing aadhar_number / aadhar_verified pattern).
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('pan_number', 10)->nullable()->after('aadhar_verified')->comment('PAN number (10 chars)');
            $table->boolean('pan_verified')->default(false)->after('pan_number')->comment('Whether PAN was verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'pan_number',
                'pan_verified',
            ]);
        });
    }
};
