<?php
/**
*	Model class for Login controller.
*
*	# Session management.
*	# reads $_SERVER variable
*	# Database contact via DAL class to fetch user(s)
*	# Business logic for creating cookie and session information
*/
namespace model;

class LoginModel extends \Model{
	
	private $strSessionKey = 'login';
	private $loginDAL;

    private $errorMSG;
	
	public function __construct(){
		parent::__construct();
		$this->loginDAL = new \model\dal\LoginDAL();
	}
	
	/**
	*	Session-stuff
	*/
	public function createLoginSession($strToken){
		$_SESSION[$this->strSessionKey] = $strToken;
	}
	
	public function loginSessionExists(){
		return (isset($_SESSION[$this->strSessionKey]) && $_SESSION[$this->strSessionKey] !== '') ? true : false;
	}
	
	public function getSessionToken(){
		return $_SESSION[$this->strSessionKey];
	}
	
	public function destroyLoginSession(){
		unset($_SESSION[$this->strSessionKey]);
	}

	/**
	*	Get user objects in various ways
	*/
	public function getUserByToken($strToken){
		return $this->loginDAL->GetUserByToken($strToken);
	}

	public function getUserById($intId){
		return $this->loginDAL->GetUserById($intId);
	}

	public function getUserByUserName($strUserName){
		return $this->loginDAL->GetUserByUserName($strUserName);
	}

	/**
	*	Business logic for Session and cookie data
	*/
	public function checkAgent($user){
		return ($user->getAgent() === $_SERVER['HTTP_USER_AGENT']) ? true : false;
	}
	
	public function checkIp($user){
		return ($user->getIp() === $_SERVER['REMOTE_ADDR']) ? true : false;
	}
	
	public function generateToken(){
		return sha1(uniqid(rand(), true));
	}
	
	public function generateIdentifier(){
		return sha1($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
	}

	public function generateCookieContent($user){
		$strIdentifier = $this->generateIdentifier();
		$strCookieValue = $user->getToken() . ':' . $strIdentifier;
		return $strCookieValue;
	}

	public function updateUserLoginData($user, $boolAddCookieTimeStamp){
		$user->setToken($this->generateToken());
		$user->setIp($_SERVER['REMOTE_ADDR']);
		$user->setAgent($_SERVER['HTTP_USER_AGENT']);
		if($boolAddCookieTimeStamp){
			$user->setCookieTime(time());
		}
		return ($user->save()) ? $user : null;
	}

    public function register($strUsername, $strPassword, $strRepeatPassword){
        $strUsername = trim($strUsername);
        $strPassword = trim($strPassword);
        $strRepeatPassword = trim($strRepeatPassword);

        if($this->getUserByUserName($strUsername) !== null) {
            throw new \Exception('Username is already taken!');
        }
        if(strlen($strUsername) === ''){
            throw new \Exception('Username can not be empty!');
        }
        if(strlen($strPassword) === ''){
            throw new \Exception('Password can not be empty!');
        }
        if(strlen($strRepeatPassword) === ''){
            throw new \Exception('Password can not be empty!');
        }
        if(preg_match('/[^a-z0-9]/i', $strUsername) || preg_match('/[^a-z0-9]/i', $strPassword)){
            throw new \Exception('Detected unallowed special characters in username or password');
        }
        if(strlen($strUsername) < 3 || strlen($strUsername) > 20){
            throw new \Exception('Username must be between 3 and 20 characters');
        }
        if(strlen($strPassword) < 6 || strlen($strPassword) > 20){
            throw new \Exception('Password must be between 6 and 20 characters');
        }
        if(strlen($strRepeatPassword) != strlen($strPassword)) {
            throw new \Exception('The passwords does not match');
        }

        $user = new \model\dobj\User();
        $user->setUsername($strUsername);
        $user->setPassword($strPassword);
        $user->scramblePassword();
        return $user->create();


    }

    public function getErrorMessage() {
        return $this->errorMSG;
    }
}
?>