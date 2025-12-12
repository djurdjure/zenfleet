#!/usr/bin/env python3
# Simple direct replacement for blood_type

edit_path = "/home/lynx/projects/zenfleet/resources/views/admin/drivers/edit.blade.php"

with open(edit_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Lines 160-188 need to be replaced (0-indexed: 159-187)
# Replace with SlimSelect component matching create.blade.php style

new_blood_select = '''                                    <x-select
                                        name="blood_type"
                                        label="Groupe sanguin"
                                        :options="[
                                            '' => 'Sélectionner',
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
                                        :error="$errors->first('blood_type')" />

'''

# Delete lines 159-187 (29 lines total)
# Insert new content
del lines[159:188]
lines.insert(159, new_blood_select)

with open(edit_path, 'w', encoding='utf-8') as f:
    f.writelines(lines)

print("✅ Converted blood_type to x-select component in edit.blade.php")
