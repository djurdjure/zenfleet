<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * RepairCategory Model - Enterprise-Grade Categorization for Repair Requests
 *
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property string|null $icon
 * @property string|null $color
 * @property int $sort_order
 * @property bool $is_active
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Organization $organization
 * @property-read \Illuminate\Database\Eloquent\Collection<RepairRequest> $repairRequests
 */
class RepairCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'repair_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'slug',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug on creating
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });

        // Set default sort_order
        static::creating(function ($category) {
            if (is_null($category->sort_order)) {
                $maxOrder = static::where('organization_id', $category->organization_id)
                    ->max('sort_order');
                $category->sort_order = ($maxOrder ?? 0) + 10;
            }
        });
    }

    /**
     * Scope active categories
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by organization
     */
    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope ordered by sort_order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the organization that owns the category.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the repair requests for the category.
     */
    public function repairRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class, 'category_id');
    }

    /**
     * Get count of active repair requests
     */
    public function getActiveRequestsCountAttribute(): int
    {
        return $this->repairRequests()
            ->whereNotIn('status', [
                RepairRequest::STATUS_REJECTED_SUPERVISOR,
                RepairRequest::STATUS_REJECTED_FINAL,
            ])
            ->count();
    }

    /**
     * Get formatted color for display
     */
    public function getColorClassAttribute(): string
    {
        $colorMap = [
            'red' => 'text-red-600 bg-red-100',
            'orange' => 'text-orange-600 bg-orange-100',
            'yellow' => 'text-yellow-600 bg-yellow-100',
            'green' => 'text-green-600 bg-green-100',
            'blue' => 'text-blue-600 bg-blue-100',
            'indigo' => 'text-indigo-600 bg-indigo-100',
            'purple' => 'text-purple-600 bg-purple-100',
            'pink' => 'text-pink-600 bg-pink-100',
            'gray' => 'text-gray-600 bg-gray-100',
        ];

        return $colorMap[$this->color] ?? 'text-gray-600 bg-gray-100';
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(): bool
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }
}
