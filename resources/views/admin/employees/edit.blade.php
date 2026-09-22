@extends('admin.layouts.app')

@section('title', 'Edit Employee - ' . $employee->name)

@push('styles')
<style>
    .form-container {
        max-width: 820px;
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

    /* Custom Direct Permission Overrides Styling */
    .perm-override-container {
        margin-top: 2rem;
        padding-top: 1.75rem;
        border-top: 1px dashed var(--border);
    }

    .perm-override-header {
        margin-bottom: 1.25rem;
    }

    .perm-override-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #f8fafc;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .perm-override-desc {
        font-size: 0.82rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
        line-height: 1.4;
    }

    .perm-group-card {
        background-color: rgba(15, 23, 42, 0.4);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.2rem;
        margin-bottom: 1rem;
    }

    .perm-group-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #cbd5e1;
        margin-bottom: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .perm-override-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 0.75rem;
    }

    .perm-override-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0.85rem;
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 9px;
        transition: all 0.15s ease;
    }

    .perm-override-item:hover {
        border-color: rgba(249, 115, 22, 0.4);
    }

    .perm-override-checkbox {
        margin-top: 0.2rem;
        width: 1.1rem;
        height: 1.1rem;
        accent-color: var(--primary);
        cursor: pointer;
    }

    .perm-override-checkbox:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .perm-override-content {
        flex: 1;
    }

    .perm-override-name {
        font-size: 0.84rem;
        font-weight: 700;
        color: #f1f5f9;
        display: block;
    }

    .perm-override-desc-text {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
        display: block;
        line-height: 1.35;
    }

    .badge-role-inherited {
        font-size: 0.68rem;
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 4px;
        padding: 0.15rem 0.45rem;
        display: inline-block;
        margin-top: 0.35rem;
        font-weight: 600;
    }

    .badge-custom-granted {
        font-size: 0.68rem;
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 4px;
        padding: 0.15rem 0.45rem;
        display: inline-block;
        margin-top: 0.35rem;
        font-weight: 600;
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

            @if(isset($currentUser) && $currentUser->hasRole('OWNER') && $employee->hasAnyRole(['CASHIER', 'STAFF']))
            <div class="perm-override-container" id="direct-permissions-section">
                <input type="hidden" name="direct_permissions_override_submitted" value="1">
                <div class="perm-override-header">
                    <div class="perm-override-title">
                        <span>🛡️ User-Level Direct Permission Overrides</span>
                    </div>
                    <p class="perm-override-desc">
                        Grant or revoke direct individual permissions for <strong>{{ $employee->name }}</strong> without changing their base role (<strong>{{ $employee->getRoleNames()->first() ?? 'STAFF' }}</strong>).
                        (Applicable exclusively to Cashier and Staff operations). Permissions inherited from the assigned role are automatically active. You can grant extra operational privileges (e.g. allowing a trusted Cashier to apply custom discounts or manage inventory) below.
                    </p>
                </div>

                @if(isset($permissionGroups) && count($permissionGroups) > 0)
                    @foreach($permissionGroups as $groupName => $groupPerms)
                        <div class="perm-group-card">
                            <div class="perm-group-title">
                                @php
                                    $groupIcon = match($groupName) {
                                        'Menu, Catalog & Categories' => '🍔',
                                        'Inventory & Stocks' => '📦',
                                        'Discounts & Customer Coupons' => '🏷️',
                                        'Tables & Dining Floor' => '🪑',
                                        'Orders & Kitchen Operations' => '🧾',
                                        'Billing & Financial Sessions' => '💳',
                                        'Staff & Security Governance' => '👥',
                                        'Financials & Executive Governance' => '📈',
                                        default => '⚙️',
                                    };
                                @endphp
                                <span>{{ $groupIcon }} {{ $groupName }}</span>
                            </div>
                            <div class="perm-override-grid">
                                @foreach($groupPerms as $permName => $permDesc)
                                    @php
                                        $isRoleInherited = in_array($permName, $rolePermissions ?? []);
                                        $isDirectGranted = in_array($permName, $directPermissions ?? []);
                                    @endphp
                                    <label class="perm-override-item" for="perm_{{ \Illuminate\Support\Str::slug($permName) }}">
                                        @if($isRoleInherited)
                                            <input type="checkbox" id="perm_{{ \Illuminate\Support\Str::slug($permName) }}" checked disabled class="perm-override-checkbox">
                                        @else
                                            <input type="checkbox" id="perm_{{ \Illuminate\Support\Str::slug($permName) }}" name="direct_permissions[]" value="{{ $permName }}" {{ $isDirectGranted ? 'checked' : '' }} class="perm-override-checkbox">
                                        @endif
                                        <div class="perm-override-content">
                                            <span class="perm-override-name">{{ $permName }}</span>
                                            <span class="perm-override-desc-text">{{ $permDesc }}</span>
                                            @if($isRoleInherited)
                                                <span class="badge-role-inherited">🔵 Active by Base Role</span>
                                            @elseif($isDirectGranted)
                                                <span class="badge-custom-granted">🟢 Custom User Override</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn-submit">Update Employee</button>
                <a href="{{ route('admin.employees.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const permSection = document.getElementById('direct-permissions-section');

        if (permSection) {
            roleInputs.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    if (this.value === 'CASHIER' || this.value === 'STAFF') {
                        permSection.style.display = 'block';
                    } else {
                        permSection.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush
