<?php

namespace Tests\Feature\Admin;

use App\Models\CashDrawerSession;
use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RealPosSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $cashier;

    protected User $staff;

    protected Restaurant $restaurant;

    protected DiningTable $table;

    protected Product $productA;

    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'OWNER', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'MANAGER', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'CASHIER', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'STAFF', 'guard_name' => 'web']);

        $this->restaurant = Restaurant::create([
            'name' => 'Antigravity Bistro',
            'slug' => 'antigravity-bistro',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Owner Ko Min',
            'email' => 'owner@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Cashier Ma Thandar',
            'email' => 'cashier@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Waiter Ko Kyaw',
            'email' => 'waiter@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->staff->assignRole('STAFF');

        $category = Category::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Main Dishes',
            'slug' => 'main-dishes',
        ]);

        $this->productA = Product::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $category->id,
            'name' => 'Hamburg Steak',
            'price' => 10000,
            'cost_price' => 4500,
            'is_available' => true,
        ]);

        $this->productB = Product::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $category->id,
            'name' => 'Italian Doria',
            'price' => 6000,
            'cost_price' => 2500,
            'is_available' => true,
        ]);

        $this->table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-01',
            'name' => 'Table 1',
            'seating_capacity' => 4,
            'floor_area' => 'Main Hall',
            'status' => 'VACANT',
            'is_active' => true,
        ]);
    }

    public function test_cashier_can_open_shift_with_initial_float(): void
    {
        $response = $this->actingAs($this->cashier)->post(route('cashier.shift.open'), [
            'terminal_code' => 'POS-REG-01',
            'opening_float' => 100000,
            'notes' => 'Morning shift open',
        ]);

        $response->assertRedirect(route('cashier.dashboard'));

        $this->assertDatabaseHas('cash_drawer_sessions', [
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->cashier->id,
            'terminal_code' => 'POS-REG-01',
            'opening_float' => 100000,
            'expected_cash' => 100000,
            'status' => 'OPEN',
        ]);
    }

    public function test_cashier_cannot_open_duplicate_shift_while_one_is_active(): void
    {
        CashDrawerSession::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->cashier->id,
            'terminal_code' => 'POS-REG-01',
            'opened_at' => now(),
            'opening_float' => 100000,
            'expected_cash' => 100000,
            'status' => 'OPEN',
        ]);

        $response = $this->actingAs($this->cashier)->postJson(route('cashier.shift.open'), [
            'terminal_code' => 'POS-REG-02',
            'opening_float' => 50000,
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_cashier_can_record_cash_in_and_cash_out(): void
    {
        $session = CashDrawerSession::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->cashier->id,
            'terminal_code' => 'POS-REG-01',
            'opened_at' => now(),
            'opening_float' => 100000,
            'expected_cash' => 100000,
            'status' => 'OPEN',
        ]);

        // Cash In
        $resIn = $this->actingAs($this->cashier)->postJson(route('cashier.shift.cashInOut'), [
            'type' => 'CASH_IN',
            'amount' => 20000,
            'reason' => 'Add change notes',
        ]);
        $resIn->assertStatus(200)->assertJson(['success' => true]);

        $this->assertEquals(120000, $session->fresh()->expected_cash);

        // Cash Out
        $resOut = $this->actingAs($this->cashier)->postJson(route('cashier.shift.cashInOut'), [
            'type' => 'CASH_OUT',
            'amount' => 5000,
            'reason' => 'Ice bag purchase',
        ]);
        $resOut->assertStatus(200)->assertJson(['success' => true]);

        $this->assertEquals(115000, $session->fresh()->expected_cash);
    }

    public function test_waiter_can_seat_guests_and_submit_tableside_order(): void
    {
        $response = $this->actingAs($this->staff)->post(route('staff.orders.store'), [
            'table_id' => $this->table->id,
            'guest_count' => 3,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 2,
                    'special_notes' => 'Medium well',
                ],
                [
                    'product_id' => $this->productB->id,
                    'quantity' => 1,
                    'special_notes' => null,
                ],
            ],
        ]);

        $response->assertRedirect(route('staff.dashboard'));

        $this->assertDatabaseHas('orders', [
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-01',
            'guest_count' => 3,
            'subtotal' => 26000, // 2x 10,000 + 1x 6,000
            'tax_amount' => 1300, // 5% of 26,000
            'total_amount' => 27300,
            'status' => 'OCCUPIED',
        ]);

        $this->assertEquals('OCCUPIED', $this->table->fresh()->status);
        $this->assertNotNull($this->table->fresh()->current_order_id);
    }

    public function test_waiter_can_append_additional_dishes_repeat_round(): void
    {
        $this->actingAs($this->staff)->post(route('staff.orders.store'), [
            'table_id' => $this->table->id,
            'guest_count' => 2,
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 1],
            ],
        ]);

        $order = Order::where('table_number', 'T-01')->first();
        $this->assertEquals(10000, $order->subtotal);

        // Waiter adds round 2
        $addRes = $this->actingAs($this->staff)->postJson(route('staff.orders.addItems', $order), [
            'items' => [
                ['product_id' => $this->productB->id, 'quantity' => 2, 'special_notes' => 'Extra hot'],
            ],
        ]);

        $addRes->assertStatus(200)->assertJson(['success' => true]);

        $updatedOrder = $order->fresh(['items']);
        $this->assertCount(2, $updatedOrder->items);
        $this->assertEquals(22000, $updatedOrder->subtotal); // 10,000 + 12,000
        $this->assertEquals(1100, $updatedOrder->tax_amount);
        $this->assertEquals(23100, $updatedOrder->total_amount);
    }

    public function test_waiter_can_request_bill_changing_table_status_to_billing(): void
    {
        $this->actingAs($this->staff)->post(route('staff.orders.store'), [
            'table_id' => $this->table->id,
            'guest_count' => 2,
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 1],
            ],
        ]);

        $res = $this->actingAs($this->staff)->postJson(route('staff.tables.requestBill'), [
            'table_id' => $this->table->id,
        ]);

        $res->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals('BILLING', $this->table->fresh()->status);
    }

    public function test_cashier_can_fetch_order_details_for_checkout_modal(): void
    {
        $this->actingAs($this->staff)->post(route('staff.orders.store'), [
            'table_id' => $this->table->id,
            'guest_count' => 2,
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 1],
            ],
        ]);

        $order = Order::where('table_number', 'T-01')->first();

        $res = $this->actingAs($this->cashier)->getJson(route('cashier.orders.details', $order));

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'table_number' => 'T-01',
                    'currency' => 'MMK',
                    'formatted_subtotal' => '10,000 MMK',
                ],
            ]);
    }

    public function test_cashier_can_settle_bill_with_cash_and_change_is_computed(): void
    {
        // Open Cash Drawer Shift
        $session = CashDrawerSession::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->cashier->id,
            'terminal_code' => 'POS-REG-01',
            'opened_at' => now(),
            'opening_float' => 100000,
            'expected_cash' => 100000,
            'status' => 'OPEN',
        ]);

        // Place order: Subtotal 10,000 MMK + 5% tax (500) = 10,500 MMK
        $this->actingAs($this->staff)->post(route('staff.orders.store'), [
            'table_id' => $this->table->id,
            'guest_count' => 2,
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 1],
            ],
        ]);

        $order = Order::where('table_number', 'T-01')->first();

        // Customer tenders 20,000 MMK cash
        $settleRes = $this->actingAs($this->cashier)->postJson(route('cashier.orders.settle', $order), [
            'payment_method' => 'CASH',
            'amount_tendered' => 20000,
            'discount_type' => 'NONE',
        ]);

        $settleRes->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $order->refresh();
        $this->assertEquals('COMPLETED', $order->status);
        $this->assertEquals('PAID', $order->payment_status);
        $this->assertEquals(10500, $order->paid_amount);

        // Payment record created
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'CASH',
            'amount' => 10500,
            'tendered_amount' => 20000,
            'change_amount' => 9500,
        ]);

        // Dining table is released to VACANT
        $this->assertEquals('VACANT', $this->table->fresh()->status);
        $this->assertNull($this->table->fresh()->current_order_id);

        // Cash drawer sales updated
        $this->assertEquals(10500, $session->fresh()->cash_sales);
        $this->assertEquals(110500, $session->fresh()->expected_cash);
    }

    public function test_cashier_can_settle_bill_with_kbzpay_digital_wallet(): void
    {
        $session = CashDrawerSession::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->cashier->id,
            'terminal_code' => 'POS-REG-01',
            'opened_at' => now(),
            'opening_float' => 100000,
            'expected_cash' => 100000,
            'status' => 'OPEN',
        ]);

        $this->actingAs($this->staff)->post(route('staff.orders.store'), [
            'table_id' => $this->table->id,
            'guest_count' => 2,
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 1],
            ],
        ]);

        $order = Order::where('table_number', 'T-01')->first();

        $settleRes = $this->actingAs($this->cashier)->postJson(route('cashier.orders.settle', $order), [
            'payment_method' => 'KBZPAY',
            'reference_no' => 'KPZ-88991122',
        ]);

        $settleRes->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'KBZPAY',
            'reference_no' => 'KPZ-88991122',
        ]);

        $this->assertEquals(10500, $session->fresh()->digital_sales);
    }

    public function test_cashier_can_print_official_thermal_receipt(): void
    {
        $this->actingAs($this->staff)->post(route('staff.orders.store'), [
            'table_id' => $this->table->id,
            'guest_count' => 2,
            'items' => [
                ['product_id' => $this->productA->id, 'quantity' => 1],
            ],
        ]);

        $order = Order::where('table_number', 'T-01')->first();

        $res = $this->actingAs($this->cashier)->get(route('cashier.orders.receipt', $order));

        $res->assertStatus(200);
        $res->assertSee('OFFICIAL RECEIPT');
        $res->assertSee($order->order_number);
        $res->assertSee('MMK');
    }

    public function test_cashier_can_close_shift_and_produce_z_report(): void
    {
        $session = CashDrawerSession::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->cashier->id,
            'terminal_code' => 'POS-REG-01',
            'opened_at' => now(),
            'opening_float' => 100000,
            'expected_cash' => 100000,
            'status' => 'OPEN',
        ]);

        $res = $this->actingAs($this->cashier)->post(route('cashier.shift.close'), [
            'closing_actual_cash' => 102000, // +2,000 MMK over
            'notes' => 'Shift closed with 2,000 over',
        ]);

        $res->assertRedirect(route('cashier.shift.zReport', $session));

        $session->refresh();
        $this->assertEquals('CLOSED', $session->status);
        $this->assertEquals(102000, $session->closing_actual_cash);
        $this->assertEquals(2000, $session->cash_difference);

        // View Z-Report
        $zRes = $this->actingAs($this->cashier)->get(route('cashier.shift.zReport', $session));
        $zRes->assertStatus(200);
        $zRes->assertSee('SHIFT Z-REPORT');
        $zRes->assertSee('POS-REG-01');
        $zRes->assertSee('100,000 MMK');
    }
}
