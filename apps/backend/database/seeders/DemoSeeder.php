<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create tenant
        $tenant = Tenant::create([
            'name' => 'Demo Corporation',
            'slug' => 'demo-corp',
            'email' => 'demo@example.com',
            'phone' => '081234567890',
            'address' => 'Jakarta, Indonesia',
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30),
            'activated_at' => now(),
        ]);

        $this->command->info("✓ Tenant created: {$tenant->name}");

        // Create Corporate (Level 0)
        $corporate = Group::create([
            'tenant_id' => $tenant->id,
            'parent_id' => null,
            'name' => 'Demo Corporation',
            'code' => 'CORP',
            'type' => 'corporate',
            'level' => 0,
            'description' => 'Corporate headquarters',
            'status' => 'active',
        ]);

        $this->command->info("✓ Corporate created: {$corporate->name}");

        // Create Company (Level 1)
        $company = Group::create([
            'tenant_id' => $tenant->id,
            'parent_id' => $corporate->id,
            'name' => 'Demo Retail Division',
            'code' => 'RET-001',
            'type' => 'company',
            'level' => 1,
            'description' => 'Retail division company',
            'status' => 'active',
        ]);

        $this->command->info("✓ Company created: {$company->name}");

        // Create Business Unit (Level 2)
        $businessUnit = Group::create([
            'tenant_id' => $tenant->id,
            'parent_id' => $company->id,
            'name' => 'Jakarta Store',
            'code' => 'JKT-001',
            'type' => 'business_unit',
            'level' => 2,
            'description' => 'Jakarta retail store',
            'status' => 'active',
        ]);

        $this->command->info("✓ Business Unit created: {$businessUnit->name}");

        // Create subscription for Business Unit
        $subscription = Subscription::create([
            'group_id' => $businessUnit->id,
            'plan_name' => 'Professional',
            'plan_slug' => 'professional',
            'price' => 500000,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'user_limit' => 10,
            'trial_ends_at' => now()->addDays(30),
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $this->command->info("✓ Subscription created: {$subscription->plan_name}");

        // Enable all modules for this subscription
        $modules = Module::all();
        foreach ($modules as $module) {
            $subscription->modules()->attach($module->id, ['is_enabled' => true]);
        }

        $this->command->info("✓ Modules enabled: " . $modules->count() . " modules");

        // Create Roles
        // Corporate Admin Role (inheritable)
        $corporateAdminRole = Role::create([
            'group_id' => $corporate->id,
            'name' => 'Corporate Admin',
            'slug' => 'corporate-admin',
            'description' => 'Full access to all corporate resources',
            'is_inheritable' => true,
            'is_system' => true,
            'priority' => 100,
        ]);

        // Attach all permissions to corporate admin
        $corporateAdminRole->permissions()->attach(Permission::all());

        $this->command->info("✓ Role created: {$corporateAdminRole->name}");

        // Business Unit Manager Role
        $managerRole = Role::create([
            'group_id' => $businessUnit->id,
            'name' => 'Store Manager',
            'slug' => 'store-manager',
            'description' => 'Manage store operations',
            'is_inheritable' => false,
            'is_system' => false,
            'priority' => 50,
        ]);

        // Attach manager permissions
        $managerPermissions = Permission::whereIn('slug', [
            'pos.view', 'pos.create', 'pos.update', 'pos.transaction.process',
            'pos.reports.view', 'pos.discount.manage',
            'inventory.view', 'inventory.create', 'inventory.update',
            'inventory.stock.adjust',
            'system.users.manage',
        ])->get();

        $managerRole->permissions()->attach($managerPermissions);

        $this->command->info("✓ Role created: {$managerRole->name}");

        // Business Unit Staff Role
        $staffRole = Role::create([
            'group_id' => $businessUnit->id,
            'name' => 'Store Staff',
            'slug' => 'store-staff',
            'description' => 'Basic store operations',
            'is_inheritable' => false,
            'is_system' => false,
            'priority' => 10,
        ]);

        // Attach staff permissions
        $staffPermissions = Permission::whereIn('slug', [
            'pos.view', 'pos.create', 'pos.transaction.process',
            'inventory.view',
        ])->get();

        $staffRole->permissions()->attach($staffPermissions);

        $this->command->info("✓ Role created: {$staffRole->name}");

        // Create Users
        // User 1: Admin
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'status' => 'active',
        ]);

        // Attach admin to corporate with corporate admin role
        $admin->groups()->attach($corporate->id, [
            'roles' => json_encode([$corporateAdminRole->id]),
            'status' => 'active',
            'joined_at' => now(),
        ]);

        // Also attach to business unit with manager role
        $admin->groups()->attach($businessUnit->id, [
            'roles' => json_encode([$managerRole->id]),
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $this->command->info("✓ User created: {$admin->name} ({$admin->email})");

        // User 2: Staff
        $staff = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Staff User',
            'email' => 'staff@demo.com',
            'password' => Hash::make('password'),
            'phone' => '081234567892',
            'status' => 'active',
        ]);

        // Attach staff to business unit with staff role
        $staff->groups()->attach($businessUnit->id, [
            'roles' => json_encode([$staffRole->id]),
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $this->command->info("✓ User created: {$staff->name} ({$staff->email})");

        $this->command->newLine();
        $this->command->info('===========================================');
        $this->command->info('Demo data seeded successfully!');
        $this->command->info('===========================================');
        $this->command->newLine();
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@demo.com / password');
        $this->command->info('Staff: staff@demo.com / password');
        $this->command->newLine();
        $this->command->info('Organization structure:');
        $this->command->info("└── {$corporate->name} (Corporate)");
        $this->command->info("    └── {$company->name} (Company)");
        $this->command->info("        └── {$businessUnit->name} (Business Unit)");
        $this->command->newLine();
    }
}
