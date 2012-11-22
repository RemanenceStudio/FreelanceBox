<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: May 30, 2010
 * Time: 3:35:33 AM
 */

Class Form
{

    public $error_list;
    public $messages;
    public $has_errors;

    public $is_edit;
    public $is_edited;

    public $form_fields;
    public $descriptive_names;
    public $rules;
    public $view_to_load;
    public $dependencies;

    function __construct()
    {
        $this->error_list = '';
        $this->has_errors = false;
        $this->messages = $this->initialize_error_messages();
        $this->dependencies = array();
    }


    function process_form()
    {
        $empty = true;

        $form_fields = &$this->form_fields;
        $rules = $this->rules;
        $view_to_load = $this->view_to_load;
        $descriptive_names = $this->descriptive_names;
        $dependencies = $this->dependencies;
        $is_edit = $this->is_edit;
        $is_edited = $this->is_edited;

        if (!empty($form_fields))
        {
            foreach ($form_fields as $field)
            {
                if (!empty($field))
                {
                    $empty = false;
                }
            }
        }


        /*The view needs to know if this is an edit operation to set up
    * edit related variable is_edited*/
        if ($is_edit)
        {
            $dependencies['is_edit'] = true;
        }
        else $dependencies['is_edit'] = false;

        //set up form dependencies; 
        if (!empty($dependencies))
        {
            foreach ($dependencies as $key => $value)
            {
                Controller::set($key, $value);
            }
        }

        if ($empty)
        {
            Controller::loadView($view_to_load);
        }
        else
        {

            //remove rules that do not match any of the submitted fields
            foreach ($rules as $key => $value)
            {
                if (!isset($form_fields[$key]))
                    unset($rules[$key]);
            }

            //Make sure clean is in the rule list
            foreach ($form_fields as $key => $field)
            {
                $rules[$key] = trim((!isset($rules[$key])) ? 'clean' : ((in_array('clean', explode('|', $rules[$key]))) ? $rules[$key] : $rules[$key] . '|clean'), '|');
            }


            /** Perform validation on each of the fields **/
            foreach ($form_fields as $key => &$field)
            {
                $field = $this->validate($field, (isset($descriptive_names[$key])) ? $descriptive_names[$key] : $key, $rules[$key]);
            }

            /**
             * Load the form under either of the following conditions
             * 1. The form has errors
             * 2. This is an edit operation and the edit has not been performed yet
             */
            if ($this->has_errors == true || ($is_edit && !$is_edited))
            {
                foreach ($form_fields as $key => &$field)
                {
                    Controller::set($key, $field);
                }

                Controller::set('error_list', $this->error_list);

                Controller::loadView($view_to_load);
            }
            else
            {
                //remove unessary fields, otherwise edit operation will fail
                if (isset($this->form_fields['is_edited']))
                {
                    unset($this->form_fields['is_edited']);
                    unset($this->form_fields['submit']);
                    unset($this->form_fields['submit_x']);
                    unset($this->form_fields['submit_y']);
                }

                return $this->form_fields;
            }
        }
    }


    function pre($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }


    function validate($field, $fieldName, $rules)
    {

        $rules = explode('|', $rules);

        foreach ($rules as $rule)
        {
            $result = null;

            // Get the parameter (if exists) from the rule
            $param = false;
            if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
            {
                $rule = $match[1];
                $param = $match[2];
            }

            // Call the function that corresponds to the rule
            if ($rule == 'clean')
            {
                $field = $this->clean($field);
            }
            else if (method_exists($this, $rule))
            {
                $result = $this->$rule($field, $param);
            }
            else
            {
                trigger_error($rule . " is not a recognized validation rule", E_USER_WARNING);
            }

            // Did the rule fail?  If so, grab the error.
            if ($result === false)
            {
                $this->has_errors = true;
                //$this->error_list[$fieldName][$rule] = $this->messages[$rule];
                $this->error_list .= "<div class='error'>$fieldName: " . $this->messages[$rule] . "</div>";
            }
        }
        return $field;
    }


    function required($str, $val = false)
    {
        if (!is_array($str))
        {
            $str = trim($str);
            return ($str == '') ? false : true;
        }
        else
        {
            return (!empty($str));
        }
    }

    // --------------------------------------------------------------------


    function min_length($str, $val)
    {
        if (preg_match("/[^0-9]/", $val))
        {
            return false;
        }

        return (strlen($str) < $val) ? false : true;
    }

    // --------------------------------------------------------------------


    function max_length($str, $val)
    {
        if (preg_match("/[^0-9]/", $val))
        {
            return false;
        }

        return (strlen($str) > $val) ? false : true;
    }

    // --------------------------------------------------------------------


    function exact_length($str, $val)
    {
        if (preg_match("/[^0-9]/", $val))
        {
            return false;
        }

        return (strlen($str) != $val) ? false : true;
    }

    // --------------------------------------------------------------------


    function valid_email($str)
    {
        //return (!preg_match('/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/', $str)) ? false : true;
        return (!preg_match("/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD", $str)) ? false : true;
    }

    // --------------------------------------------------------------------


    function numeric($str)
    {
        return (!is_numeric($str)) ? false : true;
    }

    // --------------------------------------------------------------------


    //Validate the calendar date in MM/DD/YYYY format
    function valid_date($str)
    {
        return (!preg_match('#^((0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01])[- /.](19|20)?[0-9]{2})*$#', $str)) ? false : true;
    }

    // --------------------------------------------------------------------


    //Validate all 2-letter US State abbreviattions
    function valid_state($str)
    {
        return (!preg_match('/^(?:A[KLRZ]|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ADEINOST]|N[CDEHJMVY]|O[HKR]|PA|RI|S[CD]|T[NX]|UT|V[AT]|W[AIVY])*$/i', $str)) ? false : true;
    }

    // --------------------------------------------------------------------


    //Validate US ZIP Codes, with an optional 4 number ZIP code extension
    function valid_zip($str)
    {
        return (!preg_match('/^([0-9]{5}(?:-[0-9]{4})?)*$/', $str)) ? false : true;
    }

    // --------------------------------------------------------------------


    //Validate a 10-digit US phone number.
    //Separators are not required, but can include spaces, hyphens, or periods.
    function valid_phone($str)
    {
        return (!preg_match('/^(?:1(?:[. -])?)?(?:\((?=\d{3}\)))?([2-9]\d{2})(?:(?<=\(\d{3})\))? ?(?:(?<=\d{3})[.-])?([2-9]\d{2})[. -]?(\d{4})(?: (?i:ext)\.? ?(\d{1,5}))?$/', $str)) ? false : true;
    }

    // --------------------------------------------------------------------


    function valid_url($str)
    {
        //Most restrictive, requires www and http
        //return (!preg_match('/^(http|https|ftp):\/\/([\w]*)\.([\w]*)\.(com|net|org|biz|info|mobi|us|cc|bz|tv|ws|name|co|me)(\.[a-z]{1,3})?\z/i', $str)) ? false : true;

        //Less restrictive, doesn't require www, but requires http
        //return (!preg_match("/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", $str)) ? false : true;

        
        if (!preg_match("/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", $str))
        {
            if (!preg_match("/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", 'http://' . $str))
            {
                return false;
            }
            else return true;
        } else return true;
    }

    // --------------------------------------------------------------------


    //Passwords must be at least 6 characters long with one uppercase letter and one number
    function strong_password($str)
    {
        return (!preg_match('/^(?=^.{6,}$)((?=.*[A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z]))^.*$/', $str)) ? false : true;
    }

    // --------------------------------------------------------------------


    function alpha($str)
    {
        return (!preg_match("/^([a-z])+$/i", $str)) ? false : true;
    }

    // --------------------------------------------------------------------


    function min($str, $val)
    {
        return ($str >= $val) ? true : false;
    }

    // --------------------------------------------------------------------


    function max($str, $val)
    {
        return ($str <= $val) ? true : false;
    }

    // --------------------------------------------------------------------


    function alpha_numeric($str)
    {
        return (!preg_match("/^([a-z0-9])+$/i", $str)) ? false : true;
    }

    // --------------------------------------------------------------------


    function matches($str, $field)
    {
        return ($str !== $field) ? false : true;
    }

    // --------------------------------------------------------------------


    function clean($str)
    {
        $str = is_array($str) ? array_map('Form::clean', $str) : str_replace('\\', '\\\\', strip_tags(trim(htmlspecialchars((get_magic_quotes_gpc() ? stripslashes($str) : $str), ENT_QUOTES))));

        return $str;
    }

    // --------------------------------------------------------------------


    function initialize_error_messages()
    {
        include('formvalidator.errors.php');
        return $error_messages;
    }
}

?>
