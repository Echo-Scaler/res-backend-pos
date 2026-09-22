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
            // Menu & Table Operations (Owner + Manager)
            'manage-menu',
            'manage-tables',

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

        // 2. MANAGER: Restricted - Can manage menu, tables, and staff (Cashier & Staff),
        // but CANNOT manage managers, cannot manage sensitive settings, cannot delete restaurant.
        $managerRole->syncPermissions([
            'manage-menu',
            'manage-tables',
            'manage-staff',
        ]);

        // 3. CASHIER: POS checkout and taking orders
        $cashierRole->syncPermissions([
            'pos-checkout',
            'take-orders',
        ]);

        // 4. STAFF: Taking orders only
        $staffRole->syncPermissions([
            'take-orders',
        ]);
    }
}
