<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\PageController as AdminPageController;

Route::name('dashboard.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('index');
});

// Content Section
Route::resource('pages', AdminPageController::class)->except(['show']);
Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->except(['show']);


Route::name('media.')->prefix('media')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('store');
    Route::patch('{media}', [\App\Http\Controllers\Admin\MediaController::class, 'update'])->name('update');
    Route::delete('{media}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('destroy');
    Route::post('folders', [\App\Http\Controllers\Admin\MediaController::class, 'storeFolder'])->name('folders.store');
    Route::patch('folders/{folder}', [\App\Http\Controllers\Admin\MediaController::class, 'updateFolder'])->name('folders.update');
    Route::delete('folders/{folder}', [\App\Http\Controllers\Admin\MediaController::class, 'destroyFolder'])->name('folders.destroy');
    Route::post('bulk-action', [\App\Http\Controllers\Admin\MediaController::class, 'bulkAction'])->name('bulk-action');
});

Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class)->except(['show']);

Route::resource('forms', \App\Http\Controllers\Admin\FormController::class)->except(['show']);
Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class)->except(['show']);

// System Section (Admin Only)
Route::resource('translations', \App\Http\Controllers\Admin\TranslationController::class)->except(['show']);
Route::resource('languages', \App\Http\Controllers\Admin\LanguageController::class)->except(['show']);
Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);

Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');

Route::name('theme.')->prefix('theme')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.theme.colors');
    })->name('index');

    Route::get('colors', [\App\Http\Controllers\Admin\ThemeController::class, 'colors'])->name('colors');
    Route::get('fonts', [\App\Http\Controllers\Admin\ThemeController::class, 'fonts'])->name('fonts');
    Route::get('sizes', [\App\Http\Controllers\Admin\ThemeController::class, 'sizes'])->name('sizes');
    Route::get('typography', [\App\Http\Controllers\Admin\ThemeController::class, 'typography'])->name('typography');
    Route::get('effects', [\App\Http\Controllers\Admin\ThemeController::class, 'effects'])->name('effects');
    Route::post('/', [\App\Http\Controllers\Admin\ThemeController::class, 'store'])->name('store');
});

Route::get('blocks', function () {
    return Inertia::render('Admin/Blocks');
})->name('blocks');
