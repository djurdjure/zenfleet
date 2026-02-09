<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport Analytics Statuts</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        h2 { font-size: 14px; margin: 18px 0 8px; }
        .muted { color: #6b7280; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .grid { width: 100%; }
    </style>
</head>
<body>
    <h1>Rapport Analytics Statuts</h1>
    <p class="muted">
        Type: {{ $entityType === 'vehicle' ? 'Véhicules' : 'Chauffeurs' }}
        | Période: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
        | Généré le: {{ now()->format('d/m/Y H:i') }}
    </p>

    <h2>Indicateurs clés</h2>
    <table>
        <tbody>
            <tr><th>Total changements</th><td>{{ $metrics['total_changes'] }}</td></tr>
            <tr><th>Changements manuels</th><td>{{ $metrics['manual_changes'] }}</td></tr>
            <tr><th>Changements automatiques</th><td>{{ $metrics['automatic_changes'] }}</td></tr>
            <tr><th>Entités uniques</th><td>{{ $metrics['unique_entities'] }}</td></tr>
            <tr><th>Moyenne par entité</th><td>{{ $metrics['avg_changes_per_entity'] }}</td></tr>
            <tr><th>Croissance (%)</th><td>{{ $metrics['growth_percentage'] }}</td></tr>
        </tbody>
    </table>

    <h2>Transitions principales</h2>
    <table>
        <thead>
            <tr>
                <th>De</th>
                <th>Vers</th>
                <th>Occurrences</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transitionStats as $row)
                <tr>
                    <td>{{ $row['from'] }}</td>
                    <td>{{ $row['to'] }}</td>
                    <td>{{ $row['count'] }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Aucune transition</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Distribution des statuts</h2>
    <table>
        <thead>
            <tr>
                <th>Statut</th>
                <th>Volume</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statusDistribution as $row)
                <tr>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ $row['count'] }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Aucune donnée</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Historique récent</h2>
    <table>
        <thead>
            <tr>
                <th>Entité</th>
                <th>Transition</th>
                <th>Raison</th>
                <th>Par</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentChanges as $row)
                <tr>
                    <td>{{ $row['entity_name'] }}</td>
                    <td>{{ $row['from_status'] }} -> {{ $row['to_status'] }}</td>
                    <td>{{ $row['reason'] }}</td>
                    <td>{{ $row['changed_by'] }}</td>
                    <td>{{ $row['changed_at'] }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun changement récent</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

