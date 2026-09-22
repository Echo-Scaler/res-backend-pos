@extends('admin.layouts.app')

@section('title', 'Edit Employee - ' . $employee->name)

@push('styles')
<style>
    .form-container {
        max-width: 680px;
        margin: 0 auto;
    }

    .form-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-title {
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .form-subtitle {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-top: 0.35rem;
    }

    .card-box {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }

    .form-hint {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.4rem;
    }

    .form-control {
        width: 100%;
        background-color: var(--bg-main);
        border: 1px solid var(--border);
        color: var(--text-main);
        padding: 0.75rem 1rem;
        border-radius: 10px;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    @media (max-width: 640px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }

    .role-selection-box {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .role-option {
        position: relative;
    }

    .role-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .role-label {
        display: flex;
        flex-direction: column;
        padding: 1rem;
        border-radius: 10px;
        border: 1px solid var(--border);
        background-color: var(--bg-main);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .role-option input[type="radio"]:checked + .role-label {
        border-color: var(--primary);
        background-color: rgba(249, 115, 22, 0.08);
    }

    .role-label-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #f8fafc;
        margin-bottom: 0.25rem;
    }

    .role-label-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .btn-submit {
        background-color: var(--primary);
        color: #fff;
        padding: 0.8rem 1.75rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-submit:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-cancel {
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.8rem 1.25rem;
    }

    .btn-cancel:hover {
        color: var(--text-main);
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <div>
            <h1 class="form-title">✏️ Edit Employee Details</h1>
            <p class="form-subtitle">Update employee profile, contact information, role permissions, or POS terminal PIN.</p>
        </div>
        <a href="{{ route('admin.employees.index') }}" class="btn-cancel">← Back to List</a>
    </div>

    <div class="card-box">
        <form action="{{ route('admin.employees.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" required>
                    <div class="form-hint">Used for Back-Office dashboard login.</div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" placeholder="09-xxxxxxxxx">
                </div>
            </div>

            <div class="form-group">
                <label>Assigned Role *</label>
                <div class="role-selection-box">
                    @php
                        $currentRole = old('role', $employee->getRoleNames()->first() ?? 'STAFF');
                    @endphp
                    @foreach($allowedRoles as $role)
                        @php
                            $roleDesc = match($role) {
                                'OWNER' => 'Full executive control over restaurant operations',
                                'MANAGER' => 'Floor operations, voids & staff scheduling',
                                'CASHIER' => 'POS checkout, bill split & cash sessions',
                                'STAFF' => 'Tableside ordering & kitchen alerts',
                                default => 'General restaurant staff',
                            };
                            $roleIcon = match($role) {
                                'OWNER' => '👑',
                                'MANAGER' => '👔',
                                'CASHIER' => '💵',
                                'STAFF' => '🍽️',
                                default => '👤',
                            };
                        @endphp
                        <div class="role-option">
                            <input type="radio" id="role_{{ $role }}" name="role" value="{{ $role }}" {{ $currentRole === $role ? 'checked' : '' }}>
                            <label for="role_{{ $role }}" class="role-label">
                                <span class="role-label-title">{{ $roleIcon }} {{ $role }}</span>
                                <span class="role-label-desc">{{ $roleDesc }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="password">Change Password (Optional)</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Leave empty to keep current password">
                    <div class="form-hint">Only fill if you want to reset password.</div>
                </div>

                <div class="form-group">
                    <label for="pin_code">Update POS PIN (Optional)</label>
                    <input type="password" id="pin_code" name="pin_code" maxlength="6" pattern="[0-9]{4,6}" inputmode="numeric" class="form-control" placeholder="{{ $employee->pin_code ? 'PIN is set. Enter new PIN to change' : 'Enter 4-6 digit PIN' }}">
                    <div class="form-hint">For quick staff unlocking on POS touchscreens.</div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Update Employee</button>
                <a href="{{ route('admin.employees.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
