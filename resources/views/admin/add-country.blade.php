<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Country & Services</title>
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
        .step-badge.done { background: #198754; }
        .step-badge.muted { background: #adb5bd; }
        .svc-preview {
            border: 1px solid #e3eaf3;
            border-radius: 12px;
            background: linear-gradient(160deg, #f6faff 0%, #ffffff 60%);
        }
        .cov-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 50rem;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .cov-chip.has { background: #e9f9f1; color: #1e7f4f; border-color: #c6f0da; }
        .cov-chip.new { background: #eaf1ff; color: #0b5cd6; border-color: #cfe0ff; }
        .cov-chip.dup { background: #fef3e6; color: #a4760a; border-color: #ffe3a1; }
        #serviceTable { font-size: 13px; }
        #serviceTable thead th {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #6b7a90;
            background: #f2f6fc;
            border-bottom: 1px solid #dfe7f2 !important;
            white-space: nowrap;
            padding: 8px 12px;
        }
        #serviceTable tbody td { vertical-align: middle; padding: 7px 12px; }
        #serviceTable tbody tr { transition: background .12s; }
        #serviceTable tbody tr:hover td { background-color: #f5f9ff; }
        #serviceTable tbody tr.table-active td { background-color: #eaf1ff !important; }
        .select2-container--default .select2-selection--single { height: 38px; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
        /* Checkboxes inside the Select Countries dropdown */
        .cc-row { display: flex; align-items: center; gap: 8px; width: 100%; }
        .cc-box { pointer-events: none; cursor: pointer; margin-top: 0; flex-shrink: 0; }
        .cc-all { display: inline-flex; align-items: center; gap: 8px; }
        .cc-label { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .cc-code {
            font-size: 10px; font-weight: 700; letter-spacing: .3px;
            background: #eef2ff; color: #4f46e5;
            border-radius: 6px; padding: 1px 7px; flex-shrink: 0;
        }
        /* Sticky "All Countries" header pinned on top of the dropdown */
        .select2-results__option:has(.cc-all) {
            position: sticky; top: 0; z-index: 5;
            background: linear-gradient(135deg, #eef4ff, #f5f0ff) !important;
            border-bottom: 1px solid #e0e7ff;
            box-shadow: 0 2px 6px rgba(79, 70, 229, .08);
        }
        .select2-results__option .cc-row { padding: 1px 0; }
        /* Keep the selected box compact even with 250+ picks */
        .cc-picker-wrap { position: relative; }
        .cc-picker-wrap .select2-selection--multiple {
            min-height: 48px !important; max-height: 48px !important;
            overflow: hidden !important; border-radius: 12px !important;
            cursor: pointer;
        }
        .cc-picker-wrap .select2-selection__choice,
        .cc-picker-wrap .select2-search--inline { display: none !important; }
        .cc-picker-overlay {
            position: absolute; inset: 0; z-index: 2;
            display: flex; align-items: center; gap: 10px;
            padding: 0 14px; background: #fff;
            border: 1px solid #dee2e6; border-radius: 12px;
            pointer-events: none; font-size: 13px; color: #475569;
            transition: border-color .15s, box-shadow .15s;
        }
        .cc-picker-wrap:hover .cc-picker-overlay { border-color: #93c5fd; }
        .cc-picker-overlay .cc-pick-icon {
            width: 30px; height: 30px; border-radius: 9px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #eef4ff, #f5f0ff); color: #4f46e5;
            font-size: 15px;
        }
        .cc-picker-overlay .cc-pick-chev { margin-left: auto; color: #94a3b8; flex-shrink: 0; }
        .cc-picker-count {
            font-size: 11px; font-weight: 700; color: #fff;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 20px; padding: 2px 10px; flex-shrink: 0;
        }
        #countryCountBadge { font-size: 11px; vertical-align: middle; }
        /* Selected chips (collapsible) */
        .picked-chips {
            display: flex; flex-wrap: wrap; gap: 6px;
            max-height: 150px; overflow-y: auto;
        }
        .picked-chip {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 11.5px; font-weight: 600; color: #3730a3;
            background: #eef4ff; border: 1px solid #dbe4ff;
            border-radius: 20px; padding: 2px 6px 2px 10px;
        }
        .picked-x {
            border: none; background: #c7d2fe; color: #3730a3;
            width: 16px; height: 16px; border-radius: 50%;
            font-size: 11px; line-height: 1; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0;
        }
        .picked-x:hover { background: #a5b4fc; }
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
                        <h4 class="mb-1">Country &amp; Services</h4>
                        <p class="text-muted mb-0">First select a service, then pick countries to add into it. The service is cloned for each new country.</p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ url('/admin/manage-rate') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Back to Manage Rate
                        </a>
                        <a href="{{ route('admin.add-zone') }}" class="btn btn-outline-primary">
                            <i class="ti ti-map-pin-plus me-1"></i>Add Zone
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
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-alert-circle me-1"></i>{{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- Service-first Form -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="ti ti-world-plus me-1"></i>Add Countries to Service</h6>
                                <form id="addCountryForm" method="POST" action="{{ route('admin.add-country.store') }}">
                                    @csrf

                                    <!-- Step 1: Service (DISTINCT api_provider + service_code) -->
                                    <div class="mb-4">
                                        <h6 class="mb-2"><span class="step-badge" id="step1Badge">1</span>Select Service <span class="text-danger">*</span></h6>
                                        <select class="form-select" id="service_key" name="service_key" required>
                                            <option value="">— Search &amp; select a service —</option>
                                            @foreach($serviceOptions as $opt)
                                                @php
                                                    $okey = ($opt->api_provider ?? '') . '||' . ($opt->service_code ?? '');
                                                @endphp
                                                <option value="{{ $okey }}">
                                                    {{ $opt->api_provider ?? '—' }} — {{ $opt->service_code ?? '—' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <!-- <small class="text-muted">Source: <code>SELECT DISTINCT api_provider, service_code FROM courier_services</code>. The first matching row is used as the template and cloned for each country below.</small> -->

                                        <!-- Selected service preview -->
                                        <div class="svc-preview p-3 mt-3" id="servicePreview" style="display:none;">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <strong id="pvMethod">—</strong>
                                                <span class="badge bg-secondary" id="pvStatus">—</span>
                                            </div>
                                            <div class="text-muted small mt-1" id="pvMeta">—</div>
                                            <div class="mt-2 d-flex flex-wrap gap-1" id="pvCoverage"></div>
                                        </div>
                                    </div>

                                    <!-- Step 2: Countries -->
                                    <div class="mb-3" id="countrySection" style="display:none;">
                                        <h6 class="mb-2"><span class="step-badge muted" id="step2Badge">2</span>Select Countries <span class="text-danger">*</span> <span class="badge bg-primary-subtle text-primary" id="countryCountBadge" style="display:none;"></span></h6>
                                        <select class="form-select" id="country_codes" name="country_codes[]" multiple="multiple" data-placeholder="— Search & select countries to add —">
                                            <option value="__all__">All Countries (Select All)</option>
                                            @foreach($destinations as $dest)
                                                @php $cc = strtoupper(trim($dest->country_code ?: $dest->code)); @endphp
                                                <option value="{{ $cc }}">{{ $dest->name }} ({{ $cc }})</option>
                                            @endforeach
                                        </select>
                                        <div class="d-flex align-items-center justify-content-between mt-2 flex-wrap gap-2">
                                            <small class="text-muted" id="countryHint">Countries already covered by this service are disabled automatically.</small>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearCountriesBtn">Clear</button>
                                            </div>
                                        </div>
                                        <div class="alert alert-info py-2 mt-3 mb-0" id="summaryBox" style="display:none;">
                                            <i class="ti ti-info-circle me-1"></i><span id="summaryText"></span>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-3" id="submitBtn" disabled>
                                        <i class="ti ti-device-floppy me-1"></i>Add Countries to Service
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Coverage + overview -->
                    <div class="col-lg-7">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="mb-2"><i class="ti ti-badge-check me-1"></i>Service Coverage <span class="text-muted fw-normal" id="coverageTitle">— select a service to preview —</span></h6>
                                <div class="d-flex flex-wrap gap-1" id="coverageChips">
                                    <span class="text-muted small">No service selected yet.</span>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <h6 class="mb-0"><i class="ti ti-list me-1"></i>All Services ({{ count($serviceOptions) }} distinct)</h6>
                                    <div class="input-group" style="max-width:260px;">
                                        <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="serviceSearch" placeholder="Search provider, code...">
                                    </div>
                                </div>
                                <div class="table-responsive" style="max-height:520px;overflow:auto;">
                                    <table class="table table-hover table-sm mb-0" id="serviceTable">
                                        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
                                            <tr>
                                                <th>#</th>
                                                <th>API Provider</th>
                                                <th>Service Code</th>
                                                <th>Covered</th>
                                                <th>Sample Methods</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($serviceOptions as $i => $opt)
                                                @php
                                                    $skey = ($opt->api_provider ?? '') . '||' . ($opt->service_code ?? '');
                                                    $covered = isset($coverageMap[$skey]) ? count($coverageMap[$skey]) : 0;
                                                    $meta = $serviceMeta[$skey] ?? null;
                                                    $samples = $meta && !empty($meta['methods']) ? implode(', ', $meta['methods']) : '—';
                                                @endphp
                                                <tr data-service-key="{{ $skey }}" data-search="{{ strtolower(($opt->api_provider ?? '') . ' ' . ($opt->service_code ?? '')) }}">
                                                    <td>{{ $i + 1 }}</td>
                                                    <td><span class="badge bg-primary-subtle text-primary">{{ $opt->api_provider ?? '—' }}</span></td>
                                                    <td><span class="badge bg-light text-dark">{{ $opt->service_code ?? '—' }}</span></td>
                                                    <td><span class="badge bg-success-subtle text-success">{{ $covered }} countr{{ $covered === 1 ? 'y' : 'ies' }}</span></td>
                                                    <td class="text-muted small">{{ $samples }}</td>
                                                    <td><button type="button" class="btn btn-sm btn-outline-primary pick-service-btn" data-key="{{ $skey }}">Select</button></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <small class="text-muted d-block mt-2">Click <strong>Select</strong> on any row to load it into Step 1.</small>
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
            var serviceMeta = @json($serviceMeta ?? []);
            var coverageMap = @json($coverageMap ?? []);

            function escHtml(val) {
                return $('<span>').text(val == null ? '' : String(val)).html();
            }

            function serviceLabel(key) {
                var meta = serviceMeta[key];
                if (!meta) return key;
                return (meta.api_provider || '—') + ' — ' + (meta.service_code || '—');
            }

            $('#service_key').select2({
                width: '100%',
                placeholder: '— Search & select a service —',
                allowClear: true
            });

            $('#country_codes').select2({
                width: '100%',
                placeholder: $('#country_codes').data('placeholder'),
                allowClear: true,
                closeOnSelect: false,
                escapeMarkup: function(markup) { return markup; },
                templateResult: formatCountryDropdownOption,
                templateSelection: formatCountryDropdownSelection
            });

            // Turn the Select2 box into a clean picker button: hide the raw
            // pills and overlay a summary (clicks pass through to open it).
            var $ccBox = $('#country_codes').next('.select2-container');
            if (!$ccBox.parent().hasClass('cc-picker-wrap')) {
                $ccBox.wrap('<div class="cc-picker-wrap"></div>');
                $ccBox.after(
                    '<div class="cc-picker-overlay">' +
                    '<span class="cc-pick-icon"><i class="ti ti-world"></i></span>' +
                    '<span id="ccPickerText">Search &amp; select countries to add</span>' +
                    '<span class="cc-picker-count" id="ccPickerCount" style="display:none;"></span>' +
                    '<i class="ti ti-chevron-down cc-pick-chev"></i>' +
                    '</div>'
                );
            }

            function coveredCountriesFor(key) {
                return coverageMap[key] || [];
            }

            function refreshCoveragePanel(key) {
                if (!key || !serviceMeta[key]) {
                    $('#coverageTitle').text('— select a service to preview —');
                    $('#coverageChips').html('<span class="text-muted small">No service selected yet.</span>');
                    return;
                }
                var covered = coveredCountriesFor(key);
                var label = serviceLabel(key);
                $('#coverageTitle').text('— ' + covered.length + ' countr' + (covered.length === 1 ? 'y' : 'ies') + ' already covered by ' + label);
                if (!covered.length) {
                    $('#coverageChips').html('<span class="text-muted small">This service has no countries yet — everything you pick will be new.</span>');
                    return;
                }
                var html = '';
                covered.slice().sort().forEach(function(c) {
                    html += '<span class="cov-chip has"><i class="ti ti-check"></i>' + escHtml(c) + '</span>';
                });
                $('#coverageChips').html(html);
            }

            function refreshForm(key) {
                var meta = serviceMeta[key];
                if (!key || !meta) {
                    $('#servicePreview').hide();
                    $('#countrySection').hide();
                    $('#countryCountBadge').hide();
                    $('#step1Badge').removeClass('done');
                    $('#step2Badge').addClass('muted');
                    $('#submitBtn').prop('disabled', true);
                    $('#serviceTable tbody tr').removeClass('table-active');
                    return;
                }
                $('#step1Badge').addClass('done');

                // Preview card.
                $('#pvMethod').text(serviceLabel(key));
                var sub = 'Template: ' + (meta.method || '—') + (meta.network ? ' · Network: ' + meta.network : '') + ' · Rows: ' + (meta.total_rows || 0);
                $('#pvMeta').text(sub);
                $('#pvStatus').text(parseInt(meta.status, 10) === 1 ? 'Active' : 'Inactive')
                    .removeClass('bg-success bg-secondary')
                    .addClass(parseInt(meta.status, 10) === 1 ? 'bg-success' : 'bg-secondary');
                var covered = coveredCountriesFor(key);
                var covHtml = '<small class="text-muted fw-bold">Already covers (' + covered.length + '):</small> ';
                if (!covered.length) {
                    covHtml += '<small class="text-muted">none yet</small>';
                } else {
                    covered.slice().sort().slice(0, 20).forEach(function(c) {
                        covHtml += '<span class="cov-chip has">' + escHtml(c) + '</span>';
                    });
                    if (covered.length > 20) {
                        covHtml += '<small class="text-muted">+' + (covered.length - 20) + ' more</small>';
                    }
                }
                $('#pvCoverage').html(covHtml);
                $('#servicePreview').show();

                // Step 2: enable + disable already-covered countries.
                $('#countrySection').show();
                $('#step2Badge').removeClass('muted');
                var coveredSet = {};
                covered.forEach(function(c) { coveredSet[String(c).toUpperCase()] = true; });
                $('#country_codes option').each(function() {
                    var code = String($(this).val() || '').toUpperCase();
                    if (coveredSet[code]) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
                // Drop any now-disabled selections.
                var cur = $('#country_codes').val() || [];
                var kept = cur.filter(function(v) { return !coveredSet[String(v).toUpperCase()]; });
                if (kept.length !== cur.length) {
                    $('#country_codes').val(kept).trigger('change.select2');
                }
                $('#country_codes').trigger('change.select2');
                refreshCoveragePanel(key);
                updateSummary();

                // Highlight the row in the overview table.
                $('#serviceTable tbody tr').removeClass('table-active');
                $('#serviceTable tbody tr').filter(function() {
                    return String($(this).data('service-key')) === String(key);
                }).addClass('table-active');
            }

            var pickedOpen = false;

            function updateSummary() {
                var key = $('#service_key').val();
                var selected = ($('#country_codes').val() || []).filter(function(v) { return v !== '__all__'; });
                var badge = $('#countryCountBadge');
                var available = availableCountryValues().length;
                if (!key || !selected.length) {
                    $('#summaryBox').hide();
                    $('#submitBtn').prop('disabled', !(key && selected.length));
                    if (selected.length) { badge.text(selected.length + ' selected').show(); }
                    else { badge.hide(); }
                    setPickerOverlay(selected.length, available);
                    return;
                }
                badge.text(selected.length + ' selected').show();
                setPickerOverlay(selected.length, available);
                var pct = available > 0 ? Math.round(selected.length / available * 100) : 0;
                $('#summaryText').html(
                    '<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">' +
                    '<div class="flex-fill" style="min-width:170px;">' +
                    '<div class="fs-20 fw-bold lh-1">' + selected.length + ' <small class="text-muted fw-normal fs-13">/ ' + available + ' countries</small></div>' +
                    '<div class="progress mt-2" style="height:6px;"><div class="progress-bar" role="progressbar" style="width:' + pct + '%;background:linear-gradient(90deg,#6366f1,#8b5cf6);"></div></div>' +
                    '</div>' +
                    '<div class="d-flex gap-2">' +
                    '<button type="button" class="btn btn-sm btn-outline-primary" id="viewPickedBtn">' + (pickedOpen ? 'Hide' : 'View selected') + '</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-secondary" id="clearPickedBtn">Clear</button>' +
                    '</div></div>' +
                    '<div class="text-muted small mt-2">Will be added to <strong>' + escHtml(serviceLabel(key)) + '</strong>.</div>' +
                    '<div id="pickedChips" class="picked-chips mt-2"' + (pickedOpen ? '' : ' style="display:none;"') + '></div>'
                );
                $('#summaryBox').show();
                $('#submitBtn').prop('disabled', false);
                if (pickedOpen) renderPickedChips();
            }

            function setPickerOverlay(count, available) {
                var label = $('#ccPickerText');
                var pill = $('#ccPickerCount');
                if (!label.length) return;
                if (count > 0) {
                    label.html('<strong>' + count + '</strong>&nbsp;countr' + (count === 1 ? 'y' : 'ies') + ' selected');
                    pill.text(count + ' / ' + available).show();
                } else {
                    label.text('Search & select countries to add');
                    pill.hide();
                }
            }

            function renderPickedChips() {
                var box = $('#pickedChips');
                if (!box.length) return;
                var nameMap = {};
                $('#country_codes option').each(function() { nameMap[$(this).val()] = $(this).text(); });
                var vals = ($('#country_codes').val() || []).filter(function(v) { return v !== '__all__'; });
                if (!vals.length) {
                    box.html('<small class="text-muted">Nothing selected.</small>');
                    return;
                }
                box.html(vals.map(function(v) {
                    var label = String(nameMap[v] || v).replace(/\s*\([A-Za-z0-9]+\)\s*$/, '');
                    return '<span class="picked-chip">' + escHtml(label) + '<button type="button" class="picked-x" data-val="' + escHtml(v) + '" title="Remove">&times;</button></span>';
                }).join(''));
            }

            $(document).on('click', '#viewPickedBtn', function() {
                pickedOpen = !pickedOpen;
                $(this).text(pickedOpen ? 'Hide' : 'View selected');
                if (pickedOpen) {
                    renderPickedChips();
                    $('#pickedChips').slideDown(150);
                } else {
                    $('#pickedChips').slideUp(150);
                }
            });

            $(document).on('click', '#clearPickedBtn', function() {
                pickedOpen = false;
                $('#country_codes').val(null).trigger('change');
            });

            $(document).on('click', '.picked-x', function(e) {
                e.stopPropagation();
                var v = String($(this).data('val') || '');
                var vals = ($('#country_codes').val() || []).filter(function(x) { return x !== v && x !== '__all__'; });
                $('#country_codes').val(vals).trigger('change');
            });

            $('#service_key').on('change', function() {
                // Clear country picks when the template changes to avoid
                // accidentally adding stale selections to a new service.
                $('#country_codes').val(null).trigger('change');
                refreshForm(this.value);
            });

            $('#country_codes').on('change', function() {
                var vals = $(this).val() || [];
                // "__all__" is the virtual "All Countries" entry pinned on top
                // of the dropdown — expand it to every available country
                // (toggle: clears all when everything is already selected).
                // It is never submitted as a real country code.
                if (vals.indexOf('__all__') !== -1) {
                    var allVals = availableCountryValues();
                    var rest = vals.filter(function(v) { return v !== '__all__'; });
                    var allOn = allVals.length > 0 && allVals.every(function(v) { return rest.indexOf(v) !== -1; });
                    $(this).val(allOn ? [] : allVals).trigger('change');
                    return;
                }
                updateSummary();
                refreshAllBoxState();
            });

            // Values of all selectable (non-disabled) country options.
            function availableCountryValues() {
                var vals = [];
                $('#country_codes option').each(function() {
                    var v = $(this).val();
                    if (v && v !== '__all__' && !$(this).prop('disabled') && vals.indexOf(v) === -1) {
                        vals.push(v);
                    }
                });
                return vals;
            }

            // Checkbox in front of every country inside the dropdown + an
            // "All Countries" toggle entry on top (backed by __all__).
            function formatCountryDropdownOption(opt) {
                if (!opt.id) return opt.text;
                if (opt.id === '__all__') {
                    var allVals = availableCountryValues();
                    var cur = $('#country_codes').val() || [];
                    var clean = cur.filter(function(v) { return v !== '__all__'; });
                    var allOn = allVals.length > 0 && allVals.every(function(v) { return clean.indexOf(v) !== -1; });
                    var $all = $('<span class="cc-all"><input type="checkbox" class="form-check-input cc-box" id="ccAllBox" tabindex="-1"> <strong>All Countries</strong>&nbsp;<small class="text-muted">— select all available</small></span>');
                    $all.find('input').prop('checked', allOn);
                    return $all;
                }
                var disabled = opt.element && $(opt.element).prop('disabled');
                var $row = $('<span class="cc-row"><input type="checkbox" class="form-check-input cc-box" tabindex="-1" value="' + escHtml(opt.id) + '"> <span class="cc-label"></span><span class="cc-code"></span></span>');
                $row.find('input').prop('checked', !!opt.selected);
                if (disabled) $row.find('input').prop('disabled', true);
                // Option text is "Name (CC)" — show the name, move the code into a badge.
                var label = String(opt.text || '').replace(/\s*\([A-Za-z0-9]+\)\s*$/, '');
                $row.find('.cc-label').text(label || opt.text);
                $row.find('.cc-code').text(opt.id);
                return $row;
            }

            function formatCountryDropdownSelection(opt) {
                if (!opt.id || opt.id === '__all__') return opt.text;
                return opt.text;
            }

            // Keep every checkbox in the open dropdown in sync with the real
            // selection — Select2 only re-renders rows when the dropdown
            // re-opens, so after "All Countries" (or Clear) we tick/untick
            // the visible rows ourselves.
            function refreshAllBoxState() {
                var selected = $('#country_codes').val() || [];
                var selSet = {};
                selected.forEach(function(v) { selSet[String(v).toUpperCase()] = true; });
                var allVals = availableCountryValues();
                var clean = selected.filter(function(v) { return v !== '__all__'; });
                var allOn = allVals.length > 0 && allVals.every(function(v) { return clean.indexOf(v) !== -1; });
                document.querySelectorAll('ul.select2-results__options input.cc-box').forEach(function(box) {
                    if (box.id === 'ccAllBox' || box.getAttribute('value') === '__all__') {
                        box.checked = allOn;
                        return;
                    }
                    var v = box.getAttribute('value');
                    if (v != null) box.checked = !!selSet[String(v).toUpperCase()];
                });
            }

            // Live-tick the checkbox of the (un)selected country while the
            // dropdown stays open (closeOnSelect is false).
            $('#country_codes').on('select2:select select2:unselect', function(e) {
                var data = e.params && e.params.data ? e.params.data : null;
                if (!data || data.id === '__all__') return;
                if (data._resultId) {
                    var el = document.getElementById(data._resultId);
                    if (el) {
                        var box = el.querySelector('input.cc-box');
                        if (box) box.checked = (e.type === 'select2:select');
                    }
                }
            });

            $('#clearCountriesBtn').on('click', function() {
                $('#country_codes').val(null).trigger('change');
            });

            // "Select" buttons in the overview table load the service into Step 1.
            $(document).on('click', '.pick-service-btn', function() {
                var key = String($(this).data('key'));
                $('#service_key').val(key).trigger('change');
                $('html, body').animate({ scrollTop: $('#addCountryForm').offset().top - 80 }, 300);
            });

            // Live search for the overview table.
            $('#serviceSearch').on('input', function() {
                var q = ($(this).val() || '').toLowerCase().trim();
                $('#serviceTable tbody tr').each(function() {
                    var hay = $(this).data('search') || '';
                    $(this).toggle(!q || String(hay).indexOf(q) !== -1);
                });
            });

            $('#addCountryForm').on('submit', function(e) {
                if (!$('#service_key').val()) {
                    e.preventDefault();
                    alert('Please select a service first.');
                    return;
                }
                var selected = $('#country_codes').val() || [];
                if (!selected.length) {
                    e.preventDefault();
                    alert('Please select at least one country to add.');
                    return;
                }
                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Adding...');
            });

            // Preserve old input after validation errors.
            @if(old('service_key'))
                $('#service_key').val(@json(old('service_key'))).trigger('change');
            @endif
        });
    </script>

</body>
</html>
