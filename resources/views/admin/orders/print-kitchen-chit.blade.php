<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Chit #{{ $order->order_number }} - Table {{ $order->table_number }}</title>
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

        .chit-container {
            width: 80mm;
            background: #ffffff;
            border: 1px dashed #000000;
            padding: 5mm;
            color: #000000;
        }

        .chit-header {
            text-align: center;
            border-bottom: 2px dashed #000000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }

        .chit-title {
            font-size: 1.15rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .reprint-alert {
            background: #000000;
            color: #ffffff;
            font-weight: 900;
            font-size: 0.85rem;
            padding: 2mm 0;
            margin-top: 2mm;
            letter-spacing: 0.05em;
        }

        .table-banner {
            font-size: 1.6rem;
            font-weight: 900;
            margin: 2mm 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2mm 0;
            text-align: center;
        }

        .chit-meta {
            font-size: 0.8rem;
            margin-bottom: 3mm;
            display: flex;
            justify-content: space-between;
        }

        .chit-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm;
        }

        .chit-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 1mm 0;
            font-size: 0.85rem;
        }

        .chit-table td {
            padding: 2mm 0;
            font-size: 0.95rem;
            vertical-align: top;
        }

        .dish-qty {
            font-size: 1.25rem;
            font-weight: 900;
            width: 12mm;
        }

        .dish-name {
            font-weight: 700;
        }

        .special-instruction {
            font-size: 0.8rem;
            font-weight: bold;
            display: block;
            margin-top: 1mm;
            padding-left: 2mm;
            border-left: 2px solid #000;
        }

        .chit-footer {
            border-top: 2px dashed #000000;
            padding-top: 3mm;
            text-align: center;
            font-size: 0.75rem;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .chit-container {
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

    <div class="chit-container">
        <div class="chit-header">
            <div class="chit-title">** KITCHEN ORDER CHIT **</div>
            <div>(調理指示伝票)</div>
            @if($order->reprint_count > 0)
                <div class="reprint-alert">
                    *** REPRINT #{{ $order->reprint_count }} (DUPLICATE) ***
                </div>
            @endif
        </div>

        <div class="table-banner">
            >>> TABLE {{ $order->table_number ?? 'Counter' }} <<<
        </div>

        <div class="chit-meta">
            <div>Order: #{{ $order->order_number }}</div>
            <div>Time: {{ $order->created_at->format('h:i A') }}</div>
        </div>

        <table class="chit-table">
            <thead>
                <tr>
                    <th style="width: 12mm;">QTY</th>
                    <th>ITEM NAME & SPECIAL NOTE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td class="dish-qty">[{{ $item->quantity }}]</td>
                        <td>
                            <div class="dish-name">{{ $item->item_name }}</div>
                            @if($item->special_notes)
                                <div class="special-instruction">>> NOTE: {{ $item->special_notes }}</div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="chit-footer">
            <div>Kitchen Pass Expediter Station</div>
            <div>Prepared for Server Delivery</div>
        </div>
    </div>

</body>
</html>
