@if($errors->any())
 <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg shadow-sm mb-8 fade-in">
 <div class="flex items-start">
 <div class="flex-shrink-0">
 <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-full">
 <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <div class="flex items-center justify-between">
 <h3 class="text-lg font-semibold text-red-800">
 {{ $errors->count() === 1 ? 'Une erreur détectée' : $errors->count() . ' erreurs détectées' }}
 </h3>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-200 text-red-800">
 {{ $errors->count() }}
 </span>
 </div>
 <p class="mt-1 text-sm text-red-700">
 Veuillez corriger les erreurs ci-dessous avant de soumettre le formulaire.
 </p>

 <!-- Liste des erreurs par champ -->
 <div class="mt-4 space-y-2">
 @foreach($errors->getBag('default')->toArray() as $field => $fieldErrors)
 <div class="flex items-start gap-2 text-sm">
 <i class="fas fa-arrow-right text-red-400 mt-0.5"></i>
 <div>
 <span class="font-medium text-red-800 capitalize">{{ str_replace('_', ' ', $field) }}:</span>
 @foreach($fieldErrors as $error)
 <span class="text-red-700">{{ $error }}</span>
 @if(!$loop->last), @endif
 @endforeach
 </div>
 </div>
 @endforeach
 </div>
 </div>

 <!-- Bouton pour faire défiler vers la première erreur -->
 <div class="ml-4">
 <button
 type="button"
 onclick="scrollToFirstError()"
 class="inline-flex items-center px-3 py-2 border border-red-300 bg-white text-red-700 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
 <i class="fas fa-search text-sm mr-2"></i>
 Aller à la première erreur
 </button>
 </div>
 </div>
 </div>

 <script>
 function scrollToFirstError() {
 const firstErrorField = document.querySelector('[aria-invalid="true"]');
 if (firstErrorField) {
 firstErrorField.scrollIntoView({
 behavior: 'smooth',
 block: 'center'
 });
 firstErrorField.focus();
 }
 }

 // Auto-scroll vers la première erreur au chargement
 document.addEventListener('DOMContentLoaded', function() {
 if ({{ $errors->count() > 0 ? 'true' : 'false' }}) {
 setTimeout(() => {
 scrollToFirstError();
 }, 500);
 }
 });
 </script>
@endif