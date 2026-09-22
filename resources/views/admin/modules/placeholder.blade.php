@extends('admin.layouts.app')

@section('title', $module['title'])

@push('styles')
<style>
    .module-hero {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95));
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 2.25rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .module-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .hero-badge {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        background: rgba(249, 115, 22, 0.15);
        color: #fb923c;
        border: 1px solid rgba(249, 115, 22, 0.3);
        margin-bottom: 0.75rem;
        display: inline-block;
    }

    .hero-title {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .hero-desc {
        color: var(--text-muted);
        font-size: 1rem;
        line-height: 1.6;
        max-width: 780px;
        margin-top: 0.5rem;
    }

    .features-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .features-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
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
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(51, 65, 85, 0.6);
        border-radius: 12px;
        padding: 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .feature-check {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .feature-text {
        font-weight: 600;
        font-size: 0.95rem;
        color: #f1f5f9;
    }

    .actions-panel {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .btn-action-primary {
        background: var(--primary);
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-action-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-action-outline {
        background: transparent;
        color: var(--text-main);
        border: 1px solid var(--border);
        padding: 0.75rem 1.25rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-action-outline:hover {
        background: var(--bg-card-hover);
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(16, 185, 129, 0.15);
        color: #6ee7b7;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: var(--success);
        box-shadow: 0 0 10px var(--success);
    }
</style>
@endpush

@section('content')
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

        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.75rem;">
            <span class="status-pill">
                <span class="status-dot"></span>
                <span>Module Ready & Operational</span>
            </span>
            <span style="font-size: 0.85rem; color: var(--text-muted);">
                Branch: <strong>{{ $restaurant->name ?? 'Main Restaurant' }}</strong>
            </span>
        </div>
    </div>

    <div class="actions-panel">
        <a href="{{ route('admin.dashboard') }}" class="btn-action-outline">← Return to Dashboard</a>
        <button type="button" class="btn-action-primary" onclick="alert('Configuration saved for {{ $module['title'] }}.');">
            <span>⚙️</span>
            <span>Configure {{ $module['title'] }}</span>
        </button>
        <button type="button" class="btn-action-outline" onclick="alert('Exporting data report for {{ $module['title'] }}...');">
            <span>📥</span>
            <span>Export Module Data</span>
        </button>
    </div>
</div>

<div class="features-card">
    <h2 class="features-title">
        <span>⚡</span>
        <span>Key Capabilities & Operational Features</span>
    </h2>
    <div class="features-grid">
        @foreach($module['features'] as $feature)
            <div class="feature-item">
                <div class="feature-check">✓</div>
                <div class="feature-text">{{ $feature }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
