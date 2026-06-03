<!DOCTYPE html>
<html lang="en">

<head>

	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - View Customer List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
	<meta name="keywords" content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions">
	<meta name="author" content="Dreams Technologies">
	<meta name="robots" content="index, follow">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	   <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Choices CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/choices.js/public/assets/styles/choices.min.css') }}">

    <!-- Select2 CSS -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Quill CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/quill/quill.snow.css') }}">

    <!-- Mobile CSS-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/intltelinput/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/intltelinput/css/demo.css') }}">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .status-active {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .customer-name-link {
            color: #0d6efd;
            cursor: pointer;
            text-decoration: none;
        }
        .customer-name-link:hover {
            text-decoration: underline;
        }
        .table-actions .btn-icon {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 12px;
        }
        .dataTables_wrapper .dataTables_info {
            margin-top: 8px;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 8px;
        }
    </style>

</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar Start -->
            @include('admin.partials.header')
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
            @include('admin.partials.sidebar')
        <!-- Sidenav Menu End -->

        <!-- ========================
			Start Page Content
		========================= -->
         
        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">View Customer List <span class="badge badge-soft-primary ms-2">{{ count($shipments) }}</span></h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Customer List</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                    </div>
                </div>                
				<!-- End Page Header -->

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Shipments DataTable -->
                <div class="card border shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="shipmentsTable" class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>AWB Number</th>
                                        <th>Customer Name</th>
                                        <th>Customer Email</th>
                                        <th>Shipper Company</th>
                                        <th>Consignee</th>
                                        <th>Destination</th>
                                        <th>Invoice No.</th>
                                        <th>Amount</th>
                                        <th>Currency</th>
                                        <th>Status</th>
                                        <th>Delivery</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shipments as $index => $shipment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge bg-dark">{{ $shipment->awb_number ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="customer-name-link" title="Click to view details">
                                                {{ $shipment->first_name }} {{ $shipment->last_name }}
                                            </span>
                                        </td>
                                        <td>{{ $shipment->customer_email ?? 'N/A' }}</td>
                                        <td>{{ $shipment->shipper_company ?? 'N/A' }}</td>
                                        <td>{{ $shipment->consignee_name ?? 'N/A' }}</td>
                                        <td>
                                            {{ $shipment->consignee_city ?? $shipment->shipper_city ?? 'N/A' }}
                                            @if($shipment->consignee_state || $shipment->shipper_state)
                                                , {{ $shipment->consignee_state ?? $shipment->shipper_state }}
                                            @endif
                                        </td>
                                        <td>{{ $shipment->invoice_number ?? 'N/A' }}</td>
                                        <td>
                                            @if($shipment->invoice_amount)
                                                {{ number_format($shipment->invoice_amount, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $shipment->invoice_currency ?? 'N/A' }}</td>
                                        <td>
                                            @if($shipment->status === 'cancelled')
                                                <span class="status-cancelled">Cancelled</span>
                                            @else
                                                <span class="status-active">Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($shipment->delivery_type)
                                                <span class="badge bg-info">{{ $shipment->delivery_type }}</span>
                                                @if($shipment->delivery_type === 'Self' && $shipment->delivery_person_name)
                                                    <br><small class="text-muted">{{ $shipment->delivery_person_name }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($shipment->created_at)->format('d-m-Y') }}</td>
                                        <td class="table-actions">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning btn-icon" title="Assign Delivery" onclick="openAssignDelivery({{ $shipment->id }}, '{{ $shipment->delivery_type ?? '' }}', {{ $shipment->assigned_delivery_person ?? 'null' }}, '{{ $shipment->awb_number ?? '' }}')">
                                                <i class="ti ti-truck-delivery"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="14" class="text-center text-muted py-4">
                                            <i class="ti ti-package-off fs-24 d-block mb-2"></i>
                                            No shipments found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Shipments DataTable -->

            </div>
            <!-- End Content -->

        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Main Wrapper -->

    <!-- Assign Delivery Modal -->
    <div class="modal fade" id="assignDeliveryModal" tabindex="-1" aria-labelledby="assignDeliveryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignDeliveryModalLabel">
                        <i class="ti ti-truck-delivery me-1"></i> Assign Delivery
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignDeliveryForm">
                    <input type="hidden" name="shipment_id" id="assign_shipment_id" value="">
                    <div class="modal-body">
                        <div id="assignDeliveryAlert" class="alert d-none"></div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">AWB Number</label>
                            <p class="mb-0" id="assign_awb_display">-</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Delivery Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_ddu" value="DDU">
                                    <label class="form-check-label" for="delivery_ddu">
                                        <strong>DDU</strong><br>
                                        <small class="text-muted">Delivered Duty Unpaid</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_ddp" value="DDP">
                                    <label class="form-check-label" for="delivery_ddp">
                                        <strong>DDP</strong><br>
                                        <small class="text-muted">Delivered Duty Paid</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_self" value="Self">
                                    <label class="form-check-label" for="delivery_self">
                                        <strong>Self</strong><br>
                                        <small class="text-muted">Assign Delivery Person</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="deliveryPersonSection" style="display: none;">
                            <label class="form-label fw-semibold">Select Delivery Person <span class="text-danger">*</span></label>
                            <select class="form-select" name="delivery_person_id" id="delivery_person_id">
                                <option value="">-- Select Delivery Person --</option>
                                @foreach($deliveryPersons as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }} @if($person->mobile) ({{ $person->mobile }}) @endif</option>
                                @endforeach
                            </select>
                            @if($deliveryPersons->isEmpty())
                                <div class="text-warning mt-1">
                                    <small><i class="ti ti-alert-triangle"></i> No delivery persons found. Please add them in the admin users section.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="assignDeliveryBtn">
                            <i class="ti ti-device-floppy me-1"></i> Save Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>  

    <!-- Daterangepikcer JS -->
	<script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
	<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Choices Js -->	
    <script src="{{ asset('assets/plugins/choices.js/public/assets/scripts/choices.min.js') }}" type="text/javascript"></script>

    <!-- Mobile Input -->
    <script src="{{ asset('assets/plugins/intltelinput/js/intlTelInput.js') }}" type="text/javascript"></script>

    <!-- Quill JS -->
    <script src="{{ asset('assets/plugins/quill/quill.min.js') }}" type="text/javascript"></script>

	<!-- Simplebar JS -->
	<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

    <script data-cfasync="false">
        $(document).ready(function() {
            // Initialize DataTable
            $('#shipmentsTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    emptyTable: "No shipments found",
                    info: "Showing _START_ to _END_ of _TOTAL_ shipments",
                    infoEmpty: "Showing 0 to 0 of 0 shipments",
                    infoFiltered: "(filtered from _MAX_ total shipments)",
                    lengthMenu: "Show _MENU_ shipments",
                    search: "Search:",
                    zeroRecords: "No matching shipments found"
                }
            });

            // ===== Assign Delivery Modal Logic =====

            /**
             * Show/hide delivery person section when delivery type radio changes
             */
            $('input[name="delivery_type"]').on('change', function() {
                if ($(this).val() === 'Self') {
                    $('#deliveryPersonSection').slideDown(200);
                } else {
                    $('#deliveryPersonSection').slideUp(200);
                    $('#delivery_person_id').val('');
                }
            });

            /**
             * Handle form submission via AJAX
             */
            $('#assignDeliveryForm').on('submit', function(e) {
                e.preventDefault();

                // Basic client-side validation
                const deliveryType = $('input[name="delivery_type"]:checked').val();
                if (!deliveryType) {
                    showAssignDeliveryAlert('Please select a delivery type.', 'danger');
                    return;
                }
                if (deliveryType === 'Self' && !$('#delivery_person_id').val()) {
                    showAssignDeliveryAlert('Please select a delivery person for Self delivery.', 'danger');
                    return;
                }

                const $btn = $('#assignDeliveryBtn');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

                $.ajax({
                    url: '{{ route("admin.assign-delivery") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAssignDeliveryAlert(response.message, 'success');
                            $btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
                            // Reload page after a short delay to reflect changes
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showAssignDeliveryAlert(response.message || 'Something went wrong.', 'danger');
                            $btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                        }
                        showAssignDeliveryAlert(msg, 'danger');
                        $btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
                    }
                });
            });

            /**
             * Reset modal form when it is hidden
             */
            $('#assignDeliveryModal').on('hidden.bs.modal', function() {
                $('#assignDeliveryAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');
                $('input[name="delivery_type"]').prop('checked', false);
                $('#delivery_person_id').val('');
                $('#deliveryPersonSection').hide();
                $('#assignDeliveryBtn').prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
            });

            /**
             * Helper to show alerts inside the assign delivery modal
             */
            function showAssignDeliveryAlert(message, type) {
                const $alert = $('#assignDeliveryAlert');
                $alert.removeClass('d-none alert-success alert-danger').addClass('alert-' + type).html(message);
            }

        });

        /**
         * Open the Assign Delivery modal and pre-populate with current values.
         * @param {number} shipmentId
         * @param {string} currentType - Current delivery type (DDU, DDP, Self, or empty)
         * @param {number|null} currentPersonId - Current assigned delivery person ID
         * @param {string} awbNumber - AWB number for display
         */
        function openAssignDelivery(shipmentId, currentType, currentPersonId, awbNumber) {
            // Set shipment ID
            $('#assign_shipment_id').val(shipmentId);

            // Display AWB number
            $('#assign_awb_display').text(awbNumber || '-');

            // Pre-select the current delivery type radio
            $('input[name="delivery_type"]').prop('checked', false);
            if (currentType) {
                $('input[name="delivery_type"][value="' + currentType + '"]').prop('checked', true);
            }

            // Show/hide delivery person section based on current delivery type
            if (currentType === 'Self') {
                $('#deliveryPersonSection').show();
            } else {
                $('#deliveryPersonSection').hide();
            }

            // Pre-select the delivery person
            if (currentPersonId && currentPersonId !== 'null') {
                $('#delivery_person_id').val(currentPersonId);
            } else {
                $('#delivery_person_id').val('');
            }

            // Reset alert
            $('#assignDeliveryAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');

            // Open the modal
            $('#assignDeliveryModal').modal('show');
        }

        /**
         * View shipment details in modal.
         */
        function viewShipment(shipmentId) {
            // Since all data is already in the table, we can find it from the DOM
            // For a full detail view, we show a summary in the modal
            const table = $('#shipmentsTable').DataTable();
            const rows = table.rows().data();
            
            // Find the row index with this ID (we'll use a data attribute approach)
            // Simpler approach: redirect to a detail page or show basic info
            const row = $(`#shipmentsTable tbody tr`).eq(shipmentId); // simplified

            // Show modal with basic info from the row
            let html = `
                <div class="text-center py-3">
                    <i class="ti ti-file-description fs-40 text-primary mb-2 d-block"></i>
                    <h6>Shipment #${shipmentId}</h6>
                    <p class="text-muted mb-0">Detailed view will be available in a future update.</p>
                    <hr>
                    <p class="mb-0">AWB: <strong>${$(row).find('td:eq(1)').text()}</strong></p>
                    <p class="mb-0">Customer: <strong>${$(row).find('td:eq(2)').text()}</strong></p>
                    <p class="mb-0">Invoice: <strong>${$(row).find('td:eq(7)').text()}</strong></p>
                    <p class="mb-0">Amount: <strong>${$(row).find('td:eq(8)').text()} ${$(row).find('td:eq(9)').text()}</strong></p>
                    <p class="mb-0">Status: <strong>${$(row).find('td:eq(10)').text()}</strong></p>
                </div>
            `;

            $('#shipmentDetailBody').html(html);
            $('#shipmentDetailModalLabel').text(`Shipment Details - ${$(row).find('td:eq(1)').text()}`);
            $('#shipmentDetailModal').modal('show');
        }
    </script>

</body>

</html>