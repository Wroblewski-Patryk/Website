<?php

use Illuminate\Support\Facades\Route;
use App\Models\Page;
use Inertia\Inertia;

use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\ContactController;
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
                Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)->only(['index', 'store', 'destroy']);
                Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class)->except(['show']);
                Route::resource('forms', \App\Http\Controllers\Admin\FormController::class)->except(['show']);
                Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class)->except(['show']);
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
        return Inertia::render('Welcome', ['page' => null]);
    }

    return Inertia::render('Public/Page', [
    'page' => $page,
    'settings' => $settings,
    'menus' => \App\Models\Menu::all(),
    'all_projects' => \App\Models\Project::all()
    ]);
});

Route::post('/contact', [ContactController::class , 'store'])->name('contact.store');

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
    'settings' => $settings,
    'menus' => \App\Models\Menu::all(),
    'all_projects' => \App\Models\Project::all()
    ]);
})->where('slug', '^(?!admin|livewire|storage|_debugbar|build|api).*$');
