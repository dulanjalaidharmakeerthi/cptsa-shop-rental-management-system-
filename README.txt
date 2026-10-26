ShopRent Project Hosting Guide
===============================

Summary:
- Composer validation passed: `composer.json` is valid.
- PHP syntax check passed for project PHP files outside `vendor/`.
- No runtime code errors were detected in static CLI validation.
- There is no `.env` file in this repository; use `.env.example` to create one.
- Current `.env.example` is configured for `DB_CONNECTION=sqlite`; update it for production hosting.

Required software:
- PHP 8.2 or newer (Laravel 12 requires PHP 8.2+)
- Composer
- Node.js and npm
- Web server with document root set to `public/`
- Database server if using MySQL/PostgreSQL/SQL Server

Basic installation steps:
1. Copy environment file:
   - `cp .env.example .env`
2. Set the application key:
   - `php artisan key:generate`
3. Configure database in `.env`:
   - `DB_CONNECTION=mysql`
   - `DB_HOST=127.0.0.1`
   - `DB_PORT=3306`
   - `DB_DATABASE=your_database`
   - `DB_USERNAME=your_user`
   - `DB_PASSWORD=your_password`
4. Install PHP dependencies:
   - `composer install --optimize-autoloader --no-dev`
5. Install frontend dependencies and build assets:
   - `npm install`
   - `npm run build`
6. Run database migrations:
   - `php artisan migrate --force`
7. Set file permissions for storage and cache:
   - Ensure `storage/`, `bootstrap/cache/`, and `public/storage/` are writable by the web server.

Production configuration:
- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Set `APP_URL` to the hosted application URL.
- Enable a production session driver if needed.
- Recommended queue driver: `database`, `redis`, or another supported queue.

Important runtime notes:
- This project includes a monthly rent scheduler in `app/Console/Kernel.php`.
- The scheduler command is `rent:process` and should be run by a system cron or scheduled task every minute:
  - `* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1`
- If hosting on Windows, use Task Scheduler instead of cron.

Common deployment commands:
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`
- `php artisan optimize`

Known environment note:
- The current CLI environment reports a duplicated OpenSSL module warning. This is a PHP configuration issue in the hosting machine, not in the Laravel codebase.

