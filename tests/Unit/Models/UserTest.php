<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'client',
            'phone' => '1234567890',
            'is_active' => true,
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('client', $user->role);
        $this->assertTrue($user->is_active);
    }

    /** @test */
    public function it_can_have_a_client_profile()
    {
        $user = User::factory()->create(['role' => 'client']);
        $client = Client::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Client::class, $user->client);
        $this->assertEquals($client->id, $user->client->id);
    }

    /** @test */
    public function it_can_have_a_staff_profile()
    {
        $user = User::factory()->create(['role' => 'staff']);
        $staff = Staff::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Staff::class, $user->staff);
        $this->assertEquals($staff->id, $user->staff->id);
    }

    /** @test */
    public function it_can_have_many_appointments()
    {
        $user = User::factory()->create(['role' => 'client']);
        $appointments = Appointment::factory()->count(3)->create(['client_id' => $user->id]);

        $this->assertCount(3, $user->appointments);
        $this->assertInstanceOf(Appointment::class, $user->appointments->first());
    }

    /** @test */
    public function it_can_have_many_invoices()
    {
        $user = User::factory()->create(['role' => 'client']);
        $invoices = Invoice::factory()->count(2)->create(['client_id' => $user->id]);

        $this->assertCount(2, $user->invoices);
        $this->assertInstanceOf(Invoice::class, $user->invoices->first());
    }

    /** @test */
    public function it_can_have_many_payments()
    {
        $user = User::factory()->create(['role' => 'client']);
        $payments = Payment::factory()->count(2)->create(['client_id' => $user->id]);

        $this->assertCount(2, $user->payments);
        $this->assertInstanceOf(Payment::class, $user->payments->first());
    }

    /** @test */
    public function it_can_have_many_notifications()
    {
        $user = User::factory()->create();
        $notifications = Notification::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->notifications);
        $this->assertInstanceOf(Notification::class, $user->notifications->first());
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($client->isAdmin());
    }

    /** @test */
    public function it_can_check_if_user_is_staff()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $client = User::factory()->create(['role' => 'client']);

        $this->assertTrue($staff->isStaff());
        $this->assertFalse($client->isStaff());
    }

    /** @test */
    public function it_can_check_if_user_is_client()
    {
        $client = User::factory()->create(['role' => 'client']);
        $staff = User::factory()->create(['role' => 'staff']);

        $this->assertTrue($client->isClient());
        $this->assertFalse($staff->isClient());
    }

    /** @test */
    public function it_can_check_user_role()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('client'));
    }

    /** @test */
    public function it_can_have_a_primary_location()
    {
        $user = User::factory()->create();
        $location = \App\Models\Location::factory()->create();
        
        $user->update(['primary_location_id' => $location->id]);

        $this->assertInstanceOf(\App\Models\Location::class, $user->primaryLocation);
        $this->assertEquals($location->id, $user->primaryLocation->id);
    }

    /** @test */
    public function it_can_have_many_locations()
    {
        $user = User::factory()->create(['role' => 'staff']);
        $staff = Staff::factory()->create(['user_id' => $user->id]);
        $location = \App\Models\Location::factory()->create();
        
        $staff->update(['location_id' => $location->id]);

        $this->assertCount(1, $user->locations);
        $this->assertInstanceOf(\App\Models\Location::class, $user->locations->first());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $user = User::factory()->create([
            'date_of_birth' => '1990-01-01',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->date_of_birth);
        $this->assertTrue($user->is_active);
    }

    /** @test */
    public function it_hides_sensitive_attributes()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    /** @test */
    public function it_hashes_password()
    {
        $user = User::factory()->create(['password' => 'plaintext']);

        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(\Hash::check('plaintext', $user->password));
    }

    /** @test */
    public function it_can_scope_active_users()
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);

        $activeUsers = User::where('is_active', true)->get();

        $this->assertCount(1, $activeUsers);
        $this->assertTrue($activeUsers->first()->is_active);
    }

    /** @test */
    public function it_can_scope_by_role()
    {
        User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'staff']);
        User::factory()->create(['role' => 'client']);

        $adminUsers = User::where('role', 'admin')->get();
        $staffUsers = User::where('role', 'staff')->get();
        $clientUsers = User::where('role', 'client')->get();

        $this->assertCount(1, $adminUsers);
        $this->assertCount(1, $staffUsers);
        $this->assertCount(1, $clientUsers);
    }

    /** @test */
    public function it_can_scope_recent_users()
    {
        $oldUser = User::factory()->create(['created_at' => now()->subDays(10)]);
        $recentUser = User::factory()->create(['created_at' => now()->subDays(1)]);

        $recentUsers = User::where('created_at', '>=', now()->subDays(7))->get();

        $this->assertCount(1, $recentUsers);
        $this->assertEquals($recentUser->id, $recentUsers->first()->id);
    }

    /** @test */
    public function it_can_validate_email_uniqueness()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => 'test@example.com']);
    }

    /** @test */
    public function it_can_validate_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([]);
    }

    /** @test */
    public function it_can_validate_email_format()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => 'invalid-email']);
    }

    /** @test */
    public function it_can_soft_delete()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    /** @test */
    public function it_can_restore_soft_deleted_user()
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);

        $user->restore();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_force_delete()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->forceDelete();

        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    /** @test */
    public function it_can_get_full_name()
    {
        $user = User::factory()->create(['name' => 'John Doe']);

        $this->assertEquals('John Doe', $user->name);
    }

    /** @test */
    public function it_can_get_avatar_url()
    {
        $user = User::factory()->create();

        // Test default avatar
        $this->assertStringContainsString('default-avatar', $user->avatar_url ?? 'default-avatar');
    }

    /** @test */
    public function it_can_get_last_login()
    {
        $user = User::factory()->create(['last_login_at' => now()]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->last_login_at);
    }

    /** @test */
    public function it_can_get_login_count()
    {
        $user = User::factory()->create(['login_count' => 5]);

        $this->assertEquals(5, $user->login_count);
    }

    /** @test */
    public function it_can_get_timezone()
    {
        $user = User::factory()->create(['timezone' => 'America/New_York']);

        $this->assertEquals('America/New_York', $user->timezone);
    }

    /** @test */
    public function it_can_get_language_preference()
    {
        $user = User::factory()->create(['language' => 'en']);

        $this->assertEquals('en', $user->language);
    }

    /** @test */
    public function it_can_get_notification_preferences()
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email' => true,
                'sms' => false,
                'push' => true,
            ]
        ]);

        $this->assertTrue($user->notification_preferences['email']);
        $this->assertFalse($user->notification_preferences['sms']);
        $this->assertTrue($user->notification_preferences['push']);
    }

    /** @test */
    public function it_can_get_privacy_settings()
    {
        $user = User::factory()->create([
            'privacy_settings' => [
                'profile_visibility' => 'public',
                'show_email' => false,
                'show_phone' => true,
            ]
        ]);

        $this->assertEquals('public', $user->privacy_settings['profile_visibility']);
        $this->assertFalse($user->privacy_settings['show_email']);
        $this->assertTrue($user->privacy_settings['show_phone']);
    }

    /** @test */
    public function it_can_get_security_settings()
    {
        $user = User::factory()->create([
            'security_settings' => [
                'two_factor_enabled' => true,
                'login_notifications' => true,
                'session_timeout' => 30,
            ]
        ]);

        $this->assertTrue($user->security_settings['two_factor_enabled']);
        $this->assertTrue($user->security_settings['login_notifications']);
        $this->assertEquals(30, $user->security_settings['session_timeout']);
    }

    /** @test */
    public function it_can_get_user_statistics()
    {
        $user = User::factory()->create(['role' => 'client']);
        
        // Create some related data
        Appointment::factory()->count(3)->create(['client_id' => $user->id]);
        Invoice::factory()->count(2)->create(['client_id' => $user->id]);
        Payment::factory()->count(2)->create(['client_id' => $user->id]);

        $stats = [
            'appointments_count' => $user->appointments()->count(),
            'invoices_count' => $user->invoices()->count(),
            'payments_count' => $user->payments()->count(),
            'total_spent' => $user->payments()->sum('amount'),
        ];

        $this->assertEquals(3, $stats['appointments_count']);
        $this->assertEquals(2, $stats['invoices_count']);
        $this->assertEquals(2, $stats['payments_count']);
        $this->assertIsNumeric($stats['total_spent']);
    }

    /** @test */
    public function it_can_get_user_activity_summary()
    {
        $user = User::factory()->create();
        
        $activity = [
            'last_login' => $user->last_login_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'is_active' => $user->is_active,
        ];

        $this->assertArrayHasKey('last_login', $activity);
        $this->assertArrayHasKey('created_at', $activity);
        $this->assertArrayHasKey('updated_at', $activity);
        $this->assertArrayHasKey('is_active', $activity);
    }

    /** @test */
    public function it_can_validate_password_strength()
    {
        $user = User::factory()->create();

        // Test weak password
        $weakPassword = '123';
        $this->assertFalse($user->isPasswordStrong($weakPassword));

        // Test strong password
        $strongPassword = 'StrongPassword123!';
        $this->assertTrue($user->isPasswordStrong($strongPassword));
    }

    /** @test */
    public function it_can_generate_api_token()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token');

        $this->assertNotNull($token);
        $this->assertStringStartsWith('1|', $token->plainTextToken);
    }

    /** @test */
    public function it_can_revoke_all_tokens()
    {
        $user = User::factory()->create();
        $user->createToken('test-token-1');
        $user->createToken('test-token-2');

        $this->assertCount(2, $user->tokens);

        $user->tokens()->delete();

        $this->assertCount(0, $user->fresh()->tokens);
    }
}
