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
    .section-card { margin-bottom: 20px; }
    .section-card .card-header { cursor: pointer; user-select: none; }
    .section-card .card-header:hover { background-color: #f8f9fa; }
    .section-card .card-header .collapse-icon { transition: transform 0.2s; }
    .section-card .card-header.collapsed .collapse-icon { transform: rotate(-90deg); }
    .preview-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .preview-sm { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0; }
    .form-section-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .navbar-count { display: inline-flex; align-items: center; justify-content: center; background: #2563eb; color: white; border-radius: 50%; width: 24px; height: 24px; font-size: 12px; font-weight: 600; margin-left: 8px; }
    .card-item { border-left: 3px solid #2563eb; margin-bottom: 12px; padding: 12px; background: #f8fafc; border-radius: 6px; }
    .card-item-header { font-size: 12px; font-weight: 600; color: #0f172a; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
    .faq-item-box { border-left: 3px solid #f59e0b; margin-bottom: 12px; padding: 12px; background: #fffbeb; border-radius: 6px; }
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
                        <h4 class="mb-1">Edit All Partnership Content</h4>
                        <p class="text-muted mb-0">
                            Manage all sections of the Partnership page in one place. Each section is collapsible.
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

                <form id="editAllForm" method="POST">
                    @csrf

                    <!-- ============================================================ -->
                    <!-- 1. HERO SECTION -->
                    <!-- ============================================================ -->
                    <div class="card section-card card">
                        <div class="card-header d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#collapseHero" aria-expanded="true">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-photo me-2 text-primary"></i>Hero Section
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseHero" class="collapse show">
                            <div class="card-body">
                                <input type="hidden" name="hero[id]" value="{{ $hero->id ?? '' }}">

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-section-label">Title (HTML allowed)</label>
                                        <textarea class="form-control" name="hero[content][title]" rows="3">{{ $hero->content['title'] ?? '' }}</textarea>
                                        <small class="text-muted">HTML tags like <br> and <span> are allowed</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-section-label">Description</label>
                                        <textarea class="form-control" name="hero[content][description]" rows="2">{{ $hero->content['description'] ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">CTA Text</label>
                                        <input type="text" class="form-control" name="hero[content][cta_text]" value="{{ $hero->content['cta_text'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">CTA Link</label>
                                        <input type="text" class="form-control" name="hero[content][cta_link]" value="{{ $hero->content['cta_link'] ?? '#' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-section-label">Hero Image Path</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="hero[image]" value="{{ $hero->image ?? '' }}" placeholder="images/partnership.webp">
                                            @if($hero && $hero->image)
                                            <a href="{{ asset($hero->image) }}" target="_blank" class="btn btn-outline-primary"><i class="ti ti-external-link"></i></a>
                                            @endif
                                        </div>
                                        <small class="text-muted">Path relative to public directory (e.g., images/partnership.webp)</small>
                                    </div>
                                </div>

                                @if($hero && $hero->image)
                                <div class="text-center mt-2">
                                    <img src="{{ asset($hero->image) }}" class="preview-img" alt="Hero image">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 2. LOGOS SLIDER -->
                    <!-- ============================================================ -->
                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapseLogos">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-layout-grid me-2 text-success"></i>Logo Slider
                                    <span class="navbar-count">{{ $logos->count() }}</span>
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseLogos" class="collapse">
                            <div class="card-body">
                                @forelse($logos as $index => $logo)
                                <div class="card-item">
                                    <div class="card-item-header">
                                        <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                                        Logo #{{ $loop->iteration }}
                                    </div>
                                    <input type="hidden" name="logos[{{ $index }}][id]" value="{{ $logo->id }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-section-label">Title</label>
                                            <input type="text" class="form-control" name="logos[{{ $index }}][title]" value="{{ $logo->title ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-section-label">Image Path</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="logos[{{ $index }}][image]" value="{{ $logo->image ?? '' }}">
                                                @if($logo->image)
                                                <a href="{{ asset($logo->image) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="ti ti-external-link"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($logo->image)
                                    <img src="{{ asset($logo->image) }}" class="preview-sm mt-1" alt="{{ $logo->title ?? '' }}">
                                    @endif
                                </div>
                                @empty
                                <p class="text-muted">No logos found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 3. PARTNER FORM -->
                    <!-- ============================================================ -->
                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapseForm">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-file-description me-2 text-warning"></i>Partner Form Section
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseForm" class="collapse">
                            <div class="card-body">
                                <input type="hidden" name="partner_form[id]" value="{{ $formSection->id ?? '' }}">

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-section-label">Title (HTML allowed)</label>
                                        <textarea class="form-control" name="partner_form[content][title]" rows="2">{{ $formSection->content['title'] ?? '' }}</textarea>
                                        <small class="text-muted">Example: Partner with <span class="gradient-text">United Couriers</span></small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">First Name Placeholder</label>
                                        <input type="text" class="form-control" name="partner_form[content][first_name_placeholder]" value="{{ $formSection->content['first_name_placeholder'] ?? 'First Name' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Last Name Placeholder</label>
                                        <input type="text" class="form-control" name="partner_form[content][last_name_placeholder]" value="{{ $formSection->content['last_name_placeholder'] ?? 'Last Name' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Email Placeholder</label>
                                        <input type="text" class="form-control" name="partner_form[content][email_placeholder]" value="{{ $formSection->content['email_placeholder'] ?? 'Email' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Phone Placeholder</label>
                                        <input type="text" class="form-control" name="partner_form[content][phone_placeholder]" value="{{ $formSection->content['phone_placeholder'] ?? 'Phone' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Company Placeholder</label>
                                        <input type="text" class="form-control" name="partner_form[content][company_placeholder]" value="{{ $formSection->content['company_placeholder'] ?? 'Company Name' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Message Placeholder</label>
                                        <input type="text" class="form-control" name="partner_form[content][message_placeholder]" value="{{ $formSection->content['message_placeholder'] ?? 'Message' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Button Text</label>
                                        <input type="text" class="form-control" name="partner_form[content][button_text]" value="{{ $formSection->content['button_text'] ?? 'Become a Partner' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 4. ABOUT SECTION -->
                    <!-- ============================================================ -->
                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapseAbout">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-info-circle me-2 text-info"></i>About Section
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseAbout" class="collapse">
                            <div class="card-body">
                                <input type="hidden" name="about[id]" value="{{ $aboutSection->id ?? '' }}">

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-section-label">Title (HTML allowed)</label>
                                        <textarea class="form-control" name="about[content][title]" rows="3">{{ $aboutSection->content['title'] ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-section-label">Description</label>
                                        <textarea class="form-control" name="about[content][description]" rows="4">{{ $aboutSection->content['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 5. FEATURES -->
                    <!-- ============================================================ -->
                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFeatures">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-list-check me-2 text-danger"></i>Features
                                    <span class="navbar-count">{{ $features->count() }}</span>
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseFeatures" class="collapse">
                            <div class="card-body">
                                @forelse($features as $index => $feature)
                                <div class="card-item">
                                    <div class="card-item-header">
                                        <span class="badge bg-danger">{{ $loop->iteration }}</span>
                                        Feature #{{ $loop->iteration }}
                                    </div>
                                    <input type="hidden" name="features[{{ $index }}][id]" value="{{ $feature->id }}">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-section-label">Title</label>
                                            <input type="text" class="form-control" name="features[{{ $index }}][title]" value="{{ $feature->title ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted">No features found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 6. ECOSYSTEM SECTION -->
                    <!-- ============================================================ -->
                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapseEcosystem">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-globe me-2 text-primary"></i>Ecosystem Section
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseEcosystem" class="collapse">
                            <div class="card-body">
                                <input type="hidden" name="ecosystem[id]" value="{{ $ecosystemSection->id ?? '' }}">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Badge Text</label>
                                        <input type="text" class="form-control" name="ecosystem[content][badge]" value="{{ $ecosystemSection->content['badge'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Title</label>
                                        <input type="text" class="form-control" name="ecosystem[content][title]" value="{{ $ecosystemSection->content['title'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-section-label">Description</label>
                                        <textarea class="form-control" name="ecosystem[content][description]" rows="2">{{ $ecosystemSection->content['description'] ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Global Card Title</label>
                                        <input type="text" class="form-control" name="ecosystem[content][global_card_title]" value="{{ $ecosystemSection->content['global_card_title'] ?? 'Worldwide Marketplaces' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Partner Card Title</label>
                                        <input type="text" class="form-control" name="ecosystem[content][partner_card_title]" value="{{ $ecosystemSection->content['partner_card_title'] ?? 'Our Partners' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 7. ECOSYSTEM GLOBAL CARDS (Marketplaces) -->
                    <!-- ============================================================ -->
                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapseEcoGlobal">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-building-store me-2 text-success"></i>Worldwide Marketplaces
                                    <span class="navbar-count">{{ $ecosystemGlobalCards->count() }}</span>
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseEcoGlobal" class="collapse">
                            <div class="card-body">
                                @forelse($ecosystemGlobalCards as $index => $card)
                                <div class="card-item">
                                    <div class="card-item-header">
                                        <span class="badge bg-success">{{ $loop->iteration }}</span>
                                        Global Card #{{ $loop->iteration }}
                                    </div>
                                    <input type="hidden" name="ecosystem_global[{{ $index }}][id]" value="{{ $card->id }}">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-section-label">Image Path</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="ecosystem_global[{{ $index }}][image]" value="{{ $card->image ?? '' }}">
                                                @if($card->image)
                                                <a href="{{ asset($card->image) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="ti ti-external-link"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($card->image)
                                    <img src="{{ asset($card->image) }}" class="preview-sm mt-1" alt="Card img">
                                    @endif
                                </div>
                                @empty
                                <p class="text-muted">No marketplace cards found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 8. ECOSYSTEM PARTNER CARDS -->
                    <!-- ============================================================ -->
                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapseEcoPartner">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-users me-2 text-warning"></i>Our Partners
                                    <span class="navbar-count">{{ $ecosystemPartnerCards->count() }}</span>
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseEcoPartner" class="collapse">
                            <div class="card-body">
                                @forelse($ecosystemPartnerCards as $index => $card)
                                <div class="card-item">
                                    <div class="card-item-header">
                                        <span class="badge bg-warning">{{ $loop->iteration }}</span>
                                        Partner Card #{{ $loop->iteration }}
                                    </div>
                                    <input type="hidden" name="ecosystem_partner[{{ $index }}][id]" value="{{ $card->id }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-section-label">Title</label>
                                            <input type="text" class="form-control" name="ecosystem_partner[{{ $index }}][title]" value="{{ $card->title ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-section-label">Image URL</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="ecosystem_partner[{{ $index }}][image]" value="{{ $card->image ?? '' }}">
                                                @if($card->image)
                                                <a href="{{ $card->image }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="ti ti-external-link"></i></a>
                                                @endif
                                            </div>
                                            <small class="text-muted">Full URL for external partner logos</small>
                                        </div>
                                    </div>
                                    @if($card->image)
                                    <img src="{{ str_starts_with($card->image, 'http') ? $card->image : asset($card->image) }}" class="preview-sm mt-1" alt="Partner img" onerror="this.style.display='none'">
                                    @endif
                                </div>
                                @empty
                                <p class="text-muted">No partner cards found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 9. FAQ SECTION -->
                    <!-- ============================================================ -->
                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFaq">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-question-mark me-2 text-info"></i>FAQ Section
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseFaq" class="collapse">
                            <div class="card-body">
                                <input type="hidden" name="faq[id]" value="{{ $faqSection->id ?? '' }}">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Badge</label>
                                        <input type="text" class="form-control" name="faq[content][badge]" value="{{ $faqSection->content['badge'] ?? 'Common Questions' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Title</label>
                                        <input type="text" class="form-control" name="faq[content][title]" value="{{ $faqSection->content['title'] ?? 'Why Partner with us?' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-section-label">Description</label>
                                        <textarea class="form-control" name="faq[content][description]" rows="2">{{ $faqSection->content['description'] ?? '' }}</textarea>
                                    </div>
                                </div>

                                <h6 class="mt-3 mb-2 fw-bold">Sidebar Section</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-section-label">Sidebar Image URL</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="faq[content][sidebar_image]" value="{{ $faqSection->content['sidebar_image'] ?? '' }}">
                                            @if(isset($faqSection) && isset($faqSection->content) && !empty($faqSection->content['sidebar_image']))
                                            <a href="{{ $faqSection->content['sidebar_image'] }}" target="_blank" class="btn btn-outline-primary"><i class="ti ti-external-link"></i></a>
                                            @endif
                                        </div>
                                        <small class="text-muted">External URL for sidebar GIF/image</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Sidebar Title</label>
                                        <input type="text" class="form-control" name="faq[content][sidebar_title]" value="{{ $faqSection->content['sidebar_title'] ?? 'Need personalized help?' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-section-label">Sidebar Description</label>
                                        <input type="text" class="form-control" name="faq[content][sidebar_description]" value="{{ $faqSection->content['sidebar_description'] ?? '' }}">
                                    </div>
                                </div>

                                <h6 class="mt-3 mb-2 fw-bold">Contact Box</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-section-label">Contact Box Title</label>
                                        <input type="text" class="form-control" name="faq[content][contact_box_title]" value="{{ $faqSection->content['contact_box_title'] ?? 'Contact Us' }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-section-label">Contact Box Description</label>
                                        <input type="text" class="form-control" name="faq[content][contact_box_description]" value="{{ $faqSection->content['contact_box_description'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-section-label">Contact Box Button</label>
                                        <input type="text" class="form-control" name="faq[content][contact_box_button]" value="{{ $faqSection->content['contact_box_button'] ?? 'Message Support' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- 10. FAQ ITEMS -->
                    <!-- ============================================================ -->
                    <div class="card section-card">
                        <div class="card-header d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFaqItems">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-arrows-vertical me-2 text-secondary"></i>FAQ Items (Accordion)
                                    <span class="navbar-count">{{ $faqItems->count() }}</span>
                                </h5>
                            </div>
                            <i class="ti ti-chevron-down collapse-icon"></i>
                        </div>
                        <div id="collapseFaqItems" class="collapse">
                            <div class="card-body">
                                @forelse($faqItems as $index => $faq)
                                <div class="faq-item-box">
                                    <div class="card-item-header">
                                        <span class="badge bg-warning">{{ $loop->iteration }}</span>
                                        FAQ Item #{{ $loop->iteration }}
                                    </div>
                                    <input type="hidden" name="faq_items[{{ $index }}][id]" value="{{ $faq->id }}">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-section-label">Question</label>
                                            <input type="text" class="form-control" name="faq_items[{{ $index }}][question]" value="{{ $faq->question ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-section-label">Answer</label>
                                            <textarea class="form-control" name="faq_items[{{ $index }}][answer]" rows="2">{{ $faq->answer ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted">No FAQ items found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- SUBMIT BUTTON -->
                    <!-- ============================================================ -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="ti ti-device-floppy me-2"></i> Save All Changes
                            </button>
                            <a href="{{ route('admin.change-partnership') }}" class="btn btn-outline-secondary btn-lg px-4 ms-2">
                                <i class="ti ti-x me-1"></i> Cancel
                            </a>
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
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>

    <script>
    document.getElementById('editAllForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(`${BASE_URL}/admin/update-all-partnership`, {
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

    // Ensure all collapse headers toggle properly
    document.querySelectorAll('.section-card .card-header').forEach(header => {
        header.addEventListener('click', function() {
            const icon = this.querySelector('.collapse-icon');
            if (icon) {
                setTimeout(() => {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(-90deg)';
                }, 100);
            }
        });
    });
    </script>
</body>

</html>