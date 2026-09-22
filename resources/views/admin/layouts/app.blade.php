<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Restaurant POS Management</title>
    
    <!-- Google Font: Mada -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mada:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root {
            /* Exact Soft Ambient Light Palette */
            --bg-body: #f8fafc;
            --bg-header: #ffffff;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --bg-card-tint: #eef3e8;
            --bg-hover: #f1f5f9;
            --bg-card-hover: #ffffff;
            
            --border-color: #e2e8f0;
            --border-subtle: #edf2f7;
            --border: #e2e8f0;
            
            --text-main: #0e2617;
            --text-muted: #384d3b;
            --text-light: #4d6350;
            --link-color: #245719;
            --link-hover: #14380f;
            
            --primary: #9ec63b;
            --primary-hover: #8bb42c;
            --primary-light: #e4ebd7;
            --primary-rgb: 158, 198, 59;
            
            --dark-forest: #0c2617;
            --olive-green: #5c8623;
            --lime-green: #9ec63b;
            --sage-tint: #e4ebd7;
            
            --secondary: #122a1b;
            --success: #7ea826;
            --success-light: #e4ebd7;
            --warning: #b59325;
            --warning-light: #faf5d8;
            --danger: #d44c45;
            --danger-light: #fdeeed;
            --info: #5c8623;
            --info-light: #e4ebd7;
            --violet: #122a1b;
            --violet-light: #e4ebd7;
            
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 76px;
            --header-height: 68px;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --shadow-sm: 0 1px 3px rgba(14, 38, 23, 0.04);
            --shadow-md: 0 4px 12px -2px rgba(14, 38, 23, 0.06);
            --shadow-lg: 0 12px 24px -4px rgba(14, 38, 23, 0.08);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="dark"] {
            /* Modern Obsidian / Dark Slate Theme Palette */
            --bg-body: #0b0f17;
            --bg-header: #111724;
            --bg-sidebar: #111724;
            --bg-card: #161e2e;
            --bg-card-tint: #1c2638;
            --bg-hover: #1c263a;
            --bg-card-hover: #1a2335;
            
            --border-color: #222d42;
            --border-subtle: #192233;
            --border: #222d42;
            
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --text-light: #64748b;
            --link-color: #9ec63b;
            --link-hover: #bef264;
            
            --primary: #9ec63b;
            --primary-hover: #b2dc47;
            --primary-light: rgba(158, 198, 59, 0.15);
            --primary-rgb: 158, 198, 59;
            
            --dark-forest: #0b0f17;
            --olive-green: #7ea826;
            --lime-green: #9ec63b;
            --sage-tint: rgba(158, 198, 59, 0.15);
            
            --secondary: #161e2e;
            --success: #22c55e;
            --success-light: rgba(34, 197, 94, 0.15);
            --warning: #eab308;
            --warning-light: rgba(234, 179, 8, 0.15);
            --danger: #ef4444;
            --danger-light: rgba(239, 68, 68, 0.15);
            --info: #38bdf8;
            --info-light: rgba(56, 189, 248, 0.15);
            --violet: #a855f7;
            --violet-light: rgba(168, 85, 247, 0.15);
            
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.5);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.6);
            --shadow-lg: 0 10px 24px rgba(0, 0, 0, 0.7);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Mada", -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.5;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            transition: background-color var(--transition);
        }

        /* PreAdmin Main Shell */
        .main-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Top Header */
        .header {
            height: var(--header-height);
            background-color: var(--bg-header);
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            transition: var(--transition);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            width: var(--sidebar-width);
            flex-shrink: 0;
        }

        .logo-box {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 800;
            font-size: 1.15rem;
        }

        .logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--olive-green));
            color: var(--dark-forest);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 10px rgba(158, 198, 59, 0.25);
        }

        .header-center {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
            margin-left: 0.5rem;
        }

        .btn-toggle-sidebar {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            background-color: var(--bg-card);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.2rem;
        }

        .btn-toggle-sidebar:hover {
            color: var(--primary);
            border-color: var(--primary);
            background-color: var(--bg-hover);
        }

        .btn-new-order-quick {
            background-color: #0c2617;
            color: #ffffff;
            border: 1px solid #0c2617;
            border-radius: 9999px;
            padding: 0.45rem 1.15rem;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 3px 8px rgba(12, 38, 23, 0.2);
            transition: var(--transition);
        }

        .btn-new-order-quick:hover {
            background-color: #1a422b;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .header-search {
            position: relative;
            max-width: 320px;
            width: 100%;
        }

        .header-search input {
            width: 100%;
            height: 38px;
            background-color: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: 9999px;
            padding: 0 2.5rem 0 2.25rem;
            font-size: 0.85rem;
            color: var(--text-main);
            outline: none;
            transition: var(--transition);
        }

        .header-search input:focus {
            border-color: var(--primary);
            background-color: var(--bg-card);
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15);
        }

        .header-search-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            pointer-events: none;
        }

        .header-search-badge {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background-color: var(--border-color);
            color: var(--text-muted);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
        }

        /* Header Right Controls */
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .header-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background-color: var(--bg-card);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            text-decoration: none;
            transition: var(--transition);
            font-size: 1.15rem;
        }

        .header-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
            background-color: var(--bg-hover);
        }

        .btn-badge-dot {
            position: absolute;
            top: 7px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--primary);
            border: 2px solid var(--bg-card);
        }

        /* Dropdown Menus */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            min-width: 240px;
            display: none;
            flex-direction: column;
            z-index: 1050;
            padding: 0.5rem 0;
            animation: fadeIn 0.15s ease-out;
        }

        .dropdown.open .dropdown-menu {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 1rem;
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: var(--bg-hover);
            color: var(--primary);
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 0.35rem 0;
        }

        /* User Profile Pill */
        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.25rem 0.4rem;
            border-radius: 9999px;
            transition: var(--transition);
        }

        .user-profile-btn:hover {
            background-color: var(--bg-hover);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .user-name-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.2;
        }

        .user-role-badge {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* PreAdmin Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: var(--header-height);
            bottom: 0;
            left: 0;
            z-index: 990;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.25rem 0.85rem;
            transition: var(--transition);
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: var(--border-color);
            border-radius: 4px;
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            font-weight: 800;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.85rem 0.75rem 0.35rem;
        }

        .sidebar-nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.85rem;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .sidebar-link-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-link i {
            font-size: 1.2rem;
            width: 22px;
            text-align: center;
            transition: var(--transition);
        }

        .sidebar-link:hover {
            color: var(--primary);
            background-color: var(--primary-light);
        }

        .sidebar-link.active {
            background-color: #0c2617;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(12, 38, 23, 0.25);
        }

        .sidebar-link.active i {
            color: #9ec63b !important;
        }

        [data-theme="dark"] .sidebar-link.active {
            background-color: rgba(158, 198, 59, 0.16);
            color: #f1f5f9 !important;
            border: 1px solid rgba(158, 198, 59, 0.35);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
        }

        [data-theme="dark"] .sidebar-link.active i {
            color: #bef264 !important;
        }

        .sidebar-badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            background-color: var(--primary-light);
            color: #0e2617;
        }

        [data-theme="dark"] .sidebar-badge {
            background-color: rgba(158, 198, 59, 0.2);
            color: #bef264;
        }

        .sidebar-link.active .sidebar-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        /* Sidebar Mini / Collapsed Mode */
        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
            padding: 1.25rem 0.45rem;
        }
        body.sidebar-collapsed .header-left {
            width: var(--sidebar-collapsed-width);
        }
        body.sidebar-collapsed .sidebar-section-title,
        body.sidebar-collapsed .sidebar-link span,
        body.sidebar-collapsed .sidebar-badge,
        body.sidebar-collapsed .logo-title {
            display: none !important;
        }
        body.sidebar-collapsed .sidebar-link {
            justify-content: center;
            padding: 0.75rem 0;
        }
        body.sidebar-collapsed .page-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Page Content Wrapper */
        .page-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            min-height: calc(100vh - var(--header-height));
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
            padding: 1.75rem 2rem 2.5rem;
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
        }

        /* Alerts */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: var(--success-light);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #065f46;
        }
        [data-theme="dark"] .alert-success {
            color: #6ee7b7;
        }

        .alert-danger {
            background-color: var(--danger-light);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #991b1b;
        }
        [data-theme="dark"] .alert-danger {
            color: #fca5a5;
        }

        /* Mobile Hamburger */
        .mobile-btn {
            display: none;
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            background-color: var(--bg-card);
            color: var(--text-main);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.25rem;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
                box-shadow: var(--shadow-lg);
            }
            .page-wrapper {
                margin-left: 0 !important;
            }
            .header-left {
                width: auto;
            }
            .mobile-btn {
                display: flex;
            }
            .header-search {
                display: none;
            }
            .content {
                padding: 1.25rem 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    @php
        $userRole = Auth::user()->getRoleNames()->first() ?? 'STAFF';
        $roleClass = match($userRole) {
            'OWNER' => 'role-owner',
            'MANAGER' => 'role-manager',
            'CASHIER' => 'role-cashier',
            default => 'role-staff',
        };
        $avatarName = urlencode(Auth::user()->name);
        $userAvatar = 'https://ui-avatars.com/api/?name='.$avatarName.'&background=ea580c&color=fff&bold=true';
    @endphp

    <!-- PreAdmin Shell -->
    <div class="main-wrapper">

        <!-- Top Header Navigation -->
        <header class="header">
            <div class="header-left">
                <button type="button" class="mobile-btn" id="mobile_btn" title="Toggle Mobile Navigation">
                    <i class="ti ti-menu-2"></i>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="logo-box">
                    <div class="logo-icon">
                        <i class="ti ti-flame"></i>
                    </div>
                    <div class="logo-title">
                        <div style="font-size: 1.05rem; font-weight: 800; line-height: 1.1;">{{ $restaurant->name ?? 'POS Master' }}</div>
                        <div style="font-size: 0.68rem; color: var(--primary); font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">Restaurant POS</div>
                    </div>
                </a>
            </div>

            <div class="header-center">
                <button type="button" class="btn-toggle-sidebar" id="toggle_btn" title="Collapse / Expand Sidebar">
                    <i class="ti ti-menu-deep"></i>
                </button>

                <a href="{{ route('admin.orders.index') }}" class="btn-new-order-quick">
                    <i class="ti ti-plus"></i>
                    <span>New Order</span>
                </a>

                <div class="header-search">
                    <i class="ti ti-search header-search-icon"></i>
                    <input type="text" placeholder="Search orders, tables, items...">
                    <span class="header-search-badge">⌘K</span>
                </div>
            </div>

            <div class="header-right">
                <!-- Theme Mode Toggle -->
                <button type="button" class="header-btn" id="theme-toggle" title="Toggle Dark/Light Mode">
                    <i class="ti ti-moon" id="theme-icon"></i>
                </button>

                <!-- Notifications Dropdown -->
                <div class="dropdown" id="dropdown-notifications">
                    <button type="button" class="header-btn" id="notification_popup" title="Notifications">
                        <i class="ti ti-bell"></i>
                        <span class="btn-badge-dot"></span>
                    </button>
                    <div class="dropdown-menu" style="width: 320px; right: 0;">
                        <div class="dropdown-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h6 style="font-size: 0.9rem; font-weight: 700;">Floor Notifications</h6>
                            <span style="font-size: 0.7rem; background: var(--primary-light); color: var(--primary); font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 9999px;">3 New</span>
                        </div>
                        <div style="max-height: 240px; overflow-y: auto;">
                            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); display: flex; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--info-light); color: var(--info); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="ti ti-soup"></i>
                                </div>
                                <div style="flex: 1;">
                                    <p style="font-size: 0.8rem; font-weight: 600; margin-bottom: 0.15rem;">Kitchen Order Ready</p>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">Table 04 items cooked & plated</p>
                                    <span style="font-size: 0.7rem; color: var(--text-light);"><i class="ti ti-clock me-1"></i>3 min ago</span>
                                </div>
                            </div>
                            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); display: flex; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="ti ti-alert-triangle"></i>
                                </div>
                                <div style="flex: 1;">
                                    <p style="font-size: 0.8rem; font-weight: 600; margin-bottom: 0.15rem;">Low-Stock Warning</p>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">Cooking Oil under 4 Liters safe limit</p>
                                    <span style="font-size: 0.7rem; color: var(--text-light);"><i class="ti ti-clock me-1"></i>15 min ago</span>
                                </div>
                            </div>
                            <div style="padding: 0.75rem 1rem; display: flex; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="ti ti-cash"></i>
                                </div>
                                <div style="flex: 1;">
                                    <p style="font-size: 0.8rem; font-weight: 600; margin-bottom: 0.15rem;">Payment Completed</p>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">Table 12 bill settled with KBZPay QR</p>
                                    <span style="font-size: 0.7rem; color: var(--text-light);"><i class="ti ti-clock me-1"></i>32 min ago</span>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 0.5rem; text-align: center; border-top: 1px solid var(--border-color);">
                            <a href="{{ route('admin.orders.index') }}" style="font-size: 0.75rem; color: var(--primary); font-weight: 700; text-decoration: none;">View All Notifications →</a>
                        </div>
                    </div>
                </div>

                <!-- Quick POS Shortcuts Grid -->
                <div class="dropdown" id="dropdown-shortcuts">
                    <button type="button" class="header-btn" title="Quick Applications">
                        <i class="ti ti-grid-dots"></i>
                    </button>
                    <div class="dropdown-menu" style="width: 280px; padding: 0.75rem; right: 0;">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; text-align: center;">
                            <a href="{{ route('admin.orders.index') }}" style="padding: 0.75rem 0.25rem; border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 0.75rem; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 0.35rem;" class="shortcut-item">
                                <span style="width: 36px; height: 36px; border-radius: 8px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="ti ti-receipt"></i></span>
                                Orders
                            </a>
                            <a href="{{ route('admin.tables.index') }}" style="padding: 0.75rem 0.25rem; border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 0.75rem; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 0.35rem;" class="shortcut-item">
                                <span style="width: 36px; height: 36px; border-radius: 8px; background: var(--info-light); color: var(--info); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="ti ti-layout-grid"></i></span>
                                Tables
                            </a>
                            <a href="{{ route('admin.menu.index') }}" style="padding: 0.75rem 0.25rem; border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 0.75rem; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 0.35rem;" class="shortcut-item">
                                <span style="width: 36px; height: 36px; border-radius: 8px; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="ti ti-tools-kitchen-2"></i></span>
                                Menu
                            </a>
                            <a href="{{ route('admin.inventory.index') }}" style="padding: 0.75rem 0.25rem; border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 0.75rem; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 0.35rem;" class="shortcut-item">
                                <span style="width: 36px; height: 36px; border-radius: 8px; background: var(--warning-light); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="ti ti-box"></i></span>
                                Stock
                            </a>
                            <a href="{{ route('admin.payments.index') }}" style="padding: 0.75rem 0.25rem; border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 0.75rem; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 0.35rem;" class="shortcut-item">
                                <span style="width: 36px; height: 36px; border-radius: 8px; background: var(--violet-light); color: var(--violet); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="ti ti-credit-card"></i></span>
                                Payments
                            </a>
                            <a href="{{ route('admin.employees.index') }}" style="padding: 0.75rem 0.25rem; border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 0.75rem; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 0.35rem;" class="shortcut-item">
                                <span style="width: 36px; height: 36px; border-radius: 8px; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="ti ti-users"></i></span>
                                Staff
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown" id="dropdown-user">
                    <button type="button" class="user-profile-btn">
                        <img src="{{ $userAvatar }}" alt="{{ Auth::user()->name }}" class="user-avatar">
                        <div class="user-info-text">
                            <span class="user-name-title">{{ Auth::user()->name }}</span>
                            <span class="user-role-badge">{{ $userRole }}</span>
                        </div>
                        <i class="ti ti-chevron-down" style="font-size: 0.8rem; color: var(--text-muted); margin-left: 0.2rem;"></i>
                    </button>
                    <div class="dropdown-menu">
                        <div class="dropdown-header">
                            <h6 style="font-size: 0.85rem; font-weight: 700;">{{ Auth::user()->name }}</h6>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">{{ Auth::user()->email ?? 'Executive Owner' }}</span>
                        </div>
                        <a href="{{ route('admin.account.security') }}" class="dropdown-item">
                            <i class="ti ti-user-circle"></i>
                            <span>Profile & Security</span>
                        </a>
                        <a href="{{ route('admin.settings.restaurant') }}" class="dropdown-item">
                            <i class="ti ti-settings"></i>
                            <span>Restaurant Settings</span>
                        </a>
                        <a href="{{ route('admin.audit.logs') }}" class="dropdown-item">
                            <i class="ti ti-history"></i>
                            <span>Audit Activity Trail</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item" style="color: var(--danger);">
                                <i class="ti ti-logout"></i>
                                <span>Logout Account</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- PreAdmin Collapsible Left Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-section-title">Main</div>
            <ul class="sidebar-nav-list">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-layout-dashboard"></i>
                            <span>Dashboard</span>
                        </div>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">Orders & Dining</div>
            <ul class="sidebar-nav-list">
                <li>
                    <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-receipt"></i>
                            <span>Live Orders</span>
                        </div>
                        <span class="sidebar-badge">8</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tables.index') }}" class="sidebar-link {{ request()->routeIs('admin.tables.index') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-layout-grid"></i>
                            <span>Table Floor Plan</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.menu.index') }}" class="sidebar-link {{ request()->routeIs('admin.menu.index') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-tools-kitchen-2"></i>
                            <span>Menu & Products</span>
                        </div>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">Finance & Accounts</div>
            <ul class="sidebar-nav-list">
                <li>
                    <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-credit-card"></i>
                            <span>Payments & Drawers</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.expenses.index') }}" class="sidebar-link {{ request()->routeIs('admin.expenses.index') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-cash-register"></i>
                            <span>Expense Tracker</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-chart-bar"></i>
                            <span>Reports & Analytics</span>
                        </div>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">Management & Staff</div>
            <ul class="sidebar-nav-list">
                <li>
                    <a href="{{ route('admin.employees.index') }}" class="sidebar-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-users-group"></i>
                            <span>Employee Directory</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.roles.permissions') }}" class="sidebar-link {{ request()->routeIs('admin.roles.permissions') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-shield-lock"></i>
                            <span>Roles & Permissions</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-user-heart"></i>
                            <span>Customer CRM</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.promotions.index') }}" class="sidebar-link {{ request()->routeIs('admin.promotions.index') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-discount-2"></i>
                            <span>Discounts & Promos</span>
                        </div>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">Kitchen & Stock</div>
            <ul class="sidebar-nav-list">
                <li>
                    <a href="{{ route('admin.inventory.index') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-packages"></i>
                            <span>Inventory Stock</span>
                        </div>
                        <span class="sidebar-badge" style="background: var(--danger-light); color: var(--danger);">4</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">System & Settings</div>
            <ul class="sidebar-nav-list">
                <li>
                    <a href="{{ route('admin.settings.restaurant') }}" class="sidebar-link {{ request()->routeIs('admin.settings.restaurant') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-building-store"></i>
                            <span>Restaurant Profile</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.tax') }}" class="sidebar-link {{ request()->routeIs('admin.settings.tax') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-receipt-tax"></i>
                            <span>Tax & Service Charge</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.business') }}" class="sidebar-link {{ request()->routeIs('admin.settings.business') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-printer"></i>
                            <span>Printers & Business</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.audit.logs') }}" class="sidebar-link {{ request()->routeIs('admin.audit.logs') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-file-text"></i>
                            <span>Audit Trail Logs</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.account.security') }}" class="sidebar-link {{ request()->routeIs('admin.account.security') ? 'active' : '' }}">
                        <div class="sidebar-link-content">
                            <i class="ti ti-lock-check"></i>
                            <span>Security & Account</span>
                        </div>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <div class="page-wrapper">
            <main class="content">
                @if (session('status'))
                    <div class="alert alert-success">
                        <i class="ti ti-circle-check fs-5"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="ti ti-circle-check fs-5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-circle fs-5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-circle fs-5"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

    </div>
    @endauth

    @guest
    <div style="min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 2rem 1rem;">
        <main style="max-width: 480px; margin: 0 auto; width: 100%;">
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="ti ti-circle-check fs-5"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="ti ti-circle-check fs-5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="ti ti-alert-circle fs-5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="ti ti-alert-circle fs-5"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    @endguest

    <script>
        // Sidebar Toggle Collapse
        const toggleBtn = document.getElementById('toggle_btn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('pos_sidebar_collapsed', document.body.classList.contains('sidebar-collapsed'));
            });
            if (localStorage.getItem('pos_sidebar_collapsed') === 'true') {
                document.body.classList.add('sidebar-collapsed');
            }
        }

        // Mobile Sidebar Drawer
        const mobileBtn = document.getElementById('mobile_btn');
        const sidebar = document.getElementById('sidebar');
        if (mobileBtn && sidebar) {
            mobileBtn.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
            });
        }

        // Dropdown toggles
        document.querySelectorAll('.dropdown').forEach(drop => {
            const btn = drop.querySelector('button');
            if (btn) {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = drop.classList.contains('open');
                    document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('open'));
                    if (!isOpen) drop.classList.add('open');
                });
            }
        });
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('open'));
        });

        // Theme Toggle (Dark / Light)
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        function setTheme(theme) {
            html.setAttribute('data-theme', theme);
            localStorage.setItem('pos_theme', theme);
            if (themeIcon) {
                themeIcon.className = theme === 'dark' ? 'ti ti-sun' : 'ti ti-moon';
            }
        }

        let savedTheme = localStorage.getItem('pos_theme');
        if (!savedTheme || savedTheme === 'dark') {
            savedTheme = 'light';
            localStorage.setItem('pos_theme', 'light');
        }
        setTheme(savedTheme);

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const current = html.getAttribute('data-theme');
                setTheme(current === 'dark' ? 'light' : 'dark');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
