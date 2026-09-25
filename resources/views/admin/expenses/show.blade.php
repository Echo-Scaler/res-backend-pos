@extends('admin.layouts.app')

@section('title', 'Expense ' . ($expense->expense_number ?? '#'.$expense->id))

@push('styles')
@include('admin.expenses._styles')
<style>
    .expense-detail-portal {
        font-family: "Mada", sans-serif;
        color: var(--text-main);
    }

    .detail-header-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        gap: 1.25rem;
    }

    .header-tag-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .header-tag-group h1 {
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin: 0;
        color: var(--text-main);
    }

    /* Stepper track with glowing indicators */
    .stepper-glass-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .stepper-line-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .step-node {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        min-width: 100px;
        position: relative;
    }

    .step-orb {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--bg-body);
        border: 2px solid var(--border-color);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .step-node.completed .step-orb {
        background: linear-gradient(135deg, #9ec63b 0%, #7ea826 100%);
        border-color: #9ec63b;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(158, 198, 59, 0.4);
    }

    .step-node.rejected .step-orb {
        background: #ef4444;
        border-color: #ef4444;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);
    }

    .step-node.void .step-orb {
        background: #64748b;
        border-color: #64748b;
        color: #ffffff;
    }

    .step-label {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .step-meta {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }

    .step-connector {
        flex: 1;
        height: 3px;
        background: var(--border-color);
        margin: 0 0.5rem;
        margin-bottom: 1.75rem;
        border-radius: 9999px;
    }

    .step-connector.active {
        background: linear-gradient(90deg, #9ec63b, #7ea826);
    }

    .step-connector.rejected {
        background: #ef4444;
    }

    /* Bento-style main layout */
    .bento-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 992px) {
        .bento-grid {
            grid-template-columns: 1fr;
        }
    }

    .bento-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    /* Financial highlight box */
    .fin-highlight-box {
        background: linear-gradient(135deg, rgba(158, 198, 59, 0.08) 0%, rgba(14, 38, 23, 0.04) 100%);
        border: 1px solid rgba(158, 198, 59, 0.25);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .fin-cols {
        display: grid;
        grid-template-columns: 1fr 1fr 1.2fr;
        gap: 1.25rem;
    }

    @media (max-width: 600px) {
        .fin-cols {
            grid-template-columns: 1fr;
        }
    }

    .fin-col-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .fin-col-val {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .fin-col-val.total {
        font-size: 1.625rem;
        color: #7ea826;
    }

    /* Info Table Rows */
    .info-list-group {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .info-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.55rem;
        border-bottom: 1px dashed var(--border-subtle);
        font-size: 0.875rem;
    }

    .info-line:last-child {
        border-bottom: none;
    }

    .info-key {
        color: var(--text-muted);
        font-weight: 500;
    }

    .info-data {
        color: var(--text-main);
        font-weight: 600;
        text-align: right;
    }

    /* Audit Stream */
    .audit-stream {
        position: relative;
        padding-left: 24px;
    }

    .audit-stream::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: var(--border-color);
    }

    .audit-entry {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .audit-marker {
        position: absolute;
        left: -24px;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #9ec63b;
        border: 3px solid var(--bg-card);
        box-shadow: 0 0 0 1px var(--primary);
    }

    .audit-content-card {
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.75rem 1rem;
    }
</style>
@endpush

@section('content')
<div class="expense-detail-portal">
    <!-- Sub-Navigation Bar -->
    @include('admin.expenses._nav')

    <!-- Top Action Bar -->
    <div class="detail-header-wrap">
        <div>
            <div class="header-tag-group">
                <h1>{{ $expense->expense_number }}</h1>
                @php
                    $statusConfig = match($expense->status) {
                        'PAID' => ['class' => 'status-paid', 'label' => 'Paid & Disbursed'],
                        'APPROVED' => ['class' => 'status-approved', 'label' => 'Approved'],
                        'PENDING_APPROVAL' => ['class' => 'status-pending', 'label' => 'Pending Review'],
                        'REJECTED' => ['class' => 'status-rejected', 'label' => 'Rejected'],
                        'VOID' => ['class' => 'status-void', 'label' => 'Voided'],
                        default => ['class' => 'status-draft', 'label' => 'Draft'],
                    };
                @endphp
                <span class="status-pill {{ $statusConfig['class'] }}">
                    <span class="status-dot"></span>
                    {{ $statusConfig['label'] }}
                </span>
                @if($expense->isOverdue())
                    <span class="overdue-pill">OVERDUE</span>
                @endif
            </div>
            <p class="text-muted m-0 mt-1" style="font-size: 0.875rem;">
                Created by <strong>{{ $expense->creator?->name ?? 'System' }}</strong> on {{ $expense->created_at?->format('d M Y, h:i A') }}
            </p>
        </div>

        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('admin.expenses.index') }}" class="btn-modern-secondary">
                <i class="ti ti-arrow-left"></i> All Expenses
            </a>

            @if($expense->isDraft() || $expense->isRejected())
                <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn-modern-secondary">
                    <i class="ti ti-edit"></i> Edit Details
                </a>
                <form method="POST" action="{{ route('admin.expenses.submit', $expense) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-gradient-primary">
                        <i class="ti ti-send"></i> Submit for Approval
                    </button>
                </form>
            @endif

            @if($expense->isPendingApproval())
                <button type="button" class="btn-action-emerald" data-modal="approve-modal">
                    <i class="ti ti-check"></i> Approve Expense
                </button>
                <button type="button" class="btn-action-rose" data-modal="reject-modal">
                    <i class="ti ti-x"></i> Reject
                </button>
            @endif

            @if($expense->isApproved() && ! $expense->isPaid())
                <button type="button" class="btn-gradient-primary" data-modal="pay-modal">
                    <i class="ti ti-cash"></i> Record Disbursement
                </button>
            @endif

            @if(! $expense->isVoid())
                <button type="button" class="btn-action-slate" data-modal="void-modal">
                    <i class="ti ti-ban"></i> Void
                </button>
            @endif
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success mb-4 d-flex align-items-center gap-2">
            <i class="ti ti-circle-check"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning mb-4 d-flex align-items-center gap-2">
            <i class="ti ti-alert-triangle"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    <!-- Glowing Stepper Workflow Track -->
    <div class="stepper-glass-card">
        <div class="stepper-line-wrap">
            <div class="step-node {{ in_array($expense->status, ['DRAFT', 'PENDING_APPROVAL', 'APPROVED', 'PAYMENT_PENDING', 'PAID']) ? 'completed' : '' }}">
                <div class="step-orb"><i class="ti ti-file-text"></i></div>
                <div class="step-label">Draft Created</div>
                <div class="step-meta">{{ $expense->created_at?->format('d M') }}</div>
            </div>

            <div class="step-connector {{ in_array($expense->status, ['PENDING_APPROVAL', 'APPROVED', 'PAYMENT_PENDING', 'PAID']) ? 'active' : '' }}"></div>

            <div class="step-node {{ in_array($expense->status, ['PENDING_APPROVAL', 'APPROVED', 'PAYMENT_PENDING', 'PAID']) ? 'completed' : '' }}">
                <div class="step-orb"><i class="ti ti-send"></i></div>
                <div class="step-label">Submitted</div>
                <div class="step-meta">{{ $expense->isDraft() ? 'Pending' : 'Done' }}</div>
            </div>

            <div class="step-connector {{ in_array($expense->status, ['APPROVED', 'PAYMENT_PENDING', 'PAID']) ? 'active' : '' }}"></div>

            <div class="step-node {{ in_array($expense->status, ['APPROVED', 'PAYMENT_PENDING', 'PAID']) ? 'completed' : '' }}">
                <div class="step-orb"><i class="ti ti-check"></i></div>
                <div class="step-label">Approved</div>
                <div class="step-meta">{{ $expense->approved_at?->format('d M') ?? ($expense->isPendingApproval() ? 'Awaiting' : '-') }}</div>
            </div>

            <div class="step-connector {{ $expense->isPaid() ? 'active' : '' }}"></div>

            <div class="step-node {{ $expense->isPaid() ? 'completed' : '' }}">
                <div class="step-orb"><i class="ti ti-cash"></i></div>
                <div class="step-label">Paid / Settled</div>
                <div class="step-meta">{{ $expense->paid_at?->format('d M') ?? 'Unpaid' }}</div>
            </div>

            @if($expense->isRejected())
                <div class="step-connector rejected"></div>
                <div class="step-node rejected">
                    <div class="step-orb"><i class="ti ti-x"></i></div>
                    <div class="step-label">Rejected</div>
                    <div class="step-meta">{{ $expense->rejected_at?->format('d M') }}</div>
                </div>
            @endif

            @if($expense->isVoid())
                <div class="step-connector"></div>
                <div class="step-node void">
                    <div class="step-orb"><i class="ti ti-ban"></i></div>
                    <div class="step-label">Voided</div>
                    <div class="step-meta">{{ $expense->voided_at?->format('d M') }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Bento Grid -->
    <div class="bento-grid">
        <!-- Left: Core Information & Cash Register Ledger -->
        <div>
            <!-- Financial Card -->
            <div class="bento-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">{{ $expense->title }}</h2>
                    <span class="cat-chip">
                        <span class="cat-dot" style="background-color: {{ $expense->categoryRelation?->color ?: '#9ec63b' }};"></span>
                        {{ $expense->category_display_name }} (GL: {{ $expense->categoryRelation?->gl_account_code ?? '5001' }})
                    </span>
                </div>

                <!-- Financial Highlight Box (Rule #6 MMK) -->
                <div class="fin-highlight-box">
                    <div class="fin-cols">
                        <div>
                            <div class="fin-col-label">Net Subtotal</div>
                            <div class="fin-col-val">{{ number_format($expense->amount, 0) }} MMK</div>
                        </div>
                        <div>
                            <div class="fin-col-label">Commercial Tax</div>
                            <div class="fin-col-val">{{ number_format($expense->tax_amount, 0) }} MMK</div>
                        </div>
                        <div>
                            <div class="fin-col-label">Total Outflow (MMK)</div>
                            <div class="fin-col-val total">{{ number_format($expense->total_amount ?: $expense->amount, 0) }} MMK</div>
                        </div>
                    </div>
                </div>

                <div class="info-list-group">
                    <div class="info-line">
                        <span class="info-key">Expense Date:</span>
                        <span class="info-data">{{ $expense->expense_date?->format('d F Y') }}</span>
                    </div>
                    <div class="info-line">
                        <span class="info-key">Due Date:</span>
                        <span class="info-data {{ $expense->isOverdue() ? 'text-danger font-bold' : '' }}">
                            {{ $expense->due_date?->format('d F Y') ?? 'Immediate Cash Payment' }}
                        </span>
                    </div>
                    @if($expense->reason)
                        <div class="info-line">
                            <span class="info-key">Business Reason:</span>
                            <span class="info-data">{{ $expense->reason }}</span>
                        </div>
                    @endif
                    @if($expense->notes)
                        <div class="info-line">
                            <span class="info-key">Internal Remarks:</span>
                            <span class="info-data">{{ $expense->notes }}</span>
                        </div>
                    @endif
                    @if($expense->rejection_reason)
                        <div class="info-line text-danger">
                            <span class="info-key">Rejection Reason:</span>
                            <span class="info-data text-danger">{{ $expense->rejection_reason }} (by {{ $expense->rejecter?->name }})</span>
                        </div>
                    @endif
                    @if($expense->void_reason)
                        <div class="info-line text-muted">
                            <span class="info-key">Void Reason:</span>
                            <span class="info-data">{{ $expense->void_reason }} (by {{ $expense->voider?->name }})</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cash Drawer / Bank Account Integration -->
            <div class="bento-card mb-4" id="payment-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ti ti-cash-register text-primary"></i> POS Cash Drawer & Bank Ledger
                    </h3>
                    @if($expense->isPaid())
                        <span class="status-pill status-paid"><span class="status-dot"></span> Disbursed</span>
                    @else
                        <span class="status-pill status-pending"><span class="status-dot"></span> Awaiting Payment</span>
                    @endif
                </div>

                @if($expense->transactions->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Disbursement Account</th>
                                    <th>Method</th>
                                    <th>Txn Ref</th>
                                    <th class="text-right">Amount (MMK)</th>
                                    <th>Status</th>
                                    <th>Disbursed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expense->transactions as $txn)
                                    <tr>
                                        <td class="font-bold">{{ $txn->account_or_drawer_name }}</td>
                                        <td><span class="badge" style="background: var(--bg-hover);">{{ $txn->payment_method }}</span></td>
                                        <td class="font-mono text-muted" style="font-size: 0.8125rem;">{{ $txn->reference_no ?? '-' }}</td>
                                        <td class="font-bold text-right">{{ number_format($txn->amount, 0) }} MMK</td>
                                        <td>
                                            <span class="status-pill {{ $txn->status === 'SUCCESS' ? 'status-paid' : 'status-rejected' }}">
                                                <span class="status-dot"></span> {{ $txn->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted" style="font-size: 0.8125rem;">{{ $txn->created_at?->format('d M Y, h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-3 text-center text-muted" style="background: var(--bg-hover); border-radius: 12px;">
                        <i class="ti ti-wallet mb-1" style="font-size: 1.75rem;"></i>
                        <p class="m-0" style="font-size: 0.875rem;">No payment transactions recorded yet. Once approved, click "Record Disbursement" to synchronize with the register drawer.</p>
                    </div>
                @endif
            </div>

            <!-- Audit Trail Timeline -->
            <div class="bento-card">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ti ti-history text-muted"></i> Immutable Audit Log Trail
                </h3>

                <div class="audit-stream">
                    @forelse($auditLogs as $log)
                        <div class="audit-entry">
                            <div class="audit-marker"></div>
                            <div class="audit-content-card">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="font-bold text-main" style="font-size: 0.875rem;">{{ str_replace('_', ' ', $log->action) }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at?->format('d M Y, h:i:s A') }}</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.8125rem;">
                                    User: <strong class="text-main">{{ $log->user_name ?? 'System' }}</strong> (IP: {{ $log->ip_address }})
                                </div>
                                @if($log->new_value)
                                    <div class="mt-1 font-mono text-muted" style="font-size: 0.75rem; background: var(--bg-card); padding: 4px 8px; border-radius: 6px;">
                                        {{ $log->new_value }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted m-0" style="font-size: 0.875rem;">No audit records available.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Vendor & Receipt Attachment Preview -->
        <div>
            <!-- Vendor Card -->
            <div class="bento-card mb-4">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ti ti-truck-delivery text-muted"></i> Vendor Profile
                </h3>

                @if($expense->vendor)
                    <div>
                        <div class="font-bold text-main" style="font-size: 1.1rem;">{{ $expense->vendor->name }}</div>
                        <div class="text-muted mb-3 font-mono" style="font-size: 0.8125rem;">Code: {{ $expense->vendor->code ?? 'VND-'.$expense->vendor->id }}</div>

                        <div class="info-list-group">
                            <div class="info-line">
                                <span class="info-key">Contact Person:</span>
                                <span class="info-data">{{ $expense->vendor->contact_person ?? '-' }}</span>
                            </div>
                            <div class="info-line">
                                <span class="info-key">Phone:</span>
                                <span class="info-data">{{ $expense->vendor->phone ?? '-' }}</span>
                            </div>
                            <div class="info-line">
                                <span class="info-key">Bank Name:</span>
                                <span class="info-data">{{ $expense->vendor->bank_name ?? '-' }}</span>
                            </div>
                            <div class="info-line">
                                <span class="info-key">Account No:</span>
                                <span class="info-data font-mono">{{ $expense->vendor->bank_account_number ?? '-' }}</span>
                            </div>
                            <div class="info-line">
                                <span class="info-key">Payment Terms:</span>
                                <span class="info-data">{{ $expense->vendor->payment_terms_days }} Days Net</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-3 text-center text-muted" style="background: var(--bg-hover); border-radius: 10px;">
                        <i class="ti ti-shopping-cart mb-1" style="font-size: 1.75rem;"></i>
                        <p class="m-0" style="font-size: 0.875rem;">Direct Cash Market Purchase (No vendor contract required).</p>
                    </div>
                @endif
            </div>

            <!-- Receipt Card -->
            <div class="bento-card">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ti ti-paperclip text-muted"></i> Voucher Attachment
                </h3>

                @if($expense->receipt_path)
                    <div class="text-center">
                        @php
                            $ext = pathinfo($expense->receipt_path, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                            <a href="{{ asset('storage/'.$expense->receipt_path) }}" target="_blank">
                                <img src="{{ asset('storage/'.$expense->receipt_path) }}" alt="Receipt" class="img-fluid rounded mb-3" style="max-height: 250px; border: 1.5px solid var(--border-color); object-fit: contain; width: 100%;">
                            </a>
                        @else
                            <div class="p-4 text-center mb-3 rounded" style="background: var(--bg-hover);">
                                <i class="ti ti-file-text" style="font-size: 3rem; color: #dc2626;"></i>
                                <div class="mt-2 font-bold">Attached PDF Document</div>
                            </div>
                        @endif

                        <a href="{{ asset('storage/'.$expense->receipt_path) }}" target="_blank" class="btn-modern-secondary w-100 justify-content-center">
                            <i class="ti ti-external-link"></i> Open Original Document
                        </a>
                    </div>
                @else
                    <div class="p-4 text-center text-muted" style="background: var(--bg-hover); border-radius: 12px;">
                        <i class="ti ti-photo-off mb-1" style="font-size: 2rem;"></i>
                        <p class="m-0" style="font-size: 0.875rem;">No photo or invoice voucher attached.</p>
                        @if($expense->isDraft())
                            <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn-gradient-primary btn-sm mt-3">Upload Voucher</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ================== MODAL DIALOGS ================== -->

<!-- Modal: Approve Expense -->
<div id="approve-modal" class="modern-modal-backdrop" style="display: none;">
    <div class="modern-modal-dialog">
        <form method="POST" action="{{ route('admin.expenses.approve', $expense) }}">
            @csrf
            <div class="modern-modal-header">
                <h3 class="modern-modal-title"><i class="ti ti-check text-success"></i> Approve Expense Voucher</h3>
                <button type="button" class="modern-modal-close close-modal"><i class="ti ti-x"></i></button>
            </div>
            <div class="modern-modal-body">
                <p style="font-size: 0.95rem; color: var(--text-main); margin-bottom: 1.25rem;">
                    Are you sure you want to approve <strong>{{ $expense->expense_number }}</strong> ({{ number_format($expense->total_amount ?: $expense->amount, 0) }} MMK)?
                </p>
                <div class="form-group-modern">
                    <label class="form-label-modern">Approval Remarks (Optional)</label>
                    <textarea name="notes" class="form-control-modern" rows="3" placeholder="e.g. Verified with kitchen supply list"></textarea>
                </div>
            </div>
            <div class="modern-modal-footer">
                <button type="button" class="btn-modern-secondary close-modal">Cancel</button>
                <button type="submit" class="btn-action-emerald font-bold"><i class="ti ti-check"></i> Confirm Approval</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reject Expense -->
<div id="reject-modal" class="modern-modal-backdrop" style="display: none;">
    <div class="modern-modal-dialog">
        <form method="POST" action="{{ route('admin.expenses.reject', $expense) }}">
            @csrf
            <div class="modern-modal-header">
                <h3 class="modern-modal-title text-danger"><i class="ti ti-x text-danger"></i> Reject Expense</h3>
                <button type="button" class="modern-modal-close close-modal"><i class="ti ti-x"></i></button>
            </div>
            <div class="modern-modal-body">
                <p style="font-size: 0.95rem; color: var(--text-main); margin-bottom: 1.25rem;">
                    Provide a detailed reason why expense <strong>{{ $expense->expense_number }}</strong> is rejected.
                </p>
                <div class="form-group-modern">
                    <label class="form-label-modern">Rejection Reason *</label>
                    <textarea name="reason" class="form-control-modern" rows="3" placeholder="e.g. Missing supplier receipt or over-budget" required></textarea>
                </div>
            </div>
            <div class="modern-modal-footer">
                <button type="button" class="btn-modern-secondary close-modal">Cancel</button>
                <button type="submit" class="btn-action-rose font-bold"><i class="ti ti-x"></i> Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Record Payment -->
<div id="pay-modal" class="modern-modal-backdrop" style="display: none;">
    <div class="modern-modal-dialog">
        <form method="POST" action="{{ route('admin.expenses.pay', $expense) }}">
            @csrf
            <div class="modern-modal-header">
                <h3 class="modern-modal-title"><i class="ti ti-cash text-primary"></i> Record Disbursement Outflow</h3>
                <button type="button" class="modern-modal-close close-modal"><i class="ti ti-x"></i></button>
            </div>
            <div class="modern-modal-body">
                <div class="modal-summary-card">
                    <div class="font-bold text-main" style="font-size: 1.15rem;">Payable Total: {{ number_format($expense->total_amount ?: $expense->amount, 0) }} MMK</div>
                    <div class="text-muted" style="font-size: 0.8125rem;">Vendor: {{ $expense->vendor?->name ?? 'Direct Local Market' }}</div>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Disbursement Channel *</label>
                    <select name="payment_method" class="form-control-modern form-select-modern" required id="modal_pay_method">
                        <option value="CASH" {{ $expense->payment_method === 'CASH' ? 'selected' : '' }}>Cash Drawer (POS Counter Register)</option>
                        <option value="BANK_TRANSFER" {{ $expense->payment_method === 'BANK_TRANSFER' ? 'selected' : '' }}>Bank Transfer (Corporate Account)</option>
                        <option value="KBZPAY" {{ $expense->payment_method === 'KBZPAY' ? 'selected' : '' }}>KBZPay Merchant Account</option>
                        <option value="WAVEPAY" {{ $expense->payment_method === 'WAVEPAY' ? 'selected' : '' }}>WavePay Wallet</option>
                        <option value="CREDIT_CARD" {{ $expense->payment_method === 'CREDIT_CARD' ? 'selected' : '' }}>Credit / Debit Card</option>
                    </select>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Drawer / Bank Account Name</label>
                    <input type="text" name="account_or_drawer_name" class="form-control-modern" id="modal_account_name" value="Main POS Cash Register Drawer #1">
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Slip / Txn Reference No</label>
                    <input type="text" name="payment_reference" class="form-control-modern" placeholder="e.g. TXN-84920492 or Cash Voucher #">
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Disbursement Date</label>
                    <input type="date" name="payment_date" class="form-control-modern" value="{{ now()->toDateString() }}">
                </div>
            </div>
            <div class="modern-modal-footer">
                <button type="button" class="btn-modern-secondary close-modal">Cancel</button>
                <button type="submit" class="btn-gradient-primary font-bold"><i class="ti ti-cash"></i> Confirm Disbursement</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Void Expense -->
<div id="void-modal" class="modern-modal-backdrop" style="display: none;">
    <div class="modern-modal-dialog">
        <form method="POST" action="{{ route('admin.expenses.void', $expense) }}">
            @csrf
            <div class="modern-modal-header">
                <h3 class="modern-modal-title text-danger"><i class="ti ti-ban text-danger"></i> Void Expense Transaction</h3>
                <button type="button" class="modern-modal-close close-modal"><i class="ti ti-x"></i></button>
            </div>
            <div class="modern-modal-body">
                <div class="modal-alert-box">
                    <i class="ti ti-alert-triangle"></i>
                    <div>
                        <strong>Permanent Action:</strong> Voiding will reverse linked register transactions and create an immutable audit record.
                    </div>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">Audit Void Reason *</label>
                    <textarea name="reason" class="form-control-modern" rows="3" placeholder="Provide detailed justification for voiding this voucher..." required></textarea>
                </div>
            </div>
            <div class="modern-modal-footer">
                <button type="button" class="btn-modern-secondary close-modal">Cancel</button>
                <button type="submit" class="btn-action-rose font-bold"><i class="ti ti-ban"></i> Confirm Void</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-modal]').forEach(btn => {
        btn.addEventListener('click', function () {
            const modalId = this.getAttribute('data-modal');
            const target = document.getElementById(modalId);
            if (target) target.style.display = 'flex';
        });
    });

    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('.modern-modal-backdrop').style.display = 'none';
        });
    });

    window.addEventListener('click', function (e) {
        if (e.target.classList.contains('modern-modal-backdrop')) {
            e.target.style.display = 'none';
        }
    });

    const payMethodSelect = document.getElementById('modal_pay_method');
    const accountInput = document.getElementById('modal_account_name');
    if (payMethodSelect && accountInput) {
        payMethodSelect.addEventListener('change', function () {
            if (this.value === 'CASH') {
                accountInput.value = 'Main POS Cash Register Drawer #1';
            } else if (this.value === 'KBZPAY' || this.value === 'WAVEPAY') {
                accountInput.value = this.value + ' Merchant Account';
            } else {
                accountInput.value = 'Corporate Bank Account';
            }
        });
    }
});
</script>
@endsection
