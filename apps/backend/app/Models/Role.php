<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'slug',
        'description',
        'is_inheritable',
        'is_system',
        'priority',
    ];

    protected $casts = [
        'is_inheritable' => 'boolean',
        'is_system' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the group that owns this role
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get all permissions for this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Check if role can be inherited by child groups
     */
    public function canBeInherited(): bool
    {
        return $this->is_inheritable;
    }

    /**
     * Check if this is a system role
     */
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /**
     * Sync permissions to this role
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Add permission to this role
     */
    public function givePermission(Permission $permission): void
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Remove permission from this role
     */
    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission);
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()
            ->where('slug', $permissionSlug)
            ->exists();
    }

    /**
     * Scope to filter by group
     */
    public function scopeForGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Scope to get inheritable roles
     */
    public function scopeInheritable($query)
    {
        return $query->where('is_inheritable', true);
    }

    /**
     * Scope to get system roles
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }
}
