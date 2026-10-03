@extends('admin.layouts.app')

@section('title', 'Floor Staff & Tableside Ordering Portal')

@push('styles')
<style>
    :root {
        --pos-accent: #9ec63b;
        --pos-emerald: #10b981;
        --pos-amber: #f59e0b;
        --pos-rose: #f43f5e;
        --pos-cyan: #06b6d4;
    }

    body, .role-title, .stat-value, .btn, input, select, table {
        font-family: "Mada", sans-serif !important;
    }

    .pos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    .role-title {
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #f8fafc;
        margin: 0;
    }

    .role-badge-pill {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        background: rgba(6, 182, 212, 0.15);
        color: #67e8f9;
        border: 1px solid rgba(6, 182, 212, 0.35);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        border-color: rgba(6, 182, 212, 0.4);
        transform: translateY(-2px);
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.4rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #f8fafc;
    }

    .stat-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.3rem;
    }

    /* Area Filter Tabs */
    .area-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .area-tab-btn {
        background: rgba(22, 30, 46, 0.8);
        border: 1px solid var(--border);
        color: #94a3b8;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .area-tab-btn.active, .area-tab-btn:hover {
        background: rgba(6, 182, 212, 0.2);
        color: #67e8f9;
        border-color: #06b6d4;
    }

    /* Tables Grid */
    .tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .table-card {
        background-color: var(--bg-card);
        border: 2px solid var(--border);
        border-radius: 14px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1rem;
        transition: all 0.2s ease;
        position: relative;
    }

    .table-card:hover {
        transform: translateY(-3px);
    }

    .table-card.vacant {
        border-color: rgba(16, 185, 129, 0.5);
        background: rgba(16, 185, 129, 0.03);
    }

    .table-card.occupied {
        border-color: rgba(249, 115, 22, 0.5);
        background: rgba(249, 115, 22, 0.03);
    }

    .table-card.billing {
        border-color: rgba(245, 158, 11, 0.6);
        background: rgba(245, 158, 11, 0.05);
    }

    .table-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .table-num {
        font-size: 1.25rem;
        font-weight: 700;
        color: #f8fafc;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .table-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        text-transform: uppercase;
    }

    .badge-vacant {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.4);
    }

    .badge-occupied {
        background: rgba(249, 115, 22, 0.2);
        color: #fb923c;
        border: 1px solid rgba(249, 115, 22, 0.4);
    }

    .badge-billing {
        background: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.4);
    }

    .table-info-body {
        font-size: 0.8125rem;
        color: var(--text-muted);
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .table-btn-stack {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-pos {
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.55rem 0.85rem;
        border-radius: 8px;
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-pos-primary {
        background: #06b6d4;
        color: #fff;
    }
    .btn-pos-primary:hover {
        background: #0891b2;
    }

    .btn-pos-amber {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.4);
    }
    .btn-pos-amber:hover {
        background: rgba(245, 158, 11, 0.25);
    }

    .btn-pos-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: #cbd5e1;
        border-color: var(--border);
    }
    .btn-pos-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    /* Modal Backdrop and Box */
    .pos-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(11, 15, 23, 0.85);
        backdrop-filter: blur(6px);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .pos-modal-backdrop.show {
        display: flex;
    }

    .pos-modal-box {
        background: #161e2e;
        border: 1px solid #222d42;
        border-radius: 16px;
        width: 100%;
        max-width: 950px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 1.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
    }

    .modal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid #222d42;
        margin-bottom: 1.25rem;
    }

    .modal-head h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #f8fafc;
    }

    .btn-close-modal {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 1.5rem;
        cursor: pointer;
    }

    /* Menu Order Split Layout */
    .order-terminal-grid {
        display: grid;
        grid-template-columns: 1.6fr 1.2fr;
        gap: 1.5rem;
    }

    @media (max-width: 850px) {
        .order-terminal-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Category Tabs in Modal */
    .modal-category-tabs {
        display: flex;
        gap: 0.4rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    .modal-cat-btn {
        background: #111724;
        border: 1px solid #222d42;
        color: #94a3b8;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }

    .modal-cat-btn.active, .modal-cat-btn:hover {
        background: #06b6d4;
        color: #fff;
        border-color: #06b6d4;
    }

    /* Products Grid in Modal */
    .products-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
        max-height: 380px;
        overflow-y: auto;
        padding-right: 0.25rem;
    }

    .product-pick-card {
        background: #111724;
        border: 1px solid #222d42;
        border-radius: 10px;
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-pick-card:hover {
        border-color: #06b6d4;
        transform: translateY(-2px);
    }

    .product-name {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #f8fafc;
        margin-bottom: 0.35rem;
    }

    .product-price {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #9ec63b;
    }

    /* Cart Sidebar */
    .cart-box {
        background: rgba(11, 15, 23, 0.6);
        border: 1px solid #222d42;
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .cart-items-list {
        max-height: 250px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        margin-bottom: 1rem;
    }

    .cart-item-row {
        background: #161e2e;
        border: 1px solid #222d42;
        border-radius: 8px;
        padding: 0.6rem 0.8rem;
    }

    .cart-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #f8fafc;
    }

    .cart-qty-ctrl {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.4rem;
    }

    .btn-qty {
        width: 24px;
        height: 24px;
        background: #222d42;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .input-cart-note {
        width: 100%;
        background: #0b0f17;
        border: 1px solid #222d42;
        border-radius: 4px;
        color: #cbd5e1;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        margin-top: 0.4rem;
        box-sizing: border-box;
    }

    .cart-totals {
        border-top: 1px solid #222d42;
        padding-top: 0.75rem;
        font-size: 0.875rem;
    }

    .cart-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.35rem;
        color: var(--text-muted);
    }
</style>
@endpush

@section('content')
<div class="pos-header">
    <div>
        <h1 class="role-title">
            <span>🍽️ Floor Waiter & Tableside Ordering Portal</span>
            <span class="role-badge-pill">STAFF / WAITER</span>
        </h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.3rem 0 0;">
            Live tableside ordering, repeat dish rounds & bill dispatching for <strong>{{ $restaurant->name ?? 'Restaurant' }}</strong>
        </p>
    </div>
    <div>
        <span style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600;">
            <i class="ti ti-user"></i> Waiter: <strong style="color: #f8fafc;">{{ $user->name }}</strong>
        </span>
    </div>
</div>

{{-- KPI Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Occupied Tables</div>
        <div class="stat-value" style="color: #fb923c;">{{ $stats['active_tables'] }} Tables</div>
        <div class="stat-desc">Guests currently dining</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Calling for Bill</div>
        <div class="stat-value" style="color: #fbbf24;">{{ $stats['billing_tables'] }} Tables</div>
        <div class="stat-desc">Awaiting cashier checkout</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Vacant & Clean Tables</div>
        <div class="stat-value" style="color: #34d399;">{{ $stats['vacant_tables'] }} Tables</div>
        <div class="stat-desc">Ready to seat guests</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Kitchen Pass Pickups</div>
        <div class="stat-value" style="color: #67e8f9;">{{ $stats['ready_pickup_orders'] }} Orders</div>
        <div class="stat-desc">Hot dishes ready for table delivery</div>
    </div>
</div>

{{-- Dining Tables Floor Grid --}}
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
    <h2 style="font-size: 1.15rem; font-weight: 600; color: #f8fafc; margin: 0;">
        <i class="ti ti-layout-grid"></i> Dining Floor Tables
    </h2>
    <div style="display: flex; gap: 0.6rem; align-items: center;">
        <a href="{{ route('admin.orders.verification') }}" class="btn-pos btn-pos-secondary" style="font-size: 0.75rem;">
            <i class="ti ti-chef-hat"></i> Kitchen Pass Expediter
        </a>
    </div>
</div>

<div class="tables-grid">
    @foreach ($tables as $table)
        @php
            $order = $table->active_order;
        @endphp
        <div class="table-card {{ strtolower($table->status) }}" data-area="{{ $table->floor_area }}">
            <div class="table-top">
                <div class="table-num">
                    <i class="ti ti-armchair" style="color: {{ $table->status === 'VACANT' ? '#34d399' : ($table->status === 'BILLING' ? '#fbbf24' : '#fb923c') }};"></i>
                    {{ $table->table_number }}
                </div>
                <div>
                    @if ($table->status === 'VACANT')
                        <span class="table-badge badge-vacant">Available</span>
                    @elseif ($table->status === 'OCCUPIED')
                        <span class="table-badge badge-occupied">Occupied</span>
                    @else
                        <span class="table-badge badge-billing">Bill Requested</span>
                    @endif
                </div>
            </div>

            <div class="table-info-body">
                <div><strong>Area:</strong> {{ $table->floor_area ?? 'Main Hall' }} &bull; Max: {{ $table->seating_capacity }} Pax</div>
                @if ($order)
                    <div><strong>Order:</strong> <code style="color: #67e8f9;">{{ $order->order_number }}</code></div>
                    <div><strong>Seated:</strong> {{ $order->guest_count }} Guests &bull; {{ $order->items->count() }} Items</div>
                    <div><strong>Subtotal:</strong> <strong style="color: #9ec63b;">{{ number_format($order->total_amount, 0) }} MMK</strong></div>
                @else
                    <div>Table is clean and ready for seating.</div>
                @endif
            </div>

            <div class="table-btn-stack">
                @if ($table->status === 'VACANT')
                    <button type="button" class="btn-pos btn-pos-primary" onclick="openOrderModal({{ $table->id }}, '{{ $table->table_number }}', {{ $table->seating_capacity }})">
                        <i class="ti ti-plus"></i> Seat Guests & Order
                    </button>
                @elseif ($table->status === 'OCCUPIED')
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem;">
                        <button type="button" class="btn-pos btn-pos-secondary" style="font-size: 0.75rem;" onclick="openAddItemsModal({{ $order->id }}, '{{ $table->table_number }}', '{{ $order->order_number }}')">
                            <i class="ti ti-playlist-add"></i> Add Dishes
                        </button>
                        <form method="POST" action="{{ route('staff.tables.requestBill') }}">
                            @csrf
                            <input type="hidden" name="table_id" value="{{ $table->id }}">
                            <button type="submit" class="btn-pos btn-pos-amber" style="width: 100%; font-size: 0.75rem;">
                                <i class="ti ti-bell-ringing"></i> Call Bill
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('admin.orders.printKitchenChit', $order) }}" target="_blank" class="btn-pos btn-pos-secondary" style="font-size: 0.75rem; padding: 0.35rem;">
                        <i class="ti ti-printer"></i> View Kitchen Chit
                    </a>
                @else
                    <div style="text-align: center; color: #fbbf24; font-size: 0.8125rem; font-weight: 600; padding: 0.4rem;">
                        <i class="ti ti-clock"></i> Waiting Cashier Checkout
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- MODAL: TABLESIDE TOUCH ORDERING MODAL --}}
<div id="orderModal" class="pos-modal-backdrop">
    <div class="pos-modal-box">
        <div class="modal-head">
            <div>
                <h3 id="modalTableTitle">Take Tableside Order</h3>
                <span style="color: var(--text-muted); font-size: 0.8125rem;">Select dishes and dispatch directly to kitchen pass</span>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeOrderModal()">&times;</button>
        </div>

        <form id="orderForm" method="POST" action="{{ route('staff.orders.store') }}" onsubmit="handleOrderSubmit(event)">
            @csrf
            <input type="hidden" id="modalTableId" name="table_id" value="">

            {{-- Guest Pax Selector --}}
            <div style="background: rgba(11, 15, 23, 0.5); border: 1px solid #222d42; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                <div style="font-size: 0.8125rem; font-weight: 600; color: #f8fafc;">
                    <i class="ti ti-users"></i> Number of Guests (ဧည့်သည်အရေအတွက်):
                </div>
                <div style="display: flex; gap: 0.4rem;">
                    @foreach ([1, 2, 3, 4, 5, 6, 8] as $pax)
                        <button type="button" class="btn-pos btn-pos-secondary pax-btn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="setPax({{ $pax }}, this)">
                            {{ $pax }} Pax
                        </button>
                    @endforeach
                </div>
                <input type="hidden" id="guestCountInput" name="guest_count" value="2">
            </div>

            <div class="order-terminal-grid">
                {{-- Left: Products Picker --}}
                <div>
                    {{-- Categories Bar --}}
                    <div class="modal-category-tabs">
                        <button type="button" class="modal-cat-btn active" onclick="filterModalCategory('ALL', this)">All Menu</button>
                        @foreach ($categories as $cat)
                            <button type="button" class="modal-cat-btn" onclick="filterModalCategory('CAT-{{ $cat->id }}', this)">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Products Grid --}}
                    <div class="products-picker-grid">
                        @foreach ($products as $prod)
                            <div class="product-pick-card product-item cat-all CAT-{{ $prod->category_id }}" onclick="addToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->price }})">
                                <div>
                                    <div class="product-name">{{ $prod->name }}</div>
                                    <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $prod->category->name ?? 'Dish' }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                    <span class="product-price">{{ number_format($prod->price, 0) }} MMK</span>
                                    <span style="color: #06b6d4; font-size: 0.85rem;"><i class="ti ti-plus"></i></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Live Order Cart --}}
                <div class="cart-box">
                    <div>
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.75rem;">
                            <i class="ti ti-shopping-cart"></i> Selected Order Cart
                        </h4>
                        <div id="cartItemsContainer" class="cart-items-list">
                            <div id="emptyCartNotice" style="text-align: center; color: var(--text-muted); font-size: 0.8125rem; padding: 2rem 0;">
                                ဘယ်ဘက်မှ ဟင်းလျာများကို နှိပ်၍ ရွေးချယ်ပါ။<br>(Tap dishes to add to order)
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="cart-totals">
                            <div class="cart-row">
                                <span>Subtotal:</span>
                                <strong id="cartSubtotal" style="color: #f8fafc;">0 MMK</strong>
                            </div>
                            <div class="cart-row">
                                <span>Commercial Tax (5%):</span>
                                <span id="cartTax">0 MMK</span>
                            </div>
                            <div class="cart-row" style="font-size: 1.1rem; font-weight: 700; color: #9ec63b; border-top: 1px solid #222d42; padding-top: 0.4rem; margin-top: 0.4rem;">
                                <span>Estimated Total:</span>
                                <span id="cartTotal">0 MMK</span>
                            </div>
                        </div>

                        <div style="margin-top: 1rem;">
                            <button type="submit" id="btnSubmitOrder" class="btn-pos btn-pos-primary" style="width: 100%; justify-content: center; padding: 0.75rem;" disabled>
                                <i class="ti ti-send"></i> Send Order to Kitchen (KOT)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: ADD EXTRA ROUNDS TO ACTIVE TABLE ORDER --}}
