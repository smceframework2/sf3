<?php

/**
 *
 * @author SF3
 * @copyright 2017 SF3Framework
 */

namespace SF3\Core;

use SF3\Http\HttpException;
use SF3\Sf;

class Acl
{
    private $rules = [];

    /**
     * @param $accessRules
     * @param $view
     *
     * @return bool
     */

    public function setRule($rule)
    {

        $this->rules[count($this->rules)] = $rule;

    }

    public function getRules()
    {

        return $this->rules;

    }

    public function run()
    {

        $control = false;
        $isaction=false;

        $ip = Sf::app()->ip;


        foreach ($this->rules as $key => $value) {

            if (in_array(Sf::app()->action, $value["actions"])) {
                $control = true;
                $isaction=true;

                if (isset($value["ip"]) && !in_array($ip, $value["ip"]) == true) {

                    throw new HttpException(403, "You do not have authority to allow");

                }



                if (isset($value["expression"]) && $this->expressionControl($value["expression"]) != true) {
                    $this->redirect($value["redirect"]);

                }


            }

        }

        if($isaction==false)
        {
            throw new HttpException(403, "No Action in Acl");
        }

        if ($control == false) {
            if(isset($value["redirect"]))
            {
                $this->redirect($value["redirect"]);
            }
        }


        return true;

    }


    private function redirect($url)
    {

        header("refresh:4;url=" . Sf::app()->baseUrl . "/" . $url);
        throw new HttpException(404, "You do not have authority to allow");
        exit();

    }


    /**
     * @param $expression
     *
     * @return bool
     */
    private function expressionControl($expression)
    {

        if ($expression === true) {

            return true;
        }
    }

}