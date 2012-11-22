<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 4, 2010
 * Time: 6:29:20 AM
 */


class InvoiceItem extends Model
{
    function add_invoice_item($invoice_id, $item_name, $description, $item_quantity, $item_rate, $item_type)
    {
        $result = $this->query("INSERT INTO invoice_items (invoice_id, item_name, description, item_quantity, item_rate, item_type)
                                   VALUES ('$invoice_id', '$item_name', '$description', '$item_quantity','$item_rate','$item_type')");
        if ($result)
        {
            return mysql_insert_id();
        }
        else return false;
    }


    function get_invoice_items($invoice_id, $item_id = null)
    {

        if ($item_id == null)
        {
            $where = "invoice_id = '$invoice_id'";
        }
        else $where = "id = '$item_id'";

        $result = $this->query("SELECT * FROM invoice_items WHERE $where");

        if ($result)
        {
            return $result;
        }
        else return false;
    }

    function get_item($item_id)
    {
        $result = $this->query("SELECT * FROM invoice_items WHERE  id ='$item_id'");

        $result = (isset($result[0])) ? $result[0] : false;

        return $result;
    }

    function delete($item_id)
    {
        $this->query("DELETE FROM invoice_items WHERE id = '$item_id'");

        return true;
    }
}

?>