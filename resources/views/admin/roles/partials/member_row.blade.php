@php
    $currentRoleName = $member->getRoleNames()->first() ?? 'STAFF';
    $userInitials = strtoupper(substr($member->name, 0, 1));
    if (str_contains($member->name, ' ')) {
        $parts = explode(' ', $member->name);
        $userInitials = strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
    }

    $roleSubtitles = [
        'OWNER' => 'Restaurant Owner & Executive',
        'MANAGER' => 'Operations & Floor Supervisor',
        'CASHIER' => 'POS Register & Shift Cashier',
        'STAFF' => 'Dining Waiter & Guest Server',
    ];
    $subtitle = $roleSubtitles[$currentRoleName] ?? 'Team Member';
@endphp

<tr class="member-row" id="user-row-{{ $member->id }}">
    <!-- Checkbox -->
    <td class="col-checkbox">
        <input type="checkbox" class="custom-chk member-checkbox" value="{{ $member->id }}">
    </td>

    <!-- Avatar & Name / Title -->
    <td>
        <div class="user-profile-cell">
            <div class="avatar-wrapper">
                <div class="avatar-initials" style="background: linear-gradient(135deg, {{ $avatarBg }}, #475569);">
                    {{ $userInitials }}
                </div>
                <span class="status-dot active" title="Active Account"></span>
            </div>
            <div>
                <div class="user-meta-name">{{ $member->name }}</div>
                <div class="user-meta-role">
                    <span>{{ $subtitle }}</span>
                    @if(in_array($currentRoleName, ['CASHIER', 'STAFF']))
                        @php
                            $directPerms = $member->getDirectPermissions();
                        @endphp
                        @if($directPerms->isNotEmpty())
                            <span class="custom-override-pill" title="Custom Granted: {{ $directPerms->pluck('name')->implode(', ') }}">
                                ⚡ +{{ $directPerms->count() }} {{ \Illuminate\Support\Str::plural('Override', $directPerms->count()) }}
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </td>

    <!-- Created At Date -->
    <td class="date-cell">
        {{ $member->created_at?->format('M d, Y') ?? 'Jan 26, 2025' }}
    </td>

    <!-- Email & Copy Button -->
    <td>
        <div class="email-cell">
            <span class="email-address">{{ $member->email }}</span>
            <button type="button" class="copy-email-btn" onclick="copyEmailToClipboard('{{ $member->email }}', this)" title="Copy email to clipboard">
                <i class="ti ti-copy"></i>
                <span>Copy</span>
            </button>
        </div>
    </td>

    <!-- Inline Role Selector -->
    <td>
        <div class="role-select-box">
            <select
                class="role-select-native"
                data-current-role="{{ $currentRoleName }}"
                onchange="updateMemberRole(this, {{ $member->id }}, '{{ addslashes($member->name) }}')"
                {{ ($currentUser->id === $member->id && $currentRoleName === 'OWNER') ? 'disabled title="Cannot change your own Owner role."' : '' }}
            >
                <option value="OWNER" {{ $currentRoleName === 'OWNER' ? 'selected' : '' }}>👑 Owner</option>
                <option value="MANAGER" {{ $currentRoleName === 'MANAGER' ? 'selected' : '' }}>💼 Manager</option>
                <option value="CASHIER" {{ $currentRoleName === 'CASHIER' ? 'selected' : '' }}>💳 Cashier</option>
                <option value="STAFF" {{ $currentRoleName === 'STAFF' ? 'selected' : '' }}>🍽️ Staff</option>
            </select>
            <i class="ti ti-chevron-down role-select-chevron"></i>
        </div>
    </td>

    <!-- Status Pill -->
    <td>
        <span class="status-pill active">Active</span>
    </td>

    <!-- 3-Dots Action Menu -->
    <td class="col-actions">
        <div class="action-dropdown">
            <button type="button" class="btn-action-dots" onclick="toggleActionMenu(event, 'action-menu-{{ $member->id }}')">
                <i class="ti ti-dots-vertical"></i>
            </button>
            <div class="action-dropdown-menu" id="action-menu-{{ $member->id }}">
                <a href="{{ route('admin.employees.edit', $member->id) }}" class="action-menu-item">
                    <i class="ti ti-edit"></i>
                    <span>Edit Profile</span>
                </a>
                @if(in_array($currentRoleName, ['CASHIER', 'STAFF']))
                    <a href="{{ route('admin.employees.edit', $member->id) }}#direct-permissions-section" class="action-menu-item">
                        <i class="ti ti-shield-check"></i>
                        <span>Customize Permissions</span>
                    </a>
                @endif
                <a href="{{ route('admin.employees.edit', $member->id) }}" class="action-menu-item">
                    <i class="ti ti-key"></i>
                    <span>Reset PIN / Password</span>
                </a>
                @if($currentUser->id !== $member->id && $currentRoleName !== 'OWNER')
                    <form action="{{ route('admin.employees.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Remove access for {{ addslashes($member->name) }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-menu-item danger">
                            <i class="ti ti-trash"></i>
                            <span>Revoke Access</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </td>
</tr>
