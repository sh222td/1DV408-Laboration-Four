<?php

namespace controller;

class UserController{
	
	public function _construct(){

	}

	public function index(){
		
	}

	public function view($intId){

	}

	public function add(){
		
	}
	
	/*Temporary sloppy function to create user, move functionality to view and model later*/
	/*public function create($strUsername, $strPassword){
		$strUsername = trim($strUsername);
		$strPassword = trim($strPassword);
		if(preg_match('/[^a-z0-9]/i', $strUsername) || preg_match('/[^a-z0-9]/i', $strPassword)){
			throw new \Exception('Detected unallowed special characters in username or password');
		}
		if(strlen($strUsername) < 4 || strlen($strUsername) > 20){
			throw new \Exception('Username must be between 4 and 20 characters');
		}
		if(strlen($strPassword) < 4 || strlen($strPassword) > 20){
			throw new \Exception('Password must be between 4 and 20 characters');
		}
		$user = new \model\dobj\User();
		$user->setUsername($strUsername);
		$user->setPassword($strPassword);
		$user->scramblePassword();
		if($user->create()){
			$strEcho = '
				<h1>User created</h1>
				<p>Username:' . $user->getUsername() . '</p>
				<p>Password:' . $strPassword . '</p>
			';
		}
		else{
			$strEcho = '<h1>Could not create user</h1>';
		}
		echo $strEcho;
	}*/

	public function edit(){

	}

	public function save(){
		
	}

	public function destroy(){

	}
}
?>