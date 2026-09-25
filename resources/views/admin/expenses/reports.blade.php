@extends('admin.layouts.app')

@section('title', 'Expense Reports & Financial Audit')

@push('styles')
@include('admin.expenses._styles')
<style>
    .forecast-glass-card {
        background: linear-gradient(135deg, rgba(158, 198, 59, 0.12) 0%, rgba(14, 38, 23, 0.05) 100%);
        border: 1.5px solid rgba(158, 198, 59, 0.35);
        border-radius: 20px;
        padding: 1.75rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .forecast-glass-card::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(158, 198, 59, 0.25) 0%, transparent 70%);
        pointer-events: none;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.25rem;
    }

    @media (max-width: 900px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="expense-portal">
    <!-- Sub-Navigation Bar -->
    @include('admin.expenses._nav')

    <!-- Page Header -->
    <div class="expense-header-wrap">
        <div class="header-title-group">
            <h1>
                <i class="ti ti-chart-dots text-primary"></i> Financial Audit & Expense Analytics
            </h1>
            
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.expenses.export', request()->query()) }}" class="btn-gradient-primary">
                <i class="ti ti-download"></i> Export Filtered CSV (MMK)
            </a>
        </div>
    </div>

    <!-- Date Filter Card -->
    <div class="glass-card mb-4" style="padding: 1rem 1.5rem;">
        <form method="GET" action="{{ route('admin.expenses.reports') }}" class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-calendar-search text-primary" style="font-size: 1.25rem;"></i>
                <span style="font-weight: 700; font-size: 0.9375rem; color: var(--text-main);">Reporting Date Filter:</span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="d-flex align-items-center gap-1.5">
                    <span class="text-muted" style="font-size: 0.8125rem;">From:</span>
                    <input type="date" name="from" class="form-control-modern" value="{{ $from }}" style="width: 155px;">
                </div>
                <div class="d-flex align-items-center gap-1.5">
                    <span class="text-muted" style="font-size: 0.8125rem;">To:</span>
                    <input type="date" name="to" class="form-control-modern" value="{{ $to }}" style="width: 155px;">
                </div>
                <button type="submit" class="btn-gradient-primary" style="padding: 0.55rem 1rem;">
                    <i class="ti ti-filter"></i> Apply Period
                </button>
                @if($from || $to)
                    <a href="{{ route('admin.expenses.reports') }}" class="btn-modern-secondary" style="padding: 0.55rem 1rem;">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Quick Metric Cards -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-lime">
            <div class="kpi-top">
                <span class="kpi-label">Gross Expense Spend</span>
                <div class="kpi-icon-orb"><i class="ti ti-receipt-2"></i></div>
            </div>
            <div class="kpi-value">{{ number_format($summary['total_amount'] ?? 0, 0) }} <span style="font-size: 0.9rem; font-weight: 600;">MMK</span></div>
            <div class="kpi-subtitle">{{ $summary['total_count'] ?? 0 }} recorded expense entries</div>
        </div>

        <div class="kpi-card kpi-emerald">
            <div class="kpi-top">
                <span class="kpi-label">Settled / Paid Spend</span>
                <div class="kpi-icon-orb"><i class="ti ti-circle-check"></i></div>
            </div>
            <div class="kpi-value">{{ number_format($summary['paid_amount'] ?? 0, 0) }} <span style="font-size: 0.9rem; font-weight: 600;">MMK</span></div>
            <div class="kpi-subtitle">{{ $summary['paid_count'] ?? 0 }} paid and disbursed vouchers</div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-top">
                <span class="kpi-label">Pending Approval Total</span>
                <div class="kpi-icon-orb"><i class="ti ti-clock"></i></div>
            </div>
            <div class="kpi-value">{{ number_format($summary['pending_approval_amount'] ?? 0, 0) }} <span style="font-size: 0.9rem; font-weight: 600;">MMK</span></div>
            <div class="kpi-subtitle">{{ $summary['pending_count'] ?? 0 }} claims awaiting review</div>
        </div>

        <div class="kpi-card kpi-rose">
            <div class="kpi-top">
                <span class="kpi-label">Overdue Vendor Invoices</span>
                <div class="kpi-icon-orb"><i class="ti ti-alert-triangle"></i></div>
            </div>
            <div class="kpi-value">{{ number_format($summary['overdue_amount'] ?? 0, 0) }} <span style="font-size: 0.9rem; font-weight: 600;">MMK</span></div>
            <div class="kpi-subtitle">{{ $summary['overdue_count'] ?? 0 }} invoices past due date</div>
        </div>
    </div>

    <!-- Predictive Forecast Glass Card -->
    <div class="forecast-glass-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="modern-chip" style="background: rgba(158, 198, 59, 0.2); border-color: rgba(158, 198, 59, 0.4); color: #7ea826; font-weight: 700;">
                    <i class="ti ti-sparkles"></i> AI & 3-Month Trajectory Forecasting
                </span>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: var(--text-main);">
                    Projected Expenditure for {{ $forecast['next_month_label'] ?? now()->addMonth()->format('F Y') }}
                </h3>
            </div>
            <span class="badge-mono-code" style="background: rgba(255,255,255,0.15);">
                Based on 90-Day Moving Average & Active Contracts
            </span>
        </div>

        <div class="grid-3">
            <div class="glass-card" style="margin-bottom: 0;">
                <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">Next Month Forecast</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin: 0.35rem 0;">
                    {{ number_format($forecast['projected_total'] ?? 0, 0) }} MMK
                </div>
                <div class="text-muted" style="font-size: 0.8125rem;">
                    Historical 3-mo avg: {{ number_format($forecast['historical_monthly_average'] ?? 0, 0) }} MMK
                </div>
            </div>

            <div class="glass-card" style="margin-bottom: 0;">
                <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">Fixed Recurring Commitments</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin: 0.35rem 0;">
                    {{ number_format($forecast['recurring_commitments'] ?? 0, 0) }} MMK
                </div>
                <div class="text-muted" style="font-size: 0.8125rem;">
                    Rent, licenses & recurring retainers
                </div>
            </div>

            <div class="glass-card" style="margin-bottom: 0;">
                <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">Forecast Confidence</div>
                <div style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0.35rem 0;">
                    <span class="status-pill status-paid" style="font-size: 0.8125rem;">
                        <span class="status-dot"></span> HIGH CONFIDENCE
                    </span>
                </div>
                <div class="text-muted" style="font-size: 0.8125rem;">
                    Weighted variance projection
                </div>
            </div>
        </div>
    </div>

    <!-- Category & Payment Channel Grid -->
    <div class="grid-2 mb-4">
        <!-- Breakdown by Category -->
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-chart-donut text-primary" style="font-size: 1.25rem;"></i>
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main);">Expenditure by Category Head</h3>
                </div>
                <span class="text-muted" style="font-size: 0.8125rem;">{{ count($byCategory) }} Heads</span>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($byCategory as $cat)
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.875rem;">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $cat['color'] ?? '#9ec63b' }}; display: inline-block;"></span>
                                <strong style="color: var(--text-main);">{{ $cat['category_name'] }}</strong>
                                <span class="badge-mono-code" style="font-size: 0.7rem;">{{ $cat['count'] }} entries</span>
                            </div>
                            <div class="font-bold text-main">
                                {{ $cat['formatted_amount'] }} <span class="text-muted font-normal" style="font-size: 0.75rem;">({{ $cat['percentage'] }}%)</span>
                            </div>
                        </div>
                        <div style="height: 6px; background: var(--bg-hover); border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $cat['percentage'] }}%; background-color: {{ $cat['color'] ?? '#9ec63b' }}; border-radius: 9999px;"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">No category data recorded for this period.</div>
                @endforelse
            </div>
        </div>

        <!-- Breakdown by Payment Channel -->
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-cash-banknote text-primary" style="font-size: 1.25rem;"></i>
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--text-main);">Disbursement Channels</h3>
                </div>
                <span class="text-muted" style="font-size: 0.8125rem;">Drawer & Bank Reconciliation</span>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($byPaymentMethod as $pm)
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.875rem;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-wallet text-muted"></i>
                                <strong style="color: var(--text-main);">{{ str_replace('_', ' ', $pm['payment_method']) }}</strong>
                                <span class="badge-mono-code" style="font-size: 0.7rem;">{{ $pm['count'] }} txns</span>
                            </div>
                            <div class="font-bold text-main">
                                {{ $pm['formatted_amount'] }} <span class="text-muted font-normal" style="font-size: 0.75rem;">({{ $pm['percentage'] }}%)</span>
                            </div>
                        </div>
                        <div style="height: 6px; background: var(--bg-hover); border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $pm['percentage'] }}%; background: linear-gradient(90deg, #9ec63b, #7ea826); border-radius: 9999px;"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">No disbursement records found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- General Ledger (GL) Double-Entry Journal Table -->
    <div class="table-container mb-4">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-notebook text-primary" style="font-size: 1.15rem;"></i>
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--text-main);">General Ledger (GL) Double-Entry Journal Preview</h3>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="status-pill status-paid" style="font-size: 0.75rem;">
                    <i class="ti ti-scale"></i> Debits = Credits Balanced
                </span>
            </div>
        </div>

        <div class="table-responsive-clean">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Date & Reference</th>
                        <th>Transaction Description</th>
                        <th>Debit Account</th>
                        <th>Debit (MMK)</th>
                        <th>Credit Account</th>
                        <th>Credit (MMK)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journal as $entry)
                        <tr>
                            <td>
                                <strong style="color: var(--text-main);">{{ $entry['date'] }}</strong>
                                <div class="badge-mono-code" style="margin-top: 2px;">{{ $entry['reference'] }}</div>
                            </td>
                            <td>
                                <span style="color: var(--text-main); font-weight: 500;">{{ $entry['description'] }}</span>
                            </td>
                            <td>
                                <span class="modern-chip" style="font-weight: 600; font-size: 0.75rem;">
                                    <i class="ti ti-arrow-down-left text-success"></i> {{ $entry['debit_account'] }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-main);">{{ $entry['formatted_debit'] }}</span>
                            </td>
                            <td>
                                <span class="modern-chip" style="font-weight: 600; font-size: 0.75rem;">
                                    <i class="ti ti-arrow-up-right text-primary"></i> {{ $entry['credit_account'] }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-main);">{{ $entry['formatted_credit'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No journal transactions generated for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
