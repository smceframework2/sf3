<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */

namespace SF3\Http;

use SF3\Http\Request\Get;
use SF3\Http\Request\Post;
use SF3\Http\Request\Filter;
use SF3\Http\Request\Header;
use SF3\Components\Validation;

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