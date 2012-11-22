<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 24, 2010
 * Time: 12:03:44 AM
 */

class FilesController extends Controller
{
    function add($project)
    {
        if ($user = $this->authorized(USER))
        {
            global $CONFIG;
            if (!$user['group_id'] == 0 && !$CONFIG['uploads']['allow_client_uploads'])
            {
                return false;
            }

            $this->loadModel('Project');
            $project_details = $this->Project->get_details($project);

            /**
             * Make sure client has access to this project
             */
            if ($user['group_id'] != 0)
            {
                if ($user['id'] != $project_details['client_id'])
                {
                    return false;
                }
            }

            $descriptive_names = array(
                'description' => 'Name',
                'document' => 'File',
                'phase' => 'Phase'
            );

            $rules = array(
                'description' => 'clean',
                'document' => 'required|clean',
                'phase' => 'clean'
            );

            $this->loadLibrary('form.class');
            $form = new Form();

            $this->loadModel('Project');
            $form->dependencies['phases'] = $this->Project->get_phases($project);
            $form->dependencies['project'] = $project;
            $form->dependencies['title'] = 'New File';
            $form->dependencies['form'] = 'application/views/new-file.php';

            $form->form_fields = $_POST;

            $form->form_fields['document'] = isset($_FILES['document']['name']) ? $_FILES['document']['name'] : null;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'form';
            $form->rules = $rules;

            if ($fields = $form->process_form())
            {
                $rel_project = $this->Project->get_details($project);
                $rel_client_id = $rel_project['client_id'];

                $this->loadModel('Client');
                $uploaded_by = $this->Client->get_details($user['id']);
                $this->loadModel('File');

                /**
                 * Add the document to the project
                 */
                if ($file_id = $this->File->add_file($rel_client_id, $fields['description'], $project, $fields['phase'], $uploaded_by))
                {

                    $this->alert("File Uploaded Successfully", "success");

                    $this->loadModel('Email');
                    $this->Email->send_notification($user['id'], 'file', $file_id, array('description' => $fields['description']));

                    $this->redirect('projects/view/' . $project);
                }
                else
                {
                    $this->alert("There was an error uploading the file", "error");
                    $this->redirect('projects/view/' . $project);
                }
            }
        }
    }

    function link($project_id, $link_id = null)
    {
        if ($user = $this->authorized(USER))
        {
            global $CONFIG;
            if (!$user['group_id'] == 0 && !$CONFIG['uploads']['allow_client_uploads'])
            {
                return false;
            }

            $this->loadModel('Project');
            $project_details = $this->Project->get_details($project_id);

            /**
             * Make sure client has access to this project
             */
            if ($user['group_id'] != 0)
            {
                if ($user['id'] != $project_details['client_id'])
                {
                    return false;
                }
            }

            $descriptive_names = array(
                'description' => 'Description',
                'path' => 'URL',
                'phase' => 'Phase'
            );

            $rules = array(

                'description' => 'required',
                'path' => 'required|valid_url',
                'phase' => 'clean'
            );


            $this->loadLibrary('form.class');
            $form = new Form();

            //If this is an edit, $item_id will be populated
            if ($link_id != null)
            {

                $form->is_edit = true;
                $form->is_edited = (!isset($_POST['is_edited'])) ? false : true; //is_edited will be set as post variable if edit form has been submitted
                $form->dependencies['link_id'] = $link_id;
                if ($form->is_edit && !$form->is_edited)
                {
                    /**
                     * Get fields from the db if this is the first time edit
                     * form has been submitted
                     */
                    $this->loadModel('File');
                    $fields = $this->File->get_details($link_id);
                }
                else
                {
                    //get fields from post if edit form was submitted already
                    $fields = $_POST;
                }
            }

            $form->dependencies['phases'] = $project_details['phases'];
            $form->dependencies['title'] = ($link_id == null) ? 'New Link' : 'Edit Link';
            $form->dependencies['form'] = 'application/views/new-link.php';
            $form->dependencies['project_id'] = $project_id;
            $form->dependencies['link_id'] = $link_id;
            $form->form_fields = (!$form->is_edit) ? $_POST : $fields;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'form';
            $form->rules = $rules;

            if ($fields = $form->process_form())
            {
                $this->loadModel('File');

                if (!$form->is_edit)
                {
                    $result = $this->File->add_link($fields, $project_details, $user['id']);
                }
                else
                {
                    $result = $this->File->edit($fields, "id = '$link_id'");
                }

                if ($result)
                {
                    $this->redirect('projects/view/' . $project_id);
                }
                else
                {
                    $this->alert('Error processing link', 'error');
                    $this->redirect("projects/view/$project_id");
                }
            }
        }
    }

    function edit_link($project_id, $link_id)
    {
        $this->link($project_id, $link_id);
    }

