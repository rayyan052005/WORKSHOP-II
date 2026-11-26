function getDishSvg(cat) {
    if (cat === 'healthy') {
        return `<svg width="78" height="78" viewBox="0 0 64 64" fill="none"><ellipse cx="32" cy="52" rx="18" ry="4" fill="rgba(0,0,0,0.1)"/><path d="M14 30h36c0 12-8 19-18 19S14 42 14 30Z" fill="#4f7756"/><circle cx="25" cy="27" r="7" fill="#68bf54"/><circle cx="36" cy="26" r="7.5" fill="#c97b3b"/><circle cx="32" cy="30" r="4" fill="#ffd84d"/></svg>`;
    }
    if (cat === 'western') {
        return `<svg width="78" height="78" viewBox="0 0 64 64" fill="none"><ellipse cx="32" cy="52" rx="18" ry="4" fill="rgba(0,0,0,0.1)"/><path d="M18 28c0-7 6-12 14-12s14 5 14 12H18Z" fill="#e5a95c"/><rect x="16" y="28" width="32" height="4" rx="2" fill="#5dbb47"/><rect x="17" y="32" width="30" height="6" rx="3" fill="#b5592a"/><rect x="18" y="38" width="28" height="6" rx="3" fill="#e5a95c"/></svg>`;
    }
    if (cat === 'drinks') {
        return `<svg width="78" height="78" viewBox="0 0 64 64" fill="none"><ellipse cx="32" cy="53" rx="14" ry="3.5" fill="rgba(0,0,0,0.1)"/><rect x="21" y="18" width="22" height="32" rx="5" fill="#ffffff"/><rect x="21" y="26" width="22" height="12" fill="#7bc462"/><rect x="21" y="18" width="22" height="8" rx="4" fill="#8c5838"/><path d="M35 11l-4 15" stroke="#234b35" stroke-width="2.5" stroke-linecap="round"/></svg>`;
    }
    if (cat === 'snacks') {
        return `<svg width="78" height="78" viewBox="0 0 64 64" fill="none"><ellipse cx="32" cy="52" rx="18" ry="4" fill="rgba(0,0,0,0.1)"/><rect x="18" y="21" width="22" height="22" rx="4" fill="#c98c50"/><rect x="24" y="25" width="22" height="22" rx="4" fill="#ddb078"/><rect x="30" y="31" width="10" height="7" rx="1.5" fill="#ffe169"/></svg>`;
    }
    return `<svg width="78" height="78" viewBox="0 0 64 64" fill="none"><ellipse cx="32" cy="52" rx="18" ry="4" fill="rgba(0,0,0,0.1)"/><circle cx="32" cy="31" r="18" fill="#ffffff"/><circle cx="29" cy="30" r="10" fill="#f7f4eb"/><path d="M31 23c5-2 11 1 12 6 1 5-4 10-9 10-5 0-8-4-7-9 0-3 2-6 4-7Z" fill="#c85a28"/><circle cx="25" cy="36" r="4.5" fill="#ffd84d"/></svg>`;
}

const DEFAULT_MEALS = [
    {
        id: 'nasi-lemak',
        title: 'Nasi Lemak Ayam Berempah',
        stall: 'Warung Bonda · Central court',
        category: 'local',
        badge: 'CAMPUS FAVORITE',
        theme: 'warm-peach',
        price: 8.50,
        prepMins: 8,
        rating: 4.9,
        popularRank: 1,
        desc: 'Fragrant coconut rice, house sambal, crispy spiced chicken & anchovies.',
        svg: `<svg width="78" height="78" viewBox="0 0 64 64" fill="none">
                <ellipse cx="32" cy="52" rx="18" ry="4" fill="rgba(0,0,0,0.1)"/>
                <circle cx="32" cy="31" r="18" fill="#ffffff"/>
                <circle cx="29" cy="30" r="10" fill="#f7f4eb"/>
                <path d="M31 23c5-2 11 1 12 6 1 5-4 10-9 10-5 0-8-4-7-9 0-3 2-6 4-7Z" fill="#c85a28"/>
                <circle cx="25" cy="36" r="4.5" fill="#ffd84d"/>
                <path d="M18 27c2-3 6-3 8 0" stroke="#4ca64c" stroke-width="3" stroke-linecap="round"/>
              </svg>`
    },
    {
        id: 'teriyaki-bowl',
        title: 'Teriyaki Chicken Harvest Bowl',
        stall: 'Green Bowl · Level 1',
        category: 'healthy',
        badge: 'FEEL-GOOD FOOD',
        theme: 'sage-green',
        price: 11.90,
        prepMins: 10,
        rating: 4.8,
        popularRank: 2,
        desc: 'Warm brown rice, charred broccoli, edamame, soft egg & sesame glaze.',
        svg: `<svg width="78" height="78" viewBox="0 0 64 64" fill="none">
                <ellipse cx="32" cy="52" rx="18" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M14 30h36c0 12-8 19-18 19S14 42 14 30Z" fill="#4f7756"/>
                <circle cx="25" cy="27" r="7" fill="#68bf54"/>
                <circle cx="36" cy="26" r="7.5" fill="#c97b3b"/>
                <circle cx="32" cy="30" r="4" fill="#ffd84d"/>
              </svg>`
    },
    {
        id: 'crispy-burger',
        title: 'Crispy Chicken Melt Burger',
        stall: 'Student Burger Lab',
        category: 'western',
        badge: 'BESTSELLER',
        theme: 'warm-sand',
        price: 9.80,
        prepMins: 9,
        rating: 4.9,
        popularRank: 3,
        desc: 'Toasted brioche bun, cheddar melt, crispy thigh cutlet, spicy mayo & fries.',
        svg: `<svg width="78" height="78" viewBox="0 0 64 64" fill="none">
                <ellipse cx="32" cy="52" rx="18" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M18 28c0-7 6-12 14-12s14 5 14 12H18Z" fill="#e5a95c"/>
                <rect x="16" y="28" width="32" height="4" rx="2" fill="#5dbb47"/>
                <rect x="17" y="32" width="30" height="6" rx="3" fill="#b5592a"/>
                <rect x="18" y="38" width="28" height="6" rx="3" fill="#e5a95c"/>
              </svg>`
    },
    {
        id: 'iced-matcha',
        title: 'Oat Milk Dirty Matcha Latte',
        stall: 'Campus Brew Bar',
        category: 'drinks',
        badge: 'STUDY FUEL',
        theme: 'soft-mint',
        price: 6.90,
        prepMins: 4,
        rating: 4.9,
        popularRank: 4,
        desc: 'Whisked Uji matcha layered over creamy oat milk and a double espresso shot.',
        svg: `<svg width="78" height="78" viewBox="0 0 64 64" fill="none">
                <ellipse cx="32" cy="53" rx="14" ry="3.5" fill="rgba(0,0,0,0.1)"/>
                <rect x="21" y="18" width="22" height="32" rx="5" fill="#ffffff"/>
                <rect x="21" y="26" width="22" height="12" fill="#7bc462"/>
                <rect x="21" y="18" width="22" height="8" rx="4" fill="#8c5838"/>
                <path d="M35 11l-4 15" stroke="#234b35" stroke-width="2.5" stroke-linecap="round"/>
              </svg>`
    },
    {
        id: 'char-kuey-teow',
        title: 'Wok-Hei Penang Char Kuey Teow',
        stall: 'Uncle Lim Wok · Central court',
        category: 'local',
        badge: 'HOT FROM WOK',
        theme: 'cream-gold',
        price: 7.90,
        prepMins: 7,
        rating: 4.7,
        popularRank: 5,
        desc: 'Smoky flat rice noodles stir-fried with prawns, chives, bean sprouts & egg.',
        svg: `<svg width="78" height="78" viewBox="0 0 64 64" fill="none">
                <ellipse cx="32" cy="52" rx="18" ry="4" fill="rgba(0,0,0,0.1)"/>
                <circle cx="32" cy="31" r="17" fill="#8c4a27"/>
                <path d="M22 28c4-4 16-4 20 2-4 5-16 5-20-2Z" fill="#d98c4a"/>
                <circle cx="28" cy="27" r="3.5" fill="#ff735c"/>
                <circle cx="37" cy="32" r="3.5" fill="#ff735c"/>
              </svg>`
    },
    {
        id: 'kaya-toast-set',
        title: 'Crispy Kaya Butter Toast & Eggs',
        stall: 'Kopitiam Kita',
        category: 'snacks',
        badge: 'QUICK BITE',
        theme: 'berry-cream',
        price: 5.20,
        prepMins: 5,
        rating: 4.8,
        popularRank: 6,
        desc: 'Charcoal-toasted bread with pandan kaya, cold butter slab & two soft-boiled eggs.',
        svg: `<svg width="78" height="78" viewBox="0 0 64 64" fill="none">
                <ellipse cx="32" cy="52" rx="18" ry="4" fill="rgba(0,0,0,0.1)"/>
                <rect x="18" y="21" width="22" height="22" rx="4" fill="#c98c50"/>
                <rect x="24" y="25" width="22" height="22" rx="4" fill="#ddb078"/>
                <rect x="30" y="31" width="10" height="7" rx="1.5" fill="#ffe169"/>
              </svg>`
    }
];

