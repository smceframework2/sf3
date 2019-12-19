<?php

namespace EF2\Console;

use EF2\Console\Create\Controller;
use EF2\Console\Create\Middleware;
use EF2\Console\Create\Model;

class Create
{

    public static function is($dir,$argv)
    {
        if(isset($argv[1]) && $argv[1]=="--create")
        {

            $control[]=Controller::is($dir["controller"],$argv);
            $control[]=Model::is($dir["model"],$argv);
	        $control[]=Middleware::is($dir["middleware"],$argv);

            if(!in_array(true,$control))
            {
                echo "\t".Colors::string("Undefined words. You can get help. ( php agent --help )","red")."\n";
            }

            return true;
        }

        return false;
    }

}