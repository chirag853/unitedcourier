<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store the outbound carrier request payload alongside the response
     * for debugging (populated for new manifests only).
     */
    public function up(): void
    {
        Schema::table('shipment_tracking', function (Blueprint $table) {
            $table->longText('raw_request')->nullable()->after('raw_response');
        });
    }

    public function down(): void
    {
        Schema::table('shipment_tracking', function (Blueprint $table) {
            $table->dropColumn('raw_request');
        });
    }
};
