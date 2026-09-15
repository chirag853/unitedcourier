<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShipmentRemark;
use App\Models\ShipperInfo;
use Illuminate\Http\Request;

class CodController extends Controller
{
    /**
     * Close COD/FOC order by AWB number (API).
     *
     * Request params (3 only):
     *   - awb_number: required, must exist in shipper_info.awb_number
     *   - type: required, cod|foc (cod => shipment_type 2, foc => 3)
     *   - remark: nullable, saved into shipment_remark.finance_remark
     *
     * Effect: shipper_info.status = 'cod_close' + shipment_type update,
     * plus finance_remark update. Mirrors the web CodController's
     * codCloseOrder() but looks the order up by AWB instead of invoice_id.
     */
    public function close_cod(Request $request)
    {
        $validated = $request->validate([
            'awb_number' => 'required|string|max:100|exists:shipper_info,awb_number',
            'type' => 'required|string|in:cod,foc,COD,FOC',
            'remark' => 'nullable|string|max:1000',
        ]);

        $awbNumber = trim((string) $validated['awb_number']);
        $type = strtolower(trim((string) $validated['type']));

        $shipper = ShipperInfo::where('awb_number', $awbNumber)
            ->orderBy('id', 'desc')
            ->first();

        if (! $shipper) {
            return response()->json([
                'success' => false,
                'message' => 'AWB number not found.',
            ], 404);
        }

        $shipper->shipment_type = $type === 'foc' ? 3 : 2;
        $shipper->status = 'cod_close';
        $shipper->save();

        $remark = trim((string) ($validated['remark'] ?? ''));

        $shipmentRemark = ShipmentRemark::firstOrNew(['shipper_id' => $shipper->id]);
        if ($shipmentRemark->exists === false) {
            $shipmentRemark->customer_id = $shipper->customer_id ?? 0;
        }
        // Remark hamesha finance_remark column me jayega
        if ($remark !== '') {
            $shipmentRemark->finance_remark = $remark;
        }
        $shipmentRemark->save();

        return response()->json([
            'success' => true,
            'message' => 'Order closed as ' . strtoupper($type),
            'awb_number' => $shipper->awb_number,
            'shipment_type' => (int) $shipper->shipment_type,
            'status' => $shipper->status,
            'finance_remark' => $shipmentRemark->finance_remark,
        ]);
    }
}
