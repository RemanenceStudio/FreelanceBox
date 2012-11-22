<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

class Email extends Model
{
    function send_notification($initiated_by, $reference_object, $reference_id, $object)
    {
        if ($reference_object != 'welcome' && $reference_object != 'credentials')
        {
            $this->loadModel('Event');
            $targets = $this->Event->get_targets($initiated_by, $reference_object, $reference_id);
        }
        else
        {
            $targets[] = $reference_id;
        }


       $this->setup_email($reference_object, $targets, $object);
    }

    function setup_email($type, $targets, $object)
    {
        global $CONFIG;

        switch ($type)
        {
            case 'message':
                $subject = "A message has been posted on your account";
                $body = "The following message has been posted: \n\n" .
                        $object['message'] . "\n\n" .
                        "Please log in to reply.\n" .
                        $CONFIG['base_url'];
                break;
            case 'file':
                $subject = "A file has been uploaded on your account";
                $body = "The following file has been uploaded: \n\n" .
                        $object['description'] . "\n\n" .
                        "Please log in to download.\n" .
                        $CONFIG['base_url'];
                break;
            case 'invoice':
                $subject = "A new invoice has been created for your account";
                $body = "The following invoice has created for your account: \n\n" .
                        "Invoice Number: " . $object['invoice_number'] . "\n" .
                        "Due Date: " . date('M j, Y', Invoice::timestamp($object['due_date'])) . "\n\n" .
                        "Please log in to view the invoice.\n" .
                        $CONFIG['base_url'];
                break;
            case 'welcome':
                $subject = "Your new project with " . $CONFIG['company']['name'];
                $body = "You can track your project's status by logging into our client portal\n\n" .
                        "Your login information is:\n\n" .
                        "Username: " . $object['contact_email'] . "\n" .
                        "Password: " . $object['temp_password'] . "\n\n" .
                        $CONFIG['base_url'];
                break;
            case 'credentials':
                $subject = "Your password has been reset";
                $body = "Your new login information is: \n\n" .
                        "Username: " . $object['contact_email'] . "\n" .
                        "Password: " . $object['password'] . "\n\n" .
                        $CONFIG['base_url'];
                break;
        }


        foreach ($targets as $target)
        {
            $result = $this->query("SELECT contact_email FROM clients WHERE id = '$target'");

            if (isset($result[0]))
            {
                $this->send_email($result[0]['contact_email'], $CONFIG['company']['email'], $subject, $body);
            }
        }
    }

    function send_email($to, $from, $subject = null, $message = null)
    {

        if (!isset($to) || !isset($from))
        {
            return false;
        }

        if ($subject == null)
        {
            $subject = "There is new activity on your account";
        }

        if ($message == null)
        {
            $message = "Please log into your account to see new updates: \r\n\n $base_site_url";
        }
		
		$header = 	"MIME-Versin: 1.0\r\n" .
           			"Content-type: text/html; charset=UTF-8\r\n" .
            		"From: \"FreelanceBox \"<noReply@freelancebox.fr> \r\n" .
             		"Reply-to: \"FreelanceBox \"<noReply@freelancebox.fr> \r\n".
            		"X-Mailer: PHP";
		
        //if (mail($to, $subject, $message, "From: $from\r\nReply-To: $from\r\nReturn-Path: $from\r\n"))
		if (mail($to, $subject, $message, $header))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>