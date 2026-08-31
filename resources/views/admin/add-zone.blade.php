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
                    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
                        <div class="me-2">
                            <i class="ti ti-download me-1"></i>
                            Some zone records were skipped during the last upload. You can download them below.
                        </div>
                        <a href="{{ route('admin.add-zone.skipped') }}" class="btn btn-sm btn-warning fw-bold text-nowrap">
                            <i class="ti ti-download me-1"></i>Download Skipped Records
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

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
                                                <label class="form-label fw-bold">Service <span class="text-muted">(optional)</span></label>
                                                <select class="form-select" id="service_id" name="service_id">
                                                    <option value="">— No Service —</option>
                                                    @foreach($services as $svc)
                                                        <option value="{{ $svc->id }}" data-country-id="{{ $serviceDestMap[$svc->id] ?? '' }}">
                                                            {{ $svc->method ?? ('Service #' . $svc->id) }}
                                                            @if(!empty($svc->service_code)) - ({{ $svc->service_code }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Only services for the selected country are shown. Optionally link these zones to a courier service (e.g. United Ground Premium).</small>
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
                                            <label class="form-label fw-bold">Service <span class="text-muted">(optional)</span></label>
                                            <select class="form-select" id="upload_service_id" name="service_id">
                                                <option value="">— No Service —</option>
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
        });
    </script>

</body>
</html>
