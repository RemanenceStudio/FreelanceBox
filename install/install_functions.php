<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 11, 2010
 * Time: 6:39:35 AM
 */

$controllerAction = isset($_GET['a']) ? $_GET['a'] : '';

if (!empty($controllerAction))
{
    $urlArray = array();
    $urlArray = explode("/", $controllerAction);

    $controller = $urlArray[0];
    array_shift($urlArray);
    $action = isset($urlArray[0]) ? $urlArray[0] : '';

    array_shift($urlArray);
    $queryString = $urlArray;
}

$action = isset($action) ? $action : '';

switch ($action)
{
    case 'db':

        $vars[0] = $_POST['host'];
        $vars[1] = $_POST['user'];
        $vars[2] = $_POST['password'];
        test_db_connection($vars[0], $vars[1], $vars[2]);
        break;
    case 'permissions':
        check_folder_permissions();
        break;
    case 'save_final_step':
        save_final_step($_POST);
    case 'import_sql':
        import_sql();
        break;
    case 'write_config':
        write_config();
        break;
}

function save_final_step($post)
{
    @session_start();
    $_SESSION['dbname'] = $post['dbname'];
    $_SESSION['base_url'] = $post['base_url'];

    return true;
}


function import_sql()
{
    @session_start();

    $dbHandle = mysql_connect($_SESSION['host'], $_SESSION['user'], $_SESSION['pass']);

    if ($dbHandle != 0)
    {
        $result = mysql_query("CREATE database " . $_SESSION['dbname'], $dbHandle);


        $clients = 'CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `contact_phone` varchar(30) NOT NULL,
  `address_line_1` varchar(300) NOT NULL,
  `address_line_2` varchar(300) NOT NULL,
  `additional_contacts` text NOT NULL,
  `welcome` tinyint(1) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';

        $files = 'CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `project` int(11) NOT NULL,
  `phase` int(11) NOT NULL,
  `posted_by` int(11) NOT NULL,
  `description` varchar(150) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `size` int(11) NOT NULL,
  `path` varchar(300) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';

        $invoices = 'CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `invoice_number` varchar(11) NOT NULL,
  `date_of_issue` int(11) NOT NULL,
  `due_date` int(11) NOT NULL,
  `total` float NOT NULL,
  `payments` float NOT NULL,
  `balance` float NOT NULL,
  `terms` text NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';

        $invoice_items = 'CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `item_name` varchar(300) NOT NULL,
  `description` text NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `item_rate` float NOT NULL,
  `item_type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';

        $messages = 'CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_object` varchar(20) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `posted_by` int(11) NOT NULL,
  `message` text NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';

        $payments = 'CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';

        $projects = 'CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `client_id` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `phases` varchar(400) NOT NULL,
  `progress` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';

        $sessions = 'CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1';

        $users = 'CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `tmp_pass` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';

        $admin_client_record = "INSERT INTO `clients` (`id`, `group_id`, `name`, `contact_person`, `contact_email`, `contact_phone`, `welcome`, `created`, `modified`) VALUES
(1, 0, 'Admin', 'Admin', 'admin', '', 0, 0, 0)";

        $admin_users_record = "INSERT INTO `users` (`id`, `client_id`, `password`, `salt`, `tmp_pass`) VALUES (1, 1, 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', '', '')";
        if (mysql_select_db($_SESSION['dbname'], $dbHandle))
        {
            $result = mysql_query($clients, $dbHandle);
            $result = mysql_query($files, $dbHandle);
            $result = mysql_query($invoices, $dbHandle);
            $result = mysql_query($invoice_items, $dbHandle);
            $result = mysql_query($messages, $dbHandle);
            $result = mysql_query($payments, $dbHandle);
            $result = mysql_query($projects, $dbHandle);
            $result = mysql_query($sessions, $dbHandle);
            $result = mysql_query($users, $dbHandle);
            $result = mysql_query($admin_users_record, $dbHandle);
            $result = mysql_query($admin_client_record, $dbHandle);

            echo "<div class='success'>SQL Imported</div>";
        } else echo "<div class='error'>Error Importing SQL</div>";
    } else echo "<div class='error'>Error Importing SQL</div>";
}


function check_folder_permissions()
{
    ini_set('display_errors', 'Off');
    $chmod = substr(decoct(fileperms('../ds')), 2);

    $result = "<div class='error_list'>";
    if ($chmod >= 777)
    {
        $ds = true;
        $result .= "<div  class='success'><strong>ds</strong> permissions are correct</div>";
    } else $result .= "<div class='error'><strong>ds</strong> permissions are $chmod. They should be 777</div>";

    $chmod2 = substr(decoct(fileperms('../config')), 2);

    if ($chmod2 == 777)
    {
        $config = true;
        $result .= "<div  class='success'><strong>config</strong> permissions are correct</div>";
    } else $result .= "<div  class='error'><strong>config</strong> permissions are $chmod. They should be 777</div>";

    if ($chmod == 777 && $chmod2 == 777)
    {
        if (!is_dir('../ds/thumbs'))
        {
            if (!mkdir('../ds/thumbs'))
                $result .= "<div  class='error'><strong>Error creating thumbs directory. Please create manually. See instructions</div>";
        }

        if (!is_dir('../ds/invoices'))
        {
            if (!mkdir('../ds/invoices'))
                $result .= "<div  class='error'><strong>Error creating invoices directory. Please create manually. See instructions</div>";
        }
    }

    $fp = fopen('../ds/.htaccess', 'w');
    fwrite($fp, "order deny,allow\n");
    fwrite($fp, "deny from all\n");
    fwrite($fp, "allow from " . $_SERVER['SERVER_ADDR']);

    $result .= "</div>";

    echo (($ds && $config) ? '1' : '0') . $result;
}

