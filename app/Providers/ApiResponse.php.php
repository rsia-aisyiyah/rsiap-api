<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ApiResponse extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path('ApiResponse.php');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
