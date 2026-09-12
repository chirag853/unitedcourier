<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - All Prepaid Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View all Prepaid orders for United Courier">
    <meta name="keywords" content="prepaid, orders, courier, logistics">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
        .cod-count-card {
            border-radius: 10px;
            border: 1px solid #e9ecef;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow 0.15s ease;
            text-decoration: none;
            color: inherit;
        }
        .cod-count-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            color: inherit;
        }
        .cod-count-icon {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 22px;
        }
        .cod-count-num {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.1;
        }
        .cod-count-label {
            font-size: 13px;
            color: #6c757d;
        }
        /* ===== Customer draft-table look (sticky cols, sub-info stacks) ===== */
        .shipments-table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
        }
        .shipments-table-scroll::-webkit-scrollbar { height: 8px; }
        .shipments-table-scroll::-webkit-scrollbar-thumb { background: #c9d9ff; border-radius: 6px; }
        .shipments-table-scroll::-webkit-scrollbar-track { background: #f1f4fb; }
        .shipments-table {
            min-width: 1150px;
            white-space: nowrap;
        }
        .shipments-table thead th,
        .shipments-table tbody td {
            vertical-align: middle;
        }
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
        .shipments-table .sticky-col.col-2 { left: 0; min-width: 220px; }
        .shipments-table .sticky-col.col-3 { left: 220px; width: 130px; min-width: 130px; max-width: 130px; }
        .shipments-table .status-col , .tracking-col {
            width: 130px;
            min-width: 120px;
            max-width: 140px;
            text-align: center;
        }
        .shipments-table .remark-col {
            width: 200px;
            min-width: 180px;
            max-width: 240px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
            font-size: 12px;
            vertical-align: top;
        }
        .shipments-table .action-col {
            width: 120px;
            min-width: 110px;
            max-width: 140px;
            text-align: center;
        }
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
            font-size: medium;
        }
        .receiver-details-stack .receiver-line {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            color: #1f2937;
            line-height: 1.35;
            font-weight: 600;
        }
        .receiver-details-stack .receiver-value {
            flex: 1;
            min-width: 0;
        }
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
        @media (max-width: 575.98px) {
            .shipments-table {
                min-width: 1080px;
            }
        }
        /* ===== Shipment Detail Modal (same look as manifest-detail page) ===== */
        .md-modal .modal-content {
            border-radius: 18px;
            border: none;
            overflow: hidden;
        }
        .md-modal .modal-header {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: #fff;
            padding: 16px 20px;
        }
        .md-modal .modal-header .modal-title {
            font-weight: 700;
            font-size: 16px;
        }
        .md-modal .modal-header .btn-close {
            filter: invert(1);
            opacity: .8;
        }
        #mdShipmentDetailModal .modal-body {
            padding: 20px;
            max-height: 68vh;
            overflow-y: auto;
            background: #f8fafc;
        }
        .md-detail-section {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 12px;
        }
        .md-detail-section h6 {
            color: #4f46e5;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .md-detail-row-total {
            background: #eef2ff;
            border-radius: 10px;
            padding: 10px 12px;
            margin: 6px 0;
            border-left: 3px solid #4f46e5;
        }
        .md-detail-row-total .label {
            font-weight: 600;
            color: #4f46e5;
        }
        .md-detail-row-total .value {
            font-weight: 700;
            color: #4f46e5;
        }
        .md-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 4px 0;
            font-size: 13px;
            border-bottom: 1px dashed #eef2f7;
        }
        .md-detail-row:last-child {
            border-bottom: none;
        }
        .md-detail-row .label {
            color: #64748b;
            min-width: 130px;
            flex-shrink: 0;
            font-weight: 500;
        }
        .md-detail-row .value {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
            flex: 1;
            word-break: break-word;
        }
        .md-tracking-box {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #fff;
            padding: 14px 18px;
            border-radius: 14px;
            text-align: center;
            margin-bottom: 14px;
            box-shadow: 0 6px 18px rgba(79, 70, 229, .3);
        }
        .md-tracking-box .tracking-label {
            font-size: 12px;
            opacity: .9;
            margin-bottom: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .md-tracking-box .tracking-value {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .md-route-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }
        .md-route-box .route-point {
            text-align: center;
            flex: 1;
        }
        .md-route-box .route-point .route-city {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }
        .md-route-box .route-point .route-label {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
            letter-spacing: .5px;
        }
        .md-route-box .route-arrow {
            font-size: 20px;
            color: #4f46e5;
            flex-shrink: 0;
        }
        #mdShipmentDetailModal .modal-footer {
            background: #fff;
            border-top: 1px solid #eef2f7;
            padding: 12px 20px;
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    @include('admin.partials.header')
    <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
        <div class="offcanvas-body">
            <div class="card shadow-none mb-0">
                <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                    <i class="ti ti-search fs-22"></i>
                    <input type="search" class="form-control border-0" placeholder="Search">
                    <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
                </div>
            </div>
        </div>
    </div>
    @include('admin.partials.sidebar')
    <div class="page-wrapper">
        <div class="content">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1"><i class="ti ti-wallet me-1"></i>All Prepaid Orders</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Manage Prepaid</a></li>
                            <li class="breadcrumb-item active" aria-current="page">All Prepaid Orders</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.prepaid.create-order') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Create Prepaid Order
                    </a>
                </div>
            </div> -->

            <!-- Count Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-md">
                    <a href="{{ route('admin.prepaid.all-orders', ['type' => 'all']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-primary bg-opacity-10 text-primary"><i class="ti ti-package"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['all'] ?? 0 }}</div>
                            <div class="cod-count-label">Total Orders</div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md">
                    <a href="{{ route('admin.prepaid.all-orders', ['type' => 'prepaid']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-success bg-opacity-10 text-success"><i class="ti ti-currency-rupee"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['prepaid'] ?? 0 }}</div>
                            <div class="cod-count-label">Prepaid</div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md">
                    <a href="{{ route('admin.prepaid.all-orders', ['type' => 'prepaid_close']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-info bg-opacity-10 text-info"><i class="ti ti-truck"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['prepaid_close'] ?? $counts['delivered'] ?? 0 }}</div>
                            <div class="cod-count-label">Prepaid Close</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Orders Table (customer draft-view style) -->
            <div class="card border shadow">
                <div class="card-body">
                    @if($invoices->isEmpty())
                        <div class="text-center py-5">
                            <i class="ti ti-package" style="font-size:48px;color:#ccc;"></i>
                            <p class="mt-3 text-muted">No prepaid orders matched the selected filter.</p>
                            <a href="{{ route('admin.prepaid.all-orders') }}" class="btn btn-primary">Clear Filters</a>
                        </div>
                    @else
                        <div class="table-responsive shipments-table-scroll">
                            <table class="table table-bordered table-hover shipments-table shipments-table-draft">
                                <thead class="table-light">
                                    <tr>
                                        <th class="sticky-col col-2">HAWB Number</th>
                                        <th class="sticky-col col-3">Order Date</th>
                                        <th class="receiver-details-col">Receiver Details</th>
                                        <th class="package-details-col">Package Details</th>
                                        <th class="status-col">Status</th>
                                        <th class="tracking-col">Tracking</th>
                                        <th class="remark-col">Remark</th>
                                        <th class="action-col text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                    @php
                                        $shipper = $invoice->shipperInfo;
                                        $rowStatus = ($invoice->status === 'cod') ? 'cod' : ($shipper?->status ?: 'draft');
                                        $manifestRow = $shipper?->manifest;
                                        $manifestNumber = $manifestRow?->manifest_number;
                                        $exporterName = ($shipper?->customer_id && isset($customerNames[$shipper->customer_id]))
                                            ? $customerNames[$shipper->customer_id]
                                            : null;
                                        $statusBadgeMap = [
                                            'draft' => 'badge bg-warning text-dark',
                                            'manifested' => 'badge bg-secondary',
                                            'cod' => 'badge bg-success',
                                            'cancelled' => 'badge bg-danger',
                                            'delivered' => 'badge bg-dark',
                                            'prepaid_close' => 'badge bg-dark',
                                            'cod_close' => 'badge bg-dark',
                                        ];
                                        $statusLabelMap = [
                                            'draft' => 'Draft',
                                            'manifested' => 'Manifested',
                                            'cod' => 'COD Collected',
                                            'cancelled' => 'Cancelled',
                                            'delivered' => 'Prepaid Close',
                                            'prepaid_close' => 'Prepaid Close',
                                            'cod_close' => 'COD Closed',
                                        ];
                                    @endphp
                                    <tr>
                                        <td class="sticky-col col-2">
                                            @if($shipper?->awb_number)
                                                @if($manifestNumber)
                                                    <!-- <a href="{{ route('admin.manifest-detail', ['manifestNumber' => $manifestNumber]) }}"
                                                       target="_blank"
                                                       class="badge bg-dark text-decoration-none"
                                                       title="Open manifest details in new tab"
                                                       style="white-space:nowrap;">
                                                        -->
                                                       <div
                                                       target="_blank"
                                                       class="badge bg-dark text-decoration-none"
                                                       title="Open manifest details in new tab"
                                                       style="white-space:nowrap;">
                                                        {{ $shipper->awb_number }}
                                                        <i class="ti ti-external-link ms-1" style="font-size:11px;"></i>
                                                        </div>
                                                @else
                                                    <span class="badge bg-dark">{{ $shipper->awb_number }}</span>
                                                @endif
                                            @else
                                                <strong>{{ $invoice->invoice_number }}</strong>
                                            @endif
                                            @php
                                                $hawbConsignee = $shipper?->consigneeInfo;
                                                $hawbTracking = $shipper?->shipmentTracking;
                                                $hawbPkgResults = $hawbTracking?->package_results;
                                                $hawbFirstPkg = is_array($hawbPkgResults) && isset($hawbPkgResults[0]) ? $hawbPkgResults[0] : $hawbPkgResults;
                                                $hawbTrackingNumber = (is_array($hawbFirstPkg) ? ($hawbFirstPkg['TrackingNumber'] ?? null) : null)
                                                    ?: $hawbTracking?->shipment_identification_number;
                                            @endphp
                                            <div class="hawb-sub-info">
                                                <!-- <div class="hawb-sub-row">
                                                    <span class="hawb-sub-label">Tracking No.:</span>
                                                    <span class="hawb-sub-value">{{ $hawbTrackingNumber ?: '-' }}</span>
                                                </div> -->
                                                <div class="hawb-sub-row">
                                                    <span class="hawb-sub-label">Destination:</span>
                                                    <span class="hawb-sub-value">{{ $hawbConsignee?->delivery_destination ?: '-' }}{{ $hawbConsignee?->zip_code ? ' · '.$hawbConsignee->zip_code : '' }}</span>
                                                </div>
                                                <div class="hawb-sub-row">
                                                    <span class="hawb-sub-label">Reference number:</span>
                                                    <span class="hawb-sub-value">{{ $invoice->reference_number ?: '-' }}</span>
                                                </div>
                                                <div class="hawb-sub-row">
                                                    <span class="hawb-sub-label">Invoice number:</span>
                                                    <span class="hawb-sub-value">{{ $invoice->invoice_number ?: '-' }}</span>
                                                </div>
                                                <!-- <div class="hawb-sub-row">
                                                    <span class="hawb-sub-label">Customer:</span>
                                                    <span class="hawb-sub-value">{{ $exporterName ?: '-' }}</span>
                                                </div> -->
                                            </div>
                                        </td>
                                        <td class="sticky-col col-3">
                                            @php
                                                $orderDateSource = $rowStatus === 'draft'
                                                    ? $invoice->created_at
                                                    : ($shipper?->updated_at ?: $invoice->updated_at);
                                            @endphp
                                            @if($orderDateSource)
                                                <div>{{ date('d M Y', strtotime($orderDateSource)) }}</div>
                                                <div class="text-muted">{{ date('h:i A', strtotime($orderDateSource)) }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="receiver-details-col">
                                            @php
                                                $receiverConsignee = $shipper?->consigneeInfo;
                                                $receiverDisplayName = $receiverConsignee?->consignee_name ?: ($receiverConsignee?->contact_person ?: '-');
                                            @endphp
                                            <div class="receiver-details-stack">
                                                <div class="receiver-name">
                                                    <span class="receiver-value"></span> {{ $receiverDisplayName }}
                                                </div>
                                                @if($receiverConsignee?->email)
                                                    <div class="receiver-line">
                                                        <span class="receiver-value">{{ $receiverConsignee->email }}</span>
                                                    </div>
                                                @endif
                                                @if($receiverConsignee?->phone_number)
                                                    <div class="receiver-line">
                                                        <span class="receiver-value">{{ $receiverConsignee->phone_number }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="package-details-col">
                                            @php
                                                $packages = $shipper?->packageDimensions ?? collect();
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
                                        <td class="status-col">
                                            <span class="shipment-status-badge {{ $statusBadgeMap[$rowStatus] ?? 'badge bg-warning text-dark' }}">{{ $statusLabelMap[$rowStatus] ?? ucfirst($rowStatus) }}</span>
                                        </td>
                                        <td class="tracking-col" style="white-space: initial;">
                                            <div class="hawb-sub-row">
                                                    <!-- <span class="hawb-sub-label">Tracking No.:</span> -->
                                                    <span class="hawb-sub-value">{{ $hawbTrackingNumber ?: 'Self' }}</span>
                                                </div>
                                        </td>
                                        <td class="remark-col">
                                            @php
                                                $entryRemark = trim((string) ($shipper?->shipmentRemark?->entry_remark ?? ''));
                                                $financeRemark = trim((string) ($shipper?->shipmentRemark?->finance_remark ?? ''));
                                            @endphp
                                            @if($entryRemark)
                                                <div>{{ $entryRemark }}</div>
                                            @endif
                                            @if($financeRemark)
                                                <div class="text-muted mt-1"><strong>Finance:</strong> {{ $financeRemark }}</div>
                                            @endif
                                            @if(! $entryRemark && ! $financeRemark)
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="action-col text-center">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                     <button type="button"
                                                        class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center"
                                                        title="View Details"
                                                        aria-label="View Details"
                                                        onclick="openPrepaidShipmentModal({{ $invoice->shipper_id }}, this)"
                                                        style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                        <i class="ti ti-eye" aria-hidden="true"></i>
                                                     </button>
                                                     <button type="button"
                                                             class="btn btn-sm btn-outline-info d-inline-flex align-items-center justify-content-center"
                                                             title="Print Label"
                                                            aria-label="Print Label"
                                                            onclick="prepaidDirectPrint({{ $invoice->id }}, {{ $invoice->shipper_id }}, {{ strtoupper($shipper?->shipping_method ?? '') === 'SELF' ? 'true' : 'false' }})"
                                                            style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                        <i class="ti ti-printer" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center btn-open-close-modal"
                                                            title="Close"
                                                            aria-label="Close"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#closeRemarkModal"
                                                            data-invoice-id="{{ $invoice->id }}"
                                                            data-shipper-id="{{ $invoice->shipper_id }}"
                                                            data-awb-number="{{ $shipper?->awb_number ?: $invoice->invoice_number }}"
                                                            style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                        <i class="ti ti-lock" aria-hidden="true"></i>
                                                    </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 mt-3">
                            <div class="pagination-summary">
                                Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} orders
                            </div>
                            @if ($invoices->hasPages())
                                <nav aria-label="Prepaid order pages">
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
        </div>
    </div>
</div>

    <!-- Close Remark Modal -->
    <div class="modal fade" id="closeRemarkModal" tabindex="-1" aria-labelledby="closeRemarkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="closeRemarkModalLabel">Close Order - <span id="closeRemarkAwbNumber">-</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="closeRemarkText" class="form-label">Remark</label>
                    <textarea class="form-control" id="closeRemarkText" rows="4" placeholder="Enter remark..."></textarea>
                    <div class="text-danger small mt-2 d-none" id="closeRemarkError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="closeRemarkSaveBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Label Modal (label from shipment_tracking via admin.generate-label) -->
    <div class="modal fade" id="printLabelModal" tabindex="-1" aria-labelledby="printLabelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printLabelModalLabel">
                        <i class="ti ti-printer me-1"></i> Print Shipping Label
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="printLabelContent">
                    <div id="printLabelLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Generating label PDF...</p>
                    </div>
                    <div id="printLabelError" class="text-center py-4 d-none">
                        <i class="ti ti-alert-circle fs-24 text-danger d-block mb-2"></i>
                        <p class="text-danger" id="printLabelErrorMsg">Failed to generate label.</p>
                    </div>
                    <iframe id="printLabelPdfFrame" style="width:100%;height:500px;border:none;display:none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary d-none" id="printLabelPrintBtn" onclick="triggerPrepaidPdfPrint()">
                        <i class="ti ti-printer me-1"></i> Print Label
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipment Detail Modal (same layout as manifest-detail page) -->
    <div class="modal fade md-modal" id="mdShipmentDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="ti ti-package me-2"></i>Shipment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tracking Number -->
                    <div class="md-tracking-box" id="mdDetailTrackingBox" style="display:none;">
                        <div class="tracking-label">Tracking Number</div>
                        <div class="tracking-value" id="mdDetailTrackingNumber">-</div>
                    </div>

                    <!-- Ship From → Ship To Route -->
                    <div class="md-route-box" id="mdDetailRouteBox">
                        <div class="route-point">
                            <div class="route-label">SHIP FROM</div>
                            <div class="route-city" id="mdDetailShipFrom">-</div>
                        </div>
                        <div class="route-arrow">
                            <i class="ti ti-arrow-right"></i>
                        </div>
                        <div class="route-point">
                            <div class="route-label">SHIP TO</div>
                            <div class="route-city" id="mdDetailShipTo">-</div>
                        </div>
                    </div>

                    <!-- AWB & Invoice Info -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-clipboard me-1"></i> AWB & Invoice Info</h6>
                        <div class="md-detail-row">
                            <span class="label">HAWB Number</span>
                            <span class="value" id="mdDetailAwbNumber">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Invoice Number</span>
                            <span class="value" id="mdDetailInvoiceNumber">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Invoice Date</span>
                            <span class="value" id="mdDetailInvoiceDate">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Invoice Amount</span>
                            <span class="value" id="mdDetailInvoiceAmount">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Currency</span>
                            <span class="value" id="mdDetailInvoiceCurrency">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Incoterms</span>
                            <span class="value" id="mdDetailIncoterms">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Reference No.</span>
                            <span class="value" id="mdDetailReferenceNumber">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Manifest No.</span>
                            <span class="value" id="mdDetailManifestNumber">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Order Date</span>
                            <span class="value" id="mdDetailOrderDate">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Status</span>
                            <span class="value" id="mdDetailStatus">-</span>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-building me-1"></i> Customer Info</h6>
                        <div class="md-detail-row">
                            <span class="label">Name</span>
                            <span class="value" id="mdDetailCustomerName">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Phone</span>
                            <span class="value" id="mdDetailCustomerPhone">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Email</span>
                            <span class="value" id="mdDetailCustomerEmail">-</span>
                        </div>
                    </div>

                    <!-- Shipper Info -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-user me-1"></i> Shipper Info</h6>
                        <div class="md-detail-row">
                            <span class="label">Company</span>
                            <span class="value" id="mdDetailShipperCompany">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Contact Person</span>
                            <span class="value" id="mdDetailShipperContact">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Phone</span>
                            <span class="value" id="mdDetailShipperPhone">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Email</span>
                            <span class="value" id="mdDetailShipperEmail">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Address</span>
                            <span class="value" id="mdDetailShipperAddress">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">City / State / Pincode</span>
                            <span class="value" id="mdDetailShipperCityStatePin">-</span>
                        </div>
                    </div>

                    <!-- Consignee Info -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-user-check me-1"></i> Consignee Info</h6>
                        <div class="md-detail-row">
                            <span class="label">Name</span>
                            <span class="value" id="mdDetailConsigneeName">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Contact Person</span>
                            <span class="value" id="mdDetailConsigneeContact">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Phone</span>
                            <span class="value" id="mdDetailConsigneePhone">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Email</span>
                            <span class="value" id="mdDetailConsigneeEmail">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Address</span>
                            <span class="value" id="mdDetailConsigneeAddress">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">City / State / Zip</span>
                            <span class="value" id="mdDetailConsigneeCityStateZip">-</span>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="md-detail-section" id="mdDetailItemsSection">
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
                                <tbody id="mdDetailItemsTable"></tbody>
                            </table>
                        </div>
                        <!-- <div class="text-end mt-2">
                            <strong>Totals: <span id="mdDetailItemsTotal">0.00</span></strong>
                        </div> -->
                    </div>

                    <!-- Shipment Details -->
                    <div class="md-detail-section">
                        <h6><i class="ti ti-truck me-1"></i> Shipment Details</h6>
                        <div class="md-detail-row">
                            <span class="label">Destination</span>
                            <span class="value" id="mdDetailDestination">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Origin Type</span>
                            <span class="value" id="mdDetailOriginType">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Shipping Method</span>
                            <span class="value" id="mdDetailShippingMethod">-</span>
                        </div>
                    </div>

                    <!-- Package Dimensions -->
                    <div class="md-detail-section" id="mdDetailPackagesSection">
                        <h6><i class="ti ti-box me-1"></i> Package Dimensions</h6>
                        <div id="mdDetailPackagesContainer"></div>
                    </div>

                    <!-- Service -->
                    <div class="md-detail-section" id="mdDetailServiceSection">
                        <h6><i class="ti ti-truck-delivery me-1"></i> Service</h6>
                        <div class="md-detail-row">
                            <span class="label">Method</span>
                            <span class="value" id="mdDetailServiceMethod">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Network</span>
                            <span class="value" id="mdDetailServiceNetwork">-</span>
                        </div>
                        <div class="md-detail-row">
                            <span class="label">Delivery Days</span>
                            <span class="value" id="mdDetailServiceTat">-</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- JsBarcode (4x6 custom courier label, same as customer packed tab) -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        /**
         * Open the Shipment Details modal (same layout as the manifest-detail
         * page) for one prepaid shipment. Detail is fetched via AJAX, then the
         * modal is filled and shown.
         * @param {number} shipperId
         * @param {HTMLElement} btn - clicked View button (loading state)
         */
        function openPrepaidShipmentModal(shipperId, btn) {
            if (!shipperId) {
                return;
            }

            const $btn = btn ? $(btn) : $();
            const btnOriginal = $btn.length ? $btn.html() : '';
            if ($btn.length) {
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            }
            const restoreBtn = function() {
                if ($btn.length) {
                    $btn.prop('disabled', false).html(btnOriginal);
                }
            };

            const urlTemplate = '{{ route("admin.prepaid.shipment-detail", ["shipperId" => "__S__"]) }}';
            $.ajax({
                url: urlTemplate.replace('__S__', encodeURIComponent(shipperId)),
                type: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    restoreBtn();
                    const data = response.data;
                    if (!response.success || !data) {
                        alert(response.message || 'Shipment details not found.');
                        return;
                    }

                    // Tracking Number
                    var trackingBox = document.getElementById('mdDetailTrackingBox');
                    var trackingNum = document.getElementById('mdDetailTrackingNumber');
                    if (data.tracking_number) {
                        trackingBox.style.display = 'block';
                        trackingNum.textContent = data.tracking_number;
                    } else {
                        trackingBox.style.display = 'none';
                    }

                    // Ship From → Ship To
                    document.getElementById('mdDetailShipFrom').textContent = data.ship_from || '-';
                    document.getElementById('mdDetailShipTo').textContent = data.ship_to || '-';

                    // AWB & Invoice Info
                    document.getElementById('mdDetailAwbNumber').textContent = data.awb_number || '-';
                    document.getElementById('mdDetailInvoiceNumber').textContent = data.invoice_number || '-';
                    document.getElementById('mdDetailInvoiceDate').textContent = data.invoice_date || '-';
                    document.getElementById('mdDetailInvoiceAmount').textContent = data.invoice_amount || '-';
                    document.getElementById('mdDetailInvoiceCurrency').textContent = data.invoice_currency || '-';
                    document.getElementById('mdDetailIncoterms').textContent = data.incoterms || '-';
                    document.getElementById('mdDetailReferenceNumber').textContent = data.reference_number || '-';
                    document.getElementById('mdDetailManifestNumber').textContent = data.manifest_number || '-';
                    document.getElementById('mdDetailOrderDate').textContent = data.order_date || '-';
                    document.getElementById('mdDetailStatus').textContent = data.status || '-';

                    // Customer Info
                    if (data.customer) {
                        document.getElementById('mdDetailCustomerName').textContent = data.customer.name || '-';
                        document.getElementById('mdDetailCustomerPhone').textContent = data.customer.phone || '-';
                        document.getElementById('mdDetailCustomerEmail').textContent = data.customer.email || '-';
                    } else {
                        document.getElementById('mdDetailCustomerName').textContent = '-';
                        document.getElementById('mdDetailCustomerPhone').textContent = '-';
                        document.getElementById('mdDetailCustomerEmail').textContent = '-';
                    }

                    // Shipper Info
                    if (data.shipper) {
                        document.getElementById('mdDetailShipperCompany').textContent = data.shipper.company || '-';
                        document.getElementById('mdDetailShipperContact').textContent = data.shipper.contact || '-';
                        document.getElementById('mdDetailShipperPhone').textContent = data.shipper.phone || '-';
                        document.getElementById('mdDetailShipperEmail').textContent = data.shipper.email || '-';
                        document.getElementById('mdDetailShipperAddress').textContent = data.shipper.address || '-';
                        document.getElementById('mdDetailShipperCityStatePin').textContent = data.shipper.city_state_pin || '-';
                    } else {
                        document.getElementById('mdDetailShipperCompany').textContent = '-';
                        document.getElementById('mdDetailShipperContact').textContent = '-';
                        document.getElementById('mdDetailShipperPhone').textContent = '-';
                        document.getElementById('mdDetailShipperEmail').textContent = '-';
                        document.getElementById('mdDetailShipperAddress').textContent = '-';
                        document.getElementById('mdDetailShipperCityStatePin').textContent = '-';
                    }

                    // Consignee Info
                    if (data.consignee) {
                        document.getElementById('mdDetailConsigneeName').textContent = data.consignee.name || '-';
                        document.getElementById('mdDetailConsigneeContact').textContent = data.consignee.contact || '-';
                        document.getElementById('mdDetailConsigneePhone').textContent = data.consignee.phone || '-';
                        document.getElementById('mdDetailConsigneeEmail').textContent = data.consignee.email || '-';
                        document.getElementById('mdDetailConsigneeAddress').textContent = data.consignee.address || '-';
                        document.getElementById('mdDetailConsigneeCityStateZip').textContent = data.consignee.city_state_zip || '-';
                    } else {
                        document.getElementById('mdDetailConsigneeName').textContent = '-';
                        document.getElementById('mdDetailConsigneeContact').textContent = '-';
                        document.getElementById('mdDetailConsigneePhone').textContent = '-';
                        document.getElementById('mdDetailConsigneeEmail').textContent = '-';
                        document.getElementById('mdDetailConsigneeAddress').textContent = '-';
                        document.getElementById('mdDetailConsigneeCityStateZip').textContent = '-';
                    }

                    // Shipment Details
                    document.getElementById('mdDetailDestination').textContent = data.destination || '-';
                    document.getElementById('mdDetailOriginType').textContent = data.origin_type || '-';
                    document.getElementById('mdDetailShippingMethod').textContent = data.shipping_method || '-';

                    // Package Dimensions
                    var packagesContainer = document.getElementById('mdDetailPackagesContainer');
                    var packagesSection = document.getElementById('mdDetailPackagesSection');
                    packagesContainer.innerHTML = '';
                    if (data.packages && data.packages.length > 0) {
                        packagesSection.style.display = 'block';
                        data.packages.forEach(function (pkg) {
                            var card = document.createElement('div');
                            card.className = 'package-card';
                            card.style.cssText = 'border:1px solid #e2e8f0;border-radius:10px;padding:12px;margin-bottom:8px;background:#fff;';
                            var header = document.createElement('div');
                            header.style.cssText = 'background:#f1f5f9;border-radius:8px 8px 0 0;padding:6px 10px;margin:-12px -12px 8px -12px;font-weight:600;color:#475569;';
                            header.innerHTML = '<span style="font-size:14px;"><i class="ti ti-box me-1"></i> Box #' + pkg.index + '</span>';
                            card.appendChild(header);
                            var row1 = document.createElement('div');
                            row1.className = 'row';
                            row1.innerHTML = '<div class="col-md-3"><strong>Weight:</strong> ' + (pkg.weight || '-') + ' Kg</div>' +
                                '<div class="col-md-3"><strong>Length:</strong> ' + (pkg.length || '-') + ' cm</div>' +
                                '<div class="col-md-3"><strong>Width:</strong> ' + (pkg.width || '-') + ' cm</div>' +
                                '<div class="col-md-3"><strong>Height:</strong> ' + (pkg.height || '-') + ' cm</div>';
                            card.appendChild(row1);
                            var row2 = document.createElement('div');
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
                    var itemsTable = document.getElementById('mdDetailItemsTable');
                    var itemsSection = document.getElementById('mdDetailItemsSection');
                    itemsTable.innerHTML = '';
                    if (data.items && data.items.length > 0) {
                        itemsSection.style.display = 'block';
                        data.items.forEach(function (item) {
                            var row = document.createElement('tr');
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
                        // document.getElementById('mdDetailItemsTotal').textContent = data.items_total;
                    } else {
                        itemsSection.style.display = 'none';
                    }

                    // Service (which courier service the shipment was created with)
                    var serviceSection = document.getElementById('mdDetailServiceSection');
                    if (data.service) {
                        serviceSection.style.display = 'block';
                        document.getElementById('mdDetailServiceMethod').textContent = data.service.method || '-';
                        document.getElementById('mdDetailServiceNetwork').textContent = data.service.network || '-';
                        document.getElementById('mdDetailServiceTat').textContent = data.service.tat || '-';
                    } else {
                        serviceSection.style.display = 'none';
                    }

                    // Show modal
                    var modalEl = document.getElementById('mdShipmentDetailModal');
                    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                },
                error: function(xhr) {
                    restoreBtn();
                    let errorMsg = 'Failed to load shipment details. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                }
            });
        }

        // ===== 4x6 custom courier label (same format as customer packed tab) =====
        const prepaidCourierLabelStyles =
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

        function prepaidGetDestinationCountryCode(destination) {
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

        function prepaidGetDestinationDs(destination) {
            if (!destination) return '-';
            const dest = String(destination);
            const m = dest.match(/^([A-Z]{2})[\s-]/);
            return m ? m[1].toUpperCase() : dest.slice(0, 4).toUpperCase();
        }

        function prepaidBuildCourierLabelHtml(data, boxIndex, boxCount) {
            const shipper = data.shipper || {};
            const consignee = data.consignee || {};
            const items = Array.isArray(data.items) ? data.items : [];
            const packages = Array.isArray(data.packages) ? data.packages : [];

            const packageCount = packages.length || 1;
            const hasBoxContext = typeof boxIndex === 'number' && typeof boxCount === 'number';
            const boxIdx = hasBoxContext ? boxIndex : 1;
            const boxTot = hasBoxContext ? boxCount : packageCount;

            let actualWeight = 0;
            if (hasBoxContext && packages[boxIdx - 1]) {
                actualWeight = parseFloat(packages[boxIdx - 1].weight) || 0;
            } else {
                packages.forEach(function (pkg) {
                    actualWeight += parseFloat(pkg.weight) || 0;
                });
            }

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
            const service = data.service || {};
            const network = service.api_provider || data.api_provider || (data.shipping_method || '-');
            const serviceCode = service.service_code || data.service_code || '-';
            const country = prepaidGetDestinationCountryCode(data.destination);
            const ds = prepaidGetDestinationDs(data.destination);

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
                        '<div class="info"><div class="info-title">SERVICE</div>' + serviceCode + '</div>' +
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

        /**
         * Print the 4x6 custom courier label (same as customer packed tab):
         * one label per box (BOX X OF N), each on its own page.
         * @param {number} shipperId
         * @param {HTMLElement} btn - clicked button (loading state)
         */
        function printPrepaidCustomLabel(shipperId, btn) {
            if (!shipperId) {
                return;
            }
            if (typeof JsBarcode === 'undefined') {
                alert('Barcode library failed to load. Please check your connection and try again.');
                return;
            }

            const $btn = btn ? $(btn) : $();
            const btnOriginal = $btn.length ? $btn.html() : '';
            if ($btn.length) {
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            }
            const restoreBtn = function() {
                if ($btn.length) {
                    $btn.prop('disabled', false).html(btnOriginal);
                }
            };

            const urlTemplate = '{{ route("admin.prepaid.shipment-detail", ["shipperId" => "__S__"]) }}';
            $.ajax({
                url: urlTemplate.replace('__S__', encodeURIComponent(shipperId)),
                type: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    restoreBtn();
                    const data = response.data;
                    if (!response.success || !data) {
                        alert(response.message || 'Label data not found.');
                        return;
                    }

                    const packages = Array.isArray(data.packages) ? data.packages : [];
                    const boxCount = packages.length || 1;
                    let html = '<!DOCTYPE html><html><head><title>Custom Label - ' + (data.awb_number || '') + '</title><style>' +
                        prepaidCourierLabelStyles + '@page{size:4in 6in;margin:0;}' +
                        '</style></head><body>';
                    for (let b = 1; b <= boxCount; b++) {
                        html += prepaidBuildCourierLabelHtml(data, b, boxCount);
                        if (b < boxCount) {
                            html += '<div style="page-break-after:always;"></div>';
                        }
                    }
                    html += '</body></html>';

                    const printWindow = window.open('', '_blank', 'width=900,height=700');
                    if (!printWindow) {
                        alert('Popup blocked. Allow popups to print labels.');
                        return;
                    }
                    printWindow.document.write(html);
                    printWindow.document.close();
                    setTimeout(function () {
                        printWindow.focus();
                        printWindow.print();
                        printWindow.onafterprint = function () {
                            printWindow.close();
                        };
                    }, 300);
                },
                error: function(xhr) {
                    restoreBtn();
                    let errorMsg = 'Failed to load label data. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                }
            });
        }

        /**
         * Direct print for one row: SELF shipments open the custom 4x6
         * courier label, everything else opens the carrier (UPS) label.
         * @param {number} invoiceId - shipment_invoice ID (carrier label)
         * @param {number} shipperId - shipper ID (custom label)
         * @param {boolean} isSelf - whether the shipment is SELF service
         */
        function prepaidDirectPrint(invoiceId, shipperId, isSelf) {
            if (isSelf) {
                printPrepaidCustomLabel(shipperId, null);
            } else {
                printPrepaidLabel(invoiceId);
            }
        }

        /**
         * Print shipping label - fetches base64 PDF from server (shipment_tracking
         * label via admin.generate-label) and displays it in the modal.
         * @param {number} shipmentId - The shipment_invoice ID
         */
        function printPrepaidLabel(shipmentId) {
            $('#printLabelLoading').removeClass('d-none');
            $('#printLabelError').addClass('d-none');
            $('#printLabelPdfFrame').css('display', 'none');
            $('#printLabelPrintBtn').addClass('d-none');

            $('#printLabelModal').modal('show');

            $.ajax({
                url: '{{ route("admin.generate-label") }}',
                type: 'POST',
                data: { shipment_id: shipmentId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.pdf_base64) {
                        const binaryString = atob(response.pdf_base64);
                        const bytes = new Uint8Array(binaryString.length);
                        for (let i = 0; i < binaryString.length; i++) {
                            bytes[i] = binaryString.charCodeAt(i);
                        }
                        const pdfBlob = new Blob([bytes], { type: 'application/pdf' });
                        const blobUrl = URL.createObjectURL(pdfBlob);

                        $('#printLabelLoading').addClass('d-none');
                        $('#printLabelPdfFrame').attr('src', blobUrl).css('display', 'block');
                        $('#printLabelPrintBtn').removeClass('d-none');

                        window._prepaidLabelPdfBlobUrl = blobUrl;
                    } else {
                        $('#printLabelLoading').addClass('d-none');
                        $('#printLabelError').removeClass('d-none');
                        $('#printLabelErrorMsg').text(response.message || 'Failed to generate label.');
                    }
                },
                error: function(xhr) {
                    $('#printLabelLoading').addClass('d-none');
                    $('#printLabelError').removeClass('d-none');
                    let errorMsg = 'Failed to generate label. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#printLabelErrorMsg').text(errorMsg);
                }
            });
        }

        /**
         * Trigger browser print for the PDF label.
         */
        function triggerPrepaidPdfPrint() {
            if (window._prepaidLabelPdfBlobUrl) {
                const printWindow = window.open(window._prepaidLabelPdfBlobUrl, '_blank');
                if (printWindow) {
                    printWindow.onload = function() {
                        setTimeout(function() {
                            printWindow.print();
                        }, 500);
                    };
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Revoke the blob URL to free memory when the modal closes.
            $('#printLabelModal').on('hidden.bs.modal', function() {
                if (window._prepaidLabelPdfBlobUrl) {
                    URL.revokeObjectURL(window._prepaidLabelPdfBlobUrl);
                    window._prepaidLabelPdfBlobUrl = null;
                }
                $('#printLabelLoading').addClass('d-none');
                $('#printLabelError').addClass('d-none');
                $('#printLabelPdfFrame').css('display', 'none').attr('src', '');
                $('#printLabelPrintBtn').addClass('d-none');
            });
        });
    </script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            var closeRemarkText = document.getElementById('closeRemarkText');
            var closeRemarkAwbNumber = document.getElementById('closeRemarkAwbNumber');
            var closeRemarkError = document.getElementById('closeRemarkError');
            var closeRemarkSaveBtn = document.getElementById('closeRemarkSaveBtn');
            var currentInvoiceId = null;
            var currentShipperId = null;

            document.querySelectorAll('.btn-open-close-modal').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var awbNumber = btn.getAttribute('data-awb-number') || '-';
                    currentInvoiceId = btn.getAttribute('data-invoice-id');
                    currentShipperId = btn.getAttribute('data-shipper-id');
                    if (closeRemarkAwbNumber) {
                        closeRemarkAwbNumber.textContent = awbNumber;
                    }
                    if (closeRemarkText) {
                        closeRemarkText.value = '';
                    }
                    if (closeRemarkError) {
                        closeRemarkError.classList.add('d-none');
                        closeRemarkError.textContent = '';
                    }
                });
            });

            if (closeRemarkSaveBtn) {
                closeRemarkSaveBtn.addEventListener('click', function () {
                    var remark = closeRemarkText ? closeRemarkText.value.trim() : '';

                    if (!currentInvoiceId) {
                        return;
                    }

                    closeRemarkSaveBtn.disabled = true;
                    closeRemarkSaveBtn.textContent = 'Saving...';

                    fetch("{{ route('admin.prepaid.close-order') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            invoice_id: currentInvoiceId,
                            shipper_id: currentShipperId,
                            remark: remark
                        })
                    })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Save failed');
                        }
                    })
                    .catch(function (err) {
                        if (closeRemarkError) {
                            closeRemarkError.textContent = err.message || 'Something went wrong. Please try again.';
                            closeRemarkError.classList.remove('d-none');
                        }
                    })
                    .finally(function () {
                        closeRemarkSaveBtn.disabled = false;
                        closeRemarkSaveBtn.textContent = 'Save';
                    });
                });
            }
        });
    </script>
</body>
</html>
