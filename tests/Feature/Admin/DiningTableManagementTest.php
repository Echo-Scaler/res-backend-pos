<?php

namespace Tests\Feature\Admin;

use App\Http\Resources\DiningTableResource;
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

class DiningTableManagementTest extends TestCase
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
            'name' => 'Saizeriya Yangon Flagship',
            'slug' => 'saizeriya-yangon',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'U Thant - Owner',
            'email' => 'owner@saizeriya.mm',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ko Zaw - Floor Manager',
            'email' => 'manager@saizeriya.mm',
            'password' => Hash::make('password123'),
        ]);
        $this->manager->assignRole('MANAGER');

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ma Hnin - Cashier',
            'email' => 'cashier@saizeriya.mm',
            'password' => Hash::make('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Mg Kyaw - Waiter',
            'email' => 'staff@saizeriya.mm',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_owner_and_manager_can_view_dining_tables_index_and_kpis(): void
    {
        DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-01',
            'name' => 'Window Booth',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
            'status' => 'VACANT',
        ]);

        DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-02',
            'seating_capacity' => 6,
            'floor_area' => 'Outdoor Terrace',
            'status' => 'OCCUPIED',
        ]);

        // Owner access
        $response = $this->actingAs($this->owner)->get(route('admin.tables.index'));
        $response->assertOk();
        $response->assertSee('Dining Floor & Table Management');
        $response->assertSee('T-01');
        $response->assertSee('T-02');
        $response->assertSee('Window Booth');

        // Manager access
        $mgrResponse = $this->actingAs($this->manager)->get(route('admin.tables.index'));
        $mgrResponse->assertOk();

        // Filter by OCCUPIED status: allTables is still passed to view for transfer modal
        $filterResponse = $this->actingAs($this->owner)->get(route('admin.tables.index', ['status' => 'OCCUPIED']));
        $filterResponse->assertOk();
        $filterResponse->assertViewHas('allTables');
        $this->assertCount(2, $filterResponse->viewData('allTables'));
    }

    public function test_cashier_and_staff_cannot_manage_dining_tables(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('admin.tables.index'));
        $response->assertForbidden();

        $staffResponse = $this->actingAs($this->staff)->get(route('admin.tables.index'));
        $staffResponse->assertForbidden();
    }

    public function test_can_create_dining_table_with_unique_number_and_auto_generated_qr_token(): void
    {
        $payload = [
            'table_number' => 'T-05',
            'name' => 'Garden Terrace 1',
            'seating_capacity' => 4,
            'floor_area' => 'Outdoor Terrace',
            'status' => 'VACANT',
            'notes' => 'Near garden fountain',
        ];

        $response = $this->actingAs($this->owner)->post(route('admin.tables.store'), $payload);
        $response->assertRedirect(route('admin.tables.index'));

        $this->assertDatabaseHas('dining_tables', [
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-05',
            'seating_capacity' => 4,
            'floor_area' => 'Outdoor Terrace',
            'status' => 'VACANT',
        ]);

        $table = DiningTable::where('table_number', 'T-05')->first();
        $this->assertNotNull($table->qr_token);
        $this->assertStringContainsString('/order/table/'.$table->qr_token, $table->getOrderUrl());
    }

    public function test_duplicate_table_number_in_same_restaurant_is_rejected(): void
    {
        DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-01',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
        ]);

        $payload = [
            'table_number' => 'T-01',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
        ];

        $response = $this->actingAs($this->owner)->post(route('admin.tables.store'), $payload);
        $response->assertSessionHasErrors(['table_number']);
    }

    public function test_table_number_can_be_same_across_different_restaurants(): void
    {
        DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-01',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
        ]);

        $otherRestaurant = Restaurant::create([
            'name' => 'Mandalay Royal Diner',
            'slug' => 'mandalay-royal',
            'is_active' => true,
        ]);

        $otherOwner = User::create([
            'restaurant_id' => $otherRestaurant->id,
            'name' => 'U Ba - Mandalay Owner',
            'email' => 'mandalay@saizeriya.mm',
            'password' => Hash::make('password123'),
        ]);
        $otherOwner->assignRole('OWNER');

        $payload = [
            'table_number' => 'T-01',
            'seating_capacity' => 6,
            'floor_area' => 'VIP Suite',
        ];

        $response = $this->actingAs($otherOwner)->post(route('admin.tables.store'), $payload);
        $response->assertRedirect(route('admin.tables.index'));

        $this->assertDatabaseHas('dining_tables', [
            'restaurant_id' => $otherRestaurant->id,
            'table_number' => 'T-01',
        ]);
    }

    public function test_can_update_table_details(): void
    {
        $table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-03',
            'seating_capacity' => 2,
            'floor_area' => 'Main Dining Hall',
            'status' => 'VACANT',
        ]);

        $updatePayload = [
            'table_number' => 'T-03A',
            'name' => 'Extended Corner Table',
            'seating_capacity' => 6,
            'floor_area' => 'Main Dining Hall',
            'status' => 'RESERVED',
        ];

        $response = $this->actingAs($this->manager)->put(route('admin.tables.update', $table), $updatePayload);
        $response->assertRedirect(route('admin.tables.index'));

        $this->assertDatabaseHas('dining_tables', [
            'id' => $table->id,
            'table_number' => 'T-03A',
            'seating_capacity' => 6,
            'status' => 'RESERVED',
        ]);
    }

    public function test_can_update_table_status_via_patch(): void
    {
        $table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-08',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
            'status' => 'VACANT',
        ]);

        $response = $this->actingAs($this->owner)->patch(route('admin.tables.updateStatus', $table), [
            'status' => 'OCCUPIED',
        ]);

        $response->assertRedirect();
        $this->assertEquals('OCCUPIED', $table->fresh()->status);
    }

    public function test_can_regenerate_table_qr_token(): void
    {
        $table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-10',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
            'qr_token' => 'original-token-12345',
        ]);

        $response = $this->actingAs($this->owner)->post(route('admin.tables.regenerateQr', $table));
        $response->assertRedirect();

        $freshTable = $table->fresh();
        $this->assertNotEquals('original-token-12345', $freshTable->qr_token);
        $this->assertNotEmpty($freshTable->qr_token);
    }

    public function test_can_view_print_stand_layout(): void
    {
        $table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-04',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
        ]);

        $response = $this->actingAs($this->owner)->get(route('admin.tables.print-stand', $table));
        $response->assertOk();
        $response->assertSee('TABLE T-04');
        $response->assertSee('Scan with Phone to Order');
        $response->assertSee('ဟင်းပွဲများ ကြည့်ရှုပြီး အော်ဒါတင်ရန် စကင်ဖတ်ပါ');
    }

    public function test_can_view_batch_print_stands_layout(): void
    {
        DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-01',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
        ]);

        $response = $this->actingAs($this->owner)->get(route('admin.tables.batch-print'));
        $response->assertOk();
        $response->assertSee('Batch Printing');
        $response->assertSee('TABLE T-01');
    }

    public function test_customer_can_open_table_qr_order_page_without_auth(): void
    {
        $table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-09',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
            'qr_token' => 'customer-qr-token-abc123',
            'is_active' => true,
        ]);

        // Access public customer menu via QR link (unauthenticated)
        $response = $this->get(route('customer.order.table', ['qr_token' => 'customer-qr-token-abc123']));
        $response->assertOk();
        $response->assertSee('Table T-09');
        $response->assertSee('Saizeriya Yangon Flagship');
        $response->assertSee('Table Self-Ordering');
    }

    public function test_cannot_delete_occupied_table_until_cleared(): void
    {
        $table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-06',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
            'status' => 'OCCUPIED',
        ]);

        $response = $this->actingAs($this->owner)->delete(route('admin.tables.destroy', $table));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('dining_tables', ['id' => $table->id]);

        // Set to VACANT and delete succeeds
        $table->update(['status' => 'VACANT']);
        $deleteResponse = $this->actingAs($this->owner)->delete(route('admin.tables.destroy', $table));
        $deleteResponse->assertRedirect(route('admin.tables.index'));
        $this->assertDatabaseMissing('dining_tables', ['id' => $table->id]);
    }

    public function test_dining_table_resource_transforms_cleanly(): void
    {
        $table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-12',
            'name' => 'Royal Corner',
            'seating_capacity' => 6,
            'floor_area' => 'VIP Room',
            'status' => 'VACANT',
        ]);

        $resource = (new DiningTableResource($table))->toArray(new Request);

        $this->assertEquals('T-12', $resource['table_number']);
        $this->assertEquals('Royal Corner', $resource['name']);
        $this->assertEquals(6, $resource['seating_capacity']);
        $this->assertEquals('VIP Room', $resource['floor_area']);
        $this->assertEquals('VACANT', $resource['status']);
        $this->assertStringContainsString('/order/table/'.$table->qr_token, $resource['order_url']);
    }

    public function test_table_card_displays_active_order_items_and_reprint_actions(): void
    {
        $table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-02',
            'name' => 'Window Corner 2',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
            'status' => 'OCCUPIED',
        ]);

        $order = Order::create([
            'restaurant_id' => $this->restaurant->id,
            'order_number' => 'ORD-20260926-T02',
            'table_number' => 'T-02',
            'guest_count' => 3,
            'order_type' => 'DINE_IN',
            'subtotal' => 18500,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'cogs_amount' => 9000,
            'total_amount' => 18500,
            'paid_amount' => 0,
            'refund_amount' => 0,
            'outstanding_amount' => 18500,
            'status' => 'PENDING',
            'kitchen_status' => 'PENDING_COOK',
            'reprint_count' => 0,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'item_name' => 'Milano Doria Rice Gratin',
            'quantity' => 2,
            'unit_price' => 6500,
            'cost_price' => 3200,
            'subtotal' => 13000,
            'profit' => 6600,
            'special_notes' => 'Extra cheese topping',
        ]);

        $table->update(['current_order_id' => $order->id]);

        $response = $this->actingAs($this->owner)->get(route('admin.tables.index'));
        $response->assertOk();
        $response->assertSee('ORD-20260926-T02');
        $response->assertSee('Milano Doria Rice Gratin');
        $response->assertSee('Extra cheese topping');
        $response->assertSee('18,500 MMK');
        $response->assertSee('Reprint');
        $response->assertSee('Chit');
        $response->assertSee('Bill');
    }

    public function test_async_table_status_update_returns_json_and_floor_metrics_for_zero_refresh(): void
    {
        $table = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-03',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
            'status' => 'VACANT',
        ]);

        $response = $this->actingAs($this->owner)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->patchJson(route('admin.tables.updateStatus', $table), [
                'status' => 'OCCUPIED',
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'table' => ['id', 'table_number', 'status'],
            'metrics' => ['total_tables', 'vacant_tables', 'occupied_tables', 'billing_tables', 'total_capacity'],
        ]);

        $this->assertEquals('OCCUPIED', $table->fresh()->status);
        $this->assertEquals(1, $response->json('metrics.occupied_tables'));
    }

    public function test_owner_or_manager_can_transfer_occupied_table_and_order_to_vacant_table(): void
    {
        $sourceTable = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-01',
            'status' => 'OCCUPIED',
            'seating_capacity' => 2,
            'floor_area' => 'Bar Area',
        ]);

        $targetTable = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-05',
            'status' => 'VACANT',
            'seating_capacity' => 4,
            'floor_area' => 'Outdoor Terrace',
        ]);

        $order = Order::create([
            'restaurant_id' => $this->restaurant->id,
            'order_number' => 'ORD-20260926-MV1',
            'table_number' => 'T-01',
            'subtotal' => 20000,
            'tax_amount' => 1000,
            'total_amount' => 21000,
            'status' => 'OCCUPIED',
            'kitchen_status' => 'COOKING',
        ]);

        $sourceTable->update(['current_order_id' => $order->id]);

        $response = $this->actingAs($this->manager)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('admin.tables.transfer', $sourceTable), [
                'target_table_id' => $targetTable->id,
                'reason' => 'Customer requested outdoor terrace view',
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'source_table',
            'target_table',
            'metrics',
        ]);

        // Source table must be VACANT and free
        $this->assertEquals('VACANT', $sourceTable->fresh()->status);
        $this->assertNull($sourceTable->fresh()->current_order_id);

        // Target table must now have the order and be OCCUPIED
        $this->assertEquals('OCCUPIED', $targetTable->fresh()->status);
        $this->assertEquals($order->id, $targetTable->fresh()->current_order_id);

        // Order's table_number must be updated to target table
        $this->assertEquals('T-05', $order->fresh()->table_number);
        $this->assertStringContainsString('Customer requested outdoor terrace view', $order->fresh()->cancellation_reason);
    }

    public function test_cannot_transfer_to_occupied_table_or_same_table(): void
    {
        $sourceTable = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-10',
            'status' => 'OCCUPIED',
        ]);

        $occupiedTarget = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-11',
            'status' => 'OCCUPIED',
        ]);

        // Cannot transfer to same table
        $sameResponse = $this->actingAs($this->owner)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('admin.tables.transfer', $sourceTable), [
                'target_table_id' => $sourceTable->id,
            ]);
        $sameResponse->assertStatus(422);

        // Cannot transfer to already OCCUPIED table
        $occupiedResponse = $this->actingAs($this->owner)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('admin.tables.transfer', $sourceTable), [
                'target_table_id' => $occupiedTarget->id,
            ]);
        $occupiedResponse->assertStatus(422);
    }

    public function test_cashier_and_staff_cannot_transfer_tables(): void
    {
        $sourceTable = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-20',
            'status' => 'OCCUPIED',
        ]);

        $targetTable = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => 'T-21',
            'status' => 'VACANT',
        ]);

        $response = $this->actingAs($this->staff)->post(route('admin.tables.transfer', $sourceTable), [
            'target_table_id' => $targetTable->id,
        ]);
        $response->assertForbidden();

        $cashierResponse = $this->actingAs($this->cashier)->post(route('admin.tables.transfer', $sourceTable), [
            'target_table_id' => $targetTable->id,
        ]);
        $cashierResponse->assertForbidden();
    }

    public function test_owner_or_manager_can_transfer_table_using_target_table_number_input(): void
    {
        $table2 = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => '2',
            'status' => 'OCCUPIED',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
        ]);

        $table5 = DiningTable::create([
            'restaurant_id' => $this->restaurant->id,
            'table_number' => '5',
            'status' => 'VACANT',
            'seating_capacity' => 4,
            'floor_area' => 'Main Dining Hall',
        ]);

        $order = Order::create([
            'restaurant_id' => $this->restaurant->id,
            'order_number' => 'ORD-20260927-0002',
            'table_number' => '2',
            'subtotal' => 19000,
            'tax_amount' => 950,
            'total_amount' => 19950,
            'status' => 'OCCUPIED',
            'kitchen_status' => 'READY_FOR_DELIVERY',
        ]);

        $table2->update(['current_order_id' => $order->id]);

        // Transfer by sending target_table_number = '5'
        $response = $this->actingAs($this->owner)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('admin.tables.transfer', $table2), [
                'target_table_number' => '5',
                'reason' => 'Customer requested Table 5 window seat',
            ]);

        $response->assertOk();
        $this->assertEquals('VACANT', $table2->fresh()->status);
        $this->assertNull($table2->fresh()->current_order_id);

        $this->assertEquals('OCCUPIED', $table5->fresh()->status);
        $this->assertEquals($order->id, $table5->fresh()->current_order_id);
        $this->assertEquals('5', $order->fresh()->table_number);
    }
}
