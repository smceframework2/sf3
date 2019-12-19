<?php

if (!function_exists('env')) {
	/**
	 * Get Variable on (.env) ENVIROMENT file.
	 *
	 * @param  string $varname
	 * @param  mixed $default
	 * @return mixed
	 */
	function env($varname = null, $default = null)
	{
		$var = getenv($varname);

		return $var === null ? $default : $var;
	}
}

if (!function_exists('asset')) {
	/**
	 * Get asset url starting with baseUrl
	 *
	 * @param   string $file
	 * @return  string
	 */
	function asset($file)
	{
		return EF2\Ef::app()->baseUrl . '/' . $file;
	}
}

if (!function_exists('redirect')) {
	/**
	 * Redirect class
	 *
	 * @param   string $url
	 * @param   int $delay
	 * @return  \EF2\Http\Redirect
	 */
	function redirect($url = null, $delay = -1)
	{
		return new \EF2\Http\Redirect($url, $delay);
	}
}

if (!function_exists('url')) {
	/**
	 * Generate link url with baseUrl
	 *
	 * @param $url
	 * @return string
	 */
	function url($url)
	{
		return EF2\Ef::app()->baseUrl . '/' . $url;
	}
}

if (!function_exists('Collect')) {
	/**
	 * Collect an array
	 *
	 * @param $array
	 * @return Illuminate\Support\Collection
	 */
	function Collect($array)
	{
		return new \EF2\Core\Collection($array);
	}
}

if (!function_exists('pre')) {
	/**
	 * var_dump with pre tags
	 *
	 * @param $mixed
	 * @param $die use die()
	 * @return null
	 */
	function pre($mixed, $die = true)
	{
		echo '<pre>';
		var_dump($mixed);
		echo '</pre>';
		if ($die) die();
	}
}

if (!function_exists('preJSON')) {
	/**
	 * die json_encode with pre tags
	 *
	 * @param mixed $mixed
	 * @param boolean $die calls die()
	 * @return null
	 */
	function preJSON($mixed, $die = true)
	{
		header('content-type: application/json; charset=utf-8');
		echo json_encode($mixed, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
		if ($die) die();
	}
}