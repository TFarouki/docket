<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Global System Language
        $systemLocale = \Illuminate\Support\Facades\Cache::remember('system_locale', 3600, function () {
            return \App\Models\Setting::where('key', 'system_locale')->value('value');
        });

        if ($systemLocale) {
            app()->setLocale($systemLocale);
        } elseif (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        return $next($request);
    }
}
