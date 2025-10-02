<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Client;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_create_a_client()
    {
        $user = User::factory()->create(['role' => 'client']);
        
        $clientData = [
            'user_id' => $user->id,
            'preferences' => ['email_notifications' => true],
            'notes' => 'VIP client',
            'allergies' => ['latex'],
            'medical_conditions' => ['sensitive skin'],
        ];

        $client = Client::create($clientData);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals($user->id, $client->user_id);
        $this->assertEquals('VIP client', $client->notes);
        $this->assertContains('latex', $client->allergies);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create(['role' => 'client']);
        $client = Client::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $client->user);
        $this->assertEquals($user->id, $client->user->id);
    }

    /** @test */
    public function it_can_have_many_appointments()
    {
        $client = Client::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['client_id' => $client->user_id]);

        $this->assertCount(3, $client->appointments);
        $this->assertInstanceOf(Appointment::class, $client->appointments->first());
    }

    /** @test */
    public function it_can_have_many_invoices()
    {
        $client = Client::factory()->create();
        $invoices = Invoice::factory()->count(2)->create(['client_id' => $client->user_id]);

        $this->assertCount(2, $client->invoices);
        $this->assertInstanceOf(Invoice::class, $client->invoices->first());
    }

    /** @test */
    public function it_can_have_many_payments()
    {
        $client = Client::factory()->create();
        $payments = Payment::factory()->count(2)->create(['client_id' => $client->user_id]);

        $this->assertCount(2, $client->payments);
        $this->assertInstanceOf(Payment::class, $client->payments->first());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $client = Client::factory()->create([
            'preferences' => ['email_notifications' => true, 'sms_notifications' => false],
            'allergies' => ['latex', 'perfume'],
            'medical_conditions' => ['sensitive skin', 'asthma'],
            'emergency_contact' => ['name' => 'John Doe', 'phone' => '1234567890'],
        ]);

        $this->assertIsArray($client->preferences);
        $this->assertIsArray($client->allergies);
        $this->assertIsArray($client->medical_conditions);
        $this->assertIsArray($client->emergency_contact);
    }

    /** @test */
    public function it_can_scope_active_clients()
    {
        $activeUser = User::factory()->create(['is_active' => true]);
        $inactiveUser = User::factory()->create(['is_active' => false]);
        
        Client::factory()->create(['user_id' => $activeUser->id]);
        Client::factory()->create(['user_id' => $inactiveUser->id]);

        $activeClients = Client::whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->get();

        $this->assertCount(1, $activeClients);
    }

    /** @test */
    public function it_can_scope_vip_clients()
    {
        Client::factory()->create(['is_vip' => true]);
        Client::factory()->create(['is_vip' => false]);

        $vipClients = Client::where('is_vip', true)->get();

        $this->assertCount(1, $vipClients);
        $this->assertTrue($vipClients->first()->is_vip);
    }

    /** @test */
    public function it_can_scope_clients_with_allergies()
    {
        Client::factory()->create(['allergies' => ['latex']]);
        Client::factory()->create(['allergies' => []]);

        $clientsWithAllergies = Client::whereJsonLength('allergies', '>', 0)->get();

        $this->assertCount(1, $clientsWithAllergies);
    }

    /** @test */
    public function it_can_scope_clients_with_medical_conditions()
    {
        Client::factory()->create(['medical_conditions' => ['sensitive skin']]);
        Client::factory()->create(['medical_conditions' => []]);

        $clientsWithConditions = Client::whereJsonLength('medical_conditions', '>', 0)->get();

        $this->assertCount(1, $clientsWithConditions);
    }

    /** @test */
    public function it_can_get_client_name()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $client = Client::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('John Doe', $client->name);
    }

    /** @test */
    public function it_can_get_client_email()
    {
        $user = User::factory()->create(['email' => 'john@example.com']);
        $client = Client::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('john@example.com', $client->email);
    }

    /** @test */
    public function it_can_get_client_phone()
    {
        $user = User::factory()->create(['phone' => '1234567890']);
        $client = Client::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('1234567890', $client->phone);
    }

    /** @test */
    public function it_can_get_total_appointments()
    {
        $client = Client::factory()->create();
        Appointment::factory()->count(3)->create(['client_id' => $client->user_id]);

        $this->assertEquals(3, $client->total_appointments);
    }

    /** @test */
    public function it_can_get_completed_appointments()
    {
        $client = Client::factory()->create();
        Appointment::factory()->create([
            'client_id' => $client->user_id,
            'status' => 'completed',
        ]);
        Appointment::factory()->create([
            'client_id' => $client->user_id,
            'status' => 'scheduled',
        ]);

        $this->assertEquals(1, $client->completed_appointments);
    }

    /** @test */
    public function it_can_get_cancelled_appointments()
    {
        $client = Client::factory()->create();
        Appointment::factory()->create([
            'client_id' => $client->user_id,
            'status' => 'cancelled',
        ]);
        Appointment::factory()->create([
            'client_id' => $client->user_id,
            'status' => 'scheduled',
        ]);

        $this->assertEquals(1, $client->cancelled_appointments);
    }

    /** @test */
    public function it_can_get_total_spent()
    {
        $client = Client::factory()->create();
        Payment::factory()->create([
            'client_id' => $client->user_id,
            'amount' => 50.00,
            'status' => 'completed',
        ]);
        Payment::factory()->create([
            'client_id' => $client->user_id,
            'amount' => 75.00,
            'status' => 'completed',
        ]);

        $this->assertEquals(125.00, $client->total_spent);
    }

    /** @test */
    public function it_can_get_average_spending()
    {
        $client = Client::factory()->create();
        Payment::factory()->create([
            'client_id' => $client->user_id,
            'amount' => 50.00,
            'status' => 'completed',
        ]);
        Payment::factory()->create([
            'client_id' => $client->user_id,
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        $this->assertEquals(75.00, $client->average_spending);
    }

    /** @test */
    public function it_can_get_last_appointment()
    {
        $client = Client::factory()->create();
        $oldAppointment = Appointment::factory()->create([
            'client_id' => $client->user_id,
            'appointment_date' => now()->subDay(),
        ]);
        $recentAppointment = Appointment::factory()->create([
            'client_id' => $client->user_id,
            'appointment_date' => now()->addDay(),
        ]);

        $this->assertEquals($recentAppointment->id, $client->last_appointment->id);
    }

    /** @test */
    public function it_can_get_next_appointment()
    {
        $client = Client::factory()->create();
        $pastAppointment = Appointment::factory()->create([
            'client_id' => $client->user_id,
            'appointment_date' => now()->subDay(),
            'status' => 'completed',
        ]);
        $upcomingAppointment = Appointment::factory()->create([
            'client_id' => $client->user_id,
            'appointment_date' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $this->assertEquals($upcomingAppointment->id, $client->next_appointment->id);
    }

    /** @test */
    public function it_can_get_client_loyalty_points()
    {
        $client = Client::factory()->create(['loyalty_points' => 150]);

        $this->assertEquals(150, $client->loyalty_points);
    }

    /** @test */
    public function it_can_get_client_loyalty_tier()
    {
        $bronzeClient = Client::factory()->create(['loyalty_points' => 50]);
        $silverClient = Client::factory()->create(['loyalty_points' => 150]);
        $goldClient = Client::factory()->create(['loyalty_points' => 300]);

        $this->assertEquals('bronze', $bronzeClient->loyalty_tier);
        $this->assertEquals('silver', $silverClient->loyalty_tier);
        $this->assertEquals('gold', $goldClient->loyalty_tier);
    }

    /** @test */
    public function it_can_add_loyalty_points()
    {
        $client = Client::factory()->create(['loyalty_points' => 100]);

        $client->addLoyaltyPoints(50, 'Appointment completed');

        $this->assertEquals(150, $client->loyalty_points);
    }

    /** @test */
    public function it_can_redeem_loyalty_points()
    {
        $client = Client::factory()->create(['loyalty_points' => 200]);

        $client->redeemLoyaltyPoints(50, 'Discount applied');

        $this->assertEquals(150, $client->loyalty_points);
    }

    /** @test */
    public function it_can_check_if_client_has_allergy()
    {
        $client = Client::factory()->create(['allergies' => ['latex', 'perfume']]);

        $this->assertTrue($client->hasAllergy('latex'));
        $this->assertTrue($client->hasAllergy('perfume'));
        $this->assertFalse($client->hasAllergy('pollen'));
    }

    /** @test */
    public function it_can_check_if_client_has_medical_condition()
    {
        $client = Client::factory()->create(['medical_conditions' => ['sensitive skin', 'asthma']]);

        $this->assertTrue($client->hasMedicalCondition('sensitive skin'));
        $this->assertTrue($client->hasMedicalCondition('asthma'));
        $this->assertFalse($client->hasMedicalCondition('diabetes'));
    }

    /** @test */
    public function it_can_get_client_preference()
    {
        $client = Client::factory()->create([
            'preferences' => [
                'email_notifications' => true,
                'sms_notifications' => false,
                'preferred_time' => 'morning',
            ]
        ]);

        $this->assertTrue($client->getPreference('email_notifications'));
        $this->assertFalse($client->getPreference('sms_notifications'));
        $this->assertEquals('morning', $client->getPreference('preferred_time'));
        $this->assertNull($client->getPreference('nonexistent'));
    }

    /** @test */
    public function it_can_set_client_preference()
    {
        $client = Client::factory()->create(['preferences' => []]);

        $client->setPreference('email_notifications', true);
        $client->setPreference('preferred_time', 'afternoon');

        $this->assertTrue($client->getPreference('email_notifications'));
        $this->assertEquals('afternoon', $client->getPreference('preferred_time'));
    }

    /** @test */
    public function it_can_get_emergency_contact_name()
    {
        $client = Client::factory()->create([
            'emergency_contact' => ['name' => 'Jane Doe', 'phone' => '0987654321']
        ]);

        $this->assertEquals('Jane Doe', $client->emergency_contact_name);
    }

    /** @test */
    public function it_can_get_emergency_contact_phone()
    {
        $client = Client::factory()->create([
            'emergency_contact' => ['name' => 'Jane Doe', 'phone' => '0987654321']
        ]);

        $this->assertEquals('0987654321', $client->emergency_contact_phone);
    }

    /** @test */
    public function it_can_get_client_statistics()
    {
        $client = Client::factory()->create();
        
        // Create appointments with different statuses
        Appointment::factory()->create([
            'client_id' => $client->user_id,
            'status' => 'completed',
        ]);
        Appointment::factory()->create([
            'client_id' => $client->user_id,
            'status' => 'completed',
        ]);
        Appointment::factory()->create([
            'client_id' => $client->user_id,
            'status' => 'cancelled',
        ]);

        // Create payments
        Payment::factory()->create([
            'client_id' => $client->user_id,
            'amount' => 50.00,
            'status' => 'completed',
        ]);
        Payment::factory()->create([
            'client_id' => $client->user_id,
            'amount' => 75.00,
            'status' => 'completed',
        ]);

        $stats = $client->getStatistics();

        $this->assertEquals(3, $stats['total_appointments']);
        $this->assertEquals(2, $stats['completed_appointments']);
        $this->assertEquals(1, $stats['cancelled_appointments']);
        $this->assertEquals(125.00, $stats['total_spent']);
        $this->assertEquals(62.50, $stats['average_spending']);
    }

    /** @test */
    public function it_can_get_client_activity_summary()
    {
        $client = Client::factory()->create();
        
        $activity = $client->getActivitySummary();

        $this->assertArrayHasKey('total_appointments', $activity);
        $this->assertArrayHasKey('completed_appointments', $activity);
        $this->assertArrayHasKey('cancelled_appointments', $activity);
        $this->assertArrayHasKey('total_spent', $activity);
        $this->assertArrayHasKey('last_appointment', $activity);
        $this->assertArrayHasKey('next_appointment', $activity);
    }

    /** @test */
    public function it_can_mark_as_vip()
    {
        $client = Client::factory()->create(['is_vip' => false]);

        $client->markAsVip('High-value client');

        $this->assertTrue($client->is_vip);
        $this->assertEquals('High-value client', $client->vip_reason);
    }

    /** @test */
    public function it_can_unmark_as_vip()
    {
        $client = Client::factory()->create(['is_vip' => true]);

        $client->unmarkAsVip();

        $this->assertFalse($client->is_vip);
        $this->assertNull($client->vip_reason);
    }

    /** @test */
    public function it_can_add_note()
    {
        $client = Client::factory()->create(['notes' => 'Initial note']);

        $client->addNote('Additional note');

        $this->assertStringContainsString('Initial note', $client->notes);
        $this->assertStringContainsString('Additional note', $client->notes);
    }

    /** @test */
    public function it_can_add_allergy()
    {
        $client = Client::factory()->create(['allergies' => ['latex']]);

        $client->addAllergy('perfume');

        $this->assertContains('latex', $client->allergies);
        $this->assertContains('perfume', $client->allergies);
    }

    /** @test */
    public function it_can_remove_allergy()
    {
        $client = Client::factory()->create(['allergies' => ['latex', 'perfume']]);

        $client->removeAllergy('latex');

        $this->assertNotContains('latex', $client->allergies);
        $this->assertContains('perfume', $client->allergies);
    }

    /** @test */
    public function it_can_add_medical_condition()
    {
        $client = Client::factory()->create(['medical_conditions' => ['sensitive skin']]);

        $client->addMedicalCondition('asthma');

        $this->assertContains('sensitive skin', $client->medical_conditions);
        $this->assertContains('asthma', $client->medical_conditions);
    }

    /** @test */
    public function it_can_remove_medical_condition()
    {
        $client = Client::factory()->create(['medical_conditions' => ['sensitive skin', 'asthma']]);

        $client->removeMedicalCondition('sensitive skin');

        $this->assertNotContains('sensitive skin', $client->medical_conditions);
        $this->assertContains('asthma', $client->medical_conditions);
    }

    /** @test */
    public function it_can_validate_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Client::create([]);
    }

    /** @test */
    public function it_can_soft_delete()
    {
        $client = Client::factory()->create();
        $clientId = $client->id;

        $client->delete();

        $this->assertSoftDeleted('clients', ['id' => $clientId]);
    }

    /** @test */
    public function it_can_restore_soft_deleted_client()
    {
        $client = Client::factory()->create();
        $client->delete();

        $this->assertSoftDeleted('clients', ['id' => $client->id]);

        $client->restore();

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_force_delete()
    {
        $client = Client::factory()->create();
        $clientId = $client->id;

        $client->forceDelete();

        $this->assertDatabaseMissing('clients', ['id' => $clientId]);
    }

    /** @test */
    public function it_can_get_client_search_results()
    {
        $user1 = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        $user2 = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
        
        Client::factory()->create(['user_id' => $user1->id]);
        Client::factory()->create(['user_id' => $user2->id]);

        $johnClients = Client::whereHas('user', function ($query) {
            $query->where('name', 'like', '%John%');
        })->get();

        $this->assertCount(1, $johnClients);
    }

    /** @test */
    public function it_can_get_clients_by_loyalty_tier()
    {
        Client::factory()->create(['loyalty_points' => 50]); // bronze
        Client::factory()->create(['loyalty_points' => 150]); // silver
        Client::factory()->create(['loyalty_points' => 300]); // gold

        $goldClients = Client::where('loyalty_points', '>=', 250)->get();

        $this->assertCount(1, $goldClients);
    }

    /** @test */
    public function it_can_get_clients_by_spending()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        
        Payment::factory()->create([
            'client_id' => $client1->user_id,
            'amount' => 100.00,
            'status' => 'completed',
        ]);
        Payment::factory()->create([
            'client_id' => $client2->user_id,
            'amount' => 50.00,
            'status' => 'completed',
        ]);

        $highSpenders = Client::whereHas('payments', function ($query) {
            $query->where('status', 'completed')
                  ->selectRaw('SUM(amount) as total')
                  ->havingRaw('SUM(amount) > ?', [75.00]);
        })->get();

        $this->assertCount(1, $highSpenders);
    }
}
