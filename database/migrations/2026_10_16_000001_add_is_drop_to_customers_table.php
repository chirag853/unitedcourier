<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop customers skip pickup: closing a manifest moves their
     * shipments straight to assigned_for_pickup and marks the
     * manifest rows as DROP instead of CLOSE.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_drop')->default(false)->after('can_create_shipment');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('is_drop');
        });
    }
};
