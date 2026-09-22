@extends('admin.layouts.app')

@section('title', 'Inventory & Stock Management')

@push('styles')
<style>
    .inv-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .btn-create-item {
        background: linear-gradient(135deg, var(--primary), #ea580c);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.28);
        transition: all 0.2s ease;
    }

    .btn-create-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(var(--primary-rgb), 0.38);
    }

    .inv-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .inv-stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .inv-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .icon-total-items { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .icon-alert-items { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .icon-val-items { background: rgba(16, 185, 129, 0.12); color: #10b981; }

    .inv-table-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .inv-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .inv-table th {
        background: var(--bg-body);
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 700;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .inv-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
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

    /* Modal */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.65);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        padding: 1.75rem;
        box-shadow: var(--shadow-lg);
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
    }

    .form-control {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-body);
        color: var(--text-main);
        font-size: 0.9rem;
        outline: none;
    }
</style>
@endpush

@section('content')
<div class="inventory-workspace-wrapper">

    <!-- Header -->
    <div class="inv-header">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
                <i class="ti ti-packages" style="color: #3b82f6;"></i>
                Inventory & Stock Management
            </h1>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('admin.inventory.report') }}" style="background: var(--bg-card); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.65rem 1.15rem; border-radius: 10px; font-weight: 700; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.45rem; text-decoration: none;">
                <i class="ti ti-report-analytics" style="color: #10b981;"></i>
                <span>Remaining Stock Report</span>
            </a>
            <button type="button" class="btn-create-item" onclick="openModal('inventoryModal')">
                <i class="ti ti-plus"></i>
                <span>+ Add Stock Item</span>
            </button>
        </div>
    </div>

    <!-- Live Metrics -->
    <div class="inv-stats-grid">
        <div class="inv-stat-card">
            <div class="inv-stat-icon icon-total-items"><i class="ti ti-box"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Stock Items</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ $stats['total_items'] }}</div>
            </div>
        </div>
        <div class="inv-stat-card">
            <div class="inv-stat-icon icon-alert-items"><i class="ti ti-alert-triangle"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Low Stock Alerts</div>
                <div style="font-size: 1.45rem; font-weight: 800; color: #ef4444;">{{ $stats['low_stock_count'] }}</div>
            </div>
        </div>
        <div class="inv-stat-card">
            <div class="inv-stat-icon icon-val-items"><i class="ti ti-coin"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Stock Valuation</div>
                <div style="font-size: 1.45rem; font-weight: 800; color: #10b981;">{{ number_format($stats['total_valuation'], 0) }} MMK</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.inventory.index') }}" class="inv-filter-pill {{ !$lowStockOnly ? 'active-all' : '' }}">
                All Stock Items ({{ $stats['total_items'] }})
            </a>
            <a href="{{ route('admin.inventory.index', ['low_stock_only' => 1]) }}" class="inv-filter-pill {{ $lowStockOnly ? 'active-low' : '' }}">
                ⚠️ Low Stock Only ({{ $stats['low_stock_count'] }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.inventory.index') }}" style="position: relative; min-width: 250px;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search stock or SKU..." style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.2rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-main); font-size: 0.85rem;">
            <i class="ti ti-search" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="inv-table-panel">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Item Name & SKU</th>
                    <th>Measurement Unit</th>
                    <th>Current Stock</th>
                    <th>Safety Alert Level</th>
                    <th>Unit Cost</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $item->name }}</div>
                            @if($item->sku)
                                <div style="font-size: 0.78rem; color: var(--text-muted);">SKU: {{ $item->sku }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; text-transform: uppercase; font-size: 0.82rem; background: var(--bg-body); padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px solid var(--border-color);">
                                {{ $item->unit }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 1.05rem; font-weight: 800; color: {{ $item->isLowStock() ? '#ef4444' : 'var(--text-main)' }};">
                                {{ number_format($item->current_stock, 2) }}
                            </span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $item->unit }}</span>
                        </td>
                        <td>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">≤ {{ number_format($item->min_stock_alert, 2) }} {{ $item->unit }}</span>
                        </td>
                        <td>
                            <span style="font-weight: 600;">{{ number_format($item->unit_cost, 0) }} MMK</span>
                        </td>
                        <td>
                            @if($item->isLowStock())
                                <span class="stock-badge low"><i class="ti ti-alert-triangle"></i> Low Stock</span>
                            @else
                                <span class="stock-badge ok"><i class="ti ti-check"></i> In Stock</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete inventory stock {{ addslashes($item->name) }}?');" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem;" title="Delete Item">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            No inventory items found. Click "+ Add Stock Item" to create raw stock entries.
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

<!-- MODAL: ADD INVENTORY ITEM -->
<div id="inventoryModal" class="modal-backdrop">
    <div class="modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800;">Add Raw Inventory Stock</h3>
            <button type="button" style="background:none; border:none; font-size:1.25rem; cursor:pointer;" onclick="closeModal('inventoryModal')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('admin.inventory.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Stock Item Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Basmati Rice, Olive Oil, Chicken Breast" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>SKU / Barcode</label>
                    <input type="text" name="sku" class="form-control" placeholder="RAW-001">
                </div>
                <div class="form-group">
                    <label>Measurement Unit *</label>
                    <select name="unit" class="form-control" required>
                        <option value="kg">kg (Kilogram)</option>
                        <option value="g">g (Gram)</option>
                        <option value="liter">liter (Liter)</option>
                        <option value="ml">ml (Milliliter)</option>
                        <option value="pcs">pcs (Pieces)</option>
                        <option value="bottle">bottle</option>
                        <option value="can">can</option>
                        <option value="box">box</option>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Current Stock *</label>
                    <input type="number" step="0.001" name="current_stock" class="form-control" placeholder="50.00" required>
                </div>
                <div class="form-group">
                    <label>Low Stock Warning Threshold *</label>
                    <input type="number" step="0.001" name="min_stock_alert" class="form-control" placeholder="10.00" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Unit Cost (MMK)</label>
                    <input type="number" step="0.01" name="unit_cost" class="form-control" placeholder="4500">
                </div>
                <div class="form-group">
                    <label>Supplier Name</label>
                    <input type="text" name="supplier_name" class="form-control" placeholder="e.g. City Mart Wholesale">
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" style="padding: 0.6rem 1.1rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-body); cursor: pointer;" onclick="closeModal('inventoryModal')">Cancel</button>
                <button type="submit" class="btn-create-item">Save Stock Item</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }
    window.onclick = function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.classList.remove('show');
        }
    }
</script>
@endpush
