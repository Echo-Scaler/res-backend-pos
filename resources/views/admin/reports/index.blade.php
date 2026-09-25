@extends('admin.layouts.app')

@section('title', 'Reports & Analytics — Real Work Structure')

@section('content')
<div class="reports-container">

    {{-- Top Action Header & Period Selector --}}
    <div class="reports-header-card">
        <div class="reports-title-row">
            <div class="title-group">
                <div class="header-badge">
                    <i class="ti ti-chart-dots"></i>
                    <span>Financial & Operational Intelligence</span>
                </div>
                <h1 class="page-title">Reports & Analytics</h1>
                <p class="page-subtitle">Real-work performance tracking, Food Cost (COGS), P&L financials, Category margins, Table turnover & Void audits.</p>
            </div>

            <div class="header-actions">
                <a href="{{ route('admin.reports.export', ['tab' => $activeTab, 'period' => $activePeriod, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn-action btn-export" id="btn-export-csv" title="Export current ({{ strtoupper($activeTab) }}) report as CSV">
                    <i class="ti ti-download"></i>
                    <span>Export CSV</span>
                </a>
                <button type="button" onclick="window.print()" class="btn-action btn-print">
                    <i class="ti ti-printer"></i>
                    <span>Print Report</span>
                </button>
            </div>
        </div>

        {{-- Period Pills Bar --}}
        <div class="period-filter-bar">
            <div class="period-pills-group">
                @php
                    $periods = [
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'this_year' => 'This Year',
                    ];
                @endphp
                @foreach ($periods as $key => $label)
                    <a href="{{ route('admin.reports.index', ['period' => $key, 'tab' => $activeTab]) }}"
                       class="period-pill {{ $activePeriod === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
                <button type="button" class="period-pill custom-btn {{ $activePeriod === 'custom' ? 'active' : '' }}" onclick="document.getElementById('customRangeModal').classList.toggle('show')">
                    <i class="ti ti-calendar"></i>
                    <span>Custom Range</span>
                </button>
            </div>

            <div class="active-period-indicator">
                <i class="ti ti-clock-play"></i>
                <span>{{ $periodLabel }}</span>
            </div>
        </div>

        {{-- Custom Range Filter Dropdown / Drawer --}}
        <div id="customRangeModal" class="custom-range-card {{ $activePeriod === 'custom' ? 'show' : '' }}">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="custom-range-form">
                <input type="hidden" name="period" value="custom">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <div class="form-group-range">
                    <label>From Date</label>
                    <input type="date" name="start_date" value="{{ $startDate ?: now()->subDays(6)->format('Y-m-d') }}" class="range-input">
                </div>
                <div class="form-group-range">
                    <label>To Date</label>
                    <input type="date" name="end_date" value="{{ $endDate ?: now()->format('Y-m-d') }}" class="range-input">
                </div>
                <button type="submit" class="btn-apply-range">
                    <i class="ti ti-check"></i>
                    <span>Apply Filter</span>
                </button>
            </form>
        </div>

        {{-- Domain Navigator Tab Bar --}}
        <div class="domain-tabs-wrapper">
            @php
                $tabs = [
                    'overview' => ['icon' => 'ti-dashboard', 'label' => 'Executive Overview'],
                    'sales' => ['icon' => 'ti-chart-line', 'label' => 'Sales & Categories'],
                    'cogs' => ['icon' => 'ti-meat', 'label' => 'Food Cost / COGS'],
                    'pnl' => ['icon' => 'ti-file-invoice', 'label' => 'P&L Summary'],
                    'alcohol' => ['icon' => 'ti-glass-full', 'label' => 'Alcohol Sales'],
                    'tables' => ['icon' => 'ti-armchair', 'label' => 'Table Analytics'],
                    'ordertypes' => ['icon' => 'ti-moped', 'label' => 'Order Types'],
                    'voids' => ['icon' => 'ti-circle-x', 'label' => 'Void & Cancellation'],
                    'promotions' => ['icon' => 'ti-discount', 'label' => 'Promotions ROI'],
                    'comparison' => ['icon' => 'ti-arrows-diff', 'label' => 'Comparison & Forecast'],
                    'audit' => ['icon' => 'ti-shield-check', 'label' => 'Audit Log'],
                ];
            @endphp
            <div class="domain-tabs-scroll">
                @foreach ($tabs as $tKey => $tabItem)
                    <a href="{{ route('admin.reports.index', ['tab' => $tKey, 'period' => $activePeriod, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="domain-tab-btn {{ $activeTab === $tKey ? 'active' : '' }}">
                        <i class="ti {{ $tabItem['icon'] }}"></i>
                        <span>{{ $tabItem['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TAB 1: EXECUTIVE OVERVIEW & KPIS --}}
    @if ($activeTab === 'overview')
    <div class="report-tab-content">
        {{-- 4 Primary Highlight KPI Cards --}}
        <div class="grid-kpi-highlight">
            <div class="kpi-card hero-sales">
                <div class="kpi-header">
                    <div class="kpi-header-left">
                        <div class="kpi-avatar-icon kpi-avatar-green">
                            <i class="ti ti-cash"></i>
                        </div>
                        <span class="kpi-title">Net Sales</span>
                    </div>
                    <span class="kpi-tag-success"><i class="ti ti-arrow-up-right"></i> +10.5%</span>
                </div>
                <div class="kpi-metric-main">{{ $kpis['currency_symbol'] }}{{ number_format($kpis['net_sales']) }}</div>
                <div class="kpi-footer">
                    <span>Gross: {{ $kpis['currency_symbol'] }}{{ number_format($kpis['gross_sales']) }}</span>
                    <span class="kpi-dot"></span>
                    <span>Refunds: {{ $kpis['currency_symbol'] }}{{ number_format($kpis['refund_amount']) }}</span>
                </div>
            </div>

            <div class="kpi-card hero-orders">
                <div class="kpi-header">
                    <div class="kpi-header-left">
                        <div class="kpi-avatar-icon kpi-avatar-blue">
                            <i class="ti ti-receipt"></i>
                        </div>
                        <span class="kpi-title">Total Orders</span>
                    </div>
                    <span class="kpi-badge-neutral"><i class="ti ti-box"></i> Volume</span>
                </div>
                <div class="kpi-metric-main">{{ number_format($kpis['total_orders']) }}</div>
                <div class="kpi-footer">
                    <span>Completed: {{ $kpis['completed_orders'] }}</span>
                    <span class="kpi-dot"></span>
                    <span>Void/Refund: {{ $kpis['total_orders'] - $kpis['completed_orders'] }}</span>
                </div>
            </div>

            <div class="kpi-card hero-aov">
                <div class="kpi-header">
                    <div class="kpi-header-left">
                        <div class="kpi-avatar-icon kpi-avatar-orange">
                            <i class="ti ti-calculator"></i>
                        </div>
                        <span class="kpi-title">Average Order Value (AOV)</span>
                    </div>
                    <span class="kpi-badge-neutral"><i class="ti ti-user"></i> Ticket</span>
                </div>
                <div class="kpi-metric-main">{{ $kpis['currency_symbol'] }}{{ number_format($kpis['average_order_value']) }}</div>
                <div class="kpi-footer">
                    <span>Avg items: 2.7</span>
                    <span class="kpi-dot"></span>
                    <span>Guests: {{ $kpis['total_customers'] }}</span>
                </div>
            </div>

            <div class="kpi-card hero-profit">
                <div class="kpi-header">
                    <div class="kpi-header-left">
                        <div class="kpi-avatar-icon kpi-avatar-violet">
                            <i class="ti ti-trending-up"></i>
                        </div>
                        <span class="kpi-title">Estimated Gross Profit</span>
                    </div>
                    <span class="kpi-tag-profit"><i class="ti ti-chart-line"></i> {{ $kpis['profit_margin'] }}%</span>
                </div>
                <div class="kpi-metric-main">{{ $kpis['currency_symbol'] }}{{ number_format($kpis['estimated_profit']) }}</div>
                <div class="kpi-footer">
                    <span>Est Food Cost: {{ $kpis['currency_symbol'] }}{{ number_format($kpis['cogs_amount']) }}</span>
                </div>
            </div>
        </div>

        {{-- Today's Overview Financial Card & Secondary KPIs Grid --}}
        <div class="overview-dual-grid">
            {{-- Today's Overview Card Matching Exact User Prompt Specs --}}
            <div class="content-card today-overview-spec-card">
                <div class="card-header-clean">
                    <div>
                        <h3 class="card-title">Today's Overview</h3>
                        <p class="card-sub">Daily executive sales reconciliation structure</p>
                    </div>
                    <span class="status-live-pill"><i class="ti ti-point-filled"></i> Live Today</span>
                </div>

                <div class="overview-spec-container">
                    <div class="spec-row-highlight">
                        <span class="spec-label">Sales</span>
                        <span class="spec-value-main">{{ $todayOverview['currency_symbol'] }}{{ number_format($todayOverview['sales']) }}</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Orders</span>
                        <span class="spec-value">{{ number_format($todayOverview['orders']) }}</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Average Order</span>
                        <span class="spec-value">{{ $todayOverview['currency_symbol'] }}{{ number_format($todayOverview['average_order']) }}</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Customers</span>
                        <span class="spec-value">{{ number_format($todayOverview['customers']) }}</span>
                    </div>

                    <div class="spec-divider"></div>

                    <div class="spec-row">
                        <span class="spec-label">Gross Sales</span>
                        <span class="spec-value">{{ $todayOverview['currency_symbol'] }}{{ number_format($todayOverview['gross_sales']) }}</span>
                    </div>
                    <div class="spec-row text-danger">
                        <span class="spec-label">Discount</span>
                        <span class="spec-value">- {{ $todayOverview['currency_symbol'] }}{{ number_format($todayOverview['discount']) }}</span>
                    </div>
                    <div class="spec-row text-info">
                        <span class="spec-label">Tax</span>
                        <span class="spec-value">+ {{ $todayOverview['currency_symbol'] }}{{ number_format($todayOverview['tax']) }}</span>
                    </div>
                    <div class="spec-row text-warning">
                        <span class="spec-label">Refund</span>
                        <span class="spec-value">- {{ $todayOverview['currency_symbol'] }}{{ number_format($todayOverview['refund']) }}</span>
                    </div>

                    <div class="spec-divider-double"></div>

                    <div class="spec-row-net">
                        <span class="spec-net-label">Net Sales</span>
                        <span class="spec-net-value">{{ $todayOverview['currency_symbol'] }}{{ number_format($todayOverview['net_sales']) }}</span>
                    </div>
                </div>
            </div>

            {{-- 13 KPI Complementary Operational Metrics --}}
            <div class="content-card kpi-secondary-grid-card">
                <div class="card-header-clean">
                    <div>
                        <h3 class="card-title">Real-Work Cash & Tax Audits</h3>
                        <p class="card-sub">Payment collections, active tabs & volume metrics</p>
                    </div>
                </div>

                <div class="grid-audit-metrics">
                    <div class="audit-item">
                        <div class="audit-icon icon-green"><i class="ti ti-cash"></i></div>
                        <div class="audit-info">
                            <span class="audit-lbl">Payment Collected</span>
                            <span class="audit-val">{{ $kpis['currency_symbol'] }}{{ number_format($kpis['payment_amount']) }}</span>
                        </div>
                    </div>

                    <div class="audit-item">
                        <div class="audit-icon icon-warning"><i class="ti ti-hourglass-empty"></i></div>
                        <div class="audit-info">
                            <span class="audit-lbl">Outstanding Amount</span>
                            <span class="audit-val">{{ $kpis['currency_symbol'] }}{{ number_format($kpis['outstanding_amount']) }}</span>
                        </div>
                    </div>

                    <div class="audit-item">
                        <div class="audit-icon icon-info"><i class="ti ti-packages"></i></div>
                        <div class="audit-info">
                            <span class="audit-lbl">Total Items Sold</span>
                            <span class="audit-val">{{ number_format($kpis['total_items_sold']) }} items</span>
                        </div>
                    </div>

                    <div class="audit-item">
                        <div class="audit-icon icon-lime"><i class="ti ti-percentage"></i></div>
                        <div class="audit-info">
                            <span class="audit-lbl">Discounts Granted</span>
                            <span class="audit-val">{{ $kpis['currency_symbol'] }}{{ number_format($kpis['discounts']) }}</span>
                        </div>
                    </div>

                    <div class="audit-item">
                        <div class="audit-icon icon-violet"><i class="ti ti-file-certificate"></i></div>
                        <div class="audit-info">
                            <span class="audit-lbl">Commercial Tax (Collected)</span>
                            <span class="audit-val">{{ $kpis['currency_symbol'] }}{{ number_format($kpis['tax']) }}</span>
                        </div>
                    </div>

                    <div class="audit-item">
                        <div class="audit-icon icon-danger"><i class="ti ti-rotate-clockwise-2"></i></div>
                        <div class="audit-info">
                            <span class="audit-lbl">Refunded Amount</span>
                            <span class="audit-val">{{ $kpis['currency_symbol'] }}{{ number_format($kpis['refund_amount']) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Cash Breakdown Mini-Bar --}}
                <div class="cash-settlement-bar">
                    <div class="cash-settle-title">
                        <span>Settlement Channels</span>
                        <span class="settle-status">Reconciled 100%</span>
                    </div>
                    <div class="settle-progress-multi">
                        <div class="progress-chunk kbzpay" style="width: 40%;" title="KBZPay QR (40%)"></div>
                        <div class="progress-chunk cash" style="width: 31%;" title="Cash (31%)"></div>
                        <div class="progress-chunk wavepay" style="width: 17%;" title="WavePay (17%)"></div>
                        <div class="progress-chunk card" style="width: 12%;" title="Cards (12%)"></div>
                    </div>
                    <div class="settle-legend">
                        <span><i class="legend-dot kbzpay"></i> KBZPay (40%)</span>
                        <span><i class="legend-dot cash"></i> Cash (31%)</span>
                        <span><i class="legend-dot wavepay"></i> WavePay (17%)</span>
                        <span><i class="legend-dot card"></i> Card (12%)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daily Breakdown Table (Sep 20, Sep 21, Sep 22 format) --}}
        <div class="content-card table-section-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Daily Sales Reconciliation Breakdown</h3>
                    <p class="card-sub">Daily audited rows showing Orders, Gross, Discounts, Tax, Refunds and Net Revenue</p>
                </div>
                <a href="{{ route('admin.reports.export', ['period' => $activePeriod]) }}" class="btn-sm-action">
                    <i class="ti ti-file-spreadsheet"></i> Export CSV
                </a>
            </div>

            <div class="table-responsive">
                <table class="report-data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-right">Orders</th>
                            <th class="text-right">Gross</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Tax</th>
                            <th class="text-right">Refund</th>
                            <th class="text-right">Net Sales</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dailyBreakdown as $row)
                        <tr>
                            <td class="font-medium text-main">
                                <i class="ti ti-calendar-event text-muted mr-1"></i>
                                {{ $row['date'] }}
                            </td>
                            <td class="text-right font-medium">{{ number_format($row['orders']) }}</td>
                            <td class="text-right font-medium">{{ $kpis['currency_symbol'] }}{{ number_format($row['gross']) }}</td>
                            <td class="text-right text-danger">- {{ $kpis['currency_symbol'] }}{{ number_format($row['discount']) }}</td>
                            <td class="text-right text-info">+ {{ $kpis['currency_symbol'] }}{{ number_format($row['tax']) }}</td>
                            <td class="text-right text-warning">
                                @if ($row['refund'] > 0)
                                    - {{ $kpis['currency_symbol'] }}{{ number_format($row['refund']) }}
                                @else
                                    {{ $kpis['currency_symbol'] }}0
                                @endif
                            </td>
                            <td class="text-right font-bold text-success">{{ $kpis['currency_symbol'] }}{{ number_format($row['net']) }}</td>
                            <td class="text-center">
                                <span class="badge-audited"><i class="ti ti-check"></i> Reconciled</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No sales records found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if (count($dailyBreakdown) > 0)
                    <tfoot>
                        <tr class="table-total-row">
                            <td class="font-bold">Total / Summary</td>
                            <td class="text-right font-bold">{{ number_format(array_sum(array_column($dailyBreakdown, 'orders'))) }}</td>
                            <td class="text-right font-bold">{{ $kpis['currency_symbol'] }}{{ number_format(array_sum(array_column($dailyBreakdown, 'gross'))) }}</td>
                            <td class="text-right font-bold text-danger">- {{ $kpis['currency_symbol'] }}{{ number_format(array_sum(array_column($dailyBreakdown, 'discount'))) }}</td>
                            <td class="text-right font-bold text-info">+ {{ $kpis['currency_symbol'] }}{{ number_format(array_sum(array_column($dailyBreakdown, 'tax'))) }}</td>
                            <td class="text-right font-bold text-warning">- {{ $kpis['currency_symbol'] }}{{ number_format(array_sum(array_column($dailyBreakdown, 'refund'))) }}</td>
                            <td class="text-right font-bold text-success">{{ $kpis['currency_symbol'] }}{{ number_format(array_sum(array_column($dailyBreakdown, 'net'))) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 2: SALES & CATEGORIES ("ဘယ် category က ပိုက်ဆံရှာပေးနေလဲ") --}}
    @if ($activeTab === 'sales')
    <div class="report-tab-content">
        <div class="overview-dual-grid">
            {{-- Sales by Category (Donut & Margin Rankings) --}}
            <div class="content-card">
                <div class="card-header-clean">
                    <div>
                        <h3 class="card-title">Sales by Category</h3>
                        <p class="card-sub">Restaurant မှာ ဘယ် category က ပိုက်ဆံရှာပေးနေလဲ (Profit contribution)</p>
                    </div>
                    <a href="{{ route('admin.reports.export', ['tab' => 'sales', 'period' => $activePeriod, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn-sm-action" title="Download Category Sales CSV">
                        <i class="ti ti-download"></i> Export CSV
                    </a>
                </div>

                <div class="category-breakdown-list">
                    @foreach ($salesByCategory as $cat)
                    <div class="category-row-item">
                        <div class="cat-left">
                            <span class="cat-icon">{{ $cat['icon'] }}</span>
                            <div>
                                <h4 class="cat-name">{{ $cat['name'] }}</h4>
                                <span class="cat-orders">{{ $cat['orders_count'] }} orders sold</span>
                            </div>
                        </div>
                        <div class="cat-progress-wrap">
                            <div class="cat-progress-bar">
                                <div class="cat-progress-fill" style="width: {{ $cat['percentage'] }}%; background-color: {{ $cat['color'] }};"></div>
                            </div>
                            <span class="cat-pct">{{ $cat['percentage'] }}% share</span>
                        </div>
                        <div class="cat-right">
                            <span class="cat-sales">{{ $kpis['currency_symbol'] }}{{ number_format($cat['gross_sales']) }}</span>
                            <span class="cat-margin-badge">{{ $cat['profit_margin'] }}% Gross Margin</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Tabular Breakdown matching CSV format --}}
                <div class="table-responsive" style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <table class="report-data-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-right">Orders</th>
                                <th class="text-right">Gross Sales</th>
                                <th class="text-right">Share</th>
                                <th class="text-right">Margin</th>
                                <th class="text-right">Est. Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $catTotOrders = 0;
                                $catTotGross = 0;
                                $catTotProfit = 0;
                            @endphp
                            @foreach ($salesByCategory as $cat)
                            @php
                                $catTotOrders += (int) $cat['orders_count'];
                                $catTotGross += (int) $cat['gross_sales'];
                                $cProfit = (int) round($cat['gross_sales'] * ($cat['profit_margin'] / 100));
                                $catTotProfit += $cProfit;
                            @endphp
                            <tr>
                                <td class="font-medium">
                                    <span style="margin-right: 6px;">{{ $cat['icon'] }}</span>{{ $cat['name'] }}
                                </td>
                                <td class="text-right font-medium">{{ number_format($cat['orders_count']) }}</td>
                                <td class="text-right font-bold">{{ $kpis['currency_symbol'] }}{{ number_format($cat['gross_sales']) }}</td>
                                <td class="text-right text-muted">{{ $cat['percentage'] }}%</td>
                                <td class="text-right"><span class="badge-status badge-success">{{ $cat['profit_margin'] }}%</span></td>
                                <td class="text-right font-bold text-success">{{ $kpis['currency_symbol'] }}{{ number_format($cProfit) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-total-row">
                                <td class="font-bold">TOTAL / SUMMARY</td>
                                <td class="text-right font-bold">{{ number_format($catTotOrders) }}</td>
                                <td class="text-right font-bold">{{ $kpis['currency_symbol'] }}{{ number_format($catTotGross) }}</td>
                                <td class="text-right font-bold">100%</td>
                                <td class="text-right font-bold">{{ $catTotGross > 0 ? round(($catTotProfit / $catTotGross) * 100, 1) : 0 }}%</td>
                                <td class="text-right font-bold text-success">{{ $kpis['currency_symbol'] }}{{ number_format($catTotProfit) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Multi-Period Sales Trend Chart --}}
            <div class="content-card">
                <div class="card-header-clean">
                    <div>
                        <h3 class="card-title">Sales Trend Trajectory</h3>
                        <p class="card-sub">Revenue rhythm across days, weeks and months</p>
                    </div>
                    <div class="chart-tab-pills">
                        <button type="button" class="btn-chart-pill active" onclick="switchTrendChart('daily')">Daily</button>
                        <button type="button" class="btn-chart-pill" onclick="switchTrendChart('weekly')">Weekly</button>
                        <button type="button" class="btn-chart-pill" onclick="switchTrendChart('monthly')">Monthly</button>
                        <button type="button" class="btn-chart-pill" onclick="switchTrendChart('yearly')">Yearly</button>
                    </div>
                </div>
                <div id="salesTrendApexChart" style="min-height: 320px;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 3: FOOD COST / COGS & RECIPE PROFITABILITY --}}
    @if ($activeTab === 'cogs')
    <div class="report-tab-content">
        {{-- High-Level COGS Card --}}
        <div class="cogs-hero-banner">
            <div class="cogs-stat-item">
                <span class="cogs-lbl">Total Food Sales</span>
                <span class="cogs-val">{{ $kpis['currency_symbol'] }}{{ number_format($foodCostCogs['summary']['sales']) }}</span>
            </div>
            <div class="cogs-stat-item border-l">
                <span class="cogs-lbl">Food Cost (COGS)</span>
                <span class="cogs-val text-warning">{{ $kpis['currency_symbol'] }}{{ number_format($foodCostCogs['summary']['food_cost']) }}</span>
                <span class="cogs-pct">{{ $foodCostCogs['summary']['food_cost_percentage'] }}% Cost Ratio</span>
            </div>
            <div class="cogs-stat-item border-l">
                <span class="cogs-lbl">Gross Profit</span>
                <span class="cogs-val text-success">{{ $kpis['currency_symbol'] }}{{ number_format($foodCostCogs['summary']['gross_profit']) }}</span>
                <span class="cogs-pct">{{ $foodCostCogs['summary']['gross_margin'] }}% Gross Margin</span>
            </div>
        </div>

        {{-- Beef Steak Detailed Recipe Costing (Exact User Example) --}}
        <div class="content-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Recipe Ingredient Costing: {{ $foodCostCogs['featured_recipe']['item_name'] }}</h3>
                    <p class="card-sub">Dish-level Cost of Goods Sold (COGS) vs Selling Price breakdown</p>
                </div>
                <span class="badge-featured-recipe"><i class="ti ti-star"></i> Featured Recipe</span>
            </div>

            <div class="recipe-costing-grid">
                <div class="recipe-table-wrap table-responsive">
                    <table class="report-data-table">
                        <thead>
                            <tr>
                                <th>Ingredient / Component</th>
                                <th>Portion Size</th>
                                <th class="text-right">Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($foodCostCogs['featured_recipe']['ingredients'] as $ing)
                            <tr>
                                <td class="font-medium">{{ $ing['ingredient'] }}</td>
                                <td class="text-muted">{{ $ing['unit'] }}</td>
                                <td class="text-right font-medium">{{ $kpis['currency_symbol'] }}{{ number_format($ing['cost']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-total-row">
                                <td colspan="2" class="font-bold">Total Cost (COGS)</td>
                                <td class="text-right font-bold text-warning">{{ $kpis['currency_symbol'] }}{{ number_format($foodCostCogs['featured_recipe']['total_cogs']) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Profit Visualizer Box --}}
                <div class="recipe-margin-box">
                    <div class="box-row">
                        <span class="lbl">Selling Price</span>
                        <span class="val font-bold">{{ $kpis['currency_symbol'] }}{{ number_format($foodCostCogs['featured_recipe']['selling_price']) }}</span>
                    </div>
                    <div class="box-row text-warning">
                        <span class="lbl">Ingredient Cost (COGS)</span>
                        <span class="val">- {{ $kpis['currency_symbol'] }}{{ number_format($foodCostCogs['featured_recipe']['total_cogs']) }}</span>
                    </div>
                    <div class="box-divider"></div>
                    <div class="box-row-total">
                        <span class="lbl text-success font-bold">Gross Profit</span>
                        <span class="val text-success font-bold">{{ $kpis['currency_symbol'] }}{{ number_format($foodCostCogs['featured_recipe']['gross_profit']) }}</span>
                    </div>
                    <div class="margin-badge-large">
                        {{ $foodCostCogs['featured_recipe']['margin_percentage'] }}% Gross Profit Margin
                    </div>
                </div>
            </div>
        </div>

        {{-- Other Menu Items Costing Table --}}
        <div class="content-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Menu Dish Profitability Comparison</h3>
                    <p class="card-sub">Identify top earning vs high-cost dishes</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="report-data-table">
                    <thead>
                        <tr>
                            <th>Dish Name</th>
                            <th class="text-right">COGS Cost</th>
                            <th class="text-right">Selling Price</th>
                            <th class="text-right">Gross Profit</th>
                            <th class="text-right">Margin %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($foodCostCogs['other_recipes'] as $dish)
                        <tr>
                            <td class="font-medium text-main">{{ $dish['name'] }}</td>
                            <td class="text-right text-warning">{{ $kpis['currency_symbol'] }}{{ number_format($dish['cogs']) }}</td>
                            <td class="text-right font-medium">{{ $kpis['currency_symbol'] }}{{ number_format($dish['selling']) }}</td>
                            <td class="text-right text-success font-bold">{{ $kpis['currency_symbol'] }}{{ number_format($dish['profit']) }}</td>
                            <td class="text-right font-medium">{{ $dish['margin'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 4: PROFIT & LOSS (P&L) SUMMARY --}}
    @if ($activeTab === 'pnl')
    <div class="report-tab-content">
        <div class="content-card pnl-statement-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Profit & Loss (P&L) Summary Statement</h3>
                    <p class="card-sub">Owner-level financial report incorporating revenue, COGS and operational expenses</p>
                </div>
                <span class="badge-audited"><i class="ti ti-lock"></i> Owner Confidential</span>
            </div>

            <div class="pnl-statement-table-wrap">
                <table class="report-data-table pnl-table">
                    <tbody>
                        <tr class="pnl-section-header">
                            <td colspan="2">1. Operating Revenue</td>
                        </tr>
                        <tr class="pnl-line-item highlight">
                            <td class="font-bold">Total Gross Revenue</td>
                            <td class="text-right font-bold text-main">{{ $pnl['currency_symbol'] }}{{ number_format($pnl['revenue']) }}</td>
                        </tr>

                        <tr class="pnl-section-header">
                            <td colspan="2">2. Cost of Goods Sold (COGS)</td>
                        </tr>
                        <tr class="pnl-line-item">
                            <td>Cost of Ingredients & Raw Food</td>
                            <td class="text-right text-warning">- {{ $pnl['currency_symbol'] }}{{ number_format($pnl['cogs']) }}</td>
                        </tr>
                        <tr class="pnl-subtotal-row">
                            <td class="font-bold">Gross Profit (Revenue - COGS)</td>
                            <td class="text-right font-bold text-success">{{ $pnl['currency_symbol'] }}{{ number_format($pnl['gross_profit']) }}</td>
                        </tr>

                        <tr class="pnl-section-header">
                            <td colspan="2">3. Operating Expenses (OPEX)</td>
                        </tr>
                        <tr class="pnl-line-item">
                            <td>Labor Cost (Salaries & Overtime)</td>
                            <td class="text-right text-danger">- {{ $pnl['currency_symbol'] }}{{ number_format($pnl['operating_expenses']['labor_cost']) }}</td>
                        </tr>
                        <tr class="pnl-line-item">
                            <td>Restaurant Facility Rent</td>
                            <td class="text-right text-danger">- {{ $pnl['currency_symbol'] }}{{ number_format($pnl['operating_expenses']['rent']) }}</td>
                        </tr>
                        <tr class="pnl-line-item">
                            <td>Utilities (Electricity, Water, Fuel)</td>
                            <td class="text-right text-danger">- {{ $pnl['currency_symbol'] }}{{ number_format($pnl['operating_expenses']['utilities']) }}</td>
                        </tr>
                        <tr class="pnl-line-item">
                            <td>Other Operating & Maintenance Expenses</td>
                            <td class="text-right text-danger">- {{ $pnl['currency_symbol'] }}{{ number_format($pnl['operating_expenses']['other_expenses']) }}</td>
                        </tr>
                        <tr class="pnl-subtotal-row">
                            <td class="font-bold">Total Operating Expenses</td>
                            <td class="text-right font-bold text-danger">- {{ $pnl['currency_symbol'] }}{{ number_format($pnl['operating_expenses']['total_opex']) }}</td>
                        </tr>

                        <tr class="pnl-net-profit-row">
                            <td>
                                <span class="net-title">Estimated Net Profit</span>
                                <span class="net-sub">(Gross Profit - Operating Expenses)</span>
                            </td>
                            <td class="text-right">
                                <span class="net-amount">{{ $pnl['currency_symbol'] }}{{ number_format($pnl['estimated_net_profit']) }}</span>
                                <span class="net-margin-badge">{{ $pnl['net_profit_margin'] }}% Net Margin</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 5: ALCOHOL SALES REPORT --}}
    @if ($activeTab === 'alcohol')
    <div class="report-tab-content">
        <div class="content-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Alcohol & Bar Sales Report</h3>
                    <p class="card-sub">High-margin alcoholic beverages tracking and compliance</p>
                </div>
                <div class="total-alcohol-badge">
                    <span>Total Alcohol Sales:</span>
                    <strong>{{ $kpis['currency_symbol'] }}{{ number_format($alcohol['total_alcohol_sales']) }}</strong>
                </div>
            </div>

            <div class="grid-alcohol-cards">
                @foreach ($alcohol['breakdown'] as $alc)
                <div class="alcohol-card">
                    <div class="alc-icon">{{ $alc['icon'] }}</div>
                    <div class="alc-info">
                        <h4 class="alc-title">{{ $alc['category'] }}</h4>
                        <div class="alc-sales">{{ $kpis['currency_symbol'] }}{{ number_format($alc['sales']) }}</div>
                        <div class="alc-share">{{ $alc['percentage'] }}% of Bar Revenue</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 6: TABLE & DINE-IN PERFORMANCE --}}
    @if ($activeTab === 'tables')
    <div class="report-tab-content">
        {{-- Table KPIs Summary --}}
        <div class="grid-table-stats">
            <div class="stat-pill-card">
                <span class="lbl">Table Turnover</span>
                <span class="val">{{ $tablePerf['summary']['overall_turnover'] }} turns/table</span>
            </div>
            <div class="stat-pill-card">
                <span class="lbl">Floor Occupancy</span>
                <span class="val text-success">{{ $tablePerf['summary']['overall_occupancy'] }}</span>
            </div>
            <div class="stat-pill-card">
                <span class="lbl">Avg Spend / Table</span>
                <span class="val">{{ $kpis['currency_symbol'] }}{{ number_format($tablePerf['summary']['avg_spend_per_table']) }}</span>
            </div>
            <div class="stat-pill-card">
                <span class="lbl">Avg Dining Duration</span>
                <span class="val">{{ $tablePerf['summary']['avg_dining_duration'] }}</span>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Table-Level Performance Breakdown</h3>
                    <p class="card-sub">Analyze revenue and throughput by individual dining tables</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="report-data-table">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th class="text-right">Orders</th>
                            <th class="text-right">Customer Count</th>
                            <th class="text-right">Total Sales</th>
                            <th class="text-right">Avg Spend / Guest</th>
                            <th class="text-right">Turnover</th>
                            <th class="text-center">Avg Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tablePerf['tables'] as $tbl)
                        <tr>
                            <td class="font-bold text-main">
                                <i class="ti ti-armchair text-muted mr-1"></i> {{ $tbl['table'] }}
                            </td>
                            <td class="text-right font-medium">{{ $tbl['orders'] }}</td>
                            <td class="text-right font-medium">{{ $tbl['customer_count'] }}</td>
                            <td class="text-right font-bold text-success">{{ $kpis['currency_symbol'] }}{{ number_format($tbl['sales']) }}</td>
                            <td class="text-right font-medium">{{ $kpis['currency_symbol'] }}{{ number_format($tbl['average_spend']) }}</td>
                            <td class="text-right font-medium">{{ $tbl['turnover_rate'] }}</td>
                            <td class="text-center"><span class="badge-duration">{{ $tbl['avg_duration'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 7: ORDER TYPES --}}
    @if ($activeTab === 'ordertypes')
    <div class="report-tab-content">
        <div class="content-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Order Type Revenue Distribution</h3>
                    <p class="card-sub">Dine-in, Takeaway, Delivery, and Customer Pickup performance</p>
                </div>
            </div>

            <div class="grid-ordertype-cards">
                @foreach ($orderTypes as $ot)
                <div class="order-type-card">
                    <div class="ot-icon">{{ $ot['icon'] }}</div>
                    <div class="ot-details">
                        <h4 class="ot-name">{{ $ot['type'] }}</h4>
                        <div class="ot-amount">{{ $kpis['currency_symbol'] }}{{ number_format($ot['amount']) }}</div>
                        <div class="ot-pct-wrap">
                            <div class="ot-bar">
                                <div class="ot-fill" style="width: {{ $ot['percentage'] }}%;"></div>
                            </div>
                            <span class="ot-pct-label">{{ $ot['percentage'] }}% of volume</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 8: CANCELLATIONS & VOIDS ANALYSIS --}}
    @if ($activeTab === 'voids')
    <div class="report-tab-content">
        <div class="content-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Cancellation & Void Audit Analysis</h3>
                    <p class="card-sub">Supervised tracking of cancelled tickets, voided dishes, reasons, and approvals</p>
                </div>
                <div class="void-alert-pill">
                    <i class="ti ti-alert-triangle"></i>
                    <span>Cancelled Amount: {{ $kpis['currency_symbol'] }}{{ number_format($voidAnalysis['total_cancelled_amount']) }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="report-data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Table</th>
                            <th>Item / Detail</th>
                            <th class="text-right">Amount</th>
                            <th>Void Reason</th>
                            <th>Employee</th>
                            <th>Approved By</th>
                            <th class="text-right">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($voidAnalysis['records'] as $v)
                        <tr>
                            <td class="font-bold text-main">{{ $v['order_number'] }}</td>
                            <td>{{ $v['table'] }}</td>
                            <td class="font-medium">{{ $v['item'] }}</td>
                            <td class="text-right font-bold text-danger">{{ $kpis['currency_symbol'] }}{{ number_format($v['amount']) }}</td>
                            <td>
                                <span class="badge-reason">
                                    <i class="ti ti-info-circle"></i> {{ $v['reason'] }}
                                </span>
                            </td>
                            <td>{{ $v['employee'] }}</td>
                            <td><span class="badge-approver"><i class="ti ti-shield-check"></i> {{ $v['approved_by'] }}</span></td>
                            <td class="text-right text-muted">{{ $v['time'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 9: PROMOTIONS ROI --}}
    @if ($activeTab === 'promotions')
    <div class="report-tab-content">
        <div class="content-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Promotion & Campaign Performance</h3>
                    <p class="card-sub">Measure discount budget utilization versus sales lift</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="report-data-table">
                    <thead>
                        <tr>
                            <th>Promotion Campaign</th>
                            <th class="text-right">Orders Used</th>
                            <th class="text-right">Discounts Given</th>
                            <th class="text-right">Revenue Generated</th>
                            <th class="text-right">Net Profit Impact</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promotions as $promo)
                        <tr>
                            <td class="font-bold text-main">
                                <i class="ti ti-tag text-success mr-1"></i> {{ $promo['promotion'] }}
                            </td>
                            <td class="text-right font-medium">{{ $promo['orders_used'] }} orders</td>
                            <td class="text-right font-medium text-danger">- {{ $kpis['currency_symbol'] }}{{ number_format($promo['discount_amount']) }}</td>
                            <td class="text-right font-bold text-success">{{ $kpis['currency_symbol'] }}{{ number_format($promo['revenue_generated']) }}</td>
                            <td class="text-right font-bold text-info">{{ $promo['profit_impact'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 10: COMPARISON & FORECAST --}}
    @if ($activeTab === 'comparison')
    <div class="report-tab-content">
        {{-- Sales Comparisons --}}
        <div class="grid-comparison-cards">
            <div class="comparison-card">
                <div class="comp-header">
                    <span class="comp-title">Today vs Yesterday</span>
                    <span class="comp-diff-badge positive">+{{ $comparisons['today_vs_yesterday']['percentage'] }}</span>
                </div>
                <div class="comp-metrics">
                    <div class="comp-col">
                        <span class="lbl">Today</span>
                        <span class="val font-bold text-main">{{ $kpis['currency_symbol'] }}{{ number_format($comparisons['today_vs_yesterday']['current']) }}</span>
                    </div>
                    <div class="comp-vs">vs</div>
                    <div class="comp-col">
                        <span class="lbl">Yesterday</span>
                        <span class="val text-muted">{{ $kpis['currency_symbol'] }}{{ number_format($comparisons['today_vs_yesterday']['previous']) }}</span>
                    </div>
                </div>
                <div class="comp-footer text-success">
                    <i class="ti ti-trending-up"></i> Net Gain: +{{ $kpis['currency_symbol'] }}{{ number_format($comparisons['today_vs_yesterday']['difference']) }}
                </div>
            </div>

            <div class="comparison-card">
                <div class="comp-header">
                    <span class="comp-title">This Week vs Last Week</span>
                    <span class="comp-diff-badge positive">+{{ $comparisons['this_week_vs_last_week']['percentage'] }}</span>
                </div>
                <div class="comp-metrics">
                    <div class="comp-col">
                        <span class="lbl">This Week</span>
                        <span class="val font-bold text-main">{{ $kpis['currency_symbol'] }}{{ number_format($comparisons['this_week_vs_last_week']['current']) }}</span>
                    </div>
                    <div class="comp-vs">vs</div>
                    <div class="comp-col">
                        <span class="lbl">Last Week</span>
                        <span class="val text-muted">{{ $kpis['currency_symbol'] }}{{ number_format($comparisons['this_week_vs_last_week']['previous']) }}</span>
                    </div>
                </div>
                <div class="comp-footer text-success">
                    <i class="ti ti-trending-up"></i> Net Gain: +{{ $kpis['currency_symbol'] }}{{ number_format($comparisons['this_week_vs_last_week']['difference']) }}
                </div>
            </div>

            <div class="comparison-card">
                <div class="comp-header">
                    <span class="comp-title">This Month vs Last Month</span>
                    <span class="comp-diff-badge positive">+{{ $comparisons['this_month_vs_last_month']['percentage'] }}</span>
                </div>
                <div class="comp-metrics">
                    <div class="comp-col">
                        <span class="lbl">This Month</span>
                        <span class="val font-bold text-main">{{ $kpis['currency_symbol'] }}{{ number_format($comparisons['this_month_vs_last_month']['current']) }}</span>
                    </div>
                    <div class="comp-vs">vs</div>
                    <div class="comp-col">
                        <span class="lbl">Last Month</span>
                        <span class="val text-muted">{{ $kpis['currency_symbol'] }}{{ number_format($comparisons['this_month_vs_last_month']['previous']) }}</span>
                    </div>
                </div>
                <div class="comp-footer text-success">
                    <i class="ti ti-trending-up"></i> Net Gain: +{{ $kpis['currency_symbol'] }}{{ number_format($comparisons['this_month_vs_last_month']['difference']) }}
                </div>
            </div>
        </div>

        {{-- Forecast & Moving Average Projection --}}
        <div class="content-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Sales Forecast & Predictive Velocity</h3>
                    <p class="card-sub">{{ $forecast['notice'] }}</p>
                </div>
                <span class="badge-forecast-estimate"><i class="ti ti-sparkles"></i> AI Moving-Average Projection</span>
            </div>

            <div class="forecast-grid">
                <div class="forecast-hist-col">
                    <h4 class="col-title">Recent Actual Days</h4>
                    @foreach ($forecast['historical_days'] as $hDay)
                    <div class="f-row">
                        <span class="f-day">{{ $hDay['day'] }}</span>
                        <span class="f-val">{{ $kpis['currency_symbol'] }}{{ number_format($hDay['sales']) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="forecast-pred-col">
                    <h4 class="col-title text-info">Upcoming Forecasted Days (Estimate)</h4>
                    @foreach ($forecast['forecast_days'] as $fDay)
                    <div class="f-row forecast-highlight">
                        <div>
                            <span class="f-day font-bold">{{ $fDay['day'] }}</span>
                            <span class="f-confidence"><i class="ti ti-bolt"></i> {{ $fDay['confidence'] }} confidence</span>
                        </div>
                        <span class="f-val text-info font-bold">{{ $kpis['currency_symbol'] }}{{ number_format($fDay['predicted_sales']) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TAB 11: AUDIT LOGS --}}
    @if ($activeTab === 'audit')
    <div class="report-tab-content">
        <div class="content-card">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title">Operational Audit & Change Log</h3>
                    <p class="card-sub">Immutable security trail tracking price modifications, bill voids and discount overrides</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="report-data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Resource</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                            <th>IP Address</th>
                            <th class="text-right">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($auditLogs as $log)
                        <tr>
                            <td class="font-bold text-main">{{ $log['user'] }}</td>
                            <td><span class="badge-action">{{ $log['action'] }}</span></td>
                            <td class="font-medium">{{ $log['resource'] }}</td>
                            <td class="text-muted">{{ $log['old_value'] }}</td>
                            <td class="text-success font-medium">{{ $log['new_value'] }}</td>
                            <td class="text-muted">{{ $log['ip'] }}</td>
                            <td class="text-right text-muted">{{ $log['date_time'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- Modern Responsive CSS Styling Matching Other Sectors --}}
<style>
/* Global Typography & Core Wrapper */
.reports-container {
    font-family: "Mada", sans-serif;
    color: var(--text-main);
    padding: 0.25rem 0;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow-x: hidden;
    box-sizing: border-box;
}

.reports-container *,
.reports-container *::before,
.reports-container *::after {
    box-sizing: border-box;
}

/* Defensive min-width for grid/flex items */
.reports-header-card,
.content-card,
.kpi-card,
.overview-spec-container,
.audit-item,
.category-row-item,
.alcohol-card,
.order-type-card,
.comparison-card,
.forecast-hist-col,
.forecast-pred-col,
.table-responsive,
.chart-container-wrap,
#salesTrendApexChart {
    min-width: 0;
    max-width: 100%;
}

/* Header Card (Clean, Non-Gray) */
.reports-header-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg, 20px);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.reports-title-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}

.title-group {
    flex: 1;
    min-width: 260px;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    background: rgba(158, 198, 59, 0.12);
    color: var(--primary);
    border: 1px solid rgba(158, 198, 59, 0.28);
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 0.6rem;
}

.page-title {
    font-size: 1.625rem;
    font-weight: 700;
    margin: 0 0 0.35rem 0;
    color: var(--text-main);
    letter-spacing: -0.02em;
}

.page-subtitle {
    font-size: 0.875rem;
    font-weight: 400;
    color: var(--text-muted);
    margin: 0;
    line-height: 1.5;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    border-radius: var(--radius-sm, 8px);
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: var(--transition);
    white-space: nowrap;
}

.btn-export {
    background: linear-gradient(135deg, #9ec63b, #7ea826);
    color: #ffffff !important;
    border: none;
    box-shadow: 0 4px 12px rgba(158, 198, 59, 0.28);
}
.btn-export:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(158, 198, 59, 0.38);
    filter: brightness(1.05);
}

.btn-print {
    background: var(--bg-card);
    color: var(--text-main);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
}
.btn-print:hover {
    background: var(--bg-hover);
    border-color: var(--primary);
    color: var(--primary);
}

/* Period Pills Filter Bar */
.period-filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 0.95rem 0;
    border-top: 1px solid var(--border-subtle);
    border-bottom: 1px solid var(--border-subtle);
}

.period-pills-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
}

.period-pill {
    padding: 0.45rem 1rem;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-muted);
    background: var(--bg-card);
    text-decoration: none;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    white-space: nowrap;
}
.period-pill:hover {
    color: var(--primary);
    border-color: var(--primary);
    background: rgba(158, 198, 59, 0.08);
}
.period-pill.active {
    background: linear-gradient(135deg, #9ec63b, #7ea826);
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(158, 198, 59, 0.35);
}
.period-pill.custom-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
}

.active-period-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--primary);
    background: rgba(158, 198, 59, 0.12);
    padding: 0.35rem 0.9rem;
    border-radius: 20px;
    border: 1px solid rgba(158, 198, 59, 0.28);
}

