<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */

namespace EF2\Components;

class Locale
{
	const LC_CTYPE    = 0;
	const LC_NUMERIC  = 1;
	const LC_TIME     = 2;
	const LC_COLLATE  = 3;
	const LC_MONETARY = 4;
	const LC_MESSAGES = 5;
	const LC_ALL      = 6;

	/**
	 * @param string $zone
	 */
	static public function SetTimeZone($zone = 'Europe/Istanbul')
	{
		date_default_timezone_set($zone);
	}

	/**
	 * @param int $type
	 * @param string $charset
	 * @param string $c
	 * @param string $symbol
	 * @param string $lang
	 */
	static public function SetLocale($type, $charset = 'tr_TR.UTF-8', $c = 'tr_TR', $symbol = 'tr', $lang = 'turkish')
	{
		setlocale($type, $charset, $c, $symbol, $lang);
	}
}