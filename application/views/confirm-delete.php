<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */


?>


    <form id="confirm-delete" action="index.php?a=<?php echo $controller_action ?>" method="post">


        <h4 class="page-title"><?php echo $lang["lang_delete_confirmation"] ?></h4>
        <?php if (isset($data)): ?>
            <table>
            <?php foreach ($data as $key => $value): ?>
                <tr>
                    <td><strong><?php echo $key; ?></strong></td>
                    <td><?php echo $value; ?></td>
                </tr>
            <?php endforeach; ?>
            </table>
        <?php endif; ?>



            <input type="hidden" id="is_confimed" name="is_confirmed" value="true"/>
       


           <div class="clearfix  button-container">
            <a class="small-button cancel" href="<?php echo $this->redirect("reload", true); ?>"><span>Cancel</span></a>
            <div class="button large"><input type="submit" value="Delete"></div>
        </div>

    </form>
    <div id="form-bottom"></div>
