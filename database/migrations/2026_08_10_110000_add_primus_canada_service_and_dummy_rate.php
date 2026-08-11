<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COUNTRY = 'CA';

    private const SERVICE_CODE = 'PRIMUS-CA';

    /**
     * Add an enabled Primus service for Canada and one dummy default rate.
     */
    public function up(): void
    {
        if (!Schema::hasTable('courier_services') || !Schema::hasTable('courier_rates')) {
            return;
        }

        DB::transaction(function (): void {
            DB::table('courier_services')->updateOrInsert(
                [
                    'country' => self::COUNTRY,
                    'service_code' => self::SERVICE_CODE,
                ],
                [
                    'status' => 1,
                    'api_provider' => 'primus',
                    'shipper_code' => 'PRIMUS',
                    'method_code' => self::SERVICE_CODE,
                    'network' => 'Primus',
                    'scode' => 'PRIMUSCA',
                    'type' => 'S',
                    'method' => 'PRIMUS CANADA',
                    'tat' => '5-8 DAYS',
                    'description' => 'Primus Canada service with a dummy default rate',
                    'weight' => 'kg',
                    'real_name' => 'Primus Logistics',
                ]
            );

            $serviceId = DB::table('courier_services')
                ->where('country', self::COUNTRY)
                ->where('service_code', self::SERVICE_CODE)
                ->value('id');

            DB::table('courier_rates')->updateOrInsert(
                [
                    'customer_id' => 0,
                    'service_id' => $serviceId,
                    'wt_range_start' => 0.001,
                    'wt_range_end' => 1.000,
                ],
                [
                    'zone_no' => null,
                    'price' => 1000.00,
                    'fuel_charge' => 0.00,
                    'fuel_percentage' => null,
                    'gst_percentage' => 18.00,
                    'gst_amount' => null,
                    'is_default' => 1,
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-12-31',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        });
    }

    /**
     * Remove the dummy rate and remove the service if it has no other rates.
     */
    public function down(): void
    {
        if (!Schema::hasTable('courier_services') || !Schema::hasTable('courier_rates')) {
            return;
        }

        DB::transaction(function (): void {
            $serviceId = DB::table('courier_services')
                ->where('country', self::COUNTRY)
                ->where('service_code', self::SERVICE_CODE)
                ->where('api_provider', 'primus')
                ->value('id');

            if ($serviceId === null) {
                return;
            }

            DB::table('courier_rates')
                ->where('customer_id', 0)
                ->where('service_id', $serviceId)
                ->where('wt_range_start', 0.001)
                ->where('wt_range_end', 1.000)
                ->delete();

            $hasOtherRates = DB::table('courier_rates')
                ->where('service_id', $serviceId)
                ->exists();

            if (!$hasOtherRates) {
                DB::table('courier_services')
                    ->where('id', $serviceId)
                    ->delete();
            }
        });
    }
};
