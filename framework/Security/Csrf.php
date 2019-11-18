<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */

namespace SF3\Security;

use SF3\Http\HttpException;
use SF3\Sf;
use SF3\Security\Csrf\Bridge;
use SF3\Security\Csrf\CsrfInterface;

/**
 * A simple CSRF class to protect forms against CSRF attacks. The class uses
 * PHP sessions for storage.
 *
 *
 */
class Csrf implements CsrfInterface
{
	/**
	 * The namespace for the session variable and form inputs
	 * @var string
	 */
	private $namespace;
	private $bridge;
	const Session = 1, Redis = 2;
	/**
	 * exception, json, redirect
	 * Exception:
	 *  exception
	 * callback
	 * @var string
	 */
	private $returntype;

	/**
	 * Initializes the session variable name, starts the session if not already so,
	 * and initializes the token
	 *
	 * @param string $namespace
	 */
	public function __construct($connector, $redis = null, $duration = 43200, $type = 'exception')
	{
		$this->bridge = new Bridge;
		$this->bridge->setAdaptor($connector, $redis);
		$this->bridge->setDuration($duration);
		$this->returntype = $type;
	}

	public function bind($namespace = '_csrf')
	{
		$this->namespace = $namespace;
		$this->setToken();
	}

	/**
	 * Return the token from persistent storage
	 *
	 * @return string
	 */
	public function getToken()
	{
		return $this->readTokenFromStorage();
	}

	public function getName()
	{
		return $this->namespace;
	}

	/**
	 * Verify if supplied token matches the stored token
	 *
	 * @param string $userToken
	 * @return boolean
	 */
	public function isTokenValid($token)
	{
		return ($token === $this->readTokenFromStorage());
	}

	/**
	 * Echoes the HTML input field with the token, and namespace as the
	 * name of the field
	 */
	public function echoInputField()
	{
		$token = $this->getToken();
		echo "<input type=\"hidden\" name=\"{$this->namespace}\" id=\"{$this->namespace}\"  value=\"{$token}\" />";
	}

	/**
	 * Verifies whether the post token was set, else dies with error
	 */
	public function verifyRequest($token = "")
	{
		if (!$this->isTokenValid($token)) {
			if ($this->returntype === 'exception') {
				throw new HttpException(419, "CSRF validation failed.");
			} else {
				new HttpException(419, "CSRF validation failed.");
				$this->returntype(419, "CSRF validation failed.");
			}
		}
	}

	/**
	 * Generates a new token value and stores it in persisent storage, or else
	 * does nothing if one already exists in persisent storage
	 */
	private function setToken()
	{
		$token       = md5(uniqid(rand(), true));
		$storedToken = $this->readTokenFromStorage();

		if ($storedToken == false) {
			$token = md5(uniqid(rand(), true));
			$this->writeTokenToStorage($token);
		}
	}

	/**
	 * Reads token from persistent sotrage
	 * @return string
	 */
	private function readTokenFromStorage()
	{
		if (!empty($this->bridge->get('Csrf:' . $this->namespace))) {
			return $this->bridge->get('Csrf:' . $this->namespace);
		}

		return false;
	}

	/**
	 * Writes token to persistent storage
	 */
	private function writeTokenToStorage($token)
	{
		$this->bridge->set('Csrf:' . $this->namespace, $token);
	}

	public function postControl()
	{
		if (Sf::app()->isPost() && !isset($_POST[$this->namespace]) || $this->readTokenFromStorage() == false || (isset($_POST[$this->namespace]) && $_POST[$this->namespace] != $this->readTokenFromStorage())) {
			if ($this->returntype === 'exception') {
				throw new HttpException(419, "CSRF validation failed.");
			} else {
				new HttpException(419, "CSRF validation failed.");

				call_user_func_array($this->returntype, [419, "CSRF validation failed."]);
			}
		}
	}

	public function putControl()
	{

		if (Sf::app()->GetMethod()==='PUT' && !isset($_POST[$this->namespace]) || $this->readTokenFromStorage() == false || (isset($_POST[$this->namespace]) && $_POST[$this->namespace] != $this->readTokenFromStorage())) {
			if ($this->returntype === 'exception') {
				throw new HttpException(419, "CSRF validation failed.");
			} else {
				new HttpException(419, "CSRF validation failed.");
				call_user_func_array($this->returntype, [419, "CSRF validation failed."]);
			}
		}
	}

	public function deleteControl()
	{
		if (Sf::app()->GetMethod()==='DELETE' && !isset($_POST[$this->namespace]) || $this->readTokenFromStorage() == false || (isset($_POST[$this->namespace]) && $_POST[$this->namespace] != $this->readTokenFromStorage())) {
			if ($this->returntype === 'exception') {
				throw new HttpException(419, "CSRF validation failed.");
			} else {
				new HttpException(419, "CSRF validation failed.");

				call_user_func_array($this->returntype, [419, "CSRF validation failed."]);
			}
		}
	}

	public function patchControl()
	{
		if (Sf::app()->GetMethod()==='PATCH' && !isset($_POST[$this->namespace]) || $this->readTokenFromStorage() == false || (isset($_POST[$this->namespace]) && $_POST[$this->namespace] != $this->readTokenFromStorage())) {
			if ($this->returntype === 'exception') {
				throw new HttpException(419, "CSRF validation failed.");
			} else {
				new HttpException(419, "CSRF validation failed.");

				call_user_func_array($this->returntype, [419, "CSRF validation failed."]);
			}
		}
	}

	public function optionsControl()
	{
		if (Sf::app()->GetMethod()==='OPTIONS' && !isset($_POST[$this->namespace]) || $this->readTokenFromStorage() == false || (isset($_POST[$this->namespace]) && $_POST[$this->namespace] != $this->readTokenFromStorage())) {
			if ($this->returntype === 'exception') {
				throw new HttpException(419, "CSRF validation failed.");
			} else {
				new HttpException(419, "CSRF validation failed.");

				call_user_func_array($this->returntype, [419, "CSRF validation failed."]);
			}
		}
	}

	public function getControl()
	{
		if (Sf::app()->isGet() && !isset($_GET[$this->namespace]) || $this->readTokenFromStorage() == false || (isset($_GET[$this->namespace]) && $_GET[$this->namespace] != $this->readTokenFromStorage())) {
			if ($this->returntype === 'exception') {
				throw new HttpException(419, "CSRF validation failed.");
			} else {
				new HttpException(419, "CSRF validation failed.");
				$this->returntype(419, "CSRF validation failed.");
			}
		}
	}
}