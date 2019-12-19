<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */

namespace EF2\Http;

use EF2\Http\Request\Get;
use EF2\Http\Request\Post;
use EF2\Http\Request\Filter;
use EF2\Http\Request\Header;
use EF2\Components\Validation;

class Input
{
	public  $get;
	public  $post;
	public  $header;
	private $langDirectory = "";

	public static function Get($var, Request $request){
		return $request->Get($var);
	}

	public static function Post($var, Request $request){
		return $request->Post($var);
	}

	public static function File($var, Request $request){
		return $request->File($var);
	}
}