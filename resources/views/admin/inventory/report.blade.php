@extends('admin.layouts.app')

@section('title', 'Inventory Stock Remaining Report')

@push('styles')
<style>
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .report-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-print {
        background: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        padding: 0.65rem 1.15rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-print:hover {
        background: var(--bg-body);
        border-color: var(--primary);
    }

    .btn-export {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #ffffff !important;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .btn-back {
        background: var(--bg-card);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
        padding: 0.65rem 1.15rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
    }

    /* Report Summary Metrics */
    .report-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .report-stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .report-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .icon-items { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .icon-units { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }
    .icon-alerts { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .icon-val { background: rgba(16, 185, 129, 0.12); color: #10b981; }

    /* Report Table */
    .report-table-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .report-table th {
        background: var(--bg-body);
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 700;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .report-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
    }

    .report-table tbody tr:hover {
        background-color: rgba(var(--primary-rgb), 0.02);
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.78rem;
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

    /* Print Stylesheet */
    @media print {
        .sidebar, .header, .report-actions, .breadcrumb-area, .btn-print, .btn-export, .btn-back, form {
            display: none !important;
        }
        .main-wrapper {
            padding: 0 !important;
            margin: 0 !important;
        }
        .report-header {
            margin-bottom: 1rem !important;
        }
        .report-table th {
            background: #f1f5f9 !important;
            color: #000000 !important;
        }
        .report-table td {
            color: #000000 !important;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="inventory-report-wrapper">

    <!-- Header -->
    <div class="report-header">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
                <i class="ti ti-report-analytics" style="color: #10b981;"></i>
                Inventory Stock Remaining Report
            </h1>
            <p style="color: var(--text-muted); font-size: 0.92rem; margin-top: 0.25rem;">
                Official kitchen stock balance, remaining on-hand quantities, safety threshold alerts, and total valuation for <strong>{{ $restaurant->name ?? 'Restaurant' }}</strong>.
                <span style="font-size: 0.8rem; opacity: 0.8;">Generated: {{ now()->format('M d, Y h:i A') }}</span>
            </p>
        </div>
        <div class="report-actions">
            <a href="{{ route('admin.inventory.index') }}" class="btn-back">
                <i class="ti ti-arrow-left"></i>
                <span>Inventory Catalog</span>
            </a>
            <button type="button" class="btn-print" onclick="window.print()">
                <i class="ti ti-printer"></i>
                <span>Print Report</span>
            </button>
            <a href="{{ route('admin.inventory.report', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-export">
                <i class="ti ti-download"></i>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="report-stats-grid">
        <div class="report-stat-card">
            <div class="report-stat-icon icon-items"><i class="ti ti-box"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Total Inventory Items</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ $stats['total_items'] }}</div>
            </div>
        </div>
        <div class="report-stat-card">
            <div class="report-stat-icon icon-units"><i class="ti ti-scale"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Total Units on Hand</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ number_format($stats['total_units'], 1) }}</div>
            </div>
        </div>
        <div class="report-stat-card">
            <div class="report-stat-icon icon-alerts"><i class="ti ti-alert-triangle"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Low Stock Warnings</div>
                <div style="font-size: 1.45rem; font-weight: 800; color: #ef4444;">{{ $stats['low_stock_count'] }}</div>
            </div>
        </div>
        <div class="report-stat-card">
            <div class="report-stat-icon icon-val"><i class="ti ti-coin"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Total Remaining Valuation</div>
                <div style="font-size: 1.45rem; font-weight: 800; color: #10b981;">{{ number_format($stats['total_valuation'], 0) }} MMK</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.inventory.report') }}" class="report-filter-pill {{ !$lowStockOnly ? 'active-all' : '' }}">
                All Stock ({{ $stats['total_items'] }})
            </a>
            <a href="{{ route('admin.inventory.report', ['low_stock_only' => 1]) }}" class="report-filter-pill {{ $lowStockOnly ? 'active-low' : '' }}">
                ⚠️ Low Stock Alert Items ({{ $stats['low_stock_count'] }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.inventory.report') }}" style="position: relative; min-width: 260px;">
            @if($lowStockOnly)
                <input type="hidden" name="low_stock_only" value="1">
            @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Search report items..." style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.2rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-size: 0.85rem;">
            <i class="ti ti-search" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </form>
    </div>

    <!-- Report Table Panel -->
    <div class="report-table-panel">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Item Name & SKU</th>
                    <th>Unit</th>
                    <th>Remaining Stock (Remain)</th>
                    <th>Safety Threshold</th>
                    <th>Unit Cost</th>
                    <th>Remaining Worth</th>
                    <th>Stock Health Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $worth = $item->current_stock * $item->unit_cost;
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $item->name }}</div>
                            @if($item->sku)
                                <div style="font-size: 0.76rem; color: var(--text-muted);">SKU: {{ $item->sku }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; font-size: 0.82rem; text-transform: uppercase;">{{ $item->unit }}</span>
                        </td>
                        <td>
                            <span style="font-size: 1.1rem; font-weight: 800; color: {{ $item->isLowStock() ? '#ef4444' : '#10b981' }};">
                                {{ number_format($item->current_stock, 2) }}
                            </span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $item->unit }}</span>
                        </td>
                        <td>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">
                                ≤ {{ number_format($item->min_stock_alert, 2) }} {{ $item->unit }}
                            </span>
                        </td>
                        <td>
                            <span>{{ number_format($item->unit_cost, 0) }} MMK</span>
                        </td>
                        <td>
                            <span style="font-weight: 800; color: var(--text-main);">{{ number_format($worth, 0) }} MMK</span>
                        </td>
                        <td>
                            @if($item->isLowStock())
                                <span class="stock-badge low"><i class="ti ti-alert-triangle"></i> Low Stock Warning</span>
                            @else
                                <span class="stock-badge ok"><i class="ti ti-check"></i> Healthy Stock</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            No inventory items found matching report criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
