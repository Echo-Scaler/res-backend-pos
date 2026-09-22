@extends('admin.layouts.app')

@section('title', 'Admin Executive Dashboard')

@push('styles')
<style>
    /* PreAdmin Style Page Breadcrumb */
    .page-breadcrumb {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title-box h4 {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
    }

    .breadcrumb-nav a {
        color: var(--text-muted);
        text-decoration: none;
    }

    .breadcrumb-nav a:hover {
        color: var(--primary);
    }

    .date-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 0.45rem 0.95rem;
        border-radius: var(--radius-sm);
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-main);
        box-shadow: var(--shadow-sm);
    }

    .date-filter-pill i {
        color: var(--primary);
        font-size: 1rem;
    }

    /* Cards Base */
    .card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .card:hover {
        box-shadow: var(--shadow-md);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-header-clean {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 1rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-subtle);
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .card-header-clean h5 {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-header-clean .card-link {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--primary);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: var(--transition);
    }

    .card-header-clean .card-link:hover {
        color: var(--primary-hover);
        text-decoration: underline;
    }

    /* 1. Hero Welcome Wrap (PreAdmin Rental Welcome Card) */
    .welcome-card {
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-hover) 100%);
        border: 1px solid var(--border-color);
        margin-bottom: 1.5rem;
    }

    .welcome-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 2rem;
        align-items: center;
    }

    .welcome-title {
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.35rem;
    }

    .welcome-desc {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-bottom: 1.25rem;
    }

    .welcome-stats-row {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }

    .welcome-stat-item p {
        font-size: 0.75rem;
        color: var(--text-light);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.05em;
        margin-bottom: 0.2rem;
    }

    .welcome-stat-item h3 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .welcome-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-hero-primary {
        background-color: var(--primary);
        color: #ffffff;
        padding: 0.55rem 1.25rem;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.3);
        transition: var(--transition);
    }

    .btn-hero-primary:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-hero-dark {
        background-color: var(--secondary);
        color: #ffffff;
        padding: 0.55rem 1.25rem;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        transition: var(--transition);
    }

    .btn-hero-dark:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .welcome-art {
        width: 170px;
        height: 140px;
        border-radius: var(--radius-lg);
        background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.1), rgba(251, 146, 60, 0.15));
        border: 1px dashed rgba(var(--primary-rgb), 0.3);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-align: center;
        padding: 1rem;
    }

    .welcome-art i {
        font-size: 3rem;
        color: var(--primary);
    }

    .welcome-art span {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--primary);
    }

    /* 2. Top KPI Metric Grid (PreAdmin 4-Card Layout) */
    .kpi-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .kpi-card-header {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-subtle);
        margin-bottom: 0.75rem;
    }

    .kpi-avatar-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .kpi-avatar-green {
        background-color: var(--success-light);
        color: var(--success);
    }
    .kpi-avatar-orange {
        background-color: var(--primary-light);
        color: var(--primary);
    }
    .kpi-avatar-violet {
        background-color: var(--violet-light);
        color: var(--violet);
    }
    .kpi-avatar-blue {
        background-color: var(--info-light);
        color: var(--info);
    }

    .kpi-title-text {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .kpi-content-box {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
    }

    .kpi-val-number {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
        margin-bottom: 0.35rem;
    }

    .kpi-trend-pill {
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .trend-up {
        color: var(--success);
    }

    .sparkline-chart-slot {
        width: 80px;
        height: 45px;
    }

    /* 3. 2-Column Main Section */
    .grid-2col {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    /* Table Dining Visualizer */
    .table-floor-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.85rem;
    }

    .table-box-item {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        background-color: var(--bg-hover);
        padding: 0.85rem;
        position: relative;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 110px;
    }

    .table-box-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .table-status-indicator {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .table-box-item.occupied {
        border-color: rgba(239, 68, 68, 0.4);
        background-color: var(--danger-light);
    }
    .table-box-item.occupied .table-status-indicator {
        background-color: var(--danger);
        box-shadow: 0 0 6px var(--danger);
    }

    .table-box-item.available {
        border-color: rgba(16, 185, 129, 0.4);
        background-color: var(--success-light);
    }
    .table-box-item.available .table-status-indicator {
        background-color: var(--success);
        box-shadow: 0 0 6px var(--success);
    }

    .table-box-item.billing {
        border-color: rgba(245, 158, 11, 0.4);
        background-color: var(--warning-light);
    }
    .table-box-item.billing .table-status-indicator {
        background-color: var(--warning);
        box-shadow: 0 0 6px var(--warning);
    }

    .table-box-item.reserved {
        border-color: rgba(139, 92, 246, 0.4);
        background-color: var(--violet-light);
    }
    .table-box-item.reserved .table-status-indicator {
        background-color: var(--violet);
        box-shadow: 0 0 6px var(--violet);
    }

    .table-code-name {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .table-capacity-tag {
        font-size: 0.72rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .table-footer-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* Featured Dish Card */
    .featured-dish-card .dish-img-wrap {
        width: 100%;
        height: 170px;
        border-radius: var(--radius-md);
        overflow: hidden;
        margin-bottom: 1rem;
        position: relative;
    }

    .featured-dish-card .dish-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .dish-badge-overlay {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(4px);
        color: #ffffff;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
    }

    .dish-specs-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        margin: 1rem 0;
    }

    .dish-spec-box {
        background-color: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 0.5rem;
        text-align: center;
    }

    .dish-spec-box .label {
        font-size: 0.7rem;
        color: var(--text-light);
        display: block;
    }

    .dish-spec-box .val {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-main);
    }

    /* PreAdmin Custom Tables */
    .preadmin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .preadmin-table th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-light);
        padding: 0.75rem 0.85rem;
        border-bottom: 1px solid var(--border-color);
        text-align: left;
    }

    .preadmin-table td {
        padding: 0.85rem;
        border-bottom: 1px solid var(--border-subtle);
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .preadmin-table tr:last-child td {
        border-bottom: none;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-cell img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Status Pills */
    .status-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .status-completed {
        background-color: var(--success-light);
        color: var(--success);
    }

    .status-dining {
        background-color: var(--info-light);
        color: var(--info);
    }

    .status-billing {
        background-color: var(--warning-light);
        color: var(--warning);
    }

    .status-critical {
        background-color: var(--danger-light);
        color: var(--danger);
    }

    .status-warning {
        background-color: var(--warning-light);
        color: var(--warning);
    }

    /* 17 Operational Modules Tiles */
    .modules-section-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 1.5rem 0 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modules-tiles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .module-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.15rem;
        text-decoration: none;
        color: var(--text-main);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        box-shadow: var(--shadow-sm);
    }

    .module-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .module-icon-box {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        margin-bottom: 0.35rem;
    }

    .module-title {
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--text-main);
    }

    .module-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        line-height: 1.4;
    }

    @media (max-width: 1200px) {
        .kpi-row {
            grid-template-columns: repeat(2, 1fr);
        }
        .grid-2col {
            grid-template-columns: 1fr;
        }
        .table-floor-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .kpi-row {
            grid-template-columns: 1fr;
        }
        .welcome-grid {
            grid-template-columns: 1fr;
        }
        .welcome-art {
            display: none;
        }
        .table-floor-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<!-- 1. Breadcrumb Bar -->
<div class="page-breadcrumb">
    <div class="page-title-box">
        <h4>Admin Executive Dashboard</h4>
        <div class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home me-1"></i>Home</a>
            <span>/</span>
            <span>Admin Dashboard</span>
            <span>/</span>
            <span style="color: var(--primary); font-weight: 700;">{{ $restaurant->name ?? 'Main Branch' }}</span>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <div class="date-filter-pill">
            <i class="ti ti-calendar"></i>
            <span>{{ now()->format('d M Y') }} (Today)</span>
        </div>
    </div>
</div>

<!-- Owner Authority Status Banner -->
<div style="background-color: rgba(var(--primary-rgb), 0.08); border: 1px solid rgba(var(--primary-rgb), 0.25); color: var(--primary); padding: 0.85rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
    <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem;">
        <i class="ti ti-shield-check" style="font-size: 1.25rem;"></i>
        <span><strong>Owner Authority Active:</strong> You have full, unrestricted permissions to control all restaurant operations, employees, managers, financials, and settings.</span>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center;">
        <span style="font-size: 0.75rem; background: rgba(var(--primary-rgb), 0.15); padding: 0.2rem 0.6rem; border-radius: 9999px; font-weight: 700;">Unrestricted (100%)</span>
        <span style="font-size: 0.75rem; background: var(--success-light); color: var(--success); padding: 0.2rem 0.6rem; border-radius: 9999px; font-weight: 700;">Full Control (Owner Only)</span>
    </div>
</div>

<!-- 2. Hero Welcome Card (PreAdmin Rental Style) -->
<div class="card welcome-card">
    <div class="card-body">
        <div class="welcome-grid">
            <div>
                <h3 class="welcome-title">Welcome back, {{ Auth::user()->name }} 👋</h3>
                <p class="welcome-desc">
                    Executive overview for <strong>{{ $restaurant->name ?? 'Restaurant POS' }}</strong>. Real-time floor occupancy, daily revenue stream, inventory safety limits, and staff operations.
                </p>
                <div class="welcome-stats-row">
                    <div class="welcome-stat-item">
                        <p>Total Revenue Today</p>
                        <h3>{{ number_format($metrics['today_sales'] ?? 1450000) }} <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">MMK</span></h3>
                    </div>
                    <div class="welcome-stat-item">
                        <p>Active Floor Dining</p>
                        <h3>18 <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">/ 24 Tables</span></h3>
                    </div>
                    <div class="welcome-stat-item">
                        <p>Kitchen Orders</p>
                        <h3>86 <span style="font-size: 0.85rem; font-weight: 600; color: var(--success);">Tickets</span></h3>
                    </div>
                </div>
                <div class="welcome-actions">
                    <a href="{{ route('admin.orders.index') }}" class="btn-hero-primary">
                        <i class="ti ti-receipt"></i>
                        <span>Live Orders</span>
                    </a>
                    <a href="{{ route('admin.tables.index') }}" class="btn-hero-dark">
                        <i class="ti ti-layout-grid"></i>
                        <span>Table Floor Plan</span>
                    </a>
                    <a href="{{ route('admin.menu.index') }}" class="btn-hero-dark" style="background: transparent; color: var(--text-main); border: 1px solid var(--border-color);">
                        <i class="ti ti-tools-kitchen-2"></i>
                        <span>Menu Catalog</span>
                    </a>
                </div>
            </div>
            <div class="welcome-art">
                <i class="ti ti-tools-kitchen"></i>
                <span>POS LIVE ACTIVE</span>
            </div>
        </div>
    </div>
</div>

<!-- 3. Top 4 KPI Metric Cards with Apex Sparklines -->
<div class="kpi-row">
    <!-- KPI 1: Today's Sales -->
    <div class="card">
        <div class="card-body">
            <div class="kpi-card-header">
                <div class="kpi-avatar-icon kpi-avatar-green">
                    <i class="ti ti-currency-dollar"></i>
                </div>
                <span class="kpi-title-text">Today's Sales</span>
            </div>
            <div class="kpi-content-box">
                <div>
                    <div class="kpi-val-number">{{ number_format($metrics['today_sales'] ?? 1450000) }} <span style="font-size: 0.8rem; font-weight: 600;">MMK</span></div>
                    <div class="kpi-trend-pill trend-up">
                        <i class="ti ti-arrow-up-right"></i>
                        <span>+14.8% vs yesterday</span>
                    </div>
                </div>
                <div id="sparkline-sales" class="sparkline-chart-slot"></div>
            </div>
        </div>
    </div>

    <!-- KPI 2: Today's Orders -->
    <div class="card">
        <div class="card-body">
            <div class="kpi-card-header">
                <div class="kpi-avatar-icon kpi-avatar-blue">
                    <i class="ti ti-receipt"></i>
                </div>
                <span class="kpi-title-text">Today's Orders</span>
            </div>
            <div class="kpi-content-box">
                <div>
                    <div class="kpi-val-number">{{ $metrics['today_orders'] ?? 86 }}</div>
                    <div class="kpi-trend-pill" style="color: var(--info);">
                        <span>82 Completed • 4 Active</span>
                    </div>
                </div>
                <div id="sparkline-orders" class="sparkline-chart-slot"></div>
            </div>
        </div>
    </div>

    <!-- KPI 3: Average Order Value (AOV) -->
    <div class="card">
        <div class="card-body">
            <div class="kpi-card-header">
                <div class="kpi-avatar-icon kpi-avatar-orange">
                    <i class="ti ti-chart-pie"></i>
                </div>
                <span class="kpi-title-text">Average Order Value</span>
            </div>
            <div class="kpi-content-box">
                <div>
                    <div class="kpi-val-number">{{ number_format($metrics['average_order_value'] ?? 16860) }} <span style="font-size: 0.8rem; font-weight: 600;">MMK</span></div>
                    <div class="kpi-trend-pill trend-up">
                        <i class="ti ti-arrow-up-right"></i>
                        <span>+5.2% avg table spend</span>
                    </div>
                </div>
                <div id="sparkline-aov" class="sparkline-chart-slot"></div>
            </div>
        </div>
    </div>

    <!-- KPI 4: Cancelled / Refunded Orders -->
    <div class="card">
        <div class="card-body">
            <div class="kpi-card-header">
                <div class="kpi-avatar-icon" style="background-color: var(--danger-light); color: var(--danger);">
                    <i class="ti ti-ban"></i>
                </div>
                <span class="kpi-title-text">Cancelled / Refunded</span>
            </div>
            <div class="kpi-content-box">
                <div>
                    <div class="kpi-val-number" style="color: var(--danger);">{{ count($metrics['cancelled_refunded_orders'] ?? []) }} Orders</div>
                    <div class="kpi-trend-pill" style="color: var(--text-muted);">
                        <span>36,500 MMK total voided</span>
                    </div>
                </div>
                <div id="sparkline-occupancy" class="sparkline-chart-slot"></div>
            </div>
        </div>
    </div>
</div>

<!-- 4. Middle Row: Live Floor Tracker & Featured Dish -->
<div class="grid-2col">
    <!-- Live Table Floor Visualizer (PreAdmin Live Tracking Style) -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-radar" style="color: var(--primary);"></i>
                    <span>Live Floor & Table Tracker ({{ $metrics['table_occupancy']['rate_percentage'] ?? 75 }}% Occupancy)</span>
                </h5>
                <a href="{{ route('admin.tables.index') }}" class="card-link">Floor Management →</a>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem; font-size: 0.75rem; flex-wrap: wrap;">
                <span style="display: flex; align-items: center; gap: 0.35rem;"><span style="width: 10px; height: 10px; border-radius: 50%; background: var(--success);"></span> Available ({{ $metrics['table_occupancy']['available'] ?? 4 }})</span>
                <span style="display: flex; align-items: center; gap: 0.35rem;"><span style="width: 10px; height: 10px; border-radius: 50%; background: var(--danger);"></span> Occupied ({{ $metrics['table_occupancy']['occupied'] ?? 18 }})</span>
                <span style="display: flex; align-items: center; gap: 0.35rem;"><span style="width: 10px; height: 10px; border-radius: 50%; background: var(--warning);"></span> Billing</span>
                <span style="display: flex; align-items: center; gap: 0.35rem;"><span style="width: 10px; height: 10px; border-radius: 50%; background: var(--violet);"></span> Reserved ({{ $metrics['table_occupancy']['reserved'] ?? 2 }})</span>
            </div>

            <div class="table-floor-grid">
                @foreach($metrics['floor_tables'] ?? [] as $table)
                    @php
                        $statusClass = strtolower($table['status']);
                    @endphp
                    <div class="table-box-item {{ $statusClass }}">
                        <span class="table-status-indicator"></span>
                        <div>
                            <div class="table-code-name">{{ $table['name'] }}</div>
                            <div class="table-capacity-tag">{{ $table['capacity'] }} • {{ $table['server'] }}</div>
                        </div>
                        <div class="table-footer-info">
                            <span style="color: var(--text-muted); font-size: 0.72rem;">{{ $table['elapsed'] }}</span>
                            <span style="color: var(--text-main);">{{ $table['spent'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featured / Recommendation Dish (PreAdmin Newly Added Car Style) -->
    <div class="card featured-dish-card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-star" style="color: #f59e0b;"></i>
                    <span>Top Recommendation</span>
                </h5>
                <a href="{{ route('admin.menu.index') }}" class="card-link">Menu →</a>
            </div>

            <div class="dish-img-wrap">
                <img src="{{ $metrics['featured_dish']['image'] ?? 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80' }}" alt="Dish">
                <span class="dish-badge-overlay">{{ $metrics['featured_dish']['category'] ?? 'Chef Special' }}</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                <h6 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main);">{{ $metrics['featured_dish']['name'] ?? 'Shan Noodle Special Set' }}</h6>
                <span style="font-weight: 800; color: var(--primary); font-size: 1rem;">{{ $metrics['featured_dish']['formatted_price'] ?? '6,000 MMK' }}</span>
            </div>
            <p style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.5rem;">Authentic traditional recipe with crispy garlic oil & pickled mustard greens.</p>

            <div class="dish-specs-grid">
                <div class="dish-spec-box">
                    <span class="label">Prep Time</span>
                    <span class="val">{{ $metrics['featured_dish']['prep_time'] ?? '10 Mins' }}</span>
                </div>
                <div class="dish-spec-box">
                    <span class="label">Spice Level</span>
                    <span class="val">{{ $metrics['featured_dish']['spice_level'] ?? 'Mild' }}</span>
                </div>
                <div class="dish-spec-box">
                    <span class="label">Sold Today</span>
                    <span class="val" style="color: var(--primary);">{{ $metrics['featured_dish']['sold_qty'] ?? 42 }} Bowls</span>
                </div>
            </div>

            <a href="{{ route('admin.menu.index') }}" class="btn-hero-dark" style="width: 100%; justify-content: center; background-color: var(--bg-hover); color: var(--text-main); border: 1px solid var(--border-color);">
                <span>View Full Menu & Pricing</span>
                <i class="ti ti-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- 5. Analytics Row: Sales by Date (ApexCharts) & Payment Breakdown -->
<div class="grid-2col">
    <!-- Sales by Date -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <div>
                    <h5>
                        <i class="ti ti-chart-histogram" style="color: var(--primary);"></i>
                        <span>Sales by Date (Last 7 Days)</span>
                    </h5>
                    <span style="font-size: 0.78rem; color: var(--text-muted);">Daily earnings breakdown</span>
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <span style="font-size: 0.75rem; background: var(--primary-light); color: var(--primary); padding: 0.2rem 0.6rem; border-radius: 9999px; font-weight: 700;">Live Week</span>
                </div>
            </div>

            <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
                <div style="padding: 0.65rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                    <span style="font-size: 0.72rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">7-Day Total</span>
                    <h5 style="font-weight: 800; font-size: 1.15rem; color: var(--text-main); margin-top: 0.15rem;">9,460,000 MMK</h5>
                </div>
                <div style="padding: 0.65rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                    <span style="font-size: 0.72rem; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Daily Average</span>
                    <h5 style="font-weight: 800; font-size: 1.15rem; color: var(--success); margin-top: 0.15rem;">1,351,400 MMK</h5>
                </div>
            </div>

            <div id="chart-revenue-weekly" style="min-height: 250px;"></div>
        </div>
    </div>

    <!-- Payment Breakdown (Donut Chart) -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-wallet" style="color: var(--violet);"></i>
                    <span>Payment Breakdown</span>
                </h5>
                <a href="{{ route('admin.payments.index') }}" class="card-link">Details →</a>
            </div>

            <div id="chart-payment-donut" style="min-height: 190px; margin-bottom: 1rem;"></div>

            <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                @foreach($metrics['payment_breakdown'] ?? [] as $pay)
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem;">
                        <span style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                            <span>{{ $pay['icon'] }}</span>
                            <span>{{ $pay['method'] }}</span>
                        </span>
                        <span style="font-weight: 700; color: var(--text-main);">
                            {{ number_format($pay['amount']) }} MMK <span style="color: var(--text-muted); font-size: 0.75rem; font-weight: 500;">({{ $pay['percentage'] }}%)</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- 6. Sales by Category & Low-Stock Alerts -->
<div class="grid-2col">
    <!-- Sales by Category -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-category" style="color: var(--primary);"></i>
                    <span>Sales by Category</span>
                </h5>
                <span style="font-size: 0.78rem; color: var(--text-muted);">Division revenue contribution</span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                @foreach($metrics['sales_by_category'] ?? [] as $cat)
                    <div style="background: var(--bg-hover); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: 1.35rem;">{{ $cat['icon'] }}</span>
                            <span style="font-weight: 800; font-size: 1.1rem; color: {{ $cat['color'] }};">{{ $cat['percentage'] }}%</span>
                        </div>
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);">{{ $cat['name'] }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem;">
                            {{ number_format($cat['amount']) }} MMK
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Kitchen Low-Stock Alerts (PreAdmin Maintenance Style) -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-alert-triangle" style="color: var(--danger);"></i>
                    <span>Low-Stock Alerts</span>
                </h5>
                <a href="{{ route('admin.inventory.index') }}" class="card-link">Inventory →</a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($metrics['low_stock_items'] ?? [] as $stock)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-hover);">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: {{ $stock['status'] === 'CRITICAL' ? 'var(--danger-light)' : 'var(--warning-light)' }}; color: {{ $stock['status'] === 'CRITICAL' ? 'var(--danger)' : 'var(--warning)' }}; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                                <i class="ti ti-box"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);">{{ $stock['item'] }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    Current: <strong style="color: var(--danger);">{{ $stock['current_stock'] }}</strong> (Safe Min: {{ $stock['threshold'] }})
                                </div>
                            </div>
                        </div>
                        <span class="status-badge {{ $stock['status'] === 'CRITICAL' ? 'status-critical' : 'status-warning' }}">
                            {{ $stock['status'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- 7. Best-Selling Dishes & Products & Real-Time Staff Activity -->
<div class="grid-2col">
    <!-- Best-Selling Dishes & Products -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-flame" style="color: var(--primary);"></i>
                    <span>Best-Selling Dishes & Products</span>
                </h5>
                <a href="{{ route('admin.menu.index') }}" class="card-link">All Menu →</a>
            </div>

            <table class="preadmin-table">
                <thead>
                    <tr>
                        <th>Dish Name</th>
                        <th>Category</th>
                        <th style="text-align: center;">Sold Qty</th>
                        <th style="text-align: right;">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($metrics['best_selling_products'] ?? [] as $product)
                        <tr>
                            <td style="font-weight: 700; color: var(--text-main);">{{ $product['name'] }}</td>
                            <td>
                                <span style="font-size: 0.72rem; background: var(--bg-hover); color: var(--text-muted); padding: 0.15rem 0.5rem; border-radius: 4px; border: 1px solid var(--border-color);">
                                    {{ $product['category'] }}
                                </span>
                            </td>
                            <td style="text-align: center; font-weight: 700; color: var(--info);">{{ $product['sold_qty'] }}</td>
                            <td style="text-align: right; font-weight: 700; color: var(--success);">{{ number_format($product['revenue']) }} MMK</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Real-Time Staff Activity -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-users" style="color: var(--info);"></i>
                    <span>Real-Time Staff Activity</span>
                </h5>
                <a href="{{ route('admin.employees.index') }}" class="card-link">Staff List →</a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($metrics['staff_activity'] ?? [] as $staff)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--success); box-shadow: 0 0 6px var(--success);"></span>
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);">
                                    {{ $staff['name'] }}
                                    <span style="font-size: 0.68rem; padding: 0.1rem 0.4rem; border-radius: 4px; background: var(--primary-light); color: var(--primary); font-weight: 700; margin-left: 0.3rem;">
                                        {{ $staff['role'] }}
                                    </span>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $staff['action'] }}</div>
                            </div>
                        </div>
                        <span style="font-size: 0.72rem; color: var(--text-light);">{{ $staff['last_active'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- 8. Recent Live Orders & Cancelled/Refunded Orders -->
<div class="grid-2col">
    <!-- Recent Live Orders (PreAdmin Recent Reservations Style) -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-receipt-2" style="color: var(--primary);"></i>
                    <span>Recent Live Orders</span>
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="card-link">All Orders (86) →</a>
            </div>

            <div style="overflow-x: auto;">
                <table class="preadmin-table">
                    <thead>
                        <tr>
                            <th>Order & Table</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($metrics['recent_orders'] ?? [] as $order)
                            @php
                                $statusBadge = match($order['status']) {
                                    'COMPLETED' => 'status-completed',
                                    'IN_DINING' => 'status-dining',
                                    'BILLING' => 'status-billing',
                                    default => 'status-dining',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);">{{ $order['order_code'] }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $order['table'] }}</div>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <img src="{{ $order['customer_avatar'] }}" alt="">
                                        <div>
                                            <div style="font-weight: 600;">{{ $order['customer_name'] }}</div>
                                            <div style="font-size: 0.72rem; color: var(--text-light);">{{ $order['dining_type'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);">{{ number_format($order['amount']) }} MMK</div>
                                    <div style="font-size: 0.72rem; color: var(--text-light);">{{ $order['payment_method'] }}</div>
                                </td>
                                <td>
                                    <span class="status-badge {{ $statusBadge }}">
                                        {{ $order['status_label'] }}
                                    </span>
                                </td>
                                <td style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $order['time'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted);">No orders recorded today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cancelled & Refunded Orders Audit -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-ban" style="color: var(--danger);"></i>
                    <span>Cancelled / Refunded Orders Audit</span>
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="card-link">Void Logs →</a>
            </div>

            <table class="preadmin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Table</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($metrics['cancelled_refunded_orders'] ?? [] as $cancelled)
                        <tr>
                            <td style="font-weight: 700; color: var(--text-main);">{{ $cancelled['order_code'] }}</td>
                            <td style="font-size: 0.78rem;">{{ $cancelled['table'] }}</td>
                            <td style="font-weight: 700; color: var(--danger);">{{ number_format($cancelled['amount']) }} MMK</td>
                            <td style="font-size: 0.75rem; color: var(--text-muted);">{{ $cancelled['reason'] }}</td>
                            <td>
                                <span class="status-badge status-critical">
                                    {{ $cancelled['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">No cancelled tickets today.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 9. Back-Office Operational Modules Directory (17 Modules) -->
<h3 class="modules-section-title">
    <i class="ti ti-apps" style="color: var(--primary);"></i>
    <span>Back-Office Governance Modules (17 Core Portals)</span>
</h3>

<div class="modules-tiles-grid">
    <a href="{{ route('admin.settings.restaurant') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--primary-light); color: var(--primary);"><i class="ti ti-building-store"></i></div>
        <span class="module-title">Restaurant Settings</span>
        <span class="module-desc">Store profile, operating hours & branding.</span>
    </a>

    <a href="{{ route('admin.employees.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--info-light); color: var(--info);"><i class="ti ti-users-group"></i></div>
        <span class="module-title">Employee Directory</span>
        <span class="module-desc">Managers, cashiers, servers & fast PINs.</span>
    </a>

    <a href="{{ route('admin.roles.permissions') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--violet-light); color: var(--violet);"><i class="ti ti-shield-lock"></i></div>
        <span class="module-title">Roles & Permissions</span>
        <span class="module-desc">Spatie RBAC matrix & access governance.</span>
    </a>

    <a href="{{ route('admin.menu.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--success-light); color: var(--success);"><i class="ti ti-tools-kitchen-2"></i></div>
        <span class="module-title">Menu & Products</span>
        <span class="module-desc">Dishes, modifiers, kitchen routing & prices.</span>
    </a>

    <a href="{{ route('admin.inventory.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--warning-light); color: var(--warning);"><i class="ti ti-packages"></i></div>
        <span class="module-title">Inventory & Stock</span>
        <span class="module-desc">Raw ingredients, reorder thresholds & alerts.</span>
    </a>

    <a href="{{ route('admin.tables.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--info-light); color: var(--info);"><i class="ti ti-layout-grid"></i></div>
        <span class="module-title">Table Floor Layout</span>
        <span class="module-desc">Dining layout, sections, occupancy & QR menus.</span>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--primary-light); color: var(--primary);"><i class="ti ti-receipt"></i></div>
        <span class="module-title">Order Management</span>
        <span class="module-desc">Live KDS tickets, order splits & refunds.</span>
    </a>

    <a href="{{ route('admin.payments.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--violet-light); color: var(--violet);"><i class="ti ti-credit-card"></i></div>
        <span class="module-title">Payment Management</span>
        <span class="module-desc">Cash drawer sessions, WavePay & KBZPay QR.</span>
    </a>

    <a href="{{ route('admin.customers.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--success-light); color: var(--success);"><i class="ti ti-user-heart"></i></div>
        <span class="module-title">Customer CRM</span>
        <span class="module-desc">Loyalty points, member tiers & dining history.</span>
    </a>

    <a href="{{ route('admin.promotions.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--warning-light); color: var(--warning);"><i class="ti ti-discount-2"></i></div>
        <span class="module-title">Discounts & Promos</span>
        <span class="module-desc">Happy hour discounts, coupons & vouchers.</span>
    </a>

    <a href="{{ route('admin.reports.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--info-light); color: var(--info);"><i class="ti ti-chart-bar"></i></div>
        <span class="module-title">Reports & Analytics</span>
        <span class="module-desc">P&L statements, peak hours & tax audits.</span>
    </a>

    <a href="{{ route('admin.expenses.index') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--danger-light); color: var(--danger);"><i class="ti ti-cash-register"></i></div>
        <span class="module-title">Expense Management</span>
        <span class="module-desc">Petty cash, market purchases & utility bills.</span>
    </a>

    <a href="{{ route('admin.settings.tax') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--primary-light); color: var(--primary);"><i class="ti ti-receipt-tax"></i></div>
        <span class="module-title">Tax & Service Charge</span>
        <span class="module-desc">Commercial tax (5%) & service rate settings.</span>
    </a>

    <a href="{{ route('admin.settings.business') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--secondary); color: #ffffff;"><i class="ti ti-printer"></i></div>
        <span class="module-title">Business & Printers</span>
        <span class="module-desc">Receipt printers, paper width & cash kickers.</span>
    </a>

    <a href="{{ route('admin.audit.logs') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--violet-light); color: var(--violet);"><i class="ti ti-file-text"></i></div>
        <span class="module-title">Audit Trail Logs</span>
        <span class="module-desc">Immutable trails of logins, voids & price edits.</span>
    </a>

    <a href="{{ route('admin.account.security') }}" class="module-card">
        <div class="module-icon-box" style="background: var(--danger-light); color: var(--danger);"><i class="ti ti-lock-check"></i></div>
        <span class="module-title">Account & Security</span>
        <span class="module-desc">Master passwords, 2FA & session governance.</span>
    </a>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const primaryColor = '#ea580c';
        const successColor = '#10b981';
        const infoColor = '#0284c7';
        const violetColor = '#8b5cf6';

        // 1. Sparkline 1: Today's Sales
        new ApexCharts(document.querySelector("#sparkline-sales"), {
            chart: { type: 'area', height: 45, sparkline: { enabled: true } },
            stroke: { curve: 'smooth', width: 2 },
            fill: { opacity: 0.25 },
            series: [{ data: [25, 66, 41, 89, 63, 25, 85] }],
            colors: [successColor],
            tooltip: { enabled: false }
        }).render();

        // 2. Sparkline 2: Today's Orders
        new ApexCharts(document.querySelector("#sparkline-orders"), {
            chart: { type: 'bar', height: 45, sparkline: { enabled: true } },
            plotOptions: { bar: { columnWidth: '60%', borderRadius: 3 } },
            series: [{ data: [12, 14, 18, 22, 19, 25, 28] }],
            colors: [infoColor],
            tooltip: { enabled: false }
        }).render();

        // 3. Sparkline 3: AOV
        new ApexCharts(document.querySelector("#sparkline-aov"), {
            chart: { type: 'line', height: 45, sparkline: { enabled: true } },
            stroke: { curve: 'smooth', width: 2 },
            series: [{ data: [14, 15, 14.5, 16, 15.8, 16.5, 16.8] }],
            colors: [primaryColor],
            tooltip: { enabled: false }
        }).render();

        // 4. Sparkline 4: Occupancy / Cancelled
        new ApexCharts(document.querySelector("#sparkline-occupancy"), {
            chart: { type: 'line', height: 45, sparkline: { enabled: true } },
            stroke: { curve: 'smooth', width: 2 },
            series: [{ data: [4, 3, 5, 2, 3, 1, 2] }],
            colors: ['#ef4444'],
            tooltip: { enabled: false }
        }).render();

        // 5. Weekly Revenue Stream Column Chart (Sales by Date)
        const salesDates = @json(array_column($metrics['sales_by_date'] ?? [], 'day'));
        const salesAmounts = @json(array_column($metrics['sales_by_date'] ?? [], 'amount'));

        new ApexCharts(document.querySelector("#chart-revenue-weekly"), {
            chart: {
                type: 'bar',
                height: 250,
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '40%',
                    distributed: true
                }
            },
            dataLabels: { enabled: false },
            legend: { show: false },
            colors: [
                '#94a3b8', '#94a3b8', '#94a3b8', '#94a3b8', '#94a3b8', '#94a3b8', primaryColor
            ],
            series: [{
                name: 'Sales (MMK)',
                data: salesAmounts.length > 0 ? salesAmounts : [980000, 1150000, 1280000, 1050000, 1620000, 1890000, 1450000]
            }],
            xaxis: {
                categories: salesDates.length > 0 ? salesDates : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                labels: {
                    style: { colors: '#94a3b8', fontSize: '12px', fontWeight: 600 }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return (val / 1000) + 'k';
                    },
                    style: { colors: '#94a3b8', fontSize: '11px' }
                }
            },
            grid: {
                borderColor: isDark ? '#243048' : '#f1f5f9',
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val.toLocaleString() + ' MMK';
                    }
                }
            }
        }).render();

        // 6. Payment Donut Chart
        new ApexCharts(document.querySelector("#chart-payment-donut"), {
            chart: {
                type: 'donut',
                height: 190,
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            series: [43, 30, 17, 10],
            labels: ['KBZPay QR', 'Cash', 'WavePay', 'Visa/MPU'],
            colors: ['#3b82f6', '#10b981', '#eab308', '#8b5cf6'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: () => '100%'
                            }
                        }
                    }
                }
            }
        }).render();
    });
</script>
@endpush
