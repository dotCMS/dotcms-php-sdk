<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\DotCmsHelpers;

class DotCMSHelpersServiceProvider extends ServiceProvider
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
        // Share the DotCmsHelpers with all views
        View::share('dotCmsHelpers', new DotCmsHelpers());
    }
} 