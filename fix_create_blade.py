import os
import re

file_path = r'\\wsl.localhost\Ubuntu-22.04\home\lynx\projects\zenfleet\resources\views\admin\vehicles\create.blade.php'

# Use local path if running in WSL context, but we are likely in Windows.
# The tool inputs use the \\wsl.localhost path.
# Python open() might struggle with UNC paths on some setups, but let's try.
# If it fails, we assume relative path from CWD or standard path.

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
except FileNotFoundError:
    # Try relative path
    file_path = 'resources/views/admin/vehicles/create.blade.php'
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

print(f"Read {len(content)} bytes.")

# Fix currentStep split lines
# Pattern: currentStep: {\s*{\s*old
content = re.sub(
    r'currentStep:\s*\{\s*\{\s*old\(\'current_step\',\s*1\)\s*\}\s*\},',
    "currentStep: {{ old('current_step', 1) }},",
    content,
    flags=re.MULTILINE | re.DOTALL
)

# Fix window.zenfleetErrors hasErrors
# Pattern: hasErrors: {\s*{\s*$errors
# Handling the broken - > as well
content = re.sub(
    r'hasErrors:\s*\{\s*\{\s*\$errors\s*-\s*>\s*any\(\)\s*\?\s*\'true\'\s*:\s*\'false\'\s*\}\s*\},',
    "hasErrors: {{ $errors->any() ? 'true' : 'false' }},",
    content,
    flags=re.MULTILINE | re.DOTALL
)

# Fix window.zenfleetErrors keys
# Pattern: keys: {\s*!!json_encode
content = re.sub(
    r'keys:\s*\{\s*!!json_encode\(\$errors\s*-\s*>\s*keys\(\)\)\s*!!\s*\}',
    "keys: {!! json_encode($errors->keys()) !!}",
    content,
    flags=re.MULTILINE | re.DOTALL
)

# Fix general - > issue if any remain in this file
content = content.replace('$errors - >', '$errors->')

# Fix duplicated comments if they exist
content = content.replace(
    "    // Données d'erreurs serveur (injectées depuis PHP)\n    // Données d'erreurs serveur (injectées depuis PHP)",
    "    // Données d'erreurs serveur (injectées depuis PHP)"
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("File updated successfully.")
