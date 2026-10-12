<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Not Implemented API Data</title>
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
        .ni-page-hero {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            padding: 22px 24px;
            color: #fff;
            background: linear-gradient(135deg, #7c3aed 0%, #2f66f3 100%);
            box-shadow: 0 10px 28px rgba(124, 58, 237, 0.25);
        }
        .ni-page-hero::before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            right: -70px;
            top: -100px;
            background: radial-gradient(circle, rgba(255,255,255,0.22) 0%, rgba(255,255,255,0) 70%);
        }
        .ni-page-hero h4 { margin: 0; font-weight: 800; }
        .ni-page-hero p { margin: 6px 0 0; color: rgba(255,255,255,0.85); font-size: 13px; }
        .ni-hero-ic {
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
        .ni-stat-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 3px 14px rgba(15, 23, 42, 0.06);
        }
        .ni-stat-ic {
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
            background-color: #f3f4f6;
            color: #374151;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 14px;
            border-bottom: 2px solid #d1d5db;
        }
        .table-scroll-wrap table.dataTable tbody td {
            vertical-align: middle;
            padding: 10px 14px;
            font-size: 12.5px;
        }
        .awb-badge {
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.3px;
        }
        .amt { font-variant-numeric: tabular-nums; font-weight: 700; white-space: nowrap; }
    </style>
</head>
<body>
<div class="main-wrapper">
    @include('admin.partials.header')
    @include('admin.partials.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <!-- Hero -->
            <div class="ni-page-hero d-flex align-items-center gap-3 mb-4 flex-wrap">
                <div class="ni-hero-ic"><i class="ti ti-plug-off"></i></div>
                <div class="flex-fill" style="position:relative;z-index:1;min-width:220px;">
                    <h4 style="color:white">Not Implemented API Data</h4>
                    <p>
                        All shipments whose service code is NOT_IMPLEMENTED are processed and manifested internally, without any carrier API integration.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                    <a href="{{ url('/admin/companies') }}" class="btn btn-light">
                        <i class="ti ti-arrow-left me-1"></i> All Orders
                    </a>
                    <a href="{{ route('admin.not-implemented-orders.export') }}" class="btn btn-light">
                        <i class="ti ti-download me-1"></i> Export Excel
                    </a>
                    <button type="button" class="btn btn-light" onclick="location.reload()">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="row row-gap-3 mb-4">
                <div class="col-lg-6 col-md-6 d-flex">
                    <div class="card ni-stat-card flex-fill mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <div><p class="text-muted mb-1">Total Orders</p><h3 class="mb-0">{{ count($orders) }}</h3></div>
                            <span class="ni-stat-ic" style="background:#f1eafe;color:#7c3aed;border:1px solid #c7d2fe;"><i class="ti ti-package"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 d-flex">
                    <div class="card ni-stat-card flex-fill mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <div><p class="text-muted mb-1">Total Amount</p><h3 class="mb-0">Rs {{ number_format($totalAmount, 2) }}</h3></div>
                            <span class="ni-stat-ic" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;"><i class="ti ti-wallet"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
                    <div>
                        <h6 class="mb-1"><i class="ti ti-table me-1"></i>Not Implemented Orders</h6>
                        <small class="text-muted">{{ count($orders) }} records found</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-scroll-wrap p-2">
                        <table id="notImplementedTable" class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order Date</th>
                                    <th>HAWB / Invoice</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Status / Manifest</th>
                                    <th>Route</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $i => $o)
                                    @php
                                        $cust = trim(($o->first_name ?? '') . ' ' . ($o->last_name ?? '')) ?: 'N/A';
                                    @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td style="white-space:nowrap;">{{ $o->order_date ? \Carbon\Carbon::parse($o->order_date)->format('d M Y, h:i A') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-dark awb-badge">{{ $o->awb_number ?? 'N/A' }}</span><br>
                                            <small class="text-muted">{{ $o->invoice_number ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $cust }}</strong><br>
                                            <small class="text-muted">{{ $o->customer_code ?? $o->shipper_company ?? '-' }}</small>
                                        </td>
                                        <td style="font-size:12px;">
                                            {{ $o->service_method ?? $o->shipping_method ?? '-' }}<br>
                                            <small class="text-muted">{{ $o->service_provider ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($o->shipper_status ?? '-') }}</span><br>
                                            <small class="text-muted">{{ $o->manifest_number ?? '-' }}</small>
                                        </td>
                                        <td style="font-size:12px;white-space:normal;min-width:160px;">
                                            {{ $o->shipper_city ?? '-' }}, {{ $o->shipper_state ?? '-' }}
                                            <i class="ti ti-arrow-right mx-1 text-muted"></i>
                                            {{ $o->consignee_city ?? '-' }}, {{ $o->consignee_state ?? '-' }}
                                            <br><small class="text-muted">{{ $o->consignee_destination ?? '' }}</small>
                                        </td>
                                        <td class="amt">Rs {{ number_format((float) ($o->total_price ?? 0), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <i class="ti ti-plug-off fs-30 d-block mb-2"></i>
                                            No not-implemented orders found.
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
        if ($('#notImplementedTable tbody td[colspan]').length === 0) {
            $('#notImplementedTable').DataTable({
                order: [[1, 'desc']],
                pageLength: 25,
                scrollX: true,
                language: {
                    emptyTable: 'No not-implemented orders found',
                    search: 'Search:',
                    zeroRecords: 'No matching orders found'
                }
            });
        }
    });
</script>
</body>
</html>
