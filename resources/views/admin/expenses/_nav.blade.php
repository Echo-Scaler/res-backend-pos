<div class="expense-nav-bar" style="margin-bottom: 2rem !important;">
    <div class="nav-pill-track">
        <a href="{{ route('admin.expenses.index') }}" class="nav-pill-item {{ request()->routeIs('admin.expenses.index') ? 'active' : '' }}">
            <i class="ti ti-receipt"></i>
            <span>All Expenses</span>
        </a>
        <a href="{{ route('admin.expenses.budgets') }}" class="nav-pill-item {{ request()->routeIs('admin.expenses.budgets') ? 'active' : '' }}">
            <i class="ti ti-target"></i>
            <span>Budgets & Targets</span>
        </a>
        <a href="{{ route('admin.expenses.reports') }}" class="nav-pill-item {{ request()->routeIs('admin.expenses.reports') ? 'active' : '' }}">
            <i class="ti ti-chart-pie"></i>
            <span>Financial Reports</span>
        </a>
        <a href="{{ route('admin.expenses.categories') }}" class="nav-pill-item {{ request()->routeIs('admin.expenses.categories*') ? 'active' : '' }}">
            <i class="ti ti-tags"></i>
            <span>Categories</span>
        </a>
        <a href="{{ route('admin.expenses.vendors') }}" class="nav-pill-item {{ request()->routeIs('admin.expenses.vendors*') ? 'active' : '' }}">
            <i class="ti ti-building-store"></i>
            <span>Vendors & Suppliers</span>
        </a>
        <a href="{{ route('admin.expenses.recurring') }}" class="nav-pill-item {{ request()->routeIs('admin.expenses.recurring*') ? 'active' : '' }}">
            <i class="ti ti-rotate-clockwise"></i>
            <span>Recurring Automation</span>
        </a>
    </div>

    @if(!request()->routeIs('admin.expenses.create') && !request()->routeIs('admin.expenses.index'))
        <div class="nav-action-wrap">
            <a href="{{ route('admin.expenses.create') }}" class="btn-gradient-primary">
                <i class="ti ti-plus"></i>
                <span>Record Expense</span>
            </a>
        </div>
    @endif
</div>
