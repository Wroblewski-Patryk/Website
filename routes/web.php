<?php

use Illuminate\Support\Facades\Route;
use App\Models\Page;
use Inertia\Inertia;

Route::get('/', function () {
    $page = Page::where('slug', 'home')->first();

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
Route::get('/{slug}', function ($slug) {
    $page = Page::where('slug', $slug)->firstOrFail();

    return Inertia::render('Welcome', [
    'page' => $page
    ]);
})->where('slug', '^(?!admin|livewire|storage|_debugbar|build|api).*$');
