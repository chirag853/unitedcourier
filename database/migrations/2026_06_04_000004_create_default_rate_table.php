<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create the default_rate table
        Schema::create('default_rate', function (Blueprint $table) {
            $table->id();
            $table->string('network', 50);
            $table->string('service', 50);
            $table->string('type', 10);
            $table->string('method', 100);
            $table->string('expected_delivery', 50);
            $table->decimal('weight_start_gm', 10, 3);
            $table->decimal('weight_end_gm', 10, 3);
            $table->integer('zone_id');
            $table->decimal('rate', 10, 2);
            $table->timestamps();

            $table->foreign('zone_id')
                ->references('zone_id')
                ->on('zone')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        // Fetch all methods from the methods table
        $methods = DB::table('methods')->get();

        // Fetch all zones from the zone table
        $zones = DB::table('zone')->get();

        // Expected delivery mapping based on service_description
        $deliveryMap = [
            'Ground'            => ['3-5 days', '5-7 days', '4-6 days'],
            'Next Day Air'      => ['1-2 days', '1 day', '2 days'],
            '2nd Day Air'       => ['2-3 days', '2 days', '3 days'],
            'Worldwide Express' => ['3-5 days', '4-6 days', '5-7 days'],
            'Standard'          => ['7-10 days', '8-12 days', '10-15 days'],
            'Saver'             => ['10-15 days', '12-18 days', '15-20 days'],
        ];

        $inserts = [];
        $now = now()->format('Y-m-d H:i:s');

        foreach ($methods as $method) {
            $deliveryOptions = $deliveryMap[$method->service_description] ?? ['5-7 days'];

            foreach ($zones as $zone) {
                $inserts[] = [
                    'network'           => $method->method_type,       // ddp or ddu
                    'service'           => $method->service_code,       // e.g., 03, 01, 02
                    'type'              => $method->method_type,        // ddp or ddu
                    'method'            => $method->method_name,        // e.g., United My Delivery
                    'expected_delivery' => $deliveryOptions[array_rand($deliveryOptions)],
                    'weight_start_gm'   => 0,
                    'weight_end_gm'     => 5000.000,
                    'zone_id'           => $zone->zone_id,
                    'rate'              => round(mt_rand(10000, 500000) / 100, 2), // Random rate between 100.00 and 5000.00
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        // Batch insert all records
        DB::table('default_rate')->insert($inserts);
    }

    public function down(): void
    {
        Schema::dropIfExists('default_rate');
    }
};