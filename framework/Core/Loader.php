<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */
namespace SF3\Core;

use SF3\Autoload;

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