<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */


?>
<?php 
$name = isset($name) ? $name : '';
$contact_person = isset($contact_person) ? $contact_person : '';
$contact_email = isset($contact_email) ? $contact_email : '';
$contact_phone = isset($contact_phone) ? $contact_phone : '';
$address_line_1 = isset($address_line_1) ? $address_line_1 : '';
$address_line_2 = isset($address_line_2) ? $address_line_2 : '';

$action = "clients/";
$action .= (!$is_edit) ? ((!$new_admin) ? "create" : "create/admin") : "edit/$id";

$part = explode('/', $_GET['a']);

?>
<div class="row">
    <form id="new_client" action="<?php echo Controller::redirect($action, true); ?>" method="post" enctype="multipart/form-data">
        <div class="eight columns centered">
            <? if ($part[2] == 'myprofile'): ?>
                <div>
                    <label><?php echo $lang["lang_logo"]?></label>
                    <input type="file" name="logo" id="logo" class="wide" />
                </div>
            <?php endif; ?>   
                <div>
                    <label><?php echo $lang["lang_name"]?></label>
                    <input type="text" name="name" id="name" class="wide" value="<?php echo $name; ?>"/>
                </div>
            
            <?php if (!$new_admin): ?>
                <div>
                    <label><?php echo $lang["lang_contact_person"]?></label>
                    <input type="text" name="contact_person" id="contact_person" class="wide"
                           value="<?php echo $contact_person; ?>"/>
                </div>
            <?php endif; ?>
            
                <div>
                    <label>Email</label>
                    <input type="text" name="contact_email" id="contact_email" class="wide"
                           value="<?php echo $contact_email; ?>"/>
                </div>
            
            <?php if (!$new_admin): ?>
                <div>
                    <label><?php echo $lang["lang_phone_number"]?></label>
                    <input type="text" name="contact_phone" id="contact_phone" class="wide"
                           value="<?php echo $contact_phone; ?>"/>
                </div>
            <?php endif; ?>
            
            <?php if ($is_edit): ?>
                <div>
                    <label><?php echo $lang["lang_adress"]?></label>
                    <input type="text" name="address_line_1" id="address_line_1" class="wide"
                           value="<?php echo $address_line_1; ?>"/>
                </div>
                <div>
                    <label><?php echo $lang["lang_adress_line_two"]?></label>
                    <input type="text" name="address_line_2" id="address_line_2" class="wide"
                           value="<?php echo $address_line_2; ?>"/>
                </div>
            <?php endif; ?>
            
            <?php if ($is_edit): ?>
                <input type="hidden" id="is_edited" name="is_edited" value="true"/>
            <?php endif; ?>
            
                <div class="clearfix button-container"><input class="button" type="submit" value="<?php echo $lang["lang_submit"]?>">
                </div>
        </div>
    </form>
</div>
