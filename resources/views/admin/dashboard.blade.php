@extends('admin.layouts.app')

@section('title', 'Management Dashboard')

@push('styles')
<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .header-title {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .header-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 0.25rem;
    }

    .badge-active {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background-color: rgba(16, 185, 129, 0.15);
        color: #34d399;
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 700;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 10px #10b981;
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
        border-color: rgba(249, 115, 22, 0.4);
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
        font-size: 1.5rem;
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
        border-radius: 16px;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .module-card:hover {
        border-color: var(--primary);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px);
    }

    .module-icon {
        font-size: 2.25rem;
        margin-bottom: 1rem;
    }

    .module-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 0.5rem;
    }

    .module-desc {
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1.25rem;
    }

    .module-action {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--primary);
        font-size: 0.9rem;
        font-weight: 700;
        text-decoration: none;
    }

    .role-badge-banner {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
    }

    .role-banner-owner {
        background-color: rgba(249, 115, 22, 0.12);
        border: 1px solid rgba(249, 115, 22, 0.3);
        color: #fdba74;
    }

    .role-banner-manager {
        background-color: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #93c5fd;
    }

    .system-status-box {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.8));
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: center;
    }

    .status-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.85rem;
        color: #cbd5e1;
    }
</style>
@endpush

@section('content')
<div class="dashboard-header">
    <div>
        <h1 class="header-title">{{ $restaurant->name ?? 'Restaurant Management' }}</h1>
        <p class="header-subtitle">Logged in as: <strong>{{ $user->name }}</strong> ({{ $user->email }})</p>
    </div>
    <div class="badge-active">
        <span class="status-dot"></span>
        <span>Restaurant Online ({{ $stats['status'] }})</span>
    </div>
</div>

@if($user->hasRole('OWNER'))
<div class="role-badge-banner role-banner-owner">
    <span>👑</span>
    <span><strong>Owner Authority Active:</strong> You have full, unrestricted permissions to control all restaurant operations, employees, managers, financials, and settings.</span>
</div>
@elseif($user->hasRole('MANAGER'))
<div class="role-badge-banner role-banner-manager">
    <span>👔</span>
    <span><strong>Manager Authority Active:</strong> You can manage menus, tables, and staff (Cashiers & Waiters). Manager/Owner accounts and sensitive restaurant ownership settings are restricted to the Owner.</span>
</div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Restaurant Slug</div>
        <div class="stat-value" style="font-size: 1.15rem;">{{ $restaurant->slug ?? 'pos-store' }}</div>
        <div class="stat-desc">Tenant isolation key</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Your Role</div>
        <div class="stat-value" style="color: {{ $user->hasRole('OWNER') ? 'var(--primary)' : '#38bdf8' }};">
            {{ $user->getRoleNames()->first() ?? 'Staff' }}
        </div>
        <div class="stat-desc">{{ $user->hasRole('OWNER') ? 'Full administrative control' : 'Operational supervisor' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Restaurant Accounts</div>
        <div class="stat-value">{{ $stats['total_users'] }}</div>
        <div class="stat-desc">Owners, Managers, Cashiers & Staff</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Permissions Scope</div>
        <div class="stat-value" style="font-size: 1.15rem; color: #34d399;">
            {{ $user->hasRole('OWNER') ? 'Unrestricted (100%)' : 'Operations (Staff/Menu)' }}
        </div>
        <div class="stat-desc">Role-based access matrix applied</div>
    </div>
</div>

<h2 class="section-title">Back-Office Management Modules</h2>
<div class="modules-grid">
    <div class="module-card">
        <div class="module-icon">🍕</div>
        <h3 class="module-title">Menu & Category Management</h3>
        <p class="module-desc">Organize food categories, combo meals, modifiers, and set availability for the customer menu.</p>
        <span class="module-action">Permitted for {{ $user->getRoleNames()->first() }} →</span>
    </div>

    <div class="module-card">
        <div class="module-icon">🏷️</div>
        <h3 class="module-title">Product & Inventory Pricing</h3>
        <p class="module-desc">Manage ingredient inventory, recipes, item tax rates, and dynamic price tiers.</p>
        <span class="module-action">Permitted for {{ $user->getRoleNames()->first() }} →</span>
    </div>

    <a href="{{ route('admin.employees.index') }}" class="module-card">
        <div class="module-icon">👥</div>
        <h3 class="module-title">Employee Management</h3>
        @if($user->hasRole('OWNER'))
            <p class="module-desc"><strong>Full Authority:</strong> Create, promote, and manage Managers, Cashiers, and Dining Staff. Assign permissions and PINs.</p>
            <span class="module-action">Full Control (Owner Only) →</span>
        @else
            <p class="module-desc"><strong>Staff Authority:</strong> Create and manage Cashiers and Dining Staff. (Creating or editing Managers is restricted to the Owner).</p>
            <span class="module-action" style="color: #38bdf8;">Staff Management Permitted →</span>
        @endif
    </a>

    <div class="module-card">
        <div class="module-icon">🪑</div>
        <h3 class="module-title">Table & Floor Layout</h3>
        <p class="module-desc">Customize dining room sections, table numbers, QR code menus, and track live occupancy.</p>
        <span class="module-action">Permitted for {{ $user->getRoleNames()->first() }} →</span>
    </div>

    <div class="module-card">
        <div class="module-icon">🧾</div>
        <h3 class="module-title">Orders & Billing Reports</h3>
        <p class="module-desc">Real-time order tracking, kitchen ticket display (KDS), split billing, and payment processing.</p>
        <span class="module-action">Permitted for {{ $user->getRoleNames()->first() }} →</span>
    </div>

    <div class="module-card">
        <div class="module-icon">⚙️</div>
        <h3 class="module-title">Restaurant Settings</h3>
        @if($user->hasRole('OWNER'))
            <p class="module-desc">Configure business profile, tax rates, printer routing, and system ownership.</p>
            <span class="module-action">Unlocked (Owner Control) →</span>
        @else
            <p class="module-desc">Business ownership, tax configuration, and core store profiles are restricted.</p>
            <span class="module-action" style="color: #94a3b8;">🔒 Restricted to Owner Only</span>
        @endif
    </div>
</div>

<div class="system-status-box">
    <div class="status-item">
        <span class="status-dot"></span>
        <span><strong>Database:</strong> MySQL 8.0</span>
    </div>
    <div class="status-item">
        <span class="status-dot"></span>
        <span><strong>Cache Store:</strong> Redis Alpine</span>
    </div>
    <div class="status-item">
        <span class="status-dot"></span>
        <span><strong>Active User:</strong> {{ $user->email }}</span>
    </div>
    <div class="status-item">
        <span class="status-dot"></span>
        <span><strong>Authority:</strong> {{ $user->hasRole('OWNER') ? 'Super Admin' : 'Supervisor' }}</span>
    </div>
</div>
@endsection
