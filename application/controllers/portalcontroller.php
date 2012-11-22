<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

/**
 * Loads the default view for
 * users and handles login, logout requests
 */

class PortalController extends Controller
{
    /**
     * Load the appropriate view for a
     * user
     */
    function home()
    {
        if ($user = $this->authorized(USER))
        {
            $this->loadModel('Client');
            $temp_pass = $this->Client->get_temp_pass($user['id']);

            if (strlen($temp_pass) > 0)
            {
                $this->alert("Please choose a permanent password - use the Actions menu", 'error');
            }

            ProjectsController::get();
        }
    }


    /**
     * Initiate login with user credentials
     */
    function login()
    {
        if (preg_match('/(?i)msie [1-6]/', $_SERVER['HTTP_USER_AGENT']))
        {
            $this->loadView('ie6');
            return false;
        }

        if (!isset($_POST['email']) && !isset($_POST['password']))
        {
            $this->set('title', 'Login');
            $this->set('form', 'application/views/login.php');
            $this->loadView('form');
        }
        else
        {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $this->loadModel('UserAuthentication');

            /**
             * Throw an error if the username isn't found
             */
            if ($this->UserAuthentication->username_available($email))
            {
                $this->alert('Username Or Password Incorrect', 'error');
                $this->redirect('portal/login');
            }
            else
            {
                /**
                 * If credentials are valid, start a logged in
                 * session
                 */
                if ($user_id = $this->UserAuthentication->login($email, $password))
                {
                    $session_id = $this->UserAuthentication->start_logged_in_session();

                    $this->UserAuthentication->save_session_data($session_id, $user_id, $email);

                    $this->redirect('portal/home');
                }
                else
                {
                    $this->alert('Username Or Password Incorrect', 'error');
                    $this->redirect('portal/login');
                }
            }
        }
    }


    /**
     * Log a user out
     */
    function logout()
    {
        $this->loadModel('UserAuthentication');

        $this->UserAuthentication->logout();

        $this->redirect('portal/login');
    }
}

?>