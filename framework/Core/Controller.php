<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */

namespace EF2\Core;

class Controller
{
	/**
	 * @var Template
	 */
	private $template;
	private $vars = [];

	private function loadTemplate()
	{
		$this->template = DI::resolve("template");
	}

	public function Share($var, $value)
	{
		$this->vars = array_merge($this->vars, [$var => $value]);
	}

	public function render($view, $params = [])
	{
		if (DI::has("template")) {
			$this->loadTemplate();
			$allparams = array_merge($this->vars, $params);
			echo $this->template->render($view, $allparams);
		} else {
			throw new HttpException(404, "not bind DI 'template'");
		}
	}
}