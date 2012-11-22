<?
session_start();

include('core/conlog.php');
include('lang/lang.php');

if( isset($_GET['del']) ) {

	$sql = "SELECT * FROM `timesheets` WHERE `id` = " . $_GET['sheet'];
	$rs = mysql_query($sql);
	$row = mysql_fetch_array($rs);
	
	@unlink('ds/' . $row['file'] . '.xls');
	
	$sql = "DELETE FROM `timesheets` WHERE `id` = " . $_GET['sheet'];
	mysql_query($sql);
	
	//header("Location: timesheet.php");

}

?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link href="application/views/css/basic.css" media="screen" rel="stylesheet" type="text/css"/>
    <link href="application/views/css/style.css" media="screen" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="application/views/js/main.js"></script>
	<script type="text/javascript">
	function show() {
		document.getElementById("jqmOverlay").style.display = "inline";
	}
	</script>
    <!--[if IE]>
    <link href="application/views/css/ie.css" media="screen" rel="stylesheet" type="text/css"/>
    <![endif]-->

 
    <title>Freelance Box</title>
</head>

<body>
<div id="header">
    <div class="wrapper clearfix">
        <div class="logo-container">
            <h2 id="logo"><a class="ie6fix"
                             href="index.php?a=portal/home">FreelanceBox <? if($_SESSION['name_id'] != '') echo ', <strong>' . $_SESSION['name_id'] .'</strong>'; ?></a>
            </h2>
        </div>
        <div class="navigation">
         		
            <ul class="main_nav dropdown">
                <li>
                    <a href="index.php?a=portal/home"><?php echo $lang["lang_home"] ?></a>
                </li>
                <li><a href="#">Actions</a>
                    <ul>
                    <?php if ( isset($_SESSION['auth']) && $_SESSION['auth'] == 1) { ?>
                        <li>
                            <a class="new-client-button modal"
                               href="index.php?a=clients/create"><?php echo $lang["lang_newClient"] ?></a>
                        </li>
                        <li>
                            <a class="new-project-button modal"
                               href="index.php?a=projects/start"><?php echo $lang["lang_newProject"] ?></a>
                        </li>
                        <li>
                            <a class="new-invoice-button modal"
                               href="index.php?a=invoices/create"><?php echo $lang["lang_newInvoice"] ?></a>
                        </li>
                        <li>
                            <a class="new-admin-button modal"
                               href="index.php?a=clients/edit/admin"><?php echo $lang["lang_newAdmin"] ?></a>
                        </li>
                        <li>
                            <a class="new-admin-button modal"
                               href="index.php?a=clients/edit/myprofile"><?php echo $lang["lang_editMyInfo"] ?></a>
                        </li>
                    <?php } ?>
                        <li>
                            <a class="change-pass-button modal"
                               href="index.php?a=clients/change_password"><?php echo $lang["lang_changePassword"] ?></a>
                        </li>
                    </ul>
                </li>

				<li>
                    <a href="index.php?a=portal/logout"><?php echo $lang["lang_logout"] ?></a>
                </li>
				<li>
					<a href="index.php?lang=fr" style="padding: 0 0 0 5px;"><img src="images/FR.png" alt="FR" /></a>
					<a href="index.php?lang=en" style="padding: 0 0 0 5px;"><img src="images/GB.png" alt="EN" /></a>
				</li>
            </ul>

            <a class="resize_button"></a>
		        </div>

    </div>
</div>


<div id="modal" class="jqmWindow jqmID1">
    <div id="modal-body"></div>
</div>

<?php

@session_start();

if (isset($_SESSION['alert']))
{
    $alert_class = $_SESSION['alert_class'];
    $alert = $_SESSION['alert'];

    unset($_SESSION['alert_class']);
    unset($_SESSION['alert']);
}
else $alert_class = $alert = '';
?>

<div id="content" class="clearfix">

    <div class="clear"></div>
    <div class="tmp"></div>
    <div id="alert" class="<?php echo $alert_class; ?>">
    <?php echo $alert;?>
    </div>

