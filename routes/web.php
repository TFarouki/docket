<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\MatterController;
use App\Http\Controllers\CourtCaseController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentCategoryController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [DashboardController::class, 'root']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/settings/locale', [SettingsController::class, 'updateLocale'])->name('settings.updateLocale');
    
    // Core: Party Management
    Route::resource('parties', PartyController::class);

    // Core: Matter Management (Litigation)
    Route::resource('matters', MatterController::class);
    Route::resource('court-cases', CourtCaseController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('hearings', HearingController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

    // Admin: User Management
    Route::resource('users', UserController::class)->middleware('can:manage users');

    // Document Management
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    
    Route::get('document-categories', [DocumentCategoryController::class, 'index'])->name('document-categories.index');
    Route::post('document-categories', [DocumentCategoryController::class, 'store'])->name('document-categories.store');

    // System Settings (Root only)
    Route::get('system-settings', [SystemSettingController::class, 'index'])->name('system-settings.index');
    Route::post('system-settings', [SystemSettingController::class, 'update'])->name('system-settings.update');

    // CRM: Appointments
    Route::resource('appointments', AppointmentController::class);
});

Route::get('lang/{locale}', [SettingsController::class, 'setSessionLocale'])->name('lang.switch');

require __DIR__.'/auth.php';
