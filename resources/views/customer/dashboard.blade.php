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
    <!-- <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}"> -->
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/plugins/tabler-icons/tabler-icons.min.css">


    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <style>
    .chart-filter-btn {
        padding: 6px 16px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #495057;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .chart-filter-btn:hover {
        background: #f0f0f0;
    }

    .chart-filter-btn.active {
        background: #5b5eff;
        color: #fff;
        border-color: #5b5eff;
    }

    .chart-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .chart-card h6 {
        margin-bottom: 16px;
        font-weight: 600;
    }

    body.kyc-header-hidden .header-wallet-section,
    body.kyc-header-hidden .header-credit-balance-section,
    body.kyc-header-hidden .header-notification-section,
    body.kyc-header-hidden .header-line {
        display: none !important;
    }

    /* terms and condition */
    .document-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.08), 0 8px 20px rgba(0, 0, 0, 0.02);
        padding: 2.8rem 2.5rem;
        transition: all 0.2s ease;
    }

    @media (max-width: 640px) {
        body {
            padding: 1rem 0.75rem;
        }

        .document-wrapper {
            padding: 1.8rem 1.25rem;
            border-radius: 20px;
        }
    }

    /* typography */
    h1,
    h2,
    h3,
    h4 {
        font-weight: 600;
        letter-spacing: -0.02em;
        margin-top: 1.8em;
        margin-bottom: 0.5em;
    }

    h1 {
        font-size: 2.1rem;
        margin-top: 0;
        margin-bottom: 0.3rem;
        letter-spacing: -0.03em;
        border-bottom: 2px solid #eef2f6;
        padding-bottom: 0.4rem;
    }

    .subhead-company {
        font-size: 1.3rem;
        font-weight: 500;
        color: #1f3a6b;
        margin-top: -0.1rem;
        margin-bottom: 1.2rem;
        display: block;
    }

    h2 {
        font-size: 1.5rem;
        border-left: 5px solid #1f3a6b;
        padding-left: 0.9rem;
        background: #f1f5f9;
        padding: 0.6rem 1rem 0.6rem 1.2rem;
        border-radius: 0 40px 40px 0;
        margin-top: 2.2rem;
    }

    h3 {
        font-size: 1.2rem;
        margin-top: 1.8rem;
        color: #1a2e4a;
        font-weight: 600;
        border-bottom: 1px dashed #dce3ec;
        padding-bottom: 0.3rem;
    }

    h4 {
        font-size: 1.05rem;
        font-weight: 600;
        margin-top: 1.5rem;
        color: #1f3a6b;
    }

    p {
        margin: 0.9rem 0;
    }

    .legal-block {
        background: #fafcff;
        border-radius: 16px;
        padding: 0.1rem 1.5rem 0.8rem 1.5rem;
        margin: 1.2rem 0;
        border: 1px solid #e6edf5;
    }

    .legal-block p:last-child {
        margin-bottom: 0.4rem;
    }

    ul,
    ol {
        padding-left: 1.7rem;
        margin: 0.8rem 0 1.2rem 0;
    }

    li {
        margin: 0.4rem 0;
    }

    .inline-code {
        background: #eef2f6;
        padding: 0.1rem 0.5rem;
        border-radius: 12px;
        font-family: 'Menlo', 'Cascadia Code', monospace;
        font-size: 0.9rem;
        color: #1f3a6b;
        white-space: nowrap;
    }

    .glossary-item {
        display: flex;
        flex-wrap: wrap;
        margin: 0.6rem 0;
        border-bottom: 1px solid #edf2f8;
        padding-bottom: 0.6rem;
    }

    .glossary-term {
        font-weight: 600;
        min-width: 160px;
        color: #12233f;
    }

    .glossary-def {
        flex: 1;
        color: #1d2f4a;
    }

    hr {
        border: 0;
        border-top: 2px solid #e2eaf2;
        margin: 2.2rem 0;
    }

    .signature-line {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        margin: 1.8rem 0 0.8rem 0;
        font-weight: 500;
        color: #1f3a6b;
    }

    .contact-box {
        background: #eef4fa;
        border-radius: 60px;
        padding: 0.7rem 1.6rem;
        display: inline-block;
        font-size: 0.95rem;
        margin: 0.8rem 0;
    }

    .contact-box a {
        color: #0b2b5c;
        font-weight: 500;
        text-decoration: none;
        border-bottom: 1px dotted #3a5f89;
    }

    .contact-box a:hover {
        border-bottom: 2px solid #0b2b5c;
    }

    .small-meta {
        font-size: 0.9rem;
        color: #3d5779;
        background: #f2f6fc;
        padding: 0.2rem 0.8rem;
        border-radius: 30px;
        display: inline-block;
    }

    .underline-title {
        text-decoration: underline;
        text-underline-offset: 4px;
        text-decoration-thickness: 2px;
        text-decoration-color: #b3c9e0;
    }

    .consolidated-note {
        background: #f1f7fe;
        border-left: 6px solid #1f3a6b;
        padding: 0.8rem 1.6rem;
        border-radius: 16px;
        margin: 2rem 0 0.5rem 0;
    }

    /* Responsive tweaks */
    @media (max-width: 600px) {
        .glossary-item {
            flex-direction: column;
        }

        .glossary-term {
            min-width: auto;
        }
    }
    </style>
</head>

