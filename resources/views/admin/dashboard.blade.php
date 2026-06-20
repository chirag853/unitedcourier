<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
	<meta name="keywords" content="Advanced CRM template, customer relationship management, business CRM, sales optimization, customer support software, CRM integration, customizable CRM, business tools, enterprise CRM solutions">
	<meta name="author" content="Dreams Technologies">
	<meta name="robots" content="index, follow">
	
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Daterangepicker CSS -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/css/dataTables.bootstrap5.min.css') }}">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <style>
        .chart-filter-btn {
            padding: 4px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: #fff;
            color: #495057;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .chart-filter-btn:hover {
            background: #e9ecef;
        }
        .chart-filter-btn.active {
            background: #5b5eff;
            color: #fff;
            border-color: #5b5eff;
        }
        .chart-card {
            min-height: 300px;
        }
    </style>
</head>

<body>

    <!-- Begin Wrapper -->
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

        <!-- ========================
			Start Page Content
		========================= -->
         
        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content pb-0">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">Dashboard</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ route('admin.companies') }}" class="btn btn-primary btn-sm">Companies</a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload()"><i class="ti ti-refresh"></i></a>
                    </div>
                </div>				
				<!-- End Page Header -->

                <!-- start row - Customer Summary Stat Cards -->
                <h6 class="mb-2">Customer Summary</h6>
                <div class="row row-gap-3 mb-4">
					<!-- Total Registrations -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill mb-0 position-relative overflow-hidden">
							<div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Total Registrations</p>
                                            <h2 class="mb-1 fs-16">{{ $totalRegistrations }}</h2>
                                            @if($registrationsChangePercent > 0)
                                            <p class="text-success mb-0 fs-13"> <i class="ti ti-arrow-bar-up me-1"></i>{{ $registrationsChangePercent }}%<span class="text-body ms-1">from last month</span></p>
                                            @elseif($registrationsChangePercent < 0)
                                            <p class="text-danger mb-0 fs-13"> <i class="ti ti-arrow-bar-down me-1"></i>{{ abs($registrationsChangePercent) }}%<span class="text-body ms-1">from last month</span></p>
                                            @else
                                            <p class="text-muted mb-0 fs-13">No change from last month</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="avatar avatar-md rounded-circle bg-soft-primary border border-primary">
                                            <i class="ti ti-building fs-16 text-primary"></i>
                                        </span>
                                    </div>
                                </div>
							</div>
                            <img src="{{ asset('assets/img/icons/elemnt-01.svg') }}" alt="elemnt-01" class="img-fluid position-absolute top-0 start-0">
						</div>
					</div>
					<!-- /Total Registrations -->

                    <!-- KYC Pending -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill mb-0 position-relative overflow-hidden">
							<div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">KYC Pending</p>
                                            <h2 class="mb-1 fs-16">{{ $kycPending }}</h2>
                                            @if($kycPendingChangePercent > 0)
                                            <p class="text-danger mb-0 fs-13"> <i class="ti ti-arrow-bar-up me-1"></i>{{ $kycPendingChangePercent }}%<span class="text-body ms-1">from last month</span></p>
                                            @elseif($kycPendingChangePercent < 0)
                                            <p class="text-success mb-0 fs-13"> <i class="ti ti-arrow-bar-down me-1"></i>{{ abs($kycPendingChangePercent) }}%<span class="text-body ms-1">from last month</span></p>
                                            @else
                                            <p class="text-muted mb-0 fs-13">No change from last month</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="avatar avatar-md rounded-circle bg-soft-warning border border-warning">
                                            <i class="ti ti-clock fs-16 text-warning"></i>
                                        </span>
                                    </div>
                                </div>
							</div>
                            <img src="{{ asset('assets/img/icons/elemnt-02.svg') }}" alt="elemnt-02" class="img-fluid position-absolute top-0 start-0">
						</div>
					</div>
					<!-- /KYC Pending -->

                    <!-- Onboarded Customers -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill mb-0 position-relative overflow-hidden">
							<div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Onboarded Customers</p>
                                            <h2 class="mb-1 fs-16">{{ $onboardedCustomers }}</h2>
                                            @if($onboardedChangePercent > 0)
                                            <p class="text-success mb-0 fs-13"> <i class="ti ti-arrow-bar-up me-1"></i>{{ $onboardedChangePercent }}%<span class="text-body ms-1">from last month</span></p>
                                            @elseif($onboardedChangePercent < 0)
                                            <p class="text-danger mb-0 fs-13"> <i class="ti ti-arrow-bar-down me-1"></i>{{ abs($onboardedChangePercent) }}%<span class="text-body ms-1">from last month</span></p>
                                            @else
                                            <p class="text-muted mb-0 fs-13">No change from last month</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="avatar avatar-md rounded-circle bg-soft-success border border-success">
                                            <i class="ti ti-user-check fs-16 text-success"></i>
                                        </span>
                                    </div>
                                </div>
							</div>
                            <img src="{{ asset('assets/img/icons/elemnt-03.svg') }}" alt="elemnt-03" class="img-fluid position-absolute top-0 start-0">
						</div>
					</div>
					<!-- /Onboarded Customers -->

                    <!-- CSB5 Enabled -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill mb-0 position-relative overflow-hidden">
							<div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">CSB5 Enabled</p>
                                            <h2 class="mb-1 fs-16">{{ $csb5Enabled }}</h2>
                                            @if($csb5ChangePercent > 0)
                                            <p class="text-success mb-0 fs-13"> <i class="ti ti-arrow-bar-up me-1"></i>{{ $csb5ChangePercent }}%<span class="text-body ms-1">from last month</span></p>
                                            @elseif($csb5ChangePercent < 0)
                                            <p class="text-danger mb-0 fs-13"> <i class="ti ti-arrow-bar-down me-1"></i>{{ abs($csb5ChangePercent) }}%<span class="text-body ms-1">from last month</span></p>
                                            @else
                                            <p class="text-muted mb-0 fs-13">No change from last month</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="avatar avatar-md rounded-circle bg-soft-info border border-info">
                                            <i class="ti ti-file-check fs-16 text-info"></i>
                                        </span>
                                    </div>
                                </div>
							</div>
                            <img src="{{ asset('assets/img/icons/elemnt-04.svg') }}" alt="elemnt-04" class="img-fluid position-absolute top-0 start-0">
						</div>
					</div>
					<!-- /CSB5 Enabled -->

				</div>
                <!-- end row -->

                <!-- KYC Pending List -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="mb-0">KYC Pending Customers</h6>
                                <a href="{{ url('/admin/kyc-pending') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                @if($kycPendingList->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Customer Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Organization</th>
                                                <th>GST Number</th>
                                                <th>Submitted At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kycPendingList as $key => $kyc)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td><strong>{{ $kyc->customer->first_name ?? '' }} {{ $kyc->customer->last_name ?? '' }}</strong></td>
                                                <td><a href="mailto:{{ $kyc->customer->email ?? '' }}">{{ $kyc->customer->email ?? '—' }}</a></td>
                                                <td>{{ $kyc->customer->phone_number ?? '—' }}</td>
                                                <td>{{ $kyc->organization_name ?? '—' }}</td>
                                                <td>{{ $kyc->gst_number ?? '—' }}</td>
                                                <td><span class="badge bg-warning text-dark">{{ $kyc->created_at->format('d M Y, h:i A') }}</span></td>
                                                <td>
                                                    <form action="{{ route('admin.kyc-pending.approve', $kyc->id) }}" method="POST" class="d-inline kyc-approve-form">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="Approve KYC">
                                                            <i class="ti ti-circle-check me-1"></i>Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.kyc-pending.reject', $kyc->id) }}" method="POST" class="d-inline kyc-reject-form">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Reject KYC">
                                                            <i class="ti ti-circle-x me-1"></i>Reject
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="ti ti-circle-check fs-24 text-success"></i>
                                    <p class="text-muted mb-0">No pending KYC submissions</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /KYC Pending List -->

                <!-- Shipment Analytics Section with Date Filters -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h6 class="mb-0">Shipment Analytics</h6>
                    <div class="d-flex gap-2">
                        <button class="chart-filter-btn" data-filter="today" onclick="loadChartData('today', this)">Today</button>
                        <button class="chart-filter-btn" data-filter="yesterday" onclick="loadChartData('yesterday', this)">Yesterday</button>
                        <button class="chart-filter-btn active" data-filter="this_month" onclick="loadChartData('this_month', this)">This Month</button>
                        <button class="chart-filter-btn" data-filter="last_month" onclick="loadChartData('last_month', this)">Last Month</button>
                        <button class="chart-filter-btn" data-filter="last_year" onclick="loadChartData('last_year', this)">Last Year</button>
                    </div>
                </div>

                <!-- start row - Customer Summary Charts (Pie + Bar) -->
                <div class="row row-gap-3 mb-4">
                    <!-- Customer Summary Pie/Doughnut -->
                    <div class="col-xl-5 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Customer Summary</h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <canvas id="customerSummaryChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Customer Summary Bar Chart -->
                    <div class="col-xl-7 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Customer Summary — Bar View</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="customerSummaryBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <!-- start row - Shipment & Delivery Stat Tiles -->
                <div class="row row-gap-3 mb-4">
                    <!-- Total Shipments -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Total Shipments</p>
                                            <h2 class="mb-1 fs-16" id="statTotalShipments">{{ array_sum($shipmentStatusCounts) }}</h2>
                                            <p class="text-muted mb-0 fs-13" id="statTotalShipmentsSub">for selected period</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="avatar avatar-md rounded-circle bg-soft-primary border border-primary">
                                            <i class="ti ti-package fs-16 text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-01.svg') }}" alt="elemnt-01" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Total Shipments -->

                    <!-- Delivered -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Delivered</p>
                                            <h2 class="mb-1 fs-16" id="statDelivered">{{ $deliveredCount }}</h2>
                                            <p class="text-muted mb-0 fs-13" id="statDeliveredSub">for selected period</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="avatar avatar-md rounded-circle bg-soft-success border border-success">
                                            <i class="ti ti-truck-delivery fs-16 text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-02.svg') }}" alt="elemnt-02" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Delivered -->

                    <!-- ShipRocket -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">ShipRocket</p>
                                            <h2 class="mb-1 fs-16" id="statShipRocket">{{ $shipRocketCount }}</h2>
                                            <p class="text-muted mb-0 fs-13" id="statShipRocketSub">for selected period</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="avatar avatar-md rounded-circle bg-soft-info border border-info">
                                            <i class="ti ti-rocket fs-16 text-info"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-03.svg') }}" alt="elemnt-03" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /ShipRocket -->

                    <!-- Self/Own Network -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <p class="fs-14 mb-1 text-dark">Self/Own Network</p>
                                            <h2 class="mb-1 fs-16" id="statSelfNetwork">{{ $selfCount }}</h2>
                                            <p class="text-muted mb-0 fs-13" id="statSelfNetworkSub">for selected period</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="avatar avatar-md rounded-circle bg-soft-warning border border-warning">
                                            <i class="ti ti-world fs-16 text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-04.svg') }}" alt="elemnt-04" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Self/Own Network -->

                </div>
                <!-- end row - Shipment & Delivery Stat Tiles -->

                <!-- start row - Shipment & Delivery Summary Charts (Pie + Bar) -->
                <div class="row row-gap-3 mb-4">
                    <!-- Shipment & Delivery Merged Doughnut -->
                    <div class="col-xl-5 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Shipment & Delivery Summary</h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <canvas id="shipmentDeliverySummaryChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Shipment & Delivery Merged Bar Chart -->
                    <div class="col-xl-7 col-lg-6 d-flex">
                        <div class="card flex-fill chart-card">
                            <div class="card-header">
                                <h6 class="mb-0">Shipment & Delivery Summary — Bar View</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="shipmentDeliveryBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <!-- start row - Shipment Trend Bar Chart -->
                <div class="row mb-4">
                    <div class="col-xl-12 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <h6 class="mb-0">Shipment Creation Trend</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="shipmentTrendChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div>
            <!-- End Content -->            

            <!-- Start Footer -->
            <footer class="footer d-block d-md-flex justify-content-between text-md-start text-center">
               <p class="mb-md-0 mb-1">Copyright &copy; <script type="text/javascript">document.write(new Date().getFullYear())</script> <a href="javascript:void(0);" class="link-primary text-decoration-underline">United Courier</a></p>
               <div class="d-flex align-items-center gap-2 footer-links justify-content-center justify-content-md-end">
                  <a href="javascript:void(0);">About</a>
                  <a href="javascript:void(0);">Terms</a>
                  <a href="javascript:void(0);">Contact Us</a>
               </div>
            </footer>
            <!-- End Footer -->

        </div>

        <!-- ========================
			End Page Content
		========================= -->

    </div>
    <!-- End Wrapper -->


    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script> 
    
    <!-- Daterangepikcer JS -->
	<script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

	<!-- Simplebar JS -->
	<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
	<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

    <!-- Admin Dashboard Charts Script -->
    <script>
        let customerSummaryChart = null;
        let shipmentDeliverySummaryChart = null;
        let shipmentTrendChart = null;
        let customerSummaryBarChart = null;
        let shipmentDeliveryBarChart = null;

        // Color palette for charts
        const customerColors = {
            totalRegistrations: '#5b5eff',
            kycPending: '#fd7e14',
            onboardedCustomers: '#198754',
            csb5Enabled: '#0dcaf0'
        };

        const statusColors = {
            draft: '#6c757d',
            ready: '#0d6efd',
            assigned_for_pickup: '#198754',
            packed: '#fd7e14',
            manifested: '#6610f2',
            dispatched: '#20c997',
            ready_to_dispatch: '#ffc107',
            delivered: '#0dcaf0',
            cancelled: '#dc3545',
            disputed: '#e83e8c',
            on_hold: '#495057',
            received: '#17a2b8'
        };

        const deliveryColors = {
            delivered: '#198754',
            shipRocket: '#5b5eff',
            self: '#fd7e14',
            other: '#6c757d'
        };

        function loadChartData(filter, btnElement) {
            // Update active button
            document.querySelectorAll('.chart-filter-btn').forEach(btn => btn.classList.remove('active'));
            if (btnElement) btnElement.classList.add('active');

            fetch('{{ route("admin.dashboard-chart-data") }}?filter=' + filter, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderCustomerSummaryChart(data.customerSummary);
                    renderCustomerSummaryBarChart(data.customerSummary);
                    renderShipmentDeliverySummaryChart(data.shipmentStatusCounts, data.statusMap, data.deliverySummary);
                    renderShipmentDeliveryBarChart(data.shipmentStatusCounts, data.statusMap, data.deliverySummary);
                    renderShipmentTrendChart(data.dateWiseCounts, data.filter);
                    updateShipmentDeliveryStatTiles(data.shipmentStatusCounts, data.deliverySummary, data.filter);
                }
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
            });
        }

        function updateShipmentDeliveryStatTiles(statusCounts, deliverySummary, filter) {
            // Calculate total shipments from all status counts
            const totalShipments = Object.values(statusCounts).reduce((a, b) => a + b, 0);
            const delivered = statusCounts['delivered'] || 0;
            const shipRocket = deliverySummary.shipRocket || 0;
            const selfNetwork = deliverySummary.self || 0;

            // Filter label for sub-text
            const filterLabels = {
                today: 'today',
                yesterday: 'yesterday',
                this_month: 'this month',
                last_month: 'last month',
                last_year: 'last year'
            };
            const periodLabel = filterLabels[filter] || 'selected period';

            document.getElementById('statTotalShipments').textContent = totalShipments;
            document.getElementById('statTotalShipmentsSub').textContent = 'for ' + periodLabel;

            document.getElementById('statDelivered').textContent = delivered;
            document.getElementById('statDeliveredSub').textContent = 'for ' + periodLabel;

            document.getElementById('statShipRocket').textContent = shipRocket;
            document.getElementById('statShipRocketSub').textContent = 'for ' + periodLabel;

            document.getElementById('statSelfNetwork').textContent = selfNetwork;
            document.getElementById('statSelfNetworkSub').textContent = 'for ' + periodLabel;
        }

        function renderCustomerSummaryChart(customerSummary) {
            const labels = ['Registrations', 'KYC Pending', 'Onboarded', 'CSB5 Enabled'];
            const values = [
                customerSummary.totalRegistrations,
                customerSummary.kycPending,
                customerSummary.onboardedCustomers,
                customerSummary.csb5Enabled
            ];
            const colors = [
                customerColors.totalRegistrations,
                customerColors.kycPending,
                customerColors.onboardedCustomers,
                customerColors.csb5Enabled
            ];

            if (customerSummaryChart) {
                customerSummaryChart.destroy();
            }

            const ctx = document.getElementById('customerSummaryChart').getContext('2d');
            customerSummaryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 16,
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '55%'
                }
            });
        }

        function renderShipmentDeliverySummaryChart(statusCounts, statusMap, deliverySummary) {
            // Build merged labels, values, and colors — shipment statuses + delivery metrics
            const labels = [];
            const values = [];
            const colors = [];

            // Shipment status entries
            for (const [status, count] of Object.entries(statusCounts)) {
                labels.push(statusMap[status] || status);
                values.push(count);
                colors.push(statusColors[status] || '#adb5bd');
            }

            // Delivery/network entries
            labels.push('Delivered');
            values.push(deliverySummary.delivered);
            colors.push(deliveryColors.delivered);

            labels.push('ShipRocket');
            values.push(deliverySummary.shipRocket);
            colors.push(deliveryColors.shipRocket);

            labels.push('Self/Own Network');
            values.push(deliverySummary.self);
            colors.push(deliveryColors.self);

            // Other networks
            if (deliverySummary.otherNetworks) {
                for (const [network, count] of Object.entries(deliverySummary.otherNetworks)) {
                    labels.push(network);
                    values.push(count);
                    colors.push(deliveryColors.other);
                }
            }

            if (shipmentDeliverySummaryChart) {
                shipmentDeliverySummaryChart.destroy();
            }

            const ctx = document.getElementById('shipmentDeliverySummaryChart').getContext('2d');
            shipmentDeliverySummaryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                usePointStyle: true,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '55%'
                }
            });
        }

        function renderShipmentTrendChart(dateWiseCounts, filter) {
            const labels = Object.keys(dateWiseCounts);
            const values = Object.values(dateWiseCounts);

            // Format labels for display
            const displayLabels = labels.map(label => {
                if (filter === 'last_year') {
                    // Format "2025-01" as "Jan 2025"
                    const [year, month] = label.split('-');
                    const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    return monthNames[parseInt(month) - 1] + ' ' + year;
                } else {
                    // Format "2025-06-15" as "15 Jun"
                    const parts = label.split('-');
                    const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    return parseInt(parts[2]) + ' ' + monthNames[parseInt(parts[1]) - 1];
                }
            });

            if (shipmentTrendChart) {
                shipmentTrendChart.destroy();
            }

            const ctx = document.getElementById('shipmentTrendChart').getContext('2d');
            shipmentTrendChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: displayLabels,
                    datasets: [{
                        label: 'Shipments Created',
                        data: values,
                        backgroundColor: 'rgba(91, 94, 255, 0.7)',
                        borderColor: '#5b5eff',
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Shipments: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 11 },
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        function renderCustomerSummaryBarChart(customerSummary) {
            const labels = ['Registrations', 'KYC Pending', 'Onboarded', 'CSB5 Enabled'];
            const values = [
                customerSummary.totalRegistrations,
                customerSummary.kycPending,
                customerSummary.onboardedCustomers,
                customerSummary.csb5Enabled
            ];
            const colors = [
                customerColors.totalRegistrations,
                customerColors.kycPending,
                customerColors.onboardedCustomers,
                customerColors.csb5Enabled
            ];

            if (customerSummaryBarChart) {
                customerSummaryBarChart.destroy();
            }

            const ctx = document.getElementById('customerSummaryBarChart').getContext('2d');
            customerSummaryBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Customer Summary',
                        data: values,
                        backgroundColor: colors.map(c => c + 'cc'),
                        borderColor: colors,
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 12 }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        function renderShipmentDeliveryBarChart(statusCounts, statusMap, deliverySummary) {
            // Build all labels, values, and colors in one flat array — like Customer Summary Bar View
            const allLabels = [];
            const allValues = [];
            const allColors = [];

            // Shipment status entries
            for (const [status, count] of Object.entries(statusCounts)) {
                allLabels.push(statusMap[status] || status);
                allValues.push(count);
                allColors.push(statusColors[status] || '#adb5bd');
            }

            // Delivery/network entries
            allLabels.push('Delivered');
            allValues.push(deliverySummary.delivered);
            allColors.push(deliveryColors.delivered);

            allLabels.push('ShipRocket');
            allValues.push(deliverySummary.shipRocket);
            allColors.push(deliveryColors.shipRocket);

            allLabels.push('Self/Own Network');
            allValues.push(deliverySummary.self);
            allColors.push(deliveryColors.self);

            // Other networks
            if (deliverySummary.otherNetworks) {
                for (const [network, count] of Object.entries(deliverySummary.otherNetworks)) {
                    allLabels.push(network);
                    allValues.push(count);
                    allColors.push(deliveryColors.other);
                }
            }

            if (shipmentDeliveryBarChart) {
                shipmentDeliveryBarChart.destroy();
            }

            const ctx = document.getElementById('shipmentDeliveryBarChart').getContext('2d');
            shipmentDeliveryBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: allLabels,
                    datasets: [{
                        label: 'Shipment & Delivery',
                        data: allValues,
                        backgroundColor: allColors.map(c => c + 'cc'),
                        borderColor: allColors,
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 11 },
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Load default chart data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadChartData('this_month', document.querySelector('.chart-filter-btn[data-filter="this_month"]'));
        });
    </script>

</body>

</html>
