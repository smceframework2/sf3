<?php

namespace EF2\Core;

class Hash
{
	public static function make($text): string
	{
		$salt = sha1($text);
		$salt = base64_encode($salt);
		$salt = str_replace('+', '.', $salt);
		$hash = crypt($text, '$2y$10$' . $salt . '$');

		return $hash;
	}

}