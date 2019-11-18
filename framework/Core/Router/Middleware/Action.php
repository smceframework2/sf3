<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework 2
 */

namespace SF3\Core\Router\Middleware;

use SF3\Core\Router\Exception;
use SF3\Http\HttpException;

class Action
{
	private static $routeClass;
	private static $pattern;
	private static $index;

	public function __construct($pattern, $routeClass, $index)
	{
		self::$routeClass = $routeClass;
		self::$pattern    = $pattern;
		self::$index      = $index;
	}

	public function middleware($classNames)
	{
		if (!is_array($classNames)) {
			echo self::$pattern . " middleware array contains data";
			exit;
		}

		$co = self::$routeClass::middlewareControl($classNames);

		if (!$co["control"]) {
			echo "middleware " . $co["class"] . " class not found";
			exit;
		}

		self::$routeClass::$routes[self::$pattern.self::$index]["middleware"] = array_merge(self::$routeClass::$routes[self::$pattern.self::$index]["middleware"], $classNames);
	}
}