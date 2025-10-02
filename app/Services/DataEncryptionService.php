<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Encryption\DecryptException;

class DataEncryptionService
{
    protected $encryptionKey;
    protected $algorithm = 'AES-256-CBC';

    public function __construct()
    {
        $this->encryptionKey = config('app.key');
    }

    /**
     * Encrypt sensitive data
     */
    public function encrypt($data, $key = null)
    {
        try {
            if (is_array($data) || is_object($data)) {
                $data = json_encode($data);
            }

            $key = $key ?: $this->encryptionKey;
            $encrypted = Crypt::encryptString($data);
            
            return $encrypted;
            
        } catch (\Exception $e) {
            Log::error("Data encryption failed", [
                'error' => $e->getMessage(),
                'data_type' => gettype($data),
            ]);
            throw new \Exception("Encryption failed: " . $e->getMessage());
        }
    }

    /**
     * Decrypt sensitive data
     */
    public function decrypt($encryptedData, $key = null)
    {
        try {
            $key = $key ?: $this->encryptionKey;
            $decrypted = Crypt::decryptString($encryptedData);
            
            // Try to decode as JSON, return as string if it fails
            $jsonDecoded = json_decode($decrypted, true);
            return $jsonDecoded !== null ? $jsonDecoded : $decrypted;
            
        } catch (DecryptException $e) {
            Log::error("Data decryption failed", [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception("Decryption failed: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Data decryption error", [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception("Decryption error: " . $e->getMessage());
        }
    }

    /**
     * Encrypt file content
     */
    public function encryptFile($filePath, $outputPath = null)
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception("File not found: {$filePath}");
            }

            $content = file_get_contents($filePath);
            $encryptedContent = $this->encrypt($content);
            
            $outputPath = $outputPath ?: $filePath . '.encrypted';
            file_put_contents($outputPath, $encryptedContent);
            
            return $outputPath;
            
        } catch (\Exception $e) {
            Log::error("File encryption failed", [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Decrypt file content
     */
    public function decryptFile($encryptedFilePath, $outputPath = null)
    {
        try {
            if (!file_exists($encryptedFilePath)) {
                throw new \Exception("Encrypted file not found: {$encryptedFilePath}");
            }

            $encryptedContent = file_get_contents($encryptedFilePath);
            $decryptedContent = $this->decrypt($encryptedContent);
            
            $outputPath = $outputPath ?: str_replace('.encrypted', '', $encryptedFilePath);
            file_put_contents($outputPath, $decryptedContent);
            
            return $outputPath;
            
        } catch (\Exception $e) {
            Log::error("File decryption failed", [
                'file_path' => $encryptedFilePath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Encrypt database field
     */
    public function encryptField($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        return $this->encrypt($value);
    }

    /**
     * Decrypt database field
     */
    public function decryptField($encryptedValue)
    {
        if (empty($encryptedValue)) {
            return $encryptedValue;
        }
        
        try {
            return $this->decrypt($encryptedValue);
        } catch (\Exception $e) {
            Log::warning("Failed to decrypt field, returning original value", [
                'error' => $e->getMessage(),
            ]);
            return $encryptedValue;
        }
    }

    /**
     * Encrypt sensitive user data
     */
    public function encryptUserData($userData)
    {
        $sensitiveFields = [
            'phone',
            'address',
            'date_of_birth',
            'banking_info',
            'tax_id',
            'social_security_number',
        ];

        $encryptedData = $userData;
        
        foreach ($sensitiveFields as $field) {
            if (isset($userData[$field]) && !empty($userData[$field])) {
                $encryptedData[$field] = $this->encrypt($userData[$field]);
            }
        }

        return $encryptedData;
    }

    /**
     * Decrypt sensitive user data
     */
    public function decryptUserData($encryptedUserData)
    {
        $sensitiveFields = [
            'phone',
            'address',
            'date_of_birth',
            'banking_info',
            'tax_id',
            'social_security_number',
        ];

        $decryptedData = $encryptedUserData;
        
        foreach ($sensitiveFields as $field) {
            if (isset($encryptedUserData[$field]) && !empty($encryptedUserData[$field])) {
                try {
                    $decryptedData[$field] = $this->decrypt($encryptedUserData[$field]);
                } catch (\Exception $e) {
                    Log::warning("Failed to decrypt user field: {$field}", [
                        'user_id' => $encryptedUserData['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);
                    $decryptedData[$field] = $encryptedUserData[$field];
                }
            }
        }

        return $decryptedData;
    }

    /**
     * Encrypt payment data
     */
    public function encryptPaymentData($paymentData)
    {
        $sensitiveFields = [
            'card_number',
            'cvv',
            'bank_account_number',
            'routing_number',
            'billing_address',
        ];

        $encryptedData = $paymentData;
        
        foreach ($sensitiveFields as $field) {
            if (isset($paymentData[$field]) && !empty($paymentData[$field])) {
                $encryptedData[$field] = $this->encrypt($paymentData[$field]);
            }
        }

        return $encryptedData;
    }

    /**
     * Decrypt payment data
     */
    public function decryptPaymentData($encryptedPaymentData)
    {
        $sensitiveFields = [
            'card_number',
            'cvv',
            'bank_account_number',
            'routing_number',
            'billing_address',
        ];

        $decryptedData = $encryptedPaymentData;
        
        foreach ($sensitiveFields as $field) {
            if (isset($encryptedPaymentData[$field]) && !empty($encryptedPaymentData[$field])) {
                try {
                    $decryptedData[$field] = $this->decrypt($encryptedPaymentData[$field]);
                } catch (\Exception $e) {
                    Log::warning("Failed to decrypt payment field: {$field}", [
                        'payment_id' => $encryptedPaymentData['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);
                    $decryptedData[$field] = $encryptedPaymentData[$field];
                }
            }
        }

        return $decryptedData;
    }

    /**
     * Generate secure hash
     */
    public function generateHash($data, $algorithm = 'sha256')
    {
        return hash($algorithm, $data . $this->encryptionKey);
    }

    /**
     * Verify hash
     */
    public function verifyHash($data, $hash, $algorithm = 'sha256')
    {
        $expectedHash = $this->generateHash($data, $algorithm);
        return hash_equals($expectedHash, $hash);
    }

    /**
     * Generate secure random string
     */
    public function generateRandomString($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Generate secure token
     */
    public function generateSecureToken($length = 64)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Encrypt configuration data
     */
    public function encryptConfig($configData)
    {
        $sensitiveConfigKeys = [
            'database.password',
            'mail.password',
            'services.twilio.auth_token',
            'services.stripe.secret',
            'services.paypal.client_secret',
        ];

        $encryptedConfig = $configData;
        
        foreach ($sensitiveConfigKeys as $key) {
            $keys = explode('.', $key);
            $value = $configData;
            
            foreach ($keys as $k) {
                if (isset($value[$k])) {
                    $value = $value[$k];
                } else {
                    $value = null;
                    break;
                }
            }
            
            if ($value !== null) {
                $encryptedValue = $this->encrypt($value);
                
                // Set encrypted value back to config
                $config = &$encryptedConfig;
                foreach ($keys as $k) {
                    if (!isset($config[$k])) {
                        $config[$k] = [];
                    }
                    $config = &$config[$k];
                }
                $config = $encryptedValue;
            }
        }

        return $encryptedConfig;
    }

    /**
     * Decrypt configuration data
     */
    public function decryptConfig($encryptedConfig)
    {
        $sensitiveConfigKeys = [
            'database.password',
            'mail.password',
            'services.twilio.auth_token',
            'services.stripe.secret',
            'services.paypal.client_secret',
        ];

        $decryptedConfig = $encryptedConfig;
        
        foreach ($sensitiveConfigKeys as $key) {
            $keys = explode('.', $key);
            $value = $encryptedConfig;
            
            foreach ($keys as $k) {
                if (isset($value[$k])) {
                    $value = $value[$k];
                } else {
                    $value = null;
                    break;
                }
            }
            
            if ($value !== null) {
                try {
                    $decryptedValue = $this->decrypt($value);
                    
                    // Set decrypted value back to config
                    $config = &$decryptedConfig;
                    foreach ($keys as $k) {
                        if (!isset($config[$k])) {
                            $config[$k] = [];
                        }
                        $config = &$config[$k];
                    }
                    $config = $decryptedValue;
                } catch (\Exception $e) {
                    Log::warning("Failed to decrypt config key: {$key}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $decryptedConfig;
    }

    /**
     * Encrypt backup data
     */
    public function encryptBackup($backupPath, $outputPath = null)
    {
        try {
            $outputPath = $outputPath ?: $backupPath . '.encrypted';
            
            // Read backup file
            $backupContent = file_get_contents($backupPath);
            
            // Encrypt content
            $encryptedContent = $this->encrypt($backupContent);
            
            // Write encrypted backup
            file_put_contents($outputPath, $encryptedContent);
            
            // Remove original backup file
            unlink($backupPath);
            
            return $outputPath;
            
        } catch (\Exception $e) {
            Log::error("Backup encryption failed", [
                'backup_path' => $backupPath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Decrypt backup data
     */
    public function decryptBackup($encryptedBackupPath, $outputPath = null)
    {
        try {
            $outputPath = $outputPath ?: str_replace('.encrypted', '', $encryptedBackupPath);
            
            // Read encrypted backup
            $encryptedContent = file_get_contents($encryptedBackupPath);
            
            // Decrypt content
            $decryptedContent = $this->decrypt($encryptedContent);
            
            // Write decrypted backup
            file_put_contents($outputPath, $decryptedContent);
            
            return $outputPath;
            
        } catch (\Exception $e) {
            Log::error("Backup decryption failed", [
                'backup_path' => $encryptedBackupPath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get encryption statistics
     */
    public function getEncryptionStatistics()
    {
        return [
            'algorithm' => $this->algorithm,
            'key_length' => strlen($this->encryptionKey),
            'encryption_enabled' => true,
            'last_key_rotation' => config('app.encryption_key_rotated_at', 'Never'),
        ];
    }

    /**
     * Test encryption/decryption
     */
    public function testEncryption()
    {
        try {
            $testData = 'Test encryption data: ' . time();
            $encrypted = $this->encrypt($testData);
            $decrypted = $this->decrypt($encrypted);
            
            return [
                'success' => $testData === $decrypted,
                'original' => $testData,
                'encrypted' => $encrypted,
                'decrypted' => $decrypted,
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
