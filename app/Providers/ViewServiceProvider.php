<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        if (Schema::hasTable('profiles')) {
            $profile = \App\Models\Profile::findOrNew(1);
            if ($profile) {
            view()->share('profile', $profile);
            }
        }
    }
}
