<?php

namespace EF2\Security\Csrf\Adaptor;

use EF2\Components\Session;

class SessionAdaptor
{
    private $session;

    public function __construct()
    {
        $this->createSession();
    }

    public function __call($method, $parameters)
    {

       return call_user_func_array(array($this, $method), $parameters[0]);

    }

    private function createSession()
    {

        $session = new Session;
        $session->setSecurity(md5("aksks88213jj12k%usadjask8"));
        $session->register();
        $this->session= $session;

    }

    private function set($namespace,$token)
    {
        $this->session->set($namespace,$token);
    }

    private function get($namespace)
    {
        return $this->session->get($namespace);
    }

}