<?php

use Illuminate\Support\Facades\Route;
use App\Models\Page;
use Inertia\Inertia;

// Redirect default login name to Admin login
Route::redirect('login', 'admin/login')->name('login');

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

                Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->except(['show']);
                Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->except(['show']);
                Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)->only(['index', 'store', 'destroy']);
                Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class)->except(['show']);

                Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class , 'index'])->name('settings.index');
                Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class , 'store'])->name('settings.store');
            }
            );
        });

// Public Routes
Route::get('/', function () {
    $settings = \App\Models\Setting::where('key', 'general')->value('value') ?? [];
    $homeId = $settings['home_page_id'] ?? null;

    if ($homeId) {
        $page = Page::with(['headerOverride', 'footerOverride'])->find($homeId);
    }
    else {
        $page = Page::with(['headerOverride', 'footerOverride'])->where('slug->en', 'home')->orWhere('slug->pl', 'home')->first();
    }

    if (!$page) {
        // Fallback or abort
        return Inertia::render('Welcome', ['page' => null]);
    }

    return Inertia::render('Public/Page', [
    'page' => $page,
    'settings' => $settings
    ]);
});

Route::post('/contact', [\App\Http\Controllers\ContactController::class , 'send'])->name('contact.send');

Route::get('/blog', function (\Illuminate\Http\Request $request) {
    $posts = \App\Models\Post::where('is_published', true)
        ->latest('published_at')
        ->paginate(12);

    return Inertia::render('Blog/Index', [
    'posts' => $posts,
    ]);
});

Route::get('/blog/{slug}', function (string $slug) {
    $post = \App\Models\Post::where('slug', $slug)
        ->where('is_published', true)
        ->firstOrFail();

    return Inertia::render('Blog/Show', [
    'post' => $post,
    ]);
});

Route::get('/live-preview', function () {
    return Inertia::render('Preview', [
    'page' => new \App\Models\Page()
    ]);
})->name('live-preview');

Route::get('/{slug}', function ($slug) {
    if ($slug === 'home')
        return redirect('/');

    $page = Page::with(['headerOverride', 'footerOverride'])
        ->where('slug->en', $slug)
        ->orWhere('slug->pl', $slug)
        ->firstOrFail();

    $settings = \App\Models\Setting::where('key', 'general')->value('value') ?? [];

    return Inertia::render('Public/Page', [
    'page' => $page,
    'settings' => $settings
    ]);
})->where('slug', '^(?!admin|livewire|storage|_debugbar|build|api).*$');
