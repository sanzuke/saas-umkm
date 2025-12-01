<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'module',
        'action',
        'description',
    ];

    /**
     * Get all roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Scope to filter by module
     */
    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by action
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by module and action
     */
    public function scopeForModuleAndAction($query, string $module, string $action)
    {
        return $query->where('module', $module)->where('action', $action);
    }

    /**
     * Get permission by slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Create permission with auto-generated slug
     */
    public static function createWithSlug(string $module, string $action, string $name, ?string $description = null): self
    {
        return static::create([
            'name' => $name,
            'slug' => "{$module}.{$action}",
            'module' => $module,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
