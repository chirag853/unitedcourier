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
        .action-btn {
            padding: 5px 10px;
            font-size: 12px;
        }
        .logo-preview {
            width: 80px;
            height: 40px;
            object-fit: contain;
        }
        .logo-preview-lg {
            width: 100px;
            height: 50px;
            object-fit: contain;
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
                        <h4 class="mb-1">Partner Logos</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPartnerLogoModal"><i class="ti ti-plus me-1"></i> Add Logo</a>
                    </div>
                </div>                
                <!-- End Page Header -->

                <!-- Partner Logos Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Partner Logos Management</h5>
                                <p class="card-text">Manage partner logos displayed in the footer and partnership page across all website pages</p>
                            </div>
                            <div class="card-body">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="ti ti-circle-check me-2"></i>
                                        {{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                               
                                <div class="table-responsive">
                                    <table class="table table-hover" id="partnerLogosTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Logo</th>
                                                <th>Alt Text</th>
                                                <th>Display Order</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($partnerLogos as $logo)
                                                <tr>
                                                    <td>{{ $logo->id }}</td>
                                                    <td>
                                                        <img src="{{ $logo->logo_image }}" alt="{{ $logo->alt_text ?? 'Partner Logo' }}" class="logo-preview">
                                                    </td>
                                                    <td>
                                                        <strong>{{ $logo->alt_text ?? 'N/A' }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $logo->display_order }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $logo->status ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $logo->status ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="table-actions">
                                                            <button type="button" class="btn btn-sm btn-primary action-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editPartnerLogoModal" 
                                                                onclick="editLogo({{ $logo->id }}, '{{ addslashes($logo->logo_image) }}', '{{ addslashes($logo->alt_text ?? '') }}', {{ $logo->display_order }}, {{ $logo->status ? 'true' : 'false' }})">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger action-btn" onclick="deleteLogo({{ $logo->id }})">
                                                                <i class="ti ti-trash"></i> Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <p class="text-muted">No partner logos found. Run the seeder to populate default data.</p>
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
            <!-- End Content -->


        </div>
        <!-- End Page Wrapper -->

    </div>
    <!-- End Wrapper -->

    <!-- Add Partner Logo Modal -->
    <div class="modal fade" id="addPartnerLogoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Partner Logo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addPartnerLogoForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Logo Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="logo_image" accept="image/*" required>
                            <small class="text-muted">Upload logo image (jpeg, png, jpg, gif, svg, webp) - Max 2MB</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alt Text</label>
                            <input type="text" class="form-control" name="alt_text" placeholder="e.g. FedEx">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" class="form-control" name="display_order" value="0" min="0">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Partner Logo Modal -->
    <div class="modal fade" id="editPartnerLogoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Partner Logo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPartnerLogoForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_logo_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Current Logo</label>
                            <div id="edit_logo_preview"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Change Logo Image <span class="text-muted">(optional)</span></label>
                            <input type="file" class="form-control" name="logo_image" accept="image/*">
                            <small class="text-muted">Leave empty to keep current logo</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alt Text</label>
                            <input type="text" class="form-control" id="edit_alt_text" name="alt_text" placeholder="e.g. FedEx">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="edit_display_order" name="display_order" min="0">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_status" name="status" value="1" checked>
                                    <label class="form-check-label" for="edit_status">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this partner logo? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('assets/plugins/slimscroll/slimscroll.min.js') }}" type="text/javascript"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>

    <!-- Daterangepicker JS -->
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}" type="text/javascript"></script>

    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/theme.js') }}" type="text/javascript"></script>

    <script>
        let deleteId = null;

        // Initialize DataTable
        $(document).ready(function() {
            $('#partnerLogosTable').DataTable({
                order: [[3, 'asc']], // Sort by display order
                pageLength: 25,
                language: {
                    search: "Search logos:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                }
            });

            // Handle add form submission via AJAX
            $('#addPartnerLogoForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route("admin.store-partner-logo") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('button[type="submit"]', form).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Submitting...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#addPartnerLogoModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: true,
                                confirmButtonColor: '#3085d6'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message,
                            confirmButtonColor: '#d33'
                        });
                    },
                    complete: function() {
                        $('button[type="submit"]', form).prop('disabled', false).html('Submit');
                    }
                });
            });

            // Handle edit form submission via AJAX
            $('#editPartnerLogoForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var id = $('#edit_logo_id').val();
                var formData = new FormData(this);

                $.ajax({
                    url: '{{ url("/admin/update-partner-logo") }}/' + id,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('button[type="submit"]', form).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#editPartnerLogoModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: true,
                                confirmButtonColor: '#3085d6'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message,
                            confirmButtonColor: '#d33'
                        });
                    },
                    complete: function() {
                        $('button[type="submit"]', form).prop('disabled', false).html('Save Changes');
                    }
                });
            });
        });

        // Edit logo function
        function editLogo(id, logoImage, altText, displayOrder, status) {
            $('#edit_logo_id').val(id);
            $('#edit_logo_preview').html('<img src="' + logoImage + '" class="logo-preview-lg">');
            $('#edit_alt_text').val(altText);
            $('#edit_display_order').val(displayOrder);
            $('#edit_status').prop('checked', status === true || status === 'true' || status === 1);
        }

        // Delete logo function
        function deleteLogo(id) {
            deleteId = id;
            $('#deleteModal').modal('show');
        }

        // Confirm delete
        $('#confirmDeleteBtn').on('click', function() {
            if (deleteId) {
                $.ajax({
                    url: '{{ url("/admin/delete-partner-logo") }}/' + deleteId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $('#confirmDeleteBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Deleting...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#deleteModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message,
                            confirmButtonColor: '#d33'
                        });
                    },
                    complete: function() {
                        $('#confirmDeleteBtn').prop('disabled', false).html('Delete');
                    }
                });
            }
        });
    </script>

</body>
</html>