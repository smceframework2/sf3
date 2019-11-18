<?php

namespace SF3\Template;

use SF3\Core\Router\Exception;
use SF3\Template\Blade\FileViewFinder;
use SF3\Template\Blade\Factory;
use SF3\Template\Blade\Compilers\BladeCompiler;
use SF3\Template\Blade\Engines\CompilerEngine;
use SF3\Template\Blade\Filesystem;
use SF3\Template\Blade\Engines\EngineResolver;
use SF3\Http\HttpException;

class Blade implements TemplateEngine
{
	private $view_path = [];
	private $cache_path;
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

		require_once SF3_PATH . "/Template/Blade/helpers.php";

		$file     = new Filesystem;
		$compiler = new BladeCompiler($file, $this->cache_path);

		// you can add a custom directive if you want
		$compiler->directive('datetime', function ($timestamp) {
			return preg_replace('/(\(\d+\))/', '<?php echo date("Y-m-d H:i:s", $1); ?>', $timestamp);
		});

		$resolver = new EngineResolver;
		$resolver->register('blade', function () use ($compiler) {
			return new CompilerEngine($compiler);
		});

		// get an instance of factory
		$this->factory = new Factory($resolver, new FileViewFinder($file, $this->view_path));

		// if your view file extension is not php or blade.php, use this to add it
		$this->factory->addExtension('tpl', 'blade');
	}


	public function render($view, $allparams=array()){
		echo $this->getFactory()->make($view, $allparams)->render();
	}
	/**
	 * @return mixed
	 */
	public function getFactory()
	{
		return $this->factory;
	}
}