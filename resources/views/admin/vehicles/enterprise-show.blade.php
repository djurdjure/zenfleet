{{-- resources/views/admin/vehicles/enterprise-show.blade.php --}}
{{-- 🏆 ZENFLEET ENTERPRISE - FICHE VÉHICULE ULTRA-PROFESSIONNELLE --}}
{{-- Monochrome-Optimized | Print-Ready | Rich Analytics --}}
@extends('layouts.admin.catalyst')
@section('title', $vehicle->registration_plate . ' - Fiche Véhicule')

@push('styles')
<style>
    /* ========================================================================
       ZENFLEET ENTERPRISE DESIGN SYSTEM - MONOCHROME PROFESSIONAL
       Optimized for B&W printing and official document presentation
       ======================================================================== */

    :root {
        /* Monochrome Palette */
        --zf-black: #1a1a1a;
        --zf-dark: #333333;
        --zf-mid: #666666;
        --zf-light: #999999;
        --zf-pale: #e5e5e5;
        --zf-white: #ffffff;

        /* Accent (for interactive elements only - not printed) */
        --zf-accent: #1e40af;

        /* Spacing */
        --zf-space-xs: 0.25rem;
        --zf-space-sm: 0.5rem;
        --zf-space-md: 1rem;
        --zf-space-lg: 1.5rem;
        --zf-space-xl: 2rem;

        /* Borders */
        --zf-border-thin: 1px solid var(--zf-pale);
        --zf-border-medium: 1.5px solid var(--zf-dark);
        --zf-border-thick: 2px solid var(--zf-black);
    }

    /* === BASE LAYOUT === */
    .vehicle-document {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--zf-space-lg);
        background: var(--zf-white);
        font-family: 'Segoe UI', -apple-system, Arial, sans-serif;
        color: var(--zf-black);
        line-height: 1.5;
    }

    /* === DOCUMENT HEADER === */
    .doc-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: var(--zf-space-md);
        border-bottom: var(--zf-border-thick);
        margin-bottom: var(--zf-space-lg);
    }

    .doc-org {
        max-width: 60%;
    }

    .doc-org-name {
        font-size: 1.25rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--zf-black);
        margin-bottom: 2px;
    }

    .doc-org-address {
        font-size: 0.75rem;
        color: var(--zf-mid);
    }

    .doc-meta {
        text-align: right;
    }

    .doc-title {
        font-size: 0.875rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--zf-black);
        background: var(--zf-pale);
        padding: 4px 12px;
        margin-bottom: 4px;
    }

    .doc-date {
        font-size: 0.75rem;
        color: var(--zf-mid);
    }

    /* === VEHICLE IDENTITY BLOCK === */
    .vehicle-identity {
        background: var(--zf-pale);
        border: var(--zf-border-medium);
        padding: var(--zf-space-lg);
        margin-bottom: var(--zf-space-lg);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .vehicle-main {
        flex: 1;
    }

    .registration-plate {
        font-size: 1.75rem;
        font-weight: 900;
        font-family: 'Consolas', 'Monaco', monospace;
        letter-spacing: 3px;
        border: 3px solid var(--zf-black);
        padding: 6px 16px;
        display: inline-block;
        background: var(--zf-white);
        margin-bottom: 8px;
    }

    .vehicle-desc {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--zf-black);
    }

    .vehicle-vin {
        font-size: 0.75rem;
        color: var(--zf-mid);
        font-family: monospace;
        margin-top: 4px;
    }

    .vehicle-status {
        text-align: right;
    }

    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border: 2px solid var(--zf-black);
        font-size: 0.875rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: var(--zf-white);
    }

    /* === KPI DASHBOARD === */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: var(--zf-space-sm);
        margin-bottom: var(--zf-space-lg);
    }

    .kpi-box {
        border: var(--zf-border-medium);
        padding: var(--zf-space-md);
        text-align: center;
        background: var(--zf-white);
    }

    .kpi-value {
        font-size: 1.5rem;
        font-weight: 900;
        color: var(--zf-black);
        line-height: 1.1;
    }

    .kpi-unit {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--zf-mid);
    }

    .kpi-label {
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--zf-mid);
        margin-top: 4px;
    }

    /* === CONTENT GRID === */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: var(--zf-space-lg);
    }

    .content-full {
        grid-column: 1 / -1;
    }

    /* === SECTION CARDS === */
    .section {
        border: var(--zf-border-medium);
        margin-bottom: var(--zf-space-md);
        page-break-inside: avoid;
    }

    .section-header {
        background: var(--zf-pale);
        padding: 10px 16px;
        border-bottom: var(--zf-border-medium);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-header h2 {
        font-size: 0.8125rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--zf-black);
        margin: 0;
    }

    .section-header .count {
        font-size: 0.6875rem;
        color: var(--zf-mid);
        font-weight: 600;
    }

    .section-body {
        padding: var(--zf-space-md);
    }

    /* === INFO TABLES === */
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table tr {
        border-bottom: 1px solid var(--zf-pale);
    }

    .info-table tr:last-child {
        border-bottom: none;
    }

    .info-table th,
    .info-table td {
        padding: 8px 0;
        text-align: left;
        font-size: 0.8125rem;
        vertical-align: top;
    }

    .info-table th {
        font-weight: 600;
        color: var(--zf-mid);
        width: 45%;
    }

    .info-table td {
        font-weight: 600;
        color: var(--zf-black);
    }

    /* === DATA TABLES === */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }

    .data-table thead th {
        background: var(--zf-pale);
        padding: 8px 6px;
        text-align: left;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.625rem;
        letter-spacing: 0.3px;
        border-bottom: var(--zf-border-medium);
    }

    .data-table tbody td {
        padding: 6px;
        border-bottom: 1px solid var(--zf-pale);
        vertical-align: top;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .data-table .text-right {
        text-align: right;
    }

    .data-table .font-mono {
        font-family: 'Consolas', monospace;
    }

    .data-table .text-bold {
        font-weight: 700;
    }

    .data-table tfoot td {
        background: var(--zf-pale);
        padding: 8px 6px;
        font-weight: 800;
        border-top: var(--zf-border-medium);
    }

    /* === DRIVER CARD === */
    .driver-card {
        display: flex;
        align-items: center;
        gap: var(--zf-space-md);
        padding: var(--zf-space-md);
        background: var(--zf-pale);
        border: 1px solid var(--zf-dark);
    }

    .driver-avatar {
        width: 48px;
        height: 48px;
        background: var(--zf-dark);
        color: var(--zf-white);
        font-weight: 800;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .driver-name {
        font-weight: 800;
        font-size: 0.9375rem;
        color: var(--zf-black);
    }

    .driver-meta {
        font-size: 0.75rem;
        color: var(--zf-mid);
    }

    .empty-state {
        text-align: center;
        padding: var(--zf-space-lg);
        color: var(--zf-light);
        font-style: italic;
        font-size: 0.8125rem;
        border: 2px dashed var(--zf-pale);
    }

    /* === ALERTS === */
    .alert-box {
        padding: var(--zf-space-sm) var(--zf-space-md);
        border-left: 4px solid var(--zf-black);
        background: var(--zf-pale);
        margin-bottom: var(--zf-space-sm);
        font-size: 0.75rem;
    }

    .alert-box.warning {
        border-left-color: var(--zf-dark);
    }

    .alert-box.danger {
        border-left-color: var(--zf-black);
        background: var(--zf-dark);
        color: var(--zf-white);
    }

    .alert-title {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.6875rem;
        letter-spacing: 0.5px;
    }

    /* === COST SUMMARY === */
    .cost-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: var(--zf-space-sm);
    }

    .cost-item {
        text-align: center;
        padding: var(--zf-space-md);
        background: var(--zf-pale);
        border: 1px solid var(--zf-dark);
    }

    .cost-value {
        font-size: 1.125rem;
        font-weight: 900;
        color: var(--zf-black);
    }

    .cost-label {
        font-size: 0.625rem;
        font-weight: 700;
        color: var(--zf-mid);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    /* === QUICK STATS === */
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid var(--zf-pale);
        font-size: 0.8125rem;
    }

    .stat-row:last-child {
        border-bottom: none;
    }

    .stat-label {
        color: var(--zf-mid);
    }

    .stat-value {
        font-weight: 800;
        color: var(--zf-black);
    }

    /* === TIMELINE === */
    .timeline {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .timeline-item {
        display: flex;
        gap: var(--zf-space-sm);
        padding-bottom: var(--zf-space-md);
        border-left: 2px solid var(--zf-pale);
        margin-left: 6px;
        padding-left: var(--zf-space-md);
        position: relative;
    }

    .timeline-item:last-child {
        border-left-color: transparent;
        padding-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: -7px;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--zf-dark);
        border: 2px solid var(--zf-white);
    }

    .timeline-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--zf-black);
    }

    .timeline-date {
        font-size: 0.6875rem;
        color: var(--zf-light);
    }

    /* === ACTION BUTTONS (Screen Only) === */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--zf-space-lg);
        padding-bottom: var(--zf-space-md);
        border-bottom: 1px solid var(--zf-pale);
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8125rem;
        color: var(--zf-mid);
    }

    .breadcrumb a {
        color: var(--zf-accent);
        text-decoration: none;
        font-weight: 600;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        font-size: 0.8125rem;
        font-weight: 700;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
    }

    .btn-primary {
        background: var(--zf-accent);
        color: var(--zf-white);
    }

    .btn-primary:hover {
        background: #1e3a8a;
    }

    .btn-secondary {
        background: var(--zf-white);
        color: var(--zf-dark);
        border: 1px solid var(--zf-dark);
    }

    .btn-secondary:hover {
        background: var(--zf-pale);
    }

    /* === DOCUMENT FOOTER === */
    .doc-footer {
        margin-top: var(--zf-space-xl);
        padding-top: var(--zf-space-md);
        border-top: var(--zf-border-thick);
        text-align: center;
        font-size: 0.6875rem;
        color: var(--zf-mid);
    }

    /* ========================================================================
       PRINT STYLES - FULLY MONOCHROME
       ======================================================================== */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background: white !important;
            font-size: 9pt !important;
        }

        .no-print {
            display: none !important;
        }

        .vehicle-document {
            padding: 0;
            max-width: 100%;
        }

        .section,
        .kpi-box,
        .vehicle-identity {
            box-shadow: none !important;
            break-inside: avoid;
        }

        .kpi-grid {
            grid-template-columns: repeat(6, 1fr);
        }

        .content-grid {
            display: block;
        }

        .content-grid>div {
            margin-bottom: 1rem;
        }

        .driver-avatar {
            background: #333 !important;
        }

        .timeline-dot {
            background: #333 !important;
        }

        @page {
            margin: 1.5cm;
            size: A4;
        }
    }

    /* === RESPONSIVE === */
    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .kpi-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 640px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .cost-grid {
            grid-template-columns: 1fr;
        }

        .vehicle-identity {
            flex-direction: column;
            text-align: center;
        }

        .vehicle-status {
            text-align: center;
            margin-top: var(--zf-space-md);
        }
    }
