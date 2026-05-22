<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Change Volumetric Calculator Page | CRMS - Advanced Bootstrap 5 Admin Template for Customer Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage volumetric calculator page content for the leadCRM website.">
    <meta name="keywords" content="volumetric calculator, admin, CRM, content management">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('lea/img/apple-icon.png') }}">

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
                        <h4 class="mb-1">Change Volumetric Calculator Page</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                
                <!-- End Page Header -->

                <!-- Volumetric Calculator Page Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Volumetric Calculator Page Content Management</h5>
                                <p class="card-text">View and Edit all Volumetric Calculator page content sections</p>
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
                                    <table class="table table-hover" id="volumetricCalculatorContentTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Section</th>
                                                <th>Content Preview</th>
                                                <th>Sort Order</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($volumetricCalculatorContent as $content)
                                                <tr>
                                                    <td>{{ $content->id }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $content->section }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="content-preview">
                                                            @php
                                                                $data = $content->data;
                                                            @endphp
                                                            @switch($content->section)
                                                                @case('hero')
                                                                    <strong>{{ $data['title'] ?? '' }}</strong><br>
                                                                    <small>{{ Illuminate\Support\Str::limit($data['description'] ?? '', 60) }}</small>
                                                                    @break
                                                                @case('features_header')
                                                                    <strong>{{ $data['title'] ?? '' }}</strong><br>
                                                                    <small>{{ Illuminate\Support\Str::limit($data['description'] ?? '', 60) }}</small>
                                                                    @break
                                                                @case('features')
                                                                    <strong>{{ $data['title'] ?? '' }}</strong><br>
                                                                    <small>{{ Illuminate\Support\Str::limit($data['description'] ?? '', 60) }}</small>
                                                                    @break
                                                                @case('track_cta')
                                                                    <strong>{{ $data['title'] ?? '' }}</strong><br>
                                                                    <small>{{ Illuminate\Support\Str::limit($data['description'] ?? '', 60) }}</small>
                                                                    @break
                                                                @case('faq_sidebar')
                                                                    <strong>{{ $data['title'] ?? '' }}</strong><br>
                                                                    <small>{{ Illuminate\Support\Str::limit($data['description'] ?? '', 60) }}</small>
                                                                    @break
                                                                @case('faq')
                                                                    <strong>{{ $data['question'] ?? '' }}</strong><br>
                                                                    <small>{{ Illuminate\Support\Str::limit($data['answer'] ?? '', 60) }}</small>
                                                                    @break
                                                                @case('calculator')
                                                                    @if(isset($data['divisor_options']))
                                                                        <strong>{{ count($data['divisor_options']) }} divisor options</strong>
                                                                    @else
                                                                        <small>{{ Illuminate\Support\Str::limit(json_encode($data), 60) }}</small>
                                                                    @endif
                                                                    @break
                                                                @default
                                                                    <small>{{ Illuminate\Support\Str::limit(json_encode($data), 60) }}</small>
                                                            @endswitch
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
                                                            <button type="button" class="btn btn-sm btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editContent({{ $content->id }}, '{{ $content->section }}', '{{ addslashes(json_encode($data)) }}', {{ $content->sort_order }}, {{ $content->is_active ? 1 : 0 }})">
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
                                                        <p class="text-muted">No volumetric calculator content found.</p>
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
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Volumetric Calculator Content</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    <input type="hidden" id="contentId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="section" class="form-label">Section</label>
                                    <select class="form-control" id="section" name="section" required>
                                        <option value="hero">Hero</option>
                                        <option value="features_header">Features Header</option>
                                        <option value="features">Features</option>
                                        <option value="track_cta">Track CTA</option>
                                        <option value="faq_sidebar">FAQ Sidebar</option>
                                        <option value="faq">FAQ</option>
                                        <option value="calculator">Calculator</option>
                                    </select>
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
                                <div id="heroFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="heroBadgeText" class="form-label">Badge Text</label>
                                        <input type="text" class="form-control" id="heroBadgeText" name="content[badge_text]" placeholder="Free Tool · Instant Results">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="heroTitle" name="content[title]" placeholder="Weight Calculator">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="heroDescription" name="content[description]" rows="3" placeholder="Enter package dimensions..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonText" class="form-label">Button Text</label>
                                        <input type="text" class="form-control" id="heroButtonText" name="content[button_text]" placeholder="Calculate Now">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonUrl" class="form-label">Button URL</label>
                                        <input type="text" class="form-control" id="heroButtonUrl" name="content[button_url]" placeholder="#calculator">
                                    </div>
                                </div>

                                <div id="featuresHeaderFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="featuresHeaderTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="featuresHeaderTitle" name="content[title]" placeholder="Understanding volumetric weight">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featuresHeaderDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="featuresHeaderDescription" name="content[description]" rows="3" placeholder="Carriers use dimensional weight to price large, light packages..."></textarea>
                                    </div>
                                </div>

                                <div id="featuresFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="featureIconClass" class="form-label">Icon Class</label>
                                        <input type="text" class="form-control" id="featureIconClass" name="content[icon_class]" placeholder="fa-solid fa-ruler">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featureTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="featureTitle" name="content[title]" placeholder="Measure all three dimensions">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featureDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="featureDescription" name="content[description]" rows="3" placeholder="Use a tape measure..."></textarea>
                                    </div>
                                </div>

                                <div id="trackCtaFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="trackLiveBadge" class="form-label">Live Badge</label>
                                        <input type="text" class="form-control" id="trackLiveBadge" name="content[live_badge]" placeholder="● LIVE TRACKING">
                                    </div>
                                    <div class="mb-3">
                                        <label for="trackTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="trackTitle" name="content[title]" placeholder="Track any shipment in real-time">
                                    </div>
                                    <div class="mb-3">
                                        <label for="trackDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="trackDescription" name="content[description]" rows="3" placeholder="Get live updates across all major carriers..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="trackButtonText" class="form-label">Button Text</label>
                                        <input type="text" class="form-control" id="trackButtonText" name="content[button_text]" placeholder="Track Shipment →">
                                    </div>
                                    <div class="mb-3">
                                        <label for="trackButtonUrl" class="form-label">Button URL</label>
                                        <input type="text" class="form-control" id="trackButtonUrl" name="content[button_url]" placeholder="#">
                                    </div>
                                </div>

                                <div id="faqSidebarFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="faqSidebarIconImage" class="form-label">Icon Image</label>
                                        <input type="text" class="form-control" id="faqSidebarIconImage" name="content[icon_image]" placeholder="https://...">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqSidebarTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="faqSidebarTitle" name="content[title]" placeholder="Need personalized help?">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqSidebarDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="faqSidebarDescription" name="content[description]" rows="3" placeholder="Our logistics experts are available 24/7..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqSidebarButtonText" class="form-label">Button Text</label>
                                        <input type="text" class="form-control" id="faqSidebarButtonText" name="content[button_text]" placeholder="Message Support">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqSidebarButtonUrl" class="form-label">Button URL</label>
                                        <input type="text" class="form-control" id="faqSidebarButtonUrl" name="content[button_url]" placeholder="#">
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

                                <div id="calculatorFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="calculatorJson" class="form-label">Calculator Data (JSON)</label>
                                        <textarea class="form-control" id="calculatorJson" name="content[json]" rows="8" placeholder='{"divisor_options":[{"value":"5000","text":"5000 Air","width":"105px"}]}'></textarea>
                                        <div class="form-text">Edit the calculator JSON directly for divisor options or other complex data.</div>
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
        $('#volumetricCalculatorContentTable').DataTable();
        hideAllVolumetricFields();
    });

    function toggleSectionFields(containerId, show) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.style.display = show ? 'block' : 'none';
        container.querySelectorAll('input, textarea, select').forEach(el => {
            el.disabled = !show;
        });
    }

    function hideAllVolumetricFields() {
        const groups = [
            'heroFields',
            'featuresHeaderFields',
            'featuresFields',
            'trackCtaFields',
            'faqSidebarFields',
            'faqFields',
            'calculatorFields'
        ];
        groups.forEach(groupId => toggleSectionFields(groupId, false));
    }

    function editContent(id, section, contentJson, sortOrder, isActive) {
        const content = JSON.parse(contentJson);

        document.getElementById('contentId').value = id;
        document.getElementById('section').value = section;
        document.getElementById('sortOrder').value = sortOrder;
        document.getElementById('isActive').checked = isActive == 1;

        hideAllVolumetricFields();

        switch(section) {
            case 'hero':
                toggleSectionFields('heroFields', true);
                document.getElementById('heroBadgeText').value = content.badge_text || '';
                document.getElementById('heroTitle').value = content.title || '';
                document.getElementById('heroDescription').value = content.description || '';
                document.getElementById('heroButtonText').value = content.button_text || '';
                document.getElementById('heroButtonUrl').value = content.button_url || '';
                break;
            case 'features_header':
                toggleSectionFields('featuresHeaderFields', true);
                document.getElementById('featuresHeaderTitle').value = content.title || '';
                document.getElementById('featuresHeaderDescription').value = content.description || '';
                break;
            case 'features':
                toggleSectionFields('featuresFields', true);
                document.getElementById('featureIconClass').value = content.icon_class || '';
                document.getElementById('featureTitle').value = content.title || '';
                document.getElementById('featureDescription').value = content.description || '';
                break;
            case 'track_cta':
                toggleSectionFields('trackCtaFields', true);
                document.getElementById('trackLiveBadge').value = content.live_badge || '';
                document.getElementById('trackTitle').value = content.title || '';
                document.getElementById('trackDescription').value = content.description || '';
                document.getElementById('trackButtonText').value = content.button_text || '';
                document.getElementById('trackButtonUrl').value = content.button_url || '';
                break;
            case 'faq_sidebar':
                toggleSectionFields('faqSidebarFields', true);
                document.getElementById('faqSidebarIconImage').value = content.icon_image || '';
                document.getElementById('faqSidebarTitle').value = content.title || '';
                document.getElementById('faqSidebarDescription').value = content.description || '';
                document.getElementById('faqSidebarButtonText').value = content.button_text || '';
                document.getElementById('faqSidebarButtonUrl').value = content.button_url || '';
                break;
            case 'faq':
                toggleSectionFields('faqFields', true);
                document.getElementById('faqQuestion').value = content.question || '';
                document.getElementById('faqAnswer').value = content.answer || '';
                break;
            case 'calculator':
                toggleSectionFields('calculatorFields', true);
                document.getElementById('calculatorJson').value = JSON.stringify(content, null, 2);
                break;
            default:
                toggleSectionFields('calculatorFields', true);
                document.getElementById('calculatorJson').value = JSON.stringify(content, null, 2);
                break;
        }
    }

    document.getElementById('section').addEventListener('change', function() {
        const section = this.value;
        hideAllVolumetricFields();

        switch(section) {
            case 'hero':
                toggleSectionFields('heroFields', true);
                break;
            case 'features_header':
                toggleSectionFields('featuresHeaderFields', true);
                break;
            case 'features':
                toggleSectionFields('featuresFields', true);
                break;
            case 'track_cta':
                toggleSectionFields('trackCtaFields', true);
                break;
            case 'faq_sidebar':
                toggleSectionFields('faqSidebarFields', true);
                break;
            case 'faq':
                toggleSectionFields('faqFields', true);
                break;
            case 'calculator':
                toggleSectionFields('calculatorFields', true);
                break;
        }
    });

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('contentId').value;
        const formData = new FormData(this);

        fetch(`/admin/update-volumetric-calculator-content/${id}`, {
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
            fetch(`/admin/delete-volumetric-calculator-content/${id}`, {
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
