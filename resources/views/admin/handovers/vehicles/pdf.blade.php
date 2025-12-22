<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Remise N° {{ $handoverForm->assignment->id }}</title>
    <style>
        /* Reset et Styles de Base */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #333;
            background: #fff;
            line-height: 1.3;
        }

        /* Layout Principal */
        #print-area {
            width: 100%;
            padding: 10mm;
            margin: 0 auto;
        }

        /* Helpers */
        .row {
            display: table;
            width: 100%;
            clear: both;
            margin-bottom: 3mm;
        }

        .col-half {
            float: left;
            width: 48%;
        }

        .col-half:last-child {
            float: right;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .text-small {
            font-size: 8pt;
        }

        .mb-2 {
            margin-bottom: 2mm;
        }

        .mt-2 {
            margin-top: 2mm;
        }

        /* Header Compact */
        header {
            border-bottom: 1px solid #333;
            padding-bottom: 1mm;
            margin-bottom: 1mm;
            /* Reduced from 2mm */
        }

        h1 {
            font-size: 10pt;
            /* Reduced from 13pt/14pt */
            margin: 0;
            color: #000;
            text-transform: uppercase;
        }

        /* ... existing styles ... */

        /* Checkbox simulée */
        .checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #333;
            background: #fff;
            margin: 0 auto;
        }

        .checkbox.checked {
            background: #fff;
            /* Keep white background for X */
            position: relative;
        }

        .checkbox.checked::after {
            content: "X";
            color: #000;
            font-size: 8px;
            font-weight: bold;
            position: absolute;
            top: -1px;
            left: 1px;
            line-height: 1;
        }

        /* ... existing styles ... */

        /* Footer */
        footer {
            margin-top: 3mm;
            border-top: 1px solid #ccc;
            padding-top: 2mm;
        }

        .meta-info {
            font-size: 8pt;
            color: #666;
            margin-top: 1mm;
        }

        /* Sections */
        .section-box {
            border: 1px solid #ddd;
            padding: 3mm;
            border-radius: 3px;
            background-color: #fbfbfb;
            margin-bottom: 3mm;
        }

        .section-title {
            font-size: 10pt;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 1mm;
            margin-bottom: 2mm;
            color: #444;
            text-transform: uppercase;
        }

        /* Tables de Checklist */
        table.checklist-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 4mm;
        }

        table.checklist-table th {
            background-color: #eee;
            border: 1px solid #ccc;
            padding: 2px 4px;
            text-align: center;
        }

        table.checklist-table th.item-col {
            text-align: left;
            width: 40%;
        }

        table.checklist-table td {
            border: 1px solid #eee;
            padding: 2px 4px;
            vertical-align: middle;
        }

        table.checklist-table td.status-cell {
            text-align: center;
            width: 15%;
            border-left: 1px solid #ccc;
        }

        /* Checkbox simulée */
        .checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #333;
            background: #fff;
            margin: 0 auto;
        }

        .checkbox.checked {
            background: #fff;
            position: relative;
        }

        .checkbox.checked::after {
            content: "X";
            color: #000;
            font-size: 8px;
            font-weight: bold;
            position: absolute;
            top: -1px;
            left: 1px;
            line-height: 1;
        }

        /* Vehicle & Driver Info Grid */
        table.info-table {
            width: 100%;
            margin-bottom: 0;
        }

        table.info-table td {
            padding: 1mm;
            vertical-align: top;
        }

        table.info-table .label {
            color: #666;
            font-size: 7pt;
            text-transform: uppercase;
        }

        table.info-table .value {
            font-weight: bold;
            font-size: 9pt;
        }

        /* Sketch & Obs */
        .sketch-container {
            text-align: center;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #eee;
            background: #fff;
            padding: 2mm;
        }

        .sketch-container img {
            max-height: 40mm;
            max-width: 100%;
        }

        .obs-box {
            border: 1px solid #eee;
            padding: 2mm;
            background: #fff;
            height: 40mm;
            font-size: 8pt;
        }

        /* Footer */
        footer {
            margin-top: 3mm;
            border-top: 1px solid #ccc;
            padding-top: 2mm;
        }

        .signature-area {
            height: 12mm;
            border-bottom: 1px dotted #ccc;
            margin-top: 2mm;
        }
    </style>
</head>

