<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\DamagedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/', fn() => redirect()->route('dashboard'));

// Protected
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/data', [InventoryController::class, 'data'])->name('inventory.data');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/inventory/import', [InventoryController::class, 'import'])->name('inventory.import');

    // Borrowers
    Route::get('/borrowers', [BorrowerController::class, 'index'])->name('borrowers.index');
    Route::get('/borrowers/data', [BorrowerController::class, 'data'])->name('borrowers.data');
    Route::post('/borrowers', [BorrowerController::class, 'store'])->name('borrowers.store');
    Route::put('/borrowers/{borrower}', [BorrowerController::class, 'update'])->name('borrowers.update');
    Route::delete('/borrowers/{borrower}', [BorrowerController::class, 'destroy'])->name('borrowers.destroy');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
    Route::get('/transactions/data', [TransactionController::class, 'data'])->name('transactions.data');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/active', [TransactionController::class, 'active'])->name('transactions.active');

    // Returns
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');

    // Damaged
    Route::get('/damaged', [DamagedController::class, 'index'])->name('damaged.index');
    Route::get('/damaged/history', [DamagedController::class, 'history'])->name('damaged.history');
    Route::get('/damaged/data', [DamagedController::class, 'data'])->name('damaged.data');
    Route::post('/damaged', [DamagedController::class, 'store'])->name('damaged.store');

    // Users - admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::get('/settings/data', [SettingController::class, 'data'])->name('settings.data');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
