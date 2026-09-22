<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Restaurant POS Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f97316;
            --primary-hover: #ea580c;
            --primary-light: #ffedd5;
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --bg-card-hover: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --success: #10b981;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 800;
            font-size: 1.2rem;
        }

        .nav-brand-badge {
            background: linear-gradient(135deg, var(--primary), #fb923c);
            color: #fff;
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .user-badge {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.75rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .role-owner {
            background-color: rgba(249, 115, 22, 0.15);
            color: #fb923c;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }

        .role-manager {
            background-color: rgba(99, 102, 241, 0.15);
            color: #a5b4fc;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .role-cashier {
            background-color: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .role-staff {
            background-color: rgba(6, 182, 212, 0.15);
            color: #67e8f9;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .btn-logout {
            background-color: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background-color: var(--danger);
            color: #fff;
        }

        /* Layout Container with Sidebar */
        .app-shell {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 65px);
        }

        .sidebar {
            width: 270px;
            background-color: #0b1120;
            border-right: 1px solid var(--border);
            padding: 1.25rem 0.85rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            flex-shrink: 0;
            overflow-y: auto;
            max-height: calc(100vh - 65px);
            position: sticky;
            top: 65px;
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0 0.75rem;
            margin-bottom: 0.4rem;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.85rem;
            border-radius: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
        }

        .sidebar-link.active {
            background-color: rgba(249, 115, 22, 0.15);
            color: #fb923c;
            font-weight: 700;
            border: 1px solid rgba(249, 115, 22, 0.25);
        }

        .sidebar-icon {
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
        }

        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            max-width: 1360px;
            width: 100%;
            margin: 0 auto;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.15);
            border: 1px solid var(--success);
            color: #6ee7b7;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid var(--danger);
            color: #fca5a5;
        }

        @media (max-width: 1024px) {
            .sidebar {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    @php
        $userRole = Auth::user()->getRoleNames()->first() ?? 'STAFF';
        $homeRoute = match($userRole) {
            'MANAGER' => route('manager.dashboard'),
            'CASHIER' => route('cashier.dashboard'),
            'STAFF' => route('staff.dashboard'),
            default => route('admin.dashboard'),
        };
        $roleClass = match($userRole) {
            'OWNER' => 'role-owner',
            'MANAGER' => 'role-manager',
            'CASHIER' => 'role-cashier',
            default => 'role-staff',
        };
    @endphp
    <header class="navbar">
        <a href="{{ $homeRoute }}" class="nav-brand">
            <span class="nav-brand-badge">POS</span>
            <span>{{ $restaurant->name ?? 'Restaurant POS' }}</span>
        </a>

        <div class="nav-user">
            <div class="user-badge">
                <span class="user-name">{{ Auth::user()->name }}</span>
                <span class="user-role {{ $roleClass }}">{{ $userRole }}</span>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </header>
    @endauth

    <div class="app-shell">
        @if(Auth::check() && Auth::user()->hasRole('OWNER'))
        <aside class="sidebar">
            <div>
                <div class="sidebar-section-title">Core Operations</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <span class="sidebar-icon">📊</span>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                            <span class="sidebar-icon">🧾</span>
                            <span>Order Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tables.index') }}" class="sidebar-link {{ request()->routeIs('admin.tables.index') ? 'active' : '' }}">
                            <span class="sidebar-icon">🪑</span>
                            <span>Table Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.menu.index') }}" class="sidebar-link {{ request()->routeIs('admin.menu.index') ? 'active' : '' }}">
                            <span class="sidebar-icon">🍕</span>
                            <span>Menu / Products</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="sidebar-section-title">Finance & Stock</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}">
                            <span class="sidebar-icon">💳</span>
                            <span>Payment Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.inventory.index') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                            <span class="sidebar-icon">📦</span>
                            <span>Inventory Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.expenses.index') }}" class="sidebar-link {{ request()->routeIs('admin.expenses.index') ? 'active' : '' }}">
                            <span class="sidebar-icon">💰</span>
                            <span>Expense Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                            <span class="sidebar-icon">📈</span>
                            <span>Reports & Analytics</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="sidebar-section-title">Staff & Customers</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('admin.employees.index') }}" class="sidebar-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                            <span class="sidebar-icon">👥</span>
                            <span>Employee Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.roles.permissions') }}" class="sidebar-link {{ request()->routeIs('admin.roles.permissions') ? 'active' : '' }}">
                            <span class="sidebar-icon">🛡️</span>
                            <span>Roles & Permissions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
                            <span class="sidebar-icon">👤</span>
                            <span>Customer Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.promotions.index') }}" class="sidebar-link {{ request()->routeIs('admin.promotions.index') ? 'active' : '' }}">
                            <span class="sidebar-icon">🏷️</span>
                            <span>Discounts / Promos</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="sidebar-section-title">Settings & Security</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('admin.settings.restaurant') }}" class="sidebar-link {{ request()->routeIs('admin.settings.restaurant') ? 'active' : '' }}">
                            <span class="sidebar-icon">⚙️</span>
                            <span>Restaurant Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.tax') }}" class="sidebar-link {{ request()->routeIs('admin.settings.tax') ? 'active' : '' }}">
                            <span class="sidebar-icon">📑</span>
                            <span>Tax & Service Charge</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.business') }}" class="sidebar-link {{ request()->routeIs('admin.settings.business') ? 'active' : '' }}">
                            <span class="sidebar-icon">🏢</span>
                            <span>Business Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.audit.logs') }}" class="sidebar-link {{ request()->routeIs('admin.audit.logs') ? 'active' : '' }}">
                            <span class="sidebar-icon">📜</span>
                            <span>Audit Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.account.security') }}" class="sidebar-link {{ request()->routeIs('admin.account.security') ? 'active' : '' }}">
                            <span class="sidebar-icon">🔐</span>
                            <span>Account / Security</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        @endif

        <div class="content-area">
            <main class="main-content">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
