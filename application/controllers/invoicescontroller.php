<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 11, 2010
 * Time: 7:55:26 PM
 */

class InvoicesController extends Controller
{
    //TODO: Check all auth levels in fucntions.


    function create($invoice_id = null)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $descriptive_names = array(
                'client_id' => 'Client',
                'invoice_number' => 'Invoice Number',
                'date_of_issue' => 'Date of Issue',
                'due_date' => 'Due Date',
                'terms' => 'Terms',
                'notes' => 'Notes'
            );

            $rules = array(
                'client_id' => 'required|clean',
                'invoice_number' => 'required|clean',
                'date_of_issue' => 'required|valid_date|clean',
                'due_date' => 'required|valid_date|clean'
            );

            $this->loadLibrary('form.class');
            $form = new Form();

            if ($invoice_id != null)
            {
                $form->is_edit = true;
                $form->is_edited = (!isset($_POST['is_edited'])) ? false : true; //is_edited will be set as post variable if edit form has been submitted
                $form->dependencies['invoice_id'] = $invoice_id;
                if ($form->is_edit && !$form->is_edited)
                {
                    /**
                     * Get fields from the db if this is the first time edit
                     * form has been submitted
                     */
                    $this->loadModel('Invoice');
                    $fields = $this->Invoice->get_details($invoice_id, false);
                    $fields = $fields['main'];

                    //format the dates, otherwise form will error
                    $fields['date_of_issue'] = date('m/d/Y', $fields['date_of_issue']);
                    $fields['due_date'] = date('m/d/Y', $fields['due_date']);
                }
                else
                {
                    //get fields from post if edit form was submitted already
                    $fields = $_POST;
                }
            }

            $this->loadModel('Client');
            $form->dependencies['clients'] = $this->Client->get_all();

            $this->loadModel('Invoice');
            $form->dependencies['invoice_number'] = ($form->is_edit) ? $fields['invoice_number'] : $this->Invoice->next_invoice_number();

            $form->dependencies['title'] = ($form->is_edit) ? 'Edit Invoice' : 'New Invoice';
            $form->dependencies['form'] = 'application/views/new-invoice.php';
            $form->form_fields = ($form->is_edit) ? $fields : $_POST;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'form';
            $form->rules = $rules;

