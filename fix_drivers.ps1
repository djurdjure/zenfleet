$createPath = "resources\views\admin\drivers\create.blade.php"
$editPath = "resources\views\admin\drivers\edit.blade.php"

function Fix-File($path) {
    if (Test-Path $path) {
        $content = Get-Content $path -Raw
        
        # Fix 1: PHP Object Operator spacing " - >" -> "->"
        $content = $content -replace ' - >', '->'
        
        # Fix 2: Multi-line currentStep Blade echo -> Single line
        # Regex to match the specific multi-line structure including potential whitespace
        $content = $content -replace 'currentStep:\s*\{\s*\{\s*old\(''current_step'',\s*1\)\s*\}\s*\},', 'currentStep: {{ old(''current_step'', 1) }},'
        
        # Fix 2b: Fallback for different brace spacing if the above misses
        $content = $content -replace 'currentStep:\s*\{\s*\{\s*old\(''current_step'',\s*1\)\s*\}\s*\}', 'currentStep: {{ old(''current_step'', 1) }}'

        Set-Content -Path $path -Value $content -NoNewline
        Write-Host "Fixed $path"
    } else {
        Write-Host "File not found: $path"
    }
}

Fix-File $createPath
Fix-File $editPath
