<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Un Manifest Report | United Courier</title>
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
        .shipment-status-filters {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 7px !important;
            overflow-x: auto;
            white-space: nowrap;
            padding: 3px 2px 7px;
            scrollbar-width: none;
        }
        .shipment-status-filters::-webkit-scrollbar { display: none; }
        .shipment-status-filters .status-filter-btn {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 6px 12px !important;
            border: 1px solid #e7ebf3;
            border-radius: 10px !important;
            color: #52627a;
            background: #f8faff;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1px;
            line-height: 1.2;
            text-decoration: none;
            transition: color .2s ease, background-color .2s ease, border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }
        .shipment-status-filters .status-filter-btn:hover {
            color: #2f66f3;
            background: #eef4ff;
            border-color: #c9d9ff;
            transform: translateY(-1px);
        }
        .shipment-status-filters .status-filter-btn.btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #2f66f3, #4f87ff);
            border-color: #2f66f3;
            box-shadow: 0 4px 10px rgba(47, 102, 243, .2);
        }
        .shipment-status-filters .status-filter-btn .badge {
            min-width: 19px;
            height: 19px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 6px !important;
            padding: 2px 5px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
        }
        .shipment-status-filters .status-filter-btn.btn-primary .badge {
            color: #2f66f3 !important;
            background: rgba(255, 255, 255, .95) !important;
        }
        #unManifestReportTable thead th {
            background: linear-gradient(180deg, #0a2a5e, #14418f);
            color: #fff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            white-space: nowrap;
            border: none !important;
            padding: 11px 12px;
        }
        #unManifestReportTable thead th:first-child { border-radius: 10px 0 0 0; }
        #unManifestReportTable thead th:last-child { border-radius: 0 10px 0 0; }
        #unManifestReportTable tbody td {
            vertical-align: middle;
            font-size: 13px;
            padding: 9px 12px;
        }
        #unManifestReportTable tbody tr { transition: background .12s; }
        #unManifestReportTable tbody tr:hover td { background-color: #f2f7ff; }
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
                <div class="sr-hero d-flex align-items-center justify-content-between gap-3 mb-3 flex-wrap p-4">
                    <div class="position-relative" style="z-index:1;">
                        <h4 class="mb-1"><i class="ti ti-package-off me-1"></i>Un Manifest Report</h4>
                        <p class="text-muted mb-2 small">Read-only overview of un-manifested orders (draft, ready, packed). No actions can be performed here.</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="sr-chip"><i class="ti ti-apps"></i>{{ $statusCounts['all'] ?? 0 }} total</span>
                            <span class="sr-chip"><i class="ti ti-file"></i>{{ $statusCounts['draft'] ?? 0 }} draft</span>
                            <span class="sr-chip"><i class="ti ti-circle-check"></i>{{ $statusCounts['ready'] ?? 0 }} ready</span>
                            <span class="sr-chip"><i class="ti ti-package"></i>{{ $statusCounts['packed'] ?? 0 }} packed</span>
                        </div>
                    </div>
                    <div class="position-relative" style="z-index:1;">
                        <a href="{{ route('customer.un-manifest-report.export', request()->query()) }}" class="btn btn-success">
                            <i class="ti ti-file-spreadsheet me-1"></i>Export Excel
                        </a>
                    </div>
                </div>

                <!-- Status Tabs (view-all-shipments style, limited to un-manifested) -->
                <div class="card border-0 shadow-sm rounded-4 mb-2">
                    <div class="card-body p-3">
                        <div class="shipment-status-filters" aria-label="Un-manifest status filters">
                            @foreach($statusOptions as $value => $label)
                                <a href="{{ request()->fullUrlWithQuery(['status' => $value, 'page' => null]) }}"
                                   class="btn {{ request('status', 'all') === $value ? 'btn-primary' : 'btn-light' }} rounded-pill status-filter-btn">
                                    {{ $label }} <span class="badge {{ request('status', 'all') === $value ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $statusCounts[$value] ?? 0 }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card border-0 shadow-sm rounded-4 mb-2">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('customer.un-manifest-report') }}">
                            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Customer / Consignee</label>
                                    <input type="search" name="customer_name" class="form-control" value="{{ request('customer_name') }}" placeholder="Customer name">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Shipper Name</label>
                                    <input type="search" name="shipper_name" class="form-control" value="{{ request('shipper_name') }}" placeholder="Company or contact">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">HAWB Number</label>
                                    <input type="search" name="awb_number" class="form-control" value="{{ request('awb_number') }}" placeholder="Enter AWB">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-lg-2 col-md-4 d-flex gap-2">
                                    <a href="{{ route('customer.un-manifest-report', ['status' => request('status', 'all')]) }}" class="btn btn-light flex-fill"><i class="ti ti-refresh me-1"></i>Reset</a>
                                    <button type="submit" class="btn btn-primary flex-fill"><i class="ti ti-search me-1"></i>Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Table (read-only, no Action column) -->
                <div class="card border shadow">
                    <div class="card-body">
                        @php
                            $srBadgeMap = [
                                'draft' => 'bg-secondary',
                                'ready' => 'bg-info',
                                'packed' => 'bg-primary',
                            ];
                        @endphp
                        @if($invoices->isEmpty())
                            <div class="text-center py-5">
                                <i class="ti ti-package" style="font-size:48px;color:#ccc;"></i>
                                <p class="mt-3 text-muted">No un-manifested orders found for the selected filters.</p>
                                <a href="{{ route('customer.un-manifest-report', ['status' => request('status', 'all')]) }}" class="btn btn-primary">Clear Filters</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="unManifestReportTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>HAWB Number</th>
                                            <th>Order Date</th>
                                            <th>Shipper</th>
                                            <th>Consignee</th>
                                            <th>Destination</th>
                                            <th>Service</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $idx => $invoice)
                                            @php
                                                $srShipper = $invoice->shipperInfo;
                                                $srConsignee = $srShipper ? $srShipper->consigneeInfo : null;
                                                $srService = ($srShipper && $srShipper->serviceRate && $srShipper->serviceRate->service) ? $srShipper->serviceRate->service : null;
                                                $srStatus = $srShipper->status ?? 'draft';
                                                $srBadge = $srBadgeMap[$srStatus] ?? 'bg-secondary';
                                                $srAmount = ($srShipper && $srShipper->total_price !== null && (float) $srShipper->total_price > 0)
                                                    ? (float) $srShipper->total_price
                                                    : round((float) $invoice->invoiceItems->sum('amount'), 2);
                                                $orderDateSource = $srStatus === 'draft'
                                                    ? $invoice->created_at
                                                    : ($srShipper->updated_at ?? $invoice->updated_at);
                                            @endphp
                                            <tr>
                                                <td>{{ $invoices->firstItem() + $idx }}</td>
                                                <td>
                                                    @if($srShipper && $srShipper->awb_number)
                                                        <span class="badge bg-dark">{{ $srShipper->awb_number }}</span>
                                                    @else
                                                        <strong>{{ $invoice->invoice_number }}</strong>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($orderDateSource)
                                                        <div>{{ date('d M Y', strtotime($orderDateSource)) }}</div>
                                                        <div class="text-muted small">{{ date('h:i A', strtotime($orderDateSource)) }}</div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ trim(($srShipper->company_name ?? '') . ' ' . ($srShipper->contact_person ?? '')) ?: '—' }}</td>
                                                <td>{{ $srConsignee->consignee_name ?? ($srConsignee->contact_person ?? '—') }}</td>
                                                <td>{{ $srConsignee->delivery_destination ?? '—' }}</td>
                                                <td>{{ $srService ? trim(($srService->network ?? '') . ' ' . ($srService->service_code ?? '')) : '—' }}</td>
                                                <td><span class="sr-status-badge {{ $srBadge }}">{{ ucwords(str_replace('_', ' ', $srStatus)) }}</span></td>
                                                <td>₹{{ number_format($srAmount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($invoices->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $invoices->links() }}
                                </div>
                            @endif
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
    <!-- Main theme JS initializes sidebar dropdowns -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
</body>

</html>
