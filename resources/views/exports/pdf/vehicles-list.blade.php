<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Véhicules - {{ $organization->name }}</title>
    <style>
        /**
         * 🖨️ ENTERPRISE PDF TEMPLATE - MONOCHROME OPTIMIZED
         * 
         * Design principles:
         * - High contrast black/white for laser printing
         * - No gradients, no colors (except grayscale)
         * - Border-based visual hierarchy
         * - Professional, minimalist aesthetic
         */

        @page {
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

        /* === BADGES (Monochrome) === */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 7pt;
            font-weight: 600;
            border: 1px solid #000;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-disponible {
            background: #fff;
            color: #000;
        }

        .badge-affecte {
            background: #e5e5e5;
            color: #000;
        }

        .badge-maintenance {
            background: #000;
            color: #fff;
        }

        .badge-horsservice,
        .badge-hors-service {
            background: #666;
            color: #fff;
        }

        .badge-default {
            background: #ccc;
            color: #000;
        }

        /* === DRIVER CELL === */
        .driver-name {
            font-weight: 600;
        }

        .driver-phone {
            font-size: 7pt;
            color: #666;
        }

        .not-assigned {
            color: #888;
            font-style: italic;
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

        .footer-left {
            font-weight: 500;
        }

        /* === PAGE BREAK === */
        .page-break {
            page-break-after: always;
        }

        /* === PRINT OPTIMIZATION === */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .header {
                background: #000 !important;
                color: #fff !important;
            }
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <h1>LISTE DES VÉHICULES</h1>
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
            <div class="stat-value">{{ $vehicles->count() }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-box">
            @php
            $availableCount = $vehicles->filter(function($v) {
            $statusName = optional($v->vehicleStatus)->name ?? '';
            return in_array(strtolower($statusName), ['disponible', 'available']);
            })->count();
            @endphp
            <div class="stat-value">{{ $availableCount }}</div>
            <div class="stat-label">Disponibles</div>
        </div>
        <div class="stat-box">
            @php
            $assignedCount = $vehicles->filter(function($v) {
            return $v->assignments->isNotEmpty();
            })->count();
            @endphp
            <div class="stat-value">{{ $assignedCount }}</div>
            <div class="stat-label">Affectés</div>
        </div>
        <div class="stat-box">
            @php
            $maintenanceCount = $vehicles->filter(function($v) {
            $statusName = optional($v->vehicleStatus)->name ?? '';
            return strtolower($statusName) === 'maintenance';
            })->count();
            @endphp
            <div class="stat-value">{{ $maintenanceCount }}</div>
            <div class="stat-label">Maintenance</div>
        </div>
    </div>

    {{-- Vehicle Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 12%">Immatriculation</th>
                <th style="width: 18%">Véhicule</th>
                <th style="width: 18%">Chauffeur</th>
                <th style="width: 10%">Type</th>
                <th style="width: 12%">Statut</th>
                <th style="width: 10%">Kilométrage</th>
                <th style="width: 10%">Dépôt</th>
                <th style="width: 10%">Carburant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $vehicle)
            @php
            // 🔧 FIX: Utilise directement les champs du modèle Driver
            $activeAssignment = $vehicle->assignments->first();
            $driver = $activeAssignment ? $activeAssignment->driver : null;

            // Status badge class
            $statusName = optional($vehicle->vehicleStatus)->name ?? 'Inconnu';
            $statusSlug = strtolower(str_replace([' ', 'é', 'è'], ['', 'e', 'e'], $statusName));
            $badgeClass = match(true) {
            str_contains($statusSlug, 'disponible') => 'badge-disponible',
            str_contains($statusSlug, 'affect') => 'badge-affecte',
            str_contains($statusSlug, 'maintenance') => 'badge-maintenance',
            str_contains($statusSlug, 'hors') => 'badge-horsservice',
            default => 'badge-default'
            };
            @endphp
            <tr>
                <td><strong>{{ $vehicle->registration_plate }}</strong></td>
                <td>
                    {{ $vehicle->brand }} {{ $vehicle->model }}<br>
                    <span style="font-size: 7pt; color: #666;">{{ $vehicle->manufacturing_year }}</span>
                </td>
                <td>
                    @if($driver)
                    <span class="driver-name">{{ $driver->first_name }} {{ $driver->last_name }}</span><br>
                    <span class="driver-phone">{{ $driver->personal_phone ?? '' }}</span>
                    @else
                    <span class="not-assigned">Non affecté</span>
                    @endif
                </td>
                <td>{{ optional($vehicle->vehicleType)->name ?? 'N/A' }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $statusName }}</span></td>
                <td style="text-align: right;">{{ number_format($vehicle->current_mileage, 0, ',', ' ') }} km</td>
                <td>{{ optional($vehicle->depot)->name ?? 'N/A' }}</td>
                <td>{{ optional($vehicle->fuelType)->name ?? 'N/A' }}</td>
            </tr>

            @if(($loop->iteration % 18) == 0 && !$loop->last)
        </tbody>
    </table>
    <div class="page-break"></div>
    <table>
        <thead>
            <tr>
                <th style="width: 12%">Immatriculation</th>
                <th style="width: 18%">Véhicule</th>
                <th style="width: 18%">Chauffeur</th>
                <th style="width: 10%">Type</th>
                <th style="width: 12%">Statut</th>
                <th style="width: 10%">Kilométrage</th>
                <th style="width: 10%">Dépôt</th>
                <th style="width: 10%">Carburant</th>
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
            {{ $vehicles->count() }} véhicule(s) —
            @if(isset($filters['archived']) && $filters['archived'] === 'true')
            Archives
            @elseif(isset($filters['archived']) && $filters['archived'] === 'all')
            Tous
            @else
            Actifs uniquement
            @endif
        </div>
        <div class="footer-right">
            {{ $organization->name }} • ZenFleet © {{ date('Y') }} • Document confidentiel
        </div>
    </div>
</body>

</html>