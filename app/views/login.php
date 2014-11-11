<?php
/**
*	View class for Login controller.
*
*	# Auth cookie manager.
*	# Error-, warning- and notification-messages
*	# HTML-for rendering controller action pages
*	# Getters for $_POST-variables from login form
*/
namespace view;

class LoginView extends \View{
	
	/**
	*	Constants for keeping track of error messages, warnings and notifications
	*/
	const EmptyUserName = 'Username can not be empty!';
	const NotAllowedCharsUsername = 'Detected unallowed special characters in username. (allowed characters: a-z, A-Z, 0-9)';
	const UsernameLengthError = 'Username must be between 3 and 20 characters';
	
	const EmptyPassword = 'Password can not be empty!';
	const NotAllowedCharsPassword = 'Detected unallowed special characters in password. (allowed characters: a-z, A-Z, 0-9)';
	const PasswordLengthError = 'Password must be between 6 and 20 characters';
    const UnsimilarPasswords = 'The passwords does not match';

    const RegistrationSuccess = 'Registration was successful';
    const RegistrationFailSuccess = 'Registration was not successful';
	const SignInSuccess = 'Sign in successful!';
	const SignOutSuccess = 'Sign out successfull';
	const AuthFail = 'Incorrect Username or password.';
	const UnknownIp = 'The IP-address is not known to this account, Sign out was forced(add IP to known list later).';
	const UnknownAgent = 'Unknown user agent. Suspected session hijacking forced sign out.';
	const CookieCreated = 'Cookie for persistent connection created.';
	const CookieDestroyed = 'Cookie for persistent connection destroyed';
	const CookieLogin = 'Successfully signed in with persistent cookie.';
	const CookieLoginFail = 'Could not Sign in with persistent cookie.';
	
	private $intCookieTime = 2592000; //60*60*24*30 = 30 days
	private $strCookieName = 'auth';
	
	private $loginModel;

	public function __construct($loginModel){
		parent::__construct();
		$this->loginModel = $loginModel;
	}
	
	/**
	*	Basic check to see if the login form was posted or not, i.e. not a GET-request.
	*	@return bool
	*/
	public function checkSignInTry(){
		return isset($_POST['username']);
	}
	
	/**
	*	Returns username from login form if present and correct format 
	*	@return string "username"
	*/
	public function getSignInUserName(){
		if(!isset($_POST['username']) || $_POST['username'] === ''){
			throw new \Exception(self::EmptyUserName);
		}
		$strUsername = trim($_POST['username']);
		if(preg_match('/[^a-z0-9]/i', $strUsername)){
            throw new \Exception(self::NotAllowedCharsUsername);
		}
		if(strlen($strUsername) < 3 || strlen($strUsername) > 20){
            throw new \Exception(self::UsernameLengthError);
		}
		return $strUsername;
	}

	/**
	*	Returns password from login form if present and correct format 
	*	@return string "password"
	*/
	public function getSignInPassword(){
		if(!isset($_POST['password']) || $_POST['password'] === ''){
            throw new \Exception(self::EmptyPassword);
		}
		$strPassword = trim($_POST['password']);
        $strRepeatPassword = trim($_POST['verifypassword']);
		if(preg_match('/[^a-z0-9]/i', $strPassword)){
			throw new \Exception(self::NotAllowedCharsPassword);
		}
		if(strlen($strPassword) < 6 || strlen($strPassword) > 20){
			throw new \Exception(self::PasswordLengthError);
		}
        /*if(strlen($strPassword) != strlen($strRepeatPassword)) {
            throw new \Exception(self::UnsimilarPasswords);
        }*/
		return $strPassword;
	}

    public function getSignInRepeatPassword(){
        if(!isset($_POST['verifypassword']) || $_POST['verifypassword'] === ''){
            throw new \Exception(self::EmptyPassword);
        }
        $strPassword = trim($_POST['password']);
        $strRepeatPassword = trim($_POST['verifypassword']);
        if(preg_match('/[^a-z0-9]/i', $strRepeatPassword)){
            throw new \Exception(self::NotAllowedCharsPassword);
        }
        if(strlen($strRepeatPassword) < 6 || strlen($strRepeatPassword) > 20){
            throw new \Exception(self::PasswordLengthError);
        }
        /*if(strlen($strRepeatPassword) != strlen($strPassword)) {
            throw new \Exception(self::UnsimilarPasswords);
        }*/
        return $strRepeatPassword;
    }

	/**
	*	Check if user wished for persitent login
	*	@return bool
	*/
	public function getKeepMeLoggedIn(){
		return isset($_POST['keep-me-signed-in']);
	}
	
