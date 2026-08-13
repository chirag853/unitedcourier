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
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
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

</style>



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
                    {{ $pageMeta->content['badge'] ?? 'Knowledge Base' }}
                </div>
                <h1 class="hero-title mb-4">
                    {!! $pageMeta->content['title'] ?? 'Read Our <span class="moving-gradient-text">Blogs & Articles.</span>' !!}
                </h1>
                <p style="max-width: 100%;" class="text-center mb-5 lead">
                    {{ $pageMeta->content['description'] ?? 'Explore expert perspectives, success stories, and shipping strategies shaping the future of commerce.' }}
                </p>

                <!-- Search Section -->
    <div class="container uwd-search-wrapper">
        <div class="uwd-search-bar">
            <!-- Search Icon -->
            <i class="fa-solid fa-magnifying-glass" style="margin-right: 14px; color: #94a3b8; width: 18px;"></i>
            <!-- Input -->
            <input 
                type="text" 
                class="uwd-search-input" 
                id="blogSearch"
                placeholder="Search for Blogs..."
            >
            
            <!-- Divider -->
            <div class="uwd-search-divider"></div>

            <!-- Category Dropdown -->
            <div class="uwd-category-wrapper">
                <select class="uwd-category-dropdown" id="categorySelect">
                    <option value="">Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down uwd-chevron-icon" style="width: 16px;"></i>
            </div>
        </div>
    </div>


            </div>

        </div>
    </div>
</header>




 <!-- Category Tabs -->
    <div class="container my-5">
        <div class="uwd-tabs-container">
            <button class="uwd-tab-btn active" data-filter="all">All Stories</button>
            @foreach($categories as $cat)
                <button class="uwd-tab-btn" data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
            @endforeach
        </div>
    </div>

    <!-- Blog Grid Section -->
    <div class="container mb-5">
        <div class="row g-4" id="blogGrid">
            
            @forelse($blogs as $blog)
            <div class="col-lg-4 col-md-6 blog-item" data-category="{{ $blog->category?->slug ?? $blog->category }}">
                <div class="uwd-blog-card">
                    <div class="uwd-card-image-wrapper">
                        <img src="{{ asset($blog->master_image) }}" class="uwd-card-img" alt="{{ $blog->master_image_alt_text ?? $blog->blog_title }}">
                    </div>
                    <div class="uwd-card-body">
                        <div class="uwd-card-meta">
                            <span class="uwd-category-tag">{{ $blog->category?->name ?? 'Uncategorized' }}</span>
                            <div class="uwd-read-time"><i data-lucide="clock" style="width: 14px;"></i> {{ $blog->created_at ? \Carbon\Carbon::parse($blog->created_at)->format('M d, Y') : '' }}</div>
                        </div>
                        <h3 class="uwd-blog-title">{{ $blog->blog_title }}</h3>
                        <a href="{{ route('blog.detail', $blog->slug) }}" class="uwd-explore-link">Explore Article <i data-lucide="arrow-right" style="width: 16px;"></i></a>
                        <div class="uwd-card-divider"></div>
                        <div class="uwd-author-row">
                            <div class="uwd-author-info">
                                @if($blog->author_image)
                                <img src="{{ asset($blog->author_image) }}" class="uwd-author-img" alt="{{ $blog->author_name }}">
                                @else
                                <div class="uwd-author-img" style="background: #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-user text-muted"></i>
                                </div>
                                @endif
                                <div>
                                    <p class="uwd-author-name">{{ $blog->author_name ?? 'Unknown' }}</p>
                                    @if($blog->author_description)
                                    <p class="uwd-author-role">{{ $blog->author_description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="uwd-publish-date">{{ \Carbon\Carbon::parse($blog->created_at)->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No blog posts available yet.</p>
            </div>
            @endforelse

        </div>
    </div>



<script>
        lucide.createIcons();

        // Filter Logic
        const tabBtns = document.querySelectorAll('.uwd-tab-btn');
        const blogItems = document.querySelectorAll('.blog-item');
        const searchInput = document.getElementById('blogSearch');
        const categorySelect = document.getElementById('categorySelect');

        function filterBlogs(category) {
            blogItems.forEach(item => {
                const itemCat = item.getAttribute('data-category');
                if (category === 'all' || itemCat === category) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }

        // Tab click event
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // UI update
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Logic
                const filter = btn.getAttribute('data-filter');
                filterBlogs(filter);

                // Sync dropdown
                categorySelect.value = filter === 'all' ? 'all' : filter;
            });
        });

        // Dropdown sync event
        categorySelect.addEventListener('change', (e) => {
            const val = e.target.value;
            
            // Sync tabs
            tabBtns.forEach(btn => {
                if (btn.getAttribute('data-filter') === (val === 'all' ? 'all' : val)) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            filterBlogs(val);
        });

        // Search logic (simple text filter)
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const activeTab = document.querySelector('.uwd-tab-btn.active').getAttribute('data-filter');

            blogItems.forEach(item => {
                const title = item.querySelector('.uwd-blog-title').innerText.toLowerCase();
                const itemCat = item.getAttribute('data-category');
                
                const matchesSearch = title.includes(term);
                const matchesTab = activeTab === 'all' || itemCat === activeTab;

                if (matchesSearch && matchesTab) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    </script>


@include('website_include.footer')