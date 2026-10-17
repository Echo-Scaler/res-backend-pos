<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use App\Services\TaxCalculationService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TaxServiceSettingTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    private User $cashier;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Golden Palace Restaurant & Bistro',
            'slug' => 'golden-palace-restaurant',
            'phone' => '09-450001234',
            'address' => 'No. 45, Pyay Road, Kamayut, Yangon',
            'tax_id' => 'TIN-8921-MM',
            'default_tax_percent' => 5.0,
            'default_service_charge_percent' => 10.0,
            'is_tax_enabled' => true,
            'tax_name' => 'Commercial Tax',
            'tax_calculation_mode' => 'inclusive',
            'is_service_charge_enabled' => true,
            'service_charge_name' => 'Service Charge',
            'service_charge_dine_in_only' => true,
            'is_service_charge_taxable' => false,
            'takeaway_packaging_fee' => 500,
            'rounding_method' => 'round_50',
            'rounding_direction' => 'nearest',
            'round_cash_only' => true,
            'show_tax_breakdown_on_receipt' => true,
            'show_service_charge_on_receipt' => true,
            'print_tax_stamp_seal' => true,
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'U Hla - Owner',
            'email' => 'owner@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Daw Nu - Cashier',
            'email' => 'cashier@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ko Min - Waiter',
            'email' => 'waiter@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_owner_can_view_tax_and_service_settings_page(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.settings.tax'));

        $response->assertOk();
        $response->assertViewIs('admin.settings.tax');
        $response->assertSee('Tax & Service Charge Settings', false);
        $response->assertSee('Golden Palace Restaurant & Bistro');
        $response->assertSee('Commercial Tax');
        $response->assertSee('Live Bill Simulator');
        $response->assertSee('TIN-8921-MM');
    }

    public function test_cashier_and_staff_are_forbidden_from_tax_settings(): void
    {
        $responseCashier = $this->actingAs($this->cashier)->get(route('admin.settings.tax'));
        $responseCashier->assertForbidden();

        $responseStaff = $this->actingAs($this->staff)->get(route('admin.settings.tax'));
        $responseStaff->assertForbidden();
    }

    public function test_owner_can_update_tax_and_service_settings(): void
    {
        $payload = [
            'is_tax_enabled' => '1',
            'tax_name' => 'IRD Commercial Tax',
            'default_tax_percent' => '5.0',
            'tax_calculation_mode' => 'exclusive',
            'tax_id' => 'TIN-9999-MMK',
            'tax_office_name' => 'Kamayut Township Revenue Office',
            'apply_tax_dine_in' => '1',
            'apply_tax_takeaway' => '1',
            'apply_tax_delivery' => '1',
            'apply_tax_qr_order' => '1',
            'is_service_charge_enabled' => '1',
            'service_charge_name' => 'Staff Service Charge',
            'default_service_charge_percent' => '10.0',
            'is_service_charge_taxable' => '1',
            'service_charge_dine_in_only' => '1',
            'takeaway_packaging_fee' => 1000,
            'rounding_method' => 'round_100',
            'rounding_direction' => 'nearest',
            'round_cash_only' => '1',
            'show_tax_breakdown_on_receipt' => '1',
            'show_service_charge_on_receipt' => '1',
            'print_tax_stamp_seal' => '1',
            'tax_disclaimer_text' => 'All prices are subject to 5% Commercial Tax.',
        ];

        $response = $this->actingAs($this->owner)
            ->post(route('admin.settings.tax.update'), $payload);

        $response->assertRedirect(route('admin.settings.tax'));
        $response->assertSessionHas('success');

        $this->restaurant->refresh();
        $this->assertEquals('IRD Commercial Tax', $this->restaurant->tax_name);
        $this->assertEquals('exclusive', $this->restaurant->tax_calculation_mode);
        $this->assertEquals('TIN-9999-MMK', $this->restaurant->tax_id);
        $this->assertEquals(1000, $this->restaurant->takeaway_packaging_fee);
        $this->assertEquals('round_100', $this->restaurant->rounding_method);
        $this->assertTrue($this->restaurant->is_service_charge_taxable);

        // Verify Audit Log was recorded
        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $this->owner->id,
            'action' => 'TAX_SETTINGS_UPDATED',
        ]);
    }

    public function test_form_request_validation_fails_on_out_of_bounds_percentages(): void
    {
        $payload = [
            'is_tax_enabled' => '1',
            'tax_name' => 'Commercial Tax',
            'default_tax_percent' => '50.0', // Exceeds 30%
            'tax_calculation_mode' => 'invalid_mode',
            'is_service_charge_enabled' => '1',
            'service_charge_name' => 'Service Charge',
            'default_service_charge_percent' => '40.0', // Exceeds 25%
            'takeaway_packaging_fee' => -100, // Negative fee
            'rounding_method' => 'invalid_rounding',
            'rounding_direction' => 'nearest',
        ];

        $response = $this->actingAs($this->owner)
            ->post(route('admin.settings.tax.update'), $payload);

        $response->assertSessionHasErrors([
            'default_tax_percent',
            'default_service_charge_percent',
            'tax_calculation_mode',
            'takeaway_packaging_fee',
            'rounding_method',
        ]);
    }

    public function test_ajax_update_returns_tax_service_setting_resource(): void
    {
        $payload = [
            'is_tax_enabled' => '1',
            'tax_name' => 'Commercial Tax',
            'default_tax_percent' => '5.0',
            'tax_calculation_mode' => 'inclusive',
            'tax_id' => 'TIN-8921-MM',
            'apply_tax_dine_in' => '1',
            'apply_tax_takeaway' => '1',
            'apply_tax_delivery' => '1',
            'apply_tax_qr_order' => '1',
            'is_service_charge_enabled' => '1',
            'service_charge_name' => 'Dining Service Charge',
            'default_service_charge_percent' => '10.0',
            'is_service_charge_taxable' => '0',
            'service_charge_dine_in_only' => '1',
            'takeaway_packaging_fee' => 500,
            'rounding_method' => 'round_50',
            'rounding_direction' => 'nearest',
            'round_cash_only' => '1',
            'show_tax_breakdown_on_receipt' => '1',
            'show_service_charge_on_receipt' => '1',
            'print_tax_stamp_seal' => '1',
        ];

        $response = $this->actingAs($this->owner)
            ->postJson(route('admin.settings.tax.update'), $payload);

        $response->assertOk();
        $response->assertJsonPath('data.currency', 'MMK');
        $response->assertJsonPath('data.tax.name', 'Commercial Tax');
        $response->assertJsonPath('data.tax.rate_percent', 5);
        $response->assertJsonPath('data.tax.calculation_mode', 'inclusive');
        $response->assertJsonPath('data.service_charge.formatted_packaging_fee', '500 MMK');
    }

    public function test_api_data_endpoint_returns_json_resource(): void
    {
        $response = $this->actingAs($this->owner)
            ->getJson(route('admin.settings.tax.data'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'tax' => ['is_enabled', 'name', 'rate_percent', 'calculation_mode', 'tin_id', 'channels'],
                'service_charge' => ['is_enabled', 'name', 'rate_percent', 'is_taxable', 'dine_in_only', 'takeaway_packaging_fee'],
                'rounding' => ['method', 'direction', 'round_cash_only'],
                'receipt' => ['show_tax_breakdown', 'show_service_charge', 'print_tax_stamp_seal'],
                'currency',
                'currency_symbol',
            ],
        ]);
        $response->assertJsonPath('data.currency', 'MMK');
    }

    public function test_simulate_endpoint_calculates_bill_breakdown(): void
    {
        $response = $this->actingAs($this->owner)->postJson(route('admin.settings.tax.simulate'), [
            'subtotal' => 21500,
            'channel' => 'dine_in',
            'payment_method' => 'cash',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('calculation.subtotal', 21500);
        $response->assertJsonPath('calculation.subtotal_formatted', '21,500 MMK');
        $response->assertJsonPath('calculation.service_charge_amount', 2150); // 10%
        $response->assertJsonPath('calculation.currency', 'MMK');
    }

    public function test_tax_calculation_service_inclusive_and_exclusive_modes(): void
    {
        $service = new TaxCalculationService;

        // 1. Inclusive mode test: 20,000 MMK subtotal, 10% service charge, 5% inclusive tax
        $this->restaurant->update([
            'tax_calculation_mode' => 'inclusive',
            'default_tax_percent' => 5.0,
            'default_service_charge_percent' => 10.0,
            'service_charge_dine_in_only' => true,
            'is_service_charge_taxable' => false,
            'rounding_method' => 'none',
        ]);

        $resultInclusive = $service->calculate(
            subtotal: 20000,
            channel: 'dine_in',
            paymentMethod: 'cash',
            restaurant: $this->restaurant
        );

        $this->assertEquals(2000, $resultInclusive['service_charge_amount']); // 10% of 20,000
        $this->assertEquals(952, $resultInclusive['tax_amount']); // 20,000 - (20,000 / 1.05) = 952 MMK
        $this->assertEquals(22000, $resultInclusive['final_total']); // 20,000 + 2,000 (tax already inside 20,000)

        // 2. Exclusive mode test: 20,000 MMK subtotal, 10% service charge, 5% exclusive tax
        $this->restaurant->update([
            'tax_calculation_mode' => 'exclusive',
            'rounding_method' => 'none',
        ]);

        $resultExclusive = $service->calculate(
            subtotal: 20000,
            channel: 'dine_in',
            paymentMethod: 'cash',
            restaurant: $this->restaurant
        );

        $this->assertEquals(2000, $resultExclusive['service_charge_amount']);
        $this->assertEquals(1000, $resultExclusive['tax_amount']); // 5% of 20,000 = 1,000 MMK
        $this->assertEquals(23000, $resultExclusive['final_total']); // 20,000 + 2,000 + 1,000

        // 3. Takeaway service charge exemption test:
        $resultTakeaway = $service->calculate(
            subtotal: 20000,
            channel: 'takeaway',
            paymentMethod: 'cash',
            restaurant: $this->restaurant
        );

        $this->assertEquals(0, $resultTakeaway['service_charge_amount']); // Exempt on takeaway!
        $this->assertEquals(500, $resultTakeaway['packaging_fee']); // 500 MMK packaging fee

        // 4. MMK Cash Rounding test (nearest 50 MMK):
        $this->restaurant->update([
            'rounding_method' => 'round_50',
            'rounding_direction' => 'nearest',
            'round_cash_only' => true,
        ]);

        $resultRounding = $service->calculate(
            subtotal: 21500,
            channel: 'dine_in',
            paymentMethod: 'cash',
            restaurant: $this->restaurant
        );

        // Pre-rounding: 21500 + 2150 (service) + 1075 (tax) = 24725 MMK
        // Nearest 50 of 24725 is 24750 MMK (+25 difference)
        $this->assertEquals(24750, $resultRounding['final_total']);
        $this->assertEquals(25, $resultRounding['rounding_difference']);
    }
}
