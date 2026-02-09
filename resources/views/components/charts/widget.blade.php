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

@php
    $resolvedChartId = $chartId ?? $id;
    $resolvedAriaLabel = $ariaLabel ?: 'Graphique ZenFleet';
    $chartPayload = [
        'meta' => [
            'version' => '1.0',
            'source' => 'blade.chart-widget',
        ],
        'chart' => [
            'id' => $resolvedChartId,
            'type' => $type,
            'height' => (int) $height,
            'ariaLabel' => $resolvedAriaLabel,
        ],
        'labels' => $labels,
        'series' => $series,
        'options' => $options,
    ];
@endphp

<div
    id="{{ $id }}"
    data-zenfleet-chart
    data-chart-id="{{ $resolvedChartId }}"
    data-chart-type="{{ $type }}"
    data-chart-height="{{ $height }}"
    data-chart-aria-label="{{ $resolvedAriaLabel }}"
    data-chart-labels='@json($labels)'
    data-chart-series='@json($series)'
    data-chart-options='@json($options)'
    data-chart-payload='@json($chartPayload)'
    @if($wireIgnore) wire:ignore @endif
    {{ $attributes }}
></div>
