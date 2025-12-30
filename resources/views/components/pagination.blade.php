@props(['paginator', 'recordsPerPage' => 25])

<div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <!-- Left: Nombre d'éléments par page -->
    <div class="flex items-center text-sm text-gray-700">
        <span class="mr-2">Afficher</span>
        <select
            class="block rounded-md border-0 py-1.5 pl-3 pr-8 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm"
            wire:model.live="perPage">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span class="ml-2">par page</span>
    </div>

    <!-- Right: Pagination -->
    @if ($paginator->hasPages())
    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
        <!-- Précédent -->
        @if ($paginator->onFirstPage())
        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-l-md cursor-not-allowed">
            Précédent
        </span>
        @else
        <button
            wire:click="gotoPage({{ $paginator->currentPage() - 1 }})"
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:z-20 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Précédent
        </button>
        @endif

        <!-- Pages numérotées -->
        @foreach ($paginator->render()->elements as $element)
        @if (is_string($element))
        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300">{{ $element }}</span>
        @endif

        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page === $paginator->currentPage())
        <span class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 border border-blue-600 focus:z-20 focus:outline-offset-0 shadow-sm">
            {{ $page }}
        </span>
        @else
        <button
            wire:click="gotoPage({{ $page }})"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-none focus:ring-2 focus:ring-blue-500">
            {{ $page }}
        </button>
        @endif
        @endforeach
        @endif
        @endforeach

        <!-- Suivant -->
        @if ($paginator->hasMorePages())
        <button
            wire:click="gotoPage({{ $paginator->currentPage() + 1 }})"
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-20 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Suivant
        </button>
        @else
        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-r-md cursor-not-allowed">
            Suivant
        </span>
        @endif
    </nav>
    @endif
</div>