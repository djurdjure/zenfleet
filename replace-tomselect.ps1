# ============================================================================
# 🎯 Script PowerShell: Remplacement TomSelect → SlimSelect
# ============================================================================
# Remplace intelligemment les composants x-tom-select par SlimSelect
# tout en préservant l'indentation exacte du fichier
# ============================================================================

$sourceFile = "\\wsl.localhost\Ubuntu-22.04\home\lynx\projects\zenfleet\resources\views\admin\vehicles\create.blade.php"
$backupFile = "\\wsl.localhost\Ubuntu-22.04\home\lynx\projects\zenfleet\resources\views\admin\vehicles\create.blade.php.backup"

Write-Host "🚀 Début du remplacement TomSelect → SlimSelect" -ForegroundColor Cyan
Write-Host "📁 Fichier: $sourceFile" -ForegroundColor Gray

# Créer une sauvegarde
Write-Host "💾 Création de la sauvegarde..." -ForegroundColor Yellow
Copy-Item $sourceFile $backupFile -Force
Write-Host "✅ Sauvegarde créée: $backupFile" -ForegroundColor Green

# Lire le fichier
$content = Get-Content $sourceFile -Raw

# Compteur de remplacements
$replacementCount = 0

# ============================================================================
# REMPLACEMENT 1: vehicle_type_id
# ============================================================================
$pattern1 = '(?s)(<x-tom-select\s+name="vehicle_type_id".*?/>)'
$replacement1 = @'
{{-- Type de Véhicule (SlimSelect) --}}
                    <div>
                        <label for="vehicle_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:cube" class="w-4 h-4 text-gray-500" />
                                Type de Véhicule
                                <span class="text-red-500">*</span>
                            </div>
                        </label>
                        <div id="vehicle-type-wrapper">
                            <select
                                id="vehicle_type_id"
                                name="vehicle_type_id"
                                class="slimselect-vehicle-type w-full"
                                required
                                @change="validateField('vehicle_type_id', $event.target.value)">
                                <option data-placeholder="true" value=""></option>
                                @foreach($vehicleTypes as $type)
                                    <option value="{{ $type->id }}" @selected(old('vehicle_type_id') == $type->id)>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('vehicle_type_id')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
'@

if ($content -match $pattern1) {
    $content = $content -replace $pattern1, $replacement1
    $replacementCount++
    Write-Host "✅ Remplacé: vehicle_type_id" -ForegroundColor Green
}

# ============================================================================
# REMPLACEMENT 2: fuel_type_id
# ============================================================================
$pattern2 = '(?s)(<x-tom-select\s+name="fuel_type_id".*?/>)'
$replacement2 = @'
{{-- Type de Carburant (SlimSelect) --}}
                    <div>
                        <label for="fuel_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:fire" class="w-4 h-4 text-gray-500" />
                                Type de Carburant
                                <span class="text-red-500">*</span>
                            </div>
                        </label>
                        <div id="fuel-type-wrapper">
                            <select
                                id="fuel_type_id"
                                name="fuel_type_id"
                                class="slimselect-fuel-type w-full"
                                required
                                @change="validateField('fuel_type_id', $event.target.value)">
                                <option data-placeholder="true" value=""></option>
                                @foreach($fuelTypes as $type)
                                    <option value="{{ $type->id }}" @selected(old('fuel_type_id') == $type->id)>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('fuel_type_id')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
'@

if ($content -match $pattern2) {
    $content = $content -replace $pattern2, $replacement2
    $replacementCount++
    Write-Host "✅ Remplacé: fuel_type_id" -ForegroundColor Green
}

