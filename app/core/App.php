<?php
namespace app\core;

class App{
    function __construct(){
    	//call the appropriate controller class and method to handle the HTTP Request

        //transition to routing - later

        //hardcode a call to a controller method
        $controller = new \app\controllers\Person();
        $controller->greet_again();//call greet_again from the $controller object
    }
}