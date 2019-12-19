<?php

namespace EF2\Components;

use Exception;

class I18n
{
	private static $dir;
	private static $lang;
	private static $langArr;

	public function setDir($dir)
	{
		self::$dir = $dir;
	}

	public function setLang($lang)
	{
		self::$lang = $lang;
		$file       = self::$dir . "/" . $lang . ".xml";

		if (file_exists(self::$dir . "/" . $lang . ".xml")) {
			$arr           = simplexml_load_file($file);
			self::$langArr = json_decode(json_encode($arr), true);
		} else {
			throw new Exception("i18n not load language file " . $file);
		}
	}

	private static function is()
	{
		if (empty(self::$lang)) {
			throw new Exception(403, "Set I18n 'setLang()'");
		}
		if (empty(self::$dir)) {
			throw new Exception(403, "Set I18n 'setDir()'");
		}
	}

	public static function t($str, $arr = [])
	{
		$at  = [];
		$at2 = [];
		self::is();
		if (isset(self::$langArr[$str]) && !empty(self::$langArr[$str])) {
			if (count($arr) > 0) {
				$str2 = self::$langArr[$str];

				foreach ($arr as $key => $value) {
					$at[]  = $key;
					$at2[] = $value;
				}

				$str2 = str_replace($at, $at2, self::$langArr[$str]);

				return $str2;
			} else {
				return self::$langArr[$str];
			}
		} else {
			if (count($arr) > 0) {
				$str2 = $str;

				foreach ($arr as $key => $value) {
					$at[]  = $key;
					$at2[] = $value;
				}

				$str2 = str_replace($at, $at2, $str);

				return $str2;
			} else {
				return $str;
			}
		}
	}
}