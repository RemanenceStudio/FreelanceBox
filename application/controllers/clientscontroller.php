<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

/**
 * Controller for the client
 * object
 */
class ClientsController extends Controller
{

    function create($client_id = null)
    {
        $this->edit($client_id);
    }

    /**
     * Allows a user to change their password
     */
    function change_password()
    {
        if ($user = $this->authorized(USER))
        {
            $descriptive_names = array(
                'current_password' => 'Current Password',
                'new_password' => 'New Password',
                'new_password_confirm' => 'Confirm Password'
            );

            $rules = array(
                'current_password' => ($user['group_id']==0)?'clean':'required|clean',
                'new_password' => 'required|clean',
                'new_password_confirm' => 'required|clean'
            );

            $this->loadLibrary('form.class');
            $form = new Form();

            $form->dependencies['title'] = "Change Password";
            $form->dependencies['form'] = 'application/views/change-password.php';
            $form->dependencies['admin_reset'] = false;
            $form->dependencies['client_id'] = $user['id'];
            $form->form_fields = $_POST;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'form';
            $form->rules = $rules;

            if ($fields = $form->process_form())
            {

                $this->loadModel('UserAuthentication');


                if ($this->UserAuthentication->change_password($user['id'], $fields['current_password'], $fields['new_password'], $fields['new_password_confirm']))
                {
                    $this->alert("Password Updated", "success");
                    $this->redirect("portal/home");
                }
                else
                {
                    $this->alert("Error: Password Not Updated", "error");
                    $this->redirect("portal/home");
                }
            }
        }
    }


    /**
     * Display login information for new clients
     */
    function credentials($client_id, $email = false)
    {
        if (!$this->authorized(ADMIN))
            return false;

        $this->loadModel("Client");

        $temp_pass = $this->Client->get_temp_pass($client_id);
        $client_details = $this->Client->get_details($client_id);

        $password = (empty($temp_pass)) ? "- private -" : $temp_pass;
        $this->set('password', $password);
        $this->set('details', $client_details);

        $this->set('title', 'Login Details');
        $this->set('form', 'application/views/credentials.php');

        if ($email)
        {
            $this->email_credentials($client_id, $client_details['contact_email'], $temp_pass);
            $this->alert('Login Information Sent');
            $this->redirect("clients/view/$client_id");
        }
        $this->loadView('form');
    }


    /**
     * Welcome Email
     */
    function email_credentials($client_id, $username = null, $password = null)
    {
        if (!$user = $this->authorized(ADMIN))
            return false;

        $this->loadModel('Email');

        $object = array(
            'contact_email' => $username,
            'password' => $password);

        $this->Email->send_notification($user['id'], 'credentials', $client_id, $object);
    }


    function edit($client_id = null)
    {
		
		if ($user = $this->authorized(ADMIN))
        {

            $verif_view = $client_id;
			
			if ($client_id == 'myprofile')
            {
                $client_id = $user['id'];
            }

            if ($client_id == 'admin')
            {
                $new_admin = true;
                $client_id = null; //set client_id back to null so script doesn't think this is an edit
            }
            else $new_admin = false;


            $descriptive_names = array(
				'name' => 'Name',
                'contact_person' => 'Contact Person',
                'contact_email' => 'Email',
                'contact_phone' => 'Phone Number'
            );

            $rules = array(
				'name' => 'required|clean',
                'contact_person' => 'required|clean',
                'contact_email' => 'required|valid_email|clean',
                'contact_phone' => 'clean'
            );

            if ($new_admin)
            {
                unset($rules['contact_person']);
                unset($rules['contact_phone']);
            }

            $this->loadLibrary('form.class');
            $form = new Form();

            //If this is an edit, $item_id will be populated
            if ($client_id != null)
            {
								
                $form->is_edit = true;
                $form->is_edited = (!isset($_POST['is_edited'])) ? false : true; //is_edited will be set as post variable if edit form has been submitted
                $form->dependencies['client_id'] = $client_id;
                if ($form->is_edit && !$form->is_edited)
                {
                    /**
                     * Get fields from the db if this is the first time edit
                     * form has been submitted
                     */
                    $this->loadModel('Client');
                    $fields = $this->Client->get_details($client_id);
                }
                else
                {
                    //get fields from post if edit form was submitted already
                    $fields = $_POST;
					
					// Upload image if not empty
					if( isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '' ) {
						
						// Set connect db
						include('core/conlog.php');
						
						// Set new filename and upload file
						$file = $_FILES['logo']['name'];
						$ext = explode('.', $file);
						$ext = $ext[1];
						$newFilename = uniqid();
						$newFilename .= '.' . $ext;
						$dir = "ds/logo/";
						move_uploaded_file($_FILES['logo']['tmp_name'], $dir . $newFilename);
						
						// Get old file and delete
						$sql = "SELECT `logo` FROM `clients` WHERE `id` = " . $client_id;
						$rs = mysql_query($sql);
						$row = mysql_fetch_array($rs);
						@unlink($dir . $row['logo']);
						
						// Update table clients						
						$sql = "UPDATE `clients` SET `logo` = '" . $newFilename . "' WHERE `id` = " . $client_id;
						$rs = mysql_query($sql);
						
						//die();
						
					}
					
					/*echo '<pre>';
					print_r($_FILES);
					print_r($_POST);
					print_r($_GET);
					echo '</pre>';
					die('<br> STOP');*/
                }
            }
			
			if($verif_view == 'myprofile') {
            	$form->dependencies['title'] = ($client_id == null) ? (!$new_admin) ? 'New Client' : 'New Admin' : 'Edit Profil';
			}
			else {
				$form->dependencies['title'] = ($client_id == null) ? (!$new_admin) ? 'New Client' : 'New Admin' : 'Edit Client';	
			}
            $form->dependencies['new_admin'] = $new_admin;
            $form->dependencies['form'] = 'application/views/new-client.php';
            $form->dependencies['client_id'] = $client_id;
            $form->form_fields = (!$form->is_edit) ? $_POST : $fields;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'form';
            $form->rules = $rules;
			
            if ($fields = $form->process_form())
            {
                $this->loadModel('Client');

                if (!$form->is_edit)
                {
                    $this->loadModel('UserAuthentication');

                    if ($this->UserAuthentication->username_available($fields['contact_email']))
                    {
                        if ($result = $this->Client->new_client($fields['name'], $fields['contact_person'], $fields['contact_email'], $fields['contact_phone'], $new_admin))
                        {
                            $this->alert('Client Successfully Added', 'success');

                            $this->loadModel('Email');

                            $email = array(
                                'id' => $result['id'],
                                'contact_email' => $fields['contact_email'],
                                'temp_password' => $result['temp_password']
                            );

                            $this->Email->send_notification($user['id'], 'welcome', $result['id'], $email);
                        }
                    }
                    else
                    {
                        $this->alert('Client already exists', 'error');
                        $this->redirect("clients/get");
                    }
                }
                else
                {					
					$result = $this->Client->edit($fields, "id = '$client_id'");
                    $this->alert('Client edited', 'success');
                    $result = array();
                    $result['id'] = $client_id;
                }

                if ($result)
                {
                    $this->redirect('clients/view/' . $result['id']);
                }
                else
                {
                    $this->alert('Error processing client', 'error');
                    $this->redirect("clients/get");
                }
            }
        }
    }


