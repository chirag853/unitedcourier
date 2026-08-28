<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/48.1.0/ckeditor5.css">

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
    .blog-preview-img {
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

    .cke_notifications_area{
        display: none !important;
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
                            @if($blog->id)
                                Edit Blog Post
                            @else
                                Create New Blog Post
                            @endif
                        </h4>
                        <p class="text-muted mb-0">
                            @if($blog->id)
                                Editing: <strong>{{ $blog->blog_title }}</strong>
                            @else
                                Fill in the details to create a new blog post
                            @endif
                        </p>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="{{ route('admin.change-blog') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back to Blogs
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

                <form id="blogForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="blogId" name="id" value="{{ $blog->id }}">

                    <div class="row">
                        <!-- Main Content Column -->
                        <div class="col-lg-8">

                            <!-- Basic Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="blog_title" class="form-label">Blog Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="blog_title" name="blog_title"
                                                    value="{{ old('blog_title', $blog->blog_title) }}"
                                                    placeholder="Blog post title" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="url_title" class="form-label">URL Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="url_title" name="url_title"
                                                    value="{{ old('url_title', $blog->url_title) }}"
                                                    placeholder="URL-friendly title" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="slug" name="slug"
                                                    value="{{ old('slug', $blog->slug) }}"
                                                    placeholder="blog-post-slug" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                                <select class="form-control" id="category_id" name="category_id" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ old('category_id', $blog->category_id) == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sub_heading" class="form-label">Sub Heading</label>
                                                <input type="text" class="form-control" id="sub_heading" name="sub_heading"
                                                    value="{{ old('sub_heading', $blog->sub_heading) }}"
                                                    placeholder="Short subheading below title">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="is_trending" class="form-label">Is Trending</label>
                                                <select class="form-control" id="is_trending" name="is_trending">
                                                    <option value="No" {{ old('is_trending', $blog->is_trending) == 'No' ? 'selected' : '' }}>No</option>
                                                    <option value="Yes" {{ old('is_trending', $blog->is_trending) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sub_content" class="form-label">Sub Content</label>
                                        <textarea class="form-control" id="sub_content" name="sub_content" rows="3"
                                            placeholder="Brief content below the heading">{{ old('sub_content', $blog->sub_content) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Media & Images -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Media & Images</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="master_image" class="form-label">Master Image</label>
                                                <input type="file" class="form-control" id="masterImageFile" name="master_image"
                                                    accept="image/*" onchange="previewImage(this, 'masterImagePreview')">
                                                <div id="masterImagePreview" class="mt-2">
                                                    @if($blog->master_image)
                                                    <img src="{{ asset($blog->master_image) }}" class="image-preview-box" alt="Preview">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="master_image_alt_text" class="form-label">Master Image Alt Text</label>
                                                <input type="text" class="form-control" id="master_image_alt_text" name="master_image_alt_text"
                                                    value="{{ old('master_image_alt_text', $blog->master_image_alt_text) }}"
                                                    placeholder="SEO alt text for image">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="image_alt" class="form-label">Image Alt Tag</label>
                                                <input type="text" class="form-control" id="image_alt" name="image_alt"
                                                    value="{{ old('image_alt', $blog->image_alt) }}"
                                                    placeholder="Additional image alt description">
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="social_title" class="form-label">Social Title</label>
                                                <input type="text" class="form-control" id="social_title" name="social_title"
                                                    value="{{ old('social_title', $blog->social_title) }}"
                                                    placeholder="Title shown on social shares">
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Blog Content</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="blog_description" class="form-label">Blog Description (Content)</label>
                                        <textarea class="form-control" id="blog_description" name="blog_description" rows="12"
                                            placeholder="Full blog content">{{ old('blog_description', $blog->blog_description) }}</textarea>
                                    </div>

                                    <!-- Multiple Image Upload -->
                                    <!-- <div class="mb-3 p-3 border rounded bg-light">
                                        <label class="form-label fw-semibold">
                                            <i class="ti ti-photo-plus me-1"></i> Upload Multiple Images
                                        </label>
                                        <p class="text-muted small mb-2">Select multiple images to upload into the blog_image folder. Uploaded images will be inserted into the editor at the cursor position.</p>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="file" class="form-control" id="blogImagesInput" name="images[]" accept="image/*" multiple>
                                            <button type="button" class="btn btn-primary" id="uploadImagesBtn" onclick="uploadMultipleImages()">
                                                <i class="ti ti-cloud-upload me-1"></i> Upload
                                            </button>
                                        </div>
                                        <div id="multipleImagePreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                        <div id="uploadProgress" class="mt-2" style="display:none;">
                                            <div class="progress" style="height:6px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" id="uploadProgressBar" role="progressbar" style="width:0%"></div>
                                            </div>
                                            <small id="uploadStatusText" class="text-muted mt-1 d-block">Uploading...</small>
                                        </div>
                                        <div id="uploadResult" class="mt-2"></div>
                                    </div> -->

                                    <!-- <div class="mb-3">
                                        <label for="feed" class="form-label">Feed (RSS / XML)</label>
                                        <textarea class="form-control" id="feed" name="feed" rows="4"
                                            placeholder="Feed content for RSS/XML syndication">{{ old('feed', $blog->feed) }}</textarea>
                                    </div> -->
                                </div>
                            </div>

                            <!-- SEO Meta -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">SEO & Meta</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="seo_meta_title" class="form-label">SEO Meta Title</label>
                                                <input type="text" class="form-control" id="seo_meta_title" name="seo_meta_title"
                                                    value="{{ old('seo_meta_title', $blog->seo_meta_title) }}"
                                                    placeholder="Title tag for search engines">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="meta_keyword" class="form-label">Meta Keywords</label>
                                                <input type="text" class="form-control" id="meta_keyword" name="meta_keyword"
                                                    value="{{ old('meta_keyword', $blog->meta_keyword) }}"
                                                    placeholder="keyword1, keyword2, keyword3">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                            placeholder="Description for search engine results">{{ old('meta_description', $blog->meta_description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Open Graph / Social -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Open Graph & Social Sharing</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="og_title" class="form-label">OG Title</label>
                                                <input type="text" class="form-control" id="og_title" name="og_title"
                                                    value="{{ old('og_title', $blog->og_title) }}"
                                                    placeholder="Title for social media preview">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="og_url" class="form-label">OG URL</label>
                                                <input type="text" class="form-control" id="og_url" name="og_url"
                                                    value="{{ old('og_url', $blog->og_url) }}"
                                                    placeholder="Canonical URL for social share">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="og_image_url" class="form-label">OG Image URL</label>
                                                <input type="text" class="form-control" id="og_image_url" name="og_image_url"
                                                    value="{{ old('og_image_url', $blog->og_image_url) }}"
                                                    placeholder="Image URL for social preview">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="twitter_card" class="form-label">Twitter Card Type</label>
                                                <select class="form-control" id="twitter_card" name="twitter_card">
                                                    <option value="">None</option>
                                                    <option value="summary" {{ old('twitter_card', $blog->twitter_card) == 'summary' ? 'selected' : '' }}>Summary</option>
                                                    <option value="summary_large_image" {{ old('twitter_card', $blog->twitter_card) == 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                                                    <option value="app" {{ old('twitter_card', $blog->twitter_card) == 'app' ? 'selected' : '' }}>App</option>
                                                    <option value="player" {{ old('twitter_card', $blog->twitter_card) == 'player' ? 'selected' : '' }}>Player</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="og_description" class="form-label">OG Description</label>
                                        <textarea class="form-control" id="og_description" name="og_description" rows="3"
                                            placeholder="Description for social media preview">{{ old('og_description', $blog->og_description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information -->
                            <!-- <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Location Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="country_name" class="form-label">Country</label>
                                                <input type="text" class="form-control" id="country_name" name="country_name"
                                                    value="{{ old('country_name', $blog->country_name) }}"
                                                    placeholder="e.g., India">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="state_name" class="form-label">State</label>
                                                <input type="text" class="form-control" id="state_name" name="state_name"
                                                    value="{{ old('state_name', $blog->state_name) }}"
                                                    placeholder="e.g., Delhi">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="city_name" class="form-label">City</label>
                                                <input type="text" class="form-control" id="city_name" name="city_name"
                                                    value="{{ old('city_name', $blog->city_name) }}"
                                                    placeholder="e.g., New Delhi">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <!-- Author Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Author Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="author_name" class="form-label">Author Name</label>
                                                <input type="text" class="form-control" id="author_name" name="author_name"
                                                    value="{{ old('author_name', $blog->author_name) }}"
                                                    placeholder="Author name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="author_image" class="form-label">Author Image</label>
                                                <input type="file" class="form-control" id="authorImageFile"
                                                    name="author_image" accept="image/*"
                                                    onchange="previewImage(this, 'authorImagePreview')">
                                                <div id="authorImagePreview" class="mt-2">
                                                    @if($blog->author_image)
                                                    <img src="{{ asset($blog->author_image) }}" class="image-preview-box" alt="Author Preview">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="author_description" class="form-label">Author Description</label>
                                        <input type="text" class="form-control" id="author_description" name="author_description" rows="3"
                                            placeholder="Brief bio about the author">{{ old('author_description', $blog->author_description) }}</input>
                                    </div>
                                </div>
                            </div>
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
                                        {{ $blog->id ? 'Update Blog Post' : 'Create Blog Post' }}
                                    </button>
                                    <a href="{{ route('admin.change-blog') }}" class="btn btn-outline-secondary w-100">
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
                                            <option value="Active" {{ old('status', $blog->status ?: 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ old('status', $blog->status ?: 'Active') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
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
    <!-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> -->
    <!-- <script src="https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js"></script> -->
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>


    <script>

    CKEDITOR.replace('blog_description', {
        height: 400,
        filebrowserImageUploadUrl: '{{ route("admin.upload-blog-image") }}',
    });

    CKEDITOR.on('instanceReady', function(ev) {
        ev.editor.on('fileUploadRequest', function(evt) {
            var xhr = evt.data.fileLoader.xhr;
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        });
    });

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
    // Multiple Image Upload - Upload to blog_image/ and insert into CKEditor
    // =============================================
    function uploadMultipleImages() {
        const input = document.getElementById('blogImagesInput');
        const files = input.files;
        if (!files || files.length === 0) {
            showAlert('Please select at least one image to upload.', 'warning');
            return;
        }

        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }
        formData.append('_token', '{{ csrf_token() }}');

        // Show progress
        const progressDiv = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('uploadProgressBar');
        const statusText = document.getElementById('uploadStatusText');
        const uploadBtn = document.getElementById('uploadImagesBtn');
        const resultDiv = document.getElementById('uploadResult');

        progressDiv.style.display = 'block';
        uploadBtn.disabled = true;
        resultDiv.innerHTML = '';
        statusText.textContent = 'Uploading ' + files.length + ' image(s)...';

        // Simulate progress (since fetch doesn't have real progress for upload)
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.floor(Math.random() * 15) + 5;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
        }, 300);

        fetch('{{ route("admin.upload-multiple-blog-images") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            statusText.textContent = 'Upload complete!';

            if (data.success && data.urls && data.urls.length > 0) {
                // Insert each image into CKEditor
                const editor = CKEDITOR.instances.blog_description;
                if (editor) {
                    data.urls.forEach(function(url) {
                        const imgHtml = '<p><img src="' + url + '" alt="Blog Image" style="max-width:100%;" /></p>';
                        editor.insertHtml(imgHtml);
                    });
                }

                // Show success with preview thumbnails
                let previewHtml = '<div class="alert alert-success py-2 px-3 mb-0"><i class="ti ti-circle-check me-1"></i> ' + data.message + '</div>';
                previewHtml += '<div class="d-flex flex-wrap gap-2 mt-2">';
                data.urls.forEach(function(url) {
                    previewHtml += '<img src="' + url + '" class="image-preview-box" alt="Uploaded" style="cursor:pointer;" onclick="window.open(\'' + url + '\',\'_blank\')">';
                });
                previewHtml += '</div>';
                resultDiv.innerHTML = previewHtml;

                // Clear the file input
                input.value = '';
                document.getElementById('multipleImagePreview').innerHTML = '';
            } else {
                resultDiv.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-0"><i class="ti ti-alert-circle me-1"></i> ' + (data.message || 'Upload failed') + '</div>';
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            progressBar.style.width = '0%';
            statusText.textContent = 'Upload failed!';
            resultDiv.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-0"><i class="ti ti-alert-circle me-1"></i> Network error: ' + error.message + '</div>';
        })
        .finally(function() {
            setTimeout(function() {
                progressDiv.style.display = 'none';
                progressBar.style.width = '0%';
                uploadBtn.disabled = false;
            }, 2000);
        });
    }

    // Preview selected images before upload
    document.getElementById('blogImagesInput')?.addEventListener('change', function() {
        const preview = document.getElementById('multipleImagePreview');
        preview.innerHTML = '';
        if (this.files) {
            for (let i = 0; i < this.files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview-box';
                    img.title = this.files[i].name;
                    preview.appendChild(img);
                }.bind(this, i);
                reader.readAsDataURL(this.files[i]);
            }
        }
    });

    // Auto-generate slug from blog_title
    document.getElementById('blog_title')?.addEventListener('blur', function() {
        const slugField = document.getElementById('slug');
        if (!slugField.value) {
            slugField.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
        const urlTitleField = document.getElementById('url_title');
        if (!urlTitleField.value) {
            urlTitleField.value = this.value;
        }
    });

    // =============================================
    // Blog Form Submission
    // =============================================
    const blogId = document.getElementById('blogId').value;

    document.getElementById('blogForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Sync CKEditor content back to the textarea before form submission
        if (CKEDITOR.instances.blog_description) {
            CKEDITOR.instances.blog_description.updateElement();
        }

        const formData = new FormData(this);

        let url = blogId ? `${BASE_URL}/admin/update-blog/${blogId}` : `${BASE_URL}/admin/store-blog`;

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
                    showAlert(data.message, 'success', function () { window.location.href = '{{ route("admin.change-blog") }}'; });
                } else {
                    console.error('Server Error:', data);
                    showAlert('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
                showAlert('Network error occurred. Please check your connection and try again.', 'error');
            });
    });
    </script>
</body>
</html>