<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Wallet Transactions</title>
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
        .wallet-page-hero {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            padding: 22px 24px;
            color: #fff;
            background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);
            box-shadow: 0 10px 28px rgba(13, 148, 136, 0.25);
        }
        .wallet-page-hero::before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            right: -70px;
            top: -100px;
            background: radial-gradient(circle, rgba(255,255,255,0.22) 0%, rgba(255,255,255,0) 70%);
        }
        .wallet-page-hero h4 { margin: 0; font-weight: 800; }
        .wallet-page-hero p { margin: 6px 0 0; color: rgba(255,255,255,0.85); font-size: 13px; }
        .wallet-hero-ic {
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
        .wallet-stat-card {
            border: 1px solid #c9efe9;
            border-radius: 14px;
            box-shadow: 0 3px 14px rgba(15, 23, 42, 0.06);
        }
        .wallet-stat-ic {
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
            background-color: #f0fdfa;
            color: #115e59;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }
        .table-scroll-wrap table.dataTable tbody td {
            vertical-align: middle;
            padding: 10px 14px;
            font-size: 12.5px;
        }
        .amt { font-variant-numeric: tabular-nums; font-weight: 700; white-space: nowrap; }
        .amt-credit { color: #15803d; }
        .amt-debit { color: #b91c1c; }
        .desc-cell {
            min-width: 220px;
            max-width: 340px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
            line-height: 1.5;
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    @include('admin.partials.header')
    @include('admin.partials.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <!-- Hero -->
            <div class="wallet-page-hero d-flex align-items-center gap-3 mb-4 flex-wrap">
                <div class="wallet-hero-ic"><i class="ti ti-wallet"></i></div>
                <div class="flex-fill" style="position:relative;z-index:1;min-width:220px;">
                    <h4>Wallet Transactions</h4>
                    <p>Select a customer — all of their wallet transactions will appear in the table below.</p>
                </div>
                <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                    <button type="button" class="btn btn-light" onclick="location.reload()">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>

            <!-- Customer select -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-lg-6 col-md-8">
                            <label class="form-label fw-semibold" for="wallet_customer_select">Select Customer <span class="text-danger">*</span></label>
                            <select class="form-select" id="wallet_customer_select">
                                <option value="">-- Select customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">
                                        {{ trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? '')) ?: ('Customer #' . $c->id) }}{{ $c->email ? ' — ' . $c->email : '' }}{{ $c->phone_number ? ' (' . $c->phone_number . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-4">
                            <div id="wallet_customer_hint" class="text-muted small">Transactions load as soon as a customer is selected.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="row row-gap-3 mb-4" id="wallet_stats_row" style="display:none;">
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="card wallet-stat-card flex-fill mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <div><p class="text-muted mb-1">Current Balance</p><h3 class="mb-0" id="wallet_stat_balance">Rs 0.00</h3></div>
                            <span class="wallet-stat-ic" style="background:#f0fdfa;color:#0d9488;border:1px solid #99f6e4;"><i class="ti ti-wallet"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="card wallet-stat-card flex-fill mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <div><p class="text-muted mb-1">Total Credit</p><h3 class="mb-0" style="color:#15803d;" id="wallet_stat_credit">Rs 0.00</h3></div>
                            <span class="wallet-stat-ic" style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;"><i class="ti ti-arrow-down-left"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="card wallet-stat-card flex-fill mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <div><p class="text-muted mb-1">Total Debit</p><h3 class="mb-0" style="color:#b91c1c;" id="wallet_stat_debit">Rs 0.00</h3></div>
                            <span class="wallet-stat-ic" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;"><i class="ti ti-arrow-up-right"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
                    <div>
                        <h6 class="mb-1"><i class="ti ti-table me-1"></i>Wallet Transactions</h6>
                        <small class="text-muted" id="wallet_txn_count">Select a customer first</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-scroll-wrap p-2">
                        <table id="walletTransactionsTable" class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Reason</th>
                                    <th>Amount</th>
                                    <th>Balance After</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
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
    const escWt = function (v) { return $('<div>').text(v ?? '-').html(); };
    const fmtRs = function (n) {
        n = parseFloat(n) || 0;
        return 'Rs ' + n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
    const fmtDt = function (v) {
        if (!v) return '-';
        const d = new Date(v.replace(' ', 'T'));
        if (isNaN(d)) return escWt(v);
        const date = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        let h = d.getHours(), m = String(d.getMinutes()).padStart(2, '0');
        const ap = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return date + ', ' + h + ':' + m + ' ' + ap;
    };

    let wtTable = null;

    $(document).ready(function () {
        wtTable = $('#walletTransactionsTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            scrollX: true,
            language: {
                emptyTable: 'No transactions found — select a customer first',
                search: 'Search:',
                zeroRecords: 'No matching transactions found'
            }
        });

        $('#wallet_customer_select').on('change', function () {
            const customerId = $(this).val();
            if (!customerId) {
                wtTable.clear().draw();
                $('#wallet_stats_row').hide();
                $('#wallet_txn_count').text('Select a customer first');
                $('#wallet_customer_hint').text('Transactions load as soon as a customer is selected.');
                return;
            }
            $('#wallet_customer_hint').html('<span class="spinner-border spinner-border-sm me-1"></span> Loading transactions...');
            $.ajax({
                url: '{{ route("admin.wallet-transactions.data") }}',
                type: 'GET',
                data: { customer_id: customerId },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (!response || !response.success) {
                        $('#wallet_customer_hint').text(response.message || 'Could not load.');
                        return;
                    }
                    const txns = response.transactions || [];
                    $('#wallet_stats_row').show();
                    $('#wallet_stat_balance').text(fmtRs(response.balance));
                    $('#wallet_stat_credit').text(fmtRs(response.total_credit));
                    $('#wallet_stat_debit').text(fmtRs(response.total_debit));
                    $('#wallet_txn_count').text(txns.length + ' transactions found' + (response.has_wallet ? '' : ' (no wallet created yet)'));
                    $('#wallet_customer_hint').text($('#wallet_customer_select option:selected').text());

                    wtTable.clear();
                    txns.forEach(function (t, i) {
                        const isCredit = (t.type || '') === 'credit';
                        const amt = parseFloat(t.amount) || 0;
                        wtTable.row.add([
                            i + 1,
                            '<span style="white-space:nowrap;">' + fmtDt(t.created_at) + '</span>',
                            isCredit
                                ? '<span class="badge bg-success">Credit</span>'
                                : '<span class="badge bg-danger">Debit</span>',
                            '<span class="badge bg-secondary">' + escWt(t.reason) + '</span>',
                            '<span class="amt ' + (isCredit ? 'amt-credit' : 'amt-debit') + '">' + (isCredit ? '+' : '-') + fmtRs(amt) + '</span>',
                            '<span class="amt">' + fmtRs(t.balance_after) + '</span>',
                            escWt(t.reference),
                            '<span class="desc-cell d-inline-block">' + escWt(t.description) + '</span>'
                        ]);
                    });
                    wtTable.draw();
                },
                error: function (xhr) {
                    let msg = 'Could not load transactions.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    $('#wallet_customer_hint').text(msg);
                }
            });
        });
    });
</script>
</body>
</html>
