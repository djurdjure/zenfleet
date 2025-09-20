<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlgeriaCommune extends Model
{
    protected $table = 'algeria_communes';

    protected $fillable = [
        'wilaya_code',
        'name_ar',
        'name_fr',
        'postal_code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(AlgeriaWilaya::class, 'wilaya_code', 'code');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name_fr ?? $this->name_ar ?? "Commune #{$this->id}";
    }

    public function getFullNameAttribute(): string
    {
        return $this->display_name . ' (' . $this->wilaya->display_name . ')';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByWilaya($query, string $wilayaCode)
    {
        return $query->where('wilaya_code', $wilayaCode);
    }

    public static function getSelectOptionsByWilaya(string $wilayaCode): array
    {
        return static::active()
            ->byWilaya($wilayaCode)
            ->orderBy('name_fr')
            ->pluck('name_fr', 'id')
            ->toArray();
    }
}