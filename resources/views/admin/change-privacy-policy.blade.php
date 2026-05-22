<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage privacy policy page content for leadCRM website.">
    <meta name="keywords" content="privacy policy, admin, CRM, content management">
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
        .cke_notifications_area{
            display: none !important;
        }
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
                        <h4 class="mb-1">Change Privacy Policy</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                
                <!-- End Page Header -->

                <!-- Privacy Policy Page Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Privacy Policy Page Content Management</h5>
                                <p class="card-text">View and Edit all Privacy Policy page content sections</p>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                                        <i class="ti ti-plus me-1"></i> Add New
                                    </button>
                                </div>
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
                                    <table class="table table-hover" id="privacyPolicyContentTable">
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
                                            @forelse($privacyContent as $content)
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
                                                            <button type="button" class="btn btn-sm btn-primary action-btn btn-edit-pp" data-bs-toggle="modal" data-bs-target="#editModal"
                                                                data-id="{{ $content->id }}"
                                                                data-section-key="{{ $content->section_key }}"
                                                                data-title="{{ $content->title ?? '' }}"
                                                                data-paragraphs="{{ $content->paragraphs ?? '' }}"
                                                                data-list-items="{{ json_encode($content->list_items ?? []) }}"
                                                                data-sort-order="{{ $content->sort_order }}"
                                                                data-effective-date="{{ $content->effective_date ?? '' }}"
                                                                data-footer-heading="{{ $content->footer_heading ?? '' }}"
                                                                data-footer-email="{{ $content->footer_email ?? '' }}">
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
                                                        <p class="text-muted">No privacy policy content found.</p>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addModalLabel">Add Privacy Policy Content</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addSectionKey" class="form-label">Section Key <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="addSectionKey" name="section_key" placeholder="e.g., introduction, section_1, _page_meta" required>
                                </div>

                                <div class="mb-3">
                                    <label for="addTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="addTitle" name="title" placeholder="Section Title">
                                </div>

                                <div class="mb-3">
                                    <label for="addSortOrder" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="addSortOrder" name="sort_order" placeholder="1" required>
                                </div>
                            </div>

                            <div class="col-md-6" id="addPageMetaFields" style="display:none;">
                                <div class="mb-3">
                                    <label for="addEffectiveDate" class="form-label">Effective Date</label>
                                    <input type="text" class="form-control" id="addEffectiveDate" name="effective_date" placeholder="October 2025">
                                </div>
                                <div class="mb-3">
                                    <label for="addFooterHeading" class="form-label">Footer Heading</label>
                                    <input type="text" class="form-control" id="addFooterHeading" name="footer_heading" placeholder="Questions about our Privacy Policy?">
                                </div>
                                <div class="mb-3">
                                    <label for="addFooterEmail" class="form-label">Footer Email</label>
                                    <input type="email" class="form-control" id="addFooterEmail" name="footer_email" placeholder="contact@unitedcourier.com">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="addParagraphs" class="form-label">Content</label>
                            <textarea class="form-control" id="addParagraphs" name="paragraphs" rows="15" placeholder="Enter content here..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-plus me-1"></i> Add Content
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Privacy Policy Content</h1>
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
                                    <label for="sortOrder" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sortOrder" name="sort_order" placeholder="1" required>
                                </div>
                            </div>

                            <div class="col-md-6" id="pageMetaFields" style="display:none;">
                                <div class="mb-3">
                                    <label for="effectiveDate" class="form-label">Effective Date</label>
                                    <input type="text" class="form-control" id="effectiveDate" name="effective_date" placeholder="October 2025">
                                </div>
                                <div class="mb-3">
                                    <label for="footerHeading" class="form-label">Footer Heading</label>
                                    <input type="text" class="form-control" id="footerHeading" name="footer_heading" placeholder="Questions about our Privacy Policy?">
                                </div>
                                <div class="mb-3">
                                    <label for="footerEmail" class="form-label">Footer Email</label>
                                    <input type="email" class="form-control" id="footerEmail" name="footer_email" placeholder="contact@unitedcourier.com">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="paragraphs" class="form-label">Content</label>
                            <textarea class="form-control" id="paragraphs" name="paragraphs" rows="15" placeholder="Enter content here..."></textarea>
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

    <!-- CKEditor 4.22.1 Full -->
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>

    <script>
    $(document).ready( function () {
        $('#privacyPolicyContentTable').DataTable();
    });

    // Initialize CKEditor for Edit Modal
    CKEDITOR.replace('paragraphs', {
        height: 400,
        filebrowserImageUploadUrl: '{{ route("admin.upload-blog-image") }}',
    });

    // Initialize CKEditor for Add Modal
    CKEDITOR.replace('addParagraphs', {
        height: 400,
        filebrowserImageUploadUrl: '{{ route("admin.upload-blog-image") }}',
    });

    CKEDITOR.on('instanceReady', function(ev) {
        ev.editor.on('fileUploadRequest', function(evt) {
            var xhr = evt.data.fileLoader.xhr;
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        });
    });

    // Show/hide page meta fields on Add modal when section key input changes
    document.getElementById('addSectionKey').addEventListener('input', function() {
        const metaFields = document.getElementById('addPageMetaFields');
        if (this.value.trim() === '_page_meta') {
            metaFields.style.display = 'block';
        } else {
            metaFields.style.display = 'none';
        }
    });

    // Helper: convert plain text paragraphs + list items to HTML
    function mergeToHtml(paragraphsText, listItems) {
        let html = '';
        if (paragraphsText) {
            // Check if already HTML
            if (paragraphsText.includes('<')) {
                html = paragraphsText;
            } else {
                const lines = paragraphsText.split('\n').filter(l => l.trim());
                html = lines.map(line => '<p>' + line.replace(/</g, '<').replace(/>/g, '>') + '</p>').join('');
            }
        }
        if (listItems && listItems.length > 0) {
            html += '<ul><li>' + listItems.join('</li><li>') + '</li></ul>';
        }
        return html;
    }

    // Use event delegation for all edit buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-pp');
        if (!btn) return;

        document.getElementById('contentId').value = btn.dataset.id;
        document.getElementById('sectionKey').value = btn.dataset.sectionKey;
        document.getElementById('sectionKeyDisplay').value = btn.dataset.sectionKey;
        document.getElementById('title').value = btn.dataset.title;
        document.getElementById('sortOrder').value = btn.dataset.sortOrder;

        if (btn.dataset.sectionKey === '_page_meta') {
            document.getElementById('pageMetaFields').style.display = 'block';
            if (CKEDITOR.instances.paragraphs) {
                CKEDITOR.instances.paragraphs.setData('');
            }
            document.getElementById('effectiveDate').value = btn.dataset.effectiveDate;
            document.getElementById('footerHeading').value = btn.dataset.footerHeading;
            document.getElementById('footerEmail').value = btn.dataset.footerEmail;
        } else {
            document.getElementById('pageMetaFields').style.display = 'none';
            
            let paragraphsText = btn.dataset.paragraphs || '';
            let listItems = [];
            try {
                listItems = JSON.parse(btn.dataset.listItems);
            } catch (e) {
                listItems = [];
            }
            
            const html = mergeToHtml(paragraphsText, listItems);
            if (CKEDITOR.instances.paragraphs) {
                CKEDITOR.instances.paragraphs.setData(html);
            }
        }
    });

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (CKEDITOR.instances.paragraphs) {
            CKEDITOR.instances.paragraphs.updateElement();
        }
        
        const id = document.getElementById('contentId').value;
        const formData = new FormData(this);

        fetch(`${BASE_URL}/admin/update-privacy-policy-content/${id}`, {
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

    // Add Form Submit Handler
    document.getElementById('addForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (CKEDITOR.instances.addParagraphs) {
            CKEDITOR.instances.addParagraphs.updateElement();
        }
        
        const formData = new FormData(this);

        fetch(`${BASE_URL}/admin/store-privacy-policy-content`, {
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

        const modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
        if (modal) {
            modal.hide();
        }
    });

    function deleteContent(id) {
        if (confirm('Are you sure you want to delete this content?')) {
            fetch(`${BASE_URL}/admin/delete-privacy-policy-content/${id}`, {
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
