<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */

namespace EF2\Http;

use EF2\Core\Str;
use EF2\Http\Request\File;
use EF2\Http\Request\Get;
use EF2\Http\Request\Post;
use EF2\Http\Request\Filter;
use EF2\Http\Request\Header;
use EF2\Components\Validation;

class Request
{
	public  $get;
	public  $post;
	public  $file;
	public  $header;
	private $langDirectory = "";

	public function __construct()
	{
		$this->get    = new Get();
		$this->post   = new Post();
		$this->file   = new File();
		$this->header = new Header;
	}

	public function getMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	public function isMethod($method = 'POST')
	{
		return @$_SERVER['REQUEST_METHOD'] && $_SERVER['REQUEST_METHOD'] == $method;
	}

	public function Get($params)
	{
		return $this->get->$params;
	}

	public function Post($params)
	{
		return $this->post->$params;
	}

	public function File($params)
	{
		return $this->file->$params;
	}

	public function Input($arr)
	{
		$method = strtolower($this->getMethod() == 'GET' ? 'GET' : 'POST');

		return $this->$method->$arr;
	}

	public function validate($arr = [], $lang = "tr")
	{
		$validateArr = [];

		foreach ($arr as $key => $value) {
			$val   = "";
			$field = (string) $value[0];
			$get   = $this->get->$field;
			$post  = $this->post->$field;
			if (!empty($post) || $post === 0 || $post === 0.0 || $post === '0')
				$val = $post;
			elseif (!empty($get) || $get === 0 || $get === 0.0 || $get === '0')
				$val = $get;

			$validateArr[$key] = [$value[0], $value[1], $value[2]];
			$fields[$value[0]] = $val;
		}
		$validation = new Validation($lang, $this->langDirectory, $this);
		$validation->isValid($validateArr, $fields);

		return $validation;
	}

	public function setValidationDirectory($langDirectory)
	{
		$this->langDirectory = $langDirectory;
	}

	/**
	 * @param $ar
	 * @param string $method
	 * @param bool $merge
	 */
	public function Load($ar, $method = 'POST', $merge = true)
	{
		if (Str::upper($method) === 'GET') {
			if ($merge)
				$_GET = array_merge($_GET, $ar);
			else $_GET = $ar;
		} else {
			if ($merge)
				$_POST = array_merge($_POST, $ar);
			else $_POST = $ar;
		}
	}

	public function arrayScan($arr)
	{
		$security = new Security;
		$nArr     = [];
		foreach ($arr as $key => $value) {
			if (!is_array($value)) {
				$nArr[$key] = $security->scan($value);
			} else {
				$recurArray[$key] = $value;
			}
		}

		return $arr;
	}
}