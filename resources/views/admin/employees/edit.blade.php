@extends('admin.layouts.app')

@section('title', 'Edit Employee - ' . $employee->name)

@push('styles')
<style>
    .form-container {
        max-width: 860px;
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

    /* Custom Direct Permission Overrides Styling */
    .perm-override-container {
        margin-top: 2.25rem;
        padding-top: 2rem;
        border-top: 1px dashed var(--border-color);
    }

    .perm-override-header {
        margin-bottom: 1.35rem;
    }

    .perm-override-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .perm-override-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-main) !important;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .perm-header-badge {
        font-size: 0.725rem;
        font-weight: 700;
        color: var(--primary-hover);
        background: rgba(158, 198, 59, 0.15);
        border: 1px solid rgba(158, 198, 59, 0.3);
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
    }

    .perm-override-desc {
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-top: 0.45rem;
        line-height: 1.5;
        font-weight: 400;
    }

    .perm-group-card {
        background-color: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        transition: var(--transition);
    }

    [data-theme="dark"] .perm-group-card {
        background-color: #111724;
        border-color: #222d42;
    }

    .perm-group-title {
        font-size: 0.925rem;
        font-weight: 700;
        color: var(--text-main) !important;
        margin-bottom: 0.9rem;
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
        gap: 0.85rem;
        padding: 0.85rem 1rem;
        background-color: var(--bg-card);
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .perm-override-item:hover {
        border-color: var(--primary);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .perm-override-item.is-inherited {
        cursor: default;
        background-color: rgba(14, 38, 23, 0.02);
    }

    [data-theme="dark"] .perm-override-item.is-inherited {
        background-color: rgba(255, 255, 255, 0.02);
    }

    .perm-override-item.is-inherited:hover {
        border-color: var(--border-color);
        transform: none;
        box-shadow: none;
    }

    .perm-override-checkbox {
        margin-top: 0.25rem;
        width: 1.15rem;
        height: 1.15rem;
        accent-color: var(--primary);
        cursor: pointer;
        flex-shrink: 0;
    }

    .perm-override-checkbox:disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }

    .perm-override-content {
        flex: 1;
        min-width: 0;
    }

    .perm-override-name {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-main) !important;
        display: block;
        line-height: 1.3;
    }

    .perm-override-desc-text {
        font-size: 0.775rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
        display: block;
        line-height: 1.35;
        font-weight: 400;
    }

    .badge-role-inherited {
        font-size: 0.7rem;
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.25);
        border-radius: 6px;
        padding: 0.2rem 0.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        margin-top: 0.45rem;
        font-weight: 600;
    }

    [data-theme="dark"] .badge-role-inherited {
        background: rgba(59, 130, 246, 0.18);
        color: #93c5fd;
        border-color: rgba(59, 130, 246, 0.35);
    }

    .badge-custom-granted {
        font-size: 0.7rem;
        background: rgba(158, 198, 59, 0.12);
        color: #4d7c0f;
        border: 1px solid rgba(158, 198, 59, 0.3);
        border-radius: 6px;
        padding: 0.2rem 0.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        margin-top: 0.45rem;
        font-weight: 600;
    }

    [data-theme="dark"] .badge-custom-granted {
        background: rgba(158, 198, 59, 0.18);
        color: #bef264;
        border-color: rgba(158, 198, 59, 0.35);
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
                <span>✏️</span>
                <span>Edit Employee Details</span>
            </h1>
        </div>
        <a href="{{ route('admin.employees.index') }}" class="btn-back">
            <span>←</span>
            <span>Back to List</span>
        </a>
    </div>

    <div class="card-box">
        <form action="{{ route('admin.employees.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $employee->name) }}" placeholder="e.g. Carol Davis, Daw Aye Aye" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" placeholder="employee@restaurant.com" required>
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
                    <div class="perm-override-title-wrap">
                        <h2 class="perm-override-title">
                            <span>🛡️</span>
                            <span>User-Level Direct Permission Overrides</span>
                        </h2>
                    </div>
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
                                <span>{{ $groupIcon }}</span>
                                <span>{{ $groupName }}</span>
                            </div>
                            <div class="perm-override-grid">
                                @foreach($groupPerms as $permName => $permDesc)
                                    @php
                                        $isRoleInherited = in_array($permName, $rolePermissions ?? []);
                                        $isDirectGranted = in_array($permName, $directPermissions ?? []);
                                    @endphp
                                    <label class="perm-override-item {{ $isRoleInherited ? 'is-inherited' : '' }}" for="perm_{{ \Illuminate\Support\Str::slug($permName) }}">
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
                <button type="submit" class="btn-submit">
                    <span>💾</span>
                    <span>Update Employee</span>
                </button>
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
