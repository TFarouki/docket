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
    Route::resource('court-cases', CourtCaseController::class)->only(['create', 'store', 'edit', 'update', 'destroy'])->middleware('can:edit matters');
    Route::resource('hearings', HearingController::class)->only(['create', 'store', 'edit', 'update', 'destroy'])->middleware('can:edit matters');

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
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentController::class, 'index'])
            ->name('index')
            ->middleware('can:view appointments');

        Route::get('/create', [AppointmentController::class, 'create'])
            ->name('create')
            ->middleware('can:create appointments');

        Route::post('/', [AppointmentController::class, 'store'])
            ->name('store')
            ->middleware('can:create appointments');

        Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])
            ->name('edit')
            ->middleware('can:edit appointments');

        Route::match(['put', 'patch'], '/{appointment}', [AppointmentController::class, 'update'])
            ->name('update')
            ->middleware('can:edit appointments');

        Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete appointments');
    });
});

Route::get('lang/{locale}', [SettingsController::class, 'setSessionLocale'])->name('lang.switch');

require __DIR__.'/auth.php';