/* Custom Range Dropdown */
.custom-range-card {
    display: none;
    padding: 1.25rem;
    background: var(--bg-card);
    border-radius: var(--radius-md, 14px);
    margin: 1rem 0;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
}
.custom-range-card.show {
    display: block;
}
.custom-range-form {
    display: flex;
    align-items: flex-end;
    gap: 1rem;
    flex-wrap: wrap;
}
.form-group-range {
    flex: 1;
    min-width: 160px;
}
.form-group-range label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: 0.35rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.range-input {
    width: 100%;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    color: var(--text-main);
    padding: 0.55rem 0.85rem;
    border-radius: var(--radius-sm, 8px);
    font-size: 0.875rem;
    font-family: inherit;
    transition: var(--transition);
}
.range-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.2);
}
.btn-apply-range {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: linear-gradient(135deg, #9ec63b, #7ea826);
    color: #ffffff;
    font-size: 0.875rem;
    font-weight: 700;
    padding: 0.6rem 1.25rem;
    border: none;
    border-radius: var(--radius-sm, 8px);
    cursor: pointer;
    transition: var(--transition);
    height: 40px;
    box-shadow: 0 4px 12px rgba(158, 198, 59, 0.28);
}
.btn-apply-range:hover {
    filter: brightness(1.05);
    transform: translateY(-1px);
}

/* Domain Navigator Tab Bar */
.domain-tabs-wrapper {
    margin-top: 1.15rem;
}
.domain-tabs-scroll {
    display: flex;
    overflow-x: auto;
    gap: 0.5rem;
    padding-bottom: 0.4rem;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}
.domain-tabs-scroll::-webkit-scrollbar {
    height: 4px;
}
.domain-tabs-scroll::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}
.domain-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.55rem 1.1rem;
    border-radius: 12px;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-muted);
    background: var(--bg-card);
    text-decoration: none;
    white-space: nowrap;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    flex-shrink: 0;
    transition: var(--transition);
}
.domain-tab-btn:hover {
    color: var(--text-main);
    border-color: var(--primary);
    background: rgba(158, 198, 59, 0.08);
    transform: translateY(-1px);
}
.domain-tab-btn.active {
    background: rgba(158, 198, 59, 0.12);
    color: var(--primary);
    border-color: var(--primary);
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(158, 198, 59, 0.2);
}

