<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Véhicule - {{ $vehicle->registration_plate }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Arial, sans-serif;
            color: #1f2937;
            /* gray-800 */
            line-height: 1.5;
            font-size: 10pt;
            background: #fff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Utility classes */
        .text-xs {
            font-size: 8pt;
        }

        .text-sm {
            font-size: 9pt;
        }

        .text-lg {
            font-size: 14pt;
        }

        .text-xl {
            font-size: 16pt;
        }

        .text-2xl {
            font-size: 20pt;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-medium {
            font-weight: 500;
        }

        .text-gray-400 {
            color: #9ca3af;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .text-gray-900 {
            color: #111827;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .tracking-wide {
            letter-spacing: 0.025em;
        }

        .border-b {
            border-bottom: 1px solid #e5e7eb;
        }

        /* Layout */
        .container {
            width: 100%;
        }

        /* Header */
        .header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #111827;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        /* Identity Section */
        .identity-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9fafb;
            /* gray-50 */
            border: 1px solid #e5e7eb;
            /* gray-200 */
            border-radius: 8px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 8pt;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Status Badge Colors */
        .badge-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        /* green-100 green-800 */
        .badge-maintenance {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* yellow-100 yellow-800 */
        .badge-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* red-100 red-800 */
        .badge-default {
            background-color: #f3f4f6;
            color: #1f2937;
        }

        /* gray-100 gray-800 */

        /* Grid Layout for KPIs */
        .grid-cols-3 {
            display: table;
            /* Fallback */
            width: 100%;
            table-layout: fixed;
        }

        .grid-item {
            display: table-cell;
            padding: 10px;
            vertical-align: top;
        }

        /* Section styling */
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 10pt;
            font-weight: 600;
            text-transform: uppercase;
            color: #374151;
            /* gray-700 */
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        /* Data Lists */
        .data-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .data-grid td {
            padding: 6px 0;
            vertical-align: top;
        }

        .label {
            color: #6b7280;
            font-size: 9pt;
            width: 40%;
        }

        .value {
            color: #111827;
            font-weight: 500;
            font-size: 9pt;
        }

        /* Tables */
        .table-pro {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .table-pro th {
            text-align: left;
            padding: 8px;
            background-color: #f3f4f6;
            color: #4b5563;
            font-size: 8pt;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-pro td {
            padding: 8px;
            border-bottom: 1px solid #f3f4f6;
            color: #1f2937;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>

    <div class="header">
        <table style="width: 100%">
            <tr>
                <td>
                    <div class="text-xl font-bold">{{ $organization->name ?? 'ZenFleet' }}</div>
                    <div class="text-sm text-gray-500">{{ $organization->address ?? '' }}</div>
                </td>
                <td style="text-align: right">
                    <div class="text-xs text-gray-400 uppercase">Fiche Véhicule</div>
                    <div class="text-sm font-medium">{{ now()->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="identity-section">
        <table style="width: 100%">
            <tr>
                <td>
                    <div class="text-xs text-gray-500 uppercase">Immatriculation</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $vehicle->registration_plate }}</div>
                    <div class="text-sm text-gray-600">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->manufacturing_year }})</div>
                    <div class="text-xs text-gray-400 mt-1">VIN: {{ $vehicle->vin ?? '-' }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    @php
                    $statusId = $vehicle->status_id;
                    $badgeClass = match($statusId) {
                    1 => 'badge-active',
                    2 => 'badge-maintenance',
                    3 => 'badge-inactive',
                    default => 'badge-default'
                    };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $vehicle->vehicleStatus->name ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- KPIs -->
    <div class="section">
        <div class="grid-cols-3">
            <div class="grid-item" style="border-right: 1px solid #e5e7eb;">
                <div class="text-xs text-gray-500 uppercase center">Kilométrage</div>
                <div class="text-lg font-bold text-gray-900 center">{{ number_format($vehicle->current_mileage, 0, ',', ' ') }} km</div>
            </div>
            <div class="grid-item" style="border-right: 1px solid #e5e7eb; padding-left: 20px;">
                <div class="text-xs text-gray-500 uppercase center">Âge</div>
                <div class="text-sm font-bold text-gray-900 center">{{ $analytics['vehicle_age_formatted'] ?? '-' }}</div>
            </div>
            <div class="grid-item" style="padding-left: 20px;">
                <div class="text-xs text-gray-500 uppercase center">Coût/km</div>
                <div class="text-lg font-bold text-gray-900 center">{{ number_format($analytics['cost_per_km'] ?? 0, 2, ',', ' ') }} DA</div>
            </div>
        </div>
    </div>

    <div style="clear: both; height: 10px;"></div>

    <!-- 2 Column Layout Simulation -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 15px;">

                <div class="section">
                    <div class="section-title">Informations Générales</div>
                    <table class="data-grid">
                        <tr>
                            <td class="label">Marque</td>
                            <td class="value">{{ $vehicle->brand }}</td>
                        </tr>
                        <tr>
                            <td class="label">Modèle</td>
                            <td class="value">{{ $vehicle->model }}</td>
                        </tr>
                        <tr>
                            <td class="label">Type</td>
                            <td class="value">{{ optional($vehicle->vehicleType)->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Couleur</td>
                            <td class="value">{{ $vehicle->color ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Places</td>
                            <td class="value">{{ $vehicle->seats }}</td>
                        </tr>
                        <tr>
                            <td class="label">Dépôt</td>
                            <td class="value">{{ optional($vehicle->depot)->name ?? 'Non assigné' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">Données Financières</div>
                    <table class="data-grid">
                        <tr>
                            <td class="label">Achat</td>
                            <td class="value">{{ $vehicle->acquisition_date ? $vehicle->acquisition_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Prix d'achat</td>
                            <td class="value">{{ number_format($vehicle->purchase_price, 0, ',', ' ') }} DA</td>
                        </tr>
                        <tr>
                            <td class="label">Valeur actuelle</td>
                            <td class="value">{{ number_format($vehicle->current_value, 0, ',', ' ') }} DA</td>
                        </tr>
                        <tr>
                            <td class="label">Coût Maintenance</td>
                            <td class="value">{{ number_format($analytics['maintenance_cost_total'] ?? 0, 0, ',', ' ') }} DA</td>
                        </tr>
                    </table>
                </div>

            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 15px;">

                <div class="section">
                    <div class="section-title">Spécifications Techniques</div>
                    <table class="data-grid">
                        <tr>
                            <td class="label">Carburant</td>
                            <td class="value">{{ optional($vehicle->fuelType)->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Transmission</td>
                            <td class="value">{{ optional($vehicle->transmissionType)->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Cylindrée</td>
                            <td class="value">{{ $vehicle->engine_displacement_cc }} cc</td>
                        </tr>
                        <tr>
                            <td class="label">Puissance</td>
                            <td class="value">{{ $vehicle->power_hp }} ch</td>
                        </tr>
                        <tr>
                            <td class="label">Km Initial</td>
                            <td class="value">{{ number_format($vehicle->initial_mileage, 0, ',', ' ') }} km</td>
                        </tr>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">Statistiques</div>
                    <table class="data-grid">
                        <tr>
                            <td class="label">Jours en service</td>
                            <td class="value">{{ $analytics['days_in_service'] ?? 0 }} j</td>
                        </tr>
                        <tr>
                            <td class="label">Km parcourus</td>
                            <td class="value">{{ number_format($analytics['total_km_driven'] ?? 0, 0, ',', ' ') }} km</td>
                        </tr>
                        <tr>
                            <td class="label">Affectations</td>
                            <td class="value">{{ $analytics['assignments_count'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="label">Interventions</td>
                            <td class="value">{{ $analytics['maintenance_count'] ?? 0 }}</td>
                        </tr>
                    </table>
                </div>

            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Affectation Actuelle</div>
        @if($driver)
        <div style="background: #f9fafb; padding: 10px; border-radius: 6px; border: 1px solid #f3f4f6;">
            <strong>{{ $driver->user->name ?? 'Nom Inconnu' }}</strong>
            <div class="text-xs text-gray-500">
                Depuis le {{ $analytics['active_assignment']->start_datetime->format('d/m/Y') }}
            </div>
        </div>
        @else
        <div class="text-sm text-gray-500 italic">Aucune affectation en cours</div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Dernières Activités</div>
        <table class="table-pro">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Détails</th>
                </tr>
            </thead>
            <tbody>
                {{-- Assignments --}}
                @foreach($vehicle->assignments->take(3) as $assign)
                <tr>
                    <td>Affectation</td>
                    <td>{{ $assign->start_datetime->format('d/m/Y') }}</td>
                    <td>
                        {{ $assign->driver->user->name ?? 'Inconnu' }}
                        @if($assign->end_datetime) (Terminé le {{ $assign->end_datetime->format('d/m/Y') }}) @else (En cours) @endif
                    </td>
                </tr>
                @endforeach
                {{-- Maintenance --}}
                @foreach($vehicle->maintenanceOperations->take(3) as $maint)
                <tr>
                    <td>Maintenance</td>
                    <td>{{ $maint->scheduled_date->format('d/m/Y') }}</td>
                    <td>{{ $maint->description }} ({{ number_format($maint->total_cost, 0, ',', ' ') }} DA)</td>
                </tr>
                @endforeach
                @if($vehicle->assignments->isEmpty() && $vehicle->maintenanceOperations->isEmpty())
                <tr>
                    <td colspan="3" style="text-align: center; font-style: italic; color: #9ca3af;">Aucune activité récente</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($vehicle->notes)
    <div class="section">
        <div class="section-title">Notes</div>
        <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded border border-gray-100 italic">
            "{{ $vehicle->notes }}"
        </div>
    </div>
    @endif

    <div class="footer">
        Document généré automatiquement par ZenFleet • {{ now()->format('d/m/Y H:i') }} • Page 1/1
    </div>

</body>

</html>