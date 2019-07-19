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
            __DIR__.'/../resources/js/components' => base_path('resources/js/components/passport'),
        ], 'passport-components');

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
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/passport.php', 'passport');
        }
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/passport.php' => config_path('passport.php'),
            ], 'passport-config');
        }

        $this->app->bind(
            'KUHdo\Webhookable\Repositories\WebHookRepository',
            'KUHdo\Webhookable\Repositories\EloquentWebHook'
        );

    }
}