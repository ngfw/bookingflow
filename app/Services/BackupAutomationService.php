<?php

namespace App\Services;

use App\Services\BackupService;
use App\Services\DataEncryptionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class BackupAutomationService
{
    protected $backupService;
    protected $encryptionService;

    public function __construct(BackupService $backupService, DataEncryptionService $encryptionService)
    {
        $this->backupService = $backupService;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Run scheduled backup
     */
    public function runScheduledBackup($backupType = 'full', $encrypt = true)
    {
        try {
            Log::info("Starting scheduled backup", [
                'backup_type' => $backupType,
                'encrypt' => $encrypt,
            ]);

            $result = null;

            switch ($backupType) {
                case 'full':
                    $result = $this->backupService->createFullBackup();
                    break;
                case 'database':
                    $result = $this->backupService->createDatabaseBackup();
                    break;
                case 'files':
                    $result = $this->backupService->createFilesBackup();
                    break;
                default:
                    throw new \Exception("Invalid backup type: {$backupType}");
            }

            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            // Encrypt backup if requested
            if ($encrypt) {
                $encryptedPath = $this->encryptionService->encryptBackup($result['file_path']);
                $result['file_path'] = $encryptedPath;
                $result['encrypted'] = true;
            }

            // Clean old backups
            $this->cleanOldBackups();

            // Send notification
            $this->sendBackupNotification($result, 'success');

            Log::info("Scheduled backup completed successfully", [
                'backup_name' => $result['backup_name'],
                'file_size' => $result['file_size'],
                'encrypted' => $encrypt,
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error("Scheduled backup failed", [
                'backup_type' => $backupType,
                'encrypt' => $encrypt,
                'error' => $e->getMessage(),
            ]);

            // Send failure notification
            $this->sendBackupNotification([
                'success' => false,
                'error' => $e->getMessage(),
                'backup_type' => $backupType,
            ], 'failure');

            throw $e;
        }
    }

    /**
     * Run daily backup
     */
    public function runDailyBackup()
    {
        return $this->runScheduledBackup('full', true);
    }

    /**
     * Run weekly backup
     */
    public function runWeeklyBackup()
    {
        return $this->runScheduledBackup('full', true);
    }

    /**
     * Run monthly backup
     */
    public function runMonthlyBackup()
    {
        return $this->runScheduledBackup('full', true);
    }

    /**
     * Run database backup
     */
    public function runDatabaseBackup()
    {
        return $this->runScheduledBackup('database', true);
    }

    /**
     * Run files backup
     */
    public function runFilesBackup()
    {
        return $this->runScheduledBackup('files', true);
    }

    /**
     * Test backup integrity
     */
    public function testBackupIntegrity($backupPath)
    {
        try {
            Log::info("Testing backup integrity", [
                'backup_path' => $backupPath,
            ]);

            // Check if backup file exists
            if (!file_exists($backupPath)) {
                throw new \Exception("Backup file not found: {$backupPath}");
            }

            // Check file size
            $fileSize = filesize($backupPath);
            if ($fileSize === 0) {
                throw new \Exception("Backup file is empty");
            }

            // Test ZIP integrity
            $zip = new \ZipArchive();
            $result = $zip->open($backupPath);
            
            if ($result !== TRUE) {
                throw new \Exception("Invalid ZIP file: {$result}");
            }

            $zip->close();

            // Test decryption if encrypted
            if (strpos($backupPath, '.encrypted') !== false) {
                $tempPath = $backupPath . '.test';
                $this->encryptionService->decryptBackup($backupPath, $tempPath);
                
                // Test decrypted backup
                $zip = new \ZipArchive();
                $result = $zip->open($tempPath);
                
                if ($result !== TRUE) {
                    unlink($tempPath);
                    throw new \Exception("Decrypted backup is invalid: {$result}");
                }
                
                $zip->close();
                unlink($tempPath);
            }

            Log::info("Backup integrity test passed", [
                'backup_path' => $backupPath,
                'file_size' => $fileSize,
            ]);

            return [
                'success' => true,
                'file_size' => $fileSize,
                'tested_at' => now(),
            ];

        } catch (\Exception $e) {
            Log::error("Backup integrity test failed", [
                'backup_path' => $backupPath,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'tested_at' => now(),
            ];
        }
    }

    /**
     * Clean old backups
     */
    public function cleanOldBackups($retentionDays = 30)
    {
        try {
            $backups = $this->backupService->listBackups();
            $cutoffDate = now()->subDays($retentionDays);
            $deletedCount = 0;

            foreach ($backups as $backup) {
                $backupDate = Carbon::parse($backup['created_at']);
                
                if ($backupDate->lt($cutoffDate)) {
                    $result = $this->backupService->deleteBackup($backup['path']);
                    
                    if ($result['success']) {
                        $deletedCount++;
                        Log::info("Old backup deleted", [
                            'backup_name' => $backup['name'],
                            'created_at' => $backup['created_at'],
                        ]);
                    }
                }
            }

            Log::info("Backup cleanup completed", [
                'deleted_count' => $deletedCount,
                'retention_days' => $retentionDays,
            ]);

            return [
                'success' => true,
                'deleted_count' => $deletedCount,
                'retention_days' => $retentionDays,
            ];

        } catch (\Exception $e) {
            Log::error("Backup cleanup failed", [
                'retention_days' => $retentionDays,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get backup schedule
     */
    public function getBackupSchedule()
    {
        return [
            'daily' => [
                'enabled' => true,
                'time' => '02:00',
                'type' => 'database',
                'encrypt' => true,
                'retention_days' => 7,
            ],
            'weekly' => [
                'enabled' => true,
                'day' => 'sunday',
                'time' => '03:00',
                'type' => 'full',
                'encrypt' => true,
                'retention_days' => 30,
            ],
            'monthly' => [
                'enabled' => true,
                'day' => 1,
                'time' => '04:00',
                'type' => 'full',
                'encrypt' => true,
                'retention_days' => 365,
            ],
        ];
    }

    /**
     * Update backup schedule
     */
    public function updateBackupSchedule($schedule)
    {
        try {
            // Validate schedule
            $this->validateBackupSchedule($schedule);

            // Store schedule in configuration
            config(['backup.schedule' => $schedule]);

            Log::info("Backup schedule updated", [
                'schedule' => $schedule,
            ]);

            return [
                'success' => true,
                'message' => 'Backup schedule updated successfully',
                'schedule' => $schedule,
            ];

        } catch (\Exception $e) {
            Log::error("Failed to update backup schedule", [
                'schedule' => $schedule,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate backup schedule
     */
    protected function validateBackupSchedule($schedule)
    {
        $requiredFields = ['daily', 'weekly', 'monthly'];
        
        foreach ($requiredFields as $field) {
            if (!isset($schedule[$field])) {
                throw new \Exception("Missing schedule field: {$field}");
            }
            
            if (!isset($schedule[$field]['enabled'])) {
                throw new \Exception("Missing enabled field for {$field}");
            }
            
            if (!isset($schedule[$field]['type'])) {
                throw new \Exception("Missing type field for {$field}");
            }
            
            $validTypes = ['full', 'database', 'files'];
            if (!in_array($schedule[$field]['type'], $validTypes)) {
                throw new \Exception("Invalid backup type for {$field}: {$schedule[$field]['type']}");
            }
        }
    }

    /**
     * Send backup notification
     */
    protected function sendBackupNotification($result, $status)
    {
        try {
            $notificationData = [
                'status' => $status,
                'result' => $result,
                'timestamp' => now(),
            ];

            // Log notification
            Log::info("Backup notification sent", [
                'status' => $status,
                'backup_name' => $result['backup_name'] ?? 'Unknown',
            ]);

            // Send email notification if configured
            if (config('backup.notifications.email.enabled')) {
                $this->sendEmailNotification($notificationData);
            }

            // Send webhook notification if configured
            if (config('backup.notifications.webhook.enabled')) {
                $this->sendWebhookNotification($notificationData);
            }

        } catch (\Exception $e) {
            Log::error("Failed to send backup notification", [
                'status' => $status,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification($notificationData)
    {
        try {
            $recipients = config('backup.notifications.email.recipients', []);
            
            if (empty($recipients)) {
                return;
            }

            $subject = $notificationData['status'] === 'success' 
                ? 'Backup Completed Successfully' 
                : 'Backup Failed';

            $message = $this->formatEmailMessage($notificationData);

            foreach ($recipients as $recipient) {
                Mail::raw($message, function ($mail) use ($recipient, $subject) {
                    $mail->to($recipient)
                         ->subject($subject);
                });
            }

        } catch (\Exception $e) {
            Log::error("Failed to send email notification", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send webhook notification
     */
    protected function sendWebhookNotification($notificationData)
    {
        try {
            $webhookUrl = config('backup.notifications.webhook.url');
            
            if (!$webhookUrl) {
                return;
            }

            $payload = [
                'event' => 'backup_' . $notificationData['status'],
                'data' => $notificationData,
                'timestamp' => now()->toISOString(),
            ];

            $response = \Http::post($webhookUrl, $payload);

            if (!$response->successful()) {
                throw new \Exception("Webhook request failed: " . $response->status());
            }

        } catch (\Exception $e) {
            Log::error("Failed to send webhook notification", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Format email message
     */
    protected function formatEmailMessage($notificationData)
    {
        $result = $notificationData['result'];
        $status = $notificationData['status'];
        
        if ($status === 'success') {
            return "Backup completed successfully!\n\n" .
                   "Backup Name: {$result['backup_name']}\n" .
                   "File Size: {$result['file_size']}\n" .
                   "Created At: {$result['created_at']}\n" .
                   "Encrypted: " . ($result['encrypted'] ? 'Yes' : 'No') . "\n\n" .
                   "The backup has been stored securely and is ready for use.";
        } else {
            return "Backup failed!\n\n" .
                   "Error: {$result['error']}\n" .
                   "Backup Type: {$result['backup_type']}\n" .
                   "Failed At: {$notificationData['timestamp']}\n\n" .
                   "Please check the logs for more details and take appropriate action.";
        }
    }

    /**
     * Get backup automation statistics
     */
    public function getBackupStatistics()
    {
        $backups = $this->backupService->listBackups();
        
        $totalBackups = count($backups);
        $totalSize = 0;
        $encryptedCount = 0;
        $recentBackups = 0;
        
        $weekAgo = now()->subWeek();
        
        foreach ($backups as $backup) {
            $totalSize += $this->parseFileSize($backup['size']);
            
            if (strpos($backup['name'], '.encrypted') !== false) {
                $encryptedCount++;
            }
            
            if (Carbon::parse($backup['created_at'])->gt($weekAgo)) {
                $recentBackups++;
            }
        }

        return [
            'total_backups' => $totalBackups,
            'total_size' => $this->formatFileSize($totalSize),
            'encrypted_backups' => $encryptedCount,
            'recent_backups' => $recentBackups,
            'encryption_rate' => $totalBackups > 0 ? round(($encryptedCount / $totalBackups) * 100, 2) : 0,
            'schedule' => $this->getBackupSchedule(),
        ];
    }

    /**
     * Parse file size string to bytes
     */
    protected function parseFileSize($sizeString)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = floatval($sizeString);
        $unit = strtoupper(substr($sizeString, -2));
        
        $unitIndex = array_search($unit, $units);
        if ($unitIndex !== false) {
            return $size * pow(1024, $unitIndex);
        }
        
        return $size;
    }

    /**
     * Format bytes to human readable size
     */
    protected function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
