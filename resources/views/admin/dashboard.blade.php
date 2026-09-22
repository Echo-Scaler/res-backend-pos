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
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.01em;
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

    /* Calendar Dropdown & Date Filter Pill */
    .calendar-picker-wrapper {
        position: relative;
        display: inline-block;
        z-index: 1000;
    }

    .date-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 0.45rem 1rem;
        border-radius: 9999px;
        font-size: 0.84rem;
        font-weight: 700;
        color: var(--text-main);
        box-shadow: var(--shadow-sm);
        cursor: pointer;
        transition: all 0.2s ease;
        outline: none;
    }

    .date-filter-pill:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .date-filter-pill i.ti-calendar {
        color: var(--primary);
        font-size: 1.1rem;
    }

    .calendar-dropdown-card {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 320px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        padding: 1.15rem;
        z-index: 9999;
        display: none;
    }

    .calendar-presets {
        display: flex;
        gap: 0.4rem;
        margin-bottom: 0.85rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
    }

    .preset-btn {
        flex: 1;
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.4rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-main);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .preset-btn:hover {
        background: var(--primary);
        color: #0c2617;
        border-color: var(--primary);
    }

    .calendar-header-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .cal-month-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .cal-nav-btn {
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--text-main);
        font-size: 0.85rem;
        transition: all 0.15s ease;
    }

    .cal-nav-btn:hover {
        background: var(--bg-hover);
        border-color: var(--primary);
    }

    .cal-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .cal-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 3px;
    }

    .cal-day-cell {
        background: transparent;
        border: none;
        border-radius: 8px;
        height: 32px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-main);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.12s ease;
    }

    .cal-day-cell:hover {
        background: var(--bg-hover);
    }

    .cal-day-cell.cal-day-muted {
        color: var(--text-light);
        opacity: 0.45;
        font-weight: 400;
    }

    .cal-day-cell.cal-day-today {
        border: 1.5px solid var(--primary);
        font-weight: 700;
    }

    .cal-day-cell.cal-day-selected {
        background: var(--primary) !important;
        color: #0c2617 !important;
        font-weight: 700;
    }

    .cal-footer {
        margin-top: 0.85rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 0.5rem;
    }

    .cal-direct-date {
        flex: 1;
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.35rem 0.55rem;
        font-size: 0.78rem;
        color: var(--text-main);
        font-family: inherit;
    }

    .cal-apply-btn {
        background: var(--primary);
        color: #0c2617;
        border: none;
        border-radius: 8px;
        padding: 0.35rem 0.85rem;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
    }

    .cal-apply-btn:hover {
        opacity: 0.9;
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
        min-width: 0;
        max-width: 100%;
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
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--link-color, #245719);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: var(--transition);
    }

    .card-header-clean .card-link:hover {
        color: var(--link-hover, #14380f);
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
        background-color: #0c2617;
        color: #ffffff;
        padding: 0.55rem 1.25rem;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(12, 38, 23, 0.2);
        transition: var(--transition);
        border: 1px solid #0c2617;
    }

    .btn-hero-primary:hover {
        background-color: #1a422b;
        color: #ffffff;
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
        background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.15), rgba(92, 134, 35, 0.12));
        border: 1px dashed rgba(var(--primary-rgb), 0.35);
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

    .grid-2col-equal {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .grid-2col > *, .grid-2col-equal > * {
        min-width: 0;
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
    .table-responsive-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .preadmin-table {
        width: 100%;
        min-width: 480px;
        border-collapse: collapse;
    }

    .preadmin-table th {
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-light);
        padding: 0.8rem 0.85rem;
        border-bottom: 1.5px solid var(--border-color);
        text-align: left;
        white-space: nowrap;
        background-color: var(--bg-card);
    }

    .preadmin-table td {
        padding: 0.85rem;
        border-bottom: 1px solid var(--border-subtle);
        vertical-align: middle;
        font-size: 0.85rem;
        color: var(--text-main);
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

    /* Status Pills with Clear High Contrast */
    .status-badge {
        font-size: 0.74rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
    }

    .status-completed {
        background-color: var(--success-light);
        color: #245719;
        border: 1px solid rgba(126, 168, 38, 0.35);
    }

    .status-dining {
        background-color: #e4ebd7;
        color: #1f4f22;
        border: 1px solid rgba(92, 134, 35, 0.35);
    }

    .status-billing {
        background-color: var(--warning-light);
        color: #8c6b0a;
        border: 1px solid rgba(181, 147, 37, 0.35);
    }

    .status-critical {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid rgba(239, 68, 68, 0.35);
    }

    .status-warning {
        background-color: var(--warning-light);
        color: #8c6b0a;
        border: 1px solid rgba(181, 147, 37, 0.35);
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

    /* ----------------------------------------------------
       Nature / Financial Performance Mockup Color & Layout
       ---------------------------------------------------- */
    .financial-header-card {
        margin-bottom: 1.75rem;
    }

    .brand-eyebrow {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
        letter-spacing: -0.01em;
        margin-bottom: 0.5rem;
    }

    .hero-greeting {
        font-size: 1.65rem;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.02em;
        margin-bottom: 0.25rem;
    }

    .hero-date {
        font-size: 0.92rem;
        color: var(--text-muted);
        font-weight: 400;
    }

    .dashboard-tagline {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.01em;
    }

    .btn-period-pill {
        background-color: var(--bg-card);
        color: var(--text-main);
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.45rem 1.15rem;
        border-radius: 9999px;
        border: 1px solid var(--border-color);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
    }

    .btn-period-pill:hover {
        background-color: var(--bg-hover);
        border-color: var(--primary);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    /* Financial Top 4 KPI Grid */
    .financial-kpi-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.75rem;
        align-items: stretch;
    }

    .financial-kpi-card {
        background-color: var(--bg-card);
        border-radius: 24px;
        padding: 1.4rem 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .financial-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .financial-kpi-card .kpi-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .financial-kpi-card .kpi-title {
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .financial-kpi-card .kpi-dots {
        color: var(--text-light);
        font-size: 1.2rem;
        letter-spacing: 2px;
        line-height: 1;
        cursor: pointer;
        opacity: 0.6;
    }

    .financial-kpi-card .kpi-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.02em;
        margin-bottom: 0.45rem;
        line-height: 1.15;
    }

    .financial-kpi-card .kpi-growth {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.82rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .financial-kpi-card .growth-pill {
        font-weight: 600;
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 0.15rem 0.55rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        font-size: 0.78rem;
    }

    .financial-kpi-card .growth-pill.pill-primary {
        color: #3f6212;
        background: #f7fee7;
        border-color: #d9f99d;
    }

    .financial-kpi-card .growth-pill.pill-info {
        color: #0369a1;
        background: #f0f9ff;
        border-color: #bae6fd;
    }

    .financial-kpi-card .growth-pill.pill-amber {
        color: #b45309;
        background: #fffbeb;
        border-color: #fde68a;
    }

    .financial-kpi-card .kpi-currency {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-left: 0.25rem;
    }

    [data-theme="dark"] .financial-kpi-card .growth-pill {
        color: #86efac;
        background: rgba(34, 197, 94, 0.12);
        border-color: rgba(34, 197, 94, 0.25);
    }

    [data-theme="dark"] .financial-kpi-card .growth-pill.pill-primary {
        color: #bef264;
        background: rgba(158, 198, 59, 0.15);
        border-color: rgba(158, 198, 59, 0.3);
    }

    [data-theme="dark"] .financial-kpi-card .growth-pill.pill-info {
        color: #38bdf8;
        background: rgba(56, 189, 248, 0.12);
        border-color: rgba(56, 189, 248, 0.25);
    }

    [data-theme="dark"] .financial-kpi-card .growth-pill.pill-amber {
        color: #fbbf24;
        background: rgba(251, 191, 36, 0.12);
        border-color: rgba(251, 191, 36, 0.25);
    }

    /* Financial Main 2-Column Section */
    .financial-main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
        align-items: stretch;
    }

    @media (max-width: 992px) {
        .financial-main-grid {
            grid-template-columns: 1fr;
        }
    }

    /* POS Insight Cards (Modern Redesign) */
    .pos-insight-card {
        background-color: var(--bg-card);
        border-radius: 24px;
        border: 1px solid var(--border-color);
        padding: 1.75rem 2rem;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
    }

    .pos-insight-card:hover {
        box-shadow: var(--shadow-md);
    }

    .pos-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.25rem;
    }

    .pos-card-icon-wrap {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .pos-card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .pos-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.01em;
        line-height: 1.3;
    }

    .pos-card-subtitle {
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }

    .pos-card-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
    }

    .pos-card-badge.badge-success {
        background: rgba(158, 198, 59, 0.12);
        color: var(--success);
        border: 1px solid rgba(158, 198, 59, 0.2);
    }

    .pos-card-badge.badge-info {
        background: rgba(92, 134, 35, 0.1);
        color: var(--info);
        border: 1px solid rgba(92, 134, 35, 0.2);
    }

    .pos-chart-container {
        flex: 1;
        min-height: 220px;
    }

    /* Revenue Trend Card Metrics Row */
    .revenue-metrics-strip {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .rev-metric-item {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .rev-metric-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .rev-metric-val {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.02em;
    }

    /* Peak Hours Card */
    .peak-hours-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex: 1;
        justify-content: center;
    }

    .peak-hour-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .peak-hour-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-main);
        min-width: 80px;
        flex-shrink: 0;
    }

    .peak-hour-bar-track {
        flex: 1;
        height: 10px;
        background: var(--bg-hover);
        border-radius: 6px;
        overflow: hidden;
        position: relative;
    }

    .peak-hour-bar-fill {
        height: 100%;
        border-radius: 6px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .peak-hour-value {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text-main);
        min-width: 52px;
        text-align: right;
    }

    .peak-hour-orders {
        font-size: 0.68rem;
        font-weight: 500;
        color: var(--text-muted);
        min-width: 55px;
        text-align: right;
    }

    @media (max-width: 1200px) {
        .financial-kpi-row {
            grid-template-columns: repeat(2, 1fr);
        }
        .financial-main-grid {
            grid-template-columns: 1fr;
        }
        .kpi-row {
            grid-template-columns: repeat(2, 1fr);
        }
        .grid-2col, .grid-2col-equal {
            grid-template-columns: 1fr !important;
        }
        .table-floor-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .financial-kpi-row {
            grid-template-columns: 1fr;
        }
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
        .revenue-submetrics-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .donut-layout-container {
            flex-direction: column;
            align-items: flex-start;
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

    <div class="calendar-picker-wrapper">
        <button type="button" id="calendarPickerTrigger" class="date-filter-pill" aria-expanded="false" title="Click to open interactive calendar">
            <i class="ti ti-calendar"></i>
            <span id="calendarTriggerText">{{ $dateLabel ?? now()->format('d M Y') . ' (Today)' }}</span>
            <i class="ti ti-chevron-down" style="font-size: 0.75rem; color: #5c8623; margin-left: 2px;"></i>
        </button>

        <!-- Floating Interactive Calendar Dropdown Modal -->
        <div id="calendarDropdownMenu" class="calendar-dropdown-card">
            <!-- Quick Date Presets -->
            <div class="calendar-presets">
                <button type="button" class="preset-btn" data-preset="today">Today</button>
                <button type="button" class="preset-btn" data-preset="yesterday">Yesterday</button>
                <button type="button" class="preset-btn" data-preset="this_month">This Month</button>
            </div>

            <!-- Month / Year Header Nav -->
            <div class="calendar-header-nav">
                <button type="button" id="calPrevMonth" class="cal-nav-btn" title="Previous Month">
                    <i class="ti ti-chevron-left"></i>
                </button>
                <div id="calMonthYearTitle" class="cal-month-title">September 2026</div>
                <button type="button" id="calNextMonth" class="cal-nav-btn" title="Next Month">
                    <i class="ti ti-chevron-right"></i>
                </button>
            </div>

            <!-- Weekdays Header -->
            <div class="cal-weekdays">
                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
            </div>

            <!-- Calendar Days Grid -->
            <div id="calDaysGrid" class="cal-days-grid"></div>

            <!-- Direct Date Input Fallback & Apply -->
            <div class="cal-footer">
                <input type="date" id="calDirectInput" value="{{ $selectedDate ?? now()->format('Y-m-d') }}" class="cal-direct-date">
                <button type="button" id="calApplyBtn" class="cal-apply-btn">Apply</button>
            </div>
        </div>

        @if(isset($selectedDate) && $selectedDate !== now()->format('Y-m-d'))
            <a href="{{ route('admin.dashboard') }}" class="btn-today-reset" style="margin-left: 0.5rem; font-size: 0.78rem; font-weight: 700; color: var(--text-main); text-decoration: none; padding: 0.35rem 0.65rem; border-radius: 9999px; background: var(--bg-card); border: 1px solid var(--border-color); display: inline-flex; align-items: center; gap: 0.3rem;" title="Reset to today">
                <i class="ti ti-rotate-clockwise" style="color: var(--primary);"></i> Today
            </a>
        @endif
    </div>
</div>

<!-- ========================================================
     NATURE / FINANCIAL PERFORMANCE DASHBOARD (MOCKUP ACCURATE)
     ======================================================== -->

<!-- 1. Financial Performance Header -->
<div class="financial-header-card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
             <h2 class="welcome-title">Welcome back, {{ Auth::user()->name }} 👋</h2>
            <div class="hero-date">Today is {{ now()->format('l, F d, Y') }}</div>
        </div>
       
    </div>
</div>

<!-- 2. Restaurant Operational Metric KPI Row (Responsive POS Cards - Non-duplicate) -->
<div class="financial-kpi-row">
    <!-- Metric 1: Total Guests / Covers -->
    <div class="financial-kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Total Guests (Covers)</span>
            <span class="kpi-dots">•••</span>
        </div>
        <div class="kpi-value">{{ number_format($metrics['total_guests'] ?? 248) }} <span class="kpi-currency">Guests</span></div>
        <div class="kpi-growth">
            <span>Avg 2.9 per table</span>
            <span class="growth-pill"><i class="ti ti-arrow-up"></i> +14%</span>
        </div>
    </div>

    <!-- Metric 2: Table Turnover Rate -->
    <div class="financial-kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Table Turnover Rate</span>
            <span class="kpi-dots">•••</span>
        </div>
        <div class="kpi-value">{{ $metrics['table_turnover_rate'] ?? 3.6 }}x <span class="kpi-currency">Turns</span></div>
        <div class="kpi-growth">
            <span>52 min avg dining stay</span>
            <span class="growth-pill pill-primary"><i class="ti ti-chart-arrows"></i> Optimal</span>
        </div>
    </div>

    <!-- Metric 3: Kitchen Ticket Speed -->
    <div class="financial-kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Kitchen Prep Speed</span>
            <span class="kpi-dots">•••</span>
        </div>
        <div class="kpi-value">{{ $metrics['avg_kitchen_prep_time'] ?? 12.5 }} <span class="kpi-currency">Mins</span></div>
        <div class="kpi-growth">
            <span>Target &lt; 15 min tickets</span>
            <span class="growth-pill pill-info"><i class="ti ti-bolt"></i> Fast Flow</span>
        </div>
    </div>

    <!-- Metric 4: Cash Drawer Balance -->
    <div class="financial-kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Cash Drawer Balance</span>
            <span class="kpi-dots">•••</span>
        </div>
        <div class="kpi-value">{{ number_format($metrics['cash_drawer_balance'] ?? 435000) }} <span class="kpi-currency">MMK</span></div>
        <div class="kpi-growth">
            <span>Session #12 Active</span>
            <span class="growth-pill pill-amber"><i class="ti ti-shield-check"></i> Reconciled</span>
        </div>
    </div>
</div>

<!-- 3. POS Insight Cards: Revenue Velocity & Peak Dining Traffic -->
<div class="financial-main-grid">
    <!-- Left Card: Revenue Velocity (This Week) -->
    <div class="pos-insight-card">
        <div class="pos-card-header">
            <div class="pos-card-title-group">
                <div class="pos-card-icon-wrap" style="background: rgba(158, 198, 59, 0.12); color: var(--primary);">
                    <i class="ti ti-chart-area-line"></i>
                </div>
                <div>
                    <div class="pos-card-title">Weekly Revenue Velocity</div>
                    <div class="pos-card-subtitle">Dine-in vs takeaway daily trend</div>
                </div>
            </div>
            <span class="pos-card-badge badge-success">
                <i class="ti ti-trending-up" style="font-size: 0.7rem;"></i> +14.8% vs last week
            </span>
        </div>

        <div class="revenue-metrics-strip">
            <div class="rev-metric-item">
                <span class="rev-metric-label">7-Day Total</span>
                <span class="rev-metric-val">{{ number_format(array_sum(array_column($metrics['sales_by_date'] ?? [], 'amount')) ?: 9460000) }} <span style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted);">MMK</span></span>
            </div>
            <div class="rev-metric-item">
                <span class="rev-metric-label">Daily Avg</span>
                <span class="rev-metric-val">{{ number_format((array_sum(array_column($metrics['sales_by_date'] ?? [], 'amount')) ?: 9460000) / 7) }} <span style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted);">MMK</span></span>
            </div>
            <div class="rev-metric-item">
                <span class="rev-metric-label">Dine-in Share</span>
                <span class="rev-metric-val" style="color: var(--primary);">70%</span>
            </div>
        </div>

        <div class="pos-chart-container" id="chart-pos-revenue-trend"></div>
    </div>

    <!-- Right Card: Hourly Dining Traffic & Kitchen Rush -->
    <div class="pos-insight-card">
        <div class="pos-card-header">
            <div class="pos-card-title-group">
                <div class="pos-card-icon-wrap" style="background: rgba(92, 134, 35, 0.12); color: var(--info);">
                    <i class="ti ti-clock-hour-4"></i>
                </div>
                <div>
                    <div class="pos-card-title">Hourly Dining Traffic</div>
                    <div class="pos-card-subtitle">Order volume & kitchen rush load</div>
                </div>
            </div>
            <span class="pos-card-badge badge-info">
                <i class="ti ti-flame" style="font-size: 0.7rem;"></i> Peak: 12 PM & 7 PM
            </span>
        </div>

        <div class="revenue-metrics-strip">
            <div class="rev-metric-item">
                <span class="rev-metric-label">Lunch Peak</span>
                <span class="rev-metric-val" style="color: var(--primary);">42 <span style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted);">Orders @ 12 PM</span></span>
            </div>
            <div class="rev-metric-item">
                <span class="rev-metric-label">Dinner Peak</span>
                <span class="rev-metric-val" style="color: var(--info);">38 <span style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted);">Orders @ 7 PM</span></span>
            </div>
            <div class="rev-metric-item">
                <span class="rev-metric-label">Avg Prep Time</span>
                <span class="rev-metric-val">~14 <span style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted);">Mins</span></span>
            </div>
        </div>

        <div class="pos-chart-container" id="chart-pos-peak-hours"></div>
    </div>
