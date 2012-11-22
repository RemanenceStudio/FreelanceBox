<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */


?>
<?php
$action = (isset($admin_reset) && $admin_reset == true) ? "clients/reset/$client_id" : "clients/change_password/$client_id";

?>

<form id="change-password" action="<?php echo Controller::redirect($action,true); ?>" method="post">

<?php if (!$admin_reset): ?>
    <div>
        <label><?php echo $lang["lang_current_password"] ?></label>
        <input type="password" name="current_password" id="name" class="wide"/>
    </div>
<?php endif; ?>

    <div>
        <label><?php echo $lang["lang_new_password"] ?></label>
        <input type="password" name="new_password" id="new_password" class="wide"/>
    </div>

    <div>
        <label><?php echo $lang["lang_confirm_new_password"] ?></label>
        <input type="password" name="new_password_confirm" id="new_password_confirm" class="wide"/>
    </div>

     <div class="clearfix button-container">
            <div class="button large"><input type="submit" value="<?php echo $lang["lang_submit"] ?>"></div>
        </div>

</form>