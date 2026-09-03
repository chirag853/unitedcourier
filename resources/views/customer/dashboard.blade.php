<!DOCTYPE html>
<html lang="en">


<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Customer Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
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
        <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">


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

    .input-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important;
        background-color: #fff5f5 !important;
    }

    .csbv-upload-area {
        min-height: 132px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .csbv-upload-area > div:not([style*="display: none"]) {
        width: 100%;
        min-width: 0;
    }

    .csbv-upload-filename {
        width: 100%;
        max-width: 100%;
        padding: 0 4px;
        line-height: 1.4;
        overflow-wrap: anywhere;
        word-break: break-word;
        white-space: normal;
    }

    @media (max-width: 767.98px) {
        .csbv-upload-area {
            min-height: 120px;
        }
    }

    .kyc-alert-list {
        list-style: none;
        padding: 0;
        margin: 12px 0 0;
    }
    .kyc-alert-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 10px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
    }
    .kyc-alert-list li i {
        margin-top: 2px;
        color: #ef4444;
    }

    /* ===== KYC Verification Progress (pending / under review / rejected) ===== */
    html {
        scroll-behavior: smooth;
    }

    #kycFormSection {
        scroll-margin-top: 130px;
    }

    .kyc-progress-title {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a1a;
        letter-spacing: -0.02em;
        margin: 0 0 4px;
    }

    .kyc-progress-sub {
        font-size: 15px;
        color: #8a94a6;
        margin: 0 0 20px;
    }

    .kyc-progress-card {
        background: #ffffff;
        border: 1px solid rgba(211, 197, 172, 0.25);
        border-radius: 24px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 0 3px rgba(0, 0, 0, 0.02);
        padding: 28px 24px 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .kyc-progress-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: linear-gradient(to right, #2563eb, #9333ea);
        opacity: 0.85;
    }

    .kyc-stepper {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        max-width: 720px;
        margin: 20px auto 24px;
    }

    .kyc-stepper-track,
    .kyc-stepper-track-fill {
        position: absolute;
        top: 21px;
        height: 6px;
        border-radius: 999px;
        z-index: 0;
    }

    .kyc-stepper-track {
        left: 16.66%;
        right: 16.66%;
        background: #eff4ff;
    }

    .kyc-stepper-track-fill {
        left: 16.66%;
        background: linear-gradient(to right, #fbbf24, #d97706);
        transition: width 0.4s ease;
    }

    .kyc-step {
        position: relative;
        z-index: 1;
        flex: 1 1 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        text-align: center;
    }

    .kyc-step-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        background: #ffffff;
        border: 2px solid #e8edf5;
        color: #9aa4b2;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        transition: all 0.25s ease;
    }

    .kyc-step-num {
        font-size: 15px;
        font-weight: 600;
        opacity: 0.55;
    }

    .kyc-step.is-active .kyc-step-icon {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        border-color: #ffffff;
        color: #4a3200;
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.35);
    }

    .kyc-step.is-done .kyc-step-icon {
        background: linear-gradient(135deg, #34d399, #10b981);
        border-color: #ffffff;
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
    }

    .kyc-step.is-error .kyc-step-icon {
        background: linear-gradient(135deg, #f87171, #dc2626);
        border-color: #ffffff;
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3);
    }

    .kyc-step-label {
        font-size: 14px;
        font-weight: 600;
        color: #8a94a6;
    }

    .kyc-step.is-active .kyc-step-label {
        color: #b45309;
        font-weight: 700;
    }

    .kyc-step.is-done .kyc-step-label {
        color: #059669;
        font-weight: 700;
    }

    .kyc-step.is-error .kyc-step-label {
        color: #dc2626;
        font-weight: 700;
    }

    .kyc-status-banner {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
        background: linear-gradient(to right, rgba(239, 244, 255, 0.55), #ffffff);
        border: 1px solid rgba(251, 191, 36, 0.4);
        border-radius: 16px;
        padding: 20px 22px;
    }

    .kyc-status-banner.review {
        border-color: rgba(37, 99, 235, 0.25);
        background: linear-gradient(to right, rgba(239, 246, 255, 0.6), #ffffff);
    }

    .kyc-status-banner.rejected {
        border-color: rgba(220, 38, 38, 0.25);
        background: linear-gradient(to right, rgba(254, 242, 242, 0.6), #ffffff);
    }

    .kyc-status-banner-icon {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: rgba(251, 191, 36, 0.18);
        color: #b45309;
    }

    .kyc-status-banner.review .kyc-status-banner-icon {
        background: rgba(37, 99, 235, 0.12);
        color: #2563eb;
    }

    .kyc-status-banner.rejected .kyc-status-banner-icon {
        background: rgba(220, 38, 38, 0.12);
        color: #dc2626;
    }

    .kyc-status-banner-body {
        flex: 1 1 auto;
    }

    .kyc-status-banner-title {
        font-size: 17px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 4px;
    }

    .kyc-status-banner-text {
        font-size: 14px;
        color: #5b6472;
        line-height: 1.65;
        margin: 0;
    }

    .kyc-reject-reason {
        margin-top: 10px;
        padding: 10px 14px;
        border-radius: 10px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 13px;
        font-weight: 500;
    }

    .kyc-status-banner-action {
        flex-shrink: 0;
        display: inline-block;
        border: none;
        border-radius: 12px;
        padding: 12px 26px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        background: #f59e0b;
        color: #ffffff;
        box-shadow: 0 2px 10px rgba(245, 158, 11, 0.4);
        transition: all 0.2s ease;
    }

    .kyc-status-banner-action:hover {
        opacity: 0.92;
        transform: translateY(-1px);
        color: #ffffff;
        text-decoration: none;
    }

    .kyc-status-banner.review .kyc-status-banner-action {
        background: #2563eb;
        box-shadow: 0 2px 10px rgba(37, 99, 235, 0.35);
    }

    .kyc-status-banner.rejected .kyc-status-banner-action {
        background: #dc2626;
        box-shadow: 0 2px 10px rgba(220, 38, 38, 0.35);
    }

    .kyc-restricted-panel {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 12px;
        padding: 56px 24px;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 24px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }

    .kyc-restricted-icon {
        width: 112px;
        height: 112px;
        border-radius: 34px;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        border: 1px solid rgba(203, 213, 225, 0.6);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 46px;
        color: #94a3b8;
        position: relative;
        margin-bottom: 6px;
    }

    .kyc-restricted-icon::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 34px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.45), transparent);
        pointer-events: none;
    }

    .kyc-restricted-title {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a1a;
        letter-spacing: -0.02em;
        margin: 0;
    }

    .kyc-restricted-text {
        font-size: 15px;
        color: #64748b;
        line-height: 1.7;
        max-width: 480px;
        margin: 0;
    }

    .kyc-restricted-link {
        margin-top: 10px;
        display: inline-block;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 26px;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .kyc-restricted-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
        text-decoration: none;
    }

    @media (min-width: 768px) {
        .kyc-status-banner {
            flex-direction: row;
            align-items: center;
        }
    }

    @media (max-width: 575.98px) {
        .kyc-progress-card {
            padding: 22px 14px 18px;
        }

        .kyc-progress-title {
            font-size: 20px;
        }

        .kyc-step-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            font-size: 16px;
        }

        .kyc-stepper-track,
        .kyc-stepper-track-fill {
            top: 18px;
        }

        .kyc-step-label {
            font-size: 12px;
        }

        .kyc-status-banner-action {
            width: 100%;
            text-align: center;
        }
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
                @php
                    $kycStatus = $kycRecord->kyc_status;
                    $kycIsPending = $kycStatus === 'pending';
                    $kycIsReview = $kycStatus === 'under_review';
                    $kycIsRejected = $kycStatus === 'rejected';
                    $kycRejectReason = trim((string) data_get($kycDraft?->form_data, 'reject_remark', ''));
                @endphp
                <!-- KYC Verification Progress -->
                <h5 class="kyc-progress-title">KYC Verification Progress</h5>
                <p class="kyc-progress-sub">Track the status of your account approval.</p>

                <div class="kyc-progress-card">
                    <!-- Stepper -->
                    <div class="kyc-stepper">
                        <div class="kyc-stepper-track"></div>
                        <div class="kyc-stepper-track-fill" style="width: {{ $kycIsPending ? '35%' : ($kycIsReview ? '50%' : '0%') }}"></div>

                        <!-- Step 1: Pending -->
                        <div class="kyc-step {{ $kycIsReview ? 'is-done' : ($kycIsRejected ? 'is-error' : 'is-active') }}">
                            <div class="kyc-step-icon">
                                @if($kycIsReview)
                                <i class="fas fa-check"></i>
                                @elseif($kycIsRejected)
                                <i class="fas fa-exclamation"></i>
                                @else
                                <i class="fas fa-hourglass-half"></i>
                                @endif
                            </div>
                            <span class="kyc-step-label">Pending</span>
                        </div>

                        <!-- Step 2: Under Review -->
                        <div class="kyc-step {{ $kycIsReview ? 'is-active' : '' }}">
                            <div class="kyc-step-icon">
                                @if($kycIsReview)
                                <i class="fas fa-search"></i>
                                @else
                                <span class="kyc-step-num">2</span>
                                @endif
                            </div>
                            <span class="kyc-step-label">Under Review</span>
                        </div>

                        <!-- Step 3: Approved -->
                        <div class="kyc-step">
                            <div class="kyc-step-icon"><span class="kyc-step-num">3</span></div>
                            <span class="kyc-step-label">Approved</span>
                        </div>
                    </div>

                    <!-- Status Banner -->
                    @if($kycIsPending)
                    <div class="kyc-status-banner pending">
                        <div class="kyc-status-banner-icon"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="kyc-status-banner-body">
                            <h4 class="kyc-status-banner-title">Action Required</h4>
                            <p class="kyc-status-banner-text">Your KYC application is pending. Please complete the verification process to unlock full access.</p>
                        </div>
                        <!-- <a href="{{ route('customer.kyc.summary') }}" class="kyc-status-banner-action">View KYC Summary</a> -->
                    </div>
                    @elseif($kycIsReview)
                    <div class="kyc-status-banner review">
                        <div class="kyc-status-banner-icon"><i class="fas fa-hourglass-half"></i></div>
                        <div class="kyc-status-banner-body">
                            <h4 class="kyc-status-banner-title">Application Under Review</h4>
                            <p class="kyc-status-banner-text">Our verification team is reviewing your KYC application. We'll notify you once it's approved.</p>
                        </div>
                        <!-- <a href="{{ route('customer.kyc.summary') }}" class="kyc-status-banner-action">View KYC Summary</a> -->
                    </div>
                    @elseif($kycIsRejected)
                    <div class="kyc-status-banner rejected">
                        <div class="kyc-status-banner-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="kyc-status-banner-body">
                            <h4 class="kyc-status-banner-title">KYC Application Rejected</h4>
                            <p class="kyc-status-banner-text">Your KYC application was rejected. Please review and correct the details below, then re-submit your KYC.</p>
                            @if($kycRejectReason !== '')
                            <div class="kyc-reject-reason">
                                <strong><i class="fas fa-comment-alt me-1"></i> Rejection Reason:</strong>
                                <span class="ms-1">{{ $kycRejectReason }}</span>
                            </div>
                            @endif
                        </div>
                        <!-- <a href="#kycFormSection" class="kyc-status-banner-action">Re-submit KYC</a> -->
                    </div>
                    @endif
                </div>
                @endif

                @if(!$kycExists || ($kycRecord && $kycRecord->kyc_status == 'rejected'))
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

                .step-optional-badge {
                    display: inline-block;
                    margin-left: 4px;
                    padding: 2px 6px;
                    border-radius: 999px;
                    background: rgba(99, 102, 241, 0.12);
                    border: 1px solid rgba(99, 102, 241, 0.35);
                    color: #6366f1;
                    font-size: 9px;
                    font-weight: 700;
                    letter-spacing: 0.5px;
                    line-height: 1.2;
                    text-transform: uppercase;
                    vertical-align: middle;
                }

                .csbv-optional-banner {
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                    border: 1px solid #c7d2fe;
                    background: linear-gradient(135deg, #eef2ff, #f8faff);
                    border-radius: 14px;
                    padding: 14px 16px;
                    font-size: 13px;
                    color: #4338ca;
                }

                .csbv-optional-banner i {
                    font-size: 16px;
                    color: #6366f1;
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
                                    <button type="button" id="kycWelcomeAcceptBtn"
                                        class="btn btn-primary px-5 py-2"
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
                                    <i class="far fa-file-alt me-2"></i>MERCHANT AGREEMENT
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

                <!-- KYC Alert Modal (replaces browser alert) -->
                <div class="modal fade" id="kycAlertModal" tabindex="-1" aria-labelledby="kycAlertModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
                            <div class="modal-header" style="background: #fff8f5; border-bottom: 1px solid #ffe4d6;">
                                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="kycAlertModalLabel"
                                    style="color:#b45309;">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span id="kycAlertTitle">Attention Needed</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="kycAlertBody"
                                style="padding: 20px 24px; color: #374151; font-size: 14px; line-height: 1.6;">
                            </div>
                            <div class="modal-footer" style="border-top: 1px solid #f1f5f9;">
                                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                                    <i class="fas fa-check me-1"></i>Got it
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stepper-container" id="kycFormSection">
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
                        @if(! $skipCsbV)
                        <div class="step-item" id="step4-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">4. CSB-V @if(! empty($csbVOptional))<span class="step-optional-badge">(Optional)</span>@endif</div>
                        </div>
                        @endif
                        <div class="step-item" id="step5-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">{{ $skipCsbV ? 4 : 5 }}. Upload Signature</div>
                        </div>
                        <div class="step-item" id="step6-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">{{ $skipCsbV ? 5 : 6 }}. Merchant Agreement</div>
                        </div>
                        <div class="step-item" id="step7-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">{{ $skipCsbV ? 6 : 7 }}. Activation Pending</div>
                        </div>
                        @else
                        <!-- Personal KYC (CSB-IV): 5 Steps -->
                        <div class="step-item active" id="step2-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">1. Verify Aadhar</div>
                        </div>
                        <div class="step-item" id="step3-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">2. Verify PAN</div>
                        </div>
                        <div class="step-item" id="step5-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">3. Upload Signature</div>
                        </div>
                        <div class="step-item" id="step6-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">4. Merchant Agreement</div>
                        </div>
                        <div class="step-item" id="step7-indicator">
                            <div class="step-bar">
                                <div class="step-bar-fill"></div>
                            </div>
                            <div class="step-label">5. Activation Pending</div>
                        </div>
                        @endif
                    </div>

                    <div class="kyc-card">

                        @if($userType === 'Business')
                            <!-- Step 1 Content: Verify GST Certificate (Business) -->
                            <div id="step1-content" class="step-content active">
                                <h3 class="kyc-card-title">Verify <span class="gradient-text">GST</span></h3>
                                <p class="text-muted mb-1">Provide your GST Certificate number and upload the certificate
                                    document to verify your business identity.</p>
                                <p class="text-muted small mb-4" style="color: #b45309 !important;">
                                    <i class="fas fa-info-circle me-1"></i>GST Number and Business Name are both
                                    required before you can verify.
                                </p>

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

                                <div class="row g-3 mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label-custom" for="bizGstBusinessName">Business Name</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                placeholder="Enter the name registered under this GSTIN"
                                                id="bizGstBusinessName" maxlength="255" autocomplete="organization" required>
                                            <i class="fas fa-building"></i>
                                        </div>
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
                        @endif

                        <!-- Step 2 Content: Verify Aadhar -->
                        <div id="step2-content" class="step-content">
                            <h3 class="kyc-card-title">
                                Verify <span class="gradient-text">Aadhar</span>
                                @if($isAadhaarOptional)
                                    <span class="text-muted">(Optional)</span>
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
                                @if($userType === 'Business')
                                <button class="btn btn-outline-custom flex-md-shrink-1"
                                    style="width: auto; padding-left: 40px; padding-right: 40px;"
                                    onclick="nextStep(1)">Back</button>
                                @endif
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;" id="aadharContinueBtn"
                                    onclick="{{ $isAadhaarOptional ? 'skipAadhaarStep()' : 'nextStep(' . ($userType === 'Business' ? 3 : 2) . ')' }}">{{ $isAadhaarOptional ? 'Skip / Continue' : 'Continue' }}</button>
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
                                        <input type="text" class="form-control input-custom" id="panDob"
                                            placeholder="DD/MM/YYYY" autocomplete="bday" readonly>
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
                                    onclick="nextStep({{ $userType === 'Business' ? 2 : 1 }})">Back</button>
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;" id="panContinueBtn"
                                    onclick="nextStep({{ $userType === 'Business' ? 4 : 3 }})">Continue</button>
                            </div>
                        </div>

                        @if($userType === 'Business')
                        <!-- ===== BUSINESS KYC (CSB-V) STEPS 4-7 ===== -->

                        @if(! $skipCsbV)
                        <!-- Business Step 4: CSB-V (Export Codes + LUT + Banking + Billing merged) -->
                        <div id="step4-content" class="step-content">
                            <h3 class="kyc-card-title">CSB-<span class="gradient-text">V</span></h3>
                            <p class="text-muted mb-3">Complete your CSB-V details: Export Codes, LUT, Banking and Billing
                                information.</p>

                            @if(! empty($csbVOptional))
                            <div class="csbv-optional-banner mb-4" role="note">
                                <i class="fas fa-info-circle mt-1"></i>
                                <div>
                                    <strong>CSB-V is optional for eCommerce accounts.</strong>
                                    You can skip this step and submit your KYC now, or complete it to
                                    enable CSB-V (export) shipments for your account later.
                                </div>
                            </div>
                            @endif

                            <div class="mb-4">
                                <label class="form-label-custom d-block">Select Tax Type <span class="text-danger">*</span></label>
                                <p class="text-muted small mb-2">Select GST, LUT, or both. At least one option is required.</p>
                                <div class="d-flex flex-wrap gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bizGstType">
                                        <label class="form-check-label fw-semibold" for="bizGstType">GST</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bizLutType">
                                        <label class="form-check-label fw-semibold" for="bizLutType">LUT (Against Bond or UT)</label>
                                    </div>
                                </div>
                            </div>

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
                                    <div id="bizIecUploadArea" class="csbv-upload-area"
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
                                            <p class="mb-0 fw-semibold small csbv-upload-filename" id="bizIecFileName"
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
                                            placeholder="Enter 7 or 14-digit AD Code" id="bizAdCode"
                                            inputmode="numeric" maxlength="14" pattern="[0-9]{7}|[0-9]{14}"
                                            title="AD Code must be exactly 7 or 14 digits"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0, 14)">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <small class="text-muted">AD Code must be exactly 7 or 14 numeric digits.</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Upload AD Code Document</label>
                                    <div id="bizAdCodeUploadArea" class="csbv-upload-area"
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
                                            <p class="mb-0 fw-semibold small csbv-upload-filename" id="bizAdCodeFileName"
                                                style="color: #166534;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4" style="border-color:#e2e8f0;">

                            <!-- ===== LUT Details Section ===== -->
                            <div id="bizLutDetailsSection">
                            <h5 class="fw-bold mt-2 mb-3" style="color:#4338ca;"><i class="fas fa-file-contract me-2"></i>LUT Details</h5>

                            <div class="row g-3 align-items-end mb-3">
                                <div class="col-12 col-lg-4">
                                    <label class="form-label-custom" for="bizLutNumber">LUT Number</label>
                                    <div class="input-group-custom">
                                        <input type="text" class="form-control input-custom" id="bizLutNumber"
                                            maxlength="100" placeholder="Enter LUT number">
                                        <i class="fas fa-hashtag"></i>
                                    </div>
                                </div>
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
                                        <input type="text" class="form-control input-custom" id="bizLutExpiry"
                                            placeholder="Select LUT Expiry Date" inputmode="none" autocomplete="off">
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
                                <div class="d-flex flex-column flex-sm-row gap-3">
                                    @if(! empty($csbVOptional))
                                    <button type="button" class="btn btn-outline-secondary flex-md-shrink-1"
                                        style="width: auto; padding-left: 40px; padding-right: 40px;"
                                        onclick="skipCsbVStep()" id="skipCsbVBtn">
                                        <i class="fas fa-forward me-1"></i>Skip (Optional)
                                    </button>
                                    @endif
                                    <button class="btn btn-primary-custom"
                                        style="width: auto; padding-left: 60px; padding-right: 60px;"
                                        onclick="nextStep(5)">Continue</button>
                                </div>
                            </div>
                        </div>
                        @endif

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
                                    onclick="nextStep({{ $skipCsbV ? 3 : 4 }})">Back</button>
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;"
                                    onclick="nextStep({{ $skipCsbV ? 5 : 6 }})">Continue</button>
                            </div>
                        </div>

                        @else
                        <!-- ===== PERSONAL KYC (CSB-IV) STEPS 4-5 ===== -->

                        <!-- Step 4 Content - Upload Signature -->
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
                                    onclick="nextStep(2)">Back</button>
                                <button class="btn btn-primary-custom"
                                    style="width: auto; padding-left: 60px; padding-right: 60px;"
                                    onclick="nextStep(4)">Continue</button>
                            </div>
                        </div>
                        @endif

                        <!-- Step 6 Content - Bill / Terms & Conditions -->
                        <div id="step6-content" class="step-content">
                            <!-- <h3 class="kyc-card-title">Bill</h3> -->

                            <div id="billTermsDocument" class="mt-4">
                                <div class="document-wrapper">

                                    <!-- TITLE -->
                                    <h1><span class="underline-title">MERCHANT AGREEMENT</span></h1>
                                    <span class="subhead-company"><strong>UNITED WORLDWIDE COURIERS PVT LTD</strong></span>

                                    <p>This Merchant Agreement <strong>("Agreement")</strong> is between you company/individual/firm/partnership/body corporate), together with any company or other business entity you are representing, if any (hereinafter collectively referred as "Merchant" or "you" or "User"); and <strong>United Worldwide Couriers Pvt Ltd</strong> , a company registered under the Companies Act, 1956, having its registered office at Building A-219, First Floor, Road No. 5
Mahipalpur Extension, New Delhi 110037, offering 'Logistics Management Services', under the name 'United Worldwide Couriers Pvt Ltd' (hereinafter referred to as "United Worldwide Couriers " or "we" or "Company", and together with the User referred jointly as the "Parties" and individually as a "Party"). 

.</p>
<h2>BACKGROUND</h2>
                                    <p>This Agreement comes into effect when the Merchant registers to use the Services, clicks to accept the terms, signs any proposal, onboarding form, statement of work, rate sheet, annexure, service order, or other commercial document, ships any consignment through the Company, or otherwise avails any Services from the Company. </p>

                                    <p>Such conduct shall constitute absolute, irrevocable, unconditional, and legally binding acceptance of this Agreement in its entirety. This Agreement governs the Merchant’s access to and use of the Company’s website, dashboard, software, application programming interfaces, mobile applications, communication channels, and all domestic, international, reverse logistics, customs, marketplace, importer of record, payment collection, and allied services offered by the Company from time to time. </p>

                                    <p>You are advised to read this Agreement carefully. You expressly represent and warrant that you will not avail the Services if you do not understand, agree to become a party to, and abide by all of the terms and conditions specified below. Any violation of this Agreement may result in legal liability upon you. </p>

                                    <p>This Agreement, among other things, provides the terms and conditions for use of the Services, primarily through a web-based practice management software hosted and managed remotely through the Website. </p>

                                    <p>This Agreement is an electronic record in terms of Information Technology Act, 2000 and generated by a computer system, and   does not require any physical or digital signatures. This Agreement is published in accordance with the provisions of Rule 3(1) of the Information Technology (Intermediaries guidelines) Rules, 2011 that require publishing of the rules and regulations, privacy policy and terms of usage for access or usage of the website/ service. </p>

                                    <p>United Worldwide Couriers  reserves the right to modify the terms of this Agreement, at any time, without giving you any prior notice. Your use of the Service following any such modification constitutes your agreement to follow and be bound by the terms of the Agreement, as modified. </p>

                                    <p>Any additional terms and conditions, standard operating procedures (SOPs), service-level agreements (SLAs), terms of use, disclaimers and other policies applicable to general and specific areas of this Agreement, Website, Mobile App and/or Service shall be construed to form an integral part of this Agreement and any breach thereof will be construed as a breach of this Agreement. </p>

                                    <p>Your access to use the Services will be solely at the discretion of United Worldwide Couriers . </p>

                                    <!-- Introduction -->
                                    <h2>USER ACCOUNT USAGE</h2>

                                    <p>1.1 This Agreement is a master agreement governing the relationship between the Parties in connection with one or more business-to-business and business-to-customer services made available by the Company, including but not limited to logistics management services, shipping aggregation, domestic carriage facilitation, cross-border shipping, customs facilitation, importer of record services, reverse logistics, marketplace management, product marketing support, payment collection, technology access, and all allied services described in the applicable annexures, schedules, dashboard links, and service specifications.</p>

<p>1.2 The Platforms, the Services and the Products are only available for, and to, persons who are competent to enter into legally binding agreements under the Indian Contracts Act, 1872. For avoidance of any doubt, no Services or Products or access to the Platforms will be available to any person that has been previously stopped/blocked from accessing the Platforms or availing the Services or purchasing the Products by United Worldwide Couriers, unless specifically permitted by United Worldwide Couriers at its sole discretion.</p>

<p>1.3 By accessing, browsing, using or registering on the Platforms or availing the Services or purchasing any Products, You are representing the following:</p>

<ul>
    <li>You are 18 years of age or older;</li>
    <li>You are capable of entering into a legally binding agreement as per the Applicable Law;</li>
    <li>You are not barred or otherwise legally prohibited from accessing or using the Platforms and/or Services and/or Products;</li>
    <li>You have all necessary rights, powers and authority to enter into and perform the Agreement;</li>
    <li>That the entrance and performance of the Agreement by You and/or entity You are representing shall not violate any Applicable Law and shall not breach any agreement, covenant, court order, judgment or decree to which You are bound; and</li>
    <li>That You have read and understood the contents of the Agreement and that the Agreement will not be construed in favour of or against You due to United Worldwide Couriers drafting the Agreement, and that no presumption or burden of proof shall arise favoring or disfavoring United Worldwide Couriers by virtue of being the author of any provisions of the Agreement.</li>
</ul>

<p>1.4 You may only possess one account, unless otherwise permitted by United Worldwide Couriers in writing. The login data/credentials are intended solely for Your personal use and therefore, are always to be kept secret and safe. Multiple users are not permitted to share the same/single log-in. You are not entitled to share the login credentials with any third parties for using Services or to disclose these otherwise. Further, You will be at all times responsible to maintain the confidentiality of the login credentials of Your account. You will immediately notify United Worldwide Couriers of any unauthorized use of Your passcode or account or any other breach of security, and in the event Your device has been lost or stolen, You shall request United Worldwide Couriers to block the account and/or change the passcode immediately for the account.</p>

<p>1.5 You are responsible for all activities and transactions that occur under, or take place through, Your account, whether knowingly or negligently and if anyone uses Your account, including individuals under 18 years of age, You will still be responsible for ensuring that such individuals comply with these Terms &amp; Conditions and You will be solely responsible for all actions these individuals take in and/or through Your account. You also acknowledge that United Worldwide Couriers does not have any responsibility of ensuring that You meet the aforesaid eligibility requirements.</p>

<p>1.6 Subject to compliance with this Agreement, the Company authorizes the Merchant to access and use the platform, dashboard, website, software, and mobile application solely for booking, managing, tracking, reconciling, communicating, receiving, and administering shipments and related services.</p>

<p>All content, software, layouts, workflows, processes, trademarks, service marks, trade names, dashboards, graphics, text, rate logic, data models, and compilations made available by the Company are proprietary to the Company or its licensors and shall not be copied, modified, reverse engineered, distributed, sublicensed, publicly displayed, commercially exploited, or otherwise used without prior written consent.</p>

<p>1.7 The Merchant shall not transfer, assign, sublicense, lease, share, or otherwise permit unauthorized use of any login credentials, access rights, account, token, or system access. Multiple users shall not share the same credentials unless expressly permitted in writing by the Company. The Merchant shall remain solely responsible for all activities carried out through its account, whether by employees, agents, contractors, affiliates, or other authorized or unauthorized persons using its credentials, and any such act or omission shall be deemed to be that of the Merchant.</p>

<p>1.8 The Merchant represents and warrants that all information, documents, declarations, bank details, tax registrations, KYC records, addresses, phone numbers, contact details, and other data provided to the Company are true, correct, current, complete, lawful, and not misleading. The Company may require supporting documents to verify any information and may suspend or refuse Services pending successful verification.</p>

<p>1.9 The Merchant shall use the Services only for lawful purposes and strictly in accordance with this Agreement, applicable law, applicable trade controls, customs laws, export-import regulations, sanctions laws, tax laws, and generally accepted commercial practices. The Merchant shall not impersonate any person, misrepresent its identity, use the Services for unauthorized or unlawful purposes, interfere with the platform or networks, access the Services through unauthorized means, or otherwise engage in activity that disrupts or compromises the Services or the Company’s business.</p>

<p>1.10 Access to and continued use of the Services shall remain at the sole discretion of the Company. The Company may customize, modify, suspend, restrict, or discontinue any feature, service, integration, route, rate, serviceability option, collection mode, or operational process at any time, including as required by law, government policy, carrier instructions, sanctions, local restrictions, security concerns, or operational reasons.</p>

<p>1.11 For avoidance of doubt, it is hereby stated that any change or amendment made to the Agreement will come into effect immediately upon posting (unless otherwise specified) and not on the day on which You may be notified of the changes. Any failure or delay in updating You of the changes or amendments to the Agreement by United Worldwide Couriers will not impact the validity and effectiveness of those changes or amendments. You will be solely responsible for keeping yourself updated of the changes/amendments in the Agreement by regularly reviewing the Agreement on the Platforms and checking for any updates or changes on a regular basis. Continued access or use of Platforms and/or availing of Service(s) and/or purchase of Product(s) will be deemed to constitute Your irrevocable and unconditional acceptance of the amended Agreement.</p>

<p>1.12 You have provided consent to automatically receive updates such as bug fixes, patches, enhanced functions, missing plug-ins and new versions (collectively, ‘Updates’), for the purpose of effective delivery of the Services. Please note that your continued use of the Platform following such Updates would constitute deemed acceptance by you of the same.</p>

<p>1.9 By using the Website and/or by providing your information, you consent to the collection and use of the information you disclose on the Website in accordance with this Privacy Policy, including but not limited to your consent for sharing your information as per this privacy policy.</p>

<p>1.13 Upon clicking a specific third-party link, You will be directed to the respective third party’s page offering the product or service. You irrevocably and unconditionally understand and acknowledge that: (i) You are clicking on the third party link as per Your sole discretion; (ii) accessing the third party websites shall not mean approval by United Worldwide Couriers for such products and/or services; (iii) United Worldwide Couriers shall not be liable for the accuracy of the information being shared on the third party website and cannot be held liable for any claims, losses or damages. It is Your sole responsibility to independently evaluate or obtain independent advice from a competent professional of the accuracy, completeness, and usefulness of all opinions, advice, services, merchandise, and other information provided on third-party websites. United Worldwide Couriers shall not be liable or responsible for any consequence of You accessing such websites and undertaking any transactions on such websites.</p>

<p>1.14 We receive and store certain types of information whenever you interact with us. For example, like many websites, we use “cookies,” and we obtain certain types of information when your web browser accesses our Services. We may also receive/store information about your location and your mobile device, including a unique identifier for your device. We may use this information for internal analysis, legal discrepancies, dispute resolution, fraud detection, abuse prevention, suspicious activity monitoring and to provide you with location-based services, such as advertising, search results, and other personalized content. Please note that your continued use of the Platform following such Updates would constitute deemed acceptance by you of the same. Your data is retained only as long as necessary for the purposes outlined in this policy and to meet legal requirements. We take reasonable steps to delete or de-identify data when no longer needed.</p>

<p>1.15 If at any time You do not agree with any provision of the Agreement or do not wish to be bound by the Agreement, You shall immediately cease Your use of the Platforms, Services and Products and forthwith make payment of all outstanding amounts due to United Worldwide Couriers. You shall be bound by and shall continue to adhere and abide by the Agreement, for so long as You avail the Service or owe any amount to United Worldwide Couriers in relation to any of the Service(s) and/or Products.</p>


<h2>2. Eligibility</h2>
<p>2.1 The Merchant shall pay the Company all subscription fees, shipping charges, freight, RTO charges, reverse pickup charges, COD handling charges, customs-related service fees, importer of record charges, storage charges, demurrage, incidental expenses, surcharges, accessorial charges, address correction fees, penalties, taxes, and all other amounts applicable to the Services as set out in this Agreement, any applicable annexure, rate sheet, live calculator, dashboard, invoice, commercial proposal, or written communication issued by the Company. Unless expressly stated otherwise, all fees are exclusive of taxes and all applicable taxes, including GST, shall be charged in addition.</p>

<p>2.2 The Company may add new services for additional charges or revise existing charges, rates, surcharges, accessorial charges, service conditions, or fee structures at any time by notice through dashboard, email, mobile application, rate card, calculator link, annexure, or any other official communication channel. Unless the Merchant objects in writing before the stated effective date and ceases use of the affected Services, the revised fee structure shall be deemed accepted.</p>

<p>2.3 The Company may issue invoices periodically, including mid-month, month-end, or such other cycle as determined by the Company. The Merchant shall verify invoice contents promptly and, unless a shorter period is prescribed for a specific service model, shall raise any bona fide dispute within five working days of invoice availability and pay undisputed amounts within seven days from invoice date or such other due date specified in writing. Failure to raise a dispute within the prescribed period shall constitute deemed acceptance of the invoice.</p>

<p>2.4 If the Merchant fails to pay any amount when due, the Company shall, without prejudice to any other right or remedy, have the right to suspend shipping, retain and adjust outstanding amounts against COD remittances, wallet balances, credits, deposits, refunds, or any amounts payable to the Merchant, retain custody of shipments, re-route shipments, levy interest at 18 percent per annum from the due date until realization, forfeit security deposit or wallet balance where contractually permitted, and dispose of shipments in accordance with this Agreement where the Merchant fails to regularize defaults within the applicable period. The Merchant acknowledges that freight charges become due upon pickup, shipment initiation, or RTO initiation, as applicable, whether or not already invoiced.</p>

<p>2.5 The Merchant shall provide only lawfully owned and valid payment credentials, bank account details, billing details, and other financial information. The Merchant remains solely responsible for the accuracy, legality, and confidentiality of such information. The Company may require bank verification, KYC, source-of-funds information, or any other compliance information before processing remittances, refunds, or wallet withdrawals.</p>

<p>2.6 For shipments booked under cash on delivery, the Merchant appoints the Company as a limited collection agent solely for the purpose of collecting the COD amount from the consignee through the Company’s logistics vendors and remitting the balance after deduction of applicable freight, service fees, handling fees, taxes, offsets, reversals, and other lawful deductions. The Company shall have no title in the goods. Save as otherwise agreed, COD remittance may be made within eight days from delivery of the relevant shipment or in accordance with the remittance cycle then followed by the Company, subject always to reconciliation, carrier remittance, fraud review, dispute review, status verification, valid bank details, and absence of offset rights.</p>

<p>2.7 Where a shipment status is wrongly updated as delivered, a COD amount has been wrongly remitted, a COD order is subsequently modified, a dispute is raised by a buyer, a fraud event is suspected, or any amount is otherwise found to have been incorrectly credited, the Company may deduct such amount from future remittances, wallet balances, or other monies payable to the Merchant. If any COD amount remains unremitted for 365 days from its due date for reasons not attributable to the Company, including incorrect bank details or failure of the Merchant to complete compliance formalities, the Merchant waives all claims to such amount and the Company may forfeit the same.</p>

<p>2.8 Where the Merchant operates on a prepaid model, the Merchant shall maintain sufficient shipping credits in its wallet or account before availing Services. Freight and other charges may be auto-adjusted against wallet balances, and any negative balance may be set off against COD or other receivables. Credit balance may be used only for booking shipments and may be forfeited if no shipment is booked for a continuous period of three years from the last shipment date, subject to applicable law. Refunds, if approved, may be restricted to the original source or mode of payment and may be conditioned upon KYC compliance and any surcharge or deduction permitted by law or contract.</p>

<p>2.9 Where the Merchant is granted secured postpaid or rolling credit, such facility is discretionary and may be increased, reduced, suspended, or withdrawn at any time. Used credit may be adjusted from upcoming remittances, wallet balances, deposits, or any amount payable to the Merchant. Non-payment shall entitle the Company to suspend Services and exercise all contractual and legal recovery rights.</p>

 <h2>3. METHODOLOGY FOR PRICING AND VOLUMETRIC WEIGHT CALCULATION</h2>

 <p>3.1 Each individual Shipment is subject to weight and size limits that may differ by Shipping Vendor and destination.</p>

<p>3.2 There is no restriction on the total aggregate weight of all your Shipments or on the number of boxes contained in any single Shipment.</p>

<p>3.3 Volumetric (dimensional) weight will be calculated automatically in the Platforms’ booking panel using the formula: length (cm) × breadth (cm) × height (cm) ÷ 5000.</p>

<p>For example, a parcel measuring 30 cm × 25 cm × 40 cm has a volumetric weight of 30 × 25 × 40 ÷ 5000 = 6 kg.</p>

<p>3.4 Billing for Shipments will follow these rules:</p>

<ul>
    <li>Where volumetric weight is 5 kg or less, charges will be based on actual (dead) weight;</li>
    <li>Where volumetric weight exceeds 5 kg, charges will be based on whichever is greater - actual weight or volumetric weight;</li>
    <li>Certain items that require special handling, as defined in our internal operational guidelines, will incur an additional special handling fee.</li>
</ul>

 <h2>4. SHIPMENT BOOKING AND OPERATIONAL WORKFLOW</h2>

 <p>4.1 You may choose between two booking methods for Shipments: United Worldwide Couriers-Pickup or Self-Ship. Under United Worldwide Couriers-Pickup, we will collect Shipments from the pickup address you provide. Under Self-Ship, you must deliver Shipments to our nearest hub.</p>

<p>4.2 Upon arrival at our hub, each Shipment will be scanned, weighed and sorted according to destination and selected service. If there is a discrepancy between the weight you declared and the weight recorded at the hub, the Shipment will be placed on hold and we will notify you by email for approval. Once you approve the recorded weight, the Shipment will proceed to its destination. After a Shipment has left our hub, no further weight adjustments will be charged to your account.</p>

<p>4.3 For international Shipments, after export clearance in India, the Shipment will be customs-cleared in the destination country and received at our local office before being handed to the last-mile carrier. Last-mile carriers vary by country (for example: USA - UPS, USPS, FedEx; Europe - DPD or national postal networks; Canada - Canada Post, UPS). These carriers may change from time to time. For Shipments routed through branded carrier networks (for example DHL, UPS, FedEx), the Shipment is connected to the carrier’s hub in Delhi and tracking is available on the carrier’s website using their tracking number.</p>

<p>4.4 Delivery attempts and handling - We will attempt delivery of all Shipments at least once; many last-mile Shipping Vendors attempt delivery twice depending on their policies. If no one is available to receive a Shipment, the carrier may, at their discretion, leave the Shipment with a neighbour, deposit it at the local post office for customer pickup, or place it in a secure external location outside the house (including a mailbox).</p>

<p>4.5 Return to origin (RTO) policy</p>

<p>4.5.1 United Worldwide Couriers Direct Service: If a Shipment is undeliverable for any of the reasons listed below, it will be returned to our or our partner’s local office in the destination country. RTO charges will be billed to your account. In most cases such shipments will be destroyed in the destination country, except for Shipments to the USA and Europe where re-forwarding may be available at an additional cost.</p>

<p>4.5.2 Branded carrier networks (DHL, FedEx, UPS): If a Shipment is undeliverable for any of the reasons below, the carrier may either destroy the Shipment in the destination country (per the carrier’s policy) or return it to you in India; applicable RTO charges will be billed to your account. If the RTO results from your failure to pay duties or taxes, the return and all related charges will be billed to your account.</p>

<p>4.6 Reasons a Shipment may be deemed undeliverable include incorrect or incomplete address details at booking or on the Shipment (for example, missing apartment number or street name); Customer refusal to accept delivery; Customer refusal to pay applicable duties and/or taxes.</p>

<p>4.7 RTO, storage and disposal charges</p>

<p>4.7.1 United Worldwide Couriers reserves the right to return any Shipment not accepted by the Customer and to apply RTO charges at prevailing rates.</p>

<p>4.7.2 If you do not accept an RTO Shipment or cannot be reached, we may levy demurrage/incidental storage charges for any period exceeding seven (7) working days from initiation of the return, up to forty-five (45) working days from that date.</p>

<p>4.7.3 If the Customer fails to accept the returned Shipment beyond ten (10) working days from the first RTO undelivered date or first RTO delivery attempt date, United Worldwide Couriers may dispose of the Shipment. In that case you will forfeit any claims related to the Shipment and will remain liable for disposal charges and any other applicable charges, including demurrage/incidental storage charges.</p>

<h2>5. REFUND AND LIABILITY POLICY </h2>

<p>5.1 Claims and compensation - We aim to deliver all Shipments safely. If a delivery issue occurs, you may be eligible to file a claim and receive compensation as follows.</p>

<p>5.1.1 General requirement to file a claim - To initiate a claim, provide the United Worldwide Couriers airway bill (AWB) number and all supporting documents to Csd@unitedcouriers.biz. Claims for Shipments routed via branded carrier networks (for example, DHL, UPS, FedEx) will be processed under the same policy, subject to any carrier-specific timelines.</p>

<p>5.2 Claim scenarios and compensation</p>

<p>5.2.1 Case 1 - No first scan by the destination last-mile carrier: If no first scan is recorded by the last-mile carrier in the destination country (for example, a United Worldwide Couriers Direct Shipment to the USA shows a carrier allocation such as UPS but no initial scan), compensation is limited as follows:</p>

<p>5.2.1.1 For Shipments weighing 0 - 100 g: 30% of the invoice value plus shipping charges, capped at Rs. 1,000 (INR One Thousand), inclusive of shipping charges.</p>

<p>5.2.1.2 For Shipments over 100 g: 30% of the invoice value plus shipping charges, capped at Rs. 4,000 (INR Four Thousand), inclusive of shipping charges.</p>

<p>5.2.2 Case 2 - No delivery scan or Lost in Transit: Where the last-mile carrier’s delivery scan is not available or the Shipment is otherwise lost in transit (for example when final handling is by a destination postal service), you may claim reimbursement if you have already refunded your buyer for non-delivery. To support this claim, submit the buyer-seller chat and proof of refund along with the required AWB and documents. Claims under branded networks follow the same evidentiary requirements but will be processed according to the carrier’s timelines and policies.</p>

<p>5.2.3 Case 3 - Package lost before inwarding at Delhi hub:</p>

<p>5.2.3.1 If pickup was carried out by United Worldwide Couriers’s team and the package is lost before inwarding at the Delhi hub, refunds will be made in accordance with the refund policy (referenced below).</p>

<p>5.2.3.2 If the pickup was performed by a third-party logistics provider (3PL), only the shipping charges will be refunded.</p>

<p>5.3 Additional terms</p>

<p>5.3.1 All claims are subject to verification and acceptance under United Worldwide Couriers’s Claim Policy. Submission of incomplete or fraudulent documentation may result in denial of the claim.</p>

<p>5.3.2 Where applicable, recoveries from carriers or third parties will be applied against any amounts payable; any net recovery will be distributed in accordance with the Claim Policy.</p>

<p>5.3.3 This Claims and compensation clause is subject to the limits, exclusions and timelines set out in the Claim Policy referenced in this agreement.</p>

<p>5.4 No claims maintainable - Claims are payable only under the specific circumstances described in the Claims and Compensation section. United Worldwide Couriers will not be liable to entertain claims in any other situations, including but not limited to the following:</p>

<p>5.4.1 Non-delivery resulting from incorrect or incomplete information provided by you (for example, wrong name, address, telephone number or other erroneous booking details). Where reconnection or rerouting of a Shipment is possible, United Worldwide Couriers may notify you and may, at its sole discretion, arrange reconnection subject to additional charges; reconnection is not guaranteed.</p>

<p>5.4.2 Any loss, delay, seizure or detention of a Shipment by customs, quarantine or any other governmental or regulatory authority.</p>

<p>5.4.3 Situations where a delivery attempt was made but delivery could not be completed for any reason, including the recipient’s absence or refusal to accept the Shipment.</p>

<p>5.4.4 Damage, leakage, tampering or pilferage attributable to inadequate or unsuitable packaging by you.</p>

<p>5.4.5 Circumstances where the applicable Shipping Vendor does not provide for a refund under its operating rules or contracts.</p>

<p>5.4.6 Where the Shipping Vendor’s records indicate that the Shipment was delivered (per the carrier’s tracking or delivery confirmation), the Shipment will be deemed delivered and no refund claim will be entertained.</p>

<p>United Worldwide Couriers reserves the right to rely on carrier records, investigation findings, and applicable carrier or regulatory policies when assessing any request related to the above exceptions.</p>

<p>5.5 The Claim Policy applies only to United Worldwide Couriers Self-Services (including, without limitation United My Delivery, United Air Premium, United Grd Premium, United Air Express, United Prior Post, United Eco Post, United My Pickup). For Shipments carried by branded carrier networks (for example DHL, FedEx and UPS), the applicable carrier refund/return policy will also apply.</p>

<p>5.5.1 To initiate a claim, email Csd@unitedcouriers.biz with the United Worldwide Couriers AWB and all supporting documents.</p>

<p>5.5.2 On receipt of a claim, United Worldwide Couriers’s support team will investigate, including contacting the relevant local office and/or Shipping Vendor to locate the Shipment and assess the claim.</p>

<p>5.5.3 After completing the investigation, United Worldwide Couriers will communicate the outcome to you or request further information if required.</p>

<p>5.5.4 United Worldwide Couriers will endeavour to close each refund claim within fifteen (15) working days from receipt of your complaint; where investigation requires cooperation from Shipping Vendors, additional time may be necessary.</p>

<p>5.5.5 For United Worldwide Couriers Direct services only: if a Shipment has not been delivered within thirty (30) days of pickup (and there is no dispute), you will be entitled to a refund under this Merchant Agreement. If a dispute arises, the thirty-day period will be measured from one (1) day after the dispute is resolved. The thirty-day refund entitlement does not apply to other services or where additional documents are required.</p>

<p>5.6 For any claim to be valid, the following points need to be observed by You to the extent applicable:</p>

<p>5.6.1 Claims must be made promptly; in any event United Worldwide Couriers will not consider claims submitted more than sixty (60) working days after the date of the inward scan.</p>

<p>5.6.2 If United Worldwide Couriers notifies you that a Shipment is stuck, undelivered or under RTO and you do not respond within seven (7) working days of that communication, the related claim will not be considered.</p>

<p>5.6.3 No claim will be accepted for Shipments showing a scan status of “out for delivery” or “awaiting delivery” unless you provide chat records or screenshots evidencing the buyer’s non-receipt.</p>

<p>5.6.4 For claims alleging non-connectivity (i.e., the Shipment was picked up but not scanned or connected at the hub), you must submit the signed pickup manifest for the disputed Shipment(s) within three (3) working days of pickup. Claims lacking a signed manifest will not be maintainable; normal turnaround times will not apply in case of such disputes.</p>

<p>5.6.5 For damage, pilferage, tampering, crushing or leakage, the recipient must record negative remarks on the proof of delivery (POD) at the time of delivery to preserve the claim. Absent clear negative remarks on the POD, no claim will be maintainable.</p>

<p>5.6.6 Claims for damage, pilferage, tampering, crushing or leakage will be entertained only if made within forty-eight (48) hours of delivery/receipt and only where the outer packaging applied by United Worldwide Couriers or the Shipping Vendor is damaged, altered or tampered with. If the outer packaging is intact, such claims will not be accepted.</p>

<p>5.6.7 Claims for Shipments carried under branded networks (DHL, FedEx, UPS) remain subject to the evidence and submission requirements above, but will also follow the timelines and processes mandated by the relevant carrier.</p>

<p>5.6.8 Where a customer who is not registered under the GST regime has not transacted for three hundred sixty-five (365) consecutive days, any wallet balance held will be refunded to that customer in accordance with United Worldwide Couriers’s refund procedures.</p>

<p>5.6.9 All claims are subject to verification and United Worldwide Couriers’s Claim Policy. Submission of incomplete, inaccurate or fraudulent documents may lead to claim denial. Recoveries from carriers or third parties will be applied against any amounts payable, consistent with the Claim Policy.</p>


 <h2>6.HANDLING CHARGES, REMOTE AREA CHARGES, REFORWARDING CHARGES AND OTHER CHARGES </h2>

 <p>6.1 Shipping charges for United Worldwide Couriers-branded services will be determined on a case-by-case basis. If a Shipment is detained by customs, additional charges will apply based on actual costs. Please note that handling charges set out above are for a maximum Shipment weight of:</p>

<ul>
    <li>In case of USA: 22 KGs (volume or dead weight whichever is higher);</li>
    <li>In case of UK: 30 KGs (volume or dead weight whichever is higher);</li>
    <li>In case of Europe: 30 KGs (volume or dead weight whichever is higher);</li>
    <li>In case of Australia/NZ: 20 KGs (volume or dead weight whichever is higher);</li>
    <li>In case of Canada: 20 KGs (volume or dead weight whichever is higher);</li>
    <li>In case of package volume or dead weight is higher than above mentioned slabs, there will be an additional handling charge of Rs. 4,000/- plus GST, up to a maximum of additional 10 KGs higher.</li>
</ul>

<p>6.2 Remote area charges for United Worldwide Couriers-branded services will be disclosed at the time of booking. For Non-United Worldwide Couriers branded services, remote area charges will apply as per the respective service provider’s policy.</p>

<p>6.3 Tariff and duty adjustments: United Worldwide Couriers may deduct or refund any difference in duty or tariff charged or refunded on a parcel sent using United Worldwide Couriers services. Any such adjustment will be applied directly to your wallet balance without prior notice.</p>

<p>6.4 Reforwarding charges for United Worldwide Couriers-branded services (subject to change) are:</p>

<ul>
    <li>USA - $15 for up to 500 g, $25 for 500 g to 5 kg;</li>
    <li>Europe - €20 for up to 500 g, €30 for 500 g to 5 kg;</li>
    <li>Canada - CAD 20 for up to 500 g, CAD 30 for 500 g to 5 kg;</li>
    <li>UK - £15 for up to 500 g, £21 for 500 g to 5 kg;</li>
    <li>For non-United Worldwide Couriers branded services, reforwarding charges will follow the applicable service provider’s policy.</li>
</ul>

<p>6.5 Misdeclaration charges: If a Shipment you book is found to be misdeclared, United Worldwide Couriers may levy an administrative charge of INR 5,000 per Shipment and recover it from your wallet or settlement without prior notice. RTO requests will not be entertained for misdeclared Shipments. You may opt to self-pick-up the seized Shipment within seven (7) working days of intimation; if you fail to do so, United Worldwide Couriers may destroy or dispose of the Shipment through authorized channels at your sole risk, cost and liability, with no further claim against United Worldwide Couriers.</p>

<p>6.6 Prohibited or restricted items: If a Shipment is found to contain Prohibited or Restricted Items (as defined by this Agreement, applicable law, or carrier/customs rules), you will be in material breach of this Agreement. United Worldwide Couriers may impose a penalty of INR 50,000 per Shipment, recoverable immediately from your wallet or settlement without prior notice. United Worldwide Couriers may notify and cooperate with government or law-enforcement authorities. No RTO or self-pickup requests will be accepted for such shipments; they may be seized and destroyed or disposed of through authorized channels without liability to United Worldwide Couriers. Repeat offences (two or more instances) may result in permanent suspension or banning of your account, and United Worldwide Couriers may pursue all contractual, civil and criminal remedies available under law.</p>

<p>6.7 Special handling fee: Certain goods that require specialised handling, as defined in United Worldwide Couriers’s internal operational guidelines, will attract an applicable special handling fee.</p>

<h2>7. GENERAL REPRESENTATIONS AND WARRANTIES</h2>

<p>Each Party represents and warrants that it has full right, power, legal authority, and capacity to enter into and perform this Agreement, and that its execution and performance of this Agreement do not violate any applicable law, sanction, court order, government direction, corporate authorization, contractual obligation, or third-party right binding upon it. The Merchant further represents and warrants that it lawfully owns, possesses, controls, sells, exports, imports, markets, and ships all goods tendered under this Agreement and has obtained all necessary consents, licenses, registrations, declarations, and approvals required for the Services. </p>

<h2>8. LIMITATION OF LIABILITY</h2>

<p>Notwithstanding anything contained herein or elsewhere, You hereby agree, acknowledge, and confirm that:</p>

<ol>
    <li>The liability of United Worldwide Couriers in relation to the Services shall be strictly limited to the extent expressly provided under this Agreement, the applicable Terms and Conditions, annexures, policies, and service-specific provisions.</li>

    <li>The Platform and Services are provided on an “as is”, “as available”, and “reasonable efforts” basis, and access to or use of the Platform may be interrupted, suspended, delayed, restricted, or unavailable from time to time during browsing, booking, uploading, transacting, or availing the Services.</li>

    <li>United Worldwide Couriers shall use commercially reasonable efforts to provide and maintain the Services, but does not guarantee uninterrupted, error-free, secure, or continuous operation of the Platform or Services, and shall not be liable for any interruption, suspension, delay, inaccessibility, technical failure, or inability to provide the Services.</li>

    <li>United Worldwide Couriers makes no representation, warranty, undertaking, or guarantee, whether express, implied, statutory, or otherwise, regarding the Platform, Services, Shipping Vendors, delivery timelines, merchantability, fitness for a particular purpose, non-infringement, reliability, availability, or suitability of the Services.</li>

    <li>United Worldwide Couriers does not independently verify, validate, endorse, or authenticate any information, declarations, listings, content, documents, data, or materials uploaded, transmitted, declared, or provided by Users, Merchants, customers, consignees, Shipping Vendors, or third parties, and disclaims all liability arising from reliance thereon.</li>

    <li>You acknowledge and agree that use of the Platform and availing of the Services is entirely at Your sole risk. To the maximum extent permissible under Applicable Law, United Worldwide Couriers, its affiliates, directors, officers, employees, agents, consultants, licensors, technology providers, Shipping Vendors, subcontractors, and service providers shall not be liable for any direct, indirect, incidental, special, exemplary, punitive, consequential, or economic losses or damages whatsoever, including without limitation loss of profits, loss of business, loss of revenue, loss of opportunity, loss of goodwill, business interruption, loss of data, corruption of information, shipment loss, shipment delay, or procurement of substitute services, arising out of or in connection with:</li>

    <ol>
        <li>use of or inability to use the Platform or Services;</li>
        <li>system interruption or technical malfunction;</li>
        <li>cyber incidents, malware, ransomware, bugs, viruses, or unauthorized access;</li>
        <li>temporary disablement, suspension, withdrawal, or discontinuation of the Platform or Services;</li>
        <li>acts or omissions of Shipping Vendors, customs authorities, government authorities, payment partners, or third parties; or</li>
        <li>any breach of security, data compromise, or operational disruption, whether based in contract, tort, negligence, strict liability, statutory liability, or otherwise, even if advised of the possibility of such damages.</li>
    </ol>

    <li>United Worldwide Couriers shall not be liable for any delay, interruption, suspension, failure, or non-performance arising directly or indirectly from any event beyond its reasonable control, including acts of God, flood, fire, epidemic, pandemic, war, terrorism, civil unrest, labour disputes, transport disruption, carrier failure, customs action, sanctions, cyber incidents, utility failures, governmental action, regulatory restrictions, changes in Applicable Law, or failures of third-party integrations, payment gateways, technology systems, or communication networks.</li>

    <li>Where any action is taken by United Worldwide Couriers pursuant to Your authorization, consent, instructions, declarations, shipment details, or account activity, United Worldwide Couriers shall not be responsible or liable for any resulting loss, damage, liability, expense, or consequence suffered by You or any third party.</li>

    <li>The pricing of Services, operational structure, commercial arrangements, and contractual allocations under this Agreement have been determined in reliance upon the disclaimers, exclusions, limitations of liability, and allocation of risks set out herein, which the Parties acknowledge to be fair, reasonable, and an essential basis of the commercial understanding between the Parties. United Worldwide Couriers would not have agreed to provide the Services on the same commercial terms in the absence of such limitations and exclusions.</li>

    <li>Any liability of United Worldwide Couriers, to the extent expressly accepted under this Agreement, shall extend solely toward the Merchant/User contracting with United Worldwide Couriers. The Merchant/User shall remain solely responsible and liable toward its customers, buyers, consignees, recipients, suppliers, and third parties, and neither United Worldwide Couriers nor any Shipping Vendor shall owe any direct contractual, fiduciary, statutory, or other obligation or liability toward such persons in connection with the Shipments or Services.</li>
</ol>

<p>Customers are advised to select alternative shipping services with insurance and claims options if they require additional protection for their shipments. Shipments are <strong>NOT insured</strong> unless separately purchased by the Merchant.</p>

 <h2>9. INDEMNITY</h2>

 <p>The Merchant shall indemnify, defend, and hold harmless the Company, its affiliates, directors, officers, employees, agents, subcontractors, consultants, licensors, service providers, shipping partners, customs agents, importer of record entities, marketplace partners, and representatives from and against any and all claims, actions, proceedings, losses, damages, liabilities, penalties, duties, taxes, interest, costs, and expenses, including reasonable legal fees and investigation costs, arising out of or related to: (a) the Merchant’s access to or use of the Services; (b) any breach of this Agreement; (c) violation of law, sanctions, export controls, customs requirements, tax requirements, consumer law, product law, or any government directive; (d) misdeclaration, under-declaration, wrongful valuation, wrong HS classification, wrong description, false origin declaration, counterfeit goods, restricted goods, prohibited goods, dangerous goods, infringing goods, or fraud; (e) defective, unsafe, mislabeled, non-compliant, adulterated, expired, or recalled goods; (f) third-party claims by buyers, consignees, authorities, carriers, payment intermediaries, or intellectual property owners; (g) duties, penalties, clearance charges, detention, demurrage, or storage charges; and (h) negligent, wrongful, or fraudulent acts or omissions of the Merchant or its personnel.</p>

<p>The Company may notify the Merchant of any such claim and the Merchant shall provide all necessary cooperation, documents, witness support, and funding for defense and settlement. The Company may participate in the defense at the Merchant’s cost where its interests are materially affected.</p>


 <h2>10. COMPLIANCE WITH LAWS</h2>

 <p>
    Each Party shall at all times and at its/his/her own expense: (a) strictly comply with all applicable laws (including state, central and custom/international laws/statutes), now or hereafter in effect, relating to its/his/her performance of this Agreement; (b) pay all fees and other charges required by such applicable law; and (c) maintain in full force and effect all licenses, permits, authorizations, registrations and qualification from any authority to the extent necessary to perform its obligations hereunder. 
 </p>

 <h2>11. USE OF CONFIDENTIAL INFORMATION</h2>

 <p>11.1 Each Party may receive confidential Information of the other in the course of performance of this Agreement. The receiving Party shall keep such information strictly confidential, use it only for performance of this Agreement, restrict disclosure to personnel, professional advisers, contractors, and prospective contractors on a strict need-to-know basis, and protect it with at least the same degree of care it uses for its own confidential information, and in any event not less than reasonable care.</p>

<p>11.2 All confidential Information and all intellectual property rights therein shall remain the sole property of the disclosing Party. No license, assignment, transfer, or other right is granted by implication or otherwise except the limited right to use such information for performance of this Agreement.</p>

<p>11.3 Confidentiality obligations shall not apply to information already lawfully in the public domain, lawfully known to the receiving Party without restriction, independently developed without use of the disclosing Party’s information, or required to be disclosed by law, court order, or government authority, provided lawful notice is given where permitted. Upon termination or request, the receiving Party shall return or destroy confidential Information to the extent reasonably practicable and certify compliance if requested.</p>

<p>11.4 You agree, consent to and acknowledge that as part of the registration process, You will provide personally identifiable information about You like Your Name, email address, mobile phone number, address and contact details, Postal code, Demographic profile (like your age, gender, occupation, education, address etc.), which shall be stored by United Worldwide Couriers and, United Worldwide Couriers may also store information about the pages on the site You visit/access, the links you click on the site, the number of times you access the page and any such browsing information. All such information shall be stored and used in accordance with the Privacy Policy.</p>

<h2>12. INTELLECTUAL PROPERTY RIGHTS</h2>

<p>
    All intellectual property in the Company’s platform, software, systems, APIs, dashboards, workflows, trademarks, brand assets, websites, documents, templates, service descriptions, rate engines, operating methods, and derivative works shall remain vested exclusively in the Company or its licensors. All intellectual property owned by either Party prior to this Agreement shall remain with that Party. Any feedback, suggestions, enhancement requests, process improvements, or derivative developments created in connection with the Services may be used by the Company without restriction unless otherwise agreed in writing. 
</p>


<h2>13. NON-SOLICITATION</h2>

<p>During the term of this Agreement and for thirty-six months thereafter, the Merchant shall not directly or indirectly solicit, divert, interfere with, induce, or attempt to induce any client, customer, supplier, shipping vendor, partner, employee, contractor, customs partner, or other business relationship of the Company to cease or reduce business with the Company or to provide competing services in circumvention of the Company. </p>

<h2>14. TERM AND TERMINATION</h2>

<p>This Agreement shall commence on the date the Merchant first avails the Services and shall continue unless terminated in accordance with this Agreement. The Merchant may request termination by thirty days’ prior written notice, subject to completion of in-transit shipments, reconciliation, settlement of all dues, submission of documents, discharge of liabilities, and compliance with any service-specific lock-in or closure conditions.</p>

<p>The Company may suspend or terminate this Agreement or any account immediately, with or without notice, if: (a) the Merchant breaches this Agreement; (b) the Merchant’s conduct exposes or may expose the Company or its partners to legal, regulatory, reputational, financial, fraud, sanctions, security, or operational risk; (c) the Merchant ships prohibited, dangerous, counterfeit, or unlawful goods; (d) the Merchant defaults in payment; (e) required KYC or compliance verification fails; (f) instructions are received from a carrier, regulator, payment partner, law enforcement agency, or government authority; or (g) the Company elects to discontinue the relationship for business convenience where lawful.</p>

<p>Upon suspension or termination, the Merchant shall immediately cease unauthorized use of the Services and shall not create a fresh account or access the Services through any alternate identity without the Company’s written approval.</p>

<h2>15. MISUSE OF THE SERVICES</h2>

<p>
    The Company may restrict, deactivate, suspend, or terminate the account of any Merchant that abuses or misuses the Services, including by creating false or duplicate profiles, infringing intellectual property rights, shipping prohibited or suspicious goods, evading fees, manipulating system workflows, under-declaring weight or value, booking shipments outside permitted use, circumventing controls, refuse to cooperate in an investigation or engaging in any conduct deemed suspicious, fraudulent, harmful, or contrary to the purpose of the Services. Repeat violations may result in permanent blacklisting and legal action. 


</p>

<h2>16. GOVERNING LAW AND DISPUTE RESOLUTION</h2>

<p>
    This Agreement shall be governed by the laws of India and subject to the Clause below, the courts of New Delhi shall have exclusive jurisdiction to determine any disputes arising out of, under, or in relation, to the provisions of this Agreement. Any dispute arising under this Agreement shall be settled by arbitration to be held in New Delhi in accordance with the (Indian) Arbitration and Conciliation Act, 1996, in the English language, and shall be heard and determined by a sole arbitrator appointed by United Worldwide Couriers. The decision of the sole arbitrator shall be final, conclusive and binding on the Parties. Notwithstanding the foregoing, nothing contained herein shall be deemed to prevent either Party from seeking and obtaining injunctive and/or equitable relief from any court of competent jurisdiction. 
</p>

<h2>17. SEVERABILITY</h2>

<p>The invalidity or unenforceability of any provision in this Agreement shall in no way affect the validity or enforceability of any other provision herein. In the event of the invalidity or unenforceability of any provision of this Agreement, the Parties will immediately negotiate in good faith to replace such a provision with another, which is not prohibited or unenforceable and has, as far as possible, the same legal and commercial effect as that which it replaces. </p>

<h2>18. FORCE MAJEURE</h2>

<p>Neither Party shall be liable for any failure or delay in performance, other than payment obligations already accrued, to the extent caused by events beyond its reasonable control, including acts of God, flood, fire, explosion, epidemic, pandemic, war, civil disturbance, terrorism, labor disruption, carrier failure, transport disruption, sanctions, customs restrictions, import-export policy changes, regulatory action, cyber incidents not caused by that Party’s negligence, governmental orders, or failure of utilities or communication systems. The affected Party shall notify the other Party as soon as reasonably practicable. If a force majeure event continues for more than thirty days, the unaffected Party may modify the affected obligations or temporarily excuse performance, and if it continues beyond sixty days, either Party may terminate the affected Services upon written notice. </p>


<h2>19. ENTIRE AGREEMENT, ASSIGNMENT AND SURVIVAL</h2>

<p>This Agreement, together with all annexures, schedules, statements of work, rate sheets, dashboard notices, web links, operating procedures, and written addenda, constitutes the entire agreement between the Parties with respect to its subject matter and supersedes all prior discussions, representations, correspondence, proposals, and understandings on that subject. In the event of inconsistency, the relevant annexure shall prevail only for the subject matter of that annexure, and service-specific terms, SOPs, rate cards, and web-based links may prevail over general terms where expressly stated. </p>

<p>The Merchant shall not assign, transfer, novate, subcontract, or otherwise deal with its rights or obligations under this Agreement without the Company’s prior written consent. The Company may assign or transfer this Agreement, in whole or in part, to any affiliate, successor, acquirer, or business transferee. Provisions which by their nature are intended to survive, including those relating to payment, indemnity, confidentiality, IP, limitation of liability, dispute resolution, audits, and compliance, shall survive termination. </p>


 <h2>20. NO PARTNERSHIP OR AGENCY</h2>

 <p>Nothing in this Agreement shall be construed to create a partnership, joint venture, fiduciary relationship, franchise, employment relationship, or agency between the Parties, except that the Company may act as a limited collection agent for COD remittance solely to the extent expressly stated herein. Neither Party shall have authority to bind the other except as expressly provided in writing</p>


  <h2>21. WAIVERS AND REMEDIES</h2>

<p>No failure or delay by either Party in exercising any right, power, or remedy shall operate as a waiver thereof. Any waiver must be in writing and limited to the specific instance stated. All rights and remedies under this Agreement are cumulative and in addition to all rights and remedies available under applicable law.</p>

  <h2>22. SPECIFIC PERFORMANCE</h2>

  <p>The Parties acknowledge that breach of obligations relating to confidentiality, intellectual property, restrictive covenants, misuse of systems, non-payment, or unlawful shipment may cause irreparable harm for which damages may be inadequate. Accordingly, the Company shall be entitled to seek specific performance, injunctive relief, and other equitable remedies, in addition to monetary relief and other remedies available under law. </p>

   <h2>23. INDIRECT AND CONSEQUENTIAL LOSSES</h2>

   <p>Save as expressly provided in this Agreement, neither Party shall be liable for indirect or consequential losses, including loss of income, loss of profits, loss of contracts, loss of opportunity, loss of reputation, or business interruption, however arising. This clause shall be read subject to the more specific liability exclusions and caps set out elsewhere in this Agreement. </p>

   <h2>24. CONTACT INFORMATION AND COMMUNICATIONS</h2>

<p>24.1 All notices, communications, updates, invoices, rate revisions, dashboard alerts, service notifications, legal notices, and operational instructions may be issued by the Company through email, dashboard, mobile application, SMS, WhatsApp, registered mobile number, support ticketing system, courier, or any other officially designated communication channel, and such communications shall be legally valid and binding to the extent permitted by law. The Merchant consents to receive communications through such channels.</p>

<p>24.2 The Merchant further agrees that it has voluntarily submitted KYC information and documents as required by the Company from time to time and authorizes the Company to verify such information and to share necessary details with carriers, insurers, customs authorities, importer of record entities, marketplace partners, banks, payment partners, police, courts, government agencies, complainants, or any other relevant entity for compliance, claims processing, dispute handling, fraud review, legal proceedings, or operational processing, in accordance with applicable law.</p>

<p>24.3 You shall be solely responsible for immediately notifying United Couriers Worldwide via email at <a href="mailto:Csd@unitedcouriers.biz">Csd@unitedcouriers.biz</a> of any change in your e-mail address and/or mobile number registered with United Couriers Worldwide and/or any other Personal Information provided by You to United Couriers Worldwide for accessing the Platforms and/or availing the Services and/or purchasing the Products.</p>

<h2>25. DEFINITIONS AND INTERPRETATION</h2>

<p><strong>25.1 Definitions:</strong> For the purposes of this Agreement:</p>

<ul>
    <li><strong>“Affiliate”</strong> means, in relation to a Party, any entity that directly or indirectly controls, is controlled by, or is under common control with that Party.</li>

    <li><strong>“Applicable Law”</strong> means all laws, statutes, rules, regulations, notifications, circulars, orders, trade controls, sanctions, customs laws, tax laws, and governmental requirements applicable to a Party, the Services, or the goods.</li>

    <li><strong>“Confidential Information”</strong> means with respect to each Party, any information or trade secrets, schedules, business plans including, without limitation, commercial information, financial projections, client information, administrative and/or organizational matters of a confidential/secret nature in whatever form which is acquired by, or disclosed to, the other Party pursuant to this Agreement, and includes any tangible or intangible non-public information that is marked or otherwise designated as ‘confidential’, ‘proprietary’, ‘restricted’, or with a similar designation by the disclosing Party at the time of its disclosure to the other Party, or is otherwise reasonably understood to be confidential by the circumstances surrounding its disclosure, but excludes information which: (i) is required to be disclosed in a judicial or administrative proceeding, or is otherwise requested or required to be disclosed pursuant to applicable law or regulation, and (ii) which at the time it is so acquired or disclosed, is already in the public domain or becomes so other than by reason of any breach or non-performance by the other Party of any of the provisions of this Agreement;</li>

    <li><strong>“Force Majeure Event”</strong> includes act of God, war, civil disturbance, terrorism, strike, lockout, fire, flood, explosion, epidemic, pandemic, transport disruption, carrier failure, cyber disruption, customs restriction, export-import policy change, sanction, or government action beyond reasonable control.</li>

    <li><strong>“Intellectual Property”</strong> means all patents, copyrights, trademarks, trade names, service marks, logos, domain names, trade secrets, designs, software, databases, data rights, know-how, inventions, and all allied intellectual property rights and goodwill.</li>

    <li><strong>“Services”</strong> means all domestic and international shipping, carriage facilitation, logistics management, customs support, importer of record services, reverse logistics, COD collection, marketplace support, technology access, and allied services provided or facilitated by the Company.</li>

    <li><strong>“Shipment”</strong> means any parcel, package, consignment, goods, document, or item tendered by or on behalf of the Merchant for any Service.</li>
</ul>

<p><strong>25.2 Interpretation:</strong> Unless the context of this Agreement otherwise requires:</p>

<ol type="a">
    <li>heading and bold typeface are only for convenience and shall be ignored for the purpose of interpretation;</li>

    <li>other terms may be defined elsewhere in the text of this Agreement and, unless otherwise indicated, shall have such meaning throughout this Agreement;</li>

    <li>references to this Agreement shall be deemed to include any amendments or modifications to this Agreement, as the case may be;</li>

    <li>the terms “hereof”, “herein”, “hereby”, “hereto” and derivative or similar words refer to this entire Agreement or specified Clauses of this Agreement, as the case may be;</li>

    <li>references to a particular section, clause, paragraph, sub-paragraph or schedule, exhibit or annexure shall be a reference to that section, clause, paragraph, sub-paragraph or schedule, exhibit or annexure in or to this Agreement;</li>

    <li>reference to any legislation or law or to any provision thereof shall include references to any such law as it may, after the date hereof, from time to time, be amended, supplemented or re-enacted, and any reference to statutory provision shall include any subordinate legislation made from time to time under that provision;</li>

    <li>a provision of this Agreement must not be interpreted against any Party solely on the ground that the Party was responsible for the preparation of this Agreement or that provision, and the doctrine of contra proferentem does not apply vis-à-vis this Agreement;</li>

    <li>references in the singular shall include references in the plural and vice versa; and</li>

    <li>references to the word “include” shall be construed without limitation.</li>
</ol>


 <h2>ANNEXURE A</h2>

  <h2>SERVICE SPECIFICATIONS</h2>

  <p>This Annexure A sets out the service specifications applicable to the Services and is to be read with the Merchant Agreement. Unless expressly stated otherwise, the terms of the Merchant Agreement shall apply.</p>

  <h2>1. SERVICES COVERED</h2>

<p>1.1 This Agreement is a master agreement governing one or more business-to-business and business-to-customer services made available by the Company, including but not limited to logistics management services, shipping aggregation, domestic carriage facilitation, cross-border shipping, customs facilitation, importer of record services, reverse logistics, marketplace management, product marketing support, payment collection, technology access, and all allied services described in the applicable annexures, schedules, dashboard links, and service specifications.</p>

<p>1.2 Subject to compliance with the Merchant Agreement, the Company authorizes the Merchant to access and use the platform, dashboard, website, software, and mobile application solely for booking, managing, tracking, reconciling, communicating, receiving, and administering shipments and related services.</p>

<h2>2. BOOKING METHODS</h2>

<p>2.1 The Merchant may choose between two booking methods for Shipments: United Worldwide Couriers Pickup or Self-Ship.</p>

<ul>
    <li>Under United Worldwide Couriers Pickup, the Company will collect Shipments from the pickup address provided by the Merchant.</li>
    <li>Under Self-Ship, the Merchant must deliver Shipments to the Company’s nearest hub.</li>
</ul>

<h2>3. SHIPMENT HANDLING WORKFLOW</h2>

<p>3.1 Upon arrival at the hub, each Shipment will be scanned, weighed, and sorted according to destination and selected service.</p>

<p>3.2 If there is a discrepancy between the weight declared by the Merchant and the weight recorded at the hub, the Shipment will be placed on hold and the Merchant will be notified by email for approval.</p>

<p>3.3 Once the Merchant approves the recorded weight, the Shipment will proceed to its destination.</p>

<p>3.4 After a Shipment has left the hub, no further weight adjustments will be charged to the Merchant’s account.</p>

<h2>4. INTERNATIONAL SHIPMENTS</h2>

<p>4.1 For international Shipments, after export clearance in India, the Shipment will be customs-cleared in the destination country and received at the local office before being handed to the Last-mile carrier.</p>

<p>4.2 Last-mile carriers vary by country, including USA - UPS, USPS, FedEx; Europe - DPD or national postal networks; and Canada - Canada Post, UPS. These carriers may change from time to time.</p>

<p>4.3 For Shipments routed through branded carrier networks such as DHL, UPS, and FedEx, the Shipment is connected to the carrier’s hub in Delhi and tracking is available on the carrier’s website using the tracking number.</p>

<h2>5. DELIVERY ATTEMPTS AND HANDLING</h2>

<p>5.1 The Company will attempt delivery of all Shipments at least once. Many last-mile Shipping Vendors attempt delivery twice depending on their policies.</p>

<p>5.2 If no one is available to receive a Shipment, the carrier may, at its discretion, leave the Shipment with a neighbour, deposit it at the local post office for customer pickup, or place it in a secure external location outside the house, including a mailbox.</p>


<h2>6. RETURN TO ORIGIN</h2>

<p>6.1 If a Shipment is undeliverable under United Worldwide Couriers Direct Service, it will be returned to the Company’s or its partner’s local office in the destination country.</p>

<p>6.2 RTO charges will be billed to the Merchant’s account.</p>

<p>6.3 In most cases such shipments will be destroyed in the destination country, except for Shipments to the USA and Europe where re-forwarding may be available at an additional cost.</p>

<p>6.4 For branded carrier networks such as DHL, FedEx, and UPS, the carrier may either destroy the Shipment in the destination country per the carrier’s policy or return it to the Merchant in India.</p>

<p>6.5 If the RTO results from failure to pay duties or taxes, the return and all related charges will be billed to the Merchant’s account.</p>

<h2>7. UNDELIVERABLE SHIPMENTS</h2>

<p>A Shipment may be deemed undeliverable for the following reasons: incorrect or incomplete address details at booking or on the Shipment, including missing apartment number or street name; customer refusal to accept delivery; and customer refusal to pay applicable duties and/or taxes.</p>

<h2>8. RTO, STORAGE, AND DISPOSAL</h2>

<p>8.1 The Company reserves the right to return any Shipment not accepted by the Customer and to apply RTO charges at prevailing rates.</p>

<p>8.2 If the Merchant does not accept an RTO Shipment or cannot be reached, the Company may levy demurrage/incidental storage charges for any period exceeding seven working days from initiation of the return, up to forty-five working days from that date.</p>

<p>8.3 If the Customer fails to accept the returned Shipment beyond ten working days from the first RTO undelivered date or first RTO delivery attempt date, the Company may dispose of the Shipment.</p>

<p>8.4 In that case, the Merchant will forfeit any claims related to the Shipment and will remain liable for disposal charges and any other applicable charges, including demurrage/incidental storage charges.</p>

<h2>9. PRICING AND VOLUMETRIC WEIGHT</h2>

<p>9.1 Each individual Shipment is subject to weight and size limits that may differ by Shipping Vendor and destination.</p>

<p>9.2 There is no restriction on the total aggregate weight of all Shipments or on the number of boxes contained in any single Shipment.</p>

<p>9.3 Volumetric weight will be calculated automatically in the booking panel using the formula: length (cm) × breadth (cm) × height (cm) ÷ 5000.</p>

<ul>
    <li>For example, a parcel measuring 30 cm × 25 cm × 40 cm has a volumetric weight of 30 × 25 × 40 ÷ 5000 = 6 kg.</li>
</ul>

<p>9.4 Where:</p>

<ul>
    <li>volumetric weight is 5 kg or less, charges will be based on actual dead weight.</li>
    <li>volumetric weight exceeds 5 kg, charges will be based on whichever is greater, actual weight or volumetric weight.</li>
</ul>

<p>9.5 Certain items that require special handling, as defined in the internal operational guidelines, will incur an additional special handling fee.</p>

<h2>10. FEES AND CHARGES</h2>

<p>10.1 The Merchant shall pay all subscription fees, shipping charges, freight, RTO charges, reverse pickup charges, COD handling charges, customs-related service fees, importer of record charges, storage charges, demurrage, incidental expenses, surcharges, accessorial charges, address correction fees, penalties, taxes, and all other amounts applicable to the Services as set out in the Agreement, any applicable annexure, rate sheet, live calculator, dashboard, invoice, commercial proposal, or written communication issued by the Company.</p>

<p>10.2 Unless expressly stated otherwise, all fees are exclusive of taxes and all applicable taxes, including GST, shall be charged in addition.</p>

<p>10.3 The Company may add new services for additional charges or revise existing charges, rates, surcharges, accessorial charges, service conditions, or fee structures at any time by notice through dashboard, email, mobile application, rate card, calculator link, annexure, or any other official communication channel.</p>

<p>10.4 Unless the Merchant objects in writing before the stated effective date and ceases use of the affected Services, the revised fee structure shall be deemed accepted.</p>


<h2>11. INVOICING AND PAYMENT</h2>

<p>11.1 The Company may issue invoices periodically, including mid-month, month-end, or such other cycle as determined by the Company.</p>

<p>11.2 The Merchant shall verify invoice contents promptly and, unless a shorter period is prescribed for a specific service model, shall raise any bona fide dispute within five working days of invoice availability and pay undisputed amounts within seven days from invoice date or such other due date specified in writing.</p>

<p>11.3 Failure to raise a dispute within the prescribed period shall constitute deemed acceptance of the invoice.</p>

<p>11.4 If the Merchant fails to pay any amount when due, the Company may suspend shipping, retain and adjust outstanding amounts against COD remittances, wallet balances, credits, deposits, refunds, or any amounts payable to the Merchant, retain custody of shipments, re-route shipments, levy interest at 18 percent per annum from the due date until realization, forfeit security deposit or wallet balance where contractually permitted, and dispose of shipments in accordance with the Merchant Agreement where the Merchant fails to regularize defaults within the applicable period.</p>

<p>11.5 Freight charges become due upon pickup, shipment initiation, or RTO initiation, as applicable, whether or not already invoiced.</p>

<h2>12. COD REMITTANCE</h2>

<p>12.1 For shipments booked under cash on delivery, the Merchant appoints the Company as a limited collection agent solely for the purpose of collecting the COD amount from the consignee through the Company’s logistics vendors and remitting the balance after deduction of applicable freight, service fees, handling fees, taxes, offsets, reversals, and other lawful deductions.</p>

<p>12.2 The Company shall have no title in the goods.</p>

<p>12.3 Save as otherwise agreed, COD remittance may be made within eight days from delivery of the relevant shipment or in accordance with the remittance cycle then followed by the Company, subject always to reconciliation, carrier remittance, fraud review, dispute review, status verification, valid bank details, and absence of offset rights.</p>

<p>12.4 Where a shipment status is wrongly updated as delivered, a COD amount has been wrongly remitted, a COD order is subsequently modified, a dispute is raised by a buyer, a fraud event is suspected, or any amount is otherwise found to have been incorrectly credited, the Company may deduct such amount from future remittances, wallet balances, or other monies payable to the Merchant.</p>

<p>12.5 If any COD amount remains unremitted for 365 days from its due date for reasons not attributable to the Company, including incorrect bank details or failure of the Merchant to complete compliance formalities, the Merchant waives all claims to such amount and the Company may forfeit the same.</p>

<h2>13. PREPAID AND CREDIT MODELS</h2>

<p>13.1 Where the Merchant operates on a prepaid model, the Merchant shall maintain sufficient shipping credits in its wallet or account before availing Services.</p>

<p>13.2 Freight and other charges may be auto-adjusted against wallet balances, and any negative balance may be set off against COD or other receivables.</p>

<p>13.3 Credit balance may be used only for booking shipments and may be forfeited if no shipment is booked for a continuous period of three years from the last shipment date, subject to applicable law.</p>

<p>13.4 Refunds, if approved, may be restricted to the original source or mode of payment and may be conditioned upon KYC compliance and any surcharge or deduction permitted by law or contract.</p>

<p>13.5 Where the Merchant is granted secured postpaid or rolling credit, such facility is discretionary and may be increased, reduced, suspended, or withdrawn at any time.</p>

<p>13.6 Used credit may be adjusted from upcoming remittances, wallet balances, deposits, or any amount payable to the Merchant.</p>

<p>13.7 Non-payment shall entitle the Company to suspend Services and exercise all contractual and legal recovery rights.</p>

<h2>14. CLAIMS AND COMPENSATION</h2>

<p>14.1 If a delivery issue occurs, the Merchant may be eligible to file a claim and receive compensation as set out in the Merchant Agreement.</p>

<p>14.2 To initiate a claim, the Merchant must provide the United Worldwide Couriers airway bill (AWB) number and all supporting documents to <a href="mailto:Csd@unitedcouriers.biz">Csd@unitedcouriers.biz</a>.</p>

<p>14.3 Claims for Shipments routed via branded carrier networks such as DHL, UPS, and FedEx will be processed under the same policy, subject to carrier-specific timelines.</p>

<p>14.4 If no first scan is recorded by the destination last-mile carrier in the destination country, compensation is limited to 30 percent of the invoice value plus shipping charges, capped at Rs. 1,000 for Shipments weighing 0 - 100 g and capped at Rs. 4,000 for Shipments over 100 g, inclusive of shipping charges.</p>

<p>14.5 Where the last-mile carrier’s delivery scan is not available or the Shipment is otherwise lost in transit, reimbursement may be claimed if the Merchant has already refunded the buyer for non-delivery, and the buyer-seller chat and proof of refund must be submitted along with the required AWB and documents.</p>

<p>14.6 If pickup was carried out by United Worldwide Couriers’s team and the package is lost before inwarding at the Delhi hub, refunds will be made in accordance with the refund policy.</p>

<p>14.7 If pickup was performed by a third-party logistics provider, only the shipping charges will be refunded.</p>

<p>14.8 All claims are subject to verification and acceptance under the Claim Policy, and incomplete or fraudulent documentation may result in denial of the claim.</p>

<p>14.9 Where applicable, recoveries from carriers or third parties will be applied against any amounts payable, and any net recovery will be distributed in accordance with the Claim Policy.</p>

<p>14.10 Claims are payable only under the specific circumstances described in the Claims and Compensation section, and no claims are maintainable for the other listed situations, including incorrect or incomplete booking information, customs seizure or detention, unsuccessful delivery attempts, inadequate packaging, carrier non-refund situations, or records showing delivery.</p>

<p>14.11 The Claim Policy applies only to United Worldwide Couriers Self-Services, including United My Delivery, United Air Premium, United Grd Premium, United Air Express, United Prior Post, United Eco Post, United My Pickup.</p>

<p>14.12 The Company will endeavour to close each refund claim within fifteen working days from receipt of the complaint, subject to additional time where investigation requires cooperation from Shipping Vendors.</p>

<p>14.13 For United Worldwide Couriers Direct services only, if a Shipment has not been delivered within thirty days of pickup and there is no dispute, the Merchant will be entitled to a refund under the Merchant Agreement.</p>

<p>14.14 If a dispute arises, the thirty-day period will be measured from one day after the dispute is resolved. The thirty-day refund entitlement does not apply to other services or where additional documents are required.</p>

<h2>15. CLAIM CONDITIONS</h2>

<p>15.1 Claims must be made promptly, and in any event the Company will not consider claims submitted more than sixty working days after the date of the inward scan.</p>

<p>15.2 If the Company notifies the Merchant that a Shipment is stuck, undelivered, or under RTO and the Merchant does not respond within seven working days, the related claim will not be considered.</p>

<p>15.3 No claim will be accepted for Shipments showing a scan status of out for delivery or awaiting delivery unless the Merchant provides chat records or screenshots evidencing the buyer’s non-receipt.</p>

<p>15.4 For claims alleging non-connectivity, the Merchant must submit the signed pickup manifest for the disputed Shipment within three working days of pickup.</p>

<p>15.5 For damage, pilferage, tampering, crushing, or leakage, the recipient must record negative remarks on the proof of delivery at the time of delivery to preserve the claim.</p>

<p>15.6 Claims for damage, pilferage, tampering, crushing, or leakage will be entertained only if made within forty-eight hours of delivery or receipt and only where the outer packaging applied by the Company or the Shipping Vendor is damaged, altered, or tampered with.</p>

<p>15.7 Claims for Shipments carried under branded networks remain subject to the evidence and submission requirements above, but will also follow the timelines and processes mandated by the relevant carrier.</p>

<p>15.8 Where a customer who is not registered under the GST regime has not transacted for 365 consecutive days, any wallet balance held will be refunded to that customer in accordance with the refund procedures.</p>

<h2>16. HANDLING AND RELATED CHARGES</h2>

<p>16.1 Shipping charges for branded services will be determined on a case-by-case basis.</p>

<p>16.2 If a Shipment is detained by customs, additional charges will apply based on actual costs.</p>

<p>16.3 Handling charges are set out for a maximum Shipment weight of 22 KGs for USA, 30 KGs for UK, 30 KGs for Europe, 20 KGs for Australia/NZ, and 20 KGs for Canada, based on volume or dead weight, whichever is higher.</p>

<p>16.4 If package volume or dead weight is higher than the above slabs, there will be an additional handling charge of Rs. 4,000 plus GST, up to a maximum of additional 10 KGs higher.</p>

<p>16.5 Remote area charges for branded services will be disclosed at the time of booking.</p>

<p>16.6 For non-branded services, remote area charges will apply as per the respective service provider’s policy.</p>

<p>16.7 Tariff and duty adjustments may be deducted or refunded by the Company and applied directly to the wallet balance without prior notice.</p>

<p>16.8 Reforwarding charges for branded services are as follows: USA - $15 for up to 500 g, $25 for 500 g to 5 kg; Europe - €20 for up to 500 g, €30 for 500 g to 5 kg; UK - £15 for up to 500 g, £21 for 500 g to 5 kg; Canada - CAD 20 for up to 500 g, CAD 30 for 500 g to 5 kg.</p>

<p>16.9 For non-branded services, reforwarding charges will follow the applicable service provider’s policy.</p>

<p>16.10 If a Shipment is found to be misdeclared, the Company may levy an administrative charge of INR 5,000 per Shipment and recover it from the Merchant’s wallet or settlement without prior notice.</p>

<p>16.11 RTO requests will not be entertained for misdeclared Shipments.</p>

<p>16.12 The Merchant may opt to self-pick-up the seized Shipment within seven working days of intimation.</p>

<p>16.13 If the Merchant fails to do so, the Company may destroy or dispose of the Shipment through authorized channels at the Merchant’s sole risk, cost, and liability, with no further claim against the Company.</p>

<p>16.14 If a Shipment contains Prohibited or Restricted Items, the Merchant will be in material breach of the Merchant Agreement.</p>

<p>16.15 The Company may impose a penalty of INR 50,000 per Shipment, recoverable immediately from the Merchant’s wallet or settlement without prior notice.</p>

<p>16.16 No RTO or self-pickup requests will be accepted for such shipments, and they may be seized and destroyed or disposed of through authorized channels without liability to the Company.</p>

<p>16.17 Repeat offences may result in permanent suspension or banning of the Merchant’s account.</p>

<p>16.18 A special handling fee will apply to certain goods that require specialised handling as defined in the Company’s internal operational guidelines.</p>


<h2>17. LIABILITY NOTE</h2>

<p>17.1 The liability of United Worldwide Couriers in relation to the Services is strictly limited to the extent expressly provided under the Merchant Agreement, applicable Terms and Conditions, annexures, policies, and service-specific provisions.</p>

<p>17.2 The Platform and Services are provided on an as is, as available, and reasonable efforts basis, and the Company does not guarantee uninterrupted, error-free, secure, or continuous operation.</p>

<p>17.3 The Company does not independently verify, validate, endorse, or authenticate information, declarations, listings, content, documents, data, or materials provided by Users, Merchants, customers, consignees, Shipping Vendors, or third parties.</p>

<p>17.4 Shipments are not insured unless separately purchased by the Merchant.</p>

<h2>18. COMMUNICATIONS</h2>

<p>18.1 All notices, communications, updates, invoices, rate revisions, dashboard alerts, service notifications, legal notices, and operational instructions may be issued by the Company through email, dashboard, mobile application, SMS, WhatsApp, registered mobile number, support ticketing system, courier, or any other officially designated communication channel.</p>

<p>18.2 The Merchant consents to receive communications through such channels.</p>

<p>18.3 The Merchant authorizes the Company to verify KYC information and to share necessary details with carriers, insurers, customs authorities, importer of record entities, marketplace partners, banks, payment partners, police, courts, government agencies, complainants, or any other relevant entity for compliance, claims processing, dispute handling, fraud review, legal proceedings, or operational processing, in accordance with applicable law.</p>

<h2>19. CROSS-REFERENCE</h2>

<p>19.1 This Annexure A is drafted only from the language already present in the Merchant Agreement and is intended to be read together with the Merchant Agreement, any applicable annexures, schedules, rate sheets, SOPs, SLAs, dashboard notices, and service-specific provisions.</p>

<p>19.2 In case of inconsistency, the relevant annexure shall prevail only for the subject matter of that annexure, and service-specific terms, SOPs, rate cards, and web-based links may prevail over general terms where expressly stated.</p>


<h2>ANNEXURE B</h2>
<h2>PROHIBITED, RESTRICTED, DANGEROUS, AND NON-COMPLIANT GOODS</h2>

<h3>Dangerous Goods:</h3>

<ol>
    <li>Oil-based paint and thinners (flammable liquids)</li>
    <li>Industrial solvents</li>
    <li>Insecticides, garden chemicals (fertilizers, poisons)</li>
    <li>Lithium batteries</li>
    <li>Magnetized materials</li>
    <li>Machinery (chain saws, outboard engines containing fuel or that have contained fuel)</li>
    <li>Fuel for camp stoves, lanterns, torches or heating elements</li>
    <li>Automobile batteries</li>
    <li>Infectious substances</li>
    <li>Any compound, liquid or gas that has toxic and/or infectious characteristics</li>
    <li>Bleach</li>
    <li>Flammable adhesives</li>
    <li>Arms, ammunitions or any weapon with blade (including but not limited to air guns, flares, gunpowder, firework, knives, swords and antique weaponry)</li>
    <li>Dry ice (Carbon Dioxide, Solid)</li>
    <li>Any Aerosols, liquids and/or powders or any other flammable substances classified as Dangerous Goods for transport by Air</li>
    <li>Alcohol</li>
    <li>Tobacco and tobacco related products</li>
    <li>Electronic cigarettes</li>
    <li>Ketamine</li>
</ol>


<h3>Restricted Items:</h3>

<ol>
    <li>
        Precious stones, gems and jewellery (including but not limited to antiques,
        bullion (of any precious metal), diamonds, gold, silver, platinum, trophies
        related to animal hunting, semi-precious stones in any form (including bricks).
    </li>
    <li>Uncrossed (bearer) drafts / cheque, currency and coins</li>
    <li>Poison</li>
    <li>Firearms, explosives and military equipment.</li>
    <li>Hazardous and radioactive material</li>
    <li>Foodstuff and liquor</li>
    <li>Any pornographic material</li>
    <li>
        Any hazardous chemical items (including but not limited to radioactive
        material, special chemicals, material, equipment and technologies (SCOMET)
        items, hazardous/chemical waste, corrosive items (acids), machine parts
        containing oil, grease, toner).
    </li>
    <li>
        Any plants and their related products (including but not limited to
        oxidizing substances, sand/soils/ores, sandalwood, wood, wood pulp,
        edible oils, de-oiled groundnut, endangered species of plants and their
        parts, asbestos).
    </li>
    <li>
        Any drugs and medicines (including but not limited to cocaine, cannabis,
        LSD, morphine, opium, psychotropic substances, and such other drugs,
        poisonous goods, contraband (such as illegal/illicit and counterfeit drugs).
    </li>
    <li>
        Any animals and human body-related items/products (including but not limited
        to livestock, cremated or disinterred human being’s remains, human beings
        and any animal embryos, human being and any animal remains, human beings
        and any animals’ corpses, organs/body parts of human beings and any animals).
    </li>
</ol>

<h2>Counterfeit or Fraudulent Products/Shipments</h2>

<p>United Worldwide Couriers is committed to conducting its business in compliance with all applicable laws, regulations, industry standards, and ethical requirements. The Company maintains a <strong>zero-tolerance policy</strong> towards counterfeit, fraudulent, misrepresented, cloned, duplicate, unauthorized, or otherwise unlawful products or shipments, including products or shipments whose origin, authenticity, quality, ownership, or description has been falsely represented.</p>

<p>If United Worldwide Couriers reasonably believes or determines that the Merchant or any of its customers is shipping, selling, attempting to ship, or has previously shipped or sold any counterfeit or fraudulent product or Shipment, including counterfeit electronic products such as mobile phones, smart watches, or similar devices, the Company shall have the right, without prejudice to any other contractual or legal remedy, to take one or more of the following actions:</p>

<p><strong>1.</strong> Seize or place the Shipment on hold and retain custody of the suspected counterfeit or fraudulent product/Shipment pending investigation or further instructions.</p>

<p><strong>2.</strong> Report the matter to the appropriate authorities, including any government authority, customs authority, regulatory body, law-enforcement agency, or police station, where the Company considers such reporting appropriate or is required to do so by law.</p>

<p><strong>3.</strong> Suspend, restrict, terminate, or blacklist the Merchant and/or the relevant customer from using or transacting through United Worldwide Couriers.</p>

<p><strong>4.</strong> Levy liquidated damages of up to INR 10,000 per counterfeit or fraudulent Shipment, plus applicable GST, towards estimated legal, investigation, administrative, compliance, and related expenses incurred by the Company. Where the Company's actual documented expenses exceed INR 10,000, the Company may recover such additional amounts to the extent permitted under Applicable Law.</p>

<p><strong>5.</strong> Recover additional compensation of up to INR 1,00,000, plus applicable GST, towards reputational harm, goodwill impairment, business disruption, investigation, and related losses suffered or reasonably incurred by United Worldwide Couriers as a result of the counterfeit or fraudulent activity, subject to Applicable Law.</p>

<p><strong>6.</strong> Require the Merchant to provide an additional security deposit or financial security in such amount as may reasonably be determined by the Company to cover potential losses, liabilities, claims, penalties, investigation costs, or other expenses arising from the suspected counterfeit or fraudulent Shipment.</p>

<p><strong>7.</strong> Block, retain, suspend, or set off COD amounts, wallet balances, settlements, credits, refunds, or other monies belonging or payable to the Merchant, to the extent reasonably required to secure or recover amounts due to the Company or arising from the suspected counterfeit or fraudulent activity.</p>

<p><strong>8.</strong> Seize, retain, or dispose of other products or Shipments belonging to the Merchant or the relevant customer that are in the custody or possession of United Worldwide Couriers or its logistics partners, where such action is reasonably necessary for investigation, compliance, legal proceedings, or recovery. Subject to Applicable Law and any direction of a competent authority, where the seized products remain unclaimed or unresolved for <strong>thirty (30) days from the date of seizure</strong>, the Company may dispose of such products through an authorized process.</p>

<p><strong>9.</strong> Forfeit any security deposit or financial security maintained with United Worldwide Couriers to the extent necessary to recover amounts lawfully due, including applicable damages, penalties, costs, expenses, claims, or liabilities arising from the counterfeit or fraudulent activity.</p>

<p><strong>10.</strong> The Merchant shall remain fully responsible for any claims, losses, penalties, duties, taxes, legal expenses, regulatory action, investigation costs, third-party claims, and other liabilities arising from or relating to counterfeit or fraudulent products or Shipments tendered by the Merchant or its customers.</p>

<p><strong>11.</strong> The exercise of any of the above rights shall be without prejudice to any other rights or remedies available to United Worldwide Couriers under this Agreement, Applicable Law, or any applicable carrier, customs, regulatory, or governmental requirement.</p>

<p>For avoidance of doubt, the Merchant shall be solely responsible for ensuring that all products and Shipments tendered through United Worldwide Couriers are genuine, lawfully sourced, accurately described, appropriately documented, and compliant with all Applicable Laws and carrier requirements.</p>


<h2>Disputed Shipments/Cases</h2>

<p>United Worldwide Couriers, in its sole discretion, shall have the right to levy damages, charges, or other applicable amounts, together with applicable GST, on the Merchant in relation to any Shipment or case that is disputed, questioned, investigated, or subject to a claim by any courier company, customer, consignee, third party, governmental authority, regulatory body, or department.</p>

<p>The amount of such damages, charges, or other applicable amounts may be determined by United Worldwide Couriers on a case-by-case basis, taking into consideration the nature and circumstances of the dispute, the actual or potential loss, liability, cost, expense, penalty, claim, investigation, or other exposure suffered or incurred by the Company, and may vary from case to case, subject to Applicable Law.</p>

<h2>Shipping Non-Essential Items in Government-Prohibited Areas</h2>

<p>If United Worldwide Couriers reasonably believes or determines that the Merchant is shipping or has shipped non-essential items or products into, from, or within any restricted or prohibited area, including any red zone, containment zone, restricted zone, or other area declared or notified as restricted or prohibited by the Central Government or any relevant State Government or governmental authority in India, the Company shall have the right, without prejudice to any other rights or remedies available under this Agreement or Applicable Law, to take appropriate action in relation to such Shipment.</p>

<p>United Worldwide Couriers may levy a penalty or liquidated damages of up to <strong>INR 10,000 per Shipment</strong>, together with applicable GST, towards estimated legal, compliance, administrative, investigation, and related expenses incurred by the Company and/or reputational or goodwill loss arising from such Shipment.</p>

<p>Where the actual damages, losses, liabilities, penalties, legal expenses, investigation costs, or other expenses incurred by United Worldwide Couriers exceed INR 10,000, the Company may recover such additional actual amounts from the Merchant to the extent permitted under Applicable Law.</p>

<p>The Merchant shall remain responsible for ensuring that all Shipments comply with applicable governmental restrictions, transportation restrictions, emergency orders, containment measures, and other applicable laws, notifications, directions, and requirements governing the origin, destination, nature, and movement of the Shipment.</p>

<p>The Company’s rights under this clause shall be without prejudice to any other contractual, statutory, regulatory, or legal rights and remedies available to United Worldwide Couriers.</p>


<h2>ANNEXURE C</h2>
<h2>INTERNATIONAL / CROSS-BORDER TERMS AND CONDITIONS</h2>

<p>This Annexure C sets out the international / cross-border service terms and conditions applicable to the Services and is to be read with the Merchant Agreement. Unless expressly stated otherwise, the terms of the Merchant Agreement shall apply.</p>

<h2>1. SCOPE</h2>

<p><strong>1.1</strong> This Agreement governs the domestic and international shipping, reverse logistics, customs facilitation, marketplace support, importer of record services, payment collection, technology access, and all other allied services offered or facilitated by the Company from time to time, including any services specified in applicable annexures, schedules, rate cards, service specifications, or other written communications issued by the Company.</p>

<p><strong>1.2</strong> Subject to the terms and conditions of this Agreement, the Company authorizes the Merchant to access and use the Platform, dashboard, website, software, and mobile application solely for the purposes of booking, managing, tracking, reconciling, communicating, receiving, and administering Shipments and related Services.</p>

<p><strong>1.3</strong> The Merchant shall use the Platform and Services only for lawful business purposes and in accordance with this Agreement, Applicable Law, and any service-specific terms, policies, operating procedures, or instructions communicated by the Company.</p>

<h2>2. INTERNATIONAL SHIPMENT FLOW</h2>

<p><strong>2.1</strong> For international Shipments, following export clearance in India, the Shipment shall be subject to customs clearance in the destination country and shall be received at the applicable local office before being handed over to the designated last-mile carrier.</p>

<p><strong>2.2</strong> Last-mile carriers may vary depending on the destination country and applicable service. Such carriers may include, without limitation, UPS, USPS, and FedEx in the USA; DPD or national postal networks in Europe; and Canada Post or UPS in Canada. The Company reserves the right to change or substitute last-mile carriers from time to time based on operational requirements, service availability, carrier policies, or destination-specific conditions.</p>

<p><strong>2.3</strong> For Shipments routed through branded carrier networks, including DHL, UPS, and FedEx, the Shipment may be connected to the applicable carrier's hub in Delhi. Where applicable, tracking information may be made available through the relevant carrier's tracking system or website using the applicable tracking number.</p>

<h2>3. DELIVERY HANDLING</h2>

<p><strong>3.1</strong> The Company shall make at least one delivery attempt for each Shipment. Depending on the applicable Shipping Vendor, destination, service type, and carrier policy, additional delivery attempts, including a second delivery attempt, may be made at the carrier's discretion.</p>

<p><strong>3.2</strong> If the consignee or recipient is unavailable to receive a Shipment, the applicable carrier may, subject to its operational policies and applicable law, leave the Shipment with a neighbour, deposit it at a local post office or designated collection location for customer pickup, or place it in a reasonably secure external location, including a mailbox or other designated delivery location.</p>

<h2>4. RETURN TO ORIGIN</h2>

<p><strong>4.1</strong> If a Shipment booked under United Worldwide Couriers Direct Service is determined to be undeliverable, the Shipment may be returned to the Company's or its designated partner's local office in the destination country.</p>

<p><strong>4.2</strong> All applicable Return to Origin (RTO) charges, including transportation, handling, return processing, customs, storage, duties, taxes, and other applicable charges, shall be billed to the Merchant's account.</p>

<p><strong>4.3</strong> In most cases, undeliverable Shipments under United Worldwide Couriers Direct Service may be destroyed or otherwise disposed of in the destination country in accordance with applicable law and operational or carrier requirements. For Shipments destined for the USA or Europe, re-forwarding may be available at an additional cost, subject to availability and applicable service conditions.</p>

<p><strong>4.4</strong> For Shipments routed through branded carrier networks, including DHL, FedEx, and UPS, the applicable carrier may, in accordance with its policies and applicable law, either destroy or otherwise dispose of the Shipment in the destination country or return the Shipment to the Merchant in India. All applicable return, customs, handling, storage, duty, tax, and other related charges shall be payable by the Merchant.</p>

<p><strong>4.5</strong> Where an RTO occurs due to the Merchant's or consignee's failure to pay applicable customs duties, taxes, tariffs, or other governmental charges, the Merchant shall be solely responsible for all return costs and related charges, including transportation, customs, handling, storage, demurrage, duties, taxes, penalties, and any other applicable expenses.</p>

<h2>5. UNDELIVERABLE SHIPMENTS</h2>

<p><strong>5.1</strong> A Shipment may be deemed undeliverable where delivery cannot be completed for reasons including, without limitation, incorrect, incomplete, or inaccurate address details provided at the time of booking or appearing on the Shipment, including a missing apartment number, unit number, street name, postal code, or other required address information.</p>

<p><strong>5.2</strong> A Shipment may also be deemed undeliverable where the customer or consignee refuses to accept the Shipment or refuses to pay any applicable customs duties, taxes, tariffs, or other charges required for delivery.</p>

<h2>6. RTO, STORAGE, AND DISPOSAL</h2>

<p><strong>6.1</strong> The Company reserves the right to return any Shipment that is not accepted by the Customer or otherwise becomes undeliverable and to apply the applicable Return to Origin (RTO) charges at the prevailing rates.</p>

<p><strong>6.2</strong> If the Merchant fails to accept an RTO Shipment or cannot be contacted or reached, the Company may levy demurrage, incidental storage, handling, or other applicable charges for any period exceeding seven (7) working days from the initiation of the return, up to a maximum period of forty-five (45) working days from such date.</p>

<p><strong>6.3</strong> If the Customer fails to accept or receive the returned Shipment for more than ten (10) working days from the date of the first RTO undelivered status or the first RTO delivery attempt, the Company may, subject to Applicable Law and applicable carrier policies, dispose of or otherwise deal with the Shipment through an authorized process.</p>

<p><strong>6.4</strong> Upon disposal of a Shipment under this Clause, the Merchant shall forfeit any claim, refund, compensation, or other entitlement relating to such Shipment and shall remain liable for all applicable disposal, demurrage, incidental storage, handling, transportation, and other charges incurred in connection with the Shipment.</p>

<h2>7. CUSTOMS AND CHARGES</h2>

<p><strong>7.1</strong> If a Shipment is detained, held, examined, delayed, or otherwise subject to action by customs or any other governmental or regulatory authority, the Merchant shall be responsible for all additional charges incurred in connection with such action, including customs, storage, detention, demurrage, handling, inspection, documentation, transportation, duties, taxes, penalties, and other actual costs, as applicable.</p>

<p><strong>7.2</strong> Any adjustment arising from a difference in applicable tariffs, customs duties, taxes, refunds, rebates, or other governmental charges may be deducted from or credited to the Merchant's wallet or account by the Company, without prior notice, subject to reconciliation and Applicable Law.</p>

<p><strong>7.3</strong> Subject to availability and applicable service conditions, the reforwarding charges for branded services shall be as follows:</p>

<p><strong>USA:</strong> USD 15 for Shipments up to 500 g and USD 25 for Shipments from 500 g up to 5 kg.</p>

<p><strong>Europe:</strong> EUR 20 for Shipments up to 500 g and EUR 30 for Shipments from 500 g up to 5 kg.</p>

<p><strong>UK:</strong> GBP 15 for Shipments up to 500 g and GBP 21 for Shipments from 500 g up to 5 kg.</p>

<p><strong>Canada:</strong> CAD 20 for Shipments up to 500 g and CAD 30 for Shipments from 500 g up to 5 kg.</p>

<p><strong>7.4</strong> For non-branded services, reforwarding charges shall be determined in accordance with the applicable rates, policies, terms, and conditions of the relevant service provider or Shipping Vendor.</p>

<h2>8. HANDLING AND SPECIAL CHARGES</h2>

<p><strong>8.1</strong> Shipping charges for branded services shall be determined on a case-by-case basis based on the destination, service type, Shipment characteristics, applicable carrier rates, and other operational factors.</p>

<p><strong>8.2</strong> Handling charges for branded services shall apply to a maximum Shipment weight of 22 KGs for the USA, 30 KGs for the UK, 30 KGs for Europe, 20 KGs for Australia/NZ, and 20 KGs for Canada, calculated on the basis of volumetric weight or actual dead weight, whichever is higher.</p>

<p><strong>8.3</strong> Where the volumetric weight or actual dead weight of a Shipment exceeds the applicable weight slab specified above, an additional handling charge of <strong>INR 4,000 plus applicable GST</strong> shall apply for additional weight of up to 10 KGs above the applicable slab.</p>

<p><strong>8.4</strong> Remote area charges applicable to branded services shall be disclosed to the Merchant at the time of booking or as otherwise communicated through the applicable booking platform.</p>

<p><strong>8.5</strong> For non-branded services, remote area charges shall be applicable in accordance with the rates, policies, and terms of the respective service provider or Shipping Vendor.</p>

<p><strong>8.6</strong> Certain goods requiring specialised handling, processing, packaging, transportation, documentation, or other operational treatment, as determined under the Company's internal operational guidelines, shall be subject to an applicable special handling fee.</p>

<h2>9. CLAIMS AND COMPENSATION</h2>

<p><strong>9.1</strong> Claims relating to Shipments routed through branded carrier networks, including DHL, UPS, and FedEx, shall be processed in accordance with the applicable Claim Policy, subject to the evidence requirements, timelines, procedures, exclusions, and conditions prescribed by the relevant carrier.</p>

<p><strong>9.2</strong> To initiate a claim, the Merchant must provide the United Worldwide Couriers airway bill (AWB) number together with all relevant supporting documents by email to <strong>Csd@unitedcouriers.biz</strong>.</p>

<p><strong>9.3</strong> Where no first scan is recorded by the destination last-mile carrier in the destination country, compensation shall be limited to <strong>30% of the invoice value plus applicable shipping charges</strong>, subject to a maximum amount of <strong>INR 1,000</strong> for Shipments weighing from 0 to 100 g and <strong>INR 4,000</strong> for Shipments weighing more than 100 g. Such maximum amounts shall be inclusive of shipping charges.</p>

<p><strong>9.4</strong> Where the last-mile carrier's delivery scan is unavailable or the Shipment is otherwise lost in transit, the Merchant may claim reimbursement provided that the Merchant has already refunded the buyer for the non-delivery. The Merchant must submit the buyer-seller communication or chat records and proof of refund, together with the applicable AWB and other supporting documents.</p>

<p><strong>9.5</strong> Where pickup was carried out by the United Worldwide Couriers team and the Shipment is lost before inwarding at the Delhi hub, any applicable refund shall be processed in accordance with the applicable refund and Claim Policy.</p>

<p><strong>9.6</strong> Where pickup was performed by a third-party logistics provider (3PL), the Merchant shall be entitled only to a refund of the applicable shipping charges, subject to verification and the applicable Claim Policy.</p>

<p><strong>9.7</strong> All claims shall be subject to verification, investigation, and acceptance under the applicable Claim Policy. Submission of incomplete, inaccurate, misleading, or fraudulent documentation may result in rejection or denial of the claim.</p>

<p><strong>9.8</strong> Where applicable, any recovery received from a carrier, Shipping Vendor, insurer, third party, or other source shall first be applied against amounts payable or losses incurred in connection with the Shipment. Any net recovery shall be dealt with in accordance with the applicable Claim Policy.</p>

<p><strong>9.9</strong> Claims must be submitted promptly. In any event, the Company shall not consider a claim submitted more than <strong>sixty (60) working days</strong> after the date of the inward scan, unless otherwise required by Applicable Law or the applicable carrier's mandatory claim process.</p>

<p><strong>9.10</strong> If the Company notifies the Merchant that a Shipment is stuck, undelivered, or under Return to Origin (RTO), and the Merchant fails to respond within <strong>seven (7) working days</strong> from such notification, the related claim may be rejected or deemed not maintainable.</p>

<p><strong>9.11</strong> No claim shall be accepted for a Shipment showing a tracking status of <strong>"out for delivery"</strong> or <strong>"awaiting delivery"</strong> unless the Merchant provides sufficient evidence, including buyer-seller chat records, screenshots, or other documentary evidence, demonstrating that the buyer did not receive the Shipment.</p>

<p><strong>9.12</strong> For claims alleging non-connectivity, including circumstances where a Shipment was picked up but was not scanned, inwarded, or connected at the applicable hub, the Merchant must submit the duly signed pickup manifest for the disputed Shipment within <strong>three (3) working days</strong> from the date of pickup.</p>

<p><strong>9.13</strong> In cases involving damage, pilferage, tampering, crushing, leakage, or similar physical issues, the recipient must record clear and specific negative remarks on the proof of delivery (POD) at the time of delivery in order to preserve the claim.</p>

<p><strong>9.14</strong> Claims relating to damage, pilferage, tampering, crushing, or leakage shall be entertained only if submitted within <strong>forty-eight (48) hours</strong> from delivery or receipt and where the outer packaging applied by the Company or the relevant Shipping Vendor is demonstrably damaged, altered, opened, or tampered with. Where the outer packaging remains intact, such claims may not be accepted.</p>

<p><strong>9.15</strong> Claims for Shipments carried through branded carrier networks shall remain subject to the evidence and submission requirements specified above and shall additionally be governed by the timelines, procedures, exclusions, and claim requirements prescribed by the relevant carrier.</p>

<p><strong>9.16</strong> The Claim Policy shall apply only to United Worldwide Couriers Self-Services, including <strong>United My Delivery, United Air Premium, United Grd Premium, United Air Express, United Prior Post, United Eco Post, and United My Pickup</strong>.</p>

<p><strong>9.17</strong> The Company shall endeavour to close each refund or compensation claim within <strong>fifteen (15) working days</strong> from receipt of the complaint and all required supporting documents. Where investigation requires cooperation, confirmation, or documentation from a Shipping Vendor, carrier, customs authority, or other third party, additional time may be required.</p>

<p><strong>9.18</strong> For United Worldwide Couriers Direct Services only, where a Shipment has not been delivered within <strong>thirty (30) days</strong> from the date of pickup and no dispute is pending in relation to the Shipment, the Merchant shall be entitled to a refund in accordance with the Merchant Agreement and applicable Claim Policy.</p>

<p><strong>9.19</strong> Where a dispute arises in relation to the Shipment, the thirty (30) day period referred to above shall be calculated from the day immediately following the date on which such dispute is finally resolved.</p>

<p><strong>9.20</strong> The thirty (30) day refund entitlement specified above shall apply only to United Worldwide Couriers Direct Services and shall not apply to other services or in circumstances where additional documents, verification, investigation, carrier confirmation, or other information is required to determine the claim.</p>

<h2>10. LIABILITY AND RISK</h2>

<p><strong>10.1</strong> The liability of United Worldwide Couriers in relation to the Services is strictly limited to the extent expressly provided under the Merchant Agreement, applicable Terms and Conditions, annexures, policies, and service-specific provisions.</p>

<p><strong>10.2</strong> The Platform and Services are provided on an “as is”, “as available”, and “reasonable efforts” basis, and the Company does not guarantee uninterrupted, error-free, secure, or continuous operation.</p>

<p><strong>10.3</strong> The Company does not independently verify, validate, endorse, or authenticate information, declarations, listings, content, documents, data, or materials provided by Users, Merchants, customers, consignees, Shipping Vendors, or third parties.</p>

<p><strong>10.4</strong> Shipments are not insured unless separately purchased by the Merchant.</p>

<h2>11. CROSS-REFERENCE</h2>

<p><strong>11.1</strong> This Annexure C is drafted only from the language already present in the Merchant Agreement and is intended to be read together with the Merchant Agreement, any applicable annexures, schedules, rate sheets, SOPs, SLAs, dashboard notices, and service-specific provisions.</p>

<p><strong>11.2</strong> In case of inconsistency, the relevant annexure shall prevail only for the subject matter of that annexure, and service-specific terms, SOPs, rate cards, and web-based links may prevail over general terms where expressly stated.</p>


<h2>ANNEXURE D</h2>
<h2>CLAIMS, MANIFEST, POD, AND SUPPORTING DOCUMENTS</h2>

<p>This Annexure D sets out the documentation requirements applicable to claims, manifests, proof of delivery, and supporting evidence, and is to be read with the Merchant Agreement and all other applicable annexures. Unless expressly stated otherwise, the terms of the Merchant Agreement shall apply.</p>

<h2>1. CLAIM INITIATION</h2>

<p><strong>1.1</strong> To initiate a claim, the Merchant must provide the United Worldwide Couriers airway bill (AWB) number and all supporting documents to Csd@unitedcouriers.biz.</p>

<p><strong>1.2</strong> Claims for Shipments routed via branded carrier networks such as DHL, UPS, and FedEx will be processed under the same policy, subject to carrier-specific timelines.</p>

<h2>2. PICKUP MANIFEST</h2>

<p><strong>2.1</strong> For claims alleging non-connectivity, meaning the Shipment was picked up but not scanned or connected at the hub, the Merchant must submit the signed pickup manifest for the disputed Shipment within three working days of pickup.</p>

<p><strong>2.2</strong> Claims lacking a signed manifest will not be maintainable, and normal turnaround times will not apply in such disputes.</p>

<h2>3. PROOF OF DELIVERY</h2>

<p><strong>3.1</strong> For damage, pilferage, tampering, crushing, or leakage, the recipient must record negative remarks on the proof of delivery at the time of delivery to preserve the claim.</p>

<p><strong>3.2</strong> Absent clear negative remarks on the POD, no claim will be maintainable for such issues.</p>

<p><strong>3.3</strong> Claims for damage, pilferage, tampering, crushing, or leakage will be entertained only if made within forty-eight hours of delivery or receipt and only where the outer packaging applied by the Company or the Shipping Vendor is damaged, altered, or tampered with.</p>

<h2>4. SUPPORTING DOCUMENTS</h2>

<p><strong>4.1</strong> For no first scan claims, the Merchant must submit the required AWB and supporting documents, and the claim will be assessed in accordance with the applicable compensation limits.</p>

<p><strong>4.2</strong> For no delivery scan or lost-in-transit claims, the Merchant must submit the buyer-seller chat and proof of refund along with the required AWB and documents.</p>

<p><strong>4.3</strong> For claims involving branded networks, the same evidentiary requirements apply, subject to the applicable carrier timelines and policies.</p>

<h2>5. CLAIM TIMELINES</h2>

<p><strong>5.1</strong> Claims must be made promptly, and in any event the Company will not consider claims submitted more than sixty working days after the date of the inward scan.</p>

<p><strong>5.2</strong> If the Company notifies the Merchant that a Shipment is stuck, undelivered, or under RTO and the Merchant does not respond within seven working days, the related claim will not be considered.</p>

<h2>6. VERIFICATION AND REJECTION</h2>

<p><strong>6.1</strong> All claims are subject to verification and acceptance under the Claim Policy.</p>

<p><strong>6.2</strong> Submission of incomplete, inaccurate, or fraudulent documents may result in denial of the claim.</p>

<p><strong>6.3</strong> Where applicable, recoveries from carriers or third parties will be applied against amounts payable, and any net recovery will be distributed in accordance with the Claim Policy.</p>

<h2>7. GENERAL REFERENCE</h2>

<p>This Annexure D is intended only to govern documentation, evidence, and claim-processing requirements. Compensation limits, exclusions, RTO treatment, and service-specific claim outcomes shall be governed by the Merchant Agreement and the applicable claim policy.</p>


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
                                        <span>Â© UNITED WORLDWIDE COURIERS PVT LTD</span>
                                        <span>New Delhi Â· India</span>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mt-3">
                                    <button class="btn btn-outline-custom"
                                        style="width: auto; padding-left: 40px; padding-right: 40px;"
                                        onclick="nextStep(isBusinessFlow ? (skipCsbV ? 4 : 5) : 3)">Back</button>
                                    <button class="btn btn-primary-custom"
                                        style="width: auto; padding-left: 60px; padding-right: 60px;"
                                        onclick="nextStep(isBusinessFlow ? (skipCsbV ? 6 : 7) : 5)">Submit & Finish</button>
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
                                    We have received your Business KYC{{ $skipCsbV ? ' documents' : ' and CSB-V documents' }}. Our verification team will review your application within 24 hours.
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
                    // The welcome popup is shown until the customer explicitly accepts the
                    // Merchant Agreement. Acceptance is persisted server-side, so it will not
                    // reappear after a refresh (even when a KYC draft row exists).
                    let merchantAgreementAccepted = @json($merchantAgreementAccepted);
                    const kycAgreementAcceptUrl = @json(route('customer.kyc.agreement.accept'));
                    document.addEventListener('DOMContentLoaded', function() {
                        const welcomeModalEl = document.getElementById('kycWelcomeModal');
                        const acceptBtn = document.getElementById('kycWelcomeAcceptBtn');

                        // Persist acceptance server-side so the modal does not
                        // reappear after a refresh.
                        if (acceptBtn) {
                            acceptBtn.addEventListener('click', function() {
                                acceptBtn.disabled = true;
                                fetch(kycAgreementAcceptUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    })
                                    .then(response => response.json().catch(() => ({})))
                                    .then(data => {
                                        if (data && data.success) {
                                            merchantAgreementAccepted = true;
                                        }
                                        // Keep the KYC flow usable even if the
                                        // persistence call fails (modal closes).
                                        kycData.terms_accepted = true;
                                        if (welcomeModalEl) {
                                            const modal = bootstrap.Modal.getInstance(welcomeModalEl);
                                            if (modal) {
                                                modal.hide();
                                            }
                                        }
                                    })
                                    .catch(() => {
                                        kycData.terms_accepted = true;
                                        if (welcomeModalEl) {
                                            const modal = bootstrap.Modal.getInstance(welcomeModalEl);
                                            if (modal) {
                                                modal.hide();
                                            }
                                        }
                                    })
                                    .finally(() => {
                                        acceptBtn.disabled = false;
                                    });
                            });
                        }

                        if (welcomeModalEl && !merchantAgreementAccepted) {
                            const welcomeModal = new bootstrap.Modal(welcomeModalEl, {
                                backdrop: 'static',
                                keyboard: false
                            });
                            welcomeModal.show();
                        }
                    });

                    let kycData = {
                        gst_number: '',
                        gst_business_name: '',
                        gst_verified: false,
                        aadhar_number: '',
                        aadhar_verified: false,
                        organization_name: '',
                        authorized_signatory: '',
                        billing_address: '',
                        billing_gst: '',
                        billing_contact: '',
                        billing_email: '',
                        terms_accepted: true,
                        // Stored document paths (persisted server-side on upload)
                        gst_certificate_document: '',
                        aadhar_front_document: '',
                        aadhar_back_document: '',
                        pan_document: '',
                        signature_document: '',
                        // Business KYC (CSB-V) fields
                        is_csb_v: true,
                        is_gst: false,
                        is_lut: false,
                        gst_certificate_number: '',
                        gst_certificate_verified: false,
                        iec_number: '',
                        ad_code: '',
                        lut_number: '',
                        lut_expiry_date: '',
                        lut_bond_year: '',
                        bank_account_number: '',
                        bank_type: ''
                    };

                    // Detect Business vs Personal flow and merge any server-side draft.
                    const isBusinessFlow = @json(strcasecmp(trim((string) $userType), 'Business') === 0);
                    const isAadhaarOptional = @json($isAadhaarOptional);
                    const skipCsbV = @json($skipCsbV);
                    const csbVOptional = @json($csbVOptional);
                    const totalSteps = isBusinessFlow ? (skipCsbV ? 6 : 7) : 5;
                    const savedKycDraft = @json($kycDraft?->form_data ?? []);
                    const rawSavedKycStep = Number(@json($kycDraft?->current_step ?? 1)) || 1;
                    // The Personal "Complete KYC" intro step has been removed, so any
                    // restored personal draft step is shifted down by one to map onto
                    // the new 5-step flow. Business drafts are never shifted.
                    const normalizedSavedKycStep = isBusinessFlow
                        ? rawSavedKycStep
                        : Math.max(1, rawSavedKycStep - 1);
                    const savedKycStep = Math.min(totalSteps - 1, Math.max(1, normalizedSavedKycStep));
                    const kycDraftSaveUrl = @json(route('customer.kyc.draft.save'));
                    const kycType = isBusinessFlow ? 'business' : 'personal';
                    let kycDraftTimer = null;
                    let kycDraftSaving = false;
                    let kycDraftQueuedStep = null;
                    kycData = Object.assign(kycData, savedKycDraft || {});

                    // Verification state is stored with internal session-key names
                    // in the draft. Normalize it before the form is restored so
                    // all verified identity fields survive a refresh or resume.
                    if (!kycData.gst_number) {
                        kycData.gst_number = kycData.kyc_gst_number || @json(session('kyc_gst_number', ''));
                    }
                    if (!kycData.gst_business_name) {
                        kycData.gst_business_name = kycData.kyc_gst_business_name || @json(session('kyc_gst_business_name', ''));
                    }
                    if (!kycData.gst_verified) {
                        kycData.gst_verified = Boolean(
                            kycData.kyc_gst_cashfree_verified ||
                            kycData.kyc_gst_verified ||
                            @json((bool) session('kyc_gst_cashfree_verified', false))
                        );
                    }
                    if (!kycData.aadhar_number) {
                        kycData.aadhar_number = String(
                            kycData.kyc_aadhar_number || @json(session('kyc_aadhar_number', ''))
                        ).replace(/\s+/g, '');
                    }
                    if (!kycData.aadhar_verified) {
                        kycData.aadhar_verified = Boolean(
                            kycData.kyc_aadhar_cashfree_verified ||
                            kycData.kyc_aadhar_verified ||
                            @json((bool) session('kyc_aadhar_cashfree_verified', false))
                        );
                    }
                    if (!kycData.pan_number) {
                        kycData.pan_number = String(
                            kycData.kyc_pan_number || @json(session('kyc_pan_number', ''))
                        ).replace(/\s+/g, '').toUpperCase();
                    }
                    if (!kycData.pan_holder_name) {
                        kycData.pan_holder_name = kycData.kyc_pan_holder_name || @json(session('kyc_pan_holder_name', ''));
                    }
                    if (!kycData.pan_dob) {
                        kycData.pan_dob = formatRequestDob(
                            kycData.kyc_pan_dob || @json(session('kyc_pan_dob', ''))
                        );
                    } else {
                        kycData.pan_dob = formatRequestDob(kycData.pan_dob);
                    }
                    if (!kycData.pan_dob) {
                        kycData.pan_verified = false;
                    }
                    if (!kycData.pan_verified) {
                        kycData.pan_verified = Boolean(
                            kycData.kyc_pan_cashfree_verified ||
                            kycData.kyc_pan_verified ||
                            @json((bool) session('kyc_pan_cashfree_verified', false))
                        );
                    }

                    function getActiveKycStep() {
                        const active = document.querySelector('.step-content.active');
                        const match = active && active.id.match(/step(\d+)-content/);
                        if (!match) return 1;
                        const domStep = Number(match[1]);
                        return isBusinessFlow
                            ? (skipCsbV ? (domStep >= 5 ? domStep - 1 : domStep) : domStep)
                            : (domStep <= 3 ? domStep - 1 : domStep - 2);
                    }

                    function readKycField(id, key, transform) {
                        const field = document.getElementById(id);
                        if (!field) return;
                        const value = transform ? transform(field.value) : field.value.trim();
                        kycData[key] = value;
                    }

                    function parseValidPanDob(value) {
                        const normalized = String(value || '').trim();
                        let match = normalized.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                        let year;
                        let month;
                        let day;

                        if (match) {
                            year = Number(match[1]);
                            month = Number(match[2]);
                            day = Number(match[3]);
                        } else {
                            match = normalized.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
                            if (!match) return null;
                            day = Number(match[1]);
                            month = Number(match[2]);
                            year = Number(match[3]);
                        }

                        const today = new Date();
                        const date = new Date(year, month - 1, day);
                        if (year < 1900 || year > today.getFullYear() ||
                            month < 1 || month > 12 || day < 1 || day > 31 ||
                            date.getFullYear() !== year || date.getMonth() !== month - 1 ||
                            date.getDate() !== day || date >= new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
                            return null;
                        }

                        const paddedMonth = String(month).padStart(2, '0');
                        const paddedDay = String(day).padStart(2, '0');
                        return {
                            ymd: `${String(year).padStart(4, '0')}-${paddedMonth}-${paddedDay}`,
                            dmy: `${paddedDay}/${paddedMonth}/${String(year).padStart(4, '0')}`
                        };
                    }

                    function formatDisplayDob(value) {
                        const parsed = parseValidPanDob(value);
                        return parsed ? parsed.dmy : '';
                    }

                    function formatRequestDob(value) {
                        const parsed = parseValidPanDob(value);
                        return parsed ? parsed.ymd : '';
                    }

                    function syncKycDatePickerValue(id, value) {
                        const field = document.getElementById(id);
                        if (!field) return;
                        if (!value) {
                            if (field._flatpickr) field._flatpickr.clear(false);
                            else field.value = '';
                            return;
                        }
                        if (field._flatpickr) {
                            field._flatpickr.setDate(value, false, 'Y-m-d');
                        } else {
                            field.value = value;
                        }
                    }

                    function captureKycDraftData() {
                        const businessNameField = document.getElementById('bizGstBusinessName');
                        if (businessNameField && businessNameField.value.trim()) {
                            kycData.gst_business_name = businessNameField.value.trim();
                        }
                        readKycField('aadharInput', 'aadhar_number', value => value.replace(/\s+/g, ''));
                        readKycField('panInput', 'pan_number', value => value.trim().toUpperCase());
                        readKycField('panHolderName', 'pan_holder_name');
                        readKycField('panDob', 'pan_dob', formatRequestDob);
                        readKycField('bizGstCertNumber', 'gst_certificate_number', value => value.trim().toUpperCase());
                        readKycField('bizIecNumber', 'iec_number');
                        readKycField('bizAdCode', 'ad_code', value => value.replace(/\D/g, '').slice(0, 14));
                        const gstType = document.getElementById('bizGstType');
                        const lutType = document.getElementById('bizLutType');
                        if (gstType) kycData.is_gst = gstType.checked;
                        if (lutType) kycData.is_lut = lutType.checked;
                        if (kycData.is_lut) {
                            readKycField('bizLutNumber', 'lut_number');
                            syncBusinessLutBondYear();
                            readKycField('bizLutExpiry', 'lut_expiry_date', value => value);
                            readKycField('bizLutBondYear', 'lut_bond_year');
                        } else {
                            kycData.lut_number = '';
                            kycData.lut_expiry_date = '';
                            kycData.lut_bond_year = '';
                        }
                        readKycField('bizBankType', 'bank_type', value => value);
                        readKycField('bizBankAccount', 'bank_account_number');
                        readKycField('bizBillingGst', 'billing_gst', value => value.trim().toUpperCase());
                        readKycField('bizBillingContact', 'billing_contact');
                        readKycField('bizBillingEmail', 'billing_email');
                        readKycField('bizBillingAddress', 'billing_address');

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
                                showKycAlert('Attention Needed', 'Please upload a PNG or JPG image of your signature.');
                                resetSignatureUpload();
                                return;
                            }
                            if (file.size > 2 * 1024 * 1024) {
                                showKycAlert('Attention Needed', 'Signature image must be smaller than 2MB.');
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
                            uploadKycDraftDocument(file, 'signature_document');
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
                            if (expiryField._flatpickr) expiryField._flatpickr.set('minDate', null);
                            syncBusinessLutBondYear();
                            return;
                        }

                        expiryField.min = `${startYear + 1}-01-01`;
                        if (expiryField._flatpickr) expiryField._flatpickr.set('minDate', expiryField.min);
                        if (expiryField.value && expiryField.value < expiryField.min) {
                            if (expiryField._flatpickr) {
                                expiryField._flatpickr.clear();
                            } else {
                                expiryField.value = '';
                            }
                        }
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

                    function invalidateGstVerification() {
                        if (!(kycData.gst_certificate_verified || kycData.gst_verified)) return;

                        kycData.gst_verified = false;
                        kycData.gst_certificate_verified = false;

                        const button = document.getElementById('bizVerifyGstCertBtn');
                        const status = document.getElementById('bizGstStatus');
                        if (button) {
                            button.innerHTML = 'Verify GST';
                            button.disabled = false;
                        }
                        if (status) {
                            status.innerHTML = '';
                            status.style.display = 'none';
                        }

                        queueKycDraftSave(getActiveKycStep());
                    }

                    function invalidateAadharVerification() {
                        if (!kycData.aadhar_verified) return;

                        kycData.aadhar_verified = false;

                        const field = document.getElementById('aadharInput');
                        const button = document.getElementById('verifyAadharBtn');
                        const status = document.getElementById('aadharStatus');
                        if (field) field.readOnly = false;
                        if (button) {
                            button.innerHTML = 'Verify Aadhar';
                            button.disabled = false;
                        }
                        if (status) {
                            status.innerHTML = '';
                            status.style.display = 'none';
                        }

                        queueKycDraftSave(getActiveKycStep());
                    }

                    function invalidatePanVerification() {
                        if (!kycData.pan_verified) return;

                        kycData.pan_verified = false;

                        const panField = document.getElementById('panInput');
                        const holderField = document.getElementById('panHolderName');
                        const dobField = document.getElementById('panDob');
                        const button = document.getElementById('verifyPanBtn');
                        const status = document.getElementById('panStatus');
                        if (panField) panField.readOnly = false;
                        if (holderField) holderField.readOnly = false;
                        if (dobField) dobField.readOnly = false;
                        if (button) {
                            button.innerHTML = 'Verify PAN';
                            button.disabled = false;
                        }
                        if (status) {
                            status.innerHTML = '';
                            status.style.display = 'none';
                        }

                        queueKycDraftSave(getActiveKycStep());
                    }

                    // GST identity inputs invalidate any verification tied to their old values.
                    const bizGstInput = document.getElementById('bizGstCertNumber');
                    const bizGstBusinessName = document.getElementById('bizGstBusinessName');
                    if (bizGstInput) {
                        bizGstInput.addEventListener('input', function(e) {
                            e.target.value = e.target.value.toUpperCase().replace(/[^0-9A-Z]/g, '').slice(0, 15);
                            invalidateGstVerification();
                        });
                    }
                    if (bizGstBusinessName) {
                        bizGstBusinessName.addEventListener('input', function() {
                            invalidateGstVerification();
                        });
                    }

                    // Identity changes invalidate verification tied to the old number or image.
                    const aadharInput = document.getElementById('aadharInput');
                    if (aadharInput) {
                        aadharInput.addEventListener('input', function(e) {
                            invalidateAadharVerification();
                            const value = e.target.value.replace(/\D/g, '').slice(0, 12);
                            const formatted = value.match(/.{1,4}/g);
                            e.target.value = formatted ? formatted.join(' ') : '';
                        });
                    }

                    const panInput = document.getElementById('panInput');
                    const panHolderName = document.getElementById('panHolderName');
                    const panDob = document.getElementById('panDob');
                    if (panInput) {
                        panInput.addEventListener('input', function(e) {
                            invalidatePanVerification();
                            e.target.value = e.target.value
                                .toUpperCase()
                                .replace(/[^A-Z0-9]/g, '')
                                .slice(0, 10);
                        });
                    }
                    if (panHolderName) {
                        panHolderName.addEventListener('input', invalidatePanVerification);
                    }
                    if (panDob) {
                        panDob.addEventListener('input', function() {
                            invalidatePanVerification();
                            this.value = this.value.replace(/[^0-9]/g, '').replace(/^(\d{2})(\d)/, '$1/$2').replace(/^(\d{2}\/\d{2})(\d)/, '$1/$2').slice(0, 10);
                        });
                    }

                    // ===== KYC Alert Modal helper (replaces browser alerts) =====
                    function showKycAlert(title, messageHtml) {
                        const modalEl = document.getElementById('kycAlertModal');
                        if (!modalEl) {
                            showAlert(messageHtml.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim(), 'warning');
                            return;
                        }
                        document.getElementById('kycAlertTitle').textContent = title;
                        document.getElementById('kycAlertBody').innerHTML = messageHtml;
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    }

                    function markFieldInvalid(field) {
                        if (!field) return;
                        field.classList.add('input-invalid');
                        field.addEventListener('input', function () {
                            this.classList.remove('input-invalid');
                        }, { once: true });
                    }

                    function markFieldsInvalid(fields) {
                        fields.forEach(markFieldInvalid);
                        const firstInvalid = fields.find(f => f && f.classList.contains('input-invalid'));
                        if (firstInvalid) {
                            firstInvalid.focus();
                            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }

                    function buildKycAlertList(title, items) {
                        const list = items.map(item =>
                            '<li><i class="fas fa-times-circle"></i><span>' + item + '</span></li>'
                        ).join('');
                        return '<div>' + title + '<ul class="kyc-alert-list">' + list + '</ul></div>';
                    }

                    function normalizeName(value) {
                        return String(value || '').trim().toLowerCase();
                    }

                    function verifyAadhar() {
                        const aadharField = document.getElementById('aadharInput');
                        const frontFileInput = document.getElementById('aadharFrontFileInput');
                        const verifyBtn = document.getElementById('verifyAadharBtn');
                        const aadharStatus = document.getElementById('aadharStatus');
                        const continueBtn = document.getElementById('aadharContinueBtn');

                        if (!aadharField || !frontFileInput || !verifyBtn || !aadharStatus) return;

                        const aadhar = aadharField.value.replace(/\s+/g, '');

                        const missingItems = [];
                        const invalidFields = [];

                        if (!aadhar) {
                            missingItems.push('12-digit Aadhaar Number');
                            invalidFields.push(aadharField);
                        } else if (!/^[2-9][0-9]{11}$/.test(aadhar)) {
                            missingItems.push('A valid 12-digit Aadhaar Number (starting with 2-9)');
                            invalidFields.push(aadharField);
                        }
                        const hasFrontFile = Boolean(frontFileInput.files && frontFileInput.files[0]);
                        const hasStoredFrontPath = Boolean(kycData.aadhar_front_document);
                        if (!hasFrontFile && !hasStoredFrontPath) {
                            missingItems.push('Aadhaar front image');
                            invalidFields.push(frontFileInput.closest('#aadharFrontUploadArea'));
                        }

                        if (missingItems.length > 0) {
                            markFieldsInvalid(invalidFields);
                            showKycAlert(
                                'Complete your Aadhaar details',
                                buildKycAlertList(
                                    'Please provide the following before verifying your Aadhaar:',
                                    missingItems
                                )
                            );
                            return;
                        }
                        if (hasFrontFile && !validateImageOnlyKycFile(frontFileInput.files[0], frontFileInput)) {
                            return;
                        }

                        const verifyData = new FormData();
                        verifyData.append('aadhar_number', aadhar);
                        if (hasFrontFile) {
                            verifyData.append('aadhar_front_document', frontFileInput.files[0]);
                        } else {
                            verifyData.append('aadhar_front_document_path', kycData.aadhar_front_document);
                        }

                        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                        verifyBtn.disabled = true;

                        fetch('{{ route("customer.verify.aadhar") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: verifyData
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

                                    showKycAlert('Aadhaar verification failed', data.message ||
                                        'We could not verify your Aadhaar. Please check the number and upload a clear, sharp image of the Aadhaar card, then try again.');
                                }
                            })
                            .catch(error => {
                                console.error('Aadhar verify error:', error);
                                aadharStatus.innerHTML = '<i class="fas fa-times-circle"></i> Connection error';
                                aadharStatus.style.display = 'block';
                                aadharStatus.style.color = '#dc3545';

                                verifyBtn.innerHTML = 'Verify Aadhar';
                                verifyBtn.disabled = false;

                                showKycAlert('Connection error',
                                    'A network error occurred while verifying your Aadhaar. Please try again.');
                            });
                    }

                    function verifyPan() {
                        const panField = document.getElementById('panInput');
                        const holderField = document.getElementById('panHolderName');
                        const dobField = document.getElementById('panDob');
                        const panFileInput = document.getElementById('panFileInput');
                        const verifyBtn = document.getElementById('verifyPanBtn');
                        const panStatus = document.getElementById('panStatus');

                        if (!panField || !holderField || !dobField || !panFileInput || !verifyBtn || !panStatus) return;

                        // Normalize PAN to uppercase, no spaces. Personal KYC requires
                        // an individual PAN ('P'); Business KYC requires an entity PAN.
                        const pan = panField.value.replace(/\s+/g, '').toUpperCase();

                        if (!pan) {
                            showKycAlert('PAN number required', 'Please enter your PAN number.');
                            markFieldInvalid(panField);
                            return;
                        }
                        if (pan.length !== 10 || !/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan)) {
                            showKycAlert('Invalid PAN number', 'It must be 10 characters: 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).');
                            markFieldInvalid(panField);
                            return;
                        }
                        if (isBusinessFlow && pan.charAt(3) === 'P') {
                            showKycAlert('Business PAN required', 'Please upload a business PAN card, not a personal PAN card.');
                            markFieldInvalid(panField);
                            return;
                        }
                        if (!isBusinessFlow && pan.charAt(3) !== 'P') {
                            showKycAlert('Personal PAN required', 'Please upload a personal PAN card. Business PAN cards are not accepted for Personal KYC.');
                            markFieldInvalid(panField);
                            return;
                        }
                        if (!holderField.value.trim()) {
                            showKycAlert('PAN holder name required', 'Please enter the PAN holder name.');
                            markFieldInvalid(holderField);
                            return;
                        }
                        if (!dobField.value.trim()) {
                            showKycAlert('Date of birth required', 'Please enter your date of birth in DD/MM/YYYY format.');
                            markFieldInvalid(dobField);
                            return;
                        }
                        const parsedPanDob = parseValidPanDob(dobField.value);
                        if (!parsedPanDob) {
                            showKycAlert('Invalid date of birth', 'Please select a valid past date in DD/MM/YYYY format.');
                            if (dobField._flatpickr) dobField._flatpickr.clear();
                            else dobField.value = '';
                            kycData.pan_dob = '';
                            kycData.pan_verified = false;
                            markFieldInvalid(dobField);
                            return;
                        }
                        const panDob = parsedPanDob.ymd;
                        const hasPanFile = Boolean(panFileInput.files && panFileInput.files[0]);
                        const hasStoredPanPath = Boolean(kycData.pan_document);
                        if (!hasPanFile && !hasStoredPanPath) {
                            showKycAlert('PAN image required', 'Please upload the PAN image before verification.');
                            return;
                        }
                        if (hasPanFile && !validateImageOnlyKycFile(panFileInput.files[0], panFileInput)) {
                            return;
                        }

                        const verifyData = new FormData();
                        verifyData.append('pan_number', pan);
                        verifyData.append('pan_holder_name', holderField.value.trim());
                        // Send a strictly validated DD/MM/YYYY value. The controller
                        // normalizes it to Y-m-d before validation/storage.
                        verifyData.append('pan_dob', parsedPanDob.dmy);
                        if (hasPanFile) {
                            verifyData.append('pan_document', panFileInput.files[0]);
                        } else {
                            verifyData.append('pan_document_path', kycData.pan_document);
                        }

                        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                        verifyBtn.disabled = true;

                        fetch('{{ route("customer.verify.pan") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: verifyData
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
                                const verificationStatus = String(data.verification_status || '').trim().toUpperCase();
                                if (data.success && verificationStatus === 'VALID') {
                                    kycData.pan_number = pan;
                                    kycData.pan_holder_name = holderField.value.trim();
                                    kycData.pan_dob = panDob;
                                    kycData.pan_verified = true;
                                    saveKycDraft(getActiveKycStep());

                                    panStatus.innerHTML = '<i class="fas fa-check-circle"></i> ' + (data.message || 'PAN verified successfully!') + ' (Status: ' + verificationStatus + ')';
                                    panStatus.style.display = 'block';
                                    panStatus.style.color = '#10b981';

                                    verifyBtn.innerHTML = '<i class="fas fa-check"></i> Verified';
                                    verifyBtn.disabled = true;
                                    panField.readOnly = true;
                                    if (holderField) holderField.readOnly = true;
                                    if (dobField) dobField.readOnly = true;
                                } else {
                                    const statusMessage = verificationStatus
                                        ? 'PAN verification status: ' + verificationStatus + '.'
                                        : 'PAN verification status was not received.';
                                    panStatus.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || statusMessage);
                                    panStatus.style.display = 'block';
                                    panStatus.style.color = '#dc3545';

                                    verifyBtn.innerHTML = 'Verify PAN';
                                    verifyBtn.disabled = false;

                                    showKycAlert('PAN verification failed', data.message || 'PAN verification failed. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('PAN verify error:', error);
                                panStatus.innerHTML = '<i class="fas fa-times-circle"></i> Connection error';
                                panStatus.style.display = 'block';
                                panStatus.style.color = '#dc3545';

                                verifyBtn.innerHTML = 'Verify PAN';
                                verifyBtn.disabled = false;

                                showKycAlert('Connection error', 'A network error occurred while verifying your PAN. Please try again.');
                            });
                    }

                    const imageOnlyKycInputIds = new Set([
                        'aadharFrontFileInput',
                        'aadharBackFileInput',
                        'panFileInput'
                    ]);
                    const gstCertificateInputIds = new Set([
                        'bizGstCertFileInput'
                    ]);

                    function validateImageOnlyKycFile(file, input) {
                        const extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
                        const allowedExtensions = ['jpg', 'jpeg', 'png'];
                        const allowedMimeTypes = ['image/jpeg', 'image/png'];
                        if (!allowedExtensions.includes(extension) || !allowedMimeTypes.includes(file.type)) {
                            showKycAlert('Attention Needed', 'Only JPG, JPEG, or PNG images are allowed for GST, Aadhaar, and PAN documents.');
                            input.value = '';
                            return false;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            showKycAlert('Attention Needed', 'The selected image must not exceed 5 MB.');
                            input.value = '';
                            return false;
                        }
                        return true;
                    }

                    function validatePdfOnlyKycFile(file, input) {
                        const extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
                        if (extension !== 'pdf' || file.type !== 'application/pdf') {
                            showKycAlert('Attention Needed', 'Only a PDF file is allowed for the GST Certificate.');
                            input.value = '';
                            return false;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            showKycAlert('Attention Needed', 'The GST Certificate PDF must not exceed 5 MB.');
                            input.value = '';
                            return false;
                        }
                        return true;
                    }

                    function validateBusinessSignatureFile(file, input) {
                        const extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
                        const allowedExtensions = ['jpg', 'jpeg', 'png'];
                        if (!allowedExtensions.includes(extension)) {
                            showKycAlert('Attention Needed', 'Authorized Signature must be a JPG, JPEG, or PNG image.');
                            input.value = '';
                            return false;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            showKycAlert('Attention Needed', 'Authorized Signature must not exceed 5 MB.');
                            input.value = '';
                            return false;
                        }
                        return true;
                    }

                    // File upload preview handlers for KYC documents.
                    // Maps a local file data key to its server-side document field
                    // so selected files are persisted immediately via /kyc-draft-file.
                    const kycDocFieldMap = {
                        gst_certificate_file: 'gst_certificate_document',
                        aadhar_front_file: 'aadhar_front_document',
                        aadhar_back_file: 'aadhar_back_document',
                        pan_file: 'pan_document',
                        signature_file: 'signature_document',
                        iec_file: 'iec_document',
                        ad_code_file: 'ad_code_document',
                        lut_file: 'lut_document'
                    };

                    function uploadKycDraftDocument(file, docField, done) {
                        const uploadData = new FormData();
                        uploadData.append('field', docField);
                        uploadData.append('document', file);
                        fetch('{{ route("customer.kyc.draft-file") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: uploadData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    kycData[docField] = data.path;
                                    done && done(true);
                                } else {
                                    showKycAlert('Upload failed',
                                        (data.errors && Object.values(data.errors).flat().join('\n').replace(/\n/g, '<br>')) ||
                                        (data.message || 'The document could not be saved. Please try again.'));
                                    done && done(false);
                                }
                            })
                            .catch(() => {
                                showKycAlert('Connection error',
                                    'The document could not be saved. Please check your internet connection and try again.');
                                done && done(false);
                            });
                    }

                    function handleFilePreview(fileInputId, placeholderId, previewId, fileNameId, dataKey) {
                        const input = document.getElementById(fileInputId);
                        if (!input) return;
                        input.addEventListener('change', function () {
                            if (gstCertificateInputIds.has(fileInputId)) {
                                invalidateGstVerification();
                            }
                            if (fileInputId === 'aadharFrontFileInput') {
                                invalidateAadharVerification();
                            }
                            if (fileInputId === 'panFileInput') {
                                invalidatePanVerification();
                            }
                            if (this.files && this.files[0]) {
                                const file = this.files[0];
                                if (gstCertificateInputIds.has(fileInputId) && !validatePdfOnlyKycFile(file, this)) {
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
                                const docField = kycDocFieldMap[dataKey];
                                if (docField) {
                                    uploadKycDraftDocument(file, docField);
                                }
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

                    // Optional Courier/Aggregator users can deliberately discard any
                    // restored Aadhaar draft data and continue without Aadhaar.
                    function skipAadhaarStep() {
                        if (!isAadhaarOptional) {
                            nextStep(isBusinessFlow ? 3 : 2);
                            return;
                        }

                        const aadharInput = document.getElementById('aadharInput');
                        const frontInput = document.getElementById('aadharFrontFileInput');
                        const backInput = document.getElementById('aadharBackFileInput');
                        const verifyButton = document.getElementById('verifyAadharBtn');
                        const status = document.getElementById('aadharStatus');
                        const frontPlaceholder = document.getElementById('aadharFrontUploadPlaceholder');
                        const frontPreview = document.getElementById('aadharFrontPreview');
                        const backPlaceholder = document.getElementById('aadharBackUploadPlaceholder');
                        const backPreview = document.getElementById('aadharBackPreview');

                        if (aadharInput) aadharInput.value = '';
                        if (frontInput) frontInput.value = '';
                        if (backInput) backInput.value = '';
                        if (frontPlaceholder) frontPlaceholder.style.display = 'block';
                        if (frontPreview) frontPreview.style.display = 'none';
                        if (backPlaceholder) backPlaceholder.style.display = 'block';
                        if (backPreview) backPreview.style.display = 'none';
                        if (status) status.style.display = 'none';
                        if (verifyButton) verifyButton.disabled = false;

                        delete kycData.aadhar_number;
                        delete kycData.aadhar_verified;
                        delete kycData.aadhar_front_document;
                        delete kycData.aadhar_back_document;
                        delete kycData.aadhar_front_file;
                        delete kycData.aadhar_back_file;
                        delete kycData.aadhar_address;
                        saveKycDraft(isBusinessFlow ? 3 : 2);
                        renderKycStep(isBusinessFlow ? 3 : 2, true);
                    }

                    // Validate that the current step is complete before allowing forward navigation
                    function validateStep(step) {
                        if (isBusinessFlow) {
                            // ===== BUSINESS FLOW (7 steps) =====
                            // When the CSB-V step is skipped (Courier / Aggregator),
                            // the Signature step becomes logical step 4, so later
                            // steps shift up by one before validation.
                            if (skipCsbV && step >= 4) step = step + 1;
                            if (step === 1) {
                                // Step 1: Verify GST Certificate and its registered business name.
                                const businessName = document.getElementById('bizGstBusinessName');
                                if (!businessName || !businessName.value.trim()) {
                                    showKycAlert('Attention Needed', 'Please enter the Business Name registered under this GSTIN.');
                                    if (businessName) businessName.focus();
                                    return false;
                                }
                                if ((!kycData.gst_certificate_verified && !kycData.gst_verified)
                                    || businessName.value.trim().toLowerCase() !== String(kycData.gst_business_name || '').trim().toLowerCase()) {
                                    showKycAlert('Attention Needed', 'Please verify your GST Certificate number before continuing.');
                                    return false;
                                }
                                const gstFile = document.getElementById('bizGstCertFileInput');
                                if (!gstFile || !gstFile.files || !gstFile.files[0]) {
                                    if (!kycData.gst_certificate_document) {
                                        showKycAlert('Attention Needed', 'Please upload your GST Certificate document before continuing.');
                                        return false;
                                    }
                                }
                            } else if (step === 2) {
                                // Step 2: Aadhaar is optional only for Courier / Aggregator.
                                const frontFile = document.getElementById('aadharFrontFileInput');
                                const backFile = document.getElementById('aadharBackFileInput');
                                const hasFrontFile = Boolean(frontFile && frontFile.files && frontFile.files[0])
                                    || Boolean(kycData.aadhar_front_document);
                                const hasBackFile = Boolean(backFile && backFile.files && backFile.files[0])
                                    || Boolean(kycData.aadhar_back_document);
                                const aadharInput = document.getElementById('aadharInput');
                                const hasAadhaarNumber = Boolean(aadharInput && aadharInput.value.replace(/\s+/g, ''));
                                const hasAnyAadhaarData = hasAadhaarNumber || kycData.aadhar_verified || hasFrontFile || hasBackFile;

                                if (!isAadhaarOptional || hasAnyAadhaarData) {
                                    if (!kycData.aadhar_verified) {
                                        showKycAlert('Attention Needed', isAadhaarOptional
                                            ? 'Complete Aadhaar verification, or clear Aadhaar details to skip this optional step.'
                                            : 'Please verify your Aadhaar number before continuing.');
                                        return false;
                                    }
                                    if (!hasFrontFile) {
                                        showKycAlert('Attention Needed', 'Please upload the front side of your Aadhaar before continuing.');
                                        return false;
                                    }
                                    if (!hasBackFile) {
                                        showKycAlert('Attention Needed', 'Please upload the back side of your Aadhaar before continuing.');
                                        return false;
                                    }
                                }
                            } else if (step === 3) {
                                // Step 3: Verify PAN
                                if (!kycData.pan_verified) {
                                    showKycAlert('Attention Needed', 'Please verify your PAN before continuing.');
                                    return false;
                                }
                                const panFile = document.getElementById('panFileInput');
                                if (!panFile || !panFile.files || !panFile.files[0]) {
                                    if (!kycData.pan_document) {
                                        showKycAlert('Attention Needed', 'Please upload your PAN card before continuing.');
                                        return false;
                                    }
                                }
                            } else if (step === 4) {
                                // Step 4: validate every CSB-V field and upload before Continue.
                                // CSB-V is optional for eCommerce accounts: an entirely empty step
                                // may be skipped (Continue passes when all CSB-V details are cleared),
                                // but once any CSB-V detail is entered the full validation applies.
                                if (csbVOptional) {
                                    const optIec = document.getElementById('bizIecNumber');
                                    const optAdCode = document.getElementById('bizAdCode');
                                    const optGst = document.getElementById('bizGstType');
                                    const optLut = document.getElementById('bizLutType');
                                    const optLutNumber = document.getElementById('bizLutNumber');
                                    const optLutExpiry = document.getElementById('bizLutExpiry');
                                    const optBankType = document.getElementById('bizBankType');
                                    const optBankAccount = document.getElementById('bizBankAccount');
                                    const optBillingAddress = document.getElementById('bizBillingAddress');
                                    const optBillingContact = document.getElementById('bizBillingContact');
                                    const optBillingEmail = document.getElementById('bizBillingEmail');
                                    const optIecFile = document.getElementById('bizIecFileInput');
                                    const optAdCodeFile = document.getElementById('bizAdCodeFileInput');
                                    const optLutFile = document.getElementById('bizLutFileInput');
                                    const hasAnyCsbVData = Boolean(
                                        (optIec && optIec.value.trim()) ||
                                        (optAdCode && optAdCode.value.trim()) ||
                                        (optGst && optGst.checked) ||
                                        (optLut && optLut.checked) ||
                                        (optLutNumber && optLutNumber.value.trim()) ||
                                        (optLutExpiry && optLutExpiry.value) ||
                                        (optBankType && optBankType.value) ||
                                        (optBankAccount && optBankAccount.value.trim()) ||
                                        (optBillingAddress && optBillingAddress.value.trim()) ||
                                        (optBillingContact && optBillingContact.value.trim()) ||
                                        (optBillingEmail && optBillingEmail.value.trim()) ||
                                        (optIecFile && optIecFile.files && optIecFile.files[0]) ||
                                        (optAdCodeFile && optAdCodeFile.files && optAdCodeFile.files[0]) ||
                                        (optLutFile && optLutFile.files && optLutFile.files[0])
                                    );
                                    if (!hasAnyCsbVData) {
                                        // Intentionally empty: submit as CSB-V skipped (CSB-IV/csb_status=1).
                                        kycData.is_csb_v = false;
                                        return true;
                                    }
                                }
                                const iecInput = document.getElementById('bizIecNumber');
                                const adCodeInput = document.getElementById('bizAdCode');
                                const gstType = document.getElementById('bizGstType');
                                const lutType = document.getElementById('bizLutType');
                                const lutNumber = document.getElementById('bizLutNumber');
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
                                    showKycAlert('Attention Needed', message);
                                    if (field) {
                                        field.focus();
                                        if (field.type === 'file') {
                                            const area = field.closest('[id$="UploadArea"]');
                                            if (area) area.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        }
                                    }
                                    return false;
                                };
                                const validateFile = (input, label, allowedTypes, maxBytes, storedPath) => {
                                    if (!input || !input.files || !input.files[0]) {
                                        if (storedPath) return true;
                                        return fail(`Please upload your ${label}.`, input);
                                    }
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

                                if (!gstType.checked && !lutType.checked) return fail('Please select GST, LUT, or both before continuing.', gstType);
                                if (!iecInput || !/^[A-Z0-9]{10}$/.test(iecInput.value.trim().toUpperCase())) return fail('IEC Number must be exactly 10 letters or digits.', iecInput);
                                if (!validateFile(iecFile, 'IEC Certificate', allowedDocumentTypes, fiveMb, kycData.iec_document)) return false;
                                if (!adCodeInput || !/^(\d{7}|\d{14})$/.test(adCodeInput.value.trim())) return fail('AD Code must be exactly 7 or 14 numeric digits.', adCodeInput);
                                if (!validateFile(adCodeFile, 'AD Code Document', allowedDocumentTypes, fiveMb, kycData.ad_code_document)) return false;
                                syncBusinessLutBondYear();
                                if (lutType.checked) {
                                    if (!lutNumber || !lutNumber.value.trim()) return fail('Please enter the LUT Number.', lutNumber);
                                    const startYear = Number(lutStartYear && lutStartYear.value);
                                    const endYear = Number(lutEndYear && lutEndYear.value);
                                    if (!startYear) return fail('Please select the LUT Bond Start Year.', lutStartYear);
                                    if (!endYear) return fail('Please select the LUT Bond End Year.', lutEndYear);
                                    if (endYear < startYear + 1 || endYear > startYear + 5) return fail('LUT Bond End Year must be within five years after the Start Year.', lutEndYear);
                                    if (!lutExpiry || !lutExpiry.value) return fail('Please select the LUT Expiry Date.', lutExpiry);
                                    const minimumExpiryDate = `${startYear + 1}-01-01`;
                                    if (lutExpiry.value < minimumExpiryDate) return fail(`LUT Expiry Date must be on or after ${minimumExpiryDate}.`, lutExpiry);
                                    if (!lutBondYear || !/^\d{4}-\d{2}$/.test(lutBondYear.value)) return fail('Please select valid LUT Bond Start and End Years.', lutStartYear);
                                    if (!validateFile(lutFile, 'LUT Document', ['application/pdf'], fiveMb, kycData.lut_document)) return false;
                                }
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
                                if (!selectedSignature && !kycData.signature_document) {
                                    showKycAlert('Attention Needed', 'Please upload your Authorized Signature.');
                                    return false;
                                }
                                if (selectedSignature && !validateBusinessSignatureFile(selectedSignature, sigFile)) return false;
                            }
                        } else {
                            // ===== PERSONAL FLOW (5 steps) =====
                            if (step === 1) {
                                // Step 1: Verify Aadhar
                                if (!kycData.aadhar_verified) { showKycAlert('Attention Needed', 'Please verify your Aadhar number before continuing.'); return false; }
                                const frontFile = document.getElementById('aadharFrontFileInput');
                                const backFile = document.getElementById('aadharBackFileInput');
                                const hasFrontStored = Boolean(kycData.aadhar_front_document);
                                const hasBackStored = Boolean(kycData.aadhar_back_document);
                                if (!frontFile || (!frontFile.files || !frontFile.files[0]) && !hasFrontStored) { showKycAlert('Attention Needed', 'Please upload the front side of your Aadhaar.'); return false; }
                                if (!backFile || (!backFile.files || !backFile.files[0]) && !hasBackStored) { showKycAlert('Attention Needed', 'Please upload the back side of your Aadhaar.'); return false; }
                            } else if (step === 2) {
                                // Step 2: Verify PAN
                                if (!kycData.pan_verified) { showKycAlert('Attention Needed', 'Please verify your PAN before continuing.'); return false; }
                                const panFile = document.getElementById('panFileInput');
                                if (!panFile || (!panFile.files || !panFile.files[0]) && !kycData.pan_document) { showKycAlert('Attention Needed', 'Please upload your PAN card.'); return false; }
                            } else if (step === 3) {
                                // Step 3: Upload Signature
                                const selectedSignature = signatureFileInput
                                    && signatureFileInput.files
                                    && signatureFileInput.files[0];
                                if (!selectedSignature && !kycData.signature_document) { showKycAlert('Attention Needed', 'Please upload your signature before continuing.'); return false; }
                            }
                        }
                        return true;
                    }

                    function renderKycStep(stepNumber, shouldScroll) {
                        document.querySelectorAll('.step-content').forEach(content => content.classList.remove('active'));
                        const domStep = isBusinessFlow
                            ? ((!skipCsbV || stepNumber <= 3) ? stepNumber : stepNumber + 1)
                            : (stepNumber <= 2 ? stepNumber + 1 : stepNumber + 2);
                        const target = document.getElementById('step' + domStep + '-content');
                        if (target) target.classList.add('active');

                        document.querySelectorAll('.step-item').forEach((item, index) => {
                            const currentIdx = index + 1;
                            item.classList.remove('active', 'completed');
                            if (currentIdx < stepNumber) item.classList.add('completed');
                            else if (currentIdx === stepNumber) item.classList.add('active');
                        });

                        if (stepNumber === (isBusinessFlow ? (skipCsbV ? 5 : 6) : 4)) {
                            const billSignatureImg = document.getElementById('billSignatureImg');
                            const billSignaturePlaceholder = document.getElementById('billSignaturePlaceholder');
                            // Object URLs only exist until the current page is closed.
                            // Fall back to the persisted signature path so the
                            // merchant agreement is populated after logout/login
                            // and when a rejected KYC is resumed.
                            const storedSignaturePreviewUrl = kycData.signature_document
                                && typeof kycData.signature_document === 'string'
                                ? '{{ asset('') }}' + kycData.signature_document
                                : '';
                            const termsSignaturePreviewUrl = (isBusinessFlow
                                ? businessSignaturePreviewUrl
                                : signaturePreviewUrl) || storedSignaturePreviewUrl;
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
                            } else if (!skipCsbV && stepNumber === 5) {
                                // Leaving step 4 (CSB-V merged) -> save IEC + AD Code + LUT + Bank + Billing
                                const iecInput = document.getElementById('bizIecNumber');
                                const adCodeInput = document.getElementById('bizAdCode');
                                const gstType = document.getElementById('bizGstType');
                                const lutType = document.getElementById('bizLutType');
                                const lutNumber = document.getElementById('bizLutNumber');
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
                                if (gstType) kycData.is_gst = gstType.checked;
                                if (lutType) kycData.is_lut = lutType.checked;
                                if (kycData.is_lut) {
                                    if (lutNumber) kycData.lut_number = lutNumber.value.trim();
                                    syncBusinessLutBondYear();
                                    if (lutExpiry) kycData.lut_expiry_date = lutExpiry.value;
                                    if (lutBondYear) kycData.lut_bond_year = lutBondYear.value.trim();
                                } else {
                                    kycData.lut_number = '';
                                    kycData.lut_expiry_date = '';
                                    kycData.lut_bond_year = '';
                                }
                                if (bankType) kycData.bank_type = bankType.value;
                                if (bankAccount) kycData.bank_account_number = bankAccount.value.trim();
                                if (billingGst) kycData.billing_gst = billingGst.value.trim().toUpperCase();
                                if (billingContact) kycData.billing_contact = billingContact.value.trim();
                                if (billingEmail) kycData.billing_email = billingEmail.value.trim();
                                if (billingAddress) kycData.billing_address = billingAddress.value.trim();
                                // CSB-V is optional for eCommerce accounts: it is submitted only when
                                // at least one CSB-V detail was actually provided. A skipped step
                                // (cleared by Skip) therefore stays skipped even after revisiting it.
                                if (csbVOptional) {
                                    kycData.is_csb_v = Boolean(
                                        (iecInput && iecInput.value.trim()) ||
                                        (adCodeInput && adCodeInput.value.trim()) ||
                                        kycData.is_gst ||
                                        kycData.is_lut ||
                                        (bankType && bankType.value) ||
                                        (bankAccount && bankAccount.value.trim()) ||
                                        (billingAddress && billingAddress.value.trim())
                                    );
                                }
                            } else if (stepNumber === (skipCsbV ? 6 : 7)) {
                                // Leaving step 6 (Terms & Conditions) -> submit Business KYC
                                submitBusinessKYC();
                            }
                        } else {
                            // ===== PERSONAL FLOW (5 steps) =====
                            if (stepNumber === 5) {
                                // Leaving step 4 (Bill) -> submit KYC data
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
                        const businessNameField = document.getElementById('bizGstBusinessName');
                        const gstFileInput = document.getElementById('bizGstCertFileInput');
                        const verifyBtn = document.getElementById('bizVerifyGstCertBtn');
                        const statusDiv = document.getElementById('bizGstStatus');
                        const continueBtn = document.getElementById('bizGstContinueBtn');

                        if (!gstField || !businessNameField || !verifyBtn) return;

                        const gstValue = gstField.value.trim().toUpperCase();
                        const businessName = businessNameField.value.trim();

                        const missingItems = [];
                        const invalidFields = [];

                        if (!gstValue) {
                            missingItems.push('GST Certificate Number');
                            invalidFields.push(gstField);
                        } else if (gstValue.length !== 15) {
                            missingItems.push('A valid 15-character GST Certificate Number');
                            invalidFields.push(gstField);
                        }
                        if (!businessName) {
                            missingItems.push('Business Name (as registered under this GSTIN)');
                            invalidFields.push(businessNameField);
                        }
                        // A previously uploaded GST certificate may already be stored as a
                        // path on the server (from the draft/upload endpoint or a restored
                        // CSB form). In that case verification can reuse the stored path
                        // instead of forcing the customer to upload the PDF again.
                        const hasStoredGstDoc = kycData.gst_certificate_document &&
                            typeof kycData.gst_certificate_document === 'string' &&
                            kycData.gst_certificate_document.trim() !== '';
                        const hasFreshGstFile = gstFileInput && gstFileInput.files && gstFileInput.files[0];

                        if (!hasFreshGstFile && !hasStoredGstDoc) {
                            missingItems.push('GST Certificate PDF');
                            invalidFields.push(gstFileInput ? gstFileInput.closest('#bizGstCertUploadArea') : null);
                        }

                        if (missingItems.length > 0) {
                            markFieldsInvalid(invalidFields);
                            showKycAlert(
                                'Complete your GST details',
                                buildKycAlertList(
                                    'Please fill in the following fields before verifying your GST:',
                                    missingItems
                                )
                            );
                            return;
                        }
                        if (hasFreshGstFile && !validatePdfOnlyKycFile(gstFileInput.files[0], gstFileInput)) {
                            return;
                        }

                        const verifyData = new FormData();
                        verifyData.append('gst_number', gstValue);
                        verifyData.append('business_name', businessName);
                        if (hasFreshGstFile) {
                            verifyData.append('gst_certificate_document', gstFileInput.files[0]);
                        } else if (hasStoredGstDoc) {
                            // The server accepts the already-uploaded document path.
                            verifyData.append('gst_certificate_document_path', kycData.gst_certificate_document);
                        }

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
                                    // Use the exact business name returned in the verification
                                    // response so every later check compares the same value.
                                    const verifiedBusinessName = (data.business_name || businessName).trim();
                                    kycData.gst_certificate_number = gstValue;
                                    kycData.gst_business_name = verifiedBusinessName;
                                    businessNameField.value = verifiedBusinessName;
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

                                    // Prefill Billing Address from the GST verification response
                                    if (data.address) {
                                        kycData.billing_address = data.address;
                                        const billingAddress = document.getElementById('bizBillingAddress');
                                        if (billingAddress) billingAddress.value = data.address;
                                        queueKycDraftSave();
                                    }

                                    verifyBtn.innerHTML = '<i class="fas fa-check"></i> Verified';
                                    verifyBtn.disabled = true;
                                    gstField.readOnly = true;
                                    businessNameField.readOnly = true;
                                    if (continueBtn) continueBtn.focus();
                                } else {
                                    if (statusDiv) {
                                        statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || 'GST verification failed.');
                                        statusDiv.style.display = 'block';
                                        statusDiv.style.color = '#dc3545';
                                    }
                                    verifyBtn.innerHTML = 'Verify GST';
                                    verifyBtn.disabled = false;
                                    showKycAlert('GST verification failed', data.message ||
                                        'GST verification failed. Please check the number and try again.');
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
                                showKycAlert('Connection error',
                                    'A network error occurred while verifying your GST. Please try again.');
                            });
                    }

                    function restoreStoredKycDocPreview(docField, placeholderId, previewId, fileNameId) {
                        const path = kycData[docField];
                        if (!path || typeof path !== 'string' || !path.startsWith('uploads/')) return;
                        const placeholder = document.getElementById(placeholderId);
                        const preview = document.getElementById(previewId);
                        const fileNameEl = document.getElementById(fileNameId);
                        if (placeholder) placeholder.style.display = 'none';
                        if (preview) preview.style.display = 'block';
                        if (fileNameEl) fileNameEl.textContent = path.split('/').pop();
                    }

                    function restoreVerifiedState(fieldId, buttonId, statusId, verified, message, relatedFieldId) {
                        if (!verified) return;
                        const field = document.getElementById(fieldId);
                        const relatedField = relatedFieldId ? document.getElementById(relatedFieldId) : null;
                        const button = document.getElementById(buttonId);
                        const status = document.getElementById(statusId);
                        if (field) field.readOnly = true;
                        if (relatedField) relatedField.readOnly = true;
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
                        // Billing Address fallback: the GST-verified address is kept in
                        // the session, so it survives a refresh even before the draft
                        // autosave has captured the prefilled value.
                        if (!kycData.billing_address) {
                            kycData.billing_address = @json(session('kyc_gst_address', ''));
                        }
                        const values = {
                            aadharInput: kycData.aadhar_number ? String(kycData.aadhar_number).replace(/(.{4})(?=.)/g, '$1 ') : '',
                            panInput: kycData.pan_number,
                            panHolderName: kycData.pan_holder_name,
                            panDob: formatDisplayDob(kycData.pan_dob),
                            bizGstCertNumber: kycData.gst_certificate_number,
                            bizGstBusinessName: kycData.gst_business_name || kycData.organization_name,
                            bizIecNumber: kycData.iec_number,
                            bizAdCode: kycData.ad_code,
                            bizLutNumber: kycData.lut_number,
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

                        const gstType = document.getElementById('bizGstType');
                        const lutType = document.getElementById('bizLutType');
                        const isCheckedValue = value => value === true || value === 1 || value === '1';
                        kycData.is_gst = isCheckedValue(kycData.is_gst);
                        kycData.is_lut = isCheckedValue(kycData.is_lut);
                        if (gstType) gstType.checked = kycData.is_gst;
                        if (lutType) lutType.checked = kycData.is_lut;
                        toggleBusinessLutDetails();
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

                        // File inputs cannot be restored from a JSON draft; a newly
                        // selected file replaces the stored document on submit.
                        delete kycData.signature;
                        delete kycData.business_signature;

                        // Restore previews for documents already stored server-side.
                        restoreStoredKycDocPreview('aadhar_front_document', 'aadharFrontUploadPlaceholder', 'aadharFrontPreview', 'aadharFrontFileName', true);
                        restoreStoredKycDocPreview('aadhar_back_document', 'aadharBackUploadPlaceholder', 'aadharBackPreview', 'aadharBackFileName', true);
                        restoreStoredKycDocPreview('pan_document', 'panUploadPlaceholder', 'panPreview', 'panFileName', true);
                        restoreStoredKycDocPreview('signature_document', 'signatureUploadPlaceholder', 'signaturePreview', 'signatureFileName', true);
                        if (kycData.signature_document && signaturePreviewImg) {
                            if (signatureUploadPlaceholder) signatureUploadPlaceholder.style.display = 'none';
                            signaturePreviewImg.src = '{{ asset('') }}' + kycData.signature_document;
                            if (signaturePreviewWrap) signaturePreviewWrap.style.display = 'block';
                        }

                        // Business KYC document previews (same stored path fields).
                        restoreStoredKycDocPreview('gst_certificate_document', 'bizGstCertUploadPlaceholder', 'bizGstCertPreview', 'bizGstCertFileName');
                        restoreStoredKycDocPreview('iec_document', 'bizIecUploadPlaceholder', 'bizIecPreview', 'bizIecFileName');
                        restoreStoredKycDocPreview('ad_code_document', 'bizAdCodeUploadPlaceholder', 'bizAdCodePreview', 'bizAdCodeFileName');
                        restoreStoredKycDocPreview('lut_document', 'bizLutUploadPlaceholder', 'bizLutPreview', 'bizLutFileName');
                        if (kycData.signature_document && businessSignaturePreviewImg) {
                            const bizSignaturePlaceholder = document.getElementById('bizSignatureUploadPlaceholder');
                            if (bizSignaturePlaceholder) bizSignaturePlaceholder.style.display = 'none';
                            businessSignaturePreviewImg.src = '{{ asset('') }}' + kycData.signature_document;
                            if (businessSignaturePreviewWrap) businessSignaturePreviewWrap.style.display = 'block';
                        }

                        restoreVerifiedState('aadharInput', 'verifyAadharBtn', 'aadharStatus',
                            kycData.aadhar_verified,
                            kycData.aadhar_verified
                                ? 'Aadhaar verification restored from your saved KYC.'
                                : 'Aadhaar verification is optional for your account type.',
                            'aadharInput');
                        restoreVerifiedState('panInput', 'verifyPanBtn', 'panStatus', kycData.pan_verified,
                            'PAN verification restored from your saved KYC.', 'panInput');
                        restoreVerifiedState('bizGstCertNumber', 'bizVerifyGstCertBtn', 'bizGstStatus',
                            kycData.gst_certificate_verified || kycData.gst_verified,
                            'GST Certificate verification restored from your saved KYC.',
                            'bizGstBusinessName');
                        renderKycStep(savedKycStep, false);
                    }

                    function toggleBusinessLutDetails() {
                        const lutType = document.getElementById('bizLutType');
                        const section = document.getElementById('bizLutDetailsSection');
                        if (section) section.style.display = lutType && lutType.checked ? '' : 'none';
                    }

                    // The Business KYC CSB-V step is optional for eCommerce accounts. This clears any
                    // partial CSB-V data and advances to the Signature step without validating it.
                    function skipCsbVStep() {
                        if (!csbVOptional) return;
                        ['bizGstType', 'bizLutType'].forEach(function(id) {
                            const el = document.getElementById(id);
                            if (el) el.checked = false;
                        });
                        ['bizIecNumber', 'bizAdCode', 'bizLutNumber', 'bizLutExpiry',
                            'bizLutBondStartYear', 'bizLutBondEndYear', 'bizLutBondYear',
                            'bizBankType', 'bizBankAccount', 'bizBillingGst',
                            'bizBillingContact', 'bizBillingEmail', 'bizBillingAddress'
                        ].forEach(function(id) {
                            const el = document.getElementById(id);
                            if (el) el.value = '';
                        });
                        ['bizIecFileInput', 'bizAdCodeFileInput', 'bizLutFileInput'].forEach(function(id) {
                            const el = document.getElementById(id);
                            if (el) el.value = '';
                        });
                        // Hide any restored previews for CSB-V documents and restore their placeholders.
                        ['bizIec', 'bizAdCode', 'bizLut'].forEach(function(prefix) {
                            const preview = document.getElementById(prefix + 'Preview');
                            const placeholder = document.getElementById(prefix + 'UploadPlaceholder');
                            if (preview) preview.style.display = 'none';
                            if (placeholder) placeholder.style.display = '';
                        });
                        toggleBusinessLutDetails();
                        ['iec_document', 'ad_code_document', 'lut_document'].forEach(function(key) {
                            kycData[key] = '';
                        });
                        kycData.is_gst = false;
                        kycData.is_lut = false;
                        kycData.is_csb_v = false;
                        nextStep(5);
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
                        const lutType = document.getElementById('bizLutType');
                        if (lutType) lutType.addEventListener('change', toggleBusinessLutDetails);

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

                        captureKycDraftData();

                        // Build FormData for file uploads
                        const formData = new FormData();
                        formData.append('is_csb_v', kycData.is_csb_v ? 1 : 0);
                        formData.append('is_gst', kycData.is_gst ? 1 : 0);
                        formData.append('is_lut', kycData.is_lut ? 1 : 0);
                        formData.append('gst_business_name', kycData.gst_business_name || '');
                        formData.append('gst_certificate_number', kycData.gst_certificate_number || kycData.gst_number || '');
                        formData.append('pan_number', kycData.pan_number || '');
                        formData.append('pan_holder_name', kycData.pan_holder_name || '');
                        // Flatpickr displays DD/MM/YYYY, but Laravel's date rule expects
                        // an unambiguous request value such as YYYY-MM-DD.
                        formData.append('pan_dob', formatRequestDob(kycData.pan_dob));
                        formData.append('iec_number', kycData.iec_number || '');
                        formData.append('ad_code', kycData.ad_code || '');
                        formData.append('lut_number', kycData.lut_number || '');
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
                            } else if (kycData[fieldName] && typeof kycData[fieldName] === 'string'
                                && kycData[fieldName].trim() !== '') {
                                // The stored document path may be restored from the
                                // persisted draft/CsbForm even when it does not start
                                // with "uploads/". Any non-empty string path is sent.
                                formData.append(fieldName + '_path', kycData[fieldName]);
                            }
                        });

                        fetch('{{ route("customer.csb5-form.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: formData
                            })
                            .then(async response => {
                                const contentType = response.headers.get('content-type') || '';
                                const data = contentType.includes('application/json')
                                    ? await response.json()
                                    : {
                                        success: false,
                                        message: (await response.text()).trim()
                                    };

                                if (!data.message) {
                                    data.message = 'Something went wrong. Please try again.';
                                }

                                return data;
                            })
                            .then(data => {
                                if (data.success) {
                                    const messageDiv = document.querySelector('#step7-content p.text-muted');
                                    if (messageDiv) {
                                        messageDiv.innerHTML = '<strong>' + data.message + '</strong>';
                                        messageDiv.className = 'text-success mx-auto';
                                    }
                                    if (submitBtn) {
                                        submitBtn.innerHTML = 'Redirecting to Dashboard...';
                                        submitBtn.disabled = true;
                                        submitBtn.onclick = function() {
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                location.reload();
                                            }
                                        };
                                    }
                                    // Show the Activation Pending screen for 5 seconds, then auto-refresh
                                    setTimeout(function() {
                                        if (data.redirect) {
                                            window.location.href = data.redirect;
                                        } else {
                                            location.reload();
                                        }
                                    }, 5000);
                                } else {
                                    const validationErrors = data.errors
                                        ? Object.values(data.errors).flat().join('\n')
                                        : '';
                                    showKycAlert('Attention Needed', validationErrors || data.message || 'Something went wrong. Please try again.');
                                    if (submitBtn) {
                                        submitBtn.innerHTML = 'Go to Dashboard';
                                        submitBtn.disabled = false;
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('CSB submission error:', error);
                                showKycAlert('Attention Needed', 'CSB form submission failed before the server response was received: ' + error.message);
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
                        // Document path keys are transmitted as *_path inputs so the
                        // server can distinguish stored paths from fresh uploads.
                        const kycStoredDocKeys = [
                            'gst_certificate_document', 'aadhar_front_document',
                            'aadhar_back_document', 'pan_document', 'signature_document'
                        ];
                        const kycFileKeys = {
                            gst_certificate_document: 'gst_certificate_file',
                            aadhar_front_document: 'aadhar_front_file',
                            aadhar_back_document: 'aadhar_back_file',
                            pan_document: 'pan_file',
                            signature_document: 'signature_file'
                        };
                        // Append all text fields (skip File objects and legacy signature previews)
                        Object.keys(kycData).forEach(function (key) {
                            const value = kycData[key];
                            if (value === null || value === undefined) return;
                            if (key === 'signature' || key === 'business_signature') return;
                            if (kycStoredDocKeys.indexOf(key) !== -1) return; // handled below
                            if (value instanceof File) return; // handled below
                            // Convert booleans to 1/0 (FormData stringifies, and Laravel's
                            // boolean rule rejects the string "false")
                            if (typeof value === 'boolean') {
                                formData.append(key, value ? 1 : 0);
                            } else {
                                formData.append(key, value);
                            }
                        });
                        // Append file uploads (or their stored draft paths)
                        kycStoredDocKeys.forEach(function (docField) {
                            const localFile = kycData[kycFileKeys[docField]];
                            if (localFile instanceof File) {
                                formData.append(docField, localFile);
                            } else if (kycData[docField]) {
                                formData.append(docField + '_path', kycData[docField]);
                            }
                        });

                        // Submit via AJAX (multipart/form-data — do NOT set Content-Type manually)
                        fetch('{{ route("customer.kyc.submit") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                }
                                return response.text().then(text => {
                                    console.error('Non-JSON KYC submit response:', text);
                                    return {
                                        success: false,
                                        message: 'Server error (non-JSON response). Please try again.'
                                    };
                                });
                            })
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
                                        submitBtn.innerHTML = 'Redirecting to Dashboard...';
                                        submitBtn.disabled = true;
                                        submitBtn.onclick = () => location.reload();
                                    }

                                    // Show the Activation Pending screen for 5 seconds, then auto-refresh
                                    setTimeout(() => location.reload(), 5000);
                                } else {
                                    // Show error with the actual reason (validation errors or server message)
                                    const validationErrors = data.errors
                                        ? Object.values(data.errors).flat().join('\n')
                                        : '';
                                    showKycAlert('KYC submission failed', validationErrors
                                        ? '<strong>The following problems were found:</strong><br><br>' +
                                        validationErrors.replace(/\n/g, '<br>')
                                        : (data.message || 'KYC submission failed. Please try again.'));
                                    // Reset button
                                    if (submitBtn) {
                                        submitBtn.innerHTML = 'Go to Dashboard';
                                        submitBtn.disabled = false;
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showKycAlert('Connection error',
                                    'An error occurred while submitting your KYC application. Please check your internet connection and try again.');
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
                    <!-- Restricted Access State (KYC not approved) -->
                    <div class="kyc-restricted-panel">
                        <div class="kyc-restricted-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4 class="kyc-restricted-title">Dashboard Access Restricted</h4>
                        <p class="kyc-restricted-text">Complete your KYC verification to access all dashboard features, including live reports and compliance monitoring.</p>
                        <a href="{{ url('/terms-and-conditions') }}" class="kyc-restricted-link">Learn about our KYC process</a>
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
                                                <p class="fs-14 mb-1 text-dark">In-Transit to Hub</p>
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
                    @if(! $skipCsbV)
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
                                            <a href="{{ url('/customer/csb5-form') }}" class="text-success"
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
                    @endif
                    <!-- end row -->





                    <!-- Recent Orders Section -->
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2 mt-4">
                        <h6 class="mb-0">Recent Orders <span class="badge bg-soft-primary text-primary ms-1">{{ $recentShipments->count() }} Orders</span></h6>
                        <a href="{{ route('customer.view-all-shipments') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            View All <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-0">
                            @if($recentShipments->isEmpty())
                                <div class="text-center text-muted py-5 px-3">
                                    <i class="ti ti-package-off fs-32 d-block mb-2"></i>
                                    <p class="mb-0">No recent orders found.</p>
                                </div>
                            @else
                                <div class="table-responsive recent-orders-table">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3 text-nowrap">AWBNO</th>
                                                <th class="text-nowrap">Date</th>
                                                <th class="text-nowrap">Destination</th>
                                                <th class="text-nowrap">Service</th>
                                                <th class="text-nowrap">Network</th>
                                                <th class="text-nowrap">Network No.</th>
                                                <th class="text-nowrap text-center">PCS</th>
                                                <th class="text-nowrap text-end">CHG Weight</th>
                                                <th class="text-nowrap">Status</th>
                                                <th class="pe-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentShipments as $shipment)
                                                @php
                                                    $status = $shipment->status ?: 'draft';
                                                    $statusStyles = [
                                                        'draft' => ['label' => 'Draft', 'class' => 'bg-warning text-dark'],
                                                        'ready' => ['label' => 'Ready', 'class' => 'bg-info'],
                                                        'packed' => ['label' => 'Packed', 'class' => 'bg-primary'],
                                                        'manifested' => ['label' => 'Manifested', 'class' => 'bg-primary'],
                                                        'received' => ['label' => 'In-Transit to Hub', 'class' => 'bg-warning text-dark'],
                                                        'confirm_pickup' => ['label' => 'In-Transit to Hub', 'class' => 'bg-warning text-dark'],
                                                        'assigned_for_pickup' => ['label' => 'In-Transit to Hub', 'class' => 'bg-warning text-dark'],
                                                        'dispatched' => ['label' => 'Dispatched', 'class' => 'bg-info'],
                                                        'delivered' => ['label' => 'Delivered', 'class' => 'bg-success'],
                                                        'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-danger'],
                                                        'disputed' => ['label' => 'Disputed', 'class' => 'bg-danger'],
                                                        'on_hold' => ['label' => 'On Hold', 'class' => 'bg-warning text-dark'],
                                                    ];
                                                    $statusStyle = $statusStyles[$status] ?? ['label' => ucfirst(str_replace('_', ' ', $status)), 'class' => 'bg-secondary'];
                                                    $destination = $shipment->consigneeInfo?->delivery_destination ?: ($shipment->consigneeInfo?->city ?: '-');
                                                    $service = $shipment->serviceRate?->service;
                                                    $packages = $shipment->packageDimensions;
                                                    $chargeableWeight = $packages->sum(fn ($package) => (float) $package->chargeable_weight);
                                                @endphp
                                                <tr>
                                                    <td class="ps-3 text-nowrap">
                                                        <span class="fw-semibold text-primary">{{ $shipment->awb_number ?: 'Pending' }}</span>
                                                    </td>
                                                    <td class="text-nowrap">{{ optional($shipment->created_at)->format('d M Y') ?: '-' }}</td>
                                                    <td>
                                                        <span class="d-inline-block text-truncate" style="max-width: 150px;" title="{{ $destination }}">{{ $destination }}</span>
                                                    </td>
                                                    <td class="text-nowrap">{{ $shipment->shipping_method ?: ($service?->description ?: '-') }}</td>
                                                    <td class="text-nowrap">{{ $service?->network ?: '-' }}</td>
                                                    <td class="text-nowrap">{{ $service?->service_code ?: ($service?->method_code ?: '-') }}</td>
                                                    <td class="text-center">{{ $packages->count() ?: 1 }}</td>
                                                    <td class="text-end text-nowrap">{{ number_format($chargeableWeight, 2) }} kg</td>
                                                    <td class="text-nowrap"><span class="badge {{ $statusStyle['class'] }}">{{ $statusStyle['label'] }}</span></td>
                                                    <td class="pe-3 text-center">
                                                        <a href="{{ route('customer.view-all-shipments', ['awb_number' => $shipment->awb_number]) }}" class="btn btn-sm btn-light rounded-circle" title="View order" aria-label="View order">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- End Recent Orders Section -->

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
                                                <a href="{{ route('customer.wallet-history') }}"
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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof flatpickr !== 'function') return;

                var panDob = document.getElementById('panDob');
                if (panDob && typeof flatpickr === 'function') {
                    flatpickr(panDob, {
                        dateFormat: 'd/m/Y',
                        maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)),
                        allowInput: false,
                        onChange: function () {
                            invalidatePanVerification();
                        }
                    });
                }

                var lutExpiry = document.getElementById('bizLutExpiry');
                if (lutExpiry) {
                    flatpickr(lutExpiry, {
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'd/m/Y',
                        allowInput: false,
                        disableMobile: true
                    });
                    syncKycDatePickerValue('bizLutExpiry', kycData.lut_expiry_date);
                }
            });
        </script>

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

        <script src="../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" defer></script>
        <script defer src="https://static.cloudflareinsights.com/beacon.min.js"></script>

</body>


</html>
