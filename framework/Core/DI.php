<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework 2
 */

namespace SF3\Core;

use SF3\Core\DI\DISingleton;

class DI
{
	/**
	 * @var dis
	 */
	private static $dis = [];

	/**
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * @param string
	 * @param Closure
	 *
	 * @return void|DiCache
	 */
	public static function bind($key, $closure)
	{
		self::$dis[strtolower($key)] = $closure();
	}

	/**
	 * @param string
	 * @return value|false
	 */
	public static function resolve($key)
	{
		if (isset(self::$dis[strtolower($key)])) {
			return self::$dis[strtolower($key)];
		}

		return false;
	}

	/**
	 * @param string
	 * @param Closure
	 *
	 * @return new Di\Singleton
	 */

	public static function singleton($key, $closure)
	{

		//self::$dis[strtolower($key)] = $closure();

		return new DISingleton($key, $closure());
	}

	/**
	 * @param string
	 *
	 * @return bool
	 */
	public static function remove($key = "")
	{
		if (isset(self::$dis[strtolower($key)])) {
			unset(self::$dis[strtolower($key)]);

			return true;
		}

		return false;
	}

	/**
	 * @param string
	 *
	 * @return bool
	 */
	public static function has($key = "")
	{
		if (isset(self::$dis[strtolower($key)])) {
			return true;
		}

		return false;
	}

	/**
	 *
	 * @return void
	 */
	public static function reset()
	{
		foreach (self::$dis as $key => $value) {
			unset(self::$dis[$key]);
		}
	}

	/**
	 *
	 * @return array
	 */
	public static function getKeys()
	{
		$arr = [];

		foreach (self::$dis as $key => $value) {
			$arr[] = $key;
		}

		return $arr;
	}

	/**
	 *
	 * @return array
	 */
	public static function getAll()
	{
		return self::$dis;
	}

	/**
	 *
	 * @return int
	 */
	public static function count()
	{
		return count(self::$dis);
	}
}




    
    