@props([
    'title' => '',
    'value' => '',
    'subtitle' => '',
    'icon' => 'heroicons:chart-bar',
    'color' => 'blue', // blue, green, orange, red, purple, emerald
    'trend' => null, // 'up', 'down', null
    'trendValue' => null,
    'animate' => false,
])

@php
$colorConfig = [
    'blue' => [
        'gradient' => 'from-blue-500 to-blue-600',
        'bg' => 'bg-blue-100',
        'text' => 'text-blue-900',
        'subtitle' => 'text-blue-700'
    ],
    'green' => [
        'gradient' => 'from-green-500 to-emerald-600',
        'bg' => 'bg-green-100',
        'text' => 'text-green-900',
        'subtitle' => 'text-green-700'
    ],
    'orange' => [
        'gradient' => 'from-orange-500 to-amber-600',
        'bg' => 'bg-orange-100',
        'text' => 'text-orange-900',
        'subtitle' => 'text-orange-700'
    ],
    'red' => [
        'gradient' => 'from-red-500 to-rose-600',
        'bg' => 'bg-red-100',
        'text' => 'text-red-900',
        'subtitle' => 'text-red-700'
    ],
    'purple' => [
        'gradient' => 'from-purple-500 to-purple-600',
        'bg' => 'bg-purple-100',
        'text' => 'text-purple-900',
        'subtitle' => 'text-purple-700'
    ],
    'emerald' => [
        'gradient' => 'from-emerald-500 to-teal-600',
        'bg' => 'bg-emerald-100',
        'text' => 'text-emerald-900',
        'subtitle' => 'text-emerald-700'
    ],
];

$config = $colorConfig[$color] ?? $colorConfig['blue'];
@endphp

{{-- ====================================================================
 📊 STAT CARD COMPONENT - ENTERPRISE METRICS DISPLAY
 ====================================================================
 
 @usage
 <x-stat-card 
     title="Total Chauffeurs"
     value="142"
     subtitle="Actifs dans la flotte"
     icon="heroicons:user-group"
     color="blue"
     trend="up"
     trendValue="+12%"
     animate />
 
 @version 1.0-Enterprise
 ==================================================================== --}}

<div class="zenfleet-card p-6 group cursor-pointer"
    @if($animate)
    x-data="{ count: 0, target: {{ is_numeric($value) ? $value : 0 }} }" 
    x-init="if(target > 0) { let interval = setInterval(() => { if(count < target) { count++; } else { clearInterval(interval); } }, Math.max(10, 1000 / target)); }"
    @endif>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 mb-1">{{ $title }}</p>
            <p class="text-3xl font-bold {{ $config['text'] }}"
                @if($animate && is_numeric($value))
                x-text="count"
                @endif>
                @if(!$animate || !is_numeric($value))
                    {{ $value }}
                @endif
            </p>
            
            @if($trend && $trendValue)
                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                    @if($trend === 'up')
                        <x-iconify icon="heroicons:arrow-trending-up" class="w-3.5 h-3.5 text-green-600" />
                        <span class="text-green-600 font-medium">{{ $trendValue }}</span>
                    @else
                        <x-iconify icon="heroicons:arrow-trending-down" class="w-3.5 h-3.5 text-red-600" />
                        <span class="text-red-600 font-medium">{{ $trendValue }}</span>
                    @endif
                    {{ $subtitle }}
                </p>
            @elseif($subtitle)
                <p class="text-xs {{ $config['subtitle'] }} mt-2">{{ $subtitle }}</p>
            @endif
        </div>
        
        <div class="w-14 h-14 bg-gradient-to-br {{ $config['gradient'] }} rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
            <x-iconify :icon="$icon" class="w-8 h-8 text-white" />
        </div>
    </div>
</div>
