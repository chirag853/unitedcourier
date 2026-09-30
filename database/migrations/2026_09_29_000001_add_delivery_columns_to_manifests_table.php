<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * shipment_invoice wale delivery columns manifests me bhi add karo.
     * Per-row storage: har manifests row (1 row = 1 shipper_id) par apni value.
     * Nullable + no backfill, taaki purani rows me NULL rahe.
     */
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            if (! Schema::hasColumn('manifests', 'delivery_type')) {
                $table->string('delivery_type', 20)->nullable()->after('pickup_date')->comment('DDU, DDP, Self');
            }
            if (! Schema::hasColumn('manifests', 'assigned_delivery_person')) {
                $table->integer('assigned_delivery_person')->nullable()->after('delivery_type')->comment('FK to admin_user.id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            if (Schema::hasColumn('manifests', 'assigned_delivery_person')) {
                $table->dropColumn('assigned_delivery_person');
            }
            if (Schema::hasColumn('manifests', 'delivery_type')) {
                $table->dropColumn('delivery_type');
            }
        });
    }
};
