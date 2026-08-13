<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
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

    .image-preview-box {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
        padding: 2px;
        background: #f8f9fa;
    }

    .image-preview-box:hover {
        border-color: #2563eb;
    }

    .action-btn {
        padding: 5px 10px;
        font-size: 12px;
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
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i
                                    class="ti ti-x fs-22"></i></button>
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
                                <h4 class="mb-1">Change World Time Page</h4>
                            </div>
                            <div class="gap-2 d-flex align-items-center flex-wrap">
                                <!-- <a href="{{ route('admin.create-world-time') }}" class="btn btn-primary"><i
                                        class="ti ti-plus me-1"></i> Add New</a> -->
                                <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow"
                                    data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh"
                                    data-bs-original-title="Refresh" onclick="location.reload();"><i
                                        class="ti ti-refresh"></i></a>
                                <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow"
                                    data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse"
                                    data-bs-original-title="Collapse" id="collapse-header"><i
                                        class="ti ti-transition-top"></i></a>
                            </div>
                        </div>
                        <!-- End Page Header -->

                        <!-- World Time Content Table -->
                        <div>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">World Time Page Management</h5>
                                        <p class="card-text">View and Edit all world time page content</p>
                                    </div>
                                    <div class="card-body">
                                        @if ($message = Session::get('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="ti ti-circle-check me-2"></i>
                                            {{ $message }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        @endif

                                        @if ($message = Session::get('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="ti ti-alert-circle me-2"></i>
                                            {{ $message }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        @endif

                                        <div class="table-responsive">
                                            <table class="table table-hover" id="worldTimeTable">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Image</th>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th>Link</th>
                                                        <th>Section</th>
                                                        <th>Sort Order</th>
                                                        <th>Status</th>
                                                        <th>Created</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($worldTimes as $worldTime)
                                                    @php
                                                    $displayTitle = $worldTime->title;
                                                    $displayDescription = $worldTime->description;
                                                    // For page content rows, extract from JSON content
                                                    if (!$displayTitle && $worldTime->content) {
                                                    $displayTitle = $worldTime->content['title'] ??
                                                    $worldTime->content['badge_text'] ?? 'Content Row';
                                                    }
                                                    if (!$displayDescription && $worldTime->content) {
                                                    $displayDescription = $worldTime->content['subtitle'] ??
                                                    $worldTime->content['description'] ?? null;
                                                    }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $worldTime->id }}</td>
                                                        <td>
                                                            @if($worldTime->image)
                                                            <img src="{{ asset($worldTime->image) }}"
                                                                alt="{{ $displayTitle }}" class="image-preview-box">
                                                            @else
                                                            <span class="text-muted">No Image</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>{{ $displayTitle }}</strong>
                                                            @if($worldTime->item_key)
                                                            <br><small
                                                                class="text-muted">{{ $worldTime->item_key }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="text-truncate" style="max-width: 200px;"
                                                                title="{{ $displayDescription ?: strip_tags(json_encode($worldTime->content, JSON_UNESCAPED_SLASHES)) }}">
                                                                {{ $displayDescription ?: ($worldTime->content ? 'JSON data' : '-') }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($worldTime->link)
                                                            <a href="{{ $worldTime->link }}" target="_blank"
                                                                class="text-primary"
                                                                title="{{ $worldTime->link }}">
                                                                <i class="ti ti-link"></i> View
                                                            </a>
                                                            @else
                                                            <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-secondary">{{ $worldTime->section ?: 'General' }}</span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-info">{{ $worldTime->sort_order }}</span>
                                                        </td>
                                                        <td>
                                                            @if($worldTime->status == 'Active')
                                                            <span class="badge bg-success">Active</span>
                                                            @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small
                                                                class="text-muted">{{ $worldTime->created_at ? $worldTime->created_at->format('d/m/Y') : '-' }}</small>
                                                        </td>
                                                        <td>
                                                            <div class="table-actions">
                                                                <a href="{{ url('/admin/edit-world-time/' . $worldTime->id) }}"
                                                                    class="btn btn-sm btn-primary action-btn">
                                                                    <i class="ti ti-edit"></i> Edit
                                                                </a>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger action-btn"
                                                                    onclick="deleteWorldTime({{ $worldTime->id }})">
                                                                    <i class="ti ti-trash"></i> Delete
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center py-4">
                                                            <p class="text-muted">No world time content found.
                                                            </p>
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

            <!-- Bootstrap JS -->
            <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

            <!-- jQuery -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <!-- Datatable JS -->
            <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

            <!-- Simplebar JS -->
            <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>

            <!-- Tabler Icons -->
            <script src="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.js') }}"></script>

            <!-- ChartJS -->
            <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>

            <!-- Custom JS -->
            <script src="{{ asset('assets/js/app.js') }}"></script>

            <!-- Daterangepikcer JS -->
            <script src="{{ asset('js/moment.min.js') }}" type="text/javascript"></script>
            <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript">
            </script>

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
            let dataTable = null;
            $(document).ready(function() {
                $('#worldTimeTable').DataTable();
            });

            function deleteWorldTime(id) {
                if (confirm('Are you sure you want to delete this world time content?')) {
                    fetch(`${BASE_URL}/admin/delete-world-time/${id}`, {
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
                            alert('An error occurred while deleting world time content.');
                        });
                }
            }
            </script>

</body>

</html>