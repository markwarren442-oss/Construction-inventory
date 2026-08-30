<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Root URL redirects to dashboard if authenticated, or login if guest
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [LoginController::class, 'register'])->name('register');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    
    // Module 1: Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Module 2: Materials
    Route::resource('materials', MaterialController::class);

    // Module 3: QR Code Management & Scanner
    Route::get('/qr-codes', [QrCodeController::class, 'index'])->name('qr.index');
    Route::post('/qr-codes/generate', [QrCodeController::class, 'generate'])->name('qr.generate');
    Route::get('/qr-codes/scanner', [QrCodeController::class, 'scanner'])->name('qr.scanner');
    Route::post('/qr-codes/lookup', [QrCodeController::class, 'lookup'])->name('qr.lookup');
    Route::post('/qr-codes/{id}/toggle-status', [QrCodeController::class, 'toggleStatus'])->name('qr.toggle-status');

    // Module 4: Material Transactions
    Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store', 'show']);

    // Module 5: Location & Site Management
    Route::resource('locations', LocationController::class)->only(['index', 'store', 'show']);

    // Module 6: Inventory Monitoring
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::get('/inventory/damaged-lost', [InventoryController::class, 'damagedLost'])->name('inventory.damaged-lost');

    // Module 7: Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Module 8: Governance (Admin Only)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
