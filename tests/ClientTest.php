<?php

namespace TarekAdam\Test;

use TarekAdam\DayVillas\Client;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class ClientTest
 * @package TarekAdam\Test
 *
 * @see ../config/endpoints.ini
 */
class ClientTest extends TestCase{

	private $endpoints;
	private $client;

	protected function setUp(){
		parent::setUp();

		$this->endpoints = parse_ini_file(__DIR__ .'/../config/endpoints.ini', true);
		$this->client = app(Client::class);
	}

	/**
	 * @see \TarekAdam\DayVillas\Client::__call
	 * @test
	 */
	public function can_call_endpoints(){

	    foreach($this->endpoints as $call => $endpoint){
	    	$this->client->$call();
	    	$this->assertEquals(200, $this->client->status);
	    }

	}

	public function can_paginate(){
		$data = $this->client->listProperties(['limit' => 2]);

		$tally = 0;
		foreach($data as $datum){
			$tally++;
		}

		$this->assertGreaterThan(2, $tally);
	}

}
