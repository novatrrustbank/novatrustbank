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
        // Force HTTPS on production or Render environment
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        // Prevent index-size errors on older MySQL versions
        Schema::defaultStringLength(191);

        // USER ONLINE STATUS HANDLER
        view()->composer('*', function () {

            if (Auth::check()) {

                // Update last activity timestamp
                DB::table('users')
                    ->where('id', Auth::id())
                    ->update([
                        'updated_at' => Carbon::now(),
                        'status'     => 'online',
                    ]);
            }

            // Set users offline if inactive 3+ minutes
            DB::table('users')
                ->where('updated_at', '<', Carbon::now()->subMinutes(3))
                ->update([
                    'status' => 'offline',
                ]);

        });
    }
}
