<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
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
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfficeModal">
                            <i class="ti ti-plus me-2"></i>Add New Office
                        </button>
                    </div>
                </div>
                <!-- End Page Header -->

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

                    <!-- FAQs Section -->
                    <div class="mb-5">
                        <h5 class="section-title">❓ Frequently Asked Questions</h5>
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                            <i class="ti ti-plus me-2"></i>Add New FAQ
                        </button>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Question</th>
                                        <th>Answer</th>
                                        <th>Sort Order</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($faqs as $faq)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ Str::limit($faq->question, 60) }}</td>
                                        <td>{{ Str::limit(strip_tags($faq->answer), 80) }}</td>
                                        <td>{{ $faq->sort_order }}</td>
                                        <td>{!! $faq->is_active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>' !!}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-faq"
                                                    data-id="{{ $faq->id }}"
                                                    data-question="{{ $faq->question }}"
                                                    data-answer="{{ $faq->answer }}"
                                                    data-sort_order="{{ $faq->sort_order }}"
                                                    data-is_active="{{ $faq->is_active }}">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-faq" data-id="{{ $faq->id }}">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No FAQs found. Click "Add New FAQ" to add one.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Content -->
            </div>
            <!-- End Page Wrapper -->
        </div>
        <!-- End Wrapper -->

    <!-- Add FAQ Modal -->
    <div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFaqModalLabel">Add New FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addFaqForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="faq_question" class="form-label">Question</label>
                            <input type="text" class="form-control" id="faq_question" name="question" required>
                        </div>
                        <div class="mb-3">
                            <label for="faq_answer" class="form-label">Answer</label>
                            <textarea class="form-control" id="faq_answer" name="answer" rows="4" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="faq_sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="faq_sort_order" name="sort_order" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="faq_is_active" class="form-label">Active</label>
                                <select class="form-control" id="faq_is_active" name="is_active">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit FAQ Modal -->
    <div class="modal fade" id="editFaqModal" tabindex="-1" aria-labelledby="editFaqModalLabel" aria-hidden="true">
        <div class="dialog modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFaqModalLabel">Edit FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editFaqForm">
                    @csrf
                    <input type="hidden" id="edit_faq_id" name="faq_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_faq_question" class="form-label">Question</label>
                            <input type="text" class="form-control" id="edit_faq_question" name="question" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_faq_answer" class="form-label">Answer</label>
                            <textarea class="form-control" id="edit_faq_answer" name="answer" rows="4" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_faq_sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="edit_faq_sort_order" name="sort_order">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_faq_is_active" class="form-label">Active</label>
                                <select class="form-control" id="edit_faq_is_active" name="is_active">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update FAQ</button>
                    </div>
                </form>
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

            // FAQ Add Form
            $('#addFaqForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '{{ route("admin.store-faq") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            $('#addFaqModal').modal('hide');
                            $('#addFaqForm')[0].reset();
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

            // FAQ Edit Form
            $('#editFaqForm').on('submit', function(e) {
                e.preventDefault();
                var faqId = $('#edit_faq_id').val();
                
                $.ajax({
                    url: '{{ route("admin.update-faq", ":id") }}'.replace(':id', faqId),
                    method: 'POST',
                    data: $(this).serialize() + '&_method=PUT',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            $('#editFaqModal').modal('hide');
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

            // FAQ Edit Button
            $('.edit-faq').on('click', function() {
                var btn = $(this);
                
                $('#edit_faq_id').val(btn.data('id'));
                $('#edit_faq_question').val(btn.data('question'));
                $('#edit_faq_answer').val(btn.data('answer'));
                $('#edit_faq_sort_order').val(btn.data('sort_order'));
                $('#edit_faq_is_active').val(btn.data('is_active'));
                
                var modal = new bootstrap.Modal(document.getElementById('editFaqModal'));
                modal.show();
            });

            // FAQ Delete Button
            $('.delete-faq').on('click', function() {
                var btn = $(this);
                var faqId = btn.data('id');
                
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
                            url: '{{ route("admin.delete-faq", ":id") }}'.replace(':id', faqId),
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
