<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */


class ProjectsController extends Controller
{

    /**
     * Create a new project for a particular client
     */
    function start()
    {
        $this->edit();
    }

    function edit($project_id = null)
    {
        if ($this->authorized(ADMIN))
        {
            $descriptive_names = array(
                'name' => 'Project Name',
                'client_id' => 'Client',
                'duration' => 'Duration',
                'phases' => 'Phases');
            $rules = array(
                'name' => 'required|clean',
                'client_id' => 'required|clean',
                'duration' => 'required|numeric|clean',
                'phases' => '');


            $this->loadLibrary('form.class');
            $form = new Form();

            //If this is an edit, $project_id will be populated
            if ($project_id != null)
            {
                
				$form->is_edit = true;
                $form->is_edited = (!isset($_POST['is_edited'])) ? false : true; //is_edited will be set as post variable if edit form has been submitted

                if ($form->is_edit && !$form->is_edited)
                {
                    /**
                     * Get fields from the db if this is the first time edit
                     * form has been submitted
                     */
                    $this->loadModel('Project');
                    $fields = $this->Project->get_details($project_id);
                    $fields['phases'] = html_entity_decode(implode(',', $fields['phases']));
                }
                else
                {
                    //get fields from post if edit form was submitted already
                    $fields = $_POST;
                }
            }

            $this->loadModel('Client');
            $form->dependencies['title'] = ($project_id == null) ? 'Nouveau projet' : 'Editer Projet';
            $form->dependencies['form'] = 'application/views/project.php';
            $form->dependencies['clients'] = $this->Client->selectAll("WHERE `group_id` != 0 AND `admin_id` = " . $_SESSION['auth_id']);
            $form->dependencies['project_id'] = $project_id;
            $form->form_fields = (!$form->is_edit) ? $_POST : $fields;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'form';
            $form->rules = $rules;

            if ($fields = $form->process_form())
            {
                $this->loadModel('Project');

                if (!$form->is_edit)
                {
                    $result = $this->Project->new_project($fields['name'], $fields['client_id'], $fields['duration'], $fields['phases']);
                }
                else
                {
                    $result = $this->Project->edit($fields, "id = '$project_id'", 'projects');
                    $result = $project_id;
                }

                if ($result)
                {
                    $this->redirect('projects/view/' . $result);
                }
                else
                {
                    $this->alert('Error processing project', 'error');
                    $this->redirect('projects/get/all');
                }
            }
        }
    }


    /**
     * Allows a user to view the details of
     * a particular project
     */
    function view($project_id, $messages = false, $current_page = 1)
    {

        if ($user = $this->authorized(USER, $project_id))
        {
            $this->loadModel('Project');

            $project = $this->Project->get_details($project_id);

            if ($project)
            {

                $this->loadModel('File');
                $files = $this->Project->get_files_by_phase($project_id);
                //$this->pre($files);
                $this->set('user', $user);
                $this->set('data', $files);
                $this->set('tab', 'projects');
                $this->set('project', $project);
                $this->set('File', $this->File);
                $this->set('breadcrumbs', array(
                    array($project['name'], 'null'),
                    array('Projects', $this->redirect('projects/get', true)),
                    array($project['client_name'], $this->redirect('clients/view/' . $project['client_id'], true))
                ));


                if (!$messages)
                {
                    $this->loadView('grid');
                }
                else
                {
                    $object_actions = ($user['group_id'] == 0) ? array(
                        'Hide Messages[hide-messages]' => $this->redirect('projects/view/' . $project_id, true)
                    ) : null;

                    $this->set('messages', $this->Project->get_messages($project_id, $current_page));
                    $this->set('item_id', $project_id);
                    $this->set('object_actions', $object_actions);
                    $this->set('main_content', 'application/views/small-grid.php');
                    $this->set('sidebar_content', 'application/views/sidebar-messages.php');

                    $this->set('breadcrumbs', array(
                        array($project['name'], $this->redirect('projects/view/' . $project_id, true)),
                        array('Projects', $this->redirect('projects/get', true)),
                        array($project['client_name'], $this->redirect('clients/view/' . $project['client_id'], true))
                    ));
                    $this->loadView('split-page');
                }
            }
        }
    }


    function get($filter = 'all', $page = 1)
    {
        if ($user = $this->authorized(USER))
        {
            $this->loadModel('Project');

            switch ($filter)
            {
                case 'all':
                    $data = $this->Project->get_projects_by_page($page, 10, $user);
                    $columns = array(
                        'name' => 'Project Name',
                        'client_name' => 'Client',
                        'duration' => 'Projected End Date',
                        'progress' => 'Progress');
                    $this->loadLibrary('formatdata.class');
                    $data['page'] = FormatData::format(array('progress' => 'percentage', 'duration' => 'date[M j, Y]'), $data['page']);
                    $details_link = "index.php?a=projects/view/";
                    $details_id_field = 'id';
                    $actions = ($user['group_id'] == 0) ? array('Edit[modal]' => 'index.php?a=projects/edit/', 'Delete[modal]' => 'index.php?a=projects/delete/') : '';
                    break;
            }

            $object_actions = ($user['group_id'] == 0) ? array(
                'Nouveau Projet[modal]' => $this->redirect('projects/start', true)
            ) : null;

            $base = $this->redirect("projects/" . __FUNCTION__ . "/$filter/", true);
            $this->set('user', $user);
            $this->set('base', $base);
            $this->set('tab', 'projects');
            $this->set('details_link', $details_link);
            $this->set('details_id_field', $details_id_field);
            $this->set('columns', $columns);
            $this->set('data', $data);
            $this->set('object_actions', $object_actions);
            $this->set('actions', $actions);
            $this->loadView('list');
        }
    }


    /**
     * Updates the progress for a project
     */
    function progress($id)
    {
        /**
         * Verify the user is an admin
         */
        $auth = false;
        if ($user = $this->logged_in())
        {
            if ($user['group_id'] == 0)
            {
                $auth = true;
            }
        }

        if ($auth == true)
        {
            $this->set('project', $id);
            $this->set('title', 'Progress');
            $this->set('form', 'application/views/update-progress.php');
            /**
             * Load form if variables aren't set
             */
            if (!isset($_POST['progress']))
            {
                $this->loadView('form');
            }
            else
            {
                $progress = $_POST['progress'];
                $this->set('progress', $progress);

                /**
                 * Validate input
                 */
                $this->loadLibrary('formvalidator.class');
                $validator = new FormValidator();
                $progress = $validator->run($progress, 'Progress', 'required|clean|numeric|min[0]|max[100]');

                /**
                 * reload the form and display errors
                 */
                if ($validator->has_errors == true)
                {
                    $this->set('error_list', $validator->error_list);
                    $this->loadView('update-progress');
                }
                else
                {
                    $this->loadModel('Project');

                    /**
                     * Update the progress field in the db
                     */
                    if ($this->Project->update_progress($id, $progress))
                    {
                        $this->set('project_id', $id);
                        $this->alert("Progress Updated Successfuly", "success");
                        $this->redirect('projects/view/' . $id);
                    }
                }
            }
        }
    }


    function delete($id)
    {
        if ($this->authorized(ADMIN))
        {
            $this->loadModel("Project");

            if (!isset($_POST['is_confirmed']))
            {
                $data = $this->Project->get_details($id);
                $data = array('Name' => $data['name'], 'Client Name' => $data['client_name']);

                $this->confirm_delete($id, $data);
            }
            else
            {
                $this->Project->delete($id);
                $this->alert("Project Deleted", "notice");
                $this->redirect("portal/home");
            }
        }
    }
}

?>