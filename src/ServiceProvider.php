<?php

namespace TarekAdam\DayVillas;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider
 * @package TarekAdam\LaravelCors
 */
class ServiceProvider extends BaseServiceProvider{
	CONST VENDOR_PATH = 'tarek-adam/day-villas';
	CONST SHORT_NAME = 'day-villas';

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot(){

		$this->bootConfig();

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register(){

		$this->app->singleton(Client::class, function ($app){
			return new Client(config(self::SHORT_NAME . '.base_url'),
			                  config(self::SHORT_NAME . '.key'),
			                  config(self::SHORT_NAME . '.pass'),
			                  config(self::SHORT_NAME . '.token'));
		});

	}

	/**
	 * @internal
	 */
	private function bootConfig(){
		$this->publishes([__DIR__ . '/../config/main.php' => config_path(SELF::SHORT_NAME . '.php')], 'config');
		$this->mergeConfigFrom(__DIR__ . '/../config/main.php', SELF::SHORT_NAME);
	}

}