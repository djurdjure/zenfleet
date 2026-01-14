@props(['showFiltersButton' => true])

<div class="mb-6" {{ $attributes }}>
    <div class="search-actions-bar flex flex-col lg:flex-row items-start lg:items-center gap-3">
        {{-- Search Input (flex-1) --}}
        <div class="flex-1 w-full lg:w-auto">
            {{ $search }}
        </div>

        {{-- Actions Container --}}
        <div class="flex items-center gap-2">
            @if($showFiltersButton && isset($filters))
            {{ $filters }}
            @endif
            {{ $actions }}
        </div>
    </div>

    {{-- Filters Panel Slot --}}
    @isset($filtersPanel)
    {{ $filtersPanel }}
    @endisset
</div>