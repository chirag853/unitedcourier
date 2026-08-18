<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Customer Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
    <meta name="keywords" content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .profile-cover {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            min-height: 140px;
            border-radius: 12px 12px 0 0;
            position: relative;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: 700;
            color: #1e3a8a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: absolute;
            bottom: -50px;
            left: 32px;
        }
        .profile-header-card {
            background: #fff;
            border-radius: 0 0 12px 12px;
            padding: 60px 32px 20px 32px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .profile-header-card h3 {
            margin-bottom: 4px;
            font-weight: 700;
            color: #1e293b;
        }
        .profile-header-card .text-muted {
            font-size: 14px;
        }
        .badge-status {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .badge-kyc-pending { background: #fef3c7; color: #92400e; }
        .badge-kyc-approved { background: #fef3c7; color: #92400e; }
        .badge-kyc-rejected { background: #fee2e2; color: #991b1b; }
        .badge-kyc-under-review { background: #dbeafe; color: #1e40af; }
        .badge-type { background: #e0e7ff; color: #3730a3; }

        .detail-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .detail-card h5 {
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .detail-grid .detail-row {
            display: block;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-grid .detail-row:last-child { border-bottom: 1px solid #f1f5f9; }
        .detail-grid .detail-row .label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 4px;
            min-width: 0;
        }
        .detail-grid .detail-row .value {
            display: block;
            text-align: left;
            font-size: 13.5px;
            color: #1e293b;
            font-weight: 600;
            word-break: break-word;
        }
        .personal-info-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        @media (max-width: 992px) {
            .detail-grid,
            .personal-info-grid { grid-template-columns: 1fr; }
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row .label {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            min-width: 160px;
        }
        .detail-row .value {
            font-size: 14px;
            color: #1e293b;
            font-weight: 600;
            text-align: right;
            flex: 1;
            word-break: break-word;
        }
        .status-verified {
            color: #16a34a;
            font-weight: 600;
            font-size: 13px;
        }
        .status-pending {
            color: #d97706;
            font-weight: 600;
            font-size: 13px;
        }
        .doc-link {
            color: #1565c0;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }
        .doc-link:hover { text-decoration: underline; }
        .doc-empty {
            color: #94a3b8;
            font-style: italic;
            font-size: 13px;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-activate { background: #16a34a; color: #fff; }
        .btn-activate:hover { background: #15803d; color: #fff; }
        .btn-deactivate { background: #dc2626; color: #fff; }
        .btn-deactivate:hover { background: #b91c1c; color: #fff; }
        .btn-back { background: #64748b; color: #fff; }
        .btn-back:hover { background: #475569; color: #fff; }
        .btn-reset-pwd { background: #f59e0b; color: #fff; }
        .btn-reset-pwd:hover { background: #d97706; color: #fff; }
        .btn-recharge { background: #2563eb; color: #fff; }
        .btn-recharge:hover { background: #1d4ed8; color: #fff; }
        .btn-kyc-approve { background: #16a34a; color: #fff; }
        .btn-kyc-approve:hover { background: #15803d; color: #fff; }
        .btn-kyc-reject { background: #dc2626; color: #fff; }
        .btn-kyc-reject:hover { background: #b91c1c; color: #fff; }
        .btn-export { background: #059669; color: #fff; }
        .btn-export:hover { background: #047857; color: #fff; }
        .kyc-doc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 8px;
        }
        .kyc-doc-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
        }
        .kyc-doc-card .doc-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            display: block;
            margin-bottom: 4px;
        }
        .wallet-balance {
            font-size: 28px;
            font-weight: 700;
            color: #16a34a;
        }

        /* Let both sides flow independently so tall KYC cards do not create gaps. */
        .profile-details-row {
            align-items: flex-start;
        }
        .profile-details-row > .profile-column {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .profile-details-row > .profile-column > .detail-card {
            width: 100%;
            min-width: 0;
        }
        .profile-header-card > .d-flex {
            align-items: flex-start !important;
        }
        .profile-header-card > .d-flex > div:last-child {
            justify-content: flex-end;
        }
        @media (max-width: 991.98px) {
            .profile-header-card > .d-flex > div:last-child {
                justify-content: flex-start;
            }
        }

        /* ===== KYC Read-only Wizard (same UI as the KYC form) ===== */
        .admin-kyc-view {
            --brand-blue-main: #2563eb;
            --brand-purple: #7c3aed;
            --input-bg: #f8fafc;
            --input-border: #e2e8f0;
            --step-inactive: #e2e8f0;
            --text-muted: #64748b;
            --text-dark: #1e293b;
        }
        .admin-kyc-view .stepper-container { max-width: 1100px; margin: 0 auto 30px; }
        .admin-kyc-view .stepper-title { text-align: center; font-size: 24px; font-weight: 700; margin-bottom: 30px; color: var(--text-dark); }
        .admin-kyc-view .gradient-text {
            background: linear-gradient(90deg, var(--brand-blue-main), var(--brand-purple));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
        }
        .admin-kyc-view .stepper-wrapper { display: flex; justify-content: space-between; position: relative; margin-bottom: 40px; }
        .admin-kyc-view .step-item { flex: 1; position: relative; text-align: left; padding-right: 15px; cursor: pointer; }
        .admin-kyc-view .step-bar { height: 6px; background: var(--step-inactive); border-radius: 10px; margin-bottom: 12px; position: relative; overflow: hidden; }
        .admin-kyc-view .step-bar-fill { position: absolute; height: 100%; width: 0%; background: linear-gradient(to right, var(--brand-blue-main), var(--brand-purple)); transition: width 0.4s ease; }
        .admin-kyc-view .step-item.active .step-bar-fill,
        .admin-kyc-view .step-item.completed .step-bar-fill { width: 100%; }
        .admin-kyc-view .step-label { font-size: 12px; font-weight: 700; color: var(--text-muted); line-height: 1.4; transition: color 0.3s; text-transform: uppercase; letter-spacing: 0.3px; }
        .admin-kyc-view .step-item.active .step-label { color: var(--brand-blue-main); }
        .admin-kyc-view .kyc-card { background: #fff; border-radius: 20px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); padding: 30px 32px; border: 1px solid #f1f5f9; }
        .admin-kyc-view .kyc-card-title { font-size: 24px; font-weight: 700; margin-bottom: 10px; color: var(--text-dark); }
        .admin-kyc-view .form-label-custom { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #adb5bd; margin-bottom: 8px; display: block; }
        .admin-kyc-view .input-group-custom { position: relative; display: flex; align-items: center; margin-bottom: 20px; }
        .admin-kyc-view .input-group-custom i { position: absolute; left: 16px; color: #adb5bd; font-size: 14px; pointer-events: none; }
        .admin-kyc-view .input-custom { background-color: var(--input-bg); border: 1px solid var(--input-border); border-radius: 12px; padding: 13px 16px 13px 42px; font-weight: 500; font-size: 14px; width: 100%; color: var(--text-dark); }
        .admin-kyc-view .input-custom[readonly] { background-color: #eef2f7; color: #334155; cursor: default; }
        .admin-kyc-view .replica-verify-box { display: flex; align-items: center; justify-content: center; height: 100%; min-height: 50px; }
        .admin-kyc-view .replica-doc-box { border: 2px dashed #c7d2fe; border-radius: 16px; padding: 18px 14px; text-align: center; background: #f8faff; }
        .admin-kyc-view .replica-doc-box .doc-icon { font-size: 30px; color: #6366f1; margin-bottom: 6px; display: block; }
        .admin-kyc-view .replica-doc-box .doc-thumb { max-height: 110px; max-width: 100%; object-fit: contain; border-radius: 8px; background: #fff; padding: 6px; margin-bottom: 8px; }
        .admin-kyc-view .replica-doc-box .doc-name { font-weight: 600; color: #4338ca; font-size: 13px; word-break: break-all; margin-bottom: 6px; }
        .admin-kyc-view .step-content { display: none; }
        .admin-kyc-view .step-content.active { display: block; }
        .admin-kyc-view .stepper-nav { display: flex; align-items: center; gap: 10px; margin-top: 26px; padding-top: 18px; border-top: 1px solid #eef2f7; }
        .admin-kyc-view .stepper-nav-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; border-radius: 12px; border: none; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: all 0.2s; }
        .admin-kyc-view .stepper-prev { background: #fff; border: 2px solid #e2e8f0; color: #64748b; }
        .admin-kyc-view .stepper-prev:hover { background: #f1f5f9; border-color: #cbd5e1; }
        .admin-kyc-view .stepper-next { background: linear-gradient(270deg, var(--brand-blue-main), var(--brand-purple)); color: #fff; margin-left: auto; }
        .admin-kyc-view .stepper-next:hover { opacity: 0.9; }
        .admin-kyc-view .stepper-nav-btn:disabled { opacity: 0.4; cursor: not-allowed; }
        .admin-kyc-view .step-item.skipped .step-bar-fill { background: linear-gradient(to right, #94a3b8, #cbd5e1); }
        .admin-kyc-view .step-item.skipped .step-label { color: #94a3b8; }
        .admin-kyc-view .step-skip-chip { display: inline-block; margin-left: 6px; padding: 2px 8px; border-radius: 20px; background: #f1f5f9; color: #64748b; font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; vertical-align: middle; }

        /* T&C document (Step 6) - scoped to avoid admin theme clashes */
        .admin-kyc-view .document-wrapper { max-width: 100%; margin: 0 auto; background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08); padding: 2rem 2rem; border: 1px solid #eef2f6; }
        .admin-kyc-view .document-wrapper h1,
        .admin-kyc-view .document-wrapper h2,
        .admin-kyc-view .document-wrapper h3,
        .admin-kyc-view .document-wrapper h4 { font-weight: 600; letter-spacing: -0.02em; margin-top: 1.6em; margin-bottom: 0.5em; color: #1a2e4a; }
        .admin-kyc-view .document-wrapper h1 { font-size: 1.8rem; margin-top: 0; margin-bottom: 0.3rem; border-bottom: 2px solid #eef2f6; padding-bottom: 0.4rem; }
        .admin-kyc-view .document-wrapper h2 { font-size: 1.35rem; border-left: 5px solid #1f3a6b; padding: 0.6rem 1rem 0.6rem 1.2rem; background: #f1f5f9; border-radius: 0 40px 40px 0; margin-top: 2rem; }
        .admin-kyc-view .document-wrapper h3 { font-size: 1.1rem; margin-top: 1.6rem; border-bottom: 1px dashed #dce3ec; padding-bottom: 0.3rem; }
        .admin-kyc-view .document-wrapper h4 { font-size: 1rem; margin-top: 1.4rem; }
        .admin-kyc-view .document-wrapper p { margin: 0.8rem 0; color: #334155; font-size: 14px; line-height: 1.7; }
        .admin-kyc-view .document-wrapper ul,
        .admin-kyc-view .document-wrapper ol { padding-left: 1.6rem; margin: 0.8rem 0 1rem 0; }
        .admin-kyc-view .document-wrapper li { margin: 0.35rem 0; color: #334155; font-size: 14px; line-height: 1.7; }
        .admin-kyc-view .document-wrapper hr { border: 0; border-top: 2px solid #e2eaf2; margin: 2rem 0; }
        .admin-kyc-view .subhead-company { font-size: 1.15rem; font-weight: 500; color: #1f3a6b; margin-top: -0.1rem; margin-bottom: 1.2rem; display: block; }
        .admin-kyc-view .underline-title { text-decoration: underline; text-underline-offset: 4px; text-decoration-thickness: 2px; text-decoration-color: #b3c9e0; }
        .admin-kyc-view .consolidated-note { background: #f1f7fe; border-left: 6px solid #1f3a6b; padding: 0.8rem 1.6rem; border-radius: 16px; margin: 2rem 0 0.5rem 0; }
        .admin-kyc-view .glossary-item { display: flex; flex-wrap: wrap; margin: 0.6rem 0; border-bottom: 1px solid #edf2f8; padding-bottom: 0.6rem; }
        .admin-kyc-view .glossary-term { font-weight: 600; min-width: 160px; color: #12233f; font-size: 14px; }
        .admin-kyc-view .glossary-def { flex: 1; color: #1d2f4a; font-size: 14px; line-height: 1.7; }
        .admin-kyc-view .contact-box { background: #eef4fa; border-radius: 60px; padding: 0.7rem 1.6rem; display: inline-block; font-size: 0.95rem; margin: 0.8rem 0; }
        .admin-kyc-view .contact-box a { color: #0b2b5c; font-weight: 500; text-decoration: none; border-bottom: 1px dotted #3a5f89; }
        .admin-kyc-view .small-meta { font-size: 0.9rem; color: #3d5779; background: #f2f6fc; padding: 0.2rem 0.8rem; border-radius: 30px; display: inline-block; }
        @media (max-width: 768px) {
            .admin-kyc-view .stepper-wrapper { flex-direction: column; gap: 15px; }
            .admin-kyc-view .kyc-card { padding: 24px 16px; }
            .admin-kyc-view .document-wrapper { padding: 1.4rem 1.1rem; }
        }
    </style>
</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        @include('admin.partials.header')

        @include('admin.partials.sidebar')

        <!-- ========================
            Start Page Content
        ========================= -->

        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content pb-0">

                @if($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-check me-2"></i>
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Action Bar -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <a href="{{ url()->previous() }}" class="action-btn btn-back">
                        <i class="ti ti-arrow-left"></i> Back
                    </a>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.kyc-export', ['status' => 'all']) }}" class="action-btn btn-export">
                            <i class="ti ti-file-export"></i> Export All KYC (Excel)
                        </a>
                        @if($customer->status)
                            <form action="{{ route('admin.customer.toggle-status', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to DEACTIVATE this account? The customer will not be able to log in.');">
                                @csrf
                                <button type="submit" class="action-btn btn-deactivate">
                                    <i class="ti ti-user-off"></i> Deactivate Account
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.customer.toggle-status', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to ACTIVATE this account?');">
                                @csrf
                                <button type="submit" class="action-btn btn-activate">
                                    <i class="ti ti-user-check"></i> Activate Account
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Profile Header Card -->
                <div class="profile-cover">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($customer->first_name ?? 'C', 0, 1) . substr($customer->last_name ?? '', 0, 1)) }}
                    </div>
                </div>
                <div class="profile-header-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h3>{{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}</h3>
                            <p class="text-muted mb-2">
                                <i class="ti ti-mail me-1"></i>{{ $customer->email ?? '—' }}
                                @if($customer->phone_number)
                                    &nbsp;|&nbsp; <i class="ti ti-phone me-1"></i>{{ $customer->phone_number }}
                                @endif
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($customer->status)
                                    <span class="badge-status badge-active"><i class="ti ti-circle-check"></i> Active</span>
                                @else
                                    <span class="badge-status badge-inactive"><i class="ti ti-circle-x"></i> Deactivated</span>
                                @endif
                                <span class="badge-status badge-type">{{ $userType }} ({{ $userType === 'Business' ? 'CSB-V' : 'CSB-IV' }})</span>
                                @if($personalKyc)
                                    @php
                                        $kycStatus = $personalKyc->kyc_status ?? 'pending';
                                        $kycBadgeClass = 'badge-kyc-pending';
                                        if ($kycStatus === 'approved') $kycBadgeClass = 'badge-kyc-approved';
                                        elseif ($kycStatus === 'rejected') $kycBadgeClass = 'badge-kyc-rejected';
                                        elseif ($kycStatus === 'under_review') $kycBadgeClass = 'badge-kyc-under-review';
                                    @endphp
                                    <span class="badge-status {{ $kycBadgeClass }}">KYC: {{ ucfirst(str_replace('_', ' ', $kycStatus)) }}</span>
                                @else
                                    <span class="badge-status badge-kyc-pending">KYC: Not Submitted</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($personalKyc && in_array($personalKyc->kyc_status ?? 'pending', ['pending', 'under_review']))
                                <form action="{{ route('admin.kyc-pending.approve', $personalKyc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to APPROVE this KYC? This enables shipment creation and creates the customer wallet.');">
                                    @csrf
                                    <button type="submit" class="action-btn btn-kyc-approve">
                                        <i class="ti ti-circle-check"></i> Approve KYC
                                    </button>
                                </form>
                                <button type="button" class="action-btn btn-kyc-reject" onclick="openRejectKycModal()">
                                    <i class="ti ti-circle-x"></i> Reject KYC
                                </button>
                            @endif
                            <button type="button" class="action-btn btn-reset-pwd" onclick="openResetPasswordModal({{ $customer->id }}, '{{ addslashes($customer->first_name . ' ' . $customer->last_name) }}')">
                                <i class="ti ti-key"></i> Reset Password
                            </button>
                            @if(($personalKyc?->kyc_status === 'approved') || ($businessKyc?->kyc_status === 'approved'))
                            <button type="button" class="action-btn btn-recharge" onclick="openRechargeModal({{ $customer->id }}, '{{ addslashes($customer->first_name . ' ' . $customer->last_name) }}')">
                                <i class="ti ti-wallet"></i> Recharge Wallet
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                                <div class="admin-kyc-view">
                    @php
                        $isBusinessFlow = strcasecmp(trim((string) $userType), 'Business') === 0;
                        $kyc = $isBusinessFlow ? ($businessKyc ?? $personalKyc) : ($personalKyc ?? $businessKyc);
                        $alt = $isBusinessFlow ? $personalKyc : $businessKyc;

                        $docUrl = static function (...$paths) {
                            foreach ($paths as $p) {
                                $path = trim((string) $p);
                                if ($path === '') {
                                    continue;
                                }
                                if (filter_var($path, FILTER_VALIDATE_URL)) {
                                    return $path;
                                }
                                $path = ltrim(str_replace('\\', '/', $path), '/');
                                $path = preg_replace('#^(?:(?:public|uploads)/)+#i', '', $path) ?? $path;
                                if (is_file(public_path('uploads/' . ltrim($path, '/')))) {
                                    return asset('uploads/' . ltrim($path, '/'));
                                }
                            }
                            return null;
                        };

                        $docName = static function ($url) {
                            return $url ? basename((string) parse_url($url, PHP_URL_PATH)) : null;
                        };

                        $isImageDoc = static function ($url) {
                            return (bool) preg_match('/\.(jpe?g|png|gif|webp)$/i', (string) $url);
                        };

                        $aadharNumber = $kyc->aadhar_number ?? $customer->aadhar_number ?? null;
                        $aadharFrontUrl = $docUrl($kyc->aadhar_front_document, $kyc->aadhar_document, $alt?->aadhar_front_document);
                        $aadharBackUrl = $docUrl($kyc->aadhar_back_document, $alt?->aadhar_back_document);
                        $aadharSkipped = !(bool) ($kyc->aadhar_verified) && !$aadharNumber && !$aadharFrontUrl && !$aadharBackUrl;
                    @endphp

                    @if(!$kyc)
                    <div class="detail-card text-center py-5">
                        <i class="ti ti-file-off" style="font-size:48px;color:#cbd5e1;"></i>
                        <h5 class="mt-3 text-muted">No KYC Submitted</h5>
                        <p class="text-muted">This customer has not submitted any KYC yet.</p>
                    </div>
                    @else
                    <div class="stepper-container">
                        <h2 class="stepper-title">KYC Details <span class="gradient-text">{{ $isBusinessFlow ? 'CSB-V' : 'CSB-IV' }}</span></h2>

                        <div class="stepper-wrapper">
                            @if($isBusinessFlow)
                            <div class="step-item active" data-step="1">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">1. Verify GST</div>
                            </div>
                            <div class="step-item @if($aadharSkipped) skipped @endif" data-step="2" @if($aadharSkipped) data-skipped="1" @endif>
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">2. Verify Aadhar @if($aadharSkipped)<span class="step-skip-chip">Skipped</span>@endif</div>
                            </div>
                            <div class="step-item" data-step="3">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">3. Verify PAN</div>
                            </div>
                            <div class="step-item" data-step="4">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">4. CSB-V</div>
                            </div>
                            <div class="step-item" data-step="5">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">5. Upload Signature</div>
                            </div>
                            <div class="step-item" data-step="6">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">6. Terms &amp; Conditions</div>
                            </div>
                            @else
                            <div class="step-item active" data-step="1">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">1. KYC Verification</div>
                            </div>
                            <div class="step-item @if($aadharSkipped) skipped @endif" data-step="2" @if($aadharSkipped) data-skipped="1" @endif>
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">2. Verify Aadhar @if($aadharSkipped)<span class="step-skip-chip">Skipped</span>@endif</div>
                            </div>
                            <div class="step-item" data-step="3">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">3. Verify PAN</div>
                            </div>
                            <div class="step-item" data-step="4">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">4. Basic Info &amp; Signing</div>
                            </div>
                            <div class="step-item" data-step="5">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">5. Upload Signature</div>
                            </div>
                            <div class="step-item" data-step="6">
                                <div class="step-bar"><div class="step-bar-fill"></div></div>
                                <div class="step-label">6. Bill</div>
                            </div>
                            @endif
                        </div>

                        <div class="kyc-card">

                            <!-- Step 1: Verify GST -->
                            <div id="step1-content" class="step-content active">
                                <h3 class="kyc-card-title">Verify <span class="gradient-text">GST</span></h3>
                                <p class="text-muted mb-4">GST details submitted during KYC.</p>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label-custom">GST Certificate Number</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->gst_certificate_number ?? $kyc->gst_number ?? ($alt?->gst_number ?? '—') }}"
                                                readonly>
                                            <i class="ti ti-file-invoice"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-custom d-none d-md-block">&nbsp;</label>
                                        <div class="replica-verify-box">
                                            @if($kyc->gst_verified ?? ($alt?->gst_verified ?? false))
                                                <span class="badge-status badge-kyc-approved"><i class="ti ti-circle-check"></i> Verified</span>
                                            @else
                                                <span class="badge-status badge-kyc-pending"><i class="ti ti-clock"></i> Not Verified</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label-custom">Business Name</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->gst_business_name ?? $kyc->organization_name ?? ($alt?->organization_name ?? '—') }}"
                                                readonly>
                                            <i class="ti ti-building"></i>
                                        </div>
                                    </div>
                                </div>

                                @php $gstCertUrl = $docUrl($kyc->gst_certificate_document, $kyc->gst_document, $alt?->gst_certificate_document); @endphp
                                <div class="row g-4 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label-custom">Upload GST Certificate</label>
                                        <div class="replica-doc-box">
                                            @if($gstCertUrl)
                                                @if($isImageDoc($gstCertUrl))
                                                    <img src="{{ $gstCertUrl }}" class="doc-thumb" alt="GST Certificate">
                                                @else
                                                    <i class="ti ti-file-invoice doc-icon"></i>
                                                @endif
                                                <p class="doc-name">{{ $docName($gstCertUrl) }}</p>
                                                <a class="doc-link document-preview-link" href="{{ $gstCertUrl }}" data-doc-label="GST Certificate"><i class="ti ti-eye"></i> View</a>
                                            @else
                                                <i class="ti ti-file-off doc-icon" style="color:#cbd5e1;"></i>
                                                <p class="mb-0 fw-semibold small" style="color:#94a3b8;">Not uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Verify Aadhar -->
                            <div id="step2-content" class="step-content">
                                @if($aadharSkipped)
                                <div class="text-center py-4">
                                    <i class="ti ti-circle-x" style="font-size:48px;color:#94a3b8;"></i>
                                    <h3 class="kyc-card-title mt-3 mb-2">Verify <span class="gradient-text">Aadhar</span> <span class="badge-status badge-kyc-pending ms-1">Skipped</span></h3>
                                    <p class="text-muted mx-auto" style="max-width:520px;">The customer did not provide Aadhaar details during KYC. This step has been skipped.</p>
                                </div>
                                @else
                                <h3 class="kyc-card-title">Verify <span class="gradient-text">Aadhar</span></h3>
                                <p class="text-muted mb-4">Aadhaar number and documents submitted during KYC.</p>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <label class="form-label-custom">Aadhar Number</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $aadharNumber ?? '—' }}"
                                                readonly>
                                            <i class="ti ti-id-card"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-custom d-none d-md-block">&nbsp;</label>
                                        <div class="replica-verify-box">
                                            @if($kyc->aadhar_verified)
                                                <span class="badge-status badge-kyc-approved"><i class="ti ti-circle-check"></i> Verified</span>
                                            @else
                                                <span class="badge-status badge-kyc-pending"><i class="ti ti-clock"></i> Not Verified</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Upload Aadhaar (Front)</label>
                                        <div class="replica-doc-box">
                                            @if($aadharFrontUrl)
                                                @if($isImageDoc($aadharFrontUrl))
                                                    <img src="{{ $aadharFrontUrl }}" class="doc-thumb" alt="Aadhaar Front">
                                                @else
                                                    <i class="ti ti-id-card doc-icon"></i>
                                                @endif
                                                <p class="doc-name">{{ $docName($aadharFrontUrl) }}</p>
                                                <a class="doc-link document-preview-link" href="{{ $aadharFrontUrl }}" data-doc-label="Aadhaar Front"><i class="ti ti-eye"></i> View</a>
                                            @else
                                                <i class="ti ti-file-off doc-icon" style="color:#cbd5e1;"></i>
                                                <p class="mb-0 fw-semibold small" style="color:#94a3b8;">Not uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Upload Aadhaar (Back)</label>
                                        <div class="replica-doc-box">
                                            @if($aadharBackUrl)
                                                @if($isImageDoc($aadharBackUrl))
                                                    <img src="{{ $aadharBackUrl }}" class="doc-thumb" alt="Aadhaar Back">
                                                @else
                                                    <i class="ti ti-id-card doc-icon"></i>
                                                @endif
                                                <p class="doc-name">{{ $docName($aadharBackUrl) }}</p>
                                                <a class="doc-link document-preview-link" href="{{ $aadharBackUrl }}" data-doc-label="Aadhaar Back"><i class="ti ti-eye"></i> View</a>
                                            @else
                                                <i class="ti ti-file-off doc-icon" style="color:#cbd5e1;"></i>
                                                <p class="mb-0 fw-semibold small" style="color:#94a3b8;">Not uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Step 3: Verify PAN -->
                            <div id="step3-content" class="step-content">
                                <h3 class="kyc-card-title">Verify <span class="gradient-text">PAN</span></h3>
                                <p class="text-muted mb-4">PAN details and document submitted during KYC.</p>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label-custom">PAN Number</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->pan_number ?? $alt?->pan_number ?? $customer->pan_number ?? '—' }}"
                                                readonly>
                                            <i class="ti ti-file-invoice"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-custom d-none d-md-block">&nbsp;</label>
                                        <div class="replica-verify-box">
                                            @if($kyc->pan_verified ?? ($alt?->pan_verified ?? false))
                                                <span class="badge-status badge-kyc-approved"><i class="ti ti-circle-check"></i> Verified</span>
                                            @else
                                                <span class="badge-status badge-kyc-pending"><i class="ti ti-clock"></i> Not Verified</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">PAN Holder Name</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->pan_holder_name ?? $alt?->pan_holder_name ?? '—' }}"
                                                readonly>
                                            <i class="ti ti-user"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Date of Birth</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ ($kyc->pan_dob ?? $alt?->pan_dob)?->format('d M Y') ?? '—' }}"
                                                readonly>
                                            <i class="ti ti-calendar"></i>
                                        </div>
                                    </div>
                                </div>

                                @php $panUrl = $docUrl($kyc->pan_document, $alt?->pan_document); @endphp
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Upload PAN Card</label>
                                        <div class="replica-doc-box">
                                            @if($panUrl)
                                                @if($isImageDoc($panUrl))
                                                    <img src="{{ $panUrl }}" class="doc-thumb" alt="PAN Card">
                                                @else
                                                    <i class="ti ti-file-invoice doc-icon"></i>
                                                @endif
                                                <p class="doc-name">{{ $docName($panUrl) }}</p>
                                                <a class="doc-link document-preview-link" href="{{ $panUrl }}" data-doc-label="PAN Card"><i class="ti ti-eye"></i> View</a>
                                            @else
                                                <i class="ti ti-file-off doc-icon" style="color:#cbd5e1;"></i>
                                                <p class="mb-0 fw-semibold small" style="color:#94a3b8;">Not uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($isBusinessFlow)
                            <!-- Step 4 (Business): CSB-V -->
                            <div id="step4-content" class="step-content">
                                <h3 class="kyc-card-title">CSB-<span class="gradient-text">V</span></h3>
                                <p class="text-muted mb-4">CSB-V details submitted during KYC: Export Codes, LUT, Banking and Billing.</p>

                                <h5 class="fw-bold mt-2 mb-3" style="color:#4338ca;"><i class="ti ti-file-export me-2"></i>Export Codes</h5>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label-custom">IEC Number</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->iec_number ?? '—' }}" readonly>
                                            <i class="ti ti-file-export"></i>
                                        </div>
                                    </div>
                                    @php $iecUrl = $docUrl($kyc->iec_document); @endphp
                                    <div class="col-md-4">
                                        <label class="form-label-custom">Upload IEC Certificate</label>
                                        <div class="replica-doc-box" style="padding:12px 10px;">
                                            @if($iecUrl)
                                                <p class="doc-name">{{ $docName($iecUrl) }}</p>
                                                <a class="doc-link document-preview-link" href="{{ $iecUrl }}" data-doc-label="IEC Certificate"><i class="ti ti-eye"></i> View</a>
                                            @else
                                                <i class="ti ti-file-off doc-icon" style="color:#cbd5e1;font-size:22px;"></i>
                                                <p class="mb-0 fw-semibold small" style="color:#94a3b8;">Not uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label-custom">AD Code</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->ad_code ?? '—' }}" readonly>
                                            <i class="ti ti-numbers"></i>
                                        </div>
                                    </div>
                                    @php $adCodeUrl = $docUrl($kyc->ad_code_document); @endphp
                                    <div class="col-md-4">
                                        <label class="form-label-custom">Upload AD Code Document</label>
                                        <div class="replica-doc-box" style="padding:12px 10px;">
                                            @if($adCodeUrl)
                                                <p class="doc-name">{{ $docName($adCodeUrl) }}</p>
                                                <a class="doc-link document-preview-link" href="{{ $adCodeUrl }}" data-doc-label="AD Code Document"><i class="ti ti-eye"></i> View</a>
                                            @else
                                                <i class="ti ti-file-off doc-icon" style="color:#cbd5e1;font-size:22px;"></i>
                                                <p class="mb-0 fw-semibold small" style="color:#94a3b8;">Not uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <h5 class="fw-bold mt-4 mb-3" style="color:#4338ca;"><i class="ti ti-file-text me-2"></i>LUT Details</h5>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">LUT (Against Bond or UT)</label>
                                        <div class="replica-verify-box" style="justify-content:flex-start;">
                                            @if($kyc->is_lut)
                                                <span class="badge-status badge-kyc-approved"><i class="ti ti-circle-check"></i> Yes</span>
                                            @else
                                                <span class="badge-status badge-kyc-pending"><i class="ti ti-circle-x"></i> No</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">LUT Bond Year</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->lut_bond_year ?? '—' }}" readonly>
                                            <i class="ti ti-calendar"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">LUT Expiry Date</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->lut_expiry_date?->format('d M Y') ?? '—' }}" readonly>
                                            <i class="ti ti-calendar"></i>
                                        </div>
                                    </div>
                                    @php $lutUrl = $docUrl($kyc->lut_document); @endphp
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Upload LUT Document</label>
                                        <div class="replica-doc-box" style="padding:12px 10px;">
                                            @if($lutUrl)
                                                <p class="doc-name">{{ $docName($lutUrl) }}</p>
                                                <a class="doc-link document-preview-link" href="{{ $lutUrl }}" data-doc-label="LUT Document"><i class="ti ti-eye"></i> View</a>
                                            @else
                                                <i class="ti ti-file-off doc-icon" style="color:#cbd5e1;font-size:22px;"></i>
                                                <p class="mb-0 fw-semibold small" style="color:#94a3b8;">Not uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <h5 class="fw-bold mt-4 mb-3" style="color:#4338ca;"><i class="ti ti-building-bank me-2"></i>Bank Details</h5>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Bank Category</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ ucfirst($kyc->bank_type ?? '—') }}" readonly>
                                            <i class="ti ti-building-bank"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Bank Account Number</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->bank_account_number ?? '—' }}" readonly>
                                            <i class="ti ti-credit-card"></i>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="fw-bold mt-4 mb-3" style="color:#4338ca;"><i class="ti ti-receipt-2 me-2"></i>Billing Details</h5>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Billing GST</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->billing_gst ?? ($kyc->gst_certificate_number ?? ($kyc->gst_number ?? ($alt?->gst_number ?? '—'))) }}" readonly>
                                            <i class="ti ti-file-invoice"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Billing Contact</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->billing_contact ?? '—' }}" readonly>
                                            <i class="ti ti-phone"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Billing Email</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->billing_email ?? '—' }}" readonly>
                                            <i class="ti ti-mail"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Billing Address</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->billing_address ?? '—' }}" readonly>
                                            <i class="ti ti-map-pin"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <!-- Step 4 (Personal): Business Details -->
                            <div id="step4-content" class="step-content">
                                <h3 class="kyc-card-title">Business <span class="gradient-text">Details</span></h3>
                                <p class="text-muted mb-4">Details provided for the digital agreement.</p>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Organization Name</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->organization_name ?? '—' }}" readonly>
                                            <i class="ti ti-building"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Authorized Signatory</label>
                                        <div class="input-group-custom">
                                            <input type="text" class="form-control input-custom"
                                                value="{{ $kyc->authorized_signatory ?? '—' }}" readonly>
                                            <i class="ti ti-user-tie"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Step 5: Upload Signature -->
                            <div id="step5-content" class="step-content">
                                <h3 class="kyc-card-title">Upload <span class="gradient-text">Signature</span></h3>
                                <p class="text-muted mb-4">Authorized signature uploaded during KYC.</p>

                                @php $signatureUrl = $docUrl($kyc->signature_document, $alt?->signature_document); @endphp
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label-custom">Authorized Signature (with Company Stamp)</label>
                                        <div class="replica-doc-box">
                                            @if($signatureUrl)
                                                @if($isImageDoc($signatureUrl))
                                                    <img src="{{ $signatureUrl }}" class="doc-thumb" alt="Signature" style="max-height:140px;">
                                                @else
                                                    <i class="ti ti-signature doc-icon" style="font-size:48px;"></i>
                                                @endif
                                                <p class="doc-name">{{ $docName($signatureUrl) }}</p>
                                                <a class="doc-link document-preview-link" href="{{ $signatureUrl }}" data-doc-label="Authorized Signature"><i class="ti ti-eye"></i> View</a>
                                            @else
                                                <i class="ti ti-signature doc-icon" style="font-size:48px;color:#cbd5e1;"></i>
                                                <p class="mb-0 fw-semibold small" style="color:#94a3b8;">Not uploaded</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 6: Terms & Conditions / Bill -->
                            <div id="step6-content" class="step-content">
                                <h3 class="kyc-card-title">Terms &amp; <span class="gradient-text">Conditions</span></h3>
                                <p class="text-muted mb-4">
                                    Merchant Agreement accepted by the customer during KYC.
                                    @if($kyc->terms_accepted ?? ($alt?->terms_accepted ?? false))
                                        <span class="badge-status badge-kyc-approved"><i class="ti ti-circle-check"></i> Accepted</span>
                                    @else
                                        <span class="badge-status badge-kyc-pending"><i class="ti ti-clock"></i> Not Accepted</span>
                                    @endif
                                </p>

                                <div class="document-wrapper">
                                    <!-- TITLE -->
                                    <h1><span class="underline-title">TERMS AND CONDITIONS</span></h1>
                                    <span class="subhead-company"><strong>UNITED WORLDWIDE COURIERS PVT LTD</strong></span>

                                    <!-- Agreement -->
                                    <h2>Agreement</h2>
                                    <p>These terms and conditions forms a binding agreement ("Terms &amp; Conditions") between you and the Legal Entity that you are representing, if any, (hereinafter collectively referred to as "You" or "Your" or "User", which expression shall, unless it is repugnant to the subject or context thereof, mean and include its legal heirs, executors, administrators, successors, affiliates, and permitted assigns) <strong>ON THE ONE PART</strong> and UNITED WORLDWIDE COURIERS PVT LTD , a company registered under the Companies Act, 1956, having its registered office at Building no 1, Lower Ground Floor, Khasra No 505, Bypass Road, Mahipalpur Extension, Mahipalpur, New Delhi, Delhi 110037, (hereinafter referred to as "United Worldwide Couriers" or "We" or "Our" or "Us", which expression shall, unless it is repugnant to the subject or context thereof, mean and include its successors, affiliates and assigns) <strong>ON THE OTHER PART</strong>.</p>
                                    <p>The User and United Worldwide Couriers shall hereinafter collectively be referred to as "Parties" and individually as "Party".</p>

                                    <!-- Introduction -->
                                    <h2>Introduction</h2>
                                    <p>We through our Platforms offer logistic/shipping services to you either through Shipping Vendors or ourselves ("Services") and/or sell certain Products, in accordance and subject to compliance with the terms and conditions contained in the Agreement.</p>
                                    <p>Please read these Terms &amp; Conditions and other documents referred herein carefully before registering on, accessing, browsing, downloading or using any Services or purchasing any Products offered on the Platforms or any electronic device.</p>

                                    <!-- Eligibility -->
                                    <h2>Eligibility</h2>
                                    <p>The platforms, services, and products are available only to persons competent to enter into legally binding agreements under the Indian Contracts Act, 1872. By using the platforms or services, you represent that you are 18 years of age or older, capable of entering into a legally binding agreement, and not barred from accessing or using the platforms or services. You also represent that you have full rights, powers, and authority to enter into and perform the Agreement and that doing so does not violate any applicable law, covenant, order, judgment, or decree binding on you.</p>

                                    <!-- Account Use -->
                                    <h2>Account Use</h2>
                                    <p>You may possess only one account unless otherwise permitted by United Worldwide Couriers in writing. Login credentials are intended solely for your personal use and must be kept secret and safe. Multiple users may not share the same login, and you may not transfer, assign, sublicense, lease, share, or otherwise permit unauthorized use of any login credentials, access rights, account, token, or system access. You remain responsible for all activities and transactions under your account, whether carried out knowingly, negligently, or by employees, agents, contractors, affiliates, or any other person using your credentials.</p>

                                    <!-- Platform Access -->
                                    <h2>Platform Access</h2>
                                    <p>Subject to compliance with this Agreement, the Company authorizes you personal, non-exclusive, non-transferable, limited, revocable privilege to enter and use Platforms and/or avail all or some of the Services and/or purchase the Products. The availing of any Service(s) by You shall also be subject to compliance with all the other rules, guidelines, policies, terms, and conditions specified by Us for that particular Service(s) being availed by You. You hereby consent and agree to comply with the rules, guidelines, policies, terms and conditions, instructions, requests, etc., as may be specified by United Worldwide Couriers, from time to time, in relation to each Service availed or to be availed by You. Your access to avail the Services will at all times be solely at the discretion of United Worldwide Couriers.</p>
                                    <p>All content, software, layouts, workflows, processes, trademarks, service marks, trade names, dashboards, graphics, text, rate logic, data models, and compilations made available by the Company are proprietary to the Company or its licensors and may not be copied, modified, reverse engineered, distributed, sublicensed, publicly displayed, commercially exploited, or otherwise used without prior written consent. Access to and continued use of the services remains at the sole discretion of the Company.</p>

                                    <!-- User Responsibilities -->
                                    <h2>User Responsibilities</h2>
                                    <p>You represent and warrant that all information, documents, declarations, bank details, tax registrations, KYC records, addresses, phone numbers, contact details, and other data provided to the Company are true, correct, current, complete, lawful, and not misleading. The Company may require supporting documents to verify any information and may suspend or refuse services pending successful verification. You must immediately notify the Company of any unauthorized use of your passcode or account, any breach of security, and any change in your email address, mobile number, or other personal information provided for use of the services.</p>

                                    <!-- Lawful Use -->
                                    <h2>Lawful Use</h2>
                                    <p>You shall use the services only for lawful purposes and strictly in accordance with this Agreement, applicable law, applicable trade controls, customs laws, export-import regulations, sanctions laws, tax laws, and generally accepted commercial practices. You shall not impersonate any person, misrepresent your identity, use the services for unauthorized or unlawful purposes, interfere with the platform or networks, access the services through unauthorized means, or otherwise engage in activity that disrupts or compromises the services or the Company's business. The Company may customize, modify, suspend, restrict, or discontinue any feature, service, integration, route, rate, serviceability option, collection mode, or operational process at any time, including as required by law, government policy, carrier instructions, sanctions, local restrictions, security concerns, or operational reasons.</p>

                                    <!-- Privacy and Data -->
                                    <h2>Privacy and Data</h2>
                                    <p>By using the website and/or by providing your information, you consent to the collection and use of the information you disclose on the website in accordance with the Privacy Policy, including consent for sharing your information as set out in that policy. You agree that the Company may store personally identifiable information such as your name, email address, mobile phone number, address, contact details, postal code, and demographic profile information, as well as browsing information such as pages visited, links clicked, and frequency of access. All such information shall be stored and used in accordance with the Privacy Policy.</p>

                                    <!-- Updates and Amendments -->
                                    <h2>Updates and Amendments</h2>
                                    <p>Any amendment to this Agreement comes into effect immediately upon posting unless otherwise specified, and not on the date on which you may be notified of the change. Any failure or delay in notifying you of changes or amendments does not affect the validity or effectiveness of those changes. Continued access to or use of the platforms and services will be treated as your irrevocable and unconditional acceptance of the amended Agreement. You are solely responsible for keeping yourself updated by regularly reviewing the Agreement on the platforms.</p>

                                    <!-- Communications -->
                                    <h2>Communications</h2>
                                    <p>All notices, communications, updates, invoices, rate revisions, dashboard alerts, service notifications, legal notices, and operational instructions may be issued by the Company through email, dashboard, mobile application, SMS, WhatsApp, registered mobile number, support ticketing system, courier, or any other officially designated communication channel. You consent to receive communications through such channels. The Company may also verify KYC information and share necessary details with carriers, insurers, customs authorities, importer of record entities, marketplace partners, banks, payment partners, police, courts, government agencies, complainants, or any other relevant entity for compliance, claims processing, dispute handling, fraud review, legal proceedings, or operational processing, in accordance with applicable law.</p>

                                    <!-- Booking and Handling -->
                                    <h2>Booking and Handling</h2>
                                    <p>You may choose between United Worldwide Couriers Pickup and Self-Ship, subject to availability. Under United Worldwide Couriers Pickup, the Company will collect shipments from the pickup address you provide; under Self-Ship, you must deliver shipments to the nearest hub. Upon arrival at the hub, each shipment will be scanned, weighed, and sorted according to destination and selected service. If there is a discrepancy between declared and recorded weight, the shipment will be placed on hold and you will be notified by email for approval; once approved, the shipment will proceed to its destination. After a shipment has left the hub, no further weight adjustments will be charged to your account.</p>

                                    <!-- International Shipments -->
                                    <h2>International Shipments</h2>
                                    <p>For international shipments, after export clearance in India, the shipment will be customs-cleared in the destination country and received at the local office before being handed to the last-mile carrier. Last-mile carriers may vary by country and may change from time to time. For shipments routed through branded carrier networks such as DHL, UPS, and FedEx, the shipment is connected to the carrier's hub in Delhi and tracking is available on the carrier's website using the tracking number.</p>

                                    <!-- Delivery and RTO -->
                                    <h2>Delivery and RTO</h2>
                                    <p>The Company will attempt delivery of shipments at least once, and many last-mile shipping vendors attempt delivery twice depending on their policies. If no one is available to receive a shipment, the carrier may, at its discretion, leave it with a neighbour, deposit it at the local post office for customer pickup, or place it in a secure external location outside the house, including a mailbox. A shipment may be deemed undeliverable for reasons including incorrect or incomplete address details, customer refusal to accept delivery, or customer refusal to pay applicable duties and/or taxes. If a shipment is undeliverable, return-to-origin charges will apply, storage charges may apply where applicable, and the shipment may be destroyed, returned, or disposed of in accordance with the relevant service rules.</p>

                                    <!-- Fees and Payment -->
                                    <h2>Fees and Payment</h2>
                                    <p>You shall pay all subscription fees, shipping charges, freight, RTO charges, reverse pickup charges, COD handling charges, customs-related service fees, importer of record charges, storage charges, demurrage, incidental expenses, surcharges, accessorial charges, address correction fees, penalties, taxes, and all other amounts applicable to the services. Unless expressly stated otherwise, all fees are exclusive of taxes and GST and other applicable taxes shall be charged in addition. The Company may add new services for additional charges or revise existing charges, rates, surcharges, accessorial charges, service conditions, or fee structures at any time by notice through dashboard, email, mobile application, rate card, calculator link, annexure, or any other official communication channel.</p>

                                    <!-- Invoicing and Recovery -->
                                    <h2>Invoicing and Recovery</h2>
                                    <p>The Company may issue invoices periodically, including mid-month, month-end, or on such other cycle as determined by the Company. You must verify invoice contents promptly and, unless a shorter period is prescribed for a specific service model, raise any bona fide dispute within five working days of invoice availability and pay undisputed amounts within seven days from the invoice date or such other due date specified in writing. Failure to raise a dispute within the prescribed period constitutes deemed acceptance of the invoice. If you fail to pay any amount when due, the Company may suspend shipping, retain and adjust outstanding amounts against COD remittances, wallet balances, credits, deposits, refunds, or any amounts payable to you, retain custody of shipments, re-route shipments, levy interest at 18 percent per annum from the due date until realization, forfeit security deposit or wallet balance where contractually permitted, and dispose of shipments in accordance with this Agreement where defaults are not regularized within the applicable period.</p>

                                    <!-- COD and Credits -->
                                    <h2>COD and Credits</h2>
                                    <p>For shipments booked under cash on delivery, you appoint the Company as a limited collection agent solely for the purpose of collecting the COD amount from the consignee through the Company's logistics vendors and remitting the balance after deduction of applicable freight, service fees, handling fees, taxes, offsets, reversals, and other lawful deductions. The Company has no title in the goods. Unless otherwise agreed, COD remittance may be made within eight days from delivery of the relevant shipment or in accordance with the remittance cycle then followed by the Company, subject to reconciliation, carrier remittance, fraud review, dispute review, status verification, valid bank details, and absence of offset rights. Where the Merchant operates on a prepaid model, sufficient shipping credits must be maintained in the wallet or account before availing services, and credit balance may be used only for booking shipments. Credit may be forfeited if no shipment is booked for three years from the last shipment date, subject to applicable law, and refunds, if approved, may be restricted to the original source or mode of payment and may be conditioned upon KYC compliance and any surcharge or deduction permitted by law or contract.</p>

                                    <!-- Weight and Pricing -->
                                    <h2>Weight and Pricing</h2>
                                    <p>Each shipment is subject to weight and size limits that may differ by shipping vendor and destination. Volumetric dimensional weight will be calculated automatically in the booking panel using the formula length cm x breadth cm x height cm / 5000. For billing, where volumetric weight is 5 kg or less, charges will be based on actual dead weight; where volumetric weight exceeds 5 kg, charges will be based on whichever is greater, actual weight or volumetric weight. Certain items that require special handling, as defined in the Company's internal operational guidelines, will incur an additional special handling fee.</p>

                                    <!-- Claims and Refunds -->
                                    <h2>Claims and Refunds</h2>
                                    <p>Claims and compensation are available only under the specific circumstances described in the Agreement. To initiate a claim, you must provide the United Worldwide Couriers airway bill number and all supporting documents to Csdunitedcouriers.biz. For claims involving branded carrier networks such as DHL, UPS, and FedEx, the same policy applies subject to carrier-specific timelines. No claim will be considered if submitted more than sixty working days after the inward scan, if the Company notifies you that a shipment is stuck, undelivered, or under RTO and you do not respond within seven working days, or if required evidence and documents are not provided.</p>

                                    <!-- Claim Evidence -->
                                    <h2>Claim Evidence</h2>
                                    <p>For no first scan claims, the required AWB and supporting documents must be submitted, and compensation will be limited in accordance with the applicable limits. For no delivery scan or lost-in-transit claims, you must submit the buyer-seller chat and proof of refund along with the required AWB and documents. For claims alleging non-connectivity, the signed pickup manifest for the disputed shipment must be submitted within three working days of pickup, and claims lacking a signed manifest will not be maintainable. For damage, pilferage, tampering, crushing, or leakage, the recipient must record negative remarks on the proof of delivery at the time of delivery, and such claims will be entertained only if made within forty-eight hours of delivery or receipt and only where the outer packaging applied by the Company or the shipping vendor is damaged, altered, or tampered with.</p>

                                    <!-- Liability -->
                                    <h2>Liability</h2>
                                    <p>The liability of United Worldwide Couriers in relation to the services is strictly limited to the extent expressly provided under this Agreement, the applicable Terms and Conditions, annexures, policies, and service-specific provisions. The platform and services are provided on an as-is, as-available, and reasonable-efforts basis, and the Company does not guarantee uninterrupted, error-free, secure, or continuous operation. The Company does not independently verify, validate, endorse, or authenticate information, declarations, listings, content, documents, data, or materials provided by users, merchants, customers, consignees, shipping vendors, or third parties. Shipments are not insured unless separately purchased by the merchant.</p>

                                    <!-- Indemnity -->
                                    <h2>Indemnity</h2>
                                    <p>You shall indemnify, defend, and hold harmless the Company and its affiliates, directors, officers, employees, agents, subcontractors, consultants, licensors, service providers, shipping partners, customs agents, importer of record entities, marketplace partners, and representatives from and against claims, actions, proceedings, losses, damages, liabilities, penalties, duties, taxes, interest, costs, and expenses arising out of or related to your access to or use of the services, breach of the Agreement, violation of law, misdeclaration, under-declaration, wrongful valuation, wrong HS classification, false origin declaration, counterfeit goods, restricted goods, prohibited goods, dangerous goods, infringing goods, defective or unsafe goods, third-party claims, duties, penalties, detention, demurrage, storage charges, or negligent, wrongful, or fraudulent acts or omissions by you or your personnel.</p>

                                    <!-- Compliance -->
                                    <h2>Compliance</h2>
                                    <p>Each party shall comply with all applicable laws, including state, central, customs, and international laws, statutes, rules, and regulations relating to its performance under this Agreement. Each party shall pay all fees and charges required by applicable law and maintain all licenses, permits, authorizations, registrations, and qualifications necessary to perform its obligations. The Merchant further represents that it lawfully owns, possesses, controls, sells, exports, imports, markets, and ships all goods tendered under this Agreement and has obtained all necessary consents, licenses, registrations, declarations, and approvals required for the services.</p>

                                    <!-- Confidentiality -->
                                    <h2>Confidentiality</h2>
                                    <p>Each party may receive confidential information of/from the other in the course of performance of this Agreement. The receiving party shall keep such information strictly confidential, use it only for performance of this Agreement, restrict disclosure on a strict need-to-know basis, and protect it with at least the same degree of care it uses for its own confidential information and in any event not less than reasonable care. Upon termination or request, the receiving party shall return or destroy confidential information to the extent reasonably practicable and certify compliance if requested.</p>

                                    <!-- Intellectual Property -->
                                    <h2>Intellectual Property</h2>
                                    <p>All intellectual property in the Company's platform, software, systems, APIs, dashboards, workflows, trademarks, brand assets, websites, documents, templates, service descriptions, rate engines, operating methods, and derivative works shall remain vested exclusively in the Company or its licensors. All intellectual property owned by either party before this Agreement shall remain with that party. Any feedback, suggestions, enhancement requests, process improvements, or derivative developments created in connection with the services may be used by the Company without restriction unless otherwise agreed in writing.</p>

                                    <!-- Termination -->
                                    <h2>Termination</h2>
                                    <p>This Agreement begins on the date you first avail the services and continues unless terminated in accordance with this Agreement. You may request termination by thirty days' prior written notice, subject to completion of in-transit shipments, reconciliation, settlement of all dues, submission of documents, discharge of liabilities, and compliance with any service-specific lock-in or closure conditions. The Company may suspend or terminate this Agreement or any account immediately, with or without notice, if you breach the Agreement, create legal, regulatory, reputational, financial, fraud, sanctions, security, or operational risk, ship prohibited or unlawful goods, default in payment, fail KYC or compliance verification, receive instructions from a carrier or authority, or if the Company elects to discontinue the relationship for lawful business convenience.</p>

                                    <!-- Misuse -->
                                    <h2>Misuse</h2>
                                    <p>The Company may restrict, deactivate, suspend, or terminate the account of any Merchant that abuses or misuses the services, including by creating false or duplicate profiles, infringing intellectual property rights, shipping prohibited or suspicious goods, evading fees, manipulating system workflows, under-declaring weight or value, booking shipments outside permitted use, circumventing controls, refusing to cooperate in an investigation, or engaging in conduct deemed suspicious, fraudulent, harmful, or contrary to the purpose of the services. Repeat violations may result in permanent blacklisting and legal action.</p>

                                    <!-- Governing Law -->
                                    <h2>Governing Law</h2>
                                    <p>This Agreement is governed by the laws of India. Subject to the arbitration clause, the courts of New Delhi shall have exclusive jurisdiction to determine disputes arising out of, under, or in relation to this Agreement. Any dispute shall be settled by arbitration in New Delhi in accordance with the Indian Arbitration and Conciliation Act, 1996, in the English language, by a sole arbitrator appointed by United Courier Services, and the arbitrator's decision shall be final, conclusive, and binding on the parties.</p>

                                    <!-- General Terms -->
                                    <h2>General Terms</h2>
                                    <p>No failure or delay in exercising any right, power, or remedy operates as a waiver unless in writing. If any provision is invalid or unenforceable, the remaining provisions remain in effect, and the parties shall negotiate in good faith to replace the invalid provision with one having the same legal and commercial effect as far as possible. Neither party is created as a partner, joint venturer, fiduciary, employee, or agent of the other, except where the Company acts as a limited collection agent for COD remittance to the extent expressly stated. This Agreement, together with annexures, schedules, rate sheets, dashboard notices, web links, operating procedures, and written addenda, constitutes the entire agreement between the parties on its subject matter.</p>

                                    <!-- Definitions and Interpretation -->
                                    <h2>Definitions and Interpretation</h2>
                                    <h3>Definitions</h3>
                                    <p>For the purposes of this Agreement:</p>
                                    <div class="glossary-item"><span class="glossary-term">"Affiliate"</span><span class="glossary-def">means, in relation to a Party, any entity that directly or indirectly controls, is controlled by, or is under common control with that Party.</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Applicable Law"</span><span class="glossary-def">means all laws, statutes, rules, regulations, notifications, circulars, orders, trade controls, sanctions, customs laws, tax laws, and governmental requirements applicable to a Party, the Services, or the goods.</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Confidential Information"</span><span class="glossary-def">means with respect to each Party, any information or trade secrets, schedules, business plans including, without limitation, commercial information, financial projections, client information, administrative and/or organizational matters of a confidential/secret nature in whatever form which is acquired by, or disclosed to, the other Party pursuant to this Agreement, and includes any tangible or intangible non-public information that is marked or otherwise designated as 'confidential', 'proprietary', 'restricted', or with a similar designation by the disclosing Party at the time of its disclosure to the other Party, or is otherwise reasonably understood to be confidential by the circumstances surrounding its disclosure, but excludes information which: (i) is required to be disclosed in a judicial or administrative proceeding, or is otherwise requested or required to be disclosed pursuant to applicable law or regulation, and (ii) which at the time it is so acquired or disclosed, is already in the public domain or becomes so other than by reason of any breach or non-performance by the other Party of any of the provisions of this Agreement;</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Force Majeure Event"</span><span class="glossary-def">includes act of God, war, civil disturbance, terrorism, strike, lockout, fire, flood, explosion, epidemic, pandemic, transport disruption, carrier failure, cyber disruption, customs restriction, export-import policy change, sanction, or government action beyond reasonable control.</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Intellectual Property"</span><span class="glossary-def">means all patents, copyrights, trademarks, trade names, service marks, logos, domain names, trade secrets, designs, software, databases, data rights, know-how, inventions, and all allied intellectual property rights and goodwill.</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Services"</span><span class="glossary-def">means all domestic and international shipping, carriage facilitation, logistics management, customs support, importer of record services, reverse logistics, COD collection, marketplace support, technology access, and allied services provided or facilitated by the Company.</span></div>
                                    <div class="glossary-item"><span class="glossary-term">"Shipment"</span><span class="glossary-def">means any parcel, package, consignment, goods, document, or item tendered by or on behalf of the Merchant for any Service.</span></div>

                                    <h3>Interpretation</h3>
                                    <p>Unless the context of this Agreement otherwise requires:</p>
                                    <ul>
                                        <li>(a) heading and bold typeface are only for convenience and shall be ignored for the purpose of interpretation;</li>
                                        <li>(b) other terms may be defined elsewhere in the text of this Agreement and, unless otherwise indicated, shall have such meaning throughout this Agreement;</li>
                                        <li>(c) references to this Agreement shall be deemed to include any amendments or modifications to this Agreement, as the case may be;</li>
                                        <li>(d) the terms "hereof", "herein", "hereby", "hereto" and derivative or similar words refer to this entire Agreement or specified Clauses of this Agreement, as the case may be;</li>
                                        <li>(e) references to a particular section, clause, paragraph, sub-paragraph or schedule, exhibit or annexure shall be a reference to that section, clause, paragraph, sub-paragraph or schedule, exhibit or annexure in or to this Agreement;</li>
                                        <li>(f) reference to any legislation or law or to any provision thereof shall include references to any such law as it may, after the date hereof, from time to time, be amended, supplemented or re-enacted, and any reference to statutory provision shall include any subordinate legislation made from time to time under that provision;</li>
                                        <li>(g) a provision of this Agreement must not be interpreted against any Party solely on the ground that the Party was responsible for the preparation of this Agreement or that provision, and the doctrine of contra proferentem does not apply vis-à-vis this Agreement;</li>
                                        <li>(h) references in the singular shall include references in the plural and vice versa; and</li>
                                        <li>(i) references to the word "include" shall be construed without limitation.</li>
                                    </ul>

                                    <!-- Customer Support -->
                                    <h2>Customer Support</h2>
                                    <p>If You have any questions, issues, complaint, or seek any clarity in relations to the Agreement and/or Services/Products, please feel free to contact us at <a href="mailto:Csd@unitedcouriers.biz">Csd@unitedcouriers.biz</a></p>

                                    <!-- Consolidated Agreement -->
                                    <div class="consolidated-note">
                                        <h3 style="margin-top: 0; border-bottom: none; padding-bottom: 0;">Consolidated Agreement</h3>
                                        <p>These Terms and Conditions are intended to be read together with the Consolidated Merchant Agreement, which contains the complete and comprehensive terms governing the relationship between the parties. In the event of any inconsistency, ambiguity, or conflict between these Terms and Conditions and the Consolidated Merchant Agreement, the Consolidated Merchant Agreement shall prevail to the extent of such inconsistency, ambiguity, or conflict.</p>
                                    </div>

                                    <!-- Authorized Signature -->
                                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: flex-end;">
                                        <p class="text-muted small mb-2" style="align-self: flex-end;"><strong>Authorized Signature:</strong></p>
                                        <div style="min-height: 80px; min-width: 220px; max-width: 280px; display: flex; align-items: center; justify-content: center; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px; background: #fff;">
                                            @if($signatureUrl)
                                                <img src="{{ $signatureUrl }}" alt="Customer signature" style="max-height: 90px; max-width: 260px; object-fit: contain;">
                                            @else
                                                <span class="text-muted small">No signature uploaded.</span>
                                            @endif
                                        </div>
                                    </div>

                                    <hr>
                                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap; margin-top: 0.5rem; color: #34537a; font-size: 0.9rem;">
                                        <span>© UNITED WORLDWIDE COURIERS PVT LTD</span>
                                        <span>New Delhi · India</span>
                                    </div>
                                </div>
                            </div>

                            <div class="stepper-nav">
                                <button type="button" class="stepper-nav-btn stepper-prev" data-dir="-1" disabled><i class="ti ti-arrow-left"></i> Previous</button>
                                <button type="button" class="stepper-nav-btn stepper-next" data-dir="1">Next <i class="ti ti-arrow-right"></i></button>
                            </div>

                        </div>
                    </div>
                    @endif
                </div>
                <div class="row profile-details-row">
                    <!-- Left Column: Personal Info -->
                    <div class="col-lg-6 profile-column">
                        <!-- Personal Information -->
                        <div class="detail-card">
                            <h5><i class="ti ti-user me-2"></i>Personal Information</h5>
                            <div class="detail-grid personal-info-grid">
                                <div class="detail-row">
                                    <span class="label">First Name</span>
                                    <span class="value">{{ $customer->first_name ?? '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Last Name</span>
                                    <span class="value">{{ $customer->last_name ?? '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Phone Number</span>
                                    <span class="value">{{ $customer->phone_number ?? '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Alternate Phone</span>
                                    <span class="value">{{ $customer->alternate_phone_number ?? '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">User Type</span>
                                    <span class="value">{{ $userType }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Member Since</span>
                                    <span class="value">{{ $customer->created_at ? $customer->created_at->format('d M Y') : '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Login Credentials -->
                    <div class="col-lg-6 profile-column">
                        <!-- Login Credentials -->
                        <div class="detail-card">
                            <h5><i class="ti ti-lock me-2"></i>Login Credentials</h5>
                            <div class="detail-grid">
                                <div class="detail-row">
                                    <span class="label">Login ID (Email)</span>
                                    <span class="value">
                                        {{ $customer->email ?? '—' }}
                                        <i class="ti ti-copy copy-btn ms-1" style="cursor:pointer;color:#64748b;" onclick="copyText('{{ $customer->email ?? '' }}')" title="Copy"></i>
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Password</span>
                                    <span class="value">
                                        <span class="text-muted">•••••••• (hidden for security)</span>
                                        <button type="button" class="action-btn btn-reset-pwd ms-2" style="padding:4px 12px;font-size:12px;" onclick="openResetPasswordModal({{ $customer->id }}, '{{ addslashes($customer->first_name . ' ' . $customer->last_name) }}')">
                                            <i class="ti ti-key"></i> Reset
                                        </button>
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Terms Accepted</span>
                                    <span class="value">
                                        @if($customer->is_terms_accepted)
                                            <span class="status-verified"><i class="ti ti-circle-check"></i> Accepted</span>
                                        @else
                                            <span class="status-pending"><i class="ti ti-clock"></i> Not Accepted</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Account Status</span>
                                    <span class="value">
                                        @if($customer->status)
                                            <span class="badge-status badge-active"><i class="ti ti-circle-check"></i> Active</span>
                                        @else
                                            <span class="badge-status badge-inactive"><i class="ti ti-circle-x"></i> Deactivated</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Content -->

        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Wrapper -->

    <!-- Document Preview Modal -->
    <div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentPreviewTitle"><i class="ti ti-file-search me-2"></i>Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="height:min(78vh, 900px);min-height:420px;">
                    <iframe id="documentPreviewFrame" title="Document preview" loading="lazy" style="width:100%;height:100%;border:0;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="resetPasswordForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="customer_id" id="resetCustomerId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-key me-2"></i>Reset Customer Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">Set a new password for <strong id="resetCustomerName">this customer</strong>.</p>
                        <div class="mb-3">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="resetPassword" required minlength="6">
                            <small class="text-muted">Minimum 6 characters.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" id="resetPasswordConfirmation" required minlength="6">
                            <div class="invalid-feedback d-none" id="passwordMismatchError"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning"><i class="ti ti-check me-1"></i>Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recharge Wallet Modal -->
    <div class="modal fade" id="rechargeWalletModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-wallet me-2"></i>Recharge Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rechargeWalletForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">Recharge wallet for: <strong id="rw-customer-name">—</strong></p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mode of Payment</label>
                                <select class="form-select" id="rechargeMode" name="mode">
                                    <option value="credit" selected>Credit</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (₹)</label>
                                <input type="number" class="form-control" id="rechargeAmount" name="amount" min="1" step="0.01" placeholder="Enter amount" required>
                            </div>
                        </div>
                        <div class="alert alert-info py-2 small mb-0 d-none" id="rw-result"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="rw-submit-btn"><i class="ti ti-plus me-1"></i>Recharge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject KYC Modal -->
    <div class="modal fade" id="rejectKycModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-alert-triangle me-2 text-danger"></i>Reject KYC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Are you sure you want to reject KYC for <strong>{{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}</strong>?
                    </p>
                    <div class="mb-2">
                        <label for="rejectRemarkInput" class="form-label">Remark <span class="text-danger">*</span></label>
                        <textarea id="rejectRemarkInput" class="form-control" rows="3" maxlength="1000" required
                            placeholder="Enter the reason for rejection..."></textarea>
                        <div class="text-danger small mt-1 d-none" id="rejectRemarkError">Please enter a remark before rejecting.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRejectBtn">
                        <i class="ti ti-circle-x me-1"></i>Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script>
        var rechargeCustomerId = null;
        var rechargeUrlTemplate = '{{ route("admin.customer.recharge-wallet", ["id" => "__ID__"]) }}';
        var documentPreviewElement = document.getElementById('documentPreviewModal');
        var documentPreviewFrame = document.getElementById('documentPreviewFrame');
        var documentPreviewTitle = document.getElementById('documentPreviewTitle');
        var documentPreviewModal = bootstrap.Modal.getOrCreateInstance(documentPreviewElement);

        document.querySelectorAll('.document-preview-link').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();

                var labelElement = link.closest('.detail-row') ? link.closest('.detail-row').querySelector('.label') : null;
                documentPreviewTitle.textContent = link.dataset.docLabel || (labelElement ? labelElement.textContent.trim() : 'Document Preview');
                documentPreviewFrame.src = link.href;
                documentPreviewModal.show();
            });
        });

        documentPreviewElement.addEventListener('hidden.bs.modal', function() {
            documentPreviewFrame.removeAttribute('src');
            documentPreviewTitle.textContent = 'Document Preview';
        });

        function copyText(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    showToast('Copied: ' + text);
                }).catch(function() { fallbackCopy(text); });
            } else { fallbackCopy(text); }
        }
        function fallbackCopy(text) {
            var ta = document.createElement('textarea');
            ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy'); showToast('Copied: ' + text); }
            catch(e) { showToast('Unable to copy.'); }
            document.body.removeChild(ta);
        }
        function showToast(msg) {
            var t = document.createElement('div');
            t.textContent = msg;
            t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#1d4ed8;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;font-weight:600;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,0.2);opacity:0;transition:opacity 0.3s;';
            document.body.appendChild(t);
            requestAnimationFrame(function() { t.style.opacity = '1'; });
            setTimeout(function() { t.style.opacity = '0'; setTimeout(function() { document.body.removeChild(t); }, 300); }, 2500);
        }

        function openResetPasswordModal(customerId, customerName) {
            document.getElementById('resetCustomerId').value = customerId;
            document.getElementById('resetCustomerName').textContent = customerName || 'this customer';
            document.getElementById('resetPassword').value = '';
            document.getElementById('resetPasswordConfirmation').value = '';
            document.getElementById('passwordMismatchError').classList.add('d-none');
            document.getElementById('resetPasswordForm').action = '{{ route("admin.customer.reset-password", ":id") }}'.replace(':id', customerId);
            new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
        }

        function openRechargeModal(customerId, customerName) {
            rechargeCustomerId = customerId;
            document.getElementById('rw-customer-name').textContent = customerName || '—';
            document.getElementById('rechargeAmount').value = '';
            document.getElementById('rw-result').classList.add('d-none');
            document.getElementById('rw-submit-btn').disabled = false;
            document.getElementById('rw-submit-btn').innerHTML = '<i class="ti ti-plus me-1"></i>Recharge';
            new bootstrap.Modal(document.getElementById('rechargeWalletModal')).show();
        }

        // Reset password validation
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            var pwd = document.getElementById('resetPassword').value;
            var confirm = document.getElementById('resetPasswordConfirmation').value;
            var errEl = document.getElementById('passwordMismatchError');
            if (pwd.length < 6) {
                e.preventDefault();
                errEl.textContent = 'Password must be at least 6 characters.';
                errEl.classList.remove('d-none');
                return;
            }
            if (pwd !== confirm) {
                e.preventDefault();
                errEl.textContent = 'Passwords do not match.';
                errEl.classList.remove('d-none');
                return;
            }
            errEl.classList.add('d-none');
        });

        // Recharge wallet AJAX
        document.getElementById('rechargeWalletForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var amount = document.getElementById('rechargeAmount').val;
            var resultDiv = document.getElementById('rw-result');
            var submitBtn = document.getElementById('rw-submit-btn');
            var amountVal = document.getElementById('rechargeAmount').value;

            if (!amountVal || parseFloat(amountVal) < 1) {
                resultDiv.className = 'alert alert-danger py-2 small mb-0';
                resultDiv.textContent = 'Please enter a valid amount (minimum ₹1).';
                resultDiv.classList.remove('d-none');
                return;
            }
            if (!rechargeCustomerId) {
                resultDiv.className = 'alert alert-danger py-2 small mb-0';
                resultDiv.textContent = 'Customer ID not found.';
                resultDiv.classList.remove('d-none');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
            resultDiv.classList.add('d-none');

            var url = rechargeUrlTemplate.replace('__ID__', rechargeCustomerId);
            var token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';

            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: JSON.stringify({ amount: amountVal, mode: document.getElementById('rechargeMode').value })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var html = '<i class="ti ti-circle-check me-1"></i>' + data.message;
                    if (data.new_balance !== undefined) html += '<br><strong>New Balance: ₹' + parseFloat(data.new_balance).toFixed(2) + '</strong>';
                    resultDiv.className = 'alert alert-success py-2 small mb-0';
                    resultDiv.innerHTML = html;
                    resultDiv.classList.remove('d-none');
                    submitBtn.innerHTML = '<i class="ti ti-check me-1"></i>Done';
                    document.getElementById('rechargeAmount').value = '';
                } else {
                    resultDiv.className = 'alert alert-danger py-2 small mb-0';
                    resultDiv.textContent = data.message || 'An error occurred.';
                    resultDiv.classList.remove('d-none');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="ti ti-plus me-1"></i>Recharge';
                }
            })
            .catch(function(err) {
                resultDiv.className = 'alert alert-danger py-2 small mb-0';
                resultDiv.textContent = 'Network error: ' + err.message;
                resultDiv.classList.remove('d-none');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-plus me-1"></i>Recharge';
            });
        });

        // Reject KYC with required remark
        @if($personalKyc && in_array($personalKyc->kyc_status ?? 'pending', ['pending', 'under_review']))
            var rejectKycUrl = '{{ route("admin.kyc-pending.reject", $personalKyc->id) }}';
        @else
            var rejectKycUrl = '';
        @endif

        function openRejectKycModal() {
            document.getElementById('rejectRemarkInput').value = '';
            document.getElementById('rejectRemarkInput').classList.remove('is-invalid');
            document.getElementById('rejectRemarkError').classList.add('d-none');
            new bootstrap.Modal(document.getElementById('rejectKycModal')).show();
        }

        document.getElementById('confirmRejectBtn').addEventListener('click', function() {
            if (!rejectKycUrl) return;
            var remark = document.getElementById('rejectRemarkInput').value.trim();
            if (!remark) {
                document.getElementById('rejectRemarkInput').classList.add('is-invalid');
                document.getElementById('rejectRemarkError').classList.remove('d-none');
                return;
            }
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = rejectKycUrl;
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            var remarkInput = document.createElement('input');
            remarkInput.type = 'hidden';
            remarkInput.name = 'reject_remark';
            remarkInput.value = remark;
            form.appendChild(csrf);
            form.appendChild(remarkInput);
            document.body.appendChild(form);
            bootstrap.Modal.getInstance(document.getElementById('rejectKycModal')).hide();
            form.submit();
        });

        // KYC stepper (same UI as the KYC form) - read-only navigation
        function showStepperStep(card, step) {
            var items = card.querySelectorAll('.step-item');
            var panels = card.querySelectorAll('.step-content');
            var maxStep = items.length;
            if (step < 1) step = 1;
            if (step > maxStep) step = maxStep;
            items.forEach(function(item, idx) {
                var n = idx + 1;
                item.classList.toggle('active', n === step);
                item.classList.toggle('completed', n < step);
            });
            panels.forEach(function(panel) {
                panel.classList.toggle('active', panel.id === 'step' + step + '-content');
            });
            var prevBtn = card.querySelector('.stepper-prev');
            var nextBtn = card.querySelector('.stepper-next');
            if (prevBtn) prevBtn.disabled = step <= 1;
            if (nextBtn) nextBtn.disabled = step >= maxStep;
        }

        var kycStepperCard = document.querySelector('.admin-kyc-view');
        if (kycStepperCard) {
            kycStepperCard.querySelectorAll('.step-item').forEach(function(item, idx) {
                item.addEventListener('click', function() {
                    showStepperStep(kycStepperCard, idx + 1);
                });
            });
            kycStepperCard.querySelectorAll('.stepper-nav-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var activeIdx = Array.prototype.findIndex.call(kycStepperCard.querySelectorAll('.step-item'), function(i) {
                        return i.classList.contains('active');
                    });
                    var dir = parseInt(btn.dataset.dir, 10);
                    var target = activeIdx + 1 + dir;
                    var items = kycStepperCard.querySelectorAll('.step-item');
                    var maxStep = items.length;
                    while (target >= 1 && target <= maxStep && items[target - 1].dataset.skipped === '1') {
                        target += dir;
                    }
                    if (target >= 1 && target <= maxStep) {
                        showStepperStep(kycStepperCard, target);
                    }
                });
            });
        }
    </script>

</body>
</html>
