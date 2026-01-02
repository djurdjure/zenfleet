@props([
'icon' => '',
'inline' => false
])

{{--
    🎯 ZENFLEET ICONIFY COMPONENT
    Uses Iconify 3.x runtime script (loaded in catalyst.blade.php)
    Icons are fetched from Iconify API and cached in browser localStorage
--}}
<span
    {{ $attributes->merge(['class' => $inline ? 'iconify inline-block' : 'iconify block']) }}
    data-icon="{{ $icon }}"
    data-inline="{{ $inline ? 'true' : 'false' }}"></span>