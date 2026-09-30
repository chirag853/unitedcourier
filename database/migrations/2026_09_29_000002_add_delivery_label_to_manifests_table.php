<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * manifests me delivery_label column add karo (shipper_info.custom_label
     * jaisa longText, taaki label URL ya HTML dono store ho sake).
     * Nullable + no backfill, taaki purani rows me NULL rahe.
     */
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            if (! Schema::hasColumn('manifests', 'delivery_label')) {
                $table->longText('delivery_label')->nullable()->after('assigned_delivery_person');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            if (Schema::hasColumn('manifests', 'delivery_label')) {
                $table->dropColumn('delivery_label');
            }
        });
    }
};
