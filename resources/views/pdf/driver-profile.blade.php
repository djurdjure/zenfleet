<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Personnel Chauffeur - {{ $driver->full_name }}</title>
    <style>
        /* RESET & BASE */
        @page {
            margin: 0;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            margin: 0;
            padding: 15mm;
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #111;
            background-color: #fff;
        }

        /* COLORS (STRICT MONOCHROME) */
        .text-black {
            color: #000;
        }

        .text-dark {
            color: #333;
        }

        .text-medium {
            color: #666;
        }

        .text-light {
            color: #888;
        }

        .bg-light {
            background-color: #f8f8f8;
        }

        .bg-medium {
            background-color: #f0f0f0;
        }

        .border-light {
            border-color: #e5e5e5;
        }

        .border-black {
            border-color: #000;
        }

        /* UTILITIES */
        .uppercase {
            text-transform: uppercase;
        }

        .bold {
            font-weight: 700;
        }

        .ultra-bold {
            font-weight: 800;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .w-full {
            width: 100%;
        }

        .flex {
            display: flex;
        }

        .hidden-print {
            display: none;
        }

        /* Pour masquer les éléments à l'impression si nécessaire */

        /* HEADER (10%) */
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: table;
        }

        .header-col {
            display: table-cell;
            vertical-align: bottom;
        }

        .header-left {
            text-align: left;
            width: 25%;
        }

        .header-center {
            text-align: center;
            width: 50%;
        }

        .header-right {
            text-align: right;
            width: 25%;
        }

        .org-name {
            font-size: 16pt;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .doc-title {
            font-size: 10pt;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .meta-label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
        }

        .meta-value {
            font-size: 9pt;
            font-weight: 600;
        }

        /* IDENTITY BLOCK (20%) */
        .identity-block {
            text-align: center;
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
        }

        .photo-container {
            display: inline-block;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 1px solid #333;
            overflow: hidden;
            margin-bottom: 10px;
            background-color: #fff;
            vertical-align: middle;
        }

        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(100%);
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24pt;
            font-weight: 700;
            color: #ccc;
            background-color: #f0f0f0;
            line-height: 100px;
        }

        .driver-name {
            font-size: 24pt;
            font-weight: 900;
            line-height: 1.1;
            margin: 10px 0 5px 0;
            color: #000;
            letter-spacing: -0.5px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #eee;
            border: 1px solid #ccc;
            font-size: 9pt;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 20px;
            color: #333;
        }

        /* KEY METRICS (15%) */
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-left: -5px;
            margin-right: -5px;
        }

        .metric-card {
            display: table-cell;
            width: 25%;
            background-color: #f8f8f8;
            border: 1px solid #eee;
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }

        .metric-label {
            display: block;
            font-size: 8pt;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }

        .metric-value {
            display: block;
            font-size: 14pt;
            font-weight: 800;
            color: #000;
        }

        .alert-border {
            border: 2px solid #000;
            background-color: #fff;
        }

        /* MAIN CONTENT STRUCTURE (55%) */
        .section-header {
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #000;
        }

        /* DATA TABLES */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 6px 0;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .label-col {
            width: 35%;
            color: #555;
            font-size: 9pt;
        }

        .value-col {
            width: 65%;
            font-weight: 600;
            color: #000;
            font-size: 10pt;
        }

        /* LICENSE TAGS */
        .license-tag {
            display: inline-block;
            padding: 2px 6px;
            background-color: #ddd;
            border: 1px solid #ccc;
            font-weight: 700;
            font-size: 8pt;
            margin-right: 4px;
            border-radius: 2px;
        }

        /* SANCTIONS & NOTES */
        .notes-box {
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #ccc;
            font-style: italic;
            font-size: 9pt;
            color: #444;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
            padding-bottom: 0;
            margin: 0 15mm 10mm 15mm;
            background: #fff;
        }

        .footer-text {
            font-size: 8pt;
            color: #888;
        }

        /* TWO COLUMN LAYOUT SIMULATION */
        .cols-container {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-spacing: 20px 0;
            margin-left: -10px;
        }

        .col-left {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }

        .col-right {
            display: table-cell;
            width: 55%;
            vertical-align: top;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-col header-left">
            <div class="org-name">{{ $organization->name ?? 'ZENFLEET' }}</div>
        </div>
        <div class="header-col header-center">
            <div class="doc-title">FICHE PERSONNEL CHAUFFEUR</div>
        </div>
        <div class="header-col header-right">
            <div class="meta-label">Matricule</div>
            <div class="meta-value">{{ $driver->employee_number ?? 'N/A' }}</div>
            <div style="height: 4px;"></div>
            <div class="meta-label">Généré le</div>
            <div class="meta-value">{{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    <!-- IDENTITY BLOCK -->
    <div class="identity-block">
        <div class="photo-container">
            @if(isset($photoBase64) && $photoBase64)
            <img src="{{ $photoBase64 }}" class="photo-img" alt="Photo">
            @elseif($driver->photo)
            <img src="{{ $driver->photo }}" class="photo-img" alt="Photo">
            @else
            <div class="photo-placeholder">
                {{ strtoupper(substr($driver->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($driver->last_name ?? '', 0, 1)) }}
            </div>
            @endif
        </div>
        <div class="driver-name uppercase">{{ $driver->full_name }}</div>
        <div class="status-badge">
            {{ $driver->driverStatus?->name ?? 'Statut Inconnu' }}
        </div>
    </div>

    <!-- CALCUL DE L'ANCIENNETÉ (LOGIQUE BLADE) -->
    @php
    $seniority = 'N/A';
    if ($driver->recruitment_date) {
    $diff = $driver->recruitment_date->diff(now());
    $seniority = '';
    if ($diff->y > 0) {
    $seniority .= $diff->y . ' An' . ($diff->y > 1 ? 's' : '') . ' ';
    }
    $seniority .= $diff->m . ' Mois';
    }

    $licenseAlert = false;
    if ($driver->license_expiry_date) {
    $daysToExpiry = now()->diffInDays($driver->license_expiry_date, false);
    if ($daysToExpiry < 60) {
        $licenseAlert=true;
        }
        }
        @endphp

        <!-- KEY METRICS -->
        <div class="metrics-grid">
            <div class="metric-card">
                <span class="metric-label">Ancienneté</span>
                <span class="metric-value">{{ $seniority }}</span>
            </div>
            <div class="metric-card">
                <span class="metric-label">Âge</span>
                <span class="metric-value">{{ $driver->birth_date ? $driver->birth_date->age . ' Ans' : 'N/A' }}</span>
            </div>
            <div class="metric-card {{ $licenseAlert ? 'alert-border' : '' }}">
                <span class="metric-label">Permis Expire le</span>
                <span class="metric-value">{{ $driver->license_expiry_date ? $driver->license_expiry_date->format('d/m/Y') : 'N/A' }}</span>
            </div>
            <div class="metric-card">
                <span class="metric-label">Véhicule Actuel</span>
                <span class="metric-value" style="font-size: 11pt;">{{ $driver->activeAssignment->vehicle->registration_plate ?? 'Aucun' }}</span>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="cols-container">

            <!-- COLONNE GAUCHE -->
            <div class="col-left">

                <!-- SECTION 1: COORDONNÉES & URGENCE -->
                <div class="section-header">
                    <div class="section-title">Coordonnées & Urgence</div>
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label-col">Téléphone</td>
                        <td class="value-col" style="font-size: 11pt;">{{ $driver->personal_phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Email</td>
                        <td class="value-col">{{ $driver->personal_email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Adresse</td>
                        <td class="value-col">
                            {{ $driver->address ?? '' }}<br>
                            {{ $driver->postal_code ?? '' }} {{ $driver->city ?? '' }}
                        </td>
                    </tr>
                    @if($driver->emergency_contact_name)
                    <tr>
                        <td style="border-top: 1px solid #ddd; padding-top: 10px;" colspan="2">
                            <div style="font-size: 8pt; text-transform: uppercase; color: #666; margin-bottom: 2px;">Contact Urgence</div>
                            <div style="font-weight: 700;">{{ $driver->emergency_contact_name }}</div>
                            <div style="font-size: 9pt;">{{ $driver->emergency_contact_relationship ?? 'Relation non spécifiée' }}</div>
                            <div style="font-weight: 700;">{{ $driver->emergency_contact_phone }}</div>
                        </td>
                    </tr>
                    @endif
                </table>

                <!-- SECTION 4: NOTES & SANCTIONS -->
                <div class="section-header">
                    <div class="section-title">Notes & Discipline</div>
                </div>

                @if($driver->activeSanctions && $driver->activeSanctions->count() > 0)
                <div style="padding: 8px; background-color: #f0f0f0; border: 1px solid #000; margin-bottom: 15px; text-align: center;">
                    <span style="font-weight: 700; font-size: 12pt;">{{ $driver->activeSanctions->count() }}</span>
                    <span style="text-transform: uppercase; font-size: 8pt;">Sanction(s) Active(s)</span>
                </div>
                @endif

                @if($driver->notes)
                <div class="notes-box">
                    "{{ $driver->notes }}"
                </div>
                @else
                <div style="color: #999; font-style: italic; font-size: 9pt;">Aucune note enregistrée.</div>
                @endif

            </div>

            <!-- COLONNE DROITE -->
            <div class="col-right">

                <!-- SECTION 2: INFORMATIONS ADMINISTRATIVES -->
                <div class="section-header">
                    <div class="section-title">Informations Administratives</div>
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label-col">Date de Naissance</td>
                        <td class="value-col">{{ $driver->birth_date ? $driver->birth_date->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Groupe Sanguin</td>
                        <td class="value-col">{{ $driver->blood_type ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Date Recrutement</td>
                        <td class="value-col">{{ $driver->recruitment_date ? $driver->recruitment_date->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    @if($driver->contract_end_date)
                    <tr>
                        <td class="label-col">Fin de Contrat</td>
                        <td class="value-col">{{ $driver->contract_end_date->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label-col">Superviseur</td>
                        <td class="value-col">{{ $driver->supervisor->name ?? 'Non assigné' }}</td>
                    </tr>
                </table>

                <!-- SECTION 3: PERMIS DE CONDUIRE -->
                <div class="section-header">
                    <div class="section-title">Permis de Conduire</div>
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label-col">Numéro Permis</td>
                        <td class="value-col" style="font-family: monospace; font-size: 11pt;">{{ $driver->license_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Délivré le</td>
                        <td class="value-col">{{ $driver->license_issue_date ? $driver->license_issue_date->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Autorité</td>
                        <td class="value-col">{{ $driver->license_authority ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Catégories</td>
                        <td class="value-col">
                            @if($driver->license_categories && is_array($driver->license_categories))
                            @foreach($driver->license_categories as $cat)
                            <span class="license-tag">{{ $cat }}</span>
                            @endforeach
                            @else
                            <span style="color: #999;">Aucune</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Vérification</td>
                        <td class="value-col">
                            @if($driver->license_verified)
                            <span style="font-weight: 700;">VÉRIFIÉ</span>
                            @else
                            <span style="color: #666;">Non vérifié</span>
                            @endif
                        </td>
                    </tr>
                </table>

            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p class="footer-text">
                Document généré par ZenFleet - Système de Gestion de Flotte Enterprise-Grade. Confidentialité assurée.<br>
                Page <span class="page-number">1</span>
            </p>
        </div>

</body>

</html>