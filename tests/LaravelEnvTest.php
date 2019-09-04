<?php

namespace TarekAdam\Test;

use Carbon\Carbon;

/**
 * Class LaravelEnvTest
 * @package TarekAdam\Test
 */
class LaravelEnvTest extends TestCase{

	/** @test */
	public function it_runs_the_migrations(){
		$now = Carbon::now();
		\DB::table('users')->insert([
			                            'name'       => 'Orchestra',
			                            'email'      => 'hello@orchestraplatform.com',
			                            'password'   => \Hash::make('456'),
			                            'created_at' => $now,
			                            'updated_at' => $now,
		                            ]);
		$users = \DB::table('users')->where('id', '=', 1)->first();
		$this->assertEquals('hello@orchestraplatform.com', $users->email);
		$this->assertTrue(\Hash::check('456', $users->password));
	}

}