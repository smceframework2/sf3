<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */

namespace SF3;

use SF3\Components\i18n;

class Sf
{

	private static $app;

    private static $t;

	public static function app()
	{

		if (self::$app === null) {
			self::$app = new App();
		}

		return self::$app;
	}

	public static function t($str,$arr=array())
    {
        return I18n::t($str,$arr);
    }


}