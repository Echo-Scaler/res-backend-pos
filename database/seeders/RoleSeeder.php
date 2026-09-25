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

            // Expense & Financial Control
            'view-expenses',
            'create-expenses',
            'edit-expenses',
            'approve-expenses',
            'pay-expenses',
            'void-expenses',
            'manage-expense-categories',
            'manage-vendors',
            'manage-budgets',
            'view-expense-reports',
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

        // 2. MANAGER: Supervise operations, inventory, promotions, staff, and expenses
        $managerRole->syncPermissions([
            'manage-menu',
            'manage-tables',
            'manage-inventory',
            'manage-promotions',
            'manage-staff',
            'view-expenses',
            'create-expenses',
            'edit-expenses',
            'approve-expenses',
            'pay-expenses',
            'void-expenses',
            'manage-expense-categories',
            'manage-vendors',
            'manage-budgets',
            'view-expense-reports',
        ]);

        // 3. CASHIER: POS checkout, orders, discounts, and petty cash expense logging
        $cashierRole->syncPermissions([
            'pos-checkout',
            'take-orders',
            'apply-discounts',
            'view-expenses',
            'create-expenses',
        ]);

        // 4. STAFF: Taking orders and submission of staff expenses
        $staffRole->syncPermissions([
            'take-orders',
            'view-expenses',
            'create-expenses',
        ]);
    }
}
