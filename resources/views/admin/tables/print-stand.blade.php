<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Stand - Table {{ $table->table_number }} - {{ $restaurant->name }}</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .print-controls {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
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
            transition: all 0.2s ease;
        }

        .btn-print:hover {
            background-color: #8bb42c;
            transform: translateY(-1px);
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

        /* Acrylic Stand Insert (A6 Size: 105mm x 148mm) */
        .table-tent-card {
            width: 105mm;
            height: 148mm;
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 12px;
            padding: 6mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        /* Top Brand Header */
        .tent-header {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 3mm;
            margin-bottom: 2mm;
        }

        .tent-restaurant-name {
            font-size: 1.15rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0f172a;
        }

        .tent-sub-brand {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 1mm;
        }

        /* Table Number Focus */
        .tent-table-box {
            background-color: #0f172a;
            color: #ffffff;
            width: 100%;
            padding: 2.5mm 0;
            border-radius: 6px;
            margin-bottom: 3mm;
        }

        .tent-table-title {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .tent-table-zone {
            font-size: 0.72rem;
            color: #9ec63b;
            font-weight: 600;
        }

        /* QR Code Container */
        .tent-qr-box {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 3mm;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52mm;
            height: 52mm;
            margin-bottom: 2mm;
        }

        .tent-qr-box svg, .tent-qr-box img {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Call To Action Instruction */
        .tent-instructions {
            width: 100%;
        }

        .tent-cta-main {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
        }

        .tent-cta-mm {
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
            margin-top: 1mm;
        }

        /* Footer Wi-Fi & Note */
        .tent-footer {
            width: 100%;
            border-top: 1px dashed #cbd5e1;
            padding-top: 2.5mm;
            font-size: 0.68rem;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tent-wifi-info {
            font-weight: 600;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        @media print {
            body {
                background: none;
                padding: 0;
                min-height: auto;
            }

            .print-controls {
                display: none !important;
            }

            .table-tent-card {
                box-shadow: none;
                border: 2px solid #000000;
                page-break-inside: avoid;
            }

            @page {
                size: 105mm 148mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="print-controls">
        <a href="{{ route('admin.tables.index') }}" class="btn-back">
            <i class="ti ti-arrow-left"></i> Back to Floor
        </a>
        <button type="button" class="btn-print" onclick="window.print()">
            <i class="ti ti-printer"></i> Print Table Stand (A6)
        </button>
    </div>

    <!-- Acrylic Stand Insert Card -->
    <div class="table-tent-card">
        <!-- Brand Header -->
        <div class="tent-header">
            <div class="tent-restaurant-name">{{ $restaurant->name }}</div>
            <div class="tent-sub-brand">Fine Dining & Smart QR Ordering</div>
        </div>

        <!-- Table Number Banner -->
        <div class="tent-table-box">
            <div class="tent-table-title">TABLE {{ $table->table_number }}</div>
            <div class="tent-table-zone">{{ $table->floor_area }} • {{ $table->seating_capacity }} Seats</div>
        </div>

        <!-- SVG QR Code -->
        <div class="tent-qr-box">
            {!! $table->getQrCodeSvg() !!}
        </div>

        <!-- Instructions (Bilingual) -->
        <div class="tent-instructions">
            <div class="tent-cta-main">
                <i class="ti ti-scan"></i> Scan with Phone to Order
            </div>
            <div class="tent-cta-mm">
                ဟင်းပွဲများ ကြည့်ရှုပြီး အော်ဒါတင်ရန် စကင်ဖတ်ပါ
            </div>
        </div>

        <!-- Footer -->
        <div class="tent-footer">
            <div class="tent-wifi-info">
                <i class="ti ti-wifi"></i> Wi-Fi: {{ $restaurant->slug }}-guest
            </div>
            <div>
                Pass: {{ date('Y') }}pos
            </div>
        </div>
    </div>

</body>
</html>
