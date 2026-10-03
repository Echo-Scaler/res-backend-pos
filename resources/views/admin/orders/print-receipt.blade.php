<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt - {{ $order->order_number }}</title>
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

        .receipt-container {
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
        .font-semibold { font-weight: 600; }

        .restaurant-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .receipt-meta {
            font-size: 11px;
            color: #475569;
            margin-bottom: 8px;
        }

        .receipt-type-pill {
            display: inline-block;
            border: 1px solid #000;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            margin: 6px 0;
            text-transform: uppercase;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 6px 0;
        }

        .double-divider {
            border-bottom: 2px solid #000;
            margin: 8px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        .items-table th {
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 0;
            border-bottom: 1px solid #000;
        }

        .items-table td {
            padding: 3px 0;
            font-size: 11px;
            vertical-align: top;
        }

        .calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .calc-row.grand-total {
            font-size: 14px;
            font-weight: 700;
            padding: 4px 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin: 6px 0;
        }

        .footer-note {
            font-size: 10px;
            text-align: center;
            color: #334155;
            margin-top: 10px;
            line-height: 1.4;
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

        .btn-action.close-btn {
            background: #334155;
            color: #fff;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .receipt-container {
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

<div class="receipt-container">
    {{-- Header --}}
    <div class="text-center">
        <div class="restaurant-title">{{ $order->restaurant->name ?? 'RESTAURANT POS' }}</div>
        <div class="receipt-meta">
            {{ $order->restaurant->address ?? 'Yangon, Myanmar' }}<br>
            Phone: {{ $order->restaurant->phone ?? '+95 9 12345678' }}<br>
            Commercial Tax ID: CT-MM-2026-9812
        </div>
        <div class="receipt-type-pill">OFFICIAL RECEIPT / ငွေလက်ခံဖြတ်ပိုင်း</div>
    </div>

    <div class="divider"></div>

    {{-- Order Information --}}
    <div class="info-row">
        <span>Order No:</span>
        <span class="font-bold">{{ $order->order_number }}</span>
    </div>
    <div class="info-row">
        <span>Table No:</span>
        <span class="font-bold">{{ $order->table_number }}</span>
    </div>
    <div class="info-row">
        <span>Date & Time:</span>
        <span>{{ $order->updated_at ? $order->updated_at->format('d-M-Y h:i A') : now()->format('d-M-Y h:i A') }}</span>
    </div>
    <div class="info-row">
        <span>Cashier:</span>
        <span>{{ $order->cashDrawerSession && $order->cashDrawerSession->cashier ? $order->cashDrawerSession->cashier->name : 'Counter Cashier' }}</span>
    </div>
    <div class="info-row">
        <span>Guests:</span>
        <span>{{ $order->guest_count }} Pax</span>
    </div>

    <div class="divider"></div>

    {{-- Itemized Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Item</th>
                <th style="width: 15%; text-align: center;">Qty</th>
                <th style="width: 35%; text-align: right;">Amount (MMK)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        {{ $item->item_name }}
                        @if ($item->special_notes)
                            <div style="font-size: 9px; color: #64748b;">* {{ $item->special_notes }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    {{-- Totals --}}
    <div class="calc-row">
        <span>Subtotal:</span>
        <span>{{ number_format($order->subtotal, 0) }} MMK</span>
    </div>

    @if ($order->discount_amount > 0)
        <div class="calc-row">
            <span>Discount Deducted:</span>
            <span>-{{ number_format($order->discount_amount, 0) }} MMK</span>
        </div>
    @endif

    <div class="calc-row">
        <span>Commercial Tax (5%):</span>
        <span>{{ number_format($order->tax_amount, 0) }} MMK</span>
    </div>

    @if (($order->service_charge ?? 0) > 0)
        <div class="calc-row">
            <span>Service Charge:</span>
            <span>{{ number_format($order->service_charge, 0) }} MMK</span>
        </div>
    @endif

    <div class="calc-row grand-total">
        <span>TOTAL PAID:</span>
        <span>{{ number_format($order->total_amount, 0) }} MMK</span>
    </div>

    {{-- Payment Details --}}
    @php
        $payment = $order->payments->last();
    @endphp
    @if ($payment)
        <div class="info-row font-semibold">
            <span>Payment Method:</span>
            <span>{{ $payment->payment_method }}</span>
        </div>
        @if ($payment->payment_method === 'CASH')
            <div class="info-row">
                <span>Cash Tendered:</span>
                <span>{{ number_format($payment->tendered_amount ?: $order->total_amount, 0) }} MMK</span>
            </div>
            <div class="info-row font-bold">
                <span>Change Returned:</span>
                <span>{{ number_format($payment->change_amount, 0) }} MMK</span>
            </div>
        @elseif ($payment->reference_no)
            <div class="info-row">
                <span>Ref / Txn ID:</span>
                <span>{{ $payment->reference_no }}</span>
            </div>
        @endif
    @endif

    <div class="double-divider"></div>

    {{-- Footer --}}
    <div class="footer-note">
        <strong>ကျေးဇူးတင်ပါသည်၊ နောက်တစ်ဖန် ပြန်လည်ကြွရောက်ပါရန် ဖိတ်ခေါ်အပ်ပါသည်။</strong><br>
        Thank you for dining with us! Please come again.<br>
        <span style="font-size: 9px; color: #94a3b8;">Printed by POS Terminal #{{ $order->cashDrawerSession->terminal_code ?? 'REG-01' }}</span>
    </div>
</div>

<div class="no-print-bar">
    <button type="button" class="btn-action" onclick="window.print()">🖨️ Print Receipt</button>
    <button type="button" class="btn-action close-btn" onclick="window.close()">Close Window</button>
</div>

</body>
</html>
