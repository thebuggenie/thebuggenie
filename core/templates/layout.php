<!DOCTYPE html>
<html lang="<?php echo TBGSettings::getHTMLLanguage(); ?>" style="cursor: progress;">
	<head>
		<meta charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
		<?php TBGEvent::createNew('core', 'header_begins')->trigger(); ?>
		<meta name="description" content="The bug genie, friendly issue tracking">
		<meta name="keywords" content="thebuggenie friendly issue tracking">
		<meta name="author" content="thebuggenie.com">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<title><?php echo ($tbg_response->hasTitle()) ? strip_tags(TBGSettings::getTBGname() . ' ~ ' . $tbg_response->getTitle()) : strip_tags(TBGSettings::getTBGname()); ?></title>
		<style>
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
		</style>
		<link rel="shortcut icon" href="<?php if (TBGSettings::isUsingCustomFavicon()): echo TBGSettings::getFaviconURL(); else: echo image_url('favicon.png'); endif; ?>">
		<link title="<?php echo (TBGContext::isProjectContext()) ? __('%project_name search', array('%project_name' => TBGContext::getCurrentProject()->getName())) : __('%site_name search', array('%site_name' => TBGSettings::getTBGname())); ?>" href="<?php echo (TBGContext::isProjectContext()) ? make_url('project_opensearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('opensearch'); ?>" type="application/opensearchdescription+xml" rel="search">
		<?php foreach ($tbg_response->getFeeds() as $feed_url => $feed_title): ?>
			<link rel="alternate" type="application/rss+xml" title="<?php echo str_replace('"', '\'', $feed_title); ?>" href="<?php echo $feed_url; ?>">
		<?php endforeach; ?>
		<?php include THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes' . DS . TBGSettings::getThemeName() . DS . 'theme.php'; ?>
		<?php if (count(TBGContext::getModules())): ?>
			<?php foreach (TBGContext::getModules() as $module): ?>
				<?php if (file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes' . DS . TBGSettings::getThemeName() . DS . "{$module->getName()}.css")): ?>
					<?php $tbg_response->addStylesheet("{$module->getName()}.css"); ?>
				<?php endif; ?>
				<?php if (file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'js' . DS . "{$module->getName()}.js")): ?>
					<?php $tbg_response->addJavascript("{$module->getName()}.js"); ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php list ($cssstring, $sepcss) = tbg_get_stylesheets(); ?>
		<?php foreach (explode(',', $cssstring) as $css): ?>
			<link rel="stylesheet" href="<?php print make_url('home').$css; ?>">
		<?php endforeach; ?>
		<?php foreach ($sepcss as $css): ?>
			<link rel="stylesheet" href="<?php echo $css; ?>">
		<?php endforeach; ?>

		<?php list ($jsstring, $sepjs) = tbg_get_javascripts(); ?>
		<?php foreach (explode(',', $jsstring) as $js): ?>
			<script type="text/javascript" src="<?php print make_url('home').$js; ?>"></script>
		<?php endforeach; ?>
		<?php foreach ($sepjs as $js): ?>
			<script type="text/javascript" src="<?php echo $js; ?>"></script>
		<?php endforeach; ?>
		  <!--[if lt IE 9]>
			  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		  <![endif]-->
		<?php TBGEvent::createNew('core', 'header_ends')->trigger(); ?>
	</head>
	<body id="body">
		<?php require THEBUGGENIE_CORE_PATH . 'templates/backdrops.inc.php'; ?>
		<div id="main_container">
			<?php if (!in_array(TBGContext::getRouting()->getCurrentRouteName(), array('login_page', 'elevated_login_page', 'reset_password'))): ?>
				<?php TBGLogging::log('Rendering header'); ?>
				<?php require THEBUGGENIE_CORE_PATH . 'templates/headertop.inc.php'; ?>
				<?php TBGLogging::log('done (rendering header)'); ?>
			<?php endif; ?>
			<div id="content_container">
				<?php TBGLogging::log('Rendering content'); ?>
				<?php echo $content; ?>
				<?php TBGLogging::log('done (rendering content)'); ?>
			</div>
			<?php TBGEvent::createNew('core', 'footer_begin')->trigger(); ?>
			<?php require THEBUGGENIE_CORE_PATH . 'templates/footer.inc.php'; ?>
			<?php TBGEvent::createNew('core', 'footer_end')->trigger(); ?>
		</div>
		<script type="text/javascript">
			document.observe('dom:loaded', function() {
				var f_init = function() {TBG.initialize({ basepath: '<?php echo TBGContext::getTBGPath(); ?>', data_url: '<?php echo make_url('userdata'); ?>', autocompleter_url: '<?php echo (TBGContext::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>'})};
				<?php if (TBGContext::isDebugMode()): ?>
					TBG.debug = true;
					TBG.debugUrl = '<?php echo make_url('debug', array('debug_id' => '___debugid___')); ?>';
					TBG.Core.AjaxCalls.push({location: 'Page loaded', time: new Date(), debug_id: '<?php echo TBGContext::getDebugID(); ?>'});
					TBG.loadDebugInfo('<?php echo TBGContext::getDebugID(); ?>', f_init);
				<?php else: ?>
					f_init();
				<?php endif; ?>
			});
		</script>
	</body>
</html>
