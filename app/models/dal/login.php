<?php
/**
*	DAL-class for Login model
*
*	# Fetches users from database and returns them as user objects in various ways
*/
namespace model\dal;

class LoginDAL{
	
	public function __construct(){

	}

	public function getUserById($intId){
		$db = new \db();
		$strSql = "
			SELECT
				user.*
			FROM
				user
			WHERE
				user.id = " . intval($intId) . "
			LIMIT
				1
		";
		$r = $db->GetRow($strSql);
		if($r ==! false){
			try{
				$user = new \model\dobj\User($r);
				return $user;
			}
			catch(\Exception $e){
				//Empty
			} 
		}
		return null;
	}

	public function getUserByToken($strToken){
		$db = new \db();
		$strSql = "
			SELECT
				user.*
			FROM
				user
			WHERE
				user.token = '" . $db->Wash($strToken) . "'
			LIMIT
				1
		";
		$r = $db->GetRow($strSql);
		if($r ==! false){
			try{
				$user = new \model\dobj\User($r);
				return $user;
			}
			catch(\Exception $e){
				//Empty
			} 
		}
		return null;
	}

	public function getUserByUserName($strUserName){
		$db = new \db();
		$strSql = "
			SELECT
				user.*
			FROM
				user
			WHERE
				user.username = '" . $db->Wash($strUserName) . "'
			LIMIT
				1
		";
		$r = $db->GetRow($strSql);
		if($r ==! false){
			try{
				$user = new \model\dobj\User($r);
				return $user;
			}
			catch(\Exception $e){
				//Empty
			} 
		}
		return null;
	}

}

?>