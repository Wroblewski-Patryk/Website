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

Route::get('/{path?}', [PageController::class, 'show'])->where('path', '.*');

// Przeniesiona naprawa kolejności przez wymuszenie manualnego loadu w boot.php
