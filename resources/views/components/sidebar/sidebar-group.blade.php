@props(['title', 'active'])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="relative">
    <button @click="open = !open" class="w-full flex items-center p-2 text-base text-gray-700 rounded-lg hover:bg-gray-100 group">
        @if(isset($icon))
            <span class="mr-3 text-gray-500 group-hover:text-gray-700">
                {{ $icon }}
            </span>
        @endif
        <span class="flex-1 ml-1 text-left whitespace-nowrap">{{ $title }}</span>
        <x-tabler-chevron-down class="h-4 w-4 transform transition-transform" ::class="{'rotate-180': open}"/>
    </button>
    <div x-show="open" x-transition class="mt-1 space-y-1 pl-4 border-l-2 border-dotted border-gray-300 ml-4">
        {{ $slot }}
    </div>
</div>
