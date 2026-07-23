<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Adds an `api_provider` column to the `courier_services` table.
 *
 * This column drives which external API is called when a shipment is
 * manifested. When set, it takes priority over the legacy string-matching
 * routing logic (isOverseasLogisticMethod, isPostShippingMethod,
 * isFlyingTigersMethod, network == 'ship global'). When empty/null, the
 * legacy logic continues to be used as a fallback so existing services
 * keep working unchanged.
 *
 * Recognised values (lowercase, stored case-insensitively at runtime):
 *   - overseas      → Overseas Logistic API
 *   - postshipping  → PostShipping API
 *   - flyingtigers  → Flying Tigers API
 *   - shipglobal    → Ship Global API
 *   - ups           → UPS API (default when nothing else matches)
 *
 * The migration also back-fills `api_provider` for the services whose
 * routing can be inferred from their existing `network` / `method` /
 * `real_name` values, so the database-first path works immediately for
 * the known services without manual data entry.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('courier_services')) {
            return;
        }

        if (!Schema::hasColumn('courier_services', 'api_provider')) {
            Schema::table('courier_services', function (Blueprint $table) {
                // Placed right after `status` (the last column added by the
                // 2026_07_17_000002_add_status_to_courier_services_table migration).
                $table->string('api_provider', 50)->nullable()->after('status');
            });
        }

        // Back-fill api_provider for existing services based on the same
        // detection rules used by the legacy string-matching methods. This
        // makes the database-first path work out of the box for known
        // services; new/unknown services are left NULL so the fallback
        // logic handles them.
        $services = DB::table('courier_services')->get();

        foreach ($services as $service) {
            $provider = $this->detectProvider($service);

            if ($provider !== null) {
                DB::table('courier_services')
                    ->where('id', $service->id)
                    ->update(['api_provider' => $provider]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (!Schema::hasTable('courier_services')) {
            return;
        }

        if (Schema::hasColumn('courier_services', 'api_provider')) {
            Schema::table('courier_services', function (Blueprint $table) {
                $table->dropColumn('api_provider');
            });
        }
    }

    /**
     * Determine the api_provider for an existing service row using the
     * same rules as the legacy string-matching methods. Returns null when
     * no rule matches (so the fallback logic stays in charge).
     *
     * @param object $service  A courier_services row (network, method,
     *                         real_name, service_code available).
     * @return string|null
     */
    private function detectProvider($service)
    {
        $method    = strtoupper(trim($service->method ?? ''));
        $network   = strtolower(trim($service->network ?? ''));
        $realName  = strtoupper(trim($service->real_name ?? ''));
        $serviceCd = strtoupper(trim($service->service_code ?? ''));

        // Overseas Logistic — Canada DDP / E-COMMERCE and Australia
        // (ARAMEX GPX / DPEX_AU_EXPRESS). Mirrors isOverseasLogisticMethod().
        $isCanada = str_contains($method, 'UNITED CANADA');
        $isDdpOrEcom = str_contains($method, 'DDP')
            || str_contains($method, 'E-COMMERCE')
            || str_contains($method, 'ECOMMERCE')
            || str_contains($method, 'E COMMERCE');
        if ($isCanada && $isDdpOrEcom) {
            return 'overseas';
        }
        if (str_contains($method, 'ARAMEX GPX')
            || str_contains($serviceCd, 'DPEX_AU_EXPRESS')
            || str_contains($serviceCd, 'DPEX AU EXPRESS')
        ) {
            return 'overseas';
        }

        // PostShipping — UNITED AIR PREMIUM DDP / UNITED PRIOR POST DDP.
        // Mirrors isPostShippingMethod().
        if (str_contains($method, 'DDP') && str_contains($method, 'UNITED AIR PREMIUM')) {
            return 'postshipping';
        }
        if (str_contains($method, 'DDP') && str_contains($method, 'UNITED PRIOR POST')) {
            return 'postshipping';
        }

        // Flying Tigers — UNITED ECO POST. Mirrors isFlyingTigersMethod().
        if (str_contains($method, 'UNITED ECO POST')) {
            return 'flyingtigers';
        }

        // Ship Global — network == 'Ship global' / 'shipglobal'.
        if ($network === 'ship global' || $network === 'shipglobal') {
            return 'shipglobal';
        }

        // UPS — network == 'UPS'.
        if ($network === 'ups') {
            return 'ups';
        }

        // Unknown — leave NULL so the fallback logic handles it.
        return null;
    }
};
