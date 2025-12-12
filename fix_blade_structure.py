#!/usr/bin/env python3
# Fix Blade directive structure in edit.blade.php

filepath = "/home/lynx/projects/zenfleet/resources/views/admin/drivers/edit.blade.php"

with open(filepath, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Insert @endsection after line 553 (which is </section>)
# Line 553 in the file (0-indexed is 552)
lines.insert(554, '@endsection\n\n')  # Insert after line 553 (0-indexed 553)

# Now line 735 becomes 737 (shifted by 2 lines)
# Replace @endsection with @endpush at what is now line 737
# Original line 735 is now at index 736 (0-indexed)
if lines[736].strip() == '@endsection':
    lines[736] = '@endpush\n'

with open(filepath, 'w', encoding='utf-8') as f:
    f.writelines(lines)

print("✅ Fixed Blade directive structure:")
print("  - Added @endsection after </section> to close @section('content')")
print("  - Changed final @endsection to @endpush to close @push('scripts')")
