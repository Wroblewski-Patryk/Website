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

Route::get('/{slug}', function ($slug) {
    $page = Page::where('slug', $slug)->firstOrFail();

    return Inertia::render('Welcome', [
    'page' => $page
    ]);
})->where('slug', '^(?!admin|livewire|storage|_debugbar|build|api).*$');
