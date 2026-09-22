@extends('admin.layouts.app')

@section('title', 'Owner Executive Dashboard')

@push('styles')
<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .dashboard-title {
        font-size: 1.85rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .dashboard-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 0.35rem;
    }

    /* KPI Highlights Grid (4 Key Numbers) */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .kpi-card:hover {
        border-color: rgba(249, 115, 22, 0.4);
        transform: translateY(-2px);
    }

    .kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .kpi-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
    }

    .kpi-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .kpi-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: #f8fafc;
        letter-spacing: -0.02em;
    }

    .kpi-subtext {
        font-size: 0.8rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 600;
    }

    /* 2-Column Analytics Layout */
    .analytics-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 1024px) {
        .analytics-row {
            grid-template-columns: 1fr;
        }
    }

    .card-panel {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(51, 65, 85, 0.6);
    }

    .panel-title {
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .panel-subtitle {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* Sales by Date Bar Chart */
    .chart-bars-container {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.75rem;
        height: 180px;
        padding-top: 1rem;
    }

    .chart-bar-column {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        height: 100%;
        justify-content: flex-end;
    }

    .chart-bar-fill {
        width: 100%;
        max-width: 44px;
        background: linear-gradient(180deg, #f97316, #ea580c);
        border-radius: 6px 6px 2px 2px;
        transition: all 0.3s ease;
        position: relative;
    }

    .chart-bar-fill.today {
        background: linear-gradient(180deg, #10b981, #059669);
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }

    .chart-bar-amount {
        font-size: 0.7rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .chart-bar-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
    }

    /* Payment Breakdown Bars */
    .payment-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .payment-item-header {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        margin-bottom: 0.35rem;
    }

    .progress-track {
        height: 8px;
        background-color: rgba(15, 23, 42, 0.6);
        border-radius: 9999px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        border-radius: 9999px;
    }

    /* Data Tables */
    .compact-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .compact-table th {
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        text-align: left;
        padding: 0.6rem 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .compact-table td {
        padding: 0.75rem;
        border-bottom: 1px solid rgba(51, 65, 85, 0.4);
        vertical-align: middle;
    }

    .compact-table tr:last-child td {
        border-bottom: none;
    }

    /* Badges */
    .badge-critical {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.4);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .badge-warning {
        background: rgba(234, 179, 8, 0.2);
        color: #facc15;
        border: 1px solid rgba(234, 179, 8, 0.4);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    /* Staff List */
    .staff-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.65rem 0;
        border-bottom: 1px solid rgba(51, 65, 85, 0.4);
    }

    .staff-row:last-child {
        border-bottom: none;
    }

    .staff-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .online-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: var(--success);
        box-shadow: 0 0 8px var(--success);
    }

    /* Quick Modules Grid */
    .quick-modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .module-tile {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.15rem;
        text-decoration: none;
        color: var(--text-main);
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .module-tile:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        background-color: rgba(249, 115, 22, 0.05);
    }

    .module-tile-icon {
        font-size: 1.5rem;
    }

    .module-tile-title {
        font-weight: 700;
        font-size: 0.9rem;
    }

    .module-tile-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        line-height: 1.4;
    }
</style>
@endpush

@section('content')
<div class="dashboard-header">
    <div>
        <div class="dashboard-title">
            <span>👑 Owner Executive Portal</span>
            <span style="font-size: 0.85rem; padding: 0.35rem 0.85rem; border-radius: 9999px; background: rgba(249, 115, 22, 0.15); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.3); font-weight: 700;">
                {{ $restaurant->name ?? 'Main Restaurant' }}
            </span>
        </div>
        <p class="dashboard-subtitle">
            Executive oversight, live floor operations, financial analytics, and restaurant governance.
        </p>
    </div>

    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('admin.employees.index') }}" class="btn-action-primary" style="background: var(--primary); color: #fff; padding: 0.6rem 1.1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; text-decoration: none;">
            👥 Manage Employees
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn-action-outline" style="border: 1px solid var(--border); color: var(--text-main); padding: 0.6rem 1.1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; text-decoration: none;">
            🧾 Live Orders
        </a>
    </div>
</div>

<div style="background-color: rgba(249, 115, 22, 0.12); border: 1px solid rgba(249, 115, 22, 0.3); color: #fdba74; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.9rem;">
        <span style="font-size: 1.2rem;">🛡️</span>
        <span><strong>Owner Authority Active:</strong> You have full, unrestricted permissions to control all restaurant operations, employees, managers, financials, and settings.</span>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center;">
        <span style="font-size: 0.8rem; background: rgba(249, 115, 22, 0.2); padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 700;">Unrestricted (100%)</span>
        <span style="font-size: 0.8rem; background: rgba(16, 185, 129, 0.2); color: #6ee7b7; padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 700;">Full Control (Owner Only)</span>
    </div>
</div>

<!-- 1, 2, 3, 7: TOP 4 EXECUTIVE METRIC CARDS -->
<div class="kpi-grid">
    <!-- 1. Today's Sales -->
    <div class="kpi-card">
        <div class="kpi-top">
            <span class="kpi-label">Today's Sales</span>
            <div class="kpi-icon-badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">💰</div>
        </div>
        <div class="kpi-value" style="color: #34d399;">
            {{ number_format($metrics['today_sales'] ?? 1450000) }} <span style="font-size: 0.95rem; font-weight: 600;">MMK</span>
        </div>
        <div class="kpi-subtext" style="color: #34d399;">
            <span>↑ 14.8%</span>
            <span style="color: var(--text-muted); font-weight: 400;">vs yesterday same time</span>
        </div>
    </div>

    <!-- 2. Today's Orders -->
    <div class="kpi-card">
        <div class="kpi-top">
            <span class="kpi-label">Today's Orders</span>
            <div class="kpi-icon-badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">🧾</div>
        </div>
        <div class="kpi-value">{{ $metrics['today_orders'] ?? 86 }}</div>
        <div class="kpi-subtext" style="color: #60a5fa;">
            <span>82 Completed</span>
            <span style="color: var(--text-muted); font-weight: 400;">• 4 Active in dining</span>
        </div>
    </div>

    <!-- 3. Average Order Value (AOV) -->
    <div class="kpi-card">
        <div class="kpi-top">
            <span class="kpi-label">Average Order Value</span>
            <div class="kpi-icon-badge" style="background: rgba(139, 92, 246, 0.15); color: #a78bfa;">📊</div>
        </div>
        <div class="kpi-value">
            {{ number_format($metrics['average_order_value'] ?? 16860) }} <span style="font-size: 0.95rem; font-weight: 600;">MMK</span>
        </div>
        <div class="kpi-subtext" style="color: var(--text-muted);">
            <span>Avg spending per table session</span>
        </div>
    </div>

    <!-- 7. Cancelled / Refunded Orders -->
    <div class="kpi-card">
        <div class="kpi-top">
            <span class="kpi-label">Cancelled / Refunded</span>
            <div class="kpi-icon-badge" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">🚫</div>
        </div>
        <div class="kpi-value" style="color: #f87171;">
            {{ count($metrics['cancelled_refunded_orders'] ?? []) }} Orders
        </div>
        <div class="kpi-subtext" style="color: var(--text-muted);">
            <span>36,500 MMK total voided today</span>
        </div>
    </div>
</div>

<!-- ROW 1: 9. SALES BY DATE & 4. PAYMENT BREAKDOWN -->
<div class="analytics-row">
    <!-- 9. Sales by Date -->
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">📅 Sales by Date (Last 7 Days)</h3>
                <span class="panel-subtitle">Revenue tracking and daily trend</span>
            </div>
            <span style="font-size: 0.8rem; background: rgba(249, 115, 22, 0.1); color: #fb923c; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 700;">Live Week</span>
        </div>

        <div class="chart-bars-container">
            @foreach($metrics['sales_by_date'] ?? [] as $day)
                <div class="chart-bar-column">
                    <span class="chart-bar-amount">{{ number_format($day['amount'] / 1000) }}k</span>
                    <div class="chart-bar-fill {{ $day['is_today'] ? 'today' : '' }}" style="height: {{ max(15, $day['percentage']) }}%;" title="{{ $day['date'] }}: {{ $day['formatted'] }}"></div>
                    <span class="chart-bar-label">{{ $day['day'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. Payment Breakdown -->
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">💳 Payment Breakdown</h3>
                <span class="panel-subtitle">Methods used by diners today</span>
            </div>
        </div>

        <div class="payment-list">
            @foreach($metrics['payment_breakdown'] ?? [] as $pay)
                <div>
                    <div class="payment-item-header">
                        <span style="font-weight: 600;">{{ $pay['icon'] }} {{ $pay['method'] }}</span>
                        <span style="color: var(--text-muted); font-size: 0.8rem;">
                            {{ number_format($pay['amount']) }} MMK ({{ $pay['percentage'] }}%)
                        </span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-bar" style="width: {{ $pay['percentage'] }}%; background-color: {{ $pay['color'] }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ROW 2: 5. BEST-SELLING PRODUCTS & 6. LOW-STOCK ITEMS -->
<div class="analytics-row">
    <!-- 5. Best-Selling Products -->
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">🔥 Best-Selling Dishes & Products</h3>
                <span class="panel-subtitle">Top customer favorites ordered today</span>
            </div>
            <a href="{{ route('admin.menu.index') }}" style="color: var(--primary); font-size: 0.8rem; text-decoration: none; font-weight: 700;">Full Menu →</a>
        </div>

        <table class="compact-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th style="text-align: center;">Sold Qty</th>
                    <th style="text-align: right;">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metrics['best_selling_products'] ?? [] as $product)
                    <tr>
                        <td style="font-weight: 700; color: #f8fafc;">{{ $product['name'] }}</td>
                        <td>
                            <span style="font-size: 0.75rem; background: rgba(51, 65, 85, 0.6); color: #cbd5e1; padding: 0.2rem 0.5rem; border-radius: 4px;">
                                {{ $product['category'] }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: 700; color: #38bdf8;">{{ $product['sold_qty'] }}</td>
                        <td style="text-align: right; font-weight: 700; color: #34d399;">
                            {{ number_format($product['revenue']) }} MMK
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 6. Low-Stock Items -->
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">⚠️ Low-Stock Alerts</h3>
                <span class="panel-subtitle">Ingredients below safe threshold</span>
            </div>
            <a href="{{ route('admin.inventory.index') }}" style="color: var(--primary); font-size: 0.8rem; text-decoration: none; font-weight: 700;">Inventory →</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            @foreach($metrics['low_stock_items'] ?? [] as $stock)
                <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border); border-radius: 10px; padding: 0.85rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 700; font-size: 0.85rem;">{{ $stock['item'] }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">
                            Current: <strong style="color: #f87171;">{{ $stock['current_stock'] }}</strong> (Safety Limit: {{ $stock['threshold'] }})
                        </div>
                    </div>
                    <div>
                        <span class="{{ $stock['status'] === 'CRITICAL' ? 'badge-critical' : 'badge-warning' }}">
                            {{ $stock['status'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ROW 3: 10. SALES BY CATEGORY & 8. STAFF ACTIVITY -->
<div class="analytics-row">
    <!-- 10. Sales by Category -->
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">🍲 Sales by Category</h3>
                <span class="panel-subtitle">Revenue contribution per food division</span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            @foreach($metrics['sales_by_category'] ?? [] as $cat)
                <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border); border-radius: 12px; padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 1.35rem;">{{ $cat['icon'] }}</span>
                        <span style="font-weight: 800; font-size: 1.1rem; color: {{ $cat['color'] }};">{{ $cat['percentage'] }}%</span>
                    </div>
                    <div style="font-weight: 700; font-size: 0.85rem;">{{ $cat['name'] }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">
                        {{ number_format($cat['amount']) }} MMK
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 8. Staff Activity -->
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">👥 Real-Time Staff Activity</h3>
                <span class="panel-subtitle">Logged in floor & back-office members</span>
            </div>
            <a href="{{ route('admin.employees.index') }}" style="color: var(--primary); font-size: 0.8rem; text-decoration: none; font-weight: 700;">View Staff →</a>
        </div>

        <div>
            @forelse($metrics['staff_activity'] ?? [] as $act)
                <div class="staff-row">
                    <div class="staff-left">
                        <span class="online-indicator"></span>
                        <div>
                            <div style="font-weight: 700; font-size: 0.85rem;">
                                {{ $act['name'] }}
                                <span style="font-size: 0.7rem; color: #a5b4fc; background: rgba(99, 102, 241, 0.15); padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.3rem;">
                                    {{ $act['role'] }}
                                </span>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">
                                {{ $act['action'] }}
                            </div>
                        </div>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                        {{ $act['last_active'] }}
                    </div>
                </div>
            @empty
                <div style="color: var(--text-muted); font-size: 0.85rem; padding: 1rem 0;">No active staff found.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- 7. CANCELLED / REFUNDED RECENT TICKETS -->
@if(!empty($metrics['cancelled_refunded_orders']))
<div class="card-panel" style="margin-bottom: 2rem;">
    <div class="panel-header">
        <div>
            <h3 class="panel-title">🚫 Recent Cancelled & Refunded Orders</h3>
            <span class="panel-subtitle">Audit breakdown of voided kitchen & cashier tickets</span>
        </div>
        <a href="{{ route('admin.orders.index') }}" style="color: var(--primary); font-size: 0.8rem; text-decoration: none; font-weight: 700;">Order Log →</a>
    </div>

    <table class="compact-table">
        <thead>
            <tr>
                <th>Order Ticket</th>
                <th>Table</th>
                <th>Amount</th>
                <th>Reason for Cancellation</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($metrics['cancelled_refunded_orders'] as $cancelled)
                <tr>
                    <td style="font-weight: 700; color: #f8fafc;">{{ $cancelled['order_code'] }}</td>
                    <td>{{ $cancelled['table'] }}</td>
                    <td style="font-weight: 700; color: #f87171;">{{ number_format($cancelled['amount']) }} MMK</td>
                    <td style="color: var(--text-muted);">{{ $cancelled['reason'] }}</td>
                    <td style="color: var(--text-muted); font-size: 0.8rem;">{{ $cancelled['time'] }}</td>
                    <td>
                        <span class="{{ $cancelled['status'] === 'CANCELLED' ? 'badge-critical' : 'badge-warning' }}">
                            {{ $cancelled['status'] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- OWNER BACK-OFFICE MODULES DIRECTORY (17 MODULES QUICK TILES) -->
<h2 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1rem;">Back-Office Operational Modules</h2>
<div class="quick-modules-grid">
    <a href="{{ route('admin.settings.restaurant') }}" class="module-tile">
        <span class="module-tile-icon">⚙️</span>
        <span class="module-tile-title">Restaurant Settings</span>
        <span class="module-tile-desc">Store profile, branches, hours & branding.</span>
    </a>

    <a href="{{ route('admin.employees.index') }}" class="module-tile">
        <span class="module-tile-icon">👥</span>
        <span class="module-tile-title">Employee Management</span>
        <span class="module-tile-desc">Managers, cashiers, staff & POS quick PINs.</span>
    </a>

    <a href="{{ route('admin.roles.permissions') }}" class="module-tile">
        <span class="module-tile-icon">🛡️</span>
        <span class="module-tile-title">Roles & Permissions</span>
        <span class="module-tile-desc">RBAC governance & floor authorization matrix.</span>
    </a>

    <a href="{{ route('admin.menu.index') }}" class="module-tile">
        <span class="module-tile-icon">🍕</span>
        <span class="module-tile-title">Menu & Products</span>
        <span class="module-tile-desc">Dishes, modifiers, kitchen routing & prices.</span>
    </a>

    <a href="{{ route('admin.inventory.index') }}" class="module-tile">
        <span class="module-tile-icon">📦</span>
        <span class="module-tile-title">Inventory Management</span>
        <span class="module-tile-desc">Kitchen raw ingredients, stock & low alerts.</span>
    </a>

    <a href="{{ route('admin.tables.index') }}" class="module-tile">
        <span class="module-tile-icon">🪑</span>
        <span class="module-tile-title">Table Management</span>
        <span class="module-tile-desc">Dining layout, sections, occupancy & QR menus.</span>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="module-tile">
        <span class="module-tile-icon">🧾</span>
        <span class="module-tile-title">Order Management</span>
        <span class="module-tile-desc">Live KDS tickets, order splits & refunds.</span>
    </a>

    <a href="{{ route('admin.payments.index') }}" class="module-tile">
        <span class="module-tile-icon">💳</span>
        <span class="module-tile-title">Payment Management</span>
        <span class="module-tile-desc">Cash drawer sessions, WavePay & KBZPay QR.</span>
    </a>

    <a href="{{ route('admin.customers.index') }}" class="module-tile">
        <span class="module-tile-icon">👤</span>
        <span class="module-tile-title">Customer Management</span>
        <span class="module-tile-desc">Loyalty rewards, preferences & dining CRM.</span>
    </a>

    <a href="{{ route('admin.promotions.index') }}" class="module-tile">
        <span class="module-tile-icon">🏷️</span>
        <span class="module-tile-title">Discounts & Promos</span>
        <span class="module-tile-desc">Happy hour discounts, coupons & promos.</span>
    </a>

    <a href="{{ route('admin.reports.index') }}" class="module-tile">
        <span class="module-tile-icon">📈</span>
        <span class="module-tile-title">Reports & Analytics</span>
        <span class="module-tile-desc">P&L statements, peak hours & tax audits.</span>
    </a>

    <a href="{{ route('admin.expenses.index') }}" class="module-tile">
        <span class="module-tile-icon">💰</span>
        <span class="module-tile-title">Expense Management</span>
        <span class="module-tile-desc">Market purchasing, utility bills & petty cash.</span>
    </a>

    <a href="{{ route('admin.settings.tax') }}" class="module-tile">
        <span class="module-tile-icon">📑</span>
        <span class="module-tile-title">Tax & Service Charge</span>
        <span class="module-tile-desc">Commercial tax (5%) & service rate settings.</span>
    </a>

    <a href="{{ route('admin.settings.business') }}" class="module-tile">
        <span class="module-tile-icon">🏢</span>
        <span class="module-tile-title">Business Settings</span>
        <span class="module-tile-desc">Receipt printers, paper width & cash kickers.</span>
    </a>

    <a href="{{ route('admin.audit.logs') }}" class="module-tile">
        <span class="module-tile-icon">📜</span>
        <span class="module-tile-title">Audit Logs</span>
        <span class="module-tile-desc">Immutable trails of logins, voids & price edits.</span>
    </a>

    <a href="{{ route('admin.account.security') }}" class="module-tile">
        <span class="module-tile-icon">🔐</span>
        <span class="module-tile-title">Account / Security</span>
        <span class="module-tile-desc">Master passwords, 2FA & session governance.</span>
    </a>
</div>
@endsection
