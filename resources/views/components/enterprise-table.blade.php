{{--
|--------------------------------------------------------------------------
| 🗃️ Enterprise Table Component
|--------------------------------------------------------------------------
| Tableau ultra-professionnel avec fonctionnalités avancées:
| - Tri multi-colonnes
| - Filtres en temps réel
| - Actions en lot
| - Export avancé
| - Responsive design
| - Animations fluides
--}}

@props([
 'headers' => [],
 'rows' => [],
 'sortable' => true,
 'filterable' => true,
 'exportable' => true,
 'selectable' => true,
 'searchable' => true,
 'pagination' => null,
 'emptyMessage' => 'Aucune donnée disponible',
 'emptyIcon' => 'table-cells',
 'theme' => 'gradient', // gradient, minimal, enterprise
 'size' => 'default', // compact, default, spacious
 'stickyHeader' => true,
 'actions' => [],
 'filters' => []
])

@php
$tableId = 'enterprise-table-' . Str::random(8);
$sizeClasses = [
 'compact' => 'text-xs',
 'default' => 'text-sm',
 'spacious' => 'text-base'
];

$themeClasses = [
 'gradient' => 'bg-gradient-to-r from-gray-50 to-blue-50',
 'minimal' => 'bg-white border-gray-100',
 'enterprise' => 'bg-gradient-to-r from-slate-800 to-indigo-900 text-white'
];

$currentSizeClass = $sizeClasses[$size] ?? $sizeClasses['default'];
$currentThemeClass = $themeClasses[$theme] ?? $themeClasses['gradient'];
@endphp

<div
 id="{{ $tableId }}"
 class="enterprise-table-container bg-white/70 backdrop-blur-sm shadow-2xl rounded-2xl border border-white/20 overflow-hidden {{ $currentSizeClass }}"
 x-data="enterpriseTable({
 headers: {{ json_encode($headers) }},
 rows: {{ json_encode($rows) }},
 sortable: {{ $sortable ? 'true' : 'false' }},
 filterable: {{ $filterable ? 'true' : 'false' }},
 selectable: {{ $selectable ? 'true' : 'false' }},
 searchable: {{ $searchable ? 'true' : 'false' }},
 filters: {{ json_encode($filters) }}
 })"
 data-animate="fade-in"
