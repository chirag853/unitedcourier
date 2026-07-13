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
use App\Models\ShipmentLog;
use App\Models\WalletTransaction;
use App\Models\CourierService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
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
        $userType = $businessCategory ? $businessCategory->user_type : 'Personal';

        return view('customer.dashboard', compact(
            'customer', 'totalBooked', 'pickupPending', 'outForDelivery', 'delivered',
            'recentShipments', 'walletBalance', 'totalShippedValue', 'totalShippedCost',
            'bookedChangePercent', 'pickupPendingChangePercent', 'outForDeliveryChangePercent', 'deliveredChangePercent',
            'userType', 'businessCategory'
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
        $destinations = \App\Models\Destination::where('is_active', true)->orderBy('name')->get();
        return view('customer.create-shipment', compact('customer', 'courierServices', 'zones', 'destinations'));
    }
    
    public function kycSubmit(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'gst_number' => 'nullable|string|size:15|regex:/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/',
                'gst_verified' => 'boolean',
                'otp_verified' => 'boolean',
                'aadhar_number' => 'nullable|string|max:20',
                'aadhar_verified' => 'boolean',
                'organization_name' => 'nullable|string|max:255',
                'authorized_signatory' => 'nullable|string|max:255',
                'signature' => 'nullable|string',
                'billing_address' => 'nullable|string|max:1000',
                'billing_gst' => 'nullable|string|max:15',
                'billing_contact' => 'nullable|string|max:20',
                'billing_email' => 'nullable|string|email|max:255',
                'terms_accepted' => 'boolean',
                'terms_accepted_at' => 'nullable|date',
            ], [
                'gst_number.regex' => 'The GST number format is invalid. It must be a valid 15-character GSTIN (e.g. 22AAAAA0000A1Z5).',
                'gst_number.size' => 'The GST number must be exactly 15 characters.',
            ]);

            // Get current customer
            $customer = auth()->guard('customer')->user();
            
            // Prepare KYC data
            $kycData = [
                'customer_id' => $customer->id,
                'gst_number' => $request->gst_number,
                'gst_verified' => $request->gst_verified ?? false,
                'otp_verified' => $request->otp_verified ?? false,
                'aadhar_number' => $request->aadhar_number,
                'aadhar_verified' => $request->aadhar_verified ?? false,
                'organization_name' => $request->organization_name,
                'authorized_signatory' => $request->authorized_signatory,
                'signature' => $request->signature,
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

            return response()->json([
                'success' => true,
                'message' => 'KYC application submitted successfully! Your application is now under review.',
                'kyc_id' => $kyc->id
            ]);

        } catch (\Throwable $e) {
            \Log::error('KYC submit error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error submitting KYC application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify GST number during KYC.
     * Validates the GSTIN format:
     *   - 2-digit state code (01-38)
     *   - 10-character PAN (5 letters + 4 digits + 1 letter)
     *   - 1-char entity code (1-9, A-Z)
     *   - 1 char (default 'Z')
     *   - 1-char checksum (alphanumeric)
     * Example: 22AAAAA0000A1Z5
     */
    public function verifyGst(Request $request)
    {
        try {
            $validated = $request->validate([
                'gst_number' => 'required|string|size:15',
            ]);

            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your GST number.'
                ], 401);
            }

            $gst = strtoupper(trim($request->gst_number));

            // GSTIN format validation:
            // [0-3][0-9]  -> state code 01-38
            // [A-Z]{5}    -> first 5 letters of PAN
            // [0-9]{4}    -> 4 digits of PAN
            // [A-Z]       -> last letter of PAN
            // [1-9A-Z]    -> entity code
            // Z           -> default 'Z'
            // [0-9A-Z]    -> checksum (alphanumeric)
            if (!preg_match('/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', $gst)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid GST number format. A valid GSTIN is 15 characters: 2-digit state code, 10-char PAN, entity code, Z, and checksum (e.g. 22AAAAA0000A1Z5).'
                ], 422);
            }

            // Validate state code is within 01-38
            $stateCode = substr($gst, 0, 2);
            if ((int) $stateCode < 1 || (int) $stateCode > 38) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid state code in GST number. State code must be between 01 and 38.'
                ], 422);
            }

            // Validate checksum digit using the official GSTIN checksum algorithm
            $gstWithoutChecksum = substr($gst, 0, 14);
            $actualChecksum = substr($gst, 14, 1);
            $computedChecksum = $this->computeGstChecksum($gstWithoutChecksum);
            
            if ($computedChecksum !== $actualChecksum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid GST number. The checksum digit does not match. Please verify your GSTIN.'
                ], 422);
            }

            session([
                'kyc_gst_number' => $gst,
                'kyc_gst_verified' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'GST number verified successfully!',
                'gst_number' => $gst,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('GST verification error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'GST verification failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
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
     * Verify Aadhar number during KYC.
     * Accepts an Aadhar number, validates the format, and marks it as verified.
     */
    public function verifyAadhar(Request $request)
    {
        try {
            $validated = $request->validate([
                'aadhar_number' => 'required|string|size:12',
            ]);

            // Get current customer
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your Aadhar.'
                ], 401);
            }

            // Basic Aadhar format validation: 12 digits, not starting with 0 or 1
            $aadhar = preg_replace('/\s+/', '', $request->aadhar_number);
            if (!preg_match('/^[2-9][0-9]{11}$/', $aadhar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Aadhar number. It must be 12 digits and cannot start with 0 or 1.'
                ], 422);
            }

            // Store Aadhar verification in session so it can be included when KYC is submitted
            session([
                'kyc_aadhar_number' => $aadhar,
                'kyc_aadhar_verified' => true,
            ]);

            // Also update the customer record if an aadhar_number column exists
            if (\Schema::hasColumn('customers', 'aadhar_number')) {
                $customer->aadhar_number = $aadhar;
                $customer->aadhar_verified = true;
                $customer->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Aadhar number verified successfully!',
                'aadhar_number' => $aadhar,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('Aadhar verification error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Aadhar verification failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify PAN number during Personal KYC.
     * Accepts a PAN number, validates the format, and marks it as verified.
     */
    public function verifyPan(Request $request)
    {
        try {
            $validated = $request->validate([
                'pan_number' => 'required|string|size:10',
            ]);

            // Get current customer
            $customer = auth()->guard('customer')->user();
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to verify your PAN.'
                ], 401);
            }

            // PAN format validation: 5 letters + 4 digits + 1 letter (e.g. ABCDE1234F)
            $pan = strtoupper(preg_replace('/\s+/', '', $request->pan_number));
            if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PAN number. It must be 10 characters: 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).'
                ], 422);
            }

            // Store PAN verification in session so it can be included when KYC is submitted
            session([
                'kyc_pan_number' => $pan,
                'kyc_pan_verified' => true,
            ]);

            // Also update the customer record if a pan_number column exists
            if (\Schema::hasColumn('customers', 'pan_number')) {
                $customer->pan_number = $pan;
                $customer->pan_verified = true;
                $customer->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'PAN number verified successfully!',
                'pan_number' => $pan,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('PAN verification error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'PAN verification failed. Please try again.',
                'error' => $e->getMessage()
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
                'aadhar_front_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'aadhar_back_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'aadhar_address' => 'required|string|max:1000',
                'pan_number' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
                'pan_holder_name' => 'required|string|max:255',
                'pan_dob' => 'required|date|before:today',
                'pan_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'signature_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
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

            // Basic Aadhaar format validation: 12 digits, not starting with 0 or 1
            $aadhar = preg_replace('/\s+/', '', $request->aadhar_number);
            if (!preg_match('/^[2-9][0-9]{11}$/', $aadhar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Aadhaar number. It must be 12 digits and cannot start with 0 or 1.'
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

            // Handle signature document upload
            $signaturePath = null;
            if ($request->hasFile('signature_document')) {
                $file = $request->file('signature_document');
                $filename = time() . '_signature_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/signature_documents'), $filename);
                $signaturePath = 'uploads/signature_documents/' . $filename;
            }

            // Handle merchant agreement document upload
            $merchantAgreementPath = null;
            if ($request->hasFile('merchant_agreement')) {
                $file = $request->file('merchant_agreement');
                $filename = time() . '_merchant_agreement_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/merchant_agreements'), $filename);
                $merchantAgreementPath = 'uploads/merchant_agreements/' . $filename;
            }

            // Normalize PAN to uppercase
            $panNumber = strtoupper(preg_replace('/\s+/', '', $validated['pan_number']));

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
        } catch (\Throwable $e) {
            \Log::error('Personal KYC submission error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Personal KYC submission failed. Please try again.',
                'error' => $e->getMessage()
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
            // Validate the request
            $validated = $request->validate([
                'is_csb_v' => 'required|boolean',
                'is_gst' => 'required|boolean',
                'is_lut' => 'required|boolean',
                'lut_verified' => 'nullable|boolean',
                'ad_code' => 'required|string|max:50',
                'ad_code_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'iec_number' => 'required|string|max:50',
                'iec_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'gst_certificate_number' => 'required|string|max:50',
                'gst_certificate_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'gst_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'bank_account_number' => 'required|string|max:50',
                'bank_type' => 'required|in:private,government',
                'lut_document' => 'nullable|file|mimes:pdf|max:5120',
                'lut_expiry_date' => 'nullable|date',
                'lut_bond_year' => 'nullable|string|max:10',
                'aadhar_number' => 'required|string|size:12',
                'aadhar_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'signature_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'billing_address' => 'required|string|max:1000',
                'billing_gst' => 'nullable|string|max:15',
                'billing_contact' => 'required|string|max:20',
                'billing_email' => 'required|email|max:255',
                'merchant_agreement' => 'required|file|mimes:pdf|max:10240',
                'terms_accepted' => 'required|boolean',
            ], [
                'aadhar_number.size' => 'The Aadhaar number must be exactly 12 digits.',
            ]);

            // Get current customer
            $customer = auth()->guard('customer')->user();

            // Basic Aadhaar format validation: 12 digits, not starting with 0 or 1
            $aadhar = preg_replace('/\s+/', '', $request->aadhar_number);
            if (!preg_match('/^[2-9][0-9]{11}$/', $aadhar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Aadhaar number. It must be 12 digits and cannot start with 0 or 1.'
                ], 422);
            }

            // Ensure upload directories exist
            $uploadDirs = [
                'uploads/lut_documents',
                'uploads/gst_documents',
                'uploads/iec_documents',
                'uploads/gst_certificate_documents',
                'uploads/aadhar_documents',
                'uploads/signature_documents',
                'uploads/ad_code_documents',
                'uploads/merchant_agreements',
            ];
            foreach ($uploadDirs as $dir) {
                $path = public_path($dir);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
            }

            // Handle LUT document upload
            $lutDocumentPath = null;
            if ($request->hasFile('lut_document')) {
                $file = $request->file('lut_document');
                $filename = time() . '_lut_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/lut_documents'), $filename);
                $lutDocumentPath = 'uploads/lut_documents/' . $filename;
            }

            // Handle GST document upload
            $gstDocumentPath = null;
            if ($request->hasFile('gst_document')) {
                $file = $request->file('gst_document');
                $filename = time() . '_gst_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/gst_documents'), $filename);
                $gstDocumentPath = 'uploads/gst_documents/' . $filename;
            }

            // Handle IEC document upload
            $iecDocumentPath = null;
            if ($request->hasFile('iec_document')) {
                $file = $request->file('iec_document');
                $filename = time() . '_iec_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/iec_documents'), $filename);
                $iecDocumentPath = 'uploads/iec_documents/' . $filename;
            }

            // Handle GST certificate document upload
            $gstCertificateDocumentPath = null;
            if ($request->hasFile('gst_certificate_document')) {
                $file = $request->file('gst_certificate_document');
                $filename = time() . '_gstcert_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/gst_certificate_documents'), $filename);
                $gstCertificateDocumentPath = 'uploads/gst_certificate_documents/' . $filename;
            }

            // Handle Aadhaar document upload
            $aadharDocumentPath = null;
            if ($request->hasFile('aadhar_document')) {
                $file = $request->file('aadhar_document');
                $filename = time() . '_aadhar_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aadhar_documents'), $filename);
                $aadharDocumentPath = 'uploads/aadhar_documents/' . $filename;
            }

            // Handle signature document upload
            $signatureDocumentPath = null;
            if ($request->hasFile('signature_document')) {
                $file = $request->file('signature_document');
                $filename = time() . '_signature_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/signature_documents'), $filename);
                $signatureDocumentPath = 'uploads/signature_documents/' . $filename;
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

            // Create or update CSB Form record
            $existingCsbForm = CsbForm::where('customer_id', $customer->id)->latest()->first();

            $csbData = [
                'customer_id' => $customer->id,
                'is_csb_v' => $validated['is_csb_v'],
                'is_gst' => $validated['is_gst'],
                'is_lut' => $validated['is_lut'],
                'lut_verified' => $validated['lut_verified'] ?? false,
                'ad_code' => $validated['ad_code'],
                'ad_code_document' => $adCodeDocumentPath ?? ($existingCsbForm->ad_code_document ?? null),
                'iec_number' => $validated['iec_number'],
                'iec_document' => $iecDocumentPath ?? ($existingCsbForm->iec_document ?? null),
                'gst_certificate_number' => $validated['gst_certificate_number'],
                'gst_certificate_document' => $gstCertificateDocumentPath ?? ($existingCsbForm->gst_certificate_document ?? null),
                'bank_account_number' => $validated['bank_account_number'],
                'bank_type' => $validated['bank_type'],
                'lut_document' => $lutDocumentPath ?? ($existingCsbForm->lut_document ?? null),
                'gst_document' => $gstDocumentPath ?? ($existingCsbForm->gst_document ?? null),
                'lut_expiry_date' => $validated['lut_expiry_date'] ?? null,
                'lut_bond_year' => $validated['lut_bond_year'] ?? null,
                'aadhar_number' => $aadhar,
                'aadhar_verified' => true,
                'aadhar_document' => $aadharDocumentPath ?? ($existingCsbForm->aadhar_document ?? null),
                'signature_document' => $signatureDocumentPath ?? ($existingCsbForm->signature_document ?? null),
                'billing_address' => $validated['billing_address'],
                'billing_gst' => $validated['billing_gst'] ?? null,
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
            $existingBusinessKyc = KycDetail::where('customer_id', $customer->id)
                ->where('kyc_type', 'business')
                ->latest()
                ->first();

            $businessKycData = [
                'customer_id' => $customer->id,
                'kyc_type' => 'business',
                'aadhar_number' => $aadhar,
                'aadhar_verified' => true,
                'gst_number' => $validated['gst_certificate_number'] ?? null,
                'gst_verified' => true,
                'organization_name' => $customer->first_name . ' ' . $customer->last_name,
                'authorized_signatory' => $customer->first_name . ' ' . $customer->last_name,
                'billing_address' => $validated['billing_address'],
                'billing_gst' => $validated['billing_gst'] ?? null,
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

            // Update customer record with Aadhaar and CSB status
            $customer->aadhar_number = $aadhar;
            $customer->aadhar_verified = true;
            // Business KYC: CSB-IV (1) or CSB-V (2) based on selection
            $customer->csb_status = $validated['is_csb_v'] ? 2 : 1;
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Business KYC (CSB-V) submitted successfully! Your application is now under review.',
                'redirect' => route('customer.kyc.summary')
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
                // UK destination  → only show UNITED AIR PREMIUM DDP (nothing else)
                // Non-UK destination → exclude UNITED AIR PREMIUM DDP (show all others)
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

                // Canada destination detection (mirrors getUpsRate):
                // The Excel "Destination" column may contain "Canada" or "CA".
                $isCanadaDestination = (
                    $destUpper === 'CANADA' ||
                    $destUpper === 'CA' ||
                    str_contains($destUpper, 'CANADA')
                );

                // Collect ALL available service rates for this shipment (like getUpsRate all-services mode)
                $allRates = [];
                $defaultRate = null; // first available rate as default selection

                foreach ($allServices as $service) {
                    $methodUpper = strtoupper(trim($service->method ?? ''));
                    $isAirPremium = str_contains($methodUpper, 'UNITED AIR PREMIUM');
                    if ($isUkDestination && !$isAirPremium) {
                        continue; // UK: only UNITED AIR PREMIUM DDP is shown
                    }
                    if (!$isUkDestination && $isAirPremium) {
                        continue; // Non-UK: UNITED AIR PREMIUM DDP is hidden
                    }

                    // Canada destination filtering (mirrors getUpsRate):
                    // - Canada destination → show ONLY Canada services, hide all others.
                    // - Non-Canada destination → hide Canada services, show all others.
                    $isCanadaSvc = $this->isCanadaService($service);
                    if ($isCanadaDestination && !$isCanadaSvc) {
                        continue; // Canada: skip non-Canada services
                    }
                    if (!$isCanadaDestination && $isCanadaSvc) {
                        continue; // Non-Canada: skip Canada services
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


    
    public function getUpsRate(Request $request)
    {
        // print_r($request->all());
        try {
            $serviceId = $request->service_id;
            $totalWeight = floatval($request->total_weight ?? 0);
            $consigneeState = $request->consignee_state;
            $deliveryDestination = $request->delivery_destination;

            // Optional: per-package chargeable weights for box-wise rate calculation.
            // When more than one package is present, only "United Ground Premium"
            // service is offered and its rate is computed box-wise (per package).
            $packageWeights = $request->package_weights; // array of floats
            $isMultiPackage = is_array($packageWeights) && count($packageWeights) > 1;

            // Get the currently logged-in customer
            $customer = auth()->guard('customer')->user();
            $customerId = $customer ? $customer->id : 0;

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to view rates.'
                ], 401);
            }

            // Guard: rates are only returned when the total weight is greater
            // than 0. If no actual weight has been entered, respond with an
            // empty rate list so the frontend shows nothing.
            if ($totalWeight <= 0) {
                return response()->json([
                    'success' => true,
                    'customer_exists' => false,
                    'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : null,
                    'all_rates' => [],
                    'message' => 'Please enter Actual Weight (Act. Wt) greater than 0 to view rates.',
                ]);
            }

            // Look up zone by consignee state (do this once for both modes)
            $zone = null;
            if (!empty($consigneeState)) {
                $zone = \App\Models\Zone::where('zone_code', $consigneeState)->first();
            }

            // DPD (PostShipping) rates are only available for UK destinations.
            // For any non-UK destination (e.g. US), DPD rates are hidden.
            $isUkDestination = ($deliveryDestination === 'UK - United Kingdom');

            // Canada services (CANADA-DDP, CANADA-ECOM) are only shown when the
            // delivery destination is Canada; all other services are hidden for
            // Canada, and Canada services are hidden for every other destination.
            $isCanadaDestination = ($deliveryDestination === 'Canada');

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

                    // Canada destination filtering:
                    // - Canada destination → show ONLY Canada services, hide all others.
                    // - Non-Canada destination → hide Canada services, show all others.
                    $isCanadaSvc = $this->isCanadaService($service);
                    if ($isCanadaDestination && !$isCanadaSvc) {
                        continue; // Canada: skip non-Canada services
                    }
                    if (!$isCanadaDestination && $isCanadaSvc) {
                        continue; // Non-Canada: skip Canada services
                    }

                    // Multi-package rule: when more than one package is present,
                    // only "United Ground Premium" service is offered.
                    // (DB stores the method as "UNITED GROUND PREMIUM" — compare
                    // case-insensitively so it matches regardless of casing.)
                    if ($isMultiPackage && strcasecmp($service->method, 'UNITED GROUND PREMIUM') !== 0) {
                        continue;
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

                    // -------------------------------------------------------------------
                    // BOX-WISE RATE CALCULATION (multi-package, United Ground Premium only)
                    // -------------------------------------------------------------------
                    // When multiple packages are present, the rate is computed per box:
                    // each package's chargeable weight is matched to its own rate row,
                    // and the base/fuel/gst amounts are summed into ONE combined rate card.
                    // The combined card carries a `box_breakdown` array so the frontend can
                    // render a per-box table, and `is_multi_package => true`.
                    // -------------------------------------------------------------------
                    if ($isMultiPackage && strcasecmp($service->method, 'UNITED GROUND PREMIUM') === 0) {
                        $boxBreakdown = [];
                        $combinedBase = 0;
                        $combinedFuel = 0;
                        $combinedGst = 0;
                        $combinedTotal = 0;
                        $firstMatchedRate = null;
                        $boxIndex = 1;
                        $allBoxesMatched = true;

                        foreach ($packageWeights as $pkgWt) {
                            $pkgWt = floatval($pkgWt);
                            if ($pkgWt <= 0) {
                                $pkgWt = 1; // default 1kg if missing
                            }

                            // Find the rate row matching this package's weight + zone
                            $boxMatched = null;
                            foreach ($rates as $r) {
                                if (!($pkgWt >= $r->wt_range_start && $pkgWt <= $r->wt_range_end)) {
                                    continue;
                                }
                                $zoneNo = $r->zone_no;
                                if ($zoneNo === null || $zoneNo == 0) {
                                    $boxMatched = $r;
                                    break; // Zone-independent rate - use it
                                }
                                if ($zone && $zone->zone_number_testing !== null && $zoneNo == $zone->zone_number_testing) {
                                    $boxMatched = $r;
                                    break; // Zone-matched rate
                                }
                            }

                            if (!$boxMatched) {
                                $allBoxesMatched = false;
                                break; // a box has no matching rate → skip this service
                            }

                            if (!$firstMatchedRate) {
                                $firstMatchedRate = $boxMatched;
                            }

                            // Compute per-box amounts using the SAME formula as the frontend:
                            //   fuel = fuel_charge > 0 ? fuel_charge : (base * fuel_pct / 100)
                            //   gst  = gst_amount  > 0 ? gst_amount  : ((base + fuel) * gst_pct / 100)
                            $boxBase = floatval($boxMatched->price);
                            $boxFuelPct = floatval($boxMatched->fuel_percentage);
                            $boxFuelCharge = floatval($boxMatched->fuel_charge);
                            $boxGstPct = floatval($boxMatched->gst_percentage);
                            $boxGstAmount = floatval($boxMatched->gst_amount);

                            $boxFuel = $boxFuelCharge > 0 ? $boxFuelCharge : ($boxBase * $boxFuelPct / 100);
                            $boxGst = $boxGstAmount > 0 ? $boxGstAmount : (($boxBase + $boxFuel) * $boxGstPct / 100);
                            $boxTotal = $boxBase + $boxFuel + $boxGst;

                            $boxBreakdown[] = [
                                'box' => $boxIndex,
                                'weight' => $pkgWt,
                                'base' => $boxBase,
                                'fuel' => $boxFuel,
                                'gst' => $boxGst,
                                'total' => $boxTotal,
                            ];

                            $combinedBase += $boxBase;
                            $combinedFuel += $boxFuel;
                            $combinedGst += $boxGst;
                            $combinedTotal += $boxTotal;
                            $boxIndex++;
                        }

                        // Only emit a combined card if every box found a matching rate.
                        if ($allBoxesMatched && $firstMatchedRate) {
                            $allRates[] = [
                                'rate_id' => $firstMatchedRate->id,
                                'service_id' => $service->id,
                                'method' => $service->method,
                                'method_display' => $service->method . ' ' . $service->tat,
                                'network' => $service->network,
                                'method_code' => $service->method_code,
                                'tat' => $service->tat,
                                'delivery_days' => $service->tat,
                                'scode' => $service->scode,
                                // Combined amounts so the frontend shows the grand total.
                                'price' => $combinedBase,
                                'zone_no' => $firstMatchedRate->zone_no,
                                'zone_name' => ($firstMatchedRate->zone_no && $zone && $zone->zone_number_testing !== null && $firstMatchedRate->zone_no == $zone->zone_number_testing) ? $zone->zone_name : null,
                                'zone_code' => ($firstMatchedRate->zone_no && $zone && $zone->zone_number_testing !== null && $firstMatchedRate->zone_no == $zone->zone_number_testing) ? $zone->zone_code : null,
                                // Pass the already-computed fuel/gst as fixed amounts so the
                                // frontend does NOT recompute them from percentages (avoids
                                // double-counting). Percentages are zeroed out accordingly.
                                'fuel_charge' => $combinedFuel,
                                'fuel_percentage' => 0,
                                'gst_percentage' => 0,
                                'gst_amount' => $combinedGst,
                                // Multi-package extras for the frontend breakdown table.
                                'is_multi_package' => true,
                                'box_breakdown' => $boxBreakdown,
                            ];
                        }
                    } else {
                        // -----------------------------------------------------------------
                        // STANDARD RATE MATCHING (single package, or non-multi service)
                        // -----------------------------------------------------------------
                        // Find rates matching the current weight AND the selected zone.
                        // Matching uses the zone's `zone_number_testing` field (compared against
                        // the rate's `zone_no`). Zone-independent rates (zone_no=null/0) are
                        // always shown weight-wise; zone-matched rates are shown when the rate's
                        // zone_no equals the selected zone's zone_number_testing.
                        $matchedRates = $rates->filter(function ($r) use ($totalWeight, $zone) {
                            // print_r("Checking rate ID {$r->id}: weight range {$r->wt_range_start}-{$r->wt_range_end}, zone_no={$r->zone_no}\n");
                            if (!($totalWeight >= $r->wt_range_start && $totalWeight <= $r->wt_range_end)) {
                                return false;
                            }
                            $zoneNo = $r->zone_no;
                            if ($zoneNo === null || $zoneNo == 0) {
                                return true; // Zone-independent rate - always show
                            }
                            if ($zone && $zone->zone_number_testing !== null && $zoneNo == $zone->zone_number_testing) {
                                return true; // Zone-matched rate (via zone_number_testing)
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
                                'zone_name' => ($matchedRate->zone_no && $zone && $zone->zone_number_testing !== null && $matchedRate->zone_no == $zone->zone_number_testing) ? $zone->zone_name : null,
                                'zone_code' => ($matchedRate->zone_no && $zone && $zone->zone_number_testing !== null && $matchedRate->zone_no == $zone->zone_number_testing) ? $zone->zone_code : null,
                                'fuel_charge' => $matchedRate->fuel_charge,
                                'fuel_percentage' => $matchedRate->fuel_percentage,
                                'gst_percentage' => $matchedRate->gst_percentage,
                                'gst_amount' => $matchedRate->gst_amount,
                            ];
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'customer_exists' => $customerRatesExist,
                    'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : null,
                    'selected_zone' => $zone ? [
                        'zone_id' => $zone->id,
                        'zone_number' => $zone->zone_number_testing,
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

            // Canada destination filtering (mirrors ALL-SERVICES MODE):
            // - Canada destination → ONLY Canada services are allowed; reject all others.
            // - Non-Canada destination → Canada services are not allowed; reject them.
            $isCanadaSvc = $this->isCanadaService($service);
            if ($isCanadaDestination && !$isCanadaSvc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Canada services are available for Canada destinations.'
                ], 404);
            }
            if (!$isCanadaDestination && $isCanadaSvc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Canada service is only available for Canada destinations.'
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

            // Find the rate that matches the current weight AND the selected zone.
            // Matching uses the zone's `zone_number_testing` field (compared against
            // the rate's `zone_no`). Zone-independent rates (zone_no=null/0) are
            // always shown weight-wise; zone-matched rates are shown when the rate's
            // zone_no equals the selected zone's zone_number_testing.
            $filteredRates = $rates->filter(function ($r) use ($totalWeight, $zone) {
                if (!($totalWeight >= $r->wt_range_start && $totalWeight <= $r->wt_range_end)) {
                    return false;
                }
                $zoneNo = $r->zone_no;
                if ($zoneNo === null || $zoneNo == 0) {
                    return true; // Zone-independent rate - always show
                }
                if ($zone && $zone->zone_number_testing !== null && $zoneNo == $zone->zone_number_testing) {
                    return true; // Zone-matched rate (via zone_number_testing)
                }
                return false; // Rate from a different zone - exclude
            });

            return response()->json([
                'success' => true,
                'customer_exists' => $customerExists,
                'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : null,
                'selected_zone' => $zone ? [
                    'zone_id' => $zone->id,
                    'zone_number' => $zone->zone_number_testing,
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
            $previousStatus = $shipper->status;
            $refundAmount = 0;

            // Update status to cancelled and refund wallet if paid
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
                    $wasPaid ? 'Shipment cancelled. Refund ₹' . number_format($invoice->total_amount, 2) . ' to wallet.' : 'Shipment cancelled.',
                    $customerId,
                    'customer'
                );

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
            } elseif ($this->isFlyingTigersMethod($shippingMethod)) {
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

                    \Log::info('bulkManifest: Shipper #' . $shipperId . ' → network="' . $network . '"');

                    // Priority 1: PostShipping (DPD/UK) for UNITED AIR PREMIUM DDP / UNITED PRIOR POST DDP
                    if ($this->isPostShippingMethod($shippingMethod)) {
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

                    } elseif ($this->isFlyingTigersMethod($shippingMethod)) {
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