<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */

namespace SF3\Http;

use SF3\App;
use SF3\Components\Session;
use SF3\Core\DI;
use SF3\Http\Request\Header;

class Redirect
{
	private $url;
	/**
	 * @var Session
	 */
	private $session;
	private $header;
	private $delay;
	private $redirecting = false;

	function __construct($url = null, $delay = -1)
	{
		$this->url    = $url;
		$this->delay  = $delay;
		$this->header = new Header();
	}

	function __destruct()
	{
		if (!$this->redirecting)
			$this->goHome($this->url, $this->delay);
	}

	public function to($url = null, $delay = -1)
	{
		return $this->goHome($url, $delay);
	}

	private function goHome($url = null, $delay = -1)
	{
		$this->redirecting = true;
		$url               = ltrim($url, '/');
		if ($url !== null) {
			if ($delay > 0) {
				$this->header->set('refresh', $delay . ';url=' . App::baseUrl() . '/' . $url);
			} else {
				$this->header->set('location', App::baseUrl() . '/' . $url);
			}
			exit();
		}

		return $this;
	}

	private function goOut($url = null, $delay = -1)
	{
		$this->redirecting = true;
		$url               = ltrim($url, '/');
		if ($url !== null) {
			if ($delay > 0) {
				$this->header->set('refresh', $delay . ';url=' .  $url);
			} else {
				$this->header->set('location', $url);
			}
			exit();
		}

		return $this;
	}

	public function back()
	{
		if (isset($_SERVER['HTTP_REFERER'])) return $this->goOut($_SERVER['HTTP_REFERER']);
	}

	public function away($url, $delay)
	{
		if (strlen($this->url) > 0) {
			if ($delay > 0) {
				$this->header->set('refresh', $delay . ';url=' . $url);
			} else {
				$this->header->set('location', $url);
			}
			exit();
		}
	}

	public function with(...$arg)
	{
		$this->session = DI::resolve('session');
		if (count($arg) == 1) {
			if (isset($arg[0]) && is_array($arg[0]) && count($arg[0]) > 0) {
				Collect($arg[0])->map(function ($v, $k) {
					$this->session->set($k, $v);
				});
			}
			$this->goHome($this->url, $this->delay);
		} elseif (count($arg) == 2) {
			$this->session->set($arg[0], $arg[1]);
			$this->goHome($this->url, $this->delay);
		}
	}
}