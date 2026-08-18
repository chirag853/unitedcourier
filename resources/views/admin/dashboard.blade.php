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

    <!-- Chart.js (local copy - no CDN dependency) -->
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>

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

        /* --- Dashboard polish --- */
        .dash-stat-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .dash-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.09);
        }
        .dash-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            flex-shrink: 0;
        }
        .icon-indigo { background: linear-gradient(135deg, #5b5eff, #7367f0); box-shadow: 0 4px 12px rgba(91, 94, 255, 0.32); }
        .icon-orange { background: linear-gradient(135deg, #ff9f43, #ff7a2e); box-shadow: 0 4px 12px rgba(255, 159, 67, 0.32); }
        .icon-green  { background: linear-gradient(135deg, #1abe17, #0f9d0f); box-shadow: 0 4px 12px rgba(26, 190, 23, 0.32); }
        .icon-red    { background: linear-gradient(135deg, #ff4d4f, #e5383b); box-shadow: 0 4px 12px rgba(255, 77, 79, 0.32); }
        .icon-teal   { background: linear-gradient(135deg, #20c997, #12b3a8); box-shadow: 0 4px 12px rgba(32, 201, 151, 0.32); }
        .icon-purple { background: linear-gradient(135deg, #7367f0, #9b59f6); box-shadow: 0 4px 12px rgba(115, 103, 240, 0.32); }
        .icon-blue   { background: linear-gradient(135deg, #2f80ed, #1e6fd9); box-shadow: 0 4px 12px rgba(47, 128, 237, 0.32); }
        .icon-cyan   { background: linear-gradient(135deg, #0dcaf0, #0891b2); box-shadow: 0 4px 12px rgba(13, 202, 240, 0.32); }

        .stat-trend-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .stat-trend-badge.up { background: rgba(26, 190, 23, 0.12); color: #15803d; }
        .stat-trend-badge.down { background: rgba(239, 30, 30, 0.12); color: #dc2626; }
        .stat-trend-badge.flat { background: rgba(100, 116, 139, 0.12); color: #64748b; }
        [data-bs-theme="dark"] .stat-trend-badge.up { color: #4ade80; }
        [data-bs-theme="dark"] .stat-trend-badge.down { color: #f87171; }
        [data-bs-theme="dark"] .stat-trend-badge.flat { color: #94a3b8; }

        .dash-list-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.08);
        }
        .dash-list-item:last-child { border-bottom: 0; }
        [data-bs-theme="dark"] .dash-list-item { border-bottom-color: rgba(255, 255, 255, 0.08); }
        .dash-avatar {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
            flex-shrink: 0;
        }

        .skeleton {
            position: relative;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.06);
            border-radius: 8px;
        }
        .skeleton::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.45), transparent);
            animation: skeleton-shimmer 1.3s infinite;
        }
        @keyframes skeleton-shimmer {
            100% { transform: translateX(100%); }
        }
        [data-bs-theme="dark"] .skeleton { background: rgba(255, 255, 255, 0.08); }
        [data-bs-theme="dark"] .skeleton::after { background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent); }

        .dash-status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .dashboard-title h4 { letter-spacing: -0.3px; }
        .activity-tab-link { cursor: pointer; user-select: none; }
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
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2 flex-wrap">
                    <div class="dashboard-title">
                        <h4 class="mb-1 fw-semibold">{{ $greeting }}, {{ $adminName }} <i class="ti ti-sun fs-20 text-warning align-middle ms-1"></i></h4>
                        <p class="text-muted fs-13 mb-0">{{ \Carbon\Carbon::now()->format('l, d F Y') }} — Here's what's happening with your courier network today.</p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ route('admin.kyc-pending') }}" class="btn btn-sm btn-outline-warning">
                            <i class="ti ti-file-alert me-1"></i>KYC Pending <span class="badge bg-warning text-dark ms-1">{{ $kycPending }}</span>
                        </a>
                        <a href="{{ route('admin.companies') }}" class="btn btn-primary btn-sm"><i class="ti ti-building me-1"></i>Companies</a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload()"><i class="ti ti-refresh"></i></a>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Quick glance strip -->
                <div class="row row-gap-3 mb-4">
                    <div class="col-12">
                        <div class="card mb-0">
                            <div class="card-body py-3">
                                <div class="row align-items-center text-center text-md-start row-gap-3">
                                    <div class="col-md-3 col-6">
                                        <p class="text-muted fs-13 mb-1"><i class="ti ti-truck-delivery me-1"></i>Shipments Today</p>
                                        <h5 class="mb-0 fw-semibold" id="todayShipments">{{ $todayShipments }}</h5>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <p class="text-muted fs-13 mb-1"><i class="ti ti-user-plus me-1"></i>New Registrations Today</p>
                                        <h5 class="mb-0 fw-semibold" id="todayRegistrations">{{ $todayRegistrations }}</h5>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <p class="text-muted fs-13 mb-1"><i class="ti ti-wallet me-1"></i>Total Wallet Balance</p>
                                        <h5 class="mb-0 fw-semibold">₹ {{ number_format($walletBalanceTotal, 0) }}</h5>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <p class="text-muted fs-13 mb-1"><i class="ti ti-box me-1"></i>Total Shipments (All Time)</p>
                                        <h5 class="mb-0 fw-semibold">{{ number_format($totalShipments) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Quick glance strip -->

                <!-- start row - Customer Summary Stat Cards -->
                <h6 class="mb-2"><i class="ti ti-users me-1"></i>Customer Summary</h6>
                <div class="row row-gap-3 mb-4">
					<!-- Total Registrations -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
							<div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Total Registrations</p>
                                        <h2 class="mb-2 fw-semibold">{{ number_format($totalRegistrations) }}</h2>
                                        @if($registrationsChangePercent > 0)
                                        <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $registrationsChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @elseif($registrationsChangePercent < 0)
                                        <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($registrationsChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @else
                                        <span class="stat-trend-badge flat">No change vs last month</span>
                                        @endif
                                    </div>
                                    <span class="dash-stat-icon icon-indigo"><i class="ti ti-building"></i></span>
                                </div>
							</div>
                            <img src="{{ asset('assets/img/icons/elemnt-01.svg') }}" alt="elemnt-01" class="img-fluid position-absolute top-0 start-0">
						</div>
					</div>
					<!-- /Total Registrations -->

                    <!-- KYC Pending -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
							<div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">KYC Pending</p>
                                        <h2 class="mb-2 fw-semibold">{{ number_format($kycPending) }}</h2>
                                        @if($kycPendingChangePercent > 0)
                                        <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-up"></i>{{ $kycPendingChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @elseif($kycPendingChangePercent < 0)
                                        <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-down"></i>{{ abs($kycPendingChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @else
                                        <span class="stat-trend-badge flat">No change vs last month</span>
                                        @endif
                                    </div>
                                    <span class="dash-stat-icon icon-orange"><i class="ti ti-clock"></i></span>
                                </div>
							</div>
                            <img src="{{ asset('assets/img/icons/elemnt-02.svg') }}" alt="elemnt-02" class="img-fluid position-absolute top-0 start-0">
						</div>
					</div>
					<!-- /KYC Pending -->

                    <!-- Onboarded Customers -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
							<div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Onboarded Customers</p>
                                        <h2 class="mb-2 fw-semibold">{{ number_format($onboardedCustomers) }}</h2>
                                        @if($onboardedChangePercent > 0)
                                        <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $onboardedChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @elseif($onboardedChangePercent < 0)
                                        <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($onboardedChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @else
                                        <span class="stat-trend-badge flat">No change vs last month</span>
                                        @endif
                                    </div>
                                    <span class="dash-stat-icon icon-green"><i class="ti ti-user-check"></i></span>
                                </div>
							</div>
                            <img src="{{ asset('assets/img/icons/elemnt-03.svg') }}" alt="elemnt-03" class="img-fluid position-absolute top-0 start-0">
						</div>
					</div>
					<!-- /Onboarded Customers -->

                    <!-- CSB5 Enabled -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
							<div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">CSB5 Enabled</p>
                                        <h2 class="mb-2 fw-semibold">{{ number_format($csb5Enabled) }}</h2>
                                        @if($csb5ChangePercent > 0)
                                        <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $csb5ChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @elseif($csb5ChangePercent < 0)
                                        <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($csb5ChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @else
                                        <span class="stat-trend-badge flat">No change vs last month</span>
                                        @endif
                                    </div>
                                    <span class="dash-stat-icon icon-purple"><i class="ti ti-file-check"></i></span>
                                </div>
							</div>
                            <img src="{{ asset('assets/img/icons/elemnt-04.svg') }}" alt="elemnt-04" class="img-fluid position-absolute top-0 start-0">
						</div>
					</div>
					<!-- /CSB5 Enabled -->

				</div>
                <!-- end row -->

                <!-- start row - Business Summary Stat Cards -->
                <h6 class="mb-2"><i class="ti ti-trending-up me-1"></i>Business Summary</h6>
                <div class="row row-gap-3 mb-4">
                    <!-- Revenue -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Revenue (This Month)</p>
                                        <h2 class="mb-2 fw-semibold" id="statRevenue">₹ {{ number_format($thisMonthRevenue) }}</h2>
                                        @if($revenueChangePercent > 0)
                                        <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $revenueChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @elseif($revenueChangePercent < 0)
                                        <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($revenueChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @else
                                        <span class="stat-trend-badge flat">No change vs last month</span>
                                        @endif
                                    </div>
                                    <span class="dash-stat-icon icon-green"><i class="ti ti-currency-rupee"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-01.svg') }}" alt="elemnt-01" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Revenue -->

                    <!-- In-Transit -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">In-Transit Shipments</p>
                                        <h2 class="mb-2 fw-semibold" id="statInTransit">{{ number_format($inTransit) }}</h2>
                                        <span class="stat-trend-badge flat">Moving through the network</span>
                                    </div>
                                    <span class="dash-stat-icon icon-blue"><i class="ti ti-truck"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-02.svg') }}" alt="elemnt-02" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /In-Transit -->

                    <!-- Wallet Top-ups -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Wallet Top-ups (This Month)</p>
                                        <h2 class="mb-2 fw-semibold" id="statWalletTopups">₹ {{ number_format($thisMonthWalletTopups) }}</h2>
                                        @if($walletTopupsChangePercent > 0)
                                        <span class="stat-trend-badge up"><i class="ti ti-arrow-bar-up"></i>{{ $walletTopupsChangePercent }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @elseif($walletTopupsChangePercent < 0)
                                        <span class="stat-trend-badge down"><i class="ti ti-arrow-bar-down"></i>{{ abs($walletTopupsChangePercent) }}% <span class="fw-normal ms-1">vs last month</span></span>
                                        @else
                                        <span class="stat-trend-badge flat">No change vs last month</span>
                                        @endif
                                    </div>
                                    <span class="dash-stat-icon icon-orange"><i class="ti ti-wallet"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-03.svg') }}" alt="elemnt-03" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Wallet Top-ups -->

                    <!-- Delivery Success Rate -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Delivery Success Rate</p>
                                        <h2 class="mb-2 fw-semibold" id="statSuccessRate">{{ $deliverySuccessRate }}%</h2>
                                        <span class="stat-trend-badge flat">Delivered vs non-cancelled</span>
                                    </div>
                                    <span class="dash-stat-icon icon-teal"><i class="ti ti-circle-check"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-04.svg') }}" alt="elemnt-04" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Delivery Success Rate -->
                </div>
                <!-- end row - Business Summary Stat Cards -->

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
                                                    <a href="{{ route('admin.kyc-pending') }}" class="btn btn-sm btn-outline-primary" title="View KYC Details">
                                                        <i class="ti ti-eye me-1"></i>View
                                                    </a>
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
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center flex-wrap gap-4">
                                    <div style="position: relative; width: 210px; height: 210px;">
                                        <canvas id="customerSummaryChart"></canvas>
                                    </div>
                                    <div id="customerSummaryLegend" class="flex-fill" style="min-width: 210px;"></div>
                                </div>
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
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Total Shipments</p>
                                        <h2 class="mb-2 fw-semibold" id="statTotalShipments">{{ number_format(array_sum($shipmentStatusCounts)) }}</h2>
                                        <span class="stat-trend-badge flat" id="statTotalShipmentsSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-indigo"><i class="ti ti-package"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-01.svg') }}" alt="elemnt-01" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Total Shipments -->

                    <!-- Delivered -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Delivered</p>
                                        <h2 class="mb-2 fw-semibold" id="statDelivered">{{ number_format($deliveredCount) }}</h2>
                                        <span class="stat-trend-badge flat" id="statDeliveredSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-green"><i class="ti ti-truck-delivery"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-02.svg') }}" alt="elemnt-02" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /Delivered -->

                    <!-- ShipRocket -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">ShipRocket</p>
                                        <h2 class="mb-2 fw-semibold" id="statShipRocket">{{ number_format($shipRocketCount) }}</h2>
                                        <span class="stat-trend-badge flat" id="statShipRocketSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-cyan"><i class="ti ti-rocket"></i></span>
                                </div>
                            </div>
                            <img src="{{ asset('assets/img/icons/elemnt-03.svg') }}" alt="elemnt-03" class="img-fluid position-absolute top-0 start-0">
                        </div>
                    </div>
                    <!-- /ShipRocket -->

                    <!-- Self/Own Network -->
                    <div class="col-xl-3 col-sm-6 d-flex">
                        <div class="card dash-stat-card flex-fill mb-0 position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <p class="fs-14 mb-1 text-body">Self/Own Network</p>
                                        <h2 class="mb-2 fw-semibold" id="statSelfNetwork">{{ number_format($selfCount) }}</h2>
                                        <span class="stat-trend-badge flat" id="statSelfNetworkSub"><i class="ti ti-calendar"></i><span>for selected period</span></span>
                                    </div>
                                    <span class="dash-stat-icon icon-orange"><i class="ti ti-world"></i></span>
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

                <!-- start row - Recent Activity -->
                <div class="row row-gap-3 mb-4">
                    <!-- Recent Shipments -->
                    <div class="col-xl-7 col-lg-12 d-flex">
                        <div class="card flex-fill mb-0">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="mb-0"><i class="ti ti-truck me-1"></i>Recent Shipments</h6>
                                <a href="{{ route('admin.companies') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                @if($recentShipments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>AWB / Invoice</th>
                                                <th>Company</th>
                                                <th>Route</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentShipments as $shipment)
                                            <tr>
                                                <td>
                                                    <span class="fw-semibold d-block">{{ $shipment->awb_number ?? '—' }}</span>
                                                    <small class="text-muted">{{ $shipment->invoice_number ?? '' }}</small>
                                                </td>
                                                <td>{{ $shipment->company_name ?? (trim(($shipment->first_name ?? '') . ' ' . ($shipment->last_name ?? '')) ?: '—') }}</td>
                                                <td>
                                                    <span class="d-block">{{ $shipment->pickup_city ?? '—' }}</span>
                                                    <small class="text-muted"><i class="ti ti-arrow-right me-1"></i>{{ $shipment->destination_city ?? '—' }}</small>
                                                </td>
                                                <td>
                                                    @if($shipment->invoice_amount)
                                                    <span class="fw-semibold">₹ {{ number_format($shipment->invoice_amount, 2) }}</span>
                                                    @else
                                                    <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $statusKey = $shipment->status;
                                                        $statusTitle = \App\Models\Tracking::getTitleForStatus($statusKey);
                                                        $statusBadgeColors = [
                                                            'delivered' => 'bg-success-subtle text-success',
                                                            'dispatched' => 'bg-primary-subtle text-primary',
                                                            'manifested' => 'bg-info-subtle text-info',
                                                            'cancelled' => 'bg-danger-subtle text-danger',
                                                            'disputed' => 'bg-danger-subtle text-danger',
                                                            'on_hold' => 'bg-warning-subtle text-warning',
                                                            'received' => 'bg-info-subtle text-info',
                                                            'ready_to_dispatch' => 'bg-primary-subtle text-primary',
                                                            'packed' => 'bg-primary-subtle text-primary',
                                                            'draft' => 'bg-secondary-subtle text-secondary',
                                                            'ready' => 'bg-primary-subtle text-primary',
                                                            'assigned_for_pickup' => 'bg-primary-subtle text-primary',
                                                        ];
                                                    @endphp
                                                    <span class="dash-status-badge {{ $statusBadgeColors[$statusKey] ?? 'bg-secondary-subtle text-secondary' }}">{{ $statusTitle }}</span>
                                                </td>
                                                <td class="text-muted">{{ \Carbon\Carbon::parse($shipment->created_at)->format('d M, h:i A') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="ti ti-truck fs-24 text-muted"></i>
                                    <p class="text-muted mb-0">No shipments yet</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /Recent Shipments -->

                    <!-- Recent Registrations -->
                    <div class="col-xl-5 col-lg-12 d-flex">
                        <div class="card flex-fill mb-0">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="mb-0"><i class="ti ti-user-plus me-1"></i>Recent Registrations</h6>
                                <a href="{{ route('admin.kyc-pending') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body py-2">
                                @if($recentRegistrations->count() > 0)
                                @foreach($recentRegistrations as $customer)
                                @php
                                    $initial = strtoupper(substr($customer->first_name ?? ($customer->email ?? 'U'), 0, 1));
                                    $avatarPalette = ['icon-indigo', 'icon-orange', 'icon-green', 'icon-purple', 'icon-teal', 'icon-blue'];
                                    $avatarClass = $avatarPalette[($customer->id ?? 0) % count($avatarPalette)];
                                @endphp
                                <div class="dash-list-item">
                                    <span class="dash-avatar {{ $avatarClass }}">{{ $initial }}</span>
                                    <div class="flex-fill min-w-0">
                                        <p class="mb-0 fw-semibold text-truncate">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                                        <small class="text-muted text-truncate d-block">{{ $customer->email }}</small>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <span class="stat-trend-badge flat">{{ \Carbon\Carbon::parse($customer->created_at)->format('d M') }}</span>
                                        <small class="text-muted d-block">{{ \Carbon\Carbon::parse($customer->created_at)->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="text-center py-3">
                                    <i class="ti ti-user-off fs-24 text-muted"></i>
                                    <p class="text-muted mb-0">No registrations yet</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /Recent Registrations -->
                </div>
                <!-- end row - Recent Activity -->

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

        // ---- Helpers -------------------------------------------------------
        function formatNumber(value) {
            return Number(value || 0).toLocaleString('en-IN');
        }

        function formatCurrency(value) {
            return '₹ ' + formatNumber(Math.round(Number(value || 0)));
        }

        function isDarkTheme() {
            return document.documentElement.getAttribute('data-bs-theme') === 'dark';
        }

        function cssVar(name, fallback) {
            const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
            return value || fallback;
        }

        // Resolve template theme colors (CSS variables) for canvas rendering,
        // so charts stay consistent in both light and dark mode.
        function themeColors() {
            const dark = isDarkTheme();
            return {
                text: dark ? 'rgba(255, 255, 255, 0.85)' : 'rgba(30, 41, 59, 0.9)',
                subText: dark ? 'rgba(255, 255, 255, 0.55)' : 'rgba(100, 116, 139, 0.9)',
                grid: dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)',
                cardBg: dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(255, 255, 255, 0.9)',
                primary: cssVar('--crms-primary', '#2563eb')
            };
        }

        function applyChartDefaults() {
            const colors = themeColors();
            Chart.defaults.color = colors.text;
            Chart.defaults.borderColor = colors.grid;
        }

        // Skeleton shimmer overlay for chart canvases while data refreshes
        function showChartLoading() {
            document.querySelectorAll('.chart-card').forEach(card => {
                if (!card.querySelector('.skeleton-overlay')) {
                    const overlay = document.createElement('div');
                    overlay.className = 'skeleton-overlay';
                    overlay.style.cssText = 'position:absolute;inset:0;z-index:5;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.65);backdrop-filter:blur(2px);border-radius:12px;';
                    if (isDarkTheme()) overlay.style.background = 'rgba(20,25,35,0.7)';
                    overlay.innerHTML = '<div class="spinner-border text-primary" role="status" style="width:26px;height:26px;"></div>';
                    card.style.position = 'relative';
                    card.appendChild(overlay);
                }
            });
        }

        function hideChartLoading() {
            document.querySelectorAll('.skeleton-overlay').forEach(el => el.remove());
        }

        // Color palette for charts
        const customerColors = {
            totalRegistrations: '#5b5eff',
            kycPending: '#ff9f43',
            onboardedCustomers: '#ff4d4f',
            csb5Enabled: '#7367f0'
        };

        const shipmentStatusOrder = [
            'draft',
            'ready',
            'assigned_for_pickup',
            'packed',
            'manifested',
            'dispatched',
            'ready_to_dispatch',
            'delivered',
            'cancelled',
            'disputed',
            'on_hold',
            'received'
        ];

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

            showChartLoading();

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
                    updateBusinessStatTiles(data.businessSummary || {});
                }
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
            })
            .finally(() => {
                hideChartLoading();
            });
        }

        function updateBusinessStatTiles(businessSummary) {
            document.getElementById('statRevenue').textContent = formatCurrency(businessSummary.revenue);
            document.getElementById('statInTransit').textContent = formatNumber(businessSummary.inTransit);
            document.getElementById('statWalletTopups').textContent = formatCurrency(businessSummary.walletTopups);
            document.getElementById('statSuccessRate').textContent = (businessSummary.successRate || 0) + '%';
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

            document.getElementById('statTotalShipments').textContent = formatNumber(totalShipments);
            document.getElementById('statTotalShipmentsSub').querySelector('span').textContent = 'for ' + periodLabel;

            document.getElementById('statDelivered').textContent = formatNumber(delivered);
            document.getElementById('statDeliveredSub').querySelector('span').textContent = 'for ' + periodLabel;

            document.getElementById('statShipRocket').textContent = formatNumber(shipRocket);
            document.getElementById('statShipRocketSub').querySelector('span').textContent = 'for ' + periodLabel;

            document.getElementById('statSelfNetwork').textContent = formatNumber(selfNetwork);
            document.getElementById('statSelfNetworkSub').querySelector('span').textContent = 'for ' + periodLabel;
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
                        borderWidth: 4,
                        borderColor: themeColors().cardBg,
                        borderRadius: 8,
                        spacing: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
                    cutout: '72%'
                }
            });

            renderCustomerSummaryLegend(labels, values, colors);
        }

        function renderCustomerSummaryLegend(labels, values, colors) {
            const legend = document.getElementById('customerSummaryLegend');
            if (!legend) return;

            const total = values.reduce((a, b) => a + b, 0);
            legend.innerHTML = labels.map((label, index) => {
                const value = values[index] || 0;
                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';

                return `
                    <div class="d-flex align-items-center justify-content-between rounded-3 px-3 py-2 mb-2" style="background: ${colors[index]}14;">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 12px; height: 12px; border-radius: 50%; background: ${colors[index]}; display: inline-block;"></span>
                            <span class="text-muted fs-13">${label}</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-semibold text-dark">${value}</span>
                            <span class="text-muted fs-12 ms-1">${percentage}%</span>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function buildShipmentDeliveryData(statusCounts, statusMap, deliverySummary) {
            const labels = [];
            const values = [];
            const colors = [];
            const knownStatuses = new Set(shipmentStatusOrder);

            shipmentStatusOrder.forEach(status => {
                labels.push(statusMap[status] || status.replace(/_/g, ' '));
                values.push(statusCounts[status] || 0);
                colors.push(statusColors[status] || '#adb5bd');
            });

            Object.entries(statusCounts).forEach(([status, count]) => {
                if (!knownStatuses.has(status)) {
                    labels.push(statusMap[status] || status.replace(/_/g, ' '));
                    values.push(count || 0);
                    colors.push(statusColors[status] || '#adb5bd');
                }
            });

            return { labels, values, colors };
        }

        function renderShipmentDeliverySummaryChart(statusCounts, statusMap, deliverySummary) {
            const { labels, values, colors } = buildShipmentDeliveryData(statusCounts, statusMap, deliverySummary);

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
                        borderColor: themeColors().cardBg,
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
            const colors = themeColors();
            const gradient = ctx.createLinearGradient(0, 0, 0, 320);
            gradient.addColorStop(0, 'rgba(255, 159, 67, 0.35)');
            gradient.addColorStop(1, 'rgba(255, 159, 67, 0.02)');

            shipmentTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: displayLabels,
                    datasets: [{
                        label: 'Shipments Created',
                        data: values,
                        backgroundColor: gradient,
                        borderColor: '#ff9f43',
                        borderWidth: 3,
                        pointBackgroundColor: '#ff9f43',
                        pointBorderColor: colors.cardBg,
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
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
                                color: colors.grid
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
                        backgroundColor: colors,
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
                                color: themeColors().grid
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
            const { labels: allLabels, values: allValues, colors: allColors } = buildShipmentDeliveryData(statusCounts, statusMap, deliverySummary);

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
                                color: themeColors().grid
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
            applyChartDefaults();
            loadChartData('this_month', document.querySelector('.chart-filter-btn[data-filter="this_month"]'));

            // Re-apply theme-aware chart styling when the theme changes
            const themeObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                        applyChartDefaults();
                        loadChartData(
                            document.querySelector('.chart-filter-btn.active')?.getAttribute('data-filter') || 'this_month',
                            document.querySelector('.chart-filter-btn.active')
                        );
                    }
                });
            });
            themeObserver.observe(document.documentElement, { attributes: true });
        });
    </script>

</body>

</html>
