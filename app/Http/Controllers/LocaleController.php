<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function switch ($lang)
    {
        $activeLanguages = \App\Models\Language::where('is_active', true)->pluck('code')->toArray();

        if (!in_array($lang, $activeLanguages)) {
            abort(400);
        }

        Session::put('locale', $lang);
        App::setLocale($lang);

        return redirect()->back();
    }
}
