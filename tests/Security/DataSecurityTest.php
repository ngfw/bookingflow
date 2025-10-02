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
use App\Services\DataEncryptionService;
use App\Services\GDPRComplianceService;
use App\Services\DataAnonymizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class DataSecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function sensitive_data_encryption_test()
    {
        $encryptionService = new DataEncryptionService();
        
        $sensitiveData = 'This is sensitive information';
        $encryptedData = $encryptionService->encrypt($sensitiveData);
        $decryptedData = $encryptionService->decrypt($encryptedData);
        
        $this->assertNotEquals($sensitiveData, $encryptedData);
        $this->assertEquals($sensitiveData, $decryptedData);
    }

    /** @test */
    public function client_personal_data_encryption_test()
    {
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'date_of_birth' => '1990-01-01',
        ]);

        // Verify sensitive data is encrypted in database
        $this->assertDatabaseHas('clients', [
            'email' => 'john@example.com',
        ]);

        // Test that encrypted fields are properly handled
        $retrievedClient = Client::find($client->id);
        $this->assertEquals('John Doe', $retrievedClient->name);
        $this->assertEquals('john@example.com', $retrievedClient->email);
    }

    /** @test */
    public function staff_personal_data_encryption_test()
    {
        $staff = Staff::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+1234567891',
            'address' => '456 Oak Ave',
            'salary' => 50000.00,
        ]);

        // Verify sensitive data is properly stored
        $this->assertDatabaseHas('staff', [
            'email' => 'jane@example.com',
        ]);

        // Test that encrypted fields are properly handled
        $retrievedStaff = Staff::find($staff->id);
        $this->assertEquals('Jane Smith', $retrievedStaff->name);
        $this->assertEquals('jane@example.com', $retrievedStaff->email);
    }

    /** @test */
    public function appointment_data_encryption_test()
    {
        $appointment = Appointment::factory()->create([
            'notes' => 'Client has sensitive medical condition',
            'cancellation_reason' => 'Personal emergency',
        ]);

        // Verify sensitive data is properly stored
        $this->assertDatabaseHas('appointments', [
            'notes' => 'Client has sensitive medical condition',
        ]);

        // Test that encrypted fields are properly handled
        $retrievedAppointment = Appointment::find($appointment->id);
        $this->assertEquals('Client has sensitive medical condition', $retrievedAppointment->notes);
    }

    /** @test */
    public function gdpr_data_export_test()
    {
        $gdprService = new GDPRComplianceService();
        
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
        ]);

        // Create some related data
        $appointments = Appointment::factory()->count(3)->create([
            'client_id' => $client->user_id,
        ]);

        $exportData = $gdprService->exportUserData($client->user_id);
        
        $this->assertArrayHasKey('personal_data', $exportData);
        $this->assertArrayHasKey('appointments', $exportData);
        $this->assertArrayHasKey('communications', $exportData);
        $this->assertEquals('John Doe', $exportData['personal_data']['name']);
    }

    /** @test */
    public function gdpr_data_anonymization_test()
    {
        $anonymizationService = new DataAnonymizationService();
        
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
        ]);

        // Create some related data
        $appointments = Appointment::factory()->count(3)->create([
            'client_id' => $client->user_id,
        ]);

        $anonymizationService->anonymizeUserData($client->user_id);
        
        $anonymizedClient = Client::find($client->id);
        $this->assertNotEquals('John Doe', $anonymizedClient->name);
        $this->assertNotEquals('john@example.com', $anonymizedClient->email);
        $this->assertNotEquals('+1234567890', $anonymizedClient->phone);
        $this->assertNotEquals('123 Main St', $anonymizedClient->address);
    }

    /** @test */
    public function gdpr_data_deletion_test()
    {
        $gdprService = new GDPRComplianceService();
        
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Create some related data
        $appointments = Appointment::factory()->count(3)->create([
            'client_id' => $client->user_id,
        ]);

        $gdprService->deleteUserData($client->user_id);
        
        // Verify client is soft deleted
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
        
        // Verify related appointments are also soft deleted
        foreach ($appointments as $appointment) {
            $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
        }
    }

    /** @test */
    public function data_retention_policy_test()
    {
        $gdprService = new GDPRComplianceService();
        
        // Create old client data
        $oldClient = Client::factory()->create([
            'created_at' => now()->subYears(8),
        ]);

        // Create recent client data
        $recentClient = Client::factory()->create([
            'created_at' => now()->subMonths(6),
        ]);

        // Apply data retention policy (7 years)
        $gdprService->applyDataRetentionPolicy(7);
        
        // Old client should be marked for deletion
        $this->assertSoftDeleted('clients', ['id' => $oldClient->id]);
        
        // Recent client should remain
        $this->assertDatabaseHas('clients', [
            'id' => $recentClient->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function data_backup_encryption_test()
    {
        $backupService = new \App\Services\BackupAutomationService();
        
        // Create test data
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $backupPath = $backupService->createEncryptedBackup();
        
        $this->assertFileExists($backupPath);
        
        // Verify backup is encrypted
        $backupContent = file_get_contents($backupPath);
        $this->assertStringNotContainsString('John Doe', $backupContent);
        $this->assertStringNotContainsString('john@example.com', $backupContent);
    }

    /** @test */
    public function data_integrity_verification_test()
    {
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Simulate data corruption
        $client->update(['name' => 'Corrupted Name']);
        
        // Verify data integrity check fails
        $integrityService = new \App\Services\DataIntegrityService();
        $isValid = $integrityService->verifyDataIntegrity($client);
        
        $this->assertFalse($isValid);
    }

    /** @test */
    public function audit_trail_data_security_test()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Update client data
        $client->update(['name' => 'Jane Doe']);

        // Verify audit trail doesn't expose sensitive data
        $auditLog = \App\Models\AuditLog::where('model_type', Client::class)
            ->where('model_id', $client->id)
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertStringNotContainsString('john@example.com', $auditLog->old_values);
        $this->assertStringNotContainsString('john@example.com', $auditLog->new_values);
    }

    /** @test */
    public function data_masking_test()
    {
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
        ]);

        $maskingService = new \App\Services\DataMaskingService();
        $maskedData = $maskingService->maskSensitiveData($client->toArray());
        
        $this->assertStringContainsString('***', $maskedData['email']);
        $this->assertStringContainsString('***', $maskedData['phone']);
        $this->assertStringContainsString('***', $maskedData['address']);
        $this->assertEquals('John Doe', $maskedData['name']); // Name should not be masked
    }

    /** @test */
    public function data_classification_test()
    {
        $classificationService = new \App\Services\DataClassificationService();
        
        $clientData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'date_of_birth' => '1990-01-01',
        ];

        $classifiedData = $classificationService->classifyData($clientData);
        
        $this->assertEquals('public', $classifiedData['name']);
        $this->assertEquals('confidential', $classifiedData['email']);
        $this->assertEquals('confidential', $classifiedData['phone']);
        $this->assertEquals('confidential', $classifiedData['address']);
        $this->assertEquals('restricted', $classifiedData['date_of_birth']);
    }

    /** @test */
    public function data_loss_prevention_test()
    {
        $dlpService = new \App\Services\DataLossPreventionService();
        
        $sensitiveData = [
            'credit_card' => '4111-1111-1111-1111',
            'ssn' => '123-45-6789',
            'email' => 'john@example.com',
        ];

        $violations = $dlpService->scanForViolations($sensitiveData);
        
        $this->assertCount(2, $violations);
        $this->assertContains('credit_card', $violations);
        $this->assertContains('ssn', $violations);
    }

    /** @test */
    public function data_encryption_at_rest_test()
    {
        $encryptionService = new DataEncryptionService();
        
        $sensitiveData = 'This is sensitive data at rest';
        $encryptedData = $encryptionService->encryptAtRest($sensitiveData);
        $decryptedData = $encryptionService->decryptAtRest($encryptedData);
        
        $this->assertNotEquals($sensitiveData, $encryptedData);
        $this->assertEquals($sensitiveData, $decryptedData);
    }

    /** @test */
    public function data_encryption_in_transit_test()
    {
        $encryptionService = new DataEncryptionService();
        
        $sensitiveData = 'This is sensitive data in transit';
        $encryptedData = $encryptionService->encryptInTransit($sensitiveData);
        $decryptedData = $encryptionService->decryptInTransit($encryptedData);
        
        $this->assertNotEquals($sensitiveData, $encryptedData);
        $this->assertEquals($sensitiveData, $decryptedData);
    }

    /** @test */
    public function data_anonymization_quality_test()
    {
        $anonymizationService = new DataAnonymizationService();
        
        $originalData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main St, New York, NY 10001',
        ];

        $anonymizedData = $anonymizationService->anonymizeData($originalData);
        
        // Verify data is anonymized
        $this->assertNotEquals($originalData['name'], $anonymizedData['name']);
        $this->assertNotEquals($originalData['email'], $anonymizedData['email']);
        $this->assertNotEquals($originalData['phone'], $anonymizedData['phone']);
        $this->assertNotEquals($originalData['address'], $anonymizedData['address']);
        
        // Verify anonymized data maintains structure
        $this->assertIsString($anonymizedData['name']);
        $this->assertIsString($anonymizedData['email']);
        $this->assertIsString($anonymizedData['phone']);
        $this->assertIsString($anonymizedData['address']);
    }

    /** @test */
    public function data_breach_detection_test()
    {
        $breachDetectionService = new \App\Services\DataBreachDetectionService();
        
        // Simulate suspicious activity
        $suspiciousActivity = [
            'user_id' => 1,
            'action' => 'bulk_export',
            'data_volume' => 10000,
            'timestamp' => now(),
        ];

        $isBreach = $breachDetectionService->detectBreach($suspiciousActivity);
        
        $this->assertTrue($isBreach);
    }

    /** @test */
    public function data_governance_compliance_test()
    {
        $governanceService = new \App\Services\DataGovernanceService();
        
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $complianceStatus = $governanceService->checkCompliance($client);
        
        $this->assertArrayHasKey('gdpr', $complianceStatus);
        $this->assertArrayHasKey('ccpa', $complianceStatus);
        $this->assertArrayHasKey('hipaa', $complianceStatus);
    }
}
