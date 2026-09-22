<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles & Permissions
        $this->call(RoleSeeder::class);

        // 2. Seed Default Restaurant
        $restaurant = Restaurant::firstOrCreate(
            ['slug' => 'rangoon-spice-kitchen'],
            [
                'name' => 'Rangoon Spice Kitchen',
                'phone' => '+959123456789',
                'email' => 'contact@rangoonspice.com',
                'address' => 'No. 123, Merchant Road, Yangon',
                'is_active' => true,
            ]
        );

        // 3. Seed Users for each position
        $users = [
            [
                'email' => 'admin@example.com',
                'name' => 'Restaurant Owner',
                'role' => 'OWNER',
            ],
            [
                'email' => 'manager@example.com',
                'name' => 'Operations Manager',
                'role' => 'MANAGER',
            ],
            [
                'email' => 'cashier@example.com',
                'name' => 'Counter Cashier',
                'role' => 'CASHIER',
            ],
            [
                'email' => 'staff@example.com',
                'name' => 'Dining Staff',
                'role' => 'STAFF',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'restaurant_id' => $restaurant->id,
                    'name' => $userData['name'],
                    'password' => Hash::make('password123'),
                ]
            );

            if (! $user->hasRole($userData['role'])) {
                $user->syncRoles([$userData['role']]);
            }
        }
    }
}
