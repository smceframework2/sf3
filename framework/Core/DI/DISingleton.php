<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */

namespace SF3\Core\DI;

class DISingleton
{
	private static $disSingleton = [];
	private        $cs;
	private static $reflection;
	private static $reflectionClassArr;
	public static  $instance;

	public function __construct($key, $class)
	{
		if (get_class($class) != "") {
			$this->cs = $class;
		} else {
			throw new \Exception($key . " is not recycled class");
		}
	}

	public function resolveWhen($key)
	{
		self::$disSingleton[$key] = $this->cs;

		self::$reflectionClassArr[get_class($this->cs)] = $key;
		if (!isset(self::$reflectionClassArr[get_class($this->cs)]))
			class_alias(get_class($this->cs), $key);
	}

	public static function getSingleton($key)
	{
		if (isset(self::$disSingleton[$key])) {
			return self::$disSingleton[$key];
		}

		return false;
	}

	public static function getKeys()
	{
		$arr = [];

		foreach (self::$disSingleton as $key => $value) {
			$arr[] = $key;
		}

		return $arr;
	}

	public static function getAll()
	{
		return self::$disSingleton;
	}

	public static function getCount()
	{
		return count(self::$disSingleton);
	}

	private static function controllerConstructorParamerters($controller)
	{
		$paramerter = [];

		self::$reflection = new \ReflectionClass($controller);

		if (self::$reflection->getConstructor() != false) {
			$params = self::$reflection->getConstructor()->getParameters();

			foreach ($params as $value) {
				if (isset($value->getClass()->name)) {
					$class        = self::$disSingleton[self::$reflectionClassArr[$value->getClass()->name]];
					$paramerter[] = $class;
				} else {
					throw new \Exception($value . " class not found");
				}
			}

			return $paramerter;
		}

		return false;
	}

	private static function controllerMethodParamerters($controller, $action)
	{
		$paramerter = [];

		self::$reflection = new \ReflectionClass($controller);

		if (self::$reflection->getMethod($action) != false) {
			$params = self::$reflection->getMethod($action)->getParameters();
			foreach ($params as $value) {
				if (isset($value->getClass()->name)) {
					$paramerter[] = self::$disSingleton[self::$reflectionClassArr[$value->getClass()->name]];
				} else {
					throw new \Exception($value . " class not found");
				}
			}

			return $paramerter;
		}

		return false;
	}

	public static function make($controller, $action)
	{
		$constructorParamerter = self::controllerConstructorParamerters($controller);

		if ($constructorParamerter != false && count($constructorParamerter) > 0) {
			self::$instance = self::$reflection->newInstanceArgs($constructorParamerter);
		} else {
			self::$instance = self::$reflection->newInstanceArgs();
		}

		$actionParamerter = self::controllerMethodParamerters($controller, $action);

		$refMethod = new \ReflectionMethod($controller, $action);

		if ($actionParamerter == false) {
			$refMethod->invokeArgs(self::$instance, []);
		} else {
			$refMethod->invokeArgs(self::$instance, $actionParamerter);
		}
	}
}