<?php

namespace Database\Seeders;

use App\Models\DisputeCharge;
use Illuminate\Database\Seeder;

class DisputeSurchargeChargesSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'additional_charges' => 'Additional Handling Surcharge',
                'conditions' => 'actual wt > 21 kg',
                'destination' => 'USA',
                'service_id' => 'UPS',
                'calculation_type' => 'flat',
                'values' => '$45',
                'gst_percentage' => 0,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Additional Handling Surcharge',
                'conditions' => 'length + girth > 266 cm, girth = length + (width + height) * 2',
                'destination' => 'USA',
                'service_id' => 'UPS',
                'calculation_type' => 'flat',
                'values' => '$30',
                'gst_percentage' => 0,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Additional Handling Surcharge',
                'conditions' => 'length > 120 cm',
                'destination' => 'USA',
                'service_id' => 'UPS',
                'calculation_type' => 'flat',
                'values' => '$30',
                'gst_percentage' => 0,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Additional Handling Surcharge',
                'conditions' => 'width > 75 cm',
                'destination' => 'USA',
                'service_id' => 'UPS',
                'calculation_type' => 'flat',
                'values' => '$30',
                'gst_percentage' => 0,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Additional Handling Surcharge',
                'conditions' => 'packages not packed in proper brown carton which is not a cuboid and max dimension - length 45 cm, breadth 35 cm, height 15 cm',
                'destination' => 'USA',
                'service_id' => 'UPS',
                'calculation_type' => 'flat',
                'values' => '$25',
                'gst_percentage' => 0,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Large Packet Surcharge',
                'conditions' => 'length + girth > 330 cm or length > 243 cm',
                'destination' => 'USA',
                'service_id' => 'UPS',
                'calculation_type' => 'flat',
                'values' => '$230',
                'gst_percentage' => 0,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Large Packet Surcharge',
                'conditions' => 'wt > 30 kg or length > 175 cm or girth > 300 cm',
                'destination' => 'EU and UK',
                'service_id' => 'DPD',
                'calculation_type' => 'flat',
                'values' => '$230',
                'gst_percentage' => 0,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Non-Conveyable Sucharges',
                'conditions' => 'extra large boxes',
                'destination' => 'ALL',
                'service_id' => 'ALL',
                'calculation_type' => 'flat/box',
                'values' => 'Rs 1900 + 18% GST',
                'gst_percentage' => 18,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Oversize Sucharges',
                'conditions' => 'when actual dimension are greater than declared dimensions at the time of shipment creation',
                'destination' => 'ALL',
                'service_id' => 'ALL',
                'calculation_type' => 'flat/box',
                'values' => 'Rs 5000 + 18% GST',
                'gst_percentage' => 18,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Weight dispute',
                'conditions' => 'when actual weight is greater than declared weight',
                'destination' => 'ALL',
                'service_id' => 'ALL',
                'calculation_type' => 'flat/box',
                'values' => 'Custom Amount + 18% GST',
                'gst_percentage' => 18,
                'place_of_apply' => 'weighing at first scan',
            ],
            [
                'additional_charges' => 'Address Correction Sucharges',
                'conditions' => null,
                'destination' => 'USA',
                'service_id' => 'ALL',
                'calculation_type' => 'flat/piece',
                'values' => '$15',
                'gst_percentage' => 0,
                'place_of_apply' => 'After Dispatched',
            ],
            [
                'additional_charges' => 'Shipping Bill Amendment Sucharges',
                'conditions' => null,
                'destination' => 'ALL',
                'service_id' => 'ALL',
                'calculation_type' => 'flat/hawb',
                'values' => '$1500',
                'gst_percentage' => 0,
                'place_of_apply' => 'After Dispatched',
            ],
            [
                'additional_charges' => 'COD Collection Sucharges',
                'conditions' => null,
                'destination' => 'USA',
                'service_id' => 'UPS',
                'calculation_type' => 'flat',
                'values' => '$15',
                'gst_percentage' => 0,
                'place_of_apply' => 'After Dispatched',
            ],
            [
                'additional_charges' => 'Customs Paperwork Handling Charges',
                'conditions' => null,
                'destination' => 'ALL',
                'service_id' => 'ALL',
                'calculation_type' => 'flat',
                'values' => 'Rs 500 + 18 % GST',
                'gst_percentage' => 18,
                'place_of_apply' => 'After Dispatched',
            ],
            [
                'additional_charges' => 'Customs Clearance Charges',
                'conditions' => null,
                'destination' => 'ALL',
                'service_id' => 'ALL',
                'calculation_type' => 'flat',
                'values' => 'Custom amount + 18% GST',
                'gst_percentage' => 18,
                'place_of_apply' => 'After Dispatched',
            ],
            [
                'additional_charges' => 'Demand Surcharge',
                'conditions' => null,
                'destination' => 'ALL',
                'service_id' => 'FedEx',
                'calculation_type' => 'flat',
                'values' => 'Custom amount + 18% GST',
                'gst_percentage' => 18,
                'place_of_apply' => 'After Dispatched',
            ],
            [
                'additional_charges' => 'Fumigation Sucharges',
                'conditions' => 'when customer type is cargo',
                'destination' => 'ALL',
                'service_id' => 'ALL',
                'calculation_type' => 'flat',
                'values' => 'Custom amount + 18% GST',
                'gst_percentage' => 18,
                'place_of_apply' => 'After Dispatched',
            ],
        ];

        foreach ($entries as $entry) {
            DisputeCharge::updateOrCreate(
                [
                    'additional_charges' => $entry['additional_charges'],
                    'conditions' => $entry['conditions'],
                    'destination' => $entry['destination'],
                    'service_id' => $entry['service_id'],
                ],
                [
                    'calculation_type' => $entry['calculation_type'],
                    'values' => $entry['values'],
                    'gst_percentage' => $entry['gst_percentage'],
                    'place_of_apply' => $entry['place_of_apply'],
                ]
            );
        }
    }
}
