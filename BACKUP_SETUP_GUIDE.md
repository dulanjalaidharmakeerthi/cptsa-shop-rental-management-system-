# 💾 Backup System Setup Guide

## Overview
Your ShopRent application now has a complete backup system that supports both **manual** and **automatic** backups. The system backs up:
- ✅ **Database** (SQLite or MySQL)
- ✅ **Storage Files** (uploads, documents)
- ✅ **Configuration Files** (.env, config directory)

---

## 🚀 Quick Start

### Manual Backup (Easy)
1. Navigate to **Admin Dashboard** → **💾 Backup Management**
2. Click **"Create Backup Now"** button
3. Wait for the backup to complete
4. Your backup appears in the history list below

### View Backups
- All backups are listed with date, size, and download/delete options
- The system automatically keeps the 10 most recent backups
- Older backups are automatically deleted

---

## ⏰ Automatic Daily Backups (Recommended)

### What it does:
- Automatically creates a backup every day at **2:00 AM**
- Completely hands-off once configured
- Keeps the last 10 backups for storage efficiency

### How to Enable:

#### Option 1: Using Linux Cron (Recommended for Production)

1. Open your crontab:
   ```bash
   crontab -e
   ```

2. Add this line:
   ```bash
   * * * * * cd /path/to/ShopRent && php artisan schedule:run >> /dev/null 2>&1
   ```
   Replace `/path/to/ShopRent` with your actual project directory.

3. Save and exit

#### Option 2: Using Windows Task Scheduler

1. Open **Task Scheduler**
2. Create new task with:
   - **Trigger:** Daily at 2:00 AM (or any time)
   - **Action:** Run batch file with this content:
     ```batch
     @echo off
     cd C:\path\to\ShopRent
     php artisan schedule:run
     ```

#### Option 3: Manual Command (For Testing)

Run this command manually to test:
```bash
php artisan backup:run
```

---

## 📝 Usage Examples

### Via Web Interface (Easiest)
```
URL: /admin/backups
Actions: Create, Download, Delete backups
```

### Via Command Line
```bash
# Create a backup manually
php artisan backup:run

# Check backup files
ls storage/backups/
```

---

## 📁 Backup File Structure

Each backup ZIP archive contains:
```
backup_2026-01-17_14-30-45.zip
├── database/
│   └── database_2026-01-17_14-30-45.sqlite (or .sql for MySQL)
├── storage/
│   └── app/
│       └── (all uploaded files)
└── config/
    ├── .env
    └── config/
        └── (all config files)
```

---

## 🔧 Configuration

### Change Backup Frequency

Edit `app/Console/Kernel.php` line with the schedule command:

**Daily at 2:00 AM (default):**
```php
$schedule->command('backup:run')->dailyAt('02:00');
```

**Twice daily (2:00 AM & 2:00 PM):**
```php
$schedule->command('backup:run')->dailyAt('02:00');
$schedule->command('backup:run')->dailyAt('14:00');
```

**Every 6 hours:**
```php
$schedule->command('backup:run')->everyHours(6);
```

**Weekly on Monday at 2:00 AM:**
```php
$schedule->command('backup:run')->weeklyOn(1, '02:00');
```

### Change Number of Kept Backups

Edit `app/Services/BackupService.php` line 36:
```php
$this->cleanOldBackups($backupDir, 10); // Change 10 to desired number
```

---

## 💾 Storage Requirements

- Each backup is approximately **2-10 MB** (depends on data size)
- With 10 backups kept, you'll need about **20-100 MB**
- Monitor `storage/backups/` directory size

---

## 🛡️ Best Practices

1. ✅ **Test backups regularly** - Download and verify backup files
2. ✅ **Store off-site** - Download backups to external storage periodically
3. ✅ **Monitor disk space** - Ensure `storage/` directory has free space
4. ✅ **Keep cron job running** - Make sure the scheduler is enabled
5. ✅ **Document your schedule** - Note when automatic backups occur

---

## 🔍 Troubleshooting

### Backup Not Created?
1. Check if cron/task scheduler is running:
   ```bash
   php artisan schedule:list
   ```

2. Manually run backup:
   ```bash
   php artisan backup:run
   ```

3. Check logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

### No Space Available?
1. Delete old backups from admin panel
2. Check disk space:
   ```bash
   df -h
   ```

### MySQL Backup Empty?
1. Ensure `mysqldump` is installed and in PATH
2. Verify MySQL credentials in `.env`
3. Manually test:
   ```bash
   mysqldump -h localhost -u root -p database_name
   ```

### Permission Denied?
```bash
# Make storage directory writable
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

---

## 📊 Monitoring

### Check Latest Backup
```bash
ls -lh storage/backups/ | tail -5
```

### Test Restore (SQLite)
```bash
# Stop application
# Restore from backup
cp storage/backups/database_*.sqlite database/database.sqlite
# Restart application
```

### Test Restore (MySQL)
```bash
# Import backup
mysql -u root -p database_name < storage/backups/database_*.sql
```

---

## 🚨 Important Notes

1. **Backup Location:** `/storage/backups/` - ensure this directory exists and is writable
2. **Database Type:** SQLite and MySQL both supported
3. **File Permissions:** Ensure PHP can write to storage directory
4. **Disk Space:** Monitor free disk space regularly
5. **Restore:** Manual process (contact support for help)

---

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Test manually: `php artisan backup:run`
3. Verify cron: `crontab -l`
4. Check permissions: `ls -la storage/`

---

**Last Updated:** January 17, 2026
