@include('website_include/header'); ?>



<style>
:root {
    --brand-blue-main: #2563eb;
    --brand-purple: #9333ea;
    --text-dark: #1a1a1a;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --bg-light: #f8fafc;
}

.download-container {
    max-width: 1100px;
    margin: 60px auto;
    padding: 0 20px;
}

/* --- Document Cards Layout --- */
.doc-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    padding: 24px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
}

.doc-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.04);
    border-color: var(--brand-blue-main);
}

.doc-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* File Type Badges */
.file-icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
    position: relative;
}

.file-pdf {
    background: #fef2f2;
    color: #ef4444;
    border: 1px solid #fee2e2;
}

.file-xls {
    background: #f0fdf4;
    color: #22c55e;
    border: 1px solid #dcfce7;
}

.file-zip {
    background: #f0f7ff;
    color: #3b82f6;
    border: 1px solid #dbeafe;
}

.file-doc {
    background: #eef2ff;
    color: #6366f1;
    border: 1px solid #e0e7ff;
}

.file-icon i {
    font-size: 20px;
    margin-bottom: 2px;
}

.doc-info {
    display: flex;
    flex-direction: column;
}

.doc-title {
    font-family: 'Outfit', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 4px;
    text-decoration: none;
    transition: color 0.2s ease;
}

.doc-title:hover {
    color: var(--brand-blue-main);
}

.doc-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
}

.dot-divider {
    width: 4px;
    height: 4px;
    background: var(--border-color);
    border-radius: 50%;
}

.status-badge {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 100px;
}

.status-verified {
    background: #ecfdf5;
    color: #10b981;
}

.status-pending {
    background: #fffbeb;
    color: #f59e0b;
}

/* --- Actions --- */
.doc-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-view {
    border: 1px solid var(--border-color);
    background: #ffffff;
    color: var(--text-muted);
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-view:hover {
    border-color: var(--text-dark);
    color: var(--text-dark);
}

.btn-download {
    background: var(--brand-blue-main);
    color: #ffffff;
    border: none;
    padding: 11px 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.btn-download:hover {
    background: var(--brand-purple);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(147, 51, 234, 0.15);
}

@media (max-width: 768px) {
    .doc-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }

    .doc-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>



<!-- Hero section -->
@php
    // Read the normalized columns directly. This avoids depending on the virtual
    // content accessor being present in an older/cached server model deployment.
    $heroBadge = $pageMeta?->badge_text ?: 'Explore All Documents';
    $heroTitle = $pageMeta?->title ?: 'Documents <span class="moving-gradient-text">Download</span>';
    $heroDescription = $pageMeta?->description ?: 'Must-read guides, handpicked for their popularity among global exporters';
    $heroImage = $pageMeta?->hero_image
        ? asset(ltrim($pageMeta->hero_image, '/'))
        : asset('public/website_images/document.webp');
@endphp
<header style="min-height: 70vh; padding-top: 140px; padding-bottom: 50px;" class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;">
    </div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <!-- Left Content -->
            <div class="col-md-6 text-md-start text-center animate__animated animate__fadeInLeft">

                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    {{ $heroBadge }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroTitle !!}
                </h1>
                <p style="max-width: 100%;" class="mb-5 lead">
                    {{ $heroDescription }}
                </p>

            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <div class="hero-graphic">
                    <div class="">
                        <img src="{{ $heroImage }}" alt="Document download" class="img-fluid" style="width:50%">
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

<div class="download-container">

    <!-- Document Stack -->
    <div id="document-stack">

        <div class="row g-4">
            @forelse($documents->whereNull('section') as $doc)
            @php
            $iconClass = match($doc->file_type) {
            'pdf' => 'file-pdf',
            'xls' => 'file-xls',
            'zip' => 'file-zip',
            'doc' => 'file-doc',
            default => 'file-pdf',
            };
            $iconName = match($doc->file_type) {
            'pdf' => 'ph-file-pdf',
            'xls' => 'ph-file-xls',
            'zip' => 'ph-file-archive',
            'doc' => 'ph-file-doc',
            default => 'ph-file-pdf',
            };
            $badgeClass = match($doc->status_badge) {
            'Verified' => 'status-verified',
            'Pending Sign' => 'status-pending',
            default => 'status-verified',
            };
            @endphp
            <div class="col-lg-6">
                <div class="doc-card" data-category="{{ $doc->category }}">
                    <div class="doc-left">
                        <div class="file-icon {{ $iconClass }}">
                            <i class="ph-bold {{ $iconName }}"></i>
                            <span>{{ strtoupper($doc->file_type) }}</span>
                        </div>
                        <div class="doc-info">
                            <a href="{{ $doc->file_url }}" class="doc-title" target="_blank">{{ $doc->title }}</a>
                            <div class="doc-meta">
                                <span>{{ $doc->file_size }}</span>
                                <div class="dot-divider"></div>
                                <span>{{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y') }}</span>
                                <div class="dot-divider"></div>
                                <span class="status-badge {{ $badgeClass }}">{{ $doc->status_badge }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="doc-actions">
                        <a href="{{ $doc->file_url }}" class="btn-download" target="_blank"><i
                                class="ph-bold ph-arrow-down"></i> Download</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <p class="text-muted">No documents available at the moment.</p>
                </div>
            </div>
            @endforelse
        </div>

    </div>

</div>







@include('website_include/footer'); ?>
