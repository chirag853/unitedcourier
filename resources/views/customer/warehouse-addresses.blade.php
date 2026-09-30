<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Warehouse Address | United Courier</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
        .page-wrapper .content {
            padding: 1rem;
            background: #f6f8fc;
        }
        .location-text { min-width: 220px; max-width: 300px; white-space: normal; }
    </style>
</head>
<body>
<div class="main-wrapper">
    @include('customer.partials.customer_dashboard_header')
    @include('customer.partials.sidebar')

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
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-x me-2"></i>{{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Warehouse Address</h4>
                    <p class="text-muted mb-0">Pickup warehouses with return address details.</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#warehouseModal">
                    <i class="ti ti-plus me-1"></i>Add Warehouse
                </button>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-1">Saved Warehouses</h6>
                    <small class="text-muted">{{ $warehouses->count() }} {{ \Illuminate\Support\Str::plural('record', $warehouses->count()) }} found</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Warehouse</th>
                                <th>Pickup Address</th>
                                <th>Registered Name</th>
                                <th>Return Address</th>
                                <th class="text-end">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($warehouses as $index => $warehouse)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $warehouse->name }}</strong><br>
                                        @if($warehouse->phone)<small class="text-muted"><i class="ti ti-phone me-1"></i>{{ $warehouse->phone }}</small>@endif
                                        @if($warehouse->email)<br><small class="text-muted"><i class="ti ti-mail me-1"></i>{{ $warehouse->email }}</small>@endif
                                    </td>
                                    <td class="location-text">
                                        <div class="small">{{ collect([$warehouse->address, $warehouse->city, $warehouse->state, $warehouse->pin, $warehouse->country])->filter()->implode(', ') ?: '-' }}</div>
                                    </td>
                                    <td>{{ $warehouse->registered_name ?: '-' }}</td>
                                    <td class="location-text">
                                        <div class="small">{{ collect([$warehouse->return_address, $warehouse->return_city, $warehouse->return_state, $warehouse->return_pin, $warehouse->return_country])->filter()->implode(', ') ?: '-' }}</div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon warehouse-edit-btn" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#warehouseEditModal"
                                                data-id="{{ $warehouse->id }}"
                                                data-name="{{ $warehouse->name }}"
                                                data-phone="{{ $warehouse->phone }}"
                                                data-email="{{ $warehouse->email }}"
                                                data-address="{{ $warehouse->address }}"
                                                data-city="{{ $warehouse->city }}"
                                                data-state="{{ $warehouse->state }}"
                                                data-pin="{{ $warehouse->pin }}"
                                                data-country="{{ $warehouse->country }}"
                                                data-registered-name="{{ $warehouse->registered_name }}"
                                                data-return-address="{{ $warehouse->return_address }}"
                                                data-return-pin="{{ $warehouse->return_pin }}"
                                                data-return-city="{{ $warehouse->return_city }}"
                                                data-return-state="{{ $warehouse->return_state }}"
                                                data-return-country="{{ $warehouse->return_country }}">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="ti ti-building-warehouse fs-30 d-block mb-2"></i>
                                        No warehouse address added yet.
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

<div class="modal fade" id="warehouseModal" tabindex="-1" aria-labelledby="warehouseModalLabel" aria-hidden="true">
@php
    // Same India state list as create-shipment Shipper Info (code => name).
    $indiaStates = [
        'AN' => 'Andaman And Nicobar Islands', 'AP' => 'Andhra Pradesh', 'AR' => 'Arunachal Pradesh',
        'AS' => 'Assam', 'BR' => 'Bihar', 'CH' => 'Chandigarh', 'CT' => 'Chhattisgarh',
        'DN' => 'Dadra And Nagar Haveli And Daman And Diu', 'DL' => 'Delhi', 'GA' => 'Goa',
        'GJ' => 'Gujarat', 'HR' => 'Haryana', 'HP' => 'Himachal Pradesh', 'JK' => 'Jammu And Kashmir',
        'JH' => 'Jharkhand', 'KA' => 'Karnataka', 'KL' => 'Kerala', 'LA' => 'Ladakh', 'LD' => 'Lakshadweep',
        'MP' => 'Madhya Pradesh', 'MH' => 'Maharashtra', 'MN' => 'Manipur', 'ML' => 'Meghalaya',
        'MZ' => 'Mizoram', 'NL' => 'Nagaland', 'OD' => 'Odisha', 'PY' => 'Puducherry', 'PB' => 'Punjab',
        'RJ' => 'Rajasthan', 'SK' => 'Sikkim', 'TN' => 'Tamil Nadu', 'TG' => 'Telangana', 'TR' => 'Tripura',
        'UP' => 'Uttar Pradesh', 'UK' => 'Uttarakhand', 'WB' => 'West Bengal',
    ];
