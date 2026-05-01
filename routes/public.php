<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Special previews
Route::middleware(['web', 'auth', 'can:view-admin'])->group(function () {
    Route::get('/forms/{id}/preview', function ($id) {
        $form = \App\Models\Form::findOrFail($id);

        return Inertia::render('Public/FormPreview', [
            'form' => $form,
            'settings' => \App\Models\Setting::pluck('value', 'key')->toArray(),
        ]);
    })->name('forms.preview');
});

Route::post('/forms/{form}/submit', [\App\Http\Controllers\PublicFormController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('forms.submit');

// Localized public content entrypoints.
// Pages, post details, and project details all flow through the same resolver
// so archive pages configured in settings remain the source of truth.
Route::get('/', [PageController::class, 'show'])->name('home');
Route::get('/{path}', [PageController::class, 'show'])
    ->where('path', '.*')
    ->name('public.content.show');
