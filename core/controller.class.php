<?php

class Controller
{


    protected $_controller;
    protected $_action;
    public $profiler;
    public $user;

    function __construct()
    {
        $tmp = explode('Controller', get_class($this));
        $this->_controller = $tmp[0];
        $this->user = $this->logged_in();
    }

    /**
     * Instantiate an object for the
     * requested model
     */
    function loadModel($model)
    {
        $this->$model = new $model;
    }


    /**
     * Load the requested view file and
     * include/exclude headers based on
     * preferences
     */
    function loadView($view_file, $include_headers = true, $header = null, $footer = null)
    {

        @extract($this->variables);
        global $CONFIG;

        ob_start();

        if ($include_headers)
        {
            if (file_exists((ROOT . DS . 'application' . DS . 'views' . DS . 'header.php')))
            {
                include (ROOT . DS . 'application' . DS . 'views' . DS . 'header.php');
            }
        }


        include (ROOT . DS . 'application' . DS . 'views' . DS . $view_file . '.php');


        if ($include_headers)
        {
            if (file_exists(ROOT . DS . 'application' . DS . 'views' . DS . 'footer.php'))
            {
                include (ROOT . DS . 'application' . DS . 'views' . DS . 'footer.php');
            }
        }

        // end output buffering and send our HTML to the browser as a whole
        ob_end_flush();
    }

    /**
     * Load the requested library
     */
    function loadLibrary($library)
    {
        require_once (ROOT . DS . 'application' . DS . 'libraries' . DS . $library . '.php');
    }

    /**
     * Sets up variables for view file
     */
    function set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * Create an alert message to show to the user on the next page load
     */
    function alert($msg = "There was an error", $type = "notice")
    {
        @session_start();
        $_SESSION['alert'] = $msg;
        $_SESSION['alert_class'] = $type;
    }

    /**
     * redirect to the user to the requested location
     * if $return_link is true, the function will
     * return the url string rather than perform the
     * redirect
     */
    function redirect($controller_action, $return_link = false)
    {

        global $base_site_url;

        if ($controller_action == 'reload')
        {
            $controller_action = end(explode("=", $_SERVER['HTTP_REFERER']));
        }

        $link = $base_site_url . "index.php?a=" . $controller_action;

        //redirect to appropriate controller/action or return link
        if ($return_link == false)
        {
            header("Location: $link");
            exit();
        }
        else return $link;
    }


    /** Determine if the user has the appropriate
     * level of access
     */
    function authorized($level, $check_ownership_of = false)
    {
        if ($user = $this->logged_in())
        {
            if ($user['group_id'] <= $level)
            {
                if ($check_ownership_of)
                {
                    $model = explode('Controller', get_class($this));
                    $model = substr($model[0], 0, -1);

                    if (method_exists($model, 'owner'))
                    {
                        $this->loadModel($model);
                        $owner = $this->$model->owner($check_ownership_of, $user);

                        if ($owner)
                            return $user;
                        else
                        {
                            $this->redirect('portal/home');
                            exit;
                        }
                    }
                    else return false;
                }
                else return $user;
            }
            else $this->redirect('portal/home');
        }
    }

    /**
     * Determine if the user is logged in
     * and return the user object
     */

    //TODO: look at core interactions with libraries.
    function logged_in()
    {
        if (!class_exists('AuthStatus'))
        {
            $this->loadLibrary('authstatus.class');
        }

        if ($this->user = AuthStatus::logged_in())
        {
            return $this->user;
        }
        else
        {
            PortalController::login();
            exit;
            return false;
        }
    }


    function confirm_delete($id, $data = null)
    {
        $this->set("id", $id);
        $this->set('data', $data);
        $this->set('controller_action', $this->_controller . '/delete/' . $id);
        $this->set('title', 'Confirm Delete');
        $this->set('form', 'application/views/confirm-delete.php');
        $this->loadView('form');
    }

    function pre($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    function __destruct()
    {

    }
}
