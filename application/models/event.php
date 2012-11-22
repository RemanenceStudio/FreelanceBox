<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 14, 2010
 * Time: 10:40:39 PM
 */

class Event extends Model
{

    function set_alert($msg, $type = null, $current_user, $reference_object = null, $reference_id = null, $reference_function = null)
    {
        if ($reference_function == null)
        {
            $reference_function = debug_backtrace();
            if (isset($reference_function[1]))
            {
                $reference_function = $reference_function[1]['class'] . "-" . $reference_function[1]['function'];
            }
        }

        $target_users = Event::get_targets($current_user, $reference_object, $reference_id);

        foreach($target_users as $target_user)
        {
            Model::query("INSERT INTO alerts (target_user, reference_function, reference_object, reference_id, msg, type) VALUES ('$target_user', '$reference_function', '$reference_object', '$reference_id', '$msg', '$type')");
        }
    }

    function clear_alert($alerts)
    {
        if (!is_array($alerts))
        {
            $alerts[] = $alerts;
        }

        foreach ($alerts as $alert)
        {
            Model::query("DELETE from alerts WHERE id = '$alert");
        }
    }

    function get_targets($initiated_by, $reference_object, $reference_id)
    {

        $targets = array();

        $result = $this->query("SELECT * FROM $reference_object" . "s WHERE id = '$reference_id'");
        $result = (isset($result[0])) ? $result[0] : false;

        if (!$result)
            return false;

        $client = null;

 /*       switch ($reference_object)
        {
            case 'file':
                $client = $result['client_id'];
                break;
            case 'invoice':
                $client = $result['client_id'];
                break;
            case 'project':
                $client = $result['client_id'];
                break;
            case 'message':
                break;
        }*/

        $client = $result['client_id'];
        
        if ($initiated_by == $client)
        {
            $admins = $this->query("SELECT id FROM clients WHERE group_id = 0");

            foreach ($admins as $target)
            {
                $targets[] = $target['id'];
            }
        }
        else
        {
            $targets[] = $client;
        }
     
        return $targets;
    }

   

    function get_alerts($user_id, $column = 'reference_function')
    {
        $result = $this->query("SELECT * FROM alerts WHERE target_user = '$user_id'");

        $alerts = array();
        foreach($result as $alert)
        {
            $alerts[$alert[$column]][] = $alert;
        }

        return $alerts;
    }
}

?>