<div id="page-content-outer">
  <div id="page-content" class="wrapper content admin">
    <div class="info-bar">
      <h1 class="title"><?php echo $lang["lang_timesheet"]; ?></h1>
            <ul class="sub-tabs">
                        <li class="object-action modal"><a
                        href="#" onclick="show();"><span><?php echo $lang["lang_newTimesheet"] ?></span></a></li>
              </ul>
          </div>
    <ul class="tab_menu wrapper">
      <li class=""> <a href="index.php?a=clients/get/all"><span><?php echo $lang["lang_clients"] ?></span></a> </li>
      <li class=""> <a href="index.php?a=projects/get/all"><span><?php echo $lang["lang_projects"] ?></span></a> </li>
      <li class=""> <a href="index.php?a=invoices/get/all"><span><?php echo $lang["lang_invoices"] ?></span></a> </li>
      <li class=""> <a href="calendar.php"><span><?php echo $lang["lang_calendar"] ?></span></a> </li>
      <li class="selected"> <a href="timesheet.php"><span><?php echo $lang["lang_timesheet"] ?></span></a> </li>
      <li class=""> <a href="index.php?a=payments/get/all"><span><?php echo $lang["lang_payments"] ?></span></a> </li>
      <li class=" messages"> <a href="index.php?a=messages/get/all"><span>&nbsp;</span></a> </li>
    </ul>
    <div class="inner">
    
      <table class="list ">
        <tr class="table-header">
        	<th class=""><? echo $lang["lang_name_file"]; ?></th>
            <th class=""><? echo $lang["lang_account"]; ?></th>
            <!-- <th class="action"></th> -->
            <th class="action"></th>
        </tr>
        <?
		$sql = "SELECT * FROM `timesheets` LEFT JOIN `google` ON `google`.`id` = `timesheets`.`account_id` 
		WHERE `timesheets`.`admin_id` = " . $_SESSION['auth_id'] . " ORDER BY `timesheets`.`id` DESC";
		$rs = mysql_query($sql);
		while( $row = mysql_fetch_array($rs) ) {
		?>
        <tr>
            <td class=""><a class="cell-link" href="<? echo 'ds/timesheets/' . $row['file'] . '.xls'; ?>" target="_blank"><? echo $row['file']; ?></a></td>
            <td class=""><a class="cell-link" href="<? echo 'ds/timesheets/' . $row['file'] . '.xls'; ?>" target="_blank"><? echo $row['account']; ?></a></td>
            <!-- <td class="action"><a class="small-button  modal" href="#"><span><?php echo $lang["lang_edit"] ?></span></a></td> -->
            <td class="action"><a class="small-button danger" href="timesheet.php?del&sheet=<? echo $row[0]; ?>" onclick="if(confirm('confirmation ?'))return true; else return false;"><span><?php echo $lang["lang_delete"] ?></span></a></td>
        </tr>
        <?
		}
		?>
      </table>
      
    </div>
    <div class="footer">
   </div>
  </div>
</div>
</div> <!-- end content-->

<!-- BOX NEW TIMESHEET -->
<div class="jqmOverlay" style="display: none; height: 100%; width: 100%; position: fixed; left: 0px; top: 0px; z-index: 2999; opacity: 0.5;"></div>
<div id="modal" class="jqmWindow jqmID1" style="z-index: 3000; width: 400px; height: 600px; top: 50%; left: 50%; margin-top: -300px; margin-left: -200px;">
    <div id="modal-body"><div class="form clearfix">
		<div class="form-header">
			<h2 class="form-title"><? echo $lang['lang_newTimesheet']; ?></h2>
		</div>

		<form id="new_timesheet" action="export.php" method="post">  
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
				<input type="text" name="dd" id="dd" class="wide" />
			</div>

			<div>
				<label><?php echo $lang["lang_date_fin"]?></label>
				<input type="text" name="df" id="df" class="wide" />
			</div>

			<div class="clearfix button-container">
				<div class="button large"><input type="submit" value="<?php echo $lang["lang_submit"]?>"></div>
			</div>
		</form>

	</div>

</div>


<!-- END BOX TIMESHEET -->

</body>
</html>
<?
mysql_close($connect);
?>