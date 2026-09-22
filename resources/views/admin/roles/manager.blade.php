@extends('admin.layouts.app')

@section('title', 'Manager Operations Dashboard')

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
        background: rgba(99, 102, 241, 0.2);
        color: #a5b4fc;
        border: 1px solid rgba(99, 102, 241, 0.4);
        font-weight: 700;
    }

    .role-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 0.35rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
        border-color: rgba(99, 102, 241, 0.4);
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

    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .module-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1.25rem;
    }

    .module-info h3 {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .module-info p {
        color: var(--text-muted);
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background-color: #6366f1;
        color: #fff;
        padding: 0.65rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .btn-action:hover {
        background-color: #4f46e5;
    }

    .permission-notice {
        background: rgba(15, 23, 42, 0.6);
        border: 1px dashed rgba(99, 102, 241, 0.3);
        border-radius: 12px;
        padding: 1.25rem;
        margin-top: 2rem;
    }

    .permission-notice h4 {
        color: #a5b4fc;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }

    .permission-notice ul {
        list-style-type: none;
        color: var(--text-muted);
        font-size: 0.85rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="role-header">
    <div>
        <div class="role-title">
            <span>👔 Manager Operations Portal</span>
            <span class="role-badge-pill">MANAGER</span>
        </div>
        <p class="role-subtitle">Daily floor supervision, staff assignments, table turnover & shift controls for <strong>{{ $restaurant->name ?? 'Restaurant' }}</strong></p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Floor Staff On Duty</div>
        <div class="stat-value">{{ $stats['total_staff'] ?? 2 }} Active</div>
        <div class="stat-desc">Cashiers & Service Waiters</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Floor Shift Status</div>
        <div class="stat-value" style="color: #34d399;">Active</div>
        <div class="stat-desc">Lunch/Dinner Shift Running</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Dining Tables</div>
        <div class="stat-value">{{ $stats['tables_count'] ?? 24 }} Tables</div>
        <div class="stat-desc">Indoor & Outdoor Dining Zones</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Order Voids</div>
        <div class="stat-value">{{ $stats['pending_voids'] ?? 0 }}</div>
        <div class="stat-desc">Requires Manager Approval</div>
    </div>
</div>

<h2 class="section-title">Operational Controls</h2>
<div class="modules-grid">
    <div class="module-card">
        <div class="module-info">
            <h3>👥 Staff Management</h3>
            <p>Manage on-duty Cashiers and Waiter staff. Assign tables, review shift hours, and approve void requests.</p>
        </div>
        <div>
            <a href="{{ route('admin.employees.index') }}" class="btn-action">Manage Staff Members</a>
        </div>
    </div>

    <div class="module-card">
        <div class="module-info">
            <h3>🍽️ Menu & Inventory Availabilities</h3>
            <p>Toggle 86'd items (out-of-stock items), adjust daily specials, and inspect kitchen inventory levels.</p>
        </div>
        <div>
            <button type="button" class="btn-action">Update Daily Menu</button>
        </div>
    </div>

    <div class="module-card">
        <div class="module-info">
            <h3>🪑 Floor & Table Layout</h3>
            <p>Monitor live dining table statuses, re-assign waiting guests, and manage reservation turnover.</p>
        </div>
        <div>
            <button type="button" class="btn-action">View Live Floor Layout</button>
        </div>
    </div>
</div>

<div class="permission-notice">
    <h4>🔒 Security & Role Boundary Notice</h4>
    <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">
        As Manager, you have operational authority to manage daily staff and restaurant floor activities. Executive controls are restricted to the Restaurant Owner:
    </p>
    <ul>
        <li>✅ Permitted: Manage Staff (Cashier & Waiter), Manage Tables, Update Menu items.</li>
        <li>⛔ Restricted (Owner Only): Financial Payouts, Create/Delete Managers, Restaurant Legal Settings.</li>
    </ul>
</div>
@endsection
