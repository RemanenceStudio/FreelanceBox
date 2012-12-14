<?php 




/** ENVIRONMENT TYPE **/
/** Manages whether application errors should be displayed to the user
 * Should be false for production environments */
define('DEVELOPMENT_ENVIRONMENT', false);


/** DATABASE CONNECTION VARIABLES **/
define('DB_NAME', 'freelancebox');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');


/** USER RIGHTS VARIABLES **/
define('ADMIN', 0);
define('USER', 1);
define('VIEWER', 2);


/**CONFIG VALUES**/
global $CONFIG;



/** BASE URL **/
$CONFIG['base_url'] = 'http://localhost/FreelanceBox/';

$CONFIG['company']['name'] = ''; 
$CONFIG['company']['address'] = '';
$CONFIG['company']['address_2'] = '';
$CONFIG['company']['email'] = '';
$CONFIG['company']['phone'] = '';


//IMPORTANT: DO NOT use png files with transparency. PDF generation will fail.
$CONFIG['company']['logo'] = 'application/views/images/logo.jpg';

$CONFIG['project']['default_phases'] = 'Planning, Design, Development, Testing';

$CONFIG['invoice']['base_invoice_number'] = 201000;
$CONFIG['invoice']['default_terms'] = 'All totals are final and non-negotiable. Payments must be made by the specified due date with no exceptions. Mailed checks must be postmarked by the due date above.';

$CONFIG['uploads']['path'] = ROOT . '/ds/';
$CONFIG['uploads']['web_path'] = $CONFIG['base_url'] . 'ds/';
$CONFIG['uploads']['max_file_size'] = 200000000;
$CONFIG['uploads']['allow_client_uploads'] = true;


?>