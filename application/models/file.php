<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

/**
 * Handles all logic related to uploading
 * files for a project
 */
class File extends Model
{
    /**
     * Upload a new document to the database
     */
    function add_file($client_id, $desc, $project, $phase, $uploaded_by)
    {
        global $CONFIG;

        /**
         * Ensure te file was uploaded,
         * fail otherwise
         */
        if (!is_uploaded_file($_FILES['document']['tmp_name']))
        {
            trigger_error('Upload Error: ' . 'Error ' . $_FILES['document']['error'], E_USER_NOTICE);
            return false;
        }
        else
        {
            /**
             * Get all necessary information
             * about the file
             */
            $file['temp'] = $_FILES['document']['tmp_name'];
            $file['name'] = $this->rename_file($_FILES['document']['name']);
            $file['size'] = $_FILES['document']['size'];
            $file['type'] = preg_replace("/^(.+?);.*$/", "\\1", $_FILES['document']['type']);
            $file['type'] = strtolower($file['type']);
            $file['ext'] = $this->file_extension($_FILES['document']['name']);
            $file['size'] = round($file['size'] / 1024, 2);
            $file['name'] = preg_replace("/\s+/", "_", $file['name']);

            /**
             * Move the file from the temp directory
             * to the upload directory
             */
            if (!copy($file['temp'], $CONFIG['uploads']['path'] . $file['name']))
            {
                if (!move_uploaded_file($file['temp'], $CONFIG['uploads']['path'] . $file['name']))
                {
                    trigger_error('Upload Error: ' . 'Error moving from temp dir', E_USER_NOTICE);
                    exit;
                    return false;
                }
            }


            /**
             * Create a db record for the uploaded document
             */
            if ($this->insert_asset_row($client_id, $project, $phase, $uploaded_by['id'], $desc, $file['ext'],
                $file['size'], $file['name']))
            {

                $file_id = mysql_insert_id();

                if ($this->get_type($file['ext']) == 'image')
                {
                    $this->create_thumb($CONFIG['uploads']['path'] . $file['name'], $CONFIG['uploads']['path'] . 'thumbs/' . $file['name'], 120);
                }

                
                return $file_id;
            }
            else
                return false;
        }
    }

    function add_link($link_details, $project_details, $user_id)
    {
        $this->insert_asset_row(
            $project_details['client_id'],
            $project_details['id'],
            $link_details['phase'],
            $user_id,
            $link_details['description'],
            'www',
            null,
            $link_details['path']);

        return true;
    }


    /**
     * Get the document extension
     * Useful for actions based on file type
     */
    function file_extension($document_name)
    {
        $ext = explode(".", $document_name);
        $ext = "." . end($ext);

        return $ext;
    }

    function rename_file($document_name)
    {
        $ext = explode(".", $document_name);
        $extension = array_pop($ext);

        $name = (is_array($ext)) ? implode(".", $ext) : $ext;

        $name = $name . "-" . time() . "." . $extension;

        return $name;
    }


    /**
     * Creates a db record for the document
     * with all pertinant information
     */
    function insert_asset_row($client_id, $project, $phase, $uploaded_by, $description, $type,
                              $size, $path)
    {
        $timestamp = time();
        $result = $this->query("INSERT INTO files (client_id, project, phase, posted_by, description, file_type, size, path, created, modified) VALUES ('$client_id', '$project', '$phase', '$uploaded_by', '$description','$type','$size','$path','$timestamp','$timestamp')");

        return true;
    }


    /**
     * Verify that the logged in user is
     * the owner of the requested document
     */
    function authorized_to_download($document, $user, $is_thumb = false)
    {

        if ($is_thumb)
        {
            $path = explode("/", $document);
            $path = $path[count($path) - 1];
            $where = "path = '$path'";
        }
        else $where = "id = '$document'";

        $data = $this->query("SELECT id, client_id FROM files WHERE $where");

        $client_id = (isset($data[0]['client_id'])) ? $data[0]['client_id'] : null;

        if (($client_id) != $user['id'] && ($user['group_id'] != 0))
        {
            return false;
        }
        else
        {
            return (!$is_thumb)?true:$data[0]['id'];
        }
    }

