@extends('admin.layouts.app')

@section('title', 'Cashier POS Checkout Register')

@push('styles')
<style>
    .role-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .role-title {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .role-badge-pill {
        font-size: 0.85rem;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        background: rgba(16, 185, 129, 0.2);
        color: #6ee7b7;
        border: 1px solid rgba(16, 185, 129, 0.4);
        font-weight: 700;
    }

    .role-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 0.35rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.5rem;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        border-color: rgba(16, 185, 129, 0.4);
        transform: translateY(-2px);
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: #f8fafc;
    }

    .stat-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
    }

    .checkout-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    @media (max-width: 900px) {
        .checkout-container {
            grid-template-columns: 1fr;
        }
    }

    .table-panel {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.5rem;
    }

    .panel-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .checkout-table {
        width: 100%;
        border-collapse: collapse;
    }

    .checkout-table th {
        text-align: left;
        padding: 0.75rem;
        color: var(--text-muted);
        font-size: 0.8rem;
        text-transform: uppercase;
        border-bottom: 1px solid var(--border);
    }

    .checkout-table td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid rgba(51, 65, 85, 0.5);
        font-size: 0.9rem;
    }

    .badge-status {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-billing {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .btn-checkout {
        background-color: #10b981;
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .btn-checkout:hover {
        background-color: #059669;
    }

    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .payment-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: rgba(15, 23, 42, 0.6);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.85rem 1rem;
        color: #f8fafc;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .payment-btn:hover {
        border-color: #10b981;
        background-color: rgba(16, 185, 129, 0.1);
    }
</style>
@endpush

@section('content')
<div class="role-header">
    <div>
        <div class="role-title">
            <span>💵 Cashier Register & Checkout Portal</span>
            <span class="role-badge-pill">CASHIER</span>
        </div>
        <p class="role-subtitle">Counter checkout, customer invoicing & payment settlement for <strong>{{ $restaurant->name ?? 'Restaurant' }}</strong></p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Drawer Session</div>
        <div class="stat-value" style="color: #34d399;">{{ $register['status'] ?? 'OPEN' }}</div>
        <div class="stat-desc">Terminal: {{ $register['terminal_id'] ?? 'POS-REG-01' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Opening Float</div>
        <div class="stat-value">{{ $register['opening_balance'] ?? '150,000 MMK' }}</div>
        <div class="stat-desc">Cash In Drawer at Shift Start</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Today's Sales Collected</div>
        <div class="stat-value">{{ $register['current_sales'] ?? '485,000 MMK' }}</div>
        <div class="stat-desc">{{ $register['transactions_count'] ?? 18 }} Completed Transactions</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Billing Tables</div>
        <div class="stat-value" style="color: #fbbf24;">3 Tables</div>
        <div class="stat-desc">Awaiting Payment Settlement</div>
    </div>
</div>

<div class="checkout-container">
    <div class="table-panel">
        <div class="panel-title">
            <span>🧾 Tables Awaiting Payment</span>
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal;">Live Order Feeds</span>
        </div>
        <table class="checkout-table">
            <thead>
                <tr>
                    <th>Table</th>
                    <th>Waiter</th>
                    <th>Items</th>
                    <th>Total Due</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Table 03</strong></td>
                    <td>Ko Myo (Waiter)</td>
                    <td>4 Items</td>
                    <td><strong>48,500 MMK</strong></td>
                    <td><span class="badge-status badge-billing">Bill Requested</span></td>
                    <td><button type="button" class="btn-checkout">Collect Bill</button></td>
                </tr>
                <tr>
                    <td><strong>Table 07</strong></td>
                    <td>Ma Hnin (Waiter)</td>
                    <td>6 Items</td>
                    <td><strong>72,000 MMK</strong></td>
                    <td><span class="badge-status badge-billing">Bill Requested</span></td>
                    <td><button type="button" class="btn-checkout">Collect Bill</button></td>
                </tr>
                <tr>
                    <td><strong>Table 12</strong></td>
                    <td>Ko Myo (Waiter)</td>
                    <td>2 Items</td>
                    <td><strong>18,500 MMK</strong></td>
                    <td><span class="badge-status badge-billing">Bill Requested</span></td>
                    <td><button type="button" class="btn-checkout">Collect Bill</button></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="table-panel">
        <div class="panel-title">
            <span>💳 Quick Pay Channels</span>
        </div>
        <div class="payment-methods">
            <div class="payment-btn">
                <span>💵 Cash (ငွေသား)</span>
                <span style="color: #10b981;">Ready</span>
            </div>
            <div class="payment-btn">
                <span>📱 KBZPay QR Code</span>
                <span style="color: #10b981;">Connected</span>
            </div>
            <div class="payment-btn">
                <span>📲 WavePay QR Code</span>
                <span style="color: #10b981;">Connected</span>
            </div>
            <div class="payment-btn">
                <span>💳 Card Terminal (MPU/Visa)</span>
                <span style="color: #10b981;">Online</span>
            </div>
        </div>
    </div>
</div>
@endsection
