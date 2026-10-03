@extends('admin.layouts.app')

@section('title', 'Dining Floor & Table Management')

@push('styles')
<style>
    /* Table Management Layout */
    .tables-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .tables-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tables-title p {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin: 0.25rem 0 0 0;
    }

    .tables-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* KPI Grid */
    .table-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .table-kpi-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.15rem;
        box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }

    .table-kpi-card:hover {
        transform: translateY(-3px);
        border-color: var(--primary);
        box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.08);
    }

    .kpi-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.55rem;
        flex-shrink: 0;
        transition: transform 0.25s ease;
    }

    .table-kpi-card:hover .kpi-icon-box {
        transform: scale(1.08);
    }

    .kpi-icon-total { 
        background: linear-gradient(135deg, rgba(158, 198, 59, 0.22) 0%, rgba(158, 198, 59, 0.08) 100%); 
        color: var(--primary); 
        border: 1px solid rgba(158, 198, 59, 0.35); 
    }
    .kpi-icon-vacant { 
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.22) 0%, rgba(16, 185, 129, 0.08) 100%); 
        color: #10b981; 
        border: 1px solid rgba(16, 185, 129, 0.35); 
    }
    .kpi-icon-occupied { 
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.22) 0%, rgba(59, 130, 246, 0.08) 100%); 
        color: #3b82f6; 
        border: 1px solid rgba(59, 130, 246, 0.35); 
    }
    .kpi-icon-billing { 
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.22) 0%, rgba(245, 158, 11, 0.08) 100%); 
        color: #f59e0b; 
        border: 1px solid rgba(245, 158, 11, 0.35); 
    }
    .kpi-icon-capacity { 
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.22) 0%, rgba(168, 85, 247, 0.08) 100%); 
        color: #a855f7; 
        border: 1px solid rgba(168, 85, 247, 0.35); 
    }

    .kpi-data .kpi-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .kpi-data .kpi-value {
        font-size: 1.625rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
        margin-top: 0.2rem;
    }

    /* Filter Toolbar */
    .filter-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1rem 1.35rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        box-shadow: 0 2px 10px -2px rgba(0, 0, 0, 0.03);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .area-pill {
        padding: 0.45rem 0.95rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-muted);
        border: 1px solid var(--border-color);
        background: transparent;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .area-pill:hover {
        border-color: var(--primary);
        color: var(--primary);
        background-color: var(--bg-hover);
        transform: translateY(-1px);
    }

    .area-pill.active {
        background: linear-gradient(135deg, var(--primary) 0%, #8bb42c 100%);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(158, 198, 59, 0.35);
        font-weight: 700;
    }

    .filter-select {
        padding: 0.45rem 0.85rem;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background-color: var(--bg-body);
        color: var(--text-main);
        font-size: 0.8125rem;
        font-family: inherit;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .filter-select:focus {
        border-color: var(--primary);
    }

    /* Table Grid Cards */
    .tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(315px, 1fr));
        gap: 1.4rem;
        align-items: start;
    }

    .table-box {
        align-self: start;
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.35rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
    }

    /* Modern top status hairline glow */
    .table-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
        background: transparent;
        transition: background 0.3s ease;
    }
    .table-box.status-box-vacant::before { background: linear-gradient(90deg, #10b981 0%, rgba(16, 185, 129, 0.2) 100%); }
    .table-box.status-box-occupied::before { background: linear-gradient(90deg, #3b82f6 0%, rgba(59, 130, 246, 0.2) 100%); }
    .table-box.status-box-ordering::before { background: linear-gradient(90deg, #f59e0b 0%, rgba(245, 158, 11, 0.2) 100%); }
    .table-box.status-box-billing::before { background: linear-gradient(90deg, #f97316 0%, rgba(249, 115, 22, 0.2) 100%); }
    .table-box.status-box-reserved::before { background: linear-gradient(90deg, #a855f7 0%, rgba(168, 85, 247, 0.2) 100%); }
    .table-box.status-box-out_of_service::before { background: linear-gradient(90deg, #64748b 0%, rgba(100, 116, 139, 0.2) 100%); }

    .table-box:hover {
        border-color: rgba(158, 198, 59, 0.6);
        transform: translateY(-4px);
        box-shadow: 0 18px 36px -4px rgba(0, 0, 0, 0.12), 0 4px 12px -2px rgba(0, 0, 0, 0.06);
    }

    .table-box-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.95rem;
    }

    .table-number-tag {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        letter-spacing: -0.02em;
    }

    .table-icon-avatar {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: linear-gradient(135deg, rgba(158, 198, 59, 0.2) 0%, rgba(158, 198, 59, 0.08) 100%);
        border: 1px solid rgba(158, 198, 59, 0.35);
        color: var(--primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(158, 198, 59, 0.15);
    }

    .status-badge {
        font-size: 0.725rem;
        font-weight: 700;
        padding: 0.32rem 0.8rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .status-dot {
        width: 7.5px;
        height: 7.5px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    @keyframes statusPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.35; transform: scale(0.8); }
    }

    .status-vacant { 
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.14) 0%, rgba(5, 150, 105, 0.18) 100%); 
        color: #10b981; 
        border: 1px solid rgba(16, 185, 129, 0.38); 
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.15);
    }
    .status-vacant .status-dot {
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
    }

    .status-occupied { 
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.18) 0%, rgba(59, 130, 246, 0.24) 100%); 
        color: #3b82f6; 
        border: 1px solid rgba(59, 130, 246, 0.45); 
        box-shadow: 0 2px 10px rgba(59, 130, 246, 0.25);
    }
    .status-occupied .status-dot {
        background: #3b82f6;
        box-shadow: 0 0 10px #3b82f6;
        animation: statusPulse 1.8s infinite;
    }

    .status-ordering { 
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.18) 0%, rgba(217, 119, 6, 0.24) 100%); 
        color: #f59e0b; 
        border: 1px solid rgba(245, 158, 11, 0.45); 
        box-shadow: 0 2px 10px rgba(245, 158, 11, 0.25);
    }
    .status-ordering .status-dot {
        background: #f59e0b;
        box-shadow: 0 0 10px #f59e0b;
        animation: statusPulse 1.8s infinite;
    }

    .status-billing { 
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.18) 0%, rgba(234, 88, 12, 0.24) 100%); 
        color: #f97316; 
        border: 1px solid rgba(249, 115, 22, 0.45); 
        box-shadow: 0 2px 10px rgba(249, 115, 22, 0.25);
    }
    .status-billing .status-dot {
        background: #f97316;
        box-shadow: 0 0 10px #f97316;
        animation: statusPulse 1.8s infinite;
    }

    .status-reserved { 
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.18) 0%, rgba(147, 51, 234, 0.24) 100%); 
        color: #a855f7; 
        border: 1px solid rgba(168, 85, 247, 0.45); 
        box-shadow: 0 2px 10px rgba(168, 85, 247, 0.25);
    }
    .status-reserved .status-dot {
        background: #a855f7;
        box-shadow: 0 0 10px #a855f7;
    }

    .status-out_of_service { 
        background: rgba(100, 116, 139, 0.15); 
        color: #94a3b8; 
        border: 1px solid rgba(100, 116, 139, 0.35); 
    }
    .status-out_of_service .status-dot {
        background: #64748b;
    }

    .table-details {
        margin-bottom: 1.15rem;
        font-size: 0.875rem;
    }

    .table-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-main);
        margin-bottom: 0.45rem;
    }

    .table-meta-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 0.35rem;
    }

    .table-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.22rem 0.65rem;
        border-radius: 7px;
        background: var(--bg-hover);
        color: var(--text-muted);
        font-size: 0.775rem;
        font-weight: 600;
        border: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .table-meta-item:hover {
        background: var(--bg-card-tint);
        color: var(--text-main);
    }

    /* Actions Bar */
    .table-box-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 0.95rem;
        border-top: 1px solid var(--border-color);
        gap: 0.5rem;
    }

    .btn-qr-view {
        background: linear-gradient(135deg, rgba(158, 198, 59, 0.16) 0%, rgba(158, 198, 59, 0.08) 100%);
        color: var(--primary);
        border: 1px solid rgba(158, 198, 59, 0.38);
        padding: 0.48rem 0.95rem;
        border-radius: 10px;
        font-size: 0.8125rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-qr-view:hover {
        background: linear-gradient(135deg, var(--primary) 0%, #8bb42c 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(158, 198, 59, 0.35);
        transform: translateY(-1px);
    }

    .action-icons-group {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .btn-icon-action {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-color);
        background: var(--bg-hover);
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-icon-action:hover {
        border-color: var(--primary);
        color: var(--primary);
        background-color: var(--bg-card);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .btn-icon-delete:hover {
        border-color: var(--danger);
        color: var(--danger);
        background-color: rgba(220, 38, 38, 0.1);
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.15);
    }

    /* Modals */
    .modal-backdrop-custom {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        box-sizing: border-box;
    }

    .modal-backdrop-custom.active {
        display: flex;
    }

    .modal-dialog-custom {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        width: 100%;
        max-width: 530px;
        max-height: min(92vh, 820px);
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        overflow: hidden;
    }

    .modal-dialog-custom > form {
        display: flex;
        flex-direction: column;
        max-height: min(92vh, 820px);
        overflow: hidden;
        width: 100%;
    }

    .modal-header-custom {
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        background: var(--bg-card);
    }

    .modal-header-custom h3 {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-main);
    }

    .modal-body-custom {
        padding: 1.25rem 1.4rem;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
    }

    .form-group-custom {
        margin-bottom: 1.15rem;
    }

    .form-group-custom label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.4rem;
    }

    .form-control-custom {
        width: 100%;
        padding: 0.6rem 0.85rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background-color: var(--bg-body);
        color: var(--text-main);
        font-size: 0.875rem;
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
    }

    .form-control-custom:focus {
        border-color: var(--primary);
    }

    .modal-footer-custom {
        padding: 1rem 1.4rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        flex-shrink: 0;
        background: var(--bg-card);
    }

    /* Modern Theme Buttons */
    .btn-modern-primary {
        background-color: var(--primary, #9ec63b);
        color: #0f172a !important;
        font-family: inherit;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.65rem 1.35rem;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        box-shadow: 0 4px 14px rgba(158, 198, 59, 0.32);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        line-height: 1.4;
    }

    .btn-modern-primary:hover {
        background-color: var(--primary-hover, #8bb42c);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(158, 198, 59, 0.42);
        color: #0f172a !important;
    }

    .btn-modern-primary:active {
        transform: translateY(0);
    }

    .btn-modern-secondary {
        background-color: var(--bg-card);
        color: var(--text-main) !important;
        font-family: inherit;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        line-height: 1.4;
    }

    .btn-modern-secondary:hover {
        background-color: var(--bg-hover);
        border-color: var(--primary, #9ec63b);
        color: var(--primary, #9ec63b) !important;
        transform: translateY(-1px);
    }

    .btn-modern-secondary:active {
        transform: translateY(0);
    }

    /* QR Code Display Modal */
    .qr-preview-box {
        text-align: center;
        padding: 1.5rem 1rem;
        background: #ffffff;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
    }

    .qr-preview-box img, .qr-preview-box svg {
        max-width: 220px;
        height: auto;
        display: inline-block;
    }

    .qr-url-pill {
        display: flex;
        align-items: center;
        background-color: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        margin-bottom: 1rem;
        gap: 0.5rem;
    }

    .qr-url-pill input {
        border: none;
        background: transparent;
        color: var(--text-main);
        font-size: 0.8125rem;
        flex: 1;
        outline: none;
    }

    /* Table Active Order Box & Quick Verification (Modern Clean Aesthetic) */
    .table-order-box {
        display: none;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.95rem;
        margin-bottom: 0.95rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05), inset 0 1px 0 #ffffff;
    }

    .table-order-box.is-visible {
        display: block;
        animation: orderBoxSlideIn 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes orderBoxSlideIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table-order-box:hover {
        border-color: #cbd5e1;
    }

    .status-badge-toggle {
        cursor: pointer;
        border: 1px solid currentColor;
        font-family: inherit;
        background: transparent;
        outline: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.32rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.725rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .status-badge-toggle:hover {
        transform: translateY(-1px) scale(1.03);
        filter: brightness(1.15);
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
    }

    .status-badge-toggle:active {
        transform: translateY(0) scale(0.98);
    }

    .status-badge-toggle.is-active-open {
        box-shadow: 0 0 0 2px var(--primary), 0 4px 14px rgba(158, 198, 59, 0.35);
        border-color: var(--primary);
    }

    .badge-chevron, .order-toggle-arrow {
        display: inline-flex;
        align-items: center;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        font-size: 0.75rem;
    }

    .status-badge-toggle.is-active-open .badge-chevron,
    .status-badge-toggle.is-active-open .order-toggle-arrow {
        transform: rotate(180deg);
    }

    .table-order-box-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 0.65rem;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 0.7rem;
        gap: 0.5rem;
    }

    .order-ref-text {
        font-size: 0.875rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.01em;
    }

    .order-time-ago {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
    }

    .reprint-badge-tag {
        font-size: 0.6875rem;
        font-weight: 700;
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 0.2rem 0.55rem;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        gap: 0.32rem;
        margin-bottom: 0.65rem;
    }

    .order-items-list-clean {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        margin: 0.5rem 0 0.75rem 0;
        max-height: 230px;
        overflow-y: auto;
        padding-right: 0.2rem;
    }

    .order-items-list-clean::-webkit-scrollbar {
        width: 4px;
    }
    .order-items-list-clean::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .item-list-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 0.55rem 0.75rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        gap: 0.7rem;
        transition: all 0.18s ease;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .item-list-row:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
        transform: translateY(-1px);
    }

    .item-qty-tag {
        font-weight: 700;
        color: #15803d;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        font-size: 0.8125rem;
        padding: 0.18rem 0.48rem;
        border-radius: 6px;
        min-width: 26px;
        text-align: center;
        line-height: 1.2;
    }

    .item-dish-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.35;
    }

    .special-note-pill {
        font-size: 0.7rem;
        font-weight: 600;
        background: #fffbeb;
        border: 1px solid #fef3c7;
        color: #b45309;
        padding: 0.12rem 0.45rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 0.28rem;
        margin-top: 0.25rem;
    }

    .item-price-tag {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
    }

    .table-order-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #e2e8f0;
        padding-top: 0.75rem;
        margin-top: 0.5rem;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .table-order-total {
        display: flex;
        flex-direction: column;
    }

    .table-order-total-label {
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
    }

    .table-order-total-amount {
        font-size: 1.15rem;
        font-weight: 800;
        color: #15803d;
    }

    .btn-pass-action {
        padding: 0.42rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-pass-action:hover {
        border-color: #94a3b8;
        color: #0f172a;
        background: #f1f5f9;
        transform: translateY(-1px);
    }

    .btn-reprint-slip {
        border-color: #fecdd3 !important;
        background: #fff1f2 !important;
        color: #e11d48 !important;
    }

    .btn-reprint-slip:hover {
        border-color: #e11d48 !important;
        background: #e11d48 !important;
        color: #ffffff !important;
        box-shadow: 0 3px 10px rgba(225, 29, 72, 0.25) !important;
    }

    .btn-move-table-action {
        border-color: #e2e8f0 !important;
        background: #ffffff !important;
        color: #334155 !important;
    }

    .btn-move-table-action:hover {
        border-color: #94a3b8 !important;
        color: #0f172a !important;
        background: #f1f5f9 !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06) !important;
        transform: translateY(-1px);
    }

    .btn-transfer-table:hover {
        border-color: #3b82f6 !important;
        color: #3b82f6 !important;
        background-color: rgba(59, 130, 246, 0.12) !important;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2) !important;
    }

    .btn-icon-print-chit {
        padding: 0.42rem 0.65rem;
        border-radius: 8px;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #16a34a;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-icon-print-chit:hover {
        background: #16a34a;
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(22, 163, 74, 0.25);
        transform: translateY(-1px);
    }

    .btn-icon-print-bill {
        padding: 0.42rem 0.65rem;
        border-radius: 8px;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #2563eb;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-icon-print-bill:hover {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.25);
        transform: translateY(-1px);
    }

    /* Hide table status dropdown when active order box is open */
    .table-box.order-open .table-status-wrap {
        display: none !important;
    }

    /* Dark Mode Support for Table Order Box */
    [data-theme="dark"] .table-order-box {
        background: #111724;
        border-color: #222d42;
        box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.5);
    }
    [data-theme="dark"] .table-order-box-header,
    [data-theme="dark"] .table-order-footer {
        border-color: #222d42;
    }
    [data-theme="dark"] .order-ref-text,
    [data-theme="dark"] .item-dish-name,
    [data-theme="dark"] .item-price-tag {
        color: #f1f5f9;
    }
    [data-theme="dark"] .order-time-ago,
    [data-theme="dark"] .table-order-total-label {
        color: #94a3b8;
    }
    [data-theme="dark"] .item-list-row {
        background: #161e2e;
        border-color: #222d42;
        box-shadow: none;
    }
    [data-theme="dark"] .item-list-row:hover {
        background: #1c2638;
        border-color: #3b82f6;
    }
    [data-theme="dark"] .item-qty-tag {
        background: rgba(163, 230, 53, 0.15);
        border-color: rgba(163, 230, 53, 0.35);
        color: #a3e635;
    }
    [data-theme="dark"] .special-note-pill {
        background: rgba(245, 158, 11, 0.15);
        border-color: rgba(245, 158, 11, 0.3);
        color: #fbbf24;
    }
    [data-theme="dark"] .table-order-total-amount {
        color: #a3e635;
    }
    [data-theme="dark"] .btn-move-table-action {
        background: #161e2e !important;
        border-color: #222d42 !important;
        color: #e2e8f0 !important;
    }
    [data-theme="dark"] .btn-move-table-action:hover {
        background: #1c2638 !important;
        color: #ffffff !important;
    }
    [data-theme="dark"] .btn-reprint-slip {
        background: rgba(225, 29, 72, 0.15) !important;
        border-color: rgba(225, 29, 72, 0.35) !important;
        color: #fda4af !important;
    }
    [data-theme="dark"] .btn-icon-print-chit {
        background: rgba(22, 163, 74, 0.15);
        border-color: rgba(22, 163, 74, 0.35);
        color: #86efac;
    }
    [data-theme="dark"] .btn-icon-print-bill {
        background: rgba(37, 99, 235, 0.15);
        border-color: rgba(37, 99, 235, 0.35);
        color: #93c5fd;
    }

    /* Table Transfer Step Flow & Quick Table Picker */
    .table-transfer-step-flow {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.95rem;
        gap: 0.75rem;
    }

    .transfer-flow-node {
        flex: 1;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.65rem 0.85rem;
        transition: all 0.2s ease;
    }

    .transfer-flow-node.source-node {
        border-color: rgba(59, 130, 246, 0.35);
        background: rgba(59, 130, 246, 0.04);
    }

    .transfer-flow-node.target-node {
        border-color: #cbd5e1;
        background: #ffffff;
    }

    .transfer-flow-node.target-node.has-target {
        border-color: #22c55e;
        background: #f0fdf4;
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.15);
    }

    .transfer-flow-node.target-node.has-error {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .transfer-flow-badge {
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #64748b;
        display: block;
        margin-bottom: 0.2rem;
    }

    .source-node .transfer-flow-badge {
        color: #2563eb;
    }

    .target-node .transfer-flow-badge {
        color: #15803d;
    }

    .transfer-flow-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .transfer-flow-sub {
        font-size: 0.725rem;
        color: #64748b;
        margin-top: 0.15rem;
    }

    .transfer-flow-arrow {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.2rem;
        font-size: 1.25rem;
        color: #94a3b8;
        padding: 0 0.25rem;
    }

    .transfer-flow-arrow span {
        font-size: 0.6rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .vacant-table-chips-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        max-height: 95px;
        overflow-y: auto;
        padding: 0.35rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
    }

    .btn-vacant-table-chip {
        font-family: inherit;
        cursor: pointer;
        padding: 0.25rem 0.55rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #334155;
        transition: all 0.15s ease;
    }

    .btn-vacant-table-chip:hover {
        background: #eef3e8;
        border-color: #9ec63b;
        color: #15803d;
        transform: translateY(-1px);
    }

    .btn-vacant-table-chip.is-selected {
        background: #15803d;
        border-color: #15803d;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(21, 128, 61, 0.3);
    }

    [data-theme="dark"] .table-transfer-step-flow {
        background: #111724;
        border-color: #222d42;
    }
    [data-theme="dark"] .transfer-flow-node {
        background: #161e2e;
        border-color: #222d42;
    }
    [data-theme="dark"] .transfer-flow-title {
        color: #f1f5f9;
    }
    [data-theme="dark"] .transfer-flow-sub {
        color: #94a3b8;
    }
    [data-theme="dark"] .vacant-table-chips-list {
        background: #161e2e;
        border-color: #222d42;
    }
    [data-theme="dark"] .btn-vacant-table-chip {
        background: #111724;
        border-color: #222d42;
        color: #cbd5e1;
    }
    [data-theme="dark"] .btn-vacant-table-chip:hover {
        background: #1c2638;
        border-color: #9ec63b;
        color: #a3e635;
    }

    .table-status-wrap {
        position: relative;
        margin-bottom: 0.85rem;
    }

    .table-status-select {
        width: 100%;
        padding: 0.5rem 0.85rem 0.5rem 1.95rem;
        font-size: 0.8125rem;
        font-weight: 600;
        border-radius: 11px;
        border: 1px solid var(--border-color);
        background-color: var(--bg-hover);
        color: var(--text-main);
        font-family: inherit;
        outline: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.85rem center;
    }

    .table-status-select:hover {
        border-color: var(--primary);
    }

    .table-status-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.2);
    }

    .table-status-icon-dot {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        font-size: 0.75rem;
        color: var(--primary);
    }

    /* Modal Form / Radio / Audit styles */
    .reprint-audit-notice {
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #d97706;
        padding: 0.85rem 1rem;
        border-radius: 10px;
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        font-size: 0.8125rem;
        line-height: 1.45;
    }

    .reprint-audit-notice code {
        font-family: monospace;
        font-weight: 700;
        background: rgba(0, 0, 0, 0.25);
        padding: 0.15rem 0.35rem;
        border-radius: 4px;
        color: #f59e0b;
    }

    .reprint-option-card {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: var(--bg-body);
        user-select: none;
    }

    .reprint-option-card:hover {
        border-color: var(--primary);
        background-color: rgba(158, 198, 59, 0.05);
    }

    .reprint-option-card.selected {
        border-color: var(--primary);
        background-color: rgba(158, 198, 59, 0.12);
    }

    .reprint-option-card input[type="radio"] {
        accent-color: var(--primary);
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .reprint-card-icon {
        font-size: 1.35rem;
        color: var(--primary);
        line-height: 1;
    }

    .reprint-card-text {
        flex: 1;
    }

    .reprint-card-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .reprint-card-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }

    /* Toast Notification (Zero Refresh) */
    .pos-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #111724;
        border: 1px solid #22c55e;
        color: #f1f5f9;
        padding: 0.85rem 1.25rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        z-index: 10000;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    .pos-toast.active {
        transform: translateY(0);
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="tables-header">
        <div class="tables-title">
            <h1><i class="ti ti-table text-primary"></i> Dining Floor & Table Management</h1>
            <p>Saizeriya-style QR table seating, real-time floor statuses, and acrylic stand printing</p>
        </div>
        <div class="tables-header-actions">
            <a href="{{ route('admin.tables.batch-print', ['area' => $areaFilter]) }}" target="_blank" class="btn-modern-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                <i class="ti ti-printer"></i> Batch Print QR Stands
            </a>
            <button type="button" class="btn-modern-primary" onclick="openCreateTableModal()" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.15rem; border-radius: 8px; border: none; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                <i class="ti ti-plus"></i> Add New Table
            </button>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #16a34a; padding: 0.85rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 500;">
            <i class="ti ti-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(220, 38, 38, 0.15); border: 1px solid rgba(220, 38, 38, 0.3); color: #dc2626; padding: 0.85rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 500;">
            <i class="ti ti-alert-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Floor Metrics KPI Cards -->
    <div class="table-kpi-grid">
        <div class="table-kpi-card">
            <div class="kpi-icon-box kpi-icon-total"><i class="ti ti-armchair"></i></div>
            <div class="kpi-data">
                <div class="kpi-label">Total Tables</div>
                <div class="kpi-value" id="kpiTotalTables">{{ $metrics['total_tables'] }}</div>
            </div>
        </div>
        <div class="table-kpi-card">
            <div class="kpi-icon-box kpi-icon-vacant"><i class="ti ti-circle-check"></i></div>
            <div class="kpi-data">
                <div class="kpi-label">Vacant & Ready</div>
                <div class="kpi-value" id="kpiVacantTables" style="color: #22c55e;">{{ $metrics['vacant_tables'] }}</div>
            </div>
        </div>
        <div class="table-kpi-card">
            <div class="kpi-icon-box kpi-icon-occupied"><i class="ti ti-users"></i></div>
            <div class="kpi-data">
                <div class="kpi-label">Occupied / Dining</div>
                <div class="kpi-value" id="kpiOccupiedTables" style="color: #3b82f6;">{{ $metrics['occupied_tables'] }}</div>
            </div>
        </div>
        <div class="table-kpi-card">
            <div class="kpi-icon-box kpi-icon-billing"><i class="ti ti-receipt"></i></div>
            <div class="kpi-data">
                <div class="kpi-label">Billing / Checkout</div>
                <div class="kpi-value" id="kpiBillingTables" style="color: #f59e0b;">{{ $metrics['billing_tables'] }}</div>
            </div>
        </div>
        <div class="table-kpi-card">
            <div class="kpi-icon-box kpi-icon-capacity"><i class="ti ti-user-check"></i></div>
            <div class="kpi-data">
                <div class="kpi-label">Total Floor Seats</div>
                <div class="kpi-value" id="kpiCapacity">{{ $metrics['total_capacity'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="filter-card">
        <div class="filter-group">
            <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); margin-right: 0.25rem;">Zone / Area:</span>
            <a href="{{ route('admin.tables.index', array_merge(request()->except('area'), ['area' => ''])) }}" class="area-pill {{ empty($areaFilter) ? 'active' : '' }}">All Zones</a>
            @foreach($floorAreas as $area)
                <a href="{{ route('admin.tables.index', array_merge(request()->except('area'), ['area' => $area])) }}" class="area-pill {{ $areaFilter === $area ? 'active' : '' }}">
                    {{ $area }}
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.tables.index') }}" style="display: flex; align-items: center; gap: 0.5rem;">
            @if($areaFilter)
                <input type="hidden" name="area" value="{{ $areaFilter }}">
            @endif
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="VACANT" {{ $statusFilter === 'VACANT' ? 'selected' : '' }}>🟢 Vacant</option>
                <option value="OCCUPIED" {{ $statusFilter === 'OCCUPIED' ? 'selected' : '' }}>🔵 Occupied</option>
                <option value="ORDERING" {{ $statusFilter === 'ORDERING' ? 'selected' : '' }}>🟡 Ordering</option>
                <option value="BILLING" {{ $statusFilter === 'BILLING' ? 'selected' : '' }}>🟠 Billing</option>
                <option value="RESERVED" {{ $statusFilter === 'RESERVED' ? 'selected' : '' }}>🟣 Reserved</option>
                <option value="OUT_OF_SERVICE" {{ $statusFilter === 'OUT_OF_SERVICE' ? 'selected' : '' }}>⚪ Out of Service</option>
            </select>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search table..." class="filter-select" style="min-width: 140px;">
            <button type="submit" class="btn-icon-action" title="Search"><i class="ti ti-search"></i></button>
            @if($search || $statusFilter || $areaFilter)
                <a href="{{ route('admin.tables.index') }}" class="btn-icon-action" title="Clear filters"><i class="ti ti-x"></i></a>
            @endif
        </form>
    </div>

    <!-- Dining Tables Floor Grid -->
    @if($tables->isEmpty())
        <div style="background: var(--bg-card); border: 1px dashed var(--border-color); border-radius: 14px; padding: 3rem 1.5rem; text-align: center; color: var(--text-muted);">
            <i class="ti ti-armchair-off" style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem; opacity: 0.6;"></i>
            <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.35rem;">No Dining Tables Found</h4>
            <p style="font-size: 0.875rem; margin-bottom: 1.25rem;">Start by adding dining tables to generate customer QR codes for tableside self-ordering.</p>
            <button type="button" class="btn-modern-primary" onclick="openCreateTableModal()" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                <i class="ti ti-plus"></i> Add Table Now
            </button>
        </div>
    @else
        <div class="tables-grid">
            @foreach($tables as $table)
                <div class="table-box status-box-{{ strtolower($table->status) }}" id="table-card-{{ $table->id }}">
                    <div>
                        @php
                            $activeOrder = $table->currentOrder ?? $table->orders->first();
                            $hasOrder = $activeOrder && $activeOrder->items->isNotEmpty();
                        @endphp

                        <div class="table-box-header">
                            <div class="table-number-tag">
                                <span class="table-icon-avatar"><i class="ti ti-table"></i></span>
                                {{ $table->table_number }}
                            </div>
                            @if($hasOrder)
                                <button type="button" 
                                    class="status-badge status-{{ strtolower($table->status) }} status-badge-toggle" 
                                    id="table-badge-{{ $table->id }}"
                                    data-table-id="{{ $table->id }}"
                                    onclick="toggleTableOrderBox(this)"
                                    title="Click to view/hide order items">
                                    <span class="status-dot"></span>
                                    {{ $table->status }}
                                    <span class="badge-chevron"><i class="ti ti-chevron-down" id="order-arrow-{{ $table->id }}"></i></span>
                                </button>
                            @else
                                <span class="status-badge status-{{ strtolower($table->status) }}" id="table-badge-{{ $table->id }}">
                                    <span class="status-dot"></span>
                                    {{ $table->status }}
                                </span>
                            @endif
                        </div>

                        <div class="table-details">
                            @if($table->name)
                                <div class="table-name">{{ $table->name }}</div>
                            @endif
                            <div class="table-meta-row">
                                <span class="table-meta-item"><i class="ti ti-users"></i> {{ $table->seating_capacity }} Seats</span>
                                <span class="table-meta-item"><i class="ti ti-map-pin"></i> {{ $table->floor_area }}</span>
                            </div>
                            @if($table->notes)
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.4rem; font-style: italic;">
                                    "{{ $table->notes }}"
                                </div>
                            @endif
                        </div>

                        @if($hasOrder)
                            <!-- Table Active Order Section (Employee Quick Check & Slip Reprint - Shown when clicking OCCUPIED) -->
                            <div class="table-order-box" id="table-order-box-{{ $table->id }}">
                                <div class="table-order-box-header">
                                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                                        <i class="ti ti-receipt text-primary" style="font-size: 1.05rem;"></i>
                                        <span class="order-ref-text">#{{ $activeOrder->order_number }}</span>
                                        <span class="order-time-ago">• {{ $activeOrder->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                @if($activeOrder->reprint_count > 0)
                                    <div class="reprint-badge-tag" id="reprint-badge-{{ $activeOrder->id }}">
                                        <i class="ti ti-printer"></i> REPRINT #{{ $activeOrder->reprint_count }} (DUPLICATE)
                                    </div>
                                @endif

                                <!-- Clean dishes list (Rule #5 Typography & Rule #6 MMK Currency) -->
                                <div class="order-items-list-clean">
                                    @foreach($activeOrder->items as $item)
                                        <div class="item-list-row">
                                            <div class="item-qty-tag">{{ $item->quantity }}x</div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div class="item-dish-name">{{ $item->item_name }}</div>
                                                @if($item->special_notes)
                                                    <span class="special-note-pill"><i class="ti ti-note"></i> {{ $item->special_notes }}</span>
                                                @endif
                                            </div>
                                            <div class="item-price-tag">{{ number_format($item->subtotal, 0) }} MMK</div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Total & Quick Slip Reprint Actions -->
                                <div class="table-order-footer">
                                    <div class="table-order-total">
                                        <span class="table-order-total-label">Bill Total:</span>
                                        <span class="table-order-total-amount">{{ number_format($activeOrder->total_amount, 0) }} MMK</span>
                                    </div>

                                    <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                        <button type="button" class="btn-pass-action btn-move-table-action"
                                            data-source-id="{{ $table->id }}"
                                            data-source-number="{{ $table->table_number }}"
                                            data-source-name="{{ $table->name ?? '' }}"
                                            data-order-num="{{ $activeOrder ? $activeOrder->order_number : '' }}"
                                            data-order-total="{{ $activeOrder ? number_format($activeOrder->total_amount, 0) . ' MMK' : '' }}"
                                            onclick="openTransferTableModal(this)">
                                            <i class="ti ti-arrows-exchange"></i> Change Table
                                        </button>
                                        <button type="button" class="btn-pass-action btn-reprint-slip"
                                            data-id="{{ $activeOrder->id }}"
                                            data-number="{{ $activeOrder->order_number }}"
                                            data-table="{{ $table->table_number }}"
                                            data-reprint-url="{{ route('admin.orders.reprint', $activeOrder) }}"
                                            data-count="{{ $activeOrder->reprint_count }}">
                                            <i class="ti ti-printer"></i> Reprint
                                        </button>
                                        <a href="{{ route('admin.orders.printKitchenChit', $activeOrder) }}" target="_blank" class="btn-icon-print-chit" title="Print Kitchen Chit">
                                            <i class="ti ti-tools-kitchen-2"></i> Chit
                                        </a>
                                        <a href="{{ route('admin.orders.printCustomerBill', $activeOrder) }}" target="_blank" class="btn-icon-print-bill" title="Print Bill Slip">
                                            <i class="ti ti-receipt"></i> Bill
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div>
                        <!-- Quick Status Switcher (Async / Zero Refresh) -->
                        <div class="table-status-wrap" id="table-status-wrap-{{ $table->id }}">
                            <span class="table-status-icon-dot"><i class="ti ti-point-filled"></i></span>
                            <select name="status" 
                                class="table-status-select" 
                                data-table-id="{{ $table->id }}"
                                data-table-number="{{ $table->table_number }}"
                                data-url="{{ route('admin.tables.updateStatus', $table) }}"
                                onchange="updateTableStatusAjax(this)">
                                <option value="VACANT" {{ $table->status === 'VACANT' ? 'selected' : '' }}>🟢 Vacant (Available)</option>
                                <option value="OCCUPIED" {{ $table->status === 'OCCUPIED' ? 'selected' : '' }}>🔵 Occupied (Seated)</option>
                                <option value="ORDERING" {{ $table->status === 'ORDERING' ? 'selected' : '' }}>🟡 Ordering (In Progress)</option>
                                <option value="BILLING" {{ $table->status === 'BILLING' ? 'selected' : '' }}>🟠 Billing (Guest Check)</option>
                                <option value="RESERVED" {{ $table->status === 'RESERVED' ? 'selected' : '' }}>🟣 Reserved</option>
                                <option value="OUT_OF_SERVICE" {{ $table->status === 'OUT_OF_SERVICE' ? 'selected' : '' }}>⚪ Out of Service</option>
                            </select>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="table-box-actions">
                            <button type="button" 
                                class="btn-qr-view btn-show-qr" 
                                data-id="{{ $table->id }}"
                                data-number="{{ $table->table_number }}"
                                data-url="{{ $table->getOrderUrl() }}"
                                data-qr="{{ $table->getQrCodeDataUri() }}"
                                data-area="{{ $table->floor_area }}"
                                data-capacity="{{ $table->seating_capacity }}"
                                data-print="{{ route('admin.tables.print-stand', $table) }}"
                                data-regen="{{ route('admin.tables.regenerateQr', $table) }}">
                                <i class="ti ti-qrcode"></i> QR Code
                            </button>

                            <div class="action-icons-group">
                                <button type="button" 
                                    class="btn-icon-action btn-transfer-table" 
                                    title="Change / Move Table"
                                    data-source-id="{{ $table->id }}"
                                    data-source-number="{{ $table->table_number }}"
                                    data-source-name="{{ $table->name ?? '' }}"
                                    data-order-num="{{ $activeOrder ? $activeOrder->order_number : '' }}"
                                    data-order-total="{{ $activeOrder ? number_format($activeOrder->total_amount, 0) . ' MMK' : '' }}"
                                    onclick="openTransferTableModal(this)">
                                    <i class="ti ti-arrows-exchange"></i>
                                </button>
                                <a href="{{ route('admin.tables.print-stand', $table) }}" target="_blank" class="btn-icon-action" title="Print Stand"><i class="ti ti-printer"></i></a>
                                <button type="button" 
                                    class="btn-icon-action btn-edit-table" 
                                    title="Edit Table"
                                    data-id="{{ $table->id }}"
                                    data-number="{{ $table->table_number }}"
                                    data-name="{{ $table->name ?? '' }}"
                                    data-capacity="{{ $table->seating_capacity }}"
                                    data-area="{{ $table->floor_area }}"
                                    data-status="{{ $table->status }}"
                                    data-notes="{{ $table->notes ?? '' }}"
                                    data-update="{{ route('admin.tables.update', $table) }}">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this table?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-action btn-icon-delete" title="Delete Table"><i class="ti ti-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal 1: Add New Table Modal -->
<div class="modal-backdrop-custom" id="createTableModal">
    <div class="modal-dialog-custom">
        <form action="{{ route('admin.tables.store') }}" method="POST">
            @csrf
            <div class="modal-header-custom">
                <h3><i class="ti ti-table-plus text-primary"></i> Add New Dining Table</h3>
                <button type="button" class="btn-icon-action" onclick="closeCreateTableModal()"><i class="ti ti-x"></i></button>
            </div>
            <div class="modal-body-custom">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-custom">
                        <label for="table_number">Table Number *</label>
                        <input type="text" name="table_number" id="table_number" class="form-control-custom" placeholder="e.g. T-01, VIP-1" required>
                    </div>
                    <div class="form-group-custom">
                        <label for="seating_capacity">Seating Capacity *</label>
                        <input type="number" name="seating_capacity" id="seating_capacity" class="form-control-custom" min="1" max="50" value="4" required>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label for="floor_area">Dining Floor Area / Zone *</label>
                    <input type="text" name="floor_area" id="floor_area" class="form-control-custom" list="areaList" placeholder="e.g. Main Dining Hall, Outdoor Terrace, VIP Room" required value="Main Dining Hall">
                    <datalist id="areaList">
                        <option value="Main Dining Hall">
                        <option value="Outdoor Terrace">
                        <option value="VIP Room 1">
                        <option value="VIP Room 2">
                        <option value="Bar & Lounge">
                        <option value="Second Floor">
                    </datalist>
                </div>

                <div class="form-group-custom">
                    <label for="table_name">Table Label / Description (Optional)</label>
                    <input type="text" name="name" id="table_name" class="form-control-custom" placeholder="e.g. Window Corner Booth, Garden Table">
                </div>

                <div class="form-group-custom">
                    <label for="initial_status">Initial Status</label>
                    <select name="status" id="initial_status" class="form-control-custom">
                        <option value="VACANT">🟢 Vacant (Available)</option>
                        <option value="RESERVED">🟣 Reserved</option>
                        <option value="OUT_OF_SERVICE">⚪ Out of Service</option>
                    </select>
                </div>

                <div class="form-group-custom" style="margin-bottom: 0;">
                    <label for="notes">Notes / Instructions</label>
                    <textarea name="notes" id="notes" class="form-control-custom" rows="2" placeholder="e.g. Near kitchen pickup station, power outlet available"></textarea>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-modern-secondary" onclick="closeCreateTableModal()">Cancel</button>
                <button type="submit" class="btn-modern-primary"><i class="ti ti-check"></i> Create Table</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Table Modal -->
<div class="modal-backdrop-custom" id="editTableModal">
    <div class="modal-dialog-custom">
        <form id="editTableForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header-custom">
                <h3><i class="ti ti-edit text-primary"></i> Edit Dining Table</h3>
                <button type="button" class="btn-icon-action" onclick="closeEditTableModal()"><i class="ti ti-x"></i></button>
            </div>
            <div class="modal-body-custom">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-custom">
                        <label for="edit_table_number">Table Number *</label>
                        <input type="text" name="table_number" id="edit_table_number" class="form-control-custom" required>
                    </div>
                    <div class="form-group-custom">
                        <label for="edit_seating_capacity">Seating Capacity *</label>
                        <input type="number" name="seating_capacity" id="edit_seating_capacity" class="form-control-custom" min="1" max="50" required>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label for="edit_floor_area">Dining Floor Area / Zone *</label>
                    <input type="text" name="floor_area" id="edit_floor_area" class="form-control-custom" required>
                </div>

                <div class="form-group-custom">
                    <label for="edit_name">Table Label / Description</label>
                    <input type="text" name="name" id="edit_name" class="form-control-custom">
                </div>

                <div class="form-group-custom">
                    <label for="edit_status">Current Status</label>
                    <select name="status" id="edit_status" class="form-control-custom">
                        <option value="VACANT">🟢 Vacant</option>
                        <option value="OCCUPIED">🔵 Occupied</option>
                        <option value="ORDERING">🟡 Ordering</option>
                        <option value="BILLING">🟠 Billing</option>
                        <option value="RESERVED">🟣 Reserved</option>
                        <option value="OUT_OF_SERVICE">⚪ Out of Service</option>
                    </select>
                </div>

                <div class="form-group-custom" style="margin-bottom: 0;">
                    <label for="edit_notes">Notes</label>
                    <textarea name="notes" id="edit_notes" class="form-control-custom" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-modern-secondary" onclick="closeEditTableModal()">Cancel</button>
                <button type="submit" class="btn-modern-primary"><i class="ti ti-check"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Live QR Code & Acrylic Stand Preview Modal -->
<div class="modal-backdrop-custom" id="qrModal">
    <div class="modal-dialog-custom" style="max-width: 440px;">
        <div class="modal-header-custom">
            <div>
                <h3 id="qrModalTitle"><i class="ti ti-qrcode text-primary"></i> Table QR Code</h3>
                <span id="qrModalSubtitle" style="font-size: 0.75rem; color: var(--text-muted);">Main Dining Hall</span>
            </div>
            <button type="button" class="btn-icon-action" onclick="closeQrModal()"><i class="ti ti-x"></i></button>
        </div>
        <div class="modal-body-custom" style="padding-top: 1rem;">
            <!-- QR Display Box -->
            <div class="qr-preview-box">
                <div id="qrImageContainer">
                    <img id="qrImageElement" src="" alt="Table QR Code">
                </div>
                <div style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-top: 0.75rem;" id="qrTableNumberDisplay">
                    TABLE T-01
                </div>
                <div style="font-size: 0.8125rem; color: #64748b;" id="qrTableMetaDisplay">
                    4 Seats • Main Dining Hall
                </div>
            </div>

            <!-- Direct URL & Copy -->
            <div class="qr-url-pill">
                <i class="ti ti-link text-muted"></i>
                <input type="text" id="qrUrlInput" readonly>
                <button type="button" onclick="copyQrUrl()" class="btn-modern-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-radius: 6px;">
                    <i class="ti ti-copy"></i> Copy
                </button>
            </div>

            <!-- Actions inside Modal -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <a id="btnTestOrderUrl" href="#" target="_blank" class="btn-modern-secondary" style="text-decoration: none; text-align: center; font-size: 0.8125rem; font-weight: 600; padding: 0.55rem; border-radius: 8px;">
                    <i class="ti ti-external-link"></i> Test Customer View
                </a>
                <a id="btnPrintStandUrl" href="#" target="_blank" class="btn-modern-primary" style="text-decoration: none; text-align: center; font-size: 0.8125rem; font-weight: 600; padding: 0.55rem; border-radius: 8px;">
                    <i class="ti ti-printer"></i> Print Table Stand
                </a>
            </div>

            <div style="margin-top: 1rem; text-align: center;">
                <form id="regenerateQrForm" method="POST" onsubmit="return confirm('Regenerating will invalidate previous printed QR codes for this table. Are you sure?');">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: var(--text-muted); font-size: 0.75rem; text-decoration: underline; cursor: pointer;">
                        <i class="ti ti-refresh"></i> Regenerate QR Token (Security Reset)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Reproduce / Reprint Slip Modal (Zero Refresh) -->
<div class="modal-backdrop-custom" id="reprintModal">
    <div class="modal-dialog-custom">
        <form id="reprintSlipForm" method="POST">
            @csrf
            <div class="modal-header-custom">
                <div class="modal-header-title-box">
                    <h3 id="reprintModalTitle"><i class="ti ti-printer text-primary"></i> Reproduce / Reprint Slip</h3>
                    <span id="reprintModalSubtitle">Re-issue paper slip for kitchen or guest check</span>
                </div>
                <button type="button" class="btn-icon-action" onclick="closeReprintModal()" title="Close"><i class="ti ti-x"></i></button>
            </div>
            <div class="modal-body-custom">
                <div class="reprint-audit-notice">
                    <i class="ti ti-shield-alert" style="font-size: 1.25rem; flex-shrink: 0; margin-top: 0.1rem;"></i>
                    <div>
                        <strong>Reprint Audit Tracking:</strong> All re-issued slips will be stamped with <code>*** REPRINT #N (DUPLICATE) ***</code> and logged into the executive audit trail with your employee credentials.
                    </div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Select Slip to Reproduce *</label>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label class="reprint-option-card selected" onclick="selectSlipRadio(this)">
                            <input type="radio" name="slip_type" value="KITCHEN_CHIT" checked>
                            <span class="reprint-card-icon"><i class="ti ti-tools-kitchen-2"></i></span>
                            <div class="reprint-card-text">
                                <div class="reprint-card-title">Slip 1: Kitchen Order Chit</div>
                                <div class="reprint-card-desc">調理指示伝票 • Item quantities and cooking notes for chefs (No prices)</div>
                            </div>
                        </label>

                        <label class="reprint-option-card" onclick="selectSlipRadio(this)">
                            <input type="radio" name="slip_type" value="CUSTOMER_BILL">
                            <span class="reprint-card-icon"><i class="ti ti-receipt"></i></span>
                            <div class="reprint-card-text">
                                <div class="reprint-card-title">Slip 2: Customer Bill Slip</div>
                                <div class="reprint-card-desc">会計伝票 • Guest check with itemized MMK prices, taxes & counter checkout barcode</div>
                            </div>
                        </label>

                        <label class="reprint-option-card" onclick="selectSlipRadio(this)">
                            <input type="radio" name="slip_type" value="BOTH">
                            <span class="reprint-card-icon"><i class="ti ti-copy"></i></span>
                            <div class="reprint-card-text">
                                <div class="reprint-card-title">Both Slips (Continuous Dual Roll)</div>
                                <div class="reprint-card-desc">Prints Kitchen Chit + Customer Bill continuously with perforation cut guide</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom" for="reprint_reason">Reason for Reproduction (Audit Trail) *</label>
                    <select name="reason" id="reprint_reason" class="form-control-custom" required>
                        <option value="ပရင်တာ စက္ကူကုန်သွား၍ (Printer Paper Jam / Out)">ပရင်တာ စက္ကူကုန်သွား၍ (Printer Paper Jam / Out)</option>
                        <option value="စလစ် ပျောက်ဆုံးခြင်း သို့မဟုတ် ရေစိုသွားခြင်း (Slip Lost or Wet)">စလစ် ပျောက်ဆုံးခြင်း သို့မဟုတ် ရေစိုသွားခြင်း (Slip Lost or Wet)</option>
                        <option value="ဧည့်သည် စလစ်မိတ္တူထပ်မံတောင်းခံခြင်း (Customer Requested Copy)">ဧည့်သည် စလစ်မိတ္တူထပ်မံတောင်းခံခြင်း (Customer Requested Copy)</option>
                        <option value="မီးဖိုချောင် ပြန်လည်စစ်ဆေးရန် (Kitchen Pass Verification)">မီးဖိုချောင် ပြန်လည်စစ်ဆေးရန် (Kitchen Pass Verification)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-modern-secondary" onclick="closeReprintModal()">Cancel</button>
                <button type="submit" class="btn-modern-primary" id="btnSubmitReprint">
                    <i class="ti ti-printer"></i> Reproduce Slip Now
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 5: Change / Transfer Table Modal (Restaurant Side Only) -->
<div class="modal-backdrop-custom" id="transferTableModal">
    <div class="modal-dialog-custom">
        <form id="transferTableForm" onsubmit="submitTransferTableAjax(event)">
            @csrf
            <input type="hidden" id="transferSourceTableId" name="source_table_id">
            <div class="modal-header-custom">
                <div class="modal-header-title-box">
                    <h3 id="transferModalTitle" style="display: flex; align-items: center; gap: 0.5rem; margin: 0; line-height: 1.3;"><i class="ti ti-arrows-exchange text-primary" style="font-size: 1.25rem;"></i> Change / Transfer Dining Table</h3>
                    <span id="transferModalSubtitle" style="font-size: 0.775rem; color: var(--text-muted); display: block; margin-top: 0.2rem;">Move customer order and seating to an available table</span>
                </div>
                <button type="button" class="btn-icon-action" onclick="closeTransferTableModal()" title="Close"><i class="ti ti-x"></i></button>
            </div>
            <div class="modal-body-custom">
                <!-- Transfer Visual Step Flow (Current Table -> Changed Table) -->
                <div class="table-transfer-step-flow">
                    <div class="transfer-flow-node source-node">
                        <span class="transfer-flow-badge">CURRENT SEATING</span>
                        <div class="transfer-flow-title" id="transferCurrentTableDisplay">Table 2</div>
                        <div class="transfer-flow-sub" id="transferSourceAreaDisplay">Main Dining Hall</div>
                    </div>
                    <div class="transfer-flow-arrow">
                        <i class="ti ti-arrow-right"></i>
                        <span>CHANGE TO</span>
                    </div>
                    <div class="transfer-flow-node target-node" id="transferTargetNode">
                        <span class="transfer-flow-badge">CHANGED TABLE</span>
                        <div class="transfer-flow-title" id="transferTargetTableDisplay">Table ?</div>
                        <div class="transfer-flow-sub" id="transferTargetAreaDisplay">Enter table no. below</div>
                    </div>
                </div>

                <!-- Active Order Info -->
                <div id="transferCurrentOrderInfo" style="background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 10px; padding: 0.65rem 0.95rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span class="table-meta-item" style="background: rgba(59, 130, 246, 0.15); color: #2563eb; border-color: rgba(59, 130, 246, 0.3); font-weight: 700;" id="transferOrderNumberDisplay">
                            <i class="ti ti-receipt"></i> Active Order
                        </span>
                        <span style="font-size: 0.775rem; color: var(--text-muted);">will seamlessly move to new table</span>
                    </div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: #15803d;" id="transferOrderTotalDisplay"></div>
                </div>

                @php
                    $targetOptionsList = (isset($allTables) && $allTables->isNotEmpty()) ? $allTables : $tables;
                @endphp

                <!-- Target Table Input & Selection -->
                <div class="form-group-custom">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                        <label class="form-label-custom" for="target_table_number_input" style="margin-bottom: 0;">
                            <i class="ti ti-table text-primary"></i> Changed Table No. (1 - 47) *
                        </label>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Type number or select below</span>
                    </div>

                    <div style="display: flex; gap: 0.6rem; align-items: stretch;">
                        <div style="position: relative; width: 130px; flex-shrink: 0;">
                            <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); font-weight: 700; color: var(--text-muted); font-size: 0.825rem;">No.</span>
                            <input type="number" 
                                id="target_table_number_input" 
                                name="target_table_number" 
                                class="form-control-custom" 
                                placeholder="e.g. 5" 
                                min="1" 
                                max="47"
                                style="padding-left: 2.2rem; font-size: 1.15rem; font-weight: 800; text-align: center; height: 42px;"
                                oninput="onTargetTableNumberInput(this.value)">
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <select name="target_table_id" 
                                id="target_table_id" 
                                class="form-control-custom" 
                                style="height: 42px; font-weight: 600;" 
                                required 
                                onchange="onTargetTableSelectChange(this)">
                                <option value="">-- Or Choose From Available Tables (1 - 47) --</option>
                                @foreach($targetOptionsList as $targetOpt)
                                    @if($targetOpt->is_active)
                                        <option value="{{ $targetOpt->id }}" 
                                            data-table-number="{{ $targetOpt->table_number }}"
                                            data-status="{{ $targetOpt->status }}"
                                            data-area="{{ $targetOpt->floor_area }}" 
                                            data-capacity="{{ $targetOpt->seating_capacity }}"
                                            data-name="{{ $targetOpt->name }}"
                                            {{ $targetOpt->status !== 'VACANT' ? 'disabled' : '' }}>
                                            Table {{ $targetOpt->table_number }} {{ $targetOpt->name ? '('.$targetOpt->name.')' : '' }} • {{ $targetOpt->floor_area }} ({{ $targetOpt->seating_capacity }} Seats) {{ $targetOpt->status !== 'VACANT' ? '['.$targetOpt->status.']' : '[AVAILABLE]' }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Live Validation & Availability Feedback -->
                    <div id="targetTableStatusFeedback" style="margin-top: 0.45rem; font-size: 0.8125rem; font-weight: 600; display: none;"></div>

                    <!-- Quick Pick Vacant Tables List -->
                    <div style="margin-top: 0.65rem;">
                        <div style="font-size: 0.725rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.35rem;">
                            <i class="ti ti-bolt text-primary"></i> Quick Pick Vacant Tables:
                        </div>
                        <div class="vacant-table-chips-list" id="vacantTableChipsList">
                            @foreach($targetOptionsList as $targetOpt)
                                @if($targetOpt->status === 'VACANT' && $targetOpt->is_active)
                                    <button type="button" 
                                        class="btn-vacant-table-chip" 
                                        data-table-id="{{ $targetOpt->id }}"
                                        data-table-number="{{ $targetOpt->table_number }}"
                                        onclick="quickPickTableNumber('{{ $targetOpt->table_number }}', '{{ $targetOpt->id }}')">
                                        {{ $targetOpt->table_number }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Reason / Notes (Clean Single Input) -->
                <div class="form-group-custom" style="margin-bottom: 0.5rem;">
                    <label class="form-label-custom" for="transfer_reason_custom">
                        <i class="ti ti-notes text-muted" style="margin-right: 0.25rem;"></i> Reason for Table Change (Optional)
                    </label>
                    <input type="text" name="reason" id="transfer_reason_custom" class="form-control-custom" placeholder="Type reason or leave blank (e.g. window view, larger table)..." maxlength="255">
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-modern-secondary" onclick="closeTransferTableModal()">Cancel</button>
                <button type="submit" class="btn-modern-primary" id="btnSubmitTransfer">
                    <i class="ti ti-arrows-exchange"></i> Confirm Table Change
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Async Toast Notification Container -->
<div class="pos-toast" id="posToast">
    <i class="ti ti-circle-check" style="color: #22c55e; font-size: 1.25rem;"></i>
    <span id="posToastMessage">Action completed successfully.</span>
</div>
@endsection

@push('scripts')
<script>
    let currentReprintOrderId = null;

    // Toast helper
    function showToast(message) {
        const toast = document.getElementById('posToast');
        if (!toast) return;
        document.getElementById('posToastMessage').innerText = message;
        toast.classList.add('active');
        setTimeout(() => toast.classList.remove('active'), 3500);
    }

    // Modal Radio Selection
    function selectSlipRadio(cardEl) {
        document.querySelectorAll('.reprint-option-card').forEach(el => el.classList.remove('selected'));
        cardEl.classList.add('selected');
        const radio = cardEl.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    function openReprintModal(url, orderNum, tableNum, count, orderId) {
        currentReprintOrderId = orderId;
        document.getElementById('reprintSlipForm').action = url;
        document.getElementById('reprintModalTitle').innerHTML = '<i class="ti ti-printer text-primary"></i> Reproduce Slip: Table ' + tableNum;
        document.getElementById('reprintModalSubtitle').innerText = 'Order #' + orderNum + ' • Previous Reprints: ' + count;
        document.getElementById('reprintModal').classList.add('active');
    }

    function closeReprintModal() {
        document.getElementById('reprintModal').classList.remove('active');
    }

    // Delegate click on Reprint button
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-reprint-slip');
        if (btn) {
            openReprintModal(
                btn.dataset.reprintUrl,
                btn.dataset.number,
                btn.dataset.table,
                btn.dataset.count,
                btn.dataset.id
            );
        }
    });

    // 1. ASYNC REPRINT FORM SUBMIT (ZERO PAGE REFRESH)
    document.getElementById('reprintSlipForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const url = form.action;
        const submitBtn = document.getElementById('btnSubmitReprint');
        const originalHtml = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Processing...';

        const formData = new FormData(form);

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            closeReprintModal();
            showToast(data.message || 'Slip reproduced successfully!');

            // Open thermal print window asynchronously
            if (data.target_url) {
                window.open(data.target_url, '_blank');
            }

            // Update reprint count badge in DOM without reloading page
            if (currentReprintOrderId) {
                let badge = document.getElementById('reprint-badge-' + currentReprintOrderId);
                if (!badge) {
                    const orderBox = document.querySelector('[data-id="' + currentReprintOrderId + '"]')?.closest('.table-order-box');
                    if (orderBox) {
                        badge = document.createElement('div');
                        badge.className = 'reprint-badge-tag';
                        badge.id = 'reprint-badge-' + currentReprintOrderId;
                        const header = orderBox.querySelector('.table-order-box-header');
                        if (header) header.after(badge);
                    }
                }
                if (badge) {
                    badge.innerHTML = '<i class="ti ti-printer"></i> REPRINT #' + (data.reprint_count || 1) + ' (DUPLICATE)';
                }
                const reprintBtn = document.querySelector('.btn-reprint-slip[data-id="' + currentReprintOrderId + '"]');
                if (reprintBtn) {
                    reprintBtn.dataset.count = data.reprint_count;
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to reprint slip. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        });
    });

    // Toggle table active order items list on clicking OCCUPIED badge
    function toggleTableOrderBox(target) {
        let tableId = target;
        if (typeof target === 'object' && target !== null) {
            tableId = target.dataset.tableId || target.getAttribute('data-table-id');
        }
        const box = document.getElementById('table-order-box-' + tableId);
        const badge = document.getElementById('table-badge-' + tableId);
        const card = document.getElementById('table-card-' + tableId);
        const statusWrap = document.getElementById('table-status-wrap-' + tableId);
        if (!box) {
            showToast('No active order items found for this table.');
            return;
        }

        const isVisible = box.classList.contains('is-visible');
        if (isVisible) {
            box.classList.remove('is-visible');
            if (badge) badge.classList.remove('is-active-open');
            if (card) card.classList.remove('order-open');
            if (statusWrap) statusWrap.style.display = '';
        } else {
            box.classList.add('is-visible');
            if (badge) badge.classList.add('is-active-open');
            if (card) card.classList.add('order-open');
            if (statusWrap) statusWrap.style.display = 'none';
        }
    }

    // 2. ASYNC TABLE STATUS UPDATE (ZERO PAGE REFRESH)
    function updateTableStatusAjax(selectEl) {
        const tableId = selectEl.dataset.tableId;
        const tableNumber = selectEl.dataset.tableNumber;
        const url = selectEl.dataset.url;
        const newStatus = selectEl.value;
        const prevStatus = selectEl.getAttribute('data-prev') || selectEl.value;

        selectEl.disabled = true;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            selectEl.setAttribute('data-prev', newStatus);
            
            // Update status badge on card
            const badge = document.getElementById('table-badge-' + tableId);
            if (badge) {
                badge.className = 'status-badge status-' + newStatus.toLowerCase();
                const box = document.getElementById('table-order-box-' + tableId);
                if (box) {
                    badge.classList.add('status-badge-toggle');
                    if (box.classList.contains('is-visible')) {
                        badge.classList.add('is-active-open');
                    }
                    badge.onclick = function() { toggleTableOrderBox(this); };
                    badge.title = 'Click to view/hide order items';
                    badge.innerHTML = '<span class="status-dot"></span> ' + newStatus + ' <span class="badge-chevron"><i class="ti ti-chevron-down" id="order-arrow-' + tableId + '"></i></span>';
                } else {
                    badge.onclick = null;
                    badge.title = '';
                    badge.innerHTML = '<span class="status-dot"></span> ' + newStatus;
                }
            }

            // Also update top accent border on table card
            const card = document.getElementById('table-card-' + tableId);
            if (card) {
                card.className = card.className.replace(/\bstatus-box-[a-z_]+\b/g, '').trim() + ' status-box-' + newStatus.toLowerCase();
            }

            // Update KPI metrics if returned
            if (data.metrics) {
                updateFloorMetrics(data.metrics);
            }

            showToast(data.message || `Table ${tableNumber} status set to ${newStatus}`);
        })
        .catch(err => {
            console.error(err);
            selectEl.value = prevStatus;
            alert('Failed to update table status.');
        })
        .finally(() => {
            selectEl.disabled = false;
        });
    }

    function updateFloorMetrics(metrics) {
        if (!metrics) return;
        const total = document.getElementById('kpiTotalTables');
        const vacant = document.getElementById('kpiVacantTables');
        const occupied = document.getElementById('kpiOccupiedTables');
        const billing = document.getElementById('kpiBillingTables');
        const capacity = document.getElementById('kpiCapacity');

        if (total && metrics.total_tables !== undefined) total.innerText = metrics.total_tables;
        if (vacant && metrics.vacant_tables !== undefined) vacant.innerText = metrics.vacant_tables;
        if (occupied && metrics.occupied_tables !== undefined) occupied.innerText = metrics.occupied_tables;
        if (billing && metrics.billing_tables !== undefined) billing.innerText = metrics.billing_tables;
        if (capacity && metrics.total_capacity !== undefined) capacity.innerText = metrics.total_capacity;
    }

    // Create Table Modal
    function openCreateTableModal() {
        document.getElementById('createTableModal').classList.add('active');
        document.getElementById('table_number').focus();
    }
    function closeCreateTableModal() {
        document.getElementById('createTableModal').classList.remove('active');
    }

    // Edit Table Modal
    function openEditTableModal(id, number, name, capacity, area, status, notes, updateUrl) {
        document.getElementById('editTableForm').action = updateUrl;
        document.getElementById('edit_table_number').value = number;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_seating_capacity').value = capacity;
        document.getElementById('edit_floor_area').value = area;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_notes').value = notes;
        document.getElementById('editTableModal').classList.add('active');
    }
    function closeEditTableModal() {
        document.getElementById('editTableModal').classList.remove('active');
    }

    // QR Code Modal
    function showQrModal(id, number, orderUrl, qrDataUri, area, capacity, printStandUrl, regenUrl) {
        document.getElementById('qrModalTitle').innerHTML = '<i class="ti ti-qrcode text-primary"></i> ' + number + ' QR Code';
        document.getElementById('qrModalSubtitle').innerText = area + ' • ' + capacity + ' Seats';
        document.getElementById('qrImageElement').src = qrDataUri;
        document.getElementById('qrTableNumberDisplay').innerText = 'TABLE ' + number;
        document.getElementById('qrTableMetaDisplay').innerText = capacity + ' Seats • ' + area;
        document.getElementById('qrUrlInput').value = orderUrl;
        document.getElementById('btnTestOrderUrl').href = orderUrl;
        document.getElementById('btnPrintStandUrl').href = printStandUrl;
        document.getElementById('regenerateQrForm').action = regenUrl;
        document.getElementById('qrModal').classList.add('active');
    }
    function closeQrModal() {
        document.getElementById('qrModal').classList.remove('active');
    }

    function copyQrUrl() {
        const input = document.getElementById('qrUrlInput');
        input.select();
        navigator.clipboard.writeText(input.value).then(() => {
            alert('Table QR Order Link copied to clipboard!');
        });
    }

    // Modal 5: Table Change / Transfer Functions
    function openTransferTableModal(elOrId, tableNumber, tableName, orderNum, orderTotal) {
        let sourceId = elOrId;
        let sourceNum = tableNumber;
        let sName = tableName;
        let oNum = orderNum;
        let oTotal = orderTotal;

        if (typeof elOrId === 'object' && elOrId !== null) {
            sourceId = elOrId.dataset.sourceId;
            sourceNum = elOrId.dataset.sourceNumber;
            sName = elOrId.dataset.sourceName;
            oNum = elOrId.dataset.orderNum;
            oTotal = elOrId.dataset.orderTotal;
        }

        const sourceInput = document.getElementById('transferSourceTableId');
        sourceInput.value = sourceId;
        sourceInput.setAttribute('data-source-num', sourceNum);
        document.getElementById('transferCurrentTableDisplay').innerText = 'Table ' + sourceNum;
        document.getElementById('transferSourceAreaDisplay').innerText = sName || 'Current Seating';
        
        // Reset target selection state
        const numInput = document.getElementById('target_table_number_input');
        if (numInput) {
            numInput.value = '';
        }
        
        const targetNode = document.getElementById('transferTargetNode');
        if (targetNode) {
            targetNode.classList.remove('has-target', 'has-error');
        }
        document.getElementById('transferTargetTableDisplay').innerText = 'Table ?';
        document.getElementById('transferTargetAreaDisplay').innerText = 'Enter table no. below';

        const feedback = document.getElementById('targetTableStatusFeedback');
        if (feedback) {
            feedback.style.display = 'none';
            feedback.innerHTML = '';
        }

        const orderDisplay = document.getElementById('transferOrderNumberDisplay');
        const totalDisplay = document.getElementById('transferOrderTotalDisplay');
        if (oNum) {
            orderDisplay.innerHTML = '<i class="ti ti-receipt"></i> #' + oNum;
            orderDisplay.style.display = 'inline-flex';
            totalDisplay.innerText = oTotal || '';
            totalDisplay.style.display = 'block';
        } else {
            orderDisplay.style.display = 'none';
            totalDisplay.style.display = 'none';
        }

        // Filter out source table in dropdown
        const select = document.getElementById('target_table_id');
        for (let opt of select.options) {
            if (opt.value == sourceId || opt.dataset.tableNumber == sourceNum) {
                opt.disabled = true;
                opt.style.display = 'none';
            } else {
                opt.disabled = opt.dataset.status && opt.dataset.status !== 'VACANT';
                opt.style.display = '';
            }
        }
        select.value = '';

        // Reset quick pick chips
        document.querySelectorAll('.btn-vacant-table-chip').forEach(chip => {
            chip.classList.remove('is-selected');
            if (chip.dataset.tableNumber == sourceNum) {
                chip.style.display = 'none';
            } else {
                chip.style.display = '';
            }
        });

        document.getElementById('transfer_reason_custom').value = '';

        document.getElementById('transferTableModal').classList.add('active');

        // Auto-focus changed table number input for fast typing
        setTimeout(() => {
            if (numInput) numInput.focus();
        }, 150);
    }

    function closeTransferTableModal() {
        document.getElementById('transferTableModal').classList.remove('active');
    }

    function onTargetTableNumberInput(typedNum) {
        const val = (typedNum || '').trim();
        const sourceNum = document.getElementById('transferSourceTableId').getAttribute('data-source-num') || '';
        const select = document.getElementById('target_table_id');
        const feedback = document.getElementById('targetTableStatusFeedback');
        const targetNode = document.getElementById('transferTargetNode');
        const targetTitle = document.getElementById('transferTargetTableDisplay');
        const targetSub = document.getElementById('transferTargetAreaDisplay');

        // Sync quick pick chip state
        document.querySelectorAll('.btn-vacant-table-chip').forEach(c => {
            c.classList.toggle('is-selected', c.dataset.tableNumber === val);
        });

        if (!val) {
            select.value = '';
            if (feedback) feedback.style.display = 'none';
            if (targetNode) targetNode.classList.remove('has-target', 'has-error');
            if (targetTitle) targetTitle.innerText = 'Table ?';
            if (targetSub) targetSub.innerText = 'Enter table no. below';
            return;
        }

        if (val === sourceNum) {
            select.value = '';
            if (feedback) {
                feedback.style.display = 'block';
                feedback.style.color = '#ef4444';
                feedback.innerHTML = '<i class="ti ti-alert-triangle"></i> Cannot change to the current table (Table ' + val + '). Please enter a different table number.';
            }
            if (targetNode) {
                targetNode.classList.remove('has-target');
                targetNode.classList.add('has-error');
            }
            if (targetTitle) targetTitle.innerText = 'Table ' + val;
            if (targetSub) targetSub.innerText = 'Same as current table';
            return;
        }

        // Find table option with this table_number
        let matchedOpt = null;
        for (let opt of select.options) {
            if (opt.dataset && opt.dataset.tableNumber === val) {
                matchedOpt = opt;
                break;
            }
        }

        if (!matchedOpt) {
            select.value = '';
            if (feedback) {
                feedback.style.display = 'block';
                feedback.style.color = '#ef4444';
                feedback.innerHTML = '<i class="ti ti-alert-circle"></i> Table ' + val + ' was not found. Please enter a table number between 1 and 47.';
            }
            if (targetNode) {
                targetNode.classList.remove('has-target');
                targetNode.classList.add('has-error');
            }
            if (targetTitle) targetTitle.innerText = 'Table ' + val;
            if (targetSub) targetSub.innerText = 'Table not found (1-47)';
            return;
        }

        const status = matchedOpt.dataset.status;
        const area = matchedOpt.dataset.area || '';
        const cap = matchedOpt.dataset.capacity || '';
        const name = matchedOpt.dataset.name ? ' (' + matchedOpt.dataset.name + ')' : '';

        if (status !== 'VACANT') {
            select.value = '';
            if (feedback) {
                feedback.style.display = 'block';
                feedback.style.color = '#f59e0b';
                feedback.innerHTML = '<i class="ti ti-lock"></i> Table ' + val + ' is currently <strong>' + status + '</strong>. Please choose an available VACANT table.';
            }
            if (targetNode) {
                targetNode.classList.remove('has-target');
                targetNode.classList.add('has-error');
            }
            if (targetTitle) targetTitle.innerText = 'Table ' + val;
            if (targetSub) targetSub.innerText = 'Status: ' + status + ' (Not Available)';
            return;
        }

        // Table is valid and VACANT
        select.value = matchedOpt.value;
        if (feedback) {
            feedback.style.display = 'block';
            feedback.style.color = '#15803d';
            feedback.innerHTML = '<i class="ti ti-circle-check"></i> Table ' + val + name + ' is <strong>Available (VACANT)</strong> • ' + area + ' (' + cap + ' Seats)';
        }
        if (targetNode) {
            targetNode.classList.remove('has-error');
            targetNode.classList.add('has-target');
        }
        if (targetTitle) targetTitle.innerText = 'Table ' + val;
        if (targetSub) targetSub.innerText = area + ' (' + cap + ' Seats) • READY';
    }

    function onTargetTableSelectChange(selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const numInput = document.getElementById('target_table_number_input');
        if (selectedOpt && selectedOpt.dataset && selectedOpt.dataset.tableNumber) {
            numInput.value = selectedOpt.dataset.tableNumber;
            onTargetTableNumberInput(selectedOpt.dataset.tableNumber);
        } else {
            numInput.value = '';
            onTargetTableNumberInput('');
        }
    }

    function quickPickTableNumber(tableNum, tableId) {
        const numInput = document.getElementById('target_table_number_input');
        if (numInput) {
            numInput.value = tableNum;
            onTargetTableNumberInput(tableNum);
        }
    }

    function submitTransferTableAjax(e) {
        e.preventDefault();
        const sourceId = document.getElementById('transferSourceTableId').value;
        let targetId = document.getElementById('target_table_id').value;
        const targetNum = (document.getElementById('target_table_number_input').value || '').trim();
        const reason = document.getElementById('transfer_reason_custom').value;

        if (!targetId && !targetNum) {
            alert('Please enter or select an available target table number (1 - 47).');
            return;
        }

        const btn = document.getElementById('btnSubmitTransfer');
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Moving Table...';

        const url = `/admin/tables/${sourceId}/transfer`;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                target_table_id: targetId,
                target_table_number: targetNum,
                reason: reason
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            if (status !== 200) {
                const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join("\n") : 'Failed to transfer table.');
                alert(errMsg);
                return;
            }

            closeTransferTableModal();
            showToast(body.message || 'Table transferred successfully!');

            // Smooth reload after 500ms so full floor plan reflects moved order
            setTimeout(() => {
                window.location.reload();
            }, 500);
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while moving the table.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-arrows-exchange"></i> Confirm Table Change';
        });
    }

    // Close modals on clicking outside & handle data-attributes
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop-custom')) {
            e.target.classList.remove('active');
        }

        const qrBtn = e.target.closest('.btn-show-qr');
        if (qrBtn) {
            showQrModal(
                qrBtn.dataset.id,
                qrBtn.dataset.number,
                qrBtn.dataset.url,
                qrBtn.dataset.qr,
                qrBtn.dataset.area,
                qrBtn.dataset.capacity,
                qrBtn.dataset.print,
                qrBtn.dataset.regen
            );
        }

        const editBtn = e.target.closest('.btn-edit-table');
        if (editBtn) {
            openEditTableModal(
                editBtn.dataset.id,
                editBtn.dataset.number,
                editBtn.dataset.name,
                editBtn.dataset.capacity,
                editBtn.dataset.area,
                editBtn.dataset.status,
                editBtn.dataset.notes,
                editBtn.dataset.update
            );
        }
    });

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTransferTableModal();
            closeReprintModal();
            closeCreateTableModal();
            closeEditTableModal();
            closeQrModal();
        }
    });
</script>
@endpush
