<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//block access kalau bukan Rider / Runner (Pelajar dan Vendor akan ditendang balik ke index.php)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array(strtolower($_SESSION['role']), ['rider', 'runner'])) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'UniFood | Rider & Campus Delivery Hub';
$activePage = 'rider';

$riderName = $_SESSION['full_name'] ?? 'Muhammad Alif (Campus Runner)';
$riderVehicle = $_SESSION['vehicle'] ?? 'Motorcycle (Honda EX5) · MCL 4821';
$riderPhone = $_SESSION['phone'] ?? '013-8821945';
$riderId = 1;
if (!empty($_SESSION['rider_id'])) {
    $riderId = (int) $_SESSION['rider_id'];
} elseif (!empty($_SESSION['user_id']) && strpos($_SESSION['user_id'], 'rider_') === 0) {
    $riderId = (int) str_replace('rider_', '', $_SESSION['user_id']);
}

// Fetch live rider details & stats from PostgreSQL
$riderStats = [
    'earnings_today' => 0.00,
    'total_delivered' => 0,
    'rating' => 5.0,
    'is_online' => true,
    'zone' => 'Main Campus · UTeM',
    'avatar_url' => $_SESSION['avatar_url'] ?? null
];

try {
    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', getenv('DB_HOST') ?: '127.0.0.1', getenv('DB_PORT') ?: '5432', getenv('DB_DATABASE') ?: 'unifood');
    $pdo = new PDO($dsn, getenv('DB_USERNAME') ?: 'postgres', getenv('DB_PASSWORD') ?: '123', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2
    ]);

    $stmtR = $pdo->prepare('SELECT rider_name, phone, vehicle_type, vehicle_plate, earnings_today, total_delivered, rating, is_online, current_zone, avatar_url FROM riders WHERE id = :rid OR user_id = :rid LIMIT 1');
    $stmtR->execute(['rid' => $riderId]);
    $rData = $stmtR->fetch();
    if ($rData) {
        $riderStats['earnings_today'] = (float) ($rData['earnings_today'] ?? 0.00);
        $riderStats['total_delivered'] = (int) ($rData['total_delivered'] ?? 0);
        $riderStats['rating'] = (float) ($rData['rating'] ?? 5.0);
        $riderStats['is_online'] = (bool) $rData['is_online'];
        $riderStats['zone'] = $rData['current_zone'] ?: 'Main Campus · UTeM';
        $riderVehicle = ($rData['vehicle_type'] ?? 'Motorcycle') . ' · ' . ($rData['vehicle_plate'] ?? 'MCL 4821');
        if (!empty($rData['avatar_url'])) {
            $riderStats['avatar_url'] = $rData['avatar_url'];
            $_SESSION['avatar_url'] = $rData['avatar_url'];
        }
    }
} catch (Throwable $e) {
    // fallback to defaults
}

require_once 'header.php';
?>

