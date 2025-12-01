<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = ['pos', 'inventory', 'workshop', 'garment'];
        $actions = [
            'view' => 'View',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'export' => 'Export',
        ];

        // Create CRUD permissions for each module
        foreach ($modules as $module) {
            foreach ($actions as $action => $actionName) {
                Permission::create([
                    'name' => "{$actionName} " . ucfirst($module),
                    'slug' => "{$module}.{$action}",
                    'module' => $module,
                    'action' => $action,
                    'description' => "{$actionName} access for {$module} module",
                ]);
            }
        }

        // Additional specific permissions
        $specificPermissions = [
            // POS specific
            [
                'name' => 'Process POS Transaction',
                'slug' => 'pos.transaction.process',
                'module' => 'pos',
                'action' => 'process',
                'description' => 'Process sales transactions in POS',
            ],
            [
                'name' => 'View POS Reports',
                'slug' => 'pos.reports.view',
                'module' => 'pos',
                'action' => 'view',
                'description' => 'View sales reports and analytics',
            ],
            [
                'name' => 'Manage POS Discounts',
                'slug' => 'pos.discount.manage',
                'module' => 'pos',
                'action' => 'manage',
                'description' => 'Create and apply discounts',
            ],

            // Inventory specific
            [
                'name' => 'Adjust Stock',
                'slug' => 'inventory.stock.adjust',
                'module' => 'inventory',
                'action' => 'adjust',
                'description' => 'Adjust inventory stock levels',
            ],
            [
                'name' => 'Transfer Stock',
                'slug' => 'inventory.stock.transfer',
                'module' => 'inventory',
                'action' => 'transfer',
                'description' => 'Transfer stock between locations',
            ],

            // Workshop specific
            [
                'name' => 'Create Service Order',
                'slug' => 'workshop.service.create',
                'module' => 'workshop',
                'action' => 'create',
                'description' => 'Create new service orders',
            ],
            [
                'name' => 'Assign Mechanic',
                'slug' => 'workshop.mechanic.assign',
                'module' => 'workshop',
                'action' => 'assign',
                'description' => 'Assign mechanics to service orders',
            ],

            // Garment specific
            [
                'name' => 'Create Production Order',
                'slug' => 'garment.production.create',
                'module' => 'garment',
                'action' => 'create',
                'description' => 'Create production orders',
            ],
            [
                'name' => 'Track Production',
                'slug' => 'garment.production.track',
                'module' => 'garment',
                'action' => 'track',
                'description' => 'Track production progress',
            ],

            // System permissions
            [
                'name' => 'Manage Users',
                'slug' => 'system.users.manage',
                'module' => 'system',
                'action' => 'manage',
                'description' => 'Manage system users',
            ],
            [
                'name' => 'Manage Roles',
                'slug' => 'system.roles.manage',
                'module' => 'system',
                'action' => 'manage',
                'description' => 'Manage roles and permissions',
            ],
            [
                'name' => 'Manage Groups',
                'slug' => 'system.groups.manage',
                'module' => 'system',
                'action' => 'manage',
                'description' => 'Manage organizational groups',
            ],
            [
                'name' => 'View Settings',
                'slug' => 'system.settings.view',
                'module' => 'system',
                'action' => 'view',
                'description' => 'View system settings',
            ],
            [
                'name' => 'Update Settings',
                'slug' => 'system.settings.update',
                'module' => 'system',
                'action' => 'update',
                'description' => 'Update system settings',
            ],
        ];

        foreach ($specificPermissions as $permission) {
            Permission::create($permission);
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
