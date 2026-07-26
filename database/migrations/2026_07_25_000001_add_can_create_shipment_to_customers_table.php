<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a `can_create_shipment` column to the customers table.
     *
     * This is an INDEPENDENT toggle from the account `status` field:
     *  - status (existing)            -> controls whether the customer can log in
     *  - can_create_shipment (new)    -> controls whether the customer can create shipments
     *
     * Defaults to 1 (enabled) so existing customers keep working as before.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('can_create_shipment')->default(1)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('can_create_shipment');
        });
    }
};
