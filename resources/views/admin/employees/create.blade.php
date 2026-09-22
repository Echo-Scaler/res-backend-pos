@extends('admin.layouts.app')

@section('title', 'Add New Employee')

@push('styles')
<style>
    .form-container {
        max-width: 740px;
        margin: 0 auto;
    }

    .form-header {
        margin-bottom: 1.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .form-title {
        font-family: "Mada", sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-subtitle {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 400;
        margin-top: 0.25rem;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background-color: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        padding: 0.55rem 1.1rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: var(--transition);
    }

    .btn-back:hover {
        background-color: var(--bg-hover);
        border-color: var(--primary);
        color: var(--text-main);
    }

    .card-box {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        padding: 2.25rem;
        transition: var(--transition);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.45rem;
    }

    .form-hint {
        font-size: 0.775rem;
        color: var(--text-muted);
        margin-top: 0.4rem;
        font-weight: 400;
    }

    .form-control {
        width: 100%;
        background-color: var(--bg-body);
        border: 1.5px solid var(--border-color);
        color: var(--text-main);
        padding: 0.75rem 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.9375rem;
        font-family: "Mada", sans-serif;
        font-weight: 400;
        outline: none;
        transition: var(--transition);
    }

    .form-control:focus {
        border-color: var(--primary);
        background-color: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.2);
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

    /* Role Selection Modern Cards */
    .role-selection-box {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.875rem;
        margin-top: 0.5rem;
    }

    .role-option {
        position: relative;
    }

    .role-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .role-label {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 1.1rem 1.15rem;
        border-radius: var(--radius-md);
        border: 1.5px solid var(--border-color);
        background-color: var(--bg-card);
        cursor: pointer;
        position: relative;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .role-label:hover {
        border-color: rgba(158, 198, 59, 0.6);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    .role-option input[type="radio"]:checked + .role-label {
        border-color: var(--primary);
        background-color: rgba(158, 198, 59, 0.08);
        box-shadow: 0 0 0 1px var(--primary), var(--shadow-sm);
    }

    .role-label-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.35rem;
    }

    .role-label-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-main) !important;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .role-check-indicator {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1.5px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .role-check-indicator::after {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: transparent;
        transition: all 0.2s ease;
    }

    .role-option input[type="radio"]:checked + .role-label .role-check-indicator {
        border-color: var(--primary);
        background-color: var(--primary);
    }

    .role-option input[type="radio"]:checked + .role-label .role-check-indicator::after {
        background-color: #ffffff;
    }

    .role-label-desc {
        font-size: 0.775rem;
        color: var(--text-muted);
        line-height: 1.4;
        font-weight: 400;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 2.25rem;
        padding-top: 1.75rem;
        border-top: 1px solid var(--border-color);
        flex-wrap: wrap;
    }

    .btn-submit {
        background: linear-gradient(135deg, #9ec63b, #7ea826);
        color: #ffffff;
        padding: 0.75rem 1.75rem;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(158, 198, 59, 0.28);
        transition: all 0.2s ease;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(158, 198, 59, 0.38);
        filter: brightness(1.03);
    }

    .btn-cancel {
        background-color: var(--bg-hover);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.75rem 1.5rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .btn-cancel:hover {
        background-color: var(--border-color);
        color: var(--text-main);
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <div>
            <h1 class="form-title">
                <span>➕</span>
                <span>Add New Employee</span>
            </h1>
            <p class="form-subtitle">Add a manager, cashier, or floor staff member to your restaurant POS system.</p>
        </div>
        <a href="{{ route('admin.employees.index') }}" class="btn-back">
            <span>←</span>
            <span>Back to List</span>
        </a>
    </div>

    <div class="card-box">
        <form action="{{ route('admin.employees.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Daw Aye Aye, Ko Min Min" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="employee@restaurant.com" required>
                    <div class="form-hint">Used for Back-Office dashboard login.</div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="09-xxxxxxxxx">
                </div>
            </div>

            <div class="form-group">
                <label>Assigned Role *</label>
                <div class="role-selection-box">
                    @foreach($allowedRoles as $role)
                        @php
                            $roleDesc = match($role) {
                                'MANAGER' => 'Floor operations, voids & staff scheduling',
                                'CASHIER' => 'POS checkout, bill split & cash sessions',
                                'STAFF' => 'Tableside ordering & kitchen alerts',
                                default => 'General restaurant staff',
                            };
                            $roleIcon = match($role) {
                                'MANAGER' => '👔',
                                'CASHIER' => '💵',
                                'STAFF' => '🍽️',
                                default => '👤',
                            };
                        @endphp
                        <div class="role-option">
                            <input type="radio" id="role_{{ $role }}" name="role" value="{{ $role }}" {{ (old('role') === $role || (empty(old('role')) && $loop->last)) ? 'checked' : '' }}>
                            <label for="role_{{ $role }}" class="role-label">
                                <div class="role-label-header">
                                    <span class="role-label-title">{{ $roleIcon }} {{ $role }}</span>
                                    <span class="role-check-indicator"></span>
                                </div>
                                <span class="role-label-desc">{{ $roleDesc }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="password">Login Password *</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="At least 6 characters" required>
                    <div class="form-hint">Web login credentials.</div>
                </div>

                <div class="form-group">
                    <label for="pin_code">POS Quick PIN (4-6 digits)</label>
                    <input type="password" id="pin_code" name="pin_code" maxlength="6" pattern="[0-9]{4,6}" inputmode="numeric" class="form-control" placeholder="e.g. 1234 or 8899" value="{{ old('pin_code') }}">
                    <div class="form-hint">For quick staff unlocking on POS touchscreens.</div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span>💾</span>
                    <span>Save & Create Employee</span>
                </button>
                <a href="{{ route('admin.employees.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
