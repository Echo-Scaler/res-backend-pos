@extends('admin.layouts.app')

@section('title', 'Admin Login')

@push('styles')
<style>
    .login-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 2.5rem;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .brand-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary), var(--olive-green));
        color: var(--dark-forest);
        font-size: 1.75rem;
        font-weight: 800;
        border-radius: 14px;
        margin-bottom: 1rem;
        box-shadow: 0 10px 15px -3px rgba(158, 198, 59, 0.25);
    }

    .login-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.35rem;
    }

    .login-subtitle {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .form-input {
        width: 100%;
        padding: 0.8rem 1rem;
        background-color: var(--bg-body);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-main);
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.2);
    }

    .form-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .btn-submit {
        width: 100%;
        padding: 0.9rem;
        background: var(--primary);
        color: var(--dark-forest);
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(158, 198, 59, 0.25);
    }

    .btn-submit:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .demo-credentials {
        margin-top: 1.75rem;
        padding: 1.25rem;
        background-color: rgba(15, 23, 42, 0.7);
        border: 1px dashed var(--border);
        border-radius: 12px;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .demo-title {
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 0.75rem;
        text-align: center;
    }

    .demo-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .btn-role-select {
        padding: 0.5rem 0.65rem;
        background-color: #1e293b;
        color: #cbd5e1;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: left;
    }

    .btn-role-select:hover {
        border-color: var(--primary);
        color: #fff;
        background-color: #334155;
    }
</style>
@endpush

@section('content')
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="brand-icon">🍽️</div>
            <h1 class="login-title">Restaurant Admin</h1>
            <p class="login-subtitle">Back-office management portal login</p>
        </div>

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    value="{{ old('email', 'admin@example.com') }}"
                    placeholder="name@restaurant.com"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    value="password123"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" value="1" checked>
                    <span>Remember me</span>
                </label>
                <span>POS Management</span>
            </div>

            <button type="submit" class="btn-submit">
                Sign In to Dashboard →
            </button>
        </form>

        <div class="demo-credentials">
            <div class="demo-title">Quick Select Role to Test:</div>
            <div class="demo-buttons">
                <button type="button" class="btn-role-select" onclick="selectAccount('admin@example.com', 'password123')">
                    👑 Owner (Full Access)
                </button>
                <button type="button" class="btn-role-select" onclick="selectAccount('manager@example.com', 'password123')">
                    👔 Manager (Restricted)
                </button>
                <button type="button" class="btn-role-select" onclick="selectAccount('cashier@example.com', 'password123')">
                    💵 Cashier (POS Only)
                </button>
                <button type="button" class="btn-role-select" onclick="selectAccount('staff@example.com', 'password123')">
                    🍽️ Staff (Waiter Only)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function selectAccount(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
@endsection