</style>
@endpush

@section('content')
<div class="vehicle-document">

    {{-- ========== DOCUMENT HEADER ========== --}}
    <div class="doc-header">
        <div class="doc-org">
            <div class="doc-org-name">{{ Auth::user()->organization->name ?? 'ZenFleet' }}</div>
            @if(Auth::user()->organization->address ?? null)
            <div class="doc-org-address">{{ Auth::user()->organization->address }}</div>
            @endif
        </div>
        <div class="doc-meta">
            <div class="doc-title">Fiche Véhicule</div>
            <div class="doc-date">{{ now()->format('d/m/Y à H:i') }}</div>
        </div>
    </div>

    {{-- ========== ACTION BAR (Screen Only) ========== --}}
    <div class="action-bar no-print">
        <nav class="breadcrumb">
            <a href="{{ route('admin.vehicles.index') }}">Véhicules</a>
            <x-iconify icon="lucide:chevron-right" class="w-4 h-4" />
            <span style="color: var(--zf-black); font-weight: 700;">{{ $vehicle->registration_plate }}</span>
        </nav>
        <div class="action-buttons">
            <a href="{{ route('admin.vehicles.export.single.pdf', $vehicle) }}" class="btn btn-primary">
                <x-iconify icon="lucide:file-text" class="w-4 h-4" />
                Exporter PDF
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <x-iconify icon="lucide:printer" class="w-4 h-4" />
                Imprimer
            </button>
            @can('edit vehicles')
            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-secondary">
                <x-iconify icon="lucide:edit" class="w-4 h-4" />
                Modifier
            </a>
            @endcan
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">
                <x-iconify icon="lucide:arrow-left" class="w-4 h-4" />
                Retour
            </a>
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

    {{-- ========== KPI DASHBOARD (6 Metrics) ========== --}}
    <div class="kpi-grid">
        <div class="kpi-box">
            <div class="kpi-value">{{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }}</div>
            <div class="kpi-label">Kilométrage <span class="kpi-unit">km</span></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value" style="font-size: 1rem;">{{ $analytics['duration_in_service_formatted'] ?? 'N/A' }}</div>
            <div class="kpi-label">Jours en Service</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ number_format($analytics['maintenance_cost_total'] ?? 0, 0, ',', ' ') }}</div>
            <div class="kpi-label">Coût Maintenance <span class="kpi-unit">DA</span></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ number_format($analytics['total_km_driven'] ?? 0, 0, ',', ' ') }}</div>
            <div class="kpi-label">Distance Parcourue <span class="kpi-unit">km</span></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ $analytics['maintenance_count'] ?? 0 }}</div>
            <div class="kpi-label">Interventions</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-value">{{ number_format($analytics['cost_per_km'] ?? 0, 2, ',', ' ') }}</div>
            <div class="kpi-label">Coût/km <span class="kpi-unit">DA</span></div>
        </div>
    </div>

    {{-- ========== DOCUMENT ALERTS ========== --}}
    @php
    $insuranceExpired = $vehicle->insurance_expiry_date && $vehicle->insurance_expiry_date->isPast();
    $insuranceSoon = $vehicle->insurance_expiry_date && $vehicle->insurance_expiry_date->isFuture() && $vehicle->insurance_expiry_date->diffInDays(now()) <= 30;
        $technicalExpired=$vehicle->technical_control_expiry_date && $vehicle->technical_control_expiry_date->isPast();
        $technicalSoon = $vehicle->technical_control_expiry_date && $vehicle->technical_control_expiry_date->isFuture() && $vehicle->technical_control_expiry_date->diffInDays(now()) <= 30;
            @endphp
            @if($insuranceExpired || $insuranceSoon || $technicalExpired || $technicalSoon)
            <div style="margin-bottom: var(--zf-space-lg);">
            @if($insuranceExpired)
            <div class="alert-box danger">
                <span class="alert-title">⚠ ASSURANCE EXPIRÉE</span> — Expirée le {{ $vehicle->insurance_expiry_date->format('d/m/Y') }}
            </div>
            @elseif($insuranceSoon)
            <div class="alert-box warning">
                <span class="alert-title">ASSURANCE</span> — Expire le {{ $vehicle->insurance_expiry_date->format('d/m/Y') }} ({{ $vehicle->insurance_expiry_date->diffInDays(now()) }} jours)
            </div>
            @endif
            @if($technicalExpired)
            <div class="alert-box danger">
                <span class="alert-title">⚠ CONTRÔLE TECHNIQUE EXPIRÉ</span> — Expiré le {{ $vehicle->technical_control_expiry_date->format('d/m/Y') }}
            </div>
            @elseif($technicalSoon)
            <div class="alert-box warning">
                <span class="alert-title">CONTRÔLE TECHNIQUE</span> — Expire le {{ $vehicle->technical_control_expiry_date->format('d/m/Y') }} ({{ $vehicle->technical_control_expiry_date->diffInDays(now()) }} jours)
            </div>
            @endif
