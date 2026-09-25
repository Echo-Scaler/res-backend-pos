@extends('admin.layouts.app')

@section('title', 'Expense Categories')

@push('styles')
@include('admin.expenses._styles')
@endpush

@section('content')
<div class="expense-portal">
    <!-- Sub-Navigation Bar -->
    @include('admin.expenses._nav')

    <!-- Page Header -->
    <div class="expense-header-wrap">
        <div class="header-title-group">
            <h1>
                <i class="ti ti-tags text-primary"></i> Expense Categories & GL Chart of Accounts
            </h1>
            
        </div>
        <div class="header-actions">
            <button type="button" class="btn-gradient-primary" id="open-cat-modal-btn">
                <i class="ti ti-plus"></i> New Category Head
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="glass-card mb-4" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); padding: 1rem 1.25rem;">
            <div class="d-flex align-items-center gap-2 text-success" style="font-weight: 600;">
                <i class="ti ti-circle-check" style="font-size: 1.25rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Quick Metric Cards -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-lime">
            <div class="kpi-top">
                <span class="kpi-label">Total Categories</span>
                <div class="kpi-icon-orb"><i class="ti ti-category"></i></div>
            </div>
            <div class="kpi-value">{{ $categories->count() }}</div>
            <div class="kpi-subtitle">Configured expense heads</div>
        </div>

        <div class="kpi-card kpi-emerald">
            <div class="kpi-top">
                <span class="kpi-label">Active Categories</span>
                <div class="kpi-icon-orb"><i class="ti ti-circle-check"></i></div>
            </div>
            <div class="kpi-value">{{ $stats['active_categories'] ?? $categories->where('is_active', true)->count() }}</div>
            <div class="kpi-subtitle">Available for new entries</div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-top">
                <span class="kpi-label">GL Mapped Accounts</span>
                <div class="kpi-icon-orb"><i class="ti ti-file-analytics"></i></div>
            </div>
            <div class="kpi-value">{{ $stats['gl_mapped'] ?? $categories->whereNotNull('gl_account_code')->count() }}</div>
            <div class="kpi-subtitle">Chart of Accounts linked</div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-top">
                <span class="kpi-label">Recorded Transactions</span>
                <div class="kpi-icon-orb"><i class="ti ti-receipt-2"></i></div>
            </div>
            <div class="kpi-value">{{ $stats['total_expenses'] ?? $categories->sum('expenses_count') }}</div>
            <div class="kpi-subtitle">Historical expense entries</div>
        </div>
    </div>

    <!-- Modern Categories Table Card -->
    <div class="table-container">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-layout-grid text-primary" style="font-size: 1.15rem;"></i>
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--text-main);">Configured Category Heads</h3>
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">
                Showing <strong>{{ $categories->total() }}</strong> categories
            </div>
        </div>

        <div class="table-responsive-clean">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Category Name & Swatch</th>
                        <th>Category Code</th>
                        <th>GL Account Code</th>
                        <th>Description</th>
                        <th class="text-center">Recorded Expenses</th>
                        <th>Status</th>
                        <th class="text-right">Color Hex</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span style="width: 14px; height: 14px; min-width: 14px; max-width: 14px; flex-shrink: 0; border-radius: 50%; background-color: {{ $category->color ?: '#9ec63b' }}; display: inline-block; box-shadow: 0 0 0 2px var(--bg-card), 0 0 0 3px rgba(0,0,0,0.08);"></span>
                                    <div>
                                        <strong style="font-size: 0.9375rem; font-weight: 600; color: var(--text-main);">{{ $category->name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-mono-code">{{ $category->code ?: 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="modern-chip" style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 0.75rem;">
                                    <i class="ti ti-hash text-muted"></i> {{ $category->gl_account_code ?: '5000' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 0.8125rem;">{{ $category->description ?: 'Operational expense head' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="modern-chip" style="font-weight: 700;">{{ $category->expenses_count }}</span>
                            </td>
                            <td>
                                @if($category->is_active)
                                    <span class="status-pill status-paid"><span class="status-dot"></span> Active</span>
                                @else
                                    <span class="status-pill status-draft"><span class="status-dot"></span> Disabled</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <span class="badge-mono-code" style="color: {{ $category->color ?: '#9ec63b' }}; font-weight: 700;">
                                    {{ $category->color ?: '#9ec63b' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-wrap">
                                    <div class="empty-icon-orb"><i class="ti ti-tags"></i></div>
                                    <h4 class="empty-title">No Expense Categories Found</h4>
                                    <p class="empty-desc">Create category heads to start tracking and classifying European restaurant expenses.</p>
                                    <button type="button" class="btn-gradient-primary" onclick="document.getElementById('add-cat-modal').style.display='flex'">
                                        <i class="ti ti-plus"></i> Add First Category
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-meta">
                    Showing <strong>{{ $categories->firstItem() }}</strong> to <strong>{{ $categories->lastItem() }}</strong> of <strong>{{ $categories->total() }}</strong> categories
                </div>
                <div>
                    {{ $categories->links('admin.expenses._pagination') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modern Modal: Create Expense Category -->
<div id="add-cat-modal" class="modern-modal-backdrop" style="display: none;">
    <div class="modern-modal-dialog">
        <form method="POST" action="{{ route('admin.expenses.categories.store') }}">
            @csrf
            <div class="modern-modal-header">
                <h3 class="modern-modal-title">
                    <i class="ti ti-tag text-primary"></i> Create Expense Category Head
                </h3>
                <button type="button" class="modern-modal-close" id="close-cat-modal-btn">&times;</button>
            </div>

            <div class="modern-modal-body">
                <div class="form-group-modern">
                    <label class="form-label-modern">Category Name *</label>
                    <input type="text" name="name" class="form-control-modern" placeholder="e.g. Kitchen Raw Ingredients" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Category Code</label>
                        <input type="text" name="code" class="form-control-modern font-mono" placeholder="OPEX-KIT">
                    </div>
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">GL Account Code</label>
                        <input type="text" name="gl_account_code" class="form-control-modern font-mono" placeholder="6001">
                    </div>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Description</label>
                    <textarea name="description" class="form-control-modern" rows="2" placeholder="Brief scope of costs categorized under this head..."></textarea>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Color Swatch & Badge Theme</label>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <input type="color" id="cat-color-picker" name="color" value="#9ec63b" style="border: none; width: 42px; height: 38px; border-radius: 8px; cursor: pointer; background: transparent; padding: 0;">
                        <input type="text" id="cat-color-hex" class="form-control-modern font-mono" value="#9ec63b" style="max-width: 120px;" readonly>
                    </div>
                    <div class="color-swatch-list">
                        <span class="color-swatch-btn" data-color="#9ec63b" style="background: #9ec63b;" title="Lime Green"></span>
                        <span class="color-swatch-btn" data-color="#10b981" style="background: #10b981;" title="Emerald"></span>
                        <span class="color-swatch-btn" data-color="#0ea5e9" style="background: #0ea5e9;" title="Sky Blue"></span>
                        <span class="color-swatch-btn" data-color="#f59e0b" style="background: #f59e0b;" title="Amber"></span>
                        <span class="color-swatch-btn" data-color="#8b5cf6" style="background: #8b5cf6;" title="Purple"></span>
                        <span class="color-swatch-btn" data-color="#f43f5e" style="background: #f43f5e;" title="Rose Red"></span>
                        <span class="color-swatch-btn" data-color="#06b6d4" style="background: #06b6d4;" title="Cyan"></span>
                        <span class="color-swatch-btn" data-color="#64748b" style="background: #64748b;" title="Slate Gray"></span>
                    </div>
                </div>

                <div class="form-group-modern" style="margin-bottom: 0;">
                    <label class="d-flex align-items-center gap-2" style="cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" checked style="accent-color: var(--primary); width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 0.875rem;">Enable this category immediately for recording</span>
                    </label>
                </div>
            </div>

            <div class="modern-modal-footer">
                <button type="button" class="btn-modern-secondary" id="cancel-cat-modal-btn">Cancel</button>
                <button type="submit" class="btn-gradient-primary">
                    <i class="ti ti-check"></i> Save Category
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('add-cat-modal');
    const openBtn = document.getElementById('open-cat-modal-btn');
    const closeBtn = document.getElementById('close-cat-modal-btn');
    const cancelBtn = document.getElementById('cancel-cat-modal-btn');
    const colorPicker = document.getElementById('cat-color-picker');
    const colorHex = document.getElementById('cat-color-hex');
    const swatchBtns = document.querySelectorAll('.color-swatch-btn');

    if (openBtn) openBtn.addEventListener('click', () => modal.style.display = 'flex');
    if (closeBtn) closeBtn.addEventListener('click', () => modal.style.display = 'none');
    if (cancelBtn) cancelBtn.addEventListener('click', () => modal.style.display = 'none');

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });

    if (colorPicker && colorHex) {
        colorPicker.addEventListener('input', (e) => {
            colorHex.value = e.target.value;
        });
    }

    swatchBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const color = btn.getAttribute('data-color');
            if (colorPicker && colorHex) {
                colorPicker.value = color;
                colorHex.value = color;
            }
            swatchBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
});
</script>
@endpush
@endsection
