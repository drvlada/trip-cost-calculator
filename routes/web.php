<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/

// MVP home page
Route::redirect('/', '/calculator');

Route::get('/calculator', [PageController::class, 'calculator'])->name('calculator');
Route::post('/calculator', [PageController::class, 'calculate'])->name('calculator.calculate');

Route::get('/calculator/result', [PageController::class, 'calculatorResult'])
    ->name('calculator.result');

/*
|--------------------------------------------------------------------------
| Authenticated pages (Breeze)
|--------------------------------------------------------------------------
*/

// IMPORTANT: Breeze expects "dashboard" after login.
// We keep it, but redirect to our MVP calculator page.
Route::get('/dashboard', function () {
    return redirect()->route('calculator');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // History
    Route::get('/history', [PageController::class, 'history'])->name('history');
    Route::get('/history/{trip}', [PageController::class, 'historyShow'])->name('history.show');
    Route::delete('/history/{trip}', [PageController::class, 'historyDestroy'])->name('history.destroy');

    // View from history -> open calculator with params (must exist in controller)
    Route::get('/calculator/from-history/{trip}', [PageController::class, 'calculatorFromHistory'])
        ->name('calculator.fromHistory');
});

/*
|--------------------------------------------------------------------------
| Authentication routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
