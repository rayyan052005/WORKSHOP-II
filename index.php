<?php
$pageTitle = 'UniFood | Explore Food';
$activePage = 'explore';

// Fetch live menu items from PostgreSQL database if available
$serverMenuItems = [];
try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        getenv('DB_HOST') ?: '127.0.0.1',
        getenv('DB_PORT') ?: '5432',
        getenv('DB_DATABASE') ?: 'unifood'
    );
    $pdo = new PDO($dsn, getenv('DB_USERNAME') ?: 'postgres', getenv('DB_PASSWORD') ?: '123', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2
    ]);
    $stmt = $pdo->query('
        SELECT m.id, m.seller_id, m.name AS title, m.category, m.price, m.prep_mins AS "prepMins",
               m.badge, m.description AS desc, m.is_available AS available,
               COALESCE(m.stock_quantity, 50) AS quantity,
               m.image_url AS "imageUrl",
               s.stall_name AS stall, s.campus_location
        FROM menu_items m
        LEFT JOIN sellers s ON m.seller_id = s.id
        ORDER BY m.id DESC
    ');
    $serverMenuItems = $stmt->fetchAll();

    // Fetch verified vendor stalls for filter pills
    $stmtS = $pdo->query('SELECT id, stall_name, campus_location FROM sellers ORDER BY id ASC');
    $allSellers = $stmtS->fetchAll();
} catch (Throwable $e) {
    $serverMenuItems = [];
    $allSellers = [];
}

