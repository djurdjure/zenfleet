$path = "resources/views/admin/vehicles/create.blade.php"
$absPath = Join-Path (Get-Location) $path
Write-Host "Patching $absPath"

if (-not (Test-Path $absPath)) {
    Write-Error "File not found: $absPath"
    exit 1
}

$content = Get-Content -Path $absPath -Raw -Encoding UTF8

# Fix currentStep split lines
# Pattern matches:
# currentStep: {
#     {
#         old('current_step', 1)
#     }
# },
$content = $content -replace "currentStep:\s*\{\s*\{\s*old\('current_step',\s*1\)\s*\}\s*\},", "currentStep: {{ old('current_step', 1) }},"

# Fix hasErrors with the - > issue (ignoring whitespace around ->)
# Pattern matches:
# hasErrors: {
#     {
#         $errors - > any() ? 'true' : 'false'
#     }
# },
# Escaping $ for regex ($) and for PowerShell (`$)
$content = $content -replace "hasErrors:\s*\{\s*\{\s*`$errors\s*-\s*>\s*any\(\)\s*\?\s*'true'\s*:\s*'false'\s*\}\s*\},", "hasErrors: {{ `$errors->any() ? 'true' : 'false' }},"

# Fix keys with the - > issue
# keys: {
#     !!json_encode($errors - > keys()) !!
# }
$content = $content -replace "keys:\s*\{\s*!!json_encode\(`$errors\s*-\s*>\s*keys\(\)\)\s*!!\s*\}", "keys: {!! json_encode(`$errors->keys()) !!}"

# Fix general - > issue if any remain in this file
$content = $content -replace "`$errors\s*-\s*>\s*", "`$errors->"

# Fix duplicate comments
$content = $content -replace "    // Données d'erreurs serveur \(injectées depuis PHP\)\s*// Données d'erreurs serveur \(injectées depuis PHP\)", "    // Données d'erreurs serveur (injectées depuis PHP)"

Set-Content -Path $absPath -Value $content -Encoding UTF8
Write-Host "File updated successfully."
