<?php


class TestLibrary
{

    public function process($a,$b,ListenerInterface $testListener)
    {
        /// işlemler
        ///
        ///
        ///
        ///
        ///
        $json=["sum"=>$a+$b,"foo"=>"bar"];
        $testListener->dinle($json);
    }
}