{{-- 🔗 STEP 4: Compte Utilisateur & Contact d'Urgence --}}
<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-900 mb-2">Compte Utilisateur & Contact d'Urgence</h3>
    <p class="text-gray-600">Liez le chauffeur à un compte utilisateur et définissez un contact d'urgence</p>
</div>

<div class="space-y-8">
    {{-- User Account Section --}}
    <div class="bg-blue-50 rounded-xl p-6">
        <h4 class="text-lg font-semibold text-blue-900 mb-4">
            <i class="fas fa-user-circle mr-2"></i>Compte Utilisateur
        </h4>

        @if(isset($driver) && $driver->user_id)
            {{-- Mode Édition - Utilisateur déjà lié --}}
            <div class="bg-white rounded-lg p-4 border-2 border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($driver->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $driver->user->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-600">{{ $driver->user->email ?? 'N/A' }}</div>
                            <span class="inline-flex items-center px-2 py-1 mt-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-link mr-1"></i> Compte Actif
                            </span>
                        </div>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
            </div>

            {{-- Hidden field to maintain the link --}}
            <input type="hidden" name="user_id" value="{{ $driver->user_id }}">

            <p class="mt-3 text-sm text-blue-700">
                <i class="fas fa-info-circle mr-1"></i>
                Ce chauffeur est lié au compte utilisateur ci-dessus. Cette liaison ne peut pas être modifiée après création.
            </p>
        @else
            {{-- Mode Création ou pas encore lié --}}
            <div>
                <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    Lier à un compte utilisateur existant (Optionnel)
                </label>
                <select id="user_id" name="user_id"
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all appearance-none">
                    <option value="">Ne pas lier de compte</option>
                    @foreach($linkableUsers ?? [] as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-blue-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Si aucun compte n'est lié, un compte sera automatiquement créé avec l'email personnel.
                </p>
            </div>
        @endif
    </div>

    {{-- Emergency Contact Section --}}
    <div class="bg-red-50 rounded-xl p-6">
        <h4 class="text-lg font-semibold text-red-900 mb-4">
            <i class="fas fa-phone-square-alt mr-2"></i>Contact d'Urgence
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="emergency_contact_name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user text-gray-400 mr-2"></i>Nom du Contact
                </label>
                <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $driver->emergency_contact_name ?? '') }}"
                       class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                @error('emergency_contact_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="emergency_contact_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-phone text-gray-400 mr-2"></i>Téléphone d'Urgence
                </label>
                <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $driver->emergency_contact_phone ?? '') }}"
                       class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                @error('emergency_contact_phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>
