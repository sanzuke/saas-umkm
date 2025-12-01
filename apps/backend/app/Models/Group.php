<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'code',
        'type',
        'level',
        'description',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'level' => 'integer',
    ];

    protected $appends = ['full_path'];

    /**
     * Get the tenant that owns this group
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the parent group
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    /**
     * Get child groups
     */
    public function children(): HasMany
    {
        return $this->hasMany(Group::class, 'parent_id');
    }

    /**
     * Get all descendants recursively
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all users in this group
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('roles', 'status', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get all roles defined for this group
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get subscription for this group
     */
    public function subscription(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get active subscription
     */
    public function activeSubscription()
    {
        return $this->hasMany(Subscription::class)
            ->where('status', 'active')
            ->latest()
            ->first();
    }

    /**
     * Check if group is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if this is a corporate level group
     */
    public function isCorporate(): bool
    {
        return $this->type === 'corporate' && $this->level === 0;
    }

    /**
     * Check if this is a company level group
     */
    public function isCompany(): bool
    {
        return $this->type === 'company' && $this->level === 1;
    }

    /**
     * Check if this is a business unit
     */
    public function isBusinessUnit(): bool
    {
        return $this->type === 'business_unit' && $this->level === 2;
    }

    /**
     * Get ancestors (parent, grandparent, etc.)
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get full hierarchical path
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Scope to filter by tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get only root groups (corporate level)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->where('level', 0);
    }
}
