<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affectation #{{ $assignment->id }}</title>
    <style>
        /* 🎨 DESIGN ULTRA-PROFESSIONNEL - Enterprise Document Grade */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 12mm 12mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.4;
            color: #000000;
            background: #ffffff;
        }

        /* HEADER COMPACT */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1.5pt solid #000000;
        }

        .header-left {
            flex: 1;
        }

        .header-logo {
            max-width: 120px;
            max-height: 40px;
        }

        .company-name {
            font-size: 16pt;
            font-weight: 700;
            color: #000000;
            letter-spacing: -0.02em;
        }

        .header-right {
            text-align: right;
        }

        .doc-title {
            font-size: 11pt;
            font-weight: 700;
            color: #000000;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .doc-number {
            font-size: 9pt;
            color: #666666;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            margin-top: 6px;
            border: 1pt solid #000000;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: #000000;
            color: #ffffff;
        }

        /* PERIOD BAR - Ultra compact */
        .period-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            margin-bottom: 16px;
            background: #f5f5f5;
            border: 0.5pt solid #cccccc;
        }

        .period-item {
            text-align: center;
            flex: 1;
        }

        .period-label {
            font-size: 6.5pt;
            color: #666666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 3px;
        }

        .period-value {
            font-size: 9pt;
            color: #000000;
            font-weight: 700;
        }

        .period-date {
            font-size: 7pt;
            color: #666666;
            margin-top: 2px;
        }

        .period-separator {
            width: 1px;
            height: 30px;
            background: #cccccc;
            margin: 0 8px;
        }

        /* GRID LAYOUT - 2 colonnes */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 14px;
        }

        /* SECTION */
        .section {
            margin-bottom: 14px;
        }

        .section-header {
            font-size: 9pt;
            font-weight: 700;
            color: #000000;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 0.75pt solid #000000;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* INFO BLOCK */
        .info-block {
            background: #ffffff;
            border: 0.5pt solid #dddddd;
            padding: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 0.25pt solid #eeeeee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 7pt;
            color: #666666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            width: 45%;
        }

        .info-value {
            font-size: 8pt;
            color: #000000;
            font-weight: 500;
            text-align: right;
            width: 55%;
        }

        .info-value-highlight {
            font-size: 10pt;
            font-weight: 700;
            color: #000000;
        }

        /* TABLE CONDENSED */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .data-table th {
            background: #f5f5f5;
            border: 0.5pt solid #cccccc;
            padding: 6px 8px;
            text-align: left;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #333333;
        }

        .data-table td {
            border: 0.5pt solid #eeeeee;
            padding: 6px 8px;
            color: #000000;
        }

        .data-table tr:nth-child(even) {
            background: #fafafa;
        }

        /* 3 COLUMNS GRID - Pour informations denses */
        .grid-3col {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }

        .data-cell {
            border: 0.5pt solid #dddddd;
            padding: 8px;
            background: #ffffff;
        }

        .data-cell-label {
            font-size: 6.5pt;
            color: #666666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 3px;
        }

        .data-cell-value {
            font-size: 8.5pt;
            color: #000000;
            font-weight: 600;
        }

        /* FOOTER COMPACT */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 12mm;
            background: #f5f5f5;
            border-top: 0.5pt solid #cccccc;
            font-size: 6.5pt;
            color: #666666;
            display: flex;
            justify-content: space-between;
        }

        /* UTILITY */
        .text-mono {
            font-family: 'Courier New', monospace;
            letter-spacing: 0.02em;
        }

        .text-bold {
            font-weight: 700;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .mb-1 {
            margin-bottom: 8px;
        }

        .divider {
            height: 0.5pt;
            background: #dddddd;
            margin: 10px 0;
        }

        /* PRINT OPTIMIZATION */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .section {
                page-break-inside: avoid;
            }

            .footer {
                position: running(footer);
            }
        }

        /* ALERT MINIMAL */
        .alert-minimal {
            padding: 6px 10px;
            background: #f5f5f5;
            border-left: 2pt solid #000000;
            font-size: 7pt;
            color: #333333;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    {{-- HEADER ULTRA-COMPACT --}}
    <div class="header">
        <div class="header-left">
            @if($logo_base64)
                <img src="{{ $logo_base64 }}" alt="Logo" class="header-logo">
            @else
                <div class="company-name">ZenFleet</div>
            @endif
        </div>
        <div class="header-right">
            <div class="doc-title">Affectation de Véhicule</div>
            <div class="doc-number">N° {{ str_pad($assignment->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="status-badge">{{ $assignment->status_label }}</div>
        </div>
    </div>

    {{-- PERIOD BAR - Une ligne compacte --}}
    <div class="period-bar">
        <div class="period-item">
            <div class="period-label">Début</div>
            <div class="period-value">{{ $assignment->start_datetime->format('d/m/Y') }}</div>
            <div class="period-date">{{ $assignment->start_datetime->format('H:i') }}</div>
        </div>
        <div class="period-separator"></div>
        <div class="period-item">
            <div class="period-label">Durée</div>
            <div class="period-value">{{ $duration['formatted'] }}</div>
            @if($duration['is_ongoing'])
                <div class="period-date">En cours</div>
            @endif
        </div>
        <div class="period-separator"></div>
        <div class="period-item">
            <div class="period-label">Fin</div>
            @if($assignment->end_datetime)
                <div class="period-value">{{ $assignment->end_datetime->format('d/m/Y') }}</div>
                <div class="period-date">{{ $assignment->end_datetime->format('H:i') }}</div>
            @else
                <div class="period-value">Indéterminée</div>
                <div class="period-date">Ouverte</div>
            @endif
        </div>
    </div>

    {{-- GRID 3 COLONNES - Informations principales --}}
    <div class="grid-3col">
        <div class="data-cell">
            <div class="data-cell-label">Immatriculation</div>
            <div class="data-cell-value text-uppercase">{{ $assignment->vehicle->registration_plate }}</div>
        </div>
        <div class="data-cell">
            <div class="data-cell-label">Véhicule</div>
            <div class="data-cell-value">{{ $assignment->vehicle->brand }} {{ $assignment->vehicle->model }}</div>
        </div>
        <div class="data-cell">
            <div class="data-cell-label">Type</div>
            <div class="data-cell-value">{{ $assignment->vehicle->vehicleType->name ?? '—' }}</div>
        </div>
        <div class="data-cell">
            <div class="data-cell-label">Chauffeur</div>
            <div class="data-cell-value text-uppercase">{{ $assignment->driver->first_name }} {{ $assignment->driver->last_name }}</div>
        </div>
        <div class="data-cell">
            <div class="data-cell-label">Permis N°</div>
            <div class="data-cell-value text-mono">{{ $assignment->driver->license_number ?? '—' }}</div>
        </div>
        <div class="data-cell">
            <div class="data-cell-label">Téléphone</div>
            <div class="data-cell-value">{{ $assignment->driver->personal_phone ?? '—' }}</div>
        </div>
    </div>

    {{-- SECTION DÉTAILS VÉHICULE ET CHAUFFEUR - 2 colonnes --}}
    <div class="info-grid">
        {{-- Colonne Véhicule --}}
        <div class="section">
            <div class="section-header">Véhicule</div>
            <div class="info-block">
                <div class="info-row">
                    <div class="info-label">Marque/Modèle</div>
                    <div class="info-value">{{ $assignment->vehicle->brand }} {{ $assignment->vehicle->model }}</div>
                </div>
                @if($assignment->vehicle->vin)
                <div class="info-row">
                    <div class="info-label">VIN</div>
                    <div class="info-value text-mono" style="font-size: 7pt;">{{ $assignment->vehicle->vin }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">KM Début</div>
                    <div class="info-value text-bold">{{ number_format($assignment->start_mileage ?? $assignment->vehicle->current_mileage ?? 0, 0, ',', ' ') }} km</div>
                </div>
                @if($assignment->end_mileage)
                <div class="info-row">
                    <div class="info-label">KM Fin</div>
                    <div class="info-value text-bold">{{ number_format($assignment->end_mileage, 0, ',', ' ') }} km</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Parcouru</div>
                    <div class="info-value text-bold">{{ number_format($assignment->end_mileage - ($assignment->start_mileage ?? 0), 0, ',', ' ') }} km</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Colonne Chauffeur --}}
        <div class="section">
            <div class="section-header">Chauffeur</div>
            <div class="info-block">
                <div class="info-row">
                    <div class="info-label">Nom Complet</div>
                    <div class="info-value text-bold text-uppercase">{{ $assignment->driver->first_name }} {{ $assignment->driver->last_name }}</div>
                </div>
                @if($assignment->driver->license_number)
                <div class="info-row">
                    <div class="info-label">Permis</div>
                    <div class="info-value text-mono">{{ $assignment->driver->license_number }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Téléphone</div>
                    <div class="info-value">{{ $assignment->driver->personal_phone ?? '—' }}</div>
                </div>
                @if($assignment->driver->driverStatus)
                <div class="info-row">
                    <div class="info-label">Statut</div>
                    <div class="info-value">{{ $assignment->driver->driverStatus->name }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SECTION AFFECTATION --}}
    <div class="section">
        <div class="section-header">Détails de l'Affectation</div>
        <table class="data-table">
            <tr>
                <th style="width: 30%;">Motif</th>
                <td>{{ $assignment->reason ?? 'Non renseigné' }}</td>
            </tr>
            @if($assignment->notes)
            <tr>
                <th>Notes</th>
                <td>{{ Str::limit($assignment->notes, 200) }}</td>
            </tr>
            @endif
            <tr>
                <th>Statut</th>
                <td class="text-bold text-uppercase">{{ $assignment->status_label }}</td>
            </tr>
            @if($duration['hours'])
            <tr>
                <th>Durée Totale</th>
                <td>{{ number_format($duration['hours'], 1, ',', ' ') }} heures</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- SECTION AUDIT - Ultra compact --}}
    <div class="section">
        <div class="section-header">Traçabilité</div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; font-size: 7pt;">
            <div>
                <span style="color: #666666; font-weight: 600;">Créé le:</span>
                <span style="color: #000000; font-weight: 500;">{{ $assignment->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div>
                <span style="color: #666666; font-weight: 600;">Par:</span>
                <span style="color: #000000; font-weight: 500;">{{ $assignment->creator->name ?? 'Système' }}</span>
            </div>
            @if($assignment->ended_at)
            <div>
                <span style="color: #666666; font-weight: 600;">Terminé:</span>
                <span style="color: #000000; font-weight: 500;">{{ $assignment->ended_at->format('d/m/Y H:i') }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ALERT SI EN COURS - Minimal --}}
    @if($duration['is_ongoing'])
    <div class="alert-minimal">
        <strong>INFO:</strong> Affectation en cours. Les données de fin seront disponibles après terminaison.
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        <div>Document généré le {{ $generated_at->format('d/m/Y à H:i') }} par {{ $generated_by }}</div>
        <div>ZenFleet – Gestion de Flotte Professionnelle</div>
    </div>
</body>
</html>
