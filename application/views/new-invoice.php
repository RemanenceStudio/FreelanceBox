<?php

$client_id = isset($client_id) ? $client_id : '';
$invoice_number = isset($invoice_number) ? $invoice_number : '';
$date_of_issue = isset($date_of_issue) ? $date_of_issue : '';
$due_date = isset($due_date)?$due_date :'';
$terms = isset($terms)?$terms:$CONFIG['invoice']['default_terms'];
$notes = isset($notes) ? $notes : '';
?>

<div class="row">
       <div class="twelve columns centered" style="margin-top:20px;">
        <form class="custom" id="new_project" action="index.php?a=invoices/create/<?php echo ($is_edit)?$invoice_id:''; ?>" method="post">
        
        	<div class="row">
                <div class="field wide">
                
                    <div class="two columns">
                    <label class="left inline">Client</label>
                    </div>
                    <div class="four columns end">
                        <select name="client_id" id="client_id">
                            <option value=""></option>
                        <?php foreach ($clients as $client)
                        {
                            $selected = ($client['id'] == $client_id) ? 'selected="selected"' : '';
                            echo "<option  $selected value='" . $client['id'] . "'>" . $client['name'] . "</option>";
                        }
                        ?>
                        </select>
                    </div>
                </div>
        	</div>
       		 <br />
        
                <div id="invoice-number" class="field skinny panel">
                    <label><?php echo $lang["lang_invoice_number"]?></label>
                    <div class="faux-field"><strong><?php echo $invoice_number; ?></strong></div>
                </div>
       		 <br />
            <input type="hidden" name="invoice_number" id="invoice_number" value="<?php echo $invoice_number; ?>" />
        
            <div id="date-of-issue" class="field skinny">
                <label><?php echo $lang["lang_date_of_issue"]?></label>
                <input class="datepicker" type="text" name="date_of_issue" id="date_of_issue" value="<?php echo $date_of_issue; ?>"/>
            </div>
            <br />
        
            <div id="due-date" class="field skinny">
                <label><?php echo $lang["lang_due_date"]?></label>
                <input class="datepicker" type="text" name="due_date" id="due_date" value="<?php echo $due_date; ?>"/>
            </div>
            <br />
        
        
            <div class="field wide">
                <label><?php echo $lang["lang_terms"]?></label>
                <textarea type="text" name="terms" id="terms" style="min-height:250px;"><?php echo $terms;?></textarea>
            </div>
        
            <?php if ($is_edit): ?>
                <input type="hidden" id="is_edited" name="is_edited" value="true"/>
            <?php endif; ?>
            <br />
             <input class="button" type="submit" value="<?php echo $lang["lang_submit"]?>" />
        
        </form>
        </div>
</div>
