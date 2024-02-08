<?php
namespace app\controllers;

class Person extends \app\core\Controller{

	function greet(){
		//data input
		$personName = (isset($_GET['personName'])?$_GET['personName']:'friend');
		$someArray = ['one','two','three'];//sequential array
		$assocArray = ['first_name'=>'Alice',
						'last_name'=>'Cooper'];//associative array (dictionary)
		$this->view('Person/greet', ['person_name'=>$personName,
						'numbers'=>$someArray,
						'profile'=>$assocArray]);
	}

	function greet_again(){
		//data input
		$personName = (isset($_GET['personName'])?$_GET['personName']:'friend');
		$someArray = ['one','two','three'];//sequential array

		$profileObj = new \stdClass();//profile Object
		$profileObj->first_name = 'Alice';
		$profileObj->last_name = 'Cooper';
		
		$this->view('Person/greet_again', ['person_name'=>$personName,
						'numbers'=>$someArray,
						'profile'=>$profileObj]);
	}

}