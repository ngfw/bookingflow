<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate a secret key for a user
     */
    public function generateSecretKey($user)
    {
        try {
            $secretKey = $this->google2fa->generateSecretKey();
            
            // Store the secret key temporarily (encrypted)
            $encryptedSecret = encrypt($secretKey);
            Cache::put("2fa_secret_{$user->id}", $encryptedSecret, 600); // 10 minutes
            
            return $secretKey;
            
        } catch (\Exception $e) {
            Log::error("Failed to generate 2FA secret key for user {$user->id}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get QR code URL for the secret key
     */
    public function getQRCodeUrl($user, $secretKey)
    {
        try {
            $companyName = config('app.name', 'BookingFlow');
            $companyEmail = config('mail.from.address', 'admin@bookingflow.com');
            
            $qrCodeUrl = $this->google2fa->getQRCodeUrl(
                $companyName,
                $user->email,
                $secretKey
            );
            
            return $qrCodeUrl;
            
        } catch (\Exception $e) {
            Log::error("Failed to generate QR code URL for user {$user->id}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Verify the 2FA code
     */
    public function verifyCode($user, $code)
    {
        try {
            if (!$user->two_factor_secret) {
                return false;
            }

            $secretKey = decrypt($user->two_factor_secret);
            $valid = $this->google2fa->verifyKey($secretKey, $code);
            
            if ($valid) {
                // Log successful 2FA verification
                Log::info("2FA verification successful for user {$user->id}");
            } else {
                // Log failed 2FA attempt
                Log::warning("2FA verification failed for user {$user->id}", [
                    'code' => $code,
                    'ip' => request()->ip(),
                ]);
            }
            
            return $valid;
            
        } catch (\Exception $e) {
            Log::error("Failed to verify 2FA code for user {$user->id}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Enable 2FA for a user
     */
    public function enable2FA($user, $code)
    {
        try {
            // Get the temporary secret key
            $encryptedSecret = Cache::get("2fa_secret_{$user->id}");
            
            if (!$encryptedSecret) {
                throw new \Exception("2FA setup session expired. Please start over.");
            }
            
            $secretKey = decrypt($encryptedSecret);
            
            // Verify the code
            if (!$this->google2fa->verifyKey($secretKey, $code)) {
                throw new \Exception("Invalid verification code.");
            }
            
            // Store the encrypted secret key
            $user->update([
                'two_factor_secret' => encrypt($secretKey),
                'two_factor_enabled' => true,
                'two_factor_enabled_at' => now(),
            ]);
            
            // Clear the temporary secret
            Cache::forget("2fa_secret_{$user->id}");
            
            // Log 2FA enablement
            Log::info("2FA enabled for user {$user->id}");
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to enable 2FA for user {$user->id}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Disable 2FA for a user
     */
    public function disable2FA($user, $code)
    {
        try {
            // Verify the code before disabling
            if (!$this->verifyCode($user, $code)) {
                throw new \Exception("Invalid verification code.");
            }
            
            // Clear 2FA data
            $user->update([
                'two_factor_secret' => null,
                'two_factor_enabled' => false,
                'two_factor_enabled_at' => null,
                'two_factor_disabled_at' => now(),
            ]);
            
            // Log 2FA disablement
            Log::info("2FA disabled for user {$user->id}");
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to disable 2FA for user {$user->id}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate recovery codes
     */
    public function generateRecoveryCodes($user, $count = 8)
    {
        try {
            $codes = [];
            for ($i = 0; $i < $count; $i++) {
                $codes[] = strtoupper(substr(md5(uniqid() . $user->id . time()), 0, 8));
            }
            
            // Store hashed recovery codes
            $hashedCodes = array_map('Hash::make', $codes);
            $user->update([
                'two_factor_recovery_codes' => json_encode($hashedCodes),
            ]);
            
            // Log recovery codes generation
            Log::info("Recovery codes generated for user {$user->id}");
            
            return $codes;
            
        } catch (\Exception $e) {
            Log::error("Failed to generate recovery codes for user {$user->id}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Verify recovery code
     */
    public function verifyRecoveryCode($user, $code)
    {
        try {
            if (!$user->two_factor_recovery_codes) {
                return false;
            }
            
            $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
            
            foreach ($recoveryCodes as $index => $hashedCode) {
                if (Hash::check($code, $hashedCode)) {
                    // Remove the used recovery code
                    unset($recoveryCodes[$index]);
                    $user->update([
                        'two_factor_recovery_codes' => json_encode(array_values($recoveryCodes)),
                    ]);
                    
                    // Log recovery code usage
                    Log::info("Recovery code used for user {$user->id}");
                    
                    return true;
                }
            }
            
            // Log failed recovery code attempt
            Log::warning("Invalid recovery code used for user {$user->id}", [
                'code' => $code,
                'ip' => request()->ip(),
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::error("Failed to verify recovery code for user {$user->id}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check if user has 2FA enabled
     */
    public function is2FAEnabled($user)
    {
        return $user->two_factor_enabled && $user->two_factor_secret;
    }

    /**
     * Check if user has recovery codes
     */
    public function hasRecoveryCodes($user)
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }
        
        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        return !empty($recoveryCodes);
    }

    /**
     * Get remaining recovery codes count
     */
    public function getRemainingRecoveryCodes($user)
    {
        if (!$user->two_factor_recovery_codes) {
            return 0;
        }
        
        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        return count($recoveryCodes);
    }

    /**
     * Get 2FA status for user
     */
    public function get2FAStatus($user)
    {
        return [
            'enabled' => $this->is2FAEnabled($user),
            'enabled_at' => $user->two_factor_enabled_at,
            'has_recovery_codes' => $this->hasRecoveryCodes($user),
            'remaining_recovery_codes' => $this->getRemainingRecoveryCodes($user),
        ];
    }

    /**
     * Require 2FA for specific actions
     */
    public function require2FA($user, $action)
    {
        $actions = [
            'password_change',
            'email_change',
            'profile_update',
            'payment_processing',
            'admin_actions',
        ];
        
        return in_array($action, $actions) && $this->is2FAEnabled($user);
    }

    /**
     * Get backup codes for display
     */
    public function getBackupCodes($user)
    {
        if (!$user->two_factor_recovery_codes) {
            return [];
        }
        
        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        
        // Return masked codes for display
        return array_map(function ($hashedCode) {
            return '****' . substr($hashedCode, -4);
        }, $recoveryCodes);
    }

    /**
     * Validate 2FA setup
     */
    public function validate2FASetup($user)
    {
        $errors = [];
        
        if (!$this->is2FAEnabled($user)) {
            $errors[] = '2FA is not enabled';
        }
        
        if (!$this->hasRecoveryCodes($user)) {
            $errors[] = 'No recovery codes available';
        }
        
        if ($this->getRemainingRecoveryCodes($user) < 3) {
            $errors[] = 'Low recovery codes count';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get 2FA statistics
     */
    public function get2FAStatistics()
    {
        $totalUsers = User::count();
        $enabledUsers = User::where('two_factor_enabled', true)->count();
        $usersWithRecoveryCodes = User::whereNotNull('two_factor_recovery_codes')->count();
        
        return [
            'total_users' => $totalUsers,
            'enabled_users' => $enabledUsers,
            'enabled_percentage' => $totalUsers > 0 ? round(($enabledUsers / $totalUsers) * 100, 2) : 0,
            'users_with_recovery_codes' => $usersWithRecoveryCodes,
            'recovery_codes_percentage' => $totalUsers > 0 ? round(($usersWithRecoveryCodes / $totalUsers) * 100, 2) : 0,
        ];
    }
}
