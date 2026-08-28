<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>KYC Summary | United Courier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/plugins/tabler-icons/tabler-icons.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .card {
            background: #fff;
            border-radius: 20px;
        }

        .summary-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1) !important;
        }

        .summary-card .summary-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .summary-card .summary-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .summary-card .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #212529;
        }

        .profile-header-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
        }

        .profile-cover {
            height: 120px;
            background: linear-gradient(270deg, #2563eb, #9333ea, #2563eb);
            position: relative;
        }

        .profile-avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #eef2ff;
            color: #2f66f3;
            font-size: 38px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: absolute;
            bottom: -48px;
            left: 32px;
        }

        .detail-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
        }

        .detail-card .card-header {
            background: #f8f9fc;
            border-bottom: 1px solid #eef0f5;
            padding: 16px 20px;
            font-weight: 600;
            color: #243b63;
        }

        .detail-card .card-header i {
            color: #2f66f3;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f3f7;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 13px;
            color: #6c757d;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-label i {
            font-size: 16px;
            color: #8a94a6;
        }

        .detail-value {
            font-size: 14px;
            color: #212529;
            font-weight: 600;
            text-align: right;
            word-break: break-word;
        }

        .badge-status {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 50px;
            font-weight: 600;
        }

        .doc-link {
            color: #2f66f3;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .doc-link:hover {
            text-decoration: underline;
        }

        .kyc-action-btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 20px;
            border: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .kyc-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .btn-personal {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
        }

        .btn-business {
            background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);
            color: #fff;
        }

        /* Secondary (non-primary) KYC button — outlined, less prominent */
        .btn-kyc-secondary {
            background: #fff !important;
            color: #475569 !important;
            border: 1.5px solid #cbd5e1;
        }

        .btn-kyc-secondary.btn-personal {
            border-color: #2563eb;
            color: #2563eb !important;
        }

        .btn-kyc-secondary.btn-business {
            border-color: #9333ea;
            color: #9333ea !important;
        }

        .btn-kyc-secondary:hover {
            background: #f8fafc !important;
        }

        /* Secondary (non-primary) KYC detail card — slightly faded */
        .kyc-card-secondary .detail-card {
            opacity: 0.92;
            border: 1px dashed #cbd5e1;
        }

        .kyc-card-secondary .card-header {
            background: #f8fafc;
        }

        .empty-state {
            text-align: center;
            padding: 32px 16px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 40px;
            margin-bottom: 12px;
            display: block;
        }
    </style>
