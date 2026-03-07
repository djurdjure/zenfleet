@extends('layouts.admin.catalyst')

@section('title', 'Importer un Nouveau Document')

@section('content')
<section class="zf-page min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12 lg:mx-0">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-600 mb-1">
                Importer un Nouveau Document
            </h1>
            <p class="text-xs text-gray-600">
                Ajoutez un document, ses métadonnées métier et ses liaisons à la flotte.
            </p>
        </div>

        @if ($errors->any())
            <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
                Vérifiez les champs du formulaire avant de continuer.
            </x-alert>
        @endif

        @include('admin.documents._form')
    </div>
</section>
@endsection
