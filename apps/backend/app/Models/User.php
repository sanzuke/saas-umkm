<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the tenant that owns this user
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all groups this user belongs to
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->withPivot('roles', 'status', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get all permissions for user across all groups
     */
    public function getAllPermissions()
    {
        $permissions = collect();

        foreach ($this->groups as $group) {
            $roleIds = $group->pivot->roles ?? [];
            
            if (!empty($roleIds)) {
                $roles = Role::whereIn('id', $roleIds)->with('permissions')->get();
                
                foreach ($roles as $role) {
                    $permissions = $permissions->merge($role->permissions);
                }
            }
        }

        return $permissions->unique('id');
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->getAllPermissions()
            ->where('slug', $permissionSlug)
            ->isNotEmpty();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->getAllPermissions()
            ->whereIn('slug', $permissionSlugs)
            ->isNotEmpty();
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        $userPermissions = $this->getAllPermissions()->pluck('slug');
        
        foreach ($permissionSlugs as $slug) {
            if (!$userPermissions->contains($slug)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get user's roles in a specific group
     */
    public function getRolesInGroup(Group $group)
    {
        $pivot = $this->groups()->where('group_id', $group->id)->first()?->pivot;
        
        if (!$pivot || empty($pivot->roles)) {
            return collect();
        }

        return Role::whereIn('id', $pivot->roles)->get();
    }

    /**
     * Check if user belongs to a specific group
     */
    public function belongsToGroup(Group $group): bool
    {
        return $this->groups()->where('group_id', $group->id)->exists();
    }

    /**
     * Check if user has access to a module in any group
     */
    public function hasModuleAccess(string $moduleSlug): bool
    {
        foreach ($this->groups as $group) {
            $subscription = $group->activeSubscription();
            
            if ($subscription && $subscription->modules()->where('slug', $moduleSlug)->exists()) {
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
}
