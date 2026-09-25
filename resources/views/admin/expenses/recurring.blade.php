@extends('admin.layouts.app')

@section('title', 'Recurring Expenses')

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
                <i class="ti ti-rotate-clockwise text-primary"></i> Recurring Expense Automation & Retainers
            </h1>
           
        </div>
        <div class="header-actions">
            <form method="POST" action="{{ route('admin.expenses.recurring.trigger') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-modern-secondary" title="Scan and generate expenses due today">
                    <i class="ti ti-bolt text-primary"></i> Run Generator Now
                </button>
            </form>
            <button type="button" class="btn-gradient-primary" id="open-recurring-modal-btn">
                <i class="ti ti-plus"></i> New Schedule
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
                <span class="kpi-label">Active Schedules</span>
                <div class="kpi-icon-orb"><i class="ti ti-repeat"></i></div>
            </div>
            <div class="kpi-value">{{ $stats['active_schedules'] ?? $templates->where('is_active', true)->count() }}</div>
            <div class="kpi-subtitle">Automated monthly schedules</div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-top">
                <span class="kpi-label">Monthly Commitment</span>
                <div class="kpi-icon-orb"><i class="ti ti-cash"></i></div>
            </div>
            <div class="kpi-value">{{ number_format($stats['monthly_commitment'] ?? $templates->where('is_active', true)->sum('amount'), 0) }} <span style="font-size: 0.9rem; font-weight: 600;">MMK</span></div>
            <div class="kpi-subtitle">Estimated recurring spend</div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-top">
                <span class="kpi-label">Auto-Submit Enabled</span>
                <div class="kpi-icon-orb"><i class="ti ti-send"></i></div>
            </div>
            <div class="kpi-value">{{ $stats['auto_submit_count'] ?? $templates->where('auto_submit', true)->count() }}</div>
            <div class="kpi-subtitle">Pushed straight to approval</div>
        </div>

        <div class="kpi-card kpi-emerald">
            <div class="kpi-top">
                <span class="kpi-label">Total Templates</span>
                <div class="kpi-icon-orb"><i class="ti ti-template"></i></div>
            </div>
            <div class="kpi-value">{{ $stats['total_templates'] ?? $templates->total() }}</div>
            <div class="kpi-subtitle">Defined schedule blueprints</div>
        </div>
    </div>

    <!-- Modern Recurring Table Card -->
    <div class="table-container">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-calendar-repeat text-primary" style="font-size: 1.15rem;"></i>
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--text-main);">Configured Recurring Schedules</h3>
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">
                Showing <strong>{{ $templates->total() }}</strong> schedules
            </div>
        </div>

        <div class="table-responsive-clean">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Title / Schedule Template</th>
                        <th>Category Head</th>
                        <th>Vendor / Payee</th>
                        <th class="text-right">Recurring Amount (MMK)</th>
                        <th>Cadence</th>
                        <th>Next Due Date</th>
                        <th>Auto Approval Flow</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td>
                                <div>
                                    <strong style="font-size: 0.9375rem; color: var(--text-main);">{{ $template->title }}</strong>
                                    @if($template->notes)
                                        <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;">{{ $template->notes }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="modern-chip">
                                    <span class="modern-chip-dot" style="background-color: {{ $template->category?->color ?: '#9ec63b' }};"></span>
                                    <span>{{ $template->category?->name ?: 'Operational' }}</span>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1.5 font-medium">
                                    <i class="ti ti-building-store text-muted"></i>
                                    <span>{{ $template->vendor?->name ?: 'Direct Service / Landlord' }}</span>
                                </div>
                            </td>
                            <td class="text-right">
                                <span style="font-weight: 700; font-size: 1rem; color: var(--text-main); font-family: 'Mada', sans-serif;">
                                    {{ number_format($template->amount, 0) }} MMK
                                </span>
                            </td>
                            <td>
                                <span class="badge-mono-code" style="font-weight: 600; text-transform: uppercase;">
                                    {{ $template->frequency }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <span class="modern-chip" style="font-weight: 600;">
                                        <i class="ti ti-calendar text-primary"></i> {{ $template->next_due_date ? $template->next_due_date->format('d M Y') : 'N/A' }}
                                    </span>
                                    @if($template->last_generated_at)
                                        <div class="text-muted" style="font-size: 0.7rem; margin-top: 2px;">Last: {{ $template->last_generated_at->format('d M') }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($template->auto_submit)
                                    <span class="modern-chip" style="color: #0284c7; background: rgba(56, 189, 248, 0.1); border-color: rgba(56, 189, 248, 0.25);">
                                        <i class="ti ti-bolt"></i> Auto (Pending Approval)
                                    </span>
                                @else
                                    <span class="modern-chip text-muted">
                                        <i class="ti ti-file"></i> Save as Draft
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($template->is_active)
                                    <span class="status-pill status-paid"><span class="status-dot"></span> Active</span>
                                @else
                                    <span class="status-pill status-draft"><span class="status-dot"></span> Paused</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state-wrap">
                                    <div class="empty-icon-orb"><i class="ti ti-rotate-clockwise"></i></div>
                                    <h4 class="empty-title">No Recurring Schedules Configured</h4>
                                    <p class="empty-desc">Create recurring expense templates for restaurant rent, POS cloud hosting, or utility bills.</p>
                                    <button type="button" class="btn-gradient-primary" onclick="document.getElementById('add-recurring-modal').style.display='flex'">
                                        <i class="ti ti-plus"></i> Configure First Schedule
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-meta">
                    Showing <strong>{{ $templates->firstItem() }}</strong> to <strong>{{ $templates->lastItem() }}</strong> of <strong>{{ $templates->total() }}</strong> schedules
                </div>
                <div>
                    {{ $templates->links('admin.expenses._pagination') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modern Modal: Create Recurring Schedule -->
<div id="add-recurring-modal" class="modern-modal-backdrop" style="display: none;">
    <div class="modern-modal-dialog">
        <form method="POST" action="{{ route('admin.expenses.recurring.store') }}">
            @csrf
            <div class="modern-modal-header">
                <h3 class="modern-modal-title">
                    <i class="ti ti-calendar-plus text-primary"></i> Create Recurring Expense Schedule
                </h3>
                <button type="button" class="modern-modal-close" id="close-recurring-modal-btn">&times;</button>
            </div>

            <div class="modern-modal-body">
                <div class="form-group-modern">
                    <label class="form-label-modern">Schedule Title *</label>
                    <input type="text" name="title" class="form-control-modern" placeholder="e.g. Monthly Restaurant Floor Rental" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Expense Category *</label>
                        <select name="category_id" class="form-control-modern form-select-modern" required>
                            <option value="">Select Category Head</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->gl_account_code ?: '5000' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Vendor / Payee</label>
                        <select name="vendor_id" class="form-control-modern form-select-modern">
                            <option value="">Direct / Landlord / Other</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Amount (MMK) *</label>
                        <input type="number" step="1" name="amount" class="form-control-modern font-bold" placeholder="e.g. 1500000" required>
                    </div>

                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Cadence / Frequency *</label>
                        <select name="frequency" class="form-control-modern form-select-modern" required>
                            <option value="MONTHLY" selected>Monthly (Every 30 Days)</option>
                            <option value="WEEKLY">Weekly</option>
                            <option value="QUARTERLY">Quarterly (Every 3 Months)</option>
                            <option value="SEMI_ANNUALLY">Semi-Annually (Every 6 Months)</option>
                            <option value="ANNUALLY">Annually (Every Year)</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">First Due Date *</label>
                        <input type="date" name="start_date" class="form-control-modern" value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Default Payment Method</label>
                        <select name="payment_method" class="form-control-modern form-select-modern">
                            <option value="BANK_TRANSFER">Corporate Bank Transfer (KBZ/CB/AYA)</option>
                            <option value="CASH">Cash Register Drawer</option>
                            <option value="PETTY_CASH">Petty Cash Float</option>
                            <option value="DIGITAL_WALLET">KBZPay / WavePay Wallet</option>
                            <option value="CREDIT_CARD">Corporate Card</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Schedule Notes</label>
                    <textarea name="notes" class="form-control-modern" rows="2" placeholder="Lease contract reference, contract renewal dates, etc..."></textarea>
                </div>

                <div class="form-group-modern" style="margin-bottom: 0;">
                    <label class="d-flex align-items-center gap-2 mb-2" style="cursor: pointer;">
                        <input type="checkbox" name="auto_submit" value="1" checked style="accent-color: var(--primary); width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 0.875rem;">Automatically submit generated expense to Pending Approval</span>
                    </label>
                    <label class="d-flex align-items-center gap-2" style="cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" checked style="accent-color: var(--primary); width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 0.875rem;">Active (Enable background generation)</span>
                    </label>
                </div>
            </div>

            <div class="modern-modal-footer">
                <button type="button" class="btn-modern-secondary" id="cancel-recurring-modal-btn">Cancel</button>
                <button type="submit" class="btn-gradient-primary">
                    <i class="ti ti-check"></i> Save Schedule
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('add-recurring-modal');
    const openBtn = document.getElementById('open-recurring-modal-btn');
    const closeBtn = document.getElementById('close-recurring-modal-btn');
    const cancelBtn = document.getElementById('cancel-recurring-modal-btn');

    if (openBtn) openBtn.addEventListener('click', () => modal.style.display = 'flex');
    if (closeBtn) closeBtn.addEventListener('click', () => modal.style.display = 'none');
    if (cancelBtn) cancelBtn.addEventListener('click', () => modal.style.display = 'none');

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });
});
</script>
@endpush
@endsection