let MEALS = [...DEFAULT_MEALS];
let currentStallFilter = 'all';

function selectStallFilter(stallName, btn) {
    currentStallFilter = stallName || 'all';
    document.querySelectorAll('.vendor-filter-pill').forEach(p => p.classList.remove('active'));
    if (btn) {
        btn.classList.add('active');
    } else {
        const matchingBtn = document.querySelector(`.vendor-filter-pill[data-stall="${currentStallFilter}"]`);
        if (matchingBtn) matchingBtn.classList.add('active');
    }
    if (typeof renderProducts === 'function') renderProducts();
}

// Sync meals from PostgreSQL Server and localStorage
function syncMealsData() {
    const serverItems = window.SERVER_MENU_ITEMS || [];
    const formattedServer = serverItems.map(item => ({
        id: 'db_' + item.id,
        sellerId: item.seller_id ? parseInt(item.seller_id, 10) : (item.sellerId ? parseInt(item.sellerId, 10) : null),
        title: item.title,
        stall: item.stall ? `${item.stall} · ${item.campus_location || 'Campus'}` : 'Campus Food Stall',
        category: item.category || 'local',
        badge: item.badge || 'CAMPUS PICK',
        theme: item.category === 'healthy' ? 'sage-green' : (item.category === 'western' ? 'warm-sand' : (item.category === 'drinks' ? 'soft-mint' : (item.category === 'snacks' ? 'berry-cream' : 'warm-peach'))),
        price: parseFloat(item.price),
        prepMins: parseInt(item.prepMins, 10) || 8,
        quantity: item.quantity !== undefined ? parseInt(item.quantity, 10) : 50,
        imageUrl: item.imageUrl || '',
        rating: 5.0,
        popularRank: 1,
        desc: item.desc || 'Freshly made at campus stall.',
        available: item.available !== false && (item.quantity === undefined || parseInt(item.quantity, 10) > 0),
        svg: getDishSvg(item.category)
    }));

    const customLocal = JSON.parse(localStorage.getItem('unifood_custom_meals') || '[]');
    const combined = [];
    const seenTitles = new Set();

    // 1. Add localStorage custom items (instant client-side add)
    customLocal.forEach(m => {
        const key = m.title.trim().toLowerCase();
        if (!seenTitles.has(key)) {
            seenTitles.add(key);
            if (!m.svg) m.svg = getDishSvg(m.category);
            if (m.quantity === undefined) m.quantity = 50;
            if (m.imageUrl === undefined) m.imageUrl = '';
            combined.push(m);
        }
    });

    // 2. Add PostgreSQL server items
    formattedServer.forEach(m => {
        const key = m.title.trim().toLowerCase();
        if (!seenTitles.has(key)) {
            seenTitles.add(key);
            combined.push(m);
        }
    });

    // 3. Add default campus meals
    DEFAULT_MEALS.forEach(m => {
        const key = m.title.trim().toLowerCase();
        if (!seenTitles.has(key)) {
            seenTitles.add(key);
            if (m.quantity === undefined) m.quantity = 50;
            if (m.imageUrl === undefined) m.imageUrl = '';
            combined.push(m);
        }
    });

    MEALS = combined;
}

// Initialize synchronized meals
syncMealsData();

const BANNER_SLIDES = [
    {
        eyebrow: 'LESS WAITING. MORE LIVING.',
        heading: 'Big appetite.<br>Small student budget.',
        desc: 'Skip the queue with self-pickup or get it delivered straight to your hostel or faculty.',
        cta: 'Find your next bite',
        action: 'scroll'
    },
    {
        eyebrow: 'CAMPUS RUNNER DELIVERY',
        heading: 'Studying at the library<br>or hostel room?',
        desc: 'Switch to Campus Delivery for just RM 2.00 (Free for orders RM 25+) and let our student runners bring it to you.',
        cta: 'Switch to Delivery 🛵',
        action: 'delivery'
    },
    {
        eyebrow: 'GREEN CAMPUS INITIATIVE',
        heading: 'Bring a container.<br>Save RM 0.50 instantly.',
        desc: 'Join hundreds of students cutting single-use packaging every lunch break.',
        cta: 'Activate eco pledge',
        action: 'eco'
    }
];

let currentCategory = 'all';
let currentSlide = 0;
let currentCampus = localStorage.getItem('unifood_campus') || 'Main Campus';
let currentCourt = localStorage.getItem('unifood_court') || 'Central food court';
let orderMode = localStorage.getItem('unifood_mode') || 'pickup'; // 'pickup' or 'delivery'
let deliverySpot = localStorage.getItem('unifood_delivery_spot') || 'Kolej Kediaman Satria';
let deliveryNote = localStorage.getItem('unifood_delivery_note') || '';
const CURRENT_USER_ID = (window.CURRENT_USER && window.CURRENT_USER.id) ? String(window.CURRENT_USER.id) : 'guest';
const CURRENT_USER_NAME = (window.CURRENT_USER && window.CURRENT_USER.fullName) ? String(window.CURRENT_USER.fullName) : 'Campus Student';
const USER_FAVS_KEY = 'unifood_favs_' + CURRENT_USER_ID;
const USER_BAG_KEY = 'unifood_bag_' + CURRENT_USER_ID;

// Reset previous preview demo orders for all users to 0
if (!localStorage.getItem('unifood_orders_cleared_zero')) {
    localStorage.removeItem('unifood_orders');
    localStorage.removeItem('unifood_vendor_orders');
    localStorage.setItem('unifood_orders_cleared_zero', 'true');
}

