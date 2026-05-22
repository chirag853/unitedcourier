<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Change Network Page | CRMS - Advanced Bootstrap 5 Admin Template for Customer Management</title>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

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
    <style>
        .table-actions {
            display: flex;
            gap: 5px;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .office-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        .office-header {
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1f2937;
        }
    </style>
</head>

<body>
    <!-- Wrapper -->
    <div class="page-wrapper">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main -->
        <div id="main">
            <!-- Top Header -->
            @include('admin.partials.header')

            <!-- Main Body -->
            <div class="main-content">
                <div class="p-4">
                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Manage Network Offices</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfficeModal">
                            <i class="ti ti-plus me-2"></i>Add New Office
                        </button>
                    </div>

                    <!-- India Offices Section -->
                    <div class="mb-5">
                        <h5 class="section-title">🇮🇳 India Offices</h5>
                        <div class="row">
                            @forelse ($indiaOffices as $office)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="office-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="office-header">{{ $office->name }}</div>
                                        <div class="table-actions">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-office" 
                                                    data-id="{{ $office->id }}" 
                                                    data-name="{{ $office->name }}"
                                                    data-type="{{ $office->type }}"
                                                    data-address="{{ $office->address }}"
                                                    data-telephone="{{ $office->telephone }}"
                                                    data-mobile="{{ $office->mobile }}"
                                                    data-fax="{{ $office->fax }}"
                                                    data-email="{{ $office->email }}"
                                                    data-contact-person="{{ $office->contact_person }}"
                                                    data-sort_order="{{ $office->sort_order }}">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-office" data-id="{{ $office->id }}">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="office-details">
                                        @if($office->address)
                                        <p><strong>Address:</strong> {{ $office->address }}</p>
                                        @endif
                                        @if($office->telephone)
                                        <p><strong>Tel:</strong> {{ $office->telephone }}</p>
                                        @endif
                                        @if($office->mobile)
                                        <p><strong>Mobile:</strong> {{ $office->mobile }}</p>
                                        @endif
                                        @if($office->fax)
                                        <p><strong>Fax:</strong> {{ $office->fax }}</p>
                                        @endif
                                        @if($office->email)
                                        <p><strong>Email:</strong> {{ $office->email }}</p>
                                        @endif
                                        @if($office->contact_person)
                                        <p><strong>Contact:</strong> {{ $office->contact_person }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No India offices found. Click "Add New Office" to add one.
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Overseas Offices Section -->
                    <div class="mb-5">
                        <h5 class="section-title">🌍 Overseas Offices</h5>
                        <div class="row">
                            @forelse ($overseasOffices as $office)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="office-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="office-header">{{ $office->name }}</div>
                                        <div class="table-actions">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-office" 
                                                    data-id="{{ $office->id }}" 
                                                    data-name="{{ $office->name }}"
                                                    data-type="{{ $office->type }}"
                                                    data-address="{{ $office->address }}"
                                                    data-telephone="{{ $office->telephone }}"
                                                    data-mobile="{{ $office->mobile }}"
                                                    data-fax="{{ $office->fax }}"
                                                    data-email="{{ $office->email }}"
                                                    data-contact-person="{{ $office->contact_person }}"
                                                    data-sort_order="{{ $office->sort_order }}">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-office" data-id="{{ $office->id }}">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="office-details">
                                        @if($office->address)
                                        <p><strong>Address:</strong> {{ $office->address }}</p>
                                        @endif
                                        @if($office->telephone)
                                        <p><strong>Tel:</strong> {{ $office->telephone }}</p>
                                        @endif
                                        @if($office->mobile)
                                        <p><strong>Mobile:</strong> {{ $office->mobile }}</p>
                                        @endif
                                        @if($office->fax)
                                        <p><strong>Fax:</strong> {{ $office->fax }}</p>
                                        @endif
                                        @if($office->email)
                                        <p><strong>Email:</strong> {{ $office->email }}</p>
                                        @endif
                                        @if($office->contact_person)
                                        <p><strong>Contact:</strong> {{ $office->contact_person }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No overseas offices found. Click "Add New Office" to add one.
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Office Modal -->
    <div class="modal fade" id="addOfficeModal" tabindex="-1" aria-labelledby="addOfficeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOfficeModalLabel">Add New Office</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addOfficeForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Office Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="india">India</option>
                                    <option value="overseas">Overseas</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Telephone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fax" class="form-label">Fax</label>
                                <input type="text" class="form-control" id="fax" name="fax">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Office</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Office Modal -->
    <div class="modal fade" id="editOfficeModal" tabindex="-1" aria-labelledby="editOfficeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOfficeModalLabel">Edit Office</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editOfficeForm">
                    @csrf
                    <input type="hidden" id="edit_office_id" name="office_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label">Office Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_type" class="form-label">Type</label>
                                <select class="form-control" id="edit_type" name="type" required>
                                    <option value="india">India</option>
                                    <option value="overseas">Overseas</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Address</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_telephone" class="form-label">Telephone</label>
                                <input type="text" class="form-control" id="edit_telephone" name="telephone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="edit_mobile" name="mobile">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_fax" class="form-label">Fax</label>
                                <input type="text" class="form-control" id="edit_fax" name="fax">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="edit_contact_person" name="contact_person">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="edit_sort_order" name="sort_order">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Office</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    
    <!--  -->
    <!-- Daterangepikcer JS -->
	<script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

    <!-- Apexchart JS -->
	<script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}" type="text/javascript"></script>

	<!-- Chart JS -->
	<script src="{{ asset('assets/plugins/peity/jquery.peity.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/plugins/peity/chart-data.js') }}" type="text/javascript"></script>
    
	<!-- Simplebar JS -->
	<script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
	<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            // Add Office Form
            $('#addOfficeForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '{{ route("admin.store-network-office") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            $('#addOfficeModal').modal('hide');
                            $('#addOfficeForm')[0].reset();
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong', 'error');
                    }
                });
            });

            // Edit Office Form
            $('#editOfficeForm').on('submit', function(e) {
                e.preventDefault();
                var officeId = $('#edit_office_id').val();
                
                $.ajax({
                    url: '{{ route("admin.update-network-office", ":id") }}'.replace(':id', officeId),
                    method: 'POST',
                    data: $(this).serialize() + '&_method=PUT',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            $('#editOfficeModal').modal('hide');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong', 'error');
                    }
                });
            });

            // Edit Office Button
            $('.edit-office').on('click', function() {
                console.log('Edit button clicked');
                var btn = $(this);
                console.log('Button data:', btn.data());
                
                $('#edit_office_id').val(btn.data('id'));
                $('#edit_name').val(btn.data('name'));
                $('#edit_type').val(btn.data('type'));
                $('#edit_address').val(btn.data('address'));
                $('#edit_telephone').val(btn.data('telephone'));
                $('#edit_mobile').val(btn.data('mobile'));
                $('#edit_fax').val(btn.data('fax'));
                $('#edit_email').val(btn.data('email'));
                $('#edit_contact_person').val(btn.data('contact_person'));
                $('#edit_sort_order').val(btn.data('sort_order'));
                
                console.log('Modal element:', $('#editOfficeModal'));
                var modal = new bootstrap.Modal(document.getElementById('editOfficeModal'));
                modal.show();
            });

            // Delete Office Button
            $('.delete-office').on('click', function() {
                var btn = $(this);
                var officeId = btn.data('id');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("admin.delete-network-office", ":id") }}'.replace(':id', officeId),
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    setTimeout(() => location.reload(), 1500);
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
