<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Force HTTPS on production or Render environment
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        // Prevent index-size errors on older MySQL versions
        Schema::defaultStringLength(191);

        // ================================
        // USER ONLINE STATUS HANDLER
        // ================================
        view()->composer('*', function () {

            if (Auth::check()) {

                // Update online status and last active timestamp
                DB::table('users')
                    ->where('id', Auth::id())
                    ->update([
                        
                    ]);

                // Set users offline if inactive for 3+ minutes
                DB::table('users')
                    
            }
        });
    }
}
