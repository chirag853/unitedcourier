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
            max-width: 400px;
        }
        .content-preview table tr td {
            white-space: nowrap;
        }
        .content-preview table tr td:last-child {
            white-space: normal;
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
                        <h4 class="mb-1">Change Warehousing Solutions</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <!-- <button type="button" class="btn btn-primary" onclick="openCreateModal()"><i class="ti ti-plus me-1"></i> Add New Content</button> -->
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                
                <!-- End Page Header -->

                <!-- Warehousing Solutions Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Warehousing Solutions Content Management</h5>
                                <p class="card-text">View and Edit all Warehousing Solutions page content sections</p>
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
                                    <table class="table table-hover" id="warehousingContentTable">
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
                                            @forelse($warehousingContent as $content)
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
                                                            @if(is_array($contentData) && count($contentData) > 0)
                                                                <table class="table table-sm table-bordered mb-0" style="font-size: 11px;">
                                                                    @foreach($contentData as $key => $val)
                                                                        @if($val !== null)
                                                                            <tr>
                                                                                <td class="fw-semibold text-nowrap p-1" style="width: 30%;">{{ $key }}</td>
                                                                                <td class="p-1" style="word-break: break-all;">
                                                                                    @if(is_array($val))
                                                                                        {{ is_string($val[0] ?? null) ? implode(', ', $val) : json_encode($val) }}
                                                                                    @elseif(is_string($val) && strlen($val) > 80)
                                                                                        {{ \Illuminate\Support\Str::limit($val, 80) }}
                                                                                    @else
                                                                                        {{ $val }}
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    @endforeach
                                                                </table>
                                                            @else
                                                                <small class="text-muted">No content data</small>
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
                                                            <button type="button" class="btn btn-sm btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-content='{!! json_encode($contentData, JSON_HEX_APOS) !!}' onclick="editContent(this, {{ $content->id }}, '{{ $content->section }}', '{{ $content->item_key }}', {{ $content->sort_order }}, {{ $content->is_active ? 1 : 0 }})">
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
                                                    <td colspan="7" class="text-center py-4">
                                                        <p class="text-muted">No warehousing solutions content found. The page is empty.</p>
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
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Warehousing Solutions Content</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" action="{{ route('admin.store-warehousing-solutions-content') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="contentId" name="id">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="section" class="form-label">Section</label>
                                    <select class="form-control" id="section" name="section" required>
                                        <option value="hero">Hero</option>
                                        <option value="stats">Stats</option>
                                        <option value="overview">Overview</option>
                                        <option value="features_header">Features Header</option>
                                        <option value="features">Features</option>
                                        <option value="faq">FAQ</option>
                                        <option value="cta">CTA</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="itemKey" class="form-label">Item Key</label>
                                    <input type="text" class="form-control" id="itemKey" name="item_key" placeholder="e.g., hero_main, stat_1" required>
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
                                <div id="heroFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="heroTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="heroTitle" name="content[title]" placeholder="Hero title">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroSubtitle" class="form-label">Subtitle</label>
                                        <input type="text" class="form-control" id="heroSubtitle" name="content[subtitle]" placeholder="Hero subtitle">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroParagraphs" class="form-label">Paragraphs</label>
                                        <textarea class="form-control" id="heroParagraphs" name="content[paragraphs]" rows="3" placeholder="Hero content"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroBadgeText" class="form-label">Badge Text</label>
                                        <input type="text" class="form-control" id="heroBadgeText" name="content[badge_text]" placeholder="Badge text">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonText" class="form-label">Button Text</label>
                                        <input type="text" class="form-control" id="heroButtonText" name="content[button_text]" placeholder="Button text">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonUrl" class="form-label">Button URL</label>
                                        <input type="text" class="form-control" id="heroButtonUrl" name="content[button_url]" placeholder="# or URL">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroImage" class="form-label">Image</label>
                                        <input type="hidden" id="heroImage" name="content[image]">
                                        <button type="button" class="btn btn-outline-secondary btn-upload-image" data-target="heroImage" data-preview="heroImagePreview">
                                            <i class="ti ti-upload"></i> Upload Image
                                        </button>
                                        <input type="file" class="d-none image-file-input" data-target="heroImage" data-preview="heroImagePreview" accept="image/*">
                                        <img id="heroImagePreview" class="img-thumbnail mt-2" style="max-height:120px; display:none;" alt="preview">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroListItems" class="form-label">List Items (comma-separated)</label>
                                        <textarea class="form-control" id="heroListItems" name="content[list_items]" rows="2" placeholder="Item 1, Item 2, Item 3"></textarea>
                                        <small class="text-muted">Enter list items separated by commas</small>
                                    </div>
                                </div>

                                <div id="statsFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="statNumber" class="form-label">Stat Number</label>
                                        <input type="text" class="form-control" id="statNumber" name="content[stat_number]" placeholder="50000+">
                                    </div>
                                    <div class="mb-3">
                                        <label for="statLabel" class="form-label">Stat Label</label>
                                        <input type="text" class="form-control" id="statLabel" name="content[stat_label]" placeholder="Sq Ft Storage Space">
                                    </div>
                                    <div class="mb-3">
                                        <label for="statSuffix" class="form-label">Suffix</label>
                                        <input type="text" class="form-control" id="statSuffix" name="content[suffix]" placeholder="+">
                                    </div>
                                </div>

                                <div id="featuresFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="featureSubtitle" class="form-label">Subtitle</label>
                                        <input type="text" class="form-control" id="featureSubtitle" name="content[subtitle]" placeholder="Feature subtitle">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featureParagraphs" class="form-label">Paragraphs</label>
                                        <textarea class="form-control" id="featureParagraphs" name="content[paragraphs]" rows="3" placeholder="Feature description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="featureIconSvg" class="form-label">Icon SVG</label>
                                        <textarea class="form-control" id="featureIconSvg" name="content[icon_svg]" rows="3" placeholder="SVG code"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="featureIconClass" class="form-label">Icon Class</label>
                                        <input type="text" class="form-control" id="featureIconClass" name="content[icon_class]" placeholder="fi-blue">
                                    </div>
                                </div>

                                <div id="featuresHeaderFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="featuresHeaderTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="featuresHeaderTitle" name="content[title]" placeholder="Features header title">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featuresHeaderSubtitle" class="form-label">Subtitle</label>
                                        <input type="text" class="form-control" id="featuresHeaderSubtitle" name="content[subtitle]" placeholder="Features header subtitle">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featuresHeaderDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="featuresHeaderDescription" name="content[description]" rows="3" placeholder="Section description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="featuresHeaderParagraph" class="form-label">Paragraph</label>
                                        <textarea class="form-control" id="featuresHeaderParagraph" name="content[paragraphs]" rows="3" placeholder="Section description"></textarea>
                                    </div>
                                </div>

                                <div id="overviewFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="overviewTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="overviewTitle" name="content[title]" placeholder="Overview title">
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewParagraphs" class="form-label">Paragraphs</label>
                                        <textarea class="form-control" id="overviewParagraphs" name="content[paragraphs]" rows="3" placeholder="Overview content"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewImage" class="form-label">Image</label>
                                        <input type="hidden" id="overviewImage" name="content[image]">
                                        <button type="button" class="btn btn-outline-secondary btn-upload-image" data-target="overviewImage" data-preview="overviewImagePreview">
                                            <i class="ti ti-upload"></i> Upload Image
                                        </button>
                                        <input type="file" class="d-none image-file-input" data-target="overviewImage" data-preview="overviewImagePreview" accept="image/*">
                                        <img id="overviewImagePreview" class="img-thumbnail mt-2" style="max-height:120px; display:none;" alt="preview">
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewListItems" class="form-label">List Items (comma-separated)</label>
                                        <textarea class="form-control" id="overviewListItems" name="content[list_items]" rows="3" placeholder="Item 1, Item 2, Item 3"></textarea>
                                        <small class="text-muted">Enter list items separated by commas</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewButtonText" class="form-label">Button Text</label>
                                        <input type="text" class="form-control" id="overviewButtonText" name="content[button_text]" placeholder="Button text">
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewButtonUrl" class="form-label">Button URL</label>
                                        <input type="text" class="form-control" id="overviewButtonUrl" name="content[button_url]" placeholder="# or URL">
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

                                <div id="ctaFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="ctaTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="ctaTitle" name="content[title]" placeholder="CTA title">
                                    </div>
                                    <div class="mb-3">
                                        <label for="ctaSubtitle" class="form-label">Subtitle</label>
                                        <input type="text" class="form-control" id="ctaSubtitle" name="content[subtitle]" placeholder="CTA subtitle">
                                    </div>
                                    <div class="mb-3">
                                        <label for="ctaButtonText" class="form-label">Button Text</label>
                                        <input type="text" class="form-control" id="ctaButtonText" name="content[button_text]" placeholder="Button text">
                                    </div>
                                    <div class="mb-3">
                                        <label for="ctaButtonUrl" class="form-label">Button URL</label>
                                        <input type="text" class="form-control" id="ctaButtonUrl" name="content[button_url]" placeholder="# or URL">
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
        $('#warehousingContentTable').DataTable();
    } );

    function editContent(button, id, section, itemKey, sortOrder, isActive) {
        const content = JSON.parse(button.dataset.content);
        
        // DEBUG: Log what we're getting
        console.log('editContent called', {id, section, itemKey, content});
        console.log('paragraphs value:', content.paragraphs);
        console.log('paragraphs type:', typeof content.paragraphs);
        const formSection = section === 'features' && itemKey === 'features_header' ? 'features_header' : section;
        const form = document.getElementById('editForm');
        form.action = '{{ route('admin.update-warehousing-solutions-content', ['id' => '__ID__']) }}'.replace('__ID__', id);
        
        document.getElementById('contentId').value = id;
        document.getElementById('section').value = formSection;
        document.getElementById('itemKey').value = itemKey;
        document.getElementById('sortOrder').value = sortOrder;
        document.getElementById('isActive').checked = isActive == 1;
        
        hideAllSectionFields();
        
        // Show relevant fields and populate data
        switch(formSection) {
            case 'hero':
                toggleSectionFields('heroFields', true);
                document.getElementById('heroTitle').value = content.title || '';
                document.getElementById('heroSubtitle').value = content.subtitle || '';
                document.getElementById('heroParagraphs').value = content.paragraphs || '';
                document.getElementById('heroBadgeText').value = content.badge_text || '';
                document.getElementById('heroButtonText').value = content.button_text || '';
                document.getElementById('heroButtonUrl').value = content.button_url || '';
                document.getElementById('heroImage').value = content.image || '';
                document.getElementById('heroListItems').value = Array.isArray(content.list_items) ? content.list_items.join(', ') : (content.list_items || '');
                break;
                
            case 'stats':
                toggleSectionFields('statsFields', true);
                document.getElementById('statNumber').value = content.stat_number || '';
                document.getElementById('statLabel').value = content.stat_label || '';
                document.getElementById('statSuffix').value = content.suffix || '';
                break;
                
            case 'features_header':
                toggleSectionFields('featuresHeaderFields', true);
                document.getElementById('featuresHeaderTitle').value = content.title || '';
                document.getElementById('featuresHeaderSubtitle').value = content.subtitle || '';
                document.getElementById('featuresHeaderDescription').value = content.description || '';
                document.getElementById('featuresHeaderParagraph').value = content.paragraphs || '';
                break;
            case 'features':
                toggleSectionFields('featuresFields', true);
                document.getElementById('featureSubtitle').value = content.subtitle || '';
                document.getElementById('featureParagraphs').value = content.paragraphs || '';
                document.getElementById('featureIconSvg').value = content.icon_svg || '';
                document.getElementById('featureIconClass').value = content.icon_class || '';
                break;
                
            case 'overview':
                toggleSectionFields('overviewFields', true);
                document.getElementById('overviewTitle').value = content.title || '';
                document.getElementById('overviewParagraphs').value = content.paragraphs || '';
                document.getElementById('overviewImage').value = content.image || '';
                document.getElementById('overviewListItems').value = Array.isArray(content.list_items) ? content.list_items.join(', ') : (content.list_items || '');
                document.getElementById('overviewButtonText').value = content.button_text || '';
                document.getElementById('overviewButtonUrl').value = content.button_url || '';
                break;
                
            case 'faq':
                toggleSectionFields('faqFields', true);
                document.getElementById('faqQuestion').value = content.question || '';
                document.getElementById('faqAnswer').value = content.answer || '';
                break;
                
            case 'cta':
                toggleSectionFields('ctaFields', true);
                document.getElementById('ctaTitle').value = content.title || '';
                document.getElementById('ctaSubtitle').value = content.subtitle || '';
                document.getElementById('ctaButtonText').value = content.button_text || '';
                document.getElementById('ctaButtonUrl').value = content.button_url || '';
                break;
        }

    }

    function openCreateModal() {
        const form = document.getElementById('editForm');
        form.action = '{{ route('admin.store-warehousing-solutions-content') }}';
        document.getElementById('contentId').value = '';
        document.getElementById('section').value = 'hero';
        document.getElementById('itemKey').value = '';
        document.getElementById('sortOrder').value = '';
        document.getElementById('isActive').checked = true;

        hideAllSectionFields();

        document.querySelectorAll('#editForm input, #editForm textarea').forEach(el => {
            if (el.id !== 'contentId' && el.type !== 'hidden' && el.type !== 'checkbox') {
                el.value = '';
            }
        });
        toggleSectionFields('heroFields', true);
        document.getElementById('section').dispatchEvent(new Event('change'));

        const modalEl = document.getElementById('editModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    document.getElementById('section').addEventListener('change', function() {
        const section = this.value;
        
        hideAllSectionFields();
        
        // Show relevant fields
        switch(section) {
            case 'hero':
                toggleSectionFields('heroFields', true);
                break;
            case 'stats':
                toggleSectionFields('statsFields', true);
                break;
            case 'features_header':
                toggleSectionFields('featuresHeaderFields', true);
                break;
            case 'features':
                toggleSectionFields('featuresFields', true);
                break;
            case 'overview':
                toggleSectionFields('overviewFields', true);
                break;
            case 'faq':
                toggleSectionFields('faqFields', true);
                break;
            case 'cta':
                toggleSectionFields('ctaFields', true);
                break;
        }
    });

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('contentId').value;
        
        let formData;
        try {
            formData = buildSectionFormData(this);
        } catch (e) {
            // If form data building fails, abort submission
            return;
        }
        
        // Determine the correct endpoint based on whether this is a create or update
        let url = id ? `${BASE_URL}/admin/update-warehousing-solutions-content/${id}` : `${BASE_URL}/admin/store-warehousing-solutions-content`;
        
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

    function getSectionFieldIds() {
        return ['heroFields','statsFields','featuresFields','featuresHeaderFields','overviewFields','faqFields','ctaFields'];
    }

    function getFieldIdForSection(section) {
        const sectionFields = {
            hero: 'heroFields',
            stats: 'statsFields',
            features_header: 'featuresHeaderFields',
            features: 'featuresFields',
            overview: 'overviewFields',
            faq: 'faqFields',
            cta: 'ctaFields',
        };

        return sectionFields[section] || null;
    }

    function hideAllSectionFields() {
        getSectionFieldIds().forEach(fieldId => toggleSectionFields(fieldId, false));
    }

    function showSectionFields(section) {
        hideAllSectionFields();
        switch(section) {
            case 'hero':
                toggleSectionFields('heroFields', true);
                break;
            case 'stats':
                toggleSectionFields('statsFields', true);
                break;
            case 'features_header':
                toggleSectionFields('featuresHeaderFields', true);
                break;
            case 'features':
                toggleSectionFields('featuresFields', true);
                break;
            case 'overview':
                toggleSectionFields('overviewFields', true);
                break;
            case 'faq':
                toggleSectionFields('faqFields', true);
                break;
            case 'cta':
                toggleSectionFields('ctaFields', true);
                break;
        }
    }

    function toggleSectionFields(fieldId, show) {
        document.getElementById(fieldId).style.display = show ? 'block' : 'none';
    }

    function buildSectionFormData(form) {
        const formData = new FormData();
        const section = document.getElementById('section').value;
        const activeFieldId = getFieldIdForSection(section);

        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', document.getElementById('contentId').value);
        formData.append('section', section);
        formData.append('item_key', document.getElementById('itemKey').value);
        formData.append('sort_order', document.getElementById('sortOrder').value);

        if (document.getElementById('isActive').checked) {
            formData.append('is_active', '1');
        }

        if (activeFieldId) {
            document.querySelectorAll(`#${activeFieldId} input, #${activeFieldId} textarea, #${activeFieldId} select`).forEach(el => {
                if (el.name && !el.disabled) {
                    formData.append(el.name, el.value);
                }
            });
        }

        return formData;
    }

    // ---- Image upload handling ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-upload-image');
        if (!btn) return;
        const targetId = btn.getAttribute('data-target');
        const fileInput = document.querySelector(`.image-file-input[data-target="${targetId}"]`);
        if (fileInput) fileInput.click();
    });

    document.addEventListener('change', function (e) {
        const fileInput = e.target.closest('.image-file-input');
        if (!fileInput || !fileInput.files || !fileInput.files[0]) return;

        const targetId = fileInput.getAttribute('data-target');
        const previewId = fileInput.getAttribute('data-preview');
        const targetInput = document.getElementById(targetId);
        const previewImg = document.getElementById(previewId);
        const btn = document.querySelector(`.btn-upload-image[data-target="${targetId}"]`);
        const originalBtnHtml = btn ? btn.innerHTML : '';

        const fd = new FormData();
        fd.append('upload', fileInput.files[0]);
        fd.append('_token', '{{ csrf_token() }}');

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="ti ti-loader-2 ti-spin"></i> Uploading...';
        }

        fetch(`${BASE_URL}/admin/upload-warehousing-image`, {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.uploaded && data.url) {
                if (targetInput) targetInput.value = data.url;
                if (previewImg) {
                    previewImg.src = resolveImageUrl(data.url);
                    previewImg.style.display = 'block';
                }
            } else {
                showAlert('Upload failed: ' + ((data.error && data.error.message) || data.message || 'Unknown error'), 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('An error occurred while uploading the image.', 'error');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
            }
            fileInput.value = '';
        });
    });

    // Resolve a stored image path (relative or absolute) to a full URL for preview.
    // Stored values are relative to public/ (e.g. "assets/images/photo.jpg").
    function resolveImageUrl(path) {
        if (!path) return '';
        let src = String(path).trim();
        if (!src) return '';
        if (/^https?:\/\//i.test(src) || src.startsWith('//')) return src;
        if (src.startsWith('/')) return BASE_URL + src;
        return BASE_URL + '/' + src;
    }

    // Show preview for existing image paths on edit
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.image-file-input').forEach(fi => {
            const targetId = fi.getAttribute('data-target');
            const previewId = fi.getAttribute('data-preview');
            const targetInput = document.getElementById(targetId);
            const previewImg = document.getElementById(previewId);
            if (targetInput && previewImg && targetInput.value) {
                previewImg.src = resolveImageUrl(targetInput.value);
                previewImg.style.display = 'block';
            }
        });
    });

    function deleteContent(id) {
        if (confirm('Are you sure you want to delete this content?')) {
            fetch(`${BASE_URL}/admin/delete-warehousing-solutions-content/${id}`, {
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
