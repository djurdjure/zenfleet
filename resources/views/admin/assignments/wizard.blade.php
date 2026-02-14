@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle Affectation')

@section('content')
<section class="zf-page min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12 lg:mx-0">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-600 mb-1">
                Nouvelle Affectation
            </h1>
            <p class="text-xs text-gray-600">
                Complétez les sections ci-dessous pour créer une affectation véhicule/chauffeur
            </p>
        </div>

        @livewire('assignment-form')
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('assignment-created', () => {
        setTimeout(() => {
            window.location.href = '{{ route("admin.assignments.index") }}';
        }, 2000);
    });

    Livewire.on('close-form', () => {
        window.location.href = '{{ route("admin.assignments.index") }}';
    });
});
</script>
@endpush
