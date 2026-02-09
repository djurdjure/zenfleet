<?php

namespace App\Support\Analytics;

use Illuminate\Support\Facades\Auth;

class ChartPayloadFactory
{
    /**
     * Build a normalized chart payload contract consumed by x-charts.widget.
     */
    public static function make(
        string $chartId,
        string $type,
        array $labels,
        array $series,
        array $options = [],
        array $meta = [],
        int $height = 320,
        ?string $ariaLabel = null
    ): array {
        return [
            'meta' => self::normalizeMeta($meta),
            'chart' => [
                'id' => $chartId,
                'type' => $type,
                'height' => $height,
                'ariaLabel' => $ariaLabel ?: 'Graphique ZenFleet',
            ],
            'labels' => array_values($labels),
            'series' => $series,
            'options' => $options,
        ];
    }

    /**
     * Normalize metadata and inject stable analytics context fields.
     */
    public static function normalizeMeta(array $meta = []): array
    {
        $user = Auth::user();

        return array_merge([
            'version' => '1.0',
            'source' => 'analytics.backend',
            'tenant_id' => $user?->organization_id,
            'scope_role' => $user ? $user->getRoleNames()->first() : null,
            'period' => null,
            'filters' => [],
            'timezone' => $user?->timezone ?? config('app.timezone'),
            'currency' => config('algeria.currency.code', 'DZD'),
            'generated_at' => now()->toIso8601String(),
        ], $meta);
    }
}

