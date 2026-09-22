<?php

namespace Tests\Feature\Auth;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'OWNER', 'guard_name' => 'web']);

        $this->restaurant = Restaurant::create([
            'name' => 'European Kitchen',
            'slug' => 'european-kitchen',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'John Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->owner->assignRole('OWNER');
    }

    public function test_login_fails_with_invalid_credentials_returning_401(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'owner@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_login_succeeds_and_returns_token_user_restaurant_and_roles(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'owner@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'],
                'restaurant' => ['id', 'name'],
                'roles',
            ]);

        $this->assertContains('OWNER', $response->json('roles'));
        $this->assertEquals('European Kitchen', $response->json('restaurant.name'));
        $this->assertArrayNotHasKey('password', $response->json('user'));
    }

    public function test_me_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_me_endpoint_returns_authenticated_owner_and_restaurant(): void
    {
        $token = $this->owner->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'restaurant' => ['id', 'name'],
                'roles',
            ])
            ->assertJson([
                'user' => [
                    'id' => $this->owner->id,
                    'name' => 'John Owner',
                    'email' => 'owner@example.com',
                ],
                'restaurant' => [
                    'id' => $this->restaurant->id,
                    'name' => 'European Kitchen',
                ],
                'roles' => ['OWNER'],
            ]);
    }

    public function test_logout_revokes_current_token(): void
    {
        $token = $this->owner->createToken('logout-test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully.',
            ]);

        // Assert token was deleted from database
        $this->assertCount(0, $this->owner->fresh()->tokens);

        // Reset the auth guard state between requests in the test environment
        $this->app['auth']->forgetGuards();

        // Verify token is revoked: subsequent request with the same token should fail
        $subsequentResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me');

        $subsequentResponse->assertStatus(401);
    }
}
