<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        //
        // Check if database connection is available before accessing Profile
        if (Schema::hasTable('profiles')) {
            
            $profile = \App\Models\Profile::findOrNew(1);

            if ($profile->email && $profile->email_port && $profile->email_username && $profile->email_password) {
            config([
                'mail.mailers.smtp.host' => $profile->email_server,
                'mail.mailers.smtp.port' => $profile->email_port,
                'mail.mailers.smtp.username' => $profile->email_username,
                'mail.mailers.smtp.password' => $profile->email_password,
                'mail.from.address' => $profile->email,
                'mail.from.name' => $profile->nama,
            ]);
            }

            if ($profile->nama) {
            config(['app.name' => $profile->nama]);
            }
        }

    }
}
