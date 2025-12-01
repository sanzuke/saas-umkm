<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'plan_name',
        'plan_slug',
        'price',
        'billing_cycle',
        'status',
        'user_limit',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'suspended_at',
        'limits',
        'settings',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'user_limit' => 'integer',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'suspended_at' => 'datetime',
        'limits' => 'array',
        'settings' => 'array',
    ];

    /**
     * Get the group that owns this subscription
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get all modules enabled for this subscription
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class)
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if subscription is in trial
     */
    public function isInTrial(): bool
    {
        return $this->status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->current_period_end && 
               $this->current_period_end->isPast();
    }

    /**
     * Check if subscription is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if module is enabled for this subscription
     */
    public function hasModule(string $moduleSlug): bool
    {
        return $this->modules()
            ->where('slug', $moduleSlug)
            ->wherePivot('is_enabled', true)
            ->exists();
    }

    /**
     * Enable a module
     */
    public function enableModule(Module $module): void
    {
        $this->modules()->syncWithoutDetaching([
            $module->id => ['is_enabled' => true]
        ]);
    }

    /**
     * Disable a module
     */
    public function disableModule(Module $module): void
    {
        $this->modules()->updateExistingPivot($module->id, [
            'is_enabled' => false
        ]);
    }

    /**
     * Activate subscription
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => $this->calculatePeriodEnd(),
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Suspend subscription
     */
    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);
    }

    /**
     * Calculate period end based on billing cycle
     */
    private function calculatePeriodEnd()
    {
        $start = $this->current_period_start ?? now();

        return match($this->billing_cycle) {
            'monthly' => $start->copy()->addMonth(),
            'quarterly' => $start->copy()->addMonths(3),
            'yearly' => $start->copy()->addYear(),
            default => $start->copy()->addMonth(),
        };
    }

    /**
     * Check if user limit is reached
     */
    public function hasReachedUserLimit(): bool
    {
        $userCount = $this->group->users()->count();
        return $userCount >= $this->user_limit;
    }

    /**
     * Scope to get active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get subscriptions for a group
     */
    public function scopeForGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Scope to get expiring subscriptions
     */
    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('current_period_end', '<=', now()->addDays($days))
                    ->where('current_period_end', '>', now())
                    ->where('status', 'active');
    }
}
