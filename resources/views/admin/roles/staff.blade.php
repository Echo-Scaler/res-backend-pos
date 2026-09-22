@extends('admin.layouts.app')

@section('title', 'Floor Staff & Waiter Portal')

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
        background: rgba(6, 182, 212, 0.2);
        color: #67e8f9;
        border: 1px solid rgba(6, 182, 212, 0.4);
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
        border-color: rgba(6, 182, 212, 0.4);
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

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        color: #f8fafc;
    }

    .tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .table-card {
        background-color: var(--bg-card);
        border: 2px solid var(--border);
        border-radius: 14px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .table-card:hover {
        transform: translateY(-3px);
    }

    .table-card.occupied {
        border-color: rgba(249, 115, 22, 0.6);
        background: rgba(249, 115, 22, 0.05);
    }

    .table-card.vacant {
        border-color: rgba(16, 185, 129, 0.6);
        background: rgba(16, 185, 129, 0.05);
    }

    .table-card.billing {
        border-color: rgba(245, 158, 11, 0.6);
        background: rgba(245, 158, 11, 0.05);
    }

    .table-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-num {
        font-size: 1.3rem;
        font-weight: 800;
    }

    .table-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        text-transform: uppercase;
    }

    .badge-vacant {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
    }

    .badge-occupied {
        background: rgba(249, 115, 22, 0.2);
        color: #fb923c;
    }

    .badge-billing-t {
        background: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
    }

    .table-info {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .btn-table-action {
        width: 100%;
        padding: 0.6rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-take-order {
        background-color: #06b6d4;
        color: #fff;
    }

    .btn-take-order:hover {
        background-color: #0891b2;
    }

    .btn-view-order {
        background-color: rgba(249, 115, 22, 0.2);
        color: #fb923c;
    }

    .btn-view-order:hover {
        background-color: var(--primary);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="role-header">
    <div>
        <div class="role-title">
            <span>🍽️ Floor Waiter & Order Taking Portal</span>
            <span class="role-badge-pill">STAFF / WAITER</span>
        </div>
        <p class="role-subtitle">Live tableside ordering, dining floor statuses & guest service for <strong>{{ $restaurant->name ?? 'Restaurant' }}</strong></p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">My Active Tables</div>
        <div class="stat-value" style="color: #67e8f9;">{{ $floor['active_tables'] ?? 6 }} Tables</div>
        <div class="stat-desc">Zone: {{ $floor['assigned_zone'] ?? 'Main Dining' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Kitchen Pickup Ready</div>
        <div class="stat-value" style="color: #34d399;">{{ $floor['ready_pickup_orders'] ?? 2 }} Dishes</div>
        <div class="stat-desc">Hot dishes ready on Kitchen Pass</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Floor Shift</div>
        <div class="stat-value">Dinner Service</div>
        <div class="stat-desc">Active shift on floor</div>
    </div>
</div>

<h2 class="section-title">Dining Floor Tables (Zone A)</h2>
<div class="tables-grid">
    @foreach ($floor['table_list'] as $table)
        <div class="table-card {{ $table['status'] }}">
            <div class="table-top">
                <span class="table-num">{{ $table['number'] }}</span>
                @if ($table['status'] === 'vacant')
                    <span class="table-badge badge-vacant">Available</span>
                @elseif ($table['status'] === 'occupied')
                    <span class="table-badge badge-occupied">Occupied</span>
                @else
                    <span class="table-badge badge-billing-t">Bill Out</span>
                @endif
            </div>
            <div class="table-info">
                <div>Guests: {{ $table['guests'] > 0 ? $table['guests'] . ' Pax' : 'None' }}</div>
                <div>Status: {{ $table['time'] }}</div>
            </div>
            <div>
                @if ($table['status'] === 'vacant')
                    <button type="button" class="btn-table-action btn-take-order">+ Take Order</button>
                @elseif ($table['status'] === 'occupied')
                    <button type="button" class="btn-table-action btn-view-order">Manage Order</button>
                @else
                    <button type="button" class="btn-table-action" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">Print Bill</button>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
