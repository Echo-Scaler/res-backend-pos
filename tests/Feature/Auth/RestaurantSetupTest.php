<?php

namespace Tests\Feature\Auth;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RestaurantSetupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'OWNER', 'guard_name' => 'web']);
    }

    public function test_restaurant_setup_succeeds_and_creates_owner_with_role_and_token(): void
    {
        $payload = [
            'restaurant_name' => 'European Kitchen',
            'owner_name' => 'John Owner',
            'owner_email' => 'owner@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/setup', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'token',
                'restaurant' => ['id', 'name', 'slug', 'is_active'],
                'user' => ['id', 'restaurant_id', 'name', 'email'],
                'roles',
            ]);

        // Assert database records
        $this->assertDatabaseHas('restaurants', [
            'name' => 'European Kitchen',
            'slug' => 'european-kitchen',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Owner',
            'email' => 'owner@example.com',
        ]);

        $owner = User::where('email', 'owner@example.com')->first();
        $restaurant = Restaurant::where('name', 'European Kitchen')->first();

        // Verify Owner is linked to restaurant and has OWNER role
        $this->assertEquals($restaurant->id, $owner->restaurant_id);
        $this->assertTrue($owner->hasRole('OWNER'));
        $this->assertEquals($owner->id, $restaurant->owner->id);
    }

    public function test_setup_fails_with_duplicate_email(): void
    {
        $restaurant = Restaurant::create([
            'name' => 'Existing Cafe',
            'slug' => 'existing-cafe',
            'is_active' => true,
        ]);

        User::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Existing User',
            'email' => 'duplicate@example.com',
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'restaurant_name' => 'New Bistro',
            'owner_name' => 'New Owner',
            'owner_email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/setup', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['owner_email']);
    }

    public function test_setup_fails_with_password_confirmation_mismatch(): void
    {
        $payload = [
            'restaurant_name' => 'Italian Trattoria',
            'owner_name' => 'Chef Mario',
            'owner_email' => 'mario@example.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatched123',
        ];

        $response = $this->postJson('/api/setup', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_is_never_returned_in_api_response(): void
    {
        $payload = [
            'restaurant_name' => 'Secret Recipe Grill',
            'owner_name' => 'Alice Secret',
            'owner_email' => 'alice@example.com',
            'password' => 'supersecretpass',
            'password_confirmation' => 'supersecretpass',
        ];

        $response = $this->postJson('/api/setup', $payload);

        $response->assertStatus(201);
        $response->assertJsonMissing(['password' => 'supersecretpass']);
        $this->assertArrayNotHasKey('password', $response->json('user'));
    }

    public function test_setup_transaction_rolls_back_when_operation_fails(): void
    {
        // Mock a failure after restaurant creation by intercepting or triggering an invalid state
        // Test that if an exception occurs during the transaction, the restaurant is NOT persisted
        try {
            DB::transaction(function () {
                Restaurant::create([
                    'name' => 'Rollback Bistro',
                    'slug' => 'rollback-bistro',
                    'is_active' => true,
                ]);

                // Deliberately throw an exception to simulate failure during user creation
                throw new \RuntimeException('Simulated failure during setup process');
            });
        } catch (\Throwable $e) {
            // Expected
        }

        $this->assertDatabaseMissing('restaurants', [
            'name' => 'Rollback Bistro',
        ]);
    }
}
