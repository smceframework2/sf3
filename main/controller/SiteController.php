<?php

use SF3\Core\Controller;
use SF3\Http\Request;
use SF3\Http\Response;

class SiteController extends Controller
{

    public function actionIndex(Request $request,Response $response)
    {

        echo "Hell world";
    }
}