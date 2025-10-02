<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'client',
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'client',
        ]);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertStatus(302);
        $this->assertGuest();
    }

    /** @test */
    public function user_can_request_password_reset()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    /** @test */
    public function user_can_reset_password()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = app('auth.password.broker')->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(302);
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function user_can_change_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        $this->actingAs($user);

        $response = $this->put('/user/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(302);
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function user_cannot_change_password_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        $this->actingAs($user);

        $response = $this->put('/user/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('current_password');
    }

    /** @test */
    public function user_can_enable_two_factor_authentication()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/user/two-factor-authentication');

        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->two_factor_secret);
    }

    /** @test */
    public function user_can_disable_two_factor_authentication()
    {
        $user = User::factory()->create([
            'two_factor_secret' => 'secret',
            'two_factor_confirmed_at' => now(),
        ]);
        $this->actingAs($user);

        $response = $this->delete('/user/two-factor-authentication');

        $response->assertStatus(200);
        $this->assertNull($user->fresh()->two_factor_secret);
    }

    /** @test */
    public function user_can_verify_two_factor_code()
    {
        $user = User::factory()->create([
            'two_factor_secret' => 'secret',
        ]);
        $this->actingAs($user);

        $response = $this->post('/user/two-factor-authentication/confirm', [
            'code' => '123456',
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->two_factor_confirmed_at);
    }

    /** @test */
    public function user_can_generate_recovery_codes()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/user/two-factor-recovery-codes');

        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->two_factor_recovery_codes);
    }

    /** @test */
    public function user_can_use_recovery_code()
    {
        $user = User::factory()->create([
            'two_factor_recovery_codes' => ['recovery-code-123'],
        ]);
        $this->actingAs($user);

        $response = $this->post('/user/two-factor-recovery-codes/use', [
            'code' => 'recovery-code-123',
        ]);

        $response->assertStatus(200);
        $this->assertNotContains('recovery-code-123', $user->fresh()->two_factor_recovery_codes);
    }

    /** @test */
    public function user_can_update_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put('/user/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
        ]);
    }

    /** @test */
    public function user_cannot_update_email_to_existing_email()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);
        $this->actingAs($user1);

        $response = $this->put('/user/profile', [
            'name' => $user1->name,
            'email' => 'user2@example.com',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function user_can_delete_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete('/user/profile', [
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /** @test */
    public function user_cannot_delete_account_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);

        $response = $this->delete('/user/profile', [
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /** @test */
    public function user_can_view_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/user/profile');

        $response->assertStatus(200);
        $response->assertViewIs('profile.show');
    }

    /** @test */
    public function guest_cannot_access_protected_routes()
    {
        $response = $this->get('/user/profile');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_access_admin_routes()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function non_admin_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'client']);
        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /** @test */
    public function staff_can_access_staff_routes()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);

        $response = $this->get('/staff/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function non_staff_cannot_access_staff_routes()
    {
        $user = User::factory()->create(['role' => 'client']);
        $this->actingAs($user);

        $response = $this->get('/staff/dashboard');
        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_view_login_page()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function user_can_view_register_page()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /** @test */
    public function user_can_view_forgot_password_page()
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    /** @test */
    public function user_can_view_reset_password_page()
    {
        $user = User::factory()->create();
        $token = app('auth.password.broker')->createToken($user);

        $response = $this->get('/reset-password/' . $token);
        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
    }

    /** @test */
    public function user_can_view_verify_email_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/email/verify');
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    /** @test */
    public function user_can_verify_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $this->actingAs($user);

        $response = $this->get('/email/verify/' . $user->id . '/' . sha1($user->email));
        $response->assertStatus(302);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /** @test */
    public function user_can_resend_verification_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $this->actingAs($user);

        $response = $this->post('/email/verification-notification');
        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    /** @test */
    public function user_can_view_two_factor_authentication_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/user/two-factor-authentication');
        $response->assertStatus(200);
        $response->assertViewIs('profile.two-factor-authentication');
    }

    /** @test */
    public function user_can_view_api_tokens_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/user/api-tokens');
        $response->assertStatus(200);
        $response->assertViewIs('profile.api-tokens');
    }

    /** @test */
    public function user_can_create_api_token()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/user/api-tokens', [
            'name' => 'Test Token',
            'abilities' => ['read'],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'Test Token',
        ]);
    }

    /** @test */
    public function user_can_delete_api_token()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $token = $user->createToken('Test Token');

        $response = $this->delete('/user/api-tokens/' . $token->accessToken->id);
        $response->assertStatus(302);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    /** @test */
    public function user_can_view_sessions_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/user/sessions');
        $response->assertStatus(200);
        $response->assertViewIs('profile.sessions');
    }

    /** @test */
    public function user_can_logout_other_sessions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/user/other-sessions/logout');
        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    /** @test */
    public function user_can_view_browser_sessions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/user/browser-sessions');
        $response->assertStatus(200);
        $response->assertViewIs('profile.browser-sessions');
    }

    /** @test */
    public function user_can_logout_browser_sessions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/user/browser-sessions/logout');
        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    /** @test */
    public function user_can_view_notifications_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/user/notifications');
        $response->assertStatus(200);
        $response->assertViewIs('profile.notifications');
    }

    /** @test */
    public function user_can_mark_notification_as_read()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $notification = $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->put('/user/notifications/' . $notification->id);
        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function user_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification 1'],
        ]);
        $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification 2'],
        ]);

        $response = $this->put('/user/notifications/mark-all-read');
        $response->assertStatus(200);
        
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    /** @test */
    public function user_can_delete_notification()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $notification = $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->delete('/user/notifications/' . $notification->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    /** @test */
    public function user_can_clear_all_notifications()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification 1'],
        ]);
        $user->notifications()->create([
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification 2'],
        ]);

        $response = $this->delete('/user/notifications/clear-all');
        $response->assertStatus(200);
        
        $this->assertEquals(0, $user->fresh()->notifications()->count());
    }
}
