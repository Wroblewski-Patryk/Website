<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use Inertia\Inertia;

// Public Pages
Route::get('/', [PageController::class , 'show'])->name('home');

// Special previews
Route::get('/forms/{id}/preview', function ($id) {
    $form = \App\Models\Form::findOrFail($id);
    return Inertia::render('Public/FormPreview', [
    'form' => $form,
    'settings' => \App\Models\Setting::pluck('value', 'key')->toArray()
    ]);
})->name('forms.preview');

// Generic Catch-all for Pages (should be last)
Route::get('/{path?}', [PageController::class , 'show'])
    ->name('page.show')
    ->where('path', '^(?!dashboard|auth|livewire|storage|_debugbar|build|api|lang|sitemap|robots).*$');
