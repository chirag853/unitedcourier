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
            padding: 60px 32px 24px 32px;
            margin-bottom: 24px;
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
        .badge-kyc-approved { background: #dcfce7; color: #166534; }
        .badge-kyc-rejected { background: #fee2e2; color: #991b1b; }
        .badge-kyc-under-review { background: #dbeafe; color: #1e40af; }
        .badge-type { background: #e0e7ff; color: #3730a3; }

        .detail-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .detail-card h5 {
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
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

                @if ($message = Session::get('success'))
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
                        @if ($customer->status)
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
                            <button type="button" class="action-btn btn-reset-pwd" onclick="openResetPasswordModal({{ $customer->id }}, '{{ addslashes($customer->first_name . ' ' . $customer->last_name) }}')">
                                <i class="ti ti-key"></i> Reset Password
                            </button>
                            <button type="button" class="action-btn btn-recharge" onclick="openRechargeModal({{ $customer->id }}, '{{ addslashes($customer->first_name . ' ' . $customer->last_name) }}')">
                                <i class="ti ti-wallet"></i> Recharge Wallet
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Personal Info + Login Credentials -->
                    <div class="col-lg-6">
                        <!-- Personal Information -->
                        <div class="detail-card">
                            <h5><i class="ti ti-user me-2"></i>Personal Information</h5>
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
                                <span class="label">Business Category</span>
                                <span class="value">{{ $businessCategory->name ?? '—' }}</span>
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

                        <!-- Login Credentials -->
                        <div class="detail-card">
                            <h5><i class="ti ti-lock me-2"></i>Login Credentials</h5>
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
                                <span class="label">Email Verified</span>
                                <span class="value">
                                    @if($customer->email_verified)
                                        <span class="status-verified"><i class="ti ti-circle-check"></i> Verified</span>
                                    @else
                                        <span class="status-pending"><i class="ti ti-clock"></i> Not Verified</span>
                                    @endif
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

                        <!-- Wallet -->
                        <div class="detail-card">
                            <h5><i class="ti ti-wallet me-2"></i>Wallet Balance</h5>
                            <div class="text-center py-3">
                                <div class="wallet-balance">₹{{ number_format($wallet->balance ?? 0, 2) }}</div>
                                <p class="text-muted mt-2 mb-0">Current wallet balance</p>
                                <button type="button" class="action-btn btn-recharge mt-3" onclick="openRechargeModal({{ $customer->id }}, '{{ addslashes($customer->first_name . ' ' . $customer->last_name) }}')">
                                    <i class="ti ti-plus"></i> Recharge Wallet
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: KYC Details -->
                    <div class="col-lg-6">
                        @if($personalKyc)
                        <!-- Personal KYC Details -->
                        <div class="detail-card">
                            <h5><i class="ti ti-id-badge-2 me-2"></i>Personal KYC Details (CSB-IV)</h5>
                            <div class="detail-row">
                                <span class="label">KYC Status</span>
                                <span class="value">
                                    @php
                                        $ps = $personalKyc->kyc_status ?? 'pending';
                                        $psClass = 'badge-kyc-pending';
                                        if ($ps === 'approved') $psClass = 'badge-kyc-approved';
                                        elseif ($ps === 'rejected') $psClass = 'badge-kyc-rejected';
                                        elseif ($ps === 'under_review') $psClass = 'badge-kyc-under-review';
                                    @endphp
                                    <span class="badge-status {{ $psClass }}">{{ ucfirst(str_replace('_', ' ', $ps)) }}</span>
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="label">GST Number</span>
                                <span class="value">
                                    {{ $personalKyc->gst_number ?? '—' }}
                                    @if($personalKyc->gst_verified)
                                        <span class="status-verified"><i class="ti ti-circle-check"></i></span>
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Aadhar Number</span>
                                <span class="value">
                                    {{ $personalKyc->aadhar_number ?? '—' }}
                                    @if($personalKyc->aadhar_verified)
                                        <span class="status-verified"><i class="ti ti-circle-check"></i> Verified</span>
                                    @else
                                        <span class="status-pending"><i class="ti ti-clock"></i> Pending</span>
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="label">PAN Number</span>
                                <span class="value">
                                    {{ $personalKyc->pan_number ?? '—' }}
                                    @if($personalKyc->pan_verified)
                                        <span class="status-verified"><i class="ti ti-circle-check"></i> Verified</span>
                                    @else
                                        <span class="status-pending"><i class="ti ti-clock"></i> Pending</span>
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="label">PAN Holder Name</span>
                                <span class="value">{{ $personalKyc->pan_holder_name ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">PAN Date of Birth</span>
                                <span class="value">{{ $personalKyc->pan_dob ? $personalKyc->pan_dob->format('d M Y') : '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Organization Name</span>
                                <span class="value">{{ $personalKyc->organization_name ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Authorized Signatory</span>
                                <span class="value">{{ $personalKyc->authorized_signatory ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">OTP Verified</span>
                                <span class="value">
                                    @if($personalKyc->otp_verified)
                                        <span class="status-verified"><i class="ti ti-circle-check"></i> Verified</span>
                                    @else
                                        <span class="status-pending"><i class="ti ti-clock"></i> Pending</span>
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Terms Accepted</span>
                                <span class="value">
                                    @if($personalKyc->terms_accepted)
                                        <span class="status-verified"><i class="ti ti-circle-check"></i> Accepted</span>
                                    @else
                                        <span class="status-pending"><i class="ti ti-clock"></i> Not Accepted</span>
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Submitted On</span>
                                <span class="value">{{ $personalKyc->created_at ? $personalKyc->created_at->format('d M Y, h:i A') : '—' }}</span>
                            </div>

                            <!-- Documents -->
                            <h6 class="mt-3 mb-2"><i class="ti ti-files me-2"></i>Documents</h6>
                            <div class="kyc-doc-grid">
                                @php $docBase = asset('uploads/'); @endphp
                                <div class="kyc-doc-card">
                                    <span class="doc-label">Aadhaar Front</span>
                                    @if($personalKyc->aadhar_front_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($personalKyc->aadhar_front_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else
                                        <span class="doc-empty">Not uploaded</span>
                                    @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">Aadhaar Back</span>
                                    @if($personalKyc->aadhar_back_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($personalKyc->aadhar_back_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else
                                        <span class="doc-empty">Not uploaded</span>
                                    @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">PAN Card</span>
                                    @if($personalKyc->pan_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($personalKyc->pan_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else
                                        <span class="doc-empty">Not uploaded</span>
                                    @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">Signature</span>
                                    @if($personalKyc->signature_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($personalKyc->signature_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else
                                        <span class="doc-empty">Not uploaded</span>
                                    @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">Merchant Agreement</span>
                                    @if($personalKyc->merchant_agreement)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($personalKyc->merchant_agreement, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else
                                        <span class="doc-empty">Not uploaded</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Billing Details -->
                            @if($personalKyc->billing_address || $personalKyc->billing_contact || $personalKyc->billing_email)
                            <h6 class="mt-3 mb-2"><i class="ti ti-receipt me-2"></i>Billing Details</h6>
                            <div class="detail-row">
                                <span class="label">Billing Address</span>
                                <span class="value">{{ $personalKyc->billing_address ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Billing GST</span>
                                <span class="value">{{ $personalKyc->billing_gst ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Billing Contact</span>
                                <span class="value">{{ $personalKyc->billing_contact ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Billing Email</span>
                                <span class="value">{{ $personalKyc->billing_email ?? '—' }}</span>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($businessKyc)
                        <!-- Business KYC Details (CSB-V) -->
                        <div class="detail-card">
                            <h5><i class="ti ti-building me-2"></i>Business KYC Details (CSB-V)</h5>
                            <div class="detail-row">
                                <span class="label">GST Certificate No.</span>
                                <span class="value">{{ $businessKyc->gst_certificate_number ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">IEC Number</span>
                                <span class="value">{{ $businessKyc->iec_number ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">AD Code</span>
                                <span class="value">{{ $businessKyc->ad_code ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">LUT Expiry Date</span>
                                <span class="value">{{ $businessKyc->lut_expiry_date ? $businessKyc->lut_expiry_date->format('d M Y') : '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">LUT Bond Year</span>
                                <span class="value">{{ $businessKyc->lut_bond_year ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Bank Account No.</span>
                                <span class="value">{{ $businessKyc->bank_account_number ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Bank Type</span>
                                <span class="value">{{ ucfirst($businessKyc->bank_type ?? '—') }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Aadhar Number</span>
                                <span class="value">
                                    {{ $businessKyc->aadhar_number ?? '—' }}
                                    @if($businessKyc->aadhar_verified)
                                        <span class="status-verified"><i class="ti ti-circle-check"></i> Verified</span>
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Submitted On</span>
                                <span class="value">{{ $businessKyc->created_at ? $businessKyc->created_at->format('d M Y, h:i A') : '—' }}</span>
                            </div>

                            <!-- Business Documents -->
                            <h6 class="mt-3 mb-2"><i class="ti ti-files me-2"></i>Business Documents</h6>
                            <div class="kyc-doc-grid">
                                @php $docBase = asset('uploads/'); @endphp
                                <div class="kyc-doc-card">
                                    <span class="doc-label">GST Certificate</span>
                                    @if($businessKyc->gst_certificate_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($businessKyc->gst_certificate_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else <span class="doc-empty">Not uploaded</span> @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">IEC Certificate</span>
                                    @if($businessKyc->iec_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($businessKyc->iec_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else <span class="doc-empty">Not uploaded</span> @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">AD Code Document</span>
                                    @if($businessKyc->ad_code_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($businessKyc->ad_code_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else <span class="doc-empty">Not uploaded</span> @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">LUT Document</span>
                                    @if($businessKyc->lut_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($businessKyc->lut_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else <span class="doc-empty">Not uploaded</span> @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">GST Document</span>
                                    @if($businessKyc->gst_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($businessKyc->gst_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else <span class="doc-empty">Not uploaded</span> @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">Aadhaar Document</span>
                                    @if($businessKyc->aadhar_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($businessKyc->aadhar_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else <span class="doc-empty">Not uploaded</span> @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">Authorized Signature</span>
                                    @if($businessKyc->signature_document)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($businessKyc->signature_document, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else <span class="doc-empty">Not uploaded</span> @endif
                                </div>
                                <div class="kyc-doc-card">
                                    <span class="doc-label">Merchant Agreement</span>
                                    @if($businessKyc->merchant_agreement)
                                        <a class="doc-link" href="{{ $docBase . '/' . ltrim($businessKyc->merchant_agreement, '/') }}" target="_blank"><i class="ti ti-external-link"></i> View</a>
                                    @else <span class="doc-empty">Not uploaded</span> @endif
                                </div>
                            </div>

                            <!-- Billing Details -->
                            @if($businessKyc->billing_address || $businessKyc->billing_contact || $businessKyc->billing_email)
                            <h6 class="mt-3 mb-2"><i class="ti ti-receipt me-2"></i>Billing Details</h6>
                            <div class="detail-row">
                                <span class="label">Billing Address</span>
                                <span class="value">{{ $businessKyc->billing_address ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Billing GST</span>
                                <span class="value">{{ $businessKyc->billing_gst ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Billing Contact</span>
                                <span class="value">{{ $businessKyc->billing_contact ?? '—' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Billing Email</span>
                                <span class="value">{{ $businessKyc->billing_email ?? '—' }}</span>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if(!$personalKyc && !$businessKyc)
                        <div class="detail-card text-center py-5">
                            <i class="ti ti-file-off" style="font-size:48px;color:#cbd5e1;"></i>
                            <h5 class="mt-3 text-muted">No KYC Submitted</h5>
                            <p class="text-muted">This customer has not submitted any KYC yet.</p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
            <!-- End Content -->

        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Wrapper -->

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
                        <div class="mb-3">
                            <label class="form-label">Amount (₹)</label>
                            <input type="number" class="form-control" id="rechargeAmount" name="amount" min="1" step="0.01" placeholder="Enter amount" required>
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
                body: JSON.stringify({ amount: amountVal })
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
    </script>

</body>
</html>