/* Content Cards Base (Matching Dashboard & Other Sectors) */
.content-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg, 20px);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    margin-bottom: 1.5rem;
}
.content-card:hover {
    box-shadow: var(--shadow-md);
}

.card-header-clean {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1rem;
    margin-bottom: 1.25rem;
    border-bottom: 1px solid var(--border-subtle);
    flex-wrap: wrap;
    gap: 0.75rem;
}
.card-title {
    font-size: 1.05rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    color: var(--text-main);
}
.card-sub {
    font-size: 0.8125rem;
    font-weight: 400;
    color: var(--text-muted);
    margin: 0;
}
.status-live-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    background: rgba(158, 198, 59, 0.12);
    color: var(--primary);
    border: 1px solid rgba(158, 198, 59, 0.28);
    font-size: 0.75rem;
    font-weight: 700;
}

/* Top 4 Hero KPIs */
.grid-kpi-highlight {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
.kpi-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1.25rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
}
.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.85rem;
}
.kpi-header-left {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}
.kpi-avatar-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.kpi-avatar-green {
    background-color: rgba(16, 185, 129, 0.12);
    color: #10b981;
}
.kpi-avatar-blue {
    background-color: rgba(59, 130, 246, 0.12);
    color: #3b82f6;
}
.kpi-avatar-orange {
    background-color: rgba(245, 158, 11, 0.12);
    color: #f59e0b;
}
.kpi-avatar-violet {
    background-color: rgba(139, 92, 246, 0.12);
    color: #8b5cf6;
}
.kpi-title {
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--text-muted);
    letter-spacing: 0.02em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kpi-tag-success {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--primary);
    background: rgba(158, 198, 59, 0.12);
    padding: 0.2rem 0.55rem;
    border-radius: 12px;
    border: 1px solid rgba(158, 198, 59, 0.25);
    white-space: nowrap;
}
.kpi-tag-profit {
    font-size: 0.75rem;
    font-weight: 700;
    color: #10b981;
    background: rgba(16, 185, 129, 0.12);
    padding: 0.2rem 0.55rem;
    border-radius: 12px;
    border: 1px solid rgba(16, 185, 129, 0.25);
    white-space: nowrap;
}
.kpi-badge-neutral {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
    background: var(--bg-card);
    padding: 0.2rem 0.55rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    white-space: nowrap;
}
.kpi-metric-main {
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
}
.kpi-footer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--text-muted);
    flex-wrap: wrap;
}
.kpi-dot {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--text-muted);
}

