<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework 2
 */

namespace SF3;



class Autoload
{

	private $isRedis=false;
	private $cachetime;
	private $redis;
	private $redisKey="Autoloader:";

	private static $appDirs = [];

	private function autoload($className)
	{

		if(!class_exists($className))
		{

			if ($this->isFramework($className)) {


				$this->autoloadFramework($className);
			} else {
				$this->autoloadApp($className);
			}
		}

	}


	private function autoloadFramework($className)
	{


		$className = ltrim($className, '\\');
		$parts = explode("\\", $className);

		$fileName = '';
		$namespace = '';


		if ($lastNsPos = strrpos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
			$fileName = SF3_PATH . str_replace($parts[0], "", $fileName);

		}
		$fileName .= $className . ".php";



		$this->requeireFile($fileName);



	}

	private function requeireFile($fileName)
	{

		if ($this->isRedis==true)
		{
			if($file=$this->get($fileName))
			{

				$content = eval("?>$file");
				echo $content;

				return true;
			}else{


				if(file_exists($fileName))
				{
					require $fileName;
					$this->set($fileName,file_get_contents($fileName),$this->cachetime);
					return true;

				}

			}
		}else{


			if(file_exists($fileName))
			{
				require $fileName;
				return true;
			}

		}

		return false;
	}


	private function isFramework($className)
	{
		$ex = explode("\\", $className);

		if (strtolower($ex[0]) == "sf3") {

			return true;
		}

		return false;
	}

	private function autoloadApp($className)
	{
		if (count(self::$appDirs) > 0) {
			$className = ltrim($className, '\\');

			$fileName = '';
			$namespace = '';

			if (strrpos($className, '\\')) {
				$lastNsPos = strrpos($className, '\\');
				$namespace = substr($className, 0, $lastNsPos);
				$className = substr($className, $lastNsPos + 1);
				$fileName = str_replace("\\", DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
			}


			$fileName .= $className . ".php";
			foreach (self::$appDirs as $value) {
				$len = strlen($value);
				if (substr($value, $len - 1, $len) != "/") {
					$value = $value . DIRECTORY_SEPARATOR;
				}
				$file=$value . $fileName;
				if ($this->requeireFile($file)) {

					break;
				}
			}
		}

	}


	/**
	 * autoload register
	 *
	 * @param $config
	 */

	public function register($redis,$cachetime)
	{
		$this->redis($redis,$cachetime);
		spl_autoload_register(array(__CLASS__, 'autoload'), true, true);
	}



	/**
	 * autoload register
	 *
	 * @param $config
	 */

	public static function registerApp($appDirs)
	{
		self::$appDirs = $appDirs;
	}

	/**
	 * @param $redis
	 * cache için redis varmı
	 */
	private function redis($redis,$cachetime)
	{

		if($redis!=null)
		{
			$this->isRedis=true;
			$this->redis=$redis;
			$this->cachetime=$cachetime;
		}
	}


	/**
	 * @param string
	 *
	 * @return mixed
	 */
	private function get($key)
	{

		return unserialize($this->redis->get($this->redisKey.$key));

	}

	/**
	 * @param string
	 * @param mixed
	 * @param int
	 *
	 * @return set
	 */
	private function set($key, $value, $duration)
	{

		return $this->redis->set($this->redisKey.$key, serialize($value), $duration);
	}

}