let favorites = JSON.parse(localStorage.getItem(USER_FAVS_KEY) || '[]');
let bag = JSON.parse(localStorage.getItem(USER_BAG_KEY) || '{}');
let orders = JSON.parse(localStorage.getItem('unifood_orders') || '[]');
if (Array.isArray(orders) && orders.length > 0) {
    // Only keep genuine orders that have an explicit userId owner and are not demo codes
    orders = orders.filter(o => o && o.userId && !['UF-842', 'UF-819', 'UF-790', 'UF-880', 'UF-765'].includes(o.code));
    localStorage.setItem('unifood_orders', JSON.stringify(orders));
}
let ecoPledge = JSON.parse(localStorage.getItem('unifood_eco') || 'false');
let toastTimer = null;

function getMyOrders() {
    return orders.filter(o => o && String(o.userId) === CURRENT_USER_ID);
}

function saveState() {
    localStorage.setItem(USER_FAVS_KEY, JSON.stringify(favorites));
    localStorage.setItem(USER_BAG_KEY, JSON.stringify(bag));
    localStorage.setItem('unifood_orders', JSON.stringify(orders));
    localStorage.setItem('unifood_eco', JSON.stringify(ecoPledge));
    localStorage.setItem('unifood_campus', currentCampus);
    localStorage.setItem('unifood_court', currentCourt);
    localStorage.setItem('unifood_mode', orderMode);
    localStorage.setItem('unifood_delivery_spot', deliverySpot);
    localStorage.setItem('unifood_delivery_note', deliveryNote);
}

function showToast(message, actionText = 'View Bag', actionFn = openBagDrawer) {
    const toast = document.getElementById('toast');
    const textEl = document.getElementById('toast-text');
    const btnEl = document.getElementById('toast-btn');

    if (!toast || !textEl || !btnEl) return;

    textEl.textContent = message;
    if (actionText) {
        btnEl.style.display = 'inline-block';
        btnEl.textContent = actionText;
        btnEl.onclick = actionFn;
    } else {
        btnEl.style.display = 'none';
    }

    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        toast.classList.remove('show');
    }, 3200);
}

/* PICKUP VS DELIVERY MODE */
function setOrderMode(mode, silent = false) {
    orderMode = mode;
    saveState();

    // Update top navbar buttons
    const topPickup = document.getElementById('top-mode-pickup');
    const topDelivery = document.getElementById('top-mode-delivery');
    if (topPickup) topPickup.classList.toggle('active', mode === 'pickup');
    if (topDelivery) topDelivery.classList.toggle('active', mode === 'delivery');

    // Update drawer tabs
    const tabPickup = document.getElementById('drawer-tab-pickup');
    const tabDelivery = document.getElementById('drawer-tab-delivery');
    if (tabPickup) tabPickup.classList.toggle('active', mode === 'pickup');
    if (tabDelivery) tabDelivery.classList.toggle('active', mode === 'delivery');

    // Toggle panels in bag drawer
    const pickupPanel = document.getElementById('bag-pickup-panel');
    const deliveryPanel = document.getElementById('bag-delivery-panel');
    if (pickupPanel) pickupPanel.style.display = mode === 'pickup' ? 'block' : 'none';
    if (deliveryPanel) deliveryPanel.style.display = mode === 'delivery' ? 'block' : 'none';

    // Update sub-location text in top bar
    updateLocationHeader();
    updateBagUI();

    if (!silent) {
        if (mode === 'delivery') {
            showToast(`🛵 Switched to Campus Delivery (${deliverySpot})`, 'Change Spot', openBagDrawer);
        } else {
            showToast(`🏃 Switched to Self Pickup at ${currentCourt}`, null);
        }
    }
}

function saveDeliverySpot() {
    const spotSelect = document.getElementById('delivery-spot-select');
    const noteInput = document.getElementById('delivery-note-input');
    if (spotSelect) deliverySpot = spotSelect.value;
    if (noteInput) deliveryNote = noteInput.value.trim();
    saveState();
    updateLocationHeader();
}

function updateLocationHeader() {
    const nameEl = document.getElementById('current-campus-name');
    const courtEl = document.getElementById('current-court-name');
    const drawerLabel = document.getElementById('drawer-campus-label');
    const drawerDelivCampus = document.getElementById('drawer-delivery-campus');

    if (nameEl) nameEl.textContent = currentCampus;
    if (drawerLabel) drawerLabel.textContent = `${currentCampus} · ${currentCourt}`;
    if (drawerDelivCampus) drawerDelivCampus.textContent = currentCampus;

    if (courtEl) {
        if (orderMode === 'delivery') {
            courtEl.textContent = `Deliver to: ${deliverySpot}`;
        } else {
            courtEl.textContent = currentCourt;
        }
    }
}

/* CAMPUS SELECTOR */
function toggleCampusMenu(e) {
    if (e) e.stopPropagation();
    const menu = document.getElementById('campus-menu');
    if (menu) menu.classList.toggle('open');
}

function selectCampus(campus, court, btn) {
    currentCampus = campus;
    currentCourt = court;
    saveState();
    updateLocationHeader();

    document.querySelectorAll('.campus-option').forEach(opt => {
        opt.classList.remove('selected');
        const strong = opt.querySelector('strong');
        if (strong) strong.innerHTML = strong.textContent.replace('✓', '').trim();
    });

    if (btn) {
        btn.classList.add('selected');
        const strong = btn.querySelector('strong');
        if (strong) strong.innerHTML = `${campus} <span>✓</span>`;
    }

    const menu = document.getElementById('campus-menu');
    if (menu) menu.classList.remove('open');

    if (typeof renderProducts === 'function') renderProducts();
    showToast(`Switched campus to ${campus}`, null);
}

document.addEventListener('click', () => {
    const menu = document.getElementById('campus-menu');
    if (menu) menu.classList.remove('open');
});

/* HERO BANNER CAROUSEL */
function setBannerSlide(idx) {
    currentSlide = idx;
    const slide = BANNER_SLIDES[idx];
    const eyebrow = document.getElementById('banner-eyebrow');
    const heading = document.getElementById('banner-heading');
    const desc = document.getElementById('banner-desc');
    const cta = document.getElementById('banner-cta-text');

    if (eyebrow) eyebrow.textContent = slide.eyebrow;
    if (heading) heading.innerHTML = slide.heading;
    if (desc) desc.textContent = slide.desc;
    if (cta) cta.textContent = slide.cta;

    document.querySelectorAll('.banner-dot').forEach((d, i) => {
        d.classList.toggle('active', i === idx);
    });
}

function nextBannerSlide() {
    setBannerSlide((currentSlide + 1) % BANNER_SLIDES.length);
}

function prevBannerSlide() {
    setBannerSlide((currentSlide - 1 + BANNER_SLIDES.length) % BANNER_SLIDES.length);
}

function handleBannerAction() {
    const action = BANNER_SLIDES[currentSlide].action;
    if (action === 'scroll') {
        const controls = document.getElementById('menu-controls');
        const search = document.getElementById('search-input');
        if (controls) controls.scrollIntoView({ behavior: 'smooth' });
        if (search) search.focus();
    } else if (action === 'delivery') {
        setOrderMode('delivery');
        openBagDrawer();
    } else if (action === 'eco') {
        if (!ecoPledge) toggleEcoPledge();
        openBagDrawer();
    }
}

/* ECO CONTAINER PLEDGE */
function updateEcoUI() {
    const card = document.getElementById('sidebar-eco-card');
    const label = document.getElementById('eco-pill-label');
    const checkbox = document.getElementById('bag-eco-checkbox');

    if (card) card.classList.toggle('active', ecoPledge);
    if (label) label.textContent = ecoPledge ? '✓ Active (-RM 0.50)' : 'Tap to join';
    if (checkbox) checkbox.checked = ecoPledge;
    updateBagUI();
}

