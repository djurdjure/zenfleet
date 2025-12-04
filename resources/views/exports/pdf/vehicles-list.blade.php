<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Véhicules - {{ $organization->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #1f2937;
            line-height: 1.4;
            font-size: 11px;
        }

        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            gap: 10px;
        }

        .stat-card {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
        }

        .stat-label {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background: #f3f4f6;
        }

        th {
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            font-size: 10px;
            text-transform: uppercase;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #f3f4f6;
            color: #1f2937;
        }

        tr:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 600;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
        }

        .badge-orange {
            background: #fed7aa;
            color: #9a3412;
        }

        .badge-red {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-gray {
            background: #f3f4f6;
            color: #374151;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>Liste des Véhicules</h1>
        <div class="subtitle">{{ $organization->name }} - Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

    {{-- Statistiques --}}
    <div class="stats">
        <div class="stat-card">
            <div class="stat-value">{{ $vehicles->count() }}</div>
            <div class="stat-label">Total Véhicules</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $vehicles->where('vehicleStatus.name', 'Disponible')->count() }}</div>
            <div class="stat-label">Disponibles</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $vehicles->whereNotNull('assignments')->count() }}</div>
            <div class="stat-label">Affectés</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $vehicles->where('vehicleStatus.name', 'Maintenance')->count() }}</div>
            <div class="stat-label">En Maintenance</div>
        </div>
    </div>

    {{-- Table des véhicules --}}
    <table>
        <thead>
            <tr>
                <th>Immatriculation</th>
                <th>Véhicule</th>
                <th>Chauffeur</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Kilométrage</th>
                <th>Dépôt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $vehicle)
            @php
            // Utiliser l'attribut currentAssignment (chargé eager loading)
            $activeAssignment = $vehicle->currentAssignment;
            $driver = $activeAssignment?->driver;
            $user = $driver?->user;

            $statusColors = [
            'Disponible' => 'badge-green',
            'Affecté' => 'badge-orange',
            'Maintenance' => 'badge-red',
            'Hors service' => 'badge-gray'
            ];
            $statusName = optional($vehicle->vehicleStatus)->name ?? 'Inconnu';
            $badgeClass = $statusColors[$statusName] ?? 'badge-gray';
            @endphp
            <tr>
                <td style="font-weight: 600;">{{ $vehicle->registration_plate }}</td>
                <td>
                    {{ $vehicle->brand }} {{ $vehicle->model }}<br>
                    <span style="font-size: 9px; color: #6b7280;">{{ $vehicle->manufacturing_year }}</span>
                </td>
                <td>
                    @if($user)
                    {{$user->name }} {{ $user->last_name ?? '' }}<br>
                    <span style="font-size: 9px; color: #6b7280;">{{ $driver->phone ?? $user->phone ?? '' }}</span>
                    @else
                    <span style="color: #9ca3af; font-style: italic;">Non affecté</span>
                    @endif
                </td>
                <td>{{ optional($vehicle->vehicleType)->name ?? 'N/A' }}</td>
                <td>
                    <span class="badge {{ $badgeClass }}">{{ $statusName }}</span>
                </td>
                <td>{{ number_format($vehicle->current_mileage) }} km</td>
                <td>{{ optional($vehicle->depot)->name ?? 'N/A' }}</td>
            </tr>

            @if(($loop->iteration % 20) == 0 && !$loop->last)
        </tbody>
    </table>
    <div class="page-break"></div>
    <table>
        <thead>
            <tr>
                <th>Immatriculation</th>
                <th>Véhicule</th>
                <th>Chauffeur</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Kilométrage</th>
                <th>Dépôt</th>
            </tr>
        </thead>
        <tbody>
            @endif
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Total: {{ $vehicles->count() }} véhicule(s)
            @if(isset($filters['archived']) && $filters['archived'] === 'true')
            - Véhicules archivés
            @elseif(isset($filters['archived']) && $filters['archived'] === 'all')
            - Tous les véhicules
            @else
            - Véhicules actifs
            @endif
        </p>
        <p>{{ $organization->name }} - ZenFleet © {{ date('Y') }} - Document confidentiel</p>
    </div>
</body>

</html>