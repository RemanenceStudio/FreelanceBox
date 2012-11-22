<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 16, 2010
 * Time: 10:11:34 PM
 */


class Payment extends Model
{

    function make_payment($invoice_id, $amount)
    {
        $created = time();

        $client_id = $this->query("SELECT client_id FROM invoices WHERE id = '$invoice_id'");
        $client_id = isset($client_id[0]) ? $client_id[0]['client_id'] : 'null';

        $result = $this->query("INSERT INTO payments (admin_id, invoice_id, client_id, amount, created)
        VALUES ('" .$_SESSION['auth_id']. "', '$invoice_id', '$client_id', '$amount', '$created')");

        if ($result)
        {
            Invoice::update_total($invoice_id);
            return true;
        }
        else return false;
    }

    function get_invoice_payments($invoice_id, $payment_id = null)
    {

        if ($payment_id == null)
        {
            $where = "invoice_id = '$invoice_id' AND admin_id='" .$_SESSION['auth_id']. "'";
        }
        else
        {
            $where = "id = '$payment_id' AND admin_id='" .$_SESSION['auth_id']. "'";
        }

        $result = $this->query("SELECT * FROM payments WHERE  $where");

        if ($result)
        {
            return $result;
        }
        else return false;
    }

    function delete($payment_id)
    {
        $result = $this->query("SELECT invoice_id FROM payments WHERE id = '$payment_id' AND admin_id='" .$_SESSION['auth_id']. "'");

        if ($result)
        {
            $invoice_id = $result[0]['invoice_id'];

            $result = $this->query("DELETE FROM payments WHERE id = '$payment_id'");

            Invoice::update_total($invoice_id);

            if ($result) return true;
        }
        else return false;
    }

    function get_payments_by_page($page, $num_per_page, $user)
    {
        $where = ($user['group_id'] != 0)
                ? "client_id = '" . $user['id'] . "'" : null;

        $payments = $this->paginate($page, $num_per_page, $where);

        //TODO: fix query and use format data class
        if (is_array($payments['page']))
        {
            foreach ($payments['page'] as &$payment)
            {
                $payment['invoice_number'] = $this->query("SELECT invoice_number FROM invoices WHERE id = '" . $payment['invoice_id'] . "'");
                $payment['invoice_number'] = (isset($payment['invoice_number'][0])) ? $payment['invoice_number'][0]['invoice_number'] : '';
                //$payment['created'] = ($payment['created'] != 0) ? date('m/d/Y', $payment['created']) : '';
                $payment['amount'] = '$' . number_format($payment['amount']);
            }
        }

        return $payments;
    }

    function get_details($payment_id)
    {
        $result = $this->query("SELECT * FROM payments where id = '$payment_id' AND admin_id='" .$_SESSION['auth_id']. "'");

        $result = (isset($result[0])) ? $result[0] : false;

        if ($result)
        {
            $invoice_number = $this->query("SELECT invoice_number FROM invoices WHERE id = '" . $result['invoice_id'] . "'");
            $invoice_number = (isset($invoice_number[0])) ? $invoice_number[0]['invoice_number'] : 'unknown';
            $result['invoice_number'] = $invoice_number;
        }

        return $result;
    }
}

?>
