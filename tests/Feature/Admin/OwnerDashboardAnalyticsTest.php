<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OwnerDashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    private User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Golden Palace Executive',
            'slug' => 'golden-palace-exec',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'U Hla - Executive Owner',
            'email' => 'owner.exec@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ma Su - Cashier',
            'email' => 'cashier.exec@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');
    }

    public function test_owner_dashboard_renders_all_ten_required_analytics_metrics(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');

        // 1. Today's sales
        $response->assertSee("Today's Sales", false);
        $response->assertSee('1,450,000');

        // 2. Today's orders
        $response->assertSee("Today's Orders", false);
        $response->assertSee('86');

        // 3. Average order value (AOV)
        $response->assertSee('Average Order Value');
        $response->assertSee('16,860');

        // 4. Payment breakdown
        $response->assertSee('Payment Breakdown');
        $response->assertSee('KBZPay QR');
        $response->assertSee('Cash');
        $response->assertSee('WavePay');

        // 5. Best-selling products
        $response->assertSee('Best-Selling Dishes & Products', false);
        $response->assertSee('Shan Noodle Special');
        $response->assertSee('Kyay Oh Sikyet');

        // 6. Low-stock items
        $response->assertSee('Low-Stock Alerts');
        $response->assertSee('Cooking Oil');
        $response->assertSee('Fresh Chicken Breast');

        // 7. Cancelled / refunded orders
        $response->assertSee('Cancelled / Refunded', false);
        $response->assertSee('#ORD-1082');

        // 8. Staff activity
        $response->assertSee('Real-Time Staff Activity');
        $response->assertSee('U Hla - Executive Owner');

        // 9. Sales by date
        $response->assertSee('Sales by Date');

        // 10. Sales by category
        $response->assertSee('Sales by Category');
        $response->assertSee('Main Dishes');
        $response->assertSee('Beverages');
    }

    public function test_owner_can_access_all_seventeen_back_office_module_routes(): void
    {
        $routes = [
            'admin.dashboard',
            'admin.settings.restaurant',
            'admin.employees.index',
            'admin.roles.permissions',
            'admin.menu.index',
            'admin.inventory.index',
            'admin.tables.index',
            'admin.orders.index',
            'admin.payments.index',
            'admin.customers.index',
            'admin.promotions.index',
            'admin.reports.index',
            'admin.expenses.index',
            'admin.settings.tax',
            'admin.settings.business',
            'admin.audit.logs',
            'admin.account.security',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($this->owner)->get(route($routeName));
            $response->assertOk();
        }
    }

    public function test_cashier_is_forbidden_from_owner_modules(): void
    {
        $restrictedRoutes = [
            'admin.dashboard',
            'admin.settings.restaurant',
            'admin.roles.permissions',
            'admin.inventory.index',
            'admin.reports.index',
            'admin.expenses.index',
            'admin.settings.tax',
            'admin.settings.business',
            'admin.audit.logs',
            'admin.account.security',
        ];

        foreach ($restrictedRoutes as $routeName) {
            $response = $this->actingAs($this->cashier)->get(route($routeName));
            $response->assertForbidden();
        }
    }
}