function toggleEcoPledge() {
    ecoPledge = !ecoPledge;
    saveState();
    updateEcoUI();
    showToast(
        ecoPledge
            ? '🌱 BYO Container active! RM 0.50 off your bag.'
            : 'BYO Container discount removed.',
        'View Bag'
    );
}

function toggleEcoFromBag(checked) {
    ecoPledge = checked;
    saveState();
    updateEcoUI();
}

/* SEARCH, SORT & FILTER */
function selectCategory(cat, btn) {
    currentCategory = cat;
    document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
    if (btn) btn.classList.add('active');
    if (typeof renderProducts === 'function') renderProducts();
}

function handleSearchInput() {
    const searchInput = document.getElementById('search-input');
    const clearBtn = document.getElementById('clear-search-btn');
    if (!searchInput || !clearBtn) return;
    const val = searchInput.value.trim();
    clearBtn.classList.toggle('visible', val.length > 0);
    if (typeof renderProducts === 'function') renderProducts();
}

function clearSearch() {
    const searchInput = document.getElementById('search-input');
    const clearBtn = document.getElementById('clear-search-btn');
    if (searchInput) searchInput.value = '';
    if (clearBtn) clearBtn.classList.remove('visible');
    if (typeof renderProducts === 'function') renderProducts();
}

function resetFilters() {
    currentCategory = 'all';
    currentStallFilter = 'all';
    const searchInput = document.getElementById('search-input');
    const clearBtn = document.getElementById('clear-search-btn');
    const sortSelect = document.getElementById('sort-select');

    if (searchInput) searchInput.value = '';
    if (clearBtn) clearBtn.classList.remove('visible');
    if (sortSelect) sortSelect.value = 'popular';

    document.querySelectorAll('.cat-pill').forEach(p => {
        p.classList.toggle('active', p.getAttribute('data-category') === 'all');
    });

    document.querySelectorAll('.vendor-filter-pill').forEach(p => {
        p.classList.toggle('active', (p.getAttribute('data-stall') || 'all') === 'all');
    });

    if (typeof renderProducts === 'function') renderProducts();
    showToast('Showing all fresh campus picks ✦', null);
}

/* FAVORITES */
function toggleFavorite(mealId, e) {
    if (e) e.stopPropagation();
    const idx = favorites.indexOf(mealId);
    const meal = MEALS.find(m => m.id === mealId);
    if (idx > -1) {
        favorites.splice(idx, 1);
        showToast(`Removed ${meal ? meal.title : 'item'} from Favorites`, null);
    } else {
        favorites.push(mealId);
        showToast(`♥ Saved ${meal ? meal.title : 'item'} to Favorites`, 'View Favorites', () => window.location.href = 'favorites.php');
    }
    saveState();
    updateBadges();
    if (typeof renderProducts === 'function') renderProducts();
    if (typeof renderFavorites === 'function') renderFavorites();
}

/* RENDER PRODUCT CARD HTML */
function createCardHTML(meal) {
    const isLiked = favorites.includes(meal.id);
    const qtyInBag = bag[meal.id] || 0;
    const isAvailable = meal.available !== false && (meal.quantity === undefined || parseInt(meal.quantity, 10) > 0);
    const timeLabel = orderMode === 'delivery' ? `${meal.prepMins + 10} mins deliv` : `${meal.prepMins} mins pickup`;

    // Photo or SVG Thumbnail
    const visualContent = meal.imageUrl
        ? `<img src="${meal.imageUrl}" alt="${meal.title}" style="width:100%; height:100%; object-fit:cover; border-radius:14px; position:absolute; top:0; left:0;">`
        : meal.svg;

    // Stock Quantity & Availability Badges
    let stockBadge = '';
    if (!isAvailable) {
        stockBadge = `<span style="font-size:10px; font-weight:800; background:#fde8e8; color:#c81e1e; padding:2px 7px; border-radius:999px;">SOLD OUT</span>`;
    } else if (meal.quantity !== undefined && parseInt(meal.quantity, 10) <= 10) {
        stockBadge = `<span style="font-size:10px; font-weight:800; background:#fff2e8; color:#d9531e; padding:2px 7px; border-radius:999px;">Only ${meal.quantity} left</span>`;
    }

    return `
        <article class="product-card ${!isAvailable ? 'sold-out' : ''}" onclick="openMealModal('${meal.id}')" style="${!isAvailable ? 'opacity:0.8;' : ''}">
            <div class="product-thumb ${meal.theme}" style="position:relative; overflow:hidden;">
                <span class="thumb-tag">${meal.badge}</span>
                <button class="like-btn ${isLiked ? 'liked' : ''}" type="button" onclick="toggleFavorite('${meal.id}', event)" title="Save to favorites">
                    ${isLiked ? '♥' : '♡'}
                </button>
                ${visualContent}
            </div>
            <div class="product-body">
                <div class="product-stall-row">
                    <span class="product-stall">${meal.stall}</span>
                    <span class="product-rating">★ ${meal.rating ? meal.rating.toFixed(1) : '5.0'}</span>
                </div>
                <div class="product-title" style="display:flex; align-items:center; justify-content:space-between; gap:6px;">
                    <span>${meal.title}</span>
                    ${stockBadge}
                </div>
                <div class="product-meta">${meal.desc}</div>
                <div class="product-footer">
                    <div>
                        <span class="product-price">RM ${meal.price.toFixed(2)}</span>
                        <span class="product-prep">· ${timeLabel}</span>
                    </div>
                    ${!isAvailable
                        ? `<button class="add-cart-btn disabled" type="button" disabled style="opacity:0.6; cursor:not-allowed; background:#8a968c;">Sold out</button>`
                        : `<button class="add-cart-btn ${qtyInBag > 0 ? 'added' : ''}" type="button" onclick="addToBag('${meal.id}', event)">
                            ${qtyInBag > 0 ? `✓ In bag (${qtyInBag})` : '+ Add'}
                           </button>`
                    }
                </div>
            </div>
        </article>
    `;
}

function renderProducts() {
    const grid = document.getElementById('products-grid');
    if (!grid) return;

    const searchInput = document.getElementById('search-input');
    const sortSelect = document.getElementById('sort-select');
    const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
    const sortBy = sortSelect ? sortSelect.value : 'popular';

    let filtered = MEALS.filter(m => {
        const matchCat = currentCategory === 'all' || m.category === currentCategory;
        const matchStall = currentStallFilter === 'all' || (m.stall && m.stall.toLowerCase().includes(currentStallFilter.toLowerCase()));
        const matchQuery = !query ||
            m.title.toLowerCase().includes(query) ||
            m.stall.toLowerCase().includes(query) ||
            m.desc.toLowerCase().includes(query) ||
            m.badge.toLowerCase().includes(query);
        return matchCat && matchStall && matchQuery;
    });

    filtered.sort((a, b) => {
        if (sortBy === 'price-asc') return a.price - b.price;
        if (sortBy === 'price-desc') return b.price - a.price;
        if (sortBy === 'fast') return a.prepMins - b.prepMins;
        if (sortBy === 'rating') return b.rating - a.rating;
        return a.popularRank - b.popularRank;
    });

    const countEl = document.getElementById('catalog-count');
    if (countEl) {
        const modeText = orderMode === 'delivery' ? 'available for campus delivery' : `at ${currentCampus}`;
        countEl.textContent = `${filtered.length} delicious pick${filtered.length === 1 ? '' : 's'} ${modeText}.`;
    }

    if (filtered.length === 0) {
        grid.innerHTML = `
            <div class="empty-state">
                <div style="font-size:32px;">🍽️</div>
                <h4>No campus meals match your search</h4>
                <p>Try clearing your search filter or switching to All food.</p>
                <button type="button" onclick="resetFilters()">Reset all filters</button>
            </div>
        `;
        return;
    }

    grid.innerHTML = filtered.map(createCardHTML).join('');
}

