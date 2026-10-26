<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\BusStandController;
use App\Http\Controllers\Admin\BackupController;

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');

// Admin protected routes
Route::middleware('admin.auth')->group(function () {
    Route::get('/admin', [AuthController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Admin Shop Management
    Route::get('/admin/shops/create', [ShopController::class, 'create'])->name('admin.shops.create');
    Route::get('/admin/shop-management', [\App\Http\Controllers\ShopController::class, 'manage'])->name('admin.shop_management.index');
    Route::get('/admin/shop-management/search', [\App\Http\Controllers\ShopController::class, 'search'])->name('admin.shop_management.search');
    Route::post('/admin/shop-management', [\App\Http\Controllers\ShopController::class, 'manageStore'])->name('admin.shop_management.update');
    Route::post('/admin/shops', [ShopController::class, 'store'])->name('admin.shops.store');
    Route::get('/admin/shops/manage-edit', [ShopController::class, 'manageEdit'])->name('admin.shops.manage_edit');
    Route::get('/admin/shops/search', [ShopController::class, 'search'])->name('admin.shops.search');
    // fetch single shop record for editing
    Route::get('/admin/shops/{id}', [ShopController::class, 'show'])->name('admin.shops.show');
    Route::put('/admin/shops/{id}', [ShopController::class, 'update'])->name('admin.shops.update');

    // Admin User Management
    Route::get('/admin/users', [UsersController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UsersController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UsersController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{id}', [UsersController::class, 'show'])->name('admin.users.show');
    Route::patch('/admin/users/{id}/block', [UsersController::class, 'toggleBlock'])->name('admin.users.toggle_block');
    Route::delete('/admin/users/{id}', [UsersController::class, 'destroy'])->name('admin.users.destroy');
    
    // Admins management (create new admin accounts and view registered admins)
    Route::get('/admin/admins', [AdminsController::class, 'index'])->name('admin.admins.index');
    Route::get('/admin/admins/create', [AdminsController::class, 'create'])->name('admin.admins.create');
    Route::post('/admin/admins', [AdminsController::class, 'store'])->name('admin.admins.store');

    // Admin Payments management
    Route::get('/admin/payments', [PaymentsController::class, 'index'])->name('admin.payments.index');
    // optional ajax search already handled by index using query string
    Route::get('/admin/payments/{id}/edit', [PaymentsController::class, 'edit'])->name('admin.payments.edit');
    Route::put('/admin/payments/{id}', [PaymentsController::class, 'update'])->name('admin.payments.update');

    // Admin Bus Stand management
    Route::get('/admin/bus-stands/create', [BusStandController::class, 'create'])->name('admin.bus_stands.create');
    Route::post('/admin/bus-stands', [BusStandController::class, 'store'])->name('admin.bus_stands.store');

    // Admin profile (view/edit for logged-in admin)
    Route::get('/admin/profile', [AuthController::class, 'profile'])->name('admin.profile.show');
    Route::get('/admin/profile/edit', [AuthController::class, 'editProfile'])->name('admin.profile.edit');
    Route::put('/admin/profile', [AuthController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/admin/settings/payment-edit-mode', [AuthController::class, 'updatePaymentEditModeSetting'])->name('admin.settings.payment_edit_mode');

    // Audit logs
    Route::get('/admin/audit', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('admin.audit.index');

    // Backup Management
    Route::get('/admin/backups', [BackupController::class, 'index'])->name('admin.backups.index');
    Route::post('/admin/backups/create', [BackupController::class, 'create'])->name('admin.backups.create');
    Route::get('/admin/backups/download/{backup}', [BackupController::class, 'download'])->name('admin.backups.download');
    Route::delete('/admin/backups/{backup}', [BackupController::class, 'delete'])->name('admin.backups.delete');
});
