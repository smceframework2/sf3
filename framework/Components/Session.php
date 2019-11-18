<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework 2
 */

namespace SF3\Components;

class Session
{
	private $security;
	private $iv;
	private $handler;

	function __construct($sessionhandler = null)
	{
		if ($sessionhandler !== null) {
			$this->handler = $sessionhandler;
			new $this->handler();
		}
	}

	public function GetSessionId()
	{
		return session_id();
	}

	public function SetSessionId($id)
	{
		session_abort();
		session_id($id);
		$this->register();

		return $this;
	}

	public function setSecurity($security)
	{
		$this->security = md5(sha1($security));
	}

	public function register()
	{
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}

		return $this;
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return bool
	 */
	public function set($key, $value)
	{
		$_SESSION[$this->security . md5(sha1($key))] = $this->encrypt(serialize($value));

		if ($_SESSION[$this->security . md5(sha1($key))]) {
			return true;
		}

		return false;
	}

	/**
	 * @param $key
	 *
	 * @return session or false
	 */
	public function get($key)
	{
		if (isset($_SESSION[$this->security . md5(sha1($key))])) {
			return unserialize(trim($this->decrypt($_SESSION[$this->security . md5(sha1($key))])));
		}

		return false;
	}

	public function reset()
	{
		foreach ($_SESSION as $key => $value) {
			$key = str_replace($this->security, "", $key);
			unset($_SESSION[$this->security . $key]);
		}
	}

	public function remove($key)
	{
		if (isset($_SESSION[$this->security . md5(sha1($key))])) {
			unset($_SESSION[$this->security . md5(sha1($key))]);

			return true;
		}

		return false;
	}

	public function isSessionStart()
	{
		if (session_status() == PHP_SESSION_NONE) {
			return false;
		}

		return true;
	}

	public function destroy()
	{
		session_destroy();
	}

	private function iv()
	{
		return substr(hash('sha256', $this->security), 0, 16);
	}

	private function encrypt($str)
	{
		return base64_encode(openssl_encrypt($str, "AES-256-CBC", $this->security, 0, $this->iv()));
	}

	private function decrypt($str)
	{
		return openssl_decrypt(base64_decode($str), "AES-256-CBC", $this->security, 0, $this->iv());
	}
}