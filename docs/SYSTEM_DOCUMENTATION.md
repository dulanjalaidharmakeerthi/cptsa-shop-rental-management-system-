# ShopRent System Documentation

## 1. Purpose and terminology

ShopRent manages shop rentals and the related ownership/tenant, agreement, rent, payment, and administration records. The application is built with Laravel. It does not use WordPress custom post types (CPTs): the main property record is the `Shop` model, and previous owner details are held in `ShopOwnerHistory`.

In the application, “shop owner” is the current owner/tenant contact recorded against a shop. It is not a separate user login or a legal title registry.

## 2. Main features

### Shop and property records

A shop record can include:

- Shop name and shop number
- Current owner name, NIC, phone, and address
- Shop address, location, and bus stand association
- Agreement duration, start date, and end date
- Shop value, monthly rent, rate, advance payment, and opening balance
- Yearly and monthly rent, payment, due, interest, saving, and adjustment figures
- Remarks, record year, images, and a document attachment

Administrators create and edit shop records from the admin pages. Signed-in application users can use shop management to search by owner name, NIC, or shop number and maintain shop charge and balance details. Shop agreements can be opened as printable documents.

### Ownership and charge history

When the owner name is changed through shop management, the previous owner/contact and agreement information is written to shop owner history, with the change time and reason. Charge-history records preserve old and new charge details and the user/admin name recorded as the person making the change.

Owner history is a record of prior shop ownership/contact details; it does not create a login account for the owner.

### Rent, due balances, and interest

The `rent:process` command:

- On the first day of the month, records that month's rent and adds the rent to the shop's due and yearly charge figures. A last-charged date prevents charging the same month's rent twice.
- On the 28th, adds rate-based interest for shops with an outstanding due payment, once for that month.
- Also contains a fallback check for balances that have gone more than 28 days since the last rent/interest charge.

The command is scheduled for the 1st at 00:05 and the 28th at 23:59. A scheduler runner must execute `php artisan schedule:run` every minute for these scheduled runs to happen.

### Payments and receipts

Signed-in users can search payments, inspect a shop's payment information, view payment history, and record payments. A recorded payment can include:

- Shop, receipt number, and payment date
- One or more payable months and year
- Rent amount and eligible interest
- A positive or negative saving adjustment
- An optional JPEG, PNG, JPG, or PDF receipt slip (maximum 2 MB)

The system creates a payment record per selected month and corresponding payment-detail records. It validates month selections, prevents a duplicate payment for the same shop/month/year, and only accepts interest for months where interest is due. Payment records retain receipt and payable-month information for review.

Administrators and accountants have payment listing/edit pages. An admin dashboard setting controls the displayed payment edit-mode setting.

### Dashboards and reports

- General, manager, staff, and cashier dashboard pages are available.
- The manager dashboard summarizes rent collected, expected income, outstanding payment and interest amounts, agreement status, shops with no current-month payment, recent revenue trend, and shops with overdue or expiring agreements.
- Due-payment and expired-agreement views provide follow-up lists.
- Tenant, shop, and due reports support browsing shop/owner records; shop reports provide detail/edit actions.
- Payment history can be exported.

### Bus stands

Bus stands are named locations that can be associated with shops. The application provides pages for adding a bus stand from the authenticated area and the admin area.

### User and administrator management

Administrators can create application user accounts, list and inspect users, toggle the stored blocked status, and delete user accounts. They can also create and list administrator accounts.

### Audit logs

Administrators can browse audit logs and search by action, actor name, affected record type or ID, or description. The audit-log data model can retain actor details, the affected record, a description, old/new values, IP address, user agent, and metadata.

### Backups

Administrators can list, manually create, download, and delete backups. A daily backup is scheduled at 02:00. The Artisan command is `php artisan backup:run`; backup files are managed by the application's backup service.

## 3. Authentication and access logic

ShopRent has two independent sign-in flows. An administrator session does not sign in a user through Laravel's normal `web` guard, and a normal user session does not satisfy the administrator middleware.

### Application user sign-in

