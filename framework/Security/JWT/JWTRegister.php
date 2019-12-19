<?php

namespace EF2\Security\JWT;

use EF2\Driver\Redis;

class JWTRegister
{
	/**
	 * @var Redis $redis
	 */
	public $redis;

	public function __construct($redis)
	{
		$this->redis = $redis;
	}

	public function login($id, $token, $duration)
	{
		$duration = (int) $duration;

		$this->logout($id);
		$this->redis->set($this->getTokenKey($token), $token, $duration);
		$this->redis->set($this->getIdKey($id), $token, $duration);
		$this->redis->set($this->getTokenTtlKey(), $duration, $duration);
	}

	public function logout($id)
	{
		if ($token = $this->redis->get($this->getIdKey($id))) {
			$this->redis->delete($this->getTokenKey($token));
			$this->redis->delete($this->getIdKey($id));

			return true;
		}

		return false;
	}

	public function getToken($id)
	{
		if ($token = $this->redis->get($this->getIdKey($id))) {
			return $token;
		}
	}

	public function control($token)
	{
		if ($gettoken = $this->redis->get($this->getTokenKey($token))) {
			$this->setExpire($token);

			return true;
		}
	}

	private function setExpire($token)
	{
		if ($duration = $this->redis->get($this->getTokenTtlKey())) {
			$this->redis->getRedis()->expire($this->getTokenKey($token), $duration);
		}
	}

	private function getTokenKey($token)
	{
		return "auth:token:" . $token;
	}

	private function getIdKey($id)
	{
		return "auth:id:" . $id;
	}

	private function getTokenTtlKey()
	{
		return "settings:tokenttl";
	}
}
