<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Change Terms and Conditions | CRMS - Advanced Bootstrap 5 Admin Template for Customer Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage terms and conditions page content for leadCRM website.">
    <meta name="keywords" content="terms and conditions, admin, CRM, content management">
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

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

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
        .text-truncate-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .content-preview {
            max-width: 300px;
            max-height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .list-items-preview {
            font-size: 0.85rem;
            color: #6c757d;
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
                        <h4 class="mb-1">Change Terms and Conditions</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                
                <!-- End Page Header -->

                <!-- Terms and Conditions Page Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Terms and Conditions Page Content Management</h5>
                                <p class="card-text">View and Edit all Terms and Conditions page content sections</p>
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
                                    <table class="table table-hover" id="termsAndConditionsContentTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Section Key</th>
                                                <th>Title</th>
                                                <th>Content Preview</th>
                                                <th>Sort Order</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($termsContent as $content)
                                                <tr>
                                                    <td>{{ $content->id }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $content->section_key }}</span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $content->title ?? 'N/A' }}</strong>
                                                    </td>
                                                    <td>
                                                        <div class="content-preview">
                                                            @if($content->section_key === '_page_meta')
                                                                <small class="text-muted">
                                                                    <strong>Effective Date:</strong> {{ $content->effective_date ?? 'N/A' }}<br>
                                                                    <strong>Footer Heading:</strong> {{ $content->footer_heading ?? 'N/A' }}<br>
                                                                    <strong>Footer Email:</strong> {{ $content->footer_email ?? 'N/A' }}
                                                                </small>
                                                            @else
                                                                <small>{{ Illuminate\Support\Str::limit($content->paragraphs ?? '', 80) }}</small>
                                                                @if($content->list_items && count($content->list_items) > 0)
                                                                    <div class="list-items-preview">
                                                                        <strong>List Items:</strong>
                                                                        <ul class="mb-0 mt-1">
                                                                            @foreach($content->list_items as $item)
                                                                                <li>{{ Illuminate\Support\Str::limit($item, 50) }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $content->sort_order }}</td>
                                                    <td>
                                                        <div class="table-actions">
                                                            <button type="button" class="btn btn-sm btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editContent({{ $content->id }}, '{{ $content->section_key }}', '{{ $content->title ?? '' }}', '{{ addslashes($content->paragraphs ?? '') }}', '{{ addslashes(json_encode($content->list_items ?? [])) }}', {{ $content->sort_order }}, '{{ $content->effective_date ?? '' }}', '{{ $content->footer_heading ?? '' }}', '{{ $content->footer_email ?? '' }}')">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger action-btn" onclick="deleteContent({{ $content->id }})">
                                                                <i class="ti ti-trash"></i> Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <p class="text-muted">No terms and conditions content found.</p>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Terms and Conditions Content</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    <input type="hidden" id="contentId" name="id">
                    <input type="hidden" id="sectionKey" name="section_key">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sectionKeyDisplay" class="form-label">Section Key</label>
                                    <input type="text" class="form-control" id="sectionKeyDisplay" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Section Title">
                                </div>

                                <div class="mb-3">
                                    <label for="paragraphs" class="form-label">Paragraphs</label>
                                    <textarea class="form-control" id="paragraphs" name="paragraphs" rows="6" placeholder="Enter paragraphs separated by line breaks"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="sortOrder" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sortOrder" name="sort_order" placeholder="1" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div id="regularFields">
                                    <div class="mb-3">
                                        <label for="listItems" class="form-label">List Items (JSON Array)</label>
                                        <textarea class="form-control" id="listItems" name="list_items" rows="8" placeholder='["Item 1", "Item 2", "Item 3"]'></textarea>
                                        <div class="form-text">Enter list items as JSON array. Empty array for no list items.</div>
                                    </div>
                                </div>

                                <div id="pageMetaFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="effectiveDate" class="form-label">Effective Date</label>
                                        <input type="text" class="form-control" id="effectiveDate" name="effective_date" placeholder="October 2025">
                                    </div>
                                    <div class="mb-3">
                                        <label for="footerHeading" class="form-label">Footer Heading</label>
                                        <input type="text" class="form-control" id="footerHeading" name="footer_heading" placeholder="Questions about our Terms?">
                                    </div>
                                    <div class="mb-3">
                                        <label for="footerEmail" class="form-label">Footer Email</label>
                                        <input type="email" class="form-control" id="footerEmail" name="footer_email" placeholder="contact@unitedcourier.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

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
    $(document).ready( function () {
        $('#termsAndConditionsContentTable').DataTable();
    });

    function editContent(id, sectionKey, title, paragraphs, listItemsJson, sortOrder, effectiveDate, footerHeading, footerEmail) {
        document.getElementById('contentId').value = id;
        document.getElementById('sectionKey').value = sectionKey;
        document.getElementById('sectionKeyDisplay').value = sectionKey;
        document.getElementById('title').value = title;
        document.getElementById('paragraphs').value = paragraphs;
        document.getElementById('sortOrder').value = sortOrder;

        // Show/hide fields based on section type
        if (sectionKey === '_page_meta') {
            document.getElementById('regularFields').style.display = 'none';
            document.getElementById('pageMetaFields').style.display = 'block';
            document.getElementById('effectiveDate').value = effectiveDate;
            document.getElementById('footerHeading').value = footerHeading;
            document.getElementById('footerEmail').value = footerEmail;
        } else {
            document.getElementById('regularFields').style.display = 'block';
            document.getElementById('pageMetaFields').style.display = 'none';
            
            // Parse and set list items
            try {
                const listItems = JSON.parse(listItemsJson);
                document.getElementById('listItems').value = JSON.stringify(listItems, null, 2);
            } catch (e) {
                document.getElementById('listItems').value = '[]';
            }
        }
    }

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('contentId').value;
        const formData = new FormData(this);

        fetch(`/admin/update-terms-and-conditions-content/${id}`, {
            method: 'POST',
            body: formData,
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
                console.error('Server Error:', data);
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Network Error:', error);
            alert('Network error occurred. Please check your connection and try again.');
        });

        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        if (modal) {
            modal.hide();
        }
    });

    function deleteContent(id) {
        if (confirm('Are you sure you want to delete this content?')) {
            fetch(`/admin/delete-terms-and-conditions-content/${id}`, {
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
                alert('An error occurred while deleting content.');
            });
        }
    }
    </script>

</body>

</html>
