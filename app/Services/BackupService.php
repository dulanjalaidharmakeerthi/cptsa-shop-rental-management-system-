<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use ZipArchive;

class BackupService
{
    public function performBackup(): array
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupDir = storage_path('backups');
            
            // Create backups directory if it doesn't exist
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Create database backup
            $this->backupDatabase($backupDir, $timestamp);

            // Create storage files backup
            $this->backupStorageFiles($backupDir, $timestamp);

            // Create config and important files backup
            $this->backupConfigFiles($backupDir, $timestamp);

            // Create zip archive of all backups
            $zipPath = $this->createZipArchive($backupDir, $timestamp);

            // Clean old backups (keep only last 10)
            $this->cleanOldBackups($backupDir);

            return [
                'success' => true,
                'message' => 'Backup completed successfully',
                'timestamp' => $timestamp,
                'backup_file' => $zipPath,
                'size' => filesize($zipPath) / (1024 * 1024) . ' MB'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage(),
                'error' => $e
            ];
        }
    }

    protected function backupDatabase(string $backupDir, string $timestamp): void
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $dbName = config('database.connections.' . $driver . '.database');

        if ($driver === 'sqlite') {
            $this->backupSQLiteDatabase($dbName, $backupDir, $timestamp);
        } elseif ($driver === 'mysql') {
            $this->backupMySQLDatabase($connection, $backupDir, $timestamp);
        }
    }

    protected function backupSQLiteDatabase(string $dbPath, string $backupDir, string $timestamp): void
    {
        $backupFile = $backupDir . '/database_' . $timestamp . '.sqlite';
        copy($dbPath, $backupFile);
    }

    protected function backupMySQLDatabase($connection, string $backupDir, string $timestamp): void
    {
        $backupFile = $backupDir . '/database_' . $timestamp . '.sql';
        
        try {
            // Get database name
            $dbName = config('database.connections.mysql.database');
            
            // Get all tables
            $tables = DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?', [$dbName]);
            
            $sql = "-- MySQL Database Backup\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: " . $dbName . "\n";
            $sql .= "-- Host: " . config('database.connections.mysql.host') . "\n\n";
            $sql .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
            $sql .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
            $sql .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
            $sql .= "/*!40101 SET NAMES utf8mb4 */;\n";
            $sql .= "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;\n";
            $sql .= "/*!40103 SET TIME_ZONE='+00:00' */;\n";
            $sql .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n";
            $sql .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n\n";
            
            // Add each table
            foreach ($tables as $tableObj) {
                $tableName = $tableObj->TABLE_NAME;
                $sql .= "\n-- Table structure for table `{$tableName}`\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                
                // Get CREATE TABLE statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (!empty($createTable)) {
                    $sql .= $createTable[0]->{"Create Table"} . ";\n";
                }
                
                // Get table data
                $rows = DB::select("SELECT * FROM `{$tableName}`");
                
                if (!empty($rows)) {
                    $sql .= "\n-- Dumping data for table `{$tableName}`\n";
                    $sql .= "LOCK TABLES `{$tableName}` WRITE;\n";
                    $sql .= "/*!40000 ALTER TABLE `{$tableName}` DISABLE KEYS */;\n";
                    
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array)$row as $value) {
                            if (is_null($value)) {
                                $values[] = 'NULL';
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(',', $values) . ");\n";
                    }
                    
                    $sql .= "/*!40000 ALTER TABLE `{$tableName}` ENABLE KEYS */;\n";
                    $sql .= "UNLOCK TABLES;\n";
                }
            }
            
            $sql .= "\n/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;\n";
            $sql .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n";
            $sql .= "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
            $sql .= "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n";
            $sql .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
            $sql .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
            $sql .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
            
            // Write to file
            file_put_contents($backupFile, $sql);
            
            if (filesize($backupFile) == 0) {
                throw new \Exception('Backup file is empty');
            }
        } catch (\Exception $e) {
            throw new \Exception('MySQL backup failed: ' . $e->getMessage());
        }
    }

    protected function backupStorageFiles(string $backupDir, string $timestamp): void
    {
        $storageSourcePath = storage_path('app');
        $backupFile = $backupDir . '/storage_' . $timestamp;

        if (is_dir($storageSourcePath)) {
            $this->copyDirectory($storageSourcePath, $backupFile);
        }
    }

    protected function backupConfigFiles(string $backupDir, string $timestamp): void
    {
        $configFilesPath = $backupDir . '/config_' . $timestamp;
        mkdir($configFilesPath, 0755, true);

        // Backup .env file
        if (file_exists(base_path('.env'))) {
            copy(base_path('.env'), $configFilesPath . '/.env');
        }

        // Backup config directory
        $this->copyDirectory(config_path(), $configFilesPath . '/config');
    }

    protected function createZipArchive(string $backupDir, string $timestamp): string
    {
        $zip = new ZipArchive();
        $zipFile = $backupDir . '/backup_' . $timestamp . '.zip';

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Add database backup
            $databaseFile = $backupDir . '/database_' . $timestamp . 
                (config('database.connections.' . DB::connection()->getDriverName() . '.driver') === 'sqlite' ? '.sqlite' : '.sql');
            
            if (file_exists($databaseFile)) {
                $zip->addFile($databaseFile, 'database/database_' . $timestamp . 
                    (config('database.connections.' . DB::connection()->getDriverName() . '.driver') === 'sqlite' ? '.sqlite' : '.sql'));
            }

            // Add storage files
            $storagePath = $backupDir . '/storage_' . $timestamp;
            if (is_dir($storagePath)) {
                $this->addDirectoryToZip($zip, $storagePath, 'storage');
            }

            // Add config files
            $configPath = $backupDir . '/config_' . $timestamp;
            if (is_dir($configPath)) {
                $this->addDirectoryToZip($zip, $configPath, 'config');
            }

            $zip->close();
        }

        return $zipFile;
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $dir, string $zipPath): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $zipPath . '/' . $relativePath);
            }
        }
    }

    protected function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $directory = opendir($source);

        while (($file = readdir($directory)) !== false) {
            if ($file !== '.' && $file !== '..') {
                $sourcePath = $source . '/' . $file;
                $destinationPath = $destination . '/' . $file;

                if (is_dir($sourcePath)) {
                    $this->copyDirectory($sourcePath, $destinationPath);
                } else {
                    copy($sourcePath, $destinationPath);
                }
            }
        }

        closedir($directory);
    }

    protected function cleanOldBackups(string $backupDir, int $keep = 10): void
    {
        $files = glob($backupDir . '/backup_*.zip');
        
        if (count($files) > $keep) {
            usort($files, function ($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            $filesToDelete = array_slice($files, 0, count($files) - $keep);
            
            foreach ($filesToDelete as $file) {
                unlink($file);
            }
        }
    }

    public function getBackupList(): array
    {
        $backupDir = storage_path('backups');
        
        if (!is_dir($backupDir)) {
            return [];
        }

        $backups = [];
        $files = glob($backupDir . '/backup_*.zip');

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'size_formatted' => $this->formatBytes(filesize($file)),
                'date' => \Carbon\Carbon::createFromTimestamp(filemtime($file))->format('Y-m-d H:i:s'),
                'timestamp' => filemtime($file)
            ];
        }

        // Sort by date descending
        usort($backups, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return $backups;
    }

    public function downloadBackup(string $backupFile): string
    {
        $backupDir = storage_path('backups');
        $filePath = $backupDir . '/' . basename($backupFile);

        if (!file_exists($filePath)) {
            throw new \Exception('Backup file not found');
        }

        return $filePath;
    }

    public function deleteBackup(string $backupFile): bool
    {
        $backupDir = storage_path('backups');
        $filePath = $backupDir . '/' . basename($backupFile);

        if (!file_exists($filePath)) {
            throw new \Exception('Backup file not found');
        }

        return unlink($filePath);
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
