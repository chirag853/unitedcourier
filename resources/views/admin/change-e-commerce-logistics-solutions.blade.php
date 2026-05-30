<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel | UWC</title>
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
                        <h4 class="mb-1">Change E-Commerce Logistics Solutions</h4>
                    </div>
                    <div class="gap-2 d-flex align-items-center flex-wrap">
                        <!-- <button type="button" class="btn btn-primary" onclick="openCreateModal()"><i class="ti ti-plus me-1"></i> Add New Content</button> -->
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh" onclick="location.reload();"><i class="ti ti-refresh"></i></a>
                        <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                    </div>
                </div>                
                <!-- End Page Header -->

                <!-- E-Commerce Logistics Solutions Content Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">E-Commerce Logistics Solutions Content Management</h5>
                                <p class="card-text">View and Edit all E-Commerce Logistics Solutions page content sections</p>
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
                                    <table class="table table-hover" id="ecommerceContentTable">
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
                                            @forelse($ecommerceContent as $content)
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
                                                            @if($content->section === 'hero')
                                                                <strong>{{ $contentData['title'] ?? '' }}</strong><br>
                                                                <small>{{ \Illuminate\Support\Str::limit($contentData['description'] ?? '', 50) }}</small>
                                                            @elseif($content->section === 'stats')
                                                                <strong>{{ $contentData['value'] ?? '' }}</strong> {{ $contentData['suffix'] ?? '' }}<br>
                                                                <small>{{ $contentData['label'] ?? $contentData['title'] ?? '' }}</small>
                                                            @elseif(in_array($content->section, ['features_header', 'testimonials_header', 'faq_header']))
                                                                <strong>{{ $contentData['title'] ?? '' }}</strong><br>
                                                                <small>{{ \Illuminate\Support\Str::limit($contentData['description'] ?? '', 50) }}</small>
                                                            @elseif($content->section === 'features')
                                                                <strong>{{ $contentData['title'] ?? '' }}</strong><br>
                                                                <small>{{ \Illuminate\Support\Str::limit($contentData['description'] ?? '', 50) }}</small>
                                                            @elseif($content->section === 'overview')
                                                                <strong>{{ $contentData['title'] ?? '' }}</strong><br>
                                                                <small>{{ \Illuminate\Support\Str::limit($contentData['description'] ?? '', 50) }}</small>
                                                            @elseif($content->section === 'testimonials')
                                                                <strong>{{ $contentData['name'] ?? '' }}</strong> ({{ $contentData['rating'] ?? '' }}★)<br>
                                                                <small>{{ \Illuminate\Support\Str::limit($contentData['text'] ?? '', 50) }}</small>
                                                            @elseif($content->section === 'faq')
                                                                <strong>{{ $contentData['question'] ?? '' }}</strong><br>
                                                                <small>{{ \Illuminate\Support\Str::limit($contentData['answer'] ?? '', 50) }}</small>
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
                                                            <button type="button" class="btn btn-sm btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editContent({{ $content->id }})">
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
                                                        <p class="text-muted">No e-commerce logistics solutions content found. The page is empty.</p>
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
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit E-Commerce Logistics Solutions Content</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" action="{{ route('admin.store-e-commerce-logistics-solutions-content') }}" enctype="multipart/form-data">
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
                                        <option value="testimonials_header">Testimonials Header</option>
                                        <option value="testimonials">Testimonials</option>
                                        <option value="faq_header">FAQ Header</option>
                                        <option value="faq">FAQ</option>
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
                                        <label for="heroBadgeText" class="form-label">Badge Text</label>
                                        <input type="text" class="form-control" id="heroBadgeText" name="content[badge_text]" placeholder="E-commerce Logistics Solutions">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="heroTitle" name="content[title]" placeholder="Hero title (HTML allowed)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="heroDescription" name="content[description]" rows="2" placeholder="Hero description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonPrimaryText" class="form-label">Primary Button Text</label>
                                        <input type="text" class="form-control" id="heroButtonPrimaryText" name="content[button_primary_text]" placeholder="Book a Shipping">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonPrimaryIcon" class="form-label">Primary Button Icon</label>
                                        <input type="text" class="form-control" id="heroButtonPrimaryIcon" name="content[button_primary_icon]" placeholder="fa-paper-plane">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonPrimaryUrl" class="form-label">Primary Button URL</label>
                                        <input type="text" class="form-control" id="heroButtonPrimaryUrl" name="content[button_primary_url]" placeholder="# or URL">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonSecondaryText" class="form-label">Secondary Button Text</label>
                                        <input type="text" class="form-control" id="heroButtonSecondaryText" name="content[button_secondary_text]" placeholder="Get a Quote">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonSecondaryIcon" class="form-label">Secondary Button Icon</label>
                                        <input type="text" class="form-control" id="heroButtonSecondaryIcon" name="content[button_secondary_icon]" placeholder="fa-calculator">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroButtonSecondaryUrl" class="form-label">Secondary Button URL</label>
                                        <input type="text" class="form-control" id="heroButtonSecondaryUrl" name="content[button_secondary_url]" placeholder="# or URL">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroImage" class="form-label">Image</label>
                                        <input type="text" class="form-control" id="heroImage" name="content[image]" placeholder="Image path (e.g., images/ecomm-service.webp)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroBadges" class="form-label">Badges (one per line: icon|text)</label>
                                        <textarea class="form-control" id="heroBadges" name="content[badges]" rows="3" placeholder="fa-clock|24–72 Hr Delivery&#10;fa-shield-alt|Fully Insured"></textarea>
                                        <small class="text-muted">Format: icon|text (one per line)</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="heroStatPills" class="form-label">Stat Pills (one per line: icon|value|label|color|text_color)</label>
                                        <textarea class="form-control" id="heroStatPills" name="content[stat_pills]" rows="3" placeholder="fa-box|50K+|Shipments/Month|rgba(...)|var(--primary)"></textarea>
                                        <small class="text-muted">Format: icon|value|label|bg_color|text_color (one per line)</small>
                                    </div>
                                </div>

                                <div id="statsFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="statsTitle" class="form-label">Section Title (for header)</label>
                                        <input type="text" class="form-control" id="statsTitle" name="content[title]" placeholder="Section title (HTML allowed)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="statValue" class="form-label">Value</label>
                                        <input type="text" class="form-control" id="statValue" name="content[value]" placeholder="150">
                                    </div>
                                    <div class="mb-3">
                                        <label for="statSuffix" class="form-label">Suffix</label>
                                        <input type="text" class="form-control" id="statSuffix" name="content[suffix]" placeholder="+">
                                    </div>
                                    <div class="mb-3">
                                        <label for="statLabel" class="form-label">Label</label>
                                        <input type="text" class="form-control" id="statLabel" name="content[label]" placeholder="Cities Covered">
                                    </div>
                                </div>

                                <div id="overviewFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="overviewTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="overviewTitle" name="content[title]" placeholder="Overview title (HTML allowed)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="overviewDescription" name="content[description]" rows="3" placeholder="Overview description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewImage" class="form-label">Image</label>
                                        <input type="text" class="form-control" id="overviewImage" name="content[image]" placeholder="Image path (e.g., images/map-pattern.png)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewButtonText" class="form-label">Button Text</label>
                                        <input type="text" class="form-control" id="overviewButtonText" name="content[button_text]" placeholder="Book Shipments">
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewButtonUrl" class="form-label">Button URL</label>
                                        <input type="text" class="form-control" id="overviewButtonUrl" name="content[button_url]" placeholder="# or URL">
                                    </div>
                                    <div class="mb-3">
                                        <label for="overviewCheckList" class="form-label">Check List Items (one per line)</label>
                                        <textarea class="form-control" id="overviewCheckList" name="content[check_list]" rows="4" placeholder="Priority loading on partner airline...&#10;Full customs brokerage with..."></textarea>
                                        <small class="text-muted">Enter each item on a new line</small>
                                    </div>
                                </div>

                                <div id="featuresHeaderFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="featuresHeaderTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="featuresHeaderTitle" name="content[title]" placeholder="Features header title">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featuresHeaderDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="featuresHeaderDescription" name="content[description]" rows="3" placeholder="Section description"></textarea>
                                    </div>
                                </div>

                                <div id="featuresFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="featureIcon" class="form-label">Icon Class</label>
                                        <input type="text" class="form-control" id="featureIcon" name="content[icon]" placeholder="fa-satellite">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featureColorClass" class="form-label">Color Class</label>
                                        <input type="text" class="form-control" id="featureColorClass" name="content[color_class]" placeholder="fi-blue">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featureTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="featureTitle" name="content[title]" placeholder="Real-Time Tracking">
                                    </div>
                                    <div class="mb-3">
                                        <label for="featureDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="featureDescription" name="content[description]" rows="3" placeholder="Feature description"></textarea>
                                    </div>
                                </div>

                                <div id="testimonialsHeaderFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="testimonialsHeaderTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="testimonialsHeaderTitle" name="content[title]" placeholder="Testimonials header title">
                                    </div>
                                    <div class="mb-3">
                                        <label for="testimonialsHeaderDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="testimonialsHeaderDescription" name="content[description]" rows="3" placeholder="Section description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="testimonialsGoogleReview" class="form-label">Google Review Image</label>
                                        <input type="text" class="form-control" id="testimonialsGoogleReview" name="content[google_review_image]" placeholder="images/google-review.png">
                                    </div>
                                </div>

                                <div id="testimonialsFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="testimonialName" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="testimonialName" name="content[name]" placeholder="Reviewer name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="testimonialAvatar" class="form-label">Avatar</label>
                                        <input type="text" class="form-control" id="testimonialAvatar" name="content[avatar]" placeholder="images/review-1.png">
                                    </div>
                                    <div class="mb-3">
                                        <label for="testimonialRating" class="form-label">Rating</label>
                                        <input type="number" class="form-control" id="testimonialRating" name="content[rating]" placeholder="5" min="1" max="5">
                                    </div>
                                    <div class="mb-3">
                                        <label for="testimonialText" class="form-label">Review Text</label>
                                        <textarea class="form-control" id="testimonialText" name="content[text]" rows="3" placeholder="Review text"></textarea>
                                    </div>
                                </div>

                                <div id="faqHeaderFields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="faqHeaderBadge" class="form-label">Badge</label>
                                        <input type="text" class="form-control" id="faqHeaderBadge" name="content[badge]" placeholder="Common Questions">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqHeaderTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="faqHeaderTitle" name="content[title]" placeholder="Frequently Asked Questions">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqHeaderSidebarImage" class="form-label">Sidebar Image URL</label>
                                        <input type="text" class="form-control" id="faqHeaderSidebarImage" name="content[sidebar_image]" placeholder="Sidebar image URL">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqHeaderSidebarTitle" class="form-label">Sidebar Title</label>
                                        <input type="text" class="form-control" id="faqHeaderSidebarTitle" name="content[sidebar_title]" placeholder="Need personalized help?">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqHeaderSidebarDescription" class="form-label">Sidebar Description</label>
                                        <textarea class="form-control" id="faqHeaderSidebarDescription" name="content[sidebar_description]" rows="2" placeholder="Sidebar description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqHeaderContactBoxTitle" class="form-label">Contact Box Title</label>
                                        <input type="text" class="form-control" id="faqHeaderContactBoxTitle" name="content[contact_box_title]" placeholder="Contact Us">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqHeaderContactBoxDescription" class="form-label">Contact Box Description</label>
                                        <textarea class="form-control" id="faqHeaderContactBoxDescription" name="content[contact_box_description]" rows="2" placeholder="Contact box description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="faqHeaderContactButtonText" class="form-label">Contact Button Text</label>
                                        <input type="text" class="form-control" id="faqHeaderContactButtonText" name="content[contact_button_text]" placeholder="Message Support">
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
        $('#ecommerceContentTable').DataTable();
    } );

    function editContent(id) {
        // Fetch content data via AJAX using only the ID
        fetch(`${BASE_URL}/admin/get-e-commerce-logistics-solutions-content/${id}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('editForm');
            form.action = '{{ route('admin.update-e-commerce-logistics-solutions-content', ['id' => '__ID__']) }}'.replace('__ID__', id);
            
            document.getElementById('contentId').value = data.id;
            document.getElementById('section').value = data.section;
            document.getElementById('itemKey').value = data.item_key;
            document.getElementById('sortOrder').value = data.sort_order;
            document.getElementById('isActive').checked = data.is_active == 1;
            
            const content = data.content || {};
            
            hideAllSectionFields();
            
            // Show relevant fields and populate data
            switch(data.section) {
                case 'hero':
                    toggleSectionFields('heroFields', true);
                    document.getElementById('heroBadgeText').value = content.badge_text || '';
                    document.getElementById('heroTitle').value = content.title || '';
                    document.getElementById('heroDescription').value = content.description || '';
                    document.getElementById('heroButtonPrimaryText').value = content.button_primary_text || '';
                    document.getElementById('heroButtonPrimaryIcon').value = content.button_primary_icon || '';
                    document.getElementById('heroButtonPrimaryUrl').value = content.button_primary_url || '';
                    document.getElementById('heroButtonSecondaryText').value = content.button_secondary_text || '';
                    document.getElementById('heroButtonSecondaryIcon').value = content.button_secondary_icon || '';
                    document.getElementById('heroButtonSecondaryUrl').value = content.button_secondary_url || '';
                    document.getElementById('heroImage').value = content.image || '';
                    if (Array.isArray(content.badges)) {
                        document.getElementById('heroBadges').value = content.badges.map(b => `${b.icon}|${b.text}`).join('\n');
                    } else {
                        document.getElementById('heroBadges').value = content.badges || '';
                    }
                    if (Array.isArray(content.stat_pills)) {
                        document.getElementById('heroStatPills').value = content.stat_pills.map(s => `${s.icon}|${s.value}|${s.label}|${s.color}|${s.text_color}`).join('\n');
                    } else {
                        document.getElementById('heroStatPills').value = content.stat_pills || '';
                    }
                    break;
                    
                case 'stats':
                    toggleSectionFields('statsFields', true);
                    document.getElementById('statsTitle').value = content.title || '';
                    document.getElementById('statValue').value = content.value || '';
                    document.getElementById('statSuffix').value = content.suffix || '';
                    document.getElementById('statLabel').value = content.label || '';
                    break;
                    
                case 'features_header':
                    toggleSectionFields('featuresHeaderFields', true);
                    document.getElementById('featuresHeaderTitle').value = content.title || '';
                    document.getElementById('featuresHeaderDescription').value = content.description || '';
                    break;
                    
                case 'features':
                    toggleSectionFields('featuresFields', true);
                    document.getElementById('featureIcon').value = content.icon || '';
                    document.getElementById('featureColorClass').value = content.color_class || '';
                    document.getElementById('featureTitle').value = content.title || '';
                    document.getElementById('featureDescription').value = content.description || '';
                    break;
                    
                case 'overview':
                    toggleSectionFields('overviewFields', true);
                    document.getElementById('overviewTitle').value = content.title || '';
                    document.getElementById('overviewDescription').value = content.description || '';
                    document.getElementById('overviewImage').value = content.image || '';
                    document.getElementById('overviewButtonText').value = content.button_text || '';
                    document.getElementById('overviewButtonUrl').value = content.button_url || '';
                    if (Array.isArray(content.check_list)) {
                        document.getElementById('overviewCheckList').value = content.check_list.join('\n');
                    } else {
                        document.getElementById('overviewCheckList').value = content.check_list || '';
                    }
                    break;
                    
                case 'testimonials_header':
                    toggleSectionFields('testimonialsHeaderFields', true);
                    document.getElementById('testimonialsHeaderTitle').value = content.title || '';
                    document.getElementById('testimonialsHeaderDescription').value = content.description || '';
                    document.getElementById('testimonialsGoogleReview').value = content.google_review_image || '';
                    break;
                    
                case 'testimonials':
                    toggleSectionFields('testimonialsFields', true);
                    document.getElementById('testimonialName').value = content.name || '';
                    document.getElementById('testimonialAvatar').value = content.avatar || '';
                    document.getElementById('testimonialRating').value = content.rating || '';
                    document.getElementById('testimonialText').value = content.text || '';
                    break;
                    
                case 'faq_header':
                    toggleSectionFields('faqHeaderFields', true);
                    document.getElementById('faqHeaderBadge').value = content.badge || '';
                    document.getElementById('faqHeaderTitle').value = content.title || '';
                    document.getElementById('faqHeaderSidebarImage').value = content.sidebar_image || '';
                    document.getElementById('faqHeaderSidebarTitle').value = content.sidebar_title || '';
                    document.getElementById('faqHeaderSidebarDescription').value = content.sidebar_description || '';
                    document.getElementById('faqHeaderContactBoxTitle').value = content.contact_box_title || '';
                    document.getElementById('faqHeaderContactBoxDescription').value = content.contact_box_description || '';
                    document.getElementById('faqHeaderContactButtonText').value = content.contact_button_text || '';
                    break;
                    
                case 'faq':
                    toggleSectionFields('faqFields', true);
                    document.getElementById('faqQuestion').value = content.question || '';
                    document.getElementById('faqAnswer').value = content.answer || '';
                    break;
            }
        })
        .catch(error => {
            console.error('Error fetching content:', error);
            alert('Failed to load content data.');
        });
    }

    function openCreateModal() {
        const form = document.getElementById('editForm');
        form.action = '{{ route('admin.store-e-commerce-logistics-solutions-content') }}';
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
            case 'testimonials_header':
                toggleSectionFields('testimonialsHeaderFields', true);
                break;
            case 'testimonials':
                toggleSectionFields('testimonialsFields', true);
                break;
            case 'faq_header':
                toggleSectionFields('faqHeaderFields', true);
                break;
            case 'faq':
                toggleSectionFields('faqFields', true);
                break;
        }
    });

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('contentId').value;
        const formData = buildSectionFormData(this);
        
        // Determine the correct endpoint based on whether this is a create or update
        let url = id ? `${BASE_URL}/admin/update-e-commerce-logistics-solutions-content/${id}` : `${BASE_URL}/admin/store-e-commerce-logistics-solutions-content`;
        
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

    function getSectionFieldIds() {
        return ['heroFields','statsFields','featuresFields','featuresHeaderFields','overviewFields','testimonialsFields','testimonialsHeaderFields','faqFields','faqHeaderFields'];
    }

    function getFieldIdForSection(section) {
        const sectionFields = {
            hero: 'heroFields',
            stats: 'statsFields',
            features_header: 'featuresHeaderFields',
            features: 'featuresFields',
            overview: 'overviewFields',
            testimonials_header: 'testimonialsHeaderFields',
            testimonials: 'testimonialsFields',
            faq_header: 'faqHeaderFields',
            faq: 'faqFields',
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
            case 'testimonials_header':
                toggleSectionFields('testimonialsHeaderFields', true);
                break;
            case 'testimonials':
                toggleSectionFields('testimonialsFields', true);
                break;
            case 'faq_header':
                toggleSectionFields('faqHeaderFields', true);
                break;
            case 'faq':
                toggleSectionFields('faqFields', true);
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

    function deleteContent(id) {
        if (confirm('Are you sure you want to delete this content?')) {
            fetch(`${BASE_URL}/admin/delete-e-commerce-logistics-solutions-content/${id}`, {
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