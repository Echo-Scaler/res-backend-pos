<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MenuInventoryPromotionPermissionTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    private User $manager;

    private User $cashier;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Rangoon Spice Kitchen',
            'slug' => 'rangoon-spice-kitchen',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Restaurant Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Operations Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->manager->assignRole('MANAGER');

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Counter Cashier',
            'email' => 'cashier@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Dining Waiter',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_owner_and_manager_can_access_menu_and_inventory(): void
    {
        // Owner checks
        $this->actingAs($this->owner)->get(route('admin.menu.index'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('admin.inventory.index'))->assertStatus(200);

        // Manager checks (Permissions correctly granted)
        $this->actingAs($this->manager)->get(route('admin.menu.index'))->assertStatus(200);
        $this->actingAs($this->manager)->get(route('admin.inventory.index'))->assertStatus(200);
    }

    public function test_cashier_and_staff_cannot_access_menu_or_inventory(): void
    {
        // Cashier forbidden
        $this->actingAs($this->cashier)->get(route('admin.menu.index'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('admin.inventory.index'))->assertStatus(403);

        // Staff forbidden
        $this->actingAs($this->staff)->get(route('admin.menu.index'))->assertStatus(403);
        $this->actingAs($this->staff)->get(route('admin.inventory.index'))->assertStatus(403);
    }

    public function test_can_create_category_and_product(): void
    {
        // Manager creates category
        $catResponse = $this->actingAs($this->manager)->postJson(route('admin.menu.categories.store'), [
            'name' => 'Traditional Curries',
            'description' => 'Authentic Burmese homestyle curries',
        ]);

        $catResponse->assertStatus(200);
        $this->assertDatabaseHas('categories', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Traditional Curries',
        ]);
        $categoryId = $catResponse->json('category.id');

        // Owner creates product in that category
        $prodResponse = $this->actingAs($this->owner)->postJson(route('admin.menu.products.store'), [
            'category_id' => $categoryId,
            'name' => 'Danbauk Special Set',
            'code' => 'DISH-101',
            'price' => 12500,
            'cost_price' => 6000,
            'preparation_time' => 20,
        ]);

        $prodResponse->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Danbauk Special Set',
            'price' => 12500,
            'is_available' => true,
        ]);
    }

    public function test_can_toggle_product_availability(): void
    {
        $product = Product::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Coconut Rice Set',
            'price' => 8500,
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->manager)->postJson(route('admin.menu.products.toggle', $product->id));

        $response->assertStatus(200);
        $response->assertJson(['is_available' => false]);
        $this->assertFalse($product->fresh()->is_available);
    }

    public function test_can_create_and_manage_inventory_item(): void
    {
        $response = $this->actingAs($this->manager)->postJson(route('admin.inventory.store'), [
            'name' => 'Premium Jasmine Rice',
            'sku' => 'RAW-RICE-01',
            'unit' => 'kg',
            'current_stock' => 50,
            'min_stock_alert' => 10,
            'unit_cost' => 4500,
            'supplier_name' => 'Shwe Myanmar Wholesale',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('inventory_items', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Premium Jasmine Rice',
            'current_stock' => 50,
        ]);
    }
}
