<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 6, 2010
 * Time: 9:43:18 PM
 */
?>

<?php

$is_edit = (isset($is_edit)) ? $is_edit : '';
$description = (isset($description)) ? $description : '';
$phase = (isset($phase)) ?$phase :-1;
$path = (isset($path)) ? $path :'';
$link = (isset($link)) ? $link :'';
global $CONFIG;
?>



<form id="new_file" action="<?php echo Controller::redirect('files/link/'.$project_id.'/'.$link_id, true); ?>"  method="post">


    <div>
        <label>Description</label>
        <input type="text" name="description" id="description" class="wide"
               value="<?php echo $description; ?>"/>
    </div>

    <div>

        <label><?php echo $lang["lang_project_phase"]?></label>
        <select name="phase" id="phase" class="wide">
            <option value=""></option>
        <?php $n = 0; ?>
        <?php foreach ($phases as $phase_name): ?>

            <option <?php echo ($n == $phase) ? ' selected="selected"' : ''; ?>
                    value=<?php echo $n++?>><?php echo $phase_name; ?></option>

        <?php endforeach; ?>
        </select>

    </div>

    <div>
        <label>URL</label>
        <input type="text" name="path" id="path" class="wide"
               value="<?php echo $path; ?>"/>
    </div>

    <?php if ($is_edit): ?>
        <input type="hidden" id="is_edited" name="is_edited" value="true"/>
    <?php endif; ?>

    <div class="clearfix button-container">
        <div class="button large"><input type="submit" value="Submit"></div>
    </div>

</form>
