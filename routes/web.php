<?php

use Illuminate\Support\Facades\Route;
use App\Models\Page;
use Inertia\Inertia;

use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\LocaleController;

// Redirect default login name to Admin login
Route::redirect('login', 'admin/login')->name('login');

// Locale Switcher
Route::get('lang/{locale}', [LocaleController::class , 'switch'])->name('lang.switch');

// Admin Routes
Route::name('admin.')->prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
            Route::get('login', [\App\Http\Controllers\Admin\AuthController::class , 'showLoginForm'])->name('login');
            Route::post('login', [\App\Http\Controllers\Admin\AuthController::class , 'login']);
        }
        );

        Route::middleware('auth')->group(function () {
            Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class , 'logout'])->name('logout');

            Route::get('/', function () {
                    return Inertia::render('Admin/Dashboard');
                }
                )->name('dashboard');

                Route::resource('pages', AdminPageController::class)->except(['show']);
                Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->except(['show']);
                Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)->only(['index', 'store', 'update', 'destroy']);
                Route::post('media/folders', [\App\Http\Controllers\Admin\MediaController::class , 'storeFolder'])->name('media.folders.store');
                Route::patch('media/folders/{folder}', [\App\Http\Controllers\Admin\MediaController::class , 'updateFolder'])->name('media.folders.update');
                Route::delete('media/folders/{folder}', [\App\Http\Controllers\Admin\MediaController::class , 'destroyFolder'])->name('media.folders.destroy');
                Route::post('media/bulk-action', [\App\Http\Controllers\Admin\MediaController::class , 'bulkAction'])->name('media.bulk-action');
                Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class)->except(['show']);
                Route::resource('forms', \App\Http\Controllers\Admin\FormController::class)->except(['show']);
                Route::resource('translations', \App\Http\Controllers\Admin\TranslationController::class)->except(['show']);
                Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class)->except(['show']);
                Route::resource('languages', \App\Http\Controllers\Admin\LanguageController::class)->except(['show']);
                Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);

                Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class , 'index'])->name('settings.index');
                Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class , 'store'])->name('settings.store');

                Route::prefix('theme')->name('theme.')->group(function () {
                    Route::get('/', function () {
                            return redirect()->route('admin.theme.colors');
                        }
                        )->name('index');

                        Route::get('colors', [\App\Http\Controllers\Admin\ThemeController::class , 'colors'])->name('colors');
                        Route::get('fonts', [\App\Http\Controllers\Admin\ThemeController::class , 'fonts'])->name('fonts');
                        Route::get('sizes', [\App\Http\Controllers\Admin\ThemeController::class , 'sizes'])->name('sizes');
                        Route::get('typography', [\App\Http\Controllers\Admin\ThemeController::class , 'typography'])->name('typography');
                        Route::get('effects', [\App\Http\Controllers\Admin\ThemeController::class , 'effects'])->name('effects');
                        Route::post('/', [\App\Http\Controllers\Admin\ThemeController::class , 'store'])->name('store');
                    }
                    );

                    Route::get('blocks', function () {
                    return Inertia::render('Admin/Blocks');
                }
                )->name('blocks');
            }
            );
        });

// Public Routes
Route::get('/', [App\Http\Controllers\PageController::class , 'show'])->name('home');

// Specific resource routes (if not handled by generic page slug)
Route::get('/blog/{slug}', [App\Http\Controllers\PageController::class , 'showPost'])->name('blog.post');
Route::get('/projects/{slug}', [App\Http\Controllers\PageController::class , 'showProject'])->name('projects.show');

// Special previews
Route::get('/forms/{id}/preview', function ($id) {
    $form = \App\Models\Form::findOrFail($id);
    return Inertia::render('Public/FormPreview', [
    'form' => $form,
    'settings' => \App\Models\Setting::pluck('value', 'key')->toArray()
    ]);
})->name('forms.preview');

// Generic Catch-all for Pages
Route::get('/{slug}', [App\Http\Controllers\PageController::class , 'show'])
    ->name('page.show')
    ->where('slug', '^(?!admin|livewire|storage|_debugbar|build|api).*$');