            if ($fields = $form->process_form())
            {
                global $CONFIG;
                $terms = ($fields['terms'] != $CONFIG['invoice']['default_terms']) ? $fields['terms'] : '';

                if (!$form->is_edit)
                {
                    $result = $this->Invoice->create($fields['client_id'], $fields['invoice_number'], $fields['date_of_issue'],
                        $fields['due_date'], $terms);

                    $this->loadModel('Email');

                    $email = array(
                        'invoice_number' => $fields['invoice_number'],
                        'due_date' => $fields['due_date']
                    );
                    $this->Email->send_notification($user['id'], 'invoice', $result, $email);
                }
                else
                {
                    //format timestamp for db (int field)
                    $fields['date_of_issue'] = $this->Invoice->timestamp($fields['date_of_issue']);
                    $fields['due_date'] = $this->Invoice->timestamp($fields['due_date']);

                    $result = $this->Invoice->edit($fields, "id = '$invoice_id'");

                    //for redirect below
                    $result = $invoice_id;
                }

                if ($result)
                {
                    $this->redirect("invoices/edit/$result");
                }
                else
                {
                    $this->alert('Error creating invoice', 'error');
                    $this->redirect('portal/home');
                }
            }
        }
    }


    function view($invoice_id, $expand = false)
    {

        if (!$user = $this->authorized(USER, $invoice_id))
        {
            $this->redirect('portal/home');
            exit;
        }

        if (!isset($this->Invoice))
        {
            $this->loadModel('Invoice');
        }

        $invoice = $this->Invoice->get_details($invoice_id);

        if ($invoice)
        {
            $this->set('invoice', $invoice);
            $this->set('user', $user);

            if (!$expand)
            {
                $actions = array(
                    array('name' => 'Download PDF', 'link' => $this->redirect("invoices/pdf/$invoice_id", true)),
                    array('name' => 'Expand', 'link' => $this->redirect("invoices/view/$invoice_id/expand", true))
                );

                $admin_actions = array(
                    array('name' => 'Edit', 'link' => $this->redirect("invoices/edit/$invoice_id", true)),
                    array('name' => 'Enter Payment', 'class' => 'modal', 'link' => $this->redirect("invoices/pay/$invoice_id", true))
                );

                $this->loadModel('Message');
                $this->set('item_id', $invoice_id);
                $this->set('messages', $this->Message->get_messages('invoice', $invoice_id, null, 5, "created DESC"));
                $this->set('breadcrumbs', array(
                    array('Invoice #' . $invoice['main']['invoice_number'], ''),
                    array('Invoices', 'index.php?a=invoices/get'),
                    array($invoice['client']['name'], $this->redirect('clients/view/' . $invoice['main']['client_id'], true))
                ));

                $this->set('admin_actions', $admin_actions);
                $this->set('actions', $actions);

                $this->set('main_content', 'application/views/small-invoice.php');
                $this->set('sidebar_content', 'application/views/sidebar-messages.php');
                $this->loadView('split-page');
            }
            else
            {

                $this->loadView('view-invoice');
            }
        }
    }

    function edit($invoice_id)
    {
        if ($this->authorized(ADMIN))
        {
            $this->set('editable', true);
            $this->view($invoice_id, true);
        }
    }


    function pay($invoice_id)
    {
        if ($this->authorized(ADMIN))
        {
            PaymentsController::make_payment($invoice_id);
        }
    }


    function get($filter = 'all', $page = 1)
    {
        if ($user = $this->authorized(USER))
        {
            $this->loadModel('Invoice');

            switch ($filter)
            {
                case 'all':
                    $data = $this->Invoice->get_invoices_by_page($page, 10, $user);
                    $columns = array(
                        'invoice_number' => 'Invoice Number',
                        'name' => 'Client',
                        'date_of_issue' => 'Issue Date',
                        'due_date' => 'Due Date',
                        'total' => 'Total',
                        'balance' => 'Balance');
                    $details_link = $this->redirect("invoices/view/", true);
                    $details_id_field = 'id';

                    $this->loadLibrary('formatdata.class');
                    $data['page'] = FormatData::format(array(
                        'date_of_issue' => 'date[M j, Y]',
                        'due_date' => 'date[M j, Y]',
                        'total' => 'money',
                        'balance' => 'money'), $data['page']);
                    $actions = ($user['group_id'] == 0) ? array(
                        'Edit' => $this->redirect('invoices/edit/', true),
                        'Delete[modal]' => $this->redirect('invoices/delete/', true)
                    ) : null;
                    break;
            }

            $object_actions = ($user['group_id'] == 0) ? array(
                'New Invoice[modal]' => $this->redirect('invoices/create', true)
            ) : null;


            $base = $this->redirect("invoices/" . __FUNCTION__ . "/$filter/", true);
            $this->set('user', $user);
            $this->set('base', $base);
            $this->set('tab', 'invoices');
            $this->set('details_link', $details_link);
            $this->set('details_id_field', $details_id_field);
            $this->set('object_actions', $object_actions);
            $this->set('actions', $actions);
            $this->set('columns', $columns);
            $this->set('data', $data);
            $this->loadView('list');
        }
    }

    function pdf($invoice_id)
    {
        if ($this->authorized(USER, $invoice_id))
        {
            $this->loadModel('Invoice');
            $invoice = $this->Invoice->get_details($invoice_id);

            $pdf = new PDF();
            $pdf->invoice($invoice);
        }
    }


    function delete($invoice_id)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $this->loadModel("Invoice");
            $data = $this->Invoice->get_details($invoice_id);

            if (!isset($_POST['is_confirmed']))
            {
                $data = array('Invoice' => $data['main']['invoice_number'], 'Client' => $data['client']['name'], 'Total' => $data['main']['total'], 'Balance' => $data['main']['balance'], 'Created' => date("F j, Y - g:i a", $data['main']['created']));
                $this->confirm_delete($invoice_id, $data);
            }
            else
            {
                $this->Invoice->delete($invoice_id);
                $this->alert("Invoice Deleted", "notice");
                $this->redirect('invoices/get/');
            }
        }
    }


    function invoice_status($balance, $due_date, $is_line_items)
    {
        if ($balance > 0)
        {
            if (time() > $due_date)
                return 'OVERDUE';
            else return 'DUE';
        }
        else
        {
            if ($is_line_items)
                return 'PAID';
            else return '-';
        }
    }


    function pre($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}

?>