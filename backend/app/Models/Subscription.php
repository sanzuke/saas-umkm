<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'modules',
        'features',
        'price',
        'billing_cycle',
        'max_users',
        'is_active',
    ];

    protected $casts = [
        'modules' => 'array',
        'features' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Subscriptions using this plan
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /**
     * Check if plan includes module
     */
    public function hasModule($module): bool
    {
        $modules = $this->modules ?? [];
        return in_array($module, $modules);
    }

    /**
     * Scope to get active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'plan_id',
        'modules_enabled',
        'status',
        'trial_ends_at',
        'started_at',
        'expires_at',
        'cancelled_at',
        'billing_info',
    ];

    protected $casts = [
        'modules_enabled' => 'array',
        'billing_info' => 'array',
        'trial_ends_at' => 'datetime',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Business unit this subscription belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Subscription plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Check if subscription is on trial
     */
    public function onTrial(): bool
    {
        return $this->status === 'trial' 
            && $this->trial_ends_at 
            && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription has expired
     */
    public function hasExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if module is enabled
     */
    public function hasModule($module): bool
    {
        $modules = $this->modules_enabled ?? [];
        return in_array($module, $modules);
    }

    /**
     * Enable module
     */
    public function enableModule($module)
    {
        $modules = $this->modules_enabled ?? [];
        
        if (!in_array($module, $modules)) {
            $modules[] = $module;
            $this->modules_enabled = $modules;
            $this->save();
        }

        return $this;
    }

    /**
     * Disable module
     */
    public function disableModule($module)
    {
        $modules = $this->modules_enabled ?? [];
        $modules = array_diff($modules, [$module]);
        
        $this->modules_enabled = array_values($modules);
        $this->save();

        return $this;
    }

    /**
     * Cancel subscription
     */
    public function cancel()
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->save();

        return $this;
    }

    /**
     * Renew subscription
     */
    public function renew()
    {
        $this->status = 'active';
        $this->cancelled_at = null;
        
        // Extend expiry based on billing cycle
        $billingCycle = $this->plan->billing_cycle;
        $months = match($billingCycle) {
            'monthly' => 1,
            'quarterly' => 3,
            'yearly' => 12,
            default => 1,
        };

        $this->expires_at = now()->addMonths($months);
        $this->save();

        return $this;
    }

    /**
     * Scope to get active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope to get expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope to filter by group
     */
    public function scopeForGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }
}
