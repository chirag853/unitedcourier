<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create two new courier services for Canada and insert their rates
     * sourced from "Documents/all united rate list/canada aus ,worldwide.xlsx".
     *
     *  - Sheet 1: "CANADA DDP SELF RATES"  (0.5kg – 30kg, 40 slabs)
     *  - Sheet 2: "canada eccormce"        (0.1kg – 5kg,  15 slabs)
     *
     * These are one-country rates (Canada only) so zone_no = NULL.
     */
    public function up(): void
    {
        if (!Schema::hasTable('courier_services') || !Schema::hasTable('courier_rates')) {
            return;
        }

        $now = now()->format('Y-m-d H:i:s');

        /*------------------------------------------------------------------
         | 1) Create the two new courier services
         *----------------------------------------------------------------*/
        $canadaDdpId = DB::table('courier_services')->insertGetId([
            'shipper_code'  => 'CANADA',
            'method_code'   => 'CANADA-DDP',
            'network'       => 'Canada',
            'service_code'  => 'CANADA-DDP',
            'scode'         => 'CADD',
            'type'          => 'S',
            'method'        => 'UNITED CANADA DDP',
            'tat'           => '5-8 DAYS',
            'description'   => 'Canada DDP Self Rates',
            'weight'        => null,
            'real_name'     => 'Canada DDP',
        ]);

        $canadaEcomId = DB::table('courier_services')->insertGetId([
            'shipper_code'  => 'CANADA',
            'method_code'   => 'CANADA-ECOM',
            'network'       => 'Canada',
            'service_code'  => 'CANADA-ECOM',
            'scode'         => 'CAEC',
            'type'          => 'S',
            'method'        => 'UNITED CANADA E-COMMERCE',
            'tat'           => '5-8 DAYS',
            'description'   => 'Canada E-commerce Rates',
            'weight'        => null,
            'real_name'     => 'Canada E-commerce',
        ]);

        /*------------------------------------------------------------------
         | 2) Canada DDP Self Rates  (Sheet 1)
         |    Weight column = upper bound of each slab.
         |    Converted to ranges: start = prev+0.001, end = current.
         *----------------------------------------------------------------*/
        $canadaDdpRates = [
            [0.001, 0.500, 805],
            [0.501, 1.000, 1285],
            [1.001, 1.500, 1735],
            [1.501, 2.000, 2225],
            [2.001, 2.500, 2545],
            [2.501, 3.000, 2860],
            [3.001, 3.500, 3160],
            [3.501, 4.000, 3525],
            [4.001, 4.500, 3845],
            [4.501, 5.000, 4180],
            [5.001, 5.500, 3685],
            [5.501, 6.000, 3935],
            [6.001, 6.500, 4210],
            [6.501, 7.000, 4460],
            [7.001, 7.500, 4710],
            [7.501, 8.000, 4955],
            [8.001, 8.500, 5410],
            [8.501, 9.000, 5660],
            [9.001, 9.500, 5925],
            [9.501, 10.000, 6170],
            [10.001, 11.000, 6645],
            [11.001, 12.000, 7225],
            [12.001, 13.000, 7695],
            [13.001, 14.000, 8165],
            [14.001, 15.000, 8735],
            [15.001, 16.000, 9285],
            [16.001, 17.000, 9755],
            [17.001, 18.000, 10225],
            [18.001, 19.000, 10690],
            [19.001, 20.000, 11160],
            [20.001, 21.000, 11700],
            [21.001, 22.000, 12245],
            [22.001, 23.000, 12790],
            [23.001, 24.000, 13335],
            [24.001, 25.000, 13880],
            [25.001, 26.000, 14425],
            [26.001, 27.000, 14970],
            [27.001, 28.000, 15515],
            [28.001, 29.000, 16060],
            [29.001, 30.000, 16605],
        ];

        /*------------------------------------------------------------------
         | 3) Canada E-commerce Rates  (Sheet 2)
         *----------------------------------------------------------------*/
        $canadaEcomRates = [
            [0.001, 0.100, 605],
            [0.101, 0.200, 655],
            [0.201, 0.300, 670],
            [0.301, 0.400, 750],
            [0.401, 0.500, 800],
            [0.501, 0.750, 1135],
            [0.751, 1.000, 1250],
            [1.001, 1.500, 1675],
            [1.501, 2.000, 2145],
            [2.001, 2.500, 2440],
            [2.501, 3.000, 2730],
            [3.001, 3.500, 2995],
            [3.501, 4.000, 3335],
            [4.001, 4.500, 3630],
            [4.501, 5.000, 3940],
        ];

        /*------------------------------------------------------------------
         | 4) Insert all rates into courier_rates (customer_id = 0 = default)
         *----------------------------------------------------------------*/
        $inserts = [];

        foreach ($canadaDdpRates as $r) {
            $inserts[] = [
                'customer_id'      => 0,
                'service_id'       => $canadaDdpId,
                'wt_range_start'   => $r[0],
                'wt_range_end'     => $r[1],
                'zone_no'          => null,
                'price'            => $r[2],
                'fuel_charge'      => null,
                'fuel_percentage'  => null,
                'gst_percentage'   => 18.00,
                'gst_amount'       => null,
                'is_default'        => 1,
                'created_at'        => $now,
                'updated_at'        => null,
            ];
        }

        foreach ($canadaEcomRates as $r) {
            $inserts[] = [
                'customer_id'      => 0,
                'service_id'       => $canadaEcomId,
                'wt_range_start'   => $r[0],
                'wt_range_end'     => $r[1],
                'zone_no'          => null,
                'price'            => $r[2],
                'fuel_charge'      => null,
                'fuel_percentage'  => null,
                'gst_percentage'   => 18.00,
                'gst_amount'       => null,
                'is_default'        => 1,
                'created_at'        => $now,
                'updated_at'        => null,
            ];
        }

        DB::table('courier_rates')->insert($inserts);
    }

    /**
     * Reverse: remove the two Canada services and their rates.
     */
    public function down(): void
    {
        if (!Schema::hasTable('courier_services') || !Schema::hasTable('courier_rates')) {
            return;
        }

        // Get the service IDs we created
        $serviceIds = DB::table('courier_services')
            ->where('network', 'Canada')
            ->whereIn('service_code', ['CANADA-DDP', 'CANADA-ECOM'])
            ->pluck('id');

        if ($serviceIds->isNotEmpty()) {
            // Delete the rates
            DB::table('courier_rates')
                ->where('customer_id', 0)
                ->whereIn('service_id', $serviceIds)
                ->delete();

            // Delete the services
            DB::table('courier_services')
                ->whereIn('id', $serviceIds)
                ->delete();
        }
    }
};