1. Open `/` or `/login`.
2. Submit a username (the user's email address or exact name) and password.
3. The application looks up the matching user and checks the password hash.
4. On success, Laravel's `web` session is authenticated and the user is redirected by the stored `position`:
   - `Manager` -> `/managedashboard`
   - `Staff` -> `/staffdashboard`
   - `Cashier` -> `/cashierdashboard`
   - `Accountant` -> `/managedashboard`
   - Other or unrecognized positions -> `/dashboard`
5. `/logout` logs the user out, invalidates the session, regenerates the CSRF token, and returns to `/`.

Most application pages are in an `auth` route group. That group also applies the `accountant.access` middleware. The middleware limits accountants to the manager dashboard, accountant payment pages, payment history, logout, and user profile/password pages; other named routes return HTTP 403. Other user positions do not have a comparable role-based allowlist in this middleware, so dashboard redirection should not be treated as a complete authorization policy.

### Registration and user creation

Public registration is available at `/register`. The form requires a name, unique email, phone, position, and password (at least 8 characters with confirmation). The self-registration position choices are `Manager`, `Officer`, `Staff`, `Vendor`, `Cashier`, and `Other`. The account is created with a hashed password and the new user is returned to the login page; registration does not automatically sign them in.

Administrators can instead create application users from `/admin/users/create`. That form accepts `Manager`, `Staff`, `Cashier`, and `Accountant`; the password must be at least 8 characters. Admin-created accounts start with `is_blocked` set to false.

### Forgotten password (OTP)

1. Submit the account email on `/password/forgot`.
2. If the email belongs to a user and mail delivery succeeds, the application sends a six-digit OTP.
3. The OTP expires after five minutes. A new active OTP cannot be requested while a valid request already exists.
4. Submit the OTP at `/password/verify-otp`. A request is deleted after expiry or five incorrect attempts.
5. After successful verification, the reset form is available only while the verified reset request is held in the session and has not expired.
6. Submit a new password of at least 8 characters with confirmation. The password is hashed, the reset request and session values are removed, and the user is returned to login.

Mail configuration must be working for this flow.

### Administrator sign-in

1. Open `/admin/login`.
2. Submit an administrator username or email and password.
3. On successful password verification, the application stores the administrator ID in the session as `admin_id` and redirects to `/admin`.
4. Protected admin routes use the `admin.auth` middleware. It checks for the `admin_id` session value and redirects to `/admin/login` if it is missing.
5. `/admin/logout` removes `admin_id` from the session and returns to the admin login page.

The administrator sign-in uses the `Admin` model and a separate session marker; it does not use Laravel's user `Auth` guard.

### Account status note

The admin user-management screen can toggle the `is_blocked` value on a user. The current user login controller validates credentials and logs the user in, but does not check that field. Therefore, the stored blocked status should not be assumed to prevent sign-in or revoke an existing session in the current implementation.

## 4. Important routes

| Area | Route examples | Access |
| --- | --- | --- |
| User authentication | `/login`, `/register`, `/logout` | Public entry points |
| Password reset | `/password/forgot`, `/password/verify-otp`, `/password/reset` | Public, with verified session required for reset |
| User dashboards/profile | `/dashboard`, `/managedashboard`, `/staffdashboard`, `/cashierdashboard`, `/profile` | Authenticated user |
| Shop management | `/shops/manage`, `/shops/search`, `/shops/manage` (POST), `/shops/{id}/agreement` | Authenticated user; accountants are restricted |
| Payments/history | `/payments`, `/payments/search`, `/payments/record`, `/history` | Authenticated user; accountants are restricted to allowed payment/history routes |
| Reports | `/reports/tenants`, `/reports/shops`, `/reports/due` | Authenticated user; accountants are restricted |
| Admin sign-in | `/admin/login` | Public entry point |
| Admin operations | `/admin`, `/admin/shops/*`, `/admin/users/*`, `/admin/payments/*`, `/admin/audit`, `/admin/backups` | Administrator session |

The route definitions in `routes/web.php` and `routes/admin.php` are authoritative; route names and available methods may change as the application evolves.

## 5. Data overview

| Data | Purpose |
| --- | --- |
| `shops` | Rental shop, owner/contact, agreement, charge, balance, image, and document details |
| `shop_owner_histories` | Previous owner/contact and agreement details when an owner is reassigned |
| `shop_charge_histories` | Previous and updated charge details |
| `payments` | Monthly rent/interest payment records, receipt, date, and payable period |
| `payment_details` | Shop/payment detail snapshot for payment records |
| `payment_slips` | Optional uploaded receipt attachment references |
| `bus_stands` | Named locations associated with shops |
| `users` | Application user accounts and positions |
| `admins` | Separate administrator accounts |
| `password_reset_requests` | OTP password-reset request state |
| `audit_logs` | Administrative and record-change audit information |

Database schema changes are maintained as Laravel migrations in `database/migrations`.

## 6. Local development and operations

Requirements: PHP 8.2 or later, Composer, and Node.js/npm. Install PHP and JavaScript dependencies, configure a local `.env` with the database and mail transport, generate the application key, and run migrations:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Run `npm run dev` in a separate terminal when working on front-end assets. Run the automated test suite with `php artisan test`.

For automated rent processing and backups, configure cron or Windows Task Scheduler to run:

```bash
php artisan schedule:run
```

once per minute. Keep database, mail, and backup storage settings appropriate for the deployment environment. Do not commit `.env`, credentials, OTPs, or production backups.
