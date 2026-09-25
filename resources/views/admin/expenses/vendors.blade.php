@extends('admin.layouts.app')

@section('title', 'Vendors & Suppliers')

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
                <i class="ti ti-building-store text-primary"></i> Vendors, Wholesalers & Commercial Suppliers
            </h1>
           
        </div>
        <div class="header-actions">
            <button type="button" class="btn-gradient-primary" id="open-vendor-modal-btn">
                <i class="ti ti-plus"></i> Register New Vendor
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
                <span class="kpi-label">Registered Vendors</span>
                <div class="kpi-icon-orb"><i class="ti ti-building-store"></i></div>
            </div>
            <div class="kpi-value">{{ $vendors->count() }}</div>
            <div class="kpi-subtitle">Total supplier accounts</div>
        </div>

        <div class="kpi-card kpi-emerald">
            <div class="kpi-top">
                <span class="kpi-label">Active Suppliers</span>
                <div class="kpi-icon-orb"><i class="ti ti-truck-delivery"></i></div>
            </div>
            <div class="kpi-value">{{ $stats['active_suppliers'] ?? $vendors->where('is_active', true)->count() }}</div>
            <div class="kpi-subtitle">Eligible for purchase orders</div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-top">
                <span class="kpi-label">Total Invoices Logged</span>
                <div class="kpi-icon-orb"><i class="ti ti-file-invoice"></i></div>
            </div>
            <div class="kpi-value">{{ $stats['total_invoices'] ?? $vendors->sum('expenses_count') }}</div>
            <div class="kpi-subtitle">Procurement transactions</div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-top">
                <span class="kpi-label">Avg Credit Terms</span>
                <div class="kpi-icon-orb"><i class="ti ti-calendar-time"></i></div>
            </div>
            <div class="kpi-value">{{ $stats['avg_credit_days'] ?? round($vendors->avg('payment_terms_days') ?: 30) }} <span style="font-size: 1rem; font-weight: 600;">Days</span></div>
            <div class="kpi-subtitle">Standard payment window</div>
        </div>
    </div>

    <!-- Modern Vendors Table Card -->
    <div class="table-container">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-truck text-primary" style="font-size: 1.15rem;"></i>
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--text-main);">Commercial Vendor Directory</h3>
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">
                Showing <strong>{{ $vendors->total() }}</strong> verified vendors
            </div>
        </div>

        <div class="table-responsive-clean">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Vendor / Company</th>
                        <th>Contact Person</th>
                        <th>Communication</th>
                        <th>Banking & Account</th>
                        <th>Payment Terms</th>
                        <th class="text-center">Invoices</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            <td>
                                <div class="vendor-info-cell">
                                    <div class="vendor-avatar">
                                        {{ strtoupper(substr($vendor->name, 0, 2)) }}
                                    </div>
                                    <div class="vendor-info-text">
                                        <strong class="vendor-name-title">{{ $vendor->name }}</strong>
                                        <span class="badge-mono-code" style="align-self: flex-start; display: inline-block;">{{ $vendor->code ?: 'VND-'.$vendor->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1-5 font-medium">
                                    <i class="ti ti-user text-muted"></i>
                                    <span>{{ $vendor->contact_person ?: 'Accounts Dept' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($vendor->phone)
                                        <a href="tel:{{ $vendor->phone }}" class="modern-chip" style="text-decoration: none; padding: 0.15rem 0.45rem; font-size: 0.75rem;">
                                            <i class="ti ti-phone text-primary"></i> {{ $vendor->phone }}
                                        </a>
                                    @endif
                                    @if($vendor->email)
                                        <a href="mailto:{{ $vendor->email }}" class="text-muted" style="text-decoration: none; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="ti ti-mail"></i> {{ $vendor->email }}
                                        </a>
                                    @endif
                                    @if(!$vendor->phone && !$vendor->email)
                                        <span class="text-muted" style="font-size: 0.75rem;">Not specified</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($vendor->bank_name || $vendor->bank_account_number)
                                    <div>
                                        <span class="modern-chip" style="font-weight: 600; font-size: 0.75rem; padding: 0.15rem 0.45rem;">
                                            <i class="ti ti-building-bank text-primary"></i> {{ $vendor->bank_name ?: 'Bank Transfer' }}
                                        </span>
                                        @if($vendor->bank_account_number)
                                            <div class="font-mono text-muted" style="font-size: 0.75rem; margin-top: 2px;">{{ $vendor->bank_account_number }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 0.8125rem;">Cash on Delivery</span>
                                @endif
                            </td>
                            <td>
                                <span class="modern-chip" style="font-weight: 600; color: #0284c7; background: rgba(56, 189, 248, 0.1); border-color: rgba(56, 189, 248, 0.25); padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                    <i class="ti ti-calendar"></i> {{ $vendor->payment_terms_days }}d Net
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="modern-chip" style="font-weight: 700; font-size: 0.75rem; padding: 0.2rem 0.55rem;">{{ $vendor->expenses_count }}</span>
                            </td>
                            <td>
                                @if($vendor->is_active)
                                    <span class="status-pill status-paid" style="padding: 0.25rem 0.65rem; font-size: 0.7rem;"><span class="status-dot"></span> Active</span>
                                @else
                                    <span class="status-pill status-draft" style="padding: 0.25rem 0.65rem; font-size: 0.7rem;"><span class="status-dot"></span> Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-wrap">
                                    <div class="empty-icon-orb"><i class="ti ti-building-store"></i></div>
                                    <h4 class="empty-title">No Vendors Registered</h4>
                                    <p class="empty-desc">Register your restaurant's wholesale food suppliers, beverage distributors, and service vendors.</p>
                                    <button type="button" class="btn-gradient-primary" onclick="document.getElementById('add-vendor-modal').style.display='flex'">
                                        <i class="ti ti-plus"></i> Register First Vendor
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-meta">
                    Showing <strong>{{ $vendors->firstItem() }}</strong> to <strong>{{ $vendors->lastItem() }}</strong> of <strong>{{ $vendors->total() }}</strong> vendors
                </div>
                <div>
                    {{ $vendors->links('admin.expenses._pagination') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modern Modal: Register Vendor -->
<div id="add-vendor-modal" class="modern-modal-backdrop" style="display: none;">
    <div class="modern-modal-dialog">
        <form method="POST" action="{{ route('admin.expenses.vendors.store') }}">
            @csrf
            <div class="modern-modal-header">
                <h3 class="modern-modal-title">
                    <i class="ti ti-building-store text-primary"></i> Register Supplier / Vendor
                </h3>
                <button type="button" class="modern-modal-close" id="close-vendor-modal-btn">&times;</button>
            </div>

            <div class="modern-modal-body">
                <div class="form-group-modern">
                    <label class="form-label-modern">Company / Vendor Name *</label>
                    <input type="text" name="name" class="form-control-modern" placeholder="e.g. Premium Meat Wholesaler" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Vendor Code</label>
                        <input type="text" name="code" class="form-control-modern font-mono" placeholder="VND-MEA-01">
                    </div>
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control-modern" placeholder="Ko Thura">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Phone Number</label>
                        <input type="text" name="phone" class="form-control-modern" placeholder="09 799 123 456">
                    </div>
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Email Address</label>
                        <input type="email" name="email" class="form-control-modern" placeholder="billing@supplier.com">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Bank / Digital Wallet (Myanmar)</label>
                        <input type="text" name="bank_name" class="form-control-modern" list="myanmar-banks-list" placeholder="e.g. KBZ Bank / KBZPay">
                        <datalist id="myanmar-banks-list">
                            <option value="KBZ Bank">
                            <option value="KBZPay Merchant">
                            <option value="CB Bank">
                            <option value="AYA Bank">
                            <option value="AYA Pay">
                            <option value="Wave Money / WavePay">
                            <option value="Yoma Bank">
                            <option value="UAB Bank">
                        </datalist>
                    </div>
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Bank Account / Wallet Phone</label>
                        <input type="text" name="bank_account_number" class="form-control-modern font-mono" placeholder="0123-4567-8901">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Payment Terms (Days Net)</label>
                        <input type="number" name="payment_terms_days" class="form-control-modern" value="30" min="0" max="180">
                    </div>
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Tax / Business ID</label>
                        <input type="text" name="tax_number" class="form-control-modern" placeholder="Optional">
                    </div>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Office / Warehouse Address</label>
                    <textarea name="address" class="form-control-modern" rows="2" placeholder="Street, Township, Yangon/Mandalay..."></textarea>
                </div>

                <div class="form-group-modern" style="margin-bottom: 0;">
                    <label class="d-flex align-items-center gap-2" style="cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" checked style="accent-color: var(--primary); width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 0.875rem;">Enable vendor for purchase invoices immediately</span>
                    </label>
                </div>
            </div>

            <div class="modern-modal-footer">
                <button type="button" class="btn-modern-secondary" id="cancel-vendor-modal-btn">Cancel</button>
                <button type="submit" class="btn-gradient-primary">
                    <i class="ti ti-check"></i> Register Vendor
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('add-vendor-modal');
    const openBtn = document.getElementById('open-vendor-modal-btn');
    const closeBtn = document.getElementById('close-vendor-modal-btn');
    const cancelBtn = document.getElementById('cancel-vendor-modal-btn');

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
