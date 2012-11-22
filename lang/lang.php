<?php

if( isset($_GET['lang']) ) $_SESSION['lg'] = $_GET['lang'];

switch($_SESSION['lg']) {
	case 'fr':
		include('lang/fr.php');
	break;
	case 'en':
		include('lang/en.php');
	break;
	default:
		include('lang/fr.php');
	break;
}

 ?>