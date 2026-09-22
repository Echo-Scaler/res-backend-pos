@extends('admin.layouts.app')

@section('title', 'Employee Management')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title {
        font-family: "Mada", sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.9375rem;
        font-weight: 400;
        margin-top: 0.3rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #9ec63b, #7ea826);
        color: #ffffff !important;
        padding: 0.7rem 1.4rem;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.9375rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(158, 198, 59, 0.28);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(158, 198, 59, 0.38);
        filter: brightness(1.03);
    }

    .btn-secondary {
        background-color: var(--bg-hover);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        padding: 0.65rem 1.15rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: var(--transition);
        cursor: pointer;
    }

    .btn-secondary:hover {
        background-color: var(--border-color);
        color: var(--text-main);
    }

    .filter-bar {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .search-form {
        display: flex;
        gap: 0.75rem;
        flex: 1;
        max-width: 500px;
    }

    .form-input {
        background-color: var(--bg-body);
        border: 1.5px solid var(--border-color);
        color: var(--text-main);
        padding: 0.65rem 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        font-family: "Mada", sans-serif;
        width: 100%;
        outline: none;
        transition: var(--transition);
    }

    .form-input:focus {
        border-color: var(--primary);
        background-color: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.2);
    }

    .role-pills {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .role-pill {
        padding: 0.45rem 0.9rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        border: 1.5px solid var(--border-color);
        color: var(--text-muted);
        background: var(--bg-card);
        transition: var(--transition);
    }

    .role-pill.active, .role-pill:hover {
        border-color: var(--primary);
        color: var(--primary-hover);
        background: rgba(158, 198, 59, 0.12);
    }

    .table-container {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .emp-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .emp-table th {
        background-color: var(--bg-hover);
        color: var(--text-muted);
        font-size: 0.8125rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }

    .emp-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9375rem;
        vertical-align: middle;
        color: var(--text-main);
    }

    .emp-table tr:last-child td {
        border-bottom: none;
    }

    .emp-table tr:hover td {
        background-color: var(--bg-hover);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: var(--primary-light);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--primary-hover);
        font-size: 1rem;
        flex-shrink: 0;
    }

    .user-name-title {
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .user-email-text {
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }

    .badge-role {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .role-owner {
        background-color: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    [data-theme="dark"] .role-owner {
        background-color: rgba(239, 68, 68, 0.2);
        color: #f87171;
    }

    .role-manager {
        background-color: rgba(59, 130, 246, 0.12);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    [data-theme="dark"] .role-manager {
        background-color: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
    }

    .role-cashier {
        background-color: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    [data-theme="dark"] .role-cashier {
        background-color: rgba(16, 185, 129, 0.2);
        color: #34d399;
    }

    .role-staff {
        background-color: rgba(158, 198, 59, 0.15);
        color: #4d7c0f;
        border: 1px solid rgba(158, 198, 59, 0.35);
    }
    [data-theme="dark"] .role-staff {
        background-color: rgba(158, 198, 59, 0.2);
        color: #bef264;
    }

    .badge-pin-set {
        background-color: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.28);
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    [data-theme="dark"] .badge-pin-set {
        background-color: rgba(16, 185, 129, 0.18);
        color: #6ee7b7;
    }

    .badge-pin-missing {
        background-color: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.25);
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    [data-theme="dark"] .badge-pin-missing {
        background-color: rgba(239, 68, 68, 0.18);
        color: #fca5a5;
    }

    .actions-cell {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .btn-edit {
        background: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
        border: 1px solid rgba(99, 102, 241, 0.28);
        padding: 0.4rem 0.85rem;
        border-radius: var(--radius-sm);
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: var(--transition);
    }

    .btn-edit:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    [data-theme="dark"] .btn-edit {
        background: rgba(99, 102, 241, 0.2);
        color: #a5b4fc;
        border-color: rgba(99, 102, 241, 0.4);
    }

    [data-theme="dark"] .btn-edit:hover {
        background: #6366f1;
        color: #ffffff;
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.28);
        padding: 0.4rem 0.85rem;
        border-radius: var(--radius-sm);
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: var(--transition);
    }

    .btn-delete:hover {
        background: var(--danger);
        color: #ffffff;
    }

    [data-theme="dark"] .btn-delete {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.4);
    }

    [data-theme="dark"] .btn-delete:hover {
        background: #ef4444;
        color: #ffffff;
    }

    .empty-state {
        text-align: center;
        padding: 3.5rem 1rem;
        color: var(--text-muted);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">
            <span>👥</span>
            <span>Employee Management</span>
            <span style="font-size: 0.8125rem; padding: 0.25rem 0.75rem; border-radius: 9999px; background: rgba(158, 198, 59, 0.15); color: var(--primary-hover); border: 1px solid rgba(158, 198, 59, 0.3); font-weight: 600;">
                {{ $restaurant->name ?? 'Restaurant Staff' }}
            </span>
        </div>
        
    </div>

    <div>
        <a href="{{ route('admin.employees.create') }}" class="btn-primary">
            <span>➕</span>
            <span>Add New Employee</span>
        </a>
    </div>
</div>

<div class="filter-bar">
    <div class="role-pills">
        <a href="{{ route('admin.employees.index', array_filter(['search' => $search])) }}" class="role-pill {{ empty($selectedRole) ? 'active' : '' }}">All Roles</a>
        <a href="{{ route('admin.employees.index', array_filter(['role' => 'MANAGER', 'search' => $search])) }}" class="role-pill {{ $selectedRole === 'MANAGER' ? 'active' : '' }}">Managers</a>
        <a href="{{ route('admin.employees.index', array_filter(['role' => 'CASHIER', 'search' => $search])) }}" class="role-pill {{ $selectedRole === 'CASHIER' ? 'active' : '' }}">Cashiers</a>
        <a href="{{ route('admin.employees.index', array_filter(['role' => 'STAFF', 'search' => $search])) }}" class="role-pill {{ $selectedRole === 'STAFF' ? 'active' : '' }}">Dining Staff</a>
    </div>

    <form method="GET" action="{{ route('admin.employees.index') }}" class="search-form">
        @if(!empty($selectedRole))
            <input type="hidden" name="role" value="{{ $selectedRole }}">
        @endif
        <input type="text" name="search" value="{{ $search }}" class="form-input" placeholder="Search by name, email, or phone...">
        <button type="submit" class="btn-secondary">Search</button>
        @if(!empty($search) || !empty($selectedRole))
            <a href="{{ route('admin.employees.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>
</div>

<div class="table-container">
    <table class="emp-table">
        <thead>
            <tr>
                <th>Employee Details</th>
                <th>Role & Permissions</th>
                <th>Phone Number</th>
                <th>POS Quick PIN</th>
                <th>Joined</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
                @php
                    $roleName = $employee->getRoleNames()->first() ?? 'STAFF';
                    $roleClass = match($roleName) {
                        'OWNER' => 'role-owner',
                        'MANAGER' => 'role-manager',
                        'CASHIER' => 'role-cashier',
                        default => 'role-staff',
                    };

                    // Permission logic for actions
                    $canEdit = false;
                    $canDelete = false;

                    if ($currentUser->hasRole('OWNER')) {
                        $canEdit = true;
                        $canDelete = !$employee->hasRole('OWNER') && $currentUser->id !== $employee->id;
                    } elseif ($currentUser->hasRole('MANAGER')) {
                        // Manager can only edit/delete Cashier and Staff
                        if (!$employee->hasRole('OWNER') && !$employee->hasRole('MANAGER')) {
                            $canEdit = true;
                            $canDelete = true;
                        }
                    }
                @endphp
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="user-name-title">
                                    <span>{{ $employee->name }}</span>
                                    @if($employee->id === $currentUser->id)
                                        <span style="font-size: 0.7rem; background: rgba(148, 163, 184, 0.2); color: var(--text-muted); padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;">(You)</span>
                                    @endif
                                </div>
                                <div class="user-email-text">{{ $employee->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge-role {{ $roleClass }}">{{ $roleName }}</span>
                    </td>
                    <td>
                        <span style="color: {{ $employee->phone ? 'var(--text-main)' : 'var(--text-muted)' }};">
                            {{ $employee->phone ?? '—' }}
                        </span>
                    </td>
                    <td>
                        @if($employee->pin_code)
                            <span class="badge-pin-set">🔒 PIN Active</span>
                        @else
                            <span class="badge-pin-missing">⚠️ Not Assigned</span>
                        @endif
                    </td>
                    <td>
                        <span style="color: var(--text-muted); font-size: 0.85rem;">
                            {{ $employee->created_at->format('M d, Y') }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="actions-cell" style="justify-content: flex-end;">
                            @if($canEdit)
                                <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-edit">
                                    <span>✏️</span>
                                    <span>Edit</span>
                                </a>
                            @endif

                            @if($canDelete)
                                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove {{ $employee->name }}? This action cannot be undone.');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <span>🗑️</span>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            @endif

                            @if(!$canEdit && !$canDelete)
                                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Protected</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">👥</div>
                        <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.25rem;">No employees found</h4>
                        <p style="font-size: 0.85rem;">Try adjusting your search query or add a new team member.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem;">
    {{ $employees->links() }}
</div>
@endsection