/* Overview Dual Grid */
.overview-dual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

/* Today's Overview Card (Clean, Unique, Non-Gray) */
.overview-spec-container {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1.25rem 1.5rem;
    box-shadow: var(--shadow-sm);
}
.spec-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.55rem 0;
    font-size: 0.875rem;
    color: var(--text-main);
    border-bottom: 1px dashed var(--border-subtle);
}
.spec-row:last-child {
    border-bottom: none;
}
.spec-row-highlight {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 0;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--primary);
    border-bottom: 1px solid var(--border-color);
}
.spec-label {
    font-weight: 600;
    color: var(--text-muted);
}
.spec-value {
    font-weight: 700;
    color: var(--text-main);
}
.spec-divider {
    height: 1px;
    background: var(--border-color);
    margin: 0.85rem 0;
}
.spec-divider-double {
    height: 2px;
    background: var(--border-color);
    margin: 0.85rem 0;
}
.spec-row-net {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.65rem;
}
.spec-net-label {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-main);
}
.spec-net-value {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--primary);
}

/* Grid Audit Metrics (Clean, Non-Gray, Dedicated Avatars) */
.grid-audit-metrics {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.audit-item {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 0.95rem 1.15rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.audit-item:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}
.audit-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}
.audit-icon.icon-green {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
}
.audit-icon.icon-warning {
    background: rgba(245, 158, 11, 0.12);
    color: #f59e0b;
}
.audit-icon.icon-info {
    background: rgba(59, 130, 246, 0.12);
    color: #3b82f6;
}
.audit-icon.icon-lime {
    background: rgba(158, 198, 59, 0.15);
    color: #9ec63b;
}
.audit-icon.icon-violet {
    background: rgba(139, 92, 246, 0.12);
    color: #8b5cf6;
}
.audit-icon.icon-danger {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
}

