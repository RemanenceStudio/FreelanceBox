<?
session_start();

include('core/conlog.php');
include('lang/lang.php');

if( isset($_GET['new']) ) {
	
	$sql = "INSERT INTO `google` VALUES (NULL, '" .$_SESSION['auth_id']. "', '" .$_POST['ps']. "', '" .$_POST['pw']. "', '" .$_POST['cu']. "', '" .$_POST['cv']. "')";
	$rs = mysql_query($sql);
	
	$_SESSION['alert'] = 'Agenda Google ajout&eacute; avec succ&eacute;';
    $_SESSION['alert_class'] = 'success';
	
	//header("Location: calendar.php");
	
}

if( isset($_GET['update']) ) {
	
	$sql = "UPDATE `google` SET `account` = '" .$_POST['ps']. "', `password` = '" .$_POST['pw']. "', `code_user` = '" .$_POST['cu']. "', `code_visibility` = '" .$_POST['cv']. "' WHERE `id` = " . $_GET['id'];
	$rs = mysql_query($sql);
	
	$_SESSION['alert'] = 'Agenda Google mis &agrave; jour avec succ&eacute;';
    $_SESSION['alert_class'] = 'success';
	
	//header("Location: calendar.php");
	
}

if( isset($_GET['del']) ) {
	
	$sql = "DELETE FROM `google` WHERE `id` = " . $_GET['cal'];
	mysql_query($sql);
	
	$_SESSION['alert'] = 'Agenda Google supprim&eacute; avec succ&eacute;';
    $_SESSION['alert_class'] = 'success';
	
	//header("Location: calendar.php");

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
      <h1 class="title"><?php echo $lang["lang_calendar"]; ?></h1>
            <ul class="sub-tabs">
                        <li class="object-action modal"><a
                        href="#" onclick="show();"><span><?php echo $lang["lang_newCalendar"] ?></span></a></li>
              </ul>
          </div>
    <ul class="tab_menu wrapper">
      <li class=""> <a href="index.php?a=clients/get/all"><span><?php echo $lang["lang_clients"] ?></span></a> </li>
      <li class=""> <a href="index.php?a=projects/get/all"><span><?php echo $lang["lang_projects"] ?></span></a> </li>
      <li class=""> <a href="index.php?a=invoices/get/all"><span><?php echo $lang["lang_invoices"] ?></span></a> </li>
      <li class="selected"> <a href="calendar.php"><span><?php echo $lang["lang_calendar"] ?></span></a> </li>
      <li class=""> <a href="timesheet.php"><span><?php echo $lang["lang_timesheet"] ?></span></a> </li>
      <li class=""> <a href="index.php?a=payments/get/all"><span><?php echo $lang["lang_payments"] ?></span></a> </li>
      <li class=" messages"> <a href="index.php?a=messages/get/all"><span>&nbsp;</span></a> </li>
    </ul>
    <div class="inner">
    
      <table class="list ">
        <tr class="table-header">
            <th class=""><? echo $lang["lang_account"]; ?></th>
            <th class="action"></th>
            <th class="action"></th>
        </tr>
        <?
		$sql = "SELECT * FROM `google` WHERE `admin_id` = " . $_SESSION['auth_id'] . " ORDER BY `id` DESC";
		$rs = mysql_query($sql);
		while( $row = mysql_fetch_array($rs) ) {
		?>
        <tr>
            <td class=""><a class="cell-link modal" href="calendar.php?edit&cal=<? echo $row['id']; ?>"><? echo $row['account']; ?></a></td>
            <td class="action"><a class="small-button modal" href="calendar.php?edit&cal=<? echo $row['id']; ?>"><span><?php echo $lang["lang_edit"] ?></span></a></td>
            <td class="action"><a class="small-button danger" href="calendar.php?del&cal=<? echo $row['id']; ?>" onclick="if(confirm('confirmation ?'))return true; else return false;"><span><?php echo $lang["lang_delete"] ?></span></a></td>
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
			<h2 class="form-title"><? if( isset($_GET['edit']) ) echo $lang['lang_editCalendar']; else echo $lang['lang_newCalendar']; ?></h2>
		</div>
		
        <?
		if( isset($_GET['edit']) ) {
			
			$sql = "SELECT * FROM `google` WHERE `id` = " . $_GET['cal'];
			$rs = mysql_query($sql);
			$row = mysql_fetch_array($rs);
				
		}
		?>
        
		<form id="new_timesheet" action="calendar.php?<? if( isset($_GET['edit']) ) echo 'update&id=' . $row['id']; else echo 'new'; ?>" method="post"> 
			<div>
				<label><?php echo $lang["lang_username"]?></label>
				<input type="text" name="ps" id="ps" class="wide" value="<? echo $row['account']; ?>" />
			</div>

			<div>
				<label><?php echo $lang["lang_password"]?></label>
				<input type="password" name="pw" id="pw" class="wide" value="<? echo $row['password']; ?>" />
			</div>
            
            <div>
				<label><?php echo $lang["lang_code_user"]?></label>
				<input type="text" name="cu" id="cu" class="wide" value="<? echo $row['code_user']; ?>" />
			</div>
            
            <div>
				<label><?php echo $lang["lang_code_visibility"]?></label>
				<input type="text" name="cv" id="cv" class="wide" value="<? echo $row['code_visibility']; ?>" />
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