function write_config()
{
    $fp = fopen('../config/config.php', 'w');

    @session_start();

    fwrite($fp, "<?php \n\n\n");
    fwrite($fp, "\n\n/** ENVIRONMENT TYPE **/\n/** Manages whether application errors should be displayed to the user\n * Should be false for production environments */\n");
    fwrite($fp, "define('DEVELOPMENT_ENVIRONMENT', false);\n");
    fwrite($fp, "\n\n/** DATABASE CONNECTION VARIABLES **/\n");
    fwrite($fp, "define('DB_NAME', '" . $_SESSION['dbname'] . "');\n");
    fwrite($fp, "define('DB_USER', '" . $_SESSION['user'] . "');\n");
    fwrite($fp, "define('DB_PASSWORD', '" . $_SESSION['pass'] . "');\n");
    fwrite($fp, "define('DB_HOST', '" . $_SESSION['host'] . "');\n");
    fwrite($fp, "\n\n/** USER RIGHTS VARIABLES **/\n");
    fwrite($fp, "define('ADMIN', 0);\n");
    fwrite($fp, "define('USER', 1);\n");
    fwrite($fp, "define('VIEWER', 2);\n");

    fwrite($fp, "\n\n/**CONFIG VALUES**/\n");
    fwrite($fp, "global \$CONFIG;\n\n");
    fwrite($fp, "\n\n/** BASE URL **/\n");
    fwrite($fp, '$CONFIG[\'base_url\'] = \'' . $_SESSION['base_url'] . '\';' . "\n\n");
    fwrite($fp, '$CONFIG[\'company\'][\'name\'] = \'\'; ' . "\n");
    fwrite($fp, '$CONFIG[\'company\'][\'address\'] = \'\';' . "\n");
    fwrite($fp, '$CONFIG[\'company\'][\'address_2\'] = \'\';' . "\n");
    fwrite($fp, '$CONFIG[\'company\'][\'email\'] = \'\';' . "\n");
    fwrite($fp, '$CONFIG[\'company\'][\'phone\'] = \'\';' . "\n");
    fwrite($fp, "\n\n//IMPORTANT: DO NOT use png files with transparency. PDF generation will fail.\n");
    fwrite($fp, '$CONFIG[\'company\'][\'logo\'] = \'application/views/images/logo.jpg\';' . "\n\n");
    fwrite($fp, '$CONFIG[\'project\'][\'default_phases\'] = \'Planning, Design, Development, Testing\';' . "\n\n");
    fwrite($fp, '$CONFIG[\'invoice\'][\'base_invoice_number\'] = 201000;' . "\n");
    fwrite($fp, '$CONFIG[\'invoice\'][\'default_terms\'] = \'All totals are final and non-negotiable. Payments must be made by the specified due date with no exceptions. Mailed checks must be postmarked by the due date above.\';' . "\n\n");
    fwrite($fp, '$CONFIG[\'uploads\'][\'path\'] = ROOT . \'/ds/\';' . "\n");
    fwrite($fp, '$CONFIG[\'uploads\'][\'web_path\'] = $CONFIG[\'base_url\'] . \'ds/\';' . "\n");
    fwrite($fp, '$CONFIG[\'uploads\'][\'max_file_size\'] = 200000000;' . "\n");
    fwrite($fp, '$CONFIG[\'uploads\'][\'allow_client_uploads\'] = true;' . "\n");
    fwrite($fp, "\n\n?>");
    echo "<div class='success'>Config File Created</div>";

    final_text();
}

function final_text()
{
    echo '<br/><br/>
                        <h1>Installation Complete!</h1>
                        <h3>Login: Admin, Password: -leave blank-</h3>

                            <p>You will need to update information about your company (i.e. name, address, phone, etc
                            direcctly in the config file. These values are used by the invoice generation script. </p>

                        <h5 style="color:#cc0000">It is important that you delete the install folder once the app is
                            functioning properly.</h5>
                        <h5 style="color:#cc0000">It is important that you set the chmod value of the config folder back to
                            its original value.</h5>';
}


function test_db_connection($address, $account, $pwd)
{
    $dbHandle = @mysql_connect($address, $account, $pwd);

    if ($dbHandle != 0)
    {
        @session_start();
        $_SESSION['host'] = $address;
        $_SESSION['user'] = $account;
        $_SESSION['pass'] = $pwd;

        echo '1<div class="error_list">' . "<div  class='success'>Connection suceeded. Click Next.</div></div>";
    }
    else
    {
        echo '0<div class="error_list">' . "<div class='error'>Unable to connect to database. Re-check values</div></div>";
    }
}


?>