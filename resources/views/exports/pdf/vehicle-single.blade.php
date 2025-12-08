<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Véhicule - {{ $vehicle->registration_plate }}</title>
    <style>
        /* ========================================================================
           ZENFLEET ENTERPRISE - FICHE VÉHICULE A4 PDF
           Ultra-Professional, Monochrome-Optimized, Print-Ready
           ======================================================================== */
        @page {
            size: A4;
            margin: 15mm 12mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #1a1a1a;
            background: #fff;
        }

        /* --- Document Header --- */
        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 12px;
            border-bottom: 2px solid #1a1a1a;
            margin-bottom: 15px;
        }

        .org-info {
            max-width: 60%;
        }

        .org-name {
            font-size: 14pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .org-address {
            font-size: 8pt;
            color: #4a4a4a;
        }

        .doc-meta {
            text-align: right;
            font-size: 8pt;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .doc-date {
            color: #4a4a4a;
        }

        /* --- Vehicle Identity Block --- */
        .vehicle-identity {
            background: #f5f5f5;
            border: 1px solid #ccc;
            padding: 12px 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .vehicle-main {
            flex: 1;
        }

        .registration-plate {
            font-size: 18pt;
            font-weight: 800;
            font-family: 'Consolas', 'Monaco', monospace;
            letter-spacing: 2px;
            border: 2px solid #1a1a1a;
            padding: 4px 12px;
            display: inline-block;
            background: #fff;
            margin-bottom: 6px;
        }

        .vehicle-desc {
            font-size: 11pt;
            font-weight: 600;
        }

        .vehicle-vin {
            font-size: 8pt;
            color: #666;
            margin-top: 2px;
        }

        .vehicle-status {
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border: 2px solid #1a1a1a;
            font-size: 9pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- KPI Row --- */
        .kpi-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 15px;
        }

        .kpi-box {
            flex: 1;
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .kpi-value {
            font-size: 14pt;
            font-weight: 800;
            line-height: 1.1;
        }

        .kpi-label {
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
            margin-top: 3px;
        }

        .kpi-unit {
            font-size: 9pt;
            font-weight: 600;
        }

        /* --- Main Content Grid --- */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .content-grid.full-width {
            grid-template-columns: 1fr;
        }

        /* --- Section Cards --- */
        .section {
            border: 1px solid #ccc;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .section-header {
            background: #f0f0f0;
            padding: 6px 10px;
            font-size: 9pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #ccc;
        }

        .section-body {
            padding: 10px;
        }

        /* --- Info Table --- */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table tr {
            border-bottom: 1px solid #e5e5e5;
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        .info-table th,
        .info-table td {
            padding: 5px 0;
            text-align: left;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .info-table th {
            font-weight: 600;
            color: #555;
            width: 45%;
        }

        .info-table td {
            font-weight: 500;
            color: #1a1a1a;
        }

        /* --- Data Tables --- */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .data-table thead th {
            background: #f0f0f0;
            padding: 6px 5px;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7pt;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #ccc;
        }

        .data-table tbody td {
            padding: 5px;
            border-bottom: 1px solid #e5e5e5;
            vertical-align: top;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* --- Driver Card --- */
        .driver-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }

        .driver-avatar {
            width: 40px;
            height: 40px;
            background: #333;
            color: #fff;
            font-weight: 700;
            font-size: 14pt;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .driver-info {
            flex: 1;
        }

        .driver-name {
            font-weight: 700;
            font-size: 10pt;
        }

        .driver-meta {
            font-size: 8pt;
            color: #555;
        }

        /* --- Footer --- */
        .document-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 7pt;
            color: #666;
            text-align: center;
        }

        /* --- Utility --- */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-mono {
            font-family: 'Consolas', 'Monaco', monospace;
        }

        .text-muted {
            color: #666;
        }

        .text-bold {
            font-weight: 700;
        }

        .nowrap {
            white-space: nowrap;
        }

        .empty-state {
            text-align: center;
            padding: 15px;
            color: #888;
            font-style: italic;
            font-size: 8pt;
        }
    </style>
</head>

<body>
    {{-- ========== DOCUMENT HEADER ========== --}}
    <div class="document-header">
        <div class="org-info">
            <div class="org-name">{{ $organization->name ?? 'ZenFleet' }}</div>
            @if($organization->address ?? null)
            <div class="org-address">{{ $organization->address }}</div>
            @endif
        </div>
        <div class="doc-meta">
            <div class="doc-title">Fiche Véhicule</div>
            <div class="doc-date">Généré le {{ $generatedAt->format('d/m/Y à H:i') }}</div>
        </div>
    </div>

    {{-- ========== VEHICLE IDENTITY ========== --}}
    <div class="vehicle-identity">
        <div class="vehicle-main">
            <div class="registration-plate">{{ $vehicle->registration_plate }}</div>
            <div class="vehicle-desc">{{ $vehicle->brand }} {{ $vehicle->model }} — {{ $vehicle->manufacturing_year }}</div>
            <div class="vehicle-vin">VIN: {{ $vehicle->vin ?? 'Non renseigné' }}</div>
        </div>
        <div class="vehicle-status">
            <span class="status-badge">{{ optional($vehicle->vehicleStatus)->name ?? 'Inconnu' }}</span>
        </div>
    </div>

    {{-- ========== KPI ROW ========== --}}
    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-value">{{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }}</div>
            <div class="kpi-label">Kilométrage Actuel <span class="kpi-unit">km</span></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $analytics['days_in_service'] ?? 0 }}</div>
            <div class="kpi-label">Jours en Service</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ number_format($analytics['maintenance_cost_total'] ?? 0, 0, ',', ' ') }}</div>
            <div class="kpi-label">Coût Maintenance <span class="kpi-unit">DA</span></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ number_format($analytics['cost_per_km'] ?? 0, 2, ',', ' ') }}</div>
            <div class="kpi-label">Coût par Km <span class="kpi-unit">DA</span></div>
        </div>
    </div>

    {{-- ========== MAIN CONTENT GRID ========== --}}
    <div class="content-grid">
        {{-- Left Column --}}
        <div>
            {{-- General Information --}}
            <div class="section">
                <div class="section-header">Informations Générales</div>
                <div class="section-body">
                    <table class="info-table">
                        <tr>
                            <th>Marque</th>
                            <td>{{ $vehicle->brand }}</td>
                        </tr>
                        <tr>
                            <th>Modèle</th>
                            <td>{{ $vehicle->model }}</td>
                        </tr>
                        <tr>
                            <th>Année</th>
                            <td>{{ $vehicle->manufacturing_year }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>{{ optional($vehicle->vehicleType)->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Couleur</th>
                            <td>{{ $vehicle->color ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Nombre de places</th>
                            <td>{{ $vehicle->seats ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Technical Specifications --}}
            <div class="section">
                <div class="section-header">Spécifications Techniques</div>
                <div class="section-body">
                    <table class="info-table">
                        <tr>
                            <th>Carburant</th>
                            <td>{{ optional($vehicle->fuelType)->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Transmission</th>
                            <td>{{ optional($vehicle->transmissionType)->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Cylindrée</th>
                            <td>{{ number_format($vehicle->engine_displacement_cc ?? 0) }} cc</td>
                        </tr>
                        <tr>
                            <th>Puissance</th>
                            <td>{{ number_format($vehicle->power_hp ?? 0) }} CV</td>
                        </tr>
                        <tr>
                            <th>Kilométrage initial</th>
                            <td>{{ number_format($vehicle->initial_mileage ?? 0, 0, ',', ' ') }} km</td>
                        </tr>
                        <tr>
                            <th>Distance parcourue</th>
                            <td>{{ number_format($analytics['total_km_driven'] ?? 0, 0, ',', ' ') }} km</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div>
            {{-- Financial Information --}}
            <div class="section">
                <div class="section-header">Informations Financières</div>
                <div class="section-body">
                    <table class="info-table">
                        <tr>
                            <th>Date d'acquisition</th>
                            <td>{{ $vehicle->acquisition_date ? $vehicle->acquisition_date->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Prix d'achat</th>
                            <td>{{ number_format($vehicle->purchase_price ?? 0, 0, ',', ' ') }} DA</td>
                        </tr>
                        <tr>
                            <th>Valeur actuelle</th>
                            <td>{{ number_format($vehicle->current_value ?? 0, 0, ',', ' ') }} DA</td>
                        </tr>
                        <tr>
                            <th>Dépôt</th>
                            <td>{{ optional($vehicle->depot)->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Catégorie</th>
                            <td>{{ optional($vehicle->category)->name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Current Driver --}}
            <div class="section">
                <div class="section-header">Chauffeur Actuel</div>
                <div class="section-body">
                    @if($driver)
                    <div class="driver-card">
                        <div class="driver-avatar">
                            {{ strtoupper(substr($driver->first_name ?? 'N', 0, 1)) }}{{ strtoupper(substr($driver->last_name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="driver-info">
                            <div class="driver-name">{{ $driver->first_name }} {{ $driver->last_name }}</div>
                            <div class="driver-meta">
                                Tél: {{ $driver->phone ?? 'N/A' }}<br>
                                Permis: {{ $driver->license_number ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="empty-state">Aucun chauffeur affecté</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ========== RECENT ASSIGNMENTS ========== --}}
    @if($vehicle->assignments && $vehicle->assignments->count() > 0)
    <div class="section">
        <div class="section-header">Historique des Affectations ({{ $analytics['assignments_count'] ?? $vehicle->assignments->count() }} total)</div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Chauffeur</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th class="text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicle->assignments->take(5) as $assignment)
                    <tr>
                        <td class="text-bold">{{ $assignment->driver ? $assignment->driver->first_name . ' ' . $assignment->driver->last_name : 'N/A' }}</td>
                        <td class="nowrap">{{ $assignment->start_datetime->format('d/m/Y') }}</td>
                        <td class="nowrap">{{ $assignment->end_datetime ? $assignment->end_datetime->format('d/m/Y') : '—' }}</td>
                        <td class="text-center">{{ $assignment->end_datetime ? 'Terminée' : 'En cours' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ========== RECENT MAINTENANCE ========== --}}
    @if($vehicle->maintenanceOperations && $vehicle->maintenanceOperations->count() > 0)
    <div class="section">
        <div class="section-header">Opérations de Maintenance Récentes ({{ $analytics['maintenance_count'] ?? $vehicle->maintenanceOperations->count() }} total)</div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Fournisseur</th>
                        <th class="text-right">Coût</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicle->maintenanceOperations->take(5) as $op)
                    <tr>
                        <td class="nowrap">{{ $op->operation_date ? $op->operation_date->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ Str::limit($op->description ?? $op->notes ?? 'N/A', 40) }}</td>
                        <td>{{ optional($op->supplier)->name ?? 'N/A' }}</td>
                        <td class="text-right font-mono">{{ number_format($op->total_cost ?? 0, 0, ',', ' ') }} DA</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ========== RECENT EXPENSES ========== --}}
    @if($vehicle->expenses && $vehicle->expenses->count() > 0)
    <div class="section">
        <div class="section-header">Dépenses Récentes</div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th class="text-right">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicle->expenses->take(5) as $expense)
                    <tr>
                        <td class="nowrap">{{ $expense->expense_date ? $expense->expense_date->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $expense->expense_type ?? 'N/A' }}</td>
                        <td>{{ Str::limit($expense->description ?? 'N/A', 35) }}</td>
                        <td class="text-right font-mono">{{ number_format($expense->amount ?? 0, 0, ',', ' ') }} DA</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ========== NOTES ========== --}}
    @if($vehicle->notes)
    <div class="section">
        <div class="section-header">Notes</div>
        <div class="section-body">
            <p style="white-space: pre-wrap; font-size: 8pt;">{{ $vehicle->notes }}</p>
        </div>
    </div>
    @endif

    {{-- ========== DOCUMENT FOOTER ========== --}}
    <div class="document-footer">
        Document généré automatiquement par ZenFleet &bull; {{ $organization->name ?? '' }} &bull; {{ $generatedAt->format('d/m/Y H:i') }}
    </div>
</body>

</html>