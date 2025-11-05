<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Dépôt - {{ $depot->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        /* En-tête */
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: middle;
        }

        h1 {
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 10pt;
            opacity: 0.9;
        }

        .ref-code {
            font-size: 18pt;
            font-weight: bold;
        }

        /* Sections */
        .section {
            margin-bottom: 20px;
            break-inside: avoid;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        /* Grille informations */
        .info-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 8px 0;
            width: 50%;
            vertical-align: top;
        }

        .info-label {
            font-size: 9pt;
            color: #666;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 11pt;
            color: #111;
            font-weight: 600;
        }

        /* Statistiques */
        .stats-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .stat-label {
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
        }

        /* Tableaux */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background: #f1f5f9;
        }

        table th {
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #cbd5e1;
            text-transform: uppercase;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10pt;
        }

        table tbody tr:hover {
            background: #f8fafc;
        }

        /* Badge statut */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: 600;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background: #f3f4f6;
            color: #374151;
        }

        .badge-assigned {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-transferred {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-unassigned {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Barre de progression */
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 10px;
        }

        /* Pied de page */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
        }

        /* Statut groupe */
        .status-group {
            margin-bottom: 15px;
            break-inside: avoid;
        }

        .status-group-title {
            background: #f1f5f9;
            padding: 6px 10px;
            font-weight: 600;
            font-size: 10pt;
            margin-bottom: 5px;
        }

        /* Description */
        .description-box {
            background: #f8fafc;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            margin-top: 10px;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 30px;
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">

        {{-- En-tête --}}
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <h1>FICHE DÉPÔT</h1>
                    <div class="subtitle">{{ Auth::user()->organization->name ?? 'ZenFleet' }}</div>
                </div>
                <div class="header-right">
                    <div style="font-size: 9pt; margin-bottom: 5px;">Référence</div>
                    <div class="ref-code">{{ $depot->code ?? 'DP-' . str_pad($depot->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 8pt; margin-top: 5px;">{{ now()->format('d/m/Y à H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- Informations Générales --}}
        <div class="section">
            <div class="section-title">INFORMATIONS GÉNÉRALES</div>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Nom du Dépôt</div>
                        <div class="info-value">{{ $depot->name }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Code</div>
                        <div class="info-value">{{ $depot->code ?? 'Non défini' }}</div>
                    </div>
                </div>

                @if($depot->address || $depot->city)
                <div class="info-row">
                    <div class="info-cell" colspan="2" style="width: 100%;">
                        <div class="info-label">Adresse</div>
                        <div class="info-value">
                            {{ $depot->address ?? 'Non renseignée' }}
                            @if($depot->city)
                                <br>{{ $depot->city }}@if($depot->wilaya), {{ $depot->wilaya }}@endif @if($depot->postal_code) - {{ $depot->postal_code }}@endif
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if($depot->phone || $depot->email)
                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Contact</div>
                        <div class="info-value">
                            @if($depot->phone){{ $depot->phone }}<br>@endif
                            @if($depot->email){{ $depot->email }}@endif
                        </div>
                    </div>
                    @if($depot->manager_name)
                    <div class="info-cell">
                        <div class="info-label">Responsable</div>
                        <div class="info-value">
                            {{ $depot->manager_name }}
                            @if($depot->manager_phone)<br>{{ $depot->manager_phone }}@endif
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Statut</div>
                        <div class="info-value">
                            <span class="badge {{ $depot->is_active ? 'badge-success' : 'badge-inactive' }}">
                                {{ $depot->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Capacité</div>
                        <div class="info-value">{{ $depot->capacity ?? 'Non définie' }} @if($depot->capacity)véhicule(s)@endif</div>
                    </div>
                </div>
            </div>

            @if($depot->description)
            <div class="description-box">
                <div class="info-label">Description</div>
                {{ $depot->description }}
            </div>
            @endif
        </div>

        {{-- Statistiques --}}
        <div class="section">
            <div class="section-title">STATISTIQUES</div>

            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-label">Total véhicules</div>
                    <div class="stat-value">{{ $stats['total_vehicles'] }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Capacité totale</div>
                    <div class="stat-value">{{ $stats['capacity'] ?? 'N/A' }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Places disponibles</div>
                    <div class="stat-value">{{ $stats['available_space'] }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Taux occupation</div>
                    <div class="stat-value">{{ number_format($stats['occupancy_percentage'], 1) }}%</div>
                </div>
            </div>

            @if($depot->capacity)
            <div style="margin-top: 10px;">
                <div style="font-size: 9pt; color: #666; margin-bottom: 3px;">
                    Occupation: {{ $stats['total_vehicles'] }} / {{ $stats['capacity'] }}
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min($stats['occupancy_percentage'], 100) }}%"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Véhicules --}}
        @if($vehicles->count() > 0)
        <div class="section">
            <div class="section-title">VÉHICULES ASSIGNÉS ({{ $vehicles->count() }})</div>

            @foreach($vehiclesByStatus as $status => $statusVehicles)
            <div class="status-group">
                <div class="status-group-title">{{ $status }} ({{ $statusVehicles->count() }})</div>

                <table>
                    <thead>
                        <tr>
                            <th>Immatriculation</th>
                            <th>Marque/Modèle</th>
                            <th>Type</th>
                            <th>Carburant</th>
                            <th>Kilométrage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statusVehicles as $vehicle)
                        <tr>
                            <td style="font-weight: 600;">{{ $vehicle->registration_plate }}</td>
                            <td>{{ $vehicle->brand ?? 'N/A' }} {{ $vehicle->model ?? '' }}</td>
                            <td>{{ $vehicle->vehicleType?->name ?? 'N/A' }}</td>
                            <td>{{ $vehicle->fuelType?->name ?? 'N/A' }}</td>
                            <td>{{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }} km</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
        @else
        <div class="section">
            <div class="section-title">VÉHICULES ASSIGNÉS</div>
            <div class="empty-state">
                <div style="font-weight: 600; margin-bottom: 5px;">Aucun véhicule assigné</div>
                <div style="font-size: 9pt;">Capacité: {{ $depot->capacity ?? 'non définie' }} véhicule(s)</div>
            </div>
        </div>
        @endif

        {{-- Historique --}}
        @if($recentHistory->count() > 0)
        <div class="section">
            <div class="section-title">HISTORIQUE RÉCENT</div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Véhicule</th>
                        <th>Action</th>
                        <th>Par</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentHistory as $history)
                    <tr>
                        <td style="font-size: 9pt;">{{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}</td>
                        <td style="font-weight: 600;">{{ $history->registration_plate }}</td>
                        <td>
                            <span class="badge badge-{{ $history->action }}">
                                {{ ucfirst($history->action) }}
                            </span>
                        </td>
                        <td>{{ $history->assigned_by_name }}</td>
                        <td style="font-size: 9pt;">{{ Str::limit($history->notes ?? '-', 40) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Pied de page --}}
        <div class="footer">
            <div>Document généré le {{ now()->format('d/m/Y à H:i') }}</div>
            <div style="margin-top: 3px;">{{ Auth::user()->organization->name ?? 'ZenFleet' }} - Gestion de Flotte Automobile</div>
        </div>

    </div>
</body>
</html>