>
 {{-- Header & Controls --}}
 <div class="px-8 py-6 border-b border-gray-200/50 {{ $currentThemeClass }}">
 <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">

 {{-- Title & Stats --}}
 <div class="flex items-center space-x-4">
 <h3 class="text-xl font-bold flex items-center">
 <svg class="h-6 w-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"></path>
 </svg>
 <span x-text="`${filteredRows.length} éléments`"></span>
 </h3>

 {{-- Selected Counter --}}
 <div x-show="selectedRows.length > 0" x-transition
 class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
 <span x-text="selectedRows.length"></span> sélectionné{{ selectedRows.length > 1 ? 's' : '' }}
 </div>
 </div>

 {{-- Controls --}}
 <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3">

 {{-- Search --}}
 @if($searchable)
 <div class="relative">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
 </svg>
 </div>
 <input type="text"
 x-model="searchQuery"
 placeholder="Rechercher..."
 class="block w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
 </div>
 @endif

 {{-- Filters --}}
 @if($filterable && count($filters) > 0)
 <div class="flex space-x-2">
 @foreach($filters as $filter)
 <select x-model="activeFilters['{{ $filter['key'] }}']"
 class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
 <option value="">{{ $filter['label'] ?? 'Tous' }}</option>
 @foreach($filter['options'] as $value => $label)
 <option value="{{ $value }}">{{ $label }}</option>
 @endforeach
 </select>
 @endforeach
 </div>
 @endif

 {{-- Bulk Actions --}}
 <div x-show="selectedRows.length > 0" x-transition class="flex space-x-2">
 @foreach($actions as $action)
 <button type="button"
 @click="{{ $action['action'] }}(selectedRows)"
 class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all hover:scale-105 {{ $action['class'] ?? 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
 @if(isset($action['icon']))
 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 {!! $action['icon'] !!}
 </svg>
 @endif
 {{ $action['label'] }}
 </button>
 @endforeach
 </div>

 {{-- Export --}}
 @if($exportable)
 <div class="relative"
 x-data="{
     open: false,
     styles: '',
     direction: 'down',
     toggle() {
         this.open = !this.open;
         if (this.open) {
             this.$nextTick(() => requestAnimationFrame(() => this.updatePosition()));
         }
     },
     close() { this.open = false; },
     updatePosition() {
         if (!this.$refs.trigger || !this.$refs.menu) return;
         const rect = this.$refs.trigger.getBoundingClientRect();
         const width = 192; // w-48
         const padding = 12;
         const menuHeight = this.$refs.menu.offsetHeight || 180;
         const spaceBelow = window.innerHeight - rect.bottom - padding;
         const spaceAbove = rect.top - padding;
         const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
         this.direction = shouldOpenUp ? 'up' : 'down';

         let top = shouldOpenUp ? (rect.top - menuHeight - 8) : (rect.bottom + 8);
         if (top < padding) top = padding;
         if (top + menuHeight > window.innerHeight - padding) {
             top = window.innerHeight - padding - menuHeight;
         }

         let left = rect.right - width;
         const maxLeft = window.innerWidth - width - padding;
         if (left > maxLeft) left = maxLeft;
         if (left < padding) left = padding;

         this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${width}px; z-index: 80;`;
     }
 }"
 x-init="
     window.addEventListener('scroll', () => { if (open) updatePosition(); }, true);
     window.addEventListener('resize', () => { if (open) updatePosition(); });
 ">
 <button x-ref="trigger" @click="toggle" @click.outside="close" type="button"
 class="inline-flex items-center px-4 py-2 border-2 border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all hover:scale-105">
 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
 </svg>
 Exporter
 </button>

 <template x-teleport="body">
 <div x-show="open" @click.outside="close" x-transition
 :style="styles"
 x-ref="menu"
 :class="direction === 'up' ? 'origin-bottom-right' : 'origin-top-right'"
 class="fixed bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-[80]">
 <button @click="exportData('csv')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV</button>
 <button @click="exportData('excel')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Excel</button>
 <button @click="exportData('pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF</button>
 </div>
 </template>
 </div>
 @endif
 </div>
 </div>
 </div>

 {{-- Table --}}
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200/50">
 {{-- Header --}}
 <thead class="bg-gradient-to-r from-gray-50 to-blue-50 {{ $stickyHeader ? 'sticky top-0 z-10' : '' }}">
 <tr>
 {{-- Select All --}}
 @if($selectable)
 <th scope="col" class="px-6 py-4 text-left">
 <input type="checkbox"
 x-model="selectAll"
 @change="toggleSelectAll()"
 class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
 </th>
 @endif

 {{-- Headers --}}
 <template x-for="(header, index) in headers" :key="index">
 <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
 <div class="flex items-center space-x-2">
 <span x-text="header.label"></span>
 <div x-show="header.sortable !== false && sortable" class="flex flex-col">
 <button @click="sortBy(header.key)"
 class="text-gray-400 hover:text-gray-600 transition-colors"
 :class="{ 'text-indigo-600': sortConfig.key === header.key }">
 <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
 </svg>
 </button>
 </div>
 </div>
 </th>
 </template>
 </tr>
 </thead>

 {{-- Body --}}
 <tbody class="bg-white/50 divide-y divide-gray-200/30">
 <template x-for="(row, rowIndex) in paginatedRows" :key="rowIndex">
 <tr class="hover:bg-blue-50/50 transition-colors duration-200 enterprise-hover-lift">
 {{-- Select Row --}}
 <template x-if="'{{ $selectable }}' === '1'">
 <td class="px-6 py-4 whitespace-nowrap">
 <input type="checkbox"
 x-model="selectedRows"
 :value="row.id"
 class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
 </td>
 </template>

 {{-- Row Data --}}
 <template x-for="(header, headerIndex) in headers" :key="headerIndex">
 <td class="px-6 py-4 whitespace-nowrap" x-html="formatCellValue(row[header.key], header)">
 </td>
 </template>
 </tr>
 </template>
 </tbody>
 </table>
 </div>

 {{-- Empty State --}}
 <div x-show="filteredRows.length === 0" class="text-center py-12">
 <div class="mx-auto h-24 w-24 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full flex items-center justify-center mb-4">
 <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"></path>
 </svg>
 </div>
 <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $emptyMessage }}</h3>
 <p class="text-gray-600">Essayez de modifier vos filtres ou critères de recherche.</p>
 </div>

 {{-- Pagination --}}
 @if($pagination)
 <div class="px-8 py-6 border-t border-gray-200/50 bg-gray-50/50">
 <div class="flex items-center justify-between">
 <div class="text-sm text-gray-700">
 Affichage de <span x-text="((currentPage - 1) * itemsPerPage) + 1"></span> à
 <span x-text="Math.min(currentPage * itemsPerPage, filteredRows.length)"></span> sur
 <span x-text="filteredRows.length"></span> résultats
 </div>

 <div class="flex items-center space-x-2">
 <button @click="goToPage(currentPage - 1)"
 :disabled="currentPage === 1"
 :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
 class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
 Précédent
 </button>

 <template x-for="page in visiblePages" :key="page">
 <button @click="goToPage(page)"
 :class="{ 'bg-indigo-600 text-white': page === currentPage, 'bg-white text-gray-700 hover:bg-gray-50': page !== currentPage }"
 class="px-3 py-2 text-sm font-medium border border-gray-300 rounded-lg"
 x-text="page">
 </button>
 </template>

 <button @click="goToPage(currentPage + 1)"
 :disabled="currentPage === totalPages"
 :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
 class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
 Suivant
 </button>
 </div>
 </div>
 </div>
 @endif
</div>

{{-- Custom Slot Content --}}
{{ $slot }}

{{-- Alpine.js Component --}}
@push('scripts')
<script>
function enterpriseTable(config) {
 return {
 // Data
 headers: config.headers,
 originalRows: config.rows,
 filteredRows: [],
 paginatedRows: [],

 // State
 searchQuery: '',
 selectedRows: [],
 selectAll: false,
 activeFilters: {},
 sortConfig: { key: null, direction: 'asc' },

 // Pagination
 currentPage: 1,
 itemsPerPage: 10,
 totalPages: 1,

 // Config
 sortable: config.sortable,
 filterable: config.filterable,
 selectable: config.selectable,
 searchable: config.searchable,
 filters: config.filters,

 init() {
 this.filteredRows = [...this.originalRows];
 this.updatePagination();
 this.watchChanges();
 },

 watchChanges() {
 this.$watch('searchQuery', () => this.applyFilters());
 this.$watch('activeFilters', () => this.applyFilters(), { deep: true });
 this.$watch('sortConfig', () => this.applySorting(), { deep: true });
 this.$watch('currentPage', () => this.updatePagination());
 this.$watch('filteredRows', () => this.updatePagination());
 },

 applyFilters() {
 let rows = [...this.originalRows];

 // Text search
 if (this.searchQuery) {
 const query = this.searchQuery.toLowerCase();
 rows = rows.filter(row => {
 return Object.values(row).some(value => {
 if (value === null || value === undefined) return false;
 return String(value).toLowerCase().includes(query);
 });
 });
 }

 // Column filters
 Object.keys(this.activeFilters).forEach(key => {
 const filterValue = this.activeFilters[key];
 if (filterValue) {
 rows = rows.filter(row => row[key] === filterValue);
 }
 });

 this.filteredRows = rows;
 this.currentPage = 1;
 },

 applySorting() {
 if (!this.sortConfig.key) return;

 this.filteredRows.sort((a, b) => {
 let aVal = a[this.sortConfig.key];
 let bVal = b[this.sortConfig.key];

 // Handle null/undefined values
 if (aVal === null || aVal === undefined) aVal = '';
 if (bVal === null || bVal === undefined) bVal = '';

 // Convert to comparable format
 if (typeof aVal === 'string') {
 aVal = aVal.toLowerCase();
 bVal = bVal.toLowerCase();
 }

 let comparison = 0;
 if (aVal > bVal) comparison = 1;
 else if (aVal < bVal) comparison = -1;

 return this.sortConfig.direction === 'desc' ? -comparison : comparison;
 });
 },

 sortBy(key) {
 if (this.sortConfig.key === key) {
 this.sortConfig.direction = this.sortConfig.direction === 'asc' ? 'desc' : 'asc';
 } else {
 this.sortConfig = { key, direction: 'asc' };
 }
 },

 updatePagination() {
 this.totalPages = Math.ceil(this.filteredRows.length / this.itemsPerPage);
 const start = (this.currentPage - 1) * this.itemsPerPage;
 const end = start + this.itemsPerPage;
 this.paginatedRows = this.filteredRows.slice(start, end);
 },

 goToPage(page) {
 if (page >= 1 && page <= this.totalPages) {
 this.currentPage = page;
 }
 },

 get visiblePages() {
 const pages = [];
 const start = Math.max(1, this.currentPage - 2);
 const end = Math.min(this.totalPages, this.currentPage + 2);

 for (let i = start; i <= end; i++) {
 pages.push(i);
 }
 return pages;
 },

 toggleSelectAll() {
 if (this.selectAll) {
 this.selectedRows = this.paginatedRows.map(row => row.id);
 } else {
 this.selectedRows = [];
 }
 },

 formatCellValue(value, header) {
 if (header.type === 'currency') {
 return new Intl.NumberFormat('fr-FR', {
 style: 'currency',
 currency: 'DZD'
 }).format(value);
 }

 if (header.type === 'date') {
 return new Date(value).toLocaleDateString('fr-FR');
 }

 if (header.type === 'badge') {
 const badgeClasses = header.badgeClasses || {};
 const className = badgeClasses[value] || 'bg-gray-100 text-gray-800';
 return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${className}">${value}</span>`;
 }

 return value || '-';
 },

 exportData(format) {
 const data = this.filteredRows;
 const headers = this.headers.map(h => h.label);

 switch(format) {
 case 'csv':
 this.exportToCSV(data, headers);
 break;
 case 'excel':
 this.exportToExcel(data, headers);
 break;
 case 'pdf':
 this.exportToPDF(data, headers);
 break;
 }
 },

 exportToCSV(data, headers) {
 const csvContent = [
 headers.join(','),
 ...data.map(row => Object.values(row).join(','))
 ].join('\n');

 const blob = new Blob([csvContent], { type: 'text/csv' });
 const url = URL.createObjectURL(blob);
 const a = document.createElement('a');
 a.href = url;
 a.download = `export_${new Date().toISOString().slice(0, 10)}.csv`;
 a.click();
 URL.revokeObjectURL(url);
 },

 exportToExcel(data, headers) {
 console.log('Excel export not implemented yet');
 // Implementation with libraries like xlsx
 },

 exportToPDF(data, headers) {
 console.log('PDF export not implemented yet');
 // Implementation with libraries like jsPDF
 }
 }
}
</script>
@endpush
