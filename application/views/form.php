<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 3, 2010
 * Time: 11:49:26 PM
 */
?>


<div class="form clearfix">
    <div class="form-header">
        <h2 class="form-title"><?php echo $title; ?></h2>
    </div>

<?php if (!empty($error_list)): ?>
    <div class="error_list clearfix">
    <?php echo $error_list; ?>
    </div>
<?php endif; ?>

    <?php include($form); ?>

</div>