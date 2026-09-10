<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - All COD Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View all Cash on Delivery (COD) orders for United Courier">
    <meta name="keywords" content="COD, cash on delivery, orders, courier, logistics">
    <meta name="robots" content="index, follow">
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
        .cod-count-card {
            border-radius: 10px;
            border: 1px solid #e9ecef;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow 0.15s ease;
            text-decoration: none;
            color: inherit;
        }
        .cod-count-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            color: inherit;
        }
        .cod-count-icon {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 22px;
        }
        .cod-count-num {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.1;
        }
        .cod-count-label {
            font-size: 13px;
            color: #6c757d;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
        }
        .status-badge-draft { background-color: #fff3cd; color: #856404; }
        .status-badge-manifested { background-color: #cfe2ff; color: #084298; }
        .status-badge-cod { background-color: #d4edda; color: #155724; }
        .status-badge-other { background-color: #e2e3e5; color: #383d41; }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter { margin-bottom: 12px; }
        .dataTables_wrapper .dataTables_info { margin-top: 8px; }
        .dataTables_wrapper .dataTables_paginate { margin-top: 8px; }
    </style>
</head>
<body>
<div class="main-wrapper">
    @include('admin.partials.header')
    <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
        <div class="offcanvas-body">
            <div class="card shadow-none mb-0">
                <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                    <i class="ti ti-search fs-22"></i>
                    <input type="search" class="form-control border-0" placeholder="Search">
                    <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
                </div>
            </div>
        </div>
    </div>
    @include('admin.partials.sidebar')
    <div class="page-wrapper">
        <div class="content">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1"><i class="ti ti-cash me-1"></i>All COD Orders</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Manage COD</a></li>
                            <li class="breadcrumb-item active" aria-current="page">All Order</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.cod.create-order') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Create Order
                    </a>
                </div>
            </div>

            <!-- Count Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <a href="{{ route('admin.cod.all-orders', ['status' => 'all']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-primary bg-opacity-10 text-primary"><i class="ti ti-package"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['all'] ?? 0 }}</div>
                            <div class="cod-count-label">Total Orders</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.cod.all-orders', ['status' => 'draft']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-warning bg-opacity-10 text-warning"><i class="ti ti-file-draft"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['draft'] ?? 0 }}</div>
                            <div class="cod-count-label">Draft</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.cod.all-orders', ['status' => 'manifested']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-info bg-opacity-10 text-info"><i class="ti ti-clipboard-check"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['manifested'] ?? 0 }}</div>
                            <div class="cod-count-label">Manifested</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.cod.all-orders', ['status' => 'cod']) }}" class="cod-count-card bg-white w-100">
                        <div class="cod-count-icon bg-success bg-opacity-10 text-success"><i class="ti ti-currency-rupee"></i></div>
                        <div>
                            <div class="cod-count-num">{{ $counts['cod'] ?? 0 }}</div>
                            <div class="cod-count-label">COD Collected</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="codOrdersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>AWB Number</th>
                                    <th>Invoice Number</th>
                                    <th>Invoice Date</th>
                                    <th>Amount</th>
                                    <th>Shipper</th>
                                    <th>Consignee</th>
                                    <th>Customer</th>
                                    <th>Manifest</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $index => $order)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-medium">{{ $order->awb_number ?? '—' }}</td>
                                    <td>{{ $order->invoice_number ?? '—' }}</td>
                                    <td>{{ $order->invoice_date ? \Carbon\Carbon::parse($order->invoice_date)->format('d M Y') : '—' }}</td>
                                    <td class="fw-semibold">{{ $order->invoice_currency ?? 'INR' }} {{ number_format((float) $order->invoice_amount, 2) }}</td>
                                    <td>
                                        <div class="fw-medium">{{ $order->company_name ?? '—' }}</div>
                                        <small class="text-muted">{{ $order->shipper_city ?? '' }}{{ $order->shipper_phone ? ' · '.$order->shipper_phone : '' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $order->consignee_name ?? '—' }}</div>
                                        <small class="text-muted">{{ $order->consignee_city ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if($order->first_name)
                                            {{ $order->first_name }} {{ $order->last_name ?? '' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->manifest_number ?? '—' }}</td>
                                    <td>
                                        @php
                                            $displayStatus = $order->shipper_status ?? $order->invoice_status ?? 'unknown';
                                        @endphp
                                        @if($displayStatus === 'draft')
                                            <span class="status-badge status-badge-draft">Draft</span>
                                        @elseif($displayStatus === 'manifested')
                                            <span class="status-badge status-badge-manifested">Manifested</span>
                                        @elseif($displayStatus === 'cod')
                                            <span class="status-badge status-badge-cod">COD</span>
                                        @else
                                            <span class="status-badge status-badge-other">{{ ucwords(str_replace('_', ' ', $displayStatus)) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->order_created_at ? \Carbon\Carbon::parse($order->order_created_at)->format('d M Y, h:i A') : '—' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">No COD orders found.</td>
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

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {
            @if($orders->count() > 0)
            $('#codOrdersTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                }
            });
            @endif
        });
    </script>
</body>
</html>
