<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 3, 2010
 * Time: 2:42:09 AM
 */
?>

<?php
$company_details[0] = (isset($CONFIG['company']['address'])) ? $CONFIG['company']['address'] : '';
$company_details[1] = (isset($CONFIG['company']['address_2'])) ? $CONFIG['company']['address_2'] : '';
$company_details[2] = (isset($CONFIG['company']['email'])) ? $CONFIG['company']['email'] : '';
$company_details[3] = (isset($CONFIG['company']['phone'])) ? $CONFIG['company']['phone'] : '';
?>

<div id="invoice-small">
    <div id="logo">
    <?php if (file_exists($CONFIG['company']['logo'])): ?>
        <img class="logo" src="<?php echo $CONFIG['company']['logo'] ?>"/>
    <?php endif; ?>
    </div>

    <div class="address from-address">

    <?php for ($i = 0; $i < count($company_details); $i++): ?>
    <?php if (!empty($company_details[$i])): ?>
        <p><?php echo $company_details[$i]; ?></p>
    <?php endif; ?>
    <?php endfor; ?>


    </div>

    <div class="clear"></div>
    <div class="invoice-addresses clearfix">

        <div class="address to-address">
            <h4><?php echo $lang["lang_to"]; ?></h4>

        <?php
                                           $client = $invoice['client'];
        echo " <p class=attn>";
        echo (!empty($client['name'])) ? $client['name'] . '</p>' : '';
        echo (!empty($client['address_line_1'])) ? $client['address_line_1'] . '<br/>' : '';
        echo (!empty($client['address_line_2'])) ? $client['address_line_2'] . '<br/>' : '';
        echo (!empty($client['contact_email'])) ? $client['contact_email'] . '<br/>' : '';
        echo (!empty($client['contact_phone'])) ? $client['contact_phone'] . '<br/>' : '';
        ?>


        </div>
        <div id="invoice-right-box">
            <table>
                <tbody>
                <tr>
                    <td class="right-box-label"><?php echo $lang["invoice_number"]; ?></td>
                    <td><?php echo $invoice['main']['invoice_number']; ?></td>
                </tr>

                <tr>
                    <td class="right-box-label"><?php echo $lang["lang_date"]; ?></td>
                    <td><?php echo ($invoice['main']['date_of_issue'] != 0) ? date('M j, Y', $invoice['main']['date_of_issue']) : ''; ?></td>
                </tr>


                <tr>
                    <td class="right-box-label"><?php echo $lang["lang_status"]; ?></td>
                    <td><?php echo $this->invoice_status($invoice['main']['balance'], $invoice['main']['due_date'], is_array($invoice['line_items'])); ?></td>
                </tr>

                </tbody>
            </table>
        </div>

        <div class="due-date editable">
            <h4><?php echo $lang["lang_payment_due"]; ?>:
               <span class="date">
                   <?php echo ($invoice['main']['due_date'] != 0) ? date('M j, Y', $invoice['main']['due_date']) : ''; ?>

               </span>
            </h4>
        </div>

    </div>

<?php if (is_array($invoice['line_items'])): ?>

    <table class="invoice-items clearfix">
        <tr class="header">
            <th class="item-name"><span><?php echo $lang["lang_name"]; ?></span></th>
            <th class="quantity"><span>Hrs/Qty</span></th>
            <th class="rate"><span><?php echo $lang["lang_rate"]; ?></span></th>
            <th class="subtotal"><span>Subtotal</span></th>
        </tr>

    <?php foreach ($invoice['line_items'] as $line_item): ?>

        <tr class="invoice-item clearfix">
            <td class="item-name clearfix"><span class="clearfix"><?php echo $line_item['item_name']; ?></span>
            </td>
            <td class="quantity  clearfix"><span
                    class="clearfix"><?php echo $line_item['item_quantity']; ?></span></td>
            <td class="rate  clearfix"><span
                    class="clearfix"><?php echo  '$' . number_format($line_item['item_rate']); ?></span></td>
            <td class="subtotal  clearfix"><span
                    class="clearfix"><?php echo  '$' . number_format($line_item['item_quantity'] * $line_item['item_rate']); ?></span>
            </td>
        </tr>

        <tr class="description">
            <td><span><?php echo $line_item['description']; ?></span></td>
        </tr>

    <?php endforeach; ?>

    </table>

<?php else: ?>

    <div class="no-items">
        <h3><?php echo $lang["lang_no_invoice_items"]; ?></h3>
    </div>

<?php endif; ?>


    <div class="invoice-summary">
        <div class="header"><strong><?php echo $lang["lang_invoice_summary"]; ?></strong></div>
        <div class="total"><h5>Total: <span><?php echo '$' . number_format($invoice['main']['total']); ?></span>
        </h5></div>
        <div class="payments"><h5><?php echo $lang["lang_payments"]; ?>:
            <span><?php echo '$' . number_format($invoice['main']['payments']); ?></span></h5></div>
        <div class="balance"><h6><?php echo $lang["lang_remaining_balance"]; ?>:
            <span><?php echo '$' . number_format($invoice['main']['total'] - $invoice['main']['payments']); ?></span>
        </h6></div>
    </div>
    <div class="clear"></div>

    <div class="clear"></div>

</div>