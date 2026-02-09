@props([
    'id',
    'payload' => null,
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
    $resolvedPayload = is_array($payload) ? $payload : [];
    $payloadMeta = is_array(data_get($resolvedPayload, 'meta')) ? data_get($resolvedPayload, 'meta') : [];
    $payloadChart = is_array(data_get($resolvedPayload, 'chart')) ? data_get($resolvedPayload, 'chart') : [];

    $resolvedChartId = data_get($payloadChart, 'id') ?? $chartId ?? $id;
    $resolvedType = data_get($payloadChart, 'type') ?? $type;
    $resolvedHeight = (int) (data_get($payloadChart, 'height') ?? $height);
    $resolvedAriaLabel = data_get($payloadChart, 'ariaLabel') ?? $ariaLabel ?: 'Graphique ZenFleet';
    $resolvedLabels = is_array(data_get($resolvedPayload, 'labels')) ? data_get($resolvedPayload, 'labels') : $labels;
    $resolvedSeries = is_array(data_get($resolvedPayload, 'series')) ? data_get($resolvedPayload, 'series') : $series;
    $resolvedOptions = is_array(data_get($resolvedPayload, 'options')) ? data_get($resolvedPayload, 'options') : $options;

    $chartPayload = [
        'meta' => [
            'version' => '1.0',
            'source' => data_get($payloadMeta, 'source', 'blade.chart-widget'),
            'tenant_id' => data_get($payloadMeta, 'tenant_id'),
            'scope_role' => data_get($payloadMeta, 'scope_role'),
            'period' => data_get($payloadMeta, 'period'),
            'filters' => data_get($payloadMeta, 'filters', []),
            'timezone' => data_get($payloadMeta, 'timezone', config('app.timezone')),
            'currency' => data_get($payloadMeta, 'currency', config('algeria.currency.code', 'DZD')),
            'generated_at' => data_get($payloadMeta, 'generated_at', now()->toIso8601String()),
        ],
        'chart' => [
            'id' => $resolvedChartId,
            'type' => $resolvedType,
            'height' => $resolvedHeight,
            'ariaLabel' => $resolvedAriaLabel,
        ],
        'labels' => $resolvedLabels,
        'series' => $resolvedSeries,
        'options' => $resolvedOptions,
    ];
@endphp

<div
    id="{{ $id }}"
    data-zenfleet-chart
    data-chart-id="{{ $resolvedChartId }}"
    data-chart-type="{{ $resolvedType }}"
    data-chart-height="{{ $resolvedHeight }}"
    data-chart-aria-label="{{ $resolvedAriaLabel }}"
    data-chart-labels='@json($resolvedLabels)'
    data-chart-series='@json($resolvedSeries)'
    data-chart-options='@json($resolvedOptions)'
    data-chart-payload='@json($chartPayload)'
    @if($wireIgnore) wire:ignore @endif
    {{ $attributes }}
></div>