# ============================================================================
# REMPLACEMENT 3: transmission_type_id
# ============================================================================
$pattern3 = '(?s)(<x-tom-select\s+name="transmission_type_id".*?/>)'
$replacement3 = @'
{{-- Type de Transmission (SlimSelect) --}}
                    <div>
                        <label for="transmission_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:cog-6-tooth" class="w-4 h-4 text-gray-500" />
                                Type de Transmission
                                <span class="text-red-500">*</span>
                            </div>
                        </label>
                        <div id="transmission-type-wrapper">
                            <select
                                id="transmission_type_id"
                                name="transmission_type_id"
                                class="slimselect-transmission-type w-full"
                                required
                                @change="validateField('transmission_type_id', $event.target.value)">
                                <option data-placeholder="true" value=""></option>
                                @foreach($transmissionTypes as $type)
                                    <option value="{{ $type->id }}" @selected(old('transmission_type_id') == $type->id)>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('transmission_type_id')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
'@

if ($content -match $pattern3) {
    $content = $content -replace $pattern3, $replacement3
    $replacementCount++
    Write-Host "✅ Remplacé: transmission_type_id" -ForegroundColor Green
}

# ============================================================================
# REMPLACEMENT 4: status_id
# ============================================================================
$pattern4 = '(?s)(<x-tom-select\s+name="status_id".*?/>)'
$replacement4 = @'
{{-- Statut Initial (SlimSelect) --}}
                        <div id="status-wrapper">
                            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="heroicons:signal" class="w-4 h-4 text-gray-500" />
                                    Statut Initial
                                    <span class="text-red-500">*</span>
                                </div>
                            </label>
                            <select
                                id="status_id"
                                name="status_id"
                                class="slimselect-status w-full"
                                required
                                @change="validateField('status_id', $event.target.value)">
                                <option data-placeholder="true" value=""></option>
                                @foreach($vehicleStatuses as $status)
                                    <option value="{{ $status->id }}" @selected(old('status_id') == $status->id)>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_id')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1.5 text-xs text-gray-500">État opérationnel du véhicule</p>
                        </div>
'@

if ($content -match $pattern4) {
    $content = $content -replace $pattern4, $replacement4
    $replacementCount++
    Write-Host "✅ Remplacé: status_id" -ForegroundColor Green
}

# ============================================================================
# REMPLACEMENT 5: users (multiple)
# ============================================================================
$pattern5 = '(?s)(<x-tom-select\s+name="users".*?/>)'
$replacement5 = @'
{{-- Utilisateurs Autorisés (SlimSelect Multiple) --}}
                        <div id="users-wrapper">
                            <label for="users" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="heroicons:users" class="w-4 h-4 text-gray-500" />
                                    Utilisateurs Autorisés
                                </div>
                            </label>
                            <select
                                id="users"
                                name="users[]"
                                class="slimselect-users w-full"
                                multiple>
                                @foreach($users as $user)
                                    <option 
                                        value="{{ $user->id }}" 
                                        @selected(in_array($user->id, old('users', [])))>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('users')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1.5 text-xs text-gray-500">Sélectionnez les utilisateurs autorisés à utiliser ce véhicule</p>
                        </div>
'@

if ($content -match $pattern5) {
    $content = $content -replace $pattern5, $replacement5
    $replacementCount++
    Write-Host "✅ Remplacé: users" -ForegroundColor Green
}

# ============================================================================
# Sauvegarder le fichier modifié
# ============================================================================
Write-Host "`n💾 Sauvegarde des modifications..." -ForegroundColor Yellow
Set-Content -Path $sourceFile -Value $content -Encoding UTF8 -NoNewline

Write-Host "`n✅ TERMINÉ!" -ForegroundColor Green
Write-Host "📊 Nombre de remplacements effectués: $replacementCount/5" -ForegroundColor Cyan
Write-Host "📁 Fichier modifié: $sourceFile" -ForegroundColor Gray
Write-Host "💾 Sauvegarde: $backupFile" -ForegroundColor Gray

if ($replacementCount -eq 5) {
    Write-Host "`n🎉 SUCCÈS: Tous les composants TomSelect ont été remplacés!" -ForegroundColor Green
} else {
    Write-Host "`n⚠️  ATTENTION: Seulement $replacementCount/5 composants remplacés" -ForegroundColor Yellow
    Write-Host "Vérifiez manuellement le fichier pour les composants manquants" -ForegroundColor Yellow
}
