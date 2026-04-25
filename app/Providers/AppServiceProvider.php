<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL as FacadeURL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * ✅ FORCE HTTPS (VERY IMPORTANT ON RENDER)
         * This prevents CSRF token mismatch caused by http/https switching
         */
        if (
            env('APP_ENV') === 'production' ||
            env('APP_URL', null)
        ) {
            URL::forceScheme('https');
        }

        /**
         * ✅ Fix old MySQL compatibility (safe default)
         */
        Schema::defaultStringLength(191);

        /**
         * ✅ Prevent mixed content / wrong asset URLs
         */
        if (app()->environment('production')) {
            FacadeURL::forceRootUrl(config('app.url'));
        }
    }
}
