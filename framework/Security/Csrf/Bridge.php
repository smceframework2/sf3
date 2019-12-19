<?php

namespace EF2\Security\Csrf;

use EF2\Security\Csrf\Adaptor\SessionAdaptor;
use EF2\Security\Csrf\Adaptor\RedisAdaptor;

class Bridge
{
    private $connector;

    private $adaptor;

    private $duration;

    public function setAdaptor($connector,$redis)
    {
       $this->connector=$connector;
        if($this->connector==1)
        {
            $this->adaptor=new SessionAdaptor;
        }elseif($this->connector==2)
        {
            $this->adaptor=new RedisAdaptor($redis);
        }
    }

    public function setDuration($duration)
    {
        $this->duration=$duration;
    }


    private function getMethod($call,...$params)
    {
        if($this->connector==1)
        {
            return $this->adaptor->$call($params);
        }elseif($this->connector==2)
        {
            return $this->adaptor->$call($params);
        }
    }

    public function set($namespace,$token)
    {

        $this->getMethod("set",$namespace,$token,$this->duration);
    }

    public function get($namespace)
    {
        if(!empty($this->getMethod("get",$namespace))) {
            return $this->getMethod("get",$namespace);

        }

    }
}