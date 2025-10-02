<?php

namespace App\Services;

use App\Models\User;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GDPRComplianceService
{
    /**
     * Get user's personal data
     */
    public function getUserPersonalData($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $client = $user->client;
            
            $personalData = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'date_of_birth' => $user->date_of_birth,
                    'gender' => $user->gender,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
            ];

            if ($client) {
                $personalData['client'] = [
                    'id' => $client->id,
                    'preferences' => $client->preferences,
                    'notes' => $client->notes,
                    'allergies' => $client->allergies,
                    'medical_conditions' => $client->medical_conditions,
                    'created_at' => $client->created_at,
                    'updated_at' => $client->updated_at,
                ];
            }

            // Get appointments
            $appointments = Appointment::where('client_id', $user->id)
                ->with(['service', 'staff.user'])
                ->get()
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'service_name' => $appointment->service->name ?? 'Unknown',
                        'staff_name' => $appointment->staff->user->name ?? 'Unknown',
                        'appointment_date' => $appointment->appointment_date,
                        'status' => $appointment->status,
                        'notes' => $appointment->notes,
                        'created_at' => $appointment->created_at,
                    ];
                });

            $personalData['appointments'] = $appointments;

            // Get invoices
            $invoices = Invoice::where('client_id', $user->id)
                ->with(['items'])
                ->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'total_amount' => $invoice->total_amount,
                        'status' => $invoice->status,
                        'created_at' => $invoice->created_at,
                        'items' => $invoice->items->map(function ($item) {
                            return [
                                'service_name' => $item->service_name,
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                            ];
                        }),
                    ];
                });

            $personalData['invoices'] = $invoices;

            // Get payments
            $payments = Payment::where('client_id', $user->id)
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'status' => $payment->status,
                        'payment_date' => $payment->payment_date,
                        'created_at' => $payment->created_at,
                    ];
                });

            $personalData['payments'] = $payments;

            // Get audit logs
            $auditLogs = AuditLog::where('user_id', $userId)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->action,
                        'model_type' => $log->model_type,
                        'model_id' => $log->model_id,
                        'ip_address' => $log->ip_address,
                        'created_at' => $log->created_at,
                    ];
                });

            $personalData['audit_logs'] = $auditLogs;

            return $personalData;

        } catch (\Exception $e) {
            Log::error("Failed to get user personal data", [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Export user's personal data
     */
    public function exportUserData($userId, $format = 'json')
    {
        try {
            $personalData = $this->getUserPersonalData($userId);
            
            $filename = "gdpr_export_user_{$userId}_" . now()->format('Y-m-d_H-i-s');
            
            if ($format === 'json') {
                $filepath = storage_path("app/gdpr/exports/{$filename}.json");
                $this->ensureDirectoryExists(dirname($filepath));
                
                file_put_contents($filepath, json_encode($personalData, JSON_PRETTY_PRINT));
                
                return [
                    'success' => true,
                    'filename' => "{$filename}.json",
                    'filepath' => $filepath,
                    'size' => filesize($filepath),
                ];
            } elseif ($format === 'csv') {
                $filepath = storage_path("app/gdpr/exports/{$filename}.csv");
                $this->ensureDirectoryExists(dirname($filepath));
                
                $csvData = $this->convertToCsv($personalData);
                file_put_contents($filepath, $csvData);
                
                return [
                    'success' => true,
                    'filename' => "{$filename}.csv",
                    'filepath' => $filepath,
                    'size' => filesize($filepath),
                ];
            }

            throw new \Exception("Unsupported export format: {$format}");

        } catch (\Exception $e) {
            Log::error("Failed to export user data", [
                'user_id' => $userId,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Anonymize user data
     */
    public function anonymizeUserData($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $client = $user->client;

            // Create anonymized version
            $anonymizedData = [
                'original_id' => $user->id,
                'anonymized_at' => now(),
                'reason' => 'GDPR Right to be Forgotten',
            ];

            // Anonymize user data
            $user->update([
                'name' => 'Anonymized User',
                'email' => "anonymized_{$user->id}@deleted.local",
                'phone' => null,
                'address' => null,
                'date_of_birth' => null,
                'gender' => null,
                'is_active' => false,
                'anonymized_at' => now(),
            ]);

            // Anonymize client data
            if ($client) {
                $client->update([
                    'preferences' => null,
                    'notes' => 'Data anonymized for GDPR compliance',
                    'allergies' => null,
                    'medical_conditions' => null,
                    'anonymized_at' => now(),
                ]);
            }

            // Anonymize appointments
            Appointment::where('client_id', $userId)->update([
                'notes' => 'Appointment data anonymized for GDPR compliance',
                'anonymized_at' => now(),
            ]);

            // Log the anonymization
            Log::info("User data anonymized for GDPR compliance", [
                'user_id' => $userId,
                'anonymized_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'User data has been anonymized successfully',
                'anonymized_at' => now(),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to anonymize user data", [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete user data completely
     */
    public function deleteUserData($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Create deletion record
            $deletionRecord = [
                'user_id' => $userId,
                'user_email' => $user->email,
                'deleted_at' => now(),
                'reason' => 'GDPR Right to be Forgotten',
            ];

            // Delete related data
            $client = $user->client;
            if ($client) {
                $client->delete();
            }

            // Delete appointments
            Appointment::where('client_id', $userId)->delete();

            // Delete invoices
            Invoice::where('client_id', $userId)->delete();

            // Delete payments
            Payment::where('client_id', $userId)->delete();

            // Delete audit logs
            AuditLog::where('user_id', $userId)->delete();

            // Delete user
            $user->delete();

            // Log the deletion
            Log::info("User data deleted for GDPR compliance", [
                'user_id' => $userId,
                'deleted_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'User data has been deleted successfully',
                'deleted_at' => now(),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to delete user data", [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get data retention policy
     */
    public function getDataRetentionPolicy()
    {
        return [
            'user_data' => [
                'retention_period' => '7 years',
                'reason' => 'Legal and business requirements',
                'auto_delete' => false,
            ],
            'appointment_data' => [
                'retention_period' => '5 years',
                'reason' => 'Business records and analytics',
                'auto_delete' => false,
            ],
            'payment_data' => [
                'retention_period' => '7 years',
                'reason' => 'Legal and tax requirements',
                'auto_delete' => false,
            ],
            'audit_logs' => [
                'retention_period' => '3 years',
                'reason' => 'Security and compliance',
                'auto_delete' => true,
            ],
            'backup_data' => [
                'retention_period' => '1 year',
                'reason' => 'Disaster recovery',
                'auto_delete' => true,
            ],
        ];
    }

    /**
     * Check data retention compliance
     */
    public function checkDataRetentionCompliance()
    {
        $policy = $this->getDataRetentionPolicy();
        $violations = [];

        // Check user data
        $oldUsers = User::where('created_at', '<', now()->subYears(7))->count();
        if ($oldUsers > 0) {
            $violations[] = [
                'type' => 'user_data',
                'count' => $oldUsers,
                'retention_period' => $policy['user_data']['retention_period'],
                'action_required' => 'Review and anonymize if no longer needed',
            ];
        }

        // Check appointment data
        $oldAppointments = Appointment::where('created_at', '<', now()->subYears(5))->count();
        if ($oldAppointments > 0) {
            $violations[] = [
                'type' => 'appointment_data',
                'count' => $oldAppointments,
                'retention_period' => $policy['appointment_data']['retention_period'],
                'action_required' => 'Review and anonymize if no longer needed',
            ];
        }

        // Check audit logs
        $oldAuditLogs = AuditLog::where('created_at', '<', now()->subYears(3))->count();
        if ($oldAuditLogs > 0) {
            $violations[] = [
                'type' => 'audit_logs',
                'count' => $oldAuditLogs,
                'retention_period' => $policy['audit_logs']['retention_period'],
                'action_required' => 'Delete old audit logs',
            ];
        }

        return [
            'compliant' => empty($violations),
            'violations' => $violations,
            'checked_at' => now(),
        ];
    }

    /**
     * Get consent management data
     */
    public function getConsentManagement()
    {
        return [
            'marketing_emails' => [
                'required' => false,
                'default' => false,
                'description' => 'Receive marketing emails and promotions',
            ],
            'sms_notifications' => [
                'required' => false,
                'default' => false,
                'description' => 'Receive SMS notifications and reminders',
            ],
            'data_processing' => [
                'required' => true,
                'default' => true,
                'description' => 'Process personal data for service delivery',
            ],
            'analytics' => [
                'required' => false,
                'default' => false,
                'description' => 'Use data for analytics and improvement',
            ],
            'third_party_sharing' => [
                'required' => false,
                'default' => false,
                'description' => 'Share data with third-party service providers',
            ],
        ];
    }

    /**
     * Update user consent
     */
    public function updateUserConsent($userId, $consentData)
    {
        try {
            $user = User::findOrFail($userId);
            
            $currentConsent = $user->consent_preferences ?? [];
            $updatedConsent = array_merge($currentConsent, $consentData);
            
            $user->update([
                'consent_preferences' => $updatedConsent,
                'consent_updated_at' => now(),
            ]);

            // Log consent update
            Log::info("User consent updated", [
                'user_id' => $userId,
                'consent_data' => $consentData,
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Consent preferences updated successfully',
                'updated_at' => now(),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to update user consent", [
                'user_id' => $userId,
                'consent_data' => $consentData,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get GDPR compliance report
     */
    public function getComplianceReport()
    {
        $totalUsers = User::count();
        $usersWithConsent = User::whereNotNull('consent_preferences')->count();
        $dataRetentionCheck = $this->checkDataRetentionCompliance();
        
        return [
            'total_users' => $totalUsers,
            'users_with_consent' => $usersWithConsent,
            'consent_compliance_rate' => $totalUsers > 0 ? round(($usersWithConsent / $totalUsers) * 100, 2) : 0,
            'data_retention_compliance' => $dataRetentionCheck,
            'consent_management' => $this->getConsentManagement(),
            'data_retention_policy' => $this->getDataRetentionPolicy(),
            'generated_at' => now(),
        ];
    }

    /**
     * Convert data to CSV format
     */
    protected function convertToCsv($data)
    {
        $csv = '';
        
        // Add user data
        $csv .= "User Data\n";
        $csv .= "ID,Name,Email,Phone,Address,Date of Birth,Gender,Created At\n";
        $csv .= "{$data['user']['id']},{$data['user']['name']},{$data['user']['email']},{$data['user']['phone']},{$data['user']['address']},{$data['user']['date_of_birth']},{$data['user']['gender']},{$data['user']['created_at']}\n\n";
        
        // Add appointments
        $csv .= "Appointments\n";
        $csv .= "ID,Service,Staff,Date,Status,Notes,Created At\n";
        foreach ($data['appointments'] as $appointment) {
            $csv .= "{$appointment['id']},{$appointment['service_name']},{$appointment['staff_name']},{$appointment['appointment_date']},{$appointment['status']},{$appointment['notes']},{$appointment['created_at']}\n";
        }
        
        $csv .= "\n";
        
        // Add invoices
        $csv .= "Invoices\n";
        $csv .= "ID,Invoice Number,Total Amount,Status,Created At\n";
        foreach ($data['invoices'] as $invoice) {
            $csv .= "{$invoice['id']},{$invoice['invoice_number']},{$invoice['total_amount']},{$invoice['status']},{$invoice['created_at']}\n";
        }
        
        $csv .= "\n";
        
        // Add payments
        $csv .= "Payments\n";
        $csv .= "ID,Amount,Method,Status,Date,Created At\n";
        foreach ($data['payments'] as $payment) {
            $csv .= "{$payment['id']},{$payment['amount']},{$payment['payment_method']},{$payment['status']},{$payment['payment_date']},{$payment['created_at']}\n";
        }
        
        return $csv;
    }

    /**
     * Ensure directory exists
     */
    protected function ensureDirectoryExists($directory)
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
