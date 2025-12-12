#!/usr/bin/env python3
# Fix sidebar overlap by adding lg:pl-64 to main content area

import re

filepath = "/home/lynx/projects/zenfleet/resources/views/layouts/admin/catalyst.blade.php"

with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix: Add lg:pl-64 to main element
content = re.sub(
    r'<main class="py-10">',
    '<main class="lg:pl-64 py-10">',
    content
)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)

print("✅ Fixed sidebar overlap - added lg:pl-64 to main element")
