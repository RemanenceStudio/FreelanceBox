<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */


?>
<?php
 $invoice_id = isset($invoice_id) ? $invoice_id : '';
$item_name = isset($item_name) ? $item_name : '';
$description = isset($description) ? $description : '';
$item_quantity = isset($item_quantity) ? $item_quantity : '';
$item_rate = isset($item_rate) ? $item_rate : '';
$item_type = isset($item_type) ? $item_type : '';

?>


    <form id="invoice-item"
          action="index.php?a=invoiceitems/item/<?php echo $invoice_id; echo ($is_edit) ? '/' . $item_id : ''; ?>"
          method="post">


        <div class="field wide first">
            <label><?php echo $lang["lang_name"]?></label>
            <input type="text" name="item_name" id="item_name"
                   value="<?php echo $item_name; ?>"/>

        </div>


        <div id="invoice-number" class="field skinny">
            <label><?php echo ($item_type == 'time') ? 'Hours' : $lang["lang_quantity"]; ?></label>
            <input type="text" name="item_quantity" id="item_quantity"
                   value="<?php echo $item_quantity; ?>"/>
        </div>

        <div id="date-of-issue" class="field skinny">
            <label><?php echo $lang["lang_rate"]?></label>
            <input type="text" name="item_rate" id="item_rate"
                   value="<?php echo $item_rate; ?>"/>
        </div>


        <div class="field wide">
            <label>Description</label>
            <textarea type="text" name="description" id="description"><?php echo $description; ?></textarea>
        </div>

    <?php if ($is_edit): ?>
        <input type="hidden" id="is_edited" name="is_edited" value="true"/>
    <?php endif; ?>

        <div class="clearfix button-container">
            <div class="button large"><input type="submit" value="<?php echo $lang["lang_submit"]?>"></div>
        </div>

