<?php namespace Alaouy\Youtube;

use Illuminate\Support\ServiceProvider;
use Config;

class YoutubeServiceProvider extends ServiceProvider {

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
		$this->package('alaouy/youtube', 'alaouy/youtube');
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
		$this->app['youtube'] = $this->app->share(function($app)
	  {
	  	$key = Config::get('alaouy/youtube::KEY');
	    return new Youtube($key);
	  });
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

}
