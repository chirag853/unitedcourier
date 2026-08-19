<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ConsigneeInfo;
use App\Models\CreateShipment;
use App\Models\CsbInformation;
use App\Models\PackageDimension;
use App\Models\ShipmentInvoice;
use App\Models\ShipmentInvoiceItem;
use App\Models\ShipmentLog;
use App\Models\ShipperInfo;
use App\Models\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BulkUploadController extends Controller
{
    /**
     * Show the Bulk Upload page (Excel upload form).
     */
    public function bulkUpload()
    {
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();
        // Only enabled services (status = 1) are offered in the bulk upload flow.
        $courierServices = \App\Models\CourierService::where('status', 1)->orderBy('network')->orderBy('method')->get();

        return view('customer.bulk-upload', compact('customer', 'courierServices'));
    }

    /**
     * Process the uploaded Excel file for bulk shipment creation.
     *
     * Each unique AwbNo = one consignee shipment. Multiple rows sharing the
     * same AwbNo are treated as invoice line items for that shipment.
     * Rate is calculated using the ChgWeight column against the courier_rates table.
     */
    public function processBulkUpload(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();
        $customerId = $customer->id;

        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx,csv|max:20480',
            'selected_rates' => 'nullable|string', // JSON map: { awb_no: rate_id }
        ]);

        try {
            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();

            // Load the spreadsheet using PhpSpreadsheet
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            if (count($rows) < 2) {
                return back()->with('error', 'The uploaded file does not contain any data rows.');
            }

            // First row = headers. Normalize header names (remove spaces, underscores).
            $rawHeaders = array_map(function ($h) {
                return trim(preg_replace('/\s+/', '', strtolower((string) $h)));
            }, $rows[0]);

            // Build a map: normalized header => column index
            $headerMap = [];
            foreach ($rawHeaders as $col => $header) {
                if ($header !== '') {
                    $headerMap[$header] = $col;
                }
            }

            // Helper to fetch a cell value by header name
            $getCol = function ($row, $name) use ($headerMap) {
                if (isset($headerMap[$name]) && array_key_exists($headerMap[$name], $row)) {
                    return trim((string) $row[$headerMap[$name]]);
                }
                return null;
            };

            // Group data rows by AwbNo (each unique AwbNo = one shipment/consignee)
            $grouped = [];
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $awbNo = $getCol($row, 'awbno');
                if ($awbNo === '' || $awbNo === null) {
                    continue;
                }
                $grouped[$awbNo][] = $row;
            }

            if (empty($grouped)) {
                return back()->with('error', 'No valid rows with AwbNo found in the uploaded file.');
            }

            // Parse the selected rate map: { awb_no => rate_id }
            // This comes from the preview modal where the user picks a rate card per shipment.
            $selectedRatesRaw = $request->input('selected_rates', '');
            $selectedRateMap = [];
            if (!empty($selectedRatesRaw)) {
                $decoded = json_decode($selectedRatesRaw, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $awbKey => $rateId) {
                        $selectedRateMap[trim((string) $awbKey)] = (int) $rateId;
                    }
                }
            }

            $createdShipments = [];
            $errors = [];
            $successCount = 0;

            DB::beginTransaction();

            foreach ($grouped as $awbNo => $rowGroup) {
                try {
                    $firstRow = $rowGroup[0];

                    // ---- Shipper (Consignor) data ----
                    $shipperCompanyName = $getCol($firstRow, 'consignorname') ?: ($customer->first_name . ' ' . $customer->last_name);
                    $shipperContactPerson = $getCol($firstRow, 'consignorcontactperson') ?: ($customer->first_name . ' ' . $customer->last_name);
                    $shipperAddress1 = $getCol($firstRow, 'consignoraddressline1');
                    $shipperAddress2 = $getCol($firstRow, 'consignoraddressline2');
                    $shipperAddress3 = $getCol($firstRow, 'consignoraddressline3');
                    $shipperCity = $getCol($firstRow, 'consignorcity');
                    $shipperState = $getCol($firstRow, 'consignorstate');
                    $shipperPincode = $getCol($firstRow, 'consignorpincode');
                    $shipperPhone = $getCol($firstRow, 'consignortelephone') ?: ($customer->phone_number ?? null);
                    $shipperEmail = $customer->email ?? ($shipperPhone . '@bulkupload.local');
                    $gstType = $getCol($firstRow, 'gsttype');
                    $gstIdNo = $getCol($firstRow, 'gstidno');

                    // ---- Consignee data ----
                    $consigneeName = $getCol($firstRow, 'consigneename');
                    $consigneeContactPerson = $getCol($firstRow, 'consigneecontactperson');
                    $consigneeAddress1 = $getCol($firstRow, 'consigneeaddressline1');
                    $consigneeAddress2 = $getCol($firstRow, 'consigneeaddressline2');
                    $consigneeAddress3 = $getCol($firstRow, 'consigneeaddressline3');
                    $consigneeCity = $getCol($firstRow, 'consigneecity');
                    $consigneeState = $getCol($firstRow, 'consigneestate');
                    $consigneeZip = $getCol($firstRow, 'consigneezipcode');
                    $consigneePhone = $getCol($firstRow, 'consigneetelephone');

                    $destination = $getCol($firstRow, 'destination');
                    $referenceNo = $getCol($firstRow, 'referenceno');

                    // ---- Service / shipping method ----
                    $serviceType = $getCol($firstRow, 'servicetype');

                    // ---- Calculate total chargeable weight from ChgWeight column ----
                    $totalChgWeight = 0;
                    foreach ($rowGroup as $r) {
                        $totalChgWeight += floatval($getCol($r, 'chgweight') ?: 0);
                    }
                    if ($totalChgWeight <= 0) {
                        // Fallback: sum of ActWeight
                        foreach ($rowGroup as $r) {
                            $totalChgWeight += floatval($getCol($r, 'actweight') ?: 0);
                        }
                    }

                    // ---- Resolve the rate selected by the user in the preview modal ----
                    // The preview modal returns a map of awb_no => rate_id. We look up the
                    // CourierRate by ID and derive the courierService + rate details from it.
                    $selectedRateId = $selectedRateMap[$awbNo] ?? null;
                    $rateDetails = [
                        'rate_id' => null,
                        'price' => 0,
                        'fuel_charge' => 0,
                        'fuel_percentage' => 0,
                        'gst_percentage' => 0,
                        'gst_amount' => 0,
                        'total' => 0,
                    ];
                    $courierService = null;

                    if ($selectedRateId) {
                        $matchedRate = \App\Models\CourierRate::find($selectedRateId);
                        if ($matchedRate) {
                            $courierService = \App\Models\CourierService::find($matchedRate->service_id);

                            $price = floatval($matchedRate->price);
                            $fuelPercentage = floatval($matchedRate->fuel_percentage);
                            $fuelChargeStored = floatval($matchedRate->fuel_charge);
                            $gstPercentage = floatval($matchedRate->gst_percentage);
                            $gstAmountStored = floatval($matchedRate->gst_amount);

                            // Mirror create-shipments computation exactly
                            $computedFuel = $fuelChargeStored > 0 ? $fuelChargeStored : ($price * $fuelPercentage / 100);
                            $computedGst = $gstAmountStored > 0 ? $gstAmountStored : (($price + $computedFuel) * $gstPercentage / 100);
                            $total = $price + $computedFuel + $computedGst;

                            $rateDetails = [
                                'rate_id' => $matchedRate->id,
                                'price' => round($price, 2),
                                'fuel_charge' => round($computedFuel, 2),
                                'fuel_percentage' => $fuelPercentage,
                                'gst_percentage' => $gstPercentage,
                                'gst_amount' => round($computedGst, 2),
                                'total' => round($total, 2),
                            ];
                        }
                    }

                    // Fallback: if no rate was selected (or rate_id not found), try ServiceType column
                    if (!$courierService && $serviceType) {
                        $courierService = $this->findCourierService($serviceType, null);
                        if ($courierService) {
                            $rateDetails = $this->calculateBulkRate($customerId, $courierService, $totalChgWeight, $consigneeState);
                        }
                    }

                    $shippingMethod = $courierService ? $courierService->method : ($serviceType ?: null);

                    // ---- Generate AWB number ----
                    $newAwbNumber = $this->generateAwbNumber();

                    // ---- Create ShipperInfo ----
                    $shipper = ShipperInfo::create([
                        'customer_id' => $customerId,
                        'awb_number' => $newAwbNumber,
                        'shipping_method' => $shippingMethod,
                        'shipper_same_as_customer' => false,
                        'company_name' => $shipperCompanyName,
                        'contact_person' => $shipperContactPerson,
                        'address_line1' => $shipperAddress1,
                        'address_line2' => $shipperAddress2,
                        'address_line3' => $shipperAddress3,
                        'pincode' => $shipperPincode,
                        'city' => $shipperCity,
                        'state' => $shipperState,
                        'phone_number' => $shipperPhone,
                        'email' => $shipperEmail,
                        'email_opt_out' => false,
                        'kyc_type' => $gstType,
                        'kyc_number' => $gstIdNo,
                        'service_rate_id' => $rateDetails['rate_id'] ?? null,
                        'service_id' => $courierService ? $courierService->id : null,
                    ]);

                    $shipperId = $shipper->id;

                    // ---- Create ConsigneeInfo ----
                    $consignee = ConsigneeInfo::create([
                        'shipper_id' => $shipperId,
                        'delivery_destination' => $destination,
                        'origin_type' => 'CSB IV',
                        'consignee_name' => $consigneeName,
                        'contact_person' => $consigneeContactPerson,
                        'address_line1' => $consigneeAddress1,
                        'address_line2' => $consigneeAddress2,
                        'address_line3' => $consigneeAddress3,
                        'zip_code' => $consigneeZip,
                        'city' => $consigneeCity,
                        'state' => $consigneeState,
                        'phone_number' => $consigneePhone,
                        'email' => $consigneePhone ? $consigneePhone . '@bulkupload.local' : 'consignee@bulkupload.local',
                        'email_opt_out' => false,
                    ]);

                    // ---- Create PackageDimension records (one per row) ----
                    $packageIds = [];
                    $boxNo = 1;
                    foreach ($rowGroup as $r) {
                        $package = PackageDimension::create([
                            'shipper_id' => $shipperId,
                            'shipping_method' => $shippingMethod,
                            'actual_weight_kg' => floatval($getCol($r, 'actweight') ?: 0),
                            'length_cm' => floatval($getCol($r, 'l') ?: 0),
                            'width_cm' => floatval($getCol($r, 'b') ?: 0),
                            'height_cm' => floatval($getCol($r, 'h') ?: 0),
                            'volumetric_weight' => floatval($getCol($r, 'volweight') ?: 0),
                            'chargeable_weight' => floatval($getCol($r, 'chgweight') ?: 0),
                        ]);
                        $packageIds[$boxNo] = $package->id;
                        $boxNo++;
                    }

                    // ---- Create CSB Information (minimal) ----
                    $csb = CsbInformation::create([
                        'shipper_id' => $shipperId,
                        'ecommerce' => 'No',
                        'scheme' => 'No',
                        'bond_ut_igst' => null,
                        'lut_number' => null,
                        'iec_code' => null,
                        'gst_number' => $gstIdNo,
                        'ad_code' => null,
                        'bank_account_number' => null,
                        'bank_ifsc_code' => null,
                    ]);

                    // ---- Create Shipment Invoice (one per consignee/AwbNo) ----
                    $invoiceNo = $getCol($firstRow, 'invoiceno') ?: ('INV-' . $newAwbNumber);
                    $invoiceValue = round(floatval($getCol($firstRow, 'invoicevalue') ?: 0), 2);
                    $currency = $getCol($firstRow, 'currency') ?: 'INR';

                    $invoice = ShipmentInvoice::create([
                        'shipper_id' => $shipperId,
                        'invoice_number' => $invoiceNo,
                        'invoice_date' => now()->toDateString(),
                        'invoice_amount' => $invoiceValue,
                        'incoterms' => 'DAP',
                        'invoice_currency' => $currency,
                        'reference_number' => $referenceNo,
                    ]);

                    // ---- Create Invoice Items (one per row) ----
                    // Use the invoiceValue from the current Excel row as the item's unit_rate.
                    $itemBoxNo = 1;
                    foreach ($rowGroup as $r) {
                        $description = $getCol($r, 'description');
                        $qty = floatval($getCol($r, 'pcs') ?: 1);
                        $unitRate = round(floatval($getCol($r, 'invoicevalue') ?: 0), 2);
                        $amount = round($qty * $unitRate, 2);

                        ShipmentInvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'package_dimension_id' => $packageIds[$itemBoxNo] ?? null,
                            'box_no' => $itemBoxNo,
                            'description' => $description,
                            'hs_code' => null,
                            'hts_code' => null,
                            'unit_type' => 'PCS',
                            'qty' => $qty,
                            'unit_rate' => $unitRate,
                            'igst_percentage' => 0,
                            'igst_amount' => 0,
                            'amount' => $amount,
                        ]);
                        $itemBoxNo++;
                    }

                    // ---- Create CreateShipment record ----
                    $createShipment = CreateShipment::create([
                        'shipper_id' => $shipper->id,
                        'customer_id' => $customerId,
                        'awb_number' => $newAwbNumber,
                        'shipping_method' => $shippingMethod,
                        'delivery_destination' => $destination,
                        'origin_type' => 'CSB IV',
                        'shipper_company_name' => $shipperCompanyName,
                        'shipper_contact_person' => $shipperContactPerson,
                        'shipper_address_line1' => $shipperAddress1,
                        'shipper_address_line2' => $shipperAddress2,
                        'shipper_address_line3' => $shipperAddress3,
                        'shipper_pincode' => $shipperPincode,
                        'shipper_city' => $shipperCity,
                        'shipper_state' => $shipperState,
                        'shipper_phone_number' => $shipperPhone,
                        'shipper_email' => $shipperEmail,
                        'consignee_name' => $consigneeName,
                        'consignee_contact_person' => $consigneeContactPerson,
                        'consignee_address_line1' => $consigneeAddress1,
                        'consignee_address_line2' => $consigneeAddress2,
                        'consignee_address_line3' => $consigneeAddress3,
                        'consignee_zip_code' => $consigneeZip,
                        'consignee_city' => $consigneeCity,
                        'consignee_state' => $consigneeState,
                        'consignee_phone_number' => $consigneePhone,
                        'consignee_email' => $consigneePhone ? $consigneePhone . '@bulkupload.local' : 'consignee@bulkupload.local',
                        'invoice_number' => $invoiceNo,
                        'invoice_date' => now()->toDateString(),
                        'invoice_amount' => $invoiceValue,
                        'incoterms' => 'DAP',
                        'invoice_currency' => $currency,
                        'reference_number' => $referenceNo,
                        'ecommerce' => 'No',
                        'scheme' => 'No',
                        'bond_ut_igst' => null,
                        'lut_number' => null,
                        'iec_code' => null,
                        'gst_number' => $gstIdNo,
                        'ad_code' => null,
                        'bank_account_number' => null,
                        'bank_ifsc_code' => null,
                        'status' => 'draft',
                    ]);

                    // ---- Create Tracking record ----
                    Tracking::create([
                        'awb_number' => $newAwbNumber,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment->id,
                        'uwc_id' => $newAwbNumber,
                        'title' => Tracking::getTitleForStatus('draft'),
                        'status' => 'draft',
                    ]);

                    // ---- Log the draft status change ----
                    ShipmentLog::logStatus(
                        $shipper->id,
                        $newAwbNumber,
                        'draft',
                        null,
                        'Shipment created via bulk upload (draft)',
                        $customerId,
                        'customer'
                    );

                    // ---- Generate PDF invoice for this consignee ----
                    $pdfPath = $this->generateBulkInvoicePdf($shipper, $consignee, $invoice, $rateDetails, $totalChgWeight);

                    $createdShipments[] = [
                        'awb_number' => $newAwbNumber,
                        'consignee_name' => $consigneeName,
                        'consignee_city' => $consigneeCity,
                        'total_weight' => $totalChgWeight,
                        'rate' => $rateDetails['total'] ?? 0,
                        'invoice_pdf' => $pdfPath,
                        'invoice_number' => $invoiceNo,
                    ];
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'awb_no' => $awbNo,
                        'message' => $e->getMessage(),
                    ];
                    \Log::error('Bulk upload error for AwbNo ' . $awbNo . ': ' . $e->getMessage());
                }
            }

            DB::commit();

            return back()->with([
                'success' => $successCount . ' shipment(s) created successfully from the bulk upload.',
                'created_shipments' => $createdShipments,
                'upload_errors' => $errors,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed. Please upload a valid Excel file.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk upload failed: ' . $e->getMessage());
            return back()->with('error', 'Bulk upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Preview bulk upload: parse the Excel file, group by AwbNo, calculate rates,
     * and return JSON data for the preview modal (without creating any records).
     */
    public function previewBulkUpload(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $customer = auth()->guard('customer')->user();
        $customerId = $customer->id;

        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx,csv|max:20480',
        ]);

        try {
            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            if (count($rows) < 2) {
                return response()->json(['success' => false, 'message' => 'The uploaded file does not contain any data rows.']);
            }

            // Normalize headers
            $rawHeaders = array_map(function ($h) {
                return trim(preg_replace('/\s+/', '', strtolower((string) $h)));
            }, $rows[0]);

            $headerMap = [];
            foreach ($rawHeaders as $col => $header) {
                if ($header !== '') {
                    $headerMap[$header] = $col;
                }
            }

            $getCol = function ($row, $name) use ($headerMap) {
                if (isset($headerMap[$name]) && array_key_exists($headerMap[$name], $row)) {
                    return trim((string) $row[$headerMap[$name]]);
                }
                return null;
            };

            // Group rows by AwbNo
            $grouped = [];
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $awbNo = $getCol($row, 'awbno');
                if ($awbNo === '' || $awbNo === null) {
                    continue;
                }
                $grouped[$awbNo][] = $row;
            }

            if (empty($grouped)) {
                return response()->json(['success' => false, 'message' => 'No valid rows with AwbNo found in the uploaded file.']);
            }

            $previewShipments = [];
            $grandTotal = 0;
            $errors = [];

            // Fetch all ENABLED courier services once (mirrors getUpsRate all-services mode).
            // Disabled services (status = 0) are excluded so their rates are not shown.
            $allServices = \App\Models\CourierService::where('status', 1)->orderBy('network')->orderBy('method')->get();

            foreach ($grouped as $awbNo => $rowGroup) {
                $firstRow = $rowGroup[0];

                $consigneeName = $getCol($firstRow, 'consigneename');
                $consigneeCity = $getCol($firstRow, 'consigneecity');
                $consigneeState = $getCol($firstRow, 'consigneestate');
                $consigneeZip = $getCol($firstRow, 'consigneezipcode');
                $destination = $getCol($firstRow, 'destination');
                $serviceType = $getCol($firstRow, 'servicetype');

                // Calculate total chargeable weight
                $totalChgWeight = 0;
                $totalActWeight = 0;
                $totalPcs = 0;
                foreach ($rowGroup as $r) {
                    $totalChgWeight += floatval($getCol($r, 'chgweight') ?: 0);
                    $totalActWeight += floatval($getCol($r, 'actweight') ?: 0);
                    $totalPcs += intval($getCol($r, 'pcs') ?: 0);
                }
                if ($totalChgWeight <= 0) {
                    $totalChgWeight = $totalActWeight;
                }

                // Look up zone by consignee state (mirrors getUpsRate)
                $zone = null;
                if (!empty($consigneeState)) {
                    $zone = \App\Models\Zone::where('zone_code', $consigneeState)->first();
                }

                // Destination-based service filtering now uses the `country` column on
                // the courier_services table. The destination string (from the Excel
                // "Destination" column) is normalized to a country code ("UK",
                // "Canada", or "US") and compared against each service's `country`
                // value — a service is shown only when its country matches.
                $destinationCountry = $this->resolveDestinationCountry($destination);

                // Collect ALL available service rates for this shipment (like getUpsRate all-services mode)
                $allRates = [];
                $defaultRate = null; // first available rate as default selection

                foreach ($allServices as $service) {
                    // Country-based filtering: destination-specific services and
                    // services explicitly configured for ALL countries are eligible.
                    $serviceCountry = strtoupper(trim((string) ($service->country ?? 'US')));
                    if ($serviceCountry !== 'ALL' && strcasecmp($serviceCountry, $destinationCountry) !== 0) {
                        continue; // country mismatch → skip this service
                    }

                    // Fetch rates: customer-specific first, then default fallback
                    $rates = \App\Models\CourierRate::where('customer_id', $customerId)
                        ->where('service_id', $service->id)
                        ->orderBy('wt_range_start')
                        ->get();

                    if ($rates->isEmpty() && $customerId !== 0) {
                        $rates = \App\Models\CourierRate::where('customer_id', 0)
                            ->where('service_id', $service->id)
                            ->orderBy('wt_range_start')
                            ->get();
                    }

                    // Find rates matching weight AND zone.
                    // Matching uses the zone's `zone_number_testing` field (compared against
                    // the rate's `zone_no`). Zone-independent rates (zone_no=null/0) are
                    // always shown weight-wise; zone-matched rates are shown when the rate's
                    // zone_no equals the selected zone's zone_number_testing.
                    $matchedRates = $rates->filter(function ($r) use ($totalChgWeight, $zone) {
                        if (!($totalChgWeight >= $r->wt_range_start && $totalChgWeight <= $r->wt_range_end)) {
                            return false;
                        }
                        $zoneNo = $r->zone_no;
                        if ($zoneNo === null || $zoneNo == 0) {
                            return true;
                        }
                        if ($zone && $zone->zone_number_testing !== null && $zoneNo == $zone->zone_number_testing) {
                            return true;
                        }
                        return false;
                    });

                    foreach ($matchedRates as $matchedRate) {
                        $price = floatval($matchedRate->price);
                        $fuelPercentage = floatval($matchedRate->fuel_percentage);
                        $fuelChargeStored = floatval($matchedRate->fuel_charge);
                        $gstPercentage = floatval($matchedRate->gst_percentage);
                        $gstAmountStored = floatval($matchedRate->gst_amount);

                        // Mirror create-shipments computation exactly
                        $computedFuel = $fuelChargeStored > 0 ? $fuelChargeStored : ($price * $fuelPercentage / 100);
                        $computedGst = $gstAmountStored > 0 ? $gstAmountStored : (($price + $computedFuel) * $gstPercentage / 100);
                        $total = $price + $computedFuel + $computedGst;

                        $rateEntry = [
                            'rate_id' => $matchedRate->id,
                            'service_id' => $service->id,
                            'method' => $service->method,
                            'method_display' => $service->method . ' ' . $service->tat,
                            'network' => $service->network,
                            'method_code' => $service->method_code,
                            'tat' => $service->tat,
                            'scode' => $service->scode,
                            'price' => round($price, 2),
                            'zone_no' => $matchedRate->zone_no,
                            'zone_name' => ($matchedRate->zone_no && $zone && $matchedRate->zone_no == $zone->id) ? $zone->zone_name : null,
                            'zone_code' => ($matchedRate->zone_no && $zone && $matchedRate->zone_no == $zone->id) ? $zone->zone_code : null,
                            'fuel_charge' => round($computedFuel, 2),
                            'fuel_percentage' => $fuelPercentage,
                            'gst_percentage' => $gstPercentage,
                            'gst_amount' => round($computedGst, 2),
                            'total' => round($total, 2),
                        ];

                        $allRates[] = $rateEntry;
                        if ($defaultRate === null) {
                            $defaultRate = $rateEntry;
                        }
                    }
                }

                // Filter out rate cards whose total price (base + fuel + gst) is 0
                // so 0-price services are not shown in the bulk-upload preview.
                // Re-pick the first remaining rate as the default selection.
                $allRates = array_values(array_filter($allRates, function ($r) {
                    $base = floatval($r['price'] ?? 0);
                    $fuel = floatval($r['fuel_charge'] ?? 0);
                    $gst  = floatval($r['gst_amount'] ?? 0);
                    return ($base + $fuel + $gst) > 0;
                }));
                $defaultRate = !empty($allRates) ? $allRates[0] : null;

                $invoiceNo = $getCol($firstRow, 'invoiceno') ?: ('INV-' . $awbNo);
                $invoiceValue = floatval($getCol($firstRow, 'invoicevalue') ?: 0);

                $previewShipments[] = [
                    'awb_no' => $awbNo,
                    'consignee_name' => $consigneeName ?: '-',
                    'consignee_city' => $consigneeCity ?: '-',
                    'consignee_state' => $consigneeState ?: '-',
                    'consignee_zip' => $consigneeZip ?: '-',
                    'destination' => $destination ?: '-',
                    'pieces' => $totalPcs,
                    'total_weight' => round($totalChgWeight, 3),
                    'invoice_no' => $invoiceNo,
                    'invoice_value' => $invoiceValue,
                    'all_rates' => $allRates,
                    'default_rate' => $defaultRate,
                    'row_count' => count($rowGroup),
                ];

                $grandTotal += $defaultRate['total'] ?? 0;
            }

            return response()->json([
                'success' => true,
                'shipments' => $previewShipments,
                'grand_total' => round($grandTotal, 2),
                'total_shipments' => count($previewShipments),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Bulk upload preview failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Preview failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calculate the courier rate for a bulk upload shipment using ChgWeight.
     * Mirrors the logic in getUpsRate() but returns a single best-matching rate.
     *
     * @param int $customerId
     * @param \App\Models\CourierService|null $service
     * @param float $totalWeight
     * @param string|null $consigneeState
     * @return array
     */
    public function calculateBulkRate($customerId, $service, $totalWeight, $consigneeState)
    {
        $result = [
            'rate_id' => null,
            'price' => 0,
            'fuel_charge' => 0,
            'fuel_percentage' => 0,
            'gst_percentage' => 0,
            'gst_amount' => 0,
            'total' => 0,
        ];

        if (!$service || $totalWeight <= 0) {
            return $result;
        }

        // Look up zone by consignee state
        $zone = null;
        if (!empty($consigneeState)) {
            $zone = \App\Models\Zone::where('zone_code', $consigneeState)
                ->orWhere('zone_name', $consigneeState)
                ->first();
        }

        // Step 1: Try customer-specific rates
        $rates = \App\Models\CourierRate::where('customer_id', $customerId)
            ->where('service_id', $service->id)
            ->orderBy('wt_range_start')
            ->get();

        // Step 2: Fallback to default rates (customer_id = 0)
        if ($rates->isEmpty()) {
            $rates = \App\Models\CourierRate::where('customer_id', 0)
                ->where('service_id', $service->id)
                ->orderBy('wt_range_start')
                ->get();
        }

        if ($rates->isEmpty()) {
            return $result;
        }

        // Find the rate matching the weight AND zone.
        // Matching uses the zone's `zone_number_testing` field (compared against the
        // rate's `zone_no`). Zone-independent rates (zone_no=null/0) are always shown
        // weight-wise; zone-matched rates are shown when the rate's zone_no equals the
        // selected zone's zone_number_testing.
        $matchedRate = $rates->first(function ($r) use ($totalWeight, $zone) {
            if (!($totalWeight >= $r->wt_range_start && $totalWeight <= $r->wt_range_end)) {
                return false;
            }
            $zoneNo = $r->zone_no;
            if ($zoneNo === null || $zoneNo == 0) {
                return true; // Zone-independent rate
            }
            if ($zone && $zone->zone_number_testing !== null && $zoneNo == $zone->zone_number_testing) {
                return true; // Zone-matched rate (via zone_number_testing)
            }
            return false;
        });

        if (!$matchedRate) {
            return $result;
        }

        $price = floatval($matchedRate->price);
        $fuelPercentage = floatval($matchedRate->fuel_percentage);
        $fuelChargeStored = floatval($matchedRate->fuel_charge);
        $gstPercentage = floatval($matchedRate->gst_percentage);
        $gstAmountStored = floatval($matchedRate->gst_amount);

        // Mirror the create-shipments page computation exactly:
        // - If fuel_charge > 0 use it directly, otherwise compute from percentage
        // - If gst_amount > 0 use it directly, otherwise compute from percentage
        $computedFuel = $fuelChargeStored > 0 ? $fuelChargeStored : ($price * $fuelPercentage / 100);
        $computedGst = $gstAmountStored > 0 ? $gstAmountStored : (($price + $computedFuel) * $gstPercentage / 100);
        $total = $price + $computedFuel + $computedGst;

        return [
            'rate_id' => $matchedRate->id,
            'price' => round($price, 2),
            'fuel_charge' => round($computedFuel, 2),
            'fuel_percentage' => $fuelPercentage,
            'gst_percentage' => $gstPercentage,
            'gst_amount' => round($computedGst, 2),
            'total' => round($total, 2),
        ];
    }

    /**
     * Generate a PDF invoice for a bulk-uploaded shipment using dompdf.
     *
     * @param \App\Models\ShipperInfo $shipper
     * @param \App\Models\ConsigneeInfo $consignee
     * @param \App\Models\ShipmentInvoice $invoice
     * @param array $rateDetails
     * @param float $totalWeight
     * @return string Relative path to the generated PDF
     */
    private function generateBulkInvoicePdf($shipper, $consignee, $invoice, $rateDetails, $totalWeight)
    {
        $invoiceItems = ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get();

        $data = [
            'shipper' => $shipper,
            'consignee' => $consignee,
            'invoice' => $invoice,
            'invoiceItems' => $invoiceItems,
            'rateDetails' => $rateDetails,
            'totalWeight' => $totalWeight,
        ];

        $pdf = Pdf::loadView('customer.partials.bulk-invoice-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'bulk_invoice_' . $shipper->awb_number . '.pdf';
        $relativePath = 'uploads/bulk_invoices/' . $filename;
        $fullPath = public_path($relativePath);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        $pdf->save($fullPath);

        return $relativePath;
    }

    /**
     * Shared with CustomerController: resolve a courier service by shipping method name.
     */
    private function findCourierService($shippingMethod, $shipperId)
    {
        return app(CustomerController::class)->findCourierService($shippingMethod, $shipperId);
    }

    /**
     * Shared with CustomerController: generate the next AWB number.
     */
    private function generateAwbNumber()
    {
        return app(CustomerController::class)->generateAwbNumber();
    }

    /**
     * Shared with CustomerController: normalize a destination to a country code.
     */
    private function resolveDestinationCountry($destination)
    {
        return app(CustomerController::class)->resolveDestinationCountry($destination);
    }
}
