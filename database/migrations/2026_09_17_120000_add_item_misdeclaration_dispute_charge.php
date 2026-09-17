<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "Item mis declaration" dispute rule (custom amount + 18% GST, first scan).
     * place_of_apply 'weighing at first scan' rakha hai taaki ye rule
     * admin/companies ke Apply Dispute Charge modal me dikhe (modal sirf
     * wahi place wale rules load karta hai).
     */
    public function up(): void
    {
        DB::table('dispute_surcharge_charges')->updateOrInsert(
            [
                'additional_charges' => 'Item mis declaration',
                'conditions' => 'All Shipment',
                'destination' => 'ALL',
                'service_id' => 'ALL',
                'calculation_type' => 'flat/awb',
            ],
            [
                'values' => 'Custom Amount + 18% GST',
                'gst_percentage' => 18,
                'place_of_apply' => 'weighing at first scan',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('dispute_surcharge_charges')
            ->where('additional_charges', 'Item mis declaration')
            ->where('conditions', 'All Shipment')
            ->delete();
    }
};
