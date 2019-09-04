<?php

namespace TarekAdam\Test;

use Orchestra\Testbench\TestCase as BaseCase;
use TarekAdam\DayVillas\ServiceProvider;

class TestCase extends BaseCase{
	protected function setUp(){
		parent::setUp();

		$this->loadLaravelMigrations();
	}


	protected function getPackageProviders($app){
		return [ServiceProvider::class];
	}

}