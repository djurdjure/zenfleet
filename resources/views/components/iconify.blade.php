@props([
 'icon' => '',
 'inline' => false
])

<span
 {{ $attributes->merge(['class' => $inline ? 'iconify inline-block' : 'iconify block']) }}
 data-icon="{{ $icon }}"
 data-inline="{{ $inline ? 'true' : 'false' }}"
></span>
