<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use ZipArchive;

class BackupService
{
    protected $backupPath;
    protected $maxBackups = 30; // Keep last 30 backups

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        
        // Create backup directory if it doesn't exist
        if (!file_exists($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    /**
     * Create a full system backup
     */
    public function createFullBackup($includeFiles = true)
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "full_backup_{$timestamp}";
            $backupDir = $this->backupPath . '/' . $backupName;
            
            // Create backup directory
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Backup database
            $this->backupDatabase($backupDir);

            // Backup files if requested
            if ($includeFiles) {
                $this->backupFiles($backupDir);
            }

            // Create backup manifest
            $this->createBackupManifest($backupDir, $backupName, $includeFiles);

            // Create compressed archive
            $zipPath = $this->createZipArchive($backupDir, $backupName);

            // Clean up temporary directory
            $this->removeDirectory($backupDir);

            // Clean old backups
            $this->cleanOldBackups();

            Log::info("Full backup created successfully: {$backupName}");

            return [
                'success' => true,
                'backup_name' => $backupName,
                'file_path' => $zipPath,
                'file_size' => $this->getFileSize($zipPath),
                'created_at' => Carbon::now()->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to create full backup", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create database-only backup
     */
    public function createDatabaseBackup()
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "database_backup_{$timestamp}";
            $backupDir = $this->backupPath . '/' . $backupName;
            
            // Create backup directory
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Backup database
            $this->backupDatabase($backupDir);

            // Create backup manifest
            $this->createBackupManifest($backupDir, $backupName, false);

            // Create compressed archive
            $zipPath = $this->createZipArchive($backupDir, $backupName);

            // Clean up temporary directory
            $this->removeDirectory($backupDir);

            Log::info("Database backup created successfully: {$backupName}");

            return [
                'success' => true,
                'backup_name' => $backupName,
                'file_path' => $zipPath,
                'file_size' => $this->getFileSize($zipPath),
                'created_at' => Carbon::now()->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to create database backup", [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create files-only backup
     */
    public function createFilesBackup()
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "files_backup_{$timestamp}";
            $backupDir = $this->backupPath . '/' . $backupName;
            
            // Create backup directory
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Backup files
            $this->backupFiles($backupDir);

            // Create backup manifest
            $this->createBackupManifest($backupDir, $backupName, true);

            // Create compressed archive
            $zipPath = $this->createZipArchive($backupDir, $backupName);

            // Clean up temporary directory
            $this->removeDirectory($backupDir);

            Log::info("Files backup created successfully: {$backupName}");

            return [
                'success' => true,
                'backup_name' => $backupName,
                'file_path' => $zipPath,
                'file_size' => $this->getFileSize($zipPath),
                'created_at' => Carbon::now()->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to create files backup", [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Restore from backup
     */
    public function restoreFromBackup($backupPath, $restoreType = 'full')
    {
        try {
            if (!file_exists($backupPath)) {
                throw new \Exception("Backup file not found: {$backupPath}");
            }

            $tempDir = $this->backupPath . '/restore_' . uniqid();
            
            // Extract backup
            $this->extractZipArchive($backupPath, $tempDir);

            // Read manifest
            $manifest = $this->readBackupManifest($tempDir);

            if (!$manifest) {
                throw new \Exception("Invalid backup: manifest not found");
            }

            $results = [];

            // Restore database
            if (in_array($restoreType, ['full', 'database']) && $manifest['includes_database']) {
                $results['database'] = $this->restoreDatabase($tempDir);
            }

            // Restore files
            if (in_array($restoreType, ['full', 'files']) && $manifest['includes_files']) {
                $results['files'] = $this->restoreFiles($tempDir);
            }

            // Clean up temporary directory
            $this->removeDirectory($tempDir);

            Log::info("Backup restored successfully", [
                'backup_path' => $backupPath,
                'restore_type' => $restoreType,
                'results' => $results,
            ]);

            return [
                'success' => true,
                'restore_type' => $restoreType,
                'results' => $results,
                'restored_at' => Carbon::now()->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to restore backup", [
                'backup_path' => $backupPath,
                'restore_type' => $restoreType,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * List available backups
     */
    public function listBackups()
    {
        $backups = [];
        $files = glob($this->backupPath . '/*.zip');

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => $this->getFileSize($file),
                'created_at' => Carbon::createFromTimestamp(filemtime($file))->toISOString(),
                'type' => $this->getBackupType($file),
            ];
        }

        // Sort by creation date (newest first)
        usort($backups, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $backups;
    }

    /**
     * Delete backup
     */
    public function deleteBackup($backupPath)
    {
        try {
            if (!file_exists($backupPath)) {
                throw new \Exception("Backup file not found: {$backupPath}");
            }

            unlink($backupPath);

            Log::info("Backup deleted successfully: {$backupPath}");

            return [
                'success' => true,
                'message' => 'Backup deleted successfully',
            ];

        } catch (\Exception $e) {
            Log::error("Failed to delete backup", [
                'backup_path' => $backupPath,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Backup database
     */
    protected function backupDatabase($backupDir)
    {
        $config = config('database.connections.mysql');
        $filename = $backupDir . '/database.sql';
        
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port']),
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['database']),
            escapeshellarg($filename)
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception("Database backup failed: " . implode("\n", $output));
        }

        if (!file_exists($filename) || filesize($filename) === 0) {
            throw new \Exception("Database backup file is empty or missing");
        }
    }

    /**
     * Backup files
     */
    protected function backupFiles($backupDir)
    {
        $filesDir = $backupDir . '/files';
        mkdir($filesDir, 0755, true);

        // Backup storage/app/public
        $publicPath = storage_path('app/public');
        if (is_dir($publicPath)) {
            $this->copyDirectory($publicPath, $filesDir . '/storage_app_public');
        }

        // Backup public/uploads (if exists)
        $uploadsPath = public_path('uploads');
        if (is_dir($uploadsPath)) {
            $this->copyDirectory($uploadsPath, $filesDir . '/public_uploads');
        }

        // Backup .env file
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            copy($envPath, $filesDir . '/.env');
        }
    }

    /**
     * Restore database
     */
    protected function restoreDatabase($tempDir)
    {
        $sqlFile = $tempDir . '/database.sql';
        
        if (!file_exists($sqlFile)) {
            throw new \Exception("Database backup file not found");
        }

        $config = config('database.connections.mysql');
        
        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port']),
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['database']),
            escapeshellarg($sqlFile)
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception("Database restore failed: " . implode("\n", $output));
        }

        return [
            'success' => true,
            'message' => 'Database restored successfully',
        ];
    }

    /**
     * Restore files
     */
    protected function restoreFiles($tempDir)
    {
        $filesDir = $tempDir . '/files';
        
        if (!is_dir($filesDir)) {
            throw new \Exception("Files backup directory not found");
        }

        $results = [];

        // Restore storage/app/public
        $sourcePath = $filesDir . '/storage_app_public';
        if (is_dir($sourcePath)) {
            $targetPath = storage_path('app/public');
            $this->copyDirectory($sourcePath, $targetPath);
            $results['storage_app_public'] = 'Restored successfully';
        }

        // Restore public/uploads
        $sourcePath = $filesDir . '/public_uploads';
        if (is_dir($sourcePath)) {
            $targetPath = public_path('uploads');
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
            $this->copyDirectory($sourcePath, $targetPath);
            $results['public_uploads'] = 'Restored successfully';
        }

        // Restore .env file
        $envSource = $filesDir . '/.env';
        if (file_exists($envSource)) {
            $envTarget = base_path('.env.backup');
            copy($envSource, $envTarget);
            $results['env'] = 'Backed up to .env.backup (manual restore required)';
        }

        return [
            'success' => true,
            'message' => 'Files restored successfully',
            'details' => $results,
        ];
    }

    /**
     * Create backup manifest
     */
    protected function createBackupManifest($backupDir, $backupName, $includesFiles)
    {
        $manifest = [
            'backup_name' => $backupName,
            'created_at' => Carbon::now()->toISOString(),
            'includes_database' => true,
            'includes_files' => $includesFiles,
            'version' => '1.0',
            'app_version' => config('app.version', '1.0.0'),
            'database_version' => DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown',
        ];

        file_put_contents(
            $backupDir . '/manifest.json',
            json_encode($manifest, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Read backup manifest
     */
    protected function readBackupManifest($tempDir)
    {
        $manifestPath = $tempDir . '/manifest.json';
        
        if (!file_exists($manifestPath)) {
            return null;
        }

        $content = file_get_contents($manifestPath);
        return json_decode($content, true);
    }

    /**
     * Create ZIP archive
     */
    protected function createZipArchive($sourceDir, $archiveName)
    {
        $zipPath = $this->backupPath . '/' . $archiveName . '.zip';
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            throw new \Exception("Cannot create ZIP archive: {$zipPath}");
        }

        $this->addDirectoryToZip($zip, $sourceDir, '');
        $zip->close();

        return $zipPath;
    }

    /**
     * Extract ZIP archive
     */
    protected function extractZipArchive($zipPath, $targetDir)
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== TRUE) {
            throw new \Exception("Cannot open ZIP archive: {$zipPath}");
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $zip->extractTo($targetDir);
        $zip->close();
    }

    /**
     * Add directory to ZIP
     */
    protected function addDirectoryToZip($zip, $sourceDir, $zipPath)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . substr($filePath, strlen($sourceDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    /**
     * Copy directory recursively
     */
    protected function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($source) + 1);
                $destPath = $destination . '/' . $relativePath;
                
                $destDir = dirname($destPath);
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                
                copy($filePath, $destPath);
            }
        }
    }

    /**
     * Remove directory recursively
     */
    protected function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }

    /**
     * Get file size in human readable format
     */
    protected function getFileSize($filePath)
    {
        if (!file_exists($filePath)) {
            return '0 B';
        }

        $bytes = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get backup type from filename
     */
    protected function getBackupType($filePath)
    {
        $filename = basename($filePath);
        
        if (strpos($filename, 'full_backup_') === 0) {
            return 'full';
        } elseif (strpos($filename, 'database_backup_') === 0) {
            return 'database';
        } elseif (strpos($filename, 'files_backup_') === 0) {
            return 'files';
        }
        
        return 'unknown';
    }

    /**
     * Clean old backups
     */
    protected function cleanOldBackups()
    {
        $backups = $this->listBackups();
        
        if (count($backups) > $this->maxBackups) {
            $backupsToDelete = array_slice($backups, $this->maxBackups);
            
            foreach ($backupsToDelete as $backup) {
                $this->deleteBackup($backup['path']);
            }
        }
    }
}
