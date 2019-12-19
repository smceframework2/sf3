<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */

namespace EF2\Driver;

class Redis
{
	/**
	 * @var array redis config
	 */
	private $config = [];

	/**
	 * @var redis instance
	 */
	private $redis;


	/*
	 * @return redis connect
	 */
	public function getRedis()
	{

		return $this->redis;
	}

	/*
	 * @return void
	 */
	public function setConfig($config)
	{
		$this->config = $config;
	}

	/*
	* @return array
	*/
	public function getConfig()
	{

		return $this->config;

	}

	/**
	 * @return void
	 */
	public function connect()
	{

		if(!isset($this->config))
		{
			throw new \Exception("redis server configuration must have \"host\" and \"port\" values in array.");
		}

		if(empty($this->config["host"]) && !empty($this->config["port"]))
		{

			throw new \Exception("redis server configuration must have \"host\" and \"port\" not empty");

		}

		$this->redis = new \Redis;

		if (!$this->redis->connect($this->config["host"], $this->config["port"]))
		{

			throw new \Exception("Failed on connecting to redis server at " . $this->config["host"] . ":" . $this->config["port"]);

		}

	}

	/**
	 * @param string
	 *
	 * @return mixed
	 */
	public function get($key,$isJon=false)
	{
		if($isJon)
		{
			return json_decode($this->redis->get($key));
		}

		return unserialize($this->redis->get($key));

	}

	/**
	 * @param string
	 * @param mixed
	 * @param int
	 *
	 * @return set
	 */
	public function set($key, $value, $duration,$isJson=false)
	{

		if($isJson)
		{
			return $this->redis->set($key, json_encode($value), $duration);
		}

		return $this->redis->set($key, serialize($value), $duration);
	}


	/**
	 * @param string
	 * @param mixed
	 * @param int
	 *
	 * @return delete
	 */
	public function delete($key)
	{

		return $this->redis->delete($key);
	}

	/**
	 * @param string
	 * @param mixed
	 *
	 * @return lpush
	 */
	public function lpush($key, $value)
	{

		return $this->redis->lpush($key, $value);
	}

	/**
	 * @param string
	 * @param long
	 * @param long
	 *
	 * @return lrange
	 */
	public function lrange($key, $x, $y)
	{

		return $this->redis->lrange($key, $x, $y);
	}


	/*** @param string
	 *
	 * @return hpop
	 */
	public function lpop($key)
	{

		return $this->redis->lpop($key);
	}

	/**
	 * @param string
	 * @param string
	 *
	 * @return hdel
	 */
	public function hdel($key, $key2)
	{

		return $this->redis->hdel($key, $key2);
	}




	/**
	 * @param string
	 * @param string
	 *
	 * @return sadd
	 */
	public function sadd($key,$value)
	{

		return $this->redis->sadd($key, $value);
	}

	/**
	 * @param string
	 * @param string
	 *
	 * @return srandmember
	 */
	public function srandmember($key,$count)
	{

		return $this->redis->SRANDMEMBER($key, $count);
	}


}