function renderFavorites() {
    const favGrid = document.getElementById('favorites-grid');
    if (!favGrid) return;

    const favMeals = MEALS.filter(m => favorites.includes(m.id));
    if (favMeals.length === 0) {
        favGrid.innerHTML = `
            <div class="empty-state">
                <div style="font-size:32px;">💚</div>
                <h4>No favorites saved yet</h4>
                <p>Tap the heart icon (♡) on any meal card to save it here for quick ordering between classes.</p>
                <a href="index.php">Browse campus food</a>
            </div>
        `;
        return;
    }
    favGrid.innerHTML = favMeals.map(createCardHTML).join('');
}

/* BAG & CHECKOUT */
function addToBag(mealId, e) {
    if (e) e.stopPropagation();
    bag[mealId] = (bag[mealId] || 0) + 1;
    saveState();
    updateBagUI();
    if (typeof renderProducts === 'function') renderProducts();
    if (typeof renderFavorites === 'function') renderFavorites();

    const bagBtn = document.getElementById('bag-button');
    if (bagBtn) {
        bagBtn.classList.add('bump');
        setTimeout(() => bagBtn.classList.remove('bump'), 220);
    }

    const meal = MEALS.find(m => m.id === mealId);
    showToast(`Added ${meal ? meal.title : 'item'} to your bag`, 'View Bag', openBagDrawer);
}

function changeBagQty(mealId, delta) {
    if (!bag[mealId]) return;
    bag[mealId] += delta;
    if (bag[mealId] <= 0) {
        delete bag[mealId];
    }
    saveState();
    updateBagUI();
    if (typeof renderProducts === 'function') renderProducts();
    if (typeof renderFavorites === 'function') renderFavorites();
}

function updateBagUI() {
    const entries = Object.entries(bag);
    const totalItems = entries.reduce((acc, [, q]) => acc + q, 0);
    const label = document.getElementById('bag-btn-label');
    if (label) label.textContent = `Bag ${totalItems}`;

    const container = document.getElementById('bag-items-container');
    if (!container) return;

    let subtotal = 0;

    if (entries.length === 0) {
        container.innerHTML = `
            <div style="text-align:center; padding: 36px 12px; color:#6b776d;">
                <div style="font-size:30px; margin-bottom:8px;">🛍️</div>
                <strong style="display:block; color:#1b261e; margin-bottom:4px;">Your campus bag is empty</strong>
                <span style="font-size:12.5px;">Add meals from the menu for self-pickup or campus runner delivery.</span>
            </div>
        `;
    } else {
        container.innerHTML = entries.map(([id, qty]) => {
            const meal = MEALS.find(m => m.id === id);
            if (!meal) return '';
            const lineTotal = meal.price * qty;
            subtotal += lineTotal;
            return `
                <div class="bag-item">
                    <div class="bag-item-info">
                        <h5>${meal.title}</h5>
                        <p>${meal.stall}</p>
                        <div class="item-price">RM ${lineTotal.toFixed(2)}</div>
                    </div>
                    <div class="qty-controls">
                        <button type="button" class="qty-btn" onclick="changeBagQty('${id}', -1)">−</button>
                        <span class="qty-val">${qty}</span>
                        <button type="button" class="qty-btn" onclick="changeBagQty('${id}', 1)">+</button>
                    </div>
                </div>
            `;
        }).join('');
    }

    const discount = (ecoPledge && subtotal > 0) ? 0.50 : 0;
    let deliveryFee = 0;
    if (orderMode === 'delivery' && subtotal > 0) {
        deliveryFee = subtotal >= 25 ? 0 : 2.00;
    }

    const total = Math.max(0, subtotal - discount + deliveryFee);

    const subtotalEl = document.getElementById('summary-subtotal');
    const ecoRowEl = document.getElementById('summary-eco-row');
    const feeLabelEl = document.getElementById('summary-fee-label');
    const feeValEl = document.getElementById('summary-fee-value');
    const totalEl = document.getElementById('summary-total');
    const checkoutBtn = document.getElementById('checkout-btn');
    const checkoutBtnText = document.getElementById('checkout-btn-text');

    if (subtotalEl) subtotalEl.textContent = `RM ${subtotal.toFixed(2)}`;
    if (ecoRowEl) ecoRowEl.style.display = discount > 0 ? 'flex' : 'none';

    if (feeLabelEl && feeValEl) {
        if (orderMode === 'delivery') {
            feeLabelEl.textContent = '🛵 Campus runner delivery';
            if (subtotal >= 25) {
                feeValEl.textContent = 'FREE (RM 25+)';
                feeValEl.style.color = '#2b6a3e';
            } else {
                feeValEl.textContent = 'RM 2.00';
                feeValEl.style.color = '#1b261e';
            }
        } else {
            feeLabelEl.textContent = '🏃 Campus self-pickup';
            feeValEl.textContent = 'FREE';
            feeValEl.style.color = '#2b6a3e';
        }
    }

    if (totalEl) totalEl.textContent = `RM ${total.toFixed(2)}`;
    if (checkoutBtn) checkoutBtn.disabled = entries.length === 0;
    if (checkoutBtnText) {
        checkoutBtnText.textContent = orderMode === 'delivery' ? 'Place delivery order' : 'Place pickup order';
    }

    if (typeof renderProducts === 'function') renderProducts();
}

function openBagDrawer() {
    updateBagUI();
    const backdrop = document.getElementById('drawer-backdrop');
    const drawer = document.getElementById('bag-drawer');
    if (backdrop) backdrop.classList.add('open');
    if (drawer) drawer.classList.add('open');
}

function closeBagDrawer() {
    const backdrop = document.getElementById('drawer-backdrop');
    const drawer = document.getElementById('bag-drawer');
    if (backdrop) backdrop.classList.remove('open');
    if (drawer) drawer.classList.remove('open');
}