    /**
     * Deletes a client and all associated
     * data
     */

    function delete($client_id)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $this->loadModel("Client");
            $data = $this->Client->get_details($client_id, false);

            if (!isset($_POST['is_confirmed']))
            {
                $data = array('Name' => $data['name'], 'Contact Person' => $data['contact_person'], 'Email' => $data['contact_email'], 'Phone' => $data['contact_phone'], 'Created' => date("F j, Y - g:i a", $data['created']));
                $this->confirm_delete($client_id, $data);
            }
            else
            {
                $this->Client->delete($client_id);
                $this->alert("Client Deleted", "notice");
                $this->redirect('clients/get/');
            }
        }
    }

    function get($filter = 'all', $page = 1)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $this->loadModel('Client');

            switch ($filter)
            {
                case 'all':
                    $data = $this->Client->paginate($page, 10, 'group_id = 1 AND admin_id = ' . $_SESSION['auth_id']);
                    $columns = array(
                        'name' => 'Name',
                        'contact_person' => 'Contact Person',
                        'contact_phone' => 'Phone',
                        'contact_email' => 'Email');

                    $details_link = "index.php?a=clients/view/";
                    $details_id_field = 'id';
                    $actions = array('Edit[modal]' => 'index.php?a=clients/edit/', 'Delete[modal]' => 'index.php?a=clients/delete/');
                    break;
            }

            $object_actions = ($user['group_id'] == 0) ? array(
                'New Client[modal]' => $this->redirect('clients/create', true)
            ) : null;
			
			/*echo '<pre>';
			print_r($data);*/

            $base = "index.php?a=clients/" . __FUNCTION__ . "/$filter/";
            $this->set('user', $user);
            $this->set('base', $base);
            $this->set('tab', 'clients');
            $this->set('object_actions', $object_actions);
            $this->set('actions', $actions);
            $this->set('details_link', $details_link);
            $this->set('details_id_field', $details_id_field);
            $this->set('columns', $columns);
            $this->set('data', $data);
            $this->loadView('list');
        }
    }

    function view($client_id)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $this->loadModel('Client');
            $client = $this->Client->get_details($client_id);

            if (!$client)
            {
                $this->alert('Client doesn\'t exist');
                $this->redirect('clients/get');
            }

            $this->set('client', $client);

            $this->loadModel('Project');
            $this->set('projects', $this->Project->get_projects_by_client($client_id));

            $this->loadModel('Invoice');
            $this->set('invoices', $this->Invoice->get_invoices_by_client($client_id));


            $this->set('breadcrumbs', array(array($client['name'], 'null'), array('Clients', 'index.php?a=clients/get')));
            $this->loadView('client-profile');
        }
    }


    function reset($client_id)
    {
        if ($user = $this->authorized(ADMIN))
        {

            $descriptive_names = array(
                'new_password' => 'New Password',
                'new_password_confirm' => 'Confirm Password'
            );

            $rules = array(
                'new_password' => 'required|clean',
                'new_password_confirm' => 'required|clean'
            );

            $this->loadLibrary('form.class');
            $form = new Form();

            $form->dependencies['title'] = "Reset Password";
            $form->dependencies['form'] = 'application/views/change-password.php';
            $form->dependencies['admin_reset'] = true;
            $form->dependencies['client_id'] = $client_id;
            $form->form_fields = $_POST;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'form';
            $form->rules = $rules;

            if ($fields = $form->process_form())
            {
                $this->loadModel('UserAuthentication');

                if($this->UserAuthentication->admin_pass_reset($fields['new_password'], $fields['new_password_confirm'], $client_id))
                {
                    $this->loadModel('Client');
                    $client_details = $this->Client->get_details($client_id);

                    $this->email_credentials($client_id, $client_details['contact_email'], $fields['new_password']); 

                    $this->alert("Password updated and the client has been notified", "success");
                    $this->redirect("clients/view/$client_id");   
                }
                else
                {
                    $this->alert('There was an error. Please try again', 'error');
                    $this->redirect("clients/view/$client_id");   
                }


            }
        }
    }
}

?>