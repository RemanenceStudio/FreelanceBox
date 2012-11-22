<?php
 $invoice_id = isset($invoice_id) ? $invoice_id : '';
$amount = isset($amount) ? $amount : '';

?>

    <form id="invoice-item" action="<?php echo Controller::redirect("invoices/pay/$invoice_id", true);  ?>"
          method="post">

        <div class="section top">
            <div class="field medium first">
                <label><?php echo $lang["lang_amount"];?></label>
                <input type="text" name="amount" id="amount"
                       value="<?php echo $amount; ?>"/>

            </div>


        </div>


        <div class="clearfix button-container">
            <div class="button large"><input type="submit" value="Submit"></div>
        </div>

</form>