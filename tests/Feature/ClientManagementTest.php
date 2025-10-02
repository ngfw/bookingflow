<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function admin_can_create_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $clientData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'preferences' => ['email_notifications' => true],
            'notes' => 'VIP client',
            'allergies' => ['latex'],
            'medical_conditions' => ['sensitive skin'],
        ];

        $response = $this->post('/admin/clients', $clientData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'client',
        ]);
    }

    /** @test */
    public function admin_can_view_clients()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/clients');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.index');
    }

    /** @test */
    public function admin_can_view_client_details()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.show');
    }

    /** @test */
    public function admin_can_update_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '0987654321',
            'notes' => 'Updated notes',
        ];

        $response = $this->put('/admin/clients/' . $client->user_id, $updateData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'id' => $client->user_id,
            'name' => 'Updated Name',
            'phone' => '0987654321',
        ]);
    }

    /** @test */
    public function admin_can_delete_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->delete('/admin/clients/' . $client->user_id);

        $response->assertStatus(302);
        $this->assertSoftDeleted('users', ['id' => $client->user_id]);
    }

    /** @test */
    public function admin_can_activate_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $client->user->update(['is_active' => false]);
        $this->actingAs($admin);

        $response = $this->put('/admin/clients/' . $client->user_id . '/activate');

        $response->assertStatus(302);
        $this->assertTrue($client->user->fresh()->is_active);
    }

    /** @test */
    public function admin_can_deactivate_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->put('/admin/clients/' . $client->user_id . '/deactivate');

        $response->assertStatus(302);
        $this->assertFalse($client->user->fresh()->is_active);
    }

    /** @test */
    public function admin_can_mark_client_as_vip()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['is_vip' => false]);
        $this->actingAs($admin);

        $response = $this->put('/admin/clients/' . $client->user_id . '/vip', [
            'vip_reason' => 'High-value client',
        ]);

        $response->assertStatus(302);
        $this->assertTrue($client->fresh()->is_vip);
        $this->assertEquals('High-value client', $client->fresh()->vip_reason);
    }

    /** @test */
    public function admin_can_unmark_client_as_vip()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['is_vip' => true]);
        $this->actingAs($admin);

        $response = $this->put('/admin/clients/' . $client->user_id . '/unmark-vip');

        $response->assertStatus(302);
        $this->assertFalse($client->fresh()->is_vip);
    }

    /** @test */
    public function admin_can_add_client_note()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['notes' => 'Initial note']);
        $this->actingAs($admin);

        $response = $this->post('/admin/clients/' . $client->user_id . '/notes', [
            'note' => 'Additional note',
        ]);

        $response->assertStatus(302);
        $this->assertStringContainsString('Additional note', $client->fresh()->notes);
    }

    /** @test */
    public function admin_can_add_client_allergy()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['allergies' => ['latex']]);
        $this->actingAs($admin);

        $response = $this->post('/admin/clients/' . $client->user_id . '/allergies', [
            'allergy' => 'perfume',
        ]);

        $response->assertStatus(302);
        $this->assertContains('perfume', $client->fresh()->allergies);
    }

    /** @test */
    public function admin_can_remove_client_allergy()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['allergies' => ['latex', 'perfume']]);
        $this->actingAs($admin);

        $response = $this->delete('/admin/clients/' . $client->user_id . '/allergies/latex');

        $response->assertStatus(302);
        $this->assertNotContains('latex', $client->fresh()->allergies);
        $this->assertContains('perfume', $client->fresh()->allergies);
    }

    /** @test */
    public function admin_can_add_client_medical_condition()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['medical_conditions' => ['sensitive skin']]);
        $this->actingAs($admin);

        $response = $this->post('/admin/clients/' . $client->user_id . '/medical-conditions', [
            'condition' => 'asthma',
        ]);

        $response->assertStatus(302);
        $this->assertContains('asthma', $client->fresh()->medical_conditions);
    }

    /** @test */
    public function admin_can_remove_client_medical_condition()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['medical_conditions' => ['sensitive skin', 'asthma']]);
        $this->actingAs($admin);

        $response = $this->delete('/admin/clients/' . $client->user_id . '/medical-conditions/sensitive skin');

        $response->assertStatus(302);
        $this->assertNotContains('sensitive skin', $client->fresh()->medical_conditions);
        $this->assertContains('asthma', $client->fresh()->medical_conditions);
    }

    /** @test */
    public function admin_can_view_client_appointments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['client_id' => $client->user_id]);
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/appointments');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.appointments');
    }

    /** @test */
    public function admin_can_view_client_invoices()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $invoices = Invoice::factory()->count(3)->create(['client_id' => $client->user_id]);
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/invoices');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.invoices');
    }

    /** @test */
    public function admin_can_view_client_payments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $payments = Payment::factory()->count(3)->create(['client_id' => $client->user_id]);
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/payments');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.payments');
    }

    /** @test */
    public function admin_can_view_client_statistics()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/statistics');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.statistics');
    }

    /** @test */
    public function admin_can_filter_clients_by_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activeClient = Client::factory()->create();
        $inactiveClient = Client::factory()->create();
        $inactiveClient->user->update(['is_active' => false]);
        $this->actingAs($admin);

        $response = $this->get('/admin/clients?status=active');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.index');
    }

    /** @test */
    public function admin_can_filter_clients_by_vip_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vipClient = Client::factory()->create(['is_vip' => true]);
        $regularClient = Client::factory()->create(['is_vip' => false]);
        $this->actingAs($admin);

        $response = $this->get('/admin/clients?vip=true');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.index');
    }

    /** @test */
    public function admin_can_search_clients()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client1 = Client::factory()->create();
        $client1->user->update(['name' => 'John Doe']);
        $client2 = Client::factory()->create();
        $client2->user->update(['name' => 'Jane Smith']);
        $this->actingAs($admin);

        $response = $this->get('/admin/clients?search=John');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.index');
    }

    /** @test */
    public function admin_can_export_clients()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Client::factory()->count(3)->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_import_clients()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $csvContent = "name,email,phone\nJohn Doe,john@example.com,1234567890\nJane Smith,jane@example.com,0987654321";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('clients.csv', $csvContent);

        $response = $this->post('/admin/clients/import', [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function admin_can_bulk_update_clients()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->put('/admin/clients/bulk-update', [
            'client_ids' => [$client1->user_id, $client2->user_id],
            'is_vip' => true,
        ]);

        $response->assertStatus(302);
        $this->assertTrue($client1->fresh()->is_vip);
        $this->assertTrue($client2->fresh()->is_vip);
    }

    /** @test */
    public function admin_can_bulk_delete_clients()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->delete('/admin/clients/bulk-delete', [
            'client_ids' => [$client1->user_id, $client2->user_id],
        ]);

        $response->assertStatus(302);
        $this->assertSoftDeleted('users', ['id' => $client1->user_id]);
        $this->assertSoftDeleted('users', ['id' => $client2->user_id]);
    }

    /** @test */
    public function admin_can_view_client_communication_history()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/communications');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.communications');
    }

    /** @test */
    public function admin_can_send_client_communication()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->post('/admin/clients/' . $client->user_id . '/communications', [
            'type' => 'email',
            'subject' => 'Test Email',
            'message' => 'This is a test email',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('client_communication_histories', [
            'client_id' => $client->user_id,
            'type' => 'email',
            'subject' => 'Test Email',
        ]);
    }

    /** @test */
    public function admin_can_view_client_loyalty_points()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['loyalty_points' => 150]);
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/loyalty');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.loyalty');
    }

    /** @test */
    public function admin_can_add_client_loyalty_points()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['loyalty_points' => 100]);
        $this->actingAs($admin);

        $response = $this->post('/admin/clients/' . $client->user_id . '/loyalty/add', [
            'points' => 50,
            'reason' => 'Bonus points',
        ]);

        $response->assertStatus(302);
        $this->assertEquals(150, $client->fresh()->loyalty_points);
    }

    /** @test */
    public function admin_can_redeem_client_loyalty_points()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['loyalty_points' => 200]);
        $this->actingAs($admin);

        $response = $this->post('/admin/clients/' . $client->user_id . '/loyalty/redeem', [
            'points' => 50,
            'reason' => 'Discount applied',
        ]);

        $response->assertStatus(302);
        $this->assertEquals(150, $client->fresh()->loyalty_points);
    }

    /** @test */
    public function admin_can_view_client_preferences()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create([
            'preferences' => [
                'email_notifications' => true,
                'sms_notifications' => false,
                'preferred_time' => 'morning',
            ]
        ]);
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/preferences');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.preferences');
    }

    /** @test */
    public function admin_can_update_client_preferences()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['preferences' => []]);
        $this->actingAs($admin);

        $response = $this->put('/admin/clients/' . $client->user_id . '/preferences', [
            'email_notifications' => true,
            'sms_notifications' => false,
            'preferred_time' => 'afternoon',
        ]);

        $response->assertStatus(302);
        $this->assertTrue($client->fresh()->getPreference('email_notifications'));
        $this->assertFalse($client->fresh()->getPreference('sms_notifications'));
        $this->assertEquals('afternoon', $client->fresh()->getPreference('preferred_time'));
    }

    /** @test */
    public function admin_can_view_client_emergency_contact()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create([
            'emergency_contact' => [
                'name' => 'Jane Doe',
                'phone' => '0987654321',
                'relationship' => 'spouse',
            ]
        ]);
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/emergency-contact');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.emergency-contact');
    }

    /** @test */
    public function admin_can_update_client_emergency_contact()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['emergency_contact' => []]);
        $this->actingAs($admin);

        $response = $this->put('/admin/clients/' . $client->user_id . '/emergency-contact', [
            'name' => 'John Smith',
            'phone' => '1234567890',
            'relationship' => 'brother',
        ]);

        $response->assertStatus(302);
        $this->assertEquals('John Smith', $client->fresh()->emergency_contact['name']);
        $this->assertEquals('1234567890', $client->fresh()->emergency_contact['phone']);
        $this->assertEquals('brother', $client->fresh()->emergency_contact['relationship']);
    }

    /** @test */
    public function admin_can_view_client_activity_log()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/activity');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.activity');
    }

    /** @test */
    public function admin_can_view_client_documents()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/clients/' . $client->user_id . '/documents');

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.documents');
    }

    /** @test */
    public function admin_can_upload_client_document()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $this->actingAs($admin);

        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->post('/admin/clients/' . $client->user_id . '/documents', [
            'file' => $file,
            'name' => 'Test Document',
            'type' => 'contract',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('client_documents', [
            'client_id' => $client->user_id,
            'name' => 'Test Document',
            'type' => 'contract',
        ]);
    }

    /** @test */
    public function admin_can_delete_client_document()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $document = \App\Models\ClientDocument::factory()->create(['client_id' => $client->user_id]);
        $this->actingAs($admin);

        $response = $this->delete('/admin/clients/' . $client->user_id . '/documents/' . $document->id);

        $response->assertStatus(302);
        $this->assertSoftDeleted('client_documents', ['id' => $document->id]);
    }
}
