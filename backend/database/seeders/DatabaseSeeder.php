<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\SubscriptionPlan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedSubscriptionPlans();
    }

    /**
     * Seed permissions for all modules
     */
    private function seedPermissions()
    {
        $modules = [
            'system' => ['manage_users', 'manage_roles', 'manage_groups', 'manage_subscriptions', 'view_dashboard'],
            'inventory' => ['create_product', 'read_product', 'update_product', 'delete_product', 'manage_stock'],
            'pos' => ['create_transaction', 'read_transaction', 'void_transaction', 'manage_cashier'],
            'workshop' => ['create_order', 'read_order', 'update_order', 'complete_order', 'manage_mechanics'],
            'garment' => ['create_order', 'read_order', 'update_order', 'complete_order', 'manage_production'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['slug' => "{$module}.{$action}"],
                    [
                        'name' => ucwords(str_replace('_', ' ', $action)),
                        'module' => $module,
                        'action' => explode('_', $action)[0] ?? 'manage',
                        'description' => "Permission to {$action} in {$module} module",
                        'is_system' => $module === 'system',
                    ]
                );
            }
        }

        $this->command->info('Permissions seeded successfully!');
    }

    /**
     * Seed subscription plans
     */
    private function seedSubscriptionPlans()
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for small businesses starting out',
                'modules' => ['inventory', 'pos'],
                'features' => [
                    'Up to 5 users',
                    'Basic inventory management',
                    'POS system',
                    'Email support',
                ],
                'price' => 99000,
                'billing_cycle' => 'monthly',
                'max_users' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'For growing businesses with multiple locations',
                'modules' => ['inventory', 'pos', 'workshop'],
                'features' => [
                    'Up to 20 users',
                    'Advanced inventory management',
                    'POS system',
                    'Workshop management',
                    'Priority email support',
                    'API access',
                ],
                'price' => 299000,
                'billing_cycle' => 'monthly',
                'max_users' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large enterprises with complex needs',
                'modules' => ['inventory', 'pos', 'workshop', 'garment'],
                'features' => [
                    'Unlimited users',
                    'All modules included',
                    'Advanced reporting',
                    'Custom integrations',
                    '24/7 phone support',
                    'Dedicated account manager',
                ],
                'price' => 999000,
                'billing_cycle' => 'monthly',
                'max_users' => null,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
    }
}
