<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the DHL all-country services used by ShipUniversal.
     */
    public function up(): void
    {
        if (!Schema::hasTable('courier_services')) {
            return;
        }

        $services = [
            [
                'country' => 'ALL',
                'status' => 1,
                'api_provider' => 'shipuniversal',
                'shipper_code' => 'DHL',
                'method_code' => 'DHL-PKT-INT',
                'network' => 'DHL',
                'service_code' => 'DHL PKT INT',
                'scode' => 'DHL-PKT-INT',
                'type' => 'S',
                'method' => 'UNITED AIR PREMIUM',
                'tat' => 'DHL PKT INT',
                'description' => 'DHL PKT INT (All Countries)',
                'weight' => 'Up to 5 KG',
                'real_name' => 'DHL',
            ],
            [
                'country' => 'ALL',
                'status' => 1,
                'api_provider' => 'shipuniversal',
                'shipper_code' => 'DHL',
                'method_code' => 'DHL-EUROPE-PKT',
                'network' => 'DHL',
                'service_code' => 'DHL EUROPE PKT',
                'scode' => 'DHL-EUROPE-PKT',
                'type' => 'S',
                'method' => 'UNITED AIR EXPRESS BIG',
                'tat' => 'DHL EUROPE PKT',
                'description' => 'DHL EUROPE PKT (All Countries)',
                'weight' => '6 to 31 KG',
                'real_name' => 'DHL',
            ],
        ];

        foreach ($services as $service) {
            DB::table('courier_services')->updateOrInsert(
                [
                    'country' => $service['country'],
                    'network' => $service['network'],
                    'method' => $service['method'],
                ],
                $service
            );
        }
    }

    /**
     * Remove only the DHL service records introduced by this migration.
     */
    public function down(): void
    {
        if (!Schema::hasTable('courier_services')) {
            return;
        }

        DB::table('courier_services')
            ->where('country', 'ALL')
            ->where('network', 'DHL')
            ->whereIn('method', [
                'UNITED AIR PREMIUM',
                'UNITED AIR EXPRESS BIG',
            ])
            ->delete();
    }
};