async function placeOrder() {
    const entries = Object.entries(bag);
    if (entries.length === 0) return;

    saveDeliverySpot();

    let subtotal = 0;
    const items = entries.map(([id, qty]) => {
        const meal = MEALS.find(m => m.id === id);
        subtotal += meal.price * qty;
        return { id, title: meal.title, stall: meal.stall, qty, price: meal.price };
    });

    const discount = ecoPledge ? 0.50 : 0;
    const deliveryFee = (orderMode === 'delivery' && subtotal < 25) ? 2.00 : 0;
    const total = Math.max(0, subtotal - discount + deliveryFee);

    const pickupTimeSelect = document.getElementById('pickup-time-select');
    const deliveryTimeSelect = document.getElementById('delivery-time-select');
    const timeSlot = orderMode === 'delivery'
        ? (deliveryTimeSelect ? deliveryTimeSelect.value : 'Standard (18–25 mins)')
        : (pickupTimeSelect ? pickupTimeSelect.value : 'ASAP (10–12 mins)');

    const code = 'UF-' + Math.floor(100 + Math.random() * 900);
    const itemsSummary = items.map(i => `${i.qty}x ${i.title}`).join(', ');
    const primaryStall = (items[0] && items[0].stall) ? items[0].stall : currentCourt;

    const newOrder = {
        code,
        userId: CURRENT_USER_ID,
        customerName: CURRENT_USER_NAME,
        mode: orderMode,
        campus: currentCampus,
        court: primaryStall,
        deliverySpot: orderMode === 'delivery' ? deliverySpot : null,
        deliveryNote: orderMode === 'delivery' ? deliveryNote : '',
        deliveryFee,
        pickupTime: timeSlot,
        eco: ecoPledge,
        items,
        itemsSummary,
        total,
        riderState: orderMode === 'delivery' ? 'available' : null,
        status: orderMode === 'delivery' ? 'Waiting for Campus Runner · Preparing 🛵' : 'Preparing at stall 🏃',
        createdAt: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    };

    orders.unshift(newOrder);
    bag = {};
    saveState();
    updateBagUI();
    updateBadges();
    closeBagDrawer();

    // Persist order to backend database so Riders and Vendors receive it immediately
    try {
        const fd = new FormData();
        fd.append('action', 'create_order');
        fd.append('order_code', code);
        fd.append('order_type', orderMode);
        fd.append('delivery_spot', deliverySpot || 'Kolej Kediaman Satria');
        fd.append('delivery_note', deliveryNote || '');
        fd.append('total_amount', total.toFixed(2));
        fd.append('delivery_fee', (deliveryFee > 0 ? deliveryFee : 2.00).toFixed(2));
        fd.append('customer_name', CURRENT_USER_NAME);
        fd.append('items_summary', itemsSummary);
        fd.append('stall_name', primaryStall);
        await fetch('api_rider.php', { method: 'POST', body: fd });
    } catch (e) {
        // Fallback already saved in localStorage
    }

    window.location.href = 'orders.php';
}

