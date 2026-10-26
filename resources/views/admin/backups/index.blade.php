@extends('admin.dashboard')

@section('content')
<style>
    .backup-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Header Styles */
    .backup-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    }

    .backup-header h1 {
        font-size: 32px;
        margin: 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .backup-header p {
        margin: 8px 0 0 0;
        opacity: 0.95;
        font-size: 14px;
    }

    /* Alert Styles */
    .alert {
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
        color: #155724;
    }

    .alert-error {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 2px solid #dc3545;
        color: #721c24;
    }

    .alert strong {
        font-weight: 600;
    }

    /* Create Backup Section */
    .create-backup-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
    }

    .create-backup-section h2 {
        margin: 0 0 12px 0;
        font-size: 20px;
        font-weight: 700;
    }

    .create-backup-section p {
        margin: 0 0 20px 0;
        opacity: 0.95;
        font-size: 14px;
    }

    .create-btn {
        background: white;
        color: #667eea;
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .create-btn:active {
        transform: translateY(0);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-top: 4px solid;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.1;
    }

    .stat-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
    }

    .stat-card.blue {
        border-top-color: #667eea;
    }

    .stat-card.blue::before {
        background: #667eea;
    }

    .stat-card.green {
        border-top-color: #10b981;
    }

    .stat-card.green::before {
        background: #10b981;
    }

    .stat-card.orange {
        border-top-color: #f59e0b;
    }

    .stat-card.orange::before {
        background: #f59e0b;
    }

    .stat-icon {
        font-size: 32px;
        margin-bottom: 12px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #333;
    }

    /* Backup List Section */
    .backup-list-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }

    /* Table Styles */
    .backup-table {
        width: 100%;
        border-collapse: collapse;
    }

    .backup-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
    }

    .backup-table th {
        padding: 16px 20px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .backup-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .backup-table tbody tr:hover {
        background: #f8f9fa;
    }

    .backup-table td {
        padding: 16px 20px;
        vertical-align: middle;
    }

    .backup-name {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: #667eea;
        font-weight: 500;
        word-break: break-all;
    }

    .backup-date {
        color: #666;
        font-size: 13px;
    }

    .backup-size {
        font-weight: 600;
        color: #f59e0b;
        font-size: 13px;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .btn-action {
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-download {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .btn-download:hover {
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        transform: translateY(-1px);
    }

    .btn-delete {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .btn-delete:hover {
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state-icon {
        font-size: 60px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state p {
        margin: 0;
        font-size: 16px;
        color: #999;
    }

    /* Info Section */
    .info-section {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left: 4px solid #667eea;
        border-radius: 10px;
        padding: 25px;
        margin-top: 30px;
    }

    .info-section h3 {
        margin: 0 0 16px 0;
        font-size: 16px;
        font-weight: 700;
        color: #1e40af;
    }

    .info-section ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .info-section li {
        margin-bottom: 12px;
        font-size: 13px;
        color: #1e40af;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-section li:last-child {
        margin-bottom: 0;
    }

    .info-code {
        background: white;
        padding: 6px 10px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: #667eea;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .backup-header h1 {
            font-size: 24px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .backup-table {
            font-size: 12px;
        }

        .backup-table th,
        .backup-table td {
            padding: 12px 10px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 6px;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="backup-container">
    <!-- Header -->
    <div class="backup-header">
        <h1>💾 Backup Management System</h1>
        <p>Create, manage, and restore your database backups safely</p>
    </div>

    <!-- Alert Messages -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>✅</span>
            <div>
                <strong>Success!</strong> {{ $message }}
            </div>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-error">
            <span>❌</span>
            <div>
                <strong>Error!</strong> {{ $message }}
            </div>
        </div>
    @endif

    <!-- Create Backup Section -->
    <div class="create-backup-section">
        <h2>📦 Create New Backup</h2>
        <p>Click the button below to create a complete backup of your database, uploaded files, and configuration.</p>
        
        <form action="{{ route('admin.backups.create') }}" method="POST">
            @csrf
            <button type="submit" class="create-btn">
                <span>🔄</span> Create Backup Now
            </button>
        </form>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">📊</div>
            <div class="stat-label">Total Backups</div>
            <div class="stat-value">{{ count($backups) }}</div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">📅</div>
            <div class="stat-label">Latest Backup</div>
            <div class="stat-value" style="font-size: 16px; margin-top: 4px;">
                @if(count($backups) > 0)
                    {{ data_get($backups, '0.date', 'N/A') }}
                @else
                    No backups
                @endif
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">💾</div>
            <div class="stat-label">Total Size</div>
            <div class="stat-value">
                @php
                    $totalSize = 0;
                    foreach($backups as $backup) {
                        $totalSize += $backup['size'];
                    }
                    echo formatBytes($totalSize);
                @endphp
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="backup-list-section">
        <div class="section-header">
            📋 Backup History
        </div>

        @if(count($backups) > 0)
            <div style="overflow-x: auto;">
                <table class="backup-table">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Backup File</th>
                            <th style="width: 25%;">Date</th>
                            <th style="width: 15%;">Size</th>
                            <th style="width: 20%; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                            <tr>
                                <td>
                                    <span class="backup-name">{{ $backup['name'] }}</span>
                                </td>
                                <td>
                                    <span class="backup-date">{{ $backup['date'] }}</span>
                                </td>
                                <td>
                                    <span class="backup-size">{{ $backup['size_formatted'] }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.backups.download', ['backup' => $backup['name']]) }}" 
                                           class="btn-action btn-download">
                                            ⬇️ Download
                                        </a>

                                        <form action="{{ route('admin.backups.delete', ['backup' => $backup['name']]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this backup? This action cannot be undone.');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <p>No backups created yet. Click "Create Backup Now" to get started!</p>
            </div>
        @endif
    </div>

    <!-- Information Section -->
    <div class="info-section">
        <h3>ℹ️ About Your Backup System</h3>
        <ul>
            <li>✅ <strong>Automatic daily backups</strong> scheduled at 2:00 AM</li>
            <li>✅ Each backup includes <strong>Database + Storage Files + Configuration</strong></li>
            <li>✅ The <strong>10 most recent backups</strong> are kept automatically</li>
            <li>✅ Older backups are <strong>deleted automatically</strong> to save storage space</li>
            <li>✅ Enable scheduled backups via cron: <span class="info-code">* * * * * php /path/to/artisan schedule:run</span></li>
            <li>✅ Or manually run backup anytime: <span class="info-code">php artisan backup:run</span></li>
        </ul>
    </div>
</div>
@endsection

@php
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
}
@endphp
