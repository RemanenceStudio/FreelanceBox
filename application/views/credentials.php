<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

?>




<div class="inner">
    <p>A forgotten password can not be recovered. Please reset this user's password if they are unable to log in.</p>
    <table>
        <tr>
            <td><strong>Email:</strong></td>
            <td> <?php echo $details['contact_email'] ?></td>
        </tr>

        <tr>
            <td><strong><?php echo $lang["lang_password"] ?>:</strong></td>
            <td>  <?php echo $password; ?></td>
        </tr>
    </table>
    <div><br/><br/><br/>
        <a class="small-button"
           href="<?php echo $this->redirect("clients/credentials/" . $details['id']."/true", true); ?>">
            <span>Email to client</span>
        </a>

        <a class="small-button danger"
           href="<?php echo $this->redirect("clients/reset/" . $details['id'], true); ?>">
            <span>Reset password</span>
        </a>

    </div>
</div>

 