	/**
	*	Check if there is an autgh cookie present
	*	@return bool
	*/
	public function authCookieExists(){
		return isset($_COOKIE[$this->strCookieName]);
	}
	
	/**
	*	Creates an auth cookie based on user data.
	*	@return void
	*/
	public function createAuthCookie($user){
		$strCookieContent = $this->loginModel->generateCookieContent($user);
		$intCookieTime = $user->getCookieTime() + $this->intCookieTime;
		setcookie($this->strCookieName, $strCookieContent, time() + $this->intCookieTime, '/');
		$this->addFlash(self::CookieCreated, self::FlashClassWarning);
	}
	
	/**
	*	Destroys auth cookie
	*	@return void
	*/
	public function destroyAuthCookie(){
		unset($_COOKIE[$this->strCookieName]);
		setcookie($this->strCookieName, '', time()-3600, '/');
		$this->addFlash(self::CookieDestroyed, self::FlashClassWarning);
	}
	
	/**
	*	Returns the value of auth cookie
	*	@return string
	*/
	public function getAuthCookie(){
		return $_COOKIE[$this->strCookieName];
	}
	
	/**
	*	Return the amount of seconds an auth cookie is saved
	*	@return bool
	*/
	public function getAuthCookieTime(){
		return $this->intCookieTime;
	}


    /* errrorMSG */
    public function setErrorMessage($errorMSG) {
        $this->errorMSG = $errorMSG;
    }

	
	/**
	*	Render method for controller action "add"
	*/
	public function add(){
		return '
			<h2>Not signed in</h2>
			' . $this->RenderFlash() .'
			<div id="RegisterText">
			    <a href="' . ROOT_PATH . 'Login/Register">Register new user</a>
			</div>
			<div id="SignInForm">
				<form method="post" action="' . ROOT_PATH . 'Login/Create">
					<div class="form-row">
						<label for="username">Username</label>
						<input type="text" name="username" id="username" />
					</div>
					<div class="form-row">
						<label for="password">Password</label>
						<input type="password" name="password" id="password" />
					</div>
					<div class="form-row">
						<label for="keep-me-signed-in">Keep me signed in</label>
						<input type="checkbox" id="keep-me-signed-in" name="keep-me-signed-in" />
					</div>
					<div class="form-row">
						<input type="submit" value="Sign in" />
					</div>
					<div class="clear"></div>
				</form>
			</div>
			' . $this->renderDateTimeString() . '
		';
	}
	
	/**
	*	Render method for controller action "success"
	*/
	public function success(){
		$user = $this->loginModel->getUserByToken($this->loginModel->getSessionToken());
		return '
			<h2>Signed in as: ' . $user->getUsername() . '</h2>
			' . $this->RenderFlash() .'
			<div>
				<p>Page for logged in users.</p>
				<p><a href="' . ROOT_PATH . 'Login/Destroy">Sign out</a></p>
			</div>
			' . $this->renderDateTimeString() . '
		';
	}

    public function register(){
        return '
			<h2>Not signed in, register new user</h2>
			' . $this->RenderFlash() .'
			<div id="RegisterText">
			<div>
                <a href="' . ROOT_PATH . 'Login">Return to previous page</a>
            </div>
			</div>
			<div id="SignInForm">
				<form method="post" action="' . ROOT_PATH . 'Login/Register">
					<div class="form-row">
						<label for="username">Username</label>
						<input type="text" name="username" id="username" />
					</div>
					<div class="form-row">
						<label for="password">Password</label>
						<input type="password" name="password" id="password" />
					</div>
					<div class="form-row">
						<label for="password">Repeat password</label>
						<input type="password" name="verifypassword" id="password" />
					</div>
					<div class="form-row">
						<input type="submit" name="regButton" value="Register" />
					</div>
					<div class="clear"></div>
				</form>
			</div>
			' . $this->renderDateTimeString() . '
		';
    }

    public function checkRegistrationButton() {
        if (isset($_POST['regButton'])) {
            return true;
        }
    }

    /*public function checkUserName() {
        if (isset($_POST['regusername'])) {
            return true;
        }
    }

    public function checkPassword() {
        if (isset($_POST['regpassword'])) {
            return true;
        }
    }

    public function checkRepeatPassword() {
        if (isset($_POST['verifypassword'])) {
            return true;
        }
    }*/

	private function renderDateTimeString(){
		$arrDays = array('Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag', 'Söndag');
		$arrMonths = array('Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December');
		$strDay = $arrDays[$date = date('N') - 1];
		$strMonth = $arrMonths[$date = date('n') - 1];
		return $strDay .', den ' . date('j') . ' ' . $strMonth . ' år ' . date('Y') . '. Klockan är [' . date('H:i:s') . '].';
	}
}
?>