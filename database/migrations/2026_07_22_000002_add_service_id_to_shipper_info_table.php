<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('shipper_info')) {
            return;
        }

        if (!Schema::hasColumn('shipper_info', 'service_id')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->after('service_rate_id');
            });
        }

        // Back-fill service_id for existing shippers by resolving the
        // courier_services.id from their stored shipping_method. This is a
        // best-effort pass using a case-insensitive match; rows that cannot
        // be resolved are left NULL and will be filled on the next manifest.
        $shippers = DB::table('shipper_info')
            ->whereNull('service_id')
            ->whereNotNull('shipping_method')
            ->get(['id', 'shipping_method']);

        foreach ($shippers as $shipper) {
            $method = trim((string) $shipper->shipping_method);
            if ($method === '') {
                continue;
            }

            // Tier 1: exact match
            $service = DB::table('courier_services')
                ->where('method', $method)
                ->value('id');

            // Tier 2: case-insensitive match
            if (!$service) {
                $service = DB::table('courier_services')
                    ->whereRaw('LOWER(method) = ?', [strtolower($method)])
                    ->value('id');
            }

            if ($service) {
                DB::table('shipper_info')
                    ->where('id', $shipper->id)
                    ->update(['service_id' => $service]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('shipper_info')) {
            return;
        }

        if (Schema::hasColumn('shipper_info', 'service_id')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->dropColumn('service_id');
            });
        }
    }
};
