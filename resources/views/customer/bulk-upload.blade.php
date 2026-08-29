<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bulk Upload | United Courier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bulk upload shipments via Excel file">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Tabler Icon CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}"> -->
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/plugins/tabler-icons/tabler-icons.min.css">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .upload-zone {
            border: 2px dashed #c7c9d1;
            border-radius: 12px;
            padding: 48px 24px;
            text-align: center;
            background: #fafbfc;
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .upload-zone:hover,
        .upload-zone.dragover {
            border-color: #2d8eff;
            background: #f0f7ff;
        }
        .upload-zone .upload-icon {
            font-size: 48px;
            color: #2d8eff;
        }
        .step-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 10px;
        }
        .step-badge.active {
            background: #2d8eff;
            color: #fff;
        }
        .result-row {
            transition: background 0.2s ease;
        }
        .result-row:hover {
            background: #f8f9fa;
        }
        .preview-table th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }
        .preview-table td {
            font-size: 0.85rem;
            vertical-align: middle;
        }
        .rate-badge {
            font-weight: 600;
            color: #198754;
        }
        .rate-zero {
            font-weight: 600;
            color: #dc3545;
        }
        .preview-modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        .grand-total-row {
            background: #f0f7ff;
            font-weight: 700;
        }
        /* Shipment card */
        .shipment-card {
            border: 1px solid #e3e6ec;
            border-radius: 12px;
            margin-bottom: 16px;
            overflow: hidden;
        }
        .shipment-card-header {
            background: #f8f9fa;
            padding: 12px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }
        .shipment-card-header:hover {
            background: #f1f3f5;
        }
        .shipment-card-body {
            padding: 16px;
            display: none;
        }
        .shipment-card.expanded .shipment-card-body {
            display: block;
        }
        .shipment-card.expanded .chevron-rotate {
            transform: rotate(180deg);
        }
        .chevron-rotate {
            transition: transform 0.2s ease;
        }
        /* Rate card */
        .rate-card {
            border: 1px solid #e3e6ec;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .rate-card:hover {
            border-color: #2d8eff;
            background: #f6faff;
        }
        .rate-card.selected {
            border-color: #198754;
            background: #ecfdf3;
            box-shadow: 0 0 0 1px #198754 inset;
        }
        .rate-card .rate-method {
            font-weight: 600;
            font-size: 0.9rem;
        }
        .rate-card .rate-network {
            font-size: 0.75rem;
            color: #6c757d;
        }
        .rate-card .rate-tat {
            font-size: 0.75rem;
            color: #6c757d;
        }
        .rate-card .rate-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #198754;
        }
        .rate-card .rate-breakdown {
            font-size: 0.72rem;
            color: #6c757d;
        }
        .rate-card .rate-radio {
            width: 18px;
            height: 18px;
        }
        .no-rates {
            color: #dc3545;
            font-size: 0.85rem;
            font-style: italic;
        }
        .selected-rate-summary {
            font-size: 0.8rem;
            color: #198754;
            font-weight: 600;
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
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
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
        ========================== -->
        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <!-- <h4 class="mb-1">Bulk Upload Order</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ url('/customer/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Bulk Upload</li>
                            </ol>
                        </nav> -->
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ url('/customer/view-all-shipments') }}" class="btn btn-outline-light shadow">
                            <i class="ti ti-list me-1"></i> View Order
                        </a>
                    </div>
                </div>
                <!-- End Page Header -->

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-check me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row g-3">
                    <!-- Full Width Column: Upload Form -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title d-flex align-items-center mb-3">
                                    <span class="step-badge active">1</span> Upload Excel File
                                </h5>
                                <p class="text-muted mb-4">Upload an Excel (.xlsx) or CSV file containing shipment data. Each unique <strong>AwbNo</strong> will be treated as one consignee shipment; multiple rows sharing the same AwbNo become invoice line items. Rate is calculated using the <strong>ChgWeight</strong> column.</p>

                                <form id="bulkUploadForm" action="{{ url('/customer/bulk-upload') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <!-- Hidden field for selected rates (filled by preview modal) -->
                                    <input type="hidden" name="selected_rates" id="selectedRatesInput" value="">

                                    <!-- Upload zone -->
                                    <div class="upload-zone" id="uploadZone">
                                        <input type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx,.csv" style="display:none;" required>
                                        <i class="ti ti-cloud-upload upload-icon"></i>
                                        <h6 class="mt-3 mb-1">Click to browse or drag & drop your file here</h6>
                                        <p class="text-muted mb-0">Accepted formats: .xlsx, .xls, .csv (max 20MB)</p>
                                        <div id="fileNameDisplay" class="mt-2 fw-semibold text-primary" style="display:none;"></div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mt-4">
                                        <a href="#" id="downloadTemplateBtn" class="btn btn-outline-light">
                                            <i class="ti ti-download me-1"></i> Download Excel Template
                                        </a>
                                        <button type="button" id="previewBtn" class="btn btn-primary" disabled>
                                            <i class="ti ti-eye me-1"></i> Preview & Calculate Rate
                                        </button>
                                        <button type="submit" id="uploadSubmitBtn" class="btn btn-success d-none">
                                            <i class="ti ti-check me-1"></i> Confirm & Create Shipments
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Results section (shown after upload) -->
                        @php
                            $createdShipments = session('created_shipments', []);
                            $uploadErrors = session('upload_errors', []);
                        @endphp

                        @if(!empty($createdShipments) || !empty($uploadErrors))
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h5 class="card-title d-flex align-items-center mb-3">
                                        <span class="step-badge active">2</span> Upload Results
                                    </h5>

                                    @if(!empty($createdShipments))
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>AWB Number</th>
                                                        <th>Consignee</th>
                                                        <th>City</th>
                                                        <th>Weight (kg)</th>
                                                        <th>Rate</th>
                                                        <th>Invoice</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($createdShipments as $idx => $shipment)
                                                        <tr class="result-row">
                                                            <td>{{ $idx + 1 }}</td>
                                                            <td><span class="fw-semibold">{{ $shipment['awb_number'] }}</span></td>
                                                            <td>{{ $shipment['consignee_name'] ?: '-' }}</td>
                                                            <td>{{ $shipment['consignee_city'] ?: '-' }}</td>
                                                            <td>{{ number_format($shipment['total_weight'], 2) }}</td>
                                                            <td>₹{{ number_format($shipment['rate'], 2) }}</td>
                                                            <td>{{ $shipment['invoice_number'] ?: '-' }}</td>
                                                            <td>
                                                                @if(!empty($shipment['invoice_pdf']))
                                                                    <a href="{{ asset($shipment['invoice_pdf']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                        <i class="ti ti-file-type-pdf me-1"></i> PDF
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    @if(!empty($uploadErrors))
                                        <div class="alert alert-warning mt-3 mb-0">
                                            <h6 class="alert-heading"><i class="ti ti-alert-triangle me-1"></i> {{ count($uploadErrors) }} row(s) failed:</h6>
                                            <ul class="mb-0">
                                                @foreach($uploadErrors as $err)
                                                    <li><strong>AwbNo: {{ $err['awb_no'] }}</strong> - {{ $err['message'] }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
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

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">
                        <i class="ti ti-eye me-2 text-primary"></i> Shipment Preview & Rate Calculation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body preview-modal-body">
                    <!-- Summary badges -->
                    <div class="d-flex flex-wrap gap-2 mb-3" id="previewSummary">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                            <i class="ti ti-package me-1"></i> Total Shipments: <strong id="previewTotalShipments">0</strong>
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                            <i class="ti ti-cash me-1"></i> Grand Total: <strong id="previewGrandTotal">₹0.00</strong>
                        </span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">
                            <i class="ti ti-info-circle me-1"></i> Click a shipment to expand & select a rate
                        </span>
                    </div>

                    <!-- Shipment cards container (filled by JS) -->
                    <div id="previewShipmentsContainer">
                        <!-- Filled by JS -->
                    </div>

                    <!-- Grand total footer -->
                    <div class="d-flex justify-content-end mt-3">
                        <div class="grand-total-row px-4 py-2 rounded">
                            Grand Total: <span id="previewGrandTotalCell" class="ms-2">₹0.00</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> Cancel
                    </button>
                    <button type="button" id="confirmSubmitBtn" class="btn btn-success">
                        <i class="ti ti-check me-1"></i> Confirm & Create Shipments
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Preview Modal -->

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        $(function () {
            var $zone = $('#uploadZone');
            var $input = $('#excel_file');
            var $display = $('#fileNameDisplay');
            var $previewBtn = $('#previewBtn');
            var $submitBtn = $('#uploadSubmitBtn');
            var $form = $('#bulkUploadForm');
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Store preview data for confirmation
            var previewData = null;

            // Click to browse
            $zone.on('click', function (e) {
                if (e.target === $input[0]) return;
                $input.trigger('click');
            });

            // Stop the input's click from bubbling back up to the zone (prevents infinite loop)
            $input.on('click', function (e) {
                e.stopPropagation();
            });

            // File selected
            $input.on('change', function () {
                var file = this.files[0];
                if (file) {
                    $display.text(file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)').show();
                    $previewBtn.prop('disabled', false);
                    $submitBtn.addClass('d-none');
                }
            });

            // Drag & drop
            $zone.on('dragover', function (e) {
                e.preventDefault();
                $(this).addClass('dragover');
            }).on('dragleave', function (e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            }).on('drop', function (e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                var files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    $input[0].files = files;
                    $input.trigger('change');
                }
            });

            // Format currency
            function formatINR(val) {
                return '₹' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            // Preview button click - send file to preview endpoint via AJAX
            $previewBtn.on('click', function () {
                var file = $input[0].files[0];
                if (!file) {
                    showAlert('Please select an Excel file first.', 'warning');
                    return;
                }

                var formData = new FormData();
                formData.append('excel_file', file);
                formData.append('_token', csrfToken);

                var originalHtml = $previewBtn.html();
                $previewBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Calculating...');

                $.ajax({
                    url: '{{ route("customer.bulk-upload.preview") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $previewBtn.prop('disabled', false).html(originalHtml);
                        if (response.success) {
                            previewData = response;
                            renderPreview(response);
                            var modal = new bootstrap.Modal(document.getElementById('previewModal'));
                            modal.show();
                        } else {
                            showAlert(response.message || 'Failed to generate preview.', 'error');
                        }
                    },
                    error: function (xhr) {
                        $previewBtn.prop('disabled', false).html(originalHtml);
                        var msg = 'Failed to generate preview.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert(msg, 'error');
                    }
                });
            });

            // Track selected rate per shipment: { awb_no: rate_id }
            var selectedRateMap = {};

            // Render preview as expandable shipment cards with rate cards inside
            function renderPreview(data) {
                var shipments = data.shipments || [];
                var $container = $('#previewShipmentsContainer');
                $container.empty();
                selectedRateMap = {};

                $('#previewTotalShipments').text(data.total_shipments || shipments.length);

                shipments.forEach(function (s, idx) {
                    var allRates = s.all_rates || [];
                    var defaultRate = s.default_rate || null;

                    // Pre-select the default rate
                    if (defaultRate && defaultRate.rate_id) {
                        selectedRateMap[s.awb_no] = defaultRate.rate_id;
                    }

                    // Build the rate cards HTML
                    var rateCardsHtml = '';
                    if (allRates.length === 0) {
                        rateCardsHtml = '<div class="no-rates"><i class="ti ti-alert-circle me-1"></i> No rates available for this shipment (check weight/zone/service configuration).</div>';
                    } else {
                        allRates.forEach(function (r, rIdx) {
                            var isSelected = (defaultRate && r.rate_id === defaultRate.rate_id);
                            var selectedClass = isSelected ? ' selected' : '';
                            var checkedAttr = isSelected ? 'checked' : '';

                            var breakdownText = 'Total Base: ' + formatINR(r.total_base_price != null ? r.total_base_price : r.price) +
                                ' | Total Fuel (' + Number(r.fuel_percentage || 0).toFixed(1) + '%): ' + formatINR(r.total_fuel_price != null ? r.total_fuel_price : r.fuel_charge) +
                                ' | Surcharge: ' + formatINR(r.total_surcharge != null ? r.total_surcharge : r.surcharge_total) +
                                ' | GST (' + Number(r.gst_percentage || 0).toFixed(1) + '%): ' + formatINR(r.gst_amount);

                            rateCardsHtml += '<div class="rate-card' + selectedClass + '" data-awb="' + escapeHtml(s.awb_no) + '" data-rate-id="' + r.rate_id + '">' +
                                '<div class="d-flex align-items-center gap-2">' +
                                    '<input type="radio" name="rate_' + idx + '" class="rate-radio form-check-input" value="' + r.rate_id + '" ' + checkedAttr + '>' +
                                    '<div>' +
                                        '<div class="rate-method">' + escapeHtml(r.method) + '</div>' +
                                        '<div class="rate-network">' + escapeHtml(r.network) + (r.tat ? ' &middot; ' + escapeHtml(r.tat) + ' days' : '') + '</div>' +
                                        '<div class="rate-breakdown">' + breakdownText + '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="rate-price">' + formatINR(r.total) + '</div>' +
                            '</div>';
                        });
                    }

                    // Selected rate summary (shown in header)
                    var summaryText = defaultRate
                        ? '<span class="selected-rate-summary">' + escapeHtml(defaultRate.method) + ' &mdash; ' + formatINR(defaultRate.total) + '</span>'
                        : '<span class="no-rates">No rate selected</span>';

                    var cardHtml = '<div class="shipment-card" id="shipmentCard_' + idx + '" data-awb="' + escapeHtml(s.awb_no) + '">' +
                        '<div class="shipment-card-header" data-idx="' + idx + '">' +
                            '<div class="d-flex align-items-center gap-2 flex-wrap">' +
                                '<span class="badge bg-primary">' + (idx + 1) + '</span>' +
                                '<strong>' + escapeHtml(s.awb_no) + '</strong>' +
                                '<span class="text-muted">|</span>' +
                                '<span>' + escapeHtml(s.consignee_name) + '</span>' +
                                '<span class="text-muted small">' + escapeHtml(s.consignee_city) + ' / ' + escapeHtml(s.consignee_state) + '</span>' +
                            '</div>' +
                            '<div class="d-flex align-items-center gap-3 flex-wrap">' +
                                '<span class="text-muted small"><i class="ti ti-package me-1"></i>' + s.pieces + ' pcs &middot; ' + Number(s.total_weight).toFixed(2) + ' kg</span>' +
                                summaryText +
                                '<i class="ti ti-chevron-down chevron-rotate text-muted"></i>' +
                            '</div>' +
                        '</div>' +
                        '<div class="shipment-card-body">' +
                            '<div class="row mb-2">' +
                                '<div class="col-md-6"><small class="text-muted d-block">Destination</small><span>' + escapeHtml(s.destination) + '</span></div>' +
                                '<div class="col-md-3"><small class="text-muted d-block">Invoice No</small><span>' + escapeHtml(s.invoice_no) + '</span></div>' +
                                '<div class="col-md-3"><small class="text-muted d-block">Invoice Value</small><span>' + formatINR(s.invoice_value) + '</span></div>' +
                            '</div>' +
                            '<hr class="my-2">' +
                            '<label class="form-label fw-semibold mb-2"><i class="ti ti-truck me-1 text-primary"></i> Select a Service Rate:</label>' +
                            rateCardsHtml +
                        '</div>' +
                    '</div>';

                    $container.append(cardHtml);
                });

                // Bind header click to expand/collapse
                $container.find('.shipment-card-header').on('click', function () {
                    $(this).closest('.shipment-card').toggleClass('expanded');
                });

                // Bind rate card selection
                $container.find('.rate-card').on('click', function (e) {
                    if ($(e.target).is('input[type="radio"]')) return; // let radio handle itself
                    var awb = $(this).data('awb');
                    var rateId = $(this).data('rate-id');
                    selectRate(awb, rateId, $(this));
                });
                $container.find('.rate-radio').on('change', function () {
                    var $card = $(this).closest('.rate-card');
                    var awb = $card.data('awb');
                    var rateId = $card.data('rate-id');
                    selectRate(awb, rateId, $card);
                });

                updateGrandTotal();
            }

            // Select a rate for a shipment and update UI
            function selectRate(awb, rateId, $cardEl) {
                selectedRateMap[awb] = rateId;

                // Update radio checked state within this shipment
                $cardEl.find('.rate-radio').prop('checked', true);

                // Update selected class on all rate cards in this shipment
                $cardEl.siblings('.rate-card').removeClass('selected');
                $cardEl.addClass('selected');

                // Update the summary in the shipment header
                var method = $cardEl.find('.rate-method').text();
                var price = $cardEl.find('.rate-price').text();
                var $header = $cardEl.closest('.shipment-card').find('.shipment-card-header');
                $header.find('.selected-rate-summary, .no-rates').replaceWith(
                    '<span class="selected-rate-summary">' + escapeHtml(method) + ' &mdash; ' + price + '</span>'
                );

                updateGrandTotal();
            }

            // Recalculate grand total from selected rates
            function updateGrandTotal() {
                var total = 0;
                if (previewData && previewData.shipments) {
                    previewData.shipments.forEach(function (s) {
                        var rateId = selectedRateMap[s.awb_no];
                        if (rateId && s.all_rates) {
                            var matched = s.all_rates.find(function (r) { return r.rate_id == rateId; });
                            if (matched) {
                                total += parseFloat(matched.total) || 0;
                            }
                        }
                    });
                }
                var formatted = formatINR(total);
                $('#previewGrandTotal').text(formatted);
                $('#previewGrandTotalCell').text(formatted);
            }

            // Escape HTML helper
            function escapeHtml(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/&/g, '&')
                    .replace(/</g, '<')
                    .replace(/>/g, '>')
                    .replace(/"/g, '"')
                    .replace(/'/g, '&#039;');
            }

            // Confirm button in modal - submit the form with selected rates
            $('#confirmSubmitBtn').on('click', function () {
                // Validate that every shipment has a selected rate
                var missingCount = 0;
                if (previewData && previewData.shipments) {
                    previewData.shipments.forEach(function (s) {
                        if (!selectedRateMap[s.awb_no]) {
                            missingCount++;
                        }
                    });
                }
                if (missingCount > 0) {
                    showAlert('Please select a rate for all ' + missingCount + ' shipment(s) before confirming.', 'warning');
                    return;
                }
                // Populate the hidden field with the selected rate map
                $('#selectedRatesInput').val(JSON.stringify(selectedRateMap));
                var modal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
                if (modal) modal.hide();

                // Show the hidden submit button and trigger form submission
                $submitBtn.removeClass('d-none').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');
                $form.trigger('submit');
            });

            // Form submit loading state
            $form.on('submit', function () {
                $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');
            });

            // Download template (generates a sample CSV client-side)
            $('#downloadTemplateBtn').on('click', function (e) {
                e.preventDefault();
                var headers = [
                    'AwbNo','ReferenceNo','Origin','Destination','CustomerName',
                    'ConsignorName','ConsignorContactPerson','ConsignorAddressLine1','ConsignorAddressLine2','ConsignorAddressLine3',
                    'ConsignorCity','ConsignorState','ConsignorPincode','ConsignorTelephone','GSTType','GSTIDNo',
                    'ConsigneeName','ConsigneeContactPerson','ConsigneeAddressLine1','ConsigneeAddressLine2','ConsigneeAddressLine3',
                    'ConsigneeCity','ConsigneeState','ConsigneeZipCode','ConsigneeTelephone',
                    'GoodsType','ServiceType','Pcs','ActWeight','L','B','H','VolWeight','ChgWeight','Dimention',
                    'InvoiceNo','InvoiceValue','Currency','Description','Remark',
                    'HSCode','HTSCode',
                    'CoLoader','CoLoaderNo','Network','NetworkNo','Sector','Runno'
                ];
                var sampleRow = [
                    'AWB001','REF001','Mumbai','Delhi','Acme Corp',
                    'Acme Logistics','John Doe','123 Main St','Suite 100','','Mumbai','Maharashtra','400001','9876543210','GSTIN','27ABCDE1234F1Z5',
                    'Retail Customer','Jane Smith','456 Market Rd','Apt 5','','Delhi','Delhi','110001','9123456780',
                    'Documents','Express','1','2.5','30','20','15','1.5','2.5','30x20x15',
                    'INV001','5000','INR','Shipping documents','Sample remark',
                    '123456','987654',
                    '','','','','',''
                ];
                var csv = headers.join(',') + '\n' + sampleRow.join(',') + '\n';
                var blob = new Blob([csv], { type: 'text/csv' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'bulk_upload_template.csv';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        });
    </script>

</body>

</html>
