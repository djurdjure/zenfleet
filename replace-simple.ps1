# Script PowerShell: Remplacement TomSelect vers SlimSelect
# Utilise regex pour remplacer les composants

$sourceFile = "\\wsl.localhost\Ubuntu-22.04\home\lynx\projects\zenfleet\resources\views\admin\vehicles\create.blade.php"
$backupFile = "\\wsl.localhost\Ubuntu-22.04\home\lynx\projects\zenfleet\resources\views\admin\vehicles\create.blade.php.backup"

Write-Host "Debut du remplacement TomSelect vers SlimSelect" -ForegroundColor Cyan

# Créer sauvegarde
Copy-Item $sourceFile $backupFile -Force
Write-Host "Sauvegarde creee: $backupFile" -ForegroundColor Green

# Lire le fichier
$content = Get-Content $sourceFile -Raw

# Pattern pour trouver x-tom-select name="vehicle_type_id"
$content = $content -replace '<x-tom-select\s+name="vehicle_type_id"[^>]*@change="validateField\(''vehicle_type_id'', \$event\.target\.value\)"\s*/>', '<div><label for="vehicle_type_id" class="block text-sm font-medium text-gray-700 mb-2"><div class="flex items-center gap-2"><x-iconify icon="heroicons:cube" class="w-4 h-4 text-gray-500" />Type de Vehicule<span class="text-red-500">*</span></div></label><div id="vehicle-type-wrapper"><select id="vehicle_type_id" name="vehicle_type_id" class="slimselect-vehicle-type w-full" required @change="validateField(''vehicle_type_id'', $event.target.value)"><option data-placeholder="true" value=""></option>@foreach($vehicleTypes as $type)<option value="{{ $type->id }}" @selected(old(''vehicle_type_id'') == $type->id)>{{ $type->name }}</option>@endforeach</select></div>@error(''vehicle_type_id'')<p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p>@enderror</div>'

Write-Host "Remplacement de vehicle_type_id termine" -ForegroundColor Yellow

# Sauvegarder
Set-Content -Path $sourceFile -Value $content -Encoding UTF8 -NoNewline

Write-Host "Fichier modifie avec succes!" -ForegroundColor Green
