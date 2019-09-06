<?php

namespace TarekAdam\Test;

use TarekAdam\DayVillas\RequestFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestFactoryTest extends TestCase{

	private $endpoints;

	protected function setUp(){
		parent::setUp();
		$this->endpoints = parse_ini_file(__DIR__ . '/../config/endpoints.ini', true);
	}

	/**
	 * @see          \TarekAdam\DayVillas\RequestFactory::make
	 *
	 * @dataProvider optionDataProvider
	 * @test
	 */
	public function can_make_requests($name, $option_sets){

		$request_factory = new RequestFactory($name);
		foreach($option_sets as $options){
			$request_options = $request_factory->make($options);
			$this->assertTrue(is_array($request_options));
		}

	}

	public function optionDataProvider(){
		return [
			['listProperties',
			 [[],
			  ['keyterm' => 'override property'],
			  ['keyterm' => ''],
			  ['page' => 3, 'limit' => 3],
			  [
				  "page"           => 3,
				  "limit"          => 3,
				  "isfeatured"     => 1,
				  "category_id"    => 3,
				  "keyterm"        => "string",
				  "checkin"        => "1978-05-29",
				  "checkout"       => "1978-05-30",
				  "numberadults"   => 3,
				  "children"       => 3,
				  "airconditioner" => 0,
				  "parking"        => true,
				  "ocean"          => false,
				  "allowpet"       => 0,
				  "allowsmoking"   => 0
			  ]]
			]
		];
	}


}
