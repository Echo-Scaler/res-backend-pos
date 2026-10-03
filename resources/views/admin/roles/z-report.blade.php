<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Shift Z-Report - {{ $session->terminal_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mada:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Mada", monospace, sans-serif;
            background: #e2e8f0;
            display: flex;
            justify-content: center;
            padding: 20px 10px;
            color: #000;
        }

        .report-container {
            width: 80mm;
            background: #fff;
            padding: 15px 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-size: 12px;
            line-height: 1.35;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }

        .restaurant-title {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .report-badge {
            display: inline-block;
            border: 2px solid #000;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            margin: 6px 0;
            letter-spacing: 0.05em;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 6px 0;
        }

        .double-divider {
            border-bottom: 2px solid #000;
            margin: 8px 0;
        }

        .row-info {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .section-header {
            font-weight: 700;
            font-size: 11px;
            margin: 8px 0 4px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .variance-box {
            border: 1px solid #000;
            padding: 6px;
            margin: 8px 0;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
        }

        .variance-box.exact { background: #f0fdf4; }
        .variance-box.over { background: #eff6ff; }
        .variance-box.short { background: #fff1f2; }

        .sign-area {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 15px;
            font-size: 10px;
        }

        .sign-box {
            text-align: center;
            width: 45%;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        .no-print-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            background: rgba(15, 23, 42, 0.9);
            padding: 10px 16px;
            border-radius: 9999px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            z-index: 1000;
        }

        .btn-action {
            background: #9ec63b;
            color: #0b0f17;
            font-family: 'Mada', sans-serif;
            font-weight: 700;
            font-size: 13px;
            border: none;
            padding: 6px 14px;
            border-radius: 9999px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-action.back-btn {
            background: #334155;
            color: #fff;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .report-container {
                width: 80mm;
                box-shadow: none;
                padding: 4mm;
                margin: 0 auto;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="report-container">
    <div class="text-center">
        <div class="restaurant-title">{{ $session->restaurant->name ?? 'RESTAURANT POS' }}</div>
        <div style="font-size: 11px; color: #475569;">{{ $session->restaurant->address ?? 'Yangon, Myanmar' }}</div>
        <div class="report-badge">SHIFT Z-REPORT / နေ့ချုပ်စာရင်း</div>
    </div>

    <div class="divider"></div>

    <div class="row-info">
        <span>Terminal:</span>
        <span class="font-bold">{{ $session->terminal_code }}</span>
    </div>
    <div class="row-info">
        <span>Cashier:</span>
        <span class="font-bold">{{ $session->cashier ? $session->cashier->name : 'N/A' }}</span>
    </div>
    <div class="row-info">
        <span>Shift Opened:</span>
        <span>{{ $session->opened_at->format('d-M-Y h:i A') }}</span>
    </div>
    <div class="row-info">
        <span>Shift Closed:</span>
        <span>{{ $session->closed_at ? $session->closed_at->format('d-M-Y h:i A') : now()->format('d-M-Y h:i A') }}</span>
    </div>

    <div class="double-divider"></div>

    {{-- 1. CASH DRAWER RECONCILIATION --}}
    <div class="section-header">1. Cash Drawer Float & Sales</div>
    <div class="row-info">
        <span>(+) Opening Float (အစပြုငွေ):</span>
        <span class="font-bold">{{ number_format($session->opening_float, 0) }} MMK</span>
    </div>
    <div class="row-info">
        <span>(+) Cash Sales (ငွေသားရောင်းရငွေ):</span>
        <span>{{ number_format($session->cash_sales, 0) }} MMK</span>
    </div>
    <div class="row-info">
        <span>(+) Cash In (ငွေသားထည့်သွင်းမှု):</span>
        <span>{{ number_format($session->cash_in, 0) }} MMK</span>
    </div>
    <div class="row-info">
        <span>(-) Cash Out (ငွေသားထုတ်ယူမှု):</span>
        <span>-{{ number_format($session->cash_out, 0) }} MMK</span>
    </div>

    <div class="divider"></div>

    <div class="row-info font-bold" style="font-size: 12px;">
        <span>(=) Expected Cash in Drawer:</span>
        <span>{{ number_format($session->expected_cash, 0) }} MMK</span>
    </div>
    <div class="row-info font-bold" style="font-size: 12px; margin-top: 3px;">
        <span>Actual Counted Cash:</span>
        <span>{{ number_format($session->closing_actual_cash ?? 0, 0) }} MMK</span>
    </div>

    @php
        $diff = $session->cash_difference ?? 0;
    @endphp
    <div class="variance-box {{ $diff === 0 ? 'exact' : ($diff > 0 ? 'over' : 'short') }}">
        @if ($diff === 0)
            VARIANCE: 0 MMK (EXACT MATCH / ညီမျှပါသည်)
        @elseif ($diff > 0)
            VARIANCE: +{{ number_format($diff, 0) }} MMK (OVER / ငွေပိုနေပါသည်)
        @else
            VARIANCE: {{ number_format($diff, 0) }} MMK (SHORTAGE / ငွေလိုနေပါသည်)
        @endif
    </div>

    {{-- 2. NON-CASH SALES & TOTAL TURNOVER --}}
    <div class="section-header">2. Total Sales Turnover</div>
    <div class="row-info">
        <span>Cash Sales Total:</span>
        <span>{{ number_format($session->cash_sales, 0) }} MMK</span>
    </div>
    <div class="row-info">
        <span>Digital Sales (KBZ/Wave/Card):</span>
        <span>{{ number_format($session->digital_sales, 0) }} MMK</span>
    </div>

    <div class="divider"></div>

    <div class="row-info font-bold" style="font-size: 12px;">
        <span>GROSS SHIFT REVENUE:</span>
        <span>{{ number_format($session->cash_sales + $session->digital_sales, 0) }} MMK</span>
    </div>

    {{-- 3. CASH MOVEMENTS AUDIT --}}
    @if ($session->transactions->isNotEmpty())
        <div class="section-header">3. Drawer Movements Log</div>
        @foreach ($session->transactions as $tx)
            <div class="row-info" style="font-size: 10px;">
                <span>[{{ $tx->type }}] {{ $tx->reason }}:</span>
                <span class="font-bold">{{ number_format($tx->amount, 0) }} MMK</span>
            </div>
        @endforeach
    @endif

    @if ($session->notes)
        <div class="divider"></div>
        <div style="font-size: 10px; color: #475569;">
            <strong>Notes:</strong> {{ $session->notes }}
        </div>
    @endif

    {{-- Signatures --}}
    <div class="sign-area">
        <div class="sign-box">
            Cashier Signature
        </div>
        <div class="sign-box">
            Manager Signature
        </div>
    </div>

    <div class="double-divider"></div>
    <div class="text-center" style="font-size: 9px; color: #64748b;">
        Generated by Antigravity POS Engine &bull; {{ now()->format('d-M-Y h:i:s A') }}
    </div>
</div>

<div class="no-print-bar">
    <button type="button" class="btn-action" onclick="window.print()">🖨️ Print Z-Report</button>
    <a href="{{ route('cashier.dashboard') }}" class="btn-action back-btn">Back to Register</a>
</div>

</body>
</html>
