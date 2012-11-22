<?php

class FormValidator 
{
	public $error_list;
	public $messages;
	public $has_errors;

    
	function __construct()
	{
		$this->error_list = '';
		$this->has_errors = false;
		$this->messages = $this->initialize_error_messages();
	}
		
	function run($field, $fieldName, $rules)
	{
		
		$rules = explode('|', $rules);
		
		foreach ($rules as $rule)
		{
			$result = null;
			
			// Get the parameter (if exists) from the rule
			$param = false;
			if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
			{
				$rule	= $match[1];
				$param	= $match[2];
			}
			
			// Call the function that corresponds to the rule
			if($rule == 'clean')
			{
				$field = $this->clean($field);
			}
			else if (method_exists($this, $rule))
			{
				$result = $this->$rule($field, $param);
			}
			else
			{
				//TODO: Implement $ignoreBadRules func
                //$this->error_list = array($rule => "$rule is not a recognized function");
				//return false;
			}
									
			// Did the rule fail?  If so, grab the error.
			if ($result === false)
			{
				$this->has_errors = true;
				//$this->error_list[$fieldName][$rule] = $this->messages[$rule];
				$this->error_list .= "<div class='error'>$fieldName: ".$this->messages[$rule]."</div>";
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
		return (!preg_match('/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/', $str)) ? false : true;
	}
	
	// --------------------------------------------------------------------
	
	
	
	function numeric($str)
	{
		return ( ! is_numeric($str)) ? false : true;
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
		return (!preg_match('/^(http|https|ftp):\/\/([\w]*)\.([\w]*)\.(com|net|org|biz|info|mobi|us|cc|bz|tv|ws|name|co|me)(\.[a-z]{1,3})?\z/i', $str)) ? false : true;
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
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? false : true;
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
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? false : true;
	}
	
	// --------------------------------------------------------------------
	
	
	
	                                                      													 
	function matches($str, $field)
	{
		return ($str !== $field) ? false : true;
	}
	
	// --------------------------------------------------------------------
	
	
	
	                                                      													 
	function clean($str)
	{
		$str = is_array($str) ? array_map('_clean', $str) : str_replace('\\', '\\\\', strip_tags(trim(htmlspecialchars((get_magic_quotes_gpc() ? stripslashes($str) : $str), ENT_QUOTES))));
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