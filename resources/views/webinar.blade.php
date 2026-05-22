@include('website_include.header')

<script src="https://unpkg.com/lucide@latest"></script>
<style>
.uwd-search-wrapper {
            padding: 3px 0 10px;
        }

        .uwd-search-bar {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px; 
            padding: 8px 18px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            max-width: 800px;
            margin: 0 auto;
        }

        .uwd-search-input {
            flex-grow: 1;
            border: none;
            outline: none;
            font-size: 1rem;
            padding: 10px 0;
            background: transparent;
            color: #1e293b;
        }

        .uwd-search-input::placeholder {
            color: #94a3b8;
        }

        /* Vertical Divider */
        .uwd-search-divider {
            width: 1px;
            height: 24px;
            background-color: #e2e8f0;
            margin: 0 20px;
        }

        /* Category Dropdown Styling */
        .uwd-category-dropdown {
            border: none;
            background: transparent;
            font-size: 0.95rem;
            color: #1e293b;
            font-weight: 500;
            cursor: pointer;
            outline: none;
            appearance: none; /* Hide default arrow */
            padding-right: 2px;
            padding-left: 2px;
            position: relative;
        }

        @media (max-width: 480px) {
            .uwd-category-wrapper {
                left: -50px;
            }
            }

        .uwd-category-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .uwd-chevron-icon {
            position: absolute;
            right: 0;
            pointer-events: none;
            color: #64748b;
        }



          /* --- TABS SYSTEM --- */
        .uwd-tabs-container {
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .uwd-tab-btn {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 10px 24px;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--uwd-text-muted);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .uwd-tab-btn:hover {
            border-color: var(--uwd-primary);
            color: var(--uwd-primary);
        }

        .uwd-tab-btn.active {
            background: linear-gradient(to right, #2563eb, #9333ea);
            border-color: var(--uwd-primary);
            color: white;
            box-shadow: 0 4px 12px rgba(37,99, 235, 0.2);
        }

        /* --- BLOG CARD STYLING --- */
        .uwd-blog-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #edf2f7;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }

        .uwd-blog-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        .uwd-card-image-wrapper {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .uwd-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .uwd-blog-card:hover .uwd-card-img {
            transform: scale(1.08);
        }

        .uwd-card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .uwd-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .uwd-category-tag {
            background: #ecf5ff;
            color: #475569;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .uwd-read-time {
            font-size: 0.8rem;
            color: var(--uwd-text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .uwd-blog-title {
            font-size: 20px;
            font-weight: 500;
            line-height: 1.4;
            margin-bottom: 12px;
            color: #0f172a;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }

        .uwd-explore-link {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: auto;
            margin-bottom: 24px;
        }

        .uwd-card-divider {
            height: 1px;
            background-color: #f1f5f9;
            margin-bottom: 20px;
        }

        .uwd-author-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .uwd-author-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .uwd-author-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            background: #e2e8f0;
        }

        .uwd-author-name {
            font-size: 0.85rem;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
        }

        .uwd-author-role {
            font-size: 0.7rem;
            color: var(--uwd-text-muted);
            margin: 0;
        }

        .uwd-author-role span { color: var(--uwd-primary); }

        .uwd-publish-date {
            font-size: 0.75rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Animation for card filtering */
        .blog-item {
            transition: all 0.4s ease;
        }
        
        .blog-item.hidden {
            display: none;
            opacity: 0;
            transform: scale(0.9);
        }

        /* --- PLAY OVERLAY FOR YOUTUBE CARDS --- */
        /* .uwd-play-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.35);
            transition: background 0.3s ease;
        } */

        .uwd-blog-card:hover .uwd-play-overlay {
            background: rgba(0,0,0,0.5);
        }

        .uwd-play-btn {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: rgba(255,0,0,0.85);
            display: flex;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease, background 0.3s ease;
            box-shadow: 0 4px 16px rgba(255,0,0,0.4);
        }

        .uwd-blog-card:hover .uwd-play-btn {
            transform: scale(1.1);
            background: #ff0000;
        }

</style>


@php
    $heroBadge = $heroContent->content['badge'] ?? 'Webinars';
    $heroTitle = $heroContent->content['title'] ?? 'Join Our Webinars Made <span class="moving-gradient-text">Just for Exporters.</span>';
    $heroDescription = $heroContent->content['description'] ?? 'Explore upcoming live sessions and watch past webinars on international shipping, ecommerce logistics, and exporter growth.';
@endphp

<!-- Hero section -->
<header style="min-height: 20vh;" class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;">
    </div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <div class="col-lg-12 text-center animate__animated animate__fadeInLeft">
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    {{ $heroBadge }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $heroTitle !!}
                </h1>
                <p style="max-width: 100%;" class="text-center mb-5 lead">
                    {{ $heroDescription }}
                </p>
            </div>

        </div>
    </div>
</header>


    <!-- Blog Grid Section -->
    <div class="container my-5">
        <div class="row g-4" id="blogGrid">


            @forelse($webinars as $webinar)
            @php
                $content = $webinar->content ?? [];
                $catTag = $content['category_tag'] ?? 'Webinar';
                $readTime = $content['read_time'] ?? '30 min session';
                $authorName = $content['author_name'] ?? 'Speaker';
                $authorRole = $content['author_role'] ?? 'Expert';
                $authorCompany = $content['author_company'] ?? 'UWD';
                $publishDate = $content['publish_date'] ?? 'TBD';
                $linkUrl = $webinar->link ?: '#';
                $linkText = $content['link_text'] ?? 'View Webinar';

                // Extract YouTube video ID from link
                $youtubeId = '';
                if ($linkUrl && $linkUrl !== '#') {
                    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $linkUrl, $matches);
                    $youtubeId = $matches[1] ?? '';
                }
                $cardImg = $youtubeId ? 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg' : ($webinar->image ? asset($webinar->image) : 'https://images.unsplash.com/photo-1591115765373-5207764f72e7?auto=format&fit=crop&q=80&w=800');
            @endphp
            <div class="col-lg-4 col-md-6 blog-item" data-category="webinars">
                <div class="uwd-blog-card">
                    <div class="uwd-card-image-wrapper">
                        <img src="{{ $cardImg }}" class="uwd-card-img" alt="{{ $webinar->title }}">
                        @if($youtubeId)
                        <div class="uwd-play-overlay">
                            <div class="uwd-play-btn"><i data-lucide="play" style="width: 28px; height: 28px; fill: #fff;"></i></div>
                        </div>
                        @endif
                    </div>
                    <div class="uwd-card-body">
                        <div class="uwd-card-meta">
                            <!-- <span class="uwd-category-tag">{{ $catTag }}</span> -->
                            <span class="uwd-category-tag">Recording<!--  --></span>
                            <div class="uwd-read-time"><i data-lucide="clock" style="width: 14px;"></i> {{ $readTime }}</div>
                        </div>
                        <h3 class="uwd-blog-title">{{ $webinar->title }}</h3>
                        <a href="javascript:void(0);" class="uwd-explore-link show-webinar-btn"
                           data-title="{{ $webinar->title }}"
                           data-video-url="{{ $linkUrl }}"
                           data-bs-toggle="modal"
                           data-bs-target="#webinarVideoModal">
                            Show Webinar<i data-lucide="arrow-right" style="width: 16px;"></i>
                        </a>

                        <div class="uwd-card-divider"></div>
                        <div class="uwd-author-row">
                            <div class="uwd-author-info">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($authorName) }}&background=random" class="uwd-author-img">
                                <div>
                                    <p class="uwd-author-name">{{ $authorName }}</p>
                                    <p class="uwd-author-role">{{ $authorRole }} @ <span>{{ $authorCompany }}</span></p>
                                </div>
                            </div>
                            <div class="uwd-publish-date">{{ $publishDate }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="alert alert-info">
                    <i class="ti ti-video-off me-2"></i>
                    No webinars are currently available. Please check back later.
                </div>
            </div>
            @endforelse

        </div>
    </div>

    <!-- ============================================= -->
    <!-- Webinar Video Modal (YouTube only popup) -->
    <!-- ============================================= -->
    <div class="modal fade" id="webinarVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-none" style="border-radius: 8px; overflow: hidden;">
                <div class="position-relative bg-black">
                    <!-- Close button overlaid on the video -->
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" style="z-index: 10; opacity: 0.8; filter: drop-shadow(0 0 4px rgba(0,0,0,0.5));" data-bs-dismiss="modal" aria-label="Close"></button>
                    <!-- Video Player (full width, no extra padding) -->
                    <div class="ratio ratio-16x9" style="min-height: 350px;">
                        <iframe id="webinarVideoIframe"
                                src=""
                                allow="autoplay; encrypted-media; fullscreen"
                                allowfullscreen
                                style="border: none;">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // =============================================
    // Webinar Video Modal Handler (YouTube only popup)
    // =============================================
    document.addEventListener('DOMContentLoaded', function() {
        var iframe = document.getElementById('webinarVideoIframe');

        document.querySelectorAll('.show-webinar-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var videoUrl = this.getAttribute('data-video-url');
                if (videoUrl && videoUrl !== '#') {
                    iframe.src = videoUrl;
                } else {
                    iframe.src = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1';
                }
            });
        });

        var modal = document.getElementById('webinarVideoModal');
        modal.addEventListener('hidden.bs.modal', function() {
            iframe.src = '';
        });
    });
    </script>

@include('website_include.footer')