<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Manage Rate</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .nav-tabs .nav-link {
            font-weight: 600;
            font-size: 14px;
            padding: 10px 20px;
        }
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #007bff;
            color: #007bff;
        }
        .rate-input {
            width: 100px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 13px;
        }
        .rate-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .edit-icon {
            cursor: pointer;
            color: #007bff;
            font-size: 16px;
            margin-left: 6px;
        }
        .edit-icon:hover {
            color: #0056b3;
        }
        .save-icon {
            cursor: pointer;
            color: #28a745;
            font-size: 16px;
            margin-left: 6px;
        }
        .save-icon:hover {
            color: #1e7e34;
        }
        .cancel-icon {
            cursor: pointer;
            color: #dc3545;
            font-size: 16px;
            margin-left: 4px;
        }
        .cancel-icon:hover {
            color: #a71d2a;
        }
        .rate-display {
            font-weight: 500;
        }
        .customer-select-wrapper {
            max-width: 350px;
        }
        #customerRatesTable_wrapper {
            display: none;
        }
    </style>
</head>

<body>
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

        <div class="page-wrapper">
            <div class="content pb-0">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">Manage Rate</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>

                <!-- Nav Tabs -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-tabs mb-4" id="rateTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="default-rate-tab" data-bs-toggle="tab" data-bs-target="#default-rate-pane" type="button" role="tab">
                                            <i class="ti ti-template me-1"></i>Manage Default Rate
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="customer-rate-tab" data-bs-toggle="tab" data-bs-target="#customer-rate-pane" type="button" role="tab">
                                            <i class="ti ti-user-check me-1"></i>Customer Rate
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content" id="rateTabsContent">

                                    <!-- Default Rate Tab -->
                                    <div class="tab-pane fade show active" id="default-rate-pane" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="defaultRateTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Network</th>
                                                        <th>Service Code</th>
                                                        <th>Type</th>
                                                        <th>Method</th>
                                                        <th>TAT</th>
                                                        <th>Weight Start (gm)</th>
                                                        <th>Weight End (gm)</th>
                                                        <th>Zone No</th>
                                                        <th>Price (₹)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($defaultRates as $key => $rate)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $rate->service->network ?? '—' }}</td>
                                                        <td>{{ $rate->service->service_code ?? '—' }}</td>
                                                        <td>{{ $rate->service->type ?? '—' }}</td>
                                                        <td>{{ $rate->service->method ?? '—' }}</td>
                                                        <td>{{ $rate->service->tat ?? '—' }}</td>
                                                        <td>{{ $rate->wt_range_start }}</td>
                                                        <td>{{ $rate->wt_range_end }}</td>
                                                        <td>{{ $rate->zone_no }}</td>
                                                        <td>
                                                            <span class="rate-display" id="rate-display-{{ $rate->id }}">₹ {{ number_format($rate->price, 2) }}</span>
                                                            <input type="number" step="0.01" min="0" class="rate-input d-none" id="rate-input-{{ $rate->id }}" value="{{ $rate->price }}" data-rate-id="{{ $rate->id }}" data-original="{{ $rate->price }}">
                                                            <i class="ti ti-edit edit-icon" id="edit-icon-{{ $rate->id }}" onclick="editRate({{ $rate->id }})"></i>
                                                            <i class="ti ti-device-floppy save-icon d-none" id="save-icon-{{ $rate->id }}" onclick="saveRate({{ $rate->id }})"></i>
                                                            <i class="ti ti-x cancel-icon d-none" id="cancel-icon-{{ $rate->id }}" onclick="cancelEdit({{ $rate->id }})"></i>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Customer Rate Tab -->
                                    <div class="tab-pane fade" id="customer-rate-pane" role="tabpanel">
                                        <div class="row mb-3">
                                            <div class="col-md-4 customer-select-wrapper">
                                                <label class="form-label fw-bold">Select Customer</label>
                                                <select class="form-select" id="customerSelect">
                                                    <option value="">— Select Customer —</option>
                                                    @foreach($customers as $customer)
                                                        <option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->email ?? $customer->phone_number ?? 'N/A' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="table-responsive" id="customerRatesTable" style="display:none;">
                                            <table class="table table-hover" id="customerRatesDataTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Network</th>
                                                        <th>Service Code</th>
                                                        <th>Type</th>
                                                        <th>Method</th>
                                                        <th>TAT</th>
                                                        <th>Weight Start (gm)</th>
                                                        <th>Weight End (gm)</th>
                                                        <th>Zone No</th>
                                                        <th>Price (₹)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="customerRatesBody">
                                                    <!-- Loaded via AJAX -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="noCustomerSelected" class="text-center py-5 text-muted">
                                            <i class="ti ti-user-search fs-48 mb-3 d-block"></i>
                                            <p>Select a customer to view and edit their rates.</p>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
    <!-- Slimscroll JS -->
    <script src="{{ asset('assets/plugins/slimscroll/slimscroll.min.js') }}" type="text/javascript"></script>
    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
    <!-- Daterangepicker JS -->
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}" type="text/javascript"></script>
    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>
    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        var defaultRateTable;
        var customerRateTable;

        $(document).ready(function() {
            // Initialize Default Rate DataTable
            defaultRateTable = $('#defaultRateTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 50,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No default rates found.",
                }
            });

            // Initialize Customer Rate DataTable ONCE (empty)
            customerRateTable = $('#customerRatesDataTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 50,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No rates found for this customer.",
                },
                columns: [
                    { title: '#' },
                    { title: 'Network' },
                    { title: 'Service Code' },
                    { title: 'Type' },
                    { title: 'Method' },
                    { title: 'TAT' },
                    { title: 'Weight Start (gm)' },
                    { title: 'Weight End (gm)' },
                    { title: 'Zone No' },
                    { title: 'Price (₹)', orderable: false }
                ]
            });

            // Initialize Select2 for customer dropdown
            $('#customerSelect').select2({
                placeholder: '— Select Customer —',
                allowClear: true
            });

            // Customer selection change — delegated Select2 events for robustness
            $(document).on('select2:select', '#customerSelect', function(e) {
                var customerId = e.params.data.id;
                loadCustomerRates(customerId);
            });
            $(document).on('select2:clear', '#customerSelect', function() {
                customerRateTable.clear().draw();
                $('#customerRatesTable').hide();
                $('#noCustomerSelected').show();
            });
        });

        // Inline edit for Default Rate
        function editRate(rateId) {
            $('#rate-display-' + rateId).addClass('d-none');
            $('#edit-icon-' + rateId).addClass('d-none');
            $('#rate-input-' + rateId).removeClass('d-none').focus().select();
            $('#save-icon-' + rateId).removeClass('d-none');
            $('#cancel-icon-' + rateId).removeClass('d-none');
        }

        function cancelEdit(rateId) {
            var original = $('#rate-input-' + rateId).data('original');
            $('#rate-input-' + rateId).val(original).addClass('d-none');
            $('#rate-display-' + rateId).text('₹ ' + parseFloat(original).toFixed(2)).removeClass('d-none');
            $('#edit-icon-' + rateId).removeClass('d-none');
            $('#save-icon-' + rateId).addClass('d-none');
            $('#cancel-icon-' + rateId).addClass('d-none');
        }

        function saveRate(rateId) {
            var price = $('#rate-input-' + rateId).val();
            if (price === '' || isNaN(price) || parseFloat(price) < 0) {
                alert('Please enter a valid price.');
                return;
            }

            $.ajax({
                url: '{{ url("/admin/manage-rate/update") }}/' + rateId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    price: price
                },
                success: function(response) {
                    $('#rate-display-' + rateId).text('₹ ' + parseFloat(price).toFixed(2)).removeClass('d-none');
                    $('#rate-input-' + rateId).data('original', price).addClass('d-none');
                    $('#edit-icon-' + rateId).removeClass('d-none');
                    $('#save-icon-' + rateId).addClass('d-none');
                    $('#cancel-icon-' + rateId).addClass('d-none');
                },
                error: function() {
                    alert('Failed to update rate. Please try again.');
                }
            });
        }

        // Load customer rates via AJAX
        function loadCustomerRates(customerId) {
            $.ajax({
                url: '{{ url("/admin/manage-rate/get-customer-rates") }}',
                type: 'GET',
                data: { customer_id: customerId },
                success: function(response) {
                    var rows = [];

                    if (response.rates.length > 0) {
                        $.each(response.rates, function(index, rate) {
                            rows.push([
                                index + 1,
                                rate.service ? rate.service.network : '—',
                                rate.service ? rate.service.service_code : '—',
                                rate.service ? rate.service.type : '—',
                                rate.service ? rate.service.method : '—',
                                rate.service ? rate.service.tat : '—',
                                rate.wt_range_start,
                                rate.wt_range_end,
                                rate.zone_no,
                                '<span class="rate-display" id="cust-rate-display-' + rate.id + '">₹ ' + parseFloat(rate.price).toFixed(2) + '</span>' +
                                '<input type="number" step="0.01" min="0" class="rate-input d-none" id="cust-rate-input-' + rate.id + '" value="' + rate.price + '" data-rate-id="' + rate.id + '" data-original="' + rate.price + '">' +
                                '<i class="ti ti-edit edit-icon" id="cust-edit-icon-' + rate.id + '" onclick="editCustomerRate(' + rate.id + ')"></i>' +
                                '<i class="ti ti-device-floppy save-icon d-none" id="cust-save-icon-' + rate.id + '" onclick="saveCustomerRate(' + rate.id + ')"></i>' +
                                '<i class="ti ti-x cancel-icon d-none" id="cust-cancel-icon-' + rate.id + '" onclick="cancelCustomerEdit(' + rate.id + ')"></i>'
                            ]);
                        });
                    }

                    customerRateTable
                        .clear()
                        .rows.add(rows)
                        .draw();

                    $('#customerRatesTable').show();
                    $('#noCustomerSelected').hide();
                },
                error: function() {
                    alert('Failed to load customer rates.');
                }
            });
        }

        // Inline edit for Customer Rate
        function editCustomerRate(rateId) {
            $('#cust-rate-display-' + rateId).addClass('d-none');
            $('#cust-edit-icon-' + rateId).addClass('d-none');
            $('#cust-rate-input-' + rateId).removeClass('d-none').focus().select();
            $('#cust-save-icon-' + rateId).removeClass('d-none');
            $('#cust-cancel-icon-' + rateId).removeClass('d-none');
        }

        function cancelCustomerEdit(rateId) {
            var original = $('#cust-rate-input-' + rateId).data('original');
            $('#cust-rate-input-' + rateId).val(original).addClass('d-none');
            $('#cust-rate-display-' + rateId).text('₹ ' + parseFloat(original).toFixed(2)).removeClass('d-none');
            $('#cust-edit-icon-' + rateId).removeClass('d-none');
            $('#cust-save-icon-' + rateId).addClass('d-none');
            $('#cust-cancel-icon-' + rateId).addClass('d-none');
        }

        function saveCustomerRate(rateId) {
            var price = $('#cust-rate-input-' + rateId).val();
            if (price === '' || isNaN(price) || parseFloat(price) < 0) {
                alert('Please enter a valid price.');
                return;
            }

            $.ajax({
                url: '{{ url("/admin/manage-rate/update-customer") }}/' + rateId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    price: price
                },
                success: function(response) {
                    $('#cust-rate-display-' + rateId).text('₹ ' + parseFloat(price).toFixed(2)).removeClass('d-none');
                    $('#cust-rate-input-' + rateId).data('original', price).addClass('d-none');
                    $('#cust-edit-icon-' + rateId).removeClass('d-none');
                    $('#cust-save-icon-' + rateId).addClass('d-none');
                    $('#cust-cancel-icon-' + rateId).addClass('d-none');
                },
                error: function() {
                    alert('Failed to update customer rate. Please try again.');
                }
            });
        }
    </script>

</body>
</html>