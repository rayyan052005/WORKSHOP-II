@include('partials.header')


<!-- Vendor Header -->
<div class="page-header">
    <div class="header-title">
        <div style="display:inline-flex; align-items:center; gap:8px; margin-bottom:4px;">
            <span style="background:#eaf2e3; color:var(--forest); font-size:11px; font-weight:800; padding:3px 9px; border-radius:999px; letter-spacing:0.8px;">VENDOR PORTAL</span>
            <span style="color:#78867a; font-size:12.5px; font-weight:600;">Cafe Abang Mirul · Main Campus</span>
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
            <span class="stat-trend positive">+18% today</span>
        </div>
        <div class="stat-value" id="vendor-stat-sales">RM 386.50</div>
        <div class="stat-label">Today's Revenue</div>
    </div>

    <div class="vendor-stat-card">
        <div class="stat-top">
            <span class="stat-icon">⏳</span>
            <span class="stat-badge" id="vendor-stat-active-badge">Live</span>
        </div>
        <div class="stat-value" id="vendor-stat-active">3</div>
        <div class="stat-label">Active Orders in Kitchen</div>
    </div>

    <div class="vendor-stat-card">
        <div class="stat-top">
            <span class="stat-icon">🛵</span>
            <span class="stat-trend neutral">3 Hostels</span>
        </div>
        <div class="stat-value" id="vendor-stat-deliveries">12</div>
        <div class="stat-label">Campus Runner Deliveries</div>
    </div>

    <div class="vendor-stat-card">
        <div class="stat-top">
            <span class="stat-icon">⭐</span>
            <span class="stat-trend positive">98% Positive</span>
        </div>
        <div class="stat-value">4.9 <small style="font-size:14px; color:#78867a;">/ 5.0</small></div>
        <div class="stat-label">Student Satisfaction</div>
    </div>
</div>