<body>
    <div id="print-area">
        <!-- Header -->
        <header>
            <div class="row" style="margin-bottom: 0;">
                <div style="float:left; width: 60%;">
                    <h1>FICHE DE REMISE VÉHICULE</h1>
                    <div class="meta-info">
                        Affectation N°: <strong>{{ $handoverForm->assignment->id }}</strong>
                    </div>
                </div>
                <div style="float:right; width: 40%; text-align: right;">
                    <div style="font-size: 10pt; font-weight: bold;">{{ $handoverForm->assignment->organization->name }}</div>
                </div>
            </div>
        </header>

        <!-- Info Grid (Driver & Vehicle) -->
        <div class="row">
            <div class="col-half section-box">
                <div class="section-title">CONDUCTEUR</div>
                <table class="info-table">
                    <tr>
                        <td>
                            <div class="label">Nom & Prénom</div>
                            <div class="value">{{ $handoverForm->assignment->driver->first_name }} {{ $handoverForm->assignment->driver->last_name }}</div>
                            <div class="text-small" style="color: #555; margin-top: 1mm;">
                                <em>Depuis le: {{ $handoverForm->assignment->start_datetime->format('d/m/Y') }}</em>
                            </div>
                        </td>
                        <td>
                            <div class="label">Téléphone</div>
                            <div class="value">{{ $handoverForm->assignment->driver->personal_phone ?? '-' }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-half section-box">
                <div class="section-title">VÉHICULE</div>
                <table class="info-table">
                    <tr>
                        <td>
                            <div class="label">Véhicule</div>
                            <div class="value">{{ $handoverForm->assignment->vehicle->brand }} {{ $handoverForm->assignment->vehicle->model }}</div>
                            <div class="text-small text-gray-500">{{ $handoverForm->assignment->vehicle->registration_plate }}</div>
                        </td>
                        <td class="text-right">
                            <div class="label">Kilométrage</div>
                            <div class="value">{{ number_format($handoverForm->current_mileage, 0, ',', ' ') }} km</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Sketch & Observations -->
        <div class="row">
            <div class="col-half">
                <div class="section-title" style="margin-bottom:1mm;">ÉTAT CARROSSERIE</div>
                <div class="sketch-container">
                    <img src="{{ $vehicle_sketch_base64 ?? '' }}" alt="Croquis">
                </div>
            </div>
            <div class="col-half">
                <div class="section-title" style="margin-bottom:1mm;">OBSERVATIONS GÉNÉRALES</div>
                <div class="obs-box">
                    {{ $handoverForm->general_observations ?: 'R.A.S' }}
                </div>
            </div>
        </div>

        <!-- Checklist Grid -->
        <div class="row">
            <div class="section-title">CHECKLIST DE CONTRÔLE</div>

            @php
            // Split categories into two columns for better layout
            $categories = $checklist->keys();
            $half = ceil($categories->count() / 2);
            $leftCats = $categories->slice(0, $half);
            $rightCats = $categories->slice($half);
            @endphp

            <!-- Left Column -->
            <div class="col-half" style="width: 49%; margin-right: 1%;">
                @foreach($leftCats as $cat)
                @include('admin.handovers.vehicles.partials.pdf-checklist-table', ['category' => $cat, 'details' => $checklist[$cat], 'config' => $checklistStructure[$cat] ?? []])
                @endforeach
            </div>

            <!-- Right Column -->
            <div class="col-half" style="width: 49%;">
                @foreach($rightCats as $cat)
                @include('admin.handovers.vehicles.partials.pdf-checklist-table', ['category' => $cat, 'details' => $checklist[$cat], 'config' => $checklistStructure[$cat] ?? []])
                @endforeach
            </div>
        </div>

        <!-- Signatures -->
        <footer>
            <div class="row">
                <div style="float:left; width: 32%; text-align: center;">
                    <div class="font-bold text-small">Le Conducteur</div>
                    <div class="text-small" style="color:#666;">(Lu et approuvé)</div>
                    <div class="signature-area"></div>
                    <div class="text-small">{{ $handoverForm->assignment->driver->first_name }} {{ $handoverForm->assignment->driver->last_name }}</div>
                </div>
                <div style="float:left; width: 32%; margin-left:2%; text-align: center;">
                    <div class="font-bold text-small">Responsable Parc</div>
                    <div class="signature-area"></div>
                </div>
                <div style="float:right; width: 32%; text-align: center;">
                    <div class="font-bold text-small">Responsable Hiérarchique</div>
                    <div class="signature-area"></div>
                </div>
            </div>
            <div class="text-center text-small" style="color:#999; margin-top:2mm;">
                Document généré automatiquement le {{ now()->format('d/m/Y H:i') }} | Page 1/1
            </div>
        </footer>
    </div>
</body>

</html>