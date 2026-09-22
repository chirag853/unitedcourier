<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Dispute Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
        .dispute-page-hero {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            padding: 22px 24px;
            color: #fff;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            box-shadow: 0 10px 28px rgba(220, 38, 38, 0.25);
        }
        .dispute-page-hero::before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            right: -70px;
            top: -100px;
            background: radial-gradient(circle, rgba(255,255,255,0.22) 0%, rgba(255,255,255,0) 70%);
        }
        .dispute-page-hero h4 { margin: 0; font-weight: 800; }
        .dispute-page-hero p { margin: 6px 0 0; color: rgba(255,255,255,0.85); font-size: 13px; }
        .dispute-hero-ic {
            position: relative;
            z-index: 1;
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.35);
            font-size: 26px;
            flex-shrink: 0;
        }
        .dispute-stat-card {
            border: 1px solid #f1d5d5;
            border-radius: 14px;
            box-shadow: 0 3px 14px rgba(15, 23, 42, 0.06);
        }
        .dispute-stat-ic {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 22px;
            flex-shrink: 0;
        }
        .table-scroll-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }
        .table-scroll-wrap table.dataTable thead th {
            background-color: #fef2f2;
            color: #7f1d1d;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 14px;
            border-bottom: 2px solid #fecaca;
        }
        .table-scroll-wrap table.dataTable tbody td {
            vertical-align: middle;
            padding: 10px 14px;
            font-size: 12.5px;
        }
        .cond-cell {
            min-width: 220px;
            max-width: 320px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
            line-height: 1.5;
        }
        .awb-badge {
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.3px;
        }
        .amt { font-variant-numeric: tabular-nums; font-weight: 700; white-space: nowrap; }
        .amt-total { color: #b91c1c; font-weight: 800; }
    </style>
</head>
<body>
<div class="main-wrapper">
    @include('admin.partials.header')
    @include('admin.partials.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
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

            <!-- Hero -->
            <div class="dispute-page-hero d-flex align-items-center gap-3 mb-4 flex-wrap">
                <div class="dispute-hero-ic"><i class="ti ti-alert-triangle"></i></div>
                <div class="flex-fill" style="position:relative;z-index:1;min-width:220px;">
                    <h4>Dispute Orders</h4>
                    <p>Apply Dispute Charge modal se applied saare orders — GST-inclusive totals ke saath.</p>
                </div>
                <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                    <a href="{{ url('/admin/companies') }}" class="btn btn-light">
                        <i class="ti ti-arrow-left me-1"></i> All Orders
                    </a>
                    <button type="button" class="btn btn-light" onclick="location.reload()">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="row row-gap-3 mb-4">
                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="card dispute-stat-card flex-fill mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <div><p class="text-muted mb-1">Total Disputes</p><h3 class="mb-0">{{ count($disputes) }}</h3></div>
                            <span class="dispute-stat-ic" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;"><i class="ti ti-receipt"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="card dispute-stat-card flex-fill mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <div><p class="text-muted mb-1">Base Amount</p><h3 class="mb-0">Rs {{ number_format($totalBase, 2) }}</h3></div>
                            <span class="dispute-stat-ic" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;"><i class="ti ti-wallet"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="card dispute-stat-card flex-fill mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <div><p class="text-muted mb-1">Total GST</p><h3 class="mb-0">Rs {{ number_format($totalGst, 2) }}</h3></div>
                            <span class="dispute-stat-ic" style="background:#fffbeb;color:#d97706;border:1px solid #fde68a;"><i class="ti ti-percent"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="card dispute-stat-card flex-fill mb-0" style="border-color:#fca5a5;background:linear-gradient(135deg,#fff7f7,#fef2f2);">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <!-- <div><p class="text-muted mb-1">Total (incl. GST)</p><h3 class="mb-0" style="color:#b91c1c;">Rs {{ number_format($totalIncl, 2) }}</h3></div> -->
                            <span class="dispute-stat-ic" style="background:#dc2626;color:#fff;"><i class="ti ti-calculator"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
                    <div>
                        <h6 class="mb-1"><i class="ti ti-table me-1"></i>Applied Dispute Charges</h6>
                        <small class="text-muted">{{ count($disputes) }} records found</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-scroll-wrap p-2">
                        <table id="disputeOrdersTable" class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Applied On</th>
                                    <th>HAWB / Invoice</th>
                                    <th>Customer</th>
                                    <th>Route</th>
                                    <th>Charge Type</th>
                                    <th>Condition</th>
                                    <th>Remark</th>
                                    <th>Calc</th>
                                    <th>Boxes</th>
                                    <th>Base</th>
                                    <th>GST</th>
                                    <th>Total (incl. GST)</th>
                                    <th>Status</th>
                                    <th>Applied By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($disputes as $i => $d)
                                    @php
                                        $cust = trim(($d->first_name ?? '') . ' ' . ($d->last_name ?? '')) ?: 'N/A';
                                        $currSym = ($d->currency ?? 'Rs') === '$' ? '$' : 'Rs ';
                                        $fmt = function ($n) use ($currSym) {
                                            if ($n === null) return '-';
                                            $n = (float) $n;
                                            return $currSym . (fmod($n, 1) == 0 ? number_format($n, 0) : number_format($n, 2));
                                        };
                                        $st = $d->status ?? 'applied';
                                        [$stLabel, $stColor, $stBg] = match ($st) {
                                            'applied' => ['Applied', '#b45309', '#fef3c7'],
                                            'accepted' => ['Accepted', '#1d4ed8', '#dbeafe'],
                                            'cancelled' => ['Cancelled', '#4b5563', '#f3f4f6'],
                                            default => ['Deducted', '#15803d', '#dcfce7'],
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td style="white-space:nowrap;">{{ $d->created_at ? \Carbon\Carbon::parse($d->created_at)->format('d M Y, h:i A') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-dark awb-badge">{{ $d->awb_number ?? 'N/A' }}</span><br>
                                            <small class="text-muted">{{ $d->invoice_number ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $cust }}</strong><br>
                                            <small class="text-muted">{{ $d->shipper_company ?? '-' }}</small>
                                        </td>
                                        <td style="font-size:12px;white-space:normal;min-width:160px;">
                                            {{ $d->shipper_city ?? '-' }}, {{ $d->shipper_state ?? '-' }}
                                            <i class="ti ti-arrow-right mx-1 text-muted"></i>
                                            {{ $d->consignee_city ?? '-' }}, {{ $d->consignee_state ?? '-' }}
                                            <br><small class="text-muted">{{ $d->consignee_destination ?? '' }}</small>
                                        </td>
                                        <td style="min-width:150px;"><span class="badge bg-danger-subtle text-danger border">{{ $d->charge_type ?? '-' }}</span></td>
                                        <td class="cond-cell">{{ $d->conditions ?? '-' }}</td>
                                        <td class="cond-cell">{{ $d->remark ?? '-' }}</td>
                                        <td><span class="badge bg-secondary">{{ $d->calculation_type ?? '-' }}</span></td>
                                        <td class="text-center">{{ $d->boxes ?? '—' }}</td>
                                        <td class="amt">{{ $fmt($d->base_amount) }}</td>
                                        <td class="amt">+{{ $fmt($d->gst_amount) }}<br><small class="text-muted">({{ rtrim(rtrim($d->gst_percentage, '0'), '.') }}%)</small></td>
                                        <td class="amt amt-total">{{ $fmt($d->total_incl_gst) }}</td>
                                        <td><span class="badge" style="background:{{ $stBg }};color:{{ $stColor }};border:1px solid currentColor;">{{ $stLabel }}</span></td>
                                        <td><small>{{ $d->applied_by_name ?? ('#' . ($d->applied_by ?? '-')) }}</small></td>
                                        <td style="white-space:nowrap;">
                                            @if($st === 'accepted')
                                                <button type="button" class="btn btn-sm btn-outline-primary custom-amount-btn me-1" data-id="{{ $d->id }}" data-awb="{{ $d->awb_number ?? '' }}" data-base="{{ $d->base_amount ?? '' }}" data-gst="{{ $d->gst_percentage ?? 0 }}" data-currency="{{ $d->currency ?? 'Rs' }}" title="Set custom amount">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger deduct-dispute-btn me-1" data-id="{{ $d->id }}" title="Deduct from customer wallet">
                                                    <i class="ti ti-wallet me-1"></i> Deduct
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary cancel-dispute-btn" data-id="{{ $d->id }}" title="Cancel dispute (no refund)">
                                                    <i class="ti ti-ban"></i>
                                                </button>
                                            @elseif($st === 'applied')
                                                <button type="button" class="btn btn-sm btn-outline-primary custom-amount-btn me-1" data-id="{{ $d->id }}" data-awb="{{ $d->awb_number ?? '' }}" data-base="{{ $d->base_amount ?? '' }}" data-gst="{{ $d->gst_percentage ?? 0 }}" data-currency="{{ $d->currency ?? 'Rs' }}" title="Set custom amount">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary cancel-dispute-btn me-1" data-id="{{ $d->id }}" title="Cancel dispute (no refund)">
                                                    <i class="ti ti-ban"></i>
                                                </button>
                                                <small class="text-muted">Pending customer</small>
                                            @elseif($st === 'cancelled')
                                                <small class="text-muted">Cancelled</small>
                                            @else
                                                <small class="text-success">Deducted</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="16" class="text-center text-muted py-5">
                                            <i class="ti ti-receipt-off fs-30 d-block mb-2"></i>
                                            No disputes applied yet.<br>
                                            <small>Press <b>Dispute</b> on any shipment on the admin/companies page, then Apply.</small>
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

<!-- Custom Amount Modal -->
<div class="modal fade" id="customAmountModal" tabindex="-1" aria-labelledby="customAmountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customAmountModalLabel">
                    <i class="ti ti-edit me-1"></i> Set Custom Amount
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="customAmountAlert" class="alert d-none"></div>
                <input type="hidden" id="custom_dispute_id" value="">
                <div class="mb-3">
                    <label class="form-label fw-semibold">HAWB Number</label>
                    <p class="mb-0"><span id="custom_awb_display" class="badge bg-dark">-</span></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="custom_amount_input">Custom Amount (Incl. GST) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="custom_amount_input" min="0.01" step="0.01" placeholder="e.g. 500">
                    <!-- <small class="text-muted" id="custom_gst_note">GST rule ke hisaab se alag se lagega.</small> -->
                </div>
                <!-- <div class="alert alert-info mb-0" id="custom_total_preview">Total (incl. GST): -</div> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="customAmountSaveBtn">
                    <i class="ti ti-check me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Dispute Confirm Modal -->
<div class="modal fade" id="cancelDisputeModal" tabindex="-1" aria-labelledby="cancelDisputeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="cancelDisputeModalLabel">
                    <i class="ti ti-ban me-2"></i>Cancel Dispute
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">This dispute will be cancelled and the shipment will be moved to Cancelled.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                <button type="button" class="btn btn-danger" id="confirmCancelDisputeBtn">
                    <i class="ti ti-ban me-1"></i>Yes, Cancel Dispute
                </button>
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
        // Empty-state row (colspan) par DataTable init karne se tn/4 warning
        // aati hai, isliye init sirf tab jab real data rows hon.
        if ($('#disputeOrdersTable tbody td[colspan]').length === 0) {
            $('#disputeOrdersTable').DataTable({
                order: [[1, 'desc']],
                pageLength: 25,
                scrollX: true,
                language: {
                    emptyTable: 'No dispute orders found',
                    search: 'Search:',
                    zeroRecords: 'No matching disputes found'
                }
            });
        }

        // Custom Amount modal: open, live total preview, save.
        let customGstPct = 0;
        let customCurrency = 'Rs';

        function customTotalPreview() {
            const base = parseFloat($('#custom_amount_input').val()) || 0;
            const gst = Math.round(base * customGstPct) / 100;
            const total = Math.round((base + gst) * 100) / 100;
            $('#custom_total_preview').text(
                'Total (incl. GST ' + customGstPct + '%): ' + customCurrency + ' ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            );
        }

        $(document).on('click', '.custom-amount-btn', function () {
            const $btn = $(this);
            $('#custom_dispute_id').val($btn.data('id'));
            $('#custom_awb_display').text($btn.data('awb') || '-');
            $('#custom_amount_input').val($btn.data('base') || '');
            customGstPct = parseFloat($btn.data('gst')) || 0;
            customCurrency = $btn.data('currency') || 'Rs';
            $('#custom_gst_note').text('GST ' + customGstPct + '% rule ke hisaab se alag se lagega.');
            $('#customAmountAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');
            customTotalPreview();
            $('#customAmountModal').modal('show');
        });

        $(document).on('input', '#custom_amount_input', customTotalPreview);

        $(document).on('click', '#customAmountSaveBtn', function () {
            const $btn = $(this);
            const disputeId = $('#custom_dispute_id').val();
            const amount = parseFloat($('#custom_amount_input').val());
            if (!disputeId || isNaN(amount) || amount <= 0) {
                $('#customAmountAlert').removeClass('d-none alert-success').addClass('alert-danger').text('Please enter a valid amount greater than 0.');
                return;
            }
            const orig = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
            $.ajax({
                url: '{{ route("admin.update-dispute-amount") }}',
                type: 'POST',
                data: { dispute_id: disputeId, custom_amount: amount },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response && response.success) {
                        $('#customAmountAlert').removeClass('d-none alert-danger').addClass('alert-success').text(response.message);
                        setTimeout(function () { location.reload(); }, 2000);
                    } else {
                        $('#customAmountAlert').removeClass('d-none alert-success').addClass('alert-danger').text((response && response.message) || 'Could not save.');
                        $btn.prop('disabled', false).html(orig);
                    }
                },
                error: function (xhr) {
                    let msg = 'Could not save. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    $('#customAmountAlert').removeClass('d-none alert-success').addClass('alert-danger').text(msg);
                    $btn.prop('disabled', false).html(orig);
                }
            });
        });

        // Cancel a pending dispute via confirm popup. No wallet refund —
        // dispute + shipment go to Cancelled.
        let pendingCancelDisputeId = null;

        $(document).on('click', '.cancel-dispute-btn', function () {
            const disputeId = $(this).data('id');
            if (!disputeId) return;
            pendingCancelDisputeId = disputeId;
            $('#cancelDisputeModal').modal('show');
        });

        // Popup dismissed without confirming -> drop the pending request.
        $('#cancelDisputeModal').on('hidden.bs.modal', function () {
            pendingCancelDisputeId = null;
        });

        // Popup "Yes, Cancel Dispute" -> run the cancel request.
        $(document).on('click', '#confirmCancelDisputeBtn', function () {
            $('#cancelDisputeModal').modal('hide');
            const disputeId = pendingCancelDisputeId;
            pendingCancelDisputeId = null;
            if (!disputeId) return;
            const $btn = $('.cancel-dispute-btn[data-id="' + disputeId + '"]');
            const orig = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            $.ajax({
                url: '{{ route("admin.cancel-dispute-charge") }}',
                type: 'POST',
                data: { dispute_id: disputeId },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response && response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert((response && response.message) || 'Could not cancel.');
                        $btn.prop('disabled', false).html(orig);
                    }
                },
                error: function (xhr) {
                    let msg = 'Could not cancel. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    alert(msg);
                    $btn.prop('disabled', false).html(orig);
                }
            });
        });

        // Deduct an accepted dispute from the customer wallet.
        $(document).on('click', '.deduct-dispute-btn', function () {
            const $btn = $(this);
            const disputeId = $btn.data('id');
            if (!disputeId) return;
            if (!confirm('Ye accepted dispute amount customer wallet se deduct ho jayega. Continue?')) return;
            const orig = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            $.ajax({
                url: '{{ route("admin.deduct-dispute-charge") }}',
                type: 'POST',
                data: { dispute_id: disputeId },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response && response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert((response && response.message) || 'Could not deduct.');
                        $btn.prop('disabled', false).html(orig);
                    }
                },
                error: function (xhr) {
                    let msg = 'Could not deduct. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    alert(msg);
                    $btn.prop('disabled', false).html(orig);
                }
            });
        });
    });
</script>
</body>
</html>
