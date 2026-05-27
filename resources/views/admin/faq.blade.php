<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC - FAQ Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Centralized FAQ Management">
    <meta name="keywords" content="FAQ, admin, management">
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
                        <h4 class="mb-0">❓ Centralized FAQ Management</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                            <i class="ti ti-plus me-2"></i>Add New FAQ
                        </button>
                    </div>

                    <!-- All FAQs By Page -->
                    @forelse ($faqsByPage as $pageKey => $pageFaqs)
                    <div class="page-section">
                        <div class="page-section-header">
                            <span>
                                <i class="ti ti-file-text me-2"></i>
                                {{ $pageNames[$pageKey] ?? ucfirst(str_replace('-', ' ', $pageKey)) }}
                                <span class="badge bg-primary ms-2">{{ $pageFaqs->count() }}</span>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary add-faq-for-page" 
                                    data-page="{{ $pageKey }}">
                                <i class="ti ti-plus"></i> Add
                            </button>
                        </div>
                        <div class="page-section-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
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
                                        @forelse ($pageFaqs as $faq)
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
                                                        data-answer="{{ e($faq->answer) }}"
                                                        data-page="{{ $faq->page }}"
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
                                            <td colspan="6" class="text-center text-muted">No FAQs for this page.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i> No FAQs found across any pages. Click "Add New FAQ" to get started.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

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
                            <label for="faq_page" class="form-label">Page</label>
                            <select class="form-control" id="faq_page" name="page" required>
                                <option value="">-- Select Page --</option>
                                @foreach ($pageNames as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
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
                            <label for="edit_faq_page" class="form-label">Page</label>
                            <select class="form-control" id="edit_faq_page" name="page" required>
                                <option value="">-- Select Page --</option>
                                @foreach ($pageNames as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
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
        // Initialize CKEditor for Add FAQ Answer
        CKEDITOR.replace('faq_answer', {
            height: 300,
        });

        // Initialize CKEditor for Edit FAQ Answer
        CKEDITOR.replace('edit_faq_answer', {
            height: 300,
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

            // Pre-select page when "Add" button is clicked for a specific page
            $('.add-faq-for-page').on('click', function() {
                var page = $(this).data('page');
                $('#faq_page').val(page);
                var modal = new bootstrap.Modal(document.getElementById('addFaqModal'));
                modal.show();
            });

            // FAQ Add Form
            $('#addFaqForm').on('submit', function(e) {
                e.preventDefault();
                syncCkEditors();
                
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
                syncCkEditors();
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
                $('#edit_faq_page').val(btn.data('page'));
                $('#edit_faq_sort_order').val(btn.data('sort_order'));
                $('#edit_faq_is_active').val(btn.data('is_active'));
                
                // Set CKEditor content with the answer data
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.edit_faq_answer) {
                    CKEDITOR.instances.edit_faq_answer.setData(btn.data('answer'));
                }
                
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

        // When the Add modal is hidden, reset the form and clear CKEditor
        $('#addFaqModal').on('hidden.bs.modal', function () {
            $('#addFaqForm')[0].reset();
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.faq_answer) {
                CKEDITOR.instances.faq_answer.setData('');
            }
        });

        // When the Edit modal is hidden, clear CKEditor
        $('#editFaqModal').on('hidden.bs.modal', function () {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.edit_faq_answer) {
                CKEDITOR.instances.edit_faq_answer.setData('');
            }
        });
    </script>
</body>

</html>