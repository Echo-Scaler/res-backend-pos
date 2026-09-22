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
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 0.25rem;
    }

    .btn-primary {
        background-color: var(--primary);
        color: #fff;
        padding: 0.7rem 1.4rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background-color: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border);
        padding: 0.6rem 1.1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }

    .btn-secondary:hover {
        background-color: var(--bg-card-hover);
    }

    .filter-bar {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
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

    .form-input, .form-select {
        background-color: var(--bg-main);
        border: 1px solid var(--border);
        color: var(--text-main);
        padding: 0.65rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        width: 100%;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .form-input:focus, .form-select:focus {
        border-color: var(--primary);
    }

    .role-pills {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .role-pill {
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border);
        color: var(--text-muted);
        background: var(--bg-main);
        transition: all 0.2s ease;
    }

    .role-pill.active, .role-pill:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(249, 115, 22, 0.1);
    }

    .table-container {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    .emp-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .emp-table th {
        background-color: rgba(15, 23, 42, 0.5);
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    .emp-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .emp-table tr:last-child td {
        border-bottom: none;
    }

    .emp-table tr:hover td {
        background-color: rgba(255, 255, 255, 0.02);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #334155, #1e293b);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: var(--text-main);
        font-size: 0.95rem;
    }

    .user-name-title {
        font-weight: 700;
        color: #f8fafc;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .user-email-text {
        font-size: 0.8rem;
        color: var(--text-muted);
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

    .badge-pin-set {
        background-color: rgba(16, 185, 129, 0.15);
        color: #6ee7b7;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-pin-missing {
        background-color: rgba(239, 68, 68, 0.15);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .actions-cell {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .btn-edit {
        background: rgba(99, 102, 241, 0.15);
        color: #a5b4fc;
        border: 1px solid rgba(99, 102, 241, 0.3);
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-edit:hover {
        background: #6366f1;
        color: #fff;
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.15);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-delete:hover {
        background: var(--danger);
        color: #fff;
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
            <span>👥 Employee Management</span>
            <span style="font-size: 0.85rem; padding: 0.3rem 0.7rem; border-radius: 9999px; background: rgba(249, 115, 22, 0.15); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.3);">
                {{ $restaurant->name ?? 'Restaurant Staff' }}
            </span>
        </div>
        <p class="page-subtitle">
            Manage your restaurant team members, assign POS PINs for fast terminal unlocking, and control staff roles.
        </p>
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
                                        <span style="font-size: 0.7rem; background: rgba(148, 163, 184, 0.2); color: #94a3b8; padding: 0.1rem 0.4rem; border-radius: 4px;">(You)</span>
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
                            <span class="badge-pin-set">🔒 PIN Active (4-6 digits)</span>
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
                                <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-edit">Edit</a>
                            @endif

                            @if($canDelete)
                                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove {{ $employee->name }}? This action cannot be undone.');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">Delete</button>
                                </form>
                            @endif

                            @if(!$canEdit && !$canDelete)
                                <span style="font-size: 0.8rem; color: var(--text-muted);">Protected</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">👥</div>
                        <h4 style="color: #f8fafc; margin-bottom: 0.25rem;">No employees found</h4>
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
