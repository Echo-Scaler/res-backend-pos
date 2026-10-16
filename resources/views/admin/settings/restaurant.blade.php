@extends('admin.layouts.app')

@section('title', 'Restaurant Settings & Store Profile')

@push('styles')
<style>
    /* ==========================================================================
       ENTERPRISE RESTAURANT SETTINGS UI (MADA TYPOGRAPHY & OBSIDIAN SLATE THEME)
       ========================================================================== */
    .settings-portal {
        font-family: "Mada", sans-serif;
        color: var(--text-main);
    }

    .settings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .settings-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        color: var(--text-main);
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .settings-title p {
        font-size: 0.875rem;
        font-weight: 400;
        color: var(--text-muted);
        margin: 0;
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

    /* Tabs Navigation */
    .settings-tabs-nav {
        display: flex;
        gap: 0.5rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 2rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
    }

    .tab-btn {
        padding: 0.75rem 1.25rem;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--text-muted);
        font-size: 0.9rem;
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

    /* 2-Column Split: Form (Left) & Live Receipt Mock (Right) */
    .settings-split-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2rem;
        align-items: flex-start;
    }

    @media (max-width: 1024px) {
        .settings-split-layout {
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
        font-size: 1.1rem;
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

    .input-custom:disabled, .input-custom[readonly] {
        opacity: 0.7;
        background: rgba(0, 0, 0, 0.05);
        cursor: not-allowed;
    }

    textarea.input-custom {
        resize: vertical;
        min-height: 80px;
    }

    /* Switch Toggle */
    .switch-label-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        margin-bottom: 0.85rem;
    }

    .switch-info {
        display: flex;
        flex-direction: column;
    }

    .switch-info strong {
        font-size: 0.875rem;
        color: var(--text-main);
    }

    .switch-info span {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .toggle-checkbox {
        width: 44px;
        height: 24px;
        position: relative;
        appearance: none;
        background: rgba(255, 255, 255, 0.15);
        outline: none;
        border-radius: 20px;
        cursor: pointer;
        transition: 0.3s;
        border: 1px solid var(--border-color);
    }

    .toggle-checkbox:checked {
        background: var(--primary);
        border-color: var(--primary);
    }

    .toggle-checkbox::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ffffff;
        transition: 0.3s;
    }

    .toggle-checkbox:checked::after {
        transform: translateX(20px);
        background: #0b0f17;
    }

    /* Days Checkbox Grid */
    .days-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .day-pill-label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8125rem;
        font-weight: 600;
        background: var(--bg-body);
        color: var(--text-muted);
        transition: all 0.2s ease;
    }

    .day-pill-checkbox {
        display: none;
    }

    .day-pill-checkbox:checked + .day-pill-label {
        background: var(--primary);
        color: #0b0f17;
        border-color: var(--primary);
        font-weight: 700;
    }

    /* Logo Upload Dropzone */
    .logo-upload-wrap {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .logo-preview-box {
        width: 90px;
        height: 90px;
        border-radius: 12px;
        border: 2px dashed var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-body);
        overflow: hidden;
        flex-shrink: 0;
    }

    .logo-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .logo-preview-placeholder {
        color: var(--text-muted);
        font-size: 2rem;
    }

    /* 80mm Real Thermal Receipt Mock Box */
    .receipt-preview-sticky {
        position: sticky;
        top: 90px;
    }

    .receipt-container {
        background: #ffffff;
        color: #111827;
        font-family: 'Courier New', Courier, monospace;
        padding: 1.75rem 1.5rem;
        border-radius: 4px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        border-top: 4px solid var(--primary);
        font-size: 0.78rem;
        line-height: 1.4;
    }

    .receipt-header-center {
        text-align: center;
        margin-bottom: 1rem;
    }

    .receipt-logo-wrap {
        max-width: 60px;
        max-height: 60px;
        margin: 0 auto 0.5rem auto;
        display: flex;
        justify-content: center;
    }

    .receipt-hidden {
        display: none !important;
    }

    .receipt-logo-wrap img {
        max-width: 100%;
        max-height: 60px;
        filter: grayscale(100%) contrast(150%);
    }

    .receipt-store-name {
        font-size: 1.05rem;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 0.2rem;
        word-break: break-word;
    }

    .receipt-store-info {
        font-size: 0.72rem;
        color: #4b5563;
        margin-bottom: 0.2rem;
    }

    .receipt-divider {
        border-top: 1px dashed #9ca3af;
        margin: 0.75rem 0;
    }

    .receipt-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.25rem;
    }

    .receipt-bold-row {
        display: flex;
        justify-content: space-between;
        font-weight: 800;
        font-size: 0.85rem;
        margin-top: 0.4rem;
    }

    .receipt-footer-center {
        text-align: center;
        font-size: 0.72rem;
        color: #4b5563;
        margin-top: 0.75rem;
        word-break: break-word;
    }

    .tab-content-panel {
        display: none;
    }

    .tab-content-panel.active {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="settings-portal">
    <!-- Header -->
    <div class="settings-header">
        <div class="settings-title">
            <h1><i class="ti ti-settings" style="color: var(--primary);"></i> Restaurant Business Profile & Settings</h1>
            <p>Manage store profile, 80mm thermal receipt templates, operating hours, and POS hardware triggers (Currency: MMK).</p>
        </div>
        <div>
            <button type="submit" form="restaurantSettingsForm" class="btn-save-primary">
                <i class="ti ti-device-floppy"></i> Save Changes
            </button>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(158, 198, 59, 0.15); border: 1px solid var(--primary); color: var(--primary); padding: 0.85rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem; font-weight: 600;">
            <i class="ti ti-circle-check" style="font-size: 1.25rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(220, 53, 69, 0.15); border: 1px solid #dc3545; color: #dc3545; padding: 0.85rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;">
                <i class="ti ti-alert-circle"></i>
                <strong>ကျေးဇူးပြု၍ အောက်ပါ အချက်အလက်များအား ပြန်လည်စစ်ဆေးပေးပါ:</strong>
            </div>
            <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.8125rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="settings-tabs-nav">
        <button type="button" class="tab-btn active" onclick="switchSettingsTab('profile')">
            <i class="ti ti-building-store"></i> 1. General Profile & Branding
        </button>
        <button type="button" class="tab-btn" onclick="switchSettingsTab('contact')">
            <i class="ti ti-map-pin"></i> 2. Contact & Legal Tax
        </button>
        <button type="button" class="tab-btn" onclick="switchSettingsTab('receipt')">
            <i class="ti ti-receipt"></i> 3. 80mm Receipt Template
        </button>
        <button type="button" class="tab-btn" onclick="switchSettingsTab('hours')">
            <i class="ti ti-clock"></i> 4. Hours & Order Channels
        </button>
        <button type="button" class="tab-btn" onclick="switchSettingsTab('hardware')">
            <i class="ti ti-printer"></i> 5. POS Hardware & Currency
        </button>
    </div>

    <!-- Main Layout with Split View -->
    <form id="restaurantSettingsForm" method="POST" action="{{ route('admin.settings.restaurant.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="settings-split-layout">
            <!-- Left Form Area -->
            <div>
                <!-- TAB 1: General Profile & Branding -->
                <div id="tab-profile" class="tab-content-panel active">
                    <div class="settings-card">
                        <h3 class="card-section-title"><i class="ti ti-badge"></i> Brand Identity & Store Information</h3>
                        
                        <!-- Logo Upload Section -->
                        <div class="form-group">
                            <label>Brand Logo Image (ဆိုင်တံဆိပ် Logo)</label>
                            <small>Displayed on Customer QR Menus and Thermal Print Receipts (Max 2MB, JPG/PNG/WebP)</small>
                            <div class="logo-upload-wrap">
                                <div class="logo-preview-box" id="logoPreviewBox">
                                    @if($restaurant->logo_url)
                                        <img src="{{ $restaurant->logo_url }}" id="logoPreviewImg" alt="Logo">
                                    @else
                                        <i class="ti ti-photo logo-preview-placeholder" id="logoPlaceholderIcon"></i>
                                        <img src="" id="logoPreviewImg" alt="Logo" style="display: none;">
                                    @endif
                                </div>
                                <div>
                                    <input type="file" name="logo" id="logoInput" accept="image/*" class="input-custom" style="padding: 0.4rem;" onchange="previewLogoFile(this)">
                                    <div style="margin-top: 0.4rem;">
                                        <label style="font-weight: 500; font-size: 0.75rem; color: #dc3545; cursor: pointer;">
                                            <input type="checkbox" name="remove_logo" value="1" onchange="toggleRemoveLogo(this)"> Remove current logo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="name">Restaurant Legal Name (ဆိုင်အမည်) *</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $restaurant->name) }}" class="input-custom" required oninput="updateLiveReceipt()">
                            </div>
                            <div class="form-group">
                                <label for="slug">Branch Slug Identifier</label>
                                <input type="text" id="slug" value="{{ $restaurant->slug }}" class="input-custom" readonly>
                                <small>Used for internal multi-outlet routing</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tagline">Tagline / Slogan (ဆိုင်ဆောင်ပုဒ်)</label>
                            <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $restaurant->tagline) }}" placeholder="e.g. Authentic Myanmar & Asian Fusion Dining" class="input-custom" oninput="updateLiveReceipt()">
                        </div>

                        <div class="form-group">
                            <label for="cover_image">Cover Banner Photo (ဆိုင်မျက်နှာစာ ပုံရိပ်)</label>
                            <small>Displayed on customer QR mobile menu header</small>
                            <input type="file" id="cover_image" name="cover_image" accept="image/*" class="input-custom" style="padding: 0.4rem;">
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Contact & Legal Tax -->
                <div id="tab-contact" class="tab-content-panel">
                    <div class="settings-card">
                        <h3 class="card-section-title"><i class="ti ti-phone-call"></i> Contact & Legal Tax Information</h3>
                        
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="phone">Primary Phone (အဓိကဖုန်း) *</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $restaurant->phone) }}" placeholder="e.g. 09-450001234" class="input-custom" oninput="updateLiveReceipt()">
                            </div>
                            <div class="form-group">
                                <label for="secondary_phone">Secondary / Emergency Phone</label>
                                <input type="text" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone', $restaurant->secondary_phone) }}" placeholder="e.g. 09-250009876" class="input-custom">
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="email">Official Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $restaurant->email) }}" class="input-custom">
                            </div>
                            <div class="form-group">
                                <label for="tax_id">Commercial Tax Registration TIN (အခွန်မှတ်ပုံတင်အမှတ်)</label>
                                <input type="text" id="tax_id" name="tax_id" value="{{ old('tax_id', $restaurant->tax_id) }}" placeholder="e.g. CT-98472910-YGN" class="input-custom" oninput="updateLiveReceipt()">
                                <small>Mandatory for official tax compliance slips</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Street Address (ဆိုင်လိပ်စာ အပြည့်အစုံ)</label>
                            <textarea id="address" name="address" class="input-custom" rows="2" oninput="updateLiveReceipt()">{{ old('address', $restaurant->address) }}</textarea>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="city">Township / City (မြို့နယ်/မြို့)</label>
                                <input type="text" id="city" name="city" value="{{ old('city', $restaurant->city ?? 'Yangon') }}" class="input-custom">
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $restaurant->postal_code) }}" class="input-custom">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: 80mm Receipt Template -->
                <div id="tab-receipt" class="tab-content-panel">
                    <div class="settings-card">
                        <h3 class="card-section-title"><i class="ti ti-receipt-tax"></i> 80mm Thermal Receipt Slip Layout</h3>
                        
                        <div class="form-group">
                            <label for="receipt_header">Receipt Header Welcome Text (စလစ်ခေါင်းစီး ကြိုဆိုစာသား)</label>
                            <textarea id="receipt_header" name="receipt_header" class="input-custom" rows="2" placeholder="*** Welcome to Golden Palace Bistro ***" oninput="updateLiveReceipt()">{{ old('receipt_header', $restaurant->receipt_header ?? '*** Welcome to Golden Palace Bistro ***') }}</textarea>
                            <small>Printed directly beneath store name and contact lines</small>
                        </div>

                        <div class="form-group">
                            <label for="receipt_footer">Receipt Footer Text & WiFi Note (စလစ်အောက်ခြေ ကျေးဇူးတင်စာသား)</label>
                            <textarea id="receipt_footer" name="receipt_footer" class="input-custom" rows="2" placeholder="Thank You! WiFi: GP-Guest (PW: 12345678)" oninput="updateLiveReceipt()">{{ old('receipt_footer', $restaurant->receipt_footer ?? 'Thank You! Please Come Again. WiFi: GP-Guest / pass: 12345678') }}</textarea>
                            <small>Printed at the very bottom of customer bill</small>
                        </div>

                        <div class="switch-label-wrap">
                            <div class="switch-info">
                                <strong>Print Brand Logo on Receipt (စလစ်တွင် Logo ရိုက်နှိပ်ခြင်း)</strong>
                                <span>Includes monochrome logo header on 80mm thermal printers</span>
                            </div>
                            <input type="checkbox" name="show_logo_on_receipt" value="1" class="toggle-checkbox" id="showLogoCheck" {{ old('show_logo_on_receipt', $restaurant->show_logo_on_receipt) ? 'checked' : '' }} onchange="updateLiveReceipt()">
                        </div>

                        <div class="switch-label-wrap">
                            <div class="switch-info">
                                <strong>Print Commercial Tax TIN on Receipt (အခွန်နံပါတ် ပြသခြင်း)</strong>
                                <span>Renders official tax number for commercial compliance</span>
                            </div>
                            <input type="checkbox" name="show_tax_id_on_receipt" value="1" class="toggle-checkbox" id="showTaxIdCheck" {{ old('show_tax_id_on_receipt', $restaurant->show_tax_id_on_receipt) ? 'checked' : '' }} onchange="updateLiveReceipt()">
                        </div>
                    </div>
                </div>

                <!-- TAB 4: Hours & Order Channels -->
                <div id="tab-hours" class="tab-content-panel">
                    <div class="settings-card">
                        <h3 class="card-section-title"><i class="ti ti-calendar-time"></i> Operating Hours & Service Channels</h3>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="opening_time">Opening Time (ဆိုင်ဖွင့်ချိန်)</label>
                                <input type="time" id="opening_time" name="opening_time" value="{{ old('opening_time', $restaurant->opening_time ? substr($restaurant->opening_time, 0, 5) : '09:00') }}" class="input-custom">
                            </div>
                            <div class="form-group">
                                <label for="closing_time">Closing Time (ဆိုင်ပိတ်ချိန်)</label>
                                <input type="time" id="closing_time" name="closing_time" value="{{ old('closing_time', $restaurant->closing_time ? substr($restaurant->closing_time, 0, 5) : '22:00') }}" class="input-custom">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Weekly Operating Days (ဖွင့်လှစ်သော နေ့ရက်များ)</label>
                            @php
                                $currentDays = $restaurant->operating_days ?? ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
                            @endphp
                            <div class="days-grid">
                                @foreach(['MON' => 'Mon', 'TUE' => 'Tue', 'WED' => 'Wed', 'THU' => 'Thu', 'FRI' => 'Fri', 'SAT' => 'Sat', 'SUN' => 'Sun'] as $code => $label)
                                    <div>
                                        <input type="checkbox" id="day_{{ $code }}" name="operating_days[]" value="{{ $code }}" class="day-pill-checkbox" {{ in_array($code, (array) old('operating_days', $currentDays)) ? 'checked' : '' }}>
                                        <label for="day_{{ $code }}" class="day-pill-label">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <div class="switch-label-wrap">
                                <div class="switch-info">
                                    <strong>Enable Dine-In Service (စားပွဲထိုင်စား ခွင့်ပြုခြင်း)</strong>
                                    <span>Accept table seating and tableside ordering</span>
                                </div>
                                <input type="checkbox" name="is_dine_in_enabled" value="1" class="toggle-checkbox" {{ old('is_dine_in_enabled', $restaurant->is_dine_in_enabled) ? 'checked' : '' }}>
                            </div>

                            <div class="switch-label-wrap">
                                <div class="switch-info">
                                    <strong>Enable Takeaway / Parcel Orders (ပါဆယ် အော်ဒါ ခွင့်ပြုခြင်း)</strong>
                                    <span>Counter token pickup and parcel billing</span>
                                </div>
                                <input type="checkbox" name="is_takeaway_enabled" value="1" class="toggle-checkbox" {{ old('is_takeaway_enabled', $restaurant->is_takeaway_enabled) ? 'checked' : '' }}>
                            </div>

                            <div class="switch-label-wrap">
                                <div class="switch-info">
                                    <strong>Enable Customer QR Table Ordering (စားပွဲခုံ QR မှာယူမှု)</strong>
                                    <span>Guests can scan table QR code to view menu and place order</span>
                                </div>
                                <input type="checkbox" name="is_qr_order_enabled" value="1" class="toggle-checkbox" {{ old('is_qr_order_enabled', $restaurant->is_qr_order_enabled) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: POS Hardware & Currency -->
                <div id="tab-hardware" class="tab-content-panel">
                    <div class="settings-card">
                        <h3 class="card-section-title"><i class="ti ti-cpu"></i> POS Hardware & Financial Defaults</h3>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label>System Currency (ငွေကြေးယူနစ် - Rule 6)</label>
                                <input type="text" value="MMK (Burmese Kyats)" class="input-custom" readonly>
                                <small>Mandatory standard Burmese Kyats across all POS modules</small>
                            </div>
                            <div class="form-group">
                                <label>Printer Paper Width (စလစ်စက္ကူ အရွယ်အစား)</label>
                                <select name="printer_paper_width" class="input-custom">
                                    <option value="80mm" {{ old('printer_paper_width', $restaurant->printer_paper_width) === '80mm' ? 'selected' : '' }}>80mm (Standard Restaurant Thermal Printer)</option>
                                    <option value="58mm" {{ old('printer_paper_width', $restaurant->printer_paper_width) === '58mm' ? 'selected' : '' }}>58mm (Compact Mobile POS Printer)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="default_tax_percent">Default Commercial Tax % (ကုန်သွယ်ခွန် %)</label>
                                <input type="number" step="0.1" min="0" max="100" id="default_tax_percent" name="default_tax_percent" value="{{ old('default_tax_percent', $restaurant->default_tax_percent ?? 5.0) }}" class="input-custom" oninput="updateLiveReceipt()">
                            </div>
                            <div class="form-group">
                                <label for="default_service_charge_percent">Default Service Charge % (ဝန်ဆောင်ခ %)</label>
                                <input type="number" step="0.1" min="0" max="100" id="default_service_charge_percent" name="default_service_charge_percent" value="{{ old('default_service_charge_percent', $restaurant->default_service_charge_percent ?? 5.0) }}" class="input-custom">
                            </div>
                        </div>

                        <div style="margin-top: 1.25rem;">
                            <div class="switch-label-wrap">
                                <div class="switch-info">
                                    <strong>Auto-Kick Cash Drawer (ငွေရှင်းချိန် ငွေအံဆွဲ အလိုအလျောက် ဖွင့်ခြင်း)</strong>
                                    <span>Sends RJ11 pulse to steel cash drawer upon bill settlement</span>
                                </div>
                                <input type="checkbox" name="auto_kick_drawer" value="1" class="toggle-checkbox" {{ old('auto_kick_drawer', $restaurant->auto_kick_drawer) ? 'checked' : '' }}>
                            </div>

                            <div class="switch-label-wrap">
                                <div class="switch-info">
                                    <strong>Auto-Print Official Receipt (စလစ် အလိုအလျောက် ထုတ်ခြင်း)</strong>
                                    <span>Sends print command to counter thermal printer immediately</span>
                                </div>
                                <input type="checkbox" name="auto_print_receipt" value="1" class="toggle-checkbox" {{ old('auto_print_receipt', $restaurant->auto_print_receipt) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Live 80mm Receipt Mock -->
            <div>
                <div class="receipt-preview-sticky">
                    <div style="margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-weight: 700; font-size: 0.8125rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em;">
                            <i class="ti ti-live-view"></i> Live 80mm Receipt Preview
                        </span>
                        <span style="font-size: 0.75rem; color: var(--primary); font-weight: 600;">Real-Time</span>
                    </div>

                    <div class="receipt-container">
                        <div class="receipt-header-center">
                            <div class="receipt-logo-wrap {{ ($restaurant->logo_url && $restaurant->show_logo_on_receipt) ? '' : 'receipt-hidden' }}" id="liveReceiptLogoWrap">
                                <img src="{{ $restaurant->logo_url ?? '' }}" id="liveReceiptLogoImg" alt="Logo">
                            </div>
                            <div class="receipt-store-name" id="liveReceiptName">{{ $restaurant->name }}</div>
                            <div class="receipt-store-info" id="liveReceiptTagline">{{ $restaurant->tagline ?? 'Authentic Cuisine' }}</div>
                            <div class="receipt-store-info" id="liveReceiptAddress">{{ $restaurant->address ?? 'Kamayut, Yangon' }}</div>
                            <div class="receipt-store-info" id="liveReceiptPhone">Tel: {{ $restaurant->phone ?? '09-450001234' }}</div>
                            <div class="receipt-store-info {{ ($restaurant->tax_id && $restaurant->show_tax_id_on_receipt) ? '' : 'receipt-hidden' }}" id="liveReceiptTax">
                                Tax ID: {{ $restaurant->tax_id }}
                            </div>
                            <div style="margin-top: 0.4rem; font-style: italic; font-size: 0.7rem;" id="liveReceiptHeaderNote">
                                {{ $restaurant->receipt_header ?? '*** Welcome to Golden Palace Bistro ***' }}
                            </div>
                        </div>

                        <div class="receipt-divider"></div>

                        <div class="receipt-row">
                            <span>Table: T-04</span>
                            <span>Pax: 2</span>
                        </div>
                        <div class="receipt-row">
                            <span>Date: {{ now()->format('d/m/Y H:i') }}</span>
                            <span>Cashier: {{ auth()->user()->name ?? 'Cashier' }}</span>
                        </div>
                        <div class="receipt-row">
                            <span>Bill No: #ORD-1092</span>
                            <span>Type: DINE-IN</span>
                        </div>

                        <div class="receipt-divider"></div>

                        <!-- Sample items in Burmese Kyats MMK -->
                        <div class="receipt-row">
                            <span>1x Shan Noodle Special</span>
                            <span>4,000 MMK</span>
                        </div>
                        <div class="receipt-row">
                            <span>1x Kyay Oh Sikyet</span>
                            <span>4,500 MMK</span>
                        </div>
                        <div class="receipt-row">
                            <span>2x Fresh Lime Juice</span>
                            <span>3,000 MMK</span>
                        </div>

                        <div class="receipt-divider"></div>

                        <div class="receipt-row">
                            <span>Subtotal:</span>
                            <span>11,500 MMK</span>
                        </div>
                        <div class="receipt-row">
                            <span id="liveReceiptTaxLabel">Commercial Tax (5%):</span>
                            <span id="liveReceiptTaxVal">575 MMK</span>
                        </div>
                        
                        <div class="receipt-bold-row">
                            <span>TOTAL DUE:</span>
                            <span id="liveReceiptTotalVal">12,075 MMK</span>
                        </div>

                        <div class="receipt-row" style="margin-top: 0.25rem;">
                            <span>Cash Tendered:</span>
                            <span>15,000 MMK</span>
                        </div>
                        <div class="receipt-row">
                            <span>Change Due:</span>
                            <span>2,925 MMK</span>
                        </div>

                        <div class="receipt-divider"></div>

                        <div class="receipt-footer-center" id="liveReceiptFooterNote">
                            {{ $restaurant->receipt_footer ?? 'Thank You! WiFi: GP-Guest / pass: 12345678' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function switchSettingsTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content-panel').forEach(panel => panel.classList.remove('active'));

        event.currentTarget.classList.add('active');
        const targetPanel = document.getElementById('tab-' + tabName);
        if (targetPanel) {
            targetPanel.classList.add('active');
        }
    }

    function previewLogoFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('logoPreviewImg');
                const placeholder = document.getElementById('logoPlaceholderIcon');
                const liveLogo = document.getElementById('liveReceiptLogoImg');
                const liveLogoWrap = document.getElementById('liveReceiptLogoWrap');

                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';

                liveLogo.src = e.target.result;
                liveLogoWrap.style.display = document.getElementById('showLogoCheck').checked ? 'flex' : 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleRemoveLogo(checkbox) {
        const logoInput = document.getElementById('logoInput');
        const liveLogoWrap = document.getElementById('liveReceiptLogoWrap');
        if (checkbox.checked) {
            liveLogoWrap.style.display = 'none';
        } else {
            updateLiveReceipt();
        }
    }

    function updateLiveReceipt() {
        const nameVal = document.getElementById('name').value || 'RESTAURANT NAME';
        const taglineVal = document.getElementById('tagline').value || '';
        const phoneVal = document.getElementById('phone').value || '';
        const addressVal = document.getElementById('address').value || '';
        const taxIdVal = document.getElementById('tax_id').value || '';
        const headerNoteVal = document.getElementById('receipt_header').value || '';
        const footerNoteVal = document.getElementById('receipt_footer').value || '';

        const showLogo = document.getElementById('showLogoCheck').checked;
        const showTaxId = document.getElementById('showTaxIdCheck').checked;
        const taxPercent = parseFloat(document.getElementById('default_tax_percent').value) || 0;

        document.getElementById('liveReceiptName').innerText = nameVal;
        document.getElementById('liveReceiptTagline').innerText = taglineVal;
        document.getElementById('liveReceiptPhone').innerText = phoneVal ? `Tel: ${phoneVal}` : '';
        document.getElementById('liveReceiptAddress').innerText = addressVal;

        const liveTaxEl = document.getElementById('liveReceiptTax');
        if (taxIdVal && showTaxId) {
            liveTaxEl.innerText = `Tax ID: ${taxIdVal}`;
            liveTaxEl.style.display = 'block';
        } else {
            liveTaxEl.style.display = 'none';
        }

        document.getElementById('liveReceiptHeaderNote').innerText = headerNoteVal;
        document.getElementById('liveReceiptFooterNote').innerText = footerNoteVal;

        const liveLogoWrap = document.getElementById('liveReceiptLogoWrap');
        const liveLogoImg = document.getElementById('liveReceiptLogoImg');
        if (showLogo && liveLogoImg.getAttribute('src')) {
            liveLogoWrap.style.display = 'flex';
        } else {
            liveLogoWrap.style.display = 'none';
        }

        // Compute simulated tax in MMK
        const subtotal = 11500;
        const taxAmount = Math.round(subtotal * (taxPercent / 100));
        const total = subtotal + taxAmount;

        document.getElementById('liveReceiptTaxLabel').innerText = `Commercial Tax (${taxPercent}%):`;
        document.getElementById('liveReceiptTaxVal').innerText = `${taxAmount.toLocaleString()} MMK`;
        document.getElementById('liveReceiptTotalVal').innerText = `${total.toLocaleString()} MMK`;
    }
</script>
@endpush
@endsection
