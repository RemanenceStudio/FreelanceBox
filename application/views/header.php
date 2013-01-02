<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */
 
 include ('lang/lang.php');

?>
<!doctype html>
<html>
	<head>
    <meta charset="utf-8">
    <!--<link href="application/views/css/basic.css" media="screen" rel="stylesheet" type="text/css"/>
    <link href="application/views/css/style.css" media="screen" rel="stylesheet" type="text/css"/>-->
    
	<link rel="stylesheet" href="application/views/stylesheets/app.css">
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="application/views/javascripts/main.js"></script>
    
    

	<script src="application/views/javascripts/foundation/modernizr.foundation.js"></script>
    <!--[if IE]>
    <link href="application/views/css/ie.css" media="screen" rel="stylesheet" type="text/css"/>
    <![endif]-->

 
    <title>Freelance Box</title>
</head>

<body>
<!--START TOP BAR-->
<nav class="top-bar">
	<ul>
	  <li class="name">
      <h1><a class="ie6fix" href="<?php echo Controller::redirect('portal/home', true); ?>">FreelanceBox</a></h1>
      </li>
      <li class="divider"></li>
	  <li class="name">
      <h1><a class="ie6fix" href="<?php echo Controller::redirect('portal/home', true); ?>"><? if($_SESSION['name_id'] != '') echo '<strong>' . $_SESSION['name_id'] .'</strong>'; ?></a></h1>
    </li>
      <li class="divider"></li>
      
      <section>
      	<ul class="right">
            <li class="divider"></li>
		   <?php if (isset($user['group_id']) && $user['group_id'] == 0): ?>
            <li class="has-dropdown"><a class="active" href="#">Mon compte</a>
                <ul class="dropdown">
                    <li>
                        <a class="new-admin-button modal" data-reveal-id="myModal" data-animation="fade"
                           href="<?php echo Controller::redirect('clients/edit/admin', true); ?>"><?php echo $lang["lang_newAdmin"] ?></a>
                    </li>
                    <li>
                        <a class="new-admin-button modal" data-reveal-id="myModal" data-animation="fade"
                           href="<?php echo Controller::redirect('clients/edit/myprofile', true); ?>"><?php echo $lang["lang_editMyInfo"] ?></a>
                    </li>
                    <li>
                        <a class="change-pass-button modal" data-reveal-id="myModal" data-animation="fade"
                           href="<?php echo Controller::redirect('clients/change_password', true); ?>"><?php echo $lang["lang_changePassword"] ?></a>
                    </li>
            
                 </ul>
            </li>
           <?php endif; ?>
            <li class="divider"></li>
            
           	<li class="has-dropdown"><a class="active" href="#">Langue</a>
            	<ul class="dropdown">
                    <li style="width:100px;">
                        <a style="padding:5px;" href="index.php?lang=fr">Fran&ccedil;ais</a>
                    </li>
                    <li style="width:100px;">
                        <a style="padding:5px;" href="index.php?lang=en">English</a>
                    </li>
                </ul>
            </li>
            
            <li class="divider"></li>
            
           <li>
            <a href="<?php echo Controller::redirect('portal/logout', true); ?>"><?php echo $lang["lang_logout"] ?></a>
           </li>
        </ul>
      </section>
    </ul>
</nav>
<!--END TOP BAR-->

<!--START HEADER-->
    <header id="header">
        <div class="row">
              
    
        </div>
    </header>
<!--END HEADER-->

<div class="row">


<!--START NAVIGATION-->
<div class="two columns" id="sidebar-navigation">
    <nav class="navigation">
        <div class="row">
    <? if($_SESSION['auth_id'] != '') { ?> 		
        <ul class="nav-bar mainnav vertical">
            <li>
                <a href="<?php echo Controller::redirect('portal/home', true); ?>"><?php echo $lang["lang_home"] ?></a>
            </li>
        <?php $user = isset($this->user)?$this->user:null; ?>
        <?php if (isset($user['group_id']) && $user['group_id'] == 0): ?>
            <li>
                <a class="new-client-button modal" data-reveal-id="myModal" data-animation="fade"
                   href="<?php  echo Controller::redirect('clients/create', true); ?>"><?php echo $lang["lang_newClient"] ?></a>
            </li>
            <li>
                <a class="new-project-button modal" data-reveal-id="myModal" data-animation="fade"
                   href="<?php echo Controller::redirect('projects/start', true); ?>"><?php echo $lang["lang_newProject"] ?></a>
            </li>
            <li>
                <a class="new-invoice-button modal" data-reveal-id="myModal" data-animation="fade"
                   href="<?php echo Controller::redirect('invoices/create', true); ?>"><?php echo $lang["lang_newInvoice"] ?></a>
            </li>
           
        <?php endif; ?>
           
           
        </ul>
        <a class="resize_button"></a>
    <? } ?>
        </div>
    </nav>
</div>
<!--END NAVIGATION-->

<div id="myModal" class="reveal-modal small ">
    <div id="modal-body"></div>
    <a class="close-reveal-modal">&#215;</a>
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

<!--START CONTENT-->
<div class="nine columns">
<div id="content">
    <div id="alert" class="<?php echo $alert_class; ?>">
    <?php echo $alert;?>
    </div>