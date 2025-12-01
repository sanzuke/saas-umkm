<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'level',
        'parent_id',
        'tenant_id',
        'description',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * Boot method for auto-setting tenant_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            // If this is a corporate (level 0), it becomes its own tenant
            if ($group->level === 0) {
                $group->tenant_id = null; // Will be set after creation
            } else if ($group->parent_id) {
                // Inherit tenant from parent
                $parent = self::find($group->parent_id);
                $group->tenant_id = $parent->tenant_id ?? $parent->id;
            }
        });

        static::created(function ($group) {
            // Set tenant_id to self for corporate level
            if ($group->level === 0 && !$group->tenant_id) {
                $group->tenant_id = $group->id;
                $group->saveQuietly();
            }
        });
    }

    /**
     * Parent group relationship
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    /**
     * Children groups relationship
     */
    public function children(): HasMany
    {
        return $this->hasMany(Group::class, 'parent_id');
    }

    /**
     * Tenant (root corporate) relationship
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'tenant_id');
    }

    /**
     * Users in this group
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withPivot('role_ids', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Roles defined in this group
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Subscription for this group (Business Unit level)
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
        return $this->subscription()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Get all ancestors (parent chain)
     */
    public function ancestors()
    {
        $ancestors = collect([]);
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get all descendants (recursive children)
     */
    public function descendants()
    {
        $descendants = collect([]);

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }

    /**
     * Check if group is corporate level
     */
    public function isCorporate(): bool
    {
        return $this->level === 0 && $this->type === 'corporate';
    }

    /**
     * Check if group is company level
     */
    public function isCompany(): bool
    {
        return $this->level === 1 && $this->type === 'company';
    }

    /**
     * Check if group is business unit level
     */
    public function isBusinessUnit(): bool
    {
        return $this->level === 2 && $this->type === 'business_unit';
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
     * Scope to get only active groups
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
