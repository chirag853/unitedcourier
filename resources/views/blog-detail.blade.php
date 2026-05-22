@include('website_include.header')

<style>
:root {
    --uwd-primary: #2563eb;
    --uwd-primary-dark: #1d4ed8;
    --uwd-secondary: #4f46e5;
    --uwd-text-main: #0f172a;
    --uwd-text-muted: #64748b;
    --uwd-bg: #ffffff;
    --uwd-accent-bg: #f8fbff;
    --uwd-card-border: #e2e8f0;
}

.uwd-article-meta {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--uwd-text-muted);
    font-size: 0.9rem;
}

/* --- CONTENT AREA --- */
.uwd-featured-image {
    width: 100%;
    border-radius: 24px;
    aspect-ratio: 16/7;
    object-fit: cover;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px;
}

.uwd-article-content {
    font-size: 1.1rem;
    color: #334155;
}

.uwd-article-content h2 {
    font-family: 'Outfit', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    margin: 40px 0 20px;
    color: #0f172a;
}

/* --- SIDEBAR CONTACT FORM --- */
.sidebar-sticky {
    position: sticky;
    top: 100px;
}

.contact-card {
    background: #ffffff;
    border: 1px solid var(--uwd-card-border);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
}

.contact-card h4 {
    font-family: 'Outfit', sans-serif;
    font-weight: 700;
    margin-bottom: 10px;
}

.form-control {
    border-radius: 10px;
    padding: 12px 15px;
    border: 1px solid #e2e8f0;
    font-size: 0.95rem;
    margin-bottom: 15px;
}

.form-control:focus {
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    border-color: var(--uwd-primary);
}

.btn-submit {
    background: var(--uwd-primary);
    color: white;
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    font-weight: 700;
    border: none;
    transition: 0.3s;
}

.btn-submit:hover {
    background: var(--uwd-primary-dark);
    transform: translateY(-2px);
}

/* --- MISC UI --- */
.uwd-takeaways {
    background: #f8faff;
    border-left: 4px solid var(--uwd-primary);
    padding: 24px;
    border-radius: 0 16px 16px 0;
    margin: 30px 0;
}

.uwd-highlight-box {
    background: linear-gradient(135deg, #fef9c3, #fef3c7);
    border: 1px solid #fde68a;
    border-radius: 16px;
    padding: 24px;
    margin: 30px 0;
}

.uwd-list-section {
    margin: 30px 0;
}

.uwd-list-section ul {
    list-style: none;
    padding-left: 0;
}

.uwd-list-section ul li {
    padding: 10px 16px;
    margin-bottom: 8px;
    background: #f8fafc;
    border-radius: 10px;
    border-left: 3px solid var(--uwd-primary);
}

#success-message {
    display: none;
    text-align: center;
    padding: 20px;
}

.share-row {
    display: flex;
    gap: 10px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.share-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--uwd-text-muted);
    transition: 0.3s;
    cursor: pointer;
    text-decoration: none;
}

.share-icon:hover {
    background: var(--uwd-primary);
    color: white;
}

.trending-card {
    transition: 0.3s;
    cursor: pointer;
}

.trending-card:hover {
    background: #eef2ff !important;
    transform: translateX(4px);
}

@media (max-width: 991px) {
    .sidebar-sticky {
        position: static;
        margin-top: 50px;
    }
}
</style>

<!-- Hero section -->
<header style="min-height: 20vh;" class="hero-gradient">
    <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;">
    </div>
    <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;">
    </div>

    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <div class="col-lg-12 animate__animated animate__fadeInLeft">
                <div class="gradient-text badge-trusted mb-4 animate-float-loop">
                    <span class="static-dot"></span>
                    {{ $blog->category->name ?? 'Global Logistics' }}
                </div>
                <h1 class="hero-title mb-4">
                    {{ $blog->blog_title }}
                </h1>
                <div class="uwd-article-meta">
                    @if($blog->author_name)
                    <div class="meta-item"><i class="fa-solid fa-user"></i> By {{ $blog->author_name }}</div>
                    @endif
                    @if($blog->created_at)
                    <div class="meta-item"><i class="fa-solid fa-calendar-days"></i>
                        {{ \Carbon\Carbon::parse($blog->created_at)->format('M d, Y') }}</div>
                    @endif
                    @if($blog->sub_heading)
                    <div class="meta-item"><i class="fa-solid fa-quote-right"></i> {{ $blog->sub_heading }}</div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</header>



