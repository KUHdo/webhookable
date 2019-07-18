<?php

namespace KUHdo\Webhookable;

use Illuminate\Support\ServiceProvider;

class WebhookableProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Repositories\WebHook\WebHookRepository',
            'App\Repositories\WebHook\EloquentWebHook'
        );

    }
}