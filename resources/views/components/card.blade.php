@props([
    'title' => null,
    'icon' => null,
    'description' => null,
    'padding' => 'p-6',
    'margin' => 'mb-6'
])

@php
    $component = new \App\View\Components\Card($title, $icon, $description, $padding, $margin);
    $classes = $component->getClasses();
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($title)
        <div class="mb-4">
            @if($icon)
                <div class="flex items-center gap-3 mb-4">
                    <x-iconify :icon="$icon" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
                </div>
            @else
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
            @endif

            @if($description)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $description }}</p>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
