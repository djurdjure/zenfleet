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
            font-size: 10pt;
            color: #333;
            background-color: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        #print-area {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
        }

        h1,
        h2,
        h3 {
            font-weight: bold;
            color: #000;
        }

        h1 {
            font-size: 20pt;
            margin-bottom: 5mm;
        }

        h2 {
            font-size: 14pt;
            margin-bottom: 4mm;
            border-bottom: 1px solid #eee;
            padding-bottom: 2mm;
        }

        h3 {
            font-size: 11pt;
            margin-bottom: 2mm;
            color: #444;
        }

        /* En-tête */
        header {
            border-bottom: 2px solid #000;
            padding-bottom: 5mm;
        }

        header .header-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        header .meta {
            font-size: 9pt;
            color: #666;
        }

        header .company-info p,
        header .document-info p {
            margin: 0;
        }

        header .company-info {
            font-weight: bold;
            font-size: 12pt;
        }

        header .document-info {
            text-align: right;
        }

        /* Contenu Principal */
        main {
            flex-grow: 1;
            margin-top: 8mm;
        }

        /* Section des Parties (Conducteur/Véhicule) */
        #parties {
            display: flex;
            justify-content: space-between;
            background-color: #f8f9fa;
            padding: 4mm;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .party-info {
            width: 48%;
        }

        .party-info .name {
            font-weight: bold;
            font-size: 11pt;
        }

        .party-info .role {
            font-size: 8pt;
            color: #555;
            text-transform: uppercase;
            margin-bottom: 1mm;
        }

        .party-info .contact {
            font-size: 9pt;
            color: #666;
        }

        .party-info.vehicle {
            text-align: right;
        }

        /* Section d'Inspection */
        #inspection {
            margin-top: 8mm;
            display: flex;
            gap: 8mm;
            align-items: flex-start;
        }

        .visuals {
            flex: 1;
        }

        .visuals .content {
            display: flex;
            gap: 5mm;
            border: 1px solid #dee2e6;
            padding: 4mm;
            border-radius: 4px;
        }

        .sketch {
            flex: 0 0 45%;
            text-align: center;
        }

        .sketch img {
            max-width: 100%;
            border: 1px solid #ccc;
        }

        .sketch .caption {
            font-size: 8pt;
            color: #777;
            margin-top: 2mm;
        }

        .observations {
            flex: 1;
        }

        .observations strong {
            font-size: 10pt;
        }

        .observation-text {
            background-color: #f8f9fa;
            padding: 3mm;
            border-radius: 3px;
            min-height: 40mm;
            font-size: 9pt;
            white-space: pre-wrap;
            margin-top: 1mm;
            border: 1px solid #e9ecef;
        }

        .checklist {
            flex: 1;
        }

        .checklist .categories {
            column-count: 1;
        }

        .checklist .category {
            break-inside: avoid;
            margin-bottom: 5mm;
        }

        .checklist ul {
            list-style: none;
        }

        .checklist li {
            display: flex;
            justify-content: space-between;
            padding: 1.5mm 0;
            border-bottom: 1px dotted #ccc;
        }

        .checklist .item {
            color: #333;
        }

        .checklist .status {
            font-weight: bold;
            color: #000;
        }

        /* Pied de page */
        footer {
            margin-top: auto;
            padding-top: 10mm;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .signature-box {
            width: 30%;
            padding-top: 5mm;
            border-top: 1.5px solid #000;
        }

        .signature-box .name {
            font-weight: bold;
            margin-bottom: 1mm;
            min-height: 12pt;
        }

        .signature-box .role {
            font-size: 8pt;
            color: #666;
        }

        /* Media print pour ajustements fins */
        @media print {
            body {
                background-color: white;
            }

            #print-area {
                margin: 0;
                padding: 10mm;
                /* Marges d'impression */
                box-shadow: none;
                width: 100%;
                height: auto;
                min-height: 0;
            }
        }
    </style>
</head>

<body>

    {{-- Ce conteneur représente la page A4 et contient tout le contenu à imprimer. --}}
    <div id="print-area">

        <header>
            <h1>FICHE DE REMISE VÉHICULE</h1>
            <div class="header-details">
                <div class="company-info">
                    <p>{{ $handoverForm->assignment->organization->name }}</p>
                    <p class="meta">Affectation N°: {{ $handoverForm->assignment->id }}</p>
                </div>
                <div class="document-info">
                    <p class="meta">Document généré le {{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
        </header>

        <main>
            <section id="parties">
                <div class="party-info driver">
                    <p class="name">{{ $handoverForm->assignment->driver->first_name }} {{ $handoverForm->assignment->driver->last_name }}</p>
                    <p class="role">Conducteur</p>
                    <p class="contact">{{ $handoverForm->assignment->driver->personal_phone ?? 'N/A' }}</p>
                </div>
                <div class="party-info vehicle">
                    <p class="name">{{ $handoverForm->assignment->vehicle->brand }} {{ $handoverForm->assignment->vehicle->model }} ({{ $handoverForm->assignment->vehicle->registration_plate }})</p>
                    <p class="role">Véhicule</p>
                    <p class="contact">Remis le: {{ $handoverForm->issue_date->format('d/m/Y') }} | {{ number_format($handoverForm->current_mileage, 0, ',', ' ') }} km</p>
                </div>
            </section>

            <section id="inspection">
                <div class="visuals">
                    <h2>État Visuel et Observations</h2>
                    <div class="content">
                        <div class="sketch">
                            @php
                            // La conversion en base64 se fera dans le contrôleur
                            $sketchAsset = $handoverForm->assignment->vehicle->vehicleType->name === 'Moto' ? 'images/scooter_sketch.png' : 'images/car_sketch.png';
                            @endphp
                            <img src="{{ $vehicle_sketch_base64 ?? '' }}" alt="Croquis du véhicule">
                            <p class="caption">Cocher les défauts constatés</p>
                        </div>
                        <div class="observations">
                            <strong>Observations générales:</strong>
                            <p class="observation-text">{{ $handoverForm->general_observations ?: 'Aucune observation particulière.' }}</p>
                        </div>
                    </div>
                </div>

                <div class="checklist">
                    <h2>Checklist de Contrôle</h2>
                    <div class="categories">
                        @foreach($checklist as $category => $items)
                        <div class="category">
                            <h3>{{ $category }}</h3>
                            <ul>
                                @foreach($items as $detail)
                                <li>
                                    <span class="item">{{ $detail->item }}</span>
                                    <span class="status">{{ $detail->status }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <div class="signatures">
                <div class="signature-box">
                    <p class="name">{{ $handoverForm->assignment->driver->first_name }} {{ $handoverForm->assignment->driver->last_name }}</p>
                    <p class="role">(Chauffeur)</p>
                </div>
                <div class="signature-box">
                    <p class="name">(Nom & Prénom)</p>
                    <p class="role">(Responsable Hiérarchique)</p>
                </div>
                <div class="signature-box">
                    <p class="name">(Nom & Prénom)</p>
                    <p class="role">(Responsable Parc)</p>
                </div>
            </div>
        </footer>

    </div>

</body>

</html>