@extends('admin.layouts.app')

@section('title', 'Cashier POS Checkout Register')

@push('styles')
<style>
    /* Real POS Register Theme & Styles */
    :root {
        --pos-accent: #9ec63b;
        --pos-emerald: #10b981;
        --pos-amber: #f59e0b;
        --pos-rose: #f43f5e;
        --pos-cyan: #06b6d4;
    }

    body, .role-title, .stat-value, .btn, input, select, table {
        font-family: "Mada", sans-serif !important;
    }

    .pos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    .role-title {
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #f8fafc;
        margin: 0;
    }

    .role-badge-pill {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        background: rgba(158, 198, 59, 0.15);
        color: #9ec63b;
        border: 1px solid rgba(158, 198, 59, 0.35);
    }

    .shift-status-badge {
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.4rem 0.9rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .shift-status-badge.open {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .shift-status-badge.closed {
        background: rgba(244, 63, 94, 0.15);
        color: #fb7185;
        border: 1px solid rgba(244, 63, 94, 0.35);
    }

    /* Shift Alert & Actions Bar */
    .shift-bar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .shift-actions {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .btn-pos {
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.55rem 1rem;
        border-radius: 8px;
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }

    .btn-pos-primary {
        background: #9ec63b;
        color: #0b0f17;
        font-weight: 700;
    }
    .btn-pos-primary:hover {
        background: #b2db48;
        transform: translateY(-1px);
    }

    .btn-pos-emerald {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.4);
    }
    .btn-pos-emerald:hover {
        background: rgba(16, 185, 129, 0.25);
    }

    .btn-pos-amber {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.4);
    }
    .btn-pos-amber:hover {
        background: rgba(245, 158, 11, 0.25);
    }

    .btn-pos-rose {
        background: rgba(244, 63, 94, 0.15);
        color: #fb7185;
        border-color: rgba(244, 63, 94, 0.4);
    }
    .btn-pos-rose:hover {
        background: rgba(244, 63, 94, 0.25);
    }

    .btn-pos-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: #cbd5e1;
        border-color: var(--border);
    }
    .btn-pos-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        border-color: rgba(158, 198, 59, 0.4);
        transform: translateY(-2px);
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.4rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #f8fafc;
    }

    .stat-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.3rem;
    }

    /* POS Layout Container */
    .pos-main-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 1024px) {
        .pos-main-container {
            grid-template-columns: 1fr;
        }
    }

    .panel-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.5rem;
    }

    .panel-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #f8fafc;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Billing Table */
    .billing-table {
        width: 100%;
        border-collapse: collapse;
    }

    .billing-table th {
        text-align: left;
        padding: 0.75rem 0.6rem;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        border-bottom: 1px solid var(--border);
    }

    .billing-table td {
        padding: 1rem 0.6rem;
        border-bottom: 1px solid rgba(34, 45, 66, 0.6);
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .table-num-pill {
        font-weight: 700;
        color: #f8fafc;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .badge-status {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-billing {
        background: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.4);
        animation: pulseBilling 2s infinite ease-in-out;
    }

    @keyframes pulseBilling {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.03); }
    }

    .badge-occupied {
        background: rgba(249, 115, 22, 0.15);
        color: #fb923c;
        border: 1px solid rgba(249, 115, 22, 0.35);
    }

    .btn-settle-action {
        background: #9ec63b;
        color: #0b0f17;
        font-weight: 700;
        padding: 0.5rem 0.9rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 0.8125rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }

    .btn-settle-action:hover {
        background: #b2db48;
        transform: translateY(-1px);
    }

    /* Modal Backdrop and Box */
    .pos-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(11, 15, 23, 0.85);
        backdrop-filter: blur(6px);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .pos-modal-backdrop.show {
        display: flex;
    }

    .pos-modal-box {
        background: #161e2e;
        border: 1px solid #222d42;
        border-radius: 16px;
        width: 100%;
        max-width: 850px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 1.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
    }

    .modal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid #222d42;
        margin-bottom: 1.5rem;
    }

    .modal-head h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #f8fafc;
    }

    .btn-close-modal {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
    }
    .btn-close-modal:hover {
        color: #fff;
    }

    /* Checkout Modal Layout */
    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
    }

    .order-items-box {
        background: rgba(11, 15, 23, 0.6);
        border: 1px solid #222d42;
        border-radius: 10px;
        padding: 1rem;
        max-height: 240px;
        overflow-y: auto;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.8125rem;
        padding: 0.5rem 0;
        border-bottom: 1px dashed rgba(34, 45, 66, 0.7);
    }

    .item-row:last-child {
        border-bottom: none;
    }

    .calc-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .calc-row.total-row {
        border-top: 1px solid #222d42;
        margin-top: 0.5rem;
        padding-top: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #9ec63b;
    }

    .payment-tab-btn {
        flex: 1;
        padding: 0.6rem;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 600;
        border: 1px solid #222d42;
        background: #111724;
        color: #94a3b8;
        cursor: pointer;
        text-align: center;
    }

    .payment-tab-btn.active {
        background: #9ec63b;
        color: #0b0f17;
        font-weight: 700;
        border-color: #9ec63b;
    }

    .quick-cash-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        margin-top: 0.6rem;
    }

    .btn-quick-cash {
        background: rgba(34, 45, 66, 0.5);
        border: 1px solid #222d42;
        border-radius: 6px;
        color: #f1f5f9;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.4rem;
        cursor: pointer;
        text-align: center;
    }
    .btn-quick-cash:hover {
        background: rgba(158, 198, 59, 0.2);
        border-color: #9ec63b;
    }

    .change-alert-box {
        margin-top: 0.85rem;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9375rem;
        font-weight: 700;
    }

    .change-alert-box.green {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .change-alert-box.red {
        background: rgba(244, 63, 94, 0.15);
        color: #fb7185;
        border: 1px solid rgba(244, 63, 94, 0.35);
    }

    .input-pos {
        width: 100%;
        background: #0b0f17;
        border: 1px solid #222d42;
        border-radius: 8px;
        color: #f1f5f9;
        padding: 0.6rem 0.8rem;
        font-size: 0.875rem;
        box-sizing: border-box;
    }

    .input-pos:focus {
        outline: none;
        border-color: #9ec63b;
    }

    .form-group-pos {
        margin-bottom: 1rem;
    }

    .form-group-pos label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
</style>
@endpush

@section('content')
<div class="pos-header">
    <div>
        <h1 class="role-title">
            <span>💵 Cashier Register & Checkout Portal</span>
            <span class="role-badge-pill">CASHIER</span>
        </h1>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0.3rem 0 0;">
            Counter checkout, cash drawer management & bill settlement for <strong>{{ $restaurant->name ?? 'Restaurant' }}</strong>
        </p>
    </div>
    <div>
        @if ($activeSession)
            <span class="shift-status-badge open">
                <i class="ti ti-lock-open"></i> Shift Open ({{ $activeSession->terminal_code }})
            </span>
        @else
            <span class="shift-status-badge closed">
                <i class="ti ti-lock"></i> Shift Closed
            </span>
        @endif
    </div>
</div>

{{-- Shift Operations Bar --}}
<div class="shift-bar">
    <div>
        @if ($activeSession)
            <span style="font-weight: 600; color: #f8fafc; font-size: 0.875rem;">
                <i class="ti ti-device-desktop"></i> Terminal: <strong>{{ $activeSession->terminal_code }}</strong> &bull;
                Opened: {{ $activeSession->opened_at->format('d M, h:i A') }}
            </span>
        @else
            <span style="font-weight: 600; color: #fb7185; font-size: 0.875rem;">
                <i class="ti ti-alert-triangle"></i> အံဆွဲဖွင့်လှစ်ထားခြင်း မရှိသေးပါ (Shift is Closed) &bull; ကျေးဇူးပြု၍ Shift အသစ်ဖွင့်ပြီး Float ထည့်သွင်းပါ။
            </span>
        @endif
    </div>
    <div class="shift-actions">
        @if (! $activeSession)
            <button type="button" class="btn-pos btn-pos-primary" onclick="openShiftModal()">
                <i class="ti ti-key"></i> Open Cash Drawer Shift
            </button>
        @else
            <button type="button" class="btn-pos btn-pos-emerald" onclick="openCashModal('CASH_IN')">
                <i class="ti ti-arrow-down-left"></i> Cash In (ငွေသွင်း)
            </button>
            <button type="button" class="btn-pos btn-pos-amber" onclick="openCashModal('CASH_OUT')">
                <i class="ti ti-arrow-up-right"></i> Cash Out (ငွေထုတ်)
            </button>
            <button type="button" class="btn-pos btn-pos-rose" onclick="openCloseShiftModal()">
                <i class="ti ti-lock"></i> End Shift & Z-Report
            </button>
        @endif
    </div>
</div>

{{-- Stats KPI Grid --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Drawer Session</div>
        <div class="stat-value" style="color: {{ $activeSession ? '#34d399' : '#fb7185' }}; font-size: 1.3rem;">
            {{ $register['status'] }}
        </div>
        <div class="stat-desc">Terminal: {{ $register['terminal_id'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Opening Float (MMK)</div>
        <div class="stat-value">{{ $register['opening_balance'] }}</div>
        <div class="stat-desc">Cash float at shift start</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Cash Collected (MMK)</div>
        <div class="stat-value" style="color: #34d399;">{{ $register['cash_sales'] }}</div>
        <div class="stat-desc">Physical cash tendered</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Digital Sales (MMK)</div>
        <div class="stat-value" style="color: #67e8f9;">{{ $register['digital_sales'] }}</div>
        <div class="stat-desc">KBZPay / WavePay / Card</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Expected Drawer Cash (MMK)</div>
        <div class="stat-value" style="color: #9ec63b;">{{ $register['expected_cash'] }}</div>
        <div class="stat-desc">Float + Cash Sales + In - Out</div>
    </div>
</div>

{{-- Main POS Panels --}}
<div class="pos-main-container">
    {{-- Active Billing Tables Panel --}}
    <div class="panel-card">
        <div class="panel-title">
            <span>
                <i class="ti ti-receipt"></i> Tables Awaiting Payment Settlement
                @if ($register['pending_billing_count'] > 0)
                    <span class="badge-status badge-billing" style="margin-left: 0.5rem;">
                        {{ $register['pending_billing_count'] }} Call Bill
                    </span>
                @endif
            </span>
            <span style="font-size: 0.8125rem; color: var(--text-muted); font-weight: normal;">
                {{ $activeTables->count() }} Active Floor Tables
            </span>
        </div>

        @if ($activeTables->isEmpty())
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                <i class="ti ti-coffee" style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem; opacity: 0.5;"></i>
                <p style="font-size: 0.9375rem; font-weight: 600; margin: 0;">လက်ရှိအချိန်တွင် ငွေရှင်းရန် ကျန်ရှိသော စားပွဲ မရှိသေးပါ။</p>
                <p style="font-size: 0.8125rem; margin-top: 0.25rem;">All tables are vacant or settled.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="billing-table">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th>Area</th>
                            <th>Order #</th>
                            <th>Items / Pax</th>
                            <th>Total Due (MMK)</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activeTables as $table)
                            @php
                                $order = $table->active_order;
                            @endphp
                            <tr>
                                <td>
                                    <div class="table-num-pill">
                                        <i class="ti ti-armchair" style="color: #9ec63b;"></i>
                                        <strong>{{ $table->table_number }}</strong>
                                    </div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $table->name }}</span>
                                </td>
                                <td>{{ $table->floor_area ?? 'Main Hall' }}</td>
                                <td>
                                    @if ($order)
                                        <code style="color: #67e8f9; font-size: 0.8125rem;">{{ $order->order_number }}</code>
                                    @else
                                        <span style="color: var(--text-muted);">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($order)
                                        {{ $order->items->count() }} Items ({{ $order->guest_count }} Pax)
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($order)
                                        <strong style="color: #9ec63b; font-size: 0.95rem;">
                                            {{ number_format($order->total_amount, 0) }} MMK
                                        </strong>
                                    @else
                                        0 MMK
                                    @endif
                                </td>
                                <td>
                                    @if ($table->status === 'BILLING')
                                        <span class="badge-status badge-billing">
                                            <i class="ti ti-bell-ringing"></i> Bill Requested
                                        </span>
                                    @else
                                        <span class="badge-status badge-occupied">
                                            Occupied (Dining)
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if ($order)
                                        <button type="button" class="btn-settle-action" onclick="openCheckoutModal({{ $order->id }})">
                                            <i class="ti ti-cash"></i> Settle Bill
                                        </button>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.75rem;">No Order</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Side Panel: Quick Payment Channels & Recent Orders --}}
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        {{-- Quick Pay Channels Card --}}
        <div class="panel-card">
            <div class="panel-title">
                <span><i class="ti ti-wallet"></i> Quick Pay Channels</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: rgba(11, 15, 23, 0.5); border: 1px solid var(--border); border-radius: 8px;">
                    <span style="font-weight: 600; font-size: 0.875rem;"><i class="ti ti-cash" style="color: #34d399;"></i> Cash (မြန်မာကျပ်ငွေ)</span>
                    <span style="color: #34d399; font-size: 0.75rem; font-weight: 700;">Ready</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: rgba(11, 15, 23, 0.5); border: 1px solid var(--border); border-radius: 8px;">
                    <span style="font-weight: 600; font-size: 0.875rem;"><i class="ti ti-qrcode" style="color: #38bdf8;"></i> KBZPay QR</span>
                    <span style="color: #38bdf8; font-size: 0.75rem; font-weight: 700;">Online</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: rgba(11, 15, 23, 0.5); border: 1px solid var(--border); border-radius: 8px;">
                    <span style="font-weight: 600; font-size: 0.875rem;"><i class="ti ti-device-mobile" style="color: #fbbf24;"></i> WavePay QR</span>
                    <span style="color: #fbbf24; font-size: 0.75rem; font-weight: 700;">Online</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: rgba(11, 15, 23, 0.5); border: 1px solid var(--border); border-radius: 8px;">
                    <span style="font-weight: 600; font-size: 0.875rem;"><i class="ti ti-credit-card" style="color: #a78bfa;"></i> Card Terminal (MPU/Visa)</span>
                    <span style="color: #a78bfa; font-size: 0.75rem; font-weight: 700;">Online</span>
                </div>
            </div>
        </div>

        {{-- Recent Settled Orders Card --}}
        <div class="panel-card">
            <div class="panel-title">
                <span><i class="ti ti-history"></i> Recent Settled Receipts</span>
            </div>
            @if ($recentOrders->isEmpty())
                <p style="color: var(--text-muted); font-size: 0.8125rem; margin: 0;">No completed transactions today yet.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    @foreach ($recentOrders as $ro)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0.8rem; background: rgba(11, 15, 23, 0.4); border: 1px solid var(--border); border-radius: 8px; font-size: 0.8125rem;">
                            <div>
                                <strong style="color: #f8fafc;">Table {{ $ro->table_number }}</strong>
                                <span style="color: var(--text-muted); font-size: 0.75rem; display: block;">{{ $ro->order_number }}</span>
                            </div>
                            <div style="text-align: right;">
                                <strong style="color: #9ec63b;">{{ number_format($ro->total_amount, 0) }} MMK</strong>
                                <div>
                                    <a href="{{ route('cashier.orders.receipt', $ro) }}" target="_blank" style="color: #38bdf8; font-size: 0.75rem; text-decoration: none;">
                                        <i class="ti ti-printer"></i> Reprint Receipt
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL 1: CHECKOUT & BILL SETTLEMENT MODAL --}}
<div id="checkoutModal" class="pos-modal-backdrop">
    <div class="pos-modal-box">
        <div class="modal-head">
            <div>
                <h3 id="checkoutModalTitle">Checkout & Settlement</h3>
                <span id="checkoutOrderMeta" style="color: var(--text-muted); font-size: 0.8125rem;">Table # &bull; Order #</span>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeCheckoutModal()">&times;</button>
        </div>

        <form id="settleOrderForm" method="POST" action="" onsubmit="handleSettleSubmit(event)">
            @csrf
            <div class="checkout-grid">
                {{-- Left: Itemized Bill and Total Calculation --}}
                <div>
                    <h4 style="font-size: 0.875rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.6rem;">
                        <i class="ti ti-list-check"></i> Ordered Dishes
                    </h4>
                    <div id="modalOrderItems" class="order-items-box">
                        {{-- Injected dynamically --}}
                    </div>

                    {{-- Calculations --}}
                    <div style="margin-top: 1rem; border-top: 1px solid #222d42; padding-top: 0.75rem;">
                        <div class="calc-row">
                            <span>Subtotal (ကျသင့်ငွေ):</span>
                            <span id="calcSubtotal" style="font-weight: 600; color: #f8fafc;">0 MMK</span>
                        </div>

                        {{-- Discount Controls --}}
                        <div class="form-group-pos" style="margin-top: 0.5rem;">
                            <label>Discount (လျှော့ဈေး)</label>
                            <div style="display: flex; gap: 0.4rem; margin-bottom: 0.4rem;">
                                <button type="button" class="btn-pos btn-pos-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="setDiscount('NONE', 0)">0%</button>
                                <button type="button" class="btn-pos btn-pos-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="setDiscount('PERCENT', 5)">5%</button>
                                <button type="button" class="btn-pos btn-pos-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="setDiscount('PERCENT', 10)">10%</button>
                            </div>
                            <div style="display: flex; gap: 0.4rem;">
                                <select id="discountTypeSelect" name="discount_type" class="input-pos" style="flex: 1;" onchange="recalculateTotals()">
                                    <option value="NONE">No Discount</option>
                                    <option value="PERCENT">Percentage (%)</option>
                                    <option value="FIXED">Fixed Amount (MMK)</option>
                                </select>
                                <input type="number" id="discountValueInput" name="discount_value" class="input-pos" style="flex: 1;" placeholder="0" value="0" min="0" oninput="recalculateTotals()">
                            </div>
                        </div>

                        <div class="calc-row">
                            <span>Discount Deducted:</span>
                            <span id="calcDiscount" style="color: #fb7185;">-0 MMK</span>
                        </div>
                        <div class="calc-row">
                            <span>Commercial Tax (ကုန်သွယ်ခွန် 5%):</span>
                            <span id="calcTax" style="font-weight: 600; color: #f8fafc;">0 MMK</span>
                        </div>
                        <div class="calc-row total-row">
                            <span>Total Due (စုစုပေါင်း ကျသင့်ငွေ):</span>
                            <span id="calcTotalDue">0 MMK</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Payment Tender & Change Calculator --}}
                <div>
                    <h4 style="font-size: 0.875rem; font-weight: 600; color: #f8fafc; margin-bottom: 0.6rem;">
                        <i class="ti ti-credit-card"></i> Payment Method
                    </h4>

                    {{-- Payment Tabs --}}
                    <div style="display: flex; gap: 0.4rem; margin-bottom: 1rem;">
                        <div class="payment-tab-btn active" onclick="selectPaymentMethod('CASH', this)">💵 Cash</div>
                        <div class="payment-tab-btn" onclick="selectPaymentMethod('KBZPAY', this)">📱 KBZPay</div>
                        <div class="payment-tab-btn" onclick="selectPaymentMethod('WAVEPAY', this)">📲 WavePay</div>
                        <div class="payment-tab-btn" onclick="selectPaymentMethod('CARD', this)">💳 Card</div>
                    </div>
                    <input type="hidden" id="selectedPaymentMethod" name="payment_method" value="CASH">

                    {{-- CASH SECTION --}}
                    <div id="cashPaymentSection">
                        <div class="form-group-pos">
                            <label>Amount Tendered by Customer (ပေးချေငွေ MMK) *</label>
                            <input type="number" id="amountTenderedInput" name="amount_tendered" class="input-pos" style="font-size: 1.15rem; font-weight: 700; color: #9ec63b;" placeholder="0" oninput="calculateChange()">
                        </div>

                        {{-- Quick Cash Denominations --}}
                        <div class="quick-cash-grid">
                            <div class="btn-quick-cash" onclick="addQuickCash('EXACT')">Exact (အတိအကျ)</div>
                            <div class="btn-quick-cash" onclick="addQuickCash(1000)">+1,000 MMK</div>
                            <div class="btn-quick-cash" onclick="addQuickCash(5000)">+5,000 MMK</div>
                            <div class="btn-quick-cash" onclick="addQuickCash(10000)">+10,000 MMK</div>
                            <div class="btn-quick-cash" onclick="addQuickCash(20000)">+20,000 MMK</div>
                            <div class="btn-quick-cash" onclick="addQuickCash(50000)">+50,000 MMK</div>
                        </div>

                        {{-- Change Returned Output --}}
                        <div id="changeReturnAlert" class="change-alert-box green" style="display: none;">
                            <span>ပြန်အမ်းငွေ (Change Return):</span>
                            <span id="changeReturnAmount">0 MMK</span>
                        </div>
                    </div>

                    {{-- DIGITAL SECTION (KBZPAY / WAVEPAY / CARD) --}}
                    <div id="digitalPaymentSection" style="display: none;">
                        <div class="form-group-pos">
                            <label id="digitalRefLabel">Transaction Reference No (ငွေလွှဲအမှတ်)</label>
                            <input type="text" id="digitalRefInput" name="reference_no" class="input-pos" placeholder="e.g. KPZ-98213840">
                        </div>
                        <div style="background: rgba(11, 15, 23, 0.5); border: 1px solid #222d42; border-radius: 8px; padding: 1rem; text-align: center; color: var(--text-muted); font-size: 0.8125rem;">
                            <i class="ti ti-qrcode" style="font-size: 2rem; display: block; margin-bottom: 0.4rem; color: #38bdf8;"></i>
                            <span id="digitalInstructionText">ဧည့်သည်၏ မိုဘိုင်းဖုန်းမှ QR Code စကင်ဖတ်ပြီး ငွေလွှဲအတည်ပြုချက် ရယူပါ။</span>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div style="margin-top: 1.5rem;">
                        <button type="submit" id="btnSubmitSettle" class="btn-pos btn-pos-primary" style="width: 100%; justify-content: center; padding: 0.85rem; font-size: 0.95rem;">
                            <i class="ti ti-check"></i> Complete Settlement & Print Receipt
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: OPEN CASH DRAWER SHIFT MODAL --}}
<div id="openShiftModal" class="pos-modal-backdrop">
    <div class="pos-modal-box" style="max-width: 480px;">
        <div class="modal-head">
            <h3><i class="ti ti-key"></i> Open Cashier Shift</h3>
            <button type="button" class="btn-close-modal" onclick="closeOpenShiftModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('cashier.shift.open') }}">
            @csrf
            <div class="form-group-pos">
                <label>Terminal Code *</label>
                <input type="text" name="terminal_code" class="input-pos" value="{{ $register['terminal_id'] ?? 'POS-REG-01' }}" required>
            </div>
            <div class="form-group-pos">
                <label>Opening Cash Float (အစပြုငွေသား MMK) *</label>
                <input type="number" name="opening_float" class="input-pos" placeholder="100000" min="0" required>
                <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.3rem; display: block;">
                    ဆိုင်းစတင်ချိန်တွင် အံဆွဲအတွင်း ထည့်သွင်းထားသော အကြွေစေ့နှင့် ငွေစက္ကူ (MMK) ပမာဏ။
                </span>
            </div>
            <div class="form-group-pos">
                <label>Shift Notes (မှတ်ချက်)</label>
                <textarea name="notes" class="input-pos" rows="2" placeholder="Morning / Evening shift notes"></textarea>
            </div>
            <div style="margin-top: 1.25rem;">
                <button type="submit" class="btn-pos btn-pos-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
                    Confirm & Open Shift
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 3: CASH IN / CASH OUT MODAL --}}
<div id="cashInOutModal" class="pos-modal-backdrop">
    <div class="pos-modal-box" style="max-width: 480px;">
        <div class="modal-head">
            <h3 id="cashModalTitle">Cash Drawer Transaction</h3>
            <button type="button" class="btn-close-modal" onclick="closeCashModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('cashier.shift.cashInOut') }}">
            @csrf
            <input type="hidden" id="cashModalType" name="type" value="CASH_IN">
            <div class="form-group-pos">
                <label>Amount (MMK) *</label>
                <input type="number" name="amount" class="input-pos" placeholder="10000" min="100" required>
            </div>
            <div class="form-group-pos">
                <label>Reason (အကြောင်းပြချက်) *</label>
                <input type="text" name="reason" class="input-pos" placeholder="e.g. Additional 1,000 Ks change notes / Ice purchase" minlength="3" required>
            </div>
            <div style="margin-top: 1.25rem;">
                <button type="submit" id="btnSubmitCashInOut" class="btn-pos btn-pos-emerald" style="width: 100%; justify-content: center; padding: 0.75rem;">
                    Record Cash Movement
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 4: CLOSE SHIFT & Z-REPORT MODAL --}}
<div id="closeShiftModal" class="pos-modal-backdrop">
    <div class="pos-modal-box" style="max-width: 520px;">
        <div class="modal-head">
            <h3><i class="ti ti-lock"></i> Close Shift & Produce Z-Report</h3>
            <button type="button" class="btn-close-modal" onclick="closeCloseShiftModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('cashier.shift.close') }}">
            @csrf
            <div style="background: rgba(11, 15, 23, 0.6); border: 1px solid #222d42; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.4rem;">
                    <span style="color: var(--text-muted);">Expected Drawer Cash:</span>
                    <strong style="color: #9ec63b;">{{ $register['expected_cash'] }}</strong>
                </div>
                <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">
                    Opening Float + Cash Sales + In - Out
                </span>
            </div>

            <div class="form-group-pos">
                <label>Actual Counted Cash in Drawer (အမှန်တကယ် လက်ကျန်ငွေသား MMK) *</label>
                <input type="number" name="closing_actual_cash" class="input-pos" placeholder="0" min="0" required>
                <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.3rem; display: block;">
                    အံဆွဲထဲရှိ ငွေစက္ကူများကို ကိုယ်တိုင် ရေတွက်ပြီး ရရှိသော ပမာဏကို ထည့်သွင်းပါ။ စနစ်မှ အပို/အလို (Over/Short) တွက်ချက်ပေးပါမည်။
                </span>
            </div>

            <div class="form-group-pos">
                <label>Closing Shift Notes (မှတ်ချက်)</label>
                <textarea name="notes" class="input-pos" rows="2" placeholder="Shift summary, hand-over notes"></textarea>
            </div>

            <div style="margin-top: 1.25rem;">
                <button type="submit" class="btn-pos btn-pos-rose" style="width: 100%; justify-content: center; padding: 0.75rem;">
                    Close Shift & Print Z-Report
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentOrderData = null;
    let currentNetTotal = 0;

    // Open Shift Modal
    function openShiftModal() {
        document.getElementById('openShiftModal').classList.add('show');
    }
    function closeOpenShiftModal() {
        document.getElementById('openShiftModal').classList.remove('show');
    }

    // Cash In / Cash Out Modal
    function openCashModal(type) {
        document.getElementById('cashModalType').value = type;
        const title = document.getElementById('cashModalTitle');
        const btn = document.getElementById('btnSubmitCashInOut');

        if (type === 'CASH_IN') {
            title.innerHTML = '<i class="ti ti-arrow-down-left" style="color: #34d399;"></i> Cash In (ငွေသားထည့်သွင်းခြင်း)';
            btn.className = 'btn-pos btn-pos-emerald';
            btn.innerText = 'Confirm Cash In';
        } else {
            title.innerHTML = '<i class="ti ti-arrow-up-right" style="color: #fbbf24;"></i> Cash Out (ငွေသားထုတ်ယူခြင်း)';
            btn.className = 'btn-pos btn-pos-amber';
            btn.innerText = 'Confirm Cash Out';
        }

        document.getElementById('cashInOutModal').classList.add('show');
    }
    function closeCashModal() {
        document.getElementById('cashInOutModal').classList.remove('show');
    }

    // Close Shift Modal
    function openCloseShiftModal() {
        document.getElementById('closeShiftModal').classList.add('show');
    }
    function closeCloseShiftModal() {
        document.getElementById('closeShiftModal').classList.remove('show');
    }

    // Checkout Modal
    async function openCheckoutModal(orderId) {
        try {
            const res = await fetch(`/cashier/orders/${orderId}/details`);
            const data = await res.json();
            if (!data.success) {
                alert(data.message || 'Error fetching order details.');
                return;
            }

            currentOrderData = data.order;
            document.getElementById('checkoutModalTitle').innerText = `Checkout & Settlement - Table ${currentOrderData.table_number}`;
            document.getElementById('checkoutOrderMeta').innerText = `Order: ${currentOrderData.order_number} • Guests: ${currentOrderData.guest_count} Pax`;
            document.getElementById('settleOrderForm').action = `/cashier/orders/${orderId}/settle`;

            // Populate Items
            const itemsBox = document.getElementById('modalOrderItems');
            itemsBox.innerHTML = '';
            currentOrderData.items.forEach(it => {
                const row = document.createElement('div');
                row.className = 'item-row';
                row.innerHTML = `
                    <div>
                        <strong>${it.quantity}x ${it.item_name}</strong>
                        ${it.special_notes ? `<span style="display: block; color: var(--text-muted); font-size: 0.7rem;">Note: ${it.special_notes}</span>` : ''}
                    </div>
                    <div>${it.formatted_subtotal}</div>
                `;
                itemsBox.appendChild(row);
            });

            // Reset discounts & tenders
            document.getElementById('discountTypeSelect').value = 'NONE';
            document.getElementById('discountValueInput').value = 0;
            document.getElementById('amountTenderedInput').value = '';
            selectPaymentMethod('CASH');

            recalculateTotals();

            document.getElementById('checkoutModal').classList.add('show');
        } catch (err) {
            console.error(err);
            alert('Failed to connect to POS server.');
        }
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').classList.remove('show');
    }

    function setDiscount(type, val) {
        document.getElementById('discountTypeSelect').value = type;
        document.getElementById('discountValueInput').value = val;
        recalculateTotals();
    }

    function recalculateTotals() {
        if (!currentOrderData) return;

        const subtotal = currentOrderData.subtotal;
        const discType = document.getElementById('discountTypeSelect').value;
        const discVal = parseFloat(document.getElementById('discountValueInput').value) || 0;

        let discountAmount = 0;
        if (discType === 'PERCENT' && discVal > 0) {
            discountAmount = Math.round(subtotal * (discVal / 100));
        } else if (discType === 'FIXED' && discVal > 0) {
            discountAmount = Math.min(subtotal, Math.round(discVal));
        }

        const taxable = Math.max(0, subtotal - discountAmount);
        const taxAmount = Math.round(taxable * 0.05);
        currentNetTotal = taxable + taxAmount;

        document.getElementById('calcSubtotal').innerText = subtotal.toLocaleString() + ' MMK';
        document.getElementById('calcDiscount').innerText = '-' + discountAmount.toLocaleString() + ' MMK';
        document.getElementById('calcTax').innerText = taxAmount.toLocaleString() + ' MMK';
        document.getElementById('calcTotalDue').innerText = currentNetTotal.toLocaleString() + ' MMK';

        calculateChange();
    }

    function selectPaymentMethod(method, elem) {
        document.getElementById('selectedPaymentMethod').value = method;
        document.querySelectorAll('.payment-tab-btn').forEach(btn => btn.classList.remove('active'));
        if (elem) elem.classList.add('active');

        const cashSec = document.getElementById('cashPaymentSection');
        const digSec = document.getElementById('digitalPaymentSection');
        const refLabel = document.getElementById('digitalRefLabel');

        if (method === 'CASH') {
            cashSec.style.display = 'block';
            digSec.style.display = 'none';
        } else {
            cashSec.style.display = 'none';
            digSec.style.display = 'block';
            refLabel.innerText = `${method} Transaction Reference # (ငွေလွှဲအမှတ်)`;
        }
    }

    function addQuickCash(amount) {
        const input = document.getElementById('amountTenderedInput');
        if (amount === 'EXACT') {
            input.value = currentNetTotal;
        } else {
            const current = parseInt(input.value) || 0;
            input.value = current + amount;
        }
        calculateChange();
    }

    function calculateChange() {
        const method = document.getElementById('selectedPaymentMethod').value;
        const alertBox = document.getElementById('changeReturnAlert');
        const amountSpan = document.getElementById('changeReturnAmount');

        if (method !== 'CASH') {
            alertBox.style.display = 'none';
            return;
        }

        const tendered = parseInt(document.getElementById('amountTenderedInput').value) || 0;
        if (tendered === 0) {
            alertBox.style.display = 'none';
            return;
        }

        alertBox.style.display = 'flex';
        if (tendered >= currentNetTotal) {
            const change = tendered - currentNetTotal;
            alertBox.className = 'change-alert-box green';
            alertBox.innerHTML = `<span>ပြန်အမ်းငွေ (Change Return):</span><span>${change.toLocaleString()} MMK</span>`;
        } else {
            const shortage = currentNetTotal - tendered;
            alertBox.className = 'change-alert-box red';
            alertBox.innerHTML = `<span>မလုံလောက်သေးပါ (Shortage):</span><span>-${shortage.toLocaleString()} MMK</span>`;
        }
    }

    async function handleSettleSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const method = document.getElementById('selectedPaymentMethod').value;
        const tendered = parseInt(document.getElementById('amountTenderedInput').value) || 0;

        if (method === 'CASH' && tendered < currentNetTotal) {
            alert(`ကျေးဇူးပြု၍ ပေးချေငွေ (အနည်းဆုံး ${currentNetTotal.toLocaleString()} MMK) အပြည့်အဝ ထည့်သွင်းပေးပါ။`);
            return;
        }

        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await res.json();
            if (result.success) {
                closeCheckoutModal();
                // Open Receipt window
                window.open(result.receipt_url, '_blank');
                // Reload page to update register tables
                window.location.reload();
            } else {
                alert(result.message || 'Error settling bill.');
            }
        } catch (err) {
            console.error(err);
            form.submit();
        }
    }
</script>
@endpush