</div>
@endif

{{-- ========== MAIN CONTENT GRID ========== --}}
<div class="content-grid">
    {{-- LEFT COLUMN --}}
    <div>
        {{-- General Information --}}
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:info" class="w-4 h-4" />
                <h2>Informations Générales</h2>
            </div>
            <div class="section-body">
                <table class="info-table">
                    <tr>
                        <th>Marque / Modèle</th>
                        <td>{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                    </tr>
                    <tr>
                        <th>Immatriculation</th>
                        <td>{{ $vehicle->registration_plate }}</td>
                    </tr>
                    <tr>
                        <th>Numéro VIN</th>
                        <td>{{ $vehicle->vin ?? 'Non renseigné' }}</td>
                    </tr>
                    <tr>
                        <th>Année</th>
                        <td>{{ $vehicle->manufacturing_year }}</td>
                    </tr>
                    <tr>
                        <th>Couleur</th>
                        <td>{{ $vehicle->color ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td>{{ optional($vehicle->vehicleType)->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Places</th>
                        <td>{{ $vehicle->seats ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Technical Specifications --}}
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:settings" class="w-4 h-4" />
                <h2>Spécifications Techniques</h2>
            </div>
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
                        <th>Kilométrage actuel</th>
                        <td><strong>{{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }} km</strong></td>
                    </tr>
                    <tr>
                        <th>Distance parcourue</th>
                        <td>{{ number_format($analytics['total_km_driven'] ?? 0, 0, ',', ' ') }} km</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Financial Information --}}
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:banknote" class="w-4 h-4" />
                <h2>Informations Financières</h2>
            </div>
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

        {{-- Administrative Documents --}}
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:file-check" class="w-4 h-4" />
                <h2>Documents Administratifs</h2>
            </div>
            <div class="section-body">
                <table class="info-table">
                    <tr>
                        <th>Assurance</th>
                        <td>
                            @if($vehicle->insurance_expiry_date)
                            {{ $vehicle->insurance_expiry_date->format('d/m/Y') }}
                            @if($insuranceExpired)
                            <strong style="color: #991b1b;">(EXPIRÉ)</strong>
                            @elseif($insuranceSoon)
                            <strong>({{ $vehicle->insurance_expiry_date->diffInDays(now()) }}j)</strong>
                            @endif
                            @else
                            N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Contrôle technique</th>
                        <td>
                            @if($vehicle->technical_control_expiry_date)
                            {{ $vehicle->technical_control_expiry_date->format('d/m/Y') }}
                            @if($technicalExpired)
                            <strong style="color: #991b1b;">(EXPIRÉ)</strong>
                            @elseif($technicalSoon)
                            <strong>({{ $vehicle->technical_control_expiry_date->diffInDays(now()) }}j)</strong>
                            @endif
                            @else
                            N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Prochaine maintenance</th>
                        <td>{{ $vehicle->next_maintenance_date ? $vehicle->next_maintenance_date->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Maintenance Cost Summary --}}
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:wrench" class="w-4 h-4" />
                <h2>Résumé Coûts Maintenance</h2>
            </div>
            <div class="section-body">
                <div class="cost-grid">
                    <div class="cost-item">
                        <div class="cost-value">{{ number_format($analytics['maintenance_cost_total'] ?? 0, 0, ',', ' ') }} DA</div>
                        <div class="cost-label">Total</div>
                    </div>
                    <div class="cost-item">
                        <div class="cost-value">{{ number_format($analytics['maintenance_cost_preventive'] ?? 0, 0, ',', ' ') }} DA</div>
                        <div class="cost-label">Préventive</div>
                    </div>
                    <div class="cost-item">
                        <div class="cost-value">{{ number_format($analytics['maintenance_cost_corrective'] ?? 0, 0, ',', ' ') }} DA</div>
                        <div class="cost-label">Corrective</div>
                    </div>
                </div>
                <div style="margin-top: 1rem;">
                    <div class="stat-row">
                        <span class="stat-label">Nombre d'interventions</span>
                        <span class="stat-value">{{ $analytics['maintenance_count'] ?? 0 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Dernière maintenance</span>
                        <span class="stat-value">{{ $analytics['last_maintenance_date'] ?? 'Aucune' }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Coût moyen par km</span>
                        <span class="stat-value">{{ number_format($analytics['cost_per_km'] ?? 0, 2, ',', ' ') }} DA</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Maintenance Operations --}}
        @php
        $maintenanceOps = $vehicle->maintenanceOperations()
        ->with('supplier')
        ->orderBy('completed_date', 'desc')
        ->limit(10)
        ->get();
        @endphp
        @if($maintenanceOps->count() > 0)
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:tool" class="w-4 h-4" />
                <h2>Opérations de Maintenance</h2>
                <span class="count">({{ $analytics['maintenance_count'] ?? $maintenanceOps->count() }} total)</span>
            </div>
            <div class="section-body" style="padding: 0;">
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
                        @foreach($maintenanceOps as $op)
                        <tr>
                            <td style="white-space: nowrap;">{{ $op->completed_date ? $op->completed_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ Str::limit($op->description ?? $op->notes ?? 'N/A', 40) }}</td>
                            <td>{{ optional($op->supplier)->name ?? 'N/A' }}</td>
                            <td class="text-right font-mono text-bold">{{ number_format($op->total_cost ?? 0, 0, ',', ' ') }} DA</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                            <td class="text-right font-mono">{{ number_format($maintenanceOps->sum('total_cost'), 0, ',', ' ') }} DA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{-- Expenses section removed - relationship doesn't exist on Vehicle model --}}

        {{-- Assignment History --}}
        @php
        $assignments = $vehicle->assignments()
        ->with('driver')
        ->orderBy('start_datetime', 'desc')
        ->limit(10)
        ->get();
        @endphp
        @if($assignments->count() > 0)
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:users" class="w-4 h-4" />
                <h2>Historique des Affectations</h2>
                <span class="count">({{ $analytics['assignments_count'] ?? $assignments->count() }} total)</span>
            </div>
            <div class="section-body" style="padding: 0;">
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
                        @foreach($assignments as $assignment)
                        <tr>
                            <td class="text-bold">
                                {{ $assignment->driver ? $assignment->driver->first_name . ' ' . $assignment->driver->last_name : 'N/A' }}
                            </td>
                            <td>{{ $assignment->start_datetime->format('d/m/Y') }}</td>
                            <td>{{ $assignment->end_datetime ? $assignment->end_datetime->format('d/m/Y') : '—' }}</td>
                            <td class="text-center">
                                @if($assignment->end_datetime)
                                <span style="padding: 2px 8px; border: 1px solid var(--zf-mid); font-size: 0.625rem; text-transform: uppercase;">Terminée</span>
                                @else
                                <span style="padding: 2px 8px; border: 2px solid var(--zf-black); font-weight: 700; font-size: 0.625rem; text-transform: uppercase;">En cours</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Notes --}}
        @if($vehicle->notes)
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:file-text" class="w-4 h-4" />
                <h2>Notes</h2>
            </div>
            <div class="section-body">
                <p style="white-space: pre-wrap; font-size: 0.8125rem; color: var(--zf-dark);">{{ $vehicle->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT COLUMN (SIDEBAR) --}}
    <div>
        {{-- Current Driver --}}
        @php
        $currentAssignment = $analytics['active_assignment'] ?? null;
        @endphp
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:user" class="w-4 h-4" />
                <h2>Chauffeur Actuel</h2>
            </div>
            <div class="section-body">
                @if($currentAssignment && $currentAssignment->driver)
                <div class="driver-card">
                    @if($currentAssignment->driver->photo)
                    <img src="{{ asset('storage/' . $currentAssignment->driver->photo) }}"
                        alt="{{ $currentAssignment->driver->first_name }}"
                        style="width: 48px; height: 48px; object-fit: cover;">
                    @else
                    <div class="driver-avatar">
                        {{ strtoupper(substr($currentAssignment->driver->first_name, 0, 1)) }}{{ strtoupper(substr($currentAssignment->driver->last_name, 0, 1)) }}
                    </div>
                    @endif
                    <div>
                        <div class="driver-name">{{ $currentAssignment->driver->first_name }} {{ $currentAssignment->driver->last_name }}</div>
                        <div class="driver-meta">{{ $currentAssignment->driver->personal_phone ?? 'Pas de téléphone' }}</div>
                        <div class="driver-meta">Depuis {{ $currentAssignment->start_datetime->format('d/m/Y') }}</div>
                    </div>
                </div>
                @else
                <div class="empty-state">Aucun chauffeur affecté</div>
                @endif
            </div>
        </div>

        {{-- Assigned Depot --}}
        @if($vehicle->depot)
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:building-2" class="w-4 h-4" />
                <h2>Dépôt Affecté</h2>
            </div>
            <div class="section-body">
                <div style="font-weight: 800; font-size: 1rem; margin-bottom: 4px;">{{ $vehicle->depot->name }}</div>
                @if($vehicle->depot->code)
                <div style="font-size: 0.75rem; color: var(--zf-mid);">Code: {{ $vehicle->depot->code }}</div>
                @endif
                @if($vehicle->depot->city)
                <div style="font-size: 0.75rem; color: var(--zf-mid); margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                    <x-iconify icon="lucide:map-pin" class="w-3 h-3" />
                    {{ $vehicle->depot->city }}{{ $vehicle->depot->wilaya ? ', ' . $vehicle->depot->wilaya : '' }}
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Quick Stats --}}
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:bar-chart-3" class="w-4 h-4" />
                <h2>Statistiques</h2>
            </div>
            <div class="section-body">
                <div class="stat-row">
                    <span class="stat-label">Taux d'utilisation</span>
                    <span class="stat-value">{{ $analytics['utilization_rate'] ?? 0 }}%</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Âge du véhicule</span>
                    <span class="stat-value">{{ $analytics['vehicle_age'] ?? 0 }} an(s)</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Moyenne km/mois</span>
                    <span class="stat-value">{{ number_format($analytics['avg_km_per_month'] ?? 0, 0, ',', ' ') }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Affectations totales</span>
                    <span class="stat-value">{{ $analytics['assignments_count'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        {{-- Activity Timeline --}}
        @if(!empty($timeline))
        <div class="section">
            <div class="section-header">
                <x-iconify icon="lucide:clock" class="w-4 h-4" />
                <h2>Activité Récente</h2>
            </div>
            <div class="section-body">
                <ul class="timeline">
                    @foreach(array_slice($timeline, 0, 6) as $event)
                    <li class="timeline-item">
                        <span class="timeline-dot"></span>
                        <div>
                            <div class="timeline-title">{{ $event['title'] }}</div>
                            <div class="timeline-date">{{ $event['date'] }}</div>
                            @if(!empty($event['description']))
                            <div style="font-size: 0.6875rem; color: var(--zf-light); margin-top: 2px;">{{ $event['description'] }}</div>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- Quick Actions (Screen Only) --}}
        <div class="section no-print">
            <div class="section-header">
                <x-iconify icon="lucide:zap" class="w-4 h-4" />
                <h2>Actions Rapides</h2>
            </div>
            <div class="section-body">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('admin.vehicles.mileage-history', $vehicle) }}" class="btn btn-secondary" style="justify-content: flex-start;">
                        <x-iconify icon="lucide:gauge" class="w-4 h-4" />
                        Historique kilométrique
                    </a>
                    <a href="{{ route('admin.vehicles.history', $vehicle) }}" class="btn btn-secondary" style="justify-content: flex-start;">
                        <x-iconify icon="lucide:history" class="w-4 h-4" />
                        Historique complet
                    </a>
                </div>
            </div>
        </div>

        {{-- System Info --}}
        <div class="section" style="font-size: 0.6875rem;">
            <div class="section-header">
                <x-iconify icon="lucide:database" class="w-4 h-4" />
                <h2>Système</h2>
            </div>
            <div class="section-body">
                <div class="stat-row" style="font-size: 0.6875rem;">
                    <span class="stat-label">ID</span>
                    <span class="stat-value" style="font-family: monospace;">#{{ $vehicle->id }}</span>
                </div>
                <div class="stat-row" style="font-size: 0.6875rem;">
                    <span class="stat-label">Créé le</span>
                    <span class="stat-value">{{ $vehicle->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="stat-row" style="font-size: 0.6875rem;">
                    <span class="stat-label">Modifié le</span>
                    <span class="stat-value">{{ $vehicle->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========== DOCUMENT FOOTER ========== --}}
<div class="doc-footer">
    Document généré par ZenFleet • {{ $vehicle->registration_plate }} • {{ $vehicle->brand }} {{ $vehicle->model }} • {{ now()->format('d/m/Y H:i') }}
</div>
</div>
@endsection