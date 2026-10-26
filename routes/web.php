<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

// Also allow /login to show the login page
Route::get('/login', function () {
    return view('login');
});

// Authentication routes
Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'show'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::get('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/forgot', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotPasswordForm'])->name('password.forgot');
Route::post('/password/send-otp', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendOtp'])->name('password.send-otp');
Route::get('/password/verify-otp', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showVerifyOtpForm'])->name('password.verify-otp-form');
Route::post('/password/verify-otp', [\App\Http\Controllers\Auth\PasswordResetController::class, 'verifyOtp'])->name('password.verify-otp');
Route::get('/password/reset', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset-form');
Route::post('/password/reset', [\App\Http\Controllers\Auth\PasswordResetController::class, 'resetPassword'])->name('password.reset');

// Dashboards (protected)
Route::middleware(['auth', 'accountant.access'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
    Route::get('/managedashboard', [\App\Http\Controllers\DashboardController::class, 'manager'])->name('managedashboard');
    Route::get('/managedashboard/expired-agreements', [\App\Http\Controllers\DashboardController::class, 'expiredAgreements'])->name('dashboard.expired-agreements');
    Route::get('/managedashboard/due-payments', [\App\Http\Controllers\DashboardController::class, 'duePayments'])->name('dashboard.due-payments');

    // User profile management for all authenticated roles
    Route::get('/profile', [\App\Http\Controllers\DashboardController::class, 'profile'])->name('dashboard.profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\DashboardController::class, 'editProfile'])->name('dashboard.profile.edit');
    Route::put('/profile', [\App\Http\Controllers\DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::get('/profile/password', [\App\Http\Controllers\DashboardController::class, 'editPassword'])->name('dashboard.profile.password.edit');
    Route::put('/profile/password', [\App\Http\Controllers\DashboardController::class, 'updatePassword'])->name('dashboard.profile.password.update');

    Route::get('/staffdashboard', [\App\Http\Controllers\DashboardController::class, 'staff']);
    Route::get('/cashierdashboard', [\App\Http\Controllers\DashboardController::class, 'cashier']);

    // Shops - NOTE: Shop/User creation is now admin-only. Use /admin/shops/create and /admin/users/create instead
    // Route::get('/shops/create', [\App\Http\Controllers\ShopController::class, 'create'])->name('shops.create');
    Route::get('/shops/manage', [\App\Http\Controllers\ShopController::class, 'manage'])->name('shops.manage');
    // AJAX search by owner (returns JSON) used by the management page
    Route::get('/shops/search', [\App\Http\Controllers\ShopController::class, 'search'])->name('shops.search');
    Route::post('/shops/manage', [\App\Http\Controllers\ShopController::class, 'manageStore'])->name('shops.manage.update');
    Route::get('/shops/{id}/agreement', [\App\Http\Controllers\ShopController::class, 'agreement'])->name('shops.agreement');
    Route::post('/shops', [\App\Http\Controllers\ShopController::class, 'store'])->name('shops.store');
    // Bus stands
    Route::get('/busstands/create', [\App\Http\Controllers\BusStandController::class, 'create'])->name('busstands.create');
    Route::post('/busstands', [\App\Http\Controllers\BusStandController::class, 'store'])->name('busstands.store');

    // User management - NOTE: User creation is now admin-only. Use /admin/users/create instead
    // Route::get('/users/create', [\App\Http\Controllers\UsersController::class, 'create'])->name('users.create');
    // Route::post('/users', [\App\Http\Controllers\UsersController::class, 'store'])->name('users.store');

    // Payments page (search and list payment-related fields)
    Route::get('/payments', [\App\Http\Controllers\PaymentsController::class, 'index'])->name('payments.index');
    Route::get('/payments/shop/{id}', [\App\Http\Controllers\PaymentsController::class, 'shop'])->name('payments.shop');
    Route::get('/payments/search', [\App\Http\Controllers\PaymentsController::class, 'search'])->name('payments.search');
    Route::get('/payments/history', [\App\Http\Controllers\PaymentsController::class, 'history'])->name('payments.history');
    Route::post('/payments/record', [\App\Http\Controllers\PaymentsController::class, 'record'])->name('payments.record');
    // New endpoint to store payment details including receipt number
    Route::post('/payment-details', [\App\Http\Controllers\PaymentDetailController::class, 'store'])->name('payment_details.store');
    // History page (search by shop owner and show payments)
    Route::get('/history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('history');
    Route::get('/history/export', [\App\Http\Controllers\HistoryController::class, 'export'])->name('history.export');

    Route::prefix('accountant')->name('accountant.')->group(function () {
        Route::get('/payments', [\App\Http\Controllers\Admin\PaymentsController::class, 'index'])->name('payments.index');
        Route::get('/payments/{id}/edit', [\App\Http\Controllers\Admin\PaymentsController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{id}', [\App\Http\Controllers\Admin\PaymentsController::class, 'update'])->name('payments.update');
    });
    
    // Reports landing page
    Route::get('/reports', function () {
        return redirect()->route('reports.due.index');
    })->name('reports.index');

    // Reports - Tenants
    Route::get('/reports/tenants', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.tenants.index');
    Route::get('/reports/tenants/{shop}', [\App\Http\Controllers\ReportController::class, 'show'])->name('reports.tenants.show');
    // Reports - Shops
    Route::get('/reports/shops', [\App\Http\Controllers\ReportController::class, 'shopsIndex'])->name('reports.shops.index');
    // show details for modal/ajax
    Route::get('/reports/shops/{id}', [\App\Http\Controllers\ReportController::class, 'shopsShow'])->name('reports.shops.show');
    Route::get('/reports/shops/{id}/edit', [\App\Http\Controllers\ReportController::class, 'shopsEdit'])->name('reports.shops.edit');
    Route::post('/reports/shops/{id}', [\App\Http\Controllers\ReportController::class, 'shopsUpdate'])->name('reports.shops.update');
    Route::get('/storage/{path}', function (string $path) {
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    })->where('path', '.*');
    Route::delete('/reports/shops/{id}', [\App\Http\Controllers\ReportController::class, 'shopsDestroy'])->name('reports.shops.destroy');
    // Reports - Due
    Route::get('/reports/due', [\App\Http\Controllers\ReportController::class, 'dueIndex'])->name('reports.due.index');
});

require __DIR__.'/admin.php';
