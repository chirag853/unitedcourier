<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Customer | United Courier</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />
    <style>
        .page-wrapper .content{
            padding:1rem;
            background:#f6f8fc;
        }
        .saved-address-item {
            padding-left: .5rem;
            border-left: 2px solid #e9ecef;
        }
        .saved-address-item + .saved-address-item {
            margin-top: .5rem;
        }
        .saved-address-badge {
            vertical-align: middle;
        }

        /* ============================================================
           Exporter customers design system
           (same visual language as admin/all-customer page)
        ============================================================ */
        .customer-profile-card {
            position: relative;
            overflow: hidden;
            border: 0;
            border-radius: 18px;
            background: linear-gradient(120deg, #0f2557 0%, #1d4ed8 55%, #2563eb 100%);
            box-shadow: 0 14px 34px -14px rgba(29, 78, 216, 0.55);
        }
        .customer-profile-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 88% 12%, rgba(255, 255, 255, 0.16), transparent 42%),
                radial-gradient(circle at 8% 95%, rgba(255, 255, 255, 0.08), transparent 48%);
            pointer-events: none;
        }
        .hero-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
        }
        .customer-name {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .customer-sub {
            color: rgba(255, 255, 255, 0.85);
            font-size: 13.5px;
        }
        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 5px 13px;
            border-radius: 999px;
            font-size: 12.5px;
            line-height: 1.4;
        }
        .meta-pill .ti {
            font-size: 15px;
        }
        .meta-pill b {
            font-weight: 600;
        }
        .hero-create-btn {
            background: #fff;
            color: #1d4ed8;
            font-weight: 700;
            border: 0;
            border-radius: 12px;
            padding: 10px 18px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 8px 18px -8px rgba(0, 0, 0, 0.4);
            transition: transform .15s ease, box-shadow .15s ease;
            white-space: nowrap;
        }
        .hero-create-btn:hover {
            color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 12px 22px -8px rgba(0, 0, 0, 0.45);
        }

        .stat-card {
            border: 1px solid #eef2f7;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 10px 24px -18px rgba(16, 24, 40, 0.14);
            padding: 16px 18px;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        .stat-icon.blue   { background: #eff6ff; color: #2563eb; }
        .stat-icon.violet { background: #f5f3ff; color: #7c3aed; }
        .stat-icon.sky    { background: #e0f2fe; color: #0284c7; }
        .stat-icon.green  { background: #ecfdf5; color: #059669; }
        .stat-icon.amber  { background: #fef3c7; color: #d97706; }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        .data-card {
            border: 1px solid #eef2f7;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 10px 24px -18px rgba(16, 24, 40, 0.12);
            overflow: hidden;
        }
        .data-card .nav-tabs-bottom {
            gap: 6px;
        }
        .data-card .nav-tabs-bottom .nav-link {
            border: 0;
            border-radius: 12px;
            font-weight: 600;
            color: #64748b;
            padding: 10px 16px;
            display: inline-flex;
            align-items: center;
        }
        .data-card .nav-tabs-bottom .nav-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .data-card .nav-tabs-bottom .nav-link.active {
            background: #eff6ff;
            color: #1d4ed8;
        }
        .data-card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e9eef5;
            padding: 18px 20px;
        }
        #exporterCustomersTable {
            width: 100%;
            min-width: 1080px;
            max-width: none;
        }
        #exporterCustomersTable thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-weight: 700;
            border-bottom: 1px solid #e9eef5;
            padding: 13px 12px;
            vertical-align: middle;
            white-space: nowrap;
        }
        #exporterCustomersTable thead th .ti {
            font-size: 15px;
            vertical-align: -2px;
        }
        #exporterCustomersTable tbody td {
            padding: 14px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        #exporterCustomersTable tbody tr:last-child td {
            border-bottom: 0;
        }
        #exporterCustomersTable tbody tr:hover td {
            background: #f8fafc;
        }
        .row-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            padding: 0 7px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
        }
        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.4px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px -3px rgba(16, 24, 40, 0.35);
        }
        .user-name {
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
        }
        .user-code {
            font-size: 11.5px;
            color: #64748b;
        }
        .user-contact {
            color: #64748b;
            font-size: 12.5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .user-contact .ti, .user-contact .fas {
            font-size: 13px;
            color: #94a3b8;
        }
        .type-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 11px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .type-chip.business {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }
        .type-chip.personal {
            background: #f5f3ff;
            color: #7c3aed;
            border: 1px solid #ede9fe;
        }
        .type-chip.none {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
        }
        .status-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-pill.csb-v {
            background: #dcfce7;
            color: #15803d;
        }
        .status-pill.csb-v .dot {
            background: #22c55e;
        }
        .status-pill.csb-iv {
            background: #e0f2fe;
            color: #0369a1;
        }
        .status-pill.csb-iv .dot {
            background: #0284c7;
        }
        .mini-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .mini-chip.gst {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .mini-chip.lut {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .mini-chip.na {
            background: #f1f5f9;
            color: #94a3b8;
            border: 1px dashed #cbd5e1;
        }
        .kyc-title {
            font-weight: 600;
            color: #0f172a;
            font-size: 13px;
        }
        .kyc-sub {
            font-size: 11.5px;
            color: #64748b;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }
        .address-box {
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12.5px;
        }
        .address-box .saved-address-item {
            border-left-color: #bfdbfe;
        }
        .btn-view {
            background-color: #f3e8ff;
            color: #6b21a8;
            border: 1px solid #d8b4fe;
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 10px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            white-space: nowrap;
        }
        .btn-view:hover {
            background-color: #6b21a8;
            color: #fff;
            border-color: #6b21a8;
            transform: translateY(-1px);
        }
        .btn-csb5 {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
            border: 1px solid #059669;
            font-size: 12.5px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 10px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            white-space: nowrap;
            box-shadow: 0 6px 14px -6px rgba(5, 150, 105, 0.6);
        }
        .btn-csb5:hover {
            background: #047857;
            border-color: #047857;
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-address {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 10px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            white-space: nowrap;
        }
        .btn-address:hover {
            background-color: #1d4ed8;
            color: #fff;
            border-color: #1d4ed8;
            transform: translateY(-1px);
        }
        /* ---------- View details modal ---------- */
        .view-detail-hero {
            background: linear-gradient(120deg, #0f2557, #2563eb);
            border-radius: 14px;
            color: #fff;
            padding: 16px 18px;
        }
        .view-detail-avatar {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.18);
            border: 2px solid rgba(255, 255, 255, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }
        .view-detail-section {
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 12px;
        }
        .view-detail-section h6 {
            color: #1d4ed8;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .view-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 5px 0;
            font-size: 13px;
            border-bottom: 1px dashed #eef2f7;
        }
        .view-detail-row:last-child {
            border-bottom: none;
        }
        .view-detail-row .label {
            color: #64748b;
            min-width: 130px;
            flex-shrink: 0;
            font-weight: 500;
        }
        .view-detail-row .value {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
            flex: 1;
            word-break: break-word;
        }
        .view-doc-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px 12px;
            font-size: 12.5px;
            font-weight: 600;
            color: #1d4ed8;
            text-decoration: none;
            margin: 0 6px 6px 0;
        }
        .view-doc-link:hover {
            background: #1d4ed8;
            color: #fff;
            border-color: #1d4ed8;
        }
        .empty-state {
            padding: 50px 20px;
            text-align: center;
        }
        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: #f1f5f9;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto;
        }
        .modal-content {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
        }
        .modal-header {
            background: linear-gradient(120deg, #0f2557, #2563eb);
            color: #fff;
            border-bottom: 0;
        }
        .modal-header .modal-title {
            font-weight: 700;
        }
        .modal-header .btn-close {
            filter: invert(1);
        }
        #exporterCustomersTable_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 7px 12px;
            margin-left: 8px;
        }
        #exporterCustomersTable_wrapper .dataTables_length select {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px 10px;
        }
        @media (max-width: 575.98px) {
            .page-wrapper .content { padding: .6rem; }
            .customer-name { font-size: 19px; }
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    @include('customer.partials.customer_dashboard_header')
    @include('customer.partials.sidebar')

    <div class="page-wrapper">
        <div class="content">
            @php
                $totalSaved = $exporterCustomers->count();
                $csbVCount = $exporterCustomers->where('csb_type', 'csb_v')->count();
                $csbIVCount = $totalSaved - $csbVCount;
                $gstCount = $exporterCustomers->where('is_gst', true)->count();
                $lutCount = $exporterCustomers->where('is_lut', true)->count();
            @endphp

            <!-- ============ Hero / gradient header (admin/all-customer style) ============ -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card customer-profile-card">
                        <div class="card-body position-relative" style="z-index:1;">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="hero-avatar"><i class="ti ti-users"></i></span>
                                    <div>
                                        <div class="customer-name">Exporter Customers</div>
                                        <div class="customer-sub mt-1">Add and manage your saved customers — upgrade CSB IV to CSB V in one click</div>
                                    </div>
                                </div>
                                <a href="{{ route('customer.create-shipment') }}" class="hero-create-btn">
                                    <i class="ti ti-package-export"></i>Create Order
                                </a>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <span class="meta-pill"><i class="ti ti-users"></i><b>{{ $totalSaved }}</b>&nbsp;Total</span>
                                <span class="meta-pill"><i class="ti ti-briefcase"></i><b>{{ $csbVCount }}</b>&nbsp;CSB V</span>
                                <span class="meta-pill"><i class="ti ti-user"></i><b>{{ $csbIVCount }}</b>&nbsp;CSB IV</span>
                                <span class="meta-pill"><i class="ti ti-receipt"></i><b>{{ $gstCount }}</b>&nbsp;GST</span>
                                <span class="meta-pill"><i class="ti ti-file-text"></i><b>{{ $lutCount }}</b>&nbsp;LUT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ Stat tiles (admin/all-customer style) ============ -->
            <div class="row g-3 mb-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <span class="stat-icon blue"><i class="ti ti-users"></i></span>
                        <div>
                            <div class="stat-value">{{ $totalSaved }}</div>
                            <div class="stat-label">Total Customers</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <span class="stat-icon green"><i class="ti ti-briefcase"></i></span>
                        <div>
                            <div class="stat-value">{{ $csbVCount }}</div>
                            <div class="stat-label">CSB V Customers</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <span class="stat-icon sky"><i class="ti ti-user"></i></span>
                        <div>
                            <div class="stat-value">{{ $csbIVCount }}</div>
                            <div class="stat-label">CSB IV Customers</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <span class="stat-icon amber"><i class="ti ti-receipt"></i></span>
                        <div>
                            <div class="stat-value">{{ $gstCount + $lutCount }}</div>
                            <div class="stat-label">GST / LUT Enabled</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card data-card">
                <div class="card-header data-card-header">
                    <ul class="nav nav-tabs nav-tabs-bottom border-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ session('success') ? '' : 'active' }}" id="add-customer-tab" data-bs-toggle="tab" data-bs-target="#add-customer-pane" type="button" role="tab" aria-controls="add-customer-pane" aria-selected="{{ session('success') ? 'false' : 'true' }}">
                                <i class="ti ti-user-plus me-1"></i>Add Customer
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ session('success') ? 'active' : '' }}" id="view-customers-tab" data-bs-toggle="tab" data-bs-target="#view-customers-pane" type="button" role="tab" aria-controls="view-customers-pane" aria-selected="{{ session('success') ? 'true' : 'false' }}">
                                <i class="ti ti-users me-1"></i>View All Customers
                                <span class="badge bg-primary ms-1">{{ $exporterCustomers->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade {{ session('success') ? '' : 'show active' }}" id="add-customer-pane" role="tabpanel" aria-labelledby="add-customer-tab" tabindex="0">

                            <!-- Exporter Customer Wizard Custom CSS -->
                            <link rel="stylesheet" href="{{ asset('css/exporter-customers.css') }}?v={{ filemtime(public_path('css/exporter-customers.css')) ?: 1 }}">

                            <div class="form-wrapper">
                                <div class="kyc-card">
                                    <div class="form-header">
                                        <h2>Add <span class="gradient-text">Customer</span></h2>
                                        <p>Register a new customer for your shipping account in a few simple steps.</p>
                                    </div>

                                    <!-- Wizard Progress Bar -->
                                    <div class="wizard-progress">
                                        <div class="wizard-steps">
                                            <div class="wizard-step active" data-step="1">
                                                <span class="step-number">1</span>
                                                <span class="step-label">Details</span>
                                            </div>
                                            <div class="wizard-step" data-step="2">
                                                <span class="step-number">2</span>
                                                <span class="step-label">KYC Document</span>
                                            </div>
                                            <div class="wizard-step" data-step="3">
                                                <span class="step-number">3</span>
                                                <span class="step-label">Basic Info</span>
                                            </div>
                                            <div class="wizard-step" data-step="4">
                                                <span class="step-number">4</span>
                                                <span class="step-label">CSB5 Info</span>
                                            </div>
                                        </div>
                                        <div class="wizard-bar">
                                            <div class="wizard-bar-fill" id="wizardBarFill" style="width: 25%;"></div>
                                        </div>
                                    </div>

                                    <form id="exporterCustomerForm"
                                          action="{{ route('customer.exporter-customers.store') }}"
                                          method="POST" enctype="multipart/form-data" novalidate
                                          data-verify-aadhar-url="{{ route('customer.verify.exporter-customer-aadhar') }}"
                                          data-verify-gst-url="{{ route('customer.verify.gst') }}"
                                          data-verify-pan-url="{{ route('customer.verify.exporter-customer-pan') }}">
                                        @csrf

                                        <!-- ==================== STEP 1: User Type + KYC Type + CSB Type ==================== -->
                                        <div class="wizard-panel active" data-panel="1">
                                            <div class="section-title-alt"><i class="fas fa-sliders"></i> Customer Configuration <span class="step-chip">Step 1 of 4</span></div>
                                            <p class="step-intro">Select the customer type and CSB type. The KYC type is set automatically from the customer type - Aadhar Card for Individual; for Business you can choose between PAN Card and GST (Normal). Selecting CSB V adds an extra step to collect the CSB V details.</p>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="section-label">User Type <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <select name="business_category_id" id="businessCategoryId" class="input-custom select-custom" required>
                                                            <option value="">Select Customer Type</option>
                                                            @foreach($groupedBusinessCategories as $groupName => $categories)
                                                                <optgroup label="{{ $groupName }}">
                                                                    @foreach($categories as $category)
                                                                        <option value="{{ $category->id }}" data-user-type="{{ strtolower($category->user_type ?: $category->parent_group) }}" {{ (string) old('business_category_id') === (string) $category->id ? 'selected' : '' }}>
                                                                            {{ $category->category_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <small class="text-muted">Select the type of customer you are adding.</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">CSB Type <span class="" style="color:gray">(Optional)</span></label>
                                                    <div class="csb-type-box">
                                                        <label class="csb-checkbox">
                                                            <input type="checkbox" id="csbTypeCheck" {{ old('csb_type') === 'csb_v' ? 'checked' : '' }}>
                                                            <span class="csb-checkbox-box"><i class="fas fa-check"></i></span>
                                                            <span class="csb-checkbox-label">CSB V <small>Enable CSB V fields</small></span>
                                                        </label>
                                                        <input type="hidden" name="csb_type" id="csbType" value="{{ old('csb_type', 'csb_iv') }}">
                                                    </div>
                                                    <small class="text-muted" id="csbTypeHint">Personal customers can select CSB IV or CSB V.</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">KYC Type <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <select name="kyc_type" id="kycType" class="input-custom select-custom" required>
                                                            <option value="">Select KYC Type</option>
                                                            <option value="Aadhar Card" {{ old('kyc_type', 'Aadhar Card') === 'Aadhar Card' ? 'selected' : '' }}>Aadhar Card</option>
                                                            <option value="PAN Card" {{ old('kyc_type') === 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                                                            <option value="GST (Normal)" {{ old('kyc_type') === 'GST (Normal)' ? 'selected' : '' }}>GST (Normal)</option>
                                                        </select>
                                                        <i class="fas fa-id-card"></i>
                                                    </div>
                                                    <small class="text-muted">KYC type is set automatically from the customer type - Aadhar Card for Individual; PAN Card or GST (Normal) for Business. For Business, Step 2 always requires PAN + GST verification regardless of the KYC Type chosen here.</small>
                                                </div>
                                            </div>

                                            @php
                                                $savedLutBondYear = old('lut_bond_year', '');
                                                $savedLutStartYear = preg_match('/^(\d{4})-(\d{2})$/', $savedLutBondYear, $savedLutMatches)
                                                    ? $savedLutMatches[1]
                                                    : '';
                                                $savedLutEndYear = '';
                                                if ($savedLutStartYear !== '') {
                                                    $savedLutEndYear = (intdiv((int) $savedLutStartYear, 100) * 100) + (int) $savedLutMatches[2];
                                                    if ($savedLutEndYear <= (int) $savedLutStartYear) {
                                                        $savedLutEndYear += 100;
                                                    }
                                                }
                                                $lutStartYears = range(now()->year, now()->year + 5);
                                            @endphp

                                            <div class="wizard-nav">
                                                <div></div>
                                                <button type="button" class="btn-gradient wizard-next" data-next="2">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                            </div>
                                        </div>

                                        <!-- ==================== STEP 2: KYC Document ==================== -->
                                        <div class="wizard-panel" data-panel="2">
                                            <div class="section-title-alt"><i class="fas fa-shield-halved"></i> KYC Document Verification <span class="step-chip">Step 2 of 4</span></div>
                                            <p class="step-intro">Verify the required KYC documents below. The sections shown depend on the customer type selected in Step 1. Upload the required document images and verify each section.</p>

                                            <!-- Aadhar section (mandatory for Individual customers, optional for Business customers) -->
                                            <div id="aadharKycSection">
                                                <div class="sub-section-header">
                                                    <div class="sub-section-icon"><i class="fas fa-id-card"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">Aadhaar Verification</h6>
                                                        <!-- <small class="sub-section-desc">Verify Aadhaar details through Cashfree OCR</small> -->
                                                    </div>
                                                    <span class="badge-sub badge-kyc-optional" id="aadharRequirementBadge" style="display: none;">Optional</span>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">Aadhaar Number</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom" id="aadharNumber" placeholder="Enter 12-digit Aadhaar Number *" name="aadhar_number" maxlength="12" inputmode="numeric" value="{{ old('aadhar_number') }}" required>
                                                            <i class="fas fa-id-card"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- <div class="sub-section-header mt-4">
                                                    <div class="sub-section-icon"><i class="fas fa-images"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">Aadhaar Card Documents</h6>
                                                        <small class="sub-section-desc">Upload clear, JPG / PNG photos of the front and back of your Aadhaar card</small>
                                                    </div>
                                                    <span class="badge-sub">JPG / PNG</span>
                                                </div> -->
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">Aadhaar Front</label>
                                                        <div class="doc-item compact" id="aadharFrontDocContainer">
                                                            <div class="doc-meta">
                                                                <div class="doc-file-icon"><i class="fas fa-id-card"></i></div>
                                                                <div>
                                                                    <span class="doc-name">Aadhaar Front</span>
                                                                    <div id="aadharFrontFileInfo" class="file-status">Selected: <span id="aadharFrontFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="aadharFrontFileInput" name="aadhar_front_document" style="display: none;" accept=".jpg,.jpeg,.png" required
                                                                    onchange="handleDocSelect(this, 'aadharFrontFileNameDisplay', 'aadharFrontFileInfo', 'aadharFrontRemoveFile', '.aadharFrontUploadBtn', '#aadharFrontDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent aadharFrontUploadBtn" onclick="document.getElementById('aadharFrontFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt aadharFrontRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('aadharFrontFileInput', 'aadharFrontFileNameDisplay', 'aadharFrontFileInfo', 'aadharFrontRemoveFile', '.aadharFrontUploadBtn', '#aadharFrontDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Aadhaar Back</label>
                                                        <div class="doc-item compact" id="aadharBackDocContainer">
                                                            <div class="doc-meta">
                                                                <div class="doc-file-icon"><i class="fas fa-id-card"></i></div>
                                                                <div>
                                                                    <span class="doc-name">Aadhaar Back</span>
                                                                    <div id="aadharBackFileInfo" class="file-status">Selected: <span id="aadharBackFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="aadharBackFileInput" name="aadhar_back_document" style="display: none;" accept=".jpg,.jpeg,.png" required
                                                                    onchange="handleDocSelect(this, 'aadharBackFileNameDisplay', 'aadharBackFileInfo', 'aadharBackRemoveFile', '.aadharBackUploadBtn', '#aadharBackDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent aadharBackUploadBtn" onclick="document.getElementById('aadharBackFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt aadharBackRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('aadharBackFileInput', 'aadharBackFileNameDisplay', 'aadharBackFileInfo', 'aadharBackRemoveFile', '.aadharBackUploadBtn', '#aadharBackDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                                            <button type="button" class="btn-verify mt-1" id="aadharVerifyBtn">
                                                                <i class="fas fa-shield-halved me-1"></i> Verify Aadhaar
                                                            </button>
                                                            <span class="verified-badge" id="aadharVerifiedBadge" style="display: none;">
                                                                <i class="fas fa-circle-check me-1"></i> Verified
                                                            </span>
                                                        </div>
                                                        <div id="aadharVerifyStatus" class="kyc-alert" style="display: none;"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PAN section (mandatory for both Individual and Business customers) -->
                                            <div id="panKycSection" class="d-none">
                                                <div class="sub-section-header mt-4">
                                                    <div class="sub-section-icon"><i class="fas fa-credit-card"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">PAN Verification</h6>
                                                        <small class="sub-section-desc">Verify PAN details</small>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">PAN Number</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom text-uppercase" id="panNumber" placeholder="Enter 10-character PAN Number *" name="pan_number" maxlength="10" style="text-transform: uppercase;" value="{{ old('pan_number') }}" required>
                                                            <i class="fas fa-credit-card"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Name as on PAN</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom" id="panHolderName" placeholder="Enter name as on PAN *" name="pan_holder_name" maxlength="255" value="{{ old('pan_holder_name') }}" required>
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">Date of Birth</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom" id="panDob" placeholder="DD/MM/YYYY" name="pan_dob" autocomplete="bday" readonly value="{{ old('pan_dob') }}" required>
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </div>
                                                        <small class="text-muted">Date of birth as per the PAN record.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">PAN Card Document</label>
                                                        <div class="doc-item compact" id="panDocContainer">
                                                            <div class="doc-meta">
                                                                <div class="doc-file-icon"><i class="fas fa-credit-card"></i></div>
                                                                <div>
                                                                    <span class="doc-name">PAN Card</span>
                                                                    <div id="panFileInfo" class="file-status">Selected: <span id="panFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="panFileInput" name="pan_document" style="display: none;" accept=".jpg,.jpeg,.png" required
                                                                    onchange="handleDocSelect(this, 'panFileNameDisplay', 'panFileInfo', 'panRemoveFile', '.panUploadBtn', '#panDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent panUploadBtn" onclick="document.getElementById('panFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt panRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('panFileInput', 'panFileNameDisplay', 'panFileInfo', 'panRemoveFile', '.panUploadBtn', '#panDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                                            <button type="button" class="btn-verify mt-1" id="panVerifyBtn">
                                                                <i class="fas fa-shield-halved me-1"></i> Verify PAN
                                                            </button>
                                                            <span class="verified-badge" id="panVerifiedBadge" style="display: none;">
                                                                <i class="fas fa-circle-check me-1"></i> Verified
                                                            </span>
                                                        </div>
                                                        <div id="panVerifyStatus" class="kyc-alert" style="display: none;"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- GST section (shown when customer type = Business) -->
                                            <div id="gstKycSection" class="d-none">
                                                <div class="sub-section-header">
                                                    <div class="sub-section-icon"><i class="fas fa-receipt"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">GST Verification</h6>
                                                        <small class="sub-section-desc">Verify GSTIN and registered business name </small>
                                                    </div>
                                                    <!-- <span class="badge-sub">Cashfree</span> -->
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">GSTIN</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom text-uppercase" id="gstKycNumber" placeholder="Enter 15-character GSTIN *" name="gst_kyc_number" maxlength="15" value="{{ old('gst_kyc_number') }}" required>
                                                            <i class="fas fa-receipt"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Registered Business Name</label>
                                                        <div class="input-wrapper">
                                                            <input type="text" class="input-custom" id="gstKycBusinessName" placeholder="Enter registered business name *" name="gst_kyc_business_name" maxlength="255" value="{{ old('gst_kyc_business_name') }}" required>
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="section-label">GST Certificate (PDF)</label>
                                                        <div class="doc-item compact" id="gstKycDocContainer">
                                                            <div class="doc-meta">
                                                                <div class="doc-file-icon"><i class="fas fa-file-pdf"></i></div>
                                                                <div>
                                                                    <span class="doc-name">GST Certificate</span>
                                                                    <div id="gstKycFileInfo" class="file-status">Selected: <span id="gstKycFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="gstKycFileInput" name="gst_certificate_document" style="display: none;" accept=".pdf" required
                                                                    onchange="handleDocSelect(this, 'gstKycFileNameDisplay', 'gstKycFileInfo', 'gstKycRemoveFile', '.gstKycUploadBtn', '#gstKycDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent gstKycUploadBtn" onclick="document.getElementById('gstKycFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt gstKycRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('gstKycFileInput', 'gstKycFileNameDisplay', 'gstKycFileInfo', 'gstKycRemoveFile', '.gstKycUploadBtn', '#gstKycDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">GST certificate in PDF format, up to 5 MB.</small>
                                                    </div>
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                                            <button type="button" class="btn-verify mt-1" id="gstKycVerifyBtn">
                                                                <i class="fas fa-shield-halved me-1"></i> Verify GST
                                                            </button>
                                                            <span class="verified-badge" id="gstKycVerifiedBadge" style="display: none;">
                                                                <i class="fas fa-circle-check me-1"></i> Verified
                                                            </span>
                                                        </div>
                                                        <div id="gstKycVerifyStatus" class="kyc-alert" style="display: none;"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-nav">
                                                <button type="button" class="btn-back wizard-prev" data-prev="1"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                                <button type="button" class="btn-gradient wizard-next" data-next="3">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                            </div>
                                        </div>

                                        <!-- ==================== STEP 3: Basic Info ==================== -->
                                        <div class="wizard-panel" data-panel="3">
                                            <div class="section-title-alt"><i class="fas fa-user-pen"></i> Basic Information <span class="step-chip">Step 3 of 4</span></div>
                                            <p class="step-intro">Provide the customer's basic information. Fields verified in Step 2 are auto-filled for you.</p>

                                            <div class="row g-3">
                                                <div class="col-md-6" id="companyNameWrapper">
                                                    <label class="section-label">Company Name <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="companyName" name="company_name" class="input-custom" value="{{ old('company_name') }}" minlength="2" maxlength="150" pattern="[A-Za-z0-9][A-Za-z0-9 .&()'/-]*" required>
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                    <small class="text-muted">Minimum 2 characters.</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">Contact Person <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="contactPerson" name="contact_person" class="input-custom" value="{{ old('contact_person') }}" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <small class="text-muted" id="contactPersonHint">Auto-filled from the verified KYC document.</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">Address Line 1 <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="addressLine1" name="address_line1" class="input-custom" value="{{ old('address_line1') }}" minlength="5" maxlength="255" required>
                                                        <i class="fas fa-location-dot"></i>
                                                    </div>
                                                    <small class="text-muted">Minimum 5 characters.</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">Address Line 2</label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="addressLine2" name="address_line2" class="input-custom" value="{{ old('address_line2') }}" maxlength="255">
                                                        <i class="fas fa-map"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">Address Line 3</label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="addressLine3" name="address_line3" class="input-custom" value="{{ old('address_line3') }}" maxlength="255">
                                                        <i class="fas fa-map-pin"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">Pincode <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="pincode" name="pincode" class="input-custom" value="{{ old('pincode') }}" inputmode="numeric" minlength="6" maxlength="6" pattern="[1-9][0-9]{5}" required>
                                                        <i class="fas fa-hashtag"></i>
                                                    </div>
                                                    <small class="text-muted">Enter a valid 6-digit Indian pincode.</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">City <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="city" name="city" class="input-custom" value="{{ old('city') }}" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                                        <i class="fas fa-city"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="section-label">State <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="text" id="state" name="state" class="input-custom" value="{{ old('state') }}" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                                        <i class="fas fa-flag"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">Phone Number <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="tel" id="phoneNumber" name="phone_number" class="input-custom" value="{{ old('phone_number') }}" inputmode="numeric" minlength="10" maxlength="10" pattern="[6-9][0-9]{9}" required>
                                                        <i class="fas fa-phone"></i>
                                                    </div>
                                                    <small class="text-muted">Enter a valid 10-digit Indian mobile number.</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="section-label">Email <span class="text-danger">*</span></label>
                                                    <div class="input-wrapper">
                                                        <input type="email" id="customerEmail" name="email" class="input-custom" value="{{ old('email') }}" maxlength="150" autocomplete="email" required>
                                                        <i class="fas fa-envelope"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-nav">
                                                <button type="button" class="btn-back wizard-prev" data-prev="2"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                                <button type="button" class="btn-gradient" id="step3ActionBtn">NEXT <i class="fas fa-arrow-right ms-2"></i></button>
                                            </div>
                                        </div>

                                        <!-- ==================== STEP 4: CSB5 Info (CSB V only) ==================== -->
                                        <div class="wizard-panel" data-panel="4">
                                            <div class="section-title-alt"><i class="fas fa-briefcase"></i> CSB V Information <span class="step-chip">Step 4 of 4</span></div>
                                            <p class="step-intro">Provide the additional CSB V details required for your business account.</p>

                                            <div id="csbVFields">
                                                @php
                                                    // Reuse the account's Cashfree-verified GST (session-based). When a
                                                    // valid verification is present, GST is auto-selected and the GSTIN +
                                                    // registered business name are prefilled read-only so the customer can
                                                    // save without re-verifying. The GST certificate PDF is still required
                                                    // because every saved customer stores its own copy.
                                                    $reuseGst = $verifiedGstReusable ?? false;
                                                    $prefilledGstNumber = $reuseGst ? ($verifiedGstNumber ?? '') : old('gst_certificate_number');
                                                    $prefilledGstBusinessName = $reuseGst ? ($verifiedGstBusinessName ?? '') : old('gst_business_name');
                                                @endphp
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <div class="d-flex flex-wrap gap-4 align-items-center">
                                                            <div class="form-check mb-0">
                                                                <input type="checkbox" class="form-check-input" id="isGst" name="is_gst" value="1" {{ (old('is_gst') || $reuseGst) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold" for="isGst">GST</label>
                                                            </div>
                                                            <div class="form-check mb-0">
                                                                <input type="checkbox" class="form-check-input" id="isLut" name="is_lut" value="1" {{ old('is_lut') ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold" for="isLut">LUT (Against Bond or UT)</label>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Select GST, LUT, or both. At least one option is required.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">AD Code <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="text" id="adCode" name="ad_code" class="input-custom" value="{{ old('ad_code') }}" inputmode="numeric" maxlength="14" pattern="[0-9]{7}|[0-9]{14}" data-csb-v-required>
                                                            <i class="fas fa-hashtag"></i>
                                                        </div>
                                                        <small class="text-muted">Enter exactly 7 or 14 numeric digits.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">IEC Number <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="text" id="iecNumber" name="iec_number" class="input-custom text-uppercase" value="{{ old('iec_number') }}" maxlength="10" pattern="[A-Za-z0-9]{10}" data-csb-v-required>
                                                            <i class="fas fa-file-invoice"></i>
                                                        </div>
                                                        <small class="text-muted">Enter exactly 10 letters or digits.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">AD Code Document <span class="text-danger">*</span></label>
                                                        <div class="doc-item compact" id="adCodeDocContainer">
                                                            <div class="doc-meta">
                                                                <div>
                                                                    <span class="doc-name">AD Code Document</span>
                                                                    <div id="adCodeFileInfo" class="file-status">Selected: <span id="adCodeFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="adCodeFileInput" name="ad_code_document" style="display: none;" accept=".pdf,.jpg,.jpeg,.png" data-csb-v-required
                                                                    onchange="handleDocSelect(this, 'adCodeFileNameDisplay', 'adCodeFileInfo', 'adCodeRemoveFile', '.adCodeUploadBtn', '#adCodeDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent adCodeUploadBtn" onclick="document.getElementById('adCodeFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt adCodeRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('adCodeFileInput', 'adCodeFileNameDisplay', 'adCodeFileInfo', 'adCodeRemoveFile', '.adCodeUploadBtn', '#adCodeDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">PDF, JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">IEC Document <span class="text-danger">*</span></label>
                                                        <div class="doc-item compact" id="iecDocContainer">
                                                            <div class="doc-meta">
                                                                <div>
                                                                    <span class="doc-name">IEC Document</span>
                                                                    <div id="iecFileInfo" class="file-status">Selected: <span id="iecFileNameDisplay">file</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end d-flex align-items-center">
                                                                <input type="file" id="iecFileInput" name="iec_document" style="display: none;" accept=".pdf,.jpg,.jpeg,.png" data-csb-v-required
                                                                    onchange="handleDocSelect(this, 'iecFileNameDisplay', 'iecFileInfo', 'iecRemoveFile', '.iecUploadBtn', '#iecDocContainer');">
                                                                <button type="button" class="link-alt border-0 bg-transparent iecUploadBtn" onclick="document.getElementById('iecFileInput').click();">
                                                                    <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                </button>
                                                                <span class="text-danger-alt iecRemoveFile" style="display: none;"
                                                                    onclick="clearDocInput('iecFileInput', 'iecFileNameDisplay', 'iecFileInfo', 'iecRemoveFile', '.iecUploadBtn', '#iecDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">PDF, JPG, JPEG or PNG, up to 5 MB.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Bank Account Number <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="text" id="bankAccountNumber" name="bank_account_number" class="input-custom" value="{{ old('bank_account_number') }}" inputmode="numeric" minlength="9" maxlength="18" pattern="[0-9]{9,18}" data-csb-v-required>
                                                            <i class="fas fa-building-columns"></i>
                                                        </div>
                                                        <small class="text-muted">Enter 9 to 18 numeric digits.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Bank Type <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <select name="bank_type" id="bankType" class="input-custom select-custom" data-csb-v-required>
                                                                <option value="">Select Bank Type</option>
                                                                <option value="private" {{ old('bank_type') === 'private' ? 'selected' : '' }}>Private</option>
                                                                <option value="government" {{ old('bank_type') === 'government' ? 'selected' : '' }}>Government</option>
                                                            </select>
                                                            <i class="fas fa-university"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="lutFields">
                                                    <div class="sub-section-header mt-4">
                                                        <div class="sub-section-icon"><i class="fas fa-file-signature"></i></div>
                                                        <div>
                                                            <h6 class="sub-section-title mb-0">LUT Details</h6>
                                                            <small class="sub-section-desc">Letter of Undertaking bond details</small>
                                                        </div>
                                                        <span class="badge-sub">Required when LUT is selected</span>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="section-label">LUT Bond Start Year <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <select id="lutBondStartYear" name="lut_bond_start_year" class="input-custom select-custom">
                                                                    <option value="">Select Start Year</option>
                                                                    @foreach($lutStartYears as $lutStartYear)
                                                                        <option value="{{ $lutStartYear }}" {{ (string) $lutStartYear === (string) $savedLutStartYear ? 'selected' : '' }}>{{ $lutStartYear }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <i class="fas fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="section-label">LUT Bond End Year <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <select id="lutBondEndYear" class="input-custom select-custom" data-saved-end-year="{{ $savedLutEndYear }}" disabled>
                                                                    <option value="">Select Start Year First</option>
                                                                </select>
                                                                <i class="fas fa-calendar-check"></i>
                                                            </div>
                                                            <input type="hidden" id="lutBondYear" name="lut_bond_year" value="{{ $savedLutBondYear }}">
                                                            @error('lut_bond_year')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="section-label">LUT Expiry Date <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <input type="date" id="lutExpiryDate" name="lut_expiry_date" class="input-custom" value="{{ old('lut_expiry_date') }}" readonly>
                                                                <i class="fas fa-calendar-xmark"></i>
                                                            </div>
                                                            <small class="text-muted">Automatically set to 31 March of the selected LUT Bond End Year.</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="section-label">LUT Document <span class="text-danger">*</span></label>
                                                            <div class="doc-item compact" id="lutDocContainer">
                                                                <div class="doc-meta">
                                                                    <div>
                                                                        <span class="doc-name">LUT Document (PDF)</span>
                                                                        <div id="lutFileInfo" class="file-status">Selected: <span id="lutFileNameDisplay">file</span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="text-end d-flex align-items-center">
                                                                    <input type="file" id="lutFileInput" name="lut_document" style="display: none;" accept=".pdf"
                                                                        onchange="handleDocSelect(this, 'lutFileNameDisplay', 'lutFileInfo', 'lutRemoveFile', '.lutUploadBtn', '#lutDocContainer');">
                                                                    <button type="button" class="link-alt border-0 bg-transparent lutUploadBtn" onclick="document.getElementById('lutFileInput').click();">
                                                                        <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                    </button>
                                                                    <span class="text-danger-alt lutRemoveFile" style="display: none;"
                                                                        onclick="clearDocInput('lutFileInput', 'lutFileNameDisplay', 'lutFileInfo', 'lutRemoveFile', '.lutUploadBtn', '#lutDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">Letter of Undertaking in PDF format, up to 5 MB.</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="gstFields" data-gst-reusable="{{ $reuseGst ? '1' : '0' }}">
                                                    <div class="sub-section-header mt-4">
                                                        <div class="sub-section-icon"><i class="fas fa-receipt"></i></div>
                                                        <div>
                                                            <h6 class="sub-section-title mb-0">GST Registration</h6>
                                                            <small class="sub-section-desc">Enter and verify your GSTIN and registered business name through Cashfree</small>
                                                        </div>
                                                        <span class="badge-sub">Required when GST is selected</span>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="section-label">GSTIN <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <input type="text" id="gstNumber" name="gst_certificate_number" class="input-custom text-uppercase" value="{{ $prefilledGstNumber }}" maxlength="15" placeholder="Enter 15-character GSTIN *" {{ $reuseGst ? 'readonly' : '' }}>
                                                                <i class="fas fa-receipt"></i>
                                                            </div>
                                                            <small class="text-muted">Enter exactly 15 characters (e.g., 22AAAAA0000A1Z5).</small>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="section-label">Registered Business Name <span class="text-danger">*</span></label>
                                                            <div class="input-wrapper">
                                                                <input type="text" id="gstBusinessName" name="gst_business_name" class="input-custom" value="{{ $prefilledGstBusinessName }}" maxlength="255" placeholder="Enter registered business name *" {{ $reuseGst ? 'readonly' : '' }}>
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="section-label">GST Certificate (PDF) <span class="text-danger">*</span></label>
                                                            <div class="doc-item compact" id="gstDocContainer">
                                                                <div class="doc-meta">
                                                                    <div>
                                                                        <span class="doc-name">GST Certificate (PDF)</span>
                                                                        <div id="gstFileInfo" class="file-status">Selected: <span id="gstFileNameDisplay">file</span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="text-end d-flex align-items-center">
                                                                    <input type="file" id="gstFileInput" name="gst_certificate_document" style="display: none;" accept=".pdf"
                                                                        onchange="handleDocSelect(this, 'gstFileNameDisplay', 'gstFileInfo', 'gstRemoveFile', '.gstUploadBtn', '#gstDocContainer');">
                                                                    <button type="button" class="link-alt border-0 bg-transparent gstUploadBtn" onclick="document.getElementById('gstFileInput').click();">
                                                                        <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                                    </button>
                                                                    <span class="text-danger-alt gstRemoveFile" style="display: none;"
                                                                        onclick="clearDocInput('gstFileInput', 'gstFileNameDisplay', 'gstFileInfo', 'gstRemoveFile', '.gstUploadBtn', '#gstDocContainer');"><i class="fas fa-trash-alt"></i> Remove</span>
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">GST certificate in PDF format, up to 5 MB.</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <div id="gstVerifyStatus" class="small text-muted" role="status">Click VERIFY GST to validate your details through Cashfree before submission.</div>
                                                            <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                                                                <button type="button" id="verifyGstBtn" class="btn-gradient">
                                                                    <i class="fas fa-shield-alt me-1"></i> VERIFY GST
                                                                </button>
                                                                <span class="verified-badge" id="gstVerifiedBadge" style="display: none;">
                                                                    <i class="fas fa-circle-check me-1"></i> Verified
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sub-section-header mt-4">
                                                    <div class="sub-section-icon"><i class="fas fa-receipt"></i></div>
                                                    <div>
                                                        <h6 class="sub-section-title mb-0">Billing Details</h6>
                                                        <small class="sub-section-desc">Address and contact for billing</small>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="section-label">Billing Address <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <textarea id="billingAddress" name="billing_address" class="input-custom" rows="3" minlength="10" maxlength="1000" data-csb-v-required placeholder="Enter billing address *" style="padding-top: 16px;">{{ old('billing_address') }}</textarea>
                                                            <i class="fas fa-location-dot" style="top: 22px;"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Billing Contact Number <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="tel" id="billingContact" name="billing_contact" class="input-custom" value="{{ old('billing_contact') }}" inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" data-csb-v-required>
                                                            <i class="fas fa-phone"></i>
                                                        </div>
                                                        <small class="text-muted">Enter a valid 10-digit Indian mobile number.</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="section-label">Billing Email <span class="text-danger">*</span></label>
                                                        <div class="input-wrapper">
                                                            <input type="email" id="billingEmail" name="billing_email" class="input-custom" value="{{ old('billing_email') }}" maxlength="255" data-csb-v-required>
                                                            <i class="fas fa-envelope"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-nav">
                                                <button type="button" class="btn-back wizard-prev" data-prev="3"><i class="fas fa-arrow-left me-2"></i> BACK</button>
                                                <button type="submit" class="btn-gradient" id="submitCustomerBtn">SAVE CUSTOMER <i class="fas fa-check-circle ms-2"></i></button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade {{ session('success') ? 'show active' : '' }}" id="view-customers-pane" role="tabpanel" aria-labelledby="view-customers-tab" tabindex="0">
                            @php
                                $avatarGradients = [
                                    'linear-gradient(135deg, #6366f1, #8b5cf6)',
                                    'linear-gradient(135deg, #0ea5e9, #2563eb)',
                                    'linear-gradient(135deg, #10b981, #059669)',
                                    'linear-gradient(135deg, #f59e0b, #ea580c)',
                                    'linear-gradient(135deg, #ec4899, #a855f7)',
                                    'linear-gradient(135deg, #06b6d4, #0d9488)',
                                    'linear-gradient(135deg, #f43f5e, #d946ef)',
                                ];
                            @endphp
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div>
                                    <h5 class="mb-1">All Customers</h5>
                                    <p class="mb-0 text-muted small">Click Enable CSB5 to upgrade a CSB IV customer with CSB V details</p>
                                </div>
                                <span class="mini-chip na">Total: {{ $exporterCustomers->count() }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="exporterCustomersTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><i class="ti ti-building me-1"></i>Customer</th>
                                            <th><i class="ti ti-tag me-1"></i>Type</th>
                                            <th><i class="ti ti-map-pin me-1"></i>Address</th>
                                            <th><i class="ti ti-briefcase me-1"></i>CSB</th>
                                            <th><i class="ti ti-receipt me-1"></i>GST / LUT</th>
                                            <th><i class="ti ti-shield-check me-1"></i>KYC</th>
                                            <th><i class="ti ti-calendar me-1"></i>Added</th>
                                            <th><i class="ti ti-settings me-1"></i>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($exporterCustomers as $key => $savedCustomer)
                                            @php
                                                $fullCompany = trim((string) ($savedCustomer->company_name ?: $savedCustomer->contact_person ?: 'Customer'));
                                                $nameWords = preg_split('/\s+/', $fullCompany, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                                                $initials = strtoupper((string) mb_substr($nameWords[0] ?? '?', 0, 1) . (string) mb_substr($nameWords[1] ?? ($nameWords[0] ?? ''), 0, 1));
                                                $avatarBg = $avatarGradients[$key % count($avatarGradients)];
                                                $custType = strtolower((string) ($savedCustomer->businessCategory?->category_name ?: ''));
                                                $isCsbV = $savedCustomer->csb_type === 'csb_v';
                                            @endphp
                                            <tr id="customer-row-{{ $savedCustomer->id }}">
                                                <td><span class="row-index">{{ $key + 1 }}</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="user-avatar" style="background: {{ $avatarBg }};">{{ $initials }}</span>
                                                        <div>
                                                            <div class="user-name">{{ $savedCustomer->company_name }}</div>
                                                            <div class="user-code mt-1"><i class="ti ti-user me-1"></i>{{ $savedCustomer->contact_person }}</div>
                                                            <div class="user-contact mt-1"><i class="ti ti-phone"></i>{{ $savedCustomer->phone_number }}</div>
                                                            <div class="user-contact mt-1"><i class="ti ti-mail"></i>{{ $savedCustomer->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($savedCustomer->businessCategory?->category_name)
                                                        <span class="type-chip business"><i class="ti ti-tag"></i>{{ $savedCustomer->businessCategory->category_name }}</span>
                                                    @else
                                                        <span class="type-chip none">Not specified</span>
                                                    @endif
                                                </td>
                                                <td id="address-cell-{{ $savedCustomer->id }}">
                                                    <div class="address-box">
                                                    @if(! empty($savedCustomer->address_line1))
                                                        <div class="saved-address-item">
                                                            <span class="badge bg-primary-subtle text-primary border me-1 saved-address-badge">Primary</span>
                                                            <span class="fw-semibold">{{ $savedCustomer->address_line1 }}</span>
                                                            @php($extraAddressLines = collect([$savedCustomer->address_line2, $savedCustomer->address_line3])->filter())
                                                            @if($extraAddressLines->isNotEmpty())
                                                                <div class="small text-muted">{{ $extraAddressLines->implode(', ') }}</div>
                                                            @endif
                                                            <div class="small text-muted">{{ $savedCustomer->city }}, {{ $savedCustomer->state }} - {{ $savedCustomer->pincode }}</div>
                                                        </div>
                                                    @else
                                                        <div class="small text-muted">No address provided.</div>
                                                    @endif
                                                    </div>
                                                </td>
                                                <td id="csb-type-cell-{{ $savedCustomer->id }}">
                                                    @if($isCsbV)
                                                        <span class="status-pill csb-v"><span class="dot"></span>CSB V</span>
                                                    @else
                                                        <span class="status-pill csb-iv"><span class="dot"></span>CSB IV</span>
                                                    @endif
                                                </td>
                                                <td id="gst-lut-cell-{{ $savedCustomer->id }}">
                                                    @if($isCsbV)
                                                        @if($savedCustomer->is_gst)
                                                            <span class="mini-chip gst"><i class="ti ti-receipt"></i>GST</span>
                                                        @endif
                                                        @if($savedCustomer->is_lut)
                                                            <span class="mini-chip lut"><i class="ti ti-file-text"></i>LUT</span>
                                                        @endif
                                                        @if(! $savedCustomer->is_gst && ! $savedCustomer->is_lut)
                                                            <span class="mini-chip na">-</span>
                                                        @endif
                                                    @else
                                                        <span class="mini-chip na">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($savedCustomer->kyc_type || $savedCustomer->kyc_number)
                                                        <div class="kyc-title">{{ $savedCustomer->kyc_type ?: 'Not specified' }}</div>
                                                        <div class="kyc-sub mt-1">{{ $savedCustomer->kyc_number ?: '-' }}</div>
                                                    @else
                                                        <span class="text-muted small">Not provided</span>
                                                    @endif
                                                </td>
                                                <td><span class="mini-chip na"><i class="ti ti-calendar"></i>{{ $savedCustomer->created_at?->format('d M Y') }}</span></td>
                                                <td id="actions-cell-{{ $savedCustomer->id }}">
                                                    <div class="d-flex flex-column gap-1" style="min-width:130px;">
                                                        <button type="button"
                                                                class="btn-view view-customer-btn"
                                                                data-customer-id="{{ $savedCustomer->id }}"
                                                                data-company="{{ $savedCustomer->company_name }}"
                                                                title="View Details">
                                                            <i class="ti ti-eye"></i> View
                                                        </button>
                                                        <button type="button"
                                                                class="btn-address add-address-btn"
                                                                style="background-color: #4f46e5; color: #fff;"
                                                                data-customer-id="{{ $savedCustomer->id }}"
                                                                data-company="{{ $savedCustomer->company_name }}"
                                                                title="Save Address">
                                                            <i class="fas fa-location-dot"></i> Add Address
                                                        </button>
                                                        @if(! $isCsbV)
                                                            <button type="button"
                                                                    class="btn-csb5 enable-csb5-btn"
                                                                    data-customer-id="{{ $savedCustomer->id }}"
                                                                    data-company="{{ $savedCustomer->company_name }}"
                                                                    title="Enable CSB5">
                                                                <i class="fas fa-briefcase"></i> Enable CSB5
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">
                                                    <div class="empty-state">
                                                        <div class="empty-icon mb-2"><i class="ti ti-users-off"></i></div>
                                                        <div class="fw-semibold text-secondary">No customers found</div>
                                                        <div class="small">Add your first customer using the Add Customer tab.</div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addAddressForm" method="POST"
                  action="{{ route('customer.exporter-customers.address', '__CUSTOMER_ID__') }}"
                  data-action-template="{{ route('customer.exporter-customers.address', '__CUSTOMER_ID__') }}">
                @csrf
                <input type="hidden" id="addressCustomerId" name="customer_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: #fff;" id="addAddressModalLabel"><i class="fas fa-location-dot me-2 text-white"></i>Add Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="section-label">Address Line 1 <span class="text-danger">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" id="modalAddressLine1" name="address_line1" class="input-custom" minlength="5" maxlength="255" required>
                            <i class="fas fa-location-dot"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="section-label">Address Line 2</label>
                                <div class="input-wrapper">
                                    <input type="text" id="modalAddressLine2" name="address_line2" class="input-custom" maxlength="255">
                                    <i class="fas fa-map"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="section-label">Address Line 3</label>
                                <div class="input-wrapper">
                                    <input type="text" id="modalAddressLine3" name="address_line3" class="input-custom" maxlength="255">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="section-label">Pincode <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" id="modalAddressPincode" name="pincode" class="input-custom" inputmode="numeric" minlength="6" maxlength="6" pattern="[1-9][0-9]{5}" required>
                                    <i class="fas fa-hashtag"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="section-label">City <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" id="modalAddressCity" name="city" class="input-custom" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                                    <i class="fas fa-city"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="section-label">State <span class="text-danger">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" id="modalAddressState" name="state" class="input-custom" minlength="2" maxlength="100" pattern="[A-Za-z][A-Za-z .'-]*" required>
                            <i class="fas fa-flag"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addAddressSubmitBtn"><i class="fas fa-location-dot me-1"></i> Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enable CSB5 Modal (CSB IV -> CSB V upgrade) -->
<div class="modal fade" id="enableCsb5Modal" tabindex="-1" aria-labelledby="enableCsb5ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="enableCsb5Form" method="POST" enctype="multipart/form-data"
                  action="{{ route('customer.exporter-customers.enable-csb5', '__CUSTOMER_ID__') }}"
                  data-action-template="{{ route('customer.exporter-customers.enable-csb5', '__CUSTOMER_ID__') }}">
                @csrf
                <input type="hidden" id="csb5CustomerId" name="customer_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="enableCsb5ModalLabel"><i class="fas fa-briefcase me-2 text-white"></i>Enable CSB5 - <span id="csb5CustomerName">-</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">CSB5 details bharne par is customer ka status <strong>CSB IV se CSB V</strong> me update ho jayega.</div>
                    <div id="csb5FormError" class="alert alert-danger small d-none"></div>
                    <div class="d-flex flex-wrap gap-4 align-items-center mb-3">
                        <div class="form-check mb-0">
                            <input type="checkbox" class="form-check-input" id="csb5IsGst" name="is_gst" value="1">
                            <label class="form-check-label fw-semibold" for="csb5IsGst">GST</label>
                        </div>
                        <div class="form-check mb-0">
                            <input type="checkbox" class="form-check-input" id="csb5IsLut" name="is_lut" value="1">
                            <label class="form-check-label fw-semibold" for="csb5IsLut">LUT (Against Bond or UT)</label>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Select GST, LUT, or both. At least one option is required.</small>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="section-label">AD Code <span class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <input type="text" id="csb5AdCode" name="ad_code" class="input-custom" inputmode="numeric" maxlength="14" pattern="[0-9]{7}|[0-9]{14}" required>
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <small class="text-muted">Exactly 7 or 14 numeric digits.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">IEC Number <span class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <input type="text" id="csb5IecNumber" name="iec_number" class="input-custom text-uppercase" maxlength="10" pattern="[A-Za-z0-9]{10}" style="text-transform: uppercase;" required>
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <small class="text-muted">Exactly 10 letters or digits.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">AD Code Document <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="csb5AdCodeDoc" name="ad_code_document" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">PDF, JPG, JPEG or PNG, up to 5 MB.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">IEC Document <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="csb5IecDoc" name="iec_document" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">PDF, JPG, JPEG or PNG, up to 5 MB.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">Bank Account Number <span class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <input type="text" id="csb5BankAccount" name="bank_account_number" class="input-custom" inputmode="numeric" minlength="9" maxlength="18" pattern="[0-9]{9,18}" required>
                                <i class="fas fa-building-columns"></i>
                            </div>
                            <small class="text-muted">9 to 18 numeric digits.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">Bank Type <span class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <select name="bank_type" id="csb5BankType" class="input-custom select-custom" required>
                                    <option value="">Select Bank Type</option>
                                    <option value="private">Private</option>
                                    <option value="government">Government</option>
                                </select>
                                <i class="fas fa-university"></i>
                            </div>
                        </div>
                    </div>
                    <div id="csb5LutFields" style="display: none;">
                        <hr>
                        <h6 class="fw-bold mb-3"><i class="fas fa-file-signature me-1"></i>LUT Details</h6>
                        @php($csb5LutStartYears = range(now()->year, now()->year + 5))
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="section-label">LUT Bond Start Year <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <select id="csb5LutStartYear" name="lut_bond_start_year" class="input-custom select-custom">
                                        <option value="">Select Start Year</option>
                                        @foreach($csb5LutStartYears as $csb5LutYear)
                                            <option value="{{ $csb5LutYear }}">{{ $csb5LutYear }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-calendar"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="section-label">LUT Bond End Year <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <select id="csb5LutEndYear" name="lut_bond_end_year" class="input-custom select-custom" disabled>
                                        <option value="">Select Start Year First</option>
                                    </select>
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="section-label">LUT Expiry Date <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="date" id="csb5LutExpiry" name="lut_expiry_date" class="input-custom" readonly>
                                    <i class="fas fa-calendar-xmark"></i>
                                </div>
                                <small class="text-muted">Auto: 31 March of End Year.</small>
                            </div>
                            <div class="col-12">
                                <label class="section-label">LUT Document (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="csb5LutDoc" name="lut_document" accept=".pdf">
                                <small class="text-muted">PDF, up to 5 MB.</small>
                            </div>
                        </div>
                    </div>
                    <div id="csb5GstFields" style="display: none;">
                        <hr>
                        <h6 class="fw-bold mb-3"><i class="fas fa-receipt me-1"></i>GST Registration</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="section-label">GSTIN <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" id="csb5GstNumber" name="gst_certificate_number" class="input-custom text-uppercase" maxlength="15" style="text-transform: uppercase;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <small class="text-muted">15-character GSTIN (e.g., 22AAAAA0000A1Z5).</small>
                            </div>
                            <div class="col-md-6">
                                <label class="section-label">Registered Business Name <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" id="csb5GstBusinessName" name="gst_business_name" class="input-custom" maxlength="255">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="section-label">GST Certificate (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="csb5GstDoc" name="gst_certificate_document" accept=".pdf">
                                <small class="text-muted">PDF, up to 5 MB.</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3"><i class="fas fa-receipt me-1"></i>Billing Details</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="section-label">Billing Address <span class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <textarea id="csb5BillingAddress" name="billing_address" class="input-custom" rows="3" minlength="10" maxlength="1000" required style="padding-top: 16px;"></textarea>
                                <i class="fas fa-location-dot" style="top: 22px;"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">Billing Contact Number <span class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <input type="tel" id="csb5BillingContact" name="billing_contact" class="input-custom" inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" required>
                                <i class="fas fa-phone"></i>
                            </div>
                            <small class="text-muted">10-digit mobile starting with 6/7/8/9.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">Billing Email <span class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <input type="email" id="csb5BillingEmail" name="billing_email" class="input-custom" maxlength="255" required>
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="enableCsb5SubmitBtn"><i class="fas fa-check-circle me-1"></i> Save & Enable CSB5</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Customer Details Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCustomerModalLabel"><i class="ti ti-eye me-2 text-white"></i>Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background:#f8fafc;max-height:70vh;overflow-y:auto;">
                <div id="viewCustomerLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                    <p class="mt-2 text-muted small">Loading customer details...</p>
                </div>
                <div id="viewCustomerError" class="alert alert-danger small d-none"></div>
                <div id="viewCustomerContent" style="display:none;">
                    <div class="view-detail-hero mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="view-detail-avatar" id="vcAvatar">-</span>
                            <div class="flex-grow-1">
                                <div class="fw-bold fs-5" id="vcCompany">-</div>
                                <div class="small opacity-75" id="vcContact">-</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="meta-pill" id="vcCsbPill">-</span>
                                    <span class="meta-pill" id="vcTypePill">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="view-detail-section">
                        <h6><i class="ti ti-user"></i>Basic Info</h6>
                        <div class="view-detail-row"><span class="label">Contact Person</span><span class="value" id="vcContactPerson">-</span></div>
                        <div class="view-detail-row"><span class="label">Phone</span><span class="value" id="vcPhone">-</span></div>
                        <div class="view-detail-row"><span class="label">Email</span><span class="value" id="vcEmail">-</span></div>
                        <div class="view-detail-row"><span class="label">Customer Type</span><span class="value" id="vcCustomerType">-</span></div>
                        <div class="view-detail-row"><span class="label">Added On</span><span class="value" id="vcAddedOn">-</span></div>
                    </div>
                    <div class="view-detail-section">
                        <h6><i class="ti ti-map-pin"></i>Addresses</h6>
                        <div id="vcAddresses"></div>
                    </div>
                    <div class="view-detail-section">
                        <h6><i class="ti ti-briefcase"></i>CSB Details</h6>
                        <div class="view-detail-row"><span class="label">CSB Type</span><span class="value" id="vcCsbType">-</span></div>
                        <div class="view-detail-row"><span class="label">GST / LUT</span><span class="value" id="vcGstLut">-</span></div>
                        <div class="view-detail-row"><span class="label">GSTIN</span><span class="value" id="vcGstNumber">-</span></div>
                        <div class="view-detail-row"><span class="label">GST Business</span><span class="value" id="vcGstBusiness">-</span></div>
                        <div class="view-detail-row"><span class="label">AD Code</span><span class="value" id="vcAdCode">-</span></div>
                        <div class="view-detail-row"><span class="label">IEC Number</span><span class="value" id="vcIec">-</span></div>
                        <div class="view-detail-row"><span class="label">Bank Account</span><span class="value" id="vcBankAccount">-</span></div>
                        <div class="view-detail-row"><span class="label">Bank Type</span><span class="value" id="vcBankType">-</span></div>
                        <div class="view-detail-row"><span class="label">LUT Bond Year</span><span class="value" id="vcLutYear">-</span></div>
                        <div class="view-detail-row"><span class="label">LUT Expiry</span><span class="value" id="vcLutExpiry">-</span></div>
                    </div>
                    <div class="view-detail-section">
                        <h6><i class="ti ti-shield-check"></i>KYC Details</h6>
                        <div class="view-detail-row"><span class="label">KYC Type</span><span class="value" id="vcKycType">-</span></div>
                        <div class="view-detail-row"><span class="label">KYC Number</span><span class="value" id="vcKycNumber">-</span></div>
                        <div class="view-detail-row"><span class="label">PAN Number</span><span class="value" id="vcPan">-</span></div>
                        <div class="view-detail-row"><span class="label">PAN Holder</span><span class="value" id="vcPanHolder">-</span></div>
                        <div class="view-detail-row"><span class="label">PAN DOB</span><span class="value" id="vcPanDob">-</span></div>
                    </div>
                    <div class="view-detail-section">
                        <h6><i class="ti ti-receipt"></i>Billing Details</h6>
                        <div class="view-detail-row"><span class="label">Billing Address</span><span class="value" id="vcBillingAddress">-</span></div>
                        <div class="view-detail-row"><span class="label">Billing Contact</span><span class="value" id="vcBillingContact">-</span></div>
                        <div class="view-detail-row"><span class="label">Billing Email</span><span class="value" id="vcBillingEmail">-</span></div>
                    </div>
                    <div class="view-detail-section">
                        <h6><i class="ti ti-files"></i>Documents</h6>
                        <div id="vcDocuments"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Embedded customer details — View modal ka primary source (network ki need nahi,
    // isliye host/subpath mismatch par bhi View hamesha kaam karega).
    // JSON controller me json_encode se banta hai (Blade ki json directive wali nested-array issue se bachne ke liye).
    window.exporterCustomersDetailMap = {!! $exporterCustomersDetailJson ?? '{}' !!};
</script>

<!-- Inline helpers so the upload widgets work even if external JS is cached -->
<script>
    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    (function () {
        // The shared header (partials/global-alert.blade.php) registers the real
        // modal popup as window.showAlert. Capture it BEFORE this script overwrites
        // the binding, so every helper on this page forwards to the visible popup
        // and messages are never silently swallowed.
        var realShowAlert = (typeof window !== 'undefined' && typeof window.showAlert === 'function')
            ? window.showAlert
            : null;
        var alertFallingBack = false;

        window.showAlert = function (message, type) {
            if (realShowAlert) {
                try {
                    realShowAlert(message, type);
                    return;
                } catch (e) {
                    // Fall through to the native fallback below.
                }
            }
            // Native fallback (guarded so an overridden alert() cannot recurse).
            if (!alertFallingBack && typeof window.alert === 'function') {
                alertFallingBack = true;
                try {
                    window.alert(message == null ? '' : String(message));
                } finally {
                    alertFallingBack = false;
                }
            }
        };
    })();
    function handleDocSelect(input, nameId, infoId, removeClass, uploadBtnSel, containerSel) {
        if (input.files && input.files.length > 0) {
            var file = input.files[0];
            var imageOnlyFields = ['aadhar_front_document', 'aadhar_back_document', 'pan_document'];
            var extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
            if (imageOnlyFields.indexOf(input.name) !== -1 &&
                (['jpg', 'jpeg', 'png'].indexOf(extension) === -1 ||
                    ['image/jpeg', 'image/png'].indexOf(file.type) === -1 ||
                    file.size > 5 * 1024 * 1024)) {
                input.value = '';
                showAlert('Uploaded documents must be JPG, JPEG, or PNG images up to 5 MB.', 'warning');
                return;
            }
            var name = file.name;
            var nameEl = document.getElementById(nameId);
            if (nameEl) { nameEl.textContent = name; }
            var infoEl = document.getElementById(infoId);
            if (infoEl) { infoEl.style.display = 'block'; }
            var removeEl = document.querySelector('.' + removeClass);
            if (removeEl) { removeEl.style.display = 'inline-block'; }
            var uploadBtn = document.querySelector(uploadBtnSel);
            if (uploadBtn) { uploadBtn.style.display = 'none'; }
            var container = document.querySelector(containerSel);
            if (container) { container.classList.add('has-file'); }
            var field = document.getElementById(input.id);
            if (field) { field.classList.remove('input-invalid'); }
        }
    }
    function clearDocInput(inputId, nameId, infoId, removeClass, uploadBtnSel, containerSel) {
        var input = document.getElementById(inputId);
        if (input) { input.value = ''; }
        var infoEl = document.getElementById(infoId);
        if (infoEl) { infoEl.style.display = 'none'; }
        var removeEl = document.querySelector('.' + removeClass);
        if (removeEl) { removeEl.style.display = 'none'; }
        var uploadBtn = document.querySelector(uploadBtnSel);
        if (uploadBtn) { uploadBtn.style.display = 'inline-block'; }
        var container = document.querySelector(containerSel);
        if (container) { container.classList.remove('has-file'); }
    }
</script>

<script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var panDob = document.getElementById('panDob');
        if (panDob && typeof flatpickr !== 'undefined') {
            flatpickr(panDob, {
                dateFormat: 'd/m/Y',
                maxDate: 'today'
            });
        }
    });
</script>

<script>
    // KYC Type (Step 1) must reflect the selected customer type:
    //   Individual => ONLY "Aadhar Card".
    //   Business   => "PAN Card" and "GST (Normal)" (GST stays the default).
    // Selecting one of the two Business options does not change Step 2 - a
    // Business customer still verifies PAN + GST before saving; the dropdown
    // only records which document is the customer's primary KYC type.
    // Kept inline (in addition to exporter-customers.js) so it can never be
    // skipped by an externally cached JS file or an unrelated load error.
    (function () {
        function desiredOptions(isBusiness) {
            if (isBusiness) {
                return [
                    { value: 'PAN Card', label: 'PAN Card', selected: false },
                    { value: 'GST (Normal)', label: 'GST (Normal)', selected: true }
                ];
            }
            return [
                { value: 'Aadhar Card', label: 'Aadhar Card', selected: true }
            ];
        }

        function sameOptionSet(kycType, desiredValues) {
            if (!kycType.options || kycType.options.length !== desiredValues.length) {
                return false;
            }
            for (var i = 0; i < kycType.options.length; i++) {
                if (kycType.options[i].value !== desiredValues[i]) {
                    return false;
                }
            }
            return true;
        }

        function syncKycTypeFromCustomerType() {
            var customerType = document.getElementById('businessCategoryId');
            var kycType = document.getElementById('kycType');
            if (!customerType || !kycType) {
                return;
            }
            // No customer type chosen yet - keep the server-rendered options
            // (including any old() preselection after a validation error).
            if (!customerType.value) {
                return;
            }
            var option = customerType.options[customerType.selectedIndex];
            var isBusiness = !!(option && option.value && option.dataset.userType === 'business');
            var desired = desiredOptions(isBusiness);
            var desiredValues = [];
            var defaultOption = null;
            for (var i = 0; i < desired.length; i++) {
                desiredValues.push(desired[i].value);
                if (desired[i].selected) {
                    defaultOption = desired[i];
                }
            }

            var previousValue = kycType.value;

            // Rebuild only when the current option set differs, so this stays
            // idempotent alongside the identical logic in exporter-customers.js.
            if (sameOptionSet(kycType, desiredValues)) {
                if (previousValue && desiredValues.indexOf(previousValue) !== -1) {
                    return;
                }
                if (defaultOption) {
                    kycType.value = defaultOption.value;
                }
                return;
            }

            kycType.innerHTML = '';
            var preserveSelection = previousValue && desiredValues.indexOf(previousValue) !== -1;
            for (var j = 0; j < desired.length; j++) {
                var newOption = document.createElement('option');
                newOption.value = desired[j].value;
                newOption.textContent = desired[j].label;
                newOption.selected = preserveSelection ? desired[j].value === previousValue : !!desired[j].selected;
                kycType.appendChild(newOption);
            }
            if (kycType.value === '' && defaultOption) {
                kycType.value = defaultOption.value;
            }
        }

        function init() {
            var customerType = document.getElementById('businessCategoryId');
            if (!customerType) {
                return;
            }
            customerType.addEventListener('change', syncKycTypeFromCustomerType);
            if (customerType.value) {
                syncKycTypeFromCustomerType();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>

<script src="{{ asset('js/exporter-customers.js') }}?v={{ filemtime(public_path('js/exporter-customers.js')) ?: 1 }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('addAddressModal');
        var form = document.getElementById('addAddressForm');
        if (!modalEl || !form) {
            return;
        }

        // Open the popup with empty fields. Each "Save Address" click appends a
        // brand-new address to the customer, so nothing is pre-filled anymore.
        document.querySelectorAll('.add-address-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-customer-id');
                document.getElementById('addressCustomerId').value = id || '';

                var template = form.getAttribute('data-action-template') || '';
                form.setAttribute('action', template.replace('__CUSTOMER_ID__', id || ''));

                var fields = ['modalAddressLine1', 'modalAddressLine2', 'modalAddressLine3',
                    'modalAddressPincode', 'modalAddressCity', 'modalAddressState'];
                fields.forEach(function (fieldId) {
                    var el = document.getElementById(fieldId);
                    if (el) { el.value = ''; }
                });

                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });
        });

        // Submit via AJAX, then rebuild the row's Address cell with the full
        // list of addresses (primary + any extra ones that were appended).
        function renderAddressList(cell, addresses) {
            if (!cell || !addresses || !addresses.length) {
                return;
            }
            // List me sirf primary address dikhta hai — pehla (primary) hi render karo.
            var addr = addresses[0];
            var lines23 = [addr.address_line2, addr.address_line3]
                .filter(function (p) { return p && p.trim(); })
                .join(', ');
            var html = '<div class="address-box"><div class="saved-address-item">';
            html += '<span class="badge bg-primary-subtle text-primary border me-1 saved-address-badge">Primary</span>';
            html += '<span class="fw-semibold">' + escapeHtml(addr.address_line1) + '</span>';
            if (lines23) {
                html += '<div class="small text-muted">' + escapeHtml(lines23) + '</div>';
            }
            html += '<div class="small text-muted">' +
                escapeHtml((addr.city || '') + ', ' + (addr.state || '') + ' - ' + (addr.pincode || '')) +
                '</div></div></div>';
            cell.innerHTML = html;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var submitBtn = document.getElementById('addAddressSubmitBtn');
            var originalHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
            }

            var formData = new FormData(form);
            var token = document.querySelector('meta[name="csrf-token"]');
            var csrf = token ? token.getAttribute('content') : '';
            var savedOk = false;

            function parsePayload(res) {
                // Parse JSON safely. A non-JSON body (redirect HTML, proxy error
                // page, Laravel 419 page, etc.) must NOT abort the chain - we fall
                // back to the raw text so the real failure is still reported.
                return res.text().then(function (text) {
                    var data = null;
                    var body = (text || '').trim();
                    if (body) {
                        try {
                            data = JSON.parse(body);
                        } catch (e) {
                            data = null;
                        }
                    }
                    return {
                        ok: res.ok,
                        status: res.status,
                        data: data,
                        raw: body
                    };
                });
            }

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(parsePayload)
                .then(function (result) {
                    var data = result.data || {};

                    // Success: server persisted the address and returned JSON.
                    if (result.ok && data.success) {
                        savedOk = true;

                        // Close the popup FIRST so a later UI hiccup can never
                        // leave the user stuck staring at the modal.
                        try {
                            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.hide();
                        } catch (e) {
                            // Best-effort: this modal was opened successfully earlier.
                        }

                        // Re-render the row's Address cell + confirmation popup.
                        // Any failure here must NOT surface as a network error.
                        try {
                            var id = document.getElementById('addressCustomerId').value;
                            var cell = document.getElementById('address-cell-' + id);
                            if (cell) {
                                renderAddressList(cell, data.addresses || []);
                            }
                            showAlert(data.message || 'Address saved successfully!', 'success');
                        } catch (e) {
                            // The save DID succeed - never hide that fact.
                            showAlert(data.message || 'Address saved successfully!', 'success');
                        }
                        return;
                    }

                    // Any JSON payload that reports a failure (validation errors,
                    // CSRF message, etc.) - always prefer the server's real message.
                    if (data && !data.success && (data.message || data.errors)) {
                        var msg = data.message || 'Failed to save address. Please check your details.';
                        if (data.errors) {
                            var parts = [];
                            Object.keys(data.errors).forEach(function (k) {
                                var v = data.errors[k];
                                if (Array.isArray(v)) {
                                    parts = parts.concat(v);
                                } else {
                                    parts.push(v);
                                }
                            });
                            if (parts.length) {
                                msg = parts.join(' ');
                            }
                        }
                        showAlert(msg, 'error');
                        return;
                    }

                    // Empty or non-JSON body - derive the best message from the
                    // HTTP status so a real failure is never hidden.
                    if (result.status === 419) {
                        showAlert('Your session has expired. Please refresh the page and try again.', 'error');
                    } else if (result.status === 422) {
                        showAlert('Please check the highlighted fields and try again.', 'error');
                    } else if (result.status === 403) {
                        showAlert('You are not allowed to perform this action.', 'error');
                    } else {
                        showAlert('A network error occurred. Please try again.', 'error');
                    }
                })
                .catch(function () {
                    // Only a true network failure reaches here. If the save already
                    // succeeded above, never override it with the generic message.
                    if (savedOk) {
                        return;
                    }
                    showAlert('A network error occurred. Please try again.', 'error');
                })
                .finally(function () {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }
                });
        });
    });
</script>

<script>
    // Enable CSB5 modal: CSB IV customer ko CSB V me upgrade karo.
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('enableCsb5Modal');
        var form = document.getElementById('enableCsb5Form');
        if (!modalEl || !form) {
            return;
        }
        var nameEl = document.getElementById('csb5CustomerName');
        var idEl = document.getElementById('csb5CustomerId');
        var errorEl = document.getElementById('csb5FormError');
        var isGst = document.getElementById('csb5IsGst');
        var isLut = document.getElementById('csb5IsLut');
        var gstFields = document.getElementById('csb5GstFields');
        var lutFields = document.getElementById('csb5LutFields');
        var lutStartYear = document.getElementById('csb5LutStartYear');
        var lutEndYear = document.getElementById('csb5LutEndYear');
        var lutExpiry = document.getElementById('csb5LutExpiry');
        var submitBtn = document.getElementById('enableCsb5SubmitBtn');

        function showCsb5Error(msg) {
            if (!errorEl) {
                return;
            }
            if (!msg) {
                errorEl.classList.add('d-none');
                errorEl.textContent = '';
                return;
            }
            errorEl.classList.remove('d-none');
            errorEl.textContent = msg;
        }

        function toggleCsb5Sections() {
            var gstOn = !!(isGst && isGst.checked);
            var lutOn = !!(isLut && isLut.checked);
            if (gstFields) {
                gstFields.style.display = gstOn ? '' : 'none';
            }
            if (lutFields) {
                lutFields.style.display = lutOn ? '' : 'none';
            }
        }

        function syncLutEndYears() {
            if (!lutStartYear || !lutEndYear) {
                return;
            }
            var startYear = parseInt(lutStartYear.value, 10);
            lutEndYear.innerHTML = '';
            if (!startYear) {
                var opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Select Start Year First';
                lutEndYear.appendChild(opt);
                lutEndYear.disabled = true;
                if (lutExpiry) {
                    lutExpiry.value = '';
                }
                return;
            }
            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Select End Year';
            lutEndYear.appendChild(placeholder);
            for (var i = 1; i <= 5; i++) {
                var y = startYear + i;
                var o = document.createElement('option');
                o.value = String(y);
                o.textContent = String(y);
                lutEndYear.appendChild(o);
            }
            lutEndYear.disabled = false;
            lutEndYear.value = String(startYear + 1);
            syncLutExpiry();
        }

        function syncLutExpiry() {
            if (!lutEndYear || !lutExpiry) {
                return;
            }
            var endYear = parseInt(lutEndYear.value, 10);
            lutExpiry.value = endYear ? (endYear + '-03-31') : '';
        }

        if (isGst) {
            isGst.addEventListener('change', toggleCsb5Sections);
        }
        if (isLut) {
            isLut.addEventListener('change', toggleCsb5Sections);
        }
        if (lutStartYear) {
            lutStartYear.addEventListener('change', syncLutEndYears);
        }
        if (lutEndYear) {
            lutEndYear.addEventListener('change', syncLutExpiry);
        }
        toggleCsb5Sections();

        document.querySelectorAll('.enable-csb5-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-customer-id') || '';
                var company = btn.getAttribute('data-company') || '-';
                if (idEl) {
                    idEl.value = id;
                }
                if (nameEl) {
                    nameEl.textContent = company;
                }
                var template = form.getAttribute('data-action-template') || '';
                form.setAttribute('action', template.replace('__CUSTOMER_ID__', id));
                showCsb5Error('');
                form.reset();
                toggleCsb5Sections();
                if (lutEndYear) {
                    lutEndYear.innerHTML = '<option value="">Select Start Year First</option>';
                    lutEndYear.disabled = true;
                }
                if (lutExpiry) {
                    lutExpiry.value = '';
                }
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });
        });

        var iecInput = document.getElementById('csb5IecNumber');
        if (iecInput) {
            iecInput.addEventListener('input', function () {
                iecInput.value = iecInput.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 10);
            });
        }
        var adInput = document.getElementById('csb5AdCode');
        if (adInput) {
            adInput.addEventListener('input', function () {
                adInput.value = adInput.value.replace(/\D/g, '').slice(0, 14);
            });
        }
        var gstInput = document.getElementById('csb5GstNumber');
        if (gstInput) {
            gstInput.addEventListener('input', function () {
                gstInput.value = gstInput.value.replace(/\s+/g, '').toUpperCase().slice(0, 15);
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            showCsb5Error('');

            if (isGst && isLut && !isGst.checked && !isLut.checked) {
                showCsb5Error('Select GST, LUT, or both. At least one option is required.');
                return;
            }

            var originalHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
            }

            var formData = new FormData(form);
            var token = document.querySelector('meta[name="csrf-token"]');
            var csrf = token ? token.getAttribute('content') : '';

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(function (res) {
                    return res.text().then(function (text) {
                        var data = null;
                        try {
                            data = text ? JSON.parse(text) : null;
                        } catch (err) {
                            data = null;
                        }
                        return { ok: res.ok, status: res.status, data: data };
                    });
                })
                .then(function (result) {
                    var data = result.data || {};
                    if (result.ok && data.success) {
                        try {
                            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.hide();
                        } catch (err) {}
                        var id = idEl ? idEl.value : '';
                        var csbCell = id ? document.getElementById('csb-type-cell-' + id) : null;
                        if (csbCell) {
                            csbCell.innerHTML = '<span class="status-pill csb-v"><span class="dot"></span>CSB V</span>';
                        }
                        var gstLutCell = id ? document.getElementById('gst-lut-cell-' + id) : null;
                        if (gstLutCell) {
                            var badges = '';
                            if (data.is_gst) {
                                badges += '<span class="mini-chip gst"><i class="ti ti-receipt"></i>GST</span> ';
                            }
                            if (data.is_lut) {
                                badges += '<span class="mini-chip lut"><i class="ti ti-file-text"></i>LUT</span>';
                            }
                            gstLutCell.innerHTML = badges || '<span class="mini-chip na">-</span>';
                        }
                        var rowBtn = id ? document.querySelector('.enable-csb5-btn[data-customer-id="' + id + '"]') : null;
                        if (rowBtn) {
                            rowBtn.remove();
                        }
                        // Embedded View details ko bhi fresh rakho (nayi docs page reload par ayengi).
                        try {
                            var detailMap2 = window.exporterCustomersDetailMap || (window.exporterCustomersDetailMap = {});
                            var entry = detailMap2[id] || detailMap2[String(id)];
                            if (entry) {
                                var getVal = function (x) {
                                    var el = document.getElementById(x);
                                    return el ? el.value : '';
                                };
                                entry.csb_type = 'csb_v';
                                entry.csb_label = 'CSB V';
                                entry.is_gst = !!data.is_gst;
                                entry.is_lut = !!data.is_lut;
                                entry.ad_code = getVal('csb5AdCode');
                                entry.iec_number = getVal('csb5IecNumber');
                                entry.bank_account_number = getVal('csb5BankAccount');
                                entry.bank_type = getVal('csb5BankType');
                                entry.gst_certificate_number = getVal('csb5GstNumber');
                                entry.gst_business_name = getVal('csb5GstBusinessName');
                                entry.billing_address = getVal('csb5BillingAddress');
                                entry.billing_contact = getVal('csb5BillingContact');
                                entry.billing_email = getVal('csb5BillingEmail');
                                var lutS = getVal('csb5LutStartYear');
                                var lutEndEl = document.getElementById('csb5LutEndYear');
                                var lutE = lutEndEl ? lutEndEl.value : '';
                                entry.lut_bond_year = (lutS && lutE) ? (lutS + '-' + String(lutE).slice(-2)) : '';
                                var expRaw = getVal('csb5LutExpiry');
                                if (expRaw && expRaw.split('-').length === 3) {
                                    var p = expRaw.split('-');
                                    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                    entry.lut_expiry_date = parseInt(p[2], 10) + ' ' + months[parseInt(p[1], 10) - 1] + ' ' + p[0];
                                } else {
                                    entry.lut_expiry_date = expRaw;
                                }
                            }
                        } catch (e) {}
                        showAlert(data.message || 'Customer upgraded to CSB V successfully.', 'success');
                        return;
                    }
                    var msg = (data && data.message) ? data.message : 'Failed to enable CSB5. Please check your details.';
                    if (data && data.errors) {
                        var parts = [];
                        Object.keys(data.errors).forEach(function (k) {
                            var v = data.errors[k];
                            if (Array.isArray(v)) {
                                parts = parts.concat(v);
                            } else {
                                parts.push(v);
                            }
                        });
                        if (parts.length) {
                            msg = parts.join(' ');
                        }
                    }
                    if (result.status === 419) {
                        msg = 'Your session has expired. Please refresh the page and try again.';
                    }
                    showCsb5Error(msg);
                })
                .catch(function () {
                    showCsb5Error('A network error occurred. Please try again.');
                })
                .finally(function () {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }
                });
        });
    });
</script>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
<script>
    // View customer details modal: View button click par AJAX se details lao.
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('viewCustomerModal');
        if (!modalEl) {
            return;
        }
        var loadingEl = document.getElementById('viewCustomerLoading');
        var errorEl = document.getElementById('viewCustomerError');
        var contentEl = document.getElementById('viewCustomerContent');

        function val(v) {
            return (v === null || v === undefined || v === '') ? '-' : String(v);
        }
        function setText(id, v) {
            var el = document.getElementById(id);
            if (el) {
                el.textContent = val(v);
            }
        }

        function renderAddresses(list) {
            var box = document.getElementById('vcAddresses');
            if (!box) {
                return;
            }
            if (!list || !list.length) {
                box.innerHTML = '<div class="text-muted small">No address provided.</div>';
                return;
            }
            var html = '';
            list.forEach(function (addr, idx) {
                var lines = [addr.address_line2, addr.address_line3].filter(function (p) { return p && String(p).trim(); }).join(', ');
                html += '<div class="view-detail-row"><span class="label">' + (idx === 0 ? 'Primary' : 'Address ' + (idx + 1)) + '</span><span class="value">'
                    + escapeHtml(addr.address_line1 || '-') + (lines ? '<br><span class="fw-normal text-muted">' + escapeHtml(lines) + '</span>' : '')
                    + '<br><span class="fw-normal text-muted">' + escapeHtml((addr.city || '') + ', ' + (addr.state || '') + ' - ' + (addr.pincode || '')) + '</span></span></div>';
            });
            box.innerHTML = html;
        }

        function renderDocs(docs) {
            var box = document.getElementById('vcDocuments');
            if (!box) {
                return;
            }
            if (!docs || !docs.length) {
                box.innerHTML = '<div class="text-muted small">No documents uploaded.</div>';
                return;
            }
            var available = docs.filter(function (d) { return d.url; });
            if (!available.length) {
                box.innerHTML = '<div class="text-muted small">No documents uploaded.</div>';
                return;
            }
            var html = '';
            available.forEach(function (d) {
                html += '<a class="view-doc-link" href="' + d.url + '" target="_blank"><i class="ti ti-file"></i>' + escapeHtml(d.label) + ' <i class="ti ti-external-link"></i></a>';
            });
            box.innerHTML = html;
        }

        function fillViewCustomerDetail(data) {
            var initials = (String(data.company_name || '?').trim().charAt(0) || '?').toUpperCase();
            setText('vcAvatar', initials);
            setText('vcCompany', data.company_name);
            setText('vcContact', (data.contact_person || '-') + '  •  ' + (data.phone_number || '-'));
            setText('vcCsbPill', data.csb_label || '-');
            setText('vcTypePill', data.customer_type || 'Not specified');
            setText('vcContactPerson', data.contact_person);
            setText('vcPhone', data.phone_number);
            setText('vcEmail', data.email);
            setText('vcCustomerType', data.customer_type);
            setText('vcAddedOn', data.added_on);
            renderAddresses(data.addresses);
            setText('vcCsbType', data.csb_label);
            var gstLut = [];
            if (data.is_gst) {
                gstLut.push('GST');
            }
            if (data.is_lut) {
                gstLut.push('LUT');
            }
            setText('vcGstLut', gstLut.length ? gstLut.join(' + ') : (data.csb_type === 'csb_v' ? '-' : 'N/A'));
            setText('vcGstNumber', data.gst_certificate_number);
            setText('vcGstBusiness', data.gst_business_name);
            setText('vcAdCode', data.ad_code);
            setText('vcIec', data.iec_number);
            setText('vcBankAccount', data.bank_account_number);
            setText('vcBankType', data.bank_type ? String(data.bank_type).charAt(0).toUpperCase() + String(data.bank_type).slice(1) : null);
            setText('vcLutYear', data.lut_bond_year);
            setText('vcLutExpiry', data.lut_expiry_date);
            setText('vcKycType', data.kyc_type);
            setText('vcKycNumber', data.kyc_number);
            setText('vcPan', data.pan_number);
            setText('vcPanHolder', data.pan_holder_name);
            setText('vcPanDob', data.pan_dob);
            setText('vcBillingAddress', data.billing_address);
            setText('vcBillingContact', data.billing_contact);
            setText('vcBillingEmail', data.billing_email);
            renderDocs(data.documents);
        }

        document.querySelectorAll('.view-customer-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-customer-id');
                if (!id) {
                    return;
                }
                var originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

                if (loadingEl) {
                    loadingEl.style.display = '';
                }
                if (errorEl) {
                    errorEl.classList.add('d-none');
                    errorEl.textContent = '';
                }
                if (contentEl) {
                    contentEl.style.display = 'none';
                }
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();

                // Primary source: page me embedded details (network ki need nahi,
                // isliye host/subpath mismatch par bhi View hamesha kaam karega).
                var localMap = window.exporterCustomersDetailMap || {};
                var localData = localMap[id] || localMap[String(id)];
                if (localData) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    if (loadingEl) {
                        loadingEl.style.display = 'none';
                    }
                    fillViewCustomerDetail(localData);
                    if (contentEl) {
                        contentEl.style.display = '';
                    }
                    return;
                }

                // Fallback: server se fresh details (same-origin relative URL taaki
                // APP_URL host/subpath se mismatch par CORS fail na ho).
                var basePath = window.location.pathname.replace(/\/$/, '');
                var url = basePath + '/' + encodeURIComponent(id);
                var token = document.querySelector('meta[name="csrf-token"]');
                var csrf = token ? token.getAttribute('content') : '';

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    }
                })
                    .then(function (res) {
                        return res.text().then(function (text) {
                            var data = null;
                            try {
                                data = text ? JSON.parse(text) : null;
                            } catch (e) {
                                data = null;
                            }
                            return { ok: res.ok, status: res.status, data: data };
                        });
                    })
                    .then(function (result) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        if (loadingEl) {
                            loadingEl.style.display = 'none';
                        }
                        var data = (result.data && result.data.data) ? result.data.data : null;
                        if (result.ok && result.data && result.data.success && data) {
                            // Fresh server data ko embedded map me bhi sync karo.
                            try {
                                var detailMap = window.exporterCustomersDetailMap || (window.exporterCustomersDetailMap = {});
                                detailMap[data.id] = data;
                            } catch (e) {}
                            fillViewCustomerDetail(data);
                            if (contentEl) {
                                contentEl.style.display = '';
                            }
                            return;
                        }
                        var msg = (result.data && result.data.message) ? result.data.message : ('Failed to load customer details (HTTP ' + result.status + '). Please try again.');
                        if (errorEl) {
                            errorEl.textContent = msg;
                            errorEl.classList.remove('d-none');
                        }
                    })
                    .catch(function (err) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        if (loadingEl) {
                            loadingEl.style.display = 'none';
                        }
                        var detail = '';
                        try {
                            detail = (err && err.message) ? ' (' + err.message + ')' : '';
                        } catch (e) {}
                        try {
                            console.error('[ViewCustomer] details fetch failed:', err);
                        } catch (e) {}
                        if (errorEl) {
                            errorEl.textContent = 'Could not load details' + detail + '. Please check your connection and try again.';
                            errorEl.classList.remove('d-none');
                        }
                    });
            });
        });
    });
</script>
<script>
    // DataTable for View All Customers (same UX as admin/all-customer page).
    (function initExporterCustomersTable() {
        function boot() {
            if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.DataTable === 'undefined') {
                return;
            }
            var $table = window.jQuery('#exporterCustomersTable');
            if (!$table.length || window.jQuery.fn.DataTable.isDataTable($table)) {
                return;
            }
            var dt = $table.DataTable({
                order: [[0, 'asc']],
                pageLength: 10,
                language: {
                    search: 'Search Customers:',
                    lengthMenu: 'Show _MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    emptyTable: 'No customers found.',
                },
                columnDefs: [
                    { orderable: false, targets: [8] }
                ]
            });
            // Hidden tab pane me width 0 hoti hai — tab open par adjust karo.
            var viewTab = document.getElementById('view-customers-tab');
            if (viewTab) {
                viewTab.addEventListener('shown.bs.tab', function () {
                    try {
                        dt.columns.adjust().draw(false);
                    } catch (e) {}
                });
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(boot, 100);
            });
        } else {
            setTimeout(boot, 100);
        }
    })();
</script>
</body>
</html>
