<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link href="../../application/views/css/basic.css" media="screen" rel="stylesheet" type="text/css"/>
    <link href="../../application/views/css/style.css" media="screen" rel="stylesheet" type="text/css"/>
    <link href="install.css" media="screen" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="../../application/views/js/jquery.ba-bbq.min.js"></script>
    <script type="text/javascript" src="../../application/views/js/main.js"></script>
    <!--[if IE]>
    <link href="application/views/css/ie.css" media="screen" rel="stylesheet" type="text/css"/>
    <![endif]-->

    <title>Advanced Client Portal - Installation</title>

    <script type="text/javascript">
        $(document).ready(function()
        {
            var loading_indicator = '<div class="loading-indicator">' +
                    '<div class="loading-overlay">&nbsp;</div>' +
                    '<img class="loading" src="application/views/images/loading.gif" />' +
                    '</div>';
            $('#check').live('click', function(e)
            {
                $('body').append(loading_indicator);
                $.post('install_functions.php?a=install/permissions', $(this).serialize(), function(data)
                {
                    $('.loading-indicator').remove();
                    var result = data.charAt(0);

                    if (result == 1)
                    {
                        $('#check').remove();
                        $('.button').append('<input name="next2" id="next2" type="submit" value="Next">');
                    }

                    $('.response').html(data.substr(1)).show('slow');
                });

                e.preventDefault();
                return false;
            });

            $('#next2').live('click', function(e)
            {
                $('.installation-instructions').load('install.php?step=2 .step-2', function()
                {

                });
                e.preventDefault();
                return false;
            });

            $('#install').live('submit', function(e)
            {
                $.post('install_functions.php?a=install/db', $(this).serialize(), function(data)
                {
                    var result = data.charAt(0);

                    if (result == 1)
                    {
                        $('#test-db').remove();
                        $('#install').slideUp();
                        $('.step-2').append('<div class="clear">&nbsp;</div><div class="button large"><input name="next3" id="next3" type="submit" value="Next"></div>');
                    }

                    $('.response').html(data.substr(1)).show('slow');
                });
                e.preventDefault();
                return false;
            });

            $('#next3').live('click', function(e)
            {
                $('.installation-instructions').load('install.php?step=3 .step-3', function()
                {

                });
                e.preventDefault();
                return false;
            });

            $('#final').live('submit', function (e)
            {
                $.post('install_functions.php?a=install/save_final_step', $(this).serialize(), function()
                {

                    $('.response').show();
                    $('.inner').html('<div class="error_list">&nbsp;</div>');

                    $.post('install_functions.php?a=install/import_sql', function(data)
                    {
                        $('.error_list').append(data);
                    });
                    $.post('install_functions.php?a=install/write_config', function(data)
                    {
                        $('.error_list').append(data);
                    });


                });
                e.preventDefault();
                return false;
            });
        });

    </script>
</head>

<body>

<?php $step = isset($_GET['step']) ? $_GET['step'] : 1;

?>

<div id="content" class="clearfix">

    <div class="clear"></div>

    <div id="page-content-outer">
        <div id="page-content" class="wrapper content admin">
            <div class="info-bar">


                <h2 class="title">Advanced Client Portal - Install</h2>

            </div>

            <div class="inner">
                <div class="installation-instructions">
                <?php if ($step == 1): ?>
                    <div class="step-1">
                        <h2>Step 1: Folder Permissions</h2>
                        <h4>For Advanced Client Portal to function correctly, you must set permissions on the
                            <strong>ds</strong> folder and <strong>config</strong> folders to 777. You can make these changes in your ftp program by changing the chmod values for these folder. Please contact your host if you are unsure about how to do this.
                        </h4>

                        <div class="response"></div>
                        <form id="check-permissions" action="../../">
                            <div class="clearfix button-container">

                                <div class="button large">

                                    <input name="check" id="check" type="submit"
                                           value="Check">
                                </div>
                            </div>
                        </form>
                    </div><?php endif; ?>
                <?php if ($step == 2): ?>
                    <div class="step-2">
                        <h2>Step 2: Configure your database connection</h2>
                        <h4>Please double check these values before hitting submit</h4>

                        <div class="response"></div>
                        <form id="install" action="../../">

                            <div>
                                <label>Hostname</label>
                                <input name="host" id="host" type="text" value="">
                            </div>

                            <div>
                                <label>Username</label>
                                <input name="user" id="user" type="text" value="">
                            </div>
                            <div>
                                <label>Password</label>
                                <input name="password" id="password" type="text" value="">
                            </div>

                            <div class="clearfix button-container">
                                <div class="button large"><input name="test-db" id="test-db" type="submit"
                                                                 value="Test DB Connection"></div>
                            </div>

                        </form>
                    </div>
                <?php endif; ?>

                <?php if ($step == 3): ?>
                    <div class="step-3">
                        <h2>Step 3: Configure your database name and base url</h2>
                        <h4>Please be sure your base url is correct or the links will not work correctly. See
                            documentation for more info.</h4>

                        <div class="response"></div>
                        <form id="final" action="../../">

                            <div>
                                <label>Database Name</label>
                                <input name="dbname" id="dbname" type="text" value="acp">
                            </div>

                            <div>
                                <label>Base URL (i.e. http://www.example.com/acp/)</label>
                                <input name="base_url" id="base_url" type="text" value="">
                            </div>
                            <br/><br/>


                            <div class="clearfix button-container">
                                <div class="button large"><input name="final_step" id="final_step" type="submit"
                                                                 value="Next"></div>
                            </div>

                        </form>
                    </div>
                <?php endif; ?>
                <?php if ($step == 4): ?>
                    <div class="step-4">
                        <h2>Step 4: Final Configuration</h2>


                        <div class="error_list">

                        </div>
                        <br/><br/>

                        <p>You will need to update information about your company (i.e. name, address, phone, etc
                            direcctly in the config file. These values are used by the invoice generation script. </p>

                        <h5 style="color:red">It is important that you delete the install folder once the app is
                            functioning properly.</h5>
                        <h5 style="color:red">It is important that you set the chmod value of the config folder back to
                            its original value.</h5>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>