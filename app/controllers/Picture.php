<?php
namespace app\controllers;

class Picture extends \app\core\Controller{
	private $folder='uploads/';
	public function index(){
		//implement file uploads

		if(isset($_POST['action'])){
			//get the form data and process it
			if(isset($_FILES['newPicture'])){
				$check = getimagesize($_FILES['newPicture']['tmp_name']);

				$mime_type_to_extension = ['image/jpeg'=>'.jpg',
											'image/gif'=>'.gif',
											'image/bmp'=>'.bmp',
											'image/png'=>'.png'
											];

				if($check !== false && isset($mime_type_to_extension[$check['mime']])){
					$extension = $mime_type_to_extension[$check['mime']];
				}else{
					$this->view('Picture/index', ['error'=>"Bad file type",'pictures'=>[]]);
					return;
				}

				$filename = uniqid().$extension;
				$filepath = $this->folder.$filename;

				if($_FILES['newPicture']['size'] > 4000000){
					$this->view('Picture/index', ['error'=>"File too large",'pictures'=>[]]);
					return;
				}
				if(move_uploaded_file($_FILES['newPicture']['tmp_name'], $filepath)){
					$picture = new \app\models\Picture();
					$picture->filename = $filename;
					$picture->insert();
					header('location:/Picture/index');
				}
				else
					echo "There was an error";
			}
		}else{
			//present the form
			$picture = new \app\models\Picture();
			$pictures = $picture->getAll();
			$this->view('Picture/index',['error'=>null,'pictures'=>$pictures]);
		}
	}
}
