@if ($paginator->hasPages())
 <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">
 <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-end">
 <div>
 <span class="relative z-0 inline-flex shadow-sm rounded-lg">
 {{-- Previous Page Link --}}
 @if ($paginator->onFirstPage())
 <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
 <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-white/80 border border-gray-200/80 cursor-default rounded-l-lg" aria-hidden="true">
 <i class="fas fa-chevron-left"></i>
 </span>
 </span>
 @else
 <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white/80 border border-gray-200/80 rounded-l-lg hover:bg-gray-100 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" aria-label="{{ __('pagination.previous') }}">
 <i class="fas fa-chevron-left"></i>
 </a>
 @endif

 {{-- Pagination Elements --}}
 @foreach ($elements as $element)
 {{-- "Three Dots" Separator --}}
 @if (is_string($element))
 <span aria-disabled="true">
 <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-white/80 border border-gray-200/80 cursor-default">{{ $element }}</span>
 </span>
 @endif

 {{-- Array Of Links --}}
 @if (is_array($element))
 @foreach ($element as $page => $url)
 @if ($page == $paginator->currentPage())
 <span aria-current="page">
 <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-blue-600 border border-blue-600 cursor-default">{{ $page }}</span>
 </span>
 @else
 <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-600 bg-white/80 border border-gray-200/80 hover:bg-gray-100 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
 {{ $page }}
 </a>
 @endif
 @endforeach
 @endif
 @endforeach

 {{-- Next Page Link --}}
 @if ($paginator->hasMorePages())
 <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-600 bg-white/80 border border-gray-200/80 rounded-r-lg hover:bg-gray-100 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" aria-label="{{ __('pagination.next') }}">
 <i class="fas fa-chevron-right"></i>
 </a>
 @else
 <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
 <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-400 bg-white/80 border border-gray-200/80 cursor-default rounded-r-lg" aria-hidden="true">
 <i class="fas fa-chevron-right"></i>
 </span>
 </span>
 @endif
 </span>
 </div>
 </div>
 </nav>
@endif
