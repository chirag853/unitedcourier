<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   

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
                        <h4 class="mb-1">Change About Us</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
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

                <!-- About Us Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Home Page Content Management</h5>
                                <p class="card-text">View and Edit all Home page content sections</p>
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


                                <div class="table-responsive">
                                    <table class="table table-hover" id="homeContentTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Section</th>
                                                <th>Field Name</th>
                                                <th>Content</th>
                                                <th>Sort Order</th>
                                                <th>Updated At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($homeContent as $content)
                                            <tr>
                                                <td>{{ $content->id }}</td>
                                                <td><span class="badge bg-primary">{{ $content->section }}</span></td>
                                                <td>{{ $content->field_name }}</td>
                                                <td>
                                                    @php
                                                    $contentText = $content->content;
                                                    $isImage = false;
                                                    $imagePath = '';

                                                    // Check if content is an image path or contains an img tag
                                                    if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $contentText)) {
                                                    $isImage = true;
                                                    $imagePath = $contentText;
                                                    } elseif (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/',
                                                        $contentText, $matches)) {
                                                        $isImage = true;
                                                        $imagePath = $matches[1];
                                                        }
                                                        @endphp

                                                        @if($isImage)
                                                        <img src="{{ asset($imagePath) }}" alt="Image"
                                                            style="max-width: 50px; max-height: 50px; border-radius: 4px; cursor: pointer;"
                                                            onclick="window.open('{{ asset($imagePath) }}', '_blank');"
                                                            title="Click to view full size">
                                                        @else
                                                        <small
                                                            class="text-truncate-3">{{ \Illuminate\Support\Str::limit(strip_tags($contentText), 50) }}</small>
                                                        @endif
                                                </td>
                                                <td>{{ $content->sort_order }}</td>
                                                <td>{{ $content->updated_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <div class="table-actions">
                                                        <button type="button" class="btn btn-sm btn-primary action-btn"
                                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                                            onclick="editContent({{ $content->id }})">
                                                            <i class="ti ti-edit"></i> Edit
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <p class="text-muted">No content found. The Home page is empty.</p>
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

                        @php
                            $currentMediaType = $aboutMediaType ?? null;
                            $currentMediaPath = $aboutMediaPath ?? null;
                        @endphp

                        <div id="aboutMediaSection" class="mb-3 p-3 border rounded bg-light" style="display: none;">
                            <h6 class="mb-3">About Section Media (Video / Image / GIF)</h6>
                            <div class="mb-3" id="currentMediaPreview">
                                @if ($currentMediaPath)
                                    @if ($currentMediaType === 'video')
                                        <video src="{{ asset($currentMediaPath) }}" controls
                                            style="max-width: 300px; max-height: 180px; border-radius: 8px;"></video>
                                    @else
                                        <img src="{{ asset($currentMediaPath) }}" alt="Current Media"
                                            style="max-width: 300px; max-height: 180px; border-radius: 8px;">
                                    @endif
                                    <div><small class="text-muted">Current: {{ ucfirst($currentMediaType) }} &middot; {{ $currentMediaPath }}</small></div>
                                @else
                                    <small class="text-muted">No media uploaded. The default video is currently shown.</small>
                                @endif
                            </div>

                            <div id="aboutMediaForm">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label for="mediaType" class="form-label">Media Type</label>
                                        <select class="form-select" id="mediaType" name="media_type">
                                            <option value="video" {{ $currentMediaType === 'video' ? 'selected' : '' }}>Video</option>
                                            <option value="image" {{ $currentMediaType === 'image' ? 'selected' : '' }}>Image / GIF</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="mediaFile" class="form-label">Select File</label>
                                        <input type="file" class="form-control" id="mediaFile" name="media_file"
                                            accept="video/*,image/*" onchange="previewAboutMedia(this)">
                                        <small class="text-muted">Maximum 50MB.</small>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-primary w-100" onclick="uploadAboutMedia()">
                                            <i class="ti ti-upload me-1"></i> Upload Media
                                        </button>
                                    </div>
                                </div>
                                <div id="newMediaPreview" class="mt-3"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="section" class="form-label">Section</label>
                            <input type="text" class="form-control" id="editSection" name="section" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="fieldName" class="form-label">Field Name</label>
                            <input type="text" class="form-control" id="editFieldName" name="field_name"
                                placeholder="Enter field name">
                        </div>

                        <div id="contentSection" class="mb-3">
                            <label for="editContent" class="form-label">Content</label>
                            <textarea class="form-control" id="editContent" name="content" rows="8"
                                placeholder="Enter content (HTML allowed)"></textarea>
                            <div id="contentPreview" class="mt-2"></div>
                            <small class="text-muted">Enter text content or image path. Images will be previewed
                                above.</small>
                        </div>

                        <div id="imageUploadSection" class="mb-3" style="display: none;">
                            <label for="imageUpload" class="form-label">Upload New Image (Optional)</label>
                            <input type="file" class="form-control" id="imageUpload" name="image_upload"
                                accept="image/*" onchange="previewUploadedImage(this)">
                            <div id="uploadedImagePreview" class="mt-2"></div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteCurrentImage()">
                                    <i class="ti ti-trash"></i> Delete Current Image
                                </button>
                            </div>
                            <small class="text-muted">Select a new image to upload or delete current image. This will
                                replace the current content with the new image path or empty content.</small>
                        </div>

                        <div class="mb-3">
                            <label for="editSortOrder" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="editSortOrder" name="sort_order" min="0">
                        </div>

                        <!-- <div class="mb-3">
                            <label for="editLink" class="form-label">Link</label>
                            <input type="url" class="form-control" id="editLink" name="link" placeholder="https://example.com">
                        </div> -->

                        <!-- <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="editStatus" name="status" value="1">
                                <label class="form-check-label" for="editStatus">
                                    Active
                                </label>
                            </div>
                        </div> -->
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
    function previewAboutMedia(input) {
        const preview = document.getElementById('newMediaPreview');
        preview.innerHTML = '';
        const file = input.files && input.files[0];
        if (!file) return;

        const url = URL.createObjectURL(file);
        preview.innerHTML = file.type.startsWith('video/')
            ? `<video src="${url}" controls style="max-width:300px;max-height:200px;border-radius:8px;"></video>`
            : `<img src="${url}" alt="Selected media" style="max-width:300px;max-height:200px;border-radius:8px;">`;
    }

    function uploadAboutMedia() {
        const file = document.getElementById('mediaFile').files[0];
        if (!file) {
            alert('Please select a video, image, or GIF.');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('media_type', document.getElementById('mediaType').value);
        formData.append('media_file', file);
        fetch(`${BASE_URL}/admin/update-about-media`, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Upload failed.');
            alert(data.message);
            location.reload();
        })
        .catch(error => alert(error.message));
    }

    let dataTable = null;
    $(document).ready(function() {
        $('#homeContentTable').DataTable();
    });
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

    function editContent(id) {
        // Fetch content data via AJAX using only the ID
        fetch(`${BASE_URL}/admin/get-home-content/${id}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('contentId').value = data.id;
            document.getElementById('editSection').value = data.section;
            document.getElementById('editFieldName').value = data.field_name;
            document.getElementById('editContent').value = data.content;
            document.getElementById('editSortOrder').value = data.sort_order;

            // The About media uploader belongs only to the media path record.
            const aboutMediaSection = document.getElementById('aboutMediaSection');
            const isAboutMediaRecord = data.section === 'about' && data.field_name === 'media_path';
            aboutMediaSection.style.display = isAboutMediaRecord ? 'block' : 'none';

            if (!isAboutMediaRecord) {
                document.getElementById('mediaFile').value = '';
                document.getElementById('newMediaPreview').innerHTML = '';
            }

            // Show image preview if content contains image
            updateContentPreview(data.content);

            // Show/hide image upload section based on whether content is an image
            toggleImageUploadSection(data.content);
        })
        .catch(error => {
            console.error('Error fetching content:', error);
            alert('Failed to load content data.');
        });
    }

    function toggleImageUploadSection(content) {
        const imageUploadSection = document.getElementById('imageUploadSection');
        const contentSection = document.getElementById('contentSection');

        if (content && (/\.(jpg|jpeg|png|gif|webp|svg)$/i.test(content.trim()) || /<img[^>]+src=["'][^"']+["']/i.test(
                content))) {
            // Content contains image, show image upload section and hide content section
            imageUploadSection.style.display = 'block';
            contentSection.style.display = 'none';
        } else {
            // Content doesn't contain image, hide image upload section and show content section
            imageUploadSection.style.display = 'none';
            contentSection.style.display = 'block';
        }
    }

    function updateContentPreview(content) {
        const preview = document.getElementById('contentPreview');
        preview.innerHTML = '';

        if (content) {
            // Check if content is an image path or contains an img tag
            if (/\.(jpg|jpeg|png|gif|webp|svg)$/i.test(content.trim())) {
                preview.innerHTML = `
                    <div class="mt-2">
                        <label class="form-label text-muted">Image Preview:</label><br>
                        <img src="${content.startsWith('/') ? content : '/' + content}" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 4px; border: 1px solid #ddd;" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="text-danger small" style="display: none;">Image not found</div>
                    </div>
                `;
            } else if (/<img[^>]+src=["\']([^"\']+)["\']/.test(content)) {
                const match = content.match(/<img[^>]+src=["\']([^"\']+)["\']/);
                if (match && match[1]) {
                    preview.innerHTML = `
                        <div class="mt-2">
                            <label class="form-label text-muted">Image Preview:</label><br>
                            <img src="/${match[1].replace(/^\/+/, '')}" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 4px; border: 1px solid #ddd;" 
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="text-danger small" style="display: none;">Image not found</div>
                        </div>
                    `;
                }
            }
        }
    }

    // Add event listener for content textarea changes
    document.addEventListener('DOMContentLoaded', function() {
        const contentTextarea = document.getElementById('editContent');
        if (contentTextarea) {
            contentTextarea.addEventListener('input', function() {
                updateContentPreview(this.value);
            });
        }
    });

    function previewUploadedImage(input) {
        const preview = document.getElementById('uploadedImagePreview');
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="mt-2">
                        <label class="form-label text-muted">New Image Preview:</label><br>
                        <img src="${e.target.result}" style="max-width: 200px; max-height: 150px; border-radius: 4px; border: 1px solid #ddd;" alt="Preview">
                        <div class="mt-1">
                            <small class="text-success">✓ Image selected: ${input.files[0].name}</small>
                        </div>
                    </div>
                `;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function deleteCurrentImage() {
        if (confirm('Are you sure you want to delete the current image? This will clear the content field.')) {
            const id = document.getElementById('contentId').value;
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('delete_image', 'true');

            fetch(`${BASE_URL}/admin/update-home-content/${id}`, {
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
        }
    }

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

        // Check if an image file is uploaded
        const imageUpload = document.getElementById('imageUpload');
        if (imageUpload.files && imageUpload.files[0]) {
            // If image is uploaded, send as FormData for file upload
            formData.append('image_upload', imageUpload.files[0]);

            fetch(`${BASE_URL}/admin/update-home-content/${id}`, {
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
        } else {
            // If no image uploaded, send as JSON
            const data = {
                section: formData.get('section'),
                field_name: formData.get('field_name'),
                content: formData.get('content'),
                sort_order: formData.get('sort_order'),
                _token: '{{ csrf_token() }}'
            };

            fetch(`${BASE_URL}/admin/update-home-content/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
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
        }

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