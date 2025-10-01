@php
use App\Models\Assignment;

$statusConfig = [
    Assignment::STATUS_SCHEDULED => [
        'bg' => 'bg-blue-100',
        'text' => 'text-blue-800',
        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
    ],
    Assignment::STATUS_ACTIVE => [
        'bg' => 'bg-green-100',
        'text' => 'text-green-800',
        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    ],
    Assignment::STATUS_COMPLETED => [
        'bg' => 'bg-gray-100',
        'text' => 'text-gray-800',
        'icon' => 'M5 13l4 4L19 7'
    ],
    Assignment::STATUS_CANCELLED => [
        'bg' => 'bg-red-100',
        'text' => 'text-red-800',
        'icon' => 'M6 18L18 6M6 6l12 12'
    ]
];

$config = $statusConfig[$status] ?? $statusConfig[Assignment::STATUS_ACTIVE];
$label = Assignment::STATUSES[$status] ?? 'Inconnu';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
    <svg class="mr-1.5 h-2 w-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}" />
    </svg>
    {{ $label }}
</span>