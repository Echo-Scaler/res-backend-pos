@extends('admin.layouts.app')

@section('title', 'Audit Logs & Security Trails')

@push('styles')
<style>
    /* ==========================================================================
       ENTERPRISE AUDIT LOGS MANAGEMENT (MADA TYPOGRAPHY & OBSIDIAN SLATE PALETTE)
       ========================================================================== */
    .audit-portal {
        font-family: "Mada", sans-serif;
        color: var(--text-main);
    }

    .audit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .audit-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        color: var(--text-main);
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .audit-title p {
        font-size: 0.875rem;
        font-weight: 400;
        color: var(--text-muted);
        margin: 0;
    }

    .audit-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .live-pulse-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.85rem;
        background: rgba(158, 198, 59, 0.12);
        border: 1px solid rgba(158, 198, 59, 0.35);
        color: var(--primary);
        font-size: 0.8125rem;
        font-weight: 600;
        border-radius: 9999px;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        background: var(--primary);
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(158, 198, 59, 0.7);
        animation: pulseAnimation 2s infinite;
    }

    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(158, 198, 59, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(158, 198, 59, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(158, 198, 59, 0); }
    }

    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-export:hover {
        background: var(--bg-hover);
        border-color: var(--primary);
        color: var(--primary);
    }

    /* KPI Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .kpi-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.125rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        border-color: rgba(158, 198, 59, 0.4);
    }

    .kpi-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .kpi-icon-total { background: rgba(158, 198, 59, 0.15); color: var(--primary); }
    .kpi-icon-critical { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
    .kpi-icon-void { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
    .kpi-icon-price { background: rgba(13, 202, 240, 0.15); color: #0dcaf0; }

    .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--text-main);
        margin-bottom: 0.2rem;
    }

    .kpi-label {
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* Filter Card */
    .filter-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        align-items: flex-end;
    }

    .form-group-item {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .form-group-item label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .form-control-custom {
        width: 100%;
        padding: 0.55rem 0.85rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-main);
        font-family: "Mada", sans-serif;
        font-size: 0.875rem;
        font-weight: 500;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.15);
    }

    .filter-actions {
        display: flex;
        gap: 0.625rem;
        align-items: center;
    }

    .btn-apply-filter {
        flex: 1;
        padding: 0.55rem 1rem;
        background: var(--primary);
        color: #0b0f17;
        font-size: 0.875rem;
        font-weight: 700;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }

    .btn-apply-filter:hover {
        background: var(--primary-hover);
    }

    .btn-reset-filter {
        padding: 0.55rem 0.85rem;
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-reset-filter:hover {
        color: var(--text-main);
        border-color: var(--text-muted);
    }

    /* Logs Table Card */
    .table-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 2rem;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .audit-table th {
        background: rgba(0, 0, 0, 0.03);
        padding: 0.875rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .audit-table td {
        padding: 0.95rem 1rem;
        font-size: 0.875rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .audit-table tbody tr:hover {
        background: rgba(158, 198, 59, 0.03);
    }

    .audit-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badges & Elements */
    .time-primary {
        font-weight: 600;
        color: var(--text-main);
        display: block;
        font-size: 0.875rem;
    }

    .time-sub {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .staff-name {
        font-weight: 600;
        color: var(--text-main);
        display: block;
    }

    .role-badge {
        display: inline-block;
        padding: 0.15rem 0.5rem;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--border-color);
        font-size: 0.6875rem;
        font-weight: 600;
        border-radius: 4px;
        color: var(--text-muted);
        text-transform: uppercase;
    }

    .action-badge {
        font-family: monospace;
        font-size: 0.8125rem;
        font-weight: 700;
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        display: inline-block;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        color: var(--text-main);
    }

    .category-tag {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        display: block;
        color: var(--text-muted);
        margin-bottom: 0.2rem;
    }

    .severity-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .severity-info {
        background: rgba(13, 202, 240, 0.12);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.3);
    }

    .severity-warning {
        background: rgba(255, 193, 7, 0.12);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .severity-critical {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.4);
        animation: criticalGlow 2s infinite;
    }

    @keyframes criticalGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        50% { box-shadow: 0 0 8px 2px rgba(220, 53, 69, 0.25); }
    }

    .diff-box {
        font-size: 0.8125rem;
        max-width: 280px;
        word-break: break-word;
    }

    .diff-old {
        color: #dc3545;
        text-decoration: line-through;
        font-size: 0.78rem;
    }

    .diff-new {
        color: #198754;
        font-weight: 600;
    }

    .diff-arrow {
        color: var(--text-muted);
        margin: 0 0.25rem;
    }

    .btn-view-detail {
        padding: 0.35rem 0.75rem;
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s ease;
    }

    .btn-view-detail:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: #0b0f17;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3.5rem 1.5rem;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        opacity: 0.6;
    }

    .empty-state h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0 0 0.35rem 0;
        color: var(--text-main);
    }

    .empty-state p {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* Pagination container */
    .pagination-wrap {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    /* Modal Styles */
    .audit-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(11, 15, 23, 0.75);
        backdrop-filter: blur(4px);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .audit-modal-backdrop.show {
        display: flex;
    }

    .audit-modal-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        width: 100%;
        max-width: 650px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .modal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-head h3 {
        font-size: 1.125rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-main);
    }

    .btn-close-modal {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 6px;
    }

    .btn-close-modal:hover {
        color: var(--text-main);
        background: var(--bg-hover);
    }

    .modal-body-content {
        padding: 1.5rem;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .detail-item-title {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.2rem;
    }

    .detail-item-val {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .diff-inspect-panel {
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        font-family: monospace;
        font-size: 0.8125rem;
        white-space: pre-wrap;
        word-break: break-all;
    }
</style>
@endpush

@section('content')
<div class="audit-portal">
    <!-- Header Section -->
    <div class="audit-header">
        <div class="audit-title">
            <h1><i class="ti ti-shield-check" style="color: var(--primary);"></i> Audit Logs & Security Trails</h1>
            <p>Immutable chronological record of staff actions, cashier shifts, order voids, and menu changes (Currency: MMK).</p>
        </div>
        <div class="audit-header-actions">
            <span class="live-pulse-badge">
                <span class="pulse-dot"></span>
                <span>Active Ledger</span>
            </span>
            <a href="{{ route('admin.audit.logs.export', request()->query()) }}" class="btn-export">
                <i class="ti ti-file-spreadsheet"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-wrap kpi-icon-total">
                <i class="ti ti-list-check"></i>
            </div>
            <div>
                <div class="kpi-value">{{ number_format($kpis['total_count']) }}</div>
                <div class="kpi-label">Total Logged Events</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-wrap kpi-icon-critical">
                <i class="ti ti-alert-triangle"></i>
            </div>
            <div>
                <div class="kpi-value" style="color: #dc3545;">{{ number_format($kpis['critical_count']) }}</div>
                <div class="kpi-label">Critical Security Alerts</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-wrap kpi-icon-void">
                <i class="ti ti-receipt-refund"></i>
            </div>
            <div>
                <div class="kpi-value" style="color: #ffc107;">{{ number_format($kpis['voids_count']) }}</div>
                <div class="kpi-label">Order Voids & Refunds</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-wrap kpi-icon-price">
                <i class="ti ti-tag-starred"></i>
            </div>
            <div>
                <div class="kpi-value" style="color: #0dcaf0;">{{ number_format($kpis['price_changes_count']) }}</div>
                <div class="kpi-label">Price & Menu Audits</div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.audit.logs') }}">
            <div class="filter-grid">
                <div class="form-group-item">
                    <label for="date_from">Date From</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control-custom">
                </div>

                <div class="form-group-item">
                    <label for="date_to">Date To</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control-custom">
                </div>

                <div class="form-group-item">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control-custom">
                        <option value="">All Categories</option>
                        <option value="FINANCIAL" {{ ($filters['category'] ?? '') === 'FINANCIAL' ? 'selected' : '' }}>Financial & Cash Register</option>
                        <option value="ORDER_VOID" {{ ($filters['category'] ?? '') === 'ORDER_VOID' ? 'selected' : '' }}>Order & Bill Voids</option>
                        <option value="MENU_PRICE" {{ ($filters['category'] ?? '') === 'MENU_PRICE' ? 'selected' : '' }}>Menu & Price Changes</option>
                        <option value="ACCESS_SECURITY" {{ ($filters['category'] ?? '') === 'ACCESS_SECURITY' ? 'selected' : '' }}>Access & Security</option>
                        <option value="GENERAL" {{ ($filters['category'] ?? '') === 'GENERAL' ? 'selected' : '' }}>General System</option>
                    </select>
                </div>

                <div class="form-group-item">
                    <label for="severity">Severity</label>
                    <select id="severity" name="severity" class="form-control-custom">
                        <option value="">All Severities</option>
                        <option value="INFO" {{ ($filters['severity'] ?? '') === 'INFO' ? 'selected' : '' }}>Info (Routine)</option>
                        <option value="WARNING" {{ ($filters['severity'] ?? '') === 'WARNING' ? 'selected' : '' }}>Warning (Notable)</option>
                        <option value="CRITICAL" {{ ($filters['severity'] ?? '') === 'CRITICAL' ? 'selected' : '' }}>Critical (High Risk)</option>
                    </select>
                </div>

                <div class="form-group-item">
                    <label for="user_id">Staff Member</label>
                    <select id="user_id" name="user_id" class="form-control-custom">
                        <option value="">All Staff</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ ((int) ($filters['user_id'] ?? 0)) === $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-item">
                    <label for="search">Search Keywords</label>
                    <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Action, Order #, IP..." class="form-control-custom">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-apply-filter">
                        <i class="ti ti-filter"></i> Apply
                    </button>
                    <a href="{{ route('admin.audit.logs') }}" class="btn-reset-filter" title="Reset Filters">
                        <i class="ti ti-rotate"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Staff & Role</th>
                        <th>Category & Action</th>
                        <th>Resource Target</th>
                        <th>Details / State Diff</th>
                        <th>IP Address</th>
                        <th>Severity</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <span class="time-primary">{{ $log->created_at?->format('d/m/Y H:i:s') }}</span>
                                <span class="time-sub">{{ $log->created_at?->diffForHumans() }}</span>
                            </td>
                            <td>
                                <span class="staff-name">{{ $log->user_name ?? ($log->user?->name ?? 'System Automated') }}</span>
                                <span class="role-badge">{{ $log->role_name ?? ($log->user?->roles->first()?->name ?? 'N/A') }}</span>
                            </td>
                            <td>
                                <span class="category-tag">{{ $log->category ?? 'GENERAL' }}</span>
                                <span class="action-badge">{{ $log->action }}</span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--text-main);">
                                    {{ $log->resource_type ? $log->resource_type . ($log->resource_id ? ' #' . $log->resource_id : '') : '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="diff-box">
                                    @if($log->old_value && $log->new_value)
                                        <span class="diff-old">{{ Str::limit($log->old_value, 40) }}</span>
                                        <span class="diff-arrow">➔</span>
                                        <span class="diff-new">{{ Str::limit($log->new_value, 40) }}</span>
                                    @elseif($log->new_value)
                                        <span class="diff-new">{{ Str::limit($log->new_value, 60) }}</span>
                                    @elseif($log->old_value)
                                        <span class="diff-old">{{ Str::limit($log->old_value, 60) }}</span>
                                    @else
                                        <span style="color: var(--text-muted);">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-family: monospace; font-size: 0.8125rem; color: var(--text-muted);">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $sev = strtoupper($log->severity ?? 'INFO');
                                    $sevClass = match($sev) {
                                        'CRITICAL' => 'severity-critical',
                                        'WARNING' => 'severity-warning',
                                        default => 'severity-info',
                                    };
                                @endphp
                                <span class="severity-pill {{ $sevClass }}">
                                    @if($sev === 'CRITICAL')
                                        <i class="ti ti-flame"></i>
                                    @elseif($sev === 'WARNING')
                                        <i class="ti ti-alert-circle"></i>
                                    @else
                                        <i class="ti ti-info-circle"></i>
                                    @endif
                                    {{ $sev }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" 
                                        class="btn-view-detail" 
                                        onclick="showAuditDetail('{{ $log->id }}')">
                                    <i class="ti ti-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="ti ti-notes-off"></i></div>
                                    <h3>No Audit Logs Found</h3>
                                    <p>No audit trail records matched your active filter criteria. Try adjusting the dates or clearing filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="pagination-wrap">
                <span style="font-size: 0.875rem; color: var(--text-muted);">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
                </span>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal for Audit Detail -->
<div id="auditDetailModal" class="audit-modal-backdrop">
    <div class="audit-modal-card">
        <div class="modal-head">
            <h3 id="modalLogTitle">Audit Record Detail</h3>
            <button type="button" class="btn-close-modal" onclick="closeAuditModal()">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="modal-body-content" id="modalLogContent">
            <div style="text-align: center; padding: 2rem;">
                <i class="ti ti-loader" style="font-size: 2rem; animation: spin 1s infinite linear;"></i>
                <p>Loading record metadata...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showAuditDetail(logId) {
        const modal = document.getElementById('auditDetailModal');
        const content = document.getElementById('modalLogContent');
        const title = document.getElementById('modalLogTitle');

        modal.classList.add('show');
        title.innerText = `Audit Log #${logId} Details`;
        content.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                <i class="ti ti-loader" style="font-size: 2rem;"></i>
                <p style="margin-top: 0.5rem;">Loading audit record...</p>
            </div>
        `;

        fetch(`/admin/audit-logs/${logId}`)
            .then(res => {
                if (!res.ok) throw new Error('Failed to fetch audit log');
                return res.json();
            })
            .then(res => {
                const data = res.data;
                const metadataJson = data.metadata && Object.keys(data.metadata).length > 0 
                    ? JSON.stringify(data.metadata, null, 2) 
                    : 'None';

                content.innerHTML = `
                    <div class="detail-grid">
                        <div>
                            <div class="detail-item-title">Staff Member</div>
                            <div class="detail-item-val">${data.user_name} (${data.role_name})</div>
                        </div>
                        <div>
                            <div class="detail-item-title">Timestamp</div>
                            <div class="detail-item-val">${data.created_at_formatted}</div>
                        </div>
                        <div>
                            <div class="detail-item-title">Category</div>
                            <div class="detail-item-val">${data.category}</div>
                        </div>
                        <div>
                            <div class="detail-item-title">Action</div>
                            <div class="detail-item-val"><span class="action-badge">${data.action}</span></div>
                        </div>
                        <div>
                            <div class="detail-item-title">Resource Target</div>
                            <div class="detail-item-val">${data.resource_type ? data.resource_type + (data.resource_id ? ' #' + data.resource_id : '') : '—'}</div>
                        </div>
                        <div>
                            <div class="detail-item-title">IP Address</div>
                            <div class="detail-item-val">${data.ip_address}</div>
                        </div>
                    </div>

                    <div style="margin-top: 1rem;">
                        <div class="detail-item-title">State Before Change (Old Value)</div>
                        <div class="diff-inspect-panel" style="color: #dc3545;">${data.old_value || 'None'}</div>
                    </div>

                    <div style="margin-top: 1rem;">
                        <div class="detail-item-title">State After Change (New Value)</div>
                        <div class="diff-inspect-panel" style="color: #198754;">${data.new_value || 'None'}</div>
                    </div>

                    <div style="margin-top: 1rem;">
                        <div class="detail-item-title">Metadata & Security Context (JSON)</div>
                        <div class="diff-inspect-panel" style="color: var(--text-main);">${metadataJson}</div>
                    </div>

                    ${data.user_agent ? `
                    <div style="margin-top: 1rem;">
                        <div class="detail-item-title">Client User Agent</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); word-break: break-all;">${data.user_agent}</div>
                    </div>
                    ` : ''}
                `;
            })
            .catch(err => {
                content.innerHTML = `
                    <div style="padding: 1.5rem; text-align: center; color: #dc3545;">
                        <i class="ti ti-alert-triangle" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem; font-weight: 600;">Failed to load audit record details.</p>
                    </div>
                `;
            });
    }

    function closeAuditModal() {
        document.getElementById('auditDetailModal').classList.remove('show');
    }

    // Close on backdrop click
    document.getElementById('auditDetailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAuditModal();
        }
    });
</script>
@endpush
@endsection
