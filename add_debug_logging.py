#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Add debug logging to DriverController update method
"""

file_path = "/home/lynx/projects/zenfleet/app/Http/Controllers/Admin/DriverController.php"

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Find and replace the update method
old_code = '''try {
            // Vérification des permissions pour l'organisation
            if (!auth()->user()->hasRole('Super Admin') && $driver->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Vous n\\'avez pas l\\'autorisation de modifier ce chauffeur.');
            }

            $updatedDriver = $this->driverService->updateDriver($driver, $request->validated());'''

new_code = '''try {
            // Vérification des permissions pour l'organisation
            if (!auth()->user()->hasRole('Super Admin') && $driver->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Vous n\\'avez pas l\\'autorisation de modifier ce chauffeur.');
            }

            // 🔍 DEBUG: Logger les données reçues AVANT validation
            Log::info('[DriverController] === DEBUG license_categories ===', [
                'driver_id' => $driver->id,
                'raw_license_categories' => $request->input('license_categories'),
                'license_categories_type' => gettype($request->input('license_categories')),
            ]);

            $validatedData = $request->validated();

            // 🔍 DEBUG: Logger les données APRÈS validation
            Log::info('[DriverController] Validated data', [
                'driver_id' => $driver->id,
                'has_license_categories' => isset($validatedData['license_categories']),
                'license_categories_value' => $validatedData['license_categories'] ?? 'NOT_SET',
                'license_categories_type' => isset($validatedData['license_categories']) ? gettype($validatedData['license_categories']) : 'N/A',
            ]);

            $updatedDriver = $this->driverService->updateDriver($driver, $validatedData);'''

if old_code in content:
    content = content.replace(old_code, new_code)
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("✅ Debug logging added to DriverController.update()")
else:
    print("⚠️ Target code not found in file. Looking for alternative patterns...")
    # Try alternative pattern
    if '$this->driverService->updateDriver($driver, $request->validated())' in content:
        print("Found updateDriver call - file structure may have changed")
    else:
        print("updateDriver call not found at all")
