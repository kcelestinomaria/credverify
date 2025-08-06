<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Public verification routes
Route::get('/verify', [VerificationController::class, 'index'])->name('credential.verification.index');
Route::post('/verify', [VerificationController::class, 'verify'])->name('credential.verification.verify');
Route::get('/verify/{code}', [VerificationController::class, 'verify'])->name('credential.verification.show');

// Institution-specific verification routes
Route::get('/institution/{institution:slug}/verify', [VerificationController::class, 'institutionVerify'])->name('verification.institution');
Route::post('/institution/{institution:slug}/verify', [VerificationController::class, 'institutionVerifySubmit'])->name('verification.institution.submit');

// Download Blockcerts JSON
Route::get('/credential/{credential}/json', [VerificationController::class, 'downloadJson'])->name('credential.json');

// Admin dashboard (requires authentication)
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isEmployer()) {
        return redirect()->route('employer.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes (institution staff)
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Institution management (admin only)
    Route::middleware('can:manage-institutions')->group(function () {
        Route::resource('institutions', InstitutionController::class);
    });
    
    // Credential management (admin only)
    Route::middleware('can:manage-credentials')->group(function () {
        Route::resource('credentials', CredentialController::class)->except(['edit', 'update']);
        Route::post('/credentials/{credential}/revoke', [CredentialController::class, 'revoke'])->name('credentials.revoke');
        Route::post('/credentials/process-batch', [CredentialController::class, 'processPendingBatch'])->name('credentials.process-batch');
    });
});

// Admin Management Routes (with RBAC)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management (requires manage-users permission)
    Route::middleware('permission:manage-users')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    });
    
    // System Settings (super admin only)
    Route::middleware('permission:manage-system')->group(function () {
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });
    
    // Audit Logs (requires view-audit-logs permission)
    Route::middleware('permission:view-audit-logs')->group(function () {
        Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit-logs');
    });
});

// Employer routes
Route::prefix('employer')->name('employer.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [EmployerController::class, 'dashboard'])->name('dashboard');
    Route::get('/verify', [EmployerController::class, 'verify'])->name('verify');
    Route::post('/verify', [EmployerController::class, 'verify'])->name('verify.submit');
    Route::get('/verify/{code}', [EmployerController::class, 'verify'])->name('verify.code');
    Route::get('/history', [EmployerController::class, 'history'])->name('history');
    Route::get('/bulk-verify', [EmployerController::class, 'bulkVerify'])->name('bulk-verify');
    Route::post('/bulk-verify', [EmployerController::class, 'processBulkVerify'])->name('bulk-verify.process');
});

require __DIR__.'/auth.php';
