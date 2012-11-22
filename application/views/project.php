<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */


?>
<?php 
global $CONFIG;
$name = isset($name) ? $name : '';
$client_id = isset($client_id) ? $client_id : '';
$phases = isset($phases) ? $phases : $CONFIG['project']['default_phases'];
$duration = isset($duration) ? $duration : '';
?>

	<div class="row">

    <form id="new_project" action="index.php?a=projects/edit<?php echo ($is_edit) ? '/' . $project_id : ''; ?>"
          method="post">

        <div>
            <label><?php echo $lang["lang_project_name"]?></label>
            <input type="text" name="name" id="name" class="wide" value="<?php echo $name; ?>"/>
        </div>

        <div>
            <label>Client</label>
            <select name="client_id" id="client_id" class="wide">
                <option value=""></option>

            <?php foreach ($clients as $client): ?>
                <option value="<?php echo $client['id']; ?>" <?php echo ($client_id == $client['id']) ? ' selected="selected"' : ''; ?>><?php echo $client['name']; ?></option>
                ;
            <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label><?php echo $lang["lang_project_phase"]."(".$lang["lang_comma_separated"].")"?></label>
            <textarea name="phases" id="phases" class="wide"><?php echo $phases; ?></textarea>
        </div>

        <div>
            <label><?php echo $lang["lang_duration"]."(".$lang["lang_in_weeks"].")"?></label>
            <input type="text" name="duration" id="duration" class="skinny" value="<?php echo $duration; ?>"/>
        </div>

    <?php if ($is_edit): ?>
        <input type="hidden" id="is_edited" name="is_edited" value="true"/>
    <?php endif; ?>

        <div class="clearfix">
        	<input class="button" class="large secondary" type="submit" value="<?php echo $lang["lang_submit"]?>"></div>
        </div>

    </form>
    
    </div>
