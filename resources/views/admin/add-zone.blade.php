<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Add Zone</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .zone-entry-row { transition: background-color .15s; }
        .zone-entry-row:hover { background-color: #f8f9fa; }
        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #007bff;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            margin-right: 8px;
        }
        .nav-tabs .nav-link {
            font-weight: 600;
            font-size: 14px;
            padding: 10px 20px;
            color: #5b6b7f;
            border: none;
            border-bottom: 3px solid transparent;
            transition: color .15s ease-in-out;
        }
        .nav-tabs .nav-link:hover {
            color: #007bff;
            border-bottom-color: #cfe2ff;
        }
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #007bff;
            color: #007bff;
            background: transparent;
        }
        .nav-tabs { border-bottom: 1px solid #e3eaf3; }

        /* ---------- Zone List tab ---------- */
        .zl-filter-card {
            border: 1px solid #e3eaf3;
            border-radius: 12px;
            background: linear-gradient(160deg, #f6faff 0%, #ffffff 60%);
        }
        .zl-filter-card .form-select-lg { border-radius: 8px; }
        .zl-search-card {
            border: 1px solid #e3eaf3;
            border-radius: 12px;
        }
        #zoneListTable { font-size: 13px; }
        #zoneListTable thead th {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #6b7a90;
            background: #f2f6fc;
            border-bottom: 1px solid #dfe7f2 !important;
            white-space: nowrap;
            padding: 8px 12px;
        }
        #zoneListTable tbody td {
            vertical-align: middle;
            padding: 7px 12px;
        }
        #zoneListTable tbody tr { transition: background .12s; }
        #zoneListTable tbody tr:hover td { background-color: #f5f9ff; }
        .cat-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 50rem;
            white-space: nowrap;
        }
        .zl-svc-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 50rem;
            white-space: nowrap;
            max-width: 100%;
        }
        /* Full service names: never truncate / ellipsis the service text. */
        .zl-svc-badge .svc-text { white-space: nowrap; }
        .zl-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 50rem;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.2;
            user-select: none;
            transition: all .15s ease;
        }
        .zl-chip:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(13, 110, 253, .12); }
        .zl-chip .chip-count {
            min-width: 20px;
            text-align: center;
            background: rgba(255, 255, 255, .85);
            border-radius: 50rem;
            padding: 1px 6px;
            font-size: 11px;
            font-weight: 700;
        }
        /* Smaller secondary controls inside the zone list. */
        #zoneListScopeText { font-size: 12.5px; }
        #zoneListCountBadge { font-size: 12.5px; padding: 4px 10px; }
        #zoneListResultsCard .input-group .form-control,
        #zoneListResultsCard .input-group-text { font-size: 13px; }
        #zoneListExportBtn { font-size: 12.5px; }
        .zl-chip.zl-chip-off { opacity: .5; }
        .zl-chip-total   { background: #eaf1ff; color: #0b5cd6; border-color: #cfe0ff; }
        .zl-chip-state   { background: #e2f6ff; color: #0a7ab8; border-color: #c4ecff; }
        .zl-chip-zip     { background: #fff4d6; color: #a4760a; border-color: #ffe3a1; }
        .zl-chip-city    { background: #ecebff; color: #5a4bcb; border-color: #d7d3ff; }
        .zl-chip-shared  { background: #e9f9f1; color: #1e7f4f; border-color: #c6f0da; cursor: default; }
        .zl-chip-service { background: #fdecef; color: #b9384b; border-color: #fbd0d7; cursor: default; }
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
                        <h4 class="mb-1">Add Zone</h4>
                        <p class="text-muted mb-0">Create new zones for a country. First select a country, then a zone category, then enter the zone entries.</p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ url('/admin/manage-rate') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Back to Manage Rate
                        </a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                    </div>
                </div>

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
                @if(session('skipped_zone_rows'))
                    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert" id="skippedZoneAlert">
                        <div class="me-2">
                            <i class="ti ti-download me-1"></i>
                            Some zone records were skipped during the last upload. You can download them below.
                        </div>
                        <a href="{{ route('admin.add-zone.skipped') }}" class="btn btn-sm btn-warning fw-bold text-nowrap" id="downloadSkippedBtn">
                            <i class="ti ti-download me-1"></i>Download Skipped Records
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <script>
                        // The download request clears the skipped rows from the
                        // session but stays on this page. Hide the alert right
                        // away so the button cannot be clicked a second time
                        // (which would show the confusing "no skipped records"
                        // error). The alert is also re-shown automatically if a
                        // new upload skips rows, because the page reloads then.
                        document.getElementById('downloadSkippedBtn').addEventListener('click', function() {
                            var alertEl = document.getElementById('skippedZoneAlert');
                            if (alertEl) {
                                alertEl.remove();
                            }
                        });
                    </script>
                @endif

                <ul class="nav nav-tabs mb-4" id="zoneTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="add-zones-tab" data-bs-toggle="tab" data-bs-target="#add-zones-pane" type="button" role="tab" aria-controls="add-zones-pane" aria-selected="true">
                            <i class="ti ti-plus me-1"></i>Add Zones
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="zone-list-tab" data-bs-toggle="tab" data-bs-target="#zone-list-pane" type="button" role="tab" aria-controls="zone-list-pane" aria-selected="false">
                            <i class="ti ti-list me-1"></i>Zone List
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="zoneTabsContent">

                    <!-- Add Zones Tab -->
                    <div class="tab-pane fade show active" id="add-zones-pane" role="tabpanel" aria-labelledby="add-zones-tab">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="addZoneForm" method="POST" action="{{ route('admin.add-zone.store') }}">
                                    @csrf

                                    <!-- Step 1: Country & Service -->
                                    <div class="mb-4">
                                        <h6 class="mb-3"><span class="step-badge">1</span>Select Country & Service</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                                <select class="form-select" id="destination_id" name="destination_id" required>
                                                    <option value="">— Select Country —</option>
                                                    @foreach($destinations as $dest)
                                                        <option value="{{ $dest->id }}">{{ $dest->name }} ({{ $dest->code }})</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Choose the country these zones belong to. Need a new country? <a href="{{ route('admin.add-country') }}">Add Country</a>.</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Service <span class="text-danger">*</span></label>
                                                <select class="form-select" id="service_id" name="service_id" required>
                                                    <option value="">— Select Service —</option>
                                                    @foreach($services as $svc)
                                                        <option value="{{ $svc->id }}" data-country-id="{{ $serviceDestMap[$svc->id] ?? '' }}">
                                                            {{ $svc->method ?? ('Service #' . $svc->id) }}
                                                            @if(!empty($svc->service_code)) - ({{ $svc->service_code }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Only services for the selected country are shown. Select a courier service for these zones.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2: Zone Category (only shown after country is selected) -->
                                    <div class="mb-4" id="categorySection" style="display:none;">
                                        <h6 class="mb-3"><span class="step-badge">2</span>Select Zone Category</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Zone Category <span class="text-danger">*</span></label>
                                                <select class="form-select" id="zone_category" name="zone_category" required>
                                                    <option value="">— Select Category —</option>
                                                    <option value="state">State</option>
                                                    <option value="zipcode">Zipcode</option>
                                                    <option value="city">City</option>
                                                </select>
                                                <small class="text-muted">State = state/province names, Zipcode = postal codes, City = city names.</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Zone Number <span class="text-danger">*</span></label>
                                                <select class="form-select" id="zone_number" name="zone_number" required>
                                                    <option value="">— Select —</option>
                                                    @for($i = 0; $i <= 13; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <small class="text-muted">Rates reference this number (0-13).</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3: Zone Entries (only shown after category is selected) -->
                                    <div id="entriesSection" style="display:none;">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6 class="mb-0"><span class="step-badge">3</span>Enter Zone Entries</h6>
                                            <button type="button" class="btn btn-sm btn-primary" id="addRowBtn">
                                                <i class="ti ti-plus me-1"></i>Add Row
                                            </button>
                                        </div>
                                        <div class="alert alert-info py-2">
                                            <i class="ti ti-info-circle me-1"></i>
                                            Add one row per zone entry. For the "State" category, enter state names (e.g. Alabama, Texas). For "Zipcode", enter postal codes (e.g. 10001, 90210). For "City", enter city names. <strong>A single zone name can have multiple zone codes</strong> — to add more codes for the same name, just add another row with the same zone name and a different zone code. <strong>For the "Zipcode" category, a zone code must be unique across <em>all</em> countries</strong> (it is used to look up rates globally), so a zipcode code that already exists in any country will be skipped as a duplicate. For "State"/"City", duplicate codes are only checked within the selected country <strong>and the selected Service</strong> (if a Service is chosen).
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="zoneEntriesTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width:50px;">#</th>
                                                        <th>Zone Name <span class="text-danger">*</span></th>
                                                        <th style="width:160px;">Zone Code</th>
                                                        <th style="width:60px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="zoneEntriesBody">
                                                    <!-- Rows added by JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Submit (only shown when there are entries) -->
                                    <div class="mt-4" id="submitSection" style="display:none;">
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="ti ti-device-floppy me-1"></i>Save Zones
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="resetBtn">
                                            <i class="ti ti-refresh me-1"></i>Reset
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Excel Upload Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h5 class="mb-0"><i class="ti ti-file-spreadsheet me-2"></i>Bulk Upload Zones (Excel)</h5>
                                <a href="{{ route('admin.add-zone.sample') }}" class="btn btn-sm btn-outline-success" id="downloadSampleBtn">
                                    <i class="ti ti-download me-1"></i>Download Sample Format
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Upload an Excel (.xlsx) or CSV file to add many zones at once. The file must have a header row with <strong>Zone Name</strong> (required) and <strong>Zone Code</strong> (optional). <strong>A single zone name can have multiple zone codes</strong> — repeat the same zone name on multiple rows with a different zone code each time. <strong>For the "Zipcode" category, a zone code must be unique across <em>all</em> countries</strong> (it is used to look up rates globally), so a zipcode code that already exists in any country will be skipped as a duplicate. For "State"/"City", duplicate codes are only checked within the selected country <strong>and the selected Service</strong> (if a Service is chosen). <strong>Tip:</strong> select the Country and Zone Category below first, then click <em>Download Sample Format</em> — the sample will include the zones that already exist for that country/category so you can see what's already there and avoid duplicates. The Country, Zone Category, Zone Number and Service you select below will be applied to <em>every</em> row in the file.
                                </div>
                                <form id="uploadZoneForm" method="POST" action="{{ route('admin.add-zone.upload') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                            <select class="form-select" id="upload_destination_id" name="destination_id" required>
                                                <option value="">— Select Country —</option>
                                                @foreach($destinations as $dest)
                                                    <option value="{{ $dest->id }}">{{ $dest->name }} ({{ $dest->code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold">Zone Category <span class="text-danger">*</span></label>
                                            <select class="form-select" name="zone_category" required>
                                                <option value="">— Select —</option>
                                                <option value="state">State</option>
                                                <option value="zipcode">Zipcode</option>
                                                <option value="city">City</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold">Zone Number <span class="text-danger">*</span></label>
                                            <select class="form-select" name="zone_number" required>
                                                <option value="">— Select —</option>
                                                @for($i = 0; $i <= 13; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold">Service <span class="text-danger">*</span></label>
                                            <select class="form-select" id="upload_service_id" name="service_id" required>
                                                <option value="">— Select Service —</option>
                                                @foreach($services as $svc)
                                                    <option value="{{ $svc->id }}" data-country-id="{{ $serviceDestMap[$svc->id] ?? '' }}">
                                                        {{ $svc->method ?? ('Service #' . $svc->id) }}
                                                        @if(!empty($svc->service_code)) [{{ $svc->service_code }}] @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold">Excel File <span class="text-danger">*</span></label>
                                            <small class="text-muted">Max 5 MB. .xlsx, .xls or .csv</small>
                                            <input type="file" class="form-control" name="zone_file" accept=".xlsx,.xls,.csv" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary w-100" id="uploadBtn">
                                                <i class="ti ti-upload me-1"></i>Upload
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                    </div><!-- /.add-zones-pane -->

                    <!-- Zone List Tab -->
                    <div class="tab-pane fade" id="zone-list-pane" role="tabpanel" aria-labelledby="zone-list-tab">
                        <div class="zl-filter-card p-4 mb-3">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-bold" for="list_destination_id">
                                        <i class="ti ti-world me-1 text-primary"></i>Country <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="list_destination_id">
                                        <option value="">— Select Country —</option>
                                        @foreach($destinations as $dest)
                                            <option value="{{ $dest->id }}">{{ $dest->name }} ({{ $dest->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-bold" for="list_service_id">
                                        <i class="ti ti-truck-delivery me-1 text-primary"></i>Service
                                    </label>
                                    
                                    <select class="form-select" id="list_service_id">
                                        <option value="">— All Services —</option>
                                        @foreach($services as $svc)
                                            <option value="{{ $svc->id }}" data-country-id="{{ $serviceDestMap[$svc->id] ?? '' }}">
                                                {{ $svc->method ?? ('Service #' . $svc->id) }}
                                                @if(!empty($svc->service_code)) ({{ $svc->service_code }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                </div>
                                <div class="col-lg-4 d-flex gap-2">
                                    <button type="button" class="btn btn-primary flex-fill" id="zoneListLoadBtn">
                                        <i class="ti ti-search me-1"></i>Show Zones
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="zoneListRefreshBtn" data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh">
                                        <i class="ti ti-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Results header + summary chips (visible only when a country is chosen) -->
                        <div id="zoneListSummary" class="mb-3" style="display:none;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <h6 class="mb-0">
                                    <i class="ti ti-map-pins me-1 text-primary"></i>Zones
                                    <span class="text-muted fw-normal" id="zoneListScopeText"></span>
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="zoneListSearchClear" style="display:none;">
                                    <i class="ti ti-filter-x me-1"></i>Clear Local Filters
                                </button>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="zl-chip zl-chip-total" data-cat="all">
                                    <i class="ti ti-apps"></i>All
                                    <span class="chip-count" id="chipCountAll">0</span>
                                </span>
                                <span class="zl-chip zl-chip-state" data-cat="state">
                                    <i class="ti ti-flag"></i>States
                                    <span class="chip-count" id="chipCountState">0</span>
                                </span>
                                <span class="zl-chip zl-chip-zip" data-cat="zipcode">
                                    <i class="ti ti-hash"></i>ZIP Codes
                                    <span class="chip-count" id="chipCountZip">0</span>
                                </span>
                                <span class="zl-chip zl-chip-city" data-cat="city">
                                    <i class="ti ti-building-skyscraper"></i>Cities
                                    <span class="chip-count" id="chipCountCity">0</span>
                                </span>
                                <span class="zl-chip zl-chip-shared ms-auto">
                                    <i class="ti ti-world-share"></i>Shared zones included
                                </span>
                            </div>
                        </div>

                        <!-- Results card -->
                        <div class="card zl-search-card" id="zoneListResultsCard" style="display:none;">
                            <div class="card-body">
                                <div class="row g-2 align-items-center mb-3">
                                    <div class="col-md-5 col-lg-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="ti ti-search text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0 ps-0" id="zoneListSearch"
                                                   placeholder="Search zone name, code...">
                                        </div>
                                    </div>
                                    <div class="col-md-7 col-lg-8 d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2" id="zoneListCountBadge">
                                            <i class="ti ti-database me-1"></i><span id="zoneListCountText">0 zones</span>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="zoneListExportBtn">
                                            <i class="ti ti-download me-1"></i>Export CSV
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="zoneListTable">
                                        <thead>
                                            <tr>
                                                <th style="width:50px;">#</th>
                                                <th>Country</th>
                                                <th>Zone Name</th>
                                                <th>Zone Code</th>
                                                <th>Category</th>
                                                <th>Zone Number</th>
                                                <th>Service</th>
                                            </tr>
                                        </thead>
                                        <tbody id="zoneListBody"></tbody>
                                    </table>
                                </div>
                                <div class="text-center py-5 d-none" id="zoneListNoMatch">
                                    <i class="ti ti-search-off fs-1 d-block mb-2 text-muted"></i>
                                    <h6 class="mb-1">No matching zones</h6>
                                    <p class="text-muted mb-0">Try a different search term or choose another category above.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Empty / initial state -->
                        <div class="text-center py-5" id="zoneListEmpty">
                            <div class="zl-empty-icon d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width:76px;height:76px;border-radius:50%;background:#eaf1ff;color:#0b5cd6;">
                                <i class="ti ti-world-lock fs-1"></i>
                            </div>
                            <h5 class="mb-1">No zones to show yet</h5>
                            <p class="text-muted mb-3 mx-auto" style="max-width:460px;">
                                Select a country and optionally a service above, then click
                                <strong>Show Zones</strong> to browse all the zones configured for that destination.
                            </p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="#add-zones-pane" class="btn btn-primary" data-bs-toggle="tab" role="tab">
                                    <i class="ti ti-plus me-1"></i>Add New Zones
                                </a>
                                <button type="button" class="btn btn-outline-secondary" id="zoneListEmptyRefresh">
                                    <i class="ti ti-refresh me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div><!-- /.zone-list-pane -->

                </div><!-- /.tab-content -->

            </div>
        </div>

    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <!-- Slimscroll JS -->
    <script src="{{ asset('assets/plugins/slimscroll/slimscroll.min.js') }}" type="text/javascript"></script>
    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var rowCounter = 0;

            // Add a new zone entry row.
            function addRow(name, code) {
                rowCounter++;
                var row = $(
                    '<tr class="zone-entry-row" data-row="' + rowCounter + '">' +
                        '<td class="row-num text-center">' + rowCounter + '</td>' +
                        '<td><input type="text" class="form-control zone-name-input" name="entries[' + rowCounter + '][zone_name]" placeholder="e.g. Alabama" required></td>' +
                        '<td><input type="text" class="form-control zone-code-input" name="entries[' + rowCounter + '][zone_code]" placeholder="e.g. AL" maxlength="10"></td>' +
                        '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="ti ti-trash"></i></button></td>' +
                    '</tr>'
                );
                if (name) row.find('.zone-name-input').val(name);
                if (code) row.find('.zone-code-input').val(code);
                $('#zoneEntriesBody').append(row);
                renumberRows();
                $('#submitSection').show();
            }

            // Renumber all rows after add/remove.
            function renumberRows() {
                $('#zoneEntriesBody tr').each(function(i) {
                    $(this).find('.row-num').text(i + 1);
                });
            }

            // Show only the services that belong to the selected country.
            // Each option carries a data-country-id attribute (the matching
            // destination id), populated server-side via $serviceDestMap.
            function filterServicesByCountry(countryId, $serviceSelect) {
                $serviceSelect.find('option').each(function() {
                    var $opt = $(this);
                    if ($opt.val() === '') {
                        $opt.show(); // keep the "— No Service —" placeholder
                        return;
                    }
                    if (String($opt.data('country-id')) === String(countryId)) {
                        $opt.show();
                    } else {
                        $opt.hide();
                    }
                });
            }

            function showAllServices($serviceSelect) {
                $serviceSelect.find('option').each(function() {
                    $(this).show();
                });
            }

            // Step 1 -> Step 2: when a country is selected, reveal the category
            // section and filter the Service dropdown to that country only.
            $('#destination_id').on('change', function() {
                if (this.value) {
                    $('#categorySection').show();
                    $('#service_id').val('');
                    filterServicesByCountry(this.value, $('#service_id'));
                } else {
                    $('#categorySection').hide();
                    $('#entriesSection').hide();
                    $('#submitSection').hide();
                    $('#zone_category').val('');
                    $('#zone_number').val('');
                    $('#service_id').val('');
                    showAllServices($('#service_id'));
                }
            });

            // Keep the bulk upload form's Service dropdown in sync with its
            // Country selection.
            $('#upload_destination_id').on('change', function() {
                $('#upload_service_id').val('');
                if (this.value) {
                    filterServicesByCountry(this.value, $('#upload_service_id'));
                } else {
                    showAllServices($('#upload_service_id'));
                }
            });

            // Step 2 -> Step 3: when category is selected, reveal entries and add the first row.
            $('#zone_category').on('change', function() {
                if (this.value && $('#destination_id').val()) {
                    $('#entriesSection').show();
                    $('#submitSection').show();
                    if ($('#zoneEntriesBody tr').length === 0) {
                        addRow();
                    }
                    // Update the placeholder hint based on category.
                    var ph = 'e.g. Alabama';
                    if (this.value === 'zipcode') ph = 'e.g. 10001';
                    else if (this.value === 'city') ph = 'e.g. New York';
                    $('.zone-name-input').attr('placeholder', ph);
                } else if (!this.value) {
                    $('#entriesSection').hide();
                    $('#submitSection').hide();
                }
            });

            // Add Row button
            $('#addRowBtn').on('click', function() {
                addRow();
            });

            // Remove Row (delegated, since rows are added dynamically)
            $(document).on('click', '.remove-row-btn', function() {
                $(this).closest('tr').remove();
                renumberRows();
                if ($('#zoneEntriesBody tr').length === 0) {
                    $('#submitSection').hide();
                }
            });

            // Reset button
            $('#resetBtn').on('click', function() {
                $('#addZoneForm')[0].reset();
                $('#categorySection').hide();
                $('#entriesSection').hide();
                $('#submitSection').hide();
                $('#zoneEntriesBody').empty();
                $('#service_id').val('');
                showAllServices($('#service_id'));
            });

            // Form submit guard: ensure at least one non-empty entry exists.
            $('#addZoneForm').on('submit', function(e) {
                var hasEntry = false;
                $('#zoneEntriesBody tr').each(function() {
                    if ($(this).find('.zone-name-input').val().trim() !== '') {
                        hasEntry = true;
                        return false;
                    }
                });
                if (!hasEntry) {
                    e.preventDefault();
                    showAlert('Please add at least one zone entry with a zone name.', 'warning');
                    return;
                }
                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
            });

            // Bulk upload form: show loading state while processing.
            $('#uploadZoneForm').on('submit', function() {
                $('#uploadBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Uploading...');
            });

            // Make the "Download Sample Format" button dynamic: when
            // clicked, append the currently selected country and category
            // from the upload form so the sample includes the zones that
            // already exist for that country/category.
            $('#downloadSampleBtn').on('click', function(e) {
                var destId = $('#uploadZoneForm select[name="destination_id"]').val();
                var category = $('#uploadZoneForm select[name="zone_category"]').val() || 'state';
                var base = "{{ route('admin.add-zone.sample') }}";
                var sep = base.indexOf('?') === -1 ? '?' : '&';
                if (destId) {
                    this.href = base + sep + 'destination_id=' + encodeURIComponent(destId) + '&zone_category=' + encodeURIComponent(category);
                } else {
                    this.href = base;
                }
            });

            // =================================================================
            // Zone List tab
            // =================================================================
            var zoneListUrl = "{{ route('admin.add-zone.list') }}";

            // Zones returned by the last AJAX load (kept so the client-side
            // search / category filters never need another round trip).
            var zoneListData = [];
            // Currently active category chip: 'all' | 'state' | 'zipcode' | 'city'.
            var zoneListActiveCat = 'all';

            // Only show the services that belong to the selected country in the
            // Zone List tab's Service dropdown.
            function filterZoneListServices(countryId) {
                $('#list_service_id option').each(function() {
                    var $opt = $(this);
                    if ($opt.val() === '') { // "All Services"
                        $opt.show();
                        return;
                    }
                    if (String($opt.data('country-id')) === String(countryId)) {
                        $opt.show();
                    } else {
                        $opt.hide();
                    }
                });
            }

            // Escape a value safely for use inside HTML strings.
            function escHtml(val) {
                return $('<span>').text(val == null ? '' : String(val)).html();
            }

            // Category chip metadata -> label, badge classes, tabler icon.
            function zoneCategoryMeta(cat) {
                var key = String(cat || '').toLowerCase();
                var map = {
                    'state':   { label: 'State',   cls: 'bg-info-subtle text-info',          icon: 'ti ti-flag' },
                    'zipcode': { label: 'ZIP Code', cls: 'bg-warning-subtle text-warning',    icon: 'ti ti-hash' },
                    'city':    { label: 'City',    cls: 'bg-primary-subtle text-primary',     icon: 'ti ti-building-skyscraper' }
                };
                return map[key] || { label: cat || '—', cls: 'bg-secondary-subtle text-secondary', icon: 'ti ti-tag' };
            }

            function isSharedZone(z) {
                return String(z.service_name || '').toLowerCase() === 'all services';
            }

            // Service column badge: shared zones get a green "Shared" pill,
            // service-specific zones a neutral pill with the service name.
            function serviceBadgeHtml(z) {
                if (isSharedZone(z)) {
                    return '<span class="zl-svc-badge bg-success-subtle text-success border border-success-subtle">' +
                        '<i class="ti ti-world-share"></i><span class="svc-text">Shared (All Services)</span></span>';
                }
                var svcName = z.service_name || '—';
                return '<span class="zl-svc-badge bg-secondary-subtle text-secondary border border-secondary-subtle" title="' + escHtml(svcName) + '">' +
                    '<i class="ti ti-truck-delivery"></i><span class="svc-text">' + escHtml(svcName) + '</span></span>';
            }

            // Show the "no data" panel. The panel keeps its action buttons.
            function showZoneListEmpty(title, message) {
                $('#zoneListResultsCard').hide();
                $('#zoneListSummary').hide();
                if (title) $('#zoneListEmpty h5').text(title);
                if (message) $('#zoneListEmpty p.text-muted').text(message);
                $('#zoneListEmpty').show();
            }

            // Hide the "no data" panel and reveal the results area.
            function hideZoneListEmpty() {
                $('#zoneListEmpty').hide();
                $('#zoneListSummary').show();
                $('#zoneListResultsCard').show();
            }

            // Apply the current category chip + search box to the stored zones
            // and re-render the chips, table and count badge.
            function renderZoneList() {
                var zones = zoneListData;
                var cat = zoneListActiveCat;
                var query = ($('#zoneListSearch').val() || '').trim().toLowerCase();
                var hasLocalFilter = cat !== 'all' || query !== '';

                // Header context line with the FULL country & service names.
                var destName = $('#list_destination_id option:selected').text() || 'selected country';
                var svcId = $('#list_service_id').val();
                var scope = 'Country: <strong>' + escHtml(destName) + '</strong>';
                scope += ' · Service: <strong>';
                if (svcId) {
                    scope += escHtml($('#list_service_id option:selected').text() || '');
                } else {
                    scope += 'All Services (shared + all)';
                }
                scope += '</strong>';
                $('#zoneListScopeText').html('&nbsp;' + scope);

                // Category counts for the chips.
                var counts = { state: 0, zipcode: 0, city: 0 };
                $.each(zones, function() {
                    var k = String(this.zone_category || '').toLowerCase();
                    if (counts[k] !== undefined) counts[k]++;
                });
                $('#chipCountAll').text(zones.length);
                $('#chipCountState').text(counts.state);
                $('#chipCountZip').text(counts.zipcode);
                $('#chipCountCity').text(counts.city);

                // Highlight the active chip.
                $('.zl-chip[data-cat]').each(function() {
                    var isActive = cat === String($(this).data('cat'));
                    $(this).toggleClass('zl-chip-off', !isActive);
                });

                $('#zoneListSearchClear').toggle(hasLocalFilter);

                // Filter the stored zones locally.
                var filtered = [];
                $.each(zones, function(i, z) {
                    if (cat !== 'all' && String(z.zone_category || '').toLowerCase() !== cat) return;
                    if (query) {
                        var hay = ((z.zone_name || '') + ' ' + (z.zone_code || '') + ' ' +
                                   (z.zone_category || '') + ' ' + (z.zone_number != null ? z.zone_number : '') + ' ' +
                                   (z.service_name || '') + ' ' + destName).toLowerCase();
                        if (hay.indexOf(query) === -1) return;
                    }
                    filtered.push(z);
                });

                var $tbody = $('#zoneListBody').empty();
                if (filtered.length === 0) {
                    $('#zoneListTable').addClass('d-none');
                    $('#zoneListNoMatch').removeClass('d-none');
                } else {
                    $('#zoneListNoMatch').addClass('d-none');
                    $('#zoneListTable').removeClass('d-none');
                    $.each(filtered, function(i, z) {
                        var meta = zoneCategoryMeta(z.zone_category);
                        var codeTd = z.zone_code
                            ? '<code>' + escHtml(z.zone_code) + '</code>'
                            : '<span class="text-muted">—</span>';
                        var zoneNoTd = (z.zone_number != null && z.zone_number !== '')
                            ? '<span class="badge bg-light text-dark border">#' + escHtml(z.zone_number) + '</span>'
                            : '<span class="text-muted">—</span>';
                        $tbody.append(
                            '<tr>' +
                                '<td class="text-center text-muted">' + (i + 1) + '</td>' +
                                '<td><i class="ti ti-world me-1 text-muted"></i>' + escHtml(destName) + '</td>' +
                                '<td class="fw-semibold">' + escHtml(z.zone_name || '—') + '</td>' +
                                '<td>' + codeTd + '</td>' +
                                '<td><span class="cat-badge ' + meta.cls + '"><i class="' + meta.icon + '"></i>' + meta.label + '</span></td>' +
                                '<td class="text-center">' + zoneNoTd + '</td>' +
                                '<td>' + serviceBadgeHtml(z) + '</td>' +
                            '</tr>'
                        );
                    });
                }

                var countText = filtered.length + ' of ' + zones.length + ' zone' + (zones.length === 1 ? '' : 's');
                $('#zoneListCountText').text(countText);
            }

            // Load the zones via AJAX for the chosen country/service.
            function loadZoneList() {
                var destId = $('#list_destination_id').val();
                if (!destId) {
                    zoneListData = [];
                    showZoneListEmpty('No zones to show yet',
                        'Select a country and optionally a service above, then click "Show Zones" to browse all the zones configured for that destination.');
                    return;
                }

                var $btn = $('#zoneListLoadBtn');
                var serviceId = $('#list_service_id').val();
                var params = { destination_id: destId };
                if (serviceId) {
                    params.service_id = serviceId;
                }

                // Reset the client-side search & category for the new data set.
                $('#zoneListSearch').val('');
                zoneListActiveCat = 'all';
                $('#zoneListEmpty').hide();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Loading...');

                $.ajax({
                    url: zoneListUrl,
                    type: 'GET',
                    data: params,
                    dataType: 'json',
                    success: function(res) {
                        zoneListData = res.zones || [];
                        if (zoneListData.length === 0) {
                            showZoneListEmpty('No zones found',
                                'No zones are configured for the selected country' + (serviceId ? ' and service' : '') +
                                '. Add some zones using the "Add Zones" tab.');
                        } else {
                            hideZoneListEmpty();
                            renderZoneList();
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Could not load the zone list. Please try again.';
                        try {
                            var data = JSON.parse(xhr.responseText);
                            if (data && data.message) msg = data.message;
                        } catch (e) {}
                        zoneListData = [];
                        showZoneListEmpty('Something went wrong', msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="ti ti-search me-1"></i>Show Zones');
                    }
                });
            }

            // Local search box.
            $('#zoneListSearch').on('input', renderZoneList);

            // "Clear local filters" resets search + category but keeps the data.
            $('#zoneListSearchClear').on('click', function() {
                $('#zoneListSearch').val('');
                zoneListActiveCat = 'all';
                renderZoneList();
            });

            // Category chips (States / ZIP / Cities / All).
            $('.zl-chip[data-cat]').on('click', function() {
                zoneListActiveCat = String($(this).data('cat'));
                renderZoneList();
            });

            // When a country is picked in the Zone List tab, restrict the
            // Service dropdown to that country's services and auto-load the
            // zone list straight away.
            $('#list_destination_id').on('change', function() {
                $('#list_service_id').val('');
                if (this.value) {
                    filterZoneListServices(this.value);
                    loadZoneList();
                } else {
                    showAllServices($('#list_service_id'));
                    zoneListData = [];
                    showZoneListEmpty('No zones to show yet',
                        'Select a country and optionally a service above, then click "Show Zones" to browse all the zones configured for that destination.');
                }
            });

            // When a service is selected, reload the list for that service.
            $('#list_service_id').on('change', function() {
                loadZoneList();
            });

            // Manual trigger buttons.
            $('#zoneListLoadBtn').on('click', loadZoneList);
            $('#zoneListRefreshBtn').on('click', loadZoneList);
            $('#zoneListEmptyRefresh').on('click', loadZoneList);

            // Export the currently visible (filtered) zones as a CSV file.
            $('#zoneListExportBtn').on('click', function() {
                if (!zoneListData.length) return;
                var cat = zoneListActiveCat;
                var query = ($('#zoneListSearch').val() || '').trim().toLowerCase();
                var destName = $('#list_destination_id option:selected').text() || 'selected country';
                var rows = [];
                $.each(zoneListData, function() {
                    var z = this;
                    if (cat !== 'all' && String(z.zone_category || '').toLowerCase() !== cat) return;
                    if (query) {
                        var hay = ((z.zone_name || '') + ' ' + (z.zone_code || '') + ' ' +
                                   (z.zone_category || '') + ' ' + (z.zone_number != null ? z.zone_number : '') + ' ' +
                                   (z.service_name || '') + ' ' + destName).toLowerCase();
                        if (hay.indexOf(query) === -1) return;
                    }
                    rows.push(z);
                });
                if (!rows.length) return;

                function csvCell(v) {
                    var s = String(v == null ? '' : v).replace(/"/g, '""');
                    return '"' + s + '"';
                }

                var csv = '\uFEFFCountry,Zone Name,Zone Code,Category,Zone Number,Service\n';
                $.each(rows, function() {
                    var z = this;
                    csv += [destName, z.zone_name, z.zone_code, zoneCategoryMeta(z.zone_category).label,
                            z.zone_number, isSharedZone(z) ? 'Shared (All Services)' : (z.service_name || '')]
                        .map(csvCell).join(',') + '\n';
                });

                var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'zone-list-' + ($('#list_destination_id').val() || 'country') + '.csv';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        });
    </script>

</body>
</html>
