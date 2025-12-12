#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script to fix Alpine.js syntax errors in driver forms
Ensures modifications are properly written to disk
"""

import re

def fix_file(filepath):
    """Fix syntax errors in the specified file"""
    print(f"Processing {filepath}...")
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Fix 1: currentStep initialization
    # Replace the malformed nested braces with correct syntax
    pattern1 = r"currentStep: \{\s*\{\s*old\('current_step', 1\)\s*\}\s*\},"
    replacement1 = "currentStep: {{ old('current_step', 1) }},"
    content = re.sub(pattern1, replacement1, content, flags=re.MULTILINE | re.DOTALL)
    
    # Fix 2: @if directive spacing
    content = content.replace("@if($errors - > any())", "@if($errors->any())")
    content = content.replace("@json($errors - > messages())", "@json($errors->messages())")
    
    # Write back to file
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✅ Fixed {filepath}")

if __name__ == "__main__":
    fix_file("/home/lynx/projects/zenfleet/resources/views/admin/drivers/edit.blade.php")
    fix_file("/home/lynx/projects/zenfleet/resources/views/admin/drivers/create.blade.php")
    print("\n✅ All files fixed successfully!")
