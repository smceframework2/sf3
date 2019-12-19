<?php

namespace EF2\Http\Request;

use EF2\Ef;
use EF2\Http\Security;

class File
{
	private $security;

	public function __construct()
	{
		$this->security = new Security;
	}

	public function __get($name)
	{
		$post = $this->file();

		if (isset($post[$name])) {
			return $post[$name];
		}

		return null;
	}

	public function all()
	{
		$files  = $this->file();
		$filter = [];
		foreach ($files as $key => $value) {
			$filter[$key] = $value;
		}

		return $filter;
	}

	private $mimes = ['image/png', 'image/jpeg', 'application/pdf'];

	public function SetMimeTypes(...$arg)
	{
		$this->mimes = [];
		if (count($arg) > 0) {
			foreach ($arg as $a) {
				$this->mimes[] = $a;
			}
		}

		return $this;
	}

	/**
	 * only local file upload. this function not usable for push CDNs
	 * @param $input
	 * @param $to
	 * @param $filename
	 */
	public function MoveUploadedFile($input, $to = '', $filename = null)
	{
		if ($filename === null) {
			$filename = $this->$input["name"];
		}
		if (count($this->mimes))
			if (!in_array($this->$input['type'], $this->mimes))
				return false;
			$target = $to . ($filename ?? '/' . $filename);
		if (file_exists($target)) {
			$newName                      = explode('.', $filename);
			$newName[count($newName) - 2] .= '1';
			$filename                     = implode('.', $newName);
			$target                       = $to . ($filename ?? '/' . $filename);
		}
		if (!file_exists(dirname($target))) mkdir(dirname($target),0777,true);
		$result = @move_uploaded_file($this->$input['tmp_name'], $target);
		if ($result) {
			return $to . ($filename ?? '/' . $filename);
		} else return false;
	}

	private function file()
	{
		if ($_FILES) {
			return $_FILES;
		}

		return [];
	}
}