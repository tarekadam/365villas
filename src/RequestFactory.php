<?php

namespace TarekAdam\DayVillas;

use TarekAdam\DayVillas\Exceptions\OptionTypeErrorException;

final class RequestFactory{
	/**
	 * @var string
	 */
	private $options;

	public function __construct(string $name){
		if(file_exists(__DIR__ . '/../requests/' . $name . '.json')){
			$json          = file_get_contents(__DIR__ . '/../requests/' . $name . '.json');
			$this->options = json_decode($json, true);
		}else{
			$this->options = [];
		}
	}

	public function make(array $options = []){

		$acceptable_types = [
			"boolean",
			"integer",
			"string",
		];

		foreach($this->options as $key => $type_or_default_or_null){
			if(!empty($options[$key]) and in_array($type_or_default_or_null, $acceptable_types)){
				$required_type = $type_or_default_or_null;
				$actual_type   = gettype($options[$key]);

				if($required_type == 'boolean'
					and $actual_type == 'integer'
					and in_array($options[$key], [0, 1])){
					continue;
				}

				if($required_type != $actual_type){
					throw new OptionTypeErrorException("$key must be a(n) $required_type. $actual_type given.");
				}


			}elseif($type_or_default_or_null == 'date' and !empty($options[$key])){

				if(\DateTime::createFromFormat('Y-m-d', $options[$key]) !== FALSE){
					continue;
				}

				throw new OptionTypeErrorException("$key must be a date formatted as Y-m-d. Given ". $options[$key]);

			}elseif(empty($options[$key]) and isset($options[$key])){
				$options[$key] = $options[$key];
			}elseif(!empty($type_or_default_or_null) and empty($options) and !in_array($type_or_default_or_null, $acceptable_types)){
				$options[$key] = $type_or_default_or_null;
			}
		}

		return array_filter($options);
	}

}