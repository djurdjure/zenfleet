@props([
'columns' => 2,
'gap' => 6,
'divided' => true,
])

@php
$gridCols = match((int)$columns) {
2 => 'md:grid-cols-2',
3 => 'md:grid-cols-2 lg:grid-cols-3',
4 => 'md:grid-cols-2 lg:grid-cols-4',
default => 'md:grid-cols-2',
};
@endphp

<div {{ $attributes->merge(['class' => "col-span-full w-full min-w-0 grid grid-cols-1 {$gridCols} gap-{$gap}"]) }}
    @if($divided && (int)$columns===2)
    style="position: relative;"
    @endif>
    @if($divided && (int)$columns === 2)
    {{-- Séparateur vertical central pour design professionnel --}}
    <div class="hidden md:block absolute left-1/2 top-4 bottom-4 w-px bg-gradient-to-b from-transparent via-slate-200 to-transparent" aria-hidden="true"></div>
    @endif

    {{ $slot }}
</div>
