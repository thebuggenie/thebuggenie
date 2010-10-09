<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo ($tbg_response->hasTitle()) ? strip_tags(TBGSettings::get('b2_name') . ' ~ ' . $tbg_response->getTitle()) : strip_tags(TBGSettings::get('b2_name')); ?></title>
		<?php
			
			TBGEvent::createNew('core', 'header_begins')->trigger();
				
		?>
		<meta name="description" content="The bug genie, friendly issue tracking">
		<meta name="keywords" content="thebuggenie friendly issue tracking">
		<meta name="author" content="thebuggenie.com">
		<meta http-equiv="Content-Type" content="<?php echo $tbg_response->getContentType(); ?> charset=<?php echo TBGContext::getI18n()->getCharset(); ?>">
		<?php 
			if (TBGSettings::isUsingCustomFavicon())
			{
				?>
				<link rel="shortcut icon" href="<?php print TBGContext::getTBGPath(); ?>favicon.png">
				<?php
			}
			else
			{
				?>
				<link rel="shortcut icon" href="<?php print TBGContext::getTBGPath(); ?>themes/<?php print TBGSettings::getThemeName(); ?>/favicon.png">
				<?php
			}
		?>
		<link rel="shortcut icon" href="<?php print TBGContext::getTBGPath(); ?>themes/<?php print TBGSettings::getThemeName(); ?>/favicon.png">
		<link rel="stylesheet" type="text/css" href="<?php print TBGContext::getTBGPath(); ?>css/<?php print TBGSettings::getThemeName(); ?>.css">
		<?php foreach ($tbg_response->getFeeds() as $feed_url => $feed_title): ?>
			<link rel="alternate" type="application/rss+xml" title="<?php echo $feed_title; ?>" href="<?php echo $feed_url; ?>">
		<?php endforeach; ?>
		<?php if (count(TBGContext::getModules())): ?>
			<?php foreach (TBGContext::getModules() as $module): ?>
				<?php if ($module->hasAccess()): ?>
					<?php $css_name = "css/" . TBGSettings::getThemeName() . "_" . $module->getName() . ".css"; ?>
					<?php if (file_exists(TBGContext::getIncludePath() . 'thebuggenie' . DIRECTORY_SEPARATOR . $css_name)): ?>
						<link rel="stylesheet" type="text/css" href="<?php echo TBGContext::getTBGPath() . $css_name; ?>">
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<script type="text/javascript" src="<?php print TBGContext::getTBGPath(); ?>js/prototype.js"></script>
		<script type="text/javascript" src="<?php print TBGContext::getTBGPath(); ?>js/scriptaculous.js"></script>
		<script type="text/javascript" src="<?php print TBGContext::getTBGPath(); ?>js/b2.js"></script>
		<?php foreach ($tbg_response->getJavascripts() as $javascript): ?>
			<script type="text/javascript" src="<?php print TBGContext::getTBGPath() . 'js/' . $javascript; ?>"></script>
		<?php endforeach;?>
		<?php 
			
			TBGEvent::createNew('core', 'header_ends')->trigger();
		
		?>
	</head>
	<body>
		<table style="width: 100%; height: 100%; table-layout: fixed; min-width: 1020px;" cellpadding=0 cellspacing=0>
			<tr>
				<td style="height: auto; overflow: hidden;" valign="top" id="maintd">
					<table class="main_header<?php if (isset($print_friendly) && $print_friendly) { echo '_print'; } ?>" cellpadding=0 cellspacing=0 width="100%" style="table-layout: fixed;">
						<tr>
							<td align="left" valign="middle" id="logo_td">
							<?php 
								if (TBGSettings::isUsingCustomHeaderIcon())
								{
									?>
									<a href="<?php print TBGContext::getTBGPath(); ?>"><img src="<?php print TBGContext::getTBGPath(); ?>header.png" alt="<?php print TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()); ?>" title="<?php print TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()); ?>"></a>
									<?php
								}
								else
								{
									?>
									<a href="<?php print TBGContext::getTBGPath(); ?>"><?php echo image_tag('logo_48.png', array('alt' => TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()), 'title' => TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()))) ; ?></a>
									<?php
								}
							?>
								<div class="logo_large"><?php echo TBGSettings::get('b2_name'); ?></div>
								<div class="logo_small"><?php echo TBGSettings::get('b2_tagline'); ?></div>
							</td>
							<td style="width: auto; min-width: 500px;">
								<div class="rounded_box blue" id="header_userinfo">
									<?php echo image_tag($tbg_user->getAvatarURL(false), array('style' => 'float: left; margin-right: 5px; width: 48px; height: 48px;'), true); ?>
									<?php if ($tbg_user->isGuest()): ?>
										<div class="header_username">
											<strong><?php echo __('You are currently %not_logged_in%', array('%not_logged_in%' => '')); ?></strong><br>
											<?php echo __('Not logged in'); ?>
										</div>
										<div style="text-align: right;">
											<?php echo link_tag(make_url('login'), __('Login')); ?>
											<?php if (TBGSettings::isRegistrationAllowed()): ?>
												<?php echo __('%login% or %register%', array('%login%' => '', '%register%' => link_tag(make_url('login'), __('Register')))); ?>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<?php (TBGContext::getUser()->getRealname() == '') ? $name = TBGContext::getUser()->getBuddyname() : $name = TBGContext::getUser()->getRealname() ?>
										<div class="header_username"><?php echo '<strong>' . __('Logged in as %name%', array('%name%' => '</strong><br>' . $name )); ?></div>
										<div style="text-align: right;">
											<?php echo link_tag(make_url('account'), __('My account')); ?> | <?php echo link_tag(make_url('logout'), __('Logout')); ?>
										</div>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					</table>
					<?php
				
						TBGEvent::createNew('core', 'header_end')->trigger();
						require TBGContext::getIncludePath() . 'core/templates/menu.inc.php';
						
					?>