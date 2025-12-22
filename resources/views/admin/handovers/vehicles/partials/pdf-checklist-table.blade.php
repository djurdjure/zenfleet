@php
$type = $config['type'] ?? 'condition';
$statuses = $type === 'binary' ? ['Oui', 'Non', 'N/A'] : ['Bon', 'Moyen', 'Mauvais', 'N/A'];
@endphp

<div style="margin-bottom: 2mm; break-inside: avoid;">
    <div style="font-weight: bold; background: #eee; border: 1px solid #ccc; padding: 1mm; font-size: 8pt; border-bottom: 0;">{{ $category }}</div>
    <table class="checklist-table">
        <thead>
            <tr>
                <th class="item-col">Élément</th>
                @foreach($statuses as $status)
                <th>{{ $status }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($details as $detail)
            <tr>
                <td>{{ $detail->item }}</td>
                @foreach($statuses as $status)
                <td class="status-cell">
                    <div class="checkbox {{ $detail->status === $status ? 'checked' : '' }}"></div>
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>