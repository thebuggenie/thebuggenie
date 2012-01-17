<!DOCTYPE html>
<html lang="<?php echo TBGSettings::getHTMLLanguage(); ?>" style="cursor: progress;">
	<head>
		<meta charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
		<?php TBGEvent::createNew('core', 'header_begins')->trigger(); ?>
		<meta name="description" content="The bug genie, friendly issue tracking">
		<meta name="keywords" content="thebuggenie friendly issue tracking">
		<meta name="author" content="thebuggenie.com">
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
		<link rel="shortcut icon" href="<?php print TBGSettings::getFaviconURL(); ?>">
		<link title="<?php echo (TBGContext::isProjectContext()) ? __('%project_name% search', array('%project_name%' => TBGContext::getCurrentProject()->getName())) : __('%site_name% search', array('%site_name%' => TBGSettings::getTBGname())); ?>" href="<?php echo (TBGContext::isProjectContext()) ? make_url('project_opensearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('opensearch'); ?>" type="application/opensearchdescription+xml" rel="search">
		<?php foreach ($tbg_response->getFeeds() as $feed_url => $feed_title): ?>
			<link rel="alternate" type="application/rss+xml" title="<?php echo str_replace('"', '\'', $feed_title); ?>" href="<?php echo $feed_url; ?>">
		<?php endforeach; ?>
		<?php include THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes' . DS . TBGSettings::getThemeName() . DS . 'theme.php'; ?>
		<?php if (count(TBGContext::getModules())): ?>
			<?php foreach (TBGContext::getModules() as $module): ?>
				<?php if (file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes' . DS . TBGSettings::getThemeName() . DS . "{$module->getName()}.css")): ?>
					<?php $tbg_response->addStylesheet("{$module->getName()}.css"); ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php list ($cssstring, $sepcss) = tbg_get_stylesheets(); ?>
		<link rel="stylesheet" href="<?php print make_url('serve'); ?>?g=css&files=<?php print base64_encode($cssstring); ?>">
		<?php foreach ($sepcss as $css): ?>
			<link rel="stylesheet" href="<?php echo $css; ?>">
		<?php endforeach; ?>

		<?php list ($jsstring, $sepjs) = tbg_get_javascripts(); ?>
		<script type="text/javascript" src="<?php print make_url('serve'); ?>?g=js&files=<?php print base64_encode($jsstring); ?>"></script>
		<?php foreach ($sepjs as $js): ?>
			<script type="text/javascript" src="<?php echo $js; ?>"></script>
		<?php endforeach; ?>
		  <!--[if lt IE 9]>
			  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		  <![endif]-->
		<?php TBGEvent::createNew('core', 'header_ends')->trigger(); ?>
	</head>
	<body>
		<?php require THEBUGGENIE_CORE_PATH . 'templates/backdrops.inc.php'; ?>
		<table style="width: 100%; height: 100%; table-layout: fixed; min-width: 1020px;" cellpadding=0 cellspacing=0>
			<tr>
				<td style="height: auto; overflow: hidden;" valign="top" id="maintd">
					<?php TBGLogging::log('Rendering header'); ?>
					<?php require THEBUGGENIE_CORE_PATH . 'templates/headertop.inc.php'; ?>
					<?php TBGLogging::log('done (rendering header)'); ?>
					<?php if (!TBGSettings::isMaintenanceModeEnabled()) require THEBUGGENIE_CORE_PATH . 'templates/submenu.inc.php'; ?>
					<?php TBGLogging::log('Rendering content'); ?>
					<?php echo $content; ?>
					<?php TBGLogging::log('done (rendering content)'); ?>
				</td>
			</tr>
			<tr>
				<td class="footer_bar">
					<?php TBGEvent::createNew('core', 'footer_begin')->trigger(); ?>
					<?php require THEBUGGENIE_CORE_PATH . 'templates/footer.inc.php'; ?>
					<?php TBGEvent::createNew('core', 'footer_end')->trigger(); ?>
				</td>
			</tr>
		</table>
		<script type="text/javascript">
			document.observe('dom:loaded', function() {
				var f_init = function() {TBG.initialize({ autocompleter_url: '<?php echo (TBGContext::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>'})};
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