<div id="addItemsModal" class="pos-modal-backdrop">
    <div class="pos-modal-box">
        <div class="modal-head">
            <div>
                <h3 id="addItemsModalTitle">Add Repeat Round of Dishes</h3>
                <span id="addItemsOrderMeta" style="color: var(--text-muted); font-size: 0.8125rem;">Table # &bull; Order #</span>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeAddItemsModal()">&times;</button>
        </div>

        <form id="addItemsForm" method="POST" action="" onsubmit="handleAddItemsSubmit(event)">
            @csrf
            <div class="order-terminal-grid">
                <div>
                    <div class="products-picker-grid" style="max-height: 400px;">
                        @foreach ($products as $prod)
                            <div class="product-pick-card" onclick="addToAddRoundCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->price }})">
                                <div>
                                    <div class="product-name">{{ $prod->name }}</div>
                                    <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $prod->category->name ?? 'Dish' }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                    <span class="product-price">{{ number_format($prod->price, 0) }} MMK</span>
                                    <span style="color: #06b6d4;"><i class="ti ti-plus"></i></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="cart-box">
                    <div>
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.75rem;">
                            <i class="ti ti-plus"></i> Dishes to Append (အပိုမှာကြားချက်)
                        </h4>
                        <div id="addRoundCartContainer" class="cart-items-list">
                            <div id="emptyAddNotice" style="text-align: center; color: var(--text-muted); font-size: 0.8125rem; padding: 2rem 0;">
                                ဟင်းလျာများကို ရွေးချယ်ပါ။
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="submit" id="btnSubmitAddRound" class="btn-pos btn-pos-primary" style="width: 100%; justify-content: center; padding: 0.75rem;" disabled>
                            <i class="ti ti-send"></i> Dispatch Additional Dishes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];
    let addRoundCart = [];

    function openOrderModal(tableId, tableNumber, capacity) {
        document.getElementById('modalTableId').value = tableId;
        document.getElementById('modalTableTitle').innerText = `Seat & Order: Table ${tableNumber} (Max ${capacity} Pax)`;
        cart = [];
        renderCart();
        document.getElementById('orderModal').classList.add('show');
    }

    function closeOrderModal() {
        document.getElementById('orderModal').classList.remove('show');
    }

    function setPax(num, elem) {
        document.getElementById('guestCountInput').value = num;
        document.querySelectorAll('.pax-btn').forEach(b => b.classList.remove('active'));
        if (elem) elem.classList.add('active');
    }

    function filterModalCategory(catClass, elem) {
        document.querySelectorAll('.modal-cat-btn').forEach(b => b.classList.remove('active'));
        if (elem) elem.classList.add('active');

        document.querySelectorAll('.product-item').forEach(card => {
            if (catClass === 'ALL' || card.classList.contains(catClass)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function addToCart(id, name, price) {
        const existing = cart.find(i => i.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1, notes: '' });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
        renderCart();
    }

    function updateNotes(id, text) {
        const item = cart.find(i => i.id === id);
        if (item) item.notes = text;
    }

    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const emptyNotice = document.getElementById('emptyCartNotice');
        const submitBtn = document.getElementById('btnSubmitOrder');

        if (cart.length === 0) {
            container.innerHTML = '<div id="emptyCartNotice" style="text-align: center; color: var(--text-muted); font-size: 0.8125rem; padding: 2rem 0;">ဘယ်ဘက်မှ ဟင်းလျာများကို နှိပ်၍ ရွေးချယ်ပါ။<br>(Tap dishes to add to order)</div>';
            document.getElementById('cartSubtotal').innerText = '0 MMK';
            document.getElementById('cartTax').innerText = '0 MMK';
            document.getElementById('cartTotal').innerText = '0 MMK';
            submitBtn.disabled = true;
            return;
        }

        submitBtn.disabled = false;
        container.innerHTML = '';

        let subtotal = 0;

        cart.forEach((it, index) => {
            const lineTotal = it.price * it.qty;
            subtotal += lineTotal;

            const card = document.createElement('div');
            card.className = 'cart-item-row';
            card.innerHTML = `
                <div class="cart-item-header">
                    <span>${it.name}</span>
                    <strong style="color: #9ec63b;">${lineTotal.toLocaleString()} MMK</strong>
                </div>
                <div class="cart-qty-ctrl">
                    <button type="button" class="btn-qty" onclick="updateQty(${it.id}, -1)">-</button>
                    <span style="font-weight: 700; font-size: 0.8125rem; min-width: 20px; text-align: center;">${it.qty}</span>
                    <button type="button" class="btn-qty" onclick="updateQty(${it.id}, 1)">+</button>
                    <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.5rem;">@ ${it.price.toLocaleString()} MMK</span>
                </div>
                <input type="text" class="input-cart-note" placeholder="Special Note (e.g. Less spicy, no onion)" value="${it.notes}" oninput="updateNotes(${it.id}, this.value)">
                <input type="hidden" name="items[${index}][product_id]" value="${it.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${it.qty}">
                <input type="hidden" name="items[${index}][special_notes]" value="${it.notes}">
            `;
            container.appendChild(card);
        });

        const tax = Math.round(subtotal * 0.05);
        const total = subtotal + tax;

        document.getElementById('cartSubtotal').innerText = subtotal.toLocaleString() + ' MMK';
        document.getElementById('cartTax').innerText = tax.toLocaleString() + ' MMK';
        document.getElementById('cartTotal').innerText = total.toLocaleString() + ' MMK';
    }

    async function handleOrderSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        // sync cart items into formData
        cart.forEach((it, idx) => {
            formData.set(`items[${idx}][product_id]`, it.id);
            formData.set(`items[${idx}][quantity]`, it.qty);
            formData.set(`items[${idx}][special_notes]`, it.notes || '');
        });

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await res.json();
            if (result.success) {
                closeOrderModal();
                if (result.kitchen_chit_url) {
                    window.open(result.kitchen_chit_url, '_blank');
                }
                window.location.reload();
            } else {
                alert(result.message || 'Error submitting order.');
            }
        } catch (err) {
            console.error(err);
            form.submit();
        }
    }

    // Add Items Repeat Round Modal
    function openAddItemsModal(orderId, tableNumber, orderNumber) {
        document.getElementById('addItemsForm').action = `/staff/orders/${orderId}/add-items`;
        document.getElementById('addItemsModalTitle').innerText = `Add Dishes: Table ${tableNumber}`;
        document.getElementById('addItemsOrderMeta').innerText = `Order: ${orderNumber}`;
        addRoundCart = [];
        renderAddRoundCart();
        document.getElementById('addItemsModal').classList.add('show');
    }

    function closeAddItemsModal() {
        document.getElementById('addItemsModal').classList.remove('show');
    }

    function addToAddRoundCart(id, name, price) {
        const existing = addRoundCart.find(i => i.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            addRoundCart.push({ id, name, price, qty: 1, notes: '' });
        }
        renderAddRoundCart();
    }

    function updateAddRoundQty(id, delta) {
        const item = addRoundCart.find(i => i.id === id);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) {
            addRoundCart = addRoundCart.filter(i => i.id !== id);
        }
        renderAddRoundCart();
    }

    function updateAddRoundNotes(id, text) {
        const item = addRoundCart.find(i => i.id === id);
        if (item) item.notes = text;
    }

    function renderAddRoundCart() {
        const container = document.getElementById('addRoundCartContainer');
        const submitBtn = document.getElementById('btnSubmitAddRound');

        if (addRoundCart.length === 0) {
            container.innerHTML = '<div id="emptyAddNotice" style="text-align: center; color: var(--text-muted); font-size: 0.8125rem; padding: 2rem 0;">ဟင်းလျာများကို ရွေးချယ်ပါ။</div>';
            submitBtn.disabled = true;
            return;
        }

        submitBtn.disabled = false;
        container.innerHTML = '';

        addRoundCart.forEach((it, index) => {
            const card = document.createElement('div');
            card.className = 'cart-item-row';
            card.innerHTML = `
                <div class="cart-item-header">
                    <span>${it.name}</span>
                    <strong style="color: #9ec63b;">${(it.price * it.qty).toLocaleString()} MMK</strong>
                </div>
                <div class="cart-qty-ctrl">
                    <button type="button" class="btn-qty" onclick="updateAddRoundQty(${it.id}, -1)">-</button>
                    <span style="font-weight: 700; font-size: 0.8125rem; min-width: 20px; text-align: center;">${it.qty}</span>
                    <button type="button" class="btn-qty" onclick="updateAddRoundQty(${it.id}, 1)">+</button>
                </div>
                <input type="text" class="input-cart-note" placeholder="Special Note" value="${it.notes}" oninput="updateAddRoundNotes(${it.id}, this.value)">
                <input type="hidden" name="items[${index}][product_id]" value="${it.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${it.qty}">
                <input type="hidden" name="items[${index}][special_notes]" value="${it.notes}">
            `;
            container.appendChild(card);
        });
    }

    async function handleAddItemsSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        addRoundCart.forEach((it, idx) => {
            formData.set(`items[${idx}][product_id]`, it.id);
            formData.set(`items[${idx}][quantity]`, it.qty);
            formData.set(`items[${idx}][special_notes]`, it.notes || '');
        });

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await res.json();
            if (result.success) {
                closeAddItemsModal();
                window.location.reload();
            } else {
                alert(result.message || 'Error adding items.');
            }
        } catch (err) {
            console.error(err);
            form.submit();
        }
    }
</script>
@endpush
