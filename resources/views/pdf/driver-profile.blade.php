<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Chauffeur - {{ $driver->full_name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        @page {
            margin: 0;
            size: A4;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.5;
        }

        /* A4 Container */
        .page-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            position: relative;
            overflow: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 160px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .profile-photo {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.2);
            background-color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #94a3b8;
            font-weight: 600;
        }

        .driver-identity h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .driver-identity .role {
            margin-top: 5px;
            font-size: 14px;
            opacity: 0.8;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-right {
            text-align: right;
        }

        .company-logo {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .doc-meta {
            font-size: 10px;
            opacity: 0.6;
        }

        /* Content Layout */
        .content {
            padding: 40px;
            display: flex;
            gap: 40px;
        }

        /* Sidebar (Left Column) */
        .sidebar {
            width: 35%;
            flex-shrink: 0;
        }

        /* Main (Right Column) */
        .main-content {
            flex-grow: 1;
        }

        /* Section Styling */
        .section {
            margin-bottom: 35px;
        }

        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        /* Info Items */
        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-size: 10px;
            color: #94a3b8;
            margin-bottom: 3px;
            font-weight: 500;
        }

        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .info-value.highlight {
            color: #0f172a;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-neutral { background: #f1f5f9; color: #475569; }

        /* Timeline / History */
        .timeline-item {
            position: relative;
            padding-left: 20px;
            margin-bottom: 20px;
            border-left: 2px solid #e2e8f0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -5px;
            top: 0;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
        }

        .timeline-item.active {
            border-left-color: #3b82f6;
        }

        .timeline-item.active::before {
            background: #3b82f6;
        }

        .timeline-date {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .timeline-title {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        .timeline-desc {
            font-size: 11px;
            color: #475569;
            margin-top: 2px;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px 40px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .stat-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if($driver->photo)
                    <img src="{{ $driver->photo }}" class="profile-photo" alt="Photo">
                @else
                    <div class="profile-photo">
                        {{ substr($driver->first_name, 0, 1) }}{{ substr($driver->last_name, 0, 1) }}
                    </div>
                @endif
                <div class="driver-identity">
                    <h1>{{ $driver->full_name }}</h1>
                    <div class="role">Chauffeur Professionnel</div>
                    <div style="margin-top: 8px;">
                        <span class="badge {{ $driver->driverStatus?->name === 'Disponible' ? 'badge-success' : ($driver->driverStatus?->name === 'En mission' ? 'badge-warning' : 'badge-danger') }}">
                            {{ $driver->driverStatus?->name ?? 'Statut Inconnu' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="company-logo">ZENFLEET</div>
                <div class="doc-meta">
                    Matricule: <strong>{{ $driver->employee_number }}</strong><br>
                    Généré le: {{ now()->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="section">
                    <div class="section-title">Coordonnées</div>
                    
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $driver->email ?? 'Non renseigné' }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value highlight">{{ $driver->personal_phone ?? 'Non renseigné' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Adresse</div>
                        <div class="info-value">
                            {{ $driver->address ?? '' }}<br>
                            {{ $driver->postal_code }} {{ $driver->city }}
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Informations</div>
                    
                    <div class="info-item">
                        <div class="info-label">Date de Naissance</div>
                        <div class="info-value">
                            {{ $driver->birth_date ? $driver->birth_date->format('d/m/Y') : '-' }}
                            @if($driver->birth_date) <span style="color:#94a3b8">({{ $driver->birth_date->age }} ans)</span> @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Groupe Sanguin</div>
                        <div class="info-value">{{ $driver->blood_type ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Urgence</div>
                        <div class="info-value">
                            {{ $driver->emergency_contact_name ?? '-' }}<br>
                            <span style="font-size: 11px; color: #64748b">{{ $driver->emergency_contact_phone }}</span>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Statistiques</div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-label">Missions</div>
                            <div class="stat-value">{{ $driver->assignments->count() }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Ancienneté</div>
                            <div class="stat-value">
                                {{ $driver->recruitment_date ? $driver->recruitment_date->diffInYears() . ' ans' : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <div class="section">
                    <div class="section-title">Permis de Conduire</div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; display: flex; gap: 30px;">
                        <div>
                            <div class="info-label">Numéro de Permis</div>
                            <div class="info-value highlight" style="font-size: 16px;">{{ $driver->license_number ?? 'Non renseigné' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Validité</div>
                            <div class="info-value">
                                @if($driver->license_expiry_date)
                                    Jusqu'au {{ $driver->license_expiry_date->format('d/m/Y') }}
                                    @if($driver->license_expiry_date->isPast())
                                        <span style="color: #ef4444; font-weight: bold;">(Expiré)</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="info-label">Catégories</div>
                            <div class="info-value">
                                @if($driver->license_categories)
                                    @foreach($driver->license_categories as $cat)
                                        <span class="badge badge-neutral">{{ $cat }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-neutral">{{ $driver->license_category ?? 'B' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Historique des Affectations</div>
                    
                    @forelse($driver->assignments()->with('vehicle')->latest('start_datetime')->take(6)->get() as $assignment)
                        <div class="timeline-item {{ !$assignment->end_datetime ? 'active' : '' }}">
                            <div class="timeline-date">
                                {{ $assignment->start_datetime->format('d/m/Y') }} 
                                @if($assignment->end_datetime)
                                    - {{ $assignment->end_datetime->format('d/m/Y') }}
                                @else
                                    - <span style="color: #3b82f6; font-weight: 600;">En cours</span>
                                @endif
                            </div>
                            <div class="timeline-title">
                                {{ $assignment->vehicle->name ?? 'Véhicule Inconnu' }}
                            </div>
                            <div class="timeline-desc">
                                Immatriculation: {{ $assignment->vehicle->license_plate ?? 'N/A' }}
                                @if($assignment->end_datetime)
                                    • Durée: {{ $assignment->start_datetime->diffForHumans($assignment->end_datetime, true) }}
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; color: #94a3b8; padding: 20px; font-style: italic;">
                            Aucune affectation enregistrée.
                        </div>
                    @endforelse
                </div>

                @if($driver->notes)
                    <div class="section">
                        <div class="section-title">Notes</div>
                        <div style="background: #fffbeb; color: #92400e; padding: 15px; border-radius: 8px; font-size: 11px; border: 1px solid #fcd34d;">
                            {{ $driver->notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>ZenFleet Enterprise Solution</div>
            <div>Page 1/1</div>
            <div>{{ $driver->organization->name ?? 'Organisation' }}</div>
        </div>
    </div>
</body>
</html>
