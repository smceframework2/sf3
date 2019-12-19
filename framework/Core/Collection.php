<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */

namespace EF2\Core;


use Illuminate\Support\Collection as LCollection;

class Collection extends LCollection
{

	public function __construct($items = [])
	{
		parent::__construct($items);
	}
}