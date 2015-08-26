<?php

    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header ("Cache-Control: no-store, must-revalidate"); // HTTP/1.1
    header ("Pragma: no-cache"); // HTTP/1.0

    $step = $tbg_request->getParameter('step', 0);

    $mode = (isset($mode)) ? $mode : 'install';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>The Bug Genie <?php if ($mode == 'upgrade'): ?>upgrade<?php else: ?>installation<?php endif; ?></title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="author" content="zegenie">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="iconsets/oxygen/favicon.png">
        <script type="text/javascript" src="js/prototype.js"></script>
        <script type="text/javascript" src="js/scriptaculous.js"></script>
        <script type="text/javascript" src="js/install.js"></script>
        <style type="text/css">
            <?php include THEBUGGENIE_PATH . 'themes' . DS . 'oxygen' . DS . 'css' . DS . 'theme.css'; ?>
        </style>
        <style type="text/css">
            @font-face {
              font-family: 'Droid Sans Mono';
              font-style: normal;
              font-weight: normal;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/droid_sans_mono.eot');
              src: local('Droid Sans Mono'), local('DroidSansMono'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/droid_sans_mono.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/droid_sans_mono.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: normal;
              font-weight: normal;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans.eot');
              src: local('Open Sans'), local('OpenSans'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: italic;
              font-weight: normal;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_italic.eot');
              src: local('Open Sans Italic'), local('OpenSans-Italic'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_italic.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_italic.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: normal;
              font-weight: bold;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold.eot');
              src: local('Open Sans Bold'), local('OpenSans-Bold'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: italic;
              font-weight: bold;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold_italic.eot');
              src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold_italic.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold_italic.ttf') format('truetype');
            }

            body { font-size: 12px; }
            body, html, div, p, td, input { font-family: "Open Sans", arial; color: #555; }
            h1, h2, h3, h4, h5 { text-shadow: none; border-bottom: 1px dotted #CCC; text-transform: uppercase; font-weight: normal; color: #888; }
            h1 { font-size: 1.6em; }
            h2 { font-size: 1.4em; margin-bottom: 8px; }
            h3 { font-size: 1.2em; }
            h4 { font-size: 1.1em; }
            h5 { font-size: 1.05em; }
            h2 .smaller { font-size: 0.9em; text-shadow: none; }
            p { font-size: 1.1em; }
            label { vertical-align: middle; font-weight: normal; font-size: 1em; }
            label[for=agree_license] { font-size: 1.05em; margin-top: -7px; display: inline-block; }
            .install_progress { font-weight: normal; border: 1px solid #DDD; padding: 3px; font-size: 1em; margin-bottom: 2px; width: 930px; background-color: #FDFDFD; }
            .install_progress.prereq_fail:first-line { font-weight: bold; }
            .install_progress img { float: right; vertical-align: middle; }
            .progress_bar { display: block; width: 500px; position: relative; height: 20px; background-color: #F5F5F5; box-shadow: inset 0 0 3px rgba(100, 100, 100, 0.3); padding: 0; margin: 5px auto; border-radius: 10px; }
            .progress_bar .filler { background-color: rgba(133, 185, 0, 0.7); position: absolute; left: 0; top: 0; height: 19px; min-width: 20px; border-bottom: 1px solid rgba(165, 202, 72, 1); border-radius: 10px; }
            .prereq_ok { border: 1px solid #aaC6aa; background-color: #CFE8CF; }
            .prereq_fail { border: 1px solid #B76B6B; color: #FFF; font-size: 13px; background-color: #F38888; margin-top: 10px; }
            .prereq_warn { border: 1px solid #FF9900; background-color: #FFFF99; font-size: 12px; }
            .installation_box { padding: 3px 10px 10px 10px; width: 950px; margin-left: auto; margin-right: auto; margin-top: 15px; position: relative; font-size: 1.05em; line-height: 1.6; }
            .installation_box dl { font-size: 1em; }
            .installation_box dl dd, .installation_box dl dt { vertical-align: middle; font-weight: normal; margin-left: 0; }
            .donate { border: 1px solid #aaC6aa; background-color: #CFE8CF; margin: 0; }
            .grey_box { border: 1px solid #DDD; background-color: #F5F5F5; }
            .command_box { border: 1px dashed #DDD; background-color: #F5F5F5; padding: 4px; font-family: 'Droid Sans Mono', monospace; margin-top: 5px; margin-bottom: 15px; }
            .features { width: 400px; float: right; margin-left: 10px; }
            .feature { border: 1px solid #DDD; background-color: #F5F5F5; padding: 10px; margin-bottom: 5px; }
            .feature .description { background-color: #FFF; padding: 10px; }
            .feature .content { background-color: transparent; padding: 10px; border-top: 1px solid #EEE; }
            .install_list dd { padding: 2px 0 5px 0; width: 760px; display: inline-block; float: none; }
            .helptext { color: #AAA; vertical-align: middle; display: inline-block; margin-left: 5px; }
            .install_list dt { width: 200px; padding: 7px 0; display: inline-block; float: none; }
            .install_list dt .faded_out { font-weight: normal; }
            .install_list select { padding: 5px; font-weight: 1.1em; height: auto; vertical-align: middle; border: 1px solid #BEBEBE; border-radius: 4px; }
            .main_header_print
            {
                background: #4E81AB; /* Old browsers */
                color: white;
                border-radius: 2px;
                margin-top: 10px;
                display: block;
                -moz-border-radius-bottomleft: 7px;
                -moz-border-radius-bottomright: 7px;
                -webkit-border-bottom-left-radius: 7px;
                -webkit-border-bottom-right-radius: 7px;
                height: 60px !important;
            }

            input[type=text] { padding: 4px; border: 1px solid #BEBEBE; border-radius: 4px;}
            input[type=text].small { width: 100px; margin-top: -5px; }
            input[type=text].dsn { width: 400px; margin-top: -5px; }
            input[type=text].smallest { width: 50px; }

            .footer_container { background-color: #F5F5F5; width: 100%; border-top: 1px solid #DDD; padding: 5px; text-shadow: 1px 1px 0px #FFF; }
            .footer_container img { margin-right: 10px; }
            .padded_box { padding: 3px 10px 10px 10px; }
            .error { padding: 4px; border: 1px solid #B77; background-color: #FEE; color: #955; margin: 10px 0 10px 0; }
            .ok { padding: 4px; border: 1px solid #aaC6aa; background-color: #CFE8CF; margin: 10px 0 10px 0; }
            .error:first-line, .ok:first-line { font-weight: bold; }

            .logo_small { font-size: 1.1em; color: white; white-space: nowrap; margin-top: 5px; }

            fieldset { border: 1px solid #DDD; margin: 10px 0 10px 0; background-color: #F5F5F5; padding: 0 0 0 8px; }
            legend { font-weight: normal; font-size: 1.1em; color: #555; text-transform: uppercase; padding: 5px 10px; }

            ul.outlined { margin-top: 5px; }
            ul.outlined li { font-weight: bold; }

            #logo_container { line-height: 1em; }
            #logo_container .logo { display: inline-block; vertical-align: middle; margin-right: 10px; }
            #logo_container .logo_name { font-size: 1.8em; float: none; line-height: 1.1em; color: #ECF0F4; }

            .scope_upgrade { margin: 5px; padding: 0; font-size: 0.9em; }
            .scope_upgrade li { margin: 0; padding: 2px 0; list-style: none; display: inline-block; width: 450px; }
            .scope_upgrade li:hover { background-color: rgba(200, 230, 200, 0.3); }
            .scope_upgrade li label { display: inline-block; width: 180px; vertical-align: middle; text-align: right; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
            .scope_upgrade li select { width: 250px; }

            .progress_buttons { padding: 25px 0 0; height: 30px; }
            .progress_buttons .button, .installation_box input[type="submit"] { font-size: 1.2em; padding: 4px 10px !important; }
            .progress_buttons .button-next { float: right; }
            .progress_buttons .button-previous { float: left; }

            .installpage { font-size: 1.1em; }
            ul.backuplist { margin: 15px 0; padding: 0; }
            ul.backuplist > li { background-position: 0 13px; background-repeat: no-repeat; list-style: none; padding: 10px 0 10px 40px; }
            ul.backuplist > li:first-line { font-weight: bold; font-size: 1.1em; }
            ul.backuplist > li.faded { opacity: 0.3; }
            ul.backuplist > li label, ul.backuplist > li input, ul.passwordlist li label, ul.passwordlist li input { vertical-align: middle; }
            ul.backuplist > li > ul { margin: 10px 0; padding: 0; }
            ul.backuplist > li > ul li { margin: 2px 0; display: block; clear: both; float: none; max-width: 800px; }

            ul.passwordlist { list-style: none; margin: 0; padding: 0; }
            ul.passwordlist li { margin: 5px 0 15px; }
            ul.passwordlist li .explanation { padding: 5px; font-size: 1em; }
            .installpage ul li input[type=text], input.username {
                background-image: url('iconsets/oxygen/user_mono.png');
            }
            input[type=email], input.email {
                background-image: url('iconsets/oxygen/icon-mono-email.png');
            }
            input.password, input.adminpassword {
                background-image: url('iconsets/oxygen/password_mono.png');
            }
            .installpage ul li input[type=text], input.username, input.email, input.password, input.adminpassword {
                background-position: 7px 7px;
                background-repeat: no-repeat;
                padding: 5px 5px 5px 28px;
                font-size: 1.1em;
                border-radius: 4px;
                width: 300px;
                margin-top: -5px;
                border: 1px solid #BEBEBE;
            }
        </style>
    </head>
    <body>
        <table style="width: 1000px; height: 100%; table-layout: fixed;" cellpadding=0 cellspacing=0 align="center">
            <tr style="height: 60px;">
                <td valign="top" id="maintd" class="main_header_print">
                    <div id="logo_container" width="100%">
                           <img width=48 height=48 SRC="iconsets/oxygen/logo_48.png" class="logo" alt="The Bug Genie - Installation">
                           <div class="logo_name">The Bug Genie</div><div class="logo_small"><b>Friendly</b> issue tracking and project management</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="height: auto; overflow: auto;" valign="top" id="maintd">
                    <div class="print_header_strip" style="text-align: left; padding: 5px;">
                        <?php if ($mode == 'upgrade'): ?>
                            <b style="font-size: 1.2em;">The Bug Genie upgrade</b>
                        <?php endif; ?>
                    </div>
                    <div style="text-align: left; padding: 0px;">
                        <?php if ($mode == 'install'): ?>
                            <div style="text-align: center; width: 100%; margin-top: 5px; font-size: 14px;">
                                <b>Installation progress</b><br>
                                <div class="progress_bar">
                                    <div class="filler" style="width: <?php echo ($step == 6) ? 100 : $step * 15; ?>%;"></div>
                                </div>
                            </div>
                        <?php endif; ?>
