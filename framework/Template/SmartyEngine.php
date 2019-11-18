<?php

namespace SF3\Template;

use SF3\App;
use SF3\Core\Router\Exception;
use SF3\Sf;

class SmartyEngine implements TemplateEngine
{
	private $view_path    = [];
	private $cache_path;
	private $debug        = false;
	private $showwarnings = false;
	/**
	 * @var \Smarty
	 */
	private $factory;

	public function assign($var, $value)
	{
		// TODO: Implement assign() method.
	}

	public function setViewPath($dirarr)
	{
		$this->view_path = $dirarr;
	}

	public function setCachePath($dir)
	{
		$this->cache_path = $dir;
	}

	public function getViewPath()
	{
		return $this->view_path;
	}

	public function getCachePath()
	{
		return $this->cache_path;
	}

	public function register()
	{
		if (!file_exists($this->cache_path)) {
			throw new Exception("cache directory not found");
		}

		if (!is_array($this->view_path)) {
			throw new Exception("view directory not found ");
		}

		if (count($this->view_path) == 0) {
			throw new Exception("view directory not found ");
		}

		foreach ($this->view_path as $key => $value) {
			if (!file_exists($value)) {
				throw new Exception("view directory not found " . $value);
			}
		}

		$this->factory            = new \Smarty();
		$this->factory->debugging = $this->debug;
		$this->factory->assign('sitedir', Sf::app()->currenturl);
		$this->factory->assign('themedir', Sf::app()->currenturl . '/assets');
		$this->factory->setCompileDir($this->getCachePath());
		$this->factory->setCacheDir($this->getCachePath());
		$this->factory->caching = false;
		$this->factory->setTemplateDir($this->getViewPath());

		$this->factory->error_reporting = $this->debug;

		$this->factory->error_unassigned = $this->showwarnings;
		$this->factory->force_compile    = false;


		$this->factory->assign("ajax", Sf::app()->isAjax());
		$this->factory->assign("postData", $_POST);
		$this->factory->assign("req", $_GET);
		$this->factory->assign("isPost", Sf::app()->isPost());
		$this->factory->assign("i18n", new class
		{
			public function __get($name)
			{
				return Sf::t($name);
			}
		});

		//		var_dump($this->factory);
		//		die();
	}

	public function render($view, $allparams = [])
	{
		if (is_array($allparams) && count($allparams)) {
			foreach ($allparams as $v => $d) {
				$this->getFactory()->assign($v, $d);
			}
		}
		$view = str_replace('.', '/', $view);
		if (strpos($view, '.tpl') === false) $view = $view . '.tpl';
		$this->getFactory()->assign("page", $view);
		$this->getFactory()->display('index.tpl');

		return '';
	}

	/**
	 * @return mixed
	 */
	public function getFactory(): \Smarty
	{
		return $this->factory;
	}
}