.audit-lbl {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
}
.audit-val {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-main);
}

/* Cash Settlement Bar */
.cash-settlement-bar {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1.25rem;
    box-shadow: var(--shadow-sm);
}
.cash-settle-title {
    display: flex;
    justify-content: space-between;
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 0.75rem;
}
.settle-status {
    color: var(--primary);
}
.settle-progress-multi {
    height: 8px;
    display: flex;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.75rem;
}
.progress-chunk.kbzpay { background: #3b82f6; }
.progress-chunk.cash { background: #10b981; }
.progress-chunk.wavepay { background: #eab308; }
.progress-chunk.card { background: #8b5cf6; }

.settle-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
}
.legend-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 0.35rem;
}
.legend-dot.kbzpay { background: #3b82f6; }
.legend-dot.cash { background: #10b981; }
.legend-dot.wavepay { background: #eab308; }
.legend-dot.card { background: #8b5cf6; }

/* Tables Responsive & Styled (Clean Surface) */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: var(--radius-md, 14px);
    border: 1px solid var(--border-color);
    background-color: var(--bg-card);
    box-shadow: var(--shadow-sm);
}
.report-data-table {
    width: 100%;
    min-width: 620px;
    border-collapse: collapse;
    font-size: 0.875rem;
    white-space: nowrap;
}
.report-data-table th {
    padding: 0.95rem 1.25rem;
    background-color: var(--bg-card);
    color: var(--text-muted);
    font-weight: 700;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.report-data-table td {
    padding: 0.95rem 1.25rem;
    border-bottom: 1px solid var(--border-subtle);
    color: var(--text-main);
    background-color: var(--bg-card);
}
.report-data-table tbody tr:hover td {
    background-color: rgba(158, 198, 59, 0.05);
}
.table-total-row td {
    background-color: rgba(158, 198, 59, 0.08) !important;
    border-top: 2px solid var(--border-color);
    font-weight: 700;
}
.badge-audited {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: #10b981;
    background: rgba(16, 185, 129, 0.12);
    padding: 0.25rem 0.65rem;
    border-radius: 12px;
    border: 1px solid rgba(16, 185, 129, 0.25);
}

/* Category Row Items (Clean Card Style) */
.category-breakdown-list {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}
.category-row-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1rem 1.25rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.category-row-item:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}
.cat-left {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    min-width: 190px;
}
.cat-icon { font-size: 1.5rem; }
.cat-name {
    font-size: 0.875rem;
    font-weight: 700;
    margin: 0;
    color: var(--text-main);
}
.cat-orders { font-size: 0.75rem; color: var(--text-muted); }
.cat-progress-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.cat-progress-bar {
    flex: 1;
    height: 7px;
    background: var(--border-color);
    border-radius: 4px;
    overflow: hidden;
}
.cat-progress-fill { height: 100%; border-radius: 4px; }
.cat-pct { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); min-width: 60px; }
.cat-right { text-align: right; }
.cat-sales {
    display: block;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-main);
}
.cat-margin-badge {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--primary);
}

/* Food Cost & COGS Hero Banner */
.cogs-hero-banner {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg, 20px);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    gap: 1.5rem;
}
.cogs-stat-item {
    text-align: center;
}
.cogs-stat-item.border-l {
    border-left: 1px solid var(--border-color);
}
.cogs-lbl { display: block; font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.35rem; }
.cogs-val { font-size: 1.5rem; font-weight: 700; color: var(--text-main); }
.cogs-pct { display: block; font-size: 0.75rem; font-weight: 700; margin-top: 0.25rem; color: var(--text-muted); }

.recipe-costing-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
}
.recipe-margin-box {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.box-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    padding: 0.45rem 0;
    color: var(--text-main);
}
.box-divider { height: 1px; background: var(--border-color); margin: 0.85rem 0; }
.box-row-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.15rem;
    margin-bottom: 1rem;
}
.margin-badge-large {
    text-align: center;
    background: rgba(158, 198, 59, 0.12);
    color: var(--primary);
    padding: 0.65rem;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.875rem;
    border: 1px solid rgba(158, 198, 59, 0.3);
}

