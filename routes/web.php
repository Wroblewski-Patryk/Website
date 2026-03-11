<?php

use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Tutaj rejestrujemy główne grupy tras. Używamy modularnego podejścia, | dzieląc routing na auth, dashboard i public. | */

// Główne przekierowania (Aliasy dla wygody)
Route::redirect('login', 'auth/login')->name('login');
Route::redirect('admin', 'dashboard');

// Technical Routes (Always at root)
Route::get('sitemap.xml', [\App\Http\Controllers\SeoController::class , 'sitemap'])->name('sitemap');
Route::get('robots.txt', [\App\Http\Controllers\SeoController::class , 'robots'])->name('robots');
Route::get('lang/{lang}', [\App\Http\Controllers\LocaleController::class , 'switch'])->name('lang.switch');

// --- 1. Localized Routes (Future Proofing) ---
// Must be BEFORE root routes because the root catch-all would otherwise steal localized paths.
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => 'pl|en'],
    'as' => 'localized.'
], function () {
    Route::prefix('auth')->group(base_path('routes/auth.php'));
    require base_path('routes/dashboard.php');
    require base_path('routes/public.php');
});

// --- 2. Root Routes (Default Locale) ---
Route::group([], function () {
    Route::prefix('auth')->group(base_path('routes/auth.php'));
    require base_path('routes/dashboard.php');
    require base_path('routes/public.php');
});
