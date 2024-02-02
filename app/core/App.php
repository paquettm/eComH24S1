<?php
namespace app\core;

class App{
    function __construct(){
    	//call the appropriate controller class and method to handle the HTTP Request

        echo 'You have constructed your App object';

        //hardcode a call to a controller method
        $controller = new \app\controllers\Person();
        $controller->greet();//call greet from the $controller object
    }
}