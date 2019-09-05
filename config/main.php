<?php
/**
 * @see https://secure.365villas.com/vros/api/intro/
 */
return [
	'base_url' => env('DV_BASE_URL'),
	'token'    => env('DV_TOKEN'),
	'key'      => env('DV_KEY'),
	'pass'     => env('DV_PASS'),
	'headers'  => ['Cache-Control' => 'no-cache',
	               'Content-Type'  => 'application/x-www-form-urlencoded']
];