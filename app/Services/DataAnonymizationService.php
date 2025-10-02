<?php

namespace App\Services;

use App\Models\User;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataAnonymizationService
{
    /**
     * Anonymize user data
     */
    public function anonymizeUser($userId, $reason = 'Data anonymization request')
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);
            $client = $user->client;

            // Create anonymization record
            $anonymizationRecord = [
                'user_id' => $userId,
                'original_email' => $user->email,
                'original_name' => $user->name,
                'anonymized_at' => now(),
                'reason' => $reason,
            ];

            // Anonymize user data
            $user->update([
                'name' => $this->generateAnonymizedName(),
                'email' => $this->generateAnonymizedEmail($userId),
                'phone' => $this->generateAnonymizedPhone(),
                'address' => $this->generateAnonymizedAddress(),
                'date_of_birth' => null,
                'gender' => null,
                'is_active' => false,
                'anonymized_at' => now(),
                'anonymization_reason' => $reason,
            ]);

            // Anonymize client data
            if ($client) {
                $client->update([
                    'preferences' => null,
                    'notes' => 'Client data anonymized for privacy protection',
                    'allergies' => null,
                    'medical_conditions' => null,
                    'anonymized_at' => now(),
                    'anonymization_reason' => $reason,
                ]);
            }

            // Anonymize appointments
            Appointment::where('client_id', $userId)->update([
                'notes' => 'Appointment data anonymized for privacy protection',
                'anonymized_at' => now(),
                'anonymization_reason' => $reason,
            ]);

            // Log anonymization
            Log::info("User data anonymized", [
                'user_id' => $userId,
                'reason' => $reason,
                'anonymized_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'User data has been anonymized successfully',
                'anonymized_at' => now(),
                'anonymization_record' => $anonymizationRecord,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to anonymize user data", [
                'user_id' => $userId,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Anonymize multiple users
     */
    public function anonymizeUsers($userIds, $reason = 'Bulk data anonymization')
    {
        $results = [];
        $errors = [];

        foreach ($userIds as $userId) {
            try {
                $result = $this->anonymizeUser($userId, $reason);
                $results[] = $result;
            } catch (\Exception $e) {
                $errors[] = [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => empty($errors),
            'anonymized_count' => count($results),
            'error_count' => count($errors),
            'results' => $results,
            'errors' => $errors,
        ];
    }

    /**
     * Anonymize old data based on retention policy
     */
    public function anonymizeOldData($retentionYears = 7)
    {
        try {
            $cutoffDate = now()->subYears($retentionYears);
            
            // Find old users
            $oldUsers = User::where('created_at', '<', $cutoffDate)
                ->where('anonymized_at', null)
                ->get();

            $results = [];
            $errors = [];

            foreach ($oldUsers as $user) {
                try {
                    $result = $this->anonymizeUser($user->id, "Automatic anonymization after {$retentionYears} years");
                    $results[] = $result;
                } catch (\Exception $e) {
                    $errors[] = [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            Log::info("Bulk anonymization completed", [
                'retention_years' => $retentionYears,
                'cutoff_date' => $cutoffDate,
                'anonymized_count' => count($results),
                'error_count' => count($errors),
            ]);

            return [
                'success' => true,
                'retention_years' => $retentionYears,
                'cutoff_date' => $cutoffDate,
                'anonymized_count' => count($results),
                'error_count' => count($errors),
                'results' => $results,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            Log::error("Failed to anonymize old data", [
                'retention_years' => $retentionYears,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Anonymize specific data fields
     */
    public function anonymizeFields($model, $fields, $reason = 'Field anonymization')
    {
        try {
            $anonymizedData = [];
            
            foreach ($fields as $field) {
                switch ($field) {
                    case 'name':
                        $anonymizedData[$field] = $this->generateAnonymizedName();
                        break;
                    case 'email':
                        $anonymizedData[$field] = $this->generateAnonymizedEmail($model->id);
                        break;
                    case 'phone':
                        $anonymizedData[$field] = $this->generateAnonymizedPhone();
                        break;
                    case 'address':
                        $anonymizedData[$field] = $this->generateAnonymizedAddress();
                        break;
                    case 'date_of_birth':
                        $anonymizedData[$field] = null;
                        break;
                    case 'gender':
                        $anonymizedData[$field] = null;
                        break;
                    default:
                        $anonymizedData[$field] = 'Anonymized';
                        break;
                }
            }

            $anonymizedData['anonymized_at'] = now();
            $anonymizedData['anonymization_reason'] = $reason;

            $model->update($anonymizedData);

            Log::info("Fields anonymized", [
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'fields' => $fields,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'message' => 'Fields anonymized successfully',
                'anonymized_fields' => $fields,
                'anonymized_at' => now(),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to anonymize fields", [
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'fields' => $fields,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate anonymized name
     */
    protected function generateAnonymizedName()
    {
        $firstNames = ['John', 'Jane', 'Alex', 'Sam', 'Chris', 'Taylor', 'Jordan', 'Casey'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis'];
        
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        
        return "{$firstName} {$lastName}";
    }

    /**
     * Generate anonymized email
     */
    protected function generateAnonymizedEmail($userId)
    {
        return "anonymized_user_{$userId}@anonymized.local";
    }

    /**
     * Generate anonymized phone
     */
    protected function generateAnonymizedPhone()
    {
        return '+1-555-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate anonymized address
     */
    protected function generateAnonymizedAddress()
    {
        $streets = ['Main St', 'Oak Ave', 'Pine Rd', 'Cedar Ln', 'Elm St', 'Maple Ave'];
        $cities = ['Anytown', 'Somewhere', 'Nowhere', 'Anywhere', 'Elsewhere'];
        $states = ['CA', 'NY', 'TX', 'FL', 'IL'];
        
        $street = rand(100, 9999) . ' ' . $streets[array_rand($streets)];
        $city = $cities[array_rand($cities)];
        $state = $states[array_rand($states)];
        $zip = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        
        return "{$street}, {$city}, {$state} {$zip}";
    }

    /**
     * Get anonymization statistics
     */
    public function getAnonymizationStatistics()
    {
        $totalUsers = User::count();
        $anonymizedUsers = User::whereNotNull('anonymized_at')->count();
        $activeUsers = User::where('is_active', true)->count();
        
        return [
            'total_users' => $totalUsers,
            'anonymized_users' => $anonymizedUsers,
            'active_users' => $activeUsers,
            'anonymization_rate' => $totalUsers > 0 ? round(($anonymizedUsers / $totalUsers) * 100, 2) : 0,
            'recent_anonymizations' => User::whereNotNull('anonymized_at')
                ->where('anonymized_at', '>=', now()->subDays(30))
                ->count(),
        ];
    }

    /**
     * Get anonymization history
     */
    public function getAnonymizationHistory($limit = 50)
    {
        return User::whereNotNull('anonymized_at')
            ->select(['id', 'name', 'email', 'anonymized_at', 'anonymization_reason'])
            ->orderBy('anonymized_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'anonymized_at' => $user->anonymized_at,
                    'reason' => $user->anonymization_reason,
                ];
            });
    }

    /**
     * Check if user is anonymized
     */
    public function isUserAnonymized($userId)
    {
        $user = User::find($userId);
        return $user && $user->anonymized_at !== null;
    }

    /**
     * Get anonymization reasons
     */
    public function getAnonymizationReasons()
    {
        return [
            'gdpr_request' => 'GDPR Right to be Forgotten',
            'user_request' => 'User Request',
            'retention_policy' => 'Data Retention Policy',
            'privacy_protection' => 'Privacy Protection',
            'legal_requirement' => 'Legal Requirement',
            'security_breach' => 'Security Breach Response',
            'automatic_cleanup' => 'Automatic Data Cleanup',
        ];
    }

    /**
     * Validate anonymization request
     */
    public function validateAnonymizationRequest($userId, $reason)
    {
        $errors = [];

        // Check if user exists
        $user = User::find($userId);
        if (!$user) {
            $errors[] = 'User not found';
        }

        // Check if user is already anonymized
        if ($user && $user->anonymized_at) {
            $errors[] = 'User is already anonymized';
        }

        // Validate reason
        $validReasons = array_keys($this->getAnonymizationReasons());
        if (!in_array($reason, $validReasons)) {
            $errors[] = 'Invalid anonymization reason';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Export anonymization report
     */
    public function exportAnonymizationReport($format = 'csv')
    {
        try {
            $history = $this->getAnonymizationHistory(1000);
            $statistics = $this->getAnonymizationStatistics();
            
            $filename = 'anonymization_report_' . now()->format('Y-m-d_H-i-s');
            
            if ($format === 'csv') {
                $filepath = storage_path("app/reports/{$filename}.csv");
                $this->ensureDirectoryExists(dirname($filepath));
                
                $csv = "Anonymization Report\n";
                $csv .= "Generated: " . now() . "\n\n";
                
                $csv .= "Statistics\n";
                $csv .= "Total Users,Anonymized Users,Active Users,Anonymization Rate\n";
                $csv .= "{$statistics['total_users']},{$statistics['anonymized_users']},{$statistics['active_users']},{$statistics['anonymization_rate']}%\n\n";
                
                $csv .= "Anonymization History\n";
                $csv .= "ID,Name,Email,Anonymized At,Reason\n";
                foreach ($history as $record) {
                    $csv .= "{$record['id']},{$record['name']},{$record['email']},{$record['anonymized_at']},{$record['reason']}\n";
                }
                
                file_put_contents($filepath, $csv);
                
                return [
                    'success' => true,
                    'filename' => "{$filename}.csv",
                    'filepath' => $filepath,
                    'size' => filesize($filepath),
                ];
            }

            throw new \Exception("Unsupported export format: {$format}");

        } catch (\Exception $e) {
            Log::error("Failed to export anonymization report", [
                'format' => $format,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
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
