<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\Wallet;
use App\Models\BusinessCategory;
use App\Models\KycDetail;
use App\Models\AboutPageContent;
use App\Models\HomePageContent;
use App\Models\ShipperInfo;
use App\Models\ConsigneeInfo;
use App\Models\PackageDimension;
use App\Models\CsbInformation;
use App\Models\ShipmentInvoice;
use App\Models\ShipmentInvoiceItem;
use App\Models\CsbForm;
use App\Models\CreateShipment;
use App\Models\ShipmentTracking;
use App\Models\Tracking;
use App\Models\CourierService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class customerController extends Controller
{
    public function login()
    {
        return view('customer.login');
    }
    
    public function register()
    {
        $businessCategories = BusinessCategory::active()->ordered()->get();
        return view('customer.register', compact('businessCategories'));
    }
    
    // public function index()
    // {
    //     $homeContent = HomePageContent::all();
        
    //     // Group by section type
    //     $heroData = $homeContent->where('section', 'hero')->pluck('content', 'field_name');
    //     $aboutData = $homeContent->where('section', 'about')->pluck('content', 'field_name');
    //     $processData = $homeContent->where('section', 'process')->pluck('content', 'field_name');
    //     $serviceCards = $homeContent->where('section', 'service_card')->orderBy('sort_order')->get();
    //     $shippingSolutions = $homeContent->where('section', 'shipping_solutions')->orderBy('sort_order')->get();
    //     $testimonials = $homeContent->where('section', 'testimonial')->orderBy('sort_order')->get();
    //     $faqs = $homeContent->where('section', 'faq')->orderBy('sort_order')->get();
        
    //     // Group service cards by sort_order (1, 2, 3)
    //     $serviceCard1 = $serviceCards->where('sort_order', 1)->pluck('content', 'field_name');
    //     $serviceCard2 = $serviceCards->where('sort_order', 2)->pluck('content', 'field_name');
    //     $serviceCard3 = $serviceCards->where('sort_order', 3)->pluck('content', 'field_name');
        
    //     // Group shipping solutions by sort_order (1, 2, 3, 4)
    //     $shippingSolution1 = $shippingSolutions->where('sort_order', 1)->pluck('content', 'field_name');
    //     $shippingSolution2 = $shippingSolutions->where('sort_order', 2)->pluck('content', 'field_name');
    //     $shippingSolution3 = $shippingSolutions->where('sort_order', 3)->pluck('content', 'field_name');
    //     $shippingSolution4 = $shippingSolutions->where('sort_order', 4)->pluck('content', 'field_name');
        
        
        
    /**
     * Send OTP via SMS using authkey.io API
     */
    private function sendOtpViaSms($mobile, $otp)
    {
        $authkey = config('services.sms.authkey');
        $sid = config('services.sms.sender_id');
        $countryCode = config('services.sms.country_code');

        $url = "https://api.authkey.io/request?authkey=" . $authkey
            . "&mobile=" . $mobile
            . "&country_code=" . $countryCode
            . "&sid=" . $sid
            . "&otp=" . $otp;


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            \Log::error('SMS cURL error: ' . $curlError);
            return false;
        }

        \Log::info('SMS API Response - HTTP: ' . $httpCode . ' | Body: ' . ($response ?: 'empty'));

        return true;
    }

    public function checkPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number is required'
            ], 422);
        }

        try {
            $customer = Customer::where('phone_number', $request->phone_number)->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number not found. Please check your number or register first.'
                ], 404);
            }

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store OTP in session with expiry timestamp (5 minutes)
            session([
                'login_otp' => $otp,
                'login_phone' => $request->phone_number,
                'login_otp_expires_at' => now()->addMinutes(5)->timestamp
            ]);

            // Send OTP via SMS
            $smsSent = $this->sendOtpViaSms($request->phone_number, $otp);

            if (!$smsSent) {
                // Log the OTP for development/testing if SMS fails
                \Log::warning('SMS sending failed. OTP for ' . $request->phone_number . ': ' . $otp);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your registered mobile number.',
                'customer_id' => $customer->id
            ], 200);

        } catch (\Exception $e) {
            \Log::error('checkPhone error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again.'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:20',
            'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP format'
            ], 422);
        }

        try {
            // Validate OTP from session
            $sessionOtp = session('login_otp');
            $sessionPhone = session('login_phone');
            $expiresAt = session('login_otp_expires_at');

            // Check if OTP exists in session
            if (!$sessionOtp || !$sessionPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'No OTP was requested. Please click "Get OTP" first.'
                ], 400);
            }

            // Check if OTP is expired
            if (now()->timestamp > $expiresAt) {
                // Clear expired OTP
                session()->forget(['login_otp', 'login_phone', 'login_otp_expires_at']);
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new OTP.'
                ], 400);
            }

            // Verify phone number matches
            if ($sessionPhone !== $request->phone_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number mismatch. Please request a new OTP.'
                ], 400);
            }

            // Verify OTP
            if ((string) $sessionOtp !== $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.'
                ], 400);
            }

            // OTP verified - find customer and log them in
            $customer = Customer::where('phone_number', $request->phone_number)->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number not found.'
                ], 404);
            }

            // Clear OTP from session
            session()->forget(['login_otp', 'login_phone', 'login_otp_expires_at']);

            // Authenticate customer using Laravel's auth system
            auth()->guard('customer')->login($customer);
            
            // Also keep session data for compatibility
            session(['customer_id' => $customer->id, 'customer_name' => $customer->first_name . ' ' . $customer->last_name]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully! Redirecting to dashboard...',
                'redirect' => route('customer.dashboard'),
                'customer' => [
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone_number
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('verifyOtp error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Verification failed. Please try again.'
            ], 500);
        }
    }

    public function dashboard()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();
        $customerId = $customer->id;

        // Get shipment counts by status for this customer
        $statusCounts = ShipperInfo::where('customer_id', $customerId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Calculate totals for stat cards
        $totalBooked = array_sum($statusCounts);
        $pickupPending = $statusCounts['assigned_for_pickup'] ?? 0;
        $outForDelivery = ($statusCounts['dispatched'] ?? 0) + ($statusCounts['ready_to_dispatch'] ?? 0);
        $delivered = $statusCounts['delivered'] ?? 0;

        // Month-over-month percentage changes for stat cards
        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        // This month's status counts
        $thisMonthStatusCounts = ShipperInfo::where('customer_id', $customerId)
            ->whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $thisMonthBooked = array_sum($thisMonthStatusCounts);
        $thisMonthPickupPending = $thisMonthStatusCounts['assigned_for_pickup'] ?? 0;
        $thisMonthOutForDelivery = ($thisMonthStatusCounts['dispatched'] ?? 0) + ($thisMonthStatusCounts['ready_to_dispatch'] ?? 0);
        $thisMonthDelivered = $thisMonthStatusCounts['delivered'] ?? 0;

        // Last month's status counts
        $lastMonthStatusCounts = ShipperInfo::where('customer_id', $customerId)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $lastMonthBooked = array_sum($lastMonthStatusCounts);
        $lastMonthPickupPending = $lastMonthStatusCounts['assigned_for_pickup'] ?? 0;
        $lastMonthOutForDelivery = ($lastMonthStatusCounts['dispatched'] ?? 0) + ($lastMonthStatusCounts['ready_to_dispatch'] ?? 0);
        $lastMonthDelivered = $lastMonthStatusCounts['delivered'] ?? 0;

        // Calculate percentage changes (avoid division by zero)
        $bookedChangePercent = $lastMonthBooked > 0 ? round(($thisMonthBooked - $lastMonthBooked) / $lastMonthBooked * 100, 1) : ($thisMonthBooked > 0 ? 100 : 0);
        $pickupPendingChangePercent = $lastMonthPickupPending > 0 ? round(($thisMonthPickupPending - $lastMonthPickupPending) / $lastMonthPickupPending * 100, 1) : ($thisMonthPickupPending > 0 ? 100 : 0);
        $outForDeliveryChangePercent = $lastMonthOutForDelivery > 0 ? round(($thisMonthOutForDelivery - $lastMonthOutForDelivery) / $lastMonthOutForDelivery * 100, 1) : ($thisMonthOutForDelivery > 0 ? 100 : 0);
        $deliveredChangePercent = $lastMonthDelivered > 0 ? round(($thisMonthDelivered - $lastMonthDelivered) / $lastMonthDelivered * 100, 1) : ($thisMonthDelivered > 0 ? 100 : 0);

        // Recent shipments for the orders table (latest 20)
        $recentShipments = ShipperInfo::where('customer_id', $customerId)
            ->with(['consigneeInfo', 'packageDimensions'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Wallet balance
        $wallet = Wallet::where('customer_id', $customerId)->first();
        $walletBalance = $wallet ? $wallet->balance : 0;

        // Financials: total value of products shipped and total shipped cost
        $shipperIds = ShipperInfo::where('customer_id', $customerId)->pluck('id');
        $totalShippedValue = ShipmentInvoice::whereIn('shipper_id', $shipperIds)->sum('invoice_amount');
        $totalShippedCost = ShipmentInvoiceItem::whereIn('invoice_id', function($q) use ($shipperIds) {
            $q->select('id')->from('shipment_invoice')->whereIn('shipper_id', $shipperIds);
        })->sum('amount');

        return view('customer.dashboard', compact(
            'customer', 'totalBooked', 'pickupPending', 'outForDelivery', 'delivered',
            'recentShipments', 'walletBalance', 'totalShippedValue', 'totalShippedCost',
            'bookedChangePercent', 'pickupPendingChangePercent', 'outForDeliveryChangePercent', 'deliveredChangePercent'
        ));
    }

    /**
     * Return chart data for the customer dashboard via AJAX.
     * Supports date filters: today, yesterday, this_month, last_month, last_year
     */
    public function dashboardChartData(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $customer = auth()->guard('customer')->user();
        $customerId = $customer->id;
        $filter = $request->input('filter', 'this_month');

        // Determine date range based on filter
        $now = now();
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

        // Status breakdown for the filtered period
        $statusCounts = ShipperInfo::where('customer_id', $customerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Date-wise shipment counts (group by month for last_year, by day otherwise)
        if ($filter === 'last_year') {
            $dateWiseCounts = ShipperInfo::where('customer_id', $customerId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as date"), DB::raw('count(*) as count'))
                ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
                ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m')")
                ->pluck('count', 'date')
                ->toArray();
        } else {
            $dateWiseCounts = ShipperInfo::where('customer_id', $customerId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupByRaw('DATE(created_at)')
                ->orderByRaw('DATE(created_at)')
                ->pluck('count', 'date')
                ->toArray();
        }

        $statusMap = Tracking::getStatusTitleMap();

        return response()->json([
            'success' => true,
            'statusCounts' => $statusCounts,
            'statusMap' => $statusMap,
            'dateWiseCounts' => $dateWiseCounts,
            'filter' => $filter,
        ]);
    }

    public function logout(Request $request)
    {
        // Logout customer using auth guard
        auth()->guard('customer')->logout();
        
        // Clear customer session
        session()->forget(['customer_id', 'customer_name']);
        
        // Invalidate the session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
    
        
    public function companies()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        return view('customer.companies');
    }
    
    public function createShipment()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();
        $courierServices = \App\Models\CourierService::all();
        $zones = \App\Models\Zone::orderBy('zone_name')->get();
        return view('customer.create-shipment', compact('customer', 'courierServices', 'zones'));
    }
    
    public function kycSubmit(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'gst_number' => 'nullable|string|max:20',
                'gst_verified' => 'boolean',
                'otp_verified' => 'boolean',
                'organization_name' => 'nullable|string|max:255',
                'authorized_signatory' => 'nullable|string|max:255',
                'terms_accepted' => 'boolean',
                'terms_accepted_at' => 'nullable|date',
            ],);

            // Get current customer
            $customer = auth()->guard('customer')->user();
            
            // Prepare KYC data
            $kycData = [
                'customer_id' => $customer->id,
                'gst_number' => $request->gst_number,
                'gst_verified' => $request->gst_verified ?? false,
                'otp_verified' => $request->otp_verified ?? false,
                'organization_name' => $request->organization_name,
                'authorized_signatory' => $request->authorized_signatory,
                'terms_accepted' => $request->terms_accepted ?? false,
                'terms_accepted_at' => $request->terms_accepted ? now() : null,
                'kyc_status' => 'pending', // Set status to under_review after submission
            ];

            // Create KYC record
            $kyc = KycDetail::create($kycData);

            return response()->json([
                'success' => true,
                'message' => 'KYC application submitted successfully! Your application is now under review.',
                'kyc_id' => $kyc->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting KYC application: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function csb5Form()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        return view('customer.csb5-form');
    }

    public function storeCsb5Form(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'is_csb_v' => 'required|boolean',
                'is_gst' => 'required|boolean',
                'is_lut' => 'required|boolean',
                'ad_code' => 'required|string|max:50',
                'iec_number' => 'required|string|max:50',
                'bank_account_number' => 'required|string|max:50',
                'lut_document' => 'nullable|file|mimes:pdf|max:5120',
            ]);

            // Get current customer
            $customer = auth()->guard('customer')->user();

            // Handle LUT document upload
            $lutDocumentPath = null;
            if ($request->hasFile('lut_document')) {
                $file = $request->file('lut_document');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/lut_documents'), $filename);
                $lutDocumentPath = 'uploads/lut_documents/' . $filename;
            }

            // Create CSB Form record
            $csbForm = CsbForm::create([
                'customer_id' => $customer->id,
                'is_csb_v' => $validated['is_csb_v'],
                'is_gst' => $validated['is_gst'],
                'is_lut' => $validated['is_lut'],
                'ad_code' => $validated['ad_code'],
                'iec_number' => $validated['iec_number'],
                'bank_account_number' => $validated['bank_account_number'],
                'lut_document' => $lutDocumentPath,
            ]);

            // Update customer CSB status based on selection
            $customer->csb_status = $validated['is_csb_v'] ? 2 : 1;
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'CSB form submitted successfully!',
                'redirect' => route('customer.dashboard')
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting CSB form: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:customers,email',
            'phone_number' => 'required|string|max:20',
            'alternate_phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'aadhar_number' => 'nullable|string|max:20',
            'business_category' => 'nullable|string',
            'termsCheck' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = Customer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'alternate_phone_number' => $request->alternate_phone_number,
                'password_hash' => Hash::make($request->password),
                'aadhar_number' => $request->aadhar_number,
                'business_category_id' => $request->business_category,
                'is_terms_accepted' => $request->has('termsCheck'),
                'email_verified' => false,
                'aadhar_verified' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Please check your email for verification.',
                'redirect' => route('customer.login')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store shipment data from create-shipment form
     */
    public function storeShipment(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                // Shipper Info
                'delivery_destination' => 'required|string|max:100',
                'origin_type' => 'required|string|max:50',
                'shipping_method' => 'nullable|string|max:100',
                'service_rate_id' => 'nullable|integer',
                'shipper_same_as_customer' => 'boolean',
                'shipper_company_names' => 'required|string|max:150',
                'shipper_contact_person' => 'required|string|max:100',
                'shipper_address_line1' => 'required|string|max:255',
                'shipper_address_line2' => 'nullable|string|max:255',
                'shipper_address_line3' => 'nullable|string|max:255',
                'shipper_pincode' => 'required|string|max:20',
                'shipper_city' => 'required|string|max:100',
                'shipper_state' => 'required|string|max:100',
                'shipper_phone_number' => 'required|string|max:30',
                'shipper_emails' => 'required|email|max:150',
                'shipper_email_opt_out' => 'boolean',
                'shipper_kyc_type' => 'nullable|string|max:50',
                'shipper_kyc_number' => 'nullable|string|max:100',

                // Consignee Info
                'consignee_name' => 'required|string|max:150',
                'consignee_contact_person' => 'required|string|max:100',
                'consignee_address_line1' => 'required|string|max:255',
                'consignee_address_line2' => 'nullable|string|max:255',
                'consignee_address_line3' => 'nullable|string|max:255',
                'consignee_zip_code' => 'required|string|max:20',
                'consignee_city' => 'required|string|max:100',
                'consignee_state' => 'nullable|string|max:100',
                'consignee_phone_number' => 'required|string|max:30',
                'consignee_email' => 'required|email|max:150',
                'consignee_email_opt_out' => 'boolean',

                // Package Dimension
                'number_of_boxes' => 'nullable|integer|min:1|max:50',
                'package_shipping_method' => 'nullable|string|max:100',
                'packages' => 'nullable|array',
                'packages.*.actual_weight_kg' => 'nullable|numeric|min:0',
                'packages.*.length_cm' => 'nullable|numeric|min:0',
                'packages.*.width_cm' => 'nullable|numeric|min:0',
                'packages.*.height_cm' => 'nullable|numeric|min:0',
                'packages.*.volumetric_weight' => 'nullable|numeric|min:0',
                'packages.*.chargeable_weight' => 'nullable|numeric|min:0',

                // CSB Information
                'ecommerce' => 'required_if:origin_type,CSB V|nullable|in:Yes,No',
                'scheme' => 'required_if:origin_type,CSB V|nullable|in:Yes,No',
                'bond_ut_igst' => 'nullable|in:Bond UT,IGST',
                'lut_number' => 'nullable|string|max:100',
                'iec_code' => 'nullable|string|max:50',
                'gst_number' => 'nullable|string|max:50',
                'ad_code' => 'nullable|string|max:100',
                'bank_account_number' => 'nullable|string|max:50',
                'bank_ifsc_code' => 'nullable|string|max:20',

                // Invoice Information
                'invoice_number' => 'required|string|max:100',
                'invoice_date' => 'required|date',
                'invoice_amount' => 'required|numeric|min:0',
                'incoterms' => 'required|string|max:50',
                'invoice_currency' => 'required|string|max:20',
                'reference_number' => 'nullable|string|max:100',


                // invoice items
                'items.*.box_no' => 'nullable|integer',
                'items.*.description' => 'nullable|string|max:500',
                'items.*.hs_code' => 'nullable|string|max:50',
                'items.*.hts_code' => 'nullable|string|max:50',
                'items.*.unit_type' => 'nullable|string|max:50',
                'items.*.qty' => 'nullable|numeric|min:0',
                'items.*.unit_rate' => 'nullable|numeric|min:0',
                'items.*.igst_percentage' => 'nullable|numeric|min:0|max:100',
                'items.*.igst_amount' => 'nullable|numeric|min:0',
                'items.*.amount' => 'nullable|numeric|min:0',

            ]);

            // Check if origin_type is CSB V and customer has CSB status 1 (CSB-IV only)
            if ($validatedData['origin_type'] === 'CSB V') {
                $customer = auth()->guard('customer')->user();
                if ($customer->csb_status === 1) {
                    if (!$request->expectsJson()) {
                        return back()
                            ->withErrors([
                                'origin_type' => 'CSB V requires CSB V onboarding. Your current status is CSB-IV only.'
                            ])
                            ->withInput()
                            ->with('error', 'You are not authorized to create shipments with CSB V origin type. Please complete CSB V onboarding first.');
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized to create shipments with CSB V origin type. Please complete CSB V onboarding first.',
                        'errors' => [
                            'origin_type' => ['CSB V requires CSB V onboarding. Your current status is CSB-IV only.']
                        ]
                    ], 422);
                }
            }

            // Resolve shipping_method from service_id when the shipping_method
            // <select> dropdown is empty but a DDP/DDU radio button was selected.
            // The JS at line 8610 sends 'service_id' alongside FormData.
            if (empty($validatedData['shipping_method'])) {
                $serviceId = $request->input('service_id');
                if ($serviceId) {
                    $courierService = CourierService::find($serviceId);
                    if ($courierService) {
                        $validatedData['shipping_method'] = $courierService->method;
                        \Log::info('storeShipment: Resolved shipping_method from service_id #' . $serviceId . ' → "' . $courierService->method . '"');
                    }
                }
            }

            // Store Shipper Info
            $awbNumber = $this->generateAwbNumber();
            $shipper = ShipperInfo::create([
                'customer_id' => auth()->guard('customer')->id(),
                'awb_number' => $awbNumber,
                'shipping_method' => $validatedData['shipping_method'] ?? null,
                'shipper_same_as_customer' => $validatedData['shipper_same_as_customer'] ?? false,
                'company_name' => $validatedData['shipper_company_names'],
                'contact_person' => $validatedData['shipper_contact_person'],
                'address_line1' => $validatedData['shipper_address_line1'],
                'address_line2' => $validatedData['shipper_address_line2'] ?? null,
                'address_line3' => $validatedData['shipper_address_line3'] ?? null,
                'pincode' => $validatedData['shipper_pincode'],
                'city' => $validatedData['shipper_city'],
                'state' => $validatedData['shipper_state'],
                'phone_number' => $validatedData['shipper_phone_number'],
                'email' => $validatedData['shipper_emails'],
                'email_opt_out' => $validatedData['shipper_email_opt_out'] ?? false,
                'kyc_type' => $validatedData['shipper_kyc_type'] ?? null,
                'kyc_number' => $validatedData['shipper_kyc_number'] ?? null,
                'service_rate_id' => $validatedData['service_rate_id'] ?? null,
            ]);

            $shipperId = $shipper->id;

            // Store Consignee Info
            $consignee = ConsigneeInfo::create([
                'shipper_id' => $shipperId,
                'delivery_destination' => $validatedData['delivery_destination'],
                'origin_type' => $validatedData['origin_type'],
                'consignee_name' => $validatedData['consignee_name'],
                'contact_person' => $validatedData['consignee_contact_person'],
                'address_line1' => $validatedData['consignee_address_line1'],
                'address_line2' => $validatedData['consignee_address_line2'] ?? null,
                'address_line3' => $validatedData['consignee_address_line3'] ?? null,
                'zip_code' => $validatedData['consignee_zip_code'],
                'city' => $validatedData['consignee_city'],
                'state' => $validatedData['consignee_state'] ?? null,
                'phone_number' => $validatedData['consignee_phone_number'],
                'email' => $validatedData['consignee_email'],
                'email_opt_out' => $validatedData['consignee_email_opt_out'] ?? false,
            ]);

            // Store Package Dimensions
            $packageIds = [];
            $packageShippingMethod = $validatedData['package_shipping_method'] ?? null;
            $packageRows = $validatedData['packages'] ?? [[]];

            foreach ($packageRows as $packageData) {
                $hasPackageValue = collect($packageData)->filter(function ($value) {
                    return $value !== null && $value !== '';
                })->isNotEmpty() || !empty($packageShippingMethod);

                if (!$hasPackageValue) {
                    continue;
                }

                $package = PackageDimension::create([
                    'shipper_id' => $shipperId,
                    'shipping_method' => $packageShippingMethod,
                    'actual_weight_kg' => $packageData['actual_weight_kg'] ?? null,
                    'length_cm' => $packageData['length_cm'] ?? null,
                    'width_cm' => $packageData['width_cm'] ?? null,
                    'height_cm' => $packageData['height_cm'] ?? null,
                    'volumetric_weight' => $packageData['volumetric_weight'] ?? null,
                    'chargeable_weight' => $packageData['chargeable_weight'] ?? null,
                ]);

                $packageIds[] = $package->id;
            }

            // Store CSB Information
            $csb = CsbInformation::create([
                'shipper_id' => $shipperId,
                'ecommerce' => $validatedData['ecommerce'] ?? 'No',
                'scheme' => $validatedData['scheme'] ?? 'No',
                'bond_ut_igst' => $validatedData['bond_ut_igst'] ?? null,
                'lut_number' => $validatedData['lut_number'] ?? null,
                'iec_code' => $validatedData['iec_code'] ?? null,
                'gst_number' => $validatedData['gst_number'] ?? null,
                'ad_code' => $validatedData['ad_code'] ?? null,
                'bank_account_number' => $validatedData['bank_account_number'] ?? null,
                'bank_ifsc_code' => $validatedData['bank_ifsc_code'] ?? null,
            ]);

            // Store Shipment Invoice
            $invoice = ShipmentInvoice::create([
                'shipper_id' => $shipperId,
                'invoice_number' => $validatedData['invoice_number'],
                'invoice_date' => $validatedData['invoice_date'],
                'invoice_amount' => $validatedData['invoice_amount'],
                'incoterms' => $validatedData['incoterms'],
                'invoice_currency' => $validatedData['invoice_currency'],
                'reference_number' => $validatedData['reference_number'] ?? null,
            ]);

            // Store Invoice Items
            \Log::info('Items data received:', $validatedData['items'] ?? []);
            if (isset($validatedData['items']) && is_array($validatedData['items'])) {
                foreach ($validatedData['items'] as $item) {
                    \Log::info('Processing item:', $item);
                    // Map box_no to package_dimension_id (box_no 1 = packageIds[0], box_no 2 = packageIds[1], etc.)
                    $boxNo = $item['box_no'] ?? null;
                    $packageDimensionId = null;
                    if ($boxNo !== null && isset($packageIds[$boxNo - 1])) {
                        $packageDimensionId = $packageIds[$boxNo - 1];
                    }
                    ShipmentInvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'package_dimension_id' => $packageDimensionId,
                        'box_no' => $boxNo,
                        'description' => $item['description'] ?? null,
                        'hs_code' => $item['hs_code'] ?? null,
                        'hts_code' => $item['hts_code'] ?? null,
                        'unit_type' => $item['unit_type'] ?? null,
                        'qty' => $item['qty'] ?? null,
                        'unit_rate' => $item['unit_rate'] ?? null,
                        'igst_percentage' => $item['igst_percentage'] ?? null,
                        'igst_amount' => $item['igst_amount'] ?? null,
                        'amount' => $item['amount'] ?? null,
                    ]);
                }
            } else {
                \Log::info('No items data received');
            }

            // Store into create_shipment table
            $createShipment = CreateShipment::create([
                'customer_id' => auth()->guard('customer')->id(),
                'shipper_id' => $shipperId,
                'awb_number' => $awbNumber,
                'delivery_destination' => $validatedData['delivery_destination'],
                'origin_type' => $validatedData['origin_type'],
                'shipping_method' => $validatedData['shipping_method'] ?? null,
                'shipper_same_as_customer' => $validatedData['shipper_same_as_customer'] ?? false,
                'shipper_company_name' => $validatedData['shipper_company_names'],
                'shipper_contact_person' => $validatedData['shipper_contact_person'],
                'shipper_address_line1' => $validatedData['shipper_address_line1'],
                'shipper_address_line2' => $validatedData['shipper_address_line2'] ?? null,
                'shipper_address_line3' => $validatedData['shipper_address_line3'] ?? null,
                'shipper_pincode' => $validatedData['shipper_pincode'],
                'shipper_city' => $validatedData['shipper_city'],
                'shipper_state' => $validatedData['shipper_state'],
                'shipper_phone_number' => $validatedData['shipper_phone_number'],
                'shipper_email' => $validatedData['shipper_emails'],
                'shipper_email_opt_out' => $validatedData['shipper_email_opt_out'] ?? false,
                'shipper_kyc_type' => $validatedData['shipper_kyc_type'] ?? null,
                'shipper_kyc_number' => $validatedData['shipper_kyc_number'] ?? null,
                'consignee_name' => $validatedData['consignee_name'],
                'consignee_contact_person' => $validatedData['consignee_contact_person'],
                'consignee_address_line1' => $validatedData['consignee_address_line1'],
                'consignee_address_line2' => $validatedData['consignee_address_line2'] ?? null,
                'consignee_address_line3' => $validatedData['consignee_address_line3'] ?? null,
                'consignee_zip_code' => $validatedData['consignee_zip_code'],
                'consignee_city' => $validatedData['consignee_city'],
                'consignee_state' => $validatedData['consignee_state'] ?? null,
                'consignee_phone_number' => $validatedData['consignee_phone_number'],
                'consignee_email' => $validatedData['consignee_email'],
                'consignee_email_opt_out' => $validatedData['consignee_email_opt_out'] ?? false,
                'invoice_number' => $validatedData['invoice_number'],
                'invoice_date' => $validatedData['invoice_date'],
                'invoice_amount' => $validatedData['invoice_amount'],
                'incoterms' => $validatedData['incoterms'],
                'invoice_currency' => $validatedData['invoice_currency'],
                'reference_number' => $validatedData['reference_number'] ?? null,
                'ecommerce' => $validatedData['ecommerce'] ?? 'No',
                'scheme' => $validatedData['scheme'] ?? 'No',
                'bond_ut_igst' => $validatedData['bond_ut_igst'] ?? null,
                'lut_number' => $validatedData['lut_number'] ?? null,
                'iec_code' => $validatedData['iec_code'] ?? null,
                'gst_number' => $validatedData['gst_number'] ?? null,
                'ad_code' => $validatedData['ad_code'] ?? null,
                'bank_account_number' => $validatedData['bank_account_number'] ?? null,
                'bank_ifsc_code' => $validatedData['bank_ifsc_code'] ?? null,
                'status' => 'draft',
            ]);

            // Create initial tracking record for the shipment
            Tracking::create([
                'awb_number' => $awbNumber,
                'shipper_id' => $shipper->id,
                'shipping_id' => $createShipment->id,
                'uwc_id' => $awbNumber,
                'title' => Tracking::getTitleForStatus('draft'),
                'status' => 'draft',
            ]);

            if (!$request->expectsJson()) {
                return back()->with('success', 'Shipment created successfully!');
            }

            return response()->json([
                'success' => true,
                'message' => 'Shipment created successfully!',
                'data' => [
                    'create_shipment_id' => $createShipment->id,
                    'shipper_id' => $shipper->id,
                    'consignee_id' => $consignee->id,
                    'package_id' => $packageIds[0] ?? null,
                    'package_ids' => $packageIds,
                    'csb_id' => $csb->id,
                    'invoice_id' => $invoice->id,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if (!$request->expectsJson()) {
                return back()
                    ->withErrors($e->validator)
                    ->withInput()
                    ->with('error', 'Please correct the highlighted shipment details.');
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            if (!$request->expectsJson()) {
                return back()
                    ->withInput()
                    ->with('error', 'Failed to create shipment: ' . $e->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the Bulk Upload page (Excel upload form).
     */
    public function bulkUpload()
    {
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();
        $courierServices = \App\Models\CourierService::orderBy('network')->orderBy('method')->get();

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
                    $invoiceValue = floatval($getCol($firstRow, 'invoicevalue') ?: 0);
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
                    $itemBoxNo = 1;
                    foreach ($rowGroup as $r) {
                        $description = $getCol($r, 'description');
                        $qty = floatval($getCol($r, 'pcs') ?: 1);
                        $unitRate = $invoiceValue > 0 && $qty > 0 ? round($invoiceValue / $qty, 2) : 0;
                        $amount = $unitRate * $qty;

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

            // Fetch all courier services once (mirrors getUpsRate all-services mode)
            $allServices = \App\Models\CourierService::orderBy('network')->orderBy('method')->get();

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

                // Destination-based service filtering:
                // UK destination  → only show UNITED PRIOR POST DDP (nothing else)
                // Non-UK destination → exclude UNITED PRIOR POST DDP (show all others)
                // The Excel "Destination" column may contain short codes (UK, USA) or
                // full names (United Kingdom, United States of America), so match flexibly.
                $destUpper = strtoupper(trim($destination ?? ''));
                $isUkDestination = (
                    $destUpper === 'UK' ||
                    $destUpper === 'GB' ||
                    str_contains($destUpper, 'UNITED KINGDOM') ||
                    str_contains($destUpper, 'UK -') ||
                    str_contains($destUpper, 'GREAT BRITAIN')
                );

                // Collect ALL available service rates for this shipment (like getUpsRate all-services mode)
                $allRates = [];
                $defaultRate = null; // first available rate as default selection

                foreach ($allServices as $service) {
                    $methodUpper = strtoupper(trim($service->method ?? ''));
                    $isPriorPost = str_contains($methodUpper, 'UNITED PRIOR POST');
                    if ($isUkDestination && !$isPriorPost) {
                        continue; // UK: only UNITED PRIOR POST DDP is shown
                    }
                    if (!$isUkDestination && $isPriorPost) {
                        continue; // Non-UK: UNITED PRIOR POST DDP is hidden
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

                    // Find rates matching weight AND zone
                    $matchedRates = $rates->filter(function ($r) use ($totalChgWeight, $zone) {
                        if (!($totalChgWeight >= $r->wt_range_start && $totalChgWeight <= $r->wt_range_end)) {
                            return false;
                        }
                        $zoneNo = $r->zone_no;
                        if ($zoneNo === null || $zoneNo == 0) {
                            return true;
                        }
                        if ($zone && $zoneNo == $zone->id) {
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
    private function calculateBulkRate($customerId, $service, $totalWeight, $consigneeState)
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

        // Find the rate matching the weight AND zone
        $matchedRate = $rates->first(function ($r) use ($totalWeight, $zone) {
            if (!($totalWeight >= $r->wt_range_start && $totalWeight <= $r->wt_range_end)) {
                return false;
            }
            $zoneNo = $r->zone_no;
            if ($zoneNo === null || $zoneNo == 0) {
                return true; // Zone-independent rate
            }
            if ($zone && $zoneNo == $zone->id) {
                return true; // Zone-matched rate
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('customer.partials.bulk-invoice-pdf', $data);
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
     * Proxy UPS Rate API call
     */
    /**
     * Get UPS OAuth access token and cache it.
     * Expects UPS_CLIENT_ID and UPS_CLIENT_SECRET in environment.
     */
    private function getUpsAccessToken()
    {
        $cacheKey = 'ups_access_token';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $clientId = env('UPS_CLIENT_ID');
        $clientSecret = env('UPS_CLIENT_SECRET');
        $tokenUrl = 'https://onlinetools.ups.com/security/v1/oauth/token';

        if (!$clientId || !$clientSecret) {
            throw new \Exception('UPS client credentials not configured. Set UPS_CLIENT_ID and UPS_CLIENT_SECRET in .env');
        }

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post($tokenUrl, ['grant_type' => 'client_credentials']);

        if (!$response->successful()) {
            \Log::error('UPS token error: ' . $response->body());
            throw new \Exception('Unable to retrieve UPS access token');
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            \Log::error('UPS token missing access_token: ' . $response->body());
            throw new \Exception('UPS access token not found in response');
        }

        $expiresIn = isset($data['expires_in']) ? (int)$data['expires_in'] : 3600;
        $ttl = max(60, $expiresIn - 60);
        Cache::put($cacheKey, $data['access_token'], $ttl);

        return $data['access_token'];
    }


    
    public function getUpsRate(Request $request)
    {
        // print_r($request->all());
        try {
            $serviceId = $request->service_id;
            $totalWeight = floatval($request->total_weight ?? 0);
            $consigneeState = $request->consignee_state;
            $deliveryDestination = $request->delivery_destination;

            // Get the currently logged-in customer
            $customer = auth()->guard('customer')->user();
            $customerId = $customer ? $customer->id : 0;

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to view rates.'
                ], 401);
            }

            // Look up zone by consignee state (do this once for both modes)
            $zone = null;
            if (!empty($consigneeState)) {
                $zone = \App\Models\Zone::where('zone_code', $consigneeState)->first();
            }

            // DPD (PostShipping) rates are only available for UK destinations.
            // For any non-UK destination (e.g. US), DPD rates are hidden.
            $isUkDestination = ($deliveryDestination === 'UK - United Kingdom');

            // ALL-SERVICES MODE: When service_id is empty, return best matching rate for every service
            if (empty($serviceId)) {
                $allServices = \App\Models\CourierService::orderBy('network')->orderBy('method')->get();
                $allRates = [];
                $customerRatesExist = false;

                foreach ($allServices as $service) {
                    // Destination-based DPD filtering:
                    // - UK destination  → show ONLY DPD (PostShipping) services, hide all others.
                    // - Non-UK destination → hide DPD services, show all others.
                    $isDpd = $this->isPostShippingMethod($service->method);
                    if ($isUkDestination && !$isDpd) {
                        continue; // UK: skip non-DPD services
                    }
                    if (!$isUkDestination && $isDpd) {
                        continue; // Non-UK: skip DPD services
                    }

                    // Fetch ALL rates for this service (both zone-independent and zone-dependent)
                    // Priority: customer-specific rates first, then default rates as fallback
                    $rates = collect();

                    // Step 1: Try customer-specific rates (ALL zone_no values)
                    $rates = \App\Models\CourierRate::where('customer_id', $customerId)
                        ->where('service_id', $service->id)
                        ->orderBy('wt_range_start')
                        ->get();

                    if ($rates->isNotEmpty()) {
                        $customerRatesExist = true;
                    }

                    // Step 2: If no customer-specific rates, fall back to default rates (ALL zone_no values)
                    if ($rates->isEmpty() && $customerId !== 0) {
                        $rates = \App\Models\CourierRate::where('customer_id', 0)
                            ->where('service_id', $service->id)
                            ->orderBy('wt_range_start')
                            ->get();
                    }

                    // Find rates matching the current weight AND the selected zone
                    // Show zone-independent rates (zone_no=null/0) AND zone-matched rates only
                    $matchedRates = $rates->filter(function ($r) use ($totalWeight, $zone) {
                        if (!($totalWeight >= $r->wt_range_start && $totalWeight <= $r->wt_range_end)) {
                            return false;
                        }
                        $zoneNo = $r->zone_no;
                        if ($zoneNo === null || $zoneNo == 0) {
                            return true; // Zone-independent rate - always show
                        }
                        if ($zone && $zoneNo == $zone->id) {
                            return true; // Zone-matched rate
                        }
                        return false; // Rate from a different zone - exclude
                    });

                    foreach ($matchedRates as $matchedRate) {
                        $allRates[] = [
                            'rate_id' => $matchedRate->id,
                            'service_id' => $service->id,
                            'method' => $service->method,
                            'method_display' => $service->method . ' ' . $service->tat,
                            'network' => $service->network,
                            'method_code' => $service->method_code,
                            'tat' => $service->tat,
                            'delivery_days' => $service->tat,
                            'scode' => $service->scode,
                            'price' => $matchedRate->price,
                            'zone_no' => $matchedRate->zone_no,
                            'zone_name' => ($matchedRate->zone_no && $zone && $matchedRate->zone_no == $zone->id) ? $zone->zone_name : null,
                            'zone_code' => ($matchedRate->zone_no && $zone && $matchedRate->zone_no == $zone->id) ? $zone->zone_code : null,
                            'fuel_charge' => $matchedRate->fuel_charge,
                            'fuel_percentage' => $matchedRate->fuel_percentage,
                            'gst_percentage' => $matchedRate->gst_percentage,
                            'gst_amount' => $matchedRate->gst_amount,
                        ];
                    }
                }

                return response()->json([
                    'success' => true,
                    'customer_exists' => $customerRatesExist,
                    'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : null,
                    'selected_zone' => $zone ? [
                        'zone_id' => $zone->id,
                        'zone_number' => $zone->id,
                        'zone_name' => $zone->zone_name,
                        'zone_code' => $zone->zone_code,
                        'state' => $consigneeState, // The state you selected
                    ] : [
                        'state' => $consigneeState,
                        'message' => 'No zone found for the selected state'
                    ],
                    'all_rates' => $allRates,
                ]);
            }

            // SINGLE-SERVICE MODE: original behavior when service_id is provided
            // Find matching CourierService by ID
            $service = \App\Models\CourierService::find($serviceId);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching service found for ID: ' . $serviceId
                ], 404);
            }

            // Destination-based DPD filtering (mirrors ALL-SERVICES MODE):
            // - UK destination  → ONLY DPD (PostShipping) services are allowed; reject all others.
            // - Non-UK destination → DPD services are not allowed; reject them.
            $isDpdService = $this->isPostShippingMethod($service->method);
            if ($isUkDestination && !$isDpdService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only DPD services are available for UK destinations.'
                ], 404);
            }
            if (!$isUkDestination && $isDpdService) {
                return response()->json([
                    'success' => false,
                    'message' => 'DPD service is only available for UK destinations.'
                ], 404);
            }

            // Determine if this is traditionally a zone-independent service (for response metadata)
            $isZoneIndependent = str_contains(strtoupper($service->method), 'AIREXPRESS');

            // Find matching courier rates for the customer (or default)
            // Fetch ALL rates (both zone-independent and zone-dependent) without zone filtering
            $customerExists = true;
            $rates = \App\Models\CourierRate::where('customer_id', $customerId)
                ->where('service_id', $service->id)
                ->orderBy('wt_range_start')
                ->get();

            // If no customer-specific rates found, fall back to default rates
            if ($rates->isEmpty() && $customerId !== 0) {
                $rates = \App\Models\CourierRate::where('customer_id', 0)
                    ->where('service_id', $service->id)
                    ->orderBy('wt_range_start')
                    ->get();
                $customerExists = false;
            }

            // Find the rate that matches the current weight AND the selected zone
            // Show zone-independent rates (zone_no=null/0) AND zone-matched rates only
            $filteredRates = $rates->filter(function ($r) use ($totalWeight, $zone) {
                if (!($totalWeight >= $r->wt_range_start && $totalWeight <= $r->wt_range_end)) {
                    return false;
                }
                $zoneNo = $r->zone_no;
                if ($zoneNo === null || $zoneNo == 0) {
                    return true; // Zone-independent rate - always show
                }
                if ($zone && $zoneNo == $zone->id) {
                    return true; // Zone-matched rate
                }
                return false; // Rate from a different zone - exclude
            });

            return response()->json([
                'success' => true,
                'customer_exists' => $customerExists,
                'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : null,
                'selected_zone' => $zone ? [
                    'zone_id' => $zone->id,
                    'zone_number' => $zone->id,
                    'zone_name' => $zone->zone_name,
                    'zone_code' => $zone->zone_code,
                    'state' => $consigneeState, // The state you selected
                ] : [
                    'state' => $consigneeState,
                    'message' => 'No zone found for the selected state'
                ],
                'is_zone_independent' => $isZoneIndependent,
                'service' => [
                    'network' => $service->network,
                    'method' => $service->method,
                    'type' => $service->type,
                    'tat' => $service->tat,
                ],
                'matched_rate' => $filteredRates->isNotEmpty() ? [
                    'rate_id' => $filteredRates->first()->id,
                    'zone_no' => $filteredRates->first()->zone_no,
                    'wt_range_start' => $filteredRates->first()->wt_range_start,
                    'wt_range_end' => $filteredRates->first()->wt_range_end,
                    'price' => $filteredRates->first()->price,
                    'fuel_charge' => $filteredRates->first()->fuel_charge,
                    'fuel_percentage' => $filteredRates->first()->fuel_percentage,
                    'gst_percentage' => $filteredRates->first()->gst_percentage,
                    'gst_amount' => $filteredRates->first()->gst_amount,
                ] : null,
                'all_rates' => $filteredRates->map(function ($r) {
                    return [
                        'rate_id' => $r->id,
                        'zone_no' => $r->zone_no,
                        'wt_range_start' => $r->wt_range_start,
                        'wt_range_end' => $r->wt_range_end,
                        'price' => $r->price,
                        'fuel_charge' => $r->fuel_charge,
                        'fuel_percentage' => $r->fuel_percentage,
                        'gst_percentage' => $r->gst_percentage,
                        'gst_amount' => $r->gst_amount,
                    ];
                })->values(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get UPS OAuth access token for Ship API using provided credentials.
     */
    private function getUpsShipAccessToken()
    {
        $cacheKey = 'ups_ship_access_token';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $clientId = 'GSTEuQdy5XHnWalxGQECH4yhSqJAiydVNjho6AkPGn1ZwMYX';
        $clientSecret = 'fVuQ8CMYIzxpABWkZFcOM3AyW0x4i1zo7mwiZk7gyLjpD1IWawoCXa3OXWNfVjao';
        $tokenUrl = 'https://onlinetools.ups.com/security/v1/oauth/token';

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post($tokenUrl, ['grant_type' => 'client_credentials']);

        if (!$response->successful()) {
            \Log::error('UPS Ship token error: ' . $response->body());
            throw new \Exception('Unable to retrieve UPS Ship access token');
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            \Log::error('UPS Ship token missing access_token: ' . $response->body());
            throw new \Exception('UPS Ship access token not found in response');
        }

        $expiresIn = isset($data['expires_in']) ? (int)$data['expires_in'] : 3600;
        $ttl = max(60, $expiresIn - 60);
        Cache::put($cacheKey, $data['access_token'], $ttl);

        return $data['access_token'];
    }

    /**
     * Proxy UPS Ship API call.
     * POST https://onlinetools.ups.com/api/shipments/v2403/ship
     */
    public function createUpsShipment(Request $request)
    {
        try {
            $payload = $request->all();

            // Log payload for debugging
            try {
                \Log::info('UPS Ship payload: ' . substr(json_encode($payload), 0, 2000));
            } catch (\Exception $e) {
                // ignore logging errors
            }

            // Obtain cached UPS OAuth token
            try {
                $token = $this->getUpsShipAccessToken();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to obtain UPS Ship access token: ' . $e->getMessage()
                ], 500);
            }

            $ch = curl_init('https://onlinetools.ups.com/api/shipments/v2403/ship');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Bearer ' . $token,
                    'transId: ' . uniqid('ship_', true),
                    'transactionSrc: unitedcourier',
                ],
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                \Log::error('UPS Ship cURL error: ' . $curlError);
                return response()->json([
                    'success' => false,
                    'message' => 'UPS Ship API connection error',
                    'curl_error' => $curlError
                ], 500);
            }

            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::warning('UPS Ship returned non-JSON response. HTTP: ' . $httpCode . ' Body: ' . $response);
            } else {
                \Log::info('UPS Ship response HTTP: ' . $httpCode . ' Body: ' . substr($response, 0, 2000));
            }

            if ($httpCode >= 200 && $httpCode < 300 && isset($decoded['ShipmentResponse'])) {
                $shipmentResponse = $decoded['ShipmentResponse'];

                return response()->json([
                    'success' => true,
                    'shipmentResponse' => $shipmentResponse
                ]);
            }

            // Extract meaningful error message
            $errorMessage = 'Failed to create UPS shipment';
            if (isset($decoded['response']['errors'][0]['message'])) {
                $errorMessage = $decoded['response']['errors'][0]['message'];
            } elseif (isset($decoded['ShipmentResponse']['Response']['Error'][0]['ErrorDescription'])) {
                $errorMessage = $decoded['ShipmentResponse']['Response']['Error'][0]['ErrorDescription'];
            } elseif (isset($decoded['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['ErrorDescription'])) {
                $errorMessage = $decoded['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['ErrorDescription'];
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'rawResponse' => $decoded
            ], $httpCode ?: 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build UPS Ship API payload from validated form data and service info.
     * Mirrors the JavaScript buildShipPayload() function.
     */
    private function buildUpsShipPayload($validatedData, $service)
    {
        // Shipper & ShipFrom — hardcoded (same as JS buildShipPayload)
        $shipperName = "SANDEEP KAPUR";
        $shipperAttentionName = "United";
        $shipperCompanyDisplayableName = "UWC";
        $shipperPhone = "6466741258";


        // Shipper number based on service weight column:
        // - Services with weight "OZS" or "OZS/LBS" (Saver): X19700
        // - Services with weight "LBS" (2nd Day Air / Ground): 1255AK
        $serviceWeight = $service->weight ?? 'LBS';
        $isSaver = str_contains($serviceWeight, 'OZS');
        $shipperNumber = $isSaver ? "X19700" : "1255AK";

        $shipperAddressLine = "218 WEST 37 STREET 6TH FLOOR";
        $shipperCity = "NEW YORK";
        $shipperState = "NY";
        $shipperPostal = "10018";
        $shipperCountry = "US";

        // Consignee (ShipTo) — from form data
        $consigneeName = $validatedData['consignee_name'];
        $consigneePhone = $validatedData['consignee_phone_number'];
        $consigneeAddressLines = [];
        if (!empty($validatedData['consignee_address_line1'])) {
            $consigneeAddressLines[] = $validatedData['consignee_address_line1'];
        }
        if (!empty($validatedData['consignee_address_line2'])) {
            $consigneeAddressLines[] = $validatedData['consignee_address_line2'];
        }
        if (!empty($validatedData['consignee_address_line3'])) {
            $consigneeAddressLines[] = $validatedData['consignee_address_line3'];
        }
        $consigneeCity = $validatedData['consignee_city'];
        $consigneeState = $validatedData['consignee_state'] ?? '';
        $consigneePostal = $validatedData['consignee_zip_code'];
        $destCountry = $this->getCountryCodeFromDestination($validatedData['delivery_destination']);

        // Service code and description from the selected courier service
        $serviceCode = $service->scode;
        $serviceDescription = $this->getServiceDescriptionFromMethod($service->method);

        // Weight unit determined by courier_services.weight column:
        // - "LBS" → always LBS, no service code override
        // - "OZS" → always OZS, service code from scode
        // - "OZS/LBS" → dynamic: convert KG→LBS, <1 LBS → OZS + code 92, ≥1 LBS → LBS + code 93
        $weightUnit = 'LBS';


        if ($serviceWeight === 'OZS') {
            $serviceCode = $service->scode;
            $weightUnit = 'OZS';
        } elseif ($serviceWeight === 'OZS/LBS') {
            $maxWeightKg = 0;
            $preScanRows = $validatedData['packages'] ?? [];
            foreach ($preScanRows as $pkgData) {
                $w = floatval($pkgData['actual_weight_kg'] ?? 0);

                if ($w > $maxWeightKg) {
                    $maxWeightKg = $w;
                }
            }
            // Convert max weight to LBS and check threshold
            $maxWeightLbs = $maxWeightKg * 2.205;
            if ($maxWeightLbs > 0 && $maxWeightLbs < 1) {
                // Less than 1 LBS → service code 92, weight unit OZS
                $serviceCode = '92';
                $weightUnit = 'OZS';
                $serviceDescription = 'Ground Saver Less than 1 lb';
            } else {
                // 1 LBS or greater → service code 93, weight unit LBS
                // echo "Max weight in LBS: $maxWeightLbs\n";
                $serviceCode = '93';
                $weightUnit = 'LBS';
                $serviceDescription = 'Ground Saver 1 lbs or grater';
            }
        }
        // Build Packages array from form data
        $packages = [];
        $packageRows = $validatedData['packages'] ?? [];
        foreach ($packageRows as $pkgData) {
            $weightKg = $pkgData['actual_weight_kg'] ?? null;
            if (!$weightKg || $weightKg <= 0) {
                continue;
            }

            // Convert weight from KG to the appropriate unit:
            // - OZS: 1 KG = 35.274 OZS (for saver ≤0.440 KG, service code 92)
            // - LBS: 1 KG = 2.20462 LBS (for all other services and saver >0.440 KG)
            $convertedWeight = $weightUnit === 'OZS'
                ? round($weightKg * 35.274, 2)
                : round($weightKg * 2.20462, 2);

            $pkg = [
                'Description' => 'Documents',
                'Packaging' => ['Code' => '02'],

                'ReferenceNumber' => [
                    [
                        'Code' => '9S',
                        'Value' => 'ORDER12345'
                    ]
                ],

                'PackageWeight' => [
                    'UnitOfMeasurement' => ['Code' => $weightUnit],
                    'Weight' => (string) $convertedWeight,
                ],
            ];

            $lengthCm = $pkgData['length_cm'] ?? null;
            $widthCm = $pkgData['width_cm'] ?? null;
            $heightCm = $pkgData['height_cm'] ?? null;

            if ($lengthCm && $widthCm && $heightCm) {
                $pkg['Dimensions'] = [
                    'UnitOfMeasurement' => ['Code' => 'IN'],
                    'Length' => (string) $lengthCm,
                    'Width' => (string) $widthCm,
                    'Height' => (string) $heightCm,
                ];
            } else {
                $pkg['Dimensions'] = [
                    'UnitOfMeasurement' => ['Code' => 'IN'],
                    'Length' => '10',
                    'Width' => '8',
                    'Height' => '4',
                ];
            }

            $packages[] = $pkg;
        }
        
        // Fallback: single default package if no valid packages
        if (empty($packages)) {
            // Fallback: ~5KG converted to appropriate weight unit
            $fallbackWeight = $weightUnit === 'OZS' ? '176.37' : '11.02';
            $packages[] = [
                'Description' => 'Documents',
                'ReferenceNumber' => [
                    [
                        "Code" => "9S",
                        "Value" => "ORDER12345"
                    ]
                ],
                'Packaging' => ['Code' => '02'],
                'PackageWeight' => [
                    'UnitOfMeasurement' => ['Code' => $weightUnit],
                    'Weight' => $fallbackWeight,
                ],
                'Dimensions' => [
                    'UnitOfMeasurement' => ['Code' => 'IN'],
                    'Length' => '10',
                    'Width' => '8',
                    'Height' => '4',
                ],
            ];
        }

        // Build the full ShipmentRequest payload (same structure as JS)
        $payload = [
            'ShipmentRequest' => [
                'Request' => [
                    'RequestOption' => 'validate',
                    'TransactionReference' => [
                        'CustomerContext' => 'ORDER-12345'
                    ]
                ],
                'Shipment' => [
                    'Shipper' => [
                        'Name' => $shipperName,
                        'AttentionName' => $shipperAttentionName,
                        'CompanyDisplayableName' => $shipperCompanyDisplayableName,
                        'Phone' => ['Number' => $shipperPhone],
                        'ShipperNumber' => $shipperNumber,
                        'Address' => [
                            'AddressLine' => $shipperAddressLine,
                            'City' => $shipperCity,
                            'StateProvinceCode' => $shipperState,
                            'PostalCode' => $shipperPostal,
                            'CountryCode' => $shipperCountry,
                        ],
                    ],
                    'ShipFrom' => [
                        'Name' => $shipperName,
                        'AttentionName' => $shipperAttentionName,
                        'Phone' => ['Number' => $shipperPhone],
                        'Address' => [
                            'AddressLine' => [$shipperAddressLine],
                            'City' => $shipperCity,
                            'StateProvinceCode' => $shipperState,
                            'PostalCode' => $shipperPostal,
                            'CountryCode' => $shipperCountry,
                        ],
                    ],
                    'ShipTo' => [
                        'Name' => $consigneeName,
                        'AttentionName' => $consigneeName,
                        'Phone' => ['Number' => $consigneePhone],
                        'Address' => [
                            'AddressLine' => !empty($consigneeAddressLines) ? $consigneeAddressLines : ['Receiver Address'],
                            'City' => $consigneeCity,
                            'StateProvinceCode' => $consigneeState,
                            'PostalCode' => $consigneePostal,
                            'CountryCode' => $destCountry,
                        ],
                    ],
                    'PaymentInformation' => [
                        'ShipmentCharge' => [
                            'Type' => '01',
                            'BillShipper' => [
                                'AccountNumber' => $shipperNumber,
                            ],
                        ],
                    ],
                    'Service' => [
                        'Code' => $serviceCode,
                        'Description' => $serviceDescription,
                    ],
                    
                    'Package' => $packages,
                ],
                'LabelSpecification' => [
                    'LabelImageFormat' => ['Code' => 'PDF'],
                ],
            ],
        ];

        // return;
        // print_r($payload);

        return $payload;
    }

    /**
     * Call UPS Ship API directly (internal method).
     * Returns an array with 'success' bool and either 'shipmentResponse' or 'message'+'rawResponse'.
     */
    private function callUpsShipApiInternal($payload)
    {
        try {
            // Log payload for debugging
            \Log::info('UPS Ship payload (internal): ' . substr(json_encode($payload), 0, 2000));

            // Obtain cached UPS OAuth token
            try {
                $token = $this->getUpsShipAccessToken();
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Failed to obtain UPS Ship access token: ' . $e->getMessage(),
                ];
            }

            $ch = curl_init('https://onlinetools.ups.com/api/shipments/v2403/ship');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Bearer ' . $token,
                    'transId: ' . uniqid('ship_', true),
                    'transactionSrc: unitedcourier',
                ],
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                \Log::error('UPS Ship cURL error (internal): ' . $curlError);
                return [
                    'success' => false,
                    'message' => 'UPS Ship API connection error: ' . $curlError,
                ];
            }

            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::warning('UPS Ship returned non-JSON response. HTTP: ' . $httpCode . ' Body: ' . $response);
                return [
                    'success' => false,
                    'message' => 'UPS Ship returned non-JSON response',
                    'rawResponse' => $response,
                ];
            }

            \Log::info('UPS Ship response HTTP: ' . $httpCode . ' Body: ' . substr($response, 0, 2000));

            if ($httpCode >= 200 && $httpCode < 300 && isset($decoded['ShipmentResponse'])) {
                return [
                    'success' => true,
                    'shipmentResponse' => $decoded['ShipmentResponse'],
                ];
            }

            // Extract meaningful error message
            $errorMessage = 'Failed to create UPS shipment';
            if (isset($decoded['response']['errors'][0]['message'])) {
                $errorMessage = $decoded['response']['errors'][0]['message'];
            } elseif (isset($decoded['ShipmentResponse']['Response']['Error'][0]['ErrorDescription'])) {
                $errorMessage = $decoded['ShipmentResponse']['Response']['Error'][0]['ErrorDescription'];
            } elseif (isset($decoded['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['ErrorDescription'])) {
                $errorMessage = $decoded['Fault']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['ErrorDescription'];
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'rawResponse' => $decoded,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Map delivery_destination text to ISO country code.
     */
    private function getCountryCodeFromDestination($dest)
    {
        $map = [
            'US- United State of America' => 'UK',
            'India' => 'IN',
            'UK - United Kingdom' => 'GB',
            'China' => 'CN',
            'Russia' => 'RU',
            'Srilanka' => 'LK',
        ];
        return $map[$dest] ?? 'US';
    }

    /**
     * Map courier service method name to UPS service description.
     */
    private function getServiceDescriptionFromMethod($method)
    {
        $methodUpper = strtoupper($method);
        $descMap = [
            'UNITED MY DELIVERY' => 'Ground',
            'UNITED AIR PREMIUM' => 'Next Day Air',
            'UNITED GRD PREMIUM' => '2nd Day Air',
            'UNITED AIR EXPRESS' => 'Worldwide Express',
            'UNITED PRIOR POST' => 'Standard',
            'UNITED ECO POST' => 'Saver',
            'UNITED MY PICKUP' => 'Ground',
            'DDP AIREXPRESS' => 'Worldwide Express',
            'DDU AIREXPRESS' => 'Worldwide Express',
        ];

        foreach ($descMap as $key => $desc) {
            if (str_contains($methodUpper, $key)) {
                return $desc;
            }
        }

        return 'Ground';
    }

    /**
     * Show all shipments for the logged-in customer.
     */
    public function viewAllShipments()
    {
        // Check if customer is logged in
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customerId = auth()->guard('customer')->id();

        // Get all shipper IDs for this customer
        $shipperIds = ShipperInfo::where('customer_id', $customerId)->pluck('id');

        // Get all invoices for those shippers, with shipper info
        $invoices = ShipmentInvoice::whereIn('shipper_id', $shipperIds)
            ->with(['invoiceItems', 'shipperInfo.shipmentTracking', 'shipperInfo.consigneeInfo', 'shipperInfo.packageDimensions'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Prepare shipment details data for the detail modal (JS-friendly format)
        $shipmentDetails = $invoices->mapWithKeys(function($invoice) {
            $shipper = $invoice->shipperInfo;
            $consignee = $shipper ? $shipper->consigneeInfo : null;
            $tracking = $shipper ? $shipper->shipmentTracking : null;
            $packages = $shipper ? $shipper->packageDimensions : collect([]);
            $items = $invoice->invoiceItems;

            // Extract label data from package_results
            // UPS Ship API uses "ShippingLabel" key (not "LabelImage")
            // Structure: ShippingLabel.ImageFormat.Code + ShippingLabel.GraphicImage
            $hasLabel = false;
            $labelFormat = null;
            $graphicImage = null;
            if ($tracking && $tracking->package_results) {
                $pkgResults = $tracking->package_results;
                $firstPkg = is_array($pkgResults) && isset($pkgResults[0]) ? $pkgResults[0] : $pkgResults;
                if (isset($firstPkg['ShippingLabel'])) {
                    $hasLabel = true;
                    $labelFormat = $firstPkg['ShippingLabel']['ImageFormat']['Code'] ?? 'GIF';
                    $graphicImage = $firstPkg['ShippingLabel']['GraphicImage'] ?? null;
                } elseif (isset($firstPkg['LabelImage'])) {
                    // Fallback for older/different UPS response format
                    $hasLabel = true;
                    $labelFormat = $firstPkg['LabelImage']['LabelImageFormat']['Code'] ?? 'PDF';
                    $graphicImage = $firstPkg['LabelImage']['GraphicImage'] ?? null;
                }
            }
            return [
                $invoice->id => [
                    'shipper_id' => $shipper ? $shipper->id : null,
                    'awb_number' => $shipper ? $shipper->awb_number : null,
                    'tracking_number' => $tracking ? ($tracking->shipment_identification_number ?? null) : null,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date ? $invoice->invoice_date->format('d-m-Y') : null,
                    'invoice_amount' => number_format($invoice->total_amount, 2),
                    'invoice_currency' => $invoice->invoice_currency,
                    'incoterms' => $invoice->incoterms,
                    'reference_number' => $invoice->reference_number,
                    'status' => $shipper && $shipper->status ? $shipper->status : ($invoice->status === 'cancelled' ? 'cancelled' : 'draft'),
                    'ship_from' => $shipper ? trim(($shipper->city ?? '') . ', ' . ($shipper->state ?? '') . ' - ' . ($shipper->pincode ?? '') . ', India') : null,
                    'ship_to' => $consignee ? trim(($consignee->city ?? '') . ', ' . ($consignee->state ?? '') . ' - ' . ($consignee->zip_code ?? '') . ', ' . ($consignee->delivery_destination ?? '')) : null,
                    'shipper' => $shipper ? [
                        'company' => $shipper->company_name,
                        'contact' => $shipper->contact_person,
                        'phone' => $shipper->phone_number,
                        'email' => $shipper->email,
                        'address' => trim(($shipper->address_line1 ?? '') . ' ' . ($shipper->address_line2 ?? '') . ' ' . ($shipper->address_line3 ?? '')),
                        'city_state_pin' => trim(($shipper->city ?? '') . ', ' . ($shipper->state ?? '') . ' - ' . ($shipper->pincode ?? '')),
                    ] : null,
                    'consignee' => $consignee ? [
                        'name' => $consignee->consignee_name,
                        'contact' => $consignee->contact_person,
                        'phone' => $consignee->phone_number,
                        'email' => $consignee->email,
                        'address' => trim(($consignee->address_line1 ?? '') . ' ' . ($consignee->address_line2 ?? '') . ' ' . ($consignee->address_line3 ?? '')),
                        'city_state_zip' => trim(($consignee->city ?? '') . ', ' . ($consignee->state ?? '') . ' - ' . ($consignee->zip_code ?? '')),
                    ] : null,
                    'destination' => $consignee ? $consignee->delivery_destination : null,
                    'origin_type' => $consignee ? $consignee->origin_type : null,
                    'shipping_method' => $shipper ? $shipper->shipping_method : null,
                    'packages' => $packages->map(function($pkg, $idx) {
                        return [
                            'index' => $idx + 1,
                            'weight' => $pkg->actual_weight_kg,
                            'length' => $pkg->length_cm,
                            'width' => $pkg->width_cm,
                            'height' => $pkg->height_cm,
                            'volumetric' => $pkg->volumetric_weight,
                            'chargeable' => $pkg->chargeable_weight,
                        ];
                    })->values()->toArray(),
                    'items' => $items->map(function($item) {
                        $qty = $item->qty ?? 0;
                        $rate = $item->unit_rate ?? 0;
                        $igstPct = $item->igst_percentage ?? 0;
                        $igstAmt = $item->igst_amount ?? 0;
                        $baseAmount = $qty * $rate;
                        // Use stored amount if available, otherwise calculate
                        $amount = $item->amount ?? ($baseAmount + $igstAmt);
                        return [
                            'box_no' => $item->box_no,
                            'description' => $item->description,
                            'hs_code' => $item->hs_code,
                            'hts_code' => $item->hts_code,
                            'unit_type' => $item->unit_type,
                            'qty' => $qty,
                            'unit_rate' => $rate,
                            'igst_percentage' => $igstPct,
                            'igst_amount' => number_format($igstAmt, 2),
                            'amount' => number_format($amount, 2),
                        ];
                    })->values()->toArray(),
                    'items_total' => number_format($items->sum(function($item) {
                        $qty = $item->qty ?? 0;
                        $rate = $item->unit_rate ?? 0;
                        $igstAmt = $item->igst_amount ?? 0;
                        $amount = $item->amount ?? ($qty * $rate + $igstAmt);
                        return $amount;
                    }), 2),
                    'charges' => $tracking ? [
                        'transport' => $tracking->transportation_charges_currency . ' ' . ($tracking->transportation_charges_amount ?? '-'),
                        'service_options' => $tracking->service_options_charges_currency . ' ' . ($tracking->service_options_charges_amount ?? '-'),
                        'total' => $tracking->total_charges_currency . ' ' . ($tracking->total_charges_amount ?? '-'),
                        'billing_weight' => ($tracking->billing_weight_uom ?? '') . ' ' . ($tracking->billing_weight ?? '-'),
                    ] : null,
                    'has_label' => $hasLabel,
                    'label_format' => $labelFormat,
                    'graphic_image' => $graphicImage,
                ]
            ];
        });

        return view('customer.view-all-shipments', compact('invoices', 'shipmentDetails'));
    }

    /**
     * Pay for a shipment - deduct from wallet and set status to ready.
     */
    public function payNow(Request $request)
    {
        try {
            // Check if customer is logged in
            if (!auth()->guard('customer')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            $customerId = auth()->guard('customer')->id();

            // Validate request
            $validated = $request->validate([
                'invoice_id' => 'required|integer',
                'shipper_id' => 'required|integer',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $invoiceId = $validated['invoice_id'];
            $shipperId = $validated['shipper_id'];
            $amount = $validated['amount'];

            // Find the shipper info and verify it belongs to this customer
            $shipper = ShipperInfo::where('id', $shipperId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$shipper) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found or does not belong to you.'
                ], 403);
            }

            // Check if already paid (status is ready)
            if ($shipper->status === 'ready') {
                return response()->json([
                    'success' => false,
                    'message' => 'This shipment has already been paid for.'
                ]);
            }

            // Find the customer's wallet
            $wallet = Wallet::where('customer_id', $customerId)->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found. Please contact support.'
                ]);
            }

            // Check if wallet balance is sufficient
            if ($wallet->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient wallet balance. Your current balance is ₹' . number_format($wallet->balance, 2)
                ]);
            }

            // Deduct amount from wallet and update shipper status in a transaction
            DB::transaction(function () use ($wallet, $amount, $shipper) {
                $wallet->decrement('balance', $amount);
                $shipper->status = 'ready';
                $shipper->save();
            });

            // Create tracking record for payment confirmed (ready status)
            $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();
            Tracking::create([
                'awb_number' => $shipper->awb_number,
                'shipper_id' => $shipper->id,
                'shipping_id' => $createShipment ? $createShipment->id : null,
                'uwc_id' => $shipper->awb_number,
                'title' => Tracking::getTitleForStatus('ready'),
                'status' => 'ready',
            ]);

            // Refresh wallet to get new balance
            $wallet->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Payment successful! Shipment status updated to Ready.',
                'new_balance' => (float) $wallet->balance
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a shipment (set status to cancelled) and refund wallet if paid.
     */
    public function cancelShipment($id)
    {
        try {
            // Check if customer is logged in
            if (!auth()->guard('customer')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            $customerId = auth()->guard('customer')->id();

            // Find the invoice and verify it belongs to this customer
            $invoice = ShipmentInvoice::findOrFail($id);

            // Verify the invoice's shipper belongs to this customer
            $shipper = ShipperInfo::where('id', $invoice->shipper_id)
                ->where('customer_id', $customerId)
                ->first();

            if (!$shipper) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found or does not belong to you.'
                ], 403);
            }

            // Check if already cancelled
            if ($invoice->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'This shipment is already cancelled.'
                ], 400);
            }

            // Determine if the shipment was paid (shipper status is ready/packed/manifested)
            $wasPaid = in_array($shipper->status, ['ready', 'packed', 'manifested']);
            $refundAmount = 0;

            // Update status to cancelled and refund wallet if paid
            DB::transaction(function () use ($invoice, $shipper, $wasPaid, $customerId, &$refundAmount) {
                $invoice->update(['status' => 'cancelled']);
                $shipper->update(['status' => 'cancelled']);

                // Create tracking record for cancelled status
                $createShipment = CreateShipment::where('shipper_id', $shipper->id)->first();
                Tracking::create([
                    'awb_number' => $shipper->awb_number,
                    'shipper_id' => $shipper->id,
                    'shipping_id' => $createShipment ? $createShipment->id : null,
                    'uwc_id' => $shipper->awb_number,
                    'title' => Tracking::getTitleForStatus('cancelled'),
                    'status' => 'cancelled',
                ]);

                if ($wasPaid) {
                    // Calculate the amount that was paid (total from invoice items)
                    $refundAmount = $invoice->total_amount;

                    if ($refundAmount > 0) {
                        // Find the customer's wallet and refund the amount
                        $wallet = Wallet::where('customer_id', $customerId)->first();
                        if ($wallet) {
                            $wallet->increment('balance', $refundAmount);
                        }
                    }
                }
            });

            // Refresh wallet to get new balance
            $wallet = Wallet::where('customer_id', $customerId)->first();
            $newBalance = $wallet ? (float) $wallet->balance : 0;

            $message = 'Shipment cancelled successfully.';
            if ($refundAmount > 0) {
                $message = 'Shipment cancelled successfully. ₹' . number_format($refundAmount, 2) . ' has been refunded to your wallet.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'refund_amount' => $refundAmount,
                'new_balance' => $newBalance,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling shipment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a shipment as packed (called when Print Label is clicked from Ready status).
     */
    public function markPacked(Request $request)
    {
        try {
            if (!auth()->guard('customer')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $customerId = auth()->guard('customer')->id();
            $shipperId = $request->input('shipper_id');

            $shipper = ShipperInfo::where('id', $shipperId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$shipper) {
                return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
            }

            if ($shipper->status !== 'ready') {
                return response()->json(['success' => false, 'message' => 'Shipment is not in Ready status.'], 400);
            }

            $shipper->status = 'packed';
            $shipper->save();

            // Create tracking record for packed status
            $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();
            Tracking::create([
                'awb_number' => $shipper->awb_number,
                'shipper_id' => $shipper->id,
                'shipping_id' => $createShipment ? $createShipment->id : null,
                'uwc_id' => $shipper->awb_number,
                'title' => Tracking::getTitleForStatus('packed'),
                'status' => 'packed',
            ]);

            return response()->json(['success' => true, 'message' => 'Status updated to Packed.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Manifest a single shipment - check network (UPS vs Ship Global) and call appropriate API.
     * Only works for shipments in 'packed' status.
     */
    public function manifestShipment(Request $request)
    {
        try {
            if (!auth()->guard('customer')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $customerId = auth()->guard('customer')->id();
            $shipperId = $request->input('shipper_id');

            $shipper = ShipperInfo::where('id', $shipperId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$shipper) {
                return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
            }

            if ($shipper->status !== 'packed') {
                return response()->json(['success' => false, 'message' => 'Shipment must be in Packed status to manifest.'], 400);
            }

            // Determine the network from the shipping method's CourierService
            $shippingMethod = $this->resolveShippingMethod($shipper);
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
            $network = $courierService ? strtolower(trim($courierService->network)) : 'ups';

            \Log::info('manifestShipment: Shipper #' . $shipperId . ' → shipping_method="' . $shippingMethod . '" → network="' . $network . '"');

            // Route to appropriate API based on network
            // Priority 1: PostShipping (DPD/UK) for UNITED AIR PREMIUM DDP / UNITED PRIOR POST DDP
            if ($this->isPostShippingMethod($shippingMethod)) {
                // Call PostShipping API
                $postShippingResult = $this->callPostShippingApiFromDb($shipper);
                if (!$postShippingResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PostShipping API Failed: ' . ($postShippingResult['message'] ?? 'Unknown error'),
                        'postshipping_response' => $postShippingResult['data'] ?? null,
                    ], 500);
                }

                // PostShipping succeeded - store tracking data
                $apiResponse = $postShippingResult['data'] ?? [];
                $trackingNumber = $this->extractPostShippingTrackingNumber($apiResponse);
                $labelUrl = $this->extractPostShippingLabelUrl($apiResponse);

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $customerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => '1',
                            'response_status_description' => 'PostShipping shipment created',
                            'shipment_identification_number' => $trackingNumber,
                            'total_charges_currency' => 'INR',
                            'total_charges_amount' => null,
                            'billing_weight_uom' => 'KGS',
                            'billing_weight' => null,
                            'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                            'raw_response' => $apiResponse,
                            'status' => 'created',
                        ]
                    );

                    // Update shipper status to manifested
                    $shipper->status = 'manifested';
                    $shipper->save();

                    // Create tracking record for manifested status
                    Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id' => $shipper->awb_number,
                        'title' => Tracking::getTitleForStatus('manifested'),
                        'status' => 'manifested',
                    ]);

                    \Log::info('Shipment manifested via PostShipping: ' . ($trackingNumber ?? 'N/A'));
                } catch (\Exception $e) {
                    \Log::error('Failed to store shipment tracking for PostShipping manifest: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: ' . $e->getMessage()], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via PostShipping!',
                    'tracking_number' => $trackingNumber,
                    'label_url' => $labelUrl,
                    'shipper_id' => $shipperId,
                    'network' => 'PostShipping',
                    'postshipping_response' => $apiResponse,
                ]);
            } elseif ($this->isFlyingTigersMethod($shippingMethod)) {
                // Call Flying Tigers API (UNITED ECO POST)
                $flyingTigersResult = $this->callFlyingTigersApiFromDb($shipper);
                if (!$flyingTigersResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Flying Tigers API Failed: ' . ($flyingTigersResult['message'] ?? 'Unknown error'),
                        'flyingtigers_response' => $flyingTigersResult['data'] ?? null,
                    ], 500);
                }

                // Flying Tigers succeeded - store tracking data
                $apiResponse = $flyingTigersResult['data'] ?? [];
                $trackingNumber = $this->extractFlyingTigersTrackingNumber($apiResponse);
                $labelUrl = $this->extractFlyingTigersLabelUrl($apiResponse);

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $customerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => '1',
                            'response_status_description' => 'Flying Tigers shipment created',
                            'shipment_identification_number' => $trackingNumber,
                            'total_charges_currency' => 'INR',
                            'total_charges_amount' => null,
                            'billing_weight_uom' => 'KGS',
                            'billing_weight' => null,
                            'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                            'raw_response' => $apiResponse,
                            'status' => 'created',
                        ]
                    );

                    // Update shipper status to manifested
                    $shipper->status = 'manifested';
                    $shipper->save();

                    // Create tracking record for manifested status
                    Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id' => $shipper->awb_number,
                        'title' => Tracking::getTitleForStatus('manifested'),
                        'status' => 'manifested',
                    ]);

                    \Log::info('Shipment manifested via Flying Tigers: ' . ($trackingNumber ?? 'N/A'));
                } catch (\Exception $e) {
                    \Log::error('Failed to store shipment tracking for Flying Tigers manifest: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: ' . $e->getMessage()], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via Flying Tigers!',
                    'tracking_number' => $trackingNumber,
                    'label_url' => $labelUrl,
                    'shipper_id' => $shipperId,
                    'network' => 'Flying Tigers',
                    'flyingtigers_response' => $apiResponse,
                ]);
            } elseif ($network === 'ship global' || $network === 'shipglobal') {
                // Call Ship Global API
                $shipGlobalResult = $this->callShipGlobalApiFromDb($shipper);
                if (!$shipGlobalResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ship Global API Failed: ' . ($shipGlobalResult['message'] ?? 'Unknown error'),
                        'ship_global_response' => $shipGlobalResult['data'] ?? null,
                    ], 500);
                }

                // Ship Global succeeded - store tracking data
                $apiResponse = $shipGlobalResult['data'] ?? [];
                $trackingNumber = null;
                // Ship Global returns tracking/reference number in various possible formats
                // Priority: waybill_number > tracking_number > awb_number > order_number
                if (isset($apiResponse['data']) && isset($apiResponse['data']['waybill_number']) && !empty($apiResponse['data']['waybill_number'])) {
                    $trackingNumber = $apiResponse['data']['waybill_number'];
                } elseif (isset($apiResponse['waybill_number']) && !empty($apiResponse['waybill_number'])) {
                    $trackingNumber = $apiResponse['waybill_number'];
                } elseif (isset($apiResponse['tracking_number'])) {
                    $trackingNumber = $apiResponse['tracking_number'];
                } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['tracking_number'])) {
                    $trackingNumber = $apiResponse['data']['tracking_number'];
                } elseif (isset($apiResponse['awb_number'])) {
                    $trackingNumber = $apiResponse['awb_number'];
                } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['awb_number'])) {
                    $trackingNumber = $apiResponse['data']['awb_number'];
                } elseif (isset($apiResponse['waybill'])) {
                    $trackingNumber = $apiResponse['waybill'];
                } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['waybill'])) {
                    $trackingNumber = $apiResponse['data']['waybill'];
                } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['order_number'])) {
                    // If no waybill/tracking yet, use order_number as reference (label: "manual" case)
                    $trackingNumber = $apiResponse['data']['order_number'];
                } elseif (isset($apiResponse['order_number'])) {
                    $trackingNumber = $apiResponse['order_number'];
                }

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $customerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => '1',
                            'response_status_description' => 'Ship Global order created',
                            'shipment_identification_number' => $trackingNumber,
                            'total_charges_currency' => 'INR',
                            'total_charges_amount' => null,
                            'billing_weight_uom' => 'KGS',
                            'billing_weight' => null,
                            'package_results' => null,
                            'raw_response' => $apiResponse,
                            'status' => 'created',
                        ]
                    );

                    // Update shipper status to manifested
                    $shipper->status = 'manifested';
                    $shipper->save();

                    // Create tracking record for manifested status
                    Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id' => $shipper->awb_number,
                        'title' => Tracking::getTitleForStatus('manifested'),
                        'status' => 'manifested',
                    ]);

                    \Log::info('Shipment manifested via Ship Global: ' . ($trackingNumber ?? 'N/A'));
                } catch (\Exception $e) {
                    \Log::error('Failed to store shipment tracking for Ship Global manifest: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: ' . $e->getMessage()], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via Ship Global!',
                    'tracking_number' => $trackingNumber,
                    'shipper_id' => $shipperId,
                    'network' => 'Ship Global',
                    'ship_global_response' => $apiResponse,
                ]);

            } else {
                // Default: Call UPS Ship API
                $payloadResult = $this->buildUpsShipPayloadFromDb($shipper);
                if (!$payloadResult['success']) {
                    return response()->json(['success' => false, 'message' => $payloadResult['message']], 400);
                }
                $upsPayload = $payloadResult['payload'];

                $upsResult = $this->callUpsShipApiInternal($upsPayload);

                if (!$upsResult['success']) {
                    $errorMessage = $upsResult['message'] ?? 'Unknown UPS error';
                    return response()->json([
                        'success' => false,
                        'message' => 'UPS Shipment Failed: ' . $errorMessage,
                        'rawResponse' => $upsResult['rawResponse'] ?? null
                    ], 500);
                }

                // UPS succeeded - store tracking data
                $shipmentResponse = $upsResult['shipmentResponse'];
                $trackingNumber = $shipmentResponse['ShipmentResults']['PackageResults']['TrackingNumber']
                    ?? $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber']
                    ?? null;

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $customerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => $shipmentResponse['Response']['ResponseStatus']['Code'] ?? null,
                            'response_status_description' => $shipmentResponse['Response']['ResponseStatus']['Description'] ?? null,
                            'transaction_identifier' => $shipmentResponse['Response']['TransactionReference']['TransactionIdentifier'] ?? null,
                            'customer_context' => $shipmentResponse['Response']['TransactionReference']['CustomerContext'] ?? null,
                            'shipment_identification_number' => $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber'] ?? null,
                            'transportation_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['CurrencyCode'] ?? null,
                            'transportation_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['MonetaryValue'] ?? null,
                            'service_options_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['CurrencyCode'] ?? null,
                            'service_options_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['MonetaryValue'] ?? null,
                            'total_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['CurrencyCode'] ?? null,
                            'total_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue'] ?? null,
                            'billing_weight_uom' => $shipmentResponse['ShipmentResults']['BillingWeight']['UnitOfMeasurement']['Code'] ?? null,
                            'billing_weight' => $shipmentResponse['ShipmentResults']['BillingWeight']['Weight'] ?? null,
                            'package_results' => $shipmentResponse['ShipmentResults']['PackageResults'] ?? null,
                            'raw_response' => $shipmentResponse,
                            'status' => 'created',
                        ]
                    );

                    // Update shipper status to manifested
                    $shipper->status = 'manifested';
                    $shipper->save();

                    // Create tracking record for manifested status
                    Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id' => $shipper->awb_number,
                        'title' => Tracking::getTitleForStatus('manifested'),
                        'status' => 'manifested',
                    ]);

                    \Log::info('Shipment manifested via UPS: ' . ($shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber'] ?? 'N/A'));
                } catch (\Exception $e) {
                    \Log::error('Failed to store shipment tracking for manifest: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: ' . $e->getMessage()], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via UPS!',
                    'tracking_number' => $trackingNumber,
                    'shipper_id' => $shipperId,
                    'network' => 'UPS',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk manifest multiple shipments at once.
     */
    public function bulkManifestShipments(Request $request)
    {
        try {
            if (!auth()->guard('customer')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $customerId = auth()->guard('customer')->id();
            $shipperIds = $request->input('shipper_ids', []);

            if (empty($shipperIds) || !is_array($shipperIds)) {
                return response()->json(['success' => false, 'message' => 'No shipments selected.'], 400);
            }

            $results = [
                'success' => [],
                'failed' => [],
                'total' => count($shipperIds),
            ];

            foreach ($shipperIds as $shipperId) {
                try {
                    $shipper = ShipperInfo::where('id', $shipperId)
                        ->where('customer_id', $customerId)
                        ->first();

                    if (!$shipper) {
                        $results['failed'][] = ['shipper_id' => $shipperId, 'message' => 'Shipment not found'];
                        continue;
                    }

                    if ($shipper->status !== 'packed') {
                        $results['failed'][] = ['shipper_id' => $shipperId, 'message' => 'Not in Packed status'];
                        continue;
                    }

                    // Determine the network from the shipping method's CourierService
                    $shippingMethod = $this->resolveShippingMethod($shipper);
                    $courierService = $this->findCourierService($shippingMethod, $shipper->id);
                    $network = $courierService ? strtolower(trim($courierService->network)) : 'ups';

                    \Log::info('bulkManifest: Shipper #' . $shipperId . ' → network="' . $network . '"');

                    // Priority 1: PostShipping (DPD/UK) for UNITED AIR PREMIUM DDP / UNITED PRIOR POST DDP
                    if ($this->isPostShippingMethod($shippingMethod)) {
                        // Call PostShipping API
                        $postShippingResult = $this->callPostShippingApiFromDb($shipper);

                        if (!$postShippingResult['success']) {
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'PostShipping API error: ' . ($postShippingResult['message'] ?? 'Unknown'),
                            ];
                            continue;
                        }

                        $apiResponse = $postShippingResult['data'] ?? [];
                        $trackingNumber = $this->extractPostShippingTrackingNumber($apiResponse);
                        $labelUrl = $this->extractPostShippingLabelUrl($apiResponse);

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $customerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => '1',
                                'response_status_description' => 'PostShipping shipment created',
                                'shipment_identification_number' => $trackingNumber,
                                'total_charges_currency' => 'INR',
                                'total_charges_amount' => null,
                                'billing_weight_uom' => 'KGS',
                                'billing_weight' => null,
                                'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                                'raw_response' => $apiResponse,
                                'status' => 'created',
                            ]
                        );

                        $shipper->status = 'manifested';
                        $shipper->save();

                        // Create tracking record for manifested status
                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();
                        Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                            'status' => 'manifested',
                        ]);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'label_url' => $labelUrl,
                            'network' => 'PostShipping',
                        ];

                        \Log::info('Bulk manifest: shipment ' . $shipperId . ' manifested via PostShipping.');

                    } elseif ($this->isFlyingTigersMethod($shippingMethod)) {
                        // Call Flying Tigers API (UNITED ECO POST)
                        $flyingTigersResult = $this->callFlyingTigersApiFromDb($shipper);

                        if (!$flyingTigersResult['success']) {
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'Flying Tigers API error: ' . ($flyingTigersResult['message'] ?? 'Unknown'),
                            ];
                            continue;
                        }

                        $apiResponse = $flyingTigersResult['data'] ?? [];
                        $trackingNumber = $this->extractFlyingTigersTrackingNumber($apiResponse);
                        $labelUrl = $this->extractFlyingTigersLabelUrl($apiResponse);

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $customerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => '1',
                                'response_status_description' => 'Flying Tigers shipment created',
                                'shipment_identification_number' => $trackingNumber,
                                'total_charges_currency' => 'INR',
                                'total_charges_amount' => null,
                                'billing_weight_uom' => 'KGS',
                                'billing_weight' => null,
                                'package_results' => $labelUrl ? ['LabelURL' => $labelUrl] : null,
                                'raw_response' => $apiResponse,
                                'status' => 'created',
                            ]
                        );

                        $shipper->status = 'manifested';
                        $shipper->save();

                        // Create tracking record for manifested status
                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();
                        Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                            'status' => 'manifested',
                        ]);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'label_url' => $labelUrl,
                            'network' => 'Flying Tigers',
                        ];

                        \Log::info('Bulk manifest: shipment ' . $shipperId . ' manifested via Flying Tigers.');

                    } elseif ($network === 'ship global' || $network === 'shipglobal') {
                        // Call Ship Global API
                        $shipGlobalResult = $this->callShipGlobalApiFromDb($shipper);

                        if (!$shipGlobalResult['success']) {
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'Ship Global API error: ' . ($shipGlobalResult['message'] ?? 'Unknown'),
                            ];
                            continue;
                        }

                        $apiResponse = $shipGlobalResult['data'] ?? [];
                        $trackingNumber = null;
                        // Ship Global returns tracking/reference number in various possible formats
                        // Priority: waybill_number > tracking_number > awb_number > order_number
                        if (isset($apiResponse['data']) && isset($apiResponse['data']['waybill_number']) && !empty($apiResponse['data']['waybill_number'])) {
                            $trackingNumber = $apiResponse['data']['waybill_number'];
                        } elseif (isset($apiResponse['waybill_number']) && !empty($apiResponse['waybill_number'])) {
                            $trackingNumber = $apiResponse['waybill_number'];
                        } elseif (isset($apiResponse['tracking_number'])) {
                            $trackingNumber = $apiResponse['tracking_number'];
                        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['tracking_number'])) {
                            $trackingNumber = $apiResponse['data']['tracking_number'];
                        } elseif (isset($apiResponse['awb_number'])) {
                            $trackingNumber = $apiResponse['awb_number'];
                        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['awb_number'])) {
                            $trackingNumber = $apiResponse['data']['awb_number'];
                        } elseif (isset($apiResponse['waybill'])) {
                            $trackingNumber = $apiResponse['waybill'];
                        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['waybill'])) {
                            $trackingNumber = $apiResponse['data']['waybill'];
                        } elseif (isset($apiResponse['data']) && isset($apiResponse['data']['order_number'])) {
                            // If no waybill/tracking yet, use order_number as reference (label: "manual" case)
                            $trackingNumber = $apiResponse['data']['order_number'];
                        } elseif (isset($apiResponse['order_number'])) {
                            $trackingNumber = $apiResponse['order_number'];
                        }

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $customerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => '1',
                                'response_status_description' => 'Ship Global order created',
                                'shipment_identification_number' => $trackingNumber,
                                'total_charges_currency' => 'INR',
                                'total_charges_amount' => null,
                                'billing_weight_uom' => 'KGS',
                                'billing_weight' => null,
                                'package_results' => null,
                                'raw_response' => $apiResponse,
                                'status' => 'created',
                            ]
                        );

                        $shipper->status = 'manifested';
                        $shipper->save();

                        // Create tracking record for manifested status
                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();
                        Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                            'status' => 'manifested',
                        ]);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'network' => 'Ship Global',
                        ];

                        \Log::info('Bulk manifest: shipment ' . $shipperId . ' manifested via Ship Global.');

                    } else {
                        // Default: Call UPS Ship API
                        $payloadResult = $this->buildUpsShipPayloadFromDb($shipper);
                        if (!$payloadResult['success']) {
                            $results['failed'][] = ['shipper_id' => $shipperId, 'message' => $payloadResult['message']];
                            continue;
                        }
                        $upsPayload = $payloadResult['payload'];

                        $upsResult = $this->callUpsShipApiInternal($upsPayload);

                        if (!$upsResult['success']) {
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'UPS API error: ' . ($upsResult['message'] ?? 'Unknown'),
                            ];
                            continue;
                        }

                        $shipmentResponse = $upsResult['shipmentResponse'];
                        $trackingNumber = $shipmentResponse['ShipmentResults']['PackageResults']['TrackingNumber']
                            ?? $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber']
                            ?? null;

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $customerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => $shipmentResponse['Response']['ResponseStatus']['Code'] ?? null,
                                'response_status_description' => $shipmentResponse['Response']['ResponseStatus']['Description'] ?? null,
                                'transaction_identifier' => $shipmentResponse['Response']['TransactionReference']['TransactionIdentifier'] ?? null,
                                'customer_context' => $shipmentResponse['Response']['TransactionReference']['CustomerContext'] ?? null,
                                'shipment_identification_number' => $shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber'] ?? null,
                                'transportation_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['CurrencyCode'] ?? null,
                                'transportation_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TransportationCharges']['MonetaryValue'] ?? null,
                                'service_options_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['CurrencyCode'] ?? null,
                                'service_options_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['ServiceOptionsCharges']['MonetaryValue'] ?? null,
                                'total_charges_currency' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['CurrencyCode'] ?? null,
                                'total_charges_amount' => $shipmentResponse['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue'] ?? null,
                                'billing_weight_uom' => $shipmentResponse['ShipmentResults']['BillingWeight']['UnitOfMeasurement']['Code'] ?? null,
                                'billing_weight' => $shipmentResponse['ShipmentResults']['BillingWeight']['Weight'] ?? null,
                                'package_results' => $shipmentResponse['ShipmentResults']['PackageResults'] ?? null,
                                'raw_response' => $shipmentResponse,
                                'status' => 'created',
                            ]
                        );

                        $shipper->status = 'manifested';
                        $shipper->save();

                        // Create tracking record for manifested status
                        Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                            'title' => Tracking::getTitleForStatus('manifested'),
                            'status' => 'manifested',
                        ]);

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'network' => 'UPS',
                        ];

                        \Log::info('Bulk manifest: shipment ' . $shipperId . ' manifested via UPS.');
                    }

                } catch (\Exception $e) {
                    $results['failed'][] = ['shipper_id' => $shipperId, 'message' => $e->getMessage()];
                    \Log::error('Bulk manifest error for shipper ' . $shipperId . ': ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk manifest completed. ' . count($results['success']) . ' succeeded, ' . count($results['failed']) . ' failed out of ' . $results['total'] . ' shipments.',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Build UPS Ship API payload from database records (for manifest).
     * Reads ShipperInfo, ConsigneeInfo, PackageDimension from DB instead of form data.
     */
    private function buildUpsShipPayloadFromDb($shipper)
    {
        // Get consignee info
        $consignee = $shipper->consigneeInfo;
        if (!$consignee) {
            \Log::warning('buildUpsShipPayloadFromDb: No consignee found for shipper #' . $shipper->id);
            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        // Get shipping method and lookup courier service
        // Try multiple sources because older shipments may have null in shipper_info.shipping_method.
        $shippingMethod = $shipper->shipping_method;
        $fallbackSource = null;

        if (!$shippingMethod) {
            // Fallback 1: Check create_shipment table (same column populated at storeShipment line 729)
            $createShipmentMethod = CreateShipment::where('shipper_id', $shipper->id)->value('shipping_method');
            if ($createShipmentMethod) {
                $shippingMethod = $createShipmentMethod;
                $fallbackSource = 'create_shipment';
                \Log::info('buildUpsShipPayloadFromDb: Resolved shipping_method from create_shipment for shipper #' . $shipper->id . ' → "' . $shippingMethod . '"');
            }
        }

        if (!$shippingMethod) {
            // Fallback 2: Check package_dimension table (populated from package_shipping_method field)
            $pkgMethod = PackageDimension::where('shipper_id', $shipper->id)
                ->whereNotNull('shipping_method')
                ->value('shipping_method');
            if ($pkgMethod) {
                $shippingMethod = $pkgMethod;
                $fallbackSource = 'package_dimension';
                \Log::info('buildUpsShipPayloadFromDb: Resolved shipping_method from package_dimension for shipper #' . $shipper->id . ' → "' . $shippingMethod . '"');
            }
        }

        if (!$shippingMethod) {
            // Last resort: use the first available CourierService as default.
            // The <select name="shipping_method"> in create-shipment.blade.php line 235
            // is permanently hidden (display:none), so older shipments have no
            // shipping method stored anywhere. We default to the first service.
            $defaultService = CourierService::orderBy('id', 'asc')->first();
            if ($defaultService) {
                $shippingMethod = $defaultService->method;
                $fallbackSource = 'default_first_service';
                \Log::warning('buildUpsShipPayloadFromDb: ALL sources null for shipper #' . $shipper->id . ' — defaulting to first CourierService: "' . $shippingMethod . '"');
            }
        }

        if (!$shippingMethod) {
            \Log::warning('buildUpsShipPayloadFromDb: shipping_method is null/empty for shipper #' . $shipper->id . ' (checked shipper_info, create_shipment, package_dimension, default)');
            return ['success' => false, 'message' => 'Shipping method is not set for this shipment. Please edit the shipment and select a shipping method.'];
        }

        // If we resolved from a fallback, persist it back to shipper_info so future calls work directly.
        if ($fallbackSource && !$shipper->shipping_method) {
            $shipper->shipping_method = $shippingMethod;
            $shipper->save();
            \Log::info('buildUpsShipPayloadFromDb: Persisted shipping_method to shipper_info #' . $shipper->id . ' → "' . $shippingMethod . '"');
        }

        // Multi-tier CourierService lookup
        $service = $this->findCourierService($shippingMethod, $shipper->id);

        if (!$service) {
            return ['success' => false, 'message' => 'No matching courier service found for shipping method: "' . $shippingMethod . '".'];
        }

        // Build a $validatedData array from DB records matching the format
        // that buildUpsShipPayload() expects, then delegate to it.
        $packagesData = [];
        foreach ($shipper->packageDimensions as $pkg) {
            $packagesData[] = [
                'actual_weight_kg' => $pkg->actual_weight_kg,
                'length_cm' => $pkg->length_cm,
                'width_cm' => $pkg->width_cm,
                'height_cm' => $pkg->height_cm,
            ];
        }

        $validatedData = [
            'consignee_name' => $consignee->consignee_name,
            'consignee_phone_number' => $consignee->phone_number,
            'consignee_address_line1' => $consignee->address_line1,
            'consignee_address_line2' => $consignee->address_line2,
            'consignee_address_line3' => $consignee->address_line3,
            'consignee_city' => $consignee->city,
            'consignee_state' => $consignee->state ?? '',
            'consignee_zip_code' => $consignee->zip_code,
            'delivery_destination' => $consignee->delivery_destination,
            'packages' => $packagesData,
        ];

        $upsPayload = $this->buildUpsShipPayload($validatedData, $service);
        return ['success' => true, 'payload' => $upsPayload];
    }

    /**
     * Multi-tier CourierService lookup from a shipping method string.
     * Tiers: exact → case-insensitive → str_contains → word-by-word → collapsed-string
     * Logs all available methods on total failure for diagnostics.
     */
    private function findCourierService($shippingMethod, $shipperId)
    {
        // Tier 1: Exact match
        $service = CourierService::where('method', $shippingMethod)->first();
        if ($service) {
            \Log::info('findCourierService: Exact match "' . $shippingMethod . '" for shipper #' . $shipperId);
            return $service;
        }

        // Tier 2: Case-insensitive exact match
        $service = CourierService::whereRaw('LOWER(method) = ?', [strtolower($shippingMethod)])->first();
        if ($service) {
            \Log::info('findCourierService: Case-insensitive match "' . $shippingMethod . '" → "' . $service->method . '" for shipper #' . $shipperId);
            return $service;
        }

        // Tier 3: str_contains partial match (both directions)
        $methodUpper = strtoupper($shippingMethod);
        $allServices = CourierService::all();
        foreach ($allServices as $svc) {
            $svcUpper = strtoupper($svc->method);
            if (str_contains($svcUpper, $methodUpper) || str_contains($methodUpper, $svcUpper)) {
                \Log::info('findCourierService: str_contains match "' . $shippingMethod . '" → "' . $svc->method . '" for shipper #' . $shipperId);
                return $svc;
            }
        }

        // Tier 4: Word-by-word normalized match with abbreviation detection.
        // Handles cases like "United Ground Premium" ↔ "UNITED GRD PREMIUM"
        // by checking if short words (2-3 chars) are abbreviations of longer words.
        $formWords = preg_split('/\s+/', preg_replace('/[^A-Za-z0-9\s]/', '', $methodUpper));
        $formWords = array_values(array_filter($formWords, function($w) { return strlen($w) > 0; }));

        foreach ($allServices as $svc) {
            $svcUpper = strtoupper($svc->method);
            $svcWords = preg_split('/\s+/', preg_replace('/[^A-Za-z0-9\s]/', '', $svcUpper));
            $svcWords = array_values(array_filter($svcWords, function($w) { return strlen($w) > 0; }));

            if (empty($formWords) || empty($svcWords)) continue;

            $matchedCount = 0;
            $unmatchedLongWords = 0;
            foreach ($formWords as $fw) {
                $found = false;
                foreach ($svcWords as $sw) {
                    if ($fw === $sw || str_contains($fw, $sw) || str_contains($sw, $fw)) {
                        $found = true;
                        break;
                    }
                    // Abbreviation check: if one word is short (2-3 chars) and the
                    // other is longer, check if short word's chars appear in order.
                    $shorter = strlen($fw) <= strlen($sw) ? $fw : $sw;
                    $longer  = strlen($fw) >  strlen($sw) ? $fw : $sw;
                    if (strlen($shorter) >= 2 && strlen($shorter) <= 3 && strlen($longer) >= 4) {
                        if ($this->isAbbreviationOf($shorter, $longer)) {
                            $found = true;
                            break;
                        }
                    }
                }
                if ($found) {
                    $matchedCount++;
                } elseif (strlen($fw) >= 4) {
                    $unmatchedLongWords++;
                }
            }

            // Match if all long words matched and at least 50% of total words matched
            $totalWords = count($formWords);
            if ($unmatchedLongWords === 0 && $matchedCount > 0 && ($matchedCount / $totalWords) >= 0.5) {
                \Log::info('findCourierService: Word-by-word match "' . $shippingMethod . '" → "' . $svc->method . '" (matched ' . $matchedCount . '/' . $totalWords . ' words) for shipper #' . $shipperId);
                return $svc;
            }
        }

        // Tier 5: Collapsed-string match (remove all spaces and non-alphanumeric)
        $collapsedForm = preg_replace('/[^A-Za-z0-9]/', '', $methodUpper);
        foreach ($allServices as $svc) {
            $svcUpper = strtoupper($svc->method);
            $collapsedSvc = preg_replace('/[^A-Za-z0-9]/', '', $svcUpper);
            if (str_contains($collapsedForm, $collapsedSvc) || str_contains($collapsedSvc, $collapsedForm)) {
                \Log::info('findCourierService: Collapsed-string match "' . $shippingMethod . '" → "' . $svc->method . '" for shipper #' . $shipperId);
                return $svc;
            }
        }

        // No match found — log all available methods for diagnostics
        $availableMethods = CourierService::pluck('method')->toArray();
        \Log::warning('findCourierService: No match for "' . $shippingMethod . '" (shipper #' . $shipperId . '). Available methods: ' . implode(', ', $availableMethods));

        return null;
    }

    /**
     * Check if a short string (2-3 chars) is an abbreviation of a longer string.
     * Returns true if all characters of $short appear in $long in order.
     * Example: isAbbreviationOf("GRD", "GROUND") → true
     *          isAbbreviationOf("EXP", "EXPRESS") → true
     */
    private function isAbbreviationOf($short, $long)
    {
        $shortLen = strlen($short);
        $longLen = strlen($long);
        if ($shortLen > $longLen) return false;

        $si = 0;
        for ($li = 0; $li < $longLen && $si < $shortLen; $li++) {
            if ($short[$si] === $long[$li]) {
                $si++;
            }
        }
        return $si === $shortLen;
    }

    /**
     * Resolve the shipping method for a shipper from multiple sources.
     * Tries: shipper_info.shipping_method → create_shipment.shipping_method →
     *        package_dimension.shipping_method → first CourierService as default.
     *
     * @param ShipperInfo $shipper
     * @return string
     */
    private function resolveShippingMethod($shipper)
    {
        $shippingMethod = $shipper->shipping_method;

        if (!$shippingMethod) {
            $createShipmentMethod = CreateShipment::where('shipper_id', $shipper->id)->value('shipping_method');
            if ($createShipmentMethod) {
                $shippingMethod = $createShipmentMethod;
            }
        }

        if (!$shippingMethod) {
            $pkgMethod = PackageDimension::where('shipper_id', $shipper->id)
                ->whereNotNull('shipping_method')
                ->value('shipping_method');
            if ($pkgMethod) {
                $shippingMethod = $pkgMethod;
            }
        }

        if (!$shippingMethod) {
            $defaultService = CourierService::orderBy('id', 'asc')->first();
            if ($defaultService) {
                $shippingMethod = $defaultService->method;
            }
        }

        return $shippingMethod ?? '';
    }

    /**
     * Call the Ship Global API to create an order/shipment.
     * Two-step process:
     * 1. Generate Bearer token from customers.php
     * 2. Create order via addOrder.php using the Bearer token
     *
     * @param ShipperInfo $shipper
     * @return array
     */
    private function callShipGlobalApiFromDb($shipper)
    {
        try {
            // Step 1: Generate Bearer token from Ship Global
            $tokenResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://labels.shipglobal.in/api/v1/customers.php', [
                'email' => 'csd@unitedcouriers.biz',
                'password' => 'mSN7KbhrZ0uvb229YWO',
            ]);

            if (!$tokenResponse->successful()) {
                $tokenError = $tokenResponse->json();
                $errorMessage = 'Ship Global token generation failed.';
                if (is_array($tokenError)) {
                    if (isset($tokenError['error'])) {
                        $errorMessage = is_string($tokenError['error']) ? $tokenError['error'] : json_encode($tokenError['error']);
                    } elseif (isset($tokenError['message'])) {
                        $errorMessage = $tokenError['message'];
                    }
                }
                \Log::error('Ship Global token generation failed: ' . $errorMessage . ' | Status: ' . $tokenResponse->status());
                return [
                    'success' => false,
                    'message' => $errorMessage,
                ];
            }

            $tokenData = $tokenResponse->json();
            $bearerToken = null;

            // Extract token from various possible response formats
            if (isset($tokenData['token'])) {
                $bearerToken = $tokenData['token'];
            } elseif (isset($tokenData['data']) && isset($tokenData['data']['token'])) {
                $bearerToken = $tokenData['data']['token'];
            } elseif (isset($tokenData['access_token'])) {
                $bearerToken = $tokenData['access_token'];
            } elseif (isset($tokenData['data']) && isset($tokenData['data']['access_token'])) {
                $bearerToken = $tokenData['data']['access_token'];
            }

            if (!$bearerToken) {
                \Log::error('Ship Global: No token found in response. Response: ' . json_encode($tokenData));
                return [
                    'success' => false,
                    'message' => 'No bearer token found in Ship Global authentication response.',
                ];
            }

            \Log::info('Ship Global token generated successfully.');

            // Step 2: Build the order payload and create the order
            $consignee = $shipper->consigneeInfo;
            $packages = $shipper->packageDimensions;
            $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();

            if (!$consignee) {
                return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
            }

            // Get package dimensions (use first package)
            $firstPackage = $packages->first();
            $packageWeightKg = $firstPackage ? (float)$firstPackage->actual_weight_kg * 1000 : 0.5;
            $packageLength = $firstPackage ? (float)$firstPackage->length_cm : 10;
            $packageBreadth = $firstPackage ? (float)$firstPackage->width_cm : 10;
            $packageHeight = $firstPackage ? (float)$firstPackage->height_cm : 10;

            // Get consignee country code from delivery_destination
            $consigneeCountryCode = $this->getCountryCodeFromDestination($consignee->delivery_destination ?? '');

            // Build shipper address string
            $shipperAddress = trim(
                ($shipper->address_line1 ?? '') . ' ' .
                ($shipper->address_line2 ?? '') . ' ' .
                ($shipper->address_line3 ?? '')
            );

            // Build consignee address string
            $consigneeAddress = trim(
                ($consignee->address_line1 ?? '') . ' ' .
                ($consignee->address_line2 ?? '') . ' ' .
                ($consignee->address_line3 ?? '')
            );

            // Split shipper name into first/last
            // Ship Global requires both firstname and lastname to be non-empty
            $shipperNameParts = preg_split('/\s+/', trim($shipper->contact_person ?? $shipper->company_name ?? 'Shipper'), 2);
            $sellerFirstname = $shipperNameParts[0] ?? 'Shipper';
            $sellerLastname = !empty($shipperNameParts[1]) ? $shipperNameParts[1] : $sellerFirstname;

            // Split consignee name into first/last
            // Ship Global requires both firstname and lastname to be non-empty
            $consigneeNameParts = preg_split('/\s+/', trim($consignee->consignee_name ?? $consignee->contact_person ?? 'Consignee'), 2);
            $consigneeFirstname = $consigneeNameParts[0] ?? 'Consignee';
            $consigneeLastname = !empty($consigneeNameParts[1]) ? $consigneeNameParts[1] : $consigneeFirstname;

            // Get invoice details
            $invoiceNo = $invoice ? ($invoice->invoice_number ?? '') : '';
            $invoiceDate = $invoice ? ($invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '') : '';
            $currencyCode = $invoice ? ($invoice->invoice_currency ?? 'USD') : 'USD';
            $orderReference = $shipper->awb_number ?? ($invoice ? ($invoice->reference_number ?? '') : '');

            // Get csb5_status from customer
            $customer = Customer::find($shipper->customer_id);
            $csb5Status = 0;
            if ($customer && $customer->csb_status) {
                $csb5Status = (int)$customer->csb_status;
            }

            // Get the courier service code for the shipping method
            $shippingMethod = $this->resolveShippingMethod($shipper);
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
            $serviceCode = $courierService ? ($courierService->service_code ?? $courierService->scode ?? '') : '';

            // Build vendor_order_items from invoice items
            $vendorOrderItems = [];
            if ($invoice) {
                $invoiceItems = ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get();
                foreach ($invoiceItems as $item) {
                    $vendorOrderItems[] = [
                        'vendor_order_item_name' => $item->description ?? '',
                        'vendor_order_item_sku' => $item->hs_code ?? $item->hts_code ?? '',
                        'vendor_order_item_quantity' => (int)$item->qty,
                        'vendor_order_item_unit_price' => (float)$item->unit_rate,
                        'vendor_order_item_hsn' => $item->hs_code ?? '',
                        'vendor_order_item_tax_rate' => (float)$item->igst_percentage,
                    ];
                }
            }

            // If no invoice items, add a default item
            if (empty($vendorOrderItems)) {
                $vendorOrderItems[] = [
                    'vendor_order_item_name' => 'General Merchandise',
                    'vendor_order_item_sku' => '',
                    'vendor_order_item_quantity' => 1,
                    'vendor_order_item_unit_price' => (float)($invoice ? $invoice->invoice_amount : 0),
                    'vendor_order_item_hsn' => '',
                    'vendor_order_item_tax_rate' => 0,
                ];
            }

            // Build the Ship Global order payload
            $payload = [
                'invoice_no' => $invoiceNo,
                'invoice_date' => $invoiceDate,
                'order_reference' => $orderReference,
                'service' => $serviceCode,
                'package_weight' => (float)$packageWeightKg,
                'package_length' => (float)$packageLength,
                'package_breadth' => (float)$packageBreadth,
                'package_height' => (float)$packageHeight,
                'currency_code' => $currencyCode,
                'csb5_status' => $csb5Status,
                'seller_nickname' => 'UnitedW',
                'seller_firstname' => $sellerFirstname,
                'seller_lastname' => $sellerLastname,
                'seller_mobile' => $shipper->phone_number ?? '',
                'seller_email' => $shipper->email ?? '',
                'seller_company' => $shipper->company_name ?? '',
                'seller_address' => $shipperAddress ?: 'Address not provided',
                'seller_address_2' => $shipperAddress ?: 'Address not provided',
                'seller_city' => $shipper->city ?? '',
                'seller_postcode' => $shipper->pincode ?? '',
                'seller_country_code' => 'IN',
                'seller_state' => $shipper->state ?? '',
                'customer_shipping_firstname' => $consigneeFirstname,
                'customer_shipping_lastname' => $consigneeLastname,
                'customer_shipping_mobile' => $consignee->phone_number ?? '',
                'customer_shipping_email' => $consignee->email ?? '',
                'customer_shipping_company' => $consignee->consignee_name ?? '',
                'customer_shipping_address' => $consigneeAddress ?: 'Address not provided',
                'customer_shipping_address_2' => $consigneeAddress ?: 'Address not provided',
                'customer_shipping_city' => $consignee->city ?? '',
                'customer_shipping_postcode' => $consignee->zip_code ?? '',
                'customer_shipping_country_code' => $consigneeCountryCode,
                'customer_shipping_state' => $consignee->state ?? '',
                'vendor_order_items' => $vendorOrderItems,
                // i want abw number in tracking field but ship global api is not accepting it so i am leaving it blank for now
                'tracking' => $shipper->awb_number ?? ''
                // 'mailClass' => '',
                // 'deliveryConfirmation' => '',
                // 'retry' => false,
            ];

            // print_r($payload);
            // return;

            \Log::info('Ship Global order payload for shipper #' . $shipper->id . ': ' . json_encode($payload));

            // Step 2: Call the addOrder.php API with Bearer token
            $orderResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $bearerToken,
            ])->post('https://labels.shipglobal.in/api/v1/addOrder.php', $payload);

            $apiResponse = $orderResponse->json();

            // Ship Global may return HTTP 200 but with "success": false in the body
            // (e.g., label: "manual" means order created but label needs manual generation)
            // We treat it as success if an order_number was returned, regardless of body success flag
            $orderNumber = null;
            if (isset($apiResponse['data']) && isset($apiResponse['data']['order_number'])) {
                $orderNumber = $apiResponse['data']['order_number'];
            } elseif (isset($apiResponse['order_number'])) {
                $orderNumber = $apiResponse['order_number'];
            }

            if ($orderResponse->successful() && $orderNumber) {
                \Log::info('Ship Global order created for shipper #' . $shipper->id . '. Order#: ' . $orderNumber . '. Response: ' . json_encode($apiResponse));
                return [
                    'success' => true,
                    'message' => 'Ship Global order created successfully. Order#: ' . $orderNumber,
                    'data' => $apiResponse,
                ];
            } elseif ($orderResponse->successful() && !$orderNumber) {
                // HTTP 200 but no order_number — could be a validation/business error in the body
                $errorMessage = 'Ship Global API returned no order number.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = is_string($apiResponse['error']) ? $apiResponse['error'] : json_encode($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    }
                    if (isset($apiResponse['details']) && is_array($apiResponse['details']) && !empty($apiResponse['details'])) {
                        $errorMessage .= ' — ' . implode('; ', $apiResponse['details']);
                    }
                }
                \Log::error('Ship Global order creation: HTTP 200 but no order_number. Response: ' . json_encode($apiResponse));
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $apiResponse,
                ];
            } else {
                $apiResponse = $orderResponse->json();
                $errorMessage = 'Ship Global API returned error.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = is_string($apiResponse['error']) ? $apiResponse['error'] : json_encode($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    } elseif (isset($apiResponse['errors'])) {
                        $errorMessage = is_string($apiResponse['errors']) ? $apiResponse['errors'] : json_encode($apiResponse['errors']);
                    }
                    // Append validation details if available (Ship Global returns field-level errors in "details")
                    if (isset($apiResponse['details']) && is_array($apiResponse['details']) && !empty($apiResponse['details'])) {
                        $errorMessage .= ' — ' . implode('; ', $apiResponse['details']);
                    }
                }
                \Log::error('Ship Global order creation failed: ' . $errorMessage . ' | Status: ' . $orderResponse->status() . ' | Response: ' . json_encode($apiResponse));
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $apiResponse,
                    'status_code' => $orderResponse->status(),
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Ship Global API call failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Ship Global API call failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Determine if a shipping method should be routed to the PostShipping API.
     * Triggered for DDP variants of UNITED AIR PREMIUM and UNITED PRIOR POST.
     *
     * @param string|null $shippingMethod
     * @return bool
     */
    private function isPostShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return false;
        }

        $methodUpper = strtoupper(trim($shippingMethod));

        // PostShipping API is called only for UNITED AIR PREMIUM DDP shipments.
        $isDdp = str_contains($methodUpper, 'DDP');
        $isAirPremium = str_contains($methodUpper, 'UNITED AIR PREMIUM');

        return $isDdp && $isAirPremium;
    }

    /**
     * Determine the PostShipping ServiceTypeName based on weight, parcel count,
     * and destination. Precedence:
     *   1. Offshore (Northern Ireland, Scottish Highlands & Islands) → DPD111
     *   2. Multiple Parcels (>1)                                   → MDPD112
     *   3. 0-5 Kg (single parcel)                                  → DPDUKEPND
     *   4. 5-30 Kg (single parcel)                                 → DPD112
     *
     * @param string $shippingMethod
     * @param \App\Models\CourierService|null $courierService
     * @param float $totalWeight  Total shipment weight in Kg
     * @param int $noOfItems       Number of parcels
     * @param \App\Models\ConsigneeInfo|null $consignee
     * @return string
     */
    private function getPostShippingServiceTypeName($shippingMethod, $courierService, $totalWeight = 0, $noOfItems = 1, $consignee = null)
    {
        // Priority 1: Offshore deliveries → DPD111 (DPD OFFSHORE- TWO DAY)
        if ($this->isPostShippingOffshoreDestination($consignee)) {
            \Log::info('getPostShippingServiceTypeName: Offshore destination → DPD111');
            return 'DPD111';
        }

        // Priority 2: Multiple parcels → MDPD112 (Multi DPD UK MAINLAND- NEXT DAY)
        if ((int) $noOfItems > 1) {
            \Log::info('getPostShippingServiceTypeName: Multiple parcels (' . $noOfItems . ') → MDPD112');
            return 'MDPD112';
        }

        // Priority 3 & 4: Weight-based for single parcel
        if ($totalWeight <= 5) {
            \Log::info('getPostShippingServiceTypeName: Weight ' . $totalWeight . 'kg (≤5) → DPDUKEPND');
            return 'DPDUKEPND'; // DPD UK Mainland Express PAK
        }

        \Log::info('getPostShippingServiceTypeName: Weight ' . $totalWeight . 'kg (>5) → DPD112');
        return 'DPD112'; // DPD UK Mainland Next Day
    }

    /**
     * Check if a consignee destination is an offshore area requiring DPD111 service.
     * Offshore = Northern Ireland (BT postcodes) + Scottish Highlands & Islands
     * (IV, HS, KA, KW, PA, PH, ZE postcodes) + Isle of Man (IM) + Channel Islands (JE, GY).
     *
     * @param \App\Models\ConsigneeInfo|null $consignee
     * @return bool
     */
    private function isPostShippingOffshoreDestination($consignee)
    {
        if (!$consignee) {
            return false;
        }

        $postcode = strtoupper(preg_replace('/\s+/', '', (string) ($consignee->zip_code ?? '')));
        $city = strtoupper(trim((string) ($consignee->city ?? '')));
        $state = strtoupper(trim((string) ($consignee->state ?? '')));

        // Northern Ireland: postcodes start with BT
        if ($postcode !== '' && str_starts_with($postcode, 'BT')) {
            return true;
        }

        // Scottish Highlands & Islands + other UK offshore postcode prefixes
        $offshorePrefixes = ['IV', 'HS', 'KA', 'KW', 'PA', 'PH', 'ZE', 'IM', 'JE', 'GY'];
        if ($postcode !== '') {
            foreach ($offshorePrefixes as $prefix) {
                if (str_starts_with($postcode, $prefix)) {
                    return true;
                }
            }
        }

        // Fallback: keyword matching on city/state for cases where postcode is missing
        $offshoreKeywords = [
            'NORTHERN IRELAND', 'HIGHLAND', 'ISLAND', 'ISLE OF',
            'ORKNEY', 'SHETLAND', 'HEBRIDES', 'SKYE', 'ISLE OF MAN',
            'CHANNEL ISLANDS', 'JERSEY', 'GUERNSEY',
        ];
        foreach ($offshoreKeywords as $keyword) {
            if (str_contains($city, $keyword) || str_contains($state, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the ThirdPartyToken for PostShipping.
     * A single fixed token is used for all UNITED AIR PREMIUM DDP shipments.
     *
     * @param float $totalWeight  Total shipment weight in Kg (kept for signature compatibility)
     * @return string
     */
    private function getPostShippingThirdPartyToken($totalWeight)
    {
        $token = config('services.postshipping.third_party_token');
        \Log::info('getPostShippingThirdPartyToken: Using fixed token for UNITED AIR PREMIUM DDP');
        return $token;
    }

    /**
     * Build the PostShipping API payload from database records.
     * Payload structure mirrors the documented https://api.postshipping.com/api2/shipments format.
     *
     * @param \App\Models\ShipperInfo $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */
    private function buildPostShippingPayloadFromDb($shipper)
    {
        $consignee = $shipper->consigneeInfo;
        if (!$consignee) {
            \Log::warning('buildPostShippingPayloadFromDb: No consignee found for shipper #' . $shipper->id);
            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        $packages = $shipper->packageDimensions;
        if ($packages->isEmpty()) {
            \Log::warning('buildPostShippingPayloadFromDb: No packages found for shipper #' . $shipper->id);
            return ['success' => false, 'message' => 'No package dimensions found for this shipment.'];
        }

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();

        // Resolve shipping method + courier service
        $shippingMethod = $this->resolveShippingMethod($shipper);
        $courierService = $this->findCourierService($shippingMethod, $shipper->id);

        // Consignee country code from delivery_destination
        $consigneeCountryCode = $this->getCountryCodeFromDestination($consignee->delivery_destination ?? '');

        // Currency code (default INR to match example payload)
        $currencyCode = $invoice ? ($invoice->invoice_currency ?? 'INR') : 'INR';

        // Total weight & package count (computed first so ServiceTypeName + ThirdPartyToken
        // can be selected based on weight/parcel-count/destination).
        $totalWeight = 0;
        $noOfItems = 0;
        foreach ($packages as $pkg) {
            $w = (float) ($pkg->actual_weight_kg ?? 0);
            if ($w <= 0) {
                $w = 0.5;
            }
            $totalWeight += $w;
            $noOfItems++;
        }
        if ($totalWeight <= 0) {
            $totalWeight = 0.5;
        }
        if ($noOfItems <= 0) {
            $noOfItems = 1;
        }

        // ServiceTypeName is weight/parcel-count/destination-dependent:
        //   Offshore → DPD111 | Multiple parcels → MDPD112 | ≤5kg → DPDUKEPND | >5kg → DPD112
        $serviceTypeName = $this->getPostShippingServiceTypeName($shippingMethod, $courierService, $totalWeight, $noOfItems, $consignee);

        // ThirdPartyToken is weight-dependent:
        //   1-10kg → DPDNW token | 10.1-30kg → DPD112 token
        $thirdPartyToken = $this->getPostShippingThirdPartyToken($totalWeight);

        // First package dimensions for top-level PackageDetails
        $firstPackage = $packages->first();
        $cubicL = (float) ($firstPackage->length_cm ?? 0) ?: 10;
        $cubicW = (float) ($firstPackage->width_cm ?? 0) ?: 10;
        $cubicH = (float) ($firstPackage->height_cm ?? 0) ?: 10;

        // Goods description: prefer first invoice item description, else invoice reference, else default
        $goodsDescription = 'General Merchandise';
        $invoiceItems = collect();
        if ($invoice) {
            $invoiceItems = ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get();
            $firstItem = $invoiceItems->first();
            if ($firstItem && !empty($firstItem->description)) {
                $goodsDescription = $firstItem->description;
            }
        }

        // Custom value: invoice amount (default 30.00 to match example floor)
        $customValue = $invoice ? (float) ($invoice->invoice_amount ?? 0) : 0;
        if ($customValue <= 0) {
            $customValue = 30.00;
        }

        // Incoterms
        $incoterms = $invoice ? ($invoice->incoterms ?? 'CIF') : 'CIF';
        $orderNumber = $invoice ? ($invoice->invoice_number ?? '') : ($shipper->awb_number ?? '');

        // Build ShipmentResponseItem array — one entry per package, with Pieces from invoice items.
        $shipmentResponseItems = [];
        $packageIndex = 0;
        foreach ($packages as $pkg) {
            $packageIndex++;
            $pkgWeight = (float) ($pkg->actual_weight_kg ?? 0);
            if ($pkgWeight <= 0) {
                $pkgWeight = 0.5;
            }
            $pkgL = (float) ($pkg->length_cm ?? 0) ?: 10;
            $pkgW = (float) ($pkg->width_cm ?? 0) ?: 10;
            $pkgH = (float) ($pkg->height_cm ?? 0) ?: 10;

            // Find invoice items mapped to this package via box_no (1-based)
            $boxItems = $invoiceItems->filter(function ($item) use ($packageIndex) {
                return ((int) ($item->box_no ?? 0)) === $packageIndex;
            });

            // If no items mapped to this box, use all items for the first package
            if ($boxItems->isEmpty() && $packageIndex === 1) {
                $boxItems = $invoiceItems;
            }

            $pieces = [];
            if ($boxItems->isNotEmpty()) {
                foreach ($boxItems as $item) {
                    $itemQty = (float) ($item->qty ?? 1);
                    if ($itemQty <= 0) {
                        $itemQty = 1;
                    }
                    $itemValue = (float) ($item->amount ?? 0);
                    if ($itemValue <= 0) {
                        $itemValue = $customValue;
                    }
                    $pieces[] = [
                        'HarmonisedCode'        => (string) ($item->hs_code ?? $item->hts_code ?? ''),
                        'GoodsDescription'      => $item->description ?? $goodsDescription,
                        'Content'               => $item->description ?? $goodsDescription,
                        'Quantity'              => $itemQty,
                        'Weight'                => $pkgWeight,
                        'ManufactureCountryCode' => 'IN',
                        'OriginCountryCode'     => 'IN',
                        'CurrencyCode'          => $currencyCode,
                        'CustomsValue'          => $itemValue,
                    ];
                }
            } else {
                // Fallback single piece when no invoice items exist
                $pieces[] = [
                    'HarmonisedCode'        => '',
                    'GoodsDescription'      => $goodsDescription,
                    'Content'               => $goodsDescription,
                    'Quantity'              => 1,
                    'Weight'                => $pkgWeight,
                    'ManufactureCountryCode' => 'IN',
                    'OriginCountryCode'     => 'IN',
                    'CurrencyCode'          => $currencyCode,
                    'CustomsValue'          => $customValue,
                ];
            }

            $shipmentResponseItems[] = [
                'ItemNoOfPcs'            => 1,
                'ItemCubicL'             => $pkgL,
                'ItemCubicW'             => $pkgW,
                'ItemCubicH'             => $pkgH,
                'ItemWeight'             => $pkgWeight,
                'ItemDescription'        => $goodsDescription,
                'ItemCustomValue'        => $customValue,
                'ItemCustomCurrencyCode' => $currencyCode,
                'Notes'                  => 'Commercial shipment',
                'Pieces'                 => $pieces,
            ];
        }

        // Pickup times — ReadyTime = now + 2h, CloseTime = now + 5h (format Y/m/d H:i:s)
        $readyTime = now()->addHours(2)->format('Y/m/d H:i:s');
        $closeTime = now()->addHours(5)->format('Y/m/d H:i:s');

        // Build the single shipment object (API expects an array of these)
        $shipmentObject = [
            'ThirdPartyToken' => $thirdPartyToken,
            'SenderDetails' => [
                // 'SenderName'                 => $shipper->contact_person ?? ($shipper->company_name ?? 'Ved'),
                // 'SenderCompanyName'          => $shipper->company_name ?? 'United Worldwide Couriers Pvt Ltd',
                // 'SenderCountryCode'          => 'IN',
                // 'SenderAdd1'                 => $shipper->address_line1 ?? '',
                // 'SenderAdd2'                 => $shipper->address_line2 ?? '',
                // 'SenderAdd3'                 => $shipper->address_line3 ?? '',
                // 'SenderAddCity'              => strtoupper($shipper->city ?? 'NEW DELHI'),
                // 'SenderAddState'             => strtoupper($shipper->state ?? 'DELHI'),
                // 'SenderAddPostcode'          => $shipper->pincode ?? '110037',
                // 'SenderPhone'                => $shipper->phone_number ?? '01146122222',
                // 'SenderEmail'                => $shipper->email ?? 'abc@abc.com',
                // 'SenderFax'                  => '',
                // 'SenderKycType'              => $shipper->kyc_type ?? 'Passport',
                // 'SenderKycNumber'            => $shipper->kyc_number ?? '',
                // 'SenderReceivingCountryTaxID' => '',
                'SenderName' => "Ved",
                'SenderCompanyName' => "United Worldwide Couriers Pvt Ltd",
                'SenderCountryCode' => "IN",
                'SenderAdd1' => "BUILDING NO 1 BYPASS ROAD",
                'SenderAdd2' => "MAHIPALPUR",
                'SenderAdd3' => "",
                'SenderAddCity' => "NEW DELHI",
                'SenderAddState' => "DELHI",
                'SenderAddPostcode' => "110037",
                'SenderPhone' => "01146122222",
                'SenderEmail' => "abc@abc.com",
                'SenderFax' => "",
                'SenderKycType' => "Passport",
                'SenderKycNumber' => "P00001",
                'SenderReceivingCountryTaxID' => ""
            ],
            'ReceiverDetails' => [
                'ReceiverName'          => $consignee->consignee_name ?? ($consignee->contact_person ?? 'Consignee'),
                'ReceiverCompanyName'   => $consignee->consignee_name ?? ($consignee->contact_person ?? ''),
                // 'ReceiverCountryCode'   => $consigneeCountryCode,
                'ReceiverCountryCode'   => "GB",
                'ReceiverAdd1'          => $consignee->address_line1 ?? '',
                'ReceiverAdd2'          => $consignee->address_line2 ?? '',
                'ReceiverAdd3'          => $consignee->address_line3 ?? '',
                'ReceiverAddCity'       => $consignee->city ?? '',
                'ReceiverAddState'      => $consignee->state ?? '',
                'ReceiverAddPostcode'   => $consignee->zip_code ?? '',
                'ReceiverMobile'        => $consignee->phone_number ?? '',
                'ReceiverPhone'         => $consignee->phone_number ?? '',
                'ReceiverEmail'         => $consignee->email ?? 'abc@abc.com',
                'ReceiverAddResidential' => 'N',
                'ReceiverFax'           => '',
                'ReceiverKycType'       => 'Passport',
                'ReceiverKycNumber'     => '',
            ],
            'PackageDetails' => [
                'GoodsDescription'      => $goodsDescription,
                'CustomValue'           => (float) $customValue,
                'CustomCurrencyCode'    => $currencyCode,
                'InsuranceValue'        => 0.00,
                'InsuranceCurrencyCode' => $currencyCode,
                'ShipmentTerm'          => '',
                'GoodsOriginCountryCode' => 'IN',
                'Weight'                => (float) $totalWeight,
                'WeightMeasurement'     => 'KG',
                'NoOfItems'             => (int) $noOfItems,
                'CubicL'                => (float) $cubicL,
                'CubicW'                => (float) $cubicW,
                'CubicH'                => (float) $cubicH,
                'CubicWeight'           => 0,
                'ServiceTypeName'       => $serviceTypeName,
                'BookPickUP'            => false,
                'SenderRef1'            => $shipper->awb_number ?? ('TEST-SHIPMENT-' . $shipper->id),
                'BusinessType'          => 'B2B',
                'ShipmentResponseItem'  => $shipmentResponseItems,
                'CODAmount'             => 0,
                'CODCurrencyCode'       => $currencyCode,
                'DeadWeight'            => (float) $totalWeight,
                'ReasonExport'          => 'Sale',
                'OrderNumber'           => $orderNumber,
                'Incoterms'             => $incoterms,
            ],
            'PickupDetails' => [
                'ReadyTime'             => $readyTime,
                'CloseTime'             => $closeTime,
                'SpecialInstructions'   => 'Call before pickup',
                'Address1'              => $shipper->address_line1 ?? '',
                'Address2'              => $shipper->address_line2 ?? '',
                'Address3'              => $shipper->address_line3 ?? '',
                'AddressCity'           => strtoupper($shipper->city ?? 'NEW DELHI'),
                'AddressState'          => strtoupper($shipper->state ?? 'DELHI'),
                'AddressPostalCode'     => $shipper->pincode ?? '110037',
                'AddressCountryCode'    => 'IN',
            ],
        ];

        // PostShipping expects an array of shipment objects.
        // The fixed ThirdPartyToken is also returned so the caller can
        // send it as the API-Key request header.
        return [
            'success'           => true,
            'payload'           => [$shipmentObject],
            'third_party_token' => $thirdPartyToken,
        ];
    }

    /**
     * Call the PostShipping API to create a shipment.
     * Endpoint: https://api.postshipping.com/api2/shipments
     *
     * @param \App\Models\ShipperInfo $shipper
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    private function callPostShippingApiFromDb($shipper)
    {
        try {
            $payloadResult = $this->buildPostShippingPayloadFromDb($shipper);
            if (!$payloadResult['success']) {
                return [
                    'success' => false,
                    'message' => $payloadResult['message'] ?? 'Failed to build PostShipping payload.',
                ];
            }

            $payload = $payloadResult['payload'];
            // The ThirdPartyToken is sent inside the request body.
            // A SEPARATE api_token is sent as the "token" request header for authentication.
            $apiToken = config('services.postshipping.api_token');
            $baseUrl = rtrim(config('services.postshipping.base_url'), '/');
            $endpoint = config('services.postshipping.endpoint', '/api2/shipments');
            $url = $baseUrl . $endpoint;
            $timeout = (int) config('services.postshipping.timeout', 60);

            \Log::info('PostShipping payload for shipper #' . $shipper->id . ': ' . substr(json_encode($payload), 0, 2000));

            $headers = [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'Connection'   => 'keep-alive',
            ];
            if (!empty($apiToken)) {
                $headers['token'] = $apiToken;
            }

            $response = Http::withHeaders($headers)
                ->withOptions(['verify' => false])
                ->timeout($timeout)
                ->post($url, $payload);

            $apiResponse = $response->json();

            if (!$response->successful()) {
                $errorMessage = 'PostShipping API returned error.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = is_string($apiResponse['error']) ? $apiResponse['error'] : json_encode($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    } elseif (isset($apiResponse['errors'])) {
                        $errorMessage = is_string($apiResponse['errors']) ? $apiResponse['errors'] : json_encode($apiResponse['errors']);
                    }
                    if (isset($apiResponse['details']) && is_array($apiResponse['details']) && !empty($apiResponse['details'])) {
                        $errorMessage .= ' — ' . implode('; ', $apiResponse['details']);
                    }
                }
                \Log::error('PostShipping API failed: ' . $errorMessage . ' | Status: ' . $response->status() . ' | Body: ' . $response->body());
                return [
                    'success'     => false,
                    'message'      => $errorMessage,
                    'data'         => $apiResponse,
                    'status_code'  => $response->status(),
                ];
            }

            \Log::info('PostShipping response for shipper #' . $shipper->id . ': ' . substr($response->body(), 0, 2000));

            return [
                'success' => true,
                'message' => 'PostShipping shipment created successfully.',
                'data'     => $apiResponse,
            ];
        } catch (\Exception $e) {
            \Log::error('PostShipping API call failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'PostShipping API call failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Extract a tracking/reference number from a PostShipping API response.
     *
     * The documented response format is an array of shipment objects, each
     * containing: ShipmentNumber, AlternateRef, LabelURL, ErrMessage, AcccountCode.
     * We also keep fallbacks for other possible shapes (top-level, nested under
     * "data", or wrapped under "Shipments") for resilience.
     *
     * @param mixed $apiResponse
     * @return string|null
     */
    private function extractPostShippingTrackingNumber($apiResponse)
    {
        if (!is_array($apiResponse)) {
            return null;
        }

        // Priority keys for the documented PostShipping response format.
        $priorityKeys = ['ShipmentNumber', 'shipment_number', 'AlternateRef', 'alternate_ref'];
        $fallbackKeys = [
            'tracking_number', 'TrackingNumber', 'waybill_number', 'WaybillNumber',
            'awb_number', 'AwbNumber', 'consignment_number', 'ConsignmentNumber',
            'order_number', 'OrderNumber', 'waybill', 'Waybill',
            'reference', 'Reference', 'shipment_id', 'ShipmentId',
        ];
        $candidateKeys = array_merge($priorityKeys, $fallbackKeys);

        // Case A: The response itself is a list of shipment objects
        // (documented format: [{ ShipmentNumber, LabelURL, ... }])
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($candidateKeys as $key) {
                if (isset($apiResponse[0][$key]) && !empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys (single shipment object returned directly)
        foreach ($candidateKeys as $key) {
            if (isset($apiResponse[$key]) && !empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "data"
        $data = $apiResponse['data'] ?? null;
        if (is_array($data)) {
            // data is a list of shipments
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($candidateKeys as $key) {
                    if (isset($data[0][$key]) && !empty($data[0][$key])) {
                        return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                    }
                }
            }
            // data is a single shipment object
            foreach ($candidateKeys as $key) {
                if (isset($data[$key]) && !empty($data[$key])) {
                    return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                }
            }
        }

        // Case D: Nested under "Shipments" / "shipments" / "Shipment" / "shipment"
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($candidateKeys as $key) {
                            if (isset($first[$key]) && !empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract the LabelURL from a PostShipping API response.
     *
     * The documented response format is an array of shipment objects, each
     * containing a LabelURL field with the shipping label download link.
     *
     * @param mixed $apiResponse
     * @return string|null
     */
    private function extractPostShippingLabelUrl($apiResponse)
    {
        if (!is_array($apiResponse)) {
            return null;
        }

        $labelKeys = ['LabelURL', 'label_url', 'LabelUrl', 'labelurl', 'Label', 'label'];

        // Case A: The response itself is a list of shipment objects
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($labelKeys as $key) {
                if (isset($apiResponse[0][$key]) && !empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys
        foreach ($labelKeys as $key) {
            if (isset($apiResponse[$key]) && !empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "data"
        $data = $apiResponse['data'] ?? null;
        if (is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($labelKeys as $key) {
                    if (isset($data[0][$key]) && !empty($data[0][$key])) {
                        return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                    }
                }
            }
            foreach ($labelKeys as $key) {
                if (isset($data[$key]) && !empty($data[$key])) {
                    return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                }
            }
        }

        // Case D: Nested under "Shipments" / "shipments"
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($labelKeys as $key) {
                            if (isset($first[$key]) && !empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Flying Tigers API (UNITED ECO POST)
    |--------------------------------------------------------------------------
    | Endpoint: https://app.flyingtigers.in/api/Shipment/CustomerBookingAPI
    | Auth headers: ClientCode, UserCode, AuthToken
    |
    */

    /**
     * Determine if a shipping method should be routed to the Flying Tigers API.
     * Triggered for UNITED ECO POST shipments.
     *
     * @param string|null $shippingMethod
     * @return bool
     */
    private function isFlyingTigersMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return false;
        }

        $methodUpper = strtoupper(trim($shippingMethod));

        // Flying Tigers API is called for UNITED ECO POST shipments.
        return str_contains($methodUpper, 'UNITED ECO POST');
    }

    /**
     * Build the Flying Tigers API payload from database records.
     * Payload structure mirrors the documented CustomerBookingAPI format.
     *
     * @param \App\Models\ShipperInfo $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */
    private function buildFlyingTigersPayloadFromDb($shipper)
    {
        $consignee = $shipper->consigneeInfo;
        if (!$consignee) {
            \Log::warning('buildFlyingTigersPayloadFromDb: No consignee found for shipper #' . $shipper->id);
            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        $packages = $shipper->packageDimensions;
        if ($packages->isEmpty()) {
            \Log::warning('buildFlyingTigersPayloadFromDb: No packages found for shipper #' . $shipper->id);
            return ['success' => false, 'message' => 'No package dimensions found for this shipment.'];
        }

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();

        // Consignee country code from delivery_destination (e.g. "US", "UK")
        $consigneeCountryCode = $this->getCountryCodeFromDestination($consignee->delivery_destination ?? '');

        // Currency code (default INR to match example payload)
        $currencyCode = $invoice ? ($invoice->invoice_currency ?? 'INR') : 'INR';

        // Booking date in "d-M-Y" format (e.g. "25-May-2026")
        $bookingDate = now()->format('d-M-Y');

        // Reference number: prefer invoice reference, else AWB number
        $refNo = $invoice ? ($invoice->reference_number ?? '') : '';
        if (empty($refNo)) {
            $refNo = $shipper->awb_number ?? ('FT-' . $shipper->id);
        }

        // Consignee name: prefer consignee_name, else contact_person
        $consigneeName = $consignee->consignee_name ?? '';
        if (empty(trim($consigneeName))) {
            $consigneeName = $consignee->contact_person ?? '';
        }

        // Consignee phone
        $consigneePhone = $consignee->phone_number ?? '';

        // Consignee address (combine line1 + line2 + line3 if line1 is short)
        $consigneeAddress1 = trim($consignee->address_line1 ?? '');
        if (empty($consigneeAddress1)) {
            $consigneeAddress1 = trim(($consignee->address_line2 ?? '') . ' ' . ($consignee->address_line3 ?? ''));
        }

        // Invoice number & date for packet details
        $invoiceNo = $invoice ? ($invoice->invoice_number ?? '') : '';
        if (empty($invoiceNo)) {
            $invoiceNo = $shipper->awb_number ?? ('INV-' . $shipper->id);
        }
        $invoiceDate = $invoice && $invoice->invoice_date
            ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y')
            : now()->format('d-M-Y');

        // Build addPacketDetailList — one entry per package, with boxInvoiceDetails from invoice items.
        $addPacketDetailList = [];
        $invoiceItems = collect();
        if ($invoice) {
            $invoiceItems = ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get();
        }

        $packageIndex = 0;
        foreach ($packages as $pkg) {
            $packageIndex++;

            $pkgWeight = (float) ($pkg->actual_weight_kg ?? 0);
            if ($pkgWeight <= 0) {
                $pkgWeight = 0.5;
            }
            $pkgL = (float) ($pkg->length_cm ?? 0) ?: 1;
            $pkgW = (float) ($pkg->width_cm ?? 0) ?: 1;
            $pkgH = (float) ($pkg->height_cm ?? 0) ?: 1;

            // Find invoice items mapped to this package via box_no (1-based)
            $boxItems = $invoiceItems->filter(function ($item) use ($packageIndex) {
                return ((int) ($item->box_no ?? 0)) === $packageIndex;
            });

            // If no items mapped to this box, use all items for the first package
            if ($boxItems->isEmpty() && $packageIndex === 1) {
                $boxItems = $invoiceItems;
            }

            $boxInvoiceDetails = [];
            if ($boxItems->isNotEmpty()) {
                foreach ($boxItems as $item) {
                    $itemQty = (float) ($item->qty ?? 1);
                    if ($itemQty <= 0) {
                        $itemQty = 1;
                    }
                    $itemUnitPrice = (float) ($item->unit_rate ?? 0);
                    if ($itemUnitPrice <= 0) {
                        $itemUnitPrice = (float) ($item->amount ?? 0) / $itemQty;
                    }
                    if ($itemUnitPrice <= 0) {
                        $itemUnitPrice = 5.00;
                    }
                    $boxInvoiceDetails[] = [
                        'ProductName' => (string) ($item->description ?? 'General Merchandise'),
                        'UnitPrice'   => number_format($itemUnitPrice, 2, '.', ''),
                        'Quantity'    => (string) (int) $itemQty,
                    ];
                }
            } else {
                // Fallback single item when no invoice items exist
                $boxInvoiceDetails[] = [
                    'ProductName' => 'General Merchandise',
                    'UnitPrice'   => '5.00',
                    'Quantity'    => '1',
                ];
            }

            $addPacketDetailList[] = [
                'BoxWeight'         => number_format($pkgWeight, 3, '.', ''),
                'BoxLength'         => number_format($pkgL, 2, '.', ''),
                'BoxWidth'          => number_format($pkgW, 2, '.', ''),
                'BoxHeight'         => number_format($pkgH, 2, '.', ''),
                'InvoiceNo'         => (string) $invoiceNo,
                'InvoiceDate'       => (string) $invoiceDate,
                'boxInvoiceDetails' => $boxInvoiceDetails,
            ];
        }

        
        $payload = [
            'shipmentType'      => 'Forward',
            // 'consigneeCountry'  => (string) ($consignee->delivery_destination ?? $consigneeCountryCode ?? 'US'),
            'consigneeCountry'  => (string) ('US'),
            'RefNo'             => (string) $refNo,
            'BookingDate'       => (string) $bookingDate,
            'Consignee'         => (string) $consigneeName,
            'ConsigneePhoneNo'  => (string) $consigneePhone,
            'ConsigneeAddress1' => (string) $consigneeAddress1,
            'ConsigneePinCode'  => (string) ($consignee->zip_code ?? ''),
            'ConsigneeState'    => (string) ($consignee->state ?? ''),
            'ConsigneeCity'     => (string) ($consignee->city ?? ''),
            'BusinessType'      => 'B2C',
            'Vendor'            => 'USPS Work',
            'Service'           => 'Uniuni',
            'PickupPoint'       => '2',
            'addPacketDetailList' => $addPacketDetailList,
            'PackageType'       => 'NONDOC',
            'currencyCode'      => (string) $currencyCode,
        ];
        
        print_r($payload); // Debugging line to inspect the payload structure
        

        return [
            'success' => true,
            'payload' => $payload,
        ];
    }

    /**
     * Call the Flying Tigers API to create a shipment.
     * Endpoint: https://app.flyingtigers.in/api/Shipment/CustomerBookingAPI
     *
     * @param \App\Models\ShipperInfo $shipper
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    private function callFlyingTigersApiFromDb($shipper)
    {
        try {
            $payloadResult = $this->buildFlyingTigersPayloadFromDb($shipper);
            if (!$payloadResult['success']) {
                return [
                    'success' => false,
                    'message' => $payloadResult['message'] ?? 'Failed to build Flying Tigers payload.',
                ];
            }

            $payload = $payloadResult['payload'];

            $clientCode = config('services.flyingtigers.client_code');
            $userCode = config('services.flyingtigers.user_code');
            $authToken = config('services.flyingtigers.auth_token');
            $baseUrl = rtrim(config('services.flyingtigers.base_url'), '/');
            $endpoint = config('services.flyingtigers.endpoint', '/api/Shipment/CustomerBookingAPI');
            $url = $baseUrl . $endpoint;
            $timeout = (int) config('services.flyingtigers.timeout', 60);

            \Log::info('Flying Tigers payload for shipper #' . $shipper->id . ': ' . substr(json_encode($payload), 0, 2000));

            $headers = [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'ClientCode'   => $clientCode,
                'UserCode'     => $userCode,
                'AuthToken'    => $authToken,
            ];

            $response = Http::withHeaders($headers)
                ->withOptions(['verify' => false])
                ->timeout($timeout)
                ->post($url, $payload);

            $apiResponse = $response->json();

            if (!$response->successful()) {
                $errorMessage = 'Flying Tigers API returned error.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = is_string($apiResponse['error']) ? $apiResponse['error'] : json_encode($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    } elseif (isset($apiResponse['errors'])) {
                        $errorMessage = is_string($apiResponse['errors']) ? $apiResponse['errors'] : json_encode($apiResponse['errors']);
                    }
                    if (isset($apiResponse['details']) && is_array($apiResponse['details']) && !empty($apiResponse['details'])) {
                        $errorMessage .= ' — ' . implode('; ', $apiResponse['details']);
                    }
                }
                \Log::error('Flying Tigers API failed: ' . $errorMessage . ' | Status: ' . $response->status() . ' | Body: ' . $response->body());
                return [
                    'success'     => false,
                    'message'      => $errorMessage,
                    'data'         => $apiResponse,
                    'status_code'  => $response->status(),
                ];
            }

            \Log::info('Flying Tigers response for shipper #' . $shipper->id . ': ' . substr($response->body(), 0, 2000));

            return [
                'success' => true,
                'message' => 'Flying Tigers shipment created successfully.',
                'data'     => $apiResponse,
            ];
        } catch (\Exception $e) {
            \Log::error('Flying Tigers API call failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Flying Tigers API call failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Extract a tracking/reference number from a Flying Tigers API response.
     *
     * @param mixed $apiResponse
     * @return string|null
     */
    private function extractFlyingTigersTrackingNumber($apiResponse)
    {
        if (!is_array($apiResponse)) {
            return null;
        }

        $candidateKeys = [
            'TrackingNumber', 'tracking_number', 'WaybillNumber', 'waybill_number',
            'AwbNumber', 'awb_number', 'ConsignmentNumber', 'consignment_number',
            'OrderNumber', 'order_number', 'ShipmentNumber', 'shipment_number',
            'ReferenceNo', 'reference_no', 'RefNo', 'BookingId', 'booking_id',
            'Waybill', 'waybill', 'Reference', 'reference', 'ShipmentId', 'shipment_id',
        ];

        // Case A: The response itself is a list of shipment objects
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($candidateKeys as $key) {
                if (isset($apiResponse[0][$key]) && !empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys
        foreach ($candidateKeys as $key) {
            if (isset($apiResponse[$key]) && !empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "data"
        $data = $apiResponse['data'] ?? null;
        if (is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($candidateKeys as $key) {
                    if (isset($data[0][$key]) && !empty($data[0][$key])) {
                        return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                    }
                }
            }
            foreach ($candidateKeys as $key) {
                if (isset($data[$key]) && !empty($data[$key])) {
                    return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                }
            }
        }

        // Case D: Nested under "Shipments" / "shipments" / "Shipment" / "shipment"
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment', 'Result', 'result'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($candidateKeys as $key) {
                            if (isset($first[$key]) && !empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract the LabelURL from a Flying Tigers API response.
     *
     * @param mixed $apiResponse
     * @return string|null
     */
    private function extractFlyingTigersLabelUrl($apiResponse)
    {
        if (!is_array($apiResponse)) {
            return null;
        }

        $labelKeys = ['LabelURL', 'label_url', 'LabelUrl', 'labelurl', 'Label', 'label', 'PdfUrl', 'pdf_url', 'LabelLink', 'label_link'];

        // Case A: The response itself is a list of shipment objects
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($labelKeys as $key) {
                if (isset($apiResponse[0][$key]) && !empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys
        foreach ($labelKeys as $key) {
            if (isset($apiResponse[$key]) && !empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "data"
        $data = $apiResponse['data'] ?? null;
        if (is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($labelKeys as $key) {
                    if (isset($data[0][$key]) && !empty($data[0][$key])) {
                        return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                    }
                }
            }
            foreach ($labelKeys as $key) {
                if (isset($data[$key]) && !empty($data[$key])) {
                    return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                }
            }
        }

        // Case D: Nested under "Shipments" / "shipments" / "Result" / "result"
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment', 'Result', 'result'] as $wrapKey) {
            if (isset($apiResponse[$wrapKey])) {
                $wrap = $apiResponse[$wrapKey];
                if (is_array($wrap)) {
                    $first = isset($wrap[0]) ? $wrap[0] : $wrap;
                    if (is_array($first)) {
                        foreach ($labelKeys as $key) {
                            if (isset($first[$key]) && !empty($first[$key])) {
                                return is_string($first[$key]) ? $first[$key] : (string) $first[$key];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Recharge the authenticated customer's wallet.
     */
    public function walletRecharge(Request $request)
    {
        try {
            if (!auth()->guard('customer')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
            ]);

            $customerId = auth()->guard('customer')->id();
            $amount = $validated['amount'];

            $wallet = Wallet::where('customer_id', $customerId)->first();

            if (!$wallet) {
                return response()->json(['success' => false, 'message' => 'Wallet not found. Please contact support.']);
            }

            DB::transaction(function () use ($wallet, $amount) {
                $wallet->increment('balance', $amount);
            });

            $wallet->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Wallet recharged successfully! ₹' . number_format($amount, 2) . ' has been added.',
                'new_balance' => (float) $wallet->balance,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing recharge: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a unique AWB number.
     * Format: UWC + YYMMDD + 5-digit serial (resets daily)
     * Example: UWC26060200001
     */
    public function searchHsCodes(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = \App\Models\HsHtsCode::where('items', 'LIKE', "%{$query}%")
            ->limit(20)
            ->get(['id', 'items', 'hs_code', 'hts_code']);

        return response()->json($results);
    }

    private function generateAwbNumber()
    {
        $prefix = 'UWC';
        $datePart = now()->format('ymd'); // e.g., 260602 for 2026-06-02

        // Find the highest serial number for today's date prefix
        $todayPrefix = $prefix . $datePart;
        $lastAwb = ShipperInfo::where('awb_number', 'LIKE', $todayPrefix . '%')
            ->orderBy('awb_number', 'desc')
            ->value('awb_number');

        if ($lastAwb) {
            // Extract the serial number and increment
            $lastSerial = (int) substr($lastAwb, -5);
            $newSerial = $lastSerial + 1;
        } else {
            // Start from 1 for a new day
            $newSerial = 1;
        }

        // Pad serial to 5 digits
        $serialPart = str_pad($newSerial, 5, '0', STR_PAD_LEFT);

        return $todayPrefix . $serialPart;
    }
}