require_once 'header.php';
?>
<script>
window.SERVER_MENU_ITEMS = <?= json_encode($serverMenuItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>

<!-- Page Header -->
<div class="page-header">
    <div class="header-title">
        <h1>Good food, between classes.</h1>
        <p>Your campus favorites, just a few taps away.</p>
    </div>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a href="#campus-map-section" class="header-tag" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; background:#e8f4e6; color:#1e3d29; border:1px solid #cce2ca;">🗺️ UTeM Campus Cafe Map ✦</a>
        <button type="button" class="header-tag" onclick="openHelpModal()">Made for campus life ✦</button>
    </div>
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

<!-- ========================================================
     UTEM MAIN CAMPUS INTERACTIVE CAFE MAP SECTION
     ======================================================== -->
<section class="campus-map-section" id="campus-map-section">
    <!-- Map Section Header -->
    <div class="map-section-header">
        <div class="map-header-left">
            <div class="map-badge-icon">🛰️</div>
            <div class="map-title-wrap">
                <h3>UTeM Kampus Induk · Satellite Cafe Map <span style="font-size:11px; font-weight:800; background:#cbe896; color:#1e3d29; padding:2px 8px; border-radius:999px;">SATELLITE LIVE</span></h3>
                <p>Real-time Google Satellite view of UTeM Durian Tunggal. Click any cafe below to zoom into its location.</p>
            </div>
        </div>
        <div class="map-filter-pills">
            <!-- Satellite / Schematic Mode Switcher -->
            <div class="map-mode-toggle">
                <button type="button" class="map-mode-btn active" id="btn-mode-satellite" onclick="switchMapMode('satellite')">🛰️ Satellite View</button>
                <button type="button" class="map-mode-btn" id="btn-mode-schematic" onclick="switchMapMode('schematic')">🗺️ Schematic Layout</button>
            </div>
            <a href="https://www.google.com/maps/place/Universiti+Teknikal+Malaysia+Melaka/data=!4m2!3m1!1s0x0:0xb8935957e3536888?sa=X&ved=1t:2428&ictx=111" target="_blank" rel="noopener noreferrer" class="map-filter-btn" style="text-decoration:none; display:inline-flex; align-items:center; gap:5px; background:#1e3d29; color:#cbe896; font-weight:800;">
                <span>↗</span> Open Google Maps
            </a>
            <button type="button" class="map-filter-btn active" onclick="filterMapCategory('all', this)">✦ All (7)</button>
            <button type="button" class="map-filter-btn" onclick="filterMapCategory('hostel', this)">🛏️ Residential</button>
            <button type="button" class="map-filter-btn" onclick="filterMapCategory('academic', this)">🏛️ Faculties</button>
            <button type="button" class="map-filter-btn" onclick="filterMapCategory('study', this)">☕ Library</button>
        </div>
    </div>

    <!-- Grid: Map Viewport (Left) & Active Detail Card (Right) -->
    <div class="map-grid-container">
        <!-- Map Canvas Card -->
        <div class="map-canvas-card">
            <!-- 1. Google Satellite Maps View (Active by default) -->
            <div id="map-satellite-container" style="width:100%; height:100%; min-height:450px; position:relative; display:block;">
                <iframe id="gmaps-satellite-frame" 
                        src="https://maps.google.com/maps?q=Universiti+Teknikal+Malaysia+Melaka&t=k&z=16&ie=UTF8&iwloc=&output=embed" 
                        width="100%" 
                        height="100%" 
                        style="border:0; width:100%; height:450px; display:block;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="UTeM Satellite Map">
                </iframe>
                <div style="position:absolute; bottom:12px; left:12px; background:rgba(18, 38, 24, 0.88); backdrop-filter:blur(6px); color:#ffffff; padding:7px 14px; border-radius:10px; font-size:11px; font-weight:700; display:flex; align-items:center; gap:8px; pointer-events:none; border:1px solid rgba(255,255,255,0.15);">
                    <span style="width:8px; height:8px; border-radius:50%; background:#4ade80; display:inline-block; box-shadow:0 0 8px #4ade80;"></span>
                    <span id="satellite-status-label">Google Satellite · Universiti Teknikal Malaysia Melaka</span>
                </div>
            </div>

            <!-- 2. Schematic Map Viewport (Togglable) -->
            <div id="map-schematic-container" style="width:100%; height:100%; display:none; position:relative;">
                <!-- Compass Rose -->
                <div class="map-compass" title="North">
                    <div class="map-compass-arrow"></div>
                    <span>U</span>
                </div>

                <!-- SVG Map Viewport -->
                <svg class="map-svg-viewport" viewBox="0 0 920 540" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="UTeM Main Campus Cafe Map">
                    <defs>
                        <filter id="roadGlow" x="-10%" y="-10%" width="120%" height="120%">
                            <feDropShadow dx="0" dy="1" stdDeviation="1" flood-opacity="0.1" />
                        </filter>
                        <filter id="bldgShadow" x="-10%" y="-10%" width="130%" height="130%">
                            <feDropShadow dx="0" dy="2.5" stdDeviation="2.5" flood-color="#142c1b" flood-opacity="0.12" />
                        </filter>
                        <linearGradient id="lakeWater" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#b6e0ed" />
                            <stop offset="50%" stop-color="#9dd4e6" />
                            <stop offset="100%" stop-color="#84c8de" />
                        </linearGradient>
                        <linearGradient id="terrainGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#edf6e9" />
                            <stop offset="100%" stop-color="#e3efe0" />
                        </linearGradient>
                    </defs>

                    <!-- Campus Base Ground -->
                    <rect width="920" height="540" fill="url(#terrainGrad)" />

                    <!-- Greenery / Contoured Hills / Forest Patches -->
                    <path d="M 0,0 L 280,0 Q 230,80 140,120 Q 50,160 0,220 Z" fill="#d7e8d0" opacity="0.6" />
                    <path d="M 640,0 Q 720,70 850,50 L 920,40 L 920,0 Z" fill="#d7e8d0" opacity="0.6" />
                    <path d="M 0,380 Q 80,360 120,430 Q 150,490 100,540 L 0,540 Z" fill="#d7e8d0" opacity="0.6" />
                    <path d="M 750,540 Q 800,440 920,430 L 920,540 Z" fill="#d7e8d0" opacity="0.6" />

                    <!-- Campus Perimeter Boundary dashed line -->
                    <rect x="18" y="18" width="884" height="504" rx="20" fill="none" stroke="#bed3b7" stroke-width="1.8" stroke-dasharray="6 6" />
                    <text x="36" y="44" font-family="'Plus Jakarta Sans', sans-serif" font-size="12" font-weight="800" fill="#678363" letter-spacing="1">UTEM MAIN CAMPUS (DURIAN TUNGGAL)</text>

                    <!-- Main Roads -->
                    <path d="M 30,490 Q 120,460 220,450" fill="none" stroke="#cbd7c5" stroke-width="20" stroke-linecap="round" />
                    <path d="M 30,490 Q 120,460 220,450" fill="none" stroke="#f6f9f3" stroke-width="14" stroke-linecap="round" />
                    <text x="50" y="520" font-family="'Plus Jakarta Sans', sans-serif" font-size="10" font-weight="700" fill="#586f54">PINTU GERBANG UTAMA</text>

                    <path d="M 220,450 Q 190,320 200,210 Q 220,130 330,130 Q 460,120 580,140 Q 710,160 760,250 Q 800,340 730,440 Q 600,480 430,470 Q 280,480 220,450 Z" 
                          fill="none" stroke="#cad8c4" stroke-width="22" stroke-linejoin="round" stroke-linecap="round" />
                    <path d="M 220,450 Q 190,320 200,210 Q 220,130 330,130 Q 460,120 580,140 Q 710,160 760,250 Q 800,340 730,440 Q 600,480 430,470 Q 280,480 220,450 Z" 
                          fill="none" stroke="#fbfdfa" stroke-width="15" stroke-linejoin="round" stroke-linecap="round" />

                    <path d="M 200,280 L 110,270 Q 70,260 70,210" fill="none" stroke="#cad8c4" stroke-width="16" stroke-linecap="round" />
                    <path d="M 200,280 L 110,270 Q 70,260 70,210" fill="none" stroke="#fbfdfa" stroke-width="11" stroke-linecap="round" />
                    <path d="M 320,130 L 320,70" fill="none" stroke="#cad8c4" stroke-width="16" stroke-linecap="round" />
                    <path d="M 320,130 L 320,70" fill="none" stroke="#fbfdfa" stroke-width="11" stroke-linecap="round" />
                    <path d="M 760,250 L 850,250" fill="none" stroke="#cad8c4" stroke-width="16" stroke-linecap="round" />
                    <path d="M 760,250 L 850,250" fill="none" stroke="#fbfdfa" stroke-width="11" stroke-linecap="round" />
                    <path d="M 380,240 L 540,240" fill="none" stroke="#fbfdfa" stroke-width="12" stroke-linecap="round" />

                    <!-- Tasik UTeM -->
                    <path d="M 460,280 C 510,270 560,300 580,340 C 600,380 570,420 510,430 C 450,440 400,410 400,360 C 400,310 430,285 460,280 Z" 
                          fill="url(#lakeWater)" stroke="#78b9cc" stroke-width="2.5" />
                    <path d="M 440,340 Q 460,335 480,340" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.8" />
                    <path d="M 500,370 Q 525,365 550,370" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.8" />
                    <text x="490" y="355" font-family="'Plus Jakarta Sans', sans-serif" font-size="11" font-weight="800" fill="#2c697c" text-anchor="middle">TASIK UTEM</text>
                    <rect x="475" y="276" width="30" height="9" rx="2" fill="#d2aa7a" stroke="#8c6a45" stroke-width="1.2" />

                    <!-- Buildings -->
                    <g filter="url(#bldgShadow)">
                        <rect x="230" y="55" width="85" height="50" rx="8" fill="#d9e5d4" stroke="#a4bfa0" stroke-width="1.5" />
                        <text x="272" y="84" font-family="'Plus Jakarta Sans', sans-serif" font-size="11" font-weight="800" fill="#2d4d33" text-anchor="middle">FTMK</text>
                    </g>
                    <g filter="url(#bldgShadow)">
                        <rect x="345" y="45" width="100" height="52" rx="8" fill="#eedbcc" stroke="#cb9f82" stroke-width="1.5" />
                        <text x="395" y="70" font-family="'Plus Jakarta Sans', sans-serif" font-size="10" font-weight="800" fill="#5c381e" text-anchor="middle">KOLEJ AL-JAZARI</text>
                    </g>
                    <g filter="url(#bldgShadow)">
                        <rect x="45" y="160" width="115" height="85" rx="10" fill="#eedbcc" stroke="#cb9f82" stroke-width="1.5" />
                        <text x="102" y="210" font-family="'Plus Jakarta Sans', sans-serif" font-size="10.5" font-weight="800" fill="#5c381e" text-anchor="middle">KOLEJ SATRIA</text>
                    </g>
                    <g filter="url(#bldgShadow)">
                        <rect x="290" y="185" width="125" height="80" rx="10" fill="#d0dfce" stroke="#87a982" stroke-width="1.8" />
                        <rect x="302" y="196" width="101" height="24" rx="5" fill="#1e3d29" />
                        <text x="352" y="212" font-family="'Plus Jakarta Sans', sans-serif" font-size="10" font-weight="800" fill="#cbe896" text-anchor="middle">KDK (DEWAN KULIAH)</text>
                    </g>
                    <g filter="url(#bldgShadow)">
                        <rect x="520" y="170" width="100" height="65" rx="9" fill="#dfe6f2" stroke="#9bb1d3" stroke-width="1.6" />
                        <text x="570" y="198" font-family="'Plus Jakarta Sans', sans-serif" font-size="10" font-weight="800" fill="#223d68" text-anchor="middle">PERPUSTAKAAN</text>
                    </g>
                    <g filter="url(#bldgShadow)">
                        <rect x="765" y="165" width="120" height="90" rx="10" fill="#eedbcc" stroke="#cb9f82" stroke-width="1.5" />
                        <text x="825" y="220" font-family="'Plus Jakarta Sans', sans-serif" font-size="11" font-weight="800" fill="#5c381e" text-anchor="middle">KOLEJ LESTARI</text>
                    </g>
                    <g filter="url(#bldgShadow)">
                        <rect x="330" y="380" width="110" height="60" rx="9" fill="#e8dfd3" stroke="#bbaa96" stroke-width="1.5" />
                        <text x="385" y="405" font-family="'Plus Jakarta Sans', sans-serif" font-size="10" font-weight="800" fill="#4e3b26" text-anchor="middle">CANSELORI & HEPA</text>
                    </g>
                    <g filter="url(#bldgShadow)">
                        <rect x="670" y="340" width="115" height="75" rx="9" fill="#d9e5d4" stroke="#a4bfa0" stroke-width="1.5" />
                        <text x="727" y="368" font-family="'Plus Jakarta Sans', sans-serif" font-size="10.5" font-weight="800" fill="#2d4d33" text-anchor="middle">FAKULTI KEJURUTERAAN</text>
                    </g>

                    <!-- Pins -->
                    <g class="map-pin active" id="pin-kdk" data-category="academic" onclick="selectMapCafe('kdk')" transform="translate(352, 275)">
                        <circle class="pin-halo" cx="0" cy="0" r="14" fill="#1e3d29" stroke="#cbe896" />
                        <circle cx="0" cy="0" r="13" fill="#1e3d29" stroke="#ffffff" stroke-width="2.2" />
                        <text x="0" y="4" font-size="12" text-anchor="middle">🍲</text>
                        <rect x="-56" y="-32" width="112" height="19" rx="6" fill="#1e3d29" stroke="#cbe896" stroke-width="1" />
                        <text x="0" y="-19" font-family="'Plus Jakarta Sans', sans-serif" font-size="9" font-weight="800" fill="#ffffff" text-anchor="middle">1. Cafe Abang Mirul</text>
                    </g>
                    <g class="map-pin" id="pin-satria" data-category="hostel" onclick="selectMapCafe('satria')" transform="translate(102, 255)">
                        <circle class="pin-halo" cx="0" cy="0" r="14" fill="#c85a28" stroke="#ffcc80" />
                        <circle cx="0" cy="0" r="13" fill="#c85a28" stroke="#ffffff" stroke-width="2.2" />
                        <text x="0" y="4" font-size="12" text-anchor="middle">🍛</text>
                        <rect x="-52" y="-32" width="104" height="19" rx="6" fill="#c85a28" stroke="#ffffff" stroke-width="0.8" />
                        <text x="0" y="-19" font-family="'Plus Jakarta Sans', sans-serif" font-size="9" font-weight="800" fill="#ffffff" text-anchor="middle">2. Kafeteria Satria</text>
                    </g>
                    <g class="map-pin" id="pin-lestari" data-category="hostel" onclick="selectMapCafe('lestari')" transform="translate(825, 270)">
                        <circle class="pin-halo" cx="0" cy="0" r="14" fill="#2d6a4f" stroke="#95d5b2" />
                        <circle cx="0" cy="0" r="13" fill="#2d6a4f" stroke="#ffffff" stroke-width="2.2" />
                        <text x="0" y="4" font-size="12" text-anchor="middle">🍔</text>
                        <rect x="-54" y="-32" width="108" height="19" rx="6" fill="#2d6a4f" stroke="#ffffff" stroke-width="0.8" />
                        <text x="0" y="-19" font-family="'Plus Jakarta Sans', sans-serif" font-size="9" font-weight="800" fill="#ffffff" text-anchor="middle">3. Kafeteria Lestari</text>
                    </g>
                    <g class="map-pin" id="pin-aljazari" data-category="hostel" onclick="selectMapCafe('aljazari')" transform="translate(395, 108)">
                        <circle class="pin-halo" cx="0" cy="0" r="14" fill="#d97706" stroke="#fde68a" />
                        <circle cx="0" cy="0" r="13" fill="#d97706" stroke="#ffffff" stroke-width="2.2" />
                        <text x="0" y="4" font-size="12" text-anchor="middle">🍱</text>
                        <rect x="-56" y="-32" width="112" height="19" rx="6" fill="#d97706" stroke="#ffffff" stroke-width="0.8" />
                        <text x="0" y="-19" font-family="'Plus Jakarta Sans', sans-serif" font-size="9" font-weight="800" fill="#ffffff" text-anchor="middle">4. Cafe Al-Jazari (FTMK)</text>
                    </g>
                    <g class="map-pin" id="pin-library" data-category="study" onclick="selectMapCafe('library')" transform="translate(570, 245)">
                        <circle class="pin-halo" cx="0" cy="0" r="14" fill="#2563eb" stroke="#bfdbfe" />
                        <circle cx="0" cy="0" r="13" fill="#2563eb" stroke="#ffffff" stroke-width="2.2" />
                        <text x="0" y="4" font-size="12" text-anchor="middle">☕</text>
                        <rect x="-52" y="-32" width="104" height="19" rx="6" fill="#2563eb" stroke="#ffffff" stroke-width="0.8" />
                        <text x="0" y="-19" font-family="'Plus Jakarta Sans', sans-serif" font-size="9" font-weight="800" fill="#ffffff" text-anchor="middle">5. Cafe Laman Hikmah</text>
                    </g>
                    <g class="map-pin" id="pin-hepa" data-category="academic" onclick="selectMapCafe('hepa')" transform="translate(385, 452)">
                        <circle class="pin-halo" cx="0" cy="0" r="14" fill="#059669" stroke="#a7f3d0" />
                        <circle cx="0" cy="0" r="13" fill="#059669" stroke="#ffffff" stroke-width="2.2" />
                        <text x="0" y="4" font-size="12" text-anchor="middle">🥗</text>
                        <rect x="-50" y="-32" width="100" height="19" rx="6" fill="#059669" stroke="#ffffff" stroke-width="0.8" />
                        <text x="0" y="-19" font-family="'Plus Jakarta Sans', sans-serif" font-size="9" font-weight="800" fill="#ffffff" text-anchor="middle">6. Kafeteria HEPA</text>
                    </g>
                    <g class="map-pin" id="pin-fke" data-category="academic" onclick="selectMapCafe('fke')" transform="translate(727, 425)">
                        <circle class="pin-halo" cx="0" cy="0" r="14" fill="#7c3aed" stroke="#ddd6fe" />
                        <circle cx="0" cy="0" r="13" fill="#7c3aed" stroke="#ffffff" stroke-width="2.2" />
                        <text x="0" y="4" font-size="12" text-anchor="middle">🥘</text>
                        <rect x="-58" y="-32" width="116" height="19" rx="6" fill="#7c3aed" stroke="#ffffff" stroke-width="0.8" />
                        <text x="0" y="-19" font-family="'Plus Jakarta Sans', sans-serif" font-size="9" font-weight="800" fill="#ffffff" text-anchor="middle">7. Cafe Kejuruteraan</text>
                    </g>
                </svg>

                <div class="map-legend-overlay">
                    <div class="legend-item"><span class="legend-dot" style="background:#2ebd47;"></span> 7 Campus Cafes Active</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#1e3d29;"></span> Click pins to inspect</div>
                </div>
            </div>
        </div>

        <!-- Selected Cafe Details Card (Right Side) -->
        <div class="map-detail-panel" id="map-detail-panel">
            <div>
                <!-- Status tag -->
                <div class="cafe-status-tag" id="map-detail-status">
                    <span class="live-dot"></span>
                    <span id="map-status-text">OPEN NOW · CAMPUS CENTRAL</span>
                </div>

                <!-- Title & Location -->
                <h4 class="cafe-detail-title" id="map-cafe-title">Cafe Abang Mirul</h4>
                <div class="cafe-detail-location" id="map-cafe-loc">
                    <span>📍</span> Kompleks Dewan Kuliah (KDK), Level G
                </div>

                <!-- Stats Grid -->
                <div class="cafe-stats-row">
                    <div class="cafe-stat-box">
                        <div class="stat-label">🕒 Operating Hours</div>
                        <div class="stat-val" id="map-cafe-hours">7:30 AM – 6:30 PM</div>
                    </div>
                    <div class="cafe-stat-box">
                        <div class="stat-label">🚶 Walk Time</div>
                        <div class="stat-val" id="map-cafe-walk">1–3 mins from KDK</div>
                    </div>
                    <div class="cafe-stat-box">
                        <div class="stat-label">🏪 Active Stalls</div>
                        <div class="stat-val" id="map-cafe-stalls-count">6 Food Stalls</div>
                    </div>
                    <div class="cafe-stat-box">
                        <div class="stat-label">🛵 Runner Delivery</div>
                        <div class="stat-val" id="map-cafe-delivery">Supported (RM2.00)</div>
                    </div>
                </div>

                <!-- Stalls List -->
                <div class="cafe-stalls-section">
                    <div class="cafe-stalls-header">
                        <span>AVAILABLE STALLS</span>
                        <span style="font-size:11px; color:var(--text-muted);">Verified Vendors</span>
                    </div>
                    <div class="cafe-stalls-list" id="map-cafe-stalls-list">
                        <span class="stall-chip">Warung Bonda</span>
                        <span class="stall-chip">Uncle Lim Wok</span>
                        <span class="stall-chip">Ayam Gepuk Mak Dara</span>
                        <span class="stall-chip">Green Bowl</span>
                        <span class="stall-chip">Student Burger Lab</span>
                        <span class="stall-chip">Campus Drinks</span>
                    </div>
                </div>

                <!-- Highlight Signature Food -->
                <div class="cafe-highlight-dish" id="map-cafe-signature">
                    <span style="font-size:18px;">🌟</span>
                    <div>
                        <strong>Top student pick:</strong>
                        <span id="map-cafe-dish-name">Nasi Ayam Gepuk Sambal Gesek & Char Kuey Teow</span>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="cafe-action-group">
                <button type="button" class="btn-cafe-select" id="btn-select-court" onclick="confirmMapCafeCourt()">
                    <span>✦</span> Order from this Cafe
                </button>
                <button type="button" class="btn-cafe-menu" onclick="scrollToMenu()">
                    View Menu ⬇
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Cafe Selector Bar (Bottom) -->
    <div class="cafe-quick-bar" id="cafe-quick-bar">
        <button type="button" class="cafe-quick-pill active" onclick="selectMapCafe('kdk')">
            <span>🏛️</span> 1. KDK (Abang Mirul)
        </button>
        <button type="button" class="cafe-quick-pill" onclick="selectMapCafe('satria')">
            <span>🛏️</span> 2. Kafeteria Satria
        </button>
        <button type="button" class="cafe-quick-pill" onclick="selectMapCafe('lestari')">
            <span>🌲</span> 3. Kafeteria Lestari
        </button>
        <button type="button" class="cafe-quick-pill" onclick="selectMapCafe('aljazari')">
            <span>💻</span> 4. Kafeteria Al-Jazari
        </button>
        <button type="button" class="cafe-quick-pill" onclick="selectMapCafe('library')">
            <span>📚</span> 5. Laman Hikmah
        </button>
        <button type="button" class="cafe-quick-pill" onclick="selectMapCafe('hepa')">
            <span>🎓</span> 6. HEPA / Canselori
        </button>
        <button type="button" class="cafe-quick-pill" onclick="selectMapCafe('fke')">
            <span>⚙️</span> 7. Kejuruteraan
        </button>
    </div>
</section>

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

<!-- Vendor / Stall Selection Pills -->
<?php if (!empty($allSellers)): ?>
<div class="vendor-selection-bar" style="display:flex; align-items:center; gap:8px; overflow-x:auto; padding:6px 0 10px; margin-bottom:10px; scrollbar-width:none;">
    <span style="font-size:11px; font-weight:800; color:#5c6d5e; letter-spacing:0.5px; text-transform:uppercase; white-space:nowrap; margin-right:4px;">🏪 Verified Stalls:</span>
    <button type="button" class="vendor-filter-pill active" data-stall="all" onclick="selectStallFilter('all', this)"
            style="padding:6px 14px; font-size:12.5px; font-weight:700; border-radius:999px; border:1px solid var(--border-color); background:#fff; cursor:pointer; white-space:nowrap; transition:all 0.15s ease;">
        ✦ All Stalls
    </button>
    <?php foreach ($allSellers as $s): ?>
        <button type="button" class="vendor-filter-pill" data-stall="<?= htmlspecialchars($s['stall_name'], ENT_QUOTES, 'UTF-8') ?>" onclick="selectStallFilter('<?= htmlspecialchars($s['stall_name'], ENT_QUOTES, 'UTF-8') ?>', this)"
                style="padding:6px 14px; font-size:12.5px; font-weight:700; border-radius:999px; border:1px solid var(--border-color); background:#fff; cursor:pointer; white-space:nowrap; transition:all 0.15s ease;">
            🏪 <?= htmlspecialchars($s['stall_name'], ENT_QUOTES, 'UTF-8') ?>
        </button>
    <?php endforeach; ?>
</div>
<style>
.vendor-filter-pill.active {
    background: #1e3d29 !important;
    color: #cbe896 !important;
    border-color: #1e3d29 !important;
    box-shadow: 0 2px 8px rgba(30, 61, 41, 0.18);
}
</style>
<?php endif; ?>

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

<?php require_once 'footer.php'; ?>
