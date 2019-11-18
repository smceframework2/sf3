<?php
/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */

namespace SF3\Core\Router;

use Exception;
use SF3\Core\Router\Middleware\Action;

class Route
{
	public static  $routes             = [];
	private static $prefix             = "";
	public static  $groupMiddlewareArr = [];

	public static function notFound($params)
	{
		$params = self::getParams($params);

		self::$routes["notfound"] = [
			"params" => $params,
		];
	}

	public static function get($pattern, $params)
	{
		$pattern = self::getPattern($pattern);

		$params     = self::getParams($params);
		$midlewares = self::getMiddlewares();

		self::$routes[$pattern . "_GET"] = [
			"pattern"    => $pattern,
			"params"     => $params,
			"via"        => "GET",
			"middleware" => $midlewares,
		];

		return new Action($pattern, self::class, "_GET");
	}

	public static function any($pattern, $params)
	{
		$pattern    = self::getPattern($pattern);
		$params     = self::getParams($params);
		$midlewares = self::getMiddlewares();

		self::$routes[$pattern . "_ANY"] = [
			"pattern"    => $pattern,
			"params"     => $params,
			"via"        => '*',
			"middleware" => $midlewares,
		];

		return new Action($pattern, self::class, "_ANY");
	}

	public static function post($pattern, $params)
	{
		$pattern    = self::getPattern($pattern);
		$params     = self::getParams($params);
		$midlewares = self::getMiddlewares();

		self::$routes[$pattern . "_POST"] = [
			"pattern"    => $pattern,
			"params"     => $params,
			"via"        => "POST",
			"middleware" => $midlewares,
		];

		return new Action($pattern, self::class, "_POST");
	}

	public static function put($pattern, $params)
	{
		$pattern    = self::getPattern($pattern);
		$params     = self::getParams($params);
		$midlewares = self::getMiddlewares();

		self::$routes[$pattern . "_PUT"] = [
			"pattern"    => $pattern,
			"params"     => $params,
			"via"        => "PUT",
			"middleware" => $midlewares,
		];

		return new Action($pattern, self::class, "_PUT");
	}

	public static function delete($pattern, $params)
	{
		$pattern    = self::getPattern($pattern);
		$params     = self::getParams($params);
		$midlewares = self::getMiddlewares();

		self::$routes[$pattern . "_DELETE"] = [
			"pattern"    => $pattern,
			"params"     => $params,
			"via"        => "DELETE",
			"middleware" => $midlewares,
		];

		return new Action($pattern, self::class, "_DELETE");
	}

	public static function options($pattern, $params)
	{
		$pattern    = self::getPattern($pattern);
		$params     = self::getParams($params);
		$midlewares = self::getMiddlewares();

		self::$routes[$pattern . "_OPTIONS"] = [
			"pattern"    => $pattern,
			"params"     => $params,
			"via"        => "OPTIONS",
			"middleware" => $midlewares,
		];

		return new Action($pattern, self::class, "_OPTIONS");
	}

	public static function patch($pattern, $params)
	{
		$pattern    = self::getPattern($pattern);
		$params     = self::getParams($params);
		$midlewares = self::getMiddlewares();

		self::$routes[$pattern . "_PATCH"] = [
			"pattern"    => $pattern,
			"params"     => $params,
			"via"        => "PATCH",
			"middleware" => $midlewares,
		];

		return new Action($pattern, self::class, "_PATCH");
	}

	public static function group($params, $func)
	{
		if (isset($params["prefix"])) {
			self::$prefix = $params["prefix"];
		}

		if (isset($params["middleware"])) {
			self::$groupMiddlewareArr = $params["middleware"];

			if (!is_array(self::$groupMiddlewareArr)) {
				echo "middleware array contains data";
				exit;
			}

			$co = self::middlewareControl(self::$groupMiddlewareArr);

			if (!$co["control"]) {
				echo "middleware " . $co["class"] . " class not found";
				exit;
			}
		}

		$func();

		self::$prefix = "";
	}

	public static function getRoutes()
	{
		return self::$routes;
	}

	private static function getParams($par)
	{
		$params = [];

		if (is_object($par)) {
				$params["closure"] = $par;
		} else {
			if (!is_array($par)) {
				$ex1 = explode("@", $par);

				$params = [];
				if (count($ex1) > 1) {
					$params["controller"] = $ex1[0];
					$params["action"]     = $ex1[1];
				}
			} else {
				$params = $par;
			}

			if (isset($params["controller"])) {
				new Exception("route not controller");
			} elseif (isset($params["action"])) {
				new Exception("route not action");
			}
		}

		return $params;
	}

	/**
	 * @param $pattern
	 * @return string
	 *
	 * gourp prefix alma
	 */
	private static function getPattern($pattern)
	{
		$pattern = rtrim($pattern, '/');
		if (empty(self::$prefix)) {
			return $pattern;
		} else {
			return "/" . self::$prefix . $pattern;
		}
	}

	public static function getMiddlewares()
	{
		return self::$groupMiddlewareArr;
	}

	/**
	 * @param $classNames
	 * @return array
	 *
	 * middleware Controll
	 */
	public static function middlewareControl($classNames)
	{
		$control = true;
		foreach ($classNames as $key => $value) {
			if (!class_exists($value) || !is_subclass_of($value, 'SF3\Core\Middleware')) {
				return [
					"class"   => $value,
					"control" => false
				];
			}
		}

		return [
			"class"   => "",
			"control" => true
		];
	}
}