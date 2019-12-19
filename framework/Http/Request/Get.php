<?php

namespace EF2\Http\Request;

use EF2\Http\Security;

class Get
{
	private $security;

	public function __construct()
	{
		$this->security = new Security;
	}

	public function __get($name)
	{
		if (isset($_GET[$name]))
			return trim($this->security->scan($_GET[$name]));

		return "";
	}

	public function all()
	{
		$filter = [];
		foreach ($_GET as $key => $value) {
			$filter[$key] = trim($this->security->scan($value));
		}

		return $filter;
	}
}