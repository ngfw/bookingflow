<?php

namespace Tests\Security;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Location;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

class AuthorizationSecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function role_based_access_control_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $client = User::factory()->create(['role' => 'client']);

        // Test admin access
        $this->assertTrue(Gate::forUser($admin)->allows('view-admin-dashboard'));
        $this->assertTrue(Gate::forUser($admin)->allows('manage-users'));
        $this->assertTrue(Gate::forUser($admin)->allows('view-reports'));

        // Test staff access
        $this->assertFalse(Gate::forUser($staff)->allows('view-admin-dashboard'));
        $this->assertFalse(Gate::forUser($staff)->allows('manage-users'));
        $this->assertTrue(Gate::forUser($staff)->allows('view-reports'));

        // Test client access
        $this->assertFalse(Gate::forUser($client)->allows('view-admin-dashboard'));
        $this->assertFalse(Gate::forUser($client)->allows('manage-users'));
        $this->assertFalse(Gate::forUser($client)->allows('view-reports'));
    }

    /** @test */
    public function resource_based_access_control_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff1 = User::factory()->create(['role' => 'staff']);
        $staff2 = User::factory()->create(['role' => 'staff']);
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);

        $clientModel1 = Client::factory()->create(['user_id' => $client1->id]);
        $clientModel2 = Client::factory()->create(['user_id' => $client2->id]);

        // Admin can access all clients
        $this->assertTrue(Gate::forUser($admin)->allows('view-client', $clientModel1));
        $this->assertTrue(Gate::forUser($admin)->allows('view-client', $clientModel2));

        // Staff can access all clients
        $this->assertTrue(Gate::forUser($staff1)->allows('view-client', $clientModel1));
        $this->assertTrue(Gate::forUser($staff1)->allows('view-client', $clientModel2));

        // Client can only access their own data
        $this->assertTrue(Gate::forUser($client1)->allows('view-client', $clientModel1));
        $this->assertFalse(Gate::forUser($client1)->allows('view-client', $clientModel2));
    }

    /** @test */
    public function permission_escalation_prevention_test()
    {
        $client = User::factory()->create(['role' => 'client']);
        $token = $client->createToken('test-token')->plainTextToken;

        // Attempt to escalate privileges
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/user/role', [
            'role' => 'admin',
        ]);

        $response->assertStatus(403);
        
        // Verify role wasn't changed
        $client->refresh();
        $this->assertEquals('client', $client->role);
    }

    /** @test */
    public function horizontal_privilege_escalation_test()
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
    public function vertical_privilege_escalation_test()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $token = $staff->createToken('test-token')->plainTextToken;

        // Attempt to access admin-only functionality
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/admin/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function api_endpoint_authorization_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $client = User::factory()->create(['role' => 'client']);

        $adminToken = $admin->createToken('admin-token')->plainTextToken;
        $staffToken = $staff->createToken('staff-token')->plainTextToken;
        $clientToken = $client->createToken('client-token')->plainTextToken;

        // Test admin endpoints
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->getJson('/api/admin/dashboard');

        $response->assertStatus(200);

        // Test staff access to admin endpoints
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $staffToken,
        ])->getJson('/api/admin/dashboard');

        $response->assertStatus(403);

        // Test client access to admin endpoints
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $clientToken,
        ])->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function resource_ownership_validation_test()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $clientModel1 = Client::factory()->create(['user_id' => $client1->id]);
        $clientModel2 = Client::factory()->create(['user_id' => $client2->id]);
        
        $token = $client1->createToken('test-token')->plainTextToken;

        // Attempt to update another client's data
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/clients/' . $clientModel2->id, [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
        
        // Verify data wasn't changed
        $clientModel2->refresh();
        $this->assertNotEquals('Hacked Name', $clientModel2->name);
    }

    /** @test */
    public function staff_location_access_control_test()
    {
        $location1 = Location::factory()->create(['name' => 'Location 1']);
        $location2 = Location::factory()->create(['name' => 'Location 2']);
        
        $staff1 = User::factory()->create(['role' => 'staff']);
        $staff2 = User::factory()->create(['role' => 'staff']);
        $staffModel1 = Staff::factory()->create(['user_id' => $staff1->id, 'location_id' => $location1->id]);
        $staffModel2 = Staff::factory()->create(['user_id' => $staff2->id, 'location_id' => $location2->id]);
        
        $token = $staff1->createToken('test-token')->plainTextToken;

        // Staff should only access their location's data
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/' . $location1->id . '/appointments');

        $response->assertStatus(200);

        // Attempt to access another location's data
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/' . $location2->id . '/appointments');

        $response->assertStatus(403);
    }

    /** @test */
    public function appointment_access_control_test()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        
        $appointment1 = Appointment::factory()->create(['client_id' => $client1->id, 'staff_id' => $staffModel->id]);
        $appointment2 = Appointment::factory()->create(['client_id' => $client2->id, 'staff_id' => $staffModel->id]);
        
        $client1Token = $client1->createToken('client1-token')->plainTextToken;
        $client2Token = $client2->createToken('client2-token')->plainTextToken;
        $staffToken = $staff->createToken('staff-token')->plainTextToken;

        // Client 1 can access their own appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $client1Token,
        ])->getJson('/api/appointments/' . $appointment1->id);

        $response->assertStatus(200);

        // Client 1 cannot access client 2's appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $client1Token,
        ])->getJson('/api/appointments/' . $appointment2->id);

        $response->assertStatus(403);

        // Staff can access appointments they're assigned to
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $staffToken,
        ])->getJson('/api/appointments/' . $appointment1->id);

        $response->assertStatus(200);
    }

    /** @test */
    public function inventory_access_control_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $client = User::factory()->create(['role' => 'client']);
        
        $adminToken = $admin->createToken('admin-token')->plainTextToken;
        $staffToken = $staff->createToken('staff-token')->plainTextToken;
        $clientToken = $client->createToken('client-token')->plainTextToken;

        // Admin and staff can access inventory
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->getJson('/api/inventory');

        $response->assertStatus(200);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $staffToken,
        ])->getJson('/api/inventory');

        $response->assertStatus(200);

        // Client cannot access inventory
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $clientToken,
        ])->getJson('/api/inventory');

        $response->assertStatus(403);
    }

    /** @test */
    public function report_access_control_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $client = User::factory()->create(['role' => 'client']);
        
        $adminToken = $admin->createToken('admin-token')->plainTextToken;
        $staffToken = $staff->createToken('staff-token')->plainTextToken;
        $clientToken = $client->createToken('client-token')->plainTextToken;

        // Admin can access all reports
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->getJson('/api/reports/financial');

        $response->assertStatus(200);

        // Staff can access limited reports
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $staffToken,
        ])->getJson('/api/reports/dashboard');

        $response->assertStatus(200);

        // Staff cannot access financial reports
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $staffToken,
        ])->getJson('/api/reports/financial');

        $response->assertStatus(403);

        // Client cannot access any reports
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $clientToken,
        ])->getJson('/api/reports/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function bulk_operation_authorization_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        
        $adminToken = $admin->createToken('admin-token')->plainTextToken;
        $staffToken = $staff->createToken('staff-token')->plainTextToken;

        // Admin can perform bulk operations
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->putJson('/api/clients/bulk-update', [
            'client_ids' => [1, 2, 3],
            'city' => 'Updated City',
        ]);

        $response->assertStatus(200);

        // Staff cannot perform bulk operations
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $staffToken,
        ])->putJson('/api/clients/bulk-update', [
            'client_ids' => [1, 2, 3],
            'city' => 'Updated City',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function data_export_authorization_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $client = User::factory()->create(['role' => 'client']);
        
        $adminToken = $admin->createToken('admin-token')->plainTextToken;
        $staffToken = $staff->createToken('staff-token')->plainTextToken;
        $clientToken = $client->createToken('client-token')->plainTextToken;

        // Admin can export data
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->getJson('/api/clients/export');

        $response->assertStatus(200);

        // Staff can export data
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $staffToken,
        ])->getJson('/api/clients/export');

        $response->assertStatus(200);

        // Client cannot export data
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $clientToken,
        ])->getJson('/api/clients/export');

        $response->assertStatus(403);
    }

    /** @test */
    public function system_configuration_access_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        
        $adminToken = $admin->createToken('admin-token')->plainTextToken;
        $staffToken = $staff->createToken('staff-token')->plainTextToken;

        // Admin can access system configuration
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->getJson('/api/system/config');

        $response->assertStatus(200);

        // Staff cannot access system configuration
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $staffToken,
        ])->getJson('/api/system/config');

        $response->assertStatus(403);
    }

    /** @test */
    public function audit_log_access_control_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $client = User::factory()->create(['role' => 'client']);
        
        $adminToken = $admin->createToken('admin-token')->plainTextToken;
        $staffToken = $staff->createToken('staff-token')->plainTextToken;
        $clientToken = $client->createToken('client-token')->plainTextToken;

        // Admin can access audit logs
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->getJson('/api/audit-logs');

        $response->assertStatus(200);

        // Staff cannot access audit logs
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $staffToken,
        ])->getJson('/api/audit-logs');

        $response->assertStatus(403);

        // Client cannot access audit logs
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $clientToken,
        ])->getJson('/api/audit-logs');

        $response->assertStatus(403);
    }

    /** @test */
    public function time_based_access_control_test()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $token = $staff->createToken('test-token')->plainTextToken;

        // Test access during business hours
        $this->travelTo(now()->setHour(10)); // 10 AM
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments');

        $response->assertStatus(200);

        // Test access outside business hours
        $this->travelTo(now()->setHour(22)); // 10 PM
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments');

        $response->assertStatus(403);
    }

    /** @test */
    public function ip_based_access_control_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        // Test access from allowed IP
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Forwarded-For' => '192.168.1.100',
        ])->getJson('/api/admin/dashboard');

        $response->assertStatus(200);

        // Test access from blocked IP
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Forwarded-For' => '10.0.0.1',
        ])->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }
}
