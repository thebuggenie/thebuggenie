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
			.install_progress { font-weight: normal; border: 1px solid #DDD; padding: 3px; font-size: 11px; margin-bottom: 2px; width: 930px; background-color: #FDFDFD; }
			.install_progress:first-line { font-weight: bold; }
			.install_progress img { float: right; }
			.prereq_ok { border: 1px solid #aaC6aa; background-color: #CFE8CF; font-size: 11px; }
			.prereq_fail { border: 1px solid #B76B6B; color: #FFF; font-size: 13px; background-color: #F38888; margin-top: 10px; }
			.prereq_warn { border: 1px solid #FF9900; background-color: #FFFF99; font-size: 12px; }
			.installation_box { padding: 3px 10px 10px 10px; width: 950px; margin-left: auto; margin-right: auto; margin-top: 15px; position: relative; font-size: 12px; }
			.installation_box input[type="submit"] { padding: 5px; font-weight: bold; height: 30px; font-size: 15px; }
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

			.error { padding: 4px; border: 1px solid #B77; background-color: #FEE; color: #955; margin: 10px 0 10px 0; }
			.ok { padding: 4px; border: 1px solid #aaC6aa; background-color: #CFE8CF; margin: 10px 0 10px 0; }
			.error:first-line, .ok:first-line { font-weight: bold; }
			
			.logo_large, .logo_small { color: black; }

			fieldset { border: 1px solid #DDD; margin: 10px 0 10px 0; background-color: #F5F5F5; padding: 0 0 0 8px; }
			legend { font-weight: bold;  }

			ul.outlined { margin-top: 5px; }
			ul.outlined li { font-weight: bold; }
		</style>
	</head>
	<body>
		<table style="width: 1000px; height: 100%; table-layout: fixed; border-left: 1px solid #DDD; border-right: 1px solid #DDD;" cellpadding=0 cellspacing=0 align="center">
			<tr>
				<td style="height: auto; overflow: auto;" valign="top" id="maintd">
					<table class="main_header_print" cellpadding=0 cellspacing=0 width="100%" style="table-layout: fixed;">
						<tr class="logo_back">
						    <td style="width: 70px; height: 65px; text-align: center;" align="center" valign="middle">
						        <img width=48 height=48 SRC="iconsets/oxygen/logo_48.png" alt="The Bug Genie - Installation">
						    </td>
						    <td align="left" valign="middle" style="width: 300px;"><div class="logo_large">The Bug Genie</div><div class="logo_small"><b>Friendly</b> issue tracking and project management</div></td>
						    <td style="width: auto;">
						    </td>
						</tr>
						<tr>
						    <td class='topmenu' colspan="3"></td>
						</tr>
					</table>
					<div style="border-bottom: 1px solid #DDD; height: 1px;"></div>
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
