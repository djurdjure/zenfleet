#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script to convert blood_type from native select to SlimSelect in edit.blade.php and create.blade.php
"""

import re

def convert_blood_type_to_slimselect(filepath):
    """Convert blood_type select to SlimSelect component"""
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Pattern to match the entire blood_type select block (native select)
    # It starts with the label div and ends with @enderror closing div
    pattern_edit = r'''                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Groupe sanguin
                                            @if\(\$driver->blood_type\)
                                            <span class="ml-2 text-xs text-gray-500 font-normal">
                                                \(Actuel: \{\{ \$driver->blood_type \}\}\)
                                            </span>
                                            @endif
                                        </label>
                                        <select
                                            name="blood_type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error\('blood_type'\) border-red-500 @enderror">
                                            <option value="">Sélectionner</option>
                                            <option value="A\+" \{\{ old\('blood_type', \$driver->blood_type\) == 'A\+' \? 'selected' : '' \}\}>A\+</option>
                                            <option value="A-" \{\{ old\('blood_type', \$driver->blood_type\) == 'A-' \? 'selected' : '' \}\}>A-</option>
                                            <option value="B\+" \{\{ old\('blood_type', \$driver->blood_type\) == 'B\+' \? 'selected' : '' \}\}>B\+</option>
                                            <option value="B-" \{\{ old\('blood_type', \$driver->blood_type\) == 'B-' \? 'selected' : '' \}\}>B-</option>
                                            <option value="AB\+" \{\{ old\('blood_type', \$driver->blood_type\) == 'AB\+' \? 'selected' : '' \}\}>AB\+</option>
                                            <option value="AB-" \{\{ old\('blood_type', \$driver->blood_type\) == 'AB-' \? 'selected' : '' \}\}>AB-</option>
                                            <option value="O\+" \{\{ old\('blood_type', \$driver->blood_type\) == 'O\+' \? 'selected' : '' \}\}>O\+</option>
                                            <option value="O-" \{\{ old\('blood_type', \$driver->blood_type\) == 'O-' \? 'selected' : '' \}\}>O-</option>
                                        </select>
                                        @error\('blood_type'\)
                                        <p class="mt-1\.5 text-sm text-red-600 flex items-center gap-1">
                                            <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                            \{\{ \$message \}\}
                                        </p>
                                        @enderror
                                    </div>'''
    
    replacement_edit = '''                                    <x-slim-select
                                        name="blood_type"
                                        label="Groupe sanguin"
                                        :options="[
                                            'A+' => 'A+',
                                            'A-' => 'A-',
                                            'B+' => 'B+',
                                            'B-' => 'B-',
                                            'AB+' => 'AB+',
                                            'AB-' => 'AB-',
                                            'O+' => 'O+',
                                            'O-' => 'O-'
                                        ]"
                                        :selected="old('blood_type', $driver->blood_type)"
                                        placeholder="Sélectionner le groupe sanguin"
                                        :error="$errors->first('blood_type')"
                                        helpText="Groupe sanguin du chauffeur" />
'''
    
    # Try to replace
    content_new = re.sub(pattern_edit, replacement_edit, content, flags=re.DOTALL)
    
    if content != content_new:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content_new)
        print(f"✅ Converted blood_type to SlimSelect in {filepath}")
        return True
    else:
        print(f"⚠️  Pattern not found in {filepath}")
        return False

# Convert both files
edit_path = "/home/lynx/projects/zenfleet/resources/views/admin/drivers/edit.blade.php"
create_path = "/home/lynx/projects/zenfleet/resources/views/admin/drivers/create.blade.php"

convert_blood_type_to_slimselect(edit_path)
# convert_blood_type_to_slimselect(create_path)  # Uncomment if create.blade.php needs same change

print("\n✅ Blood type conversion completed!")
