<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	
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

    <!-- Chart.js (local copy - no CDN dependency) -->
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>

    <style>
        .chart-filter-btn {
            padding: 4px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: #fff;
            color: #495057;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .chart-filter-btn:hover {
            background: #e9ecef;
        }
        .chart-filter-btn.active {
            background: #5b5eff;
            color: #fff;
            border-color: #5b5eff;
        }
        .chart-card {
            min-height: 300px;
        }
        /* All pie & bar charts render at one uniform size */
        .chart-card .card-body {
            height: 300px;
            overflow: hidden;
        }

        /* --- Dashboard polish --- */
        .dash-stat-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .dash-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.09);
        }
        .dash-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            flex-shrink: 0;
        }
        .icon-indigo { background: linear-gradient(135deg, #5b5eff, #7367f0); box-shadow: 0 4px 12px rgba(91, 94, 255, 0.32); }
        .icon-orange { background: linear-gradient(135deg, #ff9f43, #ff7a2e); box-shadow: 0 4px 12px rgba(255, 159, 67, 0.32); }
        .icon-green  { background: linear-gradient(135deg, #1abe17, #0f9d0f); box-shadow: 0 4px 12px rgba(26, 190, 23, 0.32); }
        .icon-red    { background: linear-gradient(135deg, #ff4d4f, #e5383b); box-shadow: 0 4px 12px rgba(255, 77, 79, 0.32); }
        .icon-teal   { background: linear-gradient(135deg, #20c997, #12b3a8); box-shadow: 0 4px 12px rgba(32, 201, 151, 0.32); }
        .icon-purple { background: linear-gradient(135deg, #7367f0, #9b59f6); box-shadow: 0 4px 12px rgba(115, 103, 240, 0.32); }
        .icon-blue   { background: linear-gradient(135deg, #2f80ed, #1e6fd9); box-shadow: 0 4px 12px rgba(47, 128, 237, 0.32); }
        .icon-cyan   { background: linear-gradient(135deg, #0dcaf0, #0891b2); box-shadow: 0 4px 12px rgba(13, 202, 240, 0.32); }

        .stat-trend-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .stat-trend-badge.up { background: rgba(26, 190, 23, 0.12); color: #15803d; }
        .stat-trend-badge.down { background: rgba(239, 30, 30, 0.12); color: #dc2626; }
        .stat-trend-badge.flat { background: rgba(100, 116, 139, 0.12); color: #64748b; }
        [data-bs-theme="dark"] .stat-trend-badge.up { color: #4ade80; }
        [data-bs-theme="dark"] .stat-trend-badge.down { color: #f87171; }
        [data-bs-theme="dark"] .stat-trend-badge.flat { color: #94a3b8; }

        .dash-list-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.08);
        }
        .dash-list-item:last-child { border-bottom: 0; }
        [data-bs-theme="dark"] .dash-list-item { border-bottom-color: rgba(255, 255, 255, 0.08); }
        .dash-avatar {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
            flex-shrink: 0;
        }

        .skeleton {
            position: relative;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.06);
            border-radius: 8px;
        }
        .skeleton::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.45), transparent);
            animation: skeleton-shimmer 1.3s infinite;
        }
        @keyframes skeleton-shimmer {
            100% { transform: translateX(100%); }
        }
        [data-bs-theme="dark"] .skeleton { background: rgba(255, 255, 255, 0.08); }
        [data-bs-theme="dark"] .skeleton::after { background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent); }

        .dash-status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .dashboard-title h4 { letter-spacing: -0.3px; }
        .activity-tab-link { cursor: pointer; user-select: none; }

        /* ===== New dashboard design system (light, soft, rounded) ===== */
        .content .card {
            border-radius: 16px;
            border: 1px solid #e8edf5;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
        }
        .content .card-header {
            background: transparent;
            border-bottom: 1px solid #eef1f6;
            padding: 14px 20px;
        }
        .content .card-header h6 { font-weight: 700; font-size: 14.5px; }
        .content .card-body { padding: 20px; }
        .content .table thead th {
            background: #f6f8fc;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #8a94a6;
            border-bottom: 1px solid #eef1f6;
            white-space: nowrap;
        }
        .sec-title {
            display: inline-flex; align-items: center; gap: 8px;
            font-weight: 700; font-size: 15px;
        }
        .sec-title .sec-ic {
            width: 26px; height: 26px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #2563eb, #1e6fd9); color: #fff; font-size: 14px;
        }

        /* Hero banner (background: public/assets/images/banner.png) */
        .hero-banner {
            position: relative; overflow: hidden;
            border-radius: 18px;
            background-size: cover; background-position: center right; background-repeat: no-repeat;
            border: 1px solid #e2e8f0;
            padding: 26px 28px;
            min-height: 300px;
        }
        .hero-banner::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(90deg, rgba(255,255,255,.96) 25%, rgba(255,255,255,.75) 50%, rgba(255,255,255,.1) 80%);
        }
        .hero-banner > * { position: relative; z-index: 1; }
        .hero-banner h4 { font-weight: 800; letter-spacing: -.3px; color: #0f172a; }
        .hero-date { color: #64748b; font-size: 13px; }
        .hero-cards { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 18px; }
        .hero-stat-card {
            background: #fff; border-radius: 14px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .10);
            border: 1px solid #eef1f6;
            padding: 14px 18px;
            display: flex; gap: 12px; align-items: flex-start;
            min-width: 200px; flex: 0 1 250px;
        }
        .hero-stat-card .hs-ic {
            width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 19px; color: #fff;
        }
        .hero-stat-card .hs-label { font-size: 12.5px; color: #475569; font-weight: 600; }
        .hero-stat-card .hs-value { font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1.15; }
        /* Quick action buttons */
        .qa-btn {
            display: flex; align-items: center; gap: 12px;
            border-radius: 14px; padding: 14px 18px; color: #fff !important;
            text-decoration: none; border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, .14);
            transition: transform .18s ease, box-shadow .18s ease;
            height: 100%;
        }
        .qa-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(15, 23, 42, .18); color: #fff !important; }
        .qa-btn .qa-ic {
            width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.22); font-size: 19px;
        }
        .qa-btn b { display: block; font-size: 14px; line-height: 1.2; }
        .qa-btn small { display: block; font-size: 11.5px; opacity: .85; }
        .qa-blue { background: linear-gradient(135deg, #2563eb, #1e40af); }
        .qa-green { background: linear-gradient(135deg, #10b981, #047857); }
        .qa-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .qa-orange { background: linear-gradient(135deg, #fb923c, #ea580c); }
        @media (max-width: 991px) {
            .hero-banner { background-position: center; }
            .hero-banner::before { background: rgba(255,255,255,.88); }
        }

        /* Stat tiles */
        .stat-tile { transition: transform .2s ease, box-shadow .2s ease; }
        .stat-tile:hover { transform: translateY(-3px); box-shadow: 0 12px 26px rgba(15, 23, 42, .09); }
        .tile-top { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .tile-icon {
            width: 44px; height: 44px; border-radius: 13px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 20px; color: #fff;
        }
        .tile-label { font-size: 13px; color: #475569; font-weight: 600; margin: 0; line-height: 1.3; }
        .tile-sub { font-size: 11px; color: #94a3b8; font-weight: 500; }
        .tile-value { font-size: 30px; font-weight: 800; letter-spacing: -.5px; margin: 0 0 8px; color: #0f172a; }
        .tile-foot { display: flex; align-items: flex-end; justify-content: space-between; gap: 8px; }
        .tile-spark { width: 96px !important; height: 38px !important; flex-shrink: 0; }

        /* KYC status minis */
        .kyc-mini {
            border: 1px solid #e9edf3; border-radius: 14px; padding: 16px 18px;
            display: flex; align-items: center; gap: 14px; background: #fff;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
        }
        .kyc-mini .km-ic {
            width: 46px; height: 46px; border-radius: 14px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 21px; color: #fff;
        }
        .kyc-mini b { font-size: 22px; font-weight: 800; color: #0f172a; display: block; line-height: 1.15; }
        .kyc-mini small { color: #64748b; font-size: 12.5px; font-weight: 500; }
        .kyc-card-title { font-size: 16.5px !important; font-weight: 800 !important; }
        .kyc-empty { text-align: center; padding: 30px 0 22px; }
        .kyc-empty i { font-size: 30px; color: #22c55e; }
        .kyc-empty p { color: #64748b; font-size: 14px; font-weight: 500; margin: 8px 0 0; }
        .km-amber { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .km-blue { background: linear-gradient(135deg, #60a5fa, #2563eb); }
        .km-green { background: linear-gradient(135deg, #34d399, #059669); }
        .km-red { background: linear-gradient(135deg, #f87171, #dc2626); }

        /* Filter pills */
        .chart-filter-btn {
            padding: 6px 16px; border: 1px solid #e2e8f0; border-radius: 20px;
            background: #fff; color: #64748b; font-size: 12px; font-weight: 600;
        }
        .chart-filter-btn:hover { background: #f1f5f9; }
        .chart-filter-btn.active { background: #2563eb; color: #fff; border-color: #2563eb; }

        [data-bs-theme="dark"] .content .card { background: #141b2d; border-color: rgba(255,255,255,.08); }
        [data-bs-theme="dark"] .content .card-header { border-bottom-color: rgba(255,255,255,.08); }
        [data-bs-theme="dark"] .content .table thead th { background: rgba(255,255,255,.04); color: #94a3b8; }
        [data-bs-theme="dark"] .hero-banner { border-color: rgba(255,255,255,.08); }
        [data-bs-theme="dark"] .hero-banner::before { background: linear-gradient(90deg, rgba(15,23,42,.96) 25%, rgba(15,23,42,.82) 55%, rgba(15,23,42,.35)); }
        [data-bs-theme="dark"] .hero-stat-card { background: #141b2d; border-color: rgba(255,255,255,.08); }
        [data-bs-theme="dark"] .tile-value, [data-bs-theme="dark"] .hero-banner h4, [data-bs-theme="dark"] .hero-stat-card .hs-value { color: #fff; }
        [data-bs-theme="dark"] .tile-label, [data-bs-theme="dark"] .hero-date { color: #94a3b8; }
        [data-bs-theme="dark"] .cc-picker-overlay { background: #141b2d; border-color: rgba(255,255,255,.1); color: #cbd5e1; }
        [data-bs-theme="dark"] .kyc-mini { background: #141b2d; border-color: rgba(255,255,255,.08); }

        /* ---- Dashboard entrance + micro animations ---- */
        @keyframes dashFadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes growBar { from { width: 0 !important; } }
        .dash-anim { opacity: 0; }
        .dash-anim.in { animation: dashFadeUp .55s cubic-bezier(.22,.61,.36,1) forwards; animation-delay: var(--d, 0s); }
        .hero-stat-card { animation: dashFadeUp .5s ease both; }
        .hero-stat-card:nth-child(2) { animation-delay: .08s; }
        .hero-stat-card:nth-child(3) { animation-delay: .16s; }
        #summaryBox .progress-bar { animation: growBar .8s ease-out; }
        @media (prefers-reduced-motion: reduce) {
            .dash-anim, .hero-stat-card, #summaryBox .progress-bar { animation: none !important; opacity: 1 !important; }
            .dash-anim, .hero-stat-card, .stat-tile, .qa-btn { transition: none !important; }
        }

        /* --- Display-only status tabs (view only, no actions) --- */
        .pd-view-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .pd-view-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            background: #fff;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            cursor: default;
            user-select: none;
            pointer-events: none;
        }
        .pd-view-tab .pd-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 24px;
            padding: 0 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
        }
        [data-bs-theme="dark"] .pd-view-tab {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.85);
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

                <!-- Hero banner -->
                <div class="hero-banner mb-4" style="background-image:url('{{ asset('assets/images/banner.png') }}');">
                    <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                        <div>
                            <h4 class="mb-1">{{ $greeting }}, {{ $adminName }} <i class="ti ti-sun fs-20 text-warning align-middle ms-1"></i></h4>
                            <p class="text-muted fs-13 mb-2">Here's what's happening with your courier network today.</p>
                            <p class="hero-date mb-0"><i class="ti ti-calendar me-1"></i>{{ \Carbon\Carbon::now()->format('l, d F Y') }} &bull; {{ \Carbon\Carbon::now()->format('h:i A') }}</p>
                        </div>
                        <div class="gap-2 d-flex align-items-center flex-wrap">
                            <a href="{{ route('admin.kyc-pending') }}" class="btn btn-sm btn-primary">
                                <i class="ti ti-file-alert me-1"></i>KYC Pending <span class="badge bg-white text-primary ms-1">{{ $kycPending }}</span>
                            </a>
                            <a href="javascript:void(0);" class="btn btn-icon btn-outline-light bg-white shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload()"><i class="ti ti-refresh"></i></a>
                        </div>
                    </div>
                    <div class="hero-cards">
                        <div class="hero-stat-card">
                            <span class="hs-ic icon-indigo"><i class="ti ti-package"></i></span>
                            <div>
                                <div class="hs-label">Total Shipments</div>
                                <div class="hs-value">{{ number_format($totalShipments) }}</div>
                                @if($shipMoM > 0)
                                <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $shipMoM }}% <span class="fw-normal ms-1">vs. last month</span></span>
                                @elseif($shipMoM < 0)
                                <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($shipMoM) }}% <span class="fw-normal ms-1">vs. last month</span></span>
                                @else
                                <span class="stat-trend-badge flat">No change vs last month</span>
                                @endif
                            </div>
                        </div>
                        <div class="hero-stat-card">
                            <span class="hs-ic icon-green"><i class="ti ti-truck-delivery"></i></span>
                            <div>
                                <div class="hs-label">Delivered Today</div>
                                <div class="hs-value">{{ number_format($deliveredToday) }}</div>
                                @if($deliveredMoM > 0)
                                <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $deliveredMoM }}% <span class="fw-normal ms-1">vs. last month</span></span>
                                @elseif($deliveredMoM < 0)
                                <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($deliveredMoM) }}% <span class="fw-normal ms-1">vs. last month</span></span>
                                @else
                                <span class="stat-trend-badge flat">No change vs last month</span>
                                @endif
                            </div>
                        </div>
                        <div class="hero-stat-card">
                            <span class="hs-ic icon-blue"><i class="ti ti-truck"></i></span>
                            <div>
                                <div class="hs-label">In Transit</div>
                                <div class="hs-value">{{ number_format($inTransit) }}</div>
                                <span class="stat-trend-badge flat">Moving through the network</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Hero banner -->

                <!-- Quick actions -->
                <div class="row row-gap-3 mb-4">
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <a href="{{ route('admin.companies') }}" class="qa-btn qa-blue flex-fill">
                            <span class="qa-ic"><i class="ti ti-package"></i></span>
                            <span><b>Create Shipment</b><small>New shipment</small></span>
                        </a>
                    </div>
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <a href="{{ url('/get-started') }}" class="qa-btn qa-green flex-fill">
                            <span class="qa-ic"><i class="ti ti-user-plus"></i></span>
                            <span><b>Add Customer</b><small>Register customer</small></span>
                        </a>
                    </div>
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <a href="{{ route('admin.manage-rate') }}" class="qa-btn qa-purple flex-fill">
                            <span class="qa-ic"><i class="ti ti-tag"></i></span>
                            <span><b>Add Rate</b><small>Manage rates</small></span>
                        </a>
                    </div>
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <a href="{{ route('admin.customer-report') }}" class="qa-btn qa-orange flex-fill">
                            <span class="qa-ic"><i class="ti ti-file-download"></i></span>
                            <span><b>Generate Report</b><small>Download reports</small></span>
                        </a>
                    </div>
                </div>
                <!-- /Quick actions -->

                <!-- Display-only status tabs (view only — no actions attached) -->
                <div class="row row-gap-3 mb-4">
                    <div class="col-12">
                        <div class="card mb-0">
                            <div class="card-body py-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <h6 class="mb-0"><i class="ti ti-truck-delivery me-1"></i>Pickup & Dispatch — Live Status</h6>
                                    <span class="badge bg-secondary-subtle text-secondary"><i class="ti ti-eye me-1"></i>View only</span>
                                </div>
                                <div class="pd-view-tabs">
                                    <span class="pd-view-tab" aria-disabled="true">
                                        <i class="ti ti-calendar-event text-primary"></i>Ready for Pickup
                                        <span class="pd-count" style="background:#6f42c1;" id="pdTabReady">{{ number_format($pickupDispatchCounts['ready_for_pickup'] ?? 0) }}</span>
                                    </span>
                                    <span class="pd-view-tab" aria-disabled="true">
                                        <i class="ti ti-truck-delivery text-primary"></i>Assigned for Pickup
                                        <span class="pd-count" style="background:#6366f1;" id="pdTabAssigned">{{ number_format($pickupDispatchCounts['assigned_for_pickup'] ?? 0) }}</span>
                                    </span>
                                    <span class="pd-view-tab" aria-disabled="true">
                                        <i class="ti ti-printer text-primary"></i>Print Label
                                        <span class="pd-count" style="background:#06b6d4;" id="pdTabPrint">{{ number_format($pickupDispatchCounts['print_label'] ?? 0) }}</span>
                                    </span>
                                    <span class="pd-view-tab" aria-disabled="true">
                                        <i class="ti ti-truck text-primary"></i>Ready to Dispatch
                                        <span class="pd-count" style="background:#f59e0b;" id="pdTabDispatch">{{ number_format($pickupDispatchCounts['ready_to_dispatch'] ?? 0) }}</span>
                                    </span>
                                </div>
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>AWB</th>
                                                <th>Customer</th>
                                                <th>Stage</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pdLiveTableBody">
                                            @forelse($pickupDispatchRows->take(5) as $key => $row)
                                            @php
                                                $pdLiveName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
                                                $pdLiveCustomer = $row->company_name ?? ($pdLiveName !== '' ? $pdLiveName : '—');
                                                $pdLiveStageColors = [
                                                    'ready_for_pickup' => 'background:rgba(111,66,193,.12);color:#6f42c1;',
                                                    'assigned_for_pickup' => 'background:rgba(99,102,241,.12);color:#6366f1;',
                                                    'received' => 'background:rgba(6,182,212,.12);color:#06b6d4;',
                                                    'dispatched' => 'background:rgba(6,182,212,.12);color:#06b6d4;',
                                                    'ready_to_dispatch' => 'background:rgba(245,158,11,.14);color:#b45309;',
                                                ];
                                                $pdLiveStageTitles = [
                                                    'ready_for_pickup' => 'Ready for Pickup',
                                                    'assigned_for_pickup' => 'Assigned for Pickup',
                                                    'received' => 'Print Label',
                                                    'dispatched' => 'Print Label',
                                                    'ready_to_dispatch' => 'Ready to Dispatch',
                                                ];
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td><span class="fw-semibold">{{ $row->awb_number ?? '—' }}</span></td>
                                                <td>{{ $pdLiveCustomer }}</td>
                                                <td><span class="dash-status-badge" style="{{ $pdLiveStageColors[$row->status] ?? '' }}">{{ $pdLiveStageTitles[$row->status] ?? ucfirst(str_replace('_', ' ', $row->status)) }}</span></td>
                                                <td class="text-muted">{{ \Carbon\Carbon::parse($row->created_at)->format('d M, h:i A') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No shipments in pickup / dispatch stages</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Display-only status tabs -->

                <!-- start row - Customer Summary Stat Cards -->
                <h6 class="mb-2 fw-bold" style="font-size:15px;">Key Statistics</h6>
                <div class="row row-gap-3 mb-4">
					<!-- Total Registrations -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card stat-tile flex-fill mb-0">
							<div class="card-body">
                                <div class="tile-top">
                                    <span class="tile-icon icon-indigo"><i class="ti ti-building"></i></span>
                                    <div><p class="tile-label">Total Registrations</p></div>
                                </div>
                                <h2 class="tile-value">{{ number_format($totalRegistrations) }}</h2>
                                <div class="tile-foot">
                                    @if($registrationsChangePercent > 0)
                                    <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $registrationsChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @elseif($registrationsChangePercent < 0)
                                    <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($registrationsChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @else
                                    <span class="stat-trend-badge flat">No change vs last month</span>
                                    @endif
                                    <canvas class="tile-spark" id="sparkRegistrations"></canvas>
                                </div>
							</div>
						</div>
					</div>
					<!-- /Total Registrations -->

                    <!-- KYC Pending -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card stat-tile flex-fill mb-0">
							<div class="card-body">
                                <div class="tile-top">
                                    <span class="tile-icon icon-orange"><i class="ti ti-clock"></i></span>
                                    <div><p class="tile-label">KYC Pending</p></div>
                                </div>
                                <h2 class="tile-value">{{ number_format($kycPending) }}</h2>
                                <div class="tile-foot">
                                    @if($kycPendingChangePercent > 0)
                                    <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-up"></i>{{ $kycPendingChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @elseif($kycPendingChangePercent < 0)
                                    <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-down"></i>{{ abs($kycPendingChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @else
                                    <span class="stat-trend-badge flat">No change vs last month</span>
                                    @endif
                                </div>
							</div>
						</div>
					</div>
					<!-- /KYC Pending -->

                    <!-- Onboarded Customers -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card stat-tile flex-fill mb-0">
							<div class="card-body">
                                <div class="tile-top">
                                    <span class="tile-icon icon-green"><i class="ti ti-user-check"></i></span>
                                    <div><p class="tile-label">Onboarded Customers</p></div>
                                </div>
                                <h2 class="tile-value">{{ number_format($onboardedCustomers) }}</h2>
                                <div class="tile-foot">
                                    @if($onboardedChangePercent > 0)
                                    <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $onboardedChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @elseif($onboardedChangePercent < 0)
                                    <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($onboardedChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @else
                                    <span class="stat-trend-badge flat">No change vs last month</span>
                                    @endif
                                    <canvas class="tile-spark" id="sparkOnboarded"></canvas>
                                </div>
							</div>
						</div>
					</div>
					<!-- /Onboarded Customers -->

                    <!-- CSB5 Enabled -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card stat-tile flex-fill mb-0">
							<div class="card-body">
                                <div class="tile-top">
                                    <span class="tile-icon icon-purple"><i class="ti ti-file-check"></i></span>
                                    <div><p class="tile-label">CSB5 Enabled</p></div>
                                </div>
                                <h2 class="tile-value">{{ number_format($csb5Enabled) }}</h2>
                                <div class="tile-foot">
                                    @if($csb5ChangePercent > 0)
                                    <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $csb5ChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @elseif($csb5ChangePercent < 0)
                                    <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($csb5ChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @else
                                    <span class="stat-trend-badge flat">No change vs last month</span>
                                    @endif
                                    <canvas class="tile-spark" id="sparkCsb"></canvas>
                                </div>
							</div>
						</div>
					</div>
					<!-- /CSB5 Enabled -->

				</div>
                <!-- end row -->

                <!-- start row - Business Summary Stat Cards -->
                <div class="row row-gap-3 mb-4">
                    <!-- Revenue -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card stat-tile flex-fill mb-0">
                            <div class="card-body">
                                <div class="tile-top">
                                    <span class="tile-icon icon-green"><i class="ti ti-currency-rupee"></i></span>
                                    <div><p class="tile-label">Business Summary</p><span class="tile-sub">Revenue (This Month)</span></div>
                                </div>
                                <h2 class="tile-value" id="statRevenue">₹ {{ number_format($thisMonthRevenue) }}</h2>
                                <div class="tile-foot">
                                    @if($revenueChangePercent > 0)
                                    <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $revenueChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @elseif($revenueChangePercent < 0)
                                    <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($revenueChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @else
                                    <span class="stat-trend-badge flat">No change vs last month</span>
                                    @endif
                                    <canvas class="tile-spark" id="sparkRevenue"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Revenue -->

                    <!-- Total Shipments -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card stat-tile flex-fill mb-0">
                            <div class="card-body">
                                <div class="tile-top">
                                    <span class="tile-icon icon-blue"><i class="ti ti-truck"></i></span>
                                    <div><p class="tile-label">Total Shipments</p></div>
                                </div>
                                <h2 class="tile-value" id="statShipmentsTile">{{ number_format($totalShipments) }}</h2>
                                <div class="tile-foot">
                                    <span class="stat-trend-badge flat">All time</span>
                                    <canvas class="tile-spark" id="sparkShipments"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Total Shipments -->

                    <!-- Wallet Top-ups -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card stat-tile flex-fill mb-0">
                            <div class="card-body">
                                <div class="tile-top">
                                    <span class="tile-icon icon-purple"><i class="ti ti-wallet"></i></span>
                                    <div><p class="tile-label">Wallet Top-ups</p><span class="tile-sub">This Month</span></div>
                                </div>
                                <h2 class="tile-value" id="statWalletTopups">₹ {{ number_format($thisMonthWalletTopups) }}</h2>
                                <div class="tile-foot">
                                    @if($walletTopupsChangePercent > 0)
                                    <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $walletTopupsChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @elseif($walletTopupsChangePercent < 0)
                                    <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($walletTopupsChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                    @else
                                    <span class="stat-trend-badge flat">No change vs last month</span>
                                    @endif
                                    <canvas class="tile-spark" id="sparkWallet"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Wallet Top-ups -->

                    <!-- Delivery Success Rate -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card stat-tile flex-fill mb-0">
                            <div class="card-body">
                                <div class="tile-top">
                                    <span class="tile-icon icon-teal"><i class="ti ti-circle-check"></i></span>
                                    <div><p class="tile-label">Delivery Success Rate</p></div>
                                </div>
                                <h2 class="tile-value" id="statSuccessRate">{{ $deliverySuccessRate }}%</h2>
                                <div class="tile-foot">
                                    <span class="stat-trend-badge flat">Delivered vs non-cancelled</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Delivery Success Rate -->
                </div>
                <!-- end row - Business Summary Stat Cards -->

                <!-- KYC Pending List -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 kyc-card-title"><span class="sec-title"><span class="sec-ic"><i class="ti ti-shield-check"></i></span>KYC Pending Customers</span></h6>
                                <a href="{{ url('/admin/kyc-pending') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="row row-gap-3 mb-3">
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="kyc-mini">
                                            <span class="km-ic km-amber"><i class="ti ti-clock"></i></span>
                                            <div><b id="kycMiniPending">{{ number_format($kycSplit['pending'] ?? 0) }}</b><small>Pending</small></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="kyc-mini">
                                            <span class="km-ic km-blue"><i class="ti ti-file-search"></i></span>
                                            <div><b id="kycMiniUnderReview">{{ number_format($kycSplit['under_review'] ?? 0) }}</b><small>Under Review</small></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="kyc-mini">
                                            <span class="km-ic km-green"><i class="ti ti-circle-check"></i></span>
                                            <div><b id="kycMiniApproved">{{ number_format($kycSplit['approved'] ?? 0) }}</b><small>Approved</small></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="kyc-mini">
                                            <span class="km-ic km-red"><i class="ti ti-circle-x"></i></span>
                                            <div><b id="kycMiniRejected">{{ number_format($kycSplit['rejected'] ?? 0) }}</b><small>Rejected</small></div>
                                        </div>
                                    </div>
                                </div>
                                @if($kycPendingList->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Customer Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Organization</th>
                                                <th>GST Number</th>
                                                <th>Submitted At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kycPendingList as $key => $kyc)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td><strong>{{ $kyc->customer->first_name ?? '' }} {{ $kyc->customer->last_name ?? '' }}</strong></td>
                                                <td><a href="mailto:{{ $kyc->customer->email ?? '' }}">{{ $kyc->customer->email ?? '—' }}</a></td>
                                                <td>{{ $kyc->customer->phone_number ?? '—' }}</td>
                                                <td>{{ $kyc->organization_name ?? '—' }}</td>
                                                <td>{{ $kyc->gst_number ?? '—' }}</td>
                                                <td><span class="badge bg-warning text-dark">{{ $kyc->created_at->format('d M Y, h:i A') }}</span></td>
                                                <td>
                                                    <a href="{{ route('admin.kyc-pending') }}" class="btn btn-sm btn-outline-primary" title="View KYC Details">
                                                        <i class="ti ti-eye me-1"></i>View
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="kyc-empty">
                                    <i class="ti ti-circle-check"></i>
                                    <p>No pending KYC submissions</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /KYC Pending List -->

                <!-- Shipment Analytics Section with Date Filters -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="mb-0"><span class="sec-title"><span class="sec-ic"><i class="ti ti-chart-bar"></i></span>Shipment Analytics</span></h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="chart-filter-btn" data-filter="today" onclick="loadChartData('today', this)">Today</button>
                            <button class="chart-filter-btn" data-filter="yesterday" onclick="loadChartData('yesterday', this)">Yesterday</button>
                            <button class="chart-filter-btn active" data-filter="this_month" onclick="loadChartData('this_month', this)">This Month</button>
                            <button class="chart-filter-btn" data-filter="last_month" onclick="loadChartData('last_month', this)">Last Month</button>
                            <button class="chart-filter-btn" data-filter="last_year" onclick="loadChartData('last_year', this)">Last Year</button>
                        </div>
                    </div>
                    <div class="card-body">
                <!-- start row - Customer Summary Charts (Pie + Bar) -->
                <div class="row row-gap-3">
                    <!-- Customer Summary Pie/Doughnut -->
                    <div class="col-xl-5 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Customer Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center flex-wrap gap-4 h-100">
                                    <div style="position: relative; width: 250px; height: 250px;">
                                        <canvas id="customerSummaryChart"></canvas>
                                    </div>
                                    <div id="customerSummaryLegend" class="flex-fill" style="min-width: 210px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Customer Summary Bar Chart -->
                    <div class="col-xl-7 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Customer Summary — Bar View</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="customerSummaryBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                    </div>
                </div>
                <!-- /Shipment Analytics card -->

                <!-- start row - Shipment & Delivery Stat Tiles -->
                <div class="row row-gap-3 mb-4">
                    <!-- Total Shipments -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Total Shipments</p>
                                        <h2 class="mb-2 fw-semibold" id="statTotalShipments">{{ number_format(array_sum($shipmentStatusCounts)) }}</h2>
                                        <span class="stat-trend-badge flat" id="statTotalShipmentsSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-indigo"><i class="ti ti-package"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-01.svg') }}" alt="elemnt-01" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Total Shipments -->

                    <!-- Delivered -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Delivered</p>
                                        <h2 class="mb-2 fw-semibold" id="statDelivered">{{ number_format($deliveredCount) }}</h2>
                                        <span class="stat-trend-badge flat" id="statDeliveredSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-green"><i class="ti ti-truck-delivery"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-02.svg') }}" alt="elemnt-02" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Delivered -->

                    <!-- ShipRocket -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">ShipRocket</p>
                                        <h2 class="mb-2 fw-semibold" id="statShipRocket">{{ number_format($shipRocketCount) }}</h2>
                                        <span class="stat-trend-badge flat" id="statShipRocketSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-cyan"><i class="ti ti-rocket"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-03.svg') }}" alt="elemnt-03" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /ShipRocket -->

                    <!-- Self/Own Network -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Self/Own Network</p>
                                        <h2 class="mb-2 fw-semibold" id="statSelfNetwork">{{ number_format($selfCount) }}</h2>
                                        <span class="stat-trend-badge flat" id="statSelfNetworkSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-orange"><i class="ti ti-world"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-04.svg') }}" alt="elemnt-04" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Self/Own Network -->

                </div>
                <!-- end row - Shipment & Delivery Stat Tiles -->

                <!-- start row - Shipment & Delivery Summary Charts (Pie + Bar) -->
                <div class="row row-gap-3 mb-4">
                    <!-- Shipment & Delivery Merged Doughnut -->
                    <div class="col-xl-5 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Shipment & Delivery Summary</h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <canvas id="shipmentDeliverySummaryChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Shipment & Delivery Merged Bar Chart -->
                    <div class="col-xl-7 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Shipment & Delivery Summary — Bar View</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="shipmentDeliveryBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <!-- start row - Shipment Trend Bar Chart -->
                <div class="row mb-4">
                    <div class="col-xl-12 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <h6 class="mb-0">Shipment Creation Trend</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="shipmentTrendChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <!-- start row - COD / Prepaid Stat Tiles -->
                <div class="row row-gap-3 mb-4">
                    <!-- COD Orders -->
                    <div class="col-xl-4 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">COD Orders</p>
                                        <h2 class="mb-2 fw-semibold" id="statCod">{{ number_format($codCount) }}</h2>
                                        <span class="stat-trend-badge flat" id="statCodSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-orange"><i class="ti ti-cash"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-01.svg') }}" alt="elemnt-01" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /COD Orders -->

                    <!-- Prepaid Orders -->
                    <div class="col-xl-4 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Prepaid Orders</p>
                                        <h2 class="mb-2 fw-semibold" id="statPrepaid">{{ number_format($prepaidCount) }}</h2>
                                        <span class="stat-trend-badge flat" id="statPrepaidSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-green"><i class="ti ti-credit-card"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-02.svg') }}" alt="elemnt-02" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Prepaid Orders -->

                    <!-- General Orders -->
                    <div class="col-xl-4 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">General Orders</p>
                                        <h2 class="mb-2 fw-semibold" id="statGeneral">{{ number_format($generalCount) }}</h2>
                                        <span class="stat-trend-badge flat" id="statGeneralSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-indigo"><i class="ti ti-package"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-03.svg') }}" alt="elemnt-03" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /General Orders -->

                </div>
                <!-- end row - COD / Prepaid Stat Tiles -->

                <!-- start row - COD / Prepaid Summary Charts (Pie + Bar) -->
                <div class="row row-gap-3 mb-4">
                    <!-- COD / Prepaid Doughnut -->
                    <div class="col-xl-5 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">COD / Prepaid Summary</h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <canvas id="orderTypeSummaryChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- COD / Prepaid Bar Chart -->
                    <div class="col-xl-7 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">COD / Prepaid Summary — Bar View</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="orderTypeBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <!-- start row - COD vs Prepaid Trend Chart -->
                <div class="row mb-4">
                    <div class="col-xl-12 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">COD vs Prepaid Trend</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="orderTypeTrendChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <!-- start row - Pickup & Dispatch Stat Tiles -->
                <h6 class="mb-2"><i class="ti ti-truck-delivery me-1"></i>Pickup & Dispatch</h6>
                <div class="row row-gap-3 mb-4">
                    <!-- Ready for Pickup -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Ready for Pickup</p>
                                        <h2 class="mb-2 fw-semibold" id="pdStatReady">{{ number_format($pickupDispatchCounts['ready_for_pickup'] ?? 0) }}</h2>
                                        <span class="stat-trend-badge flat" id="pdStatReadySub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon" style="background:linear-gradient(135deg,#6f42c1,#9b59f6);box-shadow:0 4px 12px rgba(111,66,193,.32);"><i class="ti ti-calendar-event"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-01.svg') }}" alt="elemnt-01" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Ready for Pickup -->

                    <!-- Assigned for Pickup -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Assigned for Pickup</p>
                                        <h2 class="mb-2 fw-semibold" id="pdStatAssigned">{{ number_format($pickupDispatchCounts['assigned_for_pickup'] ?? 0) }}</h2>
                                        <span class="stat-trend-badge flat" id="pdStatAssignedSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-indigo"><i class="ti ti-truck-delivery"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-02.svg') }}" alt="elemnt-02" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Assigned for Pickup -->

                    <!-- Print Label -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Print Label</p>
                                        <h2 class="mb-2 fw-semibold" id="pdStatPrint">{{ number_format($pickupDispatchCounts['print_label'] ?? 0) }}</h2>
                                        <span class="stat-trend-badge flat" id="pdStatPrintSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-cyan"><i class="ti ti-printer"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-03.svg') }}" alt="elemnt-03" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Print Label -->

                    <!-- Ready to Dispatch -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Ready to Dispatch</p>
                                        <h2 class="mb-2 fw-semibold" id="pdStatDispatch">{{ number_format($pickupDispatchCounts['ready_to_dispatch'] ?? 0) }}</h2>
                                        <span class="stat-trend-badge flat" id="pdStatDispatchSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-orange"><i class="ti ti-truck"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-04.svg') }}" alt="elemnt-04" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Ready to Dispatch -->
                </div>
                <!-- end row - Pickup & Dispatch Stat Tiles -->

                <!-- start row - Pickup & Dispatch Charts (Pie + Bar) -->
                <div class="row row-gap-3 mb-4">
                    <!-- Pickup & Dispatch Doughnut -->
                    <div class="col-xl-5 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Pickup & Dispatch — Share</h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <canvas id="pickupDispatchChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Pickup & Dispatch Bar Chart -->
                    <div class="col-xl-7 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Pickup & Dispatch — Bar View</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="pickupDispatchBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <!-- start row - Recent Activity -->
                <div class="row row-gap-3 mb-4">
                    <!-- Recent Shipments -->
                    <div class="col-12 d-flex">
                        <div class="card flex-fill mb-0">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="mb-0"><i class="ti ti-truck me-1"></i>Recent Shipments</h6>
                                <a href="{{ route('admin.companies') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                @if($recentShipments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>AWB / Invoice</th>
                                                <th>Company</th>
                                                <th>Route</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentShipments as $shipment)
                                            <tr>
                                                <td>
                                                    <span class="fw-semibold d-block">{{ $shipment->awb_number ?? '—' }}</span>
                                                    <small class="text-muted">{{ $shipment->invoice_number ?? '' }}</small>
                                                </td>
                                                <td>{{ $shipment->company_name ?? (trim(($shipment->first_name ?? '') . ' ' . ($shipment->last_name ?? '')) ?: '—') }}</td>
                                                <td>
                                                    <span class="d-block">{{ $shipment->pickup_city ?? '—' }}</span>
                                                    <small class="text-muted"><i class="ti ti-arrow-right me-1"></i>{{ $shipment->destination_city ?? '—' }}</small>
                                                </td>
                                                <td>
                                                    @if($shipment->invoice_amount)
                                                    <span class="fw-semibold">₹ {{ number_format($shipment->invoice_amount, 2) }}</span>
                                                    @else
                                                    <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $statusKey = $shipment->status;
                                                        $statusTitle = \App\Models\Tracking::getTitleForStatus($statusKey);
                                                        $statusBadgeColors = [
                                                            'delivered' => 'bg-success-subtle text-success',
                                                            'dispatched' => 'bg-primary-subtle text-primary',
                                                            'manifested' => 'bg-info-subtle text-info',
                                                            'cancelled' => 'bg-danger-subtle text-danger',
                                                            'disputed' => 'bg-danger-subtle text-danger',
                                                            'on_hold' => 'bg-warning-subtle text-warning',
                                                            'received' => 'bg-info-subtle text-info',
                                                            'confirm_pickup' => 'bg-warning-subtle text-warning',
                                                            'ready_to_dispatch' => 'bg-primary-subtle text-primary',
                                                            'packed' => 'bg-primary-subtle text-primary',
                                                            'draft' => 'bg-secondary-subtle text-secondary',
                                                            'ready' => 'bg-primary-subtle text-primary',
                                                            'ready_for_pickup' => 'bg-info-subtle text-info',
                                                            'assigned_for_pickup' => 'bg-primary-subtle text-primary',
                                                        ];
                                                    @endphp
                                                    <span class="dash-status-badge {{ $statusBadgeColors[$statusKey] ?? 'bg-secondary-subtle text-secondary' }}">{{ $statusTitle }}</span>
                                                </td>
                                                <td class="text-muted">{{ \Carbon\Carbon::parse($shipment->created_at)->format('d M, h:i A') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="ti ti-truck fs-24 text-muted"></i>
                                    <p class="text-muted mb-0">No shipments yet</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /Recent Shipments -->
                </div>
                <!-- end row - Recent Activity -->

            </div>
            <!-- End Content -->            

            <!-- Start Footer -->
            <footer class="footer d-block d-md-flex justify-content-between text-md-start text-center">
               <p class="mb-md-0 mb-1">Copyright &copy; <script type="text/javascript">document.write(new Date().getFullYear())</script> <a href="javascript:void(0);" class="link-primary text-decoration-underline">United Courier</a></p>
               <div class="d-flex align-items-center gap-2 footer-links justify-content-center justify-content-md-end">
                  <a href="javascript:void(0);">About</a>
                  <a href="javascript:void(0);">Terms</a>
                  <a href="javascript:void(0);">Contact Us</a>
               </div>
            </footer>
            <!-- End Footer -->

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

	<!-- Simplebar JS -->
	<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
	<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

    <!-- Admin Dashboard Charts Script -->
    <script>
        let customerSummaryChart = null;
        let shipmentDeliverySummaryChart = null;
        let shipmentTrendChart = null;
        let customerSummaryBarChart = null;
        let shipmentDeliveryBarChart = null;
        let orderTypeSummaryChart = null;
        let orderTypeBarChart = null;
        let orderTypeTrendChart = null;
        let pickupDispatchChart = null;
        let pickupDispatchBarChart = null;
        const sparkCharts = {};

        // ---- Helpers -------------------------------------------------------
        function formatNumber(value) {
            return Number(value || 0).toLocaleString('en-IN');
        }

        function formatCurrency(value) {
            return '₹ ' + formatNumber(Math.round(Number(value || 0)));
        }

        function isDarkTheme() {
            return document.documentElement.getAttribute('data-bs-theme') === 'dark';
        }

        function cssVar(name, fallback) {
            const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
            return value || fallback;
        }

        // Resolve template theme colors (CSS variables) for canvas rendering,
        // so charts stay consistent in both light and dark mode.
        function themeColors() {
            const dark = isDarkTheme();
            return {
                text: dark ? 'rgba(255, 255, 255, 0.85)' : 'rgba(30, 41, 59, 0.9)',
                subText: dark ? 'rgba(255, 255, 255, 0.55)' : 'rgba(100, 116, 139, 0.9)',
                grid: dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)',
                cardBg: dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(255, 255, 255, 0.9)',
                primary: cssVar('--crms-primary', '#2563eb')
            };
        }

        function applyChartDefaults() {
            const colors = themeColors();
            Chart.defaults.color = colors.text;
            Chart.defaults.borderColor = colors.grid;
        }

        // Skeleton shimmer overlay for chart canvases while data refreshes
        function showChartLoading() {
            document.querySelectorAll('.chart-card').forEach(card => {
                if (!card.querySelector('.skeleton-overlay')) {
                    const overlay = document.createElement('div');
                    overlay.className = 'skeleton-overlay';
                    overlay.style.cssText = 'position:absolute;inset:0;z-index:5;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.65);backdrop-filter:blur(2px);border-radius:12px;';
                    if (isDarkTheme()) overlay.style.background = 'rgba(20,25,35,0.7)';
                    overlay.innerHTML = '<div class="spinner-border text-primary" role="status" style="width:26px;height:26px;"></div>';
                    card.style.position = 'relative';
                    card.appendChild(overlay);
                }
            });
        }

        function hideChartLoading() {
            document.querySelectorAll('.skeleton-overlay').forEach(el => el.remove());
        }

        // Color palette for charts
        const customerColors = {
            totalRegistrations: '#5b5eff',
            kycPending: '#ff9f43',
            onboardedCustomers: '#ff4d4f',
            csb5Enabled: '#7367f0'
        };

        const shipmentStatusOrder = [
            'draft',
            'ready',
            'ready_for_pickup',
            'assigned_for_pickup',
            'confirm_pickup',
            'packed',
            'manifested',
            'dispatched',
            'ready_to_dispatch',
            'delivered',
            'cancelled',
            'disputed',
            'on_hold',
            'received'
        ];

        const statusColors = {
            draft: '#6c757d',
            ready: '#0d6efd',
            ready_for_pickup: '#6f42c1',
            assigned_for_pickup: '#198754',
            confirm_pickup: '#fd9f43',
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

        const deliveryColors = {
            delivered: '#198754',
            shipRocket: '#5b5eff',
            self: '#fd7e14',
            other: '#6c757d'
        };

        const orderTypeColors = {
            cod: '#fd7e14',
            prepaid: '#198754',
            general: '#5b5eff'
        };

        const pickupDispatchColors = {
            ready_for_pickup: '#6f42c1',
            assigned_for_pickup: '#6366f1',
            print_label: '#06b6d4',
            ready_to_dispatch: '#f59e0b'
        };

        const pickupDispatchTitles = {
            ready_for_pickup: 'Ready for Pickup',
            assigned_for_pickup: 'Assigned for Pickup',
            print_label: 'Print Label',
            ready_to_dispatch: 'Ready to Dispatch'
        };

        function loadChartData(filter, btnElement) {
            // Update active button
            document.querySelectorAll('.chart-filter-btn').forEach(btn => btn.classList.remove('active'));
            if (btnElement) btnElement.classList.add('active');

            showChartLoading();

            fetch('{{ route("admin.dashboard-chart-data") }}?filter=' + filter, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderCustomerSummaryChart(data.customerSummary);
                    renderCustomerSummaryBarChart(data.customerSummary);
                    renderShipmentDeliverySummaryChart(data.shipmentStatusCounts, data.statusMap, data.deliverySummary);
                    renderShipmentDeliveryBarChart(data.shipmentStatusCounts, data.statusMap, data.deliverySummary);
                    renderShipmentTrendChart(data.dateWiseCounts, data.filter);
                    renderOrderTypeSummaryChart(data.orderTypeSummary || {});
                    renderOrderTypeBarChart(data.orderTypeSummary || {});
                    renderOrderTypeTrendChart(data.orderTypeTrend || {}, data.filter);
                    updatePickupDispatchSection(data.pickupDispatchSummary || {}, data.pickupDispatchRows || [], data.filter);
                    updateShipmentDeliveryStatTiles(data.shipmentStatusCounts, data.deliverySummary, data.filter);
                    updateOrderTypeStatTiles(data.orderTypeSummary || {}, data.filter);
                    updateBusinessStatTiles(data.businessSummary || {});
                    updateKycMinis(data.customerSummary || {});
                    paintSparks(data.sparks || {});
                }
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
            })
            .finally(() => {
                hideChartLoading();
            });
        }

        function updateBusinessStatTiles(businessSummary) {
            var setTile = function(id, text) {
                var el = document.getElementById(id);
                if (el) el.textContent = text;
            };
            setTile('statRevenue', formatCurrency(businessSummary.revenue));
            setTile('statInTransit', formatNumber(businessSummary.inTransit));
            setTile('statShipmentsTile', formatNumber(businessSummary.totalShipments));
            setTile('statWalletTopups', formatCurrency(businessSummary.walletTopups));
            setTile('statSuccessRate', (businessSummary.successRate || 0) + '%');
        }

        function updateShipmentDeliveryStatTiles(statusCounts, deliverySummary, filter) {
            // Calculate total shipments from all status counts
            const totalShipments = Object.values(statusCounts).reduce((a, b) => a + b, 0);
            const delivered = statusCounts['delivered'] || 0;
            const shipRocket = deliverySummary.shipRocket || 0;
            const selfNetwork = deliverySummary.self || 0;

            // Filter label for sub-text
            const filterLabels = {
                today: 'today',
                yesterday: 'yesterday',
                this_month: 'this month',
                last_month: 'last month',
                last_year: 'last year'
            };
            const periodLabel = filterLabels[filter] || 'selected period';

            document.getElementById('statTotalShipments').textContent = formatNumber(totalShipments);
            document.getElementById('statTotalShipmentsSub').querySelector('span').textContent = 'for ' + periodLabel;

            document.getElementById('statDelivered').textContent = formatNumber(delivered);
            document.getElementById('statDeliveredSub').querySelector('span').textContent = 'for ' + periodLabel;

            document.getElementById('statShipRocket').textContent = formatNumber(shipRocket);
            document.getElementById('statShipRocketSub').querySelector('span').textContent = 'for ' + periodLabel;

            document.getElementById('statSelfNetwork').textContent = formatNumber(selfNetwork);
            document.getElementById('statSelfNetworkSub').querySelector('span').textContent = 'for ' + periodLabel;
        }

        function updateOrderTypeStatTiles(orderTypeSummary, filter) {
            const filterLabels = {
                today: 'today',
                yesterday: 'yesterday',
                this_month: 'this month',
                last_month: 'last month',
                last_year: 'last year'
            };
            const periodLabel = filterLabels[filter] || 'selected period';

            document.getElementById('statCod').textContent = formatNumber(orderTypeSummary.cod);
            document.getElementById('statCodSub').querySelector('span').textContent = 'for ' + periodLabel;

            document.getElementById('statPrepaid').textContent = formatNumber(orderTypeSummary.prepaid);
            document.getElementById('statPrepaidSub').querySelector('span').textContent = 'for ' + periodLabel;

            document.getElementById('statGeneral').textContent = formatNumber(orderTypeSummary.general);
            document.getElementById('statGeneralSub').querySelector('span').textContent = 'for ' + periodLabel;
        }

        function renderCustomerSummaryChart(customerSummary) {
            const labels = ['Registrations', 'KYC Pending', 'Onboarded', 'CSB5 Enabled'];
            const values = [
                customerSummary.totalRegistrations,
                customerSummary.kycPending,
                customerSummary.onboardedCustomers,
                customerSummary.csb5Enabled
            ];
            const colors = [
                customerColors.totalRegistrations,
                customerColors.kycPending,
                customerColors.onboardedCustomers,
                customerColors.csb5Enabled
            ];

            if (customerSummaryChart) {
                customerSummaryChart.destroy();
            }

            const ctx = document.getElementById('customerSummaryChart').getContext('2d');
            customerSummaryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 4,
                        borderColor: themeColors().cardBg,
                        borderRadius: 8,
                        spacing: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '72%'
                }
            });

            renderCustomerSummaryLegend(labels, values, colors);
        }

        function renderCustomerSummaryLegend(labels, values, colors) {
            const legend = document.getElementById('customerSummaryLegend');
            if (!legend) return;

            const total = values.reduce((a, b) => a + b, 0);
            legend.innerHTML = labels.map((label, index) => {
                const value = values[index] || 0;
                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';

                return `
                    <div class="d-flex align-items-center justify-content-between rounded-3 px-3 py-2 mb-2" style="background: ${colors[index]}14;">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 12px; height: 12px; border-radius: 50%; background: ${colors[index]}; display: inline-block;"></span>
                            <span class="text-muted fs-13">${label}</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-semibold text-dark">${value}</span>
                            <span class="text-muted fs-12 ms-1">${percentage}%</span>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function buildShipmentDeliveryData(statusCounts, statusMap, deliverySummary) {
            const labels = [];
            const values = [];
            const colors = [];
            const knownStatuses = new Set(shipmentStatusOrder);

            shipmentStatusOrder.forEach(status => {
                labels.push(statusMap[status] || status.replace(/_/g, ' '));
                values.push(statusCounts[status] || 0);
                colors.push(statusColors[status] || '#adb5bd');
            });

            Object.entries(statusCounts).forEach(([status, count]) => {
                if (!knownStatuses.has(status)) {
                    labels.push(statusMap[status] || status.replace(/_/g, ' '));
                    values.push(count || 0);
                    colors.push(statusColors[status] || '#adb5bd');
                }
            });

            return { labels, values, colors };
        }

        function renderShipmentDeliverySummaryChart(statusCounts, statusMap, deliverySummary) {
            const { labels, values, colors } = buildShipmentDeliveryData(statusCounts, statusMap, deliverySummary);

            if (shipmentDeliverySummaryChart) {
                shipmentDeliverySummaryChart.destroy();
            }

            const ctx = document.getElementById('shipmentDeliverySummaryChart').getContext('2d');
            shipmentDeliverySummaryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: themeColors().cardBg,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                usePointStyle: true,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '55%'
                }
            });
        }

        function renderShipmentTrendChart(dateWiseCounts, filter) {
            const labels = Object.keys(dateWiseCounts);
            const values = Object.values(dateWiseCounts);

            // Format labels for display
            const displayLabels = labels.map(label => {
                if (filter === 'last_year') {
                    // Format "2025-01" as "Jan 2025"
                    const [year, month] = label.split('-');
                    const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    return monthNames[parseInt(month) - 1] + ' ' + year;
                } else {
                    // Format "2025-06-15" as "15 Jun"
                    const parts = label.split('-');
                    const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    return parseInt(parts[2]) + ' ' + monthNames[parseInt(parts[1]) - 1];
                }
            });

            if (shipmentTrendChart) {
                shipmentTrendChart.destroy();
            }

            const ctx = document.getElementById('shipmentTrendChart').getContext('2d');
            const colors = themeColors();
            const gradient = ctx.createLinearGradient(0, 0, 0, 320);
            gradient.addColorStop(0, 'rgba(255, 159, 67, 0.35)');
            gradient.addColorStop(1, 'rgba(255, 159, 67, 0.02)');

            shipmentTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: displayLabels,
                    datasets: [{
                        label: 'Shipments Created',
                        data: values,
                        backgroundColor: gradient,
                        borderColor: '#ff9f43',
                        borderWidth: 3,
                        pointBackgroundColor: '#ff9f43',
                        pointBorderColor: colors.cardBg,
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                font: { size: 12 }
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
                                font: { size: 11 }
                            },
                            grid: {
                                color: colors.grid
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 11 },
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

        function renderCustomerSummaryBarChart(customerSummary) {
            const labels = ['Registrations', 'KYC Pending', 'Onboarded', 'CSB5 Enabled'];
            const values = [
                customerSummary.totalRegistrations,
                customerSummary.kycPending,
                customerSummary.onboardedCustomers,
                customerSummary.csb5Enabled
            ];
            const colors = [
                customerColors.totalRegistrations,
                customerColors.kycPending,
                customerColors.onboardedCustomers,
                customerColors.csb5Enabled
            ];

            if (customerSummaryBarChart) {
                customerSummaryBarChart.destroy();
            }

            const ctx = document.getElementById('customerSummaryBarChart').getContext('2d');
            customerSummaryBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Customer Summary',
                        data: values,
                        backgroundColor: colors,
                        borderColor: colors,
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 11 }
                            },
                            grid: {
                                color: themeColors().grid
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 12 }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        function renderShipmentDeliveryBarChart(statusCounts, statusMap, deliverySummary) {
            const { labels: allLabels, values: allValues, colors: allColors } = buildShipmentDeliveryData(statusCounts, statusMap, deliverySummary);

            if (shipmentDeliveryBarChart) {
                shipmentDeliveryBarChart.destroy();
            }

            const ctx = document.getElementById('shipmentDeliveryBarChart').getContext('2d');
            shipmentDeliveryBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: allLabels,
                    datasets: [{
                        label: 'Shipment & Delivery',
                        data: allValues,
                        backgroundColor: allColors.map(c => c + 'cc'),
                        borderColor: allColors,
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 11 }
                            },
                            grid: {
                                color: themeColors().grid
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 11 },
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

        function renderOrderTypeSummaryChart(orderTypeSummary) {
            const labels = ['COD', 'Prepaid', 'General'];
            const values = [
                orderTypeSummary.cod || 0,
                orderTypeSummary.prepaid || 0,
                orderTypeSummary.general || 0
            ];
            const colors = [orderTypeColors.cod, orderTypeColors.prepaid, orderTypeColors.general];

            if (orderTypeSummaryChart) {
                orderTypeSummaryChart.destroy();
            }

            const ctx = document.getElementById('orderTypeSummaryChart').getContext('2d');
            orderTypeSummaryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: themeColors().cardBg,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                usePointStyle: true,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '55%'
                }
            });
        }

        function renderOrderTypeBarChart(orderTypeSummary) {
            const labels = ['COD', 'Prepaid', 'General'];
            const values = [
                orderTypeSummary.cod || 0,
                orderTypeSummary.prepaid || 0,
                orderTypeSummary.general || 0
            ];
            const colors = [orderTypeColors.cod, orderTypeColors.prepaid, orderTypeColors.general];

            if (orderTypeBarChart) {
                orderTypeBarChart.destroy();
            }

            const ctx = document.getElementById('orderTypeBarChart').getContext('2d');
            orderTypeBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Orders by Type',
                        data: values,
                        backgroundColor: colors.map(c => c + 'cc'),
                        borderColor: colors,
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 11 } },
                            grid: { color: themeColors().grid }
                        },
                        x: {
                            ticks: { font: { size: 12 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        function formatTrendLabel(label, filter) {
            if (filter === 'last_year') {
                const [year, month] = label.split('-');
                const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                return monthNames[parseInt(month) - 1] + ' ' + year;
            }
            const parts = label.split('-');
            const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return parseInt(parts[2]) + ' ' + monthNames[parseInt(parts[1]) - 1];
        }

        function renderOrderTypeTrendChart(orderTypeTrend, filter) {
            const rawLabels = orderTypeTrend.labels || [];
            const displayLabels = rawLabels.map(label => formatTrendLabel(label, filter));
            const codValues = orderTypeTrend.cod || [];
            const prepaidValues = orderTypeTrend.prepaid || [];

            if (orderTypeTrendChart) {
                orderTypeTrendChart.destroy();
            }

            const ctx = document.getElementById('orderTypeTrendChart').getContext('2d');
            const colors = themeColors();

            orderTypeTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: displayLabels,
                    datasets: [
                        {
                            label: 'COD',
                            data: codValues,
                            borderColor: orderTypeColors.cod,
                            backgroundColor: orderTypeColors.cod,
                            borderWidth: 3,
                            pointBackgroundColor: orderTypeColors.cod,
                            pointBorderColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointRadius: 4,
                            fill: false,
                            tension: 0.42
                        },
                        {
                            label: 'Prepaid',
                            data: prepaidValues,
                            borderColor: orderTypeColors.prepaid,
                            backgroundColor: orderTypeColors.prepaid,
                            borderWidth: 3,
                            pointBackgroundColor: orderTypeColors.prepaid,
                            pointBorderColor: colors.cardBg,
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointRadius: 4,
                            fill: false,
                            tension: 0.42
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { usePointStyle: true, font: { size: 12 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 11 } },
                            grid: { color: colors.grid }
                        },
                        x: {
                            ticks: { font: { size: 11 }, maxRotation: 45, minRotation: 0 },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // ---- Pickup & Dispatch (display-only, follows the same date filter) ----
        const pickupDispatchOrder = ['ready_for_pickup', 'assigned_for_pickup', 'print_label', 'ready_to_dispatch'];

        const pickupStageStyle = {
            ready_for_pickup: 'background:rgba(111,66,193,.12);color:#6f42c1;',
            assigned_for_pickup: 'background:rgba(99,102,241,.12);color:#6366f1;',
            received: 'background:rgba(6,182,212,.12);color:#06b6d4;',
            dispatched: 'background:rgba(6,182,212,.12);color:#06b6d4;',
            ready_to_dispatch: 'background:rgba(245,158,11,.14);color:#b45309;'
        };

        const pickupStageTitle = {
            ready_for_pickup: 'Ready for Pickup',
            assigned_for_pickup: 'Assigned for Pickup',
            received: 'Print Label',
            dispatched: 'Print Label',
            ready_to_dispatch: 'Ready to Dispatch'
        };

        function escHtml(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function buildPickupDispatchData(summary) {
            const labels = pickupDispatchOrder.map(key => pickupDispatchTitles[key]);
            const values = pickupDispatchOrder.map(key => Number(summary[key] || 0));
            const colors = pickupDispatchOrder.map(key => pickupDispatchColors[key]);
            return { labels, values, colors };
        }

        function renderPickupDispatchChart(summary) {
            const { labels, values, colors } = buildPickupDispatchData(summary);

            if (pickupDispatchChart) {
                pickupDispatchChart.destroy();
            }

            const ctx = document.getElementById('pickupDispatchChart').getContext('2d');
            pickupDispatchChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: themeColors().cardBg,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                usePointStyle: true,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '55%'
                }
            });
        }

        function renderPickupDispatchBarChart(summary) {
            const { labels, values, colors } = buildPickupDispatchData(summary);

            if (pickupDispatchBarChart) {
                pickupDispatchBarChart.destroy();
            }

            const ctx = document.getElementById('pickupDispatchBarChart').getContext('2d');
            pickupDispatchBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pickup & Dispatch',
                        data: values,
                        backgroundColor: colors.map(c => c + 'cc'),
                        borderColor: colors,
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 11 } },
                            grid: { color: themeColors().grid }
                        },
                        x: {
                            ticks: { font: { size: 11 }, maxRotation: 30, minRotation: 0 },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        function formatPdDate(value) {
            if (!value) return '—';
            const d = new Date(String(value).replace(' ', 'T'));
            if (isNaN(d.getTime())) return escHtml(value);
            return d.toLocaleString('en-IN', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit', hour12: true });
        }

        function renderPdLiveTable(rows) {
            const tbody = document.getElementById('pdLiveTableBody');
            if (!tbody) return;

            const top5 = (rows || []).slice(0, 5);
            if (top5.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No shipments in pickup / dispatch stages</td></tr>';
                return;
            }

            tbody.innerHTML = top5.map((row, idx) => {
                const name = ((row.first_name || '') + ' ' + (row.last_name || '')).trim();
                const customer = escHtml(row.company_name || name || '—');
                const stage = pickupStageTitle[row.status] || escHtml(String(row.status || '—').replace(/_/g, ' '));
                return '<tr>' +
                    '<td>' + (idx + 1) + '</td>' +
                    '<td><span class="fw-semibold">' + escHtml(row.awb_number || '—') + '</span></td>' +
                    '<td>' + customer + '</td>' +
                    '<td><span class="dash-status-badge" style="' + (pickupStageStyle[row.status] || '') + '">' + escHtml(stage) + '</span></td>' +
                    '<td class="text-muted">' + formatPdDate(row.created_at) + '</td>' +
                    '</tr>';
            }).join('');
        }

        function updatePickupDispatchSection(summary, rows, filter) {
            renderPickupDispatchChart(summary);
            renderPickupDispatchBarChart(summary);
            renderPdLiveTable(rows);

            const filterLabels = {
                today: 'today',
                yesterday: 'yesterday',
                this_month: 'this month',
                last_month: 'last month',
                last_year: 'last year'
            };
            const periodLabel = filterLabels[filter] || 'selected period';

            const setTile = function(valueId, subId, value) {
                const valueEl = document.getElementById(valueId);
                if (valueEl) valueEl.textContent = formatNumber(value);
                const subEl = document.getElementById(subId);
                if (subEl && subEl.querySelector('span')) subEl.querySelector('span').textContent = 'for ' + periodLabel;
            };
            setTile('pdStatReady', 'pdStatReadySub', summary.ready_for_pickup);
            setTile('pdStatAssigned', 'pdStatAssignedSub', summary.assigned_for_pickup);
            setTile('pdStatPrint', 'pdStatPrintSub', summary.print_label);
            setTile('pdStatDispatch', 'pdStatDispatchSub', summary.ready_to_dispatch);

            // Display-only top tabs follow the same filter (text update only, still no actions).
            const setTab = function(id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = formatNumber(value);
            };
            setTab('pdTabReady', summary.ready_for_pickup);
            setTab('pdTabAssigned', summary.assigned_for_pickup);
            setTab('pdTabPrint', summary.print_label);
            setTab('pdTabDispatch', summary.ready_to_dispatch);
        }

        function updateKycMinis(customerSummary) {
            var split = customerSummary.kycSplit || {};
            var setMini = function(id, value) {
                var el = document.getElementById(id);
                if (el) el.textContent = formatNumber(value);
            };
            setMini('kycMiniPending', split.pending);
            setMini('kycMiniUnderReview', split.under_review);
            setMini('kycMiniApproved', split.approved);
            setMini('kycMiniRejected', split.rejected);
        }

        // Tiny tile sparklines (last 7 days, real data from backend).
        function drawSpark(canvasId, values, color) {
            var canvas = document.getElementById(canvasId);
            if (!canvas || typeof Chart === 'undefined') return;
            if (sparkCharts[canvasId]) {
                sparkCharts[canvasId].destroy();
            }
            var ctx = canvas.getContext('2d');
            var gradient = ctx.createLinearGradient(0, 0, 0, 38);
            gradient.addColorStop(0, color + '55');
            gradient.addColorStop(1, color + '05');
            sparkCharts[canvasId] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: (values || []).map(function(_, i) { return i; }),
                    datasets: [{
                        data: values || [],
                        borderColor: color,
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 3,
                        fill: true,
                        tension: 0.45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: {
                        x: { display: false },
                        y: { display: false, beginAtZero: true }
                    },
                    events: []
                }
            });
        }

        function paintSparks(sparks) {
            drawSpark('sparkRegistrations', sparks.registrations, '#5b5eff');
            drawSpark('sparkRevenue', sparks.revenue, '#1abe17');
            drawSpark('sparkShipments', sparks.shipments, '#2f80ed');
            drawSpark('sparkWallet', sparks.wallet, '#7367f0');
            drawSpark('sparkOnboarded', sparks.onboarded, '#1abe17');
            drawSpark('sparkCsb', sparks.csb5, '#ff4d4f');
        }

        // ---- Entrance reveal + count-up animations ----
        var dashReduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function initDashAnimations() {
            var items = document.querySelectorAll('.content > .hero-banner, .content > .row');
            if (!('IntersectionObserver' in window) || dashReduceMotion) return;
            items.forEach(function(el, i) {
                el.classList.add('dash-anim');
                el.style.setProperty('--d', Math.min(i * 0.06, 0.4) + 's');
            });
            var obs = new IntersectionObserver(function(entries) {
                entries.forEach(function(en) {
                    if (en.isIntersecting) { en.target.classList.add('in'); obs.unobserve(en.target); }
                });
            }, { threshold: 0.08 });
            items.forEach(function(el) { obs.observe(el); });
        }

        function animateCountUp() {
            if (dashReduceMotion) return;
            document.querySelectorAll('.tile-value, .hs-value, .dash-stat-card h2').forEach(function(el) {
                var raw = el.textContent.trim();
                var num = parseFloat(raw.replace(/[^0-9.\-]/g, ''));
                if (isNaN(num)) return;
                var decimals = (raw.replace(/,/g, '').split('.')[1] || '').length;
                var prefix = raw.charAt(0) === '₹' ? '₹ ' : '';
                var suffix = raw.charAt(raw.length - 1) === '%' ? '%' : '';
                var start = null, dur = 900;
                function step(ts) {
                    if (!start) start = ts;
                    var p = Math.min((ts - start) / dur, 1);
                    var eased = 1 - Math.pow(1 - p, 3);
                    var val = num * eased;
                    el.textContent = prefix + val.toLocaleString('en-IN', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) + suffix;
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            });
        }

        // Load default chart data on page load
        document.addEventListener('DOMContentLoaded', function() {
            applyChartDefaults();
            initDashAnimations();
            animateCountUp();
            paintSparks(@json($sparks ?? []));
            loadChartData('this_month', document.querySelector('.chart-filter-btn[data-filter="this_month"]'));

            // Re-apply theme-aware chart styling when the theme changes
            const themeObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                        applyChartDefaults();
                        loadChartData(
                            document.querySelector('.chart-filter-btn.active')?.getAttribute('data-filter') || 'this_month',
                            document.querySelector('.chart-filter-btn.active')
                        );
                    }
                });
            });
            themeObserver.observe(document.documentElement, { attributes: true });
        });
    </script>

</body>

</html>
