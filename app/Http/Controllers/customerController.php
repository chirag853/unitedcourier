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
            return redirect()->route('customer.login');
        }

        $customer = auth()->guard('customer')->user();
        return view('customer.dashboard', compact('customer'));
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
            return redirect()->route('customer.login');
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
            return redirect()->route('customer.login');
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
                'delivery_destination' => $validatedData['delivery_destination'],
                'origin_type' => $validatedData['origin_type'],
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
            ]);

            $shipperId = $shipper->id;

            // Store Consignee Info
            $consignee = ConsigneeInfo::create([
                'shipper_id' => $shipperId,
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
        try {
            $serviceId = $request->service_id;
            $totalWeight = floatval($request->total_weight ?? 0);
            $consigneeState = $request->consignee_state;

            if (empty($serviceId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service ID is required.'
                ], 400);
            }

            // Find matching CourierService by ID
            $service = \App\Models\CourierService::find($serviceId);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching service found for ID: ' . $serviceId
                ], 404);
            }

            // Check if this is a zone-independent service (e.g., AIREXPRESS)
            $isZoneIndependent = str_contains(strtoupper($service->method), 'AIREXPRESS');

            // For zone-dependent services, consignee state is required
            if (!$isZoneIndependent && empty($consigneeState)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consignee state is required to determine zone.'
                ], 400);
            }

            // Get the currently logged-in customer
            $customer = auth()->guard('customer')->user();
            $customerId = $customer ? $customer->id : 0;
            $customerExists = $customer ? true : false;

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to view rates.'
                ], 401);
            }

            // Look up zone by consignee state name (only for zone-dependent services)
            $zone = null;
            if (!$isZoneIndependent) {
                $zone = \App\Models\Zone::where('zone_code', $consigneeState)->first();

                if (!$zone) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No zone found for state: ' . $consigneeState
                    ], 404);
                }
            }

            // Find matching courier rates for the customer (or default)
            // For zone-independent services (AIREXPRESS), filter by zone_no = null or 0
            if ($isZoneIndependent) {
                $rates = \App\Models\CourierRate::where('customer_id', $customerId)
                    ->where('service_id', $service->id)
                    ->where(function($q) {
                        $q->whereNull('zone_no')->orWhere('zone_no', 0);
                    })
                    ->orderBy('wt_range_start')
                    ->get();

                // If no customer-specific rates found, fall back to default rates
                if ($rates->isEmpty() && $customerId !== 0) {
                    $rates = \App\Models\CourierRate::where('customer_id', 0)
                        ->where('service_id', $service->id)
                        ->where(function($q) {
                            $q->whereNull('zone_no')->orWhere('zone_no', 0);
                        })
                        ->orderBy('wt_range_start')
                        ->get();
                    $customerExists = false;
                }
            } else {
                $rates = \App\Models\CourierRate::where('customer_id', $customerId)
                    ->where('service_id', $service->id)
                    ->where('zone_no', $zone->zone_number)
                    ->orderBy('wt_range_start')
                    ->get();

                // If no customer-specific rates found, fall back to default rates
                if ($rates->isEmpty() && $customerId !== 0) {
                    $rates = \App\Models\CourierRate::where('customer_id', 0)
                        ->where('service_id', $service->id)
                        ->where('zone_no', $zone->zone_number)
                        ->orderBy('wt_range_start')
                        ->get();
                    $customerExists = false;
                }
            }

            // Find the rate that matches the current weight (for highlighting)
            // Filter rates by weight — only include rates where weight falls within the range
            $filteredRates = $rates->filter(function ($r) use ($totalWeight) {
                return $totalWeight >= $r->wt_range_start && $totalWeight <= $r->wt_range_end;
            });

            return response()->json([
                'success' => true,
                'customer_exists' => $customerExists,
                'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : null,
                'zone' => $zone ? [
                    'zone_id' => $zone->id,
                    'zone_number' => $zone->zone_number,
                    'zone_name' => $zone->zone_name,
                    'zone_code' => $zone->zone_code,
                ] : null,
                'is_zone_independent' => $isZoneIndependent,
                'service' => [
                    'network' => $service->network,
                    'method' => $service->method,
                    'type' => $service->type,
                    'tat' => $service->tat,
                ],
                'matched_rate' => $filteredRates->isNotEmpty() ? [
                    'zone_no' => $filteredRates->first()->zone_no,
                    'wt_range_start' => $filteredRates->first()->wt_range_start,
                    'wt_range_end' => $filteredRates->first()->wt_range_end,
                    'price' => $filteredRates->first()->price,
                ] : null,
                'all_rates' => $filteredRates->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'zone_no' => $r->zone_no,
                        'wt_range_start' => $r->wt_range_start,
                        'wt_range_end' => $r->wt_range_end,
                        'price' => $r->price,
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
            'US- United State of America' => 'US',
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
            return redirect()->route('customer.login');
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
                    'ship_to' => $consignee ? trim(($consignee->city ?? '') . ', ' . ($consignee->state ?? '') . ' - ' . ($consignee->zip_code ?? '') . ', ' . ($shipper ? $shipper->delivery_destination : '')) : null,
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
                    'destination' => $shipper ? $shipper->delivery_destination : null,
                    'origin_type' => $shipper ? $shipper->origin_type : null,
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
     * Cancel a shipment (set status to cancelled).
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

            // Update status to cancelled
            $invoice->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Shipment cancelled successfully.'
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

            return response()->json(['success' => true, 'message' => 'Status updated to Packed.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Manifest a single shipment - call UPS Ship API and store tracking data.
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

            // Build UPS payload from DB records
            $payloadResult = $this->buildUpsShipPayloadFromDb($shipper);
            if (!$payloadResult['success']) {
                return response()->json(['success' => false, 'message' => $payloadResult['message']], 400);
            }
            $upsPayload = $payloadResult['payload'];

            // Call UPS Ship API
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

            // Get create_shipment record
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

                \Log::info('Shipment manifested: ' . ($shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber'] ?? 'N/A'));
            } catch (\Exception $e) {
                \Log::error('Failed to store shipment tracking for manifest: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to store tracking data: ' . $e->getMessage()], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Shipment manifested successfully!',
                'tracking_number' => $trackingNumber,
                'shipper_id' => $shipperId,
            ]);
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

                    $results['success'][] = [
                        'shipper_id' => $shipperId,
                        'tracking_number' => $trackingNumber,
                    ];

                    \Log::info('Bulk manifest: shipment ' . $shipperId . ' manifested successfully.');

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
            'delivery_destination' => $shipper->delivery_destination,
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