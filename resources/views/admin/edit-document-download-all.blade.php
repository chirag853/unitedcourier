<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
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
    .section-card { margin-bottom: 20px; }
    .section-card .card-header { cursor: pointer; user-select: none; }
    .section-card .card-header:hover { background-color: #f8f9fa; }
    .section-card .card-header .collapse-icon { transition: transform 0.2s; }
    .section-card .card-header.collapsed .collapse-icon { transform: rotate(-90deg); }
    .form-section-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .doc-item { border-left: 3px solid #2563eb; margin-bottom: 12px; padding: 16px; background: #f8fafc; border-radius: 6px; }
    .doc-item-header { font-size: 12px; font-weight: 600; color: #0f172a; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
    .badge-file-pdf { background: #fef2f2; color: #ef4444; }
    .badge-file-xls { background: #f0fdf4; color: #22c55e; }
    .badge-file-zip { background: #f0f7ff; color: #3b82f6; }
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
                        <h4 class="mb-1">Edit All Documents</h4>
                        <p class="text-muted mb-0">
                            Manage all document download entries in one place.
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

                <form id="editAllForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#collapseDocuments" aria-expanded="true">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-file me-2 text-primary"></i>All Documents
                                    <span class="badge bg-primary ms-2">{{ $documents->count() }}</span>
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseDocuments" class="collapse show">
                            <div class="card-body">
                                @forelse($documents as $doc)
                                <div class="doc-item">
                                    <div class="doc-item-header">
                                        <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                                        <span class="badge badge-file-{{ $doc->file_type }}">{{ strtoupper($doc->file_type ?? 'PDF') }}</span>
                                        {{ $doc->title ?: 'Document #' . $doc->id }}
                                    </div>
                                    <input type="hidden" name="documents[{{ $doc->id }}][id]" value="{{ $doc->id }}">

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-section-label">Title</label>
                                            <input type="text" class="form-control" name="documents[{{ $doc->id }}][title]" value="{{ $doc->title ?? '' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-section-label">File Type</label>
                                            <select class="form-control doc-file-type" data-doc-id="{{ $doc->id }}" name="documents[{{ $doc->id }}][file_type]">
                                                <option value="pdf" {{ $doc->file_type == 'pdf' ? 'selected' : '' }}>PDF</option>
                                                <option value="doc" {{ $doc->file_type == 'doc' ? 'selected' : '' }}>DOC</option>
                                                <option value="xls" {{ $doc->file_type == 'xls' ? 'selected' : '' }}>XLS</option>
                                                <option value="zip" {{ $doc->file_type == 'zip' ? 'selected' : '' }}>ZIP</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-section-label">File Size</label>
                                            <input type="text" class="form-control doc-file-size" data-doc-id="{{ $doc->id }}" name="documents[{{ $doc->id }}][file_size]" value="{{ $doc->file_size ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-section-label">Upload File</label>
                                            <input type="file" class="form-control doc-file-input" data-doc-id="{{ $doc->id }}" name="documents[{{ $doc->id }}][document_file]" accept="">
                                            <small class="text-muted doc-file-help" data-doc-id="{{ $doc->id }}">Select file type first to set allowed formats</small>
                                            @if($doc->file_url && $doc->file_url !== '#')
                                                <div class="mt-1">
                                                    <small><a href="{{ $doc->file_url }}" target="_blank" class="text-decoration-none"><i class="ti ti-link me-1"></i> {{ basename($doc->file_url) }}</a></small>
                                                </div>
                                            @endif
                                            <input type="hidden" name="documents[{{ $doc->id }}][file_url]" value="{{ $doc->file_url ?? '#' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-section-label">Category</label>
                                            <select class="form-control" name="documents[{{ $doc->id }}][category]">
                                                <option value="">-- Select --</option>
                                                <option value="invoice" {{ $doc->category == 'invoice' ? 'selected' : '' }}>Invoice</option>
                                                <option value="label" {{ $doc->category == 'label' ? 'selected' : '' }}>Label</option>
                                                <option value="customs" {{ $doc->category == 'customs' ? 'selected' : '' }}>Customs</option>
                                                <option value="packing" {{ $doc->category == 'packing' ? 'selected' : '' }}>Packing</option>
                                                <option value="bol" {{ $doc->category == 'bol' ? 'selected' : '' }}>Bill of Lading</option>
                                                <option value="receipt" {{ $doc->category == 'receipt' ? 'selected' : '' }}>Receipt</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-section-label">Status Badge</label>
                                            <select class="form-control" name="documents[{{ $doc->id }}][status_badge]">
                                                <option value="Verified" {{ $doc->status_badge == 'Verified' ? 'selected' : '' }}>Verified</option>
                                                <option value="Pending Sign" {{ $doc->status_badge == 'Pending Sign' ? 'selected' : '' }}>Pending Sign</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-section-label">Description</label>
                                            <textarea class="form-control" name="documents[{{ $doc->id }}][description]" rows="2">{{ $doc->description ?? '' }}</textarea>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-section-label">Sort Order</label>
                                            <input type="number" class="form-control" name="documents[{{ $doc->id }}][sort_order]" value="{{ $doc->sort_order ?? 0 }}" min="0">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-section-label">Status</label>
                                            <select class="form-control" name="documents[{{ $doc->id }}][status]">
                                                <option value="Active" {{ $doc->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                <option value="Inactive" {{ $doc->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted">No documents found.</p>
                                @endforelse

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i> Save All Changes
                                    </button>
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
    document.getElementById('editAllForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(`${BASE_URL}/admin/update-all-document-download`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success', function () { location.reload(); });
                } else {
                    showAlert('Error: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while saving.', 'error');
            });
    });
    </script>

</body>
</html>