<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit Partnership Page Content | CRMS</title>
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
                            @if($partner->id)
                                Edit Partnership Content
                            @else
                                Create New Partnership Content
                            @endif
                        </h4>
                        <p class="text-muted mb-0">
                            @if($partner->id)
                                Editing: <strong>{{ $partner->title ?: ($partner->section ?: 'Partnership') }}</strong>
                            @else
                                Fill in the details to create new partnership page content
                            @endif
                        </p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ route('admin.change-partnership') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back to Partnership
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

                <form id="partnershipForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="partnerId" name="id" value="{{ $partner->id }}">
                    <input type="hidden" name="section" value="{{ $partner->section }}">

                    <div class="row">
                        <!-- Main Content Column -->
                        <div class="col-lg-8">

                            @if($partner->section)
                                {{-- ===== PAGE CONTENT ROW (has section value) ===== --}}
                                <!-- Basic Information -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Section</label>
                                            <input type="text" class="form-control" value="{{ $partner->section }}" readonly>
                                            <small class="text-muted">Section identifier (read-only)</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Item Key</label>
                                            <input type="text" class="form-control" value="{{ $partner->item_key }}" readonly>
                                            <small class="text-muted">Item key identifier (read-only)</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ old('title', $partner->title) }}"
                                                placeholder="Title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="6"
                                                placeholder="Description">{{ old('description', $partner->description) }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="link" class="form-label">Link / URL</label>
                                            <input type="text" class="form-control" id="link" name="link"
                                                value="{{ old('link', $partner->link) }}"
                                                placeholder="https://example.com">
                                            <small class="text-muted">Link or URL</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic JSON Fields -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Content Fields</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($partner->content && is_array($partner->content))
                                            @foreach($partner->content as $key => $value)
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

                                <!-- Image Upload / Display -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Image</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($partner->image)
                                        <div class="text-center mb-3">
                                            <img src="{{ asset($partner->image) }}" class="preview-img" alt="Content image">
                                            <p class="text-muted mt-2 mb-0"><small>Current image path: {{ $partner->image }}</small></p>
                                        </div>
                                        @else
                                        <p class="text-muted mb-3">No image currently set</p>
                                        @endif
                                        <div class="mb-3">
                                            <label for="partnerImageFile" class="form-label">Upload New Image</label>
                                            <input type="file" class="form-control" id="partnerImageFile"
                                                name="image" accept="image/*"
                                                onchange="previewImage(this, 'partnerImagePreview2')">
                                            <small class="text-muted">Leave empty to keep current image. Allowed: jpeg, png, jpg, gif, svg, webp, bmp, tiff (max 10MB)</small>
                                            <div id="partnerImagePreview2" class="mt-2"></div>
                                        </div>
                                    </div>
                                </div>

                            @else
                                {{-- ===== PARTNERSHIP ITEM (no section) ===== --}}

                                <!-- Basic Information -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ old('title', $partner->title) }}"
                                                placeholder="Title" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="6"
                                                placeholder="Description">{{ old('description', $partner->description) }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="link" class="form-label">Link / URL</label>
                                            <input type="text" class="form-control" id="link" name="link"
                                                value="{{ old('link', $partner->link) }}"
                                                placeholder="https://example.com">
                                            <small class="text-muted">Link or URL</small>
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
                                                <label for="badge_text" class="form-label">Badge Text</label>
                                                <input type="text" class="form-control" id="badge_text" name="json_fields[badge_text]"
                                                    value="{{ old('json_fields.badge_text', $partner->content['badge_text'] ?? '') }}"
                                                    placeholder="Our Export Ecosystem Partners">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="subtitle" class="form-label">Subtitle</label>
                                                <input type="text" class="form-control" id="subtitle" name="json_fields[subtitle]"
                                                    value="{{ old('json_fields.subtitle', $partner->content['subtitle'] ?? '') }}"
                                                    placeholder="Powering global commerce">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="button_text" class="form-label">Button Text</label>
                                                <input type="text" class="form-control" id="button_text" name="json_fields[button_text]"
                                                    value="{{ old('json_fields.button_text', $partner->content['button_text'] ?? '') }}"
                                                    placeholder="Join Network">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="features" class="form-label">Features (comma separated)</label>
                                                <input type="text" class="form-control" id="features" name="json_fields[features]"
                                                    value="{{ old('json_fields.features', $partner->content['features'] ?? '') }}"
                                                    placeholder="190+ Countries, API Integration, Support">
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
                                        @if($partner->image)
                                        <div class="text-center mb-3">
                                            <img src="{{ asset($partner->image) }}" class="preview-img" alt="{{ $partner->title }}">
                                            <p class="text-muted mt-2 mb-0"><small>Current image: {{ $partner->image }}</small></p>
                                        </div>
                                        @else
                                        <p class="text-muted mb-3">No image currently set</p>
                                        @endif
                                        <div class="mb-3">
                                            <label for="partnerImageFile" class="form-label">Upload New Image</label>
                                            <input type="file" class="form-control" id="partnerImageFile"
                                                name="image" accept="image/*"
                                                onchange="previewImage(this, 'partnerImagePreview')">
                                            <small class="text-muted">Leave empty to keep current image. Allowed: jpeg, png, jpg, gif, svg, webp, bmp, tiff (max 10MB)</small>
                                            <div id="partnerImagePreview" class="mt-2"></div>
                                        </div>
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
                                        {{ $partner->id ? 'Update' : 'Create' }}
                                    </button>
                                    <a href="{{ route('admin.change-partnership') }}" class="btn btn-outline-secondary w-100">
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
                                            <option value="Active" {{ old('status', $partner->status ?: 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ old('status', $partner->status ?: 'Active') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order"
                                            value="{{ old('sort_order', $partner->sort_order ?? 0) }}"
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
    // Partnership Form Submission
    // =============================================
    const partnerId = document.getElementById('partnerId').value;

    document.getElementById('partnershipForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        let url = partnerId ? `/admin/update-partnership/${partnerId}` : '/admin/store-partnership';

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
                    window.location.href = '{{ route("admin.change-partnership") }}';
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