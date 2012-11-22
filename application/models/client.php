<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

/**
 * Handles all logic related to a
 * specific client
 */

class Client extends Model
{
    /**
     * Create a new client by creating a profile in 
     * the clients table, as well as authentication
     * information in the users table
     */
    function new_client($name, $contact_person, $contact_email, $contact_number, $new_admin)
    {
        $created = time();
        $this->loadModel('UserAuthentication');

        /**
         * Verify that there isnt already a client
         * with the desired email address
         * then create entries in user and client tables
         */
        if ($this->UserAuthentication->username_available($contact_email))
        {
            
			if($new_admin != true)
            {
            	$group = 1;
            }
            else 
			{
				$group = 0;
			}

			$result = $this->query("INSERT INTO clients (admin_id, group_id, name, contact_person, contact_email, contact_phone, created, modified) VALUES ('" .$_SESSION['auth_id']. "', '$group', '$name', '$contact_person', '$contact_email', '$contact_number', '$created', '$created')");

            if ($result == true)
            {
                $temp_password = substr(uniqid(), -6, 6);
                $user_id = mysql_insert_id();
                $result = $this->UserAuthentication->register($user_id, $temp_password,
                    1);

                if ($result)
                {
                   return array('id'=>$user_id, 'temp_password'=>$temp_password);
                } 
				else
                    return false;
            } 
			else
                return false;
        } 
		else
            return false;

    }

    /**
     * retrieve the client's temporary
     * password from the user table
     */
    function get_temp_pass($client_id)
    {
        $pass = $this->query("SELECT tmp_pass FROM users WHERE client_id = '$client_id'");
        $pass = $pass[0]['tmp_pass'];

        return $pass;
    }


    /**
     * Get profile information for a
     * specific client
     */
    function get_details($client_id)
    { 
        $client = $this->query("SELECT * FROM clients WHERE id ='$client_id'");
		
        $client = (isset($client[0]))?$client[0]:false;

        return $client;
    }

    /**
     * Get all clients in the db
     */
    function get_all()
    {
        $clients = $this->query("SELECT * FROM clients WHERE admin_id='" .$_SESSION['auth_id']. "'");
		
        return $clients;
    }
    
    
    function delete($id)
    {
    	$this->query("DELETE FROM clients WHERE id = '$id'");
    	
    	$this->query("DELETE FROM users WHERE client_id = '$id'");
		
		$this->query("DELETE FROM projects WHERE client_id = '$id'");
		
		$this->query("DELETE FROM messages WHERE posted_by = '$id'");
    	
    	$this->query("DELETE FROM files WHERE client_id = '$id'");

        //TODO: Unset each of the client's documents
    	
    	return true;
    }
}

?>