<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */
namespace EF2\Core;

use EF2\Ef;

class Middleware
{
	public function redirect($str, $term = 0)
	{

		Ef::app()->redirect($str,$term);
	}
}