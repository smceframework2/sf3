<?php

namespace SF3\Security\Guard;
class Timepersecond
{
	private $redis;
	private $params;

	public function __construct($redis, $params)
	{
		$this->redis  = $redis;
		$this->params = (object) $params;
	}

	public function push($token)
	{
		$ms = time();
		if (!$this->control($token, $ms)) {
			$this->redis->getRedis()->zadd($this->params->namespace . $token, $ms, $token . $ms . rand(0, 9999999));
			$this->redis->getRedis()->expire($this->params->namespace . $token, $this->params->pertime);

			return false;
		}

		return true;
	}

	private function control($token, $ms)
	{
		$e = $this->redis->getRedis()->ZRANGEBYSCORE($this->params->namespace . $token, $ms - ($this->params->pertime), $ms);
		if (count($e) >= $this->params->requestcount) {
			return true;
		}
	}
}