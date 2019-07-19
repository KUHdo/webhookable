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
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/src/Migrations');
        $this->loadTranslationsFrom(__DIR__.'/translations', 'webhookable');
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
        $this->publishes([
            __DIR__.'/resources/assets/js' => public_path('vendor/webhookable'),
        ], 'public');
        $this->publishes([
        __DIR__.'/translations' => resource_path('lang/vendor/webhookable'),
        ]);
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