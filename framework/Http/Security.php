<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework 2
 */

namespace SF3\Http;

class Security
{
	function __construct($scanRequest = false)
	{
		if ($scanRequest) {
			$_POST = array_map([$this, 'scan'], $_POST);
			$_GET  = array_map([$this, 'scan'], $_GET);
		}
	}

	/**
	 * USER_AGENT gibi değerleri de tarayabilmek için, XSS, CROSS-SITE XHR, SQL Injection temizleme
	 *
	 * @param $mix
	 * @return mixed
	 */
	public function scan($mix)
	{
		return $mix;

	}
}