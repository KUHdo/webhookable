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
        //register routes
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        // register migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // publishing
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
        $this->publishes([
            __DIR__.'/../resources/js/components' => resource_path('js/components/webhookable'),
        ], 'webhookable-components');
        // localization
        //$this->publishes([
        //__DIR__.'/translations' => resource_path('lang/vendor/webhookable'),
        //]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/webhookable.php', 'webhookable');
        }
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/webhookable.php' => config_path('webhookable.php'),
            ], 'webhookable-config');
        }

        $this->app->bind(
            'KUHdo\Webhookable\Repositories\WebHookRepository',
            'KUHdo\Webhookable\Repositories\EloquentWebHook'
        );

    }
}