@endphp
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('customer.warehouse-addresses.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="warehouseModalLabel">Add Warehouse Address</h5>
                        <small class="text-muted">Pickup + return details for this warehouse.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-primary mb-3"><i class="ti ti-building-warehouse me-1"></i>Warehouse Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="test_customer_id_warehouse_1" required maxlength="150">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registered Name</label>
                            <input type="text" name="registered_name" class="form-control" placeholder="test client" maxlength="150">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="9999999999" maxlength="30">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="abc@gmail.com" maxlength="150">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="address" maxlength="255">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">State</label>
                            <select name="state" class="form-select">
                                <option value="">-- Select State --</option>
                                @foreach($indiaStates as $stateCode => $stateName)
                                    <option value="{{ $stateCode }}">{{ $stateName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" placeholder="Kota" maxlength="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pin</label>
                            <input type="text" name="pin" class="form-control" placeholder="110042" maxlength="20">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" placeholder="India" maxlength="100">
                        </div>
                    </div>
                    <h6 class="text-success mb-3"><i class="ti ti-arrow-back-up me-1"></i>Return Address</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Return Address</label>
                            <input type="text" name="return_address" class="form-control" placeholder="test return_address" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return City</label>
                            <input type="text" name="return_city" class="form-control" placeholder="Kota" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return State</label>
                            <select name="return_state" class="form-control">
                                <option value="">-- Select State --</option>
                                @foreach($indiaStates as $stateCode => $stateName)
                                    <option value="{{ $stateCode }}">{{ $stateName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return Pin</label>
                            <input type="text" name="return_pin" class="form-control" placeholder="110042" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return Country</label>
                            <input type="text" name="return_country" class="form-control" placeholder="India" maxlength="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i>Save Warehouse</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="warehouseEditModal" tabindex="-1" aria-labelledby="warehouseEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="warehouseEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="warehouseEditModalLabel">Edit Warehouse Address</h5>
                        <small class="text-muted">Update pickup + return details.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-primary mb-3"><i class="ti ti-building-warehouse me-1"></i>Warehouse Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required maxlength="150">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registered Name</label>
                            <input type="text" name="registered_name" id="edit_registered_name" class="form-control" maxlength="150">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control" maxlength="30">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" maxlength="150">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" id="edit_address" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">State</label>
                            <select name="state" id="edit_state" class="form-select">
                                <option value="">-- Select State --</option>
                                @foreach($indiaStates as $stateCode => $stateName)
                                    <option value="{{ $stateCode }}">{{ $stateName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" id="edit_city" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pin</label>
                            <input type="text" name="pin" id="edit_pin" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" id="edit_country" class="form-control" maxlength="100">
                        </div>
                    </div>
                    <h6 class="text-success mb-3"><i class="ti ti-arrow-back-up me-1"></i>Return Address</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Return Address</label>
                            <input type="text" name="return_address" id="edit_return_address" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return City</label>
                            <input type="text" name="return_city" id="edit_return_city" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return State</label>
                            <select name="return_state" id="edit_return_state" class="form-select">
                                <option value="">-- Select State --</option>
                                @foreach($indiaStates as $stateCode => $stateName)
                                    <option value="{{ $stateCode }}">{{ $stateName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return Pin</label>
                            <input type="text" name="return_pin" id="edit_return_pin" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return Country</label>
                            <input type="text" name="return_country" id="edit_return_country" class="form-control" maxlength="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i>Update Warehouse</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script>
    const warehouseEditModal = document.getElementById('warehouseEditModal');
    const warehouseEditForm = document.getElementById('warehouseEditForm');
    const warehouseBaseUrl = "{{ url('customer/warehouse-addresses') }}";
    const warehouseFields = ['name', 'phone', 'email', 'address', 'city', 'state', 'pin', 'country', 'registered_name', 'return_address', 'return_pin', 'return_city', 'return_state', 'return_country'];

    warehouseEditModal?.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        warehouseEditForm.action = warehouseBaseUrl + '/' + (button.dataset.id || '');
        warehouseFields.forEach(field => {
            const input = document.getElementById('edit_' + field);
            if (input) {
                const key = field.replace(/_([a-z])/g, (_, c) => c.toUpperCase());
                input.value = button.dataset[field] ?? button.dataset[key] ?? '';
            }
        });
    });
</script>
</body>
</html>
