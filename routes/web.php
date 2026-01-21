<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/settings/locale', [\App\Http\Controllers\SettingsController::class, 'updateLocale'])->name('settings.updateLocale');
    
    // Core: Party Management
    Route::resource('parties', \App\Http\Controllers\PartyController::class);

    // Core: Matter Management (Litigation)
    Route::resource('matters', \App\Http\Controllers\MatterController::class);
    Route::resource('court-cases', \App\Http\Controllers\CourtCaseController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('hearings', \App\Http\Controllers\HearingController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});


Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar', 'fr'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

require __DIR__.'/auth.php';
