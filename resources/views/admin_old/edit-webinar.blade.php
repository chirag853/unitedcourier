<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit Webinar Page Content | CRMS</title>
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
    .image-preview-box {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        padding: 2px;
        background: #f8f9fa;
    }
    .image-preview-box:hover {
        border-color: #2563eb;
    }
    .preview-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
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
                            @if($webinar->id)
                                Edit Webinar Content
                            @else
                                Create New Webinar Content
                            @endif
                        </h4>
                        <p class="text-muted mb-0">
                            @if($webinar->id)
                                Editing: <strong>{{ $webinar->title ?: ($webinar->section ?: 'Webinar') }}</strong>
                            @else
                                Fill in the details to create new webinar page content
                            @endif
                        </p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ route('admin.change-webinar') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back to Webinar
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

                <form id="webinarForm" method="POST" @if(!$webinar->id) enctype="multipart/form-data" @endif>
                    @csrf
                    <input type="hidden" id="webinarId" name="id" value="{{ $webinar->id }}">
                    <input type="hidden" name="section" value="{{ $webinar->section }}">

                    <div class="row">
                        <!-- Main Content Column -->
                        <div class="col-lg-8">

                            @if($webinar->section)
                                {{-- ===== PAGE CONTENT ROW (has section value) ===== --}}
                                <!-- Section Info -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Page Content</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Section</label>
                                            <input type="text" class="form-control" value="{{ $webinar->section }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Item Key</label>
                                            <input type="text" class="form-control" value="{{ $webinar->item_key }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic JSON Fields -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Content Fields</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($webinar->content && is_array($webinar->content))
                                            @foreach($webinar->content as $key => $value)
                                            <div class="mb-3">
                                                <label for="json_{{ $key }}" class="form-label">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                                @if(is_string($value) && (strlen($value) > 100 || str_contains($value, "\n")))
                                                <textarea class="form-control" id="json_{{ $key }}" name="json_fields[{{ $key }}]" rows="4">{{ old('json_fields.' . $key, $value) }}</textarea>
                                                @elseif(is_string($value) && (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')))
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="json_{{ $key }}" name="json_fields[{{ $key }}]" value="{{ old('json_fields.' . $key, $value) }}" placeholder="https://...">
                                                    @if($value)
                                                    <a href="{{ $value }}" target="_blank" class="btn btn-outline-primary"><i class="ti ti-external-link"></i></a>
                                                    @endif
                                                </div>
                                                @else
                                                <input type="text" class="form-control" id="json_{{ $key }}" name="json_fields[{{ $key }}]" value="{{ old('json_fields.' . $key, $value) }}">
                                                @endif
                                            </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted mb-0">No content data available.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Image Display -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Image</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($webinar->image)
                                        <div class="text-center">
                                            <img src="{{ asset($webinar->image) }}" class="preview-img" alt="Content image">
                                            <p class="text-muted mt-2 mb-0"><small>Image path: {{ $webinar->image }}</small></p>
                                        </div>
                                        @else
                                        <p class="text-muted mb-0">No image set</p>
                                        @endif
                                    </div>
                                </div>

                            @else
                                {{-- ===== WEBINAR ITEM (no section) ===== --}}

                                <!-- Basic Information -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ old('title', $webinar->title) }}"
                                                placeholder="Title" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="6"
                                                placeholder="Description">{{ old('description', $webinar->description) }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="link" class="form-label">Link / URL</label>
                                            <input type="text" class="form-control" id="link" name="link"
                                                value="{{ old('link', $webinar->link) }}"
                                                placeholder="https://example.com">
                                            <small class="text-muted">External link or URL</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="link_text" class="form-label">Link Text</label>
                                            <input type="text" class="form-control" id="link_text" name="json_fields[link_text]"
                                                value="{{ old('json_fields.link_text', $webinar->content['link_text'] ?? '') }}"
                                                placeholder="Watch Now">
                                        </div>
                                    </div>
                                </div>

                                <!-- Content Details (stored as JSON) -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Content Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="category_tag" class="form-label">Category Tag</label>
                                                <input type="text" class="form-control" id="category_tag" name="json_fields[category_tag]"
                                                    value="{{ old('json_fields.category_tag', $webinar->content['category_tag'] ?? '') }}"
                                                    placeholder="B2B Webinar">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="read_time" class="form-label">Read Time</label>
                                                <input type="text" class="form-control" id="read_time" name="json_fields[read_time]"
                                                    value="{{ old('json_fields.read_time', $webinar->content['read_time'] ?? '') }}"
                                                    placeholder="35 min">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="author_name" class="form-label">Author Name</label>
                                                <input type="text" class="form-control" id="author_name" name="json_fields[author_name]"
                                                    value="{{ old('json_fields.author_name', $webinar->content['author_name'] ?? '') }}"
                                                    placeholder="Rahul Mehta">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="author_role" class="form-label">Author Role</label>
                                                <input type="text" class="form-control" id="author_role" name="json_fields[author_role]"
                                                    value="{{ old('json_fields.author_role', $webinar->content['author_role'] ?? '') }}"
                                                    placeholder="Market Analyst">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="author_company" class="form-label">Author Company</label>
                                                <input type="text" class="form-control" id="author_company" name="json_fields[author_company]"
                                                    value="{{ old('json_fields.author_company', $webinar->content['author_company'] ?? '') }}"
                                                    placeholder="United">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="publish_date" class="form-label">Publish Date</label>
                                                <input type="text" class="form-control" id="publish_date" name="json_fields[publish_date]"
                                                    value="{{ old('json_fields.publish_date', $webinar->content['publish_date'] ?? '') }}"
                                                    placeholder="May 22, 2026">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Upload / Display -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Image</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($webinar->id)
                                            {{-- Edit mode: read-only display --}}
                                            @if($webinar->image)
                                            <div class="text-center">
                                                <img src="{{ asset($webinar->image) }}" class="preview-img" alt="{{ $webinar->title }}">
                                                <p class="text-muted mt-2 mb-0"><small>Image cannot be edited</small></p>
                                            </div>
                                            @else
                                            <p class="text-muted mb-0">No image available</p>
                                            @endif
                                        @else
                                            {{-- Create mode: upload field --}}
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Upload Image</label>
                                                <input type="file" class="form-control" id="webinarImageFile"
                                                    name="image" accept="image/*"
                                                    onchange="previewImage(this, 'webinarImagePreview')">
                                                <div id="webinarImagePreview" class="mt-2"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            @endif

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
                                        {{ $webinar->id ? 'Update' : 'Create' }}
                                    </button>
                                    <a href="{{ route('admin.change-webinar') }}" class="btn btn-outline-secondary w-100">
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
                                            <option value="Active" {{ old('status', $webinar->status ?: 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ old('status', $webinar->status ?: 'Active') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order"
                                            value="{{ old('sort_order', $webinar->sort_order ?? 0) }}"
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
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        preview.innerHTML = '';
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'image-preview-box';
                preview.appendChild(img);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // =============================================
    // Webinar Form Submission
    // =============================================
    const webinarId = document.getElementById('webinarId').value;

    document.getElementById('webinarForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        let url = webinarId ? `/admin/update-webinar/${webinarId}` : '/admin/store-webinar';

        fetch(url, {
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
                    window.location.href = '{{ route("admin.change-webinar") }}';
                } else {
                    console.error('Server Error:', data);
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
                alert('Network error occurred. Please check your connection and try again.');
            });
    });
    </script>
</body>

</html>