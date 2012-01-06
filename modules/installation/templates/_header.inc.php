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
		<link rel="stylesheet" type="text/css" href="themes/oxygen/oxygen.css">
		<script type="text/javascript" src="js/prototype.js"></script>
		<script type="text/javascript" src="js/scriptaculous.js"></script>
		<script type="text/javascript" src="js/install.js"></script>
		<style type="text/css">
			@font-face {
			  font-family: 'Droid Sans Mono';
			  font-style: normal;
			  font-weight: normal;
			  src: url('<?php echo TBGContext::getTBGPath(); ?>fonts/droid_sans_mono.eot');
			  src: local('Droid Sans Mono'), local('DroidSansMono'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/droid_sans_mono.woff') format('woff'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/droid_sans_mono.ttf') format('truetype');
			}
			@font-face {
			  font-family: 'Open Sans';
			  font-style: normal;
			  font-weight: normal;
			  src: url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans.eot');
			  src: local('Open Sans'), local('OpenSans'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans.woff') format('woff'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans.ttf') format('truetype');
			}
			@font-face {
			  font-family: 'Open Sans';
			  font-style: italic;
			  font-weight: normal;
			  src: url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans_italic.eot');
			  src: local('Open Sans Italic'), local('OpenSans-Italic'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans_italic.woff') format('woff'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans_italic.ttf') format('truetype');
			}
			@font-face {
			  font-family: 'Open Sans';
			  font-style: normal;
			  font-weight: bold;
			  src: url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans_bold.eot');
			  src: local('Open Sans Bold'), local('OpenSans-Bold'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans_bold.woff') format('woff'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans_bold.ttf') format('truetype');
			}
			@font-face {
			  font-family: 'Open Sans';
			  font-style: italic;
			  font-weight: bold;
			  src: url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans_bold_italic.eot');
			  src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans_bold_italic.woff') format('woff'), url('<?php echo TBGContext::getTBGPath(); ?>fonts/open_sans_bold_italic.ttf') format('truetype');
			}
		
			body, html, div, p, td, input { font-family: "Open Sans", arial; font-size: 12px; }
			h1, h2, h3, h4 { text-shadow: 1px 1px 2px #DDD; }
			h1 { font-size: 1.4em; }
			h2 { font-size: 1.3em; }
			h3 { font-size: 1.2em; }
			h4 { font-size: 1.1em; }
			h2 .smaller { font-size: 0.9em; text-shadow: none; }
			.install_progress { font-weight: normal; border: 1px solid #DDD; padding: 3px; font-size: 11px; margin-bottom: 2px; width: 930px; background-color: #FDFDFD; }
			.install_progress:first-line { font-weight: bold; }
			.install_progress img { float: right; }
			.prereq_ok { border: 1px solid #aaC6aa; background-color: #CFE8CF; font-size: 11px; }
			.prereq_fail { border: 1px solid #B76B6B; color: #FFF; font-size: 13px; background-color: #F38888; margin-top: 10px; }
			.prereq_warn { border: 1px solid #FF9900; background-color: #FFFF99; font-size: 12px; }
			.installation_box { padding: 3px 10px 10px 10px; width: 950px; margin-left: auto; margin-right: auto; margin-top: 15px; position: relative; font-size: 12px; }
			.installation_box input[type="submit"] { padding: 5px; font-weight: bold; height: 30px; font-size: 16px; }
			.donate { border: 1px solid #aaC6aa; background-color: #CFE8CF; }
			.grey_box { border: 1px solid #DDD; background-color: #F5F5F5; }
			.command_box { border: 1px dashed #DDD; background-color: #F5F5F5; padding: 4px; font-family: 'Droid Sans Mono', monospace; width: 928px; margin-top: 5px; margin-bottom: 15px; }
			.features { width: 400px; float: right; margin-left: 10px; }
			.feature { border: 1px solid #DDD; background-color: #F5F5F5; padding: 10px; margin-bottom: 5px; }
			.feature .description { background-color: #FFF; padding: 10px; }
			.feature .content { background-color: transparent; padding: 10px; border-top: 1px solid #EEE; }
			.install_list dd { padding: 2px 0 5px 0; }
			.install_list dd input[type="text"], .install_list dd input[type="password"] { width: 320px; }
			.install_list dt { width: 420px; }
			.install_list dt .faded_out { font-weight: normal; }
			.main_header_print
			{
				background: #6193cf; /* Old browsers */
				background: -moz-linear-gradient(top, #6193cf 0%, #396ba7 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#6193cf), color-stop(100%,#396ba7)); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(top, #6193cf 0%,#396ba7 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(top, #6193cf 0%,#396ba7 100%); /* Opera11.10+ */
				background: -ms-linear-gradient(top, #6193cf 0%,#396ba7 100%); /* IE10+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#6193cf', endColorstr='#396ba7',GradientType=0 ); /* IE6-9 */
				background: linear-gradient(top, #6193cf 0%,#396ba7 100%); /* W3C */
				color: white;
				border-bottom-left-radius: 7px;
				border-bottom-right-radius: 7px;
				-moz-border-radius-bottomleft: 7px;
				-moz-border-radius-bottomright: 7px;
				-webkit-border-bottom-left-radius: 7px;
				-webkit-border-bottom-right-radius: 7px;
				box-shadow: 0 0 8px rgba(0, 0, 0, 0.4); -webkit-box-shadow: 0 0 8px rgba(0, 0, 0, 0.4); -moz-box-shadow: 0 0 8px rgba(0, 0, 0, 0.4);
				height: 60px !important;
			}
			
			.footer_container { background-color: #F5F5F5; width: 100%; border-top: 1px solid #DDD; padding: 5px; text-shadow: 1px 1px 0px #FFF; }
			.footer_container img { margin-right: 10px; }
			.padded_box { padding: 3px 10px 10px 10px; }
			.error { padding: 4px; border: 1px solid #B77; background-color: #FEE; color: #955; margin: 10px 0 10px 0; }
			.ok { padding: 4px; border: 1px solid #aaC6aa; background-color: #CFE8CF; margin: 10px 0 10px 0; }
			.error:first-line, .ok:first-line { font-weight: bold; }
			
			.logo_small { font-size: 0.9em; color: white; white-space: nowrap; }

			fieldset { border: 1px solid #DDD; margin: 10px 0 10px 0; background-color: #F5F5F5; padding: 0 0 0 8px; }
			legend { font-weight: bold;  }

			ul.outlined { margin-top: 5px; }
			ul.outlined li { font-weight: bold; }
			
			#logo_container .logo { margin-right: 10px; }
			#logo_container .logo_name { font-size: 1.7em; float: none; }
		</style>
	</head>
	<body>
		<table style="width: 1000px; height: 100%; table-layout: fixed;" cellpadding=0 cellspacing=0 align="center">
			<tr>
				<td style="overflow: auto;" valign="top" id="maintd" class="main_header_print">
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
							<b>The Bug Genie upgrade</b>
						<?php else: ?>
							<b>The Bug Genie installation</b> &ndash;
							<?php if ($step == 0): ?>
								Introduction
							<?php elseif ($step == 6): ?>
								Finished
							<?php elseif ($step == 2): ?>
								Prerequisites
							<?php else: ?>
								step <?php echo $step; ?>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<div style="text-align: left; padding: 0px;">
						<?php if ($step >= 1 && $mode == 'install'): ?>
							<div style="text-align: center; width: 100%; margin-top: 5px; font-size: 12px;">
								<b>Installation progress</b><br>
								<table style="width: 700px; margin: 5px auto 0 auto;" cellpadding="0" cellspacing="0" border="0">
									<td style="background-color: #91CC87; width: <?php echo (($step - 1) * 20); ?>%; height: 10px; font-size: 1px;">&nbsp;</td>
									<td style="background-color: #C1FFB7; width: <?php echo (100 - (($step - 1) * 20)); ?>%; height: 10px; font-size: 1px;">&nbsp;</td>
								</table>
							</div>
						<?php endif; ?>
