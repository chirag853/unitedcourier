<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>My Profile | United Courier</title>
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

        .btn-light {
            background: #f5f6f8;
            border: none;
            color: #243b63;
            font-weight: 500;
        }

        .btn-primary {
            background: #2f66f3;
            border: none;
            font-weight: 500;
        }

        .rounded-pill {
            border-radius: 50px !important;
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
            font-size: 22px;
            font-weight: 700;
            color: #212529;
        }

        .wallet-balance-box {
            background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%);
            color: #fff;
            border-radius: 16px;
            padding: 20px 24px;
        }

        .wallet-balance-box .wallet-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .wallet-balance-box .wallet-value {
            font-size: 28px;
            font-weight: 700;
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

        .verified-tick {
            color: #16a34a;
            font-size: 14px;
            margin-left: 4px;
        }

        .unverified-cross {
            color: #dc2626;
            font-size: 14px;
            margin-left: 4px;
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
                        <h4 class="mb-1">My Profile</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ url('/customer/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- <div class="gap-2 d-flex align-items-center flex-wrap">
                        <button class="btn btn-light d-flex align-items-center" onclick="window.print()">
                            <i class="ti ti-printer me-1"></i> Print
                        </button>
                    </div> -->
                </div>

                <!-- Success/Error Messages -->
                <div id="alertContainer"></div>

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
                                    <span class="badge {{ $kycStatusInfo['class'] }} badge-status">
                                        <i class="ti ti-shield-check me-1"></i>KYC: {{ $kycStatusInfo['label'] }}
                                    </span>
                                    <span class="badge {{ $csbStatusInfo['class'] }} badge-status">
                                        <i class="ti ti-file-text me-1"></i>CSB: {{ $csbStatusInfo['label'] }}
                                    </span>
                                    @if($customer->email_verified)
                                        <span class="badge bg-success badge-status">
                                            <i class="ti ti-circle-check me-1"></i>Email Verified
                                        </span>
                                    @else
                                        <span class="badge bg-warning badge-status">
                                            <i class="ti ti-alert-circle me-1"></i>Email Not Verified
                                        </span>
                                    @endif
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
                    <!-- KYC Status -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#fef3c7;color:#d97706;">
                                    <i class="ti ti-shield-check"></i>
                                </div>
                                <div>
                                    <div class="summary-label">KYC Status</div>
                                    <div class="summary-value" style="font-size:16px;">{{ $kycStatusInfo['label'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CSB Status -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#ede9fe;color:#7c3aed;">
                                    <i class="ti ti-file-text"></i>
                                </div>
                                <div>
                                    <div class="summary-label">CSB Status</div>
                                    <div class="summary-value" style="font-size:16px;">{{ $csbStatusInfo['label'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Since -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#dcfce7;color:#16a34a;">
                                    <i class="ti ti-calendar-time"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Member Since</div>
                                    <div class="summary-value" style="font-size:15px;">{{ $customer->created_at ? $customer->created_at->format('d M Y') : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Business Category -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#e0f2fe;color:#0284c7;">
                                    <i class="ti ti-building-store"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Business</div>
                                    <div class="summary-value" style="font-size:14px;">{{ $businessCategory ? Str::limit($businessCategory->category_name, 18) : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Cards Row -->
                <div class="row g-3 mb-4">
                    <!-- Personal Information -->
                    <div class="col-12 col-lg-6">
                        <div class="card detail-card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center gap-2">
                                <i class="ti ti-user-circle"></i>
                                <span>Personal Information</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-user"></i>First Name</span>
                                    <span class="detail-value">{{ $customer->first_name ?: 'N/A' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-user"></i>Last Name</span>
                                    <span class="detail-value">{{ $customer->last_name ?: 'N/A' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-mail"></i>Email Address</span>
                                    <span class="detail-value">
                                        {{ $customer->email ?: 'N/A' }}
                                        @if($customer->email_verified)
                                            <i class="ti ti-circle-check verified-tick" title="Verified"></i>
                                        @else
                                            <i class="ti ti-alert-circle unverified-cross" title="Not Verified"></i>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-phone"></i>Phone Number</span>
                                    <span class="detail-value">{{ $customer->phone_number ?: 'N/A' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-device-mobile"></i>Alternate Phone</span>
                                    <span class="detail-value">{{ $customer->alternate_phone_number ?: 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aadhar Details -->
                    <div class="col-12 col-lg-6">
                        <div class="card detail-card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center gap-2">
                                <i class="ti ti-id-badge-2"></i>
                                <span>Aadhar Details</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-credit-card"></i>Aadhar Number</span>
                                    <span class="detail-value">
                                        @if($maskedAadhar)
                                            {{ $maskedAadhar }}
                                        @else
                                            <span class="text-muted">Not Provided</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-database"></i>Source</span>
                                    <span class="detail-value">
                                        @if($aadharSource === 'customer')
                                            Customer Record
                                        @elseif($aadharSource === 'kyc')
                                            KYC Record
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-shield-check"></i>Verification Status</span>
                                    <span class="detail-value">
                                        @if($aadharVerified)
                                            <span class="badge bg-success badge-status"><i class="ti ti-circle-check me-1"></i>Verified</span>
                                        @else
                                            <span class="badge bg-warning badge-status"><i class="ti ti-clock me-1"></i>Pending</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-info-circle"></i>Security Note</span>
                                    <span class="detail-value text-muted" style="font-weight:400; font-size:12px;">
                                        Aadhar number is masked for your privacy. Only last 4 digits are shown.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Business / Organization Information -->
                    <div class="col-12 col-lg-6">
                        <div class="card detail-card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center gap-2">
                                <i class="ti ti-building-store"></i>
                                <span>Business / Organization Details</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-building"></i>Organization Name</span>
                                    <span class="detail-value">{{ ($kyc && $kyc->organization_name) ? $kyc->organization_name : 'N/A' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-user-check"></i>Authorized Signatory</span>
                                    <span class="detail-value">{{ ($kyc && $kyc->authorized_signatory) ? $kyc->authorized_signatory : 'N/A' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-category"></i>Business Category</span>
                                    <span class="detail-value">{{ $businessCategory ? $businessCategory->category_name : 'N/A' }}</span>
                                </div>
                                @if($businessCategory && $businessCategory->description)
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-info-circle"></i>Category Description</span>
                                    <span class="detail-value text-muted" style="font-weight:400; font-size:12px;">{{ $businessCategory->description }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- GST Details -->
                    <div class="col-12 col-lg-6">
                        <div class="card detail-card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center gap-2">
                                <i class="ti ti-receipt-tax"></i>
                                <span>GST Details</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-file-invoice"></i>GST Number</span>
                                    <span class="detail-value">
                                        @if($kyc && $kyc->gst_number)
                                            {{ strtoupper($kyc->gst_number) }}
                                        @else
                                            <span class="text-muted">Not Provided</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-shield-check"></i>GST Verification</span>
                                    <span class="detail-value">
                                        @if($kyc && $kyc->gst_number)
                                            @if($gstVerified)
                                                <span class="badge bg-success badge-status"><i class="ti ti-circle-check me-1"></i>Verified</span>
                                            @else
                                                <span class="badge bg-warning badge-status"><i class="ti ti-clock me-1"></i>Pending</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary badge-status">N/A</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-message-2"></i>OTP Verification</span>
                                    <span class="detail-value">
                                        @if($kyc && $kyc->otp_verified)
                                            <span class="badge bg-success badge-status"><i class="ti ti-circle-check me-1"></i>Verified</span>
                                        @else
                                            <span class="badge bg-secondary badge-status"><i class="ti ti-x me-1"></i>Not Verified</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-calendar-check"></i>Terms Accepted</span>
                                    <span class="detail-value">
                                        @if($kyc && $kyc->terms_accepted)
                                            <span class="text-success" style="font-size:13px;">
                                                <i class="ti ti-circle-check"></i> Yes
                                                @if($kyc->terms_accepted_at)
                                                    <span class="text-muted d-block" style="font-size:11px; font-weight:400;">
                                                        {{ \Carbon\Carbon::parse($kyc->terms_accepted_at)->format('d M Y, h:i A') }}
                                                    </span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size:13px;"><i class="ti ti-circle-x"></i> No</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account & Verification Summary -->
                    <div class="col-12 col-lg-6">
                        <div class="card detail-card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center gap-2">
                                <i class="ti ti-settings"></i>
                                <span>Account & Verification</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-mail-check"></i>Email Verified</span>
                                    <span class="detail-value">
                                        @if($customer->email_verified)
                                            <span class="badge bg-success badge-status"><i class="ti ti-circle-check me-1"></i>Verified</span>
                                        @else
                                            <span class="badge bg-warning badge-status"><i class="ti ti-clock me-1"></i>Pending</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-id-badge-2"></i>Aadhar Verified</span>
                                    <span class="detail-value">
                                        @if($aadharVerified)
                                            <span class="badge bg-success badge-status"><i class="ti ti-circle-check me-1"></i>Verified</span>
                                        @else
                                            <span class="badge bg-warning badge-status"><i class="ti ti-clock me-1"></i>Pending</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-file-text"></i>CSB Status</span>
                                    <span class="detail-value">
                                        <span class="badge {{ $csbStatusInfo['class'] }} badge-status">{{ $csbStatusInfo['label'] }}</span>
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-shield-check"></i>KYC Status</span>
                                    <span class="detail-value">
                                        <span class="badge {{ $kycStatusInfo['class'] }} badge-status">{{ $kycStatusInfo['label'] }}</span>
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-checks"></i>Terms Accepted</span>
                                    <span class="detail-value">
                                        @if($customer->is_terms_accepted)
                                            <span class="text-success" style="font-size:13px;"><i class="ti ti-circle-check"></i> Yes</span>
                                        @else
                                            <span class="text-muted" style="font-size:13px;"><i class="ti ti-circle-x"></i> No</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Meta -->
                    <div class="col-12 col-lg-6">
                        <div class="card detail-card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center gap-2">
                                <i class="ti ti-user-cog"></i>
                                <span>Account Details</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-hash"></i>Customer ID</span>
                                    <span class="detail-value">#{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-calendar-plus"></i>Registration Date</span>
                                    <span class="detail-value">{{ $customer->created_at ? $customer->created_at->format('d M Y') : 'N/A' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-clock"></i>Registration Time</span>
                                    <span class="detail-value">{{ $customer->created_at ? $customer->created_at->format('h:i A') : 'N/A' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="ti ti-refresh"></i>Last Updated</span>
                                    <span class="detail-value">{{ $customer->updated_at ? $customer->updated_at->format('d M Y, h:i A') : 'N/A' }}</span>
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
