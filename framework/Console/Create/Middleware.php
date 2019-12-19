<?php

namespace EF2\Console\Create;

use EF2\Console\Colors;

class Middleware
{
	public static function is($dir, $argv)
	{
		if (isset($argv[2]) && $argv[2] == "middleware") {
			if (!isset($argv[3])) {
				echo Colors::string("\tnot middleware name\n\n", "red");
				exit;
			}

			self::create($dir, $argv[3]);

			return true;
		}

		return false;
	}

	private static function create($dir, $name)
	{
		$str = file_get_contents(dirname(__FILE__) . "/struct/middleware");
		$name = ucfirst(strtolower($name));
		$name="M_".$name;
		$str = str_replace(["[name]"], [$name], $str);

		$filename = $dir . $name . ".php";

		if (file_exists($filename)) {
			echo Colors::string("middleware already exists " . $filename . "\n\n", "red");
			exit;
		}
		@mkdir(dirname($filename));
		if (!file_exists($filename) && file_put_contents($filename, $str)) {
			echo Colors::string("model was created " . $filename . "\n\n", "green");
		}
	}
}