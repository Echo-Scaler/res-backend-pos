@extends('admin.layouts.app')

@section('title', $module['title'])

@push('styles')
<style>
    /* PreAdmin Style Page Breadcrumb */
    .module-breadcrumb {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .module-breadcrumb-title h4 {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
    }

    .module-breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
    }

    .module-breadcrumb-nav a {
        color: var(--text-muted);
        text-decoration: none;
    }

    .module-breadcrumb-nav a:hover {
        color: var(--primary);
    }

    /* Hero Card (PreAdmin Light Theme) */
    .module-hero {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem 2.25rem;
        margin-bottom: 1.75rem;
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
    }

    .module-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(var(--primary-rgb), 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1.75rem;
    }

    .hero-badge {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        background-color: var(--primary-light);
        color: var(--primary);
        margin-bottom: 0.85rem;
        display: inline-block;
    }

    .hero-title {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .hero-desc {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        max-width: 780px;
        margin-top: 0.5rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: var(--success-light);
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.25);
        padding: 0.45rem 0.95rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: var(--success);
        box-shadow: 0 0 8px var(--success);
    }

    .actions-panel {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex-wrap: wrap;
        padding-top: 1rem;
        border-top: 1px solid var(--border-subtle);
    }

    .btn-action-primary {
        background-color: var(--primary);
        color: #ffffff;
        padding: 0.65rem 1.35rem;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.88rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.25);
    }

    .btn-action-primary:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-action-outline {
        background-color: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        padding: 0.65rem 1.25rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.88rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
        cursor: pointer;
    }

    .btn-action-outline:hover {
        background-color: var(--bg-hover);
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Key Features Card (PreAdmin Clean White Card) */
    .features-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem 2.25rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
    }

    .features-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 1.35rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }

    .feature-item {
        background-color: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        transition: var(--transition);
    }

    .feature-item:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    .feature-check {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: var(--success-light);
        color: var(--success);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .feature-text {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-main);
    }
</style>
@endpush

@section('content')

<!-- Breadcrumb Bar -->
<div class="module-breadcrumb">
    <div class="module-breadcrumb-title">
        <h4>{{ $module['title'] }}</h4>
        <div class="module-breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home me-1"></i>Home</a>
            <span>/</span>
            <span>Modules</span>
            <span>/</span>
            <span style="color: var(--primary); font-weight: 700;">{{ $module['title'] }}</span>
        </div>
    </div>

    <div>
        <span style="font-size: 0.82rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 0.45rem 0.95rem; border-radius: var(--radius-sm); font-weight: 600; color: var(--text-main); display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: var(--shadow-sm);">
            <i class="ti ti-building-store" style="color: var(--primary);"></i>
            Branch: <strong>{{ $restaurant->name ?? 'Main Restaurant' }}</strong>
        </span>
    </div>
</div>

<!-- Main Module Hero Card -->
<div class="module-hero">
    <div class="hero-top">
        <div>
            <span class="hero-badge">{{ $module['category'] }}</span>
            <h1 class="hero-title">
                <span>{{ $module['icon'] }}</span>
                <span>{{ $module['title'] }}</span>
            </h1>
            <p class="hero-desc">{{ $module['description'] }}</p>
        </div>

        <div>
            <span class="status-pill">
                <span class="status-dot"></span>
                <span>Module Ready & Operational</span>
            </span>
        </div>
    </div>

    <div class="actions-panel">
        <a href="{{ route('admin.dashboard') }}" class="btn-action-outline">
            <i class="ti ti-arrow-left"></i>
            <span>Return to Dashboard</span>
        </a>
        <button type="button" class="btn-action-primary" onclick="alert('Configuration saved.');">
            <i class="ti ti-settings"></i>
            <span>Configure Module</span>
        </button>
        <button type="button" class="btn-action-outline" onclick="alert('Exporting data report...');">
            <i class="ti ti-download"></i>
            <span>Export Module Data</span>
        </button>
    </div>
</div>

<!-- Features Card -->
<div class="features-card">
    <h2 class="features-title">
        <i class="ti ti-bolt" style="color: #f59e0b;"></i>
        <span>Key Capabilities & Operational Features</span>
    </h2>
    <div class="features-grid">
        @foreach($module['features'] as $feature)
            <div class="feature-item">
                <div class="feature-check">
                    <i class="ti ti-check"></i>
                </div>
                <div class="feature-text">{{ $feature }}</div>
            </div>
        @endforeach
    </div>
</div>

@endsection
