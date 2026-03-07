@extends('layouts.admin.catalyst')

@section('title', 'Analytics des Depenses')

@section('content')
<section class="zf-page min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-10">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-600">Analytics des depenses</h1>
                <p class="text-xs text-gray-600">Analyse avancee des couts, tendances et opportunites d'optimisation.</p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.vehicle-expenses.index') }}"
                    class="inline-flex items-center justify-center h-10 px-4 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] transition-all duration-200">
                    Retour liste
                </a>
                @can('expenses.create')
                    <a
                        href="{{ route('admin.vehicle-expenses.create') }}"
                        class="inline-flex items-center justify-center h-10 w-10 rounded-lg border border-[#0c90ee] bg-[#0c90ee] text-white hover:bg-[#0a7fd1] hover:border-[#0a7fd1] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 transition-all duration-200"
                        title="Nouvelle depense">
                        <x-iconify icon="solar:add-circle-bold" class="w-5 h-5" />
                    </a>
                @endcan
            </div>
        </div>

        @livewire('admin.vehicle-expenses.expense-analytics')
    </div>
</section>
@endsection
