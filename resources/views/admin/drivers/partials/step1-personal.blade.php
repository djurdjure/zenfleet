<div class="mb-6">
 <h3 class="text-2xl font-bold text-gray-900 mb-2">Informations Personnelles</h3>
 <p class="text-gray-600">{{ isset($driver) && $driver ? 'Modifiez' : 'Saisissez' }} les informations personnelles du chauffeur</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
 <!-- Photo Section -->
 <div class="lg:col-span-1">
 <div class="bg-gray-50 rounded-xl p-6 text-center">
 <label for="photo" class="block text-sm font-semibold text-gray-700 mb-4">
 <i class="fas fa-camera text-gray-400 mr-2"></i>Photo de Profil
 </label>

 <div class="mb-4">
 <div x-show="!photoPreview" class="w-32 h-32 mx-auto bg-gray-200 rounded-full flex items-center justify-center">
 <i class="fas fa-user text-gray-400 text-4xl"></i>
 </div>
 <img x-show="photoPreview" :src="photoPreview" class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-white shadow-lg">
 </div>

 <input id="photo" name="photo" type="file" @change="updatePhotoPreview($event)"
 accept="image/*"
 class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">

 @error('photo')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 <!-- Personal Information -->
 <div class="lg:col-span-2 space-y-6">
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-user text-gray-400 mr-2"></i>Prénom <span class="text-red-500">*</span>
 </label>
 <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $driver->first_name ?? '') }}" required
 @blur="validateField('first_name', $event.target.value)"
 @input="validateField('first_name', $event.target.value)"
 :class="hasError('first_name') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-100' : 'border-gray-200 bg-white focus:border-blue-400 focus:ring-blue-50'"
 class="w-full px-4 py-3 rounded-xl focus:ring-4 transition-all">
 @error('first_name')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 <p x-show="hasError('first_name')" x-text="errors.first_name" class="mt-2 text-sm text-red-600" style="display: none;">
 <i class="fas fa-exclamation-circle mr-1"></i>
 </p>
 </div>

 <div>
 <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-user text-gray-400 mr-2"></i>Nom <span class="text-red-500">*</span>
 </label>
 <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $driver->last_name ?? '') }}" required
 @blur="validateField('last_name', $event.target.value)"
 @input="validateField('last_name', $event.target.value)"
 :class="hasError('last_name') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-100' : 'border-gray-200 bg-white focus:border-blue-400 focus:ring-blue-50'"
 class="w-full px-4 py-3 rounded-xl focus:ring-4 transition-all">
 @error('last_name')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 <p x-show="hasError('last_name')" x-text="errors.last_name" class="mt-2 text-sm text-red-600" style="display: none;">
 <i class="fas fa-exclamation-circle mr-1"></i>
 </p>
 </div>

 <div>
 <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-calendar text-gray-400 mr-2"></i>Date de Naissance
 </label>
 <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', ($driver?->birth_date)?->format('Y-m-d')) }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('birth_date')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="blood_type" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-tint text-gray-400 mr-2"></i>Groupe Sanguin
 </label>
 <input type="text" id="blood_type" name="blood_type" value="{{ old('blood_type', $driver->blood_type ?? '') }}"
 placeholder="Ex: O+"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('blood_type')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="personal_phone" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-phone text-gray-400 mr-2"></i>Téléphone Personnel
 </label>
 <input type="tel" id="personal_phone" name="personal_phone" value="{{ old('personal_phone', $driver->personal_phone ?? '') }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('personal_phone')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="personal_email" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-envelope text-gray-400 mr-2"></i>Email Personnel
 </label>
 <input type="email" id="personal_email" name="personal_email" value="{{ old('personal_email', $driver->personal_email ?? '') }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('personal_email')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 <div>
 <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>Adresse Complète
 </label>
 <textarea id="address" name="address" rows="3"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">{{ old('address', $driver->address ?? '') }}</textarea>
 @error('address')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>
</div>
