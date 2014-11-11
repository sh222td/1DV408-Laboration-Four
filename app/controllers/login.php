<?php
/**
*	Controller class for Loggin in.
*	@actions	: index, add, create, destroy, success
*	@methods	: checkSignIn, signInWithCookie
*/
namespace controller;

class LoginController extends \Controller{
	
	private $view;
	private $loginModel;

    private  $errorMSG;
	
	public function __construct(){
		parent::__construct();
		$this->loginModel = new \model\LoginModel();
		$this->view = new \view\LoginView($this->loginModel);
	}
	
	/**
	*	Index is the default function for all controllers, this redirects the default to NewSession instead.
	*/
	public function index(){
		$this->add();
	}
	
	/**
	*	Controller action for rendering login-form.
	*/
	public function add(){
		if(!$this->checkSignIn()){
			$this->view->Render($this->view->add());
		}
		else{
			$this->redirectTo('Login', 'success');
		}
	}

    public function register(){
        $this->view->render($this->view->register());
        if ($this->view->checkRegistrationButton()) {
            try {
                $strUsername = $this->view->getSignInUserName();
                $strPassword = $this->view->getSignInPassword();
                $strRepeatPassword = $this->view->getSignInRepeatPassword();
                $r = $this->loginModel->register($strUsername, $strPassword, $strRepeatPassword);
                if ($r = true) {
                    $this->view->addFlash(\View\LoginView::RegistrationSuccess, \View::FlashClassSuccess);
                    $this->redirectTo('Login');
                } else {
                    $this->view->addFlash(\View\LoginView::RegistrationFailSuccess, \View::FlashClassError);
                    $this->redirectTo('Login', 'register');
                }

            } catch (\Exception $e) {
                $this->view->addFlash($e->getMessage(), \View::FlashClassError);
                $this->redirectTo('Login', 'register');
            }
        }

    }
	
	/**
	*	Controller action that login-form posts to
	*/
	public function create(){
		//Check that the form was posted
		if($this->view->checkSignInTry()){
			//Get all necessary variables from sign in form
			try{
				$strUserName = $this->view->getSignInUserName();
				$strPassword = $this->view->getSignInPassword();
				$boolRemeber = $this->view->getKeepMeLoggedIn();
			}
			catch(\Exception $e){
				$this->view->addFlash($e->getMessage(), \View::FlashClassError);
				$this->redirectTo('Login');
			}
			
			//Get user based on input from sign in form
			$user = $this->loginModel->getUserByUserName($strUserName);
			
			//Make sure a user was found and also that the password was correct
			if($user !== null && $user->auth($strPassword)){
				//Create sign in-token. Update login time, user agent and ip on the user
				$user = $this->loginModel->updateUserLoginData($user, $boolRemeber);
				if($user !== null){
					//Create a persistent cookie if that was requested
					if($boolRemeber){
						$this->view->createAuthCookie($user);
					}
					//Finally set login-session that determines a successfull login
					$this->loginModel->createLoginSession($user->getToken());
					$this->view->addFlash(\View\LoginView::SignInSuccess, \View::FlashClassSuccess);
					$this->redirectTo('Login', 'success');
				}
				else{
					$this->view->addFlash(\View\LoginView::AuthFail, \View::FlashClassError);
					$this->redirectTo('Login');
				}
			}
			//Could not auth user, either username and/or password was faulty. 
			else{
				$this->view->addFlash(\View\LoginView::AuthFail, \View::FlashClassError);
				$this->redirectTo('Login');
			}
		}
		//if the login-form was not posted, url was changed manually so redirect to Sign in form
		$this->redirectTo('Login');
	}
	
	/**
	*	Controller action for signing out
	*/
	public function destroy(){
		$this->loginModel->destroyLoginSession();
		if($this->view->authCookieExists()){
			$this->view->destroyAuthCookie();
		}
		$this->view->addFlash(\View\LoginView::SignOutSuccess, \View::FlashClassSuccess);
		$this->redirectTo('Login');
	}
	
	/**
	*	Controller action for rendering successfull login page
	*/
	public function success(){
		if(!$this->checkSignIn()){
			$this->redirectTo('Login');
		}
		else{
			$this->view->render($this->view->success());
		}
	}

	/**
	*	Method to check if a user is signed in or has a persistent auth cookie that can be used to sign in
	*/
	public function checkSignIn(){
		$boolSuccess = false;
		if($this->loginModel->loginSessionExists()){
			$user = $this->loginModel->getUserByToken($this->loginModel->getSessionToken());
			if($user !== null){
				//Check if the User agent is the same in the DB as on the client
				if(!$this->loginModel->checkAgent($user)){
					$this->view->addFlash(\View\LoginView::UnknownAgent, \View::FlashClassError);
				}
				//Check the IP-address from DB and client
				else if(!$this->loginModel->checkIp($user)){
					$this->view->addFlash(\View\LoginView::UnknownIp, \View::FlashClassError);
				}
				else{
					$boolSuccess = true;
				}
			}
		}
		else{
			if($this->view->authCookieExists()){
				if(!$this->signInWithCookie()){
					$this->view->addFlash(\View\LoginView::CookieLoginFail, \View::FlashClassError);
                    $this->view->destroyAuthCookie();
				}
				else{
					$this->view->addFlash(\View\LoginView::CookieLogin, \View::FlashClassSuccess);
					$boolSuccess = true;
				}
			}
		}
		return $boolSuccess;
	}
	
	/**
	*	Method for signing in a user with an auth cookie
	*/
	public function signInWithCookie(){
		$arrCookie = explode(':', $this->view->getAuthCookie());
		$strCookieToken = $arrCookie[0];
		$strCookieIdentifier = isset($arrCookie[1]) ? $arrCookie[1] : null;
		$user = $this->loginModel->getUserByToken($strCookieToken);
		if($user !== null){
			$strCurrentVisitorIdentifier = $this->loginModel->generateIdentifier();
			//Compare identification string from cookie to newly generated one
			if($strCurrentVisitorIdentifier === $strCookieIdentifier){
				//Check in database on user when cookie was created, add the amount of time the view saves cookies.(time cookie was created + 30 days)
				//If the time right now is less than that(time created + 30 days) it's presumed that the cookie expire date has been tampered with
				if(($user->getCookieTime() + $this->view->getAuthCookieTime()) > time()){
					$this->loginModel->createLoginSession($user->getToken());
					return true;
				}
			}
		}
		return false;
	}
	
	
}
?>