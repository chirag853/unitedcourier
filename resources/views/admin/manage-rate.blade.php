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
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css" />
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
        /* Truncate long zone name lists (comma-separated) to a single line
           with an ellipsis. The full list is shown in the native tooltip. */
        .zone-name-cell {
            max-width: 260px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
            vertical-align: top;
        }
        .zone-name-cell:hover {
            color: #007bff;
        }
        .customer-select-wrapper {
            max-width: 350px;
        }
        #customerRatesTable_wrapper {
            display: none;
        }
        .customer-rate-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .customer-rate-actions .btn {
            flex: 1 1 150px;
            min-height: 42px;
            white-space: nowrap;
        }
        /* Customer multi-select dropdown with checkboxes inside */
        .customer-dropdown {
            position: relative;
            width: 100%;
        }
        .customer-dropdown-toggle {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.375rem 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: #fff;
            text-align: left;
            cursor: pointer;
            font-size: 14px;
            min-height: 38px;
        }
        .customer-dropdown-toggle:hover {
            border-color: #007bff;
        }
        .customer-dropdown-toggle:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .customer-dropdown-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #6c757d;
        }
        .customer-dropdown-toggle i {
            margin-left: 8px;
            transition: transform 0.2s;
            color: #6c757d;
            flex-shrink: 0;
        }
        .customer-dropdown.open .customer-dropdown-toggle i {
            transform: rotate(180deg);
        }
        .customer-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 4px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1050;
            max-height: 320px;
            overflow: hidden;
            flex-direction: column;
        }
        .customer-dropdown.open .customer-dropdown-menu {
            display: flex;
        }
        .customer-dropdown-search {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            background: #fff;
            flex-shrink: 0;
        }
        .customer-dropdown-actions {
            padding: 6px 10px;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        .customer-dropdown-actions button {
            font-size: 12px;
            padding: 2px 8px;
        }
        .customer-dropdown-list {
            max-height: 220px;
            overflow-y: auto;
            padding: 6px 4px;
        }
        .customer-checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 2px;
            padding: 5px 8px;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
        }
        .customer-checkbox-item:hover {
            background: #f0f6ff;
        }
        .customer-checkbox-item input[type="checkbox"] {
            flex-shrink: 0;
            margin-right: 8px;
            cursor: pointer;
        }
        .customer-checkbox-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }
        .customer-checkbox-item:last-child { margin-bottom: 0; }
        .customer-checkbox-no-result {
            padding: 10px;
            text-align: center;
            color: #6c757d;
            font-size: 13px;
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
                        <button type="button" class="btn btn-success" id="defaultExportExcel">
                            <i class="ti ti-file-spreadsheet me-1"></i>Export Excel
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRateModal">
                            <i class="ti ti-plus me-1"></i>Add Rate
                        </button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                            <i class="ti ti-upload me-1"></i>Bulk Upload
                        </button>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>

                <!-- Nav Tabs -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="ti ti-alert-circle me-1"></i>{{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
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
                                        <!-- Filters -->
                                        <div class="row mb-3 g-2 align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Country</label>
                                                <select class="form-select" id="defaultCountryFilter">
                                                    <option value="">— All Countries —</option>
                                                    @foreach($destinations as $dest)
                                                        <option value="{{ $dest->country_code }}">{{ $dest->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Service</label>
                                                <select class="form-select" id="defaultServiceFilter">
                                                    <option value="">— All Services —</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 text-md-end">
                                                <label class="form-label fw-bold d-block">&nbsp;</label>
                                                <button type="button" class="btn btn-outline-secondary" id="defaultClearFilter">
                                                    <i class="ti ti-filter-x me-1"></i>Clear Filters
                                                </button>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="defaultRateTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Network</th>
                                                        <th>Country</th>
                                                        <th>Service Code</th>
                                                        <th>Method</th>
                                                        <th>TAT</th>
                                                        <th>Weight Start (KG)</th>
                                                        <th>Weight End (KG)</th>
                                                        <th>Zone No</th>
                                                        <th>Zone Category</th>
                                                        <th>Price</th>
                                                        <th>Default</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($defaultRates as $key => $rate)
                                                    <tr data-country="{{ $rate->service->country ?? '' }}" data-service-id="{{ $rate->service_id }}">
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $rate->service->network ?? '—' }}</td>
                                                        <td>{{ $rate->service->country ?? '—' }}</td>
                                                        <td>{{ $rate->service->service_code ?? '—' }}</td>
                                                        <td>{{ $rate->service->method ?? '—' }}</td>
                                                        <td>{{ $rate->service->tat ?? '—' }}</td>
                                                        <td>{{ $rate->wt_range_start }}</td>
                                                        <td>{{ $rate->wt_range_end }}</td>
                                                        <td>{{ $rate->zone_no }}</td>
                                                        @php
                                                            // Resolve zone category for this rate.
                                                            // courier_rates.zone_no matches zone.zone_number_testing.
                                                            // The service's country maps to a destination_id which
                                                            // indexes the pre-built $zoneLookup map.
                                                            $zoneCategory = '—';
                                                            $rateCountry = $rate->service->country ?? '';
                                                            $destId = $countryToDestinationId[strtolower(trim($rateCountry))] ?? null;
                                                            if ($destId && isset($zoneLookup[$destId])) {
                                                                $zoneNo = (int) $rate->zone_no;
                                                                if (isset($zoneLookup[$destId][$zoneNo])) {
                                                                    $zoneCategory = $zoneLookup[$destId][$zoneNo]['category'];
                                                                }
                                                            }
                                                        @endphp
                                                        <td>
                                                            @if($zoneCategory === 'state')
                                                                <span class="badge bg-info">State</span>
                                                            @elseif($zoneCategory === 'zipcode')
                                                                <span class="badge bg-warning">Zipcode</span>
                                                            @else
                                                                <span class="text-muted">{{ $zoneCategory }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($rate->is_default)
                                                                <span class="rate-display" id="rate-display-{{ $rate->id }}">{{ number_format($rate->price, 2) }}</span>
                                                                <input type="number" step="0.01" min="0" class="rate-input d-none" id="rate-input-{{ $rate->id }}" value="{{ $rate->price }}" data-rate-id="{{ $rate->id }}" data-original="{{ $rate->price }}">
                                                                <i class="ti ti-edit edit-icon" id="edit-icon-{{ $rate->id }}" onclick="editRate({{ $rate->id }})"></i>
                                                                <i class="ti ti-device-floppy save-icon d-none" id="save-icon-{{ $rate->id }}" onclick="saveRate({{ $rate->id }})"></i>
                                                                <i class="ti ti-x cancel-icon d-none" id="cancel-icon-{{ $rate->id }}" onclick="cancelEdit({{ $rate->id }})"></i>
                                                            @else
                                                                <span class="rate-display text-muted">{{ number_format($rate->price, 2) }}</span>
                                                                <i class="ti ti-lock text-muted ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Non-default rate — cannot be edited"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($rate->is_default)
                                                                <span class="badge bg-success">Yes</span>
                                                            @else
                                                                <span class="badge bg-secondary">No</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Customer Rate Tab -->
                                    <div class="tab-pane fade" id="customer-rate-pane" role="tabpanel">
                                        <div class="row mb-3 g-2 align-items-end">
                                            <div class="col-md-3 customer-select-wrapper">
                                                <label class="form-label fw-bold">Select Customers</label>
                                                <div class="customer-dropdown" id="customerDropdown">
                                                    <button type="button" class="customer-dropdown-toggle" id="customerDropdownToggle">
                                                        <span class="customer-dropdown-text" id="customerDropdownText">— Select Customers —</span>
                                                        <i class="ti ti-chevron-down"></i>
                                                    </button>
                                                    <div class="customer-dropdown-menu" id="customerDropdownMenu">
                                                        <div class="customer-dropdown-search">
                                                            <input type="text" class="form-control form-control-sm" id="customerDropdownSearch" placeholder="Search customers...">
                                                        </div>
                                                        <div class="customer-dropdown-actions">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="customerDropdownSelectAll">Select All</button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="customerDropdownClearAll">Clear</button>
                                                        </div>
                                                        <div class="customer-dropdown-list" id="customerCheckboxList">
                                                            @foreach($customers as $customer)
                                                                <label class="customer-checkbox-item" title="{{ $customer->first_name }} {{ $customer->last_name }}">
                                                                    <input type="checkbox" class="customer-checkbox" value="{{ $customer->id }}">
                                                                    <span class="customer-checkbox-label">{{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->email ?? $customer->phone_number ?? 'N/A' }})</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <small class="text-muted" id="selectedCustomerCount">0 customers selected</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Country</label>
                                                <select class="form-select" id="customerCountryFilter">
                                                    <option value="">— All Countries —</option>
                                                    @foreach($destinations as $dest)
                                                        <option value="{{ $dest->country_code }}">{{ $dest->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Service</label>
                                                <select class="form-select" id="customerServiceFilter">
                                                    <option value="">— All Services —</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-outline-secondary w-100" id="customerClearFilter" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Filters">
                                                    <i class="ti ti-filter-x"></i>
                                                </button>
                                            </div>
                                            <div class="col-12 text-md-end">
                                                <label class="form-label fw-bold d-block">&nbsp;</label>
                                                <div class="customer-rate-actions">
                                                    <button type="button" class="btn btn-success" id="customerExportExcel">
                                                        <i class="ti ti-file-spreadsheet me-1"></i>Export Excel
                                                    </button>
                                                    <button type="button" class="btn btn-primary" id="customerEndDateBtn" title="Change end date for ALL rates of the selected customer">
                                                        <i class="ti ti-calendar-event me-1"></i>End Date
                                                    </button>
                                                    <button type="button" class="btn btn-warning" id="customerNewRateBtn" title="Upload updated rates for the selected customer">
                                                        <i class="ti ti-refresh me-1"></i>Update New Rate
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive" id="customerRatesTable" style="display:none;">
                                            <table class="table table-hover" id="customerRatesDataTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Network</th>
                                                        <th>Country</th>
                                                        <th>Service Code</th>
                                                        <th>Method</th>
                                                        <th>TAT</th>
                                                        <th>Weight Start (gm)</th>
                                                        <th>Weight End (gm)</th>
                                                        <th>Zone No</th>
                                                        <th>Zone Category</th>
                                                        <th>Price</th>
                                                        <th>Default</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
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

    <!-- Add Rate Modal -->
    <div class="modal fade" id="addRateModal" tabindex="-1" aria-labelledby="addRateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRateModalLabel">
                        <i class="ti ti-plus me-1"></i>Add Default Rate
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addRateForm" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Country -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Country</label>
                                <select class="form-select" id="addRateCountry" required>
                                    <option value="">— Select Country —</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->country_code }}">{{ $dest->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">From the destinations table. Determines the available zones below.</small>
                            </div>
                            <!-- Service (filtered by the selected country) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Service <span class="text-danger">*</span></label>
                                <select class="form-select" id="addRateService" required>
                                    <option value="">— Select Country First —</option>
                                </select>
                                <small class="text-muted">Only services for the selected country are listed.</small>
                            </div>
                            <!-- Weight Start -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Weight Start (KG) <span class="text-danger">*</span></label>
                                <input type="number" step="0.001" min="0" class="form-control" id="addRateWtStart" name="wt_range_start" required>
                            </div>
                            <!-- Weight End -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Weight End (KG) <span class="text-danger">*</span></label>
                                <input type="number" step="0.001" min="0" class="form-control" id="addRateWtEnd" name="wt_range_end" required>
                            </div>
                            <!-- Zone No -->
                            <div class="col-md-4" id="addRateZoneSection">
                                <label class="form-label fw-bold">Zone No <span class="text-danger">*</span></label>
                                <select class="form-select" id="addRateZoneNo" name="zone_no" required>
                                    <option value="">— Select Country First —</option>
                                </select>
                                <!-- <small class="text-muted" id="addRateZoneHint"></small> -->
                            </div>
                            <!-- Price -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control" id="addRatePrice" name="price" required>
                            </div>
                            <!-- Fuel Charge -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fuel Charge (₹)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="addRateFuelCharge" name="fuel_charge" placeholder="0.00">
                            </div>
                            <!-- Fuel Percentage -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fuel %</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="addRateFuelPct" name="fuel_percentage" placeholder="0.00">
                            </div>
                            <!-- GST Percentage -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">GST %</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="addRateGstPct" name="gst_percentage" placeholder="0.00">
                            </div>
                            <!-- Surcharges (multiple) -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Surcharges</label>
                                <select class="form-select" id="addRateSurcharges" name="surcharge_id[]" multiple>
                                    @foreach($surcharges as $sur)
                                        <option value="{{ $sur->id }}">{{ $sur->name }} ({{ $sur->code }}) — ₹{{ number_format($sur->price, 2) }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Select one or more surcharges. Their prices are added to the total when the rate is calculated.</small>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 mb-0 py-2">
                            <i class="ti ti-info-circle me-1"></i>
                            This creates a <strong>default rate</strong> (applies to all customers). Fuel/GST fields are optional and default to 0.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addRateSubmitBtn">
                            <i class="ti ti-device-floppy me-1"></i>Save Rate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Upload Rate Modal -->
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkUploadModalLabel">
                        <i class="ti ti-file-spreadsheet me-1"></i>Bulk Upload Default Rates (Excel)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2" id="bulkUploadInstructions">
                        <i class="ti ti-info-circle me-1"></i>
                        Select a country and one or more zones, then download the sample. The Excel file places each selected zone horizontally with its own <strong>Price</strong>, <strong>Fuel Charge</strong>, <strong>Fuel %</strong>, and <strong>GST %</strong> columns. During upload, only the currently checked zones are imported; all other zone columns are skipped. Existing duplicate rates are also skipped.
                    </div>
                    <form id="bulkUploadForm" method="POST" action="{{ route('admin.manage-rate.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="without_zone" id="bulkWithoutZone" value="0">
                        <div class="row g-3">
                            <!-- Country (used to populate available zones) -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Country</label>
                                <select class="form-select" id="bulkCountry" name="country">
                                    <option value="">— All Countries —</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->country_code }}">{{ $dest->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Determines the available zones below.</small>
                            </div>
                            <!-- Service (required, filtered by selected country) -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Service <span class="text-danger">*</span></label>
                                <select class="form-select" id="bulkService" name="service_id" required>
                                    <option value="">— Select Country First —</option>
                                </select>
                                <small class="text-muted">Only services for the selected country are listed. Leave country as "All Countries" to see every service.</small>
                            </div>
                            <!-- Multiple zones used by sample download and upload -->
                            <div class="col-12 d-none" id="bulkZoneSection">
                                <div class="border rounded p-3 bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-bold mb-0">Select Zones <span class="text-danger">*</span></label>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-link p-0 me-2" id="bulkSelectAllZones">Select all</button>
                                            <button type="button" class="btn btn-sm btn-link p-0" id="bulkClearZones">Clear</button>
                                        </div>
                                    </div>
                                    <div class="row g-2" id="bulkZoneCheckboxes">
                                        <div class="col-12 text-muted">Select a country to view its zones.</div>
                                    </div>
                                    <small class="text-muted d-block mt-2">Only checked zones become horizontal columns in the sample and only those zones are imported from the uploaded file.</small>
                                </div>
                            </div>
                            <!-- File -->
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Excel File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="rate_file" accept=".xlsx,.xls,.csv" required>
                                <small class="text-muted d-block">Max 5 MB. .xlsx, .xls or .csv</small>
                            </div>
                            <!-- Download Sample -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold d-block">&nbsp;</label>
                                <a href="{{ route('admin.manage-rate.sample') }}" class="btn btn-outline-success w-100" id="bulkDownloadSampleBtn">
                                    <i class="ti ti-download me-1"></i>Download Sample
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="bulkUploadSubmitBtn">
                        <i class="ti ti-upload me-1"></i>Upload Rates
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update New Customer Rate Modal -->
    <div class="modal fade" id="updateNewRateModal" tabindex="-1" aria-labelledby="updateNewRateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateNewRateModalLabel">
                        <i class="ti ti-refresh me-1"></i>Update New Rate
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateNewRateForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Customers</label>
                                <input type="text" class="form-control" id="updateNewRateCustomer" readonly>
                                <input type="hidden" id="updateNewRateCustomerIds" name="customer_ids">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Country</label>
                                <select class="form-select" id="updateNewRateCountry">
                                    <option value="">— All Countries —</option>
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->country_code }}">{{ $dest->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Service</label>
                                <select class="form-select" id="updateNewRateService" name="service_id">
                                    <option value="">— All Services —</option>
                                </select>
                                <small class="text-muted">Leave All Services selected to update every service included in the downloaded Excel file.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control" id="updateNewRateStartDate" name="start_date" readonly required>
                                <small class="text-muted">Automatically set to one day after the customer's current end date.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="updateNewRateEndDate" name="end_date" required>
                                <small class="text-muted">End date cannot be earlier than the start date.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Upload Updated Rate <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="rate_file" accept=".xlsx,.xls,.csv" required>
                                <small class="text-muted">Upload the Excel file downloaded from this customer's rate table. Update the Price column, then upload it here.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="updateNewRateSubmitBtn">
                            <i class="ti ti-upload me-1"></i>Update Rate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
    <!-- JSZip (required for Excel export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.js"></script>
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @php
            $servicesForJs = $services->map(function($s) {
                return [
                    'id' => $s->id,
                    'network' => $s->network,
                    'method' => $s->method,
                    'service_code' => $s->service_code ?? '',
                    'country' => $s->country ?? '',
                ];
            })->values();
        @endphp

        var defaultRateTable;
        var customerRateTable;
        var loadedCustomerRates = [];
        // Currently selected customer ID + details (used by the end_date popup)
        var currentSelectedCustomerId = null;
        var selectedCustomerIds = [];
        var currentCustomerInfo = null;
        var currentCustomerEndDate = null;
        var allServices = @json($servicesForJs);
        var zoneLookup = @json($zoneLookup);
        // serviceZoneNumbers: service_id -> sorted list of zone numbers that
        // apply to that service for its destination (shared zones where
        // zone.service_id is NULL + the service's own service-specific zones).
        // Used by the Bulk Upload modal to decide whether the selected service
        // has any zones for the chosen country.
        var serviceZoneNumbers = @json($serviceZoneNumbers);
        var countryToDestinationId = @json($countryToDestinationId);
        // Maps a destination NAME to the matching courier_services.country
        // value (the short code, e.g. "US", "UK", "CA", "AUS"). Kept for
        // backward compatibility — all country <select> dropdowns now use
        // the destination country_code as the option value (matching the
        // courier_services.country short code), so this lookup is rarely
        // needed. Used to filter the service dropdown by country.
        var destNameToServiceCountry = @json($destNameToServiceCountry);

        // Resolve the courier_services.country value for a given country
        // <select> value. All country dropdowns (default filter, customer
        // filter, Add Rate modal, Bulk Upload modal) now use the
        // destination country_code as the option value, which is the same
        // short code stored in courier_services.country. This helper
        // handles both cases for safety:
        //   - If the value is a destination name (legacy), look it up in
        //     destNameToServiceCountry.
        //   - Otherwise (already a service-country string like "US"),
        //     return it as-is so the service dropdown filters correctly.
        function resolveServiceCountry(countryValue) {
            if (!countryValue) return '';
            if (destNameToServiceCountry[countryValue]) {
                return destNameToServiceCountry[countryValue];
            }
            return countryValue;
        }

        // Resolve zone name & category for a given (country, zoneNo) pair.
        // Returns { names: string, category: string } or null if not found.
        function getZoneInfo(country, zoneNo) {
            if (!country || zoneNo === null || zoneNo === undefined) return null;
            var destId = countryToDestinationId[(country || '').toLowerCase().trim()];
            if (!destId) return null;
            var zoneMap = zoneLookup[destId];
            if (!zoneMap) return null;
            return zoneMap[(parseInt(zoneNo, 10))] || null;
        }

        // Format a date string (YYYY-MM-DD) into a readable DD-MM-YYYY
        // format for display in the table and Excel export. Returns an
        // empty string if the input is empty/invalid.
        function formatDate(dateStr) {
            if (!dateStr) return '';
            var normalizedDate = String(dateStr).trim().substring(0, 10);
            var match = normalizedDate.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (match) {
                return match[3] + '-' + match[2] + '-' + match[1];
            }
            return String(dateStr);
        }

        // Populate a country <select> with unique countries from allServices
        function populateCountryDropdown(selectEl) {
            var countries = [];
            allServices.forEach(function(s) {
                if (s.country && countries.indexOf(s.country) === -1) {
                    countries.push(s.country);
                }
            });
            countries.sort();
            countries.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                selectEl.appendChild(opt);
            });
        }

        // Populate a service <select>, optionally filtered by country
        function populateServiceDropdown(selectEl, country) {
            while (selectEl.options.length > 1) {
                selectEl.remove(1);
            }
            allServices.forEach(function(s) {
                if (!country || s.country === country) {
                    var opt = document.createElement('option');
                    opt.value = s.id;
                    var label = s.network + ' — ' + s.method;
                    if (s.service_code) {
                        label += ' (' + s.service_code + ')';
                    }
                    opt.textContent = label;
                    selectEl.appendChild(opt);
                }
            });
        }

        $(document).ready(function() {
            // Initialize Default Rate DataTable
            // dom: 'frtip' — the built-in Buttons are NOT shown (no 'B'); they
            // are triggered programmatically by the custom Export buttons in
            // the filter row. This keeps the UI clean.
            defaultRateTable = $('#defaultRateTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 50,
                dom: 'frtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        title: 'Default Rates',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                            // Strip HTML from all exported cells so the Excel
                            // file contains plain text values (e.g. the Zone
                            // Category column renders a <span> badge — without
                            // stripping, those HTML tags would leak into the
                            // exported spreadsheet).
                            format: {
                                body: function(data, row, column) {
                                    if (column === 10) {
                                        // Price column — export only the numeric value.
                                        return $('<div>').html(data).find('.rate-display').first().text()
                                            || $('<div>').html(data).text().replace(/[^\d.]/g, '');
                                    }
                                    // All other columns (including Zone No at
                                    // index 8 and Zone Category at index 9) —
                                    // strip any HTML tags and return the plain
                                    // text.
                                    return $('<div>').html(data).text().trim();
                                }
                            }
                        }
                    }
                ],
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
                dom: 'frtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        title: 'Customer Rates',
                        // Show the selected customer's name and ID at the
                        // top of the exported Excel sheet (above the title).
                        messageTop: function() {
                            var info = currentCustomerInfo || {};
                            var name = info.full_name
                                || ((info.first_name || '') + ' ' + (info.last_name || '')).trim()
                                || '—';
                            var id = currentSelectedCustomerId || '—';
                            return 'Customer Name: ' + name + '    |    Customer ID: ' + id;
                        },
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                            format: {
                                body: function(data, row, column) {
                                    if (column === 8) {
                                        // Zone No column — extract the zone
                                        // number text (strip HTML/badges).
                                        return $('<div>').html(data).text().trim();
                                    }
                                    if (column === 10) {
                                        return $('<div>').html(data).find('.rate-display').first().text()
                                            || $('<div>').html(data).text().replace(/[^\d.]/g, '');
                                    }
                                    if (column === 11) {
                                        return $('<div>').html(data).text().trim();
                                    }
                                    if (column === 12) {
                                        return $('<div>').html(data).text().trim();
                                    }
                                    if (column === 13) {
                                        return $('<div>').html(data).text().trim();
                                    }
                                    return data;
                                }
                            }
                        }
                    }
                ],
                columns: [
                    { title: '#' },
                    { title: 'Network' },
                    { title: 'Country' },
                    { title: 'Service Code' },
                    { title: 'Method' },
                    { title: 'TAT' },
                    { title: 'Weight Start (KG)' },
                    { title: 'Weight End (KG)' },
                    { title: 'Zone No' },
                    { title: 'Zone Category' },
                    { title: 'Price', orderable: false },
                    { title: 'Default', orderable: false },
                    { title: 'Start Date', orderable: false },
                    { title: 'End Date', orderable: false }
                ]
            });

            // Customer checkbox selection. The first selected customer is used
            // for the on-screen preview; export and upload use every selected ID.
            $(document).on('change', '.customer-checkbox', function() {
                selectedCustomerIds = $('.customer-checkbox:checked').map(function() {
                    return String(this.value);
                }).get();
                var countText = selectedCustomerIds.length + (selectedCustomerIds.length === 1 ? ' customer selected' : ' customers selected');
                $('#selectedCustomerCount').text(countText);
                updateCustomerDropdownText();

                // Reset filters when customer changes
                document.getElementById('customerCountryFilter').value = '';
                populateServiceDropdown(document.getElementById('customerServiceFilter'), '');
                document.getElementById('customerServiceFilter').value = '';
                if (selectedCustomerIds.length) {
                    loadCustomerRates(selectedCustomerIds[0]);
                } else {
                    loadedCustomerRates = [];
                    currentSelectedCustomerId = null;
                    currentCustomerInfo = null;
                    currentCustomerEndDate = null;
                    customerRateTable.clear().draw();
                    $('#customerRatesTable').hide();
                    $('#noCustomerSelected').show();
                }
            });

            // === Customer dropdown (checkboxes inside a dropdown) ===
            // Toggle open/close when the dropdown button is clicked.
            $('#customerDropdownToggle').on('click', function(e) {
                e.stopPropagation();
                $('#customerDropdown').toggleClass('open');
            });

            // Close the dropdown when clicking outside of it.
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#customerDropdown').length) {
                    $('#customerDropdown').removeClass('open');
                }
            });

            // Prevent clicks inside the dropdown menu from bubbling up
            // and closing the dropdown (the toggle handles its own stop).
            $('#customerDropdownMenu').on('click', function(e) {
                e.stopPropagation();
            });

            // Update the dropdown toggle text to reflect the current selection.
            function updateCustomerDropdownText() {
                var n = selectedCustomerIds.length;
                var textEl = $('#customerDropdownText');
                if (n === 0) {
                    textEl.text('— Select Customers —').css('color', '#6c757d');
                } else if (n === 1) {
                    var checked = $('.customer-checkbox:checked').first();
                    var label = checked.closest('.customer-checkbox-item').find('.customer-checkbox-label').text();
                    textEl.text(label || '1 customer selected').css('color', '#495057');
                } else {
                    textEl.text(n + ' customers selected').css('color', '#495057');
                }
            }

            // Search filter for the customer dropdown — hides non-matching rows.
            $('#customerDropdownSearch').on('input', function() {
                var term = $(this).val().toLowerCase().trim();
                var visibleCount = 0;
                $('#customerCheckboxList .customer-checkbox-item').each(function() {
                    var label = $(this).find('.customer-checkbox-label').text().toLowerCase();
                    var match = label.indexOf(term) !== -1;
                    $(this).toggle(match);
                    if (match) visibleCount++;
                });
                var noResult = $('#customerCheckboxNoResult');
                if (visibleCount === 0) {
                    if (noResult.length === 0) {
                        $('#customerCheckboxList').append('<div class="customer-checkbox-no-result" id="customerCheckboxNoResult">No customers match your search.</div>');
                    }
                } else {
                    noResult.remove();
                }
            });

            // Select All / Clear All buttons inside the dropdown. These set
            // the checkboxes then trigger a single change so the rates reload
            // happens only once (the change handler rebuilds the full list of
            // checked IDs from every checkbox regardless of visibility).
            $('#customerDropdownSelectAll').on('click', function() {
                $('#customerCheckboxList .customer-checkbox-item').filter(':visible').find('.customer-checkbox').prop('checked', true);
                $('.customer-checkbox').first().trigger('change');
            });
            $('#customerDropdownClearAll').on('click', function() {
                $('.customer-checkbox').prop('checked', false);
                $('.customer-checkbox').first().trigger('change');
            });

            // === Country & Service Filters Setup ===

            // The country filter dropdowns (defaultCountryFilter &
            // customerCountryFilter) are rendered server-side from the
            // destinations table, so no JS population is needed here.

            // Populate service dropdowns (all services initially)
            populateServiceDropdown(document.getElementById('defaultServiceFilter'), '');
            populateServiceDropdown(document.getElementById('customerServiceFilter'), '');

            // DataTables custom search plugin for Default Rate table
            // (global plugin — guarded by table ID so it only affects defaultRateTable)
            $.fn.dataTable.ext.search.push(function(settings, searchData, index) {
                if (settings.nTable.id !== 'defaultRateTable') {
                    return true;
                }
                var countryFilter = document.getElementById('defaultCountryFilter').value;
                var serviceFilter = document.getElementById('defaultServiceFilter').value;
                if (!countryFilter && !serviceFilter) {
                    return true;
                }
                var rowNode = settings.aoData[index].nTr;
                if (!rowNode) return true;
                var rowCountry = rowNode.getAttribute('data-country') || '';
                var rowServiceId = rowNode.getAttribute('data-service-id') || '';
                if (countryFilter && rowCountry !== countryFilter) return false;
                if (serviceFilter && rowServiceId !== serviceFilter) return false;
                return true;
            });

            // Default rate filter change handlers
            $('#defaultCountryFilter').on('change', function() {
                populateServiceDropdown(document.getElementById('defaultServiceFilter'), this.value);
                document.getElementById('defaultServiceFilter').value = '';
                defaultRateTable.draw();
            });
            $('#defaultServiceFilter').on('change', function() {
                defaultRateTable.draw();
            });
            $('#defaultClearFilter').on('click', function() {
                document.getElementById('defaultCountryFilter').value = '';
                populateServiceDropdown(document.getElementById('defaultServiceFilter'), '');
                document.getElementById('defaultServiceFilter').value = '';
                defaultRateTable.draw();
            });

            // Customer rate filter change handlers
            $('#customerCountryFilter').on('change', function() {
                populateServiceDropdown(document.getElementById('customerServiceFilter'), this.value);
                document.getElementById('customerServiceFilter').value = '';
                renderFilteredCustomerRates();
            });
            $('#customerServiceFilter').on('change', function() {
                renderFilteredCustomerRates();
            });
            $('#customerClearFilter').on('click', function() {
                document.getElementById('customerCountryFilter').value = '';
                populateServiceDropdown(document.getElementById('customerServiceFilter'), '');
                document.getElementById('customerServiceFilter').value = '';
                renderFilteredCustomerRates();
            });

            // === Export Excel button handlers ===
            // The custom buttons in the filter rows trigger the DataTables
            // built-in excelHtml5 button (index 0), which exports only the
            // rows currently visible after filtering/searching.
            $('#defaultExportExcel').on('click', function() {
                defaultRateTable.button(0).trigger();
            });
            $('#customerExportExcel').on('click', function() {
                if (selectedCustomerIds.length === 0) {
                    showAlert('Please select a customer first to export their rates.', 'warning');
                    return;
                }
                var params = new URLSearchParams();
                selectedCustomerIds.forEach(function(id) { params.append('customer_ids[]', id); });
                var country = $('#customerCountryFilter').val();
                var serviceId = $('#customerServiceFilter').val();
                if (country) params.set('country', country);
                if (serviceId) params.set('service_id', serviceId);
                window.location.href = '{{ route("admin.manage-rate.export-customer-rates") }}?' + params.toString();
            });

            function addDaysToDate(dateValue, days) {
                var parts = String(dateValue || '').substring(0, 10).split('-');
                var date = parts.length === 3 && parts[0].length === 4
                    ? new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]))
                    : new Date();
                date.setHours(0, 0, 0, 0);
                date.setDate(date.getDate() + days);
                return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
            }

            function openUpdateNewRateModal() {
                if (selectedCustomerIds.length === 0 || loadedCustomerRates.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Customer Selected',
                        text: 'Please select a customer first.',
                    });
                    return;
                }

                var info = currentCustomerInfo || {};
                var customerName = info.full_name || ((info.first_name || '') + ' ' + (info.last_name || '')).trim();
                var selectedCountry = document.getElementById('customerCountryFilter').value || '';
                var selectedService = document.getElementById('customerServiceFilter').value || '';

                $('#updateNewRateCustomer').val(selectedCustomerIds.length + ' customers selected');
                $('#updateNewRateCustomerIds').val(selectedCustomerIds.join(','));
                $('#updateNewRateCountry').val(selectedCountry);
                populateServiceDropdown(document.getElementById('updateNewRateService'), selectedCountry);
                $('#updateNewRateService').val(selectedService);

                var startDate = addDaysToDate(currentCustomerEndDate || new Date().toISOString().slice(0, 10), 1);
                $('#updateNewRateStartDate').val(startDate);
                $('#updateNewRateEndDate').attr('min', startDate).val(addDaysToDate(startDate, 365));
                $('#updateNewRateModal').modal('show');
            }

            $('#customerNewRateBtn').on('click', openUpdateNewRateModal);
            $('#updateNewRateCountry').on('change', function() {
                populateServiceDropdown(document.getElementById('updateNewRateService'), this.value);
                $('#updateNewRateService').val('').trigger('change');
            });

            $('#updateNewRateForm').on('submit', function(event) {
                event.preventDefault();
                var startDate = $('#updateNewRateStartDate').val();
                var endDate = $('#updateNewRateEndDate').val();
                if (!endDate || endDate < startDate) {
                    Swal.fire({ icon: 'warning', title: 'Invalid End Date', text: 'End date cannot be earlier than the start date.' });
                    return;
                }

                var formData = new FormData(this);
                formData.delete('customer_ids');
                selectedCustomerIds.forEach(function(id) { formData.append('customer_ids[]', id); });
                var submitButton = $('#updateNewRateSubmitBtn');
                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Updating...');

                $.ajax({
                    url: '{{ route("admin.manage-rate.update-new-rate") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#updateNewRateModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Rates Updated',
                            text: response.message || 'Customer rates updated successfully.',
                            timer: 2200,
                            showConfirmButton: false
                        });
                        loadCustomerRates(currentSelectedCustomerId);
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON || {};
                        var message = response.message || 'The rate file could not be processed.';
                        if (response.errors) {
                            message = Object.values(response.errors).flat().join('\n');
                        }
                        Swal.fire({ icon: 'error', title: 'Update Failed', text: message });
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).html('<i class="ti ti-upload me-1"></i>Update Rate');
                    }
                });
            });

            // === Common End Date button handler ===
            // Opens the end_date popup for ALL selected customers.
            // Shows each customer's details (ID, Name, Email, Phone) in the popup
            // and updates the end_date for all of them at once.
            $('#customerEndDateBtn').on('click', function() {
                if (selectedCustomerIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Customer Selected',
                        text: 'Please select at least one customer to change their end date.',
                    });
                    return;
                }
                openEndDatePopupForMultiple(selectedCustomerIds);
            });

            // === Add Rate Modal Setup ===
            // The country dropdown in the Add Rate modal is rendered server-side
            // from the destinations table (so it shows ALL countries, including
            // those added via the Add Country page — not just the ones that
            // already have courier services). No JS population needed here.

            // The Service dropdown is FILTERED by the selected country: only
            // courier services whose `country` matches the selected country are
            // listed. On modal open no country is selected yet, so we start with
            // an empty service dropdown (the placeholder only).
            populateServiceDropdown(document.getElementById('addRateService'), '');

            // Initialize Select2 on the Zone No dropdown so the admin can
            // type to filter zone suggestions. The dropdown is rendered
            // inside the modal (dropdownParent) to avoid Bootstrap 5 modal
            // focus-stealing conflicts that break the search input.
            initZoneSelect2(document.getElementById('addRateZoneNo'), '— Select Country First —');

            // Initialize Select2 on the Surcharge multi-select dropdown so the
            // admin can type to filter and pick multiple surcharges.
            $('#addRateSurcharges').select2({
                placeholder: '— Select Surcharges —',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addRateModal')
            });

            // When the country changes, repopulate BOTH the service dropdown
            // (only services for that country) and the zone dropdown (zone
            // numbers with their names for the selected country). The service
            // selection is cleared so a stale service from a previous country
            // is never submitted.
            $('#addRateCountry').on('change', function() {
                var country = this.value;
                var serviceCountry = resolveServiceCountry(country);
                populateServiceDropdown(document.getElementById('addRateService'), serviceCountry);
                document.getElementById('addRateService').value = '';
                populateZoneDropdown(document.getElementById('addRateZoneNo'), country);
                updateAddRateZoneHint();
            });

            // Populate the Zone No dropdown with ONLY the zones that actually
            // exist for the given country (from the zone table). If the
            // country has no zones at all, the entire Zone No section is
            // hidden so the admin is not prompted to pick a zone. If no
            // country is selected, the section is shown with a placeholder.
            function populateZoneDropdown(selectEl, country) {
                var zoneSection = document.getElementById('addRateZoneSection');

                // Remove all existing options
                while (selectEl.options.length > 0) {
                    selectEl.remove(0);
                }

                if (!country) {
                    var ph = document.createElement('option');
                    ph.value = '';
                    ph.textContent = '— Select Country First —';
                    selectEl.appendChild(ph);
                    initZoneSelect2(selectEl, '— Select Country First —');
                    // Show the section so the admin knows to pick a country,
                    // but zone is not required until a country with zones is chosen.
                    zoneSection.style.display = '';
                    selectEl.removeAttribute('required');
                    return;
                }

                // Look up the destination_id for this country
                var destId = countryToDestinationId[(country || '').toLowerCase().trim()];
                var zoneMap = destId ? zoneLookup[destId] : null;

                // Collect only the zone numbers that actually exist for this
                // country. zoneMap is an object keyed by zone number (as a
                // string from JSON), so parse and sort the numeric keys.
                var zoneKeys = [];
                if (zoneMap) {
                    zoneKeys = Object.keys(zoneMap)
                        .map(function(k) { return parseInt(k, 10); })
                        .filter(function(n) { return !isNaN(n); })
                        .sort(function(a, b) { return a - b; });
                }

                // If the country has no zones at all, hide the entire Zone No
                // section so the admin is not asked to pick a zone.
                if (zoneKeys.length === 0) {
                    zoneSection.style.display = 'none';
                    // The country has no zones, so zone_no is not required.
                    // Dropping the required attribute prevents the browser from
                    // failing validation on a hidden, non-focusable select
                    // ("An invalid form control with name='zone_no' is not focusable").
                    selectEl.removeAttribute('required');
                    // Clear any previous selection and reset Select2.
                    var none = document.createElement('option');
                    none.value = '';
                    none.textContent = '— No zones —';
                    selectEl.appendChild(none);
                    initZoneSelect2(selectEl, '— No zones —');
                    return;
                }

                // Show the section (it may have been hidden for a previous
                // country that had no zones). Zones exist, so re-require it.
                zoneSection.style.display = '';
                selectEl.setAttribute('required', 'required');

                var placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '— Select Zone —';
                selectEl.appendChild(placeholder);

                // Add ONE option per zone number that exists for this country.
                // If Australia has 4 zones, exactly 4 options appear. Each
                // option is labeled "Zone N (count entries)" so the admin can
                // see how many states/postal codes belong to that zone. The
                // option value is the zone number, which is what gets submitted.
                zoneKeys.forEach(function(z) {
                    var info = zoneMap[z] || {};
                    var count = info.count || 0;
                    var category = info.category || 'state';
                    var label = 'Zone ' + z;
                    if (count > 0) {
                        label += ' (' + count + ' ' + (category === 'zipcode' ? 'Records Avl' : (category === 'city' ? 'cities' : 'states')) + ')';
                    }
                    var opt = document.createElement('option');
                    opt.value = z;
                    opt.textContent = label;
                    selectEl.appendChild(opt);
                });
                initZoneSelect2(selectEl, '— Select Zone —');
            }

            // (Re)initialize Select2 on a zone dropdown so the admin can
            // type to filter zone suggestions. Select2 must be destroyed
            // first if it was already initialized, otherwise duplicate
            // search boxes appear. The dropdown is rendered inside the
            // Add Rate modal (dropdownParent) to prevent Bootstrap 5's
            // modal focus enforcement from breaking the search input.
            function initZoneSelect2(selectEl, placeholderText) {
                if ($(selectEl).hasClass('select2-hidden-accessible')) {
                    $(selectEl).select2('destroy');
                }
                $(selectEl).select2({
                    placeholder: placeholderText,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#addRateModal')
                });
            }

            // When the zone number changes, show a hint describing the zone
            // (state names for state-category zones, zipcode count otherwise).
            // Use delegated Select2 events because initZoneSelect2 destroys
            // and re-creates the Select2 instance on every country change,
            // which would drop a directly-bound handler.
            $(document).on('select2:select', '#addRateZoneNo', updateAddRateZoneHint);
            $(document).on('select2:clear', '#addRateZoneNo', function() {
                document.getElementById('addRateZoneHint').textContent = '';
            });

            function updateAddRateZoneHint() {
                var hintEl = document.getElementById('addRateZoneHint');
                var country = document.getElementById('addRateCountry').value;
                var zoneNo = document.getElementById('addRateZoneNo').value;
                if (!country || zoneNo === '') {
                    hintEl.textContent = '';
                    return;
                }
                var info = getZoneInfo(country, parseInt(zoneNo, 10));
                if (info) {
                    hintEl.textContent = info.names + ' (' + info.category + ')';
                } else {
                    hintEl.textContent = 'No zone data for this country/zone.';
                }
            }

            // Handle Add Rate form submission via AJAX.
            $('#addRateForm').on('submit', function(e) {
                e.preventDefault();
                var submitBtn = document.getElementById('addRateSubmitBtn');
                var serviceId = document.getElementById('addRateService').value;
                var wtStart = document.getElementById('addRateWtStart').value;
                var wtEnd = document.getElementById('addRateWtEnd').value;
                var zoneSection = document.getElementById('addRateZoneSection');
                var zoneSectionVisible = zoneSection && zoneSection.style.display !== 'none';
                // When the Zone No section is hidden the country has no zones,
                // so submit an empty zone_no. The backend stores it as null
                // (a zone-independent default rate) instead of forcing zone '0'.
                var zoneNo = zoneSectionVisible ? document.getElementById('addRateZoneNo').value : '';
                var price = document.getElementById('addRatePrice').value;

                // Client-side validation
                if (!serviceId) { showAlert('Please select a service.', 'warning'); return; }
                if (!wtStart || !wtEnd || parseFloat(wtEnd) <= parseFloat(wtStart)) {
                    showAlert('Weight End must be greater than Weight Start.', 'warning');
                    return;
                }
                if (zoneSectionVisible && zoneNo === '') { showAlert('Please select a zone number.', 'warning'); return; }
                if (!price || parseFloat(price) < 0) { showAlert('Please enter a valid price.', 'warning'); return; }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

                $.ajax({
                    url: '{{ url("/admin/manage-rate/add") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        service_id: serviceId,
                        wt_range_start: wtStart,
                        wt_range_end: wtEnd,
                        zone_no: zoneNo,
                        price: price,
                        fuel_charge: document.getElementById('addRateFuelCharge').value || 0,
                        fuel_percentage: document.getElementById('addRateFuelPct').value || 0,
                        gst_percentage: document.getElementById('addRateGstPct').value || 0,
                        surcharge_id: $('#addRateSurcharges').val() || [],
                    },
                    success: function(response) {
                        showAlert(response.message || 'Rate added successfully.', 'success', function() {
                            // Close modal and reset form
                            var modal = bootstrap.Modal.getInstance(document.getElementById('addRateModal'));
                            if (modal) modal.hide();
                            document.getElementById('addRateForm').reset();
                            // Reload the page so the new rate appears in the table
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        var msg = 'Failed to add rate. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var errs = xhr.responseJSON.errors;
                            msg = Object.values(errs).flat().join('\n');
                        }
                        showAlert(msg, 'error');
                    },
                    complete: function() {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Save Rate';
                    }
                });
            });

            // Reset the service & zone dropdowns when the modal is hidden so
            // the next open starts clean. populateZoneDropdown re-inits
            // Select2 with a fresh placeholder, clearing any selection.
            document.getElementById('addRateModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('addRateForm').reset();
                populateServiceDropdown(document.getElementById('addRateService'), '');
                populateZoneDropdown(document.getElementById('addRateZoneNo'), '');
                document.getElementById('addRateZoneHint').textContent = '';
            });

            // ===== Bulk Upload Rate Modal =====

            function getBulkCountryZones(country) {
                var destId = countryToDestinationId[(country || '').toLowerCase().trim()];
                var zoneMap = destId ? zoneLookup[destId] : null;
                var zoneKeys = zoneMap ? Object.keys(zoneMap)
                    .map(function(key) { return parseInt(key, 10); })
                    .filter(function(zone) { return !isNaN(zone); })
                    .sort(function(a, b) { return a - b; }) : [];

                return { map: zoneMap || {}, keys: zoneKeys };
            }

            // Return the sorted zone numbers that apply to the selected service
            // for the selected country. Uses the server-built serviceZoneNumbers
            // map (service_id -> zones), which already merges shared zones
            // (zone.service_id IS NULL) with the service's own service-specific
            // zones for its destination. A selected service that is absent from
            // the map has NO zones for the country → the without-zone format is
            // used. When no service is selected, fall back to all of the
            // country's zones (existing behaviour).
            function getBulkServiceZoneNumbers(country, serviceId) {
                if (serviceId) {
                    return (serviceZoneNumbers[serviceId] || []).slice();
                }
                return getBulkCountryZones(country).keys;
            }

            function populateBulkZoneCheckboxes(country, serviceId) {
                var zoneSection = document.getElementById('bulkZoneSection');
                var container = document.getElementById('bulkZoneCheckboxes');
                var withoutZoneInput = document.getElementById('bulkWithoutZone');
                var instructions = document.getElementById('bulkUploadInstructions');
                var defaultInstructions = '<i class="ti ti-info-circle me-1"></i>Select a country and one or more zones, then download the sample. The Excel file places each selected zone horizontally with its own <strong>Price</strong>, <strong>Fuel Charge</strong>, <strong>Fuel %</strong>, and <strong>GST %</strong> columns. During upload, only the currently checked zones are imported; all other zone columns are skipped. Existing duplicate rates are also skipped.';
                container.innerHTML = '';
                withoutZoneInput.value = '0';
                instructions.innerHTML = defaultInstructions;

                if (!country) {
                    zoneSection.classList.add('d-none');
                    container.innerHTML = '<div class="col-12 text-muted">Select a country to view its zones.</div>';
                    return;
                }

                var zoneData = getBulkCountryZones(country);
                var zoneMap = zoneData.map;
                var zoneKeys = getBulkServiceZoneNumbers(country, serviceId);

                zoneSection.classList.remove('d-none');
                if (!zoneKeys.length) {
                    withoutZoneInput.value = '1';
                    container.innerHTML = '<div class="col-12 text-success">' +
                        (serviceId
                            ? 'This service has no configured zones for the selected country. The sample and uploaded rates will use the without-zone format.'
                            : 'This country has no configured zones. The sample and uploaded rates will use the without-zone format.') +
                        '</div>';
                    instructions.innerHTML = '<i class="ti ti-info-circle me-1"></i>' +
                        (serviceId
                            ? 'The selected service has no zones for this country. Download the without-zone sample containing <strong>Price</strong>, <strong>Fuel Charge</strong>, <strong>Fuel %</strong>, and <strong>GST %</strong> columns. Uploaded rates are saved without a zone, and existing duplicate rates are skipped.'
                            : 'This country has no configured zones. Download the without-zone sample containing <strong>Price</strong>, <strong>Fuel Charge</strong>, <strong>Fuel %</strong>, and <strong>GST %</strong> columns. Uploaded rates are saved without a zone, and existing duplicate rates are skipped.');
                    return;
                }

                instructions.innerHTML = serviceId
                    ? '<i class="ti ti-info-circle me-1"></i>This service has configured zones for the selected country. Select one or more zones, then download the sample. Each selected zone receives its own <strong>Price</strong>, <strong>Fuel Charge</strong>, <strong>Fuel %</strong>, and <strong>GST %</strong> columns. Only checked zones are imported, and existing duplicate rates are skipped.'
                    : '<i class="ti ti-info-circle me-1"></i>Select one or more zones, then download the sample. Each selected zone receives its own <strong>Price</strong>, <strong>Fuel Charge</strong>, <strong>Fuel %</strong>, and <strong>GST %</strong> columns. Only checked zones are imported, and existing duplicate rates are skipped.';

                zoneKeys.forEach(function(zone) {
                    var info = zoneMap[zone] || {};
                    var category = info.category === 'zipcode' ? 'records' : (info.category === 'city' ? 'cities' : 'states');
                    var label = 'Zone ' + zone + (info.count ? ' (' + info.count + ' ' + category + ')' : '');
                    container.insertAdjacentHTML('beforeend',
                        '<div class="col-md-4 col-sm-6">' +
                        '<div class="form-check border rounded bg-white px-4 py-2">' +
                        '<input class="form-check-input bulk-zone-checkbox" type="checkbox" name="zone_nos[]" value="' + zone + '" id="bulkZone' + zone + '">' +
                        '<label class="form-check-label" for="bulkZone' + zone + '">' + label + '</label>' +
                        '</div></div>');
                });
            }

            function getCheckedBulkZones() {
                return Array.from(document.querySelectorAll('.bulk-zone-checkbox:checked')).map(function(checkbox) {
                    return checkbox.value;
                });
            }

            // When the bulk modal is shown, start with an empty service
            // dropdown (placeholder only). Services are filtered by the
            // selected country, so they appear once a country is chosen.
            document.getElementById('bulkUploadModal').addEventListener('shown.bs.modal', function() {
                populateServiceDropdown(document.getElementById('bulkService'), '');
                populateBulkZoneCheckboxes(document.getElementById('bulkCountry').value, document.getElementById('bulkService').value);
            });

            // Country change → repopulate BOTH the service dropdown (only
            // services for that country) and the zone dropdown. The service
            // selection is cleared so a stale service from a previous country
            // is never submitted. When no country is selected ("All Countries"),
            // all services are listed.
            document.getElementById('bulkCountry').addEventListener('change', function() {
                var country = this.value;
                var serviceCountry = resolveServiceCountry(country);
                populateServiceDropdown(document.getElementById('bulkService'), serviceCountry);
                document.getElementById('bulkService').value = '';
                populateBulkZoneCheckboxes(country, '');
            });

            // Service change → re-evaluate the zone section for the selected
            // service. If the service has no zones for the chosen country the
            // without-zone format is used and the zone section is hidden.
            document.getElementById('bulkService').addEventListener('change', function() {
                populateBulkZoneCheckboxes(document.getElementById('bulkCountry').value, this.value);
            });

            document.getElementById('bulkSelectAllZones').addEventListener('click', function() {
                document.querySelectorAll('.bulk-zone-checkbox').forEach(function(checkbox) { checkbox.checked = true; });
            });
            document.getElementById('bulkClearZones').addEventListener('click', function() {
                document.querySelectorAll('.bulk-zone-checkbox').forEach(function(checkbox) { checkbox.checked = false; });
            });

            // Download Sample: append selected service_id and zone_no so the
            // sample includes existing rates for that service/zone.
            document.getElementById('bulkDownloadSampleBtn').addEventListener('click', function(e) {
                e.preventDefault();
                var country = document.getElementById('bulkCountry').value;
                var serviceId = document.getElementById('bulkService').value;
                var zones = getCheckedBulkZones();
                var withoutZone = document.getElementById('bulkWithoutZone').value === '1';
                if (!country) { showAlert('Please select a country first.', 'warning'); return; }
                if (!serviceId) { showAlert('Please select a service.', 'warning'); return; }
                if (!withoutZone && !zones.length) { showAlert('Please select at least one zone.', 'warning'); return; }

                var params = new URLSearchParams();
                params.append('service_id', serviceId);
                params.append('country', country);
                params.append('without_zone', withoutZone ? '1' : '0');
                zones.forEach(function(zone) { params.append('zone_nos[]', zone); });
                window.location.href = "{{ route('admin.manage-rate.sample') }}" + '?' + params.toString();
            });

            // Submit the bulk upload form (regular POST — the controller
            // redirects back with flash success/error messages). Show a
            // loading state while the file is being processed.
            document.getElementById('bulkUploadSubmitBtn').addEventListener('click', function() {
                var form = document.getElementById('bulkUploadForm');
                var serviceId = document.getElementById('bulkService').value;
                var zones = getCheckedBulkZones();
                var withoutZone = document.getElementById('bulkWithoutZone').value === '1';
                var fileInput = form.querySelector('input[name="rate_file"]');
                if (!serviceId) { showAlert('Please select a service.', 'warning'); return; }
                if (!withoutZone && !zones.length) { showAlert('Please select at least one zone to upload.', 'warning'); return; }
                if (!fileInput.files || !fileInput.files.length) { showAlert('Please choose an Excel/CSV file.', 'warning'); return; }
                var btn = this;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...';
                form.submit();
            });

            // Reset bulk modal dropdowns when hidden so the next open starts clean.
            document.getElementById('bulkUploadModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('bulkUploadForm').reset();
                populateServiceDropdown(document.getElementById('bulkService'), '');
                populateBulkZoneCheckboxes('', '');
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
                    $('#rate-display-' + rateId).text(parseFloat(original).toFixed(2)).removeClass('d-none');
            $('#edit-icon-' + rateId).removeClass('d-none');
            $('#save-icon-' + rateId).addClass('d-none');
            $('#cancel-icon-' + rateId).addClass('d-none');
        }

        function saveRate(rateId) {
            var price = $('#rate-input-' + rateId).val();
            if (price === '' || isNaN(price) || parseFloat(price) < 0) {
                showAlert('Please enter a valid price.', 'warning');
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
                    $('#rate-display-' + rateId).text(parseFloat(price).toFixed(2)).removeClass('d-none');
                    $('#rate-input-' + rateId).data('original', price).addClass('d-none');
                    $('#edit-icon-' + rateId).removeClass('d-none');
                    $('#save-icon-' + rateId).addClass('d-none');
                    $('#cancel-icon-' + rateId).addClass('d-none');
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'This rate cannot be edited.';
                        showAlert(msg, 'error');
                        cancelEdit(rateId);
                    } else {
                        showAlert('Failed to update rate. Please try again.', 'error');
                    }
                }
            });
        }

        // Load customer rates via AJAX
        // Load customer rates via AJAX and store them for filtering
        function loadCustomerRates(customerId) {
            $.ajax({
                url: '{{ url("/admin/manage-rate/get-customer-rates") }}',
                type: 'GET',
                data: { customer_id: customerId },
                success: function(response) {
                    loadedCustomerRates = response.rates || [];
                    // Store the selected customer ID + details for the
                    // end_date popup.
                    currentSelectedCustomerId = customerId;
                    currentCustomerInfo = response.customer || null;
                    currentCustomerEndDate = response.current_end_date || null;
                    renderFilteredCustomerRates();
                    $('#customerRatesTable').show();
                    $('#noCustomerSelected').hide();
                },
                error: function() {
                    showAlert('Failed to load customer rates.', 'error');
                }
            });
        }

        // Render customer rates table, applying country & service filters
        function renderFilteredCustomerRates() {
            var countryFilter = document.getElementById('customerCountryFilter').value;
            var serviceFilter = document.getElementById('customerServiceFilter').value;
            var rows = [];
            var idx = 1;

            loadedCustomerRates.forEach(function(rate) {
                var rateCountry = rate.service ? (rate.service.country || '') : '';
                var rateServiceId = rate.service_id ? String(rate.service_id) : '';

                // Apply filters
                if (countryFilter && rateCountry !== countryFilter) return;
                if (serviceFilter && rateServiceId !== serviceFilter) return;

                var isDefault = rate.is_default ? true : false;
                var defaultBadge = isDefault
                    ? '<span class="badge bg-success">Yes</span>'
                    : '<span class="badge bg-secondary">No</span>';

                // All customer rates (default and non-default) are editable
                var priceCell =
                    '<span class="rate-display" id="cust-rate-display-' + rate.id + '">' + parseFloat(rate.price).toFixed(2) + '</span>' +
                    '<input type="number" step="0.01" min="0" class="rate-input d-none" id="cust-rate-input-' + rate.id + '" value="' + rate.price + '" data-rate-id="' + rate.id + '" data-original="' + rate.price + '">' +
                    '<i class="ti ti-edit edit-icon" id="cust-edit-icon-' + rate.id + '" onclick="editCustomerRate(' + rate.id + ')"></i>' +
                    '<i class="ti ti-device-floppy save-icon d-none" id="cust-save-icon-' + rate.id + '" onclick="saveCustomerRate(' + rate.id + ')"></i>' +
                    '<i class="ti ti-x cancel-icon d-none" id="cust-cancel-icon-' + rate.id + '" onclick="cancelCustomerEdit(' + rate.id + ')"></i>';

                // Resolve zone name & category for this customer rate.
                var rateCountry = rate.service ? (rate.service.country || '') : '';
                var zoneInfo = getZoneInfo(rateCountry, rate.zone_no);
                var zoneFullName = zoneInfo ? zoneInfo.names : '';
                // Show the ZONE NUMBER in the cell (as requested), with the
                // full zone name kept in a tooltip for reference.
                var zoneNoVal = (rate.zone_no !== null && rate.zone_no !== undefined && rate.zone_no !== '')
                    ? rate.zone_no : '—';
                var zoneNameCell;
                if (zoneFullName) {
                    zoneNameCell = '<span class="zone-name-cell" title="' + $('<div>').text(zoneFullName).html() + '">' +
                        $('<div>').text(zoneNoVal).html() + '</span>';
                } else {
                    zoneNameCell = '<span class="zone-name-cell">' + $('<div>').text(zoneNoVal).html() + '</span>';
                }
                var zoneCategoryCell;
                if (zoneInfo) {
                    if (zoneInfo.category === 'state') {
                        zoneCategoryCell = 'State';
                    } else if (zoneInfo.category === 'zipcode') {
                        zoneCategoryCell = 'Zipcode';
                    } else {
                        zoneCategoryCell = zoneInfo.category;
                    }
                } else {
                    zoneCategoryCell = '—';
                }

                // Start Date cell — read-only display of the rate start_date.
                // Formatted as DD-MM-YYYY for readability.
                var startDateVal = rate.start_date || '';
                var startDateDisplay = startDateVal ? formatDate(startDateVal) : '—';
                var startDateCell =
                    '<span class="badge bg-secondary start-date-cell">'
                    + '<i class="ti ti-calendar me-1"></i>' + startDateDisplay
                    + '</span>';

                // End Date cell — clickable to open the customer end_date popup.
                // Formatted as DD-MM-YYYY for readability; the raw value is
                // kept in data-end-date for the popup.
                var endDateVal = rate.end_date || '';
                var endDateDisplay = endDateVal ? formatDate(endDateVal) : '—';
                var endDateCell =
                    '<span class="badge bg-primary cursor-pointer end-date-cell" '
                    + 'style="cursor:pointer;" '
                    + 'data-customer-id="' + (currentSelectedCustomerId || '') + '" '
                    + 'data-end-date="' + endDateVal + '" '
                    + 'onclick="openEndDatePopup(' + (currentSelectedCustomerId || 0) + ', \'' + endDateVal + '\')" '
                    + 'title="Click to change end date for this customer">'
                    + '<i class="ti ti-calendar-event me-1"></i>' + endDateDisplay
                    + '</span>';

                rows.push([
                    idx++,
                    rate.service ? rate.service.network : '—',
                    rate.service ? (rate.service.country || '—') : '—',
                    rate.service ? rate.service.service_code : '—',
                    rate.service ? rate.service.method : '—',
                    rate.service ? rate.service.tat : '—',
                    rate.wt_range_start,
                    rate.wt_range_end,
                    zoneNameCell,
                    zoneCategoryCell,
                    priceCell,
                    defaultBadge,
                    startDateCell,
                    endDateCell
                ]);
            });

            customerRateTable
                .clear()
                .rows.add(rows)
                .draw();
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
            $('#cust-rate-display-' + rateId).text(parseFloat(original).toFixed(2)).removeClass('d-none');
            $('#cust-edit-icon-' + rateId).removeClass('d-none');
            $('#cust-save-icon-' + rateId).addClass('d-none');
            $('#cust-cancel-icon-' + rateId).addClass('d-none');
        }

        function saveCustomerRate(rateId) {
            var price = $('#cust-rate-input-' + rateId).val();
            if (price === '' || isNaN(price) || parseFloat(price) < 0) {
                showAlert('Please enter a valid price.', 'warning');
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
                    $('#cust-rate-display-' + rateId).text(parseFloat(price).toFixed(2)).removeClass('d-none');
                    $('#cust-rate-input-' + rateId).data('original', price).addClass('d-none');
                    $('#cust-edit-icon-' + rateId).removeClass('d-none');
                    $('#cust-save-icon-' + rateId).addClass('d-none');
                    $('#cust-cancel-icon-' + rateId).addClass('d-none');
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'This rate cannot be edited.';
                        showAlert(msg, 'error');
                        cancelCustomerEdit(rateId);
                    } else {
                        showAlert('Failed to update customer rate. Please try again.', 'error');
                    }
                }
            });
        }

        // ============================================================
        // End Date popup — opens when admin clicks the End Date cell
        // for a selected customer. Shows the customer's details and an
        // editable date input. On save, updates end_date for ALL rates
        // of that customer at once via AJAX.
        // ============================================================
        function openEndDatePopup(customerId, currentEndDate) {
            if (!customerId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Customer Selected',
                    text: 'Please select a customer first.',
                });
                return;
            }

            // Use the stored customer info (loaded with the rates) when
            // possible; fall back to a minimal placeholder otherwise.
            var info = currentCustomerInfo || {};
            var fullName = info.full_name
                || ((info.first_name || '') + ' ' + (info.last_name || '')).trim()
                || 'Customer #' + customerId;
            var email = info.email || '—';
            var phone = info.phone_number || '—';
            var custId = info.id || customerId;
            // Default the editable date to TOMORROW (today + 1 day) so the
            // admin sees the next day's date pre-filled, as requested.
            // The selected date is also FORCED to be at least tomorrow — the
            // admin can never pick today or any past date.
            var tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            var endDateValue = tomorrow.toISOString().split('T')[0];
            var minDate = endDateValue; // tomorrow is the minimum allowed date

            // Build the popup HTML: customer details + editable date input
            var popupHtml =
                '<div style="text-align:left;">'
                + '<div style="margin-bottom:16px;padding:12px;border:1px solid #e9ecef;border-radius:8px;background:#f8f9fa;">'
                + '<h6 style="margin:0 0 8px 0;font-weight:600;color:#333;">Customer Details</h6>'
                + '<p style="margin:4px 0;font-size:14px;"><strong>ID:</strong> ' + escapeHtml(String(custId)) + '</p>'
                + '<p style="margin:4px 0;font-size:14px;"><strong>Name:</strong> ' + escapeHtml(fullName) + '</p>'
                + '<p style="margin:4px 0;font-size:14px;"><strong>Email:</strong> ' + escapeHtml(email) + '</p>'
                + '<p style="margin:4px 0;font-size:14px;"><strong>Phone:</strong> ' + escapeHtml(phone) + '</p>'
                + '</div>'
                + '<div style="margin-bottom:8px;">'
                + '<label for="swalEndDateInput" style="display:block;font-weight:600;margin-bottom:6px;">End Date</label>'
                + '<input type="date" id="swalEndDateInput" class="swal2-input" value="' + endDateValue + '" min="' + minDate + '" style="width:100%;margin:0;">'
                + '<small style="color:#6c757d;display:block;margin-top:6px;">The end date must be <strong>tomorrow or later</strong> (today + 1). This will update the end date for <strong>all</strong> rates of this customer.</small>'
                + '</div>'
                + '</div>';

            Swal.fire({
                title: 'Change End Date',
                html: popupHtml,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Update End Date',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                preConfirm: function() {
                    var newDate = document.getElementById('swalEndDateInput').value;
                    if (!newDate) {
                        Swal.showValidationMessage('Please select a valid end date.');
                        return false;
                    }
                    // Enforce that the selected date is at least tomorrow.
                    if (newDate < minDate) {
                        Swal.showValidationMessage('The end date must be tomorrow (' + minDate + ') or a later date.');
                        return false;
                    }
                    return newDate;
                }
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }
                var newEndDate = result.value;

                // AJAX POST to update end_date for ALL rates of this customer
                $.ajax({
                    url: '{{ url("/admin/manage-rate/update-customer-end-date") }}/' + customerId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        end_date: newEndDate
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the stored value so the next popup
                            // shows the new date immediately.
                            currentCustomerEndDate = newEndDate;
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message || 'End date updated successfully.',
                                timer: 1800,
                                showConfirmButton: false
                            });
                            // Reload the customer rates table to reflect
                            // the new end_date on every row.
                            if (currentSelectedCustomerId) {
                                loadCustomerRates(currentSelectedCustomerId);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: response.message || 'Failed to update end date.'
                            });
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'Failed to update end date. Please try again.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    }
                });
            });
        }

        // ============================================================
        // End Date popup for MULTIPLE selected customers.
        // Shows ALL selected customers' details in a scrollable list
        // and updates end_date for all of them at once via AJAX.
        // ============================================================
        function openEndDatePopupForMultiple(customerIds) {
            if (!customerIds || customerIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Customer Selected',
                    text: 'Please select at least one customer.',
                });
                return;
            }

            // Default the editable date to TOMORROW
            var tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            var endDateValue = tomorrow.toISOString().split('T')[0];
            var minDate = endDateValue;

            // Build customer list HTML from the checkbox labels (which have name + email/phone)
            var customerListHtml = '';
            customerIds.forEach(function(cid) {
                var cb = document.querySelector('.customer-checkbox[value="' + cid + '"]');
                if (cb) {
                    var labelEl = cb.closest('label');
                    var labelText = labelEl ? labelEl.querySelector('.customer-checkbox-label').textContent.trim() : ('Customer #' + cid);
                    customerListHtml += '<div style="padding:6px 8px;margin:2px 0;background:#fff;border-radius:4px;border:1px solid #dee2e6;font-size:13px;">'
                        + '<strong>#' + escapeHtml(String(cid)) + '</strong> — ' + escapeHtml(labelText)
                        + '</div>';
                } else {
                    customerListHtml += '<div style="padding:6px 8px;margin:2px 0;background:#fff;border-radius:4px;border:1px solid #dee2e6;font-size:13px;">'
                        + '<strong>#' + escapeHtml(String(cid)) + '</strong></div>';
                }
            });

            var popupHtml =
                '<div style="text-align:left;">'
                + '<div style="margin-bottom:16px;padding:12px;border:1px solid #e9ecef;border-radius:8px;background:#f8f9fa;">'
                + '<h6 style="margin:0 0 8px 0;font-weight:600;color:#333;">Selected Customers (' + customerIds.length + ')</h6>'
                + '<div style="max-height:200px;overflow-y:auto;">'
                + customerListHtml
                + '</div>'
                + '</div>'
                + '<div style="margin-bottom:8px;">'
                + '<label for="swalEndDateInput" style="display:block;font-weight:600;margin-bottom:6px;">End Date</label>'
                + '<input type="date" id="swalEndDateInput" class="swal2-input" value="' + endDateValue + '" min="' + minDate + '" style="width:100%;margin:0;">'
                + '<small style="color:#6c757d;display:block;margin-top:6px;">The end date must be <strong>tomorrow or later</strong>. This will update the end date for <strong>all ' + customerIds.length + ' selected customers</strong>.</small>'
                + '</div>'
                + '</div>';

            Swal.fire({
                title: 'Change End Date',
                html: popupHtml,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Update End Date',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                preConfirm: function() {
                    var newDate = document.getElementById('swalEndDateInput').value;
                    if (!newDate) {
                        Swal.showValidationMessage('Please select a valid end date.');
                        return false;
                    }
                    if (newDate < minDate) {
                        Swal.showValidationMessage('The end date must be tomorrow (' + minDate + ') or a later date.');
                        return false;
                    }
                    return newDate;
                }
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }
                var newEndDate = result.value;

                // Update end_date for ALL selected customers one by one
                var updatePromises = customerIds.map(function(cid) {
                    return $.ajax({
                        url: '{{ url("/admin/manage-rate/update-customer-end-date") }}/' + cid,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            end_date: newEndDate
                        }
                    });
                });

                Promise.all(updatePromises)
                    .then(function(responses) {
                        var allSuccess = responses.every(function(r) { return r.success; });
                        if (allSuccess) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: 'End date updated for ' + customerIds.length + ' customer(s).',
                                timer: 1800,
                                showConfirmButton: false
                            });
                            // Reload the customer rates table
                            if (currentSelectedCustomerId) {
                                loadCustomerRates(currentSelectedCustomerId);
                            }
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Partial Success',
                                text: 'Some updates may have failed. Please check the rates.'
                            });
                        }
                    })
                    .catch(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update end date for some customers. Please try again.'
                        });
                    });
            });
        }

        // Small helper to escape HTML so customer details are safe to inject
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            // Build HTML entities via concatenation so the named entities
            // are not decoded when the blade file is saved.
            var amp = '&' + 'amp;';
            var lt = '&' + 'lt;';
            var gt = '&' + 'gt;';
            var quot = '&' + 'quot;';
            var apos = '&' + '#039;';
            return String(str)
                .replace(/&/g, amp)
                .replace(/</g, lt)
                .replace(/>/g, gt)
                .replace(/"/g, quot)
                .replace(/'/g, apos);
        }
    </script>

</body>
</html>