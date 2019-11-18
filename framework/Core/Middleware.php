<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework 2
 */
namespace SF3\Core;

use SF3\Ef;

class Middleware
{
	public function redirect($str, $term = 0)
	{

		Ef::app()->redirect($str,$term);
	}
}