</div>

<!-- 2. Hero Welcome Card (PreAdmin Rental Style) -->
<div class="card welcome-card">
    <div class="card-body">
        <div class="welcome-grid">
            <div>
                
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
                    <span style="font-size: 0.75rem; background: var(--primary-light); color: var(--dark-forest); padding: 0.2rem 0.6rem; border-radius: 9999px; font-weight: 700;">Live Week</span>
                </div>
            </div>

            <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
                <div style="padding: 0.65rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                    <span style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">7-Day Total</span>
                    <h5 style="font-weight: 800; font-size: 1.15rem; color: var(--text-main); margin-top: 0.15rem;">9,460,000 MMK</h5>
                </div>
                <div style="padding: 0.65rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                    <span style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Daily Average</span>
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
                    <i class="ti ti-wallet" style="color: var(--dark-forest);"></i>
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

            <div class="table-responsive-wrapper">
                <table class="preadmin-table">
                    <thead>
                        <tr>
                            <th style="min-width: 150px;">Dish Name</th>
                            <th style="min-width: 100px;">Category</th>
                            <th style="min-width: 80px; text-align: center;">Sold Qty</th>
                            <th style="min-width: 110px; text-align: right;">Revenue</th>
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
    </div>

    <!-- Real-Time Staff Activity -->
    <div class="card">
        <div class="card-body">
            <div class="card-header-clean">
                <h5>
                    <i class="ti ti-users" style="color: var(--info);"></i>
                    <span>Real-Time Staff Activity</span>
                </h5>
                <a href="{{ route('admin.employees.index') }}" class="card-link">Employees →</a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($metrics['staff_activity'] ?? [] as $staff)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-hover);">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <img src="{{ $staff['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($staff['name'] ?? 'Staff') }}" alt="" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
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
<div class="grid-2col-equal">
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

            <div class="table-responsive-wrapper">
                <table class="preadmin-table">
                    <thead>
                        <tr>
                            <th style="min-width: 110px;">Order & Table</th>
                            <th style="min-width: 130px;">Customer</th>
                            <th style="min-width: 110px;">Amount</th>
                            <th style="min-width: 95px;">Status</th>
                            <th style="min-width: 75px;">Time</th>
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
                                <td style="white-space: nowrap;">
                                    <div style="font-weight: 700; color: var(--text-main);">{{ $order['order_code'] }}</div>
                                    <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted);">{{ $order['table'] }}</div>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <img src="{{ $order['customer_avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($order['customer_name'] ?? 'Customer') }}" alt="">
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-main); white-space: nowrap;">{{ $order['customer_name'] }}</div>
                                            <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $order['dining_type'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div style="font-weight: 700; color: var(--text-main);">{{ number_format($order['amount']) }} MMK</div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $order['payment_method'] }}</div>
                                </td>
                                <td style="white-space: nowrap;">
                                    <span class="status-badge {{ $statusBadge }}">
                                        {{ $order['status_label'] }}
                                    </span>
                                </td>
                                <td style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); white-space: nowrap;">
                                    {{ $order['time'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No orders recorded today.</td>
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

            <div class="table-responsive-wrapper">
                <table class="preadmin-table">
                    <thead>
                        <tr>
                            <th style="min-width: 95px;">Order</th>
                            <th style="min-width: 70px;">Table</th>
                            <th style="min-width: 105px;">Amount</th>
                            <th style="min-width: 150px;">Reason</th>
                            <th style="min-width: 85px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($metrics['cancelled_refunded_orders'] ?? [] as $cancelled)
                            <tr>
                                <td style="font-weight: 700; color: var(--text-main); white-space: nowrap;">{{ $cancelled['order_code'] }}</td>
                                <td style="font-size: 0.8rem; font-weight: 600; color: var(--text-main); white-space: nowrap;">{{ $cancelled['table'] }}</td>
                                <td style="font-weight: 800; color: #b91c1c; white-space: nowrap;">{{ number_format($cancelled['amount']) }} MMK</td>
                                <td style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.35;">{{ $cancelled['reason'] }}</td>
                                <td style="white-space: nowrap;">
                                    <span class="status-badge status-critical">
                                        {{ $cancelled['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No cancelled tickets today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
        const primaryColor = '#9ec63b';
        const darkForestColor = '#0c2617';
        const oliveColor = '#5c8623';
        const successColor = '#7ea826';

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
            colors: [oliveColor],
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
            colors: ['#d44c45'],
            tooltip: { enabled: false }
        }).render();

        // 5. Weekly Revenue Velocity (Spline Area Chart: Dine-in vs Takeaway)
        if (document.querySelector("#chart-pos-revenue-trend")) {
            const rawDays = JSON.parse('{!! json_encode(array_column($metrics["sales_by_date"] ?? [], "day")) !!}');
            const rawAmounts = JSON.parse('{!! json_encode(array_column($metrics["sales_by_date"] ?? [], "amount")) !!}');

            const days = rawDays.length > 0 ? rawDays : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            const amounts = rawAmounts.length > 0 ? rawAmounts : [980000, 1150000, 1280000, 1050000, 1620000, 1890000, 1450000];

            const dineIn = amounts.map(v => Math.round(v * 0.7));
            const takeaway = amounts.map(v => Math.round(v * 0.3));

            new ApexCharts(document.querySelector("#chart-pos-revenue-trend"), {
                chart: {
                    type: 'area',
                    height: 240,
                    toolbar: { show: false },
                    fontFamily: 'Mada, sans-serif',
                    zoom: { enabled: false }
                },
                series: [
                    { name: 'Dine-In Orders', data: dineIn },
                    { name: 'Takeaway / Delivery', data: takeaway }
                ],
                colors: [primaryColor, '#5c8623'],
                stroke: {
                    curve: 'smooth',
                    width: [2.5, 2]
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                markers: {
                    size: 3.5,
                    strokeColors: isDark ? '#161e2e' : '#ffffff',
                    strokeWidth: 2,
                    hover: { size: 6 }
                },
                dataLabels: { enabled: false },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '11px',
                    fontWeight: 600,
                    labels: {
                        colors: isDark ? '#94a3b8' : '#5e6d60'
                    },
                    markers: {
                        width: 8,
                        height: 8,
                        radius: 12
                    }
                },
                xaxis: {
                    categories: days,
                    labels: {
                        style: {
                            colors: isDark ? '#94a3b8' : '#5e6d60',
                            fontSize: '11px',
                            fontWeight: 600,
                            fontFamily: 'Mada, sans-serif'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    tickAmount: 4,
                    labels: {
                        formatter: function (val) {
                            return Math.round(val / 1000) + 'k';
                        },
                        style: {
                            colors: isDark ? '#94a3b8' : '#5e6d60',
                            fontSize: '11px',
                            fontFamily: 'Mada, sans-serif'
                        }
                    }
                },
                grid: {
                    borderColor: isDark ? '#222d42' : '#f1f5f9',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    theme: isDark ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return Number(val).toLocaleString() + ' MMK';
                        }
                    }
                }
            }).render();
        }

        // 6. Hourly Dining Traffic & Kitchen Rush (Modern Column Chart)
        if (document.querySelector("#chart-pos-peak-hours")) {
            const peakHours = ['11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM', '10 PM'];
            const hourlyData = [18, 42, 28, 16, 12, 15, 24, 38, 34, 26, 18, 8];

            new ApexCharts(document.querySelector("#chart-pos-peak-hours"), {
                chart: {
                    type: 'bar',
                    height: 240,
                    toolbar: { show: false },
                    fontFamily: 'Mada, sans-serif',
                    zoom: { enabled: false }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        columnWidth: '46%',
                        distributed: true
                    }
                },
                colors: hourlyData.map(val => {
                    if (val >= 40) return primaryColor;
                    if (val >= 30) return '#7ea826';
                    if (val >= 20) return '#5c8623';
                    return isDark ? '#1f293d' : '#d5dfcd';
                }),
                series: [{
                    name: 'Orders',
                    data: hourlyData
                }],
                dataLabels: { enabled: false },
                legend: { show: false },
                xaxis: {
                    categories: peakHours,
                    labels: {
                        style: {
                            colors: isDark ? '#94a3b8' : '#5e6d60',
                            fontSize: '11px',
                            fontWeight: 600,
                            fontFamily: 'Mada, sans-serif'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    tickAmount: 4,
                    labels: {
                        formatter: function (val) {
                            return Math.round(val);
                        },
                        style: {
                            colors: isDark ? '#94a3b8' : '#5e6d60',
                            fontSize: '11px',
                            fontFamily: 'Mada, sans-serif'
                        }
                    }
                },
                grid: {
                    borderColor: isDark ? '#222d42' : '#f1f5f9',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    custom: function({ series, seriesIndex, dataPointIndex, w }) {
                        const hr = peakHours[dataPointIndex];
                        const count = series[0][dataPointIndex];
                        let rushBadge = 'Normal Traffic';
                        let badgeColor = '#5c8623';
                        if (count >= 40) { rushBadge = '🔥 Peak Dining Rush'; badgeColor = '#9ec63b'; }
                        else if (count >= 30) { rushBadge = '⚡ High Kitchen Load'; badgeColor = '#7ea826'; }
                        else if (count >= 20) { rushBadge = '🍽️ Moderate Flow'; badgeColor = '#5c8623'; }

                        const bg = isDark ? '#161e2e' : '#ffffff';
                        const border = isDark ? '#222d42' : '#e2e8f0';
                        const textMain = isDark ? '#f1f5f9' : '#0c2617';
                        const textMuted = isDark ? '#94a3b8' : '#64748b';

                        return '<div style="background: ' + bg + '; border: 1px solid ' + border + '; border-radius: 10px; padding: 10px 14px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); font-family: Mada, sans-serif; font-size: 13px;">' +
                            '<div style="font-weight: 700; color: ' + textMain + '; margin-bottom: 4px;">' + hr + '</div>' +
                            '<div style="color: ' + textMuted + '; margin-bottom: 6px;">Orders: <strong style="color: ' + textMain + '; font-size: 13px;">' + count + ' tickets</strong></div>' +
                            '<div style="display: inline-block; font-size: 11px; font-weight: 700; color: ' + badgeColor + '; background: rgba(158, 198, 59, 0.1); padding: 2px 8px; border-radius: 9999px;">' + rushBadge + '</div>' +
                        '</div>';
                    }
                }
            }).render();
        }

        // 7. Weekly Revenue Stream Column Chart (Sales by Date)
        if (document.querySelector("#chart-revenue-weekly")) {
            const salesDates = JSON.parse('{!! json_encode(array_column($metrics["sales_by_date"] ?? [], "day")) !!}');
            const salesAmounts = JSON.parse('{!! json_encode(array_column($metrics["sales_by_date"] ?? [], "amount")) !!}');

            new ApexCharts(document.querySelector("#chart-revenue-weekly"), {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'Mada, sans-serif'
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
                    '#5e6d60', '#5e6d60', '#5e6d60', '#5e6d60', '#5e6d60', '#5e6d60', primaryColor
                ],
                series: [{
                    name: 'Sales (MMK)',
                    data: salesAmounts.length > 0 ? salesAmounts : [980000, 1150000, 1280000, 1050000, 1620000, 1890000, 1450000]
                }],
                xaxis: {
                    categories: salesDates.length > 0 ? salesDates : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    labels: {
                        style: { colors: isDark ? '#94a3b8' : '#5e6d60', fontSize: '12px', fontWeight: 600, fontFamily: 'Mada, sans-serif' }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return (val / 1000) + 'k';
                        },
                        style: { colors: isDark ? '#94a3b8' : '#5e6d60', fontSize: '11px', fontFamily: 'Mada, sans-serif' }
                    }
                },
                grid: {
                    borderColor: isDark ? '#222d42' : '#e1e8db',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString() + ' MMK';
                        }
                    }
                }
            }).render();
        }

        // 8. Payment Donut Chart
        if (document.querySelector("#chart-payment-donut")) {
            new ApexCharts(document.querySelector("#chart-payment-donut"), {
                chart: {
                    type: 'donut',
                    height: 190,
                    fontFamily: 'Mada, sans-serif'
                },
                series: [43, 30, 17, 10],
                labels: ['KBZPay QR', 'Cash', 'WavePay', 'Visa/MPU'],
                colors: ['#9ec63b', '#0c2617', '#5c8623', '#7ea826'],
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
        }

        // 9. Interactive Calendar Dropdown Logic
        (function() {
            const trigger = document.getElementById('calendarPickerTrigger');
            const dropdown = document.getElementById('calendarDropdownMenu');
            if (!trigger || !dropdown) return;

            const initialDateStr = "{{ $selectedDate ?? now()->format('Y-m-d') }}";
            let [curYear, curMonth, curDay] = initialDateStr.split('-').map(Number);
            let viewYear = curYear;
            let viewMonth = curMonth - 1;

            const monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            function renderCalendar() {
                const titleEl = document.getElementById('calMonthYearTitle');
                const gridEl = document.getElementById('calDaysGrid');
                if (!titleEl || !gridEl) return;

                titleEl.textContent = `${monthNames[viewMonth]} ${viewYear}`;
                gridEl.innerHTML = '';

                const firstDayIndex = new Date(viewYear, viewMonth, 1).getDay();
                const totalDays = new Date(viewYear, viewMonth + 1, 0).getDate();
                const prevMonthTotalDays = new Date(viewYear, viewMonth, 0).getDate();

                // Previous month padding
                for (let i = firstDayIndex - 1; i >= 0; i--) {
                    const dayNum = prevMonthTotalDays - i;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'cal-day-cell cal-day-muted';
                    btn.textContent = dayNum;
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        let prevM = viewMonth;
                        let prevY = viewYear;
                        if (prevM === 0) { prevM = 12; prevY--; }
                        selectDate(prevY, prevM, dayNum);
                    });
                    gridEl.appendChild(btn);
                }

                const today = new Date();
                const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

                // Current month days
                for (let d = 1; d <= totalDays; d++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'cal-day-cell';
                    btn.textContent = d;

                    const dateStr = `${viewYear}-${String(viewMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    if (dateStr === initialDateStr) {
                        btn.classList.add('cal-day-selected');
                    }
                    if (dateStr === todayStr) {
                        btn.classList.add('cal-day-today');
                    }

                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        selectDate(viewYear, viewMonth + 1, d);
                    });
                    gridEl.appendChild(btn);
                }

                // Next month padding
                const renderedCount = firstDayIndex + totalDays;
                const remainder = renderedCount % 7 === 0 ? 0 : 7 - (renderedCount % 7);
                for (let n = 1; n <= remainder; n++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'cal-day-cell cal-day-muted';
                    btn.textContent = n;
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        let nextM = viewMonth + 2;
                        let nextY = viewYear;
                        if (nextM > 12) { nextM = 1; nextY++; }
                        selectDate(nextY, nextM, n);
                    });
                    gridEl.appendChild(btn);
                }
            }

            function selectDate(year, month, day) {
                const y = year;
                const m = String(month).padStart(2, '0');
                const d = String(day).padStart(2, '0');
                window.location.href = `{{ route('admin.dashboard') }}?date=${y}-${m}-${d}`;
            }

            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = dropdown.style.display === 'block';
                dropdown.style.display = isOpen ? 'none' : 'block';
                if (!isOpen) {
                    renderCalendar();
                }
            });

            document.getElementById('calPrevMonth')?.addEventListener('click', function(e) {
                e.stopPropagation();
                viewMonth--;
                if (viewMonth < 0) {
                    viewMonth = 11;
                    viewYear--;
                }
                renderCalendar();
            });

            document.getElementById('calNextMonth')?.addEventListener('click', function(e) {
                e.stopPropagation();
                viewMonth++;
                if (viewMonth > 11) {
                    viewMonth = 0;
                    viewYear++;
                }
                renderCalendar();
            });

            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const preset = this.getAttribute('data-preset');
                    const now = new Date();
                    if (preset === 'today') {
                        const y = now.getFullYear();
                        const m = String(now.getMonth() + 1).padStart(2, '0');
                        const d = String(now.getDate()).padStart(2, '0');
                        window.location.href = `{{ route('admin.dashboard') }}?date=${y}-${m}-${d}`;
                    } else if (preset === 'yesterday') {
                        const yest = new Date(now);
                        yest.setDate(yest.getDate() - 1);
                        const y = yest.getFullYear();
                        const m = String(yest.getMonth() + 1).padStart(2, '0');
                        const d = String(yest.getDate()).padStart(2, '0');
                        window.location.href = `{{ route('admin.dashboard') }}?date=${y}-${m}-${d}`;
                    } else if (preset === 'this_month') {
                        const y = now.getFullYear();
                        const m = String(now.getMonth() + 1).padStart(2, '0');
                        window.location.href = `{{ route('admin.dashboard') }}?date=${y}-${m}-01`;
                    }
                });
            });

            document.getElementById('calApplyBtn')?.addEventListener('click', function(e) {
                e.stopPropagation();
                const inputVal = document.getElementById('calDirectInput')?.value;
                if (inputVal) {
                    window.location.href = `{{ route('admin.dashboard') }}?date=${inputVal}`;
                }
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && !trigger.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    dropdown.style.display = 'none';
                }
            });
        })();
    });
</script>
@endpush
