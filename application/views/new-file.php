<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

?>


<?php

$is_edit = (isset($is_edit)) ? $is_edit : '';
$description = (isset($description)) ? $description : '';
$phase = (isset($phase)) ? $phase : -1;

global $CONFIG;
?>


<div class="row">
<form id="new_file" enctype="multipart/form-data"
      action='<?php echo (!$is_edit) ? "index.php?a=files/add/$project" : "index.php?a=files/edit/" . $id;?>'
      method="post">


    <div>
        <label><?php echo $lang["lang_document_description"]?></label>
        <input type="text" name="description" id="description" class="wide"
               value="<?php echo $description; ?>"/>
    </div>

    <div>

        <label><?php echo $lang["lang_project_phases"]?></label>
        <select name="phase" id="phase" class="wide">
            <option value=""></option>
        <?php $n = 0; ?>
        <?php foreach ($phases as $phase_name): ?>

            <option <?php echo ($n == $phase) ? ' selected="selected"' : ''; ?>
                    value=<?php echo $n++?>><?php echo $phase_name; ?></option>

        <?php endforeach; ?>
        </select>

    </div>
<?php if (!$is_edit): ?>
    <div>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $CONFIG['uploads']['max_file_size']; ?>"/>
        <label><?php echo $lang["lang_file_to_upload"]?></label>
  
            <input class="file" name="document" type="file"/>

    </div>
<?php endif; ?>

<?php if ($is_edit): ?>
    <input type="hidden" id="is_edited" name="is_edited" value="true"/>
<?php endif; ?>

    <div class="clearfix  button-container"><input class="button" type="submit" value="<?php echo $lang["lang_submit"]?>">
    </div>

</form>
   </div>