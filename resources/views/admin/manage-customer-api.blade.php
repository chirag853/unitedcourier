<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Manage Customer API</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .text-muted-sm { font-size:12px; color:#6c757d; }
        /* Page title icon */
        .page-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center;
            background:linear-gradient(135deg,#0d6efd 0%,#6610f2 100%); color:#fff; font-size:22px; box-shadow:0 4px 12px rgba(13,110,253,.35); flex:0 0 auto; }
        /* Stat cards */
        .stat-card { border:0; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
        .stat-card .card-body { padding:14px 16px; display:flex; align-items:center; gap:12px; }
        .stat-ico { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; flex:0 0 auto; }
        .stat-ico.blue { background:#e7f1ff; color:#0d6efd; }
        .stat-ico.violet { background:#f0e8ff; color:#6610f2; }
        .stat-ico.green { background:#e6f6ec; color:#198754; }
        .stat-ico.amber { background:#fff4e0; color:#b97a0c; }
        .stat-num { font-size:22px; font-weight:700; line-height:1.1; }
        .stat-lbl { font-size:12px; color:#6c757d; }
        /* Step badges on filter labels */
        .step-no { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%;
            background:#0d6efd; color:#fff; font-size:12px; font-weight:700; margin-right:6px; }
        .step-no.dim { background:#adb5bd; }
        .filter-card { border:0; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.08); }
        /* Toggle switch */
        .svc-switch { position:relative; display:inline-block; width:96px; height:30px; vertical-align:middle; cursor:pointer; padding:0; margin:0; border:0; background:transparent; border-radius:30px; font:inherit; }
        .svc-switch-track { position:absolute; inset:0; cursor:pointer; border-radius:30px; transition:background-color .2s; display:flex; align-items:center; justify-content:space-between; padding:0 10px; font-size:11px; font-weight:700; color:#fff; user-select:none; border:1px solid transparent; }
        .svc-switch-track.is-off { background-color:#dc3545; border-color:#c82333; }
        .svc-switch-track.is-on { background-color:#28a745; border-color:#1e7e34; }
        .svc-switch-knob { position:absolute; top:3px; left:3px; width:24px; height:24px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.3); transition:transform .2s; z-index:2; }
        .svc-switch.is-on .svc-switch-knob { transform:translateX(66px); }
        .svc-switch-track.is-on .off, .svc-switch-track.is-off .on { opacity:0; }
        .svc-switch-label { line-height:1; white-space:nowrap; transition:opacity .2s; z-index:1; }
        /* Provider pills */
        .pv { font-size:11px; font-weight:700; letter-spacing:.3px; text-transform:uppercase; padding:4px 10px; border-radius:20px; }
        .pv-ups { background:#e7f1ff; color:#0d6efd; }
        .pv-shipuniversal { background:#f0e8ff; color:#6610f2; }
        .pv-primus { background:#e0f2f1; color:#00796b; }
        .pv-overseas { background:#fff4e0; color:#b97a0c; }
        .pv-postshipping { background:#fce4ec; color:#c2185b; }
        .pv-flyingtigers { background:#e8f5e9; color:#2e7d32; }
        .pv-shipglobal { background:#e3f2fd; color:#1565c0; }
        .pv-self { background:#eceff1; color:#455a64; }
        .pv-other { background:#f1f3f5; color:#495057; }
        .ctry-pill { background:#f1f8ff; border:1px solid #cfe2ff; color:#084298; font-weight:600; font-size:12px; padding:3px 10px; border-radius:20px; white-space:nowrap; }
        /* Empty state */
        .empty-wrap { text-align:center; padding:44px 16px; }
        .empty-ico { width:72px; height:72px; border-radius:50%; background:#f0f6ff; color:#0d6efd; display:inline-flex; align-items:center; justify-content:center; font-size:34px; margin-bottom:14px; }
        /* Select2 height match */
        .select2-container--default .select2-selection--single { height:38px; border:1px solid #dee2e6; border-radius:6px; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:36px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; }
        .modal .select2-container { width:100% !important; }
    </style>
</head>

<body>
    <div class="main-wrapper">
        @include('admin.partials.header')
        @include('admin.partials.sidebar')

        <div class="page-wrapper">
            <div class="content pb-0">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-icon"><i class="ti ti-key"></i></div>
                        <div>
                            <h4 class="mb-1">Manage Customer API</h4>
                            <p class="text-muted-sm mb-0">Control which courier services each customer can use via API</p>
                        </div>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <button type="button" class="btn btn-success shadow" data-bs-toggle="modal" data-bs-target="#addAccessModal">
                            <i class="ti ti-plus me-1"></i>Add New Access
                        </button>
                    </div>
                </div>

                <!-- Stat Cards -->
                {{-- Stat tiles removed as requested --}}

                <!-- How it works -->
                <!-- <div class="alert alert-info d-flex align-items-start gap-2 mb-4" role="alert">
                    <i class="ti ti-info-circle fs-5 mt-1"></i>
                    <div class="small">
                        Customer select karo — uski saved <strong>customer_api</strong> mappings neeche table me aayengi.
                        Mapping nahi hai to <strong>open access</strong> (sab services). Pehli mapping save hote hi sirf
                        <strong>Enabled</strong> services us customer ki API (<code>/api/v1/services/{customer_code}</code>) pe dikhengi.
                    </div>
                </div> -->

                <!-- Filters -->
                <div class="card filter-card mb-4">
                    <div class="card-body">
                        <div class="row g-3 align-items-start">
                            <div class="col-md-4">
                                <label class="form-label fw-bold"><span class="step-no">1</span>Customer <span class="text-danger">*</span></label>
                                <select class="form-select searchable" id="fCustomer">
                                    <option value="">— Select Customer —</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ trim(($c->first_name ?? '').' '.($c->last_name ?? '')) }} — {{ $c->email }}@if($c->customer_code) ({{ $c->customer_code }})@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold"><span class="step-no dim">2</span>Service</label>
                                <select class="form-select searchable" id="fService">
                                    <option value="">— All Services —</option>
                                    @foreach($serviceGroups as $g)
                                        @php $gk = strtolower(trim($g->api_provider ?? '')).'||'.strtolower(trim($g->service_code ?? '')); $gc = $serviceCountryMap[$gk]['count'] ?? 0; @endphp
                                        <option value="{{ $gk }}">{{ strtoupper($g->api_provider ?? '') }} — {{ $g->service_code }} ({{ $g->method }}) — {{ $gc }} {{ $gc == 1 ? 'country' : 'countries' }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted"><span id="serviceCountryCount" class="fw-semibold"></span></small>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold"><span class="step-no dim">3</span>Country</label>
                                <select class="form-select searchable" id="fCountry">
                                    <option value="">— All Countries —</option>
                                    @foreach($countries as $ct)
                                        <option value="{{ $ct }}">{{ $ct }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted"><span id="countryHint"></span></small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold invisible" aria-hidden="true">Actions</label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary flex-fill" id="btnLoad">
                                        <i class="ti ti-search me-1"></i><span class="btn-label">Load Data</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="btnClear" title="Clear filters">
                                        <i class="ti ti-filter-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add New Access Modal -->
                <div class="modal fade" id="addAccessModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content" style="border-radius:14px; overflow:hidden;">
                            <div class="modal-header" style="background:linear-gradient(135deg,#198754 0%,#0d6efd 100%); color:#fff; border:0;">
                                <h5 class="modal-title text-white"><i class="ti ti-key me-2"></i>Grant API Access</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="alert alert-light border d-flex align-items-center gap-2 mb-3">
                                    <i class="ti ti-user-check text-success fs-5"></i>
                                    <div class="small">Mapping <span id="addCustomerName" class="fw-bold text-dark"></span> ke liye add hogi</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Customer <span class="text-danger">*</span></label>
                                        <select class="form-select" id="aCustomer">
                                            <option value="">— Select Customer —</option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c->id }}">{{ trim(($c->first_name ?? '').' '.($c->last_name ?? '')) }} — {{ $c->email }}@if($c->customer_code) ({{ $c->customer_code }})@endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label fw-bold">Service</label>
                                        <select class="form-select" id="aService">
                                            <option value="">— All Services —</option>
                                            @foreach($serviceGroups as $g)
                                                @php $gk2 = strtolower(trim($g->api_provider ?? '')).'||'.strtolower(trim($g->service_code ?? '')); $gc2 = $serviceCountryMap[$gk2]['count'] ?? 0; @endphp
                                                <option value="{{ $gk2 }}">{{ strtoupper($g->api_provider ?? '') }} — {{ $g->service_code }} ({{ $g->method }}) — {{ $gc2 }} {{ $gc2 == 1 ? 'country' : 'countries' }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted"><span id="addServiceCount" class="fw-semibold"></span></small>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold">Country</label>
                                        <select class="form-select" id="aCountry">
                                            <option value="">— All Countries —</option>
                                            @foreach($countries as $ct)
                                                <option value="{{ $ct }}">{{ $ct }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Access</label>
                                        <div class="d-flex gap-2" role="group" aria-label="Access status">
                                            <input type="radio" class="btn-check" name="aStatusRadio" id="aStatusOn" value="1" checked>
                                            <label class="btn btn-outline-success flex-fill" for="aStatusOn"><i class="ti ti-check me-1"></i>Enabled</label>
                                            <input type="radio" class="btn-check" name="aStatusRadio" id="aStatusOff" value="0">
                                            <label class="btn btn-outline-danger flex-fill" for="aStatusOff"><i class="ti ti-x me-1"></i>Disabled</label>
                                        </div>
                                        <select class="form-select d-none" id="aStatus">
                                            <option value="1">Enabled</option>
                                            <option value="0">Disabled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="alert alert-primary w-100 mb-0 py-2 px-3 small" role="alert">
                                            <i class="ti ti-sparkles me-1"></i><span id="addPreview" class="fw-semibold">select customer first</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-success px-4" id="btnAdd">
                                    <i class="ti ti-plus me-1"></i><span class="btn-label">Grant Access</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="card" id="resultCard" style="display:none;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h6 class="mb-0 fw-bold"><i class="ti ti-list-check me-1 text-primary"></i>Saved Mappings</h6>
                                <span id="resultInfo" class="badge bg-primary-subtle text-primary border"></span>
                                <span id="restrictedBadge" class="badge bg-warning ms-1" style="display:none;"><i class="ti ti-lock me-1"></i>Restricted</span>
                                <span id="openBadge" class="badge bg-success ms-1" style="display:none;"><i class="ti ti-lock-open me-1"></i>Open access</span>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-outline-success" id="btnEnableAll"><i class="ti ti-checks me-1"></i>Enable All</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnDisableAll"><i class="ti ti-x me-1"></i>Disable All</button>
                                <button type="button" class="btn btn-sm btn-primary" id="btnSave"><i class="ti ti-device-floppy me-1"></i>Save Changes</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="apiTable" class="table table-hover align-middle" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="36"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                        <th>ID</th>
                                        <th>Provider</th>
                                        <th>Method</th>
                                        <th>Network</th>
                                        <th>Service Code</th>
                                        <th>Country</th>
                                        <th>API Access</th>
                                        <th width="60">Remove</th>
                                    </tr>
                                </thead>
                                <tbody id="apiRows"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Initial hint -->
                <div class="card" id="hintCard">
                    <div class="card-body">
                        <div class="empty-wrap">
                            <div class="empty-ico"><i class="ti ti-user-search"></i></div>
                            <h6 class="fw-bold">Start by selecting a customer</h6>
                            <p class="text-muted small mb-3">Upar filter me customer chuno aur <strong>Load Data</strong> dabao — uski saved mappings yahi dikhengi.</p>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAccessModal">
                                <i class="ti ti-plus me-1"></i>Grant New Access
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.min.js"></script>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const DATA_URL = '{{ route("admin.manage-customer-api.data") }}';
        const SAVE_URL = '{{ route("admin.manage-customer-api.save") }}';
        const TOGGLE_URL = '{{ route("admin.manage-customer-api.toggle") }}';
        const ADD_URL = '{{ route("admin.manage-customer-api.add") }}';
        const PREVIEW_URL = '{{ route("admin.manage-customer-api.preview-add") }}';
        const REMOVE_URL = '{{ route("admin.manage-customer-api.remove") }}';
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let table = null;
        let pending = {}; // service_id -> 0/1 (dirty edits before Save)

        function toast(msg, type) {
            type = type || 'success';
            Swal.fire({ toast: true, position: 'top-end', timer: 2500, showConfirmButton: false, icon: type, title: msg });
        }

        function switchHtml(id, on) {
            return '<button type="button" class="svc-switch' + (on ? ' is-on' : '') + '" data-id="' + id + '">'
                + '<span class="svc-switch-track ' + (on ? 'is-on' : 'is-off') + '">'
                + '<span class="svc-switch-label on">Enabled</span><span class="svc-switch-label off">Disabled</span>'
                + '</span><span class="svc-switch-knob"></span></button>';
        }

        function providerPill(p) {
            const key = (p || '').toLowerCase().replace(/[^a-z]/g, '');
            const known = ['ups','shipuniversal','primus','overseas','postshipping','flyingtigers','shipglobal','self'];
            const cls = known.includes(key) ? 'pv-' + key : 'pv-other';
            return '<span class="pv ' + cls + '">' + (p || '-') + '</span>';
        }

        function setLoading(btn, on, label) {
            const b = $(btn);
            b.prop('disabled', on);
            b.find('.btn-label').text(on ? 'Please wait...' : label);
            b.find('.ti').toggleClass('ti-loader-2', on);
        }

        // Searchable dropdowns
        $('.searchable').select2({ width: '100%', placeholder: 'Select...' });

        // Service -> countries map (built server-side): key => {count, countries[]}.
        const SERVICE_COUNTRIES = @json($serviceCountryMap ?? []);
        const ALL_COUNTRIES = $('#fCountry option').map(function () { return $(this).val(); }).get().filter(Boolean);

        function refreshCountryOptions() {
            const key = $('#fService').val();
            const info = key ? SERVICE_COUNTRIES[key] : null;
            const list = info ? info.countries : ALL_COUNTRIES;
            const prev = $('#fCountry').val();
            const sel = $('#fCountry').empty().append('<option value="">— All Countries —</option>');
            list.forEach(function (c) { sel.append('<option value="' + c + '">' + c + '</option>'); });
            if (prev && list.includes(prev)) sel.val(prev);
            sel.trigger('change.select2');
            $('#serviceCountryCount').text(key ? (info ? info.count + (info.count == 1 ? ' country' : ' countries') + ' in this service' : '0 countries in this service') : '');
            $('#countryHint').text(key ? 'filtered by service' : '');
        }
        $('#fService').on('change', refreshCountryOptions);
        refreshCountryOptions();

        // Add modal: country dropdown me sirf us service ki countries.
        function refreshAddCountryOptions() {
            const key = $('#aService').val();
            const info = key ? SERVICE_COUNTRIES[key] : null;
            const list = info ? info.countries : ALL_COUNTRIES;
            const prev = $('#aCountry').val();
            const sel = $('#aCountry').empty().append('<option value="">— All Countries —</option>');
            list.forEach(function (c) { sel.append('<option value="' + c + '">' + c + '</option>'); });
            if (prev && list.includes(prev)) sel.val(prev);
            else if (key && list.length === 1) sel.val(list[0]);
            $('#addServiceCount').text(key ? (info ? info.count + (info.count == 1 ? ' country' : ' countries') + ' in this service' : '0 countries in this service') : '');
        }
        $('#aService').on('change', function () { refreshAddCountryOptions(); refreshAddPreview(); });

        function currentMappingCount() { return $('#apiRows tr[data-map="1"]').length; }

        $('#btnLoad').on('click', function () {
            const customerId = $('#fCustomer').val();
            if (!customerId) { toast('Please select a customer first.', 'warning'); return; }
            pending = {};
            setLoading(this, true, 'Load Data');
            $.get(DATA_URL, { customer_id: customerId, service_key: $('#fService').val(), country: $('#fCountry').val() })
                .done(function (res) {
                    $('#resultCard').show();
                    $('#hintCard').hide();
                    const uniqCountries = [...new Set(res.rows.map(r => r.country).filter(Boolean))];
                    const key = $('#fService').val();
                    const info = key ? SERVICE_COUNTRIES[key] : null;
                    let label = res.rows.length + ' mapping(s)';
                    if (uniqCountries.length) label += ' · ' + uniqCountries.length + (uniqCountries.length == 1 ? ' country' : ' countries');
                    if (info) label += ' · service covers ' + info.count + ' total';
                    $('#resultInfo').text(label);
                    $('#statMappings').text(res.rows.length);
                    $('#restrictedBadge').toggle(!!res.restricted);
                    $('#openBadge').toggle(!res.restricted);
                    const tb = $('#apiRows').empty();
                    if (!res.rows.length) {
                        tb.append('<tr><td colspan="9"><div class="empty-wrap">'
                            + '<div class="empty-ico"><i class="ti ti-database-off"></i></div>'
                            + '<h6 class="fw-bold">No mappings yet</h6>'
                            + '<p class="text-muted small mb-3">Is customer ke liye <strong>customer_api</strong> me koi row nahi — abhi open access hai.</p>'
                            + '<button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addAccessModal"><i class="ti ti-plus me-1"></i>Grant Access</button>'
                            + '</div></td></tr>');
                    }
                    res.rows.forEach(function (r) {
                        const on = !!r.api_allowed;
                        tb.append('<tr data-map="1">'
                            + '<td><input type="checkbox" class="row-check form-check-input" value="' + r.id + '"></td>'
                            + '<td><span class="text-muted">#' + r.id + '</span></td>'
                            + '<td>' + providerPill(r.api_provider) + '</td>'
                            + '<td><span class="fw-semibold">' + (r.method || '-') + '</span></td>'
                            + '<td class="text-muted">' + (r.network || '-') + '</td>'
                            + '<td><code>' + (r.service_code || '-') + '</code></td>'
                            + '<td><span class="ctry-pill">' + (r.country || '-') + '</span></td>'
                            + '<td data-switch-cell="' + r.id + '">' + switchHtml(r.id, on) + '</td>'
                            + '<td><button type="button" class="btn btn-sm btn-outline-danger btn-remove" data-id="' + r.id + '" title="Remove mapping"><i class="ti ti-trash"></i></button></td>'
                            + '</tr>');
                    });
                    if (table) { table.destroy(); table = null; }
                    if (res.rows.length) {
                        table = $('#apiTable').DataTable({ pageLength: 25, order: [[2, 'asc'], [3, 'asc']] });
                    }
                })
                .fail(function (xhr) {
                    toast((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to load data.', 'error');
                })
                .always(function () { setLoading('#btnLoad', false, 'Load Data'); });
        });

        $('#btnClear').on('click', function () {
            $('#fCustomer').val('').trigger('change.select2');
            $('#fService').val('').trigger('change.select2');
            refreshCountryOptions();
            pending = {};
            $('#resultCard').hide();
            $('#hintCard').show();
            $('#statMappings').text('—');
        });

        // Instant single toggle (saves immediately for that row)
        $(document).on('click', '.svc-switch', function () {
            const customerId = $('#fCustomer').val();
            const serviceId = $(this).data('id');
            const btn = $(this);
            btn.css('opacity', '.6');
            $.post(TOGGLE_URL, { _token: CSRF, customer_id: customerId, service_id: serviceId })
                .done(function (res) {
                    const on = res.status === 1;
                    btn.toggleClass('is-on', on);
                    btn.find('.svc-switch-track').toggleClass('is-on', on).toggleClass('is-off', !on);
                    delete pending[serviceId];
                    toast(res.message, 'success');
                })
                .fail(function () { toast('Toggle failed.', 'error'); })
                .always(function () { btn.css('opacity', ''); });
        });

        // Remove one mapping (with confirm)
        $(document).on('click', '.btn-remove', function () {
            const customerId = $('#fCustomer').val();
            const serviceId = $(this).data('id');
            Swal.fire({
                title: 'Remove this mapping?',
                text: 'Service #' + serviceId + ' will be removed from customer_api for this customer.',
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, remove it'
            }).then(function (result) {
                if (!result.isConfirmed) return;
                $.post(REMOVE_URL, { _token: CSRF, customer_id: customerId, service_id: serviceId })
                    .done(function (res) { toast(res.message || 'Removed.', res.success ? 'success' : 'warning'); $('#btnLoad').click(); })
                    .fail(function () { toast('Remove failed.', 'error'); });
            });
        });

        // ---- Add form: customer sync + preview count + add ----
        function addCustomerId() { return $('#aCustomer').val() || $('#fCustomer').val(); }
        function syncCustomerName() {
            const name = $('#aCustomer option:selected').text();
            $('#addCustomerName').text($('#aCustomer').val() ? '“' + name + '”' : '(customer select karo)');
        }
        $('#fCustomer').on('change', function () {
            if ($(this).val()) { $('#aCustomer').val($(this).val()); }
            syncCustomerName(); refreshAddPreview();
        });
        $('#aCustomer').on('change', function () {
            if ($(this).val()) { $('#fCustomer').val($(this).val()).trigger('change.select2'); }
            syncCustomerName(); refreshAddPreview();
        });
        syncCustomerName();

        // Sync segmented control with hidden select
        $('input[name="aStatusRadio"]').on('change', function () { $('#aStatus').val($(this).val()); });

        function refreshAddPreview() {
            const customerId = addCustomerId();
            if (!customerId) { $('#addPreview').text('select customer first'); return; }
            $.get(PREVIEW_URL, { customer_id: customerId, service_key: $('#aService').val(), country: $('#aCountry').val() })
                .done(function (res) { $('#addPreview').text(res.count + (res.count == 1 ? ' service' : ' services') + ' will be added'); })
                .fail(function () { $('#addPreview').text(''); });
        }
        $('#aService, #aCountry, #fCustomer, #aCustomer').on('change', refreshAddPreview);

        $('#btnAdd').on('click', function () {
            const customerId = addCustomerId();
            if (!customerId) { toast('Please select a customer first.', 'warning'); return; }
            setLoading(this, true, 'Grant Access');
            $.post(ADD_URL, {
                _token: CSRF,
                customer_id: customerId,
                service_key: $('#aService').val(),
                country: $('#aCountry').val(),
                status: $('input[name="aStatusRadio"]:checked').val() || $('#aStatus').val()
            }).done(function (res) {
                toast(res.message || 'Added.', 'success');
                refreshAddPreview();
                const modalEl = document.getElementById('addAccessModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
                $('#fCustomer').val(customerId).trigger('change.select2');
                $('#btnLoad').click();
            }).fail(function (xhr) {
                toast((xhr.responseJSON && xhr.responseJSON.message) || 'Add failed.', 'error');
            }).always(function () { setLoading('#btnAdd', false, 'Grant Access'); });
        });

        // Pre-fill modal from the filter when opened
        $('#addAccessModal').on('show.bs.modal', function () {
            if ($('#fCustomer').val() && !$('#aCustomer').val()) {
                $('#aCustomer').val($('#fCustomer').val());
            }
            if ($('#fService').val() && !$('#aService').val()) {
                $('#aService').val($('#fService').val());
            }
            refreshAddCountryOptions();
            if ($('#fCountry').val()) {
                const fv = $('#fCountry').val();
                if ($('#aCountry option[value="' + fv + '"]').length) $('#aCountry').val(fv);
            }
            syncCustomerName(); refreshAddPreview();
        });

        function setAll(on) {
            $('input.row-check').prop('checked', true);
            $('#apiRows tr[data-map="1"]').each(function () {
                const id = $(this).find('.row-check').val();
                if (!id) return;
                pending[id] = on ? 1 : 0;
                const cell = $(this).find('[data-switch-cell="' + id + '"] .svc-switch');
                cell.toggleClass('is-on', on);
                cell.find('.svc-switch-track').toggleClass('is-on', on).toggleClass('is-off', !on);
            });
        }
        $('#btnEnableAll').on('click', function () { setAll(true); });
        $('#btnDisableAll').on('click', function () { setAll(false); });
        $('#checkAll').on('change', function () { $('input.row-check').prop('checked', $(this).prop('checked')); });

        // Bulk save dirty + checked rows
        $('#btnSave').on('click', function () {
            const customerId = $('#fCustomer').val();
            if (!customerId) { toast('Please select a customer first.', 'warning'); return; }
            const rows = [];
            $('input.row-check:checked').each(function () {
                const id = $(this).val();
                if (pending[id] !== undefined) rows.push({ service_id: parseInt(id, 10), status: pending[id] });
            });
            // If nothing checked edited, save all pending edits
            if (!rows.length) {
                Object.keys(pending).forEach(function (id) { rows.push({ service_id: parseInt(id, 10), status: pending[id] }); });
            }
            if (!rows.length) { toast('No changes to save. Toggle a switch or use Enable/Disable All.', 'warning'); return; }
            const btn = this;
            setLoading(btn, true, 'Save Changes');
            $.ajax({
                url: SAVE_URL, method: 'POST', contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': CSRF },
                data: JSON.stringify({ customer_id: parseInt(customerId, 10), rows: rows })
            }).done(function (res) {
                pending = {};
                toast(res.message || 'Saved.', 'success');
                $('#btnLoad').click();
            }).fail(function (xhr) {
                toast((xhr.responseJSON && xhr.responseJSON.message) || 'Save failed.', 'error');
            }).always(function () { setLoading(btn, false, 'Save Changes'); });
        });
    </script>
</body>
</html>
