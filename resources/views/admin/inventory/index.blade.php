@extends('admin.layouts.app')

@section('title', 'Inventory & Stock Management')

@push('styles')
<style>
    /* ==========================================================================
       ENTERPRISE INVENTORY & STOCK MANAGEMENT UI (MADA TYPOGRAPHY & OBSIDIAN SLATE THEME)
       ========================================================================== */
    .inventory-workspace-wrapper {
        font-family: "Mada", sans-serif;
        color: var(--text-main);
    }

    .inv-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1.75rem;
    }

    .inv-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        color: var(--text-main);
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .inv-title p {
        font-size: 0.875rem;
        font-weight: 400;
        color: var(--text-muted);
        margin: 0;
    }

    .inv-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-create-item {
        background: var(--primary);
        color: #0b0f17 !important;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.65rem 1.25rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(158, 198, 59, 0.25);
        transition: all 0.2s ease;
    }

    .btn-create-item:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-report-link {
        background: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        padding: 0.65rem 1.15rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-report-link:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-help-toggle {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.25);
        padding: 0.65rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }

    .btn-help-toggle:hover {
        background: rgba(59, 130, 246, 0.2);
    }

    /* Admin Usage Help Hub Card */
    .inv-help-hub {
        background: var(--bg-card);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.75rem;
        display: none;
        animation: fadeIn 0.25s ease;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .inv-help-hub.show {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .help-hub-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.75rem;
    }

    .help-hub-header h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #3b82f6;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .help-grid-4 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .help-card {
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 1rem;
    }

    .help-card h4 {
        margin: 0 0 0.35rem 0;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .help-card p {
        margin: 0;
        font-size: 0.78rem;
        color: var(--text-muted);
        line-height: 1.45;
    }

    /* Live KPI Metrics Grid */
    .inv-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .inv-stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .inv-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .icon-total-items { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .icon-alert-items { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .icon-val-items { background: rgba(16, 185, 129, 0.12); color: #10b981; }

    /* Filter & Search Bar */
    .inv-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .inv-filter-pill {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-muted);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .inv-filter-pill:hover {
        color: var(--text-main);
    }

    .inv-filter-pill.active-all {
        background: var(--primary);
        color: #0b0f17;
        font-weight: 700;
        border-color: var(--primary);
    }

    .inv-filter-pill.active-low {
        background: #ef4444;
        color: #ffffff;
        font-weight: 700;
        border-color: #ef4444;
    }

    .search-input-wrap {
        position: relative;
        min-width: 280px;
    }

    .search-input-wrap input {
        width: 100%;
        padding: 0.55rem 1rem 0.55rem 2.3rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-main);
        font-family: "Mada", sans-serif;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .search-input-wrap input:focus {
        border-color: var(--primary);
    }

    .search-input-wrap i {
        position: absolute;
        left: 0.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    /* Table Panel */
    .inv-table-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .inv-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .inv-table th {
        background: var(--bg-body);
        color: var(--text-muted);
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .inv-table td {
        padding: 0.95rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .inv-table tr:hover {
        background: rgba(255, 255, 255, 0.015);
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .stock-badge.ok {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .stock-badge.low {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .stock-qty-val {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .stock-qty-val.is-low {
        color: #ef4444;
    }

    .action-btn-group {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        justify-content: flex-end;
    }

    .btn-action-small {
        padding: 0.4rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--border-color);
        background: var(--bg-body);
        color: var(--text-main);
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s ease;
    }

    .btn-action-small:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-action-restock {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border-color: rgba(16, 185, 129, 0.3);
    }

    .btn-action-restock:hover {
        background: #10b981;
        color: #0b0f17;
    }

    .btn-action-danger {
        color: #ef4444;
    }

    .btn-action-danger:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: #ef4444;
    }

    /* Modals */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        backdrop-filter: blur(4px);
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        width: 100%;
        max-width: 520px;
        padding: 1.75rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        animation: modalScale 0.2s ease;
    }

    @keyframes modalScale {
        from { transform: scale(0.96); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.75rem;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-close-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.25rem;
        cursor: pointer;
    }

    .modal-close-btn:hover {
        color: var(--text-main);
    }

    .form-group {
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .form-group label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .form-group small {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .form-control {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-body);
        color: var(--text-main);
        font-family: "Mada", sans-serif;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--primary);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--border-color);
    }

    .btn-secondary {
        padding: 0.6rem 1.1rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-body);
        color: var(--text-main);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="inventory-workspace-wrapper">

    <!-- Header -->
    <div class="inv-header">
        <div class="inv-title">
            <h1>
                <i class="ti ti-packages" style="color: #3b82f6;"></i>
                Inventory & Stock Management
            </h1>
            <p>
                စားသောက်ဆိုင် မီးဖိုချောင်သုံး ကုန်ကြမ်းပစ္စည်းများ လက်ကျန်၊ အနိမ့်ဆုံး သတိပေးချက်နှင့် ကုန်ပစ္စည်း တန်ဖိုး စီမံခန့်ခွဲခြင်း (<strong>{{ $restaurant->name ?? 'Restaurant' }}</strong>)
            </p>
        </div>
        <div class="inv-header-actions">
            <button type="button" class="btn-help-toggle" onclick="toggleHelpHub()">
                <i class="ti ti-help-circle"></i>
                <span>Help & Admin Guide (အသုံးပြုနည်း လမ်းညွှန်)</span>
            </button>
            <a href="{{ route('admin.inventory.report') }}" class="btn-report-link">
                <i class="ti ti-report-analytics" style="color: #10b981;"></i>
                <span>Remaining Stock Report</span>
            </a>
            <button type="button" class="btn-create-item" onclick="openModal('inventoryModal')">
                <i class="ti ti-plus"></i>
                <span>+ Add Stock Item</span>
            </button>
        </div>
    </div>

    <!-- Collapsible Admin Help Hub -->
    <div class="inv-help-hub" id="invHelpHub">
        <div class="help-hub-header">
            <h3><i class="ti ti-bulb"></i> စားသောက်ဆိုင် ကုန်ကြမ်းသိုလှောင်ရုံ အသုံးပြုနည်း လမ်းညွှန် (Admin Usage Guide)</h3>
            <button type="button" class="modal-close-btn" onclick="toggleHelpHub()"><i class="ti ti-x"></i></button>
        </div>
        <div class="help-grid-4">
            <div class="help-card">
                <h4><i class="ti ti-alert-triangle" style="color: #ef4444;"></i> Safety Alert Level</h4>
                <p>လက်ကျန်ပမာဏ (Current Stock) သည် Safety Alert အောက် ရောက်သွားပါက အနီရောင် Low Stock Alert ပြသပေးပြီး မီးဖိုချောင်တွင် ဟင်းလျာချက်ပြုတ်ရန် ကုန်ကြမ်းမပြတ်စေရန် ကြိုတင်ဝယ်ယူနိုင်ပါသည်။</p>
            </div>
            <div class="help-card">
                <h4><i class="ti ti-scale" style="color: #3b82f6;"></i> Measurement Units</h4>
                <p>မြန်မာစားသောက်ဆိုင်သုံး ယူနစ်များဖြစ်သော kg (ကီလို)၊ viss (ပိဿာ)၊ liter (လီတာ)၊ pcs (ခု/လုံး)၊ can (ဗူး)၊ box (ဖာ) စသည်တို့ဖြင့် အတိအကျ စနစ်တကျ မှတ်တမ်းတင်နိုင်ပါသည်။</p>
            </div>
            <div class="help-card">
                <h4><i class="ti ti-coin" style="color: #10b981;"></i> Stock Valuation (MMK)</h4>
                <p>လက်ကျန်တန်ဖိုး စုစုပေါင်းကို <code>Stock × Unit Cost</code> ဖြင့် တွက်ချက်ထားပြီး စားသောက်ဆိုင်၏ COGS (Cost of Goods Sold) နှင့် အမြတ်အစွန်း တွက်ချက်မှုတွင် တိုက်ရိုက်အသုံးပြုပါသည်။</p>
            </div>
            <div class="help-card">
                <h4><i class="ti ti-truck-delivery" style="color: #ea580c;"></i> Quick Restock</h4>
                <p>နေ့စဉ် နံနက်ခင်း စျေးဝယ်ပစ္စည်းများ ရောက်ရှိလာပါက စာရင်းတစ်ခုချင်းစီရှိ <code>+ Restock</code> ခလုတ်ကို နှိပ်၍ အသစ်ရောက်ရှိလာသော အရေအတွက်ကို လျင်မြန်စွာ ပေါင်းထည့်နိုင်ပါသည်။</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 0.9rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; gap: 0.6rem;">
            <i class="ti ti-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Live Metrics Grid -->
    <div class="inv-stats-grid">
        <div class="inv-stat-card">
            <div class="inv-stat-icon icon-total-items"><i class="ti ti-box"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Stock Items (စုစုပေါင်း)</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ $stats['total_items'] }}</div>
            </div>
        </div>
        <div class="inv-stat-card">
            <div class="inv-stat-icon icon-alert-items"><i class="ti ti-alert-triangle"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Low Stock Alerts (လက်ကျန်နည်း)</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #ef4444;">{{ $stats['low_stock_count'] }}</div>
            </div>
        </div>
        <div class="inv-stat-card">
            <div class="inv-stat-icon icon-val-items"><i class="ti ti-coin"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Total Stock Valuation (စုစုပေါင်းတန်ဖိုး)</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">{{ number_format($stats['total_valuation'], 0) }} MMK</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="inv-toolbar">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.inventory.index') }}" class="inv-filter-pill {{ !$lowStockOnly ? 'active-all' : '' }}">
                All Stock Items ({{ $stats['total_items'] }})
            </a>
            <a href="{{ route('admin.inventory.index', ['low_stock_only' => 1]) }}" class="inv-filter-pill {{ $lowStockOnly ? 'active-low' : '' }}">
                ⚠️ Low Stock Warning ({{ $stats['low_stock_count'] }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.inventory.index') }}" class="search-input-wrap">
            @if($lowStockOnly)
                <input type="hidden" name="low_stock_only" value="1">
            @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Search item, SKU or supplier...">
            <i class="ti ti-search"></i>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="inv-table-panel">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Item Name & SKU</th>
                    <th>Unit</th>
                    <th>Current Stock</th>
                    <th>Safety Alert Level</th>
                    <th>Unit Cost (MMK)</th>
                    <th>Stock Valuation</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $item->name }}</div>
                            @if($item->sku)
                                <div style="font-size: 0.75rem; color: var(--text-muted);">SKU: {{ $item->sku }}</div>
                            @endif
                            @if($item->supplier_name)
                                <div style="font-size: 0.72rem; color: var(--text-muted);">Vendor: {{ $item->supplier_name }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; text-transform: uppercase; font-size: 0.78rem; background: var(--bg-body); padding: 0.25rem 0.5rem; border-radius: 4px; border: 1px solid var(--border-color);">
                                {{ $item->unit }}
                            </span>
                        </td>
                        <td>
                            <span class="stock-qty-val {{ $item->isLowStock() ? 'is-low' : '' }}">
                                {{ number_format($item->current_stock, 2) }}
                            </span>
                            <span style="font-size: 0.78rem; color: var(--text-muted);">{{ $item->unit }}</span>
                        </td>
                        <td>
                            <span style="color: var(--text-muted); font-size: 0.82rem;">≤ {{ number_format($item->min_stock_alert, 2) }} {{ $item->unit }}</span>
                        </td>
                        <td>
                            <span style="font-weight: 600;">{{ number_format($item->unit_cost, 0) }} MMK</span>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: #10b981;">{{ number_format($item->current_stock * $item->unit_cost, 0) }} MMK</span>
                        </td>
                        <td>
                            @if($item->isLowStock())
                                <span class="stock-badge low"><i class="ti ti-alert-triangle"></i> Low Stock</span>
                            @else
                                <span class="stock-badge ok"><i class="ti ti-check"></i> Adequate</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div class="action-btn-group">
                                <!-- Quick Restock Button -->
                                <button type="button" class="btn-action-small btn-action-restock" title="Quick Restock"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-stock="{{ $item->current_stock }}"
                                    data-unit="{{ $item->unit }}"
                                    data-cost="{{ $item->unit_cost }}"
                                    data-supplier="{{ $item->supplier_name }}"
                                    onclick="handleRestockBtn(this)">
                                    <i class="ti ti-plus"></i> Restock
                                </button>
                                <!-- Edit Item Button -->
                                <button type="button" class="btn-action-small" title="Edit Item Details"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-sku="{{ $item->sku }}"
                                    data-unit="{{ $item->unit }}"
                                    data-stock="{{ $item->current_stock }}"
                                    data-alert="{{ $item->min_stock_alert }}"
                                    data-cost="{{ $item->unit_cost }}"
                                    data-supplier="{{ $item->supplier_name }}"
                                    onclick="handleEditBtn(this)">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <!-- Delete Item Button -->
                                <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete inventory stock {{ addslashes($item->name) }}?');" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-small btn-action-danger" title="Delete Item">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 3rem 1.5rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.4;"><i class="ti ti-packages"></i></div>
                            <div style="font-weight: 600; font-size: 1rem;">No inventory stock items found.</div>
                            <div style="font-size: 0.82rem; margin-top: 0.25rem;">Click "+ Add Stock Item" to register your first kitchen ingredient.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $items->links() }}
    </div>

</div>

<!-- ==========================================
     MODAL 1: ADD RAW INVENTORY STOCK ITEM
     ========================================== -->
<div id="inventoryModal" class="modal-backdrop">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="ti ti-package"></i> Add Raw Inventory Stock</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('inventoryModal')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('admin.inventory.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Stock Item Name (ပစ္စည်းအမည်) *</label>
                <input type="text" name="name" class="form-control" placeholder="ဥပမာ- Basmati Rice, Olive Oil, Chicken Breast" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>SKU / Barcode</label>
                    <input type="text" name="sku" class="form-control" placeholder="RAW-001">
                </div>
                <div class="form-group">
                    <label>Measurement Unit (ယူနစ်) *</label>
                    <select name="unit" class="form-control" required>
                        <option value="kg">kg (Kilogram)</option>
                        <option value="g">g (Gram)</option>
                        <option value="viss">viss (ပိဿာ)</option>
                        <option value="liter">liter (Liter)</option>
                        <option value="ml">ml (Milliliter)</option>
                        <option value="pcs">pcs (Pieces / ခု)</option>
                        <option value="bottle">bottle (ပုလင်း)</option>
                        <option value="can">can (ဗူး)</option>
                        <option value="box">box (ဖာ/သေတ္တာ)</option>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Current Stock (လက်ကျန်ပမာဏ) *</label>
                    <input type="number" step="0.001" name="current_stock" class="form-control" placeholder="50.00" required>
                </div>
                <div class="form-group">
                    <label>Safety Alert Threshold (အနိမ့်ဆုံးသတိပေးချက်) *</label>
                    <input type="number" step="0.001" name="min_stock_alert" class="form-control" placeholder="10.00" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Unit Cost (တစ်ယူနစ်ကုန်ကျစရိတ် MMK)</label>
                    <input type="number" step="1" name="unit_cost" class="form-control" placeholder="4500">
                </div>
                <div class="form-group">
                    <label>Supplier / Vendor Name</label>
                    <input type="text" name="supplier_name" class="form-control" placeholder="ဥပမာ- Shwe Pyi Wholesale">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('inventoryModal')">Cancel</button>
                <button type="submit" class="btn-create-item">Save Stock Item</button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================
     MODAL 2: EDIT INVENTORY STOCK ITEM
     ========================================== -->
<div id="editInventoryModal" class="modal-backdrop">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="ti ti-edit"></i> Edit Inventory Stock Item</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('editInventoryModal')"><i class="ti ti-x"></i></button>
        </div>
        <form id="editInventoryForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Stock Item Name (ပစ္စည်းအမည်) *</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>SKU / Barcode</label>
                    <input type="text" name="sku" id="edit_sku" class="form-control">
                </div>
                <div class="form-group">
                    <label>Measurement Unit (ယူနစ်) *</label>
                    <select name="unit" id="edit_unit" class="form-control" required>
                        <option value="kg">kg (Kilogram)</option>
                        <option value="g">g (Gram)</option>
                        <option value="viss">viss (ပိဿာ)</option>
                        <option value="liter">liter (Liter)</option>
                        <option value="ml">ml (Milliliter)</option>
                        <option value="pcs">pcs (Pieces / ခု)</option>
                        <option value="bottle">bottle (ပုလင်း)</option>
                        <option value="can">can (ဗူး)</option>
                        <option value="box">box (ဖာ/သေတ္တာ)</option>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Current Stock (လက်ကျန်ပမာဏ) *</label>
                    <input type="number" step="0.001" name="current_stock" id="edit_current_stock" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Safety Alert Threshold *</label>
                    <input type="number" step="0.001" name="min_stock_alert" id="edit_min_stock_alert" class="form-control" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Unit Cost (MMK)</label>
                    <input type="number" step="1" name="unit_cost" id="edit_unit_cost" class="form-control">
                </div>
                <div class="form-group">
                    <label>Supplier / Vendor Name</label>
                    <input type="text" name="supplier_name" id="edit_supplier_name" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editInventoryModal')">Cancel</button>
                <button type="submit" class="btn-create-item">Update Stock Item</button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================
     MODAL 3: QUICK RESTOCK / ADJUSTMENT
     ========================================== -->
<div id="restockModal" class="modal-backdrop">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="ti ti-plus-circle" style="color: #10b981;"></i> Quick Restock / Stock In</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('restockModal')"><i class="ti ti-x"></i></button>
        </div>
        <form id="restockForm" method="POST">
            @csrf
            <div style="background: var(--bg-body); padding: 0.85rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
                <div style="font-size: 0.78rem; color: var(--text-muted); font-weight: 600;">SELECTED ITEM:</div>
                <div style="font-size: 1.05rem; font-weight: 800; color: var(--text-main);" id="restock_item_title">Chicken Breast</div>
                <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 0.2rem;">
                    Current In-Stock: <strong style="color: var(--text-main);" id="restock_current_qty">50.00 kg</strong>
                </div>
            </div>

            <div class="form-group">
                <label>Quantity to Add / Adjust (+ သို့မဟုတ် - ထည့်ပါ) *</label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="number" step="0.001" name="quantity" id="restock_quantity" class="form-control" placeholder="e.g. 15.00" required>
                    <span id="restock_unit_label" style="font-weight: 700; font-size: 0.9rem; padding: 0.65rem 0.85rem; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: 8px;">kg</span>
                </div>
                <small>အသစ်ရောက်ရှိလာသော အရေအတွက်ကို ပေါင်းထည့်ပါ (ဥပမာ- 10.00)။ ပျက်စီးဆုံးရှုံးမှု နှုတ်လိုပါက အနုတ်တန်ဖိုး ထည့်နိုင်ပါသည်။</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>New Unit Cost (MMK)</label>
                    <input type="number" step="1" name="unit_cost" id="restock_unit_cost" class="form-control" placeholder="Leave blank if unchanged">
                </div>
                <div class="form-group">
                    <label>Supplier / Vendor</label>
                    <input type="text" name="supplier_name" id="restock_supplier" class="form-control" placeholder="e.g. Fresh Market">
                </div>
            </div>

            <div class="form-group">
                <label>Adjustment Reason / Reference Note (မှတ်ချက်)</label>
                <input type="text" name="reason" class="form-control" placeholder="e.g. Daily Morning Market Purchase, Invoice #INV-492">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('restockModal')">Cancel</button>
                <button type="submit" class="btn-create-item" style="background: #10b981; color: #ffffff;">Confirm Restock</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleHelpHub() {
        const hub = document.getElementById('invHelpHub');
        hub.classList.toggle('show');
    }

    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    function handleEditBtn(btn) {
        const d = btn.dataset;
        openEditModal(d.id, d.name, d.sku, d.unit, d.stock, d.alert, d.cost, d.supplier);
    }

    function handleRestockBtn(btn) {
        const d = btn.dataset;
        openRestockModal(d.id, d.name, d.stock, d.unit, d.cost, d.supplier);
    }

    function openEditModal(id, name, sku, unit, currentStock, minStockAlert, unitCost, supplierName) {
        const form = document.getElementById('editInventoryForm');
        form.action = `/admin/inventory/${id}`;

        document.getElementById('edit_name').value = name;
        document.getElementById('edit_sku').value = (sku && sku !== 'null') ? sku : '';
        document.getElementById('edit_unit').value = unit;
        document.getElementById('edit_current_stock').value = currentStock;
        document.getElementById('edit_min_stock_alert').value = minStockAlert;
        document.getElementById('edit_unit_cost').value = unitCost;
        document.getElementById('edit_supplier_name').value = (supplierName && supplierName !== 'null') ? supplierName : '';

        openModal('editInventoryModal');
    }

    function openRestockModal(id, name, currentStock, unit, unitCost, supplierName) {
        const form = document.getElementById('restockForm');
        form.action = `/admin/inventory/${id}/restock`;

        document.getElementById('restock_item_title').textContent = name;
        document.getElementById('restock_current_qty').textContent = `${parseFloat(currentStock).toFixed(2)} ${unit}`;
        document.getElementById('restock_unit_label').textContent = unit;
        document.getElementById('restock_quantity').value = '';
        document.getElementById('restock_unit_cost').value = unitCost || '';
        document.getElementById('restock_supplier').value = (supplierName && supplierName !== 'null') ? supplierName : '';

        openModal('restockModal');
    }

    window.onclick = function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.classList.remove('show');
        }
    }
</script>
@endpush
