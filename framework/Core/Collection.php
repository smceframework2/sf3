<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */

namespace SF3\Core;


use Illuminate\Support\Collection as LCollection;

class Collection extends LCollection
{

	public function __construct($items = [])
	{
		parent::__construct($items);
	}
}