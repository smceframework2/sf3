<?php

namespace SF3\Http\Request;

use SF3\Http\Security;

class Post
{
	private $security;

	public function __construct()
	{
		$this->security = new Security;
		$_POST          = $this->all();
	}

	public function __get($name)
	{
		$post = $this->post();

		if (isset($post[$name])) {
			if (!is_array($post[$name])) {
				return trim($this->security->scan($post[$name]));
			}

			return $post[$name];
		}

		return "";
	}

	public function all()
	{
		$post   = $this->post();
		$filter = [];
		foreach ($post as $key => $value) {
			if (!is_array($value)) {
				$filter[$key] = trim($this->security->scan($value));
			} else {
				$filter[$key] = $value;
			}
		}

		return $filter;
	}

	private function post()
	{
		if ($_POST && is_array($_POST) && count($_POST) > 0) {
			return $_POST;
		}
		$rawData = @file_get_contents('php://input');
		$raw     = @json_decode($rawData, true);
		if (is_array($raw)) return $raw;

		parse_str($rawData, $raw);
		if (is_array($raw)) return $raw;

		return [];
	}
}