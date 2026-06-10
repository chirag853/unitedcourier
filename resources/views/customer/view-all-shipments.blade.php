<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>View All Shipments | United Courier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .badge-status-active {
            background-color: #28a745;
            color: #fff;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-status-cancelled {
            background-color: #dc3545;
            color: #fff;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 12px;
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
        .card-header-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .table-actions {
            display: flex;
            gap: 6px;
        }
        .table-actions .btn {
            padding: 4px 10px;
            font-size: 13px;
        }
        .awb-link {
            color: #2563eb;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }
        .awb-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
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

                <!-- Shipments Table Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-actions">
                            <h5 class="card-title mb-0">My Shipments</h5>
                            <span class="text-muted" style="font-size:13px;">Total: {{ $invoices->count() }} shipment(s)</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($invoices->isEmpty())
                            <div class="text-center py-5">
                                <i class="ti ti-package" style="font-size:48px;color:#ccc;"></i>
                                <p class="mt-3 text-muted">No shipments found. Create your first shipment now!</p>
                                <a href="{{ url('/customer/create-shipment') }}" class="btn btn-primary">Create Shipment</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="shipmentsTable" class="table table-hover table-centered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>AWB Number</th>
                                            <th>Ship From → Ship To</th>
                                            <th>Invoice Date</th>
                                            <th>Amount</th>
                                            <th>Currency</th>
                                            <th>Incoterms</th>
                                            <th>Reference No.</th>
                                            <th>Status</th>
                                            <th>Label</th>
                                            <th>Created</th>
                                            <th class="text-center">Cancel</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $index => $invoice)
                                        <tr id="invoice-row-{{ $invoice->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($invoice->shipperInfo && $invoice->shipperInfo->awb_number)
                                                    <a href="#" class="awb-link"
                                                       data-invoice-id="{{ $invoice->id }}"
                                                       onclick="showShipmentDetail({{ $invoice->id }}); return false;">
                                                        {{ $invoice->shipperInfo->awb_number }}
                                                    </a>
                                                @else
                                                    <strong>{{ $invoice->invoice_number }}</strong>
                                                @endif
                                            </td>
                                            <td style="font-size:12px;">
                                                @php
                                                    $shipFrom = $invoice->shipperInfo ? trim(($invoice->shipperInfo->city ?? '') . ', ' . ($invoice->shipperInfo->state ?? '') . ' - ' . ($invoice->shipperInfo->pincode ?? '') . ', India') : '-';
                                                    $shipTo = $invoice->shipperInfo && $invoice->shipperInfo->consigneeInfo
                                                        ? trim(($invoice->shipperInfo->consigneeInfo->city ?? '') . ', ' . ($invoice->shipperInfo->consigneeInfo->state ?? '') . ' - ' . ($invoice->shipperInfo->consigneeInfo->zip_code ?? '') . ', ' . ($invoice->shipperInfo->delivery_destination ?? ''))
                                                        : '-';
                                                @endphp
                                                <span style="color:#2563eb;font-weight:500;">{{ $shipFrom }}</span>
                                                <i class="ti ti-arrow-right" style="color:#6c757d;font-size:12px;"></i>
                                                <span style="color:#dc3545;font-weight:500;">{{ $shipTo }}</span>
                                            </td>
                                            <td>{{ $invoice->invoice_date ? date('d-m-Y', strtotime($invoice->invoice_date)) : '-' }}</td>
                                            <td>{{ number_format($invoice->invoice_amount, 2) }}</td>
                                            <td>{{ $invoice->invoice_currency }}</td>
                                            <td>{{ $invoice->incoterms }}</td>
                                            <td>{{ $invoice->reference_number ?: '-' }}</td>
                                            <td>
                                                @if($invoice->status === 'cancelled')
                                                    <span class="badge-status-cancelled">Cancelled</span>
                                                @else
                                                    <span class="badge-status-active">Active</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(isset($shipmentDetails[$invoice->id]) && $shipmentDetails[$invoice->id]['has_label'])
                                                    <a href="#" class="label-link"
                                                       onclick="viewLabel({{ $invoice->id }}); return false;">
                                                        <i class="ti ti-file-text me-1"></i>View Label
                                                    </a>
                                                @else
                                                    <span class="text-muted" style="font-size:12px;">N/A</span>
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

            <!-- Footer -->
            @include('customer.partials.footer')
            <!-- End Footer -->

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
            $('#shipmentsTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                responsive: true,
                language: {
                    search: "Search shipments:",
                    emptyTable: "No shipments found."
                }
            });

            // Cancel button click handler
            let cancelId = null;

            $('.cancel-btn').on('click', function () {
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
                            // Update row UI
                            const row = $('#invoice-row-' + cancelId);
                            row.find('.badge-status-active').removeClass('badge-status-active').addClass('badge-status-cancelled').text('Cancelled');
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

        });
    </script>

</body>

</html>