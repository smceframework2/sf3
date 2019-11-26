<?php


use SF3\Framework;
use SF3\Core\DI;
use SF3\Core\Router;
use SF3\Core\Loader;
use SF3\Http\HttpException;
use SF3\Debug;
use SF3\Db\Eloquent;

/* framework include */
$framework_path=dirname(__FILE__).'/framework';
require_once $framework_path . '/Framework.php';

$sf3=new Framework;
$sf3->register();



DI::bind("loader",function(){

    $loader=new Loader;
    $loader->setDir(array(
        dirname(__FILE__)."/main/controller/",
        dirname(__FILE__)."/main/model/",
        dirname(__FILE__)."/main/listener/",
        dirname(__FILE__)."/main/library/",
    ));
    $loader->register();
    return $loader;

});





/* route ayarlama. açılış controller ve action */
$baseUrl=baseUrl();
DI::bind("router",function()use($baseUrl){
    $router = new Router;
    $router->route(dirname(__FILE__)."/route.php");
    $router->handle($baseUrl);
    return $router;
});

DI::bind("debug", function () {

    $debug = new Debug;
    $debug->register(Debug::DEVELOPMENT);
    return $debug;

});


function baseUrl()
{

    $uri=str_replace("/index.php", "", $_SERVER["SCRIPT_NAME"]);

    return str_replace($uri, "", $_SERVER['REQUEST_URI']);
}

try{

    $sf3->make();

}catch(HttpException $e)
{
    echo $e->getHttpCode()." ".$e->getMsg();
}