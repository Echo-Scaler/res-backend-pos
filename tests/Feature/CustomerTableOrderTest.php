<?php

namespace Tests\Feature;

use App\Models\DiningTable;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTableOrderTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    protected DiningTable $table;

    protected Product $product1;

    protected Product $product2;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Saizeriya Test Branch',
            'slug' => 'saizeriya-test-branch',
            'is_active' => true,
        ]);

        $this->table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-07',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
            'status' => 'VACANT',
            'qr_token' => 'test-secure-qr-token-32-chars-ok',
            'is_active' => true,
        ]);

        $this->product1 = Product::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Italian Hamburg Steak',
            'code' => 'DISH-01',
            'price' => 6500,
            'cost_price' => 3000,
            'is_available' => true,
        ]);

        $this->product2 = Product::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Matcha Ice Cream',
            'code' => 'DISH-02',
            'price' => 2000,
            'cost_price' => 800,
            'is_available' => true,
        ]);

        $this->staff = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_customer_can_view_table_ordering_menu_by_qr_token(): void
    {
        $response = $this->get(route('customer.order.table', $this->table->qr_token));

        $response->assertOk();
        $response->assertSee('Table T-07');
        $response->assertSee('Italian Hamburg Steak');
        $response->assertSee('6,500 MMK');
    }

    public function test_invalid_or_inactive_qr_token_returns_404(): void
    {
        $response = $this->get('/order/table/non-existent-token-xyz');
        $response->assertNotFound();

        $this->table->update(['is_active' => false]);
        $responseInactive = $this->get(route('customer.order.table', $this->table->qr_token));
        $responseInactive->assertNotFound();
    }

    public function test_customer_can_submit_order_and_items_are_saved(): void
    {
        $payload = [
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 2,
                    'special_notes' => 'Extra sauce, well done',
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 1,
                    'special_notes' => 'Serve after meal',
                ],
            ],
        ];

        $response = $this->postJson(route('customer.order.submit', $this->table->qr_token), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        // Subtotal = (6500 * 2) + (2000 * 1) = 15000 MMK
        // Tax 5% = 750 MMK
        // Total = 15750 MMK
        $this->assertDatabaseHas('orders', [
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-07',
            'status' => 'OCCUPIED',
            'kitchen_status' => 'PENDING_COOK',
            'subtotal' => 15000,
            'tax_amount' => 750,
            'total_amount' => 15750,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product1->id,
            'item_name' => 'Italian Hamburg Steak',
            'quantity' => 2,
            'unit_price' => 6500,
            'subtotal' => 13000,
            'special_notes' => 'Extra sauce, well done',
            'is_cooked' => false,
            'is_verified' => false,
        ]);

        // Table status should now be OCCUPIED
        $this->assertEquals('OCCUPIED', $this->table->fresh()->status);
    }

    public function test_staff_can_simulate_test_order_from_admin(): void
    {
        $response = $this->actingAs($this->staff)->post(route('admin.orders.simulate'), [
            'table_number' => 'T-07',
        ]);

        $response->assertRedirect(route('admin.orders.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-07',
            'kitchen_status' => 'PENDING_COOK',
        ]);
    }
}
