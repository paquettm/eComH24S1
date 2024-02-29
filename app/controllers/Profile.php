<?php
namespace app\controllers;

class Profile extends \app\core\Controller{
	public function index(){
		//make sure that the user is logged in
		if(!isset($_SESSION['user_id'])){
			header('location:/User/login');
			return;
		}

		$profile = new \app\models\Profile();
		$profile = $profile->getForUser($_SESSION['user_id']);

		$this->view('Profile/index',$profile);
	}
}