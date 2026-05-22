<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Change Document Download Page | CRMS</title>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">
    <style>
    .table-actions {
        display: flex;
        gap: 5px;
    }
    .badge-file-pdf { background: #fef2f2; color: #ef4444; }
    .badge-file-xls { background: #f0fdf4; color: #22c55e; }
    .badge-file-zip { background: #f0f7ff; color: #3b82f6; }
    .badge-file-doc { background: #eef2ff; color: #6366f1; }
    .action-btn {
        padding: 5px 10px;
        font-size: 12px;
    }
    </style>
</head>

<body>
    <div class="main-wrapper">
        @include('admin.partials.header')

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

        <div class="page-wrapper">
            <div class="content pb-0">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                    <div>
                        <h4 class="mb-1">Change Document Download Page</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <!-- <a href="{{ route('admin.edit-all-document-download') }}" class="btn btn-success"><i class="ti ti-pencil me-1"></i> Edit All</a> -->
                        <a href="{{ route('admin.create-document-download') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Add New</a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>

                <!-- Page Hero Content Section -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Page Hero Content</h5>
                            <p class="card-text">Edit the hero section content (badge, title, description) for the Document Download page</p>
                        </div>
                        <div class="card-body">
                            <form id="pageMetaForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="badge" class="form-label">Badge Text</label>
                                        <input type="text" class="form-control" id="badge" name="badge"
                                            value="{{ $pageMeta && $pageMeta->content ? ($pageMeta->content['badge'] ?? '') : '' }}"
                                            placeholder="e.g. Explore All Documents">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="title" class="form-label">Hero Title <small class="text-muted">(HTML allowed)</small></label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ $pageMeta && $pageMeta->content ? ($pageMeta->content['title'] ?? '') : '' }}"
                                            placeholder="e.g. Documents <span>Download</span>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="2"
                                            placeholder="e.g. Must-read guides, handpicked for their popularity among global exporters">{{ $pageMeta && $pageMeta->content ? ($pageMeta->content['description'] ?? '') : '' }}</textarea>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary" id="savePageMetaBtn">
                                        <i class="ti ti-device-floppy me-1"></i> Save Page Content
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Document Download Content Table -->
                <div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Document Download Page Management</h5>
                                <p class="card-text">View and Edit all document download content</p>
                            </div>
                            <div class="card-body">
                                @if ($message = Session::get('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="ti ti-circle-check me-2"></i>
                                    {{ $message }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif

                                @if ($message = Session::get('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="ti ti-alert-circle me-2"></i>
                                    {{ $message }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-hover" id="documentDownloadTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>File Type</th>
                                                <th>File Size</th>
                                                <th>Category</th>
                                                <th>Status Badge</th>
                                                <th>Sort Order</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($documents as $doc)
                                            <tr>
                                                <td>{{ $doc->id }}</td>
                                                <td>
                                                    <strong>{{ $doc->title ?: '-' }}</strong>
                                                </td>
                                                <td>
                                                    @if($doc->file_type)
                                                    <span class="badge badge-file-{{ $doc->file_type }}">{{ strtoupper($doc->file_type) }}</span>
                                                    @else
                                                    <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $doc->file_size ?: '-' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $doc->category ?: '-' }}</span>
                                                </td>
                                                <td>
                                                    @if($doc->status_badge == 'Verified')
                                                    <span class="badge bg-success">Verified</span>
                                                    @else
                                                    <span class="badge bg-warning text-dark">{{ $doc->status_badge ?: '-' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $doc->sort_order }}</span>
                                                </td>
                                                <td>
                                                    @if($doc->status == 'Active')
                                                    <span class="badge bg-success">Active</span>
                                                    @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $doc->created_at ? $doc->created_at->format('d/m/Y') : '-' }}</small>
                                                </td>
                                                <td>
                                                    <div class="table-actions">
                                                        <a href="{{ url('/admin/edit-document-download/' . $doc->id) }}" class="btn btn-sm btn-primary action-btn">
                                                            <i class="ti ti-edit"></i> Edit
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger action-btn"
                                                            onclick="deleteDocument({{ $doc->id }})">
                                                            <i class="ti ti-trash"></i> Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4">
                                                    <p class="text-muted">No documents found.</p>
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
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
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
    let dataTable = null;
    $(document).ready(function() {
        $('#documentDownloadTable').DataTable();
    });

    function deleteDocument(id) {
        if (confirm('Are you sure you want to delete this document?')) {
            fetch(`/admin/delete-document-download/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the document.');
                });
        }
    }

    // Page Meta Form Submission
    $(document).ready(function() {
        $('#pageMetaForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#savePageMetaBtn');
            btn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Saving...');

            $.ajax({
                url: '{{ route("admin.update-document-download-page-meta") }}',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'An error occurred'));
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Save Page Content');
                }
            });
        });
    });
    </script>

</body>
</html>