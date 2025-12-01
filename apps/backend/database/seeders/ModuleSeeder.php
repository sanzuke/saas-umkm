<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Point of Sale',
                'slug' => 'pos',
                'description' => 'Complete POS system for retail management',
                'icon' => 'shopping-cart',
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    'Sales transactions',
                    'Product catalog',
                    'Customer management',
                    'Payment processing',
                    'Receipt printing',
                    'Sales reports',
                ],
            ],
            [
                'name' => 'Inventory Management',
                'slug' => 'inventory',
                'description' => 'Track and manage inventory levels, stock movements',
                'icon' => 'package',
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    'Stock management',
                    'Purchase orders',
                    'Stock adjustments',
                    'Low stock alerts',
                    'Inventory reports',
                    'Barcode scanning',
                ],
            ],
            [
                'name' => 'Workshop Management',
                'slug' => 'workshop',
                'description' => 'Manage workshop operations, service orders, and repairs',
                'icon' => 'wrench',
                'is_active' => true,
                'sort_order' => 3,
                'features' => [
                    'Service orders',
                    'Job scheduling',
                    'Mechanic assignment',
                    'Spare parts tracking',
                    'Service history',
                    'Customer vehicles',
                ],
            ],
            [
                'name' => 'Garment/Konveksi',
                'slug' => 'garment',
                'description' => 'Manage garment production, orders, and delivery',
                'icon' => 'shirt',
                'is_active' => true,
                'sort_order' => 4,
                'features' => [
                    'Production orders',
                    'Material management',
                    'Production tracking',
                    'Quality control',
                    'Delivery scheduling',
                    'Customer orders',
                ],
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }

        $this->command->info('Modules seeded successfully!');
    }
}
