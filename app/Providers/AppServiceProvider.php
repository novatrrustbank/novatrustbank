<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 🔒 Force HTTPS on production (Render fix)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);
    }
}
