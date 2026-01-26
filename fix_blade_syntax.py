#!/usr/bin/env python3
# Fix the broken Blade/JavaScript syntax in create.blade.php

# Read the file
with open('/home/lynx/projects/zenfleet/resources/views/admin/vehicles/create.blade.php', 'r') as f:
    content = f.read()

# Fix the broken zenfleetErrors block
old_block = """    window.zenfleetErrors = {
        hasErrors: {
            {
                $errors->any() ? 'true' : 'false'
            }
        },
        keys: {
            !!json_encode($errors->keys()) !!
        }
    };"""

new_block = """    window.zenfleetErrors = {
        hasErrors: {{ $errors->any() ? 'true' : 'false' }},
        keys: {!! json_encode($errors->keys()) !!}
    };"""

if old_block in content:
    content = content.replace(old_block, new_block)
    print('zenfleetErrors block fixed!')
else:
    print('zenfleetErrors block pattern not found - may already be fixed')

# Write the fixed file
with open('/home/lynx/projects/zenfleet/resources/views/admin/vehicles/create.blade.php', 'w') as f:
    f.write(content)

print('File saved successfully!')
