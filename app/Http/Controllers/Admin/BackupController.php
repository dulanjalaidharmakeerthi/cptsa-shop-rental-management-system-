<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Show backup management page
     */
    public function index()
    {
        $backups = $this->backupService->getBackupList();

        return view('admin.backups.index', [
            'backups' => $backups
        ]);
    }

    /**
     * Create a manual backup
     */
    public function create(Request $request)
    {
        try {
            $result = $this->backupService->performBackup();

            if ($result['success']) {
                return redirect()->route('admin.backups.index')
                    ->with('success', 'Backup created successfully! Size: ' . $result['size']);
            } else {
                return back()->with('error', 'Backup failed: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function download(string $backup)
    {
        try {
            $filePath = $this->backupService->downloadBackup($backup);

            return response()->download($filePath, basename($filePath));
        } catch (\Exception $e) {
            return back()->with('error', 'Error downloading backup: ' . $e->getMessage());
        }
    }

    /**
     * Delete a backup file
     */
    public function delete(Request $request, string $backup)
    {
        try {
            $this->backupService->deleteBackup($backup);

            return redirect()->route('admin.backups.index')
                ->with('success', 'Backup deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting backup: ' . $e->getMessage());
        }
    }
}
