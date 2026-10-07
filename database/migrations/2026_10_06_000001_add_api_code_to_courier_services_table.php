<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_services', function (Blueprint $table) {
            $table->unsignedInteger('api_code')->nullable()->after('service_code')->index();
        });

        // Backfill: har distinct service_code ko insertion order (MIN(id))
        // me 100, 200, 300... numbering.
        $codes = DB::table('courier_services')
            ->selectRaw('service_code, MIN(id) as min_id')
            ->groupBy('service_code')
            ->orderBy('min_id')
            ->pluck('service_code');

        $code = 100;
        foreach ($codes as $serviceCode) {
            DB::table('courier_services')
                ->where('service_code', $serviceCode)
                ->update(['api_code' => $code]);
            $code += 100;
        }
    }

    public function down(): void
    {
        Schema::table('courier_services', function (Blueprint $table) {
            $table->dropColumn('api_code');
        });
    }
};
