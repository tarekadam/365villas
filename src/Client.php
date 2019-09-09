<?php

namespace TarekAdam\DayVillas;

use GuzzleHttp\Client as Guzzle;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use TarekAdam\DayVillas\Exceptions\MissingEndpointException;
use TarekAdam\DayVillas\Exceptions\UnprocessableResponseException;
use TarekAdam\PageStream\Paginatable;

class Client implements Arrayable, Jsonable, Paginatable{

	private $guzzle;
	private $endpoints;

	private $key;
	private $owner;
	private $pass;
	private $request = [];
	private $response = [];
	private $last_call;

	public $status = 205;

	public function __construct(string $api, string $key, string $pass, string $owner){
		$headers      = config(ServiceProvider::SHORT_NAME . '.headers');
		$this->guzzle = new Guzzle(['base_uri' => $api, 'headers' => $headers]);

		$this->endpoints = parse_ini_file(__DIR__ . '/../config/endpoints.ini', true);

		$this->key   = $key;
		$this->pass  = $pass;
		$this->owner = $owner;
	}

	public function __call($name, $arguments){
		if(empty($this->endpoints[$name])){
			throw new MissingEndpointException($name);
		}

		if(!empty($this->endpoints[$name]['READ-ME'])){
			unset($this->endpoints[$name]['READ-ME']);
		}

		if(!empty($arguments)){
			$arguments = $arguments[0];
		}

		$this->last_call = $name;

		$request = new RequestFactory($name);
		$options = $request->make($arguments);

		$route_info = $this->endpoints[$name];
		$params     = [
				'owner_token' => $this->owner,
				'key'         => $this->key,
				'pass'        => $this->pass,
				'action'      => (!empty($route_info['action'])) ? $route_info['action']:strtolower($name),
			] + $options;

		$url = $route_info['uri'];
		foreach($params as $k => $v){
			$pattern = '{' . strtoupper($k) . '}';
			if(strpos($url, $pattern) !== false){
				$url = str_replace($pattern, $v, $url);
				unset($params[$k]);
			}
		}

		$call = strtolower($route_info['method']);

		$this->request = [
			'method' => $call,
			'url'    => $url,
			'params' => $params
		];

		$this->exchangeData();

		return $this;
	}

	private function parseResponse(ResponseInterface $response){
		$this->status = $response->getStatusCode();

		try{
			$body           = $response->getBody();
			$this->response = (!empty($body)) ? json_decode($body, true):[];

			if(json_last_error() !== JSON_ERROR_NONE){
				throw new UnprocessableResponseException('Could not read response.');
			}

		}catch(\Exception $exception){
			Log::error($exception);
			$this->status   = 205;
			$this->response = [];
		}
	}

	public function toArray(): array{
		if(!empty($this->endpoints[$this->last_call]['data'])){
			$target = $this->endpoints[$this->last_call]['data'];
			if(isset($this->response[$target])){
				return (is_array($this->response[$target])) ? $this->response[$target]:[];
			}
		}

		return (empty($this->response['data'])) ? $this->response:$this->response['data'];
	}

	/**
	 * @param int $optionsv
	 *
	 * @return false|string
	 */
	public function toJson($options = 0){
		$data = $this->toArray();

		return json_encode($data, $options);
	}

	public function totalPages(): int{
		if(!empty($this->response['page_count'])){
			return $this->response['page_count'];
		}

		return 0;
	}

	public function getPage(): int{
		if(!empty($this->response['page'])){
			return $this->response['page'];
		}

		return 0;
	}

	public function setPage(int $page): void{
		if(empty($this->request['params'])){
			$this->request['params'] = [];
		}
		$this->request['params']['page'] = $page;
	}

	public function exchangeData(): void{

		$call   = $this->request['method'];
		$url    = $this->request['url'];
		$params = $this->request['params'];

		$data = [];

		if($call == 'get' and !empty($params)){
			unset($params['key']);
			unset($params['pass']);
			unset($params['action']);
			$url .= '?' . http_build_query($params);
		}

		if($call == 'post' and !empty($params)){
			//unset($params['owner_token']);
			$data = [
				'debug' => env('APP_DEBUG'),
				'body'  => http_build_query($params)];
		}

		$response = $this->guzzle->$call($url, $data);
		$this->parseResponse($response);
		$this->applyFilters();
	}

	private function applyFilters(){

		if(empty($this->response)
			or empty($this->endpoints[$this->last_call])){
			return;
		}

		if(empty($this->endpoints[$this->last_call]['filters'])){
			$this->endpoints[$this->last_call]['filters'] = '';
		}

		$filters = explode(',', $this->endpoints[$this->last_call]['filters']);
		$filters[] = $this->last_call;

		foreach($filters as $filter){
			switch($filter){
				case 'pos':
					$this->response = pos($this->response);
					break;

				case 'amenitiesLibrary':
					$this->response = collect($this->response)->pluck('amenity')->flatten(1)->all();
					break;
			}
		}

	}
}