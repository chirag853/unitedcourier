<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Status Report | United Courier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .sr-hero {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            background: linear-gradient(115deg, #0a2a5e 0%, #0b5cd6 62%, #38bdf8 130%);
            color: #fff;
            box-shadow: 0 18px 40px -18px rgba(10, 42, 94, .55);
        }
        .sr-hero::before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            top: -110px;
            right: -70px;
            border-radius: 50%;
            border: 30px solid rgba(255, 255, 255, .07);
        }
        .sr-hero h4 { color: #fff; }
        .sr-hero .text-muted { color: rgba(255, 255, 255, .72) !important; }
        .sr-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 5px 14px 5px 6px;
            font-size: 12.5px;
            font-weight: 600;
            white-space: nowrap;
        }
        .sr-chip i {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .18);
            font-size: 14px;
        }
        .sr-filter-card {
            background: linear-gradient(160deg, #f6faff 0%, #ffffff 65%);
            border: 1px solid #e3eaf3;
            border-radius: 14px;
            box-shadow: 0 6px 18px -12px rgba(10, 42, 94, .25);
        }
        .sr-filter-card .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #52627a;
            margin-bottom: 5px;
        }
        .sr-filter-card .form-control,
        .sr-filter-card .form-select {
            min-height: 40px;
            border-radius: 9px;
        }
        .sr-filter-card .form-control:focus,
        .sr-filter-card .form-select:focus {
            border-color: #0b5cd6;
            box-shadow: 0 0 0 .2rem rgba(11, 92, 214, .15);
        }
        #statusReportTable thead th {
            background: linear-gradient(180deg, #0a2a5e, #14418f);
            color: #fff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            white-space: nowrap;
            border: none !important;
            padding: 11px 12px;
        }
        #statusReportTable thead th:first-child { border-radius: 10px 0 0 0; }
        #statusReportTable thead th:last-child { border-radius: 0 10px 0 0; }
        #statusReportTable tbody td {
            vertical-align: middle;
            font-size: 13px;
            padding: 9px 12px;
        }
        #statusReportTable tbody tr { transition: background .12s; }
        #statusReportTable tbody tr:hover td { background-color: #f2f7ff; }
        .sr-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 50rem;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">

        @include('customer.partials.customer_dashboard_header')

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

        <!-- Sidenav Menu Start -->
        @include('customer.partials.sidebar')
        <!-- Sidenav Menu End -->

        <div class="page-wrapper">
            <div class="content pb-0">

                <!-- Page Header -->
                <div class="sr-hero d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap p-4">
                    <div class="position-relative" style="z-index:1;">
                        <h4 class="mb-1"><i class="ti ti-report-analytics me-1"></i>Status Report</h4>
                        <p class="text-muted mb-2 small">Read-only overview of all shipments and their current status. No actions can be performed here.</p>
                        @php
                            $srTotal = $statusCounts['all'] ?? 0;
                            $srNotShipped = ($statusCounts['draft'] ?? 0) + ($statusCounts['cancelled'] ?? 0);
                            $srShipped = max(0, $srTotal - $srNotShipped);
                        @endphp
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="sr-chip"><i class="ti ti-apps"></i>{{ $srTotal }} total</span>
                            <span class="sr-chip"><i class="ti ti-truck-delivery"></i>{{ $srShipped }} shipped</span>
                            <span class="sr-chip"><i class="ti ti-circle-check"></i>{{ $statusCounts['delivered'] ?? 0 }} delivered</span>
                            <span class="sr-chip"><i class="ti ti-ban"></i>{{ $srNotShipped }} not shipped</span>
                        </div>
                    </div>
                    <div class="position-relative" style="z-index:1;">
                        <a href="{{ route('customer.status-report.export', request()->query()) }}" class="btn btn-success">
                            <i class="ti ti-file-spreadsheet me-1"></i>Export Excel
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-3">
                    <div class="card-body sr-filter-card">
                        <form method="GET" action="{{ route('customer.status-report') }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label" for="sr_status">Status</label>
                                    <select class="form-select" id="sr_status" name="status">
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" {{ request('status', 'all') === $value ? 'selected' : '' }}>
                                                {{ $label }}{{ $value !== 'all' ? ' (' . ($statusCounts[$value] ?? 0) . ')' : ' (' . ($statusCounts['all'] ?? 0) . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="sr_from">Date From</label>
                                    <input type="date" class="form-control" id="sr_from" name="date_from" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="sr_to">Date To</label>
                                    <input type="date" class="form-control" id="sr_to" name="date_to" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="sr_awb">AWB Number</label>
                                    <input type="text" class="form-control" id="sr_awb" name="awb_number" value="{{ request('awb_number') }}" placeholder="Search AWB...">
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="ti ti-filter me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('customer.status-report') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Clear Filters">
                                        <i class="ti ti-filter-x"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="card">
                    <div class="card-body">
                        @php
                            $srBadgeMap = [
                                'draft' => 'bg-secondary',
                                'ready' => 'bg-info',
                                'packed' => 'bg-primary',
                                'manifested' => 'bg-primary',
                                'ready_for_pickup' => 'bg-info',
                                'assigned_for_pickup' => 'bg-info',
                                'confirm_pickup' => 'bg-info',
                                'received' => 'bg-info',
                                'ready_to_dispatch' => 'bg-warning',
                                'dispatched' => 'bg-warning',
                                'delivered' => 'bg-success',
                                'disputed' => 'bg-danger',
                                'on_hold' => 'bg-warning',
                                'cancelled' => 'bg-dark',
                            ];
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="statusReportTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>AWB Number</th>
                                        <th>Date</th>
                                        <th>Shipper</th>
                                        <th>Consignee</th>
                                        <th>Destination</th>
                                        <th>Service</th>
                                        <th>Manifest No</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $idx => $invoice)
                                        @php
                                            $srShipper = $invoice->shipperInfo;
                                            $srConsignee = $srShipper ? $srShipper->consigneeInfo : null;
                                            $srManifest = $srShipper ? $srShipper->manifest : null;
                                            $srService = ($srShipper && $srShipper->serviceRate && $srShipper->serviceRate->service) ? $srShipper->serviceRate->service : null;
                                            $srStatus = ($invoice->status === 'cancelled' || ($srShipper->status ?? '') === 'cancelled')
                                                ? 'cancelled'
                                                : ($srShipper->status ?? 'draft');
                                            $srBadge = $srBadgeMap[$srStatus] ?? 'bg-secondary';
                                            $srAmount = ($srShipper && $srShipper->total_price !== null && (float) $srShipper->total_price > 0)
                                                ? (float) $srShipper->total_price
                                                : round((float) $invoice->invoiceItems->sum('amount'), 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $invoices->firstItem() + $idx }}</td>
                                            <td><span class="badge bg-light text-dark">{{ $srShipper->awb_number ?? '—' }}</span></td>
                                            <td>{{ $invoice->created_at ? $invoice->created_at->format('d-m-Y') : '—' }}</td>
                                            <td>{{ trim(($srShipper->company_name ?? '') . ' ' . ($srShipper->contact_person ?? '')) ?: '—' }}</td>
                                            <td>{{ $srConsignee->consignee_name ?? ($srConsignee->contact_person ?? '—') }}</td>
                                            <td>{{ $srConsignee->delivery_destination ?? '—' }}</td>
                                            <td>{{ $srService ? trim(($srService->network ?? '') . ' ' . ($srService->service_code ?? '')) : '—' }}</td>
                                            <td>{{ $srManifest->manifest_number ?? '—' }}</td>
                                            <td><span class="sr-status-badge {{ $srBadge }}">{{ ucwords(str_replace('_', ' ', $srStatus)) }}</span></td>
                                            <td>₹{{ number_format($srAmount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="ti ti-report-off fs-1 d-block mb-2"></i>
                                                No shipments found for the selected filters.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($invoices->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $invoices->links() }}
                            </div>
                        @endif
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
    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
</body>

</html>
