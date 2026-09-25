@extends('admin.layouts.app')

@section('title', 'Expense Management')

@push('styles')
@include('admin.expenses._styles')
<style>
    /* ==========================================================================
       MODERN EXPENSE MANAGEMENT UI SYSTEM (MADA TYPOGRAPHY & OBSIDIAN SLATE THEME)
       ========================================================================== */
    .expense-portal {
        font-family: "Mada", sans-serif;
        color: var(--text-main);
    }

    /* Page Header */
    .page-header-wrap {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 2rem !important;
        flex-wrap: wrap !important;
        gap: 1.25rem !important;
    }

    .filter-action-footer {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 0.75rem !important;
        margin-top: 1.25rem !important;
        padding-top: 1rem !important;
        border-top: 1px solid var(--border-color) !important;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--border-color) transparent;
        -webkit-overflow-scrolling: touch;
    }
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: transparent;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: var(--text-muted);
    }

    .header-title-group h1 {
        font-size: 1.625rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin: 0;
    }

    .header-title-group p {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 400;
        margin: 0.35rem 0 0 0;
    }

    .btn-gradient-primary {
        background: linear-gradient(135deg, #9ec63b 0%, #7ea826 100%);
        color: #ffffff !important;
        padding: 0.65rem 1.35rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(158, 198, 59, 0.32);
    }

    .btn-gradient-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(158, 198, 59, 0.42);
        filter: brightness(1.04);
    }

    .btn-modern-secondary {
        background: var(--bg-card);
        color: var(--text-main) !important;
        border: 1px solid var(--border-color);
        padding: 0.6rem 1.15rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    .btn-modern-secondary:hover {
        background: var(--bg-hover);
        border-color: var(--primary);
        color: var(--primary-hover) !important;
        transform: translateY(-1px);
    }

    /* 4 High-End KPI Cards */
    .kpi-grid-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .stat-glass-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.35rem 1.4rem;
        position: relative;
        overflow: hidden;
        transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .stat-glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
        border-color: var(--primary);
    }

    .stat-glass-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: transparent;
        transition: all 0.2s ease;
    }

    .stat-glass-card.primary::before { background: linear-gradient(90deg, #9ec63b, #7ea826); }
    .stat-glass-card.success::before { background: linear-gradient(90deg, #10b981, #059669); }
    .stat-glass-card.warning::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .stat-glass-card.danger::before { background: linear-gradient(90deg, #ef4444, #dc2626); }

    .stat-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .stat-meta-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
    }

    .stat-icon-sphere {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: transform 0.25s ease;
    }

    .stat-glass-card:hover .stat-icon-sphere {
        transform: scale(1.08);
    }

    .stat-icon-sphere.primary { background: rgba(158, 198, 59, 0.15); color: #7ea826; }
    .stat-icon-sphere.success { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .stat-icon-sphere.warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .stat-icon-sphere.danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    .stat-big-metric {
        font-size: 1.625rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--text-main);
        line-height: 1.2;
        margin-bottom: 0.4rem;
    }

    .stat-bottom-text {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8125rem;
        color: var(--text-muted);
        padding-top: 0.65rem;
        border-top: 1px solid var(--border-subtle);
    }

    /* Filter Card */
    .filter-panel-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.35rem 1.5rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    .filter-fields-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .field-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
        display: block;
    }

    .modern-input-wrap {
        position: relative;
    }

    .modern-input-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }

    .modern-form-control {
        width: 100%;
        background: var(--bg-body);
        border: 1.5px solid var(--border-color);
        color: var(--text-main);
        padding: 0.55rem 0.85rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-family: "Mada", sans-serif;
        outline: none;
        transition: all 0.2s ease;
    }

    .modern-input-wrap .modern-form-control {
        padding-left: 36px;
    }

    .modern-form-control:focus {
        border-color: var(--primary);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.2);
    }

    /* Main Table Container */
    .modern-table-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .table-modern {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .table-modern thead th {
        background: var(--bg-hover);
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.8rem 0.85rem;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .table-modern tbody td {
        padding: 0.8rem 0.85rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        font-size: 0.875rem;
        color: var(--text-main);
        transition: background 0.15s ease;
    }

    .table-modern tbody tr:hover td {
        background: var(--bg-hover);
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    /* Actions Column - Sticky Right with Ample Breathing Space */
    .table-modern th.actions-col,
    .table-modern td.actions-col {
        position: sticky;
        right: 0;
        z-index: 2;
        padding-right: 1.25rem !important;
        padding-left: 0.75rem !important;
        min-width: 145px !important;
        width: 145px !important;
        text-align: center !important;
    }

    .table-modern thead th.actions-col {
        background: var(--bg-hover) !important;
        box-shadow: -4px 0 8px rgba(0, 0, 0, 0.04);
    }

    .table-modern tbody td.actions-col {
        background: var(--bg-card) !important;
        box-shadow: -4px 0 8px rgba(0, 0, 0, 0.04);
    }

    .table-modern tbody tr:hover td.actions-col {
        background: var(--bg-hover) !important;
    }

    /* Row Overdue Highlight */
    .table-modern tr.row-overdue-alert td {
        background: rgba(239, 68, 68, 0.035);
    }

    /* Voucher reference pill */
    .voucher-chip {
        font-family: monospace;
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--primary-hover);
        background: rgba(158, 198, 59, 0.12);
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.15s ease;
    }

    .voucher-chip:hover {
        background: var(--primary);
        color: #ffffff;
    }

    /* Category Pill */
    .cat-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
    }

    .cat-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Dynamic Status Badges with Pulse Dots */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-paid {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }
    .status-paid .status-dot { background: #10b981; }

    .status-approved {
        background: rgba(59, 130, 246, 0.12);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }
    .status-approved .status-dot { background: #3b82f6; }

    .status-pending {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }
    .status-pending .status-dot { background: #f59e0b; }

    .status-draft {
        background: rgba(148, 163, 184, 0.12);
        color: #475569;
        border: 1px solid rgba(148, 163, 184, 0.25);
    }
    .status-draft .status-dot { background: #94a3b8; }

    .status-rejected {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }
    .status-rejected .status-dot { background: #ef4444; }

    .status-void {
        background: rgba(100, 116, 139, 0.15);
        color: #64748b;
        border: 1px solid rgba(100, 116, 139, 0.3);
    }
    .status-void .status-dot { background: #64748b; }

    /* Amount styling */
    .metric-amount {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .overdue-pill {
        font-size: 0.65rem;
        font-weight: 800;
        background: #ef4444;
        color: #ffffff;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        letter-spacing: 0.05em;
        display: inline-block;
        margin-top: 0.15rem;
    }

    /* Action Buttons */
    .action-circle-btn {
        width: 32px;
        height: 32px;
        min-width: 32px;
        max-width: 32px;
        flex-shrink: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        padding: 0;
    }

    .action-circle-btn:hover {
        background: var(--bg-hover);
        color: var(--text-main);
        border-color: var(--text-muted);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="expense-portal">
    <!-- Header Banner -->
    <div class="page-header-wrap">
        <div class="header-title-group">
            <h1>
                <i class="ti ti-receipt-tax text-primary"></i> Expense Management
            </h1>
            
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('admin.expenses.create') }}" class="btn-gradient-primary">
                <i class="ti ti-plus"></i>
                <span>Record New Expense</span>
            </a>
            <a href="{{ route('admin.expenses.reports') }}" class="btn-modern-secondary">
                <i class="ti ti-chart-pie"></i>
                <span>Reports & Audit</span>
            </a>
            <a href="{{ route('admin.expenses.export', request()->query()) }}" class="btn-modern-secondary">
                <i class="ti ti-file-spreadsheet"></i>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <!-- Modern Shared Sub-Navigation Bar -->
    @include('admin.expenses._nav')

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ti ti-circle-check"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
            <i class="ti ti-alert-triangle"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    <!-- 4 High-End Glassmorphic KPI Cards -->
    <div class="kpi-grid-row">
        <!-- Card 1 -->
        <div class="stat-glass-card primary">
            <div class="stat-top-bar">
                <span class="stat-meta-label">Total Outflow (MMK)</span>
                <div class="stat-icon-sphere primary">
                    <i class="ti ti-cash"></i>
                </div>
            </div>
            <div class="stat-big-metric">{{ $summary['formatted_total_amount'] }}</div>
            <div class="stat-bottom-text">
                <span>{{ $summary['total_count'] }} transactions</span>
                <span>Avg: {{ $summary['formatted_daily_average'] }}/day</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="stat-glass-card success">
            <div class="stat-top-bar">
                <span class="stat-meta-label">Settled / Paid (MMK)</span>
                <div class="stat-icon-sphere success">
                    <i class="ti ti-circle-check"></i>
                </div>
            </div>
            <div class="stat-big-metric text-success">{{ $summary['formatted_paid_amount'] }}</div>
            <div class="stat-bottom-text">
                <span class="text-success font-semibold">{{ $summary['paid_count'] }} paid vouchers</span>
                <span>Cash & Bank cleared</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="stat-glass-card warning">
            <div class="stat-top-bar">
                <span class="stat-meta-label">Pending Approval (MMK)</span>
                <div class="stat-icon-sphere warning">
                    <i class="ti ti-clock-hour-4"></i>
                </div>
            </div>
            <div class="stat-big-metric text-warning">{{ $summary['formatted_pending_approval_amount'] }}</div>
            <div class="stat-bottom-text">
                <span class="text-warning font-semibold">{{ $summary['pending_count'] }} waiting review</span>
                <span>{{ $summary['draft_count'] }} drafts</span>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="stat-glass-card danger">
            <div class="stat-top-bar">
                <span class="stat-meta-label">Overdue Bills (MMK)</span>
                <div class="stat-icon-sphere danger">
                    <i class="ti ti-alert-triangle"></i>
                </div>
            </div>
            <div class="stat-big-metric text-danger">{{ $summary['formatted_overdue_amount'] }}</div>
            <div class="stat-bottom-text">
                <span class="text-danger font-semibold">{{ $summary['overdue_count'] }} invoices past due</span>
                <a href="{{ route('admin.expenses.index', ['due_status' => 'overdue']) }}" class="text-danger font-bold text-decoration-none">Filter bills &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Modern Filter Panel -->
    <div class="filter-panel-card">
        <form method="GET" action="{{ route('admin.expenses.index') }}">
            <div class="filter-fields-grid">
                <div>
                    <label class="field-label">Search Query</label>
                    <div class="modern-input-wrap">
                        <i class="ti ti-search"></i>
                        <input type="text" name="search" class="modern-form-control" placeholder="Voucher #, item, vendor..." value="{{ request('search') }}">
                    </div>
                </div>

                <div>
                    <label class="field-label">Workflow Status</label>
                    <select name="status" class="modern-form-control">
                        <option value="">All Statuses</option>
                        <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                        <option value="PENDING_APPROVAL" {{ request('status') === 'PENDING_APPROVAL' ? 'selected' : '' }}>Pending Review</option>
                        <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Approved</option>
                        <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>Paid & Disbursed</option>
                        <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                        <option value="VOID" {{ request('status') === 'VOID' ? 'selected' : '' }}>Void</option>
                    </select>
                </div>

                <div>
                    <label class="field-label">Category</label>
                    <select name="category_id" class="modern-form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label">Vendor</label>
                    <select name="vendor_id" class="modern-form-control">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label">From Date</label>
                    <input type="date" name="from" class="modern-form-control" value="{{ request('from') }}">
                </div>

                <div>
                    <label class="field-label">To Date</label>
                    <input type="date" name="to" class="modern-form-control" value="{{ request('to') }}">
                </div>
            </div>

            <div class="filter-action-footer">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.expenses.index') }}" class="btn-modern-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8125rem;">
                        <i class="ti ti-refresh"></i> Reset Filters
                    </a>
                    @if(request('due_status') === 'overdue')
                        <span class="status-pill status-rejected">Filtering: Overdue Only</span>
                    @endif
                </div>
                <button type="submit" class="btn-gradient-primary" style="padding: 0.45rem 1.15rem; font-size: 0.8125rem;">
                    <i class="ti ti-filter"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="modern-table-card">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Voucher #</th>
                        <th>Date & Due</th>
                        <th>Expense Title</th>
                        <th>Category</th>
                        <th>Supplier / Payee</th>
                        <th class="text-right">Amount (MMK)</th>
                        <th>Status</th>
                        <th>Payment Method</th>
                        <th>Recorded By</th>
                        <th class="text-center actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr class="{{ $expense->isOverdue() ? 'row-overdue-alert' : '' }}">
                            <td>
                                <a href="{{ route('admin.expenses.show', $expense) }}" class="voucher-chip">
                                    {{ $expense->expense_number ?? ('#EXP-'.$expense->id) }}
                                </a>
                            </td>
                            <td>
                                <div class="font-semibold">{{ $expense->expense_date?->format('d M Y') }}</div>
                                @if($expense->due_date)
                                    <div style="font-size: 0.75rem;" class="{{ $expense->isOverdue() ? 'text-danger font-bold' : 'text-muted' }}">
                                        Due: {{ $expense->due_date?->format('d M Y') }}
                                        @if($expense->isOverdue())
                                            <span class="overdue-pill">OVERDUE</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-main">{{ $expense->title }}</div>
                                @if($expense->receipt_path)
                                    <span class="badge" style="background: rgba(56, 189, 248, 0.12); color: #0284c7; font-size: 0.6875rem;">
                                        <i class="ti ti-paperclip"></i> Receipt Attached
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="cat-chip">
                                    <span class="cat-dot" style="background: {{ $expense->categoryRelation?->color ?? '#9ec63b' }};"></span>
                                    {{ $expense->category_display_name }}
                                </span>
                            </td>
                            <td>
                                <span class="font-medium">{{ $expense->vendor?->name ?? 'Direct Local Market' }}</span>
                            </td>
                            <td class="text-right">
                                <span class="metric-amount">{{ number_format($expense->total_amount ?: $expense->amount, 0) }} MMK</span>
                            </td>
                            <td>
                                @php
                                    $statusConfig = match($expense->status) {
                                        'PAID' => ['class' => 'status-paid', 'label' => 'Paid'],
                                        'APPROVED' => ['class' => 'status-approved', 'label' => 'Approved'],
                                        'PENDING_APPROVAL' => ['class' => 'status-pending', 'label' => 'Pending Review'],
                                        'REJECTED' => ['class' => 'status-rejected', 'label' => 'Rejected'],
                                        'VOID' => ['class' => 'status-void', 'label' => 'Void'],
                                        default => ['class' => 'status-draft', 'label' => 'Draft'],
                                    };
                                @endphp
                                <span class="status-pill {{ $statusConfig['class'] }}">
                                    <span class="status-dot"></span>
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($expense->isPaid())
                                    <span class="badge" style="background: var(--bg-hover); color: var(--text-main); font-weight: 600; font-size: 0.75rem;">
                                        <i class="ti {{ $expense->payment_method === 'CASH' ? 'ti-cash' : 'ti-building-bank' }} text-muted"></i>
                                        {{ $expense->payment_method ?? 'PAID' }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8125rem;">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 0.8125rem;">{{ $expense->creator?->name ?? 'System' }}</span>
                            </td>
                            <td class="text-center actions-col">
                                <div style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; flex-wrap: nowrap;">
                                    <a href="{{ route('admin.expenses.show', $expense) }}" class="action-circle-btn" title="View Detail">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    @if($expense->isDraft() || $expense->isRejected())
                                        <form method="POST" action="{{ route('admin.expenses.submit', $expense) }}" style="display: inline-flex; margin: 0; padding: 0;">
                                            @csrf
                                            <button type="submit" class="action-circle-btn text-warning" title="Submit for Review" onclick="return confirm('Submit expense for approval?');">
                                                <i class="ti ti-send"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.expenses.edit', $expense) }}" class="action-circle-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                    @endif

                                    @if($expense->isPendingApproval())
                                        <form method="POST" action="{{ route('admin.expenses.approve', $expense) }}" style="display: inline-flex; margin: 0; padding: 0;">
                                            @csrf
                                            <button type="submit" class="action-circle-btn text-success" title="Approve" onclick="return confirm('Approve expense {{ $expense->expense_number }}?');">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($expense->isApproved() && ! $expense->isPaid())
                                        <a href="{{ route('admin.expenses.show', $expense) }}#payment-box" class="action-circle-btn text-primary font-bold" title="Disburse Payment">
                                            <i class="ti ti-cash"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="empty-state p-4 text-center">
                                    <i class="ti ti-receipt-off" style="font-size: 2.75rem; color: var(--text-muted); opacity: 0.6;"></i>
                                    <h4 class="mt-2 text-main font-bold">No Expenses Found</h4>
                                    <p class="text-muted" style="font-size: 0.875rem;">No expenditures match your selected search or date criteria.</p>
                                    <a href="{{ route('admin.expenses.create') }}" class="btn-gradient-primary btn-sm mt-2">
                                        <i class="ti ti-plus"></i> Record First Expense
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-meta">
                    Showing <strong>{{ $expenses->firstItem() }}</strong> to <strong>{{ $expenses->lastItem() }}</strong> of <strong>{{ $expenses->total() }}</strong> expenses
                </div>
                <div>
                    {{ $expenses->links('admin.expenses._pagination') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
