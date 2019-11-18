<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework 2
 */

namespace SF3\Components;

use SF3\App;
use SF3\Ef;
use SF3\Extensions\Gump;
use SF3\Http\Request;

class Validation
{
	private $gump;
	private $validated_data;
	private $data;
	private $rules;
	private $fields   = [];
	private $replaces = [];
	/**
	 * @var Request $request
	 */
	private $request;

	public function __construct($lang = "tr", $langDirectory = "", Request $request = null)
	{
		$this->gump = new Gump($lang, $langDirectory);
		if ($request) $this->request = $request;
	}

	public function addValidor($key, $func)
	{
		GUMP::add_validator($key, $func);
	}

	public function isValid($dt, $fields)
	{

		foreach ($dt as $key => $value) {
			if (isset($fields[$value[0]])) {
				$this->data[$value[0]] = $fields[$value[0]];
			} else {
				$this->data[$value[0]] = "";
			}

			$this->rules[$value[0]] = $value[2];
			$this->fields[$key]     = '{{' . $value[0] . '}}';
			$this->replaces[$key]   = $value[1];
		}

		$data = $this->gump->sanitize($this->data);

		$this->gump->validation_rules($this->rules);

		$this->validated_data = $this->gump->run($data);

		if ($this->validated_data === false) {
			return false;
		} else {
			return true;
		}
	}

	public function getErrors()
	{
		return $this->gump->errors();
	}

	public function hasErrors()
	{
		return count($this->gump->errors()) > 0;
	}


	public function getReadableErrors($field = false)
	{

		if($field)
			return $this->getReadableErrorsByField();

		$arr=$this->getReadableErrorsByField();

		$errors=[];

		foreach ($arr as $key=>$value)
		{
			$errors[]=$value;
		}

		return $errors;
	}


	public function getReadableErrorsByField()
	{
		$arr    = $this->gump->get_readable_errors();

		$errors = $arr;
		foreach ($arr as $key => $value) {
			foreach ($this->fields as $key2 => $value2) {
				if (strpos($value, $value2) !== false) {
					$errorField = $key;
					@preg_match('/\{\{(.*?)\}\}/s', $value2, $keys);
					if (isset($keys) && count($keys) > 1)
						list(, $errorField) = $keys;
					$errors[$errorField] = str_replace($value2, $this->replaces[$key2], $value);
				}
			}
		}

		return $errors;
	}

	public function filter($data = [])
	{
		if (is_array($_GET) && count($_GET) > 0)
			$_GET = $this->gump->filter($_GET, $data);
		if (Ef::app()->GetMethod() != 'GET') {
			$_POST = $this->gump->filter($_POST, $data);
		}
		if ($this->request) {
			$this->request->get->all();
			$this->request->post->all();
		}
	}
}