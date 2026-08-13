<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - Testimonials Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Centralized Testimonials / Reviews Management">
    <meta name="keywords" content="Testimonials, Reviews, admin, management">
    <meta name="author" content="UWC">
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
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .page-section {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 24px;
            background: #fff;
            overflow: hidden;
        }
        .page-section-header {
            background: #f8fafc;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 1.1rem;
            color: #2563eb;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .page-section-body {
            padding: 16px;
        }
        .testimonial-avatar {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
        }
        .testimonial-avatar-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 1px solid #cbd5e1;
        }
        .star-rating {
            color: #f59e0b;
            font-size: 0.9rem;
        }
        .image-preview {
            max-width: 120px;
            max-height: 120px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-top: 8px;
        }

        .cke_notification_warning{
            display: none !important;
        }
    </style>
</head>

<body>
    <!-- Wrapper -->
     <div class="main-wrapper">
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
                        <h4 class="mb-0">⭐ Testimonials / Reviews Management</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                            <i class="ti ti-plus me-2"></i>Add New Testimonial
                        </button>
                    </div>

                    <!-- Common Testimonials (shown on all pages) -->
                    <div class="page-section">
                        <div class="page-section-header">
                            <span>
                                <i class="ti ti-star me-2"></i>
                                Common Testimonials
                                <span class="badge bg-primary ms-2">{{ $testimonials->count() }}</span>
                            </span>
                        </div>
                        <div class="page-section-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Customer Name</th>
                                            <th>Designation</th>
                                            <th>Content</th>
                                            <th>Rating</th>
                                            <th>Sort</th>
                                            <th>Active</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($testimonials as $testimonial)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if(!empty($testimonial->customer_image))
                                                    <img src="{{ asset($testimonial->customer_image) }}" alt="{{ $testimonial->customer_name }}" class="testimonial-avatar">
                                                @else
                                                    <div class="testimonial-avatar-placeholder">
                                                        <i class="ti ti-user"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $testimonial->customer_name }}</td>
                                            <td>{{ $testimonial->customer_designation }}</td>
                                            <td>{{ Str::limit(strip_tags($testimonial->content), 80) }}</td>
                                            <td>
                                                <span class="star-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $testimonial->rating) ★ @else ☆ @endif
                                                    @endfor
                                                </span>
                                            </td>
                                            <td>{{ $testimonial->sort_order }}</td>
                                            <td>{!! $testimonial->is_active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>' !!}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-testimonial"
                                                        data-id="{{ $testimonial->id }}"
                                                        data-customer_name="{{ $testimonial->customer_name }}"
                                                        data-customer_designation="{{ $testimonial->customer_designation }}"
                                                        data-content="{{ $testimonial->content }}"
                                                        data-rating="{{ $testimonial->rating }}"
                                                        data-sort_order="{{ $testimonial->sort_order }}"
                                                        data-is_active="{{ $testimonial->is_active }}"
                                                        data-customer_image="{{ $testimonial->customer_image ? asset($testimonial->customer_image) : '' }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-testimonial" data-id="{{ $testimonial->id }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">No testimonials found. Click "Add New Testimonial" to get started.</td>
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
    </div>

    <!-- Add Testimonial Modal -->
    <div class="modal fade" id="addTestimonialModal" tabindex="-1" aria-labelledby="addTestimonialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTestimonialModalLabel">Add New Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addTestimonialForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="testimonial_customer_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="testimonial_customer_name" name="customer_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="testimonial_customer_designation" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="testimonial_customer_designation" name="customer_designation">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="testimonial_content" class="form-label">Content</label>
                            <textarea class="form-control" id="testimonial_content" name="content" rows="4" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="testimonial_rating" class="form-label">Rating (1-5)</label>
                                <input type="number" class="form-control" id="testimonial_rating" name="rating" min="1" max="5" value="5">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="testimonial_sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="testimonial_sort_order" name="sort_order" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="testimonial_is_active" class="form-label">Active</label>
                                <select class="form-control" id="testimonial_is_active" name="is_active">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="testimonial_customer_image" class="form-label">Customer Image</label>
                            <input type="file" class="form-control" id="testimonial_customer_image" name="customer_image" accept="image/*">
                            <img id="testimonial_image_preview" class="image-preview" style="display:none;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Testimonial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Testimonial Modal -->
    <div class="modal fade" id="editTestimonialModal" tabindex="-1" aria-labelledby="editTestimonialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTestimonialModalLabel">Edit Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editTestimonialForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_testimonial_id" name="testimonial_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_testimonial_customer_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="edit_testimonial_customer_name" name="customer_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_testimonial_customer_designation" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="edit_testimonial_customer_designation" name="customer_designation">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_testimonial_content" class="form-label">Content</label>
                            <textarea class="form-control" id="edit_testimonial_content" name="content" rows="4" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_testimonial_rating" class="form-label">Rating (1-5)</label>
                                <input type="number" class="form-control" id="edit_testimonial_rating" name="rating" min="1" max="5">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_testimonial_sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="edit_testimonial_sort_order" name="sort_order">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_testimonial_is_active" class="form-label">Active</label>
                                <select class="form-control" id="edit_testimonial_is_active" name="is_active">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_testimonial_customer_image" class="form-label">Customer Image</label>
                            <input type="file" class="form-control" id="edit_testimonial_customer_image" name="customer_image" accept="image/*">
                            <div class="mt-2">
                                <span class="text-muted small">Current image:</span><br>
                                <img id="edit_testimonial_image_preview" class="image-preview" style="display:none;">
                                <span id="edit_testimonial_no_image" class="text-muted small">No image uploaded</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Testimonial</button>
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

    <!-- CKEditor 4.22.1 Full -->
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>

    <script>
        // Initialize CKEditor for Add Testimonial Content
        CKEDITOR.replace('testimonial_content', {
            height: 200,
        });

        // Initialize CKEditor for Edit Testimonial Content
        CKEDITOR.replace('edit_testimonial_content', {
            height: 200,
        });

        CKEDITOR.on('instanceReady', function(ev) {
            ev.editor.on('fileUploadRequest', function(evt) {
                var xhr = evt.data.fileLoader.xhr;
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            });
        });

        $(document).ready(function() {
            // Helper: Update CKEditor instances before any AJAX form submission
            function syncCkEditors() {
                if (typeof CKEDITOR !== 'undefined') {
                    for (var instance in CKEDITOR.instances) {
                        if (CKEDITOR.instances.hasOwnProperty(instance)) {
                            CKEDITOR.instances[instance].updateElement();
                        }
                    }
                }
            }

            // Helper: preview a selected image file in an <img> element
            function previewImage(input, imgEl) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        imgEl.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(input.files[0]);
                } else {
                    imgEl.hide();
                }
            }

            // Image preview for Add modal
            $('#testimonial_customer_image').on('change', function() {
                previewImage(this, $('#testimonial_image_preview'));
            });

            // Image preview for Edit modal
            $('#edit_testimonial_customer_image').on('change', function() {
                previewImage(this, $('#edit_testimonial_image_preview'));
                $('#edit_testimonial_no_image').hide();
            });

            // Testimonial Add Form
            $('#addTestimonialForm').on('submit', function(e) {
                e.preventDefault();
                syncCkEditors();

                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route("admin.store-testimonial") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            $('#addTestimonialModal').modal('hide');
                            $('#addTestimonialForm')[0].reset();
                            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.testimonial_content) {
                                CKEDITOR.instances.testimonial_content.setData('');
                            }
                            $('#testimonial_image_preview').hide();
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

            // Testimonial Edit Form
            $('#editTestimonialForm').on('submit', function(e) {
                e.preventDefault();
                syncCkEditors();
                var testimonialId = $('#edit_testimonial_id').val();

                var formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: '{{ route("admin.update-testimonial", ":id") }}'.replace(':id', testimonialId),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            $('#editTestimonialModal').modal('hide');
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

            // Testimonial Edit Button
            $('.edit-testimonial').on('click', function() {
                var btn = $(this);

                $('#edit_testimonial_id').val(btn.data('id'));
                $('#edit_testimonial_customer_name').val(btn.data('customer_name'));
                $('#edit_testimonial_customer_designation').val(btn.data('customer_designation'));
                $('#edit_testimonial_content').val(btn.data('content'));
                $('#edit_testimonial_rating').val(btn.data('rating'));
                $('#edit_testimonial_sort_order').val(btn.data('sort_order'));
                $('#edit_testimonial_is_active').val(btn.data('is_active') ? 1 : 0);

                // Set CKEditor content with the content data
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.edit_testimonial_content) {
                    CKEDITOR.instances.edit_testimonial_content.setData(btn.data('content'));
                }

                // Show current image preview
                var currentImage = btn.data('customer_image');
                if (currentImage) {
                    $('#edit_testimonial_image_preview').attr('src', currentImage).show();
                    $('#edit_testimonial_no_image').hide();
                } else {
                    $('#edit_testimonial_image_preview').hide();
                    $('#edit_testimonial_no_image').show();
                }
                // Reset the file input so a previously selected file isn't kept
                $('#edit_testimonial_customer_image').val('');

                var modal = new bootstrap.Modal(document.getElementById('editTestimonialModal'));
                modal.show();
            });

            // Testimonial Delete Button
            $('.delete-testimonial').on('click', function() {
                var btn = $(this);
                var testimonialId = btn.data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this! The customer image will also be deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("admin.delete-testimonial", ":id") }}'.replace(':id', testimonialId),
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

        // When the Add modal is hidden, reset the form and clear CKEditor
        $('#addTestimonialModal').on('hidden.bs.modal', function () {
            $('#addTestimonialForm')[0].reset();
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.testimonial_content) {
                CKEDITOR.instances.testimonial_content.setData('');
            }
            $('#testimonial_image_preview').hide();
        });

        // When the Edit modal is hidden, clear CKEditor
        $('#editTestimonialModal').on('hidden.bs.modal', function () {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.edit_testimonial_content) {
                CKEDITOR.instances.edit_testimonial_content.setData('');
            }
            $('#edit_testimonial_image_preview').hide();
            $('#edit_testimonial_no_image').show();
        });
    </script>
</body>

</html>
