<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Export Customers of {{ $customer->first_name }} {{ $customer->last_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="robots" content="index, follow">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

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

    <style>
        /* ---------------------------------------------------------------
           Profile header (gradient banner)
        --------------------------------------------------------------- */
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
        .customer-avatar {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
            flex-shrink: 0;
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
        .back-pill {
            background: rgba(255, 255, 255, 0.94);
            color: #1d4ed8;
            border: 0;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 999px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .back-pill:hover {
            background: #fff;
            color: #1e3a8a;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
        }

        /* ---------------------------------------------------------------
           Stat tiles
        --------------------------------------------------------------- */
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

        /* ---------------------------------------------------------------
           Data card + table
        --------------------------------------------------------------- */
        .data-card {
            border: 1px solid #eef2f7;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 10px 24px -18px rgba(16, 24, 40, 0.12);
        }
        #exportCustomersDetailTable {
            width: 100%;
            min-width: 1600px; /* wide enough to avoid cramping -> wrapper gets horizontal scroll */
            max-width: none;
        }
        #exportCustomersDetailTable thead th {
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
        #exportCustomersDetailTable thead th .ti {
            font-size: 15px;
            vertical-align: -2px;
        }
        #exportCustomersDetailTable tbody td {
            padding: 13px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
            color: #334155;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        #exportCustomersDetailTable tbody tr:last-child td {
            border-bottom: 0;
        }
        #exportCustomersDetailTable tbody tr:hover td {
            background: #f8fafc;
        }
        /* preferred column widths (auto layout + horizontal scroll) */
        #exportCustomersDetailTable th:nth-child(1) { min-width: 60px; }
        #exportCustomersDetailTable th:nth-child(2) { min-width: 260px; }
        #exportCustomersDetailTable th:nth-child(3) { min-width: 340px; }
        #exportCustomersDetailTable th:nth-child(4) { min-width: 460px; }
        #exportCustomersDetailTable th:nth-child(5) { min-width: 180px; }
        #exportCustomersDetailTable th:nth-child(6) { min-width: 180px; }
        #exportCustomersDetailTable th:nth-child(7) { min-width: 110px; }
        #exportCustomersDetailTable tbody td:nth-child(7) { white-space: nowrap; }
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

        /* Company cell */
        .company-avatar {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.4px;
            flex-shrink: 0;
        }
        .company-name {
            font-weight: 600;
            color: #0f172a;
            line-height: 1.3;
        }
        .cat-chip {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 600;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 2px 9px;
            border-radius: 999px;
            border: 1px solid #dbeafe;
        }
        .ad-chip {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 600;
            background: #fffbeb;
            color: #b45309;
            padding: 2px 9px;
            border-radius: 999px;
            border: 1px solid #fef3c7;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }

        /* Added On cell */
        .added-date-cell {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .added-date-cell > .ti {
            font-size: 16px;
            color: #2563eb;
            flex-shrink: 0;
        }
        .added-date-main {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            line-height: 1.3;
            white-space: nowrap;
        }
        .added-date-sub {
            font-size: 11.5px;
            color: #94a3b8;
            margin-top: 1px;
            white-space: nowrap;
        }
        .contact-link-row {
            margin-top: 6px;
        }
        .contact-link-row .link-cell span {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /* Phone / Email links */
        .link-cell {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #334155;
            text-decoration: none;
            font-size: 13px;
            line-height: 1.35;
        }
        .link-cell .ti {
            font-size: 16px;
            color: #2563eb;
            flex-shrink: 0;
        }
        .link-cell:hover {
            color: #1d4ed8;
        }

        /* Address blocks */
        .address-block {
            position: relative;
            background: #fff;
            border: 1px solid #eef2f7;
            border-left: 3px solid #93c5fd;
            border-radius: 10px;
            padding: 9px 11px 9px 12px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .address-block:last-child {
            margin-bottom: 0;
        }
        .address-block.is-primary {
            border-left-color: #2563eb;
            background: #f8fbff;
        }
        .address-tag {
            position: absolute;
            right: 10px;
            top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 1px 9px;
            border-radius: 999px;
        }
        .address-tag.muted {
            background: #f1f5f9;
            color: #64748b;
            border-color: #e2e8f0;
        }
        .address-pin {
            color: #2563eb;
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .address-block.is-primary .address-pin {
            color: #1d4ed8;
        }
        .address-body {
            flex: 1;
            min-width: 0;
            padding-right: 66px;
            white-space: normal;
        }
        .address-line1 {
            font-weight: 600;
            color: #0f172a;
            font-size: 13px;
            line-height: 1.4;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .address-sub {
            color: #64748b;
            font-size: 12px;
            line-height: 1.45;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /* KYC badges */
        .kyc-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.2px;
            padding: 4px 13px;
            border-radius: 999px;
            line-height: 1.5;
        }
        .kyc-badge .ti {
            font-size: 13px;
        }
        .kyc-aadhar { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .kyc-pan    { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .kyc-gst    { background: #f3e8ff; color: #7c3aed; border: 1px solid #e9d5ff; }
        .kyc-na     { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        .kyc-number {
            display: inline-block;
            margin-top: 6px;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 11px;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e9eef5;
            border-radius: 6px;
            padding: 2px 9px;
            word-break: break-all;
        }

        /* ---------------------------------------------------------------
           DataTables chrome
        --------------------------------------------------------------- */
        #exportCustomersDetailTable_wrapper .dt-search input,
        #exportCustomersDetailTable_wrapper .dt-length select {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 7px 13px;
            font-size: 13px;
            color: #334155;
            background: #fff;
            outline: none;
        }
        #exportCustomersDetailTable_wrapper .dt-search input:focus,
        #exportCustomersDetailTable_wrapper .dt-length select:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }
        #exportCustomersDetailTable_wrapper .dt-search {
            margin-bottom: 14px;
        }
        #exportCustomersDetailTable_wrapper .dt-info {
            color: #64748b;
            font-size: 12.5px;
            padding-top: 8px;
        }
        #exportCustomersDetailTable_wrapper .dt-paging {
            padding-top: 8px;
        }
        #exportCustomersDetailTable_wrapper .dt-paging .dt-paging-button {
            border-radius: 9px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #475569;
            margin: 0 2px;
            font-size: 13px;
            min-width: 34px;
            padding: 4px 9px;
        }
        #exportCustomersDetailTable_wrapper .dt-paging .dt-paging-button:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }
        #exportCustomersDetailTable_wrapper .dt-paging .dt-paging-button.current {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
            font-weight: 600;
        }
        #exportCustomersDetailTable_wrapper .dt-paging .dt-paging-button.disabled {
            color: #cbd5e1;
            background: #fff;
            border-color: #eef2f7;
        }
        .min-w-0 { min-width: 0; }
    </style>
</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        @include('admin.partials.header')

        <!-- Search Modal -->
        <div class="modal fade" id="searchModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-transparent">
                    <div class="card shadow-none mb-0">
                        <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                            <i class="ti ti-search fs-22"></i>
                            <input type="search" class="form-control border-0" placeholder="Search">
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.partials.sidebar')

        <!-- ========================
            Start Page Content
        ========================= -->

        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content pb-0">

                @php
                    $customerInitials = strtoupper(
                        mb_substr(trim((string) $customer->first_name), 0, 1) .
                        mb_substr(trim((string) $customer->last_name), 0, 1)
                    );
                    if ($customerInitials === '') {
                        $customerInitials = 'U';
                    }
                    $customerUserType = strtolower((string) ($customer->businessCategory->user_type ?? ''));
                    $customerCategoryName = (string) ($customer->businessCategory->category_name ?? '');

                    // KYC document resolver: exporter_customers.kyc_type stores the actual
                    // document label used ('Aadhar Card' / 'PAN Card' / 'GST (Normal)').
                    $kycBadgeFor = function ($ec) {
                        $k = strtolower((string) ($ec->kyc_type ?? ''));
                        if (str_contains($k, 'aadhar')) return 'aadhar';
                        if (str_contains($k, 'pan')) return 'pan';
                        if (str_contains($k, 'gst')) return 'gst';
                        // Fallback inference from dedicated columns when kyc_type is blank
                        if (! empty($ec->gst_certificate_number)) return 'gst';
                        if (! empty($ec->pan_number)) return 'pan';
                        if (! empty($ec->aadhar_front_document) || ! empty($ec->aadhar_back_document)) return 'aadhar';
                        return 'na';
                    };
                    $kycDocMap = [
                        'aadhar' => ['label' => 'Aadhar Card',  'icon' => 'ti-id-badge', 'class' => 'kyc-aadhar'],
                        'pan'    => ['label' => 'PAN Card',     'icon' => 'ti-file-text', 'class' => 'kyc-pan'],
                        'gst'    => ['label' => 'GST (Normal)', 'icon' => 'ti-building', 'class' => 'kyc-gst'],
                    ];
                    $totalExporters = $exporterCustomers->count();
                    $kycCompletedCount = $exporterCustomers->filter(fn ($ec) => $kycBadgeFor($ec) !== 'na' || ! empty($ec->kyc_number))->count();
                    $kycPendingCount = $totalExporters - $kycCompletedCount;
                    $addressTotal = $exporterCustomers->reduce(function ($carry, $ec) {
                        return $carry + count($ec->displayAddresses());
                    }, 0);

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

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-check me-2"></i>
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <!-- ============ Profile / gradient header ============ -->
                        <div class="card customer-profile-card mb-3">
                            <div class="card-body position-relative" style="z-index:1;">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="customer-avatar">{{ $customerInitials }}</span>
                                        <div>
                                            <div class="customer-name">
                                                {{ $customer->first_name }} {{ $customer->last_name }}
                                            </div>
                                            <div class="customer-sub mt-1">
                                                {{ $customer->first_name }} {{ $customer->last_name }}'s sub customers master of this account
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.export-customers') }}" class="back-pill" title="Back to all customers">
                                        <i class="ti ti-arrow-left"></i>Back to Customers
                                    </a>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    @if($customer->customer_code)
                                        <span class="meta-pill"><i class="ti ti-hash"></i><b>{{ $customer->customer_code }}</b></span>
                                    @endif
                                    @if($customerCategoryName)
                                        <span class="meta-pill"><i class="ti ti-tag"></i>{{ $customerCategoryName }}</span>
                                    @endif
                                    @if($customerUserType === 'business')
                                        <span class="meta-pill"><i class="ti ti-briefcase"></i>Business Account</span>
                                    @elseif($customerUserType === 'personal')
                                        <span class="meta-pill"><i class="ti ti-user"></i>Personal Account</span>
                                    @endif
                                    @if($customer->email)
                                        <span class="meta-pill"><i class="ti ti-mail"></i>{{ $customer->email }}</span>
                                    @endif
                                    @if($customer->phone_number)
                                        <span class="meta-pill"><i class="ti ti-phone"></i>{{ $customer->phone_number }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ Stat tiles ============ -->
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-xl-4">
                        <div class="stat-card">
                            <span class="stat-icon blue"><i class="ti ti-package"></i></span>
                            <div>
                                <div class="stat-value">{{ $totalExporters }}</div>
                                <div class="stat-label">Total Sub Customers</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="stat-card">
                            <span class="stat-icon violet"><i class="ti ti-shield-check"></i></span>
                            <div>
                                <div class="stat-value">{{ $kycCompletedCount }}</div>
                                <div class="stat-label">KYC Completed</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="stat-card">
                            <span class="stat-icon sky"><i class="ti ti-shield-off"></i></span>
                            <div>
                                <div class="stat-value">{{ $kycPendingCount }}</div>
                                <div class="stat-label">KYC Not Added</div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <span class="stat-icon blue"><i class="ti ti-map-pin"></i></span>
                            <div>
                                <div class="stat-value">{{ $addressTotal }}</div>
                                <div class="stat-label">Saved Addresses</div>
                            </div>
                        </div>
                    </div> -->
                </div>

                <!-- ============ Data card / table ============ -->
                <div class="row">
                    <div class="col-12">
                        <div class="card data-card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="exportCustomersDetailTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><i class="ti ti-building me-1"></i>Company</th>
                                                <th><i class="ti ti-user me-1"></i>Contact Person</th>
                                                <th><i class="ti ti-map-pin me-1"></i>Address</th>
                                                <th><i class="ti ti-calendar me-1"></i>Added On</th>
                                                <th><i class="ti ti-shield-check me-1"></i>KYC</th>
                                                <th><i class="ti ti-eye me-1"></i>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($exporterCustomers as $key => $exportCustomer)
                                                @php
                                                    $companyName = (string) ($exportCustomer->company_name ?? '');
                                                    $companyWords = preg_split('/\s+/', trim($companyName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
                                                    $companyInitials = strtoupper(
                                                        (string) mb_substr($companyWords[0] ?? '', 0, 1) .
                                                        (string) mb_substr($companyWords[1] ?? ($companyWords[0] ?? ''), 0, 1)
                                                    );
                                                    if ($companyInitials === '') {
                                                        $companyInitials = '?';
                                                    }
                                                    $companyGradient = $avatarGradients[
                                                        abs(crc32($companyName !== '' ? $companyName : (string) $exportCustomer->id))
                                                        % count($avatarGradients)
                                                    ];
                                                    $kycRaw = trim((string) ($exportCustomer->kyc_type ?? ''));
                                                    $kycBadgeKey = $kycBadgeFor($exportCustomer);
                                                    $kycInfo = $kycBadgeKey !== 'na' ? ($kycDocMap[$kycBadgeKey] ?? null) : null;
                                                    $kycNumberDisplay = (string) ($exportCustomer->kyc_number ?? '');
                                                    if ($kycBadgeKey === 'pan' && ! empty($exportCustomer->pan_number)) {
                                                        $kycNumberDisplay = (string) $exportCustomer->pan_number;
                                                    }
                                                    if ($kycBadgeKey === 'gst' && ! empty($exportCustomer->gst_certificate_number)) {
                                                        $kycNumberDisplay = (string) $exportCustomer->gst_certificate_number;
                                                    }
                                                    $phoneDigits = preg_replace('/[^0-9+]/', '', (string) ($exportCustomer->phone_number ?? ''));
                                                    $displayAddresses = $exportCustomer->displayAddresses();
                                                @endphp
                                                <tr>
                                                    <td><span class="row-index">{{ $key + 1 }}</span></td>
                                                    <td>
                                                        <div class="d-flex align-items-start gap-2">
                                                            <span class="company-avatar" style="background: {{ $companyGradient }};">{{ $companyInitials }}</span>
                                                            <div class="min-w-0">
                                                                <div class="company-name">{{ $companyName !== '' ? $companyName : '—' }}</div>
                                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                                    @if($exportCustomer->businessCategory)
                                                                        <span class="cat-chip">{{ $exportCustomer->businessCategory->category_name ?? 'Exporter' }}</span>
                                                                    @endif
                                                                    @if($exportCustomer->ad_code)
                                                                        <span class="ad-chip">{{ $exportCustomer->ad_code }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-medium">{{ $exportCustomer->contact_person ?? '—' }}</div>
                                                        @if($exportCustomer->phone_number)
                                                            <div class="contact-link-row">
                                                                <a href="tel:{{ $phoneDigits }}" class="link-cell">
                                                                    <i class="ti ti-phone"></i><span>{{ $exportCustomer->phone_number }}</span>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if($exportCustomer->email)
                                                            <div class="contact-link-row">
                                                                <a href="mailto:{{ $exportCustomer->email }}" class="link-cell">
                                                                    <i class="ti ti-mail"></i><span>{{ $exportCustomer->email }}</span>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $primaryAddress = null;
                                                            $isPrimaryAddress = false;
                                                            foreach ($displayAddresses as $candidateAddress) {
                                                                if (! empty($candidateAddress['is_primary'])) {
                                                                    $primaryAddress = $candidateAddress;
                                                                    $isPrimaryAddress = true;
                                                                    break;
                                                                }
                                                            }
                                                            // Fallback: agar koi primary address na ho toh pehla available dikhao
                                                            if (! $primaryAddress && ! empty($displayAddresses)) {
                                                                $primaryAddress = $displayAddresses[0];
                                                            }
                                                        @endphp
                                                        @if($primaryAddress)
                                                            @php
                                                                $extraLines = collect([$primaryAddress['address_line2'], $primaryAddress['address_line3']])->filter();
                                                                $cityFull = trim(trim((string) $primaryAddress['city'] . ', ' . (string) $primaryAddress['state'], ', '));
                                                                if (! empty($primaryAddress['pincode'])) {
                                                                    $cityFull = $cityFull !== '' ? $cityFull . ' - ' . (string) $primaryAddress['pincode'] : (string) $primaryAddress['pincode'];
                                                                }
                                                                $addrParts = [
                                                                    (string) ($primaryAddress['address_line1'] ?? ''),
                                                                    $extraLines->implode(', '),
                                                                    $cityFull,
                                                                ];
                                                                // Keep rows compact: show max 50 chars per line, add '…'
                                                                $addrShort = array_map(function ($text) {
                                                                    $text = trim((string) $text);
                                                                    return mb_strlen($text) > 50 ? mb_substr($text, 0, 50) . '…' : $text;
                                                                }, $addrParts);
                                                            @endphp
                                                            <div class="address-block {{ $isPrimaryAddress ? 'is-primary' : '' }}">
                                                                @if($isPrimaryAddress)
                                                                    <span class="address-tag">
                                                                        <i class="ti ti-star"></i> Primary
                                                                    </span>
                                                                @endif
                                                                <div class="d-flex align-items-start gap-1">
                                                                    <i class="ti ti-map-pin address-pin"></i>
                                                                    <div class="address-body">
                                                                        <div class="address-line1" @if($addrParts[0] !== $addrShort[0]) title="{{ $addrParts[0] }}" @endif>{{ $addrShort[0] !== '' ? $addrShort[0] : '—' }}</div>
                                                                        @if($extraLines->isNotEmpty())
                                                                            <div class="address-sub mt-1" @if($addrParts[1] !== $addrShort[1]) title="{{ $addrParts[1] }}" @endif>{{ $addrShort[1] }}</div>
                                                                        @endif
                                                                        <div class="address-sub" @if($addrParts[2] !== $addrShort[2]) title="{{ $addrParts[2] }}" @endif>
                                                                            {{ $addrShort[2] !== '' ? $addrShort[2] : '—' }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="text-muted small py-1">No address provided.</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($exportCustomer->created_at)
                                                            <div class="added-date-cell">
                                                                <i class="ti ti-calendar"></i>
                                                                <div>
                                                                    <div class="added-date-main">{{ $exportCustomer->created_at->format('d M Y') }}</div>
                                                                    <div class="added-date-sub">{{ $exportCustomer->created_at->format('h:i A') }}</div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($kycInfo)
                                                            <span class="kyc-badge {{ $kycInfo['class'] }}"><i class="ti {{ $kycInfo['icon'] }}"></i>{{ $kycInfo['label'] }}</span>
                                                        @elseif($kycRaw !== '')
                                                            <span class="kyc-badge kyc-na"><i class="ti ti-question-mark"></i>{{ $kycRaw }}</span>
                                                        @else
                                                            <span class="kyc-badge kyc-na"><i class="ti ti-shield-off"></i>Not set</span>
                                                        @endif
                                                        @if($kycNumberDisplay !== '')
                                                            <div class="kyc-number">{{ $kycNumberDisplay }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.export-customers.view', $exportCustomer->id) }}"
                                                           class="btn btn-sm btn-primary"
                                                           title="View full profile & documents of {{ $companyName !== '' ? $companyName : 'this exporter customer' }}">
                                                            <i class="ti ti-eye me-1"></i>View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">No exporter customers found for this user.</td>
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
            <!-- End Content -->

        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Wrapper -->

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('assets/plugins/slimscroll/slimscroll.min.js') }}" type="text/javascript"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Daterangepicker JS -->
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#exportCustomersDetailTable').DataTable({
                order: [[0, 'asc']],
                autoWidth: false,
                pageLength: 25,
                language: {
                    search: "",
                    searchPlaceholder: "Search exporter customers…",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No exporter customers found.",
                }
            });
        });
    </script>

</body>
</html>
