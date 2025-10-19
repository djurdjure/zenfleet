<div class="bg-red-50 border border-red-200 rounded-lg p-8 text-center">
 <i class="fas fa-exclamation-triangle text-red-500 text-5xl mb-4"></i>
 <h3 class="text-xl font-bold text-red-900 mb-3">Erreur de chargement</h3>
 <p class="text-red-700 mb-4">{{ $error ?? 'Une erreur inattendue s\'est produite lors du chargement des organisations.' }}</p>
 <div class="space-x-4">
 <button onclick="window.location.reload()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
 <i class="fas fa-redo mr-2"></i>
 Recharger la page
 </button>
 <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
 <i class="fas fa-home mr-2"></i>
 Retour au dashboard
 </a>
 </div>
</div>

