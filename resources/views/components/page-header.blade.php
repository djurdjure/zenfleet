@props([
    'title' => '',
    'subtitle' => '',
    'icon' => 'heroicons:squares-2x2',
    'breadcrumbs' => [],
])

{{-- ====================================================================
 📄 PAGE HEADER COMPONENT - WORLD-CLASS ENTERPRISE DESIGN
 ====================================================================
 
 Composant réutilisable pour en-têtes de page cohérents
 
 @usage
 <x-page-header 
     title="Gestion des Chauffeurs"
     subtitle="Gérez votre équipe de {{ $count }} chauffeurs"
     icon="heroicons:user-group"
     :breadcrumbs="[
         ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
         ['label' => 'Chauffeurs', 'url' => null]
     ]">
     <x-slot name="actions">
         <x-button href="#" variant="primary">Action</x-button>
     </x-slot>
 </x-page-header>
 
 @version 1.0-Enterprise
 @since 2025-01-20
 ==================================================================== --}}

<div class="mb-6">
    {{-- Breadcrumb --}}
    @if(count($breadcrumbs) > 0)
    <nav class="flex mb-3" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            @foreach($breadcrumbs as $index => $crumb)
                @if($index === 0)
                    <li class="inline-flex items-center">
                        @if($crumb['url'])
                            <a href="{{ $crumb['url'] }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                                <x-iconify icon="heroicons:home" class="w-4 h-4 mr-1.5" />
                                {{ $crumb['label'] }}
                            </a>
                        @else
                            <span class="inline-flex items-center text-sm font-medium text-gray-500">
                                <x-iconify icon="heroicons:home" class="w-4 h-4 mr-1.5" />
                                {{ $crumb['label'] }}
                            </span>
                        @endif
                    </li>
                @else
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400 mx-1" />
                            @if($crumb['url'])
                                <a href="{{ $crumb['url'] }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                                    {{ $crumb['label'] }}
                                </a>
                            @else
                                <span class="text-sm font-medium text-gray-500">{{ $crumb['label'] }}</span>
                            @endif
                        </div>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
    @endif

    {{-- Page Title Section --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            {{-- Icon --}}
            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                <x-iconify :icon="$icon" class="w-7 h-7 text-white" />
            </div>
            
            {{-- Title & Subtitle --}}
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
                @if($subtitle)
                    <p class="text-sm text-gray-600 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        {{-- Actions Slot --}}
        @if(isset($actions))
            <div class="flex items-center gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
