<?php

namespace Alaouy\YouTube;

use Illuminate\Support\ServiceProvider;

class YouTubeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(array(__DIR__ . '/config/youtube.php' => config_path('youtube.php')));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('youtube', function () {
            return new YouTube(config('youtube.key'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [YouTube::class];
    }
}
