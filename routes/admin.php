<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\PageController as AdminPageController;

Route::middleware('permission:view-admin')->name('dashboard.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('index');
});

// Content Section (Admin & Editor)
Route::middleware('permission:manage-content')->group(function () {
    Route::get('publication-calendar', [\App\Http\Controllers\Admin\DashboardController::class, 'publicationCalendar'])->name('publication-calendar');
    Route::get('content-export', \App\Http\Controllers\Admin\ContentExportController::class)->name('content-export');
    Route::resource('pages', AdminPageController::class)->except(['show']);
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->except(['show']);
    Route::post('pages/{page}/revisions/{revision}/restore', [AdminPageController::class, 'restoreRevision'])->name('pages.revisions.restore');
    Route::post('posts/{post}/revisions/{revision}/restore', [\App\Http\Controllers\Admin\PostController::class, 'restoreRevision'])->name('posts.revisions.restore');

    Route::name('media.')->prefix('media')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('store');
        Route::patch('{media}', [\App\Http\Controllers\Admin\MediaController::class, 'update'])->name('update');
        Route::delete('{media}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('destroy');
        Route::post('{media}/safe-replace', [\App\Http\Controllers\Admin\MediaController::class, 'safeReplace'])->name('safe-replace');
        Route::post('folders', [\App\Http\Controllers\Admin\MediaController::class, 'storeFolder'])->name('folders.store');
        Route::patch('folders/{folder}', [\App\Http\Controllers\Admin\MediaController::class, 'updateFolder'])->name('folders.update');
        Route::delete('folders/{folder}', [\App\Http\Controllers\Admin\MediaController::class, 'destroyFolder'])->name('folders.destroy');
        Route::post('bulk-action', [\App\Http\Controllers\Admin\MediaController::class, 'bulkAction'])->name('bulk-action');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class)->except(['show']);
    });
    
    Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class)->except(['show']);
    
    // Modular Taxonomies
    $modules = ['posts', 'projects'];
    foreach ($modules as $module) {
        Route::prefix($module)->name($module . '.')->group(function () use ($module) {
            Route::get('categories', [\App\Http\Controllers\Admin\TaxonomyController::class, 'index'])
                ->defaults('type', 'category')->defaults('module', $module)->name('categories.index');
            Route::get('tags', [\App\Http\Controllers\Admin\TaxonomyController::class, 'index'])
                ->defaults('type', 'tag')->defaults('module', $module)->name('tags.index');
        });
    }

    // Generic taxonomy routes for store/update/delete (to avoid duplicating everything)
    Route::post('taxonomies', [\App\Http\Controllers\Admin\TaxonomyController::class, 'store'])->name('taxonomies.store');
    Route::put('taxonomies/{taxonomy}', [\App\Http\Controllers\Admin\TaxonomyController::class, 'update'])->name('taxonomies.update');
    Route::delete('taxonomies/{taxonomy}', [\App\Http\Controllers\Admin\TaxonomyController::class, 'destroy'])->name('taxonomies.destroy');
    
    Route::get('blocks', [\App\Http\Controllers\Admin\DashboardController::class, 'blocks'])->name('blocks');
});

// System Section (Admin Only)
Route::middleware('permission:manage-settings')->group(function () {
    Route::resource('translations', \App\Http\Controllers\Admin\TranslationController::class)->except(['show']);
    Route::resource('languages', \App\Http\Controllers\Admin\LanguageController::class)->except(['show']);
    Route::resource('forms', \App\Http\Controllers\Admin\FormController::class)->except(['show']);
    Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class)->except(['show']);
    Route::post('templates/{template}/revisions/{revision}/restore', [\App\Http\Controllers\Admin\TemplateController::class, 'restoreRevision'])->name('templates.revisions.restore');

    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');

    Route::name('theme.')->prefix('theme')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('index');

        Route::get('colors', [\App\Http\Controllers\Admin\ThemeController::class, 'colors'])->name('colors');
        Route::get('fonts', [\App\Http\Controllers\Admin\ThemeController::class, 'fonts'])->name('fonts');
        Route::get('sizes', [\App\Http\Controllers\Admin\ThemeController::class, 'sizes'])->name('sizes');
        Route::get('typography', [\App\Http\Controllers\Admin\ThemeController::class, 'typography'])->name('typography');
        Route::get('effects', [\App\Http\Controllers\Admin\ThemeController::class, 'effects'])->name('effects');
        Route::post('/', [\App\Http\Controllers\Admin\ThemeController::class, 'store'])->name('store');
    });
});

// Users management (Admin Only)
Route::middleware('permission:manage-users')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['show']);
});
