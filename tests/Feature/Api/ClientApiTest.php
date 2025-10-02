<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_client_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $clientData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'date_of_birth' => '1990-01-01',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'emergency_contact' => 'Jane Doe',
            'emergency_phone' => '+1234567891',
            'allergies' => 'None',
            'notes' => 'Regular client',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clients', $clientData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'client' => [
                        'id',
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'date_of_birth',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'emergency_contact',
                        'emergency_phone',
                        'allergies',
                        'notes',
                        'created_at',
                    ],
                ]);

        $this->assertDatabaseHas('clients', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
        ]);
    }

    /** @test */
    public function staff_can_create_client_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $token = $staff->createToken('test-token')->plainTextToken;

        $clientData = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+1234567892',
            'date_of_birth' => '1985-05-15',
            'address' => '456 Oak Ave',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip_code' => '90210',
            'emergency_contact' => 'John Smith',
            'emergency_phone' => '+1234567893',
            'allergies' => 'Latex',
            'notes' => 'New client',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clients', $clientData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'client' => [
                        'id',
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'date_of_birth',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'emergency_contact',
                        'emergency_phone',
                        'allergies',
                        'notes',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_clients_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $clients = Client::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'clients' => [
                        '*' => [
                            'id',
                            'user_id',
                            'name',
                            'email',
                            'phone',
                            'date_of_birth',
                            'address',
                            'city',
                            'state',
                            'zip_code',
                            'emergency_contact',
                            'emergency_phone',
                            'allergies',
                            'notes',
                            'created_at',
                        ],
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total',
                    ],
                ]);
    }

    /** @test */
    public function staff_can_view_clients_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $clients = Client::factory()->count(3)->create();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'clients' => [
                        '*' => [
                            'id',
                            'user_id',
                            'name',
                            'email',
                            'phone',
                            'date_of_birth',
                            'address',
                            'city',
                            'state',
                            'zip_code',
                            'emergency_contact',
                            'emergency_phone',
                            'allergies',
                            'notes',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function client_can_view_own_profile_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/' . $clientModel->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'client' => [
                        'id',
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'date_of_birth',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'emergency_contact',
                        'emergency_phone',
                        'allergies',
                        'notes',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_client_details_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/' . $client->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'client' => [
                        'id',
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'date_of_birth',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'emergency_contact',
                        'emergency_phone',
                        'allergies',
                        'notes',
                        'created_at',
                        'appointments' => [
                            '*' => [
                                'id',
                                'appointment_date',
                                'status',
                                'service' => [
                                    'id',
                                    'name',
                                    'price',
                                ],
                                'staff' => [
                                    'id',
                                    'name',
                                    'position',
                                ],
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_update_client_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '+9876543210',
            'address' => '789 New St',
            'notes' => 'Updated notes',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/clients/' . $client->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'client' => [
                        'id' => $client->id,
                        'name' => 'Updated Name',
                        'phone' => '+9876543210',
                        'address' => '789 New St',
                        'notes' => 'Updated notes',
                    ],
                ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Name',
            'phone' => '+9876543210',
            'address' => '789 New St',
            'notes' => 'Updated notes',
        ]);
    }

    /** @test */
    public function client_can_update_own_profile_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Client Name',
            'phone' => '+1111111111',
            'address' => '123 Updated St',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/clients/' . $clientModel->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'client' => [
                        'id' => $clientModel->id,
                        'name' => 'Updated Client Name',
                        'phone' => '+1111111111',
                        'address' => '123 Updated St',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_delete_client_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/clients/' . $client->id);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Client deleted successfully',
                ]);

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    /** @test */
    public function admin_can_search_clients_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Client::factory()->create(['name' => 'John Doe']);
        Client::factory()->create(['name' => 'Jane Smith']);
        Client::factory()->create(['name' => 'Bob Johnson']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients?search=John');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'clients')
                ->assertJsonPath('clients.0.name', 'John Doe');
    }

    /** @test */
    public function admin_can_filter_clients_by_city_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Client::factory()->create(['city' => 'New York']);
        Client::factory()->create(['city' => 'Los Angeles']);
        Client::factory()->create(['city' => 'Chicago']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients?city=New York');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'clients')
                ->assertJsonPath('clients.0.city', 'New York');
    }

    /** @test */
    public function admin_can_filter_clients_by_state_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Client::factory()->create(['state' => 'NY']);
        Client::factory()->create(['state' => 'CA']);
        Client::factory()->create(['state' => 'IL']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients?state=NY');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'clients')
                ->assertJsonPath('clients.0.state', 'NY');
    }

    /** @test */
    public function admin_can_export_clients_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Client::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/export');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_bulk_update_clients_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/clients/bulk-update', [
            'client_ids' => [$client1->id, $client2->id],
            'city' => 'Updated City',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Clients updated successfully',
                ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client1->id,
            'city' => 'Updated City',
        ]);
        $this->assertDatabaseHas('clients', [
            'id' => $client2->id,
            'city' => 'Updated City',
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_clients_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/clients/bulk-delete', [
            'client_ids' => [$client1->id, $client2->id],
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Clients deleted successfully',
                ]);

        $this->assertSoftDeleted('clients', ['id' => $client1->id]);
        $this->assertSoftDeleted('clients', ['id' => $client2->id]);
    }

    /** @test */
    public function admin_can_view_client_statistics_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Client::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'statistics' => [
                        'total_clients',
                        'new_clients_this_month',
                        'active_clients',
                        'clients_by_city',
                        'clients_by_state',
                        'average_appointments_per_client',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_client_appointment_history_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['client_id' => $client->user_id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/' . $client->id . '/appointments');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'appointments' => [
                        '*' => [
                            'id',
                            'appointment_date',
                            'status',
                            'service' => [
                                'id',
                                'name',
                                'price',
                            ],
                            'staff' => [
                                'id',
                                'name',
                                'position',
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_client_communication_history_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/' . $client->id . '/communications');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'communications' => [
                        '*' => [
                            'id',
                            'type',
                            'subject',
                            'content',
                            'sent_at',
                            'status',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_send_communication_to_client_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $communicationData = [
            'type' => 'email',
            'subject' => 'Appointment Reminder',
            'content' => 'Your appointment is tomorrow at 2 PM.',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clients/' . $client->id . '/communications', $communicationData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'communication' => [
                        'id',
                        'client_id',
                        'type',
                        'subject',
                        'content',
                        'sent_at',
                        'status',
                    ],
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_clients_api()
    {
        $response = $this->getJson('/api/clients');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated',
                ]);
    }

    /** @test */
    public function user_cannot_access_other_clients_profile()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $clientModel2 = Client::factory()->create(['user_id' => $client2->id]);
        $token = $client1->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/' . $clientModel2->id);

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Forbidden',
                ]);
    }

    /** @test */
    public function client_can_view_own_appointment_history_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointments = Appointment::factory()->count(3)->create(['client_id' => $client->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/' . $clientModel->id . '/appointments');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'appointments' => [
                        '*' => [
                            'id',
                            'appointment_date',
                            'status',
                            'service' => [
                                'id',
                                'name',
                                'price',
                            ],
                            'staff' => [
                                'id',
                                'name',
                                'position',
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function client_can_view_own_communication_history_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clients/' . $clientModel->id . '/communications');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'communications' => [
                        '*' => [
                            'id',
                            'type',
                            'subject',
                            'content',
                            'sent_at',
                            'status',
                        ],
                    ],
                ]);
    }
}
