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
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/css/dataTables.bootstrap5.min.css') }}">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <!-- Tabler Icon CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}"> -->
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
                        <h4 class="mb-1">View All Shipments</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ url('/customer/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View All Shipments</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ url('/customer/create-shipment') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-plus me-1"></i> Add New Shipment
                        </a>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <div id="alertContainer"></div>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap gap-3">

                            <button class="btn btn-primary rounded-pill px-4 py-2 status-filter-btn" data-filter="all">
                                All Orders
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="draft">
                                Drafts
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="ready">
                                Ready
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="packed">
                                Packed
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="manifested">
                                Manifested
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="received">
                                Received
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="dispatched">
                                Dispatched
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="cancelled">
                                Cancelled
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="delivered">
                                Delivered
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="disputed">
                                Disputed
                            </button>

                            <button class="btn btn-light rounded-pill px-4 py-2 status-filter-btn" data-filter="on_hold">
                                On Hold
                            </button>

                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" id="statusFilterHeading">All Orders</h5>
                    <button class="btn btn-success rounded-pill px-4 py-2" id="bulkManifestBtn" style="display:none;">
                        <i class="ti ti-package-export me-1"></i> Bulk Manifest
                    </button>
                </div>
                <!-- Shipments Table Card -->
                <div class="card border shadow">
                    <div class="card-body">
                        @if($invoices->isEmpty())
                            <div class="text-center py-5">
                                <i class="ti ti-package" style="font-size:48px;color:#ccc;"></i>
                                <p class="mt-3 text-muted">No shipments found. Create your first shipment now!</p>
                                <a href="{{ url('/customer/create-shipment') }}" class="btn btn-primary">Create Shipment</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="shipmentsTable" class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th><input type="checkbox" id="selectAllCheckbox" style="display:none;"></th>
                                            <th>#</th>
                                            <th>AWB Number</th>
                                            <!-- <th>Ship From → Ship To</th> -->
                                            <th>Consignee Details</th>
                                            <!-- <th>Invoice Date</th> -->
                                            <th>Amount</th>
                                            <th>Currency</th>
                                            <th>Incoterms</th>
                                            <!-- <th>Reference No.</th> -->
                                            <th>Status</th>
                                            <th>Print Label</th>
                                            <th>Pay Now</th>
                                            <th>Manifest</th>
                                            <th>Created</th>
                                            <th class="text-center">Cancel</th>
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
                                        @endphp
                                        <tr id="invoice-row-{{ $invoice->id }}" data-status="{{ $rowStatus }}" data-shipper-id="{{ $invoice->shipperInfo ? $invoice->shipperInfo->id : '' }}">
                                            <td class="text-center">
                                                <input type="checkbox" class="shipment-checkbox bulk-manifest-checkbox" data-shipper-id="{{ $invoice->shipperInfo ? $invoice->shipperInfo->id : '' }}" style="display:none;">
                                            </td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($invoice->shipperInfo && $invoice->shipperInfo->awb_number)
                                                    <span class="badge bg-dark" style="cursor:pointer;"
                                                          data-invoice-id="{{ $invoice->id }}"
                                                          onclick="showShipmentDetail({{ $invoice->id }});">
                                                        {{ $invoice->shipperInfo->awb_number }}
                                                    </span>
                                                @else
                                                    <strong>{{ $invoice->invoice_number }}</strong>
                                                @endif
                                            </td>
                                            <td style="font-size:12px;">
                                                @php
                                                    $shipFrom = $invoice->shipperInfo ? trim(($invoice->shipperInfo->city ?? '') . ', ' . ($invoice->shipperInfo->state ?? '') . ', India') : 'India';
                                                    $shipTo = $invoice->shipperInfo && $invoice->shipperInfo->consigneeInfo
                                                        ? trim(($invoice->shipperInfo->consigneeInfo->city ?? '') . ', ' . ($invoice->shipperInfo->consigneeInfo->state ?? '') . ', ' . ($invoice->shipperInfo->delivery_destination ?? ''))
                                                        : '-';
                                                @endphp
                                                <span>{{ $shipFrom }}</span>
                                                <i class="ti ti-arrow-right mx-1" style="color:#6c757d;font-size:11px;"></i>
                                                <span>{{ $shipTo }}</span>
                                            </td>
                                            <!-- <td>{{ $invoice->invoice_date ? date('d-m-Y', strtotime($invoice->invoice_date)) : '-' }}</td> -->
                                            <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                            <td>{{ $invoice->invoice_currency }}</td>
                                            <td>{{ $invoice->incoterms }}</td>
                                            <!-- <td>{{ $invoice->reference_number ?: '-' }}</td> -->
                                            <td>
                                                @php
                                                    $displayStatus = $invoice->status === 'cancelled' ? 'cancelled' : ($invoice->shipperInfo && $invoice->shipperInfo->status ? $invoice->shipperInfo->status : 'draft');
                                                    $statusBadge = [
                                                        'draft' => 'badge bg-warning text-dark',
                                                        'ready' => 'badge bg-info',
                                                        'packed' => 'badge bg-primary',
                                                        'manifested' => 'badge bg-secondary',
                                                        'received' => 'badge bg-success',
                                                        'dispatched' => 'badge bg-dark',
                                                        'delivered' => 'badge bg-success',
                                                        'cancelled' => 'badge bg-danger',
                                                        'disputed' => 'badge bg-warning',
                                                        'on_hold' => 'badge bg-secondary',
                                                    ];
                                                    $statusLabel = [
                                                        'draft' => 'Draft',
                                                        'ready' => 'Ready',
                                                        'packed' => 'Packed',
                                                        'manifested' => 'Manifested',
                                                        'received' => 'Received',
                                                        'dispatched' => 'Dispatched',
                                                        'delivered' => 'Delivered',
                                                        'cancelled' => 'Cancelled',
                                                        'disputed' => 'Disputed',
                                                        'on_hold' => 'On Hold',
                                                    ];
                                                @endphp
                                                <span class="{{ $statusBadge[$displayStatus] ?? 'badge bg-warning text-dark' }}">{{ $statusLabel[$displayStatus] ?? ucfirst($displayStatus) }}</span>
                                            </td>
                                            <td class="text-center">
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
                                            <td class="text-center">
                                                @if($invoice->status === 'cancelled')
                                                    <span class="text-muted" style="font-size:12px;">N/A</span>
                                                @elseif($invoice->shipperInfo && $invoice->shipperInfo->status && $invoice->shipperInfo->status !== 'draft')
                                                    <span class="text-muted" style="font-size:12px;">Paid</span>
                                                @else
                                                    <button class="btn btn-sm btn-success pay-now-btn"
                                                            data-invoice-id="{{ $invoice->id }}"
                                                            data-shipper-id="{{ $invoice->shipperInfo ? $invoice->shipperInfo->id : '' }}"
                                                            data-amount="{{ $invoice->total_amount }}"
                                                            style="padding:4px 12px;font-size:13px;border-radius:4px;">
                                                        <i class="ti ti-credit-card me-1"></i>Pay Now
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="text-center manifest-col">
                                                @php
                                                    $isPacked = $invoice->shipperInfo && $invoice->shipperInfo->status === 'packed';
                                                    $isManifested = $invoice->shipperInfo && $invoice->shipperInfo->status === 'manifested';
                                                @endphp
                                                @if($isPacked)
                                                    <button class="btn btn-sm btn-outline-success manifest-single-btn"
                                                            data-shipper-id="{{ $invoice->shipperInfo ? $invoice->shipperInfo->id : '' }}"
                                                            data-invoice-id="{{ $invoice->id }}"
                                                            style="padding:4px 12px;font-size:13px;border-radius:4px;">
                                                        <i class="ti ti-package-export me-1"></i>Manifest
                                                    </button>
                                                @elseif($isManifested)
                                                    <span class="badge bg-success" style="font-size:11px;">Manifested</span>
                                                @else
                                                    <span class="text-muted" style="font-size:12px;">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $invoice->created_at ? date('d-m-Y', strtotime($invoice->created_at)) : '-' }}</td>
                                            <td class="text-center">
                                                @if($invoice->status === 'cancelled')
                                                    <button class="btn btn-cancel" disabled>
                                                        <i class="ti ti-x"></i> Cancelled
                                                    </button>
                                                @else
                                                    <button class="btn btn-cancel cancel-btn"
                                                            data-id="{{ $invoice->id }}"
                                                            data-invoice="{{ $invoice->invoice_number }}">
                                                        <i class="ti ti-ban"></i> Cancel
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

                    <!-- AWB Number -->
                    <div class="detail-section">
                        <h6><i class="ti ti-clipboard me-1"></i> AWB & Invoice Info</h6>
                        <div class="detail-row">
                            <span class="label">AWB Number</span>
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
                    <p class="text-muted mt-2 mb-0" style="font-size:13px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel Shipment</button>
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
                        <label class="form-label fw-semibold">Amount to Pay (₹)</label>
                        <input type="number" disabled class="form-control" id="payAmount" min="0.01" step="0.01" placeholder="Enter amount">
                    </div>
                    <div class="alert alert-info d-flex align-items-center py-2 px-3 mb-0" style="border-radius:8px;">
                        <i class="ti ti-wallet fs-18 me-2"></i>
                        <div>
                            <span class="fw-semibold">Wallet Balance:</span>
                            <span class="fw-bold text-primary" id="payWalletBalance">₹0.00</span>
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
                    <!-- Invoice Items -->
                    <div id="printItemsSection">
                        <strong style="font-size:13px;">INVOICE ITEMS</strong>
                        <div class="table-responsive mt-1">
                            <table class="table table-sm table-bordered mb-0" style="font-size:11px;">
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
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printLabel()">
                        <i class="ti ti-printer me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JsBarcode CDN -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

    <!-- Datatable JS -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>

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

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('shipmentDetailModal'));
        modal.show();
    }

    function viewLabel(invoiceId) {
        const data = shipmentData[invoiceId];
        if (!data || !data.has_label || !data.graphic_image) {
            alert('Label not available for this shipment.');
            return;
        }

        // Decode base64 GraphicImage and open as PDF
        const base64Data = data.graphic_image;
        const format = (data.label_format || 'PDF').toUpperCase();

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
            a.download = 'label_' + (data.awb_number || invoiceId) + '.' + extension;
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

            // Initialize DataTable
            // Column layout: 0=Checkbox, 1=#, 2=AWB, 3=Consignee, 4=Amount, 5=Currency,
            //                 6=Incoterms, 7=Status, 8=Print Label, 9=Pay Now, 10=Manifest,
            //                 11=Created, 12=Cancel
            $('#shipmentsTable').DataTable({
                order: [[1, 'asc']],
                pageLength: 25,
                columnDefs: [
                    { targets: [0, 8, 9, 10], visible: false }
                ],
                language: {
                    search: "Search shipments:",
                    emptyTable: "No shipments found."
                }
            });

            // Status filter button click handler
            $('.status-filter-btn').on('click', function () {
                const filter = $(this).data('filter');

                // Update button active state
                $('.status-filter-btn').removeClass('btn-primary').addClass('btn-light');
                $(this).removeClass('btn-light').addClass('btn-primary');

                // Update heading
                $('#statusFilterHeading').text($(this).text());

                // Filter DataTable using custom search on data-status attribute
                const dt = $('#shipmentsTable').DataTable();
                $.fn.dataTable.ext.search = []; // Clear previous custom filters

                // Column visibility: Checkbox=0, Print Label=8, Pay Now=9, Manifest=10
                if (filter === 'all') {
                    dt.column(0).visible(false);  // Hide Checkbox
                    dt.column(8).visible(false);  // Hide Print Label
                    dt.column(9).visible(false);  // Hide Pay Now
                    dt.column(10).visible(false); // Hide Manifest
                    $('#selectAllCheckbox, .bulk-manifest-checkbox').hide();
                    $('#bulkManifestBtn').hide();
                    dt.draw();
                    return;
                } else if (filter === 'draft') {
                    dt.column(0).visible(false);  // Hide Checkbox
                    dt.column(8).visible(false);  // Hide Print Label
                    dt.column(9).visible(true);   // Show Pay Now
                    dt.column(10).visible(false); // Hide Manifest
                    $('#selectAllCheckbox, .bulk-manifest-checkbox').hide();
                    $('#bulkManifestBtn').hide();
                } else if (filter === 'ready') {
                    dt.column(0).visible(false);  // Hide Checkbox
                    dt.column(8).visible(true);   // Show Print Label
                    dt.column(9).visible(false);  // Hide Pay Now
                    dt.column(10).visible(false); // Hide Manifest
                    $('#selectAllCheckbox, .bulk-manifest-checkbox').hide();
                    $('#bulkManifestBtn').hide();
                } else if (filter === 'packed') {
                    dt.column(0).visible(true);   // Show Checkbox
                    dt.column(8).visible(true);   // Show Print Label
                    dt.column(9).visible(false);  // Hide Pay Now
                    dt.column(10).visible(true);  // Show Manifest
                    $('#selectAllCheckbox, .bulk-manifest-checkbox').show();
                    $('#bulkManifestBtn').show();
                } else {
                    dt.column(0).visible(false);  // Hide Checkbox
                    dt.column(8).visible(true);   // Show Print Label
                    dt.column(9).visible(false);  // Hide Pay Now
                    dt.column(10).visible(false); // Hide Manifest
                    $('#selectAllCheckbox, .bulk-manifest-checkbox').hide();
                    $('#bulkManifestBtn').hide();
                }

                $.fn.dataTable.ext.search.push(function (settings, rowData, rowIndex) {
                    const tr = dt.row(rowIndex).node();
                    return $(tr).data('status') === filter;
                });
                dt.draw();
            });

            // Print Label button click handler (delegated)
            $('#shipmentsTable').on('click', '.print-label-btn', function () {
                const invoiceId = $(this).data('invoice-id');
                const data = shipmentData[invoiceId];
                if (!data) return;

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

                // If status is ready, mark as packed via AJAX before showing modal
                const $row = $(this).closest('tr');
                if (data.status === 'ready' && data.shipper_id) {
                    $.ajax({
                        url: '{{ url("/customer/mark-packed") }}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            shipper_id: data.shipper_id
                        },
                        success: function (response) {
                            if (response.success) {
                                // Update row data-status for DataTable filtering
                                $row.attr('data-status', 'packed');
                                // Update status badge
                                const $badge = $row.find('td:eq(7) span');
                                $badge.removeClass().addClass('badge bg-primary').text('Packed');
                                // Update shipmentData cache
                                shipmentData[invoiceId].status = 'packed';
                                

                                // Trigger event so modal close handler knows to redraw table
                                $(document).trigger('print-status-changed');

                                setTimeout(() => {
                                    window.location.reload(); // Reload to update all data - can be optimized to just redraw table row if needed
                                }, 1500);
                            }
                        },
                        error: function () {
                            // Silently fail - modal still opens
                        }
                    });
                }

                $('#printLabelModal').modal('show');
            });

            // When Print Label modal closes, redraw table if status was changed
            let printStatusChanged = false;
            $('#printLabelModal').on('hidden.bs.modal', function () {
                if (printStatusChanged) {
                    printStatusChanged = false;
                    $('#shipmentsTable').DataTable().draw();
                }
            });

            // Track when Print Label triggers a status change
            $(document).on('print-status-changed', function () {
                printStatusChanged = true;
            });

            // Cancel button click handler
            let cancelId = null;

            // Cancel button click handler (delegated)
            $('#shipmentsTable').on('click', '.cancel-btn', function () {
                cancelId = $(this).data('id');
                const invoiceRef = $(this).data('invoice');
                $('#cancelInvoiceRef').text(invoiceRef);
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
                            // Update row UI - set status to cancelled
                            const row = $('#invoice-row-' + cancelId);
                            row.attr('data-status', 'cancelled');
                            row.find('.badge').removeClass().addClass('badge bg-danger').text('Cancelled');
                            row.find('.cancel-btn').prop('disabled', true)
                                .html('<i class="ti ti-x"></i> Cancelled')
                                .removeClass('cancel-btn')
                                .addClass('disabled');

                            showAlert('success', response.message);
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
            const walletBalance = {{ $currentWalletBalance }};

            // Update wallet balance display in modal
            $('#payWalletBalance').text('₹' + number_format(walletBalance, 2));

            // Pay Now button click handler (delegated)
            $('#shipmentsTable').on('click', '.pay-now-btn', function () {
                payInvoiceId = $(this).data('invoice-id');
                payShipperId = $(this).data('shipper-id');
                const amount = $(this).data('amount');

                // Set reference and default amount
                const refText = shipmentData[payInvoiceId] ? (shipmentData[payInvoiceId].awb_number || shipmentData[payInvoiceId].invoice_number) : 'Shipment #' + payInvoiceId;
                $('#payShipmentRef').val(refText);
                $('#payAmount').val(amount);
                $('#payWalletBalance').text('₹' + number_format(walletBalance, 2));

                $('#payNowModal').modal('show');
            });

            // Confirm Pay Now
            $('#confirmPayNowBtn').on('click', function () {
                if (!payInvoiceId || !payShipperId) return;

                const amount = parseFloat($('#payAmount').val());
                if (!amount || amount <= 0) {
                    showAlert('danger', 'Please enter a valid amount to pay.');
                    return;
                }
                if (amount > walletBalance) {
                    showAlert('danger', 'Insufficient wallet balance! Your balance is ₹' + number_format(walletBalance, 2));
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

                $.ajax({
                    url: '{{ url("/customer/pay-now") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        invoice_id: payInvoiceId,
                        shipper_id: payShipperId,
                        amount: amount
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#payNowModal').modal('hide');
                            // Show success popup
                            const popupHtml = '<div class="modal fade" id="paymentSuccessPopup" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content border-0 shadow"><div class="modal-body text-center py-4"><div class="mb-3"><i class="ti ti-circle-check fs-48" style="color:#28a745;"></i></div><h5 class="fw-bold mb-1">Payment Successful!</h5><p class="text-muted mb-3">' + response.message + '<br>New wallet balance: ₹' + number_format(response.new_balance, 2) + '</p><button class="btn btn-success px-4" id="paymentSuccessOkBtn">OK</button></div></div></div></div>';
                            $('body').append(popupHtml);
                            const successPopup = new bootstrap.Modal(document.getElementById('paymentSuccessPopup'), { backdrop: 'static', keyboard: false });
                            successPopup.show();

                            // Reload page when OK is clicked or modal is hidden
                            $('#paymentSuccessOkBtn').on('click', function () {
                                window.location.reload();
                            });
                            document.getElementById('paymentSuccessPopup').addEventListener('hidden.bs.modal', function () {
                                window.location.reload();
                                this.remove();
                            });
                        } else {
                            showAlert('danger', response.message);
                            $('#payNowModal').modal('hide');
                        }
                        btn.prop('disabled', false).html('<i class="ti ti-credit-card me-1"></i>Confirm Payment');
                    },
                    error: function (xhr) {
                        let msg = 'Error processing payment.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        $('#payNowModal').modal('hide');
                        btn.prop('disabled', false).html('<i class="ti ti-credit-card me-1"></i>Confirm Payment');
                    }
                });
            });

            // number_format helper for JS
            function number_format(num, decimals) {
                decimals = decimals || 2;
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
                const shipperId = $btn.data('shipper-id');
                const invoiceId = $btn.data('invoice-id');

                if (!shipperId) return;

                // Confirm with user
                if (!confirm('Are you sure you want to manifest this shipment? This will call the UPS Ship API to create the shipment.')) {
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
                            const $badge = $row.find('td:eq(7) span');
                            $badge.removeClass().addClass('badge bg-secondary').text('Manifested');
                            // Update manifest column
                            const $manifestCol = $row.find('.manifest-col');
                            $manifestCol.html('<span class="badge bg-success" style="font-size:11px;">Manifested</span>');
                            // Update Pay Now column
                            const $payCol = $row.find('td:eq(9)');
                            $payCol.html('<span class="text-muted" style="font-size:12px;">Paid</span>');

                            showAlert('success', 'Shipment manifested successfully! Tracking: ' + (response.tracking_number || 'N/A'));
                        } else {
                            showAlert('danger', response.message || 'Manifest failed.');
                            $btn.prop('disabled', false).html('<i class="ti ti-package-export me-1"></i>Manifest');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Error manifesting shipment.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        $btn.prop('disabled', false).html('<i class="ti ti-package-export me-1"></i>Manifest');
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

                if (!confirm('Are you sure you want to manifest ' + shipperIds.length + ' selected shipment(s)? This will call the UPS Ship API for each shipment.')) {
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
                                    const $badge = $row.find('td:eq(7) span');
                                    $badge.removeClass().addClass('badge bg-secondary').text('Manifested');
                                    const $manifestCol = $row.find('.manifest-col');
                                    $manifestCol.html('<span class="badge bg-success" style="font-size:11px;">Manifested</span>');
                                    const $payCol = $row.find('td:eq(9)');
                                    $payCol.html('<span class="text-muted" style="font-size:12px;">Paid</span>');
                                });
                            }

                            // Uncheck all
                            $('#selectAllCheckbox, .bulk-manifest-checkbox').prop('checked', false);

                            const failedCount = results.failed ? results.failed.length : 0;
                            if (failedCount > 0) {
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
            // SELECT ALL: Check/Uncheck all visible checkboxes
            // =============================================
            $('#selectAllCheckbox').on('change', function () {
                const isChecked = $(this).is(':checked');
                $('.bulk-manifest-checkbox:visible').prop('checked', isChecked);
            });

            // Uncheck "Select All" if any individual checkbox is unchecked
            $(document).on('change', '.bulk-manifest-checkbox', function () {
                if (!$(this).is(':checked')) {
                    $('#selectAllCheckbox').prop('checked', false);
                } else if ($('.bulk-manifest-checkbox:visible:checked').length === $('.bulk-manifest-checkbox:visible').length) {
                    $('#selectAllCheckbox').prop('checked', true);
                }
            });

        });

        // Print Label function (outside document.ready so it's globally accessible)
        function printLabel() {
            const modalBody = document.getElementById('printLabelBody');
            const content = modalBody.cloneNode(true);
            const printWindow = window.open('', '_blank', 'width=800,height=700');
            printWindow.document.write('<!DOCTYPE html><html><head><title>Print Label</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body{font-family:Arial,sans-serif;padding:15px;color:#000;font-size:12px;}');
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
            printWindow.document.write('@media print{body{margin:0;padding:10px;} @page{size:A4;margin:10mm;}}');
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