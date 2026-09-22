@extends('admin.layouts.app')

@section('title', 'Roles & Permissions Workspace')

@push('styles')
<style>
    /* Roles Workspace Root Styles */
    .role-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .role-page-title-group h1 {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .role-page-title-group p {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 0.35rem;
    }

    .role-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-invite {
        background: linear-gradient(135deg, var(--primary), #ea580c);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.28);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-invite:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(var(--primary-rgb), 0.38);
    }

    /* Stat Cards Row */
    .role-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .role-stat-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .role-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-card-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .icon-owner { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .icon-manager { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .icon-cashier { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    .icon-staff { background: rgba(249, 115, 22, 0.12); color: #f97316; }

    .stat-card-info .label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-muted);
    }

    .stat-card-info .value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.2;
        margin-top: 0.15rem;
    }

    /* Workspace Tabs Bar */
    .workspace-tabs-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .workspace-tabs {
        display: flex;
        gap: 0.5rem;
    }

    .workspace-tab-btn {
        background: transparent;
        border: none;
        padding: 0.75rem 1.2rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: color 0.2s ease;
    }

    .workspace-tab-btn:hover {
        color: var(--text-main);
    }

    .workspace-tab-btn.active {
        color: var(--primary);
        font-weight: 700;
    }

    .workspace-tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2.5px;
        background: var(--primary);
        border-radius: 2px 2px 0 0;
    }

    .tab-badge {
        font-size: 0.75rem;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
    }

    .workspace-tab-btn.active .tab-badge {
        background: rgba(var(--primary-rgb), 0.15);
        color: var(--primary);
        border-color: transparent;
    }

    .table-search-box {
        position: relative;
        min-width: 260px;
    }

    .table-search-box input {
        width: 100%;
        padding: 0.55rem 1rem 0.55rem 2.4rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-main);
        font-size: 0.85rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .table-search-box input:focus {
        border-color: var(--primary);
    }

    .table-search-box i {
        position: absolute;
        left: 0.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    /* Main Grouped Table Panel (Direct Mockup Representation) */
    .role-table-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 16px;
    }

    .modern-role-table {
        width: 100%;
        min-width: 880px;
        border-collapse: collapse;
        text-align: left;
    }

    /* Header Columns */
    .modern-role-table thead th {
        background: var(--bg-card);
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 600;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .col-checkbox {
        width: 48px;
        padding-left: 1.5rem !important;
        padding-right: 0.5rem !important;
        text-align: center;
    }

    .col-actions {
        width: 50px;
        text-align: center;
        padding-right: 1.5rem !important;
    }

    .sort-indicator {
        display: inline-flex;
        align-items: center;
        margin-left: 0.35rem;
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    /* Section Category Header Rows */
    .section-header-row td {
        background-color: var(--bg-body);
        padding: 0.65rem 1.25rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-main);
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .section-title-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .section-info-icon {
        color: var(--text-muted);
        font-size: 0.9rem;
        cursor: pointer;
    }

    /* Table Data Rows */
    .modern-role-table tbody tr.member-row {
        border-bottom: 1px solid var(--border-color);
        transition: background-color 0.15s ease;
    }

    .modern-role-table tbody tr.member-row:last-child {
        border-bottom: none;
    }

    .modern-role-table tbody tr.member-row:hover {
        background-color: rgba(var(--primary-rgb), 0.03);
    }

    .modern-role-table td {
        padding: 1.1rem 1.25rem;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    /* Custom Checkbox */
    .custom-chk {
        width: 17px;
        height: 17px;
        border-radius: 4px;
        border: 1.5px solid var(--border-color);
        accent-color: var(--primary);
        cursor: pointer;
    }

    /* User Profile & Avatar */
    .user-profile-cell {
        display: flex;
        align-items: center;
        gap: 0.9rem;
    }

    .avatar-wrapper {
        position: relative;
        width: 42px;
        height: 42px;
        flex-shrink: 0;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .avatar-initials {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        color: #ffffff;
    }

    .avatar-invited {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 1.5px dashed var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        background: var(--bg-body);
    }

    .status-dot {
        position: absolute;
        bottom: 0px;
        right: 0px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid var(--bg-card);
    }

    .status-dot.active { background-color: #10b981; }
    .status-dot.pending { background-color: #f59e0b; }

    .user-meta-name {
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.25;
        font-size: 0.93rem;
    }

    .user-meta-role {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .custom-override-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.28);
        border-radius: 9999px;
        padding: 0.08rem 0.45rem;
        font-size: 0.68rem;
        font-weight: 700;
        cursor: help;
        transition: all 0.2s ease;
    }

    .custom-override-pill:hover {
        background: rgba(16, 185, 129, 0.2);
        border-color: rgba(16, 185, 129, 0.45);
    }

    /* Date Cell */
    .date-cell {
        color: var(--text-muted);
        font-size: 0.85rem;
        white-space: nowrap;
    }

    /* Email & Copy Button */
    .email-cell {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .email-address {
        color: var(--text-main);
        font-weight: 600;
        font-size: 0.88rem;
    }

    .copy-email-btn {
        background: none;
        border: none;
        padding: 0;
        color: #2563eb;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: color 0.15s ease;
        text-align: left;
        width: fit-content;
    }

    .copy-email-btn:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    .copy-email-btn.copied {
        color: #10b981;
    }

    /* Role Dropdown Selector */
    .role-select-box {
        position: relative;
        display: inline-block;
    }

    .role-select-native {
        appearance: none;
        -webkit-appearance: none;
        background: transparent;
        border: 1px solid transparent;
        padding: 0.45rem 1.8rem 0.45rem 0.6rem;
        border-radius: 8px;
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--text-main);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .role-select-native:hover, .role-select-native:focus {
        background: var(--bg-body);
        border-color: var(--border-color);
        outline: none;
    }

    .role-select-chevron {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .status-pill.active {
        background: #ecfdf5;
        color: #059669;
    }

    .status-pill.pending {
        background: #fef3c7;
        color: #d97706;
    }

    [data-theme="dark"] .status-pill.active {
        background: rgba(16, 185, 129, 0.18);
        color: #34d399;
    }

    [data-theme="dark"] .status-pill.pending {
        background: rgba(245, 158, 11, 0.18);
        color: #fbbf24;
    }

    /* Action Menu 3-Dots */
    .action-dropdown {
        position: relative;
    }

    .btn-action-dots {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.15rem;
        cursor: pointer;
        padding: 0.35rem 0.5rem;
        border-radius: 6px;
        transition: all 0.15s ease;
    }

    .btn-action-dots:hover {
        background: var(--bg-body);
        color: var(--text-main);
    }

    .action-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 4px);
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
        min-width: 185px;
        z-index: 100;
        padding: 0.4rem;
    }

    .action-dropdown-menu.dropdown-menu-up {
        top: auto;
        bottom: calc(100% + 6px);
        box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.25), 0 -8px 10px -6px rgba(0, 0, 0, 0.2);
    }

    .action-dropdown-menu.show {
        display: block;
    }

    .action-menu-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.84rem;
        color: var(--text-main);
        text-decoration: none;
        border-radius: 6px;
        transition: background-color 0.15s ease;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .action-menu-item:hover {
        background-color: var(--bg-body);
        color: var(--primary);
    }

    .action-menu-item.danger:hover {
        color: #ef4444;
        background-color: rgba(239, 68, 68, 0.08);
    }

    /* Permissions Matrix Tab */
    .matrix-panel {
        display: none;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
    }

    .matrix-panel.active {
        display: block;
    }

    .matrix-table {
        width: 100%;
        min-width: 820px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .matrix-table thead th {
        position: sticky;
        top: var(--header-height);
        background: var(--bg-card);
        z-index: 15;
        padding: 1.15rem 1rem;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-main);
        border-bottom: 2px solid var(--border-color);
        box-shadow: 0 3px 6px -2px rgba(0, 0, 0, 0.08);
    }

    .matrix-table td {
        padding: 0.95rem 1rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .matrix-table tbody tr:hover {
        background-color: rgba(var(--primary-rgb), 0.03);
    }

    .matrix-group-header {
        background: rgba(var(--primary-rgb), 0.06) !important;
        font-weight: 800 !important;
        color: var(--primary) !important;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 0.82rem !important;
        padding: 0.75rem 1.25rem !important;
    }

    /* Interactive Permission Buttons */
    .perm-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        min-width: 95px;
    }

    .perm-action-btn.granted {
        background: #ecfdf5;
        color: #059669;
        border-color: rgba(16, 185, 129, 0.3);
    }

    [data-theme="dark"] .perm-action-btn.granted {
        background: rgba(16, 185, 129, 0.18);
        color: #34d399;
    }

    .perm-action-btn.granted:hover {
        background: #fee2e2;
        color: #dc2626;
        border-color: rgba(239, 68, 68, 0.4);
    }

    [data-theme="dark"] .perm-action-btn.granted:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
    }

    .perm-action-btn.granted:hover span {
        display: none;
    }
    .perm-action-btn.granted:hover::after {
        content: 'Revoke ✕';
    }

    .perm-action-btn.restricted {
        background: var(--bg-body);
        color: var(--text-muted);
        border-color: var(--border-color);
        opacity: 0.75;
    }

    .perm-action-btn.restricted:hover {
        background: #ecfdf5;
        color: #059669;
        border-color: #10b981;
        opacity: 1;
    }

    [data-theme="dark"] .perm-action-btn.restricted:hover {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
    }

    .perm-action-btn.restricted:hover span {
        display: none;
    }
    .perm-action-btn.restricted:hover::after {
        content: 'Grant +';
    }

    .perm-action-btn.locked {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        cursor: not-allowed;
        border-color: transparent;
        font-weight: 700;
    }

    .role-col-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 800;
    }
    .role-col-owner { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .role-col-manager { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .role-col-cashier { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    .role-col-staff { background: rgba(249, 115, 22, 0.12); color: #f97316; }

    /* Role Definitions Cards Tab */
    .cards-panel {
        display: none;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .cards-panel.active {
        display: grid;
    }

    .role-def-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow-sm);
    }

    .role-def-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .role-badge-tag {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
    }

    /* Interactive Feedback Toast */
    .role-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #1e293b;
        color: #ffffff;
        padding: 0.85rem 1.25rem;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 9999;
    }

    .role-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    /* Responsive Media Queries */
    @media (max-width: 992px) {
        .role-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .table-search-box {
            min-width: 220px;
        }
    }

    @media (max-width: 768px) {
        .role-page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .role-header-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .workspace-tabs-container {
            flex-direction: column;
            align-items: stretch;
            gap: 0.85rem;
        }

        .workspace-tabs {
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 0.35rem;
            -webkit-overflow-scrolling: touch;
        }

        .workspace-tab-btn {
            white-space: nowrap;
        }

        .table-search-box {
            width: 100%;
            min-width: 100%;
        }

        .role-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .role-stat-card {
            padding: 1rem;
        }

        .stat-card-info .value {
            font-size: 1.3rem;
        }

        .role-table-panel, .matrix-panel {
            border-radius: 12px;
        }
    }

    @media (max-width: 480px) {
        .role-stats-grid {
            grid-template-columns: 1fr;
        }

        .role-page-title-group h1 {
            font-size: 1.4rem;
        }
    }
</style>
@endpush

@section('content')
<div class="roles-workspace-wrapper">

    <!-- Top Page Header -->
    <div class="role-page-header">
        <div class="role-page-title-group">
            <h1>
                <i class="ti ti-shield-lock" style="color: var(--primary);"></i>
                Roles & Permissions
            </h1>

        </div>
        <div class="role-header-actions">
            <a href="{{ route('admin.employees.create') }}" class="btn-invite">
                <i class="ti ti-user-plus"></i>
                <span>Add / Invite Member</span>
            </a>
        </div>
    </div>

    <!-- Live Metrics Cards -->
    <div class="role-stats-grid">
        <div class="role-stat-card">
            <div class="stat-card-icon icon-owner">
                <i class="ti ti-crown"></i>
            </div>
            <div class="stat-card-info">
                <div class="label">Executive Owners</div>
                <div class="value">{{ $stats['owner_count'] }}</div>
            </div>
        </div>
        <div class="role-stat-card">
            <div class="stat-card-icon icon-manager">
                <i class="ti ti-briefcase"></i>
            </div>
            <div class="stat-card-info">
                <div class="label">Managers</div>
                <div class="value">{{ $stats['manager_count'] }}</div>
            </div>
        </div>
        <div class="role-stat-card">
            <div class="stat-card-icon icon-cashier">
                <i class="ti ti-receipt-2"></i>
            </div>
            <div class="stat-card-info">
                <div class="label">POS Cashiers</div>
                <div class="value">{{ $stats['cashier_count'] }}</div>
            </div>
        </div>
        <div class="role-stat-card">
            <div class="stat-card-icon icon-staff">
                <i class="ti ti-soup"></i>
            </div>
            <div class="stat-card-info">
                <div class="label">Dining Waiters</div>
                <div class="value">{{ $stats['staff_count'] }}</div>
            </div>
        </div>
    </div>

    <!-- Workspace Tabs Navigation -->
    <div class="workspace-tabs-container">
        <div class="workspace-tabs">
            <button type="button" class="workspace-tab-btn active" onclick="switchWorkspaceTab('team-table', this)">
                <i class="ti ti-users"></i>
                <span>Team Role Assignment</span>
                <span class="tab-badge">{{ $stats['total_users'] }}</span>
            </button>
            <button type="button" class="workspace-tab-btn" onclick="switchWorkspaceTab('permissions-matrix', this)">
                <i class="ti ti-matrix"></i>
                <span>Permissions Matrix (RBAC)</span>
                <span class="tab-badge">{{ $stats['total_permissions'] }}</span>
            </button>
            <button type="button" class="workspace-tab-btn" onclick="switchWorkspaceTab('role-definitions', this)">
                <i class="ti ti-info-circle"></i>
                <span>Role Authorities</span>
            </button>
        </div>

        <div class="table-search-box" id="searchBoxContainer">
            <i class="ti ti-search"></i>
            <input type="text" id="roleTableSearch" placeholder="Search name or email..." onkeyup="filterMemberTable()">
        </div>
    </div>

    <!-- TAB 1: TEAM ROLE ASSIGNMENT (Direct Reference Mockup Implementation) -->
    <div id="tab-team-table" class="role-table-panel">
        <div class="table-responsive">
            <table class="modern-role-table" id="membersTable">
            <thead>
                <tr>
                    <th class="col-checkbox">
                        <input type="checkbox" class="custom-chk" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                    </th>
                    <th>User <span class="sort-indicator"><i class="ti ti-arrows-sort"></i></span></th>
                    <th>Created at <span class="sort-indicator"><i class="ti ti-arrows-sort"></i></span></th>
                    <th>Email address</th>
                    <th>Type / Role <span class="sort-indicator"><i class="ti ti-arrows-sort"></i></span></th>
                    <th>Status <span class="sort-indicator"><i class="ti ti-arrows-sort"></i></span></th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>

                <!-- SECTION 1: EXECUTIVE & OWNER -->
                <tr class="section-header-row">
                    <td colspan="7">
                        <span class="section-title-badge">
                            <span>Owner & Executive</span>
                            <i class="ti ti-info-circle section-info-icon" title="Full unrestricted authority over financial data, employee management, and POS settings."></i>
                        </span>
                    </td>
                </tr>
                @forelse($groupedUsers['OWNER'] as $user)
                    @include('admin.roles.partials.member_row', ['member' => $user, 'section' => 'Owner', 'avatarBg' => '#ef4444'])
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No executive owner found.</td>
                    </tr>
                @endforelse

                <!-- SECTION 2: OPERATIONS MANAGERS -->
                <tr class="section-header-row">
                    <td colspan="7">
                        <span class="section-title-badge">
                            <span>Management & Supervisors ({{ $groupedUsers['MANAGER']->count() }})</span>
                            <i class="ti ti-info-circle section-info-icon" title="Supervises dining floor operations, table assignments, and staff."></i>
                        </span>
                    </td>
                </tr>
                @forelse($groupedUsers['MANAGER'] as $user)
                    @include('admin.roles.partials.member_row', ['member' => $user, 'section' => 'Manager', 'avatarBg' => '#3b82f6'])
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No managers appointed yet.</td>
                    </tr>
                @endforelse

                <!-- SECTION 3: FRONT COUNTER CASHIERS -->
                <tr class="section-header-row">
                    <td colspan="7">
                        <span class="section-title-badge">
                            <span>POS Cashiers ({{ $groupedUsers['CASHIER']->count() }})</span>
                            <i class="ti ti-info-circle section-info-icon" title="Operates checkout register, customer billing, and shift cash drawer reconciliation."></i>
                        </span>
                    </td>
                </tr>
                @forelse($groupedUsers['CASHIER'] as $user)
                    @include('admin.roles.partials.member_row', ['member' => $user, 'section' => 'Cashier', 'avatarBg' => '#10b981'])
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No cashiers registered yet.</td>
                    </tr>
                @endforelse

                <!-- SECTION 4: DINING SERVICE & WAITERS -->
                <tr class="section-header-row">
                    <td colspan="7">
                        <span class="section-title-badge">
                            <span>Dining Floor & Waiters ({{ $groupedUsers['STAFF']->count() }})</span>
                            <i class="ti ti-info-circle section-info-icon" title="Takes tableside guest orders, submits kitchen tickets, and monitors table statuses."></i>
                        </span>
                    </td>
                </tr>
                @forelse($groupedUsers['STAFF'] as $user)
                    @include('admin.roles.partials.member_row', ['member' => $user, 'section' => 'Staff', 'avatarBg' => '#f97316'])
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No dining floor staff registered yet.</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
        </div>
    </div>

    <!-- TAB 2: PERMISSIONS MATRIX (RBAC) -->
    <div id="tab-permissions-matrix" class="matrix-panel">
        <div style="padding: 1rem 1.25rem; background: var(--bg-body); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div style="font-size: 0.88rem; color: var(--text-muted);">
                <i class="ti ti-info-circle" style="color: var(--primary);"></i>
                Click on any role button below to <strong>Grant (+)</strong> or <strong>Revoke (✕)</strong> permissions dynamically.
            </div>
            <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-muted);">
                Sticky Table Navigation Enabled
            </div>
        </div>
        <div class="table-responsive">
        <table class="matrix-table">
            <thead>
                <tr>
                    <th style="width: 36%; padding-left: 1.5rem;">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ti ti-shield-check" style="color: var(--primary); font-size: 1.2rem;"></i>
                            <span>Fine-Grained Spatie RBAC Permission</span>
                        </span>
                    </th>
                    <th style="width: 16%; text-align: center;">
                        <span class="role-col-badge role-col-owner" title="Executive Authority">👑 OWNER</span>
                    </th>
                    <th style="width: 16%; text-align: center;">
                        <span class="role-col-badge role-col-manager" title="Floor Operations">💼 MANAGER</span>
                    </th>
                    <th style="width: 16%; text-align: center;">
                        <span class="role-col-badge role-col-cashier" title="POS Register & Billing">💳 CASHIER</span>
                    </th>
                    <th style="width: 16%; text-align: center;">
                        <span class="role-col-badge role-col-staff" title="Tableside Service">🍽️ STAFF</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissionGroups as $groupName => $perms)
                    <tr>
                        <td colspan="5" class="matrix-group-header">
                            <i class="ti ti-folder-check" style="margin-right: 0.4rem;"></i> {{ $groupName }}
                        </td>
                    </tr>
                    @foreach($perms as $permKey => $permDescription)
                        <tr>
                            <td style="padding-left: 1.5rem;">
                                <div style="font-weight: 700; color: var(--text-main); font-size: 0.92rem;">{{ $permKey }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">{{ $permDescription }}</div>
                            </td>
                            @foreach(['OWNER', 'MANAGER', 'CASHIER', 'STAFF'] as $rName)
                                <td style="text-align: center;">
                                    @php
                                        $has = in_array($permKey, $rolePermissionsMap[$rName] ?? []);
                                    @endphp
                                    @if($rName === 'OWNER')
                                        <span class="perm-action-btn locked" title="Owner permissions are permanently active">
                                            <i class="ti ti-lock"></i>
                                            <span>Master</span>
                                        </span>
                                    @else
                                        <button
                                            type="button"
                                            class="perm-action-btn {{ $has ? 'granted' : 'restricted' }}"
                                            data-role="{{ $rName }}"
                                            data-perm="{{ $permKey }}"
                                            onclick="toggleRolePermission('{{ $rName }}', '{{ $permKey }}', this)"
                                            title="{{ $has ? 'Click to Revoke from ' . $rName : 'Click to Grant to ' . $rName }}"
                                        >
                                            <i class="ti {{ $has ? 'ti-check' : 'ti-plus' }}"></i>
                                            <span>{{ $has ? 'Granted' : 'Restricted' }}</span>
                                        </button>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <!-- TAB 3: ROLE DEFINITIONS & POLICIES -->
    <div id="tab-role-definitions" class="cards-panel">
        <div class="role-def-card">
            <div>
                <div class="role-def-card-header">
                    <h3 style="font-size: 1.15rem; font-weight: 800;">👑 OWNER</h3>
                    <span class="role-badge-tag" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">Executive</span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.5; margin-bottom: 1.25rem;">
                    Full unrestricted administrative ownership. Can view financial turnover reports, create & demote managers, configure POS hardware, tax rates, and initiate business master resets.
                </p>
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">Key Authorities:</div>
                <ul style="font-size: 0.84rem; color: var(--text-muted); padding-left: 1.2rem; line-height: 1.6;">
                    <li>Unrestricted 100% RBAC access</li>
                    <li>Audit trail & security device monitoring</li>
                    <li>Direct access to Back-Office configuration</li>
                </ul>
            </div>
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 0.82rem; color: var(--text-muted);">
                Assigned to <strong>{{ $stats['owner_count'] }}</strong> active account(s)
            </div>
        </div>

        <div class="role-def-card">
            <div>
                <div class="role-def-card-header">
                    <h3 style="font-size: 1.15rem; font-weight: 800;">💼 MANAGER</h3>
                    <span class="role-badge-tag" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">Supervision</span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.5; margin-bottom: 1.25rem;">
                    Supervises restaurant operations, manages food menus, table zones, and recruits cashiers and waiters. Restricted from executive tax rates and financial analytics.
                </p>
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">Key Authorities:</div>
                <ul style="font-size: 0.84rem; color: var(--text-muted); padding-left: 1.2rem; line-height: 1.6;">
                    <li>Menu item pricing & modifier updates</li>
                    <li>Cashier & waiter staff onboarding</li>
                    <li>Dining room floor layout rearrangement</li>
                </ul>
            </div>
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 0.82rem; color: var(--text-muted);">
                Assigned to <strong>{{ $stats['manager_count'] }}</strong> active account(s)
            </div>
        </div>

        <div class="role-def-card">
            <div>
                <div class="role-def-card-header">
                    <h3 style="font-size: 1.15rem; font-weight: 800;">💳 CASHIER</h3>
                    <span class="role-badge-tag" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">Checkout Counter</span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.5; margin-bottom: 1.25rem;">
                    Responsible for counter billing, POS register checkout, receiving KBZPay/WavePay QR settlements, cash drawer opening, and printing customer receipts.
                </p>
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">Key Authorities:</div>
                <ul style="font-size: 0.84rem; color: var(--text-muted); padding-left: 1.2rem; line-height: 1.6;">
                    <li>POS register checkout & cash drawer kicks</li>
                    <li>Split bill settlement & payment invoicing</li>
                    <li>Cash shift reconciliation</li>
                </ul>
            </div>
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 0.82rem; color: var(--text-muted);">
                Assigned to <strong>{{ $stats['cashier_count'] }}</strong> active account(s)
            </div>
        </div>

        <div class="role-def-card">
            <div>
                <div class="role-def-card-header">
                    <h3 style="font-size: 1.15rem; font-weight: 800;">🍽️ STAFF</h3>
                    <span class="role-badge-tag" style="background: rgba(249, 115, 22, 0.12); color: #f97316;">Dining Service</span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.5; margin-bottom: 1.25rem;">
                    Floor service waiters handling tablet/mobile tableside ordering, transmitting orders to the kitchen KDS, and serving guests.
                </p>
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">Key Authorities:</div>
                <ul style="font-size: 0.84rem; color: var(--text-muted); padding-left: 1.2rem; line-height: 1.6;">
                    <li>Live tableside guest ordering</li>
                    <li>Table status observation (Occupied / Dirty)</li>
                    <li>Direct kitchen order routing</li>
                </ul>
            </div>
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 0.82rem; color: var(--text-muted);">
                Assigned to <strong>{{ $stats['staff_count'] }}</strong> active account(s)
            </div>
        </div>
    </div>

</div>

<!-- Interactive Toast Feedback -->
<div id="roleToast" class="role-toast">
    <i class="ti ti-circle-check" style="color: #10b981; font-size: 1.2rem;"></i>
    <span id="roleToastMsg">Notification</span>
</div>
@endsection

@push('scripts')
<script>
    // Tab Switcher
    function switchWorkspaceTab(tabName, triggerBtn) {
        document.querySelectorAll('.workspace-tab-btn').forEach(btn => btn.classList.remove('active'));
        triggerBtn.classList.add('active');

        const tablePanel = document.getElementById('tab-team-table');
        const matrixPanel = document.getElementById('tab-permissions-matrix');
        const cardsPanel = document.getElementById('tab-role-definitions');
        const searchBox = document.getElementById('searchBoxContainer');

        tablePanel.style.display = 'none';
        matrixPanel.classList.remove('active');
        cardsPanel.classList.remove('active');

        if (tabName === 'team-table') {
            tablePanel.style.display = 'block';
            searchBox.style.display = 'block';
        } else if (tabName === 'permissions-matrix') {
            matrixPanel.classList.add('active');
            searchBox.style.display = 'none';
        } else if (tabName === 'role-definitions') {
            cardsPanel.classList.add('active');
            searchBox.style.display = 'none';
        }
    }

    // Copy to Clipboard Utility
    function copyEmailToClipboard(email, btnElement) {
        if (!navigator.clipboard) {
            const textarea = document.createElement('textarea');
            textarea.value = email;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        } else {
            navigator.clipboard.writeText(email);
        }

        const originalHtml = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="ti ti-check"></i> Copied!';
        btnElement.classList.add('copied');

        showToast('Email address copied to clipboard!');

        setTimeout(() => {
            btnElement.innerHTML = originalHtml;
            btnElement.classList.remove('copied');
        }, 2200);
    }

    // Role Dropdown Inline Change via AJAX
    function updateMemberRole(selectEl, userId, userName) {
        const newRole = selectEl.value;
        const previousRole = selectEl.getAttribute('data-current-role');

        if (newRole === previousRole) return;

        // Visual confirmation if demoting owner
        if (previousRole === 'OWNER' && newRole !== 'OWNER') {
            if (!confirm(`Warning: You are about to change the role of ${userName} from OWNER to ${newRole}. Proceed?`)) {
                selectEl.value = previousRole;
                return;
            }
        }

        fetch("{{ route('admin.roles.permissions.updateRole') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                user_id: userId,
                role: newRole
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            selectEl.setAttribute('data-current-role', newRole);
            showToast(data.message || `Role updated to ${newRole}`);
        })
        .catch(error => {
            selectEl.value = previousRole;
            const errorMsg = (error.errors && error.errors.role) ? error.errors.role[0] : (error.message || 'Failed to update role');
            alert(errorMsg);
        });
    }

    // Toast Notification helper
    function showToast(message) {
        const toast = document.getElementById('roleToast');
        const msg = document.getElementById('roleToastMsg');
        msg.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Toggle Role Permission dynamically via AJAX
    function toggleRolePermission(role, permission, btnEl) {
        btnEl.style.opacity = '0.5';
        btnEl.disabled = true;

        fetch("{{ route('admin.roles.permissions.togglePermission') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                role: role,
                permission: permission
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            btnEl.style.opacity = '1';
            btnEl.disabled = false;
            if (data.granted) {
                btnEl.classList.remove('restricted');
                btnEl.classList.add('granted');
                btnEl.querySelector('i').className = 'ti ti-check';
                btnEl.querySelector('span').textContent = 'Granted';
                btnEl.title = 'Click to Revoke from ' + role;
            } else {
                btnEl.classList.remove('granted');
                btnEl.classList.add('restricted');
                btnEl.querySelector('i').className = 'ti ti-plus';
                btnEl.querySelector('span').textContent = 'Restricted';
                btnEl.title = 'Click to Grant to ' + role;
            }
            showToast(data.message);
        })
        .catch(error => {
            btnEl.style.opacity = '1';
            btnEl.disabled = false;
            const errorMsg = (error.errors && error.errors.role) ? error.errors.role[0] : (error.message || 'Failed to toggle permission');
            alert(errorMsg);
        });
    }

    // 3-Dots Action Dropdown Toggle with Auto-Flip
    function toggleActionMenu(event, menuId) {
        event.stopPropagation();
        const targetMenu = document.getElementById(menuId);

        document.querySelectorAll('.action-dropdown-menu').forEach(menu => {
            if (menu !== targetMenu) menu.classList.remove('show');
        });

        if (targetMenu) {
            const isShowing = targetMenu.classList.contains('show');
            if (!isShowing) {
                // Auto-detect if opening downwards causes clipping at bottom of viewport or container
                const btnRect = event.currentTarget.getBoundingClientRect();
                const windowHeight = window.innerHeight || document.documentElement.clientHeight;
                const spaceBelow = windowHeight - btnRect.bottom;

                if (spaceBelow < 190) {
                    targetMenu.classList.add('dropdown-menu-up');
                } else {
                    targetMenu.classList.remove('dropdown-menu-up');
                }
                targetMenu.classList.add('show');
            } else {
                targetMenu.classList.remove('show');
            }
        }
    }

    window.addEventListener('click', function() {
        document.querySelectorAll('.action-dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    });

    // Checkbox Select All
    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.member-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
    }

    // Real-Time Table Filter
    function filterMemberTable() {
        const query = document.getElementById('roleTableSearch').value.toLowerCase();
        const rows = document.querySelectorAll('.member-row');

        rows.forEach(row => {
            const name = row.querySelector('.user-meta-name')?.textContent.toLowerCase() || '';
            const email = row.querySelector('.email-address')?.textContent.toLowerCase() || '';
            const role = row.querySelector('.role-select-native')?.value.toLowerCase() || '';

            if (name.includes(query) || email.includes(query) || role.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endpush
