<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */

namespace EF2;

use EF2\Http\HttpException;

class Debug
{
	const
		DEVELOPMENT = true,
		PRODUCTION = false;
	private $func;
	private $isError   = false;
	private $debugmode;
	private $errorlist = [];
	private $errorHideFilter=[];
    private $fireStatus=false;

	public function register($const = true)
	{
		$this->debugmode = $const;
		error_reporting(0);
		set_error_handler([$this, 'fatal_handler']);
		register_shutdown_function([$this, 'fatal_handler2']);

	}

	public function errorHideFilter($arr=[])
	{
		$this->errorHideFilter=$arr;
	}


	public function fatal_handler($errno, $errstr, $errfile, $errline)
	{
		/**
		 * Bu kontrol smarty için gerekli.
		 * Error reporting kapatıldığında smarty hataları ignore edip true false değerlere dönüştürebiliyor
		 * Örneğin {if $success} kontrolü $success değişkeni set edilmemişse bile false olarak algılanabiliyor.
		 */
		if (strpos($errfile, '.tpl.') > 0) {
			return true;
		}
		$this->errorlist[] = [
			"errstr"  => $errstr,
			"errno"   => $errno,
			"errfile" => $errfile,
			"errline" => $errline,

		];


		if (!$errstr) {
			throw new \Exception($errstr);
		}

		return false;
	}

	public function fatal_handler2()
	{

		$error = error_get_last();
		if ($error !== null || count($this->errorlist) > 0) {



			if (isset($error["message"])) {
				$arr[] = [
					"errstr"  => $error["message"],
					"errno"   => $error["type"],
					"errfile" => $error["file"],
					"errline" => $error["line"],
				];

				$this->errorlist = array_merge($arr, $this->errorlist);
			}

			$trace = $this->getTrace();

			$this->errorListFilter();

			if(count($this->errorlist)>0)
			{

				// hata var ise
				new HttpException(500, "Internal Server Error");

				if ($this->debugmode == true) {
					if (ob_get_length())
						ob_clean();

					$htmlerror = $this->show($this->errorlist, $trace);
					echo $htmlerror;

				}

				if($this->fireStatus)
					$this->fireFunc($this->errorlist,$trace);

			}



		}
	}

	/**
	 *
	 * erorlistesi filitre
	 */
	private function errorListFilter()
	{
		foreach ($this->errorlist as $key=>$value)
		{
			foreach ($this->errorHideFilter as $value2)
			{
				if(strpos($value["errstr"], $value2)!==false)
				{
					unset($this->errorlist[$key]);
				}
			}

		}
	}

	public function fire($func)
	{
		$this->func = $func;
	}

	public function fireOn($firestatus)
	{
		$this->fireStatus=$firestatus;
	}

	private function fireFunc($error, $trace)
	{
		if ($this->func != null) {
			call_user_func_array($this->func, [$error, $trace]);
		}
	}

	public function getTrace()
	{
		return debug_backtrace();
	}

	public function getIsError()
	{
		return $this->isError;
	}

	private function show($errorlist, $trace)
	{
		$content = '<html>
                    <head>
                        <meta charset="utf-8">
                    
                        <style>
                            body{
                                margin: 0px;
                            }
                    
                            .header-content{
                                padding: 20px;
                                font-size: 24px;
                                background: #13a3c6;
                                color:#fff;
                            }
                            .header-content .framework
                            {
                                font-size: 16px;
                    
                            }
                            .error{
                                margin-top:10px;
                                clear: both;
                            }
                            .margin_bottom{
                                margin-bottom: 30px;
                            }
                            .line{
                                font-weight: bold;
                                margin-right: 10px;
                            }
                            .content{
                                padding: 20px;
                                font-size:18px;
                                background: #e6e88d;
                                min-height: 100%;
                                overflow-y : auto;
                            }
                            .hr{
                                height:1px;
                                background: #333;
                                width:100%;
                              
                            }
                        </style>
                    </head>
                    <body>
                    
                    
                    <div class="header-content">
                        <div class="framework">Eight Framework 2</div>
                        <div class="error"> <span class="line">Line ' . (isset($errorlist[0]["errline"]) ? $errorlist[0]["errline"] : "") . ':</span> ' . (isset($errorlist[0]["errfile"]) ? $errorlist[0]["errfile"] : "") . '</div>
                    </div>
                    
                    <div class="content">';

		$str = "";

		foreach ($errorlist as $key => $value) {
			$str .= '<div class="error margin_bottom">
                            <div><span class="line">' . ($value["errno"] == 1 ? "FATAL ERROR " : "WARNING") . '  Line ' . $value["errline"] . ' :</span> ' . $value["errfile"] . '</div>
                            <div class="error line"><pre>' . $value["errstr"] . '</pre></div>
                            <div class=""><pre>' . (isset($trace[$key]["errstr"]) ? $trace[$key]["errstr"] : '') . '</pre></div>
                            
                  </div>';
			if (count($errorlist) - 1 != $key)
				$str .= '<div class="hr"></div>';
		}

		$str .= '<div class="hr"></div>';

		$content .= $str;
		$content .= '</div></div>
                    </body>
                    </html>';

		return $content;
	}
}