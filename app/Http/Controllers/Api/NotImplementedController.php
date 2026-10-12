<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourierService;
use App\Models\ShipmentTracking;
use App\Models\ShipperInfo;
use Illuminate\Http\Request;

class NotImplementedController extends Controller
{
    /**
     * Update the tracking number of a NOT_IMPLEMENTED shipment (no auth).
     *
     * Request JSON (2 only):
     *   - awb_number: required, must exist in shipper_info.awb_number
     *   - tracking_number: required string
     *
     * The shipment's courier service (via shipper_info.service_id) must have
     * service_code = NOT_IMPLEMENTED, otherwise the update is rejected.
     */
    public function updateTracking(Request $request)
    {
        $validated = $request->validate([
            'awb_number'      => 'required|string|exists:shipper_info,awb_number',
            'tracking_number' => 'required|string|max:100',
        ]);

        $shipper = ShipperInfo::where('awb_number', $validated['awb_number'])->first();
        if (! $shipper) {
            return response()->json([
                'status' => false,
                'message' => 'Shipment not found for this AWB number.',
            ], 404);
        }

        $serviceCode = '';
        if (! empty($shipper->service_id)) {
            $serviceCode = CourierService::whereKey((int) $shipper->service_id)->value('service_code') ?? '';
        }
        if (strtoupper(trim((string) $serviceCode)) !== 'NOT_IMPLEMENTED') {
            return response()->json([
                'status' => false,
                'message' => 'This shipment is not a NOT_IMPLEMENTED shipment. Tracking update not allowed.',
            ], 422);
        }

        $tracking = ShipmentTracking::updateOrCreate(
            ['shipper_id' => $shipper->id],
            [
                'customer_id' => $shipper->customer_id,
                'shipment_identification_number' => $validated['tracking_number'],
                'status' => 'created',
            ]
        );

        return response()->json([
            'status'          => true,
            'message'         => 'Tracking number updated successfully.',
            'awb_number'      => $shipper->awb_number,
            'tracking_number' => $tracking->shipment_identification_number,
        ]);
    }
}
