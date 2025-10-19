{{-- 🆔 STEP 3: Permis de Conduire --}}
<div class="mb-6">
 <h3 class="text-2xl font-bold text-gray-900 mb-2">Permis de Conduire</h3>
 <p class="text-gray-600">Informations sur le permis de conduire du chauffeur</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label for="license_number" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-id-card text-gray-400 mr-2"></i>Numéro de Permis
 </label>
 <input type="text" id="license_number" name="license_number" value="{{ old('license_number', $driver->license_number ?? '') }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('license_number')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="license_category" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-certificate text-gray-400 mr-2"></i>Catégorie(s)
 </label>
 <input type="text" id="license_category" name="license_category" value="{{ old('license_category', $driver->license_category ?? '') }}"
 placeholder="Ex: B, C1E"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('license_category')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="license_issue_date" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-calendar text-gray-400 mr-2"></i>Date de Délivrance
 </label>
 <input type="date" id="license_issue_date" name="license_issue_date" value="{{ old('license_issue_date', ($driver?->license_issue_date)?->format('Y-m-d')) }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('license_issue_date')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="license_authority" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-building text-gray-400 mr-2"></i>Autorité de Délivrance
 </label>
 <input type="text" id="license_authority" name="license_authority" value="{{ old('license_authority', $driver->license_authority ?? '') }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('license_authority')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
</div>
