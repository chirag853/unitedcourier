<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from crms.dreamstechnologies.com/html/template/dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 31 Jul 2025 06:57:23 GMT -->

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard | CRMS - Advanced Bootstrap 5 Admin Template for Customer Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
    <meta name="keywords"
        content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="18629d5768be2989b1211a1d-text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/css/dataTables.bootstrap5.min.css') }}">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar Start -->
        @include('customer.partials.customer_dashboard_header')
        <!-- Topbar End -->

        <!-- Dashboard-specific dropdown fix script -->
        

        <!-- Search Modal -->
        <div class="modal fade" id="searchModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-transparent">
                    <div class="card shadow-none mb-0">
                        <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                            <i class="ti ti-search fs-22"></i>
                            <input type="search" class="form-control border-0" placeholder="Search">
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i
                                    class="ti ti-x fs-22"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidenav Menu Start -->
        @include('customer.partials.sidebar')
        <!-- Sidenav Menu End -->

        <!-- ========================
			Start Page Content
		========================= -->

        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content pb-0">

                <!-- Page Header -->
                <!-- <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <div id="reportrange" class="reportrange-picker d-flex align-items-center shadow">
                            <i class="ti ti-calendar-due text-dark fs-14 me-1"></i><span class="reportrange-picker-field">9 Jun 25 - 9 Jun 25</span>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                 -->
                <!-- End Page Header -->

                <!-- Start Welcome Wrap -->
                <!-- <div class="welcome-wrap mb-4">
					<div class=" d-flex align-items-center justify-content-between flex-wrap gap-3 bg-dark rounded p-4">
						<div>
							<h2 class="mb-1 text-white fs-24">Welcome Back, Rahul</h2>
							<p class="text-light fs-14 mb-0">Check all your stats below:</p>
						</div>
						<div class="d-flex align-items-center flex-wrap gap-2">
							<a href="company.html" class="btn btn-danger btn-sm">Companies</a>
							<a href="packages.html" class="btn btn-light btn-sm">All Packages</a>
						</div>
					</div>
				</div>	 -->
                <!-- Endc Welcome Wrap -->

                {{-- Show KYC status if customer is logged in and KYC exists but not approved --}}
                @if(auth()->guard('customer')->check())
                    @php
                        $customerId = auth()->guard('customer')->user()->id;
                        $kycExists = \App\Models\KycDetail::where('customer_id', $customerId)->exists();
                        $kycRecord = \App\Models\KycDetail::where('customer_id', $customerId)->first();
                    @endphp
                    
                    @if($kycExists && $kycRecord && $kycRecord->kyc_status != 'approved')
                        <!-- KYC Progress Bar -->
                        <div class="kyc-progress-container mb-4">
                            <h5 class="mb-3">KYC Verification Progress</h5>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated {{ $kycRecord->kyc_status == 'pending' ? 'bg-warning' : ($kycRecord->kyc_status == 'under_review' ? 'bg-info' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $kycRecord->kyc_status == 'pending' ? '33' : ($kycRecord->kyc_status == 'under_review' ? '66' : '100') }}%">
                                    {{ ucfirst($kycRecord->kyc_status) }}
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">Pending</small>
                                <small class="text-muted">Under Review</small>
                                <small class="text-muted">Approved</small>
                            </div>
                            <div class="mt-3">
                                @if($kycRecord->kyc_status == 'pending')
                                    <p class="mb-0 text-warning"><i class="fas fa-clock"></i> Your KYC application is pending. Please complete the verification process.</p>
                                @elseif($kycRecord->kyc_status == 'under_review')
                                    <p class="mb-0 text-info"><i class="fas fa-hourglass-half"></i> Your KYC application is under review. We'll notify you once it's approved.</p>
                                @elseif($kycRecord->kyc_status == 'rejected')
                                    <p class="mb-0 text-danger"><i class="fas fa-exclamation-triangle"></i> Your KYC application was rejected. Please contact support for assistance.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if(!$kycExists)
                        <!-- KYC Form Start -->
                        <style>
                            :root {
                                --brand-blue-main: #2563eb;
                                --brand-purple: #9333ea;
                                --text-dark: #1a1a1a;
                                --text-muted: #666;
                                --step-inactive: #e2e8f0;
                                --bg-light: #f8fafc;
                                --input-bg: #f8f9fa;
                                --input-border: #e9ecef;
                            }

                            body {
                                font-family: 'Inter', sans-serif;
                                background-color: var(--bg-light);
                                color: var(--text-dark);
                                min-height: 100vh;
                            }

                            h1, h2, h3, h4, h5, .step-title {
                                font-family: 'Outfit', sans-serif;
                            }

                            .gradient-text {
                                background: linear-gradient(to right, var(--brand-blue-main), var(--brand-purple));
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                            }

                            /* Stepper Header Styling */
                            .stepper-container {
                                max-width: 1000px;
                                margin: 40px auto;
                                padding: 0 20px;
                            }

                            .stepper-title {
                                text-align: center;
                                font-size: 28px;
                                font-weight: 700;
                                margin-bottom: 40px;
                                color: var(--text-dark);
                            }

                            .stepper-wrapper {
                                display: flex;
                                justify-content: space-between;
                                position: relative;
                                margin-bottom: 50px;
                            }

                            .step-item {
                                flex: 1;
                                position: relative;
                                text-align: left;
                                padding-right: 15px;
                            }

                            .step-bar {
                                height: 6px;
                                background: var(--step-inactive);
                                border-radius: 10px;
                                margin-bottom: 12px;
                                position: relative;
                                overflow: hidden;
                            }

                            .step-bar-fill {
                                position: absolute;
                                height: 100%;
                                width: 0%;
                                background: linear-gradient(to right, var(--brand-blue-main), var(--brand-purple));
                                transition: width 0.4s ease;
                            }

                            .step-item.active .step-bar-fill { width: 100%; }
                            .step-item.completed .step-bar-fill { width: 100%; }

                            .step-label {
                                font-size: 12px;
                                font-weight: 700;
                                color: var(--text-muted);
                                line-height: 1.4;
                                transition: color 0.3s;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                            }

                            .step-item.active .step-label { color: var(--brand-blue-main); }

                            /* Form Card Styling */
                            .kyc-card {
                                background: white;
                                border-radius: 30px;
                                box-shadow: 0 30px 60px rgba(0,0,0,0.08);
                                margin: 0 auto 100px;
                                padding: 45px;
                                border: 1px solid #f1f5f9;
                            }

                            .kyc-card-title {
                                font-size: 28px;
                                font-weight: 700;
                                margin-bottom: 10px;
                            }

                            /* Matches your Registration Form Style */
                            .form-label-custom {
                                font-size: 13px;
                                font-weight: 700;
                                text-transform: uppercase;
                                letter-spacing: 0.8px;
                                color: #adb5bd;
                                margin-bottom: 8px;
                                display: block;
                            }

                            .input-group-custom {
                                position: relative;
                                display: flex;
                                align-items: center;
                                margin-bottom: 20px;
                            }

                            .input-group-custom i {
                                position: absolute;
                                left: 16px;
                                color: #adb5bd;
                                font-size: 14px;
                                pointer-events: none;
                                transition: color 0.3s;
                            }

                            .input-custom {
                                background-color: var(--input-bg);
                                border: 1px solid var(--input-border);
                                border-radius: 12px;
                                padding: 14px 16px 14px 42px;
                                font-weight: 500;
                                font-size: 14px;
                                width: 100%;
                                transition: all 0.3s;
                            }

                            .input-custom:focus {
                                background-color: white;
                                border-color: var(--brand-blue-main);
                                box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.05);
                                outline: none;
                            }

                            .input-custom:focus + i {
                                color: var(--brand-blue-main);
                            }

                            /* PRIMARY ACTION BUTTONS */
                            .btn-primary-custom {
                                border: none;
                                color: white !important;
                                font-weight: 700;
                                padding: 16px;
                                border-radius: 12px;
                                width: 100%;
                                transition: all 0.3s ease;
                                text-transform: uppercase;
                                letter-spacing: 1px;
                                font-size: 14px;
                                display: block;
                                text-align: center;
                                text-decoration: none;
                                background: linear-gradient(270deg, var(--brand-blue-main), var(--brand-purple), var(--brand-blue-main));
                                background-size: 200% 200%;
                                animation: bgGradientMove 2s linear infinite;
                            }

                            .btn-primary-custom:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
                                opacity: 0.9;
                            }

                            /* SECONDARY OUTLINE BUTTONS */
                            .btn-outline-custom {
                                background: white;
                                border: 2px solid #e2e8f0;
                                color: var(--text-muted);
                                font-weight: 700;
                                padding: 12px 24px;
                                border-radius: 12px;
                                font-size: 13px;
                                transition: 0.3s;
                                width: 100%;
                                display: block;
                                text-decoration: none;
                            }

                            .btn-outline-custom:hover {
                                background: #f1f5f9;
                                border-color: #cbd5e1;
                                color: var(--text-dark);
                            }

                            @keyframes bgGradientMove {
                                0% { background-position: 0% 50%; }
                                100% { background-position: 200% 50%; }
                            }

                            .step-content { display: none; }
                            .step-content.active { display: block; }

                            .otp-sent-status {
                                font-size: 12px;
                                color: #10b981;
                                margin-top: -15px;
                                margin-bottom: 15px;
                                font-weight: 600;
                                display: none;
                            }

                            /* Responsive */
                            @media (max-width: 768px) {
                                .stepper-wrapper { flex-direction: column; gap: 15px; }
                                .kyc-card { padding: 30px 20px; }
                            }
                        </style>

                        <div class="stepper-container">
                            <h2 class="stepper-title">Finish KYC <span class="gradient-text">in seconds</span></h2>

                            <div class="stepper-wrapper">
                                <div class="step-item active" id="step1-indicator">
                                    <div class="step-bar"><div class="step-bar-fill"></div></div>
                                    <div class="step-label">1. KYC Verification</div>
                                </div>
                                <div class="step-item" id="step2-indicator">
                                    <div class="step-bar"><div class="step-bar-fill"></div></div>
                                    <div class="step-label">2. Basic Info & Signing</div>
                                </div>
                                <div class="step-item" id="step3-indicator">
                                    <div class="step-bar"><div class="step-bar-fill"></div></div>
                                    <div class="step-label">3. Terms of Agreement</div>
                                </div>
                                <div class="step-item" id="step4-indicator">
                                    <div class="step-bar"><div class="step-bar-fill"></div></div>
                                    <div class="step-label">4. CSB V Ramp UP</div>
                                </div>
                            </div>

                            <div class="kyc-card">
                                
                                <!-- Step 1 Content -->
                                <div id="step1-content" class="step-content active">
                                    <h3 class="kyc-card-title">Complete <span class="gradient-text">KYC</span></h3>
                                    <p class="text-muted mb-4">Please enter your GST details to verify your business identity.</p>
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-8">
                                            <label class="form-label-custom">GST Number</label>
                                            <div class="input-group-custom">
                                                <input type="text" class="form-control input-custom" placeholder="07AANCA2340K1ZN" id="gstInput">
                                                <i class="fas fa-file-invoice"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-custom d-none d-md-block">&nbsp;</label>
                                            <button class="btn btn-primary-custom" style="padding: 14px;" onclick="sendOTP()">Send OTP</button>
                                        </div>
                                    </div>

                                    <div id="otpStatus" class="otp-sent-status">
                                        <i class="fas fa-check-circle"></i> OTP has been sent to your registered mobile/email
                                    </div>

                                    <!-- OTP Field (Visible only after sendOTP) -->
                                    <div id="otpContainer" style="display: none;">
                                        <label class="form-label-custom">Enter 6-Digit OTP</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom" placeholder="******" maxlength="6">
                                            <i class="fas fa-key"></i>
                                        </div>
                                        <button class="btn btn-primary-custom" onclick="nextStep(2)">Verify & Continue</button>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button class="btn btn-outline-custom" onclick="nextStep(2)">Skip For Now</button>
                                    </div>
                                </div>

                                <!-- Step 2 Content -->
                                <div id="step2-content" class="step-content">
                                    <h3 class="kyc-card-title">Business <span class="gradient-text">Details</span></h3>
                                    <p class="text-muted small mb-4">Provide details for the digital agreement.</p>
                                    
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Organization Name</label>
                                            <div class="input-group-custom">
                                                <input type="text" class="input-custom" placeholder="Company Ltd">
                                                <i class="fas fa-building"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Authorized Signatory</label>
                                            <div class="input-group-custom">
                                                <input type="text" class="input-custom" placeholder="Full Name">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between pt-3 gap-3">
                                        <button class="btn btn-outline-custom flex-md-shrink-1" style="width: auto; padding-left: 40px; padding-right: 40px;" onclick="nextStep(1)">Back</button>
                                        <button class="btn btn-primary-custom" style="width: auto; padding-left: 60px; padding-right: 60px;" onclick="nextStep(3)">Continue</button>
                                    </div>
                                </div>

                                <!-- Step 3 Content -->
                                <div id="step3-content" class="step-content">
                                    <h3 class="kyc-card-title">Agreement <span class="gradient-text">Signing</span></h3>
                                    <div class="p-4 bg-light rounded-4 mb-4" style="height: 200px; overflow-y: auto; font-size: 13px; border: 1px solid #eef2f7; color: var(--text-muted); line-height: 1.6;">
                                        <h6 class="text-dark fw-bold">Terms of Service</h6>
                                        <p><strong>1. Services:</strong> The company agrees to provide logistics and courier services as defined in the master service agreement...</p>
                                        <p><strong>2. Liability:</strong> We are not responsible for delays caused by nature or government restrictions...</p>
                                        <p><strong>3. Data Privacy:</strong> Your business data is handled with AES-256 encryption standards...</p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                    </div>
                                    
                                    <div class="form-check mb-4 p-0" style="display: flex; align-items: center; gap: 10px;">
                                        <input class="form-check-input" type="checkbox" id="agreeTerms" style="margin: 0; width: 18px; height: 18px; cursor: pointer;">
                                        <label class="form-check-label fw-bold small text-muted" for="agreeTerms">
                                            I accept the terms and legal agreement.
                                        </label>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                                        <button class="btn btn-outline-custom" style="width: auto; padding-left: 40px; padding-right: 40px;" onclick="nextStep(2)">Back</button>
                                        <button class="btn btn-primary-custom" style="width: auto; padding-left: 60px; padding-right: 60px;" onclick="nextStep(4)">Sign & Submit</button>
                                    </div>
                                </div>

                                <!-- Step 4 Content -->
                                <div id="step4-content" class="step-content text-center py-4">
                                    <div class="mb-4">
                                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                                    </div>
                                    <h3 class="kyc-card-title mb-2">Activation <span class="gradient-text">Pending</span></h3>
                                    <p class="text-muted mx-auto" style="max-width: 500px;">We have received your KYC documents. Our verification team will review your application within 24 hours.</p>
                                    <div class="mx-auto" style="max-width: 300px;">
                                        <button class="btn btn-primary-custom mt-4" onclick="location.reload()">Go to Dashboard</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <script>
                            let kycData = {
                                gst_number: '',
                                gst_verified: false,
                                otp_verified: false,
                                organization_name: '',
                                authorized_signatory: '',
                                terms_accepted: false
                            };

                            function sendOTP() {
                                const btn = event.currentTarget;
                                const originalText = btn.innerHTML;
                                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                                btn.disabled = true;
                                
                                // Get GST number
                                kycData.gst_number = document.getElementById('gstInput').value;
                                kycData.gst_verified = true; // Simulate GST verification
                                
                                setTimeout(() => {
                                    document.getElementById('otpStatus').style.display = 'block';
                                    document.getElementById('otpContainer').style.display = 'block';
                                    btn.innerHTML = 'Resend';
                                    btn.disabled = false;
                                }, 1000);
                            }

                            function nextStep(stepNumber) {
                                // Save data from current step before moving
                                if (stepNumber === 2) {
                                    // Save OTP verification
                                    const otpInput = document.querySelector('#otpContainer input');
                                    if (otpInput && otpInput.value.length === 6) {
                                        kycData.otp_verified = true;
                                    }
                                } else if (stepNumber === 3) {
                                    // Save business details
                                    const orgName = document.querySelector('#step2-content input[placeholder="Company Ltd"]');
                                    const signatory = document.querySelector('#step2-content input[placeholder="Full Name"]');
                                    if (orgName) kycData.organization_name = orgName.value;
                                    if (signatory) kycData.authorized_signatory = signatory.value;
                                } else if (stepNumber === 4) {
                                    // Save terms acceptance
                                    const agreeTerms = document.getElementById('agreeTerms');
                                    if (agreeTerms) {
                                        kycData.terms_accepted = agreeTerms.checked;
                                    }
                                    // Submit KYC data
                                    submitKYC();
                                }
                                
                                document.querySelectorAll('.step-content').forEach(content => content.classList.remove('active'));
                                const target = document.getElementById('step' + stepNumber + '-content');
                                target.classList.add('active');
                                
                                document.querySelectorAll('.step-item').forEach((item, index) => {
                                    const currentIdx = index + 1;
                                    item.classList.remove('active', 'completed');
                                    if (currentIdx < stepNumber) item.classList.add('completed');
                                    else if (currentIdx === stepNumber) item.classList.add('active');
                                });

                                // Scroll to top of card on step change
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }

                            function submitKYC() {
                                // Show loading state
                                const submitBtn = document.querySelector('#step4-content button');
                                if (submitBtn) {
                                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                                    submitBtn.disabled = true;
                                }

                                // Submit via AJAX
                                fetch('{{ route("customer.kyc.submit") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify(kycData)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Show success message
                                        const messageDiv = document.querySelector('#step4-content p.text-muted');
                                        if (messageDiv) {
                                            messageDiv.innerHTML = '<strong>' + data.message + '</strong>';
                                            messageDiv.className = 'text-success mx-auto';
                                        }
                                        
                                        // Update button
                                        if (submitBtn) {
                                            submitBtn.innerHTML = 'Go to Dashboard';
                                            submitBtn.onclick = () => location.reload();
                                        }
                                    } else {
                                        // Show error
                                        alert('Error: ' + data.message);
                                        // Reset button
                                        if (submitBtn) {
                                            submitBtn.innerHTML = 'Go to Dashboard';
                                            submitBtn.disabled = false;
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred while submitting your KYC application.');
                                    // Reset button
                                    if (submitBtn) {
                                        submitBtn.innerHTML = 'Go to Dashboard';
                                        submitBtn.disabled = false;
                                    }
                                });
                            }
                        </script>
                        <!-- KYC Form End -->
                    @endif
                @endif

                @if(auth()->guard('customer')->check() && $kycRecord && $kycRecord->kyc_status == 'approved')
                <h6 class="mb-2">Get Starting</h6>
                <div class="row row-gap-3 mb-4">
                    <!-- Total Companies -->
                    <div class="col-xl-4 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Complete Onboarding</p>
                                            <p class="text-success mb-0 fs-13"><span class="text-body">Done</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle border bg-soft-success border-success">
                                            <i class="fa-solid fa-check fs-16 text-success"></i>

                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                    <!-- Total Companies -->
                    <div class="col-xl-4 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Connect Store</p>
                                            <p class="text-danger mb-0 fs-13"><span class="text-body">Link Your
                                                    Marketplace</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-warning border border-warning">
                                            <i class="fa-solid fa-store fs-16 text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                    <!-- Total Companies -->
                    <div class="col-xl-4 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Start Shipping</p>
                                            <p class="text-success mb-0 fs-13"><span class="text-body ms-1">Book your
                                                    first shipment</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-warning border border-warning">
                                            <i class="fa-solid fa-rocket fs-16 text-warning"></i>

                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->


                </div>

                @else
                <!-- Show message when KYC is not approved -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-3x text-muted"></i>
                    </div>
                    <h4 class="text-muted">Dashboard Access Restricted</h4>
                    <p class="text-muted">Complete your KYC verification to access all dashboard features.</p>
                </div>
                @endif

                @if(auth()->guard('customer')->check() && $kycRecord && $kycRecord->kyc_status == 'approved')
                <!-- start row -->
                <h6 class="mb-2">Dashboard</h6>

                <div class="row row-gap-3 mb-4">
                    <!-- Total Companies -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Shipments Booked</p>
                                            <h2 class="mb-1 fs-16">7</h2>
                                            <p class="text-success mb-0 fs-13"> <i
                                                    class="ti ti-arrow-bar-up me-1"></i>5.62%<span
                                                    class="text-body ms-1">from last month</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-primary border border-primary">
                                            <i class="fa-solid fa-truck-ramp-box fs-16 text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                    <!-- Total Companies -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Pickup Pending</p>
                                            <h2 class="mb-1 fs-16">4</h2>
                                            <p class="text-success mb-0 fs-13"> <i
                                                    class="ti ti-arrow-bar-up me-1"></i>12%<span
                                                    class="text-body ms-1">from last month</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-success border border-success">
                                            <i class="ti ti-carousel-vertical fs-16 text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                    <!-- Total Companies -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Out of Delivery</p>
                                            <h2 class="mb-1 fs-16">3</h2>
                                            <p class="text-success mb-0 fs-13"> <i
                                                    class="ti ti-arrow-bar-up me-1"></i>6%<span
                                                    class="text-body ms-1">from last month</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-warning border border-warning">
                                            <i class="fa-regular fa-truck fs-16 text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                    <!-- Total Companies -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Delivered</p>
                                            <h2 class="mb-1 fs-16">15</h2>
                                            <p class="text-success mb-0 fs-13"> <i
                                                    class="ti ti-arrow-bar-up me-1"></i>16%<span
                                                    class="text-body ms-1">from last month</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-danger border border-danger mb-3">
                                            <i class="fa-solid fa-people-carry-box fs-16 text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                </div>
                <!-- end row -->



                <!-- start row -->
                <h6 class="mb-2">Upgrade Your Service</h6>

                <div class="row row-gap-3 mb-4">

                    <!-- Total Companies -->
                    <div class="col-xl-6 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Enable CSB V</p>
                                            <p class="text-danger mb-0 fs-13"><span class="text-body">Unlock commercial
                                                    exports with CSB V compliance</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center">
                                        @if($customer->csb_status === 2)
                                            <span class="text-success"><i class="fa-solid fa-check-circle fs-20"></i></span>
                                        @else
                                            <a href="/customer/csb5-form" class="text-success" style="font-weight: 500;">Enable Now <i
                                                    class="fa-solid fa-chevron-right"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                </div>
                <!-- end row -->



                <!-- start row -->
                <div class="row mb-4">

                    <div class="col-sm-12">
                        <div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                <h5 class="d-flex align-items-center mb-0">Recent Orders<span
                                        class="badge bg-soft-dark ms-2 text-dark fs-12">20 Orders</span></h5>
                                <a href="add-invoices.html" class="btn btn-md btn-primary d-flex align-items-center"><i
                                        class="ti ti-circle-plus me-2"></i>Export PDF</a>
                            </div>
                            <div class="card-body p-0">

                                <div class="table-responsive">
                                    <table class="table table-nowrap border">
                                        <thead class="table-light">
                                            <tr>
                                                <th>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox" id="select-all">
                                                    </div>
                                                </th>
                                                <th>AWBNO</th>
                                                <th>Date</th>
                                                <th>Destination</th>
                                                <th>Service</th>
                                                <th>Network</th>
                                                <th>Network No.</th>
                                                <th>PCS</th>
                                                <th>CHG Weight</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md"><input
                                                            class="form-check-input" type="checkbox"></div>
                                                </td>

                                                <td>
                                                    <a href="invoice-details.html">INV-1454</a>
                                                </td>

                                                <td>14 Jan 2024 </td>
                                                <td>USA</td>
                                                <td>PM</td>
                                                <td>PM Self</td>
                                                <td>238472348923</td>
                                                <td>1</td>
                                                <td>0.050</td>
                                                <td>
                                                    <div class="d-inline-flex align-items-center">
                                                        <a href="invoice-details.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-eye"></i></a>
                                                        <a href="edit-invoices.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#delete_modal"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"><i
                                                                class="ti ti-trash"></i></a>
                                                        <span class="badge badge-soft-success">
                                                            Paid
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md"><input
                                                            class="form-check-input" type="checkbox"></div>
                                                </td>

                                                <td>
                                                    <a href="invoice-details.html">INV-1454</a>
                                                </td>

                                                <td>14 Jan 2024 </td>
                                                <td>USA</td>
                                                <td>PM</td>
                                                <td>PM Self</td>
                                                <td>238472348923</td>
                                                <td>1</td>
                                                <td>0.050</td>
                                                <td>
                                                    <div class="d-inline-flex align-items-center">
                                                        <a href="invoice-details.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-eye"></i></a>
                                                        <a href="edit-invoices.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#delete_modal"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"><i
                                                                class="ti ti-trash"></i></a>
                                                        <span class="badge badge-soft-warning">
                                                            Draft
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md"><input
                                                            class="form-check-input" type="checkbox"></div>
                                                </td>

                                                <td>
                                                    <a href="invoice-details.html">INV-1454</a>
                                                </td>

                                                <td>14 Jan 2024 </td>
                                                <td>USA</td>
                                                <td>PM</td>
                                                <td>PM Self</td>
                                                <td>238472348923</td>
                                                <td>1</td>
                                                <td>0.050</td>
                                                <td>
                                                    <div class="d-inline-flex align-items-center">
                                                        <a href="invoice-details.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-eye"></i></a>
                                                        <a href="edit-invoices.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#delete_modal"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"><i
                                                                class="ti ti-trash"></i></a>
                                                        <span class="badge badge-soft-success">
                                                            Paid
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md"><input
                                                            class="form-check-input" type="checkbox"></div>
                                                </td>

                                                <td>
                                                    <a href="invoice-details.html">INV-1454</a>
                                                </td>

                                                <td>14 Jan 2024 </td>
                                                <td>USA</td>
                                                <td>PM</td>
                                                <td>PM Self</td>
                                                <td>238472348923</td>
                                                <td>1</td>
                                                <td>0.050</td>
                                                <td>
                                                    <div class="d-inline-flex align-items-center">
                                                        <a href="invoice-details.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-eye"></i></a>
                                                        <a href="edit-invoices.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#delete_modal"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"><i
                                                                class="ti ti-trash"></i></a>
                                                        <span class="badge badge-soft-danger">
                                                            Overdue
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md"><input
                                                            class="form-check-input" type="checkbox"></div>
                                                </td>

                                                <td>
                                                    <a href="invoice-details.html">INV-1454</a>
                                                </td>

                                                <td>14 Jan 2024 </td>
                                                <td>USA</td>
                                                <td>PM</td>
                                                <td>PM Self</td>
                                                <td>238472348923</td>
                                                <td>1</td>
                                                <td>0.050</td>
                                                <td>
                                                    <div class="d-inline-flex align-items-center">
                                                        <a href="invoice-details.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-eye"></i></a>
                                                        <a href="edit-invoices.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#delete_modal"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"><i
                                                                class="ti ti-trash"></i></a>
                                                        <span class="badge badge-soft-primary">
                                                            Pending
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md"><input
                                                            class="form-check-input" type="checkbox"></div>
                                                </td>

                                                <td>
                                                    <a href="invoice-details.html">INV-1454</a>
                                                </td>

                                                <td>14 Jan 2024 </td>
                                                <td>USA</td>
                                                <td>PM</td>
                                                <td>PM Self</td>
                                                <td>238472348923</td>
                                                <td>1</td>
                                                <td>0.050</td>
                                                <td>
                                                    <div class="d-inline-flex align-items-center">
                                                        <a href="invoice-details.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-eye"></i></a>
                                                        <a href="edit-invoices.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#delete_modal"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"><i
                                                                class="ti ti-trash"></i></a>
                                                        <span class="badge badge-soft-success">
                                                            Paid
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md"><input
                                                            class="form-check-input" type="checkbox"></div>
                                                </td>

                                                <td>
                                                    <a href="invoice-details.html">INV-1454</a>
                                                </td>

                                                <td>14 Jan 2024 </td>
                                                <td>USA</td>
                                                <td>PM</td>
                                                <td>PM Self</td>
                                                <td>238472348923</td>
                                                <td>1</td>
                                                <td>0.050</td>
                                                <td>
                                                    <div class="d-inline-flex align-items-center">
                                                        <a href="invoice-details.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-eye"></i></a>
                                                        <a href="edit-invoices.html"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#delete_modal"
                                                            class="btn btn-icon btn-sm btn-outline-white border-0"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"><i
                                                                class="ti ti-trash"></i></a>
                                                        <span class="badge badge-soft-success">
                                                            Paid
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->

                </div>
                <!-- end row -->



                <!-- start row -->
                <h6 class="mb-2">Financials</h6>

                <div class="row row-gap-3 mb-4">
                    <!-- Total Companies -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Wallet Balance</p>
                                            <h2 class="mb-1 fs-16">₹ 1,00,00</h2>
                                            <br>
                                            <a href="#"
                                                style="border: 1px solid #000; padding: 3px 6px; border-radius: 3px;">Recharge
                                                Now</a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-success border border-success">
                                            <i class="fa-solid fa-wallet fs-16 text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                    <!-- Total Companies -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">United Courier Coins</p>
                                            <h2 class="mb-1 fs-16">₹ 0.00</h2>
                                            <br>
                                            <a href="#"
                                                style="border: 1px solid #000; padding: 3px 6px; border-radius: 3px;">Earn
                                                Coins</a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-warning border border-warning">
                                            <i class="fa-solid fa-coins fs-16 text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                    <!-- Total Companies -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1">Value of product shipped</p>
                                            <h2 class="mb-1 fs-16">₹ 0.00</h2>
                                            <br>
                                            <a href="#"
                                                style="border: 1px solid #000; padding: 3px 6px; border-radius: 3px;">Download
                                                Report</a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-warning border border-warning">
                                            <i class="fa-solid fa-download fs-16 text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                    <!-- Total Companies -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1">Shipped Cost</p>
                                            <h2 class="mb-1 fs-16">₹ 0.00</h2>
                                            <br>
                                            <a href="#"
                                                style="border: 1px solid #000; padding: 3px 6px; border-radius: 3px;">Download
                                                Report</a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="avatar avatar-md rounded-circle bg-soft-warning border border-warning">
                                            <i class="fa-solid fa-coins fs-16 text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Total Companies -->

                </div>
                <!-- end row -->




                <!-- end row -->
            </div>
            <!-- End Content -->

            <!-- Start Footer -->
            <footer class="footer d-block d-md-flex justify-content-between text-md-start text-center">
                <p class="mb-md-0 mb-1">Copyright &copy; 2026 <a href="javascript:void(0);" class="">United Courier
                        worldwide</a></p>
                <div class="d-flex align-items-center gap-2 footer-links justify-content-center justify-content-md-end">
                    <a href="javascript:void(0);">About</a>
                    <a href="javascript:void(0);">Terms</a>
                    <a href="javascript:void(0);">Contact Us</a>
                </div>
            </footer>
            <!-- End Footer -->
                @endif

        </div>

        <!-- ========================
			End Page Content
		========================= -->

    </div>
    <!-- End Wrapper -->

<!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Daterangepikcer JS -->
    <script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Choices Js -->
    <script src="{{ asset('assets/plugins/choices.js/public/assets/scripts/choices.min.js') }}" type="text/javascript">
    </script>

    <!-- Mobile Input -->
    <script src="{{ asset('assets/plugins/intltelinput/js/intlTelInput.js') }}" type="text/javascript"></script>

    <!-- Quill JS -->
    <script src="{{ asset('assets/plugins/quill/quill.min.js') }}" type="text/javascript"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

    <script src="../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js"
        data-cf-settings="5d3b6c488f778ded9171c76c-|49" defer></script>
    <script defer
        src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015"
        integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ=="
        data-cf-beacon='{"rayId":"967b314f0fc122a8","version":"2025.7.0","serverTiming":{"name":{"cfExtPri":true,"cfEdge":true,"cfOrigin":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"token":"3ca157e612a14eccbb30cf6db6691c29","b":1}'
        crossorigin="anonymous"></script>
        
</body>

<!-- Mirrored from crms.dreamstechnologies.com/html/template/dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 31 Jul 2025 06:57:26 GMT -->

</html>