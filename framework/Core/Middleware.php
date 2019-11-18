<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */
namespace SF3\Core;

use SF3\Sf;

class Middleware
{
	public function redirect($str, $term = 0)
	{

		Sf::app()->redirect($str,$term);
	}
}