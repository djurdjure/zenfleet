@props([
    'id',
    'chartId' => null,
    'type' => 'line',
    'height' => 320,
    'ariaLabel' => 'Graphique ZenFleet',
    'labels' => [],
    'series' => [],
    'options' => [],
    'wireIgnore' => false,
])

<div
    id="{{ $id }}"
    data-zenfleet-chart
    data-chart-id="{{ $chartId ?? $id }}"
    data-chart-type="{{ $type }}"
    data-chart-height="{{ $height }}"
    data-chart-aria-label="{{ $ariaLabel }}"
    data-chart-labels='@json($labels)'
    data-chart-series='@json($series)'
    data-chart-options='@json($options)'
    @if($wireIgnore) wire:ignore @endif
    {{ $attributes }}
></div>

