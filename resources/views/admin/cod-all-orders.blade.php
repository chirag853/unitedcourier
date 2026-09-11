<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - All COD Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View all Cash on Delivery (COD) orders for United Courier">
    <meta name="keywords" content="COD, cash on delivery, orders, courier, logistics">
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
        .shipments-table .status-col {
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
                    <h4 class="mb-1"><i class="ti ti-cash me-1"></i>All COD Orders</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Manage COD</a></li>
                            <li class="breadcrumb-item active" aria-current="page">All Order</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.cod.create-order') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Create Order
                    </a>
                </div>
            </div> -->

            <!-- Count Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-md">
                    <a href="{{ route('admin.cod.all-orders', ['type' => 'all']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-primary bg-opacity-10 text-primary"><i class="ti ti-package"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['all'] ?? 0 }}</div>
                            <div class="cod-count-label">Total Orders</div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md">
                    <a href="{{ route('admin.cod.all-orders', ['type' => 'cod']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-success bg-opacity-10 text-success"><i class="ti ti-currency-rupee"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['cod'] ?? 0 }}</div>
                            <div class="cod-count-label">COD</div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md">
                    <a href="{{ route('admin.cod.all-orders', ['type' => 'foc']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-dark bg-opacity-10 text-dark"><i class="ti ti-gift"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['foc'] ?? 0 }}</div>
                            <div class="cod-count-label">FOC</div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md">
                    <a href="{{ route('admin.cod.all-orders', ['type' => 'delivered']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-info bg-opacity-10 text-info"><i class="ti ti-truck"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['delivered'] ?? 0 }}</div>
                            <div class="cod-count-label">Delivered</div>
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
                            <p class="mt-3 text-muted">No COD orders matched the selected filter.</p>
                            <a href="{{ route('admin.cod.all-orders') }}" class="btn btn-primary">Clear Filters</a>
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
                                            'delivered' => 'badge bg-success',
                                        ];
                                        $statusLabelMap = [
                                            'draft' => 'Draft',
                                            'manifested' => 'Manifested',
                                            'cod' => 'COD Collected',
                                            'cancelled' => 'Cancelled',
                                            'delivered' => 'Delivered',
                                        ];
                                    @endphp
                                    <tr>
                                        <td class="sticky-col col-2">
                                            @if($shipper?->awb_number)
                                                @if($manifestNumber)
                                                    <a href="{{ route('admin.manifest-detail', ['manifestNumber' => $manifestNumber]) }}"
                                                       target="_blank"
                                                       class="badge bg-dark text-decoration-none"
                                                       title="Open manifest details in new tab"
                                                       style="white-space:nowrap;">
                                                        {{ $shipper->awb_number }}
                                                        <i class="ti ti-external-link ms-1" style="font-size:11px;"></i>
                                                    </a>
                                                @else
                                                    <span class="badge bg-dark">{{ $shipper->awb_number }}</span>
                                                @endif
                                            @else
                                                <strong>{{ $invoice->invoice_number }}</strong>
                                            @endif
                                            @php
                                                $hawbConsignee = $shipper?->consigneeInfo;
                                            @endphp
                                            <div class="hawb-sub-info">
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
                                                <div class="hawb-sub-row">
                                                    <span class="hawb-sub-label">Customer:</span>
                                                    <span class="hawb-sub-value">{{ $exporterName ?: '-' }}</span>
                                                </div>
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
                                                @if($manifestNumber)
                                                    <a href="{{ route('admin.manifest-detail', ['manifestNumber' => $manifestNumber]) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center"
                                                       title="View Details"
                                                       aria-label="View Details"
                                                       style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                        <i class="ti ti-eye" aria-hidden="true"></i>
                                                    </a>
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
                                                        <i class="ti ti-x" aria-hidden="true"></i>
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center"
                                                            title="View Details"
                                                            aria-label="View Details"
                                                            disabled
                                                            style="width:32px;height:32px;padding:0;border-radius:4px;">
                                                        <i class="ti ti-eye" aria-hidden="true"></i>
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
                                                        <i class="ti ti-x" aria-hidden="true"></i>
                                                    </button>
                                                @endif
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
                                <nav aria-label="COD order pages">
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
                    <div class="d-flex gap-4 mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="closeOrderType" id="closeOrderTypeCod" value="cod" checked>
                            <label class="form-check-label" for="closeOrderTypeCod">COD</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="closeOrderType" id="closeOrderTypeFoc" value="foc">
                            <label class="form-check-label" for="closeOrderTypeFoc">FOC</label>
                        </div>
                    </div>
                    <div class="text-danger small mt-2 d-none" id="closeRemarkError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="closeRemarkSaveBtn">Save</button>
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

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            var closeRemarkText = document.getElementById('closeRemarkText');
            var closeRemarkAwbNumber = document.getElementById('closeRemarkAwbNumber');
            var closeRemarkError = document.getElementById('closeRemarkError');
            var closeRemarkSaveBtn = document.getElementById('closeRemarkSaveBtn');
            var closeOrderTypeCod = document.getElementById('closeOrderTypeCod');
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
                    if (closeOrderTypeCod) {
                        closeOrderTypeCod.checked = true;
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
                    var orderType = document.querySelector('input[name="closeOrderType"]:checked');
                    orderType = orderType ? orderType.value : 'cod';

                    if (!currentInvoiceId) {
                        return;
                    }

                    closeRemarkSaveBtn.disabled = true;
                    closeRemarkSaveBtn.textContent = 'Saving...';

                    fetch("{{ route('admin.cod.close-order') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            invoice_id: currentInvoiceId,
                            shipper_id: currentShipperId,
                            order_type: orderType,
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
