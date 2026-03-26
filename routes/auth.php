<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\InstallController;

Route::middleware('guest')->group(function () {
    Route::get('install', [InstallController::class, 'index'])->name('install.index');
    Route::post('install/database-check', [InstallController::class, 'validateDatabase'])->name('install.database.validate');
    Route::post('install/language', [InstallController::class, 'storeLanguage'])->name('install.language.store');
    Route::post('install/finalize', [InstallController::class, 'finalize'])->name('install.finalize');
});

Route::name('auth.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.post');
        Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});
