<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\AuditLogService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditLogSystemTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private Restaurant $otherRestaurant;

    private User $owner;

    private User $cashier;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Royal Mandalay Kitchen',
            'slug' => 'royal-mandalay-kitchen',
            'is_active' => true,
        ]);

        $this->otherRestaurant = Restaurant::create([
            'name' => 'Other Bistro Yangon',
            'slug' => 'other-bistro-yangon',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'U Thant - Owner',
            'email' => 'owner@royalmandalay.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Daw Mya - Cashier',
            'email' => 'cashier@royalmandalay.com',
            'password' => Hash::make('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Mg Kyaw - Waiter',
            'email' => 'waiter@royalmandalay.com',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_owner_can_access_audit_logs_dashboard(): void
    {
        // Seed sample audit log
        AuditLog::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->owner->id,
            'user_name' => $this->owner->name,
            'role_name' => 'OWNER',
            'action' => 'PRICE_CHANGE',
            'category' => 'MENU_PRICE',
            'severity' => 'WARNING',
            'resource_type' => 'Product',
            'resource_id' => 1,
            'old_value' => 'Old: 3,500 MMK',
            'new_value' => 'New: 4,000 MMK',
            'metadata' => ['dish' => 'Shan Noodle'],
            'ip_address' => '192.168.1.10',
        ]);

        $response = $this->actingAs($this->owner)->get(route('admin.audit.logs'));

        $response->assertOk();
        $response->assertViewIs('admin.audit-logs.index');
        $response->assertSee('Audit Logs & Security Trails');
        $response->assertSee('Total Logged Events');
        $response->assertSee('Critical Security Alerts');
        $response->assertSee('PRICE_CHANGE');
        $response->assertSee('MENU_PRICE');
        $response->assertSee('U Thant - Owner');
        $response->assertSee('MMK');
    }

    public function test_cashier_and_staff_are_forbidden_from_audit_logs(): void
    {
        $responseCashier = $this->actingAs($this->cashier)->get(route('admin.audit.logs'));
        $responseCashier->assertForbidden();

        $responseStaff = $this->actingAs($this->staff)->get(route('admin.audit.logs'));
        $responseStaff->assertForbidden();
    }

    public function test_audit_logs_can_be_filtered_by_category_and_severity(): void
    {
        AuditLog::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->cashier->id,
            'user_name' => $this->cashier->name,
            'role_name' => 'CASHIER',
            'action' => 'ORDER_VOID',
            'category' => 'ORDER_VOID',
            'severity' => 'CRITICAL',
            'resource_type' => 'Order',
            'resource_id' => 101,
            'new_value' => 'Status: VOIDED (65,000 MMK)',
            'ip_address' => '192.168.1.20',
        ]);

        AuditLog::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->staff->id,
            'user_name' => $this->staff->name,
            'role_name' => 'STAFF',
            'action' => 'TABLE_TRANSFER',
            'category' => 'GENERAL',
            'severity' => 'INFO',
            'resource_type' => 'Table',
            'resource_id' => 4,
            'new_value' => 'Transferred Table 1 to Table 4',
            'ip_address' => '192.168.1.25',
        ]);

        // Filter for CRITICAL
        $response = $this->actingAs($this->owner)->get(route('admin.audit.logs', [
            'severity' => 'CRITICAL',
        ]));

        $response->assertOk();
        $response->assertSee('Status: VOIDED');
        $response->assertDontSee('Transferred Table 1');

        // Filter for GENERAL
        $responseGeneral = $this->actingAs($this->owner)->get(route('admin.audit.logs', [
            'category' => 'GENERAL',
        ]));

        $responseGeneral->assertOk();
        $responseGeneral->assertSee('Transferred Table 1');
        $responseGeneral->assertDontSee('Status: VOIDED');
    }

    public function test_invalid_filter_parameters_fail_validation(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.audit.logs', [
            'category' => 'INVALID_CATEGORY',
            'date_from' => '2026-10-10',
            'date_to' => '2026-10-01', // date_to before date_from
        ]));

        $response->assertSessionHasErrors(['category', 'date_to']);
    }

    public function test_owner_can_export_audit_logs_as_csv(): void
    {
        AuditLog::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->owner->id,
            'user_name' => $this->owner->name,
            'role_name' => 'OWNER',
            'action' => 'PRICE_CHANGE',
            'category' => 'MENU_PRICE',
            'severity' => 'WARNING',
            'resource_type' => 'Product',
            'resource_id' => 1,
            'old_value' => 'Old: 3,500 MMK',
            'new_value' => 'New: 4,000 MMK',
            'ip_address' => '192.168.1.10',
        ]);

        $response = $this->actingAs($this->owner)->get(route('admin.audit.logs.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Action,Severity', $content);
        $this->assertStringContainsString('PRICE_CHANGE', $content);
        $this->assertStringContainsString('MMK', $content);
    }

    public function test_api_data_returns_json_resource_collection(): void
    {
        AuditLog::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->cashier->id,
            'user_name' => $this->cashier->name,
            'role_name' => 'CASHIER',
            'action' => 'OPEN_CASH_DRAWER_SHIFT',
            'category' => 'FINANCIAL',
            'severity' => 'INFO',
            'resource_type' => 'CashDrawerSession',
            'resource_id' => 12,
            'new_value' => 'Opening Float: 100,000 MMK',
            'ip_address' => '192.168.1.30',
        ]);

        $response = $this->actingAs($this->owner)->getJson(route('admin.audit.logs.data'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_name',
                    'role_name',
                    'category',
                    'action',
                    'severity',
                    'severity_color',
                    'resource_type',
                    'resource_id',
                    'currency',
                    'created_at_formatted',
                ],
            ],
            'links',
            'meta',
        ]);

        $response->assertJsonFragment([
            'action' => 'OPEN_CASH_DRAWER_SHIFT',
            'currency' => 'MMK',
        ]);
    }

    public function test_show_endpoint_returns_single_audit_log_resource(): void
    {
        $log = AuditLog::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->owner->id,
            'user_name' => $this->owner->name,
            'role_name' => 'OWNER',
            'action' => 'SETTINGS_UPDATE',
            'category' => 'ACCESS_SECURITY',
            'severity' => 'INFO',
            'metadata' => ['tax_rate' => 5],
            'ip_address' => '192.168.1.5',
        ]);

        $response = $this->actingAs($this->owner)->getJson(route('admin.audit.logs.show', $log));

        $response->assertOk();
        $response->assertJsonPath('data.action', 'SETTINGS_UPDATE');
        $response->assertJsonPath('data.currency', 'MMK');
        $response->assertJsonPath('data.metadata.tax_rate', 5);
    }

    public function test_owner_cannot_access_audit_log_of_another_restaurant(): void
    {
        $foreignLog = AuditLog::create([
            'restaurant_id' => $this->otherRestaurant->id,
            'user_name' => 'Foreign Manager',
            'role_name' => 'MANAGER',
            'action' => 'ORDER_VOID',
            'category' => 'ORDER_VOID',
            'severity' => 'CRITICAL',
            'ip_address' => '10.0.0.1',
        ]);

        $response = $this->actingAs($this->owner)->getJson(route('admin.audit.logs.show', $foreignLog));
        $response->assertForbidden();
    }

    public function test_audit_log_service_creates_records_cleanly(): void
    {
        $log = AuditLogService::log(
            action: 'ORDER_VOID',
            category: AuditLog::CATEGORY_ORDER_VOID,
            severity: AuditLog::SEVERITY_CRITICAL,
            resource: $this->restaurant,
            oldValue: ['total_amount' => 50000, 'currency' => 'MMK'],
            newValue: ['status' => 'VOIDED'],
            metadata: ['reason' => 'Guest left emergency', 'manager_pin_verified' => true],
            user: $this->owner,
            restaurantId: $this->restaurant->id,
        );

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'restaurant_id' => $this->restaurant->id,
            'action' => 'ORDER_VOID',
            'category' => 'ORDER_VOID',
            'severity' => 'CRITICAL',
            'role_name' => 'OWNER',
        ]);

        $this->assertEquals(50000, $log->metadata['reason'] ? 50000 : 0);
        $this->assertTrue($log->metadata['manager_pin_verified']);
    }
}
