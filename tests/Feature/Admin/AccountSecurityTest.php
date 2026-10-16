<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\TwoFactorAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    protected User $owner;

    protected User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::create([
            'name' => 'Royal Mandalay Kitchen',
            'slug' => 'royal-mandalay-kitchen',
            'currency' => 'MMK',
        ]);

        $ownerRole = Role::firstOrCreate(['name' => 'OWNER', 'guard_name' => 'web']);
        $cashierRole = Role::firstOrCreate(['name' => 'CASHIER', 'guard_name' => 'web']);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ko Aung Myint (Owner)',
            'email' => 'owner@royalkitchen.mm',
            'phone' => '0912345678',
            'password' => Hash::make('Secret1234!'),
            'pin_code' => Hash::make('1234'),
        ]);
        $this->owner->assignRole($ownerRole);

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ma Thida (Cashier)',
            'email' => 'cashier@royalkitchen.mm',
            'phone' => '0987654321',
            'password' => Hash::make('Cashier123!'),
            'pin_code' => Hash::make('9999'),
        ]);
        $this->cashier->assignRole($cashierRole);
    }

    public function test_owner_can_view_account_security_dashboard(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('admin.account.security'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.account.security');
        $response->assertSee('Account Governance & Security Hub');
        $response->assertSee('Change Master Password');
        $response->assertSee('Counter Master Approval PIN');
        $response->assertSee('Active POS Terminals');
        $response->assertSee('Two-Factor Authentication (2FA)');
        $response->assertSee('Download SQL Disaster Backup');
    }

    public function test_cashier_and_unauthorized_roles_are_forbidden(): void
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('admin.account.security'));

        $response->assertStatus(403);
    }

    public function test_owner_can_view_security_data_via_json_resource(): void
    {
        $response = $this->actingAs($this->owner)
            ->getJson(route('admin.account.security'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'has_pin_code',
                'two_factor_enabled',
                'security_score',
                'security_level',
                'sessions',
                'active_sessions_count',
            ],
        ]);

        $responseData = $response->json('data');
        $this->assertTrue($responseData['has_pin_code']);
        $this->assertFalse($responseData['two_factor_enabled']);
        // Verify secrets are never exposed in JSON
        $this->assertArrayNotHasKey('password', $responseData);
        $this->assertArrayNotHasKey('pin_code', $responseData);
        $this->assertArrayNotHasKey('two_factor_secret', $responseData);
    }

    public function test_owner_can_update_master_password_with_correct_current_password(): void
    {
        $response = $this->actingAs($this->owner)
            ->put(route('admin.account.security.password'), [
                'current_password' => 'Secret1234!',
                'password' => 'NewStrongPass2026!',
                'password_confirmation' => 'NewStrongPass2026!',
            ]);

        $response->assertRedirect(route('admin.account.security'));
        $response->assertSessionHas('success');

        $this->owner->refresh();
        $this->assertTrue(Hash::check('NewStrongPass2026!', $this->owner->password));
        $this->assertNotNull($this->owner->password_changed_at);

        // Verify audit log entry was created
        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'MASTER_PASSWORD_CHANGED',
            'category' => AuditLog::CATEGORY_ACCESS_SECURITY,
        ]);
    }

    public function test_owner_cannot_update_password_with_incorrect_current_password(): void
    {
        $response = $this->actingAs($this->owner)
            ->put(route('admin.account.security.password'), [
                'current_password' => 'WrongPassword!',
                'password' => 'NewStrongPass2026!',
                'password_confirmation' => 'NewStrongPass2026!',
            ]);

        $response->assertSessionHasErrors('current_password');
        $this->owner->refresh();
        $this->assertTrue(Hash::check('Secret1234!', $this->owner->password));
    }

    public function test_owner_can_update_master_approval_pin(): void
    {
        $response = $this->actingAs($this->owner)
            ->put(route('admin.account.security.pin'), [
                'current_password' => 'Secret1234!',
                'pin_code' => '8492',
                'pin_code_confirmation' => '8492',
            ]);

        $response->assertRedirect(route('admin.account.security'));
        $response->assertSessionHas('success');

        $this->owner->refresh();
        $this->assertTrue(Hash::check('8492', $this->owner->pin_code));

        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'MASTER_PIN_CHANGED',
            'category' => AuditLog::CATEGORY_ACCESS_SECURITY,
        ]);
    }

    public function test_owner_can_setup_and_activate_two_factor_authentication(): void
    {
        // 1. Initiate 2FA
        $initResponse = $this->actingAs($this->owner)
            ->post(route('admin.account.security.2fa.enable'));

        $initResponse->assertRedirect(route('admin.account.security'));
        $secret = session('staging_2fa_secret');
        $this->assertNotEmpty($secret);

        // 2. Compute valid TOTP code
        $validCode = TwoFactorAuthService::calculateTotp($secret);

        // 3. Confirm 2FA with valid OTP code
        $confirmResponse = $this->actingAs($this->owner)
            ->post(route('admin.account.security.2fa.confirm'), [
                'code' => $validCode,
            ]);

        $confirmResponse->assertRedirect(route('admin.account.security'));
        $confirmResponse->assertSessionHas('success');

        $this->owner->refresh();
        $this->assertTrue($this->owner->hasTwoFactorEnabled());
        $this->assertNotNull($this->owner->two_factor_confirmed_at);

        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'TWO_FACTOR_ACTIVATED',
            'category' => AuditLog::CATEGORY_ACCESS_SECURITY,
        ]);
    }

    public function test_owner_can_disable_two_factor_authentication(): void
    {
        // Pre-enable 2FA
        $secret = TwoFactorAuthService::generateSecretKey();
        $this->owner->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode(['TEST-1234'])),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->assertTrue($this->owner->hasTwoFactorEnabled());

        // Disable 2FA
        $response = $this->actingAs($this->owner)
            ->delete(route('admin.account.security.2fa.disable'));

        $response->assertRedirect(route('admin.account.security'));
        $response->assertSessionHas('success');

        $this->owner->refresh();
        $this->assertFalse($this->owner->hasTwoFactorEnabled());
        $this->assertNull($this->owner->two_factor_confirmed_at);

        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'TWO_FACTOR_DEACTIVATED',
            'category' => AuditLog::CATEGORY_ACCESS_SECURITY,
        ]);
    }

    public function test_owner_can_revoke_remote_session_and_logout_other_devices(): void
    {
        $otherSessionId = 'test_remote_session_terminal_101';
        DB::table('sessions')->insert([
            'id' => $otherSessionId,
            'user_id' => $this->owner->id,
            'ip_address' => '192.168.1.101',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
            'payload' => serialize(['test' => true]),
            'last_activity' => time() - 300,
        ]);

        $this->assertDatabaseHas('sessions', ['id' => $otherSessionId]);

        // Revoke single session
        $response = $this->actingAs($this->owner)
            ->delete(route('admin.account.security.sessions.revoke', $otherSessionId));

        $response->assertRedirect(route('admin.account.security'));
        $this->assertDatabaseMissing('sessions', ['id' => $otherSessionId]);

        // Add 2 more sessions to test logout all others
        DB::table('sessions')->insert([
            ['id' => 'sess_pad_1', 'user_id' => $this->owner->id, 'ip_address' => '192.168.1.102', 'user_agent' => 'Android Tablet', 'payload' => 'x', 'last_activity' => time()],
            ['id' => 'sess_pad_2', 'user_id' => $this->owner->id, 'ip_address' => '192.168.1.103', 'user_agent' => 'iPhone iOS', 'payload' => 'y', 'last_activity' => time()],
        ]);

        $logoutResponse = $this->actingAs($this->owner)
            ->post(route('admin.account.security.sessions.logout-others'));

        $logoutResponse->assertRedirect(route('admin.account.security'));
        $this->assertDatabaseMissing('sessions', ['id' => 'sess_pad_1']);
        $this->assertDatabaseMissing('sessions', ['id' => 'sess_pad_2']);
    }

    public function test_owner_can_download_database_disaster_recovery_backup(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('admin.account.security.backup.download'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/sql');
        $this->assertStringContainsString('attachment; filename="pos_backup_', $response->headers->get('Content-Disposition') ?? '');
        $this->assertStringContainsString('RESTAURANT POS DISASTER RECOVERY BACKUP', $response->getContent() ?: '');

        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'DATABASE_DISASTER_BACKUP_DOWNLOADED',
            'category' => AuditLog::CATEGORY_ACCESS_SECURITY,
        ]);
    }
}
