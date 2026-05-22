<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit Document Download Content | CRMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">

    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <script src="{{ asset('assets/js/theme-script.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
    .form-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #2563eb;
        display: inline-block;
    }
    </style>
</head>

<body>
    <div class="main-wrapper">
        @include('admin.partials.header')
        @include('admin.partials.sidebar')

        <div class="page-wrapper">
            <div class="content pb-0">
                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">
                            @if($document->id)
                                Edit Document Content
                            @else
                                Create New Document
                            @endif
                        </h4>
                        <p class="text-muted mb-0">
                            @if($document->id)
                                Editing: <strong>{{ $document->title ?: 'Document #' . $document->id }}</strong>
                            @else
                                Fill in the details to create a new document download entry
                            @endif
                        </p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ route('admin.change-document-download') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back to Documents
                        </a>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form id="documentForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="documentId" name="id" value="{{ $document->id }}">

                    <div class="row">
                        <!-- Main Content Column -->
                        <div class="col-lg-8">

                            <!-- Basic Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Document Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ old('title', $document->title) }}"
                                            placeholder="Document title" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="file_type" class="form-label">File Type <span class="text-danger">*</span></label>
                                            <select class="form-control" id="file_type" name="file_type" required>
                                                <option value="">-- Select File Type --</option>
                                                <option value="pdf" {{ old('file_type', $document->file_type) == 'pdf' ? 'selected' : '' }}>PDF</option>
                                                <option value="doc" {{ old('file_type', $document->file_type) == 'doc' ? 'selected' : '' }}>DOC</option>
                                                <option value="xls" {{ old('file_type', $document->file_type) == 'xls' ? 'selected' : '' }}>XLS</option>
                                                <option value="zip" {{ old('file_type', $document->file_type) == 'zip' ? 'selected' : '' }}>ZIP</option>
                                            </select>
                                            <small class="text-muted">Select file type to restrict upload format</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="file_size" class="form-label">File Size</label>
                                            <input type="text" class="form-control" id="file_size" name="file_size"
                                                value="{{ old('file_size', $document->file_size) }}"
                                                placeholder="Auto-detected from upload" readonly>
                                            <small class="text-muted">Automatically calculated from uploaded file</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="document_file" class="form-label">Upload File</label>
                                        <input type="file" class="form-control" id="document_file" name="document_file">
                                        <small class="text-muted" id="fileHelpText">Select a file matching your chosen file type</small>
                                        @if($document->file_url && $document->file_url !== '#')
                                            <div class="mt-2">
                                                <a href="{{ $document->file_url }}" target="_blank" class="text-decoration-none">
                                                    <i class="ti ti-link me-1"></i> Current file: {{ basename($document->file_url) }}
                                                </a>
                                            </div>
                                        @endif
                                        <input type="hidden" id="file_url" name="file_url" value="{{ old('file_url', $document->file_url) }}">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="form-control" id="category" name="category">
                                                <option value="">-- Select Category --</option>
                                                <option value="invoice" {{ old('category', $document->category) == 'invoice' ? 'selected' : '' }}>Invoice</option>
                                                <option value="label" {{ old('category', $document->category) == 'label' ? 'selected' : '' }}>Label</option>
                                                <option value="customs" {{ old('category', $document->category) == 'customs' ? 'selected' : '' }}>Customs</option>
                                                <option value="packing" {{ old('category', $document->category) == 'packing' ? 'selected' : '' }}>Packing</option>
                                                <option value="bol" {{ old('category', $document->category) == 'bol' ? 'selected' : '' }}>Bill of Lading</option>
                                                <option value="receipt" {{ old('category', $document->category) == 'receipt' ? 'selected' : '' }}>Receipt</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="status_badge" class="form-label">Status Badge</label>
                                            <select class="form-control" id="status_badge" name="status_badge">
                                                <option value="Verified" {{ old('status_badge', $document->status_badge) == 'Verified' ? 'selected' : '' }}>Verified</option>
                                                <option value="Pending Sign" {{ old('status_badge', $document->status_badge) == 'Pending Sign' ? 'selected' : '' }}>Pending Sign</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"
                                            placeholder="Document description">{{ old('description', $document->description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Sidebar Column -->
                        <div class="col-lg-4">

                            <!-- Save Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <button type="submit" class="btn btn-primary w-100 mb-2">
                                        <i class="ti ti-device-floppy me-1"></i>
                                        {{ $document->id ? 'Update' : 'Create' }}
                                    </button>
                                    <a href="{{ route('admin.change-document-download') }}" class="btn btn-outline-secondary w-100">
                                        <i class="ti ti-x me-1"></i> Cancel
                                    </a>
                                </div>
                            </div>

                            <!-- Status Settings -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Status Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="Active" {{ old('status', $document->status ?: 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ old('status', $document->status ?: 'Active') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order"
                                            value="{{ old('sort_order', $document->sort_order ?? 0) }}"
                                            min="0" placeholder="Display order">
                                        <small class="text-muted">Lower numbers appear first</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/peity/jquery.peity.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/peity/chart-data.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

    <script>
    const documentId = document.getElementById('documentId').value;
    const fileTypeSelect = document.getElementById('file_type');
    const fileInput = document.getElementById('document_file');
    const fileSizeInput = document.getElementById('file_size');
    const fileUrlInput = document.getElementById('file_url');
    const fileHelpText = document.getElementById('fileHelpText');

    // Map file types to allowed extensions
    const allowedExtensions = {
        pdf: ['.pdf'],
        doc: ['.doc', '.docx'],
        xls: ['.xls', '.xlsx', '.csv'],
        zip: ['.zip', '.rar', '.7z', '.tar', '.gz']
    };

    // Map file types to MIME types (for accept attribute)
    const mimeTypes = {
        pdf: '.pdf',
        doc: '.doc,.docx',
        xls: '.xls,.xlsx,.csv',
        zip: '.zip,.rar,.7z,.tar,.gz'
    };

    // When file type changes, update the accept attribute on file input
    fileTypeSelect.addEventListener('change', function() {
        const ft = this.value;
        if (ft && mimeTypes[ft]) {
            fileInput.accept = mimeTypes[ft];
            fileHelpText.textContent = 'Allowed formats: ' + mimeTypes[ft].toUpperCase();
            fileInput.disabled = false;
        } else {
            fileInput.accept = '';
            fileHelpText.textContent = 'Please select a file type first';
            fileInput.disabled = true;
        }
        // Clear any previously selected file if type changes
        fileInput.value = '';
        fileSizeInput.value = '';
    });

    // Trigger change on load to set initial accept
    if (fileTypeSelect.value) {
        fileTypeSelect.dispatchEvent(new Event('change'));
    }

    // Validate file extension when a file is selected
    fileInput.addEventListener('change', function() {
        const ft = fileTypeSelect.value;
        if (this.files && this.files.length > 0) {
            const file = this.files[0];
            const fileName = file.name.toLowerCase();
            const ext = '.' + fileName.split('.').pop();

            if (ft && allowedExtensions[ft] && !allowedExtensions[ft].includes(ext)) {
                alert('Invalid file type! Selected file type is "' + ft.toUpperCase() + '". Allowed extensions: ' + allowedExtensions[ft].join(', '));
                this.value = '';
                fileSizeInput.value = '';
                return;
            }

            // Auto-calculate file size
            const bytes = file.size;
            if (bytes < 1024) {
                fileSizeInput.value = bytes + ' B';
            } else if (bytes < 1048576) {
                fileSizeInput.value = (bytes / 1024).toFixed(1) + ' KB';
            } else if (bytes < 1073741824) {
                fileSizeInput.value = (bytes / 1048576).toFixed(1) + ' MB';
            } else {
                fileSizeInput.value = (bytes / 1073741824).toFixed(2) + ' GB';
            }
        }
    });

    document.getElementById('documentForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const ft = fileTypeSelect.value;
        if (!ft) {
            alert('Please select a file type.');
            return;
        }

        // If file is uploaded, validate it exists
        if (fileInput.files && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const fileName = file.name.toLowerCase();
            const ext = '.' + fileName.split('.').pop();
            if (!allowedExtensions[ft].includes(ext)) {
                alert('File extension "' + ext + '" does not match selected file type "' + ft.toUpperCase() + '". Allowed: ' + allowedExtensions[ft].join(', '));
                return;
            }
        }

        const formData = new FormData(this);

        let url = documentId ? `/admin/update-document-download/${documentId}` : '/admin/store-document-download';

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (data.success) {
                    alert(data.message);
                    if (data.document_id) {
                        window.location.href = `/admin/edit-document-download/${data.document_id}`;
                    } else {
                        window.location.href = '{{ route("admin.change-document-download") }}';
                    }
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                console.error('Error:', error);
                alert('An error occurred while saving.');
            });
    });
    </script>

</body>
</html>