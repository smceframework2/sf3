<?php

/**
 *
 * @author Eight
 * @copyright 2017 EightFramework 2
 */
namespace EF2\Core;

use EF2\Autoload;

class Loader
{
    private static $dirs=[];

    private $_registered = false;

    public function setDir($dir)
    {

          self::$dirs = array_merge(self::$dirs , $dir);

    }
    

    public function register()
    {
        
        if($this->_registered === false){
            Autoload::registerApp(self::$dirs);
            $this->_registered = true;
        }
        
        return $this;
    }


    
    
    

}