<style>
    /* ============================================================
   RIDER PORTAL MODERN STYLING (CLEAN & SLEEK)
   ============================================================ */

    /* Hero Banner */
    .rider-hero {
        background: linear-gradient(135deg, #183a24 0%, #204c30 55%, #2a633f 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #ffffff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 36px rgba(24, 58, 36, 0.16);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .rider-hero::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(203, 232, 150, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .rider-profile-main {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .rider-avatar-wrap {
        position: relative;
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.14);
        border: 2px solid rgba(255, 255, 255, 0.28);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        flex-shrink: 0;
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.16);
        cursor: pointer;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .rider-avatar-wrap:hover {
        transform: translateY(-2px) scale(1.03);
        border-color: rgba(255, 255, 255, 0.6);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.24);
    }

    .avatar-photo-img {
        width: 100%;
        height: 100%;
        border-radius: 18px;
        object-fit: cover;
        display: block;
    }

    .avatar-camera-badge {
        position: absolute;
        bottom: -4px;
        left: -4px;
        width: 26px;
        height: 26px;
        background: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.22);
        border: 2px solid #1a3824;
        transition: transform 0.18s ease;
        z-index: 2;
    }

    .rider-avatar-wrap:hover .avatar-camera-badge {
        transform: scale(1.15);
    }

    .rider-pulse-dot {
        position: absolute;
        bottom: -3px;
        right: -3px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #3cd02b;
        border: 3px solid #183a24;
        box-shadow: 0 0 10px #3cd02b;
    }

    .rider-pulse-dot.offline {
        background: #e04b38;
        box-shadow: 0 0 8px #e04b38;
    }

    .rider-title-block h1 {
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 4px;
        letter-spacing: -0.4px;
    }

    .rider-tags-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
    }

    .hero-pill {
        font-size: 11.5px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #f2f7f0;
    }

    .hero-pill.accent {
        background: #cbe896;
        color: #163621;
        border-color: #cbe896;
        font-weight: 800;
    }

    /* Duty Switcher Box */
    .rider-duty-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(12px);
        border-radius: 16px;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .duty-status-info {
        text-align: right;
    }

    .duty-status-info .status-state {
        font-size: 13.5px;
        font-weight: 800;
        color: #ffffff;
    }

    .duty-status-info .status-desc {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.75);
    }

    .toggle-switch-btn {
        width: 58px;
        height: 32px;
        background: #3cd02b;
        border-radius: 999px;
        position: relative;
        cursor: pointer;
        border: none;
        transition: background 0.25s ease;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.18);
    }

    .toggle-switch-btn::after {
        content: '';
        position: absolute;
        top: 3px;
        right: 3px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toggle-switch-btn.offline {
        background: #506555;
    }

    .toggle-switch-btn.offline::after {
        transform: translateX(-26px);
    }

    /* Modern Stats Row */
    .rider-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 26px;
    }

    .rider-stat-box {
        background: #ffffff;
        border: 1px solid #e7ebe4;
        border-radius: 16px;
        padding: 20px;
        position: relative;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .rider-stat-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(24, 58, 36, 0.06);
        border-color: #d1ded0;
    }

    .rider-stat-box .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .rider-stat-box .stat-icon-wrap {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }

    .rider-stat-box .stat-val {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-dark);
        letter-spacing: -0.5px;
        line-height: 1.1;
        margin-bottom: 4px;
    }

    .rider-stat-box .stat-name {
        font-size: 12px;
        font-weight: 600;
        color: #68796b;
    }

    /* Segmented Modern Tabs */
    .rider-nav-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #eef2ec;
        padding: 6px;
        border-radius: 14px;
        margin-bottom: 22px;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .rider-tab-btn {
        flex: 1;
        min-width: 170px;
        padding: 10px 18px;
        border-radius: 10px;
        border: none;
        background: transparent;
        color: #4f6052;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
    }

    .rider-tab-btn.active {
        background: #ffffff;
        color: var(--forest);
        box-shadow: 0 3px 12px rgba(24, 58, 36, 0.08);
    }

    .tab-badge-pill {
        background: #e4ebe0;
        color: #274b32;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 999px;
    }

    .rider-tab-btn.active .tab-badge-pill {
        background: var(--forest);
        color: #cbe896;
    }

    /* Delivery Job Cards */
    .rider-jobs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 18px;
    }

    .job-card {
        background: #ffffff;
        border: 1px solid #e5ebe2;
        border-radius: 18px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        transition: all 0.2s ease;
    }

    .job-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(24, 58, 36, 0.08);
        border-color: #cbe0c5;
    }

    .job-card.active-mission {
        border-color: #a4d49a;
        background: linear-gradient(180deg, #ffffff 0%, #fafdfa 100%);
        box-shadow: 0 8px 24px rgba(46, 125, 50, 0.08);
    }

    .job-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f4ee;
    }

    .job-code {
        font-size: 14px;
        font-weight: 800;
        color: var(--text-dark);
    }

    .job-placed {
        font-size: 11.5px;
        color: #7a8a7d;
        margin-top: 2px;
    }

    .payout-tag {
        background: #eaf6e7;
        color: #1e5a2c;
        border: 1px solid #cce8c7;
        font-size: 13px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Route Timeline */
    .route-stepper {
        position: relative;
        padding-left: 28px;
        margin-bottom: 16px;
    }

    .route-stepper::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 14px;
        bottom: 22px;
        width: 2px;
        background: #d8e2d5;
    }

    .step-node {
        position: relative;
        margin-bottom: 14px;
    }

    .step-node:last-child {
        margin-bottom: 0;
    }

    .step-dot {
        position: absolute;
        left: -28px;
        top: 2px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .step-dot.pickup {
        border: 2px solid #2e7d32;
        color: #2e7d32;
    }

    .step-dot.drop {
        border: 2px solid #c85a28;
        color: #c85a28;
    }

    .step-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 2px;
    }

    .step-subtitle {
        font-size: 11.5px;
        color: #6d7d70;
    }

    /* Note and Package Box */
    .package-preview {
        background: #f7faf5;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 12px;
        color: #415243;
        margin-bottom: 14px;
        border: 1px dashed #d5e2d2;
    }

    .package-note {
        background: #fff8f0;
        border-left: 3px solid #e07a36;
        border-radius: 0 8px 8px 0;
        padding: 7px 12px;
        font-size: 11.5px;
        color: #63432a;
        margin-bottom: 14px;
    }

    /* Card Actions */
    .job-actions-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-top: 14px;
        border-top: 1px solid #f0f4ee;
    }

    .btn-action-primary {
        flex: 1;
        background: var(--forest);
        color: #ffffff;
        border: none;
        border-radius: 11px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.18s ease;
    }

    .btn-action-primary:hover {
        background: #152d1c;
        box-shadow: 0 4px 14px rgba(24, 58, 36, 0.2);
    }

    .btn-action-success {
        flex: 1;
        background: #256531;
        color: #cbe896;
        border: none;
        border-radius: 11px;
        padding: 11px 16px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.18s ease;
    }

    .btn-action-success:hover {
        background: #1b4923;
        box-shadow: 0 6px 16px rgba(37, 101, 49, 0.25);
    }

    .btn-action-outline {
        background: #ffffff;
        color: #314a36;
        border: 1px solid #cedad0;
        border-radius: 11px;
        padding: 9px 13px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.18s ease;
    }

    .btn-action-outline:hover {
        background: #f2f7f0;
        border-color: #b2c7b5;
    }

    /* Route Info Modal */
    .route-modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(14, 30, 19, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .route-modal-backdrop.open {
        display: flex;
    }

    .route-modal-card {
        background: #ffffff;
        border-radius: 20px;
        max-width: 480px;
        width: 100%;
        padding: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        animation: modalSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes modalSlideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .rider-hero {
            padding: 20px;
        }

        .rider-duty-card {
            width: 100%;
            justify-content: space-between;
        }

        .rider-jobs-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- 1. Rider Hero Banner -->
<div class="rider-hero">
    <div class="rider-profile-main">
        <div class="rider-avatar-wrap" onclick="openAvatarModal()" title="Click to change your rider profile photo">
            <?php if (!empty($riderStats['avatar_url'])): ?>
                <img src="<?= htmlspecialchars($riderStats['avatar_url'], ENT_QUOTES, 'UTF-8') ?>" alt="Rider Profile"
                    class="avatar-photo-img" id="hero-avatar-img">
            <?php else: ?>
                <span id="hero-avatar-placeholder">🛵</span>
            <?php endif; ?>
            <span class="avatar-camera-badge" title="Change Photo">📷</span>
            <span class="rider-pulse-dot" id="avatar-pulse"></span>
        </div>
        <div class="rider-title-block">
            <div style="display:flex; align-items:center; gap:8px;">
                <h1><?= htmlspecialchars($riderName, ENT_QUOTES, 'UTF-8') ?></h1>
                <span class="hero-pill accent">CAMPUS RUNNER</span>
                <button type="button" onclick="openAvatarModal()"
                    style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.3); color:#ffffff; font-size:11px; font-weight:700; padding:3px 9px; border-radius:8px; cursor:pointer; display:inline-flex; align-items:center; gap:4px; backdrop-filter:blur(4px); transition:background 0.2s;"
                    title="Update Photo">📷 Edit Photo</button>
            </div>
            <div class="rider-tags-row">
                <span class="hero-pill">🛵 <?= htmlspecialchars($riderVehicle, ENT_QUOTES, 'UTF-8') ?></span>
                <span class="hero-pill">📍 <?= htmlspecialchars($riderStats['zone'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="hero-pill">⚡ Fast Dispatch (~3-5 mins)</span>
            </div>
        </div>
    </div>

    <!-- Duty Control Toggle Box -->
    <div class="rider-duty-card">
        <div class="duty-status-info">
            <div class="status-state" id="runner-duty-label">On Duty · Receiving Orders</div>
            <div class="status-desc" id="runner-duty-desc">Ready for pickup at UTeM cafes</div>
        </div>
        <button type="button" class="toggle-switch-btn" id="runner-toggle-btn" onclick="toggleDutyStatus()"
            title="Toggle Duty Status"></button>
    </div>
</div>

<!-- 2. Modern Metric Cards -->
<div class="rider-stats-grid">
    <div class="rider-stat-box">
        <div class="stat-header">
            <div class="stat-icon-wrap" style="background:#eaf6e7; color:#236b32;">💰</div>
            <span
                style="font-size:11px; font-weight:800; background:#e8f4e6; color:#1e5a2c; padding:2px 8px; border-radius:999px;">+RM
                2.00 / drop</span>
        </div>
        <div class="stat-val" id="stat-earnings">RM <?= number_format($riderStats['earnings_today'], 2) ?></div>
        <div class="stat-name">Today's Runner Earnings</div>
    </div>

    <div class="rider-stat-box">
        <div class="stat-header">
            <div class="stat-icon-wrap" style="background:#fff3e6; color:#c85a28;">🛵</div>
            <span
                style="font-size:11px; font-weight:800; background:#fff2e6; color:#b84918; padding:2px 8px; border-radius:999px;"
                id="badge-in-transit">Live</span>
        </div>
        <div class="stat-val" id="stat-active-count">0</div>
        <div class="stat-name">Active Orders in Transit</div>
    </div>

    <div class="rider-stat-box">
        <div class="stat-header">
            <div class="stat-icon-wrap" style="background:#e6f1fc; color:#1e65b8;">⚡</div>
            <span
                style="font-size:11px; font-weight:800; background:#e6f1fc; color:#1e65b8; padding:2px 8px; border-radius:999px;">Ready
                at Stalls</span>
        </div>
        <div class="stat-val" id="stat-available-count">0</div>
        <div class="stat-name">Available Jobs to Claim</div>
    </div>

    <div class="rider-stat-box">
        <div class="stat-header">
            <div class="stat-icon-wrap" style="background:#fef7e6; color:#b87d00;">⭐</div>
            <span
                style="font-size:11px; font-weight:800; background:#fef7e6; color:#9c6a00; padding:2px 8px; border-radius:999px;">99.2%
                On Time</span>
        </div>
        <div class="stat-val"><?= number_format($riderStats['rating'], 1) ?> <small
                style="font-size:14px; color:#839385;">/ 5.0</small></div>
        <div class="stat-name">Customer Rating</div>
    </div>
</div>

<!-- 3. Segmented Navigation Tabs -->
<div class="rider-nav-tabs">
    <button type="button" class="rider-tab-btn active" id="tab-btn-available" onclick="switchRiderTab('available')">
        <span>⚡ Available Delivery Jobs</span>
        <span class="tab-badge-pill" id="tab-avail-counter">0</span>
    </button>
    <button type="button" class="rider-tab-btn" id="tab-btn-active" onclick="switchRiderTab('active')">
        <span>🛵 My Active Missions</span>
        <span class="tab-badge-pill" id="tab-active-counter">0</span>
    </button>
    <button type="button" class="rider-tab-btn" id="tab-btn-history" onclick="switchRiderTab('history')">
        <span>✓ Completed History</span>
        <span class="tab-badge-pill" id="tab-history-counter"><?= (int)$riderStats['total_delivered'] ?></span>
    </button>
</div>

<!-- SECTION 1: AVAILABLE JOBS -->
<section id="section-available">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
            <h3 style="font-size:16px; font-weight:800; color:var(--text-dark); margin:0 0 2px;">Orders Waiting for
                Campus Runner</h3>
            <p style="font-size:12.5px; color:#68796b; margin:0;">Stalls have cooked and packed these orders. Claim a
                delivery to earn your runner fee:</p>
        </div>
        <button type="button" class="btn-action-outline" onclick="fetchLiveJobs()">
            <span>🔄 Refresh</span>
        </button>
    </div>
    <div class="rider-jobs-grid" id="available-jobs-container"></div>
</section>

<!-- SECTION 2: ACTIVE MISSIONS -->
<section id="section-active" style="display:none;">
    <div style="margin-bottom:16px;">
        <h3 style="font-size:16px; font-weight:800; color:var(--text-dark); margin:0 0 2px;">Deliveries in Progress</h3>
        <p style="font-size:12.5px; color:#68796b; margin:0;">Pick up package at stall, verify order code, and deliver
            to student destination:</p>
    </div>
    <div class="rider-jobs-grid" id="active-deliveries-container"></div>
</section>

<!-- SECTION 3: COMPLETED DELIVERIES -->
<section id="section-history" style="display:none;">
    <div style="margin-bottom:16px;">
        <h3 style="font-size:16px; font-weight:800; color:var(--text-dark); margin:0 0 2px;">Delivery Receipts &
            Earnings</h3>
        <p style="font-size:12.5px; color:#68796b; margin:0;">Deliveries successfully completed across campus today:</p>
    </div>
    <div style="display:flex; flex-direction:column; gap:10px;" id="history-container"></div>
</section>

<!-- Modal: Quick Campus Route Preview -->
<div class="route-modal-backdrop" id="route-modal" onclick="if(event.target===this) closeRouteModal()">
    <div class="route-modal-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:20px;">🗺️</span>
                <h4 style="font-size:16px; font-weight:800; color:var(--text-dark); margin:0;">Campus Dispatch Route
                </h4>
            </div>
            <button type="button" onclick="closeRouteModal()"
                style="background:none; border:none; font-size:18px; cursor:pointer; color:#78867a;">✕</button>
        </div>

        <div style="background:#f4f8f2; border-radius:14px; padding:16px; margin-bottom:16px;">
            <div class="route-stepper" style="margin-bottom:0;">
                <div class="step-node">
                    <span class="step-dot pickup">🏪</span>
                    <div class="step-title" id="modal-pickup-title">Cafe Abang Mirul</div>
                    <div class="step-subtitle">Collect package from stall counter</div>
                </div>
                <div class="step-node">
                    <span class="step-dot drop">📍</span>
                    <div class="step-title" id="modal-drop-title">Kolej Satria, Block B</div>
                    <div class="step-subtitle" id="modal-drop-desc">Customer: Ahmad Faiz</div>
                </div>
            </div>
        </div>

        <div
            style="font-size:12px; color:#506052; line-height:1.5; margin-bottom:20px; background:#f9faf8; border:1px solid #e5ebe2; border-radius:12px; padding:12px;">
            🛵 <strong>Fast Route:</strong> Via Jalan Lingkar Kampus Barat. Estimated transit time: <strong>3–4
                minutes</strong>. Please ride safely within campus 30 km/h speed limits.
        </div>

        <button type="button" class="btn-action-primary" style="width:100%;" onclick="closeRouteModal()">
            Got it, Let's Ride ➔
        </button>
    </div>
</div>

<!-- Modal: Rider Profile Photo Manager -->
<div class="route-modal-backdrop" id="avatar-modal" onclick="if(event.target===this) closeAvatarModal()">
    <div class="route-modal-card" style="max-width: 440px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:22px;">📷</span>
                <h4 style="font-size:16px; font-weight:800; color:var(--text-dark); margin:0;">Rider Profile Picture
                </h4>
            </div>
            <button type="button" onclick="closeAvatarModal()"
                style="background:none; border:none; font-size:18px; cursor:pointer; color:#78867a;">✕</button>
        </div>

        <!-- Current / Live Preview -->
        <div
            style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:12px 0 18px;">
            <div
                style="position:relative; width:96px; height:96px; border-radius:24px; border:3px solid #327546; box-shadow:0 8px 24px rgba(50,117,70,0.18); overflow:hidden; background:#eef4ec; display:flex; align-items:center; justify-content:center;">
                <img id="modal-avatar-preview"
                    src="<?= !empty($riderStats['avatar_url']) ? htmlspecialchars($riderStats['avatar_url'], ENT_QUOTES, 'UTF-8') : '' ?>"
                    alt="Preview"
                    style="width:100%; height:100%; object-fit:cover; display:<?= !empty($riderStats['avatar_url']) ? 'block' : 'none' ?>;">
                <span id="modal-avatar-placeholder"
                    style="font-size:42px; display:<?= empty($riderStats['avatar_url']) ? 'block' : 'none' ?>;">🛵</span>
            </div>
            <div style="font-size:13px; color:#23442a; margin-top:8px; font-weight:700;">
                <?= htmlspecialchars($riderName, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div style="font-size:11px; color:#687b6c;">Campus Delivery Runner · UTeM</div>
        </div>

        <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:20px;">
            <div
                style="background:#f7f9f6; border:2px dashed #b9ceb4; border-radius:16px; padding:24px 16px; text-align:center;">
                <label
                    style="display:inline-flex; align-items:center; gap:8px; font-size:13px; font-weight:700; color:#1e4828; cursor:pointer; background:#ffffff; border:1.5px solid #c9d8c4; padding:9px 20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05); transition:background 0.15s, border-color 0.15s;">
                    📁 Choose Photo from Device
                    <input type="file" id="avatar-file-input" accept="image/png, image/jpeg, image/jpg, image/webp"
                        style="display:none;" onchange="handleAvatarFileSelect(this)">
                </label>
                <div style="font-size:11.5px; color:#687b6a; margin-top:8px;">Supports JPG, PNG, WebP up to 5MB</div>
                <div id="file-chosen-name"
                    style="font-size:12px; color:#1e65b8; font-weight:700; margin-top:8px; display:none;"></div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:8px;">
            <button type="button" class="btn-action-primary" id="btn-save-avatar" onclick="saveAvatarProfile()"
                style="width:100%; padding:11px;">
                Save Profile Photo ✓
            </button>
            <div style="display:flex; gap:8px;">
                <button type="button" class="btn-action-outline" onclick="removeAvatarProfile()"
                    style="flex:1; padding:9px; font-size:12px; color:#c83b2b; border-color:#f0cfca;">
                    🗑️ Remove Photo
                </button>
                <button type="button" class="btn-action-outline" onclick="closeAvatarModal()"
                    style="flex:1; padding:9px; font-size:12px;">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const CURRENT_RIDER_ID = <?= json_encode($riderId) ?>;
    let runnerOnDuty = true;
    let currentRiderTab = 'available';

    // In-memory jobs state
    let availableJobs = [];
    let activeDeliveries = [];
    let completedDeliveries = [];

    let todayEarnings = <?= json_encode((float)($riderStats['earnings_today'] ?? 0.00)) ?>;

    function toggleDutyStatus() {
        runnerOnDuty = !runnerOnDuty;
        const toggleBtn = document.getElementById('runner-toggle-btn');
        const pulse = document.getElementById('avatar-pulse');
        const label = document.getElementById('runner-duty-label');
        const desc = document.getElementById('runner-duty-desc');

        if (runnerOnDuty) {
            toggleBtn.classList.remove('offline');
            pulse.classList.remove('offline');
            label.textContent = 'On Duty · Receiving Orders';
            desc.textContent = 'Ready for pickup at UTeM cafes';
            showToast('🛵 You are now ON DUTY. Live delivery requests are active!', null);
        } else {
            toggleBtn.classList.add('offline');
            pulse.classList.add('offline');
            label.textContent = 'Off Duty · Paused';
            desc.textContent = 'Duty paused · Tap toggle to resume';
            showToast('⏸️ Duty paused. You will not receive new jobs until back online.', null);
        }

        try {
            const fd = new FormData();
            fd.append('action', 'toggle_duty');
            fd.append('online', runnerOnDuty);
            fetch('api_rider.php', { method: 'POST', body: fd });
        } catch (e) { }
    }

    function switchRiderTab(tab) {
        currentRiderTab = tab;
        document.querySelectorAll('.rider-tab-btn').forEach(t => t.classList.remove('active'));

        document.getElementById('section-available').style.display = tab === 'available' ? 'block' : 'none';
        document.getElementById('section-active').style.display = tab === 'active' ? 'block' : 'none';
        document.getElementById('section-history').style.display = tab === 'history' ? 'block' : 'none';

        if (tab === 'available') document.getElementById('tab-btn-available').classList.add('active');
        if (tab === 'active') document.getElementById('tab-btn-active').classList.add('active');
        if (tab === 'history') document.getElementById('tab-btn-history').classList.add('active');
    }

    function renderRiderUI() {
        // Update metric counters
        document.getElementById('stat-earnings').textContent = `RM ${Number(todayEarnings).toFixed(2)}`;
        document.getElementById('stat-active-count').textContent = activeDeliveries.length;
        document.getElementById('stat-available-count').textContent = availableJobs.length;
        document.getElementById('tab-avail-counter').textContent = availableJobs.length;
        document.getElementById('tab-active-counter').textContent = activeDeliveries.length;

        // 1. Render Available Jobs
        const availContainer = document.getElementById('available-jobs-container');
        if (availableJobs.length === 0) {
            availContainer.innerHTML = `
                <div style="grid-column: 1 / -1; text-align:center; padding: 56px 20px; background:#fff; border-radius:18px; border:1px solid #e5ebe2;">
                    <div style="font-size:42px; margin-bottom:12px;">☕</div>
                    <h4 style="font-size:16px; font-weight:800; color:var(--text-dark); margin-bottom:4px;">No delivery jobs waiting right now</h4>
                    <p style="font-size:13px; color:#78867a; margin-bottom:16px;">When campus vendors pack customer delivery orders, new pickup missions will show up here automatically.</p>
                    <button type="button" class="btn-action-outline" onclick="fetchLiveJobs()">🔄 Check for Jobs</button>
                </div>
            `;
        } else {
            availContainer.innerHTML = availableJobs.map((job, idx) => `
                <div class="job-card">
                    <div>
                        <div class="job-card-top">
                            <div>
                                <div class="job-code">🛵 Order #${job.code}</div>
                                <div class="job-placed">Placed at ${job.placedAt} · Ready for collection</div>
                            </div>
                            <span class="payout-tag">+RM ${Number(job.fee).toFixed(2)} Fee</span>
                        </div>

                        <!-- Stepper Route -->
                        <div class="route-stepper">
                            <div class="step-node">
                                <span class="step-dot pickup">🏪</span>
                                <div class="step-title">${job.stall}</div>
                                <div class="step-subtitle">Collect packaged meal from vendor counter</div>
                            </div>
                            <div class="step-node">
                                <span class="step-dot drop">📍</span>
                                <div class="step-title">${job.dropSpot}</div>
                                <div class="step-subtitle">Customer: <strong>${job.customer}</strong> · ${job.estTime}</div>
                            </div>
                        </div>

                        <!-- Package items -->
                        <div class="package-preview">
                            📦 <strong>Order Items:</strong> ${job.items}
                        </div>

                        ${job.note ? `<div class="package-note">💬 <strong>Customer Note:</strong> "${job.note}"</div>` : ''}
                    </div>

                    <div class="job-actions-row">
                        <button type="button" class="btn-action-outline" onclick="openRouteModal('${job.stall}', '${job.dropSpot}', '${job.customer}')">
                            🗺️ Route
                        </button>
                        <button type="button" class="btn-action-primary" onclick="acceptDeliveryJob(${idx})">
                            Accept Job (+RM ${Number(job.fee).toFixed(2)}) 🛵
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // 2. Render Active Missions
        const activeContainer = document.getElementById('active-deliveries-container');
        if (activeDeliveries.length === 0) {
            activeContainer.innerHTML = `
                <div style="grid-column: 1 / -1; text-align:center; padding: 56px 20px; background:#fff; border-radius:18px; border:1px solid #e5ebe2;">
                    <div style="font-size:42px; margin-bottom:12px;">🛵</div>
                    <h4 style="font-size:16px; font-weight:800; color:var(--text-dark); margin-bottom:4px;">You have no active deliveries right now</h4>
                    <p style="font-size:13px; color:#78867a; margin-bottom:16px;">Switch to the Available Jobs tab to pick up your next delivery mission.</p>
                    <button type="button" class="btn-action-primary" style="display:inline-flex;" onclick="switchRiderTab('available')">View Available Jobs ➔</button>
                </div>
            `;
        } else {
            activeContainer.innerHTML = activeDeliveries.map((del, idx) => {
                const isInTransit = del.status === 'delivering';
                return `
                    <div class="job-card active-mission">
                        <div>
                            <div class="job-card-top">
                                <div>
                                    <div class="job-code" style="display:flex; align-items:center; gap:6px;">
                                        <span>Order #${del.code}</span>
                                        <span style="font-size:11px; font-weight:800; padding:2px 8px; border-radius:999px; background:${isInTransit ? '#e8f4e6' : '#fff3e6'}; color:${isInTransit ? '#1e5a2c' : '#b84918'};">
                                            ${isInTransit ? '🚀 ON THE WAY' : '📦 READY AT STALL'}
                                        </span>
                                    </div>
                                    <div class="job-placed">Assigned to You · Estimated ride: ${del.estTime}</div>
                                </div>
                                <span class="payout-tag">+RM ${Number(del.fee).toFixed(2)} Fee</span>
                            </div>

                            <div class="route-stepper">
                                <div class="step-node">
                                    <span class="step-dot pickup">🏪</span>
                                    <div class="step-title">${del.stall}</div>
                                    <div class="step-subtitle">${isInTransit ? '✓ Package collected' : 'Waiting at stall counter for pickup'}</div>
                                </div>
                                <div class="step-node">
                                    <span class="step-dot drop">📍</span>
                                    <div class="step-title">${del.dropSpot}</div>
                                    <div class="step-subtitle">Deliver to: <strong>${del.customer}</strong></div>
                                </div>
                            </div>

                            <div class="package-preview">
                                📦 <strong>Meal:</strong> ${del.items}
                            </div>

                            ${del.note ? `<div class="package-note">💬 <strong>Customer Note:</strong> "${del.note}"</div>` : ''}
                        </div>

                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <span style="font-size:12px; color:#68796b;">Contact Student:</span>
                                <a href="tel:${del.phone}" class="btn-action-outline" style="padding:4px 10px; font-size:11.5px;">
                                    📞 Call ${del.phone}
                                </a>
                            </div>

                            <div class="job-actions-row" style="padding-top:8px;">
                                <button type="button" class="btn-action-outline" onclick="openRouteModal('${del.stall}', '${del.dropSpot}', '${del.customer}')">
                                    🗺️ Route
                                </button>
                                ${!isInTransit ? `
                                    <button type="button" class="btn-action-primary" onclick="pickUpOrder(${idx})">
                                        Confirm Picked Up from Cafe ➔
                                    </button>
                                ` : `
                                    <button type="button" class="btn-action-success" onclick="completeDelivery(${idx})">
                                        Mark Delivered to Customer ✓
                                    </button>
                                `}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // 3. Render Completed History
        const histContainer = document.getElementById('history-container');
        if (completedDeliveries.length === 0) {
            histContainer.innerHTML = `
                <div style="text-align:center; padding:48px 20px; background:#fff; border-radius:18px; border:1px solid #e5ebe2;">
                    <div style="font-size:36px; margin-bottom:10px;">📋</div>
                    <h4 style="font-size:16px; font-weight:800; color:var(--text-dark); margin-bottom:4px;">No completed deliveries yet today</h4>
                    <p style="font-size:13px; color:#78867a; margin:0;">When you complete delivery drops to students across campus, your earnings receipts will appear here.</p>
                </div>
            `;
        } else {
            histContainer.innerHTML = completedDeliveries.map(h => `
                <div style="background:#ffffff; border:1px solid #e7ebe4; border-radius:14px; padding:16px 20px; display:flex; justify-content:space-between; align-items:center; transition:box-shadow 0.18s ease;">
                    <div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-size:13px; font-weight:800; color:#1e3d29;">✓ Delivered Order #${h.code}</span>
                            <span style="font-size:11.5px; color:#78867a;">${h.deliveredAt || 'Earlier today'}</span>
                        </div>
                        <div style="font-size:12px; color:#506052; margin-top:4px;">
                            📍 Drop-off: <strong>${h.dropSpot}</strong> · Customer: ${h.customer}
                        </div>
                    </div>
                    <span class="payout-tag">+RM ${Number(h.fee).toFixed(2)}</span>
                </div>
            `).join('');
        }
    }

    function updateLocalOrderState(orderCode, riderState, studentStatusText) {
        try {
            const allOrders = JSON.parse(localStorage.getItem('unifood_orders') || '[]');
            let changed = false;
            allOrders.forEach(o => {
                if (o && o.code === orderCode) {
                    o.riderState = riderState;
                    if (studentStatusText) o.status = studentStatusText;
                    changed = true;
                }
            });
            if (changed) {
                localStorage.setItem('unifood_orders', JSON.stringify(allOrders));
            }
        } catch (e) { }
    }

    function acceptDeliveryJob(idx) {
        const job = availableJobs.splice(idx, 1)[0];
        if (!job) return;

        job.status = 'ready';
        activeDeliveries.unshift(job);
        updateLocalOrderState(job.code, 'accepted', 'Runner assigned · Heading to stall 🛵');
        renderRiderUI();
        switchRiderTab('active');
        showToast(`🛵 Mission accepted! Ride to ${job.stall} for pickup.`, null);

        try {
            const fd = new FormData();
            fd.append('action', 'accept');
            fd.append('order_code', job.code);
            fd.append('order_id', job.id || 0);
            fetch('api_rider.php', { method: 'POST', body: fd });
        } catch (e) { }
    }

    function pickUpOrder(idx) {
        const del = activeDeliveries[idx];
        if (!del) return;
        del.status = 'delivering';
        updateLocalOrderState(del.code, 'delivering', 'Out for delivery · Runner on the way 🛵');
        renderRiderUI();
        showToast(`📦 Order #${del.code} picked up! On the way to ${del.dropSpot}.`, null);

        try {
            const fd = new FormData();
            fd.append('action', 'pickup');
            fd.append('order_code', del.code);
            fd.append('order_id', del.id || 0);
            fetch('api_rider.php', { method: 'POST', body: fd });
        } catch (e) { }
    }

    function completeDelivery(idx) {
        const del = activeDeliveries.splice(idx, 1)[0];
        if (!del) return;

        todayEarnings += Number(del.fee);
        completedDeliveries.unshift({
            code: del.code,
            stall: del.stall,
            dropSpot: del.dropSpot,
            fee: del.fee,
            deliveredAt: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            customer: del.customer
        });
        updateLocalOrderState(del.code, 'completed', 'Delivered ✓');

        renderRiderUI();
        const histCounter = document.getElementById('tab-history-counter');
        if (histCounter) histCounter.textContent = completedDeliveries.length;
        showToast(`🎉 Order #${del.code} delivered successfully! +RM ${Number(del.fee).toFixed(2)} added to your wallet.`, null);

        try {
            const fd = new FormData();
            fd.append('action', 'deliver');
            fd.append('order_code', del.code);
            fd.append('order_id', del.id || 0);
            fetch('api_rider.php', { method: 'POST', body: fd });
        } catch (e) { }
    }

    function openRouteModal(pickup, drop, customer) {
        const modal = document.getElementById('route-modal');
        document.getElementById('modal-pickup-title').textContent = pickup;
        document.getElementById('modal-drop-title').textContent = drop;
        document.getElementById('modal-drop-desc').textContent = 'Customer: ' + customer;
        if (modal) modal.classList.add('open');
    }

    function closeRouteModal() {
        const modal = document.getElementById('route-modal');
        if (modal) modal.classList.remove('open');
    }

    async function fetchLiveJobs(silent = false) {
        try {
            // 1. Backfill any browser-stored orders (delivery or pickup) into backend DB if not synced yet
            const localOrders = JSON.parse(localStorage.getItem('unifood_orders') || '[]');
            for (const lo of localOrders) {
                if (lo && lo.code && lo.mode === 'delivery' && !lo.dbSynced) {
                    const itemsText = lo.itemsSummary || (Array.isArray(lo.items) ? lo.items.map(i => `${i.qty}x ${i.title}`).join(', ') : 'Prepared campus meal package');
                    const stallName = lo.court || (Array.isArray(lo.items) && lo.items[0] ? lo.items[0].stall : 'Cafe Abang Mirul');
                    const fd = new FormData();
                    fd.append('action', 'create_order');
                    fd.append('order_code', lo.code);
                    fd.append('order_type', 'delivery');
                    fd.append('delivery_spot', lo.deliverySpot || 'Kolej Kediaman Satria');
                    fd.append('delivery_note', lo.deliveryNote || '');
                    fd.append('total_amount', Number(lo.total || 0).toFixed(2));
                    fd.append('delivery_fee', Number(lo.deliveryFee > 0 ? lo.deliveryFee : 2.00).toFixed(2));
                    fd.append('customer_name', lo.customerName || 'Campus Student');
                    fd.append('items_summary', itemsText);
                    fd.append('stall_name', stallName);
                    await fetch('api_rider.php', { method: 'POST', body: fd });
                    lo.dbSynced = true;
                }
            }
            localStorage.setItem('unifood_orders', JSON.stringify(localOrders));

            // 2. Fetch live available, active, and completed jobs from database
            const res = await fetch('api_rider.php?action=list');
            const data = await res.json();
            if (data.success) {
                const mapJob = (j) => ({
                    id: j.id,
                    code: j.order_code,
                    stall: (j.stall_name || 'Cafe Abang Mirul') + ' · ' + (j.campus_location || 'Main Campus'),
                    dropSpot: j.delivery_spot || 'Kolej Kediaman Satria',
                    customer: j.customer_name || 'Campus Student',
                    phone: j.customer_phone || '012-3456789',
                    note: j.delivery_note || '',
                    items: j.items_summary || 'Prepared campus meal package',
                    orderTotal: parseFloat(j.total_amount) || 0,
                    fee: parseFloat(j.delivery_fee) || 2.00,
                    estTime: '3–5 mins ride',
                    placedAt: j.placed_at || 'Just now',
                    status: j.status === 'delivering' ? 'delivering' : 'ready'
                });

                availableJobs = Array.isArray(data.available) ? data.available.map(mapJob) : [];
                activeDeliveries = Array.isArray(data.active) ? data.active.map(mapJob) : [];
                completedDeliveries = Array.isArray(data.completed) ? data.completed.map(j => ({
                    code: j.order_code,
                    stall: j.stall_name || 'Cafe Abang Mirul',
                    dropSpot: j.delivery_spot || 'Kolej Kediaman Satria',
                    fee: parseFloat(j.delivery_fee) || 2.00,
                    deliveredAt: j.placed_at || 'Today',
                    customer: j.customer_name || 'Campus Student'
                })) : [];

                if (data.earnings !== undefined) todayEarnings = parseFloat(data.earnings) || 0;
                const histCounter = document.getElementById('tab-history-counter');
                if (histCounter) histCounter.textContent = completedDeliveries.length;

                renderRiderUI();
                if (!silent) {
                    showToast('🔄 Synchronized with campus order queue!', null);
                }
            }
        } catch (e) {
            renderRiderUI();
        }
    }

    // Profile Avatar Management
    let pendingAvatarFile = null;

    function openAvatarModal() {
        const modal = document.getElementById('avatar-modal');
        if (modal) modal.classList.add('open');
    }

    function closeAvatarModal() {
        const modal = document.getElementById('avatar-modal');
        if (modal) modal.classList.remove('open');
    }

    function handleAvatarFileSelect(input) {
        if (input.files && input.files[0]) {
            pendingAvatarFile = input.files[0];
            const nameLabel = document.getElementById('file-chosen-name');
            if (nameLabel) {
                nameLabel.textContent = 'Selected: ' + pendingAvatarFile.name;
                nameLabel.style.display = 'block';
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                updateModalAvatarPreview(e.target.result);
            };
            reader.readAsDataURL(pendingAvatarFile);
        }
    }

    function updateModalAvatarPreview(src) {
        const img = document.getElementById('modal-avatar-preview');
        const placeholder = document.getElementById('modal-avatar-placeholder');
        if (src) {
            if (img) {
                img.src = src;
                img.style.display = 'block';
            }
            if (placeholder) placeholder.style.display = 'none';
        } else {
            if (img) img.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
        }
    }

    async function saveAvatarProfile() {
        const btn = document.getElementById('btn-save-avatar');
        const originalText = btn ? btn.textContent : 'Save Profile Photo ✓';

        if (!pendingAvatarFile) {
            showToast('⚠️ Please choose a photo from your device first.', null);
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Saving photo...';
        }

        const fd = new FormData();
        fd.append('action', 'update_avatar');
        fd.append('avatar', pendingAvatarFile);

        try {
            const res = await fetch('api_rider.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                showToast('🎉 ' + data.message, null);
                updatePageAvatars(data.avatar_url);
                pendingAvatarFile = null;
                const fileInput = document.getElementById('avatar-file-input');
                if (fileInput) fileInput.value = '';
                const nameLabel = document.getElementById('file-chosen-name');
                if (nameLabel) nameLabel.style.display = 'none';
                closeAvatarModal();
            } else {
                showToast('⚠️ ' + (data.error || 'Failed to update photo.'), null);
            }
        } catch (e) {
            showToast('⚠️ Error saving profile photo: ' + e.message, null);
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }
    }

    async function removeAvatarProfile() {
        if (!confirm('Are you sure you want to remove your profile picture?')) return;

        const fd = new FormData();
        fd.append('action', 'update_avatar');
        fd.append('remove', '1');

        try {
            const res = await fetch('api_rider.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                showToast('🗑️ Profile photo removed.', null);
                pendingAvatarFile = null;
                const fileInput = document.getElementById('avatar-file-input');
                if (fileInput) fileInput.value = '';
                const nameLabel = document.getElementById('file-chosen-name');
                if (nameLabel) nameLabel.style.display = 'none';
                updatePageAvatars(null);
                updateModalAvatarPreview(null);
                closeAvatarModal();
            } else {
                showToast('⚠️ ' + (data.error || 'Failed to remove photo.'), null);
            }
        } catch (e) {
            showToast('⚠️ Error removing profile photo: ' + e.message, null);
        }
    }

    function updatePageAvatars(url) {
        // 1. Update Hero Avatar
        const heroWrap = document.querySelector('.rider-avatar-wrap');
        if (heroWrap) {
            let heroImg = document.getElementById('hero-avatar-img');
            let heroPlaceholder = document.getElementById('hero-avatar-placeholder');
            if (url) {
                if (heroImg) {
                    heroImg.src = url;
                } else {
                    if (heroPlaceholder) heroPlaceholder.remove();
                    heroImg = document.createElement('img');
                    heroImg.id = 'hero-avatar-img';
                    heroImg.className = 'avatar-photo-img';
                    heroImg.src = url;
                    heroImg.alt = 'Rider Profile';
                    heroWrap.insertBefore(heroImg, heroWrap.firstChild);
                }
            } else {
                if (heroImg) heroImg.remove();
                if (!heroPlaceholder) {
                    heroPlaceholder = document.createElement('span');
                    heroPlaceholder.id = 'hero-avatar-placeholder';
                    heroPlaceholder.textContent = '🛵';
                    heroWrap.insertBefore(heroPlaceholder, heroWrap.firstChild);
                }
            }
        }

        // 2. Update Sidebar Avatar in header
        const sidebarAvatar = document.querySelector('.sidebar-user .user-avatar');
        if (sidebarAvatar) {
            if (url) {
                sidebarAvatar.style.overflow = 'hidden';
                sidebarAvatar.style.padding = '0';
                sidebarAvatar.innerHTML = '<img src="' + url + '" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">';
            } else {
                sidebarAvatar.style.overflow = '';
                sidebarAvatar.style.padding = '';
                sidebarAvatar.textContent = <?= json_encode($initials ?? 'CR') ?>;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderRiderUI();
        fetchLiveJobs(true);
    });
</script>

<?php require_once 'footer.php'; ?>