<body class="customer-dashboard">
    <script>
        function toggleKycHeaderState() {
            const stepper = document.querySelector('.stepper-container');
            document.body.classList.toggle('kyc-header-hidden', !!stepper);
        }

        document.addEventListener('DOMContentLoaded', toggleKycHeaderState);
        window.addEventListener('load', toggleKycHeaderState);
    </script>

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

        @php
        $customerId = auth()->guard('customer')->user()->id;
        $kycExists = \App\Models\KycDetail::where('customer_id', $customerId)->exists();
        $kycRecord = \App\Models\KycDetail::where('customer_id', $customerId)->first();
        @endphp

        <!-- Sidenav Menu Start (hidden until KYC is approved) -->
        @if($kycExists && $kycRecord && $kycRecord->kyc_status == 'approved')
        @include('customer.partials.sidebar')
        @else
        {{-- Sidebar is hidden while KYC is not yet approved (stepper / progress): hide toggle buttons + make content full width --}}
        <style>
            #mobile_btn, #toggle_btn2 { display: none !important; }
            .page-wrapper { margin-left: 0 !important; }
            .navbar-header { margin-left: 0 !important; }
        </style>
        @endif
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
                        <p class="mb-0 text-warning"><i class="fas fa-clock"></i> Your KYC application is pending.
                            Please complete the verification process.</p>
                        @elseif($kycRecord->kyc_status == 'under_review')
                        <p class="mb-0 text-info"><i class="fas fa-hourglass-half"></i> Your KYC application is under
                            review. We'll notify you once it's approved.</p>
                        @elseif($kycRecord->kyc_status == 'rejected')
                        <p class="mb-0 text-danger"><i class="fas fa-exclamation-triangle"></i> Your KYC application was
                            rejected. Please contact support for assistance.</p>
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

                h1,
                h2,
                h3,
                h4,
                h5,
                .step-title {
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

                .step-item.active .step-bar-fill {
                    width: 100%;
                }

                .step-item.completed .step-bar-fill {
                    width: 100%;
                }

                .step-label {
                    font-size: 12px;
                    font-weight: 700;
                    color: var(--text-muted);
                    line-height: 1.4;
                    transition: color 0.3s;
                    text-transform: uppercase;
                    letter-spacing: 0.3px;
                }

                .step-item.active .step-label {
                    color: var(--brand-blue-main);
                }

                /* Form Card Styling */
                .kyc-card {
                    background: white;
                    border-radius: 30px;
                    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08);
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

                .input-custom:focus+i {
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
                    0% {
                        background-position: 0% 50%;
                    }

                    100% {
                        background-position: 200% 50%;
                    }
                }

                .step-content {
                    display: none;
                }

                .step-content.active {
                    display: block;
                }

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
                    .stepper-wrapper {
                        flex-direction: column;
                        gap: 15px;
                    }

                    .kyc-card {
                        padding: 30px 20px;
                    }
                }
                </style>

                <!-- KYC Welcome Popup (blocking) -->
                <div class="modal fade" id="kycWelcomeModal" tabindex="-1" aria-labelledby="kycWelcomeModalLabel"
                    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
                            <div class="modal-body text-center p-3"
                                style="background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="far fa-file-alt me-3"
                                        style="font-size:60px;color: linear-gradient(to right, var(--brand-blue-main), var(--brand-purple))"></i>

                                    <div>
                                        <h3 class="fw-bold mb-2" style="color:#222;">
                                            Merchant Agreement v1.8
                                        </h3>

                                        <span class="badge px-3 py-2"
                                            style="background:#FFF3E6;color:#F39C12;font-size:14px;">
                                            Effective From 2 Jun, 2026
                                        </span>
                                    </div>
                                </div>

                                <hr class="my-2">

                                <p class="text-muted mb-2" style="font-size:18px;">
                                    Key updates include:
                                </p>

                                <h4 class="fw-bold mb-2" style="text-align:start;">
                                    Revisions to Existing Policies
                                </h4>

                                <p class="text-muted" style="line-height:1.8; text-align:start;">
                                    By clicking the <strong>Agree</strong> button, you accept the updated
                                    Merchant Agreement terms effective from <strong>02/06/2026</strong>.
                                    After this date, continued use of the platform will require agreement
                                    to these terms. You can also
                                    <a href="#" class="fw-semibold" data-bs-toggle="modal"
                                        data-bs-target="#termsModal">
                                        view the full document.
                                    </a>
                                </p>

                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-primary px-5 py-2" data-bs-dismiss="modal"
                                        style="min-width:250px;">
                                        Accept
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions Popup Modal -->
                <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
                            <div class="modal-header" style="background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%); border-bottom: 1px solid #e2e8f0;">
                                <h5 class="modal-title fw-bold" id="termsModalLabel" style="color:#222;">
                                    <i class="far fa-file-alt me-2"></i>Terms & Conditions
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                                <div id="termsModalContent">
                                    <!-- T&C document content is cloned here via JS from #billTermsDocument -->
                                </div>
                            </div>
                            <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                                    <i class="fas fa-check me-1"></i>I have read the document
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stepper-container">
                    <h2 class="stepper-title">Finish KYC <span class="gradient-text">in seconds</span></h2>

                    <div class="stepper-wrapper">
                        @if($userType === 'Business')
                        <!-- Business KYC (CSB-V): 7 Steps -->
                        <div class="step-item active" id="step1-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">1. Verify GST</div>
                        </div>
                        <div class="step-item" id="step2-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">2. Verify Aadhar</div>
                        </div>
                        <div class="step-item" id="step3-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">3. Verify PAN</div>
                        </div>
                        <div class="step-item" id="step4-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">4. CSB-V</div>
                        </div>
                        <div class="step-item" id="step5-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">5. Upload Signature</div>
                        </div>
                        <div class="step-item" id="step6-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">6. Terms & Conditions</div>
                        </div>
                        <div class="step-item" id="step7-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">7. Activation Pending</div>
                        </div>
                        @else
                        <!-- Personal KYC (CSB-IV): 7 Steps -->
                        <div class="step-item active" id="step1-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">1. KYC Verification</div>
                        </div>
                        <div class="step-item" id="step2-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">2. Verify Aadhar</div>
                        </div>
                        <div class="step-item" id="step3-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">3. Verify PAN</div>
                        </div>
                        <div class="step-item" id="step4-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">4. Basic Info & Signing</div>
                        </div>
                        <div class="step-item" id="step5-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">5. Upload Signature</div>
                        </div>
                        <div class="step-item" id="step6-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">6. Bill</div>
                        </div>
                        <div class="step-item" id="step7-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">7. CSB V Ramp UP</div>
                        </div>
                        @endif
                    </div>

                    <div class="kyc-card">

                        @if($userType === 'Business')
                            <!-- Step 1 Content: Verify GST Certificate (Business) -->
                            <div id="step1-content" class="step-content active">
                                <h3 class="kyc-card-title">Verify <span class="gradient-text">GST</span></h3>
                                <p class="text-muted mb-4">Provide your GST Certificate number and upload the certificate
                                    document to verify your business identity.</p>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label-custom">GST Certificate Number</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                placeholder="22AAAAA0000A1Z5" id="bizGstCertNumber" maxlength="15"
                                                style="text-transform: uppercase;">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-custom d-none d-md-block">&nbsp;</label>
                                        <button class="btn btn-primary-custom w-100" style="padding: 14px;"
                                            id="bizVerifyGstCertBtn" onclick="verifyGstBiz()">Verify GST</button>
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label-custom">Upload GST Certificate</label>
                                        <div id="bizGstCertUploadArea"
                                            style="border: 2px dashed #c7d2fe; border-radius: 16px; padding: 20px; text-align: center; background: #f8faff; cursor: pointer; transition: all 0.2s ease;"
                                            onclick="document.getElementById('bizGstCertFileInput').click()">
                                            <input type="file" id="bizGstCertFileInput"
                                                accept=".pdf,application/pdf" style="display: none;">
                                            <div id="bizGstCertUploadPlaceholder">
                                                <i class="fas fa-file-invoice"
                                                    style="font-size: 36px; color: #6366f1; margin-bottom: 8px; display: block;"></i>
                                                <p class="mb-1 fw-semibold" style="color: #4338ca; font-size: 14px;">Click to
                                                    upload GST Certificate PDF</p>
                                                <p class="text-muted small mb-0">PDF only (max 5MB)</p>
                                            </div>
                                            <div id="bizGstCertPreview" style="display: none;">
                                                <i class="fas fa-check-circle"
                                                    style="font-size: 28px; color: #10b981; display: block; margin-bottom: 6px;"></i>
                                                <p class="mb-0 fw-semibold small" id="bizGstCertFileName"
                                                    style="color: #166534;"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="bizGstStatus" class="otp-sent-status" style="display: none;">
                                    <i class="fas fa-check-circle"></i> GST Certificate verified successfully!
                                </div>

                                <div class="d-flex flex-column flex-md-row justify-content-between pt-3 gap-3">
                                    <button class="btn btn-outline-custom flex-md-shrink-1"
                                        style="width: auto; padding-left: 40px; padding-right: 40px;"
                                        onclick="nextStep(2)">Continue</button>
                                </div>
                            </div>
                        @else
                            <!-- Step 1 Content: Complete KYC (Personal) -->
                            <div id="step1-content" class="step-content active">
                                <h3 class="kyc-card-title">Complete <span class="gradient-text">KYC</span></h3>
                                <p class="text-muted mb-4">Please enter your GST details to verify your business
                                    identity.</p>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label-custom">GST Number</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                placeholder="22AAAAA0000A1Z5" id="gstInput" maxlength="15"
                                                style="text-transform: uppercase;">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <small class="text-muted d-block mt-1">Format: 2-digit state code + 10-char PAN
                                            + entity code + Z + checksum (15 chars)</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-custom d-none d-md-block">&nbsp;</label>
                                        <button class="btn btn-primary-custom" style="padding: 14px;" id="verifyGstBtn"
                                            onclick="verifyGst()">Verify GST</button>
                                    </div>
                                </div>

                                <div id="gstStatus" class="otp-sent-status" style="display: none;"></div>

                                <div id="otpSection" style="display: none;">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label-custom">Send OTP</label>
                                            <p class="text-muted small mb-2">GST verified. Send an OTP to your
                                                registered mobile/email to continue.</p>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-primary-custom" style="padding: 14px;"
                                                id="sendOtpBtn" onclick="sendOTP()">Send OTP</button>
                                        </div>
                                    </div>

                                    <div id="otpStatus" class="otp-sent-status" style="display: none;">
                                        <i class="fas fa-check-circle"></i> OTP has been sent to your registered
                                        mobile/email
                                    </div>

                                    <!-- OTP Field (Visible only after sendOTP) -->
                                    <div id="otpContainer" style="display: none;">
                                        <label class="form-label-custom">Enter 6-Digit OTP</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom" placeholder="******"
                                                maxlength="6" id="otpInput">
                                            <i class="fas fa-key"></i>
                                        </div>
                                        <button class="btn btn-primary-custom mt-2" onclick="nextStep(2)">Verify &
                                            Continue</button>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button class="btn btn-outline-custom" onclick="nextStep(2)">Skip For Now</button>
                                </div>
                            </div>
                        @endif

                        <!-- Step 2 Content: Verify Aadhar -->
                        <div id="step2-content" class="step-content">
                            <h3 class="kyc-card-title">
                                Verify <span class="gradient-text">Aadhar</span>
                                @if($isAadhaarOptional)
                                    <small class="text-muted fs-6">(Optional)</small>
                                @endif
                            </h3>
                            <p class="text-muted mb-4">
                                @if($isAadhaarOptional)
                                    Aadhaar is optional for Courier / Aggregator customers. You may skip this step, or enter your 12-digit Aadhaar number and upload both images to provide it.
                                @else
                                    Enter your 12-digit Aadhaar number and upload front & back photos to verify your identity.
                                @endif
                            </p>

                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="form-label-custom">Aadhar Number</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom"
                                            placeholder="XXXX XXXX XXXX" id="aadharInput" maxlength="14"
                                            inputmode="numeric">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom d-none d-md-block">&nbsp;</label>
                                    <button class="btn btn-primary-custom" style="padding: 14px;" id="verifyAadharBtn"
                                        onclick="verifyAadhar()">Verify Aadhar</button>
                                </div>
                            </div>

                            <!-- Aadhaar Front & Back Upload -->
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Upload Aadhaar (Front)</label>
                                    <div id="aadharFrontUploadArea"
                                        style="border: 2px dashed #c7d2fe; border-radius: 16px; padding: 20px; text-align: center; background: #f8faff; cursor: pointer; transition: all 0.2s ease;"
                                        onclick="document.getElementById('aadharFrontFileInput').click()">
                                        <input type="file" id="aadharFrontFileInput" accept=".jpg,.jpeg,.png"
                                            style="display: none;">
                                        <div id="aadharFrontUploadPlaceholder">
                                            <i class="fas fa-id-card"
                                                style="font-size: 36px; color: #6366f1; margin-bottom: 8px; display: block;"></i>
                                            <p class="mb-1 fw-semibold" style="color: #4338ca; font-size: 14px;">Click to upload Aadhaar front</p>
                                            <p class="text-muted small mb-0">PNG or JPG (max 5MB)</p>
                                        </div>
                                        <div id="aadharFrontPreview" style="display: none;">
                                            <i class="fas fa-check-circle" style="font-size: 28px; color: #10b981; display: block; margin-bottom: 6px;"></i>
                                            <p class="mb-0 fw-semibold small" id="aadharFrontFileName" style="color: #166534;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Upload Aadhaar (Back)</label>
                                    <div id="aadharBackUploadArea"
                                        style="border: 2px dashed #c7d2fe; border-radius: 16px; padding: 20px; text-align: center; background: #f8faff; cursor: pointer; transition: all 0.2s ease;"
                                        onclick="document.getElementById('aadharBackFileInput').click()">
                                        <input type="file" id="aadharBackFileInput" accept=".jpg,.jpeg,.png"
                                            style="display: none;">
                                        <div id="aadharBackUploadPlaceholder">
                                            <i class="fas fa-id-card"
                                                style="font-size: 36px; color: #6366f1; margin-bottom: 8px; display: block;"></i>
                                            <p class="mb-1 fw-semibold" style="color: #4338ca; font-size: 14px;">Click to upload Aadhaar back</p>
                                            <p class="text-muted small mb-0">PNG or JPG (max 5MB)</p>
                                        </div>
                                        <div id="aadharBackPreview" style="display: none;">
                                            <i class="fas fa-check-circle" style="font-size: 28px; color: #10b981; display: block; margin-bottom: 6px;"></i>
                                            <p class="mb-0 fw-semibold small" id="aadharBackFileName" style="color: #166534;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="aadharStatus" class="otp-sent-status" style="display: none;">
                                <i class="fas fa-check-circle"></i> Aadhar verified successfully!
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between pt-3 gap-3">
                                <button class="btn btn-outline-custom flex-md-shrink-1"
                                    style="width: auto; padding-left: 40px; padding-right: 40px;"
                                    onclick="nextStep(1)">Back</button>
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;" id="aadharContinueBtn"
                                    onclick="nextStep(3)">{{ $isAadhaarOptional ? 'Skip / Continue' : 'Continue' }}</button>
                            </div>
                        </div>

                        <!-- Step 3 Content: Verify PAN -->
                        <div id="step3-content" class="step-content">
                            <h3 class="kyc-card-title">Verify <span class="gradient-text">PAN</span></h3>
                            <p class="text-muted mb-4">Enter your PAN details and upload your PAN card to complete verification.</p>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">PAN Number</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom"
                                            placeholder="ABCDE1234F" id="panInput" maxlength="10"
                                            style="text-transform: uppercase;">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">PAN Holder Name</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom"
                                            placeholder="Name as on PAN card" id="panHolderName">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Date of Birth</label>
                                    <div class="input-group-custom">
                                        <input type="date" class="form-control input-custom" id="panDob">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Upload PAN Card</label>
                                    <div id="panUploadArea"
                                        style="border: 2px dashed #c7d2fe; border-radius: 16px; padding: 16px; text-align: center; background: #f8faff; cursor: pointer; transition: all 0.2s ease;"
                                        onclick="document.getElementById('panFileInput').click()">
                                        <input type="file" id="panFileInput" accept=".jpg,.jpeg,.png"
                                            style="display: none;">
                                        <div id="panUploadPlaceholder">
                                            <i class="fas fa-file-invoice"
                                                style="font-size: 28px; color: #6366f1; margin-bottom: 6px; display: block;"></i>
                                            <p class="mb-1 fw-semibold" style="color: #4338ca; font-size: 13px;">Click to upload PAN card</p>
                                            <p class="text-muted small mb-0">PNG or JPG (max 5MB)</p>
                                        </div>
                                        <div id="panPreview" style="display: none;">
                                            <i class="fas fa-check-circle" style="font-size: 24px; color: #10b981; display: block; margin-bottom: 4px;"></i>
                                            <p class="mb-0 fw-semibold small" id="panFileName" style="color: #166534;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary-custom w-100" style="padding: 14px;" id="verifyPanBtn"
                                        onclick="verifyPan()">Verify PAN</button>
                                </div>
                            </div>

                            <div id="panStatus" class="otp-sent-status" style="display: none;">
                                <i class="fas fa-check-circle"></i> PAN verified successfully!
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between pt-3 gap-3">
                                <button class="btn btn-outline-custom flex-md-shrink-1"
                                    style="width: auto; padding-left: 40px; padding-right: 40px;"
                                    onclick="nextStep(2)">Back</button>
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;" id="panContinueBtn"
                                    onclick="nextStep(4)">Continue</button>
                            </div>
                        </div>

                        @if($userType === 'Business')
                        <!-- ===== BUSINESS KYC (CSB-V) STEPS 4-7 ===== -->

                        <!-- Business Step 4: CSB-V (Export Codes + LUT + Banking + Billing merged) -->
                        <div id="step4-content" class="step-content">
                            <h3 class="kyc-card-title">CSB-<span class="gradient-text">V</span></h3>
                            <p class="text-muted mb-4">Complete your CSB-V details: Export Codes, LUT, Banking and Billing
                                information.</p>

                            <!-- ===== Export Codes Section ===== -->
                            <h5 class="fw-bold mt-2 mb-3" style="color:#4338ca;"><i class="fas fa-file-export me-2"></i>Export Codes</h5>

                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label-custom">IEC Number</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom"
                                            placeholder="10-character IEC number" id="bizIecNumber" maxlength="10"
                                            pattern="[A-Za-z0-9]{10}" title="IEC Number must be exactly 10 letters or digits"
                                            oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 10)">
                                        <i class="fas fa-file-export"></i>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Upload IEC Certificate</label>
                                    <div id="bizIecUploadArea"
                                        style="border: 2px dashed #c7d2fe; border-radius: 16px; padding: 16px; text-align: center; background: #f8faff; cursor: pointer; transition: all 0.2s ease;"
                                        onclick="document.getElementById('bizIecFileInput').click()">
                                        <input type="file" id="bizIecFileInput"
                                            accept="image/png, image/jpeg, image/jpg, application/pdf"
                                            style="display: none;">
                                        <div id="bizIecUploadPlaceholder">
                                            <i class="fas fa-file-export"
                                                style="font-size: 28px; color: #6366f1; margin-bottom: 6px; display: block;"></i>
                                            <p class="mb-1 fw-semibold" style="color: #4338ca; font-size: 13px;">Click to
                                                upload IEC</p>
                                            <p class="text-muted small mb-0">PNG, JPG or PDF</p>
                                        </div>
                                        <div id="bizIecPreview" style="display: none;">
                                            <i class="fas fa-check-circle"
                                                style="font-size: 24px; color: #10b981; display: block; margin-bottom: 4px;"></i>
                                            <p class="mb-0 fw-semibold small" id="bizIecFileName"
                                                style="color: #166534;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label-custom">AD Code (Authorized Dealer Code)</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom"
                                            placeholder="Enter 14-digit AD Code" id="bizAdCode"
                                            inputmode="numeric" maxlength="14" pattern="[0-9]{14}"
                                            title="AD Code must be exactly 14 digits"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0, 14)">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <small class="text-muted">AD Code must be exactly 14 numeric digits.</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Upload AD Code Document</label>
                                    <div id="bizAdCodeUploadArea"
                                        style="border: 2px dashed #c7d2fe; border-radius: 16px; padding: 16px; text-align: center; background: #f8faff; cursor: pointer; transition: all 0.2s ease;"
                                        onclick="document.getElementById('bizAdCodeFileInput').click()">
                                        <input type="file" id="bizAdCodeFileInput"
                                            accept="image/png, image/jpeg, image/jpg, application/pdf"
                                            style="display: none;">
                                        <div id="bizAdCodeUploadPlaceholder">
                                            <i class="fas fa-university"
                                                style="font-size: 28px; color: #6366f1; margin-bottom: 6px; display: block;"></i>
                                            <p class="mb-1 fw-semibold" style="color: #4338ca; font-size: 13px;">Click to
                                                upload AD Code</p>
                                            <p class="text-muted small mb-0">PNG, JPG or PDF</p>
                                        </div>
                                        <div id="bizAdCodePreview" style="display: none;">
                                            <i class="fas fa-check-circle"
                                                style="font-size: 24px; color: #10b981; display: block; margin-bottom: 4px;"></i>
                                            <p class="mb-0 fw-semibold small" id="bizAdCodeFileName"
                                                style="color: #166534;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4" style="border-color:#e2e8f0;">

                            <!-- ===== LUT Details Section ===== -->
                            <h5 class="fw-bold mt-2 mb-3" style="color:#4338ca;"><i class="fas fa-file-contract me-2"></i>LUT Details</h5>

                            <div class="row g-3 align-items-end mb-3">
                                <div class="col-12 col-lg-4">
                                    <label class="form-label-custom" for="bizLutBondStartYear">LUT Bond Start Year</label>
                                    <div class="input-group-custom">
                                        <select class="form-control input-custom" style="height:100%" id="bizLutBondStartYear">
                                            <option value="">Select Start Year</option>
                                            @foreach(range(now()->year, now()->year + 5) as $lutStartYear)
                                                <option value="{{ $lutStartYear }}">{{ $lutStartYear }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label class="form-label-custom" for="bizLutBondEndYear">LUT Bond End Year</label>
                                    <div class="input-group-custom">
                                        <select class="form-control input-custom" style="height:100%" id="bizLutBondEndYear" disabled>
                                            <option value="">Select Start Year First</option>
                                        </select>
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <input type="hidden" id="bizLutBondYear">
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label class="form-label-custom" for="bizLutExpiry">LUT Expiry Date</label>
                                    <div class="input-group-custom">
                                        <input type="date" class="form-control input-custom" id="bizLutExpiry">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label-custom" for="bizLutFileInput">Upload LUT Document</label>
                                    <div id="bizLutUploadArea"
                                        style="border: 2px dashed #c7d2fe; border-radius: 16px; min-height: 180px; padding: 24px; display: flex; align-items: center; justify-content: center; text-align: center; background: #f8faff; cursor: pointer; transition: all 0.2s ease;"
                                        onclick="document.getElementById('bizLutFileInput').click()">
                                        <input type="file" id="bizLutFileInput" accept="application/pdf"
                                            style="display: none;">
                                        <div id="bizLutUploadPlaceholder" style="width: 100%; text-align: center;">
                                            <i class="fas fa-file-contract"
                                                style="font-size: 36px; color: #6366f1; margin-bottom: 8px; display: block;"></i>
                                            <p class="mb-1 fw-semibold" style="color: #4338ca; font-size: 14px;">Click to upload LUT Document</p>
                                            <p class="text-muted small mb-0">PDF only (max 5MB)</p>
                                        </div>
                                        <div id="bizLutPreview" style="display: none; width: 100%; text-align: center;">
                                            <i class="fas fa-check-circle"
                                                style="font-size: 28px; color: #10b981; display: block; margin-bottom: 6px;"></i>
                                            <p class="mb-0 fw-semibold small" id="bizLutFileName"
                                                style="color: #166534;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4" style="border-color:#e2e8f0;">

                            <!-- ===== Banking Details Section ===== -->
                            <h5 class="fw-bold mt-2 mb-3" style="color:#4338ca;"><i class="fas fa-landmark me-2"></i>Banking Details</h5>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Bank Category</label>
                                    <div class="input-group-custom">
                                        <select class="form-control input-custom" style="height:100%" id="bizBankType">
                                            <option value="">Select bank category</option>
                                            <option value="government">Public Sector Bank</option>
                                            <option value="private">Private Sector Bank</option>
                                        </select>
                                        <i class="fas fa-landmark"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Bank Account Number</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom"
                                            placeholder="Enter bank account number" id="bizBankAccount"
                                            inputmode="numeric" minlength="9" maxlength="18" pattern="[0-9]{9,18}"
                                            title="Bank Account Number must contain 9 to 18 digits"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0, 18)">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4" style="border-color:#e2e8f0;">

                            <!-- ===== Billing Details Section ===== -->
                            <h5 class="fw-bold mt-2 mb-3" style="color:#4338ca;"><i class="fas fa-file-invoice-dollar me-2"></i>Billing Details</h5>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Billing GST Number</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom"
                                            placeholder="Auto-filled from GST verification" id="bizBillingGst"
                                            style="text-transform: uppercase;" readonly>
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Billing Contact Number</label>
                                    <div class="input-group-custom">
                                        <input type="tel" class="form-control input-custom"
                                            placeholder="10-digit mobile number" id="bizBillingContact" inputmode="numeric"
                                            maxlength="10" pattern="[6-9][0-9]{9}"
                                            title="Billing Contact Number must contain 10 digits and start with 6, 7, 8, or 9"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0, 10); if (/^[0-5]/.test(this.value)) { this.value = ''; }">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Billing Email</label>
                                    <div class="input-group-custom">
                                        <input type="email" class="form-control input-custom"
                                            placeholder="billing@company.com" id="bizBillingEmail">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label class="form-label-custom">Billing Address (as per GST)</label>
                                    <div class="input-group-custom">
                                        <textarea class="form-control input-custom" rows="3"
                                            placeholder="Enter billing address as per GST certificate"
                                            id="bizBillingAddress"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between pt-3 gap-3">
                                <button class="btn btn-outline-custom flex-md-shrink-1"
                                    style="width: auto; padding-left: 40px; padding-right: 40px;"
                                    onclick="nextStep(3)">Back</button>
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;"
                                    onclick="nextStep(5)">Continue</button>
                            </div>
                        </div>

                        <!-- Business Step 5: Upload Signature -->
                        <div id="step5-content" class="step-content">
                            <h3 class="kyc-card-title">Upload <span class="gradient-text">Signature</span></h3>
                            <p class="text-muted small mb-4">Upload your authorized signature with company stamp to be appended to the agreement.</p>

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label-custom">Authorized Signature (with Company Stamp)</label>
                                    <div id="bizSignatureUploadArea" role="button" tabindex="0"
                                        style="display: block; border: 2px dashed #c7d2fe; border-radius: 16px; padding: 24px; text-align: center; background: #f8faff; cursor: pointer; transition: all 0.2s ease;"
                                        onclick="document.getElementById('bizSignatureFileInput').click()"
                                        onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); document.getElementById('bizSignatureFileInput').click(); }">
                                        <input type="file" id="bizSignatureFileInput" name="signature_document"
                                            accept=".png,.jpg,.jpeg,image/png,image/jpeg" style="display: none;"
                                            onclick="event.stopPropagation()">
                                        <div id="bizSignatureUploadPlaceholder">
                                            <i class="fas fa-signature"
                                                style="font-size: 48px; color: #6366f1; margin-bottom: 12px; display: block;"></i>
                                            <p class="mb-1 fw-semibold" style="color: #4338ca;">Click to upload your signature</p>
                                            <p class="text-muted small mb-0">PNG or JPG, transparent background preferred (max 5MB)</p>
                                        </div>
                                        <div id="bizSignaturePreviewWrap" style="display: none;">
                                            <img id="bizSignaturePreviewImg" alt="Authorized signature preview"
                                                style="max-height: 140px; max-width: 100%; object-fit: contain; border-radius: 8px; background: #fff; padding: 8px;">
                                            <p class="text-muted small mt-2 mb-0">
                                                <a href="#" onclick="event.stopPropagation(); resetBusinessSignatureUpload(); return false;">Remove & re-upload</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between pt-3 gap-3">
                                <button class="btn btn-outline-custom flex-md-shrink-1"
                                    style="width: auto; padding-left: 40px; padding-right: 40px;"
                                    onclick="nextStep(4)">Back</button>
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;"
                                    onclick="nextStep(6)">Continue</button>
                            </div>
                        </div>

                        @else
                        <!-- ===== PERSONAL KYC (CSB-IV) STEPS 4-5 ===== -->

                        <!-- Step 4 Content -->
                        <div id="step4-content" class="step-content">
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
                                <button class="btn btn-outline-custom flex-md-shrink-1"
                                    style="width: auto; padding-left: 40px; padding-right: 40px;"
                                    onclick="nextStep(3)">Back</button>
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;"
                                    onclick="nextStep(5)">Continue</button>
                            </div>
                        </div>

                        <!-- Step 5 Content - Upload Signature -->
                        <div id="step5-content" class="step-content">
                            <h3 class="kyc-card-title">Upload <span class="gradient-text">Signature</span></h3>
                            <p class="text-muted small mb-4">Upload your signature to be appended to the agreement.</p>

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label-custom">Customer Signature</label>
                                    <div id="signatureUploadArea"
                                        style="border: 2px dashed #c7d2fe; border-radius: 16px; padding: 24px; text-align: center; background: #f8faff; cursor: pointer; transition: all 0.2s ease;"
                                        onclick="document.getElementById('signatureFileInput').click()">
                                        <input type="file" id="signatureFileInput" accept="image/png, image/jpeg, image/jpg"
                                            style="display: none;">
                                        <div id="signatureUploadPlaceholder">
                                            <i class="fas fa-signature"
                                                style="font-size: 48px; color: #6366f1; margin-bottom: 12px; display: block;"></i>
                                            <p class="mb-1 fw-semibold" style="color: #4338ca;">Click to upload your signature</p>
                                            <p class="text-muted small mb-0">PNG or JPG, transparent background preferred</p>
                                        </div>
                                        <div id="signaturePreviewWrap" style="display: none;">
                                            <img id="signaturePreviewImg" alt="Signature preview"
                                                style="max-height: 140px; max-width: 100%; object-fit: contain; border-radius: 8px; background: #fff; padding: 8px;">
                                            <p class="text-muted small mt-2 mb-0">
                                                <a href="#" onclick="resetSignatureUpload(); return false;">Remove & re-upload</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between pt-3 gap-3">
                                <button class="btn btn-outline-custom flex-md-shrink-1"
                                    style="width: auto; padding-left: 40px; padding-right: 40px;"
                                    onclick="nextStep(4)">Back</button>
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;"
                                    onclick="nextStep(6)">Continue</button>
                            </div>
                        </div>
                        @endif

                        <!-- Step 6 Content - Bill / Terms & Conditions -->
                        <div id="step6-content" class="step-content">
                            <!-- <h3 class="kyc-card-title">Bill</h3> -->

                            <div id="billTermsDocument" class="mt-4">
                                <div class="document-wrapper">

                                    <!-- TITLE -->
                                    <h1><span class="underline-title">TERMS AND CONDITIONS</span></h1>
                                    <span class="subhead-company"><strong>UNITED WORLDWIDE COURIERS PVT
                                            LTD</strong></span>

                                    <!-- Agreement -->
                                    <h2>Agreement</h2>
                                    <p>These terms and conditions forms a binding agreement ("Terms & Conditions")
                                        between you and the Legal Entity that you are representing, if any, (hereinafter
                                        collectively referred to as "You" or "Your" or "User", which expression shall,
                                        unless it is repugnant to the subject or context thereof, mean and include its
                                        legal heirs, executors, administrators, successors, affiliates, and permitted
                                        assigns) <strong>ON THE ONE PART</strong> and UNITED WORLDWIDE COURIERS PVT LTD
                                        , a company registered under the Companies Act, 1956, having its registered
                                        office at Building no 1, Lower Ground Floor, Khasra No 505, Bypass Road,
                                        Mahipalpur Extension, Mahipalpur, New Delhi, Delhi 110037, (hereinafter referred
                                        to as "United Worldwide Couriers" or "We" or "Our" or "Us", which expression
                                        shall, unless it is repugnant to the subject or context thereof, mean and
                                        include its successors, affiliates and assigns) <strong>ON THE OTHER
                                            PART</strong>.</p>
                                    <p>The User and United Worldwide Couriers shall hereinafter collectively be referred
                                        to as "Parties" and individually as "Party".</p>

                                    <!-- Introduction -->
                                    <h2>Introduction</h2>
                                    <p>We through our Platforms offer logistic/shipping services to you either through
                                        Shipping Vendors or ourselves ("Services") and/or sell certain Products, in
                                        accordance and subject to compliance with the terms and conditions contained in
                                        the Agreement.</p>
                                    <p>Please read these Terms & Conditions and other documents referred herein
                                        carefully before registering on, accessing, browsing, downloading or using any
                                        Services or purchasing any Products offered on the Platforms or any electronic
                                        device.</p>

                                    <!-- Eligibility -->
                                    <h2>Eligibility</h2>
                                    <p>The platforms, services, and products are available only to persons competent to
                                        enter into legally binding agreements under the Indian Contracts Act, 1872. By
                                        using the platforms or services, you represent that you are 18 years of age or
                                        older, capable of entering into a legally binding agreement, and not barred from
                                        accessing or using the platforms or services. You also represent that you have
                                        full rights, powers, and authority to enter into and perform the Agreement and
                                        that doing so does not violate any applicable law, covenant, order, judgment, or
                                        decree binding on you.</p>

                                    <!-- Account Use -->
                                    <h2>Account Use</h2>
                                    <p>You may possess only one account unless otherwise permitted by United Worldwide
                                        Couriers in writing. Login credentials are intended solely for your personal use
                                        and must be kept secret and safe. Multiple users may not share the same login,
                                        and you may not transfer, assign, sublicense, lease, share, or otherwise permit
                                        unauthorized use of any login credentials, access rights, account, token, or
                                        system access. You remain responsible for all activities and transactions under
                                        your account, whether carried out knowingly, negligently, or by employees,
                                        agents, contractors, affiliates, or any other person using your credentials.</p>

                                    <!-- Platform Access -->
                                    <h2>Platform Access</h2>
                                    <p>Subject to compliance with this Agreement, the Company authorizes you personal,
                                        non-exclusive, non-transferable, limited, revocable privilege to enter and use
                                        Platforms and/or avail all or some of the Services and/or purchase the Products.
                                        The availing of any Service(s) by You shall also be subject to compliance with
                                        all the other rules, guidelines, policies, terms, and conditions specified by Us
                                        for that particular Service(s) being availed by You. You hereby consent and
                                        agree to comply with the rules, guidelines, policies, terms and conditions,
                                        instructions, requests, etc., as may be specified by United Worldwide Couriers,
                                        from time to time, in relation to each Service availed or to be availed by You.
                                        Your access to avail the Services will at all times be solely at the discretion
                                        of United Worldwide Couriers.</p>
                                    <p>All content, software, layouts, workflows, processes, trademarks, service marks,
                                        trade names, dashboards, graphics, text, rate logic, data models, and
                                        compilations made available by the Company are proprietary to the Company or its
                                        licensors and may not be copied, modified, reverse engineered, distributed,
                                        sublicensed, publicly displayed, commercially exploited, or otherwise used
                                        without prior written consent. Access to and continued use of the services
                                        remains at the sole discretion of the Company.</p>

                                    <!-- User Responsibilities -->
                                    <h2>User Responsibilities</h2>
                                    <p>You represent and warrant that all information, documents, declarations, bank
                                        details, tax registrations, KYC records, addresses, phone numbers, contact
                                        details, and other data provided to the Company are true, correct, current,
                                        complete, lawful, and not misleading. The Company may require supporting
                                        documents to verify any information and may suspend or refuse services pending
                                        successful verification. You must immediately notify the Company of any
                                        unauthorized use of your passcode or account, any breach of security, and any
                                        change in your email address, mobile number, or other personal information
                                        provided for use of the services.</p>

                                    <!-- Lawful Use -->
                                    <h2>Lawful Use</h2>
                                    <p>You shall use the services only for lawful purposes and strictly in accordance
                                        with this Agreement, applicable law, applicable trade controls, customs laws,
                                        export-import regulations, sanctions laws, tax laws, and generally accepted
                                        commercial practices. You shall not impersonate any person, misrepresent your
                                        identity, use the services for unauthorized or unlawful purposes, interfere with
                                        the platform or networks, access the services through unauthorized means, or
                                        otherwise engage in activity that disrupts or compromises the services or the
                                        Company's business. The Company may customize, modify, suspend, restrict, or
                                        discontinue any feature, service, integration, route, rate, serviceability
                                        option, collection mode, or operational process at any time, including as
                                        required by law, government policy, carrier instructions, sanctions, local
                                        restrictions, security concerns, or operational reasons.</p>

                                    <!-- Privacy and Data -->
                                    <h2>Privacy and Data</h2>
                                    <p>By using the website and/or by providing your information, you consent to the
                                        collection and use of the information you disclose on the website in accordance
                                        with the Privacy Policy, including consent for sharing your information as set
                                        out in that policy. You agree that the Company may store personally identifiable
                                        information such as your name, email address, mobile phone number, address,
                                        contact details, postal code, and demographic profile information, as well as
                                        browsing information such as pages visited, links clicked, and frequency of
                                        access. All such information shall be stored and used in accordance with the
                                        Privacy Policy.</p>

                                    <!-- Updates and Amendments -->
                                    <h2>Updates and Amendments</h2>
                                    <p>Any amendment to this Agreement comes into effect immediately upon posting unless
                                        otherwise specified, and not on the date on which you may be notified of the
                                        change. Any failure or delay in notifying you of changes or amendments does not
                                        affect the validity or effectiveness of those changes. Continued access to or
                                        use of the platforms and services will be treated as your irrevocable and
                                        unconditional acceptance of the amended Agreement. You are solely responsible
                                        for keeping yourself updated by regularly reviewing the Agreement on the
                                        platforms.</p>

                                    <!-- Communications -->
                                    <h2>Communications</h2>
                                    <p>All notices, communications, updates, invoices, rate revisions, dashboard alerts,
                                        service notifications, legal notices, and operational instructions may be issued
                                        by the Company through email, dashboard, mobile application, SMS, WhatsApp,
                                        registered mobile number, support ticketing system, courier, or any other
                                        officially designated communication channel. You consent to receive
                                        communications through such channels. The Company may also verify KYC
                                        information and share necessary details with carriers, insurers, customs
                                        authorities, importer of record entities, marketplace partners, banks, payment
                                        partners, police, courts, government agencies, complainants, or any other
                                        relevant entity for compliance, claims processing, dispute handling, fraud
                                        review, legal proceedings, or operational processing, in accordance with
                                        applicable law.</p>

                                    <!-- Booking and Handling -->
                                    <h2>Booking and Handling</h2>
                                    <p>You may choose between United Worldwide Couriers Pickup and Self-Ship, subject to
                                        availability. Under United Worldwide Couriers Pickup, the Company will collect
                                        shipments from the pickup address you provide; under Self-Ship, you must deliver
                                        shipments to the nearest hub. Upon arrival at the hub, each shipment will be
                                        scanned, weighed, and sorted according to destination and selected service. If
                                        there is a discrepancy between declared and recorded weight, the shipment will
                                        be placed on hold and you will be notified by email for approval; once approved,
                                        the shipment will proceed to its destination. After a shipment has left the hub,
                                        no further weight adjustments will be charged to your account.</p>

                                    <!-- International Shipments -->
                                    <h2>International Shipments</h2>
                                    <p>For international shipments, after export clearance in India, the shipment will
                                        be customs-cleared in the destination country and received at the local office
                                        before being handed to the last-mile carrier. Last-mile carriers may vary by
                                        country and may change from time to time. For shipments routed through branded
                                        carrier networks such as DHL, UPS, and FedEx, the shipment is connected to the
                                        carrier's hub in Delhi and tracking is available on the carrier's website using
                                        the tracking number.</p>

                                    <!-- Delivery and RTO -->
                                    <h2>Delivery and RTO</h2>
                                    <p>The Company will attempt delivery of shipments at least once, and many last-mile
                                        shipping vendors attempt delivery twice depending on their policies. If no one
                                        is available to receive a shipment, the carrier may, at its discretion, leave it
                                        with a neighbour, deposit it at the local post office for customer pickup, or
                                        place it in a secure external location outside the house, including a mailbox. A
                                        shipment may be deemed undeliverable for reasons including incorrect or
                                        incomplete address details, customer refusal to accept delivery, or customer
                                        refusal to pay applicable duties and/or taxes. If a shipment is undeliverable,
                                        return-to-origin charges will apply, storage charges may apply where applicable,
                                        and the shipment may be destroyed, returned, or disposed of in accordance with
                                        the relevant service rules.</p>

                                    <!-- Fees and Payment -->
                                    <h2>Fees and Payment</h2>
                                    <p>You shall pay all subscription fees, shipping charges, freight, RTO charges,
                                        reverse pickup charges, COD handling charges, customs-related service fees,
                                        importer of record charges, storage charges, demurrage, incidental expenses,
                                        surcharges, accessorial charges, address correction fees, penalties, taxes, and
                                        all other amounts applicable to the services. Unless expressly stated otherwise,
                                        all fees are exclusive of taxes and GST and other applicable taxes shall be
                                        charged in addition. The Company may add new services for additional charges or
                                        revise existing charges, rates, surcharges, accessorial charges, service
                                        conditions, or fee structures at any time by notice through dashboard, email,
                                        mobile application, rate card, calculator link, annexure, or any other official
                                        communication channel.</p>

                                    <!-- Invoicing and Recovery -->
                                    <h2>Invoicing and Recovery</h2>
                                    <p>The Company may issue invoices periodically, including mid-month, month-end, or
                                        on such other cycle as determined by the Company. You must verify invoice
                                        contents promptly and, unless a shorter period is prescribed for a specific
                                        service model, raise any bona fide dispute within five working days of invoice
                                        availability and pay undisputed amounts within seven days from the invoice date
                                        or such other due date specified in writing. Failure to raise a dispute within
                                        the prescribed period constitutes deemed acceptance of the invoice. If you fail
                                        to pay any amount when due, the Company may suspend shipping, retain and adjust
                                        outstanding amounts against COD remittances, wallet balances, credits, deposits,
                                        refunds, or any amounts payable to you, retain custody of shipments, re-route
                                        shipments, levy interest at 18 percent per annum from the due date until
                                        realization, forfeit security deposit or wallet balance where contractually
                                        permitted, and dispose of shipments in accordance with this Agreement where
                                        defaults are not regularized within the applicable period.</p>

                                    <!-- COD and Credits -->
                                    <h2>COD and Credits</h2>
                                    <p>For shipments booked under cash on delivery, you appoint the Company as a limited
                                        collection agent solely for the purpose of collecting the COD amount from the
                                        consignee through the Company's logistics vendors and remitting the balance
                                        after deduction of applicable freight, service fees, handling fees, taxes,
                                        offsets, reversals, and other lawful deductions. The Company has no title in the
                                        goods. Unless otherwise agreed, COD remittance may be made within eight days
                                        from delivery of the relevant shipment or in accordance with the remittance
                                        cycle then followed by the Company, subject to reconciliation, carrier
                                        remittance, fraud review, dispute review, status verification, valid bank
                                        details, and absence of offset rights. Where the Merchant operates on a prepaid
                                        model, sufficient shipping credits must be maintained in the wallet or account
                                        before availing services, and credit balance may be used only for booking
                                        shipments. Credit may be forfeited if no shipment is booked for three years from
                                        the last shipment date, subject to applicable law, and refunds, if approved, may
                                        be restricted to the original source or mode of payment and may be conditioned
                                        upon KYC compliance and any surcharge or deduction permitted by law or contract.
                                    </p>

                                    <!-- Weight and Pricing -->
                                    <h2>Weight and Pricing</h2>
                                    <p>Each shipment is subject to weight and size limits that may differ by shipping
                                        vendor and destination. Volumetric dimensional weight will be calculated
                                        automatically in the booking panel using the formula length cm x breadth cm x
                                        height cm / 5000. For billing, where volumetric weight is 5 kg or less, charges
                                        will be based on actual dead weight; where volumetric weight exceeds 5 kg,
                                        charges will be based on whichever is greater, actual weight or volumetric
                                        weight. Certain items that require special handling, as defined in the Company's
                                        internal operational guidelines, will incur an additional special handling fee.
                                    </p>

                                    <!-- Claims and Refunds -->
                                    <h2>Claims and Refunds</h2>
                                    <p>Claims and compensation are available only under the specific circumstances
                                        described in the Agreement. To initiate a claim, you must provide the United
                                        Worldwide Couriers airway bill number and all supporting documents to
                                        Csdunitedcouriers.biz. For claims involving branded carrier networks such as
                                        DHL, UPS, and FedEx, the same policy applies subject to carrier-specific
                                        timelines. No claim will be considered if submitted more than sixty working days
                                        after the inward scan, if the Company notifies you that a shipment is stuck,
                                        undelivered, or under RTO and you do not respond within seven working days, or
                                        if required evidence and documents are not provided.</p>

                                    <!-- Claim Evidence -->
                                    <h2>Claim Evidence</h2>
                                    <p>For no first scan claims, the required AWB and supporting documents must be
                                        submitted, and compensation will be limited in accordance with the applicable
                                        limits. For no delivery scan or lost-in-transit claims, you must submit the
                                        buyer-seller chat and proof of refund along with the required AWB and documents.
                                        For claims alleging non-connectivity, the signed pickup manifest for the
                                        disputed shipment must be submitted within three working days of pickup, and
                                        claims lacking a signed manifest will not be maintainable. For damage,
                                        pilferage, tampering, crushing, or leakage, the recipient must record negative
                                        remarks on the proof of delivery at the time of delivery, and such claims will
                                        be entertained only if made within forty-eight hours of delivery or receipt and
                                        only where the outer packaging applied by the Company or the shipping vendor is
                                        damaged, altered, or tampered with.</p>

                                    <!-- Liability -->
                                    <h2>Liability</h2>
                                    <p>The liability of United Worldwide Couriers in relation to the services is
                                        strictly limited to the extent expressly provided under this Agreement, the
                                        applicable Terms and Conditions, annexures, policies, and service-specific
                                        provisions. The platform and services are provided on an as-is, as-available,
                                        and reasonable-efforts basis, and the Company does not guarantee uninterrupted,
                                        error-free, secure, or continuous operation. The Company does not independently
                                        verify, validate, endorse, or authenticate information, declarations, listings,
                                        content, documents, data, or materials provided by users, merchants, customers,
                                        consignees, shipping vendors, or third parties. Shipments are not insured unless
                                        separately purchased by the merchant.</p>

                                    <!-- Indemnity -->
                                    <h2>Indemnity</h2>
                                    <p>You shall indemnify, defend, and hold harmless the Company and its affiliates,
                                        directors, officers, employees, agents, subcontractors, consultants, licensors,
                                        service providers, shipping partners, customs agents, importer of record
                                        entities, marketplace partners, and representatives from and against claims,
                                        actions, proceedings, losses, damages, liabilities, penalties, duties, taxes,
                                        interest, costs, and expenses arising out of or related to your access to or use
                                        of the services, breach of the Agreement, violation of law, misdeclaration,
                                        under-declaration, wrongful valuation, wrong HS classification, false origin
                                        declaration, counterfeit goods, restricted goods, prohibited goods, dangerous
                                        goods, infringing goods, defective or unsafe goods, third-party claims, duties,
                                        penalties, detention, demurrage, storage charges, or negligent, wrongful, or
                                        fraudulent acts or omissions by you or your personnel.</p>

                                    <!-- Compliance -->
                                    <h2>Compliance</h2>
                                    <p>Each party shall comply with all applicable laws, including state, central,
                                        customs, and international laws, statutes, rules, and regulations relating to
                                        its performance under this Agreement. Each party shall pay all fees and charges
                                        required by applicable law and maintain all licenses, permits, authorizations,
                                        registrations, and qualifications necessary to perform its obligations. The
                                        Merchant further represents that it lawfully owns, possesses, controls, sells,
                                        exports, imports, markets, and ships all goods tendered under this Agreement and
                                        has obtained all necessary consents, licenses, registrations, declarations, and
                                        approvals required for the services.</p>

                                    <!-- Confidentiality -->
                                    <h2>Confidentiality</h2>
                                    <p>Each party may receive confidential information of/from the other in the course
                                        of performance of this Agreement. The receiving party shall keep such
                                        information strictly confidential, use it only for performance of this
                                        Agreement, restrict disclosure on a strict need-to-know basis, and protect it
                                        with at least the same degree of care it uses for its own confidential
                                        information and in any event not less than reasonable care. Upon termination or
                                        request, the receiving party shall return or destroy confidential information to
                                        the extent reasonably practicable and certify compliance if requested.</p>

                                    <!-- Intellectual Property -->
                                    <h2>Intellectual Property</h2>
                                    <p>All intellectual property in the Company's platform, software, systems, APIs,
                                        dashboards, workflows, trademarks, brand assets, websites, documents, templates,
                                        service descriptions, rate engines, operating methods, and derivative works
                                        shall remain vested exclusively in the Company or its licensors. All
                                        intellectual property owned by either party before this Agreement shall remain
                                        with that party. Any feedback, suggestions, enhancement requests, process
                                        improvements, or derivative developments created in connection with the services
                                        may be used by the Company without restriction unless otherwise agreed in
                                        writing.</p>

                                    <!-- Termination -->
                                    <h2>Termination</h2>
                                    <p>This Agreement begins on the date you first avail the services and continues
                                        unless terminated in accordance with this Agreement. You may request termination
                                        by thirty days' prior written notice, subject to completion of in-transit
                                        shipments, reconciliation, settlement of all dues, submission of documents,
                                        discharge of liabilities, and compliance with any service-specific lock-in or
                                        closure conditions. The Company may suspend or terminate this Agreement or any
                                        account immediately, with or without notice, if you breach the Agreement, create
                                        legal, regulatory, reputational, financial, fraud, sanctions, security, or
                                        operational risk, ship prohibited or unlawful goods, default in payment, fail
                                        KYC or compliance verification, receive instructions from a carrier or
                                        authority, or if the Company elects to discontinue the relationship for lawful
                                        business convenience.</p>

                                    <!-- Misuse -->
                                    <h2>Misuse</h2>
                                    <p>The Company may restrict, deactivate, suspend, or terminate the account of any
                                        Merchant that abuses or misuses the services, including by creating false or
                                        duplicate profiles, infringing intellectual property rights, shipping prohibited
                                        or suspicious goods, evading fees, manipulating system workflows,
                                        under-declaring weight or value, booking shipments outside permitted use,
                                        circumventing controls, refusing to cooperate in an investigation, or engaging
                                        in conduct deemed suspicious, fraudulent, harmful, or contrary to the purpose of
                                        the services. Repeat violations may result in permanent blacklisting and legal
                                        action.</p>

                                    <!-- Governing Law -->
                                    <h2>Governing Law</h2>
                                    <p>This Agreement is governed by the laws of India. Subject to the arbitration
                                        clause, the courts of New Delhi shall have exclusive jurisdiction to determine
                                        disputes arising out of, under, or in relation to this Agreement. Any dispute
                                        shall be settled by arbitration in New Delhi in accordance with the Indian
                                        Arbitration and Conciliation Act, 1996, in the English language, by a sole
                                        arbitrator appointed by United Courier Services, and the arbitrator's decision
                                        shall be final, conclusive, and binding on the parties.</p>

                                    <!-- General Terms -->
                                    <h2>General Terms</h2>
                                    <p>No failure or delay in exercising any right, power, or remedy operates as a
                                        waiver unless in writing. If any provision is invalid or unenforceable, the
                                        remaining provisions remain in effect, and the parties shall negotiate in good
                                        faith to replace the invalid provision with one having the same legal and
                                        commercial effect as far as possible. Neither party is created as a partner,
                                        joint venturer, fiduciary, employee, or agent of the other, except where the
                                        Company acts as a limited collection agent for COD remittance to the extent
                                        expressly stated. This Agreement, together with annexures, schedules, rate
                                        sheets, dashboard notices, web links, operating procedures, and written addenda,
                                        constitutes the entire agreement between the parties on its subject matter.</p>

                                    <!-- Definitions and Interpretation -->
                                    <h2>Definitions and Interpretation</h2>
                                    <h3>Definitions</h3>
                                    <p>For the purposes of this Agreement:</p>
                                    <div class="glossary-item"><span class="glossary-term">"Affiliate"</span><span
                                            class="glossary-def">means, in relation to a Party, any entity that directly
                                            or indirectly controls, is controlled by, or is under common control with
                                            that Party.</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Applicable Law"</span><span
                                            class="glossary-def">means all laws, statutes, rules, regulations,
                                            notifications, circulars, orders, trade controls, sanctions, customs laws,
                                            tax laws, and governmental requirements applicable to a Party, the Services,
                                            or the goods.</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Confidential
                                            Information"</span><span class="glossary-def">means with respect to each
                                            Party, any information or trade secrets, schedules, business plans
                                            including, without limitation, commercial information, financial
                                            projections, client information, administrative and/or organizational
                                            matters of a confidential/secret nature in whatever form which is acquired
                                            by, or disclosed to, the other Party pursuant to this Agreement, and
                                            includes any tangible or intangible non-public information that is marked or
                                            otherwise designated as 'confidential', 'proprietary', 'restricted', or with
                                            a similar designation by the disclosing Party at the time of its disclosure
                                            to the other Party, or is otherwise reasonably understood to be confidential
                                            by the circumstances surrounding its disclosure, but excludes information
                                            which: (i) is required to be disclosed in a judicial or administrative
                                            proceeding, or is otherwise requested or required to be disclosed pursuant
                                            to applicable law or regulation, and (ii) which at the time it is so
                                            acquired or disclosed, is already in the public domain or becomes so other
                                            than by reason of any breach or non-performance by the other Party of any of
                                            the provisions of this Agreement;</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Force Majeure
                                            Event"</span><span class="glossary-def">includes act of God, war, civil
                                            disturbance, terrorism, strike, lockout, fire, flood, explosion, epidemic,
                                            pandemic, transport disruption, carrier failure, cyber disruption, customs
                                            restriction, export-import policy change, sanction, or government action
                                            beyond reasonable control.</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Intellectual
                                            Property"</span><span class="glossary-def">means all patents, copyrights,
                                            trademarks, trade names, service marks, logos, domain names, trade secrets,
                                            designs, software, databases, data rights, know-how, inventions, and all
                                            allied intellectual property rights and goodwill.</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Services"</span><span
                                            class="glossary-def">means all domestic and international shipping, carriage
                                            facilitation, logistics management, customs support, importer of record
                                            services, reverse logistics, COD collection, marketplace support, technology
                                            access, and allied services provided or facilitated by the Company.</span>
                                    </div>
                                    <div class="glossary-item"><span class="glossary-term">"Shipment"</span><span
                                            class="glossary-def">means any parcel, package, consignment, goods,
                                            document, or item tendered by or on behalf of the Merchant for any
                                            Service.</span></div>

                                    <h3>Interpretation</h3>
                                    <p>Unless the context of this Agreement otherwise requires:</p>
                                    <ul>
                                        <li>(a) heading and bold typeface are only for convenience and shall be ignored
                                            for the purpose of interpretation;</li>
                                        <li>(b) other terms may be defined elsewhere in the text of this Agreement and,
                                            unless otherwise indicated, shall have such meaning throughout this
                                            Agreement;</li>
                                        <li>(c) references to this Agreement shall be deemed to include any amendments
                                            or modifications to this Agreement, as the case may be;</li>
                                        <li>(d) the terms "hereof", "herein", "hereby", "hereto" and derivative or
                                            similar words refer to this entire Agreement or specified Clauses of this
                                            Agreement, as the case may be;</li>
                                        <li>(e) references to a particular section, clause, paragraph, sub-paragraph or
                                            schedule, exhibit or annexure shall be a reference to that section, clause,
                                            paragraph, sub-paragraph or schedule, exhibit or annexure in or to this
                                            Agreement;</li>
                                        <li>(f) reference to any legislation or law or to any provision thereof shall
                                            include references to any such law as it may, after the date hereof, from
                                            time to time, be amended, supplemented or re-enacted, and any reference to
                                            statutory provision shall include any subordinate legislation made from time
                                            to time under that provision;</li>
                                        <li>(g) a provision of this Agreement must not be interpreted against any Party
                                            solely on the ground that the Party was responsible for the preparation of
                                            this Agreement or that provision, and the doctrine of contra proferentem
                                            does not apply vis-à-vis this Agreement;</li>
                                        <li>(h) references in the singular shall include references in the plural and
                                            vice versa; and</li>
                                        <li>(i) references to the word "include" shall be construed without limitation.
                                        </li>
                                    </ul>

                                    <!-- Customer Support -->
                                    <h2>Customer Support</h2>
                                    <p>If You have any questions, issues, complaint, or seek any clarity in relations to
                                        the Agreement and/or Services/Products, please feel free to contact us at <a
                                            href="mailto:Csd@unitedcouriers.biz">Csd@unitedcouriers.biz</a></p>

                                    <!-- Consolidated Agreement -->
                                    <div class="consolidated-note">
                                        <h3 style="margin-top: 0; border-bottom: none; padding-bottom: 0;">Consolidated
                                            Agreement</h3>
                                        <p>These Terms and Conditions are intended to be read together with the
                                            Consolidated Merchant Agreement, which contains the complete and
                                            comprehensive terms governing the relationship between the parties. In the
                                            event of any inconsistency, ambiguity, or conflict between these Terms and
                                            Conditions and the Consolidated Merchant Agreement, the Consolidated
                                            Merchant Agreement shall prevail to the extent of such inconsistency,
                                            ambiguity, or conflict.</p>
                                    </div>

                                    <!-- Authorized Signature (right-aligned) -->
                                    <div id="billSignatureBlock"
                                        style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: flex-end;">
                                        <p class="text-muted small mb-2" style="align-self: flex-end;"><strong>Authorized Signature:</strong></p>
                                        <div id="billSignaturePlaceholder"
                                            style="min-height: 80px; min-width: 220px; max-width: 280px; display: flex; align-items: center; justify-content: center; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px; background: #fff;">
                                            <span class="text-muted small">No signature uploaded yet.</span>
                                        </div>
                                        <img id="billSignatureImg" alt="Customer signature"
                                            style="display: none; max-height: 90px; max-width: 280px; object-fit: contain;">
                                    </div>

                                    <hr>
                                    <div
                                        style="display: flex; justify-content: space-between; flex-wrap: wrap; margin-top: 0.5rem; color: #34537a; font-size: 0.9rem;">
                                        <span>© UNITED WORLDWIDE COURIERS PVT LTD</span>
                                        <span>New Delhi · India</span>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mt-3">
                                    <button class="btn btn-outline-custom"
                                        style="width: auto; padding-left: 40px; padding-right: 40px;"
                                        onclick="nextStep(5)">Back</button>
                                    <button class="btn btn-primary-custom"
                                        style="width: auto; padding-left: 60px; padding-right: 60px;"
                                        onclick="nextStep(7)">Submit & Finish</button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 7 Content -->
                        <div id="step7-content" class="step-content text-center py-4">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                            </div>
                            <h3 class="kyc-card-title mb-2">Activation <span class="gradient-text">Pending</span></h3>
                            <p class="text-muted mx-auto" style="max-width: 500px;">
                                @if($userType === 'Business')
                                    We have received your Business KYC and CSB-V documents. Our verification team will review your application within 24 hours.
                                @else
                                    We have received your KYC documents. Our verification team will review your application within 24 hours.
                                @endif
                            </p>
                            <div class="mx-auto" style="max-width: 300px;">
                                <button class="btn btn-primary-custom mt-4" onclick="location.reload()">Go to
                                    Dashboard</button>
                            </div>
                        </div>

                    </div>

                    <script>
                    // Only show the welcome popup for a new KYC; resumed drafts open on their saved step.
                    document.addEventListener('DOMContentLoaded', function() {
                        const hasSavedKycDraft = @json((bool) $kycDraft);
                        const welcomeModalEl = document.getElementById('kycWelcomeModal');
                        if (welcomeModalEl && !hasSavedKycDraft) {
                            const welcomeModal = new bootstrap.Modal(welcomeModalEl, {
                                backdrop: 'static',
                                keyboard: false
                            });
                            welcomeModal.show();
                        }
                    });

                    let kycData = {
                        gst_number: '',
                        gst_verified: false,
                        otp_verified: false,
                        aadhar_number: '',
                        aadhar_verified: false,
                        organization_name: '',
                        authorized_signatory: '',
                        billing_address: '',
                        billing_gst: '',
                        billing_contact: '',
                        billing_email: '',
                        terms_accepted: true,
                        // Business KYC (CSB-V) fields
                        is_csb_v: true,
                        is_gst: true,
                        is_lut: true,
                        gst_certificate_number: '',
                        gst_certificate_verified: false,
                        iec_number: '',
                        ad_code: '',
                        lut_expiry_date: '',
                        lut_bond_year: '',
                        bank_account_number: '',
                        bank_type: ''
                    };

                    // Detect Business vs Personal flow and merge any server-side draft.
                    const isBusinessFlow = @json($userType === 'Business');
                    const isAadhaarOptional = @json($isAadhaarOptional);
                    const totalSteps = 7;
                    const savedKycDraft = @json($kycDraft?->form_data ?? []);
                    const savedKycStep = Math.min(totalSteps - 1, Math.max(1,
                        Number(@json($kycDraft?->current_step ?? 1)) || 1));
                    const kycDraftSaveUrl = @json(route('customer.kyc.draft.save'));
                    const kycType = isBusinessFlow ? 'business' : 'personal';
                    let kycDraftTimer = null;
                    let kycDraftSaving = false;
                    let kycDraftQueuedStep = null;
                    kycData = Object.assign(kycData, savedKycDraft || {});

                    function getActiveKycStep() {
                        const active = document.querySelector('.step-content.active');
                        const match = active && active.id.match(/step(\d+)-content/);
                        return match ? Number(match[1]) : 1;
                    }

                    function readKycField(id, key, transform) {
                        const field = document.getElementById(id);
                        if (!field) return;
                        const value = transform ? transform(field.value) : field.value.trim();
                        kycData[key] = value;
                    }

                    function captureKycDraftData() {
                        readKycField('gstInput', 'gst_number', value => value.trim().toUpperCase());
                        readKycField('aadharInput', 'aadhar_number', value => value.replace(/\s+/g, ''));
                        readKycField('panInput', 'pan_number', value => value.trim().toUpperCase());
                        readKycField('panHolderName', 'pan_holder_name');
                        readKycField('panDob', 'pan_dob', value => value);
                        readKycField('bizGstCertNumber', 'gst_certificate_number', value => value.trim().toUpperCase());
                        readKycField('bizIecNumber', 'iec_number');
                        readKycField('bizAdCode', 'ad_code', value => value.replace(/\D/g, '').slice(0, 14));
                        kycData.is_lut = true;
                        syncBusinessLutBondYear();
                        readKycField('bizLutExpiry', 'lut_expiry_date', value => value);
                        readKycField('bizLutBondYear', 'lut_bond_year');
                        readKycField('bizBankType', 'bank_type', value => value);
                        readKycField('bizBankAccount', 'bank_account_number');
                        readKycField('bizBillingGst', 'billing_gst', value => value.trim().toUpperCase());
                        readKycField('bizBillingContact', 'billing_contact');
                        readKycField('bizBillingEmail', 'billing_email');
                        readKycField('bizBillingAddress', 'billing_address');

                        const organization = document.querySelector('#step4-content input[placeholder="Company Ltd"]');
                        const signatory = document.querySelector('#step4-content input[placeholder="Full Name"]');
                        if (!isBusinessFlow && organization) kycData.organization_name = organization.value.trim();
                        if (!isBusinessFlow && signatory) kycData.authorized_signatory = signatory.value.trim();

                        return Object.keys(kycData).reduce(function(draft, key) {
                            const value = kycData[key];
                            const isSignaturePreview = key === 'signature' || key === 'business_signature';
                            if (!isSignaturePreview && !(value instanceof File) && value !== null && value !== undefined) {
                                draft[key] = value;
                            }
                            return draft;
                        }, {});
                    }

                    function saveKycDraft(stepNumber) {
                        const step = Math.min(totalSteps - 1, Math.max(1, Number(stepNumber) || getActiveKycStep()));
                        if (kycDraftSaving) {
                            kycDraftQueuedStep = step;
                            return;
                        }
                        kycDraftSaving = true;
                        fetch(kycDraftSaveUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                kyc_type: kycType,
                                current_step: step,
                                form_data: captureKycDraftData()
                            })
                        }).catch(error => console.error('Unable to save KYC draft:', error))
                            .finally(function() {
                                kycDraftSaving = false;
                                if (kycDraftQueuedStep !== null) {
                                    const queuedStep = kycDraftQueuedStep;
                                    kycDraftQueuedStep = null;
                                    saveKycDraft(queuedStep);
                                }
                            });
                    }

                    function queueKycDraftSave(stepNumber) {
                        clearTimeout(kycDraftTimer);
                        kycDraftTimer = setTimeout(function() {
                            saveKycDraft(stepNumber || getActiveKycStep());
                        }, 600);
                    }

                    // Clone the T&C document into the popup modal (avoids duplicating the large legal text)
                    (function cloneTermsIntoModal() {
                        const source = document.querySelector('#billTermsDocument .document-wrapper');
                        const target = document.getElementById('termsModalContent');
                        if (source && target) {
                            target.innerHTML = '';
                            const clone = source.cloneNode(true);
                            // Remove the signature block from the modal clone to avoid duplicate IDs
                            // (the modal is for reading the legal text and opens before any signature is uploaded)
                            const sigBlock = clone.querySelector('#billSignatureBlock');
                            if (sigBlock) sigBlock.remove();
                            target.appendChild(clone);
                        }
                    })();

                    // Signature previews use temporary object URLs. Only the actual File
                    // is submitted; preview data is never saved in the draft or database.
                    let signaturePreviewUrl = '';
                    let businessSignaturePreviewUrl = '';
                    const signatureFileInput = document.getElementById('signatureFileInput');
                    const businessSignatureFileInput = document.getElementById('bizSignatureFileInput');
                    const signaturePreviewWrap = document.getElementById('signaturePreviewWrap');
                    const signaturePreviewImg = document.getElementById('signaturePreviewImg');
                    const signatureUploadPlaceholder = document.getElementById('signatureUploadPlaceholder');
                    const signatureUploadArea = document.getElementById('signatureUploadArea');
                    const businessSignaturePreviewWrap = document.getElementById('bizSignaturePreviewWrap');
                    const businessSignaturePreviewImg = document.getElementById('bizSignaturePreviewImg');
                    const businessSignatureUploadPlaceholder = document.getElementById('bizSignatureUploadPlaceholder');

                    function resetSignatureUpload() {
                        if (signatureFileInput) signatureFileInput.value = '';
                        if (signaturePreviewUrl) URL.revokeObjectURL(signaturePreviewUrl);
                        signaturePreviewUrl = '';
                        delete kycData.signature;
                        delete kycData.signature_file;
                        if (signaturePreviewImg) signaturePreviewImg.src = '';
                        if (signaturePreviewWrap) signaturePreviewWrap.style.display = 'none';
                        if (signatureUploadPlaceholder) signatureUploadPlaceholder.style.display = 'block';
                        queueKycDraftSave();
                    }

                    function resetBusinessSignatureUpload() {
                        if (businessSignatureFileInput) businessSignatureFileInput.value = '';
                        if (businessSignaturePreviewUrl) URL.revokeObjectURL(businessSignaturePreviewUrl);
                        businessSignaturePreviewUrl = '';
                        delete kycData.business_signature;
                        if (businessSignaturePreviewImg) businessSignaturePreviewImg.src = '';
                        if (businessSignaturePreviewWrap) businessSignaturePreviewWrap.style.display = 'none';
                        if (businessSignatureUploadPlaceholder) businessSignatureUploadPlaceholder.style.display = 'block';
                        queueKycDraftSave();
                    }

                    if (signatureFileInput) {
                        signatureFileInput.addEventListener('change', function(e) {
                            const file = e.target.files && e.target.files[0];
                            if (!file) return;
                            if (!file.type.match(/image\/(png|jpe?g)/i)) {
                                alert('Please upload a PNG or JPG image of your signature.');
                                resetSignatureUpload();
                                return;
                            }
                            if (file.size > 2 * 1024 * 1024) {
                                alert('Signature image must be smaller than 2MB.');
                                resetSignatureUpload();
                                return;
                            }
                            if (signaturePreviewUrl) URL.revokeObjectURL(signaturePreviewUrl);
                            signaturePreviewUrl = URL.createObjectURL(file);
                            delete kycData.signature;
                            kycData.signature_file = file;
                            if (signaturePreviewImg) signaturePreviewImg.src = signaturePreviewUrl;
                            if (signaturePreviewWrap) signaturePreviewWrap.style.display = 'block';
                            if (signatureUploadPlaceholder) signatureUploadPlaceholder.style.display = 'none';
                            saveKycDraft(getActiveKycStep());
                        });
                    }

                    if (businessSignatureFileInput) {
                        businessSignatureFileInput.addEventListener('change', function(e) {
                            const file = e.target.files && e.target.files[0];
                            if (!file) {
                                resetBusinessSignatureUpload();
                                return;
                            }
                            if (!validateBusinessSignatureFile(file, businessSignatureFileInput)) {
                                resetBusinessSignatureUpload();
                                return;
                            }
                            if (businessSignaturePreviewUrl) URL.revokeObjectURL(businessSignaturePreviewUrl);
                            businessSignaturePreviewUrl = URL.createObjectURL(file);
                            delete kycData.business_signature;
                            if (businessSignaturePreviewImg) businessSignaturePreviewImg.src = businessSignaturePreviewUrl;
                            if (businessSignaturePreviewWrap) businessSignaturePreviewWrap.style.display = 'block';
                            if (businessSignatureUploadPlaceholder) businessSignatureUploadPlaceholder.style.display = 'none';
                            queueKycDraftSave();
                        });
                    }

                    // Drag & drop support for the signature upload area
                    if (signatureUploadArea) {
                        ['dragenter', 'dragover'].forEach(evt => {
                            signatureUploadArea.addEventListener(evt, function(e) {
                                e.preventDefault();
                                signatureUploadArea.style.borderColor = '#6366f1';
                                signatureUploadArea.style.background = '#eef2ff';
                            });
                        });
                        ['dragleave', 'drop'].forEach(evt => {
                            signatureUploadArea.addEventListener(evt, function(e) {
                                e.preventDefault();
                                signatureUploadArea.style.borderColor = '#c7d2fe';
                                signatureUploadArea.style.background = '#f8faff';
                            });
                        });
                        signatureUploadArea.addEventListener('drop', function(e) {
                            const file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                            if (file && signatureFileInput) {
                                signatureFileInput.files = e.dataTransfer.files;
                                signatureFileInput.dispatchEvent(new Event('change'));
                            }
                        });
                    }

                    function syncBusinessLutBondYear() {
                        const startYear = document.getElementById('bizLutBondStartYear');
                        const endYear = document.getElementById('bizLutBondEndYear');
                        const combinedYear = document.getElementById('bizLutBondYear');
                        if (!startYear || !endYear || !combinedYear) return;
                        combinedYear.value = startYear.value && endYear.value
                            ? `${startYear.value}-${endYear.value.slice(-2)}`
                            : '';
                    }

                    function populateBusinessLutEndYears(savedEndYear) {
                        const startYearField = document.getElementById('bizLutBondStartYear');
                        const endYearField = document.getElementById('bizLutBondEndYear');
                        const expiryField = document.getElementById('bizLutExpiry');
                        if (!startYearField || !endYearField || !expiryField) return;

                        const startYear = Number(startYearField.value);
                        endYearField.innerHTML = '';
                        if (!Number.isInteger(startYear) || startYear < 1000) {
                            endYearField.add(new Option('Select Start Year First', ''));
                            endYearField.disabled = true;
                            expiryField.removeAttribute('min');
                            syncBusinessLutBondYear();
                            return;
                        }

                        expiryField.min = `${startYear + 1}-01-01`;
                        if (expiryField.value && expiryField.value < expiryField.min) expiryField.value = '';
                        endYearField.add(new Option('Select End Year', ''));
                        for (let offset = 1; offset <= 5; offset += 1) {
                            const endYear = String(startYear + offset);
                            endYearField.add(new Option(endYear, endYear));
                        }
                        endYearField.disabled = false;
                        endYearField.value = savedEndYear && endYearField.querySelector(`option[value="${savedEndYear}"]`)
                            ? String(savedEndYear)
                            : String(startYear + 1);
                        syncBusinessLutBondYear();
                    }

                    // GST input formatting: auto-uppercase
                    const gstInput = document.getElementById('gstInput');
                    if (gstInput) {
                        gstInput.addEventListener('input', function(e) {
                            e.target.value = e.target.value.toUpperCase().replace(/[^0-9A-Z]/g, '').slice(0,
                                15);
                        });
                    }

                    // Aadhar input formatting: auto-insert spaces (XXXX XXXX XXXX)
                    const aadharInput = document.getElementById('aadharInput');
                    if (aadharInput) {
                        aadharInput.addEventListener('input', function(e) {
                            let value = e.target.value.replace(/\D/g, '').slice(0, 12);
                            // Format as XXXX XXXX XXXX
                            let formatted = value.match(/.{1,4}/g);
                            e.target.value = formatted ? formatted.join(' ') : '';
                        });
                    }

                    function verifyGst() {
                        const gstField = document.getElementById('gstInput');
                        const verifyBtn = document.getElementById('verifyGstBtn');
                        const gstStatus = document.getElementById('gstStatus');
                        const otpSection = document.getElementById('otpSection');

                        if (!gstField) return;

                        const gst = gstField.value.trim().toUpperCase();

                        if (!gst) {
                            alert('Please enter your GST number.');
                            return;
                        }
                        if (gst.length !== 15) {
                            alert('GST number must be exactly 15 characters.');
                            return;
                        }

                        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                        verifyBtn.disabled = true;

                        fetch('{{ route("customer.verify.gst") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    gst_number: gst
                                })
                            })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                }
                                return response.text().then(text => {
                                    console.error('Non-JSON GST response:', text);
                                    return {
                                        success: false,
                                        message: 'Server error (non-JSON response). Please try again.'
                                    };
                                });
                            })
                            .then(data => {
                                if (data.success) {
                                    kycData.gst_number = gst;
                                    kycData.gst_verified = true;
                                    saveKycDraft(getActiveKycStep());

                                    gstStatus.innerHTML = '<i class="fas fa-check-circle"></i> ' + (data.message ||
                                        'GST verified successfully!');
                                    gstStatus.style.display = 'block';
                                    gstStatus.style.color = '#10b981';

                                    verifyBtn.innerHTML = '<i class="fas fa-check"></i> Verified';
                                    verifyBtn.disabled = true;
                                    gstField.readOnly = true;

                                    // Reveal the OTP section now that GST is verified
                                    if (otpSection) {
                                        otpSection.style.display = 'block';
                                    }
                                } else {
                                    gstStatus.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message ||
                                        'GST verification failed.');
                                    gstStatus.style.display = 'block';
                                    gstStatus.style.color = '#dc3545';

                                    verifyBtn.innerHTML = 'Verify GST';
                                    verifyBtn.disabled = false;

                                    alert(data.message || 'GST verification failed. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('GST verify error:', error);
                                gstStatus.innerHTML = '<i class="fas fa-times-circle"></i> Connection error';
                                gstStatus.style.display = 'block';
                                gstStatus.style.color = '#dc3545';

                                verifyBtn.innerHTML = 'Verify GST';
                                verifyBtn.disabled = false;

                                alert('A network error occurred while verifying your GST. Please try again.');
                            });
                    }

                    function sendOTP() {
                        // Require GST verification before sending OTP
                        if (!kycData.gst_verified) {
                            alert('Please verify your GST number first.');
                            return;
                        }

                        const btn = document.getElementById('sendOtpBtn');
                        if (!btn) return;
                        const originalText = btn.innerHTML;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                        btn.disabled = true;

                        setTimeout(() => {
                            document.getElementById('otpStatus').style.display = 'block';
                            document.getElementById('otpContainer').style.display = 'block';
                            btn.innerHTML = 'Resend';
                            btn.disabled = false;
                        }, 1000);
                    }

                    function verifyAadhar() {
                        const aadharField = document.getElementById('aadharInput');
                        const verifyBtn = document.getElementById('verifyAadharBtn');
                        const aadharStatus = document.getElementById('aadharStatus');
                        const continueBtn = document.getElementById('aadharContinueBtn');

                        if (!aadharField) return;

                        // Strip spaces to get raw 12-digit number
                        const aadhar = aadharField.value.replace(/\s+/g, '');

                        if (!aadhar) {
                            alert('Please enter your Aadhar number.');
                            return;
                        }
                        if (aadhar.length !== 12) {
                            alert('Aadhar number must be 12 digits.');
                            return;
                        }

                        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                        verifyBtn.disabled = true;

                        fetch('{{ route("customer.verify.aadhar") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    aadhar_number: aadhar
                                })
                            })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                }
                                return response.text().then(text => {
                                    console.error('Non-JSON Aadhar response:', text);
                                    return {
                                        success: false,
                                        message: 'Server error (non-JSON response). Please try again.'
                                    };
                                });
                            })
                            .then(data => {
                                if (data.success) {
                                    kycData.aadhar_number = aadhar;
                                    kycData.aadhar_verified = true;
                                    saveKycDraft(getActiveKycStep());

                                    aadharStatus.innerHTML = '<i class="fas fa-check-circle"></i> ' + (data
                                        .message || 'Aadhar verified successfully!');
                                    aadharStatus.style.display = 'block';
                                    aadharStatus.style.color = '#10b981';

                                    verifyBtn.innerHTML = '<i class="fas fa-check"></i> Verified';
                                    verifyBtn.disabled = true;
                                    aadharField.readOnly = true;

                                    if (continueBtn) {
                                        continueBtn.classList.remove('btn-outline-custom');
                                    }
                                } else {
                                    aadharStatus.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data
                                        .message || 'Aadhar verification failed.');
                                    aadharStatus.style.display = 'block';
                                    aadharStatus.style.color = '#dc3545';

                                    verifyBtn.innerHTML = 'Verify Aadhar';
                                    verifyBtn.disabled = false;

                                    alert(data.message || 'Aadhar verification failed. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('Aadhar verify error:', error);
                                aadharStatus.innerHTML = '<i class="fas fa-times-circle"></i> Connection error';
                                aadharStatus.style.display = 'block';
                                aadharStatus.style.color = '#dc3545';

                                verifyBtn.innerHTML = 'Verify Aadhar';
                                verifyBtn.disabled = false;

                                alert('A network error occurred while verifying your Aadhar. Please try again.');
                            });
                    }

                    function verifyPan() {
                        const panField = document.getElementById('panInput');
                        const holderField = document.getElementById('panHolderName');
                        const dobField = document.getElementById('panDob');
                        const verifyBtn = document.getElementById('verifyPanBtn');
                        const panStatus = document.getElementById('panStatus');

                        if (!panField) return;

                        // Normalize PAN to uppercase, no spaces
                        const pan = panField.value.replace(/\s+/g, '').toUpperCase();

                        if (!pan) {
                            alert('Please enter your PAN number.');
                            return;
                        }
                        if (pan.length !== 10 || !/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan)) {
                            alert('Invalid PAN number. It must be 10 characters: 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).');
                            return;
                        }
                        if (holderField && !holderField.value.trim()) {
                            alert('Please enter the PAN holder name.');
                            return;
                        }
                        if (dobField && !dobField.value) {
                            alert('Please select your date of birth.');
                            return;
                        }

                        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                        verifyBtn.disabled = true;

                        fetch('{{ route("customer.verify.pan") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    pan_number: pan
                                })
                            })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                }
                                return response.text().then(text => {
                                    console.error('Non-JSON PAN response:', text);
                                    return {
                                        success: false,
                                        message: 'Server error (non-JSON response). Please try again.'
                                    };
                                });
                            })
                            .then(data => {
                                if (data.success) {
                                    kycData.pan_number = pan;
                                    kycData.pan_holder_name = holderField ? holderField.value.trim() : '';
                                    kycData.pan_dob = dobField ? dobField.value : '';
                                    kycData.pan_verified = true;
                                    saveKycDraft(getActiveKycStep());

                                    panStatus.innerHTML = '<i class="fas fa-check-circle"></i> ' + (data.message || 'PAN verified successfully!');
                                    panStatus.style.display = 'block';
                                    panStatus.style.color = '#10b981';

                                    verifyBtn.innerHTML = '<i class="fas fa-check"></i> Verified';
                                    verifyBtn.disabled = true;
                                    panField.readOnly = true;
                                    if (holderField) holderField.readOnly = true;
                                    if (dobField) dobField.readOnly = true;
                                } else {
                                    panStatus.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || 'PAN verification failed.');
                                    panStatus.style.display = 'block';
                                    panStatus.style.color = '#dc3545';

                                    verifyBtn.innerHTML = 'Verify PAN';
                                    verifyBtn.disabled = false;

                                    alert(data.message || 'PAN verification failed. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('PAN verify error:', error);
                                panStatus.innerHTML = '<i class="fas fa-times-circle"></i> Connection error';
                                panStatus.style.display = 'block';
                                panStatus.style.color = '#dc3545';

                                verifyBtn.innerHTML = 'Verify PAN';
                                verifyBtn.disabled = false;

                                alert('A network error occurred while verifying your PAN. Please try again.');
                            });
                    }

                    const imageOnlyKycInputIds = new Set([
                        'aadharFrontFileInput',
                        'aadharBackFileInput',
                        'panFileInput'
                    ]);

                    function validateImageOnlyKycFile(file, input) {
                        const extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
                        const allowedExtensions = ['jpg', 'jpeg', 'png'];
                        const allowedMimeTypes = ['image/jpeg', 'image/png'];
                        if (!allowedExtensions.includes(extension) || !allowedMimeTypes.includes(file.type)) {
                            alert('Only JPG, JPEG, or PNG images are allowed for GST, Aadhaar, and PAN documents.');
                            input.value = '';
                            return false;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            alert('The selected image must not exceed 5 MB.');
                            input.value = '';
                            return false;
                        }
                        return true;
                    }

                    function validatePdfOnlyKycFile(file, input) {
                        const extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
                        if (extension !== 'pdf' || file.type !== 'application/pdf') {
                            alert('Only a PDF file is allowed for the GST Certificate.');
                            input.value = '';
                            return false;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            alert('The GST Certificate PDF must not exceed 5 MB.');
                            input.value = '';
                            return false;
                        }
                        return true;
                    }

                    function validateBusinessSignatureFile(file, input) {
                        const extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
                        const allowedExtensions = ['jpg', 'jpeg', 'png'];
                        if (!allowedExtensions.includes(extension)) {
                            alert('Authorized Signature must be a JPG, JPEG, or PNG image.');
                            input.value = '';
                            return false;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            alert('Authorized Signature must not exceed 5 MB.');
                            input.value = '';
                            return false;
                        }
                        return true;
                    }

                    // File upload preview handlers for KYC documents.
                    function handleFilePreview(fileInputId, placeholderId, previewId, fileNameId, dataKey) {
                        const input = document.getElementById(fileInputId);
                        if (!input) return;
                        input.addEventListener('change', function () {
                            if (this.files && this.files[0]) {
                                const file = this.files[0];
                                if (fileInputId === 'bizGstCertFileInput' && !validatePdfOnlyKycFile(file, this)) {
                                    delete kycData[dataKey];
                                    return;
                                }
                                if (imageOnlyKycInputIds.has(fileInputId) && !validateImageOnlyKycFile(file, this)) {
                                    delete kycData[dataKey];
                                    return;
                                }
                                if (fileInputId === 'bizSignatureFileInput' && !validateBusinessSignatureFile(file, this)) {
                                    delete kycData[dataKey];
                                    return;
                                }
                                const placeholder = document.getElementById(placeholderId);
                                const preview = document.getElementById(previewId);
                                const fileNameEl = document.getElementById(fileNameId);
                                if (placeholder) placeholder.style.display = 'none';
                                if (preview) preview.style.display = 'block';
                                if (fileNameEl) fileNameEl.textContent = file.name;
                                kycData[dataKey] = file;
                                queueKycDraftSave();
                            }
                        });
                    }

                    function initFileUploadPreviews() {
                        handleFilePreview('aadharFrontFileInput', 'aadharFrontUploadPlaceholder', 'aadharFrontPreview', 'aadharFrontFileName', 'aadhar_front_file');
                        handleFilePreview('aadharBackFileInput', 'aadharBackUploadPlaceholder', 'aadharBackPreview', 'aadharBackFileName', 'aadhar_back_file');
                        handleFilePreview('panFileInput', 'panUploadPlaceholder', 'panPreview', 'panFileName', 'pan_file');
                        handleFilePreview('signatureFileInput', 'signatureUploadPlaceholder', 'signaturePreview', 'signatureFileName', 'signature_file');
                        // Business KYC document previews
                        handleFilePreview('bizGstCertFileInput', 'bizGstCertUploadPlaceholder', 'bizGstCertPreview', 'bizGstCertFileName', 'gst_certificate_file');
                        handleFilePreview('bizIecFileInput', 'bizIecUploadPlaceholder', 'bizIecPreview', 'bizIecFileName', 'iec_file');
                        handleFilePreview('bizAdCodeFileInput', 'bizAdCodeUploadPlaceholder', 'bizAdCodePreview', 'bizAdCodeFileName', 'ad_code_file');
                        handleFilePreview('bizLutFileInput', 'bizLutUploadPlaceholder', 'bizLutPreview', 'bizLutFileName', 'lut_file');
                    }

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initFileUploadPreviews);
                    } else {
                        initFileUploadPreviews();
                    }

                    // Validate that the current step is complete before allowing forward navigation
                    function validateStep(step) {
                        if (isBusinessFlow) {
                            // ===== BUSINESS FLOW (7 steps) =====
                            if (step === 1) {
                                // Step 1: Verify GST Certificate
                                if (!kycData.gst_certificate_verified) {
                                    alert('Please verify your GST Certificate number before continuing.');
                                    return false;
                                }
                                const gstFile = document.getElementById('bizGstCertFileInput');
                                if (!gstFile || !gstFile.files || !gstFile.files[0]) {
                                    alert('Please upload your GST Certificate document before continuing.');
                                    return false;
                                }
                            } else if (step === 2) {
                                // Step 2: Aadhaar is optional only for Courier / Aggregator.
                                const frontFile = document.getElementById('aadharFrontFileInput');
                                const backFile = document.getElementById('aadharBackFileInput');
                                const hasFrontFile = Boolean(frontFile && frontFile.files && frontFile.files[0]);
                                const hasBackFile = Boolean(backFile && backFile.files && backFile.files[0]);
                                const aadharInput = document.getElementById('aadharInput');
                                const hasAadhaarNumber = Boolean(aadharInput && aadharInput.value.replace(/\s+/g, ''));
                                const hasAnyAadhaarData = hasAadhaarNumber || kycData.aadhar_verified || hasFrontFile || hasBackFile;

                                if (!isAadhaarOptional || hasAnyAadhaarData) {
                                    if (!kycData.aadhar_verified) {
                                        alert(isAadhaarOptional
                                            ? 'Complete Aadhaar verification, or clear Aadhaar details to skip this optional step.'
                                            : 'Please verify your Aadhaar number before continuing.');
                                        return false;
                                    }
                                    if (!hasFrontFile) {
                                        alert('Please upload the front side of your Aadhaar before continuing.');
                                        return false;
                                    }
                                    if (!hasBackFile) {
                                        alert('Please upload the back side of your Aadhaar before continuing.');
                                        return false;
                                    }
                                }
                            } else if (step === 3) {
                                // Step 3: Verify PAN
                                if (!kycData.pan_verified) {
                                    alert('Please verify your PAN before continuing.');
                                    return false;
                                }
                                const panFile = document.getElementById('panFileInput');
                                if (!panFile || !panFile.files || !panFile.files[0]) {
                                    alert('Please upload your PAN card before continuing.');
                                    return false;
                                }
                            } else if (step === 4) {
                                // Step 4: validate every CSB-V field and upload before Continue.
                                const iecInput = document.getElementById('bizIecNumber');
                                const adCodeInput = document.getElementById('bizAdCode');
                                const lutStartYear = document.getElementById('bizLutBondStartYear');
                                const lutEndYear = document.getElementById('bizLutBondEndYear');
                                const lutExpiry = document.getElementById('bizLutExpiry');
                                const lutBondYear = document.getElementById('bizLutBondYear');
                                const bankType = document.getElementById('bizBankType');
                                const bankAccount = document.getElementById('bizBankAccount');
                                const billingGst = document.getElementById('bizBillingGst');
                                const billingContact = document.getElementById('bizBillingContact');
                                const billingEmail = document.getElementById('bizBillingEmail');
                                const billingAddress = document.getElementById('bizBillingAddress');
                                const iecFile = document.getElementById('bizIecFileInput');
                                const adCodeFile = document.getElementById('bizAdCodeFileInput');
                                const lutFile = document.getElementById('bizLutFileInput');
                                const allowedDocumentTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                                const fiveMb = 5 * 1024 * 1024;
                                const fail = (message, field) => {
                                    alert(message);
                                    if (field) {
                                        field.focus();
                                        if (field.type === 'file') {
                                            const area = field.closest('[id$="UploadArea"]');
                                            if (area) area.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        }
                                    }
                                    return false;
                                };
                                const validateFile = (input, label, allowedTypes, maxBytes) => {
                                    if (!input || !input.files || !input.files[0]) return fail(`Please upload your ${label}.`, input);
                                    const file = input.files[0];
                                    const extension = file.name.split('.').pop().toLowerCase();
                                    const extensionAllowed = allowedTypes.includes(file.type)
                                        || (allowedTypes.includes('application/pdf') && extension === 'pdf')
                                        || (allowedTypes.includes('image/jpeg') && ['jpg', 'jpeg'].includes(extension))
                                        || (allowedTypes.includes('image/png') && extension === 'png');
                                    if (!extensionAllowed) return fail(`${label} must be a PDF, JPG, JPEG or PNG file.`, input);
                                    if (file.size > maxBytes) return fail(`${label} must not exceed 5 MB.`, input);
                                    return true;
                                };

                                if (!iecInput || !/^[A-Z0-9]{10}$/.test(iecInput.value.trim().toUpperCase())) return fail('IEC Number must be exactly 10 letters or digits.', iecInput);
                                if (!validateFile(iecFile, 'IEC Certificate', allowedDocumentTypes, fiveMb)) return false;
                                if (!adCodeInput || !/^\d{14}$/.test(adCodeInput.value.trim())) return fail('AD Code must be exactly 14 numeric digits.', adCodeInput);
                                if (!validateFile(adCodeFile, 'AD Code Document', allowedDocumentTypes, fiveMb)) return false;
                                syncBusinessLutBondYear();
                                const startYear = Number(lutStartYear && lutStartYear.value);
                                const endYear = Number(lutEndYear && lutEndYear.value);
                                if (!startYear) return fail('Please select the LUT Bond Start Year.', lutStartYear);
                                if (!endYear) return fail('Please select the LUT Bond End Year.', lutEndYear);
                                if (endYear < startYear + 1 || endYear > startYear + 5) return fail('LUT Bond End Year must be within five years after the Start Year.', lutEndYear);
                                if (!lutExpiry || !lutExpiry.value) return fail('Please select the LUT Expiry Date.', lutExpiry);
                                const minimumExpiryDate = `${startYear + 1}-01-01`;
                                if (lutExpiry.value < minimumExpiryDate) return fail(`LUT Expiry Date must be on or after ${minimumExpiryDate}.`, lutExpiry);
                                if (!lutBondYear || !/^\d{4}-\d{2}$/.test(lutBondYear.value)) return fail('Please select valid LUT Bond Start and End Years.', lutStartYear);
                                if (!validateFile(lutFile, 'LUT Document', ['application/pdf'], fiveMb)) return false;
                                if (!bankType || !['private', 'government'].includes(bankType.value)) return fail('Please select your Bank Category.', bankType);
                                if (!bankAccount || !/^\d{9,18}$/.test(bankAccount.value.trim())) return fail('Bank Account Number must contain 9 to 18 digits.', bankAccount);
                                if (billingGst && billingGst.value.trim() && !/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(billingGst.value.trim().toUpperCase())) return fail('Billing GST Number must be a valid 15-character GSTIN.', billingGst);
                                if (!billingContact || !/^[6-9]\d{9}$/.test(billingContact.value.trim())) return fail('Billing Contact Number must be a valid 10-digit Indian mobile number.', billingContact);
                                if (!billingEmail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(billingEmail.value.trim())) return fail('Please enter a valid Billing Email address.', billingEmail);
                                if (!billingAddress || billingAddress.value.trim().length < 10) return fail('Billing Address must contain at least 10 characters.', billingAddress);
                                if (billingAddress.value.trim().length > 1000) return fail('Billing Address must not exceed 1000 characters.', billingAddress);
                            } else if (step === 5) {
                                // Step 5: Upload Signature
                                const sigFile = document.getElementById('bizSignatureFileInput');
                                const selectedSignature = sigFile && sigFile.files && sigFile.files[0];
                                if (!selectedSignature) {
                                    alert('Please upload your Authorized Signature.');
                                    return false;
                                }
                                if (!validateBusinessSignatureFile(selectedSignature, sigFile)) return false;
                            }
                        } else {
                            // ===== PERSONAL FLOW (7 steps) =====
                            // Step 1 (Complete KYC) is not validated - it has a "Skip For Now" option
                            if (step === 2) {
                                // Step 2: Verify Aadhar
                                if (!kycData.aadhar_verified) { alert('Please verify your Aadhar number before continuing.'); return false; }
                                const frontFile = document.getElementById('aadharFrontFileInput');
                                const backFile = document.getElementById('aadharBackFileInput');
                                if (!frontFile || !frontFile.files || !frontFile.files[0]) { alert('Please upload the front side of your Aadhaar.'); return false; }
                                if (!backFile || !backFile.files || !backFile.files[0]) { alert('Please upload the back side of your Aadhaar.'); return false; }
                            } else if (step === 3) {
                                // Step 3: Verify PAN
                                if (!kycData.pan_verified) { alert('Please verify your PAN before continuing.'); return false; }
                                const panFile = document.getElementById('panFileInput');
                                if (!panFile || !panFile.files || !panFile.files[0]) { alert('Please upload your PAN card.'); return false; }
                            } else if (step === 4) {
                                // Step 4: Business Details
                                const orgName = document.querySelector('#step4-content input[placeholder="Company Ltd"]');
                                const signatory = document.querySelector('#step4-content input[placeholder="Full Name"]');
                                if (!orgName || !orgName.value.trim()) { alert('Please enter your Organization Name.'); return false; }
                                if (!signatory || !signatory.value.trim()) { alert('Please enter the Authorized Signatory name.'); return false; }
                            } else if (step === 5) {
                                // Step 5: Upload Signature
                                const selectedSignature = signatureFileInput
                                    && signatureFileInput.files
                                    && signatureFileInput.files[0];
                                if (!selectedSignature) { alert('Please upload your signature before continuing.'); return false; }
                            }
                        }
                        return true;
                    }

                    function renderKycStep(stepNumber, shouldScroll) {
                        document.querySelectorAll('.step-content').forEach(content => content.classList.remove('active'));
                        const target = document.getElementById('step' + stepNumber + '-content');
                        if (target) target.classList.add('active');

                        document.querySelectorAll('.step-item').forEach((item, index) => {
                            const currentIdx = index + 1;
                            item.classList.remove('active', 'completed');
                            if (currentIdx < stepNumber) item.classList.add('completed');
                            else if (currentIdx === stepNumber) item.classList.add('active');
                        });

                        if (stepNumber === 6) {
                            const billSignatureImg = document.getElementById('billSignatureImg');
                            const billSignaturePlaceholder = document.getElementById('billSignaturePlaceholder');
                            const termsSignaturePreviewUrl = isBusinessFlow
                                ? businessSignaturePreviewUrl
                                : signaturePreviewUrl;
                            if (billSignatureImg) {
                                billSignatureImg.src = termsSignaturePreviewUrl || '';
                                billSignatureImg.style.display = termsSignaturePreviewUrl ? 'block' : 'none';
                            }
                            if (billSignaturePlaceholder) {
                                billSignaturePlaceholder.style.display = termsSignaturePreviewUrl ? 'none' : 'flex';
                            }
                        }

                        if (shouldScroll) window.scrollTo({ top: 0, behavior: 'smooth' });
                    }

                    function nextStep(stepNumber) {
                        // Determine the currently active step to detect forward vs backward navigation
                        const activeStepNum = getActiveKycStep();

                        // Only validate when moving FORWARD (target step > current step)
                        if (stepNumber > activeStepNum) {
                            if (!validateStep(activeStepNum)) {
                                return; // Block navigation - current step is incomplete
                            }
                        }

                        // Save data from current step before moving
                        if (isBusinessFlow) {
                            // ===== BUSINESS FLOW (7 steps) =====
                            if (stepNumber === 2) {
                                // Leaving step 1 (Verify GST Certificate) -> save GST certificate number
                                const gstCertInput = document.getElementById('bizGstCertNumber');
                                if (gstCertInput) kycData.gst_certificate_number = gstCertInput.value.trim().toUpperCase();
                            } else if (stepNumber === 5) {
                                // Leaving step 4 (CSB-V merged) -> save IEC + AD Code + LUT + Bank + Billing
                                const iecInput = document.getElementById('bizIecNumber');
                                const adCodeInput = document.getElementById('bizAdCode');
                                const lutExpiry = document.getElementById('bizLutExpiry');
                                const lutBondYear = document.getElementById('bizLutBondYear');
                                const bankType = document.getElementById('bizBankType');
                                const bankAccount = document.getElementById('bizBankAccount');
                                const billingGst = document.getElementById('bizBillingGst');
                                const billingContact = document.getElementById('bizBillingContact');
                                const billingEmail = document.getElementById('bizBillingEmail');
                                const billingAddress = document.getElementById('bizBillingAddress');
                                if (iecInput) kycData.iec_number = iecInput.value.trim();
                                if (adCodeInput) kycData.ad_code = adCodeInput.value.replace(/\D/g, '').slice(0, 14);
                                kycData.is_lut = true;
                                syncBusinessLutBondYear();
                                if (lutExpiry) kycData.lut_expiry_date = lutExpiry.value;
                                if (lutBondYear) kycData.lut_bond_year = lutBondYear.value.trim();
                                if (bankType) kycData.bank_type = bankType.value;
                                if (bankAccount) kycData.bank_account_number = bankAccount.value.trim();
                                if (billingGst) kycData.billing_gst = billingGst.value.trim().toUpperCase();
                                if (billingContact) kycData.billing_contact = billingContact.value.trim();
                                if (billingEmail) kycData.billing_email = billingEmail.value.trim();
                                if (billingAddress) kycData.billing_address = billingAddress.value.trim();
                            } else if (stepNumber === 7) {
                                // Leaving step 6 (Terms & Conditions) -> submit Business KYC
                                submitBusinessKYC();
                            }
                        } else {
                            // ===== PERSONAL FLOW (7 steps) =====
                            if (stepNumber === 2) {
                                // Save OTP verification
                                const otpInput = document.querySelector('#otpContainer input');
                                if (otpInput && otpInput.value.length === 6) {
                                    kycData.otp_verified = true;
                                }
                            } else if (stepNumber === 5) {
                                // Leaving step 4 (Business Details) -> save business details
                                const orgName = document.querySelector('#step4-content input[placeholder="Company Ltd"]');
                                const signatory = document.querySelector('#step4-content input[placeholder="Full Name"]');
                                if (orgName) kycData.organization_name = orgName.value;
                                if (signatory) kycData.authorized_signatory = signatory.value;
                            } else if (stepNumber === 7) {
                                // Leaving step 6 (Bill) -> submit KYC data
                                submitKYC();
                            }
                        }

                        captureKycDraftData();
                        renderKycStep(stepNumber, true);
                        // Terminal steps submit final KYC and the controller removes the draft on success.
                        if (stepNumber < totalSteps) saveKycDraft(stepNumber);
                    }

                    // ===== Business KYC: Verify GST Certificate =====
                    function verifyGstBiz() {
                        const gstField = document.getElementById('bizGstCertNumber');
                        const gstFileInput = document.getElementById('bizGstCertFileInput');
                        const verifyBtn = document.getElementById('bizVerifyGstCertBtn');
                        const statusDiv = document.getElementById('bizGstStatus');
                        const continueBtn = document.getElementById('bizGstContinueBtn');

                        if (!gstField || !verifyBtn) return;

                        const gstValue = gstField.value.trim().toUpperCase();

                        if (gstValue.length !== 15) {
                            alert('Please enter a valid 15-character GST number.');
                            return;
                        }
                        if (!gstFileInput || !gstFileInput.files || !gstFileInput.files[0]) {
                            alert('Please upload the GST Certificate PDF before verification.');
                            return;
                        }
                        if (!validatePdfOnlyKycFile(gstFileInput.files[0], gstFileInput)) {
                            return;
                        }

                        const verifyData = new FormData();
                        verifyData.append('gst_number', gstValue);
                        verifyData.append('gst_certificate_document', gstFileInput.files[0]);

                        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                        verifyBtn.disabled = true;

                        fetch('{{ route("customer.verify.gst") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: verifyData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    kycData.gst_certificate_number = gstValue;
                                    kycData.gst_certificate_verified = true;
                                    kycData.gst_number = gstValue;
                                    kycData.gst_verified = true;
                                    saveKycDraft(getActiveKycStep());

                                    if (statusDiv) {
                                        statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> GST Certificate verified successfully!';
                                        statusDiv.style.display = 'block';
                                        statusDiv.style.color = '#10b981';
                                    }

                                    // Auto-fill billing GST number
                                    const billingGst = document.getElementById('bizBillingGst');
                                    if (billingGst) billingGst.value = gstValue;

                                    verifyBtn.innerHTML = 'Verified';
                                    verifyBtn.disabled = true;
                                    if (continueBtn) continueBtn.focus();
                                } else {
                                    if (statusDiv) {
                                        statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || 'GST verification failed.');
                                        statusDiv.style.display = 'block';
                                        statusDiv.style.color = '#dc3545';
                                    }
                                    verifyBtn.innerHTML = 'Verify GST';
                                    verifyBtn.disabled = false;
                                    alert(data.message || 'GST verification failed. Please check the number and try again.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                if (statusDiv) {
                                    statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Connection error';
                                    statusDiv.style.display = 'block';
                                    statusDiv.style.color = '#dc3545';
                                }
                                verifyBtn.innerHTML = 'Verify GST';
                                verifyBtn.disabled = false;
                                alert('A network error occurred while verifying your GST. Please try again.');
                            });
                    }

                    function restoreVerifiedState(fieldId, buttonId, statusId, verified, message) {
                        if (!verified) return;
                        const field = document.getElementById(fieldId);
                        const button = document.getElementById(buttonId);
                        const status = document.getElementById(statusId);
                        if (field) field.readOnly = true;
                        if (button) {
                            button.innerHTML = '<i class="fas fa-check"></i> Verified';
                            button.disabled = true;
                        }
                        if (status) {
                            status.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
                            status.style.display = 'block';
                            status.style.color = '#10b981';
                        }
                    }

                    function restoreKycDraft() {
                        const values = {
                            gstInput: kycData.gst_number,
                            aadharInput: kycData.aadhar_number ? String(kycData.aadhar_number).replace(/(.{4})(?=.)/g, '$1 ') : '',
                            panInput: kycData.pan_number,
                            panHolderName: kycData.pan_holder_name,
                            panDob: kycData.pan_dob,
                            bizGstCertNumber: kycData.gst_certificate_number,
                            bizIecNumber: kycData.iec_number,
                            bizAdCode: kycData.ad_code,
                            bizLutExpiry: kycData.lut_expiry_date,
                            bizBankType: kycData.bank_type,
                            bizBankAccount: kycData.bank_account_number,
                            bizBillingGst: kycData.billing_gst || kycData.gst_number,
                            bizBillingContact: kycData.billing_contact,
                            bizBillingEmail: kycData.billing_email,
                            bizBillingAddress: kycData.billing_address
                        };
                        Object.keys(values).forEach(function(id) {
                            const field = document.getElementById(id);
                            if (field && values[id] !== undefined && values[id] !== null) field.value = values[id];
                        });

                        kycData.is_lut = true;
                        const bondYearMatch = String(kycData.lut_bond_year || '').match(/^(\d{4})-(\d{2})$/);
                        const startYearField = document.getElementById('bizLutBondStartYear');
                        if (bondYearMatch && startYearField) {
                            const startYear = Number(bondYearMatch[1]);
                            let endYear = (Math.floor(startYear / 100) * 100) + Number(bondYearMatch[2]);
                            if (endYear <= startYear) endYear += 100;
                            if (!startYearField.querySelector(`option[value="${startYear}"]`)) {
                                startYearField.add(new Option(String(startYear), String(startYear)));
                            }
                            startYearField.value = String(startYear);
                            populateBusinessLutEndYears(String(endYear));
                        } else {
                            populateBusinessLutEndYears('');
                        }

                        const organization = document.querySelector('#step4-content input[placeholder="Company Ltd"]');
                        const signatory = document.querySelector('#step4-content input[placeholder="Full Name"]');
                        if (!isBusinessFlow && organization) organization.value = kycData.organization_name || '';
                        if (!isBusinessFlow && signatory) signatory.value = kycData.authorized_signatory || '';

                        // File inputs cannot be restored from a JSON draft. Remove any
                        // legacy base64 signature values and require file reselection.
                        delete kycData.signature;
                        delete kycData.business_signature;

                        restoreVerifiedState('gstInput', 'verifyGstBtn', 'gstStatus', kycData.gst_verified,
                            'GST verification restored from your saved KYC.');
                        restoreVerifiedState('aadharInput', 'verifyAadharBtn', 'aadharStatus', kycData.aadhar_verified,
                            'Aadhar verification restored from your saved KYC.');
                        restoreVerifiedState('panInput', 'verifyPanBtn', 'panStatus', kycData.pan_verified,
                            'PAN verification restored from your saved KYC.');
                        restoreVerifiedState('bizGstCertNumber', 'bizVerifyGstCertBtn', 'bizGstStatus',
                            kycData.gst_certificate_verified, 'GST Certificate verification restored from your saved KYC.');
                        if (kycData.pan_verified) {
                            const holder = document.getElementById('panHolderName');
                            const dob = document.getElementById('panDob');
                            if (holder) holder.readOnly = true;
                            if (dob) dob.readOnly = true;
                        }
                        if (kycData.gst_verified) {
                            const otpSection = document.getElementById('otpSection');
                            if (otpSection) otpSection.style.display = 'block';
                        }

                        renderKycStep(savedKycStep, false);
                    }

                    function initKycDraftAutosave() {
                        const lutStartYear = document.getElementById('bizLutBondStartYear');
                        const lutEndYear = document.getElementById('bizLutBondEndYear');
                        if (lutStartYear) {
                            lutStartYear.addEventListener('change', function() {
                                populateBusinessLutEndYears('');
                            });
                        }
                        if (lutEndYear) lutEndYear.addEventListener('change', syncBusinessLutBondYear);

                        document.querySelectorAll('.step-content input:not([type="file"]), .step-content select, .step-content textarea')
                            .forEach(function(field) {
                                field.addEventListener(field.tagName === 'SELECT' ? 'change' : 'input', function() {
                                    queueKycDraftSave(getActiveKycStep());
                                });
                            });
                        restoreKycDraft();
                    }

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initKycDraftAutosave);
                    } else {
                        initKycDraftAutosave();
                    }

                    // ===== Business KYC: Submit via FormData (file uploads) =====
                    function submitBusinessKYC() {
                        const submitBtn = document.querySelector('#step7-content button');
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                            submitBtn.disabled = true;
                        }

                        // Build FormData for file uploads
                        const formData = new FormData();
                        formData.append('is_csb_v', kycData.is_csb_v ? 1 : 0);
                        formData.append('is_gst', kycData.is_gst ? 1 : 0);
                        formData.append('is_lut', 1);
                        formData.append('gst_certificate_number', kycData.gst_certificate_number || kycData.gst_number || '');
                        formData.append('pan_number', kycData.pan_number || '');
                        formData.append('pan_holder_name', kycData.pan_holder_name || '');
                        formData.append('pan_dob', kycData.pan_dob || '');
                        formData.append('iec_number', kycData.iec_number || '');
                        formData.append('ad_code', kycData.ad_code || '');
                        formData.append('lut_expiry_date', kycData.lut_expiry_date || '');
                        formData.append('lut_bond_year', kycData.lut_bond_year || '');
                        formData.append('bank_account_number', kycData.bank_account_number || '');
                        formData.append('bank_type', kycData.bank_type || '');
                        formData.append('aadhar_number', (kycData.aadhar_number || '').replace(/\s/g, ''));
                        formData.append('billing_address', kycData.billing_address || '');
                        formData.append('billing_gst', kycData.billing_gst || kycData.gst_number || '');
                        formData.append('billing_contact', kycData.billing_contact || '');
                        formData.append('billing_email', kycData.billing_email || '');
                        formData.append('terms_accepted', kycData.terms_accepted ? 1 : 0);

                        // Append every Business KYC document using the field names
                        // expected by the controller. The GST certificate is stored in
                        // both CSB GST path columns for backwards compatibility.
                        const fileFields = {
                            'gst_certificate_document': 'bizGstCertFileInput',
                            'gst_document': 'bizGstCertFileInput',
                            'iec_document': 'bizIecFileInput',
                            'ad_code_document': 'bizAdCodeFileInput',
                            'lut_document': 'bizLutFileInput',
                            'aadhar_document': 'aadharFrontFileInput',
                            'aadhar_front_document': 'aadharFrontFileInput',
                            'aadhar_back_document': 'aadharBackFileInput',
                            'pan_document': 'panFileInput',
                            'signature_document': 'bizSignatureFileInput'
                        };

                        Object.keys(fileFields).forEach(function(fieldName) {
                            const input = document.getElementById(fileFields[fieldName]);
                            if (input && input.files && input.files[0]) {
                                formData.append(fieldName, input.files[0]);
                            }
                        });

                        fetch('{{ route("customer.csb5-form.store") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const messageDiv = document.querySelector('#step7-content p.text-muted');
                                    if (messageDiv) {
                                        messageDiv.innerHTML = '<strong>' + data.message + '</strong>';
                                        messageDiv.className = 'text-success mx-auto';
                                    }
                                    if (submitBtn) {
                                        submitBtn.innerHTML = 'Go to Dashboard';
                                        submitBtn.onclick = function() {
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                location.reload();
                                            }
                                        };
                                    }
                                } else {
                                    const validationErrors = data.errors
                                        ? Object.values(data.errors).flat().join('\n')
                                        : '';
                                    alert('Error: ' + (validationErrors || data.message || 'Unknown error'));
                                    if (submitBtn) {
                                        submitBtn.innerHTML = 'Go to Dashboard';
                                        submitBtn.disabled = false;
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while submitting your Business KYC application.');
                                if (submitBtn) {
                                    submitBtn.innerHTML = 'Go to Dashboard';
                                    submitBtn.disabled = false;
                                }
                            });
                    }

                    function submitKYC() {
                        // Show loading state
                        const submitBtn = document.querySelector('#step7-content button');
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                            submitBtn.disabled = true;
                        }

                        // Build FormData so that File objects are transmitted correctly
                        const formData = new FormData();
                        // Append all text fields (skip File objects and legacy signature previews)
                        Object.keys(kycData).forEach(function (key) {
                            const value = kycData[key];
                            if (value === null || value === undefined) return;
                            if (key === 'signature' || key === 'business_signature') return;
                            if (value instanceof File) return; // handled below
                            // Convert booleans to 1/0 (FormData stringifies, and Laravel's
                            // boolean rule rejects the string "false")
                            if (typeof value === 'boolean') {
                                formData.append(key, value ? 1 : 0);
                            } else {
                                formData.append(key, value);
                            }
                        });
                        // Append file uploads (if present)
                        if (kycData.aadhar_front_file instanceof File) {
                            formData.append('aadhar_front_document', kycData.aadhar_front_file);
                        }
                        if (kycData.aadhar_back_file instanceof File) {
                            formData.append('aadhar_back_document', kycData.aadhar_back_file);
                        }
                        if (kycData.pan_file instanceof File) {
                            formData.append('pan_document', kycData.pan_file);
                        }
                        if (kycData.signature_file instanceof File) {
                            formData.append('signature_document', kycData.signature_file);
                        }

                        // Submit via AJAX (multipart/form-data — do NOT set Content-Type manually)
                        fetch('{{ route("customer.kyc.submit") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show success message
                                    const messageDiv = document.querySelector('#step7-content p.text-muted');
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
                                                <p class="text-success mb-0 fs-13"><span class="text-body">Done</span>
                                                </p>
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
                                                <p class="text-success mb-0 fs-13"><span class="text-body ms-1">Book
                                                        your
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
                                                <h2 class="mb-1 fs-16">{{ $totalBooked }}</h2>
                                                @if($bookedChangePercent > 0)
                                                <p class="text-success mb-0 fs-13"> <i
                                                        class="ti ti-arrow-bar-up me-1"></i>{{ $bookedChangePercent }}%<span
                                                        class="text-body ms-1">from last month</span></p>
                                                @elseif($bookedChangePercent < 0) <p class="text-danger mb-0 fs-13"> <i
                                                        class="ti ti-arrow-bar-down me-1"></i>{{ abs($bookedChangePercent) }}%<span
                                                        class="text-body ms-1">from last month</span></p>
                                                    @else
                                                    <p class="text-muted mb-0 fs-13">No change from last month</p>
                                                    @endif
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
                                                <h2 class="mb-1 fs-16">{{ $pickupPending }}</h2>
                                                @if($pickupPendingChangePercent > 0)
                                                <p class="text-success mb-0 fs-13"> <i
                                                        class="ti ti-arrow-bar-up me-1"></i>{{ $pickupPendingChangePercent }}%<span
                                                        class="text-body ms-1">from last month</span></p>
                                                @elseif($pickupPendingChangePercent < 0) <p
                                                    class="text-danger mb-0 fs-13"> <i
                                                        class="ti ti-arrow-bar-down me-1"></i>{{ abs($pickupPendingChangePercent) }}%<span
                                                        class="text-body ms-1">from last month</span></p>
                                                    @else
                                                    <p class="text-muted mb-0 fs-13">No change from last month</p>
                                                    @endif
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
                                                <h2 class="mb-1 fs-16">{{ $outForDelivery }}</h2>
                                                @if($outForDeliveryChangePercent > 0)
                                                <p class="text-success mb-0 fs-13"> <i
                                                        class="ti ti-arrow-bar-up me-1"></i>{{ $outForDeliveryChangePercent }}%<span
                                                        class="text-body ms-1">from last month</span></p>
                                                @elseif($outForDeliveryChangePercent < 0) <p
                                                    class="text-danger mb-0 fs-13"> <i
                                                        class="ti ti-arrow-bar-down me-1"></i>{{ abs($outForDeliveryChangePercent) }}%<span
                                                        class="text-body ms-1">from last month</span></p>
                                                    @else
                                                    <p class="text-muted mb-0 fs-13">No change from last month</p>
                                                    @endif
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
                                                <h2 class="mb-1 fs-16">{{ $delivered }}</h2>
                                                @if($deliveredChangePercent > 0)
                                                <p class="text-success mb-0 fs-13"> <i
                                                        class="ti ti-arrow-bar-up me-1"></i>{{ $deliveredChangePercent }}%<span
                                                        class="text-body ms-1">from last month</span></p>
                                                @elseif($deliveredChangePercent < 0) <p class="text-danger mb-0 fs-13">
                                                    <i
                                                        class="ti ti-arrow-bar-down me-1"></i>{{ abs($deliveredChangePercent) }}%<span
                                                        class="text-body ms-1">from last month</span></p>
                                                    @else
                                                    <p class="text-muted mb-0 fs-13">No change from last month</p>
                                                    @endif
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

                    <!-- Shipment Analytics Section -->
                    <h6 class="mb-2 mt-4">Shipment Analytics</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button class="chart-filter-btn active" data-filter="this_month"
                            onclick="loadChartData('this_month', this)">This Month</button>
                        <button class="chart-filter-btn" data-filter="today"
                            onclick="loadChartData('today', this)">Today</button>
                        <button class="chart-filter-btn" data-filter="yesterday"
                            onclick="loadChartData('yesterday', this)">Yesterday</button>
                        <button class="chart-filter-btn" data-filter="last_month"
                            onclick="loadChartData('last_month', this)">Last Month</button>
                        <button class="chart-filter-btn" data-filter="last_year"
                            onclick="loadChartData('last_year', this)">Last Year</button>
                    </div>

                    <div class="row row-gap-3 mb-4">
                        <!-- Status Breakdown Doughnut Chart -->
                        <div class="col-xl-5 col-sm-12 d-flex">
                            <div class="chart-card flex-fill">
                                <h6>Status Breakdown</h6>
                                <div style="position: relative; max-height: 320px;">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Date-wise Shipment Trend Bar Chart -->
                        <div class="col-xl-7 col-sm-12 d-flex">
                            <div class="chart-card flex-fill">
                                <h6>Shipment Trends</h6>
                                <div style="position: relative; max-height: 320px;">
                                    <canvas id="trendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Shipment Analytics Section -->

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
                                                <p class="text-danger mb-0 fs-13"><span class="text-body">Unlock
                                                        commercial
                                                        exports with CSB V compliance</span></p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center">
                                            @if($customer->csb_status === 2)
                                            <span class="text-success"><i
                                                    class="fa-solid fa-check-circle fs-20"></i></span>
                                            @else
                                            <a href="/customer/csb5-form" class="text-success"
                                                style="font-weight: 500;">Enable Now <i
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
                                            class="badge bg-soft-dark ms-2 text-dark fs-12">{{ $recentShipments->count() }}
                                            Orders</span></h5>
                                    <a href="{{ route('customer.view-all-shipments') }}"
                                        class="btn btn-md btn-primary d-flex align-items-center"><i
                                            class="ti ti-eye me-2"></i>View All</a>
                                </div>
                                <div class="card-body p-0">

                                    <div class="table-responsive">
                                        <table class="table table-nowrap border">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>
                                                        <div class="form-check form-check-md">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="select-all">
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
                                                @foreach($recentShipments as $shipment)
                                                @php
                                                $consignee = $shipment->consigneeInfo;
                                                $packages = $shipment->packageDimensions;
                                                $pcs = $packages ? $packages->count() : 0;
                                                $chgWeight = $packages ? $packages->max('chargeable_weight') : 0;
                                                $statusBadgeClass = [
                                                'draft' => 'badge-soft-warning',
                                                'ready' => 'badge-soft-info',
                                                'assigned_for_pickup' => 'badge-soft-primary',
                                                'packed' => 'badge-soft-warning',
                                                'manifested' => 'badge-soft-purple',
                                                'dispatched' => 'badge-soft-info',
                                                'ready_to_dispatch' => 'badge-soft-warning',
                                                'delivered' => 'badge-soft-success',
                                                'cancelled' => 'badge-soft-danger',
                                                'disputed' => 'badge-soft-danger',
                                                'on_hold' => 'badge-soft-secondary',
                                                'received' => 'badge-soft-success',
                                                ];
                                                $badgeClass = $statusBadgeClass[$shipment->status] ??
                                                'badge-soft-secondary';
                                                $statusTitleMap = \App\Models\Tracking::getStatusTitleMap();
                                                $statusLabel = $statusTitleMap[$shipment->status] ??
                                                ucfirst($shipment->status);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="form-check form-check-md"><input
                                                                class="form-check-input" type="checkbox"></div>
                                                    </td>

                                                    <td>
                                                        <a
                                                            href="{{ route('customer.view-all-shipments') }}">{{ $shipment->awb_number ?? 'N/A' }}</a>
                                                    </td>

                                                    <td>{{ $shipment->created_at ? $shipment->created_at->format('d M Y') : '-' }}
                                                    </td>
                                                    <td>{{ $consignee ? ($consignee->delivery_destination ?? $consignee->city ?? '-') : '-' }}
                                                    </td>
                                                    <td>{{ $shipment->shipping_method ?? '-' }}</td>
                                                    <td>{{ $shipment->shipping_method ?? '-' }}</td>
                                                    <td>{{ $shipment->awb_number ?? '-' }}</td>
                                                    <td>{{ $pcs }}</td>
                                                    <td>{{ number_format($chgWeight, 3) }}</td>
                                                    <td>
                                                        <div class="d-inline-flex align-items-center">
                                                            <a href="{{ route('customer.view-all-shipments') }}"
                                                                class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                                                    class="ti ti-eye"></i></a>
                                                            <span class="badge {{ $badgeClass }}">
                                                                {{ $statusLabel }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach

                                                @if($recentShipments->count() === 0)
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted py-4">No shipments
                                                        found. Create your first shipment to see data here.</td>
                                                </tr>
                                                @endif
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
                                                <h2 class="mb-1 fs-16">₹ {{ number_format($walletBalance, 2) }}</h2>
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
                                                <h2 class="mb-1 fs-16">₹ {{ number_format($totalShippedValue, 2) }}</h2>
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
                                                <h2 class="mb-1 fs-16">₹ {{ number_format($totalShippedCost, 2) }}</h2>
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
                    <div
                        class="d-flex align-items-center gap-2 footer-links justify-content-center justify-content-md-end">
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
        <script src="{{ asset('assets/plugins/choices.js/public/assets/scripts/choices.min.js') }}"
            type="text/javascript">
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

        <!-- Dashboard Charts Script -->
        <script>
        let statusChart = null;
        let trendChart = null;

        // Color palette for status chart
        const statusColors = {
            draft: '#6c757d',
            ready: '#0d6efd',
            assigned_for_pickup: '#198754',
            packed: '#fd7e14',
            manifested: '#6610f2',
            dispatched: '#20c997',
            ready_to_dispatch: '#ffc107',
            delivered: '#0dcaf0',
            cancelled: '#dc3545',
            disputed: '#e83e8c',
            on_hold: '#495057',
            received: '#17a2b8'
        };

        function loadChartData(filter, btnElement) {
            // Update active button
            document.querySelectorAll('.chart-filter-btn').forEach(btn => btn.classList.remove('active'));
            if (btnElement) btnElement.classList.add('active');

            fetch('{{ route("customer.dashboard-chart-data") }}?filter=' + filter, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderStatusChart(data.statusCounts, data.statusMap);
                        renderTrendChart(data.dateWiseCounts, data.filter);
                    }
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                });
        }

        function renderStatusChart(statusCounts, statusMap) {
            const labels = [];
            const values = [];
            const colors = [];

            for (const [status, count] of Object.entries(statusCounts)) {
                labels.push(statusMap[status] || status);
                values.push(count);
                colors.push(statusColors[status] || '#adb5bd');
            }

            if (statusChart) {
                statusChart.destroy();
            }

            const ctx = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 16,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '55%'
                }
            });
        }

        function renderTrendChart(dateWiseCounts, filter) {
            const labels = Object.keys(dateWiseCounts);
            const values = Object.values(dateWiseCounts);

            // Format labels for display
            const displayLabels = labels.map(label => {
                if (filter === 'last_year') {
                    // Format "2025-01" as "Jan 2025"
                    const [year, month] = label.split('-');
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct',
                        'Nov', 'Dec'
                    ];
                    return monthNames[parseInt(month) - 1] + ' ' + year;
                } else {
                    // Format "2025-06-15" as "15 Jun"
                    const parts = label.split('-');
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct',
                        'Nov', 'Dec'
                    ];
                    return parseInt(parts[2]) + ' ' + monthNames[parseInt(parts[1]) - 1];
                }
            });

            if (trendChart) {
                trendChart.destroy();
            }

            const ctx = document.getElementById('trendChart').getContext('2d');
            trendChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: displayLabels,
                    datasets: [{
                        label: 'Shipments Created',
                        data: values,
                        backgroundColor: 'rgba(91, 94, 255, 0.7)',
                        borderColor: '#5b5eff',
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Shipments: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                },
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Load default chart data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadChartData('this_month', document.querySelector('.chart-filter-btn[data-filter="this_month"]'));
        });
        </script>

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