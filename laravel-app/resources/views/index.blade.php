@include('partials.header')


<!-- Page Header -->
<div class="page-header">
    <div class="header-title">
        <h1>Good food, between classes.</h1>
        <p>Your campus favorites, just a few taps away.</p>
    </div>
    <button type="button" class="header-tag" onclick="openHelpModal()">Made for campus life ✦</button>
</div>

<!-- Hero Banner -->
<div class="hero-banner" id="hero-banner">
    <!-- Left Navigation Arrow -->
    <button type="button" class="banner-arrow prev" onclick="prevBannerSlide()" aria-label="Previous promo slide">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>

    <div class="banner-content">
        <div class="banner-eyebrow" id="banner-eyebrow">LESS WAITING. MORE LIVING.</div>
        <h2 id="banner-heading">Big appetite.<br>Small student budget.</h2>
        <p id="banner-desc">Skip the queue. Order ahead. Pick up something good.</p>
        <button class="banner-cta" id="banner-cta" type="button" onclick="handleBannerAction()">
            <span id="banner-cta-text">Find your next bite</span> ↗
        </button>
    </div>
    <div class="banner-visual" onclick="nextBannerSlide()" title="Click to switch promo">
        <div class="glow-circle"></div>
        <div class="bowl-svg">
            <svg width="138" height="118" viewBox="0 0 64 64" fill="none">
                <ellipse cx="32" cy="54" rx="20" ry="4" fill="rgba(0,0,0,0.14)"/>
                <circle cx="23" cy="27" r="9" fill="#5dbb47"/>
                <circle cx="33" cy="24" r="10.5" fill="#76d156"/>
                <circle cx="42" cy="28" r="8.5" fill="#5dbb47"/>
                <circle cx="25" cy="25" r="3.6" fill="#e84838"/>
                <circle cx="38" cy="26" r="3.8" fill="#e84838"/>
                <circle cx="31" cy="29" r="2.3" fill="#ffd84d"/>
                <circle cx="35" cy="22" r="2.1" fill="#ffd84d"/>
                <path d="M11 31h42c0 14-9 22.5-21 22.5S11 45 11 31Z" fill="#c7bfd4"/>
                <path d="M11 31h42c0 3.3-1.3 6.4-3.4 9H14.4C12.3 37.4 11 34.3 11 31Z" fill="#d9d2e5"/>
                <rect x="22" y="51" width="20" height="3.5" rx="1.75" fill="#9e95ad"/>
            </svg>
        </div>
    </div>

    <!-- Right Navigation Arrow -->
    <button type="button" class="banner-arrow next" onclick="nextBannerSlide()" aria-label="Next promo slide">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </button>

    <div class="banner-dots">
        <button type="button" class="banner-dot active" onclick="setBannerSlide(0)" aria-label="Slide 1"></button>
        <button type="button" class="banner-dot" onclick="setBannerSlide(1)" aria-label="Slide 2"></button>
        <button type="button" class="banner-dot" onclick="setBannerSlide(2)" aria-label="Slide 3"></button>
    </div>
</div>

<!-- Controls: Search & Sort -->
<div class="controls-row" id="menu-controls">
    <div class="search-container">
        <input class="search-input" id="search-input" type="search" placeholder="Search meals, stalls, or your next craving..." oninput="handleSearchInput()">
        <button type="button" class="clear-search" id="clear-search-btn" onclick="clearSearch()">✕</button>
    </div>
    <select class="sort-select" id="sort-select" onchange="renderProducts()">
        <option value="popular">Popular first</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="fast">Fastest pickup</option>
        <option value="rating">Highest rated</option>
    </select>
</div>

<!-- Categories Filters -->
<div class="categories-pills" id="category-pills">
    <button type="button" class="cat-pill active" data-category="all" onclick="selectCategory('all', this)">✦ All food</button>
    <button type="button" class="cat-pill" data-category="local" onclick="selectCategory('local', this)">🍲 Local favorites</button>
    <button type="button" class="cat-pill" data-category="western" onclick="selectCategory('western', this)">🍔 Western</button>
    <button type="button" class="cat-pill" data-category="healthy" onclick="selectCategory('healthy', this)">🥗 Healthy</button>
    <button type="button" class="cat-pill" data-category="drinks" onclick="selectCategory('drinks', this)">🥤 Drinks</button>
    <button type="button" class="cat-pill" data-category="snacks" onclick="selectCategory('snacks', this)">🥪 Snacks</button>
</div>

<!-- Products List Section -->
<section>
    <div class="section-header">
        <div>
            <h3 id="catalog-heading">Campus crowd-pleasers</h3>
            <p id="catalog-count">6 delicious picks at Main Campus.</p>
        </div>
        <button type="button" class="fresh-picks" onclick="resetFilters()">FRESH PICKS ✓</button>
    </div>

    <div class="products-grid" id="products-grid"></div>
</section>

@include('partials.footer')
