@extends('admin.layouts.app')

@section('title', 'Budget vs Actual Expenses')

@push('styles')
@include('admin.expenses._styles')
<style>
    .budget-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .budget-card-glass {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02);
    }

    .budget-card-glass:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.06);
    }

    .budget-card-glass::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--border-color);
    }

    .budget-card-glass.normal::before { background: linear-gradient(90deg, #10b981, #059669); }
    .budget-card-glass.warning::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .budget-card-glass.exceeded::before { background: linear-gradient(90deg, #ef4444, #dc2626); }

    .meter-track-modern {
        height: 12px;
        background: var(--bg-hover);
        border-radius: 9999px;
        overflow: hidden;
        margin: 1.25rem 0;
        border: 1px solid var(--border-subtle);
        position: relative;
    }

    .meter-fill-modern {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .meter-fill-modern.normal {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    }
    .meter-fill-modern.warning {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.4);
    }
    .meter-fill-modern.exceeded {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);
    }

    .threshold-marker {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background: rgba(245, 158, 11, 0.7);
        z-index: 2;
    }
</style>
@endpush

@section('content')
@php
    $budgetComparison = collect($budgetComparison);
@endphp
<div class="expense-portal">
    <!-- Sub-Navigation Bar -->
    @include('admin.expenses._nav')

    <!-- Page Header -->
    <div class="expense-header-wrap">
        <div class="header-title-group">
            <h1>
                <i class="ti ti-target text-primary"></i> Budget vs Actual Cost Variance
            </h1>
            
        </div>
        <div class="header-actions">
            <button type="button" class="btn-gradient-primary" id="open-budget-modal-btn">
                <i class="ti ti-plus"></i> Set Category Budget
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

    <!-- Month & Period Filter Strip -->
    <div class="glass-card mb-4" style="padding: 1rem 1.5rem;">
        <form method="GET" action="{{ route('admin.expenses.budgets') }}" class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-calendar-stats text-primary" style="font-size: 1.25rem;"></i>
                <span style="font-weight: 700; font-size: 0.9375rem; color: var(--text-main);">Viewing Budget Period:</span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="month" class="form-control-modern form-select-modern" style="width: 140px;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>

                <select name="year" class="form-control-modern form-select-modern" style="width: 110px;">
                    @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="btn-gradient-primary" style="padding: 0.55rem 1rem;">
                    <i class="ti ti-filter"></i> Switch Period
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Metric Cards -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-lime">
            <div class="kpi-top">
                <span class="kpi-label">Total Allocated Budget</span>
                <div class="kpi-icon-orb"><i class="ti ti-wallet"></i></div>
            </div>
            <div class="kpi-value">{{ number_format($budgetComparison->sum('budget_amount'), 0) }} <span style="font-size: 0.9rem; font-weight: 600;">MMK</span></div>
            <div class="kpi-subtitle">Planned limit for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-top">
                <span class="kpi-label">Actual Incurred Spend</span>
                <div class="kpi-icon-orb"><i class="ti ti-receipt"></i></div>
            </div>
            <div class="kpi-value">{{ number_format($budgetComparison->sum('actual_spent'), 0) }} <span style="font-size: 0.9rem; font-weight: 600;">MMK</span></div>
            <div class="kpi-subtitle">Total actual spend logged</div>
        </div>

        <div class="kpi-card kpi-emerald">
            <div class="kpi-top">
                <span class="kpi-label">Remaining Safe Buffer</span>
                <div class="kpi-icon-orb"><i class="ti ti-shield-check"></i></div>
            </div>
            @php
                $netRemaining = $budgetComparison->sum('budget_amount') - $budgetComparison->sum('actual_spent');
            @endphp
            <div class="kpi-value" style="color: {{ $netRemaining >= 0 ? '#10b981' : '#ef4444' }};">
                {{ number_format(abs($netRemaining), 0) }} <span style="font-size: 0.9rem; font-weight: 600;">MMK {{ $netRemaining >= 0 ? 'Left' : 'Over' }}</span>
            </div>
            <div class="kpi-subtitle">{{ $netRemaining >= 0 ? 'Surplus within planned limits' : 'Deficit / Over budget' }}</div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-top">
                <span class="kpi-label">Risk Overruns</span>
                <div class="kpi-icon-orb"><i class="ti ti-alert-triangle"></i></div>
            </div>
            <div class="kpi-value">{{ $budgetComparison->whereIn('status', ['WARNING', 'EXCEEDED'])->count() }} <span style="font-size: 0.9rem; font-weight: 600;">Categories</span></div>
            <div class="kpi-subtitle">Exceeded or approaching threshold</div>
        </div>
    </div>

    <!-- Bento Budget Cards Grid -->
    <div class="budget-grid">
        @forelse($budgetComparison as $item)
            @php
                $statusSlug = strtolower($item['status']);
                $pct = min(100, $item['percentage_used']);
            @endphp
            <div class="budget-card-glass {{ $statusSlug }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 12px; height: 12px; border-radius: 50%; background-color: {{ $item['color'] }}; display: inline-block;"></span>
                        <strong style="font-size: 1.05rem; color: var(--text-main);">{{ $item['category_name'] }}</strong>
                    </div>

                    @if($item['status'] === 'EXCEEDED')
                        <span class="status-pill status-rejected"><span class="status-dot"></span> EXCEEDED ({{ $item['percentage_used'] }}%)</span>
                    @elseif($item['status'] === 'WARNING')
                        <span class="status-pill status-pending"><span class="status-dot"></span> ALERT > {{ $item['alert_threshold'] }}% ({{ $item['percentage_used'] }}%)</span>
                    @else
                        <span class="status-pill status-paid"><span class="status-dot"></span> HEALTHY ({{ $item['percentage_used'] }}%)</span>
                    @endif
                </div>

                <div class="meter-track-modern">
                    <div class="threshold-marker" style="left: {{ min(100, $item['alert_threshold']) }}%;" title="Warning alert threshold: {{ $item['alert_threshold'] }}%"></div>
                    <div class="meter-fill-modern {{ $statusSlug }}" style="width: {{ $pct }}%;"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">Actual Spent</div>
                        <div style="font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                            {{ number_format($item['actual_spent'], 0) }} MMK
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">Cap Budget</div>
                        <div style="font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                            {{ number_format($item['budget_amount'], 0) }} MMK
                        </div>
                    </div>
                </div>

                <div class="pt-2" style="border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem;">
                    <span class="text-muted">
                        Remaining Buffer: <strong style="color: {{ $item['variance'] >= 0 ? '#10b981' : '#ef4444' }};">{{ number_format(abs($item['variance']), 0) }} MMK {{ $item['variance'] >= 0 ? 'Left' : 'Over' }}</strong>
                    </span>
                    <button type="button" class="btn-modern-secondary edit-budget-btn"
                            data-cat-id="{{ $item['category_id'] }}"
                            data-cat-name="{{ $item['category_name'] }}"
                            data-amount="{{ $item['budget_amount'] }}"
                            data-threshold="{{ $item['alert_threshold'] }}"
                            style="padding: 0.3rem 0.65rem; font-size: 0.75rem;">
                        <i class="ti ti-edit"></i> Adjust Cap
                    </button>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1;">
                <div class="glass-card">
                    <div class="empty-state-wrap">
                        <div class="empty-icon-orb"><i class="ti ti-target"></i></div>
                        <h4 class="empty-title">No Budgets Defined For This Month</h4>
                        <p class="empty-desc">Establish spending caps per category to track expenditure trends and receive automatic variance warnings.</p>
                        <button type="button" class="btn-gradient-primary" onclick="document.getElementById('set-budget-modal').style.display='flex'">
                            <i class="ti ti-plus"></i> Set Category Budget
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modern Modal: Set Category Budget -->
<div id="set-budget-modal" class="modern-modal-backdrop" style="display: none;">
    <div class="modern-modal-dialog">
        <form method="POST" action="{{ route('admin.expenses.budgets.store') }}">
            @csrf
            <div class="modern-modal-header">
                <h3 class="modern-modal-title">
                    <i class="ti ti-target text-primary"></i> Set Category Expense Budget
                </h3>
                <button type="button" class="modern-modal-close" id="close-budget-modal-btn">&times;</button>
            </div>

            <div class="modern-modal-body">
                <div class="form-group-modern">
                    <label class="form-label-modern">Expense Category *</label>
                    <select name="category_id" id="budget-cat-select" class="form-control-modern form-select-modern" required>
                        <option value="">Select Category Head</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->gl_account_code ?: '5000' }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Target Month *</label>
                        <select name="month" class="form-control-modern form-select-modern" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Target Year *</label>
                        <select name="year" class="form-control-modern form-select-modern" required>
                            @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Cap Amount (MMK) *</label>
                        <input type="number" step="1" name="amount" id="budget-amount-input" class="form-control-modern font-bold" placeholder="e.g. 5000000" required>
                    </div>

                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Early Warning Threshold (%)</label>
                        <input type="number" name="alert_threshold" id="budget-threshold-input" class="form-control-modern" value="80" min="50" max="100" required>
                    </div>
                </div>

                <div class="form-group-modern" style="margin-bottom: 0;">
                    <label class="form-label-modern">Budget Notes</label>
                    <textarea name="notes" class="form-control-modern" rows="2" placeholder="Rationale or special seasonal cap explanation..."></textarea>
                </div>
            </div>

            <div class="modern-modal-footer">
                <button type="button" class="btn-modern-secondary" id="cancel-budget-modal-btn">Cancel</button>
                <button type="submit" class="btn-gradient-primary">
                    <i class="ti ti-check"></i> Save Budget
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('set-budget-modal');
    const openBtn = document.getElementById('open-budget-modal-btn');
    const closeBtn = document.getElementById('close-budget-modal-btn');
    const cancelBtn = document.getElementById('cancel-budget-modal-btn');
    const catSelect = document.getElementById('budget-cat-select');
    const amountInput = document.getElementById('budget-amount-input');
    const thresholdInput = document.getElementById('budget-threshold-input');

    if (openBtn) openBtn.addEventListener('click', () => {
        catSelect.value = '';
        amountInput.value = '';
        thresholdInput.value = '80';
        modal.style.display = 'flex';
    });

    if (closeBtn) closeBtn.addEventListener('click', () => modal.style.display = 'none');
    if (cancelBtn) cancelBtn.addEventListener('click', () => modal.style.display = 'none');

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });

    document.querySelectorAll('.edit-budget-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            catSelect.value = this.getAttribute('data-cat-id');
            amountInput.value = this.getAttribute('data-amount');
            thresholdInput.value = this.getAttribute('data-threshold');
            modal.style.display = 'flex';
        });
    });
});
</script>
@endpush
@endsection
