<?php

/**
 * @author 23rd and Walnut
 * @copyright 2009
 */
class AuthStatus
{

//TODO: nonexistant record to group_id = 0,

    function logged_in()
    {
        @session_start();
        $user_agent = md5($_SERVER['HTTP_USER_AGENT']);
        $session_id = session_id();

        if (isset($_SESSION['auth']))
        {
            if ($_SESSION['auth'] == 1)
            {
                //Get session data from session table
                $db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
                mysql_select_db(DB_NAME, $db);
                $res = mysql_query("SELECT user_id, user_agent, last_activity FROM sessions WHERE session_id = '$session_id'", $db);

                $res = mysql_fetch_assoc($res);
                $db_user_agent = $res['user_agent'];

                //Determine if current user agent is the same as user agent in the db.
                if ($user_agent == $db_user_agent)
                {
                    //return success IFF user has been active within the last 30 minutes
 
                    $max_inactivity = 1800;
                    $last_activity = $res['last_activity'];

                    if ((time() - $last_activity) > $max_inactivity)
                    {
                        session_destroy();
                        $this->redirect('portal/login');
                        exit();
                    }
                    else
                    {
                        $user['id'] = $res['user_id'];
						$_SESSION['auth_id'] = $res['user_id'];

                        $res = mysql_query("SELECT group_id, name, contact_email, logo FROM clients where id = '" . $res['user_id'] . "'");
                        $res = mysql_fetch_assoc($res);
                        $user['group_id'] = $res['group_id'];
                        $user['email'] = $res['contact_email'];
                        $user['name'] = $res['name'];
						
						$_SESSION['name_id'] = $res['name'];
						$_SESSION['group_id'] = $res['group_id'];
						$_SESSION['logo_id'] = $res['logo'];

                        $ip_address = $_SERVER['REMOTE_ADDR'];
                        $last_activity = time();

                        $res = mysql_query("UPDATE sessions SET ip_address = '$ip_address', user_agent = '$user_agent', last_activity = '$last_activity' WHERE session_id = '$session_id'");

                        return $user;
                    }
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            return false;
        }
    }
}

?>