{{-- 💼 STEP 2: Informations Professionnelles - VERSION ENTERPRISE ULTRA-ROBUSTE --}}
<div class="mb-6">
 <h3 class="text-2xl font-bold text-gray-900 mb-2">Informations Professionnelles</h3>
 <p class="text-gray-600">Définissez le statut et les informations d'emploi</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label for="employee_number" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-id-badge text-gray-400 mr-2"></i>Matricule Employé
 </label>
 <input type="text" id="employee_number" name="employee_number" value="{{ old('employee_number', $driver->employee_number ?? '') }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('employee_number')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- 🚀 SOLUTION ENTERPRISE ULTRA-ROBUSTE POUR LES STATUTS --}}
 @php
 // Récupération sécurisée des statuts avec fallback garanti
 $statusesData = isset($driverStatuses) && $driverStatuses ? $driverStatuses : collect([]);
 
 // Si aucun statut, utiliser des valeurs par défaut
 if ($statusesData->isEmpty()) {
 $statusesData = collect([
 ['id' => 1, 'name' => 'Disponible', 'description' => 'Chauffeur disponible', 'color' => '#10B981', 'icon' => 'fa-check-circle', 'can_drive' => true, 'can_assign' => true],
 ['id' => 2, 'name' => 'En mission', 'description' => 'En cours de mission', 'color' => '#3B82F6', 'icon' => 'fa-truck', 'can_drive' => true, 'can_assign' => false],
 ['id' => 3, 'name' => 'En congé', 'description' => 'En congé', 'color' => '#F59E0B', 'icon' => 'fa-calendar-times', 'can_drive' => false, 'can_assign' => false],
 ['id' => 4, 'name' => 'Inactif', 'description' => 'Inactif', 'color' => '#EF4444', 'icon' => 'fa-ban', 'can_drive' => false, 'can_assign' => false],
 ]);
 }
 
 // Conversion en array pour JavaScript
 $statusesJson = $statusesData->map(function($status) {
 if (is_array($status)) {
 return $status;
 }
 return [
 'id' => $status->id ?? $status['id'],
 'name' => $status->name ?? $status['name'],
 'description' => $status->description ?? $status['description'] ?? '',
 'color' => $status->color ?? $status['color'] ?? '#6B7280',
 'icon' => $status->icon ?? $status['icon'] ?? 'fa-circle',
 'can_drive' => isset($status->can_drive) ? $status->can_drive : ($status['can_drive'] ?? true),
 'can_assign' => isset($status->can_assign) ? $status->can_assign : ($status['can_assign'] ?? true),
 ];
 })->values()->toArray();
 @endphp

 {{-- 🎯 Sélecteur de statut simplifié (style repair-requests) --}}
 <div>
 <label for="status_id" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-user-check text-blue-500 mr-2"></i>
 Statut du Chauffeur <span class="text-red-500">*</span>
 </label>

 <select id="status_id" name="status_id" required
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 <option value="">Sélectionner un statut</option>
 @forelse($statusesData as $status)
 @php
 $statusId = is_array($status) ? $status['id'] : $status->id;
 $statusName = is_array($status) ? $status['name'] : $status->name;
 $statusDesc = is_array($status) ? ($status['description'] ?? '') : ($status->description ?? '');
 $canDrive = is_array($status) ? ($status['can_drive'] ?? false) : ($status->can_drive ?? false);
 $canAssign = is_array($status) ? ($status['can_assign'] ?? false) : ($status->can_assign ?? false);

 // Emoji basé sur les capacités
 $emoji = '⚪';
 if ($canDrive && $canAssign) {
 $emoji = '🟢'; // Disponible
 } elseif ($canDrive && !$canAssign) {
 $emoji = '🔵'; // En service
 } elseif (!$canDrive && !$canAssign) {
 $emoji = '🟠'; // Limité
 }

 $displayText = $emoji . ' ' . $statusName;
 if ($statusDesc) {
 $displayText .= ' - ' . $statusDesc;
 }
 @endphp
 <option value="{{ $statusId }}"
 {{ old('status_id', $driver->status_id ?? '') == $statusId ? 'selected' : '' }}>
 {{ $displayText }}
 </option>
 @empty
 <option value="" disabled>Aucun statut disponible</option>
 @endforelse
 </select>

 @error('status_id')
 <p class="mt-2 text-sm text-red-600">
 <i class="fas fa-exclamation-triangle mr-1"></i>
 {{ $message }}
 </p>
 @enderror

 @if($statusesData->isEmpty())
 <p class="mt-2 text-xs text-orange-600">
 <i class="fas fa-exclamation-circle mr-1"></i>
 ⚠️ Aucun statut trouvé dans votre organisation
 </p>
 @else
 <p class="mt-2 text-xs text-gray-500">
 <i class="fas fa-info-circle mr-1"></i>
 {{ $statusesData->count() }} statut(s) disponible(s)
 </p>
 @endif
 </div>

 {{-- Date de recrutement --}}
 <div>
 <label for="recruitment_date" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-calendar-plus text-gray-400 mr-2"></i>Date de Recrutement
 </label>
 <input type="date" id="recruitment_date" name="recruitment_date" 
 value="{{ old('recruitment_date', ($driver->recruitment_date ?? null)?->format('Y-m-d')) }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('recruitment_date')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Date de fin de contrat --}}
 <div>
 <label for="contract_end_date" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-calendar-times text-gray-400 mr-2"></i>Date de Fin de Contrat
 </label>
 <input type="date" id="contract_end_date" name="contract_end_date"
 value="{{ old('contract_end_date', ($driver->contract_end_date ?? null)?->format('Y-m-d')) }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 @error('contract_end_date')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
</div>
