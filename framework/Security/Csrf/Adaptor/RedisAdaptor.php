<?php

namespace EF2\Security\Csrf\Adaptor;

class RedisAdaptor
{
    private $redis;

    public function __construct($redis)
    {
        $this->redis=$redis;
    }

    public function __call($method, $parameters)
    {

        return call_user_func_array(array($this, $method), $parameters[0]);

    }



    private function set($namespace,$token,$duration)
    {
        $this->redis->set($namespace,$token,$duration);
    }

    private function get($namespace)
    {
        return $this->redis->get($namespace);
    }

}