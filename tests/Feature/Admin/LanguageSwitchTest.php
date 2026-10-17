<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LanguageSwitchTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $restaurant = Restaurant::create([
            'name' => 'Royal Mandalay Cuisine',
            'code' => 'RMC-YGN',
            'slug' => 'royal-mandalay-cuisine',
        ]);

        $this->owner = User::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Owner Ko Aung',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');
    }

    public function test_can_switch_language_to_myanmar(): void
    {
        $response = $this->actingAs($this->owner)->get(route('lang.switch', 'my'));

        $response->assertSessionHas('locale', 'my');

        // Check that following requests have Myanmar locale
        $dashboardResponse = $this->actingAs($this->owner)->withSession(['locale' => 'my'])->get(route('admin.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('ပင်မစာမျက်နှာ');
        $dashboardResponse->assertSee('ကုန်ပစ္စည်းစာရင်း');
    }

    public function test_can_switch_language_to_english(): void
    {
        $response = $this->actingAs($this->owner)->get(route('lang.switch', 'en'));

        $response->assertSessionHas('locale', 'en');

        // Check that following requests have English locale
        $dashboardResponse = $this->actingAs($this->owner)->withSession(['locale' => 'en'])->get(route('admin.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Dashboard');
        $dashboardResponse->assertSee('Inventory Stock');
    }

    public function test_invalid_locale_falls_back_to_english(): void
    {
        $response = $this->actingAs($this->owner)->get(route('lang.switch', 'invalid_lang'));

        $response->assertSessionHas('locale', 'en');
    }
}
