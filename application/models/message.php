<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

/**
 * The Message Model handles all the business logic for
 * creating and displaying messages
 */

class Message extends Model
{
    /**
     * Create a new message
     *
     */
    function create($posted_by, $reference_object, $reference_id, $message)
    {

        $timestamp = time();

        $client = $this->trace($reference_object, $reference_id);
        $client = $client['client_id'];

        $this->query("INSERT INTO messages (admin_id, client_id, reference_object, reference_id, posted_by, message, created) VALUES ('" .$_SESSION['auth_id']. "', '$client', '$reference_object','$reference_id', '$posted_by', '$message', '$timestamp')");

        return mysql_insert_id();
    }




    function trace($reference_object, $reference_id)
    {
        if (!empty($reference_object))
        {
            $result = $this->query("SELECT * FROM $reference_object" . "s WHERE id = '$reference_id'");
            $result = (isset($result[0])) ? $result[0] : false;

            if (!$result)
                return false;
        } else return false;
        
        $client = null;
        $project = null;

        switch ($reference_object)
        {
            case 'file':
                $client = $result['client_id'];
                $project = $result['project'];
                break;
            case 'invoice':
                $client = $result['client_id'];
                break;
            case 'project':
                $client = $result['client_id'];
                break;
        }

        return array('client_id' => $client, 'project_id' => $project);
    }

    function get_messages($reference_object = null, $reference_id = null, $current_page, $per_page, $order = null)
    {

        $current_page = ($current_page == null) ? 1 : $current_page;
        $per_page = ($per_page == null) ? 10 : $per_page;
        $where = '';

        if ($reference_object != null)
        {
            $where = " reference_object = '$reference_object'";

            if ($reference_id != null)
            {
                $where .= " AND reference_id = '$reference_id' AND admin_id='" .$_SESSION['auth_id']. "'";
            }
        }

        $messages = $this->paginate($current_page, $per_page, $where, $order, null,
            'messages.id, messages.message, messages.reference_object, messages.reference_id, messages.created, clients.name',
            'LEFT JOIN clients ON messages.posted_by = clients.id');

        return $messages;
    }

   

    function get_user_messages($page = 1, $per_page = 10, $user)
    {
        $where = ($user['group_id'] != 0)
                ? "client_id = '" . $user['id'] . "'" : null;

        $messages = $this->paginate($page, $per_page, $where, null, null,
            'messages.id, messages.message, messages.reference_object, messages.reference_id, messages.created, clients.name',
            'LEFT JOIN clients ON messages.posted_by = clients.id');

        return $messages;
    }

    function get_details($message_id)
    {
        $message = $this->query("SELECT messages.id, messages.message, messages.reference_object, messages.reference_id, messages.created, clients.name
                    FROM messages LEFT JOIN clients ON messages.posted_by = clients.id WHERE messages.id = '$message_id'");

        $message = isset($message[0])?$message[0]:false;

        return $message;
    }

    function delete($message_id)
    {
        $this->query("DELETE FROM messages WHERE id = '$message_id'");
    }
}

?>