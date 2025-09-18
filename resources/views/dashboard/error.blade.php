@extends('layouts.admin.app')

@section('title', 'Erreur Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 -m-6 p-6 flex items-center justify-center">
    <div class="max-w-2xl mx-auto">
        {{-- Carte d'erreur principale --}}
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-12 text-center">
            {{-- Icône d'erreur --}}
            <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg">
                <i class="fas fa-exclamation-triangle text-white text-3xl"></i>
            </div>

            {{-- Titre d'erreur --}}
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Erreur Dashboard
            </h1>

            {{-- Message d'erreur --}}
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">
                <p class="text-red-800 font-medium text-lg">
                    {{ $error ?? 'Données partiellement indisponibles - Mode dégradé activé' }}
                </p>
            </div>

            {{-- Informations utilisateur --}}
            @if(isset($user) && $user)
            <div class="bg-gray-50 rounded-2xl p-6 mb-8">
                <h3 class="font-semibold text-gray-900 mb-3">Informations de session</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Utilisateur:</span>
                        <span class="font-medium text-gray-900">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium text-gray-900">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Rôle:</span>
                        <span class="font-medium text-gray-900">{{ $user->getRoleNames()->first() ?? 'Non défini' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Timestamp:</span>
                        <span class="font-medium text-gray-900">{{ $timestamp ?? now()->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Actions de récupération --}}
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Rafraîchir --}}
                    <button onclick="window.location.reload()"
                            class="flex items-center justify-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-sync-alt"></i>
                        <span>Rafraîchir la page</span>
                    </button>

                    {{-- Retour accueil --}}
                    <a href="{{ route('home') }}"
                       class="flex items-center justify-center gap-3 px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-home"></i>
                        <span>Retour à l'accueil</span>
                    </a>
                </div>

                {{-- Déconnexion --}}
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="flex items-center justify-center gap-3 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl mx-auto">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Se déconnecter</span>
                    </button>
                </form>
            </div>

            {{-- Contact support --}}
            @if(isset($supportContact))
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-gray-600 text-sm">
                    Si le problème persiste, contactez le support technique :
                    <a href="mailto:{{ $supportContact }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        {{ $supportContact }}
                    </a>
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh après 30 secondes (optionnel)
setTimeout(() => {
    if (confirm('Voulez-vous réessayer de charger le dashboard ?')) {
        window.location.reload();
    }
}, 30000);
</script>
@endpush