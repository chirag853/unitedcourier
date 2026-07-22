<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Courier Services</title>
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
        /* Sliding toggle switch for service enable/disable.
           The track is wide enough to show the "Enabled"/"Disabled" label
           on the opposite side of the knob. */
        /* The form wraps the switch button; keep it inline in the table cell. */
        .svc-switch-form {
            display: inline-block;
            margin: 0;
        }
        .svc-switch {
            position: relative;
            display: inline-block;
            width: 96px;
            height: 30px;
            flex: 0 0 auto;
            vertical-align: middle;
            cursor: pointer;
            /* Reset native <button> styling so the switch renders cleanly. */
            padding: 0;
            margin: 0;
            border: 0;
            background: transparent;
            border-radius: 30px;
            font: inherit;
        }
        .svc-switch:focus-visible {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
        }
        .svc-switch-track {
            position: absolute;
            inset: 0;
            cursor: pointer;
            border-radius: 30px;
            transition: background-color .2s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 10px;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            user-select: none;
            border: 1px solid transparent;
        }
        /* OFF state (disabled) — red, knob on the LEFT, "Disabled" label on the right */
        .svc-switch-track.is-off {
            background-color: #dc3545;
            border-color: #c82333;
        }
        .svc-switch-track.is-off:hover {
            background-color: #c82333;
        }
        /* ON state (enabled) — green, knob on the RIGHT, "Enabled" label on the left */
        .svc-switch-track.is-on {
            background-color: #28a745;
            border-color: #1e7e34;
        }
        .svc-switch-track.is-on:hover {
            background-color: #1e7e34;
        }
        .svc-switch-label {
            line-height: 1;
            white-space: nowrap;
            transition: opacity .2s ease-in-out;
            z-index: 1;
        }
        /* The label under the knob is hidden; the label on the empty side shows.
           ON  -> knob on right -> show "Enabled" (left),  hide "Disabled" (right).
           OFF -> knob on left  -> show "Disabled" (right), hide "Enabled" (left). */
        .svc-switch-track.is-on .svc-switch-label.off,
        .svc-switch-track.is-off .svc-switch-label.on {
            opacity: 0;
        }
        .svc-switch-knob {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,.3);
            transition: transform .2s ease-in-out;
            z-index: 2;
        }
        /* Knob slides to the right when ON */
        .svc-switch.is-on .svc-switch-knob {
            transform: translateX(66px);
        }
        .svc-switch.is-busy .svc-switch-track {
            cursor: progress;
            opacity: .7;
        }
        .info-note {
            background: #f0f6ff;
            border: 1px solid #cfe2ff;
            border-radius: 8px;
            padding: 12px 16px;
            color: #084298;
            font-size: 13px;
        }
        .text-muted-sm {
            font-size: 12px;
            color: #6c757d;
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
                        <h4 class="mb-1">Courier Services</h4>
                        <p class="text-muted-sm mb-0">Enable or disable the courier services that show rates to customers.</p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>

                <!-- Info Note -->
                <div class="info-note mb-4">
                    <i class="ti ti-info-circle me-1"></i>
                    <strong>Enabled</strong> services show their rates to customers on the create-shipment page, bulk upload and bulk rate calculation.
                    <strong>Disabled</strong> services are hidden from all rate calculations. You can re-enable a disabled service at any time.
                    Use the toggle switch in the Status column to flip a service on/off &mdash; the knob slides to the right (green, <strong>Enabled</strong>) when on, and to the left (red, <strong>Disabled</strong>) when off.
                </div>

                <!-- Services Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="servicesTable" class="table table-hover datatable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Network</th>
                                        <th>Method</th>
                                        <th>Service Code</th>
                                        <th>Country</th>
                                        <th>TAT</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $service)
                                    <tr id="service-row-{{ $service->id }}">
                                        <td>{{ $service->id }}</td>
                                        <td>{{ $service->network ?? '-' }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $service->method ?? '-' }}</span>
                                            @if(!empty($service->real_name))
                                                <br><span class="text-muted-sm">{{ $service->real_name }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $service->service_code ?? '-' }}</td>
                                        <td>{{ $service->country ?? '-' }}</td>
                                        <td>{{ $service->tat ?? '-' }}</td>
                                        <td>{{ $service->description ?? '-' }}</td>
                                        <td>
                                            @php $isEnabled = (int) ($service->status ?? 1) === 1; @endphp
                                            <form action="{{ route('admin.services.toggle-status', $service->id) }}"
                                                method="POST"
                                                class="d-inline svc-switch-form"
                                                data-service-name="{{ $service->method ?? ('Service #' . $service->id) }}"
                                                data-currently-enabled="{{ $isEnabled ? '1' : '0' }}">
                                                @csrf
                                                <button type="submit"
                                                    class="svc-switch {{ $isEnabled ? 'is-on' : '' }}"
                                                    title="{{ $isEnabled ? 'This service is currently ENABLED. Click to disable it.' : 'This service is currently DISABLED. Click to enable it.' }}">
                                                    <span class="svc-switch-track {{ $isEnabled ? 'is-on' : 'is-off' }}">
                                                        <span class="svc-switch-label on">Enabled</span>
                                                        <span class="svc-switch-label off">Disable</span>
                                                    </span>
                                                    <span class="svc-switch-knob"></span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/slimscrollbar/slimscrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.min.js"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialise DataTable
        $(function () {
            $('#servicesTable').DataTable({
                order: [[1, 'asc'], [2, 'asc']],
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: [6, 7] } // description + status not sortable
                ]
            });
        });

        // SweetAlert2 confirmation before DISABLING a service.
        // Enabling a disabled service goes through immediately (no prompt),
        // since that is a safe, non-destructive action. Disabling an enabled
        // service is intercepted so the admin confirms it is intentional —
        // a disabled service stops showing rates to customers.
        $(document).on('submit', '.svc-switch-form', function (e) {
            var $form = $(this);
            var currentlyEnabled = $form.data('currently-enabled') == 1;
            var serviceName = $form.data('service-name') || 'this service';

            // Only confirm when the destructive action (disable) is requested.
            if (!currentlyEnabled) {
                return; // enabling — let the form submit normally
            }

            e.preventDefault();
            Swal.fire({
                title: 'Disable this service?',
                html: '<strong>' + $('<div>').text(serviceName).html() + '</strong> will no longer show rates to customers '
                    + 'on the create-shipment page, bulk upload and bulk rate calculation. '
                    + 'You can re-enable it at any time.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, disable it',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $form[0].submit();
                }
            });
        });

        // Show the Laravel flash "success" message (set after a toggle) as a
        // transient toast at the top-right of the page.
        $(function () {
            @if (session('success'))
                showServiceToast('{{ addslashes(session('success')) }}', 'success');
            @endif
        });

        /**
         * Simple transient toast shown at the top-right of the page.
         */
        function showServiceToast(message, type) {
            type = type || 'success';
            var bg = type === 'success' ? '#28a745' : (type === 'warning' ? '#ffc107' : '#dc3545');
            var color = type === 'warning' ? '#212529' : '#fff';
            var $toast = $('<div></div>')
                .text(message)
                .css({
                    position: 'fixed',
                    top: '20px',
                    right: '20px',
                    zIndex: 9999,
                    background: bg,
                    color: color,
                    padding: '12px 18px',
                    borderRadius: '6px',
                    boxShadow: '0 4px 12px rgba(0,0,0,.15)',
                    fontSize: '13px',
                    fontWeight: 500,
                    maxWidth: '360px'
                });
            $('body').append($toast);
            setTimeout(function () { $toast.fadeOut(300, function () { $(this).remove(); }); }, 3000);
        }
    </script>

</body>
</html>