function renderOrders() {
    const container = document.getElementById('orders-container');
    if (!container) return;

    const myOrders = getMyOrders();

    if (myOrders.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div style="font-size:32px;">🧾</div>
                <h4>No orders placed yet</h4>
                <p>No orders have been placed by <strong>${CURRENT_USER_NAME}</strong> yet. When you place a self-pickup or campus delivery order, your personal order tracker will appear here.</p>
                <a href="index.php">Order your first meal</a>
            </div>
        `;
        return;
    }

    container.innerHTML = myOrders.map((o) => {
        const isDelivery = o.mode === 'delivery';
        const isCompleted = o.status.includes('✓');
        const badgeLabel = isDelivery ? `🛵 Delivery #${o.code}` : `🏃 Pickup Code #${o.code}`;
        const locationInfo = isDelivery
            ? `Deliver to: <strong>${o.deliverySpot}</strong>${o.deliveryNote ? ` (${o.deliveryNote})` : ''} · ${o.campus}`
            : `Self-pickup at: <strong>${o.court}</strong> (${o.campus})`;

        return `
            <div class="order-card">
                <div class="order-card-head">
                    <div>
                        <span class="order-code-badge">${badgeLabel}</span>
                        <span style="font-size:12px; color:#6b776d; margin-left:10px;">Ordered at ${o.createdAt} · by <strong>${o.customerName || CURRENT_USER_NAME}</strong></span>
                        <div style="font-size:12.5px; color:#364539; margin-top:8px;">📍 ${locationInfo}</div>
                    </div>
                    <div class="order-status-steps">
                        <span>● ${o.status}</span>
                    </div>
                </div>
                <div class="order-items-summary">
                    ${o.items.map(item => `
                        <div class="order-item-line">
                            <span><strong>${item.qty}x</strong> ${item.title} <small style="color:#7b8a7d;">(${item.stall})</small></span>
                            <span>RM ${(item.price * item.qty).toFixed(2)}</span>
                        </div>
                    `).join('')}
                    ${isDelivery ? `<div class="order-item-line" style="color:#556357; font-size:12px;"><span>🛵 Campus Runner Delivery Fee</span><span>${o.deliveryFee > 0 ? 'RM ' + o.deliveryFee.toFixed(2) : 'FREE'}</span></div>` : ''}
                    ${o.eco ? `<div class="order-item-line" style="color:#2b6a3e; font-size:12px;"><span>🌱 Eco Container / Bag Discount</span><span>-RM 0.50</span></div>` : ''}
                </div>
                <div class="order-card-foot">
                    <div>
                        <span style="font-size:12px; color:#6b776d;">${isDelivery ? 'Estimated arrival' : 'Pickup window'}: <strong>${o.pickupTime}</strong></span>
                        <div style="font-size:15px; font-weight:800; color:var(--forest); margin-top:2px;">Total Paid: RM ${o.total.toFixed(2)}</div>
                    </div>
                    <div class="order-actions">
                        ${!isCompleted ? `<button type="button" class="btn-outline" onclick="markOrderCompleted('${o.code}')">${isDelivery ? 'Mark as Delivered' : 'Mark as Picked Up'}</button>` : ''}
                        <button type="button" class="btn-primary-sm" onclick="reorderItems('${o.code}')">Reorder ↗</button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function markOrderCompleted(orderCode) {
    const ord = orders.find(o => o && o.code === orderCode && String(o.userId) === CURRENT_USER_ID);
    if (!ord) return;
    const isDelivery = ord.mode === 'delivery';
    ord.status = isDelivery ? 'Delivered ✓' : 'Picked up ✓';
    saveState();
    renderOrders();
    showToast(`Order #${ord.code} completed! Enjoy your meal 🍱`, null);
}

function reorderItems(orderCode) {
    const ord = orders.find(o => o && o.code === orderCode && String(o.userId) === CURRENT_USER_ID);
    if (!ord) return;
    if (ord.mode) setOrderMode(ord.mode, true);
    ord.items.forEach(item => {
        bag[item.id] = (bag[item.id] || 0) + item.qty;
    });
    saveState();
    updateBagUI();
    openBagDrawer();
}

function updateBadges() {
    const favBadge = document.getElementById('favorites-badge');
    const ordBadge = document.getElementById('orders-badge');
    const myOrdersCount = getMyOrders().length;

    if (favBadge) {
        favBadge.textContent = favorites.length;
        favBadge.classList.toggle('visible', favorites.length > 0);
    }

    if (ordBadge) {
        ordBadge.textContent = myOrdersCount;
        ordBadge.classList.toggle('visible', myOrdersCount > 0);
    }
}

/* MODALS */
function openMealModal(mealId) {
    const meal = MEALS.find(m => m.id === mealId);
    if (!meal) return;
    const modal = document.getElementById('info-modal');
    const content = document.getElementById('info-modal-content');
    if (!modal || !content) return;

    content.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:14px;">
            <span class="order-code-badge">${meal.badge}</span>
            <button type="button" class="close-drawer-btn" onclick="closeInfoModal()">✕</button>
        </div>
        <div style="background:#f4f7f0; border-radius:16px; height:140px; display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
            ${meal.svg}
        </div>
        <h3>${meal.title}</h3>
        <p style="margin-bottom:8px;"><strong>${meal.stall}</strong> · ★ ${meal.rating.toFixed(1)} · 🏃 ${meal.prepMins}m pickup / 🛵 ${meal.prepMins + 10}m delivery</p>
        <p>${meal.desc}</p>
        <div style="display:flex; align-items:center; justify-content:space-between; padding-top:14px; border-top:1px solid #e5e9e2;">
            <div style="font-size:20px; font-weight:800; color:var(--forest);">RM ${meal.price.toFixed(2)}</div>
            <button type="button" class="banner-cta" onclick="addToBag('${meal.id}'); closeInfoModal();">Add to bag · RM ${meal.price.toFixed(2)} ↗</button>
        </div>
    `;
    modal.classList.add('open');
}

function openHelpModal() {
    const modal = document.getElementById('info-modal');
    const content = document.getElementById('info-modal-content');
    if (!modal || !content) return;

    content.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3>How UniFood Campus Works</h3>
            <button type="button" class="close-drawer-btn" onclick="closeInfoModal()">✕</button>
        </div>
        <p>Built for busy students and lecturers—choose between fast self-pickup or campus runner delivery.</p>
        <div class="faq-step">
            <strong>🏃 Option 1: Self Pickup (FREE)</strong>
            Order ahead between classes and pick up straight from the stall counter using your #UF code without queuing.
        </div>
        <div class="faq-step">
            <strong>🛵 Option 2: Campus Runner Delivery (+RM 2.00)</strong>
            Have a student runner deliver your meal to your hostel lobby, library entrance, or faculty foyer (FREE on orders RM 25+).
        </div>
        <div class="faq-step">
            <strong>🌱 Bring Your Own Container (Save RM 0.50)</strong>
            Tap the green eco card in the sidebar to pledge a reusable container or bag and get RM 0.50 off automatically.
        </div>
        <div style="display:flex; justify-content:flex-end; margin-top:16px;">
            <button type="button" class="banner-cta" onclick="closeInfoModal()">Got it, let's eat ✦</button>
        </div>
    `;
    modal.classList.add('open');
}

function openProfileModal() {
    const modal = document.getElementById('info-modal');
    const content = document.getElementById('info-modal-content');
    if (!modal || !content) return;

    content.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3>Student Account</h3>
            <button type="button" class="close-drawer-btn" onclick="closeInfoModal()">✕</button>
        </div>
        <p>Manage your student demo session, fulfillment mode, and eco preferences.</p>
        <div class="faq-step">
            <strong>🛵 Current Mode</strong>
            Fulfillment: <strong>${orderMode === 'delivery' ? 'Campus Delivery (' + deliverySpot + ')' : 'Self Pickup (' + currentCourt + ')'}</strong>
        </div>
        <div class="faq-step">
            <strong>🌱 Eco Impact</strong>
            Eco Container Pledge: <strong>${ecoPledge ? 'Active (-RM 0.50 per order)' : 'Not active yet'}</strong>
        </div>
        <div class="faq-step">
            <strong>🧾 Total Campus Orders</strong>
            You have placed <strong>${getMyOrders().length}</strong> order(s) and saved <strong>${favorites.length}</strong> favorite meal(s).
        </div>
        <div style="display:flex; justify-content:space-between; margin-top:16px;">
            <button type="button" class="btn-outline" onclick="localStorage.clear(); location.reload();">Reset demo data</button>
            <button type="button" class="banner-cta" onclick="closeInfoModal()">Done</button>
        </div>
    `;
    modal.classList.add('open');
}

function closeInfoModal() {
    const modal = document.getElementById('info-modal');
    if (modal) modal.classList.remove('open');
}

/* ==========================================================
   UTEM MAIN CAMPUS INTERACTIVE CAFE MAP LOGIC
   ========================================================== */
const UTEM_CAFES = {
    'kdk': {
        id: 'kdk',
        name: 'Cafe Abang Mirul',
        courtName: 'Cafe Abang Mirul',
        location: 'Kompleks Dewan Kuliah (KDK), Level G',
        hours: '7:30 AM – 6:30 PM',
        walk: '1–3 mins from KDK lecture halls',
        stallsCount: '6 Food Stalls',
        delivery: 'Supported (RM 2.00 / Free RM 25+)',
        status: 'OPEN NOW · CENTRAL ACADEMIC HUB',
        category: 'academic',
        satQuery: 'Kompleks Dewan Kuliah UTeM Melaka',
        stalls: ['Warung Bonda', 'Uncle Lim Wok', 'Ayam Gepuk Mak Dara', 'Green Bowl', 'Student Burger Lab', 'Campus Drinks Bar'],
        signature: 'Nasi Ayam Gepuk Sambal Gesek & Penang Char Kuey Teow'
    },
    'satria': {
        id: 'satria',
        name: 'Kafeteria Kolej Kediaman Satria',
        courtName: 'Kafeteria Kolej Satria',
        location: 'Kolej Satria (Near Blok Tuah & Lekir)',
        hours: '7:00 AM – 11:30 PM',
        walk: '5–8 mins walk (Hostel Zone)',
        stallsCount: '8 Stalls & Supper Kiosks',
        delivery: 'Direct Delivery to Satria Blocks',
        status: 'OPEN NOW · RESIDENTIAL & SUPPER',
        category: 'hostel',
        satQuery: 'Kolej Kediaman Satria UTeM Melaka',
        stalls: ['Satria Corner', 'Roti Canai Terbang', 'Mee Celup Pantai Timur', 'Nasi Lemak Kukus Daun Pisang', 'Western Delight', 'Aiskrim & Waffle House'],
        signature: 'Roti Canai Garing Telur Goyang & Nasi Lemak Sambal Sotong'
    },
    'lestari': {
        id: 'lestari',
        name: 'Kafeteria Kolej Kediaman Lestari',
        courtName: 'Kafeteria Kolej Lestari',
        location: 'Kolej Lestari (Near Blok Kasturi)',
        hours: '7:30 AM – 11:00 PM',
        walk: '6–9 mins walk (East Campus)',
        stallsCount: '7 Stalls Available',
        delivery: 'Direct Delivery to Lestari Blocks',
        status: 'OPEN NOW · RESIDENTIAL CAFE',
        category: 'hostel',
        satQuery: 'Kolej Kediaman Lestari UTeM Melaka',
        stalls: ['Lestari Grill & Burgers', 'Nasi Kandar Pelita UTeM', 'Soto & Bakso Melaka', 'Ayam Penyet Mas', 'Fresh Fruit Juice Hub'],
        signature: 'Double Crispy Cheese Melt Burger & Jus Mangga Susu'
    },
    'aljazari': {
        id: 'aljazari',
        name: 'Kafeteria Kolej Al-Jazari (FTMK)',
        courtName: 'Kafeteria Al-Jazari (FTMK)',
        location: 'Al-Jazari Complex, Opposite FTMK Faculty',
        hours: '8:00 AM – 10:30 PM',
        walk: '2 mins from FTMK Labs',
        stallsCount: '5 Stalls Open',
        delivery: 'Direct Delivery to FTMK / Al-Jazari',
        status: 'OPEN NOW · TECH & COMPUTING HUB',
        category: 'hostel',
        satQuery: 'Kolej Kediaman Al-Jazari UTeM Melaka',
        stalls: ['FTMK Code & Coffee', 'Dapur Kampung Al-Jazari', 'Noodle Station', 'Dim Sum & Pau Kukus', 'Teh Tarik Madu'],
        signature: 'Nasi Goreng Kampung Cili Padi & Kopi Ais Kaw'
    },
    'library': {
        id: 'library',
        name: 'Cafe Perpustakaan Laman Hikmah',
        courtName: 'Cafe Laman Hikmah',
        location: 'Ground Foyer, Perpustakaan Laman Hikmah',
        hours: '8:30 AM – 7:00 PM',
        walk: '2 mins from Main Lake & Library',
        stallsCount: 'Boutique Coffee & Bakery',
        delivery: 'Supported (Pickup recommended)',
        status: 'OPEN NOW · QUIET STUDY ZONE',
        category: 'study',
        satQuery: 'Perpustakaan Laman Hikmah UTeM Melaka',
        stalls: ['Laman Brew Coffee', 'Hikmah Pastries & Sandwiches', 'Uji Matcha Bar', 'Salad & Wrap Express'],
        signature: 'Oat Milk Dirty Matcha Latte & Fresh Croissant Sandwiches'
    },
    'hepa': {
        id: 'hepa',
        name: 'Kafeteria Kompleks Mahasiswa (HEPA)',
        courtName: 'Kafeteria Mahasiswa HEPA',
        location: 'Bangunan Hal Ehwal Pelajar / Canselori Lakefront',
        hours: '8:00 AM – 5:30 PM',
        walk: '4 mins from Dewan Canselor',
        stallsCount: '6 Stalls Open',
        delivery: 'Supported to Canselori & Lake Zone',
        status: 'OPEN NOW · STUDENT CENTER',
        category: 'academic',
        satQuery: 'Kompleks Mahasiswa UTeM Melaka',
        stalls: ['Nasi Bajet Siswa', 'Ayam Berempah Madu', 'Warung Laksa & Mee Kari', 'Air Buah Potong'],
        signature: 'Nasi Berlauk Bajet Pelajar (RM 5.00 Value Meal)'
    },
    'fke': {
        id: 'fke',
        name: 'Kafeteria Kompleks Kejuruteraan',
        courtName: 'Cafe Fakulti Kejuruteraan',
        location: 'Engineering Quadrangle (FKE / FKM / FKEKK)',
        hours: '8:00 AM – 5:30 PM',
        walk: '2 mins from Engineering Labs & Workshops',
        stallsCount: '5 Stalls Open',
        delivery: 'Supported to Engineering Complex',
        status: 'OPEN NOW · ENGINEERING HUB',
        category: 'academic',
        satQuery: 'Fakulti Kejuruteraan Elektrik UTeM Melaka',
        stalls: ['Engineer Bite Station', 'Selera Pantai Timur', 'Nasi Kerabu Daging Bakar', 'Waffle & Smoothies'],
        signature: 'Nasi Kerabu Golok Daging Bakar & Ais Lemon Tea'
    }
};

let activeSelectedMapCafe = 'kdk';

function selectMapCafe(cafeKey) {
    const cafe = UTEM_CAFES[cafeKey];
    if (!cafe) return;
    activeSelectedMapCafe = cafeKey;

    // 1. Highlight map pin in SVG
    document.querySelectorAll('.map-pin').forEach(pin => pin.classList.remove('active'));
    const activePin = document.getElementById(`pin-${cafeKey}`);
    if (activePin) activePin.classList.add('active');

    // 2. Highlight quick selector pill
    document.querySelectorAll('.cafe-quick-pill').forEach(pill => pill.classList.remove('active'));
    const allPills = document.querySelectorAll('.cafe-quick-pill');
    const keyIndexMap = { 'kdk': 0, 'satria': 1, 'lestari': 2, 'aljazari': 3, 'library': 4, 'hepa': 5, 'fke': 6 };
    if (allPills[keyIndexMap[cafeKey]]) {
        allPills[keyIndexMap[cafeKey]].classList.add('active');
    }

    // 3. Update Google Satellite Map iframe to zoom into this cafe
    const satFrame = document.getElementById('gmaps-satellite-frame');
    if (satFrame && cafe.satQuery) {
        satFrame.src = `https://maps.google.com/maps?q=${encodeURIComponent(cafe.satQuery)}&t=k&z=18&ie=UTF8&iwloc=&output=embed`;
    }
    const satLabel = document.getElementById('satellite-status-label');
    if (satLabel) {
        satLabel.textContent = `Google Satellite · ${cafe.name} (${cafe.location})`;
    }

    // 4. Update right-side detail card
    const titleEl = document.getElementById('map-cafe-title');
    const locEl = document.getElementById('map-cafe-loc');
    const hoursEl = document.getElementById('map-cafe-hours');
    const walkEl = document.getElementById('map-cafe-walk');
    const stallsCountEl = document.getElementById('map-cafe-stalls-count');
    const deliveryEl = document.getElementById('map-cafe-delivery');
    const statusTextEl = document.getElementById('map-status-text');
    const stallsListEl = document.getElementById('map-cafe-stalls-list');
    const dishEl = document.getElementById('map-cafe-dish-name');

    if (titleEl) titleEl.textContent = cafe.name;
    if (locEl) locEl.innerHTML = `<span>📍</span> ${cafe.location}`;
    if (hoursEl) hoursEl.textContent = cafe.hours;
    if (walkEl) walkEl.textContent = cafe.walk;
    if (stallsCountEl) stallsCountEl.textContent = cafe.stallsCount;
    if (deliveryEl) deliveryEl.textContent = cafe.delivery;
    if (statusTextEl) statusTextEl.textContent = cafe.status;
    if (dishEl) dishEl.textContent = cafe.signature;

    if (stallsListEl) {
        stallsListEl.innerHTML = cafe.stalls.map(st => `<span class="stall-chip">${st}</span>`).join('');
    }
}

