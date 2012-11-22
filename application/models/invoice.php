<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 12, 2010
 * Time: 11:44:21 PM
 */

class Invoice extends Model
{
    function create($client_id, $invoice_number, $date_of_issue, $due_date, $terms)
    {
        $created = time();


        $date_of_issue = $this->timestamp($date_of_issue);
        $due_date = $this->timestamp($due_date);

        $result = $this->query("INSERT INTO invoices (admin_id, client_id, invoice_number, date_of_issue, due_date, terms, created, modified)
                        VALUES ('" .$_SESSION['auth_id']. "', '$client_id', '$invoice_number','$date_of_issue','$due_date', '$terms','$created','$created')");
        if ($result)
            return mysql_insert_id();
        else return false;
    }


    function get_invoice($invoice_id)
    {
        $result = $this->query("SELECT * FROM invoices WHERE id = '$invoice_id' AND admin_id='" .$_SESSION['auth_id']. "'");

        return ($result);
    }


    function get_details($invoice_id, $get_line_items = true)
    {
        $this->update_total($invoice_id);

        $result['main'] = array_pop($this->get_invoice($invoice_id));

        if ($get_line_items)
        {
            $this->loadModel("InvoiceItem");
            $result['line_items'] = $this->InvoiceItem->get_invoice_items($invoice_id);
        }

        $result['client'] = Client::get_details($result['main']['client_id']);

        return ($result);
    }


    function get_invoices_by_client($client_id, $get_details = false)
    {
        $invoices = $this->query("SELECT * FROM invoices WHERE client_id ='$client_id' AND admin_id='" .$_SESSION['auth_id']. "'");

        if (is_array($invoices))
        {
            foreach ($invoices as &$invoice)
            {
                $invoice = $this->get_additional_details($invoice);
            }
        }

        return $invoices;
    }

    function get_invoices_by_page($page, $num_per_page = 10, $user = null)
    {
        $where = ($user['group_id'] != 0)
                ? "client_id = '" . $user['id'] . "'" : null;

        $invoices = $this->paginate($page, $num_per_page, $where, 'invoices.created DESC', null,
            'invoices.id, invoices.invoice_number, invoices.date_of_issue, invoices.due_date, invoices.total, invoices.balance,  clients.name',
            'LEFT JOIN clients ON invoices.client_id = clients.id');

        return $invoices;
    }


    function get_additional_details($invoice)
    {
        $name = Client::get_details($invoice['client_id']);
        $invoice['client_name'] = $name['name'];
        $invoice['date_of_issue'] = ($invoice['date_of_issue'] != 0) ? date('m/d/Y', $invoice['date_of_issue']) : '';
        $invoice['due_date'] = ($invoice['due_date'] != 0) ? date('m/d/Y', $invoice['due_date']) : '';
        $invoice['total'] = '$' . number_format($invoice['total']);
        return $invoice;
    }


    function timestamp($date)
    {
        $date = explode('/', $date);
        $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);

        return $date;
    }


    function update_total($invoice_id)
    {
        $payment_total = 0;
        $invoice_total = 0;

        $result = InvoiceItem::get_invoice_items($invoice_id);
        if (is_array($result))
        {
            foreach ($result as $item)

            {
                $invoice_total += $item['item_rate'] * $item['item_quantity'];
            }
        }

        $result = Payment::get_invoice_payments($invoice_id);
        if (is_array($result))
        {
            foreach ($result as $payment)
            {
                $payment_total += $payment['amount'];
            }
        }

        $balance = $invoice_total - $payment_total;

        $result = $this->query("UPDATE invoices SET total = '$invoice_total', payments = '$payment_total', balance = '$balance' WHERE id = '$invoice_id'");

        if ($result)
        {
            return true;
        }
        else return false;
    }


    function next_invoice_number()
    {
        $result = $this->query("SELECT MAX(invoice_number)FROM invoices");

        $next = is_null($result[0]['MAX(invoice_number)']) ? $GLOBALS['CONFIG']['invoice']['base_invoice_number'] : $result[0]['MAX(invoice_number)'] + 1;

        return $next;
    }


    function owner($invoice_id, $user = null)
    {
        if ($user['group_id'] == 0)
            return true;

        $owner = $this->query("SELECT client_id FROM invoices WHERE id = '$invoice_id'");

        $owner = (isset($owner[0])) ? $owner[0]['client_id'] : false;

        if ($owner == $user['id'])
            return true;
        else return false;
    }

    function delete($id)
    {
        $this->query("DELETE FROM invoices WHERE id = '$id'");

        $this->query("DELETE FROM invoice_items WHERE invoice_id = '$id'");

        $this->query("DELETE FROM payments WHERE invoice_id = '$id'");

        return true;
    }
}

?>