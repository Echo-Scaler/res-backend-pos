<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RestaurantSettingsTest extends TestCase
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
            'name' => 'Royal Mandalay Kitchen',
            'slug' => 'royal-mandalay-kitchen',
            'phone' => '09-450001111',
            'address' => '78th Street, Mandalay',
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

    public function test_owner_can_view_restaurant_settings_page(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.settings.restaurant'));

        $response->assertOk();
        $response->assertViewIs('admin.settings.restaurant');
        $response->assertSee('Restaurant Business Profile & Settings', false);
        $response->assertSee('Royal Mandalay Kitchen');
        $response->assertSee('09-450001111');
        $response->assertSee('MMK (Burmese Kyats)', false);
        $response->assertSee('Live 80mm Receipt Preview');
    }

    public function test_cashier_and_staff_are_forbidden_from_settings(): void
    {
        $responseCashier = $this->actingAs($this->cashier)->get(route('admin.settings.restaurant'));
        $responseCashier->assertForbidden();

        $responseStaff = $this->actingAs($this->staff)->get(route('admin.settings.restaurant'));
        $responseStaff->assertForbidden();

        $responseUpdate = $this->actingAs($this->cashier)->post(route('admin.settings.restaurant.update'), [
            'name' => 'Hacked Restaurant Name',
        ]);
        $responseUpdate->assertForbidden();
    }

    public function test_owner_can_update_settings_and_audit_trail_is_recorded(): void
    {
        $response = $this->actingAs($this->owner)->post(route('admin.settings.restaurant.update'), [
            'name' => 'Royal Mandalay Executive Bistro',
            'tagline' => 'Finest Traditional Cuisine',
            'phone' => '09-450002222',
            'secondary_phone' => '09-250003333',
            'email' => 'info@royalmandalay.com',
            'address' => 'No. 12, 78th Street, Mandalay',
            'city' => 'Mandalay',
            'tax_id' => 'CT-MDY-998877',
            'receipt_header' => '*** Mingalaba Royal Mandalay ***',
            'receipt_footer' => 'Thank You! WiFi: RM-Guest',
            'show_logo_on_receipt' => 1,
            'show_tax_id_on_receipt' => 1,
            'opening_time' => '08:00',
            'closing_time' => '23:00',
            'operating_days' => ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
            'is_dine_in_enabled' => 1,
            'is_takeaway_enabled' => 1,
            'is_qr_order_enabled' => 1,
            'default_tax_percent' => 5.0,
            'default_service_charge_percent' => 10.0,
            'printer_paper_width' => '80mm',
            'auto_kick_drawer' => 1,
            'auto_print_receipt' => 1,
        ]);

        $response->assertRedirect(route('admin.settings.restaurant'));
        $response->assertSessionHas('success');

        $this->restaurant->refresh();
        $this->assertEquals('Royal Mandalay Executive Bistro', $this->restaurant->name);
        $this->assertEquals('Finest Traditional Cuisine', $this->restaurant->tagline);
        $this->assertEquals('CT-MDY-998877', $this->restaurant->tax_id);
        $this->assertEquals('Mandalay', $this->restaurant->city);
        $this->assertEquals(5.0, $this->restaurant->default_tax_percent);
        $this->assertEquals(10.0, $this->restaurant->default_service_charge_percent);

        // Verify Audit Log is recorded
        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'SETTINGS_UPDATE',
            'category' => 'ACCESS_SECURITY',
            'severity' => 'WARNING',
            'user_id' => $this->owner->id,
        ]);
    }

    public function test_validation_rejects_invalid_inputs(): void
    {
        $response = $this->actingAs($this->owner)->post(route('admin.settings.restaurant.update'), [
            'name' => '', // required
            'default_tax_percent' => 150, // max 100
            'default_service_charge_percent' => -5, // min 0
            'printer_paper_width' => 'invalid_width',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'default_tax_percent',
            'default_service_charge_percent',
            'printer_paper_width',
        ]);
    }

    public function test_owner_can_upload_and_remove_brand_logo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('brand_logo.png', 200, 200);

        $response = $this->actingAs($this->owner)->post(route('admin.settings.restaurant.update'), [
            'name' => 'Royal Mandalay Kitchen',
            'logo' => $file,
        ]);

        $response->assertRedirect(route('admin.settings.restaurant'));

        $this->restaurant->refresh();
        $this->assertNotNull($this->restaurant->logo);
        $this->assertTrue(Storage::disk('public')->exists($this->restaurant->logo));
        $this->assertStringContainsString('storage/logos/', $this->restaurant->logo_url);

        // Now remove the logo
        $responseRemove = $this->actingAs($this->owner)->post(route('admin.settings.restaurant.update'), [
            'name' => 'Royal Mandalay Kitchen',
            'remove_logo' => 1,
        ]);

        $responseRemove->assertRedirect(route('admin.settings.restaurant'));

        $this->restaurant->refresh();
        $this->assertNull($this->restaurant->logo);
    }

    public function test_api_endpoint_returns_json_resource_with_mmk_currency(): void
    {
        $response = $this->actingAs($this->owner)->getJson(route('admin.settings.restaurant.data'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'currency',
                'currency_symbol',
                'default_tax_percent',
                'default_service_charge_percent',
                'printer_paper_width',
                'is_dine_in_enabled',
                'is_takeaway_enabled',
                'is_qr_order_enabled',
                'auto_kick_drawer',
                'auto_print_receipt',
            ],
        ]);

        $response->assertJsonPath('data.currency', 'MMK');
        $response->assertJsonPath('data.currency_symbol', 'Ks ');
        $response->assertJsonPath('data.name', 'Royal Mandalay Kitchen');
    }
}
