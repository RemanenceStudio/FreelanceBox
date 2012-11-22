<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

print_r($_SESSION);


define('DS', DIRECTORY_SEPARATOR);
define('ROOT',dirname(__FILE__));
define ('PUBLIC_FILES', ''); 

$controllerAction = isset($_GET['a'])?$_GET['a']:'portal/home';
$controllerAction = trim($controllerAction, "/");

require_once ('config' . DS . 'config.php');
require_once ('core' . DS . 'shared.php');

?>