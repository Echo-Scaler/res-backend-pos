<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dual Slips #{{ $order->order_number }} - Table {{ $order->table_number }}</title>
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

        .roll-container {
            width: 80mm;
            background: #ffffff;
            color: #000000;
        }

        .slip-section {
            padding: 5mm;
            border: 1px dashed #000000;
            margin-bottom: 2mm;
        }

        .chit-header, .bill-header {
            text-align: center;
            border-bottom: 2px dashed #000000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }

        .chit-title, .restaurant-title {
            font-size: 1.15rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .reprint-alert {
            background: #000000;
            color: #ffffff;
            font-weight: 900;
            font-size: 0.825rem;
            padding: 2mm 0;
            margin-top: 2mm;
            letter-spacing: 0.05em;
            text-align: center;
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

        .meta-line {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            margin: 1mm 0;
        }

        .tear-cut-divider {
            text-align: center;
            font-size: 0.75rem;
            font-weight: bold;
            color: #000000;
            padding: 4mm 0;
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
            background: #fafafa;
            margin: 3mm 0;
            letter-spacing: 0.05em;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3mm 0;
        }

        .items-table th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 1.5mm 0;
            font-size: 0.8rem;
            text-align: left;
        }

        .items-table td {
            padding: 1.5mm 0;
            font-size: 0.85rem;
            vertical-align: top;
        }

        .item-notes {
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 5mm;
            color: #222;
        }

        .total-summary {
            border-top: 2px dashed #000;
            padding-top: 2mm;
            margin-top: 2mm;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            margin: 1mm 0;
        }

        .grand-total {
            font-size: 1.15rem;
            font-weight: 900;
            border-top: 1px solid #000;
            border-bottom: 2px solid #000;
            padding: 2mm 0;
            margin: 2mm 0;
        }

        .barcode-box {
            text-align: center;
            margin: 4mm 0 2mm 0;
            padding: 2mm;
            border: 1px dashed #000;
        }

        .barcode-svg {
            display: inline-block;
            max-width: 100%;
            height: 38px;
        }

        .barcode-number {
            font-size: 0.85rem;
            font-weight: bold;
            letter-spacing: 0.15em;
            margin-top: 1mm;
        }

        .actions-no-print {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
        }

        .btn-print {
            background-color: #0f172a;
            color: #ffffff;
            border: none;
            padding: 0.65rem 1.5rem;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-close {
            background-color: #cbd5e1;
            color: #0f172a;
            border: none;
            padding: 0.65rem 1.25rem;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .actions-no-print {
                display: none !important;
            }

            .slip-section {
                border: none;
                padding: 0;
                margin-bottom: 5mm;
            }

            .roll-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="actions-no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print Both Slips</button>
        <button class="btn-close" onclick="window.close()">✕ Close Window</button>
    </div>

    <div class="roll-container">
        <!-- ================= SLIP 1: KITCHEN ORDER CHIT ================= -->
        <div class="slip-section">
            <div class="chit-header">
                <div class="chit-title">KITCHEN ORDER CHIT</div>
                <div style="font-size: 0.75rem; margin-top: 1mm;">調理指示伝票 (FOR COOKS)</div>
                @if($order->reprint_count > 0)
                    <div class="reprint-alert">*** REPRINT #{{ $order->reprint_count }} (DUPLICATE) ***</div>
                @endif
            </div>

            <div class="table-banner">TABLE: {{ $order->table_number ?? 'TAKEAWAY' }}</div>

            <div class="meta-line">
                <span>ORDER: #{{ $order->order_number }}</span>
                <span>CHIT #1</span>
            </div>
            <div class="meta-line">
                <span>TIME: {{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                <span>STATUS: {{ $order->kitchen_status }}</span>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 18%;">QTY</th>
                        <th style="width: 82%;">DISH NAME / ITEM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td style="font-weight: 900; font-size: 1.05rem;">{{ $item->quantity }}x</td>
                            <td>
                                <strong style="font-size: 0.95rem;">{{ $item->item_name }}</strong>
                                @if($item->special_notes)
                                    <div class="item-notes">★ NOTE: {{ $item->special_notes }}</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="text-align: center; font-size: 0.75rem; border-top: 1px dashed #000; padding-top: 2mm;">
                Total Items: {{ $order->items->sum('quantity') }} dishes
            </div>
        </div>

        <!-- ================= TEAR / CUT DIVIDER ================= -->
        <div class="tear-cut-divider">
            - - - - - - - - - - - - ✂️ CUT HERE (伝票切取線) - - - - - - - - - - - -
        </div>

        <!-- ================= SLIP 2: CUSTOMER BILL SLIP ================= -->
        <div class="slip-section">
            <div class="bill-header">
                <div class="restaurant-title">{{ $restaurant->name ?? 'RESTAURANT POS' }}</div>
                <div style="font-size: 0.75rem; margin-top: 1mm;">CUSTOMER BILL SLIP • 会計伝票</div>
                <div style="font-size: 0.7rem; color: #444; margin-top: 0.5mm;">(Please bring this slip to cashier counter)</div>
                @if($order->reprint_count > 0)
                    <div class="reprint-alert">*** REPRINT #{{ $order->reprint_count }} (DUPLICATE) ***</div>
                @endif
            </div>

            <div class="table-banner">TABLE: {{ $order->table_number ?? 'TAKEAWAY' }}</div>

            <div class="meta-line">
                <span>ORDER: #{{ $order->order_number }}</span>
                <span>CHIT #2</span>
            </div>
            <div class="meta-line">
                <span>DATE: {{ $order->created_at->format('Y-m-d H:i') }}</span>
                <span>CURRENCY: MMK</span>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">QTY</th>
                        <th style="width: 55%;">ITEM</th>
                        <th style="width: 30%; text-align: right;">MMK</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->quantity }}x</td>
                            <td>{{ $item->item_name }}</td>
                            <td style="text-align: right;">{{ number_format($item->subtotal, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-summary">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>{{ number_format($order->subtotal, 0) }} MMK</span>
                </div>
                @if($order->tax_amount > 0)
                    <div class="total-row">
                        <span>Commercial Tax (5%):</span>
                        <span>{{ number_format($order->tax_amount, 0) }} MMK</span>
                    </div>
                @endif
                @if($order->discount_amount > 0)
                    <div class="total-row">
                        <span>Discount:</span>
                        <span>-{{ number_format($order->discount_amount, 0) }} MMK</span>
                    </div>
                @endif
                <div class="total-row grand-total">
                    <span>TOTAL:</span>
                    <span>{{ number_format($order->total_amount, 0) }} MMK</span>
                </div>
            </div>

            <!-- Barcode for Cashier Counter Scanner -->
            <div class="barcode-box">
                <svg class="barcode-svg" viewBox="0 0 240 40" preserveAspectRatio="none">
                    @php
                        $code = preg_replace('/[^0-9]/', '', $order->order_number) ?: (string) $order->id;
                        $bars = str_split(str_pad($code, 18, '1039281749', STR_PAD_LEFT));
                        $x = 10;
                    @endphp
                    @foreach($bars as $idx => $char)
                        @php
                            $width = ((int)$char % 3) + 1;
                            $fill = ($idx % 2 === 0) ? '#000000' : 'transparent';
                        @endphp
                        <rect x="{{ $x }}" y="0" width="{{ $width }}" height="40" fill="{{ $fill }}" />
                        @php $x += $width + 1; @endphp
                    @endforeach
                </svg>
                <div class="barcode-number">*{{ $order->order_number }}*</div>
            </div>

            <div style="text-align: center; font-size: 0.7rem; margin-top: 3mm; border-top: 1px dashed #000; padding-top: 2mm;">
                ကျေးဇူးတင်ပါသည် • THANK YOU FOR DINING WITH US<br>
                Currency: Burmese Kyats (MMK)
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
