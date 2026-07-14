<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a `country` column to the courier_services table and populate it
     * based on the existing service-detection rules:
     *
     *   - DPD (PostShipping) services  → "UK"
     *     (method contains both "DDP" and "UNITED AIR PREMIUM")
     *   - Canada services              → "Canada"
     *     (network = "Canada" OR service_code starts with "CANADA-"
     *      OR method contains "UNITED CANADA")
     *   - All other services           → "US"
     */
    public function up(): void
    {
        if (!Schema::hasTable('courier_services')) {
            return;
        }

        // Add the country column (nullable so existing rows survive even
        // if the populate step below is skipped).
        if (!Schema::hasColumn('courier_services', 'country')) {
            Schema::table('courier_services', function (Blueprint $table) {
                $table->string('country')->nullable()->after('real_name');
            });
        }

        // Populate country values for every existing service.
        $services = DB::table('courier_services')->get();

        foreach ($services as $service) {
            $methodUpper      = strtoupper(trim($service->method ?? ''));
            $networkUpper     = strtoupper(trim($service->network ?? ''));
            $serviceCodeUpper = strtoupper(trim($service->service_code ?? ''));

            // DPD (PostShipping) detection — mirrors isPostShippingMethod()
            $isDpd = str_contains($methodUpper, 'DDP')
                && str_contains($methodUpper, 'UNITED AIR PREMIUM');

            // Canada service detection — mirrors isCanadaService()
            $isCanada = $networkUpper === 'CANADA'
                || str_starts_with($serviceCodeUpper, 'CANADA-')
                || str_contains($methodUpper, 'UNITED CANADA');

            if ($isDpd) {
                $country = 'UK';
            } elseif ($isCanada) {
                $country = 'Canada';
            } else {
                $country = 'US';
            }

            DB::table('courier_services')
                ->where('id', $service->id)
                ->update(['country' => $country]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('courier_services')) {
            return;
        }

        if (Schema::hasColumn('courier_services', 'country')) {
            Schema::table('courier_services', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
    }
};
