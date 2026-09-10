<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a nullable pickup_date column to the manifests table.
     * Stored as a date (Y-m-d) selected by the customer when assigning
     * a closed manifest for pickup.
     */
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->date('pickup_date')->nullable()->after('status')->comment('Selected pickup date (Y-m-d) when manifest is assigned for pickup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropColumn('pickup_date');
        });
    }
};
