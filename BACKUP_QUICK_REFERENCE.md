# 🚀 Backup System Quick Reference

## ✨ What's New

Your ShopRent application now has a complete backup system with:
- **Manual backups** via admin panel
- **Automatic daily backups** at 2:00 AM (configurable)
- **One-click download** of any backup
- **Auto-cleanup** of old backups (keeps last 10)

---

## 📍 Access Points

| Feature | How to Access |
|---------|--------------|
| **Backup Panel** | `/admin/backups` |
| **Create Backup** | Click "Create Backup Now" button |
| **Download Backup** | Click download icon next to backup |
| **Delete Backup** | Click trash icon to remove |
| **Command Line** | `php artisan backup:run` |

---

## 🎯 3 Ways to Backup

### 1️⃣ **Web Interface (Easiest)**
- Go to Admin Dashboard → Backups
- Click "Create Backup Now"
- Download or manage backups

### 2️⃣ **Automatic (Daily)**
- Set up cron job (see guide below)
- Runs at 2:00 AM every day
- No manual action needed

### 3️⃣ **Command Line**
- Run: `php artisan backup:run`
- Good for testing/scripting

---

## ⚙️ Enable Automatic Backups

### Linux/Mac (Cron)
```bash
crontab -e
# Add this line:
* * * * * cd /your/project/path && php artisan schedule:run >> /dev/null 2>&1
```

### Windows Task Scheduler
1. Open Task Scheduler
2. New Task
3. Trigger: Daily at 2:00 AM
4. Action: Run `php artisan schedule:run`

---

## 📂 Files Created

```
app/
  ├── Services/BackupService.php          (Backup logic)
  ├── Console/Commands/BackupDatabase.php (CLI command)
  └── Http/Controllers/Admin/BackupController.php
resources/views/admin/backups/
  └── index.blade.php                    (Backup UI)
```

---

## 🔧 Files Modified

- `app/Console/Kernel.php` - Added daily schedule
- `routes/admin.php` - Added backup routes

---

## 📊 What Gets Backed Up

✅ **Database** - Full SQLite or MySQL backup
✅ **Files** - All uploads in `storage/app/`
✅ **Config** - `.env` and `config/` files

---

## 💡 Pro Tips

1. **Test Restore**: Periodically download and test backups
2. **Off-site Storage**: Move old backups to external drives
3. **Monitor Space**: Keep an eye on `storage/backups/` size
4. **Cron Verification**: `php artisan schedule:list` to confirm setup

---

## 🆘 Common Commands

```bash
# Create backup manually
php artisan backup:run

# Check if scheduling works
php artisan schedule:work

# List scheduled tasks
php artisan schedule:list

# Clear old cache if needed
php artisan cache:clear
```

---

## 📖 Full Guide
See `BACKUP_SETUP_GUIDE.md` for detailed setup instructions and troubleshooting.

---

**Next Steps:**
1. Visit `/admin/backups`
2. Click "Create Backup Now" to test
3. Set up automatic backups using your scheduler
4. Done! 🎉
