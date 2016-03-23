<?php

namespace Alaouy\Youtube;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class YoutubeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isLegacyLaravel() || $this->isOldLaravel()) {
            $this->package('alaouy/youtube', 'alaouy/youtube');
        }

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Youtube', 'Alaouy\Youtube\Facades\Youtube');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->isLegacyLaravel() || $this->isOldLaravel()) {
            $this->app['youtube'] = $this->app->share(function ($app) {
                $key = \Config::get('alaouy/youtube::KEY');
                return new Youtube($key);
            });

            return;
        }

        $this->publishes(array(__DIR__ . '/../../config/youtube.php' => config_path('youtube.php')));

        //Laravel 5.1+ fix
        if(floatval(Application::VERSION) >= 5.1){
            $this->app->bind("youtube", function(){
                return $this->app->make('Alaouy\Youtube\Youtube', [config('youtube.KEY')]);
            });
        }else{
            $this->app->bindShared('youtube', function () {
                return $this->app->make('Alaouy\Youtube\Youtube', [config('youtube.KEY')]);
            });
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('youtube');
    }

    public function isLegacyLaravel()
    {
        return Str::startsWith(Application::VERSION, array('4.1.', '4.2.'));
    }

    public function isOldLaravel()
    {
        return Str::startsWith(Application::VERSION, '4.0.');
    }
}
