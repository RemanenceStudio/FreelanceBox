<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 23, 2010
 * Time: 3:52:46 PM
 */

class PaymentsController extends Controller
{

    function make_payment($invoice_id)
    {
        if ($this->authorized(ADMIN))
        {
            $descriptive_names = array(
                'amount' => 'Amount'
            );

            $rules = array(
                'amount' => 'required|numeric'
            );

            $this->loadLibrary('form.class');
            $form = new Form();

            $form->dependencies['form'] = 'application/views/make-payment.php';
            $form->dependencies['title'] = 'Enter Payment';
            $form->dependencies['invoice_id'] = $invoice_id;
            $form->form_fields = $_POST;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'form';
            $form->rules = $rules;


            if ($fields = $form->process_form())
            {
                $this->loadModel('Payment');
                $result = $this->Payment->make_payment($invoice_id, $fields['amount']);

                if ($result)
                {
                    $this->alert('Payment Entered', 'success');
                }
                else
                {
                    $this->alert('There was an error', 'error');
                }
                $this->redirect('invoices/view/' . $invoice_id);
            }
        }
    }

    function get($filter = 'all', $page = 1)
    {
        if ($user = $this->authorized(USER))
        {
            $this->loadModel('Payment');

            switch ($filter)
            {
                case 'all':
                    $this->loadModel('Payment');
                    $data = $this->Payment->get_payments_by_page($page, 10, $user);

                    $columns = array(
                        'invoice_number' => 'Invoice Number',
                        'amount' => 'Payment Amount',
                        'created' => 'Date'
                    );
                    $this->loadLibrary('formatdata.class');
                    $data['page'] = FormatData::format(array(
                       'created' => 'date[M j, Y]'), $data['page']);
                    $details_link = "index.php?a=invoices/view/";
                    $details_id_field = 'invoice_id';
                    $actions = ($user['group_id'] == 0) ? array('Delete[modal]' => 'index.php?a=payments/delete/') : null;
                    break;
            }



            $base = $this->redirect("payments/" . __FUNCTION__ . "/$filter/", true);
            $this->set('user', $user);
            $this->set('base', $base);
            $this->set('tab', 'payments');
            $this->set('actions', $actions);
            $this->set('details_link', $details_link);
            $this->set('details_id_field', $details_id_field);
            $this->set('columns', $columns);
            $this->set('data', $data);
            $this->loadView('list');
        }
    }

    function delete($payment_id)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $this->loadModel("Payment");
            $data = $this->Payment->get_details($payment_id, false);

            if (!isset($_POST['is_confirmed']))
            {
                $data = array('Payment Amount' => $data['amount'], 'Invoice Number' => $data['invoice_number'], 'Created' => date("F j, Y - g:i a", $data['created']));
                $this->confirm_delete($payment_id, $data);
            }
            else
            {
                $this->Payment->delete($payment_id);
                $this->alert("Payment Deleted", "notice");
                $this->redirect('payments/get/');
            }
        }
    }
}

?>