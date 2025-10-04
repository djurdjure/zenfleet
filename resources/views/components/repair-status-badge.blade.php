@props(['status'])

@php
    // 🎨 CONFIGURATION DES BADGES PAR STATUT
    $statusConfig = [
        'pending_supervisor' => [
            'bg' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'label' => 'En attente superviseur',
        ],
        'approved_supervisor' => [
            'bg' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'label' => 'Approuvé superviseur',
        ],
        'rejected_supervisor' => [
            'bg' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'label' => 'Rejeté superviseur',
        ],
        'pending_fleet_manager' => [
            'bg' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'label' => 'En attente gestionnaire',
        ],
        'approved_final' => [
            'bg' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
            'label' => 'Approuvé final',
        ],
        'rejected_final' => [
            'bg' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>',
            'label' => 'Rejeté final',
        ],
    ];

    $config = $statusConfig[$status] ?? [
        'bg' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'label' => 'Statut inconnu',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ' . $config['bg']]) }}>
    <span class="mr-1.5">
        {!! $config['icon'] !!}
    </span>
    {{ $config['label'] }}
</span>