/* P&L Statement */
.pnl-statement-table-wrap {
    background-color: var(--bg-card);
    border-radius: var(--radius-md, 14px);
    overflow: hidden;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
}
.pnl-section-header td {
    background-color: rgba(158, 198, 59, 0.06);
    font-weight: 700;
    font-size: 0.8125rem;
    color: var(--text-main);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.85rem 1.25rem;
}
.pnl-subtotal-row td {
    background-color: rgba(158, 198, 59, 0.04);
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
    font-weight: 700;
}
.pnl-net-profit-row td {
    background: rgba(158, 198, 59, 0.08);
    border-top: 2px solid var(--primary);
    padding: 1.35rem 1.25rem;
}
.net-title { display: block; font-size: 1.2rem; font-weight: 700; color: var(--primary); }
.net-sub { font-size: 0.75rem; color: var(--text-muted); }
.net-amount { display: block; font-size: 1.5rem; font-weight: 800; color: var(--primary); }
.net-margin-badge {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 700;
    color: #10b981;
    background: rgba(16, 185, 129, 0.12);
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    margin-top: 0.3rem;
}

/* Alcohol Cards (Clean Card Style) */
.grid-alcohol-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
}
.alcohol-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1.25rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.alcohol-card:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}
.alc-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: rgba(158, 198, 59, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    flex-shrink: 0;
}
.alc-title { font-size: 0.875rem; font-weight: 700; margin: 0 0 0.25rem 0; color: var(--text-main); }
.alc-sales { font-size: 1.2rem; font-weight: 700; color: var(--text-main); }
.alc-share { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); }
.total-alcohol-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(158, 198, 59, 0.12);
    padding: 0.4rem 0.85rem;
    border-radius: 20px;
    border: 1px solid rgba(158, 198, 59, 0.28);
    font-size: 0.8125rem;
    color: var(--primary);
}

