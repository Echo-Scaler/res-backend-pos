<style>
    /* ==========================================================================
       MODERN EXPENSE DESIGN SYSTEM 2026 (MADA TYPOGRAPHY & OBSIDIAN SLATE PALETTE)
       ========================================================================== */
    .expense-portal {
        font-family: "Mada", sans-serif;
        color: var(--text-main);
        padding-bottom: 2.5rem;
    }

    /* Sub-Navigation Pill Bar */
    .expense-nav-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .nav-pill-track {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        background: var(--bg-card);
        padding: 0.35rem 0.45rem;
        border-radius: 14px;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        overflow-x: auto;
        max-width: 100%;
        -webkit-overflow-scrolling: touch;
    }

    .nav-pill-track::-webkit-scrollbar {
        height: 3px;
    }
    .nav-pill-track::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 4px;
    }

    .nav-pill-item {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1.15rem;
        border-radius: 10px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-pill-item i {
        font-size: 1.1rem;
        transition: transform 0.2s ease;
    }

    .nav-pill-item:hover {
        color: var(--text-main);
        background: var(--bg-hover);
        transform: translateY(-1px);
    }

    .nav-pill-item:hover i {
        transform: scale(1.1);
    }

    .nav-pill-item.active {
        background: linear-gradient(135deg, #8cb829 0%, #6f9520 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 14px rgba(111, 149, 32, 0.35) !important;
        border: 1px solid rgba(111, 149, 32, 0.4) !important;
    }

    .nav-pill-item.active i {
        color: #ffffff !important;
    }

    .nav-action-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Buttons */
    .btn-gradient-primary {
        background: linear-gradient(135deg, #9ec63b 0%, #7ea826 100%);
        color: #ffffff !important;
        padding: 0.65rem 1.35rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(158, 198, 59, 0.32);
    }

    .btn-gradient-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(158, 198, 59, 0.42);
        filter: brightness(1.05);
    }

    .btn-gradient-primary:active {
        transform: translateY(0);
    }

    .btn-modern-secondary {
        background: var(--bg-card);
        color: var(--text-main) !important;
        border: 1px solid var(--border-color);
        padding: 0.65rem 1.25rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        cursor: pointer;
    }

    .btn-modern-secondary:hover {
        background: var(--bg-hover);
        border-color: var(--primary);
        color: var(--primary-hover) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .btn-modern-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444 !important;
        border: 1px solid rgba(239, 68, 68, 0.25);
        padding: 0.65rem 1.25rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-modern-danger:hover {
        background: #ef4444;
        color: #ffffff !important;
        border-color: #ef4444;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
        transform: translateY(-1px);
    }

    .btn-action-emerald {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        padding: 0.65rem 1.35rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .btn-action-emerald:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(16, 185, 129, 0.45);
        filter: brightness(1.05);
    }

    .btn-action-rose {
        background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        color: #ffffff !important;
        padding: 0.65rem 1.35rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(244, 63, 94, 0.35);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .btn-action-rose:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(244, 63, 94, 0.45);
        filter: brightness(1.05);
    }

    .btn-action-slate {
        background: var(--bg-card);
        color: var(--text-muted) !important;
        border: 1px solid var(--border-color);
        padding: 0.65rem 1.15rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .btn-action-slate:hover {
        background: var(--bg-hover);
        color: #ef4444 !important;
        border-color: #ef4444;
        transform: translateY(-1px);
    }

    /* Glass Cards */
    .glass-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        position: relative;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.07);
        border-color: rgba(158, 198, 59, 0.35);
    }

    /* Modern KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 1.35rem 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }

    .kpi-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 4px;
        background: var(--border-color);
        border-radius: 4px 0 0 4px;
    }

    .kpi-card.kpi-lime::after { background: linear-gradient(180deg, #9ec63b, #7ea826); }
    .kpi-card.kpi-blue::after { background: linear-gradient(180deg, #38bdf8, #0284c7); }
    .kpi-card.kpi-amber::after { background: linear-gradient(180deg, #fbbf24, #d97706); }
    .kpi-card.kpi-emerald::after { background: linear-gradient(180deg, #34d399, #059669); }
    .kpi-card.kpi-purple::after { background: linear-gradient(180deg, #a855f7, #7e22ce); }
    .kpi-card.kpi-rose::after { background: linear-gradient(180deg, #f43f5e, #e11d48); }

    .kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .kpi-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .kpi-icon-orb {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: var(--bg-hover);
        color: var(--text-muted);
    }

    .kpi-lime .kpi-icon-orb { background: rgba(158, 198, 59, 0.15); color: #9ec63b; }
    .kpi-blue .kpi-icon-orb { background: rgba(56, 189, 248, 0.15); color: #0284c7; }
    .kpi-amber .kpi-icon-orb { background: rgba(251, 191, 36, 0.15); color: #d97706; }
    .kpi-emerald .kpi-icon-orb { background: rgba(52, 211, 153, 0.15); color: #059669; }
    .kpi-purple .kpi-icon-orb { background: rgba(168, 85, 247, 0.15); color: #9333ea; }
    .kpi-rose .kpi-icon-orb { background: rgba(244, 63, 94, 0.15); color: #e11d48; }

    .kpi-value {
        font-size: 1.625rem;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    .kpi-subtitle {
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* Modern Table */
    .table-container {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02);
    }

    .table-responsive-clean {
        width: 100%;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .table-responsive-clean::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0 !important;
        font-size: 0.875rem;
    }

    .modern-table thead {
        background: var(--bg-hover);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .modern-table th {
        padding: 0.8rem 0.85rem;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .modern-table td {
        padding: 0.8rem 0.85rem;
        border-bottom: 1px solid var(--border-subtle);
        color: var(--text-main);
        vertical-align: middle;
        transition: background 0.15s ease;
    }

    /* Actions Column - Sticky Right with Ample Breathing Space */
    .modern-table th.actions-col,
    .modern-table td.actions-col {
        position: sticky;
        right: 0;
        z-index: 2;
        padding-right: 1.25rem !important;
        padding-left: 0.75rem !important;
        min-width: 145px !important;
        width: 145px !important;
        text-align: center !important;
    }

    .modern-table thead th.actions-col {
        background: var(--bg-hover) !important;
        box-shadow: -4px 0 8px rgba(0, 0, 0, 0.04);
    }

    .modern-table tbody td.actions-col {
        background: var(--bg-card) !important;
        box-shadow: -4px 0 8px rgba(0, 0, 0, 0.04);
    }

    .modern-table tbody tr:hover td.actions-col {
        background: var(--bg-hover) !important;
    }

    /* Vendor Cell Layout Components */
    .vendor-info-cell {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-width: 210px;
    }

    .vendor-avatar {
        width: 38px;
        height: 38px;
        min-width: 38px;
        max-width: 38px;
        flex-shrink: 0;
        border-radius: 10px;
        background: rgba(158, 198, 59, 0.15);
        color: #7ea826;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .vendor-info-text {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        min-width: 0;
    }

    .vendor-name-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main);
        line-height: 1.35;
        margin: 0;
    }

    .modern-table tbody tr:hover td {
        background: var(--bg-hover);
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status Pills with Pulsing Dots */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        position: relative;
    }

    .status-dot::after {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 50%;
        animation: pulse-glow 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
        opacity: 0.6;
    }

    @keyframes pulse-glow {
        0%, 100% { transform: scale(1); opacity: 0.6; }
        50% { transform: scale(1.6); opacity: 0; }
    }

    .status-draft { background: rgba(148, 163, 184, 0.15); color: #64748b; }
    .status-draft .status-dot { background: #94a3b8; }
    .status-draft .status-dot::after { background: #94a3b8; }

    .status-pending { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .status-pending .status-dot { background: #f59e0b; }
    .status-pending .status-dot::after { background: #f59e0b; }

    .status-approved { background: rgba(14, 165, 233, 0.15); color: #0284c7; }
    .status-approved .status-dot { background: #0ea5e9; }
    .status-approved .status-dot::after { background: #0ea5e9; }

    .status-paid { background: rgba(16, 185, 129, 0.15); color: #059669; }
    .status-paid .status-dot { background: #10b981; }
    .status-paid .status-dot::after { background: #10b981; }

    .status-rejected { background: rgba(239, 68, 68, 0.15); color: #dc2626; }
    .status-rejected .status-dot { background: #ef4444; }
    .status-rejected .status-dot::after { background: #ef4444; }

    .status-void { background: rgba(100, 116, 139, 0.15); color: #475569; }
    .status-void .status-dot { background: #64748b; }
    .status-void .status-dot::after { background: #64748b; }

    /* Modern Badges & Chips */
    .modern-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--text-main);
    }

    .modern-chip-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .badge-mono-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.75rem;
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        background: var(--bg-hover);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }

    /* Modern Forms & Inputs */
    .form-group-modern {
        margin-bottom: 1.25rem;
    }

    .form-label-modern {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.45rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .form-control-modern {
        width: 100%;
        background: var(--bg-body);
        border: 1.5px solid var(--border-color);
        color: var(--text-main);
        padding: 0.65rem 0.95rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-family: "Mada", sans-serif;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .form-control-modern:focus {
        border-color: var(--primary);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(158, 198, 59, 0.2);
    }

    .form-select-modern {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.85rem center;
        background-size: 1.15rem;
        padding-right: 2.5rem;
    }

    /* Modern Modals */
    .modern-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(11, 15, 23, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        padding: 1.5rem;
        animation: fadeIn 0.2s ease-out;
    }

    .modern-modal-dialog {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        width: 100%;
        max-width: 580px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px -8px rgba(0, 0, 0, 0.25);
        animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .modern-modal-header {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modern-modal-title {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modern-modal-close {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.35rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .modern-modal-close:hover {
        background: var(--bg-hover);
        color: var(--text-main);
    }

    .modern-modal-body {
        padding: 1.75rem;
    }

    .modern-modal-footer {
        padding: 1.25rem 1.75rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        background: var(--bg-hover);
        border-radius: 0 0 20px 20px;
    }

    .modal-alert-box {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: 12px;
        padding: 0.85rem 1.15rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: var(--text-main);
    }
    .modal-alert-box i {
        font-size: 1.25rem;
        color: #f59e0b;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .modal-summary-card {
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(16px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Modern Empty State */
    .empty-state-wrap {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-icon-orb {
        width: 72px;
        height: 72px;
        border-radius: 24px;
        background: linear-gradient(135deg, rgba(158, 198, 59, 0.15) 0%, rgba(126, 168, 38, 0.08) 100%);
        color: var(--primary);
        font-size: 2.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem auto;
        border: 1px solid rgba(158, 198, 59, 0.25);
    }

    .empty-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 0.5rem 0;
    }

    .empty-desc {
        color: var(--text-muted);
        font-size: 0.875rem;
        max-width: 420px;
        margin: 0 auto 1.5rem auto;
    }

    /* Color Swatch Radios */
    .color-swatch-list {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
        margin-top: 0.35rem;
    }

    .color-swatch-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .color-swatch-btn.active, .color-swatch-btn:hover {
        transform: scale(1.2);
        box-shadow: 0 0 0 2px var(--bg-card), 0 0 0 4px var(--primary);
    }

    /* Core Layout & Flex Spacing Utilities (Fixes any overlap between header buttons and cards) */
    .expense-header-wrap {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 1.25rem !important;
        margin-bottom: 2rem !important;
        width: 100% !important;
    }

    .expense-header-wrap .header-title-group h1 {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        margin: 0 0 0.35rem 0 !important;
        color: var(--text-main) !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.65rem !important;
    }

    .expense-header-wrap .header-title-group p {
        color: var(--text-muted) !important;
        margin: 0 !important;
        font-size: 0.875rem !important;
    }

    .expense-header-wrap .header-actions {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        flex-wrap: wrap !important;
    }

    .d-flex { display: flex !important; }
    .d-inline-flex { display: inline-flex !important; }
    .flex-column { flex-direction: column !important; }
    .flex-wrap { flex-wrap: wrap !important; }
    .justify-content-between { justify-content: space-between !important; }
    .justify-content-center { justify-content: center !important; }
    .justify-content-end { justify-content: flex-end !important; }
    .align-items-center { align-items: center !important; }
    .align-items-start { align-items: flex-start !important; }
    .gap-1 { gap: 0.25rem !important; }
    .gap-1-5 { gap: 0.375rem !important; }
    .gap-2 { gap: 0.5rem !important; }
    .gap-2-5 { gap: 0.625rem !important; }
    .gap-3 { gap: 1rem !important; }
    .gap-4 { gap: 1.5rem !important; }
    .m-0 { margin: 0 !important; }
    .mb-0 { margin-bottom: 0 !important; }
    .mb-1 { margin-bottom: 0.25rem !important; }
    .mb-2 { margin-bottom: 0.5rem !important; }
    .mb-3 { margin-bottom: 1rem !important; }
    .mb-4 { margin-bottom: 2rem !important; }
    .mt-1 { margin-top: 0.25rem !important; }
    .mt-2 { margin-top: 0.5rem !important; }
    .mt-3 { margin-top: 1rem !important; }
    .mt-4 { margin-top: 2rem !important; }
    .p-2 { padding: 0.5rem !important; }
    .p-3 { padding: 1rem !important; }
    .p-4 { padding: 1.5rem !important; }
    .pt-2 { padding-top: 0.5rem !important; }
    .pt-3 { padding-top: 1rem !important; }
    .border-top { border-top: 1px solid var(--border-color) !important; }
    .page-header-wrap {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 1.25rem !important;
        margin-bottom: 2rem !important;
    }
    .text-right { text-align: right !important; }
    .text-center { text-align: center !important; }
    .text-muted { color: var(--text-muted) !important; }
    .text-main { color: var(--text-main) !important; }
    .text-primary { color: var(--primary) !important; }
    .text-success { color: #10b981 !important; }
    .text-danger { color: #ef4444 !important; }
    .text-warning { color: #f59e0b !important; }
    .font-bold { font-weight: 700 !important; }
    .font-semibold { font-weight: 600 !important; }
    .font-medium { font-weight: 500 !important; }
    .font-mono { font-family: ui-monospace, SFMono-Regular, monospace !important; }

    /* Modern Pagination System */
    .pagination-wrap {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 0.75rem !important;
        padding: 0.95rem 1.35rem !important;
        border-top: 1px solid var(--border-color) !important;
        background: var(--bg-card) !important;
        border-bottom-left-radius: 16px !important;
        border-bottom-right-radius: 16px !important;
    }

    .pagination-meta {
        font-size: 0.8125rem !important;
        color: var(--text-muted) !important;
        font-weight: 500 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.25rem !important;
    }

    .pagination-meta strong {
        color: var(--text-main) !important;
        font-weight: 700 !important;
    }

    .modern-pagination-nav {
        display: flex !important;
        align-items: center !important;
    }

    .modern-pagination-list {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.35rem !important;
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .modern-pagination-list .page-item {
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    .modern-pagination-list .page-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 34px !important;
        height: 34px !important;
        padding: 0 0.5rem !important;
        border-radius: 8px !important;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        font-family: "Mada", sans-serif !important;
        color: var(--text-main) !important;
        background: var(--bg-body) !important;
        border: 1px solid var(--border-color) !important;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        user-select: none !important;
    }

    .modern-pagination-list .page-link:hover:not(.dots) {
        background: var(--bg-hover) !important;
        border-color: #8cb829 !important;
        color: #8cb829 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04) !important;
    }

    .modern-pagination-list .page-item.active .page-link {
        background: linear-gradient(135deg, #8cb829 0%, #6f9520 100%) !important;
        color: #ffffff !important;
        border-color: #8cb829 !important;
        font-weight: 700 !important;
        box-shadow: 0 3px 10px rgba(140, 184, 41, 0.35) !important;
        cursor: default !important;
        transform: none !important;
    }

    .modern-pagination-list .page-item.disabled .page-link {
        opacity: 0.35 !important;
        cursor: not-allowed !important;
        background: var(--bg-body) !important;
        border-color: var(--border-color) !important;
        color: var(--text-muted) !important;
        transform: none !important;
        box-shadow: none !important;
    }

    .modern-pagination-list .page-link.dots {
        border: none !important;
        background: transparent !important;
        cursor: default !important;
        color: var(--text-muted) !important;
        min-width: 22px !important;
    }

    .modern-pagination-list .page-arrow {
        font-size: 0.95rem !important;
    }
</style>
