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
                        <h4 class="mb-1">Change Service Page</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                
                <!-- End Page Header -->

                <!-- Service Page Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Service Page Content Management</h5>
                                <p class="card-text">View and Edit all Service page content sections</p>
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
                                    <table class="table table-hover" id="serviceContentTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Section</th>
                                                <th>Item Key</th>
                                                <th>Content Preview</th>
                                                <th>Sort Order</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($serviceContent as $content)
                                                @php
                                                    $contentData = is_array($content->content) ? $content->content : json_decode($content->content, true);
                                                @endphp
                                                <tr>
                                                    <td>{{ $content->id }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $content->section }}</span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $content->item_key }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="content-preview">
                                                            @if($content->section === 'services')
                                                                <strong>{{ $contentData['title'] ?? '' }}</strong><br>
                                                                <small>{{ \Illuminate\Support\Str::limit($contentData['description'] ?? '', 50) }}</small>
                                                            @elseif($content->section === 'faq')
                                                                <strong>{{ $contentData['question'] ?? '' }}</strong><br>
                                                                <small>{{ \Illuminate\Support\Str::limit($contentData['answer'] ?? '', 50) }}</small>
                                                            @elseif($content->section === 'stats')
                                                                <strong>{{ $contentData['value'] ?? '' }}</strong> {{ $contentData['label'] ?? '' }}<br>
                                                                <small>Statistics data</small>
                                                            @elseif($content->section === 'partners')
                                                                <strong>{{ $contentData['name'] ?? '' }}</strong><br>
                                                                <small>Partner logo</small>
                                                            @else
                                                                <small class="text-muted">{{ \Illuminate\Support\Str::limit(json_encode($contentData), 50) }}</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $content->sort_order }}</td>
                                                    <td>
                                                        @if($content->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="table-actions">
                                                            <button type="button" class="btn btn-sm btn-primary action-btn edit-content-btn" data-bs-toggle="modal" data-bs-target="#editModal"
                                                                data-id="{{ $content->id }}"
                                                                data-section="{{ $content->section }}"
                                                                data-item_key="{{ $content->item_key }}"
                                                                data-content='{{ json_encode($contentData, JSON_HEX_APOS | JSON_HEX_QUOT) }}'
                                                                data-sort_order="{{ $content->sort_order }}"
                                                                data-is_active="{{ $content->is_active ? 1 : 0 }}">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </button>
                                                            <!-- <button type="button" class="btn btn-sm btn-danger action-btn" onclick="deleteContent({{ $content->id }})">
                                                                <i class="ti ti-trash"></i> Delete
                                                            </button> -->
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <p class="text-muted">No service content found. The service page is empty.</p>
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
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Service Content</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="contentId" name="id">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="section" class="form-label">Section</label>
                                    <select class="form-control" id="section" name="section" required>
                                        <option value="services">Services</option>
                                        <option value="faq">FAQ</option>
                                        <option value="stats">Stats</option>
                                        <option value="partners">Partners</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="itemKey" class="form-label">Item Key</label>
                                    <input type="text" class="form-control" id="itemKey" name="item_key" placeholder="e.g., service_1, testimonial_1" required>
                                </div>

                                <div class="mb-3">
                                    <label for="sortOrder" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sortOrder" name="sort_order" placeholder="1" required>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1">
                                        <label class="form-check-label" for="isActive">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Dynamic content fields based on section -->
                                <div id="servicesFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="serviceTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="serviceTitle" name="content[title]" placeholder="Service title">
                                    </div>
                                    <div class="mb-3">
                                        <label for="serviceDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="serviceDescription" name="content[description]" rows="3" placeholder="Service description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="serviceIcon" class="form-label">Icon SVG</label>
                                        <textarea class="form-control" id="serviceIcon" name="content[icon_svg]" rows="3" placeholder="SVG code"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="serviceColor" class="form-label">Color Class</label>
                                        <input type="text" class="form-control" id="serviceColor" name="content[color_class]" placeholder="sc-air">
                                    </div>
                                    <div class="mb-3">
                                        <label for="serviceLink" class="form-label">Link</label>
                                        <input type="text" class="form-control" id="serviceLink" name="content[link]" placeholder="# or URL">
                                    </div>
                                </div>


                                <div id="faqFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="faqQuestion" class="form-label">Question</label>
                                        <input type="text" class="form-control" id="faqQuestion" name="content[question]" placeholder="FAQ question">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqAnswer" class="form-label">Answer</label>
                                        <textarea class="form-control" id="faqAnswer" name="content[answer]" rows="4" placeholder="FAQ answer"></textarea>
                                    </div>
                                </div>

                                <div id="statsFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="statValue" class="form-label">Value</label>
                                        <input type="text" class="form-control" id="statValue" name="content[value]" placeholder="30+">
                                    </div>
                                    <div class="mb-3">
                                        <label for="statLabel" class="form-label">Label</label>
                                        <input type="text" class="form-control" id="statLabel" name="content[label]" placeholder="Years of Excellence">
                                    </div>
                                </div>

                                <div id="partnersFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="partnerName" class="form-label">Partner Name</label>
                                        <input type="text" class="form-control" id="partnerName" name="content[name]" placeholder="FedEx">
                                    </div>
                                    <div class="mb-3">
                                        <label for="partnerLogo" class="form-label">Logo URL</label>
                                        <input type="text" class="form-control" id="partnerLogo" name="content[logo_url]" placeholder="https://example.com/logo.png">
                                    </div>
                                    <div class="mb-3">
                                        <label for="partnerAlt" class="form-label">Alt Text</label>
                                        <input type="text" class="form-control" id="partnerAlt" name="content[alt]" placeholder="FedEx">
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
        $('#serviceContentTable').DataTable();
    } );

    // Click handler for edit buttons - reads from data-* attributes to populate the modal
    $(document).on('click', '.edit-content-btn', function() {
        const id = $(this).data('id');
        const section = $(this).data('section');
        const itemKey = $(this).data('item_key');
        const contentJson = $(this).data('content');
        const sortOrder = $(this).data('sort_order');
        const isActive = $(this).data('is_active');
        
        const content = typeof contentJson === 'string' ? JSON.parse(contentJson) : contentJson;
        
        document.getElementById('contentId').value = id;
        document.getElementById('section').value = section;
        document.getElementById('itemKey').value = itemKey;
        document.getElementById('sortOrder').value = sortOrder;
        document.getElementById('isActive').checked = isActive == 1;
        
        // Hide all field groups
        document.getElementById('servicesFields').style.display = 'none';
        document.getElementById('faqFields').style.display = 'none';
        document.getElementById('statsFields').style.display = 'none';
        document.getElementById('partnersFields').style.display = 'none';
        
        // Show relevant fields and populate data
        switch(section) {
            case 'services':
                document.getElementById('servicesFields').style.display = 'block';
                document.getElementById('serviceTitle').value = content.title || '';
                document.getElementById('serviceDescription').value = content.description || '';
                document.getElementById('serviceIcon').value = content.icon_svg || '';
                document.getElementById('serviceColor').value = content.color_class || '';
                document.getElementById('serviceLink').value = content.link || '';
                break;
                
            case 'faq':
                document.getElementById('faqFields').style.display = 'block';
                document.getElementById('faqQuestion').value = content.question || '';
                document.getElementById('faqAnswer').value = content.answer || '';
                break;
                
            case 'stats':
                document.getElementById('statsFields').style.display = 'block';
                document.getElementById('statValue').value = content.value || '';
                document.getElementById('statLabel').value = content.label || '';
                break;
                
            case 'partners':
                document.getElementById('partnersFields').style.display = 'block';
                document.getElementById('partnerName').value = content.name || '';
                document.getElementById('partnerLogo').value = content.logo_url || '';
                document.getElementById('partnerAlt').value = content.alt || '';
                break;
        }
    });

    document.getElementById('section').addEventListener('change', function() {
        const section = this.value;
        
        // Hide all field groups
        document.getElementById('servicesFields').style.display = 'none';
        document.getElementById('faqFields').style.display = 'none';
        document.getElementById('statsFields').style.display = 'none';
        document.getElementById('partnersFields').style.display = 'none';
        
        // Show relevant fields
        switch(section) {
            case 'services':
                document.getElementById('servicesFields').style.display = 'block';
                break;
            case 'faq':
                document.getElementById('faqFields').style.display = 'block';
                break;
            case 'stats':
                document.getElementById('statsFields').style.display = 'block';
                break;
            case 'partners':
                document.getElementById('partnersFields').style.display = 'block';
                break;
        }
    });

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('contentId').value;
        const formData = new FormData(this);
        
        fetch(`${BASE_URL}/admin/update-service-content/${id}`, {
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
                console.error('Server Error:', data);
                showAlert('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Network Error:', error);
            showAlert('Network error occurred. Please check your connection and try again.', 'error');
        });

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();
    });

    function deleteContent(id) {
        if (confirm('Are you sure you want to delete this content?')) {
            fetch(`${BASE_URL}/admin/delete-service-content/${id}`, {
                method: 'DELETE',
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
                showAlert('An error occurred while deleting content.', 'error');
            });
        }
    }
    </script>

</body>

</html>