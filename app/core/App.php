<?php
namespace app\core;

class App{
    function __construct(){
    	//call the appropriate controller class and method to handle the HTTP Request

        //Routing version 0.1
        //TODO: add PARAMETERS - later
        $url = $_GET['url'];

        //defined a few routes "url"=>"controller,method"
        $routes = ['Person/register'=>'Person,register',
                    'Person/complete_registration'=>'Person,complete_registration',
                    'Person/'=>'Person,list'];

        //one by one compare the url to resolve the route
        foreach ($routes as $routeUrl => $controllerMethod) {
            if($url == $routeUrl){//match the route
                //run the route
                [$controller,$method]=explode(',', $controllerMethod);
                $controller = '\\app\\controllers\\'.$controller;
                $controller = new $controller();
                $controller->$method();
                //make sure that we don't run a second route
                break;
            }
        }


    }
}