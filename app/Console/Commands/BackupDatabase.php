<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run {--type=full : Type of backup (full, database, files)}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database and important files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $backupService = new BackupService();

        $this->info('🔄 Starting backup process...');

        $result = $backupService->performBackup();

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            $this->line('📦 Backup File: ' . $result['backup_file']);
            $this->line('💾 Size: ' . $result['size']);
            $this->line('⏰ Timestamp: ' . $result['timestamp']);
            return 0;
        } else {
            $this->error('❌ ' . $result['message']);
            return 1;
        }
    }
}
