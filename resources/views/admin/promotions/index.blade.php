@extends('admin.layouts.app')

@section('title', 'Discounts & Promotional Coupons')

@push('styles')
<style>
    .promo-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .btn-create-promo {
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

    .btn-create-promo:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(var(--primary-rgb), 0.38);
    }

    .promo-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .promo-stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .promo-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .icon-total { background: rgba(var(--primary-rgb), 0.12); color: var(--primary); }
    .icon-active { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    .icon-percent { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }
    .icon-fixed { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }

    .promo-table-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .promo-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .promo-table th {
        background: var(--bg-body);
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 700;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .promo-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
    }

    .code-badge {
        font-family: 'SF Mono', Monaco, monospace;
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--primary);
        background: rgba(var(--primary-rgb), 0.1);
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        border: 1px dashed var(--primary);
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
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
        max-width: 520px;
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
<div class="promotions-workspace-wrapper">

    <!-- Header -->
    <div class="promo-header">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
                <i class="ti ti-ticket" style="color: var(--primary);"></i>
                Discounts & Promotional Coupons
            </h1>
        </div>
        <div>
            <button type="button" class="btn-create-promo" onclick="openModal('promoModal')">
                <i class="ti ti-plus"></i>
                <span>+ Create New Coupon</span>
            </button>
        </div>
    </div>

    <!-- Live Metrics -->
    <div class="promo-stats-grid">
        <div class="promo-stat-card">
            <div class="promo-stat-icon icon-total"><i class="ti ti-tags"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Total Coupons</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ $stats['total_promotions'] }}</div>
            </div>
        </div>
        <div class="promo-stat-card">
            <div class="promo-stat-icon icon-active"><i class="ti ti-bolt"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Live & Active Now</div>
                <div style="font-size: 1.45rem; font-weight: 800; color: #10b981;">{{ $stats['active_promotions'] }}</div>
            </div>
        </div>
        <div class="promo-stat-card">
            <div class="promo-stat-icon icon-percent"><i class="ti ti-percentage"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Percentage Deals</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ $stats['percentage_coupons'] }}</div>
            </div>
        </div>
        <div class="promo-stat-card">
            <div class="promo-stat-icon icon-fixed"><i class="ti ti-cash"></i></div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Fixed Amount Coupons</div>
                <div style="font-size: 1.45rem; font-weight: 800;">{{ $stats['fixed_coupons'] }}</div>
            </div>
        </div>
    </div>

    <!-- Table Panel -->
    <div class="promo-table-panel">
        <table class="promo-table">
            <thead>
                <tr>
                    <th>Coupon Code</th>
                    <th>Campaign & Description</th>
                    <th>Discount Rate</th>
                    <th>Min Bill Spend</th>
                    <th>Validity Period</th>
                    <th>Usage</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promotions as $promo)
                    <tr>
                        <td>
                            <span class="code-badge">{{ $promo->code }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $promo->name }}</div>
                            @if($promo->description)
                                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.15rem;">{{ $promo->description }}</div>
                            @endif
                        </td>
                        <td>
                            @if($promo->type === 'PERCENTAGE')
                                <span style="font-weight: 800; color: #8b5cf6; font-size: 1rem;">{{ (float) $promo->value }}% OFF</span>
                            @else
                                <span style="font-weight: 800; color: #10b981; font-size: 1rem;">{{ number_format($promo->value, 0) }} MMK</span>
                            @endif
                        </td>
                        <td>
                            @if($promo->min_order_amount > 0)
                                <span style="font-size: 0.85rem; color: var(--text-main);">≥ {{ number_format($promo->min_order_amount, 0) }} MMK</span>
                            @else
                                <span style="font-size: 0.82rem; color: var(--text-muted);">No Min Spend</span>
                            @endif
                        </td>
                        <td>
                            @if($promo->start_date || $promo->end_date)
                                <span style="font-size: 0.82rem; color: var(--text-muted);">
                                    {{ $promo->start_date?->format('M d') ?? 'Anytime' }} - {{ $promo->end_date?->format('M d, Y') ?? 'Never' }}
                                </span>
                            @else
                                <span style="font-size: 0.82rem; color: var(--text-muted);">Always Active</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size: 0.85rem; font-weight: 600;">{{ $promo->used_count }}</span>
                            @if($promo->usage_limit)
                                <span style="font-size: 0.78rem; color: var(--text-muted);">/ {{ $promo->usage_limit }}</span>
                            @endif
                        </td>
                        <td>
                            @if($promo->isValidNow())
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.76rem; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                    <i class="ti ti-circle-check"></i> Active
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.76rem; font-weight: 700; background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                                    <i class="ti ti-circle-x"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.promotions.toggle', $promo->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem; margin-right: 0.4rem;" title="{{ $promo->is_active ? 'Pause Coupon' : 'Activate Coupon' }}">
                                    <i class="ti {{ $promo->is_active ? 'ti-pause' : 'ti-player-play' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.promotions.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Delete coupon {{ $promo->code }}?');" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem;" title="Delete Coupon">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            No promotional coupons created yet. Click "+ Create New Coupon" above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $promotions->links() }}
    </div>

</div>

<!-- MODAL: ADD PROMOTION -->
<div id="promoModal" class="modal-backdrop">
    <div class="modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800;">Create Promotion Coupon</h3>
            <button type="button" style="background:none; border:none; font-size:1.25rem; cursor:pointer;" onclick="closeModal('promoModal')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('admin.promotions.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Coupon Code *</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. WELCOME10" style="text-transform: uppercase; font-weight: 700;" required>
                </div>
                <div class="form-group">
                    <label>Discount Type *</label>
                    <select name="type" class="form-control" required>
                        <option value="PERCENTAGE">Percentage (%)</option>
                        <option value="FIXED">Fixed Amount (MMK)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Campaign Title *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. New Customer Welcome Discount" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Discount Value *</label>
                    <input type="number" step="0.01" name="value" class="form-control" placeholder="10 (for 10%) or 5000" required>
                </div>
                <div class="form-group">
                    <label>Min Order Bill (MMK)</label>
                    <input type="number" step="0.01" name="min_order_amount" class="form-control" placeholder="0.00">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="form-group">
                    <label>Expiration Date</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Usage Limit (Optional)</label>
                <input type="number" name="usage_limit" class="form-control" placeholder="e.g. 100 times max">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" style="padding: 0.6rem 1.1rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-body); cursor: pointer;" onclick="closeModal('promoModal')">Cancel</button>
                <button type="submit" class="btn-create-promo">Save Coupon</button>
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
