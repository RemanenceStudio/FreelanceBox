<?php
session_start();

include('core/conlog.php');
include('lang/lang.php');


?>

<form id="new_timesheet" action="" method="post">  
    <div>
        <label><?php echo $lang["lang_google_agenda"]?></label>
		<select name="agenda" id="agenda" name="agenda">
			<option></option>
			<?
			$sql = "SELECT * FROM `google` WHERE `admin_id` = " . $_SESSION['auth_id'];
			$rs = mysql_query($sql);
			while( $row = mysql_fetch_array($rs) ) {
			
				echo '<option value="' .$row['id']. '">' .$row['account']. '</option>';
			
			}
			?>
		</select>
    </div>

    <div>
        <label><?php echo $lang["lang_date_depart"]?></label>
        <input type="text" name="contact_person" id="contact_person" class="wide" />
    </div>

    <div>
        <label><?php echo $lang["lang_date_fin"]?></label>
        <input type="text" name="contact_email" id="contact_email" class="wide" />
    </div>

    <div class="clearfix button-container">
        <div class="button large"><input type="submit" value="<?php echo $lang["lang_submit"]?>"></div>
    </div>


</form>
