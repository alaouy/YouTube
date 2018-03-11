<?php

namespace Alaouy\Youtube;

use Illuminate\Support\ServiceProvider;

class YoutubeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__ . '/config/youtube.php');

        $this->publishes([$source => config_path('youtube.php')]);

        $this->mergeConfigFrom($source, 'youtube');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Youtube::class, function () {
            return new Youtube(config('youtube.key'));
        });

        $this->app->alias(Youtube::class, 'youtube');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Youtube::class];
    }
}
