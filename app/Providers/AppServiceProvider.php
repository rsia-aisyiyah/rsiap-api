<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Illuminate\Support\Facades\Storage::extend('sftp', function ($app, $config) {
            return new \League\Flysystem\Filesystem(new \League\Flysystem\Sftp\SftpAdapter($config));
        });    
    }
}
