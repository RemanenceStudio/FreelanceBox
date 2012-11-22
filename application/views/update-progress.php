<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */
?>



<form id="update_progress" enctype="multipart/form-data" action="<?php echo $this->redirect("projects/progress/$project", true); ?>" method="post">
	

	
	<div>
		<label><?php echo $lang["lang_percent_complete"]; ?></label>
		<input type="text" name="progress" id="progress" class="skinny" />
	</div>
	
<div class="clearfix  button-container">
      <div class="button large"><input type="submit" value="Submit"></div>
  </div>

	
</form>
