<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->string('plan_name'); // e.g., "Starter", "Professional", "Enterprise"
            $table->string('plan_slug'); // e.g., "starter", "professional", "enterprise"
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->enum('status', ['trial', 'active', 'cancelled', 'expired', 'suspended'])->default('trial');
            $table->integer('user_limit')->default(5); // Max users for this subscription
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->json('limits')->nullable(); // Additional limits: storage, transactions, etc.
            $table->json('settings')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('group_id');
            $table->index('status');
            $table->index('current_period_end');

            // Ensure one active subscription per group
            $table->unique(['group_id', 'status'], 'unique_active_subscription');
        });

        // Module-Subscription pivot (which modules are enabled for a subscription)
        Schema::create('module_subscription', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            // Unique constraint
            $table->unique(['module_id', 'subscription_id']);

            // Indexes
            $table->index('module_id');
            $table->index('subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_subscription');
        Schema::dropIfExists('subscriptions');
    }
};
