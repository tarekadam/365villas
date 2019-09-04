<?php

namespace BoneCreative\Test;

use Orchestra\Testbench\TestCase as BaseCase;
use BoneCreative\DayVillas\ServiceProvider;

class TestCase extends BaseCase{
	protected function setUp(){
		parent::setUp();

		$this->loadLaravelMigrations();
	}


	protected function getPackageProviders($app){
		return [ServiceProvider::class];
	}

}