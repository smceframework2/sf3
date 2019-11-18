<?php

namespace SF3\Template;

interface TemplateEngine
{
	public function assign($var, $value);

	public function setViewPath($dirarr);

	public function setCachePath($dir);

	public function getViewPath();

	public function getCachePath();

	public function register();

	public function render($view, $allparams);

	/**
	 * @return mixed
	 */
	public function getFactory();
}