/* Table Analytics */
.grid-table-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
.stat-pill-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1.25rem;
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.stat-pill-card:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}
.stat-pill-card .lbl { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.35rem; }
.stat-pill-card .val { font-size: 1.2rem; font-weight: 700; color: var(--text-main); }
.badge-duration {
    background: rgba(158, 198, 59, 0.1);
    color: var(--primary);
    border: 1px solid rgba(158, 198, 59, 0.25);
    padding: 0.25rem 0.65rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
}

/* Order Types (Clean Card Style) */
.grid-ordertype-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
}
.order-type-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1.25rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.order-type-card:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}
.ot-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(59, 130, 246, 0.12);
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    flex-shrink: 0;
}
.ot-details { flex: 1; }
.ot-name { font-size: 0.875rem; font-weight: 700; margin: 0 0 0.25rem 0; color: var(--text-main); }
.ot-amount { font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.45rem; }
.ot-bar { height: 6px; background: var(--border-color); border-radius: 3px; overflow: hidden; margin-bottom: 0.25rem; }
.ot-fill { height: 100%; background: linear-gradient(135deg, #9ec63b, #7ea826); border-radius: 3px; }
.ot-pct-label { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); }

/* Voids & Approvals */
.badge-reason {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: rgba(244, 63, 94, 0.1);
    color: #f43f5e;
    padding: 0.25rem 0.6rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1px solid rgba(244, 63, 94, 0.2);
}
.badge-approver {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    padding: 0.25rem 0.6rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1px solid rgba(59, 130, 246, 0.2);
}
.void-alert-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(244, 63, 94, 0.1);
    color: #f43f5e;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    border: 1px solid rgba(244, 63, 94, 0.25);
    font-size: 0.8125rem;
    font-weight: 700;
}

