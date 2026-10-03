<?php

namespace Tests\Feature\Admin;

use App\Http\Resources\OrderSlipResource;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderVerificationAndReprintTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    private User $manager;

    private User $staff;

    private DiningTable $table;

    private Order $order;

    private OrderItem $item1;

    private OrderItem $item2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Saizeriya Test Kitchen',
            'slug' => 'saizeriya-test',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'U Hla - Owner',
            'email' => 'owner@test.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ko Zaw - Manager',
            'email' => 'manager@test.com',
            'password' => Hash::make('password123'),
        ]);
        $this->manager->assignRole('MANAGER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Mg Kyaw - Server',
            'email' => 'staff@test.com',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');

        $this->table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-04',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
            'status' => 'ORDERING',
        ]);

        $this->order = Order::create([
            'restaurant_id' => $this->restaurant->id,
            'order_number' => 'ORD-20260926-0042',
            'table_number' => 'T-04',
            'guest_count' => 3,
            'order_type' => 'DINE_IN',
            'subtotal' => 15000,
            'tax_amount' => 750,
            'total_amount' => 15750,
            'status' => 'PENDING',
            'kitchen_status' => 'PENDING_COOK',
            'reprint_count' => 0,
        ]);

        $this->item1 = OrderItem::create([
            'order_id' => $this->order->id,
            'item_name' => 'Hamburg Steak (Beef)',
            'special_notes' => 'Medium Well',
            'quantity' => 2,
            'unit_price' => 5000,
            'subtotal' => 10000,
            'is_cooked' => false,
            'is_verified' => false,
        ]);

        $this->item2 = OrderItem::create([
            'order_id' => $this->order->id,
            'item_name' => 'Milanese Doria Rice',
            'special_notes' => 'Extra Cheese',
            'quantity' => 1,
            'unit_price' => 5000,
            'subtotal' => 5000,
            'is_cooked' => false,
            'is_verified' => false,
        ]);
    }

    public function test_operations_staff_can_view_kitchen_pass_verification_screen(): void
    {
        $response = $this->actingAs($this->staff)->get(route('admin.orders.verification'));
        $response->assertOk();
        $response->assertSee('Kitchen Pass & Order Verification');
        $response->assertSee('TABLE T-04');
        $response->assertSee('Hamburg Steak (Beef)');
        $response->assertSee('Medium Well');
    }

    public function test_waiter_can_toggle_dish_verification_status(): void
    {
        $this->assertFalse($this->item1->is_verified);

        $response = $this->actingAs($this->staff)->post(route('admin.orders.verify', $this->order), [
            'action' => 'TOGGLE_ITEM',
            'item_id' => $this->item1->id,
        ]);

        $response->assertRedirect();
        $this->assertTrue($this->item1->fresh()->is_verified);
    }

    public function test_kitchen_can_mark_order_ready_for_pickup(): void
    {
        $response = $this->actingAs($this->staff)->post(route('admin.orders.verify', $this->order), [
            'action' => 'MARK_READY',
        ]);

        $response->assertRedirect();
        $freshOrder = $this->order->fresh();
        $this->assertEquals('READY_FOR_DELIVERY', $freshOrder->kitchen_status);
        $this->assertTrue($this->item1->fresh()->is_cooked);
    }

    public function test_waiter_can_verify_and_deliver_order_to_table(): void
    {
        $response = $this->actingAs($this->staff)->post(route('admin.orders.verify', $this->order), [
            'action' => 'VERIFY_AND_DELIVER',
        ]);

        $response->assertRedirect();

        $freshOrder = $this->order->fresh();
        $this->assertEquals('SERVED_TO_TABLE', $freshOrder->kitchen_status);
        $this->assertEquals('OCCUPIED', $freshOrder->status);
        $this->assertNotNull($freshOrder->verified_at);
        $this->assertEquals($this->staff->id, $freshOrder->verified_by);

        // Check dining table status updated to OCCUPIED
        $this->assertEquals('OCCUPIED', $this->table->fresh()->status);

        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'VERIFIED_AND_SERVED_ORDER',
            'user_id' => $this->staff->id,
        ]);
    }

    public function test_staff_can_reproduce_reprint_kitchen_chit_and_audit_log_is_recorded(): void
    {
        $this->assertEquals(0, $this->order->reprint_count);

        $response = $this->actingAs($this->staff)->post(route('admin.orders.reprint', $this->order), [
            'slip_type' => 'KITCHEN_CHIT',
            'reason' => 'PRINTER_PAPER_OUT',
        ]);

        $response->assertRedirect(route('admin.orders.printKitchenChit', $this->order));

        $freshOrder = $this->order->fresh();
        $this->assertEquals(1, $freshOrder->reprint_count);

        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'REPRINT_ORDER_SLIP',
            'user_id' => $this->staff->id,
        ]);
    }

    public function test_staff_can_reproduce_reprint_customer_bill_slip_with_reprint_counter(): void
    {
        $response = $this->actingAs($this->staff)->post(route('admin.orders.reprint', $this->order), [
            'slip_type' => 'CUSTOMER_BILL',
            'reason' => 'SLIP_LOST_OR_WET',
        ]);

        $response->assertRedirect(route('admin.orders.printCustomerBill', $this->order));
        $this->assertEquals(1, $this->order->fresh()->reprint_count);
    }

    public function test_kitchen_chit_and_customer_bill_render_reprint_watermark_when_reprinted(): void
    {
        // First print without reprint
        $view1 = $this->actingAs($this->owner)->get(route('admin.orders.printKitchenChit', $this->order));
        $view1->assertOk();
        $view1->assertDontSee('*** REPRINT #', false);

        // Increment reprint count
        $this->order->incrementReprintCount();

        // Print with duplicate banner
        $view2 = $this->actingAs($this->owner)->get(route('admin.orders.printKitchenChit', $this->order));
        $view2->assertOk();
        $view2->assertSee('*** REPRINT #1 (DUPLICATE) ***', false);

        // Customer bill check
        $billView = $this->actingAs($this->owner)->get(route('admin.orders.printCustomerBill', $this->order));
        $billView->assertOk();
        $billView->assertSee('*** DUPLICATE (REPRINT #1) ***', false);
        $billView->assertSee('15,750 MMK');
    }

    public function test_order_slip_resource_transforms_cleanly(): void
    {
        $this->order->incrementReprintCount();
        $resource = (new OrderSlipResource($this->order->fresh(['items', 'verifier'])))->toArray(new Request);

        $this->assertEquals('ORD-20260926-0042', $resource['order_number']);
        $this->assertEquals('T-04', $resource['table_number']);
        $this->assertEquals('MMK', $resource['currency']);
        $this->assertTrue($resource['is_reprint']);
        $this->assertEquals('REPRINT #1', $resource['reprint_badge']);
        $this->assertCount(2, $resource['items']);
    }

    public function test_staff_can_reprint_both_slips_continuous(): void
    {
        $response = $this->actingAs($this->staff)->post(route('admin.orders.reprint', $this->order), [
            'slip_type' => 'BOTH',
            'reason' => 'PRINTER_PAPER_OUT',
        ]);

        $response->assertRedirect(route('admin.orders.printBothSlips', $this->order));

        $bothView = $this->actingAs($this->staff)->get(route('admin.orders.printBothSlips', $this->order));
        $bothView->assertOk();
        $bothView->assertSee('KITCHEN ORDER CHIT');
        $bothView->assertSee('CUSTOMER BILL SLIP');
        $bothView->assertSee('CUT HERE');
    }
}
