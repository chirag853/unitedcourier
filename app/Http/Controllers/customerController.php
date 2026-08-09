<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\ExporterCustomer;
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
use App\Models\KycDraft;
use App\Models\CreateShipment;
use App\Models\ShipmentTracking;
use App\Models\Tracking;
use App\Models\ShipmentLog;
use App\Models\WalletTransaction;
use App\Models\CourierService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class customerController extends Controller
{
    public function login()
    {
        return view('customer.login');
    }

    /**
     * Handle email/password login for customers.
     */
    public function loginWithPassword(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::where('email', $credentials['email'])->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email address.',
            ], 422);
        }

        if (!Hash::check($credentials['password'], $customer->getAuthPassword())) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password. Please try again.',
            ], 422);
        }

        // Block login if the account has been deactivated by an admin
        if (isset($customer->status) && !$customer->status) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support for assistance.',
            ], 403);
        }

        $remember = (bool) $request->boolean('remember');

        auth()->guard('customer')->login($customer, $remember);
        $request->session()->regenerate();

        session([
            'customer_id'   => $customer->id,
            'customer_name' => $customer->first_name . ' ' . $customer->last_name,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Login successful! Redirecting...',
            'redirect' => route('customer.dashboard'),
        ]);
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('customer.forgot-password');
    }

    /**
     * Send a password reset link to the given email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('customer.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset the customer's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                $customer->forceFill([
                    'password_hash' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->setRememberToken(Str::random(60));

                $customer->save();

                event(new PasswordReset($customer));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
    
    public function register()
    {
        $businessCategories = BusinessCategory::active()->ordered()->get();

        // Group categories by parent_group so the registration form can
        // render them inside <optgroup> elements. Categories without a
        // parent_group fall back to an "Others" group.
        $groupedBusinessCategories = $businessCategories->groupBy(function ($category) {
            return $category->parent_group ?: 'Others';
        });

        return view('customer.register', compact('groupedBusinessCategories'));
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
                    'message' => 'This phone number is not registered. Please register yourself first.'
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

    /**
     * Send OTP for registration (does NOT require an existing customer).
     * Prevents duplicate registrations by checking if the phone is already in use.
     */
    public function sendRegistrationOtp(Request $request)
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
            // Prevent duplicate registration: if the phone is already registered, block OTP send
            $existingCustomer = Customer::where('phone_number', $request->phone_number)->first();
            if ($existingCustomer) {
                return response()->json([
                    'success' => false,
                    'message' => 'This phone number is already registered. Please login instead.'
                ], 409);
            }

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store OTP in session with a registration-specific prefix (5 min expiry)
            session([
                'registration_otp' => $otp,
                'registration_phone' => $request->phone_number,
                'registration_otp_expires_at' => now()->addMinutes(5)->timestamp,
                'registration_phone_verified' => false,
            ]);

            // Send OTP via SMS
            $smsSent = $this->sendOtpViaSms($request->phone_number, $otp);

            if (!$smsSent) {
                \Log::warning('Registration SMS sending failed. OTP for ' . $request->phone_number . ': ' . $otp);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your mobile number.'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('sendRegistrationOtp error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP for registration (does NOT log the user in).
     * Marks the phone number as verified in session so store() can proceed.
     */
    public function verifyRegistrationOtp(Request $request)
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
            $sessionOtp = session('registration_otp');
            $sessionPhone = session('registration_phone');
            $expiresAt = session('registration_otp_expires_at');

            if (!$sessionOtp || !$sessionPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'No OTP was requested. Please click "Get OTP" first.'
                ], 400);
            }

            if (now()->timestamp > $expiresAt) {
                session()->forget(['registration_otp', 'registration_phone', 'registration_otp_expires_at', 'registration_phone_verified']);
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new OTP.'
                ], 400);
            }

            if ($sessionPhone !== $request->phone_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number mismatch. Please request a new OTP.'
                ], 400);
            }

            if ((string) $sessionOtp !== $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.'
                ], 400);
            }

            // OTP verified — mark phone as verified, clear OTP value but keep the verified flag
            session()->forget(['registration_otp', 'registration_otp_expires_at']);
            session(['registration_phone_verified' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully! You can now complete your registration.'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('verifyRegistrationOtp error: ' . $e->getMessage());
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

        // Load the customer's business category to determine user_type (Personal / Business)
        $businessCategory = BusinessCategory::find($customer->business_category_id);
        $customer->setRelation('businessCategory', $businessCategory);
        $userType = $businessCategory ? $businessCategory->user_type : 'Personal';
        $isAadhaarOptional = $this->isCourierOrAggregator($customer);
        $kycType = strtolower($userType) === 'business' ? 'business' : 'personal';
        $kycDraft = KycDraft::where('customer_id', $customerId)
            ->where('kyc_type', $kycType)
            ->first();

        return view('customer.dashboard', compact(
            'customer', 'totalBooked', 'pickupPending', 'outForDelivery', 'delivered',
            'recentShipments', 'walletBalance', 'totalShippedValue', 'totalShippedCost',
            'bookedChangePercent', 'pickupPendingChangePercent', 'outForDeliveryChangePercent', 'deliveredChangePercent',
            'userType', 'businessCategory', 'isAadhaarOptional', 'kycDraft'
        ));
    }

    public function saveKycDraft(Request $request)
    {
        $customer = auth()->guard('customer')->user();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to save KYC progress.',
            ], 401);
        }

        $validated = $request->validate([
            'kyc_type' => ['required', Rule::in(['personal', 'business'])],
            'current_step' => ['required', 'integer', 'min:1', 'max:7'],
            'form_data' => ['nullable', 'array'],
        ]);

        $maxStep = $validated['kyc_type'] === 'business' ? 6 : 7;
        if ((int) $validated['current_step'] > $maxStep) {
            return response()->json([
                'success' => false,
                'message' => 'The selected KYC step is invalid.',
            ], 422);
        }

        $allowedFields = [
            'gst_number', 'gst_business_name', 'gst_verified', 'otp_verified', 'aadhar_number',
            'aadhar_verified', 'aadhar_address', 'pan_number', 'pan_holder_name',
            'pan_dob', 'pan_verified', 'organization_name', 'authorized_signatory',
            'billing_address', 'billing_gst', 'billing_contact', 'billing_email',
            'terms_accepted', 'is_csb_v', 'is_gst', 'is_lut',
            'gst_certificate_number', 'gst_certificate_verified', 'iec_number',
            'ad_code', 'lut_expiry_date', 'lut_bond_year', 'bank_account_number',
            'bank_type',
        ];
        $formData = array_intersect_key(
            $validated['form_data'] ?? [],
            array_flip($allowedFields)
        );

        $draft = KycDraft::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'kyc_type' => $validated['kyc_type'],
            ],
            [
                'current_step' => (int) $validated['current_step'],
                'form_data' => $formData,
            ]
        );

        return response()->json([
            'success' => true,
            'saved_at' => $draft->updated_at?->toIso8601String(),
        ]);
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

    /**
     * Show saved customers and the add-customer form for Courier or Aggregator accounts.
     */
    public function exporterCustomers()
    {
        $exporter = auth()->guard('customer')->user();
        if (!$exporter) {
            return redirect()->route('login');
        }

        abort_unless($this->canManageSavedCustomers($exporter), 403, 'Only Courier or Aggregator accounts can manage saved customers.');

        $exporterCustomers = $exporter->exporterCustomers()
            ->with('businessCategory')
            ->orderBy('company_name')
            ->orderBy('contact_person')
            ->get();
        $groupedBusinessCategories = BusinessCategory::active()
            ->ordered()
            ->get()
            ->groupBy(fn (BusinessCategory $category) => $category->parent_group ?: $category->user_type ?: 'Other');

        return view('customer.exporter-customers', compact('exporterCustomers', 'groupedBusinessCategories'));
    }

    /**
     * Store a shipper profile owned by the logged-in Courier or Aggregator account.
     */
    public function storeExporterCustomer(Request $request)
    {
        $exporter = auth()->guard('customer')->user();
        if (!$exporter) {
            return redirect()->route('login');
        }

        abort_unless($this->canManageSavedCustomers($exporter), 403, 'Only Courier or Aggregator accounts can manage saved customers.');

        $selectedBusinessCategory = BusinessCategory::active()
            ->find($request->input('business_category_id'));
        $isBusinessCustomer = $selectedBusinessCategory
            && strcasecmp((string) $selectedBusinessCategory->user_type, 'Business') === 0;

        $request->merge([
            'business_category_id' => $request->filled('business_category_id')
                ? (int) $request->input('business_category_id')
                : null,
            'csb_type' => $isBusinessCustomer ? 'csb_v' : $request->input('csb_type'),
            'company_name' => trim((string) $request->input('company_name')),
            'contact_person' => trim((string) $request->input('contact_person')),
            'email' => strtolower(trim((string) $request->input('email'))),
            'phone_number' => preg_replace('/\D+/', '', (string) $request->input('phone_number')),
            'pincode' => preg_replace('/\D+/', '', (string) $request->input('pincode')),
            'kyc_number' => strtoupper(preg_replace('/\s+/', '', (string) $request->input('kyc_number'))),
            'ad_code' => preg_replace('/\D+/', '', (string) $request->input('ad_code')),
            'iec_number' => strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', (string) $request->input('iec_number'))),
            'bank_account_number' => preg_replace('/\D+/', '', (string) $request->input('bank_account_number')),
            'billing_contact' => preg_replace('/\D+/', '', (string) $request->input('billing_contact')),
            'billing_email' => strtolower(trim((string) $request->input('billing_email'))),
        ]);

        $isCsbV = $request->input('csb_type') === 'csb_v';
        $usesLut = $isCsbV && $request->boolean('is_lut');

        $validated = $request->validate([
            'business_category_id' => [
                'required',
                'integer',
                Rule::exists('business_categories', 'id')->where('status', 'active'),
            ],
            'company_name' => ['required', 'string', 'min:2', 'max:150', 'regex:/^[\pL\pN][\pL\pN\s.&()\'\/-]*$/u'],
            'contact_person' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL][\pL\s.\'-]*$/u'],
            'address_line1' => ['required', 'string', 'min:5', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'address_line3' => ['nullable', 'string', 'max:255'],
            'pincode' => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'city' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL][\pL\s.\'-]*$/u'],
            'state' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL][\pL\s.\'-]*$/u'],
            'phone_number' => [
                'required',
                'regex:/^[6-9][0-9]{9}$/',
                Rule::unique('exporter_customers', 'phone_number')->where('exporter_id', $exporter->id),
            ],
            'email' => [
                'required',
                'email:rfc',
                'max:150',
                Rule::unique('exporter_customers', 'email')->where('exporter_id', $exporter->id),
            ],
            'email_opt_out' => ['sometimes', 'boolean'],
            'kyc_type' => ['nullable', 'required_with:kyc_number', Rule::in(['GST (Normal)', 'Aadhar Card', 'PAN Card', 'Passport Number'])],
            'kyc_number' => [
                'nullable',
                'required_with:kyc_type',
                'max:100',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    $patterns = [
                        'GST (Normal)' => ['/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', 'Enter a valid 15-character GST number.'],
                        'Aadhar Card' => ['/^[2-9][0-9]{11}$/', 'Enter a valid 12-digit Aadhaar number.'],
                        'PAN Card' => ['/^[A-Z]{5}[0-9]{4}[A-Z]$/', 'Enter a valid 10-character PAN number.'],
                        'Passport Number' => ['/^[A-Z][0-9]{7}$/', 'Enter a valid passport number (one letter followed by seven digits).'],
                    ];
                    $kycType = $request->input('kyc_type');
                    $rule = $patterns[$kycType] ?? null;
                    if ($value && $rule && !preg_match($rule[0], (string) $value)) {
                        $fail($rule[1]);
                        return;
                    }

                    if (
                        $value
                        && $kycType === 'Aadhar Card'
                        && \App\Models\ExporterCustomer::query()
                            ->where('kyc_type', 'Aadhar Card')
                            ->where('kyc_number', (string) $value)
                            ->exists()
                    ) {
                        $fail('This Aadhaar number is already registered for another saved customer.');
                    }
                },
            ],
            'csb_type' => ['required', Rule::in(['csb_iv', 'csb_v'])],
            'is_lut' => ['nullable', 'boolean'],
            'ad_code' => [Rule::requiredIf($isCsbV), 'nullable', 'digits:14'],
            'ad_code_document' => [Rule::requiredIf($isCsbV), 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'iec_number' => [Rule::requiredIf($isCsbV), 'nullable', 'regex:/^[A-Z0-9]{10}$/'],
            'iec_document' => [Rule::requiredIf($isCsbV), 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'bank_account_number' => [Rule::requiredIf($isCsbV), 'nullable', 'regex:/^[0-9]{9,18}$/'],
            'bank_type' => [Rule::requiredIf($isCsbV), 'nullable', Rule::in(['private', 'government'])],
            'lut_bond_year' => [Rule::requiredIf($usesLut), 'nullable', 'regex:/^[0-9]{4}-[0-9]{2}$/'],
            'lut_expiry_date' => [Rule::requiredIf($usesLut), 'nullable', 'date', 'after_or_equal:today'],
            'lut_document' => [Rule::requiredIf($usesLut), 'nullable', 'file', 'mimes:pdf', 'max:5120'],
            'billing_address' => [Rule::requiredIf($isCsbV), 'nullable', 'string', 'min:10', 'max:1000'],
            'billing_contact' => [Rule::requiredIf($isCsbV), 'nullable', 'regex:/^[6-9][0-9]{9}$/'],
            'billing_email' => [Rule::requiredIf($isCsbV), 'nullable', 'email:rfc', 'max:255'],
            'merchant_agreement' => [Rule::requiredIf($isCsbV), 'nullable', 'file', 'mimes:pdf', 'max:10240'],
            'terms_accepted' => [Rule::excludeIf(!$isCsbV), 'required', 'accepted'],
        ], [
            'business_category_id.required' => 'Please select a customer type.',
            'business_category_id.exists' => 'The selected customer type is invalid or inactive.',
            'company_name.regex' => 'Company name contains invalid characters.',
            'contact_person.regex' => 'Contact person may contain letters, spaces, dots, apostrophes and hyphens only.',
            'pincode.regex' => 'Enter a valid 6-digit Indian pincode.',
            'city.regex' => 'City may contain letters, spaces, dots, apostrophes and hyphens only.',
            'state.regex' => 'State may contain letters, spaces, dots, apostrophes and hyphens only.',
            'phone_number.regex' => 'Enter a valid 10-digit Indian mobile number starting with 6, 7, 8 or 9.',
            'phone_number.unique' => 'A customer with this phone number already exists.',
            'email.unique' => 'A customer with this email address already exists.',
            'kyc_type.required_with' => 'Select a KYC type when entering a KYC number.',
            'kyc_number.required_with' => 'Enter the KYC number for the selected KYC type.',
            'ad_code.digits' => 'The AD Code must be exactly 14 numeric digits.',
            'iec_number.regex' => 'The IEC Number must be exactly 10 letters or digits.',
            'bank_account_number.regex' => 'The Bank Account Number must contain 9 to 18 digits.',
            'lut_bond_year.regex' => 'The LUT Bond Year must use YYYY-YY format.',
            'billing_contact.regex' => 'The Billing Contact Number must contain exactly 10 digits and start with 6, 7, 8, or 9.',
            'terms_accepted.accepted' => 'You must accept the declaration and terms.',
        ]);

        $validated['email_opt_out'] = $request->boolean('email_opt_out');
        $validated['kyc_number'] = $validated['kyc_number'] ?: null;
        $validated['is_lut'] = $usesLut;
        $validated['terms_accepted'] = $isCsbV && $request->boolean('terms_accepted');
        $validated['merchant_agreement_accepted_at'] = $validated['terms_accepted'] ? now() : null;

        if ($isCsbV && !empty($validated['lut_bond_year'])) {
            [$startYear, $endYearSuffix] = explode('-', $validated['lut_bond_year']);
            $startYear = (int) $startYear;
            $endYear = (intdiv($startYear, 100) * 100) + (int) $endYearSuffix;
            if ($endYear <= $startYear) {
                $endYear += 100;
            }
            if ($endYear < $startYear + 1 || $endYear > $startYear + 5) {
                throw ValidationException::withMessages([
                    'lut_bond_year' => 'The LUT Bond End Year must be within five years after the Start Year.',
                ]);
            }

            $expectedExpiryDate = sprintf('%04d-03-31', $endYear);
            if (($validated['lut_expiry_date'] ?? null) !== $expectedExpiryDate) {
                throw ValidationException::withMessages([
                    'lut_expiry_date' => 'The LUT Expiry Date must be 31 March of the selected LUT Bond End Year.',
                ]);
            }
        }

        if ($isCsbV) {
            $uploadDirectory = public_path('uploads/exporter_customer_csb_documents/' . $exporter->id);
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }

            foreach (['ad_code_document', 'iec_document', 'lut_document', 'merchant_agreement'] as $documentField) {
                if (!$request->hasFile($documentField)) {
                    continue;
                }

                $file = $request->file($documentField);
                $filename = Str::uuid() . '_' . $documentField . '.' . $file->extension();
                $file->move($uploadDirectory, $filename);
                $validated[$documentField] = 'uploads/exporter_customer_csb_documents/' . $exporter->id . '/' . $filename;
            }
        } else {
            $validated = collect($validated)->except([
                'is_lut',
                'ad_code',
                'ad_code_document',
                'iec_number',
                'iec_document',
                'bank_account_number',
                'bank_type',
                'lut_bond_year',
                'lut_expiry_date',
                'lut_document',
                'billing_address',
                'billing_contact',
                'billing_email',
                'merchant_agreement',
                'terms_accepted',
                'merchant_agreement_accepted_at',
            ])->all();
        }

        $exporter->exporterCustomers()->create($validated);

        return redirect()->route('customer.exporter-customers')
            ->with('success', 'Customer saved successfully.');
    }
    
    public function createShipment()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();
        // Only enabled services (status = 1) are offered to the customer.
        $courierServices = \App\Models\CourierService::where('status', 1)->get();
        $zones = \App\Models\Zone::orderBy('zone_name')->get();
        $destinations = \App\Models\Destination::where('is_active', true)->orderBy('name')->get();
        $canCreateShipment = (bool) ($customer->can_create_shipment ?? true);
        $canManageSavedCustomers = $this->canManageSavedCustomers($customer);
        $exporterCustomers = $canManageSavedCustomers
            ? $customer->exporterCustomers()->orderByDesc('id')->get()
            : collect();

        return view('customer.create-shipment', compact(
            'customer',
            'courierServices',
            'zones',
            'destinations',
            'canCreateShipment',
            'canManageSavedCustomers',
            'exporterCustomers'
        ));
    }

    /**
     * AJAX endpoint: return zones for a given destination_id.
     *
     * Checks whether any zone exists in the `zone` table for the supplied
     * destination_id. If zones exist, reads the `zone_category` column to
     * determine whether suggestions should be shown as a state dropdown
     * (zone_category = 'state') or as zipcode suggestions (zone_category =
     * 'zipcode').
     *
     * Response JSON:
     *   {
     *     "exists": bool,
     *     "category": "state" | "zipcode" | null,
     *     "destination": { id, name, code, country_code } | null,
     *     "zones": [ { zone_code, zone_name }, ... ]
     *   }
     */
    public function getZonesByDestination(Request $request)
    {
        $destinationId = $request->query('destination_id');

        if (!$destinationId || !ctype_digit((string) $destinationId)) {
            return response()->json([
                'exists'  => false,
                'category' => null,
                'destination' => null,
                'zones'   => [],
            ], 200);
        }

        $destination = \App\Models\Destination::find((int) $destinationId);

        if (!$destination) {
            return response()->json([
                'exists'  => false,
                'category' => null,
                'destination' => null,
                'zones'   => [],
            ], 200);
        }

        $zones = \App\Models\Zone::where('destination_id', $destination->id)
            ->orderBy('zone_name')
            ->get();

        if ($zones->isEmpty()) {
            return response()->json([
                'exists'  => false,
                'category' => null,
                'destination' => [
                    'id'           => $destination->id,
                    'name'         => $destination->name,
                    'code'         => $destination->code,
                    'country_code' => $destination->country_code,
                ],
                'zones'   => [],
            ], 200);
        }

        // Determine the category from the first zone's zone_category value.
        // All zones for a destination are expected to share the same category.
        $category = $zones->first()->zone_category ?: 'state';

        return response()->json([
            'exists'  => true,
            'category' => $category,
            'destination' => [
                'id'           => $destination->id,
                'name'         => $destination->name,
                'code'         => $destination->code,
                'country_code' => $destination->country_code,
            ],
            'zones'   => $zones->map(function ($z) {
                return [
                    'zone_code' => $z->zone_code,
                    'zone_name' => $z->zone_name,
                ];
            })->values(),
        ], 200);
    }
    
    public function kycSubmit(Request $request)
    {
        try {
            // Normalize boolean-ish fields that arrive as strings via FormData
            foreach (['gst_verified', 'otp_verified', 'aadhar_verified', 'pan_verified', 'terms_accepted'] as $boolField) {
                if ($request->has($boolField)) {
                    $val = $request->input($boolField);
                    if (is_string($val)) {
                        $request->merge([$boolField => in_array(strtolower($val), ['1', 'true', 'yes', 'on'], true)]);
                    }
                }
            }

            // Validate the request (text fields + file fields)
            $validated = $request->validate([
                'gst_number' => 'nullable|string|size:15|regex:/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/',
                'gst_business_name' => 'required_with:gst_number|string|max:255',
                'gst_certificate_document' => 'required_with:gst_number|file|mimes:pdf|max:5120',
                'gst_verified' => 'nullable|boolean',
                'otp_verified' => 'nullable|boolean',
                'aadhar_number' => 'nullable|string|max:20',
                'aadhar_verified' => 'nullable|boolean',
                'aadhar_address' => 'nullable|string|max:1000',
                'pan_number' => 'nullable|string|max:20',
                'pan_holder_name' => 'nullable|string|max:255',
                'pan_dob' => 'nullable|date|before:today',
                'pan_verified' => 'nullable|boolean',
                'organization_name' => 'nullable|string|max:255',
                'authorized_signatory' => 'nullable|string|max:255',
                'billing_address' => 'nullable|string|max:1000',
                'billing_gst' => 'nullable|string|max:15',
                'billing_contact' => 'nullable|string|max:20',
                'billing_email' => 'nullable|string|email|max:255',
                'terms_accepted' => 'nullable|boolean',
                'terms_accepted_at' => 'nullable|date',
                // File uploads
                'aadhar_front_document' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'aadhar_back_document' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'pan_document' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'signature_document' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ], [
                'gst_number.regex' => 'The GST number format is invalid. It must be a valid 15-character GSTIN (e.g. 22AAAAA0000A1Z5).',
                'gst_number.size' => 'The GST number must be exactly 15 characters.',
                'gst_certificate_document.required_with' => 'Upload the GST Certificate PDF when submitting GST details.',
                'gst_certificate_document.mimes' => 'The GST Certificate must be a PDF file only.',
                'gst_certificate_document.max' => 'The GST Certificate PDF must not exceed 5 MB.',
            ]);

            // Get current customer
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to submit your KYC application.'
                ], 401);
            }

            $gstNumber = $request->gst_number
                ? strtoupper(preg_replace('/\s+/', '', $request->gst_number))
                : null;
            $gstBusinessName = trim((string) $request->input('gst_business_name'));

            if ($gstNumber && (
                !$request->boolean('gst_verified')
                || session('kyc_gst_number') !== $gstNumber
                || !session('kyc_gst_cashfree_verified')
                || !hash_equals(
                    (string) session('kyc_gst_business_name', ''),
                    $this->normalizeGstBusinessName($gstBusinessName)
                )
            )) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verify the submitted GSTIN through Cashfree before submitting KYC.',
                ], 422);
            }

            $aadharNumber = $request->aadhar_number
                ? preg_replace('/\s+/', '', $request->aadhar_number)
                : null;
            $panNumber = $request->pan_number
                ? strtoupper(preg_replace('/\s+/', '', $request->pan_number))
                : null;
            $panHolderName = $this->normalizePanHolderName((string) $request->input('pan_holder_name'));
            $panDob = $this->normalizePanDob((string) $request->input('pan_dob'));
            $hasAnyPanData = $panNumber
                || $panHolderName
                || $panDob
                || $request->boolean('pan_verified')
                || $request->hasFile('pan_document');

            if ($hasAnyPanData) {
                $panFile = $request->file('pan_document');
                $panDocumentHash = $panFile
                    ? hash_file('sha256', $panFile->getRealPath())
                    : null;
                if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', (string) $panNumber)
                    || $panHolderName === ''
                    || $panDob === null
                    || !$request->boolean('pan_verified')
                    || !$panFile
                    || !session('kyc_pan_cashfree_verified')
                    || session('kyc_pan_number') !== $panNumber
                    || !hash_equals((string) session('kyc_pan_holder_name', ''), $panHolderName)
                    || !hash_equals((string) session('kyc_pan_dob', ''), $panDob)
                    || !hash_equals(
                        (string) session('kyc_pan_document_hash', ''),
                        (string) $panDocumentHash
                    )) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Verify the submitted PAN number, holder name, date of birth, and selected PAN image through Cashfree before submitting KYC.',
                    ], 422);
                }
            }

            $isAadhaarOptional = $this->isCourierOrAggregator($customer);
            $hasAnyAadhaarData = $aadharNumber
                || $request->boolean('aadhar_verified')
                || $request->hasFile('aadhar_front_document')
                || $request->hasFile('aadhar_back_document');

            if (!$isAadhaarOptional || $hasAnyAadhaarData) {
                $aadharFrontFile = $request->file('aadhar_front_document');
                $aadharFrontHash = $aadharFrontFile
                    ? hash_file('sha256', $aadharFrontFile->getRealPath())
                    : null;
                if (!preg_match('/^[2-9][0-9]{11}$/', (string) $aadharNumber)
                    || !$request->boolean('aadhar_verified')
                    || !$aadharFrontFile
                    || !$request->hasFile('aadhar_back_document')
                    || !session('kyc_aadhar_cashfree_verified')
                    || session('kyc_aadhar_number') !== $aadharNumber
                    || !hash_equals(
                        (string) session('kyc_aadhar_front_hash', ''),
                        (string) $aadharFrontHash
                    )) {
                    return response()->json([
                        'success' => false,
                        'message' => $isAadhaarOptional
                            ? 'Complete Cashfree Aadhaar verification with the selected front image and upload both Aadhaar images, or leave all Aadhaar fields empty.'
                            : 'Cashfree verification of the submitted Aadhaar number and front image, plus both Aadhaar images, is required before submitting KYC.'
                    ], 422);
                }
            }

            if ($identifierConflict = $this->findKycIdentifierConflict($customer, $gstNumber, $aadharNumber, $panNumber)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }

            if ($panNumber && $this->isBusinessCustomer($customer) && $this->isIndividualPan($panNumber)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Individual PAN details cannot be used for a business account. Please enter the business PAN.'
                ], 422);
            }

            // Ensure upload directories exist
            $uploadDirs = [
                'uploads/gst_certificate_documents',
                'uploads/aadhar_front_documents',
                'uploads/aadhar_back_documents',
                'uploads/pan_documents',
                'uploads/signature_documents',
            ];
            foreach ($uploadDirs as $dir) {
                $path = public_path($dir);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
            }

            // Handle GST Certificate upload
            $gstCertificatePath = null;
            if ($request->hasFile('gst_certificate_document')) {
                $file = $request->file('gst_certificate_document');
                $filename = time() . '_gst_certificate_' . Str::uuid()
                    . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/gst_certificate_documents'), $filename);
                $gstCertificatePath = 'uploads/gst_certificate_documents/' . $filename;
            }

            // Handle Aadhaar front document upload
            $aadharFrontPath = null;
            if ($request->hasFile('aadhar_front_document')) {
                $file = $request->file('aadhar_front_document');
                $filename = time() . '_aadhar_front_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aadhar_front_documents'), $filename);
                $aadharFrontPath = 'uploads/aadhar_front_documents/' . $filename;
            }

            // Handle Aadhaar back document upload
            $aadharBackPath = null;
            if ($request->hasFile('aadhar_back_document')) {
                $file = $request->file('aadhar_back_document');
                $filename = time() . '_aadhar_back_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aadhar_back_documents'), $filename);
                $aadharBackPath = 'uploads/aadhar_back_documents/' . $filename;
            }

            // Handle PAN document upload
            $panDocumentPath = null;
            if ($request->hasFile('pan_document')) {
                $file = $request->file('pan_document');
                $filename = time() . '_pan_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pan_documents'), $filename);
                $panDocumentPath = 'uploads/pan_documents/' . $filename;
            }

            // Store the uploaded image and persist only its relative path.
            $file = $request->file('signature_document');
            $filename = time() . '_signature_' . \Illuminate\Support\Str::uuid()
                . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/signature_documents'), $filename);
            $signaturePath = 'uploads/signature_documents/' . $filename;

            // Prepare KYC data
            $kycData = [
                'customer_id' => $customer->id,
                'kyc_type' => 'personal',
                'gst_number' => $gstNumber,
                'gst_certificate_document' => $gstCertificatePath,
                'gst_verified' => $request->gst_verified ?? false,
                'otp_verified' => $request->otp_verified ?? false,
                'aadhar_number' => $aadharNumber,
                'aadhar_verified' => $request->aadhar_verified ?? false,
                'aadhar_address' => $request->aadhar_address,
                'aadhar_front_document' => $aadharFrontPath,
                'aadhar_back_document' => $aadharBackPath,
                'pan_number' => $panNumber,
                'pan_holder_name' => $request->pan_holder_name,
                'pan_dob' => $request->pan_dob,
                'pan_document' => $panDocumentPath,
                'pan_verified' => $request->pan_verified ?? false,
                'signature_document' => $signaturePath,
                'signature' => null,
                'organization_name' => $request->organization_name,
                'authorized_signatory' => $request->authorized_signatory,
                'billing_address' => $request->billing_address,
                'billing_gst' => $request->billing_gst,
                'billing_contact' => $request->billing_contact,
                'billing_email' => $request->billing_email,
                'terms_accepted' => $request->terms_accepted ?? true,
                'terms_accepted_at' => now(),
                'kyc_status' => 'pending', // Set status to under_review after submission
            ];

            // Create KYC record
            $kyc = KycDetail::create($kycData);
            KycDraft::where('customer_id', $customer->id)
                ->where('kyc_type', 'personal')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'KYC application submitted successfully! Your application is now under review.',
                'kyc_id' => $kyc->id
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('KYC submit database error: ' . $e->getMessage());
            if ($e->getCode() === '23000' && ($identifier = $this->databaseKycIdentifierFromException($e))) {
                return $this->kycIdentifierConflictResponse($identifier);
            }

            return response()->json([
                'success' => false,
                'message' => 'KYC submission failed. Please try again.'
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('KYC submit error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'KYC submission failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify GSTIN through Cashfree during either KYC flow.
     */
    public function verifyGst(Request $request)
    {
        try {
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your GST number.',
                ], 401);
            }

            $validated = $request->validate([
                'gst_number' => ['required', 'string', 'size:15'],
                'business_name' => ['required', 'string', 'max:255'],
                'gst_certificate_document' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            ], [
                'gst_certificate_document.required' => 'Upload the GST Certificate PDF before verification.',
                'gst_certificate_document.mimes' => 'The GST Certificate must be a PDF file only.',
                'gst_certificate_document.max' => 'The GST Certificate PDF must not exceed 5 MB.',
            ]);

            $gst = strtoupper(preg_replace('/\s+/', '', $validated['gst_number']));
            if (!preg_match('/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', $gst)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid GST number format. Please enter a valid 15-character GSTIN.',
                ], 422);
            }

            $stateCode = (int) substr($gst, 0, 2);
            if ($stateCode < 1 || $stateCode > 38) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid state code in GST number. State code must be between 01 and 38.',
                ], 422);
            }

            if ($this->computeGstChecksum(substr($gst, 0, 14)) !== substr($gst, 14, 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid GST number. The checksum digit does not match.',
                ], 422);
            }

            if ($identifierConflict = $this->findKycIdentifierConflict($customer, $gst, null, null)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }

            $businessName = trim($validated['business_name']);

            $clientId = config('services.cashfree.verification_client_id');
            $clientSecret = config('services.cashfree.verification_client_secret');
            if (!$clientId || !$clientSecret) {
                \Log::error('Cashfree GST verification credentials are not configured.');
                return response()->json([
                    'success' => false,
                    'message' => 'GST verification is temporarily unavailable.',
                ], 503);
            }

            $cashfreeResponse = Http::acceptJson()
                ->withHeaders([
                    'x-client-id' => $clientId,
                    'x-client-secret' => $clientSecret,
                    'Content-Type' => 'application/json',
                ])
                ->timeout((int) config('services.cashfree.verification_timeout', 30))
                ->post(rtrim(config('services.cashfree.verification_base_url'), '/') . '/gstin', [
                    'GSTIN' => $gst,
                    'business_name' => $businessName,
                ]);

            $cashfreeData = $cashfreeResponse->json();
            if (!$cashfreeResponse->successful()) {
                \Log::warning('Cashfree GST verification rejected.', [
                    'customer_id' => $customer->id,
                    'http_status' => $cashfreeResponse->status(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not verify this GSTIN.',
                ], 422);
            }

            $providerStatus = strtoupper((string) (
                data_get($cashfreeData, 'verification_status')
                ?? data_get($cashfreeData, 'status')
                ?? data_get($cashfreeData, 'status_code')
                ?? ''
            ));
            $providerValid = data_get($cashfreeData, 'valid');
            $providerSuccess = data_get($cashfreeData, 'success');
            if (in_array($providerStatus, ['FAILED', 'FAILURE', 'INVALID', 'ERROR'], true)
                || $providerValid === false
                || $providerSuccess === false) {
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not verify this GSTIN.',
                ], 422);
            }

            $legalName = trim((string) (
                data_get($cashfreeData, 'legal_name_of_business')
                ?? data_get($cashfreeData, 'data.legal_name_of_business')
                ?? data_get($cashfreeData, 'legal_name')
                ?? data_get($cashfreeData, 'data.legal_name')
                ?? ''
            ));
            $tradeName = trim((string) (
                data_get($cashfreeData, 'trade_name_of_business')
                ?? data_get($cashfreeData, 'data.trade_name_of_business')
                ?? data_get($cashfreeData, 'trade_name')
                ?? data_get($cashfreeData, 'data.trade_name')
                ?? ''
            ));
            $normalizedBusinessName = $this->normalizeGstBusinessName($businessName);
            $providerNames = array_values(array_filter([$legalName, $tradeName]));
            $nameMatches = collect($providerNames)->contains(
                fn (string $providerName) => hash_equals(
                    $this->normalizeGstBusinessName($providerName),
                    $normalizedBusinessName
                )
            );

            if (!$providerNames) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree did not return the registered business name for this GSTIN.',
                ], 422);
            }

            if (!$nameMatches) {
                return response()->json([
                    'success' => false,
                    'message' => 'The entered Business Name does not match the name registered for this GSTIN.',
                ], 422);
            }

            session([
                'kyc_gst_number' => $gst,
                'kyc_gst_business_name' => $normalizedBusinessName,
                'kyc_gst_verified' => true,
                'kyc_gst_cashfree_verified' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'GST number and Business Name verified successfully through Cashfree.',
                'gst_number' => $gst,
                'business_name' => $legalName ?: $tradeName,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::warning('Cashfree GST verification connection failed.', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to reach Cashfree for GST verification. Please try again.',
            ], 503);
        } catch (\Throwable $e) {
            \Log::error('GST verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'GST verification failed. Please try again.',
            ], 500);
        }
    }

    private function normalizeGstBusinessName(string $name): string
    {
        $asciiName = Str::ascii($name);

        return strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', $asciiName) ?? '');
    }

    private function normalizePanHolderName(string $name): string
    {
        $asciiName = Str::ascii($name);

        return strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', $asciiName) ?? '');
    }

    private function normalizePanDob(string $dob): ?string
    {
        $dob = trim($dob);
        if ($dob === '') {
            return null;
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y'] as $format) {
            $date = \DateTimeImmutable::createFromFormat('!' . $format, $dob);
            $errors = \DateTimeImmutable::getLastErrors();
            if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * Compute the GSTIN checksum digit using the official algorithm.
     * Based on the GSTN checksum formula.
     *
     * @param string $gst14  The first 14 characters of the GSTIN
     * @return string         The computed checksum character (0-9 or A-Z)
     */
    private function computeGstChecksum(string $gst14): string
    {
        // Official GSTIN checksum (Luhn mod 36):
        //  - Iterate RIGHT-TO-LEFT over the 14 characters
        //  - Factor starts at 2 for the rightmost char, alternating 2,1,2,1,...
        //  - Each char value: 0-9 = 0-9, A-Z = 10-35
        //  - product = value * factor; reduce via base-36 digit sum: floor(product/36) + (product%36)
        //  - checksum = (36 - (sum % 36)) % 36
        $sum = 0;
        $factor = 2;

        for ($i = 13; $i >= 0; $i--) {
            $char = $gst14[$i];
            // Convert character to its numeric value (0-35): 0-9 = 0-9, A-Z = 10-35
            $ascii = ord($char);
            $value = ($ascii >= 48 && $ascii <= 57) ? ($ascii - 48) : ($ascii - 55);

            $product = $value * $factor;
            // Base-36 digit reduction (NOT decimal digit-summing)
            $sum += intdiv($product, 36) + ($product % 36);

            // Alternate factor: 2,1,2,1,...
            $factor = ($factor === 2) ? 1 : 2;
        }

        $remainder = $sum % 36;
        $checksumValue = (36 - $remainder) % 36;

        // Convert back to character: 0-9 = '0'-'9', 10-35 = 'A'-'Z'
        return ($checksumValue < 10)
            ? (string) $checksumValue
            : chr($checksumValue + 55);
    }

    /**
     * Determine whether the customer's selected business category is a business account.
     */
    private function isBusinessCustomer(Customer $customer): bool
    {
        $businessCategory = BusinessCategory::find($customer->business_category_id);

        return $businessCategory && strcasecmp((string) $businessCategory->user_type, 'Business') === 0;
    }

    /**
     * PAN entity code P identifies an individual PAN.
     */
    private function isIndividualPan(string $pan): bool
    {
        return strtoupper(substr($pan, 3, 1)) === 'P';
    }

    /**
     * Check GST, Aadhaar, and PAN across personal and business KYC storage.
     */
    private function findKycIdentifierConflict(Customer $customer, ?string $gst, ?string $aadhar, ?string $pan): ?string
    {
        if ($gst && (KycDetail::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(gst_number, ' ', ''), CHAR(9), ''), CHAR(10), '')) = ?", [$gst])
            ->where('customer_id', '!=', $customer->id)
            ->exists()
            || CsbForm::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(gst_certificate_number, ' ', ''), CHAR(9), ''), CHAR(10), '')) = ?", [$gst])
                ->where('customer_id', '!=', $customer->id)
                ->exists())) {
            return 'gst';
        }

        if ($aadhar && (Customer::whereRaw("REPLACE(REPLACE(REPLACE(aadhar_number, ' ', ''), CHAR(9), ''), CHAR(10), '') = ?", [$aadhar])
            ->where('id', '!=', $customer->id)
            ->exists()
            || KycDetail::whereRaw("REPLACE(REPLACE(REPLACE(aadhar_number, ' ', ''), CHAR(9), ''), CHAR(10), '') = ?", [$aadhar])
                ->where('customer_id', '!=', $customer->id)
                ->exists()
            || CsbForm::whereRaw("REPLACE(REPLACE(REPLACE(aadhar_number, ' ', ''), CHAR(9), ''), CHAR(10), '') = ?", [$aadhar])
                ->where('customer_id', '!=', $customer->id)
                ->exists())) {
            return 'aadhar';
        }

        if ($pan && (Customer::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(pan_number, ' ', ''), CHAR(9), ''), CHAR(10), '')) = ?", [$pan])
            ->where('id', '!=', $customer->id)
            ->exists()
            || KycDetail::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(pan_number, ' ', ''), CHAR(9), ''), CHAR(10), '')) = ?", [$pan])
                ->where('customer_id', '!=', $customer->id)
                ->exists())) {
            return 'pan';
        }

        return null;
    }

    private function databaseKycIdentifierFromException(\Illuminate\Database\QueryException $exception): ?string
    {
        $message = strtolower($exception->getMessage());

        foreach (['gst' => ['gst_number', 'gst_certificate_number'], 'aadhar' => ['aadhar_number'], 'pan' => ['pan_number']] as $identifier => $columns) {
            foreach ($columns as $column) {
                if (str_contains($message, $column)) {
                    return $identifier;
                }
            }
        }

        return null;
    }

    private function kycIdentifierConflictResponse(string $identifier)
    {
        $messages = [
            'gst' => 'This GST number is already registered with another account.',
            'aadhar' => 'This Aadhaar number is already registered with another account.',
            'pan' => 'This PAN number is already registered with another account.',
        ];

        return response()->json([
            'success' => false,
            'message' => $messages[$identifier] ?? 'This KYC number is already registered with another account.',
        ], 409);
    }

    /**
     * Verify an Aadhaar front image through Cashfree Bharat OCR during KYC.
     */
    public function verifyAadhar(Request $request)
    {
        try {
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your Aadhaar.',
                ], 401);
            }

            session()->forget([
                'kyc_aadhar_number',
                'kyc_aadhar_verified',
                'kyc_aadhar_cashfree_verified',
                'kyc_aadhar_front_hash',
                'kyc_aadhar_verification_id',
            ]);

            $validated = $request->validate([
                'aadhar_number' => ['required', 'string', 'regex:/^[2-9][0-9]{11}$/'],
                'aadhar_front_document' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ], [
                'aadhar_front_document.required' => 'Upload the Aadhaar front image before verification.',
                'aadhar_front_document.mimes' => 'The Aadhaar front document must be a JPG, JPEG, or PNG image.',
                'aadhar_front_document.max' => 'The Aadhaar front image must not exceed 5 MB.',
            ]);

            $aadhar = preg_replace('/\s+/', '', $validated['aadhar_number']);
            if ($identifierConflict = $this->findKycIdentifierConflict($customer, null, $aadhar, null)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }

            $clientId = config('services.cashfree.verification_client_id');
            $clientSecret = config('services.cashfree.verification_client_secret');
            if (!$clientId || !$clientSecret) {
                \Log::error('Cashfree Aadhaar OCR credentials are not configured.');
                return response()->json([
                    'success' => false,
                    'message' => 'Aadhaar verification is temporarily unavailable.',
                ], 503);
            }

            $frontFile = $request->file('aadhar_front_document');
            $verificationId = (string) random_int(1000, 9999);
            $fileStream = fopen($frontFile->getRealPath(), 'r');
            if ($fileStream === false) {
                throw new \RuntimeException('Unable to read the uploaded Aadhaar front image.');
            }

            try {
                $cashfreeResponse = Http::acceptJson()
                    ->withHeaders([
                        'x-client-id' => $clientId,
                        'x-client-secret' => $clientSecret,
                        'x-api-version' => '2024-12-01',
                    ])
                    ->attach('file', $fileStream, $frontFile->getClientOriginalName())
                    ->timeout((int) config('services.cashfree.verification_timeout', 30))
                    ->post(rtrim(config('services.cashfree.verification_base_url'), '/') . '/bharat-ocr', [
                        'verification_id' => $verificationId,
                        'document_type' => 'AADHAAR',
                        'do_verification' => 'false',
                    ]);
            } finally {
                fclose($fileStream);
            }

            $cashfreeData = $cashfreeResponse->json();
            if (!$cashfreeResponse->successful()) {
                \Log::warning('Cashfree Aadhaar OCR rejected.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                    'http_status' => $cashfreeResponse->status(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this Aadhaar image.',
                ], 422);
            }

            $providerStatus = strtoupper((string) (
                data_get($cashfreeData, 'verification_status')
                ?? data_get($cashfreeData, 'status')
                ?? data_get($cashfreeData, 'status_code')
                ?? ''
            ));
            if (in_array($providerStatus, ['FAILED', 'FAILURE', 'INVALID', 'ERROR'], true)
                || data_get($cashfreeData, 'success') === false) {
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this Aadhaar image.',
                ], 422);
            }

            $ocrAadhaar = preg_replace(
                '/\D+/',
                '',
                (string) data_get($cashfreeData, 'document_fields.uid', '')
            );

            if (!preg_match('/^[2-9][0-9]{11}$/', $ocrAadhaar)) {
                \Log::warning('Cashfree Aadhaar OCR response did not contain a valid document_fields.uid.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree could not read a valid Aadhaar number from the uploaded image.',
                ], 422);
            }

            if (!hash_equals($aadhar, $ocrAadhaar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The Aadhaar number entered does not match the UID read from the uploaded Aadhaar image.',
                ], 422);
            }

            $frontHash = hash_file('sha256', $frontFile->getRealPath());
            session([
                'kyc_aadhar_number' => $aadhar,
                'kyc_aadhar_verified' => true,
                'kyc_aadhar_cashfree_verified' => true,
                'kyc_aadhar_front_hash' => $frontHash,
                'kyc_aadhar_verification_id' => $verificationId,
            ]);

            $customer->aadhar_number = $aadhar;
            $customer->aadhar_verified = true;
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Aadhaar document verified successfully through Cashfree!',
                'aadhar_number' => $aadhar,
                'verification_id' => $verificationId,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Aadhaar verification database error: ' . $e->getMessage());
            if ($e->getCode() === '23000' && str_contains(strtolower($e->getMessage()), 'aadhar_number')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This Aadhaar number is already registered with another account.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Aadhaar verification failed. Please try again.',
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('Aadhaar verification error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Aadhaar verification failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify a PAN number and its image through Cashfree Bharat OCR.
     */
    public function verifyPan(Request $request)
    {
        try {
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your PAN.',
                ], 401);
            }

            session()->forget([
                'kyc_pan_number',
                'kyc_pan_holder_name',
                'kyc_pan_dob',
                'kyc_pan_verified',
                'kyc_pan_cashfree_verified',
                'kyc_pan_document_hash',
                'kyc_pan_verification_id',
            ]);

            $validated = $request->validate([
                'pan_number' => ['required', 'string', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/'],
                'pan_holder_name' => ['required', 'string', 'max:255'],
                'pan_dob' => ['required', 'date', 'before:today'],
                'pan_document' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ], [
                'pan_document.required' => 'Upload the PAN image before verification.',
                'pan_document.mimes' => 'The PAN document must be a JPG, JPEG, or PNG image.',
                'pan_document.max' => 'The PAN image must not exceed 5 MB.',
            ]);

            $pan = strtoupper(preg_replace('/[^A-Z0-9]+/i', '', $validated['pan_number']));
            $panHolderName = $this->normalizePanHolderName($validated['pan_holder_name']);
            $panDob = $this->normalizePanDob($validated['pan_dob']);
            if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PAN number. It must be 10 characters: 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).',
                ], 422);
            }

            if ($this->isBusinessCustomer($customer) && $this->isIndividualPan($pan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Individual PAN details cannot be used for a business account. Please enter the business PAN.',
                ], 422);
            }

            if ($identifierConflict = $this->findKycIdentifierConflict($customer, null, null, $pan)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }

            $clientId = config('services.cashfree.verification_client_id');
            $clientSecret = config('services.cashfree.verification_client_secret');
            if (!$clientId || !$clientSecret) {
                \Log::error('Cashfree PAN OCR credentials are not configured.');
                return response()->json([
                    'success' => false,
                    'message' => 'PAN verification is temporarily unavailable.',
                ], 503);
            }

            $panFile = $validated['pan_document'];
            $fileStream = fopen($panFile->getRealPath(), 'r');
            if ($fileStream === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'The PAN image could not be read. Please upload it again.',
                ], 422);
            }

            $verificationId = (string) random_int(1000, 9999);
            try {
                $cashfreeResponse = Http::acceptJson()
                    ->withHeaders([
                        'x-client-id' => $clientId,
                        'x-client-secret' => $clientSecret,
                        'x-api-version' => '2024-12-01',
                    ])
                    ->attach('file', $fileStream, $panFile->getClientOriginalName())
                    ->timeout((int) config('services.cashfree.verification_timeout', 30))
                    ->post(rtrim(config('services.cashfree.verification_base_url'), '/') . '/bharat-ocr', [
                        'verification_id' => $verificationId,
                        'document_type' => 'PAN',
                        'do_verification' => 'false',
                    ]);
            } finally {
                fclose($fileStream);
            }

            $cashfreeData = $cashfreeResponse->json();
            if (!$cashfreeResponse->successful()) {
                \Log::warning('Cashfree PAN OCR rejected.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                    'http_status' => $cashfreeResponse->status(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this PAN image.',
                ], 422);
            }

            $providerStatus = strtoupper((string) (
                data_get($cashfreeData, 'verification_status')
                ?? data_get($cashfreeData, 'status')
                ?? data_get($cashfreeData, 'status_code')
                ?? ''
            ));
            if (in_array($providerStatus, ['FAILED', 'FAILURE', 'INVALID', 'ERROR'], true)
                || data_get($cashfreeData, 'success') === false) {
                return response()->json([
                    'success' => false,
                    'message' => data_get($cashfreeData, 'message')
                        ?: 'Cashfree could not read this PAN image.',
                ], 422);
            }

            $ocrPan = strtoupper(preg_replace(
                '/[^A-Z0-9]+/',
                '',
                (string) data_get($cashfreeData, 'document_fields.pan', '')
            ));
            $ocrPanHolderNameValue = collect([
                data_get($cashfreeData, 'document_fields.name'),
                data_get($cashfreeData, 'document_fields.pan_name'),
                data_get($cashfreeData, 'document_fields.full_name'),
            ])->first(fn ($value) => is_scalar($value) && trim((string) $value) !== '');
            $ocrPanDobValue = collect([
                data_get($cashfreeData, 'document_fields.dob'),
                data_get($cashfreeData, 'document_fields.date_of_birth'),
            ])->first(fn ($value) => is_scalar($value) && trim((string) $value) !== '');
            $ocrPanHolderName = $this->normalizePanHolderName((string) $ocrPanHolderNameValue);
            $ocrPanDob = $this->normalizePanDob((string) $ocrPanDobValue);
            if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $ocrPan)) {
                \Log::warning('Cashfree PAN OCR response did not contain a valid document_fields.pan.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree could not read a valid PAN number from the uploaded image.',
                ], 422);
            }

            if (!hash_equals($pan, $ocrPan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The PAN number entered does not match the PAN read from the uploaded image.',
                ], 422);
            }

            if ($ocrPanHolderName === '') {
                \Log::warning('Cashfree PAN OCR response did not contain a holder name.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree could not read the holder name from the uploaded PAN image.',
                ], 422);
            }

            if (!hash_equals($panHolderName, $ocrPanHolderName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The PAN holder name entered does not match the name read from the uploaded PAN image.',
                ], 422);
            }

            if ($ocrPanDob === null) {
                \Log::warning('Cashfree PAN OCR response did not contain a valid date of birth.', [
                    'customer_id' => $customer->id,
                    'verification_id' => $verificationId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cashfree could not read a valid date of birth from the uploaded PAN image.',
                ], 422);
            }

            if ($panDob === null || !hash_equals($panDob, $ocrPanDob)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The PAN date of birth entered does not match the date read from the uploaded PAN image.',
                ], 422);
            }

            $documentHash = hash_file('sha256', $panFile->getRealPath());
            session([
                'kyc_pan_number' => $pan,
                'kyc_pan_holder_name' => $panHolderName,
                'kyc_pan_dob' => $panDob,
                'kyc_pan_verified' => true,
                'kyc_pan_cashfree_verified' => true,
                'kyc_pan_document_hash' => $documentHash,
                'kyc_pan_verification_id' => $verificationId,
            ]);

            if (\Schema::hasColumn('customers', 'pan_number')) {
                $customer->pan_number = $pan;
                $customer->pan_verified = true;
                $customer->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'PAN document verified successfully through Cashfree!',
                'pan_number' => $pan,
                'verification_id' => $verificationId,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('PAN verification database error: ' . $e->getMessage());
            if ($e->getCode() === '23000' && str_contains(strtolower($e->getMessage()), 'pan_number')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This PAN number is already registered with another account.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'PAN verification failed. Please try again.',
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('PAN verification error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'PAN verification failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Show the Personal KYC (CSB-IV) form.
     */
    public function personalKyc()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();

        // Fetch existing KYC detail (if any) to pre-fill the form
        $kycDetail = KycDetail::where('customer_id', $customer->id)
            ->where('kyc_type', 'personal')
            ->latest()
            ->first();

        // Load the customer's business category to determine user_type (Personal / Business)
        $businessCategory = BusinessCategory::find($customer->business_category_id);
        $userType = $businessCategory ? $businessCategory->user_type : 'Personal';

        return view('customer.kyc-personal', compact('customer', 'kycDetail', 'userType', 'businessCategory'));
    }

    /**
     * Store the Personal KYC (CSB-IV) submission.
     * Handles Aadhaar (front/back), PAN, signature, billing details, and merchant agreement.
     */
    public function storePersonalKyc(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'aadhar_number' => 'required|string|size:12',
                'aadhar_front_document' => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'aadhar_back_document' => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'aadhar_address' => 'required|string|max:1000',
                'pan_number' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
                'pan_holder_name' => 'required|string|max:255',
                'pan_dob' => 'required|date|before:today',
                'pan_document' => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'signature_document' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'billing_address' => 'required|string|max:1000',
                'billing_contact' => 'required|string|max:20',
                'billing_email' => 'required|email|max:255',
                'merchant_agreement' => 'required|file|mimes:pdf|max:10240',
                'terms_accepted' => 'required|boolean',
            ], [
                'pan_number.regex' => 'The PAN number format is invalid. It must be 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).',
                'pan_number.size' => 'The PAN number must be exactly 10 characters.',
                'aadhar_number.size' => 'The Aadhaar number must be exactly 12 digits.',
                'pan_dob.before' => 'The date of birth must be a valid date before today.',
            ]);

            // Get current customer
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to submit personal KYC.'
                ], 401);
            }

            // Basic Aadhaar format validation: 12 digits, not starting with 0 or 1
            $aadhar = preg_replace('/\s+/', '', $request->aadhar_number);
            if (!preg_match('/^[2-9][0-9]{11}$/', $aadhar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Aadhaar number. It must be 12 digits and cannot start with 0 or 1.'
                ], 422);
            }

            $panNumber = strtoupper(preg_replace('/\s+/', '', $validated['pan_number']));
            $panHolderName = $this->normalizePanHolderName($validated['pan_holder_name']);
            $panDob = $this->normalizePanDob($validated['pan_dob']);
            $panFile = $request->file('pan_document');
            $panDocumentHash = hash_file('sha256', $panFile->getRealPath());

            if (!session('kyc_pan_cashfree_verified')
                || session('kyc_pan_number') !== $panNumber
                || !hash_equals((string) session('kyc_pan_holder_name', ''), $panHolderName)
                || $panDob === null
                || !hash_equals((string) session('kyc_pan_dob', ''), $panDob)
                || !hash_equals(
                    (string) session('kyc_pan_document_hash', ''),
                    (string) $panDocumentHash
                )) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verify the submitted PAN number, holder name, date of birth, and selected PAN image through Cashfree before submitting Personal KYC.',
                ], 422);
            }

            if ($identifierConflict = $this->findKycIdentifierConflict($customer, null, $aadhar, $panNumber)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }
            if ($this->isBusinessCustomer($customer) && $this->isIndividualPan($panNumber)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Individual PAN details cannot be used for a business account. Please enter the business PAN.'
                ], 422);
            }

            // Ensure upload directories exist
            $uploadDirs = [
                'uploads/aadhar_front_documents',
                'uploads/aadhar_back_documents',
                'uploads/pan_documents',
                'uploads/signature_documents',
                'uploads/merchant_agreements',
            ];
            foreach ($uploadDirs as $dir) {
                $path = public_path($dir);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
            }

            // Handle Aadhaar front document upload
            $aadharFrontPath = null;
            if ($request->hasFile('aadhar_front_document')) {
                $file = $request->file('aadhar_front_document');
                $filename = time() . '_aadhar_front_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aadhar_front_documents'), $filename);
                $aadharFrontPath = 'uploads/aadhar_front_documents/' . $filename;
            }

            // Handle Aadhaar back document upload
            $aadharBackPath = null;
            if ($request->hasFile('aadhar_back_document')) {
                $file = $request->file('aadhar_back_document');
                $filename = time() . '_aadhar_back_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aadhar_back_documents'), $filename);
                $aadharBackPath = 'uploads/aadhar_back_documents/' . $filename;
            }

            // Handle PAN document upload
            $panDocumentPath = null;
            if ($request->hasFile('pan_document')) {
                $file = $request->file('pan_document');
                $filename = time() . '_pan_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pan_documents'), $filename);
                $panDocumentPath = 'uploads/pan_documents/' . $filename;
            }

            // Store the uploaded image and persist only its relative path.
            $file = $request->file('signature_document');
            $filename = time() . '_signature_' . \Illuminate\Support\Str::uuid()
                . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/signature_documents'), $filename);
            $signaturePath = 'uploads/signature_documents/' . $filename;

            // Handle merchant agreement document upload
            $merchantAgreementPath = null;
            if ($request->hasFile('merchant_agreement')) {
                $file = $request->file('merchant_agreement');
                $filename = time() . '_merchant_agreement_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/merchant_agreements'), $filename);
                $merchantAgreementPath = 'uploads/merchant_agreements/' . $filename;
            }

            // Create or update KYC Detail record (Personal KYC = CSB-IV)
            $kycDetail = KycDetail::where('customer_id', $customer->id)
                ->where('kyc_type', 'personal')
                ->latest()
                ->first();

            $kycData = [
                'customer_id' => $customer->id,
                'kyc_type' => 'personal',
                'aadhar_number' => $aadhar,
                'aadhar_verified' => true,
                'aadhar_front_document' => $aadharFrontPath,
                'aadhar_back_document' => $aadharBackPath,
                'aadhar_address' => $validated['aadhar_address'],
                'pan_number' => $panNumber,
                'pan_holder_name' => $validated['pan_holder_name'],
                'pan_dob' => $validated['pan_dob'],
                'pan_document' => $panDocumentPath,
                'pan_verified' => true,
                'signature_document' => $signaturePath,
                'signature' => null,
                'billing_address' => $validated['billing_address'],
                'billing_contact' => $validated['billing_contact'],
                'billing_email' => $validated['billing_email'],
                'merchant_agreement' => $merchantAgreementPath,
                'merchant_agreement_accepted_at' => $validated['terms_accepted'] ? now() : null,
                'terms_accepted' => $validated['terms_accepted'],
                'terms_accepted_at' => $validated['terms_accepted'] ? now() : null,
                'kyc_status' => 'under_review',
            ];

            if ($kycDetail) {
                $kycDetail->update($kycData);
            } else {
                $kycDetail = KycDetail::create($kycData);
            }

            // Update customer record with Aadhaar and PAN
            $customer->aadhar_number = $aadhar;
            $customer->aadhar_verified = true;
            $customer->pan_number = $panNumber;
            $customer->pan_verified = true;
            // Personal KYC = CSB-IV (status 1)
            $customer->csb_status = 1;
            $customer->save();
            KycDraft::where('customer_id', $customer->id)
                ->where('kyc_type', 'personal')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Personal KYC (CSB-IV) submitted successfully! Your application is now under review.',
                'redirect' => route('customer.kyc.summary')
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Personal KYC database error: ' . $e->getMessage());
            if ($e->getCode() === '23000' && ($identifier = $this->databaseKycIdentifierFromException($e))) {
                return $this->kycIdentifierConflictResponse($identifier);
            }

            return response()->json([
                'success' => false,
                'message' => 'Personal KYC submission failed. Please try again.'
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('Personal KYC submission error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Personal KYC submission failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the KYC Summary page.
     * Displays both Personal KYC (KycDetail) and Business KYC (CsbForm) details.
     */
    public function kycSummary()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();

        // Fetch Personal KYC detail (if any)
        $personalKyc = KycDetail::where('customer_id', $customer->id)
            ->where('kyc_type', 'personal')
            ->latest()
            ->first();

        // Fetch Business KYC / CSB form (if any)
        $businessKyc = CsbForm::where('customer_id', $customer->id)
            ->latest()
            ->first();

        // Load the customer's business category to determine user_type (Personal / Business)
        $businessCategory = BusinessCategory::find($customer->business_category_id);
        $userType = $businessCategory ? $businessCategory->user_type : 'Personal';

        return view('customer.kyc-summary', compact('customer', 'personalKyc', 'businessKyc', 'userType', 'businessCategory'));
    }

    public function csb5Form()
    {
        // Check if customer is logged in using auth guard
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();

        // Fetch existing CSB form (if any) to pre-fill the form
        $csbForm = CsbForm::where('customer_id', $customer->id)->latest()->first();

        return view('customer.csb5-form', compact('customer', 'csbForm'));
    }

    public function storeCsb5Form(Request $request)
    {
        try {
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to submit the CSB form.'
                ], 401);
            }

            $existingCsbForm = CsbForm::where('customer_id', $customer->id)->latest()->first();
            $existingBusinessKyc = KycDetail::where('customer_id', $customer->id)
                ->where('kyc_type', 'business')
                ->latest()
                ->first();

            // Existing documents remain valid while editing KYC. A replacement file is
            // required only when the customer has not uploaded that document previously.
            $validated = $request->validate([
                'is_csb_v' => 'required|boolean',
                'gst_business_name' => ['required', 'string', 'max:255'],
                'is_gst' => 'required|boolean',
                'is_lut' => 'required|boolean',
                'gst_certificate_number' => ['required', 'string', 'size:15'],
                'gst_certificate_document' => [
                    \Illuminate\Validation\Rule::requiredIf(
                        !$existingCsbForm?->gst_certificate_document
                        && !$existingCsbForm?->gst_document
                    ),
                    'nullable', 'file', 'mimes:pdf', 'max:5120'
                ],
                'gst_document' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
                'aadhar_front_document' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
                'aadhar_back_document' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
                'aadhar_document' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
                'pan_number' => ['required', 'string', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
                'pan_holder_name' => ['required', 'string', 'max:255'],
                'pan_dob' => ['required', 'date', 'before:today'],
                'pan_document' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
                'ad_code' => ['required', 'digits:14'],
                'ad_code_document' => [
                    \Illuminate\Validation\Rule::requiredIf(!$existingCsbForm?->ad_code_document),
                    'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'
                ],
                'iec_number' => ['required', 'string', 'regex:/^[A-Za-z0-9]{10}$/'],
                'iec_document' => [
                    \Illuminate\Validation\Rule::requiredIf(!$existingCsbForm?->iec_document),
                    'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'
                ],
                'bank_account_number' => ['required', 'regex:/^[0-9]{9,18}$/'],
                'bank_type' => 'required|in:private,government',
                'lut_document' => [
                    \Illuminate\Validation\Rule::requiredIf($request->boolean('is_lut') && !$existingCsbForm?->lut_document),
                    'nullable', 'file', 'mimes:pdf', 'max:5120'
                ],
                'lut_expiry_date' => 'required_if:is_lut,1|nullable|date|after_or_equal:today',
                'lut_bond_year' => ['required_if:is_lut,1', 'nullable', 'regex:/^[0-9]{4}-[0-9]{2}$/'],
                'billing_address' => 'required|string|min:10|max:1000',
                'billing_contact' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
                'billing_email' => 'required|email|max:255',
                'signature_document' => [
                    \Illuminate\Validation\Rule::requiredIf(
                        !$existingCsbForm?->signature_document
                        && !$existingBusinessKyc?->signature_document
                    ),
                    'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'
                ],
                'merchant_agreement' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
                'terms_accepted' => 'accepted',
            ], [
                'gst_certificate_number.size' => 'The GST Certificate Number must contain exactly 15 characters.',
                'pan_number.regex' => 'The PAN number must use the valid format ABCDE1234F.',
                'pan_dob.before' => 'The PAN date of birth must be before today.',
                'ad_code.digits' => 'The AD Code must be exactly 14 numeric digits.',
                'iec_number.regex' => 'The IEC Number must be exactly 10 letters or digits.',
                'bank_account_number.regex' => 'The Bank Account Number must contain 9 to 18 digits.',
                'lut_expiry_date.after_or_equal' => 'The LUT Expiry Date cannot be in the past.',
                'lut_bond_year.regex' => 'The LUT Bond Year must use YYYY-YY format.',
                'billing_contact.regex' => 'The Billing Contact Number must contain exactly 10 digits and start with 6, 7, 8, or 9.',
                'terms_accepted.accepted' => 'You must accept the declaration and terms.',
            ]);

            $gstNumber = strtoupper(preg_replace(
                '/\s+/',
                '',
                $validated['gst_certificate_number']
            ));

            if (
                session('kyc_gst_number') !== $gstNumber
                || !session('kyc_gst_cashfree_verified')
                || !hash_equals(
                    (string) session('kyc_gst_business_name', ''),
                    $this->normalizeGstBusinessName($validated['gst_business_name'])
                )
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verify the submitted GSTIN through Cashfree before submitting the CSB-V form.',
                ], 422);
            }

            if (!empty($validated['lut_bond_year'])) {
                [$startYear, $endYearSuffix] = explode('-', $validated['lut_bond_year']);
                $startYear = (int) $startYear;
                $endYear = (intdiv($startYear, 100) * 100) + (int) $endYearSuffix;
                if ($endYear <= $startYear) {
                    $endYear += 100;
                }

                if ($endYear < $startYear + 1 || $endYear > $startYear + 5) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The LUT Bond End Year must be within five years after the Start Year.'
                    ], 422);
                }

                // The expiry date only needs to be current or future, as enforced
                // by the validator above. Do not force it into the bond end year.
            }

            // Aadhaar is optional only for Courier / Aggregator customers. Use
            // the value submitted with this form so an old incomplete customer
            // value does not prevent the customer from intentionally skipping it.
            $isAadhaarOptional = $this->isCourierOrAggregator($customer);
            $submittedAadhaar = preg_replace('/\s+/', '', (string) $request->input('aadhar_number'));
            $storedAadhaar = preg_replace('/\s+/', '', (string) $customer->aadhar_number);
            $aadhar = $isAadhaarOptional
                ? $submittedAadhaar
                : ($submittedAadhaar !== '' ? $submittedAadhaar : $storedAadhaar);

            $isValidVerifiedAadhaar = $aadhar !== ''
                && (bool) $customer->aadhar_verified
                && hash_equals($storedAadhaar, $aadhar)
                && preg_match('/^[2-9][0-9]{11}$/', $aadhar);

            if ($aadhar !== '' && !$isValidVerifiedAadhaar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complete Aadhaar verification or clear the Aadhaar number before submitting the CSB-V form.'
                ], 422);
            }

            if (!$isAadhaarOptional && !$isValidVerifiedAadhaar) {
                return response()->json([
                    'success' => false,
                    'message' => 'A verified Aadhaar is required before submitting the CSB-V form.'
                ], 422);
            }

            $aadhar = $isValidVerifiedAadhaar ? $aadhar : null;
            $aadharVerified = $isValidVerifiedAadhaar;

            if ($aadharVerified) {
                $hasAadhaarFront = $request->hasFile('aadhar_front_document')
                    || $request->hasFile('aadhar_document')
                    || !empty($existingBusinessKyc?->aadhar_front_document)
                    || !empty($existingCsbForm?->aadhar_document);
                $hasAadhaarBack = $request->hasFile('aadhar_back_document')
                    || !empty($existingBusinessKyc?->aadhar_back_document);

                if (!$hasAadhaarFront || !$hasAadhaarBack) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'aadhar_front_document' => 'Upload both the front and back Aadhaar documents.',
                    ]);
                }
            }

            $panNumber = strtoupper(preg_replace('/\s+/', '', $validated['pan_number']));
            $panHolderName = $this->normalizePanHolderName($validated['pan_holder_name']);
            $panDob = $this->normalizePanDob($validated['pan_dob']);
            $panFile = $request->file('pan_document');
            $panDocumentHash = hash_file('sha256', $panFile->getRealPath());
            if (!session('kyc_pan_cashfree_verified')
                || session('kyc_pan_number') !== $panNumber
                || !hash_equals((string) session('kyc_pan_holder_name', ''), $panHolderName)
                || $panDob === null
                || !hash_equals((string) session('kyc_pan_dob', ''), $panDob)
                || !hash_equals(
                    (string) session('kyc_pan_document_hash', ''),
                    (string) $panDocumentHash
                )) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'pan_number' => 'Verify the submitted PAN number, holder name, date of birth, and selected PAN image through Cashfree before completing Business KYC.',
                ]);
            }

            if ($identifierConflict = $this->findKycIdentifierConflict($customer, null, $aadhar, $panNumber)) {
                return $this->kycIdentifierConflictResponse($identifierConflict);
            }

            // Ensure upload directories exist
            $uploadDirs = [
                'uploads/gst_documents',
                'uploads/aadhar_front_documents',
                'uploads/aadhar_back_documents',
                'uploads/pan_documents',
                'uploads/lut_documents',
                'uploads/iec_documents',
                'uploads/ad_code_documents',
                'uploads/signature_documents',
                'uploads/merchant_agreements',
            ];
            foreach ($uploadDirs as $dir) {
                $path = public_path($dir);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
            }

            // Store identity and registration documents selected in steps 1-3.
            $gstDocumentPath = null;
            $gstFile = $request->file('gst_certificate_document') ?? $request->file('gst_document');
            if ($gstFile) {
                $filename = time() . '_gst_' . \Illuminate\Support\Str::uuid() . '.' . $gstFile->getClientOriginalExtension();
                $gstFile->move(public_path('uploads/gst_documents'), $filename);
                $gstDocumentPath = 'uploads/gst_documents/' . $filename;
            }

            $aadharFrontPath = null;
            $aadharFrontFile = $request->file('aadhar_front_document') ?? $request->file('aadhar_document');
            if ($aadharFrontFile) {
                $filename = time() . '_aadhar_front_' . \Illuminate\Support\Str::uuid() . '.' . $aadharFrontFile->getClientOriginalExtension();
                $aadharFrontFile->move(public_path('uploads/aadhar_front_documents'), $filename);
                $aadharFrontPath = 'uploads/aadhar_front_documents/' . $filename;
            }

            $aadharBackPath = null;
            if ($request->hasFile('aadhar_back_document')) {
                $file = $request->file('aadhar_back_document');
                $filename = time() . '_aadhar_back_' . \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/aadhar_back_documents'), $filename);
                $aadharBackPath = 'uploads/aadhar_back_documents/' . $filename;
            }

            $panDocumentPath = null;
            if ($request->hasFile('pan_document')) {
                $file = $request->file('pan_document');
                $filename = time() . '_pan_' . \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/pan_documents'), $filename);
                $panDocumentPath = 'uploads/pan_documents/' . $filename;
            }

            // Handle LUT document upload
            $lutDocumentPath = null;
            if ($request->hasFile('lut_document')) {
                $file = $request->file('lut_document');
                $filename = time() . '_lut_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/lut_documents'), $filename);
                $lutDocumentPath = 'uploads/lut_documents/' . $filename;
            }

            // Handle IEC document upload
            $iecDocumentPath = null;
            if ($request->hasFile('iec_document')) {
                $file = $request->file('iec_document');
                $filename = time() . '_iec_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/iec_documents'), $filename);
                $iecDocumentPath = 'uploads/iec_documents/' . $filename;
            }

            // Handle AD Code document upload
            $adCodeDocumentPath = null;
            if ($request->hasFile('ad_code_document')) {
                $file = $request->file('ad_code_document');
                $filename = time() . '_adcode_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/ad_code_documents'), $filename);
                $adCodeDocumentPath = 'uploads/ad_code_documents/' . $filename;
            }

            // Handle merchant agreement document upload
            $merchantAgreementPath = null;
            if ($request->hasFile('merchant_agreement')) {
                $file = $request->file('merchant_agreement');
                $filename = time() . '_merchant_agreement_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/merchant_agreements'), $filename);
                $merchantAgreementPath = 'uploads/merchant_agreements/' . $filename;
            }

            // Store the multipart image and persist only its relative path.
            $signaturePath = null;
            if ($request->hasFile('signature_document')) {
                $file = $request->file('signature_document');
                $filename = time() . '_signature_' . \Illuminate\Support\Str::uuid()
                    . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/signature_documents'), $filename);
                $signaturePath = 'uploads/signature_documents/' . $filename;
            }

            // Create or update CSB Form record
            $csbData = [
                'customer_id' => $customer->id,
                'is_csb_v' => $validated['is_csb_v'],
                'is_gst' => $validated['is_gst'],
                'is_lut' => $validated['is_lut'],
                'gst_certificate_number' => $gstNumber,
                'gst_certificate_document' => $gstDocumentPath
                    ?? ($existingCsbForm->gst_certificate_document ?? null)
                    ?? ($existingCsbForm->gst_document ?? null),
                'gst_document' => $gstDocumentPath
                    ?? ($existingCsbForm->gst_document ?? null)
                    ?? ($existingCsbForm->gst_certificate_document ?? null),
                'lut_verified' => false,
                'ad_code' => $validated['ad_code'],
                'ad_code_document' => $adCodeDocumentPath ?? ($existingCsbForm->ad_code_document ?? null),
                'iec_number' => $validated['iec_number'],
                'iec_document' => $iecDocumentPath ?? ($existingCsbForm->iec_document ?? null),
                'bank_account_number' => $validated['bank_account_number'],
                'bank_type' => $validated['bank_type'],
                'lut_document' => $lutDocumentPath ?? ($existingCsbForm->lut_document ?? null),
                'lut_expiry_date' => $validated['lut_expiry_date'] ?? null,
                'lut_bond_year' => $validated['lut_bond_year'] ?? null,
                'aadhar_number' => $aadhar,
                'aadhar_verified' => $aadharVerified,
                'aadhar_document' => $aadharFrontPath
                    ?? ($existingCsbForm->aadhar_document ?? null)
                    ?? ($existingBusinessKyc->aadhar_front_document ?? null),
                'signature_document' => $signaturePath
                    ?? ($existingCsbForm->signature_document ?? null)
                    ?? ($existingBusinessKyc->signature_document ?? null),
                'billing_address' => $validated['billing_address'],
                'billing_contact' => $validated['billing_contact'],
                'billing_email' => $validated['billing_email'],
                'merchant_agreement' => $merchantAgreementPath ?? ($existingCsbForm->merchant_agreement ?? null),
                'merchant_agreement_accepted_at' => $validated['terms_accepted'] ? now() : null,
            ];

            if ($existingCsbForm) {
                $existingCsbForm->update($csbData);
                $csbForm = $existingCsbForm;
            } else {
                $csbForm = CsbForm::create($csbData);
            }

            // Create or update a KycDetail record with kyc_type='business'
            // so the submission appears in the admin KYC Pending list for review.
            $businessKycData = [
                'customer_id' => $customer->id,
                'kyc_type' => 'business',
                'gst_number' => $gstNumber,
                'gst_verified' => true,
                'aadhar_number' => $aadhar,
                'aadhar_verified' => $aadharVerified,
                'aadhar_front_document' => $aadharFrontPath
                    ?? ($existingBusinessKyc->aadhar_front_document ?? null)
                    ?? ($existingCsbForm->aadhar_document ?? null),
                'aadhar_back_document' => $aadharBackPath ?? ($existingBusinessKyc->aadhar_back_document ?? null),
                'pan_number' => $panNumber,
                'pan_holder_name' => $validated['pan_holder_name'],
                'pan_dob' => $validated['pan_dob'],
                'pan_document' => $panDocumentPath ?? ($existingBusinessKyc->pan_document ?? null),
                'pan_verified' => true,
                'organization_name' => $validated['gst_business_name'],
                'authorized_signatory' => $customer->first_name . ' ' . $customer->last_name,
                'signature_document' => $signaturePath
                    ?? ($existingBusinessKyc->signature_document ?? null)
                    ?? ($existingCsbForm->signature_document ?? null),
                'signature' => $signaturePath
                    ?? ($existingBusinessKyc->signature_document ?? null)
                    ?? ($existingCsbForm->signature_document ?? null),
                'billing_address' => $validated['billing_address'],
                'billing_contact' => $validated['billing_contact'],
                'billing_email' => $validated['billing_email'],
                'merchant_agreement' => $merchantAgreementPath ?? ($existingBusinessKyc->merchant_agreement ?? null),
                'merchant_agreement_accepted_at' => $validated['terms_accepted'] ? now() : null,
                'terms_accepted' => $validated['terms_accepted'],
                'terms_accepted_at' => $validated['terms_accepted'] ? now() : null,
                'kyc_status' => 'under_review',
            ];

            if ($existingBusinessKyc) {
                $existingBusinessKyc->update($businessKycData);
            } else {
                KycDetail::create($businessKycData);
            }

            // Update customer record with the actual Aadhaar state and CSB status.
            $customer->aadhar_number = $aadhar;
            $customer->aadhar_verified = $aadharVerified;
            // Business KYC: CSB-IV (1) or CSB-V (2) based on selection
            $customer->csb_status = $validated['is_csb_v'] ? 2 : 1;
            $customer->save();
            KycDraft::where('customer_id', $customer->id)
                ->where('kyc_type', 'business')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Business KYC (CSB-V) submitted successfully! Your application is now under review.',
                'redirect' => route('customer.kyc.summary')
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return response()->json([
                'success' => false,
                'message' => $firstError ?: 'Please check the Business KYC details and try again.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('CSB form database error: ' . $e->getMessage());
            if ($e->getCode() === '23000' && ($identifier = $this->databaseKycIdentifierFromException($e))) {
                return $this->kycIdentifierConflictResponse($identifier);
            }

            return response()->json([
                'success' => false,
                'message' => 'CSB form submission failed. Please try again.'
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('CSB form submission error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'CSB form submission failed. Please try again.'
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

        // Require OTP verification: the phone number must have been verified via OTP
        $phoneVerified = session('registration_phone_verified', false);
        $verifiedPhone = session('registration_phone');

        if (!$phoneVerified || $verifiedPhone !== $request->phone_number) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your phone number via OTP before registering.'
            ], 403);
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
                'business_category_id' => $request->filled('business_category') ? (int) $request->business_category : null,
                'is_terms_accepted' => $request->has('termsCheck'),
                'email_verified' => false,
                'aadhar_verified' => false
            ]);

            // Clear registration OTP session data after successful registration
            session()->forget(['registration_otp', 'registration_phone', 'registration_otp_expires_at', 'registration_phone_verified']);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Please check your email for verification.',
                'redirect' => route('login')
            ], 200);

        } catch (\Throwable $e) {
            \Log::error('Registration store error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
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
            // Shipment creation always requires an authenticated customer.
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please log in to create a shipment.',
                    ], 401);
                }

                return redirect()->route('login');
            }

            // Block shipment creation if the admin has disabled it for this customer.
            if (!$customer->can_create_shipment) {
                $message = 'You do not have the right to create shipments. Please contact United Courier Worldwide.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 403);
                }
                return redirect()->back()
                    ->withInput()
                    ->with('error', $message);
            }

            // Keep shipper state as an uppercase two-letter code regardless of client-side behavior.
            if ($request->filled('shipper_state')) {
                $request->merge([
                    'shipper_state' => strtoupper(
                        preg_replace('/[^A-Za-z]/', '', trim((string) $request->shipper_state))
                    ),
                ]);
            }

            // Validate the request data
            $validatedData = $request->validate([
                // Shipper Info
                // delivery_destination now arrives as the destination_id (numeric)
                // selected from the dropdown. It is resolved to the destination NAME
                // below so all downstream code (storage, country-code mapping, APIs)
                // keeps working with the human-readable name.
                'delivery_destination' => 'required',
                'origin_type' => 'required|string|max:50',
                'selected_exporter_customer_id' => 'nullable|integer',
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
                'shipper_state' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
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
                'oversize_charge' => 'nullable|numeric|min:0',
                'handling_charge' => 'nullable|numeric|min:0',

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
                'invoice_date' => 'required|date|after_or_equal:today',
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

            // Never trust a client-submitted saved-customer ID or its accompanying
            // shipper fields. Verify account ownership and category access, then use
            // the saved record as the authoritative source before shipment validation.
            if (!empty($validatedData['selected_exporter_customer_id'])) {
                $selectedExporterCustomer = $this->canManageSavedCustomers($customer)
                    ? ExporterCustomer::where('exporter_id', $customer->id)
                        ->find($validatedData['selected_exporter_customer_id'])
                    : null;

                if (!$selectedExporterCustomer) {
                    throw ValidationException::withMessages([
                        'selected_exporter_customer_id' => 'The selected customer is invalid or does not belong to your account.',
                    ]);
                }

                $validatedData = array_merge(
                    $validatedData,
                    $selectedExporterCustomer->toShipperArray()
                );
            }

            // ------------------------------------------------------------------
            // KYC Number format validation based on the selected KYC Type.
            // Patterns:
            //   GST (Normal)       -> ^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$
            //   Aadhar Card        -> ^[2-9]{1}[0-9]{11}$  (12 digits, first digit 2-9)
            //   PAN Card           -> ^[A-Z]{5}[0-9]{4}[A-Z]{1}$
            //   Passport Number    -> ^[A-Z][0-9]{7}$
            // ------------------------------------------------------------------
            $kycType   = $validatedData['shipper_kyc_type']   ?? null;
            $kycNumber = $validatedData['shipper_kyc_number'] ?? null;

            if ($kycType && $kycNumber !== null && $kycNumber !== '') {
                $kycPatterns = [
                    'GST (Normal)'     => '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                    'Aadhar Card'      => '/^[2-9]{1}[0-9]{11}$/',
                    'PAN Card'          => '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                    'Passport Number'  => '/^[A-Z][0-9]{7}$/',
                ];

                $kycHints = [
                    'GST (Normal)'     => 'GSTIN must be 15 characters in the format 2 digits, 5 letters, 4 digits, 1 letter, 1 alphanumeric, Z, 1 alphanumeric (e.g. 27ABCDE1234F1Z5).',
                    'Aadhar Card'      => 'Aadhaar number must be 12 digits and the first digit must be between 2 and 9.',
                    'PAN Card'          => 'PAN must be 10 characters: 5 letters, 4 digits, 1 letter (e.g. ABCDE1234F).',
                    'Passport Number'  => 'Passport number must be 1 letter followed by 7 digits (e.g. A1234567).',
                ];

                if (isset($kycPatterns[$kycType]) && !preg_match($kycPatterns[$kycType], $kycNumber)) {
                    $kycMessage = 'The KYC Number entered is not valid for the selected KYC Type (' . $kycType . '). ' . ($kycHints[$kycType] ?? '');
                    if (!$request->expectsJson()) {
                        return back()
                            ->withErrors(['shipper_kyc_number' => $kycMessage])
                            ->withInput()
                            ->with('error', $kycMessage);
                    }
                    return response()->json([
                        'success' => false,
                        'message' => $kycMessage,
                        'errors' => [
                            'shipper_kyc_number' => [$kycMessage]
                        ]
                    ], 422);
                }
            }

            // ------------------------------------------------------------------
            // Resolve delivery_destination: the dropdown now sends the numeric
            // destination_id. Convert it back to the destination NAME so all
            // downstream logic (storage, getCountryCodeFromDestination(),
            // resolveDestinationCountry(), rate calculation, APIs) keeps
            // working with the human-readable name as before.
            // ------------------------------------------------------------------
            $destInput = $validatedData['delivery_destination'] ?? null;
            $destName  = is_numeric($destInput)
                ? optional(\App\Models\Destination::find((int) $destInput))->name
                : $destInput;

            if (!$destName) {
                if (!$request->expectsJson()) {
                    return back()
                        ->withErrors(['delivery_destination' => 'The selected destination is invalid.'])
                        ->withInput()
                        ->with('error', 'The selected destination is invalid.');
                }
                return response()->json([
                    'success' => false,
                    'message' => 'The selected destination is invalid.',
                    'errors'  => ['delivery_destination' => ['The selected destination is invalid.']],
                ], 422);
            }
            $validatedData['delivery_destination'] = $destName;

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

            // Server-side validation: enforce max invoice total based on origin_type.
            // CSB IV -> max 25,000 | CSB V -> max 10,00,000 (1,000,000)
            $items = $request->input('items', []);
            $calculatedTotal = 0;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $calculatedTotal += (float)($item['amount'] ?? 0);
                }
            }
            $maxAllowedTotal = ($validatedData['origin_type'] === 'CSB V') ? 1000000 : 25000;
            if ($calculatedTotal > $maxAllowedTotal) {
                $originLabel = ($validatedData['origin_type'] === 'CSB V') ? 'CSB V' : 'CSB IV';
                $message = 'The total invoice amount is ₹' . number_format($calculatedTotal, 2) .
                    ', which exceeds the maximum allowed limit of ₹' . number_format($maxAllowedTotal, 2) .
                    ' for ' . $originLabel . '. Please reduce the invoice total and try again.';
                if (!$request->expectsJson()) {
                    return back()
                        ->withErrors(['invoice_amount' => $message])
                        ->withInput()
                        ->with('error', $message);
                }
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => [
                        'invoice_amount' => [$message]
                    ]
                ], 422);
            }

            // Server-side validation: enforce max total package weight (68 kg) for CSB IV only.
            // CSB V has no weight limit.
            if ($validatedData['origin_type'] !== 'CSB V') {
                $packages = $request->input('packages', []);
                $totalWeight = 0;
                if (is_array($packages)) {
                    foreach ($packages as $pkg) {
                        $totalWeight += (float)($pkg['chargeable_weight'] ?? 0);
                    }
                }
                $maxWeight = 68;
                if ($totalWeight > $maxWeight) {
                    $weightMessage = 'The total package weight is ' . number_format($totalWeight, 2) .
                        ' kg, which exceeds the maximum allowed limit of ' . $maxWeight .
                        ' kg for CSB IV. Please reduce the weight and try again.';
                    if (!$request->expectsJson()) {
                        return back()
                            ->withErrors(['packages' => $weightMessage])
                            ->withInput()
                            ->with('error', $weightMessage);
                    }
                    return response()->json([
                        'success' => false,
                        'message' => $weightMessage,
                        'errors' => [
                            'packages' => [$weightMessage]
                        ]
                    ], 422);
                }
            }

            // ============================================================
            // SERVER-SIDE PER-PACKAGE WEIGHT & DIMENSION VALIDATION
            // Max Actual Weight: 30 kg | Max Length: 120 cm | Max Width: 76 cm
            // Max Volumetric Weight: 68 kg (hard block above this)
            // Oversize: vol wt 40.001–68 kg → ₹21,000 charge (allowed)
            // ============================================================
            $pkgMaxActualWeight = 30;
            $pkgMaxLength = 120;
            $pkgMaxWidth = 76;
            $pkgMaxVolumetricWeight = 68;
            $pkgOversizeMin = 40.001;
            $oversizeChargeAmount = 21000;

            $packagesForValidation = $request->input('packages', []);
            $hasOversizePackage = false;
            if (is_array($packagesForValidation)) {
                foreach ($packagesForValidation as $idx => $pkg) {
                    $actualWt = (float)($pkg['actual_weight_kg'] ?? 0);
                    $length = (float)($pkg['length_cm'] ?? 0);
                    $width = (float)($pkg['width_cm'] ?? 0);
                    $volWt = (float)($pkg['volumetric_weight'] ?? 0);

                    $pkgErrors = [];
                    if ($actualWt > $pkgMaxActualWeight) {
                        $pkgErrors[] = 'Actual weight ' . number_format($actualWt, 2) .
                            ' kg exceeds max ' . $pkgMaxActualWeight . ' kg.';
                    }
                    if ($length > $pkgMaxLength) {
                        $pkgErrors[] = 'Length ' . number_format($length, 2) .
                            ' cm exceeds max ' . $pkgMaxLength . ' cm.';
                    }
                    if ($width > $pkgMaxWidth) {
                        $pkgErrors[] = 'Width ' . number_format($width, 2) .
                            ' cm exceeds max ' . $pkgMaxWidth . ' cm.';
                    }
                    if ($volWt > $pkgMaxVolumetricWeight) {
                        $pkgErrors[] = 'Volumetric weight ' . number_format($volWt, 2) .
                            ' kg exceeds the maximum allowed ' . $pkgMaxVolumetricWeight . ' kg.';
                    }

                    if (!empty($pkgErrors)) {
                        $boxNum = $idx + 1;
                        $pkgMessage = 'Box #' . $boxNum . ': ' . implode(' ', $pkgErrors) .
                            ' Please correct the values and try again.';
                        if (!$request->expectsJson()) {
                            return back()
                                ->withErrors(['packages' => $pkgMessage])
                                ->withInput()
                                ->with('error', $pkgMessage);
                        }
                        return response()->json([
                            'success' => false,
                            'message' => $pkgMessage,
                            'errors' => [
                                'packages' => [$pkgMessage]
                            ]
                        ], 422);
                    }

                    // Track whether any package is in the oversize range (40.001–68 kg)
                    if ($volWt > $pkgOversizeMin && $volWt <= $pkgMaxVolumetricWeight) {
                        $hasOversizePackage = true;
                    }
                }
            }

            // Determine the final oversize charge:
            // Use the frontend-confirmed value if present, otherwise compute from packages.
            $oversizeCharge = (float)($validatedData['oversize_charge'] ?? 0);
            if ($hasOversizePackage && $oversizeCharge <= 0) {
                $oversizeCharge = $oversizeChargeAmount;
            }

            // Resolve shipping_method from service_id when the shipping_method
            // <select> dropdown is empty but a DDP/DDU radio button was selected.
            // The JS at line 8610 sends 'service_id' alongside FormData.
            $serviceId = $request->input('service_id');
            if (empty($validatedData['shipping_method']) && $serviceId) {
                $courierService = CourierService::find($serviceId);
                if ($courierService) {
                    $validatedData['shipping_method'] = $courierService->method;
                    \Log::info('storeShipment: Resolved shipping_method from service_id #' . $serviceId . ' → "' . $courierService->method . '"');
                }
            }

            // Ensure we have a service_id (courier_services.id) to persist on the
            // shipper_info row. Prefer the value sent by the frontend; otherwise
            // resolve it from the (possibly just-resolved) shipping_method.
            if (!$serviceId && !empty($validatedData['shipping_method'])) {
                $resolvedService = CourierService::whereRaw('LOWER(method) = ?', [strtolower($validatedData['shipping_method'])])->first();
                if ($resolvedService) {
                    $serviceId = $resolvedService->id;
                }
            }

            // ------------------------------------------------------------
            // SHIPPER STATE — MAX 2 WORDS VALIDATION
            // The shipper state field must not contain more than 2 words
            // (e.g. "Gujarat", "New South Wales" are valid, but
            // "Some Very Long State Name" is not). Block shipment creation
            // here so the user is informed BEFORE the shipment is saved.
            // ------------------------------------------------------------
            $shipperStateForWordCount = trim((string)($validatedData['shipper_state'] ?? ''));
            if ($shipperStateForWordCount !== '' && str_word_count($shipperStateForWordCount) > 2) {
                $wordCountMessage = 'Shipper state must not exceed 2 words. The provided state "' . $shipperStateForWordCount . '" contains ' . str_word_count($shipperStateForWordCount) . ' words. Please enter a shorter state name and try again.';
                if (!$request->expectsJson()) {
                    return back()
                        ->withErrors(['shipper_state' => $wordCountMessage])
                        ->withInput()
                        ->with('error', $wordCountMessage);
                }
                return response()->json([
                    'success' => false,
                    'message' => $wordCountMessage,
                    'errors' => [
                        'shipper_state' => [$wordCountMessage]
                    ]
                ], 422);
            }

            // ============================================================
            // OVERSEAS LOGISTIC — SHIPPER STATE VALIDATION
            // The Overseas Logistic API (used for UNITED CANADA DDP /
            // E-COMMERCE and ARAMEX GPX / Australia) requires the shipper
            // state to be a 2-letter code (e.g. "GJ", "MH"). If the shipper
            // state field contains more than 2 characters, block shipment
            // creation here so the user is informed BEFORE the shipment is
            // saved (rather than failing later at manifest time).
            // ============================================================
            $resolvedShippingMethod = $validatedData['shipping_method'] ?? '';
            if ($this->isOverseasLogisticMethod($resolvedShippingMethod)) {
                $shipperStateInput = trim((string)($validatedData['shipper_state'] ?? ''));
                if (strlen($shipperStateInput) > 2) {
                    $stateMessage = 'Shipper state must be a 2-letter code (e.g. "GJ", "MH") for Overseas shipments. The provided state "' . $shipperStateInput . '" is too long. Please enter a 2-letter state code and try again.';
                    if (!$request->expectsJson()) {
                        return back()
                            ->withErrors(['shipper_state' => $stateMessage])
                            ->withInput()
                            ->with('error', $stateMessage);
                    }
                    return response()->json([
                        'success' => false,
                        'message' => $stateMessage,
                        'errors' => [
                            'shipper_state' => [$stateMessage]
                        ]
                    ], 422);
                }
            }

            // ============================================================
            // USA – UNITED GROUND PREMIUM HANDLING CHARGE
            // If the resolved shipping_method is "United Ground Premium" and
            // any package's actual weight exceeds 22 kg, apply a ₹5,000
            // handling charge. Use the frontend-confirmed value if present,
            // otherwise compute from packages (defense in depth).
            // ============================================================
            $handlingChargeAmount = 5000;
            $handlingCharge = (float)($validatedData['handling_charge'] ?? 0);
            if (strcasecmp(($validatedData['shipping_method'] ?? ''), 'UNITED GROUND PREMIUM') === 0 && $handlingCharge <= 0) {
                $hasHandlingPackage = false;
                if (!empty($validatedData['packages']) && is_array($validatedData['packages'])) {
                    foreach ($validatedData['packages'] as $pkg) {
                        $actualWt = (float)($pkg['actual_weight_kg'] ?? 0);
                        if ($actualWt > 22) {
                            $hasHandlingPackage = true;
                            break;
                        }
                    }
                }
                if ($hasHandlingPackage) {
                    $handlingCharge = $handlingChargeAmount;
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
                'service_id' => $serviceId ?? null,
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
                'oversize_charge' => $oversizeCharge,
                'handling_charge' => $handlingCharge,
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

            // Log the draft status change
            ShipmentLog::logStatus(
                $shipper->id,
                $awbNumber,
                'draft',
                null,
                'Shipment created (draft)',
                auth()->guard('customer')->id(),
                'customer'
            );

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
                    'oversize_charge' => (float) $oversizeCharge,
                    'handling_charge' => (float) $handlingCharge,
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


    // new ups rate made by "Anil Sir"
    public function getUpsRate(Request $request)
    {
        //try {
            // 1. Get logged-in customer
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to view rates.'
                ], 401);
            }

            // 2. Get all inputs
            //$serviceId = $request->service_id;
            $totalWeight = floatval($request->total_weight ?? 0);
            $consigneeState = $request->consignee_state;
            $consigneeZipCode = $request->consignee_zip_code;
            $deliveryDestination = $request->delivery_destination;
            $packageWeights = $request->package_weights;
           //$isMultiPackage = is_array($packageWeights) && count($packageWeights) > 1;
            $isMultiPackage = is_array($packageWeights) ? count($packageWeights) :1;
			
            // 3. Weight validation
            if ($totalWeight <= 0) {
                return response()->json([
                    'success' => true,
                    'customer_exists' => false,
                    'customer_name' => $customer->first_name . ' ' . $customer->last_name,
                    'all_rates' => [],
                    'message' => 'Please enter Actual Weight greater than 0 to view rates.'
                ]);
            }

            // 4. Resolve destination and its zone.
            // The frontend normally sends the destination name, but API/bulk
            // callers may send the numeric destinations.id instead.
            $destinationCountry = $this->resolveDestinationCountry($deliveryDestination);
            $destination = null;
            if (ctype_digit(trim((string) $deliveryDestination))) {
                $destination = \App\Models\Destination::find((int) $deliveryDestination);
            } elseif (!empty($deliveryDestination)) {
                $destinationValue = trim((string) $deliveryDestination);
                $destination = \App\Models\Destination::where(function ($query) use ($destinationValue) {
                    $query->whereRaw('UPPER(name) = ?', [strtoupper($destinationValue)])
                        ->orWhereRaw('UPPER(code) = ?', [strtoupper($destinationValue)]);
                })->first();
            }

            // Limit state/emirate matching to the selected destination whenever
            // possible. This prevents values such as "Dubai" from resolving to
            // an unrelated country's zone. Match both stored codes and names
            // because the UAE dropdown submits full emirate names.
            $zone = null;
            if (!empty($consigneeState)) {
                $stateValue = trim((string) $consigneeState);
                $stateAliases = [$stateValue];
                $uaeEmirateCodes = [
                    'ABU DHABI' => 'AZ',
                    'AJMAN' => 'AJ',
                    'DUBAI' => 'DU',
                    'FUJAIRAH' => 'FU',
                    'RAS AL KHAIMAH' => 'RK',
                    'SHARJAH' => 'SH',
                    'UMM AL QUWAIN' => 'UQ',
                ];
                if ($destinationCountry === 'UAE' && isset($uaeEmirateCodes[strtoupper($stateValue)])) {
                    $stateAliases[] = $uaeEmirateCodes[strtoupper($stateValue)];
                }

                $zoneQuery = \App\Models\Zone::query();
                if ($destination) {
                    $zoneQuery->where('destination_id', $destination->id);
                }
                $zone = $zoneQuery->where(function ($query) use ($stateAliases) {
                    foreach ($stateAliases as $alias) {
                        $query->orWhereRaw('UPPER(zone_code) = ?', [strtoupper($alias)])
                            ->orWhereRaw('UPPER(zone_name) = ?', [strtoupper($alias)]);
                    }
                })->first();
            }

            if (empty($zone) && !empty($consigneeZipCode)) {
                // Normalise the postcode (uppercase, trim spaces) for matching.
                $zipNorm = strtoupper(preg_replace('/\s+/', '', trim($consigneeZipCode)));
                if ($zipNorm !== '') {
                    // Try an exact match first, scoped to the selected destination.
                    $exactZipQuery = \App\Models\Zone::query();
                    if ($destination) {
                        $exactZipQuery->where('destination_id', $destination->id);
                    }
                    $zone = $exactZipQuery
                        ->whereRaw("UPPER(REPLACE(zone_code, ' ', '')) = ?", [$zipNorm])
                        ->first();

                    if (empty($zone)) {
                        // Find the longest stored outward/FSA code that prefixes
                        // the full postcode, e.g. SW1 for SW1A1AA or M5H for M5H2N2.
                        $prefixZipQuery = \App\Models\Zone::where('zone_category', 'zipcode');
                        if ($destination) {
                            $prefixZipQuery->where('destination_id', $destination->id);
                        }
                        $zone = $prefixZipQuery
                            ->whereRaw("? LIKE CONCAT(UPPER(REPLACE(zone_code, ' ', '')), '%')", [$zipNorm])
                            ->orderByRaw("LENGTH(REPLACE(zone_code, ' ', '')) DESC")
                            ->first();
                    }
                }
            }

            $zoneNumber = $zone?->zone_number_testing;
            $zoneName = $zone?->zone_name;
            $zoneCode = $zone?->zone_code;

            // 5. Get services
            /*$serviceRows = \DB::select(
				 "SELECT * FROM courier_services WHERE country = ? LIMIT 1",
                [$destinationCountry]
            );*/
            // Only enabled services (status = 1) show rates to customers.
            $services = CourierService::where('country', $destinationCountry)->where('status', 1)->get();
            
            if(empty($services)){
            	return response()->json([
                    'success' => false,
                    'message' => 'Service not available.'
                ], 404); 
            }
            
            //print_r($services); exit;   
            // 6. Process rates - SINGLE LOOP
            $allRates = [];
            $customerRatesExist = false;
            $singleServiceModel = null;
                
            foreach ($services as $key=>$service) {
                
                // ========== SPECIAL CASE: US Multi-package with United Ground Premium ==========
                if ($destinationCountry === 'US' && strtolower($service->method)=='united ground premium') {
                        // echo 'A';
                    // Box-wise calculation
                    $boxBreakdown = [];
                    $combinedBase = 0;
                    $combinedFuel = 0;
                    $combinedGst = 0;
                    $firstMatchedRate = null;
                    $allBoxesMatched = true;
					
                    foreach ($packageWeights as $index => $pkgWt) {
                      $pkgWt = floatval($pkgWt);
                      
                    //echo $customer->id."".$service->id."".$destinationCountry."".$zoneNumber."".$pkgWt."".$pkgWt;
					//echo "<br>";

                        // Try customer rates first
                        $boxRate = \DB::select(
                            "SELECT cr.*, cs.country, cs.service_code, cs.method
                            FROM courier_rates cr
                            INNER JOIN courier_services cs ON cr.service_id = cs.id
                            WHERE cr.customer_id = ?
                            AND cr.service_id = ?
                            AND cs.country = ?
                            AND (cr.zone_no = ? OR (cr.zone_no IS NULL OR cr.zone_no = 0))
                            AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
                            ORDER BY cr.zone_no DESC, cr.wt_range_start
                            LIMIT 1",
                            [$customer->id, $service->id, $destinationCountry, $zoneNumber, $pkgWt]
                        );

                        // Fallback to default rates (customer_id = 0) when no customer-specific rate exists
                        if (empty($boxRate)) {
                            $boxRate = \DB::select(
                                "SELECT cr.*, cs.country, cs.service_code, cs.method
                                FROM courier_rates cr
                                INNER JOIN courier_services cs ON cr.service_id = cs.id
                                WHERE cr.customer_id = 0
                                AND cr.service_id = ?
                                AND cs.country = ?
                                AND (cr.zone_no = ? OR (cr.zone_no IS NULL OR cr.zone_no = 0))
                                AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
                                ORDER BY cr.zone_no DESC, cr.wt_range_start
                                LIMIT 1",
                                [$service->id, $destinationCountry, $zoneNumber, $pkgWt]
                            );
                        }

                        // print_r($boxRate);
                        // exit;
                        
                        if(!empty($boxRate)){
                        	$boxRate = $boxRate[0];
                        	if ($firstMatchedRate === null) { $firstMatchedRate = $boxRate; }

	                        // Calculate per-box amounts
	                        $base = floatval($boxRate->price);
	                        $fuel = floatval($boxRate->fuel_charge) > 0 
	                            ? floatval($boxRate->fuel_charge) 
	                            : ($base * floatval($boxRate->fuel_percentage) / 100);
	                        $gst = floatval($boxRate->gst_amount) > 0 
	                            ? floatval($boxRate->gst_amount) 
	                            : (($base + $fuel) * floatval($boxRate->gst_percentage) / 100);

	                        $boxBreakdown[] = [
	                            'box' => $index + 1,
	                            'weight' => $pkgWt,
	                            'base' => $base,
	                            'fuel' => $fuel,
	                            'gst' => $gst,
	                            'total' => $base + $fuel + $gst,
	                        ];

	                        $combinedBase += $base;
	                        $combinedFuel += $fuel;
	                        $combinedGst += $gst;
	                        
                        }
                        /*else{
                        	
                        	return response()->json([
			                    'success' => false,
			                    'message' => 'Service rate not available. Please contact to support.'
			                ], 404);
                        }*/ 
                        
                    }//end foreach loop

                   
                    $allRates[] = [
                        'rate_id' => $firstMatchedRate ? $firstMatchedRate->id : null,
                        'service_id' => $service->id,
                        'method' => $service->method,
                        'method_display' => $service->method . ' ' . $service->tat,
                        'network' => $service->network,
                        'method_code' => $service->method_code,
                        'tat' => $service->tat,
                        'delivery_days' => $service->tat,
                        'scode' => $service->scode,
                        'consigneeState' => $consigneeState,
                        'zone_no' => $zoneNumber,
                        // i want to print zone name
                        'zone_name' => $zoneName ?? null,
                        'pkg_wt' => $pkgWt,
                        //'wt_range_start' => $firstMatchedRate->wt_range_start,
                        //'wt_range_end' => $firstMatchedRate->wt_range_end,
                        'price' => $combinedBase,
                        'fuel_charge' => $combinedFuel,
                        'fuel_percentage' => 0,
                        'gst_percentage' => 0,
                        'gst_amount' => $combinedGst,
                        'is_multi_package' => true,
                        'box_breakdown' => $boxBreakdown,
                    ];
                
                }//end if
                
                if ($destinationCountry === 'US' && $isMultiPackage<=1 && strtolower($service->method)!='united ground premium') {
				    // Box-wise calculation
				    $boxBreakdown = [];
				    $combinedBase = 0;
				    $combinedFuel = 0;
				    $combinedGst = 0;
				    $firstMatchedRate = null;
				    $allBoxesMatched = true;

				    foreach ($packageWeights as $index => $pkgWt) {
				        $pkgWt = floatval($pkgWt) ?: 1;

				        // Try customer rates first
				        $boxRate = \DB::select(
				            "SELECT cr.*, cs.country, cs.service_code, cs.method
				            FROM courier_rates cr
				            INNER JOIN courier_services cs ON cr.service_id = cs.id
				            WHERE cr.customer_id = ?
				            AND cr.service_id = ?
				            AND cs.country = ?
				            AND (cr.zone_no = ? OR cr.zone_no IS NULL OR cr.zone_no = 0)
				            AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
				            ORDER BY cr.zone_no DESC, cr.wt_range_start
				            LIMIT 1",
				            [$customer->id, $service->id, $destinationCountry, $zoneNumber, $pkgWt]
				        );

				        // Fallback to default rates (customer_id = 0) when no customer-specific rate exists
				        if (empty($boxRate)) {
				            $boxRate = \DB::select(
				                "SELECT cr.*, cs.country, cs.service_code, cs.method
				                FROM courier_rates cr
				                INNER JOIN courier_services cs ON cr.service_id = cs.id
				                WHERE cr.customer_id = 0
				                AND cr.service_id = ?
				                AND cs.country = ?
				                AND (cr.zone_no = ? OR (cr.zone_no IS NULL OR cr.zone_no = 0))
				                AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
				                ORDER BY cr.zone_no DESC, cr.wt_range_start
				                LIMIT 1",
				                [$service->id, $destinationCountry, $zoneNumber, $pkgWt]
				            );
				        }

				        // If no customer rate, try default
				        
						if(!empty($boxRate)){
							$boxRate = $boxRate[0];
							if ($firstMatchedRate === null) { $firstMatchedRate = $boxRate; }
				        
				            // Calculate per-box amounts
				            $base = floatval($boxRate->price);
				            $fuel = floatval($boxRate->fuel_charge) > 0 
				                ? floatval($boxRate->fuel_charge) 
				                : ($base * floatval($boxRate->fuel_percentage) / 100);
				            $gst = floatval($boxRate->gst_amount) > 0 
				                ? floatval($boxRate->gst_amount) 
				                : (($base + $fuel) * floatval($boxRate->gst_percentage) / 100);

				            $boxBreakdown[] = [
				                'box' => $index + 1,
				                'weight' => $pkgWt,
				                'base' => $base,
				                'fuel' => $fuel,
				                'gst' => $gst,
				                'total' => $base + $fuel + $gst,
				            ];

				            $combinedBase += $base;
				            $combinedFuel += $fuel;
				            $combinedGst += $gst;
				            
						}
						/*else{
							
							return response()->json([
				                'success' => false,
				                'message' => 'Service rate not available. Please contact to support.'
				            ], 404);
						}*/
				        
				    }

				    $allRates[] = [
				        'rate_id' => $firstMatchedRate ? $firstMatchedRate->id : null,
				        'service_id' => $service->id,
				        'method' => $service->method,
				        'method_display' => $service->method . ' ' . $service->tat,
				        'network' => $service->network,
				        'method_code' => $service->method_code,
				        'tat' => $service->tat,
				        'delivery_days' => $service->tat,
				        'scode' => $service->scode,
				        'consigneeState' => $consigneeState,
				        'zone_no' => $zoneNumber,
				        'pkg_wt' => $pkgWt,
				        //'wt_range_start' => $firstMatchedRate->wt_range_start,
				        //'wt_range_end' => $firstMatchedRate->wt_range_end,
				        'price' => $combinedBase,
				        'fuel_charge' => $combinedFuel,
				        'fuel_percentage' => 0,
				        'gst_percentage' => 0,
				        'gst_amount' => $combinedGst,
				        'is_multi_package' => true,
				        'box_breakdown' => $boxBreakdown,
				    ];

				}//end if

				if ($destinationCountry === 'UK'){
					$boxBreakdown = [];
                    $combinedBase = 0;
                    $combinedFuel = 0;
                    $combinedGst = 0;
                    $firstMatchedRate = null;
                    $allBoxesMatched = true;
					
                    foreach ($packageWeights as $index => $pkgWt) {
                      $pkgWt = floatval($pkgWt);
                      
                    //echo $customer->id."".$service->id."".$destinationCountry."".$zoneNumber."".$pkgWt."".$pkgWt;
					//echo "<br>";

                        // Try customer rates first
                        $boxRate = \DB::select(
                            "SELECT cr.*, cs.country, cs.service_code, cs.method
                            FROM courier_rates cr
                            INNER JOIN courier_services cs ON cr.service_id = cs.id
                            WHERE cr.customer_id = ?
                            AND cr.service_id = ?
                            AND cs.country = ?
                            AND (cr.zone_no = ? OR (cr.zone_no IS NULL OR cr.zone_no = 0))
                            AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
                            ORDER BY cr.zone_no DESC, cr.wt_range_start
                            LIMIT 1",
                            [$customer->id, $service->id, $destinationCountry, $zoneNumber, $pkgWt]
                        );

                        // Fallback to default rates (customer_id = 0) when no customer-specific rate exists
                        if (empty($boxRate)) {
                            $boxRate = \DB::select(
                                "SELECT cr.*, cs.country, cs.service_code, cs.method
                                FROM courier_rates cr
                                INNER JOIN courier_services cs ON cr.service_id = cs.id
                                WHERE cr.customer_id = 0
                                AND cr.service_id = ?
                                AND cs.country = ?
                                AND (cr.zone_no = ? OR (cr.zone_no IS NULL OR cr.zone_no = 0))
                                AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
                                ORDER BY cr.zone_no DESC, cr.wt_range_start
                                LIMIT 1",
                                [$service->id, $destinationCountry, $zoneNumber, $pkgWt]
                            );
                        }

                        //print_r($boxRate);
                        
                        if(!empty($boxRate)){
                        	$boxRate = $boxRate[0];
                        	if ($firstMatchedRate === null) { $firstMatchedRate = $boxRate; }

	                        // Calculate per-box amounts
	                        $base = floatval($boxRate->price);
	                        $fuel = floatval($boxRate->fuel_charge) > 0 
	                            ? floatval($boxRate->fuel_charge) 
	                            : ($base * floatval($boxRate->fuel_percentage) / 100);
	                        $gst = floatval($boxRate->gst_amount) > 0 
	                            ? floatval($boxRate->gst_amount) 
	                            : (($base + $fuel) * floatval($boxRate->gst_percentage) / 100);

	                        $boxBreakdown[] = [
	                            'box' => $index + 1,
	                            'weight' => $pkgWt,
	                            'base' => $base,
	                            'fuel' => $fuel,
	                            'gst' => $gst,
	                            'total' => $base + $fuel + $gst,
	                        ];

	                        $combinedBase += $base;
	                        $combinedFuel += $fuel;
	                        $combinedGst += $gst;
	                        
                        }
                        /*else{
                        	
                        	return response()->json([
			                    'success' => false,
			                    'message' => 'Service rate not available. Please contact to support.'
			                ], 404);
                        }*/ 
                        
                    }//end foreach loop

                   
                    $allRates[] = [
                        'rate_id' => $firstMatchedRate ? $firstMatchedRate->id : null,
                        'service_id' => $service->id,
                        'method' => $service->method,
                        'method_display' => $service->method . ' ' . $service->tat,
                        'network' => $service->network,
                        'method_code' => $service->method_code,
                        'tat' => $service->tat,
                        'delivery_days' => $service->tat,
                        'scode' => $service->scode,
                        'consigneeState' => $consigneeState,
                        'zone_no' => $zoneNumber,
                        'pkg_wt' => $pkgWt,
                        //'wt_range_start' => $firstMatchedRate->wt_range_start,
                        //'wt_range_end' => $firstMatchedRate->wt_range_end,
                        'price' => $combinedBase,
                        'fuel_charge' => $combinedFuel,
                        'fuel_percentage' => 0,
                        'gst_percentage' => 0,
                        'gst_amount' => $combinedGst,
                        'is_multi_package' => true,
                        'box_breakdown' => $boxBreakdown,
                    ];
				}
				
				// Australia uses the same box-wise rate calculation as Canada
				// (both are zipcode-category destinations with zone_no-based
				// rates). The query below is fully parameterized by
				// $destinationCountry, so adding 'AUS' here makes the
				// ARAMEX GPX ALL IN service rates resolve correctly.
				if ($destinationCountry === 'CA' || $destinationCountry === 'AUS' || $destinationCountry === 'NZ' || $destinationCountry === 'UAE' || $destinationCountry === 'SG' || $destinationCountry === 'MY' || $destinationCountry === 'DE') {
					$boxBreakdown = [];
                    $combinedBase = 0;
                    $combinedFuel = 0;
                    $combinedGst = 0;
                    $firstMatchedRate = null;
                    $allBoxesMatched = true;
					
                    foreach ($packageWeights as $index => $pkgWt) {
                      $pkgWt = floatval($pkgWt);
                      
                    //echo $customer->id."".$service->id."".$destinationCountry."".$zoneNumber."".$pkgWt."".$pkgWt;
					//echo "<br>";

                        // Try customer rates first
                        $boxRate = \DB::select(
                            "SELECT cr.*, cs.country, cs.service_code, cs.method
                            FROM courier_rates cr
                            INNER JOIN courier_services cs ON cr.service_id = cs.id
                            WHERE cr.customer_id = ?
                            AND cr.service_id = ?
                            AND cs.country = ?
                            AND (cr.zone_no = ? OR (cr.zone_no IS NULL OR cr.zone_no = 0))
                            AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
                            ORDER BY cr.zone_no DESC, cr.wt_range_start
                            LIMIT 1",
                            [$customer->id, $service->id, $destinationCountry, $zoneNumber, $pkgWt]
                        );

                        // Fallback to default rates (customer_id = 0) when no customer-specific rate exists
                        if (empty($boxRate)) {
                            $boxRate = \DB::select(
                                "SELECT cr.*, cs.country, cs.service_code, cs.method
                                FROM courier_rates cr
                                INNER JOIN courier_services cs ON cr.service_id = cs.id
                                WHERE cr.customer_id = 0
                                AND cr.service_id = ?
                                AND cs.country = ?
                                AND (cr.zone_no = ? OR (cr.zone_no IS NULL OR cr.zone_no = 0))
                                AND ? BETWEEN cr.wt_range_start AND cr.wt_range_end
                                ORDER BY cr.zone_no DESC, cr.wt_range_start
                                LIMIT 1",
                                [$service->id, $destinationCountry, $zoneNumber, $pkgWt]
                            );
                        }

                        //print_r($boxRate);
                        
                        if(!empty($boxRate)){
                        	$boxRate = $boxRate[0];
                        	if ($firstMatchedRate === null) { $firstMatchedRate = $boxRate; }

	                        // Calculate per-box amounts
	                        $base = floatval($boxRate->price);
	                        $fuel = floatval($boxRate->fuel_charge) > 0 
	                            ? floatval($boxRate->fuel_charge) 
	                            : ($base * floatval($boxRate->fuel_percentage) / 100);
	                        $gst = floatval($boxRate->gst_amount) > 0 
	                            ? floatval($boxRate->gst_amount) 
	                            : (($base + $fuel) * floatval($boxRate->gst_percentage) / 100);

	                        $boxBreakdown[] = [
	                            'box' => $index + 1,
	                            'weight' => $pkgWt,
	                            'base' => $base,
	                            'fuel' => $fuel,
	                            'gst' => $gst,
	                            'total' => $base + $fuel + $gst,
	                        ];

	                        $combinedBase += $base;
	                        $combinedFuel += $fuel;
	                        $combinedGst += $gst;
	                        
                        }
                        /*else{
                        	
                        	return response()->json([
			                    'success' => false,
			                    'message' => 'Service rate not available. Please contact to support.'
			                ], 404);
                        }*/ 
                        
                    }//end foreach loop

                   
                    $allRates[] = [
                        'rate_id' => $firstMatchedRate ? $firstMatchedRate->id : null,
                        'service_id' => $service->id,
                        'method' => $service->method,
                        'method_display' => $service->method . ' ' . $service->tat,
                        'network' => $service->network,
                        'method_code' => $service->method_code,
                        'tat' => $service->tat,
                        'delivery_days' => $service->tat,
                        'scode' => $service->scode,
                        'consigneeState' => $consigneeState,
                        'zone_no' => $zoneNumber,
                        'pkg_wt' => $pkgWt,
                        //'wt_range_start' => $firstMatchedRate->wt_range_start,
                        //'wt_range_end' => $firstMatchedRate->wt_range_end,
                        'price' => $combinedBase,
                        'fuel_charge' => $combinedFuel,
                        'fuel_percentage' => 0,
                        'gst_percentage' => 0,
                        'gst_amount' => $combinedGst,
                        'is_multi_package' => true,
                        'box_breakdown' => $boxBreakdown,
                    ];
				}
                
                
                
                // ========== STANDARD RATE FETCHING (For all other cases) ==========
                // Skip if multi-package US and NOT United Ground Premium
               
                // print_r($isMultiPackage);
             
            }//end foreach


            //print_r($allRates);
            //exit;

            // Filter out rate cards whose total price (base + fuel + gst) is 0.
            // When no rate row matches the weight/zone the combined amounts stay
            // 0 and the card would otherwise show "₹0.00" — hide those from the
            // customer-facing rate list.
            $allRates = array_values(array_filter($allRates, function ($r) {
                $base = floatval($r['price'] ?? 0);
                $fuel = floatval($r['fuel_charge'] ?? 0);
                $gst  = floatval($r['gst_amount'] ?? 0);
                return ($base + $fuel + $gst) > 0;
            }));

            // Attach consistent selected-zone metadata to every card. Several
            // calculation branches previously omitted zone_code, which caused
            // the frontend to render labels such as "Remote (undefined)".
            $allRates = array_map(function ($rate) use ($zoneNumber, $zoneName, $zoneCode) {
                $rate['zone_no'] = $rate['zone_no'] ?? $zoneNumber;
                $rate['zone_name'] = $zoneName;
                $rate['zone_code'] = $zoneCode;
                return $rate;
            }, $allRates);

            // 7. Build response
            $response = [
                'success' => true,
                
                'customer_name' => $customer->first_name . ' ' . $customer->last_name,
                'selected_zone' => $zone ? [
                    'zone_id' => $zone->id,
                    'zone_number' => $zone->zone_number_testing,
                    'zone_name' => $zone->zone_name,
                    'zone_code' => $zone->zone_code,
                    'state' => $consigneeState,
                ] : [
                    'state' => $consigneeState,
                    'message' => 'No zone found for the selected state'
                ],
                'all_rates' => $allRates,
            ];


            return response()->json($response);

       /* } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }*/
    
    }



    // public function getUpsRate(Request $request)
    // {
    //     // print_r($request->all());
    //     try {
    //         $serviceId = $request->service_id;
    //         $totalWeight = floatval($request->total_weight ?? 0);
    //         $consigneeState = $request->consignee_state;
    //         $deliveryDestination = $request->delivery_destination;

    //         // Optional: per-package chargeable weights for box-wise rate calculation.
    //         // When more than one package is present, only "United Ground Premium"
    //         // service is offered and its rate is computed box-wise (per package).
    //         $packageWeights = $request->package_weights; // array of floats
    //         $isMultiPackage = is_array($packageWeights) && count($packageWeights) > 1;

    //         // Get the currently logged-in customer
    //         $customer = auth()->guard('customer')->user();
    //         $customerId = $customer ? $customer->id : 0;

    //         if (!$customer) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'You must be logged in to view rates.'
    //             ], 401);
    //         }

    //         // Guard: rates are only returned when the total weight is greater
    //         // than 0. If no actual weight has been entered, respond with an
    //         // empty rate list so the frontend shows nothing.
    //         if ($totalWeight <= 0) {
    //             return response()->json([
    //                 'success' => true,
    //                 'customer_exists' => false,
    //                 'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : null,
    //                 'all_rates' => [],
    //                 'message' => 'Please enter Actual Weight (Act. Wt) greater than 0 to view rates.',
    //             ]);
    //         }

    //         // Look up zone by consignee state (do this once for both modes)
    //         $zone = null;
    //         if (!empty($consigneeState)) {
    //             $zone = \App\Models\Zone::where('zone_code', $consigneeState)->first();
    //         }

    //         // Destination-based service filtering now uses the `country` column on
    //         // the courier_services table. The destination string is normalized to a
    //         // country code ("UK", "Canada", or "US") and compared against each
    //         // service's `country` value — a service is shown only when its country
    //         // matches the destination country.
    //         $destinationCountry = $this->resolveDestinationCountry($deliveryDestination);

    //         // ===================================================================
    //         // UNIFIED SINGLE-LOOP RATE CALCULATION
    //         // ===================================================================
    //         // Both modes (ALL-SERVICES and SINGLE-SERVICE) share the same per-service
    //         // work: country filter → multi-package filter → fetch rates → match by
    //         // weight + zone → build a rate card. The only difference is WHICH
    //         // services to loop over, so we build the collection up-front and run
    //         // one loop for both modes.
    //         // ===================================================================

    //         // Determine which services to process and whether this is single-service mode.
    //         $isSingleServiceMode = !empty($serviceId);

    //         if ($isSingleServiceMode) {
    //             // SINGLE-SERVICE MODE: load exactly one service by ID.
    //             $service = \App\Models\CourierService::find($serviceId);
    //             if (!$service) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'No matching service found for ID: ' . $serviceId
    //                 ], 404);
    //             }
    //             // Country check for the single service — reject early with a 404.
    //             if (strcasecmp($service->country ?? 'US', $destinationCountry) !== 0) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'This service is not available for the selected destination.'
    //                 ], 404);
    //             }
    //             $services = collect([$service]);
    //         } else {
    //             // ALL-SERVICES MODE: load every service, ordered for display.
    //             $services = \App\Models\CourierService::orderBy('network')->orderBy('method')->get();
    //         }

    //         $allRates = [];
    //         $customerRatesExist = false;   // true if ANY service had customer-specific rates
    //         $singleServiceModel = null;    // kept for SINGLE-SERVICE extra response fields

    //         foreach ($services as $service) {
    //             // -----------------------------------------------------------------
    //             // 1) COUNTRY FILTER — a service is shown only when its `country`
    //             //    column matches the destination country.
    //             //    (In single-service mode this was already checked above, but the
    //             //    loop runs it again harmlessly for consistency.)
    //             // -----------------------------------------------------------------
    //             if (strcasecmp($service->country ?? 'US', $destinationCountry) !== 0) {
    //                 continue; // country mismatch → skip this service
    //             }

    //             // -----------------------------------------------------------------
    //             // 2) MULTI-PACKAGE FILTER — when more than one package is present,
    //             //    only "United Ground Premium" service is offered.
    //             //    (DB stores the method as "UNITED GROUND PREMIUM" — compare
    //             //    case-insensitively so it matches regardless of casing.)
    //             // -----------------------------------------------------------------
    //             if ($isMultiPackage && strcasecmp($service->method, 'UNITED GROUND PREMIUM') !== 0) {
    //                 continue;
    //             }

    //             // -----------------------------------------------------------------
    //             // 3) FETCH RATES — customer-specific first, then default fallback.
    //             //    Priority: customer-specific rates (ALL zone_no values) first;
    //             //    if none exist, fall back to default rates (customer_id = 0).
    //             // -----------------------------------------------------------------
    //             $rates = \App\Models\CourierRate::where('customer_id', $customerId)
    //                 ->where('service_id', $service->id)
    //                 ->orderBy('wt_range_start')
    //                 ->get();

    //             if ($rates->isNotEmpty()) {
    //                 $customerRatesExist = true;
    //             }

    //             // If no customer-specific rates found, fall back to default rates.
    //             if ($rates->isEmpty() && $customerId !== 0) {
    //                 $rates = \App\Models\CourierRate::where('customer_id', 0)
    //                     ->where('service_id', $service->id)
    //                     ->orderBy('wt_range_start')
    //                     ->get();
    //             }

    //             // -----------------------------------------------------------------
    //             // 4) MATCH RATES BY WEIGHT + ZONE and BUILD RATE CARD(S).
    //             //    Two paths:
    //             //      a) BOX-WISE  — multi-package + United Ground Premium only.
    //             //      b) STANDARD — single package (or non-multi service).
    //             // -----------------------------------------------------------------
    //             $isGroundPremium = strcasecmp($service->method, 'UNITED GROUND PREMIUM') === 0;

    //             if ($isMultiPackage && $isGroundPremium) {
    //                 // ---------------------------------------------------------------
    //                 // 4a) BOX-WISE RATE CALCULATION (multi-package, United Ground Premium)
    //                 // ---------------------------------------------------------------
    //                 // Each package's chargeable weight is matched to its own rate row,
    //                 // and the base/fuel/gst amounts are summed into ONE combined rate
    //                 // card. The combined card carries a `box_breakdown` array so the
    //                 // frontend can render a per-box table, and `is_multi_package => true`.
    //                 // ---------------------------------------------------------------
    //                 $boxBreakdown = [];
    //                 $combinedBase = 0;
    //                 $combinedFuel = 0;
    //                 $combinedGst = 0;
    //                 $combinedTotal = 0;
    //                 $firstMatchedRate = null;
    //                 $boxIndex = 1;
    //                 $allBoxesMatched = true;

    //                 foreach ($packageWeights as $pkgWt) {
    //                     $pkgWt = floatval($pkgWt);
    //                     if ($pkgWt <= 0) {
    //                         $pkgWt = 1; // default 1kg if missing
    //                     }

    //                     // Find the rate row matching this package's weight + zone.
    //                     $boxMatched = null;
    //                     foreach ($rates as $r) {
    //                         if (!($pkgWt >= $r->wt_range_start && $pkgWt <= $r->wt_range_end)) {
    //                             continue;
    //                         }
    //                         $zoneNo = $r->zone_no;
    //                         if ($zoneNo === null || $zoneNo == 0) {
    //                             $boxMatched = $r;
    //                             break; // Zone-independent rate - use it
    //                         }
    //                         if ($zone && $zone->zone_number_testing !== null && $zoneNo == $zone->zone_number_testing) {
    //                             $boxMatched = $r;
    //                             break; // Zone-matched rate
    //                         }
    //                     }

    //                     if (!$boxMatched) {
    //                         $allBoxesMatched = false;
    //                         break; // a box has no matching rate → skip this service
    //                     }

    //                     if (!$firstMatchedRate) {
    //                         $firstMatchedRate = $boxMatched;
    //                     }

    //                     // Compute per-box amounts using the SAME formula as the frontend:
    //                     //   fuel = fuel_charge > 0 ? fuel_charge : (base * fuel_pct / 100)
    //                     //   gst  = gst_amount  > 0 ? gst_amount  : ((base + fuel) * gst_pct / 100)
    //                     $boxBase = floatval($boxMatched->price);
    //                     $boxFuelPct = floatval($boxMatched->fuel_percentage);
    //                     $boxFuelCharge = floatval($boxMatched->fuel_charge);
    //                     $boxGstPct = floatval($boxMatched->gst_percentage);
    //                     $boxGstAmount = floatval($boxMatched->gst_amount);

    //                     $boxFuel = $boxFuelCharge > 0 ? $boxFuelCharge : ($boxBase * $boxFuelPct / 100);
    //                     $boxGst = $boxGstAmount > 0 ? $boxGstAmount : (($boxBase + $boxFuel) * $boxGstPct / 100);
    //                     $boxTotal = $boxBase + $boxFuel + $boxGst;

    //                     $boxBreakdown[] = [
    //                         'box' => $boxIndex,
    //                         'weight' => $pkgWt,
    //                         'base' => $boxBase,
    //                         'fuel' => $boxFuel,
    //                         'gst' => $boxGst,
    //                         'total' => $boxTotal,
    //                     ];

    //                     $combinedBase += $boxBase;
    //                     $combinedFuel += $boxFuel;
    //                     $combinedGst += $boxGst;
    //                     $combinedTotal += $boxTotal;
    //                     $boxIndex++;
    //                 }

    //                 // Only emit a combined card if every box found a matching rate.
    //                 if ($allBoxesMatched && $firstMatchedRate) {
    //                     $allRates[] = [
    //                         'rate_id' => $firstMatchedRate->id,
    //                         'service_id' => $service->id,
    //                         'method' => $service->method,
    //                         'method_display' => $service->method . ' ' . $service->tat,
    //                         'network' => $service->network,
    //                         'method_code' => $service->method_code,
    //                         'tat' => $service->tat,
    //                         'delivery_days' => $service->tat,
    //                         'scode' => $service->scode,
    //                         // Combined amounts so the frontend shows the grand total.
    //                         'price' => $combinedBase,
    //                         'zone_no' => $firstMatchedRate->zone_no,
    //                         'wt_range_start' => $firstMatchedRate->wt_range_start,
    //                         'wt_range_end' => $firstMatchedRate->wt_range_end,
    //                         'zone_name' => ($firstMatchedRate->zone_no && $zone && $zone->zone_number_testing !== null && $firstMatchedRate->zone_no == $zone->zone_number_testing) ? $zone->zone_name : null,
    //                         'zone_code' => ($firstMatchedRate->zone_no && $zone && $zone->zone_number_testing !== null && $firstMatchedRate->zone_no == $zone->zone_number_testing) ? $zone->zone_code : null,
    //                         // Pass the already-computed fuel/gst as fixed amounts so the
    //                         // frontend does NOT recompute them from percentages (avoids
    //                         // double-counting). Percentages are zeroed out accordingly.
    //                         'fuel_charge' => $combinedFuel,
    //                         'fuel_percentage' => 0,
    //                         'gst_percentage' => 0,
    //                         'gst_amount' => $combinedGst,
    //                         // Multi-package extras for the frontend breakdown table.
    //                         'is_multi_package' => true,
    //                         'box_breakdown' => $boxBreakdown,
    //                     ];
    //                 }
    //             } else {
    //                 // ---------------------------------------------------------------
    //                 // 4b) STANDARD RATE MATCHING (single package, or non-multi service)
    //                 // ---------------------------------------------------------------
    //                 // Find rates matching the current weight AND the selected zone.
    //                 // Matching uses the zone's `zone_number_testing` field (compared
    //                 // against the rate's `zone_no`). Zone-independent rates
    //                 // (zone_no=null/0) are always shown weight-wise; zone-matched
    //                 // rates are shown when the rate's zone_no equals the selected
    //                 // zone's zone_number_testing.
    //                 // ---------------------------------------------------------------
    //                 $matchedRates = $rates->filter(function ($r) use ($totalWeight, $zone) {
    //                     if (!($totalWeight >= $r->wt_range_start && $totalWeight <= $r->wt_range_end)) {
    //                         return false;
    //                     }
    //                     $zoneNo = $r->zone_no;
    //                     if ($zoneNo === null || $zoneNo == 0) {
    //                         return true; // Zone-independent rate - always show
    //                     }
    //                     if ($zone && $zone->zone_number_testing !== null && $zoneNo == $zone->zone_number_testing) {
    //                         return true; // Zone-matched rate (via zone_number_testing)
    //                     }
    //                     return false; // Rate from a different zone - exclude
    //                 });

    //                 foreach ($matchedRates as $matchedRate) {
    //                     $allRates[] = [
    //                         'rate_id' => $matchedRate->id,
    //                         'service_id' => $service->id,
    //                         'method' => $service->method,
    //                         'method_display' => $service->method . ' ' . $service->tat,
    //                         'network' => $service->network,
    //                         'method_code' => $service->method_code,
    //                         'tat' => $service->tat,
    //                         'delivery_days' => $service->tat,
    //                         'scode' => $service->scode,
    //                         'price' => $matchedRate->price,
    //                         'zone_no' => $matchedRate->zone_no,
    //                         'wt_range_start' => $matchedRate->wt_range_start,
    //                         'wt_range_end' => $matchedRate->wt_range_end,
    //                         'zone_name' => ($matchedRate->zone_no && $zone && $zone->zone_number_testing !== null && $matchedRate->zone_no == $zone->zone_number_testing) ? $zone->zone_name : null,
    //                         'zone_code' => ($matchedRate->zone_no && $zone && $zone->zone_number_testing !== null && $matchedRate->zone_no == $zone->zone_number_testing) ? $zone->zone_code : null,
    //                         'fuel_charge' => $matchedRate->fuel_charge,
    //                         'fuel_percentage' => $matchedRate->fuel_percentage,
    //                         'gst_percentage' => $matchedRate->gst_percentage,
    //                         'gst_amount' => $matchedRate->gst_amount,
    //                     ];
    //                 }
    //             }

    //             // Remember the service model for SINGLE-SERVICE extra response fields.
    //             if ($isSingleServiceMode) {
    //                 $singleServiceModel = $service;
    //             }
    //         }

    //         // ===================================================================
    //         // 5) BUILD THE RESPONSE
    //         //    Both modes share the same base response. SINGLE-SERVICE mode
    //         //    additionally includes `matched_rate`, `service`, and
    //         //    `is_zone_independent` (built from the single service processed).
    //         // ===================================================================
    //         $response = [
    //             'success' => true,
    //             'customer_exists' => $customerRatesExist,
    //             'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : null,
    //             // i want to print destination country in response
    //             'destination_country' => $destinationCountry,
    //             'selected_zone' => $zone ? [
    //                 'zone_id' => $zone->id,
    //                 'zone_number' => $zone->zone_number_testing,
    //                 'zone_name' => $zone->zone_name,
    //                 'zone_code' => $zone->zone_code,
    //                 'state' => $consigneeState, // The state you selected
    //             ] : [
    //                 'state' => $consigneeState,
    //                 'message' => 'No zone found for the selected state'
    //             ],
    //             'all_rates' => $allRates,
    //         ];

    //         // SINGLE-SERVICE MODE extras: the first matched rate card, the service
    //         // metadata, and the zone-independence flag.
    //         if ($isSingleServiceMode && $singleServiceModel) {
    //             $isZoneIndependent = str_contains(strtoupper($singleServiceModel->method), 'AIREXPRESS');
    //             $response['is_zone_independent'] = $isZoneIndependent;
    //             $response['service'] = [
    //                 'network' => $singleServiceModel->network,
    //                 'method' => $singleServiceModel->method,
    //                 'type' => $singleServiceModel->type,
    //                 'tat' => $singleServiceModel->tat,
    //             ];
    //             $response['matched_rate'] = !empty($allRates) ? [
    //                 'rate_id' => $allRates[0]['rate_id'],
    //                 'zone_no' => $allRates[0]['zone_no'],
    //                 'wt_range_start' => $allRates[0]['wt_range_start'],
    //                 'wt_range_end' => $allRates[0]['wt_range_end'],
    //                 'price' => $allRates[0]['price'],
    //                 'fuel_charge' => $allRates[0]['fuel_charge'],
    //                 'fuel_percentage' => $allRates[0]['fuel_percentage'],
    //                 'gst_percentage' => $allRates[0]['gst_percentage'],
    //                 'gst_amount' => $allRates[0]['gst_amount'],
    //             ] : null;
    //         }

    //         return response()->json($response);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Server error: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

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
            return redirect()->route('login');
        }

        $customerId = auth()->guard('customer')->id();

        // Get all shipper IDs for this customer
        $shipperIds = ShipperInfo::where('customer_id', $customerId)->pluck('id');

        // Get all invoices and the selected rate used to calculate the complete shipping charge.
        $invoices = ShipmentInvoice::whereIn('shipper_id', $shipperIds)
            ->with([
                'invoiceItems',
                'shipperInfo.shipmentTracking',
                'shipperInfo.consigneeInfo',
                'shipperInfo.packageDimensions',
                'shipperInfo.serviceRate',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Prepare shipment details data for the detail modal (JS-friendly format)
        $shipmentDetails = $invoices->mapWithKeys(function($invoice) {
            $shipper = $invoice->shipperInfo;
            $consignee = $shipper ? $shipper->consigneeInfo : null;
            $tracking = $shipper ? $shipper->shipmentTracking : null;
            $packages = $shipper ? $shipper->packageDimensions : collect([]);
            $items = $invoice->invoiceItems;
            $selectedRate = $shipper ? $shipper->serviceRate : null;
            $displayAmount = $selectedRate
                ? (float) $selectedRate->inclusive_total
                : round((float) $invoice->total_amount, 2);

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
                    'items_total' => number_format($displayAmount, 2),
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
     * Show the Transaction History page for the logged-in customer.
     *
     * Lists all shipment invoices for the customer with payment status
     * (Paid / Unpaid / Cancelled), amounts, dates, and wallet balance summary.
     */
    public function transactionHistory()
    {
        // Check if customer is logged in
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customerId = auth()->guard('customer')->id();

        // Get all shipper IDs for this customer
        $shipperIds = ShipperInfo::where('customer_id', $customerId)->pluck('id');

        // Get all invoices for those shippers, with shipper info & consignee
        $invoices = ShipmentInvoice::whereIn('shipper_id', $shipperIds)
            ->with(['invoiceItems', 'shipperInfo.consigneeInfo'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter out unpaid invoices — only show paid and cancelled records
        $invoices = $invoices->filter(function ($inv) {
            if ($inv->status === 'cancelled') {
                return true;
            }
            // Paid if shipper status exists and is not 'draft'
            return ($inv->shipperInfo && $inv->shipperInfo->status && $inv->shipperInfo->status !== 'draft');
        })->values();

        // Compute payment summary
        $totalAmount = 0;
        $paidAmount = 0;
        $cancelledAmount = 0;

        foreach ($invoices as $inv) {
            $amount = (float) ($inv->total_amount ?? 0);
            $totalAmount += $amount;

            if ($inv->status === 'cancelled') {
                $cancelledAmount += $amount;
            } else {
                // Paid (status is ready, packed, manifested, dispatched, delivered, etc.)
                $paidAmount += $amount;
            }
        }

        // Get wallet balance
        $wallet = Wallet::where('customer_id', $customerId)->first();
        $walletBalance = $wallet ? (float) $wallet->balance : 0;

        return view('customer.transaction-history', compact(
            'invoices',
            'totalAmount',
            'paidAmount',
            'cancelledAmount',
            'walletBalance'
        ));
    }

    /**
     * Show the Wallet History page for the logged-in customer.
     *
     * Lists all wallet transactions (recharges, refunds, shipment charges)
     * for the customer with a date filter and summary cards.
     */
    public function walletHistory()
    {
        // Check if customer is logged in
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customerId = auth()->guard('customer')->id();

        // Get all wallet transactions for this customer, newest first
        $transactions = WalletTransaction::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Compute summary
        $totalRecharges = $transactions->where('reason', 'recharge')->sum(function ($t) {
            return (float) $t->amount;
        });
        $totalRefunds = $transactions->where('reason', 'refund')->sum(function ($t) {
            return (float) $t->amount;
        });
        $totalCharges = $transactions->where('type', 'debit')->sum(function ($t) {
            return (float) $t->amount;
        });

        // Get current wallet balance
        $wallet = Wallet::where('customer_id', $customerId)->first();
        $walletBalance = $wallet ? (float) $wallet->balance : 0;

        return view('customer.wallet-history', compact(
            'transactions',
            'totalRecharges',
            'totalRefunds',
            'totalCharges',
            'walletBalance'
        ));
    }

    /**
     * Show the customer's full profile page.
     *
     * Displays every detail of the logged-in customer including personal
     * info, Aadhar, GST, business/organization details, KYC status and
     * wallet balance.
     */
    public function myProfile()
    {
        // Check if customer is logged in
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('login');
        }

        $customer = auth()->guard('customer')->user();

        // Fetch KYC details (GST, organization, aadhar, etc.)
        $kyc = KycDetail::where('customer_id', $customer->id)->latest()->first();

        // Fetch wallet balance
        $wallet = Wallet::where('customer_id', $customer->id)->first();
        $walletBalance = $wallet ? (float) $wallet->balance : 0;

        // Fetch business category name
        $businessCategory = null;
        if ($customer->business_category_id) {
            $businessCategory = BusinessCategory::find($customer->business_category_id);
        }

        // Mask the Aadhar number for display (show only last 4 digits)
        $maskedAadhar = null;
        $aadharSource = null;
        if (!empty($customer->aadhar_number)) {
            $maskedAadhar = 'XXXX-XXXX-' . substr($customer->aadhar_number, -4);
            $aadharSource = 'customer';
        } elseif ($kyc && !empty($kyc->aadhar_number)) {
            $maskedAadhar = 'XXXX-XXXX-' . substr($kyc->aadhar_number, -4);
            $aadharSource = 'kyc';
        }

        // Determine Aadhar verification status
        $aadharVerified = (bool) ($customer->aadhar_verified || ($kyc && $kyc->aadhar_verified));

        // Determine GST verification status
        $gstVerified = $kyc ? (bool) $kyc->gst_verified : false;

        // Fetch CSB form (Business KYC) details if available
        $csbForm = CsbForm::where('customer_id', $customer->id)->latest()->first();

        // Mask the PAN number for display (show only last 4 characters)
        $maskedPan = null;
        $panSource = null;
        if (!empty($customer->pan_number)) {
            $maskedPan = 'XXXXXX' . substr($customer->pan_number, -4);
            $panSource = 'customer';
        } elseif ($kyc && !empty($kyc->pan_number)) {
            $maskedPan = 'XXXXXX' . substr($kyc->pan_number, -4);
            $panSource = 'kyc';
        }

        // Determine PAN verification status
        $panVerified = (bool) ($customer->pan_verified || ($kyc && $kyc->pan_verified));

        // Determine KYC status label & badge class
        $kycStatus = $kyc->kyc_status ?? 'pending';
        $kycStatusMap = [
            'pending'       => ['label' => 'Pending', 'class' => 'bg-warning'],
            'under_review'  => ['label' => 'Under Review', 'class' => 'bg-info'],
            'approved'      => ['label' => 'Approved', 'class' => 'bg-success'],
            'rejected'      => ['label' => 'Rejected', 'class' => 'bg-danger'],
        ];
        $kycStatusInfo = $kycStatusMap[$kycStatus] ?? ['label' => ucfirst($kycStatus), 'class' => 'bg-secondary'];

        // CSB status label
        $csbStatusMap = [
            0 => ['label' => 'Not Submitted', 'class' => 'bg-secondary'],
            1 => ['label' => 'Pending', 'class' => 'bg-warning'],
            2 => ['label' => 'Approved', 'class' => 'bg-success'],
            3 => ['label' => 'Rejected', 'class' => 'bg-danger'],
        ];
        $csbStatusVal = $customer->csb_status ?? 0;
        $csbStatusInfo = $csbStatusMap[$csbStatusVal] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];

        return view('customer.my-profile', compact(
            'customer',
            'kyc',
            'csbForm',
            'walletBalance',
            'businessCategory',
            'maskedAadhar',
            'aadharSource',
            'aadharVerified',
            'gstVerified',
            'maskedPan',
            'panSource',
            'panVerified',
            'kycStatusInfo',
            'csbStatusInfo'
        ));
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

            // Validate identifiers. The payable amount is always calculated from stored data.
            $validated = $request->validate([
                'invoice_id' => 'required|integer',
                'shipper_id' => 'required|integer',
                'amount' => 'nullable|numeric|min:0.01',
            ]);

            $invoiceId = $validated['invoice_id'];
            $shipperId = $validated['shipper_id'];

            // Find the shipper and selected rate, and verify ownership.
            $shipper = ShipperInfo::with('serviceRate')
                ->where('id', $shipperId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$shipper) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found or does not belong to you.'
                ], 403);
            }

            $invoice = ShipmentInvoice::where('id', $invoiceId)
                ->where('shipper_id', $shipper->id)
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found for this shipment.'
                ], 404);
            }

            $amount = $shipper->serviceRate
                ? $shipper->serviceRate->inclusive_total
                : round((float) $invoice->total_amount, 2);

            if ($amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'A valid shipment charge could not be calculated.'
                ], 422);
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
            DB::transaction(function () use ($wallet, $amount, $shipper, $customerId) {
                $wallet->decrement('balance', $amount);
                $wallet->refresh();
                $shipper->status = 'ready';
                $shipper->save();

                // Log the wallet debit (shipment charge)
                WalletTransaction::create([
                    'customer_id'   => $customerId,
                    'type'          => 'debit',
                    'reason'        => 'shipment_charge',
                    'amount'        => $amount,
                    'balance_after' => $wallet->balance,
                    'reference'     => $shipper->awb_number,
                    'description'   => 'Payment of ₹' . number_format($amount, 2) . ' for shipment ' . ($shipper->awb_number ?: '#' . $shipper->id),
                ]);
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

            // Log the ready status change (payment confirmed)
            ShipmentLog::logStatus(
                $shipper->id,
                $shipper->awb_number,
                'ready',
                'draft',
                'Payment confirmed. Amount ₹' . number_format($amount, 2) . ' deducted from wallet.',
                $customerId,
                'customer'
            );

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

            // Find the invoice and verify it belongs to this customer.
            $invoice = ShipmentInvoice::findOrFail($id);

            // Load the selected rate so refunds match the shipping charge that was paid.
            $shipper = ShipperInfo::with('serviceRate')
                ->where('id', $invoice->shipper_id)
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

            // Determine whether this shipment has a charge to refund back to the wallet.
            // Draft, ready and packed shipments all carry a shipping amount that should be returned.
            $wasPaid = in_array($shipper->status, ['draft', 'ready', 'packed', 'manifested']);
            $previousStatus = $shipper->status;
            $refundAmount = $shipper->serviceRate
                ? $shipper->serviceRate->inclusive_total
                : round((float) $invoice->total_amount, 2);

            // Update status to cancelled and refund wallet when a charge exists.
            DB::transaction(function () use ($invoice, $shipper, $wasPaid, $previousStatus, $customerId, &$refundAmount) {
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

                // Log the cancelled status change
                ShipmentLog::logStatus(
                    $shipper->id,
                    $shipper->awb_number,
                    'cancelled',
                    $previousStatus,
                    $refundAmount > 0 ? 'Shipment cancelled. Refund ₹' . number_format($refundAmount, 2) . ' to wallet.' : 'Shipment cancelled.',
                    $customerId,
                    'customer'
                );

                if ($refundAmount > 0) {
                    // A cancelled draft/ready/packed shipment should return the shipping amount to the wallet.
                    $wallet = Wallet::where('customer_id', $customerId)->first();
                    if ($wallet) {
                        $wallet->increment('balance', $refundAmount);
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

            // Log the packed status change
            ShipmentLog::logStatus(
                $shipper->id,
                $shipper->awb_number,
                'packed',
                'ready',
                'Shipment marked as packed.',
                $customerId,
                'customer'
            );

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

            // Resolve the API provider: database-first (courier_services.api_provider)
            // with a fallback to the legacy string-matching methods.
            $apiProvider = $this->resolveApiProvider($shippingMethod, $shipper, $courierService);

            \Log::info('manifestShipment: Shipper #' . $shipperId . ' → shipping_method="' . $shippingMethod . '" → network="' . $network . '" → api_provider="' . $apiProvider . '"');

            // Route to appropriate API based on the resolved provider.
            if ($apiProvider === 'shipuniversal') {
                $shipUniversalResult = $this->callShipUniversalApiFromDb($shipper);
                if (!$shipUniversalResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ShipUniversal API Failed: ' . ($shipUniversalResult['message'] ?? 'Unknown error'),
                        'shipuniversal_response' => $shipUniversalResult['data'] ?? null,
                        'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                    ], 500);
                }

                $apiResponse = $shipUniversalResult['data'] ?? [];
                $trackingNumber = $this->extractShipUniversalTrackingNumber($apiResponse);
                $labelUrl = $this->extractShipUniversalLabelUrl($apiResponse);

                if (empty($trackingNumber)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ShipUniversal created no usable AWB number. The shipment remains packed.',
                        'shipuniversal_response' => $apiResponse,
                        'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                    ], 502);
                }

                try {
                    $this->persistShipUniversalManifest(
                        $shipper,
                        $customerId,
                        $apiResponse,
                        $trackingNumber,
                        $labelUrl,
                        false
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to store ShipUniversal manifest: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to store tracking data: ' . $e->getMessage(),
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via ShipUniversal!',
                    'tracking_number' => $trackingNumber,
                    'label_url' => $labelUrl,
                    'shipper_id' => $shipperId,
                    'network' => 'ShipUniversal',
                    'shipuniversal_response' => $apiResponse,
                    'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                ]);
            // Priority 0: Overseas Logistic for UNITED CANADA DDP /
            //              UNITED CANADA E-COMMERCE and ARAMEX GPX (Australia).
            } elseif ($apiProvider === 'overseas' || $this->isOverseasLogisticMethod($shippingMethod)) {
                // Call Overseas Logistic API
                $overseasResult = $this->callOverseasLogisticApiFromDb($shipper);
                if (!$overseasResult['success']) {
                    $overseasMsg = $this->overseasValueToString($overseasResult['message'] ?? 'Unknown error');
                    return response()->json([
                        'success' => false,
                        'message' => 'Overseas Logistic API Failed: ' . $overseasMsg,
                        'overseas_response' => $overseasResult['data'] ?? null,
                        'request_payload' => $overseasResult['request_payload'] ?? null,
                    ], 500);
                }

                // Overseas Logistic succeeded - store tracking data
                $apiResponse = $overseasResult['data'] ?? [];
                $trackingNumber = $this->extractOverseasTrackingNumber($apiResponse);
                $labelUrl = $this->extractOverseasLabelUrl($apiResponse);

                $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                try {
                    ShipmentTracking::updateOrCreate(
                        ['shipper_id' => $shipperId],
                        [
                            'customer_id' => $customerId,
                            'create_shipment_id' => $createShipment ? $createShipment->id : null,
                            'response_status_code' => '1',
                            'response_status_description' => 'Overseas Logistic shipment created',
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

                    // Log the manifested status change
                    ShipmentLog::logStatus(
                        $shipper->id,
                        $shipper->awb_number,
                        'manifested',
                        'packed',
                        'Shipment manifested via Overseas Logistic. Tracking: ' . ($trackingNumber ?? 'N/A'),
                        $customerId,
                        'customer'
                    );

                    \Log::info('Shipment manifested via Overseas Logistic: ' . ($trackingNumber ?? 'N/A'));
                } catch (\Exception $e) {
                    \Log::error('Failed to store shipment tracking for Overseas Logistic manifest: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Failed to store tracking data: ' . $e->getMessage()], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Shipment manifested successfully via Overseas Logistic!',
                    'tracking_number' => $trackingNumber,
                    'label_url' => $labelUrl,
                    'shipper_id' => $shipperId,
                    'network' => 'Overseas Logistic',
                    'overseas_response' => $apiResponse,
                    'request_payload' => $overseasResult['request_payload'] ?? null,
                ]);
            } elseif ($apiProvider === 'postshipping' || $this->isPostShippingMethod($shippingMethod)) {
                // Priority 1: PostShipping (DPD/UK) for UNITED AIR PREMIUM DDP / UNITED PRIOR POST DDP
                // Call PostShipping API
                $postShippingResult = $this->callPostShippingApiFromDb($shipper);
                if (!$postShippingResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PostShipping API Failed: ' . ($postShippingResult['message'] ?? 'Unknown error'),
                        'postshipping_response' => $postShippingResult['data'] ?? null,
                        'request_payload' => $postShippingResult['request_payload'] ?? null,
                    ], 500);
                }

                // PostShipping succeeded - store tracking data
                $apiResponse = $postShippingResult['data'] ?? [];
                $trackingNumber = $this->extractPostShippingTrackingNumber($apiResponse);
                $labelUrl = $this->extractPostShippingLabelUrl($apiResponse);
                // Call PostShipping API
                $postShippingResult = $this->callPostShippingApiFromDb($shipper);
                if (!$postShippingResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PostShipping API Failed: ' . ($postShippingResult['message'] ?? 'Unknown error'),
                        'postshipping_response' => $postShippingResult['data'] ?? null,
                        'request_payload' => $postShippingResult['request_payload'] ?? null,
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
                    'request_payload' => $postShippingResult['request_payload'] ?? null,
                ]);
            } elseif ($apiProvider === 'flyingtigers' || $this->isFlyingTigersMethod($shippingMethod)) {
                // Call Flying Tigers API (UNITED ECO POST)
                $flyingTigersResult = $this->callFlyingTigersApiFromDb($shipper);
                if (!$flyingTigersResult['success']) {
                    // Check if this is an address error → return fallback info for dropdown option
                    if (!empty($flyingTigersResult['is_address_error'])) {
                        $fallbackInfo = $this->getFlyingTigersAddressErrorFallbackInfo($shipper, $customerId);
                        return response()->json([
                            'success'          => false,
                            'message'          => 'The address provided appears to be incorrect or incomplete for UNITED ECO POST. You can ship via UNITED CLASSIC (Ship Global) instead.',
                            'is_address_error' => true,
                            'shipper_id'       => $shipperId,
                            'classic_rate'     => $fallbackInfo['classic_rate'] ?? null,
                            'paid_amount'      => $fallbackInfo['paid_amount'] ?? null,
                            'difference'       => $fallbackInfo['difference'] ?? null,
                            'wallet_action'    => $fallbackInfo['wallet_action'] ?? 'none',
                            'wallet_amount'    => $fallbackInfo['wallet_amount'] ?? 0,
                            'wallet_balance'   => $fallbackInfo['wallet_balance'] ?? 0,
                            'total_weight'     => $fallbackInfo['total_weight'] ?? 0,
                        ], 422);
                    }
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
            } elseif ($apiProvider === 'shipglobal' || $network === 'ship global' || $network === 'shipglobal') {
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

                    // Log the manifested status change
                    ShipmentLog::logStatus(
                        $shipper->id,
                        $shipper->awb_number,
                        'manifested',
                        'packed',
                        'Shipment manifested via UPS. Tracking: ' . ($trackingNumber ?? 'N/A'),
                        $customerId,
                        'customer'
                    );

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

                    // Resolve the API provider: database-first (courier_services.api_provider)
                    // with a fallback to the legacy string-matching methods.
                    $apiProvider = $this->resolveApiProvider($shippingMethod, $shipper, $courierService);

                    \Log::info('bulkManifest: Shipper #' . $shipperId . ' → network="' . $network . '" → api_provider="' . $apiProvider . '"');

                    if ($apiProvider === 'shipuniversal') {
                        $shipUniversalResult = $this->callShipUniversalApiFromDb($shipper);
                        if (!$shipUniversalResult['success']) {
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'ShipUniversal API error: ' . ($shipUniversalResult['message'] ?? 'Unknown'),
                                'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                                'shipuniversal_response' => $shipUniversalResult['data'] ?? null,
                            ];
                            continue;
                        }

                        $apiResponse = $shipUniversalResult['data'] ?? [];
                        $trackingNumber = $this->extractShipUniversalTrackingNumber($apiResponse);
                        $labelUrl = $this->extractShipUniversalLabelUrl($apiResponse);

                        if (empty($trackingNumber)) {
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'ShipUniversal created no usable AWB number. The shipment remains packed.',
                                'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                                'shipuniversal_response' => $apiResponse,
                            ];
                            continue;
                        }

                        $this->persistShipUniversalManifest(
                            $shipper,
                            $customerId,
                            $apiResponse,
                            $trackingNumber,
                            $labelUrl,
                            true
                        );

                        $results['success'][] = [
                            'shipper_id' => $shipperId,
                            'tracking_number' => $trackingNumber,
                            'label_url' => $labelUrl,
                            'network' => 'ShipUniversal',
                            'request_payload' => $shipUniversalResult['request_payload'] ?? null,
                        ];

                        \Log::info('Bulk manifest: shipment ' . $shipperId . ' manifested via ShipUniversal.');
                    // Priority 0: Overseas Logistic for UNITED CANADA DDP /
                    //              UNITED CANADA E-COMMERCE and ARAMEX GPX (Australia).
                    } elseif ($apiProvider === 'overseas' || $this->isOverseasLogisticMethod($shippingMethod)) {
                        // Call Overseas Logistic API
                        $overseasResult = $this->callOverseasLogisticApiFromDb($shipper);

                        if (!$overseasResult['success']) {
                            $overseasMsg = $this->overseasValueToString($overseasResult['message'] ?? 'Unknown');
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'Overseas Logistic API error: ' . $overseasMsg,
                                'request_payload' => $overseasResult['request_payload'] ?? null,
                                'overseas_response' => $overseasResult['data'] ?? null,
                            ];
                            continue;
                        }

                        $apiResponse = $overseasResult['data'] ?? [];
                        $trackingNumber = $this->extractOverseasTrackingNumber($apiResponse);
                        $labelUrl = $this->extractOverseasLabelUrl($apiResponse);

                        $createShipment = CreateShipment::where('shipper_id', $shipperId)->first();

                        ShipmentTracking::updateOrCreate(
                            ['shipper_id' => $shipperId],
                            [
                                'customer_id' => $customerId,
                                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                                'response_status_code' => '1',
                                'response_status_description' => 'Overseas Logistic shipment created',
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
                            'network' => 'Overseas Logistic',
                            'request_payload' => $overseasResult['request_payload'] ?? null,
                        ];

                        \Log::info('Bulk manifest: shipment ' . $shipperId . ' manifested via Overseas Logistic.');

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', 'packed', 'Shipment manifested via Overseas Logistic (bulk). Tracking: ' . ($trackingNumber ?? 'N/A'), $customerId, 'customer');

                    } elseif ($apiProvider === 'postshipping' || $this->isPostShippingMethod($shippingMethod)) {
                        // Priority 1: PostShipping (DPD/UK) for UNITED AIR PREMIUM DDP / UNITED PRIOR POST DDP
                        // Call PostShipping API
                        $postShippingResult = $this->callPostShippingApiFromDb($shipper);

                        if (!$postShippingResult['success']) {
                            $results['failed'][] = [
                                'shipper_id' => $shipperId,
                                'message' => 'PostShipping API error: ' . ($postShippingResult['message'] ?? 'Unknown'),
                                'request_payload' => $postShippingResult['request_payload'] ?? null,
                                'postshipping_response' => $postShippingResult['data'] ?? null,
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
                            'request_payload' => $postShippingResult['request_payload'] ?? null,
                        ];

                        \Log::info('Bulk manifest: shipment ' . $shipperId . ' manifested via PostShipping.');

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', 'packed', 'Shipment manifested via PostShipping (bulk). Tracking: ' . ($trackingNumber ?? 'N/A'), $customerId, 'customer');

                    } elseif ($apiProvider === 'flyingtigers' || $this->isFlyingTigersMethod($shippingMethod)) {
                        // Call Flying Tigers API (UNITED ECO POST)
                        $flyingTigersResult = $this->callFlyingTigersApiFromDb($shipper);

                        if (!$flyingTigersResult['success']) {
                            // Check if this is an address error → return fallback info for dropdown option
                            if (!empty($flyingTigersResult['is_address_error'])) {
                                $fallbackInfo = $this->getFlyingTigersAddressErrorFallbackInfo($shipper, $customerId);
                                $results['address_errors'][] = [
                                    'shipper_id'       => $shipperId,
                                    'message'           => 'Address is incorrect for UNITED ECO POST. You can ship via UNITED CLASSIC (Ship Global) instead.',
                                    'is_address_error' => true,
                                    'classic_rate'      => $fallbackInfo['classic_rate'] ?? null,
                                    'paid_amount'       => $fallbackInfo['paid_amount'] ?? null,
                                    'difference'        => $fallbackInfo['difference'] ?? null,
                                    'wallet_action'    => $fallbackInfo['wallet_action'] ?? 'none',
                                    'wallet_amount'    => $fallbackInfo['wallet_amount'] ?? 0,
                                    'wallet_balance'   => $fallbackInfo['wallet_balance'] ?? 0,
                                    'total_weight'     => $fallbackInfo['total_weight'] ?? 0,
                                ];
                                \Log::info('Bulk manifest: shipment ' . $shipperId . ' address error — awaiting customer decision for UNITED CLASSIC fallback.');
                                continue;
                            }
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

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', 'packed', 'Shipment manifested via Flying Tigers (bulk). Tracking: ' . ($trackingNumber ?? 'N/A'), $customerId, 'customer');

                    } elseif ($apiProvider === 'shipglobal' || $network === 'ship global' || $network === 'shipglobal') {
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

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', 'packed', 'Shipment manifested via Ship Global (bulk). Tracking: ' . ($trackingNumber ?? 'N/A'), $customerId, 'customer');

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

                        ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', 'packed', 'Shipment manifested via UPS (bulk). Tracking: ' . ($trackingNumber ?? 'N/A'), $customerId, 'customer');
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
     * Determine if a courier service is a Canada-only service.
     * Canada services (CANADA-DDP, CANADA-ECOM) are only shown when the
     * delivery destination is Canada, and are hidden for all other destinations.
     *
     * @param \App\Models\CourierService|string|null $service
     * @return bool
     */
    private function isCanadaService($service)
    {
        if (empty($service)) {
            return false;
        }

        // Accept either a CourierService model or a plain method string.
        if (is_object($service)) {
            $network = strtoupper(trim($service->network ?? ''));
            $serviceCode = strtoupper(trim($service->service_code ?? ''));
            $method = strtoupper(trim($service->method ?? ''));
        } else {
            $network = '';
            $serviceCode = '';
            $method = strtoupper(trim((string) $service));
        }

        // Match by network, service_code, or method name (case-insensitive).
        return $network === 'CANADA'
            || str_starts_with($serviceCode, 'CANADA-')
            || str_contains($method, 'UNITED CANADA');
    }

    /**
     * Normalize a delivery-destination name, code, or numeric destination ID
     * into the short code used by the courier_services.country column.
     *
     * Returns one of: "UK", "CA", "AUS", "UAE", "NZ", "SG", "MY", "US".
     *
     * The destination string can arrive in several formats depending on the
     * caller:
     *   - getUpsRate() frontend dropdown  → "UK - United Kingdom", "Canada", "US"
     *   - previewBulkUpload() Excel cell → "UK", "GB", "United Kingdom",
     *                                       "Great Britain", "Canada", "CA", ...
     *
     * Anything that is not recognised as UK, Canada or Australia is treated
     * as "US".
     *
     * @param string|null $destination
     * @return string  "UK" | "CA" | "AUS" | "UAE" | "NZ" | "SG" | "MY" | "US"
     */
    private function resolveDestinationCountry($destination)
    {
        $destinationValue = trim((string) ($destination ?? ''));
        if ($destinationValue === '') {
            return 'US';
        }

        // Resolve numeric dropdown/API values through the destination record so
        // country selection never depends on a display-name fallback.
        if (ctype_digit($destinationValue)) {
            $destinationRecord = \App\Models\Destination::find((int) $destinationValue);
            if ($destinationRecord) {
                $destinationValue = $destinationRecord->country_code
                    ?: $destinationRecord->code
                    ?: $destinationRecord->name;
            }
        }

        $destUpper = strtoupper($destinationValue);

        // UK detection — covers "UK", "GB", "United Kingdom", "UK - United Kingdom",
        // "Great Britain", and any string starting with "UK -".
        $isUk = (
            $destUpper === 'UK'
            || $destUpper === 'GB'
            || str_contains($destUpper, 'UNITED KINGDOM')
            || str_starts_with($destUpper, 'UK -')
            || str_contains($destUpper, 'GREAT BRITAIN')
        );
        if ($isUk) {
            return 'UK';
        }

        // Canada detection — covers "Canada", "CA", and any string containing "Canada".
        $isCanada = (
            $destUpper === 'CANADA'
            || $destUpper === 'CA'
            || str_contains($destUpper, 'CANADA')
        );
        if ($isCanada) {
            return 'CA';
        }

        // Australia detection — covers "Australia", "AU", "AUS",
        // and any string containing "Australia". Returns "AUS" to match the
        // destinations.country_code value used for Australia.
        $isAustralia = (
            $destUpper === 'AUSTRALIA'
            || $destUpper === 'AU'
            || $destUpper === 'AUS'
            || str_contains($destUpper, 'AUSTRALIA')
        );
        if ($isAustralia) {
            return 'AUS';
        }

        // UAE detection also covers the destination display name "Dubai".
        $isUae = (
            // $destUpper === 'DUBAI'
            $destUpper === 'UNITED ARAB EMIRATES'
            || $destUpper === 'UAE'
            || $destUpper === 'AE'
            || $destUpper === 'ARE'
            || str_contains($destUpper, 'DUBAI')
            || str_contains($destUpper, 'UNITED ARAB EMIRATES')
        );

        if ($isUae) {
            return 'UAE';
        }


        // i want to add newzealand as well so i am adding it here
        // New Zealand detection — covers "New Zealand", "NZ", "NZL",
        // and any string containing "New Zealand". Returns "NZ" to match the
        // destinations.country_code value used for New Zealand.
        $isNewZealand = (
            $destUpper === 'NEW ZEALAND'
            || $destUpper === 'NZ'
            || $destUpper === 'NZL'
            || str_contains($destUpper, 'NEW ZEALAND')
        );
        if ($isNewZealand) {
            return 'NZ';
        }

        // Singapore detection — covers "Singapore", "SG", and "SGP".
        $isSingapore = (
            $destUpper === 'SINGAPORE'
            || $destUpper === 'SG'
            || $destUpper === 'SGP'
            || str_contains($destUpper, 'SINGAPORE')
        );
        if ($isSingapore) {
            return 'SG';
        }

        // Malaysia detection — covers "Malaysia", "MY", and "MYS".
        $isMalaysia = (
            $destUpper === 'MALAYSIA'
            || $destUpper === 'MY'
            || $destUpper === 'MYS'
            || str_contains($destUpper, 'MALAYSIA')
        );
        if ($isMalaysia) {
            return 'MY';
        }

        // i want to implement germany destination
        $isGermany = (
            $destUpper === 'GERMANY'
            || $destUpper === 'DE'
            || $destUpper === 'DEU'
            || str_contains($destUpper, 'GERMANY')
        );
        if($isGermany){
            return 'DE';
        }

        // Everything else (US, USA, United States, etc.) → US.
        return 'US';
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
     * Map a PostShipping ServiceTypeName to the DPD UK NetworkCode.
     *
     * The DPD UK API validates consignment.networkCode (error 1021 "Service Denied"
     * when missing). NetworkCode identifies the delivery service network:
     *   - DPD111 (Offshore - Two Day)        → "7"
     *   - DPDUKEPND / DPD112 / MDPD112       → "1" (Next Day mainland)
     *
     * @param string $serviceTypeName
     * @return string
     */
    private function getPostShippingNetworkCode($serviceTypeName)
    {
        $code = strtoupper(trim((string) $serviceTypeName));

        // Offshore (DPD OFFSHORE - TWO DAY) uses network code "7"
        if ($code === 'DPD111') {
            return '7';
        }

        // All mainland next-day services (DPDUKEPND, DPD112, MDPD112) use network code "1"
        return '1';
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
                'NetworkCode'           => $this->getPostShippingNetworkCode($serviceTypeName),
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
                    'success'         => false,
                    'message'         => $errorMessage,
                    'data'            => $apiResponse,
                    'request_payload' => $payload,
                    'status_code'     => $response->status(),
                ];
            }

            \Log::info('PostShipping response for shipper #' . $shipper->id . ': ' . substr($response->body(), 0, 2000));

            // The DPD/PostShipping API may return HTTP 200 but still reject the
            // shipment by embedding an error inside the "ErrMessage" field of
            // each consignment object (e.g. "Service Denied (1021) - ...").
            // Detect this so the caller treats it as a failure instead of success.
            $errMessage = $this->extractPostShippingErrorMessage($apiResponse);
            if ($errMessage !== null) {
                \Log::error('PostShipping API rejected shipment (ErrMessage): ' . $errMessage . ' | Body: ' . $response->body());
                return [
                    'success'         => false,
                    'message'         => 'PostShipping API rejected shipment: ' . $errMessage,
                    'data'            => $apiResponse,
                    'request_payload' => $payload,
                    'status_code'     => $response->status(),
                ];
            }

            return [
                'success'         => true,
                'message'          => 'PostShipping shipment created successfully.',
                'data'             => $apiResponse,
                'request_payload'  => $payload,
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
     * Detect an embedded error in a PostShipping API response.
     *
     * The DPD/PostShipping API sometimes returns HTTP 200 but rejects the
     * shipment by placing an "ErrMessage" string inside each consignment
     * object of the response array. This method scans the response for any
     * non-empty ErrMessage and returns the first one found (trimmed), or
     * null when the response is genuinely successful.
     *
     * Supported response shapes:
     *   - Indexed array of consignments: [ {ErrMessage: "..."}, ... ]
     *   - Single object: {ErrMessage: "..."}
     *   - Nested under a "data" / "shipments" key
     *
     * @param mixed $apiResponse
     * @return string|null
     */
    private function extractPostShippingErrorMessage($apiResponse)
    {
        if (!is_array($apiResponse)) {
            return null;
        }

        // Candidate containers that may hold consignment objects.
        $containers = [];

        // Top-level indexed array of consignments.
        $hasStringKeys = false;
        foreach (array_keys($apiResponse) as $k) {
            if (is_string($k)) {
                $hasStringKeys = true;
                break;
            }
        }
        if (!$hasStringKeys) {
            $containers[] = $apiResponse;
        }

        // Common nested keys.
        foreach (['data', 'shipments', 'Shipment', 'Shipments', 'consignment', 'consignments'] as $key) {
            if (isset($apiResponse[$key]) && is_array($apiResponse[$key])) {
                $containers[] = $apiResponse[$key];
            }
        }

        // If the response itself looks like a single consignment object.
        if (isset($apiResponse['ErrMessage'])) {
            $containers[] = [$apiResponse];
        }

        foreach ($containers as $container) {
            foreach ($container as $item) {
                if (!is_array($item)) {
                    continue;
                }
                if (!empty($item['ErrMessage']) && is_string($item['ErrMessage'])) {
                    $msg = trim($item['ErrMessage']);
                    if ($msg !== '') {
                        return $msg;
                    }
                }
            }
        }

        return null;
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
     * Determine if a shipping method should be routed to the Overseas Logistic API.
     * Triggered for Canada shipments created with the methods:
     *   - UNITED CANADA DDP
     *   - UNITED CANADA E-COMMERCE
     *
     * Both variants use the same Overseas Logistic endpoint and payload; the
     * Service field inside ServiceDetails differentiates the exact service.
     *
     * @param string|null $shippingMethod
     * @return bool
     */
    private function isCanadaOverseasMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return false;
        }

        $methodUpper = strtoupper(trim($shippingMethod));

        // Match "UNITED CANADA" with either "DDP" or "E-COMMERCE"/"ECOMMERCE".
        $isCanada = str_contains($methodUpper, 'UNITED CANADA');
        $isDdpOrEcom = str_contains($methodUpper, 'DDP')
            || str_contains($methodUpper, 'E-COMMERCE')
            || str_contains($methodUpper, 'ECOMMERCE')
            || str_contains($methodUpper, 'E COMMERCE');

        return $isCanada && $isDdpOrEcom;
    }

    /**
     * Determine if a shipping method should be routed to the Overseas
     * Logistic API for Australia shipments.
     *
     * Triggered for the "ARAMEX GPX ALL IN" service (Australia), whose
     * courier_services.service_code is "AUSTRALIA-ARAMEX-GPX", and for the
     * "DPEX_AU_EXPRESS" service (Australia), whose courier_services.service_code
     * is "DPEX_AU_EXPRESS". The same Overseas Logistic endpoint/payload is
     * reused; the Service field inside ServiceDetails (resolved from
     * courier_services.service_code) differentiates the exact service, and
     * ReceiverCountry is set to "AU" via getOverseasCountryCode().
     *
     * @param string|null $shippingMethod
     * @return bool
     */
    private function isAustraliaOverseasMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return false;
        }

        $methodUpper = strtoupper(trim($shippingMethod));

        // Match "ARAMEX GPX" (the Australia service method is
        // "ARAMEX GPX ALL IN"). Matching on "ARAMEX GPX" is intentionally
        // tolerant of the "ALL IN" suffix so future variants still route
        // correctly.
        //
        // Also match "DPEX_AU_EXPRESS" / "DPEX AU EXPRESS" — the Australia
        // DPEX express service. Matching is tolerant of underscores vs
        // spaces so both the service_code and human-readable variants route
        // through the Overseas Logistic API.
        return str_contains($methodUpper, 'ARAMEX GPX')
            || str_contains($methodUpper, 'DPEX_AU_EXPRESS')
            || str_contains($methodUpper, 'DPEX AU EXPRESS');
    }

    /**
     * Determine if a shipping method should be routed to the Overseas
     * Logistic API at all (Canada OR Australia variants).
     *
     * Centralises the Overseas-routing decision so both manifestShipment()
     * and bulkManifestShipments() stay in sync.
     *
     * @param string|null $shippingMethod
     * @return bool
     */
    private function isOverseasLogisticMethod($shippingMethod)
    {
        return $this->isCanadaOverseasMethod($shippingMethod)
            || $this->isAustraliaOverseasMethod($shippingMethod);
    }

    /**
     * Resolve which external API provider should handle a shipment.
     *
     * Database-first with fallback:
     *   1. If the matched CourierService has a non-empty `api_provider`
     *      column, that value wins (e.g. "shipuniversal", "overseas",
     *      "postshipping", "flyingtigers", "shipglobal", "ups").
     *   2. Otherwise, fall back to the legacy string-matching methods so
     *      existing services keep working until their rows are populated.
     *
     * This centralises the provider decision so both manifestShipment()
     * and bulkManifestShipments() stay in sync, and lets admins control
     * routing per-service from the courier_services table instead of
     * editing controller code.
     *
     * @param string|null $shippingMethod
     * @param \App\Models\ShipperInfo $shipper
     * @param \App\Models\CourierService|null $courierService  Optional
     *        pre-resolved service to avoid a redundant lookup.
     * @return string  One of: shipuniversal, overseas, postshipping,
     *                 flyingtigers, shipglobal, ups.
     */
    private function resolveApiProvider($shippingMethod, $shipper, $courierService = null)
    {
        // Default provider when nothing else matches.
        $fallback = 'ups';

        // Reuse a pre-resolved service when the caller already has one;
        // otherwise look it up now.
        if (!$courierService) {
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
        }

        if ($courierService) {
            $provider = strtolower(trim($courierService->api_provider ?? ''));
            if (!empty($provider)) {
                return $provider;
            }

            // No explicit api_provider — derive the network for the
            // ShipUniversal/ShipGlobal/UPS fallback branches below.
            $network = strtolower(trim($courierService->network ?? ''));
        } else {
            $network = '';
        }

        // Legacy fallback chain (mirrors the original if/elseif order).
        if ($network === 'ship universal' || $network === 'shipuniversal') {
            return 'shipuniversal';
        }
        if ($this->isOverseasLogisticMethod($shippingMethod)) {
            return 'overseas';
        }
        if ($this->isPostShippingMethod($shippingMethod)) {
            return 'postshipping';
        }
        if ($this->isFlyingTigersMethod($shippingMethod)) {
            return 'flyingtigers';
        }
        if ($network === 'ship global' || $network === 'shipglobal') {
            return 'shipglobal';
        }

        return $fallback;
    }

    /*
    |--------------------------------------------------------------------------
    | ShipUniversal API
    |--------------------------------------------------------------------------
    | Token URL uses HTTP Basic Auth. The shipment-create URL receives the
    | generated token as a Bearer token.
    */

    /**
     * Persist a successful ShipUniversal manifest using the common tracking tables.
     */
    private function persistShipUniversalManifest(
        $shipper,
        $customerId,
        array $apiResponse,
        $trackingNumber,
        $labelUrl,
        $isBulk = false
    ) {
        $createShipment = CreateShipment::where('shipper_id', $shipper->id)->first();

        ShipmentTracking::updateOrCreate(
            ['shipper_id' => $shipper->id],
            [
                'customer_id' => $customerId,
                'create_shipment_id' => $createShipment ? $createShipment->id : null,
                'response_status_code' => '1',
                'response_status_description' => 'ShipUniversal shipment created',
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

        Tracking::firstOrCreate(
            [
                'shipper_id' => $shipper->id,
                'status' => 'manifested',
            ],
            [
                'awb_number' => $shipper->awb_number,
                'shipping_id' => $createShipment ? $createShipment->id : null,
                'uwc_id' => $shipper->awb_number,
                'title' => Tracking::getTitleForStatus('manifested'),
            ]
        );

        ShipmentLog::logStatus(
            $shipper->id,
            $shipper->awb_number,
            'manifested',
            'packed',
            'Shipment manifested via ShipUniversal'
                . ($isBulk ? ' (bulk)' : '')
                . '. Tracking: ' . ($trackingNumber ?? 'N/A'),
            $customerId,
            'customer'
        );
    }

    /**
     * Generate a Bearer token using ShipUniversal HTTP Basic authentication.
     *
     * @return array ['success' => bool, 'token' => string|null, 'message' => string|null]
     */
    private function getShipUniversalToken()
    {
        try {
            $tokenUrl = config('services.shipuniversal.token_url');
            $username = config('services.shipuniversal.username');
            $password = config('services.shipuniversal.password');
            $grantType = config('services.shipuniversal.grant_type', 'client_credentials');
            $timeout = (int) config('services.shipuniversal.timeout', 60);

            if (empty($tokenUrl) || empty($username) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'ShipUniversal credentials are not configured.',
                ];
            }

            $response = Http::withBasicAuth($username, $password)
                ->acceptJson()
                ->asForm()
                ->timeout($timeout)
                ->post($tokenUrl, [
                    'grant_type' => $grantType,
                ]);

            if (!$response->successful()) {
                $responseData = $response->json();
                $message = $this->extractShipUniversalErrorMessage(
                    $responseData,
                    'ShipUniversal token generation failed.'
                );

                \Log::error(
                    'ShipUniversal token generation failed. Status: '
                    . $response->status() . ' | Body: ' . $response->body()
                );

                return ['success' => false, 'message' => $message];
            }

            $tokenData = $response->json();
            $token = $this->findShipUniversalResponseValue(
                $tokenData,
                ['access_token', 'accessToken', 'token', 'Token', 'bearer_token', 'bearerToken']
            );

            // Some token endpoints return a plain token instead of JSON.
            if (empty($token)) {
                $rawToken = trim($response->body(), " \t\n\r\0\x0B\"");
                if ($rawToken !== '' && !str_starts_with($rawToken, '{') && !str_starts_with($rawToken, '[')) {
                    $token = $rawToken;
                }
            }

            if (!is_scalar($token) || trim((string) $token) === '') {
                \Log::error('ShipUniversal token response did not contain a token.');
                return [
                    'success' => false,
                    'message' => 'No bearer token found in ShipUniversal authentication response.',
                ];
            }

            return ['success' => true, 'token' => trim((string) $token)];
        } catch (\Exception $e) {
            \Log::error('ShipUniversal token generation exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'ShipUniversal token generation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Build the ShipUniversal shipment-create payload from stored shipment data.
     *
     * @param \App\Models\ShipperInfo $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */
    private function buildShipUniversalPayloadFromDb($shipper)
    {
        $consignee = $shipper->consigneeInfo;
        if (!$consignee) {
            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        $packages = $shipper->packageDimensions;
        if ($packages->isEmpty()) {
            return ['success' => false, 'message' => 'No package dimensions found for this shipment.'];
        }

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();
        $invoiceItems = $invoice
            ? ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get()
            : collect();
        $csbInformation = $shipper->csbInformation;

        $shippingMethod = $this->resolveShippingMethod($shipper);
        $courierService = !empty($shipper->service_id)
            ? CourierService::find($shipper->service_id)
            : null;
        if (!$courierService) {
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
        }

        $serviceCode = trim((string) (
            $courierService->service_code
            ?? $courierService->scode
            ?? ''
        ));
        if ($serviceCode === '') {
            return [
                'success' => false,
                'message' => 'ShipUniversal service code is not configured for the selected courier service.',
            ];
        }

        $kycType = match ($shipper->kyc_type) {
            'GST (Normal)' => 'GSTIN (Normal)',
            'Aadhar Card' => 'Aadhaar Number',
            'PAN Card' => 'PAN Number',
            default => (string) ($shipper->kyc_type ?? ''),
        };

        $packageDetails = $packages->map(function ($package) {
            return [
                'Length' => (float) ($package->length_cm ?? 0),
                'Width' => (float) ($package->width_cm ?? 0),
                'Height' => (float) ($package->height_cm ?? 0),
                'ActualWeight' => (float) ($package->actual_weight_kg ?? 0),
            ];
        })->values()->all();

        $totalPackageWeight = (float) $packages->sum(function ($package) {
            return (float) ($package->actual_weight_kg ?? 0);
        });
        $totalItemQuantity = (float) $invoiceItems->sum(function ($item) {
            return max(0, (float) ($item->qty ?? 0));
        });
        $pieceWeight = $totalItemQuantity > 0
            ? round($totalPackageWeight / $totalItemQuantity, 3)
            : round($totalPackageWeight, 3);

        $productDetails = $invoiceItems->map(function ($item) use ($pieceWeight) {
            return [
                'BoxNo' => (string) ($item->box_no ?? 1),
                'Description' => (string) ($item->description ?? ''),
                'HSNCode' => (string) ($item->hs_code ?? ''),
                'HTSCode' => (string) ($item->hts_code ?? ''),
                'UnitType' => (string) ($item->unit_type ?? 'PCS'),
                'Qty' => (float) ($item->qty ?? 1),
                'UnitRate' => (float) ($item->unit_rate ?? 0),
                'ShipPieceIGST' => (float) ($item->igst_percentage ?? 0),
                'PieceWt' => $pieceWeight,
            ];
        })->values()->all();

        if (empty($productDetails)) {
            $productDetails[] = [
                'BoxNo' => '1',
                'Description' => 'General Merchandise',
                'HSNCode' => '',
                'HTSCode' => '',
                'UnitType' => 'PCS',
                'Qty' => 1,
                'UnitRate' => (float) ($invoice->invoice_amount ?? 0),
                'ShipPieceIGST' => 0,
                'PieceWt' => $pieceWeight,
            ];
        }

        $originType = strtoupper(trim((string) ($consignee->origin_type ?? '')));
        $csbType = in_array($originType, ['CSB V', 'CSB 5'], true) ? 'CSB 5' : 'CSB 4';
        $bondUtIgst = trim((string) ($csbInformation->bond_ut_igst ?? ''));
        $isIgstPaid = str_contains(strtoupper($bondUtIgst), 'IGST');
        $igstAmount = (float) $invoiceItems->sum(function ($item) {
            return (float) ($item->igst_amount ?? 0);
        });

        $invoiceDate = $invoice && $invoice->invoice_date
            ? $invoice->invoice_date->format('Y-m-d') . 'T00:00:00Z'
            : now()->format('Y-m-d') . 'T00:00:00Z';
        $referenceNumber = trim((string) ($invoice->reference_number ?? ''));
        if ($referenceNumber === '') {
            $referenceNumber = (string) ($shipper->awb_number ?? ('SU-' . $shipper->id));
        }

        $senderAddressLine1 = trim((string) ($shipper->address_line1 ?? ''));
        $senderAddressLine2 = trim((string) ($shipper->address_line2 ?? ''));
        if ($senderAddressLine2 === '') {
            $senderAddressLine2 = $senderAddressLine1;
        }

        $receiverAddressLine1 = trim((string) ($consignee->address_line1 ?? ''));
        $receiverAddressLine2 = trim((string) ($consignee->address_line2 ?? ''));
        if ($receiverAddressLine2 === '') {
            $receiverAddressLine2 = $receiverAddressLine1;
        }

        $payload = [
            'AccountCode' => config('services.shipuniversal.account_code', 'SU0119'),
            'Sender' => [
                'SenderName' => (string) ($shipper->company_name ?? $shipper->contact_person ?? 'Shipper'),
                'SenderContactPerson' => (string) ($shipper->contact_person ?? $shipper->company_name ?? 'Shipper'),
                'SenderAddressLine1' => $senderAddressLine1,
                'SenderAddressLine2' => $senderAddressLine2,
                'SenderAddressLine3' => (string) ($shipper->address_line3 ?? ''),
                'SenderPincode' => (string) ($shipper->pincode ?? ''),
                'SenderCity' => (string) ($shipper->city ?? ''),
                'SenderState' => (string) ($shipper->state ?? ''),
                'SenderTelephone' => (string) ($shipper->phone_number ?? ''),
                'SenderEmailId' => (string) ($shipper->email ?? ''),
                'KYCType' => $kycType,
                'KYCNo' => (string) ($shipper->kyc_number ?? ''),
            ],
            'Receiver' => [
                'ReceiverName' => (string) ($consignee->consignee_name ?? $consignee->contact_person ?? 'Consignee'),
                'ReceiverContactPerson' => (string) ($consignee->contact_person ?? $consignee->consignee_name ?? 'Consignee'),
                'ReceiverAddressLine1' => $receiverAddressLine1,
                'ReceiverAddressLine2' => $receiverAddressLine2,
                'ReceiverAddressLine3' => (string) ($consignee->address_line3 ?? ''),
                'ReceiverZipcode' => (string) ($consignee->zip_code ?? ''),
                'ReceiverCity' => (string) ($consignee->city ?? ''),
                'ReceiverState' => (string) ($consignee->state ?? ''),
                'ReceiverCountry' => $this->getShipUniversalCountryCode($consignee->delivery_destination),
                'ReceiverTelephone' => (string) ($consignee->phone_number ?? ''),
                'ReceiverEmailid' => (string) ($consignee->email ?? ''),
                'VatId' => '',
            ],
            'ServiceDetails' => [
                'Service' => $serviceCode,
                'GoodsType' => 'NDox',
                'PackageType' => 'PACKAGE',
            ],
            'PackageDetails' => [
                'PackageDetail' => $packageDetails,
            ],
            'AdditionalDetails' => [
                'IsThirdParty' => true,
                'ProductDetails' => $productDetails,
                'InvoiceCurrency' => (string) ($invoice->invoice_currency ?? 'INR'),
                'InvoiceNo' => (string) ($invoice->invoice_number ?? ''),
                'InvoiceDate' => $invoiceDate,
                'TermsOfSale' => (string) ($invoice->incoterms ?? 'FOB'),
                'ReasonForExport' => 'SALE',
                'FreightCharge' => 0,
                'InsuranceCharge' => 0,
                'CSB_Type' => $csbType,
                'CustomerRefNo' => $referenceNumber,
                'DeliveryConfirmation' => 'No',
                'DutyTax' => 'DDU',
                'DutiesAccountNo' => '',
                'TransactionId' => '',
                'ShipperImage' => '',
                'ShipperKYC' => '',
                'FileName' => '',
                'IECNo' => (string) ($csbInformation->iec_code ?? ''),
                'ADCode' => (string) ($csbInformation->ad_code ?? ''),
                'BankType' => 'G',
                'BankAccount' => (string) ($csbInformation->bank_account_number ?? ''),
                'NFEI' => false,
                'Ecom' => $this->shipUniversalValueIsYes($csbInformation->ecommerce ?? null),
                'MEIS' => $this->shipUniversalValueIsYes($csbInformation->scheme ?? null),
                'BoundUT' => $bondUtIgst !== '' ? $bondUtIgst : 'NA',
                'IGSTPaid' => $isIgstPaid ? 'Yes' : 'No',
                'IGSTAmount' => $isIgstPaid ? $igstAmount : 0,
            ],
        ];

        return ['success' => true, 'payload' => $payload];
    }

    /**
     * Create a shipment through ShipUniversal using a generated Bearer token.
     */
    private function callShipUniversalApiFromDb($shipper)
    {
        try {
            $tokenResult = $this->getShipUniversalToken();
            if (!$tokenResult['success']) {
                return $tokenResult;
            }

            $payloadResult = $this->buildShipUniversalPayloadFromDb($shipper);
            if (!$payloadResult['success']) {
                return $payloadResult;
            }

            $shipmentUrl = config('services.shipuniversal.shipment_url');
            $timeout = (int) config('services.shipuniversal.timeout', 60);
            $payload = $payloadResult['payload'];

            if (empty($shipmentUrl)) {
                return [
                    'success' => false,
                    'message' => 'ShipUniversal shipment URL is not configured.',
                    'request_payload' => $payload,
                ];
            }

            \Log::info(
                'ShipUniversal shipment request for shipper #' . $shipper->id,
                ['service' => $payload['ServiceDetails']['Service'] ?? null]
            );

            $response = Http::withToken($tokenResult['token'])
                ->acceptJson()
                ->asJson()
                ->timeout($timeout)
                ->post($shipmentUrl, $payload);

            $apiResponse = $response->json();
            if (!is_array($apiResponse)) {
                $apiResponse = ['raw_body' => $response->body()];
            }

            if (!$response->successful()) {
                $message = $this->extractShipUniversalErrorMessage(
                    $apiResponse,
                    'ShipUniversal shipment creation failed.'
                );
                \Log::error(
                    'ShipUniversal shipment creation failed. Status: '
                    . $response->status() . ' | Body: ' . $response->body()
                );

                return [
                    'success' => false,
                    'message' => $message,
                    'data' => $apiResponse,
                    'request_payload' => $payload,
                    'status_code' => $response->status(),
                ];
            }

            $status = $this->findShipUniversalResponseValue($apiResponse, ['Status', 'status', 'Success', 'success']);
            if ($status === false || (is_string($status) && in_array(strtoupper($status), ['ERROR', 'FAILED', 'FAILURE', 'FALSE'], true))) {
                return [
                    'success' => false,
                    'message' => $this->extractShipUniversalErrorMessage(
                        $apiResponse,
                        'ShipUniversal API returned an error status.'
                    ),
                    'data' => $apiResponse,
                    'request_payload' => $payload,
                ];
            }

            return [
                'success' => true,
                'message' => 'ShipUniversal shipment created successfully.',
                'data' => $apiResponse,
                'request_payload' => $payload,
            ];
        } catch (\Exception $e) {
            \Log::error('ShipUniversal API call failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'ShipUniversal API call failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Find the first matching scalar value anywhere in a ShipUniversal response.
     */
    private function findShipUniversalResponseValue($value, array $keys)
    {
        if (!is_array($value)) {
            return null;
        }

        $normalizedKeys = array_map('strtolower', $keys);
        foreach ($value as $key => $child) {
            if (in_array(strtolower((string) $key), $normalizedKeys, true) && is_scalar($child)) {
                return $child;
            }
        }

        foreach ($value as $child) {
            if (is_array($child)) {
                $found = $this->findShipUniversalResponseValue($child, $keys);
                if ($found !== null && $found !== '') {
                    return $found;
                }
            }
        }

        return null;
    }

    private function extractShipUniversalTrackingNumber($apiResponse)
    {
        $value = $this->findShipUniversalResponseValue($apiResponse, [
            'AwbNo', 'AwbNumber', 'AWBNumber', 'awb_number', 'awb',
            'TrackingNumber', 'tracking_number', 'WaybillNumber', 'waybill_number',
            'ConsignmentNumber', 'consignment_number', 'ShipmentNumber', 'shipment_number',
        ]);

        return is_scalar($value) && trim((string) $value) !== '' ? trim((string) $value) : null;
    }

    private function extractShipUniversalLabelUrl($apiResponse)
    {
        $value = $this->findShipUniversalResponseValue($apiResponse, [
            'AirwaybillUrl', 'BoxlabelUrl', 'LabelUrl', 'label_url', 'LabelURL',
            'LabelLink', 'label_link', 'PdfUrl', 'pdf_url', 'LabelBase64', 'label_base64',
        ]);

        return is_scalar($value) && trim((string) $value) !== '' ? trim((string) $value) : null;
    }

    private function extractShipUniversalErrorMessage($apiResponse, $fallback)
    {
        $message = $this->findShipUniversalResponseValue($apiResponse, [
            'ErrorMessage', 'error_message', 'Error', 'error', 'Message', 'message',
            'Description', 'description', 'Detail', 'detail',
        ]);

        if (is_scalar($message) && trim((string) $message) !== '') {
            return trim((string) $message);
        }

        return $fallback;
    }

    private function shipUniversalValueIsYes($value)
    {
        return in_array(strtolower(trim((string) $value)), ['1', 'yes', 'true', 'y'], true);
    }

    private function getShipUniversalCountryCode($destination)
    {
        $destination = strtoupper(trim((string) $destination));
        if (preg_match('/^[A-Z]{2}$/', $destination)) {
            return $destination;
        }

        $countries = [
            'UNITED STATES' => 'US',
            'UNITED STATE' => 'US',
            'USA' => 'US',
            'UNITED KINGDOM' => 'GB',
            'GREAT BRITAIN' => 'GB',
            'UK' => 'GB',
            'CANADA' => 'CA',
            'AUSTRALIA' => 'AU',
            'INDIA' => 'IN',
            'CHINA' => 'CN',
            'RUSSIA' => 'RU',
            'SRI LANKA' => 'LK',
            'SRILANKA' => 'LK',
            'UNITED ARAB EMIRATES' => 'AE',
            'UAE' => 'AE',
            'GERMANY' => 'DE',
            'FRANCE' => 'FR',
            'ITALY' => 'IT',
            'SPAIN' => 'ES',
            'NETHERLANDS' => 'NL',
            'SINGAPORE' => 'SG',
            'MALAYSIA' => 'MY',
        ];

        foreach ($countries as $name => $code) {
            if (str_contains($destination, $name)) {
                return $code;
            }
        }

        // Destination dropdown values commonly begin with an ISO code (e.g. "US-").
        if (preg_match('/^([A-Z]{2})\s*[-–:]/', $destination, $matches)) {
            return $matches[1];
        }

        return 'US';
    }

    /*
    |--------------------------------------------------------------------------
    | Overseas Logistic API (UNITED CANADA DDP / UNITED CANADA E-COMMERCE)
    |--------------------------------------------------------------------------
    | Token URL:    https://api.overseaslogistic.com/token
    | Shipment URL: https://api.overseaslogistic.com/api/shipment/create
    |
    | Two-step flow:
    |   1. POST /token (OAuth2 client_credentials grant) -> Bearer access_token
    |   2. POST /api/shipment/create with Authorization: Bearer <token>
    |
    | The /token endpoint is an OAuth2 token server. It requires a
    | client_credentials grant with client_id/client_secret sent as
    | form-encoded data (application/x-www-form-urlencoded). Sending
    | username/password as JSON returns "invalid_client".
    |
    | The same endpoint/payload is used for both DDP and E-Commerce variants;
    | the Service field inside ServiceDetails differentiates the service.
    |
    */

    /**
     * Generate a Bearer token from the Overseas Logistic /token endpoint.
     *
     * Uses the OAuth2 client_credentials grant type. The username configured
     * in services.overseas.username is sent as client_id and the password as
     * client_secret, form-encoded (NOT JSON).
     *
     * @return array ['success' => bool, 'token' => string|null, 'message' => string|null]
     */
    private function getOverseasLogisticToken()
    {
        try {
            $tokenUrl   = config('services.overseas.token_url');
            $clientId   = config('services.overseas.username');
            $clientSec  = config('services.overseas.password');
            $timeout    = (int) config('services.overseas.timeout', 60);

            if (empty($tokenUrl) || empty($clientId) || empty($clientSec)) {
                return [
                    'success' => false,
                    'message' => 'Overseas Logistic credentials are not configured.',
                ];
            }

            // OAuth2 client_credentials grant — must be form-encoded.
            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->timeout($timeout)
                ->post($tokenUrl, [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSec,
                ]);

            if (!$response->successful()) {
                $errorBody = $response->json() ?: $response->body();
                $errorMessage = 'Overseas Logistic token generation failed.';
                if (is_array($errorBody)) {
                    if (isset($errorBody['error_description'])) {
                        $errorMessage = $this->overseasValueToString($errorBody['error_description']);
                    } elseif (isset($errorBody['error'])) {
                        $errorMessage = $this->overseasValueToString($errorBody['error']);
                    } elseif (isset($errorBody['message'])) {
                        $errorMessage = $this->overseasValueToString($errorBody['message']);
                    }
                }
                \Log::error('Overseas Logistic token generation failed: ' . $errorMessage . ' | Status: ' . $response->status() . ' | Body: ' . $response->body());
                return [
                    'success' => false,
                    'message' => $errorMessage,
                ];
            }

            $tokenData = $response->json();
            $bearerToken = null;

            // Extract token from various possible response formats.
            // The OAuth2 server returns "access_token" at the top level.
            if (isset($tokenData['access_token'])) {
                $bearerToken = $tokenData['access_token'];
            } elseif (isset($tokenData['data']) && isset($tokenData['data']['access_token'])) {
                $bearerToken = $tokenData['data']['access_token'];
            } elseif (isset($tokenData['token'])) {
                $bearerToken = $tokenData['token'];
            } elseif (isset($tokenData['data']) && isset($tokenData['data']['token'])) {
                $bearerToken = $tokenData['data']['token'];
            } elseif (isset($tokenData['Token'])) {
                $bearerToken = $tokenData['Token'];
            }

            if (!$bearerToken) {
                \Log::error('Overseas Logistic: No token found in response. Response: ' . json_encode($tokenData));
                return [
                    'success' => false,
                    'message' => 'No bearer token found in Overseas Logistic authentication response.',
                ];
            }

            \Log::info('Overseas Logistic token generated successfully.');
            return [
                'success' => true,
                'token'   => $bearerToken,
            ];
        } catch (\Exception $e) {
            \Log::error('Overseas Logistic token generation exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Overseas Logistic token generation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Build the Overseas Logistic shipment-create payload from database records.
     * Payload structure mirrors the documented /api/shipment/create format.
     *
     * @param \App\Models\ShipperInfo $shipper
     * @return array ['success' => bool, 'payload' => array|null, 'message' => string|null]
     */
    private function buildOverseasLogisticPayloadFromDb($shipper)
    {
        
        $consignee = $shipper->consigneeInfo;
        if (!$consignee) {
            \Log::warning('buildOverseasLogisticPayloadFromDb: No consignee found for shipper #' . $shipper->id);
            return ['success' => false, 'message' => 'No consignee information found for this shipment.'];
        }

        $packages = $shipper->packageDimensions;
        if ($packages->isEmpty()) {
            \Log::warning('buildOverseasLogisticPayloadFromDb: No packages found for shipper #' . $shipper->id);
            return ['success' => false, 'message' => 'No package dimensions found for this shipment.'];
        }

        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();
    
        
        if($shipper->kyc_type == "GST (Normal)"){
            $kycType_data = "GSTIN (Normal)";
        }
        elseif($shipper->kyc_type == "Aadhar Card"){
            $kycType_data = "Aadhaar Number";
        }
        elseif($shipper->kyc_type == "PAN Card"){
            $kycType_data = "PAN Number";
        }
        else{
            $kycType_data = $shipper->kyc_type ?? '';
        }
        
        // ---- Sender ----
        $senderName      = $shipper->company_name ?? $shipper->contact_person ?? 'Shipper';
        $senderContact   = $shipper->contact_person ?? $shipper->company_name ?? 'Shipper';
        $senderAddress1  = $shipper->address_line1 ?? '';
        $senderAddress2  = $shipper->address_line2 ?? '';
        $senderAddress3  = $shipper->address_line3 ?? '';
        $senderPincode   = (string) ($shipper->pincode ?? '');
        $senderCity      = $shipper->city ?? '';
        $senderState     = $shipper->state ?? '';

        // ---- Validation: shipper state must not exceed 2 characters ----
        // The Overseas Logistic API expects a 2-letter state code (e.g. "GJ",
        // "MH"). If the shipper's state field contains more than 2 letters
        // (e.g. a full state name like "Gujarat"), shipment creation is blocked
        // here with an error so the API is never called with invalid data.
        // This applies to ALL Overseas Logistic shipments (Canada DDP /
        // E-Commerce and ARAMEX GPX / Australia).
        $senderStateTrimmed = trim((string) $senderState);
        if (strlen($senderStateTrimmed) > 2) {
            \Log::warning('buildOverseasLogisticPayloadFromDb: Shipper state exceeds 2 characters for shipper #' . $shipper->id . ' | state="' . $senderStateTrimmed . '"');
            return [
                'success' => false,
                'message' => 'Shipper state must be a 2-letter code (e.g. "GJ", "MH"). The provided state "' . $senderStateTrimmed . '" is too long. Please update the shipper state to a 2-letter code and try again.',
            ];
        }

        $senderTelephone = (string) ($shipper->phone_number ?? '');
        $senderEmail     = $shipper->email ?? '';
        // $kycType         = $shipper->kyc_type ?? 'GSTIN (Normal)';
        $kycType         = $kycType_data;
        $kycNo           = (string) ($shipper->kyc_number ?? '');
        
        // ---- Receiver ----
        // $receiverType      = $consignee->origin_type ? ucfirst(strtolower($consignee->origin_type)) : 'Business';
        // if($consignee->origin_type == "CSB IV") {
        //     $receiverType = "customer";
        // } else if($consignee->origin_type == "CSB V") {
        //     $receiverType = "Business";
        // }
        $receiverName      = $consignee->consignee_name ?? $consignee->contact_person ?? 'Consignee';
        $receiverContact   = $consignee->contact_person ?? $consignee->consignee_name ?? 'Consignee';
        $receiverAddress1  = $consignee->address_line1 ?? '';
        $receiverAddress2  = $consignee->address_line2 ?? '';
        $receiverAddress3  = $consignee->address_line3 ?? '';
        $receiverZipcode   = (string) ($consignee->zip_code ?? '');
        $receiverCity      = $consignee->city ?? '';
        $receiverState     = $consignee->state ?? '';
        $receiverCountry   = $this->getOverseasCountryCode($consignee->delivery_destination ?? '', 'CA');
        $receiverTelephone = (string) ($consignee->phone_number ?? '');
        $receiverEmail     = $consignee->email ?? '';

        // ---- Service details ----
        // Resolve the courier service to get the Service code (e.g. CANADA_YVR_SELF).
        // The overseas payload is identical for every overseas service; only the
        // Service field inside ServiceDetails changes per service. We now prefer
        // the explicit shipper_info.service_id (stored at manifest time) to look
        // up the courier_services row directly — this is more reliable than the
        // fuzzy string matching below, which can pick the wrong service when
        // several overseas services share similar method names.
        $shippingMethod = $this->resolveShippingMethod($shipper);
        $courierService = null;

        // 1) Preferred path: resolve directly from shipper_info.service_id.
        if (!empty($shipper->service_id)) {
            $serviceById = CourierService::find($shipper->service_id);
            if ($serviceById && $this->isOverseasLogisticMethod($serviceById->method)) {
                $courierService = $serviceById;
                // Keep shipping_method in sync with the resolved service so the
                // DutyTax / CSB logic below and any logging use the real method.
                $shippingMethod = $serviceById->method;
            }
        }

        // 2) Fallback: legacy fuzzy match (for older rows where service_id is NULL
        //    or points at a non-overseas service).
        if (!$courierService) {
            $courierService = $this->findCourierService($shippingMethod, $shipper->id);
        }

        $serviceCode    = $courierService ? ($courierService->service_code ?? $courierService->scode ?? '') : '';
        if (empty($serviceCode)) {
            // Fallback to a sensible default if the service code is missing.
            $serviceCode = 'CANADA_YVR_SELF';
        }

        // GoodsType: NDox (documents) vs NDox (non-documents). Default to NDox.
        $goodsType   = 'NDox';
        $packageType = 'PACKAGE';

        // ---- Package details ----
        $packageDetail = [];
        foreach ($packages as $pkg) {
            $packageDetail[] = [
                'Length'       => (float) ($pkg->length_cm ?? 0),
                'Width'        => (float) ($pkg->width_cm ?? 0),
                'Height'       => (float) ($pkg->height_cm ?? 0),
                'ActualWeight' => (float) ($pkg->actual_weight_kg ?? 0),
            ];
        }

        // ---- Additional details / product details ----
        $productDetails = [];
        if ($invoice) {
            $invoiceItems = ShipmentInvoiceItem::where('invoice_id', $invoice->id)->get();
            foreach ($invoiceItems as $item) {
                $productDetails[] = [
                    'BoxNo'         => (string) ($item->box_no ?? 1),
                    'Description'   => $item->description ?? '',
                    'HSNCode'       => (string) ($item->hs_code ?? ''),
                    'HTSCode'       => (string) ($item->hts_code ?? ''),
                    'UnitType'      => $item->unit_type ?? 'PCS',
                    'Qty'           => (int) $item->qty,
                    'UnitRate'      => (float) $item->unit_rate,
                    'ShipPieceIGST' => (float) ($item->igst_percentage ?? 0),
                    'PieceWt'       => (float) ($item->amount > 0 && $item->qty > 0 ? round($item->amount / $item->qty, 3) : 0),
                ];
            }
        }

        // Fallback product detail when no invoice items exist.
        if (empty($productDetails)) {
            $productDetails[] = [
                'BoxNo'         => '1',
                'Description'   => 'General Merchandise',
                'HSNCode'       => '',
                'HTSCode'       => '',
                'UnitType'      => 'PCS',
                'Qty'           => 1,
                'UnitRate'      => (float) ($invoice ? $invoice->invoice_amount : 0),
                'ShipPieceIGST' => 0.00,
                'PieceWt'       => 0.3,
            ];
        }

        // Invoice / export details.
        $invoiceNo       = $invoice ? ($invoice->invoice_number ?? '') : '';
        $invoiceDate      = $invoice && $invoice->invoice_date
            ? $invoice->invoice_date->format('Y-m-d') . 'T00:00:00Z'
            : now()->format('Y-m-d') . 'T00:00:00Z';
        $invoiceCurrency  = $invoice ? ($invoice->invoice_currency ?? 'INR') : 'INR';
        $termsOfSale      = $invoice ? ($invoice->incoterms ?? 'FOB') : 'FOB';
        $customerRefNo    = $invoice ? ($invoice->reference_number ?? '') : '';
        $transactionId    = $shipper->awb_number ?? ('TXN-' . $shipper->id);

        // DutyTax: DDP for UNITED CANADA DDP, DDU otherwise (E-Commerce).
        $methodUpper = strtoupper(trim($shippingMethod));
        $dutyTax = str_contains($methodUpper, 'DDP') ? 'DDP' : 'DDU';

        // CSB type from customer csb_status (1..5 -> "CSB 1".."CSB 5"); default CSB 4.
        $customer = Customer::find($shipper->customer_id);
        $csbType = 'CSB 4';
        // if ($customer && $customer->csb_status) {
        //     // m chahata hu ki ek condition ho ki agar csb_status 1 h toh csbtype "csb 1" ho toh $csbType = "CSB 4" print ho agar csb_status 2 h toh csbtype "csb 2" h toh $csbType = "CSB 5" print ho

        //     if ($customer->csb_status == 1) {
        //         $csbType = 'CSB 4';
        //     } elseif ($customer->csb_status == 2) {
        //         $csbType = 'CSB 5';
        //     }
        //     print_r($csbType);
        // }

        if($consignee->origin_type == 'CSB IV'){
            
            $csbType = 'CSB 4';
        }
        else if ($consignee->origin_type == 'CSB V'){
            
            $csbType = 'CSB 5';
        }

        $payload = [
            'AccountCode' => config('services.overseas.account_code', 'PR-U02'),
            'Sender' => [
                'SenderName'           => $senderName,
                'SenderContactPerson' => $senderContact,
                'SenderAddressLine1'   => $senderAddress1,
                'SenderAddressLine2'   => $senderAddress2,
                'SenderAddressLine3'   => $senderAddress3,
                'SenderPincode'        => $senderPincode,
                'SenderCity'           => $senderCity,
                'SenderState'          => $senderState,
                'SenderTelephone'      => $senderTelephone,
                'SenderEmailId'        => $senderEmail,
                'KYCType'              => $kycType,
                'KYCNo'                => $kycNo,
            ],
            'Receiver' => [
                'ReceiverType'          => "Business",
                'ReceiverName'          => $receiverName,
                'ReceiverContactPerson' => $receiverContact,
                'ReceiverAddressLine1'  => $receiverAddress1,
                'ReceiverAddressLine2'  => $receiverAddress2,
                'ReceiverAddressLine3'  => $receiverAddress3,
                'ReceiverZipcode'       => $receiverZipcode,
                'ReceiverCity'          => $receiverCity,
                'ReceiverState'         => $receiverState,
                'ReceiverCountry'       => $receiverCountry,
                'ReceiverTelephone'     => $receiverTelephone,
                'ReceiverEmailid'       => $receiverEmail,
                'VatId'                 => '',
            ],
            'ServiceDetails' => [
                'Service'     => $serviceCode,
                'GoodsType'   => $goodsType,
                'PackageType' => $packageType,
            ],
            'PackageDetails' => [
                'PackageDetail' => $packageDetail,
            ],
            'AdditionalDetails' => [
                'ProductDetails'       => $productDetails,
                'InvoiceCurrency'      => $invoiceCurrency,
                'InvoiceNo'            => $invoiceNo,
                'InvoiceDate'          => $invoiceDate,
                'TermsOfSale'          => $termsOfSale,
                'ReasonForExport'      => 'GIFT',
                'FreightCharge'        => 0,
                'InsuranceCharge'      => 0,
                'CSB_Type'             => $csbType,
                'CustomerRefNo'        => $customerRefNo,
                'DeliveryConfirmation' => 'No',
                'DutyTax'              => $dutyTax,
                'DutiesAccountNo'      => '',
                'TransactionId'        => $transactionId,
                'ShipperImage'         => '',
                'ShipperKYC'           => '',
                'FileName'             => '',
            ],
        ];

        return ['success' => true, 'payload' => $payload];
    }

    /**
     * Safely convert any value (string, array, object, null) into a string
     * for use in log messages and error responses. Prevents
     * "Array to string conversion" errors when API error fields are arrays.
     *
     * @param mixed $value
     * @return string
     */
    private function overseasValueToString($value)
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_null($value)) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        // Arrays / objects -> JSON string (never triggers array-to-string).
        $json = json_encode($value);
        return ($json === false) ? '' : $json;
    }

    /**
     * Call the Overseas Logistic API to create a shipment.
     * Two-step process:
     * 1. Generate Bearer token from /token
     * 2. Create shipment via /api/shipment/create using the Bearer token
     *
     * @param \App\Models\ShipperInfo $shipper
     * @return array ['success' => bool, 'message' => string, 'data' => array|null, 'request_payload' => array|null]
     */
    private function callOverseasLogisticApiFromDb($shipper)
    {
        try {
            // Step 1: Generate Bearer token.
            $tokenResult = $this->getOverseasLogisticToken();
            if (!$tokenResult['success']) {
                return [
                    'success' => false,
                    'message' => $tokenResult['message'] ?? 'Overseas Logistic token generation failed.',
                ];
            }
            $bearerToken = $tokenResult['token'];

            // Step 2: Build the shipment payload.
            $payloadResult = $this->buildOverseasLogisticPayloadFromDb($shipper);
            if (!$payloadResult['success']) {
                return [
                    'success' => false,
                    'message' => $payloadResult['message'] ?? 'Failed to build Overseas Logistic payload.',
                ];
            }
            $payload = $payloadResult['payload'];

            $shipmentUrl = config('services.overseas.shipment_url');
            $timeout     = (int) config('services.overseas.timeout', 60);

            \Log::info('Overseas Logistic shipment payload for shipper #' . $shipper->id . ': ' . substr(json_encode($payload), 0, 2000));

            // Step 3: Call the shipment/create API with the Bearer token.
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $bearerToken,
            ])
                ->timeout($timeout)
                ->post($shipmentUrl, $payload);

            $apiResponse = $response->json();

            if (!$response->successful()) {
                $errorMessage = 'Overseas Logistic API returned error.';
                if (is_array($apiResponse)) {
                    if (isset($apiResponse['error'])) {
                        $errorMessage = $this->overseasValueToString($apiResponse['error']);
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $this->overseasValueToString($apiResponse['message']);
                    } elseif (isset($apiResponse['errors'])) {
                        $errorMessage = $this->overseasValueToString($apiResponse['errors']);
                    }
                    if (isset($apiResponse['details']) && !empty($apiResponse['details'])) {
                        $details = $apiResponse['details'];
                        if (is_array($details)) {
                            $flat = array_map([$this, 'overseasValueToString'], $details);
                            $errorMessage .= ' — ' . implode('; ', $flat);
                        } else {
                            $errorMessage .= ' — ' . $this->overseasValueToString($details);
                        }
                    }
                }
                \Log::error('Overseas Logistic shipment creation failed: ' . $errorMessage . ' | Status: ' . $response->status() . ' | Body: ' . $response->body());
                return [
                    'success'         => false,
                    'message'         => $errorMessage,
                    'data'            => $apiResponse,
                    'request_payload' => $payload,
                    'status_code'     => $response->status(),
                ];
            }

            // Check for a body-level error/status even on HTTP 200.
            // The Overseas Logistic API returns { "Status": true/false, "Error": "...", "Data": {...} }.
            // Treat Status === false (boolean) OR status === "ERROR" (string) as a failure.
            if (is_array($apiResponse)) {
                $responseStatus = $apiResponse['Status'] ?? $apiResponse['status'] ?? null;
                $isError = false;
                if ($responseStatus === false) {
                    $isError = true;
                } elseif ($responseStatus !== null && strtoupper((string) $responseStatus) === 'ERROR') {
                    $isError = true;
                }
                if ($isError) {
                    $rawError = $apiResponse['Error'] ?? $apiResponse['error']
                        ?? $apiResponse['message'] ?? $apiResponse['Message']
                        ?? 'Overseas Logistic API returned an error status.';
                    $errorMessage = $this->overseasValueToString($rawError);
                    \Log::error('Overseas Logistic API returned error in body for shipper #' . $shipper->id . ': ' . $errorMessage . ' | Body: ' . $response->body());
                    return [
                        'success'         => false,
                        'message'         => $errorMessage,
                        'data'            => $apiResponse,
                        'request_payload' => $payload,
                    ];
                }
            }

            \Log::info('Overseas Logistic shipment created for shipper #' . $shipper->id . '. Response: ' . substr($response->body(), 0, 2000));
            return [
                'success'         => true,
                'message'         => 'Overseas Logistic shipment created successfully.',
                'data'            => $apiResponse,
                'request_payload' => $payload,
            ];
        } catch (\Exception $e) {
            \Log::error('Overseas Logistic API call failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Overseas Logistic API call failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Extract a tracking/AWB number from an Overseas Logistic API response.
     *
     * @param mixed $apiResponse
     * @return string|null
     */
    private function extractOverseasTrackingNumber($apiResponse)
    {
        if (!is_array($apiResponse)) {
            return null;
        }

        // Priority keys for the confirmed Overseas Logistic response format:
        // { "Status": true, "Data": { "AwbNo": "55141977", ... } }
        $priorityKeys = ['AwbNo', 'AwbNumber', 'awb_number', 'AWBNumber'];
        $candidateKeys = [
            'TrackingNumber', 'tracking_number',
            'WaybillNumber', 'waybill_number', 'ConsignmentNumber', 'consignment_number',
            'ShipmentNumber', 'shipment_number', 'OrderNumber', 'order_number',
            'ReferenceNo', 'reference_no', 'RefNo', 'BookingId', 'booking_id',
            'Waybill', 'waybill', 'Reference', 'reference', 'ShipmentId', 'shipment_id',
        ];
        $allKeys = array_merge($priorityKeys, $candidateKeys);

        // Case A: The response itself is a list of shipment objects.
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($allKeys as $key) {
                if (isset($apiResponse[0][$key]) && !empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Check top-level keys.
        foreach ($allKeys as $key) {
            if (isset($apiResponse[$key]) && !empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "Data" (capital, confirmed format) or "data".
        foreach (['Data', 'data'] as $dataKey) {
            $data = $apiResponse[$dataKey] ?? null;
            if (is_array($data)) {
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($allKeys as $key) {
                        if (isset($data[0][$key]) && !empty($data[0][$key])) {
                            return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                        }
                    }
                }
                foreach ($allKeys as $key) {
                    if (isset($data[$key]) && !empty($data[$key])) {
                        return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                    }
                }
            }
        }

        // Case D: Nested under common wrapper keys.
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment', 'Result', 'result', 'Response', 'response'] as $wrapKey) {
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
     * Extract a label URL (or base64 label) from an Overseas Logistic API response.
     *
     * @param mixed $apiResponse
     * @return string|null
     */
    private function extractOverseasLabelUrl($apiResponse)
    {
        if (!is_array($apiResponse)) {
            return null;
        }

        // Priority keys for the confirmed Overseas Logistic response format:
        // { "Data": { "Airwaybill": { "AirwaybillUrl": "...", "BoxlabelUrl": "...", "CustomInvoiceUrl": "..." } } }
        $priorityKeys = ['AirwaybillUrl', 'BoxlabelUrl', 'LabelUrl', 'label_url', 'LabelURL', 'LabelLink', 'label_link'];
        $labelKeys = [
            'PdfUrl', 'pdf_url', 'PdfLink', 'pdf_link', 'Label', 'label',
            'LabelData', 'label_data', 'PdfBase64', 'pdf_base64', 'LabelBase64', 'label_base64',
        ];
        $allKeys = array_merge($priorityKeys, $labelKeys);

        // Case 0 (confirmed format): Data.Airwaybill.<key>
        foreach (['Data', 'data'] as $dataKey) {
            $data = $apiResponse[$dataKey] ?? null;
            if (is_array($data)) {
                foreach (['Airwaybill', 'airwaybill', 'AirwayBill', 'Label', 'label'] as $awbKey) {
                    if (isset($data[$awbKey]) && is_array($data[$awbKey])) {
                        foreach ($priorityKeys as $key) {
                            if (isset($data[$awbKey][$key]) && !empty($data[$awbKey][$key])) {
                                return is_string($data[$awbKey][$key]) ? $data[$awbKey][$key] : (string) $data[$awbKey][$key];
                            }
                        }
                    }
                }
            }
        }

        // Case A: List of shipment objects.
        if (isset($apiResponse[0]) && is_array($apiResponse[0])) {
            foreach ($allKeys as $key) {
                if (isset($apiResponse[0][$key]) && !empty($apiResponse[0][$key])) {
                    return is_string($apiResponse[0][$key]) ? $apiResponse[0][$key] : (string) $apiResponse[0][$key];
                }
            }
        }

        // Case B: Top-level keys.
        foreach ($allKeys as $key) {
            if (isset($apiResponse[$key]) && !empty($apiResponse[$key])) {
                return is_string($apiResponse[$key]) ? $apiResponse[$key] : (string) $apiResponse[$key];
            }
        }

        // Case C: Nested under "Data" (capital) or "data".
        foreach (['Data', 'data'] as $dataKey) {
            $data = $apiResponse[$dataKey] ?? null;
            if (is_array($data)) {
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($allKeys as $key) {
                        if (isset($data[0][$key]) && !empty($data[0][$key])) {
                            return is_string($data[0][$key]) ? $data[0][$key] : (string) $data[0][$key];
                        }
                    }
                }
                foreach ($allKeys as $key) {
                    if (isset($data[$key]) && !empty($data[$key])) {
                        return is_string($data[$key]) ? $data[$key] : (string) $data[$key];
                    }
                }
            }
        }

        // Case D: Nested under common wrapper keys.
        foreach (['Shipments', 'shipments', 'Shipment', 'shipment', 'Result', 'result', 'Response', 'response'] as $wrapKey) {
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
     * Normalize a delivery-destination string into an ISO country code for the
     * Overseas Logistic API. Defaults to the provided fallback (CA for Canada).
     *
     * @param string|null $destination
     * @param string $fallback
     * @return string
     */
    private function getOverseasCountryCode($destination, $fallback = 'CA')
    {
        $destUpper = strtoupper(trim($destination ?? ''));

        if ($destUpper === '') {
            return $fallback;
        }

        // Canada detection.
        $isCanada = (
            $destUpper === 'CANADA'
            || $destUpper === 'CA'
            || str_contains($destUpper, 'CANADA')
        );
        if ($isCanada) {
            return 'CA';
        }

        // Australia detection — covers "Australia", "AU", "AUS", and any
        // string containing "Australia". Returns ISO code "AU" for the
        // Overseas Logistic API ReceiverCountry field.
        $isAustralia = (
            $destUpper === 'AUSTRALIA'
            || $destUpper === 'AU'
            || $destUpper === 'AUS'
            || str_contains($destUpper, 'AUSTRALIA')
        );
        if ($isAustralia) {
            return 'AU';
        }

        // UK detection.
        $isUk = (
            $destUpper === 'UK'
            || $destUpper === 'GB'
            || str_contains($destUpper, 'UNITED KINGDOM')
            || str_starts_with($destUpper, 'UK -')
            || str_contains($destUpper, 'GREAT BRITAIN')
        );
        if ($isUk) {
            return 'GB';
        }

        // US detection.
        $isUs = (
            $destUpper === 'US'
            || $destUpper === 'USA'
            || str_contains($destUpper, 'UNITED STATE')
            || str_starts_with($destUpper, 'US-')
        );
        if ($isUs) {
            return 'US';
        }

        return $fallback;
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
        
        // print_r($payload); // Debugging line to inspect the payload structure
        // die;
        
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

                // Check if this is an address-related error (for auto-fallback to UNITED CLASSIC)
                $isAddressError = $this->isFlyingTigersAddressError($errorMessage, $apiResponse, $response->body());

                return [
                    'success'          => false,
                    'message'          => $errorMessage,
                    'data'             => $apiResponse,
                    'status_code'      => $response->status(),
                    'is_address_error' => $isAddressError,
                ];
            }

            \Log::info('Flying Tigers response for shipper #' . $shipper->id . ': ' . substr($response->body(), 0, 2000));

            // Some APIs return 200 OK but with an error in the body — check for address error
            $isAddressError = $this->isFlyingTigersAddressError(null, $apiResponse, $response->body());
            if ($isAddressError) {
                \Log::warning('Flying Tigers API returned address error in success response for shipper #' . $shipper->id . ': ' . $response->body());
                return [
                    'success'          => false,
                    'message'          => 'Cannot create order: Provided address appears to be incorrect or incomplete.',
                    'data'             => $apiResponse,
                    'is_address_error' => true,
                ];
            }

            // Check if the API returned an error status in the body (HTTP 200 but status=ERROR)
            // e.g. {"status": "ERROR", "message": "Ref no already exists."}
            if (is_array($apiResponse)) {
                $responseStatus = $apiResponse['status'] ?? null;
                if ($responseStatus !== null && strtoupper((string) $responseStatus) === 'ERROR') {
                    $errorMessage = $apiResponse['message'] ?? 'Flying Tigers API returned an error status.';
                    \Log::error('Flying Tigers API returned ERROR in body for shipper #' . $shipper->id . ': ' . $errorMessage . ' | Body: ' . $response->body());

                    // Check if this is also an address error (for fallback to UNITED CLASSIC)
                    $isAddressError = $this->isFlyingTigersAddressError($errorMessage, $apiResponse, $response->body());

                    return [
                        'success'          => false,
                        'message'          => $errorMessage,
                        'data'             => $apiResponse,
                        'is_address_error' => $isAddressError,
                    ];
                }
            }

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
     * Check if a Flying Tigers API response/error indicates an address-related error.
     * Used to trigger auto-fallback to UNITED CLASSIC (Ship Global).
     *
     * @param string|null $errorMessage
     * @param mixed $apiResponse
     * @param string|null $rawBody
     * @return bool
     */
    private function isFlyingTigersAddressError($errorMessage, $apiResponse, $rawBody = null)
    {
        $addressErrorPatterns = [
            'address appears to be incorrect',
            'address appears to be incomplete',
            'address is incorrect',
            'address is incomplete',
            'incorrect or incomplete address',
            'address incorrect or incomplete',
            'provided address appears to be incorrect',
            'provided address appears to be incomplete',
        ];

        // Combine all text sources to search
        $searchText = '';
        if (!empty($errorMessage)) {
            $searchText .= ' ' . strtolower((string) $errorMessage);
        }
        if (is_array($apiResponse)) {
            $searchText .= ' ' . strtolower(json_encode($apiResponse));
        }
        if (!empty($rawBody)) {
            $searchText .= ' ' . strtolower((string) $rawBody);
        }

        if (empty(trim($searchText))) {
            return false;
        }

        foreach ($addressErrorPatterns as $pattern) {
            if (str_contains($searchText, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate UNITED CLASSIC (Ship Global) fallback info for a Flying Tigers address error.
     * Does NOT modify any data — only calculates the rate, paid amount, difference,
     * and wallet impact so the frontend can present a dropdown option to the customer.
     *
     * @param \App\Models\ShipperInfo $shipper
     * @param int $customerId
     * @return array
     */
    private function getFlyingTigersAddressErrorFallbackInfo($shipper, $customerId)
    {
        // 1. Find the UNITED CLASSIC courier service
        $classicService = \App\Models\CourierService::whereRaw('UPPER(method) LIKE ?', ['%UNITED CLASSIC%'])->first();
        if (!$classicService) {
            \Log::error('Flying Tigers address fallback: UNITED CLASSIC service not found in database.');
            return [
                'success'          => false,
                'message'           => 'Address is incorrect for UNITED ECO POST. Could not find UNITED CLASSIC service for fallback. Please contact support.',
                'is_address_error'  => true,
            ];
        }

        // 2. Calculate the total chargeable weight from packages
        $packages = $shipper->packageDimensions;
        $totalWeight = 0;
        foreach ($packages as $pkg) {
            $totalWeight += floatval($pkg->chargeable_weight ?? $pkg->actual_weight_kg ?? 0);
        }

        // 3. Get consignee state for zone lookup
        $consignee = $shipper->consigneeInfo;
        $consigneeState = $consignee ? ($consignee->state ?? '') : '';

        // 4. Calculate the UNITED CLASSIC rate
        $classicRate = $this->calculateBulkRate($customerId, $classicService, $totalWeight, $consigneeState);
        $classicTotal = floatval($classicRate['total'] ?? 0);

        \Log::info('Flying Tigers address fallback: UNITED CLASSIC rate calculated: ' . $classicTotal . ' for shipper #' . $shipper->id);

        // 5. Get the amount already paid (from invoice)
        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();
        $paidAmount = $invoice ? floatval($invoice->total_amount ?? 0) : 0;

        // 6. Calculate the difference
        $difference = $classicTotal - $paidAmount;

        // 7. Determine wallet impact (preview only — no actual deduction yet)
        $walletAction = 'none';
        $walletAmount = 0;
        $walletBalance = 0;

        $wallet = Wallet::where('customer_id', $customerId)->first();
        if ($wallet) {
            $walletBalance = (float) $wallet->balance;
            if ($difference > 0.01) {
                $walletAction = 'deduct';
                $walletAmount = $difference;
            } elseif ($difference < -0.01) {
                $walletAction = 'refund';
                $walletAmount = abs($difference);
            }
        }

        return [
            'success'           => true,
            'is_address_error'  => true,
            'shipper_id'        => $shipper->id,
            'classic_service'   => $classicService->method,
            'classic_rate'      => $classicTotal,
            'paid_amount'       => $paidAmount,
            'difference'        => $difference,
            'wallet_action'     => $walletAction,
            'wallet_amount'     => $walletAmount,
            'wallet_balance'    => $walletBalance,
            'total_weight'      => $totalWeight,
        ];
    }

    /**
     * Execute the Ship Global (UNITED CLASSIC) fallback for a Flying Tigers address error.
     * Called when the customer confirms the dropdown option in the frontend.
     * Performs: update shipping method, call Ship Global API, store tracking,
     * wallet deduction/refund, update invoice.
     *
     * @param \App\Models\ShipperInfo $shipper
     * @param int $customerId
     * @return array
     */
    private function executeShipGlobalFallback($shipper, $customerId)
    {
        // 1. Find the UNITED CLASSIC courier service
        $classicService = \App\Models\CourierService::whereRaw('UPPER(method) LIKE ?', ['%UNITED CLASSIC%'])->first();
        if (!$classicService) {
            \Log::error('Flying Tigers address fallback: UNITED CLASSIC service not found in database.');
            return [
                'success'          => false,
                'message'           => 'Could not find UNITED CLASSIC service for fallback. Please contact support.',
                'is_address_error'  => true,
            ];
        }

        // 2. Calculate the total chargeable weight from packages
        $packages = $shipper->packageDimensions;
        $totalWeight = 0;
        foreach ($packages as $pkg) {
            $totalWeight += floatval($pkg->chargeable_weight ?? $pkg->actual_weight_kg ?? 0);
        }

        // 3. Get consignee state for zone lookup
        $consignee = $shipper->consigneeInfo;
        $consigneeState = $consignee ? ($consignee->state ?? '') : '';

        // 4. Calculate the UNITED CLASSIC rate
        $classicRate = $this->calculateBulkRate($customerId, $classicService, $totalWeight, $consigneeState);
        $classicTotal = floatval($classicRate['total'] ?? 0);

        \Log::info('Flying Tigers address fallback: UNITED CLASSIC rate calculated: ' . $classicTotal . ' for shipper #' . $shipper->id);

        // 5. Get the amount already paid (from invoice)
        $invoice = ShipmentInvoice::where('shipper_id', $shipper->id)->first();
        $paidAmount = $invoice ? floatval($invoice->total_amount ?? 0) : 0;

        // 6. Calculate the difference
        $difference = $classicTotal - $paidAmount;

        // 7. Update the shipper's shipping method to UNITED CLASSIC
        $shipper->shipping_method = $classicService->method;
        $shipper->save();

        // Also update package dimensions shipping method
        PackageDimension::where('shipper_id', $shipper->id)->update(['shipping_method' => $classicService->method]);

        // 8. Call Ship Global API to create the shipment
        $shipGlobalResult = $this->callShipGlobalApiFromDb($shipper);
        if (!$shipGlobalResult['success']) {
            \Log::error('Flying Tigers address fallback: Ship Global API failed: ' . ($shipGlobalResult['message'] ?? 'Unknown'));
            return [
                'success'          => false,
                'message'           => 'Fallback to UNITED CLASSIC failed: ' . ($shipGlobalResult['message'] ?? 'Unknown error'),
                'is_address_error'  => true,
                'classic_rate'       => $classicTotal,
                'paid_amount'        => $paidAmount,
            ];
        }

        // 9. Extract tracking number from Ship Global response
        $apiResponse = $shipGlobalResult['data'] ?? [];
        $trackingNumber = null;
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
            $trackingNumber = $apiResponse['data']['order_number'];
        } elseif (isset($apiResponse['order_number'])) {
            $trackingNumber = $apiResponse['order_number'];
        }

        // 10. Store tracking data
        $createShipment = CreateShipment::where('shipper_id', $shipper->id)->first();
        try {
            ShipmentTracking::updateOrCreate(
                ['shipper_id' => $shipper->id],
                [
                    'customer_id'                    => $customerId,
                    'create_shipment_id'             => $createShipment ? $createShipment->id : null,
                    'response_status_code'           => '1',
                    'response_status_description'    => 'Ship Global shipment created (fallback from Flying Tigers address error)',
                    'shipment_identification_number' => $trackingNumber,
                    'total_charges_currency'         => 'INR',
                    'total_charges_amount'           => $classicTotal,
                    'billing_weight_uom'             => 'KGS',
                    'billing_weight'                 => $totalWeight,
                    'package_results'                 => null,
                    'raw_response'                    => $apiResponse,
                    'status'                         => 'created',
                ]
            );

            // Update shipper status to manifested
            $shipper->status = 'manifested';
            $shipper->save();

            // Create tracking record for manifested status
            Tracking::create([
                'awb_number'  => $shipper->awb_number,
                'shipper_id'   => $shipper->id,
                'shipping_id'  => $createShipment ? $createShipment->id : null,
                'uwc_id'       => $shipper->awb_number,
                'title'        => Tracking::getTitleForStatus('manifested'),
                'status'       => 'manifested',
            ]);

            // Log the manifested status change (Ship Global fallback from Flying Tigers address error)
            ShipmentLog::logStatus($shipper->id, $shipper->awb_number, 'manifested', 'packed', 'Shipment manifested via Ship Global (UNITED CLASSIC fallback from Flying Tigers address error). Tracking: ' . ($trackingNumber ?? 'N/A'), $customerId, 'customer');
        } catch (\Exception $e) {
            \Log::error('Flying Tigers address fallback: Failed to store tracking data: ' . $e->getMessage());
            return [
                'success'          => false,
                'message'           => 'Fallback to UNITED CLASSIC succeeded but failed to store tracking: ' . $e->getMessage(),
                'is_address_error'  => true,
                'classic_rate'       => $classicTotal,
                'paid_amount'        => $paidAmount,
            ];
        }

        // 11. Handle wallet deduction/refund
        $walletAction = 'none';
        $walletAmount = 0;
        $newBalance = 0;

        $wallet = Wallet::where('customer_id', $customerId)->first();
        if ($wallet) {
            if ($difference > 0.01) {
                // UNITED CLASSIC is more expensive → deduct difference from wallet
                $wallet->decrement('balance', $difference);
                $wallet->refresh();
                $walletAction = 'deducted';
                $walletAmount = $difference;
                $newBalance = (float) $wallet->balance;
                \Log::info('Flying Tigers address fallback: Deducted ₹' . $difference . ' from wallet (CLASSIC rate ₹' . $classicTotal . ' > paid ₹' . $paidAmount . ')');
            } elseif ($difference < -0.01) {
                // UNITED CLASSIC is cheaper → refund difference to wallet
                $refundAmount = abs($difference);
                $wallet->increment('balance', $refundAmount);
                $wallet->refresh();
                $walletAction = 'refunded';
                $walletAmount = $refundAmount;
                $newBalance = (float) $wallet->balance;
                \Log::info('Flying Tigers address fallback: Refunded ₹' . $refundAmount . ' to wallet (CLASSIC rate ₹' . $classicTotal . ' < paid ₹' . $paidAmount . ')');
            } else {
                $newBalance = (float) $wallet->balance;
            }
        }

        // 12. Update the invoice total to reflect the new rate
        if ($invoice && $classicTotal > 0) {
            $invoice->update(['total_amount' => $classicTotal]);
        }

        // Build the user-facing message
        $message = 'Shipment manifested successfully via UNITED CLASSIC (Ship Global).';
        if ($walletAction === 'deducted') {
            $message .= ' ₹' . number_format($walletAmount, 2) . ' has been deducted from your wallet (rate difference).';
        } elseif ($walletAction === 'refunded') {
            $message .= ' ₹' . number_format($walletAmount, 2) . ' has been refunded to your wallet (rate difference).';
        }

        return [
            'success'              => true,
            'message'              => $message,
            'tracking_number'      => $trackingNumber,
            'shipper_id'           => $shipper->id,
            'network'              => 'Ship Global (Fallback)',
            'is_address_error'     => true,
            'classic_rate'         => $classicTotal,
            'paid_amount'          => $paidAmount,
            'wallet_action'        => $walletAction,
            'wallet_amount'        => $walletAmount,
            'new_balance'          => $newBalance,
            'ship_global_response' => $apiResponse,
        ];
    }

    /**
     * Manifest a shipment via Ship Global (UNITED CLASSIC) fallback.
     * Called when the customer confirms the dropdown option after a Flying Tigers address error.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manifestWithShipGlobalFallback(Request $request)
    {
        try {
            if (!auth()->guard('customer')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $validated = $request->validate([
                'shipper_id' => 'required|integer',
            ]);

            $customerId = auth()->guard('customer')->id();
            $shipperId = $validated['shipper_id'];

            $shipper = ShipperInfo::where('id', $shipperId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$shipper) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found or does not belong to your account.',
                ], 404);
            }

            if ($shipper->status === 'manifested') {
                return response()->json([
                    'success' => false,
                    'message' => 'This shipment has already been manifested.',
                ], 400);
            }

            $result = $this->executeShipGlobalFallback($shipper, $customerId);

            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 500);
            }
        } catch (\Exception $e) {
            \Log::error('Ship Global fallback manifest error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel a shipment by shipper_id (called from address error fallback modal "Cancel" option).
     * Sets the shipment status to cancelled and refunds the paid amount to the customer's wallet.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelShipmentByShipperId(Request $request)
    {
        try {
            if (!auth()->guard('customer')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $validated = $request->validate([
                'shipper_id' => 'required|integer',
            ]);

            $customerId = auth()->guard('customer')->id();
            $shipperId = $validated['shipper_id'];

            $shipper = ShipperInfo::where('id', $shipperId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$shipper) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found or does not belong to your account.',
                ], 404);
            }

            // Check if already cancelled
            if ($shipper->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'This shipment is already cancelled.',
                ], 400);
            }

            // Check if already manifested — cannot cancel a manifested shipment
            if ($shipper->status === 'manifested') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel a shipment that has already been manifested.',
                ], 400);
            }

            // Determine if the shipment was paid (shipper status is ready/packed)
            $wasPaid = in_array($shipper->status, ['ready', 'packed']);
            $previousStatus = $shipper->status;
            $refundAmount = 0;

            // Find the invoice for this shipper
            $invoice = ShipmentInvoice::where('shipper_id', $shipperId)->first();

            DB::transaction(function () use ($shipper, $invoice, $wasPaid, $previousStatus, $customerId, &$refundAmount) {
                // Update shipper status to cancelled
                $shipper->update(['status' => 'cancelled']);

                // Update invoice status to cancelled
                if ($invoice) {
                    $invoice->update(['status' => 'cancelled']);
                }

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

                // Log the cancelled status change
                ShipmentLog::logStatus(
                    $shipper->id,
                    $shipper->awb_number,
                    'cancelled',
                    $previousStatus,
                    $wasPaid ? 'Shipment cancelled. Refund ₹' . number_format($invoice->total_amount ?? 0, 2) . ' to wallet.' : 'Shipment cancelled.',
                    $customerId,
                    'customer'
                );

                // Refund the paid amount to wallet
                if ($wasPaid && $invoice) {
                    $refundAmount = (float) ($invoice->total_amount ?? 0);

                    if ($refundAmount > 0) {
                        $wallet = Wallet::where('customer_id', $customerId)->first();
                        if ($wallet) {
                            $wallet->increment('balance', $refundAmount);
                            $wallet->refresh();

                            // Log the refund transaction
                            WalletTransaction::create([
                                'customer_id'   => $customerId,
                                'type'          => 'credit',
                                'reason'        => 'refund',
                                'amount'        => $refundAmount,
                                'balance_after' => $wallet->balance,
                                'reference'     => $shipper->awb_number,
                                'description'   => 'Refund of ₹' . number_format($refundAmount, 2) . ' for cancelled shipment ' . ($shipper->awb_number ?: '#' . $shipper->id),
                            ]);
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
                'success'       => true,
                'message'       => $message,
                'refund_amount' => $refundAmount,
                'new_balance'   => $newBalance,
                'shipper_id'    => $shipperId,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Cancel shipment by shipper_id error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
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

            // Server-side idempotency guard: reject duplicate recharge if an
            // identical recharge was logged for this customer within 10 seconds.
            // This prevents double entries from rapid double-clicks or AJAX retries.
            $recentDuplicate = WalletTransaction::where('customer_id', $customerId)
                ->where('reason', 'recharge')
                ->where('amount', $amount)
                ->where('created_at', '>=', now()->subSeconds(10))
                ->exists();

            if ($recentDuplicate) {
                return response()->json([
                    'success' => false,
                    'message' => 'A recharge of this amount was just processed. Please wait a moment before trying again.',
                ]);
            }

            DB::transaction(function () use ($wallet, $amount, $customerId) {
                $wallet->increment('balance', $amount);
                $wallet->refresh();

                // Log the wallet transaction
                WalletTransaction::create([
                    'customer_id'   => $customerId,
                    'type'          => 'credit',
                    'reason'        => 'recharge',
                    'amount'        => $amount,
                    'balance_after' => $wallet->balance,
                    'description'   => 'Wallet recharge of ₹' . number_format($amount, 2),
                ]);
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

    /**
     * Determine whether the customer belongs to the Courier / Aggregator category.
     */
    private function isCourierOrAggregator(Customer $customer): bool
    {
        $category = $customer->relationLoaded('businessCategory')
            ? $customer->businessCategory
            : $customer->businessCategory()->first();

        if (!$category) {
            return false;
        }

        $allowedCategories = [
            'courier-or-aggregator',
            'courier-aggregator',
            'courier or aggregator',
            'courier / aggregator',
            'courier/aggregator',
        ];

        return in_array(strtolower(trim((string) $category->category_slug)), $allowedCategories, true)
            || in_array(strtolower(trim((string) $category->category_name)), $allowedCategories, true);
    }

    /**
     * Determine whether a customer can create and manage saved customers.
     */
    private function canManageSavedCustomers(Customer $customer): bool
    {
        return $this->isCourierOrAggregator($customer);
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
