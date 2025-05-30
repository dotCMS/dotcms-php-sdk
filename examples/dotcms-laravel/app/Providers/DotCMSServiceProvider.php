<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;

class DotCMSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(DotCMSClient::class, function ($app) {
            $config = new Config(
                host: env('DOTCMS_HOST', 'https://demo.dotcms.com'),
                apiKey: env('DOTCMS_API_KEY', '')
            );
            
            return new DotCMSClient($config);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
