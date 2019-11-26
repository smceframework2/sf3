<?php

use SF3\Core\Controller;
use SF3\Http\Request;
use SF3\Http\Response;

class SiteController extends Controller
{

    public function actionIndex2(Request $request,Response $response)
    {

        echo "Hell world";
    }

    public function actionIndex3(Request $request,Response $response)
    {

        echo "Hell world<br>";

        (new TestLibrary())->process(5,2,new class implements ListenerInterface{

            function dinle($array)
            {
                print_r($array);
            }
        });

        echo "<br>finish";
    }
}