<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\ReportAnalyticsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $manager;

    protected User $staff;

    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Tokyo Sakura Bistro',
            'slug' => 'tokyo-sakura-bistro',
            'phone' => '+81312345678',
            'email' => 'contact@tokyosakura.jp',
            'address' => 'Shibuya 1-2-3, Tokyo',
            'is_active' => true,
        ]);

        $this->owner = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Bistro Owner',
            'email' => 'owner@tokyosakura.jp',
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Floor Manager',
            'email' => 'manager@tokyosakura.jp',
        ]);
        $this->manager->assignRole('MANAGER');

        $this->staff = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Service Staff',
            'email' => 'staff@tokyosakura.jp',
        ]);
        $this->staff->assignRole('STAFF');

        $this->seed(ReportAnalyticsSeeder::class);
    }

    public function test_guest_is_redirected_from_reports(): void
    {
        $response = $this->get('/admin/reports');
        $response->assertRedirect('/admin/login');
    }

    public function test_owner_can_access_reports_dashboard(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/reports');

        $response->assertStatus(200);
        $response->assertSee('Reports');
        $response->assertSee('Overview');
        $response->assertSee('Net Sales');
        $response->assertSee('Average Order Value');
    }

    public function test_reports_period_filter_applies_correctly(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/reports?period=yesterday');

        $response->assertStatus(200);
        $response->assertSee('Yesterday');
    }

    public function test_reports_tabs_render_domains_properly(): void
    {
        // COGS tab
        $cogsResponse = $this->actingAs($this->owner)->get('/admin/reports?tab=cogs');
        $cogsResponse->assertStatus(200);
        $cogsResponse->assertSee('Food Cost / COGS');
        $cogsResponse->assertSee('Beef Steak');

        // P&L tab
        $pnlResponse = $this->actingAs($this->owner)->get('/admin/reports?tab=pnl');
        $pnlResponse->assertStatus(200);
        $pnlResponse->assertSee('Operating Expenses');
        $pnlResponse->assertSee('Estimated Net Profit');

        // Table Analytics tab
        $tablesResponse = $this->actingAs($this->owner)->get('/admin/reports?tab=tables');
        $tablesResponse->assertStatus(200);
        $tablesResponse->assertSee('Table-Level Performance Breakdown');

        // Voids tab
        $voidsResponse = $this->actingAs($this->owner)->get('/admin/reports?tab=voids');
        $voidsResponse->assertStatus(200);
        $voidsResponse->assertSee('Void Audit Analysis');
    }

    public function test_reports_csv_export_returns_streamed_file(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/reports/export?period=today');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'attachment; filename='));
    }

    public function test_reports_csv_export_sales_tab_returns_category_breakdown_csv(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/reports/export?tab=sales&period=today');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'sales_today_'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Sales by Category & Product Mix Report', $content);
        $this->assertStringContainsString('Main Dishes & Steaks', $content);
        $this->assertStringContainsString('Alcohol & Cocktails', $content);
        $this->assertStringContainsString('TOTAL / SUMMARY', $content);
    }

    public function test_reports_csv_export_cogs_tab_returns_food_cost_recipe_csv(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/reports/export?tab=cogs&period=today');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'cogs_today_'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Food Cost & COGS Recipe Analysis Report', $content);
        $this->assertStringContainsString('Beef Steak', $content);
        $this->assertStringContainsString('Prime Beef', $content);
    }

    public function test_reports_ajax_data_returns_json_resource(): void
    {
        $response = $this->actingAs($this->owner)->getJson('/admin/reports/data?period=today');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'period_label',
                'kpis' => [
                    'net_sales',
                    'total_orders',
                    'average_order_value',
                    'total_customers',
                ],
                'today_overview',
                'daily_breakdown',
            ],
        ]);
    }

    public function test_sanctum_api_endpoints_return_successful_analytics(): void
    {
        $response = $this->actingAs($this->owner, 'sanctum')->getJson('/api/v1/reports/analytics');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'kpis',
                'today_overview',
            ],
        ]);
    }
}
