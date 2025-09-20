<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlgeriaWilaya extends Model
{
    protected $table = 'algeria_wilayas';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code',
        'name_ar',
        'name_fr',
        'name_en',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function communes(): HasMany
    {
        return $this->hasMany(AlgeriaCommune::class, 'wilaya_code', 'code');
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'wilaya', 'code');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name_fr ?? $this->name_ar ?? $this->code;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getSelectOptions(): array
    {
        return static::active()
            ->orderBy('name_fr')
            ->pluck('name_fr', 'code')
            ->toArray();
    }
}