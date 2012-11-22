<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 4, 2010
 * Time: 6:27:20 AM
 */


class InvoiceItemsController extends Controller
{
    function item($invoice_id, $item_id = null)
    {
        if ($this->authorized(ADMIN))
        {
            $descriptive_names = array(
                'item_name' => 'Item Name',
                'description' => 'Description',
                'item_quantity' => 'Quantity',
                'item_rate' => 'Rate',
                'item_type' => 'Type'
            );

            $rules = array(
                'item_name' => 'required',
                'description' => 'required',
                'item_quantity' => 'required|numeric',
                'item_rate' => 'required|numeric',
                'item_type' => 'required'
            );


            $this->loadLibrary('form.class');
            $form = new Form();

            //If this is an edit, $item_id will be populated
            if ($item_id != null)
            {

                $form->is_edit = true;
                $form->is_edited = (!isset($_POST['is_edited'])) ? false : true; //is_edited will be set as post variable if edit form has been submitted
                $form->dependencies['item_id'] = $item_id;
                if ($form->is_edit && !$form->is_edited)
                {
                    /**
                     * Get fields from the db if this is the first time edit
                     * form has been submitted
                     */
                    $this->loadModel('InvoiceItem');
                    $fields = array_pop($this->InvoiceItem->get_invoice_items($invoice_id, $item_id));
                }
                else
                {
                    //get fields from post if edit form was submitted already
                    $fields = $_POST;
                }

            }

            $form->dependencies['title'] = ($item_id == null) ? 'New Invoice Item' : 'Edit Invoice Item';
            $form->dependencies['form'] = 'application/views/invoice-item.php';
            $form->dependencies['invoice_id'] = $invoice_id;
            $form->form_fields = (!$form->is_edit) ? $_POST : $fields;
            $form->descriptive_names = $descriptive_names;
            $form->view_to_load = 'form';
            $form->rules = $rules;

            if ($fields = $form->process_form())
            {
                $this->loadModel('InvoiceItem');

                if (!$form->is_edit)
                {

                    $result = $this->InvoiceItem->add_invoice_item($invoice_id, $fields['item_name'],
                        $fields['description'], $fields['item_quantity'], $fields['item_rate'], 'item');
                }
                else
                {
                    $result = $this->InvoiceItem->edit($fields, "id = '$item_id'", 'invoice_items');
                }

                if ($result)
                {
                    $this->redirect('invoices/edit/' . $invoice_id);
                }
                else
                {
                    $this->alert('Error processing invoice item', 'error');
                    $this->redirect("invoices/edit/$invoice_id");
                }
            }
        }
    }

    function delete($item_id)
    {
        if ($user = $this->authorized(ADMIN))
        {
            $this->loadModel("InvoiceItem");
            $data = $this->InvoiceItem->get_item($item_id);

            if (!isset($_POST['is_confirmed']))
            {
                $data = array('Item Name' => $data['item_name'], 'Description' => $data['description'], 'Hrs/Qty' => $data['item_quantity'], 'Rate' => $data['item_rate']);
                $this->confirm_delete($item_id, $data);
            }
            else
            {
                $this->InvoiceItem->delete($item_id);
                $this->alert("Invoice Item Deleted", "notice");
                $this->redirect('invoices/edit/'.$data['invoice_id']);
            }
        }
    }
}
?>