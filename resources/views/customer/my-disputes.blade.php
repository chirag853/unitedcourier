<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>My Disputes | United Courier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
        .card { background: #fff; border-radius: 20px; }
        .dispute-hero {
            border-radius: 16px; padding: 22px 24px; color: #fff;
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
            box-shadow: 0 10px 28px rgba(180, 83, 9, 0.25);
        }
        .dispute-hero h4 { margin: 0; font-weight: 800; }
        .dispute-hero p { margin: 6px 0 0; color: rgba(255,255,255,0.85); font-size: 13px; }
        .cond-cell { min-width: 200px; max-width: 320px; white-space: normal; overflow-wrap: anywhere; word-break: break-word; line-height: 1.5; }
        .amt { font-variant-numeric: tabular-nums; font-weight: 700; white-space: nowrap; }
        .amt-total { color: #b91c1c; font-weight: 800; }
    </style>
</head>

<body>
    <div class="main-wrapper">
        @include('customer.partials.customer_dashboard_header')

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

        @include('customer.partials.sidebar')

        <div class="page-wrapper">
            <div class="content pb-0">
                <div id="disputeAlert"></div>

                <div class="dispute-hero d-flex align-items-center gap-3 mb-4 flex-wrap">
                    <div style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:14px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.35);font-size:26px;flex-shrink:0;">
                        <i class="ti ti-alert-triangle"></i>
                    </div>
                    <div class="flex-fill" style="min-width:220px;">
                        <h4>My Disputes</h4>
                        <p>Admin dwara raise kiye gaye charges — remark padhkar Accept karein. Accept ke baad hi amount wallet se deduct hoga.</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <div>
                            <h6 class="mb-1"><i class="ti ti-table me-1"></i>Dispute Charges</h6>
                            <small class="text-muted">{{ count($disputes) }} records found</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="myDisputesTable" class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Raised On</th>
                                        <th>HAWB</th>
                                        <th>Charge Type</th>
                                        <th>Condition</th>
                                        <th>Admin Remark</th>
                                        <th>Total (incl. GST)</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($disputes as $i => $d)
                                        @php
                                            $currSym = ($d->currency ?? 'Rs') === '$' ? '$' : 'Rs ';
                                            $tot = $d->total_incl_gst !== null ? $currSym . number_format((float) $d->total_incl_gst, 2) : '-';
                                            $st = $d->status ?? 'applied';
                                            [$stLabel, $stColor, $stBg] = match ($st) {
                                                'applied' => ['Pending Your Acceptance', '#b45309', '#fef3c7'],
                                                'accepted' => ['Accepted', '#1d4ed8', '#dbeafe'],
                                                'cancelled' => ['Cancelled', '#4b5563', '#f3f4f6'],
                                                default => ['Deducted', '#15803d', '#dcfce7'],
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td style="white-space:nowrap;">{{ $d->created_at ? \Carbon\Carbon::parse($d->created_at)->format('d M Y, h:i A') : '-' }}</td>
                                            <td><span class="badge bg-dark">{{ $d->awb_number ?? 'N/A' }}</span></td>
                                            <td><span class="badge bg-danger-subtle text-danger border">{{ $d->charge_type ?? '-' }}</span></td>
                                            <td class="cond-cell">{{ $d->conditions ?? '-' }}</td>
                                            <td class="cond-cell"><strong>{{ $d->remark ?? '-' }}</strong></td>
                                            <td class="amt amt-total">{{ $tot }}</td>
                                            <td><span class="badge" style="background:{{ $stBg }};color:{{ $stColor }};border:1px solid currentColor;">{{ $stLabel }}</span></td>
                                            <td style="white-space:nowrap;">
                                                @if($st === 'applied')
                                                    <button type="button" class="btn btn-sm btn-success accept-dispute-btn" data-id="{{ $d->id }}">
                                                        <i class="ti ti-check me-1"></i> Accept
                                                    </button>
                                                @elseif($st === 'accepted')
                                                    <small class="text-muted">Accepted — deduction pending</small>
                                                @elseif($st === 'cancelled')
                                                    <small class="text-muted">Cancelled</small>
                                                @else
                                                    <small class="text-success">Deducted</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-5">
                                                <i class="ti ti-receipt-off fs-30 d-block mb-2"></i>
                                                No disputes raised on your shipments yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $('#myDisputesTable').DataTable({
                order: [[1, 'desc']],
                pageLength: 25,
                scrollX: true,
                language: { emptyTable: 'No disputes found', search: 'Search:', zeroRecords: 'No matching disputes found' }
            });

            function showDisputeAlert(msg, type) {
                $('#disputeAlert').html(
                    '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' + msg +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                );
            }

            $(document).on('click', '.accept-dispute-btn', function () {
                const $btn = $(this);
                const disputeId = $btn.data('id');
                if (!disputeId) return;
                if (!confirm('Ye dispute accept karne par admin amount aapke wallet se deduct kar sakega. Continue?')) return;
                const orig = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                $.ajax({
                    url: '{{ route("customer.accept-dispute") }}',
                    type: 'POST',
                    data: { dispute_id: disputeId },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        if (response && response.success) {
                            showDisputeAlert(response.message, 'success');
                            setTimeout(function () { location.reload(); }, 2000);
                        } else {
                            showDisputeAlert((response && response.message) || 'Could not accept.', 'danger');
                            $btn.prop('disabled', false).html(orig);
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Could not accept. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        showDisputeAlert(msg, 'danger');
                        $btn.prop('disabled', false).html(orig);
                    }
                });
            });
        });
    </script>
</body>
</html>