<!-- Tabs: Orders vs Menu Management -->
<div class="vendor-tabs">
    <button type="button" class="vendor-tab active" id="tab-btn-orders" onclick="switchVendorTab('orders')">
        📋 Live Kitchen Orders <span class="tab-counter" id="active-orders-count">3</span>
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
    <div class="modal-card" style="width: min(540px, 100%);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3>Add New Stall Dish</h3>
            <button type="button" class="close-drawer-btn" onclick="closeAddDishModal()">✕</button>
        </div>
        <p>List a new meal or drink available for student orders.</p>

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
                    <input type="number" id="new-dish-price" step="0.10" min="1" required placeholder="8.50"
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Prep Time (mins)</label>
                    <input type="number" id="new-dish-prep" min="2" max="60" value="8"
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Badge Tag</label>
                    <input type="text" id="new-dish-badge" placeholder="e.g. SPECIAL CHEF"
                           style="width:100%; height:42px; border:1px solid #d8dfd5; border-radius:10px; padding:0 12px;">
                </div>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:5px;">Short Description</label>
                <textarea id="new-dish-desc" rows="2" placeholder="Fragrant rice, spicy sambal, crispy anchovies & peanuts..."
                          style="width:100%; border:1px solid #d8dfd5; border-radius:10px; padding:10px 12px; font-family:inherit;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-outline" onclick="closeAddDishModal()">Cancel</button>
                <button type="submit" class="banner-cta">Save Dish to Menu ↗</button>
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
    // Vendor state management
    let vendorStallOpen = localStorage.getItem('unifood_stall_open') !== 'false';
    let currentVendorFilter = 'all';

    // Default vendor orders queue
    let vendorOrders = JSON.parse(localStorage.getItem('unifood_vendor_orders') || 'null');
    if (!vendorOrders) {
        vendorOrders = [
            {
                code: 'UF-842',
                customer: 'Ahmad Faiz (Student)',
                mode: 'delivery',
                spot: 'Kolej Kediaman Satria',
                time: '12:30 PM (Lunch break)',
                status: 'preparing', // 'preparing', 'ready', 'completed'
                items: [
                    { title: 'Crispy Ayam Geprek Sambal Ijo', qty: 1, price: 9.50 },
                    { title: 'Iced Teh O Ais Limau Padu', qty: 1, price: 2.80 }
                ],
                total: 14.30,
                placedAt: '12:04 PM'
            },
            {
                code: 'UF-819',
                customer: 'Nurul Huda (Lecturer)',
                mode: 'pickup',
                spot: 'Cafe Abang Mirul Counter',
                time: 'ASAP (10–12 mins)',
                status: 'ready',
                items: [
                    { title: 'Signature Grilled Chicken Chop', qty: 1, price: 12.90 }
                ],
                total: 12.90,
                placedAt: '12:10 PM'
            },
            {
                code: 'UF-790',
                customer: 'Daniel Lee (Student)',
                mode: 'delivery',
                spot: 'Kolej Kediaman Al-Jazari',
                time: 'ASAP',
                status: 'preparing',
                items: [
                    { title: 'Roti Bakar Pandan Kaya & Half-Boiled Eggs', qty: 2, price: 5.20 }
                ],
                total: 10.40,
                placedAt: '12:14 PM'
            }
        ];
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
                    customer: 'Campus Student',
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
                            ${ord.items.map(it => `
                                <div class="v-order-item-row">
                                    <span><strong>${it.qty}x</strong> ${it.title}</span>
                                    <span>RM ${(it.price * it.qty).toFixed(2)}</span>
                                </div>
                            `).join('')}
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
        if (countEl) countEl.textContent = MEALS.length;

        container.innerHTML = MEALS.map(meal => {
            const isAvailable = meal.available !== false;
            return `
                <div class="v-menu-card">
                    <div>
                        <div class="v-menu-top">
                            <div class="v-menu-thumb">${meal.svg}</div>
                            <div>
                                <h4 style="font-size:14px; font-weight:700; color:var(--text-dark); margin-bottom:2px;">${meal.title}</h4>
                                <div style="font-size:11.5px; color:#78867a;">${meal.stall}</div>
                                <div style="font-size:14px; font-weight:800; color:var(--forest); margin-top:4px;">RM ${meal.price.toFixed(2)}</div>
                            </div>
                        </div>
                        <p style="font-size:12px; color:#68786a; line-height:1.4;">${meal.desc}</p>
                    </div>

                    <div class="v-stock-toggle">
                        <span style="font-size:12px; font-weight:700; color:${isAvailable ? '#256531' : '#b23725'};">
                            ${isAvailable ? '🟢 In Stock (Available)' : '🔴 Sold Out (Habis)'}
                        </span>
                        <button type="button" class="btn-outline" style="padding:4px 10px; font-size:11.5px;"
                                onclick="toggleMealStock('${meal.id}')">
                            ${isAvailable ? 'Set Sold Out' : 'Set Available'}
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function toggleMealStock(mealId) {
        const meal = MEALS.find(m => m.id === mealId);
        if (!meal) return;
        meal.available = !(meal.available !== false);
        renderVendorMenu();
        showToast(`${meal.title}: ${meal.available ? 'In Stock' : 'Sold Out'}`, null);
    }

    function openAddDishModal() {
        const modal = document.getElementById('add-dish-modal');
        if (modal) modal.classList.add('open');
    }

    function closeAddDishModal() {
        const modal = document.getElementById('add-dish-modal');
        if (modal) modal.classList.remove('open');
    }

    function handleAddNewDish(e) {
        e.preventDefault();
        const title = document.getElementById('new-dish-title').value.trim();
        const category = document.getElementById('new-dish-category').value;
        const price = parseFloat(document.getElementById('new-dish-price').value) || 5.00;
        const prep = parseInt(document.getElementById('new-dish-prep').value, 10) || 8;
        const badge = document.getElementById('new-dish-badge').value.trim().toUpperCase() || 'NEW ITEM';
        const desc = document.getElementById('new-dish-desc').value.trim() || 'Freshly made at campus stall.';

        const newId = 'custom_' + Date.now();
        const newMeal = {
            id: newId,
            title,
            category,
            stall: 'Cafe Abang Mirul',
            badge,
            price,
            prepMins: prep,
            rating: 5.0,
            popularRank: 99,
            desc,
            available: true,
            svg: `<svg width="78" height="78" viewBox="0 0 64 64" fill="none"><circle cx="32" cy="32" r="26" fill="#e8f3e5"/><text x="32" y="38" font-size="22" text-anchor="middle">🍲</text></svg>`
        };

        MEALS.unshift(newMeal);
        closeAddDishModal();
        renderVendorMenu();
        showToast(`🎉 "${title}" has been added to your menu!`, null);
    }

    // Init on load
    document.addEventListener('DOMContentLoaded', () => {
        updateStallUI();
        renderVendorOrders();
    });
</script>

@include('partials.footer')

