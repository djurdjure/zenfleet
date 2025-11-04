<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Report - {{ $vehicle->registration_plate }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #111827;
            line-height: 1.5;
            background: #ffffff;
            margin: 0;
            padding: 15px;
        }
        
        @media print {
            body { 
                margin: 0;
                padding: 10px;
            }
            .no-print { display: none; }
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 40px 30px;
            margin: -15px -15px 25px -15px;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 60%;
            height: 200%;
            background: rgba(255,255,255,0.05);
            transform: rotate(-35deg);
        }
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header .subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        .section {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            page-break-inside: avoid;
        }
        .section h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #dbeafe;
            display: flex;
            align-items: center;
        }
        
        .section h2::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 20px;
            background: #3b82f6;
            margin-right: 10px;
            border-radius: 2px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
        }
        .info-label {
            font-weight: 500;
            color: #6b7280;
            min-width: 140px;
        }
        .info-value {
            color: #111827;
            font-weight: 400;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-disponible { background: #dcfce7; color: #166534; }
        .status-affecte { background: #fed7aa; color: #9a3412; }
        .status-maintenance { background: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ $vehicle->registration_plate }}</h1>
            <div class="subtitle">{{ $vehicle->brand }} {{ $vehicle->model }} - {{ $organization->name }}</div>
        </div>

        {{-- Informations Générales --}}
        <div class="section">
            <h2>Informations Générales</h2>
            <div class="grid">
                <div class="info-row">
                    <span class="info-label">Immatriculation:</span>
                    <span class="info-value">{{ $vehicle->registration_plate }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Marque:</span>
                    <span class="info-value">{{ $vehicle->brand }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Modèle:</span>
                    <span class="info-value">{{ $vehicle->model }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Année:</span>
                    <span class="info-value">{{ $vehicle->manufacturing_year }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Type:</span>
                    <span class="info-value">{{ optional($vehicle->vehicleType)->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Statut:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ strtolower(optional($vehicle->vehicleStatus)->name ?? 'inconnu') }}">
                            {{ optional($vehicle->vehicleStatus)->name ?? 'Inconnu' }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kilométrage:</span>
                    <span class="info-value">{{ number_format($vehicle->current_mileage) }} km</span>
                </div>
                <div class="info-row">
                    <span class="info-label">VIN:</span>
                    <span class="info-value">{{ $vehicle->vin ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Caractéristiques Techniques --}}
        <div class="section">
            <h2>Caractéristiques Techniques</h2>
            <div class="grid">
                <div class="info-row">
                    <span class="info-label">Carburant:</span>
                    <span class="info-value">{{ optional($vehicle->fuelType)->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transmission:</span>
                    <span class="info-value">{{ optional($vehicle->transmissionType)->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Couleur:</span>
                    <span class="info-value">{{ $vehicle->color ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nombre de places:</span>
                    <span class="info-value">{{ $vehicle->seats_count ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Affectation --}}
        <div class="section">
            <h2>Affectation</h2>
            @if($user)
                <div class="grid">
                    <div class="info-row">
                        <span class="info-label">Chauffeur:</span>
                        <span class="info-value">{{ $user->name }} {{ $user->last_name ?? '' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Téléphone:</span>
                        <span class="info-value">{{ $driver->phone ?? $user->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Permis:</span>
                        <span class="info-value">{{ $driver->license_number ?? 'N/A' }}</span>
                    </div>
                </div>
            @else
                <p style="color: #6b7280; font-style: italic;">Aucun chauffeur affecté</p>
            @endif
        </div>

        {{-- Informations Administratives --}}
        <div class="section">
            <h2>Informations Administratives</h2>
            <div class="grid">
                <div class="info-row">
                    <span class="info-label">Dépôt:</span>
                    <span class="info-value">{{ optional($vehicle->depot)->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Catégorie:</span>
                    <span class="info-value">{{ optional($vehicle->category)->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date acquisition:</span>
                    <span class="info-value">
                        {{ $vehicle->acquisition_date ? $vehicle->acquisition_date->format('d/m/Y') : 'N/A' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Coût acquisition:</span>
                    <span class="info-value">
                        {{ $vehicle->acquisition_cost ? number_format($vehicle->acquisition_cost, 2, ',', ' ') . ' €' : 'N/A' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Assurance expire:</span>
                    <span class="info-value">
                        {{ $vehicle->insurance_expiry_date ? $vehicle->insurance_expiry_date->format('d/m/Y') : 'N/A' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Contrôle technique:</span>
                    <span class="info-value">
                        {{ $vehicle->technical_control_expiry_date ? $vehicle->technical_control_expiry_date->format('d/m/Y') : 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
            <p>{{ $organization->name }} - ZenFleet © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
