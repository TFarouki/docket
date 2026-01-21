<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function updateLocale(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'system_locale' => ['required', 'in:en,ar,fr'],
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'system_locale'],
            ['value' => $request->system_locale]
        );

        \Illuminate\Support\Facades\Cache::forget('system_locale');

        return back();
    }

    public function setSessionLocale($locale)
    {
        if (in_array($locale, ['en', 'ar', 'fr'])) {
            session(['locale' => $locale]);
        }
        return back();
    }
}
