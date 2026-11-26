<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//block access kalau bukan vendor (Pelajar dan Guest akan ditendang balik ke index.php)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'seller') {
    header('Location: index.php');
    exit;
}

$pageTitle = 'UniFood | Vendor & Seller Portal';
$activePage = 'vendor';
$vendorStallName = $_SESSION['stall_name'] ?? 'AL Baek Cafe';
$vendorCampusLocation = $_SESSION['campus_location'] ?? 'Engineering Campus';
$vendorSellerId = null;
if (!empty($_SESSION['seller_id'])) {
    $vendorSellerId = (int)$_SESSION['seller_id'];
} elseif (!empty($_SESSION['user_id']) && strpos($_SESSION['user_id'], 'seller_') === 0) {
    $vendorSellerId = (int)str_replace('seller_', '', $_SESSION['user_id']);
}

// Fetch live menu items exclusively for this vendor from PostgreSQL
$vendorMenuItems = [];
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

    $sql = '
        SELECT m.id, m.seller_id, m.name AS title, m.category, m.price, m.prep_mins AS "prepMins",
               m.badge, m.description AS desc, m.is_available AS available,
               COALESCE(m.stock_quantity, 50) AS quantity,
               m.image_url AS "imageUrl",
               s.stall_name AS stall, s.campus_location
        FROM menu_items m
        LEFT JOIN sellers s ON m.seller_id = s.id
    ';
    if ($vendorSellerId) {
        $sql .= ' WHERE m.seller_id = :sid OR lower(s.stall_name) = lower(:sname)';
        $stmt = $pdo->prepare($sql . ' ORDER BY m.id DESC');
        $stmt->execute(['sid' => $vendorSellerId, 'sname' => $vendorStallName]);
    } else {
        $sql .= ' WHERE lower(s.stall_name) = lower(:sname)';
        $stmt = $pdo->prepare($sql . ' ORDER BY m.id DESC');
        $stmt->execute(['sname' => $vendorStallName]);
    }
    $vendorMenuItems = $stmt->fetchAll();
    foreach ($vendorMenuItems as &$it) {
        $it['sellerId'] = isset($it['seller_id']) ? (int)$it['seller_id'] : $vendorSellerId;
    }
    unset($it);
} catch (Throwable $e) {
    $vendorMenuItems = [];
}

require_once 'header.php';
?>

