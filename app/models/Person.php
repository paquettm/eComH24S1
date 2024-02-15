<?php
namespace app\models;

class Person{
	public $first_name;
	public $last_name;
	public $email;
	public $weekly_flyer;
	public $mailing_list;

	public function __construct($object = null){
		if($object == null)
			return;// avoid this running when there is no parameter....
		$this->first_name = $object->first_name;
		$this->last_name = $object->last_name;
		$this->email = $object->email;
		$this->weekly_flyer = $object->weekly_flyer;
		$this->mailing_list = $object->mailing_list;
	}


	/* insert the record in the data file */
	public function insert(){
		$filename = 'resources/People.txt';
		//open a file for writing (append)
		$file_handle = fopen($filename, 'a'); //a is for append, w for writing from the start
		//obtain exclusive access to the file to avoid data corruption
		flock($file_handle, LOCK_EX);
		//format the data and write to the file
		$data = json_encode($this);
		fwrite($file_handle, $data . "\n");//place a single record on each line
		//release the exclusive access to the file
		flock($file_handle, LOCK_UN);
		//close the file
		fclose($file_handle);
	}

	public static function getAll(){
		//read the file and return the collection of people (all Person records)
		$filename = 'resources/People.txt';
		$records = file($filename);
		//TODO: process the JSON strings into objects
		foreach ($records as $key => $value) {
			//can I typecase objects in PHP?
			$object = json_decode($value);
			$person = new \app\models\Person($object);
			$records[$key] = $person;
		}
		return $records;
	}

}