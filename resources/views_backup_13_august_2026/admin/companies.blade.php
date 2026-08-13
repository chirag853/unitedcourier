<!DOCTYPE html>
<html lang="en">

<head>

	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - View Customer List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
	<meta name="keywords" content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions">
	<meta name="author" content="Dreams Technologies">
	<meta name="robots" content="index, follow">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	   <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Choices CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/choices.js/public/assets/styles/choices.min.css') }}">

    <!-- Select2 CSS -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Quill CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/quill/quill.snow.css') }}">

    <!-- Mobile CSS-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/intltelinput/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/intltelinput/css/demo.css') }}">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        .status-active {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-assigned {
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-dispatched {
            background-color: #cffafe;
            color: #0e7490;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-ready-to-dispatch {
            background-color: #fef3c7;
            color: #92400e;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .btn-ready-to-dispatch {
            background-color: #f59e0b;
            color: #fff;
            border: none;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn-ready-to-dispatch:hover {
            background-color: #d97706;
        }
        .customer-name-link {
            color: #0d6efd;
            cursor: pointer;
            text-decoration: none;
        }
        .customer-name-link:hover {
            text-decoration: underline;
        }
        .table-actions .btn-icon {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 12px;
        }
        .dataTables_wrapper .dataTables_info {
            margin-top: 8px;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 8px;
        }
        /* Tab card styles */
        .tab-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .tab-card .nav-tabs {
            border-bottom: 2px solid #e5e7eb;
            padding: 0 16px;
            background: #f9fafb;
        }
        .tab-card .nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            padding: 14px 24px;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.2s ease;
            position: relative;
        }
        .tab-card .nav-tabs .nav-link:hover {
            color: #374151;
            background: transparent;
        }
        .tab-card .nav-tabs .nav-link.active {
            color: #4f46e5;
            background: transparent;
            border-bottom: 3px solid #4f46e5;
        }
        .tab-card .nav-tabs .nav-link .badge {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 10px;
            margin-left: 6px;
        }
        .tab-card .tab-content {
            padding: 0;
        }
        .tab-card .tab-pane {
            padding: 0;
        }
        .tab-card .card-body {
            padding: 16px;
        }
        /* Print label button style */
        .btn-print-label {
            background-color: #0e7490;
            color: white;
            border: none;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-print-label:hover {
            background-color: #155e75;
            color: white;
        }
    </style>

</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar Start -->
            @include('admin.partials.header')
        <!-- Topbar End -->

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
            @include('admin.partials.sidebar')
        <!-- Sidenav Menu End -->

        <!-- ========================
			Start Page Content
		========================= -->
         
        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">View Customer List</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Customer List</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                    </div>
                </div>                
				<!-- End Page Header -->

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

                <!-- Three-Tab Card: Manifested | Assigned for Pickup | Print Label -->
                <div class="tab-card">
                    <ul class="nav nav-tabs" id="shipmentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="manifested-tab" data-bs-toggle="tab" data-bs-target="#manifestedPane" type="button" role="tab" aria-controls="manifestedPane" aria-selected="true">
                                <i class="ti ti-package me-1"></i> Manifested
                                <span class="badge bg-primary">{{ count($manifestedShipments) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assignedPane" type="button" role="tab" aria-controls="assignedPane" aria-selected="false">
                                <i class="ti ti-truck-delivery me-1"></i> Assigned for Pickup
                                <span class="badge" style="background:#6366f1;color:#fff;">{{ count($assignedForPickupShipments) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="printlabel-tab" data-bs-toggle="tab" data-bs-target="#printlabelPane" type="button" role="tab" aria-controls="printlabelPane" aria-selected="false">
                                <i class="ti ti-printer me-1"></i> Print Label
                                <span class="badge" style="background:#06b6d4;color:#fff;">{{ count($printLabelShipments) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="readytodispatch-tab" data-bs-toggle="tab" data-bs-target="#readytodispatchPane" type="button" role="tab" aria-controls="readytodispatchPane" aria-selected="false">
                                <i class="ti ti-truck me-1"></i> Ready to Dispatch
                                <span class="badge" style="background:#f59e0b;color:#fff;">{{ count($readyToDispatchShipments) }}</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="shipmentTabContent">

                        <!-- ===== TAB 1: Manifested ===== -->
                        <div class="tab-pane fade show active" id="manifestedPane" role="tabpanel" aria-labelledby="manifested-tab">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="manifestedTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>AWB Number</th>
                                                <th>Customer Name</th>
                                                <th>Customer Email</th>
                                                <th>Shipper Company</th>
                                                <th>Consignee</th>
                                                <th>Destination</th>
                                                <th>Invoice No.</th>
                                                <th>Amount</th>
                                                <th>Currency</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($manifestedShipments as $index => $shipment)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-dark">{{ $shipment->awb_number ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="customer-name-link" title="Click to view details">
                                                        {{ $shipment->first_name }} {{ $shipment->last_name }}
                                                    </span>
                                                </td>
                                                <td>{{ $shipment->customer_email ?? 'N/A' }}</td>
                                                <td>{{ $shipment->shipper_company ?? 'N/A' }}</td>
                                                <td>{{ $shipment->consignee_name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $shipment->consignee_city ?? $shipment->shipper_city ?? 'N/A' }}
                                                    @if($shipment->consignee_state || $shipment->shipper_state)
                                                        , {{ $shipment->consignee_state ?? $shipment->shipper_state }}
                                                    @endif
                                                </td>
                                                <td>{{ $shipment->invoice_number ?? 'N/A' }}</td>
                                                <td>
                                                    @if($shipment->invoice_amount)
                                                        {{ number_format($shipment->invoice_amount, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $shipment->invoice_currency ?? 'N/A' }}</td>
                                                <td>
                                                    @if($shipment->status === 'cancelled')
                                                        <span class="status-cancelled">Cancelled</span>
                                                    @else
                                                        <span class="status-active">Manifested</span>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($shipment->created_at)->format('d-m-Y') }}</td>
                                                <td class="table-actions">
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning btn-icon" title="Assign Pickup" onclick="openAssignDelivery({{ $shipment->id }}, '{{ $shipment->delivery_type ?? '' }}', {{ $shipment->assigned_delivery_person ?? 'null' }}, '{{ $shipment->awb_number ?? '' }}')">
                                                        <i class="ti ti-truck-delivery"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ===== TAB 2: Assigned for Pickup ===== -->
                        <div class="tab-pane fade" id="assignedPane" role="tabpanel" aria-labelledby="assigned-tab">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="assignedTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>AWB Number</th>
                                                <th>Customer Name</th>
                                                <th>Customer Email</th>
                                                <th>Shipper Company</th>
                                                <th>Consignee</th>
                                                <th>Destination</th>
                                                <th>Invoice No.</th>
                                                <th>Amount</th>
                                                <th>Currency</th>
                                                <th>Pickup Type</th>
                                                <th>Pickup Person</th>
                                                <th>Created</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($assignedForPickupShipments as $index => $shipment)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-dark">{{ $shipment->awb_number ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="customer-name-link" title="Click to view details">
                                                        {{ $shipment->first_name }} {{ $shipment->last_name }}
                                                    </span>
                                                </td>
                                                <td>{{ $shipment->customer_email ?? 'N/A' }}</td>
                                                <td>{{ $shipment->shipper_company ?? 'N/A' }}</td>
                                                <td>{{ $shipment->consignee_name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $shipment->consignee_city ?? $shipment->shipper_city ?? 'N/A' }}
                                                    @if($shipment->consignee_state || $shipment->shipper_state)
                                                        , {{ $shipment->consignee_state ?? $shipment->shipper_state }}
                                                    @endif
                                                </td>
                                                <td>{{ $shipment->invoice_number ?? 'N/A' }}</td>
                                                <td>
                                                    @if($shipment->invoice_amount)
                                                        {{ number_format($shipment->invoice_amount, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $shipment->invoice_currency ?? 'N/A' }}</td>
                                                <td>
                                                    @if($shipment->delivery_type)
                                                        <span class="badge bg-info">{{ $shipment->delivery_type }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($shipment->delivery_type === 'Self' && $shipment->delivery_person_name)
                                                        {{ $shipment->delivery_person_name }}
                                                    @elseif($shipment->delivery_type === 'DDU')
                                                        <span class="text-muted">Delhivery</span>
                                                    @elseif($shipment->delivery_type === 'DDP')
                                                        <span class="text-muted">Shiprocket</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($shipment->created_at)->format('d-m-Y') }}</td>
                                                <td class="table-actions">
                                                    <button class="btn btn-sm btn-outline-success btn-icon" title="Receive Shipment" onclick="openReceiveShipment({{ $shipment->id }}, '{{ $shipment->awb_number ?? '' }}')">
                                                        <i class="ti ti-package"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ===== TAB 3: Print Label ===== -->
                        <div class="tab-pane fade" id="printlabelPane" role="tabpanel" aria-labelledby="printlabel-tab">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="printlabelTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>AWB Number</th>
                                                <th>Customer Name</th>
                                                <th>Shipper Company</th>
                                                <th>Consignee</th>
                                                <th>Destination</th>
                                                <th>Invoice No.</th>
                                                <th>Amount</th>
                                                <th>Currency</th>
                                                <th>Pickup Type</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($printLabelShipments as $index => $shipment)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-dark">{{ $shipment->awb_number ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="customer-name-link" title="Click to view details">
                                                        {{ $shipment->first_name }} {{ $shipment->last_name }}
                                                    </span>
                                                </td>
                                                <td>{{ $shipment->shipper_company ?? 'N/A' }}</td>
                                                <td>{{ $shipment->consignee_name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $shipment->consignee_city ?? $shipment->shipper_city ?? 'N/A' }}
                                                    @if($shipment->consignee_state || $shipment->shipper_state)
                                                        , {{ $shipment->consignee_state ?? $shipment->shipper_state }}
                                                    @endif
                                                </td>
                                                <td>{{ $shipment->invoice_number ?? 'N/A' }}</td>
                                                <td>
                                                    @if($shipment->invoice_amount)
                                                        {{ number_format($shipment->invoice_amount, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $shipment->invoice_currency ?? 'N/A' }}</td>
                                                <td>
                                                    @if($shipment->delivery_type)
                                                        <span class="badge bg-info">{{ $shipment->delivery_type }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="status-dispatched">Dispatched</span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($shipment->created_at)->format('d-m-Y') }}</td>
                                                <td class="table-actions">
                                                    <button class="btn-print-label" onclick="printLabel({{ $shipment->id }})">
                                                        <i class="ti ti-printer me-1"></i> Print
                                                    </button>
                                                    <button class="btn-ready-to-dispatch ms-1" onclick="markReadyToDispatch({{ $shipment->id }})">
                                                        <i class="ti ti-truck me-1"></i> Ready to Dispatch
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ===== TAB 4: Ready to Dispatch ===== -->
                        <div class="tab-pane fade" id="readytodispatchPane" role="tabpanel" aria-labelledby="readytodispatch-tab">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="readytodispatchTable" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>AWB Number</th>
                                                <th>Customer Name</th>
                                                <th>Shipper Company</th>
                                                <th>Consignee</th>
                                                <th>Destination</th>
                                                <th>Invoice No.</th>
                                                <th>Amount</th>
                                                <th>Currency</th>
                                                <th>Pickup Type</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($readyToDispatchShipments as $index => $shipment)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-dark">{{ $shipment->awb_number ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="customer-name-link" title="Click to view details">
                                                        {{ $shipment->first_name }} {{ $shipment->last_name }}
                                                    </span>
                                                </td>
                                                <td>{{ $shipment->shipper_company ?? 'N/A' }}</td>
                                                <td>{{ $shipment->consignee_name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $shipment->consignee_city ?? $shipment->shipper_city ?? 'N/A' }}
                                                    @if($shipment->consignee_state || $shipment->shipper_state)
                                                        , {{ $shipment->consignee_state ?? $shipment->shipper_state }}
                                                    @endif
                                                </td>
                                                <td>{{ $shipment->invoice_number ?? 'N/A' }}</td>
                                                <td>
                                                    @if($shipment->invoice_amount)
                                                        {{ number_format($shipment->invoice_amount, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $shipment->invoice_currency ?? 'N/A' }}</td>
                                                <td>
                                                    @if($shipment->delivery_type)
                                                        <span class="badge bg-info">{{ $shipment->delivery_type }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="status-ready-to-dispatch">Ready to Dispatch</span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($shipment->created_at)->format('d-m-Y') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- End Three-Tab Card -->

            </div>
            <!-- End Content -->

        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Main Wrapper -->

    <!-- Assign Delivery Modal -->
    <div class="modal fade" id="assignDeliveryModal" tabindex="-1" aria-labelledby="assignDeliveryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignDeliveryModalLabel">
                        <i class="ti ti-truck-delivery me-1"></i> Assign Pickup
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignDeliveryForm">
                    <input type="hidden" name="shipment_id" id="assign_shipment_id" value="">
                    <div class="modal-body">
                        <div id="assignDeliveryAlert" class="alert d-none"></div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">AWB Number</label>
                            <p class="mb-0" id="assign_awb_display">-</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pickup Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_ddu" value="DDU">
                                    <label class="form-check-label" for="delivery_ddu">
                                        <strong>Delhivery</strong><br>
                                        <small class="text-muted">Pickup Duty Unpaid</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_ddp" value="DDP">
                                    <label class="form-check-label" for="delivery_ddp">
                                        <strong>Shiprocket</strong><br>
                                        <small class="text-muted">Pickup Duty Paid</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="delivery_self" value="Self">
                                    <label class="form-check-label" for="delivery_self">
                                        <strong>Self</strong><br>
                                        <small class="text-muted">Assign Pickup Person</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="deliveryPersonSection" style="display: none;">
                            <label class="form-label fw-semibold">Select Pickup Person <span class="text-danger">*</span></label>
                            <select class="form-select" name="delivery_person_id" id="delivery_person_id">
                                <option value="">-- Select Pickup Person --</option>
                                @foreach($deliveryPersons as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }} @if($person->mobile) ({{ $person->mobile }}) @endif</option>
                                @endforeach
                            </select>
                            @if($deliveryPersons->isEmpty())
                                <div class="text-warning mt-1">
                                    <small><i class="ti ti-alert-triangle"></i> No pickup persons found. Please add them in the admin users section.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="assignDeliveryBtn">
                            <i class="ti ti-device-floppy me-1"></i> Save Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Print Label Modal -->
    <div class="modal fade" id="printLabelModal" tabindex="-1" aria-labelledby="printLabelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printLabelModalLabel">
                        <i class="ti ti-printer me-1"></i> Print Shipping Label
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="printLabelContent">
                    <!-- Loading state -->
                    <div id="printLabelLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Generating label PDF...</p>
                    </div>
                    <!-- Error state -->
                    <div id="printLabelError" class="text-center py-4 d-none">
                        <i class="ti ti-alert-circle fs-24 text-danger d-block mb-2"></i>
                        <p class="text-danger" id="printLabelErrorMsg">Failed to generate label.</p>
                    </div>
                    <!-- PDF iframe -->
                    <iframe id="printLabelPdfFrame" style="width:100%;height:500px;border:none;display:none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary d-none" id="printLabelPrintBtn" onclick="triggerPdfPrint()">
                        <i class="ti ti-printer me-1"></i> Print Label
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receive Shipment Modal -->
    <div class="modal fade" id="receiveShipmentModal" tabindex="-1" aria-labelledby="receiveShipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiveShipmentModalLabel">
                        <i class="ti ti-package me-1"></i> Receive Shipment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="receiveShipmentForm">
                    <input type="hidden" name="shipment_id" id="receive_shipment_id" value="">
                    <div class="modal-body">
                        <div id="receiveShipmentAlert" class="alert d-none"></div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">AWB Number</label>
                            <p class="mb-0" id="receive_awb_display">-</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Has this shipment been received? <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="received" id="receive_yes" value="yes">
                                    <label class="form-check-label" for="receive_yes">
                                        <strong>Yes</strong><br>
                                        <small class="text-muted">Shipment has been received</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="received" id="receive_no" value="no">
                                    <label class="form-check-label" for="receive_no">
                                        <strong>No</strong><br>
                                        <small class="text-muted">Shipment not received / On Hold</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="receiveShipmentBtn">
                            <i class="ti ti-check me-1"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>  

    <!-- Daterangepikcer JS -->
	<script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
	<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Choices Js -->	
    <script src="{{ asset('assets/plugins/choices.js/public/assets/scripts/choices.min.js') }}" type="text/javascript"></script>

    <!-- Mobile Input -->
    <script src="{{ asset('assets/plugins/intltelinput/js/intlTelInput.js') }}" type="text/javascript"></script>

    <!-- Quill JS -->
    <script src="{{ asset('assets/plugins/quill/quill.min.js') }}" type="text/javascript"></script>

	<!-- Simplebar JS -->
	<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

    <script data-cfasync="false">
        $(document).ready(function() {
            // Initialize DataTables for each tab
            $('#manifestedTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    emptyTable: "No manifested shipments found",
                    info: "Showing _START_ to _END_ of _TOTAL_ shipments",
                    infoEmpty: "Showing 0 to 0 of 0 shipments",
                    infoFiltered: "(filtered from _MAX_ total shipments)",
                    lengthMenu: "Show _MENU_ shipments",
                    search: "Search:",
                    zeroRecords: "No matching shipments found"
                }
            });

            $('#assignedTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    emptyTable: "No assigned for pickup shipments found",
                    info: "Showing _START_ to _END_ of _TOTAL_ shipments",
                    infoEmpty: "Showing 0 to 0 of 0 shipments",
                    infoFiltered: "(filtered from _MAX_ total shipments)",
                    lengthMenu: "Show _MENU_ shipments",
                    search: "Search:",
                    zeroRecords: "No matching shipments found"
                }
            });

            $('#printlabelTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    emptyTable: "No dispatched shipments available for label printing",
                    info: "Showing _START_ to _END_ of _TOTAL_ shipments",
                    infoEmpty: "Showing 0 to 0 of 0 shipments",
                    infoFiltered: "(filtered from _MAX_ total shipments)",
                    lengthMenu: "Show _MENU_ shipments",
                    search: "Search:",
                    zeroRecords: "No matching shipments found"
                }
            });

            $('#readytodispatchTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    emptyTable: "No shipments ready to dispatch",
                    info: "Showing _START_ to _END_ of _TOTAL_ shipments",
                    infoEmpty: "Showing 0 to 0 of 0 shipments",
                    infoFiltered: "(filtered from _MAX_ total shipments)",
                    lengthMenu: "Show _MENU_ shipments",
                    search: "Search:",
                    zeroRecords: "No matching shipments found"
                }
            });

            // Reinitialize DataTable when switching tabs (to fix layout issues)
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const targetId = $(e.target).attr('data-bs-target');
                const tableId = targetId === '#manifestedPane' ? 'manifestedTable'
                              : targetId === '#assignedPane' ? 'assignedTable'
                              : targetId === '#printlabelPane' ? 'printlabelTable'
                              : 'readytodispatchTable';
                const dt = $(`#${tableId}`).DataTable();
                dt.columns.adjust().draw();
            });

            // ===== Assign Delivery Modal Logic =====

            /**
             * Show/hide delivery person section when delivery type radio changes
             */
            $('input[name="delivery_type"]').on('change', function() {
                if ($(this).val() === 'Self') {
                    $('#deliveryPersonSection').slideDown(200);
                } else {
                    $('#deliveryPersonSection').slideUp(200);
                    $('#delivery_person_id').val('');
                }
            });

            /**
             * Handle form submission via AJAX
             */
            $('#assignDeliveryForm').on('submit', function(e) {
                e.preventDefault();

                // Basic client-side validation
                const deliveryType = $('input[name="delivery_type"]:checked').val();
                if (!deliveryType) {
                    showAssignDeliveryAlert('Please select a delivery type.', 'danger');
                    return;
                }
                if (deliveryType === 'Self' && !$('#delivery_person_id').val()) {
                    showAssignDeliveryAlert('Please select a delivery person for Self delivery.', 'danger');
                    return;
                }

                const $btn = $('#assignDeliveryBtn');
                // Show contextual loading text based on delivery type
                if (deliveryType === 'DDU') {
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Creating Delhivery Pickup...');
                } else {
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
                }

                $.ajax({
                    url: '{{ route("admin.assign-delivery") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            let alertMsg = response.message;

                            // If Delhivery API was called, show additional details
                            if (response.delhivery) {
                                const delhivery = response.delhivery;
                                if (delhivery.success) {
                                    // Show waybill/awb info from Delhivery if available
                                    let waybillInfo = '';
                                    const delhiveryData = delhivery.data || {};
                                    // Extract waybill from rmk array or packages array
                                    if (delhiveryData.rmk && delhiveryData.rmk.length > 0) {
                                        waybillInfo = '<br><strong>Delhivery Waybill: ' + delhiveryData.rmk[0] + '</strong>';
                                    } 
                                    else if (delhiveryData.packages && delhiveryData.packages.length > 0) {
                                        waybillInfo = '<br><strong>Delhivery Waybill: ' + (delhiveryData.packages[0].waybill || '') + '</strong>';
                                    }
                                    alertMsg += waybillInfo;
                                    showAssignDeliveryAlert(alertMsg, 'success');
                                } else {
                                    // Delhivery API call failed but assignment was saved
                                    let failMsg = alertMsg;
                                    if (delhivery.message) {
                                        failMsg += '<br><small class="text-muted">Delhivery Error: ' + delhivery.message + '</small>';
                                    }
                                    // Show per-package error details if available
                                    const delhiveryData = delhivery.data || {};
                                    if (delhiveryData.packages && delhiveryData.packages.length > 0) {
                                        const pkg = delhiveryData.packages[0];
                                        if (pkg.status === 'Fail') {
                                            failMsg += '<br><small class="text-danger">Package Status: Failed</small>';
                                        }
                                        if (pkg.remarks && pkg.remarks.length > 0) {
                                            const remarks = pkg.remarks.filter(r => r && r.trim() !== '');
                                            if (remarks.length > 0) {
                                                failMsg += '<br><small class="text-danger">Reason: ' + remarks.join(', ') + '</small>';
                                            }
                                        }
                                    }
                                    if (delhiveryData.rmk) {
                                        const rmkText = Array.isArray(delhiveryData.rmk) ? delhiveryData.rmk.join(', ') : delhiveryData.rmk;
                                        if (rmkText) {
                                            failMsg += '<br><small class="text-muted">Delhivery Remark: ' + rmkText + '</small>';
                                        }
                                    }
                                    showAssignDeliveryAlert(failMsg, 'warning');
                                }
                            } else {
                                showAssignDeliveryAlert(alertMsg, 'success');
                            }

                            $btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
                            // Reload page after a short delay to reflect changes (shipment moves to Assigned tab)
                            setTimeout(function() {
                                // location.reload();
                            }, 2500);
                        } else {
                            showAssignDeliveryAlert(response.message || 'Something went wrong.', 'danger');
                            $btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                        }
                        showAssignDeliveryAlert(msg, 'danger');
                        $btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
                    }
                });
            });

            /**
             * Reset modal form when it is hidden
             */
            $('#assignDeliveryModal').on('hidden.bs.modal', function() {
                $('#assignDeliveryAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');
                $('input[name="delivery_type"]').prop('checked', false);
                $('#delivery_person_id').val('');
                $('#deliveryPersonSection').hide();
                $('#assignDeliveryBtn').prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Assignment');
            });

            // ===== Receive Shipment Modal Logic =====

            /**
             * Handle receive shipment form submission via AJAX
             */
            $('#receiveShipmentForm').on('submit', function(e) {
                e.preventDefault();

                const received = $('input[name="received"]:checked').val();
                if (!received) {
                    showReceiveShipmentAlert('Please select Yes or No.', 'danger');
                    return;
                }

                const $btn = $('#receiveShipmentBtn');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Submitting...');

                $.ajax({
                    url: '{{ route("admin.receive-shipment") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showReceiveShipmentAlert(response.message, 'success');
                            $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Submit');
                            // Reload page after a short delay to reflect changes
                            setTimeout(function() {
                                location.reload();
                            }, 2500);
                        } else {
                            showReceiveShipmentAlert(response.message || 'Something went wrong.', 'danger');
                            $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Submit');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                        }
                        showReceiveShipmentAlert(msg, 'danger');
                        $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Submit');
                    }
                });
            });

            /**
             * Reset receive shipment modal when it is hidden
             */
            $('#receiveShipmentModal').on('hidden.bs.modal', function() {
                $('#receiveShipmentAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');
                $('input[name="received"]').prop('checked', false);
                $('#receiveShipmentBtn').prop('disabled', false).html('<i class="ti ti-check me-1"></i> Submit');
            });

            /**
             * Reset Print Label modal when it is hidden - revoke blob URL to free memory
             */
            $('#printLabelModal').on('hidden.bs.modal', function() {
                // Revoke the blob URL to free memory
                if (window._labelPdfBlobUrl) {
                    URL.revokeObjectURL(window._labelPdfBlobUrl);
                    window._labelPdfBlobUrl = null;
                }
                // Reset modal states
                $('#printLabelLoading').addClass('d-none');
                $('#printLabelError').addClass('d-none');
                $('#printLabelPdfFrame').css('display', 'none').attr('src', '');
                $('#printLabelPrintBtn').addClass('d-none');
            });

            /**
             * Helper to show alerts inside the assign delivery modal
             */
            function showAssignDeliveryAlert(message, type) {
                const $alert = $('#assignDeliveryAlert');
                $alert.removeClass('d-none alert-success alert-danger alert-warning').addClass('alert-' + type).html(message);
            }

            /**
             * Helper to show alerts inside the receive shipment modal
             */
            function showReceiveShipmentAlert(message, type) {
                const $alert = $('#receiveShipmentAlert');
                $alert.removeClass('d-none alert-success alert-danger').addClass('alert-' + type).html(message);
            }

        });

        /**
         * Open the Assign Delivery modal and pre-populate with current values.
         * @param {number} shipmentId
         * @param {string} currentType - Current delivery type (DDU, DDP, Self, or empty)
         * @param {number|null} currentPersonId - Current assigned delivery person ID
         * @param {string} awbNumber - AWB number for display
         */
        function openAssignDelivery(shipmentId, currentType, currentPersonId, awbNumber) {
            // Set shipment ID
            $('#assign_shipment_id').val(shipmentId);

            // Display AWB number
            $('#assign_awb_display').text(awbNumber || '-');

            // Pre-select the current delivery type radio
            $('input[name="delivery_type"]').prop('checked', false);
            if (currentType) {
                $('input[name="delivery_type"][value="' + currentType + '"]').prop('checked', true);
            }

            // Show/hide delivery person section based on current delivery type
            if (currentType === 'Self') {
                $('#deliveryPersonSection').show();
            } else {
                $('#deliveryPersonSection').hide();
            }

            // Pre-select the delivery person
            if (currentPersonId && currentPersonId !== 'null') {
                $('#delivery_person_id').val(currentPersonId);
            } else {
                $('#delivery_person_id').val('');
            }

            // Reset alert
            $('#assignDeliveryAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');

            // Open the modal
            $('#assignDeliveryModal').modal('show');
        }

        /**
         * Open the Receive Shipment modal.
         * @param {number} shipmentId
         * @param {string} awbNumber - AWB number for display
         */
        function openReceiveShipment(shipmentId, awbNumber) {
            // Set shipment ID
            $('#receive_shipment_id').val(shipmentId);

            // Display AWB number
            $('#receive_awb_display').text(awbNumber || '-');

            // Reset radio buttons
            $('input[name="received"]').prop('checked', false);

            // Reset alert
            $('#receiveShipmentAlert').addClass('d-none').removeClass('alert-success alert-danger').html('');

            // Open the modal
            $('#receiveShipmentModal').modal('show');
        }

        /**
         * Print shipping label - fetches base64 PDF from server and displays in modal.
         * @param {number} shipmentId - The shipment_invoice ID
         */
        function printLabel(shipmentId) {
            // Reset modal states
            $('#printLabelLoading').removeClass('d-none');
            $('#printLabelError').addClass('d-none');
            $('#printLabelPdfFrame').css('display', 'none');
            $('#printLabelPrintBtn').addClass('d-none');

            // Open modal
            $('#printLabelModal').modal('show');

            // AJAX call to generate label PDF
            $.ajax({
                url: '/admin/generate-label',
                type: 'POST',
                data: { shipment_id: shipmentId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.pdf_base64) {
                        // Convert base64 to PDF blob
                        const binaryString = atob(response.pdf_base64);
                        const bytes = new Uint8Array(binaryString.length);
                        for (let i = 0; i < binaryString.length; i++) {
                            bytes[i] = binaryString.charCodeAt(i);
                        }
                        const pdfBlob = new Blob([bytes], { type: 'application/pdf' });
                        const blobUrl = URL.createObjectURL(pdfBlob);

                        // Hide loading, show iframe with PDF
                        $('#printLabelLoading').addClass('d-none');
                        $('#printLabelPdfFrame').attr('src', blobUrl).css('display', 'block');
                        $('#printLabelPrintBtn').removeClass('d-none');

                        // Store blob URL for printing
                        window._labelPdfBlobUrl = blobUrl;
                    } else {
                        // Show error
                        $('#printLabelLoading').addClass('d-none');
                        $('#printLabelError').removeClass('d-none');
                        $('#printLabelErrorMsg').text(response.message || 'Failed to generate label.');
                    }
                },
                error: function(xhr) {
                    // Show error
                    $('#printLabelLoading').addClass('d-none');
                    $('#printLabelError').removeClass('d-none');
                    let errorMsg = 'Failed to generate label. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#printLabelErrorMsg').text(errorMsg);
                }
            });
        }

        /**
         * Trigger browser print for the PDF label.
         * Opens the PDF blob URL in a new window and triggers print.
         */
        function triggerPdfPrint() {
            if (window._labelPdfBlobUrl) {
                const printWindow = window.open(window._labelPdfBlobUrl, '_blank');
                if (printWindow) {
                    printWindow.onload = function() {
                        setTimeout(function() {
                            printWindow.print();
                        }, 500);
                    };
                } else {
                    alert('Please allow popups to print the label.');
                }
            }
        }

        /**
         * Mark a shipment as Ready to Dispatch.
         * Creates a tracking record with status 'ready_to_dispatch' and moves
         * the shipment from Print Label tab to Ready to Dispatch tab.
         * @param {number} shipmentId - The shipment_invoice ID
         */
        function markReadyToDispatch(shipmentId) {
            if (!confirm('Are you sure you want to mark this shipment as Ready to Dispatch?')) {
                return;
            }

            const $btn = $(event.currentTarget);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            $.ajax({
                url: '{{ route("admin.ready-to-dispatch") }}',
                type: 'POST',
                data: { shipment_id: shipmentId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        // Reload page to reflect changes (shipment moves to Ready to Dispatch tab)
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        alert(response.message || 'Something went wrong.');
                        $btn.prop('disabled', false).html('<i class="ti ti-truck me-1"></i> Ready to Dispatch');
                    }
                },
                error: function(xhr) {
                    let msg = 'An error occurred. Please try again.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                    }
                    alert(msg);
                    $btn.prop('disabled', false).html('<i class="ti ti-truck me-1"></i> Ready to Dispatch');
                }
            });
        }
    </script>

</body>

</html>
