<?php

	BUGScontext::loadLibrary('ui');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo ($bugs_response->hasTitle()) ? strip_tags(BUGSsettings::get('b2_name') . ' ~ ' . $bugs_response->getTitle()) : strip_tags(BUGSsettings::get('b2_name')); ?></title>
		<?php
			
			BUGScontext::trigger('core', 'header_begins');
				
		?>
		<meta name="description" content="The bug genie, friendly issue tracking">
		<meta name="keywords" content="thebuggenie friendly issue tracking">
		<meta name="author" content="thebuggenie.com">
		<meta http-equiv="Content-Type" content="<?php echo $bugs_response->getContentType(); ?> charset=<?php echo BUGScontext::getI18n()->getCharset(); ?>">
		<link rel="shortcut icon" href="<?php print BUGScontext::getTBGPath(); ?>themes/<?php print BUGSsettings::getThemeName(); ?>/favicon.png">
		<link rel="stylesheet" type="text/css" href="<?php print BUGScontext::getTBGPath(); ?>css/<?php print BUGSsettings::getThemeName(); ?>.css">
		<?php foreach (BUGScontext::getModules() as $module): ?>
			<?php if ($module->hasAccess()): ?>
				<?php $css_name = "css/" . BUGSsettings::getThemeName() . "_" . $module->getName() . ".css"; ?>
				<?php if (file_exists(BUGScontext::getIncludePath() . 'thebuggenie' . DIRECTORY_SEPARATOR . $css_name)): ?>
					<link rel="stylesheet" type="text/css" href="<?php echo BUGScontext::getTBGPath() . $css_name; ?>">
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<script type="text/javascript" src="<?php print BUGScontext::getTBGPath(); ?>js/prototype.js"></script>
		<script type="text/javascript" src="<?php print BUGScontext::getTBGPath(); ?>js/scriptaculous.js"></script>
		<script type="text/javascript" src="<?php print BUGScontext::getTBGPath(); ?>js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="<?php print BUGScontext::getTBGPath(); ?>js/b2.js"></script>
		<?php foreach ($bugs_response->getJavascripts() as $javascript): ?>
			<script type="text/javascript" src="<?php print BUGScontext::getTBGPath() . 'js/' . $javascript; ?>"></script>
		<?php endforeach;?>
		<?php 
			
			BUGScontext::trigger('core', 'header_ends');
		
		?>
	</head>
	<body>
		<table style="width: 100%; height: 100%; table-layout: fixed;" cellpadding=0 cellspacing=0>
			<tr>
				<td style="height: auto; overflow: hidden;" valign="top" id="maintd">
					<table class="main_header<?php if (isset($print_friendly) && $print_friendly) { echo '_print'; } ?>" cellpadding=0 cellspacing=0 width="100%" style="table-layout: fixed;">
						<tr>
							<td align="left" valign="middle" style="width: 500px;" id="logo_td">
								<a href="<?php print BUGScontext::getTBGPath(); ?>"><?php echo image_tag('logo_48.png', array('alt' => '[LOGO]', 'title' => '[LOGO]')) ; ?></a>
								<div class="logo_large"><?php echo BUGSsettings::get('b2_name'); ?></div>
								<div class="logo_small"><?php echo BUGSsettings::get('b2_tagline'); ?></div>
							</td>
							<td style="width: auto;">
								<div class="rounded_box blue" id="header_userinfo">
									<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
									<div class="xboxcontent" style="vertical-align: middle; padding: 0 5px 0 5px;">
										<?php echo image_tag($bugs_user->getAvatarURL(false), array('style' => 'float: left; margin-right: 5px;'), true); ?>
										<?php if ($bugs_user->isGuest()): ?>
											<div class="header_username">
												<strong><?php echo __('You are currently %not_logged_in%', array('%not_logged_in%' => '')); ?></strong><br>
												<?php echo __('Not logged in'); ?>
											</div>
											<div style="text-align: right;">
												<?php echo link_tag(make_url('login'), __('Login')); ?>
												<?php if (BUGSsettings::isRegistrationAllowed()): ?>
													<?php echo __('%login% or %register%', array('%login%' => '', '%register%' => link_tag(make_url('login'), __('Register')))); ?>
												<?php endif; ?> 
											</div>
										<?php else: ?>
											<div class="header_username"><?php echo '<strong>' . __('Logged in as %name%', array('%name%' => '</strong><br>' . BUGScontext::getUser()->getRealname())); ?></div>
											<div style="text-align: right;">
												<?php echo link_tag(make_url('account'), __('My account')); ?> | <?php echo link_tag(make_url('logout'), __('Logout')); ?> 
											</div>
										<?php endif; ?>
									</div>
									<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
								</div>
							</td>
						</tr>
					</table>
					<?php
				
						BUGScontext::trigger('core', 'header_end');
						require BUGScontext::getIncludePath() . 'core/templates/menu.inc.php';
						
					?>