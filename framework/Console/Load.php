<?php

namespace SF3\Console;

use SF3\Console\Colors;

class Load
{
	private $commands;

	private $commandArr=[];


	public function __construct($commands)
	{
		ini_set('memory_limit', '-1');
		$this->commands=$commands;

		$this->control();

	}

	private function control()
	{
		foreach ($this->commands as $value)
		{

			if (!class_exists($value) ||  !is_subclass_of($value, 'SF3\Console\Command')) {

				echo "\n".Colors::string($value." not console command","red")."\n\n";
				exit;

			}else{



				$class=new $value;

				if(!isset($class->method))
				{
					echo "\n".Colors::string($value." method is not set","red")."\n\n";
					exit;
				}

				if(!isset($class->signature))
				{
					echo "\n".Colors::string($value." signature is not set","red")."\n\n";
					exit;
				}

				if(!isset($class->description))
				{
					echo "\n".Colors::string($value." signature is not set","red")."\n\n";
					exit;
				}

				if (!method_exists($value, "handle")) {

					echo "\n".Colors::string($value." handle() no method","red")."\n\n";
					exit;

				}

				$this->commandArr[]=array(
					"signature"=>$class->signature,
					"method"=>$class->method,
					"description"=>$class->description,
					"class"=>$value,
				);

			}
		}
	}


	public function getCommandsarr()
	{
		return $this->commandArr;
	}

	public function is($argv)
	{
		$arr= $this->commandArr;
		foreach ($arr as $value)
		{


			if((isset($argv[1]) && $argv[1] == $value["method"]))
			{


				$class=new $value["class"];

				ob_end_clean();
				ini_set('memory_limit', '-1');
				if(isset($argv[2]))
				{
					$params=$this->getParams($argv);

					$class->handle($params);
					exit;
				}else{

					$class->handle([]);
					exit;

				}



			}

		}


	}


	private function getParams($argv,$i=2,&$arr=[])
	{

		if(isset($argv[$i]))
		{
			$str=trim($argv[$i]);

			if(substr($str,0,2)=="--")
			{
				$ex=explode("=",substr($str,2,strlen($str)));
				if(isset($ex[1]))
				{
					$arr[$ex[0]]=$ex[1];
				}else{
					$arr[$ex[0]]=true;
				}

			}

			$i++;
			$this->getParams($argv,$i,$arr);
		}
		return $arr;

	}


}