/* Comparisons (Clean Cards) */
.grid-comparison-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
.comparison-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1.35rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.comparison-card:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.comp-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.comp-title { font-size: 0.875rem; font-weight: 700; color: var(--text-muted); }
.comp-diff-badge.positive {
    background: rgba(158, 198, 59, 0.12);
    color: var(--primary);
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.75rem;
    border: 1px solid rgba(158, 198, 59, 0.25);
}
.comp-metrics {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    padding: 0.85rem 1.15rem;
    border-radius: var(--radius-sm, 10px);
}
.comp-col .lbl { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.2rem; }
.comp-col .val { font-size: 1.2rem; font-weight: 700; }
.comp-vs { font-size: 0.8125rem; color: var(--text-muted); font-weight: 700; }
.comp-footer { font-size: 0.8125rem; font-weight: 700; display: flex; align-items: center; gap: 0.35rem; }

/* Forecast Grid (Clean Cards) */
.forecast-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}
.forecast-hist-col, .forecast-pred-col {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md, 14px);
    padding: 1.35rem;
    box-shadow: var(--shadow-sm);
}
.col-title { font-size: 0.875rem; font-weight: 700; margin: 0 0 1rem 0; color: var(--text-main); }
.f-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 0;
    border-bottom: 1px solid var(--border-subtle);
    font-size: 0.875rem;
}
.f-row:last-child { border-bottom: none; }
.f-row.forecast-highlight {
    background-color: rgba(158, 198, 59, 0.08);
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border: 1px solid rgba(158, 198, 59, 0.25);
}
.f-confidence { display: block; font-size: 0.72rem; color: #3b82f6; font-weight: 700; }
.badge-forecast-estimate {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(158, 198, 59, 0.12);
    color: var(--primary);
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1px solid rgba(158, 198, 59, 0.28);
}

/* Action Buttons & Helpers */
.btn-sm-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.85rem;
    border-radius: var(--radius-sm, 8px);
    background: var(--bg-card);
    color: var(--text-main);
    font-size: 0.8125rem;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.btn-sm-action:hover {
    background: var(--bg-hover);
    border-color: var(--primary);
    color: var(--primary);
}
.chart-tab-pills {
    display: flex;
    gap: 0.35rem;
}
.btn-chart-pill {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    color: var(--text-muted);
    padding: 0.35rem 0.85rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.btn-chart-pill:hover {
    color: var(--primary);
    border-color: var(--primary);
}
.btn-chart-pill.active {
    background: linear-gradient(135deg, #9ec63b, #7ea826);
    color: #ffffff;
    font-weight: 700;
    border-color: transparent;
    box-shadow: 0 2px 8px rgba(158, 198, 59, 0.3);
}
.badge-action {
    background: rgba(158, 198, 59, 0.12);
    color: var(--primary);
    padding: 0.25rem 0.6rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1px solid rgba(158, 198, 59, 0.25);
}
.badge-featured-recipe {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: rgba(158, 198, 59, 0.12);
    color: var(--primary);
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1px solid rgba(158, 198, 59, 0.25);
}

.text-right { text-align: right; }
.text-center { text-align: center; }
.font-bold { font-weight: 700; }
.font-medium { font-weight: 500; }
.text-main { color: var(--text-main); }
.text-muted { color: var(--text-muted); }
.text-success { color: #10b981; }
.text-danger { color: #f43f5e; }
.text-warning { color: #f59e0b; }
.text-info { color: #3b82f6; }
.mr-1 { margin-right: 0.25rem; }

/* Responsive Media Queries (Mobile & Tablet Breakpoints) */
@media (max-width: 1200px) {
    .grid-kpi-highlight {
        grid-template-columns: repeat(2, 1fr);
    }
    .grid-alcohol-cards,
    .grid-ordertype-cards,
    .grid-table-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    .grid-comparison-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .overview-dual-grid,
    .recipe-costing-grid,
    .forecast-grid,
    .grid-comparison-cards {
        grid-template-columns: 1fr;
    }
    .cogs-hero-banner {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    .cogs-stat-item.border-l {
        border-left: none;
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
    }
}

@media (max-width: 768px) {
    .reports-container {
        padding: 0.25rem;
    }
    .reports-header-card,
    .content-card {
        padding: 1.15rem;
        border-radius: var(--radius-md, 14px);
    }
    .reports-title-row {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    .header-actions {
        width: 100%;
        display: flex;
        gap: 0.5rem;
    }
    .btn-action {
        flex: 1;
        justify-content: center;
        font-size: 0.8125rem;
        padding: 0.6rem 0.75rem;
    }
    .period-filter-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    .period-pills-group {
        display: flex;
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
        gap: 0.4rem;
        padding-bottom: 0.35rem;
        width: 100%;
        scrollbar-width: none;
    }
    .period-pills-group::-webkit-scrollbar {
        display: none;
    }
    .period-pill {
        flex-shrink: 0;
        padding: 0.45rem 0.85rem;
        font-size: 0.78rem;
    }
    .active-period-indicator {
        align-self: flex-start;
    }
    .grid-kpi-highlight {
        grid-template-columns: 1fr;
    }
    .grid-audit-metrics {
        grid-template-columns: 1fr;
    }
    .category-row-item {
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    .cat-left {
        min-width: 0;
        width: 100%;
        justify-content: space-between;
    }
    .cat-progress-wrap {
        width: 100%;
    }
    .cat-right {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: left;
    }
    .card-header-clean {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.65rem;
    }
    .chart-tab-pills {
        width: 100%;
        display: flex;
        overflow-x: auto;
    }
    .custom-range-form {
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    .btn-apply-range {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .grid-alcohol-cards,
    .grid-ordertype-cards,
    .grid-table-stats {
        grid-template-columns: 1fr;
    }
    .page-title {
        font-size: 1.35rem;
    }
    .kpi-metric-main {
        font-size: 1.4rem;
    }
    .overview-spec-container {
        padding: 1rem;
    }
    .spec-row {
        font-size: 0.84rem;
    }
    .spec-net-label {
        font-size: 1rem;
    }
    .spec-net-value {
        font-size: 1.25rem;
    }
}
</style>

{{-- ApexCharts Script Integration --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const trendData = @json($salesTrends);
    const chartEl = document.querySelector('#salesTrendApexChart');

    if (chartEl && trendData && typeof ApexCharts !== 'undefined') {
        const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
        const labelColor = isDarkMode ? '#94a3b8' : '#384d3b';
        const gridColor = isDarkMode ? '#222d42' : '#e2e8f0';

        const options = {
            series: [{
                name: 'Net Sales',
                data: trendData.daily.series
            }],
            chart: {
                type: 'area',
                height: 320,
                toolbar: { show: false },
                background: 'transparent'
            },
            colors: ['#9ec63b'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 95, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: trendData.daily.categories,
                labels: {
                    style: {
                        colors: labelColor,
                        fontFamily: 'Mada, sans-serif',
                        fontWeight: 600
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return '¥' + (val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val);
                    },
                    style: {
                        colors: labelColor,
                        fontFamily: 'Mada, sans-serif',
                        fontWeight: 600
                    }
                }
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4
            },
            tooltip: {
                theme: isDarkMode ? 'dark' : 'light',
                y: {
                    formatter: function(val) {
                        return '¥' + val.toLocaleString();
                    }
                }
            }
        };

        window.salesChartInstance = new ApexCharts(chartEl, options);
        window.salesChartInstance.render();
    }
});

function switchTrendChart(interval) {
    const trendData = @json($salesTrends);
    document.querySelectorAll('.btn-chart-pill').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    if (window.salesChartInstance && trendData[interval]) {
        window.salesChartInstance.updateOptions({
            xaxis: {
                categories: trendData[interval].categories
            },
            series: [{
                name: interval.charAt(0).toUpperCase() + interval.slice(1) + ' Sales',
                data: trendData[interval].series
            }]
        });
    }
}
</script>
@endpush
@endsection
