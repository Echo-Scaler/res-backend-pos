<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $table->restaurant->name }} - Table {{ $table->table_number }} Ordering</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Font: Mada -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mada:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: "Mada", sans-serif;
            background-color: #0b0f17;
            color: #f1f5f9;
            min-height: 100vh;
            padding-bottom: 90px;
        }

        /* Mobile Sticky Header */
        .mobile-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background-color: #111724;
            border-bottom: 1px solid #222d42;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .restaurant-brand h1 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
        }

        .table-pill {
            background: rgba(158, 198, 59, 0.15);
            color: #9ec63b;
            border: 1px solid rgba(158, 198, 59, 0.4);
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #22c55e;
            display: inline-block;
            box-shadow: 0 0 8px #22c55e;
        }

        /* Hero Banner */
        .order-hero {
            padding: 1.25rem 1rem;
            background: linear-gradient(180deg, #111724 0%, #0b0f17 100%);
            border-bottom: 1px solid #1e293b;
            text-align: center;
        }

        .order-hero h2 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #f8fafc;
            margin-bottom: 0.25rem;
        }

        .order-hero p {
            font-size: 0.8125rem;
            color: #94a3b8;
        }

        /* Category Scrollbar */
        .category-nav {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 0.75rem 1rem;
            background-color: #111724;
            border-bottom: 1px solid #222d42;
            position: sticky;
            top: 57px;
            z-index: 90;
            scrollbar-width: none;
        }

        .category-nav::-webkit-scrollbar {
            display: none;
        }

        .cat-btn {
            background-color: #161e2e;
            border: 1px solid #222d42;
            color: #94a3b8;
            padding: 0.4rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cat-btn.active, .cat-btn:hover {
            background-color: #9ec63b;
            color: #0b0f17;
            border-color: #9ec63b;
            font-weight: 700;
        }

        /* Dish Cards */
        .menu-container {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .dish-card {
            background-color: #161e2e;
            border: 1px solid #222d42;
            border-radius: 14px;
            padding: 1rem;
            display: flex;
            gap: 1rem;
            transition: border-color 0.2s;
        }

        .dish-card:active {
            border-color: #9ec63b;
        }

        .dish-img-box {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            background-color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ec63b;
            font-size: 2rem;
            flex-shrink: 0;
            overflow: hidden;
        }

        .dish-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dish-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .dish-name {
            font-size: 1rem;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 0.2rem;
        }

        .dish-desc {
            font-size: 0.75rem;
            color: #94a3b8;
            line-height: 1.3;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .dish-price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dish-price {
            font-size: 1rem;
            font-weight: 800;
            color: #9ec63b;
        }

        .btn-add-dish {
            background-color: #9ec63b;
            color: #0b0f17;
            border: none;
            padding: 0.35rem 0.85rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            transition: transform 0.1s ease;
        }

        .btn-add-dish:active {
            transform: scale(0.95);
        }

        /* Floating Order Bar */
        .cart-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #111724;
            border-top: 1px solid #222d42;
            padding: 0.85rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 200;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.4);
        }

        .cart-summary-text {
            display: flex;
            flex-direction: column;
        }

        .cart-count-badge {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .cart-total-price {
            font-size: 1.15rem;
            font-weight: 800;
            color: #9ec63b;
        }

        .btn-view-order {
            background-color: #9ec63b;
            color: #0b0f17;
            font-weight: 800;
            font-size: 0.95rem;
            padding: 0.65rem 1.25rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Cart Drawer Modal */
        .cart-modal {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 300;
            align-items: flex-end;
            backdrop-filter: blur(4px);
        }

        .cart-modal.active {
            display: flex;
        }

        .cart-drawer {
            background-color: #111724;
            border-top: 1px solid #222d42;
            border-radius: 20px 20px 0 0;
            width: 100%;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: slideUp 0.25s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .cart-drawer-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #222d42;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-drawer-header h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #f8fafc;
        }

        .cart-items-list {
            padding: 1rem 1.25rem;
            overflow-y: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .cart-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid #1e293b;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #f8fafc;
        }

        .cart-item-price {
            font-size: 0.8125rem;
            color: #9ec63b;
            font-weight: 600;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-qty {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
        }

        .qty-number {
            font-size: 0.95rem;
            font-weight: 700;
            min-width: 20px;
            text-align: center;
        }

        .cart-drawer-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid #222d42;
            background-color: #161e2e;
        }

        .order-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .btn-confirm-order {
            width: 100%;
            background-color: #9ec63b;
            color: #0b0f17;
            font-weight: 800;
            font-size: 1.05rem;
            padding: 0.85rem;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>

    <!-- Mobile Sticky Top Header -->
    <header class="mobile-header">
        <div class="restaurant-brand">
            <h1>{{ $table->restaurant->name }}</h1>
            <span style="font-size: 0.72rem; color: #94a3b8;">{{ $table->floor_area }}</span>
        </div>
        <div class="table-pill">
            <span class="pulse-dot"></span> Table {{ $table->table_number }}
        </div>
    </header>

    <!-- Welcome Hero -->
    <div class="order-hero">
        <h2>🍽️ Table Self-Ordering</h2>
        <p>Choose your dishes and submit order. Kitchen slips will print immediately!</p>
    </div>

    <!-- Category Filter Bar -->
    <div class="category-nav" id="categoryNav">
        <button type="button" class="cat-btn active" onclick="filterCategory('all', this)">All Menu</button>
        @php
            $restaurant = $table->restaurant;
            $categories = $restaurant->categories()->with(['products' => function($q) {
                $q->where('is_available', true);
            }])->get();
            $allProducts = $restaurant->products()->where('is_available', true)->get();
        @endphp
        @foreach($categories as $category)
            <button type="button" class="cat-btn" onclick="filterCategory('cat-{{ $category->id }}', this)">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <!-- Dishes List -->
    <div class="menu-container">
        @forelse($allProducts as $product)
            <div class="dish-card" data-category="cat-{{ $product->category_id }}">
                <div class="dish-img-box">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    @else
                        <i class="ti ti-tools-kitchen-2"></i>
                    @endif
                </div>
                <div class="dish-info">
                    <div>
                        <div class="dish-name">{{ $product->name }}</div>
                        <div class="dish-desc">{{ $product->description ?? 'Freshly prepared according to restaurant recipe.' }}</div>
                    </div>
                    <div class="dish-price-row">
                        <div class="dish-price">{{ number_format($product->price, 0) }} MMK</div>
                        <button type="button" 
                            class="btn-add-dish btn-dish-add" 
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->price }}">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
                <i class="ti ti-soup" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                <p>No active dishes found for this restaurant menu yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Sticky Bottom Cart Bar -->
    <div class="cart-bar" id="cartBar" style="display: none;">
        <div class="cart-summary-text">
            <span class="cart-count-badge" id="cartCountBadge">0 items selected</span>
            <span class="cart-total-price" id="cartTotalPrice">0 MMK</span>
        </div>
        <button type="button" class="btn-view-order" onclick="openCartModal()">
            <i class="ti ti-shopping-cart"></i> View Order
        </button>
    </div>

    <!-- Cart Drawer Modal -->
    <div class="cart-modal" id="cartModal">
        <div class="cart-drawer">
            <div class="cart-drawer-header">
                <div>
                    <h3>Table {{ $table->table_number }} Order Cart</h3>
                    <span style="font-size: 0.75rem; color: #94a3b8;">Review your items before sending to kitchen</span>
                </div>
                <button type="button" onclick="closeCartModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div class="cart-items-list" id="cartItemsList">
                <!-- Dynamically Rendered Cart Items -->
            </div>

            <div class="cart-drawer-footer">
                <div class="order-total-row">
                    <span style="font-size: 1rem; font-weight: 600; color: #94a3b8;">Total (Estimated):</span>
                    <span style="font-size: 1.35rem; font-weight: 800; color: #9ec63b;" id="drawerTotalPrice">0 MMK</span>
                </div>
                <button type="button" class="btn-confirm-order" onclick="submitOrder()">
                    <i class="ti ti-send"></i> Confirm & Send Order to Kitchen
                </button>
            </div>
        </div>
    </div>

    <!-- Order Placed Success Modal -->
    <div class="cart-modal-backdrop" id="orderSuccessModal" style="z-index: 10000;">
        <div class="cart-drawer" style="max-height: 90vh; border-radius: 20px 20px 0 0; text-align: center; padding: 2rem 1.5rem;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(34, 197, 94, 0.15); color: #22c55e; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem; margin-bottom: 1rem; border: 2px solid #22c55e;">
                <i class="ti ti-check"></i>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 800; color: #ffffff; margin-bottom: 0.25rem;">Order Placed Successfully!</h3>
            <p style="font-size: 0.875rem; color: #94a3b8; margin-bottom: 1.25rem;">အော်ဒါကို မီးဖိုချောင်သို့ တိုက်ရိုက် ပေးပို့လိုက်ပါပြီ</p>

            <div style="background: #161e2e; border: 1px solid #222d42; border-radius: 12px; padding: 1.25rem; text-align: left; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #94a3b8; font-size: 0.8125rem;">Order Number:</span>
                    <strong style="color: #ffffff; font-size: 0.875rem;" id="successOrderNumber">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #94a3b8; font-size: 0.8125rem;">Table:</span>
                    <strong style="color: #9ec63b; font-size: 0.875rem;">Table {{ $table->table_number }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #94a3b8; font-size: 0.8125rem;">Total Amount:</span>
                    <strong style="color: #f1f5f9; font-size: 1rem;" id="successTotalAmount">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #222d42; padding-top: 0.5rem; margin-top: 0.5rem;">
                    <span style="color: #94a3b8; font-size: 0.8125rem;">Kitchen Status:</span>
                    <span style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">
                        🟡 Preparing in Kitchen
                    </span>
                </div>
            </div>

            <div style="background: rgba(158, 198, 59, 0.1); border: 1px solid rgba(158, 198, 59, 0.3); border-radius: 10px; padding: 0.85rem; font-size: 0.8125rem; color: #cbd5e1; margin-bottom: 1.5rem; line-height: 1.4;">
                <i class="ti ti-receipt text-primary"></i> <strong>Saizeriya Style Reminder:</strong> မီးဖိုချောင်မှ ချက်ပြုတ်ပြီးပါက ဝိတ်တာသည် အစားအသောက်နှင့်အတူ <strong>Customer Bill Slip (စလစ်)</strong> ကို စားပွဲတင်ခွက်ထဲသို့ လာရောက်ချထားပေးပါမည်။ သုံးဆောင်ပြီးပါက ထိုစလစ်ကို ယူဆောင်၍ ကောင်တာတွင် ငွေရှင်းပေးပါရန်။
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <button type="button" class="btn-confirm-order" onclick="closeSuccessModal()">
                    <i class="ti ti-plus"></i> Order More Dishes (ထပ်မံ မှာယူရန်)
                </button>
            </div>
        </div>
    </div>

    <script>
        let cart = {};

        function filterCategory(catId, btn) {
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const dishes = document.querySelectorAll('.dish-card');
            dishes.forEach(dish => {
                if (catId === 'all' || dish.getAttribute('data-category') === catId) {
                    dish.style.display = 'flex';
                } else {
                    dish.style.display = 'none';
                }
            });
        }

        function addToCart(id, name, price) {
            if (cart[id]) {
                cart[id].qty += 1;
            } else {
                cart[id] = { id: id, name: name, price: price, qty: 1 };
            }
            updateCartUI();
        }

        document.addEventListener('click', function(e) {
            const addBtn = e.target.closest('.btn-dish-add');
            if (addBtn) {
                const id = parseInt(addBtn.dataset.id, 10);
                const name = addBtn.dataset.name;
                const price = parseFloat(addBtn.dataset.price);
                addToCart(id, name, price);
            }
        });

        function changeQty(id, delta) {
            if (!cart[id]) return;
            cart[id].qty += delta;
            if (cart[id].qty <= 0) {
                delete cart[id];
            }
            updateCartUI();
            renderCartDrawer();
        }

        function updateCartUI() {
            let totalQty = 0;
            let totalPrice = 0;
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            for (const id in cart) {
                totalQty += cart[id].qty;
                totalPrice += cart[id].qty * cart[id].price;
            }

            const cartBar = document.getElementById('cartBar');
            if (totalQty > 0) {
                cartBar.style.display = 'flex';
                document.getElementById('cartCountBadge').innerText = totalQty + ' item' + (totalQty > 1 ? 's' : '') + ' in cart';
                document.getElementById('cartTotalPrice').innerText = formatMMK(totalPrice);
                document.getElementById('drawerTotalPrice').innerText = formatMMK(totalPrice);
            } else {
                cartBar.style.display = 'none';
                closeCartModal();
            }
        }

        function renderCartDrawer() {
            const list = document.getElementById('cartItemsList');
            list.innerHTML = '';

            for (const id in cart) {
                const item = cart[id];
                const row = document.createElement('div');
                row.className = 'cart-item-row';
                row.innerHTML = `
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-price">${formatMMK(item.price)} each</div>
                    </div>
                    <div class="qty-controls">
                        <button type="button" class="btn-qty" onclick="changeQty(${item.id}, -1)">-</button>
                        <span class="qty-number">${item.qty}</span>
                        <button type="button" class="btn-qty" onclick="changeQty(${item.id}, 1)">+</button>
                    </div>
                `;
                list.appendChild(row);
            }
        }

        function openCartModal() {
            renderCartDrawer();
            document.getElementById('cartModal').classList.add('active');
        }

        function closeCartModal() {
            document.getElementById('cartModal').classList.remove('active');
        }

        function showSuccessModal(data) {
            document.getElementById('successOrderNumber').innerText = '#' + (data.order?.order_number || '');
            document.getElementById('successTotalAmount').innerText = data.order?.formatted_total || '';
            document.getElementById('orderSuccessModal').classList.add('active');
        }

        function closeSuccessModal() {
            document.getElementById('orderSuccessModal').classList.remove('active');
        }

        function formatMMK(amount) {
            return new Intl.NumberFormat().format(amount) + ' MMK';
        }

        function submitOrder() {
            const items = Object.values(cart).map(item => ({
                product_id: item.id,
                quantity: item.qty,
                special_notes: null
            }));

            if (items.length === 0) {
                alert('Please select at least one dish.');
                return;
            }

            const btn = document.querySelector('.btn-confirm-order');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="ti ti-loader"></i> Sending to Kitchen...';

            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('customer.order.submit', $table->qr_token) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ items: items })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeCartModal();
                    cart = {};
                    updateCartUI();
                    showSuccessModal(data);
                } else {
                    alert(data.message || 'Could not place order. Please try again.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network connection error. Please try again.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    </script>
</body>
</html>
