<?php
/*
*	Base class for views.
*
*	Purpose is to show success/error/warning-messages to the user.
*	They can be added from a controller or the view it self.
*	They are stored as an array in the session-variable so that they live longer than a page load.
*
*/

class View extends AppView{
	const FlashClassError = 'error';
	const FlashClassSuccess = 'success';
	const FlashClassWarning = 'warning';

	protected $strActionHtml;

	protected $strFlashKey = 'View::FlashMessages';
	
	public  function __construct(){
		parent::__construct();
	}
	
	public function addFlash($strMessage, $strType){
		$_SESSION[$this->strFlashKey][$strType][] = $strMessage;
	}
	
	protected function renderFlash(){
		$arrFlash = (isset($_SESSION[$this->strFlashKey])) ? $_SESSION[$this->strFlashKey] : array();
		$strFlash = '';
		foreach($arrFlash as $type => $arrMessages){
			$strMessages = '';
			foreach($arrMessages as $strMessage){
				$strMessages .= '<div class="flash-message flash-' . $type . '">' . $strMessage . '<span class="close">hide&and;</span></div>';
			}
			$strFlash .= '<div class="flash" />' . $strMessages . '</div>';
		}
		unset($_SESSION[$this->strFlashKey]);
		return $strFlash;
	}

	public function setActionHtml($strActionHtml){
		$this->strActionHtml = $strActionHtml;
	}
}

?>