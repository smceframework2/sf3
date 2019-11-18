<?php

namespace SF3;

use SF3\Console\Create;
use \Exception;
use SF3\Console\Colors;
use SF3\Console\Load;

class Console
{


	private $help = [

        ["tag" => "help", "description" => "php agent --help"],
        ["tag" => "create/controller", "description" => "php agent --create controller name"],
        ["tag" => "create/model", "description" => "php agent --create model name"],
        ["tag" => "create/middleware", "description" => "php agent --create middleware name"],
    ];

    private $words = [
        '--create',
    ];

    private $arrdir = [
        "model", "controller","middleware"
    ];


    private $dir = [];

    private $commands = [];

    private function dirControl($arr)
    {
        foreach ($this->arrdir as $key => $value) {
            if (!isset($arr[$value])) {
                throw new Exception("set Dir '" . $value . "'");
            }

            if (!is_dir($arr[$value])) {
                throw new Exception("not Directory '" . $value . "'");
            }
        }
    }

    public function setDir($arr)
    {

        $this->dirControl($arr);
        $this->dir = $arr;

    }

    public function commands($commands = [])
    {
        $this->commands = $commands;
    }

    public function run()
    {
        $argv = $_SERVER["argv"];


        if (count($this->commands) > 0) {

            $load = new Load($this->commands);

            $load->is($argv);

            $arr = $load->getCommandsarr();


            foreach ($arr as $key => $value) {
                $value = (object)$value;
                $this->help[] = array(
                    "class" => $value->class,
                    "tag" => $value->signature,
                    "description" => $value->description,

                );
            }
        }


        $this->dirControl($this->dir);


        $control[] = false;

        echo "\n";

        $control[] = $this->help($argv);
        $control[] = Create::is($this->dir, $argv);



        if (!in_array(true, $control)) {
            echo "\t" . Colors::string("Undefined words. You can get help. ( php agent --help )", "red") . "\n";
        }

        echo "\n";


    }

    private function help($argv)
    {
        if (!isset($argv[1]) || (isset($argv[1]) && $argv[1] == "--help" && !isset($argv[2]))) {
            foreach ($this->help as $key => $value) {

                if (!array_key_exists("class", $value)) {
                    foreach ($value as $key2 => $value2) {

                        if ($key2 == "tag")
                            echo "  " . Colors::string($value2, "green") . "\t";
                        else
                            echo Colors::string($value2, "yellow") . "\n\n";
                    }
                } else {
                    foreach ($value as $key2 => $value2) {
                        if ($key2 == "class")
                            echo "  " . Colors::string($value2, "blue") . "  ";
                        else if ($key2 == "tag")
                            echo Colors::string($value2, "green") . "  ";
                        else
                            echo Colors::string($value2, "yellow") . "\n\n";
                    }
                }


            }

            return true;
        }

        return false;
    }


}