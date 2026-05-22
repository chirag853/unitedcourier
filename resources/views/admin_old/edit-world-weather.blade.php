<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit World Weather Page Content | CRMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">

    <!-- Favicon -->
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
                            @if($worldWeather->id)
                                Edit World Weather Content
                            @else
                                Create New World Weather Content
                            @endif
                        </h4>
                        <p class="text-muted mb-0">
                            @if($worldWeather->id)
                                Editing: <strong>{{ $worldWeather->title ?: ($worldWeather->section ?: 'World Weather') }}</strong>
                            @else
                                Fill in the details to create new world weather page content
                            @endif
                        </p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ route('admin.change-world-weather') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back to World Weather
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

                <form id="worldWeatherForm" method="POST" @if(!$worldWeather->id) enctype="multipart/form-data" @endif>
                    @csrf
                    <input type="hidden" id="worldWeatherId" name="id" value="{{ $worldWeather->id }}">

                    <div class="row">
                        <!-- Main Content Column -->
                        <div class="col-lg-8">

                            @if($worldWeather->section)
                                {{-- ===== PAGE CONTENT ROW (has section value) ===== --}}
                                <!-- Section Info -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Page Content</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="section" name="section"
                                                value="{{ old('section', $worldWeather->section) }}"
                                                placeholder="e.g. hero, features">
                                        </div>
                                        <div class="mb-3">
                                            <label for="item_key" class="form-label">Item Key</label>
                                            <input type="text" class="form-control" id="item_key" name="item_key"
                                                value="{{ old('item_key', $worldWeather->item_key) }}"
                                                placeholder="e.g. hero_content, features_header">
                                        </div>
                                    </div>
                                </div>

                                <!-- Basic Information -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ old('title', $worldWeather->title) }}"
                                                placeholder="Title">
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="4"
                                                placeholder="Description">{{ old('description', $worldWeather->description) }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="link" class="form-label">Link / URL</label>
                                            <input type="text" class="form-control" id="link" name="link"
                                                value="{{ old('link', $worldWeather->link) }}"
                                                placeholder="https://example.com">
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic JSON Fields -->
                                <!-- <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Content Fields (JSON)</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($worldWeather->content && is_array($worldWeather->content))
                                            @foreach($worldWeather->content as $key => $value)
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
                                </div> -->

                                <!-- Image Display (Read-Only) -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Image</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($worldWeather->image)
                                        <div class="text-center">
                                            <img src="{{ asset($worldWeather->image) }}" class="preview-img" alt="Content image">
                                            <p class="text-muted mt-2 mb-0"><small>Image path: {{ $worldWeather->image }}</small></p>
                                        </div>
                                        @else
                                        <p class="text-muted mb-0">No image set</p>
                                        @endif
                                    </div>
                                </div>

                            @else
                                {{-- ===== WORLD WEATHER ITEM (no section) ===== --}}

                                <!-- Basic Information -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ old('title', $worldWeather->title) }}"
                                                placeholder="Title" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="6"
                                                placeholder="Description">{{ old('description', $worldWeather->description) }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="link" class="form-label">Link / URL</label>
                                            <input type="text" class="form-control" id="link" name="link"
                                                value="{{ old('link', $worldWeather->link) }}"
                                                placeholder="https://example.com">
                                            <small class="text-muted">External link or URL</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Upload / Display -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Image</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($worldWeather->id)
                                            {{-- Edit mode: read-only display --}}
                                            @if($worldWeather->image)
                                            <div class="text-center">
                                                <img src="{{ asset($worldWeather->image) }}" class="preview-img" alt="{{ $worldWeather->title }}">
                                                <p class="text-muted mt-2 mb-0"><small>Image cannot be edited</small></p>
                                            </div>
                                            @else
                                            <p class="text-muted mb-0">No image available</p>
                                            @endif
                                        @else
                                            {{-- Create mode: upload field --}}
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Upload Image</label>
                                                <input type="file" class="form-control" id="worldWeatherImageFile"
                                                    name="image" accept="image/*"
                                                    onchange="previewImage(this, 'worldWeatherImagePreview')">
                                                <div id="worldWeatherImagePreview" class="mt-2"></div>
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
                                        {{ $worldWeather->id ? 'Update' : 'Create' }}
                                    </button>
                                    <a href="{{ route('admin.change-world-weather') }}" class="btn btn-outline-secondary w-100">
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
                                            <option value="Active" {{ old('status', $worldWeather->status ?: 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ old('status', $worldWeather->status ?: 'Active') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order"
                                            value="{{ old('sort_order', $worldWeather->sort_order ?? 0) }}"
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
    // World Weather Form Submission
    // =============================================
    const worldWeatherId = document.getElementById('worldWeatherId').value;

    document.getElementById('worldWeatherForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        let url = worldWeatherId ? `/admin/update-world-weather/${worldWeatherId}` : '/admin/store-world-weather';

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
                    window.location.href = '{{ route("admin.change-world-weather") }}';
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

</html></html>