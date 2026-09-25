@extends('admin.layouts.app')

@section('title', 'Edit Expense - ' . ($expense->expense_number ?? '#EXP-'.$expense->id))

@push('styles')
@include('admin.expenses._styles')
<style>
    .edit-split-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 1.75rem;
        align-items: flex-start;
    }

    @media (max-width: 1024px) {
        .edit-split-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-section-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.75rem;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02);
        margin-bottom: 1.5rem;
    }

    .section-head {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--border-subtle);
        margin-bottom: 1.25rem;
    }

    .voucher-preview-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.75rem;
        position: sticky;
        top: 90px;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
    }

    .voucher-preview-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #9ec63b, #7ea826);
        border-radius: 20px 20px 0 0;
    }

    .receipt-dropzone-modern {
        border: 2px dashed var(--border-color);
        border-radius: 14px;
        padding: 1.5rem;
        text-align: center;
        background: var(--bg-body);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .receipt-dropzone-modern:hover {
        border-color: var(--primary);
        background: rgba(158, 198, 59, 0.04);
    }
</style>
@endpush

@section('content')
<div class="expense-portal">
    <!-- Sub-Navigation Bar -->
    @include('admin.expenses._nav')

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0; color: var(--text-main);">
                    Edit Expense Voucher: {{ $expense->expense_number }}
                </h1>
                <span class="status-pill status-{{ strtolower($expense->status) }}">
                    <span class="status-dot"></span> {{ $expense->status }}
                </span>
            </div>
            <p class="text-muted m-0" style="font-size: 0.875rem;">Modify item values, adjust receipt attachments, or submit to workflow review.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.expenses.show', $expense) }}" class="btn-modern-secondary">
                <i class="ti ti-arrow-left"></i> Back to Voucher Details
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="glass-card mb-4" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); padding: 1.25rem;">
            <div class="d-flex align-items-center gap-2 text-danger font-bold mb-2">
                <i class="ti ti-alert-circle" style="font-size: 1.25rem;"></i>
                <span>Please fix the following validation errors:</span>
            </div>
            <ul class="m-0 pl-4 text-danger" style="font-size: 0.875rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.expenses.update', $expense) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="edit-split-grid">
            <!-- Left Column: Form Details -->
            <div>
                <!-- Card 1: Core Details -->
                <div class="form-section-card">
                    <div class="section-head">
                        <i class="ti ti-category text-primary"></i> 1. Expense Head & Vendor Details
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">Expense Title *</label>
                        <input type="text" id="form-title" name="title" class="form-control-modern" value="{{ old('title', $expense->title) }}" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="mb-3">
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">Category Head *</label>
                            <select id="form-category" name="category_id" class="form-control-modern form-select-modern" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-name="{{ $category->name }}" data-color="{{ $category->color }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->gl_account_code ?: '5000' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">Supplier / Vendor</label>
                            <select id="form-vendor" name="vendor_id" class="form-control-modern form-select-modern">
                                <option value="" data-name="Local Market / Direct Petty Cash">-- Direct Purchase / Local Market --</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" data-name="{{ $vendor->name }}" {{ old('vendor_id', $expense->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }} (Terms: {{ $vendor->payment_terms_days }} Days Net)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">Disbursement Channel</label>
                            <select id="form-channel" name="payment_method" class="form-control-modern form-select-modern">
                                <option value="CASH" {{ old('payment_method', $expense->payment_method) === 'CASH' ? 'selected' : '' }}>Cash (POS Drawer Disbursement)</option>
                                <option value="BANK_TRANSFER" {{ old('payment_method', $expense->payment_method) === 'BANK_TRANSFER' ? 'selected' : '' }}>Bank Transfer (KBZ / CB / AYA)</option>
                                <option value="KBZPAY" {{ old('payment_method', $expense->payment_method) === 'KBZPAY' ? 'selected' : '' }}>KBZPay Digital Wallet</option>
                                <option value="WAVEPAY" {{ old('payment_method', $expense->payment_method) === 'WAVEPAY' ? 'selected' : '' }}>WavePay Wallet</option>
                                <option value="CREDIT_CARD" {{ old('payment_method', $expense->payment_method) === 'CREDIT_CARD' ? 'selected' : '' }}>Corporate Card</option>
                                <option value="OTHER" {{ old('payment_method', $expense->payment_method) === 'OTHER' ? 'selected' : '' }}>Other Account</option>
                            </select>
                        </div>

                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">Expense Date *</label>
                            <input type="date" id="form-date" name="expense_date" class="form-control-modern" value="{{ old('expense_date', $expense->expense_date?->toDateString()) }}" required>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Financial Amounts -->
                <div class="form-section-card">
                    <div class="section-head">
                        <i class="ti ti-cash text-primary"></i> 2. Financial Breakdown (MMK)
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 1rem;" class="mb-3">
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">Net Amount (MMK) *</label>
                            <input type="number" step="1" id="form-amount" name="amount" class="form-control-modern font-bold" value="{{ old('amount', $expense->amount) }}" min="0" required>
                        </div>

                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">Commercial Tax (MMK)</label>
                            <input type="number" step="1" id="form-tax" name="tax_amount" class="form-control-modern" value="{{ old('tax_amount', $expense->tax_amount ?: 0) }}" min="0">
                        </div>

                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">Payment Due Date</label>
                            <input type="date" id="form-due-date" name="due_date" class="form-control-modern" value="{{ old('due_date', $expense->due_date?->toDateString()) }}">
                        </div>
                    </div>

                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Operational Rationale / Justification</label>
                        <input type="text" name="reason" class="form-control-modern" value="{{ old('reason', $expense->reason) }}">
                    </div>
                </div>

                <!-- Card 3: Attachments & Notes -->
                <div class="form-section-card">
                    <div class="section-head">
                        <i class="ti ti-paperclip text-primary"></i> 3. Voucher Receipt & Remarks
                    </div>

                    @if($expense->receipt_path)
                        <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded" style="background: var(--bg-hover); border: 1px solid var(--border-color);">
                            <i class="ti ti-file-check text-primary" style="font-size: 1.25rem;"></i>
                            <span style="font-size: 0.8125rem;">Current Receipt Attached: <a href="{{ asset('storage/'.$expense->receipt_path) }}" target="_blank" class="font-bold text-primary">View Current File</a></span>
                        </div>
                    @endif

                    <div class="form-group-modern">
                        <label class="form-label-modern">Replace Receipt / Invoice (Optional)</label>
                        <div class="receipt-dropzone-modern" onclick="document.getElementById('receipt-file-input').click()">
                            <i class="ti ti-cloud-upload text-primary" style="font-size: 2rem;"></i>
                            <div class="font-bold text-main mt-1" style="font-size: 0.875rem;" id="dropzone-text">Click to choose replacement file</div>
                            <div class="text-muted" style="font-size: 0.75rem;">JPG, PNG, WEBP, or PDF (Up to 10MB)</div>
                            <input type="file" id="receipt-file-input" name="receipt" style="display: none;" accept="image/jpeg,image/png,image/webp,application/pdf">
                        </div>
                    </div>

                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern">Internal Accounting Remarks</label>
                        <textarea name="notes" class="form-control-modern" rows="2">{{ old('notes', $expense->notes) }}</textarea>
                    </div>
                </div>

                <!-- Resubmission Box -->
                @if(in_array($expense->status, ['DRAFT', 'REJECTED']))
                    <div class="glass-card mb-4" style="background: rgba(158, 198, 59, 0.08); border-color: rgba(158, 198, 59, 0.25);">
                        <label class="d-flex align-items-center gap-3 m-0" style="cursor: pointer;">
                            <input type="checkbox" id="form-submit-now" name="submit_now" value="1" checked style="width: 20px; height: 20px; accent-color: var(--primary);">
                            <div>
                                <div class="font-bold text-main" style="font-size: 0.9375rem;">Submit into Pending Approval Workflow</div>
                                <div class="text-muted" style="font-size: 0.8125rem;">Move to PENDING_APPROVAL for managerial review. Uncheck to remain in DRAFT status.</div>
                            </div>
                        </label>
                    </div>
                @endif

                <!-- Submit Bar -->
                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('admin.expenses.show', $expense) }}" class="btn-modern-secondary">Cancel</a>
                    <button type="submit" class="btn-gradient-primary">
                        <i class="ti ti-check"></i> Update Voucher
                    </button>
                </div>
            </div>

            <!-- Right Column: Interactive Live Voucher Preview Card -->
            <div>
                <div class="voucher-preview-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-receipt text-primary" style="font-size: 1.25rem;"></i>
                            <span style="font-weight: 700; font-size: 0.875rem; text-transform: uppercase;">Updated Preview</span>
                        </div>
                        <span id="preview-status-pill" class="status-pill status-{{ strtolower($expense->status) }}">
                            <span class="status-dot"></span> {{ $expense->status }}
                        </span>
                    </div>

                    <div style="border-bottom: 1px dashed var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">Expense Head</div>
                        <h3 id="preview-title" style="font-size: 1.15rem; font-weight: 700; margin: 0.25rem 0; color: var(--text-main); word-break: break-word;">
                            {{ $expense->title }}
                        </h3>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="modern-chip" id="preview-category-chip">
                                <span class="modern-chip-dot" id="preview-category-dot" style="background-color: {{ $expense->categoryRelation?->color ?: '#9ec63b' }};"></span>
                                <span id="preview-category-name">{{ $expense->category_display_name }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-3" style="font-size: 0.875rem;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Vendor / Payee:</span>
                            <strong id="preview-vendor-name" style="color: var(--text-main);">{{ $expense->vendor?->name ?? 'Direct Local Market' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Disbursement:</span>
                            <span id="preview-channel" class="badge-mono-code">{{ $expense->payment_method }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Recorded Date:</span>
                            <span id="preview-date">{{ $expense->expense_date?->format('d M Y') }}</span>
                        </div>
                    </div>

                    <!-- Financial Total Summary Box -->
                    <div style="background: var(--bg-hover); border: 1px solid var(--border-color); border-radius: 14px; padding: 1rem 1.25rem; margin-top: 1rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted" style="font-size: 0.8125rem;">
                            <span>Net Subtotal:</span>
                            <span id="preview-subtotal" style="font-weight: 600; color: var(--text-main);">{{ number_format($expense->amount, 0) }} MMK</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 text-muted" style="font-size: 0.8125rem;">
                            <span>Commercial Tax:</span>
                            <span id="preview-tax" style="font-weight: 600; color: var(--text-main);">{{ number_format($expense->tax_amount ?: 0, 0) }} MMK</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px solid var(--border-color);">
                            <span style="font-weight: 700; color: var(--text-main); font-size: 0.9375rem;">Total Outflow (MMK):</span>
                            <span id="preview-total" style="font-weight: 800; font-size: 1.35rem; color: #7ea826;">
                                {{ number_format($expense->total_amount ?: $expense->amount, 0) }} MMK
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('form-title');
    const categorySelect = document.getElementById('form-category');
    const vendorSelect = document.getElementById('form-vendor');
    const channelSelect = document.getElementById('form-channel');
    const dateInput = document.getElementById('form-date');
    const amountInput = document.getElementById('form-amount');
    const taxInput = document.getElementById('form-tax');
    const fileInput = document.getElementById('receipt-file-input');
    const dropzoneText = document.getElementById('dropzone-text');

    const previewTitle = document.getElementById('preview-title');
    const previewCatName = document.getElementById('preview-category-name');
    const previewCatDot = document.getElementById('preview-category-dot');
    const previewVendorName = document.getElementById('preview-vendor-name');
    const previewChannel = document.getElementById('preview-channel');
    const previewDate = document.getElementById('preview-date');
    const previewSubtotal = document.getElementById('preview-subtotal');
    const previewTax = document.getElementById('preview-tax');
    const previewTotal = document.getElementById('preview-total');

    function updatePreview() {
        previewTitle.textContent = titleInput.value.trim() || 'Untitled Expense Voucher';

        const selectedCatOpt = categorySelect.options[categorySelect.selectedIndex];
        if (selectedCatOpt && selectedCatOpt.value) {
            previewCatName.textContent = selectedCatOpt.getAttribute('data-name');
            previewCatDot.style.backgroundColor = selectedCatOpt.getAttribute('data-color') || '#9ec63b';
        }

        const selectedVenOpt = vendorSelect.options[vendorSelect.selectedIndex];
        if (selectedVenOpt && selectedVenOpt.value) {
            previewVendorName.textContent = selectedVenOpt.getAttribute('data-name');
        } else {
            previewVendorName.textContent = 'Direct Local Market';
        }

        previewChannel.textContent = channelSelect.value.replace('_', ' ');

        if (dateInput.value) {
            const d = new Date(dateInput.value);
            previewDate.textContent = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        const amount = parseInt(amountInput.value) || 0;
        const tax = parseInt(taxInput.value) || 0;
        const total = amount + tax;

        previewSubtotal.textContent = amount.toLocaleString() + ' MMK';
        previewTax.textContent = tax.toLocaleString() + ' MMK';
        previewTotal.textContent = total.toLocaleString() + ' MMK';
    }

    titleInput.addEventListener('input', updatePreview);
    categorySelect.addEventListener('change', updatePreview);
    vendorSelect.addEventListener('change', updatePreview);
    channelSelect.addEventListener('change', updatePreview);
    dateInput.addEventListener('change', updatePreview);
    amountInput.addEventListener('input', updatePreview);
    taxInput.addEventListener('input', updatePreview);

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                dropzoneText.textContent = 'Selected: ' + this.files[0].name + ' (' + Math.round(this.files[0].size / 1024) + ' KB)';
            }
        });
    }
});
</script>
@endpush
@endsection
