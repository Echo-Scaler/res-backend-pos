<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define fine-grained permissions
        $permissions = [
            // Menu, Inventory & Promotions Operations (Owner + Manager)
            'manage-menu',
            'manage-tables',
            'manage-inventory',
            'manage-promotions',

            // Employee Management
            'manage-staff',         // Can create/manage Cashier & Staff (Owner + Manager)
            'manage-managers',      // Can create/manage Managers (Owner Only)

            // Administrative & Financials (Owner Only)
            'view-financial-reports',
            'manage-restaurant-settings',
            'delete-restaurant',

            // POS Operations (Cashier & Staff)
            'pos-checkout',
            'take-orders',
            'apply-discounts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles
        $ownerRole = Role::firstOrCreate(['name' => 'OWNER', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'MANAGER', 'guard_name' => 'web']);
        $cashierRole = Role::firstOrCreate(['name' => 'CASHIER', 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'STAFF', 'guard_name' => 'web']);

        // 1. OWNER: Full control over everything
        $ownerRole->syncPermissions(Permission::all());

        // 2. MANAGER: Supervise operations - menu, tables, inventory, promotions, and staff (Cashier & Staff)
        $managerRole->syncPermissions([
            'manage-menu',
            'manage-tables',
            'manage-inventory',
            'manage-promotions',
            'manage-staff',
        ]);

        // 3. CASHIER: POS checkout, taking orders, and applying approved discounts
        $cashierRole->syncPermissions([
            'pos-checkout',
            'take-orders',
            'apply-discounts',
        ]);

        // 4. STAFF: Taking orders only
        $staffRole->syncPermissions([
            'take-orders',
        ]);
    }
}
