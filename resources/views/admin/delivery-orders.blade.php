<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Delivery Orders | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
        .delivery-count-card {
            border: 0;
            box-shadow: 0 3px 14px rgba(15, 23, 42, .06);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .delivery-count-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(15, 23, 42, .1);
        }
        .delivery-count-card.active {
            border: 1px solid var(--bs-primary);
            background: rgba(91, 94, 255, .04);
        }
        .delivery-count-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
        .delivery-filter-link { text-decoration: none; color: inherit; }
        .location-text { min-width: 230px; max-width: 320px; white-space: normal; }
        .address-line { line-height: 1.45; }
        .pickup-detail-label { color: #6c757d; font-size: 12px; margin-bottom: 2px; }
        .pickup-detail-value { font-weight: 500; overflow-wrap: anywhere; }
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

            @php
                $viewDetails = [
                    'pending' => [
                        'title' => 'Pending Deliveries',
                        'description' => 'Deliveries assigned to you that are still awaiting pickup.',
                        'empty' => 'You do not have any pending deliveries.',
                    ],
                    'process_pickup' => [
                        'title' => 'Process Pickup',
                        'description' => 'Shipments picked up and currently being processed.',
                        'empty' => 'No shipments are currently in Process Pickup.',
                    ],
                    'completed' => [
                        'title' => 'Complete Deliveries',
                        'description' => 'Deliveries assigned to you that have been delivered successfully.',
                        'empty' => 'You have not completed any deliveries yet.',
                    ],
                    'history' => [
                        'title' => 'Delivery History',
                        'description' => 'Complete history of deliveries currently assigned to you.',
                        'empty' => 'No delivery history is available yet.',
                    ],
                ];
                $currentView = $viewDetails[$view] ?? $viewDetails['pending'];
            @endphp

            <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">{{ $currentView['title'] }}</h4>
                    <p class="text-muted mb-0">{{ $currentView['description'] }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-filter me-1"></i>{{ $currentView['title'] }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item {{ $view === 'pending' ? 'active' : '' }}" href="{{ route('admin.delivery-orders', ['view' => 'pending']) }}">Pending Delivery</a></li>
                            <li><a class="dropdown-item {{ $view === 'process_pickup' ? 'active' : '' }}" href="{{ route('admin.delivery-orders', ['view' => 'process_pickup']) }}">Process Pickup</a></li>
                            <li><a class="dropdown-item {{ $view === 'completed' ? 'active' : '' }}" href="{{ route('admin.delivery-orders', ['view' => 'completed']) }}">Complete Delivery</a></li>
                            <li><a class="dropdown-item {{ $view === 'history' ? 'active' : '' }}" href="{{ route('admin.delivery-orders', ['view' => 'history']) }}">Delivery History</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-icon btn-outline-light shadow" title="Refresh" onclick="location.reload()">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>

            <div class="row row-gap-3 mb-4">
                <div class="col-lg-3 d-flex">
                    <a class="delivery-filter-link flex-fill" href="{{ route('admin.delivery-orders', ['view' => 'pending']) }}">
                        <div class="card delivery-count-card {{ $view === 'pending' ? 'active' : '' }} h-100 mb-0">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div><p class="text-muted mb-1">Pending Delivery</p><h3 class="mb-0">{{ $pendingCount }}</h3></div>
                                <span class="delivery-count-icon bg-soft-warning text-warning"><i class="ti ti-clock-hour-4 fs-24"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 d-flex">
                    <a class="delivery-filter-link flex-fill" href="{{ route('admin.delivery-orders', ['view' => 'process_pickup']) }}">
                        <div class="card delivery-count-card {{ $view === 'process_pickup' ? 'active' : '' }} h-100 mb-0">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div><p class="text-muted mb-1">Process Pickup</p><h3 class="mb-0">{{ $processPickupCount }}</h3></div>
                                <span class="delivery-count-icon bg-soft-info text-info"><i class="ti ti-package-import fs-24"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 d-flex">
                    <a class="delivery-filter-link flex-fill" href="{{ route('admin.delivery-orders', ['view' => 'completed']) }}">
                        <div class="card delivery-count-card {{ $view === 'completed' ? 'active' : '' }} h-100 mb-0">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div><p class="text-muted mb-1">Complete Delivery</p><h3 class="mb-0">{{ $completedCount }}</h3></div>
                                <span class="delivery-count-icon bg-soft-success text-success"><i class="ti ti-circle-check fs-24"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 d-flex">
                    <a class="delivery-filter-link flex-fill" href="{{ route('admin.delivery-orders', ['view' => 'history']) }}">
                        <div class="card delivery-count-card {{ $view === 'history' ? 'active' : '' }} h-100 mb-0">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div><p class="text-muted mb-1">Delivery History</p><h3 class="mb-0">{{ $historyCount }}</h3></div>
                                <span class="delivery-count-icon bg-soft-primary text-primary"><i class="ti ti-history fs-24"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div>
                        <h6 class="mb-1">{{ $currentView['title'] }}</h6>
                        <small class="text-muted">{{ $deliveries->total() }} {{ \Illuminate\Support\Str::plural('record', $deliveries->total()) }} found</small>
                    </div>
                    <form method="GET" action="{{ route('admin.delivery-orders') }}" class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="hidden" name="view" value="{{ $view }}">
                        <div class="input-group" style="min-width: 280px;">
                            <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                            <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search AWB, invoice, customer..." aria-label="Search deliveries">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if($search !== '')
                            <a href="{{ route('admin.delivery-orders', ['view' => $view]) }}" class="btn btn-outline-secondary">Clear</a>
                        @endif
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                            <tr>
                                <th>AWB / Invoice</th>
                                <th>Pickup Name & Address</th>
                                <th>Delivery Name & Address</th>
                                <th>Delivery Type</th>
                                <th>Status</th>
                                <th>Assigned / Updated</th>
                                @if($view === 'pending')<th class="text-end">Action</th>@endif
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($deliveries as $delivery)
                                @php
                                    $statusTitle = $statusMap[$delivery->status] ?? ucfirst(str_replace('_', ' ', $delivery->status));
                                    $badgeClass = match($delivery->status) {
                                        'delivered' => 'bg-success',
                                        'cancelled', 'disputed' => 'bg-danger',
                                        'assigned_for_pickup' => 'bg-warning text-dark',
                                        'received', 'ready_to_dispatch', 'dispatched' => 'bg-info',
                                        'on_hold' => 'bg-dark',
                                        default => 'bg-secondary',
                                    };
                                    $pickupAddress = collect([
                                        $delivery->pickup_address_line1,
                                        $delivery->pickup_address_line2,
                                        $delivery->pickup_address_line3,
                                        $delivery->pickup_city,
                                        $delivery->pickup_state,
                                        $delivery->pickup_pincode,
                                    ])->filter()->implode(', ');
                                    $destinationAddress = collect([
                                        $delivery->destination_address_line1,
                                        $delivery->destination_address_line2,
                                        $delivery->destination_address_line3,
                                        $delivery->destination_city,
                                        $delivery->destination_state,
                                        $delivery->destination_pincode,
                                    ])->filter()->implode(', ');
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $delivery->awb_number ?: 'AWB pending' }}</strong><br>
                                        <small class="text-muted">{{ $delivery->invoice_number ?: '-' }}</small>
                                    </td>
                                    <td class="location-text">
                                        <strong>{{ $delivery->pickup_name ?: $delivery->company_name ?: '-' }}</strong>
                                        @if($delivery->company_name && $delivery->company_name !== $delivery->pickup_name)<br><small>{{ $delivery->company_name }}</small>@endif
                                        <div class="address-line text-muted small mt-1"><i class="ti ti-map-pin me-1"></i>{{ $pickupAddress ?: '-' }}</div>
                                        @if($delivery->pickup_phone)<div class="text-muted small"><i class="ti ti-phone me-1"></i>{{ $delivery->pickup_phone }}</div>@endif
                                    </td>
                                    <td class="location-text">
                                        <strong>{{ $delivery->consignee_name ?: $delivery->consignee_contact ?: '-' }}</strong>
                                        <div class="address-line text-muted small mt-1"><i class="ti ti-map-pin me-1"></i>{{ $destinationAddress ?: '-' }}</div>
                                        @if($delivery->destination_phone)<div class="text-muted small"><i class="ti ti-phone me-1"></i>{{ $delivery->destination_phone }}</div>@endif
                                    </td>
                                    <td>{{ $delivery->delivery_type ? ucfirst(str_replace('_', ' ', $delivery->delivery_type)) : '-' }}</td>
                                    <td><span class="badge {{ $badgeClass }}">{{ $statusTitle }}</span></td>
                                    <td>{{ $delivery->assigned_at ? \Carbon\Carbon::parse($delivery->assigned_at)->format('d M Y, h:i A') : '-' }}</td>
                                    @if($view === 'pending')
                                        <td class="text-end">
                                            @if($delivery->status === 'assigned_for_pickup')
                                                <button type="button" class="btn btn-sm btn-primary pickup-btn"
                                                        data-bs-toggle="modal" data-bs-target="#pickupConfirmationModal"
                                                        data-shipment-id="{{ $delivery->id }}"
                                                        data-awb="{{ $delivery->awb_number ?: 'AWB pending' }}"
                                                        data-invoice="{{ $delivery->invoice_number ?: '-' }}"
                                                        data-pickup-name="{{ $delivery->pickup_name ?: $delivery->company_name ?: '-' }}"
                                                        data-pickup-address="{{ $pickupAddress ?: '-' }}"
                                                        data-pickup-phone="{{ $delivery->pickup_phone ?: '-' }}"
                                                        data-delivery-name="{{ $delivery->consignee_name ?: $delivery->consignee_contact ?: '-' }}"
                                                        data-delivery-address="{{ $destinationAddress ?: '-' }}"
                                                        data-delivery-phone="{{ $delivery->destination_phone ?: '-' }}">
                                                    <i class="ti ti-package-import me-1"></i>Pickup
                                                </button>
                                            @else
                                                <span class="text-muted small">In Process</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $view === 'pending' ? 7 : 6 }}" class="text-center text-muted py-5">
                                        <i class="ti ti-package-off fs-30 d-block mb-2"></i>
                                        @if($search !== '')
                                            No deliveries match your search.
                                        @else
                                            {{ $currentView['empty'] }}
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($deliveries->hasPages())
                    <div class="card-footer d-flex justify-content-end">
                        {{ $deliveries->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pickupConfirmationModal" tabindex="-1" aria-labelledby="pickupConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="pickupConfirmationModalLabel">Confirm Shipment Pickup</h5>
                    <small class="text-muted">Verify all shipment details before confirming pickup.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pickupModalAlert" class="alert d-none" role="alert"></div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6"><div class="pickup-detail-label">AWB Number</div><div class="pickup-detail-value" id="modalAwb">-</div></div>
                    <div class="col-sm-6"><div class="pickup-detail-label">Invoice Number</div><div class="pickup-detail-value" id="modalInvoice">-</div></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-primary"><i class="ti ti-package-export me-1"></i>Pickup Details</h6>
                            <div class="pickup-detail-label">Pickup Name</div><div class="pickup-detail-value mb-2" id="modalPickupName">-</div>
                            <div class="pickup-detail-label">Complete Address</div><div class="pickup-detail-value mb-2" id="modalPickupAddress">-</div>
                            <div class="pickup-detail-label">Phone Number</div><div class="pickup-detail-value" id="modalPickupPhone">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-success"><i class="ti ti-map-pin-check me-1"></i>Delivery Details</h6>
                            <div class="pickup-detail-label">Delivery Name</div><div class="pickup-detail-value mb-2" id="modalDeliveryName">-</div>
                            <div class="pickup-detail-label">Complete Address</div><div class="pickup-detail-value mb-2" id="modalDeliveryAddress">-</div>
                            <div class="pickup-detail-label">Phone Number</div><div class="pickup-detail-value" id="modalDeliveryPhone">-</div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning mt-3 mb-0"><i class="ti ti-alert-triangle me-1"></i>Do you want to confirm this pickup and move the delivery to In Process?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmPickupButton"><i class="ti ti-check me-1"></i>Yes, Confirm Pickup</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script>
    const pickupModal = document.getElementById('pickupConfirmationModal');
    const confirmPickupButton = document.getElementById('confirmPickupButton');
    const pickupModalAlert = document.getElementById('pickupModalAlert');
    let selectedShipmentId = null;

    pickupModal?.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        selectedShipmentId = button.dataset.shipmentId;
        pickupModalAlert.className = 'alert d-none';
        pickupModalAlert.textContent = '';
        confirmPickupButton.disabled = false;
        confirmPickupButton.innerHTML = '<i class="ti ti-check me-1"></i>Yes, Confirm Pickup';

        const detailFields = {
            modalAwb: 'awb', modalInvoice: 'invoice', modalPickupName: 'pickupName',
            modalPickupAddress: 'pickupAddress', modalPickupPhone: 'pickupPhone',
            modalDeliveryName: 'deliveryName', modalDeliveryAddress: 'deliveryAddress',
            modalDeliveryPhone: 'deliveryPhone'
        };
        Object.entries(detailFields).forEach(([elementId, dataKey]) => {
            document.getElementById(elementId).textContent = button.dataset[dataKey] || '-';
        });
    });

    confirmPickupButton?.addEventListener('click', async () => {
        if (!selectedShipmentId) return;
        confirmPickupButton.disabled = true;
        confirmPickupButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

        try {
            const response = await fetch('{{ route('admin.pickup-delivery') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ shipment_id: selectedShipmentId })
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Unable to confirm pickup.');

            pickupModalAlert.className = 'alert alert-success';
            pickupModalAlert.textContent = data.message;
            setTimeout(() => window.location.reload(), 900);
        } catch (error) {
            pickupModalAlert.className = 'alert alert-danger';
            pickupModalAlert.textContent = error.message;
            confirmPickupButton.disabled = false;
            confirmPickupButton.innerHTML = '<i class="ti ti-check me-1"></i>Yes, Confirm Pickup';
        }
    });
</script>
</body>
</html>
