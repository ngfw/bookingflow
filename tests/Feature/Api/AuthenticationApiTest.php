<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_via_api()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'client',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'created_at',
                    ],
                    'token',
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'client',
        ]);
    }

    /** @test */
    public function user_can_login_via_api()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                    ],
                    'token',
                ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Invalid credentials',
                ]);
    }

    /** @test */
    public function user_can_logout_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Logged out successfully',
                ]);
    }

    /** @test */
    public function user_can_get_profile_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                ]);
    }

    /** @test */
    public function user_can_update_profile_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '1234567890',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/user', $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'name' => 'Updated Name',
                    'phone' => '1234567890',
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '1234567890',
        ]);
    }

    /** @test */
    public function user_can_change_password_via_api()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/user/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Password changed successfully',
                ]);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function user_cannot_change_password_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/user/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['current_password']);
    }

    /** @test */
    public function user_can_create_api_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/tokens', [
            'name' => 'New Token',
            'abilities' => ['read', 'write'],
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'token' => [
                        'id',
                        'name',
                        'abilities',
                        'token',
                    ],
                ]);
    }

    /** @test */
    public function user_can_list_api_tokens()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $user->createToken('Token 1');
        $user->createToken('Token 2');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user/tokens');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'tokens' => [
                        '*' => [
                            'id',
                            'name',
                            'abilities',
                            'last_used_at',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function user_can_delete_api_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $tokenToDelete = $user->createToken('Token to Delete');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/user/tokens/' . $tokenToDelete->accessToken->id);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Token deleted successfully',
                ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenToDelete->accessToken->id,
        ]);
    }

    /** @test */
    public function user_can_get_notifications_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/notifications');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'notifications' => [
                        '*' => [
                            'id',
                            'type',
                            'data',
                            'read_at',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function user_can_mark_notification_as_read_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $notification = $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/notifications/' . $notification->id . '/read');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Notification marked as read',
                ]);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function user_can_delete_notification_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $notification = $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/notifications/' . $notification->id);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Notification deleted successfully',
                ]);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }

    /** @test */
    public function user_can_request_password_reset_via_api()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Password reset link sent to your email',
                ]);
    }

    /** @test */
    public function user_can_reset_password_via_api()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = app('auth.password.broker')->createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Password reset successfully',
                ]);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function user_can_verify_email_via_api()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/email/verify/' . $user->id . '/' . sha1($user->email));

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Email verified successfully',
                ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /** @test */
    public function user_can_resend_verification_email_via_api()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/email/verification-notification');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Verification email sent',
                ]);
    }

    /** @test */
    public function user_can_enable_two_factor_authentication_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/two-factor-authentication');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'qr_code',
                    'recovery_codes',
                ]);

        $this->assertNotNull($user->fresh()->two_factor_secret);
    }

    /** @test */
    public function user_can_disable_two_factor_authentication_via_api()
    {
        $user = User::factory()->create([
            'two_factor_secret' => 'secret',
            'two_factor_confirmed_at' => now(),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/user/two-factor-authentication');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Two-factor authentication disabled',
                ]);

        $this->assertNull($user->fresh()->two_factor_secret);
    }

    /** @test */
    public function user_can_confirm_two_factor_authentication_via_api()
    {
        $user = User::factory()->create([
            'two_factor_secret' => 'secret',
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/two-factor-authentication/confirm', [
            'code' => '123456',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Two-factor authentication confirmed',
                ]);

        $this->assertNotNull($user->fresh()->two_factor_confirmed_at);
    }

    /** @test */
    public function user_can_generate_recovery_codes_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/two-factor-recovery-codes');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'recovery_codes',
                ]);

        $this->assertNotNull($user->fresh()->two_factor_recovery_codes);
    }

    /** @test */
    public function user_can_use_recovery_code_via_api()
    {
        $user = User::factory()->create([
            'two_factor_recovery_codes' => ['recovery-code-123'],
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/two-factor-recovery-codes/use', [
            'code' => 'recovery-code-123',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Recovery code used successfully',
                ]);

        $this->assertNotContains('recovery-code-123', $user->fresh()->two_factor_recovery_codes);
    }

    /** @test */
    public function user_can_get_sessions_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user/sessions');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'sessions' => [
                        '*' => [
                            'id',
                            'ip_address',
                            'user_agent',
                            'last_activity',
                            'is_current',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function user_can_logout_other_sessions_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/other-sessions/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Other sessions logged out successfully',
                ]);
    }

    /** @test */
    public function user_can_delete_account_via_api()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/user', [
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Account deleted successfully',
                ]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /** @test */
    public function user_cannot_delete_account_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/user', [
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_endpoints()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated',
                ]);
    }

    /** @test */
    public function user_can_refresh_token_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/refresh-token');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'token',
                ]);
    }

    /** @test */
    public function user_can_get_token_info_via_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/token-info');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'token' => [
                        'id',
                        'name',
                        'abilities',
                        'last_used_at',
                        'created_at',
                    ],
                ]);
    }
}
