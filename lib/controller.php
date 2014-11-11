<?php
/*
*	Base class for all Controllers
*	
*	Purpose is to have generic "controller functionality" accessible in all controllers.
*	
*	NOTICE:(Might have to change behavior and location)
*	RedirectTo function is a lazy way of jumping between controllers and/or actions.
*	
*/
class Controller{
	
	public function __construct(){
		
	}
	
	public function redirectTo($strController = '', $strAction = ''){
		$strLocaton = ROOT_PATH . (($strController != '') ? $strController . '/' : '') . (($strAction != '') ? $strAction . '/' : '');
		header('location: ' . $strLocaton);
		die();
	}
}
?>