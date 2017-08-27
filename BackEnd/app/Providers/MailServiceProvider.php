<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Api\MailController;

class MailServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(MailController::class, function ($app) {
            return new MailController();
        });
    }

    public function provides()
    {
        return [MailController::class];
    }
}
