<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

Route::name('auth.')->group(function () {
    Route::middleware('guest')->group(function () {
            Route::get('login', [AuthController::class , 'showLoginForm'])->name('login');
            Route::post('login', [AuthController::class , 'login']);
        // Future: register, password reset
        }
        );

        Route::middleware('auth')->group(function () {
            Route::post('logout', [AuthController::class , 'logout'])->name('logout');
        }
        );    });
