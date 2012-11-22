<?php

/**
 * @author 23rd and Walnut
 * @copyright 2009
 */

/**
 * The User Authentication class handles all
 * access related function such as login, 
 * check status, etc. This class validates sessions 
 * against the database.
 */

class UserAuthentication extends Model
{
    /**
     * Logs the user in
     */
    function login($username, $password)
    {
        /** If the username is available, the
         * user doesn't exist and the login fails
         */
        if ($this->username_available($username) == true)
        {
            return false;
        } 
		else
        {
            $result = $this->query("SELECT id FROM clients WHERE contact_email = '$username'");

            $client_id = $result[0]['id'];

            $result = $this->query("SELECT password, salt FROM users WHERE client_id = '$client_id'");

            $credentials = $result[0];

            if (!empty($credentials))
            {
                $salt = $credentials['salt'];
                $db_password = $credentials['password'];
                $input_password = $this->hash_password($password, $salt);

                /**
                 * If the hased input password matches the
                 * hashed password in the db, the login 
                 * succeed, otherwise it fails.
                 */
                if ($input_password == $db_password)
                {
                    return $client_id;
                } 
				else
                {
                    return false;
                }
            } 
			else
            {
                return false;
            }
        }
    }


    /**
     * Checks to see if a username exists in the db
     */
    public function username_available($username)
    {
        $result = $this->query("SELECT id from clients WHERE contact_email = '$username'");

        if (!empty($result))
        {
            return false;
        } else
            return true;
    }


    /**
     * Perform a one way hash on password
     * using the supplied salt
     */
    public function hash_password($password = false, $salt = false)
    {
        if ($password === false)
        {
            return false;
        }

        $pepper = '';
        $password = hash('sha256', $password . $salt . $pepper);

        return $password;
    }


    /**
     * Generate a random salt
     */
    public function salt()
    {
        return hash('sha256', uniqid(mt_rand(), true));
    }


    /**
     * Initiates a session for the user 
     * if the login has succeeded
     */
    function start_logged_in_session()
    {
        @session_start();

        //reset all session variables
        $_SESSION = array();

        //generate a new session id
       @session_regenerate_id();

        //set the ussers status to logged in
        $_SESSION['auth'] = 1;

        return session_id();
    }


    /**
     * Creates an entry in the sessions table
     * for a user session. All subsequent status 
     * checks are verified against this information
     */
    function save_session_data($session_id, $user_id, $username)
    {
        $user_agent = md5($_SERVER['HTTP_USER_AGENT']);
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $last_activity = time();

        $result = $this->query("SELECT * FROM sessions WHERE session_id = '$session_id'");

        if (empty($result))
        {
            $result = $this->query("INSERT INTO sessions (session_id, user_id, username, ip_address, user_agent, last_activity) VALUES ('$session_id', '$user_id', '$username', '$ip_address', '$user_agent', '$last_activity')");
        } else
        {
            $result = $this->query("UPDATE sessions SET ip_address = '$ip_address', user_agent = '$user_agent', last_activity = '$last_activity' WHERE session_id = '$session_id'");
        }
    }


    /**
     * Create the authentication details for 
     * a new user
     */
    function register($client_id, $password, $group_id)
    {
        $salt = $this->salt();
        $tmp_pass = $password;
        $password = $this->hash_password($password, $salt);

        $result = $this->query("INSERT INTO users (client_id, password, salt, tmp_pass) VALUES ('$client_id', '$password','$salt', '$tmp_pass') ");

        return $result;
    }

    /**
     * Allows a user to change thier password
     * in the users table
     */
    function change_password($client_id, $password, $new_password, $confirm_new_pass)
    {
        $result = $this->query("SELECT password, salt FROM users WHERE client_id = '$client_id'");

        $credentials = $result[0];

        if (!empty($credentials))
        {
            $salt = $credentials['salt'];
            $db_password = $credentials['password'];
            $input_password = $this->hash_password($password, $salt);

            /**
             * If the user entered the wrong value 
             * for current password, the change 
             * password request fails
             */
            if ($input_password != $db_password)
            {
                return false;
            }

            /**
             * Fail if new pass and confirm pass
             * do not match
             */
            if ($new_password != $confirm_new_pass)
            {
                return false;
            }

            /**
             * Salt new passowrd and update the
             * user table
             */
            $salt = $this->salt();
            $password = $this->hash_password($new_password, $salt);
            $result = $this->query("UPDATE users SET password = '$password',  salt = '$salt', tmp_pass='' WHERE  client_id = '$client_id' ");

            if ($result == true)
            {
                return true;
            } else
                return false;
        }

    }

    function admin_pass_reset($new_password, $new_pass_confirm, $client_id)
    {

        if($new_password != $new_pass_confirm)
        {
            return false;
        }
        $salt = $this->salt();
        $password = $this->hash_password($new_password, $salt);
        $result = $this->query("UPDATE users SET password = '$password',  salt = '$salt', tmp_pass='' WHERE  client_id = '$client_id' ");

        if ($result == true)
        {
            return true;
        } else
            return false;
    }

    /**
     * Logs a user out by destroying the session
     * and the session data in db
     */
    function logout()
    {
        @session_start();

        $session_id = session_id();

        $this->query("DELETE FROM sessions WHERE session_id ='$session_id'");

        @session_destroy();
    }

}

?>