<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework 2
 */

namespace SF3;

use SF3\Components\Locale;
use SF3\Core\Controller;
use SF3\Http\HttpException;
use SF3\Core\DI;
use SF3\Core\DI\DISingleton;
use SF3\Http\Request;
use SF3\Http\Response;
use SF3\Components\PHPImageWorkshop\ImageWorkshop;

define('SF3_PATH', dirname(__FILE__));

class Framework
{
	/**
	 * @var Controller
	 */
	public static $controller;
	/**
	 * @var $action
	 */
	public static $action;
	/**
	 * @var \closure $closure
	 */
	public static $closure;
	private $restApi;

	/**
	 * Framework constructor.
	 * @param bool $restApi
	 */

	function __construct($restApi = false)
	{
		$this->restApi = $restApi;
	}

	public function register($redis = null, $cachetime = null)
	{
		if ($this->restApi)
			$this->checkRestApi();
		ob_start();



		$this->includeFile();
		$this->autoloadRegister($redis, $cachetime);
		$this->setLocales();

		return $this;
	}

	/**
	 * @return void
	 */
	public function make()
	{
		$this->router();
		$this->command();

		if (!ob_get_length()) ob_end_clean();
	}

	private function checkRestApi()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: POST,GET,OPTIONS,PUT,DELETE');
			header('Access-Control-Max-Age: 1000');
			if (array_key_exists('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', $_SERVER)) {
				header('Access-Control-Allow-Headers: ' . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
			} else {
				header('Access-Control-Allow-Headers: *');
			}
			exit;
		}
	}

	private function includeFile()
	{
		require SF3_PATH . "/Core/Helper.php";
		require SF3_PATH . "/Autoload.php";
	}

	private function autoloadRegister($redis, $cachetime)
	{
		$autoload = new Autoload;
		$autoload->register($redis, $cachetime);
	}

	/**
	 * @return HttpException|void
	 */
	private function router()
	{
		$router = DI::resolve("router");

		if ($router != false) {
			self::$closure = DI::resolve("router")->getClosure();

			$excontroller = explode("/", DI::resolve("router")->getControllerName());

			if (count($excontroller) == 1) {
				self::$controller = ucfirst(DI::resolve("router")->getControllerName()) . "Controller";
			} else {
				self::$controller = DI::resolve("router")->getControllerName() . "Controller";
				self::$controller = str_replace("/", "\\", self::$controller);
			}

			self::$action = "action" . ucfirst(DI::resolve("router")->getActionName());
		} else {
			throw new HttpException(404, "not bind DI 'router'");
		}
	}

	public function SetLocales($timezone = 'Europe/Istanbul', ...$params)
	{

		Locale::SetTimeZone($timezone);
		if ($params && count($params)) {
			Locale::SetLocale(LC_ALL, $params);
		}
	}

	/**
	 *
	 * @return HttpException|void
	 */
	private function command()
	{
		if (self::$closure == null && !$this->isController()) {
			throw new HttpException(404, "Controller Not Found");
		}

		$this->controllerAction();
	}

	/**
	 *
	 * @return bool
	 */
	private function isController()
	{
		if (class_exists(self::$controller) && is_subclass_of(self::$controller, 'SF3\Core\Controller')) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 * @return HttpException|void
	 */
	public function controllerAction()
	{
		if (self::$closure != null || method_exists(self::$controller, self::$action)) {
			$this->runAction();
		} else {
			throw new HttpException(404, "Page Not Found");
		}
	}

	/**
	 *
	 * @return void
	 */
	private function runAction()
	{
		if (DI::has("debug")) {
			ob_start();

			if (self::$closure != null) {
				call_user_func(self::$closure);
			} else {
				$this->beforeAction();
				DISingleton::make(self::$controller, self::$action);
			}

			$content = ob_get_contents();
			ob_end_clean();

			if (!DI::resolve("debug")->getIsError() && (is_array(error_get_last()) && count(error_get_last()) == 0) || !is_array(error_get_last())) {
				echo $content;
			}
		} else {
			if (self::$closure != null) {
				call_user_func(self::$closure);
			} else {
				$this->beforeAction();
				DISingleton::make(self::$controller, self::$action);
			}
		}
	}

	private function beforeAction()
	{
		if (DI::has("template")) {
			$template = DI::resolve("template");
			$template->register();
		}

		DI::singleton("request", function () {
			return new Request;
		})->resolveWhen("Request");

		DI::singleton("response", function () {
			return new Response;
		})->resolveWhen("Response");
	}
}