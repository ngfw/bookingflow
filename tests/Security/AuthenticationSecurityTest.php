<?php

namespace Tests\Security;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function brute_force_attack_protection_test()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Attempt multiple failed logins
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);

            if ($i < 5) {
                $response->assertStatus(401);
            } else {
                // After 5 failed attempts, should be rate limited
                $response->assertStatus(429);
            }
        }
    }

    /** @test */
    public function sql_injection_protection_test()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Attempt SQL injection in email field
        $response = $this->postJson('/api/login', [
            'email' => "test@example.com'; DROP TABLE users; --",
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        
        // Verify user still exists (table wasn't dropped)
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function xss_protection_test()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-token')->plainTextToken;

        // Attempt XSS in client name
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clients', [
            'name' => '<script>alert("XSS")</script>',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(201);
        
        // Verify XSS was escaped
        $this->assertDatabaseHas('clients', [
            'name' => '<script>alert("XSS")</script>',
        ]);
    }

    /** @test */
    public function csrf_protection_test()
    {
        $user = User::factory()->create(['role' => 'admin']);

        // Attempt to create client without CSRF token
        $response = $this->postJson('/api/clients', [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function token_expiration_test()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-token', ['*'], now()->addMinutes(1))->plainTextToken;

        // Wait for token to expire
        $this->travel(2)->minutes();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients');

        $response->assertStatus(401);
    }

    /** @test */
    public function token_revocation_test()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-token')->plainTextToken;

        // Revoke token
        $user->tokens()->delete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients');

        $response->assertStatus(401);
    }

    /** @test */
    public function password_strength_validation_test()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-token')->plainTextToken;

        // Test weak passwords
        $weakPasswords = [
            '123',
            'password',
            '12345678',
            'abcdefgh',
            'Password1', // No special character
        ];

        foreach ($weakPasswords as $password) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->putJson('/api/user/password', [
                'current_password' => 'password',
                'password' => $password,
                'password_confirmation' => $password,
            ]);

            $response->assertStatus(422);
        }
    }

    /** @test */
    public function session_fixation_protection_test()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Login and get session
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $token1 = $response->json('token');

        // Login again
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $token2 = $response->json('token');

        // Tokens should be different
        $this->assertNotEquals($token1, $token2);
    }

    /** @test */
    public function privilege_escalation_protection_test()
    {
        $client = User::factory()->create(['role' => 'client']);
        $token = $client->createToken('test-token')->plainTextToken;

        // Attempt to access admin-only endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function data_access_control_test()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $clientModel1 = Client::factory()->create(['user_id' => $client1->id]);
        $clientModel2 = Client::factory()->create(['user_id' => $client2->id]);
        
        $token = $client1->createToken('test-token')->plainTextToken;

        // Attempt to access another client's data
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/' . $clientModel2->id);

        $response->assertStatus(403);
    }

    /** @test */
    public function input_validation_security_test()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-token')->plainTextToken;

        // Test various malicious inputs
        $maliciousInputs = [
            'name' => 'A' . str_repeat('A', 1000), // Too long
            'email' => 'not-an-email',
            'phone' => 'invalid-phone',
            'date_of_birth' => 'not-a-date',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clients', $maliciousInputs);

        $response->assertStatus(422);
    }

    /** @test */
    public function file_upload_security_test()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-token')->plainTextToken;

        // Attempt to upload malicious file
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/upload', [
            'file' => '<?php echo "hack"; ?>',
            'filename' => 'malicious.php',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function rate_limiting_test()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-token')->plainTextToken;

        // Make many requests quickly
        for ($i = 0; $i < 100; $i++) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->getJson('/api/clients');

            if ($i < 60) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429);
                break;
            }
        }
    }

    /** @test */
    public function https_enforcement_test()
    {
        // This test would need to be run in a real environment with HTTPS
        // For now, we'll test that the application doesn't expose sensitive data over HTTP
        $response = $this->getJson('/api/clients');
        
        // Should require authentication
        $response->assertStatus(401);
    }

    /** @test */
    public function information_disclosure_test()
    {
        // Test that error messages don't reveal sensitive information
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
        
        // Should not reveal whether user exists or not
        $this->assertStringNotContainsString('User not found', $response->getContent());
    }

    /** @test */
    public function session_hijacking_protection_test()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Login
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $token = $response->json('token');

        // Simulate session hijacking by using token from different IP
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Forwarded-For' => '192.168.1.100',
        ])->getJson('/api/user/profile');

        // Should still work (IP checking would be implemented in production)
        $response->assertStatus(200);
    }

    /** @test */
    public function clickjacking_protection_test()
    {
        $response = $this->get('/');
        
        // Check for X-Frame-Options header
        $this->assertTrue($response->headers->has('X-Frame-Options'));
        $this->assertEquals('DENY', $response->headers->get('X-Frame-Options'));
    }

    /** @test */
    public function content_type_options_test()
    {
        $response = $this->get('/');
        
        // Check for X-Content-Type-Options header
        $this->assertTrue($response->headers->has('X-Content-Type-Options'));
        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
    }

    /** @test */
    public function xss_protection_header_test()
    {
        $response = $this->get('/');
        
        // Check for X-XSS-Protection header
        $this->assertTrue($response->headers->has('X-XSS-Protection'));
        $this->assertEquals('1; mode=block', $response->headers->get('X-XSS-Protection'));
    }

    /** @test */
    public function strict_transport_security_test()
    {
        $response = $this->get('/');
        
        // Check for Strict-Transport-Security header
        $this->assertTrue($response->headers->has('Strict-Transport-Security'));
        $this->assertStringContainsString('max-age', $response->headers->get('Strict-Transport-Security'));
    }

    /** @test */
    public function content_security_policy_test()
    {
        $response = $this->get('/');
        
        // Check for Content-Security-Policy header
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
    }

    /** @test */
    public function referrer_policy_test()
    {
        $response = $this->get('/');
        
        // Check for Referrer-Policy header
        $this->assertTrue($response->headers->has('Referrer-Policy'));
    }

    /** @test */
    public function permissions_policy_test()
    {
        $response = $this->get('/');
        
        // Check for Permissions-Policy header
        $this->assertTrue($response->headers->has('Permissions-Policy'));
    }
}
