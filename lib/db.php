<?php
/**
*	Help class to make Database interaction easier
*
*/
class db{
	private $arrError = array();
	protected static $con;

	private function connect(){
		if(!isset(self::$con)){
			self::$con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		}
		return (self::$con === false) ? false : self::$con;
	}

	public function query($strSql){
		$con = $this->connect();
		$result = $con->query($strSql);
		if($result === false){
			$this->addError($strSql);
			return false;
		}
		return true;
	}

	public function getScalar($strSql){
		$con = $this->connect();
		$result = $con->query($strSql);
		if($result->num_rows !== 1){
			$this->addError($strSql);
			return false;
		}
		$arr = $result->fetch_row();
		$val = $arr[0];
		return $val;
	}

	public function getRow($strSql){
		$con = $this->connect();
		$result = $con->query($strSql);
		if($result === false || $result->num_rows != 1){
			$this->addError($strSql);
			return false;
		}

		$row = $result->fetch_assoc();
		return $row;
	}

	public function getAsoc($strSql){
		$con = $this->connect();
		$rows = array();
		$result = $con->query($strSql);
		if($result === false){
			$this->addError($strSql);	
			return false;
		}
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}
		return $rows;
	}

	public function wash($strString){
		$con = $this->connect();
		return $con->real_escape_string($strString);
	}

	public function getError(){
		$con = $this->connect();
		return $con->error;
	}

	private function addError($strSql){
		$this->arrError['message'] = $this->getError();
		$this->arrError['sql'] = $strSql;
	}

	public function getErrorArray(){
		return $this->arrError;
	}
}

?>