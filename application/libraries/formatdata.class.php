<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

class FormatData
{
    /**
     * @param  $format_rules
     * @param  $data
     * @return
     * given a collection of object, apply formatting to field(s) in each individual object
     */
    function format($format_rules, $data)
    {
        if(!is_array($data))
            return $data;
        
        foreach ($format_rules as $field => $rule)
        {
            $param = false;
			if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
			{
				$rule	= $match[1];
				$param	= $match[2];
			}

            if (method_exists('FormatData', $rule))
            {
                foreach ($data as &$data_item)
                {
                    $data_item[$field] = FormatData::$rule($data_item[$field], $param);
                }
            }
        }
        return $data;
    }

//TODO: Should I put this all in the main function to avoid having to instantiate the object?
    function percentage($data)
    {
        return $data . "%";
    }

    function date($data, $format)
    {
       $format = ($format==null)?'m/d/Y':$format;
       return  date($format, $data);
    }

    function money($data)
    {
        //$format = ($format==null)?'%i.P0':$format;
        return '$' . number_format($data);
    }


}


?>