    function edit($file_id)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $descriptive_names = array(
                'description' => 'Name',
                'document' => 'File',
                'phase' => 'Phase'
            );

            $rules = array(
                'description' => 'clean',
                'document' => 'required|clean',
                'phase' => 'clean'
            );

            $this->loadLibrary('form.class');
            $form = new Form();

            $form->is_edit = true;
            $form->is_edited = (!isset($_POST['is_edited'])) ? false : true; //is_edited will be set as post variable if edit form has been submitted

            $this->loadModel('File');
            $file = $this->File->get_details($file_id);

            $form->form_fields = (!empty($_POST)) ? $_POST : $file;
            $form->descriptive_names = $descriptive_names;

            $form->rules = $rules;

            $this->loadModel('Project');
            $form->dependencies['phases'] = $this->Project->get_phases($file['project']);
            $form->dependencies['title'] = 'Edit File';
            $form->dependencies['form'] = 'application/views/new-file.php';
            $form->view_to_load = 'form';

            if ($form->process_form())
            {
                $this->File->edit($form->form_fields, "id = '$file_id'", 'files');
                $this->alert('File Edited', 'success');
                $this->redirect('projects/view/' . $file['project']);
            }
        }
    }

    function view($file_id)
    {
        if ($user = $this->authorized(USER))
        {

            if (empty($file_id))
            {
                $this->alert('Invalid File id');
                $this->redirect('projects/get');
            }

            $this->loadModel('File');

            if (!$this->File->authorized_to_download($file_id, $user))
            {
                return false;
            }

            $file = $this->File->get_details($file_id);
            $type = $this->File->get_type($file['file_type']);

            $actions = array();
            if ($type != 'website')
            {
                $actions[] = array('name' => 'Download', 'link' => "index.php?a=files/download/$file_id");
            }
            else
            {
                $actions[] = array('name' => 'Visit Site', 'link' => $file['path']);
            }

            $admin_actions = array(
                array('name' => 'Delete', 'link' => "index.php?a=files/delete/$file_id", 'class' => 'danger'),
                array('name' => 'Edit', 'link' => "index.php?a=files/edit" . (($type == 'website') ? '_link/' . $file['project'] : '') . "/$file_id")
            );

            $this->loadModel('Message');

            $this->set('messages', $this->Message->get_messages('file', $file_id, null, 5, "created DESC"));
            $this->set('user', $user);
            $this->set('file', $file);
            $this->set('item_id', $file_id);
            $this->set('actions', $actions);
            $this->set('admin_actions', $admin_actions);
            $this->set('type', $type);
            $this->set('breadcrumbs', array(
                array($file['project_name'], 'index.php?a=projects/view/' . $file['project']),
                array('Projects', 'index.php?a=projects/get'),
                array($file['client_name'], $this->redirect('clients/view/' . $file['client_id'], true))
            ));
            $this->set('main_content', 'application/views/view-file.php');
            $this->set('sidebar_content', 'application/views/sidebar-messages.php');

            $this->loadView('split-page');
        }
    }

    function download($file_id, $is_thumb = false)
    {
        /** Check that the user is logged in **/

        if ($user = $this->authorized(USER))
        {
            /** Make sure the document is associated
             * with the user account or the user is an
             * admin
             */
            $this->loadModel('File');
            $authorized = $this->File->authorized_to_download($file_id, $user, $is_thumb);

            if (!$authorized)
            {
                return false;
            }

            //If $is_thumb, the file_id will be returned in authorzed func call
            if ($is_thumb)
            {
                $file_id = $authorized;
            }


            /** Turn off error reporting to avoid
             * document getting corrupted
             */

            error_reporting(0);


            /**
             * Verify file exists then send to
             * browser
             */
            global $CONFIG;
            $file = $this->File->get_details($file_id);
            $file = $CONFIG['uploads']['path'] .
                    (($is_thumb) ? 'thumbs/' : '') . $file['path'];

            if (file_exists($file))
            {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                exit;
            }
            else
            {
                $this->alert("The requested file does not exist", "error");
                $this->redirect('projects/view/' . $file['project']);
            }
        }
    }

    function delete($file_id)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $this->loadModel("File");
            $data = $this->File->get_details($file_id);

            if (!isset($_POST['is_confirmed']))
            {
                $data = array('Name' => $data['description'], 'Project' => $data['project_name'], 'Uploded by' => $data['uploaded_by'], 'Created' => date("F j, Y - g:i a", $data['created']));
                $this->confirm_delete($file_id, $data);
            }
            else
            {
                $this->File->delete($file_id);
                $this->alert("File Deleted", "notice");
                $this->redirect('projects/view/' . $data['project']);
            }
        }
    }
}

?>