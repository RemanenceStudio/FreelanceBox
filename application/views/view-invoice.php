<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 14, 2010
 * Time: 1:45:51 PM
 */
?>

<?php
$editable = (isset($editable)) ? true : null;
$invoice_id = $invoice['main']['id'];

$company_details[0] = (isset($CONFIG['company']['address'])) ? $CONFIG['company']['address'] : '';
$company_details[1] = (isset($CONFIG['company']['address_2'])) ? $CONFIG['company']['address_2'] : '';
$company_details[2] = (isset($CONFIG['company']['email'])) ? $CONFIG['company']['email'] : '';
$company_details[3] = (isset($CONFIG['company']['phone'])) ? $CONFIG['company']['phone'] : '';


function edit_item_link($invoice_id, $item_id)
{
    return "<td class='edit-icon'><a class='edit-icon' href='index.php?a=invoiceitems/item/$invoice_id/$item_id'>&nbsp;</a></td>" .
            "<td class='delete-icon'><a class='delete-icon' href='index.php?a=invoiceitems/delete/$item_id'>&nbsp;</a></td>";
}

function edit_invoice_link($invoice_id)
{
    return "<div class='edit-icon'><a class='edit-icon' href='index.php?a=invoices/create/$invoice_id/'>Edit Main Details &nbsp;</a></div>";
}


?>
<div id="content">
    <div id="page-content" class="wrapper content admin">
        <div class="invoice-actions">

        <?php if (!$editable && $user['group_id'] == 0): ?>
            <a class="small-button"
               href="<?php echo $this->redirect("invoices/edit/" . $invoice['main']['id'], true); ?>">
                <span><?php echo $lang["lang_edit"]; ?></span>
            </a>
            <a class="small-button"
               href="<?php echo $this->redirect("invoices/pay/" . $invoice['main']['id'], true); ?>">
                <span><?php echo $lang["lang_enter_payment"];?></span>
            </a>
        <?php endif; ?>

        <?php if ($editable): ?>
            <a class="small-button"
               href="<?php echo $this->redirect("invoices/view/" . $invoice['main']['id'], true) . '/expand'; ?>">
                <span><?php echo $lang["lang_preview"];?></span>
            </a>
        <?php endif; ?>

            <a class="small-button"
               href="<?php echo $this->redirect("invoices/pdf/" . $invoice['main']['id'], true); ?>">
                <span><?php echo $lang["lang_downloadPDF"]; ?></span>
            </a>

            <a class="small-button"
               href="<?php echo $this->redirect("invoices/view/" . $invoice['main']['id'], true); ?>">
                <span><?php echo $lang["lang_minimize"]; ?></span>
            </a>
        </div>
        <div class="clear"></div>
        <div id="invoice" class="<?php if ($editable) echo 'editable'; ?>">

            <div id="logo">
            <?php if ( isset($_SESSION['logo_id']) && $_SESSION['logo_id'] != '' ): ?>
            	<img class="logo" src="ds/logo/<?php echo $_SESSION['logo_id']; ?>" />
            <?php else: ?>
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
                            <td class="right-box-label"><?php echo $lang["lang_invoice_number"]; ?></td>
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
            <?php if ($editable)
            {
                echo edit_invoice_link($invoice_id);
            } ?>
            </div>
            <table class="invoice-items clearfix">
            <?php if (is_array($invoice['line_items'])): ?>
                <tr class="header">

                    <th class="item-name"><span><?php echo $lang["lang_name"]; ?></span></th>
                    <th class="quantity"><span><?php echo $lang["lang_hour_quantity"];?></span></th>
                    <th class="rate"><span><?php echo $lang["lang_rate"]; ?></span></th>
                    <th class="subtotal"><span><?php echo $lang["lang_subtotal"]; ?></span></th>
                <?php if ($editable)
                {
                    echo "<th class='edit-icon'>&nbsp;</th>";
                }?>

                </tr>
            <?php endif ?>

            <?php if (is_array($invoice['line_items'])): ?>
            <?php foreach ($invoice['line_items'] as $line_item): ?>
                <tr class="invoice-item">

                    <td class="item-name clearfix"><span class="clearfix"><?php echo $line_item['item_name']; ?></span>
                    </td>
                    <td class="quantity  clearfix"><span
                            class="clearfix"><?php echo $line_item['item_quantity']; ?></span></td>
                    <td class="rate  clearfix"><span
                            class="clearfix"><?php echo  '$' . number_format($line_item['item_rate']); ?></span></td>
                    <td class="subtotal  clearfix"><span
                            class="clearfix"><?php echo  '$' . number_format($line_item['item_quantity'] * $line_item['item_rate']); ?></span>
                    </td>
                <?php if ($editable)
                {
                    echo edit_item_link($invoice_id, $line_item['id']);
                }?>

                </tr>
                <tr class="description">
                    <td><span><?php echo $line_item['description']; ?></span></td>
                </tr>

            <?php endforeach; ?>

            <?php if ($editable): ?>
                <tr>
                    <td colspan="4">
                        <div class="add-item alert notice fluid large">
                            <a class="small-button"
                               href="<?php echo $this->redirect("invoiceitems/item/" . $invoice['main']['id'], true); ?>">
                                <span><?php echo $lang["lang_add_item"]; ?></span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>

            <?php else: ?>
                <div class="no-items alert notice fluid large">
                    <h3> <?php echo $lang["lang_no_invoice_item"]; ?> </h3>
                    <a class="small-button"
                       href="<?php echo $this->redirect("invoiceitems/item/" . $invoice['main']['id'], true); ?>">
                        <span><?php echo $lang["lang_add_item"]; ?></span>
                    </a>
                </div>
            <?php endif; ?>

            </table>
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
            <div class="terms">
            <?php echo (!empty($invoice['main']['terms'])) ? $invoice['main']['terms'] : $CONFIG['invoice']['default_terms']; ?>
            </div>

            <div class="clear"></div>


        </div>
    </div>
</div>