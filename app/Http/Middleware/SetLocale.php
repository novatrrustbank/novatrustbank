<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = [
            'en',
            'fr',
            'es',
            'de',
            'it',
            'pt',
        ];

        // Use previously selected language if available
        $locale = session('locale');

        // Otherwise detect browser language
        if (!$locale) {
            $browserLocale = $request->getPreferredLanguage($supportedLocales);

            $locale = $browserLocale
                ? substr($browserLocale, 0, 2)
                : 'en';

            session(['locale' => $locale]);
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
