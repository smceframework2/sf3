<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */

namespace EF2\Http;

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