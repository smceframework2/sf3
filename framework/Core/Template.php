<?php

namespace EF2\Core;

use EF2\Core\Router\Exception;
use EF2\Template\blade\FileViewFinder;
use EF2\Template\blade\Factory;
use EF2\Template\blade\Compilers\BladeCompiler;
use EF2\Template\blade\Engines\CompilerEngine;
use EF2\Template\blade\Filesystem;
use EF2\Template\blade\Engines\EngineResolver;
use EF2\Http\HttpException;
use EF2\Template\templateengine;

class Template
{
	const BladeEngine="Blade";

	const SmartyEngine="SmartyEngine";

	/**
	 * @var templateengine
	 */
	private $engine = null;

	function __construct($template="Blade")
	{
		$className = 'EF2\Template\\' . $template;
		$this->engine = new $className();
	}

	public function setViewPath($dirarr)
	{
		$this->engine->setViewPath($dirarr);
	}

	public function setCachePath($dir)
	{
		$this->engine->setCachePath($dir);
	}

	public function getViewPath()
	{
		return $this->engine->getViewPath();
	}

	public function getCachePath()
	{
		return $this->engine->getCachePath();
	}

	public function register()
	{
		$this->engine->register();
	}

	/**
	 * @return mixed
	 */
	public function getFactory()
	{
		return $this->engine->getFactory();
	}

	public function render($view, $params=[])
	{
		return $this->engine->render($view, $params);
	}

	public function view($view, $params=[])
	{
		ob_start();
		$this->engine->render($view, $params);
		$render = ob_get_contents();
		ob_end_clean();

		return $render;
	}
}