<?php

use Illuminate\Support\Facades\Route;
use App\Models\Page;
use Inertia\Inertia;

Route::get('/', function () {
    $homeSlug = \App\Models\Setting::where('key', 'home_page_slug')->value('value') ?? 'home';
    $page = Page::with(['headerOverride', 'footerOverride'])->where('slug', $homeSlug)->first();

    return Inertia::render('Welcome', [
    'page' => $page
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
    // If user tries to visit the slug directly, check if it's the home page
    $homeSlug = \App\Models\Setting::where('key', 'home_page_slug')->value('value') ?? 'home';
    if ($slug === $homeSlug) {
        return redirect('/');
    }

    $page = Page::with(['headerOverride', 'footerOverride'])->where('slug', $slug)->firstOrFail();

    return Inertia::render('Welcome', [
    'page' => $page
    ]);
})->where('slug', '^(?!admin|livewire|storage|_debugbar|build|api).*$');
