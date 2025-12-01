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
        'name',
        'slug',
        'group_id',
        'description',
        'is_inheritable',
        'is_system',
    ];

    protected $casts = [
        'is_inheritable' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Group this role belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Permissions assigned to this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->withTimestamps();
    }

    /**
     * Assign permission to role
     */
    public function givePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        return $this->permissions()->syncWithoutDetaching($permission);
    }

    /**
     * Remove permission from role
     */
    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        return $this->permissions()->detach($permission);
    }

    /**
     * Check if role has permission
     */
    public function hasPermission($permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Sync permissions for this role
     */
    public function syncPermissions(array $permissions)
    {
        $permissionIds = Permission::whereIn('slug', $permissions)->pluck('id');
        return $this->permissions()->sync($permissionIds);
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
