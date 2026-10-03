<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Slip #{{ $order->order_number }} - Table {{ $order->table_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: monospace, -apple-system, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .bill-container {
            width: 80mm;
            background: #ffffff;
            border: 1px dashed #000000;
            padding: 5mm;
            color: #000000;
        }

        .bill-header {
            text-align: center;
            border-bottom: 2px dashed #000000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }

        .restaurant-title {
            font-size: 1.15rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .reprint-alert {
            background: #000000;
            color: #ffffff;
            font-weight: 900;
            font-size: 0.8rem;
            padding: 2mm 0;
            margin-top: 2mm;
            letter-spacing: 0.05em;
        }

        .table-banner {
            font-size: 1.5rem;
            font-weight: 900;
            margin: 2mm 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2mm 0;
            text-align: center;
        }

        .bill-meta {
            font-size: 0.8rem;
            margin-bottom: 3mm;
            display: flex;
            justify-content: space-between;
        }

        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }

        .bill-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 1mm 0;
            font-size: 0.8rem;
        }

        .bill-table td {
            padding: 1.5mm 0;
            font-size: 0.85rem;
        }

        .totals-section {
            border-top: 1px dashed #000000;
            border-bottom: 2px solid #000000;
            padding: 2mm 0;
            margin-bottom: 3mm;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            margin-bottom: 1mm;
        }

        .grand-total-row {
            font-size: 1.15rem;
            font-weight: 900;
            border-top: 1px solid #000;
            padding-top: 1.5mm;
            margin-top: 1.5mm;
        }

        .barcode-box {
            text-align: center;
            margin: 3mm 0;
            padding: 2mm;
            border: 1px dashed #000;
        }

        .barcode-font {
            font-size: 1.4rem;
            letter-spacing: 0.15em;
            font-weight: bold;
        }

        .bill-footer {
            text-align: center;
            font-size: 0.75rem;
            line-height: 1.4;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .bill-container {
                border: none;
                width: 100%;
                padding: 0;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="bill-container">
        <div class="bill-header">
            <div class="restaurant-title">{{ $restaurant->name }}</div>
            <div>CUSTOMER BILL SLIP (伝票)</div>
            @if($order->reprint_count > 0)
                <div class="reprint-alert">
                    *** DUPLICATE (REPRINT #{{ $order->reprint_count }}) ***
                </div>
            @endif
        </div>

        <div class="table-banner">
            TABLE {{ $order->table_number ?? 'Counter' }}
        </div>

        <div class="bill-meta">
            <div>Order: #{{ $order->order_number }}</div>
            <div>Date: {{ $order->created_at->format('d/m/Y h:i A') }}</div>
        </div>

        <table class="bill-table">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th style="text-align: center;">QTY</th>
                    <th style="text-align: right;">PRICE</th>
                    <th style="text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td style="max-width: 32mm;">{{ $item->item_name }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->unit_price, 0) }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($item->subtotal, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <div class="totals-row">
                <span>Subtotal (Gross Sales):</span>
                <span>{{ number_format($order->subtotal, 0) }} MMK</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="totals-row">
                    <span>Discount:</span>
                    <span>-{{ number_format($order->discount_amount, 0) }} MMK</span>
                </div>
            @endif
            <div class="totals-row">
                <span>Commercial Tax (5%):</span>
                <span>{{ number_format($order->tax_amount, 0) }} MMK</span>
            </div>
            <div class="totals-row grand-total-row">
                <span>TOTAL DUE:</span>
                <span>{{ number_format($order->total_amount, 0) }} MMK</span>
            </div>
        </div>

        <div class="barcode-box">
            <div class="barcode-font">||||| |||| |||||||| |||||</div>
            <div style="font-size: 0.8rem; font-weight: bold;">*{{ $order->order_number }}*</div>
        </div>

        <div class="bill-footer">
            <div>(ကျေးဇူးပြု၍ စားသောက်ပြီးပါက ဤစလစ်ကိုယူဆောင်၍</div>
            <div>ကောင်တာတွင် ငွေရှင်းပေးပါရန်)</div>
            <div style="font-size: 0.7rem; color: #475569; margin-top: 1mm;">
                Please present this slip at cashier counter for checkout
            </div>
        </div>
    </div>

</body>
</html>