</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar Start -->
        @include('customer.partials.customer_dashboard_header')
        <!-- Topbar End -->

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
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">KYC Summary</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ url('/customer/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">KYC Summary</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ route('customer.my-profile') }}" class="btn btn-light d-flex align-items-center">
                            <i class="ti ti-user me-1"></i> My Profile
                        </a>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <div id="alertContainer"></div>

                @php
                    // Determine KYC statuses
                    $personalStatus = 'Not Submitted';
                    $personalStatusClass = 'bg-secondary';
                    if ($personalKyc) {
                        $personalStatus = ucfirst($personalKyc->kyc_status ?? 'pending');
                        $personalStatusClass = match($personalKyc->kyc_status) {
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'under_review' => 'bg-info',
                            default => 'bg-warning',
                        };
                    }

                    $businessStatus = 'Not Submitted';
                    $businessStatusClass = 'bg-secondary';
                    if ($businessKyc) {
                        $businessStatus = ucfirst($businessKyc->kyc_status ?? 'pending');
                        $businessStatusClass = match($businessKyc->kyc_status) {
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'under_review' => 'bg-info',
                            default => 'bg-warning',
                        };
                    }

                    // CSB type label
                    $csbTypeLabel = 'None';
                    if ($customer->csb_status == 1) {
                        $csbTypeLabel = 'CSB-IV (Personal)';
                    } elseif ($customer->csb_status == 2) {
                        $csbTypeLabel = 'CSB-V (Business)';
                    }

                    // Helper to mask Aadhaar
                    $maskAadhar = function ($number) {
                        if (!$number || strlen($number) < 4) return 'N/A';
                        return 'XXXX XXXX ' . substr($number, -4);
                    };

                    // Helper to mask PAN
                    $maskPan = function ($number) {
                        if (!$number) return 'N/A';
                        return $number;
                    };

                    // Helper to build document link
                    $docLink = function ($path, $label) {
                        if (!$path) return '<span class="text-muted">Not Uploaded</span>';
                        $fullPath = asset('uploads/' . ltrim($path, '/'));
                        return '<a href="' . $fullPath . '" target="_blank" class="doc-link"><i class="ti ti-external-link me-1"></i>' . $label . '</a>';
                    };

                    // A signed agreement can only be generated when both the
                    // customer signature and the accepted merchant agreement exist.
                    $hasSignedPersonalAgreement = !empty($personalKyc?->signature_document ?: $personalKyc?->signature)
                        && !empty($personalKyc?->merchant_agreement);
                    $hasSignedBusinessAgreement = !empty($businessKyc?->signature_document)
                        && !empty($businessKyc?->merchant_agreement);
                @endphp

                <!-- Profile Header Card -->
                <div class="card profile-header-card shadow-sm mb-4">
                    <div class="profile-cover">
                        <div class="profile-avatar">
                            {{ strtoupper(substr($customer->first_name ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                    <div class="card-body pt-5 pb-4 px-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                            <div class="ps-0 ps-sm-5">
                                <h4 class="mb-1 fw-bold">
                                    {{ trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) ?: 'Customer' }}
                                </h4>
                                <p class="text-muted mb-2">
                                    <i class="ti ti-mail me-1"></i>{{ $customer->email ?? 'N/A' }}
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge {{ $personalStatusClass }} badge-status">
                                        <i class="ti ti-user me-1"></i>Personal KYC: {{ $personalStatus }}
                                    </span>
                                    <span class="badge {{ $businessStatusClass }} badge-status">
                                        <i class="ti ti-building me-1"></i>Business KYC: {{ $businessStatus }}
                                    </span>
                                    <span class="badge bg-primary badge-status">
                                        <i class="ti ti-file-text me-1"></i>{{ $csbTypeLabel }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <p class="text-muted mb-0" style="font-size:12px;">Customer ID</p>
                                <p class="fw-bold mb-0">#{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Personal KYC Status -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#dbeafe;color:#2563eb;">
                                    <i class="ti ti-user-check"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Personal KYC</div>
                                    <div class="summary-value" style="font-size:16px;">{{ $personalStatus }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Business KYC Status -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#ede9fe;color:#7c3aed;">
                                    <i class="ti ti-building-store"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Business KYC</div>
                                    <div class="summary-value" style="font-size:16px;">{{ $businessStatus }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CSB Type -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#dcfce7;color:#16a34a;">
                                    <i class="ti ti-file-text"></i>
                                </div>
                                <div>
                                    <div class="summary-label">CSB Type</div>
                                    <div class="summary-value" style="font-size:15px;">{{ $csbTypeLabel }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Since -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#fef3c7;color:#d97706;">
                                    <i class="ti ti-calendar-time"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Member Since</div>
                                    <div class="summary-value" style="font-size:15px;">{{ $customer->created_at ? $customer->created_at->format('d M Y') : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1 fw-bold">Complete Your KYC</h5>
                                <p class="text-muted mb-0" style="font-size:13px;">
                                    Choose a KYC type to start or update your verification.
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @if($userType === 'Personal')
                                    <a href="{{ route('customer.kyc.personal') }}" class="btn kyc-action-btn btn-personal">
                                        <i class="ti ti-user me-1"></i>
                                        {{ $personalKyc ? 'Edit Personal KYC' : 'Start Personal KYC (CSB-IV)' }}
                                    </a>
                                    <a href="{{ route('customer.csb5-form') }}" class="btn kyc-action-btn btn-business btn-kyc-secondary">
                                        <i class="ti ti-building me-1"></i>
                                        {{ $businessKyc ? 'Edit Business KYC' : 'Start Business KYC (CSB-V)' }}
                                    </a>
                                @else
                                    <a href="{{ route('customer.csb5-form') }}" class="btn kyc-action-btn btn-business">
                                        <i class="ti ti-building me-1"></i>
                                        {{ $businessKyc ? 'Edit Business KYC' : 'Start Business KYC (CSB-V)' }}
                                    </a>
                                    <a href="{{ route('customer.kyc.personal') }}" class="btn kyc-action-btn btn-personal btn-kyc-secondary">
                                        <i class="ti ti-user me-1"></i>
                                        {{ $personalKyc ? 'Edit Personal KYC' : 'Start Personal KYC (CSB-IV)' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Cards Row -->
                <div class="row g-3 mb-4">
                    <!-- Personal KYC Details -->
                    <div class="col-12 col-lg-6 {{ $userType === 'Personal' ? 'order-first' : 'order-last kyc-card-secondary' }}">
                        <div class="card detail-card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-user-check"></i>
                                    <span>Personal KYC Details (CSB-IV)</span>
                                </div>
                                <span class="badge {{ $personalStatusClass }} badge-status">{{ $personalStatus }}</span>
                            </div>
                            <div class="card-body p-4">
                                @if($personalKyc)
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-id-badge-2"></i>Aadhaar Number</span>
                                        <span class="detail-value">{{ $maskAadhar($personalKyc->aadhar_number) }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-shield-check"></i>Aadhaar Verified</span>
                                        <span class="detail-value">
                                            @if($personalKyc->aadhar_verified)
                                                <span class="badge bg-success badge-status"><i class="ti ti-circle-check me-1"></i>Verified</span>
                                            @else
                                                <span class="badge bg-warning badge-status"><i class="ti ti-clock me-1"></i>Pending</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-credit-card"></i>Aadhaar Front</span>
                                        <span class="detail-value">{!! $docLink($personalKyc->aadhar_front_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-credit-card"></i>Aadhaar Back</span>
                                        <span class="detail-value">{!! $docLink($personalKyc->aadhar_back_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-map-pin"></i>Aadhaar Address</span>
                                        <span class="detail-value" style="max-width:60%;">{{ $personalKyc->aadhar_address ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-text"></i>PAN Number</span>
                                        <span class="detail-value">{{ $maskPan($personalKyc->pan_number) }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-user"></i>PAN Holder Name</span>
                                        <span class="detail-value">{{ $personalKyc->pan_holder_name ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-calendar"></i>PAN Date of Birth</span>
                                        <span class="detail-value">{{ $personalKyc->pan_dob ? \Carbon\Carbon::parse($personalKyc->pan_dob)->format('d M Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-shield-check"></i>PAN Verified</span>
                                        <span class="detail-value">
                                            @if($personalKyc->pan_verified)
                                                <span class="badge bg-success badge-status"><i class="ti ti-circle-check me-1"></i>Verified</span>
                                            @else
                                                <span class="badge bg-warning badge-status"><i class="ti ti-clock me-1"></i>Pending</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-upload"></i>PAN Document</span>
                                        <span class="detail-value">{!! $docLink($personalKyc->pan_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-signature"></i>Signature</span>
                                        <span class="detail-value">{!! $docLink($personalKyc->signature_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-map-pin"></i>Billing Address</span>
                                        <span class="detail-value" style="max-width:60%;">{{ $personalKyc->billing_address ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-phone"></i>Billing Contact</span>
                                        <span class="detail-value">{{ $personalKyc->billing_contact ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-mail"></i>Billing Email</span>
                                        <span class="detail-value">{{ $personalKyc->billing_email ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-upload"></i>Merchant Agreement</span>
                                        <span class="detail-value">
                                            {!! $docLink($personalKyc->merchant_agreement, 'View') !!}
                                            @if($hasSignedPersonalAgreement)
                                                <a href="{{ route('customer.kyc.agreement.download') }}" class="btn btn-sm btn-success ms-2">
                                                    <i class="ti ti-download me-1"></i>Download Signed Agreement (PDF)
                                                </a>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-checks"></i>Terms Accepted</span>
                                        <span class="detail-value">
                                            @if($personalKyc->terms_accepted)
                                                <span class="text-success" style="font-size:13px;"><i class="ti ti-circle-check"></i> Yes</span>
                                            @else
                                                <span class="text-muted" style="font-size:13px;"><i class="ti ti-circle-x"></i> No</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-calendar-plus"></i>Submitted On</span>
                                        <span class="detail-value">{{ $personalKyc->created_at ? $personalKyc->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="ti ti-user-x"></i>
                                        <p class="mb-0">Personal KYC has not been submitted yet.</p>
                                        <a href="{{ route('customer.kyc.personal') }}" class="btn kyc-action-btn btn-personal mt-3">
                                            <i class="ti ti-plus me-1"></i> Start Personal KYC
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Business KYC Details -->
                    <div class="col-12 col-lg-6 {{ $userType === 'Business' ? 'order-first' : 'order-last kyc-card-secondary' }}">
                        <div class="card detail-card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-building-store"></i>
                                    <span>Business KYC Details (CSB-V)</span>
                                </div>
                                <span class="badge {{ $businessStatusClass }} badge-status">{{ $businessStatus }}</span>
                            </div>
                            <div class="card-body p-4">
                                @if($businessKyc)
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-receipt-tax"></i>GST Certificate Number</span>
                                        <span class="detail-value">{{ strtoupper($businessKyc->gst_certificate_number ?: 'N/A') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-upload"></i>GST Certificate</span>
                                        <span class="detail-value">{!! $docLink($businessKyc->gst_certificate_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-upload"></i>GST Document</span>
                                        <span class="detail-value">{!! $docLink($businessKyc->gst_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-text"></i>IEC Number</span>
                                        <span class="detail-value">{{ $businessKyc->iec_number ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-upload"></i>IEC Document</span>
                                        <span class="detail-value">{!! $docLink($businessKyc->iec_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-text"></i>AD Code</span>
                                        <span class="detail-value">{{ $businessKyc->ad_code ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-upload"></i>AD Code Document</span>
                                        <span class="detail-value">{!! $docLink($businessKyc->ad_code_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-calendar"></i>LUT Expiry Date</span>
                                        <span class="detail-value">{{ $businessKyc->lut_expiry_date ? \Carbon\Carbon::parse($businessKyc->lut_expiry_date)->format('d M Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-calendar-day"></i>LUT Bond Year</span>
                                        <span class="detail-value">{{ $businessKyc->lut_bond_year ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-upload"></i>LUT Document</span>
                                        <span class="detail-value">{!! $docLink($businessKyc->lut_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-shield-check"></i>LUT Verified</span>
                                        <span class="detail-value">
                                            @if($businessKyc->lut_verified)
                                                <span class="badge bg-success badge-status"><i class="ti ti-circle-check me-1"></i>Verified</span>
                                            @else
                                                <span class="badge bg-warning badge-status"><i class="ti ti-clock me-1"></i>Pending</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-university"></i>Bank Account Number</span>
                                        <span class="detail-value">{{ $businessKyc->bank_account_number ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-landmark"></i>Bank Type</span>
                                        <span class="detail-value">{{ ucfirst($businessKyc->bank_type ?: 'N/A') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-id-badge-2"></i>Aadhaar Number</span>
                                        <span class="detail-value">{{ $maskAadhar($businessKyc->aadhar_number) }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-upload"></i>Aadhaar Document</span>
                                        <span class="detail-value">{!! $docLink($businessKyc->aadhar_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-signature"></i>Authorized Signature</span>
                                        <span class="detail-value">{!! $docLink($businessKyc->signature_document, 'View') !!}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-map-pin"></i>Billing Address</span>
                                        <span class="detail-value" style="max-width:60%;">{{ $businessKyc->billing_address ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-receipt"></i>Billing GST</span>
                                        <span class="detail-value">{{ strtoupper($businessKyc->billing_gst ?: 'N/A') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-phone"></i>Billing Contact</span>
                                        <span class="detail-value">{{ $businessKyc->billing_contact ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-mail"></i>Billing Email</span>
                                        <span class="detail-value">{{ $businessKyc->billing_email ?: 'N/A' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-file-upload"></i>Merchant Agreement</span>
                                        <span class="detail-value">
                                            {!! $docLink($businessKyc->merchant_agreement, 'View') !!}
                                            @if($hasSignedBusinessAgreement)
                                                <a href="{{ route('customer.kyc.agreement.download') }}" class="btn btn-sm btn-success ms-2">
                                                    <i class="ti ti-download me-1"></i>Download Signed Agreement (PDF)
                                                </a>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-checks"></i>Terms Accepted</span>
                                        <span class="detail-value">
                                            @if($businessKyc->terms_accepted)
                                                <span class="text-success" style="font-size:13px;"><i class="ti ti-circle-check"></i> Yes</span>
                                            @else
                                                <span class="text-muted" style="font-size:13px;"><i class="ti ti-circle-x"></i> No</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label"><i class="ti ti-calendar-plus"></i>Submitted On</span>
                                        <span class="detail-value">{{ $businessKyc->created_at ? $businessKyc->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="ti ti-building-x"></i>
                                        <p class="mb-0">Business KYC has not been submitted yet.</p>
                                        <a href="{{ route('customer.csb5-form') }}" class="btn kyc-action-btn btn-business mt-3">
                                            <i class="ti ti-plus me-1"></i> Start Business KYC
                                        </a>
                                    </div>
                                @endif
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

    <!-- JAVASCRIPT FILES -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Auto-dismiss any alert messages after 5 seconds
            setTimeout(function () {
                $('#alertContainer .alert').fadeOut('slow');
            }, 5000);
        });
    </script>

</body>

</html>
