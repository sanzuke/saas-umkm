<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'tenant_id',
        'is_active',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_super_admin' => 'boolean',
    ];

    /**
     * Tenant (root corporate) relationship
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'tenant_id');
    }

    /**
     * Groups this user belongs to
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->withPivot('role_ids', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Get primary group for user
     */
    public function primaryGroup()
    {
        return $this->groups()->wherePivot('is_primary', true)->first();
    }

    /**
     * Get all roles across all groups
     */
    public function getAllRoles()
    {
        $roleIds = [];

        foreach ($this->groups as $group) {
            $groupRoleIds = json_decode($group->pivot->role_ids, true) ?? [];
            $roleIds = array_merge($roleIds, $groupRoleIds);
        }

        return Role::whereIn('id', array_unique($roleIds))->get();
    }

    /**
     * Get roles for specific group
     */
    public function getRolesForGroup($groupId)
    {
        $group = $this->groups()->where('group_id', $groupId)->first();

        if (!$group) {
            return collect([]);
        }

        $roleIds = json_decode($group->pivot->role_ids, true) ?? [];

        return Role::whereIn('id', $roleIds)->get();
    }

    /**
     * Get all permissions across all groups
     */
    public function getAllPermissions()
    {
        $permissions = collect([]);

        foreach ($this->getAllRoles() as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions->unique('id');
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($permissionSlug, $groupId = null): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        $roles = $groupId 
            ? $this->getRolesForGroup($groupId)
            : $this->getAllRoles();

        foreach ($roles as $role) {
            if ($role->permissions()->where('slug', $permissionSlug)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions, $groupId = null): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission, $groupId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all given permissions
     */
    public function hasAllPermissions(array $permissions, $groupId = null): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission, $groupId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has role
     */
    public function hasRole($roleSlug, $groupId = null): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        $roles = $groupId 
            ? $this->getRolesForGroup($groupId)
            : $this->getAllRoles();

        return $roles->contains('slug', $roleSlug);
    }

    /**
     * Check if user belongs to group
     */
    public function belongsToGroup($groupId): bool
    {
        return $this->groups()->where('group_id', $groupId)->exists();
    }

    /**
     * Check if user can access group (belongs to group or its ancestors)
     */
    public function canAccessGroup($groupId): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        $group = Group::find($groupId);
        if (!$group) {
            return false;
        }

        // Check if user belongs to this group
        if ($this->belongsToGroup($groupId)) {
            return true;
        }

        // Check if user belongs to any ancestor groups (corporate/company can see children)
        foreach ($group->ancestors() as $ancestor) {
            if ($this->belongsToGroup($ancestor->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Scope to filter by tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
