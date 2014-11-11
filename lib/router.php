<?php
/*
*
*	Loader(or Routing) class.
*	Every request(or page-load) goes through this Loader class from index.php.
*	It interprets the request to determine what controller to use and what function to run in that controller.
*	
*	It requires mod_rewrite in apaches httpd.conf to be enabled. This so that .htaccess can rewrite urls like this:
*	/index.php?c=UserController&Action=Save&UserId=5
*	to:
*	/User/save/5
*
*	With this, URLs can easily be split into controller, action and arguments.
*	Checks are performed to make sure there is a controller class and action-function.
*	
*/
class Router{
	private $strUrl;
	private $strController;
	private $strAction;
	private $arrArgs = array();

	private $controller;
	
	public function __construct(){
		$this->strUrl = isset($_GET['url']) ? $_GET['url'] : '';
		$this->parseURL();
		if($this->setup()){
			$this->dispatch();
		}
	}
	
	//Split URL to get controller, action and arguments/parameters
	private function parseURL(){
		$arrUrl = explode('/', $this->strUrl);
		
		$this->strController = (isset($arrUrl[0]) && $arrUrl[0] !== '') ? ucfirst(strToLower($arrUrl[0])) : DEFAULT_CONTROLLER;
		$this->strAction = (isset($arrUrl[1]) && $arrUrl[1] !== '') ? ucfirst(strToLower($arrUrl[1])) : DEFAULT_ACTION;
		
		for($i = 2; $i < count($arrUrl); $i ++){
			$this->arrArgs[] = $arrUrl[$i];
		}
	}
	
	/*
	*	Confirm class and action-function. If passed, runs the controller class and it's function and sends the arguments as parameters to it.
	*	A public controller function like this always returns output to be presented to the user(mostly html-code).
	*	That resulting output is passed on to a Render-function, that uses a Layout class to render the complete page for the user.
	*/
	private function setup(){
		$strController = '\controller\\' . $this->strController . 'Controller';

		if(class_exists($strController)){
			$this->controller = new $strController();
			if(method_exists($strController, $this->strAction)){
				return true;
			}
			else{
				echo 'Can not find Action: ' . $this->strAction . ' in Controller: ' . $strController;
			}
		}
		else{
			echo 'Can not find Controller: ' . $strController;
		}
		return false;
	}

	private function dispatch(){
		//$this->controller->BeforeAction(); //figure out behaviour for this
		call_user_func_array(array($this->controller, $this->strAction), $this->arrArgs);
	}
	
}

/*
*	Loads class files automaticly with help of namespaces
*	Inspiration from: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
*/
function AutoLoadClasses($class){
	$class = ltrim($class, '\\');
	
	if(strripos($class, '\\') !== false){
		$strClassName = strToLower(substr($class, strripos($class, '\\') + 1));
		$arrNamespaces = explode('\\', $class, -1);
		$strClassName = str_ireplace($arrNamespaces, '', $strClassName);
		
		$arrNamespaces[0] = $arrNamespaces[0] . 's';
		$strNamespace = strToLower(implode(DS, $arrNamespaces));
		
		$strFilePath = APP_DIR . $strNamespace . DS . $strClassName . '.php';
	}
	else{
		$strClassName = strToLower($class);
		$strFilePath = LIB_DIR . $strClassName . '.php';
	}
	if(!file_exists($strFilePath)){
		return false;
	}
	require_once($strFilePath);
}

spl_autoload_register('AutoLoadClasses');
?>