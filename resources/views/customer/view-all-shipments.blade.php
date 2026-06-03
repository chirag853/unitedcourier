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
                                            <th>Invoice No.</th>
                                            <th>Invoice Date</th>
                                            <th>Amount</th>
                                            <th>Currency</th>
                                            <th>Incoterms</th>
                                            <th>Reference No.</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th class="text-center">Cancel</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $index => $invoice)
                                        <tr id="invoice-row-{{ $invoice->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
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