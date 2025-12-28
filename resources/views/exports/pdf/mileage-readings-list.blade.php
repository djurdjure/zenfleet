<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique Kilométrique - {{ $organization->name }}</title>
    <style>
        /**
         * 🖨️ ENTERPRISE PDF TEMPLATE - MONOCHROME OPTIMIZED
         */

        @page {
            size: A4 portrait;
            /* Portrait is better for list of readings usually, or Landscape? List has 6-7 cols. Portrait might be tight. Let's stick to Landscape to be safe and match Vehicles. */
            size: A4 landscape;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Arial, sans-serif;
            color: #000;
            line-height: 1.4;
            font-size: 9pt;
            background: #fff;
        }

        /* === HEADER === */
        .header {
            background: #1a1a1a;
            color: #fff;
            padding: 16px 20px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left h1 {
            font-size: 18pt;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 2px;
        }

        .header-left .subtitle {
            font-size: 9pt;
            opacity: 0.85;
            font-weight: 400;
        }

        .header-right {
            text-align: right;
            font-size: 8pt;
            opacity: 0.85;
        }

        /* === STATISTICS BAR === */
        .stats-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-box {
            flex: 1;
            border: 1.5px solid #000;
            padding: 10px 12px;
            text-align: center;
        }

        .stat-value {
            font-size: 16pt;
            font-weight: 700;
            color: #000;
        }

        .stat-label {
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #333;
            margin-top: 2px;
        }

        /* === TABLE === */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
        }

        thead {
            background: #f0f0f0;
        }

        th {
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            color: #000;
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 2px solid #000;
            border-right: 1px solid #ccc;
        }

        th:last-child {
            border-right: none;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #eee;
            vertical-align: top;
        }

        td:last-child {
            border-right: none;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* === BADGES === */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 7pt;
            font-weight: 600;
            border: 1px solid #000;
            text-transform: uppercase;
        }

        /* === FOOTER === */
        .footer {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 2px solid #000;
            display: flex;
            justify-content: space-between;
            font-size: 8pt;
            color: #333;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <h1>HISTORIQUE KILOMÉTRIQUE</h1>
            <div class="subtitle">{{ $organization->name }}</div>
        </div>
        <div class="header-right">
            <strong>{{ now()->format('d/m/Y') }}</strong><br>
            {{ now()->format('H:i') }}
        </div>
    </div>

    {{-- Statistics Bar --}}
    <div class="stats-bar">
        <div class="stat-box">
            <div class="stat-value">{{ $readings->count() }}</div>
            <div class="stat-label">Relevés Affichés</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($analytics['total_mileage_covered'] ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-label">Km Parcourus (Période)</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($analytics['avg_daily_mileage'] ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-label">Moyenne Journalière</div>
        </div>
    </div>

    {{-- Readings Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 15%">Véhicule</th>
                <th style="width: 20%">Modèle</th>
                <th style="width: 10%">Date</th>
                <th style="width: 8%">Heure</th>
                <th style="width: 10%; text-align: right;">Kilométrage</th>
                <th style="width: 10%; text-align: right;">Diff.</th>
                <th style="width: 10%">Méthode</th>
                <th style="width: 17%">Enregistré par</th>
            </tr>
        </thead>
        <tbody>
            @foreach($readings as $reading)
            <tr>
                <td><strong>{{ $reading->vehicle->registration_plate ?? 'Véhicule Inconnu' }}</strong></td>
                <td>
                    {{ $reading->vehicle ? ($reading->vehicle->brand . ' ' . $reading->vehicle->model) : 'N/A' }}
                </td>
                <td>{{ $reading->recorded_at ? $reading->recorded_at->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ $reading->recorded_at ? $reading->recorded_at->format('H:i') : 'N/A' }}</td>
                <td style="text-align: right; font-weight: bold;">
                    {{ number_format($reading->mileage, 0, ',', ' ') }} km
                </td>
                <td style="text-align: right;">
                    @php
                    $diff = $reading->previous_mileage ? $reading->mileage - $reading->previous_mileage : null;
                    @endphp
                    @if($diff !== null)
                    +{{ number_format($diff, 0, ',', ' ') }}
                    @else
                    -
                    @endif
                </td>
                <td>
                    {{ $reading->recording_method === 'manual' ? 'Manuel' : 'Auto' }}
                </td>
                <td>
                    {{ $reading->recordedBy?->name ?? 'Système' }}
                </td>
            </tr>

            {{-- Gestion saut de page tous les 20 relevés --}}
            @if(($loop->iteration % 20) == 0 && !$loop->last)
        </tbody>
    </table>
    <div class="page-break"></div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%">Véhicule</th>
                <th style="width: 20%">Modèle</th>
                <th style="width: 10%">Date</th>
                <th style="width: 8%">Heure</th>
                <th style="width: 10%; text-align: right;">Kilométrage</th>
                <th style="width: 10%; text-align: right;">Diff.</th>
                <th style="width: 10%">Méthode</th>
                <th style="width: 17%">Enregistré par</th>
            </tr>
        </thead>
        <tbody>
            @endif
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">
            ZenFleet Analytics
        </div>
        <div class="footer-right">
            Confidentiel • {{ $organization->name }}
        </div>
    </div>
</body>

</html>