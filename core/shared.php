<?php


/**
 *Check if environment is development 
 */
 
function setReporting() 
{
	if (DEVELOPMENT_ENVIRONMENT == true) 
	{
		error_reporting(E_ALL);
		ini_set('display_errors','On');
	} 
	else 
	{
		error_reporting(E_ALL);
		ini_set('display_errors','Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
	}
}



/**
 * Check for Magic Quotes and remove them
 */
function stripSlashesDeep($value) 
{
	$value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	return $value;
}

function removeMagicQuotes() 
{
	if ( get_magic_quotes_gpc() ) 
	{
		$_GET    = stripSlashesDeep($_GET   );
		$_POST   = stripSlashesDeep($_POST  );
		$_COOKIE = stripSlashesDeep($_COOKIE);
	}
}


/**
 * This function loads the appropriate 
 * controller and executes the requested
 * function on that controller
 */
function callHook() {
	
	global $controllerAction;

	if(!empty($controllerAction))
	{
		$urlArray = array();
		$urlArray = explode("/",$controllerAction);

		$controller = $urlArray[0];
		array_shift($urlArray);
		$action = isset($urlArray[0])?$urlArray[0]:'';

		array_shift($urlArray);
		$queryString = $urlArray;
	}
	else
	{
		$controller = "portal";
		$action = "home";
		$queryString = array();
	}

	$controllerName = $controller;
	$controller = ucfirst($controller).'Controller';

       
	$dispatch = new $controller($controllerName,$action);

	if ((int)method_exists($controller, $action))
	{
		call_user_func_array(array($dispatch,$action),$queryString);
	} 
}




/**
 * This function autoloads the controller 
 * for the call hook function
 */
function __autoload($className)
{

    if (file_exists(ROOT . DS . 'core' . DS . strtolower($className) . '.class.php'))
	{
		require_once(ROOT . DS . 'core' . DS . strtolower($className) . '.class.php');
	} 
	else if (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php')) 
	{
		require_once(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php');
	} 
	else if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php')) 
	{
		require_once(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php');
	}
}


setReporting();
removeMagicQuotes();
callHook();


