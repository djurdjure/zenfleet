#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import re

# Fix 1: Retirer lg:pl-64 du layout catalyst.blade.php
catalyst_path = "/home/lynx/projects/zenfleet/resources/views/layouts/admin/catalyst.blade.php"
with open(catalyst_path, 'r', encoding='utf-8') as f:
    catalyst_content = f.read()

# Retirer lg:pl-64
catalyst_content = catalyst_content.replace('<main class="lg:pl-64 py-10">', '<main class="py-10">')

with open(catalyst_path, 'w', encoding='utf-8') as f:
    f.write(catalyst_content)

print("✅ Removed lg:pl-64 from catalyst.blade.php")

# Fix 2: Corriger edit.blade.php structure
edit_path = "/home/lynx/projects/zenfleet/resources/views/admin/drivers/edit.blade.php"
with open(edit_path, 'r', encoding='utf-8') as f:
    edit_content = f.read()

# Insérer @endsection après </section> et avant @push
edit_content = re.sub(
    r'(</section>\s*)\n(\s*@push\(\'scripts\'\))',
    r'\1\n@endsection\n\n\2',
    edit_content
)

# Remplacer le dernier @endsection (après @push) par @endpush
# Trouve @push('scripts') ... @endsection à la fin du fichier
edit_content = re.sub(
    r"(@push\('scripts'\).*?)@endsection(\s*)$",
    r"\1@endpush\2",
    edit_content,
    flags=re.DOTALL
)

with open(edit_path, 'w', encoding='utf-8') as f:
    f.write(edit_content)

print("✅ Fixed edit.blade.php Blade directive structure")
print("  - Added @endsection after </section>")
print("  - Changed final @endsection to @endpush")
