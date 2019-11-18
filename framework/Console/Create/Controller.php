<?php

namespace SF3\Console\Create;

use SF3\Console\Colors;

class Controller
{
	public static function is($dir, $argv)
	{
		if (isset($argv[2]) && $argv[2] == "controller") {
			if (!isset($argv[3])) {
				echo Colors::string("\tnot controller name\n\n", "red");
				exit;
			}

			self::create($dir, $argv[3]);

			return true;
		}

		return false;
	}

	private static function create($dir, $name)
	{
		$str  = file_get_contents(dirname(__FILE__) . "/struct/controller");
		$name = ucfirst(strtolower($name)) . "Controller";

		$str = str_replace(["[name]"], [$name], $str);

		$filename = $dir . $name . ".php";

		if (file_exists($filename)) {
			echo Colors::string("controller allready exists " . $filename . "\n\n", "red");
			exit;
		}
		@mkdir(dirname($filename));
		if (!file_exists($filename) && file_put_contents($filename, $str)) {
			echo Colors::string("controller was created " . $filename . "\n\n", "green");
		}
	}
}