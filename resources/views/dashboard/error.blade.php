@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Erreur Dashboard
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                {{ $error ?? 'Une erreur est survenue lors du chargement du dashboard.' }}
            </p>
            <div class="mt-6">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-redo mr-2"></i>
                    Réessayer
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

