<?php

namespace EF2\Security\Csrf;

interface CsrfInterface {

    function __construct($connector,$redis=null,$duration=null);

    function bind($namespace=null);

    function getToken();

    function getName();

    function isTokenValid($token);

    function echoInputField();

    function verifyRequest($token);

    function postControl();

    function getControl();
}