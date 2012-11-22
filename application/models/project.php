<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

/**
 * The project model performs all
 * functions related to a client
 * project
 */

class Project extends Model
{
    /**
     * Get the project phases for
     * a specific project
     */
    function get_phases($id)
    {
        $result = $this->query("SELECT phases FROM projects WHERE id = '$id'");

        $phases = (isset($result[0])) ? $result[0]['phases'] : '';
        $phases = explode(",", $phases);

        return $phases;
    }


    /**
     * Get the files related to a
     * particular project
     */
    function get_files($id, $order_by = null)
    {
        $order_by = (isset($order_by)) ? 'ORDER BY ' . $order_by : '';

        $result = $this->query("SELECT * FROM files WHERE project = '$id' $order_by");

        return $result;
    }


    /**
     * Get the files for a specific
     * project and organize them by project phase
     */
    function get_files_by_phase($project_id)
    {
        $docs = $files = null;
        $files = $this->get_files($project_id, 'phase');


        if (is_array($files))
        {
            foreach ($files as $document)
            {
                $result = $this->query("SELECT name FROM clients WHERE id = '" . $document['posted_by'] . "'");
                if ($result)
                {
                    $document['uploaded_by'] = $result[0]['name'];
                }
                $docs[$document['phase']][] = $document;
            }
        }


        $result = $this->query("SELECT phases FROM projects WHERE id ='$project_id'");
        $phases = (!empty($result)) ? explode(',', (isset($result[0])) ? $result[0]['phases'] : null) : null;

        //Change the name of the keys - Better way? Couldn't find it...
        if (count($phases) > 0 && is_array($docs))
        {
            $tmp = null;
            foreach ($docs as $phase => &$doc)
            {
                $tmp[trim($phases[$phase])] = $docs[$phase];
                unset($docs[$phase]);
            }
            $docs = $tmp;
        }

        return $docs;
    }


    /** Create a new project for a specific client **/

    function new_project($name, $client_id, $duration, $phases)
    {
        $timestamp = time();

        $result = $this->query("INSERT INTO projects (name, client_id, duration, phases, created, modified, admin_id) VALUES ('$name', '$client_id', '$duration', '$phases', '$timestamp', '$timestamp', '" .$_SESSION['auth_id']. "')");

        if ($result)
        {
            return mysql_insert_id();
        } else
            return false;
    }


    /** Retrieve details for a specific project **/

    function get_details($id)
    {
        $result = $this->query("SELECT * FROM projects WHERE id = '$id'");

        $result = $result[0];
        $result['phases'] = explode(',', $result['phases']);

        $name = $this->query("SELECT name FROM clients WHERE id = '" . $result['client_id'] . "'");
        $result['client_name'] = (isset($name[0]['name'])) ? $name[0]['name'] : '';

        if ($result)
        {
            return $result;
        } else
            return false;
    }


    /**
     * Update the progress percentege
     * for a project
     */
    function update_progress($id, $progress)
    {
        $result = $this->query("UPDATE projects SET progress = '$progress' WHERE id = '$id'");
        if ($result)
        {
            return true;
        } else
            return false;
    }


    /**
     * Returns a "page" of projects. The number
     * of pages in a project is determined
     * by $per_page
     */
    function get_projects_by_page($curr_page, $per_page, $user = null)
    {
		if($_SESSION['auth'] == 1) {
			$where = ($_SESSION['auth_id'] != 0)
					? "admin_id = '" . $_SESSION['auth_id'] ."'" : null;
		}
		else {		
			$where = ($user['group_id'] != 0)
					? "client_id = '" . $user['id'] . "'" : null;
		}
		
        $projects = $this->paginate($curr_page, 10, $where);


        if (is_array($projects['page']))
        {
            foreach ($projects['page'] as & $project)
            {
                $id = $project['client_id'];
                $name = $this->query("SELECT name FROM clients WHERE id = '$id'");

                $project['client_name'] = (isset($name[0]['name'])) ? $name[0]['name'] : '';

                $duration = $project['duration'] * 604800; //convert to seconds
                $project['duration'] = $project['created'] + $duration;
            }
        }
        return $projects;
    }


    /**
     * Gets all projects
     */
    function get_all()
    {
        $projects = $this->query("SELECT * FROM projects");

        foreach ($projects as & $project)
        {
            $id = $project['client_id'];
            $name = $this->query("SELECT name FROM clients WHERE id = '$id'");

            $project['client_name'] = $name[0]['name'];
        }

        return $projects;
    }


    /**
     * Get all projects for a specific
     * client
     */
    function get_projects_by_client($client_id)
    {
        $projects = $this->query("SELECT * FROM projects WHERE client_id = '$client_id'");

        return $projects;
    }

    function owner($project_id, $user = null)
    {
        if ($user['group_id'] == 0)
            return true;

        $owner = $this->query("SELECT client_id FROM projects WHERE id = '$project_id'");

        $owner = (isset($owner[0])) ? $owner[0]['client_id'] : false;

        if ($owner == $user['id'])
            return true;
        else return false;
    }

    function get_messages($project_id, $current_page, $per_page = 10)
    {
        $files = $this->query("SELECT id FROM files WHERE project = '$project_id'");

        $ids = null;
        if (is_array($ids))
        {
            foreach ($files as $file)
            {
                $ids .= $file['id'] . ', ';
            }
            $ids = trim($ids, ', ');
        }
        $where = "(reference_object = 'project' and reference_id = '$project_id')";

        $where .= (is_array($ids)) ? " OR (reference_object = 'file' AND reference_id IN ($ids))" : '';
      
        $messages = $this->paginate($current_page, $per_page, $where, null, 'messages',
            'messages.id, messages.message, messages.reference_object, messages.reference_id, messages.created, clients.name',
            'LEFT JOIN clients ON messages.posted_by = clients.id');

        return $messages;
    }

    function delete($id)
    {
        $this->query("DELETE FROM projects WHERE id = '$id'");

        $this->query("DELETE FROM files WHERE project = '$id'");

        return true;
    }
}

?>