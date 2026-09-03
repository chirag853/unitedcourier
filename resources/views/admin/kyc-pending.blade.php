<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - KYC Pending</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
        /* ============================================================
           KYC Pending — Polished UI
           ============================================================ */
        :root {
            --uwc-primary: #2563eb;
            --uwc-indigo: #6d28d9;
            --uwc-success: #16a34a;
            --uwc-danger: #dc2626;
            --uwc-amber: #d97706;
            --uwc-sky: #0284c7;
            --uwc-slate: #64748b;
            --uwc-border: #e2e8f0;
            --uwc-soft: #f1f5f9;
        }

        /* ---- Page header banner ---- */
        .kyc-hero {
            background: linear-gradient(120deg, #1e3a8a 0%, #2563eb 55%, #6d28d9 100%);
            border-radius: 16px;
            padding: 26px 28px;
            margin-bottom: 22px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 30px -12px rgba(37, 99, 235, .55);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            color: #fff;
        }
        .kyc-hero::before,
        .kyc-hero::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }
        .kyc-hero::before {
            width: 260px;
            height: 260px;
            right: -70px;
            top: -110px;
        }
        .kyc-hero::after {
            width: 160px;
            height: 160px;
            right: 130px;
            bottom: -90px;
        }
        .kyc-hero .hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-right: 14px;
            flex: 0 0 auto;
        }
        .kyc-hero h4 {
            font-weight: 700;
            letter-spacing: .2px;
            margin: 0;
        }
        .kyc-hero p {
            margin: 2px 0 0;
            opacity: .85;
            font-size: 13px;
            max-width: 620px;
        }
        .kyc-hero .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            position: relative;
            z-index: 1;
        }
        .btn-export {
            background: rgba(255, 255, 255, .14);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .35);
            font-size: 13px;
            font-weight: 600;
            padding: 9px 16px;
            border-radius: 10px;
            transition: all .2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            backdrop-filter: blur(4px);
        }
        .btn-export:hover {
            background: #fff;
            color: #1d4ed8;
            border-color: #fff;
            transform: translateY(-1px);
        }
        .btn-refresh {
            background: rgba(255, 255, 255, .14);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .35);
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
            text-decoration: none;
        }
        .btn-refresh:hover {
            background: #fff;
            color: #1d4ed8;
            transform: rotate(90deg);
        }

        /* ---- Summary stat cards ---- */
        .stat-card {
            border: 1px solid var(--uwc-border);
            border-radius: 14px;
            background: #fff;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 4px 14px -8px rgba(15, 23, 42, .12);
            transition: all .2s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -10px rgba(15, 23, 42, .18);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            flex: 0 0 auto;
        }
        .stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .stat-icon.amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.red { background: linear-gradient(135deg, #f87171, #dc2626); }
        .stat-meta .stat-label {
            font-size: 12px;
            color: var(--uwc-slate);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
        }
        .stat-meta .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        /* ---- Main card ---- */
        .kyc-main-card {
            border: 1px solid var(--uwc-border);
            border-radius: 16px;
            box-shadow: 0 6px 20px -12px rgba(15, 23, 42, .14);
            overflow: hidden;
        }
        .kyc-main-card .card-header {
            background: #fff;
            border-bottom: 1px solid var(--uwc-border);
            padding: 18px 22px;
        }
        .kyc-main-card .card-title {
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ---- Tabs ---- */
        .kyc-tabs {
            border-bottom: 1px solid var(--uwc-border);
            padding: 0 6px;
            gap: 6px;
        }
        .kyc-tabs .nav-link {
            border: 0;
            border-radius: 10px 10px 0 0;
            color: var(--uwc-slate);
            font-weight: 600;
            font-size: 14px;
            padding: 12px 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            transition: color .15s;
        }
        .kyc-tabs .nav-link:hover {
            color: var(--uwc-primary);
        }
        .kyc-tabs .nav-link.active {
            color: var(--uwc-primary);
            background: transparent;
        }
        .kyc-tabs .nav-link.active::after {
            content: "";
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: -1px;
            height: 3px;
            border-radius: 3px 3px 0 0;
            background: linear-gradient(90deg, #2563eb, #6d28d9);
        }
        .kyc-tab-badge {
            background: var(--uwc-primary);
            color: #fff;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 700;
            min-width: 24px;
            text-align: center;
        }
        .kyc-tab-badge-amber {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }
        .tab-card-kyc {
            padding: 8px 0 4px;
        }

        /* ---- Tables ---- */
        .kyc-table {
            margin-bottom: 0;
            font-size: 13.5px;
        }
        .kyc-table thead th {
            background: #f8fafc;
            color: var(--uwc-slate);
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid var(--uwc-border);
            padding: 12px 16px;
            white-space: nowrap;
        }
        .kyc-table tbody td {
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            vertical-align: middle;
        }
        .kyc-table tbody tr {
            transition: background .15s;
        }
        .kyc-table tbody tr:hover {
            background: #f8faff;
        }
        .kyc-table tbody tr:last-child td {
            border-bottom: 0;
        }

        /* ---- Customer cell with avatar ---- */
        .customer-cell {
            min-width: 225px;
        }
        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: .5px;
            flex: 0 0 auto;
            box-shadow: 0 3px 8px -3px rgba(15, 23, 42, .35);
        }
        .avatar-grad-0 { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .avatar-grad-1 { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .avatar-grad-2 { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .avatar-grad-3 { background: linear-gradient(135deg, #10b981, #059669); }
        .avatar-grad-4 { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .avatar-grad-5 { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .avatar-grad-6 { background: linear-gradient(135deg, #f43f5e, #e11d48); }
        .avatar-grad-7 { background: linear-gradient(135deg, #6366f1, #4f46e5); }

        .customer-name {
            font-weight: 700;
            color: #0f172a;
            display: block;
            line-height: 1.2;
        }
        .customer-sub {
            color: var(--uwc-slate);
            font-size: 12px;
            display: block;
            line-height: 1.35;
        }
        .customer-sub a {
            color: var(--uwc-slate);
            text-decoration: none;
        }
        .customer-sub a:hover {
            color: var(--uwc-primary);
        }

        /* ---- Badges / pills ---- */
        .kyc-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .2px;
            white-space: nowrap;
        }
        .kyc-badge.business {
            background: #ecfeff;
            color: #0e7490;
            border: 1px solid #a5f3fc;
        }
        .kyc-badge.personal {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .kyc-badge.verified {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .kyc-badge.rejected {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .kyc-badge.pending {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .kyc-badge.under-review {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            flex: 0 0 auto;
        }
        .status-dot.green { background: #16a34a; box-shadow: 0 0 0 3px rgba(22, 163, 74, .15); }
        .status-dot.red { background: #dc2626; box-shadow: 0 0 0 3px rgba(220, 38, 38, .15); }
        .status-dot.amber { background: #d97706; box-shadow: 0 0 0 3px rgba(217, 119, 6, .15); }

        .date-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            border: 1px solid var(--uwc-border);
            color: #334155;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .date-badge .ti {
            font-size: 14px;
            color: var(--uwc-slate);
        }

        .org-cell {
            min-width: 170px;
        }
        .org-name {
            font-weight: 600;
            color: #0f172a;
            display: block;
            line-height: 1.25;
        }
        .org-sub {
            color: var(--uwc-slate);
            font-size: 12px;
        }

        /* ---- Account status pill ---- */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.5;
            white-space: nowrap;
        }
        .status-pill.active {
            background: #dcfce7;
            color: #15803d;
        }
        .status-pill.inactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* ---- Action buttons ---- */
        .action-cell {
            min-width: 235px;
        }
        .action-cell .d-flex {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .action-cell .d-flex form {
            display: contents;
        }
        .icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            border: 1px solid transparent;
            transition: all .18s;
            flex: 0 0 auto;
            text-decoration: none;
            cursor: pointer;
            line-height: 1;
        }
        .icon-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -6px rgba(15, 23, 42, .3);
        }
        .icon-btn.view {
            background: #f5f3ff;
            color: #6d28d9;
            border-color: #ede9fe;
        }
        .icon-btn.view:hover {
            background: #ede9fe;
            color: #5b21b6;
        }
        .icon-btn.approve {
            background: #ecfdf5;
            color: #16a34a;
            border-color: #bbf7d0;
        }
        .icon-btn.approve:hover {
            background: #d1fae5;
            color: #15803d;
        }
        .icon-btn.reject {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }
        .icon-btn.reject:hover {
            background: #fecaca;
            color: #b91c1c;
        }
        .icon-btn.deactivate {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fecaca;
        }
        .icon-btn.deactivate:hover {
            background: #fecaca;
            color: #991b1b;
        }
        .icon-btn.activate {
            background: #dcfce7;
            color: #15803d;
            border-color: #bbf7d0;
        }
        .icon-btn.activate:hover {
            background: #bbf7d0;
            color: #14532d;
        }

        .btn-follow-up {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            font-size: 12.5px;
            font-weight: 700;
            padding: 7px 14px;
            border-radius: 999px;
            white-space: nowrap;
            transition: all .18s;
            text-decoration: none;
            flex: 0 0 auto;
        }
        .btn-follow-up:hover {
            background: #0369a1;
            color: #fff;
            border-color: #0369a1;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -6px rgba(3, 105, 161, .5);
        }
        .action-cell-compact {
            min-width: 200px;
        }

        /* ---- Incomplete: progress / notes ---- */
        .progress-cell {
            min-width: 190px;
        }
        .progress-track {
            height: 6px;
            background: #eef2f7;
            border-radius: 999px;
            overflow: hidden;
            width: 120px;
        }
        .progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #2563eb, #6d28d9);
            transition: width .4s ease;
        }
        .progress-meta {
            margin-top: 5px;
        }
        .progress-meta .step-count {
            font-weight: 700;
            color: #0f172a;
            font-size: 12.5px;
        }
        .progress-labels {
            display: block;
            color: var(--uwc-slate);
            font-size: 11.5px;
            line-height: 1.4;
        }
        .rejected-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }
        .incomplete-note {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--uwc-slate);
            font-size: 12.5px;
            font-weight: 600;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 5px 11px;
            border-radius: 8px;
        }

        /* ---- Incomplete: saved details chips ---- */
        .draft-details-cell {
            min-width: 240px;
        }
        .draft-details {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        .draft-detail {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f8fafc;
            border: 1px solid var(--uwc-border);
            border-radius: 8px;
            padding: 3px 9px;
            font-size: 11.5px;
            color: #334155;
            white-space: nowrap;
        }
        .draft-detail .ti {
            font-size: 12px;
            color: var(--uwc-primary);
        }
        .draft-detail .dd-label {
            color: var(--uwc-slate);
            font-weight: 500;
        }
        .draft-detail strong {
            color: #0f172a;
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 11px;
        }
        .draft-detail.ok .ti {
            color: var(--uwc-success);
        }

        /* ---- DataTables refinements ---- */
        .dataTables_wrapper .dt-layout-row {
            padding: 10px 16px 4px;
        }
        .dataTables_wrapper .dt-search input,
        .dataTables_wrapper .dt-length select {
            border-radius: 8px;
            border-color: var(--uwc-border);
            font-size: 13px;
            padding: 4px 10px;
        }
        .dataTables_wrapper .dt-info,
        .dataTables_wrapper .dt-length label {
            color: var(--uwc-slate);
            font-size: 12.5px;
        }

        /* ---- Reject modal ---- */
        #rejectKycModal .modal-content {
            border: 0;
            border-radius: 16px;
        }
        #rejectKycModal .modal-header {
            border-bottom: 1px solid #f1f5f9;
        }
        #rejectKycModal .modal-footer {
            border-top: 1px solid #f1f5f9;
        }
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
                    $pendingKycCount    = count($kycDetails);
                    $incompleteCount    = $incompleteKycItems->count();
                    $draftStartedCount  = $incompleteKycItems->where('has_draft', true)->count();
                    $neverStartedCount  = $incompleteCount - $draftStartedCount;
                @endphp

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-check me-2"></i>
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- ============ Hero banner ============ -->
                <div class="kyc-hero">
                    <div class="d-flex align-items-center">
                        <span class="hero-icon"><i class="ti ti-shield-check"></i></span>
                        <div>
                            <h4 class="mb-0" style="color:white">KYC Management</h4>
                            <p>Review pending KYC verifications, track drafts and follow up with customers who have not completed their KYC yet.</p>
                        </div>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.kyc-export', ['status' => 'pending']) }}" class="btn-export" title="Export pending KYC records to Excel">
                            <i class="ti ti-file-spreadsheet me-1"></i>Export Pending
                        </a>
                        <a href="{{ route('admin.kyc-export', ['status' => 'all']) }}" class="btn-export" title="Export all KYC records to Excel">
                            <i class="ti ti-download me-1"></i>Export All
                        </a>
                        <a href="javascript:void(0);" class="btn-refresh" title="Refresh" onclick="location.reload();">
                            <i class="ti ti-refresh"></i>
                        </a>
                    </div>
                </div>

                <!-- ============ Summary stat cards ============ -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card">
                            <span class="stat-icon blue"><i class="ti ti-user-check"></i></span>
                            <div class="stat-meta">
                                <div class="stat-value">{{ $pendingKycCount }}</div>
                                <div class="stat-label">Pending Review</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card">
                            <span class="stat-icon amber"><i class="ti ti-alert-circle"></i></span>
                            <div class="stat-meta">
                                <div class="stat-value">{{ $incompleteCount }}</div>
                                <div class="stat-label">Incomplete KYC</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card">
                            <span class="stat-icon purple"><i class="ti ti-edit"></i></span>
                            <div class="stat-meta">
                                <div class="stat-value">{{ $draftStartedCount }}</div>
                                <div class="stat-label">Drafts In Progress</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card">
                            <span class="stat-icon red"><i class="ti ti-user-off"></i></span>
                            <div class="stat-meta">
                                <div class="stat-value">{{ $neverStartedCount }}</div>
                                <div class="stat-label">Need to be Initiate</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ Main card with two tiles ============ -->
                <div class="card kyc-main-card mb-0">
                    <ul class="nav nav-tabs kyc-tabs" id="kycPendingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="complete-kyc-tab" data-bs-toggle="tab"
                                    data-bs-target="#completeKycPane" type="button" role="tab"
                                    aria-controls="completeKycPane" aria-selected="true">
                                <i class="ti ti-circle-check"></i> Complete KYC
                                <span class="kyc-tab-badge">{{ $pendingKycCount }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="incomplete-kyc-tab" data-bs-toggle="tab"
                                    data-bs-target="#incompleteKycPane" type="button" role="tab"
                                    aria-controls="incompleteKycPane" aria-selected="false">
                                <i class="ti ti-alert-circle"></i> Incomplete KYC
                                <span class="kyc-tab-badge kyc-tab-badge-amber">{{ $incompleteCount }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content tab-card-kyc">

                        <!-- ============ TILE 1 : Complete KYC ============ -->
                        <div class="tab-pane fade show active" id="completeKycPane" role="tabpanel" aria-labelledby="complete-kyc-tab">
                            <div class="table-responsive">
                                <table class="table kyc-table" id="kycPendingTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Customer</th>
                                            <th>KYC Type</th>
                                            <th>Organization</th>
                                            <th>Submitted At</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kycDetails as $key => $kyc)
                                            @php
                                                $customer     = $kyc->customer;
                                                $customerId   = $customer->id ?? null;
                                                $fullName     = trim((string) ($customer->first_name ?? '') . ' ' . (string) ($customer->last_name ?? '')) ?: (string) ($customer->name ?? '');
                                                if ($fullName === '') { $fullName = 'Customer'; }
                                                $nameWords    = preg_split('/\s+/', trim($fullName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
                                                $initials     = strtoupper((string) mb_substr($nameWords[0] ?? '', 0, 1) . (string) mb_substr($nameWords[1] ?? ($nameWords[0] ?? ''), 0, 1));
                                                if ($initials === '') { $initials = '?'; }
                                                $isBusiness   = ($kyc->kyc_type ?? 'personal') === 'business';
                                                $isActive     = isset($customer->status) ? (bool) $customer->status : true;
                                                $submittedOn  = optional($kyc->created_at)->format('d M Y, h:i A');
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="customer-cell">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="customer-avatar avatar-grad-{{ $key % 8 }}">{{ $initials }}</span>
                                                        <div>
                                                            <span class="customer-name">{{ $fullName }}</span>
                                                            <span class="customer-sub">
                                                                <a href="mailto:{{ $customer->email ?? '' }}">{{ $customer->email ?? '—' }}</a>
                                                            </span>
                                                            <span class="customer-sub">{{ $customer->phone_number ?? '—' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($isBusiness)
                                                        <span class="kyc-badge business"><i class="ti ti-building"></i> Business (CSB-V)</span>
                                                    @else
                                                        <span class="kyc-badge personal"><i class="ti ti-user"></i> Personal (CSB-IV)</span>
                                                    @endif
                                                </td>
                                                <td class="org-cell">
                                                    @if(! empty($kyc->organization_name))
                                                        <span class="org-name"><i class="ti ti-building-warehouse me-1"></i>{{ $kyc->organization_name }}</span>
                                                        @if(! empty($kyc->gst_number))
                                                            <span class="org-sub">{{ $kyc->gst_number }}</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="date-badge">
                                                        <i class="ti ti-clock"></i>{{ $submittedOn ?: '—' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column align-items-start gap-1">
                                                        @if(($kyc->kyc_status ?? '') === 'under_review')
                                                            <span class="kyc-badge under-review"><span class="status-dot blue"></span> Under Review</span>
                                                        @else
                                                            <span class="kyc-badge pending"><span class="status-dot amber"></span> Pending</span>
                                                        @endif
                                                        @if($isActive)
                                                            <span class="status-pill active"><i class="ti ti-circle-check"></i> Account Active</span>
                                                        @else
                                                            <span class="status-pill inactive"><i class="ti ti-ban"></i> Deactivated</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="action-cell">
                                                    <div class="d-flex flex-nowrap">
                                                        @if($customerId)
                                                            <a href="{{ route('admin.customer-profile', $customerId) }}" class="icon-btn view"
                                                               title="View customer profile, login credentials & full KYC">
                                                                <i class="ti ti-user"></i>
                                                            </a>
                                                        @endif
                                                        <form action="{{ route('admin.kyc-pending.approve', $kyc->id) }}" method="POST" class="approve-form">
                                                            @csrf
                                                            <button type="submit" class="icon-btn approve" title="Approve KYC">
                                                                <i class="ti ti-circle-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.kyc-pending.reject', $kyc->id) }}" method="POST" class="reject-form">
                                                            @csrf
                                                            <input type="hidden" name="reject_remark" class="reject-remark-input" value="">
                                                            <button type="submit" class="icon-btn reject" title="Reject KYC">
                                                                <i class="ti ti-circle-x"></i>
                                                            </button>
                                                        </form>
                                                        @if($customerId)
                                                            <form action="{{ route('admin.customer.toggle-status', $customerId) }}" method="POST" class="toggle-status-form">
                                                                @csrf
                                                                @if($isActive)
                                                                    <button type="submit" class="icon-btn deactivate" title="Deactivate this customer account (blocks login)">
                                                                        <i class="ti ti-user-off"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="submit" class="icon-btn activate" title="Activate this customer account (allows login)">
                                                                        <i class="ti ti-user-check"></i>
                                                                    </button>
                                                                @endif
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ============ TILE 2 : Incomplete KYC ============ -->
                        <div class="tab-pane fade" id="incompleteKycPane" role="tabpanel" aria-labelledby="incomplete-kyc-tab">
                            <div class="table-responsive">
                                <table class="table kyc-table" id="kycIncompleteTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Customer</th>
                                            <th>KYC Type</th>
                                            <th>KYC In Progress</th>
                                            <th>Saved Details</th>
                                            <th>Last Updated</th>
                                            <!-- <th>Actions</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($incompleteKycItems as $key => $item)
                                            @php
                                                $icCustomer    = $item['customer'];
                                                $icId          = $icCustomer->id ?? null;
                                                $icFullName    = trim((string) ($icCustomer->first_name ?? '') . ' ' . (string) ($icCustomer->last_name ?? '')) ?: (string) ($icCustomer->name ?? '');
                                                if ($icFullName === '') { $icFullName = 'Customer'; }
                                                $icNameWords   = preg_split('/\s+/', trim($icFullName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
                                                $icInitials    = strtoupper((string) mb_substr($icNameWords[0] ?? '', 0, 1) . (string) mb_substr($icNameWords[1] ?? ($icNameWords[0] ?? ''), 0, 1));
                                                if ($icInitials === '') { $icInitials = '?'; }
                                                $icBusiness    = ($item['kyc_type'] ?? 'personal') === 'business';
                                                $icHasDraft    = (bool) ($item['has_draft'] ?? false);
                                                $icRejected    = (bool) ($item['is_rejected'] ?? false);
                                                $icDone        = (int) ($item['progress_done'] ?? 0);
                                                $icTotal       = (int) ($item['progress_total'] ?? 0);
                                                $icLabels      = (string) ($item['progress_labels'] ?? '');
                                                $icPercent     = $icTotal > 0 ? (int) round(($icDone / $icTotal) * 100) : 0;
                                                $fdata         = is_array($item['form_data'] ?? null) ? $item['form_data'] : [];
                                                $icUpdated     = $item['updated_at'] ?? null;
                                                $icUpdatedText = '';
                                                if ($icUpdated instanceof \DateTimeInterface) {
                                                    $icUpdatedText = $icUpdated->format('d M Y, h:i A');
                                                } elseif (is_string($icUpdated) && $icUpdated !== '') {
                                                    $ts = strtotime($icUpdated);
                                                    if ($ts) { $icUpdatedText = date('d M Y, h:i A', $ts); }
                                                }

                                                $maskAadhar = static function ($v) {
                                                    $digits = preg_replace('/\D+/', '', (string) $v);
                                                    return strlen($digits) >= 4 ? 'XXXX-XXXX-' . substr($digits, -4) : ($digits !== '' ? 'XXXX-XXXX' : '');
                                                };
                                                $maskPan = static function ($v) {
                                                    $s = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $v));
                                                    return strlen($s) === 10 ? substr($s, 0, 4) . '*****' . substr($s, -1) : ($s !== '' ? substr($s, 0, 3) . '*****' : '');
                                                };
                                                $maskBank = static function ($v) {
                                                    $digits = preg_replace('/\D+/', '', (string) $v);
                                                    return strlen($digits) >= 4 ? 'XXXX' . substr($digits, -4) : ($digits !== '' ? 'XXXX' : '');
                                                };

                                                $detailItems = [];
                                                $candidateDetails = [
                                                    ['label' => 'GSTIN',      'value' => $fdata['gst_number'] ?? null,               'icon' => 'ti-building'],
                                                    ['label' => 'Aadhaar',    'value' => $maskAadhar($fdata['aadhar_number'] ?? ''), 'icon' => 'ti-id-badge'],
                                                    ['label' => 'PAN',        'value' => $maskPan($fdata['pan_number'] ?? ''),       'icon' => 'ti-file-text'],
                                                    ['label' => 'IEC',        'value' => $fdata['iec_number'] ?? null,               'icon' => 'ti-file-certificate'],
                                                    ['label' => 'Bank A/C',   'value' => $maskBank($fdata['bank_account_number'] ?? ''), 'icon' => 'ti-wallet'],
                                                ];
                                                foreach ($candidateDetails as $cand) {
                                                    if (! empty($cand['value'])) { $detailItems[] = $cand; }
                                                }
                                                if (! empty($fdata['signature_document']) || ! empty($fdata['signature'])) {
                                                    $detailItems[] = ['label' => 'Signature', 'value' => 'Uploaded', 'icon' => 'ti-edit', 'ok' => true];
                                                }
                                                if (! empty($fdata['terms_accepted'])) {
                                                    $detailItems[] = ['label' => 'Agreement', 'value' => 'Accepted', 'icon' => 'ti-file-check', 'ok' => true];
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="customer-cell">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="customer-avatar avatar-grad-{{ $key % 8 }}">{{ $icInitials }}</span>
                                                        <div>
                                                            <span class="customer-name">{{ $icFullName }}</span>
                                                            <span class="customer-sub">
                                                                <a href="mailto:{{ $icCustomer->email ?? '' }}">{{ $icCustomer->email ?? '—' }}</a>
                                                            </span>
                                                            <span class="customer-sub">{{ $icCustomer->phone_number ?? '—' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($icBusiness)
                                                        <span class="kyc-badge business"><i class="ti ti-building"></i> Business (CSB-V)</span>
                                                    @else
                                                        <span class="kyc-badge personal"><i class="ti ti-user"></i> Personal (CSB-IV)</span>
                                                    @endif
                                                </td>
                                                <td class="progress-cell">
                                                    @if($icHasDraft)
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="progress-track">
                                                                <div class="progress-fill" style="width: {{ $icPercent }}%"></div>
                                                            </div>
                                                            <span class="step-count">{{ $icDone }}/{{ $icTotal }}</span>
                                                        </div>
                                                        <div class="progress-meta">
                                                            @if($icLabels)
                                                                <span class="progress-labels">{{ $icLabels }}</span>
                                                            @else
                                                                <span class="progress-labels">No steps completed yet</span>
                                                            @endif
                                                        </div>
                                                        @if($icRejected)
                                                            <span class="rejected-pill"><i class="ti ti-alert-triangle"></i> Re-submission required</span>
                                                        @endif
                                                    @elseif($icRejected)
                                                        <span class="rejected-pill"><i class="ti ti-alert-triangle"></i> Rejected — please resubmit</span>
                                                    @else
                                                        <span class="incomplete-note"><i class="ti ti-user-off"></i> Not started KYC</span>
                                                    @endif
                                                </td>
                                                <td class="draft-details-cell">
                                                    @if(count($detailItems) > 0)
                                                        <div class="draft-details">
                                                            @foreach($detailItems as $detailItem)
                                                                <span class="draft-detail {{ ! empty($detailItem['ok']) ? 'ok' : '' }}">
                                                                    <i class="ti {{ $detailItem['icon'] }}"></i>
                                                                    <span class="dd-label">{{ $detailItem['label'] }}:</span>
                                                                    <strong>{{ $detailItem['value'] }}</strong>
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">No details saved yet</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="date-badge">
                                                        <i class="ti ti-clock"></i>{{ $icUpdatedText ?: '—' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

    <!-- Reject KYC Modal -->
    <div class="modal fade" id="rejectKycModal" tabindex="-1" aria-labelledby="rejectKycModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectKycModalLabel">
                        <i class="ti ti-alert-triangle me-2 text-danger"></i>Reject KYC
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Are you sure you want to reject KYC for <strong id="rk-customer-name">—</strong>?
                    </p>
                    <div class="mb-2">
                        <label for="rejectRemarkInput" class="form-label">Remark <span class="text-danger">*</span></label>
                        <textarea id="rejectRemarkInput" class="form-control" rows="3" maxlength="1000" required
                            placeholder="Enter the reason for rejection..."></textarea>
                        <div class="text-danger small mt-1 reject-remark-error d-none">Please enter a remark before rejecting.</div>
                        @if ($errors->has('reject_remark'))
                            <div class="text-danger small mt-1">{{ $errors->first('reject_remark') }}</div>
                        @endif
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
        $(document).ready(function() {

            // ----- Suppress benign DataTables "unknown parameter" warnings -----
            // Header & body cells in the tables below always match in count.
            // This guard simply cancels the default alert/console error for the
            // "Requested unknown parameter" (TN4) case so it can never break the UI.
            $(document).on('error.dt', function(e, settings, techNote, message) {
                if (typeof message === 'string' && message.indexOf('Requested unknown parameter') !== -1) {
                    return false; // cancel default action for this benign case
                }
            });

            // ===== TILE 1 : Complete KYC DataTable =====
            if ($('#kycPendingTable').length) {
                $('#kycPendingTable').DataTable({
                    order: [[0, 'asc']],
                    pageLength: 25,
                    language: {
                        search: "Search KYC:",
                        lengthMenu: "Show _MENU_ entries per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        emptyTable: "No pending KYC submissions found. All submissions have been reviewed.",
                    },
                    columnDefs: [
                        { orderable: false, targets: [5, 6] }
                    ]
                });
            }

            // ===== TILE 2 : Incomplete KYC DataTable =====
            if ($('#kycIncompleteTable').length) {
                $('#kycIncompleteTable').DataTable({
                    order: [[0, 'asc']],
                    pageLength: 25,
                    language: {
                        search: "Search customers:",
                        lengthMenu: "Show _MENU_ entries per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        emptyTable: "No incomplete KYC records found. All registered customers have completed KYC.",
                    },
                    columnDefs: [
                        { orderable: false, targets: [5] }
                    ]
                });
            }

            // ===== Re-size tables when switching tabs =====
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('data-bs-target');
                if (target === '#completeKycPane' && $.fn.DataTable.isDataTable('#kycPendingTable')) {
                    $('#kycPendingTable').DataTable().columns.adjust().draw();
                } else if (target === '#incompleteKycPane' && $.fn.DataTable.isDataTable('#kycIncompleteTable')) {
                    $('#kycIncompleteTable').DataTable().columns.adjust().draw();
                }
            });

            // ===== SweetAlert2 confirmation for Approve =====
            $('.approve-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: 'Approve KYC?',
                    text: "Are you sure you want to approve this KYC submission?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // ===== SweetAlert2 confirmation for Activate/Deactivate account =====
            $('.toggle-status-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var btn = $(form).find('button[type="submit"]');
                var isDeactivate = btn.hasClass('deactivate');
                var title = isDeactivate ? 'Deactivate Account?' : 'Activate Account?';
                var text = isDeactivate
                    ? "This customer will no longer be able to log in. You can reactivate the account later."
                    : "This will allow the customer to log in again.";
                var icon = isDeactivate ? 'warning' : 'question';
                var confirmColor = isDeactivate ? '#b91c1c' : '#15803d';
                var confirmText = isDeactivate ? 'Yes, Deactivate' : 'Yes, Activate';
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // ===== Reject KYC with remark: open modal, then submit on confirm =====
            var rejectFormToSubmit = null;
            var rejectModalEl = document.getElementById('rejectKycModal');
            var rejectModal = rejectModalEl ? bootstrap.Modal.getOrCreateInstance(rejectModalEl) : null;

            $('.reject-form').on('submit', function(e) {
                e.preventDefault();
                rejectFormToSubmit = this;
                var rowName = $(this).closest('tr').find('.customer-name').first().text().trim() || 'Customer';
                $('#rejectRemarkInput').val('');
                $('#rk-customer-name').text(rowName);
                $('.reject-remark-error').addClass('d-none');
                $('#rejectRemarkInput').removeClass('is-invalid');
                if (rejectModal) {
                    rejectModal.show();
                }
            });

            $('#confirmRejectBtn').on('click', function() {
                if (!rejectFormToSubmit) return;
                var remark = $('#rejectRemarkInput').val().trim();
                if (!remark) {
                    $('#rejectRemarkInput').addClass('is-invalid');
                    $('.reject-remark-error').removeClass('d-none');
                    return;
                }
                $('#rejectRemarkInput').removeClass('is-invalid');
                $('.reject-remark-error').addClass('d-none');
                $(rejectFormToSubmit).find('.reject-remark-input').val(remark);
                if (rejectModal) {
                    rejectModal.hide();
                }
                var form = rejectFormToSubmit;
                rejectFormToSubmit = null;
                form.submit();
            });

        });
    </script>

</body>
</html>
