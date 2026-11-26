    </main>

    <!-- BAG DRAWER -->
    <div class="drawer-backdrop" id="drawer-backdrop" onclick="closeBagDrawer()"></div>
    <aside class="bag-drawer" id="bag-drawer" aria-label="Shopping bag">
        <div class="drawer-header">
            <h3>Your campus bag</h3>
            <button type="button" class="close-drawer-btn" onclick="closeBagDrawer()">✕</button>
        </div>
        <div class="drawer-body">
            <!-- Mode Switcher: Pickup vs Delivery -->
            <div class="drawer-mode-tabs">
                <button type="button" class="drawer-mode-tab active" id="drawer-tab-pickup" onclick="setOrderMode('pickup')">
                    🏃 Self Pickup (Free)
                </button>
                <button type="button" class="drawer-mode-tab" id="drawer-tab-delivery" onclick="setOrderMode('delivery')">
                    🛵 Delivery (+RM 2)
                </button>
            </div>

            <!-- Pickup Details Box -->
            <div class="pickup-box" id="bag-pickup-panel">
                <strong>📍 Pickup at <span id="drawer-campus-label">Main Campus · Central food court</span></strong>
                <span>Select when you want to pick up your meal:</span>
                <select class="pickup-select" id="pickup-time-select">
                    <option value="ASAP (10–12 mins)">⚡ ASAP (Ready in 10–12 mins)</option>
                    <option value="12:30 PM (Between lectures)">🕛 12:30 PM (Between lectures)</option>
                    <option value="1:00 PM (Lunch break)">🕐 1:00 PM (Lunch break)</option>
                    <option value="5:00 PM (After class)">🕔 5:00 PM (After class)</option>
                </select>
            </div>

            <!-- Delivery Details Box -->
            <div class="pickup-box" id="bag-delivery-panel" style="display:none;">
                <strong>🛵 Campus Runner Delivery (<span id="drawer-delivery-campus">Main Campus</span>)</strong>
                <span>Choose your campus building / drop-off point:</span>
                <select class="pickup-select" id="delivery-spot-select" onchange="saveDeliverySpot()">
                    <option value="Kolej Kediaman Satria">🏢 Kolej Kediaman Satria</option>
                    <option value="Kolej Kediaman Lestari">🏢 Kolej Kediaman Lestari</option>
                    <option value="Kolej Kediaman Al-Jazari">🏢 Kolej Kediaman Al-Jazari</option>
                    <option value="FTKEK">🏛️ FTKEK</option>
                    <option value="FTMK">🏛️ FTMK</option>
                    <option value="FTKM">🏛️ FTKM</option>
                    <option value="FTKIP">🏛️ FTKIP</option>
                    <option value="FTKE">🏛️ FTKE</option>
                    <option value="STEP">🏛️ STEP</option>
                </select>
                <input type="text" class="delivery-input" id="delivery-note-input" placeholder="Room / floor / landmark note (e.g. Level 2 bench)..." oninput="saveDeliverySpot()">
                <select class="pickup-select" id="delivery-time-select">
                    <option value="Standard (18–25 mins)">🛵 Standard Delivery (18–25 mins)</option>
                    <option value="12:30 PM (Between lectures)">🕛 12:30 PM (Between lectures)</option>
                    <option value="1:00 PM (Lunch break)">🕐 1:00 PM (Lunch break)</option>
                    <option value="6:30 PM (Dinner drop-off)">🕡 6:30 PM (Dinner drop-off)</option>
                </select>
            </div>

            <div id="bag-items-container"></div>

            <label class="eco-check-row">
                <span>🌱 <strong>Bring my own container / Reusable bag</strong> (-RM 0.50)</span>
                <input type="checkbox" id="bag-eco-checkbox" onchange="toggleEcoFromBag(this.checked)">
            </label>
        </div>
        <div class="drawer-footer">
            <div class="summary-line">
                <span>Subtotal</span>
                <span id="summary-subtotal">RM 0.00</span>
            </div>
            <div class="summary-line" id="summary-eco-row" style="display:none; color:#2b6a3e; font-weight:700;">
                <span>🌱 Eco Container Discount</span>
                <span>-RM 0.50</span>
            </div>
            <div class="summary-line">
                <span id="summary-fee-label">Campus pickup fee</span>
                <span id="summary-fee-value" style="color:#2b6a3e; font-weight:700;">FREE</span>
            </div>
            <div class="summary-line total">
                <span>Total</span>
                <span id="summary-total">RM 0.00</span>
            </div>
            <button type="button" class="checkout-btn" id="checkout-btn" onclick="placeOrder()">
                <span id="checkout-btn-text">Place pickup order</span>
                <span>↗</span>
            </button>
        </div>
    </aside>

    <!-- HELP / PROFILE MODAL -->
    <div class="modal-backdrop" id="info-modal" onclick="if(event.target===this) closeInfoModal()">
        <div class="modal-card" id="info-modal-content"></div>
    </div>

    <!-- TOAST -->
    <div class="toast" id="toast">
        <span id="toast-text">Added to bag</span>
        <button type="button" class="toast-action" id="toast-btn" onclick="openBagDrawer()">View Bag</button>
    </div>

    <script src="app.js"></script>
</body>
</html>
