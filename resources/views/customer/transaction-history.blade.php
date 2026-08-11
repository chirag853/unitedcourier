<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Transaction History | United Courier</title>
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

        .summary-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1) !important;
        }

        .summary-card .summary-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .summary-card .summary-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .summary-card .summary-value {
            font-size: 22px;
            font-weight: 700;
            color: #212529;
        }

        .wallet-balance-box {
            background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%);
            color: #fff;
            border-radius: 16px;
            padding: 20px 24px;
        }

        .wallet-balance-box .wallet-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .wallet-balance-box .wallet-value {
            font-size: 28px;
            font-weight: 700;
        }

        .wallet-balance-box .recharge-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .wallet-balance-box .recharge-btn:hover {
            background: rgba(255, 255, 255, 0.3);
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
                        <h4 class="mb-1">Transaction History</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ url('/customer/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item">Account</li>
                                <li class="breadcrumb-item active" aria-current="page">Transaction History</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <button class="btn btn-light d-flex align-items-center" id="exportCsvBtn">
                            <i class="ti ti-file-export me-1"></i> Export CSV
                        </button>
                        <button class="btn btn-light d-flex align-items-center" onclick="window.print()">
                            <i class="ti ti-printer me-1"></i> Print
                        </button>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <div id="alertContainer"></div>

                <!-- Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Wallet Balance -->
                    <div class="col-12 col-lg-6">
                        <div class="wallet-balance-box h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="wallet-label"><i class="ti ti-wallet me-1"></i> Wallet Balance</div>
                                <div class="wallet-value">₹{{ number_format($walletBalance, 2) }}</div>
                            </div>
                            <div class="mt-2">
                                <button class="recharge-btn" id="rechargeWalletBtn">
                                    <i class="ti ti-plus me-1"></i>Recharge Wallet
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#eef2ff;color:#4f46e5;">
                                    <i class="ti ti-receipt"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Total</div>
                                    <div class="summary-value" id="totalAmountValue">₹{{ number_format($totalAmount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paid Amount -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#dcfce7;color:#16a34a;">
                                    <i class="ti ti-circle-check"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Paid</div>
                                    <div class="summary-value" id="paidAmountValue">₹{{ number_format($paidAmount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cancelled Amount -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <div class="card summary-card shadow-sm h-100">
                            <div class="card-body p-3 d-flex align-items-center gap-3">
                                <div class="summary-icon" style="background:#fee2e2;color:#dc2626;">
                                    <i class="ti ti-ban"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Cancelled</div>
                                    <div class="summary-value" id="cancelledAmountValue">₹{{ number_format($cancelledAmount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-4">
                        <!-- Date Range Filter -->
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label fw-semibold mb-1"><i class="ti ti-calendar me-1"></i>From Date</label>
                                <input type="text" class="form-control" id="dateFrom" placeholder="Select start date" autocomplete="off">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label fw-semibold mb-1"><i class="ti ti-calendar me-1"></i>To Date</label>
                                <input type="text" class="form-control" id="dateTo" placeholder="Select end date" autocomplete="off">
                            </div>
                            <div class="col-12 col-md-3">
                                <button class="btn btn-primary w-100" id="applyDateFilter">
                                    <i class="ti ti-filter me-1"></i>Apply Filter
                                </button>
                            </div>
                            <div class="col-12 col-md-3">
                                <button class="btn btn-light w-100" id="clearDateFilter">
                                    <i class="ti ti-x me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                        <hr class="my-3">
                        <!-- Payment Status Filter Buttons -->
                        <div class="d-flex flex-wrap gap-3">
                            <button class="btn btn-primary rounded-pill px-4 py-2 payment-filter-btn" data-filter="all">
                                All <span class="badge bg-light text-dark ms-1" id="allCount">{{ $invoices->count() }}</span>
                            </button>
                            <button class="btn btn-light rounded-pill px-4 py-2 payment-filter-btn" data-filter="paid">
                                Paid <span class="badge bg-secondary ms-1" id="paidCount">0</span>
                            </button>
                            <button class="btn btn-light rounded-pill px-4 py-2 payment-filter-btn" data-filter="cancelled">
                                Cancelled <span class="badge bg-secondary ms-1" id="cancelledCount">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0" id="filterHeading">All Transactions</h5>
                        <small class="text-muted" id="countInfo">Showing {{ $invoices->count() }} of {{ $invoices->count() }} records</small>
                    </div>
                </div>

                <!-- Payments Table Card -->
                <div class="card border shadow">
                    <div class="card-body">
                        @if($invoices->isEmpty())
                            <div class="text-center py-5">
                                <i class="ti ti-receipt-off" style="font-size:48px;color:#ccc;"></i>
                                <p class="mt-3 text-muted">No transaction records found.</p>
                                <a href="{{ url('/customer/create-shipment') }}" class="btn btn-primary">Create Shipment</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="paymentTable" class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Invoice No.</th>
                                            <th>AWB Number</th>
                                            <th>Consignee</th>
                                            <th>Invoice Date</th>
                                            <th>Amount</th>
                                            <th>Currency</th>
                                            <th>Incoterms</th>
                                            <th>Payment Status</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $index => $invoice)
                                        @php
                                            $rowPaymentStatus = 'unpaid';
                                            if ($invoice->status === 'cancelled') {
                                                $rowPaymentStatus = 'cancelled';
                                            } elseif ($invoice->shipperInfo && $invoice->shipperInfo->status && $invoice->shipperInfo->status !== 'draft') {
                                                $rowPaymentStatus = 'paid';
                                            }

                                            $paymentBadge = [
                                                'paid' => 'badge bg-success',
                                                'unpaid' => 'badge bg-warning text-dark',
                                                'cancelled' => 'badge bg-danger',
                                            ];
                                            $paymentLabel = [
                                                'paid' => 'Paid',
                                                'unpaid' => 'Unpaid',
                                                'cancelled' => 'Cancelled',
                                            ];

                                            $consigneeName = $invoice->shipperInfo && $invoice->shipperInfo->consigneeInfo
                                                ? $invoice->shipperInfo->consigneeInfo->consignee_name
                                                : '-';
                                        @endphp
                                        <tr data-payment-status="{{ $rowPaymentStatus }}" data-created-date="{{ $invoice->created_at ? $invoice->created_at->format('Y-m-d') : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                            <td>
                                                @if($invoice->shipperInfo && $invoice->shipperInfo->awb_number)
                                                    <span class="badge bg-dark">{{ $invoice->shipperInfo->awb_number }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="font-size:13px;">{{ $consigneeName }}</td>
                                            <td>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d-m-Y') : '-' }}</td>
                                            <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                            <td>{{ $invoice->invoice_currency }}</td>
                                            <td>{{ $invoice->incoterms ?: '-' }}</td>
                                            <td>
                                                <span class="{{ $paymentBadge[$rowPaymentStatus] ?? 'badge bg-warning text-dark' }}">{{ $paymentLabel[$rowPaymentStatus] ?? ucfirst($rowPaymentStatus) }}</span>
                                            </td>
                                            <td>{{ $invoice->created_at ? $invoice->created_at->format('d-m-Y') : '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="5" class="text-end">Total</th>
                                            <th id="tfootTotal">₹{{ number_format($totalAmount, 2) }}</th>
                                            <th colspan="4"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- End Payments Table Card -->

            </div>
            <!-- End Content -->

        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Main Wrapper -->

    <!-- Wallet Recharge Modal -->
    <div class="modal fade" id="walletRechargeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="ti ti-wallet me-2"></i>Recharge Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Balance</label>
                        <div class="fw-bold fs-5 text-primary">₹{{ number_format($walletBalance, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recharge Amount (₹)</label>
                        <input type="number" class="form-control" id="rechargeAmount" placeholder="Enter amount" min="1" step="0.01">
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button class="btn btn-light btn-sm quick-amount" data-amount="500">₹500</button>
                        <button class="btn btn-light btn-sm quick-amount" data-amount="1000">₹1,000</button>
                        <button class="btn btn-light btn-sm quick-amount" data-amount="2000">₹2,000</button>
                        <button class="btn btn-light btn-sm quick-amount" data-amount="5000">₹5,000</button>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmRechargeBtn">
                        <i class="ti ti-check me-1"></i>Recharge
                    </button>
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
    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <!-- Main theme JS initializes sidebar dropdowns -->
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script>
        $(document).ready(function () {

            // =============================================
            // ALERT HELPER
            // =============================================
            function showAlert(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : (type === 'danger' ? 'alert-danger' : 'alert-warning');
                var icon = type === 'success' ? 'ti-circle-check' : 'ti-alert-circle';
                var html = '<div class="alert ' + alertClass + ' alert-dismissible fade show d-flex align-items-center" role="alert">' +
                    '<i class="ti ' + icon + ' me-2"></i>' +
                    '<div>' + message + '</div>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                    '</div>';
                $('#alertContainer').html(html);
                setTimeout(function () {
                    $('#alertContainer').empty();
                }, 5000);
            }

            // =============================================
            // DATA TABLE INITIALIZATION
            // =============================================
            var dataTable = $('#paymentTable').DataTable({
                pageLength: 25,
                order: [[4, 'desc']],
                language: {
                    search: "",
                    searchPlaceholder: "Search transactions...",
                    paginate: {
                        previous: "<i class='ti ti-chevron-left'></i>",
                        next: "<i class='ti ti-chevron-right'></i>"
                    }
                },
                dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-md-end"f>>' +
                     'rt' +
                     '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // =============================================
            // FILTER STATE (status + date range combined)
            // =============================================
            var currentStatusFilter = 'all';
            var fpFrom = null, fpTo = null;

            // =============================================
            // NUMBER FORMATTER (matches PHP number_format)
            // =============================================
            function formatCurrency(num) {
                return '₹' + num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            // =============================================
            // FLATPICKR DATE PICKERS
            // =============================================
            fpFrom = flatpickr('#dateFrom', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                maxDate: 'today',
                onChange: function (selectedDates, dateStr) {
                    // Ensure "To" date is never before "From" date
                    if (fpTo && selectedDates[0]) {
                        fpTo.set('minDate', dateStr);
                    }
                }
            });

            fpTo = flatpickr('#dateTo', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                maxDate: 'today',
                onChange: function (selectedDates, dateStr) {
                    // Ensure "From" date is never after "To" date
                    if (fpFrom && selectedDates[0]) {
                        fpFrom.set('maxDate', dateStr || 'today');
                    }
                }
            });

            // =============================================
            // COMBINED FILTER FUNCTION (status + date range)
            // A single persistent search function that checks
            // BOTH the payment status and the date range so the
            // two filters work together seamlessly.
            // =============================================
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var row = dataTable.row(dataIndex).node();
                if (!row) return true;

                var status = $(row).data('payment-status');
                var createdDate = $(row).data('created-date');

                // --- Payment status filter ---
                if (currentStatusFilter !== 'all' && status !== currentStatusFilter) {
                    return false;
                }

                // --- Date range filter ---
                var fromVal = fpFrom && fpFrom.selectedDates[0] ? fpFrom.selectedDates[0] : null;
                var toVal = fpTo && fpTo.selectedDates[0] ? fpTo.selectedDates[0] : null;

                if (fromVal || toVal) {
                    if (!createdDate) return false;
                    var rowDate = new Date(createdDate + 'T00:00:00');
                    if (fromVal && rowDate < fromVal) return false;
                    if (toVal) {
                        var toEnd = new Date(toVal);
                        toEnd.setHours(23, 59, 59, 999);
                        if (rowDate > toEnd) return false;
                    }
                }

                return true;
            });

            // =============================================
            // UPDATE SUMMARY CARDS BASED ON VISIBLE ROWS
            // =============================================
            function updateSummaryCards() {
                var total = 0, paid = 0, cancelled = 0;
                var paidCnt = 0, cancelledCnt = 0;
                var visibleRows = dataTable.rows({ search: 'applied' }).nodes();
                var visibleCount = 0;

                visibleRows.each(function (row) {
                    var $row = $(row);
                    var status = $row.data('payment-status');
                    var amountText = $row.find('td').eq(5).text().replace(/,/g, '').trim();
                    var amount = parseFloat(amountText) || 0;
                    total += amount;
                    visibleCount++;
                    if (status === 'paid') { paid += amount; paidCnt++; }
                    else if (status === 'cancelled') { cancelled += amount; cancelledCnt++; }
                });

                $('#totalAmountValue').text(formatCurrency(total));
                $('#paidAmountValue').text(formatCurrency(paid));
                $('#cancelledAmountValue').text(formatCurrency(cancelled));
                $('#tfootTotal').text(formatCurrency(total));

                // Update filter button badge counts based on visible rows
                $('#allCount').text(visibleCount);
                $('#paidCount').text(paidCnt);
                $('#cancelledCount').text(cancelledCnt);
            }

            // =============================================
            // PAYMENT STATUS FILTER BUTTONS
            // =============================================
            $('.payment-filter-btn').on('click', function () {
                currentStatusFilter = $(this).data('filter');

                // Update button styles
                $('.payment-filter-btn').removeClass('btn-primary').addClass('btn-light');
                $(this).removeClass('btn-light').addClass('btn-primary');

                // Update heading
                var headingMap = {
                    'all': 'All Transactions',
                    'paid': 'Paid Transactions',
                    'cancelled': 'Cancelled Transactions'
                };
                $('#filterHeading').text(headingMap[currentStatusFilter] || 'All Transactions');

                dataTable.draw();
            });

            // =============================================
            // APPLY DATE FILTER BUTTON
            // =============================================
            $('#applyDateFilter').on('click', function () {
                // Validate: "From" must not be after "To"
                var fromVal = fpFrom && fpFrom.selectedDates[0] ? fpFrom.selectedDates[0] : null;
                var toVal = fpTo && fpTo.selectedDates[0] ? fpTo.selectedDates[0] : null;
                if (fromVal && toVal && fromVal > toVal) {
                    showAlert('warning', '"From Date" cannot be later than "To Date".');
                    return;
                }
                dataTable.draw();
            });

            // =============================================
            // CLEAR DATE FILTER BUTTON
            // =============================================
            $('#clearDateFilter').on('click', function () {
                fpFrom.clear();
                fpTo.clear();
                fpFrom.set('maxDate', 'today');
                fpTo.set('minDate', null);
                dataTable.draw();
            });

            // =============================================
            // UPDATE COUNT INFO & SUMMARY ON EVERY DRAW
            // =============================================
            dataTable.on('draw', function () {
                var visibleCount = dataTable.rows({ search: 'applied' }).count();
                var totalCount = dataTable.rows().count();
                $('#countInfo').text('Showing ' + visibleCount + ' of ' + totalCount + ' records');
                updateSummaryCards();
            });

            // =============================================
            // EXPORT CSV
            // =============================================
            $('#exportCsvBtn').on('click', function () {
                var rows = [];
                rows.push(['#', 'Invoice No.', 'AWB Number', 'Consignee', 'Invoice Date', 'Amount', 'Currency', 'Incoterms', 'Payment Status', 'Created']);

                $('#paymentTable tbody tr:visible').each(function (idx) {
                    var $tds = $(this).find('td');
                    var status = $(this).data('payment-status');
                    rows.push([
                        idx + 1,
                        $tds.eq(1).text().trim(),
                        $tds.eq(2).text().trim(),
                        $tds.eq(3).text().trim(),
                        $tds.eq(4).text().trim(),
                        $tds.eq(5).text().trim(),
                        $tds.eq(6).text().trim(),
                        $tds.eq(7).text().trim(),
                        status,
                        $tds.eq(9).text().trim()
                    ]);
                });

                var csvContent = rows.map(function (row) {
                    return row.map(function (cell) {
                        var c = String(cell || '');
                        if (c.includes(',') || c.includes('"') || c.includes('\n')) {
                            c = '"' + c.replace(/"/g, '""') + '"';
                        }
                        return c;
                    }).join(',');
                }).join('\n');

                var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                var url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'transaction-history-' + new Date().toISOString().slice(0, 10) + '.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });

            // =============================================
            // WALLET RECHARGE MODAL
            // =============================================
            $('#rechargeWalletBtn').on('click', function () {
                $('#rechargeAmount').val('');
                var modal = new bootstrap.Modal(document.getElementById('walletRechargeModal'));
                modal.show();
            });

            $('.quick-amount').on('click', function () {
                $('#rechargeAmount').val($(this).data('amount'));
            });

            var isRecharging = false;
            $('#confirmRechargeBtn').on('click', function () {
                if (isRecharging) return;
                var amount = $('#rechargeAmount').val();
                if (!amount || parseFloat(amount) <= 0) {
                    showAlert('danger', 'Please enter a valid amount.');
                    return;
                }

                isRecharging = true;
                var $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

                $.ajax({
                    url: '{{ route("customer.wallet-recharge") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        amount: amount
                    },
                    success: function (response) {
                        if (response.success) {
                            showAlert('success', response.message || 'Wallet recharged successfully!');
                            // Update wallet balance display
                            $('.wallet-value').text('₹' + parseFloat(response.new_balance).toFixed(2));
                            // Close modal
                            var modalEl = document.getElementById('walletRechargeModal');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            // Keep button disabled — recharge already processed
                            setTimeout(function () { location.reload(); }, 1500);
                        } else {
                            showAlert('danger', response.message || 'Recharge failed.');
                            isRecharging = false;
                            $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Recharge');
                        }
                    },
                    error: function (xhr) {
                        var msg = 'Error processing recharge.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        isRecharging = false;
                        $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Recharge');
                    }
                });
            });

        });
    </script>

</body>

</html>
