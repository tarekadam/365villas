<?php

namespace BoneCreative\DayVillas;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider
 * @package BoneCreative\LaravelCors
 */
class ServiceProvider extends BaseServiceProvider{
	CONST VENDOR_PATH = 'bone-creative/day-villas';
	CONST SHORT_NAME = 'day-villas';

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot(){

		$this->bootConfig();
		//$this->bootMigrations();

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register(){

	}


	/**
	 * @internal
	 */
	private function bootConfig(){
		$this->publishes([__DIR__ . '/../config/main.php' => config_path(SELF::SHORT_NAME . '.php')], 'config');
		$this->mergeConfigFrom(__DIR__ . '/../config/main.php', SELF::SHORT_NAME);
	}

	/**
	 * @internal
	 */
	private function bootMigrations(){
		$this->loadMigrationsFrom(__DIR__ . '/../migrations');
	}

}