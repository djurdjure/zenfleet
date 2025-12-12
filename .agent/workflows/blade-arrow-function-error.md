---
description: Fixing PHP ParseError from arrow functions in Blade x-data attributes
---

# PHP ParseError: Arrow Functions in Blade x-data Attributes

## Error
```
ParseError: syntax error, unexpected token ">"
```

## Root Cause
JavaScript **arrow functions (`=>`)** inside Blade `x-data` attributes are parsed by PHP as PHP syntax, not JavaScript.

### Problematic Pattern
```blade
<div x-data="{
    items: [],
    process() {
        this.items.forEach(item => item.doSomething());  // ❌ FAILS
    }
}">
```

## Solutions

### 1. Use Native HTML (Recommended)
Replace Alpine.js with standard HTML form elements:
```blade
@foreach($options as $value => $label)
    <input type="checkbox" name="field[]" value="{{ $value }}">
@endforeach
```

### 2. Move JavaScript to @push('scripts')
```blade
@push('scripts')
<script>
    Alpine.data('myComponent', () => ({
        items: [],
        process() {
            this.items.forEach(item => item.doSomething());  // ✅ WORKS
        }
    }));
</script>
@endpush
```

### 3. Use Regular Functions (Avoid Arrow Functions in Alpine)
```blade
<!-- ❌ FAILS: Arrow function in attribute -->
<div x-init="setTimeout(() => show = false, 1000)">

<!-- ✅ WORKS: Standard function -->
<div x-init="setTimeout(function() { show = false; }, 1000)">
```

## Key Rule
> **Never use arrow functions (`=>`) directly inside Blade attributes.**
