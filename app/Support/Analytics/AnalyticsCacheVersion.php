<?php

namespace App\Support\Analytics;

use Illuminate\Support\Facades\Cache;

class AnalyticsCacheVersion
{
    public static function current(string $module, int|string|null $organizationId): int
    {
        $key = self::key($module, $organizationId);

        return (int) Cache::get($key, 1);
    }

    public static function bump(string $module, int|string|null $organizationId): int
    {
        $key = self::key($module, $organizationId);

        if (!Cache::has($key)) {
            Cache::forever($key, 1);
        }

        return (int) Cache::increment($key);
    }

    protected static function key(string $module, int|string|null $organizationId): string
    {
        return sprintf('analytics:version:%s:org:%s', $module, $organizationId ?? 'global');
    }
}

