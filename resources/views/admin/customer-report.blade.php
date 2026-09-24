<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Customer Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
        .report-hero {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            padding: 22px 24px;
            color: #fff;
            background: linear-gradient(135deg, #0a2a5e 0%, #0b5cd6 60%, #38bdf8 130%);
            box-shadow: 0 10px 28px rgba(11, 92, 214, 0.25);
        }
        .report-hero::before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            right: -70px;
            top: -100px;
            background: radial-gradient(circle, rgba(255,255,255,0.22) 0%, rgba(255,255,255,0) 70%);
        }
        .report-hero h4 { margin: 0; font-weight: 800; color: #fff; }
        .report-hero p { margin: 6px 0 0; color: rgba(255,255,255,0.85); font-size: 13px; }
        .report-hero-ic {
            position: relative; z-index: 1;
            width: 52px; height: 52px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 14px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.35);
            font-size: 26px; flex-shrink: 0;
        }
        .filter-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 3px 14px rgba(15, 23, 42, 0.06);
        }
        .filter-card .form-label { font-size: 12px; font-weight: 600; color: #52627a; margin-bottom: 5px; }
        .filter-card .form-control, .filter-card .form-select { min-height: 40px; border-radius: 9px; }
        .status-filters {
            display: flex; flex-wrap: nowrap; align-items: center;
            gap: 7px; overflow-x: auto; white-space: nowrap;
            padding: 3px 2px 7px; scrollbar-width: none;
        }
        .status-filters::-webkit-scrollbar { display: none; }
        .status-filters .status-btn {
            flex: 0 0 auto; display: inline-flex; align-items: center; justify-content: center;
            min-height: 34px; padding: 6px 12px;
            border: 1px solid #e7ebf3; border-radius: 10px;
            color: #52627a; background: #f8faff;
            font-size: 12px; font-weight: 600; text-decoration: none;
            transition: all .2s ease;
        }
        .status-filters .status-btn:hover { color: #0b5cd6; background: #eef4ff; border-color: #c9d9ff; }
        .status-filters .status-btn.active {
            color: #fff; background: linear-gradient(135deg, #0b5cd6, #38bdf8);
            border-color: #0b5cd6; box-shadow: 0 4px 10px rgba(11,92,214,.2);
        }
        .status-filters .status-btn .badge {
            min-width: 19px; height: 19px; display: inline-flex; align-items: center; justify-content: center;
            margin-left: 6px; padding: 2px 5px; border-radius: 6px; font-size: 10px; font-weight: 700;
        }
        .table-scroll-wrap {
            border: 1px solid #e5e7eb; border-radius: 12px;
            overflow: hidden; background: #fff;
        }
        .table-scroll-wrap .table-responsive { overflow-x: auto; }
        .table-scroll-wrap thead th {
            background-color: #f1f5f9; color: #334155;
            font-size: 11.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .4px;
            white-space: nowrap; vertical-align: middle; padding: 12px 14px;
            border-bottom: 2px solid #e2e8f0;
        }
        .table-scroll-wrap tbody td { vertical-align: middle; padding: 10px 14px; font-size: 12.5px; }
        .table-scroll-wrap tbody tr:nth-child(even) { background-color: #fafbfc; }
        .table-scroll-wrap tbody tr:hover { background-color: #f1f5f9; }
        .route-cell { white-space: normal; min-width: 240px; }
        .customer-cell { white-space: normal; min-width: 190px; }
        .consignee-cell { white-space: normal; min-width: 180px; }
        .status-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 700; padding: 3px 10px;
            border-radius: 50rem; white-space: nowrap;
        }
        .st-draft { background: #f1f5f9; color: #475569; }
        .st-ready { background: #dbeafe; color: #1d4ed8; }
        .st-packed { background: #e0e7ff; color: #3730a3; }
        .st-manifested { background: #ede9fe; color: #6d28d9; }
        .st-ready_for_pickup { background: #fef3c7; color: #92400e; }
        .st-assigned_for_pickup { background: #cffafe; color: #0e7490; }
        .st-received { background: #ccfbf1; color: #0f766e; }
        .st-dispatched { background: #e0f2fe; color: #0369a1; }
        .st-delivered { background: #d1fae5; color: #047857; }
        .st-cancelled { background: #f8d7da; color: #721c24; }
        .st-disputed { background: #fee2e2; color: #b91c1c; }
        .st-on_hold { background: #fef9c3; color: #854d0e; }
        .awb-badge { font-variant-numeric: tabular-nums; letter-spacing: .3px; }
        .sub-info { font-size: 11px; color: #6b7280; margin-top: 3px; line-height: 1.4; }
        .read-only-note {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600; color: #475569;
            background: #f8fafc; border: 1px solid #e2e8f0;
            padding: 6px 12px; border-radius: 999px; white-space: nowrap;
        }
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

            @php
                $customerMap = $customers->keyBy('id');
                $badgeClass = function ($st) {
                    $map = [
                        'draft' => 'st-draft', 'ready' => 'st-ready', 'packed' => 'st-packed',
                        'manifested' => 'st-manifested', 'ready_for_pickup' => 'st-ready_for_pickup',
                        'assigned_for_pickup' => 'st-assigned_for_pickup', 'confirm_pickup' => 'st-assigned_for_pickup',
                        'received' => 'st-received', 'dispatched' => 'st-dispatched',
                        'ready_to_dispatch' => 'st-dispatched', 'delivered' => 'st-delivered',
                        'cancelled' => 'st-cancelled', 'disputed' => 'st-disputed', 'on_hold' => 'st-on_hold',
                    ];
                    return $map[$st] ?? 'st-draft';
                };
                $prettyStatus = function ($st) use ($statusOptions) {
                    if ($st === 'ready_to_dispatch') return 'Dispatched';
                    if ($st === 'confirm_pickup') return 'In-Transit to Hub';
                    return $statusOptions[$st] ?? ucfirst(str_replace('_', ' ', (string) $st));
                };
            @endphp

            <!-- Hero : read-only report, koi action nahi -->
            <div class="report-hero d-flex align-items-center gap-3 mb-3 flex-wrap">
                <div class="report-hero-ic"><i class="ti ti-file-analytics"></i></div>
                <div class="flex-fill" style="position:relative;z-index:1;min-width:220px;">
                    <h4>Customer Report</h4>
                    <p>Customer-wise list of all shipments showing which step each shipment is currently at (same view as view-all-shipments). This page is view-only — no actions (pay / cancel / print / manifest / assign) can be performed from here.</p>
                </div>
                <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                    <span class="read-only-note" style="background:rgba(255,255,255,.15);border-color:rgba(255,255,255,.35);color:#fff;">
                        <i class="ti ti-eye"></i> Read-only — No actions
                    </span>
                    <a href="{{ route('admin.customer-report.export', request()->query()) }}" class="btn btn-success">
                        <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
                    </a>
                    <button type="button" class="btn btn-light" onclick="location.reload()" title="Refresh">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>

            <!-- Filters : customer-wise + status + awb + dates -->
            <div class="card filter-card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.customer-report') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Customer</label>
                                <select name="customer_id" class="form-select">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $cust)
                                        <option value="{{ $cust->id }}" @selected((string) request('customer_id') === (string) $cust->id)>
                                            {{ trim(($cust->first_name ?? '') . ' ' . ($cust->last_name ?? '')) ?: $cust->email }}@if($cust->customer_code) ({{ $cust->customer_code }})@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">HAWB Number</label>
                                <input type="search" name="awb_number" class="form-control" value="{{ request('awb_number') }}" placeholder="Enter AWB">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">Invoice No.</label>
                                <input type="search" name="invoice_number" class="form-control" value="{{ request('invoice_number') }}" placeholder="Invoice no.">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">Status (Step)</label>
                                <select name="status" class="form-select">
                                    @foreach($statusOptions as $val => $label)
                                        <option value="{{ $val }}" @selected(request('status', 'all') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-1 col-md-6">
                                <label class="form-label">From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-lg-1 col-md-6">
                                <label class="form-label">To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-lg-1 col-md-12 d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.customer-report') }}" class="btn btn-light" title="Reset"><i class="ti ti-refresh"></i></a>
                                <button type="submit" class="btn btn-primary" title="Search & Filter"><i class="ti ti-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Status step tabs with counts -->
            <div class="card filter-card mb-3">
                <div class="card-body py-2">
                    <div class="status-filters" aria-label="Shipment step filters">
                        @foreach($statusOptions as $val => $label)
                            <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => null]) }}"
                               class="status-btn {{ request('status', 'all') === $val ? 'active' : '' }}">
                                {{ $label }}
                                <span class="badge {{ request('status', 'all') === $val ? 'bg-light text-dark' : 'bg-secondary' }}">{{ $statusCounts[$val] ?? 0 }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Read-only table : view-all-shipment jaisa, bina Action column -->
            <div class="table-scroll-wrap">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>HAWB Number</th>
                                <th>Order Date</th>
                                <th>Customer</th>
                                <th>From / To</th>
                                <th>Consignee</th>
                                <th>Invoice No.</th>
                                <th>Amount</th>
                                <th>Manifest</th>
                                <th>Status (Step)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $index => $invoice)
                                @php
                                    $shipper = $invoice->shipperInfo;
                                    $consignee = $shipper ? $shipper->consigneeInfo : null;
                                    $manifest = $shipper ? $shipper->manifest : null;
                                    $rowStatus = 'draft';
                                    if (($invoice->status ?? '') === 'cancelled') {
                                        $rowStatus = 'cancelled';
                                    } elseif ($shipper && $shipper->status) {
                                        $rowStatus = $shipper->status;
                                    }
                                    $cust = $shipper && $shipper->customer_id ? ($customerMap->get($shipper->customer_id)) : null;
                                    $custName = $cust ? trim(($cust->first_name ?? '') . ' ' . ($cust->last_name ?? '')) : null;
                                @endphp
                                <tr>
                                    <td>{{ $invoices->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-dark awb-badge">{{ $shipper->awb_number ?? 'N/A' }}</span>
                                        @if($shipper && $shipper->company_name)
                                            <div class="sub-info">{{ $shipper->company_name }}</div>
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap;">
                                        <div>{{ $invoice->created_at ? $invoice->created_at->format('d-m-Y') : '-' }}</div>
                                        <div class="text-muted" style="font-size:11px;">{{ $invoice->created_at ? $invoice->created_at->format('h:i A') : '' }}</div>
                                    </td>
                                    <td class="customer-cell">
                                        <div class="fw-semibold">{{ $custName ?: 'N/A' }}</div>
                                        @if($cust)
                                            <div class="sub-info">{{ $cust->email ?? '' }}@if($cust->customer_code) • {{ $cust->customer_code }}@endif</div>
                                        @elseif($shipper && $shipper->customer_id)
                                            <div class="sub-info">ID: {{ $shipper->customer_id }}</div>
                                        @endif
                                    </td>
                                    <td class="route-cell">
                                        <div>{{ trim(($shipper->city ?? '-') . ', ' . ($shipper->state ?? '-') . ' - ' . ($shipper->pincode ?? '')) }}</div>
                                        <div class="text-muted"><i class="ti ti-arrow-down"></i> {{ $consignee ? trim(($consignee->city ?? '-') . ', ' . ($consignee->state ?? '-') . ' - ' . ($consignee->zip_code ?? '')) : '-' }}@if($consignee && $consignee->delivery_destination), {{ $consignee->delivery_destination }}@endif</div>
                                    </td>
                                    <td class="consignee-cell">
                                        <div class="fw-semibold">{{ $consignee->consignee_name ?? ($consignee->contact_person ?? 'N/A') }}</div>
                                        @if($consignee && $consignee->contact_person && $consignee->consignee_name)
                                            <div class="sub-info">{{ $consignee->contact_person }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->invoice_number ?? 'N/A' }}</td>
                                    <td style="font-weight:600;white-space:nowrap;">{{ $shipper && $shipper->total_price ? number_format((float) $shipper->total_price, 2) : '-' }}</td>
                                    <td>
                                        @if($manifest && $manifest->manifest_number)
                                            <span class="badge bg-secondary">{{ $manifest->manifest_number }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $badgeClass($rowStatus) }}">{{ $prettyStatus($rowStatus) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="ti ti-package" style="font-size:40px;color:#ccc;"></i>
                                        <p class="mt-2 mb-0">Koi shipment nahi mili. Filter badal kar dobara try karein.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($invoices->hasPages())
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-3 py-3">
                        <div class="text-muted" style="font-size:12.5px;">
                            Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} shipments
                        </div>
                        <div>{{ $invoices->links() }}</div>
                    </div>
                @endif
            </div>

            <p class="text-muted mt-2" style="font-size:12px;">
                <i class="ti ti-lock me-1"></i> Read-only report — is page par koi button/action nahi hai. Shipment par action lena ho to <a href="{{ url('/admin/companies') }}">View All Order</a> par jayein.
            </p>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/feather.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
</body>
</html>