    function get_details($file_id)
    {
        $document = $this->query("SELECT files.*, clients.name AS client_name FROM files LEFT JOIN clients ON files.client_id = clients.id WHERE files.id = '$file_id'");
        $document = (isset($document[0])) ? $document[0] : false;

        if (!$document)
            return false;

        $result = $this->query("SELECT name FROM clients WHERE id = '" . $document['posted_by'] . "'");

        $document['uploaded_by'] = ($result) ? $result[0]['name'] : '';

        $result = $this->query("SELECT name FROM projects WHERE id = '" . $document['project'] . "'");

        $document['project_name'] = ($result) ? $result[0]['name'] : '';


        return $document;
    }

    function get_type($extension)
    {
        $image = array('.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tif', '.raw');
        $audio = array('.mp3', '.wav', '.wma', '.flac', '.aac');
        $video = array('.flv', '.mpg', '.mov', '.divx', '.avi');

        if (in_array($extension, $image))
            return 'image';
        else if (in_array($extension, $audio))
            return 'audio';
        else if (in_array($extension, $video))
            return 'video';
        else if ($extension == 'www')
            return 'website';
        else return 'other';
    }


    function create_thumb($src_image, $dest_image, $thumb_size = 64, $jpg_quality = 90)
    {


        $image = getimagesize($src_image);


        if ($image[0] <= 0 || $image[1] <= 0) return false;


        $image['format'] = strtolower(preg_replace('/^.*?\//', '', $image['mime']));


        switch ($image['format'])
        {
            case 'jpg':
            case 'jpeg':
                $image_data = imagecreatefromjpeg($src_image);
                break;
            case 'png':
                $image_data = imagecreatefrompng($src_image);
                break;
            case 'gif':
                $image_data = imagecreatefromgif($src_image);
                break;
            default:
                // Unsupported format
                return false;
                break;
        }


        if ($image_data == false) return false;

        // Calculate measurements
        if ($image[0] > $image[1])
        {
            // For landscape images
            $x_offset = ($image[0] - $image[1]) / 2;
            $y_offset = 0;
            $square_size = $image[0] - ($x_offset * 2);
        } else
        {
            // For portrait and square images
            $x_offset = 0;
            $y_offset = ($image[1] - $image[0]) / 2;
            $square_size = $image[1] - ($y_offset * 2);
        }

        // Resize and crop
        $canvas = imagecreatetruecolor($thumb_size, $thumb_size);
        if (imagecopyresampled($canvas, $image_data, 0, 0, $x_offset, $y_offset,
            $thumb_size, $thumb_size, $square_size, $square_size))
        {

            // Create thumbnail
            switch (strtolower(preg_replace('/^.*\./', '', $dest_image)))
            {
                case 'jpg':
                case 'jpeg':
                    return imagejpeg($canvas, $dest_image, $jpg_quality);
                    break;
                case 'png':
                    return imagepng($canvas, $dest_image);
                    break;
                case 'gif':
                    return imagegif($canvas, $dest_image);
                    break;
                default:
                    // Unsupported format
                    return false;
                    break;
            }
        } else
        {
            return false;
        }
    }


    function delete($file_id, $path = false)
    {
        if (!$path)
        {
            $path = $this->query("SELECT path FROM files WHERE id = '$file_id'");
            $path = (isset($path[0])) ? $path[0]['path'] : false;
        }

        global $CONFIG;
        unlink($CONFIG['uploads']['path'] . $path);

        unlink($CONFIG['uploads']['path'] . 'thumbs/' . $path);

        $this->query("DELETE FROM files WHERE id = '$file_id'");

        return true;
    }
}

?>