function switchMapMode(mode) {
    const satCont = document.getElementById('map-satellite-container');
    const schCont = document.getElementById('map-schematic-container');
    const btnSat = document.getElementById('btn-mode-satellite');
    const btnSch = document.getElementById('btn-mode-schematic');

    if (mode === 'satellite') {
        if (satCont) satCont.style.display = 'block';
        if (schCont) schCont.style.display = 'none';
        if (btnSat) btnSat.classList.add('active');
        if (btnSch) btnSch.classList.remove('active');
    } else {
        if (satCont) satCont.style.display = 'none';
        if (schCont) schCont.style.display = 'block';
        if (btnSat) btnSat.classList.remove('active');
        if (btnSch) btnSch.classList.add('active');
    }
}

function filterMapCategory(cat, btn) {
    document.querySelectorAll('.map-filter-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const pins = document.querySelectorAll('.map-pin');
    pins.forEach(pin => {
        const pinCat = pin.getAttribute('data-category');
        if (cat === 'all' || pinCat === cat) {
            pin.style.display = '';
            pin.style.opacity = '1';
            pin.style.pointerEvents = 'auto';
        } else {
            pin.style.opacity = '0.2';
            pin.style.pointerEvents = 'none';
        }
    });

    // Auto-select first matching pin
    for (const key of Object.keys(UTEM_CAFES)) {
        if (cat === 'all' || UTEM_CAFES[key].category === cat) {
            selectMapCafe(key);
            break;
        }
    }
}

function confirmMapCafeCourt() {
    const cafe = UTEM_CAFES[activeSelectedMapCafe];
    if (!cafe) return;

    currentCourt = cafe.courtName;
    saveState();
    updateLocationHeader();

    showToast(`📍 Active Court: ${cafe.courtName}`, null);
    scrollToMenu();
}

function scrollToMenu() {
    const menuEl = document.getElementById('menu-controls') || document.getElementById('category-pills');
    if (menuEl) {
        menuEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Initial Run on load
document.addEventListener('DOMContentLoaded', () => {
    const spotSelect = document.getElementById('delivery-spot-select');
    const noteInput = document.getElementById('delivery-note-input');
    if (spotSelect) {
        spotSelect.value = deliverySpot;
        if (!spotSelect.value && spotSelect.options.length > 0) {
            spotSelect.selectedIndex = 0;
            deliverySpot = spotSelect.value;
        }
    }
    if (noteInput) noteInput.value = deliveryNote;

    setOrderMode(orderMode, true);
    updateEcoUI();
    updateBadges();

    if (document.getElementById('products-grid')) {
        renderProducts();
    }
    if (document.getElementById('orders-container')) {
        renderOrders();
    }
    if (document.getElementById('favorites-grid')) {
        renderFavorites();
    }
});
