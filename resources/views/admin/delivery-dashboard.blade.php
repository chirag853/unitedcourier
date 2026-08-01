<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Delivery Dashboard | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <style>
        .delivery-stat-card { border: 0; box-shadow: 0 3px 14px rgba(15, 23, 42, .06); }
        .delivery-stat-icon { width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; }
        .chart-box { position: relative; min-height: 320px; }
        .chart-filter-btn { padding: 5px 12px; border: 1px solid #dee2e6; border-radius: 6px; background: #fff; color: #495057; font-size: 12px; }
        .chart-filter-btn.active, .chart-filter-btn:hover { background: #5b5eff; border-color: #5b5eff; color: #fff; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
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
                    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-x me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Delivery Dashboard</h4>
                    <p class="text-muted mb-0">Welcome, {{ $admin->name }}. These statistics only include deliveries assigned to you.</p>
                </div>
                <button type="button" class="btn btn-icon btn-outline-light shadow" title="Refresh" onclick="location.reload()">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>

            <div class="row row-gap-3 mb-4">
                <div class="col-xl-3 col-sm-6 d-flex">
                    <div class="card delivery-stat-card flex-fill mb-0"><div class="card-body d-flex justify-content-between align-items-center">
                        <div><p class="text-muted mb-1">Total Assigned</p><h3 class="mb-0">{{ $totalAssigned }}</h3></div>
                        <span class="delivery-stat-icon bg-soft-primary text-primary"><i class="ti ti-clipboard-list fs-24"></i></span>
                    </div></div>
                </div>
                <div class="col-xl-3 col-sm-6 d-flex">
                    <div class="card delivery-stat-card flex-fill mb-0"><div class="card-body d-flex justify-content-between align-items-center">
                        <div><p class="text-muted mb-1">Deliveries Performed</p><h3 class="mb-0">{{ $performed }}</h3></div>
                        <span class="delivery-stat-icon bg-soft-info text-info"><i class="ti ti-truck-delivery fs-24"></i></span>
                    </div></div>
                </div>
                <div class="col-xl-3 col-sm-6 d-flex">
                    <div class="card delivery-stat-card flex-fill mb-0"><div class="card-body d-flex justify-content-between align-items-center">
                        <div><p class="text-muted mb-1">In Progress</p><h3 class="mb-0">{{ $inProgress }}</h3></div>
                        <span class="delivery-stat-icon bg-soft-warning text-warning"><i class="ti ti-progress fs-24"></i></span>
                    </div></div>
                </div>
                <div class="col-xl-3 col-sm-6 d-flex">
                    <div class="card delivery-stat-card flex-fill mb-0"><div class="card-body d-flex justify-content-between align-items-center">
                        <div><p class="text-muted mb-1">Completed</p><h3 class="mb-0">{{ $completed }}</h3><small class="text-success">{{ $completionPercentage }}% completion</small></div>
                        <span class="delivery-stat-icon bg-soft-success text-success"><i class="ti ti-circle-check fs-24"></i></span>
                    </div></div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-4 d-flex">
                    <div class="card flex-fill mb-3 mb-lg-0">
                        <div class="card-header"><h6 class="mb-0">Current Status Distribution</h6></div>
                        <div class="card-body chart-box"><canvas id="statusChart"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-8 d-flex">
                    <div class="card flex-fill mb-0">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h6 class="mb-0">Assignment & Completion Trend</h6>
                            <div class="d-flex gap-1 flex-wrap">
                                <button class="chart-filter-btn" data-filter="today">Today</button>
                                <button class="chart-filter-btn" data-filter="yesterday">Yesterday</button>
                                <button class="chart-filter-btn active" data-filter="this_month">This Month</button>
                                <button class="chart-filter-btn" data-filter="last_month">Last Month</button>
                                <button class="chart-filter-btn" data-filter="last_year">Last Year</button>
                            </div>
                        </div>
                        <div class="card-body chart-box"><canvas id="trendChart"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Recent Assigned Deliveries</h6>
                    <span class="badge bg-primary">Pending Pickup: {{ $pendingPickup }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>AWB / Invoice</th><th>Pickup Name & Address</th><th>Delivery Name & Address</th><th>Status</th><th>Assigned/Updated</th></tr></thead>
                            <tbody>
                            @forelse($recentDeliveries as $delivery)
                                @php
                                    $statusTitle = $statusMap[$delivery->status] ?? ucfirst(str_replace('_', ' ', $delivery->status));
                                    $badgeClass = match($delivery->status) {
                                        'delivered' => 'bg-success',
                                        'cancelled', 'disputed' => 'bg-danger',
                                        'assigned_for_pickup' => 'bg-warning text-dark',
                                        'received', 'ready_to_dispatch', 'dispatched' => 'bg-info',
                                        default => 'bg-secondary',
                                    };
                                    $pickupAddress = collect([$delivery->pickup_address_line1, $delivery->pickup_address_line2, $delivery->pickup_address_line3, $delivery->pickup_city, $delivery->pickup_state, $delivery->pickup_pincode])->filter()->implode(', ');
                                    $destinationAddress = collect([$delivery->destination_address_line1, $delivery->destination_address_line2, $delivery->destination_address_line3, $delivery->destination_city, $delivery->destination_state, $delivery->destination_pincode])->filter()->implode(', ');
                                @endphp
                                <tr>
                                    <td><strong>{{ $delivery->awb_number ?: 'AWB pending' }}</strong><br><small class="text-muted">{{ $delivery->invoice_number ?: '-' }}</small></td>
                                    <td style="min-width: 240px;"><strong>{{ $delivery->pickup_name ?: $delivery->company_name ?: '-' }}</strong><br><small class="text-muted"><i class="ti ti-map-pin me-1"></i>{{ $pickupAddress ?: '-' }}</small>@if($delivery->pickup_phone)<br><small class="text-muted"><i class="ti ti-phone me-1"></i>{{ $delivery->pickup_phone }}</small>@endif</td>
                                    <td style="min-width: 240px;"><strong>{{ $delivery->consignee_name ?: '-' }}</strong><br><small class="text-muted"><i class="ti ti-map-pin me-1"></i>{{ $destinationAddress ?: '-' }}</small>@if($delivery->destination_phone)<br><small class="text-muted"><i class="ti ti-phone me-1"></i>{{ $delivery->destination_phone }}</small>@endif</td>
                                    <td><span class="badge {{ $badgeClass }}">{{ $statusTitle }}</span></td>
                                    <td>{{ $delivery->assigned_at ? \Carbon\Carbon::parse($delivery->assigned_at)->format('d M Y, h:i A') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4"><i class="ti ti-package-off fs-24 d-block mb-2"></i>No deliveries have been assigned yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script>
    const initialStatusCounts = @json($statusCounts);
    const initialStatusMap = @json($statusMap);
    const chartColors = ['#5b5eff', '#20c997', '#ffc107', '#17a2b8', '#28a745', '#dc3545', '#6f42c1', '#6c757d'];
    let statusChart;
    let trendChart;

    function renderStatusChart(statusCounts, statusMap) {
        const statuses = Object.keys(statusCounts);
        const labels = statuses.map(status => statusMap[status] || status.replaceAll('_', ' '));
        const values = statuses.map(status => Number(statusCounts[status] || 0));

        if (statusChart) statusChart.destroy();
        statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: { labels, datasets: [{ data: values, backgroundColor: chartColors.slice(0, values.length), borderWidth: 2, borderColor: '#fff' }] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                    tooltip: { callbacks: { label: context => `${context.label}: ${context.raw}` } }
                }
            }
        });
    }

    function renderTrendChart(assignmentTrend, completionTrend) {
        const labels = [...new Set([...Object.keys(assignmentTrend), ...Object.keys(completionTrend)])].sort();
        if (trendChart) trendChart.destroy();
        trendChart = new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'Assigned', data: labels.map(label => Number(assignmentTrend[label] || 0)), borderColor: '#5b5eff', backgroundColor: 'rgba(91,94,255,.12)', fill: true, tension: .35 },
                    { label: 'Completed', data: labels.map(label => Number(completionTrend[label] || 0)), borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,.08)', fill: true, tension: .35 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
            }
        });
    }

    async function loadChartData(filter, button) {
        document.querySelectorAll('.chart-filter-btn').forEach(item => item.classList.remove('active'));
        if (button) button.classList.add('active');

        try {
            const response = await fetch(`{{ route('admin.delivery-dashboard-chart-data') }}?filter=${encodeURIComponent(filter)}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Unable to load delivery statistics.');
            const data = await response.json();
            renderStatusChart(data.statusCounts, data.statusMap);
            renderTrendChart(data.assignmentTrend, data.completionTrend);
        } catch (error) {
            console.error(error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderStatusChart(initialStatusCounts, initialStatusMap);
        document.querySelectorAll('.chart-filter-btn').forEach(button => {
            button.addEventListener('click', () => loadChartData(button.dataset.filter, button));
        });
        loadChartData('this_month', document.querySelector('[data-filter="this_month"]'));
    });
</script>
</body>
</html>
