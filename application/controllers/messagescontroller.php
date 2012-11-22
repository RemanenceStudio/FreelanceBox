<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

class MessagesController extends Controller
{

    function add($reference_object, $reference_id)
    {
        if ($user = $this->authorized(USER))
        {
            $descriptive_names = array(
                'message' => 'Message Body'
            );

            $rules = array(
                'message' => 'required|clean'
            );

            $this->loadLibrary('form.class');
            $form = new Form();

            $form->form_fields = $_POST;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'new-message';
            $form->rules = $rules;

            if ($fields = $form->process_form())
            {
                $this->loadModel('Message');
                $mid = $this->Message->create($user['id'], $reference_object, $reference_id, $fields['message']);

                $this->loadModel('Email');
             
                $this->Email->send_notification($user['id'], 'message', $mid, array('message'=>$fields['message']));

                $this->redirect('reload');
            }
        }
    }

    function get($filter = 'all', $page = 1)
    {

        if ($user = $this->authorized(USER))
        {


            switch ($filter)
            {
                case 'all':
                    $this->loadModel('Message');
                    $data = $this->Message->get_user_messages($page, 10, $user);
                    $columns = array(
                        'name' => 'From',
                        'message[list-message]' => 'Message',
                        'created' => 'Date'
                    );
                    $this->loadLibrary('formatdata.class');
                    $data['page'] = FormatData::format(array('created' => 'date[M j - g:i a]'), $data['page']);
                    $details_link = '';
                    $details_id_field = 'id';
                    $actions = ($user['group_id']==0)?array('Delete[modal]' => 'index.php?a=messages/delete/'):null;
                    break;
            }

            $base = $this->redirect("messages/" . __FUNCTION__ . "/$filter/",true);
            $this->set('user', $user);
            $this->set('base', $base);
            $this->set('tab', 'messages');
            $this->set('list_class', 'messages');
            $this->set('actions', $actions);
            $this->set('details_link', $details_link);
            $this->set('details_id_field', $details_id_field);
            $this->set('columns', $columns);
            $this->set('data', $data);
            $this->loadView('message-list');
        }
    }

    function delete($message_id)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $this->loadModel("Message");
            $data = $this->Message->get_details($message_id);

            if (!isset($_POST['is_confirmed']))
            {
                $data = array('Message' => $data['message'], 'Posted By' => $data['name'], 'Created' => date("F j, Y - g:i a", $data['created']));
                $this->confirm_delete($message_id, $data);
            }
            else
            {
                $this->Message->delete($message_id);
                $this->alert("Message Deleted", "notice");
                $this->redirect('messages/get/');
            }
        }
    }
}

?>