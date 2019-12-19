<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */

namespace EF2\Core;

class EventManager
{
	private static $evts;

	public static function __callStatic($key, $params)
	{
		if (substr($key, 0, 3) == "get") {
			$es = strtolower(substr($key, 3, strlen($key)));
		} else {
			$es = strtolower($key);
		}

		if (!isset(self::$evts[$es]) || !is_callable(self::$evts[$es])) {
			throw new \Exception("Method Not Found Exception");
		}

		return call_user_func_array(self::$evts[$es], $params);
	}

	public function __call($key, $params)
	{
		if (substr($key, 0, 3) == "get") {
			$es = strtolower(substr($key, 3, strlen($key)));
		} else {
			$es = strtolower($key);
		}

		if (!isset(self::$evts[$es]) || !is_callable(self::$evts[$es])) {
			throw new \Exception("Method Not Found Exception");
		}

		return call_user_func_array(self::$evts[$es], $params);
	}

	public static function push($key, $class)
	{
		self::$evts[strtolower($key)] = $class;
	}

	public static function pull($key)
	{
		if (isset(self::$evts[strtolower($key)])) {
			return self::$evts[strtolower($key)];
		} else {
			return false;
		}
	}

	public static function bind($key, $class)
	{
		self::$evts[strtolower($key)] = $class();
	}

	public static function remove($key = "")
	{
		if (isset(self::$evts[strtolower($key)])) {
			unset(self::$evts[strtolower($key)]);

			return true;
		}

		return false;
	}

	public static function has($key = "")
	{
		if (isset(self::$evts[strtolower($key)])) {
			return true;
		}

		return false;
	}

	public static function reset()
	{
		foreach (self::$evts as $key => $value) {
			unset(self::$evts[$key]);
		}
	}

	public static function getKeys()
	{
		foreach (self::$evts as $key => $value) {
			$arr[] = $key;
		}

		return $arr;
	}

	public static function getAll()
	{
		return self::$evts;
	}

	public static function getCount()
	{
		if (is_array(self::$evts)) {
			return count(self::$evts);
		} else {
			return 0;
		}
	}
}