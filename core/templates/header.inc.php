<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo ($tbg_response->hasTitle()) ? strip_tags(TBGSettings::getTBGname() . ' ~ ' . $tbg_response->getTitle()) : strip_tags(TBGSettings::getTBGname()); ?></title>
		<?php TBGEvent::createNew('core', 'header_begins')->trigger(); ?>
		<meta name="description" content="The bug genie, friendly issue tracking">
		<meta name="keywords" content="thebuggenie friendly issue tracking">
		<meta name="author" content="thebuggenie.com">
		<meta http-equiv="Content-Type" content="<?php echo $tbg_response->getContentType(); ?> charset=<?php echo TBGContext::getI18n()->getCharset(); ?>">
		<?php if (TBGSettings::getFaviconType() == '2'): ?>
			<link rel="shortcut icon" href="<?php print TBGSettings::getFaviconURL(); ?>">
		<?php elseif (TBGSettings::getFaviconType() == '1'): ?>
			<link rel="shortcut icon" href="<?php print TBGContext::getTBGPath(); ?>favicon.png">
		<?php else: ?>
			<link rel="shortcut icon" href="<?php print TBGContext::getTBGPath(); ?>themes/<?php print TBGSettings::getThemeName(); ?>/favicon.png">
		<?php endif; ?>
		
		<?php
		include(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . TBGSettings::getThemeName() . DIRECTORY_SEPARATOR . 'theme.php');
		?>
		
		<link rel="shortcut icon" href="<?php print TBGContext::getTBGPath(); ?>themes/<?php print TBGSettings::getThemeName(); ?>/favicon.png">
		<link title="<?php echo (TBGContext::isProjectContext()) ? __('%project_name% search', array('%project_name%' => TBGContext::getCurrentProject()->getName())) : __('%site_name% search', array('%site_name%' => TBGSettings::getTBGname())); ?>" href="<?php echo (TBGContext::isProjectContext()) ? make_url('project_opensearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('opensearch'); ?>" type="application/opensearchdescription+xml" rel="search">
		<?php $tbg_response->addStylesheet(TBGSettings::getThemeName().'.css'); ?>
		<?php foreach ($tbg_response->getFeeds() as $feed_url => $feed_title): ?>
			<link rel="alternate" type="application/rss+xml" title="<?php echo str_replace('"', '\'', $feed_title); ?>" href="<?php echo $feed_url; ?>">
		<?php endforeach; ?>
		<?php if (count(TBGContext::getModules())): ?>
			<?php foreach (TBGContext::getModules() as $module): ?>
				<?php $css_name = TBGSettings::getThemeName() . "_" . $module->getName() . ".css"; ?>
				<?php
				if (file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $css_name)):
					$tbg_response->addStylesheet($css_name);
				endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php 
		$tbg_response->addJavascript('prototype.js');
		$tbg_response->addJavascript('builder.js');
		$tbg_response->addJavascript('effects.js');
		$tbg_response->addJavascript('dragdrop.js');
		$tbg_response->addJavascript('controls.js');
		$tbg_response->addJavascript('jquery.markitup.js');
		$tbg_response->addJavascript('thebuggenie.js');

		if ($tbg_user->isGuest()):
			$tbg_response->addJavascript('login.js');
		endif;

		$cssstring = '';
		$jsstring = '';
		
		$sepcss = array();
		$sepjs = array();
		
		foreach ($tbg_response->getStylesheets() as $stylesheet => $minify)
		{
			if ($minify == true)
			{
				$cssstring .= ',css/'.$stylesheet;
			}
			else
			{
				$sepcss[] = $stylesheet;
			}
		}
		
		foreach ($tbg_response->getJavascripts() as $script => $minify)
		{
			if ($minify == true)
			{
				$jsstring .= ',js/'.$script;
			}
			else
			{
				$sepjs[] = $script;
			}
		}
		
		$cssstring = ltrim($cssstring, ',');
		$jsstring = ltrim($jsstring, ',');
		?>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
		<script type="text/javascript">jQuery.noConflict();</script>
		<link rel="stylesheet" type="text/css" href="<?php print make_url('serve'); ?>&g=css&files=<?php print base64_encode($cssstring); ?>">
		<script type="text/javascript" src="<?php print make_url('serve'); ?>&g=js&files=<?php print base64_encode($jsstring); ?>"></script>

		<?php
		// files opted out of minification, wont use content server on minify=off mode
		foreach ($sepcss as $css)
		{
			echo '<link rel="stylesheet" type="text/css" href="'.$css.'">';
		}
		
		foreach ($sepjs as $js)
		{
			echo 'script type="text/javascript" src="'.$js.'">';
		}
		
		?>

		<script type="text/javascript">
			jQuery(document).ready(function(){jQuery('textarea').markItUp({
				previewParserPath:	'', // path to your Wiki parser
				onShiftEnter:		{keepDefault:false, replaceWith:'\n\n'},
				markupSet: [
					{name:'Heading 1', key:'1', openWith:'== ', closeWith:' ==', placeHolder:'Your title here...' },
					{name:'Heading 2', key:'2', openWith:'=== ', closeWith:' ===', placeHolder:'Your title here...' },
					{name:'Heading 3', key:'3', openWith:'==== ', closeWith:' ====', placeHolder:'Your title here...' },
					{name:'Heading 4', key:'4', openWith:'===== ', closeWith:' =====', placeHolder:'Your title here...' },
					{name:'Heading 5', key:'5', openWith:'====== ', closeWith:' ======', placeHolder:'Your title here...' },
					{separator:'---------------' },		
					{name:'Bold', key:'B', openWith:"'''", closeWith:"'''"}, 
					{name:'Italic', key:'I', openWith:"''", closeWith:"''"}, 
					{name:'Stroke through', key:'S', openWith:'<s>', closeWith:'</s>'}, 
					{separator:'---------------' },
					{name:'Bulleted list', openWith:'(!(* |!|*)!)'}, 
					{name:'Numeric list', openWith:'(!(# |!|#)!)'}, 
					{separator:'---------------' },
					{name:'Picture', key:"P", replaceWith:'[[Image:[![Url:!:http://]!]|[![name]!]]]'}, 
					{name:'Link', key:"L", openWith:"[[![Link]!] ", closeWith:']', placeHolder:'Your text to link here...' },
					{name:'Url', openWith:"[[![Url:!:http://]!] ", closeWith:']', placeHolder:'Your text to link here...' },
					{separator:'---------------' },
					{name:'Quotes', openWith:'(!(> |!|>)!)', placeHolder:''},
					{name:'Code', openWith:'(!(<source lang="[![Language:!:php]!]">|!|<pre>)!)', closeWith:'(!(</source>|!|</pre>)!)'}
				]
			});});
		</script>
		<style type="text/css">
			/* -------------------------------------------------------------------
			// markItUp! Universal MarkUp Engine, JQuery plugin
			// By Jay Salvat - http://markitup.jaysalvat.com/
			// ------------------------------------------------------------------*/
			.markItUp .markItUpButton1 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>h1.png); 
			}
			.markItUp .markItUpButton2 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>h2.png); 
			}
			.markItUp .markItUpButton3 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>h3.png); 
			}
			.markItUp .markItUpButton4 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>h4.png); 
			}
			.markItUp .markItUpButton5 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>h5.png); 
			}

			.markItUp .markItUpButton6 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>bold.png);
			}
			.markItUp .markItUpButton7 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>italic.png);
			}
			.markItUp .markItUpButton8 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>stroke.png);
			}

			.markItUp .markItUpButton9 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>list-bullet.png);
			}
			.markItUp .markItUpButton10 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>list-numeric.png);
			}

			.markItUp .markItUpButton11 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>picture.png); 
			}
			.markItUp .markItUpButton12 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>link.png);
			}
			.markItUp .markItUpButton13 a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>url.png);
			}

			.markItUp .markItUpButton14 a	{
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>quotes.png);
			}
			.markItUp .markItUpButton15 a	{
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>code.png);
			}

			.markItUp .preview a {
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>preview.png);
			}
			.markItUp * {
				margin:0px; padding:0px;
				outline:none;
			}
			.markItUp a:link,
			.markItUp a:visited {
				color:#000;
				text-decoration:none;
			}
			.markItUp  {
/*				width:700px;*/
				margin:5px 0 5px 0;
			}
			.markItUpContainer  {
				font:11px 'Droid Sans', sans-serif;
			}
			.markItUpEditor {
				font:12px 'Droid Sans Mono', monospace;
				padding:5px;
				width:690px;
				height:320px;
				clear:both;
				line-height:18px;
				overflow:auto;
			}
			.markItUpPreviewFrame	{
				overflow:auto;
				background-color:#FFF;
				width:99.9%;
				height:300px;
				margin:5px 0;
			}
			.markItUpFooter {
				width:100%;
			}
			.markItUpResizeHandle {
				overflow:hidden;
				width:22px; height:5px;
				margin-left:auto;
				margin-right:auto;
				background-image:url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>handle.png);
				cursor:n-resize;
			}
			/***************************************************************************************/
			/* first row of buttons */
			.markItUpHeader ul li	{
				list-style:none;
				float:left;
				position:relative;
			}
			.markItUpHeader ul li:hover > ul{
				display:block;
			}
			.markItUpHeader ul .markItUpDropMenu {
				background:transparent url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>menu.png) no-repeat 115% 50%;
				margin-right:5px;
			}
			.markItUpHeader ul .markItUpDropMenu li {
				margin-right:0px;
			}
			/* next rows of buttons */
			.markItUpHeader ul ul {
				display:none;
				position:absolute;
				top:18px; left:0px;	
				background:#FFF;
				border:1px solid #000;
			}
			.markItUpHeader ul ul li {
				float:none;
				border-bottom:1px solid #000;
			}
			.markItUpHeader ul ul .markItUpDropMenu {
				background:#FFF url(<?php echo TBGSettings::getURLhost() . TBGContext::getStrippedTBGPath() . '/themes/' . TBGSettings::getThemeName() . '/markitup/'; ?>submenu.png) no-repeat 100% 50%;
			}
			.markItUpHeader ul .markItUpSeparator {
				margin:0 10px;
				width:1px;
				height:16px;
				overflow:hidden;
				background-color:#CCC;
			}
			.markItUpHeader ul ul .markItUpSeparator {
				width:auto; height:1px;
				margin:0px;
			}
			/* next rows of buttons */
			.markItUpHeader ul ul ul {
				position:absolute;
				top:-1px; left:150px; 
			}
			.markItUpHeader ul ul ul li {
				float:none;
			}
			.markItUpHeader ul a {
				display:block;
				width:16px; height:16px;
				text-indent:-10000px;
				background-repeat:no-repeat;
				padding:3px;
				margin:0px;
			}
			.markItUpHeader ul ul a {
				display:block;
				padding-left:0px;
				text-indent:0;
				width:120px; 
				padding:5px 5px 5px 25px;
				background-position:2px 50%;
			}
			.markItUpHeader ul ul a:hover  {
				color:#FFF;
				background-color:#000;
			}
			.markItUpHeader a:hover  {
				border-bottom: none;
			}			
		</style>
		<?php TBGEvent::createNew('core', 'header_ends')->trigger(); ?>
	</head>
	<body>
		<div class="medium_transparent rounded_box shadowed popup_message failure" onclick="TBG.Main.Helpers.Message.clear();" style="display: none;" id="thebuggenie_failuremessage">
			<div style="padding: 10px 0 10px 0;">
				<div class="dismiss_me"><?php echo __('Click this message to dismiss it'); ?></div>
				<span style="color: #000; font-weight: bold;" id="thebuggenie_failuremessage_title"></span><br>
				<span id="thebuggenie_failuremessage_content"></span>
			</div>
		</div>
		<div class="medium_transparent rounded_box shadowed popup_message success" onclick="TBG.Main.Helpers.Message.clear();" style="display: none;" id="thebuggenie_successmessage">
			<div style="padding: 10px 0 10px 0;">
				<div class="dismiss_me"><?php echo __('Click this message to dismiss it'); ?></div>
				<span style="color: #000; font-weight: bold;" id="thebuggenie_successmessage_title"></span><br>
				<span id="thebuggenie_successmessage_content"></span>
			</div>
		</div>
		<div id="fullpage_backdrop" style="display: none; background-color: transparent; width: 100%; height: 100%; position: fixed; top: 0; left: 0; margin: 0; padding: 0; text-align: center;">
			<div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;" id="fullpage_backdrop_indicator">
				<?php echo image_tag('spinning_32.gif'); ?><br>
				<?php echo __('Please wait, loading content'); ?>...
			</div>
			<div id="fullpage_backdrop_content"> </div>
			<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 100000;" class="semi_transparent" <?php if (TBGContext::getRouting()->getCurrentRouteAction() != 'login'): ?>onclick="TBG.Main.Helpers.Backdrop.reset();"<?php endif; ?>> </div>
		</div>
		<table style="width: 100%; height: 100%; table-layout: fixed; min-width: 1020px;" cellpadding=0 cellspacing=0>
			<tr>
				<td style="height: auto; overflow: hidden;" valign="top" id="maintd">
					<table class="main_header" cellpadding=0 cellspacing=0 width="100%" style="table-layout: fixed;">
						<tr>
							<td align="left" valign="middle" id="logo_td">
								<?php $link = (TBGSettings::getHeaderLink() == '') ? TBGContext::getTBGPath() : TBGSettings::getHeaderLink(); ?>
								<?php if (TBGSettings::isUsingCustomHeaderIcon() == TBGSettings::APPEARANCE_HEADER_URL): ?>
									<a class="logo" href="<?php print $link; ?>"><img src="<?php print TBGSettings::getHeaderIconURL(); ?>" alt="[logo]" title="[logo]"></a>
								<?php elseif (TBGSettings::isUsingCustomHeaderIcon() == TBGSettings::APPEARANCE_HEADER_CUSTOM): ?>
									<a class="logo" href="<?php print $link; ?>"><img src="<?php print TBGContext::getTBGPath(); ?>header.png" alt="[logo]" title="[logo]"></a>
								<?php else: ?>
									<a class="logo" href="<?php print $link; ?>"><?php echo image_tag('logo_24.png', array('alt' => '[logo]', 'title' => '[logo]')) ; ?></a>
								<?php endif; ?>
								<div class="logo_large"><?php echo TBGSettings::getTBGname(); ?></div>
								<div class="logo_small"><?php echo TBGSettings::getTBGtagline(); ?></div>
							</td>
							<td style="width: auto;">
								<div class="tab_menu header_menu<?php if (TBGContext::isProjectContext()): ?> project_context<?php endif; ?>">
									<ul>
										<?php if (!TBGSettings::isSingleProjectTracker() && !TBGContext::isProjectContext()): ?>
											<li<?php if ($tbg_response->getPage() == 'home'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('home'), image_tag('tab_index.png').__('Frontpage')); ?></li>
										<?php elseif (TBGContext::isProjectContext()): ?>
											<li<?php if (in_array($tbg_response->getPage(), array('project_dashboard', 'project_planning', 'project_scrum', 'project_scrum_sprint_details', 'project_timeline', 'project_team', 'project_roadmap', 'project_statistics', 'vcs_commitspage'))): ?> class="selected"<?php endif; ?>>
												<div>
													<?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_dashboard_small.png').__('Summary')); ?>
													<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
												</div>
												<div id="project_information_menu" class="tab_menu_dropdown shadowed">
													<?php include_template('project/projectinfolinks', array('submenu' => true)); ?>
												</div>
											</li>
										<?php endif; ?>
										<?php if (!$tbg_user->isThisGuest() && !TBGSettings::isSingleProjectTracker() && !TBGContext::isProjectContext()): ?>
											<li<?php if ($tbg_response->getPage() == 'dashboard'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('dashboard'), image_tag('icon_dashboard_small.png').__('Dashboard')); ?></li>
										<?php endif; ?>
										<?php if (TBGContext::isProjectContext() && ($tbg_user->canReportIssues() || $tbg_user->canReportIssues(TBGContext::getCurrentProject()->getID()))): ?>
											<li<?php if ($tbg_response->getPage() == 'reportissue'): ?> class="selected"<?php endif; ?>>
												<div>
													<?php echo link_tag(make_url('project_reportissue', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('tab_reportissue.png') . __('Report an issue')); ?>
													<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
													</div>
													<div id="project_issue_menu" class="tab_menu_dropdown shadowed">
													<?php foreach (TBGContext::getCurrentProject()->getIssuetypeScheme()->getReportableIssuetypes() as $issuetype): ?>
														<?php echo link_tag(make_url('project_reportissue_with_issuetype', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'issuetype' => $issuetype->getKey())), image_tag($issuetype->getIcon() . '_tiny.png' ) . __($issuetype->getName())); ?>
													<?php endforeach;?>
												</div>											
											</li>
										<?php endif; ?>
										<?php if (TBGContext::isProjectContext() && $tbg_user->canSearchForIssues()): ?>
											<li<?php if (in_array($tbg_response->getPage(), array('project_issues', 'viewissue'))): ?> class="selected"<?php endif; ?>>
												<div>
													<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('tab_search.png').__('Issues')); ?>
													<?php if (TBGContext::isProjectContext()): ?>
														<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
													<?php endif; ?>
												</div>
												<?php if (TBGContext::isProjectContext()): ?>
													<div id="issues_menu" class="tab_menu_dropdown shadowed">
														<?php echo link_tag(make_url('project_open_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Open issues for this project')); ?>
														<?php echo link_tag(make_url('project_closed_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Closed issues for this project')); ?>
														<?php echo link_tag(make_url('project_milestone_todo_list', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Milestone todo-list for this project')); ?>
														<?php echo link_tag(make_url('project_most_voted_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Most voted for issues')); ?>
													</div>
												<?php endif; ?>
											</li>
										<?php endif; ?>
										<?php if (!TBGContext::isProjectContext() && ($tbg_user->hasPageAccess('teamlist') || count($tbg_user->getTeams())) && !is_null(TBGTeamsTable::getTable()->getAll())): ?>
											<li<?php if ($tbg_response->getPage() == 'team'): ?> class="selected"<?php endif; ?>>
												<div>
													<?php echo link_tag('javascript:void(0)', image_tag('tab_teams.png') . __('Teams'), array('class' => 'not_clickable')); ?>
													<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
												</div>
												<div id="team_menu" class="tab_menu_dropdown shadowed">
													<?php foreach (TBGTeam::getAll() as $team): ?>
														<?php if (!$team->hasAccess()) continue; ?>
														<?php echo link_tag(make_url('team_dashboard', array('team_id' => $team->getID())), image_tag('tab_teams.png' ) . $team->getName()); ?>
													<?php endforeach;?>
												</div>											
											</li>
										<?php endif; ?>
										<?php if (!TBGContext::isProjectContext() && ($tbg_user->hasPageAccess('clientlist') || count($tbg_user->getClients())) && !is_null(TBGClientsTable::getTable()->getAll())): ?>
											<li<?php if ($tbg_response->getPage() == 'client'): ?> class="selected"<?php endif; ?>>
												<div>
													<?php echo link_tag('javascript:void(0)', image_tag('tab_clients.png') . __('Clients'), array('class' => 'not_clickable')); ?>
													<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
												</div>
												<div id="client_menu" class="tab_menu_dropdown shadowed">
													<?php foreach (TBGClient::getAll() as $client): ?>
														<?php if (!$client->hasAccess()) continue; ?>
														<?php echo link_tag(make_url('client_dashboard', array('client_id' => $client->getID())), image_tag('tab_clients.png' ) . $client->getName()); ?>
													<?php endforeach;?>
												</div>											
											</li>
										<?php endif; ?>
										<?php TBGEvent::createNew('core', 'menustrip_item_links', null, array('selected_tab' => $tbg_response->getPage()))->trigger(); ?>
									</ul>
									<?php TBGEvent::createNew('core', 'before_header_userinfo')->trigger(); ?>
									<div class="rounded_box blue tab_menu_container" id="header_userinfo">
										<table style="width: auto;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width: 30px; padding-top: 4px;" valign="middle">
													<?php echo image_tag($tbg_user->getAvatarURL(true), array('alt' => '[avatar]'), true); ?>
												</td>
												<td id="header_username" valign="middle">
													<?php if ($tbg_user->isGuest()): ?>
														<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'login')); ?>')"><?php echo __('You are not logged in'); ?></a>
													<?php else: ?>
														<?php $name = (TBGContext::getUser()->getRealname() == '') ? TBGContext::getUser()->getBuddyname() : TBGContext::getUser()->getRealname(); ?>
														<?php echo link_tag(make_url('dashboard'), tbg_decodeUTF8($name)); ?>
													<?php endif; ?>
												</td>
												<td class="header_userlinks">
													<div class="dropdown_separator">
														<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
													</div>
												</td>
											</tr>
										</table>
										<div class="rounded_box blue tab_menu_dropdown user_menu_dropdown shadowed">
											<?php if ($tbg_user->isGuest()): ?>
												<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'login')); ?>')"><?php echo image_tag('icon_login.png').__('Login'); ?></a>
												<?php if (TBGSettings::isRegistrationAllowed()): ?>
													<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'login', 'section' => 'register')); ?>');"><?php echo image_tag('icon_register.png').__('Register'); ?></a>
												<?php endif; ?>
											<?php else: ?>
												<div class="header"><?php echo __('You are: %userstate%', array('%userstate%' => '<span class="userstate">'.(($tbg_user->getState() instanceof TBGUserState) ? $tbg_user->getState()->getName() : __('offline')).'</span>')); ?></div>
												<?php echo link_tag(make_url('dashboard'), image_tag('icon_dashboard_small.png').__('Your dashboard')); ?>	
												<?php echo link_tag(make_url('account'), image_tag('icon_account.png').__('Your account')); ?>
												<?php if ($tbg_user->canAccessConfigurationPage()): ?>
													<?php echo link_tag(make_url('configure'), image_tag('tab_config.png').__('Configure The Bug Genie')); ?>
												<?php endif; ?>
												<?php echo link_tag(make_url('publish_article', array('article_name' => 'Category:Help')), image_tag('help.png').__('Help')); ?>
												<?php echo link_tag(make_url('logout'), image_tag('logout.png').__('Logout')); ?>
												<div class="header"><?php echo __('Your issues'); ?></div>
												<?php echo link_tag(make_url('my_reported_issues'), image_tag('icon_savedsearch.png') . __('Issues reported by me')); ?>
												<?php echo link_tag(make_url('my_assigned_issues'), image_tag('icon_savedsearch.png') . __('Open issues assigned to me')); ?>
												<?php echo link_tag(make_url('my_teams_assigned_issues'), image_tag('icon_savedsearch.png') . __('Open issues assigned to my teams')); ?>
												<?php foreach ($tbg_user->getStarredIssues() as $issue): ?>
													<?php if (!TBGContext::isProjectContext() || $issue->getProject()->getID() != TBGContext::getCurrentProject()->getID()) continue; ?>
													<?php
													
														$link_text = image_tag('star_small.png');
														if ($issue->isBlocking()) $link_text .= image_tag('icon_important.png', array('style' => 'margin-right: 5px;', 'title' => __('This issue is blocking the next release')));
														$link_text .= $issue->getIssueType()->getName() . ' ' . $issue->getFormattedIssueNo(true) . '<br>';
														$link_text .= (strlen($issue->getTitle()) > 43) ? substr($issue->getTitle(), 0, 40) . '...' : $issue->getTitle();
														$classes = ($issue->isClosed()) ? 'issue_closed' : '';
													
													?>
													<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $link_text, array('class' => $classes)); ?>
												<?php endforeach; ?>
											<?php endif; ?>
										</div>
									</div>
									<?php TBGEvent::createNew('core', 'after_header_userinfo')->trigger(); ?>
								</div>
							</td>
						</tr>
					</table>
					<?php require THEBUGGENIE_CORE_PATH . 'templates/submenu.inc.php'; ?>
					<?php if (!TBGContext::isDebugMode()): ?>
						<div class="rounded_box iceblue borderless infobox" style="margin: 5px; display: none;" id="firebug_warning">
							<div style="padding: 5px;">
								<?php echo image_tag('icon_info_big.png', array('style' => 'float: left; margin: 5px 5px 0 0;')); ?>
								<div>
									<div class="header"><?php echo __("Cool - you're using Firebug, too - so do we!"); ?></div>
									<div class="content">
										<?php echo __("As you probably know, Firebug has awesome monitoring and profiling features, which in turn means The Bug Genie will probably be a bit slow if you have Firebug enabled with it."); ?>
										<?php echo __("To learn how to disable Firebug for this site only, see %disabling_Firebug%.", array('%disabling_Firebug%' => link_tag(make_url('publish_article', array('article_name' => 'TheBugGenie:DisablingFirebug')), __('Disabling Firebug')))); ?><br>
										<br>
										<?php echo __('As soon as Firebug is disabled for The Bug Genie, this message will go away. Promise.'); ?>
									</div>
								</div>
							</div>
						</div>
						<script>
							Event.observe(window, 'load', function() {
							  if (window.console && window.console.firebug) {
								  $('firebug_warning').show();
							  }
							});
						</script>
					<?php endif; ?>