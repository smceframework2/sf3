<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */

namespace SF3\Security;

use SF3\Core\Router\Exception;
use SF3\Http\HttpException;
use SF3\Security\Guard\Timepersecond;

class Guard
{
	private $redis;
	private $args = [];

	public function setRedis($redis)
	{
		$this->redis = $redis;
	}

	public function timePerRequest($namespace = "", $pertimesecond = 1, $requestcount = 1)
	{
		$namespace = "guard:timePerRequest:" . $namespace;

		$this->args["timeperrequest"] = [
			"namespace"    => $namespace,
			"pertime"      => $pertimesecond,
			"requestcount" => $requestcount,
		];

		return $this;
	}

	public function verify($token)
	{
		if ($this->redis == null) {
			return new HttpException("500", "not set redis");
		}

		foreach ($this->args as $key => $value) {
			if ($key == "timeperrequest") {
				$timepersecond = new Timepersecond($this->redis, $value);

				if ($timepersecond->push($token)) {
					return false;
				}
			}
		}

		return true;
	}
}