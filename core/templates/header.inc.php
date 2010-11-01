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
			if (TBGSettings::isUsingCustomFavicon() == '2')
			{
				?>
				<link rel="shortcut icon" href="<?php print TBGSettings::getFaviconURL(); ?>">
				<?php
			}
			elseif (TBGSettings::isUsingCustomFavicon() == '1')
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
		<?php TBGEvent::createNew('core', 'header_ends')->trigger(); ?>
	</head>
	<body>
		<div class="medium_transparent rounded_box shadowed popup_message failure" onclick="clearPopupMessages();" style="display: none;" id="thebuggenie_failuremessage">
			<div style="padding: 10px 0 10px 0;">
				<span style="color: #000; font-weight: bold;" id="thebuggenie_failuremessage_title"></span><br>
				<span id="thebuggenie_failuremessage_content"></span>
			</div>
		</div>
		<div class="medium_transparent rounded_box shadowed popup_message success" onclick="clearPopupMessages();" style="display: none;" id="thebuggenie_successmessage">
			<div style="padding: 10px 0 10px 0;">
				<span style="color: #000; font-weight: bold;" id="thebuggenie_successmessage_title"></span><br>
				<span id="thebuggenie_successmessage_content"></span>
			</div>
		</div>
		<div id="fullpage_backdrop" style="display: none; background-color: transparent; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; text-align: center;">
			<div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;" id="fullpage_backdrop_indicator">
				<?php echo image_tag('spinning_32.gif'); ?><br>
				<?php echo __('Please wait, loading content'); ?>...
			</div>
			<div id="fullpage_backdrop_content"> </div>
			<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 100000;" class="semi_transparent" onclick="resetFadedBackdrop();"> </div>
		</div>
		<table style="width: 100%; height: 100%; table-layout: fixed; min-width: 1020px;" cellpadding=0 cellspacing=0>
			<tr>
				<td style="height: auto; overflow: hidden;" valign="top" id="maintd">
					<table class="main_header" cellpadding=0 cellspacing=0 width="100%" style="table-layout: fixed;">
						<tr>
							<td align="left" valign="middle" id="logo_td">
								<?php if (TBGSettings::isUsingCustomHeaderIcon() == '2'): ?>
									<a class="logo" href="<?php print TBGContext::getTBGPath(); ?>"><img src="<?php print TBGSettings::getHeaderIconURL(); ?>" alt="<?php print TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()); ?>" title="<?php print TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()); ?>"></a>
								<?php elseif (TBGSettings::isUsingCustomHeaderIcon() == '1'): ?>
									<a class="logo" href="<?php print TBGContext::getTBGPath(); ?>"><img src="<?php print TBGContext::getTBGPath(); ?>header.png" alt="<?php print TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()); ?>" title="<?php print TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()); ?>"></a>
								<?php else: ?>
									<a class="logo" href="<?php print TBGContext::getTBGPath(); ?>"><?php echo image_tag('logo_24.png', array('alt' => TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()), 'title' => TBGSettings::getTBGname() . ' ~ ' . strip_tags(TBGSettings::getTBGtagline()))) ; ?></a>
								<?php endif; ?>
								<div class="logo_large"><?php echo TBGSettings::get('b2_name'); ?></div>
								<div class="logo_small"><?php echo TBGSettings::get('b2_tagline'); ?></div>
							</td>
							<td style="width: auto;">
								<div class="tab_menu header_menu<?php if (TBGContext::isProjectContext()): ?> project_context<?php endif; ?>">
									<ul>
										<?php if (!TBGSettings::isSingleProjectTracker() && !TBGContext::isProjectContext()): ?>
											<li<?php if ($tbg_response->getPage() == 'home'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('home'), image_tag('tab_index.png').__('Frontpage')); ?></li>
										<?php elseif (TBGContext::isProjectContext()): ?>
											<li<?php if ($tbg_response->getPage() == 'project_dashboard'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_dashboard_small.png').__('Project dashboard')); ?></li>
										<?php endif; ?>
										<?php if (!$tbg_user->isThisGuest() && !TBGSettings::isSingleProjectTracker() && !TBGContext::isProjectContext()): ?>
											<li<?php if ($tbg_response->getPage() == 'dashboard'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('dashboard'), image_tag('icon_dashboard_small.png').__('Dashboard')); ?></li>
										<?php endif; ?>
										<?php if (TBGContext::isProjectContext() && ($tbg_user->canReportIssues() || $tbg_user->canReportIssues(TBGContext::getCurrentProject()->getID()))): ?>
											<li<?php if ($tbg_response->getPage() == 'reportissue'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_reportissue', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('tab_reportissue.png').((isset($_SESSION['rni_step1_set'])) ? __('Continue reporting') : __('Report an issue'))); ?></li>
										<?php endif; ?>
										<?php TBGEvent::createNew('core', 'menustrip_item_links', null, array('selected_tab' => $tbg_response->getPage()))->trigger(); ?>
										<?php if (!TBGContext::isProjectContext() && $tbg_user->canAccessConfigurationPage()): ?>
											<li<?php if ($tbg_response->getPage() == 'config'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure'), image_tag('tab_config.png').__('Configure')); ?></li>
										<?php endif; ?>
										<?php /*?><li<?php if ($tbg_response->getPage() == 'about'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('about'), image_tag('tab_about.png').__('About')); ?></li> */ ?>
									</ul>
									<div class="rounded_box blue" id="header_userinfo">
										<table style="width: auto;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width: 30px; padding-top: 4px;" valign="middle">
													<?php echo image_tag($tbg_user->getAvatarURL(true), array('alt' => '[avatar]'), true); ?>
												</td>
												<td class="header_username" valign="middle">
													<?php if ($tbg_user->isGuest()): ?>
														<?php echo __('You are not logged in'); ?>
													<?php else: ?>
														<?php echo (TBGContext::getUser()->getRealname() == '') ? TBGContext::getUser()->getBuddyname() : TBGContext::getUser()->getRealname(); ?>
													<?php endif; ?>
												</td>
												<td class="header_userlinks">
													<?php if ($tbg_user->isGuest()): ?>
														<?php echo link_tag(make_url('login'), __('Login')); ?>
														<?php if (TBGSettings::isRegistrationAllowed()): ?>
															<br>
															<?php echo link_tag(make_url('login'), __('Register')); ?>
														<?php endif; ?>
													<?php else: ?>
														<?php echo link_tag(make_url('account'), __('Your account')); ?><br>
														<?php echo link_tag(make_url('logout'), __('Logout')); ?>
													<?php endif; ?>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
					</table>
					<?php /*if ($tbg_response->isProjectMenuStripVisible()): ?>
						<div id="project_menustrip"><?php include_component('project/menustrip', array('project' => TBGContext::getCurrentProject())); ?></div>
					<?php elseif ($tbg_user->canSearchForIssues()): */ ?>
						<div class="submenu_strip<?php if (TBGContext::isProjectContext()): ?> project_context<?php endif; ?>">
							<?php if (!TBGContext::isProjectContext()): ?>
								<span class="selected_project_name">
									<?php echo __('No project selected'); ?>
								</span>
							<?php else: ?>
								<div class="project_stuff tab_menu">
									<ul>
										<li class="project_name nohover"><?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), TBGContext::getCurrentProject()->getName()); ?></li>
										<?php /*
										<li<?php if ($tbg_response->getPage() == 'project_planning'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_planning', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Planning')); ?></li>
										<li<?php if ($tbg_response->getPage() == 'project_files'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_files', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Files')); ?></li>
										 */ ?>
										<?php if (TBGContext::getCurrentProject()->usesScrum()): ?>
											<li<?php if (in_array($tbg_response->getPage(), array('project_scrum', 'project_scrum_sprint_burndown'))): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_scrum', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Scrum')); ?></li>
										<?php endif; ?>
										<li<?php if (in_array($tbg_response->getPage(), array('project_issues', 'viewissue'))): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Issues')); ?></li>
										<li<?php if ($tbg_response->getPage() == 'project_roadmap'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_roadmap', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Roadmap')); ?></li>
										<li<?php if ($tbg_response->getPage() == 'project_team'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_team', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Team')); ?></li>
										<li<?php if ($tbg_response->getPage() == 'project_statistics'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_statistics', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Statistics')); ?></li>
										<li<?php if ($tbg_response->getPage() == 'project_timeline'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_timeline', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Timeline')); ?></li>
										<?php TBGEvent::createNew('core', 'project_menustrip_item_links', TBGContext::getCurrentProject(), array('selected_tab' => $tbg_response->getPage()))->trigger(); ?>
									</ul>
								</div>
							<?php endif; ?>
							<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo (TBGContext::isProjectContext()) ? make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'quicksearch' => 'true')) : make_url('search', array('quicksearch' => 'true')); ?>" method="get" name="quicksearchform" style="float: right;">
								<div style="width: auto; padding: 0; text-align: right; position: relative;">
									<?php $quicksearch_title = __('Search for anything here'); ?>
									<input type="text" name="searchfor" id="searchfor" value="<?php echo $quicksearch_title; ?>" style="width: 220px; padding: 1px 1px 1px;" onblur="if ($('searchfor').getValue() == '') { $('searchfor').value = '<?php echo $quicksearch_title; ?>'; $('searchfor').addClassName('faded_out'); }" onfocus="if ($('searchfor').getValue() == '<?php echo $quicksearch_title; ?>') { $('searchfor').clear(); } $('searchfor').removeClassName('faded_out');" class="faded_out"><div id="searchfor_autocomplete_choices" class="autocomplete"></div>
									<script type="text/javascript">

									new Ajax.Autocompleter("searchfor", "searchfor_autocomplete_choices", '<?php echo (TBGContext::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>', {paramName: "searchfor", minChars: 2});

									</script>
									<input type="submit" value="<?php echo TBGContext::getI18n()->__('Find'); ?>" style="padding: 0 2px 0 2px;">
								</div>
							</form>
						</div>
					<?php //endif; ?>