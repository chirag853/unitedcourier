<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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

        return redirect()->route('customer.login')->with('success', 'You have been logged out successfully.');
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
                'package_shipping_method' => 'nullable|string|max:100',
                'packages' => 'nullable|array',
                'packages.*.actual_weight_kg' => 'nullable|numeric|min:0',
                'packages.*.length_cm' => 'nullable|numeric|min:0',
                'packages.*.width_cm' => 'nullable|numeric|min:0',
                'packages.*.height_cm' => 'nullable|numeric|min:0',
                'packages.*.volumetric_weight' => 'nullable|numeric|min:0',

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
                'items.*.unit_type' => 'nullable|string|max:50',
                'items.*.qty' => 'nullable|numeric|min:0',
                'items.*.unit_rate' => 'nullable|numeric|min:0',
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
                    ShipmentInvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'box_no' => $item['box_no'] ?? null,
                        'description' => $item['description'] ?? null,
                        'hs_code' => $item['hs_code'] ?? null,
                        'unit_type' => $item['unit_type'] ?? null,
                        'qty' => $item['qty'] ?? null,
                        'unit_rate' => $item['unit_rate'] ?? null,
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
                'status' => 'pending',
            ]);

            // Store UPS tracking data if ups_shipment_response is provided
            $upsResponse = $request->input('ups_shipment_response');
            if ($upsResponse) {
                try {
                    $shipmentResponse = is_string($upsResponse) ? json_decode($upsResponse, true) : $upsResponse;
                    if ($shipmentResponse && isset($shipmentResponse['ShipmentResults'])) {
                        ShipmentTracking::create([
                            'customer_id' => auth()->guard('customer')->id(),
                            'shipper_id' => $shipperId,
                            'create_shipment_id' => $createShipment->id,
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
                        ]);
                        \Log::info('Shipment tracking stored for shipment: ' . ($shipmentResponse['ShipmentResults']['ShipmentIdentificationNumber'] ?? 'N/A'));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to store shipment tracking: ' . $e->getMessage());
                }
            }

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
            ->with(['invoiceItems'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.view-all-shipments', compact('invoices'));
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
     * Generate a unique AWB number.
     * Format: UWC + YYMMDD + 5-digit serial (resets daily)
     * Example: UWC26060200001
     */
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