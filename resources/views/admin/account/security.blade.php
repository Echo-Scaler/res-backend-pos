@extends('admin.layouts.app')

@section('title', 'Account Governance & Security Hub')

@push('styles')
<style>
    /* ==========================================================================
       ENTERPRISE ACCOUNT SECURITY UI (MADA TYPOGRAPHY & ADAPTIVE THEME)
       ========================================================================== */
    .security-portal {
        font-family: "Mada", sans-serif;
        color: var(--text-main);
    }

    .security-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .security-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        color: var(--text-main);
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .security-title p {
        font-size: 0.875rem;
        font-weight: 400;
        color: var(--text-muted);
        margin: 0;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-backup-download {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.25rem;
        background: rgba(158, 198, 59, 0.12);
        color: #2e5902;
        border: 1px solid rgba(158, 198, 59, 0.4);
        font-size: 0.875rem;
        font-weight: 700;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    [data-theme="dark"] .btn-backup-download {
        color: var(--primary);
        background: rgba(158, 198, 59, 0.12);
        border-color: rgba(158, 198, 59, 0.35);
    }

    .btn-backup-download:hover {
        background: var(--primary);
        color: #0b0f17;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(158, 198, 59, 0.25);
    }

    /* KPI Summary Cards Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        border-color: rgba(158, 198, 59, 0.4);
    }

    .kpi-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
    }

    .kpi-sub {
        font-size: 0.75rem;
        font-weight: 400;
        color: var(--text-muted);
        margin-top: 0.35rem;
    }

    .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .kpi-icon.primary { background: rgba(158, 198, 59, 0.15); color: #4d7c0f; }
    [data-theme="dark"] .kpi-icon.primary { color: var(--primary); }
    .kpi-icon.warning { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .kpi-icon.info { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
    .kpi-icon.success { background: rgba(16, 185, 129, 0.15); color: #059669; }

    .text-configured { color: #4d7c0f !important; }
    [data-theme="dark"] .text-configured { color: var(--primary) !important; }
    .text-not-set { color: #d97706 !important; }
    .text-enabled { color: #059669 !important; }
    [data-theme="dark"] .text-enabled { color: #10b981 !important; }
    .text-disabled { color: #dc2626 !important; }

    /* Security Sections Layout */
    .security-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 1024px) {
        .security-layout {
            grid-template-columns: 1fr;
        }
    }

    .security-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .card-title-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
    }

    .card-title-box h3 {
        font-size: 1.0625rem;
        font-weight: 600;
        margin: 0;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.4rem;
    }

    /* Clean Adaptive Input Styling (Light & Dark Compatible) */
    .form-control {
        width: 100%;
        padding: 0.7rem 0.9rem;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        color: #0f172a;
        font-family: "Mada", sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        outline: none;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .form-control::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .form-control:focus {
        border-color: var(--primary);
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.2);
    }

    [data-theme="dark"] .form-control {
        background: #0f1623;
        border-color: #222d42;
        color: #f1f5f9;
        box-shadow: none;
    }

    [data-theme="dark"] .form-control::placeholder {
        color: #64748b;
    }

    [data-theme="dark"] .form-control:focus {
        background: #161e2e;
        border-color: var(--primary);
        color: #f1f5f9;
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.2);
    }

    /* Password Input with Visibility Toggle Button */
    .input-password-wrap {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-password-wrap .form-control {
        padding-right: 2.75rem;
    }

    .password-toggle-btn {
        position: absolute;
        right: 0.75rem;
        background: transparent;
        border: none;
        color: #64748b;
        font-size: 1.15rem;
        cursor: pointer;
        padding: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
    }

    .password-toggle-btn:hover {
        color: var(--text-main);
    }

    .btn-submit-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.7rem 1rem;
        background: var(--primary);
        color: #0b0f17;
        font-size: 0.875rem;
        font-weight: 700;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(158, 198, 59, 0.2);
    }

    .btn-submit-action:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(158, 198, 59, 0.3);
    }

    /* Sessions Table / List */
    .session-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    [data-theme="dark"] .session-item {
        background: rgba(15, 22, 35, 0.6);
        border-color: #222d42;
    }

    .session-item:hover {
        border-color: rgba(158, 198, 59, 0.4);
        background: var(--bg-hover);
    }

    .session-item.current {
        border-left: 3.5px solid var(--primary);
    }

    .session-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .session-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: rgba(158, 198, 59, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #4d7c0f;
    }

    [data-theme="dark"] .session-icon {
        background: rgba(255, 255, 255, 0.05);
        color: var(--primary);
    }

    .session-device {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .session-meta {
        font-size: 0.75rem;
        font-weight: 400;
        color: var(--text-muted);
        margin-top: 0.2rem;
    }

    .badge-current {
        background: rgba(158, 198, 59, 0.18);
        color: #2e5902;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 4px;
        text-transform: uppercase;
    }

    [data-theme="dark"] .badge-current {
        color: var(--primary);
        background: rgba(158, 198, 59, 0.15);
    }

    .btn-revoke {
        padding: 0.45rem 0.85rem;
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.3);
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-revoke:hover {
        background: #dc2626;
        color: #ffffff;
    }

    /* 2FA Card Styling */
    .two-factor-box {
        text-align: center;
        padding: 1.25rem 0;
    }

    .qr-container {
        display: inline-block;
        padding: 1rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin: 1rem 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .qr-container svg {
        display: block;
        width: 170px;
        height: 170px;
    }

    .secret-key-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: #f8fafc;
        border: 1.5px dashed var(--primary);
        color: #1e3a10;
        font-family: monospace;
        font-size: 1.05rem;
        letter-spacing: 0.15em;
        font-weight: 700;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    [data-theme="dark"] .secret-key-badge {
        background: #0f1623;
        color: var(--primary);
    }

    .recovery-code-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.625rem;
        background: var(--bg-body);
        padding: 0.85rem;
        border-radius: 8px;
        margin: 1rem 0;
        border: 1px solid var(--border-color);
    }

    [data-theme="dark"] .recovery-code-grid {
        background: #0f1623;
        border-color: #222d42;
    }

    .recovery-code-pill {
        font-family: monospace;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-main);
        padding: 0.45rem 0.65rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        text-align: center;
        letter-spacing: 0.05em;
    }

    [data-theme="dark"] .recovery-code-pill {
        background: rgba(255, 255, 255, 0.05);
        border-color: #222d42;
        color: #f1f5f9;
    }

    /* Security Log Table with High-Contrast Header (Light & Dark) */
    .audit-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
        text-align: left;
    }

    .audit-table th {
        padding: 0.875rem 1rem;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-color);
    }

    [data-theme="dark"] .audit-table th {
        background: #111724;
        color: #94a3b8;
        border-bottom-color: #222d42;
    }

    .audit-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
        vertical-align: middle;
    }

    [data-theme="dark"] .audit-table td {
        border-bottom-color: #222d42;
    }

    .audit-table tbody tr:hover {
        background: var(--bg-hover);
    }

    .severity-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
    }

    .severity-CRITICAL { background: rgba(239, 68, 68, 0.15); color: #dc2626; }
    .severity-WARNING { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .severity-INFO { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
</style>
@endpush

@section('content')
<div class="security-portal">
    {{-- Header with Title & Quick Backup Export --}}
    <div class="security-header">
        <div class="security-title">
            <h1><i class="ti ti-shield-lock" style="color: var(--primary);"></i> Account Governance & Security Hub</h1>
            <p>ထိန်းချုပ်သူ စကားဝှက်၊ POS ဘေလ်ဖျက် Master PIN၊ အဝေးမှ Terminal Logout ချခြင်းနှင့် 2FA လုံခြုံရေး စင်တာ</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.account.security.backup.download') }}" class="btn-backup-download" id="downloadBackupBtn">
                <i class="ti ti-download"></i> Download SQL Disaster Backup
            </a>
        </div>
    </div>

    {{-- System Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #065f46; border-radius: 8px;">
            <i class="ti ti-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #991b1b; border-radius: 8px;">
            <i class="ti ti-alert-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert" style="background: rgba(59, 130, 246, 0.15); border: 1px solid #3b82f6; color: #1e40af; border-radius: 8px;">
            <i class="ti ti-info-circle me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KPI Cards Row --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div>
                <div class="kpi-label">Master Password</div>
                <div class="kpi-value">Active</div>
                <div class="kpi-sub">
                    @if($user->password_changed_at)
                        ပြောင်းလဲခဲ့ချိန်: {{ $user->password_changed_at->diffForHumans() }}
                    @else
                        မူလသတ်မှတ်ထားသော စကားဝှက်
                    @endif
                </div>
            </div>
            <div class="kpi-icon primary"><i class="ti ti-key"></i></div>
        </div>

        <div class="kpi-card">
            <div>
                <div class="kpi-label">Counter Void/Discount PIN</div>
                <div class="kpi-value {{ $user->hasPinCode() ? 'text-configured' : 'text-not-set' }}">
                    {{ $user->hasPinCode() ? 'Configured' : 'Not Set' }}
                </div>
                <div class="kpi-sub">
                    {{ $user->hasPinCode() ? 'ဂဏန်း ၄-၆ လုံး အတည်ပြုပြီး' : 'ငွေအံဆွဲ/ဘေလ်ဖျက် PIN မသတ်မှတ်ရသေးပါ' }}
                </div>
            </div>
            <div class="kpi-icon warning"><i class="ti ti-lock-access"></i></div>
        </div>

        <div class="kpi-card">
            <div>
                <div class="kpi-label">Active POS Terminals</div>
                <div class="kpi-value">{{ count($sessions) }} Devices</div>
                <div class="kpi-sub">ဖွင့်လှစ်ထားသော စက်များ စောင့်ကြည့်မှု</div>
            </div>
            <div class="kpi-icon info"><i class="ti ti-devices"></i></div>
        </div>

        <div class="kpi-card">
            <div>
                <div class="kpi-label">Two-Factor Authentication</div>
                <div class="kpi-value {{ $user->hasTwoFactorEnabled() ? 'text-enabled' : 'text-disabled' }}">
                    {{ $user->hasTwoFactorEnabled() ? 'Enabled' : 'Disabled' }}
                </div>
                <div class="kpi-sub">
                    {{ $user->hasTwoFactorEnabled() ? 'Google Authenticator ဖြင့် ကာကွယ်ထားသည်' : 'လုံခြုံရေးအတွက် 2FA ဖွင့်ရန် အကြံပြုပါသည်' }}
                </div>
            </div>
            <div class="kpi-icon success"><i class="ti ti-shield-check"></i></div>
        </div>
    </div>

    {{-- Main Security Grid Layout --}}
    <div class="security-layout">
        {{-- Section 1: Change Master Password --}}
        <div class="security-card">
            <div class="card-title-box">
                <h3><i class="ti ti-key" style="color: var(--primary);"></i> Change Master Password</h3>
                <span class="badge" style="background: rgba(0, 0, 0, 0.05); color: var(--text-muted); font-size: 0.75rem;">Executive</span>
            </div>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                လုပ်ငန်းရှင် အကောင့်စကားဝှက်အား အနည်းဆုံး စာလုံး ၈ လုံး၊ စာလုံးကြီး/သေးနှင့် ဂဏန်းများ ရောနှော၍ ပြောင်းလဲပါ။
            </p>

            <form action="{{ route('admin.account.security.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="current_password">လက်ရှိ စကားဝှက် (Current Password) *</label>
                    <div class="input-password-wrap">
                        <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required placeholder="လက်ရှိ စကားဝှက် ရိုက်ထည့်ပါ">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('current_password', this)" title="Show/Hide Password">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <small class="text-danger mt-1 d-block" style="color: #dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">စကားဝှက်အသစ် (New Password) *</label>
                    <div class="input-password-wrap">
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="အနည်းဆုံး ၈ လုံး ပါဝင်ရမည်">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password', this)" title="Show/Hide Password">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <small class="text-danger mt-1 d-block" style="color: #dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">အတည်ပြု စကားဝှက် (Confirm Password) *</label>
                    <div class="input-password-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="စကားဝှက်အသစ် ပြန်ရိုက်ပါ">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password_confirmation', this)" title="Show/Hide Password">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit-action" id="submitPasswordBtn">
                    <i class="ti ti-device-floppy"></i> Update Master Password
                </button>
            </form>
        </div>

        {{-- Section 2: Set / Update Master POS Approval PIN --}}
        <div class="security-card">
            <div class="card-title-box">
                <h3><i class="ti ti-lock-access" style="color: #d97706;"></i> Counter Master Approval PIN</h3>
                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #d97706; font-size: 0.75rem;">Manager Approvals</span>
            </div>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                Cashier ကောင်တာတွင် ဘေလ်ဖျက်ခြင်း (Void Bill)၊ လျှော့စျေးပေးခြင်း (Discounts) နှင့် ငွေအံဆွဲဖွင့်ခြင်းတို့အတွက် ၄-၆ လုံး အတည်ပြု PIN ဖြစ်ပါသည်။
            </p>

            <form action="{{ route('admin.account.security.pin') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="pin_current_password">အတည်ပြုရန် လက်ရှိ စကားဝှက် (Current Password) *</label>
                    <div class="input-password-wrap">
                        <input type="password" id="pin_current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required placeholder="လက်ရှိ စကားဝှက် ရိုက်ထည့်ပါ">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('pin_current_password', this)" title="Show/Hide Password">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <small class="text-danger mt-1 d-block" style="color: #dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="pin_code">Master Approval PIN အသစ် (4-6 Digits) *</label>
                    <div class="input-password-wrap">
                        <input type="password" id="pin_code" name="pin_code" inputmode="numeric" maxlength="6" class="form-control @error('pin_code') is-invalid @enderror" required placeholder="ဥပမာ: 8492">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('pin_code', this)" title="Show/Hide PIN">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                    @error('pin_code')
                        <small class="text-danger mt-1 d-block" style="color: #dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="pin_code_confirmation">အတည်ပြု PIN (Confirm Master PIN) *</label>
                    <div class="input-password-wrap">
                        <input type="password" id="pin_code_confirmation" name="pin_code_confirmation" inputmode="numeric" maxlength="6" class="form-control" required placeholder="PIN နံပါတ် ပြန်ရိုက်ပါ">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('pin_code_confirmation', this)" title="Show/Hide PIN">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit-action" id="submitPinBtn" style="background: #f59e0b; color: #0b0f17;">
                    <i class="ti ti-shield-check"></i> Set / Update Master PIN
                </button>
            </form>
        </div>
    </div>

    {{-- Section 3 & 4 Layout --}}
    <div class="security-layout">
        {{-- Section 3: Active Browser & POS Sessions --}}
        <div class="security-card">
            <div class="card-title-box">
                <h3><i class="ti ti-devices" style="color: #2563eb;"></i> Active POS Terminals & Devices</h3>
                <form action="{{ route('admin.account.security.sessions.logout-others') }}" method="POST" onsubmit="return confirm('အခြား စက်များပေါ်ရှိ POS Session အားလုံးကို အဝေးမှ Logout ချမည်မှာ သေချာပါသလား?');">
                    @csrf
                    <button type="submit" class="btn-revoke" id="logoutOthersBtn">
                        <i class="ti ti-logout"></i> Log Out All Other Devices
                    </button>
                </form>
            </div>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                ကောင်တာ POS စက်များ သို့မဟုတ် ဝန်ထမ်း Tablet များတွင် Login မေ့ကျန်ခဲ့ပါက ဤနေရာမှ ချက်ချင်း အဝေးမှ Session ပိတ်သိမ်းနိုင်ပါသည်။
            </p>

            <div class="sessions-list">
                @forelse($sessions as $sessionItem)
                    <div class="session-item {{ $sessionItem['is_current'] ? 'current' : '' }}">
                        <div class="session-info">
                            <div class="session-icon">
                                <i class="ti {{ $sessionItem['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="session-device">
                                    {{ $sessionItem['device_type'] }}
                                    @if($sessionItem['is_current'])
                                        <span class="badge-current">You (Current)</span>
                                    @endif
                                </div>
                                <div class="session-meta">
                                    <span><i class="ti ti-world"></i> {{ $sessionItem['browser'] }}</span> • 
                                    <span><i class="ti ti-map-pin"></i> {{ $sessionItem['ip_address'] }}</span> • 
                                    <span><i class="ti ti-clock"></i> {{ $sessionItem['last_activity'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            @if(! $sessionItem['is_current'])
                                <form action="{{ route('admin.account.security.sessions.revoke', $sessionItem['id']) }}" method="POST" onsubmit="return confirm('ဤစက်အား Session ချက်ချင်း ပိတ်သိမ်းရန် သေချာပါသလား?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-revoke">
                                        <i class="ti ti-x"></i> Revoke
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 0.75rem; color: #4d7c0f; font-weight: 700;">
                                    <i class="ti ti-point-filled"></i> Online
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4" style="color: var(--text-muted);">
                        အခြား ဖွင့်လှစ်ထားသော စက်များ မရှိပါ။
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Section 4: Two-Factor Authentication (2FA) --}}
        <div class="security-card">
            <div class="card-title-box">
                <h3><i class="ti ti-shield-check" style="color: #059669;"></i> Two-Factor Authentication (2FA)</h3>
                @if($user->hasTwoFactorEnabled())
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; font-size: 0.75rem; font-weight: 700;">Active</span>
                @else
                    <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #dc2626; font-size: 0.75rem; font-weight: 700;">Disabled</span>
                @endif
            </div>

            @if($user->hasTwoFactorEnabled())
                <div class="two-factor-box">
                    <div style="font-size: 3rem; color: #059669; margin-bottom: 0.5rem;">
                        <i class="ti ti-shield-lock"></i>
                    </div>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">
                        Two-Factor Authentication is Enabled
                    </h4>
                    <p style="font-size: 0.8125rem; color: var(--text-muted); max-width: 420px; margin: 0 auto 1.5rem auto;">
                        သင့်အကောင့်သည် Google Authenticator / Authy အတည်ပြုချက်ဖြင့် လုံခြုံစွာ ကာကွယ်ထားပြီး ဖြစ်ပါသည်။ အတည်ပြုခဲ့သည့်အချိန်: {{ $user->two_factor_confirmed_at?->format('d M Y, h:i A') }}
                    </p>

                    <form action="{{ route('admin.account.security.2fa.disable') }}" method="POST" onsubmit="return confirm('2FA လုံခြုံရေးအား ပိတ်သိမ်းရန် သေချာပါသလား?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-revoke" style="padding: 0.6rem 1.25rem;">
                            <i class="ti ti-shield-x"></i> Disable Two-Factor Authentication
                        </button>
                    </form>
                </div>
            @elseif($staging2FaQrSvg)
                {{-- QR Code Setup View --}}
                <div class="two-factor-box">
                    <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.25rem;">
                        Scan QR Code with Google Authenticator
                    </h4>
                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        ဖုန်းရှိ Google Authenticator သို့မဟုတ် Authy App ဖြင့် အောက်ပါ QR Code ကို Scan ဖတ်ပါ။
                    </p>

                    <div class="qr-container">
                        {!! $staging2FaQrSvg !!}
                    </div>

                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 600;">Manual Setup Key:</div>
                        <div class="secret-key-badge">{{ $staging2FaSecret }}</div>
                    </div>

                    @if(!empty($recoveryCodes))
                        <div style="text-align: left; margin: 1.25rem 0;">
                            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.35rem;">
                                <i class="ti ti-lifebuoy"></i> Emergency Recovery Codes (ဖုန်းပျောက်ပါက အသုံးပြုရန်):
                            </div>
                            <div class="recovery-code-grid">
                                @foreach($recoveryCodes as $code)
                                    <div class="recovery-code-pill">{{ $code }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.account.security.2fa.confirm') }}" method="POST" style="max-width: 320px; margin: 0 auto;">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label text-start" for="code">Authenticator App မှ 6-Digit Code ရိုက်ထည့်ပါ *</label>
                            <input type="text" id="code" name="code" inputmode="numeric" maxlength="6" class="form-control text-center" style="font-size: 1.35rem; letter-spacing: 0.3em; font-weight: 700;" required placeholder="000000">
                        </div>
                        <button type="submit" class="btn-submit-action" id="confirm2FaBtn">
                            <i class="ti ti-check"></i> Verify & Activate 2FA
                        </button>
                    </form>
                </div>
            @else
                {{-- Prompt to Enable 2FA --}}
                <div class="two-factor-box">
                    <div style="font-size: 3rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                        <i class="ti ti-shield-off"></i>
                    </div>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">
                        Two-Factor Authentication is Disabled
                    </h4>
                    <p style="font-size: 0.8125rem; color: var(--text-muted); max-width: 420px; margin: 0 auto 1.5rem auto;">
                        ဆိုင်ပြင်ပ သို့မဟုတ် အိမ်မှနေ၍ စာရင်းစစ်ဆေးရာတွင် စကားဝှက် ပေါက်ကြားခဲ့လျှင်ပင် အကောင့်ဟက်ခံရမှု မရှိစေရန် 2FA ကို ဖွင့်လှစ်ထားသင့်ပါသည်။
                    </p>

                    <form action="{{ route('admin.account.security.2fa.enable') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-submit-action" id="enable2FaBtn" style="max-width: 260px; margin: 0 auto;">
                            <i class="ti ti-qrcode"></i> Setup Google Authenticator
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Section 5: Recent Security Audit Trail --}}
    <div class="security-card mt-4">
        <div class="card-title-box">
            <h3><i class="ti ti-history" style="color: var(--primary);"></i> Security Audit Trail & Access Logs</h3>
            <a href="{{ route('admin.audit.logs') }}" style="font-size: 0.8125rem; color: #4d7c0f; text-decoration: none; font-weight: 600;">
                View All Audit Logs <i class="ti ti-arrow-right"></i>
            </a>
        </div>
        <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1.25rem;">
            စကားဝှက် ပြောင်းလဲခြင်း၊ PIN သတ်မှတ်ခြင်းနှင့် Session ဖြတ်တောက်မှု မှတ်တမ်းများ (Immutable Logs)
        </p>

        <div class="table-responsive" style="border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Action</th>
                        <th>Severity</th>
                        <th>User / Role</th>
                        <th>IP Address</th>
                        <th>Note / Context</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($securityAuditLogs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td style="font-weight: 700; font-family: monospace;">{{ $log->action }}</td>
                            <td>
                                <span class="severity-badge severity-{{ $log->severity }}">
                                    {{ $log->severity }}
                                </span>
                            </td>
                            <td>{{ $log->user_name }} ({{ $log->role_name }})</td>
                            <td>{{ $log->metadata['ip'] ?? '127.0.0.1' }}</td>
                            <td>{{ $log->metadata['action_note'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color: var(--text-muted);">
                                လတ်တလော လုံခြုံရေး မှတ်တမ်းများ မရှိသေးပါ။
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.className = 'ti ti-eye-off';
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.className = 'ti ti-eye';
            }
        }
    }
</script>
@endpush
