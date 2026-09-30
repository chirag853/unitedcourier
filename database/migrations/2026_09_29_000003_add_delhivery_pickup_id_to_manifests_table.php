<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * manifests me delhivery_pickup_id column add karo (Delhivery API se
     * mila pickup reference, numeric ya alphanumeric ho sakta hai isliye string).
     * Nullable + no backfill, taaki purani rows me NULL rahe.
     */
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            if (! Schema::hasColumn('manifests', 'delhivery_pickup_id')) {
                $table->string('delhivery_pickup_id', 100)->nullable()->after('delivery_label');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            if (Schema::hasColumn('manifests', 'delhivery_pickup_id')) {
                $table->dropColumn('delhivery_pickup_id');
            }
        });
    }
};
