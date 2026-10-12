<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerManifestService;
use Illuminate\Http\Request;

class ShipmentCmsController extends Controller
{
    /**
     * Create a shipment draft WITHOUT token (no auth).
     * POST /api/shipment-creation-cms
     *
     * Same parameters as POST /api/v1/manifests, plus:
     *   - customer_code: required, must exist in customers.customer_code
     *
     * The request is delegated to CustomerManifestController@store with the
     * customer resolved from customer_code, so validation, service
     * resolution and draft creation behave identically.
     */
    public function store(Request $request, CustomerManifestService $manifestService)
    {
        try {
            $request->validate([
                'customer_code' => 'required|string|exists:customers,customer_code',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        $customer = Customer::where('customer_code', $request->input('customer_code'))->first();
        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found for this customer_code.',
            ], 404);
        }

        $request->attributes->set('api_customer', $customer);

        $response = app(CustomerManifestController::class)->store($request, $manifestService);
        $payload = json_decode($response->getContent(), true) ?? [];

        // CMS shape: only status true/false (no "success" key).
        $data = ['status' => (bool) ($payload['success'] ?? false)];
        foreach (['message', 'awb_number', 'errors'] as $key) {
            if (array_key_exists($key, $payload)) {
                $data[$key] = $payload[$key];
            }
        }

        return response()->json($data, $response->getStatusCode());
    }
}
