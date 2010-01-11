<?php 

    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header ("Pragma: no-cache"); // HTTP/1.0

    $step = $tbg__request->getParameter('step', 0);
    
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>The Bug Genie installation</title>
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="zegenie">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="shortcut icon" href="themes/oxygen/favicon.png">
		<link rel="stylesheet" type="text/css" href="css/oxygen.css">
		<script type="text/javascript" src="js/prototype.js"></script>
		<script type="text/javascript" src="js/scriptaculous.js"></script>
		<script type="text/javascript" src="js/install.js"></script>
	</head>
	<body>
		<table style="width: 1000px; height: 100%; table-layout: fixed; border-left: 1px solid #DDD; border-right: 1px solid #DDD;" cellpadding=0 cellspacing=0 align="center">
			<tr>
				<td style="height: auto; overflow: auto;" valign="top" id="maintd">
					<table class="main_header_print" cellpadding=0 cellspacing=0 width="100%" style="table-layout: fixed;">
						<tr class="logo_back">
						    <td style="width: 70px; height: 65px; text-align: center;" align="center" valign="middle">
						        <img width=48 height=48 SRC="themes/oxygen/logo_48.png" alt="The Bug Genie - Installation">
						    </td>
						    <td align="left" valign="middle" style="width: 300px;"><div class="logo_large">The Bug Genie</div><div class="logo_small"><b>Friendly</b> issue tracking</div></td>
						    <td style="width: auto;">
						    </td>
						</tr>
						<tr>
						    <td class='topmenu' colspan="3"></td>
						</tr>
					</table>
					<div style="border-bottom: 1px solid #DDD; height: 1px;"></div>
					<div class="print_header_strip" style="text-align: left; padding: 5px;">
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
					</div>
					<div style="text-align: left; padding: 0px;">
						<?php if ($step >= 1): ?>
							<div style="text-align: center; width: 100%; margin-top: 5px; font-size: 12px;">
								<b>Installation progress</b><br>
								<table style="width: 700px; margin: 5px auto 0 auto;" cellpadding="0" cellspacing="0" border="0">
									<td style="background-color: #91CC87; width: <?php echo (($step - 1) * 20); ?>%; height: 10px; font-size: 1px;">&nbsp;</td>
									<td style="background-color: #C1FFB7; width: <?php echo (100 - (($step - 1) * 20)); ?>%; height: 10px; font-size: 1px;">&nbsp;</td>
								</table>
							</div>
						<?php endif; ?>