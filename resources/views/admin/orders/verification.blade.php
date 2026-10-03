@extends('admin.layouts.app')

@section('title', 'Table Orders & Slip Reproduction')

@push('styles')
<style>
    .expediter-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .expediter-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .expediter-title p {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin: 0.25rem 0 0 0;
    }

    .live-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #22c55e;
        display: inline-block;
        box-shadow: 0 0 8px #22c55e;
        animation: pulseLive 2s infinite;
    }

    @keyframes pulseLive {
        0% { transform: scale(0.95); opacity: 0.8; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(0.95); opacity: 0.8; }
    }

    /* KPI Metrics Grid */
    .expediter-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .expediter-kpi-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--shadow-sm);
    }

    .exp-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .exp-icon-total { background: rgba(158, 198, 59, 0.15); color: var(--primary); }
    .exp-icon-pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .exp-icon-cooking { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .exp-icon-ready { background: rgba(168, 85, 247, 0.15); color: #a855f7; }
    .exp-icon-served { background: rgba(34, 197, 94, 0.15); color: #22c55e; }

    /* Orders Grid */
    .orders-expediter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.25rem;
    }

    .order-pass-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow-sm);
        transition: border-color 0.2s, transform 0.2s;
        position: relative;
    }

    .order-pass-card:hover {
        border-color: var(--primary);
    }

    .order-pass-card.new-order-glow {
        animation: cardGlow 1.5s ease-out;
    }

    @keyframes cardGlow {
        0% { border-color: #22c55e; box-shadow: 0 0 15px rgba(34, 197, 94, 0.5); }
        100% { border-color: var(--border-color); box-shadow: var(--shadow-sm); }
    }

    .order-pass-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 0.85rem;
    }

    .order-table-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .order-id-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
    }

    .reprint-badge-tag {
        font-size: 0.7rem;
        font-weight: 800;
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.35rem;
    }

    .badge-k-status {
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .k-status-pending_cook { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .k-status-cooking { background: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); }
    .k-status-ready_for_delivery { background: rgba(168, 85, 247, 0.15); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.3); }
    .k-status-served_to_table { background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); }

    /* Clean Items List (No Checkboxes) */
    .order-items-list-clean {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        margin: 0.75rem 0 1rem 0;
    }

    .item-list-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 0.55rem 0.75rem;
        background-color: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        gap: 0.6rem;
    }

    .item-qty-tag {
        font-weight: 800;
        color: var(--primary);
        font-size: 0.95rem;
        min-width: 24px;
        line-height: 1.2;
    }

    .item-dish-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main);
        line-height: 1.3;
    }

    .special-note-pill {
        font-size: 0.7rem;
        font-weight: 600;
        background: rgba(245, 158, 11, 0.15);
        color: #d97706;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        display: inline-block;
        margin-top: 0.25rem;
    }

    .item-price-tag {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--text-muted);
        white-space: nowrap;
    }

    /* Order Card Summary & Action Buttons */
    .order-card-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid var(--border-color);
        padding-top: 0.85rem;
        margin-top: 0.5rem;
        gap: 0.5rem;
    }

    .order-total-metric {
        display: flex;
        flex-direction: column;
    }

    .card-print-buttons {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .btn-pass-action {
        padding: 0.45rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background-color: var(--bg-body);
        color: var(--text-main);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s;
    }

    .btn-pass-action:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(158, 198, 59, 0.08);
    }

    .btn-icon-print {
        padding: 0.45rem 0.65rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-body);
        color: var(--text-main);
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s;
    }

    .btn-icon-print:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(158, 198, 59, 0.08);
    }

    /* Modal Backdrop & Dialog (Rule #5 Modern Theme) */
    .modal-backdrop-custom {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }

    .modal-backdrop-custom.active {
        display: flex;
    }

    .modal-dialog-custom {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65);
        overflow: hidden;
        animation: modalSlideUp 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes modalSlideUp {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .modal-header-custom {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        background: var(--bg-card);
    }

    .modal-header-title-box h3 {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-header-title-box span {
        font-size: 0.8125rem;
        color: var(--text-muted);
        display: block;
        margin-top: 0.25rem;
    }

    .modal-close-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .modal-close-btn:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.3);
    }

    .modal-body-custom {
        padding: 1.25rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
        max-height: calc(85vh - 140px);
        overflow-y: auto;
    }

    .reprint-audit-notice {
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #d97706;
        padding: 0.85rem 1rem;
        border-radius: 10px;
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        font-size: 0.8125rem;
        line-height: 1.45;
    }

    .reprint-audit-notice code {
        font-family: monospace;
        font-weight: 700;
        background: rgba(0, 0, 0, 0.25);
        padding: 0.15rem 0.35rem;
        border-radius: 4px;
        color: #f59e0b;
    }

    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-label-custom {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .reprint-option-card {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: var(--bg-body);
        user-select: none;
    }

    .reprint-option-card:hover {
        border-color: var(--primary);
        background-color: rgba(158, 198, 59, 0.05);
    }

    .reprint-option-card.selected {
        border-color: var(--primary);
        background-color: rgba(158, 198, 59, 0.12);
    }

    .reprint-option-card input[type="radio"] {
        accent-color: var(--primary);
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .reprint-card-icon {
        font-size: 1.35rem;
        color: var(--primary);
        line-height: 1;
    }

    .reprint-card-text {
        flex: 1;
    }

    .reprint-card-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .reprint-card-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }

    .form-control-custom {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background-color: var(--bg-body);
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .form-control-custom:focus {
        border-color: var(--primary);
    }

    .modal-footer-custom {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        background: var(--bg-card);
    }

    /* Toast Notification (No Refresh) */
    .pos-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #111724;
        border: 1px solid #22c55e;
        color: #f1f5f9;
        padding: 0.85rem 1.25rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        z-index: 10000;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    .pos-toast.active {
        transform: translateY(0);
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <!-- Header -->
    <div class="expediter-header">
        <div class="expediter-title">
            <h1>
                <i class="ti ti-receipt-2 text-primary"></i> Kitchen Pass &amp; Order Verification
                <span class="live-status-pill">
                    <span class="live-dot"></span> Live Sync (No Refresh)
                </span>
            </h1>
            <p>Monitor table orders and reprint receipt slips seamlessly without page refresh</p>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <button type="button" class="btn-modern-primary" id="btnSimulateOrder" onclick="simulateOrderAjax(this)" style="font-size: 0.8125rem; padding: 0.5rem 1rem;">
                <i class="ti ti-bolt"></i> Simulate Test Order
            </button>
            <a href="{{ route('admin.tables.index') }}" class="btn-modern-secondary" style="font-size: 0.8125rem; padding: 0.5rem 0.85rem;">
                <i class="ti ti-table"></i> Floor Tables
            </a>
            <button type="button" class="btn-modern-secondary" onclick="fetchOrdersAjax(true)" style="font-size: 0.8125rem; padding: 0.5rem 0.85rem;">
                <i class="ti ti-refresh" id="refreshSpinIcon"></i> Sync Now
            </button>
        </div>
    </div>

    <!-- Expediter KPI Cards -->
    <div class="expediter-kpi-grid">
        <div class="expediter-kpi-card">
            <div class="exp-icon exp-icon-total"><i class="ti ti-tools-kitchen-2"></i></div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Active Orders</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);" id="kpiTotalActive">{{ $metrics['total_active'] }}</div>
            </div>
        </div>
        <div class="expediter-kpi-card">
            <div class="exp-icon exp-icon-pending"><i class="ti ti-clock"></i></div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Pending Cook</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #f59e0b;" id="kpiPendingCook">{{ $metrics['pending_cook'] }}</div>
            </div>
        </div>
        <div class="expediter-kpi-card">
            <div class="exp-icon exp-icon-cooking"><i class="ti ti-flame"></i></div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Cooking on Line</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #3b82f6;" id="kpiCooking">{{ $metrics['cooking'] }}</div>
            </div>
        </div>
        <div class="expediter-kpi-card">
            <div class="exp-icon exp-icon-ready"><i class="ti ti-bell-ringing"></i></div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Ready to Carry</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #a855f7;" id="kpiReady">{{ $metrics['ready_for_delivery'] }}</div>
            </div>
        </div>
        <div class="expediter-kpi-card">
            <div class="exp-icon exp-icon-served"><i class="ti ti-circle-check"></i></div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Delivered / Dining</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #22c55e;" id="kpiServed">{{ $metrics['served'] }}</div>
            </div>
        </div>
    </div>

    <!-- Active Orders Grid Container -->
    <div id="ordersGridContainer">
        @if($orders->isEmpty())
            <div class="empty-state-box" id="emptyStateBox" style="background: var(--bg-card); border: 1px dashed var(--border-color); border-radius: 14px; padding: 3rem 1.5rem; text-align: center; color: var(--text-muted);">
                <i class="ti ti-receipt-off" style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem; opacity: 0.5;"></i>
                <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.35rem;">No Active Orders in Kitchen Pass</h4>
                <p style="font-size: 0.875rem; margin-bottom: 1.25rem;">Orders submitted from table QR codes appear here automatically, or generate a test order right now.</p>
                <button type="button" class="btn-modern-primary" onclick="simulateOrderAjax(this)" style="font-size: 0.875rem; padding: 0.65rem 1.35rem;">
                    <i class="ti ti-bolt"></i> Generate Test Order for Table T-01
                </button>
            </div>
        @else
            <div class="orders-expediter-grid" id="ordersGrid">
                @foreach($orders as $order)
                    <div class="order-pass-card" id="order-card-{{ $order->id }}">
                        <div>
                            <div class="order-pass-header">
                                <div>
                                    <div class="order-table-title">
                                        <i class="ti ti-table text-primary"></i> TABLE {{ $order->table_number ?? 'Counter' }}
                                    </div>
                                    <div class="order-id-meta">
                                        #{{ $order->order_number }} • {{ $order->created_at->diffForHumans() }}
                                    </div>
                                    @if($order->reprint_count > 0)
                                        <div class="reprint-badge-tag">
                                            <i class="ti ti-printer"></i> REPRINT #{{ $order->reprint_count }} (DUPLICATE)
                                        </div>
                                    @endif
                                </div>
                                <span class="badge-k-status k-status-{{ strtolower($order->kitchen_status) }}">
                                    {{ str_replace('_', ' ', $order->kitchen_status) }}
                                </span>
                            </div>

                            <!-- Dishes List (Clean presentation without checkboxes) -->
                            <div class="order-items-list-clean">
                                @foreach($order->items as $item)
                                    <div class="item-list-row">
                                        <div class="item-qty-tag">{{ $item->quantity }}x</div>
                                        <div style="flex: 1;">
                                            <div class="item-dish-name">{{ $item->item_name }}</div>
                                            @if($item->special_notes)
                                                <span class="special-note-pill">
                                                    <i class="ti ti-note"></i> {{ $item->special_notes }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="item-price-tag">
                                            {{ number_format($item->subtotal, 0) }} MMK
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Card Summary & Quick Print Buttons -->
                        <div class="order-card-summary">
                            <div class="order-total-metric">
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Total:</span>
                                <span style="font-size: 1.05rem; font-weight: 800; color: #9ec63b;">{{ number_format($order->total_amount, 0) }} MMK</span>
                            </div>

                            <div class="card-print-buttons">
                                <button type="button" class="btn-pass-action btn-reprint-slip" 
                                    data-id="{{ $order->id }}"
                                    data-number="{{ $order->order_number }}"
                                    data-table="{{ $order->table_number }}"
                                    data-reprint-url="{{ route('admin.orders.reprint', $order) }}"
                                    data-count="{{ $order->reprint_count }}">
                                    <i class="ti ti-printer"></i> Reprint
                                </button>
                                <a href="{{ route('admin.orders.printKitchenChit', $order) }}" target="_blank" class="btn-icon-print" title="Print Kitchen Chit">
                                    <i class="ti ti-tools-kitchen-2"></i> Chit
                                </a>
                                <a href="{{ route('admin.orders.printCustomerBill', $order) }}" target="_blank" class="btn-icon-print" title="Print Bill Slip">
                                    <i class="ti ti-receipt"></i> Bill
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Reprint Slip Modal -->
<div class="modal-backdrop-custom" id="reprintModal">
    <div class="modal-dialog-custom">
        <form id="reprintSlipForm" method="POST">
            @csrf
            <div class="modal-header-custom">
                <div class="modal-header-title-box">
                    <h3 id="reprintModalTitle"><i class="ti ti-printer text-primary"></i> Reproduce / Reprint Slip</h3>
                    <span id="reprintModalSubtitle">Re-issue paper slip for kitchen or guest check</span>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeReprintModal()" title="Close"><i class="ti ti-x"></i></button>
            </div>
            <div class="modal-body-custom">
                <div class="reprint-audit-notice">
                    <i class="ti ti-shield-alert" style="font-size: 1.25rem; flex-shrink: 0; margin-top: 0.1rem;"></i>
                    <div>
                        <strong>Reprint Audit Tracking:</strong> All re-issued slips will be stamped with <code>*** REPRINT #N (DUPLICATE) ***</code> and logged into the executive audit trail with your employee credentials.
                    </div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Select Slip to Reproduce *</label>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label class="reprint-option-card selected" onclick="selectSlipRadio(this)">
                            <input type="radio" name="slip_type" value="KITCHEN_CHIT" checked>
                            <span class="reprint-card-icon"><i class="ti ti-tools-kitchen-2"></i></span>
                            <div class="reprint-card-text">
                                <div class="reprint-card-title">Slip 1: Kitchen Order Chit</div>
                                <div class="reprint-card-desc">調理指示伝票 • Item quantities and cooking notes for chefs (No prices)</div>
                            </div>
                        </label>

                        <label class="reprint-option-card" onclick="selectSlipRadio(this)">
                            <input type="radio" name="slip_type" value="CUSTOMER_BILL">
                            <span class="reprint-card-icon"><i class="ti ti-receipt"></i></span>
                            <div class="reprint-card-text">
                                <div class="reprint-card-title">Slip 2: Customer Bill Slip</div>
                                <div class="reprint-card-desc">会計伝票 • Guest check with itemized MMK prices, taxes & counter checkout barcode</div>
                            </div>
                        </label>

                        <label class="reprint-option-card" onclick="selectSlipRadio(this)">
                            <input type="radio" name="slip_type" value="BOTH">
                            <span class="reprint-card-icon"><i class="ti ti-copy"></i></span>
                            <div class="reprint-card-text">
                                <div class="reprint-card-title">Both Slips (Continuous Dual Roll)</div>
                                <div class="reprint-card-desc">Prints Kitchen Chit + Customer Bill continuously with perforation cut guide</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom" for="reprint_reason">Reason for Reproduction (Audit Trail) *</label>
                    <select name="reason" id="reprint_reason" class="form-control-custom" required>
                        <option value="ပရင်တာ စက္ကူကုန်သွား၍ (Printer Paper Jam / Out)">ပရင်တာ စက္ကူကုန်သွား၍ (Printer Paper Jam / Out)</option>
                        <option value="စလစ် ပျောက်ဆုံးခြင်း သို့မဟုတ် ရေစိုသွားခြင်း (Slip Lost or Wet)">စလစ် ပျောက်ဆုံးခြင်း သို့မဟုတ် ရေစိုသွားခြင်း (Slip Lost or Wet)</option>
                        <option value="ဧည့်သည် စလစ်မိတ္တူထပ်မံတောင်းခံခြင်း (Customer Requested Copy)">ဧည့်သည် စလစ်မိတ္တူထပ်မံတောင်းခံခြင်း (Customer Requested Copy)</option>
                        <option value="မီးဖိုချောင် ပြန်လည်စစ်ဆေးရန် (Kitchen Pass Verification)">မီးဖိုချောင် ပြန်လည်စစ်ဆေးရန် (Kitchen Pass Verification)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-modern-secondary" onclick="closeReprintModal()">Cancel</button>
                <button type="submit" class="btn-modern-primary" id="btnSubmitReprint">
                    <i class="ti ti-printer"></i> Reproduce Slip Now
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Async Toast Notification Container -->
<div class="pos-toast" id="posToast">
    <i class="ti ti-circle-check" style="color: #22c55e; font-size: 1.25rem;"></i>
    <span id="posToastMessage">Action completed successfully.</span>
</div>
@endsection

@push('scripts')
<script>
    let currentReprintOrderId = null;

    // Toast helper
    function showToast(message) {
        const toast = document.getElementById('posToast');
        document.getElementById('posToastMessage').innerText = message;
        toast.classList.add('active');
        setTimeout(() => toast.classList.remove('active'), 3500);
    }

    // Modal Radio Selection
    function selectSlipRadio(cardEl) {
        document.querySelectorAll('.reprint-option-card').forEach(el => el.classList.remove('selected'));
        cardEl.classList.add('selected');
        const radio = cardEl.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    function openReprintModal(url, orderNum, tableNum, count, orderId) {
        currentReprintOrderId = orderId;
        document.getElementById('reprintSlipForm').action = url;
        document.getElementById('reprintModalTitle').innerHTML = '<i class="ti ti-printer text-primary"></i> Reproduce Slip: Table ' + tableNum;
        document.getElementById('reprintModalSubtitle').innerText = 'Order #' + orderNum + ' • Previous Reprints: ' + count;
        document.getElementById('reprintModal').classList.add('active');
    }

    function closeReprintModal() {
        document.getElementById('reprintModal').classList.remove('active');
    }

    // Delegate click on Reprint button
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-reprint-slip');
        if (btn) {
            openReprintModal(
                btn.dataset.reprintUrl,
                btn.dataset.number,
                btn.dataset.table,
                btn.dataset.count,
                btn.dataset.id
            );
        }
    });

    // Close on outside click or Escape
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop-custom')) {
            closeReprintModal();
        }
    });

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReprintModal();
        }
    });

    // 1. ASYNC REPRINT FORM SUBMIT (NO PAGE REFRESH)
    document.getElementById('reprintSlipForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const url = form.action;
        const submitBtn = document.getElementById('btnSubmitReprint');
        const originalHtml = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Processing...';

        const formData = new FormData(form);

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            closeReprintModal();
            showToast(data.message || 'Slip reproduced successfully!');

            // Open thermal print window asynchronously
            if (data.target_url) {
                window.open(data.target_url, '_blank');
            }

            // Update reprint count badge in DOM without reloading page
            if (currentReprintOrderId) {
                const card = document.getElementById('order-card-' + currentReprintOrderId);
                if (card) {
                    let badge = card.querySelector('.reprint-badge-tag');
                    if (!badge) {
                        badge = document.createElement('div');
                        badge.className = 'reprint-badge-tag';
                        card.querySelector('.order-id-meta').after(badge);
                    }
                    badge.innerHTML = '<i class="ti ti-printer"></i> REPRINT #' + (data.reprint_count || 1) + ' (DUPLICATE)';
                    const reprintBtn = card.querySelector('.btn-reprint-slip');
                    if (reprintBtn) {
                        reprintBtn.dataset.count = data.reprint_count;
                    }
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to reprint slip. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        });
    });

    // 2. ASYNC SIMULATE ORDER (NO PAGE REFRESH)
    function simulateOrderAjax(btn) {
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Creating...';

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('admin.orders.simulate') }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.order) {
                showToast(data.message || 'Live order created successfully!');
                prependOrderToGrid(data.order);
                incrementKpiActive();
            } else {
                alert(data.message || 'Could not simulate order.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error simulating order.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // Prepend order card into grid
    function prependOrderToGrid(order) {
        let grid = document.getElementById('ordersGrid');
        const emptyState = document.getElementById('emptyStateBox');

        if (!grid) {
            if (emptyState) emptyState.remove();
            grid = document.createElement('div');
            grid.className = 'orders-expediter-grid';
            grid.id = 'ordersGrid';
            document.getElementById('ordersGridContainer').appendChild(grid);
        }

        const card = document.createElement('div');
        card.className = 'order-pass-card new-order-glow';
        card.id = 'order-card-' + order.id;

        let itemsHtml = '';
        (order.items || []).forEach(item => {
            itemsHtml += `
                <div class="item-list-row">
                    <div class="item-qty-tag">${item.quantity}x</div>
                    <div style="flex: 1;">
                        <div class="item-dish-name">${item.item_name}</div>
                        ${item.special_notes ? `<span class="special-note-pill"><i class="ti ti-note"></i> ${item.special_notes}</span>` : ''}
                    </div>
                    <div class="item-price-tag">${item.formatted_subtotal || (item.subtotal + ' MMK')}</div>
                </div>
            `;
        });

        card.innerHTML = `
            <div>
                <div class="order-pass-header">
                    <div>
                        <div class="order-table-title">
                            <i class="ti ti-table text-primary"></i> TABLE ${order.table_number || 'Counter'}
                        </div>
                        <div class="order-id-meta">
                            #${order.order_number} • Just now
                        </div>
                    </div>
                    <span class="badge-k-status k-status-pending_cook">PENDING COOK</span>
                </div>
                <div class="order-items-list-clean">
                    ${itemsHtml}
                </div>
            </div>
            <div class="order-card-summary">
                <div class="order-total-metric">
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Total:</span>
                    <span style="font-size: 1.05rem; font-weight: 800; color: #9ec63b;">${order.formatted_total || (order.total_amount + ' MMK')}</span>
                </div>
                <div class="card-print-buttons">
                    <button type="button" class="btn-pass-action btn-reprint-slip" 
                        data-id="${order.id}"
                        data-number="${order.order_number}"
                        data-table="${order.table_number}"
                        data-reprint-url="/admin/orders/${order.id}/reprint"
                        data-count="0">
                        <i class="ti ti-printer"></i> Reprint
                    </button>
                    <a href="/admin/orders/${order.id}/print-kitchen-chit" target="_blank" class="btn-icon-print" title="Print Kitchen Chit">
                        <i class="ti ti-tools-kitchen-2"></i> Chit
                    </a>
                    <a href="/admin/orders/${order.id}/print-customer-bill" target="_blank" class="btn-icon-print" title="Print Bill Slip">
                        <i class="ti ti-receipt"></i> Bill
                    </a>
                </div>
            </div>
        `;

        grid.prepend(card);
    }

    function incrementKpiActive() {
        const total = document.getElementById('kpiTotalActive');
        const pending = document.getElementById('kpiPendingCook');
        if (total) total.innerText = parseInt(total.innerText || '0', 10) + 1;
        if (pending) pending.innerText = parseInt(pending.innerText || '0', 10) + 1;
    }

    // 3. BACKGROUND LIVE POLLING (AUTO-SYNC WITHOUT FULL PAGE REFRESH)
    function fetchOrdersAjax(manual = false) {
        const spin = document.getElementById('refreshSpinIcon');
        if (spin && manual) spin.classList.add('ti-spin');

        fetch("{{ route('admin.orders.index') }}?view=active", {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.metrics) {
                if (document.getElementById('kpiTotalActive')) document.getElementById('kpiTotalActive').innerText = data.metrics.total_active;
                if (document.getElementById('kpiPendingCook')) document.getElementById('kpiPendingCook').innerText = data.metrics.pending_cook;
                if (document.getElementById('kpiCooking')) document.getElementById('kpiCooking').innerText = data.metrics.cooking;
                if (document.getElementById('kpiReady')) document.getElementById('kpiReady').innerText = data.metrics.ready_for_delivery;
                if (document.getElementById('kpiServed')) document.getElementById('kpiServed').innerText = data.metrics.served;
            }

            if (data.orders) {
                // If any order in data.orders is not yet in the DOM, prepend it
                data.orders.forEach(order => {
                    if (!document.getElementById('order-card-' + order.id)) {
                        prependOrderToGrid(order);
                        showToast('New Order #' + order.order_number + ' for Table ' + order.table_number + ' received!');
                    }
                });
            }

            if (manual) showToast('Live orders synchronized.');
        })
        .catch(err => {
            console.warn('Sync error:', err);
        })
        .finally(() => {
            if (spin && manual) spin.classList.remove('ti-spin');
        });
    }

    // Auto poll every 6 seconds in background
    setInterval(() => {
        fetchOrdersAjax(false);
    }, 6000);
</script>
@endpush
