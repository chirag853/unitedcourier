<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\Admin;
use App\Models\NetworkOffice;
use App\Models\ShipmentInvoice;
use App\Models\Customer;
use App\Models\KycDetail;
use App\Models\ShipperInfo;
use App\Models\Tracking;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class AdminController extends Controller
{
    public function index()
    {
        // Check if admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login first');
        }

        // Customer Summary
        $totalRegistrations = Customer::count();
        $kycPending = KycDetail::whereIn('kyc_status', ['pending', 'under_review'])->count();
        $onboardedCustomers = KycDetail::where('kyc_status', 'approved')->count();
        $csb5Enabled = Customer::where('csb_status', 2)->count();

        // Shipment Summary (all statuses)
        $shipmentStatusCounts = ShipperInfo::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Delivery/Network Summary - group by shipping_method
        $networkCounts = ShipperInfo::select('shipping_method', DB::raw('count(*) as count'))
            ->whereNotNull('shipping_method')
            ->where('shipping_method', '!=', '')
            ->groupBy('shipping_method')
            ->pluck('count', 'shipping_method')
            ->toArray();

        // Categorize networks: ShipRocket, Self/Delhivery/UPS/etc
        $shipRocketCount = 0;
        $selfCount = 0;
        $otherNetworkCount = 0;
        foreach ($networkCounts as $method => $count) {
            $methodLower = strtolower($method);
            if (str_contains($methodLower, 'shiprocket') || str_contains($methodLower, 'ship_rocket')) {
                $shipRocketCount += $count;
            } elseif (str_contains($methodLower, 'self') || str_contains($methodLower, 'own')) {
                $selfCount += $count;
            } else {
                $otherNetworkCount += $count;
            }
        }
        // Delivered shipments count
        $deliveredCount = $shipmentStatusCounts['delivered'] ?? 0;

        // Month-over-month changes for stat cards
        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $thisMonthRegistrations = Customer::whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])->count();
        $lastMonthRegistrations = Customer::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $registrationsChangePercent = $lastMonthRegistrations > 0 ? round(($thisMonthRegistrations - $lastMonthRegistrations) / $lastMonthRegistrations * 100, 1) : ($thisMonthRegistrations > 0 ? 100 : 0);

        $thisMonthKycPending = KycDetail::whereIn('kyc_status', ['pending', 'under_review'])->whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])->count();
        $lastMonthKycPending = KycDetail::whereIn('kyc_status', ['pending', 'under_review'])->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $kycPendingChangePercent = $lastMonthKycPending > 0 ? round(($thisMonthKycPending - $lastMonthKycPending) / $lastMonthKycPending * 100, 1) : ($thisMonthKycPending > 0 ? 100 : 0);

        $thisMonthOnboarded = KycDetail::where('kyc_status', 'approved')->whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])->count();
        $lastMonthOnboarded = KycDetail::where('kyc_status', 'approved')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $onboardedChangePercent = $lastMonthOnboarded > 0 ? round(($thisMonthOnboarded - $lastMonthOnboarded) / $lastMonthOnboarded * 100, 1) : ($thisMonthOnboarded > 0 ? 100 : 0);

        $thisMonthCsb5 = Customer::where('csb_status', 2)->whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])->count();
        $lastMonthCsb5 = Customer::where('csb_status', 2)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $csb5ChangePercent = $lastMonthCsb5 > 0 ? round(($thisMonthCsb5 - $lastMonthCsb5) / $lastMonthCsb5 * 100, 1) : ($thisMonthCsb5 > 0 ? 100 : 0);

        // KYC Pending list for the dashboard table
        $kycPendingList = KycDetail::with('customer')
            ->whereIn('kyc_status', ['pending', 'under_review'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'totalRegistrations', 'kycPending', 'onboardedCustomers', 'csb5Enabled',
            'shipmentStatusCounts', 'shipRocketCount', 'selfCount', 'otherNetworkCount', 'deliveredCount',
            'networkCounts',
            'registrationsChangePercent', 'kycPendingChangePercent', 'onboardedChangePercent', 'csb5ChangePercent',
            'kycPendingList'
        ));
    }

    /**
     * Return chart data for the admin dashboard via AJAX.
     * Supports date filters: today, yesterday, this_month, last_month, last_year
     */
    public function dashboardChartData(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $filter = $request->input('filter', 'this_month');
        $now = now();

        // Determine date range based on filter
        switch ($filter) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $endDate = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'last_year':
                $startDate = $now->copy()->subYearNoOverflow()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
        }

        // Customer Summary — cumulative (all-time), not date-filtered
        // These metrics represent total counts regardless of date period
        $totalRegistrations = Customer::count();
        $kycPending = KycDetail::whereIn('kyc_status', ['pending', 'under_review'])->count();
        $onboardedCustomers = KycDetail::where('kyc_status', 'approved')->count();
        $csb5Enabled = Customer::where('csb_status', 2)->count();

        // Shipment Summary for filtered period
        $shipmentStatusCounts = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Network/Delivery Summary for filtered period
        $networkCounts = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('shipping_method')
            ->where('shipping_method', '!=', '')
            ->select('shipping_method', DB::raw('count(*) as count'))
            ->groupBy('shipping_method')
            ->pluck('count', 'shipping_method')
            ->toArray();

        $shipRocketCount = 0;
        $selfCount = 0;
        $otherNetworks = [];
        foreach ($networkCounts as $method => $count) {
            $methodLower = strtolower($method);
            if (str_contains($methodLower, 'shiprocket') || str_contains($methodLower, 'ship_rocket')) {
                $shipRocketCount += $count;
            } elseif (str_contains($methodLower, 'self') || str_contains($methodLower, 'own')) {
                $selfCount += $count;
            } else {
                $otherNetworks[$method] = $count;
            }
        }
        $deliveredCount = $shipmentStatusCounts['delivered'] ?? 0;

        // Date-wise shipment counts for trend chart
        if ($filter === 'last_year') {
            $dateWiseCounts = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period"), DB::raw('count(*) as count'))
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('count', 'period')
                ->toArray();
        } else {
            $dateWiseCounts = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as period"), DB::raw('count(*) as count'))
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('count', 'period')
                ->toArray();
        }

        $statusMap = Tracking::getStatusTitleMap();

        return response()->json([
            'success' => true,
            'filter' => $filter,
            'customerSummary' => [
                'totalRegistrations' => $totalRegistrations,
                'kycPending' => $kycPending,
                'onboardedCustomers' => $onboardedCustomers,
                'csb5Enabled' => $csb5Enabled,
            ],
            'shipmentStatusCounts' => $shipmentStatusCounts,
            'statusMap' => $statusMap,
            'deliverySummary' => [
                'delivered' => $deliveredCount,
                'shipRocket' => $shipRocketCount,
                'self' => $selfCount,
                'otherNetworks' => $otherNetworks,
            ],
            'dateWiseCounts' => $dateWiseCounts,
        ]);
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    public function loginPost(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Look up the admin by email first (so we can give a clear inactive message)
        $admin = Admin::where('email', $request->email)->first();

        // Block login for deactivated users
        if ($admin && $admin->status != 1) {
            return redirect()->route('admin.login')
                ->with('error', 'Your account has been deactivated. Please contact the Super Admin.')
                ->withInput($request->only('email'));
        }

        // Attempt to login with admin guard (also enforce status at credential level)
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1], $request->has('remember'))) {
            return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
        }

        // Login failed
        return redirect()->route('admin.login')->with('error', 'Invalid email or password');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', 'Logged out successfully!');
    }

    public function companies()
    {
        // Base query builder for shipments with all related data, filtered by shipper status
        $baseQuery = function ($status) {
            return DB::table('shipment_invoice')
                ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
                ->leftJoin('customers', 'shipper_info.customer_id', '=', 'customers.id')
                ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
                ->leftJoin('admin_user', 'shipment_invoice.assigned_delivery_person', '=', 'admin_user.id')
                ->where('shipper_info.status', $status)
                ->select(
                    'shipment_invoice.id',
                    'shipment_invoice.invoice_number',
                    'shipment_invoice.invoice_date',
                    'shipment_invoice.invoice_amount',
                    'shipment_invoice.incoterms',
                    'shipment_invoice.invoice_currency',
                    'shipment_invoice.reference_number',
                    'shipment_invoice.status',
                    'shipment_invoice.delivery_type',
                    'shipment_invoice.assigned_delivery_person',
                    'shipment_invoice.created_at',
                    'shipment_invoice.updated_at',
                    'shipper_info.id as shipper_id',
                    'shipper_info.company_name as shipper_company',
                    'shipper_info.contact_person as shipper_contact',
                    'shipper_info.city as shipper_city',
                    'shipper_info.state as shipper_state',
                    'shipper_info.awb_number',
                    'customers.id as customer_id',
                    'customers.first_name',
                    'customers.last_name',
                    'customers.email as customer_email',
                    'customers.phone_number as customer_phone',
                    'consignee_info.consignee_name',
                    'consignee_info.city as consignee_city',
                    'consignee_info.state as consignee_state',
                    'admin_user.name as delivery_person_name'
                )
                ->orderBy('shipment_invoice.created_at', 'desc')
                ->get();
        };

        // Fetch shipments by status for each tab
        $manifestedShipments = $baseQuery('manifested');
        $assignedForPickupShipments = $baseQuery('assigned_for_pickup');
        $printLabelShipments = $baseQuery('dispatched');
        $readyToDispatchShipments = $baseQuery('ready_to_dispatch');

        // Fetch delivery persons where type = 'Delivery_person'
        $deliveryPersons = Admin::where('type', 'Delivery_person')
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'mobile']);

        return view('admin.companies', compact('manifestedShipments', 'assignedForPickupShipments', 'printLabelShipments', 'readyToDispatchShipments', 'deliveryPersons'));
    }

    /**
     * Assign delivery type and/or delivery person to a shipment.
     * When DDU (Delhivery) is selected, also calls the Delhivery API to create a pickup.
     */
    public function assignDelivery(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
                'delivery_type' => 'required|string|in:DDU,DDP,Self',
                'delivery_person_id' => 'nullable|integer|exists:admin_user,id',
            ]);

            $updateData = [
                'delivery_type' => $request->delivery_type,
            ];

            // If Self is selected, assign delivery person; otherwise set to null
            if ($request->delivery_type === 'Self') {
                if (!$request->delivery_person_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a delivery person for Self delivery type.'
                    ]);
                }
                $updateData['assigned_delivery_person'] = $request->delivery_person_id;
            } else {
                $updateData['assigned_delivery_person'] = null;
            }

            ShipmentInvoice::where('id', $request->shipment_id)->update($updateData);

            // Create tracking record for pickup assignment and update shipper status
            $shipmentInvoice = ShipmentInvoice::find($request->shipment_id);
            if ($shipmentInvoice && $shipmentInvoice->shipper_id) {
                $shipper = \App\Models\ShipperInfo::find($shipmentInvoice->shipper_id);
                if ($shipper && $shipper->awb_number) {
                    $createShipment = \App\Models\CreateShipment::where('shipper_id', $shipper->id)->first();
                    \App\Models\Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'status'     => 'assigned_for_pickup',
                        'title'      => 'Assigned for Pickup',
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id'     => $shipper->awb_number,
                    ]);
                    // Update shipper status so it moves to "Assigned for Pickup" tab
                    $shipper->status = 'assigned_for_pickup';
                    $shipper->save();
                }
            }

            // If DDU (Delhivery) is selected, call the Delhivery API
            $delhiveryResponse = null;
            if ($request->delivery_type === 'DDU') {
                $delhiveryResponse = $this->callDelhiveryApi($request->shipment_id);
            }

            $response = [
                'success' => true,
                'message' => 'Delivery assignment saved successfully.'
            ];

            // Include Delhivery API response if applicable
            if ($delhiveryResponse !== null) {
                $response['delhivery'] = $delhiveryResponse;
                if (!$delhiveryResponse['success']) {
                    $response['message'] = 'Delivery assignment saved, but Delhivery API call failed: ' . $delhiveryResponse['message'];
                } else {
                    $response['message'] = 'Delivery assignment saved and Delhivery pickup created successfully.';
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Receive a shipment - mark as received in tracking table.
     * When "Yes" is selected, creates a tracking record with status "received"
     * and updates shipper status to "dispatched" (moves shipment to Print Label tab).
     * When "No" is selected, creates a tracking record with status "on_hold"
     * and updates shipper status to "on_hold".
     */
    public function receiveShipment(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
                'received'    => 'required|string|in:yes,no',
            ]);

            $shipmentInvoice = ShipmentInvoice::find($request->shipment_id);
            if (!$shipmentInvoice || !$shipmentInvoice->shipper_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found or no shipper associated.'
                ]);
            }

            $shipper = \App\Models\ShipperInfo::find($shipmentInvoice->shipper_id);
            if (!$shipper || !$shipper->awb_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipper info not found or AWB number missing.'
                ]);
            }

            $createShipment = \App\Models\CreateShipment::where('shipper_id', $shipper->id)->first();

            if ($request->received === 'yes') {
                // Mark as received in tracking, but set shipper status to 'dispatched'
                // so the shipment moves to the Print Label tab
                \App\Models\Tracking::create([
                    'awb_number'  => $shipper->awb_number,
                    'status'      => 'received',
                    'title'       => 'Shipment Received',
                    'shipper_id'  => $shipper->id,
                    'shipping_id' => $createShipment ? $createShipment->id : null,
                    'uwc_id'      => $shipper->awb_number,
                ]);
                $shipper->status = 'dispatched';
                $shipper->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment received successfully. It has been moved to Print Label tab.'
                ]);
            } else {
                // Mark as on hold (not received)
                \App\Models\Tracking::create([
                    'awb_number'  => $shipper->awb_number,
                    'status'      => 'on_hold',
                    'title'       => 'Shipment On Hold',
                    'shipper_id'  => $shipper->id,
                    'shipping_id' => $createShipment ? $createShipment->id : null,
                    'uwc_id'      => $shipper->awb_number,
                ]);
                $shipper->status = 'on_hold';
                $shipper->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment marked as on hold (not received).'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark a shipment as Ready to Dispatch.
     * Creates a tracking record with status 'ready_to_dispatch' and updates
     * the shipper status so the shipment moves to the Ready to Dispatch tab.
     */
    public function readyToDispatch(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
            ]);

            $shipmentInvoice = ShipmentInvoice::find($request->shipment_id);
            if (!$shipmentInvoice || !$shipmentInvoice->shipper_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found or no shipper associated.'
                ]);
            }

            $shipper = \App\Models\ShipperInfo::find($shipmentInvoice->shipper_id);
            if (!$shipper || !$shipper->awb_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipper info not found or AWB number missing.'
                ]);
            }

            $createShipment = \App\Models\CreateShipment::where('shipper_id', $shipper->id)->first();

            // Create tracking record for ready to dispatch
            \App\Models\Tracking::create([
                'awb_number'  => $shipper->awb_number,
                'status'      => 'ready_to_dispatch',
                'title'       => 'Ready to Dispatch',
                'shipper_id'  => $shipper->id,
                'shipping_id' => $createShipment ? $createShipment->id : null,
                'uwc_id'      => $shipper->awb_number,
            ]);

            // Update shipper status so it moves to "Ready to Dispatch" tab
            $shipper->status = 'ready_to_dispatch';
            $shipper->save();

            return response()->json([
                'success' => true,
                'message' => 'Shipment marked as Ready to Dispatch successfully. It has been moved to the Ready to Dispatch tab.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate a shipping label PDF as base64 string.
     * Priority: Use shipment_tracking.raw_response for existing label data.
     * - UPS (response_status_description = "success"): Extract GraphicImage base64 → PDF
     * - Ship Global (response_status_description = "Ship Global order created"): Extract pdf_base64 → PDF
     * Fallback: Generate label via Dompdf if no tracking record exists.
     */
    public function generateLabel(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
            ]);

            // Get shipper_id from shipment_invoice
            $shipperId = DB::table('shipment_invoice')
                ->where('id', $request->shipment_id)
                ->value('shipper_id');

            if (!$shipperId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found.'
                ]);
            }

            // Look up shipment_tracking record for this shipper
            $trackingRecord = \App\Models\ShipmentTracking::where('shipper_id', $shipperId)->first();

            // Try to extract label from shipment_tracking raw_response
            if ($trackingRecord && $trackingRecord->raw_response) {
                $rawResponse = $trackingRecord->raw_response;
                $statusDescription = $trackingRecord->response_status_description;
                $pdfBase64 = null;

                // UPS case: response_status_description contains "success"
                if (stripos($statusDescription, 'success') !== false) {
                    // Extract GraphicImage from raw_response
                    $packageResults = null;

                    // Try ShipmentResults.PackageResults path in raw_response
                    if (isset($rawResponse['ShipmentResults']['PackageResults'])) {
                        $packageResults = $rawResponse['ShipmentResults']['PackageResults'];
                    } elseif (isset($rawResponse['PackageResults'])) {
                        $packageResults = $rawResponse['PackageResults'];
                    } elseif ($trackingRecord->package_results) {
                        $packageResults = $trackingRecord->package_results;
                    }

                    if ($packageResults) {
                        $firstPkg = is_array($packageResults) && isset($packageResults[0]) ? $packageResults[0] : $packageResults;

                        $graphicImage = null;
                        $labelFormat = null;

                        // Try ShippingLabel key (newer UPS Ship API format)
                        if (isset($firstPkg['ShippingLabel'])) {
                            $labelFormat = $firstPkg['ShippingLabel']['ImageFormat']['Code'] ?? 'GIF';
                            $graphicImage = $firstPkg['ShippingLabel']['GraphicImage'] ?? null;
                        } elseif (isset($firstPkg['LabelImage'])) {
                            // Older/different UPS response format
                            $labelFormat = $firstPkg['LabelImage']['LabelImageFormat']['Code'] ?? 'PDF';
                            $graphicImage = $firstPkg['LabelImage']['GraphicImage'] ?? null;
                        }

                        if ($graphicImage) {
                            if ($labelFormat === 'PDF') {
                                // GraphicImage is already base64-encoded PDF — return directly
                                $pdfBase64 = $graphicImage;
                            } else {
                                // GraphicImage is base64-encoded image (GIF/SPL/EPL etc.)
                                // Convert to PDF by embedding the image in a Dompdf HTML template
                                $mimeType = strtolower($labelFormat);
                                // Map common UPS format codes to MIME types
                                $mimeMap = [
                                    'gif'  => 'image/gif',
                                    'png'  => 'image/png',
                                    'jpg'  => 'image/jpeg',
                                    'jpeg' => 'image/jpeg',
                                    'pdf'  => 'application/pdf',
                                    'spl'  => 'application/pdf',
                                    'epl'  => 'application/pdf',
                                    'zpl'  => 'application/pdf',
                                ];
                                $mimeType = $mimeMap[$mimeType] ?? 'image/gif';

                                $imageBase64Src = 'data:' . $mimeType . ';base64,' . $graphicImage;

                                $html = '<html><body style="margin:0;padding:0;"><img src="' . $imageBase64Src . '" style="width:100%;height:auto;"></body></html>';
                                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
                                $pdf->setPaper([0, 0, 400, 600], 'portrait');
                                $pdfBase64 = base64_encode($pdf->output());
                            }
                        }
                    }
                }
                // Ship Global case: response_status_description = "Ship Global order created"
                elseif (stripos($statusDescription, 'Ship Global') !== false) {
                    // Extract pdf_base64 from raw_response
                    if (isset($rawResponse['data']['pdf_base64'])) {
                        $pdfBase64 = $rawResponse['data']['pdf_base64'];
                    } elseif (isset($rawResponse['pdf_base64'])) {
                        $pdfBase64 = $rawResponse['pdf_base64'];
                    }
                }

                // If we found a label from shipment_tracking, return it
                if ($pdfBase64) {
                    $awbNumber = DB::table('shipper_info')->where('id', $shipperId)->value('awb_number');
                    return response()->json([
                        'success'    => true,
                        'pdf_base64' => $pdfBase64,
                        'awb_number' => $awbNumber,
                        'source'     => 'shipment_tracking',
                    ]);
                }
            }

            // Fallback: Generate label using Dompdf from label-pdf template
            $shipment = DB::table('shipment_invoice')
                ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
                ->leftJoin('customers', 'shipper_info.customer_id', '=', 'customers.id')
                ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
                ->where('shipment_invoice.id', $request->shipment_id)
                ->select(
                    'shipment_invoice.id',
                    'shipment_invoice.invoice_number',
                    'shipment_invoice.invoice_date',
                    'shipment_invoice.invoice_amount',
                    'shipment_invoice.invoice_currency',
                    'shipper_info.awb_number',
                    'shipper_info.company_name as shipper_company',
                    'shipper_info.contact_person as shipper_contact',
                    'shipper_info.address_line1 as shipper_address_line1',
                    'shipper_info.address_line2 as shipper_address_line2',
                    'shipper_info.address_line3 as shipper_address_line3',
                    'shipper_info.pincode as shipper_pincode',
                    'shipper_info.city as shipper_city',
                    'shipper_info.state as shipper_state',
                    'shipper_info.phone_number as shipper_phone',
                    'customers.first_name',
                    'customers.last_name',
                    'consignee_info.consignee_name',
                    'consignee_info.address_line1 as consignee_address_line1',
                    'consignee_info.address_line2 as consignee_address_line2',
                    'consignee_info.address_line3 as consignee_address_line3',
                    'consignee_info.zip_code as consignee_zip_code',
                    'consignee_info.city as consignee_city',
                    'consignee_info.state as consignee_state',
                    'consignee_info.phone_number as consignee_phone'
                )
                ->first();

            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found.'
                ]);
            }

            $labelHtml = view('admin.label-pdf', compact('shipment'))->render();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($labelHtml);
            $pdf->setPaper([0, 0, 280, 400], 'portrait');
            $pdfBase64 = base64_encode($pdf->output());

            return response()->json([
                'success'    => true,
                'pdf_base64' => $pdfBase64,
                'awb_number' => $shipment->awb_number,
                'source'     => 'dompdf_fallback',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Call the Delhivery API to create a pickup/shipment.
     *
     * @param int $shipmentId
     * @return array
     */
    private function callDelhiveryApi($shipmentId)
    {
        try {
            // Fetch shipment with all related data
            $shipment = DB::table('shipment_invoice')
                ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
                // i want to join shipment_invoice_items
                ->join('shipment_invoice_items', 'shipment_invoice.id', '=', 'shipment_invoice_items.invoice_id')
                ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
                ->leftJoin('package_dimension', 'shipper_info.id', '=', 'package_dimension.shipper_id')
                ->where('shipment_invoice.id', $shipmentId)
                ->select(
                    'shipment_invoice.id',
                    'shipment_invoice.invoice_number',
                    'shipment_invoice.invoice_amount',
                    'shipment_invoice.invoice_currency',
                    'shipment_invoice.reference_number',
                    'shipper_info.id as shipper_id',
                    'shipper_info.company_name',
                    'shipper_info.contact_person',
                    'shipper_info.address_line1',
                    'shipper_info.address_line2',
                    'shipper_info.address_line3',
                    'shipper_info.pincode',
                    'shipper_info.city as shipper_city',
                    'shipper_info.state as shipper_state',
                    'shipper_info.phone_number as shipper_phone',
                    'shipper_info.email as shipper_email',
                    'shipper_info.awb_number',
                    'consignee_info.consignee_name',
                    'consignee_info.contact_person as consignee_contact',
                    'consignee_info.address_line1 as consignee_add1',
                    'consignee_info.address_line2 as consignee_add2',
                    'consignee_info.address_line3 as consignee_add3',
                    'consignee_info.zip_code as consignee_pin',
                    'consignee_info.city as consignee_city',
                    'consignee_info.state as consignee_state',
                    'consignee_info.phone_number as consignee_phone',
                    'consignee_info.email as consignee_email',
                    'package_dimension.actual_weight_kg',
                    'package_dimension.length_cm',
                    'package_dimension.width_cm as pkg_width',
                    'package_dimension.height_cm as pkg_height',
                    'shipment_invoice_items.description'
                )
                ->first();

            if (!$shipment) {
                return ['success' => false, 'message' => 'Shipment data not found for Delhivery API call.'];
            }

            // Build the full address string for shipper (origin)
            $shipperAddress = trim(
                ($shipment->address_line1 ?? '') . ' ' .
                ($shipment->address_line2 ?? '') . ' ' .
                ($shipment->address_line3 ?? '')
            );

            // Determine payment mode based on incoterms or default to prepaid
            $paymentMode = 'prepaid';

            // Build shipments array for Delhivery API with shipper_info details
            $shipmentsData = [
                [
                    'name' => $shipment->company_name ?? $shipment->contact_person ?? 'Shipper',
                    'add' => $shipperAddress ?: 'Address not provided',
                    'pin' => $shipment->pincode ?? '',
                    'city' => $shipment->shipper_city ?? '',
                    'state' => $shipment->shipper_state ?? '',
                    'country' => 'India',
                    'phone' => $shipment->shipper_phone ?? '',
                    'order' => $shipment->reference_number ?? $shipment->invoice_number ?? '',
                    'payment_mode' => $paymentMode,
                    'quantity' => 1,
                    'weight' => $shipment->actual_weight_kg ?? 0,
                    'total_amount' => $shipment->invoice_amount ?? 0,
                    'products_desc' => $shipment->description ?? '',
                    'cod_amount' => $paymentMode === 'COD' ? ($shipment->invoice_amount ?? 0) : 0,
                    'shipping_mode' => 'Surface',
                    'shipment_width' => $shipment->pkg_width ?? 0,
                    'shipment_length' => $shipment->pkg_length ?? 0,
                    'shipment_height' => $shipment->pkg_height ?? 0,
                    'end_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
                ]
            ];

            // Build pickup_location object - name must remain unchanged as specified
            $pickupLocation = [
                'name' => 'ac549e-UNITEDWORLDWIDECOURI-do',
            ];

            // Build the full data structure
            $data = [
                'shipments' => $shipmentsData,
                'pickup_location' => $pickupLocation,
            ];

            // Make the API call to Delhivery
            // Note: asForm() sets Content-Type to application/x-www-form-urlencoded automatically
            // The Delhivery API expects form-encoded body with Accept: application/json header
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Token 462d4dd4644874ba774fa599aef160a97ed3fa7f',
            ])->asForm()->post('https://track.delhivery.com/api/cmu/create.json', [
                'format' => 'json',
                'data' => json_encode($data),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                // Check the inner data.success field — Delhivery can return HTTP 200
                // but with data.success = false (e.g. "Duplicate order id")
                $innerSuccess = true;
                if (isset($apiResponse['success']) && $apiResponse['success'] === false) {
                    $innerSuccess = false;
                }

                if ($innerSuccess) {
                    return [
                        'success' => true,
                        'message' => 'Delhivery pickup created successfully.',
                        'data' => $apiResponse,
                    ];
                } else {
                    // Extract error details from the Delhivery response
                    $errorMessage = 'Delhivery pickup creation failed.';
                    if (isset($apiResponse['rmk']) && !empty($apiResponse['rmk'])) {
                        $errorMessage = is_array($apiResponse['rmk']) ? implode(', ', $apiResponse['rmk']) : $apiResponse['rmk'];
                    }
                    // Also check packages for per-package error remarks
                    if (isset($apiResponse['packages']) && is_array($apiResponse['packages'])) {
                        foreach ($apiResponse['packages'] as $pkg) {
                            if (isset($pkg['remarks']) && is_array($pkg['remarks']) && !empty($pkg['remarks'])) {
                                $errorMessage .= ' - ' . implode(', ', $pkg['remarks']);
                            }
                            if (isset($pkg['status']) && $pkg['status'] === 'Fail') {
                                $errorMessage .= ' (Status: Fail)';
                            }
                        }
                    }
                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'data' => $apiResponse,
                    ];
                }
            } else {
                $apiResponse = $response->json();
                $errorMessage = 'Delhivery API returned error.';
                if (is_array($apiResponse)) {
                    // Delhivery sometimes returns errors in various formats
                    if (isset($apiResponse['error'])) {
                        $errorMessage = $apiResponse['error'];
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    } elseif (isset($apiResponse['rmk'])) {
                        $errorMessage = $apiResponse['rmk'];
                    }
                }
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $apiResponse,
                    'status_code' => $response->status(),
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Delhivery API call failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Display delivery persons management page.
     */
    public function deliveryPersons()
    {
        $deliveryPersons = Admin::where('type', 'Delivery_person')
            ->orderBy('name')
            ->get();

        return view('admin.delivery-persons', compact('deliveryPersons'));
    }

    /**
     * Store a new delivery person.
     */
    public function storeDeliveryPerson(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:admin_user,email',
                'mobile' => 'nullable|string|max:20',
                'password' => 'required|string|min:6',
                'designation' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1',
            ]);

            Admin::create([
                'type' => 'Delivery_person',
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'password' => bcrypt($validated['password']),
                'designation' => $validated['designation'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'status' => $validated['status'] ?? 1,
            ]);

            return redirect()->route('admin.delivery-persons')
                ->with('success', 'Delivery person added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.delivery-persons')
                ->with('error', 'Validation failed: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('admin.delivery-persons')
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update an existing delivery person.
     */
    public function updateDeliveryPerson(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:admin_user,email,' . $id,
                'mobile' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6',
                'designation' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1',
            ]);

            $deliveryPerson = Admin::where('type', 'Delivery_person')->findOrFail($id);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'designation' => $validated['designation'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'status' => $validated['status'] ?? 1,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = bcrypt($validated['password']);
            }

            $deliveryPerson->update($updateData);

            return redirect()->route('admin.delivery-persons')
                ->with('success', 'Delivery person updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.delivery-persons')
                ->with('error', 'Validation failed: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('admin.delivery-persons')
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the Create User management page.
     * Lists all admin users (except Super Admin) with their module access.
     */
    public function createUser()
    {
        $users = Admin::where('type', '!=', 'Delivery_person')
            ->orderByDesc('id')
            ->get();

        $modules = Admin::getModules();

        return view('admin.create-user', compact('users', 'modules'));
    }

    /**
     * Store a newly created admin user with module-wise access.
     */
    public function storeUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:admin_user,email',
                'mobile' => 'nullable|string|max:20',
                'password' => 'required|string|min:6',
                'designation' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1',
                'type' => 'required|in:Admin,Super Admin',
                'module_access' => 'nullable|array',
                'module_access.*' => 'string|in:' . implode(',', Admin::getModuleKeys()),
            ]);

            // Super Admin gets all modules; Admin gets only selected ones
            $moduleAccess = $validated['type'] === 'Super Admin'
                ? Admin::getModuleKeys()
                : ($validated['module_access'] ?? []);

            Admin::create([
                'type' => $validated['type'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'password' => bcrypt($validated['password']),
                'designation' => $validated['designation'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'status' => $validated['status'] ?? 1,
                'module_access' => $moduleAccess,
            ]);

            return redirect()->route('admin.create-user')
                ->with('success', 'User created successfully with module access!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Validation failed: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update an existing admin user and their module access.
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = Admin::where('type', '!=', 'Delivery_person')->findOrFail($id);

            // Prevent editing a Super Admin account
            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.create-user')
                    ->with('error', 'Super Admin account cannot be modified.');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:admin_user,email,' . $id,
                'mobile' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6',
                'designation' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1',
                'type' => 'required|in:Admin,Super Admin',
                'module_access' => 'nullable|array',
                'module_access.*' => 'string|in:' . implode(',', Admin::getModuleKeys()),
            ]);

            // Super Admin gets all modules; Admin gets only selected ones
            $moduleAccess = $validated['type'] === 'Super Admin'
                ? Admin::getModuleKeys()
                : ($validated['module_access'] ?? []);

            $updateData = [
                'type' => $validated['type'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'designation' => $validated['designation'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'status' => $validated['status'] ?? 1,
                'module_access' => $moduleAccess,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = bcrypt($validated['password']);
            }

            $user->update($updateData);

            return redirect()->route('admin.create-user')
                ->with('success', 'User updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Validation failed: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete an admin user.
     */
    public function deleteUser($id)
    {
        try {
            $user = Admin::where('type', '!=', 'Delivery_person')->findOrFail($id);

            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.create-user')
                    ->with('error', 'Super Admin account cannot be deleted.');
            }

            // Prevent self-deletion
            if (Auth::guard('admin')->check() && Auth::guard('admin')->id() == $id) {
                return redirect()->route('admin.create-user')
                    ->with('error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()->route('admin.create-user')
                ->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function createShipment()
    {
        return view('admin.create-shipment');
    }


    


    public function csb5Form()
    {
        return view('admin.csb5-form');
    }

    public function formKyc()
    {
        return view('admin.form-kyc');
    }
    


    // Network Page Management Methods


    // FAQ Management Methods


    // Testimonials / Reviews Management Methods


    // Blog Management Methods


    // ========== E-Book Management ==========


    // ========== Track Order Page Management ==========

    public function changeTrackOrder()
    {
        $trackOrders = \App\Models\TrackOrderPage::ordered()->get();
        return view('admin.change-track-order', compact('trackOrders'));
    }

    public function createTrackOrder()
    {
        $trackOrder = new \App\Models\TrackOrderPage();
        return view('admin.edit-track-order', compact('trackOrder'));
    }

    public function editTrackOrder($id)
    {
        try {
            $trackOrder = \App\Models\TrackOrderPage::findOrFail($id);
            return view('admin.edit-track-order', compact('trackOrder'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-track-order')
                ->with('error', 'Track Order content not found.');
        }
    }

    public function getTrackOrder($id)
    {
        try {
            $trackOrder = \App\Models\TrackOrderPage::findOrFail($id);
            return response()->json([
                'success' => true,
                'trackOrder' => $trackOrder
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Track Order content not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeTrackOrder(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $trackOrder = new \App\Models\TrackOrderPage();
            $trackOrder->fill($request->except(['image']));

            // Explicitly set as a track order item (not page content)
            $trackOrder->section = null;
            $trackOrder->item_key = null;
            $trackOrder->content = null;

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $trackOrder->image = $imagePath;
            }

            $trackOrder->status = $request->status ?? 'Active';
            $trackOrder->sort_order = $request->sort_order ?? 0;
            $trackOrder->save();

            return response()->json([
                'success' => true,
                'message' => 'Track Order content created successfully!',
                'track_order_id' => $trackOrder->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateTrackOrder(Request $request, $id)
    {
        try {
            $trackOrder = \App\Models\TrackOrderPage::findOrFail($id);

            if ($trackOrder->section) {
                // ── Page content row: save individual JSON fields ──
                $request->validate([
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                ]);

                $trackOrder->content = $request->json_fields ?? [];
                $trackOrder->status = $request->status ?? 'Active';
                $trackOrder->sort_order = $request->sort_order ?? 0;
                $trackOrder->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Page content updated successfully!'
                ]);
            }

            // ── Track Order item row ──
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $trackOrder->fill($request->except(['image']));

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $trackOrder->image = $imagePath;
            }

            $trackOrder->status = $request->status ?? 'Active';
            $trackOrder->sort_order = $request->sort_order ?? 0;
            $trackOrder->save();

            return response()->json([
                'success' => true,
                'message' => 'Track Order content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteTrackOrder($id)
    {
        try {
            $trackOrder = \App\Models\TrackOrderPage::findOrFail($id);
            $trackOrder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Track Order content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Webinar Page Management ==========

    public function changeWebinar()
    {
        $webinars = \App\Models\WebinarPage::ordered()->get();
        return view('admin.change-webinar', compact('webinars'));
    }

    public function createWebinar()
    {
        $webinar = new \App\Models\WebinarPage();
        return view('admin.edit-webinar', compact('webinar'));
    }

    public function editWebinar($id)
    {
        try {
            $webinar = \App\Models\WebinarPage::findOrFail($id);
            return view('admin.edit-webinar', compact('webinar'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-webinar')
                ->with('error', 'Webinar content not found.');
        }
    }

    public function getWebinar($id)
    {
        try {
            $webinar = \App\Models\WebinarPage::findOrFail($id);
            return response()->json([
                'success' => true,
                'webinar' => $webinar
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Webinar content not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeWebinar(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $webinar = new \App\Models\WebinarPage();
            $webinar->fill($request->except(['image']));

            // Explicitly set as a webinar item (not page content)
            $webinar->section = null;
            $webinar->item_key = null;
            $webinar->content = null;

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $webinar->image = $imagePath;
            }

            $webinar->status = $request->status ?? 'Active';
            $webinar->sort_order = $request->sort_order ?? 0;
            $webinar->save();

            return response()->json([
                'success' => true,
                'message' => 'Webinar content created successfully!',
                'webinar_id' => $webinar->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateWebinar(Request $request, $id)
    {
        try {
            $webinar = \App\Models\WebinarPage::findOrFail($id);

            if ($webinar->section) {
                // ── Page content row: save individual JSON fields ──
                $request->validate([
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                ]);

                $webinar->content = $request->json_fields ?? [];
                $webinar->status = $request->status ?? 'Active';
                $webinar->sort_order = $request->sort_order ?? 0;
                $webinar->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Page content updated successfully!'
                ]);
            }

            // ── Webinar item row ──
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'json_fields' => 'nullable|array',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $webinar->fill($request->except(['image']));

            // Save content JSON fields (category_tag, read_time, author_name, etc.)
            if ($request->has('json_fields')) {
                $existingContent = $webinar->content ?? [];
                $webinar->content = array_merge($existingContent, $request->json_fields);
            }

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $webinar->image = $imagePath;
            }

            $webinar->status = $request->status ?? 'Active';
            $webinar->sort_order = $request->sort_order ?? 0;
            $webinar->save();

            return response()->json([
                'success' => true,
                'message' => 'Webinar content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteWebinar($id)
    {
        try {
            $webinar = \App\Models\WebinarPage::findOrFail($id);
            $webinar->delete();

            return response()->json([
                'success' => true,
                'message' => 'Webinar content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Currency Calculator Page Management ==========


    // ========== World Weather Page Management ==========


    // ========== World Time Page Management ==========


    // ========== Express Air Freight Solutions Page Management ==========


    /**
     * CKEditor Image Upload - stores images in blog_image folder
     */


    // ========== Barcode Generator Page Management ==========


    // ========== Shipping Rate Calculator Page Management ==========


    // ========== HSN Finder Page Management ==========


    // ========== Partnership Page Management ==========


    // =============================================
    // DOCUMENT DOWNLOAD PAGE MANAGEMENT
    // =============================================


    // ========== Common Stats (Fact Number Section) Management ==========


    // ========== Partners Section (Logos) Management ==========


    public function kycPending()
    {
        $kycDetails = \App\Models\KycDetail::with(['customer.csbForm'])
            ->whereIn('kyc_status', ['pending', 'under_review'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.kyc-pending', compact('kycDetails'));
    }

    public function kycApproved()
    {
        $approvedKycDetails = \App\Models\KycDetail::with(['customer.csbForm'])
            ->where('kyc_status', 'approved')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.kyc-approved', compact('approvedKycDetails'));
    }

    /**
     * Reset a customer's password (admin action).
     */
    public function resetCustomerPassword(Request $request, $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $customer->password_hash = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $customer->setRememberToken(\Illuminate\Support\Str::random(60));
        $customer->save();

        return redirect()->back()
            ->with('success', 'Password for ' . $customer->first_name . ' ' . $customer->last_name . ' has been reset successfully.');
    }

    public function approveKyc($id)
    {
        $kycDetail = \App\Models\KycDetail::findOrFail($id);
        $kycDetail->kyc_status = 'approved';
        $kycDetail->save();

        // Clone all default rates (customer_id = 0) from courier_rates for the approved customer
        $defaultRates = \App\Models\CourierRate::where('customer_id', 0)->get();

        foreach ($defaultRates as $rate) {
            $newRate = $rate->replicate();
            $newRate->customer_id = $kycDetail->customer_id;
            $newRate->save();
        }

        $ratesCount = $defaultRates->count();

        return redirect()->route('admin.kyc-pending')
            ->with('success', 'KYC for ' . ($kycDetail->organization_name ?? 'Customer #' . $kycDetail->customer_id) . ' has been approved successfully. ' . $ratesCount . ' courier rates copied.');
    }

    public function rejectKyc($id)
    {
        $kycDetail = \App\Models\KycDetail::findOrFail($id);
        $kycDetail->kyc_status = 'rejected';
        $kycDetail->save();

        return redirect()->route('admin.kyc-pending')
            ->with('success', 'KYC for ' . ($kycDetail->organization_name ?? 'Customer #' . $kycDetail->customer_id) . ' has been rejected.');
    }

    /**
     * Admin recharges a customer's wallet by a given amount.
     */
    public function rechargeCustomerWallet(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
            ]);

            $amount = (float) $validated['amount'];

            // Find or create wallet for the customer
            $wallet = Wallet::firstOrCreate(
                ['customer_id' => $customer->id],
                ['balance' => 0]
            );

            DB::transaction(function () use ($wallet, $amount, $customer) {
                $wallet->increment('balance', $amount);
                $wallet->refresh();

                WalletTransaction::create([
                    'customer_id'   => $customer->id,
                    'type'          => 'credit',
                    'reason'        => 'recharge',
                    'amount'        => $amount,
                    'balance_after' => $wallet->balance,
                    'reference'     => 'ADMIN-' . now()->format('ymd'),
                    'description'   => 'Wallet recharge of ₹' . number_format($amount, 2) . ' by Admin',
                ]);
            });

            $wallet->refresh();

            return response()->json([
                'success'     => true,
                'message'     => 'Wallet recharged successfully! ₹' . number_format($amount, 2) . ' has been added to ' . $customer->first_name . ' ' . $customer->last_name . "'s wallet.",
                'new_balance' => (float) $wallet->balance,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Admin wallet recharge error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing recharge: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function manageRate()
    {
        $defaultRates = \App\Models\CourierRate::with('service')
            ->where('customer_id', 0)
            ->orderBy('service_id')
            ->orderBy('zone_no')
            ->orderBy('wt_range_start')
            ->get();

        $customers = \App\Models\Customer::orderBy('first_name')->get();

        $services = \App\Models\CourierService::orderBy('network')->orderBy('method')->get();

        // ------------------------------------------------------------------
        // Zone lookup map for the "Zone Name" / "Zone Category" columns.
        //
        // courier_rates.zone_no  matches  zone.zone_number_testing  (0-13).
        // Multiple zones can share the same zone_number_testing, so for each
        // (destination_id, zone_number_testing) pair we pre-compute:
        //   - category : the zone_category ('state' or 'zipcode')
        //   - names    : comma-separated zone names (for the table columns)
        //   - nameList : array of individual zone names (for the Add Rate
        //                modal's Select2 dropdown, where each name becomes a
        //                separate searchable option)
        //   - count    : number of zones in this group
        //
        // This is built once server-side and passed to the view so the
        // Default Rate table can render the columns without extra queries,
        // and the Customer Rate tab can look them up in JS. The Add Rate
        // modal's Select2 zone dropdown shows each zone name as a separate
        // option so the admin can search for a specific state/postal code.
        // ------------------------------------------------------------------
        $zoneLookup = [];
        // Increase GROUP_CONCAT limit so the full list of zone names (which
        // can be hundreds of postal codes for zipcode-category zones) is
        // not truncated by MySQL's default 1024-byte limit.
        \DB::statement('SET SESSION group_concat_max_len = 1000000');
        // A single zone_name may now have multiple zone_codes, so we
        // GROUP_CONCAT DISTINCT zone_name to avoid the same name appearing
        // several times in the comma-separated display / Select2 dropdown.
        $zones = \App\Models\Zone::selectRaw('destination_id, zone_number_testing, zone_category, COUNT(*) as cnt, GROUP_CONCAT(DISTINCT zone_name SEPARATOR ", ") as names')
            ->groupBy('destination_id', 'zone_number_testing', 'zone_category')
            ->get();

        foreach ($zones as $z) {
            $zoneNo = (int) $z->zone_number_testing;
            $category = $z->zone_category ?: 'state';
            $count = (int) $z->cnt;
            // Comma-separated string for the table columns.
            $nameDisplay = $z->names ?: ('Zone ' . $zoneNo);
            // Array of individual names for the Add Rate modal dropdown,
            // where each name becomes a separate searchable Select2 option.
            $nameList = $z->names
                ? array_map('trim', explode(',', $z->names))
                : ['Zone ' . $zoneNo];
            $zoneLookup[$z->destination_id][$zoneNo] = [
                'category' => $category,
                'names'    => $nameDisplay,
                'nameList' => $nameList,
                'count'    => $count,
            ];
        }

        // Map CourierService.country -> Destination.id so we can look up
        // zones for a given rate's service. courier_services.country now
        // stores the same short code as destinations.country_code
        // (e.g. "US", "UK", "CA", "AUS"). We build a lookup that tries several
        // match strategies (code, country_code, name, plus legacy friendly
        // names) so the mapping stays correct regardless of which format is
        // used.
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $countryToDestId = [];
        foreach ($destinations as $dest) {
            $countryToDestId[$dest->id] = [
                'name'         => $dest->name,
                'code'         => $dest->code,
                'country_code' => $dest->country_code,
            ];
        }

        // Helper: given a service's country string, find the matching
        // destination_id. Tries (in order): exact code, exact country_code,
        // case-insensitive name contains, code prefix.
        $countryToDestinationId = [];
        foreach ($destinations as $dest) {
            $countryToDestinationId[strtolower($dest->code)] = $dest->id;
            $countryToDestinationId[strtolower($dest->country_code)] = $dest->id;
            $countryToDestinationId[strtolower($dest->name)] = $dest->id;
        }
        // Also map the friendly country names used by courier_services.
        $friendlyMap = [
            'us'        => 1,
            'usa'       => 1,
            'uk'        => 2,
            'united kingdom' => 2,
            'canada'    => 3,
            'ca'        => 3,
            'australia' => 4,
            'aus'       => 4,
            'au'        => 4,
        ];
        foreach ($friendlyMap as $k => $v) {
            if (!isset($countryToDestinationId[$k])) {
                $countryToDestinationId[$k] = $v;
            }
        }

        // ------------------------------------------------------------------
        // Map each destination NAME to the matching courier_services.country
        // value (the short code, e.g. "US", "UK", "CA", "AUS"). Kept for
        // backward compatibility — all country <select> dropdowns in the
        // manage-rate view now use the destination country_code as the
        // option value (matching the courier_services.country short code),
        // so this name-based lookup is rarely needed. It still lets the
        // service dropdown be filtered by the selected country.
        //
        // We reuse countryToDestinationId (which already resolves every
        // friendly-name/code variant to a destination_id) by inverting it:
        // for each distinct courier_services.country, look up its
        // destination_id, then map every destination's name -> that
        // service-country string.
        // ------------------------------------------------------------------
        $serviceCountries = \App\Models\CourierService::distinct()
            ->pluck('country')
            ->filter()
            ->unique()
            ->values();
        $destIdToServiceCountry = [];
        foreach ($serviceCountries as $sc) {
            $key = strtolower(trim((string) $sc));
            $destId = $countryToDestinationId[$key] ?? null;
            if ($destId) {
                $destIdToServiceCountry[$destId] = $sc;
            }
        }
        $destNameToServiceCountry = [];
        foreach ($destinations as $dest) {
            if (isset($destIdToServiceCountry[$dest->id])) {
                $destNameToServiceCountry[$dest->name] = $destIdToServiceCountry[$dest->id];
            }
        }

        return view('admin.manage-rate', compact('defaultRates', 'customers', 'services', 'zoneLookup', 'countryToDestId', 'countryToDestinationId', 'destinations', 'destNameToServiceCountry'));
    }

    /**
     * Service page content management page.
     *
     * Backs the admin/change-service route and the change-service.blade.php
     * view. Loads every ServicePage record (active AND inactive) ordered by
     * sort_order so the admin can view and edit all service-page content
     * sections (services, faq, stats, partners, testimonials, ...).
     *
     * NOTE: This is distinct from services() below, which manages the
     * CourierService catalogue (enable/disable rate services). The route
     * /change-service maps here; /services maps to services().
     */
    public function service()
    {
        $serviceContent = \App\Models\ServicePage::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.change-service', compact('serviceContent'));
    }

    /**
     * Courier Services management page.
     *
     * Lists every courier service (enabled AND disabled) so the admin can
     * see the full catalogue and toggle each service's status. Only enabled
     * services (status = 1) show rates to customers on the create-shipment
     * page, bulk upload and bulk rate calculation.
     */
    public function services()
    {
        // Show all services so disabled ones can be re-enabled from this page.
        $services = \App\Models\CourierService::orderBy('network')->orderBy('method')->get();

        return view('admin.services', compact('services'));
    }

    /**
     * Toggle the enabled/disabled status of a courier service.
     *
     *  - status = 1  -> service shows rates to customers
     *  - status = 0  -> service is hidden from rate calculations
     *
     * Mirrors toggleCustomerStatus(): a plain form POST that redirects back
     * with a flash message. This is the proven, reliable pattern used across
     * the admin panel (no AJAX/CSRF-token juggling required).
     */
    public function toggleServiceStatus($id)
    {
        $service = \App\Models\CourierService::findOrFail($id);

        $service->status = $service->status ? 0 : 1;
        $service->save();

        $action = $service->status ? 'ENABLED' : 'DISABLED';
        $message = 'Service "' . $service->method . '" has been ' . $action . '.';

        return redirect()->back()->with('success', $message);
    }

    public function getCustomerRates(Request $request)
    {
        $customerId = $request->customer_id;

        $rates = \App\Models\CourierRate::with('service')
            ->where('customer_id', $customerId)
            ->orderBy('service_id')
            ->orderBy('zone_no')
            ->orderBy('wt_range_start')
            ->get();

        // Include customer details (name, email, phone) and the current
        // end_date (taken from the first rate, since all rates for a
        // customer share the same end_date) so the manage-rate page can
        // show them in the end_date popup.
        $customer = \App\Models\Customer::find($customerId);
        $customerInfo = null;
        $currentEndDate = null;
        if ($customer) {
            $customerInfo = [
                'id'            => $customer->id,
                'first_name'    => $customer->first_name,
                'last_name'     => $customer->last_name,
                'email'         => $customer->email,
                'phone_number'  => $customer->phone_number,
                'full_name'     => trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
            ];
            $firstRate = $rates->first();
            if ($firstRate && $firstRate->end_date) {
                $currentEndDate = $firstRate->end_date instanceof \DateTime
                    ? $firstRate->end_date->format('Y-m-d')
                    : (string) $firstRate->end_date;
            }
        }

        return response()->json([
            'rates'           => $rates,
            'customer'        => $customerInfo,
            'current_end_date' => $currentEndDate,
        ]);
    }

    /**
     * Export rates for one or more selected customers in the upload format.
     */
    public function exportCustomerRates(Request $request)
    {
        $customerIds = array_values(array_filter(array_map('intval', (array) $request->input('customer_ids', []))));
        if (empty($customerIds)) {
            abort(422, 'Please select at least one customer.');
        }

        $serviceId = $request->input('service_id');
        $country = trim((string) $request->input('country', ''));
        $query = \App\Models\CourierRate::with(['service', 'customer'])
            ->whereIn('customer_id', $customerIds)
            ->orderBy('customer_id')
            ->orderBy('service_id')
            ->orderBy('zone_no')
            ->orderBy('wt_range_start');
        if ($serviceId !== null && $serviceId !== '') {
            $query->where('service_id', (int) $serviceId);
        }
        if ($country !== '') {
            $query->whereHas('service', function ($serviceQuery) use ($country) {
                $serviceQuery->where('country', $country);
            });
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['Customer ID', 'Customer Name', 'Network', 'Service Code', 'Method', 'TAT', 'Weight Start (gm)', 'Weight End (gm)', 'Zone No', 'Zone Category', 'Price', 'Default', 'Start Date', 'End Date'];

        // PhpSpreadsheet 2.x+ removed setCellValueByColumnAndRow(), so we
        // build column letters (A, B, C, ...) and use coordinate-based addressing.
        $columnLetters = [];
        for ($i = 0; $i < count($headers); $i++) {
            $columnLetters[$i] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetters[$i] . '1', $headers[$i]);
        }

        $rowNumber = 2;
        foreach ($query->get() as $rate) {
            $service = $rate->service;
            $customer = $rate->customer;
            $startDate = $rate->start_date
                ? ($rate->start_date instanceof \DateTime ? $rate->start_date->format('Y-m-d') : (string) $rate->start_date)
                : '';
            $endDate = $rate->end_date
                ? ($rate->end_date instanceof \DateTime ? $rate->end_date->format('Y-m-d') : (string) $rate->end_date)
                : '';
            $values = [
                $rate->customer_id,
                trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
                $service->network ?? '',
                $service->service_code ?? '',
                $service->method ?? '',
                $service->tat ?? '',
                $rate->wt_range_start,
                $rate->wt_range_end,
                $rate->zone_no,
                '',
                $rate->price,
                $rate->is_default ? 'Yes' : 'No',
                $startDate,
                $endDate,
            ];
            foreach ($values as $index => $value) {
                $sheet->setCellValue($columnLetters[$index] . $rowNumber, $value);
            }
            $rowNumber++;
        }

        $lastColumn = $columnLetters[count($columnLetters) - 1];
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        foreach ($columnLetters as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $fileName = 'customer-rates-' . date('Y-m-d-His') . '.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function updateRate(Request $request, $id)
    {
        $rate = \App\Models\CourierRate::findOrFail($id);

        // Only default rates (is_default = 1) can be changed from the admin panel
        if (!$rate->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'This rate is marked as non-default and cannot be edited from the admin panel.',
            ], 403);
        }

        $rate->price = $request->price;
        $rate->save();

        // Propagate the default rate change to all customers who still use the default rate (is_default = 1).
        // Customer-specific rates that have been customized (is_default = 0) are left untouched.
        $updatedCustomers = \App\Models\CourierRate::where('customer_id', '!=', 0)
            ->where('is_default', 1)
            ->where('service_id', $rate->service_id)
            ->where('wt_range_start', $rate->wt_range_start)
            ->where('wt_range_end', $rate->wt_range_end)
            ->where('zone_no', $rate->zone_no)
            ->update(['price' => $request->price]);

        $message = 'Rate updated successfully.';
        if ($updatedCustomers > 0) {
            $message .= ' The same rate was also updated for ' . $updatedCustomers . ' customer(s) using the default rate.';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function updateCustomerRate(Request $request, $id)
    {
        $rate = \App\Models\CourierRate::findOrFail($id);

        $rate->price = $request->price;
        // Once a customer rate is customized, mark it as non-default so it is no longer
        // overwritten when the corresponding default rate is changed from the Default Rates table.
        $rate->is_default = false;
        $rate->save();

        return response()->json(['success' => true, 'message' => 'Customer rate updated successfully.']);
    }

    /**
     * Update the end_date for ALL courier_rates rows belonging to a given
     * customer at once.
     *
     * Called from the manage-rate page when the admin clicks on the
     * end_date cell of any customer rate row — a popup shows the customer
     * details and an editable end_date input; on save this endpoint
     * bulk-updates every rate row for that customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $customerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomerEndDate(Request $request, $customerId)
    {
        $validated = $request->validate([
            'end_date' => ['required', 'date'],
        ]);

        // Enforce that the end_date is at least tomorrow (today + 1 day).
        // The admin must never be able to set an end_date of today or earlier.
        $tomorrow = \Carbon\Carbon::tomorrow()->toDateString();
        if ($validated['end_date'] < $tomorrow) {
            return response()->json([
                'success' => false,
                'message' => 'The end date must be tomorrow (' . $tomorrow . ') or a later date.',
            ], 422);
        }

        $customer = \App\Models\Customer::find($customerId);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $updated = \App\Models\CourierRate::where('customer_id', $customerId)
            ->update(['end_date' => $validated['end_date']]);

        return response()->json([
            'success'  => true,
            'message'  => 'End date updated successfully for ' . $updated . ' rate(s) of ' . trim($customer->first_name . ' ' . $customer->last_name) . '.',
            'updated'  => $updated,
            'end_date' => $validated['end_date'],
        ]);
    }

    /**
     * Bulk-update the end_date for ALL courier_rates rows belonging to
     * MULTIPLE customers at once.
     *
     * Called from the manage-rate page when the admin has selected several
     * customers in the dropdown and clicks the "End Date" button — a popup
     * shows how many customers are selected and an editable end_date input;
     * on save this endpoint bulk-updates every rate row for every selected
     * customer to the same end_date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMultipleCustomersEndDate(Request $request)
    {
        $validated = $request->validate([
            'customer_ids'   => ['required', 'array', 'min:1'],
            'customer_ids.*' => ['required', 'integer'],
            'end_date'       => ['required', 'date'],
        ]);

        // Enforce that the end_date is at least tomorrow (today + 1 day).
        $tomorrow = \Carbon\Carbon::tomorrow()->toDateString();
        if ($validated['end_date'] < $tomorrow) {
            return response()->json([
                'success' => false,
                'message' => 'The end date must be tomorrow (' . $tomorrow . ') or a later date.',
            ], 422);
        }

        // Cast all submitted IDs to integers (HTTP requests send strings).
        $customerIds = array_map('intval', $validated['customer_ids']);

        // Bulk-update every rate row for every selected customer in one query.
        $updated = \App\Models\CourierRate::whereIn('customer_id', $customerIds)
            ->update(['end_date' => $validated['end_date']]);

        $customerCount = count($customerIds);

        return response()->json([
            'success'        => true,
            'message'        => 'End date updated successfully for ' . $updated . ' rate(s) across ' . $customerCount . ' customer(s).',
            'updated'        => $updated,
            'customer_count' => $customerCount,
            'end_date'       => $validated['end_date'],
        ]);
    }

    /**
     * Apply a downloaded customer-rate Excel sheet as a new dated rate set.
     * Rows are matched by service, weight range and zone number.
     */
    public function updateNewCustomerRate(Request $request)
    {
        $validated = $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'integer|exists:customers,id',
            'service_id'  => 'nullable|integer|exists:courier_services,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'rate_file'   => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $filePath = $request->file('rate_file')->getRealPath();

        try {
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not read the uploaded file. Please upload the Excel file downloaded from customer rates.',
            ], 422);
        }

        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        if (count($rows) < 2) {
            return response()->json(['success' => false, 'message' => 'The uploaded file has no data rows.'], 422);
        }

        $normalizeHeader = function ($value) {
            return preg_replace('/\s+/', ' ', strtolower(trim((string) $value)));
        };

        // DataTables Excel exports can contain a title, customer details and
        // blank rows before the actual column header. Find that header row
        // instead of assuming it is always the first row.
        $headerRowIndex = null;
        $header = [];
        foreach (array_slice($rows, 0, 10, true) as $rowIndex => $candidateRow) {
            $candidateHeader = array_map($normalizeHeader, $candidateRow);
            $hasWeightStart = in_array('weight start', $candidateHeader, true)
                || in_array('weight start (gm)', $candidateHeader, true)
                || in_array('wt start', $candidateHeader, true)
                || in_array('wt_range_start', $candidateHeader, true);
            $hasWeightEnd = in_array('weight end', $candidateHeader, true)
                || in_array('weight end (gm)', $candidateHeader, true)
                || in_array('wt end', $candidateHeader, true)
                || in_array('wt_range_end', $candidateHeader, true);
            $hasZoneNo = in_array('zone no', $candidateHeader, true)
                || in_array('zone_no', $candidateHeader, true)
                || in_array('zone number', $candidateHeader, true)
                || in_array('zone', $candidateHeader, true);
            $hasPrice = in_array('price', $candidateHeader, true)
                || in_array('rate', $candidateHeader, true)
                || in_array('amount', $candidateHeader, true)
                || in_array('cost', $candidateHeader, true);

            if ($hasWeightStart && $hasWeightEnd && $hasZoneNo && $hasPrice) {
                $headerRowIndex = $rowIndex;
                $header = $candidateHeader;
                break;
            }
        }

        if ($headerRowIndex === null) {
            return response()->json([
                'success' => false,
                'message' => 'Could not find the rate table header. Please upload the downloaded customer rates Excel file.',
            ], 422);
        }

        $findColumn = function (array $names) use ($header) {
            foreach ($header as $index => $value) {
                if (in_array($value, $names, true)) {
                    return $index;
                }
            }
            return null;
        };

        $wtStartCol = $findColumn(['weight start', 'weight start (gm)', 'wt start', 'wt_range_start']);
        $wtEndCol   = $findColumn(['weight end', 'weight end (gm)', 'wt end', 'wt_range_end']);
        $zoneNoCol  = $findColumn(['zone no', 'zone_no', 'zone number', 'zone']);
        $priceCol   = $findColumn(['price', 'rate', 'amount', 'cost']);
        $customerIdCol = $findColumn(['customer id', 'customer_id', 'customerid']);
        $serviceCodeCol = $findColumn(['service code', 'service_code', 'servicecode']);
        $networkCol = $findColumn(['network']);
        $methodCol = $findColumn(['method']);

        if ($wtStartCol === null || $wtEndCol === null || $zoneNoCol === null || $priceCol === null || $customerIdCol === null
            || (!$validated['service_id'] && $serviceCodeCol === null && $networkCol === null && $methodCol === null)) {
            return response()->json([
                'success' => false,
                'message' => 'The file must contain Customer ID, Weight Start, Weight End, Zone No and Price. When All Services is selected, it must also contain Service Code, Network or Method.',
            ], 422);
        }

        $updated = 0;
        $skipped = 0;
        $notFound = 0;

        \DB::transaction(function () use ($rows, $headerRowIndex, $wtStartCol, $wtEndCol, $zoneNoCol, $priceCol, $customerIdCol, $serviceCodeCol, $networkCol, $methodCol, $validated, &$updated, &$skipped, &$notFound) {
            $normalizeValue = function ($value) {
                return strtolower(preg_replace('/\s+/', ' ', trim((string) $value)));
            };
            $normalizeNumber = function ($value) {
                return rtrim(rtrim(number_format((float) $value, 6, '.', ''), '0'), '.');
            };

            // Load the customer's rates once. Querying the database inside
            // the Excel-row loop makes large exports hit the request timeout.
            $existingRates = \App\Models\CourierRate::with('service')
                ->whereIn('customer_id', $validated['customer_ids'])
                ->when($validated['service_id'], function ($query) use ($validated) {
                    $query->where('service_id', $validated['service_id']);
                })
                ->get();
            $rateLookup = [];
            foreach ($existingRates as $existingRate) {
                $weightKey = $normalizeNumber($existingRate->wt_range_start) . '|'
                    . $normalizeNumber($existingRate->wt_range_end) . '|'
                    . (int) $existingRate->zone_no;
                $rateLookup['id:' . $existingRate->customer_id . '|' . $existingRate->service_id . '|' . $weightKey] = $existingRate;

                if (!$validated['service_id'] && $existingRate->service) {
                    $service = $existingRate->service;
                    foreach ([$service->service_code, $service->scode] as $code) {
                        $code = $normalizeValue($code);
                        if ($code !== '') {
                            $rateLookup['code:' . $existingRate->customer_id . '|' . $code . '|' . $weightKey] = $existingRate;
                        }
                    }
                    $network = $normalizeValue($service->network);
                    $method = $normalizeValue($service->method);
                    if ($network !== '' && $method !== '') {
                        $rateLookup['method:' . $existingRate->customer_id . '|' . $network . '|' . $method . '|' . $weightKey] = $existingRate;
                    }
                }
            }
            $pendingUpdates = [];

            foreach (array_slice($rows, $headerRowIndex + 1) as $row) {
                $wtStart = isset($row[$wtStartCol]) ? trim((string) $row[$wtStartCol]) : '';
                $wtEnd   = isset($row[$wtEndCol]) ? trim((string) $row[$wtEndCol]) : '';
                $zoneNo  = isset($row[$zoneNoCol]) ? trim((string) $row[$zoneNoCol]) : '';
                $price   = isset($row[$priceCol]) ? trim((string) $row[$priceCol]) : '';
                $customerId = isset($row[$customerIdCol]) ? (int) trim((string) $row[$customerIdCol]) : 0;
                $serviceCode = $serviceCodeCol !== null && isset($row[$serviceCodeCol]) ? trim((string) $row[$serviceCodeCol]) : '';
                $network = $networkCol !== null && isset($row[$networkCol]) ? trim((string) $row[$networkCol]) : '';
                $method = $methodCol !== null && isset($row[$methodCol]) ? trim((string) $row[$methodCol]) : '';

                if ($wtStart === '' && $wtEnd === '' && $zoneNo === '' && $price === '') {
                    continue;
                }
                if (!is_numeric($wtStart) || !is_numeric($wtEnd) || !is_numeric($zoneNo) || !is_numeric($price)
                    || (float) $wtEnd <= (float) $wtStart || (int) $zoneNo < 0 || (int) $zoneNo > 13) {
                    $skipped++;
                    continue;
                }
                // NOTE: $validated['customer_ids'] comes from the request as
                // an array of STRINGS (e.g. ["3","17"]), while $customerId is
                // cast to int above. Using strict comparison (true) here made
                // in_array(3, ["3","17"], true) return false, causing EVERY
                // row to be skipped with "No matching customer rates found".
                // Cast the validated IDs to int so the comparison is reliable.
                $validatedCustomerIds = array_map('intval', $validated['customer_ids']);
                if (!in_array($customerId, $validatedCustomerIds, true)
                    || (!$validated['service_id'] && $serviceCode === '' && ($network === '' || $method === ''))) {
                    $skipped++;
                    continue;
                }

                $weightKey = $normalizeNumber($wtStart) . '|'
                    . $normalizeNumber($wtEnd) . '|' . (int) $zoneNo;
                $rate = null;
                if ($validated['service_id']) {
                    $rate = $rateLookup['id:' . $customerId . '|' . $validated['service_id'] . '|' . $weightKey] ?? null;
                } else {
                    $uploadedCode = $normalizeValue($serviceCode);
                    $rate = $uploadedCode !== ''
                        ? ($rateLookup['code:' . $customerId . '|' . $uploadedCode . '|' . $weightKey] ?? null)
                        : null;
                    if (!$rate && $network !== '' && $method !== '') {
                        $rate = $rateLookup['method:' . $customerId . '|' . $normalizeValue($network) . '|' . $normalizeValue($method) . '|' . $weightKey] ?? null;
                    }
                }

                if (!$rate) {
                    $notFound++;
                    continue;
                }

                $pendingUpdates[$rate->id] = [
                    'id'         => $rate->id,
                    'price'      => $price,
                    'start_date' => $validated['start_date'],
                    'end_date'   => $validated['end_date'],
                    'updated_at' => now(),
                ];
            }

            $updated = count($pendingUpdates);

            foreach (array_chunk(array_values($pendingUpdates), 500) as $chunk) {
                \DB::table('courier_rates')->upsert(
                    $chunk,
                    ['id'],
                    ['price', 'start_date', 'end_date', 'updated_at']
                );
            }
        });

        if ($updated === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No matching customer rates were found in the uploaded file.',
                'skipped' => $skipped,
                'not_found' => $notFound,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $updated . ' rate(s) updated successfully.'
                . ($notFound ? ' ' . $notFound . ' row(s) did not match an existing rate.' : '')
                . ($skipped ? ' ' . $skipped . ' invalid row(s) skipped.' : ''),
            'updated' => $updated,
            'skipped' => $skipped,
            'not_found' => $notFound,
        ]);
    }

    /**
     * Add a new default rate (customer_id = 0) for a given service.
     *
     * The admin selects a country (which filters the service list) and a
     * service, then provides the weight range, zone number and price.
     * Optional fuel/GST fields default to 0 when left blank.
     *
     * A duplicate guard prevents creating two default rates for the exact
     * same (service_id, wt_range_start, wt_range_end, zone_no) combination.
     */
    public function addRate(Request $request)
    {
        $validated = $request->validate([
            'service_id'      => 'required|integer|exists:courier_services,id',
            'wt_range_start'  => 'required|numeric|min:0',
            'wt_range_end'    => 'required|numeric|min:0|gt:wt_range_start',
            'zone_no'         => 'required|integer|min:0|max:13',
            'price'           => 'required|numeric|min:0',
            'fuel_charge'     => 'nullable|numeric|min:0',
            'fuel_percentage' => 'nullable|numeric|min:0',
            'gst_percentage'  => 'nullable|numeric|min:0',
        ]);

        // Guard against duplicate default rates for the same service+weight+zone.
        $exists = \App\Models\CourierRate::where('customer_id', 0)
            ->where('service_id', $validated['service_id'])
            ->where('wt_range_start', $validated['wt_range_start'])
            ->where('wt_range_end', $validated['wt_range_end'])
            ->where('zone_no', $validated['zone_no'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A default rate already exists for this service, weight range and zone.',
            ], 409);
        }

        $customerCount = 0;

        DB::transaction(function () use ($validated, &$rate, &$customerCount) {
            $rate = \App\Models\CourierRate::create([
                'customer_id'     => 0,
                'service_id'      => $validated['service_id'],
                'wt_range_start'  => $validated['wt_range_start'],
                'wt_range_end'    => $validated['wt_range_end'],
                'zone_no'         => $validated['zone_no'],
                'price'           => $validated['price'],
                'fuel_charge'     => $validated['fuel_charge'] ?? 0,
                'fuel_percentage' => $validated['fuel_percentage'] ?? 0,
                'gst_percentage'  => $validated['gst_percentage'] ?? 0,
                'gst_amount'      => 0,
                'is_default'      => true,
                'start_date'      => '2026-01-01',
                'end_date'        => '2026-12-31',
            ]);

            // Propagate the new default rate to every customer so they inherit
            // it (is_default = 1) until an admin customizes their own rate.
            // Customers who already have a rate for this service + weight +
            // zone (inherited or customized) are skipped so we never clobber
            // a customized rate.
            $customerIds = \App\Models\Customer::pluck('id')->toArray();

            if (!empty($customerIds)) {
                $customersWithRate = \App\Models\CourierRate::whereIn('customer_id', $customerIds)
                    ->where('service_id', $validated['service_id'])
                    ->where('wt_range_start', $validated['wt_range_start'])
                    ->where('wt_range_end', $validated['wt_range_end'])
                    ->where('zone_no', $validated['zone_no'])
                    ->pluck('customer_id')
                    ->toArray();

                $customersNeedingRate = array_values(array_diff($customerIds, $customersWithRate));
                $customerCount = count($customersNeedingRate);

                if (!empty($customersNeedingRate)) {
                    $now = now();
                    $rows = array_map(function ($customerId) use ($validated, $now) {
                        return [
                            'customer_id'     => $customerId,
                            'service_id'      => $validated['service_id'],
                            'wt_range_start'  => $validated['wt_range_start'],
                            'wt_range_end'    => $validated['wt_range_end'],
                            'zone_no'         => $validated['zone_no'],
                            'price'           => $validated['price'],
                            'fuel_charge'     => $validated['fuel_charge'] ?? 0,
                            'fuel_percentage' => $validated['fuel_percentage'] ?? 0,
                            'gst_percentage'  => $validated['gst_percentage'] ?? 0,
                            'gst_amount'      => 0,
                            'is_default'      => true,
                            'start_date'      => '2026-01-01',
                            'end_date'        => '2026-12-31',
                            'created_at'      => $now,
                            'updated_at'      => $now,
                        ];
                    }, $customersNeedingRate);

                    foreach (array_chunk($rows, 500) as $chunk) {
                        \App\Models\CourierRate::insert($chunk);
                    }
                }
            }
        });

        return response()->json([
            'success'        => true,
            'message'        => 'Default rate added successfully' . ($customerCount > 0 ? ' and propagated to ' . $customerCount . ' customer(s).' : '.'),
            'rate_id'        => $rate->id,
            'propagated_to'  => $customerCount,
        ]);
    }

    /**
     * Download a sample Excel file for bulk rate upload.
     *
     * The admin selects a Service (and optionally a Zone No) on the Manage
     * Rate page, then clicks "Download Sample". This generates an .xlsx
     * file with the expected header row (Weight Start, Weight End, Zone No,
     * Price, Fuel Charge, Fuel %, GST %). If a service is selected, the
     * file is pre-filled with the existing default rates for that service
     * so the admin can see the current values and edit them.
     *
     * Query params:
     *   - service_id : optional, pre-fills existing rates for this service
     *   - zone_no    : optional, further filters the pre-filled rates
     */
    public function downloadRateSample(Request $request)
    {
        $serviceId = $request->query('service_id');
        $zoneNo    = $request->query('zone_no');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->setCellValue('A1', 'Weight Start');
        $sheet->setCellValue('B1', 'Weight End');
        $sheet->setCellValue('C1', 'Zone No');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Fuel Charge');
        $sheet->setCellValue('F1', 'Fuel %');
        $sheet->setCellValue('G1', 'GST %');

        $row = 2;

        // If a service is selected, include the default rates that already
        // exist for that service (optionally filtered by zone) so the admin
        // can see what's already there and edit/update them.
        if ($serviceId) {
            $query = \App\Models\CourierRate::where('customer_id', 0)
                ->where('service_id', $serviceId)
                ->orderBy('zone_no')
                ->orderBy('wt_range_start');

            if ($zoneNo !== null && $zoneNo !== '') {
                $query->where('zone_no', $zoneNo);
            }

            $existingRates = $query->get();

            foreach ($existingRates as $r) {
                $sheet->setCellValue('A' . $row, $r->wt_range_start);
                $sheet->setCellValue('B' . $row, $r->wt_range_end);
                $sheet->setCellValue('C' . $row, $r->zone_no);
                $sheet->setCellValue('D' . $row, $r->price);
                $sheet->setCellValue('E' . $row, $r->fuel_charge);
                $sheet->setCellValue('F' . $row, $r->fuel_percentage);
                $sheet->setCellValue('G' . $row, $r->gst_percentage);
                $row++;
            }
        }

        // If no existing rates were found (or no service was selected), fall
        // back to a few example rows so the file is not empty.
        if ($row === 2) {
            $samples = [
                [0.5, 1.0, 1, 1500, 0, 0, 18],
                [1.0, 2.0, 1, 1800, 0, 0, 18],
                [2.0, 3.0, 1, 2100, 0, 0, 18],
                [0.5, 1.0, 2, 1600, 0, 0, 18],
                [1.0, 2.0, 2, 1900, 0, 0, 18],
            ];
            foreach ($samples as $s) {
                $sheet->setCellValue('A' . $row, $s[0]);
                $sheet->setCellValue('B' . $row, $s[1]);
                $sheet->setCellValue('C' . $row, $s[2]);
                $sheet->setCellValue('D' . $row, $s[3]);
                $sheet->setCellValue('E' . $row, $s[4]);
                $sheet->setCellValue('F' . $row, $s[5]);
                $sheet->setCellValue('G' . $row, $s[6]);
                $row++;
            }
        }

        // Bold the header row and auto-size columns
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'rate-upload-sample.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Bulk-import default rates from an uploaded Excel file.
     *
     * The admin selects a Service (and optionally a Zone No) on the Manage
     * Rate page, then uploads an .xlsx/.xls/.csv file. The file must have a
     * header row with "Weight Start", "Weight End", "Price" (required) and
     * optionally "Zone No", "Fuel Charge", "Fuel %", "GST %". If the file
     * does not contain a "Zone No" column, the zone selected on the form is
     * applied to every row. Each data row creates a new default rate
     * (customer_id = 0, is_default = true) for the chosen service.
     *
     * Duplicate detection: a rate is considered a duplicate if a default
     * rate already exists for the same service_id + wt_range_start +
     * wt_range_end + zone_no combination.
     */
    public function uploadRateExcel(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|integer|exists:courier_services,id',
            'zone_no'    => 'nullable|integer|min:0|max:13',
            'rate_file'  => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $file = $request->file('rate_file');
        $filePath = $file->getRealPath();

        try {
            // Detect the file type and load the spreadsheet.
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', 'Could not read the uploaded file. Please ensure it is a valid Excel/CSV file.');
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', 'The file is empty or has no data rows (only a header was found).');
        }

        // The first row is the header. Find each column by matching header
        // text (case-insensitive) so the admin does not have to worry about
        // exact column order.
        $header = array_map(function ($h) {
            // Normalize: lowercase, trim and collapse internal whitespace.
            return preg_replace('/\s+/', ' ', strtolower(trim((string) $h)));
        }, $rows[0]);

        $wtStartHeaders    = ['weight start', 'wt start', 'wt_range_start', 'weightstart', 'start weight', 'from weight', 'min weight', 'weight from'];
        $wtEndHeaders      = ['weight end', 'wt end', 'wt_range_end', 'weightend', 'end weight', 'to weight', 'max weight', 'weight to'];
        $zoneNoHeaders     = ['zone no', 'zone_no', 'zoneno', 'zone number', 'zone'];
        $priceHeaders      = ['price', 'rate', 'amount', 'cost'];
        $fuelChargeHeaders = ['fuel charge', 'fuel_charge', 'fuelcharge', 'fuel'];
        $fuelPctHeaders    = ['fuel %', 'fuel percentage', 'fuel_percentage', 'fuelpercentage', 'fuel pct', 'fuel_pct'];
        $gstPctHeaders     = ['gst %', 'gst percentage', 'gst_percentage', 'gstpercentage', 'gst pct', 'gst_pct', 'gst'];

        $wtStartCol    = null;
        $wtEndCol      = null;
        $zoneNoCol     = null;
        $priceCol      = null;
        $fuelChargeCol = null;
        $fuelPctCol    = null;
        $gstPctCol     = null;

        foreach ($header as $idx => $h) {
            if ($wtStartCol === null && in_array($h, $wtStartHeaders, true)) {
                $wtStartCol = $idx;
            }
            if ($wtEndCol === null && in_array($h, $wtEndHeaders, true)) {
                $wtEndCol = $idx;
            }
            if ($zoneNoCol === null && in_array($h, $zoneNoHeaders, true)) {
                $zoneNoCol = $idx;
            }
            if ($priceCol === null && in_array($h, $priceHeaders, true)) {
                $priceCol = $idx;
            }
            if ($fuelChargeCol === null && in_array($h, $fuelChargeHeaders, true)) {
                $fuelChargeCol = $idx;
            }
            if ($fuelPctCol === null && in_array($h, $fuelPctHeaders, true)) {
                $fuelPctCol = $idx;
            }
            if ($gstPctCol === null && in_array($h, $gstPctHeaders, true)) {
                $gstPctCol = $idx;
            }
        }

        // Weight Start, Weight End and Price are required columns.
        if ($wtStartCol === null || $wtEndCol === null || $priceCol === null) {
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', 'The file must contain "Weight Start", "Weight End" and "Price" columns. Please download the sample file for the correct format.');
        }

        $created = 0;
        $skipped = 0;
        $duplicates = 0;
        $dataRows = array_slice($rows, 1);

        // Pre-fetch all existing default rates for this service so we can
        // skip duplicates without running a query per row. Keys are
        // "wtStart|wtEnd|zoneNo" for fast lookup.
        $existingRates = \App\Models\CourierRate::where('customer_id', 0)
            ->where('service_id', $validated['service_id'])
            ->get(['wt_range_start', 'wt_range_end', 'zone_no']);

        $existingKeys = [];
        foreach ($existingRates as $r) {
            $key = $r->wt_range_start . '|' . $r->wt_range_end . '|' . $r->zone_no;
            $existingKeys[$key] = true;
        }

        // Track keys seen within this upload too, so the same combination
        // appearing twice in the same file is only inserted once.
        $seenInUpload = [];

        $formZoneNo = $validated['zone_no'] ?? null;

        // Pre-fetch all customer IDs once so we can propagate every new
        // default rate to all customers (is_default = 1) in a single batch
        // insert per row. Customers who already have a rate for a given
        // service + weight + zone combination are skipped so we never
        // clobber a customized rate.
        $customerIds = \App\Models\Customer::pluck('id')->toArray();
        $propagated = 0;

        // Map of "wtStart|wtEnd|zoneNo" => [customerId => true] for every
        // customer rate that already exists for this service.
        $existingCustomerKeys = [];
        if (!empty($customerIds)) {
            $existingCustomerRates = \App\Models\CourierRate::whereIn('customer_id', $customerIds)
                ->where('service_id', $validated['service_id'])
                ->get(['customer_id', 'wt_range_start', 'wt_range_end', 'zone_no']);
            foreach ($existingCustomerRates as $cr) {
                $k = $cr->wt_range_start . '|' . $cr->wt_range_end . '|' . $cr->zone_no;
                $existingCustomerKeys[$k][$cr->customer_id] = true;
            }
        }

        // Track customer rates we create during this upload so we don't
        // duplicate-propagate when the same combination appears twice in
        // the file.
        $propagatedInUpload = [];

        foreach ($dataRows as $row) {
            $wtStart = isset($row[$wtStartCol]) ? trim((string) $row[$wtStartCol]) : '';
            $wtEnd   = isset($row[$wtEndCol]) ? trim((string) $row[$wtEndCol]) : '';
            $price   = isset($row[$priceCol]) ? trim((string) $row[$priceCol]) : '';

            // Zone No: prefer the file column, fall back to the form value.
            $zoneNo = ($zoneNoCol !== null && isset($row[$zoneNoCol]) && trim((string) $row[$zoneNoCol]) !== '')
                ? trim((string) $row[$zoneNoCol])
                : ($formZoneNo !== null ? (string) $formZoneNo : '');

            $fuelCharge = ($fuelChargeCol !== null && isset($row[$fuelChargeCol])) ? trim((string) $row[$fuelChargeCol]) : '';
            $fuelPct    = ($fuelPctCol !== null && isset($row[$fuelPctCol])) ? trim((string) $row[$fuelPctCol]) : '';
            $gstPct     = ($gstPctCol !== null && isset($row[$gstPctCol])) ? trim((string) $row[$gstPctCol]) : '';

            // Skip completely empty rows.
            if ($wtStart === '' && $wtEnd === '' && $price === '' && $zoneNo === '') {
                $skipped++;
                continue;
            }

            // Validate required numeric fields.
            if ($wtStart === '' || $wtEnd === '' || $price === '') {
                $skipped++;
                continue;
            }
            if (!is_numeric($wtStart) || !is_numeric($wtEnd) || !is_numeric($price)) {
                $skipped++;
                continue;
            }
            if ((float) $wtEnd <= (float) $wtStart) {
                $skipped++;
                continue;
            }

            // Zone No must be a valid integer 0-13.
            if ($zoneNo === '' || !is_numeric($zoneNo) || (int) $zoneNo < 0 || (int) $zoneNo > 13) {
                $skipped++;
                continue;
            }
            $zoneNoInt = (int) $zoneNo;

            $key = $wtStart . '|' . $wtEnd . '|' . $zoneNoInt;

            // Skip duplicates (already in DB or already in this upload).
            if (isset($existingKeys[$key]) || isset($seenInUpload[$key])) {
                $duplicates++;
                continue;
            }

            \App\Models\CourierRate::create([
                'customer_id'     => 0,
                'service_id'      => $validated['service_id'],
                'wt_range_start'  => $wtStart,
                'wt_range_end'    => $wtEnd,
                'zone_no'         => $zoneNoInt,
                'price'           => $price,
                'fuel_charge'     => ($fuelCharge !== '' && is_numeric($fuelCharge)) ? $fuelCharge : 0,
                'fuel_percentage' => ($fuelPct !== '' && is_numeric($fuelPct)) ? $fuelPct : 0,
                'gst_percentage'  => ($gstPct !== '' && is_numeric($gstPct)) ? $gstPct : 0,
                'gst_amount'      => 0,
                'is_default'      => true,
                'start_date'      => '2026-01-01',
                'end_date'        => '2026-12-31',
            ]);
            $seenInUpload[$key] = true;
            $created++;

            // Propagate this new default rate to every customer that does
            // not already have a rate for this service + weight + zone
            // combination (inherited or customized).
            if (!empty($customerIds)) {
                $alreadyHave = $existingCustomerKeys[$key] ?? [];
                $propagatedHave = $propagatedInUpload[$key] ?? [];
                $customersNeedingRate = array_values(array_filter($customerIds, function ($cid) use ($alreadyHave, $propagatedHave) {
                    return !isset($alreadyHave[$cid]) && !isset($propagatedHave[$cid]);
                }));

                if (!empty($customersNeedingRate)) {
                    $now = now();
                    $rows = array_map(function ($customerId) use ($validated, $wtStart, $wtEnd, $zoneNoInt, $price, $fuelCharge, $fuelPct, $gstPct, $now) {
                        return [
                            'customer_id'     => $customerId,
                            'service_id'      => $validated['service_id'],
                            'wt_range_start'  => $wtStart,
                            'wt_range_end'    => $wtEnd,
                            'zone_no'         => $zoneNoInt,
                            'price'           => $price,
                            'fuel_charge'     => ($fuelCharge !== '' && is_numeric($fuelCharge)) ? $fuelCharge : 0,
                            'fuel_percentage' => ($fuelPct !== '' && is_numeric($fuelPct)) ? $fuelPct : 0,
                            'gst_percentage'  => ($gstPct !== '' && is_numeric($gstPct)) ? $gstPct : 0,
                            'gst_amount'      => 0,
                            'is_default'      => true,
                            'start_date'      => '2026-01-01',
                            'end_date'        => '2026-12-31',
                            'created_at'      => $now,
                            'updated_at'      => $now,
                        ];
                    }, $customersNeedingRate);

                    foreach (array_chunk($rows, 500) as $chunk) {
                        \App\Models\CourierRate::insert($chunk);
                    }

                    foreach ($customersNeedingRate as $cid) {
                        $propagatedInUpload[$key][$cid] = true;
                    }
                    $propagated += count($customersNeedingRate);
                }
            }
        }

        if ($created === 0) {
            $msg = 'No new rates were imported.';
            if ($duplicates > 0) {
                $msg .= ' ' . $duplicates . ' duplicate rate(s) already exist and were skipped.';
            }
            if ($skipped > 0) {
                $msg .= ' ' . $skipped . ' invalid/empty row(s) skipped.';
            }
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', $msg);
        }

        $msg = $created . ' rate(s) imported successfully.';
        if ($duplicates > 0) {
            $msg .= ' ' . $duplicates . ' duplicate rate(s) already existed and were skipped.';
        }
        if ($skipped > 0) {
            $msg .= ' ' . $skipped . ' invalid/empty row(s) skipped.';
        }
        if ($propagated > 0) {
            $msg .= ' ' . $propagated . ' customer rate(s) propagated.';
        }

        return redirect()
            ->route('admin.manage-rate')
            ->with('success', $msg);
    }

    /**
     * Show the "Add Zone" page.
     *
     * Loads all destinations (countries) so the admin can pick one, then
     * choose a zone category (state / zipcode / city), and finally enter one
     * or more zone entries (zone name, zone code, zone number) for that
     * country + category combination.
     */
    public function addZone()
    {
        $destinations = \App\Models\Destination::orderBy('name')->get();

        return view('admin.add-zone', compact('destinations'));
    }

    /**
     * Store one or more new zone entries submitted from the Add Zone page.
     *
     * The form submits:
     *   - destination_id : the selected country
     *   - zone_category  : state | zipcode | city
     *   - zone_number    : the zone number (0-13) that rates reference
     *   - entries        : array of { zone_name, zone_code } pairs
     *
     * Each entry creates a new row in the `zone` table linked to the chosen
     * destination and category. zone_number_testing is set to the supplied
     * zone_number so the new zones are immediately usable by the rate system.
     *
     * A single zone_name is allowed to have MULTIPLE zone_codes (e.g. a state
     * can be referenced by several codes). Therefore a repeated zone_name is
     * NOT treated as a duplicate. A row is only skipped when its zone_code
     * already exists or when the exact (zone_name + zone_code) pair already
     * exists.
     *
     * CODE UNIQUENESS SCOPE:
     *   zone_code uniqueness is scoped to the SELECTED COUNTRY only, for every
     *   category (state, zipcode, city). The same code may legitimately exist
     *   in different countries (e.g. "AL" for Alabama (US) and Albania, or the
     *   postcode prefix "SW1" in UK and another country). A code is only a
     *   duplicate when it already exists within the same selected country +
     *   category.
     */
    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|integer|exists:destinations,id',
            'zone_category'  => 'required|in:state,zipcode,city',
            'zone_number'    => 'required|integer|min:0|max:13',
            'entries'        => 'required|array|min:1',
            'entries.*.zone_name' => 'required|string|max:100',
            'entries.*.zone_code' => 'nullable|string|max:10',
        ]);

        $created = 0;
        $skipped = 0;

        // Pre-fetch existing zone codes AND (zone_name + zone_code) pairs for
        // this country + category (lower-cased keys for case-insensitive
        // duplicate detection) so we can skip duplicates without running a
        // query per entry.
        //
        // A single zone_name may have multiple zone_codes, so we no longer
        // build a name-only duplicate map. We track codes (which must stay
        // unique) and exact (name + code) pairs (to avoid identical rows).
        //
        // CODE UNIQUENESS SCOPE:
        //   zone_code uniqueness is scoped to the SELECTED COUNTRY only, for
        //   every category (state, zipcode, city). The same code may exist in
        //   different countries; it is only a duplicate within the same
        //   selected country + category.
        $existingZones = \App\Models\Zone::where('destination_id', $validated['destination_id'])
            ->where('zone_category', $validated['zone_category'])
            ->get(['zone_name', 'zone_code']);

        $existingCodes = [];
        $existingPairs = [];
        foreach ($existingZones as $z) {
            $n = strtolower(trim((string) $z->zone_name));
            $c = strtolower(trim((string) $z->zone_code));
            if ($c !== '') {
                $existingCodes[$c] = true;
            }
            $existingPairs[$n . '|' . $c] = true;
        }

        // Track codes AND (name + code) pairs added within this same
        // submission too, so the same value appearing twice in the form is
        // only inserted once.
        $seenCodes = [];
        $seenPairs = [];

        // Normalize the case of a zone name so that "DELHI", "Delhi" and
        // "delhi" (or "MANGAWHAI", "Mangawhai", "mangawai") all collapse to a
        // single consistent Title Case form ("Delhi", "Mangawhai"). This
        // applies to EVERY category (state, zipcode, city) because the
        // zone_name column always holds a place name, never a raw postcode.
        // The actual postcode is stored in zone_code (handled below) and is
        // always upper-cased. The `zone` table uses a case-insensitive
        // collation (utf8mb4_general_ci) so the rate-system lookup still
        // matches regardless of stored case.
        $normalizeName = function ($value) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }
            return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        };
        $normalizeCode = function ($value) {
            $value = trim((string) $value);
            return $value !== '' ? mb_strtoupper($value, 'UTF-8') : '';
        };

        foreach ($validated['entries'] as $entry) {
            $name = $normalizeName($entry['zone_name']);
            $code = $normalizeCode($entry['zone_code'] ?? '');

            $nameKey = strtolower($name);
            $codeKey = $code !== '' ? strtolower($code) : '';
            $pairKey = $nameKey . '|' . $codeKey;

            // A zone_name may have multiple zone_codes, so a repeated name is
            // NOT a duplicate. Skip only when the exact (name + code) pair
            // already exists, or when a non-empty zone_code already exists
            // (codes must stay unique because the rate system looks them up).
            if (isset($existingPairs[$pairKey]) || isset($seenPairs[$pairKey])) {
                $skipped++;
                continue;
            }
            if ($codeKey !== '' && (isset($existingCodes[$codeKey]) || isset($seenCodes[$codeKey]))) {
                $skipped++;
                continue;
            }

            \App\Models\Zone::create([
                'destination_id'      => $validated['destination_id'],
                'zone_category'       => $validated['zone_category'],
                'zone_number'         => $validated['zone_number'],
                'zone_number_testing' => $validated['zone_number'],
                'zone_name'           => $name,
                'zone_code'           => $code,
            ]);
            $seenPairs[$pairKey] = true;
            if ($codeKey !== '') {
                $seenCodes[$codeKey] = true;
            }
            $created++;
        }

        $msg = $created . ' zone(s) added successfully.';
        if ($skipped > 0) {
            $msg .= ' ' . $skipped . ' duplicate zone(s) (name or code already existed) were skipped.';
        }

        return redirect()
            ->route('admin.add-zone')
            ->with($created > 0 ? 'success' : 'error', $msg);
    }

    /**
     * Download a sample Excel file showing the expected format for bulk
     * zone uploads.
     *
     * The sample contains a header row (Zone Name, Zone Code). If a
     * destination_id (and optionally zone_category) is passed as a query
     * parameter, the existing zones for that country/category are also
     * included so the admin can see which zones are already present (and
     * avoid duplicating them on re-upload). The file is generated on the fly
     * with PhpSpreadsheet and streamed back as a download (.xlsx).
     */
    public function downloadZoneSample(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $zoneCategory  = $request->query('zone_category', 'state');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->setCellValue('A1', 'Zone Name');
        $sheet->setCellValue('B1', 'Zone Code');

        $row = 2;

        // If a country is selected, include the zones that already exist for
        // that country + category so the admin can see what's already there.
        if ($destinationId) {
            $existingZones = \App\Models\Zone::where('destination_id', $destinationId)
                ->where('zone_category', $zoneCategory)
                ->orderBy('zone_name')
                ->get();

            foreach ($existingZones as $z) {
                $sheet->setCellValue('A' . $row, $z->zone_name);
                $sheet->setCellValue('B' . $row, $z->zone_code ?: '');
                $row++;
            }
        }

        // If no existing zones were found (or no country was selected), fall
        // back to a few example rows so the file is not empty.
        if ($row === 2) {
            $samples = [
                ['New South Wales', 'NSW'],
                ['Victoria',        'VIC'],
                ['Queensland',      'QLD'],
                ['Western Australia', 'WA'],
                ['South Australia', 'SA'],
            ];
            foreach ($samples as $s) {
                $sheet->setCellValue('A' . $row, $s[0]);
                $sheet->setCellValue('B' . $row, $s[1]);
                $row++;
            }
        }

        // Bold the header row and auto-size columns
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $fileName = 'zone-upload-sample.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Bulk-import zones from an uploaded Excel file.
     *
     * The admin first selects a Country, Zone Category and Zone Number on
     * the Add Zone page, then uploads an .xlsx/.xls/.csv file. The file must
     * have a header row with "Zone Name" (required) and "Zone Code"
     * (optional). Each data row creates a new zone entry linked to the
     * chosen country + category + zone number.
     *
     * A single zone_name is allowed to have MULTIPLE zone_codes (e.g. a state
     * can be referenced by several codes). Therefore a repeated zone_name is
     * NOT treated as a duplicate. A row is only skipped when its zone_code
     * already exists or when the exact (zone_name + zone_code) pair already
     * exists.
     *
     * CODE UNIQUENESS SCOPE:
     *   zone_code uniqueness is scoped to the SELECTED COUNTRY only, for every
     *   category (state, zipcode, city). The same code may legitimately exist
     *   in different countries (e.g. "AL" for Alabama (US) and Albania, or the
     *   postcode prefix "SW1" in UK and another country). A code is only a
     *   duplicate when it already exists within the same selected country +
     *   category.
     */
    public function uploadZoneExcel(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|integer|exists:destinations,id',
            'zone_category'  => 'required|in:state,zipcode,city',
            'zone_number'    => 'required|integer|min:0|max:13',
            'zone_file'      => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $file = $request->file('zone_file');
        $filePath = $file->getRealPath();

        try {
            // Detect the file type and load the spreadsheet.
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.add-zone')
                ->with('error', 'Could not read the uploaded file. Please ensure it is a valid Excel/CSV file.');
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return redirect()
                ->route('admin.add-zone')
                ->with('error', 'The file is empty or has no data rows (only a header was found).');
        }

        // The first row is the header. Find the Zone Name and Zone Code
        // columns by matching header text (case-insensitive) so the admin
        // does not have to worry about exact column order. Each column is
        // detected INDEPENDENTLY so that a file which only has a "Zone Name"
        // or only a "Zone Code" column is mapped to the correct database
        // column (zone_name / zone_code) instead of being forced into the
        // first two positions.
        $header = array_map(function ($h) {
            // Normalize: lowercase, trim and collapse internal whitespace so
            // "Zone  Name", "ZONE NAME" and "Zone_Name" all match the same key.
            return preg_replace('/\s+/', ' ', strtolower(trim((string) $h)));
        }, $rows[0]);

        $nameHeaders = ['zone name', 'zone_name', 'zonename', 'name', 'state', 'zipcode', 'zip', 'city', 'zone'];
        $codeHeaders = ['zone code', 'zone_code', 'zonecode', 'code'];

        $nameCol = null;
        $codeCol = null;
        foreach ($header as $idx => $h) {
            if ($nameCol === null && in_array($h, $nameHeaders, true)) {
                $nameCol = $idx;
            }
            if ($codeCol === null && in_array($h, $codeHeaders, true)) {
                $codeCol = $idx;
            }
        }

        // Fallback ONLY when NEITHER column was recognized by its header:
        // assume the first column is the zone name and the second (if
        // present) is the zone code. This keeps backward compatibility for
        // headerless files while no longer clobbering a correctly-detected
        // "Zone Code" column.
        if ($nameCol === null && $codeCol === null) {
            $nameCol = 0;
            $codeCol = isset($header[1]) ? 1 : null;
        } elseif ($nameCol === null) {
            // Only the code column was recognized. Use the first available
            // column that is not the code column as the name column.
            $nameCol = ($codeCol === 0 && isset($header[1])) ? 1 : 0;
        }
        // If only the name column was recognized, codeCol stays null (no code).

        $created = 0;
        $skipped = 0;
        $duplicates = 0;
        $dataRows = array_slice($rows, 1);

        // Collect every row that is NOT inserted (empty, duplicate, or
        // duplicate code) so the admin can download them afterwards and see
        // exactly which rows were skipped and why. Each entry stores the
        // original (raw) name + code from the file plus a human-readable
        // reason. This array is flashed to the session at the end.
        $skippedRows = [];

        // Pre-fetch existing zone codes AND (zone_name + zone_code) pairs for
        // this country + category so we can skip duplicates without running a
        // query per row. Keys are lower-cased values for case-insensitive
        // comparison.
        //
        // A single zone_name may have multiple zone_codes, so we no longer
        // build a name-only duplicate map. We track codes (which must stay
        // unique) and exact (name + code) pairs (to avoid identical rows).
        //
        // CODE UNIQUENESS SCOPE:
        //   zone_code uniqueness is scoped to the SELECTED COUNTRY only, for
        //   every category (state, zipcode, city). The same code may exist in
        //   different countries; it is only a duplicate within the same
        //   selected country + category.
        $existingZones = \App\Models\Zone::where('destination_id', $validated['destination_id'])
            ->where('zone_category', $validated['zone_category'])
            ->get(['zone_name', 'zone_code']);

        $existingCodes = [];
        $existingPairs = [];
        foreach ($existingZones as $z) {
            $n = strtolower(trim((string) $z->zone_name));
            $c = strtolower(trim((string) $z->zone_code));
            if ($c !== '') {
                $existingCodes[$c] = true;
            }
            $existingPairs[$n . '|' . $c] = true;
        }

        // Track codes AND (name + code) pairs seen within this upload too,
        // so the same value appearing twice in the same file is only inserted
        // once.
        $seenCodesInUpload = [];
        $seenPairsInUpload = [];

        // Normalize the case of a zone name so that "DELHI", "Delhi" and
        // "delhi" (or "MANGAWHAI", "Mangawhai", "mangawai") all collapse to a
        // single consistent Title Case form ("Delhi", "Mangawhai"). This
        // applies to EVERY category (state, zipcode, city) because the
        // zone_name column always holds a place name, never a raw postcode.
        // The actual postcode is stored in zone_code (handled below) and is
        // always upper-cased. The `zone` table uses a case-insensitive
        // collation (utf8mb4_general_ci) so the rate-system lookup still
        // matches regardless of stored case.
        $normalizeName = function ($value) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }
            return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        };
        $normalizeCode = function ($value) {
            $value = trim((string) $value);
            return $value !== '' ? mb_strtoupper($value, 'UTF-8') : '';
        };

        foreach ($dataRows as $row) {
            $rawName = ($nameCol !== null && isset($row[$nameCol])) ? trim((string) $row[$nameCol]) : '';
            $rawCode = ($codeCol !== null && isset($row[$codeCol])) ? trim((string) $row[$codeCol]) : '';

            // Skip completely empty rows (neither a name nor a code present).
            if ($rawName === '' && $rawCode === '') {
                $skipped++;
                $skippedRows[] = [
                    'zone_name' => $rawName,
                    'zone_code' => $rawCode,
                    'reason'    => 'Empty row (no zone name and no zone code)',
                ];
                continue;
            }

            // The `zone` table requires a zone_name. If the file only had a
            // "Zone Code" column (no Zone Name), fall back to using the code
            // as the name so the row can still be imported. The fallback name
            // is normalized the same way as a regular name.
            $effectiveName = $rawName !== '' ? $normalizeName($rawName) : $normalizeName($rawCode);
            $zoneCode      = $normalizeCode($rawCode);

            $nameKey = strtolower($effectiveName);
            $codeKey = $zoneCode !== '' ? strtolower($zoneCode) : '';
            $pairKey = $nameKey . '|' . $codeKey;

            // A zone_name may have multiple zone_codes, so a repeated name is
            // NOT a duplicate. Skip only when the exact (name + code) pair
            // already exists, or when a non-empty zone_code already exists
            // (codes must stay unique because the rate system looks them up).
            if (isset($existingPairs[$pairKey]) || isset($seenPairsInUpload[$pairKey])) {
                $duplicates++;
                $skippedRows[] = [
                    'zone_name' => $rawName,
                    'zone_code' => $rawCode,
                    'reason'    => 'Duplicate zone name + zone code pair already exists',
                ];
                continue;
            }
            if ($codeKey !== '' && (isset($existingCodes[$codeKey]) || isset($seenCodesInUpload[$codeKey]))) {
                $duplicates++;
                $skippedRows[] = [
                    'zone_name' => $rawName,
                    'zone_code' => $rawCode,
                    'reason'    => 'Duplicate zone code already exists in this country',
                ];
                continue;
            }

            \App\Models\Zone::create([
                'destination_id'      => $validated['destination_id'],
                'zone_category'       => $validated['zone_category'],
                'zone_number'         => $validated['zone_number'],
                'zone_number_testing' => $validated['zone_number'],
                'zone_name'           => mb_substr($effectiveName, 0, 100),
                'zone_code'           => mb_substr($zoneCode, 0, 10),
            ]);
            $seenPairsInUpload[$pairKey] = true;
            if ($codeKey !== '') {
                $seenCodesInUpload[$codeKey] = true;
            }
            $created++;
        }

        // Flash the skipped rows to the session so the admin can download
        // them as an Excel file from the Add Zone page. Only flash when there
        // is at least one skipped row, otherwise the download button is not
        // shown.
        if (!empty($skippedRows)) {
            session()->flash('skipped_zone_rows', $skippedRows);
        }

        if ($created === 0) {
            $msg = 'No new zones were imported.';
            if ($duplicates > 0) {
                $msg .= ' ' . $duplicates . ' duplicate zone(s) already exist and were skipped.';
            }
            if ($skipped > 0) {
                $msg .= ' ' . $skipped . ' empty row(s) skipped.';
            }
            if (!empty($skippedRows)) {
                $msg .= ' You can download the skipped records below.';
            }
            return redirect()
                ->route('admin.add-zone')
                ->with('error', $msg);
        }

        $msg = $created . ' zone(s) imported successfully.';
        if ($duplicates > 0) {
            $msg .= ' ' . $duplicates . ' duplicate zone(s) already existed and were skipped.';
        }
        if ($skipped > 0) {
            $msg .= ' ' . $skipped . ' empty row(s) skipped.';
        }
        if (!empty($skippedRows)) {
            $msg .= ' You can download the skipped records below.';
        }

        return redirect()
            ->route('admin.add-zone')
            ->with('success', $msg);
    }

    /**
     * Download the zones that were skipped during the last bulk Excel
     * upload as an .xlsx file.
     *
     * When uploadZoneExcel() skips rows (empty rows, duplicate name+code
     * pairs, or duplicate zone codes within the selected country), it
     * flashes them to the session under 'skipped_zone_rows'. This method
     * reads that flash data, builds an Excel with three columns
     * (Zone Name, Zone Code, Reason) and streams it back as a download.
     * The session key is forgotten afterwards so the same file is not
     * downloaded again on a later visit.
     */
    public function downloadSkippedZones(Request $request)
    {
        $skippedRows = session('skipped_zone_rows');

        if (empty($skippedRows)) {
            return redirect()
                ->route('admin.add-zone')
                ->with('error', 'There are no skipped records to download. Please upload a zone Excel file first.');
        }

        // Capture the data and clear the flash key so it cannot be
        // downloaded again on a later visit.
        session()->forget('skipped_zone_rows');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->setCellValue('A1', 'Zone Name');
        $sheet->setCellValue('B1', 'Zone Code');
        $sheet->setCellValue('C1', 'Reason');

        $row = 2;
        foreach ($skippedRows as $entry) {
            $sheet->setCellValue('A' . $row, $entry['zone_name'] ?? '');
            $sheet->setCellValue('B' . $row, $entry['zone_code'] ?? '');
            $sheet->setCellValue('C' . $row, $entry['reason'] ?? '');
            $row++;
        }

        // Bold the header row and auto-size columns
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $fileName = 'skipped-zones.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Show the "Add Country" page.
     *
     * A simple form where the admin enters a country name. The new country
     * is added to the `destinations` table so it can be selected when adding
     * zones or rates.
     *
     * The admin can also pick one or more existing courier services (from the
     * courier_services table) to clone for the new country. Cloning a service
     * creates a brand-new courier_services row whose `country` column is set
     * to the new country's short code, so the service becomes available for
     * rate calculation against that destination — without touching the
     * original service row.
     */
    public function addCountry()
    {
        $destinations = \App\Models\Destination::orderBy('name')->get();

        // Load every courier service so the admin can pick which ones to make
        // available for the new country. We order by country then method so the
        // list is grouped logically in the dropdown.
        $courierServices = \App\Models\CourierService::orderBy('country')
            ->orderBy('method')
            ->get();

        return view('admin.add-country', compact('destinations', 'courierServices'));
    }

    /**
     * Store a new country (destination).
     *
     * The admin supplies a country name. We auto-derive a short code from the
     * name if one is not provided, and default is_active to true.
     *
     * Optionally the admin can select one or more existing courier services
     * (from the courier_services table) to "add" to the new country. Each
     * selected service is CLONED into a brand-new courier_services row whose
     * `country` column is set to the new country's short code — so the service
     * becomes available for rate calculation against that destination. The
     * original service row is left untouched (it keeps its own country).
     */
    public function storeCountry(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150|unique:destinations,name',
            'code'           => 'nullable|string|max:10|unique:destinations,code',
            'country_code'   => 'nullable|string|max:5',
            'is_active'      => 'nullable|boolean',
            // Optional list of courier_services IDs to clone for this country.
            'service_ids'    => 'nullable|array',
            'service_ids.*'  => 'integer|exists:courier_services,id',
        ]);

        // Auto-derive a short code from the name if none was provided.
        $code = $validated['code'] ?? '';
        if ($code === '') {
            $words = preg_split('/\s+/', trim($validated['name']));
            if (count($words) === 1) {
                $code = strtoupper(substr($words[0], 0, 3));
            } else {
                $code = '';
                foreach ($words as $w) {
                    $code .= strtoupper(substr($w, 0, 1));
                }
                $code = substr($code, 0, 10);
            }
            // Ensure uniqueness by appending a number if needed.
            $base = $code;
            $i = 1;
            while (\App\Models\Destination::where('code', $code)->exists()) {
                $code = $base . $i;
                $i++;
            }
        }

        \App\Models\Destination::create([
            'name'         => $validated['name'],
            'code'         => $code,
            'country_code' => $validated['country_code'] ?? null,
            'is_active'    => $validated['is_active'] ?? true,
        ]);

        // -----------------------------------------------------------------
        // Clone the selected courier services for the new country.
        //
        // Each selected service is duplicated into a new courier_services
        // row. Every column is copied verbatim EXCEPT `country`, which is
        // overwritten with the new country's ISO country code (falling back
        // to the short code when no ISO code was supplied) so the cloned
        // service is matched against this destination during rate
        // calculation. The original service row is never modified.
        // -----------------------------------------------------------------
        $clonedCount = 0;
        $serviceIds = $validated['service_ids'] ?? [];
        if (!empty($serviceIds)) {
            $services = \App\Models\CourierService::whereIn('id', $serviceIds)->get();

            // Prefer the ISO country code; fall back to the short code when
            // the admin did not provide an ISO code.
            $serviceCountry = $validated['country_code'] !== null && $validated['country_code'] !== ''
                ? $validated['country_code']
                : $code;

            foreach ($services as $service) {
                // Build a fresh row from the source service's attributes.
                $data = $service->getAttributes();

                // Drop the primary key & country so we can set our own values.
                unset($data['id']);
                $data['country'] = $serviceCountry;

                \App\Models\CourierService::create($data);
                $clonedCount++;
            }
        }

        $msg = 'Country "' . $validated['name'] . '" added successfully (code: ' . $code . ').';
        if ($clonedCount > 0) {
            $msg .= ' ' . $clonedCount . ' service(s) cloned for this country.';
        }

        return redirect()
            ->route('admin.add-country')
            ->with('success', $msg);
    }

    public function myProfile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.my-profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admin_user,email,' . $admin->id,
            'mobile' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ];

        // If user is trying to change password, add password validation rules
        if ($request->filled('new_password')) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = 'required|string|min:6|confirmed';
        }

        $validated = $request->validate($rules);

        // Check current password if changing password
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $admin->password = $request->new_password;
        }

        // Update profile fields
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->mobile = $validated['mobile'];
        $admin->designation = $validated['designation'];
        $admin->state = $validated['state'];
        $admin->city = $validated['city'];
        $admin->save();

        return redirect()->route('admin.my-profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * View a customer's full profile (personal info, KYC, business details, wallet).
     */
    public function customerProfile($id)
    {
        $customer = Customer::with(['kycDetail', 'csbForm', 'wallet', 'businessCategory'])
            ->findOrFail($id);

        $personalKyc = $customer->kycDetail;
        $businessKyc = $customer->csbForm;
        $businessCategory = $customer->businessCategory;
        $userType = $businessCategory ? $businessCategory->user_type : 'Personal';
        $wallet = $customer->wallet;

        return view('admin.customer-profile', compact(
            'customer',
            'personalKyc',
            'businessKyc',
            'businessCategory',
            'userType',
            'wallet'
        ));
    }

    /**
     * Activate or deactivate a customer account.
     */
    public function toggleCustomerStatus($id)
    {
        $customer = Customer::findOrFail($id);

        $customer->status = !$customer->status;
        $customer->save();

        $action = $customer->status ? 'activated' : 'deactivated';
        $customerName = $customer->first_name . ' ' . $customer->last_name;

        return redirect()->back()
            ->with('success', "Account for {$customerName} has been {$action} successfully.");
    }

    /**
     * Enable or disable a customer's ability to create shipments.
     *
     * This is INDEPENDENT from the account `status` toggle:
     *  - status               -> can the customer log in at all
     *  - can_create_shipment  -> can the customer create new shipments
     *
     * When disabled, the customer sees a warning banner on the create-shipment
     * page and the storeShipment endpoint rejects the request.
     */
    public function toggleShipmentAccess($id)
    {
        $customer = Customer::findOrFail($id);

        $customer->can_create_shipment = !$customer->can_create_shipment;
        $customer->save();

        $action = $customer->can_create_shipment ? 'enabled' : 'disabled';
        $customerName = $customer->first_name . ' ' . $customer->last_name;

        return redirect()->back()
            ->with('success', "Shipment creation for {$customerName} has been {$action} successfully.");
    }

    /**
     * Export KYC records to an Excel (.xlsx) file using PhpSpreadsheet.
     * Accepts an optional ?status=pending|approved|rejected|all filter.
     */
    public function exportKycExcel(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = KycDetail::with(['customer.csbForm', 'customer.businessCategory']);

        if ($status === 'pending') {
            $query->whereIn('kyc_status', ['pending', 'under_review']);
        } elseif ($status === 'approved') {
            $query->where('kyc_status', 'approved');
        } elseif ($status === 'rejected') {
            $query->where('kyc_status', 'rejected');
        }

        $kycRecords = $query->orderBy('id', 'desc')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('KYC Records');

        $headers = [
            'KYC ID', 'KYC Type', 'KYC Status', 'Customer Name', 'Email', 'Phone',
            'Alternate Phone', 'Organization', 'Authorized Signatory', 'GST Number',
            'GST Verified', 'Aadhar Number', 'Aadhar Verified', 'PAN Number',
            'PAN Holder Name', 'PAN DOB', 'PAN Verified', 'OTP Verified', 'Terms Accepted',
            'Billing Address', 'Billing GST', 'Billing Contact', 'Billing Email',
            'IEC Number', 'AD Code', 'GST Certificate Number', 'LUT Expiry Date',
            'LUT Bond Year', 'Bank Account Number', 'Bank Type', 'Account Status',
            'Submitted At', 'Updated At',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        $row = 2;
        foreach ($kycRecords as $kyc) {
            $customer = $kyc->customer;
            $csb = $customer ? $customer->csbForm : null;

            $data = [
                $kyc->id,
                ucfirst($kyc->kyc_type ?? 'personal'),
                ucfirst($kyc->kyc_status ?? 'pending'),
                $customer ? ($customer->first_name . ' ' . $customer->last_name) : '',
                $customer->email ?? '',
                $customer->phone_number ?? '',
                $customer->alternate_phone_number ?? '',
                $kyc->organization_name ?? '',
                $kyc->authorized_signatory ?? '',
                $kyc->gst_number ?? '',
                $kyc->gst_verified ? 'Yes' : 'No',
                $kyc->aadhar_number ?? '',
                $kyc->aadhar_verified ? 'Yes' : 'No',
                $kyc->pan_number ?? '',
                $kyc->pan_holder_name ?? '',
                $kyc->pan_dob ? $kyc->pan_dob->format('Y-m-d') : '',
                $kyc->pan_verified ? 'Yes' : 'No',
                $kyc->otp_verified ? 'Yes' : 'No',
                $kyc->terms_accepted ? 'Yes' : 'No',
                $kyc->billing_address ?? '',
                $kyc->billing_gst ?? '',
                $kyc->billing_contact ?? '',
                $kyc->billing_email ?? '',
                $csb->iec_number ?? '',
                $csb->ad_code ?? '',
                $csb->gst_certificate_number ?? '',
                $csb && $csb->lut_expiry_date ? $csb->lut_expiry_date->format('Y-m-d') : '',
                $csb->lut_bond_year ?? '',
                $csb->bank_account_number ?? '',
                $csb->bank_type ?? '',
                $customer && isset($customer->status) ? ($customer->status ? 'Active' : 'Deactivated') : 'Active',
                $kyc->created_at ? $kyc->created_at->format('Y-m-d H:i:s') : '',
                $kyc->updated_at ? $kyc->updated_at->format('Y-m-d H:i:s') : '',
            ];

            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // foreach (range('A', 'AG') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));

        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension(
                Coordinate::stringFromColumnIndex($i)
            )->setAutoSize(true);
        }

        $fileName = 'kyc_records_' . $status . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

}
