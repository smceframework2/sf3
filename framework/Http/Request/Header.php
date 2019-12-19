<?php

namespace EF2\Http\Request;

use EF2\Http\Security;

class Header
{
	private          $headers      = [];
	private          $security;
	protected static $CONTENT_JSON = 'application/json';
	protected static $CONTENT_HTML = 'text/html';
	protected static $CONTENT_XML  = 'text/xml';
	protected static $CONTENT_TEXT = 'text/plain';

	public function __construct()
	{
		$this->security = new Security;

		if (!function_exists('getallheaders')) {
			foreach ($_SERVER as $name => $value) {
				/* RFC2616 (HTTP/1.1) defines header fields as case-insensitive entities. */
				if (strtolower(substr($name, 0, 5)) == 'http_') {
					$this->headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
				}
			}
		} else {

		    $header=getallheaders();
            $header=array_merge($header,array_change_key_case(getallheaders(),CASE_LOWER));
			return $this->headers = $header;
		}
	}

	public function __get($name)
	{
		if (isset($this->headers[$name]))
			return trim($this->security->scan($this->headers[$name]));

		return "";
	}

	public function get($name)
	{
		if (isset($this->headers[$name]))
			return trim($this->headers[$name]);

		return "";
	}

	public function set()
	{
		if (func_num_args() == 2) {
			list($name, $value) = func_get_args();
			header($name . ': ' . $value);
		} elseif (is_array(func_get_arg(0))) {
			foreach (func_get_arg(0) as $val) {
				list($name, $value) = explode(':', $val);
				header($name . ': ' . $value);
			}
		}
	}

	public function EnableCors()
	{
		$this->set("Access-Control-Allow-Origin", "*");
		$this->set("Access-Control-Allow-Methods", "GET, POST, PATCH, PUT, DELETE, OPTIONS");
		$this->set("Access-Control-Allow-Headers", "AccountKey, x-requested-with, Content-Type, origin, authorization, accept, client-security-token, host, date, cookie, cookie2");

		return $this;
	}

	public function SetContentType($type = 'text/html; charset=utf-8', $charset = '')
	{
		$this->set('Content-Type', $type . ($charset != '' ? '; charset=' . $charset : ''));

		return $this;
	}

	public function all()
	{
		$arr = [];

		foreach ($this->headers as $key => $value) {
			$arr[$key] = trim($this->security->scan($value));
		}

		return $arr;
	}

	function getAuthorizationHeader()
	{
		$headers = null;
		if (isset($_SERVER['Authorization'])) {
			$headers = trim($_SERVER["Authorization"]);
		} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
			$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		} elseif (function_exists('apache_request_headers')) {
			$requestHeaders = apache_request_headers();
			// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
			//print_r($requestHeaders);
			if (isset($requestHeaders['Authorization'])) {
				$headers = trim($requestHeaders['Authorization']);
			}
		}

		return $headers;
	}

	/**
	 * get access token from header
	 * */
	function getBearerToken()
	{
		$headers = $this->getAuthorizationHeader();
		// HEADER: Get the access token from the header
		if (!empty($headers)) {
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				return $matches[1];
			}
		}

		return null;
	}
}