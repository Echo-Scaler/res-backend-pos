<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Print Table Stands - {{ $restaurant->name }}</title>
    <!-- Google Font: Mada -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mada:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Mada", sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 2rem 1rem;
        }

        .print-controls {
            max-width: 210mm;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-print {
            background-color: #9ec63b;
            color: #0f172a;
            font-family: inherit;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.65rem 1.5rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-print:hover {
            background-color: #8bb42c;
        }

        .btn-back {
            background-color: #ffffff;
            color: #475569;
            font-family: inherit;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* A4 Page Container (210mm x 297mm) */
        .sheet-container {
            max-width: 210mm;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8mm;
        }

        .table-tent-card {
            width: 100%;
            height: 135mm;
            background: #ffffff;
            border: 2px dashed #0f172a;
            border-radius: 8px;
            padding: 5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            page-break-inside: avoid;
        }

        .tent-header {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .tent-restaurant-name {
            font-size: 1.05rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0f172a;
        }

        .tent-table-box {
            background-color: #0f172a;
            color: #ffffff;
            width: 100%;
            padding: 2mm 0;
            border-radius: 4px;
            margin-bottom: 2mm;
        }

        .tent-table-title {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .tent-table-zone {
            font-size: 0.7rem;
            color: #9ec63b;
            font-weight: 600;
        }

        .tent-qr-box {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 2mm;
            width: 48mm;
            height: 48mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tent-qr-box svg, .tent-qr-box img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .tent-cta-main {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
        }

        .tent-cta-mm {
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            margin-top: 1mm;
        }

        .tent-footer {
            width: 100%;
            border-top: 1px dashed #cbd5e1;
            padding-top: 2mm;
            font-size: 0.65rem;
            color: #64748b;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .print-controls {
                display: none !important;
            }

            .sheet-container {
                max-width: 100%;
                gap: 5mm;
            }

            .table-tent-card {
                box-shadow: none;
                border: 1.5px dashed #000000;
            }

            @page {
                size: A4 portrait;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

    <div class="print-controls">
        <a href="{{ route('admin.tables.index') }}" class="btn-back">
            <i class="ti ti-arrow-left"></i> Back to Tables
        </a>
        <div style="font-weight: 700; color: #0f172a;">
            Batch Printing {{ $tables->count() }} Table Stands {{ $areaFilter ? "({$areaFilter})" : '' }}
        </div>
        <button type="button" class="btn-print" onclick="window.print()">
            <i class="ti ti-printer"></i> Print All Table Stands (A4)
        </button>
    </div>

    <div class="sheet-container">
        @foreach($tables as $table)
            <div class="table-tent-card">
                <div class="tent-header">
                    <div class="tent-restaurant-name">{{ $restaurant->name }}</div>
                </div>

                <div class="tent-table-box">
                    <div class="tent-table-title">TABLE {{ $table->table_number }}</div>
                    <div class="tent-table-zone">{{ $table->floor_area }} • {{ $table->seating_capacity }} Seats</div>
                </div>

                <div class="tent-qr-box">
                    {!! $table->getQrCodeSvg() !!}
                </div>

                <div style="margin: 2mm 0;">
                    <div class="tent-cta-main">Scan with Phone to Order</div>
                    <div class="tent-cta-mm">ဟင်းပွဲများ ကြည့်ရှုပြီး အော်ဒါတင်ရန် စကင်ဖတ်ပါ</div>
                </div>

                <div class="tent-footer">
                    <div><i class="ti ti-wifi"></i> Wi-Fi: {{ $restaurant->slug }}-guest</div>
                    <div>Pass: {{ date('Y') }}pos</div>
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