<main class="container my-5">
    <div class="row">
        <!-- Article Content -->
        <div class="col-lg-8 pe-lg-5">
            @if($blog->master_image)
            <img src="{{ asset($blog->master_image) }}" class="uwd-featured-image"
                alt="{{ $blog->master_image_alt_text ?? $blog->blog_title }}">
            @endif

            <div class="uwd-article-content">
                {{-- Render main blog content --}}
                @if($blog->blog_description)
                {!! $blog->blog_description !!}
                @endif

                {{-- Render sub content if present --}}
                @if($blog->sub_content)
                <div class="uwd-takeaways">
                    @if($blog->sub_heading)
                    <h5 class="fw-bold mb-2">{{ $blog->sub_heading }}</h5>
                    @endif
                    {!! nl2br(e($blog->sub_content)) !!}
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar with Form -->
        <aside class="col-lg-4">
            <div class="sidebar-sticky">
                <div class="contact-card" id="contact-form-container">
                    <div id="main-form">
                        <h4>Scale Your Deliveries</h4>
                        <p class="text-muted small mb-4">Want to sell on Blinkit? We help brands with hub-logistics and
                            international shipping.</p>

                        <form id="blog-sidebar-form">
                            <div class="mb-1">
                                <label class="small fw-bold mb-1">Full Name</label>
                                <input type="text" class="form-control" placeholder="Enter name" required>
                            </div>
                            <div class="mb-1">
                                <label class="small fw-bold mb-1">Work Email</label>
                                <input type="email" class="form-control" placeholder="name@company.com" required>
                            </div>
                            <div class="mb-1">
                                <label class="small fw-bold mb-1">Contact Number</label>
                                <input type="tel" class="form-control" placeholder="+91 XXXX XXX XXX" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold mb-1">Interest</label>
                                <select class="form-control">
                                    <option>Logistics Support</option>
                                    <option>International Shipping</option>
                                    <option>Warehouse Management</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-submit">
                                Get a Free Consultation
                            </button>
                        </form>
                    </div>

                    <!-- Success State -->
                    <div id="success-message">
                        <div class="text-success mb-3">
                            <i class="fa-solid fa-circle-check" style="font-size: 48px;"></i>
                        </div>
                        <h5 class="fw-bold">Request Received!</h5>
                        <p class="text-muted small">Our logistics expert will contact you within 24 hours to discuss
                            your requirements.</p>
                        <button class="btn btn-outline-secondary btn-sm rounded-pill mt-2" onclick="resetForm()">Send
                            another</button>
                    </div>

                    <div class="share-row">
                        <span class="small text-muted align-self-center me-auto">Share:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                            target="_blank" class="share-icon" title="Share on Facebook"><i
                                class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->blog_title) }}"
                            target="_blank" class="share-icon" title="Share on X (Twitter)"><i
                                class="fa-brands fa-x-twitter"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($blog->blog_title) }}"
                            target="_blank" class="share-icon" title="Share on LinkedIn"><i
                                class="fa-brands fa-linkedin-in"></i></a>
                        <a href="https://wa.me/?text={{ urlencode($blog->blog_title . ' ' . url()->current()) }}"
                            target="_blank" class="share-icon" title="Share on WhatsApp"><i
                                class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Trending Blogs Sidebar -->
                @if($trendingBlogs && $trendingBlogs->count() > 0)
                <div class="mt-4 px-2">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-arrow-trend-up text-primary me-2"></i>Trending Now
                    </h6>
                    @foreach($trendingBlogs as $trending)
                    <a href="{{ route('blog.detail', $trending->slug) }}" class="text-decoration-none">
                        <div class="d-flex align-items-start gap-3 bg-light p-3 rounded-4 mb-2 trending-card">
                            @if($trending->master_image)
                            <img src="{{ asset($trending->master_image) }}" alt="{{ $trending->blog_title }}"
                                style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover; flex-shrink: 0;">
                            @else
                            <div
                                style="width: 50px; height: 50px; border-radius: 10px; background: #e2e8f0; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-newspaper text-muted"></i>
                            </div>
                            @endif
                            <div style="flex: 1; min-width: 0;">
                                <h6 class="mb-1 fw-bold"
                                    style="font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $trending->blog_title }}</h6>
                                <p class="small mb-0 text-muted"
                                    style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ Str::limit(strip_tags($trending->blog_description), 60) }}
                                </p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </aside>
    </div>
</main>



@include('website_include.footer')