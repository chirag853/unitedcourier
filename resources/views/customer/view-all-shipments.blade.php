<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>View All Shipments | United Courier</title>
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
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/plugins/tabler-icons/tabler-icons.min.css">
    <style>
        .card {
            background: #fff;
            border-radius: 20px;
        }

        .shipment-status-filters {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 7px !important;
            overflow-x: auto;
            white-space: nowrap;
            padding: 3px 2px 7px;
            scrollbar-width: none;
        }

        .shipment-status-filters::-webkit-scrollbar {
            display: none;
        }

        .shipment-status-filters .status-filter-btn {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 6px 12px !important;
            border: 1px solid #e7ebf3;
            border-radius: 10px !important;
            color: #52627a;
            background: #f8faff;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1px;
            line-height: 1.2;
            transition: color .2s ease, background-color .2s ease, border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .shipment-status-filters .status-filter-btn:hover {
            color: #2f66f3;
            background: #eef4ff;
            border-color: #c9d9ff;
            transform: translateY(-1px);
        }

        .shipment-status-filters .status-filter-btn.btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #2f66f3, #4f87ff);
            border-color: #2f66f3;
            box-shadow: 0 4px 10px rgba(47, 102, 243, .2);
        }

        .shipment-status-filters .status-filter-btn .badge {
            min-width: 19px;
            height: 19px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 6px !important;
            padding: 2px 5px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
        }

        .shipment-status-filters .status-filter-btn.btn-primary .badge {
            color: #2f66f3 !important;
            background: rgba(255, 255, 255, .95) !important;
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
        .shipment-heading-row {
            flex-wrap: nowrap;
        }

        .shipment-route-line {
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 2px;
            transform: translateY(-50%);
            background: repeating-linear-gradient(
                90deg,
                #9ca3af 0 7px,
                transparent 7px 12px
            );
            background-size: 12px 2px;
            animation: shipment-route-flow .6s linear infinite;
        }

        .shipment-route-plane {
            position: absolute;
            left: 50%;
            z-index: 1;
            padding: 0 4px;
            line-height: 1;
            background: #fff;
            transform: translateX(-50%);
        }

        @keyframes shipment-route-flow {
            to {
                background-position: 12px 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .shipment-route-line {
                animation: none;
            }
        }

        .bulk-actions-row {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: flex-end;
            width: auto;
            margin-left: auto;
        }

        .bulk-action-bar {
            display: none;
            flex: 0 0 auto;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            width: auto;
        }

        .bulk-action-bar.is-visible {
            display: flex;
        }

        @media (max-width: 575.98px) {
            .shipment-heading-row {
                align-items: flex-start !important;
                flex-direction: column;
            }

            .bulk-actions-row {
                width: 100%;
            }

            .bulk-action-bar.is-visible {
                width: 100%;
                flex-wrap: wrap;
            }
        }

        .btn-cancel {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
            padding: 4px 12px;
            font-size: 13px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-cancel:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: #fff;
        }
        .btn-cancel:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .detail-section {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        .detail-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        .detail-section h6 {
            color: #2563eb;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 13px;
        }
        .detail-row .label {
            color: #6c757d;
            min-width: 140px;
        }
        .detail-row .value {
            color: #212529;
            font-weight: 500;
            text-align: right;
            flex: 1;
        }
        .tracking-number-box {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            padding: 12px 16px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 16px;
        }
        .tracking-number-box .tracking-label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 4px;
        }
        .tracking-number-box .tracking-value {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .route-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }
        .route-box .route-point {
            text-align: center;
            flex: 1;
        }
        .route-box .route-point .route-city {
            font-size: 14px;
            font-weight: 600;
            color: #212529;
        }
        .route-box .route-point .route-label {
            font-size: 11px;
            color: #6c757d;
            margin-top: 2px;
        }
        .route-box .route-arrow {
            font-size: 20px;
            color: #2563eb;
            flex-shrink: 0;
        }
        .label-link {
            color: #dc3545;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }
        .label-link:hover {
            color: #c82333;
            text-decoration: underline;
        }

        .page-wrapper .content{
            padding:0.5rem !important;
        }

        .shipment-filter-card .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #52627a;
            margin-bottom: 5px;
        }

        .shipment-filter-card .form-control,
        .shipment-filter-card .form-select {
            min-height: 40px;
            border-radius: 9px;
        }

        .pagination-summary {
            color: #6c757d;
            font-size: 13px;
        }

        .shipment-pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            margin: 0;
        }

        .shipment-pagination .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            min-height: 38px;
            padding: 6px 10px;
            border: 1px solid #dfe5ef;
            border-radius: 6px;
            color: #243b63;
            background: #fff;
            font-size: 13px;
            font-weight: 600;
            box-shadow: none;
        }

        .shipment-pagination .page-link:hover {
            color: #fff;
            background: #2f66f3;
            border-color: #2f66f3;
        }

        .shipment-pagination .page-item.active .page-link {
            color: #fff;
            background: #2f66f3;
            border-color: #2f66f3;
        }

        .shipment-pagination .page-item.disabled .page-link {
            color: #a0a9b8;
            background: #f5f6f8;
            border-color: #e7ebf3;
        }

        /* Horizontal scrolling for the shipments table so wide draft
           columns never break the layout. The first 3 columns
           (checkbox, HAWB Number, Order Date) stay frozen for context. */
        .shipments-table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
        }

        .shipments-table-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .shipments-table-scroll::-webkit-scrollbar-thumb {
            background: #c9d9ff;
            border-radius: 6px;
        }

        .shipments-table-scroll::-webkit-scrollbar-track {
            background: #f1f4fb;
        }

        .shipments-table {
            /* Many columns are now hidden/merged, so the table no longer needs
               a 1500px floor. Keeping it smaller stops auto layout from pouring
               the leftover space into the Status / Action columns. */
            min-width: 1150px;
            white-space: nowrap;
        }

        .shipments-table thead th,
        .shipments-table tbody td {
            vertical-align: middle;
        }

        /* Freeze the first 3 columns (checkbox, HAWB, Created At) so the
           row identity is always visible while scrolling horizontally. */
        .shipments-table thead th.sticky-col,
        .shipments-table tbody td.sticky-col {
            position: sticky;
            background: #fff;
            z-index: 2;
            box-shadow: inset -1px 0 0 #dee2e6;
        }

        .shipments-table thead th.sticky-col {
            background: #f8f9fa;
            z-index: 3;
        }

        /* Checkbox is tiny; pin the column so it cannot absorb slack. */
        .shipments-table .sticky-col.col-1 { left: 0; width: 40px; min-width: 40px; max-width: 40px; }
        .shipments-table .sticky-col.col-2 { left: 40px; min-width: 130px; }
        /* Order Date content is short; cap the width so the column does not
           soak up the extra space left by wide-table auto layout. */
        .shipments-table .sticky-col.col-3 { left: 170px; width: 130px; min-width: 130px; max-width: 130px; }

        /* Status badge text is short; cap the column tightly so it fits the
           badge and cannot stretch to absorb leftover table space. */
        .shipments-table .status-col {
            width: 130px;
            min-width: 120px;
            max-width: 140px;
            text-align: center;
            /* white-space: nowrap; */
        }

        /* Action column holds up to three 32px icon buttons plus gaps;
           keep it compact so it does not stretch across empty space. */
        .shipments-table .action-col {
            width: 120px;
            min-width: 110px;
            max-width: 140px;
            text-align: center;
        }

        /* Tracking Number column holds the carrier tracking id (e.g. UPS /
           overseas airway bill). It is non-sticky, sits after the frozen
           Order Date column, and allows long ids to wrap instead of
           stretching the table. */
        .shipments-table .tracking-col {
            width: 180px;
            min-width: 160px;
            max-width: 220px;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        @media (max-width: 575.98px) {
            .shipments-table {
                min-width: 1080px;
            }
        }

        /* From / To grid must wrap normally even though the table is nowrap. */
        .shipments-route-grid {
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /* Give the From / To column enough width so it never becomes
           absurdly narrow on smaller screens. */
        .shipments-table .shipments-route-col {
            min-width: 360px;
        }

        /* Receiver Details column: clean vertical stack with proper spacing.
           Width is capped so the column stays compact and does not absorb
           the leftover space from the wide-table auto layout. */
        .shipments-table .receiver-details-col {
            width: 220px;
            min-width: 200px;
            max-width: 220px;
            vertical-align: top;
        }

        .receiver-details-stack {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 12px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .receiver-details-stack .receiver-name {
            font-weight: 600;
            color: #1f2937;
            line-height: 1.3;
            font-size:medium
        }

        .receiver-details-stack .receiver-line {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            color: #1f2937;
            line-height: 1.35;
            font-weight: 600;
        }

        .receiver-details-stack .receiver-icon {
            color: #1f2937;
            font-size: 12px;
            line-height: 1.35;
            flex-shrink: 0;
            margin-top: 2px;
        } 

        .receiver-details-stack .receiver-label {
            font-weight: 600;
            color: #374151;
            flex-shrink: 0;
            min-width: 44px;
        }

        .receiver-details-stack .receiver-value {
            flex: 1;
            min-width: 0;
        }

        /* Package Details column: billable / dead / volumetric weight,
           shown as a compact label-value stack per package.
           Width is capped so it cannot grow wider than its content needs. */
        .shipments-table .package-details-col {
            width: 160px;
            min-width: 210px;
            max-width: 240px;
            vertical-align: top;
        }

        .package-details-card {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 12px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .package-details-card + .package-details-card {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #dee2e6;
        }

        .package-details-title {
            font-weight: 600;
            color: #1f2937;
            line-height: 1.3;
        }

        .package-details-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            line-height: 1.35;
        }

        .package-details-label {
            color: #6b7280;
            flex-shrink: 0;
        }

        .package-details-value {
            font-weight: 600;
            color: #1f2937;
            text-align: right;
            min-width: 0;
        }

        /* Draft view: the HAWB column is widened and hosts the destination
           ISO & pin, reference number and invoice number stacked below the
           AWB number. col-3 (Created At) must shift to stay glued to it. */
        .shipments-table.shipments-table-draft .sticky-col.col-2 {
            min-width: 220px;
        }

        .shipments-table.shipments-table-draft .sticky-col.col-3 {
            left: 260px;
        }

        .hawb-sub-info {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px dashed #dee2e6;
            display: flex;
            flex-direction: column;
            gap: 3px;
            font-size: 11px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .hawb-sub-row {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            line-height: 1.35;
        }

        .hawb-sub-label {
            color: #6b7280;
            flex-shrink: 0;
            font-weight: 600;
            min-width: 32px;
        }

        .hawb-sub-value {
            color: #1f2937;
            min-width: 0;
        }

        /* ===== Pickup Date Options (Assign for Pickup modal) ===== */
        .md-pickup-date-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 8px;
        }

        .md-pickup-date-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .md-pickup-date-option {
            flex: 1 1 90px;
            min-width: 90px;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            text-align: center;
            cursor: pointer;
            transition: all .2s ease;
        }

        .md-pickup-date-option:hover {
            border-color: #2f66f3;
            background: #eaf0fe;
        }

        .md-pickup-date-option.selected {
            border-color: #2f66f3;
            background: #eaf0fe;
            box-shadow: 0 4px 12px rgba(47, 102, 243, .18);
        }

        .md-pickup-date-option .dp-label {
            display: block;
            font-weight: 700;
            font-size: 12px;
            color: #1e293b;
        }

        .md-pickup-date-option.selected .dp-label {
            color: #2f66f3;
        }

        .md-pickup-date-option .dp-date {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
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
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2 flex-wrap">
                    <div>
                        <!-- <h4 class="mb-1">View All Orders</h4> -->
                    </div>
                    <div class="gap-2 d-flex align-items-center justify-content-end flex-wrap">
                        <div class="bulk-actions-row">
                            <button class="btn btn-success rounded-pill px-4 py-2" id="bulkManifestBtn" style="display:none;">
                                <i class="ti ti-package-export me-1"></i> Bulk Manifest (Retry)
                            </button>

                            <div id="draftBulkActions" class="bulk-action-bar">
                                <span id="draftBulkTotal" class="fw-bold text-success"></span>
                                <button type="button" class="btn btn-success btn-sm" id="bulkDraftPayBtn" disabled>
                                    <i class="ti ti-credit-card me-1"></i>Pay Selected
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="bulkDraftCancelBtn" disabled>
                                    <i class="ti ti-ban me-1"></i>Cancel Selected
                                </button>
                            </div>

                            <div id="readyBulkActions" class="bulk-action-bar">
                                <span id="readyBulkTotal" class="fw-bold text-primary"></span>
                                <div class="d-flex align-items-center gap-1">
                                    <label class="form-label mb-0 small fw-semibold">Label Size:</label>
                                    <select class="form-select form-select-sm bulk-label-size" style="width:auto;">
                                        <option value="a4" selected>A4</option>
                                        <option value="4x6">4x6</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="bulkReadyPrintBtn" disabled>
                                    <i class="ti ti-printer me-1"></i>Print Selected
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="bulkReadyCancelBtn" disabled>
                                    <i class="ti ti-ban me-1"></i>Cancel Selected
                                </button>
                            </div>

                            <div id="packedBulkActions" class="bulk-action-bar">
                                <span id="packedBulkTotal" class="fw-bold text-primary"></span>
                                <div class="d-flex align-items-center gap-1">
                                    <label class="form-label mb-0 small fw-semibold">Label Size:</label>
                                    <select class="form-select form-select-sm bulk-label-size" style="width:auto;">
                                        <option value="a4" selected>A4</option>
                                        <option value="4x6">4x6</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="bulkPackedPrintBtn" disabled>
                                    <i class="ti ti-printer me-1"></i>Print Selected
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="bulkPackedCancelBtn" disabled>
                                    <i class="ti ti-ban me-1"></i>Cancel Selected
                                </button>
                            </div>
                        </div>

                        <a href="{{ url('/customer/create-shipment') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-plus me-1"></i> Add New Orders
                        </a>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <div id="alertContainer"></div>
                <div class="card border-0 shadow-sm rounded-4 shipment-filter-card mb-2">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('customer.view-all-shipments') }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Customer / Consignee</label>
                                    <input type="search" name="customer_name" class="form-control" value="{{ request('customer_name') }}" placeholder="Customer name">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Shipper Name</label>
                                    <input type="search" name="shipper_name" class="form-control" value="{{ request('shipper_name') }}" placeholder="Company or contact">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">HAWB Number</label>
                                    <input type="search" name="awb_number" class="form-control" value="{{ request('awb_number') }}" placeholder="Enter AWB">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="all">All Statuses</option>
                                        @foreach(['draft' => 'Draft', 'ready' => 'Ready', 'packed' => 'Packed', 'manifested' => 'Manifested', 'ready_for_pickup' => 'Ready for Pickup', 'assigned_for_pickup' => 'In-Transit to Hub', 'received' => 'Received', 'dispatched' => 'Dispatched', 'cancelled' => 'Cancelled', 'delivered' => 'Delivered', 'disputed' => 'Disputed', 'on_hold' => 'On Hold'] as $value => $label)
                                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-12 d-flex gap-2 justify-content-end mt-3">
                                    <a href="{{ route('customer.view-all-shipments') }}" class="btn btn-light"><i class="ti ti-refresh me-1"></i>Reset</a>
                                    <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>Search & Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card border-0 shadow-sm rounded-4 shipment-status-card">
                    <div class="card-body p-3">
                        <div class="shipment-status-filters" aria-label="Shipment status filters">
                            @foreach(['all' => 'All Orders', 'draft' => 'Drafts', 'ready' => 'Ready', 'packed' => 'Packed', 'manifested' => 'Manifested', 'ready_for_pickup' => 'Ready for Pickup', 'assigned_for_pickup' => 'In-Transit to Hub', 'received' => 'Received', 'dispatched' => 'Dispatched', 'cancelled' => 'Cancelled', 'delivered' => 'Delivered', 'disputed' => 'Disputed', 'on_hold' => 'On Hold'] as $value => $label)
                                <a href="{{ request()->fullUrlWithQuery(['status' => $value, 'page' => null]) }}"
                                   class="btn {{ request('status', 'all') === $value ? 'btn-primary' : 'btn-light' }} rounded-pill status-filter-btn"
                                   data-filter="{{ $value }}">
                                    {{ $label }} <span class="badge {{ request('status', 'all') === $value ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $statusCounts[$value] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Shipments Table Card -->
                <div class="card border shadow">
                    <div class="card-body">
                        @php
                            // These flags are needed by the JavaScript at the bottom of the page even
                            // when the shipments table itself is empty, so they are computed here at the
                            // top level (before the @if/@else that renders the table) rather than inside
                            // the table-rendering branch.
                            $selectedStatus = request('status');
                            $manifestColumnLocked = in_array($selectedStatus, ['draft', 'ready', 'packed'], true);
                        @endphp
                        @if($invoices->isEmpty())
                            <div class="text-center py-5">
                                <i class="ti ti-package" style="font-size:48px;color:#ccc;"></i>
                                <p class="mt-3 text-muted">No shipments matched the selected filters.</p>
                                <a href="{{ route('customer.view-all-shipments') }}" class="btn btn-primary">Clear Filters</a>
                            </div>
                        @elseif(in_array($selectedStatus, ['manifested', 'ready_for_pickup'], true))
                            {{-- Manifested / Ready for Pickup tabs: grouped manifest table
                                (one row per manifest number with all its shipments collapsed) --}}
                            @if($manifestGroups->isEmpty())
                                <div class="text-center py-5">
                                    <i class="ti ti-package" style="font-size:48px;color:#ccc;"></i>
                                    <p class="mt-3 text-muted">{{ $selectedStatus === 'ready_for_pickup' ? 'No shipments ready for pickup found.' : 'No manifested shipments found.' }}</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table id="manifestGroupsTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Manifest Code</th>
                                                <th>Order Date</th>
                                                <th>Shipments</th>
                                                <th>Total Value</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($manifestGroups as $index => $manifest)
                                            <tr class="manifest-group-row">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <a href="{{ route('customer.manifest-detail', ['manifestNumber' => $manifest->manifest_number]) }}"
                                                       target="_blank"
                                                       class="badge bg-success text-decoration-none manifest-link"
                                                       title="Open manifest details in new tab"
                                                       style="white-space:nowrap;">
                                                        {{ $manifest->manifest_number }}
                                                        <i class="ti ti-external-link ms-1" style="font-size:11px;"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($manifest->manifest_created_at)
                                                        <div style="white-space:nowrap;">{{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('d-m-Y') }}</div>
                                                        <div class="text-muted" style="font-size:11px;white-space:nowrap;">{{ \Carbon\Carbon::parse($manifest->manifest_created_at)->format('h:i A') }}</div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $manifest->shipment_count }}</span>
                                                </td>
                                                <td style="font-weight:600;color:#0f172a;">
                                                    {{ number_format($manifest->total_value, 2) }}
                                                </td>
                                                <td>
                                                    @php
                                                        $manifestStatusVal = (int) ($manifest->status ?? \App\Models\Manifest::STATUS_PICKUP);
                                                        $manifestStatusLabel = \App\Models\Manifest::statusLabel($manifestStatusVal);
                                                        $manifestStatusBadge = \App\Models\Manifest::statusBadgeClass($manifestStatusVal);
                                                    @endphp
                                                    <span class="badge {{ $manifestStatusBadge }}">
                                                        {{ $manifestStatusLabel }} ({{ $manifestStatusVal }})
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('customer.manifest-detail', ['manifestNumber' => $manifest->manifest_number]) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary btn-icon"
                                                       title="View Manifest Details">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    @if($manifestStatusVal === \App\Models\Manifest::STATUS_OPEN)
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-secondary btn-icon close-manifest-btn"
                                                                title="Close Manifest"
                                                                data-manifest-number="{{ $manifest->manifest_number }}">
                                                            <i class="ti ti-lock"></i>
                                                        </button>
                                                    @elseif(in_array($manifestStatusVal, [\App\Models\Manifest::STATUS_CLOSE, \App\Models\Manifest::STATUS_PICKUP], true))
                                                        <a href="{{ route('customer.manifest-label', ['manifestNumber' => $manifest->manifest_number]) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-outline-success btn-icon"
                                                           title="Print Manifest Label">
                                                            <i class="ti ti-printer"></i>
                                                        </a>
                                                        <a href="{{ route('customer.manifest-document', ['manifestNumber' => $manifest->manifest_number]) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-outline-info btn-icon"
                                                           title="Download Manifest Document">
                                                            <i class="ti ti-file-text"></i>
                                                        </a>
                                                        @if($manifestStatusVal === \App\Models\Manifest::STATUS_CLOSE)
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-primary btn-icon assign-pickup-btn"
                                                                    title="Assign for Pickup"
                                                                    data-manifest-number="{{ $manifest->manifest_number }}">
                                                                <i class="ti ti-truck"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            @endif
                        @else
                            @php
                                $isDraftView = request('status') === 'draft';
                                $isAllOrdersView = $selectedStatus === null || $selectedStatus === 'all';
                                $showActionColumn = in_array($selectedStatus, ['draft', 'ready', 'packed'], true);
                                $showHawbSubInfo = $isDraftView || in_array($selectedStatus, ['ready', 'packed', 'manifested'], true);
                                $postPackedStatuses = ['packed', 'manifested', 'ready_for_pickup', 'assigned_for_pickup', 'received', 'confirm_pickup', 'dispatched', 'cancelled', 'delivered', 'disputed', 'on_hold'];
                                $hideCurrencyColumn = $isAllOrdersView || $isDraftView || $selectedStatus === 'ready' || in_array($selectedStatus, $postPackedStatuses, true);
                                $hideIncotermsAndPayColumns = $isAllOrdersView || $isDraftView || $selectedStatus === 'ready' || in_array($selectedStatus, $postPackedStatuses, true);
                                $hidePrintLabelColumn = $isAllOrdersView || $showActionColumn || in_array($selectedStatus, $postPackedStatuses, true);
                                // The Manifest column is only meaningful once a shipment has been manifested.
                                // It is hidden entirely on the dedicated Draft/Ready/Packed tabs, and on the
                                // All Orders view it is also hidden whenever no shipment on the current page
                                // has progressed past 'packed' — so a page full of draft/ready/packed rows
                                // never shows the Manifest column. When manifested rows exist, the column
                                // stays visible and only those rows show a manifest number (the per-row
                                // cell below is hidden for draft/ready/packed rows).
                                $hasManifestedRows = $invoices->getCollection()->contains(function ($inv) {
                                    if ($inv->status === 'cancelled') {
                                        return false;
                                    }
                                    return $inv->shipperInfo && in_array($inv->shipperInfo->status, [
                                        'manifested', 'ready_for_pickup', 'assigned_for_pickup', 'received', 'confirm_pickup',
                                        'dispatched', 'delivered', 'disputed', 'on_hold',
                                    ], true);
                                });
                                $hideManifestColumn = in_array($selectedStatus, ['draft', 'ready', 'packed'], true) || ! $hasManifestedRows;
                                $hideCancelColumn = $isAllOrdersView || in_array($selectedStatus, $postPackedStatuses, true);
                                $hideTrackingColumn = $isDraftView || $selectedStatus === 'ready';
                            @endphp
                            <div class="table-responsive shipments-table-scroll">
                                <table id="shipmentsTable" @class(['table', 'table-bordered', 'table-hover', 'shipments-table', 'shipments-table-draft' => $showHawbSubInfo])>
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sticky-col col-1"><input type="checkbox" id="selectAllCheckbox" style="display:none;"></th>
                                            <th class="sticky-col col-2">HAWB Number</th>
                                            <th class="sticky-col col-3">Order Date</th>
                                            @if(!$hideTrackingColumn)
                                            <th class="tracking-col">Tracking Number</th>
                                            @endif
                                            <th class="receiver-details-col" @class(['d-none' => !$isDraftView])>Receiver Details</th>
                                            <th class="package-details-col" @class(['d-none' => !$isDraftView])>Package Details</th>
                                            <!-- <th>Ship From → Ship To</th> -->
                                            {{-- From / To column temporarily hidden
                                            <th class="shipments-route-col">From / To</th>
                                            --}}
                                            <!-- <th>Invoice Date</th> -->
                                            {{-- Amount column temporarily hidden
                                            <th>Amount</th>
                                            --}}
                                            <th @class(['d-none' => $hideCurrencyColumn])>Currency</th>
                                            <th @class(['d-none' => $hideIncotermsAndPayColumns])>Incoterms</th>
                                            <!-- <th>Reference No.</th> -->
                                            <th class="status-col">Status</th>
                                            {{-- Print Label / Pay Now columns merged into the Action column
                                            <th @class(['d-none' => $hidePrintLabelColumn])>Print Label</th>
                                            <th @class(['d-none' => $hideIncotermsAndPayColumns])>Pay Now</th>
                                            --}}
                                            <th @class(['manifest-col', 'd-none' => $hideManifestColumn])>Manifest</th>
                                            <th class="action-col" @class(['text-center', 'd-none' => !$showActionColumn])>Action</th>
                                            {{-- Standalone Cancel column merged into Action column
                                            <th @class(['text-center', 'd-none' => $hideCancelColumn || $showActionColumn])>Cancel</th>
                                            --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $index => $invoice)
                                        @php
                                            $rowStatus = 'draft';
                                            if ($invoice->status === 'cancelled') {
                                                $rowStatus = 'cancelled';
                                            } elseif ($invoice->shipperInfo && $invoice->shipperInfo->status) {
                                                $rowStatus = $invoice->shipperInfo->status;
                                            }
                                            // Per-row manifest cell visibility: hidden for draft/ready/packed rows
                                            // so only manifested shipments show a manifest number in All Orders view.
                                            $hideManifestCell = in_array($rowStatus, ['draft', 'ready', 'packed'], true);

                                            // All tabs: COD / FOC / prepare rows (shipment_type 2/3/4)
                                            // show neither print label nor manifest number - just "-".
                                            // NOTE: shipment_type must stay in the shipperInfo select list
                                            // in viewAllShipments(), otherwise it reads null here.
                                            // Types: 1=general, 2=cod, 3=foc, 4=prepare.
                                            $shipmentTypeId = (int) ($invoice->shipperInfo?->shipment_type ?? 0);
                                            $isCodFocShipment = in_array($shipmentTypeId, [2, 3, 4], true) || $invoice->status === 'cod';
                                            $hideCodFocPrintManifest = $isCodFocShipment;

                                            $selectedRate = $invoice->shipperInfo
                                                ? $invoice->shipperInfo->serviceRate
                                                : null;
                                            $shipmentAmount = $invoice->shipperInfo
                                                && $invoice->shipperInfo->total_price !== null
                                                && (float) $invoice->shipperInfo->total_price > 0
                                                ? (float) $invoice->shipperInfo->total_price
                                                : ($selectedRate
                                                    ? $selectedRate->inclusive_total
                                                    : round((float) $invoice->invoiceItems->sum('amount'), 2));
                                        @endphp
                                        <tr id="invoice-row-{{ $invoice->id }}" data-status="{{ $rowStatus }}" data-shipper-id="{{ $invoice->shipperInfo ? $invoice->shipperInfo->id : '' }}" data-invoice-id="{{ $invoice->id }}" data-shipment-type="{{ $invoice->shipperInfo?->shipment_type ?? '' }}" data-amount="{{ number_format($shipmentAmount, 2, '.', '') }}">
                                            <td class="text-center sticky-col col-1">
                                                <input type="checkbox" class="shipment-checkbox bulk-manifest-checkbox" data-shipper-id="{{ $invoice->shipperInfo ? $invoice->shipperInfo->id : '' }}" style="display:none;">
                                            </td>
                                            <td class="sticky-col col-2">
                                                @if($invoice->shipperInfo && $invoice->shipperInfo->awb_number)
                                                    <span class="badge bg-dark" style="cursor:pointer;"
                                                          data-invoice-id="{{ $invoice->id }}"
                                                          onclick="showShipmentDetail({{ $invoice->id }});">
                                                        {{ $invoice->shipperInfo->awb_number }}
                                                    </span>
                                                @else
                                                    <strong>{{ $invoice->invoice_number }}</strong>
                                                @endif
                                                @if($showHawbSubInfo)
                                                    @php
                                                        $hawbConsignee = $invoice->shipperInfo ? $invoice->shipperInfo->consigneeInfo : null;
                                                        $hawbDestName = $hawbConsignee?->delivery_destination;
                                                        $hawbIsoCode = $destinationIsoMap[$hawbDestName] ?? ($fallbackIsoMap[$hawbDestName] ?? '-');
                                                    @endphp
                                                    <div class="hawb-sub-info">
                                                        <div class="hawb-sub-row">
                                                            <span class="hawb-sub-label">Destination:</span>
                                                            <span class="hawb-sub-value">{{ $hawbIsoCode !== '' ? $hawbIsoCode : '-' }}{{ $hawbConsignee && $hawbConsignee->zip_code ? ' · '.$hawbConsignee->zip_code : '' }}</span>
                                                        </div>
                                                        <div class="hawb-sub-row">
                                                            <span class="hawb-sub-label">Reference number:</span>
                                                            <span class="hawb-sub-value">{{ $invoice->reference_number ?: '-' }}</span>
                                                        </div>
                                                        <div class="hawb-sub-row">
                                                            <span class="hawb-sub-label">Invoice number:</span>
                                                            <span class="hawb-sub-value">{{ $invoice->invoice_number ?: '-' }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="sticky-col col-3">
                                                @php
                                                    // Draft rows keep the original order date (created_at).
                                                    // Once a shipment leaves draft, show the last-updated date from
                                                    // the shipper record (every status change touches shipper_info),
                                                    // falling back to the invoice's own updated_at.
                                                    $orderDateSource = $rowStatus === 'draft'
                                                        ? $invoice->created_at
                                                        : ($invoice->shipperInfo?->updated_at ?: $invoice->updated_at);
                                                @endphp
                                                @if($orderDateSource)
                                                    <div>{{ date('d M Y', strtotime($orderDateSource)) }}</div>
                                                    <div class="text-muted" style="">{{ date('h:i A', strtotime($orderDateSource)) }}</div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            @if(!$hideTrackingColumn)
                                            <td class="tracking-col">
                                                @php
                                                    $rowTrackingNumber = in_array($rowStatus, ['packed', 'manifested'], true)
                                                        ? $invoice->shipperInfo?->shipmentTracking?->shipment_identification_number
                                                        : null;
                                                @endphp
                                                @if($rowTrackingNumber)
                                                    <span>{{ $rowTrackingNumber }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            @endif
                                            <td class="receiver-details-col" @class(['d-none' => !$isDraftView])>
                                                @php
                                                    $receiverConsignee = $invoice->shipperInfo ? $invoice->shipperInfo->consigneeInfo : null;
                                                    $receiverDisplayName = $receiverConsignee?->consignee_name ?: ($receiverConsignee?->contact_person ?: '-');
                                                @endphp
                                                <div class="receiver-details-stack">
                                                    <div class="receiver-name">
                                                        <span class="receiver-value"></span> {{ $receiverDisplayName }}
                                                    </div>
                                                    @if($receiverConsignee?->email)
                                                        <div class="receiver-line">
                                                            <!-- <i class="bi bi-envelope-fill receiver-icon"></i> -->
                                                            <!-- <span class="receiver-label"></span> -->
                                                            <span class="receiver-value">{{ $receiverConsignee->email }}</span>
                                                        </div>
                                                    @endif
                                                    @if($receiverConsignee?->phone_number)
                                                        <div class="receiver-line">
                                                            <!-- <i class="bi bi-telephone-fill receiver-icon"></i> -->
                                                            <!-- <span class="receiver-label"></span> -->
                                                            <span class="receiver-value">{{ $receiverConsignee->phone_number }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="package-details-col" @class(['d-none' => !$isDraftView])>
                                                @php
                                                    $packageShipper = $invoice->shipperInfo;
                                                    $packages = $packageShipper?->packageDimensions ?? collect();
                                                    $packageCount = is_countable($packages) ? count($packages) : 0;

                                                    $totalBillable = 0.0;
                                                    $totalDead = 0.0;
                                                    $totalVol = 0.0;
                                                    foreach ($packages as $package) {
                                                        if ($package->chargeable_weight !== null && $package->chargeable_weight !== '') {
                                                            $totalBillable += (float) $package->chargeable_weight;
                                                        }
                                                        if ($package->actual_weight_kg !== null && $package->actual_weight_kg !== '') {
                                                            $totalDead += (float) $package->actual_weight_kg;
                                                        }
                                                        if ($package->volumetric_weight !== null && $package->volumetric_weight !== '') {
                                                            $totalVol += (float) $package->volumetric_weight;
                                                        }
                                                    }
                                                @endphp
                                                @if($packageCount > 0)
                                                    <div class="package-details-card">
                                                        @if($packageCount > 1)
                                                            <div class="package-details-title">Total ({{ $packageCount }} pkgs)</div>
                                                        @endif
                                                        <div class="package-details-row">
                                                            <span class="package-details-label">Billable Wt.</span>
                                                            <span class="package-details-value">{{ $totalBillable > 0 ? number_format($totalBillable, 2).' kg' : '-' }}</span>
                                                        </div>
                                                        <div class="package-details-row">
                                                            <span class="package-details-label">Dead Wt.</span>
                                                            <span class="package-details-value">{{ $totalDead > 0 ? number_format($totalDead, 2).' kg' : '-' }}</span>
                                                        </div>
                                                        <div class="package-details-row">
                                                            <span class="package-details-label">Vol. Wt.</span>
                                                            <span class="package-details-value">{{ $totalVol > 0 ? number_format($totalVol, 2).' kg' : '-' }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            {{-- From / To column temporarily hidden
                                            <td class="shipments-route-col" style="font-size:12px;white-space:normal;">
                                                @php
                                                    $shipper = $invoice->shipperInfo;
                                                    $consignee = $shipper?->consigneeInfo;
                                                    $senderName = $shipper?->company_name ?: ($shipper?->contact_person ?: '-');
                                                    $receiverName = $consignee?->consignee_name ?: ($consignee?->contact_person ?: '-');
                                                @endphp
                                                <div class="align-items-center w-100 shipments-route-grid" style="display:grid;grid-template-columns:minmax(0, 1fr) 90px minmax(0, 1fr);column-gap:12px;">
                                                    <div style="min-width:0;white-space:normal;overflow-wrap:anywhere;word-break:break-word;">
                                                        <div>{{ $shipper?->state ?: '-' }}, {{ $shipper?->city ?: '-' }}, India</div>
                                                        <div style="font-weight:600;">{{ $senderName }}</div>
                                                        @if($shipper?->phone_number)
                                                            <div class="text-muted" style=""><i class="bi bi-telephone me-1"></i>{{ $shipper->phone_number }}</div>
                                                        @endif
                                                        <div class="text-muted" style=""><i class="bi bi-geo-alt me-1"></i>{{ $shipper?->pincode ?: '-' }}</div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-center position-relative" style="width:90px;height:30px;">
                                                        <span class="shipment-route-line" aria-hidden="true"></span>
                                                        <span class="shipment-route-plane">
                                                            <i class="ti ti-plane" aria-hidden="true" style="font-size:22px;color:#0d6efd;"></i>
                                                        </span>
                                                    </div>
                                                    <div class="text-end" style="min-width:0;white-space:normal;overflow-wrap:anywhere;word-break:break-word;">
                                                        <div>{{ $consignee?->city ?: '-' }}, {{ $consignee?->state ?: '-' }}, {{ $consignee?->delivery_destination ?: '-' }}</div>
                                                        <div style="font-weight:600;">{{ $receiverName }}</div>
                                                        @if($consignee?->phone_number)
                                                            <div class="text-muted" style=""><i class="bi bi-telephone me-1"></i>{{ $consignee->phone_number }}</div>
                                                        @endif
                                                        <div class="text-muted" style=""><i class="bi bi-geo-alt me-1"></i>{{ $consignee?->zip_code ?: '-' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            --}}
                                            <!-- <td>{{ $invoice->invoice_date ? date('d-m-Y', strtotime($invoice->invoice_date)) : '-' }}</td> -->
                                            {{-- Amount column temporarily hidden
                                            <td class="amount-col">INR {{ number_format($shipmentAmount, 2) }}</td>
                                            --}}
                                            <td @class(['d-none' => $hideCurrencyColumn])>{{ $invoice->invoice_currency }}</td>
                                            <td @class(['d-none' => $hideIncotermsAndPayColumns])>{{ $invoice->incoterms }}</td>
                                            <!-- <td>{{ $invoice->reference_number ?: '-' }}</td> -->
                                            <td class="status-col">
                                                @php
                                                    $displayStatus = $invoice->status === 'cancelled' ? 'cancelled' : ($invoice->shipperInfo && $invoice->shipperInfo->status ? $invoice->shipperInfo->status : 'draft');
                                                    $statusBadge = [
                                                        'draft' => 'badge bg-warning text-dark',
                                                        'ready' => 'badge bg-info',
                                                        'packed' => 'badge bg-primary',
                                                        'manifested' => 'badge bg-secondary',
                                                        'ready_for_pickup' => 'badge bg-info',
                                                        'assigned_for_pickup' => 'badge bg-warning text-dark',
                                                        'received' => 'badge bg-info',
                                                        'confirm_pickup' => 'badge bg-warning text-dark',
                                                        'dispatched' => 'badge bg-dark',
                                                        'delivered' => 'badge bg-success',
                                                        'cancelled' => 'badge bg-danger',
                                                        'disputed' => 'badge bg-warning',
                                                        'on_hold' => 'badge bg-secondary',
                                                    ];
                                                    $statusLabel = [
                                                        'draft' => 'Draft',
                                                        'ready' => 'Ready for Packing',
                                                        'packed' => 'Packed',
                                                        'manifested' => 'Manifested',
                                                        'ready_for_pickup' => 'Ready for Pickup',
                                                        'assigned_for_pickup' => 'In-Transit to Hub',
                                                        'received' => 'Received',
                                                        'confirm_pickup' => 'In-Transit to Hub',
                                                        'dispatched' => 'Dispatched',
                                                        'delivered' => 'Delivered',
                                                        'cancelled' => 'Cancelled',
                                                        'disputed' => 'Disputed',
                                                        'on_hold' => 'On Hold',
                                                    ];
                                                @endphp
                                                <span class="shipment-status-badge {{ $statusBadge[$displayStatus] ?? 'badge bg-warning text-dark' }}">{{ $statusLabel[$displayStatus] ?? ucfirst($displayStatus) }}</span>
                                            </td>
                                            {{-- Print Label column merged into Action column
                                            <td @class(['text-center', 'd-none' => $hidePrintLabelColumn])>
                                                @if($invoice->shipperInfo && $invoice->shipperInfo->awb_number)
                                                    <button class="btn btn-sm btn-outline-primary print-label-btn"
                                                            data-invoice-id="{{ $invoice->id }}"
                                                            style="padding:4px 12px;font-size:13px;border-radius:4px;">
                                                        <i class="ti ti-printer me-1"></i>Print
                                                    </button>
                                                @else
                                                    <span class="text-muted" style="font-size:12px;">N/A</span>
                                                @endif
                                            </td>
                                            --}}
                                            <!-- <td class="text-center">
                                                @if(isset($shipmentDetails[$invoice->id]) && $shipmentDetails[$invoice->id]['has_label'])
                                                    <a href="#" class="label-link"
                                                       onclick="viewLabel({{ $invoice->id }}); return false;">
                                                        <i class="ti ti-file-text me-1"></i>View Label
                                                    </a>
                                                @else
                                                    <span class="text-muted" style="font-size:12px;">N/A</span>
                                                @endif
                                            </td> -->
                                            {{-- Pay Now column merged into Action column
                                            <td class="pay-now-col" @class(['text-center', 'd-none' => $hideIncotermsAndPayColumns])>
                                                @if($invoice->status === 'cancelled')
                                                    <span class="text-muted" style="font-size:12px;">N/A</span>
                                                @elseif($invoice->shipperInfo && $invoice->shipperInfo->status && $invoice->shipperInfo->status !== 'draft')
                                                    <span class="text-muted" style="font-size:12px;">Paid</span>
                                                @else
                                                    <button class="btn btn-sm btn-success pay-now-btn"
                                                            data-invoice-id="{{ $invoice->id }}"
                                                            data-shipper-id="{{ $invoice->shipperInfo ? $invoice->shipperInfo->id : '' }}"
                                                            data-amount="{{ number_format($shipmentAmount, 2, '.', '') }}"
                                                            style="padding:4px 12px;font-size:13px;border-radius:4px;">
                                                        <i class="ti ti-credit-card me-1"></i>Pay Now
                                                    </button>
                                                @endif
                                            </td>
                                            --}}
                                            {{-- Manifest column — value sourced from the manifests table --}}
                                            <td @class(['text-center', 'manifest-col', 'd-none' => $hideManifestColumn || $hideManifestCell])>
                                                @php
                                                    $manifestRow = $invoice->shipperInfo ? $invoice->shipperInfo->manifest : null;
                                                @endphp
                                                @if($manifestRow && $manifestRow->manifest_number && empty($hideCodFocPrintManifest))
                                                    <div class="d-inline-flex flex-column align-items-center gap-1">
                                                        <a href="{{ route('customer.manifest-detail', ['manifestNumber' => $manifestRow->manifest_number]) }}"
                                                           target="_blank"
                                                           class="badge bg-success text-decoration-none manifest-link"
                                                           title="Open manifest details in new tab"
                                                           style="white-space:nowrap;">
                                                            {{ $manifestRow->manifest_number }}
                                                            <i class="ti ti-external-link ms-1" style="font-size:11px;"></i>
                                                        </a>
                                                        @if(in_array($rowStatus, ['ready_for_pickup', 'assigned_for_pickup'], true))
                                                            <div class="d-inline-flex align-items-center gap-1">
                                                                <a href="{{ route('customer.manifest-label', ['manifestNumber' => $manifestRow->manifest_number]) }}"
                                                                   target="_blank"
                                                                   class="btn btn-sm btn-outline-success btn-icon"
                                                                   title="Print Manifest Label">
                                                                    <i class="ti ti-printer"></i>
                                                                </a>
                                                                <a href="{{ route('customer.manifest-document', ['manifestNumber' => $manifestRow->manifest_number]) }}"
                                                                   target="_blank"
                                                                   class="btn btn-sm btn-outline-info btn-icon"
                                                                   title="Download Manifest Document">
                                                                    <i class="ti ti-file-text"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted" style="font-size:12px;">-</span>
                                                @endif
                                            </td>
                                            <td class="action-col" @class(['text-center', 'd-none' => !$showActionColumn])>
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    @if($rowStatus === 'draft')
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-success pay-now-btn d-inline-flex align-items-center justify-content-center"
                                                                data-invoice-id="{{ $invoice->id }}"
                                                                data-shipper-id="{{ $invoice->shipperInfo ? $invoice->shipperInfo->id : '' }}"
                                                                data-amount="{{ number_format($shipmentAmount, 2, '.', '') }}"
                                                                title="Pay Now"
                                                                aria-label="Pay Now"
                                                                style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                            <i class="ti ti-credit-card" aria-hidden="true"></i>
                                                        </button>
                                                    @elseif($invoice->shipperInfo && $invoice->shipperInfo->awb_number)
                                                        @if(!empty($hideCodFocPrintManifest))
                                                            <span class="text-muted" style="font-size:12px;">-</span>
                                                        @else
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-primary print-label-btn d-inline-flex align-items-center justify-content-center"
                                                                    data-invoice-id="{{ $invoice->id }}"
                                                                    title="Print Label or Invoice"
                                                                    aria-label="Print Label or Invoice"
                                                                    style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                                <i class="ti ti-printer" aria-hidden="true"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                    @if($rowStatus === 'packed')
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-success manifest-single-btn d-inline-flex align-items-center justify-content-center"
                                                                data-shipper-id="{{ $invoice->shipperInfo ? $invoice->shipperInfo->id : '' }}"
                                                                data-invoice-id="{{ $invoice->id }}"
                                                                title="Manifest Shipment (Retry)"
                                                                aria-label="Manifest Shipment"
                                                                style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                            <i class="ti ti-package-export" aria-hidden="true"></i>
                                                        </button>
                                                    @endif
                                                    @if($rowStatus === 'draft')
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger cancel-btn d-inline-flex align-items-center justify-content-center"
                                                                data-id="{{ $invoice->id }}"
                                                                data-invoice="{{ $invoice->invoice_number }}"
                                                                data-amount="{{ number_format($shipmentAmount, 2, '.', '') }}"
                                                                data-paid="{{ $invoice->shipperInfo && in_array($invoice->shipperInfo->status, ['draft', 'ready', 'packed', 'manifested'], true) ? '1' : '0' }}"
                                                                title="Cancel Shipment"
                                                                aria-label="Cancel Shipment"
                                                                style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                            <i class="ti ti-ban" aria-hidden="true"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center"
                                                                title="Edit"
                                                                aria-label="Edit"
                                                                style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                            <i class="ti ti-edit" aria-hidden="true"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            {{-- Standalone Cancel column merged into Action column
                                            <td @class(['text-center', 'd-none' => $showActionColumn || $hideCancelColumn || in_array($rowStatus, ['packed', 'manifested', 'assigned_for_pickup', 'received', 'confirm_pickup', 'dispatched', 'cancelled', 'delivered', 'disputed', 'on_hold'], true)])>
                                                @if($invoice->status === 'cancelled')
                                                    <button class="btn btn-cancel" disabled>
                                                        <i class="ti ti-x"></i> Cancelled
                                                    </button>
                                                @else
                                                    <button class="btn btn-cancel cancel-btn"
                                                            data-id="{{ $invoice->id }}"
                                                            data-invoice="{{ $invoice->invoice_number }}"
                                                            data-amount="{{ number_format($shipmentAmount, 2, '.', '') }}"
                                                            data-paid="{{ $invoice->shipperInfo && in_array($invoice->shipperInfo->status, ['draft', 'ready', 'packed', 'manifested'], true) ? '1' : '0' }}">
                                                        <i class="ti ti-ban"></i> Cancel
                                                    </button>
                                                @endif
                                            </td>
                                            --}}
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 mt-3">
                                <div class="pagination-summary">
                                    Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} shipments
                                </div>

                                @if ($invoices->hasPages())
                                    <nav aria-label="Shipment pages">
                                        <ul class="pagination shipment-pagination">
                                            <li class="page-item {{ $invoices->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link"
                                                   href="{{ $invoices->onFirstPage() ? '#' : $invoices->previousPageUrl() }}"
                                                   aria-label="Previous page">
                                                    Previous
                                                </a>
                                            </li>

                                            @php
                                                $firstPage = max(1, $invoices->currentPage() - 1);
                                                $lastPage = min($invoices->lastPage(), $invoices->currentPage() + 1);
                                            @endphp

                                            @for ($page = $firstPage; $page <= $lastPage; $page++)
                                                <li class="page-item {{ $page === $invoices->currentPage() ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ $invoices->url($page) }}">{{ $page }}</a>
                                                </li>
                                            @endfor

                                            <li class="page-item {{ $invoices->hasMorePages() ? '' : 'disabled' }}">
                                                <a class="page-link"
                                                   href="{{ $invoices->hasMorePages() ? $invoices->nextPageUrl() : '#' }}"
                                                   aria-label="Next page">
                                                    Next
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <!-- End Shipments Table Card -->

            </div>
            <!-- End Content -->

        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Main Wrapper -->

    <!-- Shipment Detail Modal -->
    <div class="modal fade" id="shipmentDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="ti ti-package me-2"></i>Shipment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="shipmentDetailBody">
                    <!-- Tracking Number -->
                    <div class="tracking-number-box" id="detailTrackingBox">
                        <div class="tracking-label">UPS Tracking Number</div>
                        <div class="tracking-value" id="detailTrackingNumber">-</div>
                    </div>

                    <!-- Ship From → Ship To Route -->
                    <div class="route-box" id="detailRouteBox">
                        <div class="route-point">
                            <div class="route-label">SHIP FROM</div>
                            <div class="route-city" id="detailShipFrom">-</div>
                        </div>
                        <div class="route-arrow">
                            <i class="ti ti-arrow-right"></i>
                        </div>
                        <div class="route-point">
                            <div class="route-label">SHIP TO</div>
                            <div class="route-city" id="detailShipTo">-</div>
                        </div>
                    </div>

                    <!-- HAWB Number -->
                    <div class="detail-section">
                        <h6><i class="ti ti-clipboard me-1"></i> AWB & Invoice Info</h6>
                        <div class="detail-row">
                            <span class="label">HAWB Number</span>
                            <span class="value" id="detailAwbNumber">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Invoice Number</span>
                            <span class="value" id="detailInvoiceNumber">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Invoice Date</span>
                            <span class="value" id="detailInvoiceDate">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Invoice Amount</span>
                            <span class="value" id="detailInvoiceAmount">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Currency</span>
                            <span class="value" id="detailInvoiceCurrency">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Incoterms</span>
                            <span class="value" id="detailIncoterms">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Reference No.</span>
                            <span class="value" id="detailReferenceNumber">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Manifest No.</span>
                            <span class="value" id="detailManifestNumber">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Status</span>
                            <span class="value" id="detailStatus">-</span>
                        </div>
                    </div>

                    <!-- Shipper Info -->
                    <div class="detail-section">
                        <h6><i class="ti ti-user me-1"></i> Shipper Info</h6>
                        <div class="detail-row">
                            <span class="label">Company</span>
                            <span class="value" id="detailShipperCompany">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Contact Person</span>
                            <span class="value" id="detailShipperContact">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Phone</span>
                            <span class="value" id="detailShipperPhone">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Email</span>
                            <span class="value" id="detailShipperEmail">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Address</span>
                            <span class="value" id="detailShipperAddress">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">City / State / Pincode</span>
                            <span class="value" id="detailShipperCityStatePin">-</span>
                        </div>
                    </div>

                    <!-- Consignee Info -->
                    <div class="detail-section">
                        <h6><i class="ti ti-user-check me-1"></i> Consignee Info</h6>
                        <div class="detail-row">
                            <span class="label">Name</span>
                            <span class="value" id="detailConsigneeName">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Contact Person</span>
                            <span class="value" id="detailConsigneeContact">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Phone</span>
                            <span class="value" id="detailConsigneePhone">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Email</span>
                            <span class="value" id="detailConsigneeEmail">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Address</span>
                            <span class="value" id="detailConsigneeAddress">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">City / State / Zip</span>
                            <span class="value" id="detailConsigneeCityStateZip">-</span>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="detail-section" id="detailItemsSection">
                        <h6><i class="ti ti-file-text me-1"></i> Invoice Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Box</th>
                                        <th>Description</th>
                                        <th>HS Code</th>
                                        <th>HTS Code</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>IGST(%)</th>
                                        <th>IGST</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="detailItemsTable"></tbody>
                            </table>
                        </div>
                        <div class="text-end mt-2">
                            <strong>Total: <span id="detailItemsTotal">0.00</span></strong>
                        </div>
                    </div>

                    <!-- Shipment Details -->
                    <div class="detail-section">
                        <h6><i class="ti ti-truck me-1"></i> Shipment Details</h6>
                        <div class="detail-row">
                            <span class="label">Destination</span>
                            <span class="value" id="detailDestination">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Origin Type</span>
                            <span class="value" id="detailOriginType">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Shipping Method</span>
                            <span class="value" id="detailShippingMethod">-</span>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="detail-section" id="detailPriceBreakdownSection" style="display:none;">
                        <h6><i class="ti ti-receipt-2 me-1"></i> Price Breakdown</h6>
                        <div class="detail-row">
                            <span class="label">Base Price</span>
                            <span class="value" id="detailBasePrice">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Fuel Price</span>
                            <span class="value" id="detailFuelPrice">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Surcharge</span>
                            <span class="value" id="detailSurchargePrice">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">GST</span>
                            <span class="value" id="detailGstPrice">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Total</span>
                            <span class="value fw-semibold" id="detailTotalPrice">-</span>
                        </div>
                    </div>

                    <!-- Package Dimensions -->
                    <div class="detail-section" id="detailPackagesSection">
                        <h6><i class="ti ti-box me-1"></i> Package Dimensions</h6>
                        <div id="detailPackagesContainer"></div>
                    </div>

                    <!-- UPS Charges -->
                    <div class="detail-section" id="detailChargesSection">
                        <h6><i class="ti ti-currency-dollar me-1"></i> UPS Shipping Charges</h6>
                        <div class="detail-row">
                            <span class="label">Transportation Charges</span>
                            <span class="value" id="detailTransportCharges">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Service Options Charges</span>
                            <span class="value" id="detailServiceOptionsCharges">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Total Charges</span>
                            <span class="value" id="detailTotalCharges">-</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Billing Weight</span>
                            <span class="value" id="detailBillingWeight">-</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Confirm Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to cancel shipment <strong id="cancelInvoiceRef"></strong>?</p>
                    <p id="cancelRefundInfo" class="mt-2 mb-0" style="font-size:13px;color:#28a745;display:none;">
                        <i class="ti ti-refund me-1"></i> If <strong id="cancelRefundAmount"></strong> was deducted, it will be refunded to your wallet.
                    </p>
                    <p class="text-muted mt-2 mb-0" style="font-size:13px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel Shipment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Close Manifest Confirm Modal -->
    <div class="modal fade" id="closeManifestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="ti ti-lock me-2"></i>Close Manifest</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to close manifest <strong id="closeManifestNumberRef"></strong>?</p>
                    <p class="text-muted mt-2 mb-0" style="font-size:13px;">Once closed, shipments in this manifest can no longer be removed or modified.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It Open</button>
                    <button type="button" class="btn btn-dark" id="confirmCloseManifestBtn">
                        <i class="ti ti-lock me-1"></i>Yes, Close Manifest
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign for Pickup Confirm Modal -->
    <div class="modal fade" id="assignPickupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="ti ti-truck me-2"></i>Assign for Pickup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to assign manifest <strong id="assignPickupNumberRef"></strong> for pickup?</p>
                    <p class="text-muted mt-2 mb-0" style="font-size:13px;">All shipments in this manifest will be scheduled for pickup by the courier team.</p>
                    <div class="mt-3">
                        <div class="md-pickup-date-label">Select Pickup Date</div>
                        <div class="md-pickup-date-options" id="assignPickupDates">
                            @foreach($pickupDateOptions ?? [] as $idx => $option)
                                <div class="md-pickup-date-option{{ $idx === 0 ? ' selected' : '' }}"
                                     data-value="{{ $option['value'] }}">
                                    <span class="dp-label">{{ $option['label'] }}</span>
                                    <span class="dp-date">{{ $option['display'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" id="assignPickupDate" value="{{ ($pickupDateOptions[0]['value'] ?? '') }}">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It Closed</button>
                    <button type="button" class="btn btn-primary" id="confirmAssignPickupBtn">
                        <i class="ti ti-truck me-1"></i>Yes, Assign for Pickup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pay Now Modal -->
    <div class="modal fade" id="payNowModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="ti ti-credit-card me-2"></i>Pay Now</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Shipment AWB / Invoice</label>
                        <input type="text" class="form-control" id="payShipmentRef" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount to Pay (INR )</label>
                        <input type="number" disabled class="form-control" id="payAmount" min="0.01" step="0.01" placeholder="Enter amount">
                    </div>
                    <div class="alert alert-info d-flex align-items-center py-2 px-3 mb-0" style="border-radius:8px;">
                        <i class="ti ti-wallet fs-18 me-2"></i>
                        <div>
                            <span class="fw-semibold">Wallet Balance:</span>
                            <span class="fw-bold text-primary" id="payWalletBalance">INR 0.00</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmPayNowBtn">
                        <i class="ti ti-credit-card me-1"></i>Confirm Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Error Fallback Modal (UNITED ECO POST → UNITED CLASSIC) -->
    <div class="modal fade" id="addressErrorFallbackModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="ti ti-alert-triangle me-2"></i>Incorrect Address
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="alert alert-warning mb-3" role="alert">
                        <i class="ti ti-alert-circle me-1"></i>
                        The address provided appears to be <strong>incorrect or incomplete</strong> for <strong>UNITED ECO POST</strong>.
                        You can choose to ship this shipment via <strong>UNITED CLASSIC (Ship Global)</strong> instead.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Shipping Option</label>
                        <select class="form-select" id="addressErrorFallbackSelect">
                            <option value="">-- Select an option --</option>
                            <option value="ship_global">Ship via UNITED CLASSIC</option>
                            <option value="cancel">Cancel & correct the address</option>
                        </select>
                    </div>

                    <div id="fallbackRateInfo" style="display:none;">
                        <div class="card bg-light border-0 p-3 mb-0">
                            <h6 class="fw-bold mb-2 text-primary">
                                <i class="ti ti-truck-delivery me-1"></i>UNITED CLASSIC (Ship Global) Rate Details
                            </h6>
                            <div class="detail-row">
                                <span class="label">Total Weight:</span>
                                <span class="value" id="fbTotalWeight">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">UNITED CLASSIC Rate:</span>
                                <span class="value fw-bold text-success" id="fbClassicRate">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Amount Already Paid:</span>
                                <span class="value" id="fbPaidAmount">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Rate Difference:</span>
                                <span class="value" id="fbDifference">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Wallet Balance:</span>
                                <span class="value" id="fbWalletBalance">-</span>
                            </div>
                            <div class="detail-row" id="fbWalletActionRow" style="display:none;">
                                <span class="label">Wallet Impact:</span>
                                <span class="value fw-bold" id="fbWalletAction">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmShipGlobalFallbackBtn" disabled>
                        <i class="ti ti-check me-1"></i>Confirm & Manifest
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Label Modal -->
    <div class="modal fade" id="printLabelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="ti ti-printer me-2"></i>Print Label</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3" id="printLabelBody">
                    <!-- Company Logo + Barcode Row -->
                    <div class="row align-items-center mb-3">
                        <div class="col-4">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="United Courier" style="max-height:65px;">
                        </div>
                        <div class="col-8 text-end">
                            <svg id="printLabelBarcode"></svg>
                        </div>
                    </div>
                    <hr>
                    <!-- Ship From / Ship To -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong style="font-size:13px;">SHIP FROM</strong>
                            <p class="mb-1" style="font-size:13px;" id="printShipperCompany">-</p>
                            <p class="mb-1" style="font-size:12px;" id="printShipperContact">-</p>
                            <p class="mb-1" style="font-size:12px;" id="printShipperAddress">-</p>
                            <p class="mb-0" style="font-size:12px;" id="printShipperCityStatePin">-</p>
                            <p class="mb-0" style="font-size:12px;">Phone: <span id="printShipperPhone">-</span></p>
                        </div>
                        <div class="col-6">
                            <strong style="font-size:13px;">SHIP TO</strong>
                            <p class="mb-1" style="font-size:13px;" id="printConsigneeName">-</p>
                            <p class="mb-1" style="font-size:12px;" id="printConsigneeContact">-</p>
                            <p class="mb-1" style="font-size:12px;" id="printConsigneeAddress">-</p>
                            <p class="mb-0" style="font-size:12px;" id="printConsigneeCityStateZip">-</p>
                            <p class="mb-0" style="font-size:12px;">Phone: <span id="printConsigneePhone">-</span></p>
                        </div>
                    </div>
                    <hr>
                    <!-- Shipment Info -->
                    <div id="printShipmentInfoSection" style="margin-bottom:10px;font-size:12px;">
                        <strong style="font-size:13px;">SHIPMENT INFO</strong>
                        <div style="display:flex;flex-wrap:wrap;margin-top:4px;">
                            <div style="width:50%;padding:2px 0;"><strong>Invoice No.:</strong> <span
                                    id="printInvoiceNo">-</span></div>
                            <div style="width:50%;padding:2px 0;"><strong>Invoice Date:</strong> <span
                                    id="printInvoiceDate">-</span></div>
                            <div style="width:50%;padding:2px 0;"><strong>Reference No.:</strong> <span
                                    id="printReferenceNo">-</span></div>
                            <div style="width:50%;padding:2px 0;"><strong>Method:</strong> <span
                                    id="printMethod">-</span></div>
                            <div style="width:50%;padding:2px 0;"><strong>Service Code:</strong> <span
                                    id="printServiceCode">-</span></div>
                        </div>
                    </div>
                    <hr>
                    <!-- Invoice Items -->
                    <div id="printItemsSection">
                        <strong style="font-size:13px;">INVOICE ITEMS</strong>
                        <div class="table-responsive mt-1">
                            <table class="table table-sm table-bordered mb-0" style="">
                                <thead class="table-light">
                                    <tr>
                                        <th>Box</th>
                                        <th>Description</th>
                                        <th>HS Code</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>IGST(%)</th>
                                        <th>IGST</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="printItemsTable"></tbody>
                            </table>
                        </div>
                        <div class="text-end mt-1" style="font-size:13px;">
                            <strong>Total: <span id="printItemsTotal">0.00</span></strong>
                        </div>
                    </div>
                    <hr>
                    <!-- Package Dimensions -->
                    <div id="printPackagesSection">
                        <strong style="font-size:13px;">PACKAGE DIMENSIONS</strong>
                        <div id="printPackagesContainer" class="mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-semibold" for="printLabelSize">Label Size:</label>
                        <select id="printLabelSize" class="form-select form-select-sm" style="width:auto;">
                            <option value="a4" selected>A4</option>
                            <option value="4x6">4x6</option>
                        </select>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="printLabel()">
                            <i class="ti ti-printer me-1"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Options Modal (Label / Invoice) -->
    <div class="modal fade" id="printOptionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="ti ti-printer me-2"></i>Print Options</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="text-muted mb-3" style="font-size:13px;">What would you like to print for this shipment?</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2" id="printLabelOptionBtn">
                            <i class="ti ti-barcode"></i> Print Label
                        </button>
                        <button type="button" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2" id="printInvoiceOptionBtn">
                            <i class="ti ti-file-invoice"></i> Print Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JsBarcode CDN -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <!-- Main theme JS initializes the sidebar dropdowns. -->
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script>
    // Shipment data embedded from server for detail modal
    const shipmentData = @json($shipmentDetails);

    function showShipmentDetail(invoiceId) {
        const data = shipmentData[invoiceId];
        if (!data) return;

        // Tracking Number
        const trackingBox = document.getElementById('detailTrackingBox');
        const trackingNum = document.getElementById('detailTrackingNumber');
        if (data.tracking_number) {
            trackingBox.style.display = 'block';
            trackingNum.textContent = data.tracking_number;
        } else {
            trackingBox.style.display = 'none';
        }

        // Ship From → Ship To
        document.getElementById('detailShipFrom').textContent = data.ship_from || '-';
        document.getElementById('detailShipTo').textContent = data.ship_to || '-';

        // AWB & Invoice Info
        document.getElementById('detailAwbNumber').textContent = data.awb_number || '-';
        document.getElementById('detailInvoiceNumber').textContent = data.invoice_number || '-';
        document.getElementById('detailInvoiceDate').textContent = data.invoice_date || '-';
        document.getElementById('detailInvoiceAmount').textContent = data.invoice_amount || '-';
        document.getElementById('detailInvoiceCurrency').textContent = data.invoice_currency || '-';
        document.getElementById('detailIncoterms').textContent = data.incoterms || '-';
        document.getElementById('detailReferenceNumber').textContent = data.reference_number || '-';
        // COD / FOC / prepare rows never show a manifest number - just "-".
        // Types: 1=general, 2=cod, 3=foc, 4=prepare.
        const detailRow = document.querySelector('#invoice-row-' + invoiceId);
        const detailShipmentType = detailRow ? String(detailRow.dataset.shipmentType || '') : '';
        const hideDetailManifest = (detailShipmentType === '2' || detailShipmentType === '3' || detailShipmentType === '4');
        document.getElementById('detailManifestNumber').textContent = hideDetailManifest ? '-' : (data.manifest_number || '-');
        document.getElementById('detailStatus').textContent = data.status || '-';

        // Shipper Info
        if (data.shipper) {
            document.getElementById('detailShipperCompany').textContent = data.shipper.company || '-';
            document.getElementById('detailShipperContact').textContent = data.shipper.contact || '-';
            document.getElementById('detailShipperPhone').textContent = data.shipper.phone || '-';
            document.getElementById('detailShipperEmail').textContent = data.shipper.email || '-';
            document.getElementById('detailShipperAddress').textContent = data.shipper.address || '-';
            document.getElementById('detailShipperCityStatePin').textContent = data.shipper.city_state_pin || '-';
        }

        // Consignee Info
        if (data.consignee) {
            document.getElementById('detailConsigneeName').textContent = data.consignee.name || '-';
            document.getElementById('detailConsigneeContact').textContent = data.consignee.contact || '-';
            document.getElementById('detailConsigneePhone').textContent = data.consignee.phone || '-';
            document.getElementById('detailConsigneeEmail').textContent = data.consignee.email || '-';
            document.getElementById('detailConsigneeAddress').textContent = data.consignee.address || '-';
            document.getElementById('detailConsigneeCityStateZip').textContent = data.consignee.city_state_zip || '-';
        }

        // Shipment Details
        document.getElementById('detailDestination').textContent = data.destination || '-';
        document.getElementById('detailOriginType').textContent = data.origin_type || '-';
        document.getElementById('detailShippingMethod').textContent = data.shipping_method || '-';

        // Package Dimensions
        const packagesContainer = document.getElementById('detailPackagesContainer');
        const packagesSection = document.getElementById('detailPackagesSection');
        packagesContainer.innerHTML = '';
        if (data.packages && data.packages.length > 0) {
            packagesSection.style.display = 'block';
            data.packages.forEach(function(pkg) {
                const card = document.createElement('div');
                card.className = 'package-card';
                card.style.cssText = 'border:1px solid #dee2e6;border-radius:8px;padding:12px;margin-bottom:8px;background:#fff;';
                const header = document.createElement('div');
                header.style.cssText = 'background:#f0f0f3;border-radius:6px 6px 0 0;padding:6px 10px;margin:-12px -12px 8px -12px;font-weight:600;color:#495057;';
                header.innerHTML = '<span style="font-size:14px;"><i class="ti ti-box me-1"></i> Box #' + pkg.index + '</span>';
                card.appendChild(header);
                const row1 = document.createElement('div');
                row1.className = 'row';
                row1.innerHTML = '<div class="col-md-3"><strong>Weight:</strong> ' + (pkg.weight || '-') + ' Kg</div>' +
                    '<div class="col-md-3"><strong>Length:</strong> ' + (pkg.length || '-') + ' cm</div>' +
                    '<div class="col-md-3"><strong>Width:</strong> ' + (pkg.width || '-') + ' cm</div>' +
                    '<div class="col-md-3"><strong>Height:</strong> ' + (pkg.height || '-') + ' cm</div>';
                card.appendChild(row1);
                const row2 = document.createElement('div');
                row2.className = 'row mt-1';
                row2.innerHTML = '<div class="col-md-3"><strong>Volumetric Wt:</strong> ' + (pkg.volumetric || '-') + ' Kg</div>' +
                    '<div class="col-md-3"><strong>Chg. Wt:</strong> ' + (pkg.chargeable || '-') + ' Kg</div>';
                card.appendChild(row2);
                packagesContainer.appendChild(card);
            });
        } else {
            packagesSection.style.display = 'none';
        }

        // Invoice Items
        const itemsTable = document.getElementById('detailItemsTable');
        const itemsSection = document.getElementById('detailItemsSection');
        itemsTable.innerHTML = '';
        if (data.items && data.items.length > 0) {
            itemsSection.style.display = 'block';
            data.items.forEach(function(item) {
                const row = document.createElement('tr');
                row.innerHTML = '<td>' + (item.box_no || '-') + '</td>' +
                    '<td>' + (item.description || '-') + '</td>' +
                    '<td>' + (item.hs_code || '-') + '</td>' +
                    '<td>' + (item.hts_code || '-') + '</td>' +
                    '<td>' + (item.unit_type || '-') + '</td>' +
                    '<td>' + (item.qty || '-') + '</td>' +
                    '<td>' + (item.unit_rate || '-') + '</td>' +
                    '<td>' + (item.igst_percentage || '-') + '</td>' +
                    '<td>' + (item.igst_amount || '-') + '</td>' +
                    '<td>' + item.amount + '</td>';
                itemsTable.appendChild(row);
            });
            document.getElementById('detailItemsTotal').textContent = data.items_total;
        } else {
            itemsSection.style.display = 'none';
        }

        // UPS Charges
        const chargesSection = document.getElementById('detailChargesSection');
        if (data.charges) {
            chargesSection.style.display = 'block';
            document.getElementById('detailTransportCharges').textContent = data.charges.transport;
            document.getElementById('detailServiceOptionsCharges').textContent = data.charges.service_options;
            document.getElementById('detailTotalCharges').textContent = data.charges.total;
            document.getElementById('detailBillingWeight').textContent = data.charges.billing_weight;
        } else {
            chargesSection.style.display = 'none';
        }

        // Price Breakdown
        const priceBreakdownSection = document.getElementById('detailPriceBreakdownSection');
        if (data.price_breakdown) {
            priceBreakdownSection.style.display = 'block';
            const formatPrice = function(v) {
                return v !== null && v !== undefined ? 'INR ' + parseFloat(v).toFixed(2) : '-';
            };
            document.getElementById('detailBasePrice').textContent = formatPrice(data.price_breakdown.base);
            document.getElementById('detailFuelPrice').textContent = formatPrice(data.price_breakdown.fuel);
            document.getElementById('detailSurchargePrice').textContent = formatPrice(data.price_breakdown.surcharge);
            document.getElementById('detailGstPrice').textContent = formatPrice(data.price_breakdown.gst);
            document.getElementById('detailTotalPrice').textContent = formatPrice(data.price_breakdown.total);
        } else {
            priceBreakdownSection.style.display = 'none';
        }

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('shipmentDetailModal'));
        modal.show();
    }

    function viewLabel(invoiceId) {
        // The base64 label image is intentionally not embedded in the page for performance.
        // Fetch it on-demand from the server when the user actually requests the label.
        fetch('{{ url("/customer/shipment-label") }}/' + invoiceId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function (res) { return res.json(); })
        .then(function (response) {
            if (!response.success || !response.graphic_image) {
                showAlert(response.message || 'Label not available for this shipment.', 'warning');
                return;
            }
            openLabelFromBase64(response.graphic_image, response.label_format || 'PDF', response.awb_number || invoiceId);
        })
        .catch(function () {
            showAlert('Failed to load the label. Please try again.', 'error');
        });
    }

    function openLabelFromBase64(base64Data, format, awbNumber) {
        // Decode base64 GraphicImage and open as PDF
        format = (format || 'PDF').toUpperCase();

        // Convert base64 to binary
        const byteCharacters = atob(base64Data);
        const byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        const byteArray = new Uint8Array(byteNumbers);

        // Determine MIME type based on label format
        let mimeType = 'application/pdf';
        let extension = 'pdf';
        if (format === 'GIF') {
            mimeType = 'image/gif';
            extension = 'gif';
        } else if (format === 'PNG') {
            mimeType = 'image/png';
            extension = 'png';
        } else if (format === 'JPEG' || format === 'JPG') {
            mimeType = 'image/jpeg';
            extension = 'jpg';
        } else if (format === 'SPL') {
            mimeType = 'application/pdf';
            extension = 'pdf';
        }

        // Create blob and open in new tab
        const blob = new Blob([byteArray], { type: mimeType });
        const url = URL.createObjectURL(blob);

        // For PDF, open in new window; for images, open in new tab
        const newWindow = window.open(url, '_blank');
        if (!newWindow) {
            // If popup blocked, try downloading
            const a = document.createElement('a');
            a.href = url;
            a.download = 'label_' + (awbNumber || 'shipment') + '.' + extension;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Clean up the object URL after a delay
        setTimeout(function() { URL.revokeObjectURL(url); }, 60000);
    }
    </script>

    <script>
        $(document).ready(function () {


            // Keep status counters mutable so AJAX status changes are reflected immediately.
            const liveStatusCounts = @json($statusCounts);

            // The Manifest column is removed entirely on the dedicated Draft/Ready/Packed tabs.
            // On the All Orders view it may start hidden (when no manifested rows exist on the
            // current page) but must be revealed once a shipment is manifested in-place.
            const manifestColumnLocked = @json($manifestColumnLocked);

            // Reveal the Manifest column + a single row's manifest cell after a successful manifest.
            // No-op on the Draft/Ready/Packed tabs where the column is permanently removed.
            function revealManifestCell($row, manifestNumber) {
                if (manifestColumnLocked) return;
                $('#shipmentsTable th.manifest-col').removeClass('d-none');
                $row.find('.manifest-col').removeClass('d-none').html('<span class="badge bg-success">' + manifestNumber + '</span>');
            }

            function refreshStatusCounters() {
                $('.status-filter-btn').each(function () {
                    const filter = $(this).data('filter');
                    $(this).find('.badge').text(liveStatusCounts[filter] || 0);
                });

                const activeFilter = $('.status-filter-btn.btn-primary').data('filter') || 'all';
                const visibleCount = liveStatusCounts[activeFilter] || 0;
                $('#shipmentCountInfo').text('Showing ' + visibleCount + ' of ' + liveStatusCounts.all + ' shipments');
            }

            // Manifest failed → server reverts a Ready shipment back to Draft.
            // Reflect that in the row so the UI stays consistent with the database.
            const revertRowToDraft = function ($row) {
                if (!$row || !$row.length) return;
                const invoiceId = $row.data('invoice-id');

                $row.attr('data-status', 'draft').data('status', 'draft');

                $row.find('.shipment-status-badge')
                    .removeClass()
                    .addClass('shipment-status-badge badge bg-warning text-dark')
                    .text('Draft');

                if (invoiceId && shipmentData[invoiceId]) {
                    shipmentData[invoiceId].status = 'draft';
                }

                liveStatusCounts.ready = Math.max(0, liveStatusCounts.ready - 1);
                liveStatusCounts.draft += 1;
                refreshStatusCounters();
            };

            function getSelectedRowsByStatus(status) {
                return $('tr[data-status="' + status + '"] .shipment-checkbox:checked').closest('tr');
            }

            function getRowAmount(row) {
                const $row = $(row);
                const payAmount = $row.find('.pay-now-btn').data('amount');
                const cancelAmount = $row.find('.cancel-btn').data('amount');
                const fallbackText = $row.find('.amount-col').text();
                const rowAmount = $row.data('amount');
                const raw = payAmount || cancelAmount || fallbackText || rowAmount;
                const numeric = parseFloat(String(raw).replace(/[^0-9.\-]/g, ''));
                return isNaN(numeric) ? 0 : numeric;
            }

            function sumSelectedAmounts(rows) {
                let total = 0;
                rows.each(function () {
                    total += getRowAmount(this);
                });
                return total;
            }

            function updateBulkSelectionTotals() {
                const draftRows = getSelectedRowsByStatus('draft');
                const draftTotal = sumSelectedAmounts(draftRows);
                const draftDisabled = draftRows.length === 0;
                $('#bulkDraftPayBtn').prop('disabled', draftDisabled);
                $('#bulkDraftCancelBtn').prop('disabled', draftDisabled);
                $('#draftBulkTotal').text(draftDisabled ? '' : 'Selected total: INR ' + number_format(draftTotal, 2));

                const readyRows = getSelectedRowsByStatus('ready');
                const readyTotal = sumSelectedAmounts(readyRows);
                const readyDisabled = readyRows.length === 0;
                $('#bulkReadyPrintBtn').prop('disabled', readyDisabled);
                $('#bulkReadyCancelBtn').prop('disabled', readyDisabled);
                $('#readyBulkTotal').text(readyDisabled ? '' : 'Selected total: INR ' + number_format(readyTotal, 2));

                const packedRows = getSelectedRowsByStatus('packed');
                const packedTotal = sumSelectedAmounts(packedRows);
                const packedDisabled = packedRows.length === 0;
                $('#bulkPackedPrintBtn').prop('disabled', packedDisabled);
                $('#bulkPackedCancelBtn').prop('disabled', packedDisabled);
                $('#packedBulkTotal').text(packedDisabled ? '' : 'Selected total: INR ' + number_format(packedTotal, 2));
            }

            function showBulkDraftActions(show) {
                $('#draftBulkActions').toggleClass('is-visible', show);
            }

            function showBulkReadyActions(show) {
                $('#readyBulkActions').toggleClass('is-visible', show);
            }

            function showBulkPackedActions(show) {
                $('#packedBulkActions').toggleClass('is-visible', show);
            }

            function configureBulkActionsForStatus(status) {
                showBulkDraftActions(status === 'draft');
                showBulkReadyActions(status === 'ready');
                showBulkPackedActions(status === 'packed');

                const selectable = ['draft', 'ready', 'packed'].includes(status);
                $('#selectAllCheckbox, .bulk-manifest-checkbox').toggle(selectable);
                $('#bulkManifestBtn').toggle(status === 'packed');
            }

            configureBulkActionsForStatus(@json(request('status', 'all')));
            updateBulkSelectionTotals();

            // Print button click handler (delegated) -> opens the Print Options popup
            let pendingPrintInvoiceId = null;
            let pendingPrintRow = null;

            $('#shipmentsTable').on('click', '.print-label-btn', function () {
                const invoiceId = $(this).data('invoice-id');
                const data = shipmentData[invoiceId];
                if (!data) return;

                pendingPrintInvoiceId = invoiceId;
                pendingPrintRow = $(this).closest('tr');
                $('#printOptionsModal').modal('show');
            });

            // Print Options modal: "Print Label" choice
            $('#printLabelOptionBtn').on('click', function () {
                $('#printOptionsModal').modal('hide');
                const invoiceId = pendingPrintInvoiceId;
                const $row = pendingPrintRow;
                if (invoiceId) {
                    openPrintLabel(invoiceId, $row);
                }
            });

            // Print Options modal: "Print Invoice" choice
            $('#printInvoiceOptionBtn').on('click', function () {
                $('#printOptionsModal').modal('hide');
                const invoiceId = pendingPrintInvoiceId;
                if (invoiceId) {
                    openPrintInvoice(invoiceId);
                }
            });

            // Populate the Print Label modal for the given invoice and show it.
            // A Ready shipment becomes Packed only after its rendered label is stored.
            function openPrintLabel(invoiceId, $row) {
                const data = shipmentData[invoiceId];
                if (!data) return;

                // Remember which shipment is being printed so the global
                // printLabel() helper can build the 4x6 courier label.
                window.currentPrintInvoiceId = invoiceId;

                // Populate Barcode
                document.getElementById('printLabelBarcode').innerHTML = '';
                JsBarcode('#printLabelBarcode', data.awb_number || 'N/A', {
                    format: 'CODE128',
                    lineColor: '#000',
                    width: 2,
                    height: 100,
                    displayValue: true,
                    fontSize: 16
                });

                // Shipper Info
                if (data.shipper) {
                    $('#printShipperCompany').text(data.shipper.company || '-');
                    $('#printShipperContact').text(data.shipper.contact || '-');
                    $('#printShipperAddress').text(data.shipper.address || '-');
                    $('#printShipperCityStatePin').text(data.shipper.city_state_pin || '-');
                    $('#printShipperPhone').text(data.shipper.phone || '-');
                } else {
                    $('#printShipperCompany,#printShipperContact,#printShipperAddress,#printShipperCityStatePin').text('-');
                    $('#printShipperPhone').text('-');
                }

                // Consignee Info
                if (data.consignee) {
                    $('#printConsigneeName').text(data.consignee.name || '-');
                    $('#printConsigneeContact').text(data.consignee.contact || '-');
                    $('#printConsigneeAddress').text(data.consignee.address || '-');
                    $('#printConsigneeCityStateZip').text(data.consignee.city_state_zip || '-');
                    $('#printConsigneePhone').text(data.consignee.phone || '-');
                } else {
                    $('#printConsigneeName,#printConsigneeContact,#printConsigneeAddress,#printConsigneeCityStateZip').text('-');
                    $('#printConsigneePhone').text('-');
                }

                // Shipment Info
                $('#printInvoiceNo').text(data.invoice_number || '-');
                $('#printInvoiceDate').text(data.invoice_date || '-');
                $('#printReferenceNo').text(data.reference_number || '-');
                $('#printMethod').text(data.shipping_method || '-');
                $('#printServiceCode').text(data.service_code || '-');

                // Invoice Items
                const itemsTable = document.getElementById('printItemsTable');
                const itemsSection = document.getElementById('printItemsSection');
                itemsTable.innerHTML = '';
                if (data.items && data.items.length > 0) {
                    itemsSection.style.display = 'block';
                    data.items.forEach(function(item) {
                        const row = document.createElement('tr');
                        row.innerHTML = '<td>' + (item.box_no || '-') + '</td>' +
                            '<td>' + (item.description || '-') + '</td>' +
                            '<td>' + (item.hs_code || '-') + '</td>' +
                            '<td>' + (item.qty || '-') + '</td>' +
                            '<td>' + (item.unit_rate || '-') + '</td>' +
                            '<td>' + (item.igst_percentage || '-') + '</td>' +
                            '<td>' + (item.igst_amount || '-') + '</td>' +
                            '<td>' + item.amount + '</td>';
                        itemsTable.appendChild(row);
                    });
                    $('#printItemsTotal').text(data.items_total);
                } else {
                    itemsSection.style.display = 'none';
                }

                // Package Dimensions
                const packagesContainer = document.getElementById('printPackagesContainer');
                const packagesSection = document.getElementById('printPackagesSection');
                packagesContainer.innerHTML = '';
                if (data.packages && data.packages.length > 0) {
                    packagesSection.style.display = 'block';
                    data.packages.forEach(function(pkg) {
                        const card = document.createElement('div');
                        card.style.cssText = 'border:1px solid #dee2e6;border-radius:6px;padding:8px;margin-bottom:6px;font-size:12px;';
                        card.innerHTML = '<strong>Box #' + pkg.index + '</strong>: ' +
                            'Weight: ' + (pkg.weight || '-') + ' Kg | ' +
                            'L: ' + (pkg.length || '-') + ' × W: ' + (pkg.width || '-') + ' × H: ' + (pkg.height || '-') + ' cm | ' +
                            'Vol. Wt: ' + (pkg.volumetric || '-') + ' Kg | Chg. Wt: ' + (pkg.chargeable || '-') + ' Kg';
                        packagesContainer.appendChild(card);
                    });
                } else {
                    packagesSection.style.display = 'none';
                }

                if (!$row || !$row.length) {
                    $row = $('#shipmentsTable').find('.print-label-btn[data-invoice-id="' + invoiceId + '"]').closest('tr');
                }

                if (data.status === 'ready' && data.shipper_id) {
                    const customLabel = document.getElementById('printLabelBody').innerHTML;
                    $.ajax({
                        url: '{{ url("/customer/mark-packed") }}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            shipper_id: data.shipper_id,
                            custom_label: customLabel
                        },
                        success: function (response) {
                            if (!response.success) {
                                showAlert('danger', response.message || 'Failed to store the custom label.');
                                return;
                            }

                            $row.attr('data-status', 'packed').data('status', 'packed');

                            const $badge = $row.find('.shipment-status-badge');
                            $badge.removeClass().addClass('shipment-status-badge badge bg-primary').text('Packed');

                            shipmentData[invoiceId].status = 'packed';
                            liveStatusCounts.ready = Math.max(0, liveStatusCounts.ready - 1);
                            liveStatusCounts.packed += 1;
                            refreshStatusCounters();
                            printCourierLabel4x6(invoiceId);
                        },
                        error: function (xhr) {
                            const message = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : 'Failed to store the custom label.';
                            showAlert('danger', message);
                        }
                    });
                    return;
                }

                printCourierLabel4x6(invoiceId);
            }

            // Build and print a standalone A4 invoice from shipmentData.
            // This mirrors the A4 invoice layout rendered by
            // bulk-invoice-pdf.blade.php (the invoice the user sees in A4).
            function openPrintInvoice(invoiceId) {
                const data = shipmentData[invoiceId];
                if (!data) return;

                const shipper = data.shipper || {};
                const consignee = data.consignee || {};
                const items = Array.isArray(data.items) ? data.items : [];
                const packages = Array.isArray(data.packages) ? data.packages : [];
                const pb = data.price_breakdown || null;
                const currency = data.invoice_currency || '';

                // Safe numeric parse (handles comma-formatted strings)
                const num = function (v) {
                    const n = parseFloat(String(v == null ? '' : v).replace(/,/g, ''));
                    return isNaN(n) ? 0 : n;
                };

                // Format numbers with two decimals (number_format equivalent)
                const fmt = function (v) {
                    return Number(v).toFixed(2);
                };

                // Total chargeable weight from packages
                let totalChargeableWeight = 0;
                packages.forEach(function (pkg) {
                    totalChargeableWeight += num(pkg.chargeable);
                });

                // Goods value (Subtotal) from invoice items
                let subtotal = 0;
                items.forEach(function (item) {
                    subtotal += num(item.amount);
                });
                const invoiceAmount = num(data.invoice_amount);
                const goodsTotal = invoiceAmount > 0 ? invoiceAmount : subtotal;

                // Shipping-side totals (mirrors rateDetails in the A4 invoice)
                const shippingCost = pb && pb.base != null ? num(pb.base) : 0;
                const fuelCharge = pb && pb.fuel != null ? num(pb.fuel) : 0;
                const surchargeAmt = pb && pb.surcharge != null ? num(pb.surcharge) : 0;
                const gstAmt = pb && pb.gst != null ? num(pb.gst) : 0;
                const shippingTotal = pb && pb.total != null
                    ? num(pb.total)
                    : (shippingCost + fuelCharge + surchargeAmt + gstAmt);

                // Grand Total = shipping total + goods subtotal (matches A4 invoice)
                const grandTotal = shippingTotal + goodsTotal;

                const gstPct = (data.gst_percentage != null && data.gst_percentage !== '')
                    ? parseFloat(data.gst_percentage)
                    : 0;

                // Items table uses the same 5 columns as the A4 invoice
                // (#, Description, Qty, Unit Rate, Amount).
                const rowsHtml = items.length ? items.map(function (item, idx) {
                    return '<tr>' +
                        '<td>' + (item.box_no || (idx + 1)) + '</td>' +
                        '<td>' + (item.description || 'Goods') + '</td>' +
                        '<td class="text-center">' + (item.qty || '-') + '</td>' +
                        '<td class="text-right">' + fmt(item.unit_rate) + '</td>' +
                        '<td class="text-right">' + fmt(item.amount) + '</td>' +
                        '</tr>';
                }).join('') : '<tr><td colspan="5" class="text-center">No items</td></tr>';

                // Totals block mirrors the A4 invoice
                // (Subtotal, Shipping Cost, Fuel Charge, GST and Grand Total).
                let totalsHtml = '';
                totalsHtml += '<tr><td>Subtotal (' + currency + '):</td><td class="text-right">' + fmt(goodsTotal) + '</td></tr>';
                if (shippingCost > 0) {
                    totalsHtml += '<tr><td>Shipping Cost:</td><td class="text-right">' + fmt(shippingCost) + '</td></tr>';
                }
                if (fuelCharge > 0) {
                    totalsHtml += '<tr><td>Fuel Charge:</td><td class="text-right">' + fmt(fuelCharge) + '</td></tr>';
                }
                if (gstAmt > 0) {
                    totalsHtml += '<tr><td>GST (' + gstPct + '%):</td><td class="text-right">' + fmt(gstAmt) + '</td></tr>';
                }
                totalsHtml += '<tr class="grand-total"><td>Grand Total:</td><td class="text-right">' + fmt(grandTotal) + ' ' + currency + '</td></tr>';

                const weightHtml = packages.length ? fmt(totalChargeableWeight) + ' kg' : '-';

                const printWindow = window.open('', '_blank', 'width=900,height=750');
                if (!printWindow) {
                    showAlert('danger', 'Popup blocked. Allow popups to print the invoice.');
                    return;
                }

                printWindow.document.write('<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">');
                printWindow.document.write('<title>Invoice ' + (data.invoice_number || '') + '</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('* { box-sizing: border-box; }');
                printWindow.document.write('body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; font-size: 12px; }');
                printWindow.document.write('.invoice-wrapper { padding: 30px 40px; }');
                printWindow.document.write('.invoice-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #2d8eff; padding-bottom: 20px; margin-bottom: 25px; }');
                printWindow.document.write('.company-info h1 { font-size: 22px; color: #2d8eff; margin: 0 0 5px 0; }');
                printWindow.document.write('.company-info p { margin: 2px 0; color: #666; font-size: 11px; }');
                printWindow.document.write('.invoice-meta { text-align: right; }');
                printWindow.document.write('.invoice-meta h2 { font-size: 18px; margin: 0 0 8px 0; color: #333; text-transform: uppercase; letter-spacing: 1px; }');
                printWindow.document.write('.invoice-meta table { font-size: 11px; margin-left: auto; }');
                printWindow.document.write('.invoice-meta td { padding: 2px 8px; }');
                printWindow.document.write('.invoice-meta td:first-child { color: #888; font-weight: 600; }');
                printWindow.document.write('.parties { display: flex; justify-content: space-between; margin-bottom: 25px; gap: 20px; }');
                printWindow.document.write('.party-box { flex: 1; background: #f8f9fa; border-left: 3px solid #2d8eff; padding: 12px 15px; border-radius: 0 6px 6px 0; }');
                printWindow.document.write('.party-box h4 { font-size: 11px; text-transform: uppercase; color: #2d8eff; margin: 0 0 8px 0; letter-spacing: 0.5px; }');
                printWindow.document.write('.party-box p { margin: 2px 0; font-size: 11px; line-height: 1.5; }');
                printWindow.document.write('.party-box .name { font-weight: 700; font-size: 12px; color: #333; }');
                printWindow.document.write('table.items { width: 100%; border-collapse: collapse; margin-bottom: 25px; }');
                printWindow.document.write('table.items thead th { background: #2d8eff; color: #fff; padding: 10px 8px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }');
                printWindow.document.write('table.items tbody td { padding: 8px; border-bottom: 1px solid #e9ecef; font-size: 11px; }');
                printWindow.document.write('table.items tbody tr:nth-child(even) { background: #fafbfc; }');
                printWindow.document.write('table.items tfoot td { padding: 8px; font-weight: 600; border-top: 2px solid #2d8eff; }');
                printWindow.document.write('.totals { margin-left: auto; width: 300px; margin-bottom: 25px; }');
                printWindow.document.write('.totals table { width: 100%; border-collapse: collapse; }');
                printWindow.document.write('.totals td { padding: 6px 10px; font-size: 11px; }');
                printWindow.document.write('.totals td:first-child { color: #666; }');
                printWindow.document.write('.totals .grand-total td { background: #2d8eff; color: #fff; font-size: 13px; font-weight: 700; border-radius: 4px; }');
                printWindow.document.write('.awb-box { background: #fff3cd; border: 1px solid #ffe69c; border-radius: 6px; padding: 12px 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }');
                printWindow.document.write('.awb-box .label { font-size: 11px; color: #997404; text-transform: uppercase; font-weight: 600; }');
                printWindow.document.write('.awb-box .value { font-size: 16px; font-weight: 700; color: #997404; letter-spacing: 1px; }');
                printWindow.document.write('.footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e9ecef; text-align: center; color: #999; font-size: 10px; }');
                printWindow.document.write('.text-right { text-align: right; }');
                printWindow.document.write('.text-center { text-align: center; }');
                printWindow.document.write('@page { size: A4; margin: 0; }');
                printWindow.document.write('@media print { body { margin: 0; } }');
                printWindow.document.write('</style></head><body>');
                printWindow.document.write('<div class="invoice-wrapper">');

                // Header
                printWindow.document.write('<div class="invoice-header">');
                printWindow.document.write('<div class="company-info">');
                printWindow.document.write('<h1>United Courier</h1>');
                printWindow.document.write('<p>Providing seamless global logistics solutions since 1995</p>');
                printWindow.document.write('<p>support@unitedcourier.com</p>');
                printWindow.document.write('</div>');
                printWindow.document.write('<div class="invoice-meta">');
                printWindow.document.write('<h2>Invoice</h2>');
                printWindow.document.write('<table>');
                printWindow.document.write('<tr><td>Invoice No:</td><td><strong>' + (data.invoice_number || '-') + '</strong></td></tr>');
                printWindow.document.write('<tr><td>Date:</td><td>' + (data.invoice_date || '-') + '</td></tr>');
                printWindow.document.write('<tr><td>AWB Number:</td><td><strong>' + (data.awb_number || '-') + '</strong></td></tr>');
                if (data.reference_number) {
                    printWindow.document.write('<tr><td>Reference:</td><td>' + data.reference_number + '</td></tr>');
                }
                printWindow.document.write('</table>');
                printWindow.document.write('</div>');
                printWindow.document.write('</div>');

                // Parties (address lines rendered one per line like the A4 invoice)
                printWindow.document.write('<div class="parties">');
                printWindow.document.write('<div class="party-box">');
                printWindow.document.write('<h4>From (Shipper)</h4>');
                printWindow.document.write('<p class="name">' + (shipper.company || '-') + '</p>');
                printWindow.document.write('<p>' + (shipper.contact || '') + '</p>');
                if (shipper.address_line1) { printWindow.document.write('<p>' + shipper.address_line1 + '</p>'); }
                if (shipper.address_line2) { printWindow.document.write('<p>' + shipper.address_line2 + '</p>'); }
                if (shipper.address_line3) { printWindow.document.write('<p>' + shipper.address_line3 + '</p>'); }
                printWindow.document.write('<p>' + (shipper.city_state_pin || '') + '</p>');
                printWindow.document.write('<p>Phone: ' + (shipper.phone || '-') + '</p>');
                if (shipper.kyc_number) { printWindow.document.write('<p>GST: ' + shipper.kyc_number + '</p>'); }
                printWindow.document.write('</div>');
                printWindow.document.write('<div class="party-box">');
                printWindow.document.write('<h4>To (Consignee)</h4>');
                printWindow.document.write('<p class="name">' + (consignee.name || '-') + '</p>');
                printWindow.document.write('<p>' + (consignee.contact || '') + '</p>');
                if (consignee.address_line1) { printWindow.document.write('<p>' + consignee.address_line1 + '</p>'); }
                if (consignee.address_line2) { printWindow.document.write('<p>' + consignee.address_line2 + '</p>'); }
                if (consignee.address_line3) { printWindow.document.write('<p>' + consignee.address_line3 + '</p>'); }
                printWindow.document.write('<p>' + (consignee.city_state_zip || '') + '</p>');
                printWindow.document.write('<p>Phone: ' + (consignee.phone || '-') + '</p>');
                printWindow.document.write('</div>');
                printWindow.document.write('</div>');

                // AWB highlight
                printWindow.document.write('<div class="awb-box">');
                printWindow.document.write('<div><div class="label">Air Waybill Number</div><div class="value">' + (data.awb_number || '-') + '</div></div>');
                printWindow.document.write('<div class="text-right"><div class="label">Total Chargeable Weight</div><div class="value">' + weightHtml + '</div></div>');
                printWindow.document.write('</div>');

                // Items
                printWindow.document.write('<table class="items">');
                printWindow.document.write('<thead><tr><th style="width:40px;">#</th><th>Description</th><th class="text-center" style="width:60px;">Qty</th><th class="text-right" style="width:90px;">Unit Rate</th><th class="text-right" style="width:100px;">Amount</th></tr></thead>');
                printWindow.document.write('<tbody>' + rowsHtml + '</tbody>');
                printWindow.document.write('</table>');

                // Totals
                printWindow.document.write('<div class="totals"><table>' + totalsHtml + '</table></div>');

                // Footer
                printWindow.document.write('<div class="footer">');
                printWindow.document.write('<p>This is a system-generated invoice. Generated on ' + new Date().toLocaleString() + '.</p>');
                printWindow.document.write('<p>United Courier &copy; ' + new Date().getFullYear() + '. All rights reserved.</p>');
                printWindow.document.write('</div>');

                printWindow.document.write('</div></body></html>');
                printWindow.document.close();

                printWindow.onload = function () {
                    printWindow.print();
                    printWindow.onafterprint = function () {
                        printWindow.close();
                    };
                };

                if (printWindow.document.readyState === 'complete') {
                    printWindow.print();
                    printWindow.onafterprint = function () {
                        printWindow.close();
                    };
                }
            }

            // Cancel button click handler
            let cancelId = null;

            // Cancel button click handler (delegated)
            $('#shipmentsTable').on('click', '.cancel-btn', function () {
                cancelId = $(this).data('id');
                const invoiceRef = $(this).data('invoice');
                const isPaid = $(this).data('paid') == 1;
                const amount = $(this).data('amount');
                $('#cancelInvoiceRef').text(invoiceRef);

                // Show refund info if shipment was paid
                if (isPaid && amount > 0) {
                    $('#cancelRefundInfo').show();
                    $('#cancelRefundAmount').text('INR ' + number_format(amount, 2));
                } else {
                    $('#cancelRefundInfo').hide();
                }

                $('#cancelModal').modal('show');
            });

            // Confirm cancellation
            $('#confirmCancelBtn').on('click', function () {
                if (!cancelId) return;

                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Cancelling...');

                $.ajax({
                    url: '{{ url("/customer/cancel-shipment") }}/' + cancelId,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            // Refresh immediately so table + wallet balance + counters reflect the cancellation.
                            window.location.reload();
                            return;
                        } else {
                            showAlert('danger', response.message);
                        }
                        $('#cancelModal').modal('hide');
                        btn.prop('disabled', false).text('Yes, Cancel Shipment');
                    },
                    error: function (xhr) {
                        let msg = 'Error cancelling shipment.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        $('#cancelModal').modal('hide');
                        btn.prop('disabled', false).text('Yes, Cancel Shipment');
                    }
                });
            });

            // Pay Now button click handler
            @php
                $authCustomer = auth()->guard('customer')->user();
                $currentWalletBalance = $authCustomer && $authCustomer->wallet ? $authCustomer->wallet->balance : 0;
            @endphp
            let payInvoiceId = null;
            let payShipperId = null;
            let bulkPayQueue = [];
            let walletBalance = {{ $currentWalletBalance }};

            // Update wallet balance display in modal
            $('#payWalletBalance').text('INR ' + number_format(walletBalance, 2));

            $('#payNowModal').on('hidden.bs.modal', function () {
                bulkPayQueue = [];
                payInvoiceId = null;
                payShipperId = null;
            });

            // Pay Now button click handler (delegated)
            $('#shipmentsTable').on('click', '.pay-now-btn', function () {
                bulkPayQueue = [];
                payInvoiceId = $(this).data('invoice-id');
                payShipperId = $(this).data('shipper-id');
                const amount = $(this).data('amount');

                // Set reference and default amount
                const refText = shipmentData[payInvoiceId] ? (shipmentData[payInvoiceId].awb_number || shipmentData[payInvoiceId].invoice_number) : 'Shipment #' + payInvoiceId;
                $('#payShipmentRef').val(refText);
                $('#payAmount').val(amount);
                $('#payWalletBalance').text('INR ' + number_format(walletBalance, 2));

                $('#payNowModal').modal('show');
            });

            // Confirm Pay Now — payment + auto-manifest (manifest moved from Packed button to here)
            $('#confirmPayNowBtn').on('click', function () {
                const amount = parseFloat($('#payAmount').val());
                if (!amount || amount <= 0) {
                    showAlert('danger', 'Please enter a valid amount to pay.');
                    return;
                }
                if (amount > walletBalance) {
                    showAlert('danger', 'Insufficient wallet balance! Your balance is INR ' + number_format(walletBalance, 2));
                    return;
                }

                const btn = $(this);
                const originalBtnHtml = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

                const queue = bulkPayQueue.length ? bulkPayQueue : [{
                    invoice_id: payInvoiceId,
                    shipper_id: payShipperId,
                    amount: amount
                }];

                let index = 0;
                let successfulPayments = 0;
                let successfulManifests = 0;
                let failedManifests = 0;

                const markRowReady = function (item, response) {
                    const $row = $('#invoice-row-' + item.invoice_id);
                    if (!$row.length) return;

                    $row.attr('data-status', 'ready').data('status', 'ready');
                    $row.find('.shipment-status-badge')
                        .removeClass()
                        .addClass('shipment-status-badge badge bg-info')
                        .text('Ready for Packing');

                    if (shipmentData[item.invoice_id]) {
                        shipmentData[item.invoice_id].status = 'ready';
                    }

                    liveStatusCounts.draft = Math.max(0, liveStatusCounts.draft - 1);
                    liveStatusCounts.ready += 1;
                    walletBalance = Number(response.new_balance ?? walletBalance);
                    $('#payWalletBalance').text('INR ' + number_format(walletBalance, 2));
                    refreshStatusCounters();
                };

                // Manifest Confirm Payment par hota hai, lekin status Ready hi rehta hai (user requirement).
                const manifestAfterPayment = function (item, done) {
                    btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Manifesting...');
                    $.ajax({
                        url: '{{ url("/customer/manifest") }}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            shipper_id: item.shipper_id,
                            target_status: 'ready'
                        },
                        success: function (manifestResponse) {
                            if (manifestResponse && manifestResponse.success) {
                                successfulManifests++;
                                // Payment is cut ONLY after the manifest succeeds — update wallet from manifest response.
                                if (typeof manifestResponse.new_balance !== 'undefined' && manifestResponse.new_balance !== null) {
                                    walletBalance = Number(manifestResponse.new_balance);
                                    $('#payWalletBalance').text('INR ' + number_format(walletBalance, 2));
                                }
                                // Show the manifest number in the Manifest column (sourced from the manifests table).
                                const pmNo = manifestResponse.manifest_number;
                                if (pmNo) {
                                    const $mRow = $('#invoice-row-' + item.invoice_id);
                                    if ($mRow.length) {
                                        revealManifestCell($mRow, pmNo);
                                    }
                                    if (shipmentData[item.invoice_id]) {
                                        shipmentData[item.invoice_id].manifest_number = pmNo;
                                    }
                                }
                                // Status Ready hi rehta hai, row already Ready mark ho chuki hai.
                            } else {
                                failedManifests++;
                                // Manifest failed — server reverts the shipment back to Draft, reflect it in the row.
                                if ($('#invoice-row-' + item.invoice_id).data('status') === 'ready') {
                                    revertRowToDraft($('#invoice-row-' + item.invoice_id));
                                }
                                showAlert('danger', (manifestResponse && manifestResponse.message) || 'Manifest failed. No payment was deducted from your wallet. The shipment has been moved back to Draft.');
                            }
                            done();
                        },
                        error: function (xhr) {
                            failedManifests++;
                            // Manifest failed — server reverts the shipment back to Draft, reflect it in the row.
                            if ($('#invoice-row-' + item.invoice_id).data('status') === 'ready') {
                                revertRowToDraft($('#invoice-row-' + item.invoice_id));
                            }
                            if (xhr.responseJSON && xhr.responseJSON.is_address_error) {
                                showAddressErrorFallbackModal(xhr.responseJSON);
                            } else {
                                let msg = 'Manifest failed. No payment was deducted from your wallet. The shipment has been moved back to Draft.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg += ' ' + xhr.responseJSON.message;
                                }
                                showAlert('danger', msg);
                            }
                            done();
                        }
                    });
                };

                const processNext = function () {
                    if (index >= queue.length) {
                        $('#payNowModal').modal('hide');
                        btn.prop('disabled', false).html(originalBtnHtml);

                        // Never show the "Manifest Successful!" popup when nothing was actually manifested.
                        if (!successfulManifests) return;

                        $('#paymentSuccessPopup').remove();
                        const allSucceeded = failedManifests === 0;
                        const popupTitle = allSucceeded ? 'Manifest Successful!' : 'Manifest Partially Successful';
                        const popupIcon = allSucceeded ? 'ti-circle-check' : 'ti-alert-triangle';
                        const popupColor = allSucceeded ? '#28a745' : '#ffc107';
                        const popupBtnClass = allSucceeded ? 'btn-success' : 'btn-warning';
                        let resultText = successfulManifests + ' shipment(s) manifested successfully';
                        if (failedManifests) {
                            resultText += '.<br>' + failedManifests + ' shipment(s) failed to manifest and were moved back to Draft. No payment was deducted for them.';
                        }
                        resultText += '.<br>Payment was deducted from your wallet only after the manifest succeeded.<br>New wallet balance: INR ' + number_format(walletBalance, 2);
                        const popupHtml = '<div class="modal fade" id="paymentSuccessPopup" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content border-0 shadow"><div class="modal-body text-center py-4"><div class="mb-3"><i class="ti ' + popupIcon + ' fs-48" style="color:' + popupColor + ';"></i></div><h5 class="fw-bold mb-1">' + popupTitle + '</h5><p class="text-muted mb-3">' + resultText + '</p><button type="button" class="btn ' + popupBtnClass + ' px-4" data-bs-dismiss="modal">OK</button></div></div></div></div>';
                        $('body').append(popupHtml);
                        const popupElement = document.getElementById('paymentSuccessPopup');
                        const successPopup = new bootstrap.Modal(popupElement);
                        popupElement.addEventListener('hidden.bs.modal', function () {
                            this.remove();
                        });
                        successPopup.show();
                        return;
                    }

                    const item = queue[index];
                    index++;

                    $.ajax({
                        url: '{{ url("/customer/pay-now") }}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            invoice_id: item.invoice_id,
                            shipper_id: item.shipper_id,
                            amount: item.amount
                        },
                        success: function (response) {
                            if (response.success) {
                                successfulPayments++;
                                markRowReady(item, response);
                                manifestAfterPayment(item, processNext);
                            } else {
                                showAlert('danger', response.message || 'Payment failed for one shipment.');
                                processNext();
                            }
                        },
                        error: function (xhr) {
                            let msg = 'Error processing payment.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            showAlert('danger', msg);
                            processNext();
                        }
                    });
                };

                processNext();
            });

            // number_format helper for JS
            function number_format(num, decimals) {
                decimals = decimals || 2;
                num = parseFloat(num);
                const parts = num.toFixed(decimals).split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return parts.join('.');
            }

            // Show alert helper
            function showAlert(type, message) {
                const alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>';
                $('#alertContainer').html(alertHtml);
                // Auto scroll to top
                $('html, body').animate({ scrollTop: 0 }, 300);
                // Auto dismiss after 5 seconds
                setTimeout(function () {
                    $('.alert').alert('close');
                }, 5000);
            }

            // =============================================
            // MANIFEST: Single shipment manifest button
            // =============================================
            $('#shipmentsTable').on('click', '.manifest-single-btn', function () {
                const $btn = $(this);
                const originalButtonHtml = $btn.html();
                const shipperId = $btn.data('shipper-id');
                const invoiceId = $btn.data('invoice-id');

                if (!shipperId) return;

                // Confirm with user
                if (!confirm('Are you sure you want to manifest this shipment? This will call the appropriate shipping API (UPS or Ship Global) based on the shipment network.')) {
                    return;
                }

                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Manifesting...');

                $.ajax({
                    url: '{{ url("/customer/manifest") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        shipper_id: shipperId
                    },
                    success: function (response) {
                        if (response.success) {
                            // Update row status to manifested
                            const $row = $('#invoice-row-' + invoiceId);
                            $row.attr('data-status', 'manifested');
                            // Update status badge
                            const $badge = $row.find('.shipment-status-badge');
                            $badge.removeClass().addClass('shipment-status-badge badge bg-secondary').text('Manifested');
                            // Show the manifest number in the Manifest column (sourced from the manifests table).
                            const smNo = response.manifest_number;
                            if (smNo) {
                                revealManifestCell($row, smNo);
                                if (shipmentData[invoiceId]) {
                                    shipmentData[invoiceId].manifest_number = smNo;
                                }
                            }
                            // Remove Manifest + Pay Now buttons from the Action column (columns merged into Action)
                            $btn.remove();
                            $row.find('.pay-now-btn').remove();

                            // Payment is cut ONLY after the manifest succeeds — update wallet from response.
                            if (typeof response.new_balance !== 'undefined' && response.new_balance !== null) {
                                walletBalance = Number(response.new_balance);
                            }
                            let chargeNote = '';
                            if (response.amount_charged && Number(response.amount_charged) > 0) {
                                chargeNote = ' Payment of INR ' + number_format(response.amount_charged, 2) + ' deducted from your wallet.';
                            }
                            showAlert('success', 'Shipment manifested successfully! Tracking: ' + (response.tracking_number || 'N/A') + chargeNote);
                        } else {
                            // Manifest failed — server reverts a Ready shipment back to Draft, reflect it in the row.
                            if ($('#invoice-row-' + invoiceId).data('status') === 'ready') {
                                revertRowToDraft($('#invoice-row-' + invoiceId));
                            }
                            showAlert('danger', response.message || 'Manifest failed. No payment was deducted. The shipment has been moved back to Draft.');
                            $btn.prop('disabled', false).html(originalButtonHtml);
                        }
                    },
                    error: function (xhr) {
                        // Manifest failed — server reverts a Ready shipment back to Draft, reflect it in the row.
                        if ($('#invoice-row-' + invoiceId).data('status') === 'ready') {
                            revertRowToDraft($('#invoice-row-' + invoiceId));
                        }
                        // Check if this is an address error that can fall back to Ship Global
                        if (xhr.responseJSON && xhr.responseJSON.is_address_error) {
                            showAddressErrorFallbackModal(xhr.responseJSON);
                            $btn.prop('disabled', false).html(originalButtonHtml);
                            return;
                        }
                        let msg = 'Error manifesting shipment.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        $btn.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });

            // =============================================
            // BULK MANIFEST: Manifest multiple selected shipments
            // =============================================
            $('#bulkManifestBtn').on('click', function () {
                const $checked = $('.bulk-manifest-checkbox:checked');
                if ($checked.length === 0) {
                    showAlert('warning', 'Please select at least one shipment to manifest.');
                    return;
                }

                const shipperIds = $checked.map(function () {
                    return $(this).data('shipper-id');
                }).get();

                if (!confirm('Are you sure you want to manifest ' + shipperIds.length + ' selected shipment(s)? This will call the appropriate shipping API (UPS or Ship Global) based on each shipment\'s network.')) {
                    return;
                }

                const $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Manifesting ' + shipperIds.length + ' shipment(s)...');

                $.ajax({
                    url: '{{ url("/customer/bulk-manifest") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        shipper_ids: shipperIds
                    },
                    success: function (response) {
                        if (response.success) {
                            const results = response.results;
                            // Update each successfully manifested row
                            if (results.success && results.success.length > 0) {
                                results.success.forEach(function (item) {
                                    const $row = $('tr[data-shipper-id="' + item.shipper_id + '"]');
                                    $row.attr('data-status', 'manifested');
                                    const $badge = $row.find('.shipment-status-badge');
                                    $badge.removeClass().addClass('shipment-status-badge badge bg-secondary').text('Manifested');
                                    // Show the manifest number in the Manifest column (sourced from the manifests table).
                                    if (item.manifest_number) {
                                        revealManifestCell($row, item.manifest_number);
                                        const bmInvoiceId = $row.data('invoice-id');
                                        if (bmInvoiceId && shipmentData[bmInvoiceId]) {
                                            shipmentData[bmInvoiceId].manifest_number = item.manifest_number;
                                        }
                                    }
                                    $row.find('.manifest-single-btn, .pay-now-btn').remove();
                                });
                            }

                            // Manifest failures — server reverts Ready shipments back to Draft, reflect in the rows.
                            if (results.failed && results.failed.length > 0) {
                                results.failed.forEach(function (f) {
                                    const $r = $('tr[data-shipper-id="' + f.shipper_id + '"]');
                                    if ($r.length && $r.data('status') === 'ready') {
                                        revertRowToDraft($r);
                                    }
                                });
                            }
                            if (results.address_errors && results.address_errors.length > 0) {
                                results.address_errors.forEach(function (f) {
                                    const $r = $('tr[data-shipper-id="' + f.shipper_id + '"]');
                                    if ($r.length && $r.data('status') === 'ready') {
                                        revertRowToDraft($r);
                                    }
                                });
                            }

                            // Payment is cut ONLY after the manifest succeeds — update wallet from response.
                            if (typeof response.new_balance !== 'undefined' && response.new_balance !== null) {
                                walletBalance = Number(response.new_balance);
                            }

                            // Uncheck all
                            $('#selectAllCheckbox, .bulk-manifest-checkbox').prop('checked', false);

                            const failedCount = results.failed ? results.failed.length : 0;
                            const addressErrorCount = results.address_errors ? results.address_errors.length : 0;

                            if (addressErrorCount > 0) {
                                // Set up the queue and show the modal for the first address error
                                addressErrorQueue = results.address_errors;
                                addressErrorQueueIndex = 0;
                                showAddressErrorFallbackModal(addressErrorQueue[0]);

                                var summaryMsg = addressErrorCount + ' shipment(s) have address errors for UNITED ECO POST. Please review each and choose to ship via UNITED CLASSIC (Ship Global) or correct the address.';
                                if (failedCount > 0) {
                                    summaryMsg += '<br><br>Additionally, ' + failedCount + ' shipment(s) failed for other reasons.';
                                }
                                showAlert('warning', summaryMsg);
                            } else if (failedCount > 0) {
                                let failMsg = 'Some shipments failed to manifest:\n';
                                results.failed.forEach(function (f) {
                                    failMsg += '- Shipment #' + f.shipper_id + ': ' + f.message + '\n';
                                });
                                showAlert('warning', failMsg.replace(/\n/g, '<br>'));
                            } else {
                                showAlert('success', response.message);
                            }

                            $btn.prop('disabled', false).html('<i class="ti ti-package-export me-1"></i> Bulk Manifest');
                        } else {
                            showAlert('danger', response.message || 'Bulk manifest failed.');
                            $btn.prop('disabled', false).html('<i class="ti ti-package-export me-1"></i> Bulk Manifest');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Error during bulk manifest.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        $btn.prop('disabled', false).html('<i class="ti ti-package-export me-1"></i> Bulk Manifest');
                    }
                });
            });

            // =============================================
            // ADDRESS ERROR FALLBACK: Ship via UNITED CLASSIC (Ship Global)
            // Shows a modal with a dropdown when UNITED ECO POST returns an address error.
            // The customer can choose to ship via Ship Global or cancel & correct the address.
            // =============================================

            // Queue for bulk address errors (processed one at a time)
            var addressErrorQueue = [];
            var addressErrorQueueIndex = 0;

            // Populate and show the address error fallback modal
            function showAddressErrorFallbackModal(data) {
                // Store the shipper ID for the confirm handler
                $('#confirmShipGlobalFallbackBtn').data('shipper-id', data.shipper_id);

                // Populate rate details
                $('#fbTotalWeight').text((data.total_weight || 0) + ' kg');
                $('#fbClassicRate').text('\u20B9' + Number(data.classic_rate || 0).toFixed(2));
                $('#fbPaidAmount').text('\u20B9' + Number(data.paid_amount || 0).toFixed(2));

                // Format the difference
                var diff = Number(data.difference || 0);
                var diffText;
                if (diff > 0.01) {
                    diffText = '+ \u20B9' + diff.toFixed(2) + ' (extra to pay)';
                } else if (diff < -0.01) {
                    diffText = '- \u20B9' + Math.abs(diff).toFixed(2) + ' (to be refunded)';
                } else {
                    diffText = '\u20B90.00 (no difference)';
                }
                $('#fbDifference').text(diffText);
                $('#fbWalletBalance').text('\u20B9' + Number(data.wallet_balance || 0).toFixed(2));

                // Show wallet impact
                if (data.wallet_action && data.wallet_action !== 'none') {
                    var actionText = '';
                    if (data.wallet_action === 'deduct') {
                        actionText = '\u20B9' + Number(data.wallet_amount || 0).toFixed(2) + ' will be deducted from wallet';
                        $('#fbWalletAction').removeClass('text-success').addClass('text-danger');
                    } else if (data.wallet_action === 'refund') {
                        actionText = '\u20B9' + Number(data.wallet_amount || 0).toFixed(2) + ' will be refunded to wallet';
                        $('#fbWalletAction').removeClass('text-danger').addClass('text-success');
                    }
                    $('#fbWalletAction').text(actionText);
                    $('#fbWalletActionRow').show();
                } else {
                    $('#fbWalletActionRow').hide();
                }

                // Reset dropdown and hide rate info
                $('#addressErrorFallbackSelect').val('');
                $('#fallbackRateInfo').hide();
                $('#confirmShipGlobalFallbackBtn').prop('disabled', true);

                // Show the modal
                var modalEl = document.getElementById('addressErrorFallbackModal');
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.show();
            }

            // Dropdown change handler — show/hide rate info and enable/disable confirm button
            $('#addressErrorFallbackSelect').on('change', function () {
                var val = $(this).val();
                if (val === 'ship_global') {
                    $('#fallbackRateInfo').slideDown();
                    $('#confirmShipGlobalFallbackBtn').prop('disabled', false);
                } else if (val === 'cancel') {
                    $('#fallbackRateInfo').hide();
                    $('#confirmShipGlobalFallbackBtn').prop('disabled', false);
                } else {
                    $('#fallbackRateInfo').hide();
                    $('#confirmShipGlobalFallbackBtn').prop('disabled', true);
                }
            });

            // Confirm button click handler — call Ship Global fallback API or cancel
            $('#confirmShipGlobalFallbackBtn').on('click', function () {
                var $btn = $(this);
                var shipperId = $btn.data('shipper-id');
                var selectedOption = $('#addressErrorFallbackSelect').val();

                if (!shipperId) return;

                // If user chose "Cancel & correct the address" — cancel shipment and refund wallet
                if (selectedOption === 'cancel') {
                    if (!confirm('Are you sure you want to cancel this shipment? If a payment was deducted, it will be refunded to your wallet.')) {
                        return;
                    }
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Cancelling...');

                    $.ajax({
                        url: '{{ url("/customer/cancel-shipment-by-shipper") }}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            shipper_id: shipperId
                        },
                        success: function (response) {
                            if (response.success) {
                                // Hide modal
                                var modalEl = document.getElementById('addressErrorFallbackModal');
                                var modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();

                                // Update the row in the table to show cancelled status
                                var $row = $('tr[data-shipper-id="' + shipperId + '"]');
                                if ($row.length) {
                                    $row.attr('data-status', 'cancelled');
                                    var $badge = $row.find('.shipment-status-badge');
                                    $badge.removeClass().addClass('shipment-status-badge badge bg-danger').text('Cancelled');
                                    $row.find('.manifest-single-btn, .pay-now-btn, .cancel-btn').remove();
                                }

                                // If a refund was processed, update wallet balance from the response.
                                if (typeof response.new_balance !== 'undefined' && response.new_balance !== null) {
                                    walletBalance = Number(response.new_balance);
                                }

                                showAlert('success', response.message || 'Shipment cancelled. Any payment deducted will be refunded to your wallet.');
                            } else {
                                showAlert('danger', response.message || 'Failed to cancel shipment.');
                                $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Confirm & Manifest');
                            }
                        },
                        error: function (xhr) {
                            var msg = 'Error cancelling shipment.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            showAlert('danger', msg);
                            $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Confirm & Manifest');
                        }
                    });
                    return;
                }

                if (selectedOption !== 'ship_global') return;

                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

                $.ajax({
                    url: '{{ url("/customer/manifest-ship-global-fallback") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        shipper_id: shipperId
                    },
                    success: function (response) {
                        if (response.success) {
                            // Update the row in the table
                            var $row = $('tr[data-shipper-id="' + shipperId + '"]');
                            if ($row.length) {
                                $row.attr('data-status', 'manifested');
                                var $badge = $row.find('.shipment-status-badge');
                                $badge.removeClass().addClass('shipment-status-badge badge bg-secondary').text('Manifested');
                                // Show the manifest number in the Manifest column (sourced from the manifests table).
                                if (response.manifest_number) {
                                    revealManifestCell($row, response.manifest_number);
                                    var fbInvoiceId = $row.data('invoice-id');
                                    if (fbInvoiceId && shipmentData[fbInvoiceId]) {
                                        shipmentData[fbInvoiceId].manifest_number = response.manifest_number;
                                    }
                                }
                                $row.find('.manifest-single-btn, .pay-now-btn').remove();
                            }
                            // Payment is cut ONLY after the manifest succeeds — update wallet from response.
                            if (typeof response.new_balance !== 'undefined' && response.new_balance !== null) {
                                walletBalance = Number(response.new_balance);
                            }
                            // Hide modal
                            var modalEl = document.getElementById('addressErrorFallbackModal');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            showAlert('success', response.message || 'Shipment manifested via UNITED CLASSIC (Ship Global)! Tracking: ' + (response.tracking_number || 'N/A'));
                        } else {
                            showAlert('danger', response.message || 'Failed to manifest via Ship Global.');
                            $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Confirm & Manifest');
                        }
                    },
                    error: function (xhr) {
                        var msg = 'Error manifesting via Ship Global.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Confirm & Manifest');
                    }
                });
            });

            // When modal is hidden, process next address error in the queue (bulk manifest)
            var addressErrorModalEl = document.getElementById('addressErrorFallbackModal');
            addressErrorModalEl.addEventListener('hidden.bs.modal', function () {
                if (addressErrorQueue.length > 0 && (addressErrorQueueIndex + 1) < addressErrorQueue.length) {
                    addressErrorQueueIndex++;
                    setTimeout(function () {
                        showAddressErrorFallbackModal(addressErrorQueue[addressErrorQueueIndex]);
                    }, 300);
                } else {
                    // All address errors processed — reset queue
                    addressErrorQueue = [];
                    addressErrorQueueIndex = 0;
                }
            });

            // =============================================
            // SELECT ALL: Check/Uncheck all visible checkboxes
            // =============================================
            $('#selectAllCheckbox').on('change', function () {
                const isChecked = $(this).is(':checked');
                $('.bulk-manifest-checkbox:visible').prop('checked', isChecked);
                updateBulkSelectionTotals();
            });

            // Uncheck "Select All" if any individual checkbox is unchecked
            $(document).on('change', '.bulk-manifest-checkbox', function () {
                if (!$(this).is(':checked')) {
                    $('#selectAllCheckbox').prop('checked', false);
                } else if ($('.bulk-manifest-checkbox:visible:checked').length === $('.bulk-manifest-checkbox:visible').length) {
                    $('#selectAllCheckbox').prop('checked', true);
                }
                updateBulkSelectionTotals();
            });

            $('#bulkDraftPayBtn').on('click', function () {
                const rows = getSelectedRowsByStatus('draft');
                if (!rows.length) {
                    showAlert('warning', 'Please select at least one Draft shipment to pay.');
                    return;
                }

                const total = sumSelectedAmounts(rows);
                bulkPayQueue = rows.map(function () {
                    const $row = $(this);
                    return {
                        invoice_id: $row.find('.pay-now-btn').data('invoice-id'),
                        shipper_id: $row.find('.pay-now-btn').data('shipper-id'),
                        amount: parseFloat($row.find('.pay-now-btn').data('amount') || 0)
                    };
                }).get();

                payInvoiceId = null;
                payShipperId = null;
                $('#payShipmentRef').val('Selected Draft Shipment(s) (' + rows.length + ')');
                $('#payAmount').val(total);
                $('#payWalletBalance').text('INR ' + number_format(walletBalance, 2));
                $('#payNowModal').modal('show');
            });

            $('#bulkDraftCancelBtn').on('click', function () {
                const rows = getSelectedRowsByStatus('draft');
                if (!rows.length) {
                    showAlert('warning', 'Please select at least one Draft shipment to cancel.');
                    return;
                }

                const total = sumSelectedAmounts(rows);
                const confirmed = confirm('Cancel ' + rows.length + ' selected Draft shipment(s) and refund INR ' + number_format(total, 2) + '?');
                if (!confirmed) return;

                const queue = rows.map(function () {
                    const $row = $(this);
                    return $row.find('.cancel-btn').data('id') || $row.data('invoice-id');
                }).get();

                let index = 0;
                const processNext = function () {
                    if (index >= queue.length) {
                        window.location.reload();
                        return;
                    }
                    const id = queue[index];
                    index++;
                    $.ajax({
                        url: '{{ url("/customer/cancel-shipment") }}/' + id,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (!response.success) {
                                showAlert('danger', response.message || 'Cancel failed for one shipment.');
                            }
                            processNext();
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Cancel failed for one shipment.';
                            showAlert('danger', msg);
                            processNext();
                        }
                    });
                };

                processNext();
            });

            $('#bulkReadyPrintBtn').on('click', function () {
                const rows = getSelectedRowsByStatus('ready');
                if (!rows.length) {
                    showAlert('warning', 'Please select at least one Ready shipment to print.');
                    return;
                }

                const queue = rows.map(function () {
                    const $row = $(this);
                    return {
                        invoice_id: $row.find('.print-label-btn').data('invoice-id'),
                        shipper_id: $row.data('shipper-id') || $row.find('.print-label-btn').data('shipper-id')
                    };
                }).get().filter(function (item) {
                    return item.invoice_id && item.shipper_id;
                });

                const storedInvoiceIds = [];
                let index = 0;
                const processNext = function () {
                    if (index >= queue.length) {
                        printSelectedLabelsByInvoiceIds(storedInvoiceIds);
                        return;
                    }

                    const item = queue[index];
                    index++;

                    const labelData = shipmentData[item.invoice_id];
                    if (!labelData) {
                        showAlert('danger', 'No label data found for one selected shipment.');
                        processNext();
                        return;
                    }

                    const customLabel = buildBulkLabelHtml(labelData);
                    $.ajax({
                        url: '{{ url("/customer/mark-packed") }}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            shipper_id: item.shipper_id,
                            custom_label: customLabel
                        },
                        success: function (response) {
                            if (response.success) {
                                storedInvoiceIds.push(item.invoice_id);
                                const $row = $('tr[data-shipper-id="' + item.shipper_id + '"]');
                                if ($row.length) {
                                    $row.attr('data-status', 'packed').data('status', 'packed');
                                    const $badge = $row.find('.shipment-status-badge');
                                    $badge.removeClass().addClass('shipment-status-badge badge bg-primary').text('Packed');
                                    if (shipmentData[item.invoice_id]) {
                                        shipmentData[item.invoice_id].status = 'packed';
                                    }
                                    liveStatusCounts.ready = Math.max(0, liveStatusCounts.ready - 1);
                                    liveStatusCounts.packed += 1;
                                    refreshStatusCounters();
                                }
                            } else {
                                showAlert('danger', response.message || 'Failed to move shipment to Packed.');
                            }
                            processNext();
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to move shipment to Packed.';
                            showAlert('danger', msg);
                            processNext();
                        }
                    });
                };

                processNext();
            });

            $('#bulkReadyCancelBtn').on('click', function () {
                const rows = getSelectedRowsByStatus('ready');
                if (!rows.length) {
                    showAlert('warning', 'Please select at least one Ready shipment to cancel.');
                    return;
                }

                const total = sumSelectedAmounts(rows);
                const confirmed = confirm('Cancel ' + rows.length + ' selected Ready shipment(s) and refund INR ' + number_format(total, 2) + '?');
                if (!confirmed) return;

                const queue = rows.map(function () {
                    const $row = $(this);
                    return $row.find('.cancel-btn').data('id') || $row.data('invoice-id');
                }).get();

                let index = 0;
                const processNext = function () {
                    if (index >= queue.length) {
                        window.location.reload();
                        return;
                    }
                    const id = queue[index];
                    index++;
                    $.ajax({
                        url: '{{ url("/customer/cancel-shipment") }}/' + id,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (!response.success) {
                                showAlert('danger', response.message || 'Cancel failed for one shipment.');
                            }
                            processNext();
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Cancel failed for one shipment.';
                            showAlert('danger', msg);
                            processNext();
                        }
                    });
                };

                processNext();
            });

            $('#bulkPackedPrintBtn').on('click', function () {
                let rows = getSelectedRowsByStatus('packed');
                if (!rows.length) {
                    showAlert('warning', 'Please select at least one Packed shipment to print.');
                    return;
                }

                // COD / FOC / prepare rows have no printable label - skip them.
                rows = rows.filter(function () {
                    const t = String($(this).data('shipment-type') || '');
                    return t !== '2' && t !== '3' && t !== '4';
                });
                if (!rows.length) {
                    showAlert('warning', 'Selected shipments are COD/FOC/prepare orders. Print label is not available for them.');
                    return;
                }

                const invoiceIds = rows.map(function () {
                    return $(this).find('.print-label-btn').data('invoice-id');
                }).get().filter(Boolean);

                printSelectedLabelsByInvoiceIds(invoiceIds);
            });

            $('#bulkPackedCancelBtn').on('click', function () {
                const rows = getSelectedRowsByStatus('packed');
                if (!rows.length) {
                    showAlert('warning', 'Please select at least one Packed shipment to cancel.');
                    return;
                }

                const total = sumSelectedAmounts(rows);
                const confirmed = confirm('Cancel ' + rows.length + ' selected Packed shipment(s) and refund INR ' + number_format(total, 2) + '?');
                if (!confirmed) return;

                const queue = rows.map(function () {
                    return $(this).find('.cancel-btn').data('id') || $(this).data('invoice-id');
                }).get().filter(Boolean);

                let index = 0;
                const processNext = function () {
                    if (index >= queue.length) {
                        window.location.reload();
                        return;
                    }

                    const id = queue[index];
                    index++;
                    $.ajax({
                        url: '{{ url("/customer/cancel-shipment") }}/' + id,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (!response.success) {
                                showAlert('danger', response.message || 'Cancel failed for one shipment.');
                            }
                            processNext();
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Cancel failed for one shipment.';
                            showAlert('danger', msg);
                            processNext();
                        }
                    });
                };

                processNext();
            });

            // =============================================
            // MANIFEST: Close manifest button
            // =============================================
            let closeManifestNumber = null;

            $(document).on('click', '.close-manifest-btn', function () {
                const manifestNumber = $(this).data('manifest-number');

                if (!manifestNumber) return;

                closeManifestNumber = manifestNumber;
                $('#closeManifestNumberRef').text(manifestNumber);
                $('#closeManifestModal').modal('show');
            });

            $('#confirmCloseManifestBtn').on('click', function () {
                const $btn = $(this);

                if (!closeManifestNumber) return;

                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Closing...');

                $.ajax({
                    url: '{{ url("/customer/manifest/close") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        manifest_number: closeManifestNumber
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#closeManifestModal').modal('hide');
                            closeManifestNumber = null;
                            $btn.prop('disabled', false).html('<i class="ti ti-lock me-1"></i>Yes, Close Manifest');
                            showAlert('success', response.message);
                            setTimeout(function () { window.location.reload(); }, 1200);
                        } else {
                            showAlert('danger', response.message || 'Unable to close manifest.');
                            $btn.prop('disabled', false).html('<i class="ti ti-lock me-1"></i>Yes, Close Manifest');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Unable to close manifest. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        $btn.prop('disabled', false).html('<i class="ti ti-lock me-1"></i>Yes, Close Manifest');
                    }
                });
            });

            // =============================================
            // MANIFEST: Assign for Pickup button
            // =============================================
            let assignPickupNumber = null;

            $(document).on('click', '.md-pickup-date-option', function () {
                const $opt = $(this);
                $opt.closest('.modal').find('.md-pickup-date-option').removeClass('selected');
                $opt.addClass('selected');
                $opt.closest('.modal').find('input[type="hidden"]').val($opt.data('value'));
            });

            $(document).on('click', '.assign-pickup-btn', function () {
                const manifestNumber = $(this).data('manifest-number');

                if (!manifestNumber) return;

                assignPickupNumber = manifestNumber;
                $('#assignPickupNumberRef').text(manifestNumber);
                $('#assignPickupModal').modal('show');
            });

            $('#confirmAssignPickupBtn').on('click', function () {
                const $btn = $(this);

                if (!assignPickupNumber) return;

                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Assigning...');

                $.ajax({
                    url: '{{ url("/customer/manifest/assign-pickup") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        manifest_number: assignPickupNumber,
                        pickup_date: $('#assignPickupDate').val()
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#assignPickupModal').modal('hide');
                            assignPickupNumber = null;
                            $btn.prop('disabled', false).html('<i class="ti ti-truck me-1"></i>Yes, Assign for Pickup');
                            showAlert('success', response.message);
                            setTimeout(function () { window.location.reload(); }, 1200);
                        } else {
                            showAlert('danger', response.message || 'Unable to assign manifest for pickup.');
                            $btn.prop('disabled', false).html('<i class="ti ti-truck me-1"></i>Yes, Assign for Pickup');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Unable to assign manifest for pickup. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        $btn.prop('disabled', false).html('<i class="ti ti-truck me-1"></i>Yes, Assign for Pickup');
                    }
                });
            });

        });

        // =====================================================================
        // 4x6 Courier Label (standard courier label format)
        // =====================================================================

        const courierLabelStyles =
            '*{box-sizing:border-box;}' +
            'html,body{margin:0;padding:0;background:#fff;}' +
            'body{padding:10px;font-family:Arial,Helvetica,sans-serif;color:#111;}' +
            '.label{width:384px;height:576px;margin:0 auto;border:2px solid #222;background:#fff;display:flex;flex-direction:column;}' +
            '.header{text-align:center;padding:7px 7px 5px;border-bottom:2px solid #222;flex-shrink:0;}' +
            '.company-name{font-size:16px;font-weight:700;line-height:18px;margin-bottom:3px;}' +
            '.company-address{font-size:11px;line-height:13px;}' +
            '.company-contact{font-size:11px;line-height:13px;margin-top:3px;}' +
            '.box-badge{margin-top:4px;font-size:12px;font-weight:700;letter-spacing:1px;text-align:center;border-top:1px solid #222;padding-top:4px;}' +
            '.ship-row{height:26px;display:flex;border-bottom:1px solid #222;align-items:center;flex-shrink:0;}' +
            '.ship-title{width:70%;padding-left:8px;font-size:12px;font-weight:700;}' +
            '.date{width:30%;padding-right:8px;text-align:right;font-size:10px;}' +
            '.section-title{height:24px;padding:5px 8px 4px;font-size:12px;font-weight:700;border-bottom:1px solid #222;display:flex;align-items:center;flex-shrink:0;}' +
            '.address{padding:6px 24px 6px;font-size:11px;line-height:15px;border-bottom:1px solid #222;}' +
            '.from-address{flex:1;min-height:48px;}' +
            '.to-address{flex:1.35;min-height:70px;}' +
            '.receiver-name{font-size:13px;font-weight:700;margin-bottom:4px;}' +
            '.shipping-info{height:45px;display:grid;grid-template-columns:1.15fr 1fr .65fr .55fr .85fr;border-bottom:1px solid #222;flex-shrink:0;}' +
            '.info{padding:5px 6px 4px;font-size:10px;line-height:13px;}' +
            '.info-title{font-size:10px;font-weight:700;margin-bottom:3px;}' +
            '.center{text-align:center;}' +
            '.content{height:34px;display:flex;align-items:center;padding:4px 8px;border-bottom:1px solid #222;font-size:11px;flex-shrink:0;}' +
            '.content-title{width:72px;font-weight:700;}' +
            '.barcode-area{height:104px;padding-top:11px;text-align:center;border-bottom:1px solid #222;flex-shrink:0;}' +
            '.barcode-area svg{height:52px;max-width:100%;}' +
            '.barcode-number{margin-top:8px;font-size:13px;font-weight:700;}' +
            '.bottom{padding:8px 18px 9px;text-align:center;flex-shrink:0;}' +
            '.bottom-line{height:2px;background:#111;margin-bottom:5px;}' +
            '.country{font-size:19px;font-weight:700;line-height:21px;}' +
            '@media print{html,body{padding:0;} .label{width:100%;height:6in;margin:0;}}' +
            '@media (max-width:480px){body{padding:3px;} .label{width:100%;height:auto;min-height:576px;}}';

        // Friendly destination country for the label's bottom "country" box
        function getDestinationCountryCode(destination) {
            if (!destination) return 'INTL';
            const dest = String(destination);
            const map = {
                'US- United State of America': 'USA',
                'US': 'USA', 'USA': 'USA',
                'India': 'INDIA', 'IN': 'INDIA',
                'UK - United Kingdom': 'UK', 'UK': 'UK', 'GB': 'UK', 'United Kingdom': 'UK',
                'Canada': 'CANADA', 'CA': 'CANADA',
                'Australia': 'AUSTRALIA', 'AU': 'AUSTRALIA',
                'Srilanka': 'SRILANKA', 'Sri Lanka': 'SRILANKA', 'LK': 'SRILANKA',
                'China': 'CHINA', 'CN': 'CHINA',
                'Russia': 'RUSSIA', 'RU': 'RUSSIA',
                'Germany': 'GERMANY', 'DE': 'GERMANY',
                'France': 'FRANCE', 'FR': 'FRANCE',
                'UAE': 'UAE', 'AE': 'UAE', 'United Arab Emirates': 'UAE',
                'Singapore': 'SINGAPORE', 'SG': 'SINGAPORE'
            };
            if (map[dest]) return map[dest];
            const m = dest.match(/^([A-Z]{2,3})[\s-]/);
            return m ? m[1].toUpperCase() : dest.toUpperCase();
        }

        // D/S (destination service code) derived from the destination name
        function getDestinationDs(destination) {
            if (!destination) return '-';
            const dest = String(destination);
            const m = dest.match(/^([A-Z]{2})[\s-]/);
            return m ? m[1].toUpperCase() : dest.slice(0, 4).toUpperCase();
        }

        // Build one 4x6 courier label (returns the outer .label HTML, barcode embedded).
        // When boxIndex/boxCount are given, the label is built for ONE box of the
        // shipment (per-box ACT WT., marked "BOX X OF N").
        function buildCourierLabelHtml(data, boxIndex, boxCount) {
            const shipper = data.shipper || {};
            const consignee = data.consignee || {};
            const items = Array.isArray(data.items) ? data.items : [];
            const packages = Array.isArray(data.packages) ? data.packages : [];

            const packageCount = packages.length || 1;
            const hasBoxContext = typeof boxIndex === 'number' && typeof boxCount === 'number';
            const boxIdx = hasBoxContext ? boxIndex : 1;
            const boxTot = hasBoxContext ? boxCount : packageCount;

            // ACT WT. shows this box's weight on a per-box label, otherwise the
            // summed weight of all packages on a single combined label.
            let actualWeight = 0;
            if (hasBoxContext && packages[boxIdx - 1]) {
                actualWeight = parseFloat(packages[boxIdx - 1].weight) || 0;
            } else {
                packages.forEach(function (pkg) {
                    actualWeight += parseFloat(pkg.weight) || 0;
                });
            }

            // CONTENT shows only the items that belong to this box on a
            // per-box label (matched by box_no). On a combined label (no box
            // context) all items are shown. If no item maps to this box, fall
            // back to the full item list so the label never looks empty.
            let boxItems = items;
            if (hasBoxContext) {
                boxItems = items.filter(function (item) {
                    return String(item.box_no) === String(boxIdx);
                });
                if (!boxItems.length) {
                    boxItems = items;
                }
            }
            const itemText = boxItems.map(function (item) {
                return item.description;
            }).filter(Boolean).join(', ') || 'Goods';

            const awb = data.awb_number || 'N/A';
            const date = data.invoice_date || new Date().toLocaleDateString('en-GB');
            // NETWORK comes from the courier service's api_provider; SERVICE is
            // the courier service's service_code. Fall back to the shipping
            // method / service_code if the courier service lookup is missing.
            const network = data.api_provider || (data.shipping_method || '-');
            const service = data.service_code || '-';
            const country = getDestinationCountryCode(data.destination);
            const ds = getDestinationDs(data.destination);

            const labelHtml =
                '<div class="label">' +
                    '<div class="header">' +
                        '<div class="company-name">United Worldwide Courier Pvt. Ltd.</div>' +
                        '<div class="company-address">A-219, First Floor, Road No. 5 Mahipalpur Extension, New Delhi 110037</div>' +
                        '<div class="company-contact">TEL:-011-46122222,www.unitedcouriers.biz</div>' +
                        '<div class="box-badge">BOX ' + boxIdx + ' OF ' + boxTot + '</div>' +
                    '</div>' +
                    '<div class="ship-row">' +
                        '<div class="ship-title">SHIP FROM:</div>' +
                        '<div class="date"><b>DATE:</b>&nbsp;&nbsp;&nbsp;' + date + '</div>' +
                    '</div>' +
                    '<div class="section-title">' + (shipper.company || 'SHIPPER') + '</div>' +
                    '<div class="address from-address">' +
                        (shipper.address || '') + '<br>' +
                        (shipper.city_state_pin || '') + '<br>' +
                        (shipper.phone || '') +
                    '</div>' +
                    '<div class="section-title">SHIP TO:</div>' +
                    '<div class="address to-address">' +
                        '<div class="receiver-name">' + (consignee.name || '-') + '</div>' +
                        (consignee.address || '') + '<br>' +
                        (consignee.city_state_zip || '') + '<br>' +
                        (consignee.phone || '-') +
                    '</div>' +
                    '<div class="shipping-info">' +
                        '<div class="info"><div class="info-title">NETWORK</div>' + network + '</div>' +
                        '<div class="info"><div class="info-title">SERVICE</div>' + service + '</div>' +
                        '<div class="info center"><div class="info-title">D/S</div>' + ds + '</div>' +
                        '<div class="info center"><div class="info-title">PCS</div>' + packageCount + '</div>' +
                        '<div class="info center"><div class="info-title">ACT WT.</div>' + actualWeight.toFixed(3) + '</div>' +
                    '</div>' +
                    '<div class="content">' +
                        '<div class="content-title">CONTENT</div>' +
                        '<div>' + itemText + '</div>' +
                    '</div>' +
                    '<div class="barcode-area">' +
                        '<svg data-label-barcode></svg>' +
                        '<div class="barcode-number">' + awb + '</div>' +
                    '</div>' +
                    '<div class="bottom">' +
                        '<div class="bottom-line"></div>' +
                        '<div class="country">' + country + '</div>' +
                    '</div>' +
                '</div>';

            const container = document.createElement('div');
            container.innerHTML = labelHtml;
            const barcode = container.querySelector('[data-label-barcode]');
            JsBarcode(barcode, awb, {
                format: 'CODE128',
                lineColor: '#000',
                width: 2,
                height: 52,
                displayValue: false,
                margin: 0
            });
            barcode.removeAttribute('data-label-barcode');
            return container.firstElementChild.outerHTML;
        }

        function buildBulkLabelHtml(data) {
            const shipper = data.shipper || {};
            const consignee = data.consignee || {};
            const items = Array.isArray(data.items) ? data.items : [];
            const packages = Array.isArray(data.packages) ? data.packages : [];
            const itemsHtml = items.length ? items.map(function (item) {
                return '<tr>' +
                    '<td>' + (item.box_no || '-') + '</td>' +
                    '<td>' + (item.description || '-') + '</td>' +
                    '<td>' + (item.hs_code || '-') + '</td>' +
                    '<td>' + (item.qty || '-') + '</td>' +
                    '<td>' + (item.unit_rate || '-') + '</td>' +
                    '<td>' + (item.igst_percentage || '-') + '</td>' +
                    '<td>' + (item.igst_amount || '-') + '</td>' +
                    '<td>' + (item.amount || '0.00') + '</td>' +
                    '</tr>';
            }).join('') : '<tr><td colspan="8">No items</td></tr>';

            const packageHtml = packages.length ? packages.map(function (pkg) {
                return '<div style="border:1px solid #dee2e6;border-radius:6px;padding:8px;margin-bottom:6px;">' +
                    '<strong>Box #' + (pkg.index || '-') + '</strong>: ' +
                    'Weight: ' + (pkg.weight || '-') + ' Kg | ' +
                    'L: ' + (pkg.length || '-') + ' × W: ' + (pkg.width || '-') + ' × H: ' + (pkg.height || '-') + ' cm | ' +
                    'Vol. Wt: ' + (pkg.volumetric || '-') + ' Kg | Chg. Wt: ' + (pkg.chargeable || '-') + ' Kg' +
                    '</div>';
            }).join('') : '<div class="text-muted">No package details</div>';

            const labelHtml = '<div style="border:1px solid #ddd;padding:16px;margin:20px 0;border-radius:10px;page-break-inside:avoid;">' +
                '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">' +
                '<img src="{{ asset("assets/img/logo.png") }}" alt="United Courier" style="max-height:50px;">' +
                '<svg data-label-barcode style="width:100%;max-width:260px;height:auto;"></svg>' +
                '</div>' +
                '<div style="display:flex;gap:20px;margin-bottom:12px;">' +
                '<div style="flex:1;border:1px solid #ddd;padding:8px;border-radius:6px;">' +
                '<strong>SHIP FROM</strong><br>' +
                '<span>' + (shipper.company || '-') + '</span><br>' +
                '<span>' + (shipper.contact || '-') + '</span><br>' +
                '<span>' + (shipper.address || '-') + '</span><br>' +
                '<span>' + (shipper.city_state_pin || '-') + '</span><br>' +
                '<span>Phone: ' + (shipper.phone || '-') + '</span>' +
                '</div>' +
                '<div style="flex:1;border:1px solid #ddd;padding:8px;border-radius:6px;">' +
                '<strong>SHIP TO</strong><br>' +
                '<span>' + (consignee.name || '-') + '</span><br>' +
                '<span>' + (consignee.contact || '-') + '</span><br>' +
                '<span>' + (consignee.address || '-') + '</span><br>' +
                '<span>' + (consignee.city_state_zip || '-') + '</span><br>' +
                '<span>Phone: ' + (consignee.phone || '-') + '</span>' +
                '</div>' +
                '</div>' +
                '<div style="margin-bottom:12px;font-size:12px;">' +
                '<strong>SHIPMENT INFO</strong>' +
                '<div style="display:flex;flex-wrap:wrap;margin-top:4px;">' +
                '<div style="width:50%;padding:2px 0;"><strong>Invoice No.:</strong> ' + (data.invoice_number || '-') + '</div>' +
                '<div style="width:50%;padding:2px 0;"><strong>Invoice Date:</strong> ' + (data.invoice_date || '-') + '</div>' +
                '<div style="width:50%;padding:2px 0;"><strong>Reference No.:</strong> ' + (data.reference_number || '-') + '</div>' +
                '<div style="width:50%;padding:2px 0;"><strong>Method:</strong> ' + (data.shipping_method || '-') + '</div>' +
                '<div style="width:50%;padding:2px 0;"><strong>Service Code:</strong> ' + (data.service_code || '-') + '</div>' +
                '</div>' +
                '</div>' +
                '<div style="margin-bottom:12px;">' +
                '<strong>INVOICE ITEMS</strong>' +
                '<table style="width:100%;border-collapse:collapse;margin-top:8px;">' +
                '<thead><tr><th style="border:1px solid #333;padding:4px;">Box</th><th style="border:1px solid #333;padding:4px;">Description</th><th style="border:1px solid #333;padding:4px;">HS Code</th><th style="border:1px solid #333;padding:4px;">Qty</th><th style="border:1px solid #333;padding:4px;">Rate</th><th style="border:1px solid #333;padding:4px;">IGST(%)</th><th style="border:1px solid #333;padding:4px;">IGST</th><th style="border:1px solid #333;padding:4px;">Amount</th></tr></thead>' +
                '<tbody>' + itemsHtml + '</tbody>' +
                '</table>' +
                '<div style="text-align:right;margin-top:6px;font-size:12px;"><strong>Total: ' + (data.items_total || '0.00') + '</strong></div>' +
                '</div>' +
                '<div>' +
                '<strong>PACKAGE DIMENSIONS</strong>' +
                '<div style="margin-top:8px;">' + packageHtml + '</div>' +
                '</div>' +
                '</div>';

            const container = document.createElement('div');
            container.innerHTML = labelHtml;
            const barcode = container.querySelector('[data-label-barcode]');
            JsBarcode(barcode, data.awb_number || 'N/A', {
                format: 'CODE128',
                lineColor: '#000',
                width: 2,
                height: 100,
                displayValue: true,
                fontSize: 16
            });
            barcode.removeAttribute('data-label-barcode');

            return container.firstElementChild.outerHTML;
        }

        function printSelectedLabelsByInvoiceIds(invoiceIds) {
            if (!invoiceIds || !invoiceIds.length) {
                showAlert('warning', 'Please select at least one shipment to print.');
                return;
            }

            const selectedData = invoiceIds
                .map(function (invoiceId) {
                    return shipmentData[invoiceId];
                })
                .filter(Boolean);

            if (!selectedData.length) {
                showAlert('danger', 'No print data found for the selected shipments.');
                return;
            }

            const printWindow = window.open('', '_blank', 'width=900,height=700');
            if (!printWindow) {
                showAlert('danger', 'Popup blocked. Allow popups to print labels.');
                return;
            }

            const sizeSelect = document.querySelector('.bulk-label-size');
            const labelSize = sizeSelect ? sizeSelect.value : 'a4';
            const is4x6 = labelSize === '4x6';
            let html = '<!DOCTYPE html><html><head><title>Print Selected Labels</title><style>' +
                // @page margin stays 0 so the browser does not print its own
                // header/footer (date/time). Spacing is handled via body padding.
                (is4x6
                    ? courierLabelStyles + '@page{size:4in 6in;margin:0;}'
                    : '@page{size:A4;margin:0;} body{padding:8mm;} ') +
                (is4x6 ? '' : 'body{font-family:Arial,sans-serif;color:#000;margin:0;box-sizing:border-box;} ') +
                (is4x6 ? '' : 'table{border-collapse:collapse;width:100%;} ') +
                (is4x6 ? '' : 'th,td{border:1px solid #333;padding:4px;text-align:left;} ') +
                (is4x6 ? '' : '@media print{body{margin:0;}}') +
                '</style></head><body>';

            // Total number of 4x6 labels so page breaks go BETWEEN labels only.
            let totalLabels = 0;
            selectedData.forEach(function (data) {
                if (is4x6) {
                    const packages = Array.isArray(data.packages) ? data.packages : [];
                    totalLabels += packages.length || 1;
                }
            });

            let labelCount = 0;
            selectedData.forEach(function (data) {
                if (is4x6) {
                    // Every box of every shipment gets its own 4x6 label marked
                    // "BOX X OF N". Page breaks are added between labels so no
                    // trailing blank page results.
                    const packages = Array.isArray(data.packages) ? data.packages : [];
                    const boxCount = packages.length || 1;
                    for (let b = 1; b <= boxCount; b++) {
                        html += buildCourierLabelHtml(data, b, boxCount);
                        labelCount++;
                        if (labelCount < totalLabels) {
                            html += '<div style="page-break-after:always;"></div>';
                        }
                    }
                } else {
                    html += buildBulkLabelHtml(data);
                }
            });

            html += '</body></html>';
            printWindow.document.write(html);
            printWindow.document.close();

            setTimeout(function () {
                printWindow.focus();
                printWindow.print();
                printWindow.onafterprint = function () {
                    printWindow.close();
                };
            }, 300);
        }

        // Print a 4x6 courier label for a single shipment (globally accessible).
        // Used directly by the "Print Label" option and by printLabel() when the
        // 4x6 size is selected.
        function printCourierLabel4x6(invoiceId) {
            const data = shipmentData[invoiceId];
            if (!data) return;

            const packages = Array.isArray(data.packages) ? data.packages : [];
            const boxCount = packages.length || 1;

            const printWindow = window.open('', '_blank', 'width=800,height=700');
            if (!printWindow) {
                alert('Popup blocked. Allow popups to print the label.');
                return;
            }

            printWindow.document.write('<!DOCTYPE html><html><head><title>Courier Label</title>');
            printWindow.document.write('<style>' + courierLabelStyles + '@page{size:4in 6in;margin:0;}</style>');
            printWindow.document.write('</head><body>');

            // One 4x6 label per box, each marked "BOX X OF N". The page break is
            // added BETWEEN labels so no trailing blank page results.
            for (let i = 1; i <= boxCount; i++) {
                printWindow.document.write(buildCourierLabelHtml(data, i, boxCount));
                if (i < boxCount) {
                    printWindow.document.write('<div style="page-break-after:always;"></div>');
                }
            }

            printWindow.document.write('</body></html>');
            printWindow.document.close();

            printWindow.onload = function() {
                printWindow.print();
                printWindow.onafterprint = function() {
                    printWindow.close();
                };
            };
            if (printWindow.document.readyState === 'complete') {
                printWindow.print();
                printWindow.onafterprint = function() {
                    printWindow.close();
                };
            }
        }

        // Print Label function (outside document.ready so it's globally accessible)
        function printLabel() {
            const sizeSelect = document.getElementById('printLabelSize');
            const labelSize = sizeSelect ? sizeSelect.value : 'a4';
            const is4x6 = labelSize === '4x6';

            // For the 4x6 size the courier label format is used, built from the
            // shipment the user opened via openPrintLabel().
            if (is4x6 && window.currentPrintInvoiceId && shipmentData[window.currentPrintInvoiceId]) {
                // ---- 4x6 Courier label format ----
                printCourierLabel4x6(window.currentPrintInvoiceId);
                return;
            }

            const printWindow = window.open('', '_blank', 'width=800,height=700');
            if (!printWindow) {
                alert('Popup blocked. Allow popups to print the label.');
                return;
            }

            // ---- A4 (default) label format ----
            const modalBody = document.getElementById('printLabelBody');
            const content = modalBody.cloneNode(true);
            printWindow.document.write('<!DOCTYPE html><html><head><title>Print Label</title>');
            printWindow.document.write('<style>');
            // @page margin must stay 0 so the browser does not print its own
            // header/footer (date/time). Spacing is handled via body padding.
            printWindow.document.write('body{font-family:Arial,sans-serif;padding:8mm;color:#000;font-size:12px;}');
            printWindow.document.write('@page{size:A4;margin:0;}');
            printWindow.document.write('table{border-collapse:collapse;width:100%;margin-bottom:8px;}');
            printWindow.document.write('table th,table td{border:1px solid #333;padding:3px 5px;text-align:left;}');
            printWindow.document.write('table th{background:#eee;font-weight:bold;}');
            printWindow.document.write('.text-center{text-align:center;}');
            printWindow.document.write('.text-end{text-align:right;}');
            printWindow.document.write('.fw-bold{font-weight:bold;}');
            printWindow.document.write('.row{display:flex;gap:15px;margin-bottom:10px;}');
            printWindow.document.write('.col-6{flex:1;border:1px solid #333;padding:8px;}');
            printWindow.document.write('hr{border:none;border-top:1px dashed #ccc;margin:8px 0;}');
            printWindow.document.write('svg{max-width:100%;height:auto;}');
            printWindow.document.write('@media print{body{margin:0;padding:8mm;}}');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write(content.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();

            printWindow.onload = function() {
                printWindow.print();
                printWindow.onafterprint = function() {
                    printWindow.close();
                };
            };

            if (printWindow.document.readyState === 'complete') {
                printWindow.print();
                printWindow.onafterprint = function() {
                    printWindow.close();
                };
            }
        }
    </script>

</body>

</html>