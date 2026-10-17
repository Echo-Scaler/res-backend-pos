@extends('admin.layouts.app')

@section('title', 'Tax & Service Charge Settings')

@push('styles')
<style>
    /* ==========================================================================
       TAX & SERVICE CHARGE SETTINGS UI (MADA TYPOGRAPHY & OBSIDIAN SLATE THEME)
       ========================================================================== */
    .tax-portal {
        font-family: "Mada", sans-serif;
        color: var(--text-main);
    }

    .tax-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .tax-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        color: var(--text-main);
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .tax-title p {
        font-size: 0.875rem;
        font-weight: 400;
        color: var(--text-muted);
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .btn-save-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.4rem;
        background: var(--primary);
        color: #0b0f17;
        font-size: 0.875rem;
        font-weight: 700;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(158, 198, 59, 0.25);
    }

    .btn-save-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-save-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Tabs Navigation */
    .tax-tabs-nav {
        display: flex;
        gap: 0.5rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 1.75rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
    }

    .tab-btn {
        padding: 0.75rem 1.25rem;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .tab-btn:hover {
        color: var(--text-main);
    }

    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        font-weight: 700;
    }

    /* 2-Column Split Layout */
    .tax-split-layout {
        display: grid;
        grid-template-columns: 1fr 390px;
        gap: 2rem;
        align-items: flex-start;
    }

    @media (max-width: 1080px) {
        .tax-split-layout {
            grid-template-columns: 1fr;
        }
    }

    .settings-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.5rem;
    }

    .card-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0 0 1.25rem 0;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    @media (max-width: 640px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        margin-bottom: 1.25rem;
    }

    .form-group label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .form-group small {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .input-custom {
        width: 100%;
        padding: 0.65rem 0.85rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-main);
        font-family: "Mada", sans-serif;
        font-size: 0.875rem;
        font-weight: 500;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .input-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.15);
    }

    /* Mode Radio Card Selector */
    .mode-selector-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 0.25rem;
    }

    .mode-card-label {
        position: relative;
        display: flex;
        flex-direction: column;
        padding: 1rem;
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        background: var(--bg-body);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .mode-card-radio {
        position: absolute;
        opacity: 0;
    }

    .mode-card-radio:checked + .mode-card-label {
        border-color: var(--primary);
        background: rgba(158, 198, 59, 0.05);
    }

    .mode-card-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .mode-card-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        line-height: 1.35;
    }

    /* Toggle Card Row */
    .toggle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        margin-bottom: 0.75rem;
    }

    .toggle-info h4 {
        margin: 0 0 0.2rem 0;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .toggle-info p {
        margin: 0;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* iOS-Style Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }

    .toggle-checkbox {
        display: none;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #374151;
        transition: 0.3s;
        border-radius: 24px;
    }

    .toggle-slider::before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: #ffffff;
        transition: 0.3s;
        border-radius: 50%;
    }

    .toggle-checkbox:checked + .toggle-slider {
        background-color: var(--primary);
    }

    .toggle-checkbox:checked + .toggle-slider::before {
        transform: translateX(20px);
        background-color: #0b0f17;
    }

    /* Channels Checklist */
    .channels-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .channel-box-label {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.75rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
        transition: all 0.2s ease;
    }

    .channel-box-checkbox {
        accent-color: var(--primary);
        width: 16px;
        height: 16px;
    }

    /* 80mm Real Thermal Receipt Simulator Box */
    .receipt-preview-sticky {
        position: sticky;
        top: 90px;
    }

    .simulator-controls {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .sim-btn-group {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .sim-btn {
        flex: 1;
        padding: 0.45rem 0.6rem;
        border: 1px solid var(--border-color);
        background: var(--bg-body);
        color: var(--text-muted);
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }

    .receipt-hidden {
        display: none !important;
    }

    .sim-btn.active {
        background: var(--primary);
        color: #0b0f17;
        border-color: var(--primary);
        font-weight: 700;
    }

    .receipt-paper {
        background: #ffffff;
        color: #111827;
        font-family: 'Courier New', Courier, monospace;
        padding: 1.75rem 1.4rem;
        border-radius: 4px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        border-top: 4px solid var(--primary);
        font-size: 0.78rem;
        line-height: 1.45;
    }

    .receipt-center {
        text-align: center;
    }

    .receipt-store-title {
        font-size: 1.05rem;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 0.2rem;
    }

    .receipt-store-meta {
        font-size: 0.72rem;
        color: #4b5563;
        margin-bottom: 0.2rem;
    }

    .receipt-dashed {
        border-top: 1px dashed #9ca3af;
        margin: 0.65rem 0;
    }

    .receipt-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.25rem;
    }

    .receipt-row.bold {
        font-weight: 700;
    }

    .receipt-row.highlight {
        font-size: 0.95rem;
        font-weight: 900;
        margin-top: 0.4rem;
        padding-top: 0.4rem;
        border-top: 1px solid #111827;
    }

    .receipt-tax-stamp {
        border: 1.5px dashed #4b5563;
        border-radius: 6px;
        padding: 0.6rem;
        margin: 0.75rem 0;
        text-align: center;
        background: #f9fafb;
    }

    .receipt-tax-stamp .stamp-title {
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #1f2937;
    }

    .receipt-tax-stamp .stamp-id {
        font-size: 0.65rem;
        font-weight: 700;
        color: #4b5563;
    }

    .receipt-footer-text {
        font-size: 0.68rem;
        color: #6b7280;
        text-align: center;
        margin-top: 0.5rem;
        line-height: 1.35;
    }

    /* Notification Toast */
    .tax-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 0.85rem 1.25rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #ffffff;
        background: #10b981;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        display: none;
        align-items: center;
        gap: 0.6rem;
        z-index: 99999;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@endpush

@section('content')
<div class="tax-portal">
    <!-- Header with Title and Actions -->
    <div class="tax-header">
        <div class="tax-title">
            <h1>
                <i class="ti ti-receipt-tax" style="color: var(--primary);"></i>
                Tax & Service Charge Settings
            </h1>
            <p>မြန်မာ့အခွန်ဦးစီးဌာန ကုန်သွယ်လုပ်ငန်းခွန် (၅%)၊ စားသောက်ဆိုင် ဝန်ဆောင်ခ (၁၀%) နှင့် မြန်မာကျပ်ငွေ (MMK) Rounding စည်းမျဉ်းများ စီမံခန့်ခွဲခြင်း</p>
        </div>
        <div class="header-actions">
            <button type="submit" form="taxSettingsForm" class="btn-save-primary" id="saveTaxBtn">
                <i class="ti ti-check"></i>
                <span id="saveBtnText">Save Changes (သိမ်းဆည်းမည်)</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 0.9rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; gap: 0.6rem;">
            <i class="ti ti-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 0.9rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.875rem;">
            <div style="font-weight: 700; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="ti ti-alert-triangle"></i> အချက်အလက်များ ဖြည့်စွက်ရာတွင် အမှားအယွင်း ရှိနေပါသည်:
            </div>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="tax-tabs-nav">
        <button type="button" class="tab-btn active" data-tab="tab-tax">
            <i class="ti ti-receipt-tax"></i> ကုန်သွယ်လုပ်ငန်းခွန် (Commercial Tax)
        </button>
        <button type="button" class="tab-btn" data-tab="tab-service">
            <i class="ti ti-bell-ringing"></i> ဝန်ဆောင်ခ (Service Charge)
        </button>
        <button type="button" class="tab-btn" data-tab="tab-rounding">
            <i class="ti ti-coins"></i> ငွေကြေး Rounding စည်းမျဉ်း (MMK)
        </button>
        <button type="button" class="tab-btn" data-tab="tab-receipt">
            <i class="ti ti-printer"></i> ပြေစာစလစ် စည်းမျဉ်း (Thermal Receipt)
        </button>
    </div>

    <!-- 2-Column Split: Form (Left) & Live Simulator Receipt (Right) -->
    <div class="tax-split-layout">
        <!-- Left: Configuration Form -->
        <form id="taxSettingsForm" method="POST" action="{{ route('admin.settings.tax.update') }}">
            @csrf

            <!-- ==========================================
                 TAB 1: COMMERCIAL TAX ENGINE
                 ========================================== -->
            <div class="tab-pane active" id="tab-tax">
                <div class="settings-card">
                    <h3 class="card-section-title">
                        <i class="ti ti-building-bank"></i>
                        ကုန်သွယ်လုပ်ငန်းခွန် သတ်မှတ်ချက်များ (Commercial Tax Engine)
                    </h3>

                    <!-- Enable Tax Switch -->
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <h4>Enable Commercial Tax (ကုန်သွယ်လုပ်ငန်းခွန် ကောက်ခံမှု)</h4>
                            <p>ပိတ်ထားပါက အော်ဒါအားလုံးတွင် အခွန် ၀ ကျပ် အဖြစ် တွက်ချက်မည်ဖြစ်ပါသည်။</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_tax_enabled" id="is_tax_enabled" class="toggle-checkbox live-trigger" value="1" {{ old('is_tax_enabled', $restaurant->is_tax_enabled ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="tax_name">Tax Label on Bill (အခွန်အမည်) *</label>
                            <input type="text" name="tax_name" id="tax_name" class="input-custom live-trigger" value="{{ old('tax_name', $restaurant->tax_name ?? 'Commercial Tax') }}" required>
                            <small>ပြေစာစလစ်ပေါ်တွင် ဖော်ပြမည့် အမည် (ဥပမာ- Commercial Tax, ကုန်သွယ်ခွန်)</small>
                        </div>

                        <div class="form-group">
                            <label for="default_tax_percent">Tax Percentage (အခွန် ရာခိုင်နှုန်း %) *</label>
                            <input type="number" step="0.1" min="0" max="30" name="default_tax_percent" id="default_tax_percent" class="input-custom live-trigger" value="{{ old('default_tax_percent', $restaurant->default_tax_percent ?? 5.0) }}" required>
                            <small>မြန်မာနိုင်ငံ IRD စံနှုန်းအရ 5.0% ဖြစ်ပါသည်။</small>
                        </div>
                    </div>

                    <!-- Tax Calculation Mode (Inclusive vs Exclusive) -->
                    <div class="form-group" style="margin-top: 0.5rem;">
                        <label>Tax Calculation Mode (အခွန်တွက်ချက်မှု ပုံစံ) *</label>
                        <div class="mode-selector-grid">
                            <div>
                                <input type="radio" name="tax_calculation_mode" id="mode_inclusive" class="mode-card-radio live-trigger" value="inclusive" {{ old('tax_calculation_mode', $restaurant->tax_calculation_mode ?? 'inclusive') === 'inclusive' ? 'checked' : '' }}>
                                <label for="mode_inclusive" class="mode-card-label">
                                    <span class="mode-card-title"><i class="ti ti-check-circle"></i> Inclusive (အခွန်ပါပြီး)</span>
                                    <span class="mode-card-desc">မီနူးစျေးနှုန်းထဲတွင် အခွန် ၅% ပါဝင်ပြီးသားဖြစ်ပြီး ဧည့်သည်သည် မီနူးစျေးနှုန်းအတိုင်း အတိအကျ ပေးချေရပါမည်။ (Recommended)</span>
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="tax_calculation_mode" id="mode_exclusive" class="mode-card-radio live-trigger" value="exclusive" {{ old('tax_calculation_mode', $restaurant->tax_calculation_mode ?? 'inclusive') === 'exclusive' ? 'checked' : '' }}>
                                <label for="mode_exclusive" class="mode-card-label">
                                    <span class="mode-card-title"><i class="ti ti-plus"></i> Exclusive (အပေါ်မှထပ်ပေါင်း)</span>
                                    <span class="mode-card-desc">မီနူးစျေးနှုန်းတွင် အခွန်မပါဝင်ဘဲ ငွေရှင်းချိန် စုစုပေါင်း အော်ဒါတန်ဖိုးအပေါ်တွင် ၅% အခွန် ထပ်ပေါင်းစည်းကြပ်ပါမည်။</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 1rem;">
                        <div class="form-group">
                            <label for="tax_id">TIN Number (အခွန်မှတ်ပုံတင်အမှတ်)</label>
                            <input type="text" name="tax_id" id="tax_id" class="input-custom live-trigger" value="{{ old('tax_id', $restaurant->tax_id) }}" placeholder="ဥပမာ- TIN-984512-YGN">
                            <small>မြန်မာ့အခွန်ဦးစီးဌာနမှ ထုတ်ပေးထားသော Taxpayer ID</small>
                        </div>

                        <div class="form-group">
                            <label for="tax_office_name">Tax Revenue Office (အခွန်ရုံးအမည်)</label>
                            <input type="text" name="tax_office_name" id="tax_office_name" class="input-custom live-trigger" value="{{ old('tax_office_name', $restaurant->tax_office_name) }}" placeholder="ဥပမာ- ကမာရွတ်မြို့နယ် အခွန်ဦးစီးဌာနမှူးရုံး">
                            <small>တရားဝင် စာရွက်စာတမ်းနှင့် ပြေစာတွင် ပြသမည့် အခွန်ရုံး</small>
                        </div>
                    </div>

                    <!-- Applicable Channels -->
                    <div class="form-group" style="margin-top: 0.5rem;">
                        <label>Tax Applicability Across Channels (အခွန်စည်းကြပ်မည့် လမ်းကြောင်းများ)</label>
                        <div class="channels-grid">
                            <label class="channel-box-label">
                                <input type="checkbox" name="apply_tax_dine_in" class="channel-box-checkbox live-trigger" value="1" {{ old('apply_tax_dine_in', $restaurant->apply_tax_dine_in ?? true) ? 'checked' : '' }}>
                                <span>🍽️ Dine-In (ထိုင်စား)</span>
                            </label>
                            <label class="channel-box-label">
                                <input type="checkbox" name="apply_tax_takeaway" class="channel-box-checkbox live-trigger" value="1" {{ old('apply_tax_takeaway', $restaurant->apply_tax_takeaway ?? true) ? 'checked' : '' }}>
                                <span>🥡 Takeaway (ပါဆယ်)</span>
                            </label>
                            <label class="channel-box-label">
                                <input type="checkbox" name="apply_tax_delivery" class="channel-box-checkbox live-trigger" value="1" {{ old('apply_tax_delivery', $restaurant->apply_tax_delivery ?? true) ? 'checked' : '' }}>
                                <span>🛵 Delivery (ပို့ဆောင်မှု)</span>
                            </label>
                            <label class="channel-box-label">
                                <input type="checkbox" name="apply_tax_qr_order" class="channel-box-checkbox live-trigger" value="1" {{ old('apply_tax_qr_order', $restaurant->apply_tax_qr_order ?? true) ? 'checked' : '' }}>
                                <span>📱 QR Table Order (စားပွဲခုံ QR)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 2: DINING SERVICE CHARGE ENGINE
                 ========================================== -->
            <div class="tab-pane" id="tab-service" style="display: none;">
                <div class="settings-card">
                    <h3 class="card-section-title">
                        <i class="ti ti-bell-ringing"></i>
                        စားသောက်ဆိုင် ဝန်ဆောင်ခ သတ်မှတ်ချက်များ (Service Charge Engine)
                    </h3>

                    <!-- Enable Service Charge Switch -->
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <h4>Enable Dining Service Charge (ဝန်ဆောင်ခ ကောက်ခံမှု)</h4>
                            <p>စားသောက်ဆိုင် ဧည့်သည်များအတွက် ဝန်ဆောင်ခ ရာခိုင်နှုန်း စည်းကြပ်ခြင်း</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_service_charge_enabled" id="is_service_charge_enabled" class="toggle-checkbox live-trigger" value="1" {{ old('is_service_charge_enabled', $restaurant->is_service_charge_enabled ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="service_charge_name">Service Charge Label (ဝန်ဆောင်ခ အမည်) *</label>
                            <input type="text" name="service_charge_name" id="service_charge_name" class="input-custom live-trigger" value="{{ old('service_charge_name', $restaurant->service_charge_name ?? 'Service Charge') }}" required>
                            <small>ပြေစာစလစ်ပေါ်တွင် ဖော်ပြမည့် အမည်</small>
                        </div>

                        <div class="form-group">
                            <label for="default_service_charge_percent">Service Charge Rate (% ရာခိုင်နှုန်း) *</label>
                            <input type="number" step="0.5" min="0" max="25" name="default_service_charge_percent" id="default_service_charge_percent" class="input-custom live-trigger" value="{{ old('default_service_charge_percent', $restaurant->default_service_charge_percent ?? 10.0) }}" required>
                            <small>များသောအားဖြင့် 5.0% သို့မဟုတ် 10.0% သတ်မှတ်ကြပါသည်။</small>
                        </div>
                    </div>

                    <!-- Dine-In Only Exemption Toggle -->
                    <div class="toggle-row" style="margin-top: 0.5rem;">
                        <div class="toggle-info">
                            <h4>Dine-In Only (ပါဆယ်နှင့် ပို့ဆောင်မှု ကင်းလွတ်ခွင့်)</h4>
                            <p>ထိုင်စား ဧည့်သည်များထံမှသာ ဝန်ဆောင်ခ ကောက်ခံပြီး Takeaway နှင့် Delivery အော်ဒါများတွင် ဝန်ဆောင်ခ အလိုအလျောက် ပယ်ဖျက်ပေးခြင်း</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="service_charge_dine_in_only" id="service_charge_dine_in_only" class="toggle-checkbox live-trigger" value="1" {{ old('service_charge_dine_in_only', $restaurant->service_charge_dine_in_only ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Compound Tax Toggle -->
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <h4>Taxable Service Charge (ဝန်ဆောင်ခအပေါ် အခွန်ထပ်ပေါင်းခြင်း)</h4>
                            <p>ကုန်သွယ်လုပ်ငန်းခွန် ၅% အား (အစားအသောက် + ဝန်ဆောင်ခ) စုစုပေါင်းအပေါ် စည်းကြပ်မည်လား (Compound Tax)</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_service_charge_taxable" id="is_service_charge_taxable" class="toggle-checkbox live-trigger" value="1" {{ old('is_service_charge_taxable', $restaurant->is_service_charge_taxable ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Takeaway Packaging Fee -->
                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="takeaway_packaging_fee">Takeaway Packaging Fee (ပါဆယ်ဘူးခ MMK) *</label>
                        <input type="number" step="50" min="0" max="100000" name="takeaway_packaging_fee" id="takeaway_packaging_fee" class="input-custom live-trigger" value="{{ old('takeaway_packaging_fee', $restaurant->takeaway_packaging_fee ?? 0) }}" required>
                        <small>ပါဆယ် (Takeaway) မှာယူသည့်အခါ အော်ဒါတစ်ခုလျှင် ထပ်မံကောက်ခံမည့် သီးသန့် ဘူးခ (မြန်မာကျပ်ငွေ)</small>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 3: MMK CASH ROUNDING RULES
                 ========================================== -->
            <div class="tab-pane" id="tab-rounding" style="display: none;">
                <div class="settings-card">
                    <h3 class="card-section-title">
                        <i class="ti ti-coins"></i>
                        မြန်မာကျပ်ငွေ (MMK) Rounding စည်းမျဉ်းများ (Rule 6 Compliance)
                    </h3>

                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                        မြန်မာနိုင်ငံတွင် ၁၀/၂၀ ကျပ်တန် အကြွေစက္ကူများ ရှားပါးသဖြင့် ဘေလ်ရှင်းချိန်တွင် အကြွေပြန်အမ်းရန် အဆင်ပြေစေရန်အတွက် Rounding စနစ်ကို ထည့်သွင်းထားပါသည်။
                    </p>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="rounding_method">Rounding Unit (ညှိနှိုင်းမည့် အကြွေယူနစ်) *</label>
                            <select name="rounding_method" id="rounding_method" class="input-custom live-trigger">
                                <option value="round_50" {{ old('rounding_method', $restaurant->rounding_method ?? 'round_50') === 'round_50' ? 'selected' : '' }}>Nearest 50 MMK (အနီးစပ်ဆုံး ၅၀ ကျပ်) - Recommended</option>
                                <option value="round_100" {{ old('rounding_method', $restaurant->rounding_method ?? 'round_50') === 'round_100' ? 'selected' : '' }}>Nearest 100 MMK (အနီးစပ်ဆုံး ၁၀၀ ကျပ်)</option>
                                <option value="none" {{ old('rounding_method', $restaurant->rounding_method ?? 'round_50') === 'none' ? 'selected' : '' }}>Exact Integer (တိကျသော မြန်မာကျပ်ငွေ)</option>
                            </select>
                            <small>ငွေရှင်းရာတွင် အကြွေအခက်အခဲ မရှိစေရန် ၅၀ သို့မဟုတ် ၁၀၀ ကျပ် အတိအကျ ညှိနှိုင်းပေးခြင်း</small>
                        </div>

                        <div class="form-group">
                            <label for="rounding_direction">Rounding Direction (ညှိနှိုင်းမည့် ဦးတည်ချက်) *</label>
                            <select name="rounding_direction" id="rounding_direction" class="input-custom live-trigger">
                                <option value="nearest" {{ old('rounding_direction', $restaurant->rounding_direction ?? 'nearest') === 'nearest' ? 'selected' : '' }}>Round to Nearest (အနီးစပ်ဆုံး ညှိရန်)</option>
                                <option value="ceil" {{ old('rounding_direction', $restaurant->rounding_direction ?? 'nearest') === 'ceil' ? 'selected' : '' }}>Round Up (အမြဲတမ်း အထက်သို့ တင်ရန်)</option>
                                <option value="floor" {{ old('rounding_direction', $restaurant->rounding_direction ?? 'nearest') === 'floor' ? 'selected' : '' }}>Round Down (အမြဲတမ်း ကျပ်စွန်းကို ပယ်ဖျက်ရန် - Customer Friendly)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Round Cash Only Toggle -->
                    <div class="toggle-row" style="margin-top: 0.5rem;">
                        <div class="toggle-info">
                            <h4>Round Cash Payments Only (ငွေသားရှင်းချိန်သာ Rounding သုံးရန်)</h4>
                            <p>ဖွင့်ထားပါက ငွေသားဖြင့် ရှင်းချိန်တွင်သာ ၅၀/၁၀၀ ကျပ်သို့ ညှိပေးမည်ဖြစ်ပြီး KBZPay, WavePay, Card ဖြင့် ရှင်းပါက အတိအကျ ကောက်ခံပါမည်။</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="round_cash_only" id="round_cash_only" class="toggle-checkbox live-trigger" value="1" {{ old('round_cash_only', $restaurant->round_cash_only ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 4: THERMAL RECEIPT PRESENTATION
                 ========================================== -->
            <div class="tab-pane" id="tab-receipt" style="display: none;">
                <div class="settings-card">
                    <h3 class="card-section-title">
                        <i class="ti ti-printer"></i>
                        ၈၀ မီလီမီတာ အပူခံပြေစာစလစ် အခွန်ပြသမှု (Thermal Receipt Presentation)
                    </h3>

                    <!-- Show Tax Breakdown Line -->
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <h4>Show Commercial Tax Breakdown (အခွန်ပမာဏ သီးခြားစာကြောင်း ပြသရန်)</h4>
                            <p>ပြေစာပေါ်တွင် အခွန်မည်မျှ ကျသင့်သည်ကို စာကြောင်းဖြင့် ထင်ရှားစွာ ဖော်ပြခြင်း</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="show_tax_breakdown_on_receipt" id="show_tax_breakdown_on_receipt" class="toggle-checkbox live-trigger" value="1" {{ old('show_tax_breakdown_on_receipt', $restaurant->show_tax_breakdown_on_receipt ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Show Service Charge Breakdown -->
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <h4>Show Service Charge Breakdown (ဝန်ဆောင်ခ သီးခြားစာကြောင်း ပြသရန်)</h4>
                            <p>ပြေစာပေါ်တွင် ဝန်ဆောင်ခ မည်မျှ ကောက်ခံသည်ကို စာကြောင်းဖြင့် ဖော်ပြခြင်း</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="show_service_charge_on_receipt" id="show_service_charge_on_receipt" class="toggle-checkbox live-trigger" value="1" {{ old('show_service_charge_on_receipt', $restaurant->show_service_charge_on_receipt ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Print Tax Stamp Seal -->
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <h4>Print Official Tax Stamp Seal (တရားဝင် အခွန်တံဆိပ်ခေါင်း ဘောင် ရိုက်နှိပ်ရန်)</h4>
                            <p>မြန်မာ့အခွန်ဦးစီးဌာန စံပြု အခွန်တံဆိပ်ခေါင်း အမှတ်အသားအား ပြေစာအောက်ခြေတွင် ထည့်သွင်းရိုက်နှိပ်ခြင်း</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="print_tax_stamp_seal" id="print_tax_stamp_seal" class="toggle-checkbox live-trigger" value="1" {{ old('print_tax_stamp_seal', $restaurant->print_tax_stamp_seal ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Disclaimer Text -->
                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="tax_disclaimer_text">Tax & Service Disclaimer Notice (ပြေစာအောက်ခြေ အသိပေးစာသား)</label>
                        <textarea name="tax_disclaimer_text" id="tax_disclaimer_text" rows="3" class="input-custom live-trigger" placeholder="Prices are inclusive of 5% Commercial Tax and 10% Service Charge as per Myanmar Government Regulations.">{{ old('tax_disclaimer_text', $restaurant->tax_disclaimer_text) }}</textarea>
                        <small>ပြေစာစလစ်၏ အောက်ဆုံးတွင် ရိုက်နှိပ်ဖော်ပြမည့် အသိပေးစာသား</small>
                    </div>
                </div>
            </div>
        </form>

        <!-- Right: Sticky Live Dynamic Bill Simulator (80mm Thermal Receipt) -->
        <div class="receipt-preview-sticky">
            <div class="simulator-controls">
                <div style="font-size: 0.8125rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.25rem; display: flex; align-items: center; justify-content: space-between;">
                    <span><i class="ti ti-device-analytics" style="color: var(--primary);"></i> Live Bill Simulator</span>
                    <span style="font-size: 0.7rem; color: var(--primary); font-weight: 600;">Reactive (MMK)</span>
                </div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.6rem;">
                    ဘယ်ဘက်ရှိ ဆက်တင်များကို ပြောင်းလဲကြည့်ရှုပါက ညာဘက်ရှိ ဘေလ်စလစ်တွင် ချက်ချင်း အလိုအလျောက် တွက်ချက်ပြသပေးပါမည်။
                </div>

                <!-- Simulation Mode Switcher -->
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;">Channel Preview:</div>
                <div class="sim-btn-group">
                    <button type="button" class="sim-btn active" id="simDineInBtn" onclick="switchSimChannel('dine_in')">🍽️ Dine-In</button>
                    <button type="button" class="sim-btn" id="simTakeawayBtn" onclick="switchSimChannel('takeaway')">🥡 Takeaway</button>
                </div>

                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-main); margin: 0.6rem 0 0.25rem 0;">Payment Method:</div>
                <div class="sim-btn-group">
                    <button type="button" class="sim-btn active" id="simCashBtn" onclick="switchSimPayment('cash')">💵 Cash</button>
                    <button type="button" class="sim-btn" id="simDigitalBtn" onclick="switchSimPayment('digital')">📱 KBZPay / Wave</button>
                </div>
            </div>

            <!-- 80mm Real Thermal Paper Preview -->
            <div class="receipt-paper">
                <div class="receipt-center">
                    <div class="receipt-store-title" id="receiptStoreName">{{ $restaurant->name ?? 'RESTAURANT POS' }}</div>
                    <div class="receipt-store-meta" id="receiptStoreAddress">{{ $restaurant->address ?? 'Yangon, Myanmar' }}</div>
                    <div class="receipt-store-meta {{ empty($restaurant->tax_id) ? 'receipt-hidden' : '' }}" id="receiptTinLine">
                        TIN: <span id="receiptTinText">{{ $restaurant->tax_id ?? 'TIN-000000-MM' }}</span>
                    </div>
                </div>

                <div class="receipt-dashed"></div>

                <div class="receipt-row" style="font-size: 0.72rem; color: #4b5563;">
                    <span>Order: #ORD-9821</span>
                    <span id="receiptChannelBadge">TABLE: VIP-04</span>
                </div>
                <div class="receipt-row" style="font-size: 0.72rem; color: #4b5563;">
                    <span>Cashier: Admin (POS 1)</span>
                    <span>{{ date('d/m/Y H:i') }}</span>
                </div>

                <div class="receipt-dashed"></div>

                <!-- Sample Order Items -->
                <div class="receipt-row bold">
                    <span>ITEMS</span>
                    <span>AMOUNT (MMK)</span>
                </div>
                <div class="receipt-row" style="margin-top: 0.3rem;">
                    <span>1x Myanmar Chicken Curry</span>
                    <span>12,000</span>
                </div>
                <div class="receipt-row">
                    <span>1x Coconut Rice (Large)</span>
                    <span>4,500</span>
                </div>
                <div class="receipt-row">
                    <span>2x Fresh Lime Juice</span>
                    <span>5,000</span>
                </div>

                <div class="receipt-dashed"></div>

                <!-- Calculations Breakdown -->
                <div class="receipt-row">
                    <span>Subtotal:</span>
                    <span id="previewSubtotal">21,500 MMK</span>
                </div>

                <div class="receipt-row" id="rowPackagingFee" style="display: none;">
                    <span>Packaging Fee:</span>
                    <span id="previewPackagingFee">+0 MMK</span>
                </div>

                <div class="receipt-row" id="rowServiceCharge">
                    <span id="previewServiceChargeLabel">Service Charge (10%):</span>
                    <span id="previewServiceCharge">+2,150 MMK</span>
                </div>

                <div class="receipt-row" id="rowTax">
                    <span id="previewTaxLabel">Commercial Tax (5%):</span>
                    <span id="previewTax">+1,183 MMK</span>
                </div>

                <div class="receipt-row" id="rowRounding" style="color: #6b7280; font-size: 0.72rem;">
                    <span id="previewRoundingLabel">Rounding (Nearest 50):</span>
                    <span id="previewRounding">-33 MMK</span>
                </div>

                <div class="receipt-dashed"></div>

                <!-- Final Total Payable (Rule 6: MMK) -->
                <div class="receipt-row highlight">
                    <span>TOTAL PAYABLE:</span>
                    <span id="previewFinalTotal">24,800 MMK</span>
                </div>

                <!-- Tax Note / Inclusive Breakdown Notice -->
                <div id="inclusiveNotice" style="font-size: 0.68rem; color: #4b5563; margin-top: 0.35rem; font-style: italic; text-align: center;">
                    * All menu prices are inclusive of 5% Commercial Tax.
                </div>

                <!-- Tax Stamp Seal Placeholder -->
                <div class="receipt-tax-stamp" id="receiptStampBox">
                    <div class="stamp-title">🏛️ INTERNAL REVENUE DEPARTMENT</div>
                    <div class="stamp-id">OFFICIAL COMMERCIAL TAX STAMP</div>
                    <div style="font-size: 0.62rem; color: #6b7280; margin-top: 0.2rem;">
                        TAX CODE: <span id="stampTinCode">{{ $restaurant->tax_id ?? 'TIN-NOT-REGISTERED' }}</span>
                    </div>
                </div>

                <!-- Disclaimer Text -->
                <div class="receipt-footer-text" id="previewDisclaimer">
                    {{ $restaurant->tax_disclaimer_text ?? 'Thank you for dining with us! Please come again.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="tax-toast" id="taxToast">
        <i class="ti ti-circle-check" style="font-size: 1.25rem;"></i>
        <span id="toastMessage">Success</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab Switching Logic
    document.querySelectorAll('.tax-tabs-nav .tab-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.tax-tabs-nav .tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.style.display = 'none');

            button.classList.add('active');
            const targetPane = document.getElementById(button.getAttribute('data-tab'));
            if (targetPane) {
                targetPane.style.display = 'block';
            }
        });
    });

    // Simulator State
    let currentChannel = 'dine_in';
    let currentPayment = 'cash';
    const sampleSubtotal = 21500; // 12000 + 4500 + 5000 MMK

    function switchSimChannel(channel) {
        currentChannel = channel;
        document.getElementById('simDineInBtn').classList.toggle('active', channel === 'dine_in');
        document.getElementById('simTakeawayBtn').classList.toggle('active', channel === 'takeaway');
        document.getElementById('receiptChannelBadge').textContent = channel === 'dine_in' ? 'TABLE: VIP-04' : 'TAKEAWAY #08';
        updateSimulation();
    }

    function switchSimPayment(payment) {
        currentPayment = payment;
        document.getElementById('simCashBtn').classList.toggle('active', payment === 'cash');
        document.getElementById('simDigitalBtn').classList.toggle('active', payment === 'digital');
        updateSimulation();
    }

    // Reactive Simulation Calculation Engine (Zero-Latency Client Computation)
    function updateSimulation() {
        const isTaxEnabled = document.getElementById('is_tax_enabled')?.checked ?? true;
        const taxRate = parseFloat(document.getElementById('default_tax_percent')?.value) || 0;
        const taxName = document.getElementById('tax_name')?.value || 'Commercial Tax';
        const taxMode = document.querySelector('input[name="tax_calculation_mode"]:checked')?.value || 'inclusive';
        const taxId = document.getElementById('tax_id')?.value || '';

        const isServiceEnabled = document.getElementById('is_service_charge_enabled')?.checked ?? true;
        const serviceRate = parseFloat(document.getElementById('default_service_charge_percent')?.value) || 0;
        const serviceName = document.getElementById('service_charge_name')?.value || 'Service Charge';
        const serviceDineInOnly = document.getElementById('service_charge_dine_in_only')?.checked ?? true;
        const isServiceTaxable = document.getElementById('is_service_charge_taxable')?.checked ?? false;
        const packagingFee = parseInt(document.getElementById('takeaway_packaging_fee')?.value) || 0;

        const roundingMethod = document.getElementById('rounding_method')?.value || 'round_50';
        const roundingDirection = document.getElementById('rounding_direction')?.value || 'nearest';
        const roundCashOnly = document.getElementById('round_cash_only')?.checked ?? true;

        const showTaxBreakdown = document.getElementById('show_tax_breakdown_on_receipt')?.checked ?? true;
        const showServiceBreakdown = document.getElementById('show_service_charge_on_receipt')?.checked ?? true;
        const printTaxStamp = document.getElementById('print_tax_stamp_seal')?.checked ?? true;
        const disclaimer = document.getElementById('tax_disclaimer_text')?.value || 'Thank you for dining with us! Please come again.';

        // 1. Service Charge Calculation
        let serviceAmount = 0;
        let appliesService = false;
        if (isServiceEnabled) {
            if (serviceDineInOnly) {
                appliesService = (currentChannel === 'dine_in');
            } else {
                appliesService = true;
            }

            if (appliesService && serviceRate > 0) {
                serviceAmount = Math.round(sampleSubtotal * (serviceRate / 100));
            }
        }

        // 2. Packaging Fee
        let currentPackaging = 0;
        if (currentChannel === 'takeaway') {
            currentPackaging = packagingFee;
        }

        // 3. Tax Calculation
        let taxAmount = 0;
        let preRoundingTotal = sampleSubtotal;

        if (isTaxEnabled && taxRate > 0) {
            let taxableBase = sampleSubtotal;
            if (isServiceTaxable && serviceAmount > 0) {
                taxableBase += serviceAmount;
            }

            if (taxMode === 'inclusive') {
                const baseWithoutTax = Math.round(taxableBase / (1 + (taxRate / 100)));
                taxAmount = taxableBase - baseWithoutTax;
                preRoundingTotal = sampleSubtotal + serviceAmount + currentPackaging;
            } else {
                taxAmount = Math.round(taxableBase * (taxRate / 100));
                preRoundingTotal = sampleSubtotal + serviceAmount + taxAmount + currentPackaging;
            }
        } else {
            preRoundingTotal = sampleSubtotal + serviceAmount + currentPackaging;
        }

        // 4. Cash Rounding
        let shouldRound = true;
        if (roundCashOnly && currentPayment !== 'cash') {
            shouldRound = false;
        }

        let roundedTotal = preRoundingTotal;
        let roundingDiff = 0;

        if (shouldRound && roundingMethod !== 'none') {
            const unit = (roundingMethod === 'round_100') ? 100 : 50;
            if (roundingDirection === 'ceil') {
                roundedTotal = Math.ceil(preRoundingTotal / unit) * unit;
            } else if (roundingDirection === 'floor') {
                roundedTotal = Math.floor(preRoundingTotal / unit) * unit;
            } else {
                roundedTotal = Math.round(preRoundingTotal / unit) * unit;
            }
            roundingDiff = roundedTotal - preRoundingTotal;
        }

        // Update DOM elements
        document.getElementById('previewSubtotal').textContent = sampleSubtotal.toLocaleString() + ' MMK';

        // Packaging Fee Row
        const rowPackaging = document.getElementById('rowPackagingFee');
        if (currentPackaging > 0) {
            rowPackaging.style.display = 'flex';
            document.getElementById('previewPackagingFee').textContent = '+' + currentPackaging.toLocaleString() + ' MMK';
        } else {
            rowPackaging.style.display = 'none';
        }

        // Service Charge Row
        const rowService = document.getElementById('rowServiceCharge');
        if (showServiceBreakdown && serviceAmount > 0) {
            rowService.style.display = 'flex';
            document.getElementById('previewServiceChargeLabel').textContent = `${serviceName} (${serviceRate}%):`;
            document.getElementById('previewServiceCharge').textContent = '+' + serviceAmount.toLocaleString() + ' MMK';
        } else {
            rowService.style.display = 'none';
        }

        // Tax Row
        const rowTax = document.getElementById('rowTax');
        if (showTaxBreakdown && taxAmount > 0) {
            rowTax.style.display = 'flex';
            const modeSuffix = (taxMode === 'inclusive') ? ' (Incl.)' : '';
            document.getElementById('previewTaxLabel').textContent = `${taxName} (${taxRate}%)${modeSuffix}:`;
            document.getElementById('previewTax').textContent = (taxMode === 'inclusive' ? '' : '+') + taxAmount.toLocaleString() + ' MMK';
        } else {
            rowTax.style.display = 'none';
        }

        // Rounding Row
        const rowRounding = document.getElementById('rowRounding');
        if (roundingDiff !== 0) {
            rowRounding.style.display = 'flex';
            const unitLabel = (roundingMethod === 'round_100') ? '100' : '50';
            document.getElementById('previewRoundingLabel').textContent = `Rounding (${unitLabel} MMK):`;
            document.getElementById('previewRounding').textContent = (roundingDiff > 0 ? '+' : '') + roundingDiff.toLocaleString() + ' MMK';
        } else {
            rowRounding.style.display = 'none';
        }

        // Final Total
        document.getElementById('previewFinalTotal').textContent = roundedTotal.toLocaleString() + ' MMK';

        // Inclusive Notice
        const incNotice = document.getElementById('inclusiveNotice');
        if (isTaxEnabled && taxMode === 'inclusive') {
            incNotice.style.display = 'block';
            incNotice.textContent = `* All menu prices are inclusive of ${taxRate}% ${taxName}.`;
        } else {
            incNotice.style.display = 'none';
        }

        // TIN line
        const tinLine = document.getElementById('receiptTinLine');
        if (taxId.trim() !== '') {
            tinLine.style.display = 'block';
            document.getElementById('receiptTinText').textContent = taxId;
            document.getElementById('stampTinCode').textContent = taxId;
        } else {
            tinLine.style.display = 'none';
            document.getElementById('stampTinCode').textContent = 'TIN-NOT-REGISTERED';
        }

        // Stamp Box
        document.getElementById('receiptStampBox').style.display = printTaxStamp ? 'block' : 'none';

        // Disclaimer
        document.getElementById('previewDisclaimer').textContent = disclaimer;
    }

    // Attach event listeners to all triggers
    document.querySelectorAll('.live-trigger').forEach(el => {
        el.addEventListener('input', updateSimulation);
        el.addEventListener('change', updateSimulation);
    });

    // Form AJAX Submission
    const form = document.getElementById('taxSettingsForm');
    const saveBtn = document.getElementById('saveTaxBtn');
    const saveBtnText = document.getElementById('saveBtnText');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        saveBtn.disabled = true;
        saveBtnText.textContent = 'Saving... (သိမ်းဆည်းနေပါသည်)';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            saveBtn.disabled = false;
            saveBtnText.textContent = 'Save Changes (သိမ်းဆည်းမည်)';

            if (response.ok) {
                const data = await response.json();
                showToast(data.message || 'အခွန်နှင့် ဝန်ဆောင်ခ ဆက်တင်များကို အောင်မြင်စွာ သိမ်းဆည်းပြီးပါပြီ။', 'success');
                updateSimulation();
            } else if (response.status === 422) {
                const errorData = await response.json();
                let errors = Object.values(errorData.errors || {}).flat().join('<br>');
                showToast(errors || 'စစ်ဆေးချက် အမှားအယွင်းများ ရှိနေပါသည်။', 'error');
            } else {
                showToast('ဆာဗာတွင် ပြဿနာတစ်ခု ဖြစ်ပွားခဲ့ပါသည်။', 'error');
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtnText.textContent = 'Save Changes (သိမ်းဆည်းမည်)';
            showToast('ကွန်ရက် ချိတ်ဆက်မှု အမှားအယွင်း ဖြစ်ပွားခဲ့ပါသည်။', 'error');
        });
    });

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('taxToast');
        const toastMessage = document.getElementById('toastMessage');
        toastMessage.innerHTML = msg;
        toast.style.background = (type === 'error') ? '#ef4444' : '#10b981';
        toast.style.display = 'inline-flex';

        setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }

    // Initial calculation on page load
    document.addEventListener('DOMContentLoaded', updateSimulation);
</script>
@endpush