<!-- Vendor Header -->
<div class="page-header">
    <div class="header-title">
        <div style="display:inline-flex; align-items:center; gap:8px; margin-bottom:4px;">
            <span style="background:#eaf2e3; color:var(--forest); font-size:11px; font-weight:800; padding:3px 9px; border-radius:999px; letter-spacing:0.8px;">VENDOR PORTAL</span>
            <span style="color:#78867a; font-size:12.5px; font-weight:600;"><?= htmlspecialchars($vendorStallName, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($vendorCampusLocation, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <h1>Stall & Order Manager</h1>
        <p>Manage incoming orders, update food preparation status, and control menu availability.</p>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <button type="button" class="vendor-status-toggle" id="vendor-stall-btn" onclick="toggleStallStatus()">
            <span class="status-indicator online" id="stall-dot"></span>
            <span id="stall-status-text">Stall is Open · Accepting Orders</span>
        </button>
        <button type="button" class="banner-cta" onclick="openAddDishModal()">+ Add New Dish</button>
    </div>
</div>

<!-- Vendor Quick Stats -->
<div class="vendor-stats-grid">
    <div class="vendor-stat-card">
        <div class="stat-top">
            <span class="stat-icon">💰</span>
            <span class="stat-trend neutral">Live today</span>
        </div>
        <div class="stat-value" id="vendor-stat-sales">RM 0.00</div>
        <div class="stat-label">Today's Revenue</div>
    </div>

    <div class="vendor-stat-card">
        <div class="stat-top">
            <span class="stat-icon">⏳</span>
            <span class="stat-badge" id="vendor-stat-active-badge">Live</span>
        </div>
        <div class="stat-value" id="vendor-stat-active">0</div>
        <div class="stat-label">Active Orders in Kitchen</div>
    </div>

    <div class="vendor-stat-card">
        <div class="stat-top">
            <span class="stat-icon">🛵</span>
            <span class="stat-trend neutral">Campus Runners</span>
        </div>
        <div class="stat-value" id="vendor-stat-deliveries">0</div>
        <div class="stat-label">Campus Runner Deliveries</div>
    </div>

    <div class="vendor-stat-card">
        <div class="stat-top">
            <span class="stat-icon">⭐</span>
            <span class="stat-trend positive">100% Rating</span>
        </div>
        <div class="stat-value">5.0 <small style="font-size:14px; color:#78867a;">/ 5.0</small></div>
        <div class="stat-label">Student Satisfaction</div>
    </div>
</div>

<!-- Tabs: Orders vs Menu Management -->
<div class="vendor-tabs">
    <button type="button" class="vendor-tab active" id="tab-btn-orders" onclick="switchVendorTab('orders')">
        📋 Live Kitchen Orders <span class="tab-counter" id="active-orders-count">0</span>
    </button>
    <button type="button" class="vendor-tab" id="tab-btn-menu" onclick="switchVendorTab('menu')">
        🍲 Menu & Stock Control (<span id="menu-items-count">6</span> items)
    </button>
</div>

<!-- SECTION 1: LIVE ORDERS LIST -->
<section id="vendor-orders-section">
    <!-- Filter pills -->
    <div class="vendor-filter-bar">
        <div class="filter-group">
            <button type="button" class="v-pill active" onclick="filterVendorOrders('all', this)">All Orders</button>
            <button type="button" class="v-pill" onclick="filterVendorOrders('preparing', this)">🔥 In Preparation</button>
            <button type="button" class="v-pill" onclick="filterVendorOrders('ready', this)">⚡ Ready for Pickup / Runner</button>
            <button type="button" class="v-pill" onclick="filterVendorOrders('completed', this)">✓ Completed</button>
        </div>
        <button type="button" class="refresh-vendor-btn" onclick="renderVendorOrders()">🔄 Refresh Orders</button>
    </div>

    <div class="vendor-orders-grid" id="vendor-orders-container">
        <!-- Rendered via JavaScript -->
    </div>
</section>

<!-- SECTION 2: MENU & STOCK CONTROL -->
<section id="vendor-menu-section" style="display:none;">
    <div class="section-header">
        <div>
            <h3>Your Stall Offerings</h3>
            <p>Toggle availability instantly when ingredients run low.</p>
        </div>
        <button type="button" class="fresh-picks" onclick="openAddDishModal()">+ ADD DISH</button>
    </div>

    <div class="vendor-menu-grid" id="vendor-menu-container">
        <!-- Rendered via JavaScript -->
    </div>
</section>

<!-- MODAL: ADD NEW DISH -->
<div class="modal-backdrop" id="add-dish-modal" onclick="if(event.target===this) closeAddDishModal()">
    <div class="modal-card" style="width: min(540px, 100%); max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3>Add New Stall Dish</h3>
            <button type="button" class="close-drawer-btn" onclick="closeAddDishModal()">✕</button>
        </div>
        <p>List a new meal or drink with photo, price, quantity, and description.</p>

        <form id="add-dish-form" onsubmit="handleAddNewDish(event)">
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Dish Title</label>
                <input type="text" id="new-dish-title" required placeholder="e.g. Nasi Lemak Sambal Sotong"
                       style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Category</label>
                    <select id="new-dish-category" style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                        <option value="local">🍲 Local Favorites</option>
                        <option value="western">🍔 Western</option>
                        <option value="healthy">🥗 Healthy</option>
                        <option value="drinks">🥤 Drinks</option>
                        <option value="snacks">🥪 Snacks</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Price (RM)</label>
                    <input type="number" id="new-dish-price" step="0.10" min="0.50" required placeholder="8.50"
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:12px;">
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Stock Quantity</label>
                    <input type="number" id="new-dish-quantity" min="1" max="500" value="50" required placeholder="50"
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Prep Time (mins)</label>
                    <input type="number" id="new-dish-prep" min="2" max="60" value="8"
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Badge Tag</label>
                    <input type="text" id="new-dish-badge" placeholder="e.g. SPECIAL CHEF"
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Description (Ingredients & Details)</label>
                <textarea id="new-dish-desc" rows="2" placeholder="Fragrant rice, house sambal, crispy chicken & fried egg..."
                          style="width:100%; border:1px solid #d8dfd5; border-radius:10px; padding:10px 12px; font-family:inherit;"></textarea>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Dish Photo (Upload Image or paste URL)</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; align-items:center;">
                    <div>
                        <input type="file" id="new-dish-file" accept="image/*" onchange="previewDishImage(this, 'new')"
                               style="font-size:12px; width:100%;">
                    </div>
                    <div>
                        <input type="url" id="new-dish-url" placeholder="Or paste image URL (https://...)" oninput="previewUrlImage(this.value, 'new')"
                               style="width:100%; height:38px; border:1px solid #d8dfd5; border-radius:8px; padding:0 10px; font-size:12px;">
                    </div>
                </div>
                <div id="new-dish-preview-wrap" style="display:none; margin-top:8px;">
                    <img id="new-dish-preview" src="" alt="Preview" style="height:65px; border-radius:8px; border:1px solid #d8dfd5; object-fit:cover;">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-outline" onclick="closeAddDishModal()">Cancel</button>
                <button type="submit" class="banner-cta">Save Dish to Menu ↗</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDIT EXISTING DISH -->
<div class="modal-backdrop" id="edit-dish-modal" onclick="if(event.target===this) closeEditDishModal()">
    <div class="modal-card" style="width: min(540px, 100%); max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3>✏️ Edit Stall Dish</h3>
            <button type="button" class="close-drawer-btn" onclick="closeEditDishModal()">✕</button>
        </div>
        <p>Update title, price, available quantity, description, or change photo.</p>

        <form id="edit-dish-form" onsubmit="handleSaveEditedDish(event)">
            <input type="hidden" id="edit-dish-id">

            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Dish Title</label>
                <input type="text" id="edit-dish-title" required
                       style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Category</label>
                    <select id="edit-dish-category" style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                        <option value="local">🍲 Local Favorites</option>
                        <option value="western">🍔 Western</option>
                        <option value="healthy">🥗 Healthy</option>
                        <option value="drinks">🥤 Drinks</option>
                        <option value="snacks">🥪 Snacks</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Price (RM)</label>
                    <input type="number" id="edit-dish-price" step="0.10" min="0.50" required
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Available Stock (Portions)</label>
                    <input type="number" id="edit-dish-quantity" min="0" max="500" required
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Badge Tag</label>
                    <input type="text" id="edit-dish-badge" placeholder="e.g. SPECIAL CHEF"
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Description (Ingredients & Notes)</label>
                <textarea id="edit-dish-desc" rows="2" placeholder="Ingredients, taste, spicy level..."
                          style="width:100%; border:1px solid #d8dfd5; border-radius:10px; padding:10px 12px; font-family:inherit;"></textarea>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Dish Photo (Upload File or Image URL)</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; align-items:center;">
                    <div>
                        <input type="file" id="edit-dish-file" accept="image/*" onchange="previewDishImage(this, 'edit')"
                               style="font-size:12px; width:100%;">
                    </div>
                    <div>
                        <input type="url" id="edit-dish-url" placeholder="Or paste image URL (https://...)" oninput="previewUrlImage(this.value, 'edit')"
                               style="width:100%; height:38px; border:1px solid #d8dfd5; border-radius:8px; padding:0 10px; font-size:12px;">
                    </div>
                </div>
                <div id="edit-dish-preview-wrap" style="margin-top:8px;">
                    <img id="edit-dish-preview" src="" alt="Preview" style="height:65px; border-radius:8px; border:1px solid #d8dfd5; object-fit:cover; display:none;">
                </div>
            </div>

            <div style="margin-bottom:18px; display:flex; align-items:center; gap:8px;">
                <input type="checkbox" id="edit-dish-available" style="width:18px; height:18px; accent-color:var(--forest);">
                <label for="edit-dish-available" style="font-size:13px; font-weight:700; cursor:pointer;">Dish is in stock & available for student orders</label>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-outline" onclick="closeEditDishModal()">Cancel</button>
                <button type="submit" class="banner-cta" id="btn-save-edit-dish">Save Changes ↗</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Vendor Specific Styling */
    .vendor-status-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        padding: 9px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dark);
        cursor: pointer;
        transition: all 0.15s;
    }
    .vendor-status-toggle:hover {
        background: #f4f7f1;
        border-color: #d1ded0;
    }
    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-indicator.online {
        background: #3ec72b;
        box-shadow: 0 0 8px rgba(62, 199, 43, 0.6);
    }
    .status-indicator.offline {
        background: #d9432e;
        box-shadow: 0 0 8px rgba(217, 67, 46, 0.5);
    }

    /* Stats Grid */
    .vendor-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    .vendor-stat-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        transition: transform 0.15s;
    }
    .vendor-stat-card:hover {
        transform: translateY(-2px);
    }
    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .stat-icon {
        font-size: 22px;
    }
    .stat-trend {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 999px;
    }
    .stat-trend.positive {
        background: #eaf6ea;
        color: #276b32;
    }
    .stat-trend.neutral {
        background: #eef3f7;
        color: #2f5674;
    }
    .stat-badge {
        font-size: 11px;
        font-weight: 800;
        background: #fdf3e7;
        color: #c96515;
        padding: 3px 8px;
        border-radius: 999px;
        animation: pulseStat 1.2s infinite alternate;
    }
    @keyframes pulseStat {
        0% { opacity: 0.8; }
        100% { opacity: 1; }
    }
    .stat-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-dark);
        letter-spacing: -0.8px;
        margin-bottom: 4px;
    }
    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* Vendor Tabs */
    .vendor-tabs {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 20px;
        padding-bottom: 2px;
    }
    .vendor-tab {
        background: transparent;
        border: none;
        padding: 12px 18px;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        border-bottom: 3px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
    }
    .vendor-tab:hover {
        color: var(--forest);
    }
    .vendor-tab.active {
        color: var(--forest);
        border-bottom-color: var(--forest);
    }
    .tab-counter {
        background: #eef4e8;
        color: var(--forest);
        font-size: 11px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 999px;
    }

    /* Filter Bar */
    .vendor-filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }
    .filter-group {
        display: flex;
        gap: 8px;
    }
    .v-pill {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 999px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.15s;
    }
    .v-pill:hover {
        background: #f4f6f1;
        color: var(--text-dark);
    }
    .v-pill.active {
        background: var(--forest);
        color: #ffffff;
        border-color: var(--forest);
    }
    .refresh-vendor-btn {
        background: transparent;
        border: 1px solid var(--border-color);
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
    }
    .refresh-vendor-btn:hover {
        background: #f2f5ee;
        color: var(--forest);
    }

    /* Orders Cards */
    .vendor-orders-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    .vendor-order-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: box-shadow 0.15s;
    }
    .vendor-order-card:hover {
        box-shadow: 0 8px 20px rgba(24, 40, 28, 0.05);
    }
    .v-order-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #edf1eb;
    }
    .v-badge-pickup {
        background: #eaf3e6;
        color: #276233;
        font-size: 11.5px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
    }
    .v-badge-delivery {
        background: #fdf0e6;
        color: #b55314;
        font-size: 11.5px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
    }
    .v-order-items {
        margin: 12px 0;
        font-size: 13px;
        color: var(--text-dark);
    }
    .v-order-item-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
    }
    .v-order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #edf1eb;
    }
    .v-status-btn {
        padding: 8px 14px;
        border-radius: 9px;
        font-size: 12.5px;
        font-weight: 800;
        border: none;
        cursor: pointer;
        transition: transform 0.15s, background 0.15s;
    }
    .v-status-btn.prep {
        background: #fbf3e6;
        color: #9c5c16;
    }
    .v-status-btn.prep:hover {
        background: #f6e6cd;
    }
    .v-status-btn.ready {
        background: #e5f6e5;
        color: #246d32;
    }
    .v-status-btn.ready:hover {
        background: #d3eed3;
    }
    .v-status-btn.done {
        background: #eaf0ec;
        color: #556959;
        cursor: default;
    }

    /* Menu Control Grid */
    .vendor-menu-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .v-menu-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .v-menu-top {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
    }
    .v-menu-thumb {
        width: 60px;
        height: 60px;
        background: #f3f6ee;
        border-radius: 12px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .v-stock-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f7f9f5;
        border-radius: 10px;
        padding: 8px 12px;
        margin-top: 10px;
    }

    @media (max-width: 900px) {
        .vendor-stats-grid {
            grid-template-columns: 1fr 1fr;
        }
        .vendor-orders-grid {
            grid-template-columns: 1fr;
        }
        .vendor-menu-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Vendor profile constants
    const CURRENT_VENDOR_ID = <?= json_encode($vendorSellerId) ?>;
    const CURRENT_VENDOR_STALL = <?= json_encode($vendorStallName) ?>;
    const CURRENT_VENDOR_CAMPUS = <?= json_encode($vendorCampusLocation) ?>;
    const SERVER_VENDOR_ITEMS = <?= json_encode($vendorMenuItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    // Dedicated vendor menu list (strictly for this vendor / stall)
    let vendorMeals = [];

    function initVendorMeals() {
        const serverItems = Array.isArray(SERVER_VENDOR_ITEMS) ? SERVER_VENDOR_ITEMS : [];
        const formattedServer = serverItems.map(item => ({
            id: 'db_' + item.id,
            sellerId: item.seller_id ? parseInt(item.seller_id, 10) : CURRENT_VENDOR_ID,
            title: item.title,
            stall: (item.stall || CURRENT_VENDOR_STALL) + ' · ' + (item.campus_location || CURRENT_VENDOR_CAMPUS),
            category: item.category || 'local',
            badge: item.badge || 'SPECIAL',
            price: parseFloat(item.price),
            prepMins: parseInt(item.prepMins, 10) || 8,
            quantity: item.quantity !== undefined ? parseInt(item.quantity, 10) : 50,
            imageUrl: item.imageUrl || '',
            desc: item.desc || '',
            available: item.available !== false && (item.quantity === undefined || parseInt(item.quantity, 10) > 0),
            svg: typeof getDishSvg === 'function' ? getDishSvg(item.category) : ''
        }));

        // Filter localStorage custom meals strictly to this vendor
        const customLocal = JSON.parse(localStorage.getItem('unifood_custom_meals') || '[]');
        const stallCustom = customLocal.filter(m => {
            if (m.sellerId && CURRENT_VENDOR_ID) {
                return parseInt(m.sellerId, 10) === parseInt(CURRENT_VENDOR_ID, 10);
            }
            return m.stall && m.stall.toLowerCase().includes(CURRENT_VENDOR_STALL.toLowerCase());
        });

        const combined = [];
        const seenTitles = new Set();

        stallCustom.forEach(m => {
            const key = m.title.trim().toLowerCase();
            if (!seenTitles.has(key)) {
                seenTitles.add(key);
                if (!m.svg && typeof getDishSvg === 'function') m.svg = getDishSvg(m.category);
                combined.push(m);
            }
        });

        formattedServer.forEach(m => {
            const key = m.title.trim().toLowerCase();
            if (!seenTitles.has(key)) {
                seenTitles.add(key);
                combined.push(m);
            }
        });

        vendorMeals = combined;
    }

    // Vendor state management
    let vendorStallOpen = localStorage.getItem('unifood_stall_open') !== 'false';
    let currentVendorFilter = 'all';

    // Default vendor orders queue
    let vendorOrders = JSON.parse(localStorage.getItem('unifood_vendor_orders') || '[]');
    // Clean out any old mock orders
    if (Array.isArray(vendorOrders)) {
        vendorOrders = vendorOrders.filter(o => !['UF-842', 'UF-819', 'UF-790', 'UF-880', 'UF-765'].includes(o.code));
        localStorage.setItem('unifood_vendor_orders', JSON.stringify(vendorOrders));
    }

    function toggleStallStatus() {
        vendorStallOpen = !vendorStallOpen;
        localStorage.setItem('unifood_stall_open', vendorStallOpen);
        updateStallUI();
        showToast(vendorStallOpen ? '🟢 Stall is now OPEN for customer orders' : '🔴 Stall is now CLOSED for orders', null);
    }

    function updateStallUI() {
        const dot = document.getElementById('stall-dot');
        const text = document.getElementById('stall-status-text');
        if (dot && text) {
            dot.className = vendorStallOpen ? 'status-indicator online' : 'status-indicator offline';
            text.textContent = vendorStallOpen ? 'Stall is Open · Accepting Orders' : 'Stall is Closed · Paused';
        }
    }

    function switchVendorTab(tab) {
        const ordersSec = document.getElementById('vendor-orders-section');
        const menuSec = document.getElementById('vendor-menu-section');
        const tabOrders = document.getElementById('tab-btn-orders');
        const tabMenu = document.getElementById('tab-btn-menu');

        if (tab === 'orders') {
            ordersSec.style.display = 'block';
            menuSec.style.display = 'none';
            tabOrders.classList.add('active');
            tabMenu.classList.remove('active');
        } else {
            ordersSec.style.display = 'none';
            menuSec.style.display = 'block';
            tabOrders.classList.remove('active');
            tabMenu.classList.add('active');
            renderVendorMenu();
        }
    }

    function filterVendorOrders(filter, btn) {
        currentVendorFilter = filter;
        document.querySelectorAll('.v-pill').forEach(p => p.classList.remove('active'));
        if (btn) btn.classList.add('active');
        renderVendorOrders();
    }

    function updateOrderStatus(idx, newStatus) {
        vendorOrders[idx].status = newStatus;
        localStorage.setItem('unifood_vendor_orders', JSON.stringify(vendorOrders));
        renderVendorOrders();
        showToast(`Order #${vendorOrders[idx].code} updated to: ${newStatus.toUpperCase()}`, null);
    }

    function renderVendorOrders() {
        const container = document.getElementById('vendor-orders-container');
        if (!container) return;

        // Also sync any customer orders placed in app
        const studentOrders = JSON.parse(localStorage.getItem('unifood_orders') || '[]');
        studentOrders.forEach(so => {
            const exists = vendorOrders.some(vo => vo.code === so.code);
            if (!exists) {
                vendorOrders.unshift({
                    code: so.code,
                    customer: so.customerName || 'Campus Student',
                    userId: so.userId || null,
                    mode: so.mode || 'pickup',
                    spot: so.deliverySpot || so.court || 'Cafe Abang Mirul',
                    time: so.pickupTime || 'ASAP',
                    status: 'preparing',
                    items: so.items || [],
                    total: so.total || 0,
                    placedAt: so.createdAt || 'Just now'
                });
            }
        });

        // Filter
        const filtered = vendorOrders.filter(o => {
            if (currentVendorFilter === 'all') return true;
            return o.status === currentVendorFilter;
        });

        // Update active stats
        const activeCount = vendorOrders.filter(o => o.status !== 'completed').length;
        const activeBadge = document.getElementById('vendor-stat-active');
        const tabCount = document.getElementById('active-orders-count');
        if (activeBadge) activeBadge.textContent = activeCount;
        if (tabCount) tabCount.textContent = activeCount;

        const completedRev = vendorOrders.filter(o => o.status === 'completed').reduce((sum, o) => sum + (Number(o.total) || 0), 0);
        const salesEl = document.getElementById('vendor-stat-sales');
        if (salesEl) salesEl.textContent = 'RM ' + completedRev.toFixed(2);

        const deliveryCount = vendorOrders.filter(o => o.mode === 'delivery').length;
        const delEl = document.getElementById('vendor-stat-deliveries');
        if (delEl) delEl.textContent = deliveryCount;

        if (filtered.length === 0) {
            container.innerHTML = `
                <div style="grid-column: 1 / -1; text-align:center; padding: 48px 16px; background:#fff; border-radius:16px; border:1px solid var(--border-color);">
                    <div style="font-size:32px; margin-bottom:8px;">☕</div>
                    <h4 style="font-size:16px; color:var(--text-dark); margin-bottom:4px;">No ${currentVendorFilter} orders found</h4>
                    <p style="font-size:13px; color:var(--text-muted);">Incoming student orders will appear right here automatically.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = filtered.map((ord, idx) => {
            const isDelivery = ord.mode === 'delivery';
            const badgeClass = isDelivery ? 'v-badge-delivery' : 'v-badge-pickup';
            const badgeText = isDelivery ? `🛵 Delivery #${ord.code}` : `🏃 Pickup #${ord.code}`;
            const destLabel = isDelivery ? `Destination: <strong>${ord.spot}</strong>` : `Pickup Counter: <strong>${ord.spot}</strong>`;

            let actionBtn = '';
            if (ord.status === 'preparing') {
                actionBtn = `<button type="button" class="v-status-btn prep" onclick="updateOrderStatus(${idx}, 'ready')">Mark as Ready ⚡</button>`;
            } else if (ord.status === 'ready') {
                actionBtn = `<button type="button" class="v-status-btn ready" onclick="updateOrderStatus(${idx}, 'completed')">Mark Completed ✓</button>`;
            } else {
                actionBtn = `<span class="v-status-btn done">Completed ✓</span>`;
            }

            const itemsHtml = ord.items.map(it => {
                const itemTotal = (it.price * it.qty).toFixed(2);
                return `<div class="v-order-item-row"><span><strong>${it.qty}x</strong> ${it.title}</span><span>RM ${itemTotal}</span></div>`;
            }).join('');

            return `
                <div class="vendor-order-card">
                    <div>
                        <div class="v-order-head">
                            <div>
                                <span class="${badgeClass}">${badgeText}</span>
                                <div style="font-size:11.5px; color:#78867a; margin-top:6px;">${ord.customer} · Placed at ${ord.placedAt}</div>
                            </div>
                            <span style="font-size:12px; font-weight:700; color:#4e5e51;">⏰ ${ord.time}</span>
                        </div>

                        <div style="font-size:12px; color:#4a5b4d; margin-bottom:8px;">📍 ${destLabel}</div>

                        <div class="v-order-items">
                            ${itemsHtml}
                        </div>
                    </div>

                    <div class="v-order-footer">
                        <div style="font-size:14px; font-weight:800; color:var(--forest);">Total: RM ${Number(ord.total).toFixed(2)}</div>
                        ${actionBtn}
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderVendorMenu() {
        const container = document.getElementById('vendor-menu-container');
        if (!container) return;

        const countEl = document.getElementById('menu-items-count');
        if (countEl) countEl.textContent = vendorMeals.length;

        if (vendorMeals.length === 0) {
            container.innerHTML = `
                <div style="grid-column: 1 / -1; text-align:center; padding: 48px 16px; background:#fff; border-radius:16px; border:1px solid var(--border-color);">
                    <div style="font-size:36px; margin-bottom:8px;">🍽️</div>
                    <h4 style="font-size:16px; color:var(--text-dark); margin-bottom:4px;">No dishes in your stall menu yet</h4>
                    <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">Add dishes to start taking customer orders for ${CURRENT_VENDOR_STALL}.</p>
                    <button type="button" class="banner-cta" onclick="openAddDishModal()">+ Add First Dish</button>
                </div>
            `;
            return;
        }

        container.innerHTML = vendorMeals.map(meal => {
            const isAvailable = meal.available !== false && (meal.quantity === undefined || parseInt(meal.quantity, 10) > 0);
            const qty = meal.quantity !== undefined ? parseInt(meal.quantity, 10) : 50;

            const thumbHTML = meal.imageUrl
                ? `<img src="${meal.imageUrl}" alt="${meal.title}" style="width:100%; height:100%; object-fit:cover; border-radius:10px;">`
                : (meal.svg || `<svg width="78" height="78" viewBox="0 0 64 64" fill="none"><circle cx="32" cy="32" r="26" fill="#e8f3e5"/><text x="32" y="38" font-size="22" text-anchor="middle">🍲</text></svg>`);

            return `
                <div class="v-menu-card">
                    <div>
                        <div class="v-menu-top">
                            <div class="v-menu-thumb" style="position:relative; overflow:hidden;">${thumbHTML}</div>
                            <div style="flex:1;">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:4px;">
                                    <h4 style="font-size:14px; font-weight:700; color:var(--text-dark); margin-bottom:2px;">${meal.title}</h4>
                                    <span style="font-size:10px; font-weight:800; background:#e8f4e6; color:#1e3d29; padding:2px 6px; border-radius:999px;">${(meal.category || 'LOCAL').toUpperCase()}</span>
                                </div>
                                <div style="font-size:11.5px; color:#78867a;">${meal.stall}</div>
                                <div style="display:flex; align-items:center; gap:8px; margin-top:4px;">
                                    <span style="font-size:14px; font-weight:800; color:var(--forest);">RM ${Number(meal.price).toFixed(2)}</span>
                                    <span style="font-size:11px; font-weight:700; background:#f0f5ec; color:#305b38; padding:2px 6px; border-radius:6px;">📦 Stock: ${qty} portions</span>
                                </div>
                            </div>
                        </div>
                        <p style="font-size:12px; color:#68786a; line-height:1.4; margin-top:10px;">${meal.desc || 'No description provided.'}</p>
                    </div>

                    <div class="v-stock-toggle" style="display:flex; justify-content:space-between; align-items:center; gap:6px; flex-wrap:wrap; margin-top:12px; padding-top:10px; border-top:1px solid #eef2ec;">
                        <span style="font-size:11.5px; font-weight:700; color:${isAvailable ? '#256531' : '#b23725'};">
                            ${isAvailable ? '🟢 In Stock' : '🔴 Sold Out'}
                        </span>
                        <div style="display:flex; gap:6px;">
                            <button type="button" class="btn-outline" style="padding:5px 10px; font-size:11.5px; display:inline-flex; align-items:center; gap:4px;"
                                    data-id="${meal.id}" onclick="openEditDishModal(this.dataset.id)">
                                ✏️ Edit Dish
                            </button>
                            <button type="button" class="btn-outline" style="padding:5px 10px; font-size:11.5px;"
                                    data-id="${meal.id}" onclick="toggleMealStock(this.dataset.id)">
                                ${isAvailable ? 'Set Sold Out' : 'Set Available'}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    async function toggleMealStock(mealId) {
        const meal = vendorMeals.find(m => m.id === mealId);
        if (!meal) return;
        meal.available = !(meal.available !== false);

        // Sync with PostgreSQL API if db item
        try {
            const formData = new FormData();
            formData.append('action', 'toggle_stock');
            formData.append('id', mealId);
            formData.append('available', meal.available);
            fetch('api_menu.php', { method: 'POST', body: formData });
        } catch (e) {
            // Ignore network errors
        }

        // Update localStorage
        const customMeals = JSON.parse(localStorage.getItem('unifood_custom_meals') || '[]');
        const idx = customMeals.findIndex(m => m.id === mealId);
        if (idx !== -1) {
            customMeals[idx].available = meal.available;
            localStorage.setItem('unifood_custom_meals', JSON.stringify(customMeals));
        }

        renderVendorMenu();
        showToast(`${meal.title}: ${meal.available ? 'In Stock (Available)' : 'Sold Out (Habis)'}`, null);
    }

    function openAddDishModal() {
        const modal = document.getElementById('add-dish-modal');
        if (modal) modal.classList.add('open');
    }

    function closeAddDishModal() {
        const modal = document.getElementById('add-dish-modal');
        if (modal) modal.classList.remove('open');
    }

    // Image preview helper for file input
    function previewDishImage(input, type) {
        const file = input.files && input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const previewEl = document.getElementById(type === 'edit' ? 'edit-dish-preview' : 'new-dish-preview');
            const wrapEl = document.getElementById(type === 'edit' ? 'edit-dish-preview-wrap' : 'new-dish-preview-wrap');
            if (previewEl) {
                previewEl.src = e.target.result;
                previewEl.style.display = 'block';
            }
            if (wrapEl) wrapEl.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // Image preview helper for URL input
    function previewUrlImage(url, type) {
        const previewEl = document.getElementById(type === 'edit' ? 'edit-dish-preview' : 'new-dish-preview');
        const wrapEl = document.getElementById(type === 'edit' ? 'edit-dish-preview-wrap' : 'new-dish-preview-wrap');
        if (url && url.trim().length > 5) {
            if (previewEl) {
                previewEl.src = url.trim();
                previewEl.style.display = 'block';
            }
            if (wrapEl) wrapEl.style.display = 'block';
        } else {
            if (previewEl) previewEl.style.display = 'none';
        }
    }

    // OPEN EDIT MODAL
    function openEditDishModal(mealId) {
        const meal = vendorMeals.find(m => m.id === mealId);
        if (!meal) return;

        document.getElementById('edit-dish-id').value = meal.id;
        document.getElementById('edit-dish-title').value = meal.title || '';
        document.getElementById('edit-dish-category').value = meal.category || 'local';
        document.getElementById('edit-dish-price').value = meal.price ? Number(meal.price).toFixed(2) : '5.00';
        document.getElementById('edit-dish-quantity').value = meal.quantity !== undefined ? meal.quantity : 50;
        document.getElementById('edit-dish-badge').value = meal.badge || '';
        document.getElementById('edit-dish-desc').value = meal.desc || '';
        document.getElementById('edit-dish-available').checked = meal.available !== false && (meal.quantity === undefined || meal.quantity > 0);

        const previewEl = document.getElementById('edit-dish-preview');
        const wrapEl = document.getElementById('edit-dish-preview-wrap');
        const urlInput = document.getElementById('edit-dish-url');
        const fileInput = document.getElementById('edit-dish-file');
        if (fileInput) fileInput.value = '';

        if (meal.imageUrl) {
            if (urlInput) urlInput.value = meal.imageUrl;
            if (previewEl) {
                previewEl.src = meal.imageUrl;
                previewEl.style.display = 'block';
            }
            if (wrapEl) wrapEl.style.display = 'block';
        } else {
            if (urlInput) urlInput.value = '';
            if (previewEl) previewEl.style.display = 'none';
        }

        const modal = document.getElementById('edit-dish-modal');
        if (modal) modal.classList.add('open');
    }

    function closeEditDishModal() {
        const modal = document.getElementById('edit-dish-modal');
        if (modal) modal.classList.remove('open');
    }

    // SAVE EDITED DISH
    async function handleSaveEditedDish(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('btn-save-edit-dish');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving changes...';
        }

        const dishId = document.getElementById('edit-dish-id').value;
        const title = document.getElementById('edit-dish-title').value.trim();
        const category = document.getElementById('edit-dish-category').value;
        const price = parseFloat(document.getElementById('edit-dish-price').value) || 5.00;
        const quantity = parseInt(document.getElementById('edit-dish-quantity').value, 10) || 0;
        const badge = document.getElementById('edit-dish-badge').value.trim().toUpperCase() || 'SPECIAL';
        const desc = document.getElementById('edit-dish-desc').value.trim();
        const available = document.getElementById('edit-dish-available').checked && quantity > 0;
        const fileInput = document.getElementById('edit-dish-file');
        const urlInput = document.getElementById('edit-dish-url');
        const imageUrl = urlInput ? urlInput.value.trim() : '';

        const formData = new FormData();
        formData.append('action', 'edit');
        formData.append('id', dishId);
        formData.append('title', title);
        formData.append('category', category);
        formData.append('price', price);
        formData.append('quantity', quantity);
        formData.append('badge', badge);
        formData.append('description', desc);
        formData.append('image_url', imageUrl);
        formData.append('available', available);

        if (fileInput && fileInput.files && fileInput.files[0]) {
            formData.append('photo', fileInput.files[0]);
        }

        let updatedItem = null;

        try {
            const res = await fetch('api_menu.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success && result.item) {
                updatedItem = result.item;
            }
        } catch (err) {
            console.warn('API edit error, saving locally:', err);
        }

        // Update in-memory vendorMeals
        const meal = vendorMeals.find(m => m.id === dishId);
        if (meal) {
            meal.title = title;
            meal.category = category;
            meal.price = price;
            meal.quantity = quantity;
            meal.badge = badge;
            meal.desc = desc;
            meal.available = available;
            if (updatedItem && updatedItem.imageUrl) {
                meal.imageUrl = updatedItem.imageUrl;
            } else if (imageUrl) {
                meal.imageUrl = imageUrl;
            }
        }

        // Update localStorage
        const customMeals = JSON.parse(localStorage.getItem('unifood_custom_meals') || '[]');
        const cIdx = customMeals.findIndex(m => m.id === dishId);
        if (cIdx !== -1) {
            customMeals[cIdx].title = title;
            customMeals[cIdx].category = category;
            customMeals[cIdx].price = price;
            customMeals[cIdx].quantity = quantity;
            customMeals[cIdx].badge = badge;
            customMeals[cIdx].desc = desc;
            customMeals[cIdx].available = available;
            if (meal && meal.imageUrl) customMeals[cIdx].imageUrl = meal.imageUrl;
        } else if (meal) {
            customMeals.unshift(meal);
        }
        localStorage.setItem('unifood_custom_meals', JSON.stringify(customMeals));

        closeEditDishModal();
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Changes ↗';
        }

        renderVendorMenu();
        showToast(`✅ "${title}" updated successfully!`, null);
    }

    async function handleAddNewDish(e) {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Adding dish...';
        }

        const title = document.getElementById('new-dish-title').value.trim();
        const category = document.getElementById('new-dish-category').value;
        const price = parseFloat(document.getElementById('new-dish-price').value) || 5.00;
        const quantity = parseInt(document.getElementById('new-dish-quantity').value, 10) || 50;
        const prep = parseInt(document.getElementById('new-dish-prep').value, 10) || 8;
        const badge = document.getElementById('new-dish-badge').value.trim().toUpperCase() || 'NEW ITEM';
        const desc = document.getElementById('new-dish-desc').value.trim() || 'Freshly made at campus stall.';
        const fileInput = document.getElementById('new-dish-file');
        const urlInput = document.getElementById('new-dish-url');
        const imageUrl = urlInput ? urlInput.value.trim() : '';

        const currentStall = CURRENT_VENDOR_STALL;
        const currentCampus = CURRENT_VENDOR_CAMPUS;

        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('title', title);
        formData.append('category', category);
        formData.append('price', price);
        formData.append('quantity', quantity);
        formData.append('prep_mins', prep);
        formData.append('badge', badge);
        formData.append('description', desc);
        formData.append('image_url', imageUrl);

        if (fileInput && fileInput.files && fileInput.files[0]) {
            formData.append('photo', fileInput.files[0]);
        }

        let newMeal = null;

        // 1. Submit to PostgreSQL database via api_menu.php
        try {
            const response = await fetch('api_menu.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.success && result.item) {
                newMeal = result.item;
            }
        } catch (err) {
            console.warn('API error, saving locally:', err);
        }

        // Fallback meal object if offline
        if (!newMeal) {
            newMeal = {
                id: 'custom_' + Date.now(),
                sellerId: CURRENT_VENDOR_ID,
                title: title,
                stall: currentStall + ' · ' + currentCampus,
                category: category,
                badge: badge,
                price: price,
                prepMins: prep,
                quantity: quantity,
                imageUrl: imageUrl,
                rating: 5.0,
                popularRank: 1,
                desc: desc,
                available: true
            };
        }

        if (!newMeal.svg) {
            newMeal.svg = typeof getDishSvg === 'function' ? getDishSvg(category) : `<svg width="78" height="78" viewBox="0 0 64 64" fill="none"><circle cx="32" cy="32" r="26" fill="#e8f3e5"/><text x="32" y="38" font-size="22" text-anchor="middle">🍲</text></svg>`;
        }

        // 2. Save into localStorage tagged with this vendor
        const customMeals = JSON.parse(localStorage.getItem('unifood_custom_meals') || '[]');
        customMeals.unshift(newMeal);
        localStorage.setItem('unifood_custom_meals', JSON.stringify(customMeals));

        // 3. Prepend to vendorMeals
        vendorMeals.unshift(newMeal);

        // 4. Reset modal form
        closeAddDishModal();
        e.target.reset();
        const previewEl = document.getElementById('new-dish-preview');
        const wrapEl = document.getElementById('new-dish-preview-wrap');
        if (previewEl) previewEl.style.display = 'none';
        if (wrapEl) wrapEl.style.display = 'none';

        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Dish to Menu ↗';
        }

        renderVendorMenu();
        showToast(`🎉 "${title}" added to ${CURRENT_VENDOR_STALL} menu!`, null);
    }

    // Init on load
    document.addEventListener('DOMContentLoaded', () => {
        initVendorMeals();
        renderVendorMenu();
        updateStallUI();
        renderVendorOrders();
    });
</script>

<?php require_once 'footer.php'; ?>

