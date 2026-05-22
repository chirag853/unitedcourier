<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | United Courier</title>
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
    <!-- <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/css/dataTables.bootstrap5.min.css') }}"> -->

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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css" />
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
                        <h4 class="mb-1">Change About Us</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                
                <!-- End Page Header -->

                <!-- About Us Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">About Us Page Content Management</h5>
                                <p class="card-text">View and Edit all About Us page content sections</p>
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
                                    <table class="table table-hover" id="aboutContentTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Section Type</th>
                                                <th>Title</th>
                                                <th>Subtitle</th>
                                                <th>Description</th>
                                                <th>Image</th>
                                                <th>Icon SVG</th>
                                                <th>Display Order</th>
                                                <th>Extra Data</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($aboutContent as $content)
                                                <tr>
                                                    <td>{{ $content->id }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $content->section_type }}</span>
                                                    </td>
                                                    <td>
                                                        <small class="text-truncate-3">{{ \Illuminate\Support\Str::limit($content->title, 20) ?? '-' }}</small>
                                                    </td>
                                                    <td>{{ $content->subtitle ?? '-' }}</td>
                                                    <td>
                                                        <small class="text-truncate-3">{{ \Illuminate\Support\Str::limit($content->description, 20) ?? '-' }}</small>
                                                    </td>
                                                    <td>
                                                        @if($content->image)
                                                            <img src="{{ asset($content->image) }}" alt="Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($content->icon_svg)
                                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($content->icon_svg, 15) }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $content->display_order ?? '-' }}</td>
                                                    <td>
                                                        @if($content->extra_data)
                                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit(json_encode($content->extra_data), 30) }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($content->status)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="table-actions">
                                                            <button type="button" class="btn btn-sm btn-primary action-btn edit-content-btn"
                                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                                data-id="{{ $content->id }}"
                                                                data-section-type="{{ $content->section_type }}"
                                                                data-title="{{ $content->title }}"
                                                                data-subtitle="{{ $content->subtitle }}"
                                                                data-description="{{ $content->description }}"
                                                                data-image="{{ $content->image }}"
                                                                data-icon-svg="{{ $content->icon_svg }}"
                                                                data-status="{{ $content->status ? 1 : 0 }}"
                                                                data-page-badge-text="{{ $content->page_badge_text }}"
                                                                data-page-target-number="{{ $content->page_target_number }}"
                                                                data-page-suffix="{{ $content->page_suffix }}"
                                                                data-page-button-text="{{ $content->page_button_text }}"
                                                                data-page-tag="{{ $content->page_tag }}"
                                                                data-page-color-scheme="{{ $content->page_color_scheme }}"
                                                                data-page-year="{{ $content->page_year }}"
                                                                data-page-card-color-class="{{ $content->page_card_color_class }}"
                                                                data-page-rating="{{ $content->page_rating }}"
                                                                data-page-countries="{{ $content->page_countries }}"
                                                                data-page-pin-codes="{{ $content->page_pin_codes }}">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center py-4">
                                                        <p class="text-muted">No content found. The About Us page is empty.</p>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Content</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="contentId" name="id">
                        
                        <div class="mb-3">
                            <label for="sectionType" class="form-label">Section Type</label>
                            <input type="text" class="form-control" id="sectionType" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" name="title" placeholder="Enter title">
                        </div>

                        <div class="mb-3">
                            <label for="editSubtitle" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="editSubtitle" name="subtitle" placeholder="Enter subtitle">
                        </div>

                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="5" placeholder="Enter description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="editImage" class="form-label">Image Upload</label>
                            <div class="d-flex gap-2">
                                <input type="file" class="form-control" id="editImageFile" name="image_file" accept="image/*" onchange="previewImage(this)">
                                <input type="text" class="form-control" id="editImage" name="image" placeholder="public/website_images/example.jpg" readonly>
                            </div>
                            <div id="imagePreview" class="mt-2"></div>
                            <small class="text-muted">Upload an image or leave current path unchanged</small>
                        </div>

                        <div class="mb-3">
                            <label for="editIconSvg" class="form-label">Icon SVG</label>
                            <textarea class="form-control" id="editIconSvg" name="icon_svg" rows="3" placeholder="Enter SVG code or path"></textarea>
                        </div>

                        <!-- ========================================== -->
                        <!-- Extra Data Fields (from normalized columns) -->
                        <!-- ========================================== -->
                        <hr>
                        <h6 class="fw-bold mb-3">Extra Data Fields</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPageBadgeText" class="form-label">Badge Text</label>
                                <input type="text" class="form-control" id="editPageBadgeText" name="page_badge_text" placeholder="e.g. About Us">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editPageTargetNumber" class="form-label">Target Number</label>
                                <input type="text" class="form-control" id="editPageTargetNumber" name="page_target_number" placeholder="e.g. 50000">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPageSuffix" class="form-label">Suffix</label>
                                <input type="text" class="form-control" id="editPageSuffix" name="page_suffix" placeholder="e.g. +, %, K">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editPageButtonText" class="form-label">Button Text</label>
                                <input type="text" class="form-control" id="editPageButtonText" name="page_button_text" placeholder="e.g. Learn More">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPageTag" class="form-label">Tag</label>
                                <input type="text" class="form-control" id="editPageTag" name="page_tag" placeholder="e.g. Our Mission">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editPageColorScheme" class="form-label">Color Scheme</label>
                                <input type="text" class="form-control" id="editPageColorScheme" name="page_color_scheme" placeholder="e.g. blue, purple">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="editPageYear" class="form-label">Year</label>
                                <input type="text" class="form-control" id="editPageYear" name="page_year" placeholder="e.g. 2024">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editPageCardColorClass" class="form-label">Card Color Class</label>
                                <input type="text" class="form-control" id="editPageCardColorClass" name="page_card_color_class" placeholder="e.g. card-blue">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editPageRating" class="form-label">Rating</label>
                                <input type="text" class="form-control" id="editPageRating" name="page_rating" placeholder="e.g. 4.5">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPageCountries" class="form-label">Countries</label>
                                <input type="text" class="form-control" id="editPageCountries" name="page_countries" placeholder="e.g. 220+">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editPagePinCodes" class="form-label">PIN Codes</label>
                                <input type="text" class="form-control" id="editPagePinCodes" name="page_pin_codes" placeholder="e.g. 19000+">
                            </div>
                        </div>
                        <!-- ========================================== -->
                        <!-- End Extra Data Fields -->
                        <!-- ========================================== -->

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="editStatus" name="status" value="1">
                                <label class="form-check-label" for="editStatus">
                                    Active
                                </label>
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
    <!-- <script src="{{ asset('assets/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/js/dataTables.bootstrap5.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>

    <!-- Tabler Icons -->
    <script src="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.js') }}"></script>

    <!-- ChartJS -->
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>

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
    let dataTable = null;
    $(document).ready( function () {
        $('#aboutContentTable').DataTable();
    } );
    // $(document).ready( function () {
    //     $('#aboutContentTable').DataTable({
    //         "paging": false,
    //         "info": false,
    //         "searching": true,
    //         "ordering": true,
    //         "lengthChange": false,
    //         "pageLength": -1,
    //         "dom": 'lrtip',
    //         "columnDefs": [
    //             {
    //                 "targets": 7,
    //                 "orderable": false,
    //                 "searchable": false
    //             }
    //         ]
    //     });
    // } );

    function editContent(button) {
        // Use dataset from the button's data-* attributes
        document.getElementById('contentId').value = button.dataset.id;
        document.getElementById('sectionType').value = button.dataset.sectionType;
        document.getElementById('editTitle').value = button.dataset.title || '';
        document.getElementById('editSubtitle').value = button.dataset.subtitle || '';
        document.getElementById('editDescription').value = button.dataset.description || '';
        document.getElementById('editImage').value = button.dataset.image || '';
        document.getElementById('editIconSvg').value = button.dataset.iconSvg || '';
        const statusCheckbox = document.getElementById('editStatus');
        if (statusCheckbox) {
            statusCheckbox.checked = button.dataset.status == 1;
        }
        
        // Set extra data fields
        document.getElementById('editPageBadgeText').value = button.dataset.pageBadgeText || '';
        document.getElementById('editPageTargetNumber').value = button.dataset.pageTargetNumber || '';
        document.getElementById('editPageSuffix').value = button.dataset.pageSuffix || '';
        document.getElementById('editPageButtonText').value = button.dataset.pageButtonText || '';
        document.getElementById('editPageTag').value = button.dataset.pageTag || '';
        document.getElementById('editPageColorScheme').value = button.dataset.pageColorScheme || '';
        document.getElementById('editPageYear').value = button.dataset.pageYear || '';
        document.getElementById('editPageCardColorClass').value = button.dataset.pageCardColorClass || '';
        document.getElementById('editPageRating').value = button.dataset.pageRating || '';
        document.getElementById('editPageCountries').value = button.dataset.pageCountries || '';
        document.getElementById('editPagePinCodes').value = button.dataset.pagePinCodes || '';
        
        // Clear file input and preview
        document.getElementById('editImageFile').value = '';
        document.getElementById('imagePreview').innerHTML = '';
    }

    // Use event delegation for edit buttons
    $(document).on('click', '.edit-content-btn', function() {
        editContent(this);
    });

    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}" style="max-width: 200px; max-height: 150px; border-radius: 4px; border: 1px solid #ddd;" alt="Preview">
                    <div class="mt-1">
                        <small class="text-success">✓ Image selected: ${input.files[0].name}</small>
                    </div>
                `;
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('contentId').value;
        const formData = new FormData(this);
        
        // Add file if selected
        const fileInput = document.getElementById('editImageFile');
        if (fileInput.files && fileInput.files[0]) {
            formData.append('image_file', fileInput.files[0]);
        }
        
        // Add other data
        formData.append('title', formData.get('title'));
        formData.append('subtitle', formData.get('subtitle'));
        formData.append('description', formData.get('description'));
        formData.append('icon_svg', formData.get('icon_svg'));
        formData.append('status', formData.get('status') ? 1 : 0);

        fetch(`${BASE_URL}/admin/update-about-content/${id}`, {
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

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();
    });

    function deleteContent(id) {
        if (confirm('Are you sure you want to delete this content?')) {
            fetch(`${BASE_URL}/admin/delete-about-content/${id}`, {
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
                alert('An error occurred while deleting the content.');
            });
        }
    }
    </script>

</body>

</html>